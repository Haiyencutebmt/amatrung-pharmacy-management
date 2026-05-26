@extends('layouts.admin')

@section('title', 'Sửa thuốc dùng ngoài/Trà thảo mộc — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div style="width: 44px; height: 44px; background: #eff6ff; color: #3b82f6; border-radius: 0.375rem; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; border: 1px solid #dbeafe;">
        ✏️
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Sửa thuốc dùng ngoài/Trà thảo mộc</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">{{ $packagedProduct->sku }} — {{ $packagedProduct->name }}</p>
    </div>
</div>
@endsection

@section('content')
<div style="font-family: 'Inter', system-ui, sans-serif; max-width: 800px; margin-top: -1rem;">

    <a href="{{ route('admin.packaged-products.index') }}" 
       style="display: inline-flex; align-items: center; gap: 0.5rem; color: #64748b; text-decoration: none; font-size: 0.875rem; font-weight: 600; margin-bottom: 1.5rem; padding: 0.5rem 0.85rem; background: #fff; border: 1px solid #e2e8f0; border-radius: 0.375rem; transition: all 0.2s;"
       onmouseover="this.style.borderColor='#cbd5e1'" onmouseout="this.style.borderColor='#e2e8f0'">
        ← Quay lại danh sách
    </a>

    <form method="POST" action="{{ route('admin.packaged-products.update', $packagedProduct) }}">
        @csrf
        @method('PUT')

        {{-- Thông tin sản phẩm --}}
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 0.375rem; padding: 1.75rem; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #f1f5f9;">
                <div style="width: 34px; height: 34px; background: #f0fdf4; border-radius: 0.375rem; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; border: 1px solid #dcfce7;">📋</div>
                <h2 style="margin: 0; font-size: 1rem; font-weight: 700; color: #1e3a5f;">Thông tin sản phẩm</h2>
                <span style="margin-left: auto; font-family: monospace; font-size: 0.8rem; background: #f1f5f9; color: #475569; padding: 0.2rem 0.6rem; border-radius: 0.25rem; font-weight: 700;">{{ $packagedProduct->sku }}</span>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                <div style="grid-column: span 2;">
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 0.4rem;">Tên sản phẩm <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $packagedProduct->name) }}"
                           style="width: 100%; padding: 0.65rem 0.9rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.9rem; color: #1e293b; outline: none; box-sizing: border-box; transition: border-color 0.2s;"
                           onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
                    @error('name')<p style="color: #ef4444; font-size: 0.78rem; margin: 0.3rem 0 0;">{{ $message }}</p>@enderror
                </div>

                <div style="grid-column: span 2;">
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 0.4rem;">Mô tả / Thành phần</label>
                    <textarea name="description" rows="3"
                              style="width: 100%; padding: 0.65rem 0.9rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.9rem; color: #1e293b; outline: none; box-sizing: border-box; resize: vertical; transition: border-color 0.2s;"
                              onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">{{ old('description', $packagedProduct->description) }}</textarea>
                </div>

                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 0.4rem;">Trạng thái</label>
                    <select name="status" style="width: 100%; padding: 0.65rem 0.9rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.9rem; color: #1e293b; outline: none; box-sizing: border-box;">
                        <option value="active" {{ old('status', $packagedProduct->status) === 'active' ? 'selected' : '' }}>✅ Đang bán</option>
                        <option value="inactive" {{ old('status', $packagedProduct->status) === 'inactive' ? 'selected' : '' }}>⏸ Ngừng bán</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 0.4rem;">Giá bán (đ)</label>
                    <input type="number" name="price" value="{{ old('price', $packagedProduct->price) }}" min="0" step="1000"
                           style="width: 100%; padding: 0.65rem 0.9rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.9rem; color: #1e293b; outline: none; box-sizing: border-box; transition: border-color 0.2s;"
                           onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
            </div>
        </div>

        {{-- Dược liệu nguồn --}}
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 0.375rem; padding: 1.75rem; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #f1f5f9;">
                <div style="width: 34px; height: 34px; background: #f0fdf4; border-radius: 0.375rem; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; border: 1px solid #dcfce7;">🌿</div>
                <h2 style="margin: 0; font-size: 1rem; font-weight: 700; color: #1e3a5f;">Dược liệu nguồn & Quy cách</h2>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem;">
                <div style="grid-column: span 3;">
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 0.4rem;">Dược liệu nguồn <span style="color: #ef4444;">*</span></label>
                    <select name="medicinal_herb_id" style="width: 100%; padding: 0.65rem 0.9rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.9rem; color: #1e293b; outline: none; box-sizing: border-box;">
                        <option value="">— Chọn dược liệu —</option>
                        @foreach($herbs as $herb)
                            <option value="{{ $herb->id }}" {{ old('medicinal_herb_id', $packagedProduct->medicinal_herb_id) == $herb->id ? 'selected' : '' }}>
                                {{ $herb->name }} (Tồn: {{ number_format($herb->stock_quantity, 1) }} {{ $herb->unit }})
                            </option>
                        @endforeach
                    </select>
                    @error('medicinal_herb_id')<p style="color: #ef4444; font-size: 0.78rem; margin: 0.3rem 0 0;">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 0.4rem;">Lượng dược liệu / 1 đơn vị SP <span style="color: #ef4444;">*</span></label>
                    <input type="number" name="herb_quantity_per_unit" value="{{ old('herb_quantity_per_unit', $packagedProduct->herb_quantity_per_unit) }}" min="0.01" step="0.01"
                           style="width: 100%; padding: 0.65rem 0.9rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.9rem; color: #1e293b; outline: none; box-sizing: border-box; transition: border-color 0.2s;"
                           onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
                    @error('herb_quantity_per_unit')<p style="color: #ef4444; font-size: 0.78rem; margin: 0.3rem 0 0;">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 0.4rem;">Đơn vị dược liệu <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="herb_unit" value="{{ old('herb_unit', $packagedProduct->herb_unit) }}"
                           style="width: 100%; padding: 0.65rem 0.9rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.9rem; color: #1e293b; outline: none; box-sizing: border-box; transition: border-color 0.2s;"
                           onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
                </div>

                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 0.4rem;">Đơn vị sản phẩm <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="unit" value="{{ old('unit', $packagedProduct->unit) }}"
                           style="width: 100%; padding: 0.65rem 0.9rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.9rem; color: #1e293b; outline: none; box-sizing: border-box; transition: border-color 0.2s;"
                           onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
                </div>

                <div style="grid-column: span 3;">
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 0.4rem;">Số lượng tồn kho <span style="color: #ef4444;">*</span></label>
                    <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $packagedProduct->stock_quantity) }}" min="0" step="1"
                           style="width: 100%; padding: 0.65rem 0.9rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.9rem; color: #1e293b; outline: none; box-sizing: border-box; transition: border-color 0.2s; max-width: 200px;"
                           onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
            </div>
        </div>

        {{-- Nút submit --}}
        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
            <a href="{{ route('admin.packaged-products.index') }}"
               style="display: inline-flex; align-items: center; padding: 0.65rem 1.5rem; background: #fff; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.9rem; font-weight: 700; color: #475569; text-decoration: none; transition: all 0.2s;"
               onmouseover="this.style.borderColor='#cbd5e1'" onmouseout="this.style.borderColor='#e2e8f0'">Hủy bỏ</a>
            <button type="submit" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.5rem; background: #3b82f6; border: none; border-radius: 0.375rem; font-size: 0.9rem; font-weight: 700; color: #fff; cursor: pointer; transition: all 0.2s;"
                    onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">
                💾 Lưu thay đổi
            </button>
        </div>
    </form>
</div>
@endsection
