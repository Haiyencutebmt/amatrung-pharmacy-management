<?php

namespace App\Services;

use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\MedicalRecord;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Exception;

class PrescriptionService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function createPrescription(array $data, int $staffId): Prescription
    {
        $record = MedicalRecord::findOrFail($data['medical_record_id']);
        $numDoses = (int) ($data['num_of_doses'] ?? 1);
        
        // 1. Logic hướng điều trị (treatment_direction)
        $direction = $record->treatment_direction;
        if ($direction === 'referral') {
            throw new Exception('Ca khám đã được chỉ định chuyển tuyến, không được phép kê đơn điều trị thông thường.');
        }

        return DB::transaction(function () use ($data, $numDoses, $staffId, $record, $direction) {
            
            // Tối đa 1 prescription chính cho 1 medical record (nếu chưa bị hủy)
            $existing = Prescription::where('medical_record_id', $record->id)
                ->where('status', '!=', 'cancelled')
                ->exists();
                
            if ($existing) {
                throw new Exception('Bệnh án này đã có một đơn thuốc chính. Hãy tạo bệnh án mới cho lần tái khám.');
            }

            $prescription = Prescription::create([
                'medical_record_id' => $record->id,
                'staff_id'          => $staffId,
                'treatment_type'    => $data['treatment_type'] ?? 'combined',
                'note'              => $data['note'] ?? null,
                'num_of_doses'      => $numDoses,
                'usage_instruction' => $data['usage_instruction'] ?? null,
                'course_days'       => $data['course_days'] ?? null,
                'follow_up_date'    => $data['follow_up_date'] ?? null,
                'public_instruction' => $data['public_instruction'] ?? null,
                'internal_note'     => $data['internal_note'] ?? null,
                'status'            => 'confirmed', // draft or confirmed initially. Confirmed by doctor.
            ]);

            foreach ($data['items'] as $item) {
                $inventoryItemId = !empty($item['inventory_item_id']) ? $item['inventory_item_id'] : null;
                $quantityPerDose = !empty($item['quantity_per_dose']) ? (float) $item['quantity_per_dose'] : 0;
                $customName = $item['custom_name'] ?? null;
                
                if ($inventoryItemId) {
                    $inventoryItem = InventoryItem::find($inventoryItemId);
                    if (!$inventoryItem) throw new Exception('Mặt hàng không tồn tại.');
                    
                    // Validate treatment direction
                    if ($direction === 'oral_only' && $inventoryItem->usage_route === 'external') {
                        throw new Exception("Hồ sơ chỉ định 'oral_only' nhưng mặt hàng '{$inventoryItem->name}' dùng ngoài.");
                    }
                    if ($direction === 'external_only' && $inventoryItem->usage_route === 'oral') {
                        throw new Exception("Hồ sơ chỉ định 'external_only' nhưng mặt hàng '{$inventoryItem->name}' đường uống.");
                    }

                    $totalQty = $quantityPerDose * $numDoses;
                    if ($inventoryItem->item_type !== 'herb') {
                        $totalQty = $quantityPerDose; // For packaged products, total is just quantity
                        $quantityPerDose = $totalQty;
                        $numDosesForItem = 1;
                    } else {
                        $numDosesForItem = $numDoses;
                    }
                    
                    PrescriptionItem::create([
                        'prescription_id'   => $prescription->id,
                        'inventory_item_id' => $inventoryItemId,
                        'item_type'         => $inventoryItem->item_type,
                        'quantity_per_dose' => $quantityPerDose,
                        'number_of_doses'   => $numDosesForItem,
                        'quantity'          => $totalQty, // Total required
                        'unit'              => $inventoryItem->unit,
                        'dosage'            => $item['dosage'] ?? null,
                        'note'              => $item['note'] ?? null,
                        'affects_stock'     => true,
                    ]);
                } else {
                    // Custom / Therapy service
                    PrescriptionItem::create([
                        'prescription_id'    => $prescription->id,
                        'inventory_item_id'  => null,
                        'item_type'          => $item['item_type'] ?? 'therapy_service',
                        'custom_name'        => $customName,
                        'quantity_per_dose'  => 0,
                        'number_of_doses'    => 0,
                        'quantity'           => 0,
                        'unit'               => $item['unit'] ?? 'lần',
                        'note'               => $item['note'] ?? null,
                        'affects_stock'      => false,
                    ]);
                }
            }

            return $prescription;
        });
    }

    public function dispensePrescription(Prescription $prescription, int $userId): bool
    {
        if ($prescription->status !== 'confirmed') {
            throw new Exception("Chỉ có thể xuất thuốc cho đơn đã xác nhận.");
        }

        DB::transaction(function () use ($prescription, $userId) {
            foreach ($prescription->items as $item) {
                if ($item->affects_stock && $item->inventory_item_id && $item->quantity > 0) {
                    $this->inventoryService->deductStockFefo($item->inventory_item_id, $item->quantity, $item->id, $userId);
                }
            }

            $prescription->update(['status' => 'dispensed']);
        });

        return true;
    }

    public function cancelPrescription(Prescription $prescription): bool
    {
        if ($prescription->status === 'dispensed') {
            // Không tự động nhập lại kho, phải làm thủ công hoặc qua process trả thuốc
            $prescription->update(['status' => 'cancelled']);
            return true;
        }

        $prescription->update(['status' => 'cancelled']);
        return true;
    }
}
