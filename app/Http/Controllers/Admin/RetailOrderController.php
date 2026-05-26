<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RetailOrder;
use App\Models\RetailOrderItem;
use App\Models\PackagedProduct;
use Illuminate\Support\Facades\DB;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RetailOrderController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:medicinal_herbs.view', only: ['index', 'show']),
            new Middleware('permission:medicinal_herbs.create', only: ['create', 'store']),
            new Middleware('permission:medicinal_herbs.delete', only: ['destroy']),
        ];
    }

    /**
     * Danh sách phiếu xuất kho
     */
    public function index(Request $request)
    {
        $query = RetailOrder::with(['staff', 'items.packagedProduct']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->orderByDesc('id')->paginate(20);

        $totalOrders     = RetailOrder::count();
        $ordersToday     = RetailOrder::whereDate('created_at', today())->count();
        $ordersThisMonth = RetailOrder::whereMonth('created_at', date('m'))
                                      ->whereYear('created_at', date('Y'))
                                      ->count();

        return view('admin.retail.index', compact('orders', 'totalOrders', 'ordersToday', 'ordersThisMonth'));
    }

    /**
     * Form tạo phiếu xuất kho
     */
    public function create()
    {
        $products = PackagedProduct::where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->where(function($q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>', now());
            })
            ->orderBy('name')
            ->get();

        return view('admin.retail.create', compact('products'));
    }

    /**
     * Lưu phiếu xuất kho + trừ tồn kho thuốc dùng ngoài/Trà thảo mộc
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'         => 'nullable|string|max:100',
            'customer_phone'        => 'nullable|string|max:20',
            'customer_address'      => 'nullable|string|max:255',
            'note'                  => 'nullable|string',
            'items'                 => 'required|array|min:1',
            'items.*.product_id'    => 'required|exists:packaged_products,id',
            'items.*.quantity'      => 'required|numeric|min:1',
            'items.*.note'          => 'nullable|string',
        ], [
            'items.required'           => 'Phiếu xuất phải có ít nhất một sản phẩm.',
            'items.*.product_id.required' => 'Vui lòng chọn sản phẩm.',
            'items.*.quantity.min'     => 'Số lượng phải ít nhất là 1.',
        ]);

        try {
            $order = DB::transaction(function () use ($validated) {
                $order = RetailOrder::create([
                    'order_code'       => RetailOrder::generateCode(),
                    'staff_id'         => auth()->id(),
                    'customer_name'    => $validated['customer_name'] ?: 'Khách lẻ',
                    'customer_phone'   => $validated['customer_phone'] ?? null,
                    'customer_address' => $validated['customer_address'] ?? null,
                    'note'             => $validated['note'] ?? null,
                ]);

                foreach ($validated['items'] as $item) {
                    $product = PackagedProduct::lockForUpdate()->findOrFail($item['product_id']);

                    // Kiểm tra trạng thái
                    if ($product->status !== 'active') {
                        throw new \Exception("Sản phẩm \"{$product->name}\" hiện không còn bán.");
                    }
                    if ($product->expiry_date && $product->expiry_date->isPast()) {
                        throw new \Exception("Sản phẩm \"{$product->name}\" đã hết hạn sử dụng.");
                    }

                    // Kiểm tra tồn kho
                    if ($item['quantity'] > $product->stock_quantity) {
                        throw new \Exception("Sản phẩm \"{$product->name}\" không đủ tồn kho (Yêu cầu: {$item['quantity']}, Hiện có: {$product->stock_quantity} {$product->unit}).");
                    }

                    RetailOrderItem::create([
                        'retail_order_id'    => $order->id,
                        'packaged_product_id' => $product->id,
                        'quantity'           => $item['quantity'],
                        'unit'               => $product->unit,
                        'unit_price'         => $product->price,
                        'note'               => $item['note'] ?? null,
                    ]);

                    // Trừ tồn kho
                    $product->decrement('stock_quantity', $item['quantity']);

                    if ($product->stock_quantity <= 0) {
                        $product->update(['status' => 'inactive']);
                    }
                }

                // Tính tổng tiền
                $total = collect($validated['items'])->sum(function ($item) use (&$validated) {
                    $p = PackagedProduct::find($item['product_id']);
                    return ($p->price ?? 0) * $item['quantity'];
                });
                $order->update(['total_amount' => $total]);

                return $order;
            });

            return redirect()->route('admin.retail-orders.show', $order)
                ->with('success', "Đã tạo phiếu xuất kho {$order->order_code} thành công.");

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Chi tiết phiếu xuất kho
     */
    public function show(RetailOrder $retailOrder)
    {
        $retailOrder->load(['staff', 'items.packagedProduct']);
        return view('admin.retail.show', compact('retailOrder'));
    }

    /**
     * Xoá phiếu + hoàn kho thuốc dùng ngoài/Trà thảo mộc
     */
    public function destroy(RetailOrder $retailOrder)
    {
        DB::transaction(function () use ($retailOrder) {
            foreach ($retailOrder->items as $item) {
                $product = $item->packagedProduct;
                if ($product) {
                    $product->increment('stock_quantity', $item->quantity);
                    // Kích hoạt lại nếu đang inactive do hết hàng
                    if ($product->status === 'inactive' && $product->stock_quantity > 0) {
                        $product->update(['status' => 'active']);
                    }
                }
            }
            $retailOrder->delete();
        });

        return redirect()->route('admin.retail-orders.index')
            ->with('success', 'Đã xóa phiếu xuất kho và hoàn lại tồn kho.');
    }

    /**
     * API: Lấy thông tin thuốc dùng ngoài/Trà thảo mộc cho AJAX
     */
    public function herbInfo(Request $request)
    {
        $product = PackagedProduct::find($request->product_id);
        if (!$product) {
            return response()->json(['error' => 'Không tìm thấy sản phẩm'], 404);
        }
        return response()->json([
            'id'             => $product->id,
            'name'           => $product->name,
            'sku'            => $product->sku,
            'unit'           => $product->unit,
            'stock_quantity' => floatval($product->stock_quantity),
            'price'          => floatval($product->price),
            'status'         => $product->status,
        ]);
    }
}
