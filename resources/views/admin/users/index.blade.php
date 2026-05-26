@extends('layouts.admin')

@section('title', 'Quản lý Tài Khoản — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div class="icon-bg" style="width: 44px; height: 44px; background: #eef9ee; color: #5eb542; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
        <span class="icon">👥</span>
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Quản lý tài khoản</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Quản lý người dùng, phân quyền và trạng thái truy cập</p>
    </div>
</div>
@endsection

@section('content')
<div class="record-index-container" style="margin-top: -1rem;">

    <div class="main-content-card">
        <form action="{{ route('admin.users.index') }}" method="GET" class="filter-form">
            <div class="filter-row">
                <div style="display: flex; gap: 1.5rem; align-items: flex-end; flex: 1;">
                    <div class="filter-item" style="flex-direction: column; align-items: flex-start; gap: 0.5rem;">
                        <label style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Tìm tài khoản</label>
                        <div class="search-input-group" style="width: 350px;">
                            <span class="icon">🔍</span>
                            <input type="text" name="search" placeholder="Tìm tên, email, SĐT..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="filter-item" style="flex-direction: column; align-items: flex-start; gap: 0.5rem;">
                        <label style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Vai trò</label>
                        <select name="role" style="padding: 0.8rem 1rem; border-radius: 0.75rem; border: 1px solid var(--border-color); background: #fcfdfe; width: 160px; color: var(--text-dark); font-weight: 600;">
                            <option value="">-- Tất cả vai trò --</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                        </select>
                    </div>
                    <div class="action-buttons" style="margin-bottom: 2px;">
                        <button type="submit" class="btn-filter">
                            <span class="icon">🔍</span> Lọc
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn-reset-box">
                            <span class="icon">🔄</span> Reset
                        </a>
                    </div>
                </div>
                
                <div class="action-buttons" style="margin-bottom: 2px;">
                    <a href="{{ route('admin.users.create') }}" class="btn-add">
                        <span class="icon">+</span> Tạo Tài Khoản
                    </a>
                </div>
            </div>
        </form>

        <div class="table-container">
            <table class="patient-table">
                <thead>
                    <tr>
                        <th>HỌ TÊN</th>
                        <th>EMAIL</th>
                        <th>SĐT</th>
                        <th>VAI TRÒ</th>
                        <th>TRẠNG THÁI</th>
                        <th>NGÀY TẠO</th>
                        <th>HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="patient-name-cell">
                                <div class="avatar-circle" style="background: {{ '#' . substr(md5($user->name), 0, 6) }}20; color: {{ '#' . substr(md5($user->name), 0, 6) }}">
                                    {{ mb_substr($user->name, 0, 1) }}
                                </div>
                                <div class="name">{{ $user->name }}</div>
                            </div>
                        </td>
                        <td>
                            <span style="color: #475569; font-weight: 500;">{{ $user->email }}</span>
                        </td>
                        <td>
                            <span style="color: #475569; font-weight: 500;">{{ $user->phone ?? '---' }}</span>
                        </td>
                        <td>
                            @if($user->role === 'admin')
                                <span style="background: #fef2f2; color: #ef4444; padding: 0.3rem 0.75rem; border-radius: 0.5rem; font-weight: 800; font-size: 0.75rem;">Admin</span>
                            @elseif($user->role === 'staff')
                                <span style="background: #eff6ff; color: #3b82f6; padding: 0.3rem 0.75rem; border-radius: 0.5rem; font-weight: 800; font-size: 0.75rem;">Staff</span>
                            @else
                                <span style="background: #f1f5f9; color: #64748b; padding: 0.3rem 0.75rem; border-radius: 0.5rem; font-weight: 800; font-size: 0.75rem;">User</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_active)
                                <span style="background: #eef9ee; color: #16a34a; padding: 0.3rem 0.75rem; border-radius: 0.5rem; font-weight: 800; font-size: 0.75rem;">Hoạt động</span>
                            @else
                                <span style="background: #f1f5f9; color: #64748b; padding: 0.3rem 0.75rem; border-radius: 0.5rem; font-weight: 800; font-size: 0.75rem;">Đã khóa</span>
                            @endif
                        </td>
                        <td>
                            <span style="font-weight: 600; color: #64748b; font-size: 0.85rem;">{{ $user->created_at->format('d/m/Y') }}</span>
                        </td>
                        <td>
                            <div class="action-cell" style="flex-wrap: wrap;">
                                @if($user->role !== 'user')
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn-icon edit" title="Sửa/Phân quyền">
                                        <span class="icon">✏️</span> Sửa
                                    </a>
                                    
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn {{ $user->is_active ? 'khóa' : 'mở khóa' }} tài khoản này?');">
                                            @csrf @method('PATCH')
                                            @if($user->is_active)
                                                <button type="submit" class="btn-icon delete" title="Khóa">
                                                    <span class="icon">🔒</span> Khóa
                                                </button>
                                            @else
                                                <button type="submit" class="btn-icon view" style="border-color: #5eb542; color: #5eb542;" title="Mở khóa">
                                                    <span class="icon">🔓</span> Mở khóa
                                                </button>
                                            @endif
                                        </form>

                                        <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mật khẩu sẽ được đặt lại thành amatrung@123. Bạn có chắc chắn?');">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn-icon view" title="Đặt lại mật khẩu">
                                                <span class="icon">🔑</span> Reset mật khẩu
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <span style="color: #94a3b8; font-size: 0.8rem; font-weight: 600; font-style: italic; background: #f1f5f9; padding: 0.25rem 0.5rem; border-radius: 0.25rem;">Chỉ xem</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #64748b; padding: 3rem 1rem; font-weight: 500;">
                            Không tìm thấy tài khoản nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="pagination-area">
            <p class="summary">Hiển thị {{ $users->firstItem() }} đến {{ $users->lastItem() }} của {{ $users->total() }} tài khoản</p>
            <div class="pagination-controls">
                {{ $users->withQueryString()->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.record-index-container {
    --primary-green: #5eb542;
    --text-dark: #1e293b;
    --text-muted: #64748b;
    --bg-light: #f8fafc;
    --border-color: #e2e8f0;
}

.header-title {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-title .icon-bg {
    width: 48px;
    height: 48px;
    background: #eef9ee;
    color: #5eb542;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.header-title h1 {
    font-size: 1.75rem;
    font-weight: 850;
    color: var(--text-dark);
    margin: 0;
}

.header-title p {
    margin: 0.25rem 0 0 0;
    color: var(--text-muted);
}

.main-content-card {
    background: #fff;
    border-radius: 1.5rem;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.02);
}

.filter-form {
    margin-bottom: 2rem;
}

.filter-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.search-input-group {
    position: relative;
    width: 400px;
}

.search-input-group input {
    width: 100%;
    padding: 0.8rem 1rem 0.8rem 2.75rem;
    border-radius: 0.75rem;
    border: 1px solid var(--border-color);
    background: #fcfdfe;
}

.search-input-group .icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

.action-buttons {
    display: flex;
    gap: 1rem;
}

.btn-filter {
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem;
    border: 1px solid #eef2ff;
    background: #f8fbff;
    color: #3b82f6;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-reset-box {
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem;
    border: 1px solid var(--border-color);
    background: #fff;
    color: var(--text-muted);
    font-weight: 700;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-reset-box:hover {
    background: #f8fafc;
}

.btn-add {
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem;
    background: #5eb542;
    color: #fff;
    text-decoration: none;
    font-weight: 700;
    box-shadow: 0 4px 12px rgba(94, 181, 66, 0.2);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.patient-table {
    width: 100%;
    border-collapse: collapse;
}

.patient-table th {
    text-align: left;
    padding: 1.25rem 1rem;
    font-size: 0.75rem;
    font-weight: 850;
    color: #1e3a5f;
    letter-spacing: 0.05em;
    border-bottom: 1.5px solid #f1f5f9;
}

.patient-table td {
    padding: 1.25rem 1rem;
    border-bottom: 1px solid #f8fafc;
}

.patient-code {
    background: #f0f7ff;
    color: #3b82f6;
    padding: 0.4rem 0.75rem;
    border-radius: 0.6rem;
    font-weight: 800;
    font-size: 0.8rem;
    border: 1px solid #dbeafe;
}

.patient-name-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.avatar-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.9rem;
}

.patient-name-cell .name {
    font-weight: 750;
    color: var(--text-dark);
}

.action-cell {
    display: flex;
    gap: 0.5rem;
}

.btn-icon {
    padding: 0.4rem 0.75rem;
    border-radius: 0.6rem;
    font-size: 0.75rem;
    font-weight: 750;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    border: 1px solid var(--border-color);
}

.btn-icon.view { background: #fff; color: var(--text-dark); }
.btn-icon.edit { background: #fff; color: #3b82f6; }
.btn-icon.delete { background: #fff; color: #ef4444; }

.btn-icon:hover {
    background: #f8fafc;
}

.pagination-area {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 2rem;
}

.pagination-area .summary {
    font-size: 0.85rem;
    color: var(--text-muted);
    font-weight: 600;
}

/* Pagination Overrides for Laravel */
.pagination {
    display: flex;
    gap: 0.25rem;
}

.page-item .page-link {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.5rem;
    background: #fff;
    border: 1px solid var(--border-color);
    color: var(--text-dark);
    text-decoration: none;
    font-weight: 700;
}

.page-item.active .page-link {
    background: #5eb542;
    color: #fff;
    border-color: #5eb542;
}
</style>
@endsection
