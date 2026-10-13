@extends('layouts.admin')

@section('title', __('messages.berita_acara_ok'))

@section('content')
<!-- Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 -->
<!-- Hallmark · genre: modern-minimal · macrostructure: workbench · design-system: design.md · designed-as-app -->
<style>
    .ba-index-workbench {
        overflow-x: clip;
        width: 100%;
        padding-bottom: 90px;
    }
    .ba-banner {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.85), rgba(30, 41, 59, 0.75));
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 20px;
        padding: 16px 20px;
        margin-bottom: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }
    body.light-mode .ba-banner {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(241, 245, 249, 0.95));
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
    }
    .ba-stat-card {
        background: var(--color-paper-1, #1e293b);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 14px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: transform 180ms ease;
    }
    .ba-stat-card:hover {
        transform: translateY(-2px);
    }
    .ba-stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .ba-stat-num {
        font-family: 'Outfit', sans-serif;
        font-size: 1.25rem;
        font-weight: 700;
        line-height: 1.1;
        color: var(--color-ink-0, #f8fafc);
    }
    .ba-stat-label {
        font-size: 0.75rem;
        color: var(--color-ink-2, #94a3b8);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .ba-table-card {
        background: var(--color-paper-1, #1e293b);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }
    body.light-mode .ba-table-card {
        background: #ffffff;
        border-color: #e2e8f0;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.06);
    }
    /* Card Top Header with Primary Create Action */
    .ba-card-top-header {
        padding: 16px 20px;
        background: rgba(15, 23, 42, 0.35);
        border-bottom: 1px solid rgba(148, 163, 184, 0.12);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    body.light-mode .ba-card-top-header {
        background: #f8fafc;
        border-bottom-color: #e2e8f0;
    }
    .ba-card-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: rgba(99, 102, 241, 0.15);
        border: 1px solid rgba(99, 102, 241, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #818cf8;
        font-size: 0.95rem;
        flex-shrink: 0;
        margin-right: 10px;
    }
    .ba-card-heading {
        font-family: 'Outfit', sans-serif;
        font-size: 0.92rem;
        font-weight: 700;
        color: var(--color-ink-0, #f8fafc);
        line-height: 1.2;
    }
    .ba-count-badge {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.68rem;
        padding: 2px 7px;
        border-radius: 6px;
        background: rgba(99, 102, 241, 0.15);
        border: 1px solid rgba(99, 102, 241, 0.3);
        color: #818cf8;
        font-weight: 600;
    }
    .ba-btn-create-main {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 10px;
        padding: 9px 18px;
        font-size: 0.88rem;
        font-weight: 600;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 180ms cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none !important;
    }
    .ba-btn-create-main:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
        color: #ffffff !important;
        filter: brightness(1.08);
    }
    .ba-filter-bar {
        padding: 16px 20px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.12);
        background: rgba(15, 23, 42, 0.2);
    }
    body.light-mode .ba-filter-bar {
        background: #f8fafc;
        border-bottom-color: #e2e8f0;
    }
    .ba-filter-label {
        font-size: 0.73rem;
        font-weight: 600;
        color: var(--color-ink-2, #94a3b8);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 6px;
        display: block;
    }
    .ba-filter-input {
        background: rgba(15, 23, 42, 0.45);
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 10px;
        color: var(--color-ink-0, #f8fafc);
        height: 40px;
        font-size: 0.86rem;
        padding: 6px 12px;
        transition: border-color 160ms ease, box-shadow 160ms ease;
    }
    .ba-filter-input:focus {
        background: rgba(15, 23, 42, 0.7);
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
        color: var(--color-ink-0, #f8fafc);
        outline: none;
    }
    body.light-mode .ba-filter-input {
        background: #ffffff;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    body.light-mode .ba-filter-input:focus {
        background: #ffffff;
        border-color: #6366f1;
    }
    /* Robust Search & Date Wrappers (No empty voids, no cut-off edges) */
    .ba-search-wrapper, .ba-date-wrapper {
        position: relative;
        width: 100%;
    }
    .ba-search-icon, .ba-date-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #818cf8;
        font-size: 0.82rem;
        pointer-events: none;
        z-index: 2;
    }
    body.light-mode .ba-search-icon, body.light-mode .ba-date-icon {
        color: #4f46e5;
    }
    .ba-search-input {
        padding-left: 32px !important;
        border-radius: 10px !important;
        width: 100%;
    }
    .ba-date-input {
        padding-left: 30px !important;
        border-radius: 10px !important;
        width: 100%;
    }
    /* Date Input Visible Placeholder & Picker Styling */
    .ba-filter-input[type="date"] {
        color-scheme: dark;
        color: var(--color-ink-0, #f8fafc) !important;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.83rem;
    }
    body.light-mode .ba-filter-input[type="date"] {
        color-scheme: light;
        color: #0f172a !important;
    }
    .ba-filter-input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(0.85);
        cursor: pointer;
        opacity: 0.85;
    }
    body.light-mode .ba-filter-input[type="date"]::-webkit-calendar-picker-indicator {
        filter: none;
        opacity: 0.85;
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
    .ba-table {
        border-collapse: separate;
        border-spacing: 0;
    }
    .ba-table th {
        background: rgba(15, 23, 42, 0.5);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: var(--color-ink-2, #94a3b8);
        padding: 14px 16px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.14);
    }
    body.light-mode .ba-table th {
        background: #f1f5f9;
        color: #475569;
        border-bottom-color: #e2e8f0;
    }
    .ba-table td {
        padding: 13px 16px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(148, 163, 184, 0.08);
        font-size: 0.88rem;
    }
    body.light-mode .ba-table td {
        border-bottom-color: #f1f5f9;
    }
    .ba-table tbody tr {
        transition: background-color 150ms ease;
    }
    .ba-table tbody tr:hover td {
        background: rgba(99, 102, 241, 0.05);
    }
    body.light-mode .ba-table tbody tr:hover td {
        background: #f8fafc;
    }
    .ba-badge-status {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }
    .ba-btn-action {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        border: 1px solid rgba(148, 163, 184, 0.2);
        background: rgba(15, 23, 42, 0.3);
        color: var(--color-ink-1, #cbd5e1);
        text-decoration: none !important;
        transition: all 180ms ease;
    }
    .ba-btn-action:hover {
        transform: translateY(-1px);
        color: #ffffff;
    }
    .ba-btn-print:hover {
        background: #6366f1;
        border-color: #6366f1;
    }
    .ba-btn-view:hover {
        background: #0284c7;
        border-color: #0284c7;
    }
    .ba-btn-edit:hover {
        background: #eab308;
        border-color: #eab308;
    }
    .ba-btn-delete:hover {
        background: #ef4444;
        border-color: #ef4444;
    }
    /* Count Badge and Button Optimizations */
    .ba-count-badge {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.7rem;
        padding: 2px 8px;
        border-radius: 6px;
        background: rgba(99, 102, 241, 0.15);
        border: 1px solid rgba(99, 102, 241, 0.3);
        color: #818cf8;
        font-weight: 600;
    }

    /* ═══════════════════════════════════════════════════════════════
       Hallmark Mobile Card Stack (View for screen < 768px)
       ══════════════════════════════════════════════════════════════ */
    .ba-mobile-list {
        padding: 12px;
    }
    .ba-mobile-card {
        background: rgba(15, 23, 42, 0.45);
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-left: 3.5px solid #6366f1;
        border-radius: 14px;
        padding: 15px 16px;
        margin-bottom: 14px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.14);
        transition: transform 180ms ease, border-color 180ms ease;
    }
    body.light-mode .ba-mobile-card {
        background: #ffffff;
        border-color: #e2e8f0;
        border-left-color: #4f46e5;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
    }
    .ba-mobile-card:hover {
        border-color: rgba(99, 102, 241, 0.4);
    }
    @media (max-width: 767.98px) {
        .ba-card-top-header {
            padding: 10px 12px;
        }
        .ba-card-heading {
            font-size: 0.80rem;
        }
        .ba-count-badge {
            font-size: 0.65rem;
            padding: 2px 6px;
        }
        .ba-filter-bar {
            padding: 12px 14px;
        }
        .ba-filter-label {
            font-size: 0.68rem;
            margin-bottom: 4px;
        }
        .ba-filter-input {
            font-size: 0.8rem;
            height: 36px;
            padding: 5px 10px;
        }
        .ba-search-icon {
            font-size: 0.8rem;
            left: 10px;
        }
        .ba-search-input {
            padding-left: 32px !important;
        }
        .ba-stat-card {
            padding: 9px 11px;
            gap: 8px;
        }
        .ba-stat-icon {
            width: 32px;
            height: 32px;
            font-size: 0.95rem;
            border-radius: 8px;
        }
        .ba-stat-num {
            font-size: 1.05rem;
        }
        .ba-stat-label {
            font-size: 0.65rem;
        }
        .ba-mobile-title {
            font-size: 0.86rem;
        }
        .ba-mobile-tag {
            font-size: 0.68rem;
            padding: 2px 6px;
        }
        .ba-mobile-field-lbl {
            font-size: 0.65rem;
        }
        .ba-mobile-field-val {
            font-size: 0.78rem;
        }
        .ba-mobile-btn-print {
            font-size: 0.76rem;
            padding: 6px 10px;
        }
        .ba-mobile-btn-icon {
            width: 32px;
            height: 32px;
            font-size: 0.8rem;
        }
    }
    .ba-mobile-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 8px;
        margin-bottom: 8px;
    }
    .ba-mobile-title {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.88rem;
        font-weight: 700;
        color: #818cf8;
        text-decoration: none !important;
        line-height: 1.3;
    }
    body.light-mode .ba-mobile-title {
        color: #4f46e5;
    }
    .ba-mobile-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 10px;
    }
    .ba-mobile-tag {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.68rem;
        padding: 2px 6px;
        border-radius: 6px;
        background: rgba(148, 163, 184, 0.12);
        color: var(--color-ink-1, #cbd5e1);
    }
    body.light-mode .ba-mobile-tag {
        background: #f1f5f9;
        color: #475569;
    }
    .ba-mobile-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-bottom: 12px;
        padding: 8px 10px;
        border-radius: 10px;
        background: rgba(15, 23, 42, 0.25);
    }
    body.light-mode .ba-mobile-grid {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }
    .ba-mobile-field-lbl {
        font-size: 0.65rem;
        color: var(--color-ink-2, #94a3b8);
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 2px;
    }
    .ba-mobile-field-val {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--color-ink-0, #f8fafc);
        word-break: break-word;
    }
    .ba-mobile-actions {
        display: flex;
        align-items: center;
        gap: 6px;
        padding-top: 10px;
        border-top: 1px solid rgba(148, 163, 184, 0.1);
    }
    .ba-mobile-btn-print {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 0.82rem;
        font-weight: 600;
        padding: 8px 12px;
        border-radius: 8px;
        background: #6366f1;
        color: #ffffff;
        text-decoration: none !important;
        border: none;
        transition: background-color 180ms ease;
    }
    .ba-mobile-btn-print:hover {
        background: #4f46e5;
        color: #ffffff;
    }
    .ba-mobile-btn-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.88rem;
        border: 1px solid rgba(148, 163, 184, 0.2);
        background: rgba(15, 23, 42, 0.3);
        color: var(--color-ink-1, #cbd5e1);
        text-decoration: none !important;
    }
    body.light-mode .ba-mobile-btn-icon {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #334155;
    }
</style>

<div class="content-header p-0">
    <div class="container-fluid">
        <!-- Master Header Banner -->
        <div class="ba-banner mt-3">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; color: #818cf8; flex-shrink: 0;">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <div>
                        <h1 class="m-0 font-weight-bold" style="font-family: 'Outfit', sans-serif; font-size: 1.25rem; color: var(--color-ink-0);">
                            {{ __('messages.berita_acara_ok') }}
                        </h1>
                        <p class="text-muted mb-0" style="font-size: 0.8rem;">
                            Generator & Verifikasi Surat Jalan Berita Acara OK dengan AI Vision OCR.
                        </p>
                    </div>
                </div>
                <div class="d-none d-md-block">
                    <a href="{{ route('berita-acara.create') }}" class="btn btn-primary px-3 py-2" style="border-radius: 10px; font-weight: 600; font-size: 0.88rem; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                        <i class="fas fa-plus"></i>
                        <span>{{ __('messages.create_berita_acara') }}</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="row mb-3">
            <div class="col-6 col-md-4 mb-2">
                <div class="ba-stat-card">
                    <div class="ba-stat-icon" style="background: rgba(99, 102, 241, 0.15); color: #818cf8;">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div>
                        <div class="ba-stat-num">{{ $beritaAcaras->total() }}</div>
                        <div class="ba-stat-label">{{ __('messages.total_ba_ok') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 mb-2">
                <div class="ba-stat-card">
                    <div class="ba-stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #34d399;">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div>
                        <div class="ba-stat-num">{{ $beritaAcaras->where('status_verifikasi', '!=', '')->count() }}</div>
                        <div class="ba-stat-label">{{ __('messages.verified_ok') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-2">
                <div class="ba-stat-card">
                    <div class="ba-stat-icon" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24;">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div>
                        <div class="ba-stat-num">{{ $beritaAcaras->sum(fn($b) => is_array($b->items) ? count($b->items) : 0) }}</div>
                        <div class="ba-stat-label">{{ __('messages.processed_items') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content pb-4">
    <div class="container-fluid">
        <!-- Flash Message -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; font-size: 0.88rem;">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="ba-table-card">
            <!-- Card Header with Primary "Tambah Dokumen" Button -->
            <div class="ba-card-top-header">
                <div class="d-flex align-items-start" style="gap: 12px;">
                    <div class="ba-card-icon mt-1" style="flex-shrink: 0; margin-right: 0;">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                            <h2 class="ba-card-heading mb-0" style="margin-right: 0;">{{ __('messages.document_list_title') }}</h2>
                            <span class="ba-count-badge">
                                {{ __('messages.document_count', ['count' => $beritaAcaras->total()]) }}
                            </span>
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 0.76rem; line-height: 1.35;">
                            {{ __('messages.document_list_subtitle') }}
                        </small>
                    </div>
                </div>

                <div class="w-100 w-sm-auto mt-2 mt-sm-0">
                    <a href="{{ route('berita-acara.create') }}" class="ba-btn-create-main w-100 w-sm-auto justify-content-center" id="btnCreateBeritaAcara">
                        <i class="fas fa-plus"></i>
                        <span>{{ __('messages.create_berita_acara') }}</span>
                    </a>
                </div>
            </div>

            <!-- Filter Bar (Mobile & Desktop Responsive) -->
            <div class="ba-filter-bar">
                <form action="{{ route('berita-acara.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-12 col-md-5 col-lg-5 mb-2 mb-md-0">
                            <label class="ba-filter-label">{{ __('messages.search_document') }}</label>
                            <div class="ba-search-wrapper">
                                <i class="fas fa-search ba-search-icon"></i>
                                <input type="text" name="search" class="form-control ba-filter-input ba-search-input" placeholder="{{ __('messages.search_document_placeholder') }}" value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-4 col-lg-5 mb-2 mb-md-0">
                            <label class="ba-filter-label">DATE RANGE</label>
                            <div class="ba-daterange-pill">
                                <i class="far fa-calendar-alt" style="color: #818cf8; font-size: 0.9rem; flex-shrink: 0;"></i>
                                <input type="date" name="start_date" id="start_date" class="ba-range-input" value="{{ request('start_date') }}" title="Start Date" placeholder="Start Date">
                                <i class="fas fa-arrow-right" style="color: #818cf8; font-size: 0.75rem; flex-shrink: 0; opacity: 0.8;"></i>
                                <input type="date" name="end_date" id="end_date" class="ba-range-input" value="{{ request('end_date') }}" title="End Date" placeholder="End Date">
                            </div>
                        </div>
                        <div class="col-12 col-md-3 col-lg-2 text-md-right mt-2 mt-md-0">
                            <label class="ba-filter-label d-none d-md-block">&nbsp;</label>
                            <div class="d-flex gap-2 justify-content-md-end">
                                <button type="submit" class="btn btn-primary flex-fill flex-md-grow-0" style="border-radius: 10px; height: 40px; padding: 0 16px; font-weight: 600; font-size: 0.84rem; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                                    <i class="fas fa-filter"></i> <span>{{ __('messages.filter') }}</span>
                                </button>
                                @if(request()->anyFilled(['search', 'start_date', 'end_date']))
                                    <a href="{{ route('berita-acara.index') }}" class="btn btn-outline-secondary flex-fill flex-md-grow-0" style="border-radius: 10px; height: 40px; padding: 0 14px; font-size: 0.84rem; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                                        <i class="fas fa-times"></i> <span>{{ __('messages.reset') }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- DESKTOP VIEW: Table (Visible on md and up) -->
            <div class="table-responsive d-none d-md-block">
                <table class="ba-table w-100 mb-0">
                    <thead>
                        <tr>
                            <th style="width: 44px; text-align: center;">No</th>
                            <th style="min-width: 180px;">No. Berita Acara &amp; Ref SJ</th>
                            <th style="min-width: 100px; white-space: nowrap;">No. PO</th>
                            <th style="min-width: 120px; white-space: nowrap;">Tanggal Terima</th>
                            <th style="min-width: 180px; white-space: nowrap;">Kepada Yth / Penerima</th>
                            <th style="min-width: 140px; text-align: center; white-space: nowrap;">Jumlah Item</th>
                            <th style="min-width: 120px; white-space: nowrap;">Status</th>
                            <th style="width: 140px; text-align: center; white-space: nowrap;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($beritaAcaras as $index => $item)
                            <tr>
                                <td style="text-align: center; color: var(--color-ink-2); font-family: 'JetBrains Mono', monospace; font-size: 0.82rem;">
                                    {{ $beritaAcaras->firstItem() + $index }}
                                </td>
                                <td style="white-space: nowrap;">
                                    <a href="{{ route('berita-acara.show', $item) }}" class="font-weight-bold d-block text-truncate" style="font-family: 'JetBrains Mono', monospace; font-size: 0.92rem; color: #818cf8;" title="{{ $item->nomor_ba ?: $item->nomor_surat }}">
                                        {{ $item->nomor_ba ?: $item->nomor_surat }}
                                    </a>
                                    <span class="badge badge-dark mt-1" style="background: rgba(148, 163, 184, 0.12); color: #94a3b8; font-family: 'JetBrains Mono', monospace; font-size: 0.72rem; border: 1px solid rgba(148, 163, 184, 0.18);">
                                        <i class="fas fa-truck mr-1 text-muted"></i>SJ: {{ $item->nomor_surat }}
                                    </span>
                                </td>
                                <td style="white-space: nowrap;">
                                    @if($item->nomor_po)
                                        <span style="font-family: 'JetBrains Mono', monospace; font-weight: 600; color: var(--color-ink-1); font-size: 0.85rem;">
                                            {{ $item->nomor_po }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td style="white-space: nowrap;">
                                    <span style="color: var(--color-ink-1); font-size: 0.86rem; font-weight: 500;">
                                        <i class="far fa-calendar-alt text-muted mr-1"></i>
                                        {{ $item->tanggal_verifikasi ? \Carbon\Carbon::parse($item->tanggal_verifikasi)->translatedFormat('d M Y') : ($item->tanggal_surat ? \Carbon\Carbon::parse($item->tanggal_surat)->translatedFormat('d M Y') : '-') }}
                                    </span>
                                </td>
                                <td style="white-space: nowrap;">
                                    <div class="font-weight-600" style="color: var(--color-ink-0); font-size: 0.88rem;">
                                        {{ $item->kepada_yth ?: 'Ka. Pembelian' }}
                                    </div>
                                    @if($item->nama_penerima)
                                        <small class="text-muted d-block" style="font-size: 0.76rem;">
                                            <i class="far fa-user mr-1"></i>{{ $item->nama_penerima }}
                                        </small>
                                    @endif
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <span class="badge px-2 py-1" style="background: rgba(99, 102, 241, 0.12); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.25); border-radius: 6px; font-family: 'JetBrains Mono', monospace; font-size: 0.78rem;">
                                        {{ is_array($item->items) ? count($item->items) : 0 }} Item <span class="text-muted">({{ $item->total_quantity }} Unit)</span>
                                    </span>
                                </td>
                                <td style="white-space: nowrap;">
                                    <span class="ba-badge-status">
                                        <i class="fas fa-check-circle"></i>
                                        <span>{{ $item->status_verifikasi ?: 'TTB / LGT ok' }}</span>
                                    </span>
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <a href="{{ route('berita-acara.print', $item) }}" target="_blank" class="ba-btn-action ba-btn-print" title="Cetak Format A4 / Simpan PDF">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="{{ route('berita-acara.show', $item) }}" class="ba-btn-action ba-btn-view" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('berita-acara.edit', $item) }}" class="ba-btn-action ba-btn-edit" title="Ubah Data">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('berita-acara.destroy', $item) }}" method="POST" class="d-inline m-0 form-delete-ba">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="ba-btn-action ba-btn-delete btn-delete-ba" data-no-ba="{{ $item->nomor_ba ?: $item->nomor_surat }}" title="Hapus Dokumen">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div style="width: 50px; height: 50px; border-radius: 14px; background: rgba(99, 102, 241, 0.1); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #818cf8; margin-bottom: 10px;">
                                            <i class="fas fa-file-invoice"></i>
                                        </div>
                                        <h6 class="font-weight-bold" style="color: var(--color-ink-0);">Belum Ada Dokumen Berita Acara OK</h6>
                                        <p class="text-muted mb-2" style="font-size: 0.82rem;">
                                            Unggah foto scan Surat Jalan untuk membuat Berita Acara OK otomatis.
                                        </p>
                                        <a href="{{ route('berita-acara.create') }}" class="btn btn-sm btn-primary px-3 py-1" style="border-radius: 8px;">
                                            <i class="fas fa-plus mr-1"></i> Buat Berita Acara
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- MOBILE VIEW: Hallmark Card Stack (Visible on screen < 768px) -->
            <div class="ba-mobile-list d-block d-md-none">
                @forelse($beritaAcaras as $index => $item)
                    <div class="ba-mobile-card">
                        <!-- Top: No BA + Status Badge -->
                        <div class="ba-mobile-card-top">
                            <div>
                                <a href="{{ route('berita-acara.show', $item) }}" class="ba-mobile-title">
                                    {{ $item->nomor_ba ?: $item->nomor_surat }}
                                </a>
                            </div>
                            <div>
                                <span class="ba-badge-status">
                                    <i class="fas fa-check-circle"></i>
                                    <span>{{ $item->status_verifikasi ?: 'TTB / LGT ok' }}</span>
                                </span>
                            </div>
                        </div>

                        <!-- Subtags: Ref SJ & PO -->
                        <div class="ba-mobile-tags">
                            <span class="ba-mobile-tag">
                                <i class="fas fa-truck mr-1 text-muted"></i>SJ: {{ $item->nomor_surat }}
                            </span>
                            @if($item->nomor_po)
                                <span class="ba-mobile-tag">
                                    <i class="fas fa-hashtag mr-1 text-muted"></i>PO: {{ $item->nomor_po }}
                                </span>
                            @endif
                        </div>

                        <!-- 2x2 Meta Grid -->
                        <div class="ba-mobile-grid">
                            <div>
                                <div class="ba-mobile-field-lbl"><i class="far fa-calendar-alt mr-1"></i> Tanggal Terima</div>
                                <div class="ba-mobile-field-val">
                                    {{ $item->tanggal_verifikasi ? \Carbon\Carbon::parse($item->tanggal_verifikasi)->translatedFormat('d M Y') : ($item->tanggal_surat ? \Carbon\Carbon::parse($item->tanggal_surat)->translatedFormat('d M Y') : '-') }}
                                </div>
                            </div>
                            <div>
                                <div class="ba-mobile-field-lbl"><i class="fas fa-boxes mr-1"></i> Jumlah Item</div>
                                <div class="ba-mobile-field-val text-info">
                                    {{ is_array($item->items) ? count($item->items) : 0 }} Item ({{ $item->total_quantity }} Unit)
                                </div>
                            </div>
                            <div>
                                <div class="ba-mobile-field-lbl"><i class="far fa-building mr-1"></i> Kepada Yth</div>
                                <div class="ba-mobile-field-val">
                                    {{ $item->kepada_yth ?: 'Ka. Pembelian' }}
                                </div>
                            </div>
                            <div>
                                <div class="ba-mobile-field-lbl"><i class="far fa-user mr-1"></i> Dibuat Oleh</div>
                                <div class="ba-mobile-field-val">
                                    {{ $item->nama_penerima ?: 'Bagas Purnomo' }}
                                </div>
                            </div>
                        </div>

                        <!-- Actions Touch Bar -->
                        <div class="ba-mobile-actions">
                            <a href="{{ route('berita-acara.print', $item) }}" target="_blank" class="ba-mobile-btn-print">
                                <i class="fas fa-print"></i>
                                <span>Cetak A4 / PDF</span>
                            </a>
                            <a href="{{ route('berita-acara.show', $item) }}" class="ba-mobile-btn-icon" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('berita-acara.edit', $item) }}" class="ba-mobile-btn-icon text-warning" title="Ubah Data">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('berita-acara.destroy', $item) }}" method="POST" class="d-inline m-0 form-delete-ba">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="ba-mobile-btn-icon text-danger btn-delete-ba" data-no-ba="{{ $item->nomor_ba ?: $item->nomor_surat }}" title="Hapus Dokumen">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(99, 102, 241, 0.1); display: inline-flex; align-items: center; justify-content: center; font-size: 1.3rem; color: #818cf8; margin-bottom: 8px;">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <h6 class="font-weight-bold mb-1" style="color: var(--color-ink-0);">Belum Ada Dokumen</h6>
                        <p class="text-muted mb-2" style="font-size: 0.8rem;">Unggah foto Surat Jalan untuk membuat Berita Acara.</p>
                        <a href="{{ route('berita-acara.create') }}" class="btn btn-sm btn-primary px-3">
                            <i class="fas fa-plus mr-1"></i> Buat Dokumen
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($beritaAcaras->hasPages())
                <div class="p-3 border-top" style="border-color: rgba(148, 163, 184, 0.12) !important;">
                    {{ $beritaAcaras->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $(document).on('click', '.btn-delete-ba', function(e) {
        e.preventDefault();
        var btn = $(this);
        var form = btn.closest('form');
        var noBa = btn.data('no-ba') || 'dokumen ini';

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Berita Acara?',
                html: `<div style="font-size: 0.88rem; color: #94a3b8; margin-top: 6px;">
                         Apakah Anda yakin ingin menghapus Berita Acara <strong style="color: #818cf8; font-family: 'JetBrains Mono', monospace;">${noBa}</strong>?
                         <div class="mt-2.5 p-2.5 rounded text-left" style="background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; font-size: 0.78rem; line-height: 1.35;">
                           <i class="fas fa-exclamation-triangle mr-1 text-danger"></i> <span>Data yang dihapus tidak dapat dikembalikan lagi.</span>
                         </div>
                       </div>`,
                icon: 'warning',
                iconColor: '#ef4444',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Ya, Hapus',
                cancelButtonText: 'Batal',
                background: '#1e293b',
                color: '#f8fafc',
                customClass: {
                    popup: 'border border-secondary rounded-xl',
                    confirmButton: 'btn btn-danger px-3 py-2 font-weight-600',
                    cancelButton: 'btn btn-secondary px-3 py-2 font-weight-600 mr-2'
                },
                buttonsStyling: false,
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        } else {
            if (confirm('Apakah Anda yakin ingin menghapus Berita Acara ' + noBa + '?')) {
                form.submit();
            }
        }
    });
});
</script>
@endpush
