@extends('layouts.admin')

@section('title', 'Import bệnh nhân từ CSV — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div class="icon-bg" style="width: 44px; height: 44px; background: #e0f2fe; color: #0284c7; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
        <span class="icon">📊</span>
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Import bệnh nhân từ file CSV</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Nhập hàng loạt dữ liệu bệnh nhân từ file Excel/CSV</p>
    </div>
</div>
@endsection

@section('header-right')
<a href="{{ route('admin.patients.index') }}" style="color: #64748b; text-decoration: none; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; background: #fff; padding: 0.6rem 1.2rem; border-radius: 12px; border: 1px solid #f0f3ff; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#cbd5e1'" onmouseout="this.style.background='#fff'; this.style.borderColor='#f0f3ff'">
    ⬅️ <span>Quay lại</span>
</a>
@endsection

@section('content')
<style>
    .csv-import-container {
        font-family: 'Inter', system-ui, sans-serif;
        width: 100%;
        margin-top: -1rem;
    }
    .import-card {
        background: #ffffff;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        padding: 2rem;
        border: 1px solid #f1f5f9;
        margin-bottom: 1.5rem;
    }
    .import-card h3 {
        margin: 0 0 1rem 0;
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .steps-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .step-card {
        background: #f8fafc;
        border-radius: 1rem;
        padding: 1.5rem;
        border: 1px solid #f1f5f9;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }
    .step-number {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #5eb542;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .step-content h4 {
        margin: 0 0 0.25rem 0;
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
    }
    .step-content p {
        margin: 0;
        font-size: 0.85rem;
        color: #64748b;
        line-height: 1.5;
    }

    .upload-area {
        border: 2px dashed #e2e8f0;
        border-radius: 1rem;
        padding: 3rem 2rem;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
        position: relative;
    }
    .upload-area:hover,
    .upload-area.dragover {
        border-color: #5eb542;
        background: #f0fdf4;
    }
    .upload-area .upload-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    .upload-area h4 {
        margin: 0 0 0.5rem 0;
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
    }
    .upload-area p {
        margin: 0;
        font-size: 0.85rem;
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
        gap: 1rem;
        padding: 1rem 1.5rem;
        background: #f0fdf4;
        border-radius: 0.75rem;
        margin-top: 1rem;
        border: 1px solid #dcfce7;
    }
    .file-info.show {
        display: flex;
    }
    .file-info .file-icon {
        font-size: 1.5rem;
    }
    .file-info .file-name {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.9rem;
    }
    .file-info .file-size {
        font-size: 0.8rem;
        color: #64748b;
    }
    .file-info .file-remove {
        margin-left: auto;
        color: #ef4444;
        cursor: pointer;
        font-size: 1.2rem;
    }

    .format-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
        margin-top: 1rem;
    }
    .format-table th {
        background: #f8fafc;
        padding: 0.75rem 1rem;
        text-align: left;
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #f1f5f9;
    }
    .format-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
    }
    .format-table code {
        background: #f1f5f9;
        padding: 0.15rem 0.4rem;
        border-radius: 0.25rem;
        font-size: 0.8rem;
        color: #0f172a;
    }
    .col-required {
        color: #ef4444;
        font-weight: 600;
    }
    .col-optional {
        color: #64748b;
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
        box-shadow: 0 4px 12px rgba(94, 181, 66, 0.2);
    }
    .btn-upload:hover {
        background: #4da036;
    }
    .btn-upload:disabled {
        background: #94a3b8;
        cursor: not-allowed;
        box-shadow: none;
    }
    .btn-back {
        background: #fff;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }
    .btn-back:hover {
        background: #f8fafc;
    }

    .error-box {
        background: #fef2f2;
        color: #ef4444;
        padding: 1rem;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }
</style>

<div class="csv-import-container">
    
    @if(session('error'))
        <div class="error-box">{{ session('error') }}</div>
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
                <p>Mở file bằng Excel hoặc Google Sheets, điền thông tin bệnh nhân theo từng dòng.</p>
            </div>
        </div>
        <div class="step-card">
            <div class="step-number">3</div>
            <div class="step-content">
                <h4>Upload & Import</h4>
                <p>Tải file lên hệ thống. Hệ thống sẽ tự động kiểm tra và import dữ liệu.</p>
            </div>
        </div>
    </div>
 
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        {{-- Upload Area --}}
        <div class="import-card">
            <h3>📤 Upload file Excel/CSV</h3>
 
            <form action="{{ route('admin.patients.csv-import-process') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                <div class="upload-area" id="uploadArea">
                    <input type="file" name="csv_file" id="csvFile" accept=".csv,.txt,.xlsx,.xls">
                    <div class="upload-icon">📄</div>
                    <h4>Kéo thả file Excel hoặc CSV vào đây</h4>
                    <p>hoặc nhấp để chọn file (tối đa 5MB)</p>
                </div>
 
                <div class="file-info" id="fileInfo">
                    <span class="file-icon">📄</span>
                    <div>
                        <div class="file-name" id="fileName"></div>
                        <div class="file-size" id="fileSize"></div>
                    </div>
                    <span class="file-remove" id="fileRemove" title="Xóa file">✕</span>
                </div>
 
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; justify-content: space-between; align-items: center;">
                    <a href="{{ asset('templates/mau_import_benh_nhan.xlsx') }}" class="btn btn-template" download>
                        📥 Tải file Excel mẫu
                    </a>
                    <button type="submit" class="btn btn-upload" id="submitBtn" disabled>
                        🚀 Bắt đầu Import
                    </button>
                </div>
            </form>
        </div>
 
        {{-- Format Guide --}}
        <div class="import-card">
            <h3>📋 Định dạng file</h3>
            <p style="font-size: 0.85rem; color: #64748b; margin: 0 0 1rem 0;">File Excel/CSV cần có các cột sau (dòng đầu tiên là header):</p>
            
            <table class="format-table">
                <thead>
                    <tr>
                        <th>Tên cột</th>
                        <th>Mô tả</th>
                        <th>Bắt buộc</th>
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
                        <td>Ngày sinh (YYYY-MM-DD)</td>
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
                        <td>Địa chỉ</td>
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
                        <td>Quan hệ với bệnh nhân</td>
                        <td class="col-optional">Không</td>
                    </tr>
                    <tr>
                        <td><code>legacy_note</code></td>
                        <td>Ghi chú hồ sơ cũ</td>
                        <td class="col-optional">Không</td>
                    </tr>
                </tbody>
            </table>
            <p style="font-size: 0.8rem; color: #94a3b8; margin-top: 0.75rem;">* Bắt buộc có ít nhất một trong hai: <code>phone</code> hoặc <code>guardian_phone</code></p>
        </div>
    </div>

    <div style="margin-top: 1rem;">
        <a href="{{ route('admin.patients.index') }}" class="btn btn-back">
            ← Quay lại danh sách bệnh nhân
        </a>
    </div>
</div>

<script>
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

    // Drag and drop
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
