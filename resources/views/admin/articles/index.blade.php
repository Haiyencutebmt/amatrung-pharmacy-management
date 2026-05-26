@extends('layouts.admin')

@section('title', 'Bài Viết — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div class="icon-bg" style="width: 44px; height: 44px; background: #eef9ee; color: #5eb542; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
        <span class="icon">📰</span>
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Quản lý bài viết</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Quản lý và xuất bản các bài viết, tin tức trên hệ thống</p>
    </div>
</div>
@endsection

@section('content')
<div class="record-index-container" style="margin-top: -1rem;">

    <div class="main-content-card">
        <form action="{{ route('admin.articles.index') }}" method="GET" class="filter-form">
            <div class="filter-row">
                <div style="display: flex; gap: 1.5rem; align-items: flex-end; flex: 1;">
                    <div class="filter-item" style="flex-direction: column; align-items: flex-start; gap: 0.5rem;">
                        <label style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Tìm bài viết</label>
                        <div class="search-input-group" style="width: 350px;">
                            <span class="icon">🔍</span>
                            <input type="text" name="search" placeholder="Tìm tiêu đề bài viết..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="action-buttons" style="margin-bottom: 2px;">
                        <button type="submit" class="btn-filter">
                            <span class="icon">🔍</span> Lọc
                        </button>
                        <a href="{{ route('admin.articles.index') }}" class="btn-reset-box">
                            <span class="icon">🔄</span> Reset
                        </a>
                    </div>
                </div>
                
                <div class="action-buttons" style="margin-bottom: 2px;">
                    <a href="{{ route('admin.articles.create') }}" class="btn-add">
                        <span class="icon">+</span> Viết Bài Mới
                    </a>
                </div>
            </div>
        </form>

        <div class="table-container">
            <table class="patient-table">
                <thead>
                    <tr>
                        <th>TIÊU ĐỀ</th>
                        <th>TÁC GIẢ</th>
                        <th>NGÀY ĐĂNG</th>
                        <th>BÌNH LUẬN</th>
                        <th>TRẠNG THÁI</th>
                        <th>HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $article)
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: var(--text-dark); max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $article->title }}">
                                <a href="{{ route('articles.show', $article->slug) }}" target="_blank" style="color: #1e3a5f; text-decoration: none;">
                                    {{ $article->title }}
                                </a>
                            </div>
                        </td>
                        <td><span style="font-weight: 600; color: #475569;">{{ $article->author->name ?? 'N/A' }}</span></td>
                        <td><span style="font-weight: 600; color: #64748b;">{{ $article->published_at ? $article->published_at->format('d/m/Y H:i') : '---' }}</span></td>
                        <td>
                            <a href="{{ route('admin.comments.index', ['search' => $article->title]) }}" style="background: #e0f2fe; color: #0369a1; padding: 0.3rem 0.6rem; border-radius: 0.4rem; font-weight: 700; font-size: 0.75rem; text-decoration: none;">
                                {{ $article->comments_count }} 💬
                            </a>
                        </td>
                        <td>
                            @if($article->is_published)
                                <span style="background: #eef9ee; color: #16a34a; padding: 0.3rem 0.75rem; border-radius: 0.5rem; font-weight: 800; font-size: 0.75rem;">Đã xuất bản</span>
                            @else
                                <span style="background: #f1f5f9; color: #64748b; padding: 0.3rem 0.75rem; border-radius: 0.5rem; font-weight: 800; font-size: 0.75rem;">Bản nháp</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-cell">
                                <a href="{{ route('admin.articles.edit', $article) }}" class="btn-icon edit" title="Sửa">
                                    <span class="icon">✏️</span> Sửa
                                </a>
                                <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này? Tất cả bình luận liên quan cũng sẽ bị xóa.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon delete" title="Xóa">
                                        <span class="icon">🗑️</span> Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #64748b; padding: 3rem 1rem; font-weight: 500;">
                            Không tìm thấy bài viết nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($articles->hasPages())
        <div class="pagination-area">
            <p class="summary">Hiển thị {{ $articles->firstItem() }} đến {{ $articles->lastItem() }} của {{ $articles->total() }} bài viết</p>
            <div class="pagination-controls">
                {{ $articles->withQueryString()->links() }}
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
