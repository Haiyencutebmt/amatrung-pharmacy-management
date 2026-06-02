@extends('layouts.admin')

@section('title', 'Sửa Tài Khoản — AmaTrung')
@section('page-title', 'Sửa Tài Khoản: ' . $user->name)

@section('content')
<div class="card" style="max-width: 1000px;">
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <div>
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="name" class="form-label">Họ tên *</label>
                    <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                </div>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" name="email" id="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" id="phone" class="form-input" value="{{ old('phone', $user->phone) }}">
                </div>
            </div>

            <div>
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="role" class="form-label">Vai trò *</label>
                    <select name="role" id="role" class="form-input" required onchange="togglePermissions()" {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                        <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Nhân viên (Staff)</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                    </select>
                    @if(auth()->id() === $user->id)
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <p class="form-hint" style="color: #d97706;">Bạn không thể tự đổi vai trò của chính mình.</p>
                    @endif
                </div>
            </div>
        </div>

        @php
            $perms = $user->legacy_permissions_json ?? [];
        @endphp

        <div id="permissions_section" style="border-top: 1px solid #e2e8f0; padding-top: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="margin: 0; font-size: 1.1rem; color: #1e3a5f;">Cấp quyền sử dụng (Chỉ áp dụng cho Staff)</h3>
                <button type="button" class="btn btn-sm btn-secondary" onclick="checkAllBasic()">Chọn các quyền cơ bản</button>
            </div>
            <p class="form-hint" style="color: #b91c1c; font-weight: 500; margin-bottom: 1.5rem;">⚠️ Staff chỉ được sử dụng các chức năng được Admin cấp dấu tích (✓) dưới đây.</p>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
                {{-- Bệnh nhân --}}
                <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
                    <h4 style="margin: 0 0 0.75rem; color: #3b82f6; font-size: 0.95rem;">👥 Quản lý Bệnh nhân</h4>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="patients.view" class="perm-cb perm-basic" {{ in_array('patients.view', $perms) ? 'checked' : '' }}> Xem danh sách
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="patients.create" class="perm-cb perm-basic" {{ in_array('patients.create', $perms) ? 'checked' : '' }}> Thêm mới
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="patients.edit" class="perm-cb perm-basic" {{ in_array('patients.edit', $perms) ? 'checked' : '' }}> Sửa thông tin
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="patients.delete" class="perm-cb" {{ in_array('patients.delete', $perms) ? 'checked' : '' }}> Xóa bệnh nhân
                    </label>
                </div>

                {{-- Bệnh án --}}
                <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
                    <h4 style="margin: 0 0 0.75rem; color: #10b981; font-size: 0.95rem;">📋 Quản lý Bệnh án</h4>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="medical_records.view" class="perm-cb perm-basic" {{ in_array('medical_records.view', $perms) ? 'checked' : '' }}> Xem bệnh án
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="medical_records.create" class="perm-cb perm-basic" {{ in_array('medical_records.create', $perms) ? 'checked' : '' }}> Tạo bệnh án
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="medical_records.edit" class="perm-cb perm-basic" {{ in_array('medical_records.edit', $perms) ? 'checked' : '' }}> Sửa bệnh án
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="medical_records.delete" class="perm-cb" {{ in_array('medical_records.delete', $perms) ? 'checked' : '' }}> Xóa bệnh án
                    </label>
                </div>

                {{-- Đơn thuốc --}}
                <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
                    <h4 style="margin: 0 0 0.75rem; color: #f59e0b; font-size: 0.95rem;">💊 Quản lý Đơn thuốc</h4>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="prescriptions.view" class="perm-cb perm-basic" {{ in_array('prescriptions.view', $perms) ? 'checked' : '' }}> Xem đơn thuốc
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="prescriptions.create" class="perm-cb perm-basic" {{ in_array('prescriptions.create', $perms) ? 'checked' : '' }}> Kê đơn
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="prescriptions.edit" class="perm-cb" {{ in_array('prescriptions.edit', $perms) ? 'checked' : '' }}> Sửa đơn (Hạn chế)
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="prescriptions.delete" class="perm-cb" {{ in_array('prescriptions.delete', $perms) ? 'checked' : '' }}> Xóa đơn thuốc
                    </label>
                </div>

                {{-- Dược liệu --}}
                <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
                    <h4 style="margin: 0 0 0.75rem; color: #8b5cf6; font-size: 0.95rem;">🌿 Kho Dược liệu</h4>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="medicinal_herbs.view" class="perm-cb perm-basic" {{ in_array('medicinal_herbs.view', $perms) ? 'checked' : '' }}> Xem danh sách
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="medicinal_herbs.create" class="perm-cb" {{ in_array('medicinal_herbs.create', $perms) ? 'checked' : '' }}> Nhập kho
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="medicinal_herbs.edit" class="perm-cb" {{ in_array('medicinal_herbs.edit', $perms) ? 'checked' : '' }}> Sửa thông tin
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="medicinal_herbs.delete" class="perm-cb" {{ in_array('medicinal_herbs.delete', $perms) ? 'checked' : '' }}> Xóa
                    </label>
                </div>

                {{-- Nội dung & Khác --}}
                <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
                    <h4 style="margin: 0 0 0.75rem; color: #ec4899; font-size: 0.95rem;">📰 Khác</h4>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="articles.manage" class="perm-cb" {{ in_array('articles.manage', $perms) ? 'checked' : '' }}> Quản lý Bài viết
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="herb_dictionary.manage" class="perm-cb" {{ in_array('herb_dictionary.manage', $perms) ? 'checked' : '' }}> Quản lý Từ điển dược liệu
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="comments.manage" class="perm-cb" {{ in_array('comments.manage', $perms) ? 'checked' : '' }}> Quản lý Bình luận
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="ai.suggest" class="perm-cb perm-basic" {{ in_array('ai.suggest', $perms) ? 'checked' : '' }}> Sử dụng AI Gợi ý
                    </label>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem; justify-content: flex-end;">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Hủy</a>
            <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function togglePermissions() {
        const role = document.getElementById('role').value;
        const section = document.getElementById('permissions_section');
        const checkboxes = document.querySelectorAll('.perm-cb');
        
        if (role === 'staff') {
            section.style.opacity = '1';
            section.style.pointerEvents = 'auto';
            checkboxes.forEach(cb => cb.disabled = false);
        } else {
            section.style.opacity = '0.5';
            section.style.pointerEvents = 'none';
            checkboxes.forEach(cb => cb.disabled = true);
        }
    }

    function checkAllBasic() {
        if(document.getElementById('role').value !== 'staff') return;
        document.querySelectorAll('.perm-basic').forEach(cb => cb.checked = true);
    }

    // Run on load
    document.addEventListener('DOMContentLoaded', togglePermissions);
</script>
@endpush
