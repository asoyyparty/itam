<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara OK - {{ $beritaAcara->nomor_ba ?: $beritaAcara->nomor_surat }}</title>

    <!-- App Logo / Favicon -->
    <link rel="icon" href="{{ vasset('logo-192.png') }}" type="image/png" sizes="192x192">
    <link rel="shortcut icon" href="{{ vasset('logo.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ vasset('logo-192.png') }}">
    <meta name="theme-color" content="#0f172a">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 */
        /* Hallmark · genre: modern-minimal · macrostructure: document · design-system: design.md · designed-as-app */
        :root {
            --font-display: 'Outfit', sans-serif;
            --font-body: 'Inter', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-body);
            background-color: #f1f5f9;
            color: #000000;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-size: 12px;
            line-height: 1.45;
        }

        /* Top Action Bar (hidden during print) */
        .no-print-bar {
            background: #0f172a;
            color: #ffffff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .no-print-bar .title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 600;
            font-family: var(--font-display);
        }

        .no-print-bar .actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: background-color 180ms ease, transform 180ms ease;
        }

        .btn-print {
            background: #4f46e5;
            color: #ffffff;
        }

        .btn-print:hover {
            background: #4338ca;
            transform: translateY(-1px);
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.1);
            color: #f8fafc;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* A4 Container */
        .page-wrapper {
            padding: 30px 0;
            display: flex;
            justify-content: center;
        }

        .a4-sheet {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            padding: 24mm 20mm 20mm 20mm;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Top Location & Date */
        .doc-date-header {
            text-align: right;
            font-size: 12.5px;
            font-weight: 500;
            margin-bottom: 25px;
            color: #000000;
        }

        /* Letter Info (No, Hal, Lampiran, Kepada) */
        .doc-letter-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            font-size: 12px;
        }

        .letter-meta-table {
            border-collapse: collapse;
            font-size: 12px;
        }

        .letter-meta-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .letter-meta-table td.field-name {
            width: 80px;
            font-weight: 500;
        }

        .letter-meta-table td.field-colon {
            width: 18px;
            text-align: center;
        }

        .letter-meta-table td.field-val {
            font-weight: 500;
        }

        .recipient-block {
            margin-top: 6px;
            line-height: 1.5;
        }

        .cc-block {
            margin-top: 4px;
            display: flex;
            gap: 4px;
        }

        .cc-block .cc-title {
            width: 32px;
        }

        .cc-block .cc-list {
            line-height: 1.5;
        }

        /* Document Title */
        .doc-title-container {
            text-align: center;
            margin: 15px 0 15px 0;
        }

        .doc-main-title {
            font-family: var(--font-body);
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.04em;
            color: #000000;
            display: inline-block;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        /* Preamble & Statements */
        .doc-preamble {
            font-size: 11.5px;
            margin-bottom: 12px;
            text-align: justify;
            line-height: 1.5;
        }

        .doc-closing {
            font-size: 11.5px;
            margin-top: 14px;
            margin-bottom: 35px;
            text-align: justify;
            line-height: 1.5;
        }

        /* Table of Items */
        .ba-items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            margin: 10px 0;
        }

        .ba-items-table th,
        .ba-items-table td {
            border: 1px solid #000000;
            padding: 6px 4px;
            vertical-align: middle;
        }

        .ba-items-table th {
            background-color: #ffffff;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            font-size: 8.5px;
            line-height: 1.2;
        }

        .ba-items-table td.col-no {
            text-align: center;
            width: 25px;
            font-weight: 500;
        }

        .ba-items-table td.col-desc {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 8.5px;
            line-height: 1.3;
            width: 140px;
        }

        .ba-items-table td.col-ket {
            font-size: 8px;
            text-transform: uppercase;
            line-height: 1.25;
            width: 170px;
        }

        .ba-items-table td.col-qty {
            text-align: center;
            font-weight: 600;
            white-space: nowrap;
            width: 45px;
        }

        .ba-items-table td.col-tgl {
            text-align: center;
            white-space: nowrap;
            width: 65px;
            font-size: 8px;
        }

        .ba-items-table td.col-supp {
            text-align: center;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 8px;
            width: 65px;
        }

        .ba-items-table td.col-sj {
            text-align: center;
            font-family: var(--font-mono);
            font-size: 8px;
            white-space: nowrap;
            width: 85px;
        }

        .ba-items-table td.col-po {
            text-align: center;
            font-family: var(--font-mono);
            font-size: 8px;
            white-space: nowrap;
            width: 55px;
        }

        /* Signatures Grid */
        .signatures-container {
            margin-top: 15px;
        }

        .signatures-grid {
            display: grid;
            grid-template-columns: 1.2fr 2fr 2fr;
            gap: 15px;
            font-size: 11.5px;
        }

        .sig-block {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sig-header {
            font-weight: 500;
            margin-bottom: 55px;
        }

        .sig-center-header {
            text-align: center;
            font-weight: 500;
            margin-bottom: 55px;
        }

        .sig-names-row {
            display: flex;
            justify-content: space-around;
            gap: 15px;
            text-align: center;
        }

        .sig-person {
            flex: 1;
            text-align: center;
        }

        .sig-name-underlined {
            font-weight: 700;
            text-decoration: underline;
            text-underline-offset: 3px;
            font-size: 12px;
        }

        .sig-digital-mark {
            height: 48px;
            display: flex;
            align-items: flex-end;
            margin-bottom: 6px;
        }

        /* Mobile Screen Styles */
        @media screen and (max-width: 767.98px) {
            .no-print-bar {
                padding: 10px 14px;
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }
            .no-print-bar .title {
                font-size: 13px;
                justify-content: center;
            }
            .no-print-bar .actions {
                display: flex;
                width: 100%;
                gap: 8px;
            }
            .no-print-bar .btn-action {
                flex: 1;
                justify-content: center;
                font-size: 12px;
                padding: 8px 10px;
            }
            .page-wrapper {
                padding: 12px 6px;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }

        /* Print Settings */
        @media print {
            .no-print-bar {
                display: none !important;
            }

            body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
                font-size: 11pt !important;
            }

            .page-wrapper {
                padding: 0 !important;
            }

            .a4-sheet {
                width: 100% !important;
                min-height: auto !important;
                padding: 0 !important;
                box-shadow: none !important;
            }

            @page {
                size: A4 portrait;
                margin: 20mm 20mm 15mm 20mm;
            }
        }
    </style>
</head>
<body>

    <!-- Action Bar for Screen view -->
    <div class="no-print-bar">
        <div class="title">
            <i class="fas fa-file-signature" style="color: #818cf8;"></i>
            <span>Berita Acara OK &middot; {{ $beritaAcara->nomor_ba ?: ($beritaAcara->nomor_surat ?: '16/BA/IT/06/2026') }}</span>
        </div>
        <div class="actions">
            <a href="{{ route('berita-acara.show', $beritaAcara) }}" class="btn-action btn-back">
                <i class="fas fa-arrow-left"></i> <span>Kembali</span>
            </a>
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="fas fa-print"></i> <span>Cetak A4 / PDF</span>
            </button>
        </div>
    </div>

    <!-- Printable A4 Sheet -->
    <div class="page-wrapper">
        <div class="a4-sheet">
            <div>
                <!-- Location & Date Header (Top Right) -->
                <div class="doc-date-header">
                    {{ $beritaAcara->lokasi ?: 'Cikande' }}, {{ $beritaAcara->tanggal_verifikasi ? \Carbon\Carbon::parse($beritaAcara->tanggal_verifikasi)->translatedFormat('d F Y') : ($beritaAcara->tanggal_surat ? \Carbon\Carbon::parse($beritaAcara->tanggal_surat)->translatedFormat('d F Y') : '22 Juli 2026') }}
                </div>

                <!-- Letter Info (No, Hal, Lampiran, Kepada Yth, Cc) -->
                <div class="doc-letter-info">
                    <div>
                        <table class="letter-meta-table">
                            <tr>
                                <td class="field-name">No</td>
                                <td class="field-colon">:</td>
                                <td class="field-val">{{ $beritaAcara->nomor_ba ?: '16/BA/IT/06/2026' }}</td>
                            </tr>
                            <tr>
                                <td class="field-name">Hal</td>
                                <td class="field-colon">:</td>
                                <td class="field-val">{{ $beritaAcara->hal ?: 'Pemberitahuan Barang/Jasa OK' }}</td>
                            </tr>
                            <tr>
                                <td class="field-name">Lampiran</td>
                                <td class="field-colon">:</td>
                                <td class="field-val">{{ $beritaAcara->lampiran ?: '-' }}</td>
                            </tr>
                        </table>

                        <div class="recipient-block">
                            Kepada Yth, {{ $beritaAcara->kepada_yth ?: 'Ka. Pembelian' }}
                        </div>

                        <div class="cc-block">
                            <div class="cc-title">Cc :</div>
                            <div class="cc-list">
                                <div>Ka. Pabrik</div>
                                <div>Ka. Keuangan</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Title Centered -->
                <div class="doc-title-container">
                    <h1 class="doc-main-title">BERITA ACARA OK</h1>
                </div>

                <!-- Preamble -->
                <p class="doc-preamble">
                    Dengan ini di beritahukan bahwa {{ $beritaAcara->nama_perusahaan ?: 'PT. CBA Chemical Industry Pabrik' }} telah menerima barang/jasa yang sudah dapat digunakan oleh {{ $beritaAcara->unit_penerima ?: 'IT Pabrik' }} berupa:
                </p>

                <!-- Items Table -->
                <table class="ba-items-table">
                    <thead>
                        <tr>
                            <th style="width: 25px;">No</th>
                            <th style="width: 140px;">ITEM DESCRIPTION</th>
                            <th style="width: 170px;">KETERANGAN</th>
                            <th style="width: 45px;">QTY<br>Request</th>
                            <th style="width: 65px;">TGL</th>
                            <th style="width: 65px;">SUPPLIER</th>
                            <th style="width: 85px;">NO. SURATJALAN</th>
                            <th style="width: 55px;">NO.PO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($beritaAcara->items as $index => $item)
                            <tr>
                                <td class="col-no">{{ $item['no'] ?? ($index + 1) }}</td>
                                <td class="col-desc">{{ $item['nama_item'] ?? ($item['item_description'] ?? '-') }}</td>
                                <td class="col-ket">{{ $item['keterangan'] ?? '-' }}</td>
                                <td class="col-qty">{{ $item['quantity'] ?? 1 }} {{ $item['uom'] ?? 'PCS' }}</td>
                                <td class="col-tgl">{{ $item['tgl'] ?? ($beritaAcara->tanggal_verifikasi ? \Carbon\Carbon::parse($beritaAcara->tanggal_verifikasi)->translatedFormat('d F Y') : '-') }}</td>
                                <td class="col-supp">{{ $item['supplier'] ?? '-' }}</td>
                                <td class="col-sj">{{ $item['no_suratjalan'] ?? $beritaAcara->nomor_surat }}</td>
                                <td class="col-po">{{ $item['no_po'] ?? ($beritaAcara->nomor_po ?: '-') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 15px; color: #64748b;">Tidak ada rincian barang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Closing Statement -->
                <p class="doc-closing">
                    Menyatakan bahwa barang tersebut sudah sesuai yang diinginkan dan layak untuk digunakan. Demikian berita acara ini di buat, untuk dapat di proses lebih lanjut, Atas perhatiannya kami ucapkan terima kasih.
                </p>

                <!-- Signatures -->
                <div class="signatures-container">
                    <div class="signatures-grid">
                        <!-- Column 1: Dibuat Oleh -->
                        <div class="sig-block">
                            <div class="sig-header">Dibuat Oleh,</div>
                            <div class="sig-digital-mark"></div>
                            <div>
                                <span class="sig-name-underlined">{{ $beritaAcara->nama_penerima ?: 'Bagas Purnomo' }}</span>
                            </div>
                        </div>

                        <!-- Column 2: Mengetahui -->
                        <div class="sig-block">
                            <div class="sig-center-header">Mengetahui,</div>
                            <div class="sig-digital-mark"></div>
                            <div class="sig-names-row">
                                <div class="sig-person">
                                    <span class="sig-name-underlined">{{ $beritaAcara->mengetahui_1 ?: 'Martin' }}</span>
                                </div>
                                <div class="sig-person">
                                    <span class="sig-name-underlined">{{ $beritaAcara->mengetahui_2 ?: 'Edi Awaludin' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Column 3: Menyetujui -->
                        <div class="sig-block">
                            <div class="sig-center-header">Menyetujui,</div>
                            <div class="sig-digital-mark"></div>
                            <div class="sig-names-row">
                                <div class="sig-person">
                                    <span class="sig-name-underlined">{{ $beritaAcara->menyetujui_1 ?: 'Herinata' }}</span>
                                </div>
                                <div class="sig-person">
                                    <span class="sig-name-underlined">{{ $beritaAcara->menyetujui_2 ?: 'David' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('autoprint') === '1') {
                setTimeout(() => {
                    window.print();
                }, 500);
            }
        });
    </script>
</body>
</html>
