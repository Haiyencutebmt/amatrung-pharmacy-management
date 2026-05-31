@extends('layouts.admin')

@section('title', 'Tạo Bệnh Án Mới')

@section('content')
<div class="container-fluid" style="padding-bottom: 3rem;">
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('admin.medical-records.index') }}" class="btn btn-secondary btn-lg" style="border-radius: 4px;">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>
    
    <h1 class="h3 mb-4 text-gray-800 font-weight-bold">Tạo Bệnh Án Mới</h1>

    @if($errors->any())
        <div class="alert alert-danger" style="font-size: 18px;">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.medical-records.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- KHỐI 1: THÔNG TIN KHÁM CHUNG --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary">
                <h6 class="m-0 font-weight-bold text-white" style="font-size: 20px;">
                    <i class="fas fa-user-injured"></i> 1. Thông tin Bệnh nhân & Khám bệnh
                </h6>
            </div>
            <div class="card-body" style="font-size: 18px;">
                <div class="row mb-3">
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Bệnh nhân <span class="text-danger">*</span></label>
                        <select name="patient_id" class="form-control form-control-lg" required>
                            <option value="">-- Chọn bệnh nhân --</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ (old('patient_id') == $patient->id || $patient_id == $patient->id) ? 'selected' : '' }}>
                                    {{ $patient->full_name }} ({{ $patient->patient_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="font-weight-bold">Ngày khám <span class="text-danger">*</span></label>
                        <input type="date" name="visit_date" class="form-control form-control-lg" value="{{ old('visit_date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="font-weight-bold">Phân loại ca khám</label>
                        <select name="case_type" id="case_type" class="form-control form-control-lg" onchange="toggleCaseType(this.value)">
                            <option value="normal" {{ old('case_type', 'normal') === 'normal' ? 'selected' : '' }}>Thông thường</option>
                            <option value="musculoskeletal" {{ old('case_type') === 'musculoskeletal' ? 'selected' : '' }}>Xương khớp - Chấn thương</option>
                            <option value="combined" {{ old('case_type') === 'combined' ? 'selected' : '' }}>Kết hợp cả hai</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold text-danger">Ghi chú Dị ứng (Thuốc/Dược liệu)</label>
                        <input type="text" name="allergies" class="form-control form-control-lg" value="{{ old('allergies') }}" placeholder="Ví dụ: Dị ứng phấn hoa, hải sản...">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold text-warning text-dark">Bệnh nền</label>
                        <input type="text" name="underlying_diseases" class="form-control form-control-lg" value="{{ old('underlying_diseases') }}" placeholder="Ví dụ: Huyết áp cao, tiểu đường...">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12 form-group">
                        <label class="font-weight-bold">Thuốc đang sử dụng</label>
                        <input type="text" name="current_medications" class="form-control form-control-lg" value="{{ old('current_medications') }}" placeholder="Ví dụ: Đang uống thuốc tây tiểu đường...">
                    </div>
                </div>
            </div>
        </div>

        {{-- KHỐI 2: KHÁM XƯƠNG KHỚP (ẨN/HIỆN THEO CASE_TYPE) --}}
        <div id="musculoskeletal_fields" class="card shadow mb-4" style="display: none; border-left: 4px solid #e74a3b;">
            <div class="card-header py-3 bg-danger">
                <h6 class="m-0 font-weight-bold text-white" style="font-size: 20px;">
                    <i class="fas fa-bone"></i> 2. Khám Xương Khớp & Chấn thương
                </h6>
            </div>
            <div class="card-body" style="font-size: 18px;">
                <div class="row mb-3">
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Vị trí chấn thương / Vùng bị đau</label>
                        <input type="text" name="injury_location" class="form-control form-control-lg" value="{{ old('injury_location') }}" placeholder="Khớp cổ chân, đốt sống lưng...">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Mức độ đau (0 - 10)</label>
                        <select name="pain_level" class="form-control form-control-lg">
                            <option value="">-- Chọn mức độ đau --</option>
                            <option value="3" {{ old('pain_level') == 3 ? 'selected' : '' }}>Nhẹ (1-3)</option>
                            <option value="5" {{ old('pain_level') == 5 ? 'selected' : '' }}>Trung bình (4-6)</option>
                            <option value="8" {{ old('pain_level') == 8 ? 'selected' : '' }}>Nặng (7-10)</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Dấu hiệu lâm sàng / Kết quả sờ nắn</label>
                    <textarea name="clinical_signs" class="form-control form-control-lg" rows="2" placeholder="Sưng nề, ấn đau chói..."></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold"><i class="fas fa-file-medical-alt"></i> Đính kèm Phim X-Quang/Ảnh (Bảo mật)</label>
                        <input type="file" name="xray_image" class="form-control-file" accept=".jpg,.jpeg,.png,.pdf" style="font-size: 18px;">
                        <small class="form-text text-muted">Hỗ trợ JPG, PNG, PDF. Tối đa 5MB. File được lưu bảo mật (không public).</small>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Ghi chú phim ảnh</label>
                        <input type="text" name="xray_note" class="form-control form-control-lg" value="{{ old('xray_note') }}" placeholder="Kết luận từ phim X-Quang...">
                    </div>
                </div>
            </div>
        </div>

        {{-- KHỐI 3: CHẨN ĐOÁN & HƯỚNG ĐIỀU TRỊ --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-success">
                <h6 class="m-0 font-weight-bold text-white" style="font-size: 20px;">
                    <i class="fas fa-stethoscope"></i> 3. Chẩn đoán & Hướng Điều Trị
                </h6>
            </div>
            <div class="card-body" style="font-size: 18px;">
                <div class="form-group" id="symptoms_group">
                    <label class="font-weight-bold">Triệu chứng chính / Lý do khám <span class="text-danger">*</span></label>
                    <textarea name="symptoms" id="symptoms" class="form-control form-control-lg" rows="2" required placeholder="Ghi nhận cụ thể triệu chứng chủ quan của bệnh nhân...">{{ old('symptoms') }}</textarea>
                </div>

                <div class="form-group" id="diagnosis_group">
                    <label class="font-weight-bold">Chẩn đoán kết luận <span class="text-muted" style="font-size:0.85rem;">(có thể bổ sung sau)</span></label>
                    <textarea name="diagnosis" id="diagnosis" class="form-control form-control-lg" rows="2" placeholder="Có thể để trống để AI hỗ trợ nhận định sơ bộ ở trang chi tiết...">{{ old('diagnosis') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold" style="font-size: 20px;">Định hướng Cấp Thuốc / Điều Trị <span class="text-danger">*</span></label>
                    <div class="p-3 border rounded" style="background: #f8f9fc;">
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="dir_oral" name="treatment_direction" value="oral_only" class="custom-control-input" style="width:20px;height:20px;" {{ old('treatment_direction', 'oral_only') === 'oral_only' ? 'checked' : '' }} onchange="toggleReferral(this.value)">
                            <label class="custom-control-label font-weight-bold text-primary ml-2" for="dir_oral" style="font-size: 18px;">Chỉ Thuốc Uống (Kê đơn sắc uống)</label>
                        </div>
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="dir_ext" name="treatment_direction" value="external_only" class="custom-control-input" style="width:20px;height:20px;" {{ old('treatment_direction') === 'external_only' ? 'checked' : '' }} onchange="toggleReferral(this.value)">
                            <label class="custom-control-label font-weight-bold text-danger ml-2" for="dir_ext" style="font-size: 18px;">Chỉ Dùng Ngoài (Bó thuốc, Rượu xoa bóp)</label>
                        </div>
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="dir_combined" name="treatment_direction" value="combined" class="custom-control-input" style="width:20px;height:20px;" {{ old('treatment_direction') === 'combined' ? 'checked' : '' }} onchange="toggleReferral(this.value)">
                            <label class="custom-control-label font-weight-bold text-success ml-2" for="dir_combined" style="font-size: 18px;">Kết hợp Uống & Dùng Ngoài</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="dir_ref" name="treatment_direction" value="referral" class="custom-control-input" style="width:20px;height:20px;" {{ old('treatment_direction') === 'referral' ? 'checked' : '' }} onchange="toggleReferral(this.value)">
                            <label class="custom-control-label font-weight-bold text-warning ml-2" style="font-size: 18px; color: #856404 !important;" for="dir_ref">Khuyến nghị chuyển đến cơ sở y tế phù hợp (Không kê đơn)</label>
                        </div>
                    </div>
                </div>

                <div class="form-group" id="referral_reason_group" style="display: none;">
                    <label class="font-weight-bold text-warning" style="color: #856404 !important;">Lý do khuyến nghị chuyển cơ sở y tế <span class="text-danger">*</span></label>
                    <textarea name="referral_reason" id="referral_reason" class="form-control form-control-lg border-warning" rows="2" placeholder="Ví dụ: Vượt quá khả năng chuyên môn, Cần can thiệp phẫu thuật tây y..."></textarea>
                </div>
                
                <div class="form-group">
                    <label class="font-weight-bold">Ghi chú điều trị / Lời dặn dò</label>
                    <textarea name="doctor_note" class="form-control form-control-lg" rows="2" placeholder="Dặn dò kiêng khem, ăn uống...">{{ old('doctor_note') }}</textarea>
                </div>
            </div>
        </div>

        <div class="text-right">
            <button type="submit" class="btn btn-primary btn-lg font-weight-bold px-5" style="font-size: 20px;">
                <i class="fas fa-save"></i> LƯU BỆNH ÁN
            </button>
        </div>
    </form>
</div>

<script>
    function toggleCaseType(value) {
        const musculoskeletalBox = document.getElementById('musculoskeletal_fields');
        if (value === 'musculoskeletal' || value === 'combined') {
            musculoskeletalBox.style.display = 'block';
        } else {
            musculoskeletalBox.style.display = 'none';
        }
    }

    function toggleReferral(value) {
        const referralGroup = document.getElementById('referral_reason_group');
        const referralInput = document.getElementById('referral_reason');
        
        if (value === 'referral') {
            referralGroup.style.display = 'block';
            referralInput.setAttribute('required', 'required');
        } else {
            referralGroup.style.display = 'none';
            referralInput.removeAttribute('required');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const caseType = document.getElementById('case_type').value;
        toggleCaseType(caseType);

        const checkedDir = document.querySelector('input[name="treatment_direction"]:checked');
        if (checkedDir) {
            toggleReferral(checkedDir.value);
        }
    });
</script>
@endsection
