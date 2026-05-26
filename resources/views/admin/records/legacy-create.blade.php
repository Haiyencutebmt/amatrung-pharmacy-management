@extends('layouts.admin')

@section('title', 'Nhập bệnh án cũ — ' . $patient->full_name . ' — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div class="icon-bg" style="width: 44px; height: 44px; background: #fef3c7; color: #f59e0b; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
        <span class="icon">📋</span>
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Nhập bệnh án cũ</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Ghi nhận lịch sử khám bệnh từ sổ sách cho <strong>{{ $patient->full_name }}</strong> ({{ $patient->patient_code }})</p>
    </div>
</div>
@endsection

@section('header-right')
<a href="{{ route('admin.patients.show', $patient) }}" style="color: #64748b; text-decoration: none; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; background: #fff; padding: 0.6rem 1.2rem; border-radius: 12px; border: 1px solid #f0f3ff; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#cbd5e1'" onmouseout="this.style.background='#fff'; this.style.borderColor='#f0f3ff'">
    ⬅️ <span>Quay lại</span>
</a>
@endsection

@section('content')
<style>
    .legacy-record-container {
        font-family: 'Inter', system-ui, sans-serif;
        width: 100%;
        margin-top: -1rem;
    }
    .legacy-notice {
        background: linear-gradient(135deg, #fef9c3 0%, #fef3c7 100%);
        border: 1px solid #fde68a;
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .legacy-notice-icon {
        width: 40px;
        height: 40px;
        border-radius: 0.75rem;
        background: #fbbf2420;
        color: #f59e0b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .legacy-notice-text h4 {
        margin: 0 0 0.25rem 0;
        font-size: 0.95rem;
        font-weight: 700;
        color: #92400e;
    }
    .legacy-notice-text p {
        margin: 0;
        font-size: 0.85rem;
        color: #a16207;
        line-height: 1.5;
    }
    .form-card {
        background: #ffffff;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        padding: 2rem;
        border: 1px solid #f1f5f9;
        margin-bottom: 1.5rem;
    }
    .form-section-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 1rem;
    }
    .form-section-title .icon-box {
        width: 36px;
        height: 36px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    .form-section-title h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 800;
        color: #1e3a5f;
    }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .form-group.full-width {
        grid-column: span 2;
    }
    .form-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
    }
    .form-input {
        padding: 0.7rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        font-size: 0.95rem;
        color: #1e293b;
        background: #fff;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
        width: 100%;
    }
    .form-input:focus {
        outline: none;
        border-color: #5eb542;
        box-shadow: 0 0 0 3px rgba(94, 181, 66, 0.15);
    }
    .form-input::placeholder {
        color: #94a3b8;
    }
    textarea.form-input {
        resize: vertical;
        min-height: 80px;
    }
    .prescription-block {
        margin-top: 1rem;
        background: #fafbff;
        border-radius: 1rem;
        padding: 1.5rem;
        border: 1px solid #e8ecf4;
    }
    .prescription-toggle {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        background: #f8fafc;
        border-radius: 0.75rem;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.2s;
        margin-bottom: 1.5rem;
    }
    .prescription-toggle:hover {
        background: #f0fdf4;
        border-color: #5eb542;
    }
    .prescription-toggle input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: #5eb542;
    }
    .prescription-toggle label {
        font-weight: 600;
        color: #1e293b;
        cursor: pointer;
        font-size: 0.95rem;
    }
    .prescription-toggle .toggle-desc {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 400;
    }

    .herb-row {
        display: grid;
        grid-template-columns: 2fr 1fr 0.8fr 1.5fr auto;
        gap: 0.75rem;
        align-items: end;
        margin-bottom: 0.75rem;
        padding: 0.75rem;
        background: #fff;
        border-radius: 0.75rem;
        border: 1px solid #f1f5f9;
    }
    .herb-row .form-input {
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
    }
    .herb-row .form-label {
        font-size: 0.75rem;
    }
    .btn-remove-row {
        width: 32px;
        height: 32px;
        border-radius: 0.5rem;
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #ef4444;
        font-size: 1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .btn-remove-row:hover {
        background: #ef4444;
        color: #fff;
    }
    .btn-add-row {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: #f0fdf4;
        color: #16a34a;
        border: 1px dashed #86efac;
        border-radius: 0.75rem;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-add-row:hover {
        background: #dcfce7;
    }
    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        justify-content: flex-end;
        border-top: 1px solid #f1f5f9;
        padding-top: 1.5rem;
    }
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }
    .btn-cancel {
        background: #fff;
        color: #64748b;
        border: 1px solid #cbd5e1;
    }
    .btn-cancel:hover {
        background: #f8fafc;
    }
    .btn-save {
        background: #5eb542;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(94, 181, 66, 0.2);
    }
    .btn-save:hover {
        background: #4da036;
    }
    .error-box {
        background: #fef2f2;
        color: #ef4444;
        padding: 1rem;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }
    .error-box ul {
        margin: 0;
        padding-left: 1.5rem;
    }
    .patient-info-bar {
        background: #f8fafc;
        border-radius: 0.75rem;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e2e8f0;
    }
    .patient-info-bar .avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #e0f2fe;
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .patient-info-bar .info strong {
        color: #0f172a;
        font-size: 1rem;
    }
    .patient-info-bar .info span {
        color: #64748b;
        font-size: 0.85rem;
    }
</style>

<div class="legacy-record-container">

    <div class="legacy-notice">
        <div class="legacy-notice-icon">📋</div>
        <div class="legacy-notice-text">
            <h4>Nhập bệnh án từ hồ sơ giấy</h4>
            <p>Form này dùng để ghi nhận lại lịch sử khám bệnh từ sổ sách cũ. Dữ liệu sẽ được đánh dấu là "bệnh án cũ". Đơn thuốc nhập kèm sẽ <strong>không ảnh hưởng</strong> đến tồn kho hiện tại.</p>
        </div>
    </div>

    <div class="patient-info-bar">
        <div class="avatar">
            @if($patient->gender == 'female') 👩 @elseif($patient->gender == 'male') 👨 @else 👤 @endif
        </div>
        <div class="info">
            <strong>{{ $patient->full_name }}</strong><br>
            <span>{{ $patient->patient_code }} · {{ $patient->age ? $patient->age . ' tuổi' : '' }} · {{ $patient->gender_label }}</span>
        </div>
    </div>

    <div class="form-card">
        @if ($errors->any())
            <div class="error-box">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="error-box">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.medical-records.legacy-store') }}" method="POST" id="legacyRecordForm">
            @csrf
            <input type="hidden" name="patient_id" value="{{ $patient->id }}">

            {{-- Phần bệnh án --}}
            <div class="form-section-title">
                <div class="icon-box" style="background: #eff6ff; color: #3b82f6;">🩺</div>
                <h3>Thông tin bệnh án cũ</h3>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="visit_date" class="form-label">Ngày khám cũ <span style="color: #ef4444;">*</span></label>
                    <input type="date" name="visit_date" id="visit_date" class="form-input" value="{{ old('visit_date') }}" required>
                </div>
                <div class="form-group">
                    <label for="legacy_note" class="form-label">Ghi chú hồ sơ giấy <span style="color: #94a3b8; font-weight: 400;">(VD: trang sổ, số thứ tự...)</span></label>
                    <input type="text" name="legacy_note" id="legacy_note" class="form-input" value="{{ old('legacy_note') }}" placeholder="VD: Sổ khám 2024, trang 15">
                </div>
            </div>

            <div class="form-group full-width" style="margin-top: 1rem;">
                <label for="symptoms" class="form-label">Tình trạng bệnh / Lý do khám <span style="color: #ef4444;">*</span></label>
                <textarea name="symptoms" id="symptoms" class="form-input" rows="3" placeholder="Mô tả triệu chứng, lý do đến khám..." required>{{ old('symptoms') }}</textarea>
            </div>

            <div class="form-group full-width" style="margin-top: 1rem;">
                <label for="diagnosis" class="form-label">Chẩn đoán <span style="color: #ef4444;">*</span></label>
                <textarea name="diagnosis" id="diagnosis" class="form-input" rows="3" placeholder="Chẩn đoán bệnh..." required>{{ old('diagnosis') }}</textarea>
            </div>

            <div class="form-grid" style="margin-top: 1rem;">
                <div class="form-group">
                    <label for="treatment_plan" class="form-label">Hướng điều trị</label>
                    <textarea name="treatment_plan" id="treatment_plan" class="form-input" rows="2" placeholder="Phương pháp điều trị...">{{ old('treatment_plan') }}</textarea>
                </div>
                <div class="form-group">
                    <label for="doctor_note" class="form-label">Ghi chú bác sĩ</label>
                    <textarea name="doctor_note" id="doctor_note" class="form-input" rows="2" placeholder="Ghi chú thêm...">{{ old('doctor_note') }}</textarea>
                </div>
            </div>

            {{-- Phần đơn thuốc cũ --}}
            <div style="margin-top: 2rem;">
                <div class="prescription-toggle" onclick="document.getElementById('has_prescription').click()">
                    <input type="checkbox" id="has_prescription" name="has_prescription" value="1" {{ old('has_prescription') ? 'checked' : '' }} onclick="event.stopPropagation()">
                    <div>
                        <label for="has_prescription">💊 Có nhập đơn thuốc cũ kèm theo</label>
                        <div class="toggle-desc">Nếu hồ sơ giấy có ghi đơn thuốc, tích chọn để nhập lại. Đơn thuốc cũ sẽ không trừ tồn kho.</div>
                    </div>
                </div>

                <div id="prescriptionBlock" class="prescription-block" style="display: {{ old('has_prescription') ? 'block' : 'none' }};">
                    <div class="form-section-title" style="border-color: #e8ecf4;">
                        <div class="icon-box" style="background: #faf5ff; color: #7c3aed;">💊</div>
                        <h3>Đơn thuốc cũ <span style="font-weight: 400; color: #94a3b8; font-size: 0.85rem; margin-left: 0.5rem;">(không ảnh hưởng tồn kho)</span></h3>
                    </div>

                    <div class="form-grid" style="margin-bottom: 1.5rem;">
                        <div class="form-group">
                            <label for="prescription_note" class="form-label">Cách dùng / Lời dặn</label>
                            <textarea name="prescription_note" id="prescription_note" class="form-input" rows="2" placeholder="VD: Sắc 1 thang/ngày, uống 2 lần sáng chiều...">{{ old('prescription_note') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="prescription_legacy_note" class="form-label">Ghi chú đơn thuốc</label>
                            <textarea name="prescription_legacy_note" id="prescription_legacy_note" class="form-input" rows="2" placeholder="VD: Ghi trên sổ 2 thang...">{{ old('prescription_legacy_note') }}</textarea>
                        </div>
                    </div>

                    <label class="form-label" style="margin-bottom: 0.75rem; display: block;">Danh sách dược liệu</label>
                    <div id="herbRows">
                        <div class="herb-row">
                            <div class="form-group">
                                <label class="form-label">Tên dược liệu</label>
                                <select name="items[0][herb_id]" class="form-input">
                                    <option value="">-- Chọn dược liệu --</option>
                                    @foreach($herbs as $herb)
                                        <option value="{{ $herb->id }}">{{ $herb->name }} ({{ $herb->unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Số lượng</label>
                                <input type="number" name="items[0][quantity]" class="form-input" step="0.01" placeholder="VD: 10">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Đơn vị</label>
                                <input type="text" name="items[0][unit]" class="form-input" value="g" placeholder="g">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Liều dùng</label>
                                <input type="text" name="items[0][dosage]" class="form-input" placeholder="VD: 1 thang/ngày">
                            </div>
                            <button type="button" class="btn-remove-row" onclick="this.closest('.herb-row').remove()" title="Xóa dòng">✕</button>
                        </div>
                    </div>

                    <button type="button" class="btn-add-row" onclick="addHerbRow()">
                        + Thêm dược liệu
                    </button>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-cancel">Hủy bỏ</a>
                <button type="submit" class="btn btn-save">
                    <span>💾</span> Lưu bệnh án cũ
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Toggle prescription block
    document.getElementById('has_prescription').addEventListener('change', function() {
        document.getElementById('prescriptionBlock').style.display = this.checked ? 'block' : 'none';
    });

    // Add herb row
    let herbIndex = 1;
    function addHerbRow() {
        const container = document.getElementById('herbRows');
        const row = document.createElement('div');
        row.className = 'herb-row';
        row.innerHTML = `
            <div class="form-group">
                <select name="items[${herbIndex}][herb_id]" class="form-input">
                    <option value="">-- Chọn dược liệu --</option>
                    @foreach($herbs as $herb)
                        <option value="{{ $herb->id }}">{{ $herb->name }} ({{ $herb->unit }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <input type="number" name="items[${herbIndex}][quantity]" class="form-input" step="0.01" placeholder="VD: 10">
            </div>
            <div class="form-group">
                <input type="text" name="items[${herbIndex}][unit]" class="form-input" value="g" placeholder="g">
            </div>
            <div class="form-group">
                <input type="text" name="items[${herbIndex}][dosage]" class="form-input" placeholder="VD: 1 thang/ngày">
            </div>
            <button type="button" class="btn-remove-row" onclick="this.closest('.herb-row').remove()" title="Xóa dòng">✕</button>
        `;
        container.appendChild(row);
        herbIndex++;
    }
</script>
@endsection
