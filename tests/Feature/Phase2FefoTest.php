<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\InventoryItem;
use App\Models\InventoryBatch;
use App\Models\StockMovement;
use App\Services\PrescriptionService;
use App\Services\InventoryService;
use Exception;

class Phase2FefoTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected $prescriptionService;

    protected $inventoryService;
    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->inventoryService = new InventoryService();
        $this->prescriptionService = new PrescriptionService($this->inventoryService);

        $this->adminUser = User::factory()->create();
    }

    private function createDummyData($direction = 'combined')
    {
        $patient = Patient::create(['full_name' => 'Test Patient', 'phone' => '0123456789', 'patient_code' => 'PT' . uniqid()]);
        $record = MedicalRecord::create([
            'patient_id' => $patient->id,
            'staff_id' => $this->adminUser->id,
            'visit_date' => now()->toDateString(),
            'symptoms' => 'Test',
            'diagnosis' => 'Test',
            'treatment_direction' => $direction,
            'status' => 'examining'
        ]);
        return $record;
    }

    private function createItemAndBatches($route = 'oral', $type = 'herb', $qty = 100, $status = 'available', $expiry = '+1 year')
    {
        $item = InventoryItem::create([
            'name' => 'Test Item ' . uniqid(),
            'item_type' => $type,
            'usage_route' => $route,
            'unit' => 'g',
            'is_active' => true
        ]);

        $batch = InventoryBatch::create([
            'inventory_item_id' => $item->id,
            'quantity_remaining' => $qty,
            'status' => $status,
            'expiry_date' => $expiry ? now()->modify($expiry) : null
        ]);

        return [$item, $batch];
    }

    public function test_draft_and_confirmed_do_not_deduct_stock()
    {
        $record = $this->createDummyData();
        list($item, $batch) = $this->createItemAndBatches();
        
        $data = [
            'medical_record_id' => $record->id,
            'items' => [
                ['inventory_item_id' => $item->id, 'quantity_per_dose' => 10, 'number_of_doses' => 1]
            ]
        ];

        $prescription = $this->prescriptionService->createPrescription($data, $this->adminUser->id);
        
        $this->assertEquals('confirmed', $prescription->status); // Initial is confirmed
        $this->assertEquals(100, $batch->fresh()->quantity_remaining); // Not deducted yet
    }

    public function test_dispensed_deducts_stock_and_creates_movements()
    {
        $record = $this->createDummyData();
        list($item, $batch) = $this->createItemAndBatches();
        
        $prescription = $this->prescriptionService->createPrescription([
            'medical_record_id' => $record->id,
            'items' => [['inventory_item_id' => $item->id, 'quantity_per_dose' => 10, 'number_of_doses' => 1]]
        ], $this->adminUser->id);

        $this->prescriptionService->dispensePrescription($prescription, $this->adminUser->id);
        
        $this->assertEquals('dispensed', $prescription->fresh()->status);
        $this->assertEquals(90, $batch->fresh()->quantity_remaining); // Deducted
        
        $movement = StockMovement::where('inventory_batch_id', $batch->id)->first();
        $this->assertNotNull($movement);
        $this->assertEquals('dispense', $movement->movement_type);
        $this->assertEquals(-10, $movement->quantity);
    }

    public function test_multiple_batches_used_if_first_insufficient()
    {
        $record = $this->createDummyData();
        $item = InventoryItem::create(['name' => 'MultiBatch', 'item_type' => 'herb', 'usage_route' => 'oral', 'unit' => 'g', 'is_active' => true]);
        
        $batch1 = InventoryBatch::create(['inventory_item_id' => $item->id, 'quantity_remaining' => 10, 'status' => 'available', 'expiry_date' => now()->modify('+1 month')]);
        $batch2 = InventoryBatch::create(['inventory_item_id' => $item->id, 'quantity_remaining' => 20, 'status' => 'available', 'expiry_date' => now()->modify('+2 months')]);

        $prescription = $this->prescriptionService->createPrescription([
            'medical_record_id' => $record->id,
            'items' => [['inventory_item_id' => $item->id, 'quantity_per_dose' => 25, 'number_of_doses' => 1]]
        ], $this->adminUser->id);

        $this->prescriptionService->dispensePrescription($prescription, $this->adminUser->id);

        $this->assertEquals(0, $batch1->fresh()->quantity_remaining);
        $this->assertEquals(5, $batch2->fresh()->quantity_remaining);
    }

    public function test_cannot_use_expired_batch()
    {
        $this->expectException(Exception::class);
        $record = $this->createDummyData();
        list($item, $batch) = $this->createItemAndBatches('oral', 'herb', 100, 'expired', '-1 day');

        $prescription = $this->prescriptionService->createPrescription([
            'medical_record_id' => $record->id,
            'items' => [['inventory_item_id' => $item->id, 'quantity_per_dose' => 10, 'number_of_doses' => 1]]
        ], $this->adminUser->id);

        $this->prescriptionService->dispensePrescription($prescription, $this->adminUser->id);
    }

    public function test_cannot_use_unknown_expiry_batch()
    {
        $this->expectException(Exception::class);
        $record = $this->createDummyData();
        list($item, $batch) = $this->createItemAndBatches('oral', 'herb', 100, 'unknown_expiry', null);

        $prescription = $this->prescriptionService->createPrescription([
            'medical_record_id' => $record->id,
            'items' => [['inventory_item_id' => $item->id, 'quantity_per_dose' => 10, 'number_of_doses' => 1]]
        ], $this->adminUser->id);

        $this->prescriptionService->dispensePrescription($prescription, $this->adminUser->id);
    }

    public function test_oral_only_blocks_external()
    {
        $this->expectException(Exception::class);
        $record = $this->createDummyData('oral_only');
        list($item, $batch) = $this->createItemAndBatches('external', 'external_product');

        $this->prescriptionService->createPrescription([
            'medical_record_id' => $record->id,
            'items' => [['inventory_item_id' => $item->id, 'quantity_per_dose' => 1]]
        ], $this->adminUser->id);
    }

    public function test_external_only_blocks_oral()
    {
        $this->expectException(Exception::class);
        $record = $this->createDummyData('external_only');
        list($item, $batch) = $this->createItemAndBatches('oral', 'herb');

        $this->prescriptionService->createPrescription([
            'medical_record_id' => $record->id,
            'items' => [['inventory_item_id' => $item->id, 'quantity_per_dose' => 1]]
        ], $this->adminUser->id);
    }

    public function test_combined_allows_oral_and_external_items()
    {
        $record = $this->createDummyData('combined');
        list($itemOral, $batchOral) = $this->createItemAndBatches('oral', 'herb');
        list($itemExternal, $batchExternal) = $this->createItemAndBatches('external', 'external_product');

        $prescription = $this->prescriptionService->createPrescription([
            'medical_record_id' => $record->id,
            'items' => [
                ['inventory_item_id' => $itemOral->id, 'quantity_per_dose' => 1],
                ['inventory_item_id' => $itemExternal->id, 'quantity_per_dose' => 1]
            ]
        ], $this->adminUser->id);

        $this->assertCount(2, $prescription->items);
    }

    public function test_referral_blocks_prescription()
    {
        $this->expectException(Exception::class);
        $record = $this->createDummyData('referral');
        
        $this->prescriptionService->createPrescription([
            'medical_record_id' => $record->id,
            'items' => []
        ], $this->adminUser->id);
    }

    public function test_decoction_calculates_total_qty()
    {
        $record = $this->createDummyData();
        list($item, $batch) = $this->createItemAndBatches('oral', 'herb');
        
        $prescription = $this->prescriptionService->createPrescription([
            'medical_record_id' => $record->id,
            'num_of_doses' => 10,
            'items' => [['inventory_item_id' => $item->id, 'quantity_per_dose' => 15]]
        ], $this->adminUser->id);

        $pItem = $prescription->items->first();
        $this->assertEquals(150, $pItem->quantity); // 15 * 10
    }

    public function test_packaged_does_not_multiply_qty()
    {
        $record = $this->createDummyData();
        list($item, $batch) = $this->createItemAndBatches('oral', 'prepared_product'); // Not herb
        
        $prescription = $this->prescriptionService->createPrescription([
            'medical_record_id' => $record->id,
            'num_of_doses' => 10,
            'items' => [['inventory_item_id' => $item->id, 'quantity_per_dose' => 2]]
        ], $this->adminUser->id);

        $pItem = $prescription->items->first();
        $this->assertEquals(2, $pItem->quantity); // 2, not 20
        $this->assertEquals(1, $pItem->number_of_doses);
    }

    public function test_max_one_prescription_per_record()
    {
        $record = $this->createDummyData();
        list($item, $batch) = $this->createItemAndBatches();
        
        $this->prescriptionService->createPrescription([
            'medical_record_id' => $record->id,
            'items' => [['inventory_item_id' => $item->id, 'quantity_per_dose' => 1]]
        ], $this->adminUser->id);

        $this->expectException(Exception::class);
        // Trying to create a second one should fail
        $this->prescriptionService->createPrescription([
            'medical_record_id' => $record->id,
            'items' => [['inventory_item_id' => $item->id, 'quantity_per_dose' => 1]]
        ], $this->adminUser->id);
    }
}
