@extends('layouts.admin')

@section('title', 'Phiếu bán lẻ ' . $retailOrder->order_code . ' — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div class="icon-bg" style="width: 44px; height: 44px; background: #ecfdf5; color: #16a34a; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
        <span class="icon">🛒</span>
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Phiếu xuất kho {{ $retailOrder->order_code }}</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Ngày tạo: {{ $retailOrder->created_at->format('d/m/Y H:i') }}</p>
    </div>
</div>
@endsection

@section('content')
<style>
    .retail-show { font-family: 'Inter', system-ui, sans-serif; width: 100%; margin-top: -1rem; }
    .show-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; align-items: start; }
    .info-card {
        background: #fff; border: 1px solid #f1f5f9; border-radius: 1rem; padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    .info-card h3 {
        margin: 0 0 1.25rem 0; font-size: 1.1rem; font-weight: 700; color: #1e3a5f;
        border-bottom: 2px solid #f1f5f9; padding-bottom: 0.75rem;
    }
    .info-row { display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.9rem; }
    .info-row .label { color: #64748b; font-weight: 500; }
    .info-row .value { color: #0f172a; font-weight: 600; text-align: right; }

    .items-table { width: 100%; border-collapse: collapse; }
    .items-table th {
        padding: 0.75rem 1rem; text-align: left; font-size: 0.8rem; font-weight: 600;
        color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;
        border-bottom: 2px solid #f1f5f9; background: #fafbfc;
    }
    .items-table td {
        padding: 0.9rem 1rem; border-bottom: 1px solid #f8fafc; font-size: 0.9rem; color: #1e293b;
    }
    .items-table tr:last-child td { border-bottom: none; }

    .btn-group { display: flex; gap: 0.75rem; margin-bottom: 1.5rem; }
    .btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; border-radius: 0.75rem; font-size: 0.85rem; font-weight: 600; text-decoration: none; cursor: pointer; border: none; transition: all 0.2s; }
    .btn-outline { background: #fff; color: #475569; border: 1px solid #e2e8f0; }
    .btn-outline:hover { background: #f8fafc; border-color: #5eb542; color: #5eb542; }
    .btn-danger { background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; }
    .btn-danger:hover { background: #ef4444; color: #fff; }

    .note-box {
        background: #f8fafc; border-radius: 0.75rem; padding: 1rem; margin-top: 1rem;
        font-size: 0.9rem; color: #475569; border: 1px solid #f1f5f9;
    }
</style>

<div class="retail-show">
    <div class="btn-group">
        <a href="{{ route('admin.retail-orders.index') }}" class="btn btn-outline">← Quay lại danh sách</a>
        <form action="{{ route('admin.retail-orders.destroy', $retailOrder) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa phiếu bán lẻ này? Tồn kho sẽ được hoàn lại.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">🗑️ Xóa phiếu & hoàn kho</button>
        </form>
    </div>

    <div class="show-grid">
        {{-- Cột trái: Thông tin --}}
        <div>
            <div class="info-card" style="margin-bottom: 1.5rem;">
                <h3>👤 Khách hàng</h3>
                <div class="info-row">
                    <span class="label">Họ tên</span>
                    <span class="value">{{ $retailOrder->customer_name }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Điện thoại</span>
                    <span class="value">{{ $retailOrder->customer_phone ?: '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Địa chỉ</span>
                    <span class="value">{{ $retailOrder->customer_address ?: '—' }}</span>
                </div>
            </div>

            <div class="info-card">
                <h3>📋 Thông tin phiếu</h3>
                <div class="info-row">
                    <span class="label">Mã phiếu</span>
                    <span class="value" style="color: #5eb542;">{{ $retailOrder->order_code }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Ngày tạo</span>
                    <span class="value">{{ $retailOrder->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Nhân viên</span>
                    <span class="value">{{ $retailOrder->staff->name ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Số sản phẩm</span>
                    <span class="value">{{ $retailOrder->items->count() }}</span>
                </div>
                @if($retailOrder->note)
                <div class="note-box">
                    <strong>Ghi chú:</strong> {{ $retailOrder->note }}
                </div>
                @endif
            </div>
        </div>

        {{-- Cột phải: Danh sách sản phẩm --}}
        <div class="info-card">
            <h3>📦 Sản phẩm đã xuất kho</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên sản phẩm</th>
                        <th style="text-align: right;">Số lượng</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($retailOrder->items as $index => $item)
                    <tr>
                        <td style="color: #94a3b8; font-weight: 600;">{{ $index + 1 }}</td>
                        <td style="font-weight: 600;">
                                {{ $item->packagedProduct->name ?? 'N/A' }}
                                @if(($item->packagedProduct->sku ?? '') !== '')
                                    <span style="font-family:monospace; font-size:0.72rem; background:#f1f5f9; color:#64748b; padding:0.1rem 0.35rem; border-radius:0.2rem; font-weight:700; margin-left:0.35rem;">{{ $item->packagedProduct->sku }}</span>
                                @endif
                        </td>
                        <td style="text-align: right; font-weight: 700;">{{ floatval($item->quantity) }} {{ $item->unit }}</td>
                        <td style="font-size: 0.85rem; color: #64748b;">{{ $item->note ?: '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
