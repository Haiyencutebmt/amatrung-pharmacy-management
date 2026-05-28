@extends('layouts.admin')

@section('title', 'Lịch hẹn ngày ' . $parsedDate->format('d/m/Y') . ' — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div class="icon-bg" style="width: 44px; height: 44px; background: #f0f7ff; color: #3b82f6; border-radius: 0.25rem; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; border: 1px solid #cbd5e1;">
        <span class="icon">📅</span>
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Lịch hẹn ngày {{ $parsedDate->format('d/m/Y') }}</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Chi tiết danh sách bệnh nhân và trạng thái điểm danh khám bệnh</p>
    </div>
</div>
@endsection

@section('content')
<div class="day-view-container" style="font-family: 'Inter', system-ui, sans-serif; margin-top: -1rem;">
    
    {{-- Thanh thao tác --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; background: #fff; padding: 1rem 1.5rem; border-radius: 1rem; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; border-radius: 0.25rem; font-size: 0.9rem; font-weight: 700; text-decoration: none; border: 1px solid #cbd5e1; background: #fff; color: #475569; transition: all 0.2s;">
            ← Quay lại Lịch tháng
        </a>
        <div style="font-size: 0.9rem; font-weight: 700; color: #1e3a5f; background: #f1f5f9; padding: 0.5rem 1rem; border-radius: 0.25rem; border: 1px solid #e2e8f0;">
            Tổng cộng: <span style="color: #3b82f6; font-size: 1rem;">{{ $appointments->count() }}</span> cuộc hẹn
        </div>
    </div>

    @if($appointments->isEmpty())
        <div style="background: #fff; border-radius: 1rem; border: 1px solid #f1f5f9; padding: 5rem 2rem; text-align: center; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
            <div style="font-size: 4rem; margin-bottom: 1.5rem; color: #cbd5e1;">📅</div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e3a5f; margin: 0 0 0.5rem 0;">Không có lịch hẹn</h3>
            <p style="color: #64748b; font-size: 0.95rem; margin: 0;">Không ghi nhận bất kỳ lịch hẹn nào của bệnh nhân cho ngày hôm nay.</p>
        </div>
    @else
        {{-- Lưới timeline hiển thị theo dây --}}
        <div class="timeline-wrapper" style="background: #fff; border-radius: 1rem; border: 1px solid #f1f5f9; padding: 2rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
            <div class="timeline-container">
                @foreach($appointments as $app)
                    @php
                        $now = now();
                        $isPastDate = $app->appointment_date < $now->toDateString();
                        $isToday = $app->appointment_date == $now->toDateString();
                        $isPast1730 = $now->hour > 17 || ($now->hour == 17 && $now->minute >= 30);
                        $isOverdue = ($app->status === 'pending') && ($isPastDate || ($isToday && $isPast1730));
                    @endphp
                    
                    <div class="timeline-item @if($app->status === 'confirmed' || $app->status === 'completed') completed @elseif($isOverdue) overdue @else active @endif" 
                         id="timeline-item-{{ $app->id }}" 
                         data-overdue="{{ $isOverdue ? 'true' : 'false' }}">
                        
                        <div class="timeline-dot"></div>
                        
                        <div class="timeline-card">
                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.25rem;">
                                
                                {{-- Bên trái: Thời gian & Thông tin bệnh nhân --}}
                                <div style="display: flex; align-items: center; gap: 1rem; flex: 1; min-width: 250px;">
                                    <div style="background: #f1f5f9; color: #1e3a5f; font-weight: 800; font-size: 1.05rem; padding: 0.5rem 0.75rem; border-radius: 0.25rem; border: 1px solid #e2e8f0; text-align: center; min-width: 65px; height: auto;">
                                        {{ \Carbon\Carbon::parse($app->appointment_time)->format('H:i') }}
                                    </div>
                                    <div>
                                        <h3 style="margin: 0; font-size: 1.15rem; font-weight: 850; color: #1e3a5f; display: flex; align-items: center; gap: 0.5rem;">
                                            {{ $app->patient->full_name }}
                                            <div id="status-badge-{{ $app->id }}" style="display: inline-block;">
                                                @if($app->status === 'confirmed' || $app->status === 'completed')
                                                    <span style="background: #eef9ee; color: #16a34a; font-weight: 800; font-size: 0.75rem; padding: 0.2rem 0.4rem; border-radius: 0.15rem; border: 1px solid #bbf7d0;">✓ Đã đến</span>
                                                @elseif($isOverdue)
                                                    <span style="background: #fef2f2; color: #dc2626; font-weight: 800; font-size: 0.75rem; padding: 0.2rem 0.4rem; border-radius: 0.15rem; border: 1px solid #fecaca;">⚠️ Vắng / Quá hạn</span>
                                                @else
                                                    <span style="background: #fef3c7; color: #d97706; font-weight: 800; font-size: 0.75rem; padding: 0.2rem 0.4rem; border-radius: 0.15rem; border: 1px solid #fde68a;">🕒 Chờ khám</span>
                                                @endif
                                            </div>
                                        </h3>
                                        <div style="display: flex; gap: 0.75rem; margin-top: 0.25rem; color: #64748b; font-size: 0.82rem; font-weight: 600;">
                                            <span>Mã BN: <strong style="color: #475569;">{{ $app->patient->patient_code }}</strong></span>
                                            <span>•</span>
                                            <span>SĐT: <strong style="color: #475569;">{{ $app->patient->phone ?: 'Chưa có' }}</strong></span>
                                            <span>•</span>
                                            <span>Tuổi: <strong style="color: #475569;">{{ $app->patient->age ?: '—' }}</strong></span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Giữa: Lý do khám bệnh --}}
                                <div style="color: #475569; font-size: 0.85rem; font-style: italic; background: #f8fafc; border-left: 3px solid #cbd5e1; padding: 0.4rem 0.75rem; margin: 0; min-width: 200px; max-width: 350px; flex: 1;">
                                    Lý do: {{ $app->reason ?: 'Tái khám định kỳ' }}
                                </div>

                                {{-- Bên phải: Điểm danh & Nút vào xem bệnh án --}}
                                <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                                    
                                    {{-- Tích đã đến khám --}}
                                    <div>
                                        <label class="check-in-toggle-container" style="display: inline-flex; align-items: center; gap: 0.6rem; cursor: pointer; font-weight: 700; color: #334155; background: #f8fafc; padding: 0.6rem 0.9rem; border-radius: 0.25rem; border: 1px solid #cbd5e1; transition: all 0.2s; user-select: none;">
                                            <input type="checkbox" class="check-in-checkbox" data-id="{{ $app->id }}" {{ $app->status === 'confirmed' || $app->status === 'completed' ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer; accent-color: #16a34a; margin: 0;">
                                            <span class="checkbox-label" style="font-size: 0.85rem;">{{ $app->status === 'confirmed' || $app->status === 'completed' ? 'Đã đến khám' : 'Tích đã đến' }}</span>
                                        </label>
                                    </div>

                                    @php
                                        $associatedRecord = $app->getAssociatedMedicalRecord();
                                    @endphp
                                    {{-- Nút xem bệnh án --}}
                                    <div>
                                        @if($associatedRecord)
                                            <a href="{{ route('admin.medical-records.show', $associatedRecord->id) }}" 
                                               class="btn-view-records {{ $app->status === 'confirmed' || $app->status === 'completed' ? '' : 'disabled' }}" 
                                               id="record-btn-{{ $app->id }}"
                                               title="{{ $app->status === 'confirmed' || $app->status === 'completed' ? 'Vào xem chi tiết bệnh án ' . $associatedRecord->record_code : 'Vui lòng tích Đã đến khám để vào xem bệnh án' }}">
                                                <span>📋</span> Vào xem bệnh án
                                            </a>
                                        @else
                                            <a href="{{ route('admin.patients.show', $app->patient_id) }}" 
                                               class="btn-view-records {{ $app->status === 'confirmed' || $app->status === 'completed' ? '' : 'disabled' }}" 
                                               id="record-btn-{{ $app->id }}"
                                               title="{{ $app->status === 'confirmed' || $app->status === 'completed' ? 'Vào xem hồ sơ bệnh nhân' : 'Vui lòng tích Đã đến khám để vào xem bệnh án' }}">
                                                <span>📋</span> Vào xem bệnh án
                                            </a>
                                        @endif
                                    </div>

                                    {{-- Xóa lịch hẹn --}}
                                    <div>
                                        <button type="button" 
                                                onclick="confirmDeleteAppointment(event, {{ $app->id }}, '{{ addslashes($app->patient->full_name) }}')" 
                                                style="background: none; border: 1px solid #fee2e2; border-radius: 0.25rem; cursor: pointer; color: #ef4444; font-size: 1.1rem; padding: 0.5rem 0.75rem; transition: all 0.2s; display: flex; align-items: center; justify-content: center;" 
                                                onmouseover="this.style.background='#fee2e2'; this.style.borderColor='#fecaca';" 
                                                onmouseout="this.style.background='none'; this.style.borderColor='#fee2e2';">
                                            🗑️
                                        </button>
                                    </div>
                                    
                                </div>

                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

{{-- Custom Confirmation Modal --}}
<div id="confirmModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.25s ease;">
    <div style="background: #fff; border-radius: 0.25rem; width: 420px; padding: 2rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); transform: scale(0.9); transition: transform 0.25s ease; border: 1px solid #e2e8f0;" id="confirmModalCard">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="width: 56px; height: 56px; background: #fee2e2; color: #ef4444; border-radius: 0.25rem; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 1rem; border: 1px solid #fecaca;">
                ⚠️
            </div>
            <h3 style="margin: 0 0 0.5rem; font-size: 1.25rem; font-weight: 800; color: #1e3a5f;" id="confirmModalTitle">Xác nhận xóa</h3>
            <p style="margin: 0; color: #64748b; font-size: 0.9rem; line-height: 1.5;" id="confirmModalText"></p>
        </div>
        <div style="display: flex; gap: 0.75rem; justify-content: center;">
            <button type="button" style="padding: 0.6rem 1.25rem; background: #fff; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-weight: 700; color: #475569; cursor: pointer; font-size: 0.85rem;" id="confirmCancelBtn">Hủy</button>
            <button type="button" style="padding: 0.6rem 1.25rem; background: #ef4444; border: none; border-radius: 0.25rem; font-weight: 700; color: #fff; cursor: pointer; font-size: 0.85rem;" id="confirmOkBtn">Xóa bỏ</button>
        </div>
    </div>
</div>

<style>
/* CSS cho dây dòng thời gian */
.timeline-container {
    position: relative;
    padding-left: 2rem;
}
.timeline-container::before {
    content: '';
    position: absolute;
    left: 7px;
    top: 15px;
    bottom: 15px;
    width: 2px;
    background: #e2e8f0;
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
}
.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-dot {
    position: absolute;
    left: -29px;
    top: 24px;
    width: 14px;
    height: 14px;
    border-radius: 0.25rem;
    background: #cbd5e1;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #cbd5e1;
    transition: all 0.25s ease;
    z-index: 1;
}

/* Đổi màu node liên kết của dây dòng thời gian theo trạng thái */
.timeline-item.completed .timeline-dot {
    background: #16a34a;
    box-shadow: 0 0 0 3px #bbf7d0;
}
.timeline-item.overdue .timeline-dot {
    background: #dc2626;
    box-shadow: 0 0 0 3px #fecaca;
}
.timeline-item.active .timeline-dot {
    background: #d97706;
    box-shadow: 0 0 0 3px #fde68a;
}

.timeline-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 0.25rem;
    padding: 1.25rem;
    transition: all 0.2s;
}
.timeline-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
}

/* Kiểu dáng cho nút Xem bệnh án */
.btn-view-records {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    font-weight: 700;
    font-size: 0.85rem;
    padding: 0.6rem 1rem;
    border-radius: 0.25rem;
    background: #3b82f6;
    color: #fff;
    transition: all 0.2s;
    border: 1px solid transparent;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(59, 130, 246, 0.15);
}
.btn-view-records:hover {
    background: #2563eb;
}
.btn-view-records.disabled {
    opacity: 0.45;
    pointer-events: none;
    cursor: not-allowed;
    background: #94a3b8;
    box-shadow: none;
}

.check-in-toggle-container:hover {
    border-color: #94a3b8 !important;
    background: #f1f5f9 !important;
}
</style>

@push('scripts')
<script>
    // Xử lý điểm danh AJAX
    document.querySelectorAll('.check-in-checkbox').forEach(chk => {
        chk.addEventListener('change', function() {
            const appointmentId = this.dataset.id;
            const isChecked = this.checked;
            const newStatus = isChecked ? 'confirmed' : 'pending';
            const labelSpan = this.nextElementSibling;
            
            // Cập nhật giao diện tạm thời
            labelSpan.innerText = isChecked ? 'Đã đến khám' : 'Tích đã đến';
            
            fetch(`/admin/appointments/${appointmentId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const btnRecord = document.getElementById(`record-btn-${appointmentId}`);
                    const timelineItem = document.getElementById(`timeline-item-${appointmentId}`);
                    const badgeContainer = document.getElementById(`status-badge-${appointmentId}`);
                    const isOverdue = timelineItem.dataset.overdue === 'true';

                    if (isChecked) {
                        // Enable nút vào xem bệnh án
                        btnRecord.classList.remove('disabled');
                        btnRecord.setAttribute('title', 'Vào xem chi tiết bệnh án');
                        
                        // Update timeline item status color
                        timelineItem.classList.remove('active', 'overdue');
                        timelineItem.classList.add('completed');

                        // Update status badge
                        badgeContainer.innerHTML = `<span style="background: #eef9ee; color: #16a34a; font-weight: 800; font-size: 0.75rem; padding: 0.2rem 0.4rem; border-radius: 0.15rem; border: 1px solid #bbf7d0;">✓ Đã đến</span>`;
                    } else {
                        // Disable nút vào xem bệnh án
                        btnRecord.classList.add('disabled');
                        btnRecord.setAttribute('title', 'Vui lòng tích Đã đến khám để vào xem bệnh án');
                        
                        // Restoring color based on overdue
                        timelineItem.classList.remove('completed');
                        if (isOverdue) {
                            timelineItem.classList.add('overdue');
                            badgeContainer.innerHTML = `<span style="background: #fef2f2; color: #dc2626; font-weight: 800; font-size: 0.75rem; padding: 0.2rem 0.4rem; border-radius: 0.15rem; border: 1px solid #fecaca;">⚠️ Vắng / Quá hạn</span>`;
                        } else {
                            timelineItem.classList.add('active');
                            badgeContainer.innerHTML = `<span style="background: #fef3c7; color: #d97706; font-weight: 800; font-size: 0.75rem; padding: 0.2rem 0.4rem; border-radius: 0.15rem; border: 1px solid #fde68a;">🕒 Chờ khám</span>`;
                        }
                    }
                } else {
                    // Rollback on failure
                    this.checked = !isChecked;
                    labelSpan.innerText = (!isChecked) ? 'Đã đến khám' : 'Tích đã đến';
                    alert(data.message || 'Có lỗi xảy ra.');
                }
            })
            .catch(err => {
                console.error(err);
                // Rollback on error
                this.checked = !isChecked;
                labelSpan.innerText = (!isChecked) ? 'Đã đến khám' : 'Tích đã đến';
                alert('Không thể kết nối máy chủ để cập nhật trạng thái.');
            });
        });
    });

    // Xác nhận xóa lịch hẹn
    const confirmModal = document.getElementById('confirmModal');
    const confirmModalCard = document.getElementById('confirmModalCard');
    const confirmModalText = document.getElementById('confirmModalText');
    const confirmCancelBtn = document.getElementById('confirmCancelBtn');
    const confirmOkBtn = document.getElementById('confirmOkBtn');

    function showConfirmModal(text, onConfirm) {
        confirmModalText.innerHTML = text;
        confirmModal.style.display = 'flex';
        setTimeout(() => {
            confirmModal.style.opacity = '1';
            confirmModalCard.style.transform = 'scale(1)';
        }, 10);

        const closeHandler = () => {
            confirmModal.style.opacity = '0';
            confirmModalCard.style.transform = 'scale(0.9)';
            setTimeout(() => {
                confirmModal.style.display = 'none';
            }, 250);
            confirmOkBtn.removeEventListener('click', confirmHandler);
            confirmCancelBtn.removeEventListener('click', closeHandler);
        };

        const confirmHandler = () => {
            onConfirm();
            closeHandler();
        };

        confirmCancelBtn.addEventListener('click', closeHandler);
        confirmOkBtn.addEventListener('click', confirmHandler);
    }

    function confirmDeleteAppointment(e, appointmentId, patientName) {
        e.preventDefault();
        showConfirmModal(
            `Bạn có chắc chắn muốn xóa lịch hẹn của bệnh nhân <strong>${patientName}</strong> không? Hành động này sẽ không thể hoàn tác.`,
            function() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/appointments/${appointmentId}`;
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                
                form.appendChild(csrfInput);
                form.appendChild(methodInput);
                document.body.appendChild(form);
                form.submit();
            }
        );
    }

    window.onclick = function(event) {
        if (event.target == confirmModal) {
            confirmModal.style.opacity = '0';
            confirmModalCard.style.transform = 'scale(0.9)';
            setTimeout(() => {
                confirmModal.style.display = 'none';
            }, 250);
        }
    }
</script>
@endpush
@endsection
