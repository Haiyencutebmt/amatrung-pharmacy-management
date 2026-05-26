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
            line-height: 1.4;
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
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }
        .clinic-info h1 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
            font-weight: bold;
        }
        .clinic-info p {
            margin: 2px 0;
            font-size: 9.5pt;
        }
        .doc-title {
            text-align: center;
            margin: 20px 0;
        }
        .doc-title h2 {
            margin: 0;
            font-size: 16pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
        }
        .doc-title p {
            margin: 5px 0 0;
            font-style: italic;
            font-size: 10pt;
        }
        .herb-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .herb-table th, .herb-table td {
            border: 1px solid #000;
            padding: 8px 10px;
            text-align: left;
            font-size: 10pt;
            vertical-align: middle;
        }
        .herb-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10pt;
            text-align: center;
        }
        .text-center {
            text-align: center !important;
        }
        .text-right {
            text-align: right !important;
        }
        .footer {
            margin-top: 35px;
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
            background: #16a34a;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-family: Arial, sans-serif;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 9999;
            transition: background-color 0.2s;
        }
        .no-print:hover {
            background: #15803d;
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
            <div style="text-align: right; font-size: 9.5pt;">
                <p>Bác sĩ phụ trách: <strong>BS. Y Hiếu Niê</strong></p>
                <p>Ngày in: {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="doc-title">
            <h2>{{ $title }}</h2>
            <p>Tổng số dược liệu hiển thị: <strong>{{ $herbs->count() }}</strong></p>
        </div>

        <table class="herb-table">
            <thead>
                <tr>
                    <th style="width: 40px;">STT</th>
                    <th>Tên dược liệu</th>
                    <th>Phân loại</th>
                    <th style="width: 100px;">Cách dùng</th>
                    <th style="width: 100px;" class="text-right">Tồn kho</th>
                    <th style="width: 80px;" class="text-center">Đơn vị</th>
                    <th style="width: 120px;" class="text-center">Hạn sử dụng</th>
                    <th style="width: 120px;" class="text-center">Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                @forelse($herbs as $index => $herb)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td style="font-weight: bold;">{{ $herb->name }}</td>
                    <td>{{ $herb->category ?? '—' }}</td>
                    <td class="text-center">{{ $herb->usage_type ?? '—' }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ floatval($herb->stock_quantity) }}</td>
                    <td class="text-center">{{ $herb->unit }}</td>
                    <td class="text-center">
                        @if($herb->expiry_date)
                            {{ $herb->expiry_date->format('d/m/Y') }}
                            @if($herb->isExpired())
                                <span style="color: red; font-weight: bold; font-size: 8pt;"><br>(Hết hạn)</span>
                            @endif
                        @else
                            —
                        @endif
                    </td>
                    <td class="text-center">
                        @if($herb->status == 'active')
                            Đang dùng
                        @elseif($herb->status == 'out_of_stock')
                            Hết hàng
                        @else
                            Ngừng sử dụng
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; font-style: italic; color: #666;">
                        Không tìm thấy dược liệu nào theo bộ lọc đã chọn.
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
                <p style="margin-top: 60px; font-style: italic; font-weight: normal; font-size: 10pt;">(Ký và ghi rõ họ tên)</p>
            </div>
        </div>
    </div>

    <script>
        // Tự động kích hoạt in khi được mở
        window.onload = function() {
            // Đợi một khoảng ngắn để trình duyệt render hoàn chỉnh rồi mở hộp thoại in
            setTimeout(function() {
                window.print();
            }, 300);
        }
    </script>
</body>
</html>
