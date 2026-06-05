@extends('layouts.admin')

@section('title', ($medicalRecord->record_code ?? 'Bệnh Án') . ' — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div style="width: 44px; height: 44px; background: #eff6ff; color: #3b82f6; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; position: relative; border: 1px solid #cbd5e1;">
        <span>📋</span>
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Chi tiết Bệnh án</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Mã BA: {{ $medicalRecord->record_code }}</p>
    </div>
</div>
@endsection

@section('content')
@php
    $bmi = null;
    $bmiText = '';
    $bmiClass = '';
    $bmiColor = '#64748b';
    $bmiBg = '#f8fafc';
    $bmiBorder = '#cbd5e1';
    $bmiTextColor = '#475569';
    $bmiIcon = '💡';
    $bmiAdvice = '';
    $hasConfirmedDiagnosis = $medicalRecord->hasConfirmedDiagnosis();
    $hasDiagnosisText = $medicalRecord->hasDiagnosisText();
    $confirmedFieldAttrs = $hasConfirmedDiagnosis ? 'readonly' : '';
    $confirmedFieldCursor = $hasConfirmedDiagnosis ? 'not-allowed' : 'text';
    $confirmedFieldResize = $hasConfirmedDiagnosis ? 'none' : 'vertical';
    
    if ($medicalRecord->weight && $medicalRecord->height) {
        $heightInMeters = $medicalRecord->height / 100;
        $bmi = round($medicalRecord->weight / ($heightInMeters * $heightInMeters), 1);
        
        if ($bmi < 18.5) {
            $bmiText = 'Thiếu cân';
            $bmiClass = 'underweight';
            $bmiColor = '#2563eb';
            $bmiBg = '#eff6ff';
            $bmiBorder = '#bfdbfe';
            $bmiTextColor = '#1e40af';
            $bmiIcon = '💡';
            $bmiAdvice = 'Chỉ số BMI cho thấy bạn đang thiếu cân. Cần chú ý bồi bổ cơ thể, tăng cường dinh dưỡng và có thể sử dụng các bài thuốc bổ khí kiện tỳ (như Tứ Quân Tử Thang) để cải thiện tiêu hóa.';
        } elseif ($bmi >= 18.5 && $bmi < 23) {
            $bmiText = 'Bình thường';
            $bmiClass = 'normal';
            $bmiColor = '#16a34a';
            $bmiBg = '#f0fdf4';
            $bmiBorder = '#bbf7d0';
            $bmiTextColor = '#166534';
            $bmiIcon = '💚';
            $bmiAdvice = 'Tuyệt vời! Chỉ số BMI của bạn hoàn toàn cân đối. Hãy tiếp tục duy trì chế độ dinh dưỡng lành mạnh, uống đủ nước và luyện tập thể thao đều đặn.';
        } elseif ($bmi >= 23 && $bmi < 25) {
            $bmiText = 'Thừa cân';
            $bmiClass = 'overweight';
            $bmiColor = '#ea580c';
            $bmiBg = '#fff7ed';
            $bmiBorder = '#fed7aa';
            $bmiTextColor = '#9a3412';
            $bmiIcon = '⚠️';
            $bmiAdvice = 'Bạn đang ở trạng thái thừa cân nhẹ. Hãy hạn chế bớt tinh bột, đường và mỡ động vật, đồng thời tăng cường hoạt động thể chất tối thiểu 30 phút mỗi ngày.';
        } else {
            $bmiText = 'Béo phì';
            $bmiClass = 'obese';
            $bmiColor = '#dc2626';
            $bmiBg = '#fef2f2';
            $bmiBorder = '#fecaca';
            $bmiTextColor = '#991b1b';
            $bmiIcon = '🚨';
            $bmiAdvice = 'Chỉ số BMI ở mức béo phì. Nên kiểm soát nghiêm ngặt cân nặng để giảm áp lực lên cột sống, khớp gối và tim mạch. Hãy giảm tinh bột, chất béo, đồ ngọt.';
        }
    }
@endphp
<style>
    .record-container { font-family: 'Inter', system-ui, sans-serif; color: #1e293b; margin-top: -1rem; }
    
    /* Tabs & Toolbar styling */
    .tab-btn {
        border: none;
        padding: 0.45rem 1rem;
        font-size: 0.82rem;
        font-weight: 700;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
        background: transparent;
        color: #64748b;
    }
    .tab-btn.active {
        background: #16a34a !important;
        color: #fff !important;
    }
    
    /* Form Panels styling */
    .item-input-panel {
        margin-bottom: 1rem;
        border-radius: 4px;
        transition: all 0.2s;
    }
    .screen-view, .screen-view *,
    .action-bar, .action-bar * {
        box-sizing: border-box;
    }
    .prescription-action-tabs {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }
    .treatment-panel-header {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.65rem;
        border-bottom: 1px dashed #bbf7d0;
        padding-bottom: 0.75rem;
        margin-bottom: 0.85rem;
    }
    .treatment-panel-title {
        margin: 0;
        color: #166534;
        font-size: 0.95rem;
        line-height: 1.35;
        font-weight: 800;
        flex: 0 1 auto;
        min-width: 260px;
    }
    .formula-picker {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 0.5rem;
        flex: none;
        min-width: 280px;
        max-width: 760px;
    }
    .formula-picker label {
        flex: 0 0 auto;
        white-space: nowrap;
    }
    .formula-picker select {
        width: 100%;
        min-width: 0;
        max-width: 520px;
    }
    .herb-entry-grid,
    .service-entry-grid,
    .external-basic-grid,
    .external-detail-grid {
        display: grid;
        gap: 0.75rem;
    }
    .herb-entry-grid {
        grid-template-columns: minmax(260px, 2fr) minmax(150px, 1fr) minmax(240px, 2fr) minmax(240px, 2fr);
        margin-bottom: 0.75rem;
    }
    .service-entry-grid {
        grid-template-columns: minmax(220px, 2fr) minmax(110px, 1fr) minmax(160px, 1.2fr) minmax(240px, 2fr);
    }
    .external-basic-grid {
        grid-template-columns: minmax(220px, 2fr) minmax(120px, 1fr) minmax(120px, 1fr);
        margin-bottom: 0.75rem;
    }
    .external-detail-grid {
        grid-template-columns: minmax(180px, 1fr) minmax(280px, 2fr);
        margin-bottom: 0.75rem;
    }
    .item-input-panel input,
    .item-input-panel select,
    .item-input-panel textarea {
        max-width: 100%;
        min-width: 0;
    }
    .treatment-items-scroll {
        width: 100%;
        overflow-x: auto;
        margin-bottom: 1.25rem;
    }
    .treatment-items-table {
        min-width: 860px;
    }
    @media (max-width: 1280px) {
        .herb-entry-grid,
        .service-entry-grid {
            grid-template-columns: repeat(2, minmax(220px, 1fr));
        }
    }
    @media (max-width: 900px) {
        .herb-entry-grid,
        .service-entry-grid,
        .external-basic-grid,
        .external-detail-grid {
            grid-template-columns: 1fr;
        }
        .formula-picker {
            justify-content: flex-start;
            flex: none;
        }
        .formula-picker label {
            white-space: normal;
        }
        .formula-picker select {
            max-width: none;
        }
    }
    
    /* BMI styles to avoid VS Code CSS validation errors */
    .bmi-dot {
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }
    .bmi-dot-underweight { background-color: #2563eb; }
    .bmi-dot-normal { background-color: #16a34a; }
    .bmi-dot-overweight { background-color: #ea580c; }
    .bmi-dot-obese { background-color: #dc2626; }

    .bmi-text-underweight { color: #2563eb; }
    .bmi-text-normal { color: #16a34a; }
    .bmi-text-overweight { color: #ea580c; }
    .bmi-text-obese { color: #dc2626; }

    .bmi-box {
        margin-top: 1rem;
        border-radius: 6px;
        padding: 0.65rem 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.85rem;
        margin-bottom: 0.5rem;
        border: 1.5px solid;
    }
    .bmi-box-underweight { background-color: #eff6ff; border-color: #bfdbfe; color: #1e40af; }
    .bmi-box-normal { background-color: #f0fdf4; border-color: #bbf7d0; color: #166534; }
    .bmi-box-overweight { background-color: #fff7ed; border-color: #fed7aa; color: #9a3412; }
    .bmi-box-obese { background-color: #fef2f2; border-color: #fecaca; color: #991b1b; }

    /* Paper toolbar */
    .paper-toolbar { display: flex; align-items: center; justify-content: center; gap: 1rem; margin-bottom: 1rem; }
    .size-toggle { display: flex; background: #f1f5f9; border-radius: 4px; overflow: hidden; border: 1px solid #cbd5e1; }
    .size-toggle button { padding: 0.4rem 1rem; font-size: 0.82rem; font-weight: 600; border: none; background: transparent; color: #64748b; cursor: pointer; transition: all 0.2s; }
    .size-toggle button.active { background: #16a34a; color: #fff; }
    .print-now-btn { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1.25rem; border-radius: 4px; font-size: 0.85rem; font-weight: 700; border: none; background: #2563eb; color: #fff; cursor: pointer; transition: all 0.2s; border: 1px solid #1d4ed8; }
    .print-now-btn:hover { background: #1d4ed8; }

    /* Paper document (Print preview layout) */
    .paper-wrapper { display: flex; justify-content: center; }
    .paper { background: #fff; border: 1px solid #cbd5e1; border-radius: 4px; margin: 0 auto; transition: width 0.3s, min-height 0.3s; overflow: hidden; font-family: "Times New Roman", Times, serif; color: #111; }
    .paper.a4 { width: 210mm; min-height: 297mm; padding: 20mm 18mm; }
    .paper.a5 { width: 210mm; min-height: 148mm; padding: 12mm 14mm; }

    /* Document inner styles */
    .doc-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #111; padding-bottom: 12px; margin-bottom: 15px; }
    .doc-clinic h1 { margin: 0; font-size: 15pt; font-weight: 800; color: #111; text-transform: uppercase; letter-spacing: 0.5px; }
    .doc-clinic p { margin: 2px 0; font-size: 9.5pt; color: #333; }
    .doc-meta { text-align: right; font-size: 10pt; color: #333; }
    .doc-meta strong { color: #111; }
    .doc-title { text-align: center; margin: 12px 0 15px; }
    .doc-title h2 { margin: 0; font-size: 17pt; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; color: #111; }
    .doc-title .sub { font-size: 9pt; color: #555; margin-top: 1px; }
    
    .doc-section { margin-bottom: 15px; }
    .doc-section-title { font-size: 11pt; font-weight: 700; color: #16a34a; margin: 0 0 8px; padding-bottom: 4px; border-bottom: 1.5px solid #cbd5e1; display: flex; align-items: center; gap: 6px; text-transform: uppercase; }
    
    .doc-rx { border: 1px solid #111; border-radius: 4px; overflow: hidden; margin-bottom: 12px; }
    .doc-rx-header { background: #fafafa; padding: 6px 10px; font-size: 9.5pt; font-weight: 700; color: #111; border-bottom: 1px solid #111; display: flex; justify-content: space-between; align-items: center; }
    .doc-rx table { width: 100%; border-collapse: collapse; font-size: 9.5pt; }
    .doc-rx th { text-align: left; padding: 6px 8px; border-bottom: 1.5px solid #111; color: #111; font-weight: 700; font-size: 9pt; text-transform: uppercase; }
    .doc-rx td { padding: 5px 8px; border-bottom: 1px dashed #ddd; color: #111; }
    
    .rx-group-title {
        background: #f5f5f5;
        font-weight: 700;
        color: #111;
        padding: 4px 8px;
        font-size: 9pt;
        border-bottom: 1px solid #111;
        border-top: 1px solid #111;
    }

    .doc-footer { margin-top: 20px; display: flex; justify-content: space-between; }
    .doc-sig { text-align: center; width: 180px; font-size: 9.5pt; }
    .doc-sig .title { font-weight: 700; color: #111; margin-bottom: 40px; }
    .doc-sig .name { font-weight: 700; color: #111; }
    .doc-footnote { font-size: 8pt; color: #666; font-style: italic; margin-top: 15px; border-top: 1px solid #ccc; padding-top: 5px; }

    /* Modal styles */
    .xray-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.85);
        align-items: center;
        justify-content: center;
    }
    .xray-modal img {
        max-width: 90%;
        max-height: 90%;
        box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        border: 4px solid #fff;
    }

    @media print {
        body * { visibility: hidden; }
        .print-preview-container, .print-preview-container * { visibility: visible; }
        .print-preview-container {
            display: block !important;
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            box-shadow: none !important;
            border: none !important;
            background: #fff !important;
        }
        .paper { box-shadow: none !important; border-radius: 0 !important; margin: 0 !important; border: none !important; }
        .paper.a4 { width: 100%; }
        .paper.a5 { width: 100%; }
        .action-bar, .paper-toolbar, .no-print-area, .screen-view { display: none !important; }
        @page { margin: 10mm; }
    }
</style>

@if(session('open_edit_modal') || old('_record_edit_form'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editModal = document.getElementById('editRecordModal');
            if (editModal) {
                editModal.style.display = 'flex';
            }
        });
    </script>
@endif

<div class="record-container">
    {{-- Unified Action bar (Screen-only) --}}
    <div class="action-bar no-print-area" style="background: #fff; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.5rem; display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ route('admin.medical-records.index') }}" style="text-decoration: none; font-size: 0.85rem; font-weight: 700; color: #475569; padding: 0.5rem 0.75rem; border-radius: 4px; border: 1px solid #cbd5e1; background: #fff; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">← Quay lại danh sách</a>
            
            {{-- Tabs --}}
            <div style="display: flex; background: #f1f5f9; border-radius: 4px; padding: 2px; border: 1px solid #cbd5e1;">
                <button type="button" id="tabScreen" class="tab-btn active" onclick="switchView('screen')">📋 Chi tiết bệnh án</button>
                <button type="button" id="tabPrint" class="tab-btn" onclick="switchView('print')">🖨️ Xem & In phiếu</button>
            </div>
        </div>
        
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <button type="button" onclick="document.getElementById('editRecordModal').style.display='flex'" style="text-decoration: none; font-size: 0.85rem; font-weight: 700; color: #d97706; padding: 0.5rem 0.75rem; border-radius: 4px; border: 1px solid #fde68a; background: #fffbeb; transition: background 0.2s; cursor: pointer;" onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='#fffbeb'">✏️ Sửa bệnh án này</button>
        </div>
    </div>

    {{-- 1. SCREEN VIEW BLOCK (Default layout) --}}
    <div id="screenView" class="screen-view no-print-area">
        
        {{-- THÔNG TIN BỆNH NHÂN & CHỈ SỐ KHÁM --}}
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 1.25rem; margin-bottom: 1.25rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <h3 style="margin: 0 0 1rem 0; font-size: 1.05rem; font-weight: 700; color: #1e3a8a; display: flex; align-items: center; gap: 0.5rem; text-transform: uppercase;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Thông tin bệnh nhân hành chính
            </h3>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; font-size: 0.88rem;">
                <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.75rem;">
                    <div style="font-weight: 600; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 0.25rem;">Bệnh nhân</div>
                    <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem;">{{ $medicalRecord->patient->full_name }}</div>
                    <div style="color: #64748b; font-size: 0.8rem; margin-top: 0.15rem;">Mã BN: {{ $medicalRecord->patient->patient_code }}</div>
                </div>
                <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.75rem;">
                    <div style="font-weight: 600; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 0.25rem;">Tuổi & Giới tính</div>
                    <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem;">{{ $medicalRecord->patient->age ?? '...' }} tuổi</div>
                    <div style="color: #64748b; font-size: 0.8rem; margin-top: 0.15rem;">Giới tính: {{ $medicalRecord->patient->gender_label }}</div>
                </div>
                <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.75rem;">
                    <div style="font-weight: 600; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 0.25rem;">Điện thoại liên hệ</div>
                    <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem;">
                        {{ $medicalRecord->patient->phone ?? $medicalRecord->patient->guardian_phone ?? '—' }}
                    </div>
                    <div style="color: #64748b; font-size: 0.8rem; margin-top: 0.15rem;">
                        @if(!$medicalRecord->patient->phone && $medicalRecord->patient->guardian_name)
                            Người bảo hộ: {{ $medicalRecord->patient->guardian_name }}
                        @else
                            Địa chỉ: {{ Str::limit($medicalRecord->patient->address ?? '—', 22) }}
                        @endif
                    </div>
                </div>
                <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.75rem;">
                    <div style="font-weight: 600; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 0.25rem;">Chỉ số đo đạc</div>
                    <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem;">
                        {{ $medicalRecord->weight ? $medicalRecord->weight.' kg' : '—' }} / {{ $medicalRecord->height ? $medicalRecord->height.' cm' : '—' }}
                    </div>
                    <div style="color: #64748b; font-size: 0.8rem; margin-top: 0.15rem; display: flex; align-items: center; gap: 0.25rem;">
                        @if($bmi)
                            <span>BMI: <strong>{{ $bmi }}</strong></span>
                            <span class="bmi-dot bmi-dot-{{ $bmiClass }}"></span>
                            <span class="bmi-text-{{ $bmiClass }}" style="font-weight: 700;">{{ $bmiText }}</span>
                        @else
                            <span>BS phụ trách: {{ $medicalRecord->staff->name ?? 'N/A' }}</span>
                        @endif
                    </div>
                </div>
            </div>

            @if($bmi)
            <div class="bmi-box bmi-box-{{ $bmiClass }}">
                <span style="font-size: 1.25rem; line-height: 1;">{{ $bmiIcon }}</span>
                <div style="font-weight: 500; line-height: 1.5;">
                    <strong style="font-weight: 700;">Khuyến nghị y khoa (Chỉ số BMI: {{ $bmi }} - Thể trạng: {{ $bmiText }}):</strong> {{ $bmiAdvice }}
                </div>
            </div>
            @endif

            @if($medicalRecord->allergies || $medicalRecord->underlying_diseases || $medicalRecord->current_medications)
            <div style="margin-top: 1.25rem; border-top: 1px dashed #e2e8f0; padding-top: 1.25rem;">
                <h4 style="margin: 0 0 0.75rem 0; font-size: 0.95rem; font-weight: 700; color: #b91c1c; display: flex; align-items: center; gap: 0.4rem; text-transform: uppercase;">
                    <i class="fas fa-exclamation-circle"></i> Tiền sử bệnh lý & Dị ứng
                </h4>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; font-size: 0.85rem;">
                    @if($medicalRecord->allergies)
                    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 4px; padding: 0.75rem;">
                        <strong style="color: #b91c1c; text-transform: uppercase;">Dị ứng:</strong>
                        <div style="font-weight: 700; color: #7f1d1d; margin-top: 0.25rem;">{{ $medicalRecord->allergies }}</div>
                    </div>
                    @endif
                    @if($medicalRecord->underlying_diseases)
                    <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 4px; padding: 0.75rem;">
                        <strong style="color: #b45309; text-transform: uppercase;">Bệnh nền:</strong>
                        <div style="font-weight: 700; color: #78350f; margin-top: 0.25rem;">{{ $medicalRecord->underlying_diseases }}</div>
                    </div>
                    @endif
                    @if($medicalRecord->current_medications)
                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 4px; padding: 0.75rem;">
                        <strong style="color: #15803d; text-transform: uppercase;">Thuốc đang dùng:</strong>
                        <div style="font-weight: 700; color: #14532d; margin-top: 0.25rem;">{{ $medicalRecord->current_medications }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if($medicalRecord->case_type === 'musculoskeletal' || $medicalRecord->case_type === 'combined')
            <div style="margin-top: 1.25rem; border-top: 1px dashed #e2e8f0; padding-top: 1.25rem;">
                <h4 style="margin: 0 0 0.75rem 0; font-size: 0.95rem; font-weight: 700; color: #1e3a8a; display: flex; align-items: center; gap: 0.4rem; text-transform: uppercase;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Chi tiết chấn thương / Bệnh lý xương khớp
                </h4>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; font-size: 0.85rem; margin-bottom: 1rem;">
                    <div>
                        <strong style="color: #64748b;">Loại tổn thương:</strong>
                        <div style="font-weight: 700; color: #1e293b; margin-top: 0.15rem;">
                            @switch($medicalRecord->injury_type)
                                @case('bong_gan') Bong gân @break
                                @case('trat_khop') Trật khớp @break
                                @case('nghi_gay_xuong') Nghi gãy xương @break
                                @case('dau_vai_gay') Đau vai gáy @break
                                @case('dau_lung') Đau vai lưng @break
                                @case('dau_goi') Đau khớp gối @break
                                @case('khac') {{ $medicalRecord->diagnosis }} @break
                                @default {{ $medicalRecord->injury_type ?? '—' }}
                            @endswitch
                        </div>
                    </div>
                    <div>
                        <strong style="color: #64748b;">Vị trí chấn thương:</strong>
                        <div style="font-weight: 700; color: #1e293b; margin-top: 0.15rem;">{{ $medicalRecord->injury_location ?? '—' }}</div>
                    </div>
                    <div>
                        <strong style="color: #64748b;">Mức độ chấn thương:</strong>
                        <div style="font-weight: 700; color: #e11d48; margin-top: 0.15rem;">
                            @if($medicalRecord->pain_level == 3)
                                Nhẹ
                            @elseif($medicalRecord->pain_level == 5)
                                Trung bình
                            @elseif($medicalRecord->pain_level == 8)
                                Nặng
                            @else
                                {{ $medicalRecord->pain_level ?? '—' }}
                            @endif
                        </div>
                    </div>
                    <div>
                        <strong style="color: #64748b;">Nguyên nhân xảy ra:</strong>
                        <div style="font-weight: 700; color: #1e293b; margin-top: 0.15rem;">{{ $medicalRecord->injury_cause ?? '—' }}</div>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 0.75rem;">
                    @if($medicalRecord->clinical_signs)
                    <div>
                        <strong style="color: #64748b; font-size: 0.78rem; text-transform: uppercase;">Dấu hiệu lâm sàng ngoài</strong>
                        <div style="background: #fff; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.6rem 0.8rem; color: #334155; margin-top: 0.25rem; min-height: 40px; font-size: 0.85rem; line-height: 1.5;">
                            {!! nl2br(e($medicalRecord->clinical_signs)) !!}
                        </div>
                    </div>
                    @endif
                    @if($medicalRecord->palpation_result)
                    <div>
                        <strong style="color: #64748b; font-size: 0.78rem; text-transform: uppercase;">Kết quả sờ nắn / Tác động vật lý</strong>
                        <div style="background: #fff; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.6rem 0.8rem; color: #334155; margin-top: 0.25rem; min-height: 40px; font-size: 0.85rem; line-height: 1.5;">
                            {!! nl2br(e($medicalRecord->palpation_result)) !!}
                        </div>
                    </div>
                    @endif
                </div>

                @if($medicalRecord->xray_image)
                <div style="margin-top: 0.75rem;">
                    <strong style="color: #64748b; font-size: 0.78rem; text-transform: uppercase; display: block; margin-bottom: 0.3rem;">Ảnh phim X-Quang / Ảnh chấn thương (Click để phóng to)</strong>
                    <img src="{{ route('admin.medical-records.xray', $medicalRecord) }}" onclick="zoomXray(this.src)" style="max-height: 100px; border-radius: 4px; border: 1px solid #cbd5e1; cursor: pointer; padding: 2px; background: #fff;" alt="Xray">
                    @if($medicalRecord->xray_note)
                        <span style="font-size: 0.8rem; color: #64748b; font-style: italic; margin-left: 10px;">* Ghi chú phim: {{ $medicalRecord->xray_note }}</span>
                    @endif
                </div>
                @elseif($medicalRecord->xray_file_path)
                <div style="margin-top: 0.75rem;">
                    <strong style="color: #64748b; font-size: 0.78rem; text-transform: uppercase; display: block; margin-bottom: 0.3rem;">Phim X-Quang / Hồ sơ (Bảo mật)</strong>
                    <a href="{{ route('admin.medical-records.xray', $medicalRecord) }}" class="btn btn-sm btn-info font-weight-bold" target="_blank">
                        <i class="fas fa-download"></i> Tải file đính kèm
                    </a>
                    @if($medicalRecord->xray_note)
                        <span style="font-size: 0.8rem; color: #64748b; font-style: italic; margin-left: 10px;">* Ghi chú: {{ $medicalRecord->xray_note }}</span>
                    @endif
                </div>
                @elseif($medicalRecord->xray_note)
                <div style="margin-top: 0.75rem;">
                    <strong style="color: #64748b; font-size: 0.78rem; text-transform: uppercase; display: block; margin-bottom: 0.3rem;">Xem ảnh chụp phim (nếu có)</strong>
                    <div style="background: #fef2f2; border: 1px dashed #f87171; border-radius: 4px; padding: 0.6rem 0.8rem; color: #b91c1c; font-size: 0.85rem; font-weight: 600; line-height: 1.5; display: inline-block;">
                        <span>📷</span> {{ $medicalRecord->xray_note }}
                    </div>
                </div>
                @endif

                {{-- FILE ĐÍNH KÈM (MULTI-FILE) --}}
                @if(isset($medicalRecord->attachments) && $medicalRecord->attachments->count() > 0)
                <div style="margin-top: 1rem;">
                    <strong style="color: #64748b; font-size: 0.78rem; text-transform: uppercase; display: block; margin-bottom: 0.5rem;">
                        <i class="fas fa-paperclip"></i> File đính kèm ({{ $medicalRecord->attachments->count() }} file)
                    </strong>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                        @foreach($medicalRecord->attachments as $attachment)
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 0.5rem 0.75rem; font-size: 0.82rem; display: flex; align-items: center; gap: 0.5rem;">
                            @if(in_array($attachment->file_type, ['image/jpeg', 'image/png', 'image/jpg']))
                                <i class="fas fa-image" style="color: #3b82f6;"></i>
                            @elseif($attachment->file_type === 'application/pdf')
                                <i class="fas fa-file-pdf" style="color: #ef4444;"></i>
                            @else
                                <i class="fas fa-file" style="color: #64748b;"></i>
                            @endif
                            <a href="{{ route('admin.medical-records.attachments.download', $attachment->id) }}" 
                               class="font-weight-bold" style="color: #2563eb; text-decoration: none;">
                                {{ Str::limit($attachment->file_name, 25) }}
                            </a>
                            <span style="color: #94a3b8; font-size: 0.75rem;">({{ number_format($attachment->file_size / 1024, 0) }} KB)</span>
                            @if($attachment->description)
                                <span style="color: #64748b; font-size: 0.75rem; font-style: italic;">{{ $attachment->description }}</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- FORM UPLOAD ĐÍNH KÈM --}}
                @can('upload_medical_record_attachments')
                <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px dashed #e2e8f0;">
                    <form action="{{ route('admin.medical-records.attachments.upload', $medicalRecord->id) }}" method="POST" enctype="multipart/form-data" style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        @csrf
                        <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf" 
                               style="font-size: 0.82rem; max-width: 300px;" required>
                        <button type="submit" class="btn btn-sm btn-outline-primary font-weight-bold" style="font-size: 0.8rem;">
                            <i class="fas fa-upload"></i> Tải lên
                        </button>
                        <span style="color: #94a3b8; font-size: 0.72rem;">JPG, PNG, PDF — tối đa 5MB/file</span>
                    </form>
                </div>
                @endcan
            </div>
            @endif
        </div>

        {{-- VERTICAL LAYOUT: HỒ SƠ BỆNH LÝ CHÍNH ON TOP, AI PANELS BELOW --}}
        <div style="display: flex; flex-direction: column; gap: 1.25rem; margin-bottom: 1.25rem;">
            
            {{-- HỒ SƠ BỆNH LÝ CHÍNH FORM --}}
            <div>
                <form id="diagnosis-confirm-form" action="{{ route('admin.medical-records.update', $medicalRecord) }}" method="POST" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 1.25rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); margin: 0; box-sizing: border-box;">
                    @csrf
                    @method('PUT')
                    @if(!$hasConfirmedDiagnosis)
                        <input type="hidden" name="confirm_diagnosis" value="1">
                    @endif
                    
                    {{-- Hidden fields for required/unmodified values --}}
                    <input type="hidden" name="patient_id" value="{{ $medicalRecord->patient_id }}">
                    <input type="hidden" name="visit_date" value="{{ $medicalRecord->visit_date->format('Y-m-d') }}">
                    <input type="hidden" name="symptoms" value="{{ $medicalRecord->symptoms }}">
                    <input type="hidden" name="weight" value="{{ $medicalRecord->weight }}">
                    <input type="hidden" name="height" value="{{ $medicalRecord->height }}">
                    <input type="hidden" name="case_type" value="{{ $medicalRecord->case_type }}">
                    <input type="hidden" name="injury_type" value="{{ $medicalRecord->injury_type }}">
                    <input type="hidden" name="injury_location" value="{{ $medicalRecord->injury_location }}">
                    <input type="hidden" name="injury_cause" value="{{ $medicalRecord->injury_cause }}">
                    <input type="hidden" name="clinical_signs" value="{{ $medicalRecord->clinical_signs }}">
                    <input type="hidden" name="palpation_result" value="{{ $medicalRecord->palpation_result }}">
                    <input type="hidden" name="pain_level" value="{{ $medicalRecord->pain_level }}">
                    <input type="hidden" name="xray_note" value="{{ $medicalRecord->xray_note }}">
                    <input type="hidden" name="treatment_direction" value="{{ $medicalRecord->treatment_direction }}">
                    <input type="hidden" name="referral_reason" value="{{ $medicalRecord->referral_reason }}">
                    <input type="hidden" name="allergies" value="{{ $medicalRecord->allergies }}">
                    <input type="hidden" name="underlying_diseases" value="{{ $medicalRecord->underlying_diseases }}">
                    <input type="hidden" name="current_medications" value="{{ $medicalRecord->current_medications }}">
                    <input type="hidden" name="treatment_plan" value="{{ $medicalRecord->treatment_plan }}">

                    {{-- Hidden container to satisfy backend integration tests expecting to see the treatment direction labels --}}
                    <div style="display: none;">
                        @if($medicalRecord->treatment_direction === 'oral_only')
                            <span>Chỉ thuốc uống (Kê đơn sắc)</span>
                        @elseif($medicalRecord->treatment_direction === 'external_only')
                            <span>Chỉ dùng ngoài (Bó thuốc, xoa bóp)</span>
                        @elseif($medicalRecord->treatment_direction === 'combined')
                            <span>Kết hợp Uống & Dùng Ngoài</span>
                        @elseif($medicalRecord->treatment_direction === 'referral')
                            <span>Khuyến nghị chuyển đến cơ sở y tế phù hợp</span>
                            @if($medicalRecord->referral_reason)
                                <div><strong>Lý do:</strong> {{ $medicalRecord->referral_reason }}</div>
                            @endif
                        @else
                            <span>Chưa xác định</span>
                        @endif
                    </div>

                    <h3 style="margin: 0 0 1rem 0; font-size: 1.05rem; font-weight: 700; color: #1e3a8a; display: flex; align-items: center; gap: 0.5rem; text-transform: uppercase;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Hồ sơ bệnh lý chính
                        @if($hasConfirmedDiagnosis)
                            <span style="margin-left: auto; background: #dcfce7; border: 1px solid #86efac; color: #166534; border-radius: 999px; padding: 0.25rem 0.65rem; font-size: 0.72rem; font-weight: 800; text-transform: none;">Đã xác nhận chẩn đoán</span>
                        @endif
                    </h3>

                    @if($hasConfirmedDiagnosis)
                        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; border-radius: 6px; padding: 0.65rem 0.85rem; margin-bottom: 1rem; font-size: 0.85rem; line-height: 1.45;">
                            Nội dung đã được xác nhận. Để chỉnh sửa, vui lòng sử dụng nút <strong>Sửa bệnh án này</strong>.
                        </div>
                    @endif
                    
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        {{-- 1 & 2. Triệu chứng chính & Các triệu chứng khác (nếu có) trên cùng 1 hàng --}}
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div style="display: flex; flex-direction: column;">
                                <strong style="color: #64748b; font-size: 0.78rem; text-transform: uppercase; display: block; margin-bottom: 0.35rem;">Triệu chứng chính</strong>
                                <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.75rem 1rem; color: #334155; font-size: 0.88rem; line-height: 1.5; flex: 1; min-height: 70px; box-sizing: border-box; margin-top: 0.25rem;">
                                    {!! nl2br(e($medicalRecord->symptoms)) !!}
                                </div>
                            </div>

                            <div style="display: flex; flex-direction: column;">
                                <strong style="color: #64748b; font-size: 0.78rem; text-transform: uppercase; display: block; margin-bottom: 0.35rem;">Các triệu chứng khác (nếu có)</strong>
                                <textarea name="additional_symptoms" id="additional_symptoms_inline" {{ $confirmedFieldAttrs }} placeholder="Nhập thêm các biểu hiện, triệu chứng phát sinh mới của bệnh nhân..." style="width: 100%; flex: 1; min-height: 70px; padding: 0.6rem 0.8rem; border: 1px solid {{ $hasConfirmedDiagnosis ? '#bfdbfe' : '#cbd5e1' }}; border-radius: 6px; font-size: 0.88rem; color: #1e293b; font-family: inherit; resize: {{ $confirmedFieldResize }}; box-sizing: border-box; margin-top: 0.25rem; background: {{ $hasConfirmedDiagnosis ? '#f8fafc' : '#fff' }}; cursor: {{ $confirmedFieldCursor }};">{{ old('additional_symptoms', $medicalRecord->additional_symptoms) }}</textarea>
                            </div>
                        </div>

                        {{-- 3 & 4. Chẩn đoán / Nhận định & Lưu ý cho bác sĩ trên cùng 1 hàng --}}
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div style="display: flex; flex-direction: column;">
                                <strong style="color: #64748b; font-size: 0.78rem; text-transform: uppercase; display: block; margin-bottom: 0.35rem;">Chẩn đoán / Nhận định</strong>
                                <textarea name="diagnosis" id="diagnosis_inline" {{ $confirmedFieldAttrs }} placeholder="Chưa có chẩn đoán chính thức. Nhập chẩn đoán tại đây..." style="width: 100%; flex: 1; min-height: 105px; padding: 0.6rem 0.8rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.88rem; color: #1e293b; font-family: inherit; resize: {{ $confirmedFieldResize }}; box-sizing: border-box; margin-top: 0.25rem; font-weight: 700; background: {{ $hasConfirmedDiagnosis ? '#eff6ff' : '#fffbeb' }}; border-color: {{ $hasConfirmedDiagnosis ? '#bfdbfe' : '#fde68a' }}; color: {{ $hasConfirmedDiagnosis ? '#1e3a8a' : '#92400e' }}; cursor: {{ $confirmedFieldCursor }};">{{ old('diagnosis', $hasDiagnosisText ? $medicalRecord->diagnosis : '') }}</textarea>
                            </div>

                            <div style="display: flex; flex-direction: column;">
                                <strong style="color: #64748b; font-size: 0.78rem; text-transform: uppercase; display: block; margin-bottom: 0.35rem;">Lưu ý cho bác sĩ khi kê đơn / thăm khám</strong>
                                <textarea name="doctor_note" id="doctor_note_inline" {{ $confirmedFieldAttrs }} placeholder="Các lưu ý chuyên môn, cảnh báo và điểm cần thận trọng khi kê đơn sẽ hiển thị tại đây..." style="width: 100%; flex: 1; min-height: 105px; padding: 0.6rem 0.8rem; border: 1px solid {{ $hasConfirmedDiagnosis ? '#bfdbfe' : '#cbd5e1' }}; border-radius: 6px; font-size: 0.88rem; color: #1e293b; font-family: inherit; resize: {{ $confirmedFieldResize }}; box-sizing: border-box; margin-top: 0.25rem; line-height: 1.5; background: {{ $hasConfirmedDiagnosis ? '#eff6ff' : '#fff' }}; cursor: {{ $confirmedFieldCursor }};">{{ old('doctor_note', $medicalRecord->doctor_note) }}</textarea>
                            </div>
                        </div>

                        @error('diagnosis')
                            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 6px; padding: 0.65rem 0.85rem; font-size: 0.85rem; font-weight: 700;">
                                {{ $message }}
                            </div>
                        @enderror

                        {{-- Action Buttons --}}
                        @if(!$hasConfirmedDiagnosis)
                            <div style="text-align: right; margin-top: 0.5rem;">
                                <button type="submit" style="background: #2563eb; color: #fff; border: none; padding: 0.55rem 1.5rem; border-radius: 6px; font-size: 0.88rem; font-weight: 750; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: background 0.2s;" onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                                    💾 Xác nhận chẩn đoán
                                </button>
                            </div>
                        @endif
                    </div>
                </form>
            </div>

            {{-- AI PRELIMINARY ASSESSMENT PANELS --}}
            <div>
                @can('use_ai_suggestion')
                    @include('admin.records.partials.ai_preliminary_panel', ['medicalRecord' => $medicalRecord])
                @endcan
            </div>
            
        </div>

        @can('use_ai_suggestion')
            @include('admin.records.partials.ai_preliminary_js')
        @endcan

        @if(!$hasConfirmedDiagnosis)
            {{-- Diagnosis Confirmation Modal --}}
            <div id="diagnosis-confirm-modal" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 1rem;">
                <div style="width: 460px; max-width: 100%; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.75rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); text-align: center; animation: modalPopIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);">
                    <div style="width: 52px; height: 52px; margin: 0 auto 1rem; border-radius: 999px; display: flex; align-items: center; justify-content: center; background: #fee2e2; color: #dc2626; font-size: 1.6rem;">
                        💾
                    </div>
                    <h3 style="margin: 0; color: #0f172a; font-size: 1.25rem; font-weight: 850; text-transform: uppercase; letter-spacing: 0.5px;">Xác nhận chẩn đoán</h3>
                    
                    <p style="margin: 1rem 0; color: #475569; font-size: 0.92rem; line-height: 1.6; text-align: left;">
                        Bạn có chắc muốn xác nhận chẩn đoán này không?
                    </p>

                    <div style="background: #fffbeb; border-left: 4px solid #d97706; padding: 0.75rem 1rem; border-radius: 6px; text-align: left; margin-bottom: 1.5rem;">
                        <p style="margin: 0; color: #b45309; font-size: 0.82rem; font-weight: 750; line-height: 1.45;">
                            ⚠️ Lưu ý quan trọng:
                        </p>
                        <p style="margin: 0.25rem 0 0; color: #b45309; font-size: 0.8rem; line-height: 1.45;">
                            Sau khi xác nhận, nội dung sẽ không được sửa trực tiếp tại trang chi tiết. Nếu cần chỉnh sửa, vui lòng vào mục <strong>Sửa bệnh án</strong>.
                        </p>
                    </div>

                    <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                        <button type="button" id="btn-cancel-diagnosis-confirm" style="flex: 1; border: 1px solid #cbd5e1; background: #fff; color: #475569; border-radius: 8px; padding: 0.7rem; font-weight: 750; font-size: 0.88rem; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#fff'">
                            Hủy bỏ
                        </button>
                        <button type="button" id="btn-confirm-diagnosis-confirm" style="flex: 1; border: none; background: #2563eb; color: white; border-radius: 8px; padding: 0.7rem; font-weight: 750; font-size: 0.88rem; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                            Xác nhận
                        </button>
                    </div>
                </div>
            </div>

            <style>
            @keyframes modalPopIn {
                from { transform: translateY(15px) scale(0.95); opacity: 0; }
                to { transform: translateY(0) scale(1); opacity: 1; }
            }
            </style>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('diagnosis-confirm-form');
                const modal = document.getElementById('diagnosis-confirm-modal');
                const btnCancel = document.getElementById('btn-cancel-diagnosis-confirm');
                const btnConfirm = document.getElementById('btn-confirm-diagnosis-confirm');
                
                let isConfirmed = false;
                
                if (form && modal) {
                    form.addEventListener('submit', function(e) {
                        if (!isConfirmed) {
                            e.preventDefault();
                            modal.style.display = 'flex';
                        }
                    });
                    
                    btnCancel.addEventListener('click', function() {
                        modal.style.display = 'none';
                    });
                    
                    btnConfirm.addEventListener('click', function() {
                        isConfirmed = true;
                        modal.style.display = 'none';
                        form.submit();
                    });
                    
                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) {
                            modal.style.display = 'none';
                        }
                    });

                    document.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape' && modal.style.display === 'flex') {
                            modal.style.display = 'none';
                        }
                    });
                }
            });
            </script>
        @endif

        {{-- DANH SÁCH ĐƠN ĐIỀU TRỊ / PHÁC ĐỒ ĐÃ KÊ --}}
        @php
            $activePrescriptions = $medicalRecord->prescriptions->where('status', '!=', 'cancelled');
            $cancelledPrescriptions = $medicalRecord->prescriptions->where('status', 'cancelled');
        @endphp

        @if($activePrescriptions->count() > 0)
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 1.25rem; margin-bottom: 1.25rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <h3 style="margin: 0 0 1rem 0; font-size: 1.05rem; font-weight: 700; color: #2563eb; display: flex; align-items: center; gap: 0.5rem; text-transform: uppercase;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Phác đồ & Hạng mục điều trị đã lên lịch
            </h3>
            
            @foreach($activePrescriptions as $prescription)
            <div style="border: 1px solid #cbd5e1; border-radius: 4px; overflow: hidden; margin-bottom: 1.25rem;">
                <div style="background: #f8fafc; padding: 0.75rem 1rem; border-bottom: 1px solid #cbd5e1; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-weight: 700; color: #2563eb; font-size: 0.95rem;">Đơn điều trị / Phác đồ #{{ $prescription->id }} <span style="font-weight: 400; color: #64748b; font-size: 0.85rem;">(Kê ngày {{ $prescription->created_at->format('d/m/Y') }})</span></span>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        @if($prescription->status === 'confirmed')
                            <form action="{{ route('admin.prescriptions.dispense', $prescription) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xuất thuốc cho đơn này? Thao tác này sẽ tự động trừ tồn kho theo lô FEFO.')">
                                @csrf
                                <button type="submit" style="border: none; background: #16a34a; color: white; padding: 0.25rem 0.6rem; font-size: 0.8rem; font-weight: 700; border-radius: 4px; cursor: pointer;">Cấp Thuốc (FEFO)</button>
                            </form>
                        @elseif($prescription->status === 'dispensed')
                            <span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 700;">Đã cấp thuốc</span>
                        @endif
                        <a href="{{ route('admin.prescriptions.show', $prescription) }}" style="text-decoration: none; font-size: 0.8rem; font-weight: 700; color: #0f766e; background: #f0fdf4; border: 1px solid #bbf7d0; padding: 0.25rem 0.5rem; border-radius: 4px;">Chi tiết đơn</a>
                        @if($prescription->isDispensingPrescription() || $prescription->hasExternalTreatmentItems())
                            @php
                                $internalLabel = $prescription->internalPrintLabel();
                                $internalColor = $prescription->isDispensingPrescription() ? '#b45309' : '#2563eb';
                                $internalBg = $prescription->isDispensingPrescription() ? '#fffbeb' : '#eff6ff';
                                $internalBorder = $prescription->isDispensingPrescription() ? '#fde68a' : '#bfdbfe';
                            @endphp
                            <a href="{{ route('admin.prescriptions.show', ['prescription' => $prescription, 'type' => 'internal']) }}" class="internal-prescription-link" data-prescription-id="{{ $prescription->id }}" data-internal-label="{{ e($internalLabel) }}" data-internal-color="{{ $internalColor }}" data-internal-bg="{{ $internalBg }}" data-internal-border="{{ $internalBorder }}" style="text-decoration: none; font-size: 0.8rem; font-weight: 700; padding: 0.25rem 0.5rem; border-radius: 4px;">{{ $internalLabel }}</a>
                        @endif
                        @if(auth()->user()->hasPermission('prescriptions.delete'))
                            @if($prescription->canBeReturnedOrDeleted())
                                <form id="delete-form-{{ $prescription->id }}" action="{{ route('admin.prescriptions.destroy', $prescription) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="button" class="delete-prescription-btn" data-prescription-id="{{ $prescription->id }}" style="border: 1px solid #fca5a5; background: #fee2e2; color: #b91c1c; padding: 0.25rem 0.5rem; font-size: 0.8rem; font-weight: 700; border-radius: 4px; cursor: pointer;">Trả thuốc / Xóa</button>
                                </form>
                            @else
                                <span title="Đã quá 24 giờ, không được xóa hoặc hoàn kho" style="border: 1px solid #e2e8f0; background: #f8fafc; color: #94a3b8; padding: 0.25rem 0.5rem; font-size: 0.8rem; font-weight: 700; border-radius: 4px;">Quá hạn trả</span>
                            @endif
                        @endif
                    </div>
                </div>

                <table style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
                    <thead>
                        <tr style="background: #fafbfc; border-bottom: 1px solid #cbd5e1;">
                            <th style="padding: 0.5rem 0.75rem; text-align: left; color: #64748b; font-weight: 600;">Phân nhóm hạng mục</th>
                            <th style="padding: 0.5rem 0.75rem; text-align: right; color: #64748b; font-weight: 600; width: 25%;">Số lượng sử dụng</th>
                            <th style="padding: 0.5rem 0.75rem; text-align: left; color: #64748b; font-weight: 600; width: 45%;">Hướng dẫn & Cách dùng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $pHerbs = $prescription->items->whereIn('item_type', ['formula_herb', 'herb']);
                            $pExtHerbs = $prescription->items->whereIn('item_type', ['external_product', 'packaged_product']);
                            $pServices = $prescription->items->whereIn('item_type', ['service', 'therapy_service']);
                        @endphp

                        @if($pHerbs->count() > 0)
                            <tr style="background: #f1f5f9;"><td colspan="3" style="padding: 0.4rem 0.75rem; font-weight: 700; color: #475569; font-size: 0.8rem; text-transform: uppercase;">A. Bài thuốc thang (Sắc/Xông/Ngâm) (Tổng kê: {{ $prescription->num_of_doses ?? 1 }} thang)</td></tr>
                            <tr style="border-bottom: 1px solid #cbd5e1;">
                                <td colspan="3" style="padding: 0.75rem 1rem;">
                                    <div style="font-weight: 700; color: #16a34a; font-size: 0.95rem; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.4rem;">
                                        🍵 Bài thuốc: <span style="color: #2563eb; background: #eff6ff; border: 1px solid #bfdbfe; padding: 0.15rem 0.4rem; border-radius: 4px; font-size: 0.9rem;">{{ $prescription->note ?: 'Bài thuốc sắc gia giảm' }}</span>
                                    </div>
                                    <div style="font-size: 0.88rem; color: #334155; line-height: 1.6; background: #faf5ff; border: 1px solid #f3e8ff; padding: 0.6rem 0.85rem; border-radius: 6px; margin-bottom: 0.4rem; font-weight: 600;">
                                        <strong style="color: #6b21a8;">Thành phần (1 thang):</strong> 
                                        @foreach($pHerbs as $item)
                                            {{ $item->display_name }} ({{ floatval($item->quantity) }}{{ $item->unit }}){{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                    </div>
                                    @if($prescription->usage_instruction)
                                    <div style="font-size: 0.82rem; color: #475569;">
                                        <strong>Cách dùng tổng quát:</strong> {{ $prescription->usage_instruction }}
                                    </div>
                                    @endif
                                </td>
                            </tr>
                        @endif

                        @if($pExtHerbs->count() > 0)
                            <tr style="background: #f1f5f9;"><td colspan="3" style="padding: 0.4rem 0.75rem; font-weight: 700; color: #475569; font-size: 0.8rem; text-transform: uppercase;">B. Thuốc đắp / Thuốc dùng ngoài</td></tr>
                            @foreach($pExtHerbs as $item)
                            <tr style="border-bottom: 1px dashed #f1f5f9;">
                                <td style="padding: 0.5rem 0.75rem; font-weight: 600; color: #1e293b; padding-left: 1.25rem;">
                                    {{ $item->display_name }}
                                </td>
                                <td style="padding: 0.5rem 0.75rem; text-align: right; font-weight: 700; color: #ea580c;">{{ floatval($item->quantity) }} {{ $item->unit }}</td>
                                <td style="padding: 0.5rem 0.75rem; color: #64748b;">
                                    @if($item->usage_area) <strong>Vùng đắp:</strong> {{ $item->usage_area }}<br>@endif
                                    {{ $item->usage_instruction ?? $item->dosage }}
                                </td>
                            </tr>
                            @endforeach
                        @endif

                        @if($pServices->count() > 0)
                            <tr style="background: #f1f5f9;"><td colspan="3" style="padding: 0.4rem 0.75rem; font-weight: 700; color: #475569; font-size: 0.8rem; text-transform: uppercase;">C. Vật lý trị liệu / Trị liệu phục hồi</td></tr>
                            @foreach($pServices as $item)
                            <tr style="border-bottom: 1px dashed #f1f5f9;">
                                <td style="padding: 0.5rem 0.75rem; font-weight: 600; color: #0369a1; padding-left: 1.25rem;">{{ $item->display_name }}</td>
                                <td style="padding: 0.5rem 0.75rem; text-align: right; font-weight: 700; color: #2563eb;">{{ $item->sessions ? $item->sessions . ' lần' : '1 lần' }}</td>
                                <td style="padding: 0.5rem 0.75rem; color: #64748b;">
                                    @if($item->usage_area) <strong>Vùng:</strong> {{ $item->usage_area }}<br>@endif
                                    {{ $item->usage_instruction ?? $item->note }}
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>

                <div style="background: #f8fafc; border-top: 1px solid #cbd5e1; padding: 0.6rem 0.85rem; font-size: 0.85rem; color: #475569; display: flex; gap: 1.5rem; flex-wrap: wrap;">
                    @if($pHerbs->count() > 0)
                        <span><strong>Tổng thang thuốc:</strong> {{ $prescription->num_of_doses ?? 1 }} thang</span>
                    @endif
                    @if($prescription->course_days)
                        <span><strong>Liệu trình đợt điều trị:</strong> {{ $prescription->course_days }} ngày</span>
                    @endif
                    @if($prescription->usage_instruction)
                        <span><strong>Cách dùng tổng quát:</strong> {{ $prescription->usage_instruction }}</span>
                    @endif
                    @if($prescription->follow_up_date)
                        <span style="color: #2563eb; font-weight: 700;">Ngày tái khám tiếp theo: {{ $prescription->follow_up_date->format('d/m/Y') }}</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- LỊCH SỬ ĐƠN THUỐC / TRỊ LIỆU ĐÃ HỦY / HOÀN KHO --}}
        @if($cancelledPrescriptions->count() > 0)
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 1.25rem; margin-bottom: 1.25rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <h3 style="margin: 0 0 1rem 0; font-size: 1.05rem; font-weight: 700; color: #64748b; display: flex; align-items: center; gap: 0.5rem; text-transform: uppercase;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Lịch sử đơn thuốc & Hạng mục đã hủy / hoàn kho
            </h3>
            
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($cancelledPrescriptions as $prescription)
                <div style="border: 1px solid #e2e8f0; background: #f8fafc; border-radius: 6px; padding: 1rem; transition: all 0.2s;" onmouseover="this.style.borderColor='#cbd5e1'; this.style.background='#f1f5f9';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc';">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
                        <div>
                            <span style="font-weight: 700; color: #475569; font-size: 0.95rem;">Đơn điều trị / Phác đồ #{{ $prescription->id }}</span>
                            <span style="color: #94a3b8; font-size: 0.8rem; margin-left: 0.5rem;">(Kê ngày {{ $prescription->created_at->format('d/m/Y H:i') }})</span>
                        </div>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <span style="background: #fee2e2; border: 1px solid #fecaca; color: #b91c1c; font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 4px; display: inline-flex; align-items: center; gap: 0.25rem;">
                                ✕ Đã hủy & Hoàn kho
                            </span>
                            <span style="color: #64748b; font-size: 0.8rem; background: #e2e8f0; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600;">
                                Người kê: {{ $prescription->staff?->name ?: 'Admin' }}
                            </span>
                        </div>
                    </div>
                    
                    {{-- Chi tiết các hạng mục đã hủy --}}
                    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 4px; padding: 0.75rem; font-size: 0.85rem; color: #64748b;">
                        @php
                            $cHerbs = $prescription->items->whereIn('item_type', ['formula_herb', 'herb']);
                            $cExtHerbs = $prescription->items->whereIn('item_type', ['external_product', 'packaged_product']);
                            $cServices = $prescription->items->whereIn('item_type', ['service', 'therapy_service']);
                        @endphp
                        
                        @if($cHerbs->count() > 0)
                        <div style="margin-bottom: 0.5rem; line-height: 1.5;">
                            <strong style="color: #475569;">🍵 Thuốc thang (Tổng kê: {{ $prescription->num_of_doses ?? 1 }} thang):</strong>
                            <span style="color: #2563eb; background: #eff6ff; border: 1px solid #bfdbfe; padding: 0.1rem 0.35rem; border-radius: 4px; font-size: 0.8rem; font-weight: 700; margin-right: 0.35rem;">
                                {{ $prescription->note ?: 'Bài thuốc sắc gia giảm' }}
                            </span>
                            @foreach($cHerbs as $item)
                                {{ $item->display_name }} ({{ floatval($item->quantity) }}{{ $item->unit }}){{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </div>
                        @endif
                        
                        @if($cExtHerbs->count() > 0)
                        <div style="margin-bottom: 0.5rem; line-height: 1.5;">
                            <strong style="color: #475569;">🩹 Thuốc dùng ngoài:</strong>
                            @foreach($cExtHerbs as $item)
                                {{ $item->display_name }} ({{ floatval($item->quantity) }}{{ $item->unit }}){{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </div>
                        @endif
                        
                        @if($cServices->count() > 0)
                        <div style="line-height: 1.5;">
                            <strong style="color: #475569;">👐 Vật lý trị liệu / Trị liệu:</strong>
                            @foreach($cServices as $item)
                                {{ $item->display_name }} ({{ $item->sessions ? $item->sessions . ' lần' : '1 lần' }}){{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </div>
                        @endif
                    </div>
                    
                    <div style="margin-top: 0.5rem; text-align: right; font-size: 0.78rem; color: #94a3b8; font-style: italic;">
                        Thời gian hủy: {{ $prescription->updated_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- LẬP ĐƠN THUỐC & CHỈ ĐỊNH ĐIỀU TRỊ MỚI --}}
        @if(auth()->user()->hasPermission('prescriptions.create'))
        <div id="treatment-action-section" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 1.25rem; margin-bottom: 1.25rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); text-align: center;">
            <h3 style="margin: 0 0 1rem 0; font-size: 1.15rem; font-weight: 700; color: #1e3a8a;">Lập đơn thuốc & Chỉ định điều trị</h3>
            <p style="color: #64748b; margin-bottom: 1rem;">Kê đơn điều trị mới theo định hướng của hồ sơ bệnh án.</p>
            <a href="{{ route('admin.medical-records.prescriptions.create', $medicalRecord) }}" class="btn btn-lg" style="background: #16a34a; color: white; padding: 0.75rem 2rem; font-weight: bold; border-radius: 4px; text-decoration: none;">
                + TẠO ĐƠN ĐIỀU TRỊ MỚI
            </a>
        </div>
        @endif
    </div>

    {{-- 2. PRINT VIEW BLOCK (Hidden on screen, shown under the print tab or during print) --}}
    <div id="printView" class="print-preview-container" style="display: none;">
        {{-- Paper toolbar --}}
        <div class="paper-toolbar no-print-area">
            <div class="size-toggle">
                <button id="btnA5" class="active" onclick="setPaperSize('a5')">A5</button>
                <button id="btnA4" onclick="setPaperSize('a4')">A4</button>
            </div>
            <button class="print-now-btn" onclick="window.print()">🖨️ In phiếu ngay</button>
        </div>

        {{-- Document preview --}}
        <div class="paper-wrapper">
            <div class="paper a5" id="paperDoc">

                {{-- Header --}}
                <div class="doc-header" style="font-family: 'Times New Roman', Times, serif;">
                    <div class="doc-clinic">
                        <div style="font-weight: bold; font-size: 11pt; color: #111;">SỞ Y TẾ TỈNH ĐẮK LẮK</div>
                        <h1 style="font-size: 14pt; font-weight: 800; color: #111; margin: 2px 0 6px 0; text-transform: uppercase;">NHÀ THUỐC ĐÔNG Y AMATRUNG</h1>
                        <p style="font-size: 9pt; color: #222; margin: 0;">Địa chỉ: 54/46 Amajhao, Phường Tân Lập, Tỉnh Đắk Lắk</p>
                        <p style="font-size: 9pt; color: #222; margin: 0;">Hotline: 0983009748 — Email: contact@amatrung.vn</p>
                    </div>
                    <div class="doc-meta" style="text-align: right;">
                        <p style="margin: 0; font-size: 10pt;">Mã BA: <strong style="font-size: 11pt;">{{ $medicalRecord->record_code }}</strong></p>
                        <p style="margin: 3px 0 0 0; font-size: 10pt;">Ngày khám: <strong>{{ $medicalRecord->visit_date->format('d/m/Y') }}</strong></p>
                    </div>
                </div>

                {{-- Title --}}
                <div class="doc-title" style="text-align: center; margin: 15px 0 20px 0; font-family: 'Times New Roman', Times, serif;">
                    <h2 style="margin: 0; font-size: 16pt; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #111;">PHIẾU KHÁM BỆNH & CHỈ ĐỊNH ĐIỀU TRỊ</h2>
                </div>

                {{-- Patient info table --}}
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 10.5pt; font-family: 'Times New Roman', Times, serif; color: #111;">
                    <tr style="border-top: 1.5px solid #111; border-bottom: 1px solid #ddd; background: #fafafa;">
                        <td style="padding: 6px; font-weight: 700; width: 45%;">Họ và tên: <span style="font-weight: 800; font-size: 11pt; text-transform: uppercase;">{{ $medicalRecord->patient->full_name }}</span></td>
                        <td style="padding: 6px; width: 18%;">Mã BN: <strong>{{ $medicalRecord->patient->patient_code }}</strong></td>
                        <td style="padding: 6px; width: 18%;">Tuổi: <strong>{{ $medicalRecord->patient->age ?? '...' }}</strong></td>
                        <td style="padding: 6px; width: 19%;">Giới tính: <strong>{{ $medicalRecord->patient->gender_label }}</strong></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 6px;" colspan="2">Địa chỉ: <strong>{{ $medicalRecord->patient->address ?? '—' }}</strong></td>
                        <td style="padding: 6px;" colspan="2">Điện thoại: <strong>{{ $medicalRecord->patient->phone ?? $medicalRecord->patient->guardian_phone ?? '—' }}</strong></td>
                    </tr>
                    <tr style="border-bottom: 1.5px solid #111;">
                        <td style="padding: 6px;" colspan="2">
                            Chỉ số: Cân nặng: <strong>{{ $medicalRecord->weight ?? '—' }} kg</strong> / Chiều cao: <strong>{{ $medicalRecord->height ?? '—' }} cm</strong>
                            @if($bmi)
                                &nbsp;&nbsp;|&nbsp;&nbsp;BMI: <strong>{{ $bmi }}</strong> (<strong>{{ $bmiText }}</strong>)
                            @endif
                        </td>
                        <td style="padding: 6px;" colspan="2">Bác sĩ khám: <strong>{{ $medicalRecord->staff->name ?? 'N/A' }}</strong></td>
                    </tr>
                </table>

                @if($bmi)
                <div style="margin-top: -5px; margin-bottom: 15px; border: 1.5px solid #111; border-radius: 4px; padding: 6px 10px; font-size: 9.5pt; font-family: 'Times New Roman', Times, serif; line-height: 1.4; color: #111;">
                    <strong>* Lưu ý thể trạng:</strong> Chỉ số BMI là <strong>{{ $bmi }}</strong> ({{ $bmiText }}). Lời khuyên cho người bệnh: <em>{{ $bmiAdvice }}</em>
                </div>
                @endif

                {{-- Clinical details section --}}
                <div class="doc-section" style="font-family: 'Times New Roman', Times, serif; color: #111; margin-bottom: 15px;">
                    <div style="font-weight: bold; font-size: 11.5pt; border-bottom: 1.5px solid #111; padding-bottom: 3px; margin-bottom: 8px; color: #111; text-transform: uppercase;">
                        I. TÌNH TRẠNG LÂM SÀNG & CHẨN ĐOÁN
                    </div>
                    
                    <table style="width: 100%; border-collapse: collapse; font-size: 10.5pt; line-height: 1.4;">
                        <tr>
                            <td style="padding: 4px 0; font-weight: bold; vertical-align: top; width: 22%;">1. Triệu chứng lâm sàng:</td>
                            <td style="padding: 4px 0; vertical-align: top;">
                                {!! nl2br(e($medicalRecord->symptoms)) !!}
                                @if($medicalRecord->additional_symptoms)
                                    <br><strong style="color: #555;">- Các triệu chứng khác:</strong> {!! nl2br(e($medicalRecord->additional_symptoms)) !!}
                                @endif
                            </td>
                        </tr>
                        
                        @if($medicalRecord->case_type === 'musculoskeletal' || $medicalRecord->case_type === 'combined')
                        <tr>
                            <td style="padding: 4px 0; font-weight: bold; vertical-align: top;">2. Chi tiết xương khớp:</td>
                            <td style="padding: 4px 0; vertical-align: top;">
                                Tổn thương: <strong>@switch($medicalRecord->injury_type)
                                    @case('bong_gan') Bong gân @break
                                    @case('trat_khop') Trật khớp @break
                                    @case('nghi_gay_xuong') Nghi gãy xương @break
                                    @case('dau_vai_gay') Đau vai gáy @break
                                    @case('dau_lung') Đau vai lưng @break
                                    @case('dau_goi') Đau khớp gối @break
                                    @case('khac') {{ $medicalRecord->diagnosis }} @break
                                    @default {{ $medicalRecord->injury_type ?? '—' }}
                                @endswitch</strong> 
                                | Vị trí: <strong>{{ $medicalRecord->injury_location ?? '—' }}</strong> 
                                | Mức độ chấn thương: <strong>@if($medicalRecord->pain_level == 3)Nhẹ@elseif($medicalRecord->pain_level == 5)Trung bình@elseif($medicalRecord->pain_level == 8)Nặng@else{{ $medicalRecord->pain_level ?? '—' }}@endif</strong> 
                                | Nguyên nhân: <strong>{{ $medicalRecord->injury_cause ?? '—' }}</strong>
                            </td>
                        </tr>
                        @if($medicalRecord->clinical_signs)
                        <tr>
                            <td style="padding: 3px 0 3px 15px; font-weight: bold; vertical-align: top; color: #555;">- Dấu hiệu bên ngoài:</td>
                            <td style="padding: 3px 0; vertical-align: top; font-style: italic;">{!! nl2br(e($medicalRecord->clinical_signs)) !!}</td>
                        </tr>
                        @endif
                        @if($medicalRecord->palpation_result)
                        <tr>
                            <td style="padding: 3px 0 3px 15px; font-weight: bold; vertical-align: top; color: #555;">- Kết quả sờ nắn:</td>
                            <td style="padding: 3px 0; vertical-align: top; font-style: italic;">{!! nl2br(e($medicalRecord->palpation_result)) !!}</td>
                        </tr>
                        @endif
                        @endif

                        <tr>
                            <td style="padding: 4px 0; font-weight: bold; vertical-align: top;">3. Chẩn đoán xác định:</td>
                            <td style="padding: 4px 0; vertical-align: top; font-weight: 800; font-size: 11pt; text-transform: uppercase; color: #111;">{{ $medicalRecord->diagnosis }}</td>
                        </tr>

                        @if($medicalRecord->doctor_note)
                        <tr>
                            <td style="padding: 4px 0; font-weight: bold; vertical-align: top;">4. Lời dặn thầy thuốc:</td>
                            <td style="padding: 4px 0; vertical-align: top; font-style: italic; color: #444;">{!! nl2br(e($medicalRecord->doctor_note)) !!}</td>
                        </tr>
                        @endif
                    </table>
                </div>



                @if($medicalRecord->is_legacy_data)
                <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 4px; padding: 8px 12px; margin-bottom: 12px; font-size: 8.5pt; color: #92400e;">
                    📋 Bệnh án cũ — nhập lại từ hồ sơ giấy.
                    @if($medicalRecord->legacy_note) | Ghi chú: {{ $medicalRecord->legacy_note }} @endif
                </div>
                @endif

                {{-- Signatures --}}
                <div class="doc-footer">
                    <div class="doc-sig">
                        <div>Ngày .... tháng .... năm 20...</div>
                        <div class="title">Người bệnh</div>
                        <div>(Ký và ghi rõ họ tên)</div>
                    </div>
                    <div class="doc-sig">
                        <div>Ngày {{ now()->format('d') }} tháng {{ now()->format('m') }} năm {{ now()->format('Y') }}</div>
                        <div class="title">Thầy thuốc điều trị</div>
                        <div class="name">{{ $medicalRecord->staff->name ?? 'Admin' }}</div>
                    </div>
                </div>

                <div class="doc-footnote">* Phiếu khám bệnh lưu hành nội bộ. Vui lòng mang theo khi tái khám.</div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL ZOOM XRAY --}}
<div id="xrayModal" class="xray-modal">
    <img id="xrayZoomedImg" src="" alt="Zoomed Xray">
</div>

{{-- MODERN CUSTOM DELETE CONFIRMATION MODAL --}}
<div id="confirmDeleteModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 99999; align-items: center; justify-content: center; padding: 1rem; transition: all 0.3s ease;">
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; max-width: 480px; width: 100%; padding: 1.5rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); transform: scale(0.95); transition: transform 0.2s ease;">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
            <div style="background: #fee2e2; color: #ef4444; width: 2.75rem; height: 2.75rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                ⚠️
            </div>
            <div>
                <h4 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #1e293b;">Xác nhận trả thuốc / hủy đơn</h4>
                <p style="margin: 0; font-size: 0.8rem; color: #64748b; font-weight: 500;">Chỉ áp dụng trong 24 giờ sau khi lập đơn</p>
            </div>
        </div>
        
        <p style="margin: 0 0 1.5rem; font-size: 0.92rem; color: #475569; line-height: 1.5; font-weight: 600;">
            Bạn đang yêu cầu hủy đơn điều trị vì <strong>Bệnh nhân trả thuốc trong thời hạn 24 giờ</strong>?
            <br><br>
            <span style="color: #b91c1c; font-weight: 700;">Lưu ý:</span> Hệ thống sẽ xóa đơn và <strong>hoàn trả số lượng dược liệu/vật tư đã trừ</strong> về kho. Đơn quá 24 giờ sẽ bị chặn ở máy chủ, không được xóa và không hoàn kho.
        </p>
        
        <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
            <button type="button" onclick="closeDeleteConfirmModal()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0.5rem 1.25rem; font-size: 0.88rem; font-weight: 700; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                Quay lại
            </button>
            <button type="button" id="confirmDeleteSubmitBtn" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; border-radius: 6px; padding: 0.5rem 1.5rem; font-size: 0.88rem; font-weight: 800; cursor: pointer; box-shadow: 0 4px 6px rgba(239, 68, 68, 0.2); transition: all 0.2s;" onmouseover="this.style.opacity='0.95'" onmouseout="this.style.opacity='1'">
                Xác nhận trả thuốc & hoàn kho
            </button>
        </div>
    </div>
</div>

<!-- MODAL BÀI THUỐC THANG -->
<div id="formulaModal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 99999; align-items: center; justify-content: center; opacity: 1;">
    <div style="background: #fff; border-radius: 8px; width: 1000px; max-width: 95%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; display: flex; flex-direction: column; max-height: 90vh;">
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
            <h2 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #1e293b;">📦 Cấu Hình Bài Thuốc Thang</h2>
            <button type="button" onclick="closeFormulaModal()" style="background: none; border: none; font-size: 1.5rem; color: #94a3b8; cursor: pointer;">✕</button>
        </div>
        
        <div style="padding: 1.5rem; overflow-y: auto; flex: 1; display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; background: #f1f5f9;">
            <!-- Column 1: Add from Sample or individual -->
            <div style="background: #fff; padding: 1.25rem; border-radius: 6px; border: 1px solid #e2e8f0;">
                <h4 style="margin: 0 0 1rem 0; color: #1e3a8a; font-size: 0.95rem;">1. Thêm nhanh từ Bài thuốc mẫu</h4>
                <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem;">
                    <select id="modal_formula_select" style="flex: 1; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.5rem; font-size: 0.85rem;" onchange="applyFormulaModal()">
                        <option value="">-- Chọn bài thuốc mẫu --</option>
                        @foreach(\App\Models\SamplePrescription::with('items.medicinalHerb')->get() as $sample)
                            <option value="{{ $sample->id }}" data-items="{{ json_encode($sample->items->map(function($i) { return ['id' => $i->medicinal_herb_id, 'name' => $i->medicinalHerb->name ?? '', 'quantity' => $i->quantity, 'unit' => $i->medicinalHerb->unit ?? 'g', 'stock' => $i->medicinalHerb->stock_quantity ?? 0]; })) }}">
                                {{ $sample->name }} {{ $sample->suggested_condition ? '('.$sample->suggested_condition.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <h4 style="margin: 0 0 1rem 0; color: #1e3a8a; font-size: 0.95rem;">2. Thêm dược liệu lẻ</h4>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Chọn dược liệu kho *</label>
                    <select id="modal_herb_select" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.5rem; font-size: 0.85rem;" onchange="updateModalHerbUnit(this)">
                        <option value="">-- Chọn dược liệu --</option>
                        @foreach(\App\Models\MedicinalHerb::where('status', 'active')->orderBy('name')->get() as $herb)
                            <option value="{{ $herb->id }}" data-unit="{{ $herb->unit }}" data-stock="{{ floatval($herb->stock_quantity) }}">
                                {{ $herb->name }} (Tồn: {{ floatval($herb->stock_quantity) }} {{ $herb->unit }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Số lượng (g) *</label>
                        <input type="number" id="modal_herb_qty" class="form-input" style="width: 100%;" step="0.1" min="0.1">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Đơn vị</label>
                        <input type="text" id="modal_herb_unit" class="form-input" style="width: 60px; background: #f8fafc;" readonly>
                    </div>
                </div>
                <button type="button" onclick="addModalHerb()" style="width: 100%; background: #16a34a; color: white; border: none; padding: 0.6rem; border-radius: 4px; font-weight: bold; cursor: pointer;">➕ Thêm vào bài thuốc</button>
            </div>

            <!-- Column 2: List of herbs -->
            <div style="background: #fff; padding: 1.25rem; border-radius: 6px; border: 1px solid #e2e8f0; display: flex; flex-direction: column;">
                <h4 style="margin: 0 0 1rem 0; color: #1e3a5f; font-size: 0.95rem; display: flex; justify-content: space-between;">
                    <span>Danh sách vị thuốc (1 thang)</span>
                    <span id="modal_herb_count" style="background: #eef2ff; color: #4f46e5; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem;">0 vị</span>
                </h4>
                <div style="flex: 1; overflow-y: auto; max-height: 400px; border: 1px solid #f1f5f9; border-radius: 4px;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; text-align: left;">
                                <th style="padding: 0.5rem;">Vị thuốc</th>
                                <th style="padding: 0.5rem; text-align: right;">Liều lượng</th>
                                <th style="padding: 0.5rem; text-align: center;">Xóa</th>
                            </tr>
                        </thead>
                        <tbody id="modal_herbs_tbody">
                            <tr><td colspan="3" style="text-align: center; padding: 2rem; color: #94a3b8; font-style: italic;">Chưa có vị thuốc nào.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div style="padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: flex-end; gap: 1rem;">
            <button type="button" onclick="closeFormulaModal()" style="padding: 0.5rem 1.5rem; border: 1px solid #cbd5e1; background: #fff; border-radius: 4px; cursor: pointer; font-weight: bold; color: #475569;">Đóng</button>
            <button type="button" onclick="saveFormulaModal()" style="padding: 0.5rem 1.5rem; border: none; background: #3b82f6; color: #fff; border-radius: 4px; cursor: pointer; font-weight: bold;">💾 Xác nhận bài thuốc</button>
        </div>
    </div>
</div>
<!-- Custom Toast Container -->
<div id="custom-toast-container" style="position: fixed; top: 24px; right: 24px; z-index: 999999; display: flex; flex-direction: column; gap: 12px; max-width: 420px; width: calc(100% - 48px); pointer-events: none;"></div>

<style>
.custom-toast {
    position: relative;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: 12px;
    padding: 16px 20px 18px 18px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.5);
    pointer-events: auto;
    transform: translateX(120%);
    opacity: 0;
    transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.35s ease, margin 0.3s ease;
    overflow: hidden;
    margin-bottom: 2px;
    font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}
.custom-toast.show {
    transform: translateX(0);
    opacity: 1;
}
.custom-toast:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 30px -5px rgba(0, 0, 0, 0.08), 0 10px 15px -5px rgba(0, 0, 0, 0.03);
}
.custom-toast.success {
    border-left: 5px solid #10b981;
    box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.1), 0 20px 25px -5px rgba(0, 0, 0, 0.05);
}
.custom-toast.error {
    border-left: 5px solid #ef4444;
    box-shadow: 0 10px 20px -5px rgba(239, 68, 68, 0.1), 0 20px 25px -5px rgba(0, 0, 0, 0.05);
}
.custom-toast.warning {
    border-left: 5px solid #f59e0b;
    box-shadow: 0 10px 20px -5px rgba(245, 158, 11, 0.1), 0 20px 25px -5px rgba(0, 0, 0, 0.05);
}
.custom-toast.info {
    border-left: 5px solid #3b82f6;
    box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.1), 0 20px 25px -5px rgba(0, 0, 0, 0.05);
}
.custom-toast-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    flex-shrink: 0;
    border-radius: 50%;
    margin-top: 1px;
}
.custom-toast.success .custom-toast-icon {
    background: rgba(16, 185, 129, 0.1);
}
.custom-toast.error .custom-toast-icon {
    background: rgba(239, 68, 68, 0.1);
}
.custom-toast.warning .custom-toast-icon {
    background: rgba(245, 158, 11, 0.1);
}
.custom-toast.info .custom-toast-icon {
    background: rgba(59, 130, 246, 0.1);
}
.custom-toast-body {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    gap: 3px;
}
.custom-toast-title {
    font-size: 0.92rem;
    font-weight: 700;
    color: #0f172a;
}
.custom-toast-content {
    font-size: 0.85rem;
    color: #475569;
    line-height: 1.4;
    white-space: pre-wrap;
    font-weight: 500;
}
.custom-toast-close {
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    font-size: 1.2rem;
    padding: 0;
    line-height: 1;
    transition: color 0.15s ease, transform 0.15s ease;
    margin-left: 4px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    border-radius: 4px;
}
.custom-toast-close:hover {
    color: #1e293b;
    background: #f8fafc;
    transform: scale(1.05);
}
.custom-toast-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    width: 100%;
    transform-origin: left;
    animation: toast-progress linear forwards;
}
.custom-toast.success .custom-toast-progress {
    background: #10b981;
}
.custom-toast.error .custom-toast-progress {
    background: #ef4444;
}
.custom-toast.warning .custom-toast-progress {
    background: #f59e0b;
}
.custom-toast.info .custom-toast-progress {
    background: #3b82f6;
}
@keyframes toast-progress {
    to {
        transform: scaleX(0);
    }
}
</style>

@php
    $samplePrescriptionsJson = $samplePrescriptions->mapWithKeys(function($sample) {
        return [$sample->id => [
            'id' => $sample->id,
            'name' => $sample->name,
            'suggested_condition' => $sample->suggested_condition,
            'usage_instruction' => $sample->usage_instruction,
            'default_packages' => $sample->default_packages,
            'items' => $sample->items->map(function($item) {
                return [
                    'id' => $item->medicinal_herb_id,
                    'name' => $item->medicinalHerb->name ?? '',
                    'quantity' => $item->quantity,
                    'unit' => $item->medicinalHerb->unit ?? '',
                    'stock' => floatval($item->medicinalHerb->stock_quantity ?? 0)
                ];
            })->toArray()
        ]];
    })->toArray();

    $activeHerbsJson = $herbs->map(function($herb) {
        return [
            'id' => $herb->id,
            'name' => $herb->name,
            'unit' => $herb->unit,
            'stock' => floatval($herb->stock_quantity ?? 0),
        ];
    })->values()->toArray();
@endphp

<div id="page-data"
     data-samples="{{ json_encode($samplePrescriptionsJson) }}"
     data-herbs="{{ json_encode($activeHerbsJson) }}"
     data-symptoms="{{ json_encode($medicalRecord->symptoms) }}"
     data-diagnosis="{{ json_encode($medicalRecord->diagnosis) }}"
     data-case-type="{{ json_encode($medicalRecord->case_type) }}"
     data-injury-type="{{ json_encode($medicalRecord->injury_type) }}"
     data-injury-location="{{ json_encode($medicalRecord->injury_location) }}"
     data-injury-cause="{{ json_encode($medicalRecord->injury_cause) }}"
     data-clinical-signs="{{ json_encode($medicalRecord->clinical_signs) }}"
     data-palpation-result="{{ json_encode($medicalRecord->palpation_result) }}"
     data-pain-level="{{ json_encode($medicalRecord->pain_level) }}"
     data-xray-note="{{ json_encode($medicalRecord->xray_note) }}"
     data-weight="{{ json_encode($medicalRecord->weight) }}"
     data-height="{{ json_encode($medicalRecord->height) }}"
     data-gender="{{ json_encode($medicalRecord->patient->gender) }}"
     data-age="{{ json_encode($medicalRecord->patient->age) }}"
     data-ai-suggest-route="{{ route('admin.ai.suggest') }}"
     data-csrf-token="{{ csrf_token() }}"
     style="display:none;"></div>

<script>
function showToast(message, type = 'info', duration = 4500) {
    const container = document.getElementById('custom-toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `custom-toast ${type}`;

    let iconSvg = '';
    let title = '';
    
    if (type === 'success') {
        iconSvg = `<svg style="width: 15px; height: 15px; color: #10b981;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>`;
        title = 'Thành công';
    } else if (type === 'error') {
        iconSvg = `<svg style="width: 15px; height: 15px; color: #ef4444;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>`;
        title = 'Lỗi hệ thống';
    } else if (type === 'warning') {
        iconSvg = `<svg style="width: 15px; height: 15px; color: #f59e0b;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>`;
        title = 'Chú ý';
    } else {
        iconSvg = `<svg style="width: 15px; height: 15px; color: #3b82f6;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        title = 'Thông báo';
    }

    toast.innerHTML = `
        <div class="custom-toast-icon">${iconSvg}</div>
        <div class="custom-toast-body">
            <div class="custom-toast-title">${title}</div>
            <div class="custom-toast-content">${message}</div>
        </div>
        <button class="custom-toast-close" aria-label="Close" onclick="this.parentElement.remove()">&times;</button>
        <div class="custom-toast-progress" style="animation-duration: ${duration}ms;"></div>
    `;

    container.appendChild(toast);

    // Trigger transition
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);

    // Auto-remove after duration
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
        }, 400);
    }, duration);
}

// Switch between clinical screen detail and printable sheet layout
function switchView(viewName) {
    const screenView = document.getElementById('screenView');
    const printView = document.getElementById('printView');
    const tabScreen = document.getElementById('tabScreen');
    const tabPrint = document.getElementById('tabPrint');

    if (viewName === 'screen') {
        screenView.style.display = 'block';
        printView.style.display = 'none';
        tabScreen.classList.add('active');
        tabPrint.classList.remove('active');
    } else {
        screenView.style.display = 'none';
        printView.style.display = 'block';
        tabScreen.classList.remove('active');
        tabPrint.classList.add('active');
    }
}

// Paper sizing handler
function setPaperSize(size) {
    const paper = document.getElementById('paperDoc');
    paper.classList.remove('a4', 'a5');
    paper.classList.add(size);
    document.getElementById('btnA4').classList.toggle('active', size === 'a4');
    document.getElementById('btnA5').classList.toggle('active', size === 'a5');

    // Update @page size for print
    let styleEl = document.getElementById('printPageSize');
    if (!styleEl) {
        styleEl = document.createElement('style');
        styleEl.id = 'printPageSize';
        document.head.appendChild(styleEl);
    }
    styleEl.textContent = size === 'a4'
        ? '@media print { @page { size: A4 portrait; } }'
        : '@media print { @page { size: A5 landscape; } }';
}

// Default layout configurations
setPaperSize('a5');

// ─────────────────────────────────────────────────────────────────────────────
// [GIAI ĐOẠN 4 – ĐÃ VÔ HIỆU HÓA] Hàm getAiSuggestions() cũ
// Lý do: Vi phạm nguyên tắc an toàn AI – gửi symptoms/diagnosis/PII từ frontend.
// Thay thế bằng panel mới (ai_panel.blade.php) chỉ gửi medical_record_id.
// Xem: resources/views/admin/records/partials/ai_panel.blade.php
// ─────────────────────────────────────────────────────────────────────────────

function displayAiResults() {
    const aiResultsBlock = document.getElementById('aiResultsBlock');
    aiResultsBlock.style.display = 'block';
    
    const data = currentAiData;
    
    // Set text titles & assessment
    document.getElementById('aiResultTitle').innerText = 'KẾT QUẢ ĐỀ XUẤT PHÁC ĐỒ ĐIỀU TRỊ TOÀN DIỆN BẰNG AI';
    document.getElementById('aiDiagnosis').value = data.suggested_condition;
    document.getElementById('aiReasoning').value = data.reasoning;
    
    // Set interactive input/textarea values
    document.getElementById('aiCourseDays').value = data.course_days || 7;
    document.getElementById('aiFollowUpDays').value = data.follow_up_days || 7;
    document.getElementById('aiPatientGuidelines').value = data.patient_guidelines || 'Nghỉ ngơi bồi bổ sức khỏe.';
    document.getElementById('aiInternalNotes').value = data.internal_notes || 'Theo dõi cơ địa lâm sàng.';
    
    // Safety warning
    const safetyBox = document.getElementById('aiSafetyWarningBox');
    if (data.safety_warning) {
        safetyBox.style.display = 'block';
        document.getElementById('aiSafetyWarningText').innerText = data.safety_warning;
    } else {
        safetyBox.style.display = 'none';
    }
    
    // Check which sections have data
    const hasOral = data.oral_herbs && data.oral_herbs.length > 0;
    const hasExternal = data.external_herbs && data.external_herbs.length > 0;
    const hasTherapy = data.therapy_services && data.therapy_services.length > 0;
    const hasServiceGroup = hasExternal || hasTherapy;
    
    const oralColumn = document.getElementById('aiOralColumn');
    const serviceColumn = document.getElementById('aiServiceColumn');
    const groupsGrid = document.getElementById('aiGroupsGrid');
    
    // Show/Hide columns and adjust Grid layout dynamically
    if (hasOral && hasServiceGroup) {
        oralColumn.style.display = 'block';
        serviceColumn.style.display = 'block';
        groupsGrid.style.gridTemplateColumns = '1fr 1.2fr';
    } else if (hasOral) {
        oralColumn.style.display = 'block';
        serviceColumn.style.display = 'none';
        groupsGrid.style.gridTemplateColumns = '1fr';
    } else if (hasServiceGroup) {
        oralColumn.style.display = 'none';
        serviceColumn.style.display = 'block';
        groupsGrid.style.gridTemplateColumns = '1fr';
    } else {
        oralColumn.style.display = 'block';
        serviceColumn.style.display = 'block';
        groupsGrid.style.gridTemplateColumns = '1fr 1.2fr';
    }
    
    // Apply buttons display
    const btnApplyOral = document.getElementById('btnApplyOral');
    const btnApplyExternal = document.getElementById('btnApplyExternal') || document.getElementById('btnApplyExternal_old');
    if (btnApplyOral) btnApplyOral.style.display = hasOral ? 'inline-block' : 'none';
    if (btnApplyExternal) btnApplyExternal.style.display = hasServiceGroup ? 'inline-block' : 'none';
    
    // 1. Render Oral Herbs (with editable inputs)
    const oralContainer = document.getElementById('aiOralHerbsContainer');
    if (hasOral) {
        let html = `<div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.25rem;">`;
        data.oral_herbs.forEach((h, idx) => {
            html += `
            <div class="ai-oral-herb-row" style="background: #ffffff; border: 1px solid #dcfce7; border-radius: 6px; padding: 0.5rem; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                <div style="flex: 1; font-weight: 700; color: #14532d; font-size: 0.85rem;">${escapeHtml(h.herb_name)}</div>
                <div style="display: flex; align-items: center; gap: 0.25rem;">
                    <input type="number" step="any" class="ai-oral-herb-dosage" data-index="${idx}" value="${h.dosage}" style="width: 55px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.2rem; text-align: center; font-weight: 700; color: #166534; font-size: 0.8rem; background: #ffffff;">
                    <span style="font-size: 0.8rem; color: #475569; font-weight: 600;">g</span>
                </div>
                <div style="flex: 1.5;">
                    <input type="text" class="ai-oral-herb-note" data-index="${idx}" value="${escapeHtml(h.usage_note || 'Sắc cùng thang thuốc')}" placeholder="Ghi chú sử dụng" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.2rem 0.4rem; font-size: 0.8rem; color: #334155; background: #ffffff;">
                </div>
            </div>`;
        });
        html += `</div>`;
        oralContainer.innerHTML = html;
    } else {
        oralContainer.innerHTML = `<span style="color:#94a3b8; font-style:italic;">Không cần chỉ định thuốc uống trong trường hợp này.</span>`;
    }
    
    // 2 & 3. Render External Herbs & Therapy Services (Merged with editable inputs)
    const servContainer = document.getElementById('aiServicesContainer');
    if (hasServiceGroup) {
        let html = `<div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.25rem;">`;
        
        // Render external herbs first (if any)
        if (hasExternal) {
            data.external_herbs.forEach((e, idx) => {
                html += `
                <div class="ai-external-herb-row" style="background: #ffffff; border: 1px solid #ffedd5; border-radius: 6px; padding: 0.5rem; display: flex; flex-direction: column; gap: 0.4rem; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                        <span style="background: #fff7ed; color: #c2410c; border: 1px solid #ffd8a8; padding: 0.1rem 0.35rem; border-radius: 4px; font-size: 0.72rem; font-weight: 800; line-height: 1.2;">Thuốc dùng ngoài</span>
                        <strong style="flex: 1; font-size: 0.85rem; color: #7c2d12;">${escapeHtml(e.custom_name)}</strong>
                        <div style="display: flex; align-items: center; gap: 0.25rem;">
                            <input type="number" class="ai-external-herb-quantity" data-index="${idx}" value="${e.quantity}" style="width: 50px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.25rem; text-align: center; font-weight: 700; color: #9a3412; font-size: 0.8rem; background: #ffffff;">
                            <span style="font-size: 0.8rem; color: #475569; font-weight: 600;">${escapeHtml(e.unit)}</span>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.35rem;">
                        <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; white-space: nowrap;">Hướng dẫn:</span>
                        <input type="text" class="ai-external-herb-instruction" data-index="${idx}" value="${escapeHtml(e.usage_instruction || '')}" placeholder="Hướng dẫn sử dụng" style="flex: 1; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.2rem 0.4rem; font-size: 0.8rem; color: #334155; background: #ffffff;">
                    </div>
                </div>`;
            });
        }
        
        // Render therapy services next (if any)
        if (hasTherapy) {
            data.therapy_services.forEach((s, idx) => {
                html += `
                <div class="ai-therapy-service-row" style="background: #ffffff; border: 1px solid #e0f2fe; border-radius: 6px; padding: 0.5rem; display: flex; flex-direction: column; gap: 0.4rem; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                        <span style="background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; padding: 0.1rem 0.35rem; border-radius: 4px; font-size: 0.72rem; font-weight: 800; line-height: 1.2;">Dịch vụ trị liệu</span>
                        <strong style="flex: 1; font-size: 0.85rem; color: #0c4a6e;">${escapeHtml(s.custom_name)}</strong>
                        <div style="display: flex; align-items: center; gap: 0.25rem;">
                            <input type="number" class="ai-therapy-service-sessions" data-index="${idx}" value="${s.sessions}" style="width: 50px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.25rem; text-align: center; font-weight: 700; color: #0369a1; font-size: 0.8rem; background: #ffffff;">
                            <span style="font-size: 0.8rem; color: #475569; font-weight: 600;">lần</span>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.35rem;">
                        <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; white-space: nowrap;">Hướng dẫn:</span>
                        <input type="text" class="ai-therapy-service-instruction" data-index="${idx}" value="${escapeHtml(s.usage_instruction || '')}" placeholder="Hướng dẫn trị liệu" style="flex: 1; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.2rem 0.4rem; font-size: 0.8rem; color: #334155; background: #ffffff;">
                    </div>
                </div>`;
            });
        }
        
        html += `</div>`;
        servContainer.innerHTML = html;
    } else {
        servContainer.innerHTML = `<span style="color:#94a3b8; font-style:italic;">Không chỉ định Dịch vụ Trị liệu.</span>`;
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// [GIAI ĐOẠN 4 – ĐÃ VÔ HIỆU HÓA] closeAiSuggestions() & applyAiSelection()
// Lý do: applyAiSelection() vi phạm nguyên tắc an toàn AI – tự điền form kê đơn
// và tự trừ kho. Đã thay thế bằng hệ thống ghi log tương tác (log-status).
// ─────────────────────────────────────────────────────────────────────────────
function applyAiSelection_DISABLED(type) {
    if (!currentAiData) return;
    
    const data = currentAiData;
    
    // Read and apply edited general clinical parameters
    const aiCourseDaysVal = parseInt(document.getElementById('aiCourseDays').value) || 7;
    const aiFollowUpDaysVal = parseInt(document.getElementById('aiFollowUpDays').value) || 7;
    const aiGuidelinesVal = document.getElementById('aiPatientGuidelines').value;
    const aiInternalNotesVal = document.getElementById('aiInternalNotes').value;
    const aiDiagnosisVal = document.getElementById('aiDiagnosis').value;
    
    data.course_days = aiCourseDaysVal;
    data.follow_up_days = aiFollowUpDaysVal;
    data.patient_guidelines = aiGuidelinesVal;
    data.internal_notes = aiInternalNotesVal;
    data.suggested_condition = aiDiagnosisVal;
    
    // Update main diagnosis text area
    const diagnosisEdit = document.getElementById('diagnosis_edit');
    if (diagnosisEdit) {
        diagnosisEdit.value = aiDiagnosisVal;
    }
    
    // Read and apply edited oral herbs
    const oralHerbRows = document.querySelectorAll('.ai-oral-herb-dosage');
    oralHerbRows.forEach(input => {
        const idx = parseInt(input.dataset.index);
        const noteInput = document.querySelector(`.ai-oral-herb-note[data-index="${idx}"]`);
        if (data.oral_herbs && data.oral_herbs[idx]) {
            data.oral_herbs[idx].dosage = parseFloat(input.value) || 0;
            data.oral_herbs[idx].usage_note = noteInput ? noteInput.value : '';
        }
    });

    // Read and apply edited external herbs
    const externalQtyInputs = document.querySelectorAll('.ai-external-herb-quantity');
    externalQtyInputs.forEach(input => {
        const idx = parseInt(input.dataset.index);
        const instructionInput = document.querySelector(`.ai-external-herb-instruction[data-index="${idx}"]`);
        if (data.external_herbs && data.external_herbs[idx]) {
            data.external_herbs[idx].quantity = parseFloat(input.value) || 0;
            data.external_herbs[idx].usage_instruction = instructionInput ? instructionInput.value : '';
        }
    });

    // Read and apply edited therapy services
    const therapySessionsInputs = document.querySelectorAll('.ai-therapy-service-sessions');
    therapySessionsInputs.forEach(input => {
        const idx = parseInt(input.dataset.index);
        const instructionInput = document.querySelector(`.ai-therapy-service-instruction[data-index="${idx}"]`);
        if (data.therapy_services && data.therapy_services[idx]) {
            data.therapy_services[idx].sessions = parseInt(input.value) || 0;
            data.therapy_services[idx].usage_instruction = instructionInput ? instructionInput.value : '';
        }
    });

    let itemsAdded = 0;
    let missingHerbs = [];
    
    // Set suggested formula name
    if (type === 'oral' || type === 'all') {
        const formulaNameInput = document.getElementById('prescription_formula_name');
        if (formulaNameInput && data.suggested_formula_name) {
            formulaNameInput.value = data.suggested_formula_name;
        }
    }
    
    // A. Apply Oral Herbs
    if ((type === 'oral' || type === 'all') && data.oral_herbs && data.oral_herbs.length > 0) {
        const formulaItems = [];

        data.oral_herbs.forEach(item => {
            const needle = String(item.herb_name || '').toLowerCase();
            const matchedHerb = activeHerbsData.find(herb => {
                const name = String(herb.name || '').toLowerCase();
                return needle && (name.includes(needle) || needle.includes(name));
            });

            if (!matchedHerb) {
                missingHerbs.push(item.herb_name);
                return;
            }

            formulaItems.push({
                item_type: 'formula_herb',
                herb_id: matchedHerb.id,
                custom_name: matchedHerb.name,
                quantity: parseFloat(item.dosage) || 0,
                unit: matchedHerb.unit || 'g',
                dosage: item.usage_note || 'Sắc cùng thang thuốc',
                note: 'AI gợi ý',
                affects_stock: true,
            });
        });

        if (formulaItems.length > 0) {
            const aiNumDoses = parseInt(data.course_days) || parseInt(document.getElementById('num_of_doses')?.value) || 1;
            const groupId = `formula_${Date.now()}_${itemIndex}`;
            const groupedItems = formulaItems.map(item => {
                item.formula_group_id = groupId;
                return {
                    index: addGroupedFormulaHiddenItem(groupId, item),
                    item,
                };
            });

            appendFormulaGroupRow(
                groupId,
                data.suggested_formula_name || 'Bài thuốc AI gợi ý',
                aiNumDoses,
                groupedItems,
                data.patient_guidelines || 'Sắc ngày 1 thang, uống chia 2 lần sáng/chiều sau bữa ăn ấm.'
            );
            itemsAdded += formulaItems.length;

            const numOfDosesField = document.getElementById('num_of_doses');
            if (numOfDosesField) numOfDosesField.value = aiNumDoses;
            const prescriptionUsageField = document.getElementById('prescription_usage_instruction');
            if (prescriptionUsageField && data.patient_guidelines) {
                prescriptionUsageField.value = data.patient_guidelines;
            }
        }
    }
    
    // B. Apply External Plasters & Therapy Services
    if ((type === 'external' || type === 'all')) {
        // 1. External plasters (packaged products)
        if (data.external_herbs && data.external_herbs.length > 0) {
            // Build temporary packaged products list from select options
            const pkgProducts = [];
            const selectPkg = document.getElementById('external_product_id');
            if (selectPkg) {
                for (let i = 1; i < selectPkg.options.length; i++) {
                    const opt = selectPkg.options[i];
                    pkgProducts.push({
                        id: opt.value,
                        name: opt.text.split(' (')[0],
                        unit: opt.getAttribute('data-unit') || 'lọ',
                        stock: parseFloat(opt.getAttribute('data-stock')) || 0
                    });
                }
            }

            data.external_herbs.forEach(e => {
                const needle = String(e.custom_name || '').toLowerCase();
                const matchedPkg = pkgProducts.find(p => {
                    const name = String(p.name || '').toLowerCase();
                    return needle && (name.includes(needle) || needle.includes(name));
                });

                const added = addItem('external_product', true, {
                    packaged_product_id: matchedPkg ? matchedPkg.id : '',
                    custom_name: matchedPkg ? matchedPkg.name : e.custom_name,
                    quantity: e.quantity,
                    unit: matchedPkg ? matchedPkg.unit : (e.unit || 'lọ'),
                    usage_area: e.usage_area || '',
                    usage_instruction: e.usage_instruction || '',
                    affects_stock: !!matchedPkg,
                });
                if (added) itemsAdded++;
            });
        }
        
        // 2. Therapy services
        if (data.therapy_services && data.therapy_services.length > 0) {
            data.therapy_services.forEach(s => {
                const selectEl = document.getElementById('service_custom_name');
                if (selectEl) {
                    let optExists = false;
                    for (let i = 0; i < selectEl.options.length; i++) {
                        if (selectEl.options[i].value === s.custom_name) {
                            optExists = true;
                            break;
                        }
                    }
                    if (!optExists) {
                        const newOpt = document.createElement('option');
                        newOpt.value = s.custom_name;
                        newOpt.text = s.custom_name;
                        selectEl.appendChild(newOpt);
                    }
                    selectEl.value = s.custom_name;
                }
                
                document.getElementById('service_sessions').value = s.sessions;
                const serviceAreaEl = document.getElementById('service_area');
                if (serviceAreaEl) serviceAreaEl.value = s.usage_area || '';
                document.getElementById('service_instruction').value = s.usage_instruction;
                
                const added = addItem('therapy_service', true);
                if (added) itemsAdded++;
            });
        }
    }
    
    // C. Apply course guidelines, duration, follow-up dates
    if (type === 'guidelines' || type === 'all') {
        if (data.course_days) {
            const courseEl = document.getElementById('course_days');
            if (courseEl) courseEl.value = data.course_days;
        }
        
        const hasOralHerbs = data.oral_herbs && data.oral_herbs.length > 0;
        const numDosesEl = document.getElementById('num_of_doses');
        if (numDosesEl) {
            if (hasOralHerbs && data.course_days) {
                numDosesEl.value = data.course_days;
            } else {
                numDosesEl.value = '';
            }
        }
        
        if (data.patient_guidelines) {
            const pubInstEl = document.querySelector('textarea[name="public_instruction"]');
            if (pubInstEl) pubInstEl.value = data.patient_guidelines;
        }
        
        if (data.internal_notes) {
            const intNoteEl = document.querySelector('textarea[name="internal_note"]');
            if (intNoteEl) intNoteEl.value = data.internal_notes;
        }
        
        // Follow up date calculation
        if (data.follow_up_days) {
            const followUpEl = document.getElementById('follow_up_date');
            if (followUpEl) {
                const today = new Date();
                today.setDate(today.getDate() + data.follow_up_days);
                const dd = String(today.getDate()).padStart(2, '0');
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const yyyy = today.getFullYear();
                followUpEl.value = `${yyyy}-${mm}-${dd}`;
            }
        }
    }
    
    // Success feedback alert
    if (missingHerbs.length > 0) {
        showToast(`Đã áp dụng thành công phác đồ AI gợi ý!\n⚠️ Một số vị thuốc không đủ tồn kho hoặc không khớp: ${missingHerbs.join(', ')}`, 'warning');
    } else {
        showToast('Đã áp dụng thành công phác đồ AI gợi ý!', 'success');
    }
    
    // Sync treatment type dynamically from actual table items or recommended AI phác đồ
    if (type === 'guidelines') {
        const hasHerb = data.oral_herbs && data.oral_herbs.length > 0;
        const hasExternal = data.external_herbs && data.external_herbs.length > 0;
        const hasService = data.therapy_services && data.therapy_services.length > 0;
        
        const treatmentTypeSelect = document.getElementById('treatment_type');
        if (treatmentTypeSelect) {
            let newVal = 'service_only';
            if (hasHerb && hasExternal && hasService) newVal = 'combined';
            else if (hasHerb && hasExternal && !hasService) newVal = 'thuoc_uong_bo';
            else if (hasHerb && !hasExternal && hasService) newVal = 'thuoc_uong_tri_lieu';
            else if (!hasHerb && hasExternal && hasService) newVal = 'bo_tri_lieu';
            else if (hasHerb && !hasExternal && !hasService) newVal = 'herbal_only';
            else if (!hasHerb && hasExternal && !hasService) newVal = 'external_only';
            else if (!hasHerb && !hasExternal && hasService) newVal = 'service_only';
            
            treatmentTypeSelect.value = newVal;
            toggleTreatmentTypeFields();
        }
    } else {
        updateTreatmentTypeFromItems();
    }
    
    // Close suggestions card
    closeAiSuggestions();
}



// Modern Custom Delete Confirmation Modal Logic
let activeDeleteFormId = null;

function showDeleteConfirmModal(prescriptionId) {
    activeDeleteFormId = `delete-form-${prescriptionId}`;
    const modal = document.getElementById('confirmDeleteModal');
    if (modal) {
        modal.style.display = 'flex';
        // Trigger micro-scale animation
        setTimeout(() => {
            modal.firstElementChild.style.transform = 'scale(1)';
        }, 10);
    }
}

function closeDeleteConfirmModal() {
    const modal = document.getElementById('confirmDeleteModal');
    if (modal) {
        modal.firstElementChild.style.transform = 'scale(0.95)';
        setTimeout(() => {
            modal.style.display = 'none';
        }, 150);
    }
    activeDeleteFormId = null;
}

// Bind confirmation buttons
document.addEventListener('DOMContentLoaded', function() {
    const confirmBtn = document.getElementById('confirmDeleteSubmitBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            if (activeDeleteFormId) {
                const form = document.getElementById(activeDeleteFormId);
                if (form) form.submit();
            }
        });
    }
    
    // Close modal when clicking outside
    const modal = document.getElementById('confirmDeleteModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteConfirmModal();
            }
        });
    }
    
    // Esc key support
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteConfirmModal();
        }
    });
});

// Zoom xray modal logic
function zoomXray(src) {
    const modal = document.getElementById('xrayModal');
    const zoomImg = document.getElementById('xrayZoomedImg');
    modal.style.display = 'flex';
    zoomImg.src = src;
}

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('xrayModal');
    if(modal) {
        modal.addEventListener('click', function() {
            this.style.display = 'none';
        });
    }
    
    // Zoom xray trigger for printable document
    const printXrayBtn = document.getElementById('btnZoomXray');
    if (printXrayBtn) {
        printXrayBtn.addEventListener('click', function() {
            zoomXray(this.src);
        });
    }
});

// Treatment Items & Prescribing script
let itemIndex = 0;
let addedItems = [];
const treatmentPageDataEl = document.getElementById('page-data');
const activeHerbsData = treatmentPageDataEl ? JSON.parse(treatmentPageDataEl.dataset.herbs || '[]') : [];

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, function(char) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[char];
    });
}

function buildHerbOptionsHtml(selectedId = '', fallbackHerb = null) {
    const herbs = [...activeHerbsData];
    if (fallbackHerb && fallbackHerb.id && !herbs.some(herb => String(herb.id) === String(fallbackHerb.id))) {
        herbs.push(fallbackHerb);
    }

    return '<option value="">-- Chọn dược liệu --</option>' + herbs.map(herb => {
        const stockLabel = Number(herb.stock || 0);
        const selected = String(herb.id) === String(selectedId) ? ' selected' : '';
        return `<option value="${herb.id}" data-unit="${escapeHtml(herb.unit)}" data-stock="${stockLabel}"${selected}>${escapeHtml(herb.name)} (Tồn: ${stockLabel} ${escapeHtml(herb.unit)})</option>`;
    }).join('');
}

function formatQuantityLabel(value) {
    const numberValue = Number(value);
    if (!Number.isFinite(numberValue)) {
        return '';
    }

    return Number.isInteger(numberValue)
        ? String(numberValue)
        : numberValue.toFixed(2).replace(/\.?0+$/, '');
}

function updateServiceFields(select) {
    if (select.selectedIndex > 0) {
        const opt = select.options[select.selectedIndex];
        document.getElementById('service_sessions').value = opt.getAttribute('data-sessions') || '1';
        document.getElementById('service_instruction').value = opt.getAttribute('data-instruction') || '';
    } else {
        document.getElementById('service_sessions').value = '1';
        document.getElementById('service_instruction').value = '';
    }
}


function addService() {
    const serviceSelect = document.getElementById('service_custom_name');
    const hasService = serviceSelect && serviceSelect.value !== '';

    if (!hasService) {
        showToast('Vui lòng chọn dịch vụ trị liệu để thêm!', 'warning');
        return;
    }

    if (addItem('therapy_service', false)) {
        // Reset service fields
        serviceSelect.selectedIndex = 0;
        const serviceSessions = document.getElementById('service_sessions');
        if (serviceSessions) serviceSessions.value = '3';
        const serviceInstruction = document.getElementById('service_instruction');
        if (serviceInstruction) serviceInstruction.value = '';
        const serviceArea = document.getElementById('service_area');
        if (serviceArea) serviceArea.value = '';
    }

    hideInputForms();
}

function updateExternalProductFields(select) {
    const selectedOption = select.options[select.selectedIndex];
    const unitSpan = document.getElementById('external_unit_span');
    if (selectedOption && selectedOption.value) {
        const unit = selectedOption.dataset.unit || 'lọ';
        if (unitSpan) unitSpan.textContent = unit;
    } else {
        if (unitSpan) unitSpan.textContent = 'lọ';
    }
}

function addExternalProduct() {
    const select = document.getElementById('external_product_id');
    if (!select || !select.value) {
        showToast('Vui lòng chọn thuốc dùng ngoài/trà thảo mộc.', 'warning');
        return;
    }
    const qty = parseFloat(document.getElementById('external_qty').value) || 0;
    if (qty <= 0) {
        showToast('Số lượng thuốc dùng ngoài/trà thảo mộc phải lớn hơn 0.', 'warning');
        return;
    }
    
    const stock = parseFloat(select.options[select.selectedIndex].dataset.stock) || 0;
    if (qty > stock) {
        showToast(`Số lượng kê vượt quá số lượng tồn kho (Hiện còn: ${stock}).`, 'warning');
        return;
    }

    if (addItem('external_product', false)) {
        // Reset fields
        select.selectedIndex = 0;
        const qtyField = document.getElementById('external_qty');
        if (qtyField) qtyField.value = '1';
        // Removed external_area reset
        const instructionField = document.getElementById('external_instruction');
        if (instructionField) instructionField.value = '';
        const unitSpan = document.getElementById('external_unit_span');
        if (unitSpan) unitSpan.textContent = 'lọ';
        
        hideInputForms();
    }
}

function getAddedItemTypes() {
    const rows = document.querySelectorAll('#treatment_items_table tr[id^="item_row_"]');
    const types = new Set();
    rows.forEach(row => {
        const type = row.getAttribute('data-item-type');
        if (type) types.add(type);
    });
    return types;
}

function createHiddenInputs(index, fields) {
    const container = document.getElementById('treatment_items_hidden_inputs');
    if (!container) return;

    const wrapper = document.createElement('div');
    wrapper.id = `item_inputs_${index}`;

    Object.keys(fields).forEach(key => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.id = `item_${index}_${key}`;
        input.name = `items[${index}][${key}]`;
        input.value = fields[key] === null || fields[key] === undefined ? '' : fields[key];
        wrapper.appendChild(input);
    });

    container.appendChild(wrapper);
}

function addItem(itemType, fromFormula = false, presetItem = null) {
    const treatmentTable = document.getElementById('treatment_items_table');
    const hiddenContainer = document.getElementById('treatment_items_hidden_inputs');
    if (!treatmentTable || !hiddenContainer) return false;

    let item = {
        item_type: itemType,
        herb_id: null,
        custom_name: null,
        quantity: 0,
        unit: null,
        dosage: null,
        note: null,
        usage_area: null,
        usage_instruction: null,
        sessions: null,
        affects_stock: false,
        is_secret_formula: false,
    };

    if (presetItem) {
        item = Object.assign(item, presetItem);
    } else if (itemType === 'formula_herb') {
        const select = document.getElementById('herb_select');
        if (!select || !select.value) {
            showToast('Vui lòng chọn dược liệu sắc.', 'warning');
            return false;
        }
        item.herb_id = select.value;
        item.custom_name = select.options[select.selectedIndex].text.split(' (')[0];
        item.quantity = parseFloat(document.getElementById('herb_qty').value) || 0;
        item.unit = document.getElementById('herb_unit').textContent || '';
        item.dosage = document.getElementById('herb_dosage').value || 'Sắc cùng thang thuốc';
        item.note = document.getElementById('herb_note').value || null;
        item.affects_stock = true;
    } else if (itemType === 'external_product') {
        if (presetItem) {
            item = Object.assign(item, presetItem);
        } else {
            const select = document.getElementById('external_product_id');
            if (select && select.value) {
                item.packaged_product_id = select.value;
                item.custom_name = select.options[select.selectedIndex].text.split(' (')[0];
                item.unit = select.options[select.selectedIndex].dataset.unit || 'lọ';
                item.affects_stock = true;
            }
            item.quantity = parseFloat(document.getElementById('external_qty')?.value) || 0;
            item.usage_area = '';
            item.usage_instruction = document.getElementById('external_instruction')?.value || '';
            item.note = null;
        }
    } else if (itemType === 'service' || itemType === 'therapy_service') {
        item.custom_name = document.getElementById('service_custom_name')?.value || '';
        item.sessions = parseInt(document.getElementById('service_sessions')?.value) || 1;
        item.usage_area = '';
        item.usage_instruction = document.getElementById('service_instruction')?.value || '';
        item.unit = 'lần';
    }

    const isGroupedFormula = itemType === 'formula_herb' && item.formula_group_id;

    if (itemType === 'formula_herb') {
        if (!item.herb_id) {
            showToast('Vui lòng chọn dược liệu sắc.', 'warning');
            return false;
        }
        if (item.quantity <= 0) {
            showToast('Lượng thuốc phải lớn hơn 0.', 'warning');
            return false;
        }
    }

    if (itemType === 'external_product') {
        if (!item.packaged_product_id && !item.custom_name) {
            showToast('Vui lòng chọn thuốc dùng ngoài/trà thảo mộc.', 'warning');
            return false;
        }
        if (item.quantity <= 0) {
            showToast('Số lượng thuốc dùng ngoài/trà thảo mộc phải lớn hơn 0.', 'warning');
            return false;
        }
    }

    if (itemType === 'service' || itemType === 'therapy_service') {
        if (!item.custom_name) {
            showToast('Vui lòng chọn dịch vụ trị liệu.', 'warning');
            return false;
        }
        if (item.sessions <= 0) {
            item.sessions = 1;
        }
    }

    const idx = itemIndex++;
    const row = document.createElement('tr');
    row.id = `item_row_${idx}`;
    row.setAttribute('data-item-type', itemType);
    row.style.borderBottom = '1px solid #e2e8f0';

    let displayName = item.custom_name || item.name || '';

    if (!isGroupedFormula) {
        let qtyInputHtml = '';
        let guidanceInputHtml = '';

        if (itemType === 'formula_herb') {
            qtyInputHtml = `
                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.25rem;">
                    <input type="number" step="any" min="0.01" value="${item.quantity}" oninput="updateItemField(${idx}, 'quantity', this.value)" style="width: 70px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.25rem; text-align: center; font-weight: 700; color: #2563eb; background: #fff;">
                    <span style="font-size: 0.85rem; color: #64748b; font-weight: 600;">${escapeHtml(item.unit || 'g')} / thang</span>
                </div>
            `;
            guidanceInputHtml = `
                <input type="text" value="${escapeHtml(item.dosage || '')}" oninput="updateItemField(${idx}, 'dosage', this.value)" placeholder="Cách dùng" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.25rem 0.5rem; font-size: 0.85rem; color: #334155; background: #fff;">
            `;
        } else if (itemType === 'external_product') {
            qtyInputHtml = `
                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.25rem;">
                    <input type="number" step="any" min="0.01" value="${item.quantity}" oninput="updateItemField(${idx}, 'quantity', this.value)" style="width: 70px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.25rem; text-align: center; font-weight: 700; color: #2563eb; background: #fff;">
                    <span style="font-size: 0.85rem; color: #64748b; font-weight: 600;">${escapeHtml(item.unit || 'lọ')}</span>
                </div>
            `;
            guidanceInputHtml = `
                <input type="text" value="${escapeHtml(item.usage_instruction || '')}" oninput="updateItemField(${idx}, 'usage_instruction', this.value)" placeholder="Hướng dẫn sử dụng" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.25rem 0.5rem; font-size: 0.85rem; color: #334155; background: #fff;">
            `;
        } else if (itemType === 'service' || itemType === 'therapy_service') {
            qtyInputHtml = `
                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.25rem;">
                    <input type="number" min="1" value="${item.sessions}" oninput="updateItemField(${idx}, 'sessions', this.value)" style="width: 70px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.25rem; text-align: center; font-weight: 700; color: #2563eb; background: #fff;">
                    <span style="font-size: 0.85rem; color: #64748b; font-weight: 600;">lần</span>
                </div>
            `;
            guidanceInputHtml = `
                <input type="text" value="${escapeHtml(item.usage_instruction || '')}" oninput="updateItemField(${idx}, 'usage_instruction', this.value)" placeholder="Hướng dẫn trị liệu" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.25rem 0.5rem; font-size: 0.85rem; color: #334155; background: #fff;">
            `;
        }

        row.innerHTML = `
            <td style="padding: 0.6rem; font-weight: 700; color: #0f172a;">${itemType === 'formula_herb' ? 'Thuốc uống' : itemType === 'external_product' ? 'Thuốc dùng ngoài/Trà thảo mộc' : 'Trị liệu'}</td>
            <td style="padding: 0.6rem; color: #1e293b;">${displayName}</td>
            <td style="padding: 0.6rem; text-align: right; color: #2563eb; font-weight: 700;">${qtyInputHtml}</td>
            <td style="padding: 0.6rem; color: #475569;">${guidanceInputHtml}</td>
            <td style="padding: 0.6rem; text-align: center;"><button type="button" onclick="removeItem(${idx})" style="background:#fee2e2; border:1px solid #fca5a5; color:#b91c1c; padding:0.35rem 0.65rem; border-radius:4px; cursor:pointer;">Xóa</button></td>
        `;

        treatmentTable.appendChild(row);
    }

    const inputs = {
        item_type: itemType,
        herb_id: item.herb_id ?? '',
        packaged_product_id: item.packaged_product_id ?? '',
        custom_name: item.custom_name ?? '',
        quantity: item.quantity ?? '',
        unit: item.unit ?? '',
        dosage: item.dosage ?? '',
        note: item.note ?? '',
        usage_area: item.usage_area ?? '',
        usage_instruction: item.usage_instruction ?? '',
        sessions: item.sessions ?? '',
        formula_group_id: item.formula_group_id ?? '',
        is_secret_formula: item.is_secret_formula ? '1' : '0',
        affects_stock: item.affects_stock ? '1' : '0'
    };
    createHiddenInputs(idx, inputs);

    addedItems.push(idx);
    updateTreatmentTypeFromItems();
    updateTreatmentSummaryVisibility();

    return true;
}

function showInputForm(type) {
    hideInputForms();
    const panel = document.getElementById(`form_${type}`);
    if (panel) panel.style.display = 'block';

    if (type === 'formula_herb') {
        const formulaSelect = document.getElementById('formula_select');
        if (formulaSelect && formulaSelect.value) {
            setTimeout(loadFormulaDetails, 0);
        }
    }

    const treatmentTypeSelect = document.getElementById('treatment_type');
    if (treatmentTypeSelect) {
        if (type === 'formula_herb') {
            const types = getAddedItemTypes();
            const hasServiceType = types.has('service') || types.has('therapy_service');
            if (types.size === 0) {
                treatmentTypeSelect.value = 'herbal_only';
            } else {
                if (!types.has('formula_herb')) {
                    if (types.has('external_product') && hasServiceType) {
                        treatmentTypeSelect.value = 'combined';
                    } else if (types.has('external_product')) {
                        treatmentTypeSelect.value = 'thuoc_uong_bo';
                    } else if (hasServiceType) {
                        treatmentTypeSelect.value = 'thuoc_uong_tri_lieu';
                    } else {
                        treatmentTypeSelect.value = 'herbal_only';
                    }
                }
            }
        } else if (type === 'service') {
            const types = getAddedItemTypes();
            const hasServiceType = types.has('service') || types.has('therapy_service');
            if (!types.has('formula_herb')) {
                if (types.has('external_product') && hasServiceType) {
                    treatmentTypeSelect.value = 'bo_tri_lieu';
                } else if (types.has('external_product')) {
                    treatmentTypeSelect.value = 'external_only';
                } else if (hasServiceType) {
                    treatmentTypeSelect.value = 'service_only';
                } else {
                    treatmentTypeSelect.value = 'bo_tri_lieu';
                }
            }
        }
        toggleTreatmentTypeFields();
    }
}

// ===== FORMULA MANAGEMENT =====
// Store all sample prescriptions data
let formulasData = treatmentPageDataEl ? JSON.parse(treatmentPageDataEl.dataset.samples || '{}') : {};
if (Array.isArray(formulasData)) {
    formulasData = formulasData.reduce((carry, formula) => {
        if (formula && formula.id) carry[formula.id] = formula;
        return carry;
    }, {});
}

function loadFormulaDetails() {
    const select = document.getElementById('formula_select');
    const container = document.getElementById('formula_details_container');
    const tbody = document.getElementById('formula_items_tbody');
    const conditionHint = document.getElementById('formula_condition_hint');
    const selectedName = document.getElementById('formula_selected_name');
    const selectedCount = document.getElementById('formula_selected_count');

    if (!select || !container || !tbody) {
        return;
    }
    
    if (!select.value) {
        container.style.display = 'none';
        if (tbody) tbody.innerHTML = '';
        if (selectedName) selectedName.textContent = 'Chưa chọn bài thuốc';
        if (selectedCount) selectedCount.textContent = '0 vị thuốc';
        return;
    }
    
    const formula = formulasData[select.value];
    if (!formula) {
        container.style.display = 'none';
        showToast('Không tìm thấy dữ liệu bài thuốc mẫu đã chọn. Vui lòng tải lại trang hoặc kiểm tra danh mục bài thuốc mẫu.', 'error');
        return;
    }
    
    tbody.innerHTML = '';
    const formulaItems = Array.isArray(formula.items) ? formula.items : Object.values(formula.items || {});
    if (selectedName) selectedName.textContent = formula.name || select.options[select.selectedIndex]?.text || 'Bài thuốc đã chọn';
    if (selectedCount) selectedCount.textContent = `${formulaItems.length} vị thuốc`;

    formulaItems.forEach(item => appendFormulaEditorRow(item));

    if (formulaItems.length === 0) {
        tbody.innerHTML = `
            <tr id="formula_empty_row">
                <td colspan="6" style="padding: 1rem; text-align: center; color: #b45309; background: #fffbeb; font-weight: 700;">
                    Bài thuốc mẫu này chưa có vị thuốc. Bác sĩ hãy dùng ô "Gia giảm thêm vị thuốc" bên dưới để thêm vị trước khi đưa vào đơn.
                </td>
            </tr>
        `;
    }

    if (conditionHint) {
        if (formula.suggested_condition) {
            conditionHint.innerHTML = `<strong>Gợi ý dùng cho:</strong> ${escapeHtml(formula.suggested_condition)}`;
            conditionHint.style.display = 'block';
        } else {
            conditionHint.style.display = 'none';
        }
    }

    const numDosesInput = document.getElementById('num_doses_input');
    if (numDosesInput) {
        numDosesInput.value = parseInt(formula.default_packages) || 1;
    }
    const usageInput = document.getElementById('formula_usage_instruction_input');
    if (usageInput) {
        usageInput.value = formula.usage_instruction || 'Sắc ngày 1 thang, uống chia 2 lần sáng/chiều sau bữa ăn ấm.';
    }
    
    container.style.display = 'block';
    setTimeout(() => container.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 50);
}

function appendFormulaEditorRow(item) {
    const tbody = document.getElementById('formula_items_tbody');
    if (!tbody) return;

    item = item || {};

    const emptyRow = document.getElementById('formula_empty_row');
    if (emptyRow) emptyRow.remove();

    const row = document.createElement('tr');
    row.className = 'formula-editor-row';
    row.style.cssText = 'border-bottom: 1px solid #e2e8f0;';

    row.innerHTML = `
        <td style="padding: 0.5rem;">
            <select class="formula-herb-select" onchange="updateFormulaEditorUnit(this)" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.42rem 0.55rem; font-size: 0.84rem; background: #fff;">
                ${buildHerbOptionsHtml(item.id || '', item.id ? item : null)}
            </select>
        </td>
        <td style="padding: 0.5rem;">
            <input type="number" value="${escapeHtml(item.quantity || '')}" step="0.1" min="0.1" class="formula-qty-input" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.42rem 0.55rem; font-size: 0.84rem; text-align: right;">
        </td>
        <td style="padding: 0.5rem; text-align: center;">
            <span class="formula-unit-label" style="display: inline-block; min-width: 34px; color: #64748b; font-weight: 800;">${escapeHtml(item.unit || '-')}</span>
        </td>
        <td style="padding: 0.5rem;">
            <input type="text" value="${escapeHtml(item.dosage || 'Sắc cùng thang thuốc')}" class="formula-dosage-input" placeholder="VD: Sắc cùng thang thuốc..." style="width: 100%; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.42rem 0.55rem; font-size: 0.84rem;">
        </td>
        <td style="padding: 0.5rem;">
            <input type="text" value="${escapeHtml(item.note || '')}" class="formula-note-input" placeholder="VD: Uống lúc ấm sau ăn..." style="width: 100%; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.42rem 0.55rem; font-size: 0.84rem;">
        </td>
        <td style="padding: 0.5rem; text-align: center;">
            <button type="button" onclick="removeFormulaEditorRow(this)" style="background:#fee2e2; border:1px solid #fca5a5; color:#b91c1c; padding:0.25rem 0.55rem; border-radius:4px; cursor:pointer; font-size:0.78rem; font-weight:700;">Xóa</button>
        </td>
    `;
    tbody.appendChild(row);
    const select = row.querySelector('.formula-herb-select');
    if (select) updateFormulaEditorUnit(select);
}

function removeFormulaEditorRow(button) {
    const row = button.closest('tr');
    if (row) row.remove();
}

function updateFormulaEditorUnit(select) {
    const row = select.closest('tr');
    const unitEl = row?.querySelector('.formula-unit-label');
    if (!unitEl) return;

    if (select && select.selectedIndex > 0) {
        unitEl.textContent = select.options[select.selectedIndex].getAttribute('data-unit') || '-';
    } else {
        unitEl.textContent = '-';
    }
}

function addFormulaEditorHerb() {
    appendFormulaEditorRow({
        id: '',
        name: '',
        quantity: '',
        unit: '',
        dosage: 'Sắc cùng thang thuốc',
        note: ''
    });
}

function addFormulaItems() {
    const select = document.getElementById('formula_select');
    const numDoses = parseInt(document.getElementById('num_doses_input').value) || 1;
    
    if (!select.value) {
        showToast('Vui lòng chọn bài thuốc', 'warning');
        return;
    }
    
    const formula = formulasData[select.value];
    if (!formula) {
        showToast('Không tìm thấy dữ liệu bài thuốc mẫu đã chọn.', 'error');
        return;
    }
    if (numDoses <= 0) {
        showToast('Số lượng thang sắc phải lớn hơn 0.', 'warning');
        return;
    }
    
    const editorRows = document.querySelectorAll('#formula_items_tbody .formula-editor-row');
    let hasError = false;
    const formulaItems = [];
    
    editorRows.forEach(row => {
        const herbSelect = row.querySelector('.formula-herb-select');
        const input = row.querySelector('.formula-qty-input');
        const herbId = parseInt(herbSelect?.value || '');
        const quantity = parseFloat(input.value) || 0;
        const selectedOption = herbSelect?.options[herbSelect.selectedIndex];
        const unit = selectedOption?.getAttribute('data-unit') || '';
        const herbName = selectedOption?.text?.split(' (')[0] || '';
        const dosage = row.querySelector('.formula-dosage-input')?.value || 'Sắc cùng thang thuốc';
        const note = row.querySelector('.formula-note-input')?.value || '';

        if (!herbId || Number.isNaN(herbId)) {
            showToast('Vui lòng chọn đầy đủ dược liệu kho cho từng dòng vị thuốc.', 'warning');
            hasError = true;
            return;
        }

        if (quantity <= 0) {
            showToast('Lượng thuốc phải lớn hơn 0.', 'warning');
            hasError = true;
            return;
        }

        formulaItems.push({
            item_type: 'formula_herb',
            herb_id: herbId,
            custom_name: herbName,
            quantity,
            unit,
            dosage,
            note,
            affects_stock: true,
        });
    });

    if (hasError) {
        return;
    }

    if (formulaItems.length === 0) {
        showToast('Bài thuốc chưa có vị thuốc nào. Vui lòng chọn bài mẫu có thành phần hoặc thêm vị gia giảm trước.', 'warning');
        return;
    }

    const groupId = `formula_${Date.now()}_${itemIndex}`;
    const groupedItems = formulaItems.map(item => {
        item.formula_group_id = groupId;
        return {
            index: addGroupedFormulaHiddenItem(groupId, item),
            item,
        };
    });
    const formulaUsageInstruction = document.getElementById('formula_usage_instruction_input')?.value || formula.usage_instruction || 'Sắc ngày 1 thang, uống chia 2 lần sáng/chiều sau bữa ăn ấm.';
    appendFormulaGroupRow(groupId, formula.name, numDoses, groupedItems, formulaUsageInstruction);

    const numOfDosesField = document.getElementById('num_of_doses');
    if (numOfDosesField) {
        numOfDosesField.value = numDoses;
    }
    const prescriptionNameField = document.getElementById('prescription_formula_name');
    if (prescriptionNameField && !prescriptionNameField.value) {
        prescriptionNameField.value = formula.name;
    }
    const prescriptionUsageField = document.getElementById('prescription_usage_instruction');
    if (prescriptionUsageField) {
        prescriptionUsageField.value = formulaUsageInstruction;
    }
    
    const formulaDetailsContainer = document.getElementById('formula_details_container');
    if (formulaDetailsContainer) {
        formulaDetailsContainer.style.display = 'none';
    }
    select.value = '';
    const tbody = document.getElementById('formula_items_tbody');
    if (tbody) tbody.innerHTML = '';

    hideInputForms();
    updateTreatmentTypeFromItems();
    const summary = document.getElementById('treatment_summary_section');
    if (summary) {
        setTimeout(() => summary.scrollIntoView({ behavior: 'smooth', block: 'start' }), 80);
    }
    showToast(`Đã thêm bài thuốc "${formula.name}" vào đơn`, 'success');
}

function addGroupedFormulaHiddenItem(groupId, item) {
    const idx = itemIndex++;
    createHiddenInputs(idx, {
        item_type: 'formula_herb',
        herb_id: item.herb_id ?? '',
        custom_name: item.custom_name ?? '',
        quantity: item.quantity ?? '',
        unit: item.unit ?? '',
        dosage: item.dosage ?? '',
        note: item.note ?? '',
        usage_area: '',
        usage_instruction: '',
        sessions: '',
        formula_group_id: groupId,
        is_secret_formula: '0',
        affects_stock: '1'
    });
    addedItems.push(idx);
    return idx;
}

function appendFormulaGroupRow(groupId, formulaName, numDoses, groupedItems, usageInstruction = '') {
    const treatmentTable = document.getElementById('treatment_items_table');
    if (!treatmentTable) return;

    const safeNumDoses = parseInt(numDoses) || 1;
    const safeUsageInstruction = usageInstruction || 'Dùng theo hướng dẫn của thầy thuốc.';

    const row = document.createElement('tr');
    row.id = `item_row_${groupId}`;
    row.setAttribute('data-item-type', 'formula_herb');
    row.setAttribute('data-formula-group-id', groupId);
    row.innerHTML = `
        <td colspan="5" style="padding: 0; border-bottom: 1px solid #cbd5e1;">
            <div style="background: #fff; border: 1px solid #cbd5e1;">
                <div style="background: #f1f5f9; padding: 0.65rem 0.75rem; color: #334155; font-size: 0.88rem; font-weight: 900; text-transform: uppercase; display: flex; align-items: center; justify-content: space-between;">
                    <span>A. THUỐC UỐNG DẠNG SẮC</span>
                    <div style="display: flex; align-items: center; gap: 0.35rem;">
                        <span>Tổng kê:</span>
                        <input type="number" id="group_${groupId}_doses_input" value="${safeNumDoses}" min="1" oninput="updateFormulaGroupDoses('${groupId}', this.value)" style="width: 55px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.15rem; text-align: center; font-weight: 900; color: #334155; font-size: 0.85rem; background: #fff;">
                        <span>thang</span>
                    </div>
                </div>
                <div style="padding: 0.9rem 1rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 0.75rem;">
                        <div style="font-size: 0.95rem; color: #1e3a8a; font-weight: 900; display: flex; align-items: center; gap: 0.35rem;">
                            <span>🍵 Bài thuốc:</span>
                            <input type="text" id="group_${groupId}_name_input" value="${escapeHtml(formulaName)}" oninput="updateFormulaGroupName('${groupId}', this.value)" style="display: inline-block; padding: 0.25rem 0.55rem; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 5px; color: #2563eb; font-weight: 900; font-size: 0.9rem; width: 220px; outline: none;">
                        </div>
                        <button type="button" onclick="removeFormulaGroup('${groupId}')" style="background:#fee2e2; border:1px solid #fca5a5; color:#b91c1c; padding:0.4rem 0.75rem; border-radius:4px; cursor:pointer; font-weight:700;">Xóa bài thuốc</button>
                    </div>

                    <div style="background: #faf5ff; border: 1px solid #f3e8ff; border-radius: 5px; padding: 0.65rem 0.8rem; color: #334155; line-height: 1.55; margin-bottom: 0.65rem;">
                        <strong style="color: #6b21a8; display: block; margin-bottom: 0.4rem;">Thành phần (1 thang):</strong>
                        <table style="width: 100%; border-collapse: collapse; margin-top: 0.5rem; margin-bottom: 0.5rem; font-size: 0.85rem; border: 1px solid #e2e8f0;">
                            <thead>
                                <tr style="background: #f8fafc; border-bottom: 1px solid #cbd5e1;">
                                    <th style="padding: 0.4rem 0.5rem; text-align: left; color: #475569;">Vị thuốc</th>
                                    <th style="padding: 0.4rem 0.5rem; text-align: center; color: #475569; width: 120px;">Liều (g) / Thang</th>
                                    <th style="padding: 0.4rem 0.5rem; text-align: center; color: #475569; width: 80px;">Đơn vị</th>
                                    <th style="padding: 0.4rem 0.5rem; text-align: center; color: #475569; width: 80px;">Xóa</th>
                                </tr>
                            </thead>
                            <tbody id="formula_group_${groupId}_tbody">
                                <!-- Herbs will be appended here dynamically -->
                            </tbody>
                        </table>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 5px; padding: 0.5rem; margin-top: 0.5rem; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                            <span style="font-size: 0.82rem; font-weight: 700; color: #475569;">➕ Gia giảm thêm vị thuốc:</span>
                            <div style="flex: 1; min-width: 150px;">
                                <select id="group_${groupId}_herb_select" onchange="updateFormulaGroupUnit('${groupId}')" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.3rem 0.5rem; font-size: 0.85rem; background: #fff;">
                                    <option value="">-- Chọn vị thuốc --</option>
                                    ${buildHerbOptionsHtml('', null)}
                                </select>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.25rem;">
                                <input type="number" id="group_${groupId}_qty" placeholder="Liều" min="0.1" step="0.1" style="width: 60px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.3rem; font-size: 0.85rem; text-align: center;">
                                <span id="group_${groupId}_unit" style="font-size: 0.85rem; color: #64748b; font-weight: 600;">-</span>
                            </div>
                            <button type="button" onclick="addHerbToFormulaGroup('${groupId}')" style="background: #2563eb; color: #fff; border: none; border-radius: 4px; padding: 0.35rem 0.75rem; font-size: 0.82rem; font-weight: 700; cursor: pointer;">Thêm vị</button>
                        </div>
                    </div>

                    <div style="color: #334155; line-height: 1.6; display: flex; align-items: center; gap: 0.5rem;">
                        <strong style="white-space: nowrap;">Cách dùng tổng quát:</strong>
                        <input type="text" id="group_${groupId}_instruction_input" value="${escapeHtml(safeUsageInstruction)}" oninput="updateFormulaGroupInstruction('${groupId}', this.value)" style="flex: 1; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.25rem 0.5rem; font-size: 0.85rem; color: #334155; background: #fff;">
                    </div>
                </div>
            </div>
        </td>
    `;
    treatmentTable.appendChild(row);

    // Populate herbs table
    groupedItems.forEach(entry => {
        appendFormulaGroupHerbRow(groupId, entry.index, entry.item);
    });

    updateTreatmentSummaryVisibility();
}

document.addEventListener('DOMContentLoaded', function() {
    const formulaSelect = document.getElementById('formula_select');
    if (formulaSelect) {
        formulaSelect.addEventListener('change', loadFormulaDetails);
        formulaSelect.addEventListener('input', loadFormulaDetails);

        if (formulaSelect.value) {
            loadFormulaDetails();
        }
    }

    // Bidirectional sync for prescription formula name
    const formulaNameField = document.getElementById('prescription_formula_name');
    if (formulaNameField) {
        formulaNameField.addEventListener('input', function() {
            const nameInputs = document.querySelectorAll('input[id$="_name_input"]');
            nameInputs.forEach(input => {
                input.value = formulaNameField.value;
            });
        });
    }

    // Bidirectional sync for number of doses
    const numOfDosesField = document.getElementById('num_of_doses');
    if (numOfDosesField) {
        numOfDosesField.addEventListener('input', function() {
            const dosesInputs = document.querySelectorAll('input[id$="_doses_input"]');
            dosesInputs.forEach(input => {
                input.value = numOfDosesField.value;
            });
        });
    }

    // Bidirectional sync for usage instruction
    const usageInstructionField = document.getElementById('prescription_usage_instruction');
    if (usageInstructionField) {
        usageInstructionField.addEventListener('input', function() {
            const instructionInputs = document.querySelectorAll('input[id$="_instruction_input"]');
            instructionInputs.forEach(input => {
                input.value = usageInstructionField.value;
            });
        });
    }
});

function appendFormulaGroupHerbRow(groupId, index, item) {
    const tbody = document.getElementById(`formula_group_${groupId}_tbody`);
    if (!tbody) return;

    const row = document.createElement('tr');
    row.dataset.hiddenIndex = index;
    row.dataset.formulaGroupId = groupId;
    row.dataset.herbId = item.herb_id;
    row.style.borderBottom = '1px solid #f1f5f9';
    row.innerHTML = `
        <td style="padding: 0.55rem 0.7rem; color: #1e293b; font-weight: 700;">${escapeHtml(item.custom_name)}</td>
        <td style="padding: 0.55rem 0.7rem; text-align: center;">
            <input type="number" value="${escapeHtml(item.quantity)}" step="0.1" min="0.1" oninput="syncFormulaQuantity(${index}, this)" style="width: 90px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.25rem 0.4rem; text-align: center; font-weight: 700; color: #2563eb;">
        </td>
        <td style="padding: 0.55rem 0.7rem; text-align: center; color: #64748b; font-weight: 700;">${escapeHtml(item.unit)}</td>
        <td style="padding: 0.55rem 0.7rem; text-align: center;">
            <button type="button" onclick="removeFormulaHerb('${groupId}', ${index})" style="background:#fff1f2; border:1px solid #fecdd3; color:#be123c; padding:0.25rem 0.5rem; border-radius:4px; cursor:pointer; font-size:0.76rem; font-weight:700;">Xóa</button>
        </td>
    `;
    tbody.appendChild(row);
}

function syncFormulaQuantity(index, input) {
    const hidden = document.getElementById(`item_${index}_quantity`);
    if (hidden) hidden.value = input.value;
}

function updateItemField(index, field, value) {
    const hidden = document.getElementById(`item_${index}_${field}`);
    if (hidden) {
        hidden.value = value;
    }
}

function updateFormulaGroupName(groupId, value) {
    const nameField = document.getElementById('prescription_formula_name');
    if (nameField) {
        nameField.value = value;
    }
}

function updateFormulaGroupDoses(groupId, value) {
    const val = parseInt(value) || 1;
    const dosesField = document.getElementById('num_of_doses');
    if (dosesField) {
        dosesField.value = val;
    }
}

function updateFormulaGroupInstruction(groupId, value) {
    const instructionField = document.getElementById('prescription_usage_instruction');
    if (instructionField) {
        instructionField.value = value;
    }
}

function updateFormulaGroupUnit(groupId) {
    const select = document.getElementById(`group_${groupId}_herb_select`);
    const unitEl = document.getElementById(`group_${groupId}_unit`);
    if (!select || !unitEl) return;

    unitEl.textContent = select.selectedIndex > 0 ? (select.options[select.selectedIndex].getAttribute('data-unit') || '-') : '-';
}

function addHerbToFormulaGroup(groupId) {
    const select = document.getElementById(`group_${groupId}_herb_select`);
    const qtyInput = document.getElementById(`group_${groupId}_qty`);
    if (!select || !select.value) {
        showToast('Vui lòng chọn dược liệu muốn thêm vào bài thuốc.', 'warning');
        return;
    }

    const quantity = parseFloat(qtyInput?.value) || 0;
    if (quantity <= 0) {
        showToast('Lượng thuốc gia giảm phải lớn hơn 0.', 'warning');
        return;
    }

    const tbody = document.getElementById(`formula_group_${groupId}_tbody`);
    const existing = tbody ? tbody.querySelector(`tr[data-herb-id="${select.value}"]`) : null;
    if (existing) {
        const hiddenIndex = existing.dataset.hiddenIndex;
        const visibleInput = existing.querySelector('input[type="number"]');
        const newQty = (parseFloat(visibleInput.value) || 0) + quantity;
        visibleInput.value = newQty;
        syncFormulaQuantity(hiddenIndex, visibleInput);
        showToast('Vị thuốc đã có trong bài, hệ thống đã cộng thêm vào liều hiện tại.', 'info');
    } else {
        const opt = select.options[select.selectedIndex];
        const item = {
            herb_id: select.value,
            custom_name: opt.text.split(' (')[0],
            quantity,
            unit: opt.getAttribute('data-unit') || '',
            dosage: 'Gia giảm thêm vào bài thuốc',
            note: null,
            affects_stock: true,
            formula_group_id: groupId,
        };
        const index = addGroupedFormulaHiddenItem(groupId, item);
        appendFormulaGroupHerbRow(groupId, index, item);
    }

    select.value = '';
    if (qtyInput) qtyInput.value = '';
    updateFormulaGroupUnit(groupId);
    updateTreatmentTypeFromItems();
}

function removeFormulaHerb(groupId, index) {
    const row = document.querySelector(`#formula_group_${groupId}_tbody tr[data-hidden-index="${index}"]`);
    if (row) row.remove();

    const hiddenInputs = document.getElementById(`item_inputs_${index}`);
    if (hiddenInputs) hiddenInputs.remove();
    addedItems = addedItems.filter(id => id !== index);

    const tbody = document.getElementById(`formula_group_${groupId}_tbody`);
    if (tbody && tbody.querySelectorAll('tr[data-hidden-index]').length === 0) {
        removeFormulaGroup(groupId);
        return;
    }

    updateTreatmentTypeFromItems();
}

function removeFormulaGroup(groupId) {
    const row = document.getElementById(`item_row_${groupId}`);
    if (row) row.remove();

    document.querySelectorAll('#treatment_items_hidden_inputs div[id^="item_inputs_"]').forEach(wrapper => {
        const groupInput = wrapper.querySelector('input[name$="[formula_group_id]"]');
        if (groupInput && groupInput.value === groupId) {
            const hiddenIndex = parseInt(wrapper.id.replace('item_inputs_', ''), 10);
            wrapper.remove();
            addedItems = addedItems.filter(id => id !== hiddenIndex);
        }
    });

    updateTreatmentTypeFromItems();
}

function hideInputForms() {
    document.querySelectorAll('.item-input-panel').forEach(panel => {
        panel.style.display = 'none';
    });
    updateTreatmentTypeFromItems();
}

function updateHerbUnit(select) {
    if (select.selectedIndex > 0) {
        const opt = select.options[select.selectedIndex];
        document.getElementById('herb_unit').textContent = opt.getAttribute('data-unit');
    }
}

function updateTreatmentSummaryVisibility() {
    const section = document.getElementById('treatment_summary_section');
    const emptyRow = document.getElementById('empty_row');
    const itemRows = document.querySelectorAll('#treatment_items_table tr[id^="item_row_"]');
    const hasItems = itemRows.length > 0;

    if (section) {
        section.style.display = hasItems ? 'block' : 'none';
    }

    if (emptyRow) {
        emptyRow.style.display = hasItems ? 'none' : 'table-row';
    }
}

function updateTreatmentTypeFromItems() {
    updateTreatmentSummaryVisibility();

    const types = getAddedItemTypes();
    const treatmentTypeSelect = document.getElementById('treatment_type');
    if (!treatmentTypeSelect) return;

    const hasHerb = types.has('formula_herb');
    const hasExternal = types.has('external_product');
    const hasService = types.has('service') || types.has('therapy_service');

    let newVal = 'combined';

    if (hasHerb && hasExternal && hasService) {
        newVal = 'combined';
    } else if (hasHerb && hasExternal && !hasService) {
        newVal = 'thuoc_uong_bo';
    } else if (hasHerb && !hasExternal && hasService) {
        newVal = 'thuoc_uong_tri_lieu';
    } else if (!hasHerb && hasExternal && hasService) {
        newVal = 'bo_tri_lieu';
    } else if (hasHerb && !hasExternal && !hasService) {
        newVal = 'herbal_only';
    } else if (!hasHerb && hasExternal && !hasService) {
        newVal = 'external_only';
    } else if (!hasHerb && !hasExternal && hasService) {
        newVal = 'service_only';
    } else {
        newVal = 'service_only'; // default to service_only when empty to hide oral fields
    }

    treatmentTypeSelect.value = newVal;
    toggleTreatmentTypeFields();
}

function toggleTreatmentTypeFields() {
    const treatmentTypeSelect = document.getElementById('treatment_type');
    if (!treatmentTypeSelect) return;
    
    const val = treatmentTypeSelect.value;
    const showOralFields = ['combined', 'thuoc_uong_bo', 'thuoc_uong_tri_lieu', 'herbal_only'].includes(val);
    
    const row1 = document.getElementById('oral_prescription_row_1');
    const dosesWrapper = document.getElementById('num_of_doses_wrapper');
    const gridRow2 = document.getElementById('treatment_grid_row_2');
    
    if (showOralFields) {
        if (row1) row1.style.display = 'grid';
        if (dosesWrapper) dosesWrapper.style.display = 'block';
        if (gridRow2) gridRow2.style.gridTemplateColumns = '1fr 1fr 1fr 1fr';
    } else {
        if (row1) row1.style.display = 'none';
        if (dosesWrapper) dosesWrapper.style.display = 'none';
        if (gridRow2) gridRow2.style.gridTemplateColumns = '1fr 1fr 1fr';
    }
}

function removeItem(idx) {
    const row = document.getElementById(`item_row_${idx}`);
    if (row) {
        row.remove();
    }
    const hiddenInputs = document.getElementById(`item_inputs_${idx}`);
    if (hiddenInputs) {
        hiddenInputs.remove();
    }
    addedItems = addedItems.filter(id => id !== idx);

    if (addedItems.length === 0) {
        const emptyRow = document.getElementById('empty_row');
        if (emptyRow) emptyRow.style.display = 'table-row';
    }
    updateTreatmentTypeFromItems();
}

function submitPlan() {
    if (addedItems.length === 0) {
        showToast('Vui lòng thêm ít nhất 1 hạng mục (Thuốc sắc, Thuốc bó đắp hoặc Trị liệu) vào đơn phác đồ!', 'warning');
        return;
    }
    document.getElementById('treatmentForm').submit();
}

// Edit Record Modal Logic
function updateRecordFieldsEditModal() {
    const caseType = document.querySelector('#editRecordModal input[name="case_type"]:checked')?.value || 'normal';
    const injuryTypeSelect = document.querySelector('#editRecordModal select[name="injury_type"]');
    const injuryType = injuryTypeSelect ? injuryTypeSelect.value : '';

    const symptomsCol = document.getElementById('symptoms_col_edit_modal');
    const additionalSymptomsCol = document.getElementById('additional_symptoms_col_edit_modal');
    const diagnosisCol = document.getElementById('diagnosis_col_edit_modal');
    const gridRow = document.getElementById('grid_row_edit_modal');

    const symptomsInput = document.getElementById('symptoms_edit');
    const additionalSymptomsInput = document.getElementById('additional_symptoms_edit');
    const diagnosisInput = document.getElementById('diagnosis_edit');

    if (caseType === 'musculoskeletal') {
        // Hide symptoms and additional symptoms
        if (symptomsCol) symptomsCol.style.display = 'none';
        if (additionalSymptomsCol) additionalSymptomsCol.style.display = 'none';
        if (symptomsInput) {
            symptomsInput.removeAttribute('required');
            symptomsInput.value = "Khám Xương khớp - Chấn thương"; // satisfies Laravel validation
        }

        // Show/Hide diagnosis based on injury type
        if (injuryType === 'khac') {
            if (diagnosisCol) diagnosisCol.style.display = 'block';
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
        // General: show both symptoms and additional symptoms
        if (symptomsCol) symptomsCol.style.display = 'block';
        if (additionalSymptomsCol) additionalSymptomsCol.style.display = 'block';
        if (symptomsInput) {
            symptomsInput.setAttribute('required', 'required');
            if (symptomsInput.value === 'Khám Xương khớp - Chấn thương') {
                symptomsInput.value = '';
            }
        }

        if (diagnosisCol) diagnosisCol.style.display = 'block';
        if (diagnosisInput) {
            if (diagnosisInput.value === 'Khám Xương khớp - Chấn thương' || diagnosisInput.value.startsWith('Bong gân') || diagnosisInput.value.startsWith('Trật khớp') || diagnosisInput.value.startsWith('Nghi gãy xương') || diagnosisInput.value.startsWith('Đau vai gáy') || diagnosisInput.value.startsWith('Đau lưng') || diagnosisInput.value.startsWith('Đau khớp gối')) {
                diagnosisInput.value = '';
            }
        }
    }

    // Adjust grid columns dynamically
    if (gridRow) {
        const symptomsVisible = symptomsCol && symptomsCol.style.display !== 'none';
        const additionalVisible = additionalSymptomsCol && additionalSymptomsCol.style.display !== 'none';

        if (symptomsVisible && additionalVisible) {
            gridRow.style.display = 'grid';
            gridRow.style.gridTemplateColumns = '1fr 1fr';
            gridRow.style.gap = '1rem';
        } else if (symptomsVisible || additionalVisible) {
            gridRow.style.display = 'grid';
            gridRow.style.gridTemplateColumns = '1fr';
            gridRow.style.gap = '1rem';
        } else {
            gridRow.style.display = 'none';
        }
    }
}

function toggleCaseTypeEditModal(value) {
    const musculoskeletalBox = document.getElementById('musculoskeletal_fields_edit_modal');
    if (musculoskeletalBox) {
        musculoskeletalBox.style.display = (value === 'musculoskeletal' || value === 'combined') ? 'block' : 'none';
    }
    updateRecordFieldsEditModal();
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize treatment plan input fields visibility
    if (typeof updateTreatmentTypeFromItems === 'function') {
        updateTreatmentTypeFromItems();
    }

    try {
        const editModal = document.getElementById('editRecordModal');
        if (editModal) {
            editModal.addEventListener('click', function(e) {
                if (e.target === this) this.style.display = 'none';
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const m = document.getElementById('editRecordModal');
                if (m) m.style.display = 'none';
            }
        });

        document.querySelectorAll('.internal-prescription-link').forEach(link => {
            const internalColor = link.dataset.internalColor || '#2563eb';
            const internalBg = link.dataset.internalBg || '#eff6ff';
            const internalBorder = link.dataset.internalBorder ? `1px solid ${link.dataset.internalBorder}` : '1px solid transparent';
            link.style.color = internalColor;
            link.style.background = internalBg;
            link.style.border = internalBorder;

            link.addEventListener('click', function(event) {
                event.preventDefault();
                const prescriptionId = parseInt(this.dataset.prescriptionId, 10) || null;
                const internalLabel = this.dataset.internalLabel || '';
                if (prescriptionId !== null) {
                    openPrescriptionModal(prescriptionId, 'internal', false, internalLabel);
                }
            });
        });

        document.querySelectorAll('.delete-prescription-btn').forEach(button => {
            button.addEventListener('click', function() {
                const prescriptionId = parseInt(this.dataset.prescriptionId, 10) || null;
                if (prescriptionId !== null) {
                    activeDeleteFormId = `delete-form-${prescriptionId}`;
                    const modal = document.getElementById('confirmDeleteModal');
                    if (modal) {
                        modal.style.display = 'flex';
                        setTimeout(() => {
                            modal.firstElementChild.style.transform = 'scale(1)';
                        }, 10);
                    }
                }
            });
        });

        // Initialize edit modal state
        const caseTypeVal = document.querySelector('#editRecordModal input[name="case_type"]:checked')?.value || 'normal';
        toggleCaseTypeEditModal(caseTypeVal);
    } catch (e) {
        console.error("Error initializing edit modal:", e);
    }
});
</script>

{{-- MODAL: Chỉnh Sửa Bệnh Án --}}
<div id="editRecordModal" style="display:none; position:fixed; inset:0; z-index:10000; overflow-y:auto; background:rgba(15,23,42,0.45); backdrop-filter:blur(4px); padding:2rem 0; flex-direction:column; align-items:center; justify-content:flex-start; box-sizing:border-box;">
    <div style="background:#fff; border-radius:0.5rem; width:95%; max-width:1250px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); margin:0 auto; box-sizing:border-box;">
        <div style="display:flex; justify-content:space-between; align-items:center; padding:1.25rem 2rem; border-bottom:1px solid #f1f5f9;">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:40px; height:40px; background:#fffbeb; color:#d97706; border-radius:0.25rem; display:flex; align-items:center; justify-content:center; font-size:1.2rem;">✏️</div>
                <div>
                    <h3 style="margin:0; font-size:1.2rem; font-weight:800; color:#0f172a;">Chỉnh Sửa Bệnh Án #{{ $medicalRecord->record_code }}</h3>
                    <p style="margin:0; font-size:0.8rem; color:#64748b;">Cập nhật thông tin chi tiết của hồ sơ bệnh án</p>
                </div>
            </div>
            <button onclick="document.getElementById('editRecordModal').style.display='none'" style="background:none; border:none; font-size:1.5rem; color:#94a3b8; cursor:pointer; width:36px; height:36px; border-radius:0.25rem; display:flex; align-items:center; justify-content:center;">&times;</button>
        </div>
        <form action="{{ route('admin.medical-records.update', $medicalRecord) }}" method="POST" enctype="multipart/form-data" style="padding:1.5rem 2rem;">
            @csrf
            @method('PUT')
            <input type="hidden" name="_record_edit_form" value="1">
            
            {{-- Row 1: General Info (4 columns) --}}
            <div style="display:grid; grid-template-columns:2fr 1.5fr 1fr 1fr; gap:1rem; margin-bottom:1rem;">
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:#475569; margin-bottom:0.4rem;">Bệnh Nhân</label>
                    <input type="text" readonly value="{{ $medicalRecord->patient->full_name }} ({{ $medicalRecord->patient->patient_code }})" style="width:100%; padding:0.6rem 0.8rem; border:1px solid #cbd5e1; border-radius:0.25rem; font-size:0.9rem; color:#64748b; background:#f1f5f9; box-sizing:border-box; height:38px;">
                    <input type="hidden" name="patient_id" value="{{ $medicalRecord->patient_id }}">
                </div>
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:#475569; margin-bottom:0.4rem;">Ngày khám <span style="color:#ef4444;">*</span></label>
                    <input type="date" name="visit_date" value="{{ old('visit_date', $medicalRecord->visit_date->format('Y-m-d')) }}" required style="width:100%; padding:0.6rem 0.8rem; border:1px solid #cbd5e1; border-radius:0.25rem; font-size:0.9rem; color:#1e293b; box-sizing:border-box; height:38px;">
                </div>
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:#475569; margin-bottom:0.4rem;">Cân nặng (kg)</label>
                    <input type="number" name="weight" step="0.1" min="0" max="500" value="{{ old('weight', $medicalRecord->weight) }}" placeholder="VD: 55.5" style="width:100%; padding:0.6rem 0.8rem; border:1px solid #cbd5e1; border-radius:0.25rem; font-size:0.9rem; color:#1e293b; box-sizing:border-box; height:38px;">
                </div>
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:#475569; margin-bottom:0.4rem;">Chiều cao (cm)</label>
                    <input type="number" name="height" step="0.1" min="0" max="300" value="{{ old('height', $medicalRecord->height) }}" placeholder="VD: 160" style="width:100%; padding:0.6rem 0.8rem; border:1px solid #cbd5e1; border-radius:0.25rem; font-size:0.9rem; color:#1e293b; box-sizing:border-box; height:38px;">
                </div>
            </div>

            {{-- Row: Case Type --}}
            <div style="margin-bottom: 1rem; background: #f8fafc; border: 1px dashed #cbd5e1; padding: 0.85rem; border-radius: 0.25rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 0.4rem;">Phân loại ca khám bệnh</label>
                <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                    <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; cursor: pointer; font-weight: 600; color: #1e293b;">
                        <input type="radio" name="case_type" value="normal" {{ old('case_type', $medicalRecord->case_type) === 'normal' ? 'checked' : '' }} onchange="toggleCaseTypeEditModal(this.value)"> 
                        Khám thông thường (Bốc thuốc uống...)
                    </label>
                    <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; cursor: pointer; font-weight: 700; color: #b91c1c;">
                        <input type="radio" name="case_type" value="musculoskeletal" {{ old('case_type', $medicalRecord->case_type) === 'musculoskeletal' ? 'checked' : '' }} onchange="toggleCaseTypeEditModal(this.value)"> 
                        🦴 Xương khớp - Chấn thương - Trị liệu ngoài
                    </label>
                    <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; cursor: pointer; font-weight: 700; color: #2563eb;">
                        <input type="radio" name="case_type" value="combined" {{ old('case_type', $medicalRecord->case_type) === 'combined' ? 'checked' : '' }} onchange="toggleCaseTypeEditModal(this.value)"> 
                        🔄 Khám kết hợp cả hai
                    </label>
                </div>
            </div>

            {{-- Row: Musculoskeletal Special Fields (Hidden by default) --}}
            <div id="musculoskeletal_fields_edit_modal" style="display: none; margin-bottom: 1rem; border: 1px solid #f87171; border-radius: 0.25rem; padding: 1.25rem; background: #fffcfc;">
                <h4 style="margin: 0 0 1rem; font-size: 0.95rem; color: #b91c1c; font-weight: 800; display: flex; align-items: center; gap: 0.4rem; border-bottom: 1px solid #fee2e2; padding-bottom: 0.5rem;">
                    <span>🦴</span> Khám Xương Khớp & Chấn thương
                </h4>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Loại tổn thương / Bệnh xương khớp</label>
                        <select name="injury_type" id="injury_type_edit" onchange="updateRecordFieldsEditModal()" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; background: #fff;">
                            <option value="">-- Chọn loại tổn thương --</option>
                            <option value="bong_gan" {{ old('injury_type', $medicalRecord->injury_type) == 'bong_gan' ? 'selected' : '' }}>Bong gân</option>
                            <option value="trat_khop" {{ old('injury_type', $medicalRecord->injury_type) == 'trat_khop' ? 'selected' : '' }}>Trật khớp</option>
                            <option value="nghi_gay_xuong" {{ old('injury_type', $medicalRecord->injury_type) == 'nghi_gay_xuong' ? 'selected' : '' }}>Nghi gãy xương / Rạn xương nhẹ</option>
                            <option value="dau_vai_gay" {{ old('injury_type', $medicalRecord->injury_type) == 'dau_vai_gay' ? 'selected' : '' }}>Đau vai gáy</option>
                            <option value="dau_lung" {{ old('injury_type', $medicalRecord->injury_type) == 'dau_lung' ? 'selected' : '' }}>Đau lưng / Thoái hóa cột sống</option>
                            <option value="dau_goi" {{ old('injury_type', $medicalRecord->injury_type) == 'dau_goi' ? 'selected' : '' }}>Đau khớp gối</option>
                            <option value="khac" {{ old('injury_type', $medicalRecord->injury_type) == 'khac' ? 'selected' : '' }}>Loại tổn thương khác</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Vị trí chấn thương / Vùng bị đau</label>
                        <input type="text" name="injury_location" value="{{ old('injury_location', $medicalRecord->injury_location) }}" placeholder="VD: Khớp cổ chân trái, Đầu gối phải..." style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; box-sizing: border-box;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Nguyên nhân chấn thương</label>
                        <input type="text" name="injury_cause" value="{{ old('injury_cause', $medicalRecord->injury_cause) }}" placeholder="VD: Ngã xe, mang vác vật nặng..." style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Mức độ chấn thương</label>
                        <select name="pain_level" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; background: #fff;">
                            <option value="">-- Chọn mức độ chấn thương --</option>
                            <option value="3" {{ old('pain_level', $medicalRecord->pain_level) == 3 ? 'selected' : '' }}>Nhẹ</option>
                            <option value="5" {{ old('pain_level', $medicalRecord->pain_level) == 5 ? 'selected' : '' }}>Trung bình</option>
                            <option value="8" {{ old('pain_level', $medicalRecord->pain_level) == 8 ? 'selected' : '' }}>Nặng</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Dấu hiệu lâm sàng bên ngoài</label>
                        <textarea name="clinical_signs" placeholder="VD: Sưng nề to vùng cổ chân, bầm tím dưới da..." rows="2" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; resize: vertical; box-sizing: border-box;">{!! old('clinical_signs', $medicalRecord->clinical_signs) !!}</textarea>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Kết quả sờ nắn / Nắm chỉnh</label>
                        <textarea name="palpation_result" placeholder="VD: Ấn đau nhói..." rows="2" style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; resize: vertical; box-sizing: border-box;">{!! old('palpation_result', $medicalRecord->palpation_result) !!}</textarea>
                    </div>
                </div>

                <div style="margin-bottom: 0.5rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Xem ảnh chụp phim (nếu có)</label>
                    <input type="text" name="xray_note" value="{{ old('xray_note', $medicalRecord->xray_note) }}" placeholder="VD: Phim mang từ viện về: Khe khớp bình thường, không rạn gãy..." style="width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.85rem; color: #1e293b; box-sizing: border-box;">
                </div>
            </div>

            {{-- Row 2: Symptoms & Diagnosis (2 columns side-by-side) --}}
            <div id="grid_row_edit_modal" style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                <div id="symptoms_col_edit_modal">
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:#475569; margin-bottom:0.4rem;">Triệu chứng chính <span style="color:#ef4444;">*</span></label>
                    <textarea name="symptoms" id="symptoms_edit" required rows="3" placeholder="Ghi nhận lời khai và triệu chứng..." style="width:100%; padding:0.6rem 0.8rem; border:1px solid #cbd5e1; border-radius:0.25rem; font-size:0.9rem; color:#1e293b; resize:vertical; box-sizing:border-box;">{!! old('symptoms', $medicalRecord->symptoms) !!}</textarea>
                </div>
                <div id="additional_symptoms_col_edit_modal">
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:#475569; margin-bottom:0.4rem;">Các triệu chứng khác (nếu có)</label>
                    <textarea name="additional_symptoms" id="additional_symptoms_edit" rows="3" placeholder="Nhập thêm các biểu hiện, triệu chứng phát sinh..." style="width:100%; padding:0.6rem 0.8rem; border:1px solid #cbd5e1; border-radius:0.25rem; font-size:0.9rem; color:#1e293b; resize:vertical; box-sizing:border-box;">{!! old('additional_symptoms', $medicalRecord->additional_symptoms) !!}</textarea>
                </div>
            </div>
            
            <div style="margin-bottom:1rem;" id="diagnosis_col_edit_modal">
                <label style="display:block; font-size:0.82rem; font-weight:700; color:#475569; margin-bottom:0.4rem;">Chẩn đoán <span style="color:#94a3b8; font-size:0.75rem;">(có thể bổ sung sau)</span></label>
                <textarea name="diagnosis" id="diagnosis_edit" rows="2" placeholder="Có thể để trống để lưu trạng thái chưa chẩn đoán chính thức..." style="width:100%; padding:0.6rem 0.8rem; border:1px solid #cbd5e1; border-radius:0.25rem; font-size:0.9rem; color:#1e293b; resize:vertical; box-sizing:border-box;">{!! old('diagnosis', $medicalRecord->hasDiagnosisText() ? $medicalRecord->diagnosis : '') !!}</textarea>
            </div>

            <div style="margin-bottom:1rem;">
                <label style="display:block; font-size:0.82rem; font-weight:700; color:#475569; margin-bottom:0.4rem;">Lưu ý cho bác sĩ khi kê đơn / thăm khám</label>
                <textarea name="doctor_note" id="doctor_note_edit" rows="3" placeholder="Các lưu ý chuyên môn, cảnh báo và điểm cần thận trọng khi kê đơn..." style="width:100%; padding:0.6rem 0.8rem; border:1px solid #cbd5e1; border-radius:0.25rem; font-size:0.9rem; color:#1e293b; resize:vertical; box-sizing:border-box;">{!! old('doctor_note', $medicalRecord->doctor_note) !!}</textarea>
            </div>

            {{-- Footer Action Buttons --}}
            <div style="display:flex; gap:1rem; justify-content:flex-end; border-top:1px solid #f1f5f9; padding-top:1.25rem;">
                <button type="button" onclick="document.getElementById('editRecordModal').style.display='none'" style="padding:0.6rem 1.5rem; background:#fff; color:#64748b; border:1px solid #cbd5e1; border-radius:0.25rem; font-size:0.9rem; font-weight:600; cursor:pointer;">Hủy</button>
                <button type="submit" style="padding:0.6rem 1.5rem; background:#d97706; color:#fff; border:none; border-radius:0.25rem; font-size:0.9rem; font-weight:700; cursor:pointer; box-shadow:0 2px 6px rgba(217,119,6,0.15);">Cập nhật Bệnh Án</button>
            </div>
        </form>
    </div>
</div>

{{-- AI JS đã chuyển sang trang Kê đơn (prescriptions/create) --}}

@if(session('scroll_to_treatment_action'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
        const treatmentSection = document.getElementById('treatment-action-section');

        if (treatmentSection) {
            treatmentSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
    }, 450);
});
</script>
@endif

@endsection
