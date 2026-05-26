@extends('layouts.admin')

@section('title', 'Sửa Dược Liệu — AmaTrung')
@section('page-title', 'Chỉnh Sửa: ' . $medicinalHerb->name)

@section('content')
<div class="card" style="max-width: 800px;">
    <form action="{{ route('admin.medicinal-herbs.update', $medicinalHerb) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="name" class="form-label">Tên dược liệu/chế phẩm *</label>
                <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $medicinalHerb->name) }}" required>
            </div>

            <div class="form-group">
                <label for="category" class="form-label">Phân loại/Nhóm</label>
                <input type="text" name="category" id="category" class="form-input" value="{{ old('category', $medicinalHerb->category) }}">
            </div>

            <div class="form-group">
                <label for="usage_type" class="form-label">Cách dùng</label>
                <select name="usage_type" id="usage_type" class="form-input">
                    <option value="">-- Chọn cách dùng --</option>
                    <option value="Uống" {{ old('usage_type', $medicinalHerb->usage_type) == 'Uống' ? 'selected' : '' }}>Uống</option>
                    <option value="Sắc" {{ old('usage_type', $medicinalHerb->usage_type) == 'Sắc' ? 'selected' : '' }}>Sắc</option>
                    <option value="Dùng ngoài" {{ old('usage_type', $medicinalHerb->usage_type) == 'Dùng ngoài' ? 'selected' : '' }}>Dùng ngoài (Xoa bóp, Bôi)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="unit" class="form-label">Đơn vị tính</label>
                <input type="text" name="unit" id="unit" class="form-input" value="{{ old('unit', $medicinalHerb->unit) }}">
            </div>

            <div class="form-group">
                <label for="stock_quantity" class="form-label">Số lượng tồn kho *</label>
                <input type="number" step="0.01" name="stock_quantity" id="stock_quantity" class="form-input" value="{{ old('stock_quantity', floatval($medicinalHerb->stock_quantity)) }}" required min="0">
            </div>

            <div class="form-group">
                <label for="expiry_date" class="form-label">Hạn sử dụng</label>
                <input type="date" name="expiry_date" id="expiry_date" class="form-input" value="{{ old('expiry_date', $medicinalHerb->expiry_date?->format('Y-m-d')) }}">
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Trạng thái *</label>
                <select name="status" id="status" class="form-input" required>
                    <option value="active" {{ old('status', $medicinalHerb->status) == 'active' ? 'selected' : '' }}>Đang dùng (Còn hàng)</option>
                    <option value="out_of_stock" {{ old('status', $medicinalHerb->status) == 'out_of_stock' ? 'selected' : '' }}>Hết hàng</option>
                    <option value="expired" {{ old('status', $medicinalHerb->status) == 'expired' ? 'selected' : '' }}>Hết hạn / Ngừng dùng</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="warning_note" class="form-label">Cảnh báo/Chống chỉ định</label>
                <input type="text" name="warning_note" id="warning_note" class="form-input" value="{{ old('warning_note', $medicinalHerb->warning_note) }}">
            </div>
        </div>

        <div class="form-group" style="margin-top: 1.5rem;">
            <label for="description" class="form-label">Mô tả/Công dụng</label>
            <textarea name="description" id="description" class="form-input" rows="4">{{ old('description', $medicinalHerb->description) }}</textarea>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem; justify-content: flex-end;">
            <a href="{{ route('admin.medicinal-herbs.index') }}" class="btn btn-secondary">Hủy</a>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
        </div>
    </form>
</div>
@endsection
