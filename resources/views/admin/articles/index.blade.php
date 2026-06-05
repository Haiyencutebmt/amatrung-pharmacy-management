@extends('layouts.admin')

@section('title', 'Bài Viết — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div class="icon-bg" style="width: 44px; height: 44px; background: #eff6ff; color: #2563eb; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
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

    {{-- Stats Cards --}}
    @php
        $totalArticles = \App\Models\Article::count();
        $publishedArticles = \App\Models\Article::where('is_published', true)->count();
        $draftArticles = \App\Models\Article::where('is_published', false)->count();
    @endphp

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline" style="border-color: #2563eb; color: #2563eb; background: #eff6ff;">
                        <span class="icon">📰</span>
                    </div>
                    <span class="stat-title">Tổng Bài Viết</span>
                </div>
                <div class="stat-menu-wrapper">
                    <div class="stat-menu" onclick="toggleStatDropdown(this)">⋮</div>
                    <div class="stat-dropdown">
                        <a href="{{ route('admin.articles.index') }}">Xem tất cả</a>
                    </div>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value">{{ number_format($totalArticles) }}</h3>
                <span class="stat-badge bg-green-light">Toàn bộ</span>
            </div>
            <div class="stat-footer">
                Tất cả bài viết trên hệ thống
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline" style="border-color: #10b981; color: #10b981; background: #ecfdf5;">
                        <span class="icon">🟢</span>
                    </div>
                    <span class="stat-title">Đã Xuất Bản</span>
                </div>
                <div class="stat-menu-wrapper">
                    <div class="stat-menu" onclick="toggleStatDropdown(this)">⋮</div>
                    <div class="stat-dropdown">
                        <a href="{{ route('admin.articles.index') }}">Xem bài viết</a>
                    </div>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value">{{ number_format($publishedArticles) }}</h3>
                <span class="stat-badge" style="background: #ecfdf5; color: #10b981; border: 1px solid #a7f3d0;">Hiển thị</span>
            </div>
            <div class="stat-footer">
                Bài viết hiển thị công khai trên trang chủ
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline" style="border-color: #64748b; color: #64748b; background: #f1f5f9;">
                        <span class="icon">📝</span>
                    </div>
                    <span class="stat-title">Bản Nháp</span>
                </div>
                <div class="stat-menu-wrapper">
                    <div class="stat-menu" onclick="toggleStatDropdown(this)">⋮</div>
                    <div class="stat-dropdown">
                        <a href="{{ route('admin.articles.index') }}">Xem bản nháp</a>
                    </div>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value">{{ number_format($draftArticles) }}</h3>
                <span class="stat-badge" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;">Lưu nháp</span>
            </div>
            <div class="stat-footer">
                Bài viết chưa được xuất bản chính thức
            </div>
        </div>
    </div>

    <div class="main-content-card">
        <form action="{{ route('admin.articles.index') }}" method="GET" class="filter-form">
            <div class="filter-row">
                <div class="search-input-group" style="width: 400px;">
                    <button type="submit" class="icon" title="Tìm kiếm" style="background: none; border: none; cursor: pointer; padding: 0; outline: none; position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.9rem;">🔍</button>
                    <input type="text" name="search" placeholder="Tìm tiêu đề bài viết... (Nhấn Enter)" value="{{ request('search') }}">
                </div>
                
                <div class="action-buttons" style="display: flex; gap: 0.5rem; align-items: center;">
                    <button type="submit" class="btn-filter">
                        <span class="icon">🔍</span> Lọc
                    </button>
                    <a href="{{ route('admin.articles.index') }}" class="btn-reset-box">
                        <span class="icon">🔄</span> Reset
                    </a>
                    
                    <a href="{{ route('admin.articles.create') }}" class="btn-add">
                        <span class="icon">+</span> Viết Bài Mới
                    </a>
                </div>
            </div>
        </form>

        @if(session('success'))
            <div class="article-alert success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="article-alert danger">{{ $errors->first() }}</div>
        @endif

        <form id="articleBulkDeleteForm" action="{{ route('admin.articles.bulk-destroy') }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

        <div class="article-bulk-delete-bar" id="articleBulkDeleteBar" hidden>
            <div>
                <strong id="articleSelectedCount">Đã chọn 0 bài viết</strong>
                <span>Chỉ xóa khi bạn xác nhận ở bước tiếp theo.</span>
            </div>
            <button type="button" class="article-bulk-delete-button" id="openArticleDeleteModal">
                <span class="icon">🗑️</span> Xóa đã chọn
            </button>
        </div>

        @php
            $resolveArticleCategory = function ($article) use ($articleCategories) {
                $text = mb_strtolower(($article->title ?? '') . ' ' . ($article->slug ?? ''), 'UTF-8');

                $categoryKey = $article->category;

                if (!$categoryKey) {
                    if (str_contains($text, 'dạ dày') || str_contains($text, 'da-day') || str_contains($text, 'bệnh')) {
                        $categoryKey = 'benh-hoc-phuong-phap-dieu-tri';
                    } elseif (str_contains($text, 'ngũ hành') || str_contains($text, 'ngu-hanh') || str_contains($text, 'dưỡng sinh')) {
                        $categoryKey = 'cham-soc-suc-khoe-duong-sinh';
                    } elseif (str_contains($text, 'hiện đại') || str_contains($text, 'hien-dai')) {
                        $categoryKey = 'y-hoc-hien-dai-ket-hop';
                    } elseif (str_contains($text, 'phòng khám') || str_contains($text, 'phong-kham') || str_contains($text, 'tin tức')) {
                        $categoryKey = 'tin-tuc-phong-kham';
                    } else {
                        $categoryKey = 'duoc-lieu-bai-thuoc';
                    }
                }

                return [
                    'label' => $articleCategories[$categoryKey] ?? $articleCategories['duoc-lieu-bai-thuoc'],
                    'class' => $categoryKey,
                ];
            };
        @endphp

        <div class="table-container">
            <table class="patient-table">
                <thead>
                    <tr>
                        <th class="article-select-col" style="width: 40px; text-align: center;">
                            <input type="checkbox" id="articleSelectAll" class="article-check" aria-label="Chọn tất cả bài viết">
                        </th>
                        <th>TIÊU ĐỀ</th>
                        <th style="width: 200px;">DANH MỤC</th>
                        <th style="width: 140px;">TÁC GIẢ</th>
                        <th style="width: 140px;">NGÀY ĐĂNG</th>
                        <th style="width: 100px; text-align: center;">BÌNH LUẬN</th>
                        <th style="width: 120px; text-align: center;">TRẠNG THÁI</th>
                        <th style="width: 90px; text-align: center;">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $article)
                    <tr>
                        <td class="article-select-col" style="text-align: center;">
                            <input
                                type="checkbox"
                                name="ids[]"
                                value="{{ $article->id }}"
                                form="articleBulkDeleteForm"
                                class="article-check article-row-check"
                                data-article-title="{{ $article->title }}"
                                aria-label="Chọn bài viết {{ $article->title }}"
                            >
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #0f172a; max-width: 380px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $article->title }}">
                                <a href="{{ route('articles.show', $article->slug) }}" target="_blank" style="color: #0f172a; text-decoration: none;">
                                    {{ $article->title }}
                                </a>
                            </div>
                        </td>
                        @php($category = $resolveArticleCategory($article))
                        <td>
                            <span class="article-category-badge {{ $category['class'] }}">
                                {{ $category['label'] }}
                            </span>
                        </td>
                        <td style="font-weight: 600; color: #475569;">{{ $article->author->name ?? 'N/A' }}</td>
                        <td style="font-weight: 600; color: #475569;">{{ $article->published_at ? $article->published_at->format('d/m/Y H:i') : '---' }}</td>
                        <td style="text-align: center;">
                            <a href="{{ route('admin.comments.index', ['search' => $article->title]) }}" style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; padding: 0.15rem 0.4rem; border-radius: 0.25rem; font-weight: 700; font-size: 0.75rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem; justify-content: center;">
                                {{ $article->comments_count }} <span style="font-size: 0.8rem;">💬</span>
                            </a>
                        </td>
                        <td style="text-align: center;">
                            @if($article->is_published)
                                <span style="background: #ecfdf5; color: #16a34a; border: 1px solid #bbf7d0; padding: 0.15rem 0.4rem; border-radius: 0.25rem; font-weight: 700; font-size: 0.75rem; display: inline-block;">Đã xuất bản</span>
                            @else
                                <span style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; padding: 0.15rem 0.4rem; border-radius: 0.25rem; font-weight: 700; font-size: 0.75rem; display: inline-block;">Bản nháp</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <div class="action-cell" style="justify-content: center;">
                                <a href="{{ route('admin.articles.edit', $article) }}" class="btn-icon edit" title="Sửa">
                                    <span class="icon">✏️</span> Sửa
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: #64748b; padding: 3rem 1rem; font-weight: 500;">
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

<div class="article-delete-modal" id="articleDeleteModal" hidden>
    <div class="article-delete-dialog" role="dialog" aria-modal="true" aria-labelledby="articleDeleteTitle">
        <div class="article-delete-icon">🗑️</div>
        <h2 id="articleDeleteTitle">Xác nhận xóa bài viết</h2>
        <p id="articleDeleteSummary">Bạn đang chọn 0 bài viết.</p>
        <div class="article-delete-list" id="articleDeleteList"></div>
        <p class="article-delete-warning">Sau khi xóa, bài viết và dữ liệu liên quan sẽ không còn hiển thị trong danh sách quản trị.</p>
        <div class="article-delete-actions">
            <button type="button" class="article-modal-cancel" id="cancelArticleDelete">Hủy</button>
            <button type="button" class="article-modal-confirm" id="confirmArticleDelete">Xác nhận xóa</button>
        </div>
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

.btn-add {
    padding: 0.75rem 1.5rem;
    border-radius: 0.25rem;
    background: #2563eb;
    color: #fff;
    text-decoration: none;
    font-weight: 700;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.15);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.article-alert {
    margin-bottom: 1rem;
    padding: 0.75rem 1rem;
    border-radius: 0.25rem;
    font-weight: 700;
    font-size: 0.85rem;
}

.article-alert.success {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #bbf7d0;
}

.article-alert.danger {
    background: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fecaca;
}

.article-bulk-delete-bar {
    align-items: center;
    background: #fff7ed;
    border: 1px solid #fed7aa;
    border-radius: 0.25rem;
    color: #9a3412;
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    padding: 0.5rem 0.75rem;
}

.article-bulk-delete-bar[hidden] {
    display: none;
}

.article-bulk-delete-bar strong {
    display: block;
    color: #7c2d12;
    font-size: 0.85rem;
}

.article-bulk-delete-bar span {
    color: #9a3412;
    font-size: 0.75rem;
    font-weight: 600;
}

.article-bulk-delete-button {
    align-items: center;
    background: #ef4444;
    border: 0;
    border-radius: 0.25rem;
    color: #fff;
    cursor: pointer;
    display: flex;
    font-weight: 800;
    gap: 0.45rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.8rem;
}

.article-bulk-delete-button:hover {
    background: #dc2626;
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

.patient-table tr.is-selected td {
    background: #fff7ed;
}

.patient-table td a:hover {
    color: #2563eb !important;
    text-decoration: underline !important;
}

.article-select-col {
    width: 40px;
    text-align: center !important;
}

.article-check {
    width: 1.1rem;
    height: 1.1rem;
    cursor: pointer;
}

#articleSelectAll {
    accent-color: #2563eb;
}

.article-row-check {
    accent-color: #ef4444;
}

.article-category-badge {
    border-radius: 0.25rem;
    display: inline-flex;
    font-size: 0.75rem;
    font-weight: 700;
    line-height: 1.25;
    max-width: 190px;
    padding: 0.15rem 0.4rem;
    white-space: normal;
    border: 1px solid transparent;
}

.article-category-badge.duoc-lieu-bai-thuoc {
    background: #ecfdf5;
    color: #047857;
    border-color: #a7f3d0;
}

.article-category-badge.benh-hoc-phuong-phap-dieu-tri {
    background: #fef2f2;
    color: #dc2626;
    border-color: #fecaca;
}

.article-category-badge.cham-soc-suc-khoe-duong-sinh {
    background: #eef2ff;
    color: #4338ca;
    border-color: #c7d2fe;
}

.article-category-badge.tin-tuc-phong-kham {
    background: #eff6ff;
    color: #2563eb;
    border-color: #bfdbfe;
}

.article-category-badge.y-hoc-hien-dai-ket-hop {
    background: #fdf4ff;
    color: #a21caf;
    border-color: #f5d0fe;
}

.action-cell {
    display: flex;
    gap: 0.5rem;
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
}

.btn-icon.edit { color: #2563eb; }

.btn-icon:hover {
    background: #f8fafc;
    border-color: #94a3b8;
    color: #0f172a;
}

.article-delete-modal {
    align-items: center;
    background: rgba(15, 23, 42, 0.48);
    bottom: 0;
    display: flex;
    justify-content: center;
    left: 0;
    padding: 1.5rem;
    position: fixed;
    right: 0;
    top: 0;
    z-index: 1100;
}

.article-delete-modal[hidden] {
    display: none;
}

.article-delete-dialog {
    background: #fff;
    border-radius: 0.5rem;
    box-shadow: 0 22px 60px rgba(15, 23, 42, 0.22);
    max-width: 480px;
    padding: 1.5rem;
    text-align: center;
    width: 100%;
}

.article-delete-icon {
    align-items: center;
    background: #fef2f2;
    border-radius: 999px;
    color: #ef4444;
    display: flex;
    font-size: 1.5rem;
    height: 56px;
    justify-content: center;
    margin: 0 auto 1rem;
    width: 56px;
}

.article-delete-dialog h2 {
    color: #1e293b;
    font-size: 1.2rem;
    font-weight: 800;
    margin: 0 0 0.5rem;
}

.article-delete-dialog p {
    color: #64748b;
    font-size: 0.85rem;
    font-weight: 600;
    margin: 0.35rem 0;
}

.article-delete-list {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.25rem;
    color: #334155;
    font-size: 0.8rem;
    font-weight: 700;
    margin: 1rem 0;
    max-height: 120px;
    overflow: auto;
    padding: 0.75rem;
    text-align: left;
}

.article-delete-warning {
    color: #b91c1c !important;
}

.article-delete-actions {
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
    margin-top: 1.25rem;
}

.article-modal-cancel,
.article-modal-confirm {
    border-radius: 0.25rem;
    cursor: pointer;
    font-weight: 800;
    padding: 0.5rem 0.75rem;
    font-size: 0.8rem;
}

.article-modal-cancel {
    background: #fff;
    border: 1px solid #cbd5e1;
    color: #475569;
}

.article-modal-confirm {
    background: #ef4444;
    border: 1px solid #ef4444;
    color: #fff;
}

.article-modal-confirm:hover {
    background: #dc2626;
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

document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('articleSelectAll');
    const rowChecks = Array.from(document.querySelectorAll('.article-row-check'));
    const bulkBar = document.getElementById('articleBulkDeleteBar');
    const selectedCount = document.getElementById('articleSelectedCount');
    const openModalButton = document.getElementById('openArticleDeleteModal');
    const modal = document.getElementById('articleDeleteModal');
    const cancelButton = document.getElementById('cancelArticleDelete');
    const confirmButton = document.getElementById('confirmArticleDelete');
    const deleteForm = document.getElementById('articleBulkDeleteForm');
    const deleteSummary = document.getElementById('articleDeleteSummary');
    const deleteList = document.getElementById('articleDeleteList');

    function selectedRows() {
        return rowChecks.filter((checkbox) => checkbox.checked);
    }

    function updateBulkState() {
        const selected = selectedRows();
        const count = selected.length;

        if (bulkBar) {
            bulkBar.hidden = count === 0;
        }

        if (selectedCount) {
            selectedCount.textContent = `Đã chọn ${count} bài viết`;
        }

        if (selectAll) {
            selectAll.checked = count > 0 && count === rowChecks.length;
            selectAll.indeterminate = count > 0 && count < rowChecks.length;
        }

        rowChecks.forEach((checkbox) => {
            checkbox.closest('tr')?.classList.toggle('is-selected', checkbox.checked);
        });
    }

    function openDeleteModal() {
        const selected = selectedRows();

        if (selected.length === 0 || !modal) {
            return;
        }

        if (deleteSummary) {
            deleteSummary.textContent = `Bạn đang chọn ${selected.length} bài viết để xóa.`;
        }

        if (deleteList) {
            deleteList.innerHTML = '';
            selected.forEach((checkbox) => {
                const item = document.createElement('div');
                item.textContent = `- ${checkbox.dataset.articleTitle || 'Bài viết đã chọn'}`;
                deleteList.appendChild(item);
            });
        }

        modal.hidden = false;
    }

    function closeDeleteModal() {
        if (modal) {
            modal.hidden = true;
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            rowChecks.forEach((checkbox) => {
                checkbox.checked = selectAll.checked;
            });
            updateBulkState();
        });
    }

    rowChecks.forEach((checkbox) => {
        checkbox.addEventListener('change', updateBulkState);
    });

    openModalButton?.addEventListener('click', openDeleteModal);
    cancelButton?.addEventListener('click', closeDeleteModal);
    modal?.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeDeleteModal();
        }
    });
    confirmButton?.addEventListener('click', function () {
        if (selectedRows().length > 0) {
            deleteForm.submit();
        }
    });

    updateBulkState();
});
</script>
@endsection
