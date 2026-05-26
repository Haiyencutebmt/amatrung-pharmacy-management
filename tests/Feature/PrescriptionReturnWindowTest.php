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

class PrescriptionReturnWindowTest extends TestCase
{
    use RefreshDatabase;

    public function test_saving_prescription_deducts_stock(): void
    {
        [$admin, $record, $herb] = $this->makeBaseData();

        $this->actingAs($admin)->post(route('admin.prescriptions.store'), [
            'medical_record_id' => $record->id,
            'treatment_type' => 'herbal_only',
            'note' => 'Thuốc xông cảm',
            'num_of_doses' => 3,
            'items' => [
                [
                    'item_type' => 'formula_herb',
                    'herb_id' => $herb->id,
                    'quantity' => 10,
                    'unit' => 'g',
                    'dosage' => 'Sắc cùng thang',
                ],
            ],
        ])->assertRedirect();

        $this->assertSame(70.0, (float) $herb->fresh()->stock_quantity);
    }

    public function test_prescription_can_be_deleted_and_stock_refunded_within_24_hours(): void
    {
        [$admin, $record, $herb] = $this->makeBaseData();
        $prescription = $this->makeDeductedPrescription($record, $admin, $herb, now()->subHours(23));

        $this->actingAs($admin)
            ->from(route('admin.medical-records.show', $record))
            ->delete(route('admin.prescriptions.destroy', $prescription))
            ->assertRedirect(route('admin.medical-records.show', $record));

        $this->assertDatabaseHas('prescriptions', [
            'id' => $prescription->id,
            'status' => 'cancelled',
        ]);
        $this->assertSame(100.0, (float) $herb->fresh()->stock_quantity);
    }

    public function test_prescription_cannot_be_deleted_or_refunded_after_24_hours(): void
    {
        [$admin, $record, $herb] = $this->makeBaseData();
        $prescription = $this->makeDeductedPrescription($record, $admin, $herb, now()->subHours(25));

        $this->actingAs($admin)
            ->from(route('admin.medical-records.show', $record))
            ->delete(route('admin.prescriptions.destroy', $prescription))
            ->assertRedirect(route('admin.medical-records.show', $record))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('prescriptions', [
            'id' => $prescription->id,
            'status' => 'active',
        ]);
        $this->assertSame(70.0, (float) $herb->fresh()->stock_quantity);
    }

    public function test_internal_print_label_depends_on_case_type_and_items(): void
    {
        [$admin, $record, $herb] = $this->makeBaseData();
        $dispensingPrescription = $this->makeDeductedPrescription($record, $admin, $herb, now());

        $this->assertTrue($dispensingPrescription->isDispensingPrescription());
        $this->assertSame('Bốc thuốc', $dispensingPrescription->internalPrintLabel());

        $record->update(['case_type' => 'musculoskeletal']);

        $therapyPrescription = Prescription::create([
            'medical_record_id' => $record->id,
            'staff_id' => $admin->id,
            'treatment_type' => 'service_only',
            'note' => null,
            'num_of_doses' => 0,
            'status' => 'active',
            'affect_stock' => true,
        ]);

        PrescriptionItem::create([
            'prescription_id' => $therapyPrescription->id,
            'medicinal_herb_id' => null,
            'item_type' => 'therapy_service',
            'custom_name' => 'Nắn chỉnh khớp xương',
            'quantity' => 0,
            'unit' => 'buổi',
            'sessions' => 3,
            'affects_stock' => false,
        ]);

        $therapyPrescription = $therapyPrescription->fresh(['medicalRecord', 'items']);

        $this->assertFalse($therapyPrescription->isDispensingPrescription());
        $this->assertTrue($therapyPrescription->hasExternalTreatmentItems());
        $this->assertSame('Trị liệu', $therapyPrescription->internalPrintLabel());
    }

    public function test_patient_print_for_oral_herbs_shows_24_hour_return_warning(): void
    {
        $template = file_get_contents(resource_path('views/admin/prescriptions/partials/document.blade.php'));

        $this->assertStringContainsString('24 giờ (01 ngày)', $template);
        $this->assertStringContainsString('không tiếp nhận trả thuốc/hoàn kho', $template);
        $this->assertStringContainsString('không sử dụng', $template);
    }

    private function makeBaseData(): array
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'patient_code' => 'BNTEST',
            'full_name' => 'Nguyen Van A',
            'phone' => '0900000000',
            'gender' => 'male',
        ]);

        $record = MedicalRecord::create([
            'patient_id' => $patient->id,
            'staff_id' => $admin->id,
            'record_code' => 'BATEST',
            'visit_date' => now()->toDateString(),
            'symptoms' => 'Dau nhuc',
            'diagnosis' => 'Cam mao',
        ]);

        $herb = MedicinalHerb::create([
            'name' => 'Sa',
            'category' => 'Duoc lieu',
            'usage_type' => 'Sac uong',
            'unit' => 'g',
            'stock_quantity' => 100,
            'expiry_date' => now()->addYear()->toDateString(),
            'status' => 'active',
        ]);

        return [$admin, $record, $herb];
    }

    private function makeDeductedPrescription(MedicalRecord $record, User $admin, MedicinalHerb $herb, $createdAt): Prescription
    {
        $prescription = Prescription::create([
            'medical_record_id' => $record->id,
            'staff_id' => $admin->id,
            'treatment_type' => 'herbal_only',
            'note' => 'Thuoc xong cam',
            'num_of_doses' => 3,
            'status' => 'active',
            'affect_stock' => true,
        ]);

        $prescription->forceFill([
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ])->save();

        PrescriptionItem::create([
            'prescription_id' => $prescription->id,
            'medicinal_herb_id' => $herb->id,
            'item_type' => 'formula_herb',
            'quantity' => 10,
            'unit' => 'g',
            'dosage' => 'Sac uong',
            'affects_stock' => true,
        ]);

        $herb->stockLogType = 'prescription';
        $herb->stockLogNote = 'Kê đơn test';
        $herb->decrement('stock_quantity', 30);

        return $prescription->fresh();
    }

    public function test_cancelled_prescriptions_are_hidden_from_active_list_and_shown_in_history(): void
    {
        [$admin, $record, $herb] = $this->makeBaseData();
        
        // 1. Create an active prescription
        $activePrescription = $this->makeDeductedPrescription($record, $admin, $herb, now());
        
        // 2. Create a cancelled prescription
        $cancelledPrescription = Prescription::create([
            'medical_record_id' => $record->id,
            'staff_id' => $admin->id,
            'treatment_type' => 'herbal_only',
            'note' => 'Thuoc hoan tra',
            'num_of_doses' => 2,
            'status' => 'cancelled',
            'affect_stock' => false,
        ]);

        PrescriptionItem::create([
            'prescription_id' => $cancelledPrescription->id,
            'medicinal_herb_id' => $herb->id,
            'item_type' => 'formula_herb',
            'quantity' => 5,
            'unit' => 'g',
            'dosage' => 'Sac uong',
            'affects_stock' => false,
        ]);

        // 3. Access the medical record show page
        $response = $this->actingAs($admin)
            ->get(route('admin.medical-records.show', $record));
            
        $response->assertStatus(200);
        
        // 4. Assert active prescription is displayed with "Trả thuốc / Xóa"
        $response->assertSee('Đơn điều trị / Phác đồ #' . $activePrescription->id);
        $response->assertSee('Trả thuốc / Xóa');
        
        // 5. Assert cancelled prescription is displayed in history with "Đã hủy & Hoàn kho" badge
        $response->assertSee('Đơn điều trị / Phác đồ #' . $cancelledPrescription->id);
        $response->assertSee('Đã hủy & Hoàn kho', false);
        $response->assertSee('Lịch sử đơn thuốc & Hạng mục đã hủy / hoàn kho', false);
        
        // 6. Assert print view only contains active prescriptions
        $response->assertSee('II. PHÁC ĐỒ ĐIỀU TRỊ & CHỈ ĐỊNH DÙNG THUỐC', false);
    }
}
