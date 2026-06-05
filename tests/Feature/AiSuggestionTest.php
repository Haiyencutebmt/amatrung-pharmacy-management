<?php

namespace Tests\Feature;

use App\Models\MedicinalHerb;
use App\Models\User;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\InventoryItem;
use App\Models\InventoryBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiSuggestionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
    }

    private function createAdmin()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $user->assignRole('admin');
        return $user;
    }

    private function createStaff()
    {
        $user = User::factory()->create(['role' => 'staff']);
        $user->assignRole('staff');
        return $user;
    }

    private function createPatient()
    {
        return Patient::create([
            'patient_code' => 'PT-TEST-' . rand(1000, 9999),
            'full_name' => 'Nguyễn Văn Test',
            'phone' => '098765' . rand(1000, 9999),
            'gender' => 'male',
            'date_of_birth' => '1990-01-01',
        ]);
    }

    private function createMedicalRecord($patientId, $staffId, array $extra = [])
    {
        $attributes = array_merge([
            'patient_id' => $patientId,
            'staff_id' => $staffId,
            'visit_date' => now()->format('Y-m-d'),
            'case_type' => 'normal',
            'symptoms' => 'Triệu chứng test',
            'diagnosis' => 'Chẩn đoán test',
            'treatment_direction' => 'oral_only',
        ], $extra);

        if (!array_key_exists('diagnosis_confirmed_at', $attributes)
            && !empty($attributes['diagnosis'])
            && $attributes['diagnosis'] !== MedicalRecord::PENDING_DIAGNOSIS) {
            $attributes['diagnosis_confirmed_at'] = now();
            $attributes['diagnosis_confirmed_by'] = $staffId;
        }

        return MedicalRecord::create($attributes);
    }

    /**
     * Test unauthenticated access is blocked.
     */
    public function test_guest_cannot_access_ai_suggestions(): void
    {
        $response = $this->postJson(route('admin.ai.suggest'), [
            'medical_record_id' => 999999,
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test staff user without permission is forbidden.
     */
    public function test_staff_without_permission_cannot_access_ai_suggestions(): void
    {
        $staff = $this->createStaff();
        // Remove the permission check by ensuring they don't have Spatie permission (which standard staff doesn't have)
        
        $patient = $this->createPatient();
        $record = $this->createMedicalRecord($patient->id, $staff->id);

        $response = $this->actingAs($staff)->postJson(route('admin.ai.suggest'), [
            'medical_record_id' => $record->id,
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test validation failure with empty or short inputs.
     */
    public function test_ai_suggestions_validation_failures(): void
    {
        $admin = $this->createAdmin();

        // Empty medical_record_id
        $response = $this->actingAs($admin)->postJson(route('admin.ai.suggest'), [
            'medical_record_id' => '',
        ]);
        $response->assertStatus(422);

        // Invalid medical_record_id
        $response = $this->actingAs($admin)->postJson(route('admin.ai.suggest'), [
            'medical_record_id' => 'abc',
        ]);
        $response->assertStatus(422);

        // Non-existent medical_record_id
        $response = $this->actingAs($admin)->postJson(route('admin.ai.suggest'), [
            'medical_record_id' => 999999,
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

        $admin = $this->createAdmin();
        $this->seedAvailableHerbs(['Quế Chi', 'Bạch Thược', 'Cam Thảo'], 'oral');

        $patient = $this->createPatient();
        $record = $this->createMedicalRecord($patient->id, $admin->id, [
            'symptoms' => 'Ho nhiều, sốt cao, sợ gió sợ lạnh',
            'diagnosis' => 'Cảm mạo phong hàn',
            'treatment_direction' => 'oral_only',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.ai.suggest'), [
            'medical_record_id' => $record->id,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        
        $suggestions = $response->json('suggestions');
        $this->assertNotEmpty($suggestions['pre_prescription_note']);
        $this->assertNotEmpty($suggestions['treatment_principles']);
        $this->assertNotEmpty($suggestions['suggested_items']);
        $this->assertNotEmpty($suggestions['safety_and_followup']);
        $this->assertSame('herb', $suggestions['suggested_items'][0]['type']);
        $this->assertArrayHasKey('role', $suggestions['suggested_items'][0]);
        $this->assertArrayHasKey('draft_dosage', $suggestions['suggested_items'][0]);
        $this->assertArrayHasKey('inventory_status', $suggestions['suggested_items'][0]);
        $this->assertNotEmpty($suggestions['oral_herbs']);
        $this->assertEmpty($suggestions['external_herbs']);
        $this->assertEmpty($suggestions['therapy_services']);
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

        $admin = $this->createAdmin();
        
        // Seed both oral herbs and external herbs/packaged products
        $this->seedAvailableHerbs(['Đương Quy', 'Xuyên Khung', 'Bạch Thược', 'Cam Thảo'], 'oral');
        $this->seedAvailableHerbs(['Bó thuốc nam', 'Lọ rượu thuốc xoa bóp'], 'external');

        $patient = $this->createPatient();
        $record = $this->createMedicalRecord($patient->id, $admin->id, [
            'symptoms' => 'Té ngã sưng bầm cổ chân, đau buốt',
            'diagnosis' => 'Bong gân cổ chân',
            'treatment_direction' => 'combined',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.ai.suggest'), [
            'medical_record_id' => $record->id,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        
        $suggestions = $response->json('suggestions');
        $this->assertNotEmpty($suggestions['oral_herbs']);
        $this->assertNotEmpty($suggestions['external_herbs']);
        $this->assertNotEmpty($suggestions['therapy_services']);
        
        // Assert Bó thuốc nam is present
        $externalNames = array_column($suggestions['external_herbs'], 'custom_name');
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

        $admin = $this->createAdmin();
        $this->seedAvailableHerbs(['Bó thuốc nam', 'Lọ rượu thuốc xoa bóp'], 'external');

        $patient = $this->createPatient();
        $record = $this->createMedicalRecord($patient->id, $admin->id, [
            'symptoms' => 'Té ngã sưng bầm cổ chân, đau buốt',
            'diagnosis' => 'Bong gân cổ chân',
            'case_type' => 'musculoskeletal',
            'injury_location' => 'vùng cổ chân',
            'treatment_direction' => 'external_only',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.ai.suggest'), [
            'medical_record_id' => $record->id,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');

        $suggestions = $response->json('suggestions');
        $this->assertEmpty($suggestions['oral_herbs']);
        $this->assertNotEmpty($suggestions['external_herbs']);
        $this->assertNotEmpty($suggestions['therapy_services']);
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

        $admin = $this->createAdmin();

        $patient = $this->createPatient();
        $record = $this->createMedicalRecord($patient->id, $admin->id, [
            'symptoms' => 'Khòm khom người, đi đứng mỏi cơ thắt lưng và vai, có gù lưng',
            'diagnosis' => 'Gù lưng / Lệch cột sống',
            'treatment_direction' => 'combined',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.ai.suggest'), [
            'medical_record_id' => $record->id,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        
        $suggestions = $response->json('suggestions');
        $this->assertEmpty($suggestions['oral_herbs']);
        $this->assertEmpty($suggestions['external_herbs']);
        $this->assertNotEmpty($suggestions['therapy_services']);
        
        // Assert that the suggested therapy services are mapped correctly
        $therapyNames = array_column($suggestions['therapy_services'], 'custom_name');
        $this->assertTrue(in_array('Nắn chỉnh khớp xương', $therapyNames));
        $this->assertTrue(in_array('Theo dõi phục hồi vận động', $therapyNames));
    }

    private function seedAvailableHerbs(array $names, string $usageRoute = 'oral'): void
    {
        foreach ($names as $name) {
            MedicinalHerb::create([
                'name' => $name,
                'category' => 'Dược liệu bốc thuốc',
                'usage_type' => $usageRoute === 'oral' ? 'Sắc uống' : 'Dùng ngoài',
                'unit' => 'g',
                'stock_quantity' => 1000,
                'expiry_date' => now()->addYear()->toDateString(),
                'status' => 'active',
            ]);

            // Seed new inventory item and batch
            $item = InventoryItem::create([
                'name' => $name,
                'item_type' => 'herb',
                'usage_route' => $usageRoute,
                'unit' => 'g',
                'is_active' => true,
            ]);

            InventoryBatch::create([
                'inventory_item_id' => $item->id,
                'batch_number' => 'BATCH-' . strtoupper(\Illuminate\Support\Str::random(5)),
                'expiry_date' => now()->addYear()->toDateString(),
                'quantity_remaining' => 1000,
                'status' => 'available',
            ]);
        }
    }
}
