@extends('layouts.admin')

@section('title', 'Quản lý Bình Luận — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div class="icon-bg" style="width: 44px; height: 44px; background: #eff6ff; color: #2563eb; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
        <span class="icon">💬</span>
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Quản lý bình luận</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Kiểm duyệt và quản lý phản hồi của người dùng trên hệ thống</p>
    </div>
</div>
@endsection

@section('content')
<div class="record-index-container" style="margin-top: -1rem;">

    {{-- Stats Cards --}}
    @php
        $totalComments = \App\Models\Comment::count();
        $approvedComments = \App\Models\Comment::where('is_approved', true)->count();
        $pendingComments = \App\Models\Comment::where('is_approved', false)->count();
    @endphp

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline" style="border-color: #2563eb; color: #2563eb; background: #eff6ff;">
                        <span class="icon">💬</span>
                    </div>
                    <span class="stat-title">Tổng Bình Luận</span>
                </div>
                <div class="stat-menu-wrapper">
                    <div class="stat-menu" onclick="toggleStatDropdown(this)">⋮</div>
                    <div class="stat-dropdown">
                        <a href="{{ route('admin.comments.index') }}">Xem tất cả</a>
                    </div>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value">{{ number_format($totalComments) }}</h3>
                <span class="stat-badge bg-green-light">Toàn bộ</span>
            </div>
            <div class="stat-footer">
                Tất cả bình luận trên hệ thống
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline" style="border-color: #10b981; color: #10b981; background: #ecfdf5;">
                        <span class="icon">🟢</span>
                    </div>
                    <span class="stat-title">Đã Duyệt</span>
                </div>
                <div class="stat-menu-wrapper">
                    <div class="stat-menu" onclick="toggleStatDropdown(this)">⋮</div>
                    <div class="stat-dropdown">
                        <a href="{{ route('admin.comments.index') }}?status=1">Xem đã duyệt</a>
                    </div>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value">{{ number_format($approvedComments) }}</h3>
                <span class="stat-badge" style="background: #ecfdf5; color: #10b981; border: 1px solid #a7f3d0;">Đã duyệt</span>
            </div>
            <div class="stat-footer">
                Bình luận hiển thị công khai dưới bài viết
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline" style="border-color: #d97706; color: #d97706; background: #fffbeb;">
                        <span class="icon">⏳</span>
                    </div>
                    <span class="stat-title">Chờ Duyệt</span>
                </div>
                <div class="stat-menu-wrapper">
                    <div class="stat-menu" onclick="toggleStatDropdown(this)">⋮</div>
                    <div class="stat-dropdown">
                        <a href="{{ route('admin.comments.index') }}?status=0">Xem chờ duyệt</a>
                    </div>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value">{{ number_format($pendingComments) }}</h3>
                <span class="stat-badge" style="background: #fffbeb; color: #d97706; border: 1px solid #fde68a;">Cần xử lý</span>
            </div>
            <div class="stat-footer">
                Bình luận đang đợi quản trị viên phê duyệt
            </div>
        </div>
    </div>

    <div class="main-content-card">
        <form action="{{ route('admin.comments.index') }}" method="GET" class="filter-form">
            <div class="filter-row">
                <div class="search-input-group" style="width: 400px;">
                    <button type="submit" class="icon" title="Tìm kiếm" style="background: none; border: none; cursor: pointer; padding: 0; outline: none; position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.9rem;">🔍</button>
                    <input type="text" name="search" placeholder="Tìm nội dung, người dùng, bài viết..." value="{{ request('search') }}">
                </div>

                <div class="action-buttons" style="display: flex; gap: 0.5rem; align-items: center;">
                    <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Trạng thái:</span>
                    <select name="status" onchange="this.form.submit()" style="padding: 0.5rem 1rem; border-radius: 0.25rem; border: 1px solid var(--border-color); background: #fff; font-size: 0.85rem; font-weight: 600; color: var(--text-dark); cursor: pointer;">
                        <option value="">Tất cả</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đã duyệt</option>
                    </select>

                    <button type="submit" class="btn-filter">
                        <span class="icon">🔍</span> Lọc
                    </button>
                    <a href="{{ route('admin.comments.index') }}" class="btn-reset-box">
                        <span class="icon">🔄</span> Reset
                    </a>
                </div>
            </div>
        </form>

        <div class="table-container">
            <table class="patient-table">
                <thead>
                    <tr>
                        <th style="width: 200px;">NGƯỜI BÌNH LUẬN</th>
                        <th style="min-width: 250px;">NỘI DUNG</th>
                        <th style="width: 220px;">BÀI VIẾT</th>
                        <th style="width: 140px;">NGÀY GỬI</th>
                        <th style="width: 110px; text-align: center;">TRẠNG THÁI</th>
                        <th style="width: 160px; text-align: center;">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($comments as $comment)
                    <tr>
                        <td>
                            <div class="patient-name-cell">
                                <div class="avatar-circle" style="background: {{ '#' . substr(md5($comment->user->name ?? 'User'), 0, 6) }}20; color: {{ '#' . substr(md5($comment->user->name ?? 'User'), 0, 6) }}; font-weight: 800;">
                                    {{ mb_substr($comment->user->name ?? 'U', 0, 1) }}
                                </div>
                                <div class="name" style="font-weight: 700; color: #0f172a;">{{ $comment->user->name ?? 'Người dùng' }}</div>
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 0.82rem; color: #334155; line-height: 1.4; background: #f8fafc; padding: 0.4rem 0.6rem; border-radius: 0.25rem; border: 1px solid #e2e8f0; word-break: break-word;">
                                {{ Str::limit($comment->content, 150) }}
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('articles.show', $comment->article->slug ?? '') }}#comments" target="_blank" style="color: #2563eb; text-decoration: none; font-weight: 600;">
                                {{ Str::limit($comment->article->title ?? 'N/A', 40) }}
                            </a>
                        </td>
                        <td style="font-weight: 600; color: #475569;">
                            {{ $comment->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td style="text-align: center;">
                            @if($comment->is_approved)
                                <span style="background: #ecfdf5; color: #16a34a; border: 1px solid #bbf7d0; padding: 0.15rem 0.4rem; border-radius: 0.25rem; font-weight: 700; font-size: 0.75rem; display: inline-block;">Đã duyệt</span>
                            @else
                                <span style="background: #fffbeb; color: #d97706; border: 1px solid #fde68a; padding: 0.15rem 0.4rem; border-radius: 0.25rem; font-weight: 700; font-size: 0.75rem; display: inline-block;">Chờ duyệt</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 0.4rem; justify-content: center; align-items: center;">
                                @if($comment->is_approved)
                                    <form action="{{ route('admin.comments.update', $comment) }}" method="POST" style="margin: 0; width: 50%;">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="is_approved" value="0">
                                        <button type="submit" class="btn-icon edit" style="width: 100%; justify-content: center; border-color: #cbd5e1; color: #475569;" title="Bỏ duyệt">
                                            Bỏ duyệt
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.comments.update', $comment) }}" method="POST" style="margin: 0; width: 50%;">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="is_approved" value="1">
                                        <button type="submit" class="btn-icon edit" style="width: 100%; justify-content: center; border-color: #bfdbfe; color: #2563eb;" title="Duyệt">
                                            Duyệt
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bình luận này?');" style="margin: 0; width: 50%;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon delete" style="width: 100%; justify-content: center; border-color: #fecaca; color: #ef4444;" title="Xóa">
                                        <span class="icon">🗑️</span> Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #64748b; padding: 3rem 1rem; font-weight: 500;">
                            Không tìm thấy bình luận nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($comments->hasPages())
        <div class="pagination-area">
            <p class="summary">Hiển thị {{ $comments->firstItem() }} đến {{ $comments->lastItem() }} của {{ $comments->total() }} bình luận</p>
            <div class="pagination-controls">
                {{ $comments->withQueryString()->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.record-index-container {
    --primary-green: #2563eb;
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
    background: #eff6ff;
    color: #2563eb;
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

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: #fff;
    padding: 1.25rem 1.5rem;
    border-radius: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
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
    gap: 0.75rem;
}

.stat-icon-outline {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1.5px solid var(--primary-green);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: var(--primary-green);
}

.stat-title {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--text-dark);
}

.stat-menu-wrapper {
    position: relative;
}

.stat-menu {
    color: #cbd5e1;
    font-size: 1.25rem;
    font-weight: 800;
    cursor: pointer;
    line-height: 1;
    padding: 0 0.5rem;
    user-select: none;
}
.stat-menu:hover {
    color: var(--text-dark);
}

.stat-dropdown {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    background: #fff;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    border-radius: 0.75rem;
    padding: 0.5rem;
    min-width: 160px;
    z-index: 10;
    border: 1px solid #f1f5f9;
}

.stat-dropdown.show {
    display: flex;
    flex-direction: column;
    animation: fadeIn 0.2s ease;
}

.stat-dropdown a {
    text-decoration: none;
    color: var(--text-dark);
    font-size: 0.85rem;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    transition: background 0.2s;
    font-weight: 600;
    white-space: nowrap;
}

.stat-dropdown a:hover {
    background: #f8fafc;
    color: var(--primary-green);
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

.stat-body {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.stat-value {
    margin: 0;
    font-size: 2rem;
    font-weight: 850;
    color: var(--text-dark);
    line-height: 1;
}

.stat-badge {
    padding: 0.2rem 0.5rem;
    border-radius: 2rem;
    font-size: 0.75rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.2rem;
}

.bg-green-light {
    background: #eff6ff;
    color: #2563eb;
    border: 1px solid #dbeafe;
}

.stat-footer {
    font-size: 0.8rem;
    color: var(--text-muted);
    font-weight: 500;
    padding-top: 0.5rem;
}

.stat-footer strong {
    color: var(--text-dark);
    font-weight: 750;
}

.main-content-card {
    background: #fff;
    border-radius: 0.5rem;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.01);
}

.filter-form {
    margin-bottom: 2rem;
}

.filter-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.search-input-group {
    position: relative;
    width: 400px;
}

.search-input-group input {
    width: 100%;
    padding: 0.8rem 1rem 0.8rem 2.75rem;
    border-radius: 0.25rem;
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
    gap: 0.5rem;
    align-items: center;
}

.btn-filter {
    padding: 0.75rem 1.5rem;
    border-radius: 0.25rem;
    border: 1px solid #cbd5e1;
    background: #fff;
    color: #475569;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
}

.btn-filter:hover {
    background: #f8fafc;
    border-color: #94a3b8;
    color: #0f172a;
}

.btn-reset-box {
    padding: 0.75rem 1.5rem;
    border-radius: 0.25rem;
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

/* Excel Spreadsheet Table Styling */
.table-container {
    overflow-x: auto;
    border: 1px solid #cbd5e1;
    border-radius: 0.25rem;
    background: #fff;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
}

.patient-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.82rem;
    color: #334155;
    font-family: 'Inter', system-ui, sans-serif;
}

.patient-table th {
    text-align: left;
    padding: 0.5rem 0.75rem;
    font-size: 0.78rem;
    font-weight: 700;
    color: #475569;
    background-color: #f1f5f9;
    border: 1px solid #cbd5e1;
    letter-spacing: 0.02em;
    text-transform: uppercase;
}

.patient-table td {
    padding: 0.5rem 0.75rem;
    border: 1px solid #e2e8f0;
    background-color: #fff;
    vertical-align: middle;
}

.patient-table tr:nth-child(even) td {
    background-color: #f8fafc;
}

.patient-table tr:hover td {
    background-color: #f1f5f9;
}

.patient-table td a:hover {
    color: #2563eb !important;
    text-decoration: underline !important;
}

.patient-name-cell {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.85rem;
}

.patient-name-cell .name {
    font-weight: 700;
    color: #0f172a;
}

.btn-icon {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    border: 1px solid #cbd5e1;
    background: #fff;
    color: #475569;
    transition: all 0.2s;
    cursor: pointer;
}

.btn-icon.edit:hover {
    background: #eff6ff;
    border-color: #3b82f6;
    color: #2563eb;
}

.btn-icon.delete:hover {
    background: #fef2f2;
    border-color: #f87171;
    color: #ef4444;
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
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.25rem;
    background: #fff;
    border: 1px solid var(--border-color);
    color: var(--text-dark);
    text-decoration: none;
    font-weight: 700;
    font-size: 0.8rem;
}

.page-item.active .page-link {
    background: #2563eb;
    color: #fff;
    border-color: #2563eb;
}
</style>

<script>
function toggleStatDropdown(el) {
    el.nextElementSibling.classList.toggle('show');
}
window.onclick = function(event) {
    if (!event.target.matches('.stat-menu')) {
        document.querySelectorAll('.stat-dropdown').forEach(d => d.classList.remove('show'));
    }
}
</script>
@endsection
