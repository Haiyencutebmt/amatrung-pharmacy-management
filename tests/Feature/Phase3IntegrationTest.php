<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\InventoryItem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Database\Seeders\RoleAndPermissionSeeder;

class Phase3IntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function createPractitioner()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        return $user;
    }

    private function createPatient()
    {
        return Patient::create([
            'patient_code' => 'PT-1234' . rand(100,999),
            'full_name' => 'Nguyễn Văn Test',
            'phone' => '0987654321',
            'gender' => 'male',
            'date_of_birth' => '1990-01-01',
        ]);
    }



    public function test_medical_record_treatment_direction_oral_only_cannot_prescribe_external()
    {
        $practitioner = $this->createPractitioner();
        $patient = $this->createPatient();

        $record = MedicalRecord::create([
            'patient_id' => $patient->id,
            'staff_id' => $practitioner->id,
            'visit_date' => now()->format('Y-m-d'),
            'case_type' => 'normal',
            'symptoms' => 'Test',
            'diagnosis' => 'Test',
            'treatment_direction' => 'oral_only',
        ]);

        $extItem = InventoryItem::create([
            'item_code' => 'EXT-123',
            'name' => 'Cồn xoa bóp Test',
            'item_type' => 'packaged_product',
            'usage_route' => 'external',
            'unit' => 'Hộp',
            'stock_quantity' => 10,
        ]);

        // Try to prescribe an external product
        $response = $this->actingAs($practitioner)->post(route('admin.prescriptions.store'), [
            'medical_record_id' => $record->id,
            'num_of_doses' => 1,
            'items' => [
                [
                    'item_type' => 'packaged_product',
                    'inventory_item_id' => $extItem->id,
                    'quantity_per_dose' => 1,
                    'unit' => 'Hộp'
                ]
            ]
        ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('nhưng mặt hàng', session('error'));
    }

    public function test_medical_record_treatment_direction_external_only_cannot_prescribe_oral()
    {
        $practitioner = $this->createPractitioner();
        $patient = $this->createPatient();

        $record = MedicalRecord::create([
            'patient_id' => $patient->id,
            'staff_id' => $practitioner->id,
            'visit_date' => now()->format('Y-m-d'),
            'case_type' => 'musculoskeletal',
            'symptoms' => 'Test',
            'diagnosis' => 'Test',
            'treatment_direction' => 'external_only',
        ]);

        $herb = InventoryItem::create([
            'item_code' => 'HRB-123',
            'name' => 'Cam thảo Test',
            'item_type' => 'herb',
            'usage_route' => 'oral',
            'unit' => 'g',
            'stock_quantity' => 1000,
        ]);

        // Try to prescribe an oral herb
        $response = $this->actingAs($practitioner)->post(route('admin.prescriptions.store'), [
            'medical_record_id' => $record->id,
            'num_of_doses' => 1,
            'items' => [
                [
                    'item_type' => 'herb',
                    'inventory_item_id' => $herb->id,
                    'quantity_per_dose' => 10,
                    'unit' => 'g'
                ]
            ]
        ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('nhưng mặt hàng', session('error'));
    }
}
