@extends('layouts.admin')

@section('title', __('messages.create_berita_acara'))

@section('content')
<!-- Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 -->
<!-- Hallmark · genre: modern-minimal · macrostructure: workbench · design-system: design.md · designed-as-app -->
<style>
    .ba-studio-container {
        padding-bottom: 50px;
    }
    .ba-card {
        background: var(--color-paper-1, #1e293b);
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }
    .ba-card-header {
        padding: 10px 14px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.12);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .ba-card-title, h2.ba-card-title {
        font-family: 'Outfit', sans-serif !important;
        font-size: 0.82rem !important;
        font-weight: 700 !important;
        color: var(--color-ink-0, #f8fafc) !important;
        margin: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        line-height: 1.25 !important;
    }
    .ba-card-title i, h2.ba-card-title i {
        font-size: 0.82rem !important;
        flex-shrink: 0 !important;
    }
    .ba-ocr-dropzone {
        border: 2px dashed rgba(99, 102, 241, 0.4);
        background: rgba(99, 102, 241, 0.03);
        border-radius: 14px;
        padding: 16px 14px;
        text-align: center;
        cursor: pointer;
        transition: all 180ms cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ba-ocr-dropzone:hover, .ba-ocr-dropzone.dragover {
        border-color: #6366f1;
        background: rgba(99, 102, 241, 0.08);
        transform: translateY(-1px);
    }
    .ba-ocr-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(99, 102, 241, 0.2) !important;
        border: 1px solid rgba(99, 102, 241, 0.35);
        color: #818cf8 !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 8px;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
    }
    .ba-input {
        background: rgba(15, 23, 42, 0.35);
        border: 1px solid rgba(148, 163, 184, 0.22);
        border-radius: 10px;
        color: var(--color-ink-0, #f8fafc);
        font-size: 0.84rem;
        padding: 7px 11px;
        height: 38px;
        width: 100%;
        transition: border-color 180ms ease, box-shadow 180ms ease;
    }
    .ba-input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
        outline: none;
        background: rgba(15, 23, 42, 0.5);
    }
    /* File Selector Button Custom Styling */
    .ba-input[type="file"] {
        padding: 4px 8px !important;
        height: 38px !important;
        display: flex !important;
        align-items: center !important;
    }
    .ba-input[type="file"]::file-selector-button,
    .ba-input[type="file"]::-webkit-file-upload-button {
        background: #334155 !important;
        color: #f8fafc !important;
        border: 1px solid #475569 !important;
        border-radius: 6px !important;
        padding: 3px 10px !important;
        font-size: 0.78rem !important;
        font-weight: 600 !important;
        margin-right: 10px !important;
        cursor: pointer !important;
        transition: all 180ms ease !important;
        height: 28px !important;
        line-height: 1 !important;
    }
    .ba-input[type="file"]::file-selector-button:hover,
    .ba-input[type="file"]::-webkit-file-upload-button:hover {
        background: #475569 !important;
        color: #ffffff !important;
        border-color: #64748b !important;
    }
    body.light-mode .ba-input[type="file"]::file-selector-button,
    body.light-mode .ba-input[type="file"]::-webkit-file-upload-button {
        background: #e2e8f0 !important;
        color: #0f172a !important;
        border: 1px solid #cbd5e1 !important;
    }
    body.light-mode .ba-input[type="file"]::file-selector-button:hover,
    body.light-mode .ba-input[type="file"]::-webkit-file-upload-button:hover {
        background: #cbd5e1 !important;
        color: #020617 !important;
    }
    .ba-label {
        font-size: 0.74rem;
        font-weight: 600;
        color: var(--color-ink-2, #94a3b8);
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 4px;
        display: block;
    }
    @media (max-width: 575.98px) {
        .ba-card-header {
            padding: 8px 10px !important;
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 6px !important;
        }
        .ba-card-title, h2.ba-card-title {
            font-size: 0.72rem !important;
            line-height: 1.25 !important;
        }
        .ba-card-title i, h2.ba-card-title i {
            font-size: 0.72rem !important;
        }
        .ba-ocr-dropzone {
            padding: 12px 8px;
        }
        .ba-ocr-icon {
            width: 32px;
            height: 32px;
            font-size: 1rem;
            margin-bottom: 4px;
        }
        .ba-label {
            font-size: 0.68rem;
        }
        .ba-input {
            font-size: 0.8rem;
            height: 35px;
            padding: 5px 9px;
        }
    }
    .ba-input[type="date"] {
        color-scheme: dark;
        color: var(--color-ink-0, #f8fafc) !important;
    }
    body.light-mode .ba-input[type="date"] {
        color-scheme: light;
        color: #0f172a !important;
    }
    .ba-input[type="date"]::-webkit-datetime-edit-text,
    .ba-input[type="date"]::-webkit-datetime-edit-month-field,
    .ba-input[type="date"]::-webkit-datetime-edit-day-field,
    .ba-input[type="date"]::-webkit-datetime-edit-year-field {
        color: rgba(248, 250, 252, 0.7);
    }
    body.light-mode .ba-input[type="date"]::-webkit-datetime-edit-text,
    body.light-mode .ba-input[type="date"]::-webkit-datetime-edit-month-field,
    body.light-mode .ba-input[type="date"]::-webkit-datetime-edit-day-field,
    body.light-mode .ba-input[type="date"]::-webkit-datetime-edit-year-field {
        color: rgba(15, 23, 42, 0.7);
    }
    .ba-input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(0.85);
        cursor: pointer;
        opacity: 0.85;
    }
    body.light-mode .ba-input[type="date"]::-webkit-calendar-picker-indicator {
        filter: none;
        opacity: 0.85;
    }
    .ba-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--color-ink-2, #94a3b8);
        margin-bottom: 4px;
        display: block;
    }
    .ba-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.83rem;
    }
    .ba-table th {
        background: rgba(15, 23, 42, 0.4);
        color: var(--color-ink-2, #94a3b8);
        font-weight: 600;
        padding: 8px 6px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.15);
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    .ba-table td {
        padding: 6px 4px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.08);
        vertical-align: middle;
    }
    .ba-table .form-control-sm {
        font-size: 0.8rem;
        padding: 4px 6px;
        height: auto;
    }
    /* Live Preview A4 Paper */
    .ba-preview-wrapper {
        position: sticky;
        top: 80px;
    }
    .ba-paper-preview {
        background: #ffffff;
        color: #000000;
        border-radius: 8px;
        padding: 26px 22px 30px 22px;
        box-shadow: 0 14px 40px rgba(0, 0, 0, 0.28), 0 2px 8px rgba(0, 0, 0, 0.12);
        font-family: 'Inter', sans-serif;
        font-size: 9px;
        line-height: 1.45;
        min-height: 580px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        box-sizing: border-box;
        max-height: calc(100vh - 120px);
        overflow-y: auto;
    }
    .prev-title {
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        font-weight: 700;
        text-align: center;
        color: #000000;
        margin: 10px 0 8px 0;
        text-decoration: underline;
    }
    .prev-table {
        width: 100%;
        border-collapse: collapse;
        margin: 8px 0;
        font-size: 7.5px;
    }
    .prev-table th, .prev-table td {
        border: 1px solid #000000;
        padding: 4px 2px;
    }
    .prev-table th {
        background: #ffffff;
        font-weight: 700;
        text-align: center;
        color: #000;
        text-transform: uppercase;
        line-height: 1.2;
    }
    /* Mobile Responsiveness */
    @media (max-width: 991.98px) {
        .ba-studio-container {
            padding-bottom: 90px !important;
            overflow-x: clip;
        }
        .ba-preview-wrapper {
            position: static !important;
            margin-top: 15px;
        }
        .ba-paper-preview {
            max-height: none !important;
            overflow-x: auto;
            padding: 18px 14px;
        }
        .mobile-studio-nav {
            display: flex !important;
        }
    }
    /* Action Button Styles - High Contrast & Dark/Light Compatible */
    .ba-btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 7px 15px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 600;
        transition: all 180ms cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none !important;
        cursor: pointer;
        line-height: 1.25;
        white-space: nowrap;
    }
    .ba-btn-secondary {
        background: #334155 !important;
        color: #f8fafc !important;
        border: 1px solid #475569 !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.18);
    }
    .ba-btn-secondary:hover {
        background: #475569 !important;
        color: #ffffff !important;
        border-color: #64748b !important;
        transform: translateY(-1px);
    }
    body.light-mode .ba-btn-secondary {
        background: #e2e8f0 !important;
        color: #0f172a !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    body.light-mode .ba-btn-secondary:hover {
        background: #cbd5e1 !important;
        color: #020617 !important;
        border-color: #94a3b8 !important;
    }

    /* Redesigned Hallmark Primary & Simpan Cetak Buttons */
    .ba-btn-simpan-cetak {
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        color: #ffffff !important;
        border: 1px solid rgba(52, 211, 153, 0.45) !important;
        border-radius: 12px !important;
        padding: 9px 20px !important;
        font-family: 'Outfit', sans-serif !important;
        font-weight: 700 !important;
        font-size: 0.88rem !important;
        letter-spacing: 0.015em !important;
        box-shadow: 0 4px 18px rgba(5, 150, 105, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.25) !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 9px !important;
        transition: all 220ms cubic-bezier(0.16, 1, 0.3, 1) !important;
        cursor: pointer !important;
        text-decoration: none !important;
        white-space: nowrap !important;
    }
    .ba-btn-simpan-cetak:hover {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        border-color: rgba(52, 211, 153, 0.8) !important;
        transform: translateY(-2px) scale(1.02) !important;
        box-shadow: 0 8px 26px rgba(16, 185, 129, 0.48), inset 0 1px 0 rgba(255, 255, 255, 0.35) !important;
    }
    .ba-btn-simpan-cetak:active {
        transform: translateY(0) scale(0.98) !important;
        box-shadow: 0 2px 8px rgba(5, 150, 105, 0.3) !important;
    }
    .ba-btn-simpan-cetak .ba-btn-icon-badge {
        width: 26px;
        height: 26px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.22);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        flex-shrink: 0;
        transition: transform 200ms cubic-bezier(0.34, 1.56, 0.64, 1), background 200ms ease;
    }
    .ba-btn-simpan-cetak:hover .ba-btn-icon-badge {
        transform: rotate(-10deg) scale(1.15);
        background: rgba(255, 255, 255, 0.32);
    }

    .ba-btn-primary-action {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%) !important;
        color: #ffffff !important;
        border: 1px solid rgba(129, 140, 248, 0.45) !important;
        border-radius: 12px !important;
        padding: 9px 20px !important;
        font-family: 'Outfit', sans-serif !important;
        font-weight: 700 !important;
        font-size: 0.88rem !important;
        letter-spacing: 0.015em !important;
        box-shadow: 0 4px 18px rgba(79, 70, 229, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.25) !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 9px !important;
        transition: all 220ms cubic-bezier(0.16, 1, 0.3, 1) !important;
        cursor: pointer !important;
        text-decoration: none !important;
        white-space: nowrap !important;
    }
    .ba-btn-primary-action:hover {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
        border-color: rgba(165, 180, 252, 0.8) !important;
        transform: translateY(-2px) scale(1.02) !important;
        box-shadow: 0 8px 26px rgba(99, 102, 241, 0.48), inset 0 1px 0 rgba(255, 255, 255, 0.35) !important;
    }
    .ba-btn-primary-action:active {
        transform: translateY(0) scale(0.98) !important;
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.3) !important;
    }
    .ba-btn-primary-action .ba-btn-icon-badge {
        width: 26px;
        height: 26px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.22);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        flex-shrink: 0;
        transition: transform 200ms cubic-bezier(0.34, 1.56, 0.64, 1), background 200ms ease;
    }
    .ba-btn-primary-action:hover .ba-btn-icon-badge {
        transform: scale(1.15);
        background: rgba(255, 255, 255, 0.32);
    }
    .ba-page-title {
        font-family: 'Outfit', sans-serif;
        font-size: 1.12rem;
        font-weight: 700;
        color: var(--color-ink-0, #f8fafc);
        line-height: 1.3;
    }
    @media (max-width: 575.98px) {
        .ba-page-title {
            font-size: 0.92rem !important;
        }
        .ba-page-subtitle {
            font-size: 0.72rem !important;
        }
        .ba-header-actions {
            margin-top: 8px;
            width: 100%;
            justify-content: flex-start !important;
        }
        .ba-btn-action {
            padding: 6px 12px;
            font-size: 0.78rem;
        }
    }
</style>

<div class="content-header py-2">
    <div class="container-fluid">
        <div class="row align-items-center mb-1">
            <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                <h1 class="m-0 font-weight-bold ba-page-title d-flex align-items-center">
                    <i class="fas fa-file-signature text-primary mr-2" style="font-size: 1.15em;"></i>
                    <span>{{ __('messages.create_berita_acara') }}</span>
                </h1>
                <p class="text-muted mb-0 ba-page-subtitle" style="font-size: 0.78rem;">
                    Upload foto/scan Surat Jalan untuk mengekstrak barang otomatis ke format resmi Berita Acara OK.
                </p>
            </div>
            <div class="col-12 col-sm-6 text-sm-right d-flex align-items-center justify-content-start justify-content-sm-end ba-header-actions">
                <a href="{{ route('berita-acara.index') }}" class="ba-btn-action ba-btn-secondary mr-2">
                    <i class="fas fa-arrow-left mr-1"></i>
                    <span>{{ __('messages.back') }}</span>
                </a>
                <button type="reset" form="baForm" class="ba-btn-action ba-btn-secondary" onclick="setTimeout(updatePreview, 50)">
                    <i class="fas fa-undo mr-1"></i>
                    <span>{{ __('messages.reset') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="content ba-studio-container">
    <div class="container-fluid">
        <form action="{{ route('berita-acara.store') }}" method="POST" enctype="multipart/form-data" id="baForm">
            @csrf
            <input type="hidden" name="and_print" id="andPrintInput" value="0">

            <!-- Mobile View Tab Switcher (Visible only on < 992px) -->
            <div class="mobile-studio-nav mb-3 p-1 rounded-pill" style="display: none; background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(148, 163, 184, 0.18);">
                <button type="button" class="btn btn-sm btn-primary flex-fill rounded-pill active" id="btnTabForm" onclick="switchMobileStudioTab('form')" style="font-weight: 600; font-size: 0.84rem; padding: 7px 12px;">
                    <i class="fas fa-edit mr-1"></i> Form &amp; Scanner
                </button>
                <button type="button" class="btn btn-sm flex-fill rounded-pill text-muted" id="btnTabPreview" onclick="switchMobileStudioTab('preview')" style="font-weight: 600; font-size: 0.84rem; padding: 7px 12px;">
                    <i class="fas fa-eye mr-1"></i> Pratinjau Dokumen
                </button>
            </div>

            <div class="row">
                <!-- Left Column: OCR Scanner & Form Fields -->
                <div class="col-lg-7 col-xl-7" id="studioColForm">
                    
                    <!-- AI Vision OCR Dropzone Card -->
                    <div class="ba-card mb-3">
                        <div class="ba-card-header" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(168, 85, 247, 0.05) 100%);">
                            <h2 class="ba-card-title">
                                <i class="fas fa-magic" style="color: #818cf8;"></i>
                                <span>AI Vision Scanner Surat Jalan</span>
                            </h2>
                            <span class="badge badge-primary px-2 py-1" style="border-radius: 6px; font-size: 0.72rem; background-color: #6366f1;">
                                <i class="fas fa-bolt mr-1"></i> Gemini AI Vision
                            </span>
                        </div>
                        <div class="p-3">
                            <div class="ba-ocr-dropzone" id="ocrDropzone">
                                <input type="file" id="ocrFileInput" accept="image/jpeg,image/png,image/webp,application/pdf" style="display: none;" onchange="handleFileSelect(event)">
                                <input type="file" id="ocrCameraInput" accept="image/*" capture="environment" style="display: none;" onchange="handleFileSelect(event)">
                                <div class="ba-ocr-icon" id="ocrIcon">
                                    <i class="fas fa-cloud-arrow-up"></i>
                                </div>
                                <h6 class="font-weight-bold mb-1" style="color: var(--color-ink-0); font-size: 0.92rem;">
                                    Tarik Foto / Scan Surat Jalan ke Sini
                                </h6>
                                <p class="text-muted mb-2" style="font-size: 0.78rem;">
                                    AI otomatis membaca No Surat Jalan, PO, Tanggal, Supplier, &amp; Item Barang ke format Berita Acara OK.
                                </p>
                                <div class="d-flex flex-wrap gap-2 justify-content-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary px-3 py-1" onclick="document.getElementById('ocrFileInput').click()" style="border-radius: 8px; font-size: 0.8rem; font-weight: 500;">
                                        <i class="fas fa-folder-open mr-1"></i> Pilih Berkas / PDF
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info px-3 py-1 d-inline-flex d-md-none" onclick="document.getElementById('ocrCameraInput').click()" style="border-radius: 8px; font-size: 0.8rem; font-weight: 500;">
                                        <i class="fas fa-camera mr-1"></i> Ambil Foto Kamera
                                    </button>
                                </div>
                                <div id="ocrStatus" style="display: none;" class="mt-2">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <div class="spinner-border spinner-border-sm text-primary mr-2" role="status"></div>
                                        <span class="font-weight-600 text-info" id="ocrStatusText" style="font-size: 0.83rem;">Memproses scan dokumen dengan AI...</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="ocrSuccessAlert" class="alert alert-success mt-2 mb-0" style="display: none; border-radius: 8px; font-size: 0.82rem;">
                                <i class="fas fa-check-circle mr-1"></i> <span id="ocrSuccessMsg">Data Surat Jalan berhasil diekstrak!</span>
                            </div>
                        </div>
                    </div>

                    <!-- Header Dokumen Berita Acara Card -->
                    <div class="ba-card mb-3">
                        <div class="ba-card-header">
                            <h2 class="ba-card-title">
                                <i class="fas fa-file-invoice text-info"></i>
                                <span>Informasi Surat Berita Acara OK</span>
                            </h2>
                        </div>
                        <div class="p-3">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="ba-label">Nomor Berita Acara <span class="text-danger">*</span></label>
                                    <input type="text" name="nomor_ba" id="nomor_ba" class="ba-input font-weight-bold" style="font-family: 'JetBrains Mono', monospace;" value="{{ old('nomor_ba', \App\Models\BeritaAcara::generateNextNomorBa()) }}" required oninput="updatePreview()">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="ba-label">Kota / Lokasi Dokumen</label>
                                    <input type="text" name="lokasi" id="lokasi" class="ba-input" value="{{ old('lokasi', 'Cikande') }}" oninput="updatePreview()">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="ba-label">Hal Surat</label>
                                    <input type="text" name="hal" id="hal" class="ba-input" value="{{ old('hal', 'Pemberitahuan Barang/Jasa OK') }}" oninput="updatePreview()">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="ba-label">Lampiran</label>
                                    <input type="text" name="lampiran" id="lampiran" class="ba-input" value="{{ old('lampiran', '-') }}" oninput="updatePreview()">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="ba-label">Kepada Yth</label>
                                    <input type="text" name="kepada_yth" id="kepada_yth" class="ba-input" value="{{ old('kepada_yth', 'Ka. Pembelian') }}" oninput="updatePreview()">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="ba-label">Cc (Tembusan)</label>
                                    <input type="text" name="cc" id="cc" class="ba-input" value="{{ old('cc', 'Ka. Pabrik, Ka. Keuangan') }}" oninput="updatePreview()">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="ba-label">Nama Perusahaan</label>
                                    <input type="text" name="nama_perusahaan" id="nama_perusahaan" class="ba-input" value="{{ old('nama_perusahaan', 'PT. CBA Chemical Industry Pabrik') }}" oninput="updatePreview()">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="ba-label">Unit Penerima</label>
                                    <input type="text" name="unit_penerima" id="unit_penerima" class="ba-input" value="{{ old('unit_penerima', 'IT Pabrik') }}" oninput="updatePreview()">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="ba-label">Kepada (Pada Dokumen Asli)</label>
                                    <input type="text" name="kepada" id="kepada" class="ba-input" value="{{ old('kepada', 'Abdul / Bagas (IT Pabrik)') }}" placeholder="Bagas / Abdul (IT Pabrik)">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="ba-label">No. Surat Jalan Vendor <span class="text-danger">*</span></label>
                                    <input type="text" name="nomor_surat" id="nomor_surat" class="ba-input" placeholder="045/LGT/VII/2026" required value="{{ old('nomor_surat', '045/LGT/VII/2026') }}" oninput="updatePreview()">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="ba-label">No. PO Utama</label>
                                    <input type="text" name="nomor_po" id="nomor_po" class="ba-input" placeholder="2601436" value="{{ old('nomor_po', '2601436') }}" oninput="updatePreview()">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="ba-label">Tanggal Terima / Verifikasi <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_verifikasi" id="tanggal_verifikasi" class="ba-input" value="{{ old('tanggal_verifikasi', date('Y-m-d')) }}" required oninput="updatePreview()">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="ba-label">Tanggal Surat Jalan</label>
                                    <input type="date" name="tanggal_surat" id="tanggal_surat" class="ba-input" value="{{ old('tanggal_surat', date('Y-m-d')) }}" required oninput="updatePreview()">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="ba-label">Status Verifikasi / TTB</label>
                                    <input type="text" name="status_verifikasi" id="status_verifikasi" class="ba-input" value="{{ old('status_verifikasi', 'TTB / LGT ok') }}" placeholder="TTB / SJ ok 14/10 2026">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table Card -->
                    <div class="ba-card mb-3">
                        <div class="ba-card-header d-flex align-items-center justify-content-between">
                            <h2 class="ba-card-title mb-0">
                                <i class="fas fa-boxes text-warning mr-1"></i>
                                <span>Tabel Rincian Barang</span>
                            </h2>
                            <button type="button" class="btn btn-sm btn-primary px-3" onclick="addItemRow()" style="font-size: 0.76rem; font-weight: 600; border-radius: 8px; flex-shrink: 0; margin-left: auto;">
                                <i class="fas fa-plus mr-1"></i> Item
                            </button>
                        </div>
                        <div class="p-0">
                            <div class="d-flex align-items-center justify-content-between px-3 py-1.5 border-bottom" style="border-color: rgba(148, 163, 184, 0.1) !important; background: rgba(15, 23, 42, 0.25);">
                                <small class="text-muted" style="font-size: 0.72rem;">
                                    <i class="fas fa-arrows-alt-h mr-1 text-primary"></i> Geser tabel untuk mengisi semua kolom
                                </small>
                            </div>
                            <div class="table-responsive" style="-webkit-overflow-scrolling: touch;">
                                <table class="ba-table" id="itemsTable" style="min-width: 720px;">
                                    <thead>
                                        <tr>
                                            <th style="width: 30px; text-align: center;">No</th>
                                            <th style="width: 170px;">Item Description <span class="text-danger">*</span></th>
                                            <th>Keterangan (Keperluan)</th>
                                            <th style="width: 60px; text-align: center;">Qty</th>
                                            <th style="width: 60px;">UoM</th>
                                            <th style="width: 95px;">Supplier</th>
                                            <th style="width: 95px;">No. SJ</th>
                                            <th style="width: 80px;">No. PO</th>
                                            <th style="width: 30px; text-align: center;">#</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTableBody">
                                        <!-- Default Row 1 -->
                                        <tr class="item-row">
                                            <td class="text-center font-weight-bold row-index">1</td>
                                            <td>
                                                <input type="text" name="items[0][nama_item]" class="ba-input form-control-sm item-nama" placeholder="BATERAI LAPTOP..." required oninput="updatePreview()">
                                                <input type="hidden" name="items[0][kode]" class="item-kode" value="">
                                            </td>
                                            <td>
                                                <input type="text" name="items[0][keterangan]" class="ba-input form-control-sm item-keterangan" placeholder="UNTUK MENGGANTI..." oninput="updatePreview()">
                                            </td>
                                            <td>
                                                <input type="number" name="items[0][quantity]" class="ba-input form-control-sm item-qty text-center font-weight-bold" value="1" min="1" required oninput="updatePreview()">
                                            </td>
                                            <td>
                                                <input type="text" name="items[0][uom]" class="ba-input form-control-sm item-uom" value="PCS" oninput="updatePreview()">
                                            </td>
                                            <td>
                                                <input type="text" name="items[0][supplier]" class="ba-input form-control-sm item-supplier" placeholder="TOKOPEDIA" oninput="updatePreview()">
                                            </td>
                                            <td>
                                                <input type="text" name="items[0][no_suratjalan]" class="ba-input form-control-sm item-sj" placeholder="045/LGT/..." oninput="updatePreview()">
                                            </td>
                                            <td>
                                                <input type="text" name="items[0][no_po]" class="ba-input form-control-sm item-po" placeholder="2601..." oninput="updatePreview()">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm text-danger p-0" onclick="removeItemRow(this)" title="Hapus Baris">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Signatures / Penandatangan Card -->
                    <div class="ba-card mb-3">
                        <div class="ba-card-header">
                            <h2 class="ba-card-title">
                                <i class="fas fa-signature text-success"></i>
                                <span>Penandatangan Berita Acara OK</span>
                            </h2>
                        </div>
                        <div class="p-3">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="ba-label font-weight-bold">Dibuat Oleh</label>
                                    <input type="text" name="nama_penerima" id="nama_penerima" class="ba-input" value="{{ old('nama_penerima', 'Bagas Purnomo') }}" oninput="updatePreview()">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="ba-label font-weight-bold">Mengetahui (1)</label>
                                    <input type="text" name="mengetahui_1" id="mengetahui_1" class="ba-input" value="{{ old('mengetahui_1', 'Martin') }}" oninput="updatePreview()">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="ba-label font-weight-bold">Mengetahui (2)</label>
                                    <input type="text" name="mengetahui_2" id="mengetahui_2" class="ba-input" value="{{ old('mengetahui_2', 'Edi Awaludin') }}" oninput="updatePreview()">
                                </div>
                                <div class="col-md-4 mb-2"></div>
                                <div class="col-md-4 mb-2">
                                    <label class="ba-label font-weight-bold">Menyetujui (1)</label>
                                    <input type="text" name="menyetujui_1" id="menyetujui_1" class="ba-input" value="{{ old('menyetujui_1', 'Herinata') }}" oninput="updatePreview()">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="ba-label font-weight-bold">Menyetujui (2)</label>
                                    <input type="text" name="menyetujui_2" id="menyetujui_2" class="ba-input" value="{{ old('menyetujui_2', 'David') }}" oninput="updatePreview()">
                                </div>
                                <div class="col-12 mt-2">
                                    <label class="ba-label">File Lampiran Scan Asli (Opsional)</label>
                                    <input type="file" name="document_file" id="document_file" class="ba-input" accept="image/jpeg,image/png,application/pdf">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons (Mobile-Optimized Hierarchy) -->
                    <div class="ba-action-bar-bottom mb-5">
                        <div class="d-flex flex-column flex-sm-row-reverse align-items-stretch align-items-sm-center justify-content-between" style="gap: 12px;">
                            <div class="d-flex flex-column flex-sm-row align-items-center w-100 w-sm-auto" style="gap: 10px;">
                                <button type="submit" class="ba-btn-primary-action w-100 w-sm-auto">
                                    <span class="ba-btn-icon-badge"><i class="fas fa-save"></i></span>
                                    <span>Simpan</span>
                                </button>
                                <button type="button" class="ba-btn-simpan-cetak w-100 w-sm-auto" onclick="submitAndPrint()">
                                    <span class="ba-btn-icon-badge"><i class="fas fa-print"></i></span>
                                    <span>Simpan &amp; Cetak</span>
                                </button>
                            </div>
                            <div class="d-flex align-items-center w-100 w-sm-auto justify-content-between" style="gap: 10px;">
                                <a href="{{ route('berita-acara.index') }}" class="ba-btn-action ba-btn-secondary flex-fill text-center py-2">
                                    <i class="fas fa-arrow-left mr-1"></i> {{ __('messages.back') }}
                                </a>
                                <button type="reset" form="baForm" class="ba-btn-action ba-btn-secondary flex-fill text-center py-2" onclick="setTimeout(updatePreview, 50)">
                                    <i class="fas fa-undo mr-1"></i> {{ __('messages.reset') }}
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Live Real-time Document Preview -->
                <div class="col-lg-5 col-xl-5 d-none d-lg-block" id="studioColPreview">
                    <div class="ba-preview-wrapper">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="font-weight-bold" style="font-family: 'Outfit', sans-serif; font-size: 0.95rem; color: var(--color-ink-0);">
                                <i class="fas fa-eye text-primary mr-1"></i> Pratinjau Berita Acara OK (Format Cetak)
                            </span>
                            <span class="badge badge-success px-2 py-1" style="border-radius: 6px; font-size: 0.75rem;">
                                Live Real-Time
                            </span>
                        </div>

                        <!-- Paper Sheet Simulator (Enforced A4 Proportions) -->
                        <div class="ba-paper-preview" id="paperPreview">
                            <div class="ba-paper-content-main">
                                <!-- Location & Date Header (Top Right) -->
                                <div style="text-align: right; font-weight: 500; margin-bottom: 15px; font-size: 9.5px;">
                                    <span id="prevLokasi">Cikande</span>, <span id="prevTanggal">22 Juli 2026</span>
                                </div>

                                <!-- Letter Meta -->
                                <div style="font-size: 9px; line-height: 1.4; margin-bottom: 12px;">
                                    <table style="border: none; border-collapse: collapse; font-size: 9px;">
                                        <tr>
                                            <td style="width: 60px; padding: 1px 0;">No</td>
                                            <td style="width: 12px; text-align: center;">:</td>
                                            <td style="font-weight: 600;" id="prevNomorBA">16/BA/IT/06/2026</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 1px 0;">Hal</td>
                                            <td style="text-align: center;">:</td>
                                            <td id="prevHal">Pemberitahuan Barang/Jasa OK</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 1px 0;">Lampiran</td>
                                            <td style="text-align: center;">:</td>
                                            <td id="prevLampiran">-</td>
                                        </tr>
                                    </table>
                                    <div style="margin-top: 4px;">Kepada Yth, <span id="prevKepadaYth">Ka. Pembelian</span></div>
                                    <div style="margin-top: 2px; display: flex; gap: 4px;">
                                        <span>Cc :</span>
                                        <div id="prevCc">Ka. Pabrik<br>Ka. Keuangan</div>
                                    </div>
                                </div>

                                <!-- Title -->
                                <div class="prev-title">BERITA ACARA OK</div>

                                <!-- Preamble -->
                                <p style="font-size: 8.5px; margin-bottom: 6px; text-align: justify; line-height: 1.35;">
                                    Dengan ini di beritahukan bahwa <span id="prevPerusahaan">PT. CBA Chemical Industry Pabrik</span> telah menerima barang/jasa yang sudah dapat digunakan oleh <span id="prevUnit">IT Pabrik</span> berupa:
                                </p>

                                <!-- Table -->
                                <table class="prev-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 20px;">No</th>
                                            <th style="width: 90px;">ITEM DESCRIPTION</th>
                                            <th>KETERANGAN</th>
                                            <th style="width: 35px;">QTY<br>Request</th>
                                            <th style="width: 45px;">TGL</th>
                                            <th style="width: 50px;">SUPPLIER</th>
                                            <th style="width: 65px;">NO. SURATJALAN</th>
                                            <th style="width: 40px;">NO.PO</th>
                                        </tr>
                                    </thead>
                                    <tbody id="prevTableBody">
                                        <!-- Populated dynamically -->
                                    </tbody>
                                </table>

                                <!-- Closing -->
                                <p style="font-size: 8.5px; margin-top: 6px; margin-bottom: 20px; text-align: justify; line-height: 1.35;">
                                    Menyatakan bahwa barang tersebut sudah sesuai yang diinginkan dan layak untuk digunakan. Demikian berita acara ini di buat, untuk dapat di proses lebih lanjut, Atas perhatiannya kami ucapkan terima kasih.
                                </p>
                            </div>

                            <!-- Signatures (Pinned to Bottom of A4 Sheet) -->
                            <div class="ba-paper-signatures mt-auto pt-3" style="display: grid; grid-template-columns: 1.2fr 2fr 2fr; gap: 8px; font-size: 8.5px; text-align: left;">
                                <div>
                                    <div style="margin-bottom: 30px;">Dibuat Oleh,</div>
                                    <div style="font-weight: 700; text-decoration: underline;" id="prevSignDibuat">Bagas Purnomo</div>
                                </div>
                                <div style="text-align: center;">
                                    <div style="margin-bottom: 30px;">Mengetahui,</div>
                                    <div style="display: flex; justify-content: space-around; gap: 4px;">
                                        <span style="font-weight: 700; text-decoration: underline;" id="prevSignMeng1">Martin</span>
                                        <span style="font-weight: 700; text-decoration: underline;" id="prevSignMeng2">Edi Awaludin</span>
                                    </div>
                                </div>
                                <div style="text-align: center;">
                                    <div style="margin-bottom: 30px;">Menyetujui,</div>
                                    <div style="display: flex; justify-content: space-around; gap: 4px;">
                                        <span style="font-weight: 700; text-decoration: underline;" id="prevSignMeny1">Herinata</span>
                                        <span style="font-weight: 700; text-decoration: underline;" id="prevSignMeny2">David</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Drag and Drop
    const dropzone = document.getElementById('ocrDropzone');

    ['dragenter', 'dragover'].forEach(name => {
        dropzone.addEventListener(name, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(name => {
        dropzone.addEventListener(name, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.remove('dragover');
        }, false);
    });

    dropzone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        if (dt.files.length > 0) {
            processOcrFile(dt.files[0]);
        }
    });

    function handleFileSelect(event) {
        if (event.target.files.length > 0) {
            processOcrFile(event.target.files[0]);
        }
    }

    function processOcrFile(file) {
        const formData = new FormData();
        formData.append('document', file);
        formData.append('_token', '{{ csrf_token() }}');

        document.getElementById('ocrStatus').style.display = 'block';
        document.getElementById('ocrIcon').innerHTML = '<i class="fas fa-spinner fa-spin text-primary"></i>';
        document.getElementById('ocrSuccessAlert').style.display = 'none';

        // Also assign to document_file input
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('document_file').files = dt.files;

        fetch('{{ route("berita-acara.scan-ocr") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(res => {
            document.getElementById('ocrStatus').style.display = 'none';
            document.getElementById('ocrIcon').innerHTML = '<i class="fas fa-cloud-arrow-up"></i>';

            if (res.success && res.data) {
                const data = res.data;

                if (data.nomor_ba) document.getElementById('nomor_ba').value = data.nomor_ba;
                if (data.no_sj) document.getElementById('nomor_surat').value = data.no_sj;
                if (data.no_po) document.getElementById('nomor_po').value = data.no_po;
                if (data.tanggal_surat) document.getElementById('tanggal_surat').value = data.tanggal_surat;
                if (data.tanggal_verifikasi) document.getElementById('tanggal_verifikasi').value = data.tanggal_verifikasi;
                if (data.nama_penerima) document.getElementById('nama_penerima').value = data.nama_penerima;
                if (data.unit_penerima) document.getElementById('unit_penerima').value = data.unit_penerima;
                if (data.kepada && document.getElementById('kepada')) document.getElementById('kepada').value = data.kepada;
                if (data.status_verifikasi && document.getElementById('status_verifikasi')) document.getElementById('status_verifikasi').value = data.status_verifikasi;

                // Fill items repeater
                if (Array.isArray(data.items) && data.items.length > 0) {
                    const tbody = document.getElementById('itemsTableBody');
                    tbody.innerHTML = '';

                    data.items.forEach((item, index) => {
                        const tr = document.createElement('tr');
                        tr.className = 'item-row';
                        tr.innerHTML = `
                            <td class="text-center font-weight-bold row-index">${index + 1}</td>
                            <td>
                                <input type="text" name="items[${index}][nama_item]" class="ba-input form-control-sm item-nama" value="${escapeHtml(item.nama_item || '')}" required oninput="updatePreview()">
                                <input type="hidden" name="items[${index}][kode]" class="item-kode" value="${escapeHtml(item.kode || '')}">
                            </td>
                            <td>
                                <input type="text" name="items[${index}][keterangan]" class="ba-input form-control-sm item-keterangan" value="${escapeHtml(item.keterangan || '')}" oninput="updatePreview()">
                            </td>
                            <td>
                                <input type="number" name="items[${index}][quantity]" class="ba-input form-control-sm item-qty text-center font-weight-bold" value="${item.quantity || 1}" min="1" required oninput="updatePreview()">
                            </td>
                            <td>
                                <input type="text" name="items[${index}][uom]" class="ba-input form-control-sm item-uom" value="${escapeHtml(item.uom || 'PCS')}" oninput="updatePreview()">
                            </td>
                            <td>
                                <input type="text" name="items[${index}][supplier]" class="ba-input form-control-sm item-supplier" value="${escapeHtml(item.supplier || '')}" oninput="updatePreview()">
                            </td>
                            <td>
                                <input type="text" name="items[${index}][no_suratjalan]" class="ba-input form-control-sm item-sj" value="${escapeHtml(item.no_suratjalan || data.no_sj || '')}" oninput="updatePreview()">
                            </td>
                            <td>
                                <input type="text" name="items[${index}][no_po]" class="ba-input form-control-sm item-po" value="${escapeHtml(item.no_po || data.no_po || '')}" oninput="updatePreview()">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm text-danger p-0" onclick="removeItemRow(this)" title="Hapus Baris">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }

                const alertBox = document.getElementById('ocrSuccessAlert');
                const msgBox = document.getElementById('ocrSuccessMsg');
                alertBox.style.display = 'block';

                if (data._is_fallback) {
                    alertBox.className = 'alert alert-warning mt-2 mb-0';
                    msgBox.innerHTML = `<i class="fas fa-exclamation-triangle mr-1"></i> Data terbaca dengan mode standar (${data.items ? data.items.length : 0} item). Silakan periksa kembali rincian data Surat Jalan.`;
                } else {
                    alertBox.className = 'alert alert-success mt-2 mb-0';
                    msgBox.innerHTML = `<i class="fas fa-check-circle mr-1"></i> AI Vision berhasil mengekstrak <strong>${data.items ? data.items.length : 0} item barang</strong> dari Surat Jalan No. <code>${escapeHtml(data.no_sj || '-')}</code>!`;
                }

                updatePreview();
            } else {
                alert(res.message || 'Gagal mengekstrak data Surat Jalan.');
            }
        })
        .catch(err => {
            document.getElementById('ocrStatus').style.display = 'none';
            document.getElementById('ocrIcon').innerHTML = '<i class="fas fa-cloud-arrow-up"></i>';
            console.error(err);
            alert('Terjadi kesalahan saat memproses OCR: ' + err.message);
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function addItemRow() {
        const tbody = document.getElementById('itemsTableBody');
        const count = tbody.querySelectorAll('.item-row').length;
        const mainSj = document.getElementById('nomor_surat').value || '';
        const mainPo = document.getElementById('nomor_po').value || '';
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td class="text-center font-weight-bold row-index">${count + 1}</td>
            <td>
                <input type="text" name="items[${count}][nama_item]" class="ba-input form-control-sm item-nama" placeholder="Nama item..." required oninput="updatePreview()">
                <input type="hidden" name="items[${count}][kode]" class="item-kode" value="">
            </td>
            <td>
                <input type="text" name="items[${count}][keterangan]" class="ba-input form-control-sm item-keterangan" placeholder="Keperluan/keterangan..." oninput="updatePreview()">
            </td>
            <td>
                <input type="number" name="items[${count}][quantity]" class="ba-input form-control-sm item-qty text-center font-weight-bold" value="1" min="1" required oninput="updatePreview()">
            </td>
            <td>
                <input type="text" name="items[${count}][uom]" class="ba-input form-control-sm item-uom" value="PCS" oninput="updatePreview()">
            </td>
            <td>
                <input type="text" name="items[${count}][supplier]" class="ba-input form-control-sm item-supplier" placeholder="Supplier..." oninput="updatePreview()">
            </td>
            <td>
                <input type="text" name="items[${count}][no_suratjalan]" class="ba-input form-control-sm item-sj" value="${escapeHtml(mainSj)}" oninput="updatePreview()">
            </td>
            <td>
                <input type="text" name="items[${count}][no_po]" class="ba-input form-control-sm item-po" value="${escapeHtml(mainPo)}" oninput="updatePreview()">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm text-danger p-0" onclick="removeItemRow(this)" title="Hapus Baris">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        updateRowIndices();
        updatePreview();
    }

    function removeItemRow(btn) {
        const tbody = document.getElementById('itemsTableBody');
        const rows = tbody.querySelectorAll('.item-row');
        if (rows.length > 1) {
            btn.closest('tr').remove();
            updateRowIndices();
            updatePreview();
        } else {
            alert('Minimal harus ada 1 baris barang.');
        }
    }

    function updateRowIndices() {
        const rows = document.querySelectorAll('#itemsTableBody .item-row');
        rows.forEach((row, idx) => {
            row.querySelector('.row-index').textContent = idx + 1;
            row.querySelector('.item-nama').name = `items[${idx}][nama_item]`;
            row.querySelector('.item-kode').name = `items[${idx}][kode]`;
            row.querySelector('.item-keterangan').name = `items[${idx}][keterangan]`;
            row.querySelector('.item-qty').name = `items[${idx}][quantity]`;
            row.querySelector('.item-uom').name = `items[${idx}][uom]`;
            row.querySelector('.item-supplier').name = `items[${idx}][supplier]`;
            row.querySelector('.item-sj').name = `items[${idx}][no_suratjalan]`;
            row.querySelector('.item-po').name = `items[${idx}][no_po]`;
        });
    }

    function updatePreview() {
        const nomorBA = document.getElementById('nomor_ba').value || '16/BA/IT/06/2026';
        const lokasi = document.getElementById('lokasi').value || 'Cikande';
        const hal = document.getElementById('hal').value || 'Pemberitahuan Barang/Jasa OK';
        const lampiran = document.getElementById('lampiran').value || '-';
        const kepadaYth = document.getElementById('kepada_yth').value || 'Ka. Pembelian';
        const cc = document.getElementById('cc').value || 'Ka. Pabrik, Ka. Keuangan';
        const perusahaan = document.getElementById('nama_perusahaan').value || 'PT. CBA Chemical Industry Pabrik';
        const unit = document.getElementById('unit_penerima').value || 'IT Pabrik';
        const tglVerif = document.getElementById('tanggal_verifikasi').value || '';
        const dibuatOleh = document.getElementById('nama_penerima').value || 'Bagas Purnomo';
        const meng1 = document.getElementById('mengetahui_1').value || 'Martin';
        const meng2 = document.getElementById('mengetahui_2').value || 'Edi Awaludin';
        const meny1 = document.getElementById('menyetujui_1').value || 'Herinata';
        const meny2 = document.getElementById('menyetujui_2').value || 'David';

        document.getElementById('prevNomorBA').textContent = nomorBA;
        document.getElementById('prevLokasi').textContent = lokasi;
        document.getElementById('prevTanggal').textContent = formatDateIndo(tglVerif);
        document.getElementById('prevHal').textContent = hal;
        document.getElementById('prevLampiran').textContent = lampiran;
        document.getElementById('prevKepadaYth').textContent = kepadaYth;
        
        // Format Cc
        const ccItems = cc.split(/[,;\n]+/).map(s => s.trim()).filter(Boolean);
        document.getElementById('prevCc').innerHTML = ccItems.join('<br>') || 'Ka. Pabrik<br>Ka. Keuangan';

        document.getElementById('prevPerusahaan').textContent = perusahaan;
        document.getElementById('prevUnit').textContent = unit;

        // Signatures
        document.getElementById('prevSignDibuat').textContent = dibuatOleh;
        document.getElementById('prevSignMeng1').textContent = meng1;
        document.getElementById('prevSignMeng2').textContent = meng2;
        document.getElementById('prevSignMeny1').textContent = meny1;
        document.getElementById('prevSignMeny2').textContent = meny2;

        // Items Table
        const prevTbody = document.getElementById('prevTableBody');
        prevTbody.innerHTML = '';

        const rows = document.querySelectorAll('#itemsTableBody .item-row');
        if (rows.length === 0) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="text-align: center;">1</td>
                <td style="color: #94a3b8; font-style: italic;">-</td>
                <td style="color: #94a3b8; font-style: italic;">-</td>
                <td style="text-align: center; font-weight: 600;">1 PCS</td>
                <td style="text-align: center;">${formatDateIndo(tglVerif)}</td>
                <td style="text-align: center;">-</td>
                <td style="text-align: center; font-family: 'JetBrains Mono', monospace; font-size: 7px;">${escapeHtml(document.getElementById('nomor_surat').value || '-')}</td>
                <td style="text-align: center; font-family: 'JetBrains Mono', monospace; font-size: 7px;">${escapeHtml(document.getElementById('nomor_po').value || '-')}</td>
            `;
            prevTbody.appendChild(tr);
        } else {
            rows.forEach((row, idx) => {
                const nama = row.querySelector('.item-nama').value || '-';
                const ket = row.querySelector('.item-keterangan').value || '-';
                const qty = row.querySelector('.item-qty').value || '1';
                const uom = row.querySelector('.item-uom').value || 'PCS';
                const supp = row.querySelector('.item-supplier').value || '-';
                const sj = row.querySelector('.item-sj').value || '-';
                const po = row.querySelector('.item-po').value || '-';

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td style="text-align: center;">${idx + 1}</td>
                    <td style="font-weight: 600; text-transform: uppercase;">${escapeHtml(nama)}</td>
                    <td style="text-transform: uppercase; font-size: 7px;">${escapeHtml(ket)}</td>
                    <td style="text-align: center; font-weight: 600;">${qty} ${escapeHtml(uom)}</td>
                    <td style="text-align: center;">${formatDateIndo(tglVerif)}</td>
                    <td style="text-align: center; text-transform: uppercase; font-weight: 600;">${escapeHtml(supp)}</td>
                    <td style="text-align: center; font-family: 'JetBrains Mono', monospace; font-size: 7px;">${escapeHtml(sj)}</td>
                    <td style="text-align: center; font-family: 'JetBrains Mono', monospace; font-size: 7px;">${escapeHtml(po)}</td>
                `;
                prevTbody.appendChild(tr);
            });
        }
    }

    function formatDateIndo(dateStr) {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
    }

    function submitAndPrint() {
        document.getElementById('andPrintInput').value = '1';
        document.getElementById('baForm').submit();
    }

    function switchMobileStudioTab(tab) {
        const colForm = document.getElementById('studioColForm');
        const colPrev = document.getElementById('studioColPreview');
        const btnForm = document.getElementById('btnTabForm');
        const btnPrev = document.getElementById('btnTabPreview');

        if (tab === 'preview') {
            colForm.classList.add('d-none');
            colForm.classList.remove('d-block');
            colPrev.classList.remove('d-none');
            colPrev.classList.add('d-block');
            btnPrev.classList.add('btn-primary', 'active');
            btnPrev.classList.remove('text-muted');
            btnForm.classList.remove('btn-primary', 'active');
            btnForm.classList.add('text-muted');
            updatePreview();
        } else {
            colForm.classList.remove('d-none');
            colForm.classList.add('d-block');
            colPrev.classList.add('d-none');
            colPrev.classList.remove('d-block');
            btnForm.classList.add('btn-primary', 'active');
            btnForm.classList.remove('text-muted');
            btnPrev.classList.remove('btn-primary', 'active');
            btnPrev.classList.add('text-muted');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updatePreview();
    });
</script>
@endpush
@endsection
