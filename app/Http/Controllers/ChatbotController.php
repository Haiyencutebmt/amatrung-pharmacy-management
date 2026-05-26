<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Article;
use App\Models\MedicinalHerb;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $message = $request->input('message');
        
        if (empty($message)) {
            return response()->json(['success' => false, 'error' => 'Message is empty']);
        }

        // 1. RAG: Search context from database based on keywords
        $context = $this->searchContext($message);

        // 2. Prepare the system prompt
        $systemPrompt = "Bạn là Trợ lý AI y tế thông minh của Phòng khám Y học cổ truyền AmaTrung.
Tên bạn là: Trợ lý Y tế AmaTrung.
Mục đích của bạn là cung cấp thông tin tham khảo về dược liệu, các bài báo y tế trên website, và trả lời thắc mắc của bệnh nhân.

QUY TẮC AN TOÀN Y TẾ (VÔ CÙNG QUAN TRỌNG - PHẢI TUÂN THỦ NGHIÊM NGẶT):
1. KHÔNG BAO GIỜ được chẩn đoán bệnh tật một cách chắc chắn.
2. KHÔNG BAO GIỜ được kê đơn thuốc cụ thể liều lượng để tự điều trị.
3. Nếu người dùng hỏi các câu hỏi nhạy cảm, nguy hiểm, khó hiểu, hoặc đòi hỏi chuyên môn sâu để chữa bệnh, bạn bắt buộc phải chèn đoạn này vào cuối câu trả lời:
   'Tôi chỉ là trợ lý AI cung cấp các thông tin tham khảo về dược liệu và bài viết, không thể thay thế bác sĩ. Hãy đến nhà thuốc AmaTrung để được các y bác sĩ tư vấn kỹ hơn nhé.'
4. Khi tư vấn thông tin về bệnh lý hoặc dược liệu, hãy sử dụng ngôn từ an toàn, nhấn mạnh đây là thông tin 'tham khảo'.

NGỮ CẢNH DỮ LIỆU TỪ HỆ THỐNG AMATRUNG:
Dưới đây là một số thông tin (bài viết, dược liệu) được trích xuất từ database của AmaTrung liên quan đến câu hỏi của người dùng:
$context
(Hãy ưu tiên sử dụng các thông tin này để trả lời nếu nó phù hợp, vì nó là thông tin chính thống từ website).";

        // 3. Call Gemini API
        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-1.5-flash');
        
        if (empty($apiKey)) {
            return response()->json([
                'success' => true, 
                'reply' => "Hệ thống chưa được cấu hình API Key của Google Gemini. Quản trị viên vui lòng thêm GEMINI_API_KEY vào file .env."
            ]);
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                'system_instruction' => [
                    'parts' => [
                        ['text' => $systemPrompt]
                    ]
                ],
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $message]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 800,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? "Xin lỗi, tôi không thể trả lời lúc này.";
                
                return response()->json([
                    'success' => true,
                    'reply' => $reply
                ]);
            } else {
                Log::error('Gemini API Error: ' . $response->body());
                return response()->json([
                    'success' => false,
                    'reply' => 'Lỗi kết nối đến dịch vụ AI. Vui lòng thử lại sau.'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Chatbot Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'reply' => 'Đã có lỗi hệ thống xảy ra.'
            ]);
        }
    }

    private function searchContext($message)
    {
        // Simple keyword extraction (remove common words)
        $stopWords = ['là', 'gì', 'cho', 'hỏi', 'cách', 'như', 'thế', 'nào', 'khi', 'bị', 'nên', 'không', 'về'];
        $words = explode(' ', mb_strtolower($message, 'UTF-8'));
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return !in_array($word, $stopWords) && mb_strlen($word, 'UTF-8') > 2;
        });

        $contextString = "";

        if (count($keywords) > 0) {
            // Search in Medicinal Herbs
            $herbQuery = MedicinalHerb::query();
            foreach ($keywords as $kw) {
                $herbQuery->orWhere('name', 'LIKE', '%' . $kw . '%')
                          ->orWhere('description', 'LIKE', '%' . $kw . '%');
            }
            $herbs = $herbQuery->take(2)->get();
            
            if ($herbs->isNotEmpty()) {
                $contextString .= "[DƯỢC LIỆU]\n";
                foreach ($herbs as $herb) {
                    $contextString .= "- Tên: " . $herb->name . "\n";
                    $contextString .= "  Mô tả: " . $herb->description . "\n";
                    $contextString .= "  Lưu ý: " . $herb->warning_note . "\n";
                }
            }

            // Search in Articles
            $articleQuery = Article::query();
            foreach ($keywords as $kw) {
                $articleQuery->orWhere('title', 'LIKE', '%' . $kw . '%')
                             ->orWhere('content', 'LIKE', '%' . $kw . '%');
            }
            $articles = $articleQuery->take(2)->get();

            if ($articles->isNotEmpty()) {
                $contextString .= "\n[BÀI VIẾT TỪ WEBSITE]\n";
                foreach ($articles as $article) {
                    $contextString .= "- Tiêu đề: " . $article->title . "\n";
                    // Only take a summary of the content to save tokens
                    $contentSummary = mb_substr(strip_tags($article->content), 0, 300, 'UTF-8') . '...';
                    $contextString .= "  Nội dung: " . $contentSummary . "\n";
                }
            }
        }

        if (empty($contextString)) {
            $contextString = "Không tìm thấy thông tin liên quan trong cơ sở dữ liệu.";
        }

        return $contextString;
    }
}
