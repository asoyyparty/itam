@extends('layouts.admin')

@section('title', "Audit Studio - {$session->session_code}")

@section('content')
<!-- Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 -->
<!-- Hallmark · genre: modern-minimal · macrostructure: workbench · design-system: design.md · designed-as-app -->
<style>
    .so-studio-container {
        padding-bottom: 50px;
    }
    .so-card {
        background: var(--color-paper-1, #1e293b);
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }
    .so-input {
        background: rgba(15, 23, 42, 0.35);
        border: 1px solid rgba(148, 163, 184, 0.22);
        border-radius: 10px;
        color: var(--color-ink-0, #f8fafc);
        font-size: 0.85rem;
        padding: 8px 12px;
        height: 42px;
        width: 100%;
    }
    .so-input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
        outline: none;
    }
    .so-btn-action {
        height: 42px;
        padding: 0 18px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 180ms cubic-bezier(0.16, 1, 0.3, 1);
        border: none;
        cursor: pointer;
        text-decoration: none !important;
        white-space: nowrap;
    }
    .so-btn-primary {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
    }
    .so-btn-primary:hover {
        background: linear-gradient(135deg, #4338ca, #4f46e5);
        color: #ffffff;
    }
    .so-btn-success {
        background: linear-gradient(135deg, #059669, #10b981);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
    }
    .so-btn-secondary {
        background: rgba(148, 163, 184, 0.15);
        color: var(--color-ink-0, #f8fafc);
        border: 1px solid rgba(148, 163, 184, 0.25);
    }

    /* Camera Scanner Viewfinder Box */
    .qr-scanner-box {
        background: #0f172a;
        border: 2px dashed rgba(99, 102, 241, 0.4);
        border-radius: 16px;
        overflow: hidden;
        position: relative;
        min-height: 280px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    #qr-reader {
        width: 100% !important;
        border: none !important;
    }
    #qr-reader video {
        object-fit: cover !important;
        border-radius: 14px !important;
        width: 100% !important;
    }

    /* Live Feed Status Toast */
    .scan-status-feed {
        border-radius: 12px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.88rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .scan-status-matched {
        background: rgba(16, 185, 129, 0.15);
        border: 1px solid rgba(16, 185, 129, 0.35);
        color: #34d399;
    }
    .scan-status-displaced {
        background: rgba(245, 158, 11, 0.15);
        border: 1px solid rgba(245, 158, 11, 0.35);
        color: #fbbf24;
    }
    .scan-status-unknown {
        background: rgba(239, 68, 68, 0.15);
        border: 1px solid rgba(239, 68, 68, 0.35);
        color: #f87171;
    }

    /* Stat Counters Pill */
    .stat-pill-box {
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 12px;
        padding: 10px 14px;
        text-align: center;
    }

    /* SweetAlert2 Rounded */
    .swal2-minimal-rounded {
        border-radius: 18px !important;
        background: var(--color-paper-1, #1e293b) !important;
        color: var(--color-ink-0, #f8fafc) !important;
        border: 1px solid rgba(148, 163, 184, 0.2) !important;
    }
</style>

<div class="container-fluid so-studio-container">
    <!-- Header & Action Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4" style="gap: 16px;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1" style="gap: 8px;">
                <a href="{{ route('stock-opname.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size: 0.78rem;">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <span class="badge" style="background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3); font-family: var(--font-mono, monospace); font-size: 0.8rem; padding: 5px 9px;">
                    {{ $session->session_code }}
                </span>
                @if($session->status === 'completed')
                <span class="badge badge-success px-3 py-1" style="font-size: 0.78rem;">SELESAI</span>
                @endif
            </div>
            <h1 class="h3 font-weight-bold mb-1 text-white">{{ $session->title }}</h1>
            <p class="text-muted small mb-0">
                Target Audit: <strong class="text-white">{{ $session->location ? $session->location->name : 'Semua Lokasi' }}</strong>
                • Kategori: <strong class="text-white">{{ $session->category ? $session->category->name : 'Semua Kategori' }}</strong>
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap: 10px;">
            <a href="{{ route('stock-opname.export', $session->id) }}" class="so-btn-action so-btn-secondary">
                <i class="fas fa-file-excel text-success"></i>
                <span>Ekspor Report CSV</span>
            </a>
            @if($session->status === 'in_progress')
            @can('menu_stock_opname')
            <form action="{{ route('stock-opname.complete', $session->id) }}" method="POST" id="complete-session-form">
                @csrf
                <button type="button" class="so-btn-action so-btn-success" onclick="confirmCompleteSession()">
                    <i class="fas fa-check-double"></i>
                    <span>Selesaikan Audit</span>
                </button>
            </form>
            @endcan
            @endif
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-lg border-0 shadow-sm mb-4" role="alert" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3) !important; color: #34d399;">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-lg border-0 shadow-sm mb-4" role="alert" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3) !important; color: #f87171;">
        <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
        <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Top Live Stats Counters Bar -->
    <div class="so-card p-3 mb-4">
        <div class="row text-white text-center align-items-center" style="row-gap: 12px;">
            <div class="col-6 col-md-2">
                <div class="stat-pill-box">
                    <div class="text-muted small text-uppercase" style="font-size: 0.68rem;">Total Target</div>
                    <div class="h4 font-weight-bold mb-0 text-white" id="statTotalExpected">{{ number_format($session->total_expected) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="stat-pill-box" style="border-color: rgba(16, 185, 129, 0.3);">
                    <div class="text-muted small text-uppercase" style="font-size: 0.68rem; color: #34d399 !important;">Terverifikasi (Matched)</div>
                    <div class="h4 font-weight-bold mb-0 text-success" id="statTotalMatched">{{ number_format($session->total_matched) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="stat-pill-box" style="border-color: rgba(245, 158, 11, 0.3);">
                    <div class="text-muted small text-uppercase" style="font-size: 0.68rem; color: #fbbf24 !important;">Berpindah (Displaced)</div>
                    <div class="h4 font-weight-bold mb-0 text-warning" id="statTotalDisplaced">{{ number_format($session->total_displaced) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="stat-pill-box" style="border-color: rgba(239, 68, 68, 0.3);">
                    <div class="text-muted small text-uppercase" style="font-size: 0.68rem; color: #f87171 !important;">Belum Ditemukan</div>
                    <div class="h4 font-weight-bold mb-0 text-danger" id="statTotalMissing">{{ number_format($session->total_missing) }}</div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="stat-pill-box text-left px-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small font-weight-bold">Progres Audit</span>
                        <span class="font-weight-bold text-white" id="statProgressText">{{ $session->progress_percentage }}%</span>
                    </div>
                    <div class="progress" style="height: 10px; background: rgba(148, 163, 184, 0.2); border-radius: 5px;">
                        <div id="statProgressBar" class="progress-bar bg-success" role="progressbar" style="width: {{ $session->progress_percentage }}%; border-radius: 5px;" aria-valuenow="{{ $session->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($session->status === 'in_progress')
    <!-- Interactive Scanner Console Grid -->
    <div class="row mb-4">
        <!-- Left: Live Camera QR Viewfinder -->
        <div class="col-12 col-lg-5 mb-3 mb-lg-0">
            <div class="so-card p-3 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="font-weight-bold text-white mb-0">
                            <i class="fas fa-camera text-primary mr-2"></i> Camera QR Code Scanner
                        </h6>
                        <button type="button" id="btnToggleCamera" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size: 0.78rem; font-weight: 600;">
                            <i class="fas fa-video mr-1"></i> Mulai Kamera
                        </button>
                    </div>

                    <div class="qr-scanner-box mb-3" id="qrScannerWrapper">
                        <div id="qrScannerPlaceholder" class="text-center p-4 text-muted">
                            <i class="fas fa-qrcode fa-3x mb-2 text-indigo opacity-60"></i>
                            <div class="font-weight-bold text-white mb-1">Kamera Belum Aktif</div>
                            <p class="small text-muted mb-0">Klik <strong>"Mulai Kamera"</strong> untuk mengaktifkan pemindai kamera HP / Laptop.</p>
                        </div>
                        <div id="qr-reader" style="display: none;"></div>
                    </div>
                </div>

                <!-- Current Audit Scan Location Selector -->
                <div class="p-2 rounded mt-2" style="background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(148, 163, 184, 0.12);">
                    <label class="form-label text-muted small font-weight-bold mb-1">Lokasi Hasil Scan Saat Ini:</label>
                    <select id="scannedLocationSelect" class="so-input" style="height: 36px; font-size: 0.8rem;">
                        @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ $session->location_id == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Right: Barcode Scanner Gun / Manual Input & Live Feed Status -->
        <div class="col-12 col-lg-7">
            <div class="so-card p-3 h-100 d-flex flex-column justify-content-between">
                <div>
                    <h6 class="font-weight-bold text-white mb-2">
                        <i class="fas fa-barcode text-warning mr-2"></i> Input Manual / Barcode Scanner Gun
                    </h6>
                    <p class="text-muted small mb-3">Arahkan *barcode scanner gun* atau ketik Kode Aset (misal: AST-001) / Serial Number lalu tekan Enter.</p>

                    <form id="manualScanForm" onsubmit="handleManualScan(event)">
                        <div class="input-group mb-3">
                            <input type="text" id="manualScanInput" class="so-input" style="border-top-right-radius: 0; border-bottom-right-radius: 0;" placeholder="Scan barcode / ketik kode aset..." autofocus autocomplete="off">
                            <div class="input-group-append">
                                <button type="submit" class="so-btn-action so-btn-primary" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                    <i class="fas fa-search"></i> Process Scan
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Realtime Scan Status Feed Container -->
                    <div id="scanStatusFeedBox" class="mt-3" style="display: none;"></div>
                </div>

                <div class="p-3 rounded mt-3" style="background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(148, 163, 184, 0.12);">
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <i class="fas fa-info-circle text-info" style="font-size: 1.2rem;"></i>
                        <div class="text-muted small" style="font-size: 0.78rem; line-height: 1.4;">
                            <strong class="text-white">Tips Audit:</strong> Setiap barcode yang terscan akan otomatis diverifikasi lokasi dan nomor serinya. Efek suara *chime* akan berbunyi saat aset berhasil diverifikasi.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Scanned Items Feed & Audit Table -->
    <div class="so-card">
        <div class="p-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center" style="border-color: rgba(148, 163, 184, 0.12) !important; gap: 12px;">
            <h6 class="font-weight-bold text-white mb-0" style="font-size: 0.95rem;">
                <i class="fas fa-list-ul text-primary mr-2"></i> Daftar Aset Audit ({{ count($items) }})
            </h6>
            <!-- Table Status Tabs Filter -->
            <div class="d-flex flex-wrap gap-2" style="gap: 6px;">
                <button type="button" class="btn btn-sm btn-outline-secondary active item-filter-btn px-3" data-status="all" style="font-size: 0.78rem; font-weight: 600;">Semua ({{ count($items) }})</button>
                <button type="button" class="btn btn-sm btn-outline-success item-filter-btn px-3" data-status="matched" style="font-size: 0.78rem; font-weight: 600;">Matched ({{ $session->total_matched }})</button>
                <button type="button" class="btn btn-sm btn-outline-warning item-filter-btn px-3" data-status="displaced" style="font-size: 0.78rem; font-weight: 600;">Displaced ({{ $session->total_displaced }})</button>
                <button type="button" class="btn btn-sm btn-outline-danger item-filter-btn px-3" data-status="missing" style="font-size: 0.78rem; font-weight: 600;">Missing/Pending ({{ $session->total_missing + $session->items()->where('status', 'pending')->count() }})</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="color: var(--color-ink-0, #f8fafc);" id="auditItemsTable">
                <thead style="background: rgba(15, 23, 42, 0.6); border-bottom: 1px solid rgba(148, 163, 184, 0.15);">
                    <tr>
                        <th class="py-3 px-3 text-muted small text-uppercase" style="width: 40px;">#</th>
                        <th class="py-3 px-3 text-muted small text-uppercase">Kode Aset</th>
                        <th class="py-3 px-3 text-muted small text-uppercase">Nama & Kategori Aset</th>
                        <th class="py-3 px-3 text-muted small text-uppercase">Lokasi Ekspektasi</th>
                        <th class="py-3 px-3 text-muted small text-uppercase">Lokasi Hasil Scan</th>
                        <th class="py-3 px-3 text-muted small text-uppercase">Status Audit</th>
                        <th class="py-3 px-3 text-muted small text-uppercase">Kondisi Fisik</th>
                        <th class="py-3 px-3 text-muted small text-uppercase">Petugas & Waktu</th>
                        @if($session->status === 'in_progress')
                        <th class="py-3 px-3 text-muted small text-uppercase text-center" style="width: 100px;">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="auditItemsTableBody">
                    @forelse($items as $index => $item)
                    <tr class="audit-item-row item-status-{{ $item->status }}" data-status="{{ $item->status }}" id="item-row-{{ $item->asset_id }}" style="border-bottom: 1px solid rgba(148, 163, 184, 0.08);">
                        <td class="py-3 px-3 text-muted small">{{ $index + 1 }}</td>
                        <td class="py-3 px-3">
                            <span class="badge" style="background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3); font-family: var(--font-mono, monospace); font-size: 0.78rem; padding: 5px 9px;">
                                {{ $item->asset ? $item->asset->asset_tag : 'N/A' }}
                            </span>
                        </td>
                        <td class="py-3 px-3">
                            <div class="font-weight-bold text-white mb-1" style="font-size: 0.88rem;">{{ $item->asset ? $item->asset->name : 'N/A' }}</div>
                            <div class="text-muted small" style="font-size: 0.75rem;">
                                SN: {{ $item->asset && $item->asset->serial_number ? $item->asset->serial_number : '-' }}
                                • Kategori: {{ $item->asset && $item->asset->category ? $item->asset->category->name : '-' }}
                            </div>
                        </td>
                        <td class="py-3 px-3 text-muted small">
                            <span class="badge" style="background: rgba(148, 163, 184, 0.12); color: #cbd5e1; font-size: 0.75rem;">
                                <i class="fas fa-map-marker-alt mr-1"></i> {{ $item->expectedLocation ? $item->expectedLocation->name : '-' }}
                            </span>
                        </td>
                        <td class="py-3 px-3 text-muted small">
                            @if($item->scannedLocation)
                            <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.25); font-size: 0.75rem;">
                                <i class="fas fa-search-location mr-1"></i> {{ $item->scannedLocation->name }}
                            </span>
                            @else
                            <span class="text-muted font-italic" style="font-size: 0.75rem;">Belum Ter-scan</span>
                            @endif
                        </td>
                        <td class="py-3 px-3">
                            <span class="item-status-badge">
                                @if($item->status === 'matched')
                                <span class="badge badge-pill badge-success" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); font-size: 0.72rem; padding: 4px 10px;">
                                    <i class="fas fa-check-circle mr-1"></i> Terverifikasi (Matched)
                                </span>
                                @elseif($item->status === 'displaced')
                                <span class="badge badge-pill badge-warning" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); font-size: 0.72rem; padding: 4px 10px;">
                                    <i class="fas fa-exchange-alt mr-1"></i> Berpindah (Displaced)
                                </span>
                                @elseif($item->status === 'missing')
                                <span class="badge badge-pill badge-danger" style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); font-size: 0.72rem; padding: 4px 10px;">
                                    <i class="fas fa-times-circle mr-1"></i> Hilang (Missing)
                                </span>
                                @else
                                <span class="badge badge-pill badge-secondary" style="font-size: 0.72rem; padding: 4px 10px;">
                                    <i class="far fa-clock mr-1"></i> Belum Di-scan
                                </span>
                                @endif
                            </span>
                        </td>
                        <td class="py-3 px-3 text-muted small">
                            @if($item->physical_condition === 'good')
                            <span class="text-success"><i class="fas fa-check mr-1"></i> Baik</span>
                            @elseif($item->physical_condition === 'damaged')
                            <span class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> Rusak</span>
                            @else
                            <span class="text-warning"><i class="fas fa-tools mr-1"></i> Perlu Perbaikan</span>
                            @endif
                        </td>
                        <td class="py-3 px-3 text-muted small" style="white-space: nowrap;">
                            @if($item->scanned_at)
                            <div><i class="far fa-user mr-1 text-info"></i> {{ $item->scanner->name ?? 'System' }}</div>
                            <div class="text-muted" style="font-size: 0.72rem;"><i class="far fa-clock mr-1"></i> {{ $item->scanned_at->format('d/m H:i') }}</div>
                            @else
                            <span class="text-muted font-italic">-</span>
                            @endif
                        </td>
                        @if($session->status === 'in_progress')
                        <td class="py-3 px-3 text-center">
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-link text-muted p-1" data-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" style="background: var(--color-paper-1, #1e293b); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 10px;">
                                    <button type="button" class="dropdown-item text-success small" onclick="updateItemStatus({{ $item->id }}, 'matched')">
                                        <i class="fas fa-check mr-2"></i> Tandai Matched
                                    </button>
                                    <button type="button" class="dropdown-item text-warning small" onclick="updateItemStatus({{ $item->id }}, 'displaced')">
                                        <i class="fas fa-exchange-alt mr-2"></i> Tandai Displaced
                                    </button>
                                    <button type="button" class="dropdown-item text-danger small" onclick="updateItemStatus({{ $item->id }}, 'missing')">
                                        <i class="fas fa-times mr-2"></i> Tandai Missing
                                    </button>
                                </div>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-5 text-center text-muted">
                            <i class="fas fa-folder-open fa-3x mb-3 text-secondary opacity-50"></i>
                            <div class="h6 font-weight-bold text-white">Belum Ada Item Aset di Sesi Ini</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<!-- HTML5 QR Code Scanner Library -->
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let html5QrCode = null;
    let isCameraRunning = false;

    // Web Audio Chime Generator on Successful Scan
    function playAudioChime(type) {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);

            if (type === 'matched') {
                osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
                osc.frequency.setValueAtTime(880, ctx.currentTime + 0.1); // A5
                gain.gain.setValueAtTime(0.15, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.25);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.25);
            } else if (type === 'displaced') {
                osc.frequency.setValueAtTime(440, ctx.currentTime); // A4
                osc.frequency.setValueAtTime(554.37, ctx.currentTime + 0.1); // C#5
                gain.gain.setValueAtTime(0.15, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.3);
            } else {
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(220, ctx.currentTime); // A3
                gain.gain.setValueAtTime(0.2, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.3);
            }
        } catch(e) {
            console.log("Web Audio API not supported", e);
        }
    }

    // Toggle Camera Scanner
    document.getElementById('btnToggleCamera')?.addEventListener('click', function() {
        if (isCameraRunning) {
            stopCamera();
        } else {
            startCamera();
        }
    });

    function startCamera() {
        document.getElementById('qrScannerPlaceholder').style.display = 'none';
        document.getElementById('qr-reader').style.display = 'block';

        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("qr-reader");
        }

        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        html5QrCode.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanFailure
        ).then(() => {
            isCameraRunning = true;
            document.getElementById('btnToggleCamera').innerHTML = '<i class="fas fa-stop mr-1"></i> Hentikan Kamera';
            document.getElementById('btnToggleCamera').classList.replace('btn-outline-primary', 'btn-outline-danger');
        }).catch(err => {
            console.error(err);
            Swal.fire({
                title: 'Akses Kamera Gagal',
                text: 'Gagal mengakses kamera HP/Laptop. Pastikan izin kamera telah diberikan.',
                icon: 'error',
                customClass: { popup: 'swal2-minimal-rounded' }
            });
            stopCamera();
        });
    }

    function stopCamera() {
        if (html5QrCode && isCameraRunning) {
            html5QrCode.stop().then(() => {
                isCameraRunning = false;
                document.getElementById('qr-reader').style.display = 'none';
                document.getElementById('qrScannerPlaceholder').style.display = 'block';
                document.getElementById('btnToggleCamera').innerHTML = '<i class="fas fa-video mr-1"></i> Mulai Kamera';
                document.getElementById('btnToggleCamera').classList.replace('btn-outline-danger', 'btn-outline-primary');
            }).catch(err => console.error(err));
        }
    }

    let lastScannedCode = null;
    let lastScanTime = 0;

    function onScanSuccess(decodedText, decodedResult) {
        const now = Date.now();
        // Cooldown 2.5 seconds to prevent double scan trigger
        if (decodedText === lastScannedCode && (now - lastScanTime) < 2500) {
            return;
        }
        lastScannedCode = decodedText;
        lastScanTime = now;

        processAssetScan(decodedText);
    }

    function onScanFailure(error) {
        // Silent failure for video frames
    }

    function handleManualScan(e) {
        e.preventDefault();
        const input = document.getElementById('manualScanInput');
        const code = input.value.trim();
        if (code) {
            processAssetScan(code);
            input.value = '';
        }
    }

    // AJAX call to process scanned asset tag
    function processAssetScan(code) {
        const locationId = document.getElementById('scannedLocationSelect')?.value;

        fetch(`/stock-opname/{{ $session->id }}/scan`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                code: code,
                location_id: locationId
            })
        })
        .then(res => res.json())
        .then(data => {
            const feedBox = document.getElementById('scanStatusFeedBox');
            feedBox.style.display = 'block';

            if (data.success) {
                playAudioChime(data.status);

                const statusClass = data.status === 'matched' ? 'scan-status-matched' : 'scan-status-displaced';
                const statusIcon = data.status === 'matched' ? 'fas fa-check-circle text-success' : 'fas fa-exclamation-triangle text-warning';

                feedBox.innerHTML = `
                    <div class="scan-status-feed ${statusClass}">
                        <i class="${statusIcon} fa-2x"></i>
                        <div>
                            <div class="font-weight-bold">${data.message}</div>
                            <div class="small opacity-80">SN: ${data.asset.serial_number} • Kategori: ${data.asset.category} • Lokasi Scan: ${data.asset.scanned_location}</div>
                        </div>
                    </div>
                `;

                // Update Stats
                updateStatsUI(data.session_stats);

                // Reload or update table row dynamically
                setTimeout(() => window.location.reload(), 1200);
            } else {
                playAudioChime('unknown');
                feedBox.innerHTML = `
                    <div class="scan-status-feed scan-status-unknown">
                        <i class="fas fa-times-circle fa-2x text-danger"></i>
                        <div>
                            <div class="font-weight-bold">${data.message}</div>
                            <div class="small opacity-80">Pastikan kode barcode/QR terdaftar di katalog Aset ITAM.</div>
                        </div>
                    </div>
                `;
            }
        })
        .catch(err => {
            console.error(err);
            playAudioChime('unknown');
        });
    }

    function updateStatsUI(stats) {
        if (!stats) return;
        document.getElementById('statTotalExpected').innerText = stats.total_expected;
        document.getElementById('statTotalMatched').innerText = stats.total_matched;
        document.getElementById('statTotalDisplaced').innerText = stats.total_displaced;
        document.getElementById('statTotalMissing').innerText = stats.total_missing;
        document.getElementById('statProgressText').innerText = `${stats.progress_percentage}%`;
        
        const pb = document.getElementById('statProgressBar');
        if (pb) pb.style.width = `${stats.progress_percentage}%`;
    }

    // Manual status override
    function updateItemStatus(itemId, newStatus) {
        fetch(`/stock-opname/{{ $session->id }}/items/${itemId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }

    // Filter Table Rows
    document.querySelectorAll('.item-filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.item-filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const status = this.getAttribute('data-status');
            document.querySelectorAll('.audit-item-row').forEach(row => {
                if (status === 'all' || row.getAttribute('data-status') === status) {
                    row.style.display = '';
                } else if (status === 'missing' && (row.getAttribute('data-status') === 'missing' || row.getAttribute('data-status') === 'pending')) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    function confirmCompleteSession() {
        Swal.fire({
            title: 'Konfirmasi Selesaikan Audit',
            html: 'Apakah Anda yakin ingin menyelesaikan sesi audit ini?<br><small class="text-muted">Aset yang belum ter-scan akan otomatis dicatat sebagai <b>Belum Ditemukan (Missing)</b>.</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fas fa-check-circle mr-1"></i> Ya, Selesaikan Audit',
            cancelButtonText: 'Batal',
            customClass: { popup: 'swal2-minimal-rounded' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('complete-session-form').submit();
            }
        });
    }
</script>
@endpush
@endsection
