@extends('layouts.admin')

@section('title', __('messages.stock_opname') ?? 'Stock Opname & Audit')

@section('content')
<!-- Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 -->
<!-- Hallmark · genre: modern-minimal · macrostructure: workbench · design-system: design.md · designed-as-app -->
<style>
    .so-index-container {
        padding-bottom: 50px;
    }
    .so-card {
        background: var(--color-paper-1, #1e293b);
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }
    .so-stat-card {
        background: var(--color-paper-1, #1e293b);
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 16px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 180ms ease, box-shadow 180ms ease;
    }
    .so-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
    }
    .so-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .so-input {
        background: rgba(15, 23, 42, 0.35);
        border: 1px solid rgba(148, 163, 184, 0.22);
        border-radius: 10px;
        color: var(--color-ink-0, #f8fafc);
        font-size: 0.84rem;
        padding: 7px 11px;
        height: 40px;
        width: 100%;
    }
    .so-input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
        outline: none;
    }
    .so-btn-action {
        height: 40px;
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
        transform: translateY(-1px);
    }
    .so-btn-secondary {
        background: rgba(148, 163, 184, 0.15);
        color: var(--color-ink-0, #f8fafc);
        border: 1px solid rgba(148, 163, 184, 0.25);
    }
    .so-btn-secondary:hover {
        background: rgba(148, 163, 184, 0.25);
        color: var(--color-ink-0, #f8fafc);
    }

    /* Session Card Grid */
    .session-grid-card {
        background: rgba(15, 23, 42, 0.45);
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 14px;
        padding: 18px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        transition: all 0.2s ease;
    }
    .session-grid-card:hover {
        border-color: rgba(99, 102, 241, 0.4);
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
    }
    .progress-bar-thin {
        height: 6px;
        border-radius: 3px;
        background: rgba(148, 163, 184, 0.2);
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #10b981, #34d399);
        border-radius: 3px;
        transition: width 0.3s ease;
    }

    /* Custom Modal */
    .swal2-minimal-rounded {
        border-radius: 18px !important;
        background: var(--color-paper-1, #1e293b) !important;
        color: var(--color-ink-0, #f8fafc) !important;
        border: 1px solid rgba(148, 163, 184, 0.2) !important;
    }
</style>

<div class="container-fluid so-index-container">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4" style="gap: 16px;">
        <div>
            <h1 class="h3 font-weight-bold mb-1 text-white">
                <i class="fas fa-qrcode text-indigo mr-2"></i> {{ __('messages.stock_opname_title') }}
            </h1>
            <p class="text-muted small mb-0">
                Audit fisik aset IT, verifikasi QR Code/Barcode realtime, dan laporan rekonsiliasi stok.
            </p>
        </div>
        @can('menu_stock_opname')
        <button type="button" class="so-btn-action so-btn-primary" data-toggle="modal" data-target="#createSessionModal">
            <i class="fas fa-plus-circle"></i>
            <span>+ Sesi Audit Baru</span>
        </button>
        @endcan
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

    <!-- Stat Cards -->
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3 mb-md-0">
            <div class="so-stat-card">
                <div class="so-stat-icon" style="background: rgba(99, 102, 241, 0.12); color: #818cf8;">
                    <i class="fas fa-tasks"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase font-weight-bold" style="font-size: 0.7rem;">Total Sesi Audit</div>
                    <div class="h4 font-weight-bold mb-0 text-white">{{ number_format($stats['total_sessions']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3 mb-md-0">
            <div class="so-stat-card">
                <div class="so-stat-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase font-weight-bold" style="font-size: 0.7rem;">Sesi Berjalan</div>
                    <div class="h4 font-weight-bold mb-0 text-white">{{ number_format($stats['in_progress']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="so-stat-card">
                <div class="so-stat-icon" style="background: rgba(16, 185, 129, 0.12); color: #34d399;">
                    <i class="fas fa-check-double"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase font-weight-bold" style="font-size: 0.7rem;">Sesi Selesai</div>
                    <div class="h4 font-weight-bold mb-0 text-white">{{ number_format($stats['completed']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="so-stat-card">
                <div class="so-stat-icon" style="background: rgba(6, 182, 212, 0.12); color: #38bdf8;">
                    <i class="fas fa-barcode"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase font-weight-bold" style="font-size: 0.7rem;">Aset Ter-Scan</div>
                    <div class="h4 font-weight-bold mb-0 text-white">{{ number_format($stats['total_audited_items']) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="so-card p-3 mb-4">
        <form method="GET" action="{{ route('stock-opname.index') }}">
            <div class="row align-items-center" style="row-gap: 12px;">
                <div class="col-12 col-md-4">
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute" style="left: 12px; top: 12px; color: #94a3b8; font-size: 0.85rem;"></i>
                        <input type="text" name="search" class="so-input" style="padding-left: 36px;" placeholder="Cari kode sesi, judul..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <select name="status" class="so-input">
                        <option value="">-- Semua Status --</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Berjalan</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <select name="location_id" class="so-input">
                        <option value="">-- Semua Lokasi --</option>
                        @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2 justify-content-end" style="gap: 8px;">
                    <button type="submit" class="so-btn-action so-btn-primary flex-grow-1">
                        <i class="fas fa-filter"></i> <span>Filter</span>
                    </button>
                    @if(request()->anyFilled(['search', 'status', 'location_id']))
                    <a href="{{ route('stock-opname.index') }}" class="so-btn-action so-btn-secondary" title="Reset Filter">
                        <i class="fas fa-undo"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Sessions Cards Grid -->
    <div class="row" style="row-gap: 18px;">
        @forelse($sessions as $session)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="session-grid-card">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge" style="background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3); font-family: var(--font-mono, monospace); font-size: 0.78rem; padding: 5px 9px;">
                            {{ $session->session_code }}
                        </span>
                        @if($session->status === 'in_progress')
                        <span class="badge badge-pill badge-warning" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); font-size: 0.72rem; padding: 4px 10px;">
                            <i class="fas fa-spinner fa-spin mr-1"></i> Sedang Berjalan
                        </span>
                        @elseif($session->status === 'completed')
                        <span class="badge badge-pill badge-success" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); font-size: 0.72rem; padding: 4px 10px;">
                            <i class="fas fa-check-circle mr-1"></i> Selesai
                        </span>
                        @else
                        <span class="badge badge-pill badge-secondary" style="font-size: 0.72rem; padding: 4px 10px;">
                            {{ strtoupper($session->status) }}
                        </span>
                        @endif
                    </div>

                    <h5 class="font-weight-bold text-white mb-2" style="font-size: 1.05rem;">{{ $session->title }}</h5>

                    <div class="d-flex flex-wrap text-muted small mb-3" style="gap: 12px; font-size: 0.78rem;">
                        <div><i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $session->location ? $session->location->name : 'Semua Lokasi' }}</div>
                        <div><i class="fas fa-tag text-info mr-1"></i> {{ $session->category ? $session->category->name : 'Semua Kategori' }}</div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-3 p-2 rounded" style="background: rgba(15, 23, 42, 0.5);">
                        <div class="d-flex justify-content-between text-muted small mb-1" style="font-size: 0.75rem;">
                            <span>Progres Audit</span>
                            <span class="font-weight-bold text-white">{{ $session->progress_percentage }}%</span>
                        </div>
                        <div class="progress-bar-thin">
                            <div class="progress-bar-fill" style="width: {{ $session->progress_percentage }}%;"></div>
                        </div>
                    </div>

                    <!-- Mini Stat Badges -->
                    <div class="d-flex align-items-center justify-content-between p-2 rounded mb-3" style="background: rgba(15, 23, 42, 0.4); font-size: 0.76rem;">
                        <span title="Terverifikasi"><i class="fas fa-check text-success mr-1"></i> {{ $session->total_matched }} Matched</span>
                        <span title="Berpindah Lokasi"><i class="fas fa-exchange-alt text-warning mr-1"></i> {{ $session->total_displaced }} Displaced</span>
                        <span title="Belum Ditemukan"><i class="fas fa-times text-danger mr-1"></i> {{ $session->total_missing }} Missing</span>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between pt-3 border-top" style="border-color: rgba(148, 163, 184, 0.12) !important;">
                    <div class="text-muted small" style="font-size: 0.72rem;">
                        Dibuat oleh {{ $session->creator->name ?? 'System' }}<br>
                        {{ $session->created_at ? $session->created_at->format('d M Y') : '-' }}
                    </div>
                    <div class="d-flex gap-2" style="gap: 6px;">
                        <a href="{{ route('stock-opname.export', $session->id) }}" class="so-btn-action so-btn-secondary p-2" style="width: 36px; height: 36px;" title="Unduh Laporan CSV/Excel">
                            <i class="fas fa-download"></i>
                        </a>
                        <a href="{{ route('stock-opname.show', $session->id) }}" class="so-btn-action so-btn-primary py-1 px-3" style="font-size: 0.8rem;">
                            <i class="fas fa-camera mr-1"></i> Studio Audit
                        </a>
                        @can('menu_stock_opname')
                        <button type="button" class="so-btn-action p-2" style="width: 36px; height: 36px; background: rgba(239, 68, 68, 0.12); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.25);" onclick="confirmDeleteSession({{ $session->id }}, '{{ addslashes($session->session_code) }}')" title="Hapus Sesi Audit">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        <form id="delete-session-form-{{ $session->id }}" action="{{ route('stock-opname.destroy', $session->id) }}" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 py-5 text-center text-muted">
            <i class="fas fa-qrcode fa-3x mb-3 text-secondary opacity-50"></i>
            <div class="h5 font-weight-bold text-white">Belum Ada Sesi Stock Opname</div>
            <p class="small text-muted mb-3">Mulai sesi audit fisik baru untuk memindai aset IT menggunakan QR Code / Barcode.</p>
            @can('menu_stock_opname')
            <button type="button" class="so-btn-action so-btn-primary" data-toggle="modal" data-target="#createSessionModal">
                <i class="fas fa-plus-circle"></i> Buat Sesi Audit Pertama
            </button>
            @endcan
        </div>
        @endforelse
    </div>

    @if($sessions->hasPages())
    <div class="mt-4">
        {{ $sessions->links() }}
    </div>
    @endif
</div>

<!-- Modal Create Audit Session -->
<div class="modal fade" id="createSessionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="background: var(--color-paper-1, #1e293b); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 18px; color: var(--color-ink-0, #f8fafc);">
            <div class="modal-header border-bottom py-3 px-4" style="border-color: rgba(148, 163, 184, 0.15) !important;">
                <h5 class="modal-title font-weight-bold text-white" style="font-size: 1.05rem;">
                    <i class="fas fa-plus-circle text-primary mr-2"></i> Buat Sesi Stock Opname Baru
                </h5>
                <button type="button" class="close text-white opacity-70" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('stock-opname.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold text-white small">Judul Sesi Audit <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="so-input" placeholder="Contoh: Audit Stok Q3 2026 - Data Center A" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold text-white small">Lokasi Target (Opsional)</label>
                        <select name="location_id" class="so-input">
                            <option value="">-- Semua Lokasi (Seluruh Perusahaan) --</option>
                            @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">Pilih lokasi spesifik atau biarkan kosong untuk audit menyeluruh.</small>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold text-white small">Kategori Target (Opsional)</label>
                        <select name="category_id" class="so-input">
                            <option value="">-- Semua Kategori Perangkat --</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label font-weight-bold text-white small">Catatan Sesi</label>
                        <textarea name="notes" class="so-input" style="height: 80px;" placeholder="Instruksi tambahan untuk tim auditor IT..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4 d-flex justify-content-end gap-2" style="border-color: rgba(148, 163, 184, 0.15) !important;">
                    <button type="button" class="so-btn-action so-btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="so-btn-action so-btn-primary">
                        <i class="fas fa-play"></i> Mulai Sesi Audit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDeleteSession(id, code) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Konfirmasi Hapus Sesi Audit',
                html: `Apakah Anda yakin ingin menghapus sesi audit <b>"${code}"</b>?<br><small class="text-muted">Data hasil scan dalam sesi ini akan terhapus secara permanen.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Ya, Hapus Sesi',
                cancelButtonText: 'Batal',
                customClass: { popup: 'swal2-minimal-rounded' }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-session-form-${id}`).submit();
                }
            });
        } else {
            if (confirm(`Apakah Anda yakin ingin menghapus sesi audit "${code}"?`)) {
                document.getElementById(`delete-session-form-${id}`).submit();
            }
        }
    }
</script>
@endpush
@endsection
