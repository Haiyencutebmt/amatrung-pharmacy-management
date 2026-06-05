@extends('layouts.admin')

@section('title', 'Từ điển dược liệu — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display:flex;align-items:center;gap:1rem;">
    <div style="width:44px;height:44px;background:#eff6ff;color:#2563eb;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.25rem;border:1px solid #dbeafe;">🌿</div>
    <div>
        <h1 style="font-size:1.5rem;font-weight:850;color:#1e293b;margin:0;line-height:1.2;">Từ điển dược liệu</h1>
        <p style="margin:0;color:#64748b;font-size:0.85rem;font-weight:500;">Quản lý cây thuốc, hình ảnh, tác dụng và lưu ý an toàn</p>
    </div>
</div>
@endsection

@section('content')
<div class="dict-admin-page">
    <div class="dict-admin-card">
        <form action="{{ route('admin.herb-dictionary.index') }}" method="GET" class="dict-filter">
            <div>
                <label>Tìm kiếm</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên cây thuốc, tên khoa học...">
            </div>
            <div>
                <label>Trạng thái</label>
                <select name="status">
                    <option value="">Tất cả</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Bản nháp</option>
                </select>
            </div>
            <div class="dict-filter-actions">
                <button type="submit">Lọc</button>
                <a href="{{ route('admin.herb-dictionary.index') }}">Reset</a>
            </div>
            <div style="display:flex; gap:0.5rem; justify-content: flex-end;">
                <a href="{{ route('admin.herb-dictionary.export-excel') }}" class="dict-add" style="background:#10b981;">Xuất Excel</a>
                <a href="{{ route('admin.herb-dictionary.create') }}" class="dict-add">Thêm mục mới</a>
            </div>
        </form>

        <div class="dict-import-panel">
            <div class="dict-import-copy">
                <strong>Nhập danh sách bằng Excel</strong>
                <span>Hỗ trợ file .xlsx hoặc .csv. Hệ thống chỉ nhập thông tin chữ, hình ảnh có thể bổ sung bằng dấu + ở cột Hình ảnh.</span>
            </div>
            <form action="{{ route('admin.herb-dictionary.import') }}" method="POST" enctype="multipart/form-data" class="dict-import-form">
                @csrf
                <input type="file" name="dictionary_file" accept=".xlsx,.csv,.txt" required>
                <a href="{{ route('admin.herb-dictionary.download-template') }}">Tải mẫu Excel</a>
                <button type="submit">Nhập danh sách</button>
            </form>
        </div>

        @if(session('import_errors'))
            <div class="dict-import-errors">
                <strong>Có {{ count(session('import_errors')) }} dòng chưa nhập được:</strong>
                <ul>
                    @foreach(array_slice(session('import_errors'), 0, 8) as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                @if(count(session('import_errors')) > 8)
                    <span>Và {{ count(session('import_errors')) - 8 }} lỗi khác.</span>
                @endif
            </div>
        @endif

        <form id="dict-bulk-delete-form" action="{{ route('admin.herb-dictionary.bulk-destroy') }}" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>

        <div id="dict-bulk-bar" class="dict-bulk-bar">
            <div>
                <strong id="dict-selected-count">Đã chọn 0 mục</strong>
                <span>Chỉ xóa khi bạn xác nhận ở bước tiếp theo.</span>
            </div>
            <button type="button" id="dict-open-delete-modal">Xóa đã chọn</button>
        </div>

        <div class="dict-table-wrap">
            <table class="dict-table">
                <thead>
                    <tr>
                        <th class="dict-select-col">
                            <input type="checkbox" id="dict-select-all" class="dict-check" aria-label="Chọn tất cả mục trên trang này">
                        </th>
                        <th>Hình ảnh</th>
                        <th>Tên dược liệu</th>
                        <th>Thông tin nhanh</th>
                        <th>Yêu thích</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $entry)
                        <tr>
                            <td class="dict-select-col">
                                <input
                                    type="checkbox"
                                    name="ids[]"
                                    value="{{ $entry->id }}"
                                    form="dict-bulk-delete-form"
                                    class="dict-check dict-row-check"
                                    data-entry-name="{{ $entry->name }}"
                                    aria-label="Chọn xóa {{ $entry->name }}"
                                >
                            </td>
                            <td class="dict-image-cell">
                                @if($entry->primary_image_url)
                                    <img src="{{ $entry->primary_image_url }}" alt="{{ $entry->name }}" class="dict-thumb">
                                @else
                                    <form
                                        action="{{ route('admin.herb-dictionary.images.store', $entry) }}"
                                        method="POST"
                                        enctype="multipart/form-data"
                                        class="dict-inline-upload"
                                        data-entry-name="{{ $entry->name }}"
                                    >
                                        @csrf
                                        <button type="button" class="dict-thumb empty dict-upload-button" aria-label="Thêm hình ảnh cho {{ $entry->name }}">+</button>
                                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="dict-inline-input">
                                    </form>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $entry->name }}</strong>
                                @if($entry->scientific_name)
                                    <span>{{ $entry->scientific_name }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="dict-info">{{ $entry->short_info }}</div>
                            </td>
                            <td><strong>{{ $entry->favorites_count }}</strong></td>
                            <td>
                                @if($entry->status === 'published')
                                    <span class="status published">Đã xuất bản</span>
                                @else
                                    <span class="status draft">Bản nháp</span>
                                @endif
                            </td>
                            <td>
                                <div class="dict-actions">
                                    @if($entry->status === 'published')
                                        <a href="{{ route('herb-dictionary.show', $entry) }}" target="_blank">Xem</a>
                                    @endif
                                    <a href="{{ route('admin.herb-dictionary.edit', $entry) }}">Sửa</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;color:#64748b;padding:3rem;">Chưa có mục từ điển nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($entries->hasPages())
            <div class="dict-pagination">{{ $entries->withQueryString()->links() }}</div>
        @endif
    </div>
</div>

<div id="dict-delete-modal" class="dict-delete-modal" aria-hidden="true">
    <div class="dict-delete-dialog" role="dialog" aria-modal="true" aria-labelledby="dict-delete-title">
        <div class="dict-delete-icon">!</div>
        <h2 id="dict-delete-title">Xác nhận xóa từ điển</h2>
        <p id="dict-delete-message">Bạn đang chọn xóa 0 mục từ điển. Thao tác này sẽ xóa cả hình ảnh và không thể hoàn tác.</p>
        <div class="dict-delete-list" id="dict-delete-list"></div>
        <div class="dict-delete-actions">
            <button type="button" id="dict-cancel-delete">Hủy bỏ</button>
            <button type="button" id="dict-confirm-delete">Xóa dữ liệu</button>
        </div>
    </div>
</div>

<style>
.dict-admin-page { margin-top: -1rem; font-family: 'Inter', system-ui, sans-serif; }
.dict-admin-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:1.25rem; }
.dict-filter { display:grid; grid-template-columns: minmax(240px, 1fr) 180px auto auto; gap:0.75rem; align-items:end; margin-bottom:1rem; }
.dict-filter label { display:block; font-size:0.78rem; font-weight:850; color:#64748b; text-transform:uppercase; margin-bottom:0.35rem; }
.dict-filter input, .dict-filter select { width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:0.65rem 0.75rem; box-sizing:border-box; }
.dict-filter-actions { display:flex; gap:0.5rem; }
.dict-filter-actions button, .dict-filter-actions a, .dict-add { border:0; border-radius:8px; padding:0.65rem 0.9rem; font-weight:850; text-decoration:none; white-space:nowrap; }
.dict-filter-actions button { background:#f0f9ff; color:#2563eb; cursor:pointer; }
.dict-filter-actions a { background:#f1f5f9; color:#64748b; }
.dict-add { background:#2563eb; color:white; text-align:center; }
.dict-import-panel { display:flex; justify-content:space-between; gap:1rem; align-items:center; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:0.95rem; margin-bottom:1rem; }
.dict-import-copy { display:flex; flex-direction:column; gap:0.25rem; min-width:220px; }
.dict-import-copy strong { color:#1e3a8a; font-size:0.95rem; }
.dict-import-copy span { color:#64748b; font-size:0.84rem; line-height:1.45; }
.dict-import-form { display:flex; align-items:center; justify-content:flex-end; gap:0.6rem; flex-wrap:wrap; }
.dict-import-form input[type="file"] { max-width:280px; border:1px solid #cbd5e1; border-radius:8px; padding:0.55rem; background:white; color:#334155; }
.dict-import-form a, .dict-import-form button { border:1px solid #99f6e4; border-radius:8px; padding:0.62rem 0.85rem; font-weight:850; text-decoration:none; white-space:nowrap; }
.dict-import-form a { background:#eff6ff; color:#2563eb; border-color:#bfdbfe; }
.dict-import-form button { border-color:#2563eb; background:#2563eb; color:white; cursor:pointer; }
.dict-import-errors { border:1px solid #fecaca; background:#fef2f2; color:#991b1b; border-radius:10px; padding:0.9rem 1rem; margin-bottom:1rem; font-size:0.9rem; }
.dict-import-errors ul { margin:0.5rem 0 0; padding-left:1.1rem; }
.dict-import-errors li { margin:0.25rem 0; }
.dict-bulk-bar { display:none; justify-content:space-between; align-items:center; gap:1rem; border:1px solid #fecaca; background:#fff7f7; border-radius:10px; padding:0.75rem 0.9rem; margin-bottom:1rem; }
.dict-bulk-bar.is-visible { display:flex; }
.dict-bulk-bar strong { display:block; color:#991b1b; font-size:0.92rem; }
.dict-bulk-bar span { color:#64748b; font-size:0.82rem; }
.dict-bulk-bar button { border:1px solid #ef4444; background:#ef4444; color:white; border-radius:8px; padding:0.58rem 0.85rem; font-weight:850; cursor:pointer; white-space:nowrap; }
.dict-table-wrap { overflow-x:auto; }
.dict-table { width:100%; border-collapse:collapse; min-width:960px; }
.dict-table th { text-align:left; color:#475569; font-size:0.78rem; text-transform:uppercase; border-bottom:1px solid #e2e8f0; padding:0.75rem; }
.dict-table td { border-bottom:1px solid #f1f5f9; padding:0.75rem; vertical-align:middle; color:#334155; }
.dict-table td strong { display:block; color:#0f172a; }
.dict-table td span { display:block; color:#64748b; font-style:italic; font-size:0.85rem; margin-top:0.2rem; }
.dict-select-col { width:38px; text-align:center !important; padding-left:0.5rem !important; padding-right:0.35rem !important; }
.dict-check { width:16px; height:16px; accent-color:#ef4444; cursor:pointer; }
.dict-image-cell { width:88px; }
.dict-thumb { width:64px; height:64px; object-fit:cover; border-radius:8px; border:1px solid #e2e8f0; }
.dict-thumb.empty { display:flex; align-items:center; justify-content:center; color:#2563eb; background:#eff6ff; font-size:1.5rem; font-weight:800; }
.dict-inline-upload { margin:0; width:64px; height:64px; position:relative; }
.dict-inline-input { display:none; }
.dict-upload-button { padding:0; font-family:inherit; cursor:pointer; transition:background 0.2s, border-color 0.2s, transform 0.2s; }
.dict-upload-button:hover { background:#eff6ff; border-color:#bfdbfe; transform:translateY(-1px); }
.dict-inline-upload.is-uploading .dict-upload-button { color:transparent; pointer-events:none; }
.dict-inline-upload.is-uploading .dict-upload-button::after { content:''; width:20px; height:20px; border:3px solid #bfdbfe; border-top-color:#2563eb; border-radius:50%; animation:dictSpin 0.75s linear infinite; }
.dict-info { max-width:340px; color:#64748b; line-height:1.45; }
.status { display:inline-flex; border-radius:999px; padding:0.25rem 0.55rem; font-weight:850; font-size:0.75rem; }
.status.published { color:#166534; background:#dcfce7; border:1px solid #bbf7d0; }
.status.draft { color:#64748b; background:#f1f5f9; border:1px solid #e2e8f0; }
.dict-actions { display:flex; gap:0.5rem; }
.dict-actions a { text-decoration:none; color:#0f766e; background:#f0fdfa; border:1px solid #99f6e4; border-radius:7px; padding:0.45rem 0.65rem; font-weight:850; }
.dict-pagination { display:flex; justify-content:center; margin-top:1rem; }
.dict-delete-modal { display:none; position:fixed; inset:0; z-index:99999; background:rgba(15,23,42,0.45); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:1rem; }
.dict-delete-modal.is-visible { display:flex; }
.dict-delete-dialog { width:420px; max-width:100%; background:#fff; border:1px solid #fee2e2; border-radius:12px; padding:1.5rem; box-shadow:0 24px 60px rgba(15,23,42,0.22); text-align:center; animation:dictModalIn 0.22s ease-out; }
.dict-delete-icon { width:44px; height:44px; margin:0 auto 0.85rem; border-radius:999px; display:flex; align-items:center; justify-content:center; background:#fee2e2; color:#dc2626; font-weight:900; font-size:1.25rem; }
.dict-delete-dialog h2 { margin:0; color:#0f172a; font-size:1.15rem; font-weight:900; }
.dict-delete-dialog p { margin:0.65rem 0 0; color:#64748b; font-size:0.9rem; line-height:1.5; }
.dict-delete-list { display:none; margin-top:0.85rem; padding:0.7rem 0.85rem; border-radius:8px; background:#f8fafc; color:#334155; text-align:left; font-size:0.85rem; line-height:1.45; max-height:120px; overflow:auto; }
.dict-delete-list.is-visible { display:block; }
.dict-delete-actions { display:flex; gap:0.65rem; margin-top:1.15rem; }
.dict-delete-actions button { flex:1; border:0; border-radius:8px; padding:0.72rem; font-weight:850; cursor:pointer; }
#dict-cancel-delete { background:#f1f5f9; color:#475569; }
#dict-confirm-delete { background:#ef4444; color:white; }
@keyframes dictSpin { to { transform:rotate(360deg); } }
@keyframes dictModalIn { from { transform:translateY(8px) scale(0.98); opacity:0; } to { transform:translateY(0) scale(1); opacity:1; } }
@media (max-width: 900px) {
    .dict-filter { grid-template-columns:1fr; }
    .dict-filter-actions { align-items:stretch; }
    .dict-import-panel { align-items:stretch; flex-direction:column; }
    .dict-import-form { justify-content:stretch; }
    .dict-import-form input[type="file"], .dict-import-form a, .dict-import-form button { width:100%; max-width:none; text-align:center; }
    .dict-bulk-bar { align-items:stretch; flex-direction:column; }
    .dict-bulk-bar button { width:100%; }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function firstErrorMessage(data) {
        if (data?.errors) {
            const firstField = Object.keys(data.errors)[0];
            if (firstField && data.errors[firstField]?.length) {
                return data.errors[firstField][0];
            }
        }

        return data?.message || 'Không thể thêm hình ảnh.';
    }

    function showDictionaryToast(message, isSuccess = true) {
        const toast = document.getElementById('globalToast');
        const toastIcon = document.getElementById('toastIcon');
        const toastMessage = document.getElementById('toastMessage');

        if (!toast || !toastIcon || !toastMessage) {
            return;
        }

        toastMessage.textContent = message;
        toastIcon.style.background = isSuccess ? '#d1fae5' : '#fee2e2';
        toastIcon.style.color = isSuccess ? '#10b981' : '#ef4444';
        toastIcon.textContent = isSuccess ? '✓' : '✕';
        toast.style.display = 'flex';

        requestAnimationFrame(function () {
            toast.style.transform = 'translateY(0)';
            toast.style.opacity = '1';
        });

        setTimeout(function () {
            if (toastMessage.textContent === message && typeof closeToast === 'function') {
                closeToast();
            }
        }, 5000);
    }

    const bulkForm = document.getElementById('dict-bulk-delete-form');
    const bulkBar = document.getElementById('dict-bulk-bar');
    const selectedCount = document.getElementById('dict-selected-count');
    const selectAll = document.getElementById('dict-select-all');
    const rowChecks = Array.from(document.querySelectorAll('.dict-row-check'));
    const deleteModal = document.getElementById('dict-delete-modal');
    const deleteMessage = document.getElementById('dict-delete-message');
    const deleteList = document.getElementById('dict-delete-list');
    const openDeleteModalButton = document.getElementById('dict-open-delete-modal');
    const cancelDeleteButton = document.getElementById('dict-cancel-delete');
    const confirmDeleteButton = document.getElementById('dict-confirm-delete');

    function checkedRows() {
        return rowChecks.filter(function (check) {
            return check.checked;
        });
    }

    function updateBulkState() {
        const checked = checkedRows();
        const count = checked.length;

        if (bulkBar) {
            bulkBar.classList.toggle('is-visible', count > 0);
        }

        if (selectedCount) {
            selectedCount.textContent = 'Đã chọn ' + count + ' mục';
        }

        if (selectAll) {
            selectAll.checked = rowChecks.length > 0 && count === rowChecks.length;
            selectAll.indeterminate = count > 0 && count < rowChecks.length;
        }
    }

    function openDeleteModal() {
        const checked = checkedRows();
        const count = checked.length;

        if (!deleteModal || count === 0) {
            return;
        }

        deleteMessage.textContent = 'Bạn đang chọn xóa ' + count + ' mục từ điển. Thao tác này sẽ xóa cả hình ảnh liên quan và không thể hoàn tác.';
        deleteList.innerHTML = '';
        checked.slice(0, 5).forEach(function (check) {
            const item = document.createElement('div');
            item.textContent = '• ' + (check.dataset.entryName || 'Mục từ điển');
            deleteList.appendChild(item);
        });

        if (count > 5) {
            const more = document.createElement('div');
            more.textContent = '• Và ' + (count - 5) + ' mục khác';
            deleteList.appendChild(more);
        }

        deleteList.classList.toggle('is-visible', count > 0);
        deleteModal.classList.add('is-visible');
        deleteModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        confirmDeleteButton?.focus();
    }

    function closeDeleteModal() {
        if (!deleteModal) {
            return;
        }

        deleteModal.classList.remove('is-visible');
        deleteModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    rowChecks.forEach(function (check) {
        check.addEventListener('change', updateBulkState);
    });

    selectAll?.addEventListener('change', function () {
        rowChecks.forEach(function (check) {
            check.checked = selectAll.checked;
        });
        updateBulkState();
    });

    openDeleteModalButton?.addEventListener('click', openDeleteModal);
    cancelDeleteButton?.addEventListener('click', closeDeleteModal);
    confirmDeleteButton?.addEventListener('click', function () {
        if (checkedRows().length > 0 && bulkForm) {
            bulkForm.submit();
        }
    });

    deleteModal?.addEventListener('click', function (event) {
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && deleteModal?.classList.contains('is-visible')) {
            closeDeleteModal();
        }
    });

    updateBulkState();

    document.querySelectorAll('.dict-inline-upload').forEach(function (form) {
        const button = form.querySelector('.dict-upload-button');
        const input = form.querySelector('.dict-inline-input');

        if (!button || !input) {
            return;
        }

        button.addEventListener('click', function () {
            input.click();
        });

        input.addEventListener('change', async function () {
            if (!input.files.length) {
                return;
            }

            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('image', input.files[0]);
            form.classList.add('is-uploading');
            button.disabled = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: formData,
                    credentials: 'same-origin',
                });
                const data = await response.json().catch(function () {
                    return {};
                });

                if (!response.ok) {
                    throw new Error(firstErrorMessage(data));
                }

                const image = document.createElement('img');
                image.src = data.image.url;
                image.alt = data.image.alt || form.dataset.entryName || 'Hình ảnh';
                image.className = 'dict-thumb';
                form.replaceWith(image);
                showDictionaryToast(data.message || 'Đã thêm hình ảnh.');
            } catch (error) {
                input.value = '';
                button.disabled = false;
                form.classList.remove('is-uploading');
                showDictionaryToast(error.message || 'Không thể thêm hình ảnh.', false);
            }
        });
    });
});
</script>
@endpush
@endsection
