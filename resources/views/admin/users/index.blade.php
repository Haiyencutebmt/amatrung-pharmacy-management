@extends('layouts.admin')

@section('title', 'Quản lý Tài Khoản — AmaTrung')
@section('page-title', '')

@section('header-left')
<div style="display: flex; justify-content: space-between; align-items: center; width: 100%; padding-right: 2rem;">
    <div class="header-title" style="display: flex; align-items: center; gap: 0.75rem;">
        <div class="icon-bg" style="width: 44px; height: 44px; background: #eff6ff; color: #3b82f6; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
            <span class="icon">👥</span>
        </div>
        <div>
            <h1 style="font-size: 1.4rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Quản lý tài khoản</h1>
            <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Quản lý người dùng, phân quyền và trạng thái truy cập hệ thống.</p>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="users-container" style="font-family: 'Inter', sans-serif; margin-top: -1rem;">

    {{-- Stats Cards --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline">
                        <span class="icon">👥</span>
                    </div>
                    <span class="stat-title">Tổng Tài Khoản</span>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value">{{ number_format($totalUsers) }}</h3>
            </div>
            <div class="stat-footer">
                Tất cả người dùng
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline" style="border-color: #ef4444; background: #fef2f2; color: #ef4444;">
                        <span class="icon">🛡️</span>
                    </div>
                    <span class="stat-title">Admin</span>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value">{{ number_format($adminCount) }}</h3>
            </div>
            <div class="stat-footer">
                Quản trị viên hệ thống
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline" style="border-color: #3b82f6; background: #eff6ff; color: #3b82f6;">
                        <span class="icon">👤</span>
                    </div>
                    <span class="stat-title">Nhân viên (Staff)</span>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value">{{ number_format($staffCount) }}</h3>
            </div>
            <div class="stat-footer">
                Nhân viên nội bộ
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline" style="border-color: #16a34a; background: #f0fdf4; color: #16a34a;">
                        <span class="icon">📈</span>
                    </div>
                    <span class="stat-title">Đang hoạt động</span>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value">{{ number_format($activeCount) }}</h3>
            </div>
            <div class="stat-footer">
                Tài khoản đang hoạt động
            </div>
        </div>
    </div>

    {{-- Main Content Area (Filters + Table) --}}
    <div class="main-content-card">
        {{-- Filter Form --}}
        <form id="filterForm" action="{{ route('admin.users.index') }}" method="GET" class="filter-form">
            <div class="filter-row">
                <div class="search-input-group">
                    <button type="submit" class="icon" title="Tìm kiếm" style="background: none; border: none; cursor: pointer; padding: 0; outline: none; color: #3b82f6;">🔍</button>
                    <input type="text" id="searchInput" name="search" placeholder="Tìm tên, email, SĐT..." value="{{ request('search') }}">
                </div>
                
                <div class="action-buttons" style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="font-size: 0.8rem; font-weight: 700; color: #1e293b;">Vai trò</span>
                        <select name="role" onchange="this.form.submit()" style="padding: 0.65rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.85rem; min-width: 130px; outline: none;">
                            <option value="">Tất cả</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                        </select>
                    </div>

                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="font-size: 0.8rem; font-weight: 700; color: #1e293b;">Trạng thái</span>
                        <select name="status" onchange="this.form.submit()" style="padding: 0.65rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.85rem; min-width: 140px; outline: none;">
                            <option value="">Tất cả</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Đã khóa</option>
                        </select>
                    </div>

                    <a href="{{ route('admin.users.index') }}" class="btn-reset" style="padding: 0.65rem 1.25rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; color: #64748b; font-weight: 700; text-decoration: none; font-size: 0.85rem; display: flex; align-items: center; gap: 0.4rem; transition: background 0.2s;">
                        ↻ Đặt lại
                    </a>
                    <div style="width: 1px; background: #e2e8f0; margin: 0 0.25rem; height: 32px;"></div>
                    <a href="{{ route('admin.users.create') }}" class="btn-add" style="padding: 0.65rem 1.25rem; background: #3b82f6; color: #fff; border-radius: 0.5rem; font-weight: 700; text-decoration: none; font-size: 0.85rem; display: flex; align-items: center; gap: 0.4rem; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2);">
                        <span class="icon">👤+</span> Tạo tài khoản
                    </a>
                </div>
            </div>
        </form>

        {{-- Table --}}
        <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="font-size: 0.75rem;">HỌ TÊN</th>
                            <th style="font-size: 0.75rem;">EMAIL</th>
                            <th style="font-size: 0.75rem;">SĐT</th>
                            <th style="font-size: 0.75rem;">VAI TRÒ</th>
                            <th style="font-size: 0.75rem;">TRẠNG THÁI</th>
                            <th style="font-size: 0.75rem;">NGÀY TẠO</th>
                            <th style="font-size: 0.75rem; width: 220px;">HÀNH ĐỘNG</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr style="transition: background 0.2s;">
                                <td style="padding: 1rem;">
                                    <div style="display: flex; align-items: center; gap: 0.6rem;">
                                        <div style="width: 32px; height: 32px; border-radius: 50%; background: {{ '#' . substr(md5($user->name), 0, 6) }}20; color: {{ '#' . substr(md5($user->name), 0, 6) }}; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem;">
                                            {{ mb_substr($user->name, 0, 1) }}
                                        </div>
                                        <div style="font-weight: 700; color: #1e293b; font-size: 0.85rem;">{{ $user->name }}</div>
                                    </div>
                                </td>
                                <td style="padding: 1rem; color: #475569; font-size: 0.85rem;">{{ $user->email }}</td>
                                <td style="padding: 1rem; color: #475569; font-size: 0.85rem;">{{ $user->phone ?? '---' }}</td>
                                <td style="padding: 1rem;">
                                    @if($user->role === 'admin')
                                        <span style="background: #fef2f2; color: #ef4444; padding: 0.25rem 0.65rem; border-radius: 1rem; font-weight: 700; font-size: 0.7rem;">Admin</span>
                                    @elseif($user->role === 'staff')
                                        <span style="background: #eff6ff; color: #3b82f6; padding: 0.25rem 0.65rem; border-radius: 1rem; font-weight: 700; font-size: 0.7rem;">Staff</span>
                                    @else
                                        <span style="background: #f1f5f9; color: #64748b; padding: 0.25rem 0.65rem; border-radius: 1rem; font-weight: 700; font-size: 0.7rem;">User</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem;">
                                    @if($user->is_active)
                                        <span style="background: #eef9ee; color: #16a34a; padding: 0.25rem 0.65rem; border-radius: 1rem; font-weight: 700; font-size: 0.7rem; display: inline-flex; align-items: center; gap: 0.3rem;">
                                            <div style="width: 6px; height: 6px; border-radius: 50%; background: #16a34a;"></div> Hoạt động
                                        </span>
                                    @else
                                        <span style="background: #fef2f2; color: #ef4444; padding: 0.25rem 0.65rem; border-radius: 1rem; font-weight: 700; font-size: 0.7rem; display: inline-flex; align-items: center; gap: 0.3rem;">
                                            <div style="width: 6px; height: 6px; border-radius: 50%; background: #ef4444;"></div> Đã khóa
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 1rem; color: #64748b; font-size: 0.8rem; font-weight: 500;">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td style="padding: 1rem;">
                                    <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                                        @if($user->role !== 'user')
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn-action" style="color: #3b82f6; border-color: #bfdbfe; background: #eff6ff;">
                                                ✏️ Sửa
                                            </a>
                                            
                                            @if(auth()->id() !== $user->id)
                                                <button type="button" onclick="document.getElementById('form-toggle-{{ $user->id }}').submit();" class="btn-action" style="color: {{ $user->is_active ? '#ef4444' : '#16a34a' }}; border-color: {{ $user->is_active ? '#fecaca' : '#bbf7d0' }}; background: {{ $user->is_active ? '#fef2f2' : '#f0fdf4' }};">
                                                    {{ $user->is_active ? '🔒 Khóa' : '🔓 Mở khóa' }}
                                                </button>
                                                
                                                <button type="button" onclick="showResetConfirm('{{ $user->id }}', '{{ addslashes($user->name) }}')" class="btn-action" style="color: #d97706; border-color: #fde68a; background: #fffbeb;">
                                                    🔑 Reset
                                                </button>

                                                @if($user->role === 'staff')
                                                    <button type="button" onclick="if(confirm('Bạn có chắc chắn muốn xóa tài khoản nhân viên này? Hành động này không thể hoàn tác.')) document.getElementById('form-destroy-{{ $user->id }}').submit();" class="btn-action" style="color: #ef4444; border-color: #fecaca; background: #fef2f2;">
                                                        🗑️ Xóa
                                                    </button>
                                                @endif
                                            @endif
                                            <a href="#" class="btn-action" style="color: #3b82f6; border-color: #bfdbfe; background: #fff;">
                                                ℹ️ Chi tiết
                                            </a>
                                        @else
                                            <span class="btn-action" style="color: #94a3b8; border-color: #e2e8f0; background: #f8fafc; cursor: not-allowed;">Chỉ xem</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->role === 'admin' ? '8' : '7' }}" style="padding: 3rem; text-align: center; color: #64748b; font-size: 0.95rem; font-weight: 500;">
                                    🔍 Không tìm thấy tài khoản nào phù hợp.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($users->hasPages())
                <div style="padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9;">
                    <div style="font-size: 0.85rem; color: #64748b; font-weight: 500;">
                        Hiển thị {{ $users->firstItem() }} đến {{ $users->lastItem() }} của {{ $users->total() }} kết quả
                    </div>
                    <div class="custom-pagination">
                        {{ $users->withQueryString()->links() }}
                    </div>
                </div>
            @endif

        {{-- Hidden forms for actions --}}
        @foreach($users as $user)
            @if($user->role !== 'user' && auth()->id() !== $user->id)
                <form id="form-toggle-{{ $user->id }}" action="{{ route('admin.users.toggle-status', $user) }}" method="POST" style="display: none;">
                    @csrf @method('PATCH')
                </form>
                <form id="form-reset-{{ $user->id }}" action="{{ route('admin.users.reset-password', $user) }}" method="POST" style="display: none;">
                    @csrf @method('PATCH')
                </form>
                @if($user->role === 'staff')
                <form id="form-destroy-{{ $user->id }}" action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: none;">
                    @csrf @method('DELETE')
                </form>
                @endif
            @endif
        @endforeach

        {{-- Custom Reset Password Modal --}}
        <div id="resetConfirmModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 999999; justify-content: center; align-items: center; padding: 1.5rem; opacity: 0; transition: opacity 0.3s ease;">
            <div style="background: #fff; width: 400px; max-width: 95vw; border-radius: 1.25rem; box-shadow: 0 20px 40px rgba(0,0,0,0.1); overflow: hidden; transform: scale(0.95); transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
                <div style="padding: 2rem 1.5rem 1.5rem; text-align: center;">
                    <div style="width: 56px; height: 56px; background: #fffbeb; color: #d97706; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin: 0 auto 1.25rem;">
                        🔑
                    </div>
                    <h3 style="margin: 0 0 0.5rem; font-size: 1.25rem; font-weight: 800; color: #1e293b;">Đặt lại mật khẩu</h3>
                    <p style="margin: 0; font-size: 0.95rem; color: #64748b; line-height: 1.5;">
                        Mật khẩu của tài khoản <strong id="resetUserName" style="color: #1e293b;"></strong> sẽ được đưa về mật khẩu mặc định. Hành động này không thể hoàn tác.
                    </p>
                </div>
                <div style="background: #f8fafc; padding: 1rem 1.5rem; display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" onclick="closeResetConfirm()" style="padding: 0.65rem 1.25rem; background: #fff; border: 1px solid #e2e8f0; border-radius: 0.75rem; font-weight: 700; color: #64748b; cursor: pointer; transition: background 0.2s; font-size: 0.9rem;">
                        Hủy bỏ
                    </button>
                    <button type="button" id="confirmResetBtn" style="padding: 0.65rem 1.25rem; background: #d97706; border: none; border-radius: 0.75rem; font-weight: 700; color: #fff; cursor: pointer; transition: background 0.2s; font-size: 0.9rem; box-shadow: 0 4px 10px rgba(217, 119, 6, 0.2);">
                        Xác nhận đặt lại
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.25rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: #fff;
    padding: 1.25rem;
    border-radius: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    border: 1px solid rgba(241, 245, 249, 0.8);
}

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stat-title-group {
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.stat-icon-outline {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 1.5px solid #3b82f6;
    background: #eff6ff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: #3b82f6;
}

.stat-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: #1e293b;
}

.stat-body {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.stat-value {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 850;
    color: #1e293b;
    line-height: 1;
}

.stat-footer {
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 500;
    padding-top: 0.25rem;
}

/* Main Content Card (mimicking patient index) */
.main-content-card {
    background: #fff;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.01);
    border: 1px solid #f1f5f9;
}

.filter-form {
    margin-bottom: 1.5rem;
}

.filter-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.search-input-group {
    position: relative;
    width: 320px;
}

.search-input-group input {
    width: 100%;
    padding: 0.65rem 1rem 0.65rem 2.25rem;
    border-radius: 0.5rem;
    border: 1px solid #e2e8f0;
    font-size: 0.85rem;
    outline: none;
    transition: border-color 0.2s;
}

.search-input-group input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.search-input-group .icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 0.95rem;
}

.btn-action {
    padding: 0.35rem 0.65rem;
    border-radius: 0.4rem;
    border: 1px solid;
    font-size: 0.75rem;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-action:hover {
    filter: brightness(0.95);
    transform: scale(1.02);
}

.table-container {
    overflow-x: auto;
    border-radius: 0.5rem;
    border: 1px solid #f1f5f9;
}

.data-table th {
    background-color: #f8fafc;
    color: #475569;
    font-weight: 800;
    padding: 0.85rem 1rem;
    border-bottom: 2px solid #e2e8f0;
    text-align: left;
}

.data-table td {
    border-bottom: 1px solid #f1f5f9;
}

.data-table tr:hover {
    background: #f8fafc;
}

/* Pagination Overrides for Laravel */
.custom-pagination nav {
    display: flex;
    align-items: center;
}
.custom-pagination nav > div:first-child {
    display: none;
}
.custom-pagination svg {
    width: 14px;
    height: 14px;
}
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        let searchTimeout = null;

        if (searchInput) {
            // Đưa con trỏ vào cuối chữ trong ô search khi trang load (nếu có chữ)
            if (searchInput.value) {
                const len = searchInput.value.length;
                searchInput.focus();
                searchInput.setSelectionRange(len, len);
            }

            // Gõ xong tự lọc sau 600ms
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 600);
            });
            
            // Xóa chữ khi nhấn ESC và tự động reload
            searchInput.addEventListener('keydown', function(e) {
                if(e.key === 'Escape') {
                    this.value = '';
                    document.getElementById('filterForm').submit();
                }
            });
        }
    });

    // Modal Logic
    let currentResetFormId = null;

    function showResetConfirm(userId, userName) {
        currentResetFormId = 'form-reset-' + userId;
        document.getElementById('resetUserName').textContent = userName;
        
        const modal = document.getElementById('resetConfirmModal');
        modal.style.display = 'flex';
        // Trigger reflow for animation
        void modal.offsetWidth;
        modal.style.opacity = '1';
        modal.children[0].style.transform = 'scale(1)';
    }

    function closeResetConfirm() {
        const modal = document.getElementById('resetConfirmModal');
        modal.style.opacity = '0';
        modal.children[0].style.transform = 'scale(0.95)';
        setTimeout(() => {
            modal.style.display = 'none';
            currentResetFormId = null;
        }, 300);
    }

    document.getElementById('confirmResetBtn').addEventListener('click', function() {
        if (currentResetFormId) {
            document.getElementById(currentResetFormId).submit();
        }
    });
</script>
@endpush
@endsection
