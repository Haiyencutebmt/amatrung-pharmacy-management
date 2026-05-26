<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PackagedProduct;
use Illuminate\Http\Request;

class PackagedProductController extends Controller
{
    /**
     * Danh sách sản phẩm hỗ trợ/Trà thảo mộc (trang riêng — vẫn giữ để tương thích route cũ)
     */
    public function index(Request $request)
    {
        return redirect()->route('admin.warehouse.index', ['tab' => 'products']);
    }

    /**
     * Form tạo thuốc dùng ngoài/Trà thảo mộc (redirect về warehouse modal)
     */
    public function create()
    {
        return redirect()->route('admin.warehouse.index', ['tab' => 'products']);
    }

    /**
     * Lưu thuốc dùng ngoài/Trà thảo mộc mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:200',
            'description'    => 'nullable|string',
            'category'       => 'nullable|string|max:100',
            'unit'           => 'required|string|max:30',
            'stock_quantity' => 'required|numeric|min:0',
            'expiry_date'    => 'nullable|date',
            'price'          => 'sometimes|nullable|numeric|min:0',
            'status'         => 'required|in:active,inactive',
        ], [
            'name.required'           => 'Tên sản phẩm là bắt buộc.',
            'unit.required'           => 'Đơn vị sản phẩm là bắt buộc.',
            'stock_quantity.required' => 'Số lượng tồn kho là bắt buộc.',
            'stock_quantity.min'      => 'Số lượng tồn kho không được âm.',
        ]);

        $validated['sku']   = PackagedProduct::generateSku();
        $validated['price'] = $validated['price'] ?? 0;
        if ((float) $validated['stock_quantity'] <= 0) {
            $validated['status'] = 'inactive';
        }

        PackagedProduct::create($validated);

        return redirect()->route('admin.warehouse.index', ['tab' => 'products'])
            ->with('success', "Đã thêm sản phẩm \"{$validated['name']}\" thành công.");
    }

    /**
     * Form chỉnh sửa (redirect về warehouse modal)
     */
    public function edit(PackagedProduct $packagedProduct)
    {
        return redirect()->route('admin.warehouse.index', ['tab' => 'products']);
    }

    /**
     * Cập nhật thuốc dùng ngoài/Trà thảo mộc
     */
    public function update(Request $request, PackagedProduct $packagedProduct)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:200',
            'description'    => 'nullable|string',
            'category'       => 'nullable|string|max:100',
            'unit'           => 'required|string|max:30',
            'stock_quantity' => 'required|numeric|min:0',
            'expiry_date'    => 'nullable|date',
            'price'          => 'sometimes|nullable|numeric|min:0',
            'status'         => 'required|in:active,inactive',
        ]);

        if ($request->has('price')) {
            $validated['price'] = $validated['price'] ?? 0;
        }
        if ((float) $validated['stock_quantity'] <= 0) {
            $validated['status'] = 'inactive';
        }

        $packagedProduct->update($validated);

        return redirect()->route('admin.warehouse.index', ['tab' => 'products'])
            ->with('success', "Đã cập nhật sản phẩm \"{$packagedProduct->name}\" thành công.");
    }

    /**
     * Xóa thuốc dùng ngoài/Trà thảo mộc
     */
    public function destroy(PackagedProduct $packagedProduct)
    {
        $name = $packagedProduct->name;
        $packagedProduct->delete();

        return redirect()->route('admin.warehouse.index', ['tab' => 'products'])
            ->with('success', "Đã xóa sản phẩm \"{$name}\".");
    }
}
