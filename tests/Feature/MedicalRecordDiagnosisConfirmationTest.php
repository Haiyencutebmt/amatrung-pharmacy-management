<?php

namespace Tests\Feature;

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalRecordDiagnosisConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_can_confirm_diagnosis_and_detail_page_becomes_readonly(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $patient = $this->createPatient();
        $record = $this->createMedicalRecord($patient->id, $admin->id);

        $initial = $this->actingAs($admin)->get(route('admin.medical-records.show', $record));
        $initial->assertOk();
        $initial->assertSee('Xác nhận chẩn đoán');
        $this->assertStringNotContainsString('id="diagnosis_inline" readonly', $initial->getContent());
        $this->assertFalse($record->hasConfirmedDiagnosis());

        $response = $this->actingAs($admin)->put(route('admin.medical-records.update', $record), [
            'patient_id' => $patient->id,
            'visit_date' => '2026-06-04',
            'symptoms' => 'Mất ngủ kéo dài, mệt mỏi',
            'additional_symptoms' => 'Ăn kém, đau đầu nhẹ',
            'diagnosis' => 'Suy nhược cơ thể do thiếu ngủ kéo dài',
            'doctor_note' => "Lưu ý cho bác sĩ:\n- Hỏi thêm thuốc đang dùng.\n\nCảnh báo nếu có:\n- Chuyển khám nếu khó thở.",
            'case_type' => MedicalRecord::CASE_NORMAL,
            'treatment_direction' => 'oral_only',
            'confirm_diagnosis' => '1',
        ]);

        $response->assertRedirect(route('admin.medical-records.prescriptions.create', $record));
        $response->assertSessionHas('success');

        $record->refresh();
        $this->assertTrue($record->hasConfirmedDiagnosis());
        $this->assertNotNull($record->diagnosis_confirmed_at);
        $this->assertSame($admin->id, $record->diagnosis_confirmed_by);
        $this->assertSame('Suy nhược cơ thể do thiếu ngủ kéo dài', $record->diagnosis);
        $this->assertStringContainsString('Hỏi thêm thuốc đang dùng.', $record->doctor_note);

        $confirmed = $this->actingAs($admin)->get(route('admin.medical-records.show', $record));
        $confirmed->assertOk();
        $confirmed->assertSee('Đã xác nhận chẩn đoán');
        $confirmed->assertSee('Để chỉnh sửa, vui lòng sử dụng nút');
        $confirmed->assertDontSee('Xác nhận chẩn đoán');

        $html = $confirmed->getContent();
        $this->assertMatchesRegularExpression('/id="diagnosis_inline"[^>]*readonly/s', $html);
        $this->assertMatchesRegularExpression('/id="doctor_note_inline"[^>]*readonly/s', $html);
        $this->assertStringContainsString('id="treatment-action-section"', $html);
    }

    public function test_confirming_diagnosis_requires_diagnosis_text(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $patient = $this->createPatient();
        $record = $this->createMedicalRecord($patient->id, $admin->id);

        $response = $this->actingAs($admin)->from(route('admin.medical-records.show', $record))
            ->put(route('admin.medical-records.update', $record), [
                'patient_id' => $patient->id,
                'visit_date' => '2026-06-04',
                'symptoms' => 'Mất ngủ kéo dài',
                'diagnosis' => '',
                'case_type' => MedicalRecord::CASE_NORMAL,
                'treatment_direction' => 'oral_only',
                'confirm_diagnosis' => '1',
            ]);

        $response->assertRedirect(route('admin.medical-records.show', $record));
        $response->assertSessionHasErrors('diagnosis');

        $record->refresh();
        $this->assertFalse($record->hasConfirmedDiagnosis());
        $this->assertNull($record->diagnosis_confirmed_at);
    }

    private function createMedicalRecord(int $patientId, int $staffId): MedicalRecord
    {
        return MedicalRecord::create([
            'patient_id' => $patientId,
            'staff_id' => $staffId,
            'visit_date' => '2026-06-04',
            'case_type' => MedicalRecord::CASE_NORMAL,
            'symptoms' => 'Mất ngủ kéo dài',
            'diagnosis' => MedicalRecord::PENDING_DIAGNOSIS,
            'treatment_direction' => 'oral_only',
        ]);
    }

    private function createPatient(): Patient
    {
        return Patient::create([
            'patient_code' => Patient::generateCode(),
            'full_name' => 'Nguyễn Văn Test',
            'phone' => '0900000000',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male',
            'address' => 'TP.HCM',
        ]);
    }
}
