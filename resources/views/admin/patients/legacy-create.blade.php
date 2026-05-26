@extends('layouts.admin')

@section('title', 'Nhập hồ sơ giấy cũ — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div class="icon-bg" style="width: 44px; height: 44px; background: #eff8ee; color: #5eb542; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
        <span class="icon">📋</span>
    </div>
    <div>
        <h1 style="font-size: 1.4rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Nhập dữ liệu từ hồ sơ giấy</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Chuyển đổi thông tin bệnh nhân từ sổ sách cũ sang hệ thống điện tử</p>
    </div>
</div>
@endsection

@section('header-right')
<a href="{{ route('admin.patients.index') }}" style="color: #64748b; text-decoration: none; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; background: #fff; padding: 0.6rem 1.2rem; border-radius: 4px; border: 1px solid #cbd5e1; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#94a3b8'" onmouseout="this.style.background='#fff'; this.style.borderColor='#cbd5e1'">
    ⬅️ <span>Quay lại</span>
</a>
@endsection

@section('content')
<style>
    .legacy-container {
        font-family: 'Inter', system-ui, sans-serif;
        width: 100%;
        margin-top: -1rem;
    }

    /* Tab Switcher */
    .import-mode-switcher {
        display: flex;
        gap: 0.25rem;
        margin-bottom: 1.5rem;
        background: #e2e8f0;
        padding: 0.25rem;
        border-radius: 4px;
        max-width: 600px;
        border: 1px solid #cbd5e1;
    }
    .switcher-btn {
        flex: 1;
        padding: 0.6rem 1rem;
        border-radius: 2px;
        border: none;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.15s;
        background: transparent;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
    }
    .switcher-btn.active {
        background: #ffffff;
        color: #1e3a5f;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    /* Cards */
    .form-card {
        background: #ffffff;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        padding: 1.75rem;
        border: 1px solid #cbd5e1;
        margin-bottom: 1.5rem;
    }
    .legacy-notice {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }
    .legacy-notice-icon {
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .legacy-notice-text h4 {
        margin: 0 0 0.25rem 0;
        font-size: 0.9rem;
        font-weight: 700;
        color: #1e293b;
    }
    .legacy-notice-text p {
        margin: 0;
        font-size: 0.82rem;
        color: #475569;
        line-height: 1.5;
    }

    .form-section-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.25rem;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 0.75rem;
    }
    .form-section-title h3 {
        margin: 0;
        font-size: 1rem;
        font-weight: 800;
        color: #1e3a5f;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
    }
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }
    .form-group.full-width {
        grid-column: span 2;
    }
    .form-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .form-input {
        padding: 0.5rem 0.75rem;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        font-size: 0.85rem;
        color: #1e293b;
        background: #fff;
        transition: border-color 0.15s, box-shadow 0.15s;
        box-sizing: border-box;
        width: 100%;
    }
    .form-input:focus {
        outline: none;
        border-color: #5eb542;
        box-shadow: 0 0 0 2px rgba(94, 181, 66, 0.15);
    }
    .form-input::placeholder {
        color: #94a3b8;
    }
    textarea.form-input {
        resize: vertical;
        min-height: 70px;
    }

    .guardian-block {
        margin-top: 1.5rem;
        background: #f8fafc;
        border-radius: 4px;
        padding: 1.25rem;
        border: 1px solid #cbd5e1;
    }

    .form-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
        justify-content: flex-end;
        border-top: 1px solid #e2e8f0;
        padding-top: 1.25rem;
    }
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.6rem 1.25rem;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.15s;
    }
    .btn-cancel {
        background: #fff;
        color: #475569;
        border: 1px solid #cbd5e1;
    }
    .btn-cancel:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }
    .btn-save {
        background: #5eb542;
        color: #ffffff;
        border: 1px solid #4da036;
    }
    .btn-save:hover {
        background: #4da036;
    }

    /* CSV Import Styles */
    .steps-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .step-card {
        background: #ffffff;
        border-radius: 4px;
        padding: 1.25rem;
        border: 1px solid #cbd5e1;
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
    }
    .step-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #5eb542;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        flex-shrink: 0;
    }
    .step-content h4 {
        margin: 0 0 0.25rem 0;
        font-size: 0.88rem;
        font-weight: 700;
        color: #1e293b;
    }
    .step-content p {
        margin: 0;
        font-size: 0.8rem;
        color: #64748b;
        line-height: 1.4;
    }

    .upload-area {
        border: 2px dashed #cbd5e1;
        border-radius: 4px;
        padding: 2.5rem 1.5rem;
        text-align: center;
        transition: all 0.2s;
        cursor: pointer;
        position: relative;
        background: #f8fafc;
    }
    .upload-area:hover,
    .upload-area.dragover {
        border-color: #5eb542;
        background: #f0fdf4;
    }
    .upload-area .upload-icon {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
    }
    .upload-area h4 {
        margin: 0 0 0.25rem 0;
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
    }
    .upload-area p {
        margin: 0;
        font-size: 0.8rem;
        color: #64748b;
    }
    .upload-area input[type="file"] {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .file-info {
        display: none;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1.25rem;
        background: #f0fdf4;
        border-radius: 4px;
        margin-top: 1rem;
        border: 1px solid #bbf7d0;
    }
    .file-info.show {
        display: flex;
    }
    .file-info .file-icon {
        font-size: 1.25rem;
    }
    .file-info .file-name {
        font-weight: 700;
        color: #16a34a;
        font-size: 0.85rem;
    }
    .file-info .file-size {
        font-size: 0.75rem;
        color: #64748b;
    }
    .file-info .file-remove {
        margin-left: auto;
        color: #ef4444;
        cursor: pointer;
        font-size: 1.1rem;
        font-weight: bold;
    }

    .format-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8rem;
        margin-top: 0.5rem;
    }
    .format-table th {
        background: #f1f5f9;
        padding: 0.5rem 0.75rem;
        text-align: left;
        font-weight: 700;
        color: #475569;
        border: 1px solid #cbd5e1;
        text-transform: uppercase;
        font-size: 0.75rem;
    }
    .format-table td {
        padding: 0.5rem 0.75rem;
        border: 1px solid #cbd5e1;
        color: #334155;
    }
    .format-table tr:nth-child(even) td {
        background-color: #f8fafc;
    }
    .format-table code {
        background: #f1f5f9;
        padding: 0.15rem 0.3rem;
        border-radius: 2px;
        font-size: 0.75rem;
        color: #0f172a;
        border: 1px solid #cbd5e1;
        font-family: monospace;
    }
    .col-required {
        color: #ef4444;
        font-weight: 700;
    }
    .col-optional {
        color: #64748b;
    }

    .btn-template {
        background: #fff;
        color: #5eb542;
        border: 1px solid #5eb542;
    }
    .btn-template:hover {
        background: #f0fdf4;
    }
    .btn-upload {
        background: #5eb542;
        color: #fff;
        border: 1px solid #4da036;
    }
    .btn-upload:hover {
        background: #4da036;
    }
    .btn-upload:disabled {
        background: #94a3b8;
        border-color: #cbd5e1;
        cursor: not-allowed;
    }

    .error-box {
        background: #fef2f2;
        color: #ef4444;
        padding: 0.75rem 1rem;
        border-radius: 4px;
        margin-bottom: 1.25rem;
        font-size: 0.85rem;
        border: 1px solid #fca5a5;
    }
    .error-box ul {
        margin: 0;
        padding-left: 1.25rem;
    }

    .duplicate-warning-box {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 4px;
        padding: 1rem;
        margin-bottom: 1.25rem;
    }
    .duplicate-warning-box h4 {
        margin: 0 0 0.5rem 0;
        color: #92400e;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .duplicate-warning-box p {
        margin: 0 0 0.5rem 0;
        color: #a16207;
        font-size: 0.82rem;
    }
    .duplicate-list {
        list-style: none;
        padding: 0;
        margin: 0 0 1rem 0;
    }
    .duplicate-list li {
        padding: 0.4rem 0.6rem;
        background: #fff;
        border-radius: 2px;
        font-size: 0.82rem;
        color: #1e293b;
        margin-bottom: 0.25rem;
        border: 1px solid #fde68a;
    }
    .duplicate-actions {
        display: flex;
        gap: 0.5rem;
    }
    .btn-force-save {
        background: #f59e0b;
        color: #fff;
        padding: 0.4rem 0.8rem;
        border-radius: 2px;
        border: none;
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
    }
    .btn-force-save:hover {
        background: #d97706;
    }

    /* AJAX Warning Inline */
    .ajax-warning {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 2px;
        padding: 0.5rem 0.75rem;
        margin-top: 0.4rem;
        font-size: 0.78rem;
        color: #92400e;
        display: none;
    }
    .ajax-warning.show {
        display: block;
    }
</style>

@php
    $activeMode = (session('error') || session('import_errors') || session('import_warnings') || $errors->has('csv_file')) ? 'file' : 'manual';
@endphp

<div class="legacy-container">

    {{-- Switcher Mode --}}
    <div class="import-mode-switcher">
        <button type="button" class="switcher-btn {{ $activeMode === 'manual' ? 'active' : '' }}" data-mode="manual">
            ✍️ Nhập thủ công bằng tay
        </button>
        <button type="button" class="switcher-btn {{ $activeMode === 'file' ? 'active' : '' }}" data-mode="file">
            📥 Nhập bằng file danh sách (Excel/CSV)
        </button>
    </div>

    {{-- PHÂN HỆ 1: NHẬP THỦ CÔNG --}}
    <div id="manual-mode-container" style="display: {{ $activeMode === 'manual' ? 'block' : 'none' }};">
        <div class="legacy-notice">
            <span class="legacy-notice-icon">📋</span>
            <div class="legacy-notice-text">
                <h4>Dữ liệu nhập lại từ hồ sơ giấy cũ</h4>
                <p>Form này dùng để nhập tay thông tin bệnh nhân từ sổ sách hoặc bệnh án giấy cũ. Các trường không bắt buộc phải điền đầy đủ nếu hồ sơ cũ thiếu thông tin. Hệ thống tự động đánh dấu cờ dữ liệu chuyển đổi.</p>
            </div>
        </div>

        <div class="form-card">
            @if ($errors->any() && $activeMode === 'manual')
                <div class="error-box">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('duplicate_warning'))
                <div class="duplicate-warning-box">
                    <h4>⚠️ Phát hiện bệnh nhân trùng lặp!</h4>
                    <p>Hệ thống tìm thấy bệnh nhân có thông tin tương tự trong cơ sở dữ liệu:</p>
                    <ul class="duplicate-list">
                        @foreach (session('duplicates') as $dup)
                            <li>
                                <strong>{{ $dup->patient_code }}</strong> — {{ $dup->full_name }}
                                @if($dup->date_of_birth)
                                    ({{ $dup->date_of_birth->format('d/m/Y') }})
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <p>Bạn có chắc chắn muốn tạo thêm bệnh nhân mới không?</p>
                    <form action="{{ route('admin.patients.legacy-store') }}" method="POST" style="display: inline;">
                        @csrf
                        @foreach (old() as $key => $value)
                            @if ($key !== '_token' && $key !== 'force_save')
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <input type="hidden" name="force_save" value="1">
                        <div class="duplicate-actions">
                            <button type="submit" class="btn-force-save">✓ Vẫn lưu bệnh nhân mới</button>
                            <a href="{{ route('admin.patients.legacy-create') }}" class="btn btn-cancel" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">Nhập lại</a>
                        </div>
                    </form>
                </div>
            @endif

            <form action="{{ route('admin.patients.legacy-store') }}" method="POST" id="legacyForm">
                @csrf

                <div class="form-section-title">
                    <h3>👤 Thông tin cơ bản</h3>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="full_name" class="form-label">Họ và tên <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="full_name" id="full_name" class="form-input" placeholder="VD: Nguyễn Văn An" value="{{ old('full_name') }}" required>
                        <div id="nameWarning" class="ajax-warning"></div>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label" style="display: flex; justify-content: space-between; align-items: center;">
                            <span>Số điện thoại <span style="color: #ef4444;">*</span></span>
                            <label style="display: flex; align-items: center; gap: 0.25rem; font-weight: normal; cursor: pointer; font-size: 0.78rem; color: #64748b; text-transform: none; letter-spacing: 0;">
                                <input type="checkbox" id="is_guardian_phone" name="is_guardian_phone" value="1" {{ old('is_guardian_phone') ? 'checked' : '' }}>
                                SĐT người giám hộ
                            </label>
                        </label>
                        <input type="tel" name="{{ old('is_guardian_phone') ? 'guardian_phone' : 'phone' }}" id="phone" class="form-input" placeholder="VD: 0912345678" value="{{ old('phone') ?? old('guardian_phone') }}">
                        <div id="phoneWarning" class="ajax-warning"></div>
                    </div>

                    <div class="form-group">
                        <label for="date_of_birth" class="form-label">Ngày sinh <span style="color: #94a3b8; font-weight: 400;">(nếu có)</span></label>
                        <input type="date" name="date_of_birth" id="date_of_birth" class="form-input" value="{{ old('date_of_birth') }}">
                    </div>

                    <div class="form-group">
                        <label for="gender" class="form-label">Giới tính</label>
                        <select name="gender" id="gender" class="form-input" style="appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2364748b%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 0.6rem auto; padding-right: 2rem;">
                            <option value="">-- Chọn giới tính --</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Khác</option>
                        </select>
                    </div>
                </div>

                <div class="form-group full-width" style="margin-top: 1rem;">
                    <label for="address" class="form-label">Địa chỉ <span style="color: #94a3b8; font-weight: 400;">(nếu có)</span></label>
                    <input type="text" name="address" id="address" class="form-input" placeholder="Số nhà, đường, phường/xã, quận/huyện..." value="{{ old('address') }}">
                </div>

                {{-- Guardian Block --}}
                <div id="guardian_block" class="guardian-block" style="display: {{ old('is_guardian_phone') ? 'block' : 'none' }};">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                        <span style="font-size: 1.1rem;">🛡️</span>
                        <h3 style="margin: 0; font-size: 0.9rem; color: #1e3a5f; font-weight: 700;">Thông tin người giám hộ <span style="font-weight: 400; color: #94a3b8; font-size: 0.8rem; margin-left: 0.25rem;">(Trẻ em/Người già)</span></h3>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="guardian_name" class="form-label">Họ tên <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="guardian_name" id="guardian_name" class="form-input" value="{{ old('guardian_name') }}">
                        </div>
                        <div class="form-group">
                            <label for="relationship" class="form-label">Quan hệ <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="relationship" id="relationship" class="form-input" value="{{ old('relationship') }}" placeholder="VD: Bố, Mẹ...">
                        </div>
                    </div>
                </div>

                {{-- Legacy Note --}}
                <div style="margin-top: 1.5rem;">
                    <div class="form-section-title">
                        <h3>📝 Ghi chú dữ liệu cũ</h3>
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label for="note" class="form-label">Ghi chú y tế / Tiền sử</label>
                        <textarea name="note" id="note" class="form-input" rows="3" placeholder="Nhập các lưu ý quan trọng về bệnh nhân (nếu có)...">{{ old('note') }}</textarea>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="legacy_date" class="form-label">Ngày ghi hồ sơ giấy gốc <span style="color: #94a3b8; font-weight: 400;">(nếu có)</span></label>
                            <input type="date" name="legacy_date" id="legacy_date" class="form-input" value="{{ old('legacy_date') }}">
                        </div>

                        <div class="form-group">
                            <label for="legacy_note" class="form-label">Ghi chú hồ sơ giấy <span style="color: #94a3b8; font-weight: 400;">(số sổ cũ, thứ tự...)</span></label>
                            <input type="text" name="legacy_note" id="legacy_note" class="form-input" placeholder="VD: Sổ khám 2023, số thứ tự 45..." value="{{ old('legacy_note') }}">
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.patients.index') }}" class="btn btn-cancel">Hủy bỏ</a>
                    <button type="submit" class="btn btn-save">
                        <span>💾</span> Lưu bệnh nhân
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- PHÂN HỆ 2: UPLOAD FILE ĐỒNG LOẠT --}}
    <div id="file-mode-container" style="display: {{ $activeMode === 'file' ? 'block' : 'none' }};">
        @if((session('error') || session('import_errors')) && $activeMode === 'file')
            <div class="error-box">
                @if(session('error'))
                    {{ session('error') }}
                @endif
                @if(session('import_errors'))
                    <ul style="margin-top: 0.5rem;">
                        @foreach(session('import_errors') as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        {{-- Steps Guide --}}
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h4>Tải file mẫu</h4>
                    <p>Tải file Excel mẫu về để biết đúng định dạng các cột cần nhập.</p>
                </div>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h4>Điền dữ liệu</h4>
                    <p>Mở file Excel điền danh sách bệnh nhân và thông tin theo hàng dọc.</p>
                </div>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h4>Tải lên hệ thống</h4>
                    <p>Thả file vào uploader để hệ thống tự phân tích và thêm bệnh nhân tự động.</p>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start;">
            {{-- Upload Area --}}
            <div class="form-card">
                <div class="form-section-title">
                    <h3>📤 Upload file danh sách</h3>
                </div>

                <form action="{{ route('admin.patients.csv-import-process') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    <div class="upload-area" id="uploadArea">
                        <input type="file" name="csv_file" id="csvFile" accept=".csv,.txt,.xlsx,.xls">
                        <div class="upload-icon">📄</div>
                        <h4>Kéo thả file Excel hoặc CSV vào đây</h4>
                        <p>hoặc click vào đây để duyệt file máy tính (tối đa 5MB)</p>
                    </div>

                    <div class="file-info" id="fileInfo">
                        <span class="file-icon">📄</span>
                        <div>
                            <div class="file-name" id="fileName"></div>
                            <div class="file-size" id="fileSize"></div>
                        </div>
                        <span class="file-remove" id="fileRemove" title="Gỡ bỏ file">✕</span>
                    </div>

                    <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem; justify-content: space-between; align-items: center;">
                        <a href="{{ asset('templates/mau_import_benh_nhan.xlsx') }}" class="btn btn-template" download>
                            📥 Tải file Excel mẫu
                        </a>
                        <button type="submit" class="btn btn-upload" id="submitBtn" disabled>
                            🚀 Bắt đầu Import
                        </button>
                    </div>
                </form>
            </div>

            {{-- Formatting Rules --}}
            <div class="form-card">
                <div class="form-section-title">
                    <h3>📋 Định dạng dữ liệu mẫu</h3>
                </div>
                <p style="font-size: 0.8rem; color: #64748b; margin: 0 0 1rem 0;">Bảng tính Excel/CSV cần khớp chuẩn xác tiêu đề dòng đầu tiên:</p>
                
                <table class="format-table">
                    <thead>
                        <tr>
                            <th>Tên cột</th>
                            <th>Mô tả chi tiết</th>
                            <th>Yêu cầu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>full_name</code></td>
                            <td>Họ và tên bệnh nhân</td>
                            <td class="col-required">Có</td>
                        </tr>
                        <tr>
                            <td><code>date_of_birth</code></td>
                            <td>Ngày sinh dạng YYYY-MM-DD</td>
                            <td class="col-optional">Không</td>
                        </tr>
                        <tr>
                            <td><code>gender</code></td>
                            <td>Nam / Nữ / Khác</td>
                            <td class="col-optional">Không</td>
                        </tr>
                        <tr>
                            <td><code>phone</code></td>
                            <td>Số điện thoại bệnh nhân</td>
                            <td class="col-required">Có *</td>
                        </tr>
                        <tr>
                            <td><code>address</code></td>
                            <td>Địa chỉ nơi ở hiện tại</td>
                            <td class="col-optional">Không</td>
                        </tr>
                        <tr>
                            <td><code>guardian_name</code></td>
                            <td>Họ tên người giám hộ</td>
                            <td class="col-optional">Không</td>
                        </tr>
                        <tr>
                            <td><code>guardian_phone</code></td>
                            <td>SĐT người giám hộ</td>
                            <td class="col-required">Có *</td>
                        </tr>
                        <tr>
                            <td><code>relationship</code></td>
                            <td>Mối quan hệ</td>
                            <td class="col-optional">Không</td>
                        </tr>
                        <tr>
                            <td><code>legacy_note</code></td>
                            <td>Ghi chú sổ cũ</td>
                            <td class="col-optional">Không</td>
                        </tr>
                    </tbody>
                </table>
                <p style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.75rem; line-height: 1.4;">* Cần cung cấp ít nhất 1 trong 2 số điện thoại: <code>phone</code> hoặc <code>guardian_phone</code> để hoàn tất xác minh liên hệ.</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Xử lý chuyển đổi Tab (Chế độ nhập tay / file)
    const switcherBtns = document.querySelectorAll('.switcher-btn');
    const manualContainer = document.getElementById('manual-mode-container');
    const fileContainer = document.getElementById('file-mode-container');

    switcherBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            switcherBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const mode = this.getAttribute('data-mode');
            if (mode === 'manual') {
                manualContainer.style.display = 'block';
                fileContainer.style.display = 'none';
            } else {
                manualContainer.style.display = 'none';
                fileContainer.style.display = 'block';
            }
        });
    });

    // --- Xử lý form Nhập tay (Guardian phone toggle) ---
    const isGuardianPhoneCheckbox = document.getElementById('is_guardian_phone');
    if (isGuardianPhoneCheckbox) {
        isGuardianPhoneCheckbox.addEventListener('change', function() {
            const phoneInput = document.getElementById('phone');
            const guardianBlock = document.getElementById('guardian_block');
            
            if (this.checked) {
                phoneInput.name = 'guardian_phone';
                guardianBlock.style.display = 'block';
            } else {
                phoneInput.name = 'phone';
                guardianBlock.style.display = 'none';
            }
        });
    }

    // AJAX cảnh báo trùng
    let debounceTimer;
    function checkDuplicate() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(async () => {
            const phone = document.getElementById('phone').value.trim();
            const fullName = document.getElementById('full_name').value.trim();
            const dob = document.getElementById('date_of_birth').value;
            const gender = document.getElementById('gender').value;

            if (!phone && !fullName) return;

            try {
                const res = await fetch('{{ route("admin.patients.check-duplicate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ phone, full_name: fullName, date_of_birth: dob, gender }),
                });
                const data = await res.json();

                // Phone warnings
                const phoneWarning = document.getElementById('phoneWarning');
                const phoneW = data.warnings.find(w => w.type === 'phone');
                if (phoneW) {
                    const names = phoneW.patients.map(p => `${p.code} - ${p.name}`).join(', ');
                    phoneWarning.innerHTML = `⚠️ ${phoneW.message} (${names})`;
                    phoneWarning.classList.add('show');
                } else {
                    phoneWarning.classList.remove('show');
                }

                // Name warnings
                const nameWarning = document.getElementById('nameWarning');
                const nameW = data.warnings.find(w => w.type === 'identity');
                if (nameW) {
                    const names = nameW.patients.map(p => `${p.code} - ${p.name}`).join(', ');
                    nameWarning.innerHTML = `⚠️ ${nameW.message} (${names})`;
                    nameWarning.classList.add('show');
                } else {
                    nameWarning.classList.remove('show');
                }
            } catch (e) {
                // Silently ignore
            }
        }, 500);
    }

    document.getElementById('phone').addEventListener('blur', checkDuplicate);
    document.getElementById('full_name').addEventListener('blur', checkDuplicate);
    document.getElementById('date_of_birth').addEventListener('change', checkDuplicate);
    document.getElementById('gender').addEventListener('change', checkDuplicate);


    // --- Xử lý kéo thả upload File Excel ---
    const csvFile = document.getElementById('csvFile');
    const uploadArea = document.getElementById('uploadArea');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const fileRemove = document.getElementById('fileRemove');
    const submitBtn = document.getElementById('submitBtn');

    csvFile.addEventListener('change', function() {
        if (this.files.length > 0) {
            const file = this.files[0];
            fileName.textContent = file.name;
            fileSize.textContent = (file.size / 1024).toFixed(1) + ' KB';
            fileInfo.classList.add('show');
            uploadArea.style.display = 'none';
            submitBtn.disabled = false;
        }
    });

    fileRemove.addEventListener('click', function() {
        csvFile.value = '';
        fileInfo.classList.remove('show');
        uploadArea.style.display = 'block';
        submitBtn.disabled = true;
    });

    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    uploadArea.addEventListener('dragleave', function() {
        this.classList.remove('dragover');
    });
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        if (e.dataTransfer.files.length > 0) {
            csvFile.files = e.dataTransfer.files;
            csvFile.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection
