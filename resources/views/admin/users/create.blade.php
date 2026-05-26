@extends('layouts.admin')

@section('title', 'Tạo Tài Khoản — AmaTrung')
@section('page-title', 'Tạo Tài Khoản Nhân Viên')

@section('content')
<div class="card" style="max-width: 1000px;">
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <div>
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="name" class="form-label">Họ tên *</label>
                    <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}" required>
                </div>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" name="email" id="email" class="form-input" value="{{ old('email') }}" required>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" id="phone" class="form-input" value="{{ old('phone') }}">
                </div>
            </div>

            <div>
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="password" class="form-label">Mật khẩu *</label>
                    <input type="text" name="password" id="password" class="form-input" value="{{ old('password', 'amatrung@123') }}" required>
                    <p class="form-hint">Mật khẩu mặc định là amatrung@123.</p>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="role" class="form-label">Vai trò *</label>
                    <select name="role" id="role" class="form-input" required onchange="togglePermissions()">
                        <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Nhân viên (Staff)</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                    </select>
                </div>
            </div>
        </div>

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
                        <input type="checkbox" name="permissions[]" value="patients.view" class="perm-cb perm-basic"> Xem danh sách
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="patients.create" class="perm-cb perm-basic"> Thêm mới
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="patients.edit" class="perm-cb perm-basic"> Sửa thông tin
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="patients.delete" class="perm-cb"> Xóa bệnh nhân
                    </label>
                </div>

                {{-- Bệnh án --}}
                <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
                    <h4 style="margin: 0 0 0.75rem; color: #10b981; font-size: 0.95rem;">📋 Quản lý Bệnh án</h4>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="medical_records.view" class="perm-cb perm-basic"> Xem bệnh án
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="medical_records.create" class="perm-cb perm-basic"> Tạo bệnh án
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="medical_records.edit" class="perm-cb perm-basic"> Sửa bệnh án
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="medical_records.delete" class="perm-cb"> Xóa bệnh án
                    </label>
                </div>

                {{-- Đơn thuốc --}}
                <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
                    <h4 style="margin: 0 0 0.75rem; color: #f59e0b; font-size: 0.95rem;">💊 Quản lý Đơn thuốc</h4>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="prescriptions.view" class="perm-cb perm-basic"> Xem đơn thuốc
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="prescriptions.create" class="perm-cb perm-basic"> Kê đơn
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="prescriptions.edit" class="perm-cb"> Sửa đơn (Hạn chế)
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="prescriptions.delete" class="perm-cb"> Xóa đơn thuốc
                    </label>
                </div>

                {{-- Dược liệu --}}
                <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
                    <h4 style="margin: 0 0 0.75rem; color: #8b5cf6; font-size: 0.95rem;">🌿 Kho Dược liệu</h4>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="medicinal_herbs.view" class="perm-cb perm-basic"> Xem danh sách
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="medicinal_herbs.create" class="perm-cb"> Nhập kho
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="medicinal_herbs.edit" class="perm-cb"> Sửa thông tin
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="medicinal_herbs.delete" class="perm-cb"> Xóa
                    </label>
                </div>

                {{-- Nội dung & Khác --}}
                <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
                    <h4 style="margin: 0 0 0.75rem; color: #ec4899; font-size: 0.95rem;">📰 Khác</h4>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="articles.manage" class="perm-cb"> Quản lý Bài viết
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="herb_dictionary.manage" class="perm-cb"> Quản lý Từ điển thuốc nam
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="comments.manage" class="perm-cb"> Quản lý Bình luận
                    </label>
                    <label style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="ai.suggest" class="perm-cb perm-basic"> Sử dụng AI Gợi ý
                    </label>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem; justify-content: flex-end;">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Hủy</a>
            <button type="submit" class="btn btn-primary">Tạo Tài Khoản</button>
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
            checkboxes.forEach(cb => {
                cb.checked = false;
                cb.disabled = true;
            });
        }
    }

    function checkAllBasic() {
        document.querySelectorAll('.perm-basic').forEach(cb => cb.checked = true);
    }

    // Run on load
    document.addEventListener('DOMContentLoaded', togglePermissions);
</script>
@endpush
