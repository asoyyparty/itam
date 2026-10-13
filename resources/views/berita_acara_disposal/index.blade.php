@extends('layouts.admin')

@section('title', 'Berita Acara Disposal Asset')

@section('content')
<!-- Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 -->
<!-- Hallmark · genre: modern-minimal · macrostructure: workbench · design-system: design.md · designed-as-app -->
<style>
    .ba-index-container {
        padding-bottom: 50px;
    }
    .ba-card {
        background: var(--color-paper-1, #1e293b);
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }
    .ba-stat-card {
        background: var(--color-paper-1, #1e293b);
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 16px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 180ms ease, box-shadow 180ms ease;
    }
    .ba-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
    }
    .ba-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .ba-input {
        background: rgba(15, 23, 42, 0.35);
        border: 1px solid rgba(148, 163, 184, 0.22);
        border-radius: 10px;
        color: var(--color-ink-0, #f8fafc);
        font-size: 0.84rem;
        padding: 7px 11px;
        height: 40px;
        width: 100%;
    }
    .ba-input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
        outline: none;
    }
    .ba-btn-action {
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
    .ba-btn-secondary {
        background: rgba(148, 163, 184, 0.15);
        color: var(--color-ink-0, #f8fafc);
        border: 1px solid rgba(148, 163, 184, 0.25);
    }
    .ba-btn-secondary:hover {
        background: rgba(148, 163, 184, 0.25);
        color: var(--color-ink-0, #f8fafc);
    }

    /* Table Action Button Styling */
    .ba-action-btn {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.82rem;
        transition: all 180ms ease;
        border: 1px solid transparent;
        text-decoration: none !important;
        margin: 0;
    }
    .ba-action-btn-view {
        background: rgba(14, 165, 233, 0.15);
        color: #38bdf8;
        border-color: rgba(14, 165, 233, 0.3);
    }
    .ba-action-btn-view:hover {
        background: rgba(14, 165, 233, 0.3);
        color: #ffffff;
        transform: translateY(-1px);
    }
    .ba-action-btn-edit {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
        border-color: rgba(245, 158, 11, 0.3);
    }
    .ba-action-btn-edit:hover {
        background: rgba(245, 158, 11, 0.3);
        color: #ffffff;
        transform: translateY(-1px);
    }
    .ba-action-btn-print {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border-color: rgba(16, 185, 129, 0.3);
    }
    .ba-action-btn-print:hover {
        background: rgba(16, 185, 129, 0.3);
        color: #ffffff;
        transform: translateY(-1px);
    }
    .ba-action-btn-delete {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border-color: rgba(239, 68, 68, 0.3);
    }
    .ba-action-btn-delete:hover {
        background: rgba(239, 68, 68, 0.3);
        color: #ffffff;
        transform: translateY(-1px);
    }

    /* Mobile Card View */
    .ba-mobile-card {
        background: var(--color-paper-1, #1e293b);
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 14px;
    }

    /* Light mode adaptations */
    body.light-mode .ba-card, body.light-mode .ba-stat-card, body.light-mode .ba-mobile-card {
        background: #ffffff;
        border-color: #e2e8f0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }
    body.light-mode .ba-input {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    /* Unified Date Range Pill Component (Exact Match for media_1789529411117) */
    .ba-daterange-pill {
        display: flex;
        align-items: center;
        background: rgba(15, 23, 42, 0.45);
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 10px;
        height: 40px;
        padding: 0 12px;
        gap: 8px;
        width: 100%;
        transition: border-color 160ms ease, box-shadow 160ms ease;
    }
    .ba-daterange-pill:focus-within {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
        background: rgba(15, 23, 42, 0.7);
    }
    body.light-mode .ba-daterange-pill {
        background: #ffffff;
        border-color: #cbd5e1;
    }
    body.light-mode .ba-daterange-pill:focus-within {
        border-color: #6366f1;
    }
    .ba-range-input {
        border: none !important;
        background: transparent !important;
        color: var(--color-ink-0, #f8fafc) !important;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.81rem;
        padding: 0 !important;
        height: 100% !important;
        flex: 1;
        min-width: 0;
        outline: none !important;
        box-shadow: none !important;
    }
    body.light-mode .ba-range-input {
        color: #0f172a !important;
    }
    .ba-range-input[type="date"] {
        color-scheme: dark;
    }
    body.light-mode .ba-range-input[type="date"] {
        color-scheme: light;
    }
    .ba-range-input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(0.85);
        cursor: pointer;
        opacity: 0.85;
    }
    body.light-mode .ba-range-input[type="date"]::-webkit-calendar-picker-indicator {
        filter: none;
    }
</style>

<div class="container-fluid ba-index-container">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h1 style="font-family: 'Outfit', sans-serif; font-size: 1.5rem; font-weight: 700; color: var(--color-ink-0, #f8fafc); margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-dumpster text-danger"></i> Berita Acara Disposal Asset
            </h1>
            <p style="font-size: 0.85rem; color: var(--color-ink-2, #94a3b8); margin: 4px 0 0 0;">
                Kelola formulir pengajuan disposal aset perusahaan (CBA/MTN/F-023 Rev. 00)
            </p>
        </div>
        <div>
            <a href="{{ route('berita-acara-disposal.create') }}" class="ba-btn-action ba-btn-primary">
                <i class="fas fa-plus"></i> Buat BA Disposal Baru
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px;">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="ba-stat-card">
                <div class="ba-stat-icon" style="background: rgba(99, 102, 241, 0.15); color: #818cf8;">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <div style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-ink-2, #94a3b8); font-weight: 600;">Total BA Disposal</div>
                    <div style="font-family: 'Outfit', sans-serif; font-size: 1.4rem; font-weight: 700; color: var(--color-ink-0, #f8fafc);">{{ $disposals->total() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ba-stat-card">
                <div class="ba-stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #34d399;">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <div style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-ink-2, #94a3b8); font-weight: 600;">Disposal Bulan Ini</div>
                    <div style="font-family: 'Outfit', sans-serif; font-size: 1.4rem; font-weight: 700; color: var(--color-ink-0, #f8fafc);">
                        {{ \App\Models\BeritaAcaraDisposal::whereMonth('tanggal_ba', date('m'))->whereYear('tanggal_ba', date('Y'))->count() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ba-stat-card">
                <div class="ba-stat-icon" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24;">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <div style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-ink-2, #94a3b8); font-weight: 600;">Divisi Pemohon</div>
                    <div style="font-family: 'Outfit', sans-serif; font-size: 1.1rem; font-weight: 700; color: var(--color-ink-0, #f8fafc);">
                        IT PABRIK / MAINT.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar (Presisi, Compact & Balanced Grid) -->
    <div class="ba-card mb-4">
        <div class="p-3">
            <form action="{{ route('berita-acara-disposal.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-12 col-md-5 col-lg-5">
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem;"></i>
                        <input type="text" name="search" class="ba-input" style="padding-left: 38px !important;" placeholder="Cari Nomor BA, Pembuat, atau Nama Aset..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-4 col-lg-4">
                    <div class="ba-daterange-pill">
                        <i class="far fa-calendar-alt" style="color: #818cf8; font-size: 0.9rem; flex-shrink: 0;"></i>
                        <input type="date" name="start_date" id="start_date" class="ba-range-input" value="{{ request('start_date') }}" title="Start Date" placeholder="Start Date">
                        <i class="fas fa-arrow-right" style="color: #818cf8; font-size: 0.75rem; flex-shrink: 0; opacity: 0.8;"></i>
                        <input type="date" name="end_date" id="end_date" class="ba-range-input" value="{{ request('end_date') }}" title="End Date" placeholder="End Date">
                    </div>
                </div>
                <div class="col-12 col-md-3 col-lg-3">
                    <div class="d-flex align-items-center justify-content-md-end gap-2">
                        <button type="submit" class="ba-btn-action ba-btn-primary flex-fill flex-md-grow-0 px-4">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        @if(request()->hasAny(['search', 'start_date', 'end_date']))
                        <a href="{{ route('berita-acara-disposal.index') }}" class="ba-btn-action ba-btn-secondary" title="Reset Filter">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Desktop Table View (Hidden on mobile) -->
    <div class="ba-card d-none d-md-block">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.84rem;">
                <thead>
                    <tr style="background: rgba(148, 163, 184, 0.08); border-bottom: 1px solid rgba(148,163,184,0.15);">
                        <th class="py-3 px-3" style="width: 22%;">NOMOR BA DISPOSAL</th>
                        <th class="py-3" style="width: 14%;">TANGGAL BA</th>
                        <th class="py-3" style="width: 14%;">DIVISI</th>
                        <th class="py-3" style="width: 22%;">ASET DISPOSAL</th>
                        <th class="py-3" style="width: 14%;">PEMBUAT</th>
                        <th class="py-3 text-center" style="width: 14%;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($disposals as $ba)
                    @php
                        $items = is_array($ba->items) ? $ba->items : json_decode($ba->items ?? '[]', true);
                        $itemCount = is_array($items) ? count($items) : 0;
                        $firstItem = is_array($items) && count($items) > 0 ? ($items[0]['nama_asset'] ?? ($items[0]['nama_aset'] ?? '-')) : '-';
                    @endphp
                    <tr style="border-bottom: 1px solid rgba(148, 163, 184, 0.08);">
                        <td class="px-3">
                            <a href="{{ route('berita-acara-disposal.show', $ba) }}" style="font-family: 'JetBrains Mono', monospace; font-weight: 600; color: #818cf8; text-decoration: none;">
                                {{ $ba->nomor_ba }}
                            </a>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($ba->tanggal_ba)->translatedFormat('d M Y') }}</td>
                        <td><span class="badge bg-indigo" style="font-size: 0.72rem; padding: 4px 8px;">{{ $ba->divisi ?: 'IT PABRIK' }}</span></td>
                        <td>
                            <div style="font-weight: 600;">{{ $firstItem }}</div>
                            @if($itemCount > 1)
                            <small class="text-muted" style="font-size: 0.73rem;">+ {{ $itemCount - 1 }} item aset lainnya</small>
                            @endif
                        </td>
                        <td>{{ $ba->nama_pembuat }}</td>
                        <td class="text-center">
                            <!-- Action Buttons Container -->
                            <div class="d-inline-flex align-items-center justify-content-center" style="gap: 8px;">
                                <a href="{{ route('berita-acara-disposal.show', $ba) }}" class="ba-action-btn ba-action-btn-view" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('berita-acara-disposal.edit', $ba) }}" class="ba-action-btn ba-action-btn-edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('berita-acara-disposal.print', $ba) }}" target="_blank" class="ba-action-btn ba-action-btn-print" title="Cetak PDF">
                                    <i class="fas fa-print"></i>
                                </a>
                                <form action="{{ route('berita-acara-disposal.destroy', $ba) }}" method="POST" class="d-inline delete-form" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ba-action-btn ba-action-btn-delete" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-folder-open fa-2x mb-2" style="opacity: 0.5;"></i>
                            <div>Belum ada dokumen Berita Acara Disposal Asset.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top border-secondary border-opacity-10 d-flex justify-content-end">
            {{ $disposals->withQueryString()->links() }}
        </div>
    </div>

    <!-- Mobile Card Deck View (Visible only on mobile screens) -->
    <div class="d-block d-md-none">
        @forelse($disposals as $ba)
        @php
            $items = is_array($ba->items) ? $ba->items : json_decode($ba->items ?? '[]', true);
            $itemCount = is_array($items) ? count($items) : 0;
            $firstItem = is_array($items) && count($items) > 0 ? ($items[0]['nama_asset'] ?? ($items[0]['nama_aset'] ?? '-')) : '-';
        @endphp
        <div class="ba-mobile-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <a href="{{ route('berita-acara-disposal.show', $ba) }}" style="font-family: 'JetBrains Mono', monospace; font-weight: 700; font-size: 0.9rem; color: #818cf8; text-decoration: none;">
                    {{ $ba->nomor_ba }}
                </a>
                <span class="badge bg-indigo" style="font-size: 0.7rem;">{{ $ba->divisi ?: 'IT PABRIK' }}</span>
            </div>
            <div style="font-size: 0.8rem; color: var(--color-ink-2, #94a3b8);" class="mb-2">
                <i class="far fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($ba->tanggal_ba)->translatedFormat('d M Y') }}
                <span class="mx-1">&bull;</span>
                <i class="far fa-user mr-1"></i> {{ $ba->nama_pembuat }}
            </div>
            <div class="p-2 mb-3 rounded" style="background: rgba(148, 163, 184, 0.08); font-size: 0.82rem;">
                <div class="font-weight-bold text-truncate">{{ $firstItem }}</div>
                @if($itemCount > 1)
                <small class="text-muted">+ {{ $itemCount - 1 }} item aset lainnya</small>
                @endif
            </div>
            <div class="d-flex align-items-center justify-content-between pt-2 border-top border-secondary border-opacity-10">
                <div class="d-flex gap-2">
                    <a href="{{ route('berita-acara-disposal.show', $ba) }}" class="ba-action-btn ba-action-btn-view">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('berita-acara-disposal.edit', $ba) }}" class="ba-action-btn ba-action-btn-edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="{{ route('berita-acara-disposal.print', $ba) }}" target="_blank" class="ba-action-btn ba-action-btn-print">
                        <i class="fas fa-print"></i>
                    </a>
                    <form action="{{ route('berita-acara-disposal.destroy', $ba) }}" method="POST" class="d-inline delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="ba-action-btn ba-action-btn-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="ba-card p-4 text-center text-muted">
            <i class="fas fa-folder-open fa-2x mb-2" style="opacity: 0.5;"></i>
            <div>Belum ada dokumen Berita Acara Disposal Asset.</div>
        </div>
        @endforelse

        <div class="mt-3 d-flex justify-content-center">
            {{ $disposals->withQueryString()->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formEl = this;
                const btn = formEl.querySelector('.ba-action-btn-delete');
                const noBa = btn ? (btn.getAttribute('data-no-ba') || 'dokumen ini') : 'dokumen ini';

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Hapus BA Disposal?',
                        html: `<div style="font-size: 0.88rem; color: #94a3b8; margin-top: 6px;">
                                 Apakah Anda yakin ingin menghapus Berita Acara Disposal <strong style="color: #818cf8; font-family: 'JetBrains Mono', monospace;">${noBa}</strong>?
                                 <div style="background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; font-size: 0.78rem; line-height: 1.35; margin-top: 12px; padding: 10px; border-radius: 8px; text-align: left;">
                                   <i class="fas fa-exclamation-triangle mr-1 text-danger"></i> <span>Data yang dihapus tidak dapat dikembalikan lagi.</span>
                                 </div>
                               </div>`,
                        icon: 'warning',
                        iconColor: '#ef4444',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#475569',
                        confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Ya, Hapus',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        customClass: {
                            popup: 'swal2-minimal-rounded'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            formEl.submit();
                        }
                    });
                } else {
                    if (confirm(`Apakah Anda yakin ingin menghapus dokumen Berita Acara Disposal ${noBa}?`)) {
                        formEl.submit();
                    }
                }
            });
        });
    });
</script>
@endpush
@endsection
