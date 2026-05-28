<?php

namespace App\Services;

use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    /**
     * Deduct stock using FEFO (First-Expired, First-Out) principle.
     * 
     * @param int $itemId
     * @param float $quantityToDeduct
     * @param int|null $prescriptionItemId
     * @param int|null $userId
     * @return bool
     * @throws Exception
     */
    public function deductStockFefo(int $itemId, float $quantityToDeduct, ?int $prescriptionItemId = null, ?int $userId = null): bool
    {
        if ($quantityToDeduct <= 0) {
            return true; // Nothing to deduct
        }

        DB::transaction(function () use ($itemId, $quantityToDeduct, $prescriptionItemId, $userId) {
            // Get available batches, not expired, ordered by nearest expiry date, with lockForUpdate
            $batches = InventoryBatch::where('inventory_item_id', $itemId)
                ->where('status', 'available')
                ->where('quantity_remaining', '>', 0)
                ->where(function($query) {
                    $query->whereNull('expiry_date')
                          ->orWhereDate('expiry_date', '>=', now()->toDateString());
                })
                ->orderBy('expiry_date', 'asc')
                ->orderBy('id', 'asc') // tie breaker
                ->lockForUpdate()
                ->get();

            $totalAvailable = $batches->sum('quantity_remaining');

            if ($totalAvailable < $quantityToDeduct) {
                $item = InventoryItem::find($itemId);
                throw new Exception("Không đủ tồn kho hợp lệ cho mặt hàng '{$item->name}'. Yêu cầu: {$quantityToDeduct}, Hiện có hợp lệ: {$totalAvailable}.");
            }

            $remainingToDeduct = $quantityToDeduct;

            foreach ($batches as $batch) {
                if ($remainingToDeduct <= 0) {
                    break;
                }

                $deductFromBatch = min($batch->quantity_remaining, $remainingToDeduct);
                
                // Update batch quantity
                $batch->quantity_remaining -= $deductFromBatch;
                $batch->save();

                // Record stock movement
                StockMovement::create([
                    'inventory_batch_id' => $batch->id,
                    'prescription_item_id' => $prescriptionItemId,
                    'performed_by' => $userId,
                    'movement_type' => 'dispense',
                    'quantity' => -$deductFromBatch,
                    'note' => 'Xuất kho theo đơn thuốc (FEFO)',
                ]);

                $remainingToDeduct -= $deductFromBatch;
            }

            if ($remainingToDeduct > 0) {
                throw new Exception("Lỗi đồng bộ tồn kho FEFO, không thể trừ đủ số lượng.");
            }
        });

        return true;
    }
}
