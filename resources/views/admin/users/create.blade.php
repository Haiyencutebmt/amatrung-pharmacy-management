@extends('layouts.admin')

@section('title', 'Tạo Tài Khoản — AmaTrung')
@section('page-title', '')

@section('header-left')
<div style="display: flex; justify-content: space-between; align-items: center; width: 100%; padding-right: 2rem;">
    <div class="header-title" style="display: flex; align-items: center; gap: 0.75rem;">
        <div class="icon-bg" style="width: 44px; height: 44px; background: #eff6ff; color: #3b82f6; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
            <span class="icon">👤+</span>
        </div>
        <div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: #1e293b; margin: 0; line-height: 1.2;">Tạo tài khoản nhân viên</h1>
            <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Thêm tài khoản nội bộ và cấp quyền truy cập hệ thống AmaTrung.</p>
        </div>
    </div>
</div>
@endsection

@section('content')
@php
    $selectedPermissions = old('permissions', []);
    if (!is_array($selectedPermissions)) {
        $selectedPermissions = [];
    }

    $permissionGroups = [
        [
            'title' => 'Quản lý bệnh nhân',
            'icon' => '👥',
            'tone' => 'blue',
            'items' => [
                ['value' => 'patients.view', 'label' => 'Xem danh sách', 'basic' => true],
                ['value' => 'patients.create', 'label' => 'Thêm mới', 'basic' => true],
                ['value' => 'patients.edit', 'label' => 'Sửa thông tin', 'basic' => true],
                ['value' => 'patients.delete', 'label' => 'Xóa bệnh nhân', 'basic' => false],
            ],
        ],
        [
            'title' => 'Quản lý bệnh án',
            'icon' => '📋',
            'tone' => 'green',
            'items' => [
                ['value' => 'medical_records.view', 'label' => 'Xem bệnh án', 'basic' => true],
                ['value' => 'medical_records.create', 'label' => 'Tạo bệnh án', 'basic' => true],
                ['value' => 'medical_records.edit', 'label' => 'Sửa bệnh án', 'basic' => true],
                ['value' => 'medical_records.delete', 'label' => 'Xóa bệnh án', 'basic' => false],
            ],
        ],
        [
            'title' => 'Quản lý đơn thuốc',
            'icon' => '💊',
            'tone' => 'amber',
            'items' => [
                ['value' => 'prescriptions.view', 'label' => 'Xem đơn thuốc', 'basic' => true],
                ['value' => 'prescriptions.create', 'label' => 'Kê đơn', 'basic' => true],
                ['value' => 'prescriptions.edit', 'label' => 'Sửa đơn (Hạn chế)', 'basic' => false],
                ['value' => 'prescriptions.delete', 'label' => 'Xóa đơn thuốc', 'basic' => false],
            ],
        ],
        [
            'title' => 'Kho dược liệu',
            'icon' => '🌿',
            'tone' => 'violet',
            'items' => [
                ['value' => 'medicinal_herbs.view', 'label' => 'Xem danh sách', 'basic' => true],
                ['value' => 'medicinal_herbs.create', 'label' => 'Nhập kho', 'basic' => false],
                ['value' => 'medicinal_herbs.edit', 'label' => 'Sửa thông tin', 'basic' => false],
                ['value' => 'medicinal_herbs.delete', 'label' => 'Xóa', 'basic' => false],
            ],
        ],
        [
            'title' => 'Nội dung & khác',
            'icon' => '📰',
            'tone' => 'rose',
            'items' => [
                ['value' => 'articles.manage', 'label' => 'Quản lý bài viết', 'basic' => false],
                ['value' => 'herb_dictionary.manage', 'label' => 'Quản lý từ điển dược liệu', 'basic' => false],
                ['value' => 'comments.manage', 'label' => 'Quản lý bình luận', 'basic' => false],
                ['value' => 'ai.suggest', 'label' => 'Sử dụng AI gợi ý', 'basic' => true],
            ],
        ],
    ];
@endphp

<div class="user-create-page">
    <form action="{{ route('admin.users.store') }}" method="POST" class="user-create-form">
        @csrf

        <div class="form-shell">
            <div class="form-topbar">
                <div>
                    <h2>Thông tin tài khoản</h2>
                    <p>Nhập thông tin đăng nhập và vai trò cho nhân viên.</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn-ghost">← Quay lại danh sách</a>
            </div>

            @if($errors->any())
                <div class="form-alert">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="account-grid">
                <section class="form-panel">
                    <div class="panel-title">
                        <span>👤</span>
                        <h3>Hồ sơ nhân viên</h3>
                    </div>

                    <div class="field-grid">
                        <div class="form-field">
                            <label for="name">Họ tên <span>*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="VD: Nguyễn Văn An">
                        </div>

                        <div class="form-field">
                            <label for="email">Email <span>*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="email@amatrung.vn">
                        </div>

                        <div class="form-field">
                            <label for="phone">Số điện thoại</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="VD: 0912345678">
                        </div>
                    </div>
                </section>

                <section class="form-panel">
                    <div class="panel-title">
                        <span>🛡️</span>
                        <h3>Vai trò truy cập</h3>
                    </div>

                    <div class="field-grid">
                        <div class="form-field">
                            <label for="password">Mật khẩu <span>*</span></label>
                            <input type="text" name="password" id="password" value="{{ old('password', 'amatrung@123') }}" required>
                            <small>Mật khẩu mặc định là <strong>amatrung@123</strong>.</small>
                        </div>

                        <div class="form-field">
                            <label for="role">Vai trò <span>*</span></label>
                            <select name="role" id="role" required onchange="togglePermissions()">
                                <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Nhân viên (Staff)</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                            </select>
                            <small>Admin có toàn quyền, Staff chỉ dùng các quyền được chọn bên dưới.</small>
                        </div>
                    </div>
                </section>
            </div>

            <section id="permissions_section" class="permissions-section">
                <div class="permissions-head">
                    <div>
                        <h2>Cấp quyền sử dụng</h2>
                        <p>Chỉ áp dụng cho tài khoản Staff. Chọn đúng chức năng nhân viên được phép thao tác.</p>
                    </div>
                    <div class="permission-actions">
                        <button type="button" class="btn-soft" onclick="clearPermissions()">Bỏ chọn</button>
                        <button type="button" class="btn-soft primary" onclick="checkAllBasic()">Chọn quyền cơ bản</button>
                    </div>
                </div>

                <div class="notice-row">
                    <span>⚠️</span>
                    <strong>Staff chỉ được sử dụng các chức năng được Admin cấp dấu tích dưới đây.</strong>
                </div>

                <div class="permission-grid">
                    @foreach($permissionGroups as $group)
                        <div class="permission-card permission-{{ $group['tone'] }}">
                            <div class="permission-title">
                                <span>{{ $group['icon'] }}</span>
                                <h3>{{ $group['title'] }}</h3>
                            </div>

                            <div class="permission-list">
                                @foreach($group['items'] as $permission)
                                    <label class="permission-option">
                                        <input
                                            type="checkbox"
                                            name="permissions[]"
                                            value="{{ $permission['value'] }}"
                                            class="perm-cb {{ $permission['basic'] ? 'perm-basic' : '' }}"
                                            @checked(in_array($permission['value'], $selectedPermissions, true))
                                        >
                                        <span>{{ $permission['label'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <div class="form-footer">
                <a href="{{ route('admin.users.index') }}" class="btn-cancel">Hủy</a>
                <button type="submit" class="btn-submit">Tạo tài khoản</button>
            </div>
        </div>
    </form>
</div>

<style>
.user-create-page {
    color: #1e293b;
    font-family: var(--font-sans, 'Be Vietnam Pro', 'Segoe UI', system-ui, sans-serif);
    margin-top: -1rem;
}

.user-create-form {
    max-width: 1180px;
}

.form-shell {
    background: #fff;
    border: 1px solid #f1f5f9;
    border-radius: 1rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01);
    padding: 1.5rem;
}

.form-topbar,
.permissions-head,
.form-footer {
    align-items: center;
    display: flex;
    justify-content: space-between;
    gap: 1rem;
}

.form-topbar {
    border-bottom: 1px solid #f1f5f9;
    margin-bottom: 1.5rem;
    padding-bottom: 1.25rem;
}

.form-topbar h2,
.permissions-head h2 {
    color: #1e293b;
    font-size: 1.05rem;
    font-weight: 700;
    line-height: 1.2;
    margin: 0;
}

.form-topbar p,
.permissions-head p {
    color: #64748b;
    font-size: 0.82rem;
    font-weight: 500;
    margin: 0.3rem 0 0;
}

.btn-ghost,
.btn-cancel,
.btn-soft,
.btn-submit {
    align-items: center;
    border-radius: 0.5rem;
    cursor: pointer;
    display: inline-flex;
    font-size: 0.85rem;
    font-weight: 700;
    justify-content: center;
    min-height: 42px;
    padding: 0.65rem 1.1rem;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-ghost,
.btn-cancel,
.btn-soft {
    background: #fff;
    border: 1px solid #e2e8f0;
    color: #64748b;
}

.btn-ghost:hover,
.btn-cancel:hover,
.btn-soft:hover {
    background: #f8fafc;
    color: #1e293b;
}

.btn-soft.primary,
.btn-submit {
    background: #3b82f6;
    border: 1px solid #3b82f6;
    color: #fff;
    box-shadow: 0 4px 10px rgba(59, 130, 246, 0.18);
}

.btn-soft.primary:hover,
.btn-submit:hover {
    background: #2563eb;
    border-color: #2563eb;
}

.form-alert {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 0.75rem;
    color: #b91c1c;
    font-size: 0.85rem;
    font-weight: 700;
    margin-bottom: 1.25rem;
    padding: 0.9rem 1rem;
}

.account-grid {
    display: grid;
    gap: 1rem;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    margin-bottom: 1.5rem;
}

.form-panel {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 1.2rem;
}

.panel-title,
.permission-title {
    align-items: center;
    display: flex;
    gap: 0.6rem;
}

.panel-title {
    border-bottom: 1px solid #e2e8f0;
    margin-bottom: 1rem;
    padding-bottom: 0.85rem;
}

.panel-title span,
.permission-title span {
    align-items: center;
    background: #eff6ff;
    border-radius: 0.5rem;
    color: #3b82f6;
    display: flex;
    height: 34px;
    justify-content: center;
    width: 34px;
}

.panel-title h3,
.permission-title h3 {
    color: #1e293b;
    font-size: 0.95rem;
    font-weight: 700;
    margin: 0;
}

.field-grid {
    display: grid;
    gap: 1rem;
}

.form-field label {
    color: #334155;
    display: block;
    font-size: 0.82rem;
    font-weight: 700;
    margin-bottom: 0.45rem;
}

.form-field label span {
    color: #ef4444;
}

.form-field input,
.form-field select {
    background: #fff;
    border: 1px solid #dbe4f0;
    border-radius: 0.6rem;
    color: #1e293b;
    font-size: 0.92rem;
    font-weight: 600;
    min-height: 46px;
    outline: none;
    padding: 0.72rem 0.85rem;
    transition: border-color 0.2s, box-shadow 0.2s;
    width: 100%;
}

.form-field input:focus,
.form-field select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-field small {
    color: #94a3b8;
    display: block;
    font-size: 0.76rem;
    font-weight: 600;
    margin-top: 0.45rem;
}

.permissions-section {
    border-top: 1px solid #f1f5f9;
    padding-top: 1.5rem;
}

.permission-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.65rem;
    justify-content: flex-end;
}

.notice-row {
    align-items: center;
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 0.75rem;
    color: #b45309;
    display: flex;
    gap: 0.6rem;
    font-size: 0.85rem;
    margin: 1rem 0 1.25rem;
    padding: 0.8rem 0.9rem;
}

.permission-grid {
    display: grid;
    gap: 1rem;
    grid-template-columns: repeat(auto-fill, minmax(245px, 1fr));
}

.permission-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 1rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.permission-card:hover {
    border-color: #bfdbfe;
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.06);
}

.permission-title {
    margin-bottom: 0.8rem;
}

.permission-blue .permission-title span { background: #eff6ff; color: #3b82f6; }
.permission-green .permission-title span { background: #f0fdf4; color: #16a34a; }
.permission-amber .permission-title span { background: #fffbeb; color: #d97706; }
.permission-violet .permission-title span { background: #f5f3ff; color: #8b5cf6; }
.permission-rose .permission-title span { background: #fdf2f8; color: #ec4899; }

.permission-list {
    display: grid;
    gap: 0.55rem;
}

.permission-option {
    align-items: center;
    border-radius: 0.5rem;
    color: #334155;
    cursor: pointer;
    display: flex;
    font-size: 0.88rem;
    font-weight: 500;
    gap: 0.55rem;
    min-height: 34px;
    padding: 0.35rem 0.45rem;
}

.permission-option:hover {
    background: #f8fafc;
}

.permission-option input {
    accent-color: #3b82f6;
    height: 15px;
    width: 15px;
}

.permissions-section.is-disabled {
    opacity: 0.55;
}

.permissions-section.is-disabled .permission-card {
    background: #f8fafc;
}

.form-footer {
    border-top: 1px solid #f1f5f9;
    justify-content: flex-end;
    margin-top: 1.5rem;
    padding-top: 1.25rem;
}

@media (max-width: 1100px) {
    .account-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 720px) {
    .form-shell {
        padding: 1rem;
    }

    .form-topbar,
    .permissions-head,
    .form-footer {
        align-items: stretch;
        flex-direction: column;
    }

    .permission-actions,
    .btn-ghost,
    .btn-cancel,
    .btn-submit {
        width: 100%;
    }

    .permission-actions {
        flex-direction: column;
    }
}
</style>
@endsection

@push('scripts')
<script>
    function togglePermissions() {
        const role = document.getElementById('role').value;
        const section = document.getElementById('permissions_section');
        const checkboxes = document.querySelectorAll('.perm-cb');
        
        if (role === 'staff') {
            section.classList.remove('is-disabled');
            section.style.pointerEvents = 'auto';
            checkboxes.forEach(cb => cb.disabled = false);
        } else {
            section.classList.add('is-disabled');
            section.style.pointerEvents = 'none';
            checkboxes.forEach(cb => {
                cb.checked = false;
                cb.disabled = true;
            });
        }
    }

    function checkAllBasic() {
        if (document.getElementById('role').value !== 'staff') return;
        document.querySelectorAll('.perm-basic').forEach(cb => cb.checked = true);
    }

    function clearPermissions() {
        if (document.getElementById('role').value !== 'staff') return;
        document.querySelectorAll('.perm-cb').forEach(cb => cb.checked = false);
    }

    document.addEventListener('DOMContentLoaded', togglePermissions);
</script>
@endpush
