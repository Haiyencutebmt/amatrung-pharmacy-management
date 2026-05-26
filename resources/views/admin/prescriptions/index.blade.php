@extends('layouts.admin')

@section('title', 'Danh sách Đơn Điều Trị — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div class="icon-bg" style="width: 44px; height: 44px; background: #eef9ee; color: #5eb542; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; border: 1px solid #cbd5e1;">
        <span class="icon">💊</span>
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Danh sách đơn điều trị</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Quản lý và theo dõi các phác đồ / đơn điều trị đã kê trong hệ thống</p>
    </div>
</div>
@endsection

@section('content')
<div class="record-index-container" style="margin-top: -1rem; font-family: 'Inter', system-ui, sans-serif;">

    {{-- Filters & Table --}}
    <div class="main-content-card" style="border-radius: 4px; border: 1px solid #cbd5e1; box-shadow: none; padding: 1.5rem; background: #fff;">
        <form action="{{ route('admin.prescriptions.index') }}" method="GET" class="filter-form">
            <div class="filter-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div style="display: flex; gap: 1.5rem; align-items: flex-end; flex: 1;">
                    <div class="filter-item" style="display: flex; flex-direction: column; align-items: flex-start; gap: 0.5rem;">
                        <label style="font-size: 0.85rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Tìm theo bệnh nhân</label>
                        <div class="search-input-group" style="position: relative; width: 350px;">
                            <span class="icon" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8;">🔍</span>
                            <input type="text" name="search" placeholder="Tên, mã BN hoặc SĐT..." value="{{ request('search') }}" style="width: 100%; padding: 0.6rem 0.6rem 0.6rem 2.25rem; border-radius: 4px; border: 1px solid #cbd5e1; font-size: 0.95rem;">
                        </div>
                    </div>
                    <div class="action-buttons" style="display: flex; gap: 0.5rem; margin-bottom: 2px;">
                        <button type="submit" class="btn-filter" style="padding: 0.6rem 1.25rem; border-radius: 4px; border: 1px solid #cbd5e1; background: #f8fafc; color: #2563eb; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.4rem;">
                            🔍 Lọc kết quả
                        </button>
                        <a href="{{ route('admin.prescriptions.index') }}" class="btn-reset-box" style="padding: 0.6rem 1.25rem; border-radius: 4px; border: 1px solid #cbd5e1; background: #fff; color: #64748b; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 0.4rem;">
                            🔄 Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <div class="table-container" style="overflow-x: auto;">
            <table class="patient-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #cbd5e1; background: #f8fafc;">
                        <th style="text-align: left; padding: 0.75rem; color: #1e3a5f; font-weight: 800; font-size: 0.85rem; text-transform: uppercase;">MÃ ĐƠN</th>
                        <th style="text-align: left; padding: 0.75rem; color: #1e3a5f; font-weight: 800; font-size: 0.85rem; text-transform: uppercase;">NGÀY KÊ PHÁC ĐỒ</th>
                        <th style="text-align: left; padding: 0.75rem; color: #1e3a5f; font-weight: 800; font-size: 0.85rem; text-transform: uppercase;">BỆNH NHÂN</th>
                        <th style="text-align: left; padding: 0.75rem; color: #1e3a5f; font-weight: 800; font-size: 0.85rem; text-transform: uppercase;">THẦY THUỐC ĐIỀU TRỊ</th>
                        <th style="text-align: left; padding: 0.75rem; color: #1e3a5f; font-weight: 800; font-size: 0.85rem; text-transform: uppercase;">HẠNG MỤC</th>
                        <th style="text-align: center; padding: 0.75rem; color: #1e3a5f; font-weight: 800; font-size: 0.85rem; text-transform: uppercase; width: 340px;">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prescriptions as $prescription)
                    <tr style="border-bottom: 1px solid #e2e8f0; font-size: 0.95rem;">
                        <td style="padding: 0.75rem;">
                            <span class="patient-code" style="background: #f0f7ff; color: #2563eb; padding: 0.3rem 0.6rem; border-radius: 4px; font-weight: bold; border: 1px solid #bfdbfe;">#{{ $prescription->id }}</span>
                        </td>
                        <td style="padding: 0.75rem;">
                            <span style="font-weight: 600; color: #334155;">{{ $prescription->created_at->format('d/m/Y H:i') }}</span>
                        </td>
                        <td style="padding: 0.75rem;">
                            <div class="patient-name-cell" style="display: flex; align-items: center; gap: 0.75rem;">
                                @if(isset($prescription->medicalRecord->patient))
                                <div class="avatar-circle" style="width: 32px; height: 32px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; background: {{ '#' . substr(md5($prescription->medicalRecord->patient->full_name), 0, 6) }}20; color: {{ '#' . substr(md5($prescription->medicalRecord->patient->full_name), 0, 6) }}">
                                    {{ mb_substr($prescription->medicalRecord->patient->full_name, 0, 1) }}
                                </div>
                                @endif
                                <div>
                                    <div class="name" style="font-weight: bold; color: #1e293b;">{{ $prescription->medicalRecord->patient->full_name ?? 'N/A' }}</div>
                                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.1rem; font-weight: 600;">Mã BN: {{ $prescription->medicalRecord->patient->patient_code ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 0.75rem;">
                            <span style="color: #334155; font-weight: 600;">{{ $prescription->staff->name ?? 'N/A' }}</span>
                        </td>
                        <td style="padding: 0.75rem;">
                            <span style="background: #f0fdf4; color: #166534; padding: 0.3rem 0.6rem; border-radius: 4px; font-weight: 800; font-size: 0.8rem; border: 1px solid #bbf7d0;">
                                {{ $prescription->items->count() }} mục phác đồ
                            </span>
                        </td>
                        <td style="padding: 0.75rem; text-align: center;">
                            <div class="action-cell" style="display: flex; gap: 0.4rem; justify-content: center; flex-wrap: wrap;">
                                <a href="{{ route('admin.prescriptions.show', $prescription) }}" class="btn-icon view" style="padding: 0.4rem 0.75rem; border-radius: 4px; border: 1px solid #cbd5e1; text-decoration: none; color: #334155; font-weight: bold; font-size: 0.82rem; background: #fff;" title="Xem">
                                    👁️ Chi tiết
                                </a>
                                @if($prescription->isDispensingPrescription() || $prescription->hasExternalTreatmentItems())
                                    @php
                                        $internalLabel = $prescription->internalPrintLabel();
                                        $internalColor = $prescription->isDispensingPrescription() ? '#b45309' : '#2563eb';
                                        $internalBg = $prescription->isDispensingPrescription() ? '#fffbeb' : '#eff6ff';
                                        $internalBorder = $prescription->isDispensingPrescription() ? '#fde68a' : '#bfdbfe';
                                    @endphp
                                    <a href="{{ route('admin.prescriptions.show', ['prescription' => $prescription, 'type' => 'internal']) }}" onclick="event.preventDefault(); openPrescriptionModal({{ $prescription->id }}, 'internal', false, {{ Illuminate\Support\Js::from($internalLabel) }});" class="btn-icon" style="padding: 0.4rem 0.75rem; border-radius: 4px; border: 1px solid {{ $internalBorder }}; text-decoration: none; color: {{ $internalColor }}; font-weight: bold; font-size: 0.82rem; background: {{ $internalBg }};" title="In phiếu {{ mb_strtolower($internalLabel) }} nội bộ">
                                        📋 {{ $internalLabel }}
                                    </a>
                                @endif
                                @if(auth()->user()->hasPermission('prescriptions.delete'))
                                    @if($prescription->canBeReturnedOrDeleted())
                                        <form action="{{ route('admin.prescriptions.destroy', $prescription) }}" method="POST" style="display:inline;" onsubmit="return confirm('Chỉ xóa/hoàn kho khi bệnh nhân trả thuốc trong 24 giờ. Bạn chắc chắn muốn thực hiện?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-icon delete" style="padding: 0.4rem 0.75rem; border-radius: 4px; border: 1px solid #fee2e2; background: #fff; color: #ef4444; font-weight: bold; font-size: 0.82rem; cursor: pointer;" title="Trả thuốc / Xóa">
                                                🗑️ Trả/Xóa
                                            </button>
                                        </form>
                                    @else
                                        <span title="Đã quá 24 giờ, không được xóa hoặc hoàn kho" style="padding: 0.4rem 0.75rem; border-radius: 4px; border: 1px solid #e2e8f0; background: #f8fafc; color: #94a3b8; font-weight: bold; font-size: 0.82rem;">
                                            Quá hạn trả
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #64748b; padding: 3rem 1rem; font-weight: 500;">
                            Chưa có đơn điều trị nào được lập.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($prescriptions->hasPages())
        <div class="pagination-area" style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem;">
            <p class="summary" style="font-size: 0.85rem; color: #64748b;">Hiển thị {{ $prescriptions->firstItem() }} đến {{ $prescriptions->lastItem() }} của {{ $prescriptions->total() }} đơn</p>
            <div class="pagination-controls">
                {{ $prescriptions->withQueryString()->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
