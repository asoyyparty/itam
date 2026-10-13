<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Disposal Aset - {{ $beritaAcaraDisposal->nomor_ba }}</title>

    <!-- App Favicon -->
    <link rel="icon" href="{{ vasset('logo-192.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Times+New+Roman&family=Arial&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #e2e8f0;
            color: #000000;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-size: 11pt;
            line-height: 1.35;
        }

        /* Top Control Bar */
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
            font-family: 'Inter', sans-serif;
        }

        .no-print-bar .title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 600;
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
        }

        .btn-print {
            background: #4f46e5;
            color: #ffffff;
        }

        .btn-print:hover {
            background: #4338ca;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }

        /* A4 Page Layout */
        .page-wrapper {
            padding: 30px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
        }

        .a4-sheet {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            padding: 20mm 20mm 20mm 20mm;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Top Header Area */
        .doc-top-right {
            text-align: right;
            margin-bottom: 25px;
            font-size: 11pt;
        }

        .doc-header-info {
            margin-bottom: 30px;
            font-size: 11pt;
            line-height: 1.4;
        }

        .doc-header-info table {
            border-collapse: collapse;
        }

        .doc-header-info td {
            padding: 1px 0;
            vertical-align: top;
        }

        /* Center Form Title */
        .doc-title-block {
            text-align: center;
            margin-bottom: 30px;
        }

        .doc-title-block h1 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .doc-title-block h2 {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .doc-title-block h3 {
            font-size: 11pt;
            font-weight: bold;
        }

        /* Preamble Narration */
        .doc-preamble {
            font-size: 11pt;
            line-height: 1.4;
            margin-bottom: 20px;
            text-align: justify;
        }

        /* Main Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000000;
            margin-bottom: 20px;
            font-size: 10.5pt;
        }

        .items-table th {
            border: 1px solid #000000;
            padding: 6px 4px;
            text-align: center;
            font-weight: bold;
            background-color: #ffffff;
        }

        .items-table td {
            border: 1px solid #000000;
            padding: 5px 6px;
            vertical-align: middle;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .cell-divisi {
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
            line-height: 1.3;
        }

        /* Closing Line */
        .doc-closing {
            font-size: 11pt;
            margin-bottom: 35px;
        }

        /* Signatories Layout (Rapi & Balanced Widths) */
        .signatures-container {
            width: 100%;
            margin-top: auto;
        }

        .sig-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .sig-table th {
            font-weight: normal;
            text-align: center;
            padding-bottom: 60px;
            font-size: 11pt;
        }

        .sig-table td {
            text-align: center;
            vertical-align: top;
            font-size: 10.5pt;
            line-height: 1.35;
            padding: 0 4px;
            width: 20%;
        }

        .sig-name {
            font-weight: normal;
            white-space: nowrap;
        }

        .sig-title {
            font-weight: normal;
            font-size: 9.5pt;
            white-space: nowrap;
        }

        /* Halaman Lampiran Foto */
        .lampiran-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 35px;
        }

        .photo-grid-2x2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
            width: 100%;
        }

        .photo-box {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .photo-box img {
            width: 100%;
            max-height: 310px;
            object-fit: contain;
            border: 1px solid #000000;
        }

        /* Print Media Styles */
        @media print {
            .no-print-bar {
                display: none !important;
            }
            body {
                background-color: #ffffff !important;
            }
            .page-wrapper {
                padding: 0 !important;
                gap: 0 !important;
            }
            .a4-sheet {
                box-shadow: none !important;
                width: 100% !important;
                min-height: 100vh !important;
                padding: 15mm 15mm 15mm 15mm !important;
                page-break-after: always;
            }
            .a4-sheet:last-child {
                page-break-after: avoid;
            }
        }
    </style>
</head>
<body>

    <!-- Top Control Bar (Hidden on print) -->
    <div class="no-print-bar">
        <div class="title">
            <i class="fas fa-file-pdf text-indigo-400"></i>
            <span>Preview Cetak BA Disposal Asset</span>
            <span style="font-family: monospace; background: rgba(255,255,255,0.15); padding: 2px 8px; border-radius: 4px;">{{ $beritaAcaraDisposal->nomor_ba }}</span>
        </div>
        <div class="actions">
            <a href="{{ route('berita-acara-disposal.index') }}" class="btn-action btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="fas fa-print"></i> Cetak Dokumen
            </button>
        </div>
    </div>

    <div class="page-wrapper">
        <!-- PAGE 1: BERITA ACARA DISPOSAL ASET -->
        <div class="a4-sheet">
            <div>
                <!-- Top Right Date -->
                <div class="doc-top-right">
                    {{ $beritaAcaraDisposal->lokasi ?: 'Cikande' }}, {{ \Carbon\Carbon::parse($beritaAcaraDisposal->tanggal_ba)->translatedFormat('d F Y') }}
                </div>

                <!-- Top Left Header Info -->
                <div class="doc-header-info">
                    <table>
                        <tr>
                            <td style="width: 80px;">No</td>
                            <td style="width: 15px;">:</td>
                            <td>{{ $beritaAcaraDisposal->nomor_ba }}</td>
                        </tr>
                        <tr>
                            <td>Hal</td>
                            <td>:</td>
                            <td>{{ $beritaAcaraDisposal->hal ?: 'Disposal Asset' }}</td>
                        </tr>
                        <tr>
                            <td>Lampiran</td>
                            <td>:</td>
                            <td>{{ $beritaAcaraDisposal->lampiran_label ?: 'Foto Assets' }}</td>
                        </tr>
                    </table>

                    <div style="margin-top: 15px;">
                        <div>Kepada Yth,</div>
                        @php
                            $kepadaList = explode(',', $beritaAcaraDisposal->kepada_yth ?: 'Ka. Bagian, Ka. Accounting, Ka. Asset');
                        @endphp
                        @foreach($kepadaList as $kepada)
                            <div>{{ trim($kepada) }}</div>
                        @endforeach
                        @if(!empty($beritaAcaraDisposal->cc))
                            <div>Cc : {{ $beritaAcaraDisposal->cc }}</div>
                        @endif
                    </div>
                </div>

                <!-- Center Title Block -->
                <div class="doc-title-block">
                    <h1>BERITA ACARA DISPOSAL ASET</h1>
                    <h2>{{ $beritaAcaraDisposal->form_code ?: 'CBA/MTN/F-023' }}</h2>
                    <h3>{{ $beritaAcaraDisposal->form_rev ?: 'Rev. : 00' }}</h3>
                </div>

                <!-- Preamble Narration -->
                <div class="doc-preamble">
                    {{ $beritaAcaraDisposal->narasi_pembuka ?: 'Berdasarkan pemeriksaan dari team IT pabrik, diketahui bahwa terdapat kerusakan pada beberapa asset IT. Kerusakan pada unit tersebut tidak bisa diperbaiki, maka dari itu diperlukan tindakan jual berat pada aset tersebut. Keterangan kerusakan dan penjelasan asset dicantumkan pada halaman lampiran.' }}
                </div>

                <!-- Items Table -->
                @php
                    $rawItems = is_array($beritaAcaraDisposal->items) ? $beritaAcaraDisposal->items : json_decode($beritaAcaraDisposal->items ?? '[]', true);
                    if (!is_array($rawItems) || count($rawItems) === 0) {
                        $rawItems = [
                            ['no' => 1, 'kode_asset' => '', 'nama_asset' => 'Printer LX 310', 'qty' => 4, 'uom' => 'Unit', 'keterangan' => 'Sudah mati total, usia 10 tahun'],
                            ['no' => 2, 'kode_asset' => '', 'nama_asset' => 'Monitor', 'qty' => 5, 'uom' => 'Unit', 'keterangan' => 'Sudah mati total, usia 10 tahun'],
                            ['no' => 3, 'kode_asset' => '', 'nama_asset' => 'Ketrit printer KZ A85', 'qty' => 21, 'uom' => 'Unit', 'keterangan' => 'Sudah mati total, usia 10 tahun'],
                            ['no' => 4, 'kode_asset' => '', 'nama_asset' => 'Mouse', 'qty' => 41, 'uom' => '', 'keterangan' => ''],
                            ['no' => 5, 'kode_asset' => '', 'nama_asset' => 'Batrai UPS', 'qty' => 7, 'uom' => '', 'keterangan' => ''],
                            ['no' => 6, 'kode_asset' => '', 'nama_asset' => 'Batrai Leptop', 'qty' => 1, 'uom' => '', 'keterangan' => ''],
                            ['no' => 7, 'kode_asset' => '', 'nama_asset' => 'Kyboard', 'qty' => 9, 'uom' => '', 'keterangan' => ''],
                            ['no' => 8, 'kode_asset' => '', 'nama_asset' => 'Swich Hp 16 Port', 'qty' => 1, 'uom' => '', 'keterangan' => ''],
                            ['no' => 9, 'kode_asset' => '', 'nama_asset' => 'Canon F190500', 'qty' => 1, 'uom' => 'Unit', 'keterangan' => 'Mesin sudah mati total']
                        ];
                    }
                    $rowCount = count($rawItems);
                    $divisiText = $beritaAcaraDisposal->divisi ?: 'IT PABRIK';
                    $divisiFormatted = str_replace(' ', '<br>', $divisiText);
                @endphp

                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 14%;">Divisi</th>
                            <th style="width: 14%;">Kode Asset</th>
                            <th style="width: 6%;">NO</th>
                            <th style="width: 32%;">Nama Asset</th>
                            <th style="width: 7%;">QTY</th>
                            <th style="width: 8%;">UOM</th>
                            <th style="width: 19%;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rawItems as $index => $item)
                        <tr>
                            @if($index === 0)
                            <td rowspan="{{ $rowCount }}" class="cell-divisi">
                                {!! $divisiFormatted !!}
                            </td>
                            @endif
                            <td class="text-center">{{ $item['kode_asset'] ?? '' }}</td>
                            <td class="text-center">{{ $item['no'] ?? ($index + 1) }}</td>
                            <td>{{ $item['nama_asset'] ?? ($item['nama_aset'] ?? '') }}</td>
                            <td class="text-center">{{ $item['qty'] ?? ($item['jumlah'] ?? 1) }}</td>
                            <td class="text-center">{{ $item['uom'] ?? '' }}</td>
                            <td>{{ $item['keterangan'] ?? ($item['alasan_disposal'] ?? '') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Closing Line -->
                <div class="doc-closing">
                    Demikian berita acara ini di buat, Atas perhatiannya kami ucapkan terima kasih.
                </div>
            </div>

            <!-- Signatories Section (5 Signatories Balanced 20% Columns) -->
            <div class="signatures-container">
                <table class="sig-table">
                    <thead>
                        <tr>
                            <th style="width: 20%;">Membuat,</th>
                            <th colspan="2" style="width: 40%;">Memeriksa,</th>
                            <th colspan="2" style="width: 40%;">Menyetujui,</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="sig-name">{{ $beritaAcaraDisposal->nama_pembuat ?: 'Qois Abdul Qudus' }}</div>
                                <div class="sig-title">({{ $beritaAcaraDisposal->jabatan_pembuat ?: 'IT Pabrik' }})</div>
                            </td>
                            <td>
                                <div class="sig-name">{{ $beritaAcaraDisposal->nama_pemeriksa_1 ?: 'Alex Asmel' }}</div>
                                <div class="sig-title">({{ $beritaAcaraDisposal->jabatan_pemeriksa_1 ?: 'HC & GA Head' }})</div>
                            </td>
                            <td>
                                <div class="sig-name">{{ $beritaAcaraDisposal->nama_pemeriksa_2 ?: 'Basuki' }}</div>
                                <div class="sig-title">({{ $beritaAcaraDisposal->jabatan_pemeriksa_2 ?: 'Ka. Maintenance' }})</div>
                            </td>
                            <td>
                                <div class="sig-name">{{ $beritaAcaraDisposal->nama_penyetuju_1 ?: 'Novan Prihasa' }}</div>
                                <div class="sig-title">({{ $beritaAcaraDisposal->jabatan_penyetuju_1 ?: 'Factory Head' }})</div>
                            </td>
                            <td>
                                <div class="sig-name">{{ $beritaAcaraDisposal->nama_penyetuju_2 ?: 'Agus Sapto' }}</div>
                                <div class="sig-title">({{ $beritaAcaraDisposal->jabatan_penyetuju_2 ?: 'Wakil Management' }})</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PAGE 2+: LAMPIRAN FOTO ASSETS -->
        @php
            $photos = is_array($beritaAcaraDisposal->foto_lampiran) ? $beritaAcaraDisposal->foto_lampiran : json_decode($beritaAcaraDisposal->foto_lampiran ?? '[]', true);
        @endphp

        @if(is_array($photos) && count($photos) > 0)
            @php
                $photoChunks = array_chunk($photos, 4);
            @endphp

            @foreach($photoChunks as $pageIdx => $chunk)
            <div class="a4-sheet">
                <div>
                    @if($pageIdx === 0)
                    <div class="lampiran-title">LAMPIRAN</div>
                    @else
                    <div class="lampiran-title">LAMPIRAN (Lanjutan)</div>
                    @endif

                    <div class="photo-grid-2x2">
                        @foreach($chunk as $pIndex => $photo)
                        @php
                            $rawPath = is_array($photo) ? ($photo['path'] ?? '') : $photo;
                            $cleanPath = ltrim(str_replace(['public/', 'storage/'], '', $rawPath), '/');
                            $imgUrl = asset('storage/' . $cleanPath);
                        @endphp
                        <div class="photo-box">
                            <img src="{{ $imgUrl }}" alt="Foto Asset {{ ($pageIdx * 4) + $pIndex + 1 }}">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        @endif
    </div>

</body>
</html>
