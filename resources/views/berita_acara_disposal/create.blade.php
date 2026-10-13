@extends('layouts.admin')

@section('title', 'Buat Berita Acara Disposal Asset')

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
        padding: 12px 16px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.12);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .ba-card-title {
        font-family: 'Outfit', sans-serif !important;
        font-size: 0.9rem !important;
        font-weight: 700 !important;
        color: var(--color-ink-0, #f8fafc) !important;
        margin: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
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
    textarea.ba-input {
        height: auto;
        min-height: 70px;
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
    }
    .ba-btn-primary {
        background: #4f46e5;
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
    }
    .ba-btn-primary:hover {
        background: #4338ca;
        color: #ffffff;
        transform: translateY(-1px);
    }
    .ba-btn-print {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
    }
    .ba-btn-print:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: #ffffff;
        transform: translateY(-1px);
    }
    .ba-btn-secondary {
        background: rgba(148, 163, 184, 0.15);
        color: var(--color-ink-0, #f8fafc);
        border: 1px solid rgba(148, 163, 184, 0.25);
    }
    .ba-btn-secondary:hover {
        background: rgba(148, 163, 184, 0.25);
        color: var(--color-ink-0, #f8fafc);
    }

    /* Modern Hallmark Dropzone Styling (Gambar 2 Redesign Fix) */
    .ba-dropzone {
        border: 2px dashed rgba(99, 102, 241, 0.4);
        background: rgba(15, 23, 42, 0.3);
        border-radius: 14px;
        padding: 24px 16px;
        text-align: center;
        cursor: pointer;
        transition: all 180ms cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ba-dropzone:hover, .ba-dropzone.dragover {
        border-color: #6366f1;
        background: rgba(99, 102, 241, 0.08);
        transform: translateY(-1px);
    }
    .ba-dropzone-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: rgba(99, 102, 241, 0.18);
        border: 1px solid rgba(99, 102, 241, 0.3);
        color: #818cf8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin: 0 auto 10px auto;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
    }
    .ba-dropzone-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--color-ink-0, #f8fafc);
        margin-bottom: 4px;
    }
    .ba-dropzone-subtitle {
        font-size: 0.78rem;
        color: var(--color-ink-2, #94a3b8);
        max-width: 480px;
        margin: 0 auto;
        line-height: 1.4;
    }
    .ba-dropzone-btn {
        background: rgba(99, 102, 241, 0.2);
        color: #818cf8;
        border: 1px solid rgba(99, 102, 241, 0.4);
        border-radius: 8px;
        padding: 7px 16px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 12px;
        transition: all 180ms ease;
    }
    .ba-dropzone:hover .ba-dropzone-btn {
        background: #4f46e5;
        color: #ffffff;
        border-color: #4f46e5;
    }

    /* Live Preview Paper Styles matching EXACT template */
    .preview-paper {
        background: #ffffff;
        color: #000000;
        padding: 24px;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
        font-size: 10px;
        line-height: 1.35;
        font-family: 'Times New Roman', Times, serif;
        min-height: 650px;
    }
    .prev-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000000;
        margin-bottom: 12px;
        font-size: 9.5px;
    }
    .prev-table td, .prev-table th {
        border: 1px solid #000000;
        padding: 3px 5px;
    }

    /* Light mode adaptations */
    body.light-mode .ba-card {
        background: #ffffff;
        border-color: #e2e8f0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }
    body.light-mode .ba-input {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    body.light-mode .ba-dropzone {
        background: #f8fafc;
        border-color: #cbd5e1;
    }
</style>

<div class="container-fluid ba-studio-container">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h1 style="font-family: 'Outfit', sans-serif; font-size: 1.4rem; font-weight: 700; color: var(--color-ink-0, #f8fafc); margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-dumpster text-danger"></i> Buat Berita Acara Disposal Asset
            </h1>
            <p style="font-size: 0.85rem; color: var(--color-ink-2, #94a3b8); margin: 4px 0 0 0;">
                Formulir standar disposal aset perusahaan (CBA/MTN/F-023 Rev. 00)
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('berita-acara-disposal.index') }}" class="ba-btn-action ba-btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('berita-acara-disposal.store') }}" method="POST" enctype="multipart/form-data" id="baDisposalForm">
        @csrf
        <input type="hidden" name="and_print" id="andPrintInput" value="0">

        <div class="row g-4">
            <!-- Left Column: Form Inputs -->
            <div class="col-lg-7">
                <!-- Data Dokumen Card -->
                <div class="ba-card mb-4">
                    <div class="ba-card-header">
                        <h2 class="ba-card-title">
                            <i class="fas fa-file-alt text-indigo-400"></i> Data Surat &amp; Dokumen
                        </h2>
                    </div>
                    <div class="p-3">
                        <div class="row g-3">
                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold" style="font-size: 0.78rem;">Lokasi</label>
                                <input type="text" name="lokasi" id="lokasi" class="ba-input" value="Cikande" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold" style="font-size: 0.78rem;">Tanggal BA</label>
                                <input type="date" name="tanggal_ba" id="tanggal_ba" class="ba-input" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold" style="font-size: 0.78rem;">Nomor Surat (No)</label>
                                <input type="text" name="nomor_ba" id="nomor_ba" class="ba-input" value="{{ $nextNomorBa }}" required style="font-family: 'JetBrains Mono', monospace; font-weight: 600;">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold" style="font-size: 0.78rem;">Hal</label>
                                <input type="text" name="hal" id="hal" class="ba-input" value="Disposal Asset" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold" style="font-size: 0.78rem;">Lampiran Label</label>
                                <input type="text" name="lampiran_label" id="lampiran_label" class="ba-input" value="Foto Assets" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold" style="font-size: 0.78rem;">Divisi</label>
                                <input type="text" name="divisi" id="divisi" class="ba-input" value="IT PABRIK" required placeholder="Contoh: IT PABRIK">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold" style="font-size: 0.78rem;">Kepada Yth (Pisahkan Koma)</label>
                                <input type="text" name="kepada_yth" id="kepada_yth" class="ba-input" value="Ka. Bagian, Ka. Accounting, Ka. Asset" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold" style="font-size: 0.78rem;">Cc</label>
                                <input type="text" name="cc" id="cc" class="ba-input" value="Ka. Pabrik">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold" style="font-size: 0.78rem;">Kode Form</label>
                                <input type="text" name="form_code" id="form_code" class="ba-input" value="CBA/MTN/F-023">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold" style="font-size: 0.78rem;">Revisi</label>
                                <input type="text" name="form_rev" id="form_rev" class="ba-input" value="Rev. : 00">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preamble Card -->
                <div class="ba-card mb-4">
                    <div class="ba-card-header">
                        <h2 class="ba-card-title">
                            <i class="fas fa-paragraph text-warning"></i> Narasi Pembuka (Preamble)
                        </h2>
                    </div>
                    <div class="p-3">
                        <textarea name="narasi_pembuka" id="narasi_pembuka" class="ba-input" rows="3">{{ $defaultPreamble }}</textarea>
                    </div>
                </div>

                <!-- Asset Items Card -->
                <div class="ba-card mb-4">
                    <div class="ba-card-header">
                        <h2 class="ba-card-title">
                            <i class="fas fa-boxes text-emerald-400"></i> Daftar Aset yang Didisposal
                        </h2>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddItem" style="border-radius: 8px; font-size: 0.78rem;">
                            <i class="fas fa-plus"></i> Tambah Item
                        </button>
                    </div>
                    <div class="p-3">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="itemsTable" style="font-size: 0.82rem;">
                                <thead>
                                    <tr style="background: rgba(148, 163, 184, 0.08);">
                                        <th style="width: 15%;">Kode Asset</th>
                                        <th style="width: 32%;">Nama Asset</th>
                                        <th style="width: 10%;">QTY</th>
                                        <th style="width: 12%;">UOM</th>
                                        <th style="width: 25%;">Keterangan</th>
                                        <th style="width: 6%;">#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="text" name="items[0][kode_asset]" class="ba-input item-kode" placeholder="Kode"></td>
                                        <td><input type="text" name="items[0][nama_asset]" class="ba-input item-nama" value="Printer LX 310" required></td>
                                        <td><input type="number" name="items[0][qty]" class="ba-input item-qty" value="4" min="1" required></td>
                                        <td><input type="text" name="items[0][uom]" class="ba-input item-uom" value="Unit" placeholder="Unit/Pcs"></td>
                                        <td><input type="text" name="items[0][keterangan]" class="ba-input item-ket" value="Sudah mati total, usia 10 tahun"></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-link text-danger remove-item-btn" disabled><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" name="items[1][kode_asset]" class="ba-input item-kode" placeholder="Kode"></td>
                                        <td><input type="text" name="items[1][nama_asset]" class="ba-input item-nama" value="Monitor" required></td>
                                        <td><input type="number" name="items[1][qty]" class="ba-input item-qty" value="5" min="1" required></td>
                                        <td><input type="text" name="items[1][uom]" class="ba-input item-uom" value="Unit"></td>
                                        <td><input type="text" name="items[1][keterangan]" class="ba-input item-ket" value="Sudah mati total, usia 10 tahun"></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-link text-danger remove-item-btn"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Signatories Card -->
                <div class="ba-card mb-4">
                    <div class="ba-card-header">
                        <h2 class="ba-card-title">
                            <i class="fas fa-signature text-purple-400"></i> Penandatangan (5 Roles)
                        </h2>
                    </div>
                    <div class="p-3">
                        <div class="row g-3">
                            <div class="col-md-4 mb-2">
                                <label class="form-label font-weight-bold" style="font-size: 0.78rem;">Membuat (Nama)</label>
                                <input type="text" name="nama_pembuat" id="nama_pembuat" class="ba-input" value="Qois Abdul Qudus" required>
                                <label class="form-label text-muted mt-1" style="font-size: 0.72rem;">Jabatan</label>
                                <input type="text" name="jabatan_pembuat" id="jabatan_pembuat" class="ba-input" value="IT Pabrik">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label font-weight-bold" style="font-size: 0.78rem;">Memeriksa 1 (Nama)</label>
                                <input type="text" name="nama_pemeriksa_1" id="nama_pemeriksa_1" class="ba-input" value="Alex Asmel" required>
                                <label class="form-label text-muted mt-1" style="font-size: 0.72rem;">Jabatan</label>
                                <input type="text" name="jabatan_pemeriksa_1" id="jabatan_pemeriksa_1" class="ba-input" value="HC & GA Head">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label font-weight-bold" style="font-size: 0.78rem;">Memeriksa 2 (Nama)</label>
                                <input type="text" name="nama_pemeriksa_2" id="nama_pemeriksa_2" class="ba-input" value="Basuki" required>
                                <label class="form-label text-muted mt-1" style="font-size: 0.72rem;">Jabatan</label>
                                <input type="text" name="jabatan_pemeriksa_2" id="jabatan_pemeriksa_2" class="ba-input" value="Ka. Maintenance">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold" style="font-size: 0.78rem;">Menyetujui 1 (Nama)</label>
                                <input type="text" name="nama_penyetuju_1" id="nama_penyetuju_1" class="ba-input" value="Novan Prihasa" required>
                                <label class="form-label text-muted mt-1" style="font-size: 0.72rem;">Jabatan</label>
                                <input type="text" name="jabatan_penyetuju_1" id="jabatan_penyetuju_1" class="ba-input" value="Factory Head">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold" style="font-size: 0.78rem;">Menyetujui 2 (Nama)</label>
                                <input type="text" name="nama_penyetuju_2" id="nama_penyetuju_2" class="ba-input" value="Agus Sapto" required>
                                <label class="form-label text-muted mt-1" style="font-size: 0.72rem;">Jabatan</label>
                                <input type="text" name="jabatan_penyetuju_2" id="jabatan_penyetuju_2" class="ba-input" value="Wakil Management">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modern Redesigned Dropzone Card (Gambar 2 Redesign Fix) -->
                <div class="ba-card mb-4">
                    <div class="ba-card-header">
                        <h2 class="ba-card-title">
                            <i class="fas fa-camera text-sky-400"></i> Lampiran Foto Assets
                        </h2>
                    </div>
                    <div class="p-3">
                        <div class="ba-dropzone" id="baDropzone" onclick="document.getElementById('fotoFilesInput').click();">
                            <input type="file" name="foto_files[]" id="fotoFilesInput" multiple accept="image/*" style="display: none;">
                            <div class="ba-dropzone-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="ba-dropzone-title">Upload atau Drag &amp; Drop Foto Assets Di Sini</div>
                            <div class="ba-dropzone-subtitle">Format pendukung: JPG, PNG, WEBP (Maks. 10MB/foto). Foto akan otomatis ditata 2x2 per halaman lampiran.</div>
                            <button type="button" class="ba-dropzone-btn">
                                <i class="fas fa-plus-circle"></i> Pilih File Foto
                            </button>
                        </div>
                        <div id="selectedPhotosPreview" class="row g-2 mt-3" style="display: none;"></div>
                    </div>
                </div>

                <!-- Submit Bar -->
                <div class="d-flex align-items-center justify-content-end gap-3 mt-4">
                    <a href="{{ route('berita-acara-disposal.index') }}" class="ba-btn-action ba-btn-secondary">Batal</a>
                    <button type="submit" onclick="document.getElementById('andPrintInput').value='0';" class="ba-btn-action ba-btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <button type="submit" onclick="document.getElementById('andPrintInput').value='1';" class="ba-btn-action ba-btn-print">
                        <i class="fas fa-print"></i> Simpan &amp; Cetak
                    </button>
                </div>
            </div>

            <!-- Right Column: Live Real-Time A4 Preview -->
            <div class="col-lg-5">
                <div style="position: sticky; top: 80px;">
                    <div class="ba-card">
                        <div class="ba-card-header">
                            <h2 class="ba-card-title">
                                <i class="fas fa-eye text-cyan-400"></i> Live Real-Time Document Preview
                            </h2>
                            <span class="badge bg-primary" style="font-size: 0.7rem;" id="prev_form_code">CBA/MTN/F-023</span>
                        </div>
                        <div class="p-3">
                            <div class="preview-paper">
                                <!-- Top Right Date -->
                                <div class="text-end mb-3">
                                    <span id="prev_lokasi">Cikande</span>, <span id="prev_tanggal">{{ date('d F Y') }}</span>
                                </div>

                                <!-- Top Left Info -->
                                <div class="mb-3">
                                    <div>No : <strong id="prev_nomor" style="font-family: monospace;">{{ $nextNomorBa }}</strong></div>
                                    <div>Hal : <span id="prev_hal">Disposal Asset</span></div>
                                    <div>Lampiran : <span id="prev_lampiran">Foto Assets</span></div>
                                    <div class="mt-2">
                                        <div>Kepada Yth,</div>
                                        <div id="prev_kepada">Ka. Bagian<br>Ka. Accounting<br>Ka. Asset</div>
                                        <div>Cc : <span id="prev_cc">Ka. Pabrik</span></div>
                                    </div>
                                </div>

                                <!-- Center Title -->
                                <div class="text-center my-3">
                                    <div style="font-weight: bold; font-size: 11px;">BERITA ACARA DISPOSAL ASET</div>
                                    <div style="font-weight: bold;" id="prev_code_text">CBA/MTN/F-023</div>
                                    <div style="font-weight: bold;" id="prev_rev_text">Rev. : 00</div>
                                </div>

                                <!-- Narration -->
                                <div class="mb-3 text-justify" id="prev_narasi" style="line-height: 1.35;">
                                    {{ $defaultPreamble }}
                                </div>

                                <!-- Items Preview Table -->
                                <table class="prev-table" id="prev_items_table">
                                    <thead>
                                        <tr style="text-align: center; font-weight: bold;">
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
                                    </tbody>
                                </table>

                                <div class="my-2">
                                    Demikian berita acara ini di buat, Atas perhatiannya kami ucapkan terima kasih.
                                </div>

                                <!-- Signatures preview -->
                                <table style="width: 100%; border-collapse: collapse; margin-top: 25px; text-align: center; font-size: 9px; table-layout: fixed;">
                                    <tr>
                                        <th style="width: 20%; font-weight: normal; padding-bottom: 35px;">Membuat,</th>
                                        <th colspan="2" style="width: 40%; font-weight: normal; padding-bottom: 35px;">Memeriksa,</th>
                                        <th colspan="2" style="width: 40%; font-weight: normal; padding-bottom: 35px;">Menyetujui,</th>
                                    </tr>
                                    <tr>
                                        <td style="width: 20%; padding: 0 2px;">
                                            <div id="prev_pembuat_n" style="white-space: nowrap;">Qois Abdul Qudus</div>
                                            <div id="prev_pembuat_j" style="white-space: nowrap;">(IT Pabrik)</div>
                                        </td>
                                        <td style="width: 20%; padding: 0 2px;">
                                            <div id="prev_pemeriksa1_n" style="white-space: nowrap;">Alex Asmel</div>
                                            <div id="prev_pemeriksa1_j" style="white-space: nowrap;">(HC &amp; GA Head)</div>
                                        </td>
                                        <td style="width: 20%; padding: 0 2px;">
                                            <div id="prev_pemeriksa2_n" style="white-space: nowrap;">Basuki</div>
                                            <div id="prev_pemeriksa2_j" style="white-space: nowrap;">(Ka. Maintenance)</div>
                                        </td>
                                        <td style="width: 20%; padding: 0 2px;">
                                            <div id="prev_penyetuju1_n" style="white-space: nowrap;">Novan Prihasa</div>
                                            <div id="prev_penyetuju1_j" style="white-space: nowrap;">(Factory Head)</div>
                                        </td>
                                        <td style="width: 20%; padding: 0 2px;">
                                            <div id="prev_penyetuju2_n" style="white-space: nowrap;">Agus Sapto</div>
                                            <div id="prev_penyetuju2_j" style="white-space: nowrap;">(Wakil Management)</div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let itemIndex = 2;

        // Dynamic item addition
        document.getElementById('btnAddItem').addEventListener('click', function() {
            const tbody = document.querySelector('#itemsTable tbody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" name="items[${itemIndex}][kode_asset]" class="ba-input item-kode" placeholder="Kode"></td>
                <td><input type="text" name="items[${itemIndex}][nama_asset]" class="ba-input item-nama" required placeholder="Nama Asset"></td>
                <td><input type="number" name="items[${itemIndex}][qty]" class="ba-input item-qty" value="1" min="1" required></td>
                <td><input type="text" name="items[${itemIndex}][uom]" class="ba-input item-uom" placeholder="Unit"></td>
                <td><input type="text" name="items[${itemIndex}][keterangan]" class="ba-input item-ket" placeholder="Keterangan"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-link text-danger remove-item-btn"><i class="fas fa-trash"></i></button>
                </td>
            `;
            tbody.appendChild(row);
            itemIndex++;
            updateRemoveButtons();
            updateLivePreview();
        });

        // Delegate remove item
        document.querySelector('#itemsTable').addEventListener('click', function(e) {
            if (e.target.closest('.remove-item-btn')) {
                const btn = e.target.closest('.remove-item-btn');
                if (!btn.disabled) {
                    btn.closest('tr').remove();
                    updateRemoveButtons();
                    updateLivePreview();
                }
            }
        });

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('#itemsTable tbody tr');
            rows.forEach(r => {
                const btn = r.querySelector('.remove-item-btn');
                if (btn) btn.disabled = (rows.length === 1);
            });
        }

        // Handle Photo Dropzone preview
        const fileInput = document.getElementById('fotoFilesInput');
        const previewContainer = document.getElementById('selectedPhotosPreview');
        const dropzone = document.getElementById('baDropzone');

        if (fileInput && previewContainer) {
            fileInput.addEventListener('change', function() {
                previewContainer.innerHTML = '';
                if (this.files && this.files.length > 0) {
                    previewContainer.style.display = 'flex';
                    Array.from(this.files).forEach((file, i) => {
                        const col = document.createElement('div');
                        col.className = 'col-4 col-md-3';
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            col.innerHTML = `
                                <div class="border rounded p-1 text-center bg-dark text-white">
                                    <img src="${e.target.result}" style="height: 60px; width: 100%; object-fit: cover; border-radius: 4px;">
                                    <div class="mt-1 text-truncate" style="font-size: 0.68rem;">Foto ${i + 1}</div>
                                </div>
                            `;
                        };
                        reader.readAsDataURL(file);
                        previewContainer.appendChild(col);
                    });
                } else {
                    previewContainer.style.display = 'none';
                }
            });

            // Drag and drop events
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, e => { e.preventDefault(); dropzone.classList.add('dragover'); }, false);
            });
            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, e => { e.preventDefault(); dropzone.classList.remove('dragover'); }, false);
            });
            dropzone.addEventListener('drop', e => {
                if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                    fileInput.files = e.dataTransfer.files;
                    fileInput.dispatchEvent(new Event('change'));
                }
            });
        }

        // Live preview sync
        function updateLivePreview() {
            document.getElementById('prev_lokasi').innerText = document.getElementById('lokasi').value || 'Cikande';
            document.getElementById('prev_nomor').innerText = document.getElementById('nomor_ba').value || '-';
            document.getElementById('prev_hal').innerText = document.getElementById('hal').value || 'Disposal Asset';
            document.getElementById('prev_lampiran').innerText = document.getElementById('lampiran_label').value || 'Foto Assets';
            document.getElementById('prev_cc').innerText = document.getElementById('cc').value || 'Ka. Pabrik';
            
            const codeVal = document.getElementById('form_code').value || 'CBA/MTN/F-023';
            document.getElementById('prev_form_code').innerText = codeVal;
            document.getElementById('prev_code_text').innerText = codeVal;
            document.getElementById('prev_rev_text').innerText = document.getElementById('form_rev').value || 'Rev. : 00';

            const kepadaStr = document.getElementById('kepada_yth').value || 'Ka. Bagian, Ka. Accounting, Ka. Asset';
            document.getElementById('prev_kepada').innerHTML = kepadaStr.split(',').map(s => s.trim()).join('<br>');

            const tglVal = document.getElementById('tanggal_ba').value;
            if (tglVal) {
                const d = new Date(tglVal);
                document.getElementById('prev_tanggal').innerText = d.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
            }

            document.getElementById('prev_narasi').innerText = document.getElementById('narasi_pembuka').value || '';

            // Signatories
            document.getElementById('prev_pembuat_n').innerText = document.getElementById('nama_pembuat').value || 'Qois Abdul Qudus';
            document.getElementById('prev_pembuat_j').innerText = '(' + (document.getElementById('jabatan_pembuat').value || 'IT Pabrik') + ')';
            document.getElementById('prev_pemeriksa1_n').innerText = document.getElementById('nama_pemeriksa_1').value || 'Alex Asmel';
            document.getElementById('prev_pemeriksa1_j').innerText = '(' + (document.getElementById('jabatan_pemeriksa_1').value || 'HC & GA Head') + ')';
            document.getElementById('prev_pemeriksa2_n').innerText = document.getElementById('nama_pemeriksa_2').value || 'Basuki';
            document.getElementById('prev_pemeriksa2_j').innerText = '(' + (document.getElementById('jabatan_pemeriksa_2').value || 'Ka. Maintenance') + ')';
            document.getElementById('prev_penyetuju1_n').innerText = document.getElementById('nama_penyetuju_1').value || 'Novan Prihasa';
            document.getElementById('prev_penyetuju1_j').innerText = '(' + (document.getElementById('jabatan_penyetuju_1').value || 'Factory Head') + ')';
            document.getElementById('prev_penyetuju2_n').innerText = document.getElementById('nama_penyetuju_2').value || 'Agus Sapto';
            document.getElementById('prev_penyetuju2_j').innerText = '(' + (document.getElementById('jabatan_penyetuju_2').value || 'Wakil Management') + ')';

            // Table items sync
            const divisiVal = document.getElementById('divisi').value || 'IT PABRIK';
            const divisiFormatted = divisiVal.replace(' ', '<br>');
            const rows = document.querySelectorAll('#itemsTable tbody tr');
            const prevTbody = document.querySelector('#prev_items_table tbody');
            prevTbody.innerHTML = '';

            rows.forEach((r, idx) => {
                const kode = r.querySelector('.item-kode')?.value || '';
                const nama = r.querySelector('.item-nama')?.value || '';
                const qty = r.querySelector('.item-qty')?.value || '1';
                const uom = r.querySelector('.item-uom')?.value || '';
                const ket = r.querySelector('.item-ket')?.value || '';

                const tr = document.createElement('tr');
                let divisiTd = '';
                if (idx === 0) {
                    divisiTd = `<td rowspan="${rows.length}" style="text-align: center; vertical-align: middle; font-weight: bold;">${divisiFormatted}</td>`;
                }

                tr.innerHTML = `
                    ${divisiTd}
                    <td style="text-align: center;">${kode}</td>
                    <td style="text-align: center;">${idx + 1}</td>
                    <td>${nama}</td>
                    <td style="text-align: center;">${qty}</td>
                    <td style="text-align: center;">${uom}</td>
                    <td>${ket}</td>
                `;
                prevTbody.appendChild(tr);
            });
        }

        document.getElementById('baDisposalForm').addEventListener('input', updateLivePreview);
        updateLivePreview();
    });
</script>
@endpush
@endsection
