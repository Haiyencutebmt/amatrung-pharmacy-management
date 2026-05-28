@php
    $printType = ($printType ?? request('type')) === 'internal' ? 'internal' : 'patient';
    $isInternal = $printType === 'internal';
    $patient = $prescription->medicalRecord->patient;
    $record = $prescription->medicalRecord;
    $herbs = $prescription->items->whereIn('item_type', ['formula_herb', 'herb', 'oral_herb']);
    $externalHerbs = $prescription->items->whereIn('item_type', ['external_product', 'packaged_product', 'external_herb']);
    $services = $prescription->items->whereIn('item_type', ['therapy_service', 'service']);
    $numDoses = $prescription->num_of_doses ?: ($herbs->max('number_of_doses') ?: 1);
    $formulaName = $prescription->note ?: 'Bài thuốc sắc gia giảm';
    $isDispensingSheet = $prescription->isDispensingPrescription();
    $showDispensingColumns = $isInternal && $isDispensingSheet;
    $internalTitle = $isDispensingSheet ? 'PHIẾU BỐC THUỐC NỘI BỘ' : 'PHIẾU CHỈ ĐỊNH TRỊ LIỆU NỘI BỘ';
    $internalSignerTitle = $isDispensingSheet ? 'Người bốc thuốc' : 'Người thực hiện trị liệu';
    $internalFootnote = $isDispensingSheet
        ? '* Phiếu bốc thuốc nội bộ, có đầy đủ thành phần và tổng lượng cần bốc. Không giao phiếu này cho bệnh nhân.'
        : '* Phiếu trị liệu nội bộ, có đầy đủ thuốc dùng ngoài/dịch vụ và hướng dẫn thực hiện. Không giao phiếu này cho bệnh nhân.';

    $fmt = function ($value) {
        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
    };

    $bmi = null;
    $bmiText = '';
    $bmiColor = '#111';
    $bmiBg = '#fafafa';
    $bmiBorder = '#ccc';
    $bmiAdvice = '';

    if ($record->weight && $record->height) {
        $heightInMeters = (float) $record->height / 100;
        if ($heightInMeters > 0) {
            $bmi = round((float) $record->weight / ($heightInMeters * $heightInMeters), 1);

            if ($bmi < 18.5) {
                $bmiText = 'Thiếu cân';
                $bmiColor = '#1e40af';
                $bmiBg = '#eff6ff';
                $bmiBorder = '#bfdbfe';
                $bmiAdvice = 'Chỉ số BMI cho thấy người bệnh đang thiếu cân. Cần chú ý bồi bổ cơ thể, tăng cường dinh dưỡng và theo dõi khả năng tiêu hóa, hấp thu.';
            } elseif ($bmi < 23) {
                $bmiText = 'Bình thường';
                $bmiColor = '#166534';
                $bmiBg = '#f0fdf4';
                $bmiBorder = '#bbf7d0';
                $bmiAdvice = 'Chỉ số BMI ở mức cân đối. Nên tiếp tục duy trì chế độ dinh dưỡng lành mạnh, uống đủ nước và vận động đều đặn.';
            } elseif ($bmi < 25) {
                $bmiText = 'Thừa cân';
                $bmiColor = '#9a3412';
                $bmiBg = '#fff7ed';
                $bmiBorder = '#fed7aa';
                $bmiAdvice = 'Người bệnh đang ở trạng thái thừa cân nhẹ. Nên hạn chế bớt tinh bột, đường và mỡ động vật, đồng thời tăng cường hoạt động thể chất phù hợp.';
            } else {
                $bmiText = 'Béo phì';
                $bmiColor = '#991b1b';
                $bmiBg = '#fef2f2';
                $bmiBorder = '#fecaca';
                $bmiAdvice = 'Chỉ số BMI ở mức béo phì. Nên kiểm soát nghiêm ngặt cân nặng để giảm áp lực lên cột sống, khớp gối và tim mạch. Hãy giảm tinh bột, chất béo, đồ ngọt.';
            }
        }
    }
@endphp

{{-- Header --}}
<div class="doc-header">
    <div class="doc-clinic">
        <div style="font-weight: bold; font-size: 11pt;">SỞ Y TẾ TỈNH ĐẮK LẮK</div>
        <h1 style="margin: 0; font-size: 15pt; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">NHÀ THUỐC ĐÔNG Y AMATRUNG</h1>
        <p style="margin: 2px 0; font-size: 9.5pt; color: #111;">Địa chỉ: 54/46 Amajhao, Phường Tân Lập, Tỉnh Đắk Lắk</p>
        <p style="margin: 2px 0; font-size: 9.5pt; color: #111;">Hotline: 0983009748 | amatrung.vn</p>
    </div>
    <div class="doc-meta" style="text-align: right; font-size: 10pt;">
        <p style="margin: 0;">Mã đơn: <strong>#{{ $prescription->id }}</strong></p>
        <p style="margin: 3px 0 0 0;">Ngày kê: <strong>{{ $prescription->created_at->format('d/m/Y') }}</strong></p>
        @if($isInternal)
            <p style="margin: 3px 0 0 0; color: #b45309; font-weight: bold;">PHIẾU NỘI BỘ</p>
        @endif
    </div>
</div>

{{-- Title --}}
<div class="doc-title" style="text-align: center; margin: 10px 0 15px 0;">
    <h2 style="margin: 0; font-size: 17pt; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">
        {{ $isInternal ? $internalTitle : 'PHIẾU ĐIỀU TRỊ DÀNH CHO BỆNH NHÂN' }}
    </h2>
    @if($isInternal)
        <div style="font-size: 9pt; color: #b45309; font-weight: bold; margin-top: 3px;">Không giao phiếu này cho bệnh nhân</div>
    @endif
</div>

{{-- Patient Info Box --}}
<div class="patient-border-box" style="border: 1px solid #000; padding: 8px 12px; margin-bottom: 15px; border-radius: 4px;">
    <table class="patient-info-table" style="width: 100%; border-collapse: collapse; font-size: 10.5pt;">
        <tr>
            <td style="width: 50%; padding: 5px; vertical-align: top;">Họ và tên BN: <strong>{{ $patient->full_name }}</strong></td>
            <td style="width: 25%; padding: 5px; vertical-align: top;">Tuổi: <strong>{{ $patient->age ?? '...' }}</strong></td>
            <td style="width: 25%; padding: 5px; vertical-align: top;">Giới tính: <strong>{{ $patient->gender_label }}</strong></td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 5px; vertical-align: top;">Địa chỉ: {{ $patient->address ?? '...' }}</td>
            <td style="padding: 5px; vertical-align: top;">Điện thoại: {{ $patient->phone ?? '...' }}</td>
        </tr>
        @if($bmi)
            <tr style="border-top: 1px dashed #ccc;">
                <td colspan="3" style="padding: 7px 5px; vertical-align: top;">
                    Chỉ số đo đạc:
                    <strong>{{ $fmt($record->weight) }} kg / {{ $fmt($record->height) }} cm</strong>
                    &nbsp;|&nbsp;
                    BMI: <strong>{{ $bmi }}</strong>
                    <strong style="color: {{ $bmiColor }};">- {{ $bmiText }}</strong>
                </td>
            </tr>
        @endif
        <tr style="border-top: 1px dashed #ccc;">
            <td colspan="3" style="padding: 8px 5px 5px 5px; vertical-align: top;">
                Chẩn đoán chính: <strong style="color: #b91c1c; text-transform: uppercase;">{{ $record->diagnosis }}</strong>
                @if($record->injury_location)
                    <br><span style="color: #e65100;">Vị trí tổn thương: {{ $record->injury_location }}</span>
                @endif
            </td>
        </tr>
    </table>
</div>

@if($bmi)
    <div class="usage-instruction-box" style="margin: -6px 0 15px 0; padding: 9px 12px; border: 1px solid {{ $bmiBorder }}; font-size: 10.5pt; border-radius: 4px; background-color: {{ $bmiBg }}; color: {{ $bmiColor }};">
        <strong>Khuyến nghị y khoa (Chỉ số BMI: {{ $bmi }} - Thể trạng: {{ $bmiText }}):</strong>
        {{ $bmiAdvice }}
    </div>
@endif

{{-- Items Table --}}
<table class="prescription-table" style="width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 10.5pt;">
    <thead>
        <tr>
            <th style="border: 1px solid #000; padding: 6px 8px; text-align: left; background-color: #f2f2f2; font-weight: bold; font-size: 10pt; text-transform: uppercase;">Hạng mục phác đồ điều trị</th>
            @if($showDispensingColumns)
                <th style="border: 1px solid #000; padding: 6px 8px; text-align: center; width: 120px; background-color: #f2f2f2; font-weight: bold; font-size: 10pt; text-transform: uppercase;">Mỗi thang</th>
                <th style="border: 1px solid #000; padding: 6px 8px; text-align: center; width: 120px; background-color: #f2f2f2; font-weight: bold; font-size: 10pt; text-transform: uppercase;">Tổng bốc</th>
            @else
                <th style="border: 1px solid #000; padding: 6px 8px; text-align: center; width: 120px; background-color: #f2f2f2; font-weight: bold; font-size: 10pt; text-transform: uppercase;">Số lượng</th>
            @endif
            <th style="border: 1px solid #000; padding: 6px 8px; text-align: left; background-color: #f2f2f2; font-weight: bold; font-size: 10pt; text-transform: uppercase;">Cách dùng & Hướng dẫn cụ thể</th>
        </tr>
    </thead>
    <tbody>
        @if($herbs->count() > 0)
            <tr>
                <td colspan="{{ $showDispensingColumns ? 4 : 3 }}" class="rx-group-title" style="background-color: #eaeaea; font-weight: bold; padding: 5px 8px; font-size: 10pt; border: 1px solid #000;">
                    A. BÀI THUỐC THANG (Sắc/Xông/Ngâm) (Tổng kê: {{ $numDoses }} thang)
                </td>
            </tr>

            @if($showDispensingColumns)
                <tr>
                    <td colspan="4" style="border: 1px solid #000; padding: 6px 8px; font-weight: bold;">
                        Bài thuốc: {{ $formulaName }}
                    </td>
                </tr>
                @foreach($herbs as $item)
                    @php
                        $doseCount = (int) ($item->number_of_doses ?: $numDoses ?: 1);
                        $perDoseQty = (float) ($item->quantity_per_dose ?: ($doseCount > 0 ? ((float) $item->quantity / $doseCount) : $item->quantity));
                        $totalQty = (float) ($item->quantity ?: ($perDoseQty * $doseCount));
                    @endphp
                    <tr>
                        <td style="border: 1px solid #000; padding: 6px 8px; font-weight: bold; padding-left: 15px;">{{ $item->display_name }}</td>
                        <td style="border: 1px solid #000; padding: 6px 8px; text-align: center; font-weight: bold;">{{ $fmt($perDoseQty) }} {{ $item->unit }}</td>
                        <td style="border: 1px solid #000; padding: 6px 8px; text-align: center; font-weight: bold;">{{ $fmt($totalQty) }} {{ $item->unit }}</td>
                        <td style="border: 1px solid #000; padding: 6px 8px;">{{ $item->dosage }} {{ $item->note ? '('.$item->note.')' : '' }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td style="border: 1px solid #000; padding: 6px 8px; font-weight: bold; padding-left: 15px;">Bài thuốc: {{ $formulaName }}</td>
                    <td style="border: 1px solid #000; padding: 6px 8px; text-align: center; font-weight: bold;">{{ $numDoses }} thang</td>
                    <td style="border: 1px solid #000; padding: 6px 8px;">{{ $prescription->usage_instruction ?: 'Dùng theo hướng dẫn của thầy thuốc.' }}</td>
                </tr>
            @endif
        @endif

        @if($externalHerbs->count() > 0)
            <tr>
                <td colspan="{{ $showDispensingColumns ? 4 : 3 }}" class="rx-group-title" style="background-color: #eaeaea; font-weight: bold; padding: 5px 8px; font-size: 10pt; border: 1px solid #000;">B. THUỐC BÓ & CHẾ PHẨM DÙNG NGOÀI</td>
            </tr>
            @foreach($externalHerbs as $item)
                <tr>
                    <td style="border: 1px solid #000; padding: 6px 8px; font-weight: bold; padding-left: 15px;">{{ $item->display_name }}</td>
                    @if($showDispensingColumns)
                        <td style="border: 1px solid #000; padding: 6px 8px; text-align: center;">-</td>
                    @endif
                    <td style="border: 1px solid #000; padding: 6px 8px; text-align: center; font-weight: bold;">{{ $fmt($item->quantity) }} {{ $item->unit }}</td>
                    <td style="border: 1px solid #000; padding: 6px 8px;">
                        @if($item->usage_area) <strong>Vùng đắp:</strong> {{ $item->usage_area }}<br> @endif
                        {{ $item->usage_instruction ?? $item->dosage }}
                    </td>
                </tr>
            @endforeach
        @endif

        @if($services->count() > 0)
            <tr>
                <td colspan="{{ $showDispensingColumns ? 4 : 3 }}" class="rx-group-title" style="background-color: #eaeaea; font-weight: bold; padding: 5px 8px; font-size: 10pt; border: 1px solid #000;">C. VẬT LÝ TRỊ LIỆU / DỊCH VỤ TÁC ĐỘNG</td>
            </tr>
            @foreach($services as $item)
                <tr>
                    <td style="border: 1px solid #000; padding: 6px 8px; font-weight: bold; padding-left: 15px; color: #1e3a8a;">{{ $item->display_name }}</td>
                    @if($showDispensingColumns)
                        <td style="border: 1px solid #000; padding: 6px 8px; text-align: center;">-</td>
                    @endif
                    <td style="border: 1px solid #000; padding: 6px 8px; text-align: center; font-weight: bold;">{{ $item->sessions ? $item->sessions . ' lần' : '1 lần' }}</td>
                    <td style="border: 1px solid #000; padding: 6px 8px;">
                        @if($item->usage_area) <strong>Vùng:</strong> {{ $item->usage_area }}<br> @endif
                        {{ $item->usage_instruction ?? $item->note }}
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

@if(!$isInternal && ($prescription->public_instruction || $prescription->note))
    <div class="usage-instruction-box" style="margin-top: 12px; padding: 10px; border: 1px dashed #000; font-size: 10.5pt; border-radius: 4px; background: #fafafa;">
        <strong>Lời dặn & Hướng dẫn dành cho bệnh nhân:</strong><br>
        {!! nl2br(e($prescription->public_instruction ?? 'Dùng thuốc đúng theo hướng dẫn, tái khám đúng hẹn.')) !!}
    </div>
@endif

@if(!$isInternal && $herbs->count() > 0)
    <div class="usage-instruction-box" style="margin-top: 12px; padding: 10px; border: 1px solid #f59e0b; font-size: 10.5pt; border-radius: 4px; background: #fffbeb;">
        <strong style="color: #92400e;">Quy định đổi/trả và an toàn sử dụng thuốc:</strong><br>
        Bệnh nhân chỉ được phản hồi đổi/trả thuốc trong vòng <strong>24 giờ (01 ngày)</strong> kể từ thời điểm nhận thuốc. Sau 24 giờ, nhà thuốc không tiếp nhận trả thuốc/hoàn kho. Nếu phát hiện thuốc có dấu hiệu ẩm mốc, hư hỏng, mùi lạ hoặc bao gói bất thường, <strong>không sử dụng</strong> và liên hệ ngay bác sĩ/nhà thuốc để được kiểm tra trong thời hạn trên.
    </div>
@endif

@if($isInternal)
    <div class="usage-instruction-box" style="margin-top: 12px; padding: 10px; border: 1px dashed #000; font-size: 10.5pt; border-radius: 4px; background-color: #fffbeb; border-color: #b45309;">
        <strong style="color: #b45309;">GHI CHÚ NỘI BỘ THẦY THUỐC:</strong><br>
        {!! nl2br(e($prescription->internal_note ?: 'Không có ghi chú nội bộ.')) !!}
    </div>
@endif

{{-- Treatment metadata --}}
<div style="display: flex; gap: 30px; margin-top: 15px; font-size: 11pt; border-top: 1px dashed #ccc; padding-top: 10px; flex-wrap: wrap;">
    @if($herbs->count() > 0)
        <div><strong>Tổng sắc uống:</strong> {{ $numDoses }} thang</div>
    @endif
    @if($prescription->course_days)
        <div><strong>Liệu trình đợt:</strong> {{ $prescription->course_days }} ngày</div>
    @endif
    @if($prescription->usage_instruction)
        <div><strong>Cách dùng chung:</strong> {{ $prescription->usage_instruction }}</div>
    @endif
</div>

@if($prescription->follow_up_date)
    <div style="border: 1px solid #000; padding: 8px; margin-top: 12px; text-align: center; font-weight: bold; font-size: 11.5pt; background-color: #e6f4ea; border-radius: 4px;">
        HẸN TÁI KHÁM TIẾP THEO: {{ $prescription->follow_up_date->format('d/m/Y') }}
    </div>
@endif

{{-- Signatures --}}
<div class="doc-footer" style="margin-top: 20px; display: flex; justify-content: space-between;">
    <div class="doc-sig" style="text-align: center; width: 200px; font-size: 10pt;">
        <p>Ngày .... tháng .... năm 20...</p>
        <p class="title" style="font-weight: 700; margin-bottom: 50px;">{{ $isInternal ? $internalSignerTitle : 'Người nhận thuốc / Bệnh nhân' }}</p>
        <p style="color: #64748b; font-size: 0.85em; font-style: italic;">(Ký và ghi rõ họ tên)</p>
    </div>
    <div class="doc-sig" style="text-align: center; width: 200px; font-size: 10pt;">
        <p>Ngày {{ $prescription->created_at->format('d') }} tháng {{ $prescription->created_at->format('m') }} năm {{ $prescription->created_at->format('Y') }}</p>
        <p class="title" style="font-weight: 700; margin-bottom: 50px;">Bác sĩ / Thầy thuốc điều trị</p>
        <p class="name" style="font-weight: 700;">{{ $prescription->staff->name ?? 'BS. Y Hiếu Niê' }}</p>
    </div>
</div>

<p class="doc-footnote" style="font-size: 8pt; color: #666; font-style: italic; margin-top: 15px; border-top: 1px solid #ccc; padding-top: 5px;">
    {{ $isInternal ? $internalFootnote : '* Phiếu này có giá trị theo dõi phác đồ trong suốt đợt điều trị. Vui lòng mang theo khi tái khám.' }}
</p>
