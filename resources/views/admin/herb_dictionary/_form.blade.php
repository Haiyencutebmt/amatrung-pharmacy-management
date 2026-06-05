@php
    $entry = $entry ?? null;
    $isEdit = (bool) $entry;
    $currentImageCount = $isEdit ? $entry->images->count() : 0;
    $remainingImages = max(0, 5 - $currentImageCount);
@endphp

@if($errors->any())
    <div style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:1rem; border-radius:8px; margin-bottom:1rem; font-weight:700;">
        {{ $errors->first() }}
    </div>
@endif

<div class="dict-admin-grid">
    <div class="dict-admin-main">
        <div class="form-block">
            <label>Tên cây thuốc / vị thuốc *</label>
            <input type="text" name="name" value="{{ old('name', $entry->name ?? '') }}" required>
        </div>
        <div class="form-row">
            <div class="form-block">
                <label>Tên khoa học</label>
                <input type="text" name="scientific_name" value="{{ old('scientific_name', $entry->scientific_name ?? '') }}">
            </div>
            <div class="form-block">
                <label>Tên gọi khác</label>
                <input type="text" name="other_names" value="{{ old('other_names', $entry->other_names ?? '') }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-block">
                <label>Họ thực vật</label>
                <input type="text" name="family" value="{{ old('family', $entry->family ?? '') }}">
            </div>
            <div class="form-block">
                <label>Bộ phận dùng</label>
                <input type="text" name="plant_part" value="{{ old('plant_part', $entry->plant_part ?? '') }}" placeholder="VD: lá, rễ, thân, hoa...">
            </div>
        </div>
        <div class="form-block">
            <label>Tính vị / đặc điểm ngắn</label>
            <input type="text" name="properties" value="{{ old('properties', $entry->properties ?? '') }}" placeholder="VD: vị cay, tính ấm; dùng ngoài...">
        </div>
        <div class="form-block">
            <label>Thông tin cơ bản *</label>
            <textarea name="basic_info" rows="5" required>{{ old('basic_info', $entry->basic_info ?? '') }}</textarea>
        </div>
        <div class="form-block">
            <label>Tác dụng *</label>
            <textarea name="effects" rows="5" required>{{ old('effects', $entry->effects ?? '') }}</textarea>
        </div>
        <div class="form-block">
            <label>Cách dùng</label>
            <textarea name="usage_notes" rows="4">{{ old('usage_notes', $entry->usage_notes ?? '') }}</textarea>
        </div>
        <div class="form-block">
            <label>Khuyến cáo an toàn</label>
            <textarea name="safety_warning" rows="4" placeholder="Nếu để trống, hệ thống vẫn hiển thị khuyến cáo không tự ý sử dụng.">{{ old('safety_warning', $entry->safety_warning ?? '') }}</textarea>
        </div>
    </div>

    <aside class="dict-admin-side">
        <div class="side-card">
            <label>Trạng thái</label>
            <select name="status">
                <option value="published" {{ old('status', $entry->status ?? 'published') === 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                <option value="draft" {{ old('status', $entry->status ?? '') === 'draft' ? 'selected' : '' }}>Bản nháp</option>
            </select>
        </div>

        <div class="side-card">
            <label>Hình ảnh {{ $isEdit ? '' : '*' }}</label>
            <input type="file" name="images[]" accept="image/*" multiple {{ $isEdit ? '' : 'required' }}>
            <p>Tối đa 5 ảnh cho mỗi mục. Mỗi ảnh tối đa 4MB.</p>
            @if($isEdit)
                <p>Hiện có {{ $currentImageCount }}/5 ảnh. Có thể thêm {{ $remainingImages }} ảnh nữa.</p>
            @endif
        </div>

        @if($isEdit && $entry->images->count() > 0)
            <div class="side-card">
                <label>Ảnh hiện tại</label>
                <div class="image-preview-grid">
                    @foreach($entry->images as $image)
                        <div class="image-preview-item">
                            <img src="{{ $image->url }}" alt="{{ $entry->name }}">
                            <button
                                type="submit"
                                form="delete-herb-image-{{ $image->id }}"
                                class="image-delete-button"
                                title="Xóa ảnh"
                                aria-label="Xóa ảnh khỏi {{ $entry->name }}"
                            >
                                &times;
                            </button>
                        </div>
                    @endforeach
                </div>
                <p>Bấm dấu x trên ảnh để xóa ảnh không đúng. Hệ thống sẽ hỏi xác nhận trước khi xóa.</p>
            </div>
        @endif

        <div class="side-actions">
            <button type="submit">{{ $isEdit ? 'Cập nhật' : 'Thêm vào từ điển' }}</button>
            <a href="{{ route('admin.herb-dictionary.index') }}">Hủy</a>
        </div>
    </aside>
</div>

<style>
.dict-admin-grid { display: grid; grid-template-columns: minmax(0, 1fr) 320px; gap: 1rem; }
.dict-admin-main, .dict-admin-side .side-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem; }
.form-block { margin-bottom: 0.85rem; }
.form-block label, .side-card label { display: block; font-size: 0.8rem; font-weight: 850; color: #475569; text-transform: uppercase; margin-bottom: 0.35rem; }
.form-block input, .form-block textarea, .side-card select, .side-card input { width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0.65rem 0.75rem; font-size: 0.95rem; box-sizing: border-box; }
.form-block textarea { resize: vertical; line-height: 1.55; }
.dict-admin-side { display: flex; flex-direction: column; gap: 1rem; }
.side-card p { margin: 0.55rem 0 0; color: #64748b; font-size: 0.85rem; line-height: 1.45; }
.image-preview-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; }
.image-preview-item { position: relative; aspect-ratio: 1; }
.image-preview-item img { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; box-sizing: border-box; }
.image-delete-button { position: absolute; top: 0.35rem; right: 0.35rem; width: 28px; height: 28px; border: 1px solid #fecaca; border-radius: 999px; background: #fff; color: #dc2626; font-size: 1.1rem; font-weight: 900; line-height: 1; cursor: pointer; box-shadow: 0 8px 18px rgba(15, 23, 42, 0.14); display: flex; align-items: center; justify-content: center; }
.image-delete-button:hover { background: #fef2f2; border-color: #fca5a5; }
.side-actions { display: flex; gap: 0.6rem; }
.side-actions button, .side-actions a { flex: 1; border: 0; border-radius: 8px; padding: 0.75rem; font-weight: 850; cursor: pointer; text-align: center; text-decoration: none; }
.side-actions button { background: #16a34a; color: white; }
.side-actions a { background: #f1f5f9; color: #475569; }
@media (max-width: 980px) {
    .dict-admin-grid { grid-template-columns: 1fr; }
    .form-row { grid-template-columns: 1fr; }
}
</style>
