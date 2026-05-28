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
                        <th class="article-select-col">
                            <input type="checkbox" id="articleSelectAll" class="article-check" aria-label="Chọn tất cả bài viết">
                        </th>
                        <th>TIÊU ĐỀ</th>
                        <th>DANH MỤC</th>
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
                        <td class="article-select-col">
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
                            <div style="font-weight: 700; color: var(--text-dark); max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $article->title }}">
                                <a href="{{ route('articles.show', $article->slug) }}" target="_blank" style="color: #1e3a5f; text-decoration: none;">
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
    background: #2563eb;
    color: #fff;
    text-decoration: none;
    font-weight: 700;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.article-alert {
    margin-bottom: 1rem;
    padding: 0.85rem 1rem;
    border-radius: 0.75rem;
    font-weight: 700;
    font-size: 0.9rem;
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
    border-radius: 0.85rem;
    color: #9a3412;
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    padding: 0.9rem 1rem;
}

.article-bulk-delete-bar[hidden] {
    display: none;
}

.article-bulk-delete-bar strong {
    display: block;
    color: #7c2d12;
    font-size: 0.95rem;
}

.article-bulk-delete-bar span {
    color: #9a3412;
    font-size: 0.82rem;
    font-weight: 600;
}

.article-bulk-delete-button {
    align-items: center;
    background: #ef4444;
    border: 0;
    border-radius: 0.7rem;
    color: #fff;
    cursor: pointer;
    display: flex;
    font-weight: 800;
    gap: 0.45rem;
    padding: 0.75rem 1rem;
}

.article-bulk-delete-button:hover {
    background: #dc2626;
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

.patient-table tr.is-selected td {
    background: #fff7ed;
}

.article-select-col {
    width: 42px;
    text-align: center !important;
}

.article-check {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #ef4444;
}

.article-category-badge {
    border-radius: 999px;
    display: inline-flex;
    font-size: 0.75rem;
    font-weight: 850;
    line-height: 1.25;
    max-width: 190px;
    padding: 0.4rem 0.7rem;
    white-space: normal;
}

.article-category-badge.duoc-lieu-bai-thuoc {
    background: #ecfdf5;
    color: #047857;
}

.article-category-badge.benh-hoc-phuong-phap-dieu-tri {
    background: #fef2f2;
    color: #dc2626;
}

.article-category-badge.cham-soc-suc-khoe-duong-sinh {
    background: #eef2ff;
    color: #4338ca;
}

.article-category-badge.tin-tuc-phong-kham {
    background: #eff6ff;
    color: #2563eb;
}

.article-category-badge.y-hoc-hien-dai-ket-hop {
    background: #fdf4ff;
    color: #a21caf;
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
    border-radius: 1rem;
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
    font-size: 1.25rem;
    font-weight: 850;
    margin: 0 0 0.5rem;
}

.article-delete-dialog p {
    color: #64748b;
    font-size: 0.92rem;
    font-weight: 600;
    margin: 0.35rem 0;
}

.article-delete-list {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    color: #334155;
    font-size: 0.85rem;
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
    border-radius: 0.7rem;
    cursor: pointer;
    font-weight: 800;
    padding: 0.75rem 1rem;
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
    background: #2563eb;
    color: #fff;
    border-color: #2563eb;
}
</style>

<script>
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
