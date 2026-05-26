<?php

namespace Tests\Feature;

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalRecordCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_medical_record_with_normal_case_type(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $patient = $this->createPatient();

        $response = $this->actingAs($admin)->post(route('admin.medical-records.store'), [
            'patient_id' => $patient->id,
            'from_patient' => '1',
            'visit_date' => '2026-05-22',
            'case_type' => 'normal',
            'symptoms' => 'Ho, sợ lạnh, đau đầu',
            'diagnosis' => 'Cảm mạo phong hàn',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('medical_records', [
            'patient_id' => $patient->id,
            'case_type' => MedicalRecord::CASE_NORMAL,
            'symptoms' => 'Ho, sợ lạnh, đau đầu',
            'diagnosis' => 'Cảm mạo phong hàn',
        ]);
    }

    public function test_legacy_modal_case_type_values_are_normalized(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $patient = $this->createPatient();

        $this->actingAs($admin)->post(route('admin.medical-records.store'), [
            'patient_id' => $patient->id,
            'visit_date' => '2026-05-22',
            'case_type' => 'general',
            'symptoms' => 'Mệt mỏi, ăn kém',
            'diagnosis' => 'Tỳ vị hư nhược',
        ])->assertSessionHasNoErrors();

        $this->actingAs($admin)->post(route('admin.medical-records.store'), [
            'patient_id' => $patient->id,
            'visit_date' => '2026-05-22',
            'case_type' => 'both',
            'symptoms' => 'Đau lưng kèm suy nhược',
            'diagnosis' => 'Đau lưng kèm khí huyết hư',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('medical_records', [
            'patient_id' => $patient->id,
            'case_type' => MedicalRecord::CASE_NORMAL,
            'diagnosis' => 'Tỳ vị hư nhược',
        ]);

        $this->assertDatabaseHas('medical_records', [
            'patient_id' => $patient->id,
            'case_type' => MedicalRecord::CASE_COMBINED,
            'diagnosis' => 'Đau lưng kèm khí huyết hư',
        ]);
    }

    private function createPatient(): Patient
    {
        return Patient::create([
            'patient_code' => Patient::generateCode(),
            'full_name' => 'Triệu Thị Phương Uyên',
            'phone' => '0325360086',
            'date_of_birth' => '2004-01-01',
            'gender' => 'female',
            'address' => 'Cư Mgar',
        ]);
    }
}
