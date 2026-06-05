<?php

namespace Tests\Feature;

use App\Models\AiSuggestionLog;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiPreliminaryFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        Config::set('services.gemini.api_key', 'test-key');
    }

    public function test_preliminary_assessment_uses_temporary_additional_symptoms_without_saving_and_limits_options(): void
    {
        $admin = $this->createAdmin();
        $record = $this->createRecord($admin->id, ['additional_symptoms' => 'Thông tin bổ sung đã lưu']);
        $capturedPrompt = '';

        Http::fake(function ($request) use (&$capturedPrompt) {
            $body = json_decode($request->body(), true);
            $capturedPrompt = data_get($body, 'contents.0.parts.0.text', '');

            return Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [[
                            'text' => json_encode([
                                'summary' => 'Tóm tắt tham khảo',
                                'warning' => '',
                                'followup_questions' => ['Cần hỏi thêm thời gian khởi phát?'],
                                'assessments' => [
                                    [
                                        'title' => 'Nhận định A',
                                        'confidence_percent' => 40,
                                        'doctor_notes' => ['Hỏi thêm diễn tiến mất ngủ.'],
                                        'caution_flags' => [],
                                    ],
                                    [
                                        'title' => 'Nhận định B',
                                        'confidence_percent' => 55,
                                        'doctor_notes' => ['Rà soát bệnh nền và thuốc đang dùng.'],
                                        'caution_flags' => ['Chóng mặt dữ dội'],
                                    ],
                                    [
                                        'title' => 'Nhận định C',
                                        'confidence_percent' => 5,
                                        'doctor_notes' => ['Đối chiếu thể trạng.'],
                                        'caution_flags' => [],
                                    ],
                                ],
                            ]),
                        ]],
                    ],
                ]],
            ], 200);
        });

        $response = $this->actingAs($admin)->postJson(route('admin.ai.preliminary-assessment'), [
            'medical_record_id' => $record->id,
            'additional_symptoms' => 'Thông tin bổ sung chỉ dùng cho request AI',
        ]);

        $response->assertOk()->assertJson(['status' => 'success']);
        $this->assertCount(2, $response->json('suggestions.assessment_options'));
        $this->assertSame('Nhận định B', $response->json('suggestions.assessment_options.0.title'));
        $this->assertSame(55, $response->json('suggestions.assessment_options.0.confidence_percent'));
        $this->assertSame('Rà soát bệnh nền và thuốc đang dùng.', $response->json('suggestions.assessment_options.0.doctor_notes.0'));
        $this->assertSame('Chóng mặt dữ dội', $response->json('suggestions.assessment_options.0.caution_flags.0'));
        $this->assertStringContainsString('Thông tin bổ sung chỉ dùng cho request AI', $capturedPrompt);
        $this->assertStringContainsString('Không trả về lời dặn bệnh nhân', $capturedPrompt);
        $this->assertStringNotContainsString($record->patient->full_name, $capturedPrompt);
        $this->assertStringNotContainsString($record->patient->phone, $capturedPrompt);
        $this->assertSame('Thông tin bổ sung đã lưu', $record->fresh()->additional_symptoms);

        $log = AiSuggestionLog::latest()->first();
        $this->assertSame('preliminary_assessment', $log->payload['ai_flow']);
        $this->assertSame('Thông tin bổ sung chỉ dùng cho request AI', $log->payload['clinical']['additional_symptoms']);
    }

    public function test_follow_up_question_endpoint_returns_question_mode_payload(): void
    {
        $admin = $this->createAdmin();
        $record = $this->createRecord($admin->id);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [[
                            'text' => json_encode([
                                'safety_note' => 'Chỉ tham khảo',
                                'follow_up_questions' => [
                                    'Triệu chứng bắt đầu từ khi nào?',
                                    'Mức độ đau hiện tại là bao nhiêu?',
                                ],
                                'missing_information' => [
                                    'Thời gian khởi phát',
                                    'Yếu tố làm nặng hoặc giảm',
                                ],
                            ]),
                        ]],
                    ],
                ]],
            ], 200),
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.ai.preliminary-assessment.follow-up-questions'), [
            'medical_record_id' => $record->id,
        ]);

        $response->assertOk()
            ->assertJson(['status' => 'success'])
            ->assertJsonPath('suggestions.follow_up_questions.0', 'Triệu chứng bắt đầu từ khi nào?')
            ->assertJsonPath('suggestions.missing_information.0', 'Thời gian khởi phát');

        $log = AiSuggestionLog::latest()->first();
        $this->assertSame('generate_followup_questions', $log->payload['ai_flow']);
    }

    private function createAdmin(): User
    {
        $user = User::factory()->create(['role' => 'admin']);
        $user->assignRole('admin');

        return $user;
    }

    private function createRecord(int $staffId, array $extra = []): MedicalRecord
    {
        $patient = Patient::create([
            'patient_code' => 'PT-AI-' . fake()->unique()->numberBetween(1000, 9999),
            'full_name' => 'Nguyễn Văn Test',
            'phone' => '0987654321',
            'gender' => 'male',
            'date_of_birth' => '1990-01-01',
        ]);

        return MedicalRecord::create(array_merge([
            'patient_id' => $patient->id,
            'staff_id' => $staffId,
            'visit_date' => now()->toDateString(),
            'case_type' => MedicalRecord::CASE_NORMAL,
            'symptoms' => 'Đau lưng kéo dài',
            'diagnosis' => MedicalRecord::PENDING_DIAGNOSIS,
            'treatment_direction' => 'oral_only',
        ], $extra))->load('patient');
    }
}
