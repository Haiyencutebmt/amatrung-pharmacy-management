<?php

namespace Tests\Feature;

use App\Models\MedicalRecord;
use App\Models\MedicinalHerb;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\User;
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

        $response = $this->actingAs($admin)->post(route('admin.prescriptions.store'), [
            'medical_record_id' => $record->id,
            'treatment_type' => 'herbal_only',
            'note' => 'Thuốc sỏi thận',
            'num_of_doses' => 3,
            'usage_instruction' => 'Sắc ngày 1 thang.',
            'course_days' => 3,
            'items' => [
                [
                    'item_type' => 'formula_herb',
                    'herb_id' => $herb->id,
                    'custom_name' => 'Kim tiền thảo',
                    'quantity' => 10,
                    'unit' => 'g',
                    'dosage' => 'Sắc cùng thang thuốc',
                    'formula_group_id' => 'formula_test_1',
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
            'medicinal_herb_id' => $herb->id,
            'item_type' => 'formula_herb',
            'quantity' => 10,
            'unit' => 'g',
        ]);

        $this->assertSame(70.0, (float) $herb->fresh()->stock_quantity);
        $this->assertSame(1, PrescriptionItem::where('item_type', 'formula_herb')->count());
    }
}
