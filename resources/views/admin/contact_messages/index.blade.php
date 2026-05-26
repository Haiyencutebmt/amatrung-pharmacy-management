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
<div class="record-index-container" style="margin-top: -1rem;">

    <div class="main-content-card">
        <div class="table-container">
            <table class="patient-table">
                <thead>
                    <tr>
                        <th>NGƯỜI GỬI</th>
                        <th>EMAIL</th>
                        <th style="width: 40%;">NỘI DUNG YÊU CẦU</th>
                        <th>NGÀY GỬI</th>
                        <th>TRẠNG THÁI</th>
                        <th>HÀNH ĐỘNG</th>
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
                            <span style="font-size: 0.9rem; font-weight: 600; color: #475569;">{{ $msg->email }}</span>
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
                            <div style="display: flex; flex-direction: column; gap: 0.5rem; max-width: 130px;">
                                @if($msg->status === 'pending')
                                    <form action="{{ route('admin.contact-messages.update', $msg) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="resolved">
                                        <button type="submit" class="btn-icon view" style="width: 100%; justify-content: center; border-color: #5eb542; color: #5eb542; font-weight: 700; padding: 0.4rem 0.8rem; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 0.2rem; font-size: 0.8rem;" title="Đánh dấu đã giải quyết">
                                            ✅ Giải quyết
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.contact-messages.update', $msg) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="pending">
                                        <button type="submit" class="btn-icon view" style="width: 100%; justify-content: center; font-weight: 700; padding: 0.4rem 0.8rem; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 0.2rem; font-size: 0.8rem;" title="Mở lại yêu cầu">
                                            Mở lại
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.contact-messages.destroy', $msg) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa yêu cầu hỗ trợ này?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon delete" style="width: 100%; justify-content: center; border-color: #ef4444; color: #ef4444; font-weight: 700; padding: 0.4rem 0.8rem; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 0.2rem; font-size: 0.8rem;" title="Xóa">
                                        🗑️ Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #64748b; padding: 3rem 1rem; font-weight: 500;">
                            Không có yêu cầu hỗ trợ nào.
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
@endsection
