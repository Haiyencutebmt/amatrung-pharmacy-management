@extends('layouts.admin')

@section('title', 'Phiếu xuất kho — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div class="icon-bg" style="width: 44px; height: 44px; background: #ecfdf5; color: #16a34a; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
        <span class="icon">🧾</span>
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Phiếu xuất kho</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Quản lý phiếu xuất kho Phiếu xuất kho & sản phẩm</p>
    </div>
</div>
@endsection

@section('content')
<style>
    .retail-container { font-family: 'Inter', system-ui, sans-serif; width: 100%; }
    .stat-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
    .stat-card {
        background: #fff; border: 1px solid #f1f5f9; border-radius: 1rem; padding: 1.25rem 1.5rem;
        display: flex; align-items: center; gap: 1rem;
    }
    .stat-card .stat-icon {
        width: 48px; height: 48px; border-radius: 0.75rem; display: flex;
        align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;
    }
    .stat-card .stat-value { font-size: 1.5rem; font-weight: 800; color: #0f172a; line-height: 1; }
    .stat-card .stat-label { font-size: 0.8rem; color: #64748b; font-weight: 500; white-space: nowrap; }

    .toolbar {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap;
    }
    .search-box {
        display: flex; align-items: center; gap: 0.5rem; background: #fff; border: 1px solid #e2e8f0;
        border-radius: 0.75rem; padding: 0.5rem 1rem; flex: 1; max-width: 400px;
    }
    .search-box input {
        border: none; outline: none; background: transparent; font-size: 0.9rem; width: 100%; color: #1e293b;
    }
    .btn-new {
        display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.7rem 1.5rem;
        background: #5eb542; color: #fff; border-radius: 0.75rem; font-weight: 700;
        font-size: 0.9rem; text-decoration: none; border: none; cursor: pointer;
        box-shadow: 0 4px 12px rgba(94,181,66,0.2); transition: background 0.2s;
    }
    .btn-new:hover { background: #4da036; }

    .order-table { width: 100%; border-collapse: collapse; }
    .order-table th {
        padding: 0.75rem 1rem; text-align: left; font-size: 0.8rem; font-weight: 600;
        color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;
        border-bottom: 2px solid #f1f5f9; background: #fafbfc;
    }
    .order-table td {
        padding: 0.9rem 1rem; border-bottom: 1px solid #f8fafc; font-size: 0.9rem; color: #1e293b;
    }
    .order-table tr:hover td { background: #f8fafc; }
    .order-code {
        font-weight: 700; color: #5eb542; font-size: 0.95rem;
    }
    .customer-name { font-weight: 600; }
    .item-count {
        display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.2rem 0.6rem;
        background: #f0fdf4; color: #16a34a; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;
    }
    .btn-detail {
        padding: 0.4rem 1rem; background: #fff; border: 1px solid #e2e8f0; border-radius: 0.5rem;
        font-size: 0.82rem; font-weight: 600; color: #475569; text-decoration: none;
        transition: all 0.2s;
    }
    .btn-detail:hover { background: #f8fafc; border-color: #5eb542; color: #5eb542; }

    .table-card {
        background: #fff; border: 1px solid #f1f5f9; border-radius: 1rem; overflow: hidden;
    }
    .empty-state {
        text-align: center; padding: 3rem; color: #94a3b8;
    }
    .empty-state .icon { font-size: 3rem; margin-bottom: 1rem; }
</style>

<div class="retail-container">

    {{-- Thống kê --}}
    <div class="stat-row">
        <div class="stat-card">
            <div class="stat-icon" style="background: #f0fdf4; color: #16a34a;">📦</div>
            <div>
                <div class="stat-value">{{ $totalOrders }}</div>
                <div class="stat-label">Tổng phiếu</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">📅</div>
            <div>
                <div class="stat-value">{{ $ordersToday }}</div>
                <div class="stat-label">Hôm nay</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #faf5ff; color: #7c3aed;">📊</div>
            <div>
                <div class="stat-value">{{ $ordersThisMonth }}</div>
                <div class="stat-label">Tháng này</div>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="toolbar">
        <form class="search-box" method="GET">
            <button type="submit" style="background: none; border: none; cursor: pointer; padding: 0;">🔍</button>
            <input type="text" name="search" placeholder="Tìm mã phiếu, tên khách, SĐT..." value="{{ request('search') }}">
        </form>
        <a href="{{ route('admin.retail-orders.create') }}" class="btn-new">
            <span>+</span> Tạo phiếu xuất kho
        </a>
    </div>

    {{-- Bảng phiếu --}}
    <div class="table-card">
        @if($orders->count() > 0)
        <table class="order-table">
            <thead>
                <tr>
                    <th>Mã phiếu</th>
                    <th>Khách hàng</th>
                    <th>Sản phẩm</th>
                    <th>Ngày tạo</th>
                    <th>Nhân viên</th>
                    <th style="text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td><span class="order-code">{{ $order->order_code }}</span></td>
                    <td>
                        <span class="customer-name">{{ $order->customer_name }}</span>
                        @if($order->customer_phone)
                            <br><span style="font-size: 0.8rem; color: #64748b;">📞 {{ $order->customer_phone }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="item-count">🌿 {{ $order->items->count() }} sản phẩm</span>
                    </td>
                    <td style="font-size: 0.85rem; color: #475569;">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td style="font-size: 0.85rem;">{{ $order->staff->name ?? '—' }}</td>
                    <td style="text-align: center;">
                        <a href="{{ route('admin.retail-orders.show', $order) }}" class="btn-detail">Chi tiết</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="padding: 1rem 1.5rem; border-top: 1px solid #f1f5f9;">
            {{ $orders->links() }}
        </div>
        @else
        <div class="empty-state">
            <div class="icon">🧾</div>
            <h4 style="color: #475569; margin: 0 0 0.5rem 0;">Chưa có phiếu xuất kho nào</h4>
            <p style="margin: 0 0 1rem 0;">Nhấn nút bên trên để tạo phiếu xuất kho đầu tiên.</p>
            <a href="{{ route('admin.retail-orders.create') }}" class="btn-new">+ Tạo phiếu xuất kho</a>
        </div>
        @endif
    </div>
</div>
@endsection

