<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\InventoryItem;
use App\Models\InventoryBatch;
use App\Models\StockMovement;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\MedicalRecordAttachment;
use App\Services\InventoryService;
use App\Services\PrescriptionService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Database\Seeders\RoleAndPermissionSeeder;

class Phase3BIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    // ── Helper methods ──

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

    private function createMedicalRecord($patientId, $staffId, $direction = 'oral_only')
    {
        return MedicalRecord::create([
            'patient_id' => $patientId,
            'staff_id' => $staffId,
            'visit_date' => now()->format('Y-m-d'),
            'case_type' => 'normal',
            'symptoms' => 'Triệu chứng test',
            'diagnosis' => 'Chẩn đoán test',
            'treatment_direction' => $direction,
        ]);
    }

    private function createInventoryItem($type = 'herb', $route = 'oral', $name = null)
    {
        return InventoryItem::create([
            'name' => $name ?? 'Test Item ' . rand(100, 999),
            'item_type' => $type,
            'usage_route' => $route,
            'unit' => $type === 'herb' ? 'g' : 'Hộp',
            'is_active' => true,
        ]);
    }

    private function createBatch($itemId, $qty = 100, $status = 'available', $expiryDate = null)
    {
        return InventoryBatch::create([
            'inventory_item_id' => $itemId,
            'batch_number' => 'BATCH-' . rand(1000, 9999),
            'expiry_date' => $expiryDate ?? now()->addMonths(6)->format('Y-m-d'),
            'quantity_remaining' => $qty,
            'status' => $status,
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    // 1. KIỂM TRA KHO - Trang danh sách kho hiển thị đúng
    // ══════════════════════════════════════════════════════════════

    public function test_01_inventory_index_displays_items()
    {
        $admin = $this->createAdmin();
        $item = $this->createInventoryItem('herb', 'oral', 'Cam thảo test');
        $this->createBatch($item->id, 50);

        $response = $this->actingAs($admin)->get(route('admin.inventory.index'));
        $response->assertStatus(200);
        $response->assertSee('Cam thảo test');
    }

    // ══════════════════════════════════════════════════════════════
    // 2. KIỂM TRA KHO - Bộ lọc expired hoạt động
    // ══════════════════════════════════════════════════════════════

    public function test_02_inventory_filter_expired_batches()
    {
        $admin = $this->createAdmin();
        $item = $this->createInventoryItem('herb', 'oral', 'Thuốc hết hạn');
        $this->createBatch($item->id, 10, 'expired', now()->subDays(10)->format('Y-m-d'));

        $response = $this->actingAs($admin)->get(route('admin.inventory.index', ['filter' => 'expired']));
        $response->assertStatus(200);
        $response->assertSee('Thuốc hết hạn');
    }

    // ══════════════════════════════════════════════════════════════
    // 3. KIỂM TRA KHO - Chi tiết mặt hàng hiển thị lô và thẻ kho
    // ══════════════════════════════════════════════════════════════

    public function test_03_inventory_show_displays_batches_and_movements()
    {
        $admin = $this->createAdmin();
        $item = $this->createInventoryItem('herb', 'oral', 'Chi tiết test');
        $batch = $this->createBatch($item->id, 100);
        StockMovement::create([
            'inventory_batch_id' => $batch->id,
            'movement_type' => 'opening_balance',
            'quantity' => 100,
            'performed_by' => $admin->id,
            'note' => 'Số dư đầu kỳ',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.inventory.show', $item->id));
        $response->assertStatus(200);
        $response->assertSee('Chi tiết test');
        $response->assertSee($batch->batch_number);
    }

    // ══════════════════════════════════════════════════════════════
    // 4. KIỂM TRA KHO - Thêm mặt hàng mới thành công
    // ══════════════════════════════════════════════════════════════

    public function test_04_inventory_store_new_item_with_batch()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.inventory.store'), [
            'name' => 'Dược liệu ABC',
            'item_type' => 'herb',
            'usage_route' => 'oral',
            'unit' => 'g',
            'batch_code' => 'L001-2026',
            'quantity' => 500,
            'expiry_date' => now()->addYear()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('admin.inventory.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('inventory_items', ['name' => 'Dược liệu ABC']);
        $this->assertDatabaseHas('inventory_batches', ['batch_number' => 'L001-2026']);
        $this->assertDatabaseHas('stock_movements', ['movement_type' => 'import', 'quantity' => 500]);
    }

    // ══════════════════════════════════════════════════════════════
    // 5. KIỂM TRA KHO - Nhập lô mới cho mặt hàng hiện có
    // ══════════════════════════════════════════════════════════════

    public function test_05_inventory_store_new_batch_for_existing_item()
    {
        $admin = $this->createAdmin();
        $item = $this->createInventoryItem('herb', 'oral');

        $response = $this->actingAs($admin)->post(route('admin.inventory.batch.store', $item->id), [
            'batch_code' => 'L002-2026',
            'quantity' => 200,
            'expiry_date' => now()->addMonths(3)->format('Y-m-d'),
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('inventory_batches', [
            'inventory_item_id' => $item->id,
            'batch_number' => 'L002-2026',
            'quantity_remaining' => 200,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'movement_type' => 'import',
            'quantity' => 200,
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    // 6. KIỂM TRA KHO - Cập nhật hạn dùng lô unknown_expiry
    // ══════════════════════════════════════════════════════════════

    public function test_06_update_unknown_expiry_batch()
    {
        $admin = $this->createAdmin();
        $item = $this->createInventoryItem();
        $batch = $this->createBatch($item->id, 50, 'unknown_expiry', null);

        $newExpiry = now()->addMonths(6)->format('Y-m-d');
        $response = $this->actingAs($admin)->put(route('admin.inventory.batch.update', $batch->id), [
            'expiry_date' => $newExpiry,
        ]);

        $response->assertSessionHas('success');
        $batch->refresh();
        $this->assertEquals('available', $batch->status);
        $this->assertEquals($newExpiry, $batch->expiry_date->format('Y-m-d'));
    }

    // ══════════════════════════════════════════════════════════════
    // 7. KIỂM TRA KHO - Toggle khóa lô hàng
    // ══════════════════════════════════════════════════════════════

    public function test_07_toggle_batch_status_block_and_unblock()
    {
        $admin = $this->createAdmin();
        $item = $this->createInventoryItem();
        $batch = $this->createBatch($item->id, 50, 'available');

        // Block
        $response = $this->actingAs($admin)->patch(route('admin.inventory.batch.toggle', $batch->id));
        $response->assertSessionHas('success');
        $batch->refresh();
        $this->assertEquals('blocked', $batch->status);

        // Unblock
        $response = $this->actingAs($admin)->patch(route('admin.inventory.batch.toggle', $batch->id));
        $batch->refresh();
        $this->assertEquals('available', $batch->status);
    }

    // ══════════════════════════════════════════════════════════════
    // 8. KIỂM TRA KHO - Badge dùng ngoài da hiển thị
    // ══════════════════════════════════════════════════════════════

    public function test_08_external_usage_badge_displayed()
    {
        $admin = $this->createAdmin();
        $item = $this->createInventoryItem('packaged_product', 'external', 'Thuốc bó test');
        $this->createBatch($item->id, 20);

        $response = $this->actingAs($admin)->get(route('admin.inventory.index'));
        $response->assertStatus(200);
        $response->assertSee('Dùng ngoài da - Không được uống');
    }

    // ══════════════════════════════════════════════════════════════
    // 9. BỆNH ÁN - Chặn kê đơn khi referral
    // ══════════════════════════════════════════════════════════════

    public function test_09_referral_blocks_prescription_creation()
    {
        $admin = $this->createAdmin();
        $patient = $this->createPatient();
        $record = $this->createMedicalRecord($patient->id, $admin->id, 'referral');
        $record->referral_reason = 'Cần phẫu thuật';
        $record->save();

        $response = $this->actingAs($admin)->get(route('admin.medical-records.prescriptions.create', $record));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // ══════════════════════════════════════════════════════════════
    // 10. BỆNH ÁN - Referral label đúng
    // ══════════════════════════════════════════════════════════════

    public function test_10_referral_label_correct()
    {
        $admin = $this->createAdmin();
        $patient = $this->createPatient();
        $record = $this->createMedicalRecord($patient->id, $admin->id, 'referral');
        $record->referral_reason = 'Cần phẫu thuật tây y';
        $record->save();

        $response = $this->actingAs($admin)->get(route('admin.medical-records.show', $record));
        $response->assertStatus(200);
        $response->assertSee('Khuyến nghị chuyển đến cơ sở y tế phù hợp');
    }

    // ══════════════════════════════════════════════════════════════
    // 11. ĐÍNH KÈM - Upload file thành công
    // ══════════════════════════════════════════════════════════════

    public function test_11_upload_attachment_success()
    {
        Storage::fake('local');
        $admin = $this->createAdmin();
        $patient = $this->createPatient();
        $record = $this->createMedicalRecord($patient->id, $admin->id);

        $file = UploadedFile::fake()->image('xray_scan.jpg', 800, 600)->size(2048);

        $response = $this->actingAs($admin)->post(
            route('admin.medical-records.attachments.upload', $record->id),
            ['attachments' => [$file]]
        );

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('medical_record_attachments', [
            'medical_record_id' => $record->id,
            'file_name' => 'xray_scan.jpg',
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    // 12. ĐÍNH KÈM - Reject file quá 5MB
    // ══════════════════════════════════════════════════════════════

    public function test_12_reject_attachment_over_5mb()
    {
        Storage::fake('local');
        $admin = $this->createAdmin();
        $patient = $this->createPatient();
        $record = $this->createMedicalRecord($patient->id, $admin->id);

        $file = UploadedFile::fake()->create('bigfile.pdf', 6000, 'application/pdf');

        $response = $this->actingAs($admin)->post(
            route('admin.medical-records.attachments.upload', $record->id),
            ['attachments' => [$file]]
        );

        $response->assertSessionHasErrors();
    }

    // ══════════════════════════════════════════════════════════════
    // 13. ĐƠN THUỐC - oral_only chặn thuốc dùng ngoài
    // ══════════════════════════════════════════════════════════════

    public function test_13_oral_only_blocks_external_item()
    {
        $admin = $this->createAdmin();
        $patient = $this->createPatient();
        $record = $this->createMedicalRecord($patient->id, $admin->id, 'oral_only');

        $extItem = $this->createInventoryItem('packaged_product', 'external', 'Cồn test');

        $response = $this->actingAs($admin)->post(route('admin.prescriptions.store'), [
            'medical_record_id' => $record->id,
            'num_of_doses' => 1,
            'items' => [[
                'item_type' => 'packaged_product',
                'inventory_item_id' => $extItem->id,
                'quantity_per_dose' => 1,
                'unit' => 'Hộp',
            ]],
        ]);

        $response->assertSessionHas('error');
    }

    // ══════════════════════════════════════════════════════════════
    // 14. ĐƠN THUỐC - external_only chặn thuốc uống
    // ══════════════════════════════════════════════════════════════

    public function test_14_external_only_blocks_oral_item()
    {
        $admin = $this->createAdmin();
        $patient = $this->createPatient();
        $record = $this->createMedicalRecord($patient->id, $admin->id, 'external_only');

        $herb = $this->createInventoryItem('herb', 'oral', 'Cam thảo');

        $response = $this->actingAs($admin)->post(route('admin.prescriptions.store'), [
            'medical_record_id' => $record->id,
            'num_of_doses' => 1,
            'items' => [[
                'item_type' => 'herb',
                'inventory_item_id' => $herb->id,
                'quantity_per_dose' => 10,
                'unit' => 'g',
            ]],
        ]);

        $response->assertSessionHas('error');
    }

    // ══════════════════════════════════════════════════════════════
    // 15. ĐƠN THUỐC - Referral chặn tạo đơn qua service
    // ══════════════════════════════════════════════════════════════

    public function test_15_prescription_service_rejects_referral()
    {
        $admin = $this->createAdmin();
        $patient = $this->createPatient();
        $record = $this->createMedicalRecord($patient->id, $admin->id, 'referral');
        $record->referral_reason = 'Quá khả năng';
        $record->save();

        $service = app(PrescriptionService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('chuyển tuyến');

        $service->createPrescription([
            'medical_record_id' => $record->id,
            'items' => [[
                'item_type' => 'herb',
                'inventory_item_id' => null,
                'custom_name' => 'Test',
                'quantity_per_dose' => 0,
            ]],
        ], $admin->id);
    }

    // ══════════════════════════════════════════════════════════════
    // 16. FEFO - Xuất kho đúng thứ tự hạn gần nhất
    // ══════════════════════════════════════════════════════════════

    public function test_16_fefo_deducts_nearest_expiry_first()
    {
        $item = $this->createInventoryItem('herb', 'oral', 'FEFO test herb');
        $batchFar = $this->createBatch($item->id, 100, 'available', now()->addYear()->format('Y-m-d'));
        $batchNear = $this->createBatch($item->id, 50, 'available', now()->addMonth()->format('Y-m-d'));

        $service = app(InventoryService::class);
        $service->deductStockFefo($item->id, 30);

        $batchNear->refresh();
        $batchFar->refresh();

        // Near batch should be deducted first
        $this->assertEquals(20, $batchNear->quantity_remaining);
        $this->assertEquals(100, $batchFar->quantity_remaining);
    }

    // ══════════════════════════════════════════════════════════════
    // 17. FEFO - Thiếu tồn kho throw Exception
    // ══════════════════════════════════════════════════════════════

    public function test_17_fefo_insufficient_stock_throws_exception()
    {
        $item = $this->createInventoryItem('herb', 'oral', 'Thiếu tồn test');
        $this->createBatch($item->id, 5, 'available');

        $service = app(InventoryService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Không đủ tồn kho');

        $service->deductStockFefo($item->id, 100);
    }

    // ══════════════════════════════════════════════════════════════
    // 18. FEFO - Lô hết hạn không được chọn
    // ══════════════════════════════════════════════════════════════

    public function test_18_fefo_skips_expired_batches()
    {
        $item = $this->createInventoryItem('herb', 'oral');
        $this->createBatch($item->id, 100, 'expired', now()->subDays(5)->format('Y-m-d'));
        $validBatch = $this->createBatch($item->id, 50, 'available', now()->addMonth()->format('Y-m-d'));

        $service = app(InventoryService::class);
        $service->deductStockFefo($item->id, 30);

        $validBatch->refresh();
        $this->assertEquals(20, $validBatch->quantity_remaining);
    }

    // ══════════════════════════════════════════════════════════════
    // 19. FEFO - Lô bị khóa không được chọn
    // ══════════════════════════════════════════════════════════════

    public function test_19_fefo_skips_blocked_batches()
    {
        $item = $this->createInventoryItem('herb', 'oral');
        $this->createBatch($item->id, 100, 'blocked', now()->addMonth()->format('Y-m-d'));

        $service = app(InventoryService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Không đủ tồn kho');

        $service->deductStockFefo($item->id, 10);
    }

    // ══════════════════════════════════════════════════════════════
    // 20. CẤP THUỐC - Trạng thái đơn chuyển sang dispensed
    // ══════════════════════════════════════════════════════════════

    public function test_20_dispense_changes_status_to_dispensed()
    {
        $admin = $this->createAdmin();
        $patient = $this->createPatient();
        $record = $this->createMedicalRecord($patient->id, $admin->id, 'oral_only');

        $item = $this->createInventoryItem('herb', 'oral', 'Thuốc dispense test');
        $this->createBatch($item->id, 500, 'available', now()->addMonths(6)->format('Y-m-d'));

        // Create prescription via service
        $service = app(PrescriptionService::class);
        $prescription = $service->createPrescription([
            'medical_record_id' => $record->id,
            'num_of_doses' => 5,
            'items' => [[
                'item_type' => 'herb',
                'inventory_item_id' => $item->id,
                'quantity_per_dose' => 10,
                'unit' => 'g',
            ]],
        ], $admin->id);

        $this->assertEquals('confirmed', $prescription->status);

        // Dispense
        $service->dispensePrescription($prescription, $admin->id);
        $prescription->refresh();
        $this->assertEquals('dispensed', $prescription->status);

        // Stock should be deducted (10g * 5 doses = 50g)
        $this->assertDatabaseHas('stock_movements', [
            'movement_type' => 'dispense',
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    // 21. CẤP THUỐC - Không cấp khi đơn chưa confirmed
    // ══════════════════════════════════════════════════════════════

    public function test_21_cannot_dispense_non_confirmed_prescription()
    {
        $admin = $this->createAdmin();
        $patient = $this->createPatient();
        $record = $this->createMedicalRecord($patient->id, $admin->id, 'oral_only');

        $prescription = Prescription::create([
            'medical_record_id' => $record->id,
            'staff_id' => $admin->id,
            'status' => 'cancelled',
        ]);

        $service = app(PrescriptionService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('xác nhận');

        $service->dispensePrescription($prescription, $admin->id);
    }
}
