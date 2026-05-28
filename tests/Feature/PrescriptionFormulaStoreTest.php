<?php

namespace Tests\Feature;

use App\Models\MedicalRecord;
use App\Models\MedicinalHerb;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\User;
use App\Models\InventoryItem;
use App\Models\InventoryBatch;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrescriptionFormulaStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_formula_prescription_and_stock_is_deducted(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'patient_code' => 'BNFORM',
            'full_name' => 'Triệu Thị Phương Uyên',
            'phone' => '0325360086',
            'gender' => 'female',
        ]);

        $record = MedicalRecord::create([
            'patient_id' => $patient->id,
            'staff_id' => $admin->id,
            'visit_date' => '2026-05-22',
            'case_type' => MedicalRecord::CASE_NORMAL,
            'symptoms' => 'Đau đầu, sợ lạnh',
            'diagnosis' => 'Cảm mạo phong hàn',
        ]);

        $herb = MedicinalHerb::create([
            'name' => 'Kim tiền thảo',
            'category' => 'Dược liệu',
            'usage_type' => 'Sắc uống',
            'unit' => 'g',
            'stock_quantity' => 100,
            'expiry_date' => now()->addYear()->toDateString(),
            'status' => 'active',
        ]);

        // Seed new inventory tables
        $item = InventoryItem::create([
            'name' => 'Kim tiền thảo',
            'item_type' => 'herb',
            'usage_route' => 'oral',
            'unit' => 'g',
            'is_active' => true,
            'legacy_source_table' => 'medicinal_herbs',
            'legacy_source_id' => $herb->id,
        ]);

        $batch = InventoryBatch::create([
            'inventory_item_id' => $item->id,
            'batch_number' => 'BATCH-TEST',
            'expiry_date' => now()->addYear()->toDateString(),
            'quantity_remaining' => 100.0,
            'status' => 'available',
        ]);

        // 1. Post to store
        $response = $this->actingAs($admin)->post(route('admin.prescriptions.store'), [
            'medical_record_id' => $record->id,
            'treatment_type' => 'herbal_only',
            'note' => 'Thuốc sỏi thận',
            'num_of_doses' => 3,
            'usage_instruction' => 'Sắc ngày 1 thang.',
            'course_days' => 3,
            'items' => [
                [
                    'item_type' => 'herb',
                    'inventory_item_id' => $item->id,
                    'quantity_per_dose' => 10,
                    'unit' => 'g',
                    'dosage' => 'Sắc cùng thang thuốc',
                    'affects_stock' => '1',
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $prescription = Prescription::first();
        $this->assertNotNull($prescription);

        $this->assertDatabaseHas('prescription_items', [
            'prescription_id' => $prescription->id,
            'inventory_item_id' => $item->id,
            'item_type' => 'herb',
            'quantity' => 30, // 10 * 3
            'unit' => 'g',
        ]);

        // Before dispense, stock is untouched
        $this->assertSame(100.0, (float) $batch->fresh()->quantity_remaining);

        // 2. Dispense
        $this->actingAs($admin)->post(route('admin.prescriptions.dispense', $prescription))
            ->assertRedirect();

        $this->assertEquals('dispensed', $prescription->fresh()->status);
        $this->assertSame(70.0, (float) $batch->fresh()->quantity_remaining);
    }
}
