<?php

namespace App\Http\Controllers;

use App\Services\Chatbot\PublicChatbotGeminiService;
use App\Services\Chatbot\PublicChatbotSearchService;
use App\Services\Chatbot\VietnameseQueryNormalizerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            return $this->jsonReply($safetyAnswer, $searchResult['sources']);
        }

        $answer = $geminiService->generateAnswer($message, $normalizedQuery, $searchResult);

        return $this->jsonReply($answer, $searchResult['sources']);
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
