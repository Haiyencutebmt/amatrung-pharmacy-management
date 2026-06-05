@extends('layouts.admin')

@section('title', 'Danh sách Bệnh án — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div class="icon-bg" style="width: 44px; height: 44px; background: #eff6ff; color: #2563eb; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
        <span class="icon">📋</span>
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Danh sách bệnh án</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Quản lý và theo dõi hồ sơ bệnh án của bệnh nhân</p>
    </div>
</div>
@endsection

@section('content')
<div class="record-index-container" style="margin-top: -1rem;">

    {{-- Stats Cards --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline" style="border-color: #2563eb; color: #2563eb; background: #eff6ff;">
                        <span class="icon">📋</span>
                    </div>
                    <span class="stat-title">Tổng Bệnh Án</span>
                </div>
                <div class="stat-menu-wrapper">
                    <div class="stat-menu" onclick="toggleStatDropdown(this)">⋮</div>
                    <div class="stat-dropdown">
                        <a href="{{ route('admin.medical-records.index', ['date' => 'all']) }}">Xem tất cả</a>
                        <a href="#">Xuất báo cáo</a>
                    </div>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value">{{ number_format($totalRecords) }}</h3>
                <span class="stat-badge bg-green-light">Toàn bộ</span>
            </div>
            <div class="stat-footer">
                Bao gồm cả <strong>bệnh án cũ</strong> số hóa
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline" style="border-color: #3b82f6; color: #3b82f6; background: #eff6ff;">
                        <span class="icon">📅</span>
                    </div>
                    <span class="stat-title">Tháng Này</span>
                </div>
                <div class="stat-menu-wrapper">
                    <div class="stat-menu" onclick="toggleStatDropdown(this)">⋮</div>
                    <div class="stat-dropdown">
                        <a href="{{ route('admin.medical-records.index', ['date' => 'this_month']) }}">Lọc tháng này</a>
                    </div>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value">{{ number_format($recordsThisMonth) }}</h3>
                <span class="stat-badge" style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;">Bệnh án mới</span>
            </div>
            <div class="stat-footer">
                Được tạo trong <strong>Tháng {{ date('n') }}</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline" style="border-color: #f59e0b; color: #f59e0b; background: #fffbeb;">
                        <span class="icon">☀️</span>
                    </div>
                    <span class="stat-title">Khám Hôm Nay</span>
                </div>
                <div class="stat-menu-wrapper">
                    <div class="stat-menu" onclick="toggleStatDropdown(this)">⋮</div>
                    <div class="stat-dropdown">
                        <a href="{{ route('admin.medical-records.index', ['date' => 'today']) }}">Chỉ xem hôm nay</a>
                    </div>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value">{{ number_format($recordsToday) }}</h3>
                <span class="stat-badge" style="background: #fffbeb; color: #d97706; border: 1px solid #fde68a;">{{ date('d/m/Y') }}</span>
            </div>
            <div class="stat-footer">
                Số ca khám <strong>đang diễn ra</strong>
            </div>
        </div>
    </div>

    {{-- Filters & Table --}}
    <div class="main-content-card">
        <form action="{{ route('admin.medical-records.index') }}" method="GET" class="filter-form">
            <div class="filter-row">
                <div class="search-input-group">
                    <button type="submit" class="icon" title="Tìm kiếm" style="background: none; border: none; cursor: pointer; padding: 0; outline: none;">🔍</button>
                    <input type="text" name="search" placeholder="Tìm mã bệnh án, tên bệnh nhân, SĐT, chẩn đoán... (Nhấn Enter)" value="{{ request('search') }}">
                </div>
                <div class="action-buttons" style="display: flex; gap: 0.5rem; align-items: center;">
                    @if(auth()->user()->hasPermission('medical_records.delete'))
                        <button type="button" id="btnBulkDelete" class="btn-filter screen-only" style="display: none; border: 1px solid #ef4444; color: #ef4444; background: #fef2f2; padding: 0.75rem 1.25rem; font-size: 0.85rem; white-space: nowrap; border-radius: 0.25rem; align-items: center; gap: 0.4rem; cursor: pointer; font-weight: 700;" onclick="showBulkDeleteModal()">
                            <span class="icon">🗑️</span> Xóa đã chọn
                        </button>
                    @endif
                    @if(auth()->user()->hasPermission('medical_records.create'))
                        <button type="button" class="btn-add screen-only" style="border: none; cursor: pointer; padding: 0.75rem 1.25rem;" onclick="document.getElementById('createRecordModal').style.display='flex'">
                            <span class="icon">+</span> Tạo Bệnh Án Mới
                        </button>
                    @endif
                </div>
            </div>
            <div class="filter-bottom-row">
                <div class="filter-item">
                    <label>Thời gian:</label>
                    <select name="date" onchange="this.form.submit()">
                        <option value="today" {{ (request('date') === 'today' || (!request()->has('date') && !request()->has('search') && !request()->has('status') && !request()->has('legacy') && !request()->has('page'))) ? 'selected' : '' }}>Hôm nay</option>
                        <option value="this_week" {{ request('date') === 'this_week' ? 'selected' : '' }}>Tuần này</option>
                        <option value="this_month" {{ request('date') === 'this_month' ? 'selected' : '' }}>Tháng này</option>
                        <option value="all" {{ request('date') === 'all' ? 'selected' : '' }}>Tất cả</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label>Trạng thái:</label>
                    <select name="status" onchange="this.form.submit()">
                        <option value="" {{ request('status') === null ? 'selected' : '' }}>Tất cả</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ kê đơn</option>
                        <option value="prescribed" {{ request('status') === 'prescribed' ? 'selected' : '' }}>Đã kê đơn</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label>Loại hồ sơ:</label>
                    <select name="legacy" onchange="this.form.submit()">
                        <option value="" {{ request('legacy') === null ? 'selected' : '' }}>Tất cả</option>
                        <option value="1" {{ request('legacy') === '1' ? 'selected' : '' }}>Hồ sơ cũ</option>
                    </select>
                </div>

                <a href="{{ route('admin.medical-records.index') }}" class="btn-reset">
                    <span class="icon">🔄</span> Làm mới
                </a>
            </div>
        </form>

        <div class="table-container">
            <table class="patient-table">
                <thead>
                    <tr>
                        <th style="width: 40px;" class="screen-only"><input type="checkbox" id="selectAll" style="accent-color: #2563eb; cursor: pointer; width: 1.1rem; height: 1.1rem;"></th>
                        <th style="width: 90px;">MÃ BA</th>
                        <th style="width: 100px;">NGÀY KHÁM</th>
                        <th style="width: 250px;">BỆNH NHÂN</th>
                        <th>CHẨN ĐOÁN & TRIỆU CHỨNG</th>
                        <th style="width: 120px;">TRẠNG THÁI</th>
                        <th style="width: 160px;">BÁC SĨ KHÁM</th>
                        <th style="width: 90px;" class="screen-only">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                    <tr>
                        <td style="text-align: center;" class="screen-only">
                            <input type="checkbox" class="record-checkbox" value="{{ $record->id }}" style="width: 1.1rem; height: 1.1rem; cursor: pointer; accent-color: #ef4444;" title="Chọn để xóa">
                        </td>
                        <td>
                            <span class="patient-code">{{ $record->record_code ?? '—' }}</span>
                        </td>
                        <td style="font-weight: 600; color: #475569;">
                            {{ $record->visit_date->format('d/m/Y') }}
                            @if($record->visit_date->isToday())
                                <span style="background: #ef4444; color: #fff; padding: 0.05rem 0.25rem; border-radius: 2px; font-size: 0.6rem; font-weight: 800; margin-left: 0.25rem;">MỚI</span>
                            @endif
                            @if($record->is_legacy_data)
                                <span style="background: #fef3c7; color: #92400e; padding: 0.05rem 0.25rem; border-radius: 2px; font-size: 0.6rem; font-weight: 800; margin-left: 0.25rem;" title="Dữ liệu bệnh án cũ từ hồ sơ giấy">CŨ</span>
                            @endif
                        </td>
                        <td>
                            <div class="patient-name-cell">
                                <span class="name">{{ $record->patient->full_name ?? 'N/A' }}</span>
                                @if($record->patient)
                                    <span style="font-size: 0.7rem; color: #3b82f6; background: #eff6ff; padding: 0.1rem 0.3rem; border-radius: 2px; font-weight: 700; border: 1px solid #bfdbfe; margin-left: 0.3rem;">{{ $record->patient->patient_code }}</span>
                                @endif
                            </div>
                            @if($record->patient && $record->patient->phone)
                                <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.1rem;">{{ $record->patient->phone }}</div>
                            @elseif($record->patient && $record->patient->guardian_phone)
                                <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.1rem;">{{ $record->patient->guardian_phone }} <small>({{ $record->patient->guardian_name }})</small></div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #1e3a5f;" title="{{ $record->diagnosis }}">{{ Str::limit($record->diagnosis, 60) }}</div>
                            <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.1rem;" title="{{ $record->symptoms }}">{{ Str::limit($record->symptoms, 70) }}</div>
                        </td>
                        <td>
                            @if($record->prescriptions_count > 0)
                                <span style="color: #10b981; font-weight: 700; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                                    ✔ Đã kê đơn
                                </span>
                            @else
                                @if(auth()->user()->hasPermission('prescriptions.create'))
                                    <a href="{{ route('admin.medical-records.show', $record) }}" style="color: #d97706; font-weight: 700; font-size: 0.8rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;" title="Nhấn để kê đơn">
                                        ⏳ Chờ kê đơn
                                    </a>
                                @else
                                    <span style="color: #d97706; font-weight: 700; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                                        ⏳ Chờ kê đơn
                                    </span>
                                @endif
                            @endif
                        </td>
                        <td style="color: #475569; font-weight: 500;">
                            {{ $record->staff->name ?? 'N/A' }}
                        </td>
                        <td class="screen-only">
                            <div class="action-cell">
                                <a href="{{ route('admin.medical-records.show', $record) }}" class="btn-icon view" title="Xem chi tiết bệnh án">
                                    <span>👁️</span> Xem
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: #64748b; padding: 3rem 1rem; font-weight: 500;">
                            Không tìm thấy bệnh án nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
        <div class="pagination-area">
            <p class="summary">Hiển thị {{ $records->firstItem() }} đến {{ $records->lastItem() }} của {{ $records->total() }} bệnh án</p>
            <div class="pagination-controls">
                {{ $records->withQueryString()->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

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
    gap: 1rem;
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

.patient-code {
    background: #eff6ff;
    color: #2563eb;
    padding: 0.25rem 0.5rem;
    border-radius: 0.125rem;
    font-weight: 700;
    font-size: 0.8rem;
    border: 1px solid #bfdbfe;
    font-family: monospace;
}

.patient-name-cell {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.patient-name-cell .name {
    font-weight: 700;
    color: #0f172a;
}

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
    border-radius: 0.25rem;
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

@keyframes modalSlideIn {
    from { opacity: 0; transform: translateY(-20px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

@keyframes pulse {
    0% { transform: scale(1); opacity: 1; box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
    70% { transform: scale(1.05); opacity: 0.9; box-shadow: 0 0 0 4px rgba(239, 68, 68, 0); }
    100% { transform: scale(1); opacity: 1; box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}

@media (max-width: 768px) {
    .modal-grid-4 {
        grid-template-columns: 1fr !important;
    }
    .modal-grid-2 {
        grid-template-columns: 1fr !important;
    }
}

.patient-select-option:hover {
    background-color: #f8fafc;
}
.patient-case-type-panel,
.patient-tcm-panel {
    margin-bottom: 1rem;
    border: 1px solid #d9f5e4;
    border-radius: 0.6rem;
    padding: 0.9rem;
    background: linear-gradient(135deg, #f7fef9 0%, #ffffff 100%);
}
.patient-section-title {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    margin-bottom: 0.8rem;
    color: #15803d;
    font-size: 0.9rem;
    font-weight: 800;
}
.patient-section-title-green {
    color: #047857;
}
.patient-case-type-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1rem;
}
.patient-case-type-card {
    position: relative;
    display: grid;
    grid-template-columns: auto auto 1fr;
    align-items: center;
    gap: 0.75rem;
    min-height: 74px;
    padding: 0.8rem 1rem;
    border: 1px solid #dbe5ef;
    border-radius: 0.5rem;
    background: #fff;
    color: #1e293b;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}
.patient-case-type-card:hover {
    border-color: #86efac;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.07);
    transform: translateY(-1px);
}
.patient-case-type-card input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.patient-case-radio-dot {
    width: 17px;
    height: 17px;
    border: 2px solid #94a3b8;
    border-radius: 9999px;
    box-shadow: inset 0 0 0 4px #fff;
}
.patient-case-type-card:has(input:checked) {
    border-color: #22c55e;
    background: #f0fdf4;
    box-shadow: 0 0 0 1px rgba(34, 197, 94, 0.16);
}
.patient-case-type-card:has(input:checked) .patient-case-radio-dot {
    background: #059669;
    border-color: #059669;
}
.patient-case-icon {
    width: 38px;
    height: 38px;
    border-radius: 0.45rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}
.patient-case-icon-green,
.patient-tcm-icon {
    background: #dcfce7;
    color: #16a34a;
}
.patient-case-icon-purple {
    background: #f3e8ff;
    color: #7e22ce;
}
.patient-case-icon-blue {
    background: #dbeafe;
    color: #2563eb;
}
.patient-case-copy {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    line-height: 1.35;
}
.patient-case-copy strong {
    font-size: 0.88rem;
    font-weight: 800;
}
.patient-case-copy small {
    color: #475569;
    font-size: 0.78rem;
    font-weight: 700;
}
.patient-tcm-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.75rem 1rem;
}
@media (max-width: 768px) {
    .patient-tcm-grid {
        grid-template-columns: 1fr;
    }
}
.patient-tcm-card {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 0.75rem;
    align-items: flex-start;
    padding: 0.75rem;
    border: 1px solid #dbe5ef;
    border-radius: 0.5rem;
    background: #fff;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}
.patient-tcm-icon {
    width: 34px;
    height: 34px;
    border-radius: 0.45rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}
.patient-tcm-body label {
    display: block;
    margin-bottom: 0.2rem;
    color: #1e293b;
    font-size: 0.84rem;
    font-weight: 800;
}
.patient-tcm-body textarea {
    width: 100%;
    min-height: 46px;
    padding: 0.3rem 0.1rem;
    border: none;
    outline: none;
    resize: vertical;
    box-sizing: border-box;
    color: #1e293b;
    font-size: 0.84rem;
    line-height: 1.45;
    background: transparent;
}
.patient-tcm-body textarea::placeholder {
    color: #94a3b8;
}
.patient-bottom-field-label {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    margin-bottom: 0.4rem;
    color: #334155;
    font-size: 0.82rem;
    font-weight: 800;
}
.patient-bottom-field-label span {
    width: 28px;
    height: 28px;
    border-radius: 0.4rem;
    background: #ecfdf5;
    color: #16a34a;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #bbf7d0;
}
.patient-bottom-field-label strong {
    color: #ef4444;
}
</style>

{{-- MODAL: Tạo Bệnh Án Mới --}}
<div id="createRecordModal" style="display:none; position:fixed; inset:0; z-index:1000; overflow-y:auto; background:rgba(15,23,42,0.45); backdrop-filter:blur(4px); padding:2rem 0; flex-direction:column; align-items:center; justify-content:flex-start; box-sizing:border-box;">
    <div style="background:#fff; border-radius:0.5rem; width:95%; max-width:1250px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); animation: modalSlideIn 0.25s ease-out; margin:0 auto; padding:1.5rem 2rem; border:1px solid #5eb542; box-sizing:border-box;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; border-bottom:1px solid #f1f5f9; padding-bottom:1rem;">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:40px; height:40px; background:#f0fdf4; color:#16a34a; border-radius:0.25rem; display:flex; align-items:center; justify-content:center; font-size:1.2rem;">🩺</div>
                <div>
                    <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:#0f172a;">Tạo Bệnh Án Mới</h3>
                    <p style="margin:0; font-size:0.8rem; color:#64748b;">Lập bệnh án cho lượt khám hiện tại</p>
                </div>
            </div>
            <button onclick="document.getElementById('createRecordModal').style.display='none'" style="background:none; border:none; font-size:1.5rem; color:#94a3b8; cursor:pointer; width:36px; height:36px; border-radius:0.25rem; display:flex; align-items:center; justify-content:center;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">&times;</button>
        </div>
        <form action="{{ route('admin.medical-records.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            {{-- Row 1: General Info (4 columns) --}}
            <div class="modal-grid-4" style="display:grid; grid-template-columns:2fr 1.5fr 1fr 1fr; gap:1rem; margin-bottom:1rem;">
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:#475569; margin-bottom:0.4rem;">Chọn Bệnh Nhân <span style="color:#ef4444;">*</span></label>
                    <div style="position:relative; width:100%;">
                        {{-- Search Input field --}}
                        <input type="text" id="patient_search_input" placeholder="Gõ tên hoặc số điện thoại..." required autocomplete="off" style="width:100%; padding:0.6rem 0.8rem; border:1px solid #cbd5e1; border-radius:0.25rem; font-size:0.9rem; color:#1e293b; height:38px; box-sizing:border-box;">
                        
                        {{-- Hidden Input for original Form POST value --}}
                        <input type="hidden" name="patient_id" id="patient_hidden_id" required>

                        {{-- Dropdown arrow --}}
                        <div id="patient_dropdown_arrow" style="position:absolute; right:0.75rem; top:50%; transform:translateY(-50%); pointer-events:none; color:#64748b; font-size:0.75rem; transition:transform 0.2s;">▼</div>

                        {{-- Custom Dropdown Container --}}
                        <div id="patient_search_dropdown" style="display:none; position:absolute; top:100%; left:0; right:0; max-height:220px; overflow-y:auto; background:#fff; border:1px solid #cbd5e1; border-radius:0.25rem; z-index:1100; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); margin-top:0.25rem;">
                            @foreach($patients as $p)
                                <div class="patient-select-option" data-id="{{ $p->id }}" data-name="{{ $p->full_name }}" data-code="{{ $p->patient_code }}" data-phone="{{ $p->phone }}" style="padding:0.6rem 0.8rem; cursor:pointer; font-size:0.88rem; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; transition:background 0.15s;">
                                    <div>
                                        <strong style="color:#0f172a;">{{ $p->full_name }}</strong>
                                        <span style="display:inline-block; margin-left:0.4rem; padding:0.1rem 0.3rem; background:#f1f5f9; color:#64748b; font-size:0.75rem; border-radius:0.125rem;">{{ $p->patient_code }}</span>
                                    </div>
                                    @if($p->phone)
                                        <span style="font-size:0.8rem; color:#64748b; font-family:monospace;">📞 {{ $p->phone }}</span>
                                    @endif
                                </div>
                            @endforeach
                            <div id="patient_no_results" style="display:none; padding:0.75rem 1rem; text-align:center; color:#94a3b8; font-size:0.85rem;">Không tìm thấy bệnh nhân</div>
                        </div>
                    </div>
                </div>
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:#475569; margin-bottom:0.4rem;">Ngày khám <span style="color:#ef4444;">*</span></label>
                    <input type="date" name="visit_date" value="{{ date('Y-m-d') }}" required style="width:100%; padding:0.6rem 0.8rem; border:1px solid #cbd5e1; border-radius:0.25rem; font-size:0.9rem; color:#1e293b; box-sizing:border-box; height:38px;">
                </div>
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:#475569; margin-bottom:0.4rem;">Cân nặng (kg)</label>
                    <input type="number" name="weight" step="0.1" min="0" max="500" placeholder="VD: 55.5" style="width:100%; padding:0.6rem 0.8rem; border:1px solid #cbd5e1; border-radius:0.25rem; font-size:0.9rem; color:#1e293b; box-sizing:border-box; height:38px;">
                </div>
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:#475569; margin-bottom:0.4rem;">Chiều cao (cm)</label>
                    <input type="number" name="height" step="0.1" min="0" max="300" placeholder="VD: 160" style="width:100%; padding:0.6rem 0.8rem; border:1px solid #cbd5e1; border-radius:0.25rem; font-size:0.9rem; color:#1e293b; box-sizing:border-box; height:38px;">
                </div>
            </div>

            {{-- Row: Case Type --}}
            <div class="patient-case-type-panel">
                <div class="patient-section-title">
                    <span>🛡️</span>
                    Phân loại ca khám bệnh
                </div>
                <div class="patient-case-type-grid">
                    <label class="patient-case-type-card patient-case-type-card-green">
                        <input type="radio" name="case_type" value="normal" checked onchange="toggleCaseTypeModal(this.value)">
                        <span class="patient-case-radio-dot"></span>
                        <span class="patient-case-icon patient-case-icon-green">🩺</span>
                        <span class="patient-case-copy">
                            <strong>Khám thông thường</strong>
                            <small>(Bốc thuốc uống...)</small>
                        </span>
                    </label>
                    <label class="patient-case-type-card patient-case-type-card-purple">
                        <input type="radio" name="case_type" value="musculoskeletal" onchange="toggleCaseTypeModal(this.value)">
                        <span class="patient-case-radio-dot"></span>
                        <span class="patient-case-icon patient-case-icon-purple">🦴</span>
                        <span class="patient-case-copy">
                            <strong>Xương khớp - Chấn thương - Trị liệu ngoài</strong>
                        </span>
                    </label>
                    <label class="patient-case-type-card patient-case-type-card-blue">
                        <input type="radio" name="case_type" value="combined" onchange="toggleCaseTypeModal(this.value)">
                        <span class="patient-case-radio-dot"></span>
                        <span class="patient-case-icon patient-case-icon-blue">🔄</span>
                        <span class="patient-case-copy">
                            <strong>Khám kết hợp cả hai</strong>
                        </span>
                    </label>
                </div>
            </div>

            {{-- Row: Traditional Diagnosis Helper --}}
            <div id="traditional_exam_modal" class="patient-tcm-panel">
                <div class="patient-section-title patient-section-title-green">
                    <span>🌿</span>
                    Tứ chẩn & Biện chứng
                </div>
                <div class="patient-tcm-grid">
                    <div class="patient-tcm-card">
                        <span class="patient-tcm-icon">💬</span>
                        <div class="patient-tcm-body">
                            <label for="tcm_inquiry_modal">Hỏi bệnh</label>
                            <textarea id="tcm_inquiry_modal" name="tcm_inquiry" rows="2" oninput="syncTraditionalExam()" placeholder="Ghi nhận diễn biến bệnh, hoàn cảnh, thói quen, khẩu vị, giấc ngủ..."></textarea>
                        </div>
                    </div>
                    <div class="patient-tcm-card">
                        <span class="patient-tcm-icon">👁️</span>
                        <div class="patient-tcm-body">
                            <label for="tcm_observation_modal">Vọng + Văn chẩn</label>
                            <textarea id="tcm_observation_modal" name="tcm_observation" rows="2" oninput="syncTraditionalExam()" placeholder="Quan sát thần sắc, hình thái, lưỡi, rêu lưỡi, sắc mặt, khí sắc..."></textarea>
                        </div>
                    </div>
                    <div class="patient-tcm-card">
                        <span class="patient-tcm-icon">〰️</span>
                        <div class="patient-tcm-body">
                            <label for="tcm_pulse_modal">Bắt mạch</label>
                            <textarea id="tcm_pulse_modal" name="tcm_pulse" rows="2" oninput="syncTraditionalExam()" placeholder="Ghi nhận mạch tượng: tả - hữu, phù - trầm, khẩn - hoãn..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Row: Musculoskeletal Special Fields (Hidden by default) --}}
            <div id="musculoskeletal_fields_modal" style="display: none; margin-bottom: 1rem; border: 1px solid #f87171; border-radius: 0.25rem; padding: 1.25rem; background: #fffcfc;">
                <h4 style="margin: 0 0 1rem; font-size: 0.95rem; color: #b91c1c; font-weight: 800; display: flex; align-items: center; gap: 0.4rem; border-bottom: 1px solid #fee2e2; padding-bottom: 0.5rem;">
                    <span>🦴</span> Khám Xương Khớp & Chấn thương
                </h4>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Loại tổn thương / Bệnh xương khớp</label>
                        <select name="injury_type" onchange="updateRecordFieldsModal()" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; background: #fff;">
                            <option value="">-- Chọn loại tổn thương --</option>
                            <option value="bong_gan">Bong gân</option>
                            <option value="trat_khop">Trật khớp</option>
                            <option value="nghi_gay_xuong">Nghi gãy xương / Rạn xương nhẹ</option>
                            <option value="dau_vai_gay">Đau vai gáy</option>
                            <option value="dau_lung">Đau lưng / Thoái hóa cột sống</option>
                            <option value="dau_goi">Đau khớp gối</option>
                            <option value="khac">Loại tổn thương khác</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Vị trí chấn thương / Vùng bị đau</label>
                        <input type="text" name="injury_location" placeholder="VD: Khớp cổ chân trái, Đầu gối phải..." style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; box-sizing: border-box;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Nguyên nhân chấn thương</label>
                        <input type="text" name="injury_cause" placeholder="VD: Ngã xe, mang vác vật nặng..." style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Mức độ chấn thương</label>
                        <select name="pain_level" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; background: #fff;">
                            <option value="">-- Chọn mức độ chấn thương --</option>
                            <option value="3">Nhẹ</option>
                            <option value="5">Trung bình</option>
                            <option value="8">Nặng</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Dấu hiệu lâm sàng bên ngoài</label>
                        <textarea name="clinical_signs" placeholder="VD: Sưng nề to vùng cổ chân, bầm tím dưới da..." rows="2" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; resize: vertical; box-sizing: border-box;"></textarea>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Kết quả sờ nắn / Nắm chỉnh</label>
                        <textarea name="palpation_result" placeholder="VD: Ấn đau nhói..." rows="2" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; resize: vertical; box-sizing: border-box;"></textarea>
                    </div>
                </div>

                <div style="margin-bottom: 0.5rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Xem ảnh chụp phim (nếu có)</label>
                    <input type="text" name="xray_note" placeholder="VD: Phim mang từ viện về: Khe khớp bình thường, không rạn gãy..." style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; box-sizing: border-box;">
                </div>
            </div>

            {{-- Row 2: Symptoms & Diagnosis (2 columns side-by-side) --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                <div id="symptoms_col_modal">
                    <label class="patient-bottom-field-label"><span>📋</span> Triệu chứng <strong>*</strong></label>
                    <textarea id="symptoms_modal" name="symptoms" required rows="3" oninput="markTraditionalFieldManual('symptoms')" placeholder="Ghi nhận lời khai và triệu chứng của bệnh nhân..." style="width:100%; padding:0.6rem 0.8rem; border:1px solid #cbd5e1; border-radius:0.25rem; font-size:0.9rem; color:#1e293b; resize:vertical; box-sizing:border-box;"></textarea>
                </div>
                <div id="diagnosis_col_modal" style="display:none;">
                    <label class="patient-bottom-field-label"><span>🎯</span> Chẩn đoán <small style="color:#94a3b8;">(có thể bổ sung sau)</small></label>
                    <textarea id="diagnosis_modal" name="diagnosis" rows="3" oninput="markTraditionalFieldManual('diagnosis')" placeholder="Có thể để trống để AI hỗ trợ nhận định sơ bộ ở trang chi tiết..." style="width:100%; padding:0.6rem 0.8rem; border:1px solid #cbd5e1; border-radius:0.25rem; font-size:0.9rem; color:#1e293b; resize:vertical; box-sizing:border-box;"></textarea>
                </div>
            </div>

            {{-- Footer Action Buttons --}}
            <div style="display:flex; gap:1rem; justify-content:flex-end; border-top:1px solid #f1f5f9; padding-top:1.25rem;">
                <button type="button" onclick="document.getElementById('createRecordModal').style.display='none'" style="padding:0.6rem 1.5rem; background:#fff; color:#64748b; border:1px solid #cbd5e1; border-radius:0.25rem; font-size:0.9rem; font-weight:600; cursor:pointer;">Hủy</button>
                <button type="submit" style="padding:0.6rem 1.5rem; background:#5eb542; color:#fff; border:none; border-radius:0.25rem; font-size:0.9rem; font-weight:700; cursor:pointer; box-shadow:0 2px 6px rgba(94,181,66,0.15);">Lưu Bệnh Án</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Xác nhận xóa nhiều --}}
<div id="bulkDeleteConfirmModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.4); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="modal-content" style="background: #fff; padding: 2rem; border-radius: 1rem; width: 400px; max-width: 90%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); text-align: center; animation: modalSlideIn 0.3s ease-out;">
        <div style="width: 60px; height: 60px; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
            <span style="font-size: 1.8rem;">🗑️</span>
        </div>
        <h3 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">Xác nhận xóa bệnh án</h3>
        <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 2rem; line-height: 1.5;">
            Bạn đang chọn xóa <strong id="bulkDeleteCount" style="color: #ef4444;">0</strong> bệnh án. 
            Hành động này không thể hoàn tác. Các bệnh án đã kê đơn thuốc sẽ không bị xóa.
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <button type="button" onclick="document.getElementById('bulkDeleteConfirmModal').style.display='none'" style="padding: 0.75rem 1.5rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; background: #fff; color: #64748b; font-weight: 600; cursor: pointer;">Hủy bỏ</button>
            <button type="button" onclick="executeBulkDelete()" style="padding: 0.75rem 1.5rem; border-radius: 0.5rem; border: none; background: #ef4444; color: #fff; font-weight: 600; cursor: pointer;">Xóa dữ liệu</button>
        </div>
    </div>
</div>

<form id="bulkDeleteForm" action="{{ route('admin.medical-records.bulk-destroy') }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function getTraditionalExamValue(id) {
    return (document.getElementById(id)?.value || '').trim();
}

function markTraditionalFieldManual(field) {
    const input = field === 'diagnosis'
        ? document.getElementById('diagnosis_modal')
        : document.getElementById('symptoms_modal');

    if (input) {
        input.dataset.tcmAuto = '0';
    }
}

function syncTraditionalExam() {
    const caseType = document.querySelector('#createRecordModal input[name="case_type"]:checked')?.value || 'normal';
    if (caseType === 'musculoskeletal') {
        return;
    }

    const symptomsInput = document.getElementById('symptoms_modal');
    const diagnosisInput = document.getElementById('diagnosis_modal');
    const inquiry = getTraditionalExamValue('tcm_inquiry_modal');
    const observation = getTraditionalExamValue('tcm_observation_modal');
    const pulse = getTraditionalExamValue('tcm_pulse_modal');
    const pattern = getTraditionalExamValue('tcm_pattern_modal');

    const symptomsText = [
        inquiry ? `Hỏi bệnh: ${inquiry}` : '',
        observation ? `Vọng + Văn chẩn: ${observation}` : '',
        pulse ? `Bắt mạch: ${pulse}` : '',
    ].filter(Boolean).join("\n");

    if (symptomsInput && symptomsText && (!symptomsInput.value.trim() || symptomsInput.dataset.tcmAuto === '1')) {
        symptomsInput.value = symptomsText;
        symptomsInput.dataset.tcmAuto = '1';
    }

    if (symptomsInput && !symptomsText && symptomsInput.dataset.tcmAuto === '1') {
        symptomsInput.value = '';
    }

    if (diagnosisInput && pattern && (!diagnosisInput.value.trim() || diagnosisInput.dataset.tcmAuto === '1')) {
        diagnosisInput.value = pattern;
        diagnosisInput.dataset.tcmAuto = '1';
    }

    if (diagnosisInput && !pattern && diagnosisInput.dataset.tcmAuto === '1') {
        diagnosisInput.value = '';
    }
}

function updateRecordFieldsModal() {
    const caseType = document.querySelector('#createRecordModal input[name="case_type"]:checked')?.value || 'normal';
    const injuryTypeSelect = document.querySelector('#createRecordModal select[name="injury_type"]');
    const injuryType = injuryTypeSelect ? injuryTypeSelect.value : '';

    const symptomsCol = document.getElementById('symptoms_col_modal');
    const diagnosisCol = document.getElementById('diagnosis_col_modal');
    const gridRow = symptomsCol?.parentElement;
    const traditionalExam = document.getElementById('traditional_exam_modal');

    const symptomsInput = document.querySelector('#createRecordModal textarea[name="symptoms"]');
    const diagnosisInput = document.querySelector('#createRecordModal textarea[name="diagnosis"]');

    if (traditionalExam) {
        traditionalExam.style.display = caseType === 'musculoskeletal' ? 'none' : 'block';
    }

    if (caseType === 'musculoskeletal') {
        // Hide symptoms
        if (symptomsCol) symptomsCol.style.display = 'none';
        if (symptomsInput) {
            symptomsInput.removeAttribute('required');
            symptomsInput.value = "Khám Xương khớp - Chấn thương"; // satisfies Laravel validation
        }

        // Show/Hide diagnosis based on injury type
        if (injuryType === 'khac') {
            if (diagnosisCol) diagnosisCol.style.display = 'none';
            if (diagnosisInput) {
                if (diagnosisInput.value === 'Khám Xương khớp - Chấn thương' || diagnosisInput.value === '' || diagnosisInput.value.startsWith('Bong gân') || diagnosisInput.value.startsWith('Trật khớp') || diagnosisInput.value.startsWith('Nghi gãy xương') || diagnosisInput.value.startsWith('Đau vai gáy') || diagnosisInput.value.startsWith('Đau lưng') || diagnosisInput.value.startsWith('Đau khớp gối')) {
                    diagnosisInput.value = '';
                }
            }
        } else {
            if (diagnosisCol) diagnosisCol.style.display = 'none';
            if (diagnosisInput) {
                diagnosisInput.removeAttribute('required');
                let injuryText = "Khám Xương khớp - Chấn thương";
                if (injuryTypeSelect && injuryTypeSelect.value) {
                    const opt = injuryTypeSelect.options[injuryTypeSelect.selectedIndex];
                    injuryText = opt.text;
                }
                diagnosisInput.value = injuryText;
            }
        }
    } else {
        // General: show both
        if (symptomsCol) symptomsCol.style.display = 'block';
        if (symptomsInput) {
            symptomsInput.setAttribute('required', 'required');
            if (symptomsInput.value === 'Khám Xương khớp - Chấn thương') {
                symptomsInput.value = '';
            }
        }

        if (diagnosisCol) diagnosisCol.style.display = 'none';
        if (diagnosisInput) {
            if (diagnosisInput.value === 'Khám Xương khớp - Chấn thương' || diagnosisInput.value.startsWith('Bong gân') || diagnosisInput.value.startsWith('Trật khớp') || diagnosisInput.value.startsWith('Nghi gãy xương') || diagnosisInput.value.startsWith('Đau vai gáy') || diagnosisInput.value.startsWith('Đau lưng') || diagnosisInput.value.startsWith('Đau khớp gối')) {
                diagnosisInput.value = '';
            }
        }
    }

    // Adjust grid-template-columns dynamically
    if (gridRow) {
        const symptomsVisible = symptomsCol && symptomsCol.style.display !== 'none';
        const diagnosisVisible = diagnosisCol && diagnosisCol.style.display !== 'none';

        if (symptomsVisible && diagnosisVisible) {
            gridRow.style.display = 'grid';
            gridRow.style.gridTemplateColumns = '1fr 1fr';
        } else if (symptomsVisible || diagnosisVisible) {
            gridRow.style.display = 'grid';
            gridRow.style.gridTemplateColumns = '1fr';
        } else {
            gridRow.style.display = 'none'; // both hidden
        }
    }

    syncTraditionalExam();
}

function toggleCaseTypeModal(value) {
    const musculoskeletalBox = document.getElementById('musculoskeletal_fields_modal');
    if (musculoskeletalBox) {
        musculoskeletalBox.style.display = (value === 'musculoskeletal' || value === 'combined') ? 'block' : 'none';
    }
    updateRecordFieldsModal();
}

document.addEventListener('DOMContentLoaded', function() {
    updateRecordFieldsModal();
});

document.getElementById('createRecordModal')?.addEventListener('click', function(e) { if(e.target===this) this.style.display='none'; });
document.getElementById('bulkDeleteConfirmModal')?.addEventListener('click', function(e) { if(e.target===this) this.style.display='none'; });
document.addEventListener('keydown', function(e) { 
    if(e.key==='Escape') { 
        var m1=document.getElementById('createRecordModal'); if(m1) m1.style.display='none'; 
        var m2=document.getElementById('bulkDeleteConfirmModal'); if(m2) m2.style.display='none';
    } 
});

function toggleBulkDeleteBtn() {
    var checkedCount = document.querySelectorAll('.record-checkbox:checked').length;
    var btn = document.getElementById('btnBulkDelete');
    if (btn) {
        btn.style.display = checkedCount > 0 ? 'inline-flex' : 'none';
    }
}

document.getElementById('selectAll')?.addEventListener('change', function(e) {
    document.querySelectorAll('.record-checkbox').forEach(cb => cb.checked = e.target.checked);
    toggleBulkDeleteBtn();
});

document.querySelectorAll('.record-checkbox').forEach(cb => {
    cb.addEventListener('change', toggleBulkDeleteBtn);
});

function showBulkDeleteModal() {
    var checked = document.querySelectorAll('.record-checkbox:checked');
    if (checked.length === 0) return;
    
    document.getElementById('bulkDeleteCount').innerText = checked.length;
    document.getElementById('bulkDeleteConfirmModal').style.display = 'flex';
}

function executeBulkDelete() {
    var checked = document.querySelectorAll('.record-checkbox:checked');
    var form = document.getElementById('bulkDeleteForm');
    form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
    checked.forEach(cb => {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = cb.value;
        form.appendChild(input);
    });
    form.submit();
}

function toggleStatDropdown(element) {
    document.querySelectorAll('.stat-dropdown.show').forEach(dropdown => {
        if (dropdown !== element.nextElementSibling) {
            dropdown.classList.remove('show');
        }
    });
    element.nextElementSibling.classList.toggle('show');
}

document.addEventListener('click', function(event) {
    if (!event.target.closest('.stat-menu-wrapper')) {
        document.querySelectorAll('.stat-dropdown.show').forEach(dropdown => {
            dropdown.classList.remove('show');
        });
    }
});

// Patient Search Autocomplete logic
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('patient_search_input');
    const hiddenId = document.getElementById('patient_hidden_id');
    const dropdown = document.getElementById('patient_search_dropdown');
    const arrow = document.getElementById('patient_dropdown_arrow');
    const options = document.querySelectorAll('.patient-select-option');
    const noResults = document.getElementById('patient_no_results');

    function removeAccents(str) {
        if (!str) return '';
        return str.normalize('NFD')
                  .replace(/[\u0300-\u036f]/g, '')
                  .replace(/đ/g, 'd')
                  .replace(/Đ/g, 'D');
    }

    if (searchInput && dropdown) {
        // Show dropdown on focus
        searchInput.addEventListener('focus', function() {
            dropdown.style.display = 'block';
            arrow.style.transform = 'translateY(-50%) rotate(180deg)';
            filterOptions();
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
                arrow.style.transform = 'translateY(-50%)';
                
                // If user typed something invalid without selecting, restore valid state
                if (!hiddenId.value) {
                    searchInput.value = '';
                } else {
                    const activeOption = document.querySelector(`.patient-select-option[data-id="${hiddenId.value}"]`);
                    if (activeOption) {
                        const name = activeOption.getAttribute('data-name');
                        const code = activeOption.getAttribute('data-code');
                        searchInput.value = `${name} (${code})`;
                    }
                }
            }
        });

        // Filter options on input
        searchInput.addEventListener('input', function() {
            hiddenId.value = ''; // Reset ID while typing
            dropdown.style.display = 'block';
            arrow.style.transform = 'translateY(-50%) rotate(180deg)';
            filterOptions();
        });

        function filterOptions() {
            const query = removeAccents(searchInput.value.trim().toLowerCase());
            let count = 0;

            options.forEach(opt => {
                const name = removeAccents(opt.getAttribute('data-name').toLowerCase());
                const code = opt.getAttribute('data-code').toLowerCase();
                const phone = opt.getAttribute('data-phone') ? opt.getAttribute('data-phone').toLowerCase() : '';

                if (name.includes(query) || code.includes(query) || phone.includes(query)) {
                    opt.style.display = 'flex';
                    count++;
                } else {
                    opt.style.display = 'none';
                }
            });

            if (count === 0) {
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
            }
        }

        // Option clicked
        options.forEach(opt => {
            opt.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const code = this.getAttribute('data-code');

                hiddenId.value = id;
                searchInput.value = `${name} (${code})`;
                
                dropdown.style.display = 'none';
                arrow.style.transform = 'translateY(-50%)';
            });
        });
    }
});
</script>
@endsection
