@extends('layouts.admin')

@section('title', 'Lịch hẹn — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div class="icon-bg" style="width: 44px; height: 44px; background: #f0f7ff; color: #3b82f6; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
        <span class="icon">📅</span>
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Lịch hẹn bệnh nhân</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Lập kế hoạch và quản lý lịch hẹn của bạn</p>
    </div>
</div>
@endsection

@section('content')
<div class="calendar-container" style="margin-top: -1rem;">
    {{-- Header Lịch --}}
    <div class="calendar-header">
        <div class="calendar-actions" style="width: 100%; justify-content: flex-end; gap: 0.75rem;">
            {{-- Nav tháng/năm --}}
            <div class="nav-wrapper">
                <a href="{{ route('admin.appointments.index', ['month' => $date->copy()->subMonth()->month, 'year' => $date->copy()->subMonth()->year]) }}" class="nav-btn-circle">
                    <span class="icon">‹</span>
                </a>
                
                <div style="position: relative; display: inline-block;">
                    <div class="month-selector-trigger" onclick="togglePicker(event)">
                        <span class="icon">📅</span>
                        <span class="text">Tháng {{ $date->format('m, Y') }}</span>
                        <span class="arrow">▾</span>
                    </div>

                    {{-- Picker Tháng/Năm --}}
                    <div id="monthYearPicker" class="picker-content" style="display: none; position: absolute; top: calc(100% + 8px); right: 0; left: auto; transform: none; z-index: 2000; margin: 0; animation: fadeInScale 0.2s ease-out;">
                        <div class="picker-header">
                            <div class="year-nav">
                                <button type="button" onclick="changeYear(-1, event)">‹</button>
                                <span id="pickerYear">{{ $date->year }}</span>
                                <button type="button" onclick="changeYear(1, event)">›</button>
                            </div>
                        </div>
                        <div class="months-grid">
                            @for ($m = 1; $m <= 12; $m++)
                                <div class="month-item {{ $date->month == $m ? 'active' : '' }}" 
                                     onclick="selectMonth({{ $m }})">
                                    Tháng {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.appointments.index', ['month' => $date->copy()->addMonth()->month, 'year' => $date->copy()->addMonth()->year]) }}" class="nav-btn-circle">
                    <span class="icon">›</span>
                </a>
            </div>

            {{-- Nút thêm lịch hẹn --}}
            <button class="btn-add-appointment" onclick="openAddModal()">
                <span>+</span> Thêm lịch hẹn
            </button>
        </div>
    </div>


    {{-- Grid Lịch --}}
    <div class="calendar-grid">
        <div class="grid-day-header">CN</div>
        <div class="grid-day-header">T2</div>
        <div class="grid-day-header">T3</div>
        <div class="grid-day-header">T4</div>
        <div class="grid-day-header">T5</div>
        <div class="grid-day-header">T6</div>
        <div class="grid-day-header">T7</div>

        @php
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            $daysInMonth = $date->daysInMonth;
            $startDayOfWeek = $startOfMonth->dayOfWeek; // 0 (Sun) to 6 (Sat)
            $totalCells = ceil(($daysInMonth + $startDayOfWeek) / 7) * 7;
        @endphp

        @for ($i = 0; $i < $totalCells; $i++)
            @php
                $dayNumber = $i - $startDayOfWeek + 1;
                $currentDayDate = $date->copy()->startOfMonth()->addDays($dayNumber - 1);
                $isCurrentMonth = $dayNumber > 0 && $dayNumber <= $daysInMonth;
                $dateKey = $currentDayDate->format('Y-m-d');
                $dayAppointments = $appointments->get($dateKey, collect());
            @endphp

            <div class="calendar-cell {{ !$isCurrentMonth ? 'other-month' : '' }}" 
                 @if($isCurrentMonth) onclick="viewAppointments('{{ $dateKey }}', 'Tháng {{ $currentDayDate->format('m') }} ngày {{ $currentDayDate->format('d, Y') }}')" @endif>
                
                <div class="cell-top">
                    <span class="cell-day-num {{ $currentDayDate->isToday() && $isCurrentMonth ? 'is-today' : '' }}">
                        {{ $dayNumber > 0 && $dayNumber <= $daysInMonth ? str_pad($dayNumber, 2, '0', STR_PAD_LEFT) : '' }}
                    </span>
                </div>
                
                @if($isCurrentMonth && $dayAppointments->count() > 0)
                    <div class="appointment-indicator">
                        <div class="cat-icon-wrapper">
                            <svg class="cat-svg" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <path id="hl-{{ $i }}"
                                          d="M20,21 C20,18 12,15 12,11 C12,7 16,5 20,10 C24,5 28,7 28,11 C28,15 20,18 20,21 Z"/>
                                </defs>
                                <use href="#hl-{{ $i }}" fill="#b4f0a8" stroke="#88d878" stroke-width="0.5"/>
                                <use href="#hl-{{ $i }}" fill="#a8e8a0" stroke="#88d878" stroke-width="0.5" transform="rotate(90,20,20)"/>
                                <use href="#hl-{{ $i }}" fill="#b4f0a8" stroke="#88d878" stroke-width="0.5" transform="rotate(180,20,20)"/>
                                <use href="#hl-{{ $i }}" fill="#a8e8a0" stroke="#88d878" stroke-width="0.5" transform="rotate(270,20,20)"/>
                                <circle cx="20" cy="20" r="2.8" fill="#6dcf5e"/>
                            </svg>
                            <span class="cat-count-badge">{{ $dayAppointments->count() }}</span>
                        </div>
                        <span class="appt-circle-label">lịch hẹn</span>
                    </div>
                @endif
            </div>
        @endfor
    </div>
</div>

{{-- Modal Thêm lịch hẹn --}}
<div id="addAppointmentModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Thêm lịch hẹn mới</h3>
            <button class="close-btn" onclick="closeAddModal()">×</button>
        </div>
        <form action="{{ route('admin.appointments.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Bệnh nhân</label>
                    <select name="patient_id" class="form-control" required>
                        <option value="">Chọn bệnh nhân...</option>
                        @foreach(\App\Models\Patient::all() as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->full_name }} ({{ $patient->patient_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Ngày hẹn</label>
                        <input type="date" name="appointment_date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label>Giờ hẹn</label>
                        <input type="time" name="appointment_time" class="form-control" required value="08:00">
                    </div>
                </div>
                <div class="form-group">
                    <label>Lý do / Nội dung</label>
                    <input type="text" name="reason" class="form-control" placeholder="Ví dụ: Tái khám">
                </div>
                <div class="form-group">
                    <label>Ghi chú</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu lịch hẹn</button>
            </div>
        </form>
    </div>
</div>

{{-- Custom Confirmation Modal --}}
<div id="confirmModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.25s ease;">
    <div style="background: #fff; border-radius: 1rem; width: 420px; padding: 2rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); transform: scale(0.9); transition: transform 0.25s ease; border: 1px solid #f1f5f9;" id="confirmModalCard">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="width: 56px; height: 56px; background: #fee2e2; color: #ef4444; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 1rem;">
                ⚠️
            </div>
            <h3 style="margin: 0 0 0.5rem 0; font-size: 1.25rem; font-weight: 800; color: #1e293b;">Xác nhận xóa lịch hẹn?</h3>
            <p style="margin: 0; color: #64748b; font-size: 0.9rem; line-height: 1.5;" id="confirmModalText">Bạn có chắc chắn muốn xóa lịch hẹn này không? Hành động này không thể hoàn tác.</p>
        </div>
        <div style="display: flex; gap: 0.75rem; justify-content: center;">
            <button type="button" id="confirmCancelBtn" style="flex: 1; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid #cbd5e1; background: #fff; color: #475569; font-weight: 700; cursor: pointer; transition: all 0.2s; font-size: 0.9rem;">
                Hủy bỏ
            </button>
            <button type="button" id="confirmOkBtn" style="flex: 1; padding: 0.75rem; border-radius: 0.5rem; border: none; background: #ef4444; color: #fff; font-weight: 700; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.15); font-size: 0.9rem;">
                Đồng ý xóa
            </button>
        </div>
    </div>
</div>

<style>
.calendar-container {
    background: #fff;
    border-radius: 0.375rem;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.02);
    border: 1px solid #e2e8f0;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.calendar-title-area h2 {
    font-size: 1.75rem;
    font-weight: 800;
    color: #1e293b;
    margin: 0;
}

.calendar-title-area p {
    color: #64748b;
    margin: 0.25rem 0 0 0;
    font-size: 0.9rem;
}

.calendar-actions {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.nav-wrapper {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.nav-btn-circle {
    width: 36px;
    height: 36px;
    border-radius: 0.25rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: #64748b;
    font-size: 1.25rem;
    transition: all 0.2s;
}

.nav-btn-circle:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
}

.month-selector-trigger {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 1rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 0.25rem;
    cursor: pointer;
    transition: all 0.2s;
}

.month-selector-trigger:hover {
    border-color: #3b82f6;
}

.month-selector-trigger .text {
    font-weight: 700;
    color: #1e293b;
    font-size: 0.9rem;
}

.month-selector-trigger .arrow {
    color: #94a3b8;
    font-size: 0.75rem;
}

.btn-add-appointment {
    background: #5eb542;
    color: #fff;
    border: none;
    padding: 0.75rem 1.25rem;
    border-radius: 0.25rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-add-appointment:hover {
    background: #4a9e32;
    transform: translateY(-1px);
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    border-left: 1px solid #f1f5f9;
    border-top: 1px solid #f1f5f9;
}

.grid-day-header {
    padding: 1rem;
    text-align: center;
    font-weight: 800;
    color: #1e3a5f;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
    border-right: 1px solid #f1f5f9;
    border-bottom: 1px solid #f1f5f9;
}

    .calendar-cell {
        min-height: 80px; /* reduced height for compact view */
        padding: 0.5rem;
        border-right: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: background 0.2s;
        background: #fff;
        display: flex;
        flex-direction: column;
    }

    .appointment-indicator {
        margin-top: auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.1rem;
    }

    .cat-icon-wrapper {
        position: relative;
        width: 36px;
        height: 36px;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .calendar-cell:hover .cat-icon-wrapper {
        transform: scale(1.15) rotate(-5deg);
    }

    .cat-svg {
        width: 36px;
        height: 36px;
        filter: drop-shadow(0 2px 6px rgba(34, 197, 94, 0.4));
    }

    .cat-count-badge {
        position: absolute;
        top: -5px;
        right: -7px;
        background: #16a34a;
        color: #fff;
        font-size: 0.62rem;
        font-weight: 800;
        min-width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 2px;
        border: 2px solid #fff;
        box-shadow: 0 2px 6px rgba(22, 163, 74, 0.4);
        line-height: 1;
    }

    .appt-circle-label {
        font-size: 0.58rem;
        font-weight: 600;
        color: #94a3b8;
        letter-spacing: 0.02em;
        text-transform: lowercase;
    }

.calendar-cell:hover {
    background: #f8fafc;
}

.calendar-cell.other-month {
    background: #fcfdfe;
    opacity: 0.3;
}

.cell-top {
    margin-bottom: 0.75rem;
}

.cell-day-num {
    font-size: 0.85rem;
    font-weight: 700;
    color: #1e293b;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cell-day-num.is-today {
    background: #3b82f6;
    color: #fff;
    border-radius: 0.25rem;
}

.appointments-stack {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.app-item-bar {
    padding: 0.35rem 0.6rem;
    border-radius: 0.15rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.7rem;
    font-weight: 700;
    color: #1e3a5f;
}

.app-item-bar.color-1 { background: #f0fdf4; border: 1px solid #dcfce7; }
.app-item-bar.color-2 { background: #eff6ff; border: 1px solid #dbeafe; }

.app-item-bar .time { opacity: 0.8; font-weight: 800; }
.app-item-bar .name { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

.app-more {
    font-size: 0.65rem;
    font-weight: 700;
    color: #94a3b8;
    padding-left: 0.25rem;
}

/* Picker CSS */
.picker-content {
    background: #fff;
    width: 280px;
    border-radius: 0.375rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    padding: 1.5rem;
    border: 1px solid #e2e8f0;
}

.picker-header {
    margin-bottom: 1.5rem;
}

.year-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8fafc;
    padding: 0.5rem;
    border-radius: 0.25rem;
}

.year-nav button {
    background: #fff;
    border: 1px solid #e2e8f0;
    width: 32px;
    height: 32px;
    border-radius: 0.25rem;
    cursor: pointer;
}

.year-nav span {
    font-weight: 800;
    color: #1e293b;
    font-size: 1rem;
}

.months-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.5rem;
}

.month-item {
    padding: 0.75rem 0.5rem;
    text-align: center;
    border-radius: 0.25rem;
    font-weight: 700;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.85rem;
}

.month-item:hover {
    background: #f1f5f9;
    color: #1e293b;
}

.month-item.active {
    background: #3b82f6;
    color: #fff;
}

/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.4);
    backdrop-filter: blur(8px);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background: #fff;
    width: 90%;
    max-width: 500px;
    border-radius: 0.375rem;
    padding: 2rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
}

.modal-header h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 800;
    color: #1e3a5f;
}

.modal-header p {
    margin: 0.25rem 0 0 0;
    color: #94a3b8;
    font-weight: 600;
}

.close-btn {
    background: #f8faff;
    border: 1px solid #f0f3ff;
    width: 36px;
    height: 36px;
    border-radius: 0.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    cursor: pointer;
    color: #64748b;
    transition: all 0.2s;
}

.close-btn:hover {
    background: #fee2e2;
    color: #ef4444;
}

.appointment-item {
    padding: 1.25rem;
    background: #f8fbff;
    border: 1px solid #f0f3ff;
    border-radius: 0.25rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.2s;
}

.appointment-item:hover {
    transform: scale(1.01);
    border-color: #3b82f6;
}

.item-time {
    background: #fff;
    padding: 0.5rem;
    border-radius: 0.15rem;
    text-align: center;
    min-width: 60px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.03);
}

.item-time .time {
    font-weight: 800;
    color: #1e3a5f;
    font-size: 0.9rem;
    display: block;
}

.item-info {
    flex-grow: 1;
}

.item-name {
    font-weight: 800;
    color: #1e3a5f;
    font-size: 1rem;
    display: block;
    margin-bottom: 0.2rem;
}

.item-reason {
    font-size: 0.8rem;
    color: #64748b;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-weight: 700;
    color: #1e3a5f;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.form-control {
    width: 100%;
    padding: 0.8rem 1rem;
    border-radius: 0.25rem;
    border: 1px solid #eef2ff;
    background: #fcfdfe;
    font-family: inherit;
    font-size: 0.95rem;
    transition: all 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
}

.btn {
    padding: 0.8rem 1.5rem;
    border-radius: 0.25rem;
    font-weight: 700;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
}

.btn-secondary { background: #f1f5f9; color: #475569; }
.btn-primary { background: #3b82f6; color: #fff; box-shadow: 0 8px 20px rgba(59, 130, 246, 0.2); }

.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 25px rgba(59, 130, 246, 0.3); }

.calendar-cell.selected-cell {
    background: #f0f7ff;
    border: 2px solid #3b82f6 !important;
}

.btn-check-in {
    padding: 0.35rem 0.65rem;
    font-size: 0.75rem;
    font-weight: 700;
    border-radius: 0.25rem;
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid transparent;
}
.btn-check-in.checked {
    background: #eef9ee;
    color: #16a34a;
    border-color: #bbf7d0;
}
.btn-check-in.checked:hover {
    background: #fee2e2;
    color: #ef4444;
    border-color: #fecaca;
}
.btn-check-in.checked:hover::after {
    content: " (Hủy)";
}
.btn-check-in.pending {
    background: #eff6ff;
    color: #2563eb;
    border-color: #bfdbfe;
}
.btn-check-in.pending:hover {
    background: #3b82f6;
    color: #fff;
    border-color: #3b82f6;
}
.btn-check-in.pending-overdue {
    background: #fff;
    color: #dc2626;
    border-color: #fecaca;
}
.btn-check-in.pending-overdue:hover {
    background: #dc2626;
    color: #fff;
    border-color: #dc2626;
}
</style>

@push('scripts')
<script>
    const addModal = document.getElementById('addAppointmentModal');
    const picker = document.getElementById('monthYearPicker');
    
    let currentYear = {{ $date->year }};
    let dayAppointmentsData = [];
    let selectedDateKey = '';

    function togglePicker(e) {
        if (e) e.stopPropagation();
        picker.style.display = picker.style.display === 'block' ? 'none' : 'block';
    }

    function changeYear(delta, e) {
        if (e) e.stopPropagation();
        currentYear += delta;
        document.getElementById('pickerYear').innerText = currentYear;
    }

    function selectMonth(month) {
        window.location.href = `{{ route('admin.appointments.index') }}?month=${month}&year=${currentYear}`;
    }

    document.addEventListener('click', function(e) {
        const trigger = document.querySelector('.month-selector-trigger');
        if (picker && trigger && !picker.contains(e.target) && !trigger.contains(e.target)) {
            picker.style.display = 'none';
        }
    });

    function viewAppointments(date, displayDate) {
        window.location.href = `/admin/appointments/day/${date}`;
    }

    function openAddModal() {
        addModal.style.display = 'flex';
    }

    function closeAddModal() {
        addModal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == addModal) closeAddModal();
    }
</script>
@endpush
@endsection
