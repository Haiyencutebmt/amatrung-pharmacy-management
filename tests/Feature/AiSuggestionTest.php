<?php

namespace Tests\Feature;

use App\Models\MedicinalHerb;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiSuggestionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test unauthenticated access is blocked.
     */
    public function test_guest_cannot_access_ai_suggestions(): void
    {
        $response = $this->postJson(route('admin.ai.suggest'), [
            'symptoms' => 'Đau nhức thắt lưng lan xuống chân',
            'diagnosis' => 'Thoái hóa cột sống thắt lưng L4-L5',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test staff user without permission is forbidden.
     */
    public function test_staff_without_permission_cannot_access_ai_suggestions(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'permissions' => []
        ]);

        $response = $this->actingAs($staff)->postJson(route('admin.ai.suggest'), [
            'symptoms' => 'Đau nhức thắt lưng lan xuống chân',
            'diagnosis' => 'Thoái hóa cột sống thắt lưng L4-L5',
        ]);

        // Laravel permission middleware might throw 403 Forbidden or custom handling
        $response->assertStatus(403);
    }

    /**
     * Test validation failure with empty or short inputs.
     */
    public function test_ai_suggestions_validation_failures(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Empty symptoms and diagnosis
        $response = $this->actingAs($admin)->postJson(route('admin.ai.suggest'), [
            'symptoms' => '',
            'diagnosis' => '',
        ]);
        $response->assertStatus(422);

        // Symptoms too short
        $response = $this->actingAs($admin)->postJson(route('admin.ai.suggest'), [
            'symptoms' => 'Đau',
            'diagnosis' => 'Đau thắt lưng',
        ]);
        $response->assertStatus(422);
    }

    /**
     * Test AI Suggestion for normal cold: only oral herbs, no services.
     */
    public function test_ai_suggestion_for_cold_returns_only_oral_herbs(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'suggested_formula_name' => 'Quế Chi Thang gia giảm',
                                        'suggested_condition' => 'Cảm mạo phong hàn ngoại cảm (Cảm lạnh)',
                                        'reasoning' => 'Bệnh nhân nhiễm phong hàn tà bên ngoài',
                                        'treatment_method' => 'Tân ôn giải biểu',
                                        'course_days' => 3,
                                        'follow_up_days' => 3,
                                        'patient_guidelines' => 'Ăn cháo hành nóng',
                                        'internal_notes' => 'Theo dõi cơn sốt',
                                        'safety_warning' => 'Lưu ý sốt cao',
                                        'oral_herbs' => [
                                            ['herb_name' => 'Quế Chi', 'dosage' => '12', 'usage_note' => 'Tân ôn giải biểu'],
                                            ['herb_name' => 'Bạch Thược', 'dosage' => '12', 'usage_note' => 'Hòa vinh dưỡng âm'],
                                            ['herb_name' => 'Cam Thảo', 'dosage' => '4', 'usage_note' => 'Điều hòa tỳ vị']
                                        ],
                                        'external_herbs' => [],
                                        'therapy_services' => []
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $this->seedAvailableHerbs(['Quế Chi', 'Bạch Thược', 'Cam Thảo']);

        $response = $this->actingAs($admin)->postJson(route('admin.ai.suggest'), [
            'symptoms' => 'Ho nhiều, sốt cao, sợ gió sợ lạnh',
            'diagnosis' => 'Cảm mạo phong hàn',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        
        $data = $response->json('data');
        $this->assertNotEmpty($data['oral_herbs']);
        $this->assertEmpty($data['external_herbs']);
        $this->assertEmpty($data['therapy_services']);
    }

    /**
     * Test AI Suggestion for Injury: returns both oral herbs and services/external preparations.
     */
    public function test_ai_suggestion_for_injury_returns_both_modalities(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'suggested_formula_name' => 'Tứ Vật Thang gia giảm',
                                        'suggested_condition' => 'Chấn thương phần mềm cấp tính thể Phong hàn thấp ngưng trệ',
                                        'reasoning' => '...',
                                        'treatment_method' => '...',
                                        'course_days' => 7,
                                        'follow_up_days' => 7,
                                        'patient_guidelines' => '...',
                                        'internal_notes' => '...',
                                        'safety_warning' => '...',
                                        'oral_herbs' => [
                                            ['herb_name' => 'Đương Quy', 'dosage' => '12', 'usage_note' => '...'],
                                            ['herb_name' => 'Xuyên Khung', 'dosage' => '8', 'usage_note' => '...'],
                                            ['herb_name' => 'Bạch Thược', 'dosage' => '12', 'usage_note' => '...'],
                                            ['herb_name' => 'Cam Thảo', 'dosage' => '4', 'usage_note' => '...']
                                        ],
                                        'external_herbs' => [
                                            [
                                                'custom_name' => 'Bó thuốc nam',
                                                'quantity' => 3,
                                                'unit' => 'gói',
                                                'usage_area' => 'vùng cổ chân',
                                                'usage_instruction' => '...'
                                            ],
                                            [
                                                'custom_name' => 'Lọ rượu thuốc xoa bóp',
                                                'quantity' => 1,
                                                'unit' => 'lọ',
                                                'usage_area' => 'vùng cổ chân',
                                                'usage_instruction' => '...'
                                            ]
                                        ],
                                        'therapy_services' => [
                                            [
                                                'custom_name' => 'Nắn chỉnh khớp xương',
                                                'sessions' => 3,
                                                'usage_area' => 'vùng cổ chân',
                                                'usage_instruction' => '...'
                                            ]
                                        ]
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $this->seedAvailableHerbs(['Đương Quy', 'Xuyên Khung', 'Bạch Thược', 'Cam Thảo']);

        $response = $this->actingAs($admin)->postJson(route('admin.ai.suggest'), [
            'symptoms' => 'Té ngã sưng bầm cổ chân, đau buốt',
            'diagnosis' => 'Bong gân cổ chân',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        
        $data = $response->json('data');
        $this->assertNotEmpty($data['oral_herbs']);
        $this->assertNotEmpty($data['external_herbs']);
        $this->assertNotEmpty($data['therapy_services']);
        
        // Assert Bó thuốc nam is present
        $externalNames = array_column($data['external_herbs'], 'custom_name');
        $this->assertTrue(in_array('Bó thuốc nam', $externalNames));
        $this->assertTrue(in_array('Lọ rượu thuốc xoa bóp', $externalNames));
    }

    /**
     * Test AI Suggestion for musculoskeletal-only injury: no oral decoction.
     */
    public function test_ai_suggestion_for_musculoskeletal_injury_skips_oral_herbs(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'suggested_formula_name' => null,
                                        'suggested_condition' => 'Chấn thương phần mềm cấp tính thể Phong hàn thấp ngưng trệ',
                                        'reasoning' => '...',
                                        'treatment_method' => '...',
                                        'course_days' => 7,
                                        'follow_up_days' => 7,
                                        'patient_guidelines' => '...',
                                        'internal_notes' => 'không bốc thuốc sắc. ...',
                                        'safety_warning' => '...',
                                        'oral_herbs' => [],
                                        'external_herbs' => [
                                            [
                                                'custom_name' => 'Bó thuốc nam',
                                                'quantity' => 3,
                                                'unit' => 'gói',
                                                'usage_area' => 'vùng cổ chân',
                                                'usage_instruction' => '...'
                                            ],
                                            [
                                                'custom_name' => 'Lọ rượu thuốc xoa bóp',
                                                'quantity' => 1,
                                                'unit' => 'lọ',
                                                'usage_area' => 'vùng cổ chân',
                                                'usage_instruction' => '...'
                                            ]
                                        ],
                                        'therapy_services' => [
                                            [
                                                'custom_name' => 'Nắn chỉnh khớp xương',
                                                'sessions' => 3,
                                                'usage_area' => 'vùng cổ chân',
                                                'usage_instruction' => '...'
                                            ]
                                        ]
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.ai.suggest'), [
            'symptoms' => 'Té ngã sưng bầm cổ chân, đau buốt',
            'diagnosis' => 'Bong gân cổ chân',
            'case_type' => 'musculoskeletal',
            'injury_location' => 'vùng cổ chân',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');

        $data = $response->json('data');
        $this->assertEmpty($data['oral_herbs']);
        $this->assertNotEmpty($data['external_herbs']);
        $this->assertNotEmpty($data['therapy_services']);
        $this->assertNull($data['suggested_formula_name']);
        $this->assertStringContainsString('không bốc thuốc sắc', $data['internal_notes']);
    }

    /**
     * Test AI Suggestion for spine deformity (e.g. gù lưng): returns only therapy services.
     */
    public function test_ai_suggestion_for_hunchback_returns_only_therapy_services(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'suggested_formula_name' => null,
                                        'suggested_condition' => 'Tật gù lưng / Cong vẹo cột sống do sai lệch tư thế',
                                        'reasoning' => '...',
                                        'treatment_method' => '...',
                                        'course_days' => 10,
                                        'follow_up_days' => 10,
                                        'patient_guidelines' => '...',
                                        'internal_notes' => '...',
                                        'safety_warning' => '...',
                                        'oral_herbs' => [],
                                        'external_herbs' => [],
                                        'therapy_services' => [
                                            [
                                                'custom_name' => 'Nắn chỉnh khớp xương',
                                                'sessions' => 5,
                                                'usage_area' => 'vùng cột sống',
                                                'usage_instruction' => '...'
                                            ],
                                            [
                                                'custom_name' => 'Theo dõi phục hồi vận động',
                                                'sessions' => 5,
                                                'usage_area' => 'vùng cột sống',
                                                'usage_instruction' => '...'
                                            ]
                                        ]
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.ai.suggest'), [
            'symptoms' => 'Khòm khom người, đi đứng mỏi cơ thắt lưng và vai, có gù lưng',
            'diagnosis' => 'Gù lưng / Lệch cột sống',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        
        $data = $response->json('data');
        $this->assertEmpty($data['oral_herbs']);
        $this->assertEmpty($data['external_herbs']);
        $this->assertNotEmpty($data['therapy_services']);
        
        // Assert that the suggested therapy services are mapped correctly
        $therapyNames = array_column($data['therapy_services'], 'custom_name');
        $this->assertTrue(in_array('Nắn chỉnh khớp xương', $therapyNames));
        $this->assertTrue(in_array('Theo dõi phục hồi vận động', $therapyNames));
    }

    private function seedAvailableHerbs(array $names): void
    {
        foreach ($names as $name) {
            MedicinalHerb::create([
                'name' => $name,
                'category' => 'Dược liệu bốc thuốc',
                'usage_type' => 'Sắc uống',
                'unit' => 'g',
                'stock_quantity' => 1000,
                'expiry_date' => now()->addYear()->toDateString(),
                'status' => 'active',
            ]);
        }
    }
}
