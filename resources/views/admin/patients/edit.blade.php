@extends('layouts.admin')

@section('title', 'Sửa Bệnh Nhân — AmaTrung')
@section('page-title', 'Chỉnh Sửa: ' . $patient->full_name)

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-secondary" style="background: #fff;">
            <span>←</span> Quay lại hồ sơ
        </a>
    </div>

    <div class="card" style="padding: 2.5rem; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.04);">
        <form action="{{ route('admin.patients.update', $patient) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 2rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 1rem;">
                <span style="background: #eff6ff; color: #3b82f6; width: 36px; height: 36px; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">👤</span>
                <h3 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: #1e3a5f;">Thông tin cơ bản</h3>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="full_name" class="form-label">Họ và tên <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="full_name" id="full_name" class="form-input" placeholder="VD: Nguyễn Văn An" value="{{ old('full_name', $patient->full_name) }}" required>
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label" style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Số điện thoại <span style="color: #ef4444;">*</span></span>
                        <label style="display: flex; align-items: center; gap: 0.25rem; font-weight: normal; cursor: pointer; font-size: 0.8rem; color: #64748b;">
                            <input type="checkbox" id="is_guardian_phone" name="is_guardian_phone" value="1" {{ (old('is_guardian_phone') || $patient->guardian_phone) ? 'checked' : '' }}>
                            SĐT người giám hộ
                        </label>
                    </label>
                    <input type="tel" name="{{ (old('is_guardian_phone') || $patient->guardian_phone) ? 'guardian_phone' : 'phone' }}" id="phone" class="form-input" placeholder="VD: 0912345678" value="{{ old('phone', $patient->phone) ?? old('guardian_phone', $patient->guardian_phone) }}" required>
                </div>

                <div class="form-group">
                    <label for="date_of_birth" class="form-label">Ngày sinh <span style="color: #ef4444;">*</span></label>
                    <input type="date" name="date_of_birth" id="date_of_birth" class="form-input" value="{{ old('date_of_birth', $patient->date_of_birth?->format('Y-m-d')) }}" required>
                </div>

                <div class="form-group">
                    <label for="gender" class="form-label">Giới tính <span style="color: #ef4444;">*</span></label>
                    <select name="gender" id="gender" class="form-input" style="appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2364748b%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 0.65rem auto; padding-right: 2.5rem;" required>
                        <option value="">-- Chọn giới tính --</option>
                        <option value="male" {{ old('gender', $patient->gender) == 'male' ? 'selected' : '' }}>Nam</option>
                        <option value="female" {{ old('gender', $patient->gender) == 'female' ? 'selected' : '' }}>Nữ</option>
                        <option value="other" {{ old('gender', $patient->gender) == 'other' ? 'selected' : '' }}>Khác</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-top: 1rem;">
                <label for="address" class="form-label">Địa chỉ thường trú <span style="color: #ef4444;">*</span></label>
                <input type="text" name="address" id="address" class="form-input" placeholder="Số nhà, đường, phường/xã, quận/huyện..." value="{{ old('address', $patient->address) }}" required>
            </div>

            <div id="guardian_block" style="display: {{ (old('is_guardian_phone') || $patient->guardian_phone) ? 'block' : 'none' }}; margin-top: 3rem; background: #fcfcfd; border-radius: 1.5rem; padding: 2rem; border: 1px solid #f1f5f9;">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                    <span style="background: #fdf2f8; color: #db2777; width: 32px; height: 32px; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; font-size: 1rem;">🛡️</span>
                    <h3 style="margin: 0; font-size: 1.1rem; color: #1e3a5f; font-weight: 700;">Thông tin người giám hộ <span style="font-weight: 400; color: #94a3b8; font-size: 0.85rem; margin-left: 0.5rem;">(Nếu có)</span></h3>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="guardian_name" class="form-label">Họ tên <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="guardian_name" id="guardian_name" class="form-input" value="{{ old('guardian_name', $patient->guardian_name) }}">
                    </div>
                    <div class="form-group">
                        <label for="relationship" class="form-label">Quan hệ <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="relationship" id="relationship" class="form-input" value="{{ old('relationship', $patient->relationship) }}" placeholder="VD: Bố, Mẹ, Con cái...">
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-top: 2rem;">
                <label for="note" class="form-label">Ghi chú y tế / Tiền sử</label>
                <textarea name="note" id="note" class="form-input" rows="4" placeholder="Nhập các lưu ý quan trọng về bệnh nhân (nếu có)...">{{ old('note', $patient->note) }}</textarea>
            </div>

            @if($patient->is_legacy_data)
            <div style="margin-top: 2rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 1rem;">
                    <span style="background: #fef3c7; color: #f59e0b; width: 36px; height: 36px; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">📋</span>
                    <h3 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: #1e3a5f;">Thông tin hồ sơ giấy</h3>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label for="legacy_date" class="form-label">Ngày ghi hồ sơ giấy gốc</label>
                        <input type="date" name="legacy_date" id="legacy_date" class="form-input" value="{{ old('legacy_date', $patient->legacy_date?->format('Y-m-d')) }}">
                    </div>

                    <div class="form-group">
                        <label for="legacy_note" class="form-label">Ghi chú về hồ sơ giấy</label>
                        <input type="text" name="legacy_note" id="legacy_note" class="form-input" placeholder="VD: Bệnh nhân từ sổ khám năm 2023, số thứ tự 45..." value="{{ old('legacy_note', $patient->legacy_note) }}">
                    </div>
                </div>
            </div>
            @endif

            <div style="display: flex; gap: 1rem; margin-top: 3rem; justify-content: flex-end; border-top: 1px solid #f1f5f9; padding-top: 2rem;">
                <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-secondary" style="background: #fff; min-width: 120px;">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary" style="min-width: 200px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <span>💾</span> Cập Nhật Thay Đổi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>

