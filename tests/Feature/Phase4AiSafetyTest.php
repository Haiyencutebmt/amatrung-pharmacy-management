<?php

namespace Tests\Feature;

use App\Models\AiSuggestionLog;
use App\Models\MedicalRecord;
use App\Models\MedicinalHerb;
use App\Models\Patient;
use App\Models\User;
use App\Services\AiClinicalContextBuilder;
use App\Services\AiClinicalSuggestionService;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Phase4AiSafetyTest
 *
 * Bộ test kiểm thử an toàn module "AI gợi ý tham khảo" (Giai đoạn 4).
 * Gồm 26 test cases bắt buộc theo yêu cầu phê duyệt.
 */
class Phase4AiSafetyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RoleAndPermissionSeeder::class);
        // Mặc định tắt API key để test không gọi Gemini thật
        Config::set('services.gemini.api_key', null);
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function createAdmin(): User
    {
        $user = User::factory()->create(['role' => 'admin']);
        $user->assignRole('admin');
        return $user;
    }

    private function createPractitioner(): User
    {
        $user = User::factory()->create(['role' => 'staff']);
        $user->assignRole('practitioner');
        return $user;
    }

    private function createStaff(): User
    {
        $user = User::factory()->create(['role' => 'staff']);
        $user->assignRole('staff');
        return $user;
    }

    private function createRegularUser(): User
    {
        return User::factory()->create(['role' => 'user']);
    }

    private function createPatient(): Patient
    {
        return Patient::create([
            'patient_code' => 'PT-P4-' . rand(1000, 9999),
            'full_name'    => 'Nguyễn Văn An',
            'phone'        => '098765' . rand(1000, 9999),
            'gender'       => 'male',
            'date_of_birth' => '1990-05-15',
            'address'      => '123 Đường Trần Hưng Đạo, TP.HCM',
        ]);
    }

    private function createRecord(int $patientId, int $staffId, string $direction = 'oral_only', string $caseType = 'normal'): MedicalRecord
    {
        return MedicalRecord::create([
            'patient_id'          => $patientId,
            'staff_id'            => $staffId,
            'visit_date'          => now()->format('Y-m-d'),
            'case_type'           => $caseType,
            'symptoms'            => 'Đau nhức thắt lưng vùng L4-L5',
            'diagnosis'           => 'Thoái hóa cột sống thắt lưng',
            'diagnosis_confirmed_at' => now(),
            'diagnosis_confirmed_by' => $staffId,
            'treatment_direction' => $direction,
            'weight'              => 65.0,
            'height'              => 170.0,
            'allergies'           => 'Dị ứng Penicillin',
            'underlying_diseases' => 'Cao huyết áp',
        ]);
    }

    private function createHerb(string $name = 'Đương Quy', float $stock = 500, string $usageRoute = 'oral'): MedicinalHerb
    {
        $herb = MedicinalHerb::create([
            'name'           => $name,
            'unit'           => 'g',
            'stock_quantity' => $stock,
            'status'         => 'active',
            'price'          => 50000,
        ]);

        $item = \App\Models\InventoryItem::create([
            'name'        => $name,
            'item_type'   => ($usageRoute === 'external') ? 'external_product' : 'herb',
            'usage_route' => $usageRoute,
            'unit'        => 'g',
            'is_active'   => true,
        ]);

        \App\Models\InventoryBatch::create([
            'inventory_item_id'  => $item->id,
            'batch_number'       => 'BATCH-' . uniqid(),
            'expiry_date'        => now()->addYear()->toDateString(),
            'quantity_remaining' => $stock,
            'status'             => 'available',
        ]);

        return $herb;
    }

    private function getAiRoute(): string
    {
        return route('admin.ai.suggest');
    }

    private function getLogStatusRoute(): string
    {
        return route('admin.ai.suggest.log-status');
    }

    // ══════════════════════════════════════════════════════════════
    // I. PERMISSION & ACCESS CONTROL (TC01-TC04)
    // ══════════════════════════════════════════════════════════════

    /** TC01: Guest bị chặn */
    public function test_tc01_guest_cannot_access_ai_suggest(): void
    {
        $this->postJson($this->getAiRoute(), ['medical_record_id' => 1])
             ->assertStatus(401);
    }

    /** TC02: User thường (role=user) bị chặn */
    public function test_tc02_regular_user_without_permission_is_forbidden(): void
    {
        $user = $this->createRegularUser();

        $this->actingAs($user)
             ->postJson($this->getAiRoute(), ['medical_record_id' => 1])
             ->assertStatus(403);
    }

    /** TC03: Staff không có quyền use_ai_suggestion bị chặn */
    public function test_tc03_staff_without_ai_permission_is_forbidden(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        // Không assign role nào → không có permission use_ai_suggestion

        $this->actingAs($staff)
             ->postJson($this->getAiRoute(), ['medical_record_id' => 1])
             ->assertStatus(403);
    }

    /** TC04: Admin/Practitioner có quyền use_ai_suggestion được phép truy cập */
    public function test_tc04_admin_with_ai_permission_can_access(): void
    {
        $admin   = $this->createAdmin();
        $patient = $this->createPatient();
        $record  = $this->createRecord($patient->id, $admin->id);

        $response = $this->actingAs($admin)
                         ->postJson($this->getAiRoute(), ['medical_record_id' => $record->id]);

        // Phải trả 200, không cần thành công AI - chỉ cần qua permission
        $response->assertStatus(200);
    }

    // ══════════════════════════════════════════════════════════════
    // II. REQUEST VALIDATION (TC05-TC07)
    // ══════════════════════════════════════════════════════════════

    /** TC05: Request không có medical_record_id → 422 */
    public function test_tc05_missing_medical_record_id_returns_422(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
             ->postJson($this->getAiRoute(), [])
             ->assertStatus(422)
             ->assertJsonValidationErrors('medical_record_id');
    }

    /** TC06: medical_record_id không tồn tại → 422 */
    public function test_tc06_nonexistent_medical_record_id_returns_422(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
             ->postJson($this->getAiRoute(), ['medical_record_id' => 99999])
             ->assertStatus(422);
    }

    /** TC07: CHỈ nhận medical_record_id, các field PII khác bị bỏ qua */
    public function test_tc07_only_medical_record_id_is_accepted(): void
    {
        $admin   = $this->createAdmin();
        $patient = $this->createPatient();
        $record  = $this->createRecord($patient->id, $admin->id);

        // Gửi kèm PII - controller phải bỏ qua hoàn toàn
        $response = $this->actingAs($admin)
             ->postJson($this->getAiRoute(), [
                 'medical_record_id' => $record->id,
                 'symptoms'          => 'Hack attempt',
                 'diagnosis'         => 'Injected',
                 'patient_name'      => 'Secret Name',
                 'phone'             => '0987654321',
             ]);

        $response->assertStatus(200);
        // Đảm bảo không trả lại dữ liệu PII
        $response->assertJsonMissing(['patient_name']);
        $response->assertJsonMissing(['phone']);
    }

    // ══════════════════════════════════════════════════════════════
    // III. AUTHORIZATION (TC08-TC09)
    // ══════════════════════════════════════════════════════════════

    /** TC08: Staff không phải staff phụ trách bệnh án bị chặn */
    public function test_tc08_unassigned_staff_is_denied_access_to_record(): void
    {
        $practitioner = $this->createPractitioner();
        $otherStaff   = $this->createStaff();
        $patient      = $this->createPatient();
        $record       = $this->createRecord($patient->id, $practitioner->id);

        // otherStaff không phải staff_id → bị chặn
        $response = $this->actingAs($otherStaff)
                         ->postJson($this->getAiRoute(), ['medical_record_id' => $record->id]);

        $response->assertStatus(403);
    }

    /** TC09: Admin có thể truy cập bệnh án của bất kỳ staff nào */
    public function test_tc09_admin_can_access_any_staff_record(): void
    {
        $admin        = $this->createAdmin();
        $practitioner = $this->createPractitioner();
        $patient      = $this->createPatient();
        $record       = $this->createRecord($patient->id, $practitioner->id);

        $response = $this->actingAs($admin)
                         ->postJson($this->getAiRoute(), ['medical_record_id' => $record->id]);

        $response->assertStatus(200);
    }

    // ══════════════════════════════════════════════════════════════
    // IV. CONTEXT BUILDER - PII SAFETY (TC10-TC12)
    // ══════════════════════════════════════════════════════════════

    /** TC10: Payload không chứa patient_id, staff_id, record_code, phone */
    public function test_tc10_payload_excludes_pii_fields(): void
    {
        $admin   = $this->createAdmin();
        $patient = $this->createPatient();
        $record  = $this->createRecord($patient->id, $admin->id);

        $builder = new AiClinicalContextBuilder();
        $payload = $builder->build($record->load('patient'));

        // KHÔNG có PII định danh
        $flatPayload = json_encode($payload);
        $this->assertStringNotContainsString($patient->full_name, $flatPayload);
        $this->assertStringNotContainsString($patient->phone, $flatPayload);
        $this->assertStringNotContainsString($record->record_code, $flatPayload);
        $this->assertArrayNotHasKey('patient_id', $payload['clinical']);
        $this->assertArrayNotHasKey('staff_id', $payload['clinical']);
        $this->assertArrayNotHasKey('record_code', $payload['clinical']);
    }

    /** TC11: Payload chứa đúng các field trong allowlist */
    public function test_tc11_payload_contains_only_allowlisted_fields(): void
    {
        $admin   = $this->createAdmin();
        $patient = $this->createPatient();
        $record  = $this->createRecord($patient->id, $admin->id);

        $builder = new AiClinicalContextBuilder();
        $payload = $builder->build($record->load('patient'));

        $this->assertArrayHasKey('clinical', $payload);
        $this->assertArrayHasKey('symptoms', $payload['clinical']);
        $this->assertArrayHasKey('diagnosis', $payload['clinical']);
        $this->assertArrayHasKey('treatment_direction', $payload['clinical']);

        // Có tuổi ẩn danh
        $this->assertArrayHasKey('age', $payload);
        $this->assertIsInt($payload['age']);
    }

    /** TC12: Date of birth chính xác KHÔNG xuất hiện trong payload */
    public function test_tc12_date_of_birth_is_not_in_payload(): void
    {
        $admin   = $this->createAdmin();
        $patient = $this->createPatient();
        $record  = $this->createRecord($patient->id, $admin->id);

        $builder = new AiClinicalContextBuilder();
        $payload = $builder->build($record->load('patient'));

        $flatPayload = json_encode($payload);
        $this->assertStringNotContainsString('1990-05-15', $flatPayload);
        $this->assertStringNotContainsString('date_of_birth', $flatPayload);
    }

    // ══════════════════════════════════════════════════════════════
    // V. TREATMENT DIRECTION LOGIC (TC13-TC16)
    // ══════════════════════════════════════════════════════════════

    /** TC13: Referral → available_inventory rỗng */
    public function test_tc13_referral_returns_empty_inventory(): void
    {
        $this->createHerb('Cam Thảo', 100);

        $builder   = new AiClinicalContextBuilder();
        $inventory = $builder->buildAvailableInventory('referral');

        $this->assertEmpty($inventory);
    }

    /** TC14: Referral → suggestions luôn rỗng */
    public function test_tc14_referral_returns_empty_suggestions(): void
    {
        $admin   = $this->createAdmin();
        $patient = $this->createPatient();
        $record  = $this->createRecord($patient->id, $admin->id, 'referral');

        $response = $this->actingAs($admin)
                         ->postJson($this->getAiRoute(), ['medical_record_id' => $record->id]);

        $response->assertStatus(200)
                 ->assertJson(['status' => 'referral'])
                 ->assertJsonPath('suggestions', []);
    }

    /** TC15: oral_only → available_inventory chỉ có vị thuốc uống */
    public function test_tc15_oral_only_direction_returns_inventory(): void
    {
        $this->createHerb('Bạch Thược', 200);

        $builder   = new AiClinicalContextBuilder();
        $inventory = $builder->buildAvailableInventory('oral_only');

        $this->assertNotEmpty($inventory);
        $this->assertEquals('Bạch Thược', $inventory[0]['name']);
    }

    /** TC16: external_only → inventory vẫn trả về (cho tra cứu), nhưng AI post-verify sẽ lọc */
    public function test_tc16_external_only_direction_returns_inventory_for_lookup(): void
    {
        $this->createHerb('Cam Thảo', 100, 'external');

        $builder   = new AiClinicalContextBuilder();
        $inventory = $builder->buildAvailableInventory('external_only');

        // external_only không phải referral → inventory vẫn trả về
        $this->assertNotEmpty($inventory);
    }

    // ══════════════════════════════════════════════════════════════
    // VI. AI SERVICE - NO FALLBACK (TC17-TC18)
    // ══════════════════════════════════════════════════════════════

    /** TC17: Khi không có API key → trả về ai_unavailable, KHÔNG fallback heuristic */
    public function test_tc17_no_api_key_returns_ai_unavailable(): void
    {
        Config::set('services.gemini.api_key', null);

        $admin   = $this->createAdmin();
        $patient = $this->createPatient();
        $record  = $this->createRecord($patient->id, $admin->id);

        $response = $this->actingAs($admin)
                         ->postJson($this->getAiRoute(), ['medical_record_id' => $record->id]);

        $response->assertStatus(200)
                 ->assertJson(['status' => 'ai_unavailable']);

        // KHÔNG có oral_herbs trong suggestions (không fallback heuristic)
        $suggestions = $response->json('suggestions');
        $this->assertEmpty($suggestions);
    }

    /** TC18: Khi Gemini lỗi HTTP → trả về ai_unavailable */
    public function test_tc18_gemini_error_returns_ai_unavailable(): void
    {
        Config::set('services.gemini.api_key', 'test-key');

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response('Internal Error', 500),
        ]);

        $admin   = $this->createAdmin();
        $patient = $this->createPatient();
        $record  = $this->createRecord($patient->id, $admin->id);

        $response = $this->actingAs($admin)
                         ->postJson($this->getAiRoute(), ['medical_record_id' => $record->id]);

        $response->assertStatus(200)
                 ->assertJson(['status' => 'ai_unavailable']);
    }

    // ══════════════════════════════════════════════════════════════
    // VII. RESPONSE FORMAT - NO DOSAGE (TC19-TC20)
    // ══════════════════════════════════════════════════════════════

    /** TC19: Response schema không có trường 'dosage' riêng biệt */
    public function test_tc19_response_does_not_contain_dosage_field(): void
    {
        Config::set('services.gemini.api_key', 'test-key');

        $geminiResponse = [
            'candidates' => [[
                'content' => [
                    'parts' => [[
                        'text' => json_encode([
                            'reasoning'            => 'Test reasoning',
                            'safety_note'          => 'Test safety',
                            'follow_up_suggestion' => 'Follow up',
                            'oral_herbs'           => [
                                ['herb_name' => 'Đương Quy', 'usage_note' => 'Bổ huyết'],
                            ],
                            'external_herbs'   => [],
                            'therapy_services' => [],
                        ]),
                    ]],
                ],
            ]],
        ];

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response($geminiResponse, 200),
        ]);

        $admin   = $this->createAdmin();
        $patient = $this->createPatient();
        $record  = $this->createRecord($patient->id, $admin->id);
        $this->createHerb('Đương Quy', 500);

        $response = $this->actingAs($admin)
                         ->postJson($this->getAiRoute(), ['medical_record_id' => $record->id]);

        $response->assertStatus(200);
        $suggestions = $response->json('suggestions');

        // Kiểm tra oral_herbs KHÔNG có trường dosage
        if (!empty($suggestions['oral_herbs'])) {
            foreach ($suggestions['oral_herbs'] as $herb) {
                $this->assertArrayNotHasKey('dosage', $herb);
                $this->assertArrayHasKey('herb_name', $herb);
                $this->assertArrayHasKey('usage_note', $herb);
            }
        }
    }

    /** TC20: Response có disclaimer bắt buộc */
    public function test_tc20_response_contains_disclaimer(): void
    {
        $admin   = $this->createAdmin();
        $patient = $this->createPatient();
        $record  = $this->createRecord($patient->id, $admin->id);

        $response = $this->actingAs($admin)
                         ->postJson($this->getAiRoute(), ['medical_record_id' => $record->id]);

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('disclaimer'));
        $this->assertStringContainsString('GỢI Ý THAM KHẢO', $response->json('disclaimer'));
    }

    // ══════════════════════════════════════════════════════════════
    // VIII. LOGGING (TC21-TC23)
    // ══════════════════════════════════════════════════════════════

    /** TC21: Gọi AI tạo bản ghi trong ai_suggestion_logs */
    public function test_tc21_ai_call_creates_log_entry(): void
    {
        $admin   = $this->createAdmin();
        $patient = $this->createPatient();
        $record  = $this->createRecord($patient->id, $admin->id);

        $this->actingAs($admin)
             ->postJson($this->getAiRoute(), ['medical_record_id' => $record->id]);

        $this->assertDatabaseHas('ai_suggestion_logs', [
            'user_id'           => $admin->id,
            'medical_record_id' => $record->id,
            'status'            => 'failed', // key is null in test setup -> failed
        ]);
    }

    /** TC22: Log entry payload KHÔNG chứa PII bệnh nhân */
    public function test_tc22_log_payload_excludes_patient_pii(): void
    {
        $admin   = $this->createAdmin();
        $patient = $this->createPatient();
        $record  = $this->createRecord($patient->id, $admin->id);

        $this->actingAs($admin)
             ->postJson($this->getAiRoute(), ['medical_record_id' => $record->id]);

        $log     = AiSuggestionLog::latest()->first();
        $payload = is_array($log->payload) ? json_encode($log->payload) : $log->payload;

        $this->assertStringNotContainsString($patient->full_name, $payload);
        $this->assertStringNotContainsString($patient->phone, $payload);
    }

    /** TC23: Cập nhật interaction_status thành công */
    public function test_tc23_update_log_status_works(): void
    {
        $admin   = $this->createAdmin();
        $patient = $this->createPatient();
        $record  = $this->createRecord($patient->id, $admin->id);

        // Tạo log entry
        $this->actingAs($admin)
             ->postJson($this->getAiRoute(), ['medical_record_id' => $record->id]);

        $log = AiSuggestionLog::latest()->first();
        $this->assertNotNull($log);

        // Cập nhật status
        $response = $this->actingAs($admin)
                         ->postJson($this->getLogStatusRoute(), [
                             'log_id'             => $log->id,
                             'interaction_status' => 'referenced',
                         ]);

        $response->assertStatus(200)
                 ->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('ai_suggestion_logs', [
            'id'     => $log->id,
            'status' => 'referenced',
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    // IX. LOG STATUS VALIDATION (TC24-TC25)
    // ══════════════════════════════════════════════════════════════

    /** TC24: interaction_status không hợp lệ → 422 */
    public function test_tc24_invalid_interaction_status_returns_422(): void
    {
        $admin = $this->createAdmin();

        $log = AiSuggestionLog::create([
            'user_id'           => $admin->id,
            'medical_record_id' => null,
            'payload'           => [],
            'response'          => [],
            'status'            => 'generated',
        ]);

        $response = $this->actingAs($admin)
                         ->postJson($this->getLogStatusRoute(), [
                             'log_id'             => $log->id,
                             'interaction_status' => 'applied_auto',
                         ]);

        $response->assertStatus(422);
    }

    /** TC25: Staff khác không thể cập nhật log của người khác */
    public function test_tc25_other_staff_cannot_update_another_users_log(): void
    {
        $practitioner = $this->createPractitioner();
        $otherStaff   = $this->createStaff();

        $log = AiSuggestionLog::create([
            'user_id'           => $practitioner->id,
            'medical_record_id' => null,
            'payload'           => [],
            'response'          => [],
            'status'            => 'generated',
        ]);

        $response = $this->actingAs($otherStaff)
                         ->postJson($this->getLogStatusRoute(), [
                             'log_id'             => $log->id,
                             'interaction_status' => 'not_used',
                         ]);

        $response->assertStatus(403);
    }

    // ══════════════════════════════════════════════════════════════
    // X. DATABASE SCHEMA (TC26)
    // ══════════════════════════════════════════════════════════════

    /** TC26: Bảng ai_suggestion_logs có cột error_message */
    public function test_tc26_ai_suggestion_logs_has_error_message_column(): void
    {
        $this->assertTrue(Schema::hasTable('ai_suggestion_logs'));
        $this->assertTrue(Schema::hasColumn('ai_suggestion_logs', 'error_message'));
        $this->assertTrue(Schema::hasColumn('ai_suggestion_logs', 'payload'));
        $this->assertTrue(Schema::hasColumn('ai_suggestion_logs', 'response'));
        $this->assertTrue(Schema::hasColumn('ai_suggestion_logs', 'status'));
    }

    public function test_staff_permissions_only_use_spatie_no_legacy_fallback(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'legacy_permissions_json' => ['use_ai_suggestion'],
        ]);
        
        $patient = $this->createPatient();
        $record  = $this->createRecord($patient->id, $staff->id);

        // Even though they have 'use_ai_suggestion' in legacy JSON, they must be blocked
        $response = $this->actingAs($staff)
                         ->postJson($this->getAiRoute(), ['medical_record_id' => $record->id]);
        $response->assertStatus(403);

        // Grant permission via Spatie
        $staff->givePermissionTo('use_ai_suggestion');

        $response = $this->actingAs($staff)
                         ->postJson($this->getAiRoute(), ['medical_record_id' => $record->id]);
        $response->assertStatus(200);

        // Revoke permission via Spatie
        $staff->revokePermissionTo('use_ai_suggestion');

        $response = $this->actingAs($staff)
                         ->postJson($this->getAiRoute(), ['medical_record_id' => $record->id]);
        $response->assertStatus(403);
    }

    public function test_ai_only_reads_new_inventory_batches_not_legacy_stock(): void
    {
        // 1. Seed legacy herb with stock > 0
        MedicinalHerb::create([
            'name'           => 'Nhân Sâm',
            'unit'           => 'g',
            'stock_quantity' => 500,
            'status'         => 'active',
            'price'          => 50000,
        ]);

        // Do not seed new inventory for 'Nhân Sâm'
        $builder = new AiClinicalContextBuilder();
        $inventory = $builder->buildAvailableInventory('oral_only');

        // Confirm 'Nhân Sâm' is not in available inventory
        $names = array_column($inventory, 'name');
        $this->assertNotContains('Nhân Sâm', $names);

        // 2. Seed a new inventory item but with expired batch
        $item = \App\Models\InventoryItem::create([
            'name'        => 'Linh Chi',
            'item_type'   => 'herb',
            'usage_route' => 'oral',
            'unit'        => 'g',
            'is_active'   => true,
        ]);
        \App\Models\InventoryBatch::create([
            'inventory_item_id'  => $item->id,
            'batch_number'       => 'BATCH-EXP',
            'expiry_date'        => now()->subDay()->toDateString(),
            'quantity_remaining' => 100,
            'status'             => 'available',
        ]);

        $inventory = $builder->buildAvailableInventory('oral_only');
        $names = array_column($inventory, 'name');
        $this->assertNotContains('Linh Chi', $names);

        // 3. Seed a new inventory item with null expiry date
        $item2 = \App\Models\InventoryItem::create([
            'name'        => 'Táo Tàu',
            'item_type'   => 'herb',
            'usage_route' => 'oral',
            'unit'        => 'g',
            'is_active'   => true,
        ]);
        \App\Models\InventoryBatch::create([
            'inventory_item_id'  => $item2->id,
            'batch_number'       => 'BATCH-NULL',
            'expiry_date'        => null,
            'quantity_remaining' => 100,
            'status'             => 'available',
        ]);

        $inventory = $builder->buildAvailableInventory('oral_only');
        $names = array_column($inventory, 'name');
        $this->assertNotContains('Táo Tàu', $names);

        // 4. Seed a valid new inventory item (no legacy herb)
        $item3 = \App\Models\InventoryItem::create([
            'name'        => 'Đương Quy',
            'item_type'   => 'herb',
            'usage_route' => 'oral',
            'unit'        => 'g',
            'is_active'   => true,
        ]);
        \App\Models\InventoryBatch::create([
            'inventory_item_id'  => $item3->id,
            'batch_number'       => 'BATCH-OK',
            'expiry_date'        => now()->addYear()->toDateString(),
            'quantity_remaining' => 100,
            'status'             => 'available',
        ]);

        $inventory = $builder->buildAvailableInventory('oral_only');
        $names = array_column($inventory, 'name');
        $this->assertContains('Đương Quy', $names);
    }

    public function test_ai_response_post_verification_filters_out_of_stock_items(): void
    {
        Config::set('services.gemini.api_key', 'test-key');

        $geminiResponse = [
            'candidates' => [[
                'content' => [
                    'parts' => [[
                        'text' => json_encode([
                            'reasoning'            => 'Test reasoning',
                            'safety_note'          => 'Test safety',
                            'follow_up_suggestion' => 'Follow up',
                            'oral_herbs'           => [
                                ['herb_name' => 'Nhân Sâm', 'usage_note' => 'Bổ khí (không có trong kho)'],
                                ['herb_name' => 'Đương Quy', 'usage_note' => 'Bổ huyết (có trong kho)'],
                            ],
                            'external_herbs'   => [],
                            'therapy_services' => [],
                        ]),
                    ]],
                ],
            ]],
        ];

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response($geminiResponse, 200),
        ]);

        $admin   = $this->createAdmin();
        $patient = $this->createPatient();
        $record  = $this->createRecord($patient->id, $admin->id);

        $this->createHerb('Đương Quy', 500);

        $response = $this->actingAs($admin)
                         ->postJson($this->getAiRoute(), ['medical_record_id' => $record->id]);

        $response->assertStatus(200);
        $suggestions = $response->json('suggestions');

        $oralNames = array_column($suggestions['oral_herbs'], 'herb_name');
        $this->assertContains('Đương Quy', $oralNames);
        $this->assertNotContains('Nhân Sâm', $oralNames);
    }

    public function test_ai_response_post_verification_forces_empty_on_referral(): void
    {
        $admin   = $this->createAdmin();
        $patient = $this->createPatient();
        $record  = $this->createRecord($patient->id, $admin->id, 'referral');

        $response = $this->actingAs($admin)
                         ->postJson($this->getAiRoute(), ['medical_record_id' => $record->id]);

        $response->assertStatus(200);
        $this->assertEquals('referral', $response->json('status'));
        $this->assertEmpty($response->json('suggestions'));
    }

    public function test_double_dispense_request_deducts_stock_only_once(): void
    {
        $admin   = $this->createAdmin();
        $patient = $this->createPatient();
        $record  = $this->createRecord($patient->id, $admin->id);

        $item = \App\Models\InventoryItem::create([
            'name'        => 'Đương Quy',
            'item_type'   => 'herb',
            'usage_route' => 'oral',
            'unit'        => 'g',
            'is_active'   => true,
        ]);
        $batch = \App\Models\InventoryBatch::create([
            'inventory_item_id'  => $item->id,
            'batch_number'       => 'BATCH-OK',
            'expiry_date'        => now()->addYear()->toDateString(),
            'quantity_remaining' => 100,
            'status'             => 'available',
        ]);

        $service = app(\App\Services\PrescriptionService::class);
        $prescription = $service->createPrescription([
            'medical_record_id' => $record->id,
            'num_of_doses' => 2,
            'items' => [
                ['inventory_item_id' => $item->id, 'quantity_per_dose' => 10, 'unit' => 'g'],
            ],
        ], $admin->id);

        $res1 = $service->dispensePrescription($prescription, $admin->id);
        $this->assertTrue($res1);
        $this->assertEquals(80, $batch->fresh()->quantity_remaining);
        $this->assertEquals('dispensed', $prescription->fresh()->status);

        $this->expectException(\Exception::class);
        $service->dispensePrescription($prescription, $admin->id);
    }

    public function test_insufficient_stock_rolls_back_status_and_movements(): void
    {
        $admin   = $this->createAdmin();
        $patient = $this->createPatient();
        $record  = $this->createRecord($patient->id, $admin->id);

        $itemA = \App\Models\InventoryItem::create([
            'name'        => 'Đương Quy',
            'item_type'   => 'herb',
            'usage_route' => 'oral',
            'unit'        => 'g',
            'is_active'   => true,
        ]);
        $batchA = \App\Models\InventoryBatch::create([
            'inventory_item_id'  => $itemA->id,
            'batch_number'       => 'BATCH-A',
            'expiry_date'        => now()->addYear()->toDateString(),
            'quantity_remaining' => 100,
            'status'             => 'available',
        ]);

        $itemB = \App\Models\InventoryItem::create([
            'name'        => 'Nhân Sâm',
            'item_type'   => 'herb',
            'usage_route' => 'oral',
            'unit'        => 'g',
            'is_active'   => true,
        ]);
        $batchB = \App\Models\InventoryBatch::create([
            'inventory_item_id'  => $itemB->id,
            'batch_number'       => 'BATCH-B',
            'expiry_date'        => now()->addYear()->toDateString(),
            'quantity_remaining' => 5,
            'status'             => 'available',
        ]);

        $service = app(\App\Services\PrescriptionService::class);
        $prescription = $service->createPrescription([
            'medical_record_id' => $record->id,
            'num_of_doses' => 2,
            'items' => [
                ['inventory_item_id' => $itemA->id, 'quantity_per_dose' => 10, 'unit' => 'g'],
                ['inventory_item_id' => $itemB->id, 'quantity_per_dose' => 10, 'unit' => 'g'],
            ],
        ], $admin->id);

        $initialMovementsCount = \App\Models\StockMovement::count();

        try {
            $service->dispensePrescription($prescription, $admin->id);
            $this->fail('Dispense should have failed due to insufficient stock.');
        } catch (\Exception $e) {
            $this->assertStringContainsString('Không đủ tồn kho', $e->getMessage());
        }

        $this->assertEquals('confirmed', $prescription->fresh()->status);
        $this->assertEquals(100, $batchA->fresh()->quantity_remaining);
        $this->assertEquals(5, $batchB->fresh()->quantity_remaining);
        $this->assertEquals($initialMovementsCount, \App\Models\StockMovement::count());
    }

    public function test_interaction_status_only_updates_log_no_prescription_or_stock_changes(): void
    {
        $admin   = $this->createAdmin();
        $patient = $this->createPatient();
        $record  = $this->createRecord($patient->id, $admin->id);

        $log = AiSuggestionLog::create([
            'user_id'           => $admin->id,
            'medical_record_id' => $record->id,
            'payload'           => [],
            'response'          => [],
            'status'            => 'generated',
        ]);

        $initialPrescriptionsCount = \App\Models\Prescription::count();
        $initialMovementsCount     = \App\Models\StockMovement::count();

        $response = $this->actingAs($admin)->postJson($this->getLogStatusRoute(), [
            'log_id'             => $log->id,
            'interaction_status' => 'referenced',
        ]);

        $response->assertStatus(200);

        $this->assertEquals('referenced', $log->fresh()->status);
        $this->assertEquals($initialPrescriptionsCount, \App\Models\Prescription::count());
        $this->assertEquals($initialMovementsCount, \App\Models\StockMovement::count());
    }
}
