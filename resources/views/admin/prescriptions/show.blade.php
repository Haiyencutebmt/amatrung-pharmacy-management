@extends('layouts.admin')

@section('title', 'Đơn Điều Trị #' . $prescription->id . ' — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div style="width: 44px; height: 44px; background: #e0f2fe; color: #0369a1; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; border: 1px solid #cbd5e1; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
        <span>💊</span>
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Chi tiết Đơn điều trị</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Mã đơn: DT{{ str_pad($prescription->id, 5, '0', STR_PAD_LEFT) }}</p>
    </div>
</div>
@endsection

@section('content')
<style>
    /* Paper sizing & print styling matching medical record print view */
    @page {
        size: A4 portrait;
        margin: 5mm;
    }
    .paper-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 0.75rem 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .size-toggle {
        display: flex;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 0.2rem;
        gap: 0.2rem;
    }
    .size-toggle button,
    .size-toggle a {
        border: none;
        background: none;
        padding: 0.4rem 1rem;
        font-size: 0.85rem;
        font-weight: 700;
        color: #475569;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }
    .size-toggle button.active,
    .size-toggle a.active {
        background: #16a34a;
        color: #fff;
    }
    .print-now-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.6rem 1.25rem;
        border-radius: 8px;
        font-size: 0.88rem;
        font-weight: 700;
        border: none;
        background: #2563eb;
        color: #fff;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid #1d4ed8;
    }
    .print-now-btn:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(37, 99, 235, 0.15);
    }
    .btn-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.6rem 1.25rem;
        border-radius: 8px;
        font-size: 0.88rem;
        font-weight: 700;
        text-decoration: none;
        color: #475569;
        background: #fff;
        border: 1px solid #cbd5e1;
        transition: all 0.2s;
    }
    .btn-back-btn:hover {
        background: #f8fafc;
        color: #1e293b;
    }

    /* Paper document (Print preview layout) */
    .paper-wrapper {
        display: flex;
        justify-content: center;
        background: #f8fafc;
        padding: 2rem 0;
        border-radius: 16px;
        border: 1px dashed #cbd5e1;
        margin-bottom: 2rem;
    }
    .paper {
        background: #fff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        border-radius: 4px;
        margin: 0 auto;
        transition: width 0.3s, min-height 0.3s;
        overflow: hidden;
        font-family: "Times New Roman", Times, serif;
        color: #000;
    }
    .paper.a4 {
        width: 210mm;
        min-height: 297mm;
        padding: 12mm;
    }

    /* Document inner styles */
    .doc-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }
    .doc-clinic h1 {
        margin: 0;
        font-size: 15pt;
        font-weight: 800;
        color: #000;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .doc-clinic p {
        margin: 2px 0;
        font-size: 9.5pt;
        color: #111;
    }
    .doc-meta {
        text-align: right;
        font-size: 10pt;
        color: #111;
    }
    .doc-title {
        text-align: center;
        margin: 10px 0 15px 0;
    }
    .doc-title h2 {
        margin: 0;
        font-size: 17pt;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .patient-info-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
        font-size: 10.5pt;
    }
    .patient-info-table td {
        padding: 5px;
        vertical-align: top;
    }
    .patient-border-box {
        border: 1px solid #000;
        padding: 8px 12px;
        margin-bottom: 15px;
        border-radius: 4px;
    }
    .prescription-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
        font-size: 10.5pt;
    }
    .prescription-table th, .prescription-table td {
        border: 1px solid #000;
        padding: 6px 8px;
        text-align: left;
    }
    .prescription-table th {
        background-color: #f2f2f2;
        font-weight: bold;
        font-size: 10pt;
        text-transform: uppercase;
    }
    .rx-group-title {
        background-color: #eaeaea;
        font-weight: bold;
        padding: 5px 8px;
        font-size: 10pt;
        border: 1px solid #000;
    }
    .usage-instruction-box {
        margin-top: 12px;
        padding: 10px;
        border: 1px dashed #000;
        font-size: 10.5pt;
        border-radius: 4px;
        background: #fafafa;
    }
    .doc-footer {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
    }
    .doc-sig {
        text-align: center;
        width: 200px;
        font-size: 10pt;
    }
    .doc-sig .title {
        font-weight: 700;
        margin-bottom: 50px;
    }
    .doc-sig .name {
        font-weight: 700;
    }

    @media print {
        body * {
            visibility: hidden;
        }
        .printable-area-wrapper, .printable-area-wrapper * {
            visibility: visible;
        }
        .printable-area-wrapper {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            background: #fff !important;
        }
        .paper {
            border: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .paper.a4 {
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
    }
</style>

<div class="no-print paper-toolbar">
    <a href="{{ route('admin.medical-records.show', $prescription->medical_record_id) }}" class="btn-back-btn">
        ← Quay lại Bệnh án
    </a>
    <div style="display: flex; gap: 0.75rem; align-items: center;">
        <div class="size-toggle">
            <a href="{{ route('admin.prescriptions.show', ['prescription' => $prescription, 'type' => 'patient']) }}" class="{{ $printType === 'patient' ? 'active' : '' }}">Phiếu bệnh nhân</a>
            <a href="{{ route('admin.prescriptions.show', ['prescription' => $prescription, 'type' => 'internal']) }}" class="{{ $printType === 'internal' ? 'active' : '' }}">Phiếu {{ mb_strtolower($prescription->internalPrintLabel()) }}</a>
        </div>
        <button class="print-now-btn" onclick="window.print()">
            🖨️ In phiếu ngay
        </button>
    </div>
</div>

<div class="printable-area-wrapper paper-wrapper">
    <div class="paper a4" id="paperDoc">
        @include('admin.prescriptions.modal_content', ['printType' => $printType])
    </div>
</div>
@endsection
