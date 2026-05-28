@extends('layouts.admin')

@section('title', 'Quản lý Kho — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div class="icon-bg" style="width: 44px; height: 44px; background: #eff6ff; color: #2563eb; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
        <span class="icon">🏪</span>
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Quản lý Kho</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Theo dõi tồn kho, lô hàng và trạng thái dược liệu</p>
    </div>
</div>
@endsection

@section('content')
@php
    $totalItems = $items->total();
    $nearExpiryCount = 0;
    $expiredCount = 0;
    $unknownExpiryCount = 0;
    foreach($items as $item) {
        if(($item->computed_status ?? '') === 'near_expiry') $nearExpiryCount++;
        if(($item->computed_status ?? '') === 'expired') $expiredCount++;
        if(($item->computed_status ?? '') === 'unknown_expiry') $unknownExpiryCount++;
    }
@endphp

<div class="inventory-index-container" style="margin-top: -1rem;">

    {{-- Stats Cards --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline">
                        <span class="icon">📦</span>
                    </div>
                    <span class="stat-title">Tổng Mặt Hàng</span>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value">{{ number_format($totalItems) }}</h3>
                <span class="stat-badge bg-green-light">Đang theo dõi</span>
            </div>
            <div class="stat-footer">
                Tất cả mặt hàng trong hệ thống kho mới
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline" style="border-color: #f59e0b; background: #fffbeb;">
                        <span class="icon">⏰</span>
                    </div>
                    <span class="stat-title">Sắp Hết Hạn</span>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value" style="color: #d97706;">{{ $nearExpiryCount }}</h3>
                <span class="stat-badge" style="background: #fffbeb; color: #d97706; border: 1px solid #fef3c7;">≤ 2 tháng</span>
            </div>
            <div class="stat-footer">
                Cần kiểm tra và ưu tiên sử dụng
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline" style="border-color: #ef4444; background: #fef2f2;">
                        <span class="icon">🚫</span>
                    </div>
                    <span class="stat-title">Có Lô Hết Hạn</span>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value" style="color: #ef4444;">{{ $expiredCount }}</h3>
                <span class="stat-badge" style="background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2;">Cần xử lý</span>
            </div>
            <div class="stat-footer">
                Mặt hàng có lô đã quá hạn sử dụng
            </div>
        </div>
    </div>

    {{-- Filters & Table --}}
    <div class="main-content-card">
        <form action="{{ route('admin.inventory.index') }}" method="GET" class="filter-form">
            <div class="filter-row">
                <div class="search-input-group" style="flex: 1; max-width: 400px; position: relative; display: flex; align-items: center;">
                    <span class="icon" style="position: absolute; left: 1rem; color: #64748b; pointer-events: none;">🔍</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm mặt hàng theo tên..." style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-size: 0.9rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#cbd5e1'">
                </div>
                <div class="action-buttons" style="display: flex; gap: 0.5rem; align-items: center;">
                    <button type="button" class="btn-add screen-only" style="border: none; cursor: pointer; padding: 0.75rem 1.25rem;" onclick="document.getElementById('addItemModal').style.display='flex'">
                        <span class="icon">+</span> Thêm mặt hàng mới
                    </button>
                </div>
            </div>
            <div class="filter-bottom-row">
                <div class="filter-item">
                    <label>Lọc theo:</label>
                    <select name="filter" onchange="this.form.submit()">
                        <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>Tất cả mặt hàng</option>
                        <option value="available" {{ $filter === 'available' ? 'selected' : '' }}>Còn sử dụng được</option>
                        <option value="near_expiry" {{ $filter === 'near_expiry' ? 'selected' : '' }}>Sắp hết hạn (≤ 2 tháng)</option>
                        <option value="expired" {{ $filter === 'expired' ? 'selected' : '' }}>Đã hết hạn</option>
                        <option value="unknown_expiry" {{ $filter === 'unknown_expiry' ? 'selected' : '' }}>Chưa rõ hạn dùng</option>
                        <option value="external_products" {{ $filter === 'external_products' ? 'selected' : '' }}>Thuốc/Chế phẩm dùng ngoài</option>
                    </select>
                </div>
                <a href="{{ route('admin.inventory.index') }}" class="btn-reset">
                    <span class="icon">🔄</span> Làm mới
                </a>
            </div>
        </form>

        @if(session('success'))
            <div class="inventory-alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="inventory-alert alert-error">{{ session('error') }}</div>
        @endif

        <form id="inventory-bulk-delete-form" action="{{ route('admin.inventory.bulk-destroy') }}" method="POST" class="screen-only" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

        <div id="inventory-bulk-bar" class="bulk-delete-bar screen-only" hidden>
            <div class="bulk-delete-copy">
                <strong id="inventory-selected-count">Đã chọn 0 mặt hàng</strong>
                <span>Chỉ xóa các mặt hàng chưa có lịch sử lô, giao dịch kho hoặc đơn điều trị.</span>
            </div>
            <button type="button" id="open-inventory-delete-modal" class="btn-bulk-delete">
                Xóa đã chọn
            </button>
        </div>

        <div class="table-container">
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th class="inventory-select-col screen-only">
                            <input type="checkbox" id="inventory-select-all" class="inventory-check" aria-label="Chọn tất cả mặt hàng trên trang">
                        </th>
                        <th style="width: 250px;">TÊN MẶT HÀNG</th>
                        <th style="width: 130px;">LOẠI</th>
                        <th style="width: 120px;">ĐƯỜNG DÙNG</th>
                        <th style="width: 80px;">ĐƠN VỊ</th>
                        <th style="width: 110px;">TỒN KHẢ DỤNG</th>
                        <th style="width: 70px;">SỐ LÔ</th>
                        <th style="width: 130px;">LÔ FEFO ƯU TIÊN</th>
                        <th style="width: 110px;">HSD GẦN NHẤT</th>
                        <th style="width: 130px;">TRẠNG THÁI</th>
                        <th style="width: 90px;" class="screen-only">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td class="inventory-select-col screen-only">
                            <input type="checkbox" name="ids[]" value="{{ $item->id }}" form="inventory-bulk-delete-form" class="inventory-check inventory-row-check" data-item-name="{{ $item->name }}" aria-label="Chọn {{ $item->name }}">
                        </td>
                        <td>
                            <div class="item-name-cell">
                                <span class="name">{{ $item->name }}</span>
                                @if($item->usage_route === 'external')
                                    <span class="external-marker" title="Dùng ngoài da - Không được uống">⚠️ Ngoài</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($item->item_type === 'herb')
                                <span class="type-badge type-herb">🌿 Dược liệu</span>
                            @elseif($item->item_type === 'prepared_product')
                                <span class="type-badge type-prepared">💊 Chế phẩm</span>
                            @else
                                <span class="type-badge type-external">🧴 Dùng ngoài</span>
                            @endif
                        </td>
                        <td>
                            @if($item->usage_route === 'external')
                                <span class="route-badge route-external">Dùng ngoài</span>
                            @else
                                <span class="route-badge route-oral">Uống</span>
                            @endif
                        </td>
                        <td style="font-weight: 600; color: #475569;">{{ $item->unit }}</td>
                        <td>
                            <span class="quantity-display">{{ number_format($item->total_quantity) }}</span>
                        </td>
                        <td style="text-align: center;">
                            <span class="batch-count">{{ $item->batches->count() }}</span>
                        </td>
                        <td>
                            @if($item->fefo_batch)
                                <span class="fefo-code">{{ $item->fefo_batch->batch_number }}</span>
                            @else
                                <span style="color: #94a3b8; font-size: 0.78rem;">Chưa có lô KD</span>
                            @endif
                        </td>
                        <td>
                            @if($item->fefo_batch)
                                @if($item->fefo_batch->expiry_date)
                                    <span style="font-weight: 700; color: #334155; font-size: 0.82rem;">{{ $item->fefo_batch->expiry_date->format('d/m/Y') }}</span>
                                @else
                                    <span style="color: #d97706; font-weight: 600; font-size: 0.78rem;">Chưa rõ HSD</span>
                                @endif
                            @else
                                <span style="color: #94a3b8;">—</span>
                            @endif
                        </td>
                        <td>
                            @if(($item->computed_status ?? '') === 'expired')
                                <span class="status-badge status-expired">Có lô hết hạn</span>
                            @elseif(($item->computed_status ?? '') === 'unknown_expiry')
                                <span class="status-badge status-unknown">Chưa rõ HSD</span>
                            @elseif(($item->computed_status ?? '') === 'near_expiry')
                                <span class="status-badge status-near-expiry">Sắp hết hạn</span>
                            @else
                                <span class="status-badge status-available">Khả dụng</span>
                            @endif
                        </td>
                        <td class="screen-only">
                            <div class="action-cell">
                                <a href="{{ route('admin.inventory.show', $item->id) }}" class="btn-icon view" title="Xem chi tiết lô">
                                    <span>👁️</span> Xem lô
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" style="text-align: center; padding: 2rem; color: #94a3b8; font-weight: 600;">Không có dữ liệu mặt hàng nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-area">
            <p class="summary">Hiển thị {{ $items->firstItem() ?? 0 }} đến {{ $items->lastItem() ?? 0 }} của {{ $items->total() }} mặt hàng</p>
            <div class="pagination-controls">
                {{ $items->appends(['filter' => $filter, 'search' => request('search')])->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Modal xác nhận xóa mặt hàng kho --}}
<div id="inventoryDeleteModal" class="modal-overlay delete-modal-overlay" style="display: none;">
    <div class="modal-container delete-modal-container">
        <div class="modal-header">
            <h2>Xác nhận xóa mặt hàng</h2>
            <button type="button" class="btn-close-modal" id="closeInventoryDeleteModal">✕</button>
        </div>
        <div class="modal-body">
            <div class="delete-warning-box">
                <strong>Thao tác này cần xác nhận trước khi xóa.</strong>
                <p>Hệ thống chỉ xóa các mặt hàng chưa có lịch sử lô, giao dịch kho hoặc đơn điều trị để tránh lệch dữ liệu tồn kho.</p>
            </div>
            <div class="delete-selected-box">
                <span id="inventory-delete-count">Đã chọn 0 mặt hàng</span>
                <p id="inventory-delete-names"></p>
            </div>
            <div class="modal-footer delete-modal-footer">
                <button type="button" class="btn-cancel-delete" id="cancelInventoryDelete">Hủy</button>
                <button type="button" class="btn-confirm-delete" id="confirmInventoryDelete">Xác nhận xóa</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Thêm mặt hàng mới --}}
<div id="addItemModal" class="modal-overlay" style="display: none;">
    <div class="modal-container" style="max-width: 850px;">
        <div class="modal-header">
            <h2>Thêm Mặt Hàng Mới</h2>
            <button type="button" class="btn-close-modal" onclick="document.getElementById('addItemModal').style.display='none'">✕</button>
        </div>
        <div class="modal-body">
            @php
                $nextBatchId = \App\Models\InventoryBatch::max('id') + 1;
                $defaultBatchCode = 'LÔ ' . $nextBatchId;
            @endphp
            @if($errors->any())
                <div style="background: #fef2f2; color: #ef4444; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; font-size: 0.9rem;">
                    <ul style="margin: 0; padding-left: 1.5rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.inventory.store') }}" method="POST">
                @csrf
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                    <span style="background: #eff6ff; color: #2563eb; width: 36px; height: 36px; border-radius: 0.25rem; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">📦</span>
                    <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a;">Thông tin mặt hàng</h3>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="item_name" class="form-label">Tên mặt hàng <span style="color: #ef4444;">*</span></label>
                        <input type="text" class="form-input" id="item_name" name="name" value="{{ old('name') }}" required placeholder="Nhập tên mặt hàng">
                    </div>

                    <div class="form-group">
                        <label for="item_type" class="form-label">Loại mặt hàng <span style="color: #ef4444;">*</span></label>
                        <select class="form-input" id="item_type" name="item_type" required style="appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2364748b%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 0.65rem auto; padding-right: 2.5rem;">
                            <option value="">-- Chọn loại --</option>
                            <option value="herb" {{ old('item_type') === 'herb' ? 'selected' : '' }}>Dược liệu uống</option>
                            <option value="prepared_product" {{ old('item_type') === 'prepared_product' ? 'selected' : '' }}>Chế phẩm uống</option>
                            <option value="packaged_product" {{ old('item_type') === 'packaged_product' ? 'selected' : '' }}>Chế phẩm dùng ngoài</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="usage_route" class="form-label">Đường dùng <span style="color: #ef4444;">*</span></label>
                        <select class="form-input" id="usage_route" name="usage_route" required style="appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2364748b%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 0.65rem auto; padding-right: 2.5rem;">
                            <option value="">-- Chọn đường dùng --</option>
                            <option value="oral" {{ old('usage_route') === 'oral' ? 'selected' : '' }}>Uống</option>
                            <option value="external" {{ old('usage_route') === 'external' ? 'selected' : '' }}>Dùng ngoài</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="item_unit" class="form-label">Đơn vị tính <span style="color: #ef4444;">*</span></label>
                        <input type="text" class="form-input" id="item_unit" name="unit" value="{{ old('unit') }}" required placeholder="VD: gram, lọ, hộp, chai...">
                    </div>

                    <div class="form-group">
                        <label for="item_description" class="form-label">Mô tả</label>
                        <input type="text" class="form-input" id="item_description" name="description" value="{{ old('description') }}" placeholder="Mô tả (không bắt buộc)">
                    </div>
                </div>

                <div id="warningGroup" style="display: none; margin-top: 1.25rem;">
                    <div class="form-group">
                        <label for="item_warning" class="form-label" style="color: #ef4444;">
                            ⚠️ Cảnh báo sử dụng
                        </label>
                        <textarea class="form-input" id="item_warning" name="warning" rows="2" placeholder="VD: Chỉ dùng ngoài da, không được uống. Tránh tiếp xúc mắt..." style="border-color: #fca5a5;">{{ old('warning') }}</textarea>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.75rem; margin: 1.5rem 0 1.25rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                    <span style="background: #eef2ff; color: #4f46e5; width: 36px; height: 36px; border-radius: 0.25rem; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">📋</span>
                    <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a;">Thông tin lô hàng đầu tiên</h3>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem;">
                    <div class="form-group">
                        <label for="batch_code" class="form-label">Mã lô <span style="color: #ef4444;">*</span></label>
                        <input type="text" class="form-input" id="batch_code" name="batch_code" value="{{ old('batch_code', $defaultBatchCode) }}" required placeholder="VD: LÔ 1">
                    </div>
                    <div class="form-group">
                        <label for="quantity" class="form-label">Số lượng nhập <span style="color: #ef4444;">*</span></label>
                        <input type="number" class="form-input" id="quantity" name="quantity" value="{{ old('quantity') }}" required min="0.01" step="0.01" placeholder="VD: 100">
                    </div>
                    <div class="form-group">
                        <label for="expiry_date" class="form-label">Hạn sử dụng <span style="color: #ef4444;">*</span></label>
                        <input type="date" class="form-input" id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}" required>
                    </div>
                </div>

                <div class="modal-footer" style="display: flex; gap: 1rem; margin-top: 1.5rem; justify-content: flex-end; border-top: 1px solid #f1f5f9; padding-top: 1.25rem;">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('addItemModal').style.display='none'" style="background: #fff; min-width: 100px; padding: 0.6rem 1.5rem; border-radius: 0.25rem; border: 1px solid #cbd5e1; font-weight: 600; cursor: pointer; color: #64748b;">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary" style="min-width: 150px; padding: 0.6rem 1.5rem; border-radius: 0.25rem; background: #2563eb; color: white; border: none; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: 0 2px 6px rgba(37, 99, 235, 0.15);">
                        Lưu Mặt Hàng
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Auto open modal if validation errors exist
    @if($errors->any())
        document.getElementById('addItemModal').style.display='flex';
    @endif

    // Toggle warning field based on usage route
    document.addEventListener('DOMContentLoaded', function() {
        var selectAll = document.getElementById('inventory-select-all');
        var rowChecks = Array.prototype.slice.call(document.querySelectorAll('.inventory-row-check'));
        var bulkBar = document.getElementById('inventory-bulk-bar');
        var selectedCount = document.getElementById('inventory-selected-count');
        var deleteModal = document.getElementById('inventoryDeleteModal');
        var deleteCount = document.getElementById('inventory-delete-count');
        var deleteNames = document.getElementById('inventory-delete-names');
        var bulkDeleteForm = document.getElementById('inventory-bulk-delete-form');

        function getSelectedChecks() {
            return rowChecks.filter(function(check) {
                return check.checked;
            });
        }

        function updateBulkDeleteState() {
            var selected = getSelectedChecks();
            var count = selected.length;

            if (bulkBar) {
                bulkBar.hidden = count === 0;
            }
            if (selectedCount) {
                selectedCount.textContent = count > 0 ? 'Đã chọn ' + count + ' mặt hàng' : 'Đã chọn 0 mặt hàng';
            }
            if (selectAll) {
                selectAll.checked = rowChecks.length > 0 && count === rowChecks.length;
                selectAll.indeterminate = count > 0 && count < rowChecks.length;
            }

            rowChecks.forEach(function(check) {
                var row = check.closest('tr');
                if (row) {
                    row.classList.toggle('row-selected', check.checked);
                }
            });
        }

        function openDeleteModal() {
            var selected = getSelectedChecks();
            if (selected.length === 0 || !deleteModal) {
                return;
            }

            var names = selected.map(function(check) {
                return check.dataset.itemName;
            }).filter(Boolean);

            if (deleteCount) {
                deleteCount.textContent = 'Đã chọn ' + selected.length + ' mặt hàng';
            }
            if (deleteNames) {
                deleteNames.textContent = names.slice(0, 6).join(', ') + (names.length > 6 ? '...' : '');
            }
            deleteModal.style.display = 'flex';
        }

        function closeDeleteModal() {
            if (deleteModal) {
                deleteModal.style.display = 'none';
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                rowChecks.forEach(function(check) {
                    check.checked = selectAll.checked;
                });
                updateBulkDeleteState();
            });
        }

        rowChecks.forEach(function(check) {
            check.addEventListener('change', updateBulkDeleteState);
        });

        var openDeleteButton = document.getElementById('open-inventory-delete-modal');
        if (openDeleteButton) {
            openDeleteButton.addEventListener('click', openDeleteModal);
        }

        var closeDeleteButton = document.getElementById('closeInventoryDeleteModal');
        var cancelDeleteButton = document.getElementById('cancelInventoryDelete');
        if (closeDeleteButton) {
            closeDeleteButton.addEventListener('click', closeDeleteModal);
        }
        if (cancelDeleteButton) {
            cancelDeleteButton.addEventListener('click', closeDeleteModal);
        }

        var confirmDeleteButton = document.getElementById('confirmInventoryDelete');
        if (confirmDeleteButton && bulkDeleteForm) {
            confirmDeleteButton.addEventListener('click', function() {
                confirmDeleteButton.disabled = true;
                confirmDeleteButton.textContent = 'Đang xóa...';
                bulkDeleteForm.submit();
            });
        }

        if (deleteModal) {
            deleteModal.addEventListener('click', function(e) {
                if (e.target === this) closeDeleteModal();
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });

        updateBulkDeleteState();

        function toggleWarningField() {
            var usageRoute = document.getElementById('usage_route').value;
            var warningGroup = document.getElementById('warningGroup');
            if (usageRoute === 'external') {
                warningGroup.style.display = 'block';
            } else {
                warningGroup.style.display = 'none';
            }
        }

        document.getElementById('usage_route').addEventListener('change', toggleWarningField);

        document.getElementById('item_type').addEventListener('change', function() {
            var itemType = this.value;
            var usageRoute = document.getElementById('usage_route');
            if (itemType === 'herb' || itemType === 'prepared_product') {
                usageRoute.value = 'oral';
            } else if (itemType === 'packaged_product') {
                usageRoute.value = 'external';
            }
            toggleWarningField();
        });

        toggleWarningField();

        // Close modal when clicking outside
        var modal = document.getElementById('addItemModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) this.style.display = 'none';
            });
        }
    });
</script>

<style>
.inventory-index-container {
    --primary-green: #2563eb;
    --text-dark: #1e293b;
    --text-muted: #64748b;
    --bg-light: #f8fafc;
    --border-color: #e2e8f0;
}

/* Stats Grid - same as patients */
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
    background: #eff6ff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

.stat-title {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--text-dark);
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

/* Main Content Card */
.main-content-card {
    background: #fff;
    border-radius: 0.5rem;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.01);
}

.inventory-alert {
    margin: -0.5rem 0 1.25rem;
    padding: 0.8rem 1rem;
    border-radius: 0.35rem;
    font-size: 0.86rem;
    font-weight: 700;
}

.alert-success {
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.alert-error {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.bulk-delete-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1rem;
    padding: 0.85rem 1rem;
    border: 1px solid #fecaca;
    border-radius: 0.35rem;
    background: #fff7f7;
}

.bulk-delete-bar[hidden] {
    display: none;
}

.bulk-delete-copy {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
    color: #7f1d1d;
}

.bulk-delete-copy strong {
    font-size: 0.9rem;
}

.bulk-delete-copy span {
    font-size: 0.78rem;
    color: #991b1b;
}

.btn-bulk-delete {
    border: none;
    border-radius: 0.25rem;
    background: #dc2626;
    color: #fff;
    padding: 0.65rem 1rem;
    font-weight: 800;
    cursor: pointer;
    white-space: nowrap;
}

.btn-bulk-delete:hover {
    background: #b91c1c;
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
}

.action-buttons {
    display: flex;
    gap: 1rem;
}

.btn-add {
    padding: 0.75rem 1.5rem;
    border-radius: 0.25rem;
    background: #2563eb;
    color: #fff;
    text-decoration: none;
    font-weight: 700;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.15);
}

.filter-bottom-row {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding-top: 1.5rem;
    border-top: 1px dashed #f1f5f9;
    flex-wrap: wrap;
}

.filter-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.filter-item label {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text-muted);
}

.filter-item select {
    padding: 0.5rem 1rem;
    border-radius: 0.25rem;
    border: 1px solid var(--border-color);
    background: #fff;
    font-size: 0.85rem;
    font-weight: 600;
}

.btn-reset {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text-muted);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

/* Table */
.table-container {
    overflow-x: auto;
    border: 1px solid #cbd5e1;
    border-radius: 0.25rem;
    background: #fff;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
}

.inventory-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.82rem;
    color: #334155;
    font-family: 'Inter', system-ui, sans-serif;
}

.inventory-table th {
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

.inventory-table th.inventory-select-col,
.inventory-table td.inventory-select-col {
    width: 44px;
    min-width: 44px;
    padding: 0.5rem;
    text-align: center;
}

.inventory-check {
    width: 18px;
    height: 18px;
    accent-color: #dc2626;
    cursor: pointer;
}

.inventory-table td {
    padding: 0.5rem 0.75rem;
    border: 1px solid #e2e8f0;
    background-color: #fff;
    vertical-align: middle;
}

.inventory-table tr:nth-child(even) td {
    background-color: #f8fafc;
}

.inventory-table tr:hover td {
    background-color: #f1f5f9;
}

.inventory-table tr.row-selected td {
    background-color: #fff1f2;
}

.inventory-table tr.row-selected:hover td {
    background-color: #ffe4e6;
}

/* Item name styling */
.item-name-cell {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.item-name-cell .name {
    font-weight: 700;
    color: #0f172a;
}

.external-marker {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.15rem 0.4rem;
    background: #fef2f2;
    color: #991b1b;
    border-radius: 0.125rem;
    font-size: 0.7rem;
    font-weight: 600;
    white-space: nowrap;
    border: 1px solid #fecaca;
}

/* Type badges */
.type-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.2rem 0.5rem;
    border-radius: 0.125rem;
    font-size: 0.78rem;
    font-weight: 600;
    white-space: nowrap;
}

.type-herb {
    background: #f0fdf4;
    color: #16a34a;
    border: 1px solid #bbf7d0;
}

.type-prepared {
    background: #eef2ff;
    color: #4f46e5;
    border: 1px solid #c7d2fe;
}

.type-external {
    background: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fecaca;
}

/* Route badges */
.route-badge {
    display: inline-flex;
    padding: 0.2rem 0.5rem;
    border-radius: 0.125rem;
    font-size: 0.78rem;
    font-weight: 600;
}

.route-oral {
    background: #f0fdf4;
    color: #16a34a;
    border: 1px solid #bbf7d0;
}

.route-external {
    background: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fecaca;
}

/* Quantity display */
.quantity-display {
    font-weight: 800;
    color: #1e40af;
    font-size: 0.9rem;
    font-family: monospace;
}

/* Batch count */
.batch-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #eef2ff;
    color: #4f46e5;
    font-weight: 700;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    font-size: 0.82rem;
    border: 1px solid #c7d2fe;
}

/* FEFO code */
.fefo-code {
    font-family: monospace;
    font-weight: 700;
    background: #eef2ff;
    color: #4f46e5;
    padding: 0.2rem 0.5rem;
    border-radius: 0.125rem;
    font-size: 0.8rem;
    border: 1px solid #c7d2fe;
}

/* Status badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.2rem 0.5rem;
    border-radius: 0.125rem;
    font-size: 0.78rem;
    font-weight: 700;
    white-space: nowrap;
}

.status-available {
    background: #f0fdf4;
    color: #16a34a;
    border: 1px solid #bbf7d0;
}

.status-near-expiry {
    background: #fffbeb;
    color: #d97706;
    border: 1px solid #fef3c7;
}

.status-expired {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

.status-unknown {
    background: #fffbeb;
    color: #92400e;
    border: 1px solid #fde68a;
}

/* Action cell */
.action-cell {
    display: flex;
    gap: 0.5rem;
}

.btn-icon {
    padding: 0.4rem 0.75rem;
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

.btn-icon:hover {
    background: #f8fafc;
    border-color: #94a3b8;
    color: #0f172a;
}

/* Pagination */
.pagination-area {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.pagination-area .summary {
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 600;
}

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
    border-radius: 0.25rem;
    background: #fff;
    border: 1px solid #cbd5e1;
    color: #475569;
    text-decoration: none;
    font-weight: 700;
}

.page-item.active .page-link {
    background: #2563eb;
    color: #fff;
    border-color: #2563eb;
}

/* Modal */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(15, 23, 42, 0.4);
    backdrop-filter: blur(4px);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-container {
    background: #fff;
    width: 100%;
    max-width: 800px;
    border-radius: 0.5rem;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    animation: modal-pop 0.25s ease-out;
}

@keyframes modal-pop {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

.modal-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h2 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 800;
    color: #1e293b;
}

.btn-close-modal {
    background: #f1f5f9;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 0.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    cursor: pointer;
    font-size: 1rem;
    transition: all 0.2s;
}

.btn-close-modal:hover {
    background: #e2e8f0;
    color: #0f172a;
}

.modal-body {
    padding: 2rem;
    overflow-y: auto;
}

.delete-modal-container {
    max-width: 520px;
}

.delete-warning-box {
    border: 1px solid #fecaca;
    background: #fef2f2;
    color: #991b1b;
    border-radius: 0.35rem;
    padding: 1rem;
}

.delete-warning-box strong {
    display: block;
    font-size: 0.95rem;
    margin-bottom: 0.4rem;
}

.delete-warning-box p {
    margin: 0;
    color: #7f1d1d;
    font-size: 0.85rem;
    line-height: 1.5;
}

.delete-selected-box {
    margin-top: 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.35rem;
    padding: 0.9rem 1rem;
    background: #f8fafc;
}

.delete-selected-box span {
    font-weight: 800;
    color: #0f172a;
}

.delete-selected-box p {
    margin: 0.35rem 0 0;
    color: #64748b;
    font-size: 0.84rem;
    line-height: 1.5;
}

.delete-modal-footer {
    border-top: 1px solid #f1f5f9;
    padding-top: 1rem;
    margin-top: 1.25rem;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}

.btn-cancel-delete,
.btn-confirm-delete {
    border-radius: 0.25rem;
    padding: 0.65rem 1.1rem;
    font-weight: 800;
    cursor: pointer;
}

.btn-cancel-delete {
    background: #fff;
    color: #475569;
    border: 1px solid #cbd5e1;
}

.btn-confirm-delete {
    background: #dc2626;
    color: #fff;
    border: 1px solid #dc2626;
}

.btn-confirm-delete:disabled {
    opacity: 0.75;
    cursor: wait;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-label {
    font-size: 0.85rem;
    font-weight: 700;
    color: #475569;
}

.form-input {
    width: 100%;
    box-sizing: border-box;
    padding: 0.6rem 0.8rem;
    border-radius: 0.25rem;
    border: 1px solid #cbd5e1;
    font-size: 0.9rem;
    color: #1e293b;
    background: #fff;
    transition: border-color 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}

@media (max-width: 768px) {
    .action-buttons {
        width: 100%;
        justify-content: space-between;
    }
    .btn-add {
        flex: 1;
        text-align: center;
    }
    .filter-item {
        width: 100%;
        justify-content: space-between;
    }
    .filter-item select {
        width: 60%;
    }
    .bulk-delete-bar {
        align-items: stretch;
        flex-direction: column;
    }
}
</style>
@endsection
