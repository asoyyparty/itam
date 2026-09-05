@extends('layouts.admin')

@section('title', __('messages.pic_data'))

@section('content')
<style>
/* Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 */
:root {
    --hl-bg-canvas: #090d16;
    --hl-bg-surface: #0f172a;
    --hl-bg-card: rgba(15, 23, 42, 0.78);
    --hl-border-subtle: rgba(148, 163, 184, 0.14);
    --hl-border-active: rgba(148, 163, 184, 0.28);
    --hl-text-bright: #f8fafc;
    --hl-text-muted: #94a3b8;
    --hl-text-dim: #64748b;
    --hl-accent-indigo: #6366f1;
    --hl-accent-cyan: #06b6d4;
    --hl-accent-emerald: #10b981;
    --hl-accent-amber: #f59e0b;
    --hl-accent-crimson: #f43f5e;
    --hl-font-mono: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}

/* Master Header Banner */
.master-header-banner {
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.92) 0%, rgba(30, 41, 59, 0.82) 100%);
    border: 1px solid var(--hl-border-subtle);
    border-radius: 16px;
    padding: 18px 22px;
    margin-bottom: 18px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    backdrop-filter: blur(12px);
}
body.light-mode .master-header-banner {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(241, 245, 249, 0.95));
    border: 1px solid rgba(226, 232, 240, 0.9);
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
}

.master-banner-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
}

.master-banner-title-box {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
    flex: 1 1 300px;
}

.master-banner-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: rgba(99, 102, 241, 0.15);
    border: 1px solid rgba(99, 102, 241, 0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #818cf8;
    font-size: 1.25rem;
    flex-shrink: 0;
    box-shadow: 0 0 16px rgba(99, 102, 241, 0.2);
}

.master-count-pill {
    font-family: var(--hl-font-mono);
    font-size: 0.72rem;
    padding: 5px 12px;
    background: rgba(99, 102, 241, 0.12);
    border: 1px solid rgba(99, 102, 241, 0.3);
    color: #a5b4fc;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 700;
    letter-spacing: 0.06em;
    white-space: nowrap;
}

.live-dot-indigo {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #818cf8;
    box-shadow: 0 0 8px #818cf8;
    display: inline-block;
}

.btn-tactile-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 8px 18px;
    border-radius: 10px;
    font-size: 0.84rem;
    font-weight: 700;
    color: #ffffff !important;
    background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    border: 1px solid rgba(129, 140, 248, 0.4);
    box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    text-decoration: none !important;
    white-space: nowrap;
}
.btn-tactile-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(79, 70, 229, 0.45);
    filter: brightness(1.08);
}

/* Bento KPI Grid */
.pic-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 18px;
}

.pic-kpi-card {
    background: var(--hl-bg-card);
    border: 1px solid var(--hl-border-subtle);
    border-radius: 14px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
    backdrop-filter: blur(8px);
    transition: border-color 0.2s;
}
body.light-mode .pic-kpi-card {
    background: #ffffff;
    border-color: #e2e8f0;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
}

.pic-kpi-icon-box {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.pic-kpi-val {
    font-family: var(--hl-font-mono);
    font-size: 1.35rem;
    font-weight: 800;
    line-height: 1.1;
    color: var(--hl-text-bright);
}
body.light-mode .pic-kpi-val {
    color: #0f172a;
}

.pic-kpi-label {
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--hl-text-muted);
    margin-top: 2px;
    white-space: nowrap;
}

/* Main Table & Filter Card */
.master-table-card {
    background: var(--hl-bg-card);
    border: 1px solid var(--hl-border-subtle);
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.22);
    overflow: hidden;
    backdrop-filter: blur(12px);
}
body.light-mode .master-table-card {
    background: #ffffff;
    border-color: #e2e8f0;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
}

.master-card-header {
    padding: 14px 18px;
    border-bottom: 1px solid var(--hl-border-subtle);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    background: rgba(15, 23, 42, 0.4);
}
body.light-mode .master-card-header {
    background: #f8fafc;
    border-bottom-color: #e2e8f0;
}

/* Sleek Search Input */
.sleek-search-wrapper {
    position: relative;
    width: 100%;
    max-width: 420px;
}
.sleek-search-wrapper .search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--hl-text-dim);
    font-size: 0.85rem;
    pointer-events: none;
}
.sleek-search-wrapper .clear-search-btn {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--hl-text-dim);
    font-size: 0.82rem;
    text-decoration: none;
    padding: 4px;
    border-radius: 50%;
    transition: color 0.15s;
}
.sleek-search-wrapper .clear-search-btn:hover {
    color: var(--hl-text-bright);
}
.sleek-search-input {
    width: 100% !important;
    height: 40px !important;
    border-radius: 10px !important;
    background: rgba(15, 23, 42, 0.7) !important;
    border: 1px solid var(--hl-border-active) !important;
    color: var(--hl-text-bright) !important;
    font-size: 0.85rem !important;
    padding-left: 38px !important;
    padding-right: 36px !important;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
}
.sleek-search-input:focus {
    border-color: #818cf8 !important;
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2) !important;
    background: rgba(15, 23, 42, 0.9) !important;
}
body.light-mode .sleek-search-input {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    color: #1e293b !important;
}

/* Avatar Badge */
.pic-avatar {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(6, 182, 212, 0.1));
    border: 1px solid rgba(129, 140, 248, 0.35);
    color: #a5b4fc;
    font-family: var(--hl-font-mono);
    font-size: 0.82rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

/* Action Icon Buttons */
.btn-action-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.82rem;
    transition: all 0.15s;
    text-decoration: none !important;
    border: 1px solid transparent;
}
.btn-action-edit {
    background: rgba(245, 158, 11, 0.12);
    border-color: rgba(245, 158, 11, 0.25);
    color: #fbbf24 !important;
}
.btn-action-edit:hover {
    background: rgba(245, 158, 11, 0.25);
    border-color: rgba(245, 158, 11, 0.5);
    transform: translateY(-1px);
}
.btn-action-delete {
    background: rgba(244, 63, 94, 0.12);
    border-color: rgba(244, 63, 94, 0.25);
    color: #f43f5e !important;
}
.btn-action-delete:hover {
    background: rgba(244, 63, 94, 0.25);
    border-color: rgba(244, 63, 94, 0.5);
    transform: translateY(-1px);
}

/* Telemetry Status Pills */
.pic-status-pill {
    font-family: var(--hl-font-mono);
    font-size: 0.72rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
}
.pic-status-active {
    background: rgba(245, 158, 11, 0.12);
    border: 1px solid rgba(245, 158, 11, 0.3);
    color: #fbbf24;
}
.pic-status-standby {
    background: rgba(16, 185, 129, 0.12);
    border: 1px solid rgba(16, 185, 129, 0.3);
    color: #34d399;
}

/* Mobile Tactile Feed */
.pic-mobile-feed {
    padding: 10px 12px;
}

.pic-mobile-card {
    background: rgba(15, 23, 42, 0.7);
    border: 1px solid var(--hl-border-subtle);
    border-radius: 12px;
    padding: 12px 14px;
    margin-bottom: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    transition: border-color 0.15s;
}
body.light-mode .pic-mobile-card {
    background: #ffffff;
    border-color: #e2e8f0;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
}

.pic-mobile-card:last-child {
    margin-bottom: 0;
}

.pic-mobile-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.pic-mobile-name {
    font-size: 0.94rem;
    font-weight: 700;
    color: var(--hl-text-bright);
    line-height: 1.35;
    word-break: break-word;
    overflow-wrap: anywhere;
    min-width: 0;
}
body.light-mode .pic-mobile-name {
    color: #0f172a;
}

.pic-mobile-meta-box {
    margin-top: 10px;
    padding: 8px 10px;
    background: rgba(0, 0, 0, 0.22);
    border: 1px solid rgba(148, 163, 184, 0.08);
    border-radius: 8px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}
body.light-mode .pic-mobile-meta-box {
    background: #f8fafc;
    border-color: #e2e8f0;
}

.pic-mobile-stat-item {
    font-size: 0.76rem;
    color: var(--hl-text-muted);
}
.pic-mobile-stat-val {
    font-family: var(--hl-font-mono);
    font-weight: 700;
    color: var(--hl-text-bright);
    margin-left: 4px;
}
body.light-mode .pic-mobile-stat-val {
    color: #0f172a;
}

/* Modals */
.theme-modal .modal-content {
    background: #0f172a !important;
    border: 1px solid rgba(129, 140, 248, 0.3) !important;
    border-radius: 18px !important;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6) !important;
    backdrop-filter: blur(16px);
}
body.light-mode .theme-modal .modal-content {
    background: #ffffff !important;
    border-color: #e2e8f0 !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.1) !important;
}

.custom-modal-input {
    height: 44px !important;
    border-radius: 10px !important;
    border: 1px solid rgba(148, 163, 184, 0.22) !important;
    background: rgba(15, 23, 42, 0.4) !important;
    box-shadow: none !important;
    font-size: 0.9rem !important;
    color: var(--hl-text-bright) !important;
    transition: all 0.2s ease !important;
}
.custom-modal-input:focus {
    border-color: #818cf8 !important;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
    background: rgba(15, 23, 42, 0.6) !important;
}
body.light-mode .custom-modal-input {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}
body.light-mode .custom-modal-input:focus {
    background: #ffffff !important;
}

/* Responsive Rules */
@media (max-width: 767.98px) {
    .master-header-banner {
        padding: 14px 16px;
        border-radius: 14px;
        margin-bottom: 14px;
    }

    .master-banner-top {
        flex-direction: column !important;
        align-items: stretch !important;
        justify-content: flex-start !important;
        gap: 12px !important;
    }

    .master-banner-title-box {
        width: 100% !important;
        flex: 0 0 auto !important;
    }

    .master-banner-icon {
        width: 38px;
        height: 38px;
        font-size: 1.1rem;
    }

    .master-banner-actions {
        width: 100%;
    }

    .master-banner-actions .btn-tactile-primary {
        width: 100%;
        height: 42px;
        font-size: 0.86rem;
    }

    .pic-kpi-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        margin-bottom: 14px;
    }

    .pic-kpi-card {
        padding: 10px 12px;
        gap: 10px;
    }

    .pic-kpi-icon-box {
        width: 32px;
        height: 32px;
        font-size: 0.85rem;
    }

    .pic-kpi-val {
        font-size: 1.15rem;
    }

    .pic-kpi-label {
        font-size: 0.65rem;
    }

    .master-card-header {
        padding: 12px 14px !important;
    }

    .btn-action-icon {
        width: 36px;
        height: 36px;
        font-size: 0.88rem;
    }

    .sleek-search-wrapper {
        max-width: 100%;
    }

    .modal-footer-actions {
        flex-direction: column-reverse;
        width: 100%;
        gap: 8px !important;
    }
    .modal-footer-actions .btn {
        width: 100% !important;
        height: 42px !important;
    }
}
</style>

<!-- Hallmark Master Header Banner -->
<div class="master-header-banner">
    <div class="master-banner-top">
        <div class="master-banner-title-box">
            <div class="master-banner-icon">
                <i class="fas fa-user-shield"></i>
            </div>
            <div style="flex: 1; min-width: 0;">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h5 class="mb-0 font-weight-bold theme-text text-truncate" style="font-size: 1.12rem; line-height: 1.2;">
                        {{ __('messages.pic_data') }}
                    </h5>
                    <span class="master-count-pill">
                        <span class="live-dot-indigo"></span>
                        {{ $totalPics }} {{ __('messages.total_officers') ?? 'OFFICERS' }}
                    </span>
                </div>
                <small class="text-muted d-block" style="font-size: 0.78rem; margin-top: 3px; line-height: 1.35;">
                    {{ __('messages.pic_subtitle') }}
                </small>
            </div>
        </div>

        <div class="master-banner-actions">
            <button type="button" class="btn btn-tactile-primary" data-toggle="modal" data-target="#addPicModal">
                <i class="fas fa-plus mr-1"></i>
                <span>{{ __('messages.add_pic') }}</span>
            </button>
        </div>
    </div>
</div>

<!-- Bento KPI Telemetry Strips -->
<div class="pic-kpi-grid">
    <div class="pic-kpi-card">
        <div class="pic-kpi-icon-box" style="background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3); color: #818cf8;">
            <i class="fas fa-id-badge"></i>
        </div>
        <div>
            <div class="pic-kpi-val">{{ $totalPics }}</div>
            <div class="pic-kpi-label">{{ __('messages.total_officers') }}</div>
        </div>
    </div>

    <div class="pic-kpi-card">
        <div class="pic-kpi-icon-box" style="background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #fbbf24;">
            <i class="fas fa-bolt"></i>
        </div>
        <div>
            <div class="pic-kpi-val" style="color: #fbbf24;">{{ $totalActiveTickets }}</div>
            <div class="pic-kpi-label">{{ __('messages.active_incident_load') }}</div>
        </div>
    </div>

    <div class="pic-kpi-card">
        <div class="pic-kpi-icon-box" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399;">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <div class="pic-kpi-val" style="color: #34d399;">{{ $totalResolvedTickets }}</div>
            <div class="pic-kpi-label">{{ __('messages.resolved_by_roster') }}</div>
        </div>
    </div>

    <div class="pic-kpi-card">
        <div class="pic-kpi-icon-box" style="background: rgba(6, 182, 212, 0.15); border: 1px solid rgba(6, 182, 212, 0.3); color: #38bdf8;">
            <i class="fas fa-user-check"></i>
        </div>
        <div>
            <div class="pic-kpi-val" style="color: #38bdf8;">{{ $standbyPicsCount }}</div>
            <div class="pic-kpi-label">{{ __('messages.standby_available') }}</div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; border-radius: 12px; margin-bottom: 16px;">
        <button type="button" class="close text-white" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
@endif

<!-- Main Table Card -->
<div class="card theme-card master-table-card">
    <div class="master-card-header">
        <form action="{{ route('pics.index') }}" method="GET" class="w-100 m-0">
            <div class="sleek-search-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" class="form-control theme-input sleek-search-input" placeholder="{{ __('messages.search_pic_placeholder') }}" value="{{ request('search') }}" autocomplete="off">
                @if(request('search'))
                    <a href="{{ route('pics.index') }}" class="clear-search-btn" title="{{ __('messages.cancel') }}">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Desktop View (Tabel Industrial Telemetry) -->
    <div class="card-body p-0 d-none d-md-block">
        <div class="table-responsive m-0">
            <table class="table table-hover m-0 theme-table" style="vertical-align: middle;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--hl-border-subtle);">
                        <th width="70" class="pl-4 font-weight-bold" style="font-size: 0.76rem; letter-spacing: 0.05em; color: var(--hl-text-muted);">
                            {{ __('messages.no') }}
                        </th>
                        <th class="font-weight-bold" style="font-size: 0.76rem; letter-spacing: 0.05em; color: var(--hl-text-muted);">
                            {{ __('messages.pic_name') }}
                        </th>
                        <th width="180" class="font-weight-bold text-center" style="font-size: 0.76rem; letter-spacing: 0.05em; color: var(--hl-text-muted);">
                            {{ __('messages.active_incident_load') }}
                        </th>
                        <th width="180" class="font-weight-bold text-center" style="font-size: 0.76rem; letter-spacing: 0.05em; color: var(--hl-text-muted);">
                            {{ __('messages.resolved_by_roster') }}
                        </th>
                        <th width="130" class="text-center pr-4 font-weight-bold" style="font-size: 0.76rem; letter-spacing: 0.05em; color: var(--hl-text-muted);">
                            {{ __('messages.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pics as $pic)
                    @php
                        $initials = collect(explode(' ', $pic->name))->map(fn($seg) => mb_substr($seg, 0, 1))->take(2)->join('');
                    @endphp
                    <tr style="border-bottom: 1px solid rgba(148, 163, 184, 0.08);">
                        <td class="pl-4" style="font-family: var(--hl-font-mono); font-size: 0.82rem; color: var(--hl-text-dim);">
                            #{{ sprintf('%02d', $loop->iteration) }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center" style="gap: 12px;">
                                <div class="pic-avatar">{{ strtoupper($initials) }}</div>
                                <div>
                                    <div class="font-weight-bold theme-text" style="font-size: 0.92rem; line-height: 1.3;">
                                        {{ $pic->name }}
                                    </div>
                                    <small class="text-muted d-block" style="font-family: var(--hl-font-mono); font-size: 0.72rem;">
                                        ID: PIC-{{ sprintf('%03d', $pic->id) }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($pic->active_tickets_count > 0)
                                <span class="pic-status-pill pic-status-active">
                                    <i class="fas fa-bolt mr-1"></i>
                                    {{ $pic->active_tickets_count }} {{ __('messages.active_tickets_count_label') }}
                                </span>
                            @else
                                <span class="pic-status-pill pic-status-standby">
                                    <i class="fas fa-check mr-1"></i>
                                    {{ __('messages.status_standby') }}
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span style="font-family: var(--hl-font-mono); font-size: 0.85rem; font-weight: 700; color: var(--hl-text-bright);">
                                {{ $pic->resolved_tickets_count }}
                            </span>
                            <span class="text-muted ml-1" style="font-size: 0.75rem;">{{ __('messages.resolved_tickets_count_label') }}</span>
                        </td>
                        <td class="text-center pr-4">
                            <div class="d-flex align-items-center justify-content-center" style="gap: 6px;">
                                <button type="button" class="btn btn-action-icon btn-action-edit btn-edit-pic" 
                                    data-id="{{ $pic->id }}" 
                                    data-name="{{ $pic->name }}" 
                                    data-action="{{ route('pics.update', $pic->id) }}"
                                    title="{{ __('messages.edit') }}">
                                    <i class="fas fa-pen"></i>
                                </button>
                                @can('action_manage_master_data')
                                <form action="{{ route('pics.destroy', $pic->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-action-icon btn-action-delete btn-delete" 
                                        data-confirm-message="{{ __('messages.confirm_delete') }}"
                                        title="{{ __('messages.delete') }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <div style="width: 50px; height: 50px; border-radius: 14px; background: rgba(148, 163, 184, 0.08); display: inline-flex; align-items: center; justify-content: center; font-size: 1.4rem; color: var(--hl-text-dim); margin-bottom: 8px;">
                                <i class="fas fa-user-slash"></i>
                            </div>
                            <div class="font-weight-bold theme-text" style="font-size: 0.95rem;">{{ __('messages.no_data') }}</div>
                            <small class="text-muted">{{ __('messages.search_pic_placeholder') }}</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile View (Feed Kartu Taktil Anti-Truncation) -->
    <div class="d-block d-md-none pic-mobile-feed">
        @forelse($pics as $pic)
        @php
            $initials = collect(explode(' ', $pic->name))->map(fn($seg) => mb_substr($seg, 0, 1))->take(2)->join('');
        @endphp
        <div class="pic-mobile-card">
            <div class="pic-mobile-header">
                <div class="d-flex align-items-center" style="gap: 10px; min-width: 0; flex: 1;">
                    <div class="pic-avatar">{{ strtoupper($initials) }}</div>
                    <div style="min-width: 0; flex: 1;">
                        <div class="pic-mobile-name">{{ $pic->name }}</div>
                        <small class="text-muted d-block" style="font-family: var(--hl-font-mono); font-size: 0.7rem; margin-top: 1px;">
                            ID: PIC-{{ sprintf('%03d', $pic->id) }}
                        </small>
                    </div>
                </div>

                <div class="d-flex align-items-center" style="gap: 6px; flex-shrink: 0;">
                    <button type="button" class="btn btn-action-icon btn-action-edit btn-edit-pic" 
                        data-id="{{ $pic->id }}" 
                        data-name="{{ $pic->name }}" 
                        data-action="{{ route('pics.update', $pic->id) }}"
                        title="{{ __('messages.edit') }}">
                        <i class="fas fa-pen"></i>
                    </button>
                    @can('action_manage_master_data')
                    <form action="{{ route('pics.destroy', $pic->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="button" class="btn btn-action-icon btn-action-delete btn-delete" 
                            data-confirm-message="{{ __('messages.confirm_delete') }}"
                            title="{{ __('messages.delete') }}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                    @endcan
                </div>
            </div>

            <div class="pic-mobile-meta-box">
                <div class="pic-mobile-stat-item">
                    <span>{{ __('messages.active_incident_load') }}:</span>
                    @if($pic->active_tickets_count > 0)
                        <span class="pic-mobile-stat-val text-warning font-weight-bold">
                            {{ $pic->active_tickets_count }}
                        </span>
                    @else
                        <span class="pic-mobile-stat-val text-success font-weight-bold">
                            0 ({{ __('messages.status_standby') }})
                        </span>
                    @endif
                </div>
                <div class="pic-mobile-stat-item">
                    <span>{{ __('messages.resolved_by_roster') }}:</span>
                    <span class="pic-mobile-stat-val text-info font-weight-bold">
                        {{ $pic->resolved_tickets_count }}
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(148, 163, 184, 0.08); display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem; color: var(--hl-text-dim); margin-bottom: 8px;">
                <i class="fas fa-user-slash"></i>
            </div>
            <div class="font-weight-bold theme-text" style="font-size: 0.9rem;">{{ __('messages.no_data') }}</div>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Tambah PIC (Tactile Obsidian Modal) -->
<div class="modal fade theme-modal" id="addPicModal" tabindex="-1" role="dialog" aria-labelledby="addPicModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('pics.store') }}" method="POST" style="width: 100%;">
            @csrf
            <div class="modal-content">
                <div class="modal-header py-3 px-4" style="border-bottom: 1px solid rgba(148, 163, 184, 0.12) !important;">
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(99, 102, 241, 0.15); color: #818cf8; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h5 class="modal-title font-weight-bold theme-text" id="addPicModalLabel" style="font-size: 1.05rem;">
                            {{ __('messages.add_pic') }}
                        </h5>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-0">
                        <label class="form-label theme-text font-weight-bold" style="font-size: 0.85rem;">
                            {{ __('messages.pic_name') }} <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" class="form-control theme-input custom-modal-input" required placeholder="{{ __('messages.enter_pic_name') }}" autocomplete="off">
                        <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;">
                            {{ __('messages.pic_subtitle') }}
                        </small>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4 d-flex align-items-center justify-content-end modal-footer-actions" style="border-color: rgba(148, 163, 184, 0.12) !important; gap: 10px;">
                    <button type="button" class="btn btn-outline-secondary px-4" style="height: 42px; border-radius: 10px !important; font-weight: 600;" data-dismiss="modal">
                        {{ __('messages.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-tactile-primary px-4" style="height: 42px; border-radius: 10px !important;">
                        <i class="fas fa-check mr-1"></i> {{ __('messages.save') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit PIC (Reusable High-Efficiency Modal) -->
<div class="modal fade theme-modal" id="editPicModal" tabindex="-1" role="dialog" aria-labelledby="editPicModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="editPicForm" action="" method="POST" style="width: 100%;">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header py-3 px-4" style="border-bottom: 1px solid rgba(148, 163, 184, 0.12) !important;">
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(245, 158, 11, 0.15); color: #fbbf24; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <h5 class="modal-title font-weight-bold theme-text" id="editPicModalLabel" style="font-size: 1.05rem;">
                            {{ __('messages.edit_pic') }}
                        </h5>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-0">
                        <label class="form-label theme-text font-weight-bold" style="font-size: 0.85rem;">
                            {{ __('messages.pic_name') }} <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="editPicName" name="name" class="form-control theme-input custom-modal-input" required placeholder="{{ __('messages.enter_pic_name') }}" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4 d-flex align-items-center justify-content-end modal-footer-actions" style="border-color: rgba(148, 163, 184, 0.12) !important; gap: 10px;">
                    <button type="button" class="btn btn-outline-secondary px-4" style="height: 42px; border-radius: 10px !important; font-weight: 600;" data-dismiss="modal">
                        {{ __('messages.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-tactile-primary px-4" style="height: 42px; border-radius: 10px !important; background: linear-gradient(135deg, #d97706 0%, #b45309 100%); border-color: rgba(245, 158, 11, 0.4);">
                        <i class="fas fa-save mr-1"></i> {{ __('messages.save') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dynamic Edit Modal population
    const editBtns = document.querySelectorAll('.btn-edit-pic');
    const editModal = $('#editPicModal');
    const editForm = document.getElementById('editPicForm');
    const editInput = document.getElementById('editPicName');

    editBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            const name = this.getAttribute('data-name');
            if (editForm && editInput) {
                editForm.action = action;
                editInput.value = name;
                editModal.modal('show');
                setTimeout(function() {
                    editInput.focus();
                    editInput.select();
                }, 300);
            }
        });
    });

    // Auto focus add modal input on shown
    $('#addPicModal').on('shown.bs.modal', function() {
        $(this).find('input[name="name"]').focus();
    });
});
</script>
@endpush
@endsection
