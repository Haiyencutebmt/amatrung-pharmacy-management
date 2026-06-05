<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\MedicinalHerb;
use App\Models\User;
use App\Services\Chatbot\PublicChatbotGeminiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PublicChatbotOutputTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.gemini.api_key', 'test-key');
        Config::set('services.gemini.model', 'gemini-test');
    }

    public function test_public_chatbot_answers_do_not_show_reference_sections_or_internal_links(): void
    {
        $this->seedPublicChatbotData();

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [[
                            'text' => implode("\n", [
                                'Hương nhu và tía tô đều là dược liệu có mùi thơm, nhưng cách dùng và công năng trong y học cổ truyền có điểm khác nhau.',
                                '',
                                'Nội dung này chỉ mang tính chất tham khảo, không thay thế cho lời khuyên y tế chuyên nghiệp.',
                                '',
                                'Nguồn tham khảo:',
                                '1. PHÂN BIỆT HƯƠNG NHU VÀ TÍA TÔ (/bai-viet/phan-biet-huong-nhu-va-tia-to)',
                                '2. Kim tiền thảo (/tu-dien-thuoc-nam/kim-tien-thao)',
                            ]),
                        ]],
                    ],
                ]],
            ], 200),
        ]);

        foreach ([
            'hương nhu và tía tô khác nhau sao',
            'đau dạ dày theo yhct',
            'kim tiền thảo có tác dụng gì',
        ] as $question) {
            $response = $this->postJson(route('chatbot.chat'), [
                'message' => $question,
            ]);

            $response->assertOk()
                ->assertJsonPath('success', true);

            $answer = (string) $response->json('answer');

            $this->assertStringContainsString('tham khảo', $answer);
            $this->assertStringNotContainsString('Nguồn tham khảo', $answer);
            $this->assertStringNotContainsString('Tài liệu tham khảo', $answer);
            $this->assertStringNotContainsString('Tham khảo:', $answer);
            $this->assertStringNotContainsString('/bai-viet/', $answer);
            $this->assertStringNotContainsString('/tu-dien-thuoc-nam/', $answer);
            $this->assertStringNotContainsString('1. PHÂN BIỆT', $answer);
            $this->assertStringNotContainsString('2. Kim tiền thảo', $answer);
        }
    }

    public function test_frontend_only_uses_answer_text_and_ignores_sources_field(): void
    {
        $component = file_get_contents(resource_path('views/components/chatbot.blade.php'));

        $this->assertStringNotContainsString('formatAnswerWithSources', $component);
        $this->assertStringNotContainsString('data.sources', $component);
        $this->assertStringContainsString('appendMessage(botAnswer', $component);
    }

    public function test_public_chatbot_prompt_forbids_source_lists_and_internal_paths(): void
    {
        $prompt = app(PublicChatbotGeminiService::class)->buildSystemPrompt([
            'context' => 'Dữ liệu thử nghiệm từ AmaTrung.',
        ]);
        $promptAscii = \Illuminate\Support\Str::ascii($prompt);

        $this->assertStringContainsString('Không hiển thị tiêu đề "Nguồn tham khảo"', $prompt);
        $this->assertStringContainsString('Không liệt kê URL', $prompt);
        $this->assertStringContainsString('danh sach', $promptAscii);
        $this->assertStringContainsString('o cuoi cau tra loi', $promptAscii);
        $this->assertStringContainsString('Không đưa đường dẫn /bai-viet/...', $prompt);
        $this->assertStringContainsString('Thông tin được tổng hợp từ dữ liệu tham khảo trên AmaTrung', $prompt);
    }

    public function test_sanitizer_removes_reference_sections_and_internal_paths(): void
    {
        $answer = app(PublicChatbotGeminiService::class)->sanitizePublicAnswer(implode("\n", [
            'Kim tiền thảo thường được nhắc đến trong dữ liệu dược liệu AmaTrung.',
            'Xem thêm tại /tu-dien-thuoc-nam/kim-tien-thao.',
            '',
            'Tài liệu tham khảo:',
            '1. Kim tiền thảo (/tu-dien-thuoc-nam/kim-tien-thao)',
        ]));

        $this->assertSame(
            'Kim tiền thảo thường được nhắc đến trong dữ liệu dược liệu AmaTrung.',
            $answer
        );
    }

    private function seedPublicChatbotData(): void
    {
        $author = User::factory()->create();

        Article::create([
            'user_id' => $author->id,
            'title' => 'Phân biệt hương nhu và tía tô',
            'slug' => 'phan-biet-huong-nhu-va-tia-to',
            'summary' => 'Hương nhu và tía tô đều có mùi thơm nhưng khác nhau về đặc điểm và cách dùng tham khảo.',
            'content' => 'Hương nhu và tía tô khác nhau về hình thái, mùi thơm, tính vị và ứng dụng trong y học cổ truyền.',
            'category' => 'duoc-lieu-bai-thuoc',
            'tags' => ['hương nhu', 'tía tô'],
            'is_published' => 1,
            'published_at' => now(),
        ]);

        Article::create([
            'user_id' => $author->id,
            'title' => 'Đau dạ dày theo y học cổ truyền',
            'slug' => 'dau-da-day-theo-y-hoc-co-truyen',
            'summary' => 'Một số cách nhìn tham khảo về đau dạ dày theo y học cổ truyền.',
            'content' => 'Đau dạ dày theo y học cổ truyền thường được nhìn theo hàn nhiệt, khí trệ, can vị bất hòa và cần thăm khám để biện chứng.',
            'category' => 'benh-hoc-phuong-phap-dieu-tri',
            'tags' => ['đau dạ dày', 'y học cổ truyền'],
            'is_published' => 1,
            'published_at' => now(),
        ]);

        MedicinalHerb::create([
            'name' => 'Kim tiền thảo',
            'category' => 'Dược liệu rời',
            'usage_type' => 'Sắc uống',
            'description' => 'Kim tiền thảo thường được dùng trong các tài liệu y học cổ truyền để hỗ trợ lợi tiểu, thanh nhiệt theo hướng tham khảo.',
            'unit' => 'g',
            'stock_quantity' => 100,
            'warning_note' => 'Không tự dùng thay chỉ định điều trị cá nhân.',
            'status' => 'active',
        ]);
    }
}
