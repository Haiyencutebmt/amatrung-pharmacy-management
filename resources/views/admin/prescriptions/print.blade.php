@php
    $printType = ($printType ?? request('type')) === 'internal' ? 'internal' : 'patient';
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $printType === 'internal' ? 'Phiếu ' . $prescription->internalPrintLabel() . ' Nội Bộ' : 'Phiếu Điều Trị Bệnh Nhân' }} #{{ $prescription->id }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 5mm;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.35;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
        }
        .doc-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
        }
        .doc-clinic h1 {
            margin: 0;
            font-size: 16pt;
            text-transform: uppercase;
        }
        .doc-clinic p {
            margin: 2px 0;
            font-size: 10pt;
        }
        .doc-title {
            text-align: center;
            margin: 10px 0;
        }
        .doc-title h2 {
            margin: 0;
            font-size: 18pt;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .patient-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10.5pt;
        }
        .prescription-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .prescription-table th,
        .prescription-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            text-align: left;
        }
        .prescription-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 11pt;
        }
        .rx-group-title {
            background-color: #eaeaea;
            font-weight: bold;
            padding: 4px 8px;
            border: 1px solid #000;
        }
        .usage-instruction-box {
            margin-top: 10px;
            padding: 8px;
            border: 1px dashed #000;
            font-size: 11pt;
        }
        .doc-footer {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
        }
        .doc-sig {
            text-align: center;
            width: 200px;
        }
        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 8px;
            z-index: 9999;
            font-family: Arial, sans-serif;
        }
        .no-print a,
        .no-print button {
            background: #fff;
            color: #1e293b;
            border: 1px solid #cbd5e1;
            padding: 10px 14px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .no-print .primary {
            background: #2563eb;
            border-color: #1d4ed8;
            color: #fff;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <a href="{{ route('admin.prescriptions.print', ['prescription' => $prescription, 'type' => 'patient']) }}">Phiếu bệnh nhân</a>
        @if($prescription->medicalRecord->case_type !== 'musculoskeletal')
        <a href="{{ route('admin.prescriptions.print', ['prescription' => $prescription, 'type' => 'internal']) }}">Phiếu {{ mb_strtolower($prescription->internalPrintLabel()) }}</a>
        @endif
        <button class="primary" onclick="window.print()">In phiếu này</button>
    </div>

    <div class="container">
        @include('admin.prescriptions.partials.document', [
            'prescription' => $prescription,
            'printType' => $printType,
        ])
    </div>

    @if(request('auto_print') == '1')
        <script>
            window.addEventListener('load', function () {
                setTimeout(function () {
                    window.focus();
                    window.print();
                }, 250);
            });
        </script>
    @endif
</body>
</html>
