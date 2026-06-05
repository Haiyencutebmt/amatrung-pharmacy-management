<?php

namespace Tests\Feature;

use Tests\TestCase;

class AiPreliminarySafetyLanguageTest extends TestCase
{
    public function test_preliminary_ai_ui_uses_reference_assessment_language(): void
    {
        $panel = file_get_contents(resource_path('views/admin/records/partials/ai_preliminary_panel.blade.php'));
        $script = file_get_contents(resource_path('views/admin/records/partials/ai_preliminary_js.blade.php'));
        $show = file_get_contents(resource_path('views/admin/records/show.blade.php'));

        $combined = $panel . "\n" . $script;

        $this->assertStringContainsString('AI hỗ trợ nhận định sơ bộ', $combined);
        $this->assertStringContainsString('Kết quả AI chỉ mang tính hỗ trợ tham khảo. Quyết định chẩn đoán và điều trị thuộc về thầy thuốc.', $combined);
        $this->assertStringContainsString('Câu hỏi / thông tin nên khai thác thêm', $combined);
        $this->assertStringContainsString('Gợi ý câu hỏi khai thác thêm', $combined);
        $this->assertStringContainsString('Triệu chứng / thông tin bổ sung sau khi khai thác thêm', $combined);
        $this->assertStringContainsString('Phân tích sơ bộ', $combined);
        $this->assertStringContainsString('Áp dụng nhận định tham khảo', $combined);
        $this->assertStringContainsString('Lưu ý cho bác sĩ khi kê đơn/thăm khám', $combined);
        $this->assertStringContainsString('Bạn có muốn điền nhận định tham khảo này vào form bệnh án không?', $combined);
        $this->assertStringContainsString('Lưu ý cho bác sĩ:', $combined);
        $this->assertStringContainsString('Cảnh báo nếu có:', $combined);
        $this->assertStringContainsString('doctor_note_inline', $combined);
        $this->assertStringContainsString('name="doctor_note" id="doctor_note_inline"', $show);
        $this->assertStringContainsString('Lưu ý cho bác sĩ khi kê đơn / thăm khám', $show);
        $this->assertStringContainsString('Các lưu ý chuyên môn, cảnh báo và điểm cần thận trọng khi kê đơn sẽ hiển thị tại đây...', $show);
        $this->assertDoesNotMatchRegularExpression('/id="btn-ai-preliminary"[^>]*disabled/i', $panel);

        $this->assertStringNotContainsString('AI chẩn đoán', $combined);
        $this->assertStringNotContainsString('Kết quả chẩn đoán AI', $combined);
        $this->assertStringNotContainsString('Áp dụng chẩn đoán', $combined);
        $this->assertStringNotContainsString('Mức độ chẩn đoán', $combined);
        $this->assertStringNotContainsString('Xác nhận chẩn đoán', $combined);
        $this->assertStringNotContainsString('Lời dặn tham khảo', $combined);
        $this->assertStringNotContainsString('Sao chép lời dặn', $combined);
        $this->assertStringNotContainsString('fetch(APPLY_URL', $script);
    }
}
