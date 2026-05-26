<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }
        .clinic-info h1 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
        }
        .clinic-info p {
            margin: 2px 0;
            font-size: 9pt;
        }
        .doc-title {
            text-align: center;
            margin: 15px 0;
        }
        .doc-title h2 {
            margin: 0;
            font-size: 16pt;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .doc-title p {
            margin: 5px 0 0;
            font-style: italic;
            font-size: 10pt;
        }
        .patient-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .patient-table th, .patient-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
            font-size: 10pt;
        }
        .patient-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10pt;
        }
        .text-center {
            text-align: center !important;
        }
        .footer {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            font-size: 11pt;
        }
        .signature {
            text-align: center;
            width: 250px;
        }
        .signature p {
            margin: 3px 0;
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
    <button class="no-print" onclick="window.print()">🖨️ Bấm vào đây để IN DANH SÁCH</button>

    <div class="container">
        <div class="header">
            <div class="clinic-info">
                <h1>NHÀ THUỐC AMATRUNG</h1>
                <p>Địa chỉ: 54/36 AmaJhao, Phường Tân Lập, Tỉnh Đắk Lắk</p>
                <p>Hotline: 0983.009.748 - 0918.303.983 | MST: 066070008130</p>
            </div>
            <div style="text-align: right; font-size: 9pt;">
                <p>Bác sĩ phụ trách: <strong>BS. Y Hiếu Niê</strong></p>
                <p>Ngày in: {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="doc-title">
            <h2>{{ $title }}</h2>
            <p>Tổng cộng: <strong>{{ $patients->count() }}</strong> bệnh nhân được hiển thị</p>
        </div>

        <table class="patient-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">STT</th>
                    <th style="width: 80px;">Mã BN</th>
                    <th style="width: 180px;">Họ tên bệnh nhân</th>
                    <th style="width: 80px;" class="text-center">Ngày sinh</th>
                    <th style="width: 70px;" class="text-center">Giới tính</th>
                    <th style="width: 100px;">Điện thoại</th>
                    <th>Địa chỉ thường trú</th>
                    <th>Ghi chú y tế / Tiền sử</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $index => $patient)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td style="font-weight: bold;">{{ $patient->patient_code }}</td>
                    <td style="font-weight: bold;">{{ $patient->full_name }}</td>
                    <td class="text-center">{{ $patient->date_of_birth ? $patient->date_of_birth->format('d/m/Y') : '—' }}</td>
                    <td class="text-center">{{ $patient->gender_label }}</td>
                    <td>
                        @if($patient->phone)
                            {{ $patient->phone }}
                        @elseif($patient->guardian_phone)
                            {{ $patient->guardian_phone }} <small>({{ $patient->guardian_name }})</small>
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $patient->address ?? '—' }}</td>
                    <td>{{ $patient->note ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; font-style: italic; color: #666;">
                        Không có bệnh nhân nào trong bộ lọc thời gian đã chọn.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <div class="signature">
                <!-- Trống bên trái -->
            </div>
            <div class="signature">
                <p>Ngày {{ now()->format('d') }} tháng {{ now()->format('m') }} năm {{ now()->format('Y') }}</p>
                <p><strong>Người lập danh sách</strong></p>
                <p style="margin-top: 50px; font-style: italic; font-weight: normal; font-size: 10pt;">(Ký và ghi rõ họ tên)</p>
            </div>
        </div>
    </div>

    <script>
        // Tự động in khi được tải bên trong iframe ẩn
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('auto_print') === '1') {
            window.onload = function() {
                window.focus();
                window.print();
            }
        }
    </script>
</body>
</html>
