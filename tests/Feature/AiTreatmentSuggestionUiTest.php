<?php

namespace Tests\Feature;

use Tests\TestCase;

class AiTreatmentSuggestionUiTest extends TestCase
{
    public function test_treatment_ai_panel_uses_draft_prescription_language(): void
    {
        $panel = file_get_contents(resource_path('views/admin/records/partials/ai_panel.blade.php'));
        $script = file_get_contents(resource_path('views/admin/records/partials/ai_js.blade.php'));
        $create = file_get_contents(resource_path('views/admin/prescriptions/create.blade.php'));

        $combined = $panel . "\n" . $script . "\n" . $create;

        $this->assertStringContainsString('Nhận xét trước kê đơn', $combined);
        $this->assertStringContainsString('Nguyên tắc điều trị tham khảo', $combined);
        $this->assertStringContainsString('Gợi ý đơn thuốc / dịch vụ nháp', $combined);
        $this->assertStringContainsString('Lưu ý an toàn và theo dõi', $combined);
        $this->assertStringContainsString('Gợi ý AI chỉ mang tính tham khảo. Thầy thuốc cần kiểm tra, chỉnh sửa và xác nhận trước khi lập đơn.', $combined);
        $this->assertStringContainsString('Áp dụng vào đơn nháp', $combined);
        $this->assertStringContainsString('ai-pre-note-text', $combined);
        $this->assertStringContainsString('ai-principles-text', $combined);
        $this->assertStringContainsString('ai-draft-items-text', $combined);
        $this->assertStringContainsString('ai-safety-followup-text', $combined);
        $this->assertStringContainsString('normalizeAiDraftItems', $combined);

        $this->assertStringNotContainsString('Nhận xét lâm sàng', $panel);
        $this->assertStringNotContainsString('Gợi ý theo dõi', $panel);
        $this->assertStringNotContainsString('Áp dụng gợi ý AI', $create);
        $this->assertStringNotContainsString('Kê đơn tự động', $combined);
        $this->assertStringNotContainsString('Chốt đơn bằng AI', $combined);
        $this->assertStringNotContainsString('Dùng đơn AI', $combined);
    }
}
