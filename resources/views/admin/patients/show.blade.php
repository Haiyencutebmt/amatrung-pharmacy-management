@extends('layouts.admin')

@section('title', 'Hồ sơ: ' . $patient->patient_code . ' — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div class="icon-bg" style="width: 44px; height: 44px; background: #eff6ff; color: #3b82f6; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; position: relative;">
        <span class="icon">👤</span>
        <span style="position: absolute; top: -2px; right: -2px; background: #22c55e; width: 12px; height: 12px; border-radius: 50%; border: 2px solid #fff;"></span>
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Hồ sơ bệnh nhân</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Chi tiết thông tin và hồ sơ bệnh án</p>
    </div>
</div>
@endsection

@section('content')
<style>
    .patient-profile-container {
        font-family: 'Inter', system-ui, sans-serif;
        color: #1e293b;
        width: 100%;
        margin-top: -1rem;
    }
    .action-bar {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 1rem;
        background: #ffffff;
        padding: 0.5rem;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        margin-bottom: 1rem;
    }
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.2rem;
        border-radius: 0.5rem;
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    .btn-secondary {
        background: transparent;
        color: #64748b;
        border-color: transparent;
    }
    .btn-secondary:hover {
        background: #f8fafc;
        color: #0f172a;
    }
    .btn-outline {
        background: #ffffff;
        color: #475569;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .btn-outline:hover {
        background: #f8fafc;
        color: #0f172a;
        border-color: #cbd5e1;
    }
    .btn-edit {
        background: transparent;
        color: #f97316;
        border-color: transparent;
    }
    .btn-edit:hover {
        background: #fff7ed;
    }
    .btn-primary {
        background: #16a34a;
        color: #ffffff;
        box-shadow: 0 4px 6px -1px rgba(22, 163, 74, 0.2), 0 2px 4px -1px rgba(22, 163, 74, 0.1);
    }
    .btn-primary:hover {
        background: #15803d;
    }
    
    .card {
        background: #ffffff;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        padding: 1.5rem;
        border: 1px solid #cbd5e1;
        margin-bottom: 1.5rem;
    }
    
    /* Top Section */
    .top-section {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        clear: both;
    }
    @media (min-width: 1024px) {
        .top-section {
            grid-template-columns: 1fr auto;
            gap: 2rem;
        }
    }
    @media (min-width: 1400px) {
        .top-section {
            grid-template-columns: 1fr auto;
        }
    }
    .top-left {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    @media (min-width: 1024px) {
        .top-left {
            padding-right: 2rem;
            border-right: 1px solid #e2e8f0;
        }
    }
    .top-right {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
    }
    .stats-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }
    
    .profile-header {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        margin-bottom: 1.25rem;
    }
    .avatar-wrapper {
        width: 64px;
        height: 64px;
        border-radius: 0.25rem;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        overflow: hidden;
        flex-shrink: 0;
    }
    .profile-info h2 {
        margin: 0 0 0.4rem 0;
        font-size: 1.4rem;
        font-weight: 800;
        color: #0f172a;
    }
    .badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .badge {
        padding: 0.2rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.78rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .badge-code {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #cbd5e1;
    }
    .badge-status {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #cbd5e1;
    }
    
    .divider {
        height: 1px;
        background: #e2e8f0;
        margin: 0 0 1.25rem 0;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
    }
    @media (max-width: 1200px) {
        .info-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
        padding-right: 1.25rem;
        border-right: 1px solid #e2e8f0;
    }
    .info-item:last-child {
        border-right: none;
        padding-right: 0;
    }
    @media (max-width: 1200px) {
        .info-item:nth-child(2n) {
            border-right: none;
            padding-right: 0;
        }
    }
    .info-label {
        font-size: 0.8rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 0.3rem;
        font-weight: 600;
    }
    .info-label .icon {
        color: #16a34a;
    }
    .info-value {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1e293b;
    }
    
    .stat-card {
        background: #ffffff;
        border-radius: 0.5rem;
        border: 1px solid #cbd5e1;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        box-shadow: none;
    }
    .stat-icon {
        width: 32px;
        height: 32px;
        border-radius: 0.25rem;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #16a34a;
        font-size: 1rem;
    }
    .stat-number {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }
    .stat-label {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 600;
        white-space: nowrap;
    }

    /* Bottom Section */
    .bottom-section {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        align-items: start;
    }
    @media (min-width: 1024px) {
        .bottom-section {
            grid-template-columns: 350px 1fr;
        }
    }
    @media (min-width: 1400px) {
        .bottom-section {
            grid-template-columns: 400px 1fr;
        }
    }
    .bottom-left {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .card-title {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0 0 1.5rem 0;
    }
    .card-title .icon-bg {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #f0fdf4;
        color: #16a34a;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }

    .list-info {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .list-item {
        display: flex;
        justify-content: space-between;
        font-size: 0.9rem;
        border-bottom: 1px dashed #f1f5f9;
        padding-bottom: 0.5rem;
    }
    .list-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .list-item-label {
        color: #64748b;
        font-weight: 500;
    }
    .list-item-value {
        color: #1e293b;
        font-weight: 600;
        text-align: right;
        max-width: 60%;
    }

    .note-box {
        background: #f8fafc;
        border-radius: 0.75rem;
        padding: 1.25rem;
        color: #64748b;
        font-size: 0.9rem;
        line-height: 1.5;
        border: 1px solid #f1f5f9;
        min-height: 80px;
    }
    
    .btn-icon {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        width: 32px;
        height: 32px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        cursor: pointer;
        margin-left: auto;
        transition: all 0.2s;
    }
    .btn-icon:hover {
        background: #e2e8f0;
    }

    /* Empty State */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4rem 2rem;
        text-align: center;
        height: 100%;
        min-height: 300px;
    }
    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        color: #dcfce7;
        position: relative;
    }
    .empty-state-icon::after {
        content: "✨";
        position: absolute;
        top: 0;
        right: -10px;
        font-size: 1.5rem;
    }
    .empty-state-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e3a5f;
        margin: 0 0 0.5rem 0;
    }
    .empty-state-desc {
        color: #64748b;
        font-size: 0.95rem;
        max-width: 350px;
        margin: 0 auto 2rem auto;
        line-height: 1.5;
    }
</style>

<div class="patient-profile-container">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <a href="{{ route('admin.patients.index') }}" class="btn btn-outline">
                <span style="color: #94a3b8;">←</span> Quay lại danh sách
            </a>
        </div>
        <div style="display: flex; gap: 1rem;">
            <button type="button" class="btn btn-outline" onclick="toggleEditForm()">
                <span style="color: #f97316;">✏️</span> Sửa thông tin
            </button>
            <a href="{{ route('admin.medical-records.legacy-create', ['patient_id' => $patient->id]) }}" class="btn btn-outline" style="background: #fffbeb; border-color: #fde68a; color: #92400e;">
                <span>📋</span> Nhập bệnh án cũ
            </a>
            <button type="button" class="btn btn-primary" style="border: none; cursor: pointer;" onclick="toggleCreateForm()">
                <span>+</span> Tạo bệnh án mới
            </button>
        </div>
    </div>

    <div class="card top-section">
        <div class="top-left">
            <div class="profile-header">
                <div class="avatar-wrapper">
                    @if($patient->gender == 'female')
                        👩
                    @elseif($patient->gender == 'male')
                        👨
                    @else
                        👤
                    @endif
                </div>
                <div class="profile-info">
                    <h2>{{ $patient->full_name }}</h2>
                    <div class="badges">
                        <span class="badge badge-code">Mã: {{ $patient->patient_code }}</span>
                        <span class="badge badge-status">✓ Đang theo dõi</span>
                        @if($patient->is_legacy_data)
                            <span class="badge" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a;">📋 Hồ sơ giấy</span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="divider"></div>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label"><span class="icon">🗓️</span> Tuổi</div>
                    <div class="info-value">{{ $patient->age ? $patient->age . ' tuổi' : '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label"><span class="icon">⚥</span> Giới tính</div>
                    <div class="info-value">{{ $patient->gender_label }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label"><span class="icon">📞</span> Điện thoại</div>
                    <div class="info-value">{{ $patient->phone ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label"><span class="icon">📍</span> Địa chỉ</div>
                    <div class="info-value">{{ $patient->address ?? '—' }}</div>
                </div>
            </div>
        </div>
        
        <div class="top-right">
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon">🩺</div>
                    <div class="stat-number">{{ $patient->medicalRecords->count() }}</div>
                    <div class="stat-label">Số lượt khám</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📄</div>
                    <div class="stat-number">0</div>
                    <div class="stat-label">Bệnh án đang theo dõi</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🗓️</div>
                    <div class="stat-number">
                        @php
                            $latestRecord = $patient->medicalRecords->sortByDesc('visit_date')->first();
                        @endphp
                        {{ $latestRecord ? $latestRecord->visit_date->format('d/m') : '—' }}
                    </div>
                    <div class="stat-label">Lần khám gần nhất</div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Modal: Sửa thông tin bệnh nhân --}}
    <div id="editPatientInline" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.4); z-index: 1000; overflow-y: auto; backdrop-filter: blur(4px); padding: 2rem 0; flex-direction: column; align-items: center; justify-content: flex-start; box-sizing: border-box;">
        <div style="background: #fff; border-radius: 0.5rem; width: 100%; max-width: 800px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); animation: modalSlideIn 0.25s ease-out; margin: 0 auto; padding: 1.5rem 2rem; border: 1px solid #cbd5e1; box-sizing: border-box;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="width: 32px; height: 32px; background: #fff7ed; color: #f97316; border-radius: 0.25rem; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; border: 1px solid #cbd5e1;">✏️</div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a;">Sửa Thông Tin Bệnh Nhân</h3>
                        <p style="margin: 0; font-size: 0.78rem; color: #64748b;">Mã BN: <strong>{{ $patient->patient_code }}</strong></p>
                    </div>
                </div>
                <button type="button" onclick="toggleEditForm()" style="background: none; border: none; font-size: 1.5rem; color: #94a3b8; cursor: pointer; width: 32px; height: 32px; border-radius: 0.25rem; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">&times;</button>
            </div>

            <form action="{{ route('admin.patients.update', $patient) }}" method="POST">
                @csrf
                @method('PUT')

                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Họ và tên <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="full_name" value="{{ $patient->full_name }}" required style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; box-sizing: border-box; background: #fff;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Ngày sinh <span style="color: #ef4444;">*</span></label>
                        <input type="date" name="date_of_birth" value="{{ $patient->date_of_birth->format('Y-m-d') }}" required style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; box-sizing: border-box; background: #fff;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Giới tính <span style="color: #ef4444;">*</span></label>
                        <select name="gender" required style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; background: #fff; box-sizing: border-box;">
                            <option value="male" {{ $patient->gender == 'male' ? 'selected' : '' }}>Nam</option>
                            <option value="female" {{ $patient->gender == 'female' ? 'selected' : '' }}>Nữ</option>
                            <option value="other" {{ $patient->gender == 'other' ? 'selected' : '' }}>Khác</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Số điện thoại</label>
                        <input type="text" name="phone" value="{{ $patient->phone }}" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; box-sizing: border-box; background: #fff;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Địa chỉ <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="address" value="{{ $patient->address }}" required style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; box-sizing: border-box; background: #fff;">
                    </div>
                </div>

                @if($patient->is_legacy_data)
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; margin-bottom: 1rem; background: #fffbeb; padding: 0.85rem; border-radius: 0.25rem; border: 1px solid #fde68a;">
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #92400e; margin-bottom: 0.3rem;">Ngày ghi hồ sơ gốc</label>
                        <input type="date" name="legacy_date" value="{{ $patient->legacy_date ? $patient->legacy_date->format('Y-m-d') : '' }}" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #fde68a; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; box-sizing: border-box; background: #fff;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #92400e; margin-bottom: 0.3rem;">Ghi chú hồ sơ giấy</label>
                        <input type="text" name="legacy_note" value="{{ $patient->legacy_note }}" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #fde68a; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; box-sizing: border-box; background: #fff;">
                    </div>
                </div>
                @endif

                <div style="background: #f8fafc; padding: 1rem; border-radius: 0.25rem; margin-bottom: 1rem; border: 1px dashed #cbd5e1;">
                    <div style="display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.75rem;">
                        <input type="checkbox" name="is_guardian_phone" id="is_guardian_phone_edit" value="1" {{ $patient->is_guardian_phone ? 'checked' : '' }} style="width: 16px; height: 16px; cursor: pointer;">
                        <label for="is_guardian_phone_edit" style="font-size: 0.82rem; font-weight: 700; color: #475569; cursor: pointer;">Sử dụng thông tin người giám hộ</label>
                    </div>
                    
                    <div id="guardian_fields_edit" style="display: {{ $patient->is_guardian_phone ? 'grid' : 'none' }}; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 600; color: #64748b; margin-bottom: 0.3rem;">Tên người giám hộ</label>
                            <input type="text" name="guardian_name" value="{{ $patient->guardian_name }}" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; box-sizing: border-box; background: #fff;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 600; color: #64748b; margin-bottom: 0.3rem;">SĐT người giám hộ</label>
                            <input type="text" name="guardian_phone" value="{{ $patient->guardian_phone }}" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; box-sizing: border-box; background: #fff;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 600; color: #64748b; margin-bottom: 0.3rem;">Quan hệ</label>
                            <input type="text" name="relationship" value="{{ $patient->relationship }}" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; box-sizing: border-box; background: #fff;">
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Ghi chú tiểu sử</label>
                    <textarea name="note" rows="2" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; resize: vertical; box-sizing: border-box; background: #fff;">{{ $patient->note }}</textarea>
                </div>

                <div style="display: flex; gap: 0.75rem; justify-content: flex-end; border-top: 1px solid #f1f5f9; padding-top: 1rem;">
                    <button type="button" onclick="toggleEditForm()" style="padding: 0.55rem 1.25rem; background: #fff; color: #64748b; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; font-weight: 600; cursor: pointer;">Hủy</button>
                    <button type="submit" style="padding: 0.55rem 1.25rem; background: #f97316; color: #fff; border: none; border-radius: 0.25rem; font-size: 0.85rem; font-weight: 700; cursor: pointer;">💾 Cập Nhật</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Tạo bệnh án mới --}}
    <div id="createRecordInline" class="modal-overlay" style="display:{{ old('from_patient') ? 'flex' : 'none' }}; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.4); z-index:1000; overflow-y:auto; backdrop-filter:blur(4px); padding:2rem 0; flex-direction:column; align-items:center; justify-content:flex-start; box-sizing:border-box;">
        <div style="background:#fff; border-radius:0.5rem; width:95%; max-width:1250px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); animation:modalSlideIn 0.25s ease-out; margin:0 auto; padding:1.5rem 2rem; border:1px solid #5eb542; box-sizing:border-box;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; border-bottom:1px solid #f1f5f9; padding-bottom:1rem;">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="width:40px; height:40px; background:#f0fdf4; color:#16a34a; border-radius:0.25rem; display:flex; align-items:center; justify-content:center; font-size:1.2rem;">🩺</div>
                    <div>
                        <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:#0f172a;">Tạo Bệnh Án Mới</h3>
                        <p style="margin:0; font-size:0.8rem; color:#64748b;">Bệnh nhân: <strong>{{ $patient->full_name }}</strong> ({{ $patient->patient_code }})</p>
                    </div>
                </div>
                <button type="button" onclick="toggleCreateForm()" style="background:none; border:none; font-size:1.5rem; color:#94a3b8; cursor:pointer; width:36px; height:36px; border-radius:0.25rem; display:flex; align-items:center; justify-content:center;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">&times;</button>
            </div>

            <form action="{{ route('admin.medical-records.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                <input type="hidden" name="from_patient" value="1">

                @if(old('from_patient') && $errors->any())
                    <div style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:0.35rem; padding:0.75rem 1rem; margin-bottom:1rem; font-size:0.86rem; font-weight:600; line-height:1.5;">
                        <div style="font-weight:800; margin-bottom:0.25rem;">Không thể tạo bệnh án mới:</div>
                        @foreach($errors->all() as $error)
                            <div>- {{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                {{-- Row 1: Visit Date, Weight, Height (3 columns) --}}
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label style="display:block; font-size:0.82rem; font-weight:700; color:#475569; margin-bottom:0.4rem;">Ngày khám <span style="color:#ef4444;">*</span></label>
                        <input type="date" name="visit_date" value="{{ old('visit_date', date('Y-m-d')) }}" required style="width:100%; padding:0.6rem 0.8rem; border:1px solid #cbd5e1; border-radius:0.25rem; font-size:0.9rem; color:#1e293b; box-sizing:border-box; height:38px;">
                    </div>
                    <div>
                        <label style="display:block; font-size:0.82rem; font-weight:700; color:#475569; margin-bottom:0.4rem;">Cân nặng (kg)</label>
                        <input type="number" name="weight" step="0.1" min="0" max="500" value="{{ old('weight') }}" placeholder="VD: 55.5" style="width:100%; padding:0.6rem 0.8rem; border:1px solid #cbd5e1; border-radius:0.25rem; font-size:0.9rem; color:#1e293b; box-sizing:border-box; height:38px;">
                    </div>
                    <div>
                        <label style="display:block; font-size:0.82rem; font-weight:700; color:#475569; margin-bottom:0.4rem;">Chiều cao (cm)</label>
                        <input type="number" name="height" step="0.1" min="0" max="300" value="{{ old('height') }}" placeholder="VD: 160" style="width:100%; padding:0.6rem 0.8rem; border:1px solid #cbd5e1; border-radius:0.25rem; font-size:0.9rem; color:#1e293b; box-sizing:border-box; height:38px;">
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
                            <input type="radio" name="case_type" value="normal" {{ in_array(old('case_type', 'normal'), ['normal', 'general'], true) ? 'checked' : '' }} onchange="toggleCaseTypeModalPatient(this.value)">
                            <span class="patient-case-radio-dot"></span>
                            <span class="patient-case-icon patient-case-icon-green">🩺</span>
                            <span class="patient-case-copy">
                                <strong>Khám thông thường</strong>
                                <small>(Bốc thuốc uống...)</small>
                            </span>
                        </label>
                        <label class="patient-case-type-card patient-case-type-card-purple">
                            <input type="radio" name="case_type" value="musculoskeletal" {{ old('case_type') === 'musculoskeletal' ? 'checked' : '' }} onchange="toggleCaseTypeModalPatient(this.value)">
                            <span class="patient-case-radio-dot"></span>
                            <span class="patient-case-icon patient-case-icon-purple">🦴</span>
                            <span class="patient-case-copy">
                                <strong>Xương khớp - Chấn thương - Trị liệu ngoài</strong>
                            </span>
                        </label>
                        <label class="patient-case-type-card patient-case-type-card-blue">
                            <input type="radio" name="case_type" value="combined" {{ in_array(old('case_type'), ['combined', 'both'], true) ? 'checked' : '' }} onchange="toggleCaseTypeModalPatient(this.value)">
                            <span class="patient-case-radio-dot"></span>
                            <span class="patient-case-icon patient-case-icon-blue">🔄</span>
                            <span class="patient-case-copy">
                                <strong>Khám kết hợp cả hai</strong>
                            </span>
                        </label>
                    </div>
                </div>

                {{-- Row: Traditional Diagnosis Helper --}}
                <div id="traditional_exam_modal_patient" class="patient-tcm-panel">
                    <div class="patient-section-title patient-section-title-green">
                        <span>🌿</span>
                        Tứ chẩn & Biện chứng
                    </div>
                    <div class="patient-tcm-grid">
                        <div class="patient-tcm-card">
                            <span class="patient-tcm-icon">💬</span>
                            <div class="patient-tcm-body">
                                <label for="tcm_inquiry_modal_patient">Hỏi bệnh</label>
                                <textarea id="tcm_inquiry_modal_patient" name="tcm_inquiry" rows="2" oninput="syncTraditionalExamPatient()" placeholder="Ghi nhận diễn biến bệnh, hoàn cảnh, thói quen, khẩu vị, giấc ngủ...">{{ old('tcm_inquiry') }}</textarea>
                            </div>
                        </div>
                        <div class="patient-tcm-card">
                            <span class="patient-tcm-icon">👁️</span>
                            <div class="patient-tcm-body">
                                <label for="tcm_observation_modal_patient">Vọng + Văn chẩn</label>
                                <textarea id="tcm_observation_modal_patient" name="tcm_observation" rows="2" oninput="syncTraditionalExamPatient()" placeholder="Quan sát thần sắc, hình thái, lưỡi, rêu lưỡi, sắc mặt, khí sắc...">{{ old('tcm_observation') }}</textarea>
                            </div>
                        </div>
                        <div class="patient-tcm-card">
                            <span class="patient-tcm-icon">〰️</span>
                            <div class="patient-tcm-body">
                                <label for="tcm_pulse_modal_patient">Bắt mạch</label>
                                <textarea id="tcm_pulse_modal_patient" name="tcm_pulse" rows="2" oninput="syncTraditionalExamPatient()" placeholder="Ghi nhận mạch tượng: tả - hữu, phù - trầm, khẩn - hoãn...">{{ old('tcm_pulse') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Row: Musculoskeletal Special Fields (Hidden by default) --}}
                <div id="musculoskeletal_fields_modal_patient" style="display: none; margin-bottom: 1rem; border: 1px solid #f87171; border-radius: 0.25rem; padding: 1.25rem; background: #fffcfc;">
                    <h4 style="margin: 0 0 1rem; font-size: 0.95rem; color: #b91c1c; font-weight: 800; display: flex; align-items: center; gap: 0.4rem; border-bottom: 1px solid #fee2e2; padding-bottom: 0.5rem;">
                        <span>🦴</span> Khám Xương Khớp & Chấn thương
                    </h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Loại tổn thương / Bệnh xương khớp</label>
                            <select name="injury_type" onchange="updateRecordFieldsModalPatient()" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; background: #fff;">
                                <option value="">-- Chọn loại tổn thương --</option>
                                <option value="bong_gan" {{ old('injury_type') === 'bong_gan' ? 'selected' : '' }}>Bong gân</option>
                                <option value="trat_khop" {{ old('injury_type') === 'trat_khop' ? 'selected' : '' }}>Trật khớp</option>
                                <option value="nghi_gay_xuong" {{ old('injury_type') === 'nghi_gay_xuong' ? 'selected' : '' }}>Nghi gãy xương / Rạn xương nhẹ</option>
                                <option value="dau_vai_gay" {{ old('injury_type') === 'dau_vai_gay' ? 'selected' : '' }}>Đau vai gáy</option>
                                <option value="dau_lung" {{ old('injury_type') === 'dau_lung' ? 'selected' : '' }}>Đau lưng / Thoái hóa cột sống</option>
                                <option value="dau_goi" {{ old('injury_type') === 'dau_goi' ? 'selected' : '' }}>Đau khớp gối</option>
                                <option value="khac" {{ old('injury_type') === 'khac' ? 'selected' : '' }}>Loại tổn thương khác</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Vị trí chấn thương / Vùng bị đau</label>
                            <input type="text" name="injury_location" value="{{ old('injury_location') }}" placeholder="VD: Khớp cổ chân trái, Đầu gối phải..." style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; box-sizing: border-box;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Nguyên nhân chấn thương</label>
                            <input type="text" name="injury_cause" value="{{ old('injury_cause') }}" placeholder="VD: Ngã xe, mang vác vật nặng..." style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; box-sizing: border-box;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Mức độ chấn thương</label>
                            <select name="pain_level" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; background: #fff;">
                                <option value="">-- Chọn mức độ chấn thương --</option>
                                <option value="3" {{ old('pain_level') == 3 ? 'selected' : '' }}>Nhẹ</option>
                                <option value="5" {{ old('pain_level') == 5 ? 'selected' : '' }}>Trung bình</option>
                                <option value="8" {{ old('pain_level') == 8 ? 'selected' : '' }}>Nặng</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Dấu hiệu lâm sàng bên ngoài</label>
                            <textarea name="clinical_signs" placeholder="VD: Sưng nề to vùng cổ chân, bầm tím dưới da..." rows="2" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; resize: vertical; box-sizing: border-box;">{{ old('clinical_signs') }}</textarea>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Kết quả sờ nắn / Nắm chỉnh</label>
                            <textarea name="palpation_result" placeholder="VD: Ấn đau nhói..." rows="2" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; resize: vertical; box-sizing: border-box;">{{ old('palpation_result') }}</textarea>
                        </div>
                    </div>

                    <div style="margin-bottom: 0.5rem;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Xem ảnh chụp phim (nếu có)</label>
                        <input type="text" name="xray_note" value="{{ old('xray_note') }}" placeholder="VD: Phim mang từ viện về: Khe khớp bình thường, không rạn gãy..." style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; box-sizing: border-box;">
                    </div>
                </div>

                {{-- Row 2: Symptoms & Diagnosis (2 columns side-by-side) --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div id="symptoms_col_modal_patient">
                        <label class="patient-bottom-field-label"><span>📋</span> Triệu chứng <strong>*</strong></label>
                        <textarea id="symptoms_modal_patient" name="symptoms" required rows="3" oninput="markTraditionalFieldManualPatient('symptoms')" placeholder="Ghi nhận lời khai và triệu chứng của bệnh nhân..." style="width:100%; padding:0.6rem 0.8rem; border:1px solid #cbd5e1; border-radius:0.25rem; font-size:0.9rem; color:#1e293b; resize:vertical; box-sizing:border-box;">{{ old('symptoms') }}</textarea>
                    </div>
                    <div id="diagnosis_col_modal_patient" style="display:none;">
                        <label class="patient-bottom-field-label"><span>🎯</span> Chẩn đoán <small style="color:#94a3b8;">(có thể bổ sung sau)</small></label>
                        <textarea id="diagnosis_modal_patient" name="diagnosis" rows="3" oninput="markTraditionalFieldManualPatient('diagnosis')" placeholder="Có thể để trống để AI hỗ trợ nhận định sơ bộ ở trang chi tiết..." style="width:100%; padding:0.6rem 0.8rem; border:1px solid #cbd5e1; border-radius:0.25rem; font-size:0.9rem; color:#1e293b; resize:vertical; box-sizing:border-box;">{{ old('diagnosis') }}</textarea>
                    </div>
                </div>



                {{-- Footer Action Buttons --}}
                <div style="display:flex; gap:1rem; justify-content:flex-end; border-top:1px solid #f1f5f9; padding-top:1.25rem;">
                    <button type="button" onclick="toggleCreateForm()" style="padding:0.6rem 1.5rem; background:#fff; color:#64748b; border:1px solid #cbd5e1; border-radius:0.25rem; font-size:0.9rem; font-weight:600; cursor:pointer;">Hủy</button>
                    <button type="submit" style="padding:0.6rem 1.5rem; background:#5eb542; color:#fff; border:none; border-radius:0.25rem; font-size:0.9rem; font-weight:700; cursor:pointer; box-shadow:0 2px 6px rgba(94,181,66,0.15);">Lưu Bệnh Án</button>
                </div>
            </form>
        </div>
    </div>

    <div class="bottom-section">
        <div class="bottom-left">
            <div class="card" style="margin-bottom: 0; padding: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem;">
                
                <!-- Section: Ghi chú y tế / Tiền sử -->
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.6rem;">
                        <h4 style="margin: 0; font-size: 0.9rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 0.4rem;">
                            <span style="color: #16a34a; font-size: 0.95rem;">📝</span> Ghi chú y tế & Tiền sử
                        </h4>
                        <button type="button" onclick="toggleEditForm()" style="background: none; border: none; font-size: 0.8rem; color: #f97316; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem; font-weight: 700; padding: 0.2rem 0.4rem; border-radius: 0.25rem; transition: background 0.2s;" onmouseover="this.style.background='#fff7ed'" onmouseout="this.style.background='none'">
                            <span>✏️</span> Sửa
                        </button>
                    </div>
                    <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 0.25rem; padding: 0.75rem 1rem; color: #334155; font-size: 0.85rem; line-height: 1.5;">
                        {{ $patient->note ?: 'Chưa có ghi chú y tế hoặc tiền sử bệnh.' }}
                    </div>
                </div>

                <!-- Section: Người giám hộ -->
                <div style="border-top: 1px solid #e2e8f0; padding-top: 1rem;">
                    <div style="margin-bottom: 0.6rem;">
                        <h4 style="margin: 0; font-size: 0.9rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 0.4rem;">
                            <span style="color: #e11d48; font-size: 0.95rem;">🛡️</span> Người giám hộ
                        </h4>
                    </div>
                    @if($patient->is_guardian_phone || $patient->guardian_name || $patient->guardian_phone)
                        <div style="background: #fff; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; overflow: hidden;">
                            <div style="display: flex; border-bottom: 1px solid #f1f5f9; padding: 0.5rem 0.75rem; align-items: center;">
                                <div style="width: 100px; color: #64748b; font-weight: 600; flex-shrink: 0;">Họ và tên</div>
                                <div style="color: #1e293b; font-weight: 700;">{{ $patient->guardian_name ?? '—' }}</div>
                            </div>
                            <div style="display: flex; border-bottom: 1px solid #f1f5f9; padding: 0.5rem 0.75rem; align-items: center;">
                                <div style="width: 100px; color: #64748b; font-weight: 600; flex-shrink: 0;">Quan hệ</div>
                                <div style="color: #1e293b; font-weight: 700;">{{ $patient->relationship ?? '—' }}</div>
                            </div>
                            <div style="display: flex; padding: 0.5rem 0.75rem; align-items: center;">
                                <div style="width: 100px; color: #64748b; font-weight: 600; flex-shrink: 0;">Điện thoại</div>
                                <div style="color: #1e293b; font-weight: 700;">{{ $patient->guardian_phone ?? '—' }}</div>
                            </div>
                        </div>
                    @else
                        <div style="color: #94a3b8; font-size: 0.8rem; font-style: italic; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 0.25rem; padding: 0.6rem; text-align: center;">
                            Không yêu cầu người giám hộ (Bệnh nhân tự chủ)
                        </div>
                    @endif
                </div>

                <!-- Section: Dữ liệu hồ sơ giấy -->
                @if($patient->is_legacy_data)
                <div style="border-top: 1px solid #e2e8f0; padding-top: 1rem;">
                    <div style="margin-bottom: 0.6rem;">
                        <h4 style="margin: 0; font-size: 0.9rem; font-weight: 800; color: #92400e; display: flex; align-items: center; gap: 0.4rem;">
                            <span style="color: #d97706; font-size: 0.95rem;">📋</span> Dữ liệu hồ sơ giấy gốc
                        </h4>
                    </div>
                    <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 0.25rem; padding: 0.75rem 1rem; font-size: 0.82rem; color: #92400e; line-height: 1.6;">
                        @if($patient->legacy_date)
                            <div style="margin-bottom: 0.25rem;"><strong>Ngày lập hồ sơ giấy:</strong> {{ $patient->legacy_date->format('d/m/Y') }}</div>
                        @endif
                        @if($patient->imported_at)
                            <div style="margin-bottom: 0.25rem;"><strong>Ngày chuyển số:</strong> {{ $patient->imported_at->format('d/m/Y H:i') }}</div>
                        @endif
                        @if($patient->legacy_note)
                            <div style="margin-top: 0.4rem; padding-top: 0.4rem; border-top: 1px dashed #fde68a;">
                                <strong>Ghi chú gốc:</strong> {{ $patient->legacy_note }}
                            </div>
                        @endif
                    </div>
                </div>
                @endif

            </div>
        </div>

        <div class="bottom-right">
            <div class="card" style="height: 100%; margin-bottom: 0; display: flex; flex-direction: column;">
                <h3 class="card-title">
                    <div class="icon-bg" style="background: #f0fdf4; color: #16a34a;">🕒</div>
                    Lịch sử khám bệnh
                </h3>

                @if($patient->medicalRecords->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 0.75rem; flex: 1;">
                        @foreach($patient->medicalRecords()->orderByDesc('visit_date')->get() as $record)
                            <div class="record-expand-card" id="record-card-{{ $record->id }}">
                                <div class="record-expand-header" onclick="toggleRecordDetail({{ $record->id }})" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; cursor: pointer; border-radius: 0.75rem; transition: background 0.2s;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="width: 38px; height: 38px; border-radius: 0.5rem; background: #f0fdf4; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">🩺</div>
                                        <div>
                                            <div style="font-weight: 700; color: #0f172a; font-size: 0.95rem; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                                <span style="background: #f0f7ff; color: #3b82f6; padding: 0.15rem 0.5rem; border-radius: 0.4rem; font-size: 0.78rem; font-weight: 800; border: 1px solid #dbeafe;">{{ $record->record_code }}</span>
                                                @if($record->is_legacy_data)
                                                    <span style="display: inline-flex; align-items: center; gap: 0.2rem; padding: 0.1rem 0.45rem; background: #fef3c7; color: #92400e; border-radius: 9999px; font-size: 0.65rem; font-weight: 600;">📋 Cũ</span>
                                                @endif
                                            </div>
                                            <div style="color: #64748b; font-size: 0.8rem;">{{ $record->visit_date->format('d/m/Y') }} · {{ $record->staff->name ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        @if($record->prescriptions->count() > 0)
                                            <span style="display: inline-flex; align-items: center; gap: 0.2rem; padding: 0.15rem 0.5rem; background: #eff6ff; color: #2563eb; border-radius: 9999px; font-size: 0.7rem; font-weight: 600;">💊 {{ $record->prescriptions->count() }}</span>
                                        @endif
                                        <span id="arrow-{{ $record->id }}" style="font-size: 0.8rem; color: #94a3b8; transition: transform 0.3s;">▼</span>
                                    </div>
                                </div>

                                <div id="detail-{{ $record->id }}" style="display: none; padding: 0 1.25rem 1.25rem;">
                                    @if($record->weight || $record->height)
                                    <div style="display: flex; gap: 1rem; margin-bottom: 0.6rem;">
                                        @if($record->weight)
                                        <div style="background: #f0f9ff; padding: 0.5rem 0.85rem; border-radius: 0.5rem; flex: 1;">
                                            <span style="font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Cân nặng</span>
                                            <div style="font-weight: 700; color: #0369a1; font-size: 0.95rem;">{{ floatval($record->weight) }} kg</div>
                                        </div>
                                        @endif
                                        @if($record->height)
                                        <div style="background: #faf5ff; padding: 0.5rem 0.85rem; border-radius: 0.5rem; flex: 1;">
                                            <span style="font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Chiều cao</span>
                                            <div style="font-weight: 700; color: #7c3aed; font-size: 0.95rem;">{{ floatval($record->height) }} cm</div>
                                        </div>
                                        @endif
                                    </div>
                                    @endif
                                    <div style="background: #f8fafc; padding: 0.85rem; border-radius: 0.6rem; margin-bottom: 0.6rem;">
                                        <div style="font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.2rem;">Triệu chứng</div>
                                        <div style="color: #1e293b; font-size: 0.85rem; line-height: 1.5;">{{ $record->symptoms ?? '—' }}</div>
                                    </div>
                                    <div style="background: #f0fdf4; padding: 0.85rem; border-radius: 0.6rem; margin-bottom: 0.6rem;">
                                        <div style="font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.2rem;">Chẩn đoán</div>
                                        <div style="color: #15803d; font-size: 0.85rem; font-weight: 600; line-height: 1.5;">{{ $record->diagnosis ?? '—' }}</div>
                                    </div>
                                    @if($record->treatment_plan)
                                    <div style="background: #faf5ff; padding: 0.85rem; border-radius: 0.6rem; margin-bottom: 0.6rem;">
                                        <div style="font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.2rem;">Điều trị</div>
                                        <div style="color: #6b21a8; font-size: 0.85rem; line-height: 1.5;">{{ $record->treatment_plan }}</div>
                                    </div>
                                    @endif
                                    @if($record->doctor_note)
                                    <div style="background: #fffbeb; padding: 0.85rem; border-radius: 0.6rem; margin-bottom: 0.6rem;">
                                        <div style="font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.2rem;">Ghi chú</div>
                                        <div style="color: #92400e; font-size: 0.85rem; line-height: 1.5;">{{ $record->doctor_note }}</div>
                                    </div>
                                    @endif

                                    @if($record->prescriptions->count() > 0)
                                    @foreach($record->prescriptions as $px)
                                    <div style="border: 1px solid #e8ecf4; border-radius: 0.6rem; overflow: hidden; margin-bottom: 0.5rem;">
                                        <div style="background: #f8fafc; padding: 0.5rem 0.85rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9;">
                                            <span style="font-weight: 700; font-size: 0.8rem; color: #1e3a5f;">💊 Đơn thuốc @if($px->is_legacy_data)<span style="padding: 0.1rem 0.35rem; background: #fef3c7; color: #92400e; border-radius: 9999px; font-size: 0.6rem;">Cũ</span>@endif</span>
                                            <a href="{{ route('admin.prescriptions.show', $px) }}" style="font-size: 0.72rem; color: #5eb542; font-weight: 600; text-decoration: none;">Xem/In →</a>
                                        </div>
                                        <table style="width: 100%; border-collapse: collapse; font-size: 0.8rem;">
                                            <thead>
                                                <tr style="background: #fafbfc;">
                                                    <th style="padding: 0.35rem 0.6rem; text-align: left; font-weight: 600; color: #64748b; font-size: 0.7rem; text-transform: uppercase;">Dược liệu</th>
                                                    <th style="padding: 0.35rem 0.6rem; text-align: right; font-weight: 600; color: #64748b; font-size: 0.7rem;">SL</th>
                                                    <th style="padding: 0.35rem 0.6rem; text-align: left; font-weight: 600; color: #64748b; font-size: 0.7rem;">Liều dùng</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($px->items as $item)
                                                <tr>
                                                    <td style="padding: 0.3rem 0.6rem; color: #1e293b; font-weight: 500;">{{ $item->medicinalHerb->name ?? 'N/A' }}</td>
                                                    <td style="padding: 0.3rem 0.6rem; text-align: right; font-weight: 700;">{{ floatval($item->quantity) }} {{ $item->unit }}</td>
                                                    <td style="padding: 0.3rem 0.6rem; color: #64748b;">{{ $item->dosage ?? '—' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @endforeach
                                    @endif

                                    <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #f1f5f9;">
                                        <a href="{{ route('admin.medical-records.show', $record) }}" style="padding: 0.3rem 0.7rem; background: #fff; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; color: #475569; text-decoration: none;">👁️ Xem đầy đủ</a>
                                        @if(auth()->user()->hasPermission('prescriptions.create'))
                                        <a href="{{ route('admin.medical-records.show', $record) }}" style="padding: 0.3rem 0.7rem; background: #eff6ff; border: 1px solid #dbeafe; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; color: #2563eb; text-decoration: none;">💊 Kê đơn</a>
                                        @endif
                                        @if(auth()->user()->hasPermission('medical_records.edit'))
                                        <a href="{{ route('admin.medical-records.edit', $record) }}" style="padding: 0.3rem 0.7rem; background: #fff; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; color: #f59e0b; text-decoration: none;">✏️ Sửa</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state" style="flex: 1;">
                        <div class="empty-state-icon">🩺</div>
                        <h4 class="empty-state-title">Chưa có lịch sử khám bệnh</h4>
                        <p class="empty-state-desc">Bệnh nhân này chưa thực hiện bất kỳ lượt thăm khám nào tại phòng khám.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .record-expand-card {
        border: 1px solid #f1f5f9;
        border-radius: 0.75rem;
        background: #fff;
        transition: all 0.3s;
    }
    .record-expand-card:hover {
        border-color: #e2e8f0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .record-expand-card.expanded {
        border-color: #5eb54240;
        box-shadow: 0 4px 12px rgba(94,181,66,0.08);
    }
    .record-expand-header:hover {
        background: #fafbfc;
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
    @media (max-width: 900px) {
        .patient-case-type-grid,
        .patient-tcm-grid {
            grid-template-columns: 1fr;
        }
        .patient-case-type-card {
            grid-template-columns: auto auto 1fr;
        }
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

@push('scripts')
<script>
function getTraditionalExamPatientValue(id) {
    return (document.getElementById(id)?.value || '').trim();
}

function markTraditionalFieldManualPatient(field) {
    const input = field === 'diagnosis'
        ? document.getElementById('diagnosis_modal_patient')
        : document.getElementById('symptoms_modal_patient');

    if (input) {
        input.dataset.tcmAuto = '0';
    }
}

function syncTraditionalExamPatient() {
    const caseType = document.querySelector('#createRecordInline input[name="case_type"]:checked')?.value || 'normal';
    if (caseType === 'musculoskeletal') {
        return;
    }

    const symptomsInput = document.getElementById('symptoms_modal_patient');
    const diagnosisInput = document.getElementById('diagnosis_modal_patient');
    const inquiry = getTraditionalExamPatientValue('tcm_inquiry_modal_patient');
    const observation = getTraditionalExamPatientValue('tcm_observation_modal_patient');
    const pulse = getTraditionalExamPatientValue('tcm_pulse_modal_patient');
    const pattern = getTraditionalExamPatientValue('tcm_pattern_modal_patient');

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

function updateRecordFieldsModalPatient() {
    const caseType = document.querySelector('#createRecordInline input[name="case_type"]:checked')?.value || 'normal';
    const injuryTypeSelect = document.querySelector('#createRecordInline select[name="injury_type"]');
    const injuryType = injuryTypeSelect ? injuryTypeSelect.value : '';

    const symptomsCol = document.getElementById('symptoms_col_modal_patient');
    const diagnosisCol = document.getElementById('diagnosis_col_modal_patient');
    const gridRow = symptomsCol?.parentElement;
    const traditionalExam = document.getElementById('traditional_exam_modal_patient');

    const symptomsInput = document.querySelector('#createRecordInline textarea[name="symptoms"]');
    const diagnosisInput = document.querySelector('#createRecordInline textarea[name="diagnosis"]');

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

    syncTraditionalExamPatient();
}

function toggleCaseTypeModalPatient(value) {
    const musculoskeletalBox = document.getElementById('musculoskeletal_fields_modal_patient');
    if (musculoskeletalBox) {
        musculoskeletalBox.style.display = (value === 'musculoskeletal' || value === 'combined') ? 'block' : 'none';
    }
    updateRecordFieldsModalPatient();
}

document.addEventListener('DOMContentLoaded', function() {
    updateRecordFieldsModalPatient();
});

function toggleRecordDetail(id) {
    var detail = document.getElementById('detail-' + id);
    var arrow = document.getElementById('arrow-' + id);
    var card = document.getElementById('record-card-' + id);
    if (detail.style.display === 'none') {
        detail.style.display = 'block';
        arrow.style.transform = 'rotate(180deg)';
        card.classList.add('expanded');
    } else {
        detail.style.display = 'none';
        arrow.style.transform = 'rotate(0deg)';
        card.classList.remove('expanded');
    }
}

function toggleCreateForm() {
    var form = document.getElementById('createRecordInline');
    var editForm = document.getElementById('editPatientInline');
    if (form.style.display === 'none' || form.style.display === '') {
        editForm.style.display = 'none'; // Hide edit form if open
        form.style.display = 'flex';
    } else {
        form.style.display = 'none';
    }
}

function toggleEditForm() {
    var form = document.getElementById('editPatientInline');
    var createForm = document.getElementById('createRecordInline');
    if (form.style.display === 'none' || form.style.display === '') {
        createForm.style.display = 'none'; // Hide create form if open
        form.style.display = 'flex';
    } else {
        form.style.display = 'none';
    }
}

// Logic for guardian fields in edit form
if (document.getElementById('is_guardian_phone_edit')) {
    document.getElementById('is_guardian_phone_edit').addEventListener('change', function() {
        document.getElementById('guardian_fields_edit').style.display = this.checked ? 'grid' : 'none';
    });
}

// Close modals on click outside or ESC key
document.getElementById('createRecordInline')?.addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
document.getElementById('editPatientInline')?.addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        var m1 = document.getElementById('createRecordInline');
        if (m1) m1.style.display = 'none';
        var m2 = document.getElementById('editPatientInline');
        if (m2) m2.style.display = 'none';
    }
});
</script>
@endpush
@endsection
