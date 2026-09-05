@extends('layouts.admin')

@section('title', __('messages.ip_management'))

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
    --hl-accent-cyan: #06b6d4;
    --hl-accent-indigo: #6366f1;
    --hl-accent-emerald: #10b981;
    --hl-accent-amber: #f59e0b;
    --hl-accent-crimson: #f43f5e;
    --hl-font-mono: ui-monospace, "JetBrains Mono", SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
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
    background: rgba(6, 182, 212, 0.15);
    border: 1px solid rgba(6, 182, 212, 0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #38bdf8;
    font-size: 1.25rem;
    flex-shrink: 0;
    box-shadow: 0 0 16px rgba(6, 182, 212, 0.2);
}

.master-count-pill {
    font-family: var(--hl-font-mono);
    font-size: 0.72rem;
    padding: 5px 12px;
    background: rgba(6, 182, 212, 0.12);
    border: 1px solid rgba(6, 182, 212, 0.3);
    color: #38bdf8;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 700;
    letter-spacing: 0.06em;
    white-space: nowrap;
}

.live-dot-cyan {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #06b6d4;
    box-shadow: 0 0 8px #06b6d4;
    display: inline-block;
}

.master-banner-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-tactile-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 0.82rem;
    font-weight: 700;
    color: #ffffff !important;
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    border: 1px solid rgba(56, 189, 248, 0.4);
    box-shadow: 0 4px 14px rgba(2, 132, 199, 0.3);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    text-decoration: none !important;
    white-space: nowrap;
}
.btn-tactile-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(2, 132, 199, 0.45);
    filter: brightness(1.08);
}

.btn-tactile-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--hl-text-bright) !important;
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid var(--hl-border-active);
    transition: all 0.2s;
    text-decoration: none !important;
    white-space: nowrap;
}
.btn-tactile-secondary:hover {
    background: rgba(30, 41, 59, 0.9);
    border-color: rgba(148, 163, 184, 0.45);
    color: #ffffff !important;
    transform: translateY(-1px);
}
body.light-mode .btn-tactile-secondary {
    background: #ffffff;
    border-color: #cbd5e1;
    color: #334155 !important;
}
body.light-mode .btn-tactile-secondary:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
}

/* Bento KPI Grid */
.ip-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 18px;
}

.ip-kpi-card {
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
body.light-mode .ip-kpi-card {
    background: #ffffff;
    border-color: #e2e8f0;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
}

.ip-kpi-icon-box {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.ip-kpi-val {
    font-family: var(--hl-font-mono);
    font-size: 1.35rem;
    font-weight: 800;
    line-height: 1.1;
    color: var(--hl-text-bright);
}
body.light-mode .ip-kpi-val {
    color: #0f172a;
}

.ip-kpi-label {
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--hl-text-muted);
    margin-top: 2px;
    white-space: nowrap;
}

/* Main Console Card */
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

/* Tactical Filter Console */
.ip-console-header {
    padding: 16px 18px;
    border-bottom: 1px solid var(--hl-border-subtle);
    background: rgba(15, 23, 42, 0.4);
    display: flex;
    flex-direction: column;
    gap: 12px;
}
body.light-mode .ip-console-header {
    background: #f8fafc;
    border-bottom-color: #e2e8f0;
}

/* Status Quick Ribbon */
.ip-ribbon-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    padding-bottom: 2px;
}
.ip-ribbon-wrapper::-webkit-scrollbar {
    height: 3px;
}
.ip-ribbon-wrapper::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.2);
    border-radius: 4px;
}
.ip-status-ribbon {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    min-width: max-content;
}
.ip-ribbon-pill {
    font-size: 0.76rem;
    font-weight: 600;
    padding: 6px 14px;
    border-radius: 8px;
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid var(--hl-border-subtle);
    color: var(--hl-text-muted);
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.15s;
}
.ip-ribbon-pill:hover {
    color: var(--hl-text-bright);
    border-color: var(--hl-border-active);
    background: rgba(30, 41, 59, 0.8);
}
.ip-ribbon-pill.active {
    background: rgba(6, 182, 212, 0.15);
    border-color: rgba(6, 182, 212, 0.45);
    color: #38bdf8 !important;
    font-weight: 700;
}
body.light-mode .ip-ribbon-pill {
    background: #ffffff;
    border-color: #cbd5e1;
    color: #475569;
}
body.light-mode .ip-ribbon-pill:hover {
    background: #f1f5f9;
    color: #0f172a;
}
body.light-mode .ip-ribbon-pill.active {
    background: #e0f2fe;
    border-color: #0284c7;
    color: #0369a1 !important;
}

/* Tactical Controls Row */
.ip-controls-row {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.sleek-search-wrapper {
    position: relative;
    flex: 1;
    min-width: 240px;
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
    border-color: #06b6d4 !important;
    box-shadow: 0 0 0 2px rgba(6, 182, 212, 0.2) !important;
    background: rgba(15, 23, 42, 0.9) !important;
}
body.light-mode .sleek-search-input {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    color: #1e293b !important;
}

.vlan-select-wrap {
    min-width: 220px;
    flex-shrink: 0;
}
.custom-vlan-select {
    height: 40px !important;
    border-radius: 10px !important;
    background: rgba(15, 23, 42, 0.7) !important;
    border: 1px solid var(--hl-border-active) !important;
    color: var(--hl-text-bright) !important;
    font-size: 0.84rem !important;
    padding: 0 12px !important;
}
body.light-mode .custom-vlan-select {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    color: #1e293b !important;
}

/* IP Monospace Font Styling */
.ip-mono-badge {
    font-family: var(--hl-font-mono);
    font-size: 0.92rem;
    font-weight: 700;
    letter-spacing: -0.01em;
    color: #38bdf8;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
body.light-mode .ip-mono-badge {
    color: #0284c7;
}

/* Live Ping Badges */
.ping-pulse-badge {
    font-family: var(--hl-font-mono);
    font-size: 0.7rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
}
.ping-online {
    background: rgba(16, 185, 129, 0.15);
    border: 1px solid rgba(16, 185, 129, 0.35);
    color: #34d399;
    box-shadow: 0 0 10px rgba(16, 185, 129, 0.2);
}
.ping-offline {
    background: rgba(244, 63, 94, 0.15);
    border: 1px solid rgba(244, 63, 94, 0.35);
    color: #f43f5e;
}
.ping-unchecked {
    background: rgba(148, 163, 184, 0.12);
    border: 1px solid rgba(148, 163, 184, 0.2);
    color: #94a3b8;
}

/* Allocation Badges & Dropdown */
.status-pill-alloc {
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
.alloc-available {
    background: rgba(16, 185, 129, 0.12);
    border: 1px solid rgba(16, 185, 129, 0.3);
    color: #34d399;
}
.alloc-used {
    background: rgba(6, 182, 212, 0.12);
    border: 1px solid rgba(6, 182, 212, 0.3);
    color: #38bdf8;
}
.alloc-reserved {
    background: rgba(245, 158, 11, 0.12);
    border: 1px solid rgba(245, 158, 11, 0.3);
    color: #fbbf24;
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
.btn-action-ping {
    background: rgba(16, 185, 129, 0.12);
    border-color: rgba(16, 185, 129, 0.28);
    color: #34d399 !important;
}
.btn-action-ping:hover {
    background: rgba(16, 185, 129, 0.28);
    border-color: rgba(16, 185, 129, 0.6);
    transform: translateY(-1px);
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

/* Mobile Tactile Feed */
.ip-mobile-feed {
    padding: 10px 12px;
}

.ip-mobile-card {
    background: rgba(15, 23, 42, 0.7);
    border: 1px solid var(--hl-border-subtle);
    border-radius: 14px;
    padding: 13px 15px;
    margin-bottom: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    transition: border-color 0.15s;
}
body.light-mode .ip-mobile-card {
    background: #ffffff;
    border-color: #e2e8f0;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
}

.ip-mobile-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(148, 163, 184, 0.1);
}

.ip-mobile-meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    padding: 10px 0;
}

.ip-mobile-meta-item {
    font-size: 0.78rem;
    line-height: 1.35;
    word-break: break-word;
    overflow-wrap: anywhere;
}
.ip-mobile-meta-label {
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--hl-text-dim);
    margin-bottom: 2px;
}

.ip-mobile-actions-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    padding-top: 10px;
    border-top: 1px solid rgba(148, 163, 184, 0.1);
}

.btn-mobile-ping {
    flex: 1;
    height: 40px;
    border-radius: 10px;
    font-size: 0.82rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    background: rgba(16, 185, 129, 0.12);
    border: 1px solid rgba(16, 185, 129, 0.35);
    color: #34d399 !important;
    transition: all 0.2s;
    text-decoration: none !important;
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
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .master-banner-actions .btn-tactile-secondary {
        flex: 0 0 auto;
        min-width: 100px;
        height: 42px;
        padding: 0 14px;
        font-size: 0.82rem;
        box-sizing: border-box;
    }

    .master-banner-actions .btn-tactile-primary {
        flex: 1;
        min-width: 0;
        height: 42px;
        font-size: 0.84rem;
        box-sizing: border-box;
    }

    .ip-kpi-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        margin-bottom: 14px;
    }

    .ip-kpi-card {
        padding: 10px 12px;
        gap: 10px;
    }

    .ip-kpi-icon-box {
        width: 32px;
        height: 32px;
        font-size: 0.85rem;
    }

    .ip-kpi-val {
        font-size: 1.15rem;
    }

    .ip-kpi-label {
        font-size: 0.65rem;
    }

    .ip-console-header {
        padding: 12px 14px !important;
    }

    .ip-controls-row {
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
    }

    .vlan-select-wrap {
        width: 100%;
    }

    .btn-action-icon {
        width: 40px;
        height: 40px;
        font-size: 0.95rem;
    }
}
</style>

<!-- Hallmark Master Header Banner -->
<div class="master-header-banner">
    <div class="master-banner-top">
        <div class="master-banner-title-box">
            <div class="master-banner-icon">
                <i class="fas fa-network-wired"></i>
            </div>
            <div style="flex: 1; min-width: 0;">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h5 class="mb-0 font-weight-bold theme-text text-truncate" style="font-size: 1.12rem; line-height: 1.2;">
                        {{ __('messages.ip_management') }}
                    </h5>
                    <span class="master-count-pill">
                        <span class="live-dot-cyan"></span>
                        {{ $totalIps }} {{ __('messages.total_managed_ips') ?? 'IPS MANAGED' }}
                    </span>
                </div>
                <small class="text-muted d-block" style="font-size: 0.78rem; margin-top: 3px; line-height: 1.35;">
                    {{ __('messages.ip_subtitle') }}
                </small>
            </div>
        </div>

        <div class="master-banner-actions">
            <a href="{{ route('ips.export', request()->query()) }}" class="btn btn-tactile-secondary" title="{{ __('messages.export') }}">
                <i class="fas fa-file-export mr-1"></i>
                <span>{{ __('messages.export') }}</span>
            </a>
            @can('action_manage_network')
            <a href="{{ route('ips.create') }}" class="btn btn-tactile-primary" title="{{ __('messages.add_ip') }}">
                <i class="fas fa-plus mr-1"></i>
                <span>{{ __('messages.add_ip') }}</span>
            </a>
            @endcan
        </div>
    </div>
</div>

<!-- Bento KPI Matrix (NOC Telemetry) -->
<div class="ip-kpi-grid">
    <div class="ip-kpi-card">
        <div class="ip-kpi-icon-box" style="background: rgba(6, 182, 212, 0.15); border: 1px solid rgba(6, 182, 212, 0.3); color: #38bdf8;">
            <i class="fas fa-network-wired"></i>
        </div>
        <div>
            <div class="pic-kpi-val" style="color: #38bdf8;">{{ $totalIps }}</div>
            <div class="pic-kpi-label">{{ __('messages.total_managed_ips') }}</div>
        </div>
    </div>

    <div class="ip-kpi-card">
        <div class="ip-kpi-icon-box" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399;">
            <i class="fas fa-broadcast-tower"></i>
        </div>
        <div>
            <div class="pic-kpi-val" style="color: #34d399;">
                {{ $onlineIps }}
            </div>
            <div class="pic-kpi-label">{{ __('messages.devices_online') }}</div>
        </div>
    </div>

    <div class="ip-kpi-card">
        <div class="ip-kpi-icon-box" style="background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3); color: #818cf8;">
            <i class="fas fa-laptop-code"></i>
        </div>
        <div>
            <div class="pic-kpi-val" style="color: #818cf8;">{{ $usedIps }}</div>
            <div class="pic-kpi-label">{{ __('messages.ip_in_use') }}</div>
        </div>
    </div>

    <div class="ip-kpi-card">
        <div class="ip-kpi-icon-box" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981;">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <div class="pic-kpi-val" style="color: #10b981;">{{ $availableIps }}</div>
            <div class="pic-kpi-label">{{ __('messages.ip_available_capacity') }}</div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; border-radius: 12px; margin-bottom: 16px;">
        <button type="button" class="close text-white" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
@endif

<!-- Main NOC Console Card -->
<div class="card theme-card master-table-card">
    <div class="ip-console-header">
        <!-- Quick Status Ribbon Tabs -->
        <div class="ip-ribbon-wrapper">
            <div class="ip-status-ribbon">
                @php
                    $curStatus = request('status');
                    $curPing = request('ping_status');
                    $isAll = empty($curStatus) && empty($curPing);
                @endphp
                <a href="{{ route('ips.index', request()->except(['status', 'ping_status', 'page'])) }}" class="ip-ribbon-pill {{ $isAll ? 'active' : '' }}">
                    <i class="fas fa-layer-group"></i>
                    <span>{{ __('messages.all_ips') }}</span>
                    <span class="badge badge-pill badge-secondary ml-1" style="font-size: 0.65rem;">{{ $totalIps }}</span>
                </a>
                <a href="{{ route('ips.index', array_merge(request()->except(['status', 'ping_status', 'page']), ['ping_status' => 'online'])) }}" class="ip-ribbon-pill {{ $curPing == 'online' ? 'active' : '' }}">
                    <i class="fas fa-circle text-success" style="font-size: 0.6rem;"></i>
                    <span>{{ __('messages.devices_online') }}</span>
                    <span class="badge badge-pill badge-success ml-1" style="font-size: 0.65rem;">{{ $onlineIps }}</span>
                </a>
                <a href="{{ route('ips.index', array_merge(request()->except(['status', 'ping_status', 'page']), ['ping_status' => 'offline'])) }}" class="ip-ribbon-pill {{ $curPing == 'offline' ? 'active' : '' }}">
                    <i class="fas fa-circle text-danger" style="font-size: 0.6rem;"></i>
                    <span>{{ __('messages.devices_offline') }}</span>
                </a>
                <a href="{{ route('ips.index', array_merge(request()->except(['status', 'ping_status', 'page']), ['status' => 'Available'])) }}" class="ip-ribbon-pill {{ $curStatus == 'Available' ? 'active' : '' }}">
                    <i class="fas fa-check text-info" style="font-size: 0.65rem;"></i>
                    <span>{{ __('messages.available') }}</span>
                    <span class="badge badge-pill badge-info ml-1" style="font-size: 0.65rem;">{{ $availableIps }}</span>
                </a>
                <a href="{{ route('ips.index', array_merge(request()->except(['status', 'ping_status', 'page']), ['status' => 'Used'])) }}" class="ip-ribbon-pill {{ $curStatus == 'Used' ? 'active' : '' }}">
                    <i class="fas fa-laptop text-primary" style="font-size: 0.65rem;"></i>
                    <span>{{ __('messages.used') }}</span>
                    <span class="badge badge-pill badge-primary ml-1" style="font-size: 0.65rem;">{{ $usedIps }}</span>
                </a>
                <a href="{{ route('ips.index', array_merge(request()->except(['status', 'ping_status', 'page']), ['status' => 'Reserved'])) }}" class="ip-ribbon-pill {{ $curStatus == 'Reserved' ? 'active' : '' }}">
                    <i class="fas fa-lock text-warning" style="font-size: 0.65rem;"></i>
                    <span>{{ __('messages.reserved') }}</span>
                    <span class="badge badge-pill badge-warning ml-1" style="font-size: 0.65rem;">{{ $reservedIps }}</span>
                </a>
            </div>
        </div>

        <!-- Tactical Controls Form (VLAN & Search) -->
        <form action="{{ route('ips.index') }}" method="GET" class="w-100 m-0">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            @if(request('ping_status'))
                <input type="hidden" name="ping_status" value="{{ request('ping_status') }}">
            @endif

            <div class="ip-controls-row">
                <!-- VLAN Filter -->
                <div class="vlan-select-wrap">
                    <select name="vlan_id" class="form-control custom-vlan-select" onchange="this.form.submit()">
                        <option value="">{{ __('messages.all_vlans') }}</option>
                        @foreach($vlans as $vlan)
                            <option value="{{ $vlan->id }}" {{ request('vlan_id') == $vlan->id ? 'selected' : '' }}>
                                VLAN {{ $vlan->vlan_number }} - {{ $vlan->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sleek Search Input -->
                <div class="sleek-search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" class="form-control theme-input sleek-search-input" placeholder="{{ __('messages.search_ip') }}" value="{{ request('search') }}" autocomplete="off">
                    @if(request()->anyFilled(['search', 'vlan_id', 'status', 'ping_status']))
                        <a href="{{ route('ips.index') }}" class="clear-search-btn" title="{{ __('messages.cancel') }}">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Desktop View (Tabel Industrial NOC) -->
    <div class="card-body p-0 d-none d-md-block">
        <div class="table-responsive m-0">
            <table class="table table-hover m-0 theme-table" style="vertical-align: middle;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--hl-border-subtle);">
                        <th width="60" class="pl-4 font-weight-bold" style="font-size: 0.76rem; letter-spacing: 0.05em; color: var(--hl-text-muted);">
                            {{ __('messages.no') }}
                        </th>
                        <th width="240" class="font-weight-bold" style="font-size: 0.76rem; letter-spacing: 0.05em; color: var(--hl-text-muted);">
                            {{ __('messages.ip_address') }}
                        </th>
                        <th class="font-weight-bold" style="font-size: 0.76rem; letter-spacing: 0.05em; color: var(--hl-text-muted);">
                            {{ __('messages.assigned_asset') }}
                        </th>
                        <th width="160" class="font-weight-bold" style="font-size: 0.76rem; letter-spacing: 0.05em; color: var(--hl-text-muted);">
                            {{ __('messages.vlans') }}
                        </th>
                        <th class="font-weight-bold" style="font-size: 0.76rem; letter-spacing: 0.05em; color: var(--hl-text-muted);">
                            {{ __('messages.assigned_employee') }}
                        </th>
                        <th width="140" class="font-weight-bold text-center" style="font-size: 0.76rem; letter-spacing: 0.05em; color: var(--hl-text-muted);">
                            {{ __('messages.status') }}
                        </th>
                        <th width="130" class="text-center pr-4 font-weight-bold" style="font-size: 0.76rem; letter-spacing: 0.05em; color: var(--hl-text-muted);">
                            {{ __('messages.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ips as $ip)
                    <tr class="ip-row" data-ip-id="{{ $ip->id }}" style="border-bottom: 1px solid rgba(148, 163, 184, 0.08);">
                        <td class="pl-4" style="font-family: var(--hl-font-mono); font-size: 0.82rem; color: var(--hl-text-dim);">
                            #{{ sprintf('%02d', $ips->firstItem() + $loop->index) }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                                <span class="ip-mono-badge">
                                    <i class="fas fa-network-wired text-muted mr-1" style="font-size: 0.8rem;"></i>
                                    {{ $ip->ip_address }}
                                </span>
                                <span class="ping-status-badge-{{ $ip->id }}">
                                    @if($ip->is_online === true)
                                        <span class="ping-pulse-badge ping-online" title="{{ $ip->last_ping_at ? 'Ping: ' . $ip->last_ping_at->diffForHumans() : 'Online' }}">
                                            <i class="fas fa-circle text-xs"></i> Online
                                        </span>
                                    @elseif($ip->is_online === false)
                                        <span class="ping-pulse-badge ping-offline" title="{{ $ip->last_ping_at ? 'Ping: ' . $ip->last_ping_at->diffForHumans() : 'Offline' }}">
                                            <i class="fas fa-circle text-xs"></i> Offline
                                        </span>
                                    @else
                                        <span class="ping-pulse-badge ping-unchecked" title="Unchecked">
                                            <i class="far fa-circle text-xs"></i> Unchecked
                                        </span>
                                    @endif
                                </span>
                            </div>
                            @if($ip->mac_address)
                                <small class="text-muted d-block mt-0.5" style="font-family: var(--hl-font-mono); font-size: 0.7rem;">
                                    MAC: {{ $ip->mac_address }}
                                </small>
                            @endif
                        </td>
                        <td>
                            @if($ip->asset)
                                <a href="{{ route('assets.show', $ip->asset_id) }}" class="font-weight-bold text-info text-decoration-none" style="font-size: 0.88rem;">
                                    {{ $ip->asset->name }}
                                </a>
                                <small class="text-muted d-block" style="font-family: var(--hl-font-mono); font-size: 0.72rem;">
                                    TAG: {{ $ip->asset->asset_tag }}
                                </small>
                            @elseif($ip->notes)
                                <span class="theme-text" style="font-size: 0.82rem;">{{ $ip->notes }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($ip->vlan)
                                <span class="badge" style="background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3); color: #a5b4fc; font-size: 0.76rem; border-radius: 6px; padding: 4px 8px;">
                                    VLAN {{ $ip->vlan->vlan_number }}
                                </span>
                                <small class="text-muted d-block mt-0.5" style="font-size: 0.7rem;">{{ $ip->vlan->name }}</small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($ip->employee)
                                <a href="{{ route('employees.show', $ip->employee_id) }}" class="font-weight-bold text-info text-decoration-none" style="font-size: 0.86rem;">
                                    {{ $ip->employee->name }}
                                </a>
                                <small class="text-muted d-block" style="font-family: var(--hl-font-mono); font-size: 0.7rem;">
                                    {{ $ip->employee->employee_id }}
                                </small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="dropdown">
                                @can('action_manage_network')
                                <button class="btn btn-sm dropdown-toggle p-0 border-0 bg-transparent status-btn" type="button" data-toggle="dropdown" aria-expanded="false" data-id="{{ $ip->id }}" style="box-shadow: none;">
                                    @if($ip->status == 'Available')
                                        <span class="status-pill-alloc alloc-available status-badge">
                                            <i class="fas fa-check mr-1"></i> {{ __('messages.available') }}
                                        </span>
                                    @elseif($ip->status == 'Used')
                                        <span class="status-pill-alloc alloc-used status-badge">
                                            <i class="fas fa-laptop mr-1"></i> {{ __('messages.used') }}
                                        </span>
                                    @else
                                        <span class="status-pill-alloc alloc-reserved status-badge">
                                            <i class="fas fa-lock mr-1"></i> {{ __('messages.reserved') }}
                                        </span>
                                    @endif
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" style="background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 10px; min-width: 140px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
                                    <a class="dropdown-item status-change-btn text-success" href="#" data-status="Available" style="font-size: 0.82rem; font-weight: 600;">
                                        <i class="fas fa-check mr-2"></i> {{ __('messages.available') }}
                                    </a>
                                    <a class="dropdown-item status-change-btn text-info" href="#" data-status="Used" style="font-size: 0.82rem; font-weight: 600;">
                                        <i class="fas fa-laptop mr-2"></i> {{ __('messages.used') }}
                                    </a>
                                    <a class="dropdown-item status-change-btn text-warning" href="#" data-status="Reserved" style="font-size: 0.82rem; font-weight: 600;">
                                        <i class="fas fa-lock mr-2"></i> {{ __('messages.reserved') }}
                                    </a>
                                </div>
                                @else
                                    @if($ip->status == 'Available')
                                        <span class="status-pill-alloc alloc-available">
                                            <i class="fas fa-check mr-1"></i> {{ __('messages.available') }}
                                        </span>
                                    @elseif($ip->status == 'Used')
                                        <span class="status-pill-alloc alloc-used">
                                            <i class="fas fa-laptop mr-1"></i> {{ __('messages.used') }}
                                        </span>
                                    @else
                                        <span class="status-pill-alloc alloc-reserved">
                                            <i class="fas fa-lock mr-1"></i> {{ __('messages.reserved') }}
                                        </span>
                                    @endif
                                @endcan
                            </div>
                        </td>
                        <td class="text-center pr-4">
                            <div class="d-flex align-items-center justify-content-center" style="gap: 6px;">
                                <button type="button" class="btn btn-action-icon btn-action-ping ping-btn"
                                    data-id="{{ $ip->id }}"
                                    data-ping-url="{{ route('ips.ping', $ip) }}"
                                    title="{{ __('messages.quick_ping') }}">
                                    <i class="fas fa-play" style="font-size: 0.75rem;"></i>
                                </button>
                                @can('action_manage_network')
                                <a href="{{ route('ips.edit', array_merge([$ip->id], request()->query())) }}" class="btn btn-action-icon btn-action-edit" title="{{ __('messages.edit') }}">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form action="{{ route('ips.destroy', array_merge([$ip->id], request()->query())) }}" method="POST" class="d-inline">
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
                        <td colspan="7" class="text-center py-5 text-muted">
                            <div style="width: 50px; height: 50px; border-radius: 14px; background: rgba(148, 163, 184, 0.08); display: inline-flex; align-items: center; justify-content: center; font-size: 1.4rem; color: var(--hl-text-dim); margin-bottom: 8px;">
                                <i class="fas fa-network-wired"></i>
                            </div>
                            <div class="font-weight-bold theme-text" style="font-size: 0.95rem;">{{ __('messages.no_data') }}</div>
                            <small class="text-muted">{{ __('messages.search_ip') }}</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile View (Feed Kartu Taktil NOC) -->
    <div class="d-block d-md-none ip-mobile-feed">
        @forelse($ips as $ip)
        <div class="ip-mobile-card ip-row" data-ip-id="{{ $ip->id }}">
            <div class="ip-mobile-header">
                <div class="d-flex align-items-center" style="gap: 8px;">
                    <span class="ip-mono-badge" style="font-size: 0.95rem;">
                        <i class="fas fa-network-wired text-muted" style="font-size: 0.8rem;"></i>
                        {{ $ip->ip_address }}
                    </span>
                </div>
                <span class="ping-status-badge-{{ $ip->id }}">
                    @if($ip->is_online === true)
                        <span class="ping-pulse-badge ping-online">
                            <i class="fas fa-circle text-xs"></i> Online
                        </span>
                    @elseif($ip->is_online === false)
                        <span class="ping-pulse-badge ping-offline">
                            <i class="fas fa-circle text-xs"></i> Offline
                        </span>
                    @else
                        <span class="ping-pulse-badge ping-unchecked">
                            <i class="far fa-circle text-xs"></i> Unchecked
                        </span>
                    @endif
                </span>
            </div>

            <div class="ip-mobile-meta-grid">
                <div class="ip-mobile-meta-item">
                    <div class="ip-mobile-meta-label">{{ __('messages.assigned_asset') }}</div>
                    @if($ip->asset)
                        <a href="{{ route('assets.show', $ip->asset_id) }}" class="font-weight-bold text-info text-decoration-none">
                            {{ $ip->asset->name }}
                        </a>
                        <small class="text-muted d-block" style="font-family: var(--hl-font-mono); font-size: 0.7rem;">{{ $ip->asset->asset_tag }}</small>
                    @elseif($ip->notes)
                        <span class="theme-text">{{ $ip->notes }}</span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </div>

                <div class="ip-mobile-meta-item">
                    <div class="ip-mobile-meta-label">{{ __('messages.vlans') }}</div>
                    @if($ip->vlan)
                        <span class="font-weight-bold" style="color: #a5b4fc;">VLAN {{ $ip->vlan->vlan_number }}</span>
                        <small class="text-muted d-block">{{ $ip->vlan->name }}</small>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </div>

                <div class="ip-mobile-meta-item">
                    <div class="ip-mobile-meta-label">{{ __('messages.assigned_employee') }}</div>
                    @if($ip->employee)
                        <span class="font-weight-bold theme-text">{{ $ip->employee->name }}</span>
                        <small class="text-muted d-block" style="font-family: var(--hl-font-mono); font-size: 0.7rem;">{{ $ip->employee->employee_id }}</small>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </div>

                <div class="ip-mobile-meta-item">
                    <div class="ip-mobile-meta-label">{{ __('messages.status') }}</div>
                    @if($ip->status == 'Available')
                        <span class="status-pill-alloc alloc-available" style="padding: 2px 8px; font-size: 0.68rem;">
                            <i class="fas fa-check mr-1"></i> {{ __('messages.available') }}
                        </span>
                    @elseif($ip->status == 'Used')
                        <span class="status-pill-alloc alloc-used" style="padding: 2px 8px; font-size: 0.68rem;">
                            <i class="fas fa-laptop mr-1"></i> {{ __('messages.used') }}
                        </span>
                    @else
                        <span class="status-pill-alloc alloc-reserved" style="padding: 2px 8px; font-size: 0.68rem;">
                            <i class="fas fa-lock mr-1"></i> {{ __('messages.reserved') }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="ip-mobile-actions-bar">
                <button type="button" class="btn btn-mobile-ping ping-btn"
                    data-id="{{ $ip->id }}"
                    data-ping-url="{{ route('ips.ping', $ip) }}"
                    title="{{ __('messages.quick_ping') }}">
                    <i class="fas fa-play" style="font-size: 0.75rem;"></i>
                    <span>{{ __('messages.quick_ping') }}</span>
                </button>

                @can('action_manage_network')
                <a href="{{ route('ips.edit', array_merge([$ip->id], request()->query())) }}" class="btn btn-action-icon btn-action-edit" title="{{ __('messages.edit') }}">
                    <i class="fas fa-pen"></i>
                </a>
                <form action="{{ route('ips.destroy', array_merge([$ip->id], request()->query())) }}" method="POST" class="d-inline">
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
        @empty
        <div class="text-center py-5 text-muted">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(148, 163, 184, 0.08); display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem; color: var(--hl-text-dim); margin-bottom: 8px;">
                <i class="fas fa-network-wired"></i>
            </div>
            <div class="font-weight-bold theme-text" style="font-size: 0.9rem;">{{ __('messages.no_data') }}</div>
        </div>
        @endforelse
    </div>

    @if($ips->hasPages())
        <div class="pagination-footer card-footer d-flex justify-content-end align-items-center flex-wrap py-2" style="background: transparent; border-top: 1px solid var(--hl-border-subtle); gap: 10px;">
            {{ $ips->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // Single Ping AJAX Trigger
    $(document).on('click', '.ping-btn', function () {
        var button = $(this);
        var ipId = button.data('id');
        var icon = button.find('i');
        var originalClass = icon.attr('class');
        var pingUrl = button.data('ping-url');
        var statusContainer = $('.ping-status-badge-' + ipId);

        // Show spinner state
        icon.attr('class', 'fas fa-spinner fa-spin');
        button.prop('disabled', true);
        statusContainer.html('<span class="ping-pulse-badge ping-unchecked"><i class="fas fa-spinner fa-spin text-xs"></i> Pinging...</span>');

        $.ajax({
            url: pingUrl,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                button.prop('disabled', false);
                if (response.online) {
                    icon.attr('class', 'fas fa-check-circle');
                    statusContainer.html('<span class="ping-pulse-badge ping-online" title="Just now"><i class="fas fa-circle text-xs"></i> Online</span>');
                    if (window.showHallmarkToast) {
                        window.showHallmarkToast('success', 'Ping Success! Device is Online', 'Perangkat ' + (response.ip_address || '') + ' terhubung & merespons dengan cepat.');
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            icon: 'success',
                            title: "{{ __('messages.ping_online') ?? 'Ping Successful! Device is Online.' }}"
                        });
                    }
                } else {
                    icon.attr('class', 'fas fa-times-circle');
                    statusContainer.html('<span class="ping-pulse-badge ping-offline" title="Just now"><i class="fas fa-circle text-xs"></i> Offline</span>');
                    if (window.showHallmarkToast) {
                        window.showHallmarkToast('error', 'Ping Failed! Device is Offline', 'Perangkat ' + (response.ip_address || '') + ' tidak memberikan tanggapan (Offline).');
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            icon: 'error',
                            title: "{{ __('messages.ping_offline') ?? 'Ping Failed! Device is Offline.' }}"
                        });
                    }
                }
                setTimeout(function () {
                    icon.attr('class', originalClass);
                }, 4000);
            },
            error: function (xhr) {
                button.prop('disabled', false);
                icon.attr('class', 'fas fa-exclamation-triangle');
                setTimeout(function () {
                    icon.attr('class', originalClass);
                }, 3000);

                var msg = "{{ __('messages.ping_error') ?? 'Error executing ping command.' }}";
                if (xhr.status === 419) {
                    msg = "Sesi halaman kedaluwarsa (419 CSRF). Silakan refresh halaman.";
                } else if (xhr.status === 403) {
                    msg = "Akses ditolak (403 Forbidden).";
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }

                if (window.showHallmarkToast) {
                    window.showHallmarkToast('warning', 'Ping Warning', msg);
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000,
                        icon: 'warning',
                        title: msg
                    });
                }
            }
        });
    });

    // Helper to update individual IP status badge in real-time
    function updateIpBadge(ipId, isOnline, lastPingAt) {
        var statusContainer = $('.ping-status-badge-' + ipId);
        if (!statusContainer.length) return;

        var titleAttr = lastPingAt ? 'Last ping: ' + lastPingAt : 'Just now';
        var newBadgeHtml = '';

        if (isOnline === true) {
            newBadgeHtml = '<span class="ping-pulse-badge ping-online" title="' + titleAttr + '"><i class="fas fa-circle text-xs"></i> Online</span>';
        } else if (isOnline === false) {
            newBadgeHtml = '<span class="ping-pulse-badge ping-offline" title="' + titleAttr + '"><i class="fas fa-circle text-xs"></i> Offline</span>';
        } else {
            newBadgeHtml = '<span class="ping-pulse-badge ping-unchecked" title="Unchecked"><i class="far fa-circle text-xs"></i> Unchecked</span>';
        }

        statusContainer.html(newBadgeHtml);
    }

    // Live status polling every 15s for automatic background status updates
    setInterval(function() {
        var visibleIds = [];
        $('.ip-row').each(function() {
            var id = $(this).data('ip-id');
            if (id && visibleIds.indexOf(id) === -1) {
                visibleIds.push(id);
            }
        });

        if (visibleIds.length > 0) {
            $.ajax({
                url: '{{ route("ips.live-status") }}',
                method: 'GET',
                data: { ip_ids: visibleIds },
                success: function(res) {
                    if (res.success && res.ips) {
                        res.ips.forEach(function(item) {
                            updateIpBadge(item.id, item.is_online, item.last_ping_at);
                        });
                    }
                }
            });
        }
    }, 15000);

    // Status Change Dropdown via AJAX
    $(document).on('click', '.status-change-btn', function (e) {
        e.preventDefault();
        var item = $(this);
        var newStatus = item.data('status');
        var dropdown = item.closest('.dropdown');
        var button = dropdown.find('.status-btn');
        var ipId = button.data('id');
        var statusBadge = button.find('.status-badge');

        var oldHtml = statusBadge.html();
        statusBadge.html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: '{{ url("ips") }}/' + ipId + '/status',
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function (response) {
                var badgeClass = '';
                var iconClass = '';
                if (newStatus === 'Available') {
                    badgeClass = 'alloc-available';
                    iconClass = 'fas fa-check';
                } else if (newStatus === 'Used') {
                    badgeClass = 'alloc-used';
                    iconClass = 'fas fa-laptop';
                } else {
                    badgeClass = 'alloc-reserved';
                    iconClass = 'fas fa-lock';
                }

                statusBadge.attr('class', 'status-pill-alloc status-badge ' + badgeClass);
                statusBadge.html('<i class="' + iconClass + ' mr-1"></i> ' + newStatus);

                if (window.showHallmarkToast) {
                    window.showHallmarkToast('success', 'Status Updated', 'Status alokasi IP ' + (response.ip_address || '') + ' diubah ke ' + newStatus + '.');
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2500,
                        icon: 'success',
                        title: 'Status: ' + newStatus
                    });
                }
            },
            error: function (xhr) {
                statusBadge.html(oldHtml);
                var errorMsg = 'Gagal mengubah status IP.';
                if (xhr.status === 403) {
                    errorMsg = 'Akses ditolak: Anda tidak memiliki izin mengelola jaringan.';
                }
                if (window.showHallmarkToast) {
                    window.showHallmarkToast('error', 'Update Failed', errorMsg);
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3500,
                        icon: 'error',
                        title: errorMsg
                    });
                }
            }
        });
    });
});
</script>
@endpush