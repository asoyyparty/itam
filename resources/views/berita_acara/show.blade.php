@extends('layouts.admin')

@section('title', 'Detail Berita Acara OK - ' . ($beritaAcara->nomor_ba ?: $beritaAcara->nomor_surat))

@section('content')
<!-- Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 -->
<!-- Hallmark · genre: modern-minimal · macrostructure: workbench · design-system: design.md · designed-as-app -->
<style>
    .ba-show-container {
        padding-bottom: 90px;
        overflow-x: clip;
    }
    .ba-card {
        background: var(--color-paper-1, #1e293b);
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }
    .ba-card-header {
        padding: 12px 16px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.12);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .ba-card-title, h2.ba-card-title {
        font-family: 'Outfit', sans-serif !important;
        font-size: 0.88rem !important;
        font-weight: 700 !important;
        color: var(--color-ink-0, #f8fafc) !important;
        margin: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
    }
    .ba-meta-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .ba-meta-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 9px 0;
        border-bottom: 1px solid rgba(148, 163, 184, 0.08);
        font-size: 0.83rem;
    }
    .ba-meta-item:last-child {
        border-bottom: none;
    }
    .ba-meta-label {
        color: var(--color-ink-2, #94a3b8);
        font-weight: 500;
    }
    .ba-meta-value {
        color: var(--color-ink-0, #f8fafc);
        font-weight: 600;
        text-align: right;
    }
    /* Outer scroll wrapper for A4 Paper simulator on mobile */
    .ba-paper-viewer-outer {
        background: rgba(15, 23, 42, 0.4);
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 14px;
        padding: 10px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    .ba-paper-viewer {
        background: #ffffff;
        color: #000000;
        border-radius: 10px;
        padding: 28px 22px;
        min-width: 560px;
        min-height: 600px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        font-family: 'Inter', sans-serif;
        font-size: 11px;
        line-height: 1.45;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        box-sizing: border-box;
    }
    .ba-paper-viewer table {
        width: 100%;
        border-collapse: collapse;
        margin: 14px 0;
        font-size: 9.5px;
    }
    .ba-paper-viewer th, .ba-paper-viewer td {
        border: 1px solid #000000;
        padding: 6px 4px;
    }
    .ba-paper-viewer th {
        background: #ffffff;
        font-weight: 700;
        text-align: center;
        color: #000;
        text-transform: uppercase;
    }
    /* Action Button Styles - High Contrast */
    .ba-btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 7px 15px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 600;
        transition: all 180ms ease;
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
    }
    body.light-mode .ba-btn-secondary {
        background: #e2e8f0 !important;
        color: #0f172a !important;
        border: 1px solid #cbd5e1 !important;
    }
    @media (max-width: 575.98px) {
        .ba-page-title {
            font-size: 0.92rem !important;
        }
        .ba-card-title, h2.ba-card-title {
            font-size: 0.76rem !important;
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
        .ba-paper-viewer {
            padding: 18px 12px;
            font-size: 10px;
        }
    }
</style>

<div class="content-header py-2">
    <div class="container-fluid">
        <div class="row align-items-center mb-1">
            <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                <h1 class="m-0 font-weight-bold ba-page-title d-flex align-items-center flex-wrap">
                    <i class="fas fa-file-signature text-primary mr-2" style="font-size: 1.15em;"></i>
                    <span>Berita Acara OK</span>
                    <span class="mx-1">&middot;</span>
                    <span style="font-family: 'JetBrains Mono', monospace; color: #818cf8; font-size: 0.9em;">{{ $beritaAcara->nomor_ba ?: $beritaAcara->nomor_surat }}</span>
                </h1>
            </div>
            <div class="col-12 col-sm-6 text-sm-right d-flex align-items-center justify-content-start justify-content-sm-end ba-header-actions">
                <a href="{{ route('berita-acara.index') }}" class="ba-btn-action ba-btn-secondary mr-2">
                    <i class="fas fa-arrow-left mr-1"></i>
                    <span>{{ __('messages.back') }}</span>
                </a>
                <a href="{{ route('berita-acara.edit', $beritaAcara) }}" class="ba-btn-action ba-btn-secondary mr-2">
                    <i class="fas fa-edit mr-1 text-warning"></i>
                    <span>Ubah Data</span>
                </a>
                <a href="{{ route('berita-acara.print', $beritaAcara) }}" target="_blank" class="btn btn-primary px-3 py-1.5" style="border-radius: 10px; font-size: 0.82rem; font-weight: 600;">
                    <i class="fas fa-print mr-1"></i> Cetak A4 / PDF
                </a>
            </div>
        </div>
    </div>
</div>

<div class="content ba-show-container">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px; font-size: 0.9rem;">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            <!-- Left: Metadata -->
            <div class="col-lg-4 col-xl-4 mb-4">
                <div class="ba-card mb-4">
                    <div class="ba-card-header">
                        <h2 class="ba-card-title">
                            <i class="fas fa-info-circle text-primary"></i>
                            <span>Data Berita Acara</span>
                        </h2>
                        <span class="badge badge-success px-2 py-1" style="border-radius: 6px; font-size: 0.75rem;">
                            Resmi OK
                        </span>
                    </div>
                    <div class="p-3">
                        <ul class="ba-meta-list">
                            <li class="ba-meta-item">
                                <span class="ba-meta-label">Nomor Berita Acara</span>
                                <span class="ba-meta-value" style="font-family: 'JetBrains Mono', monospace;">{{ $beritaAcara->nomor_ba ?: ($beritaAcara->nomor_surat ?: '-') }}</span>
                            </li>
                            <li class="ba-meta-item">
                                <span class="ba-meta-label">Lokasi</span>
                                <span class="ba-meta-value">{{ $beritaAcara->lokasi ?: 'Cikande' }}</span>
                            </li>
                            <li class="ba-meta-item">
                                <span class="ba-meta-label">Hal</span>
                                <span class="ba-meta-value">{{ $beritaAcara->hal ?: 'Pemberitahuan Barang/Jasa OK' }}</span>
                            </li>
                            <li class="ba-meta-item">
                                <span class="ba-meta-label">Kepada Yth</span>
                                <span class="ba-meta-value">{{ $beritaAcara->kepada_yth ?: 'Ka. Pembelian' }}</span>
                            </li>
                            <li class="ba-meta-item">
                                <span class="ba-meta-label">Cc</span>
                                <span class="ba-meta-value">{{ $beritaAcara->cc ?: 'Ka. Pabrik, Ka. Keuangan' }}</span>
                            </li>
                            <li class="ba-meta-item">
                                <span class="ba-meta-label">No. Surat Jalan Vendor</span>
                                <span class="ba-meta-value" style="font-family: 'JetBrains Mono', monospace;">{{ $beritaAcara->nomor_surat }}</span>
                            </li>
                            <li class="ba-meta-item">
                                <span class="ba-meta-label">No. PO</span>
                                <span class="ba-meta-value" style="font-family: 'JetBrains Mono', monospace;">{{ $beritaAcara->nomor_po ?: '-' }}</span>
                            </li>
                            <li class="ba-meta-item">
                                <span class="ba-meta-label">Tanggal Terima</span>
                                <span class="ba-meta-value">{{ $beritaAcara->tanggal_verifikasi ? \Carbon\Carbon::parse($beritaAcara->tanggal_verifikasi)->translatedFormat('d F Y') : '-' }}</span>
                            </li>
                            <li class="ba-meta-item">
                                <span class="ba-meta-label">Total Jenis Barang</span>
                                <span class="ba-meta-value">{{ is_array($beritaAcara->items) ? count($beritaAcara->items) : 0 }} Item</span>
                            </li>
                            <li class="ba-meta-item">
                                <span class="ba-meta-label">Total Kuantitas</span>
                                <span class="ba-meta-value">{{ $beritaAcara->total_quantity }} Unit</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Signatures Info -->
                <div class="ba-card mb-4">
                    <div class="ba-card-header">
                        <h2 class="ba-card-title">
                            <i class="fas fa-signature text-success"></i>
                            <span>Penandatangan</span>
                        </h2>
                    </div>
                    <div class="p-3">
                        <ul class="ba-meta-list">
                            <li class="ba-meta-item">
                                <span class="ba-meta-label">Dibuat Oleh</span>
                                <span class="ba-meta-value">{{ $beritaAcara->nama_penerima ?: 'Bagas Purnomo' }}</span>
                            </li>
                            <li class="ba-meta-item">
                                <span class="ba-meta-label">Mengetahui</span>
                                <span class="ba-meta-value">{{ $beritaAcara->mengetahui_1 ?: 'Martin' }} &amp; {{ $beritaAcara->mengetahui_2 ?: 'Edi Awaludin' }}</span>
                            </li>
                            <li class="ba-meta-item">
                                <span class="ba-meta-label">Menyetujui</span>
                                <span class="ba-meta-value">{{ $beritaAcara->menyetujui_1 ?: 'Herinata' }} &amp; {{ $beritaAcara->menyetujui_2 ?: 'David' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                @if($beritaAcara->file_lampiran)
                    <div class="ba-card mb-4">
                        <div class="ba-card-header">
                            <h2 class="ba-card-title">
                                <i class="fas fa-paperclip text-warning"></i>
                                <span>Lampiran Scan Surat Jalan</span>
                            </h2>
                        </div>
                        <div class="p-3">
                            <a href="{{ asset('storage/' . $beritaAcara->file_lampiran) }}" target="_blank" class="btn btn-sm btn-outline-info w-100" style="border-radius: 8px;">
                                <i class="fas fa-external-link-alt mr-1"></i> Buka Berkas Scan Asli
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right: Document Layout (Format A4 Resmi Berita Acara OK) -->
            <div class="col-lg-8 col-xl-8 mb-4">
                <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-2 mb-3">
                    <span class="font-weight-bold" style="font-family: 'Outfit', sans-serif; font-size: 1.05rem; color: var(--color-ink-0);">
                        <i class="fas fa-file-contract text-primary mr-1"></i> Tampilan Dokumen Fisik A4
                    </span>
                    <a href="{{ route('berita-acara.print', $beritaAcara) }}" target="_blank" class="btn btn-sm btn-primary px-3" style="border-radius: 8px; font-weight: 600;">
                        <i class="fas fa-print mr-1"></i> Cetak / Unduh PDF
                    </a>
                </div>

                <div class="d-block d-md-none mb-2">
                    <small class="text-muted d-flex align-items-center gap-1" style="font-size: 0.74rem; background: rgba(99, 102, 241, 0.1); padding: 6px 10px; border-radius: 8px; color: #a5b4fc !important;">
                        <i class="fas fa-arrows-alt-h text-primary"></i> <span>Geser dokumen ke samping untuk melihat tabel lengkap</span>
                    </small>
                </div>

                <div class="ba-paper-viewer-outer">
                    <div class="ba-paper-viewer">
                        <div style="text-align: right; margin-bottom: 20px; font-weight: 500; font-size: 11px;">
                            {{ $beritaAcara->lokasi ?: 'Cikande' }}, {{ $beritaAcara->tanggal_verifikasi ? \Carbon\Carbon::parse($beritaAcara->tanggal_verifikasi)->translatedFormat('d F Y') : ($beritaAcara->tanggal_surat ? \Carbon\Carbon::parse($beritaAcara->tanggal_surat)->translatedFormat('d F Y') : '22 Juli 2026') }}
                        </div>

                        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                            <table style="width: auto; border: none; margin: 0;">
                                <tr>
                                    <td style="width: 70px; font-weight: 500; border: none; padding: 2px 0;">No</td>
                                    <td style="width: 15px; border: none; padding: 2px 0; text-align: center;">:</td>
                                    <td style="font-weight: 600; border: none; padding: 2px 0; font-family: 'JetBrains Mono', monospace;">{{ $beritaAcara->nomor_ba ?: ($beritaAcara->nomor_surat ?: '16/BA/IT/06/2026') }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 500; border: none; padding: 2px 0;">Hal</td>
                                    <td style="border: none; padding: 2px 0; text-align: center;">:</td>
                                    <td style="font-weight: 600; border: none; padding: 2px 0;">{{ $beritaAcara->hal ?: 'Pemberitahuan Barang/Jasa OK' }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 500; border: none; padding: 2px 0;">Lampiran</td>
                                    <td style="border: none; padding: 2px 0; text-align: center;">:</td>
                                    <td style="border: none; padding: 2px 0;">-</td>
                                </tr>
                            </table>
                        </div>

                        <div style="margin-bottom: 18px; font-weight: 500; line-height: 1.5;">
                            Kepada Yth, {{ $beritaAcara->kepada_yth ?: 'Ka. Pembelian' }}<br>
                            <span style="display: inline-block; margin-top: 2px;">{{ $beritaAcara->cc ?: 'Ka. Pabrik, Ka. Keuangan' }}</span>
                        </div>

                        <div style="text-align: center; margin: 22px 0 16px 0;">
                            <h3 style="font-weight: 700; text-decoration: underline; text-underline-offset: 3px; font-size: 14px; margin: 0; text-transform: uppercase; letter-spacing: 0.03em;">BERITA ACARA OK</h3>
                        </div>

                        <p style="text-align: justify; margin-bottom: 14px; line-height: 1.5;">
                            Dengan ini di beritahukan bahwa PT. CBA Chemical Industry Pabrik telah menerima barang/jasa yang sudah dapat digunakan oleh IT Pabrik berupa:
                        </p>

                        <table style="width: 100%; border-collapse: collapse; margin: 12px 0; font-size: 9px;">
                            <thead>
                                <tr style="background: #ffffff;">
                                    <th style="border: 1px solid #000; padding: 6px 4px; text-align: center; width: 25px;">NO</th>
                                    <th style="border: 1px solid #000; padding: 6px 4px; text-align: center; width: 140px;">ITEM DESCRIPTION</th>
                                    <th style="border: 1px solid #000; padding: 6px 4px; text-align: center; width: 150px;">KETERANGAN</th>
                                    <th style="border: 1px solid #000; padding: 6px 4px; text-align: center; width: 50px;">QTY REQUEST</th>
                                    <th style="border: 1px solid #000; padding: 6px 4px; text-align: center; width: 65px;">TGL</th>
                                    <th style="border: 1px solid #000; padding: 6px 4px; text-align: center; width: 65px;">SUPPLIER</th>
                                    <th style="border: 1px solid #000; padding: 6px 4px; text-align: center; width: 85px;">NO. SURATJALAN</th>
                                    <th style="border: 1px solid #000; padding: 6px 4px; text-align: center; width: 55px;">NO. PO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(is_array($beritaAcara->items) && count($beritaAcara->items) > 0)
                                    @foreach($beritaAcara->items as $index => $item)
                                        <tr>
                                            <td style="border: 1px solid #000; padding: 6px 4px; text-align: center;">{{ $index + 1 }}</td>
                                            <td style="border: 1px solid #000; padding: 6px 4px; font-weight: 600; text-transform: uppercase;">{{ $item['nama_barang'] ?? ($item['item_description'] ?? '-') }}</td>
                                            <td style="border: 1px solid #000; padding: 6px 4px; text-transform: uppercase;">{{ $item['keterangan'] ?? '-' }}</td>
                                            <td style="border: 1px solid #000; padding: 6px 4px; text-align: center; font-weight: 600; white-space: nowrap;">{{ $item['jumlah'] ?? ($item['qty'] ?? 1) }} {{ $item['satuan'] ?? 'Pieces' }}</td>
                                            <td style="border: 1px solid #000; padding: 6px 4px; text-align: center; white-space: nowrap;">{{ $beritaAcara->tanggal_verifikasi ? \Carbon\Carbon::parse($beritaAcara->tanggal_verifikasi)->translatedFormat('d F Y') : '22 July 2026' }}</td>
                                            <td style="border: 1px solid #000; padding: 6px 4px; text-align: center; font-weight: 600;">{{ $item['supplier'] ?? '-' }}</td>
                                            <td style="border: 1px solid #000; padding: 6px 4px; text-align: center; font-family: 'JetBrains Mono', monospace; white-space: nowrap;">{{ $beritaAcara->nomor_surat }}</td>
                                            <td style="border: 1px solid #000; padding: 6px 4px; text-align: center; font-family: 'JetBrains Mono', monospace; white-space: nowrap;">{{ $beritaAcara->nomor_po ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" style="border: 1px solid #000; padding: 12px; text-align: center; color: #64748b;">Belum ada item barang</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>

                        <p style="text-align: justify; margin-top: 14px; margin-bottom: 24px; line-height: 1.5;">
                            Demikian Berita Acara ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.
                        </p>

                        <div style="display: grid; grid-template-columns: 1.2fr 2fr 2fr; gap: 10px; text-align: center; font-size: 9.5px; margin-top: 24px;">
                            <div style="display: flex; flex-direction: column; justify-content: space-between;">
                                <div style="margin-bottom: 45px; font-weight: 500; text-align: left;">Dibuat Oleh:</div>
                                <div style="text-align: left;">
                                    <div style="font-weight: 700; text-decoration: underline; text-underline-offset: 3px;">{{ $beritaAcara->nama_penerima ?: 'Bagas Purnomo' }}</div>
                                    <div style="color: #475569; font-size: 8.5px; margin-top: 2px;">IT Staff</div>
                                </div>
                            </div>
                            <div style="display: flex; flex-direction: column; justify-content: space-between;">
                                <div style="margin-bottom: 45px; font-weight: 500;">Mengetahui:</div>
                                <div style="display: flex; justify-content: space-around;">
                                    <div>
                                        <div style="font-weight: 700; text-decoration: underline; text-underline-offset: 3px;">{{ $beritaAcara->mengetahui_1 ?: 'Martin' }}</div>
                                        <div style="color: #475569; font-size: 8.5px; margin-top: 2px;">Ka. IT</div>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; text-decoration: underline; text-underline-offset: 3px;">{{ $beritaAcara->mengetahui_2 ?: 'Edi Awaludin' }}</div>
                                        <div style="color: #475569; font-size: 8.5px; margin-top: 2px;">Ka. HRD</div>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; flex-direction: column; justify-content: space-between;">
                                <div style="margin-bottom: 45px; font-weight: 500;">Menyetujui:</div>
                                <div style="display: flex; justify-content: space-around;">
                                    <div>
                                        <div style="font-weight: 700; text-decoration: underline; text-underline-offset: 3px;">{{ $beritaAcara->menyetujui_1 ?: 'Herinata' }}</div>
                                        <div style="color: #475569; font-size: 8.5px; margin-top: 2px;">General Manager</div>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; text-decoration: underline; text-underline-offset: 3px;">{{ $beritaAcara->menyetujui_2 ?: 'David' }}</div>
                                        <div style="color: #475569; font-size: 8.5px; margin-top: 2px;">Director</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
