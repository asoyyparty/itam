@extends('layouts.admin')

@section('title', 'Detail Berita Acara Disposal - ' . $beritaAcaraDisposal->nomor_ba)

@section('content')
<!-- Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 -->
<!-- Hallmark · genre: modern-minimal · macrostructure: workbench · design-system: design.md · designed-as-app -->
<style>
    .ba-studio-container {
        padding-bottom: 50px;
    }
    .ba-btn-action {
        height: 38px;
        padding: 0 18px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.84rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 180ms cubic-bezier(0.16, 1, 0.3, 1);
        border: none;
        cursor: pointer;
        text-decoration: none !important;
    }
    .ba-btn-primary {
        background: #4f46e5;
        color: #ffffff;
    }
    .ba-btn-primary:hover {
        background: #4338ca;
        color: #ffffff;
    }
    .ba-btn-print {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
    }
    .ba-btn-print:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: #ffffff;
    }
    .ba-btn-secondary {
        background: rgba(148, 163, 184, 0.15);
        color: var(--color-ink-0, #f8fafc);
        border: 1px solid rgba(148, 163, 184, 0.25);
    }

    /* A4 Document Preview Simulator matching EXACT template */
    .a4-preview-card {
        background: #ffffff;
        color: #000000;
        padding: 30px 35px;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        font-size: 11pt;
        line-height: 1.35;
        font-family: 'Times New Roman', Times, serif;
        max-width: 800px;
        margin: 0 auto;
    }
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
    }
    .items-table td {
        border: 1px solid #000000;
        padding: 5px 6px;
        vertical-align: middle;
    }
</style>

<div class="container-fluid ba-studio-container">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h1 style="font-family: 'Outfit', sans-serif; font-size: 1.4rem; font-weight: 700; color: var(--color-ink-0, #f8fafc); margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-file-alt text-indigo-400"></i> Detail BA Disposal: {{ $beritaAcaraDisposal->nomor_ba }}
            </h1>
            <p style="font-size: 0.85rem; color: var(--color-ink-2, #94a3b8); margin: 4px 0 0 0;">
                Format resmi CBA/MTN/F-023 Rev. 00
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('berita-acara-disposal.index') }}" class="ba-btn-action ba-btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('berita-acara-disposal.edit', $beritaAcaraDisposal) }}" class="ba-btn-action ba-btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('berita-acara-disposal.print', $beritaAcaraDisposal) }}" target="_blank" class="ba-btn-action ba-btn-print">
                <i class="fas fa-print"></i> Cetak / PDF
            </a>
        </div>
    </div>

    <!-- Main Simulator Sheet -->
    <div class="a4-preview-card">
        <!-- Top Right Date -->
        <div style="text-align: right; margin-bottom: 20px;">
            {{ $beritaAcaraDisposal->lokasi ?: 'Cikande' }}, {{ \Carbon\Carbon::parse($beritaAcaraDisposal->tanggal_ba)->translatedFormat('d F Y') }}
        </div>

        <!-- Header Info -->
        <div style="margin-bottom: 25px;">
            <table style="border-collapse: collapse;">
                <tr>
                    <td style="width: 80px;">No</td>
                    <td style="width: 15px;">:</td>
                    <td><strong>{{ $beritaAcaraDisposal->nomor_ba }}</strong></td>
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
                @foreach($kepadaList as $k)
                    <div>{{ trim($k) }}</div>
                @endforeach
                @if(!empty($beritaAcaraDisposal->cc))
                    <div>Cc : {{ $beritaAcaraDisposal->cc }}</div>
                @endif
            </div>
        </div>

        <!-- Center Title Block -->
        <div style="text-align: center; margin-bottom: 25px;">
            <div style="font-size: 13pt; font-weight: bold; text-transform: uppercase;">BERITA ACARA DISPOSAL ASET</div>
            <div style="font-size: 11pt; font-weight: bold;">{{ $beritaAcaraDisposal->form_code ?: 'CBA/MTN/F-023' }}</div>
            <div style="font-size: 11pt; font-weight: bold;">{{ $beritaAcaraDisposal->form_rev ?: 'Rev. : 00' }}</div>
        </div>

        <!-- Narration -->
        <div style="margin-bottom: 20px; text-align: justify; line-height: 1.4;">
            {{ $beritaAcaraDisposal->narasi_pembuka ?: 'Berdasarkan pemeriksaan dari team IT pabrik, diketahui bahwa terdapat kerusakan pada beberapa asset IT. Kerusakan pada unit tersebut tidak bisa diperbaiki, maka dari itu diperlukan tindakan jual berat pada aset tersebut. Keterangan kerusakan dan penjelasan asset dicantumkan pada halaman lampiran.' }}
        </div>

        <!-- Items Table -->
        @php
            $rawItems = is_array($beritaAcaraDisposal->items) ? $beritaAcaraDisposal->items : json_decode($beritaAcaraDisposal->items ?? '[]', true);
            if (!is_array($rawItems) || count($rawItems) === 0) {
                $rawItems = [
                    ['no' => 1, 'kode_asset' => '', 'nama_asset' => 'Printer LX 310', 'qty' => 4, 'uom' => 'Unit', 'keterangan' => 'Sudah mati total, usia 10 tahun']
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
                    <td rowspan="{{ $rowCount }}" style="text-align: center; vertical-align: middle; font-weight: bold;">
                        {!! $divisiFormatted !!}
                    </td>
                    @endif
                    <td style="text-align: center;">{{ $item['kode_asset'] ?? '' }}</td>
                    <td style="text-align: center;">{{ $item['no'] ?? ($index + 1) }}</td>
                    <td>{{ $item['nama_asset'] ?? ($item['nama_aset'] ?? '') }}</td>
                    <td style="text-align: center;">{{ $item['qty'] ?? ($item['jumlah'] ?? 1) }}</td>
                    <td style="text-align: center;">{{ $item['uom'] ?? '' }}</td>
                    <td>{{ $item['keterangan'] ?? ($item['alasan_disposal'] ?? '') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Closing Line -->
        <div style="margin-bottom: 35px;">
            Demikian berita acara ini di buat, Atas perhatiannya kami ucapkan terima kasih.
        </div>

        <!-- Signatories Table -->
        <table style="width: 100%; border-collapse: collapse; text-align: center; table-layout: fixed;">
            <thead>
                <tr>
                    <th style="width: 20%; font-weight: normal; padding-bottom: 50px;">Membuat,</th>
                    <th colspan="2" style="width: 40%; font-weight: normal; padding-bottom: 50px;">Memeriksa,</th>
                    <th colspan="2" style="width: 40%; font-weight: normal; padding-bottom: 50px;">Menyetujui,</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 20%; padding: 0 4px; vertical-align: top;">
                        <div style="white-space: nowrap;">{{ $beritaAcaraDisposal->nama_pembuat ?: 'Qois Abdul Qudus' }}</div>
                        <div style="white-space: nowrap; font-size: 9.5pt;">({{ $beritaAcaraDisposal->jabatan_pembuat ?: 'IT Pabrik' }})</div>
                    </td>
                    <td style="width: 20%; padding: 0 4px; vertical-align: top;">
                        <div style="white-space: nowrap;">{{ $beritaAcaraDisposal->nama_pemeriksa_1 ?: 'Alex Asmel' }}</div>
                        <div style="white-space: nowrap; font-size: 9.5pt;">({{ $beritaAcaraDisposal->jabatan_pemeriksa_1 ?: 'HC & GA Head' }})</div>
                    </td>
                    <td style="width: 20%; padding: 0 4px; vertical-align: top;">
                        <div style="white-space: nowrap;">{{ $beritaAcaraDisposal->nama_pemeriksa_2 ?: 'Basuki' }}</div>
                        <div style="white-space: nowrap; font-size: 9.5pt;">({{ $beritaAcaraDisposal->jabatan_pemeriksa_2 ?: 'Ka. Maintenance' }})</div>
                    </td>
                    <td style="width: 20%; padding: 0 4px; vertical-align: top;">
                        <div style="white-space: nowrap;">{{ $beritaAcaraDisposal->nama_penyetuju_1 ?: 'Novan Prihasa' }}</div>
                        <div style="white-space: nowrap; font-size: 9.5pt;">({{ $beritaAcaraDisposal->jabatan_penyetuju_1 ?: 'Factory Head' }})</div>
                    </td>
                    <td style="width: 20%; padding: 0 4px; vertical-align: top;">
                        <div style="white-space: nowrap;">{{ $beritaAcaraDisposal->nama_penyetuju_2 ?: 'Agus Sapto' }}</div>
                        <div style="white-space: nowrap; font-size: 9.5pt;">({{ $beritaAcaraDisposal->jabatan_penyetuju_2 ?: 'Wakil Management' }})</div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Photos section if exists -->
        @php
            $photos = is_array($beritaAcaraDisposal->foto_lampiran) ? $beritaAcaraDisposal->foto_lampiran : json_decode($beritaAcaraDisposal->foto_lampiran ?? '[]', true);
        @endphp
        @if(is_array($photos) && count($photos) > 0)
            <div style="border-top: 1px solid #000; margin-top: 40px; padding-top: 20px;">
                <div style="text-align: center; font-size: 14pt; font-weight: bold; margin-bottom: 20px;">LAMPIRAN</div>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                    @foreach($photos as $pIdx => $photo)
                        @php
                            $rawPath = is_array($photo) ? ($photo['path'] ?? '') : $photo;
                            $cleanPath = ltrim(str_replace(['public/', 'storage/'], '', $rawPath), '/');
                            $imgUrl = asset('storage/' . $cleanPath);
                        @endphp
                        <div style="text-align: center;">
                            <img src="{{ $imgUrl }}" style="max-height: 220px; width: 100%; object-fit: contain; border: 1px solid #000;">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
