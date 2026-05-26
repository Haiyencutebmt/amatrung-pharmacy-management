@extends('layouts.admin')

@section('title', 'Kho Dược Liệu — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div class="icon-bg" style="width: 44px; height: 44px; background: #eef9ee; color: #5eb542; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
        <span class="icon">🌿</span>
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Quản lý kho dược liệu</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Theo dõi tồn kho, phân loại và trạng thái thảo dược, chế phẩm</p>
    </div>
</div>
@endsection

@section('content')
<div class="record-index-container" style="margin-top: -1rem;">

    {{-- Stats Cards --}}
    <div class="stats-grid" style="margin-bottom: 2rem;">
        <a href="{{ route('admin.medicinal-herbs.index', array_merge(request()->query(), ['filter' => null, 'page' => null])) }}" class="stat-card {{ !request('filter') ? 'active-filter' : '' }}">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline">
                        <span class="icon">🌿</span>
                    </div>
                    <span class="stat-title">Tổng Dược Liệu</span>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value">{{ number_format($totalHerbs) }}</h3>
            </div>
            <div class="stat-footer">
                Tổng số thảo dược lưu trong hệ thống
            </div>
        </a>

        <a href="{{ route('admin.medicinal-herbs.index', array_merge(request()->query(), ['filter' => 'warning', 'page' => null])) }}" class="stat-card {{ request('filter') === 'warning' ? 'active-filter-warning' : '' }}">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline" style="border-color: #f59e0b; background: #fffbeb; color: #d97706;">
                        <span class="icon">⚠️</span>
                    </div>
                    <span class="stat-title">Sắp Hết / Hết Hàng</span>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value" style="color: #d97706;">{{ number_format($outOfStockCount + $warningStockCount) }}</h3>
            </div>
            <div class="stat-footer">
                Hết hàng: <strong>{{ $outOfStockCount }}</strong> &nbsp;&nbsp; Sắp hết: <strong>{{ $warningStockCount }}</strong>
            </div>
        </a>

        <a href="{{ route('admin.medicinal-herbs.index', array_merge(request()->query(), ['filter' => 'expired', 'page' => null])) }}" class="stat-card {{ request('filter') === 'expired' ? 'active-filter-expired' : '' }}">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline" style="border-color: #ef4444; background: #fef2f2; color: #dc2626;">
                        <span class="icon">❌</span>
                    </div>
                    <span class="stat-title">Hết Hạn Sử Dụng</span>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value" style="color: #dc2626;">{{ number_format($expiredCount) }}</h3>
            </div>
            <div class="stat-footer">
                Dược liệu quá hạn cần thanh lý / cập nhật
            </div>
        </a>
    </div>

    {{-- Filters & Table --}}
    <div class="main-content-card">
        <form action="{{ route('admin.medicinal-herbs.index') }}" method="GET" class="filter-form">
            @if(request('filter'))
                <input type="hidden" name="filter" value="{{ request('filter') }}">
            @endif
            <div class="filter-row" style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="display: flex; gap: 0.75rem; align-items: flex-end; flex-wrap: wrap; flex: 1;">
                    <div class="filter-item" style="display: flex; flex-direction: column; align-items: flex-start; gap: 0.35rem;">
                        <label style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Tìm dược liệu</label>
                        <div class="search-input-group" style="width: 280px; position: relative;">
                            <span class="icon" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.9rem;">🔍</span>
                            <input type="text" name="search" placeholder="Tên dược liệu, phân loại..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="filter-item" style="display: flex; flex-direction: column; align-items: flex-start; gap: 0.35rem;">
                        <label style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Cách dùng</label>
                        <select name="usage_type" onchange="this.form.submit()" style="height: 38px; padding: 0 1.75rem 0 1rem; border-radius: 0.375rem; border: 1px solid var(--border-color); background: #fcfdfe; width: 140px; color: var(--text-dark); font-weight: 600; font-size: 0.88rem; outline: none; cursor: pointer;">
                            <option value="">-- Tất cả --</option>
                            <option value="Uống" {{ request('usage_type') == 'Uống' ? 'selected' : '' }}>Uống</option>
                            <option value="Sắc" {{ request('usage_type') == 'Sắc' ? 'selected' : '' }}>Sắc</option>
                            <option value="Dùng ngoài" {{ request('usage_type') == 'Dùng ngoài' ? 'selected' : '' }}>Dùng ngoài</option>
                        </select>
                    </div>
                    <div style="height: 38px; display: flex; align-items: flex-end;">
                        <a href="{{ route('admin.medicinal-herbs.index') }}" class="btn-reset-box">
                            <span class="icon">🔄</span> Reset
                        </a>
                    </div>
                </div>

                <div class="action-buttons" style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                    <button type="button" id="btn-bulk-delete" style="display: none; height: 38px; padding: 0 1rem; border-radius: 0.375rem; background: #ef4444; color: #fff; border: none; font-weight: 700; cursor: pointer; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.1); align-items: center; justify-content: center; gap: 0.35rem; font-size: 0.85rem; white-space: nowrap; transition: all 0.2s;">
                        🗑️ Xóa đã chọn (<span id="selected-count">0</span>)
                    </button>
                    
                    {{-- Dropdown In & Nhập/Xuất Excel --}}
                    <div class="dropdown-export-wrapper" style="position: relative; display: inline-block;">
                        <button type="button" id="btnExportDropdown" style="height: 38px; padding: 0 1rem; border-radius: 0.375rem; border: 1px solid var(--border-color); background: #fff; color: #475569; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem; font-size: 0.85rem; white-space: nowrap; transition: all 0.2s; box-shadow: none;" onclick="toggleExportDropdown()">
                            <span class="icon">📊</span> In & Nhập/Xuất Excel ▾
                        </button>
                        <div id="exportDropdownMenu" class="export-dropdown-menu" style="display: none; position: absolute; top: 100%; right: 0; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 4px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); padding: 0.5rem; min-width: 240px; z-index: 999; margin-top: 5px;">
                            <div style="font-size: 0.72rem; font-weight: 800; color: #64748b; padding: 0.4rem 0.5rem; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; margin-bottom: 5px; letter-spacing: 0.5px;">🖨️ IN KHO DƯỢC LIỆU</div>
                            <a href="javascript:void(0)" onclick="printHerbList()" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; color: #1e293b; text-decoration: none; font-size: 0.85rem; font-weight: 600; border-radius: 4px; text-align: left;" class="dropdown-item-hover">
                                <span>📄</span> In danh sách hiện tại
                            </a>
                            
                            <div style="font-size: 0.72rem; font-weight: 800; color: #64748b; padding: 0.4rem 0.5rem; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; margin: 10px 0 5px 0; letter-spacing: 0.5px;">📥 XUẤT FILE EXCEL (CSV)</div>
                            <a href="{{ route('admin.medicinal-herbs.export-excel') }}?search={{ request('search') }}&usage_type={{ request('usage_type') }}" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; color: #166534; text-decoration: none; font-size: 0.85rem; font-weight: 600; border-radius: 4px; text-align: left;" class="dropdown-item-hover">
                                <span>📥</span> Xuất danh sách Excel
                            </a>
                            
                            <div style="font-size: 0.72rem; font-weight: 800; color: #64748b; padding: 0.4rem 0.5rem; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; margin: 10px 0 5px 0; letter-spacing: 0.5px;">📤 NHẬP FILE EXCEL (IMPORT)</div>
                            <a href="{{ route('admin.medicinal-herbs.download-template') }}" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; color: #475569; text-decoration: none; font-size: 0.85rem; font-weight: 600; border-radius: 4px; text-align: left;" class="dropdown-item-hover">
                                <span>📥</span> Tải file mẫu nhập liệu
                            </a>
                            <a href="javascript:void(0)" onclick="document.getElementById('importExcelModal').style.display='flex'" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; color: #2563eb; text-decoration: none; font-size: 0.85rem; font-weight: 600; border-radius: 4px; text-align: left;" class="dropdown-item-hover">
                                <span>📤</span> Nhập danh sách từ Excel
                            </a>
                        </div>
                    </div>

                    <button type="button" class="btn-add" onclick="document.getElementById('addHerbModal').style.display='flex'">
                        <span class="icon">+</span> Thêm Dược Liệu
                    </button>
                </div>
            </div>
        </form>

        <form id="bulk-delete-form" action="{{ route('admin.medicinal-herbs.bulk-destroy') }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="table-container">
                <table class="patient-table">
                    <thead>
                        <tr>
                            <th style="width: 45px; text-align: center;">
                                <input type="checkbox" id="select-all" style="width: 18px; height: 18px; cursor: pointer; accent-color: #16a34a; margin-top: 3px;">
                            </th>
                            <th>TÊN DƯỢC LIỆU</th>
                            <th>PHÂN LOẠI</th>
                            <th>TỒN KHO</th>
                            <th>TRẠNG THÁI</th>
                            <th>CẢNH BÁO</th>
                            <th>HÀNH ĐỘNG</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($herbs as $herb)
                        <tr>
                            <td style="text-align: center; vertical-align: middle;">
                                <input type="checkbox" name="ids[]" value="{{ $herb->id }}" class="herb-checkbox" style="width: 18px; height: 18px; cursor: pointer; accent-color: #16a34a;">
                            </td>
                            <td>
                                <div style="font-weight: 750; color: var(--text-dark); font-size: 1rem;">
                                    {{ $herb->name }}
                                </div>
                                @if($herb->usage_type == 'Dùng ngoài' || str_contains(strtolower($herb->usage_type), 'xoa bóp'))
                                    <div style="font-size: 0.75rem; color: #ef4444; font-weight: bold; margin-top: 0.25rem; display: flex; align-items: center; gap: 0.25rem;">
                                        <span>⚠️</span> Chỉ dùng ngoài da, không uống!
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #475569;">{{ $herb->category ?? 'N/A' }}</span>
                            </td>
                            <td>
                                @if($herb->stock_quantity <= 0)
                                    <span style="color: #ef4444; font-weight: 800; font-size: 1.1rem;">0</span> <span style="color: #64748b; font-weight: 600;">{{ $herb->unit }}</span>
                                @elseif($herb->isWarningStock())
                                    <span style="color: #f59e0b; font-weight: 800; font-size: 1.1rem;">{{ floatval($herb->stock_quantity) }}</span> <span style="color: #64748b; font-weight: 600;">{{ $herb->unit }}</span>
                                @else
                                    <span style="font-weight: 750; color: #1e3a5f; font-size: 1.05rem;">{{ floatval($herb->stock_quantity) }}</span> <span style="color: #64748b; font-weight: 600;">{{ $herb->unit }}</span>
                                @endif
                            </td>
                            <td>
                                @if($herb->status == 'active')
                                    <span style="background: #eef9ee; color: #16a34a; padding: 0.3rem 0.75rem; border-radius: 0.25rem; font-weight: 800; font-size: 0.75rem;">Đang dùng</span>
                                @elseif($herb->status == 'out_of_stock')
                                    <span style="background: #f1f5f9; color: #64748b; padding: 0.3rem 0.75rem; border-radius: 0.25rem; font-weight: 800; font-size: 0.75rem;">Hết hàng</span>
                                @else
                                    <span style="background: #fef2f2; color: #ef4444; padding: 0.3rem 0.75rem; border-radius: 0.25rem; font-weight: 800; font-size: 0.75rem;">Ngừng sử dụng</span>
                                @endif
                            </td>
                            <td>
                                @if($herb->stock_quantity <= 0)
                                    <div style="color: #ef4444; font-size: 0.8rem; font-weight: 700; display: flex; align-items: center; gap: 0.25rem; margin-bottom: 0.15rem;"><span>⚠️</span> Hết hàng</div>
                                @elseif($herb->isWarningStock())
                                    <div style="color: #f59e0b; font-size: 0.8rem; font-weight: 700; display: flex; align-items: center; gap: 0.25rem; margin-bottom: 0.15rem;"><span>⚠️</span> Sắp hết</div>
                                @endif
                                
                                @if($herb->isExpired())
                                    <div style="color: #ef4444; font-size: 0.8rem; font-weight: 700; display: flex; align-items: center; gap: 0.25rem;"><span>❌</span> Hết hạn ({{ $herb->expiry_date->format('d/m/Y') }})</div>
                                @elseif($herb->expiry_date)
                                    <div style="color: #64748b; font-size: 0.8rem; font-weight: 600;">HSD: {{ $herb->expiry_date->format('d/m/Y') }}</div>
                                @endif
                            <td>
                                <div class="action-cell">
                                    <a href="javascript:void(0)" 
                                       class="btn-edit-box btn-edit-trigger" 
                                       title="Sửa"
                                       data-id="{{ $herb->id }}"
                                       data-name="{{ e($herb->name) }}"
                                       data-category="{{ e($herb->category) }}"
                                       data-usage_type="{{ e($herb->usage_type) }}"
                                       data-unit="{{ e($herb->unit) }}"
                                       data-stock_quantity="{{ floatval($herb->stock_quantity) }}"
                                       data-expiry_date="{{ $herb->expiry_date?->format('Y-m-d') }}"
                                       data-status="{{ e($herb->status) }}"
                                       data-warning_note="{{ e($herb->warning_note) }}"
                                       data-description="{{ e($herb->description) }}">
                                        <span class="icon">✏️</span> Sửa
                                    </a>
                                    <a href="javascript:void(0)" 
                                       class="btn-history-box btn-history-trigger" 
                                       title="Xem lịch sử thao tác"
                                       data-id="{{ $herb->id }}"
                                       data-name="{{ e($herb->name) }}">
                                        <span class="icon">📜</span> Lịch sử
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #64748b; padding: 3rem 1rem; font-weight: 500;">
                                Không tìm thấy dược liệu nào.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @if($herbs->hasPages())
        <div class="pagination-area">
            <p class="summary">Hiển thị {{ $herbs->firstItem() }} đến {{ $herbs->lastItem() }} của {{ $herbs->total() }} dược liệu</p>
            <div class="pagination-controls">
                {{ $herbs->withQueryString()->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: #fff;
    padding: 1.25rem 1.5rem;
    border-radius: 0.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    border: 1px solid rgba(241, 245, 249, 0.8);
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    border-color: #cbd5e1;
}

.stat-card.active-filter {
    border-color: #5eb542;
    box-shadow: 0 0 0 3px rgba(94, 181, 66, 0.15);
    background: #f8fafc;
}

.stat-card.active-filter-warning {
    border-color: #f59e0b;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
    background: #fffdf9;
}

.stat-card.active-filter-expired {
    border-color: #ef4444;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
    background: #fffcfc;
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
    background: #f0fdf4;
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

.stat-body {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.stat-value {
    margin: 0;
    font-size: 2.2rem;
    font-weight: 850;
    color: var(--text-dark);
    line-height: 1;
}

.stat-footer {
    font-size: 0.8rem;
    color: var(--text-muted);
    font-weight: 600;
}

.form-input {
    width: 100%;
    box-sizing: border-box;
    transition: all 0.2s;
}
.form-input:focus {
    border-color: #5eb542 !important;
    box-shadow: 0 0 0 3px rgba(94, 181, 66, 0.15);
}

.modal-overlay {
    transition: opacity 0.3s ease;
}

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
    border-radius: 0.5rem;
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
    width: 280px;
}

.search-input-group input {
    width: 100%;
    height: 38px;
    padding: 0 1rem 0 2.25rem;
    border-radius: 0.375rem;
    border: 1px solid var(--border-color);
    background: #fcfdfe;
    font-size: 0.88rem;
    color: var(--text-dark);
    transition: all 0.2s;
}

.search-input-group input:focus {
    border-color: #5eb542;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(94, 181, 66, 0.15);
    outline: none;
}

.search-input-group .icon {
    position: absolute;
    left: 0.85rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 0.9rem;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.btn-filter {
    height: 38px;
    padding: 0 1.25rem;
    border-radius: 0.375rem;
    border: 1px solid #eef2ff;
    background: #f8fbff;
    color: #3b82f6;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    white-space: nowrap;
}

.btn-reset-box {
    height: 38px;
    padding: 0 1.25rem;
    border-radius: 0.375rem;
    border: 1px solid var(--border-color);
    background: #fff;
    color: var(--text-muted);
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    white-space: nowrap;
    transition: all 0.2s;
}

.btn-reset-box:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: var(--text-dark);
}

.btn-add {
    height: 38px;
    padding: 0 1.25rem;
    border-radius: 0.375rem;
    background: #5eb542;
    color: #fff;
    text-decoration: none;
    font-weight: 700;
    box-shadow: 0 4px 10px rgba(94, 181, 66, 0.15);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    white-space: nowrap;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-add:hover {
    background: #4fa235;
    box-shadow: 0 4px 12px rgba(79, 162, 53, 0.25);
}

.btn-edit-box {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.45rem 0.9rem;
    border-radius: 0.375rem;
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
    font-weight: 700;
    text-decoration: none;
    font-size: 0.82rem;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.btn-edit-box:hover {
    background: #16a34a;
    color: #ffffff;
    border-color: #16a34a;
    box-shadow: 0 2px 6px rgba(22, 163, 74, 0.2);
}

.btn-history-box {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.45rem 0.9rem;
    border-radius: 0.375rem;
    background: #f8fafc;
    color: #475569;
    border: 1px solid #e2e8f0;
    font-weight: 700;
    text-decoration: none;
    font-size: 0.82rem;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.btn-history-box:hover {
    background: #e2e8f0;
    color: #1e293b;
    border-color: #cbd5e1;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.export-dropdown-menu a.dropdown-item-hover:hover {
    background-color: #f1f5f9 !important;
}

.patient-table {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid #cbd5e1;
}

.patient-table thead tr {
    background: #f8fafc;
}

.patient-table th {
    text-align: left;
    padding: 0.85rem 1rem;
    font-size: 0.8rem;
    font-weight: 800;
    color: #1e3a5f;
    letter-spacing: 0.02em;
    border: 1px solid #cbd5e1;
}

.patient-table td {
    padding: 0.85rem 1rem;
    border: 1px solid #e2e8f0;
    vertical-align: middle;
}

.patient-table tbody tr:hover {
    background: #fafbfc;
}

.patient-code {
    background: #f0f7ff;
    color: #3b82f6;
    padding: 0.4rem 0.75rem;
    border-radius: 0.3rem;
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
    border-radius: 0.3rem;
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

<!-- Modal Thêm Dược Liệu -->
<div id="addHerbModal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; opacity: 1;">
    <div style="background: #fff; border-radius: 0.5rem; width: 950px; max-width: 95%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border: 1px solid #f1f5f9; overflow: hidden; display: flex; flex-direction: column; max-height: 90vh; animation: modalSlideIn 0.3s ease-out;">
        <div style="padding: 1.25rem 2rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #fafbfc;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: 850; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                <span>🌿</span> Thêm Dược Liệu Mới
            </h2>
            <button type="button" onclick="document.getElementById('addHerbModal').style.display='none'" style="background: none; border: none; font-size: 1.5rem; color: #94a3b8; cursor: pointer; padding: 0.25rem;">✕</button>
        </div>
        <div style="padding: 1.5rem 2rem; overflow-y: auto; flex: 1;">
            @if ($errors->any())
                <div style="background: #fef2f2; color: #ef4444; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem; font-size: 0.9rem;">
                    <ul style="margin: 0; padding-left: 1.5rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.medicinal-herbs.store') }}" method="POST">
                @csrf
                <input type="hidden" name="form_type" value="create">
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem;">
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="font-weight: 700; color: #475569; font-size: 0.9rem;">Tên dược liệu <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="name" class="form-input" style="padding: 0.75rem 1rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem;" placeholder="VD: Nhân sâm, Bạch thược..." value="{{ old('name') }}" required>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="font-weight: 700; color: #475569; font-size: 0.9rem;">Phân loại</label>
                        <select name="category" class="form-input" style="padding: 0.75rem 1rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem;">
                            <option value="Dược liệu bốc thuốc" {{ old('category') == 'Dược liệu bốc thuốc' ? 'selected' : '' }}>Dược liệu bốc thuốc</option>
                            <option value="Dược liệu rời" {{ old('category') == 'Dược liệu rời' ? 'selected' : '' }}>Dược liệu rời</option>
                            <option value="Thuốc ngâm rượu" {{ old('category') == 'Thuốc ngâm rượu' ? 'selected' : '' }}>Thuốc ngâm rượu</option>
                            <option value="Chế phẩm dùng ngoài" {{ old('category') == 'Chế phẩm dùng ngoài' ? 'selected' : '' }}>Chế phẩm dùng ngoài</option>
                        </select>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="font-weight: 700; color: #475569; font-size: 0.9rem;">Cách dùng</label>
                        <select name="usage_type" class="form-input" style="padding: 0.75rem 1rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem;">
                            <option value="Sắc" {{ old('usage_type') == 'Sắc' ? 'selected' : '' }}>Sắc</option>
                            <option value="Uống" {{ old('usage_type') == 'Uống' ? 'selected' : '' }}>Uống</option>
                            <option value="Dùng ngoài" {{ old('usage_type') == 'Dùng ngoài' ? 'selected' : '' }}>Dùng ngoài</option>
                        </select>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="font-weight: 700; color: #475569; font-size: 0.9rem;">Đơn vị tính <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="unit" class="form-input" style="padding: 0.75rem 1rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem;" placeholder="VD: g, kg, thang, gói..." value="{{ old('unit') }}" required>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="font-weight: 700; color: #475569; font-size: 0.9rem;">Số lượng tồn kho <span style="color: #ef4444;">*</span></label>
                        <input type="number" step="any" name="stock_quantity" class="form-input" style="padding: 0.75rem 1rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem;" placeholder="VD: 5, 1000..." value="{{ old('stock_quantity', '0') }}" required>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="font-weight: 700; color: #475569; font-size: 0.9rem;">Hạn sử dụng</label>
                        <input type="date" name="expiry_date" class="form-input" style="padding: 0.75rem 1rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem;" value="{{ old('expiry_date') }}">
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="font-weight: 700; color: #475569; font-size: 0.9rem;">Trạng thái <span style="color: #ef4444;">*</span></label>
                        <select name="status" class="form-input" style="padding: 0.75rem 1rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem;" required>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Đang sử dụng</option>
                            <option value="out_of_stock" {{ old('status') == 'out_of_stock' ? 'selected' : '' }}>Hết hàng</option>
                            <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>Ngừng sử dụng</option>
                        </select>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="font-weight: 700; color: #475569; font-size: 0.9rem;">Cảnh báo đặc biệt</label>
                        <input type="text" name="warning_note" class="form-input" style="padding: 0.75rem 1rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem;" placeholder="VD: Độc tính nhẹ, phụ nữ có thai không dùng..." value="{{ old('warning_note') }}">
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem; grid-column: span 3;">
                        <label style="font-weight: 700; color: #475569; font-size: 0.9rem;">Mô tả / Tác dụng</label>
                        <textarea name="description" class="form-input" rows="2" style="padding: 0.75rem 1rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem;" placeholder="Mô tả công dụng, tính vị của vị thuốc...">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; justify-content: flex-end; border-top: 1px solid #f1f5f9; padding-top: 1.25rem;">
                    <button type="button" onclick="document.getElementById('addHerbModal').style.display='none'" style="background: #fff; min-width: 100px; padding: 0.75rem 1.5rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; font-weight: 700; cursor: pointer; color: #64748b; font-size: 0.95rem;">Hủy bỏ</button>
                    <button type="submit" style="min-width: 160px; padding: 0.75rem 1.5rem; border-radius: 0.375rem; background: #5eb542; color: white; border: none; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(94, 181, 66, 0.2); font-size: 0.95rem;">
                        Lưu dược liệu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa Dược Liệu -->
<div id="editHerbModal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; opacity: 1;">
    <div style="background: #fff; border-radius: 0.5rem; width: 950px; max-width: 95%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border: 1px solid #f1f5f9; overflow: hidden; display: flex; flex-direction: column; max-height: 90vh; animation: modalSlideIn 0.3s ease-out;">
        <div style="padding: 1.25rem 2rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #fafbfc;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: 850; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                <span>✏️</span> Chỉnh Sửa Dược Liệu
            </h2>
            <button type="button" onclick="document.getElementById('editHerbModal').style.display='none'" style="background: none; border: none; font-size: 1.5rem; color: #94a3b8; cursor: pointer; padding: 0.25rem;">✕</button>
        </div>
        <div style="padding: 1.5rem 2rem; overflow-y: auto; flex: 1;">
            @if ($errors->any() && old('form_type') === 'edit')
                <div style="background: #fef2f2; color: #ef4444; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem; font-size: 0.9rem;">
                    <ul style="margin: 0; padding-left: 1.5rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="editHerbForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="form_type" value="edit">
                <input type="hidden" name="id" id="edit_id" value="{{ old('id') }}">

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem;">
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="font-weight: 700; color: #475569; font-size: 0.9rem;">Tên dược liệu <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-input" style="padding: 0.75rem 1rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem;" placeholder="VD: Nhân sâm, Bạch thược..." required>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="font-weight: 700; color: #475569; font-size: 0.9rem;">Phân loại</label>
                        <select name="category" id="edit_category" class="form-input" style="padding: 0.75rem 1rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem;">
                            <option value="Dược liệu bốc thuốc">Dược liệu bốc thuốc</option>
                            <option value="Dược liệu rời">Dược liệu rời</option>
                            <option value="Thuốc ngâm rượu">Thuốc ngâm rượu</option>
                            <option value="Chế phẩm dùng ngoài">Chế phẩm dùng ngoài</option>
                        </select>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="font-weight: 700; color: #475569; font-size: 0.9rem;">Cách dùng</label>
                        <select name="usage_type" id="edit_usage_type" class="form-input" style="padding: 0.75rem 1rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem;">
                            <option value="Sắc">Sắc</option>
                            <option value="Uống">Uống</option>
                            <option value="Dùng ngoài">Dùng ngoài</option>
                        </select>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="font-weight: 700; color: #475569; font-size: 0.9rem;">Đơn vị tính <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="unit" id="edit_unit" class="form-input" style="padding: 0.75rem 1rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem;" placeholder="VD: g, kg, thang, gói..." required>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="font-weight: 700; color: #475569; font-size: 0.9rem;">Số lượng tồn kho <span style="color: #ef4444;">*</span></label>
                        <input type="number" step="any" name="stock_quantity" id="edit_stock_quantity" class="form-input" style="padding: 0.75rem 1rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem;" placeholder="VD: 5, 1000..." required>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="font-weight: 700; color: #475569; font-size: 0.9rem;">Hạn sử dụng</label>
                        <input type="date" name="expiry_date" id="edit_expiry_date" class="form-input" style="padding: 0.75rem 1rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem;">
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="font-weight: 700; color: #475569; font-size: 0.9rem;">Trạng thái <span style="color: #ef4444;">*</span></label>
                        <select name="status" id="edit_status" class="form-input" style="padding: 0.75rem 1rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem;" required>
                            <option value="active">Đang sử dụng</option>
                            <option value="out_of_stock">Hết hàng</option>
                            <option value="expired">Ngừng sử dụng</option>
                        </select>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="font-weight: 700; color: #475569; font-size: 0.9rem;">Cảnh báo đặc biệt</label>
                        <input type="text" name="warning_note" id="edit_warning_note" class="form-input" style="padding: 0.75rem 1rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem;" placeholder="VD: Độc tính nhẹ, phụ nữ có thai không dùng...">
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem; grid-column: span 3;">
                        <label style="font-weight: 700; color: #475569; font-size: 0.9rem;">Mô tả / Tác dụng</label>
                        <textarea name="description" id="edit_description" class="form-input" rows="2" style="padding: 0.75rem 1rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem;" placeholder="Mô tả công dụng, tính vị của vị thuốc..."></textarea>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; justify-content: flex-end; border-top: 1px solid #f1f5f9; padding-top: 1.25rem;">
                    <button type="button" onclick="document.getElementById('editHerbModal').style.display='none'" style="background: #fff; min-width: 100px; padding: 0.75rem 1.5rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; font-weight: 700; cursor: pointer; color: #64748b; font-size: 0.95rem;">Hủy bỏ</button>
                    <button type="submit" style="min-width: 160px; padding: 0.75rem 1.5rem; border-radius: 0.375rem; background: #5eb542; color: white; border: none; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(94, 181, 66, 0.2); font-size: 0.95rem;">
                        Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Tự động mở modal nếu có lỗi validation trả về từ server
    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            @if (old('form_type') === 'edit')
                const editModal = document.getElementById('editHerbModal');
                const editForm = document.getElementById('editHerbForm');
                
                editForm.action = `/admin/medicinal-herbs/{{ old('id') }}`;
                
                document.getElementById('edit_id').value = "{{ old('id') }}";
                document.getElementById('edit_name').value = "{{ old('name') }}";
                document.getElementById('edit_category').value = "{{ old('category') }}";
                document.getElementById('edit_usage_type').value = "{{ old('usage_type') }}";
                document.getElementById('edit_unit').value = "{{ old('unit') }}";
                document.getElementById('edit_stock_quantity').value = "{{ old('stock_quantity') }}";
                document.getElementById('edit_expiry_date').value = "{{ old('expiry_date') }}";
                document.getElementById('edit_status').value = "{{ old('status') }}";
                document.getElementById('edit_warning_note').value = "{{ old('warning_note') }}";
                document.getElementById('edit_description').value = "{{ old('description') }}";
                
                editModal.style.display = 'flex';
            @else
                document.getElementById('addHerbModal').style.display = 'flex';
            @endif
        });
    @endif
</script>

{{-- Custom Confirmation Modal --}}
<div id="confirmModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.25s ease;">
    <div style="background: #fff; border-radius: 0.5rem; width: 480px; padding: 2rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); transform: scale(0.9); transition: transform 0.25s ease; border: 1px solid #f1f5f9;" id="confirmModalCard">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="width: 64px; height: 64px; background: #fee2e2; color: #ef4444; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 2.2rem; margin-bottom: 1rem;">
                ⚠️
            </div>
            <h3 style="margin: 0 0 0.5rem 0; font-size: 1.35rem; font-weight: 800; color: #1e293b;">Xác nhận xóa dược liệu?</h3>
            <p style="margin: 0; color: #64748b; font-size: 0.95rem; line-height: 1.5;" id="confirmModalText">Bạn có chắc chắn muốn xóa các dược liệu đã chọn khỏi hệ thống không? Hành động này không thể hoàn tác.</p>
        </div>
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <button type="button" id="confirmCancelBtn" style="flex: 1; padding: 0.8rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; background: #fff; color: #475569; font-weight: 700; cursor: pointer; transition: all 0.2s;">
                Hủy bỏ
            </button>
            <button type="button" id="confirmOkBtn" style="flex: 1; padding: 0.8rem; border-radius: 0.375rem; border: none; background: #ef4444; color: #fff; font-weight: 700; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.15);">
                Đồng ý xóa
            </button>
        </div>
    </div>
</div>

{{-- Modal Nhập Excel --}}
<div id="importExcelModal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; opacity: 1; transition: opacity 0.25s ease;">
    <div class="modal-card" style="width: 550px; background: #ffffff; border-radius: 0.5rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); padding: 1.5rem; position: relative; border: 1px solid #f1f5f9;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem; margin-bottom: 1.25rem;">
            <h3 style="font-size: 1.15rem; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 0.5rem; margin: 0;">📤 Nhập Dược Liệu từ Excel</h3>
            <button type="button" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #94a3b8;" onclick="document.getElementById('importExcelModal').style.display='none'">×</button>
        </div>
        <form action="{{ route('admin.medicinal-herbs.import-excel') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-weight: 700; color: #475569; margin-bottom: 0.5rem; font-size: 0.9rem;">Chọn file Excel hoặc CSV (.xlsx, .xls, .csv)</label>
                <input type="file" name="excel_file" accept=".xlsx, .xls, .csv" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.375rem; font-size: 0.9rem; background: #f8fafc;">
                <p style="font-size: 0.78rem; color: #64748b; margin-top: 0.65rem; line-height: 1.45; background: #f8fafc; padding: 0.75rem; border-left: 3px solid #5eb542; border-radius: 2px;">
                    * <strong>Cấu trúc tệp:</strong> Nên sử dụng tệp mẫu Excel có định dạng cột tiếng Việt rõ ràng, dễ hiểu. 
                    <a href="{{ route('admin.medicinal-herbs.download-template') }}" style="color: #2563eb; font-weight: 700; text-decoration: underline; display: inline-flex; align-items: center; gap: 0.2rem; margin-left: 5px;">
                        <span>📥</span> Tải file Excel mẫu tại đây
                    </a>
                    <br>* <strong>Trùng tên dược liệu:</strong> Hệ thống sẽ tự động cộng dồn số lượng tồn kho và cập nhật thông tin mới nhất.
                </p>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid #e2e8f0; padding-top: 1rem; margin-top: 1rem;">
                <button type="button" class="btn-reset-box" style="padding: 0.6rem 1.25rem; border-radius: 0.375rem; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-weight: 700; cursor: pointer;" onclick="document.getElementById('importExcelModal').style.display='none'">Hủy</button>
                <button type="submit" class="btn-add" style="padding: 0.6rem 1.25rem; border-radius: 0.375rem; font-weight: 700; border: none; cursor: pointer;">Tải lên & Nhập</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Lịch sử Thao tác --}}
<div id="historyStockModal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; opacity: 1;">
    <div style="background: #fff; border-radius: 0.5rem; width: 700px; max-width: 95%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border: 1px solid #f1f5f9; overflow: hidden; display: flex; flex-direction: column; max-height: 80vh;">
        <div style="padding: 1.25rem 2rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #fafbfc;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: 850; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                <span>📜</span> Lịch Sử Thao Tác: <span id="history_herb_name" style="color: #5eb542;">...</span>
            </h2>
            <button type="button" onclick="document.getElementById('historyStockModal').style.display='none'" style="background: none; border: none; font-size: 1.5rem; color: #94a3b8; cursor: pointer; padding: 0.25rem;">✕</button>
        </div>
        <div style="padding: 1.5rem 2rem; overflow-y: auto; flex: 1;">
            <div id="history_loading" style="text-align: center; padding: 2rem; color: #64748b; font-weight: 600;">
                <span>⏳</span> Đang tải lịch sử thao tác...
            </div>
            
            <div id="history_empty" style="display: none; text-align: center; padding: 3rem 1rem; color: #64748b; font-weight: 500;">
                ❌ Dược liệu này chưa có lịch sử thay đổi số lượng tồn kho.
            </div>

            <div id="history_content" style="display: none;">
                <div id="history_list" style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <!-- Rows dynamic load here -->
                </div>
            </div>
        </div>
        <div style="display: flex; gap: 1rem; padding: 1rem 2rem; justify-content: flex-end; border-top: 1px solid #f1f5f9; background: #fcfdfe;">
            <button type="button" onclick="document.getElementById('historyStockModal').style.display='none'" style="background: #fff; min-width: 100px; padding: 0.6rem 1.25rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; font-weight: 700; cursor: pointer; color: #64748b; font-size: 0.9rem;">Đóng</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý mở modal sửa
    const editTriggers = document.querySelectorAll('.btn-edit-trigger');
    const editModal = document.getElementById('editHerbModal');
    const editForm = document.getElementById('editHerbForm');

    editTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const category = this.getAttribute('data-category');
            const usageType = this.getAttribute('data-usage_type');
            const unit = this.getAttribute('data-unit');
            const stockQuantity = this.getAttribute('data-stock_quantity');
            const expiryDate = this.getAttribute('data-expiry_date');
            const status = this.getAttribute('data-status');
            const warningNote = this.getAttribute('data-warning_note');
            const description = this.getAttribute('data-description');

            // Cập nhật action của form
            editForm.action = `/admin/medicinal-herbs/${id}`;

            // Điền dữ liệu vào form
            document.getElementById('edit_id').value = id || '';
            document.getElementById('edit_name').value = name || '';
            document.getElementById('edit_category').value = category || 'Dược liệu bốc thuốc';
            document.getElementById('edit_usage_type').value = usageType || 'Sắc';
            document.getElementById('edit_unit').value = unit || '';
            document.getElementById('edit_stock_quantity').value = stockQuantity || '0';
            document.getElementById('edit_expiry_date').value = expiryDate || '';
            document.getElementById('edit_status').value = status || 'active';
            document.getElementById('edit_warning_note').value = warningNote || '';
            document.getElementById('edit_description').value = description || '';

            // Hiển thị modal
            editModal.style.display = 'flex';
        });
    });

    const selectAllCheckbox = document.getElementById('select-all');
    const herbCheckboxes = document.querySelectorAll('.herb-checkbox');
    const bulkDeleteBtn = document.getElementById('btn-bulk-delete');
    const selectedCountSpan = document.getElementById('selected-count');
    const bulkDeleteForm = document.getElementById('bulk-delete-form');

    const confirmModal = document.getElementById('confirmModal');
    const confirmModalCard = document.getElementById('confirmModalCard');
    const confirmModalText = document.getElementById('confirmModalText');
    const confirmCancelBtn = document.getElementById('confirmCancelBtn');
    const confirmOkBtn = document.getElementById('confirmOkBtn');

    // Toggle hiển thị nút xóa hàng loạt
    function toggleBulkDeleteButton() {
        const checkedCount = document.querySelectorAll('.herb-checkbox:checked').length;
        selectedCountSpan.textContent = checkedCount;
        if (checkedCount > 0) {
            bulkDeleteBtn.style.display = 'inline-flex';
        } else {
            bulkDeleteBtn.style.display = 'none';
        }
    }

    // Checkbox chọn tất cả
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            herbCheckboxes.forEach(cb => {
                cb.checked = selectAllCheckbox.checked;
            });
            toggleBulkDeleteButton();
        });
    }

    // Các checkbox dòng lẻ
    herbCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            // Cập nhật checkbox chọn tất cả
            const allChecked = document.querySelectorAll('.herb-checkbox:checked').length === herbCheckboxes.length;
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
            }
            toggleBulkDeleteButton();
        });
    });

    // Mở Custom Modal
    function showModal(text, onConfirm) {
        confirmModalText.innerHTML = text;
        confirmModal.style.display = 'flex';
        setTimeout(() => {
            confirmModal.style.opacity = '1';
            confirmModalCard.style.transform = 'scale(1)';
        }, 10);

        // Đóng modal
        const closeHandler = () => {
            confirmModal.style.opacity = '0';
            confirmModalCard.style.transform = 'scale(0.9)';
            setTimeout(() => {
                confirmModal.style.display = 'none';
            }, 250);
            
            // Cleanup events
            confirmOkBtn.removeEventListener('click', confirmHandler);
            confirmCancelBtn.removeEventListener('click', closeHandler);
        };

        // Xác nhận
        const confirmHandler = () => {
            onConfirm();
            closeHandler();
        };

        confirmCancelBtn.addEventListener('click', closeHandler);
        confirmOkBtn.addEventListener('click', confirmHandler);
    }

    // Xử lý click nút xóa hàng loạt
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const checkedCount = document.querySelectorAll('.herb-checkbox:checked').length;
            showModal(
                `Bạn có chắc chắn muốn xóa <strong>${checkedCount} dược liệu</strong> đã chọn khỏi hệ thống không? Hành động này sẽ không thể hoàn tác.`,
                function() {
                    bulkDeleteForm.submit();
                }
            );
        });
    }

    // Toggle dropdown In & Nhập/Xuất Excel
    window.toggleExportDropdown = function() {
        const menu = document.getElementById('exportDropdownMenu');
        if (menu) {
            menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
        }
    };

    // Đóng dropdown khi click ra ngoài
    document.addEventListener('click', function(event) {
        const wrapper = document.querySelector('.dropdown-export-wrapper');
        const menu = document.getElementById('exportDropdownMenu');
        if (wrapper && menu && !wrapper.contains(event.target)) {
            menu.style.display = 'none';
        }
    });

    window.printHerbList = function() {
        const search = "{{ request('search') }}";
        const usageType = "{{ request('usage_type') }}";
        const url = "{{ route('admin.medicinal-herbs.print-list') }}?search=" + encodeURIComponent(search) + "&usage_type=" + encodeURIComponent(usageType) + "&auto_print=1";
        
        // Tạo iframe ẩn
        const iframe = document.createElement('iframe');
        iframe.style.position = 'fixed';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = 'none';
        iframe.style.opacity = '0';
        iframe.src = url;
        
        document.body.appendChild(iframe);
        
        iframe.onload = function() {
            try {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            } catch (e) {
                console.error("Iframe print error:", e);
            }
            // Xóa iframe sau khi in để giữ DOM sạch
            setTimeout(() => {
                document.body.removeChild(iframe);
            }, 3000);
        };
    };

    // Xử lý mở modal lịch sử thao tác
    const historyTriggers = document.querySelectorAll('.btn-history-trigger');
    const historyModal = document.getElementById('historyStockModal');
    const historyHerbName = document.getElementById('history_herb_name');
    const historyLoading = document.getElementById('history_loading');
    const historyEmpty = document.getElementById('history_empty');
    const historyContent = document.getElementById('history_content');
    const historyList = document.getElementById('history_list');

    historyTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');

            historyHerbName.textContent = name;
            historyLoading.style.display = 'block';
            historyEmpty.style.display = 'none';
            historyContent.style.display = 'none';
            historyList.innerHTML = '';
            
            historyModal.style.display = 'flex';

            fetch(`/admin/medicinal-herbs/${id}/stock-logs`)
                .then(response => response.json())
                .then(data => {
                    historyLoading.style.display = 'none';
                    if (data.logs.length === 0) {
                        historyEmpty.style.display = 'block';
                    } else {
                        data.logs.forEach(log => {
                            const card = document.createElement('div');
                            card.style.background = '#fff';
                            card.style.padding = '0.85rem 1.15rem';
                            card.style.borderRadius = '6px';
                            card.style.border = '1px solid #cbd5e1';
                            card.style.boxShadow = '0 1px 3px rgba(0, 0, 0, 0.04)';
                            card.style.display = 'flex';
                            card.style.flexDirection = 'column';
                            card.style.gap = '0.4rem';
                            
                            let changeBadge = '';
                            if (log.change_quantity > 0) {
                                changeBadge = `<span style="color: #16a34a; font-weight: 800; background: #eef9ee; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.8rem; border: 1px solid #bbf7d0;">+${log.change_quantity} ${data.unit}</span>`;
                            } else if (log.change_quantity < 0) {
                                changeBadge = `<span style="color: #dc2626; font-weight: 800; background: #fef2f2; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.8rem; border: 1px solid #fecaca;">${log.change_quantity} ${data.unit}</span>`;
                            } else {
                                changeBadge = `<span style="color: #64748b; font-weight: 800; background: #f1f5f9; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.8rem; border: 1px solid #e2e8f0;">0 ${data.unit}</span>`;
                            }

                            let detailsHtml = '';
                            if (log.details && log.details.length > 0) {
                                detailsHtml += '<div style="margin-top: 0.4rem; padding-top: 0.4rem; border-top: 1px dashed #e2e8f0; display: flex; flex-direction: column; gap: 0.25rem;">';
                                log.details.forEach(item => {
                                    if (item.old === null || item.old === '') {
                                        detailsHtml += `
                                            <div style="font-size: 0.76rem; color: #475569; display: flex; align-items: center; gap: 0.25rem; flex-wrap: wrap;">
                                                <span style="font-weight: 700; color: #334155;">${item.label}:</span> 
                                                <span style="color: #16a34a; font-weight: 600; background: #eef9ee; padding: 0.1rem 0.35rem; border-radius: 0.2rem; border: 1px solid #bbf7d0; font-size: 0.72rem;">+ ${item.new}</span>
                                            </div>
                                        `;
                                    } else {
                                        detailsHtml += `
                                            <div style="font-size: 0.76rem; color: #475569; display: flex; align-items: center; gap: 0.25rem; flex-wrap: wrap;">
                                                <span style="font-weight: 700; color: #334155;">${item.label}:</span> 
                                                <span style="color: #b91c1c; text-decoration: line-through; background: #fef2f2; padding: 0.1rem 0.35rem; border-radius: 0.2rem; border: 1px solid #fecaca; font-size: 0.72rem;">${item.old}</span> 
                                                <span style="color: #94a3b8; font-size: 0.75rem;">➔</span> 
                                                <span style="color: #16a34a; font-weight: 600; background: #eef9ee; padding: 0.1rem 0.35rem; border-radius: 0.2rem; border: 1px solid #bbf7d0; font-size: 0.72rem;">${item.new}</span>
                                            </div>
                                        `;
                                    }
                                });
                                detailsHtml += '</div>';
                            }

                            card.innerHTML = `
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div style="font-weight: 800; color: #1e3a5f; font-size: 0.92rem; display: flex; align-items: center; gap: 0.35rem;">
                                        <span>📝</span>
                                        <span>${log.action_type_label}</span>
                                    </div>
                                    <div>
                                        ${changeBadge}
                                    </div>
                                </div>
                                ${log.note ? `<div style="font-size: 0.8rem; color: #475569; background: #f8fafc; padding: 0.4rem 0.6rem; border-radius: 4px; border-left: 3px solid #cbd5e1; font-style: italic; margin-top: 0.15rem;">${log.note}</div>` : ''}
                                ${detailsHtml}
                                <div style="margin-top: 0.4rem; padding-top: 0.4rem; border-top: 1px dashed #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; font-size: 0.78rem; color: #64748b;">
                                    <div style="display: flex; gap: 1rem; align-items: center;">
                                        <span>👤 <strong>${log.operator}</strong></span>
                                        <span>📅 ${log.time}</span>
                                    </div>
                                    <div style="display: flex; gap: 0.6rem; align-items: center;">
                                        <span>Trước: <strong>${log.old_quantity} ${data.unit}</strong></span>
                                        <span style="color: #cbd5e1;">➔</span>
                                        <span>Sau: <strong style="color: #0f766e;">${log.new_quantity} ${data.unit}</strong></span>
                                    </div>
                                </div>
                            `;
                            historyList.appendChild(card);
                        });
                        historyContent.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error("Error fetching logs:", error);
                    historyLoading.innerHTML = '<span style="color: #ef4444;">❌ Lỗi khi tải lịch sử. Vui lòng thử lại!</span>';
                });
        });
    });
});
</script>
@endpush

@endsection
