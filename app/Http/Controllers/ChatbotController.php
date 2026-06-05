<?php

namespace App\Http\Controllers;

use App\Services\Chatbot\PublicChatbotGeminiService;
use App\Services\Chatbot\PublicChatbotSearchService;
use App\Services\Chatbot\VietnameseQueryNormalizerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function chat(
        Request $request,
        VietnameseQueryNormalizerService $normalizer,
        PublicChatbotSearchService $searchService,
        PublicChatbotGeminiService $geminiService
    ): JsonResponse {
        $message = trim((string) $request->input('message', ''));

        if ($message === '') {
            return $this->jsonReply('Bạn vui lòng nhập câu hỏi trước khi gửi nhé.', [], false, 422);
        }

        if (mb_strlen($message, 'UTF-8') > 1000) {
            return $this->jsonReply('Câu hỏi hơi dài. Bạn vui lòng rút gọn dưới 1000 ký tự để Trợ lý AmaTrung hỗ trợ tốt hơn nhé.', [], false, 422);
        }

        $normalizedQuery = $normalizer->normalize($message);
        $searchResult = $searchService->search($normalizedQuery);
        $requiresSafetyAnswer = ($normalizedQuery['is_emergency'] ?? false)
            || ($normalizedQuery['is_treatment_request'] ?? false)
            || ($normalizedQuery['is_prompt_injection'] ?? false);

        if (!$requiresSafetyAnswer && !$searchResult['has_results'] && $geminiService->isConfigured()) {
            $retryTerms = $geminiService->suggestSearchTerms($message);
            if (!empty($retryTerms)) {
                $retryResult = $searchService->search($normalizedQuery, $retryTerms);
                if ($retryResult['has_results']) {
                    $searchResult = $retryResult;
                }
            }
        }

        $safetyAnswer = $geminiService->safetyAnswer($normalizedQuery, $searchResult);
        if ($safetyAnswer !== null) {
            return $this->jsonReply($geminiService->sanitizePublicAnswer($safetyAnswer), $searchResult['sources']);
        }

        $answer = $geminiService->generateAnswer($message, $normalizedQuery, $searchResult);
        if ($answer === PublicChatbotGeminiService::UNAVAILABLE_MESSAGE && ($searchResult['has_results'] ?? false)) {
            $answer = $this->fallbackSearchAnswer($searchResult);
        }

        return $this->jsonReply($geminiService->sanitizePublicAnswer($answer), $searchResult['sources']);
    }

    private function fallbackSearchAnswer(array $searchResult): string
    {
        $lines = [
            'Hiện tại phần tạo câu trả lời bằng AI đang quá tải, nhưng mình đã tìm thấy một số nội dung liên quan trên AmaTrung để bạn tham khảo:',
        ];

        foreach (array_slice($searchResult['herbs'] ?? [], 0, 3) as $herb) {
            $description = trim((string) ($herb['description'] ?? ''));
            $warning = trim((string) ($herb['warning'] ?? ''));

            $line = '- Dược liệu: ' . ($herb['title'] ?? 'Không rõ tên');
            if ($description !== '') {
                $line .= ' — ' . Str::limit($description, 180);
            }
            if ($warning !== '') {
                $line .= ' Lưu ý: ' . Str::limit($warning, 120);
            }

            $lines[] = $line;
        }

        foreach (array_slice($searchResult['articles'] ?? [], 0, 2) as $article) {
            $summary = trim((string) ($article['summary'] ?? $article['content'] ?? ''));
            $line = '- Bài viết: ' . ($article['title'] ?? 'Không rõ tiêu đề');
            if ($summary !== '') {
                $line .= ' — ' . Str::limit($summary, 180);
            }

            $lines[] = $line;
        }

        $lines[] = 'Thông tin trên chỉ mang tính tham khảo. Nếu câu hỏi liên quan triệu chứng, chẩn đoán hoặc dùng thuốc cụ thể, bạn nên trao đổi trực tiếp với thầy thuốc để được hướng dẫn an toàn.';

        return implode("\n", $lines);
    }

    private function jsonReply(string $answer, array $sources = [], bool $success = true, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => $success,
            'answer' => $answer,
            'reply' => $answer,
            'sources' => $sources,
        ], $status);
    }
}
