<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryBatch;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('manage_inventory', InventoryItem::class);

        $filter = $request->get('filter', 'all');
        $today = now()->startOfDay();
        $nearExpiryDate = $today->copy()->addMonthsNoOverflow(2);

        $query = InventoryItem::with('batches');

        $search = $request->get('search');
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        switch ($filter) {
            case 'available':
                $query->whereHas('batches', function ($q) use ($today) {
                    $q->where('status', 'available')
                      ->where('quantity_remaining', '>', 0)
                      ->whereNotNull('expiry_date')
                      ->whereDate('expiry_date', '>=', $today->toDateString());
                });
                break;
            case 'expired':
                $query->whereHas('batches', function ($q) {
                    $q->where('status', 'expired')->where('quantity_remaining', '>', 0);
                });
                break;
            case 'unknown_expiry':
                $query->whereHas('batches', function ($q) {
                    $q->where('status', 'unknown_expiry')->where('quantity_remaining', '>', 0);
                });
                break;
            case 'external_products':
                $query->where('usage_route', 'external');
                break;
            case 'near_expiry':
                $query->whereHas('batches', function ($q) use ($today, $nearExpiryDate) {
                    $q->where('status', 'available')
                      ->where('quantity_remaining', '>', 0)
                      ->whereNotNull('expiry_date')
                      ->whereBetween('expiry_date', [$today->toDateString(), $nearExpiryDate->toDateString()]);
                });
                break;
        }

        $items = $query->paginate(20);

        // Dynamically compute the total available and status for each item
        foreach ($items as $item) {
            $total = 0;
            $hasNearExpiry = false;
            $hasExpired = false;
            $hasUnknown = false;

            foreach ($item->batches as $batch) {
                if ($batch->quantity_remaining <= 0) continue;

                if (($batch->status === 'available' || $batch->status === 'near_expiry') && $batch->expiry_date) {
                    $expiryDate = $batch->expiry_date->copy()->startOfDay();
                    if ($expiryDate->betweenIncluded($today, $nearExpiryDate)) {
                        $hasNearExpiry = true;
                    }
                    $total += $batch->quantity_remaining;
                }
                
                if ($batch->status === 'expired' || ($batch->expiry_date && $batch->expiry_date < $today)) {
                    $hasExpired = true;
                }
                if ($batch->status === 'unknown_expiry' || !$batch->expiry_date) {
                    $hasUnknown = true;
                }
            }
            $item->total_quantity = $item->total_available_quantity;
            
            if ($hasExpired) {
                $item->computed_status = 'expired';
            } elseif ($hasUnknown) {
                $item->computed_status = 'unknown_expiry';
            } elseif ($hasNearExpiry) {
                $item->computed_status = 'near_expiry';
            } else {
                $item->computed_status = 'available';
            }
        }

        return view('admin.inventory.index', compact('items', 'filter'));
    }

    public function show($id)
    {
        $this->authorize('manage_inventory', InventoryItem::class);
        $item = InventoryItem::with(['batches' => function ($q) {
            $q->orderBy('expiry_date', 'asc')->orderBy('created_at', 'desc');
        }, 'batches.stockMovements' => function ($q) {
            $q->orderBy('created_at', 'desc')->take(10);
        }])->findOrFail($id);

        return view('admin.inventory.show', compact('item'));
    }

    public function bulkDestroy(Request $request)
    {
        $this->authorize('manage_inventory', InventoryItem::class);

        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:inventory_items,id',
        ], [
            'ids.required' => 'Vui lòng chọn ít nhất một mặt hàng để xóa.',
            'ids.min' => 'Vui lòng chọn ít nhất một mặt hàng để xóa.',
        ]);

        $items = InventoryItem::with('batches.stockMovements')
            ->whereIn('id', $validated['ids'])
            ->get();

        $usedPrescriptionItemIds = collect();
        if (Schema::hasTable('prescription_items') && Schema::hasColumn('prescription_items', 'inventory_item_id')) {
            $usedPrescriptionItemIds = DB::table('prescription_items')
                ->whereIn('inventory_item_id', $items->pluck('id'))
                ->whereNotNull('inventory_item_id')
                ->pluck('inventory_item_id')
                ->unique();
        }

        $deletedCount = 0;
        $skippedNames = [];

        DB::transaction(function () use ($items, $usedPrescriptionItemIds, &$deletedCount, &$skippedNames) {
            foreach ($items as $item) {
                $hasStockMovements = $item->batches->contains(function ($batch) {
                    return $batch->stockMovements->isNotEmpty();
                });
                $isUsedInPrescription = $usedPrescriptionItemIds->contains($item->id);

                if ($hasStockMovements || $isUsedInPrescription) {
                    $skippedNames[] = $item->name;
                    continue;
                }

                $item->delete();
                $deletedCount++;
            }
        });

        if ($deletedCount === 0) {
            return back()->with('error', 'Không thể xóa các mặt hàng đã có lịch sử lô, giao dịch kho hoặc đã dùng trong đơn điều trị.');
        }

        $message = "Đã xóa {$deletedCount} mặt hàng khỏi kho.";
        if (!empty($skippedNames)) {
            $message .= ' Không xóa các mục đã có lịch sử: ' . implode(', ', array_slice($skippedNames, 0, 5));
            if (count($skippedNames) > 5) {
                $message .= '...';
            }
        }

        return back()->with('success', $message);
    }

    public function updateBatch(Request $request, $id)
    {
        $this->authorize('manage_inventory', InventoryItem::class);
        
        $request->validate([
            'expiry_date' => 'required|date'
        ]);

        $batch = InventoryBatch::findOrFail($id);
        
        // Only allow updating unknown_expiry or near_expiry batches
        if (!in_array($batch->status, ['unknown_expiry', 'available', 'near_expiry', 'expired'])) {
            return back()->with('error', 'Không thể cập nhật hạn dùng cho lô hàng này.');
        }

        $expiryDate = \Carbon\Carbon::parse($request->expiry_date);
        $status = $expiryDate->isBefore(now()->startOfDay()) ? 'expired' : 'available';

        $batch->update([
            'expiry_date' => $expiryDate,
            'status' => $status
        ]);

        return back()->with('success', 'Đã cập nhật hạn dùng cho lô hàng.');
    }

    public function storeBatch(Request $request, $itemId)
    {
        $this->authorize('manage_inventory', InventoryItem::class);

        $request->validate([
            'batch_code' => 'required|string|max:50',
            'quantity' => 'required|numeric|min:0.01',
            'expiry_date' => 'required|date',
            'note' => 'nullable|string'
        ]);

        $item = InventoryItem::findOrFail($itemId);

        DB::transaction(function () use ($request, $item) {
            $expiryDate = \Carbon\Carbon::parse($request->expiry_date);
            $status = $expiryDate->isBefore(now()->startOfDay()) ? 'expired' : 'available';

            $batch = InventoryBatch::create([
                'inventory_item_id' => $item->id,
                'batch_number' => $request->batch_code,
                'expiry_date' => $expiryDate,
                'quantity_remaining' => $request->quantity,
                'status' => $status,
            ]);

            StockMovement::create([
                'inventory_batch_id' => $batch->id,
                'movement_type' => 'import',
                'quantity' => $request->quantity,
                'performed_by' => auth()->id(),
                'note' => $request->note ?: 'Nhập lô hàng mới',
            ]);
        });

        return back()->with('success', 'Đã nhập thêm lô hàng mới.');
    }

    public function toggleBatchStatus(Request $request, $id)
    {
        $this->authorize('manage_inventory', InventoryItem::class);
        $batch = InventoryBatch::findOrFail($id);
        
        if ($batch->status === 'blocked') {
            // Unblock -> check expiry
            if ($batch->expiry_date && $batch->expiry_date < now()->startOfDay()) {
                $batch->status = 'expired';
            } elseif (!$batch->expiry_date) {
                $batch->status = 'unknown_expiry';
            } else {
                $batch->status = 'available';
            }
        } else {
            $batch->status = 'blocked';
        }

        $batch->save();
        return back()->with('success', 'Đã thay đổi trạng thái lô hàng.');
    }

    public function storeItem(Request $request)
    {
        $this->authorize('manage_inventory', InventoryItem::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'item_type' => 'required|in:herb,packaged_product,prepared_product',
            'usage_route' => 'required|in:oral,external',
            'unit' => 'required|string|max:50',
            'description' => 'nullable|string',
            'warning' => 'nullable|string',
            'batch_code' => 'required|string|max:50',
            'quantity' => 'required|numeric|min:0.01',
            'expiry_date' => 'required|date',
        ]);

        DB::transaction(function () use ($request) {
            $item = InventoryItem::create([
                'name' => $request->name,
                'item_type' => $request->item_type,
                'usage_route' => $request->usage_route,
                'unit' => $request->unit,
                'description' => $request->description,
                'warning_note' => $request->warning,
                'is_active' => true,
            ]);

            $expiryDate = \Carbon\Carbon::parse($request->expiry_date);
            $status = $expiryDate->isBefore(now()->startOfDay()) ? 'expired' : 'available';

            $batch = InventoryBatch::create([
                'inventory_item_id' => $item->id,
                'batch_number' => $request->batch_code,
                'expiry_date' => $expiryDate,
                'quantity_remaining' => $request->quantity,
                'status' => $status,
            ]);

            StockMovement::create([
                'inventory_batch_id' => $batch->id,
                'movement_type' => 'import',
                'quantity' => $request->quantity,
                'performed_by' => auth()->id(),
                'note' => 'Tạo mặt hàng mới và nhập lô đầu tiên',
            ]);
        });

        return redirect()->route('admin.inventory.index')->with('success', 'Đã thêm mặt hàng mới thành công.');
    }
}
