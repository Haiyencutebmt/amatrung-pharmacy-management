@extends('layouts.admin')

@section('title', 'Thuốc dùng ngoài/Trà thảo mộc — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div style="width: 44px; height: 44px; background: #f0fdf4; color: #16a34a; border-radius: 0.375rem; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; border: 1px solid #dcfce7;">
        📦
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Thuốc dùng ngoài/Trà thảo mộc</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Quản lý thuốc dùng ngoài/Trà thảo mộc từ kho dược liệu</p>
    </div>
</div>
@endsection

@section('content')
<div style="font-family: 'Inter', system-ui, sans-serif;">

    {{-- Thống kê --}}
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 0.375rem; padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 1rem;">
            <div style="width: 48px; height: 48px; border-radius: 0.375rem; background: #f0fdf4; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; border: 1px solid #dcfce7;">📦</div>
            <div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a;">{{ $totalProducts }}</div>
                <div style="font-size: 0.8rem; color: #64748b; font-weight: 500;">Tổng sản phẩm</div>
            </div>
        </div>
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 0.375rem; padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 1rem;">
            <div style="width: 48px; height: 48px; border-radius: 0.375rem; background: #eff6ff; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; border: 1px solid #dbeafe;">✅</div>
            <div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a;">{{ $activeProducts }}</div>
                <div style="font-size: 0.8rem; color: #64748b; font-weight: 500;">Đang bán</div>
            </div>
        </div>
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 0.375rem; padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 1rem;">
            <div style="width: 48px; height: 48px; border-radius: 0.375rem; background: #fef2f2; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; border: 1px solid #fecaca;">⚠️</div>
            <div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a;">{{ $lowStock }}</div>
                <div style="font-size: 0.8rem; color: #64748b; font-weight: 500;">Sắp hết hàng (≤ 10)</div>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; gap: 1rem; flex-wrap: wrap;">
        <form method="GET" style="display: flex; gap: 0.75rem; flex: 1; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 0.5rem; background: #fff; border: 1px solid #e2e8f0; border-radius: 0.375rem; padding: 0.5rem 0.85rem; flex: 1; max-width: 360px;">
                <span style="color: #94a3b8;">🔍</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên, mã SKU..." 
                       style="border: none; outline: none; font-size: 0.875rem; width: 100%; color: #1e293b;">
            </div>
            <select name="status" onchange="this.form.submit()" style="border: 1px solid #e2e8f0; border-radius: 0.375rem; padding: 0.5rem 0.85rem; font-size: 0.875rem; background: #fff; color: #475569; cursor: pointer;">
                <option value="">Tất cả trạng thái</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang bán</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Ngừng bán</option>
            </select>
            <button type="submit" style="background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 600; color: #475569; cursor: pointer;">Lọc</button>
        </form>

        <a href="{{ route('admin.packaged-products.create') }}" 
           style="display: inline-flex; align-items: center; gap: 0.5rem; background: #16a34a; color: #fff; border: none; padding: 0.65rem 1.25rem; border-radius: 0.375rem; font-weight: 700; font-size: 0.875rem; text-decoration: none; transition: all 0.2s; white-space: nowrap;">
            + Thêm sản phẩm
        </a>
    </div>

    {{-- Bảng sản phẩm --}}
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 0.375rem; overflow: hidden;">
        @if($products->isEmpty())
            <div style="padding: 4rem 2rem; text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 1rem; color: #cbd5e1;">📦</div>
                <h3 style="font-size: 1.1rem; font-weight: 700; color: #1e3a5f; margin: 0 0 0.5rem;">Chưa có thuốc dùng ngoài/Trà thảo mộc</h3>
                <p style="color: #64748b; font-size: 0.9rem; margin: 0 0 1.5rem;">Hãy tạo thuốc dùng ngoài/Trà thảo mộc đầu tiên từ kho dược liệu.</p>
                <a href="{{ route('admin.packaged-products.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #16a34a; color: #fff; padding: 0.65rem 1.25rem; border-radius: 0.375rem; font-weight: 700; font-size: 0.875rem; text-decoration: none;">
                    + Thêm sản phẩm đầu tiên
                </a>
            </div>
        @else
            <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <th style="padding: 0.85rem 1rem; text-align: left; font-weight: 700; color: #475569; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">SKU</th>
                        <th style="padding: 0.85rem 1rem; text-align: left; font-weight: 700; color: #475569; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Tên sản phẩm</th>
                        <th style="padding: 0.85rem 1rem; text-align: left; font-weight: 700; color: #475569; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Dược liệu nguồn</th>
                        <th style="padding: 0.85rem 1rem; text-align: center; font-weight: 700; color: #475569; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Lượng / đơn vị</th>
                        <th style="padding: 0.85rem 1rem; text-align: center; font-weight: 700; color: #475569; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Tồn kho</th>
                        <th style="padding: 0.85rem 1rem; text-align: right; font-weight: 700; color: #475569; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Giá bán</th>
                        <th style="padding: 0.85rem 1rem; text-align: center; font-weight: 700; color: #475569; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Trạng thái</th>
                        <th style="padding: 0.85rem 1rem; text-align: center; font-weight: 700; color: #475569; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                        <td style="padding: 0.9rem 1rem;">
                            <span style="font-family: monospace; font-size: 0.8rem; background: #f1f5f9; color: #475569; padding: 0.2rem 0.5rem; border-radius: 0.25rem; font-weight: 700;">{{ $product->sku }}</span>
                        </td>
                        <td style="padding: 0.9rem 1rem;">
                            <div style="font-weight: 700; color: #1e3a5f;">{{ $product->name }}</div>
                            @if($product->description)
                                <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.15rem;">{{ Str::limit($product->description, 60) }}</div>
                            @endif
                        </td>
                        <td style="padding: 0.9rem 1rem;">
                            <div style="font-weight: 600; color: #166534;">🌿 {{ $product->medicinalHerb->name ?? '—' }}</div>
                        </td>
                        <td style="padding: 0.9rem 1rem; text-align: center; color: #475569; font-weight: 600;">
                            {{ number_format($product->herb_quantity_per_unit, 2) }} {{ $product->herb_unit }} / {{ $product->unit }}
                        </td>
                        <td style="padding: 0.9rem 1rem; text-align: center;">
                            @if($product->stock_quantity <= 0)
                                <span style="background: #fef2f2; color: #dc2626; padding: 0.2rem 0.6rem; border-radius: 0.25rem; font-size: 0.78rem; font-weight: 700; border: 1px solid #fecaca;">Hết hàng</span>
                            @elseif($product->stock_quantity <= 10)
                                <span style="background: #fef3c7; color: #d97706; padding: 0.2rem 0.6rem; border-radius: 0.25rem; font-size: 0.78rem; font-weight: 700; border: 1px solid #fde68a;">{{ number_format($product->stock_quantity, 0) }} {{ $product->unit }}</span>
                            @else
                                <span style="background: #f0fdf4; color: #16a34a; padding: 0.2rem 0.6rem; border-radius: 0.25rem; font-size: 0.78rem; font-weight: 700; border: 1px solid #dcfce7;">{{ number_format($product->stock_quantity, 0) }} {{ $product->unit }}</span>
                            @endif
                        </td>
                        <td style="padding: 0.9rem 1rem; text-align: right; font-weight: 700; color: #1e3a5f;">
                            {{ $product->price > 0 ? number_format($product->price, 0, ',', '.') . ' đ' : '—' }}
                        </td>
                        <td style="padding: 0.9rem 1rem; text-align: center;">
                            @if($product->status === 'active')
                                <span style="background: #f0fdf4; color: #16a34a; padding: 0.25rem 0.65rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 700; border: 1px solid #dcfce7;">✅ Đang bán</span>
                            @else
                                <span style="background: #f8fafc; color: #94a3b8; padding: 0.25rem 0.65rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 700; border: 1px solid #e2e8f0;">⏸ Ngừng bán</span>
                            @endif
                        </td>
                        <td style="padding: 0.9rem 1rem; text-align: center;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                                <a href="{{ route('admin.packaged-products.edit', $product) }}" 
                                   style="display: inline-flex; align-items: center; gap: 0.3rem; background: #eff6ff; color: #3b82f6; border: 1px solid #dbeafe; padding: 0.35rem 0.75rem; border-radius: 0.25rem; font-size: 0.78rem; font-weight: 700; text-decoration: none; transition: all 0.15s;"
                                   onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                                    ✏️ Sửa
                                </a>
                                <form method="POST" action="{{ route('admin.packaged-products.destroy', $product) }}" onsubmit="return confirm('Xóa sản phẩm \'{{ addslashes($product->name) }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="display: inline-flex; align-items: center; gap: 0.3rem; background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; padding: 0.35rem 0.75rem; border-radius: 0.25rem; font-size: 0.78rem; font-weight: 700; cursor: pointer; transition: all 0.15s;"
                                            onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fef2f2'">
                                        🗑 Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Phân trang --}}
            @if($products->hasPages())
            <div style="padding: 1rem 1.5rem; border-top: 1px solid #f1f5f9;">
                {{ $products->withQueryString()->links() }}
            </div>
            @endif
        @endif
    </div>
</div>
@endsection
