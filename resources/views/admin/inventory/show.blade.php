@extends('layouts.admin')

@section('title', 'Chi tiết Mặt Hàng — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div class="icon-bg" style="width: 44px; height: 44px; background: #eef2ff; color: #4f46e5; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
        <span class="icon">📋</span>
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">{{ $item->name }}</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Chi tiết lô hàng và lịch sử giao dịch</p>
    </div>
</div>
@endsection

@section('header-right')
<a href="{{ route('admin.inventory.index') }}" style="color: #64748b; text-decoration: none; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; background: #fff; padding: 0.6rem 1.2rem; border-radius: 12px; border: 1px solid #f0f3ff;">
    ← Quay lại Danh sách
</a>
@endsection

@section('content')
@php
    $totalBatches = $item->batches->count();
    $activeBatches = $item->batches->where('status', '!=', 'blocked')->count();
    $totalQty = $item->total_available_quantity;
    $allMovements = $item->batches->flatMap->stockMovements->sortByDesc('created_at')->take(10);
@endphp

<div class="inventory-show-container" style="margin-top: -1rem;">

    {{-- Warning for external products --}}
    @if($item->usage_route === 'external')
    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.75rem; padding: 1rem 1.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
        <span style="font-size: 1.5rem;">⚠️</span>
        <div>
            <strong style="color: #991b1b; font-size: 0.95rem;">CẢNH BÁO: DÙNG NGOÀI DA — KHÔNG ĐƯỢC UỐNG</strong>
            @if($item->warning_note)
                <p style="margin: 0.25rem 0 0; font-size: 0.85rem; color: #991b1b;">{{ $item->warning_note }}</p>
            @endif
        </div>
    </div>
    @endif

    {{-- Stats Cards --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline">
                        <span class="icon">📦</span>
                    </div>
                    <span class="stat-title">Tồn Khả Dụng</span>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value" style="color: #1e40af;">{{ number_format($totalQty, 2) }}</h3>
                <span class="stat-badge bg-green-light">{{ $item->unit }}</span>
            </div>
            <div class="stat-footer">
                Tổng từ các lô còn hạn và khả dụng
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline" style="border-color: #4f46e5; background: #eef2ff;">
                        <span class="icon">🏷️</span>
                    </div>
                    <span class="stat-title">Tổng Số Lô</span>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value" style="color: #4f46e5;">{{ $totalBatches }}</h3>
                <span class="stat-badge" style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe;">{{ $activeBatches }} hoạt động</span>
            </div>
            <div class="stat-footer">
                Bao gồm tất cả lô (hết hạn, khóa, chưa rõ)
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline" style="border-color: #059669; background: #ecfdf5;">
                        <span class="icon">📊</span>
                    </div>
                    <span class="stat-title">Thông Tin</span>
                </div>
            </div>
            <div class="stat-body" style="flex-direction: column; align-items: flex-start; gap: 0.4rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem;">
                    <span style="font-weight: 700; color: #475569;">Loại:</span>
                    @if($item->item_type === 'herb')
                        <span class="type-badge type-herb">🌿 Dược liệu</span>
                    @elseif($item->item_type === 'prepared_product')
                        <span class="type-badge type-prepared">💊 Chế phẩm</span>
                    @else
                        <span class="type-badge type-external">🧴 Dùng ngoài</span>
                    @endif
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem;">
                    <span style="font-weight: 700; color: #475569;">Đường dùng:</span>
                    @if($item->usage_route === 'external')
                        <span class="route-badge route-external">Dùng ngoài</span>
                    @else
                        <span class="route-badge route-oral">Uống</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Two column layout --}}
    <div style="display: grid; grid-template-columns: 1fr 380px; gap: 1.5rem;">

        {{-- Left: Batch List --}}
        <div class="main-content-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 0.5rem;">
                    🏷️ Danh sách Lô hàng
                </h3>
                <button type="button" class="btn-add screen-only" style="border: none; cursor: pointer; padding: 0.6rem 1.25rem; font-size: 0.85rem;" onclick="document.getElementById('importBatchModal').style.display='flex'">
                    <span class="icon">+</span> Nhập lô mới
                </button>
            </div>

            <div class="table-container">
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th style="width: 130px;">MÃ LÔ</th>
                            <th style="width: 110px;">HẠN DÙNG</th>
                            <th style="width: 130px;">SỐ LƯỢNG CÒN</th>
                            <th style="width: 140px;">TRẠNG THÁI</th>
                            <th style="width: 160px;" class="screen-only">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($item->batches as $batch)
                        <tr>
                            <td>
                                <span class="fefo-code">{{ $batch->batch_number }}</span>
                            </td>
                            <td>
                                @if($batch->expiry_date)
                                    <span style="font-weight: 700; color: #334155; font-size: 0.82rem;">{{ $batch->expiry_date->format('d/m/Y') }}</span>
                                @else
                                    <span style="color: #d97706; font-weight: 600; font-size: 0.78rem;">Chưa rõ</span>
                                @endif
                            </td>
                            <td>
                                <span class="quantity-display">{{ number_format($batch->quantity_remaining, 2) }} {{ $item->unit }}</span>
                            </td>
                            <td>
                                @if($batch->status === 'blocked')
                                    <span class="status-badge" style="background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;">🔒 Bị khóa</span>
                                @elseif($batch->status === 'unknown_expiry' || !$batch->expiry_date)
                                    <span class="status-badge status-unknown">Chưa rõ HSD</span>
                                @elseif($batch->status === 'expired' || ($batch->expiry_date && $batch->expiry_date->isBefore(now()->startOfDay())))
                                    <span class="status-badge status-expired">Đã hết hạn</span>
                                @elseif($batch->expiry_date && $batch->expiry_date->copy()->startOfDay()->betweenIncluded(now()->startOfDay(), now()->startOfDay()->addMonthsNoOverflow(2)))
                                    <span class="status-badge status-near-expiry">Sắp hết hạn</span>
                                @else
                                    <span class="status-badge status-available">Còn sử dụng</span>
                                @endif
                            </td>
                            <td class="screen-only">
                                <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                                    @if(in_array($batch->status, ['unknown_expiry', 'near_expiry', 'available', 'expired']))
                                        <button class="btn-icon" onclick="document.getElementById('updateExpiryModal{{ $batch->id }}').style.display='flex'" title="Cập nhật hạn dùng">
                                            ✏️ Cập nhật HSD
                                        </button>
                                    @endif

                                    <form action="{{ route('admin.inventory.batch.toggle', $batch->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn thay đổi trạng thái lô này?');">
                                        @csrf
                                        @method('PATCH')
                                        @if($batch->status === 'blocked')
                                            <button type="submit" class="btn-icon" style="color: #16a34a; border-color: #bbf7d0;">🔓 Mở khóa</button>
                                        @else
                                            <button type="submit" class="btn-icon" style="color: #64748b; border-color: #e2e8f0;">🔒 Khóa</button>
                                        @endif
                                    </form>
                                </div>

                                {{-- Update Expiry Modal --}}
                                <div id="updateExpiryModal{{ $batch->id }}" class="modal-overlay" style="display: none;">
                                    <div class="modal-container" style="max-width: 500px;">
                                        <div class="modal-header">
                                            <h2 style="font-size: 1.1rem;">Cập nhật HSD — Lô: {{ $batch->batch_number }}</h2>
                                            <button type="button" class="btn-close-modal" onclick="document.getElementById('updateExpiryModal{{ $batch->id }}').style.display='none'">✕</button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('admin.inventory.batch.update', $batch->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="form-group">
                                                    <label class="form-label">Hạn sử dụng mới <span style="color: #ef4444;">*</span></label>
                                                    <input type="date" name="expiry_date" class="form-input" required value="{{ $batch->expiry_date ? $batch->expiry_date->format('Y-m-d') : '' }}">
                                                    <small style="color: #64748b; font-size: 0.78rem; margin-top: 0.25rem;">Hệ thống sẽ tự động cập nhật trạng thái dựa trên ngày bạn nhập.</small>
                                                </div>
                                                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; justify-content: flex-end;">
                                                    <button type="button" onclick="document.getElementById('updateExpiryModal{{ $batch->id }}').style.display='none'" style="background: #fff; padding: 0.5rem 1.25rem; border-radius: 0.25rem; border: 1px solid #cbd5e1; font-weight: 600; cursor: pointer; color: #64748b;">Hủy</button>
                                                    <button type="submit" style="padding: 0.5rem 1.25rem; border-radius: 0.25rem; background: #2563eb; color: white; border: none; font-weight: 700; cursor: pointer;">Lưu cập nhật</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem; color: #94a3b8; font-weight: 600;">Chưa có lô hàng nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Right: Transaction History --}}
        <div class="main-content-card" style="padding: 1.5rem; height: fit-content;">
            <h3 style="margin: 0 0 1.25rem 0; font-size: 1rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 0.5rem;">
                📊 10 Giao dịch gần nhất
            </h3>

            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @forelse($allMovements as $mv)
                    <div style="padding: 0.75rem; background: #f8fafc; border-radius: 0.5rem; border: 1px solid #f1f5f9;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                            <span style="font-weight: 700; color: #475569; font-size: 0.8rem;">Lô: {{ $mv->batch->batch_number }}</span>
                            <span style="color: #94a3b8; font-size: 0.72rem;">{{ $mv->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span>
                                @if($mv->movement_type === 'opening_balance')
                                    <span class="movement-badge" style="background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;">Số dư đầu kỳ</span>
                                @elseif($mv->movement_type === 'import')
                                    <span class="movement-badge" style="background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0;">Nhập lô mới</span>
                                @elseif($mv->movement_type === 'dispense')
                                    <span class="movement-badge" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;">Xuất kho (Cấp thuốc)</span>
                                @else
                                    <span class="movement-badge" style="background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;">{{ $mv->movement_type }}</span>
                                @endif
                            </span>
                            <span style="font-weight: 800; font-size: 0.9rem; font-family: monospace; {{ $mv->quantity > 0 ? 'color: #16a34a;' : 'color: #dc2626;' }}">
                                {{ $mv->quantity > 0 ? '+' : '' }}{{ number_format($mv->quantity, 2) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 2rem; color: #94a3b8; font-size: 0.85rem;">
                        Chưa có giao dịch nào.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Import Batch Modal --}}
<div id="importBatchModal" class="modal-overlay" style="display: none;">
    <div class="modal-container" style="max-width: 650px;">
        <div class="modal-header">
            <h2>Nhập lô hàng mới: {{ $item->name }}</h2>
            <button type="button" class="btn-close-modal" onclick="document.getElementById('importBatchModal').style.display='none'">✕</button>
        </div>
        <div class="modal-body">
            <form action="{{ route('admin.inventory.batch.store', $item->id) }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                    <div class="form-group">
                        <label class="form-label">Mã lô hàng <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="batch_code" class="form-input" required placeholder="Ví dụ: L001-2026">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Số lượng nhập ({{ $item->unit }}) <span style="color: #ef4444;">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="quantity" class="form-input" required>
                    </div>
                </div>
                <div class="form-group" style="margin-top: 1.25rem;">
                    <label class="form-label">Hạn sử dụng <span style="color: #ef4444;">*</span></label>
                    <input type="date" name="expiry_date" class="form-input" required>
                </div>
                <div class="form-group" style="margin-top: 1.25rem;">
                    <label class="form-label">Ghi chú (Tùy chọn)</label>
                    <textarea name="note" class="form-input" rows="2" placeholder="Nguồn nhập, ghi chú bảo quản..."></textarea>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; justify-content: flex-end; border-top: 1px solid #f1f5f9; padding-top: 1.25rem;">
                    <button type="button" onclick="document.getElementById('importBatchModal').style.display='none'" style="background: #fff; min-width: 100px; padding: 0.6rem 1.5rem; border-radius: 0.25rem; border: 1px solid #cbd5e1; font-weight: 600; cursor: pointer; color: #64748b;">Hủy bỏ</button>
                    <button type="submit" style="min-width: 150px; padding: 0.6rem 1.5rem; border-radius: 0.25rem; background: #2563eb; color: white; border: none; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: 0 2px 6px rgba(37, 99, 235, 0.15);">
                        ✅ Xác nhận Nhập Kho
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Close modals when clicking outside
    document.querySelectorAll('.modal-overlay').forEach(function(modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) this.style.display = 'none';
        });
    });
});
</script>

<style>
.inventory-show-container {
    --primary-green: #2563eb;
    --text-dark: #1e293b;
    --text-muted: #64748b;
    --bg-light: #f8fafc;
    --border-color: #e2e8f0;
}

/* Stats Grid */
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

/* Main content */
.main-content-card {
    background: #fff;
    border-radius: 0.5rem;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.01);
}

.btn-add {
    padding: 0.6rem 1.25rem;
    border-radius: 0.25rem;
    background: #2563eb;
    color: #fff;
    font-weight: 700;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.15);
    font-size: 0.85rem;
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
.type-herb { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
.type-prepared { background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; }
.type-external { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

/* Route badges */
.route-badge {
    display: inline-flex;
    padding: 0.2rem 0.5rem;
    border-radius: 0.125rem;
    font-size: 0.78rem;
    font-weight: 600;
}
.route-oral { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
.route-external { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

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

/* Quantity */
.quantity-display {
    font-weight: 800;
    color: #1e40af;
    font-size: 0.88rem;
    font-family: monospace;
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
.status-available { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
.status-near-expiry { background: #fffbeb; color: #d97706; border: 1px solid #fef3c7; }
.status-expired { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
.status-unknown { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }

/* Movement badge */
.movement-badge {
    display: inline-flex;
    padding: 0.15rem 0.4rem;
    border-radius: 0.125rem;
    font-size: 0.72rem;
    font-weight: 700;
}

/* Button icon */
.btn-icon {
    padding: 0.35rem 0.6rem;
    border-radius: 0.25rem;
    font-size: 0.72rem;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    border: 1px solid #cbd5e1;
    background: #fff;
    color: #475569;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-icon:hover {
    background: #f8fafc;
    border-color: #94a3b8;
    color: #0f172a;
}

/* Modal */
.modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
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
    font-size: 1.15rem;
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

textarea.form-input {
    resize: vertical;
}

@media (max-width: 1024px) {
    .inventory-show-container > div:last-child {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endsection
