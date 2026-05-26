<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In Phiếu Khám & Điều Trị - BA{{ $medicalRecord->record_code }}</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 10mm;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #111;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .container {
            width: 100%;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            border-bottom: 2px solid #111;
            padding-bottom: 10px;
        }
        .clinic-info h1 {
            margin: 0;
            font-size: 15pt;
            text-transform: uppercase;
            font-weight: 800;
        }
        .clinic-info p {
            margin: 2px 0;
            font-size: 9.5pt;
            color: #333;
        }
        .doc-title {
            text-align: center;
            margin: 12px 0 18px 0;
        }
        .doc-title h2 {
            margin: 0;
            font-size: 16pt;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 1px;
        }
        
        .patient-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10.5pt;
        }
        .patient-table tr {
            border-bottom: 1px solid #ddd;
        }
        .patient-table td {
            padding: 5px;
        }

        .section-title {
            font-weight: bold;
            font-size: 11pt;
            border-bottom: 1.5px solid #111;
            padding-bottom: 2px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .clinical-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5pt;
            margin-bottom: 15px;
            line-height: 1.4;
        }
        .clinical-table td {
            padding: 4px 0;
        }

        .rx-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-bottom: 12px;
        }
        .rx-table th {
            text-align: left;
            padding: 5px;
            border-top: 1.5px solid #111;
            border-bottom: 1.5px solid #111;
            background-color: #fafafa;
            font-weight: bold;
        }
        .rx-table td {
            padding: 4px 5px;
            border-bottom: 1px dashed #ddd;
        }
        .rx-group-title {
            background-color: #f5f5f5;
            font-weight: bold;
            padding: 4px;
            border-top: 1px solid #111;
            border-bottom: 1px solid #111;
            font-size: 9.5pt;
            text-transform: uppercase;
        }

        .footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }
        .signature {
            text-align: center;
            width: 200px;
            font-size: 10pt;
        }
        .signature .title {
            font-weight: bold;
            margin-bottom: 50px;
        }

        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-family: sans-serif;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 9999;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()">🖨️ Bấm vào đây để IN PHIẾU</button>

    <div class="container">
        {{-- Header --}}
        <div class="header">
            <div class="clinic-info">
                <div style="font-weight: bold; font-size: 11pt; color: #111;">SỞ Y TẾ TỈNH ĐẮK LẮK</div>
                <h1>NHÀ THUỐC ĐÔNG Y AMATRUNG</h1>
                <p>Địa chỉ: 54/46 Amajhao, Phường Tân Lập, Tỉnh Đắk Lắk</p>
                <p>Hotline: 0983009748 — Email: contact@amatrung.vn</p>
            </div>
            <div style="text-align: right; font-size: 10pt;">
                <p style="margin: 0;">Mã BA: <strong style="font-size: 11pt;">{{ $medicalRecord->record_code }}</strong></p>
                <p style="margin: 3px 0 0 0;">Ngày khám: <strong>{{ $medicalRecord->visit_date->format('d/m/Y') }}</strong></p>
            </div>
        </div>

        {{-- Document Title --}}
        <div class="doc-title">
            <h2>PHIẾU KHÁM BỆNH & CHỈ ĐỊNH ĐIỀU TRỊ</h2>
        </div>

        {{-- Patient administrative data --}}
        <table class="patient-table">
            <tr style="border-top: 1.5px solid #111; background: #fafafa;">
                <td style="width: 45%; font-weight: bold;">Họ và tên: <span style="font-size: 11pt; text-transform: uppercase;">{{ $medicalRecord->patient->full_name }}</span></td>
                <td style="width: 18%;">Mã BN: <strong>{{ $medicalRecord->patient->patient_code }}</strong></td>
                <td style="width: 18%;">Tuổi: <strong>{{ $medicalRecord->patient->age ?? '...' }}</strong></td>
                <td style="width: 19%;">Giới tính: <strong>{{ $medicalRecord->patient->gender_label }}</strong></td>
            </tr>
            <tr>
                <td colspan="2">Địa chỉ: <strong>{{ $medicalRecord->patient->address ?? '—' }}</strong></td>
                <td colspan="2">Điện thoại: <strong>{{ $medicalRecord->patient->phone ?? $medicalRecord->patient->guardian_phone ?? '—' }}</strong></td>
            </tr>
            <tr style="border-bottom: 1.5px solid #111;">
                <td colspan="2">Chỉ số: Cân nặng: <strong>{{ $medicalRecord->weight ?? '—' }} kg</strong> / Chiều cao: <strong>{{ $medicalRecord->height ?? '—' }} cm</strong></td>
                <td colspan="2">Bác sĩ khám: <strong>{{ $medicalRecord->staff->name ?? 'N/A' }}</strong></td>
            </tr>
        </table>

        {{-- Clinical examination info --}}
        <div class="section-title">I. TÌNH TRẠNG LÂM SÀNG & CHẨN ĐOÁN</div>
        <table class="clinical-table">
            <tr>
                <td style="width: 22%; font-weight: bold; vertical-align: top;">1. Triệu chứng lâm sàng:</td>
                <td style="vertical-align: top;">{!! nl2br(e($medicalRecord->symptoms)) !!}</td>
            </tr>
            
            @if($medicalRecord->case_type === 'musculoskeletal' || $medicalRecord->case_type === 'combined')
            <tr>
                <td style="font-weight: bold; vertical-align: top;">2. Chi tiết xương khớp:</td>
                <td style="vertical-align: top;">
                    Tổn thương: <strong>@switch($medicalRecord->injury_type)
                        @case('bong_gan') Bong gân @break
                        @case('trat_khop') Trật khớp @break
                        @case('nghi_gay_xuong') Nghi gãy xương @break
                        @case('dau_vai_gay') Đau vai gáy @break
                        @case('dau_lung') Đau vai lưng @break
                        @case('dau_goi') Đau khớp gối @break
                        @default {{ $medicalRecord->injury_type ?? '—' }}
                    @endswitch</strong> 
                    | Vị trí: <strong>{{ $medicalRecord->injury_location ?? '—' }}</strong> 
                    | Mức độ chấn thương: <strong>@if($medicalRecord->pain_level == 3)Nhẹ@elseif($medicalRecord->pain_level == 5)Trung bình@elseif($medicalRecord->pain_level == 8)Nặng@else{{ $medicalRecord->pain_level ?? '—' }}@endif</strong> 
                    | Nguyên nhân: <strong>{{ $medicalRecord->injury_cause ?? '—' }}</strong>
                </td>
            </tr>
            @if($medicalRecord->clinical_signs)
            <tr>
                <td style="font-weight: bold; vertical-align: top; padding-left: 15px; color: #555;">- Dấu hiệu bên ngoài:</td>
                <td style="vertical-align: top; font-style: italic;">{!! nl2br(e($medicalRecord->clinical_signs)) !!}</td>
            </tr>
            @endif
            @if($medicalRecord->palpation_result)
            <tr>
                <td style="font-weight: bold; vertical-align: top; padding-left: 15px; color: #555;">- Kết quả sờ nắn:</td>
                <td style="vertical-align: top; font-style: italic;">{!! nl2br(e($medicalRecord->palpation_result)) !!}</td>
            </tr>
            @endif
            @endif

            <tr>
                <td style="font-weight: bold; vertical-align: top;">3. Chẩn đoán xác định:</td>
                <td style="vertical-align: top; font-weight: bold; font-size: 11pt; text-transform: uppercase;">{{ $medicalRecord->diagnosis }}</td>
            </tr>

            @if($medicalRecord->doctor_note)
            <tr>
                <td style="font-weight: bold; vertical-align: top;">4. Lời dặn thầy thuốc:</td>
                <td style="vertical-align: top; font-style: italic; color: #333;">{!! nl2br(e($medicalRecord->doctor_note)) !!}</td>
            </tr>
            @endif
        </table>

        {{-- Treatment plan & prescriptions --}}
        @if($medicalRecord->prescriptions->count() > 0)
        <div class="section-title">II. PHÁC ĐỒ ĐIỀU TRỊ & CHỈ ĐỊNH DÙNG THUỐC</div>
        
        @foreach($medicalRecord->prescriptions as $prescription)
        <table class="rx-table">
            <thead>
                <tr>
                    <th style="width: 6%;">STT</th>
                    <th style="width: 44%;">Tên Vị thuốc / Phương pháp</th>
                    <th style="width: 18%; text-align: right;">Số lượng</th>
                    <th style="width: 32%;">Hướng dẫn chi tiết</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $stt = 1;
                    $pHerbs = $prescription->items->where('item_type', 'herb');
                    $pExtHerbs = $prescription->items->where('item_type', 'external_herb');
                    $pServices = $prescription->items->where('item_type', 'service');
                @endphp

                {{-- Group A --}}
                @if($pHerbs->count() > 0)
                    <tr style="background: #f5f5f5;">
                        <td colspan="4" class="rx-group-title">🌱 A. THUỐC UỐNG DẠNG SẮC (Kê: {{ $prescription->num_of_doses ?? 1 }} thang)</td>
                    </tr>
                    @foreach($pHerbs as $item)
                    <tr>
                        <td>{{ $stt++ }}</td>
                        <td style="font-weight: bold;">{{ $item->display_name }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ floatval($item->quantity) }} {{ $item->unit }} / thang</td>
                        <td style="font-size: 9.5pt; color: #333;">{{ $item->dosage }} {{ $item->note ? ' ('.$item->note.')' : '' }}</td>
                    </tr>
                    @endforeach
                @endif

                {{-- Group B --}}
                @if($pExtHerbs->count() > 0)
                    <tr style="background: #f5f5f5;">
                        <td colspan="4" class="rx-group-title">🩹 B. THUỐC BÓ / THUỐC DÙNG NGOÀI DA</td>
                    </tr>
                    @foreach($pExtHerbs as $item)
                    <tr>
                        <td>{{ $stt++ }}</td>
                        <td style="font-weight: bold;">
                            {{ $item->display_name }}
                        </td>
                        <td style="text-align: right; font-weight: bold;">{{ floatval($item->quantity) }} {{ $item->unit }}</td>
                        <td style="font-size: 9.5pt; color: #333;">
                            @if($item->usage_area) <strong>Vùng đắp:</strong> {{ $item->usage_area }}<br>@endif
                            {{ $item->usage_instruction ?? $item->dosage }}
                        </td>
                    </tr>
                    @endforeach
                @endif

                {{-- Group C --}}
                @if($pServices->count() > 0)
                    <tr style="background: #f5f5f5;">
                        <td colspan="4" class="rx-group-title">👐 C. VẬT LÝ TRỊ LIỆU / DỊCH VỤ TÁC ĐỘNG NGOÀI</td>
                    </tr>
                    @foreach($pServices as $item)
                    <tr>
                        <td>{{ $stt++ }}</td>
                        <td style="font-weight: bold;">{{ $item->display_name }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ $item->sessions ? $item->sessions . ' buổi' : '1 lần' }}</td>
                        <td style="font-size: 9.5pt; color: #333;">
                            @if($item->usage_area) <strong>Vùng:</strong> {{ $item->usage_area }}<br>@endif
                            {{ $item->usage_instruction ?? $item->note }}
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        @if($prescription->public_instruction)
        <div style="padding: 6px; font-size: 9.5pt; color: #222; font-style: italic; background: #fafafa; border: 1px dashed #111; margin-bottom: 8px; line-height: 1.4;">
            <strong>Hướng dẫn đặc biệt của thầy thuốc:</strong> {{ $prescription->public_instruction }}
        </div>
        @endif

        <div style="font-size: 10pt; color: #111; background: #fafafa; border: 1px solid #111; padding: 6px 10px; display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 15px;">
            @if($pHerbs->count() > 0)
                <span><strong>Tổng số thang sắc:</strong> {{ $prescription->num_of_doses ?? 1 }} thang</span>
            @endif
            @if($prescription->course_days)
                <span><strong>Thời gian liệu trình:</strong> {{ $prescription->course_days }} ngày</span>
            @endif
            @if($prescription->usage_instruction)
                <span><strong>Cách dùng chung:</strong> {{ $prescription->usage_instruction }}</span>
            @endif
            @if($prescription->follow_up_date)
                <span style="color: #000; font-weight: bold;">📅 Hẹn ngày tái khám: {{ $prescription->follow_up_date->format('d/m/Y') }}</span>
            @endif
        </div>
        @endforeach
        @endif

        {{-- Signatures --}}
        <div class="footer">
            <div class="signature">
                <p>Ngày .... tháng .... năm 20...</p>
                <p class="title">Người bệnh</p>
                <p style="margin-top: 50px;">(Ký và ghi rõ họ tên)</p>
            </div>
            <div class="signature">
                <p>Ngày {{ now()->format('d') }} tháng {{ now()->format('m') }} năm {{ now()->format('Y') }}</p>
                <p class="title">Thầy thuốc điều trị</p>
                <p style="margin-top: 50px;"><strong>{{ $medicalRecord->staff->name ?? 'Admin' }}</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
