@extends('layouts.admin')

@section('title', 'Quản lý Yêu Cầu Hỗ Trợ — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div class="icon-bg" style="width: 44px; height: 44px; background: #eef3f9; color: #1a5b8f; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
        <span class="icon">📧</span>
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Yêu cầu hỗ trợ</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Quản lý tin nhắn liên hệ và yêu cầu hỗ trợ từ khách hàng</p>
    </div>
</div>
@endsection

@section('content')
<div class="contact-container" style="font-family: 'Inter', sans-serif; margin-top: -1rem;">

    <div class="main-content-card">
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="font-size: 0.75rem;">NGƯỜI GỬI</th>
                        <th style="font-size: 0.75rem;">SỐ ĐIỆN THOẠI</th>
                        <th style="font-size: 0.75rem; width: 40%;">NỘI DUNG YÊU CẦU</th>
                        <th style="font-size: 0.75rem;">NGÀY GỬI</th>
                        <th style="font-size: 0.75rem;">TRẠNG THÁI</th>
                        <th style="font-size: 0.75rem;">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $msg)
                    <tr>
                        <td>
                            <div class="patient-name-cell">
                                <div class="avatar-circle" style="background: {{ '#' . substr(md5($msg->name), 0, 6) }}20; color: {{ '#' . substr(md5($msg->name), 0, 6) }}">
                                    {{ mb_substr($msg->name, 0, 1) }}
                                </div>
                                <div class="name">{{ $msg->name }}</div>
                            </div>
                        </td>
                        <td>
                            <span style="font-size: 0.9rem; font-weight: 600; color: #475569;">{{ $msg->phone }}</span>
                        </td>
                        <td>
                            <div style="font-size: 0.9rem; color: #334155; line-height: 1.5; background: #f8fafc; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; white-space: pre-line;">{{ $msg->message }}</div>
                        </td>
                        <td>
                            <span style="font-size: 0.85rem; color: #64748b; font-weight: 600;">{{ $msg->created_at->format('d/m/Y H:i') }}</span>
                        </td>
                        <td>
                            @if($msg->status === 'resolved')
                                <span style="background: #eef9ee; color: #16a34a; padding: 0.3rem 0.75rem; border-radius: 0.5rem; font-weight: 800; font-size: 0.75rem;">Đã giải quyết</span>
                            @else
                                <span style="background: #fef9c3; color: #a16207; padding: 0.3rem 0.75rem; border-radius: 0.5rem; font-weight: 800; font-size: 0.75rem;">Chờ xử lý</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.4rem; align-items: center; max-width: 150px;">
                                @if($msg->status === 'pending')
                                    <form action="{{ route('admin.contact-messages.update', $msg) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="resolved">
                                        <button type="submit" class="btn-action" style="color: #2563eb; border-color: #bfdbfe; background: #eff6ff;" title="Đánh dấu đã giải quyết">
                                            ✅ Giải quyết
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.contact-messages.update', $msg) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="pending">
                                        <button type="submit" class="btn-action" style="color: #64748b; border-color: #e2e8f0; background: #f8fafc;" title="Mở lại yêu cầu">
                                            🔄 Mở lại
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.contact-messages.destroy', $msg) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa yêu cầu hỗ trợ này?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-action" style="color: #ef4444; border-color: #fecaca; background: #fef2f2;" title="Xóa">
                                        🗑️ Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding: 3rem; text-align: center; color: #64748b; font-size: 0.95rem; font-weight: 500;">
                            🔍 Không có yêu cầu hỗ trợ nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($messages->hasPages())
        <div class="pagination-area">
            <p class="summary">Hiển thị {{ $messages->firstItem() }} đến {{ $messages->lastItem() }} của {{ $messages->total() }} yêu cầu</p>
            <div class="pagination-controls">
                {{ $messages->withQueryString()->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<style>
/* Same CSS structure as users/patients page */
.main-content-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 1px solid #e2e8f0;
}

.table-container {
    overflow-x: auto;
    border-radius: 0.5rem;
    border: 1px solid #f1f5f9;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}

.data-table th {
    background-color: #f8fafc;
    color: #475569;
    font-weight: 700;
    padding: 0.85rem 1rem;
    border-bottom: 2px solid #e2e8f0;
    text-align: left;
}

.data-table td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}

.data-table tr:hover {
    background: #f8fafc;
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
    font-size: 1rem;
}

.name {
    font-weight: 700;
    color: #1e293b;
    font-size: 0.9rem;
}

.btn-action {
    padding: 0.35rem 0.65rem;
    border-radius: 0.4rem;
    border: 1px solid;
    font-size: 0.75rem;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-action:hover {
    filter: brightness(0.95);
    transform: scale(1.02);
}

.pagination-area {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1.5rem;
    margin-top: 1.5rem;
    border-top: 1px solid #e2e8f0;
}
.pagination-area .summary {
    color: #64748b;
    font-size: 0.85rem;
    margin: 0;
}

/* Pagination Overrides for Laravel */
.custom-pagination nav {
    display: flex;
    align-items: center;
}
.custom-pagination nav > div:first-child {
    display: none;
}
.custom-pagination svg {
    width: 14px;
    height: 14px;
}
</style>

@endsection
