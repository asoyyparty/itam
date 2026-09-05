@extends('layouts.admin')

@section('title', __('messages.vlan_management'))

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
    --hl-accent-indigo-light: #818cf8;
    --hl-accent-cyan: #06b6d4;
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
    color: #818cf8;
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
    background: #6366f1;
    box-shadow: 0 0 8px #6366f1;
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
    padding: 9px 18px;
    border-radius: 10px;
    font-size: 0.84rem;
    font-weight: 700;
    color: #ffffff !important;
    background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    border: 1px solid rgba(129, 140, 248, 0.4);
    box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    text-decoration: none !important;
    white-space: nowrap;
}
.btn-tactile-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(79, 70, 229, 0.5);
    filter: brightness(1.08);
}

/* Bento KPI Grid */
.vlan-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 18px;
}

.vlan-kpi-card {
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
body.light-mode .vlan-kpi-card {
    background: #ffffff;
    border-color: #e2e8f0;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
}

.vlan-kpi-icon-box {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.vlan-kpi-val {
    font-family: var(--hl-font-mono);
    font-size: 1.35rem;
    font-weight: 800;
    line-height: 1.1;
    color: var(--hl-text-bright);
}
body.light-mode .vlan-kpi-val {
    color: #0f172a;
}

.vlan-kpi-label {
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
.vlan-console-header {
    padding: 16px 18px;
    border-bottom: 1px solid var(--hl-border-subtle);
    background: rgba(15, 23, 42, 0.4);
    display: flex;
    flex-direction: column;
    gap: 12px;
}
body.light-mode .vlan-console-header {
    background: #f8fafc;
    border-bottom-color: #e2e8f0;
}

/* Status Quick Ribbon */
.vlan-ribbon-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    padding-bottom: 2px;
}
.vlan-ribbon-wrapper::-webkit-scrollbar {
    height: 3px;
}
.vlan-ribbon-wrapper::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.2);
    border-radius: 4px;
}
.vlan-status-ribbon {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    min-width: max-content;
}
.vlan-ribbon-pill {
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
.vlan-ribbon-pill:hover {
    color: var(--hl-text-bright);
    border-color: var(--hl-border-active);
    background: rgba(30, 41, 59, 0.8);
}
.vlan-ribbon-pill.active {
    background: rgba(99, 102, 241, 0.18);
    border-color: rgba(99, 102, 241, 0.45);
    color: #818cf8;
    font-weight: 700;
}
body.light-mode .vlan-ribbon-pill {
    background: #ffffff;
    border-color: #e2e8f0;
    color: #64748b;
}
body.light-mode .vlan-ribbon-pill:hover {
    background: #f1f5f9;
    color: #0f172a;
}
body.light-mode .vlan-ribbon-pill.active {
    background: rgba(99, 102, 241, 0.1);
    border-color: #6366f1;
    color: #4f46e5;
}

/* Sleek Search Controls */
.vlan-search-row {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.vlan-search-wrapper {
    position: relative;
    flex: 1 1 260px;
    min-width: 0;
}
.vlan-search-input {
    width: 100%;
    height: 40px;
    padding-left: 36px;
    padding-right: 36px;
    border-radius: 10px;
    border: 1px solid var(--hl-border-active);
    background: rgba(15, 23, 42, 0.6);
    color: var(--hl-text-bright);
    font-size: 0.84rem;
    transition: all 0.2s;
}
.vlan-search-input:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
    background: rgba(15, 23, 42, 0.85);
}
body.light-mode .vlan-search-input {
    background: #ffffff;
    border-color: #cbd5e1;
    color: #0f172a;
}
.vlan-search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 0.82rem;
    color: var(--hl-text-dim);
    pointer-events: none;
}
.vlan-clear-btn {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 0.8rem;
    color: var(--hl-text-dim);
    transition: color 0.15s;
    text-decoration: none !important;
}
.vlan-clear-btn:hover {
    color: var(--hl-accent-crimson);
}

/* Data Table Styling */
.vlan-table {
    width: 100%;
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
}
.vlan-table th {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--hl-text-muted);
    background: rgba(15, 23, 42, 0.7);
    padding: 12px 16px;
    border-bottom: 1px solid var(--hl-border-subtle);
    border-top: none;
    white-space: nowrap;
}
body.light-mode .vlan-table th {
    background: #f8fafc;
    border-bottom-color: #e2e8f0;
    color: #475569;
}
.vlan-table td {
    padding: 13px 16px;
    vertical-align: middle;
    border-top: 1px solid var(--hl-border-subtle);
    color: var(--hl-text-bright);
    font-size: 0.85rem;
    transition: background-color 0.15s;
}
body.light-mode .vlan-table td {
    border-top-color: #f1f5f9;
    color: #1e293b;
}
.vlan-table tbody tr:hover td {
    background: rgba(99, 102, 241, 0.04);
}
body.light-mode .vlan-table tbody tr:hover td {
    background: rgba(99, 102, 241, 0.03);
}

/* Specialized Table Cell Components */
.vlan-id-badge {
    font-family: var(--hl-font-mono);
    font-size: 0.84rem;
    font-weight: 700;
    padding: 4px 10px;
    background: rgba(99, 102, 241, 0.14);
    border: 1px solid rgba(99, 102, 241, 0.35);
    color: #818cf8;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
}

.mono-ip {
    font-family: var(--hl-font-mono);
    font-size: 0.82rem;
    font-weight: 600;
    font-variant-numeric: tabular-nums;
}

.vlan-ip-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 999px;
    background: rgba(6, 182, 212, 0.12);
    border: 1px solid rgba(6, 182, 212, 0.3);
    color: #38bdf8;
    font-weight: 700;
    font-size: 0.78rem;
    font-family: var(--hl-font-mono);
    text-decoration: none !important;
    transition: all 0.2s;
}
.vlan-ip-pill:hover {
    background: rgba(6, 182, 212, 0.24);
    border-color: rgba(6, 182, 212, 0.5);
    color: #7dd3fc;
    transform: translateY(-1px);
}

.status-badge {
    font-size: 0.74rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 8px;
    letter-spacing: 0.02em;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.status-badge-active {
    background: rgba(16, 185, 129, 0.14);
    color: #34d399;
    border: 1px solid rgba(16, 185, 129, 0.3);
}
.status-badge-inactive {
    background: rgba(244, 63, 94, 0.14);
    color: #fb7185;
    border: 1px solid rgba(244, 63, 94, 0.3);
}

.action-btn-group {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.action-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    border: 1px solid var(--hl-border-subtle);
    background: rgba(15, 23, 42, 0.6);
    color: var(--hl-text-muted);
    transition: all 0.15s;
    text-decoration: none !important;
}
.action-btn:hover {
    transform: translateY(-1px);
}
.action-btn-edit:hover {
    background: rgba(245, 158, 11, 0.18);
    border-color: rgba(245, 158, 11, 0.4);
    color: #fbbf24;
}
.action-btn-delete:hover {
    background: rgba(244, 63, 94, 0.18);
    border-color: rgba(244, 63, 94, 0.4);
    color: #fb7185;
}
body.light-mode .action-btn {
    background: #ffffff;
    border-color: #cbd5e1;
    color: #64748b;
}

/* Empty State */
.vlan-empty-state {
    padding: 48px 24px;
    text-align: center;
}
.vlan-empty-icon {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    background: rgba(99, 102, 241, 0.1);
    border: 1px solid rgba(99, 102, 241, 0.25);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: #818cf8;
    margin-bottom: 14px;
}

/* Mobile Responsiveness */
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
        width: 100% !important;
    }
    .master-banner-actions .btn-tactile-primary {
        width: 100% !important;
        height: 42px !important;
    }
    .vlan-kpi-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        margin-bottom: 14px;
    }
    .vlan-kpi-card {
        padding: 10px 12px;
        border-radius: 12px;
        gap: 8px;
    }
    .vlan-kpi-icon-box {
        width: 32px;
        height: 32px;
        font-size: 0.85rem;
    }
    .vlan-kpi-val {
        font-size: 1.15rem;
    }
    .vlan-kpi-label {
        font-size: 0.65rem;
    }
    .vlan-console-header {
        padding: 12px 14px;
        gap: 10px;
    }
    .vlan-search-row {
        flex-direction: column;
        align-items: stretch;
    }
    .vlan-search-wrapper {
        width: 100%;
        flex: 0 0 auto;
    }
}
</style>

@php
    $totalVlans = $vlans->count();
    $activeVlans = $vlans->where('status', 'Active')->count();
    $inactiveVlans = $vlans->where('status', 'Inactive')->count();
    $totalBoundIps = $vlans->sum('ip_addresses_count');
@endphp

<!-- Master Header Banner -->
<div class="master-header-banner">
    <div class="master-banner-top">
        <div class="master-banner-title-box">
            <div class="master-banner-icon">
                <i class="fas fa-sitemap"></i>
            </div>
            <div>
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <h4 class="mb-0 font-weight-bold" style="font-size: 1.12rem; color: var(--hl-text-bright); line-height: 1.2;">
                        {{ __('messages.vlan_management') }}
                    </h4>
                    <span class="master-count-pill">
                        <span class="live-dot-indigo"></span>
                        {{ $totalVlans }} {{ __('messages.vlans') }}
                    </span>
                </div>
                <small style="color: var(--hl-text-muted); font-size: 0.78rem; display: block; margin-top: 2px;">
                    {{ __('messages.vlan_subtitle') }}
                </small>
            </div>
        </div>
        @can('action_manage_network')
        <div class="master-banner-actions">
            <a href="{{ route('vlans.create') }}" class="btn-tactile-primary">
                <i class="fas fa-plus mr-1"></i> {{ __('messages.add_vlan') }}
            </a>
        </div>
        @endcan
    </div>
</div>

<!-- Bento KPI Stat Strip -->
<div class="vlan-kpi-grid">
    <!-- Total VLANs -->
    <div class="vlan-kpi-card">
        <div class="vlan-kpi-icon-box" style="background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3);">
            <i class="fas fa-sitemap"></i>
        </div>
        <div style="min-width: 0;">
            <div class="vlan-kpi-val">{{ $totalVlans }}</div>
            <div class="vlan-kpi-label">{{ __('messages.total_vlans') }}</div>
        </div>
    </div>
    <!-- Active VLANs -->
    <div class="vlan-kpi-card">
        <div class="vlan-kpi-icon-box" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3);">
            <i class="fas fa-check-circle"></i>
        </div>
        <div style="min-width: 0;">
            <div class="vlan-kpi-val" style="color: var(--hl-accent-emerald);">{{ $activeVlans }}</div>
            <div class="vlan-kpi-label">{{ __('messages.active_vlans') }}</div>
        </div>
    </div>
    <!-- Inactive VLANs -->
    <div class="vlan-kpi-card">
        <div class="vlan-kpi-icon-box" style="background: rgba(244, 63, 94, 0.15); color: #fb7185; border: 1px solid rgba(244, 63, 94, 0.3);">
            <i class="fas fa-ban"></i>
        </div>
        <div style="min-width: 0;">
            <div class="vlan-kpi-val" style="color: var(--hl-accent-crimson);">{{ $inactiveVlans }}</div>
            <div class="vlan-kpi-label">{{ __('messages.inactive_vlans') }}</div>
        </div>
    </div>
    <!-- Bound IPs -->
    <div class="vlan-kpi-card">
        <div class="vlan-kpi-icon-box" style="background: rgba(6, 182, 212, 0.15); color: #38bdf8; border: 1px solid rgba(6, 182, 212, 0.3);">
            <i class="fas fa-network-wired"></i>
        </div>
        <div style="min-width: 0;">
            <div class="vlan-kpi-val" style="color: var(--hl-accent-cyan);">{{ $totalBoundIps }}</div>
            <div class="vlan-kpi-label">{{ __('messages.total_bound_ips') }}</div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; border-radius: 12px;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="icon fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; border-radius: 12px;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="icon fas fa-ban mr-2"></i> {{ session('error') }}
    </div>
@endif

<!-- Main Console Card -->
<div class="master-table-card">
    <!-- Tactical Filter Console Header -->
    <div class="vlan-console-header">
        <!-- Status Quick Ribbon -->
        <div class="vlan-ribbon-wrapper">
            <div class="vlan-status-ribbon">
                <a href="{{ route('vlans.index', array_merge(request()->except('status', 'page'))) }}" 
                   class="vlan-ribbon-pill {{ !request('status') ? 'active' : '' }}">
                    <i class="fas fa-th-list"></i>
                    {{ __('messages.all_status') }}
                </a>
                <a href="{{ route('vlans.index', array_merge(request()->except('status', 'page'), ['status' => 'Active'])) }}" 
                   class="vlan-ribbon-pill {{ request('status') === 'Active' ? 'active' : '' }}">
                    <span class="live-dot-indigo" style="background: #10b981; box-shadow: 0 0 6px #10b981; width: 6px; height: 6px;"></span>
                    {{ __('messages.active') }} ({{ $activeVlans }})
                </a>
                <a href="{{ route('vlans.index', array_merge(request()->except('status', 'page'), ['status' => 'Inactive'])) }}" 
                   class="vlan-ribbon-pill {{ request('status') === 'Inactive' ? 'active' : '' }}">
                    <span class="live-dot-indigo" style="background: #f43f5e; box-shadow: 0 0 6px #f43f5e; width: 6px; height: 6px;"></span>
                    {{ __('messages.inactive') }} ({{ $inactiveVlans }})
                </a>
            </div>
        </div>

        <!-- Search Bar Row -->
        <form action="{{ route('vlans.index') }}" method="GET" class="m-0">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="vlan-search-row">
                <div class="vlan-search-wrapper">
                    <i class="fas fa-search vlan-search-icon"></i>
                    <input type="text" name="search" class="vlan-search-input" 
                           placeholder="{{ __('messages.search_vlan_placeholder') }}" 
                           value="{{ request('search') }}" autocomplete="off">
                    @if(request('search'))
                        <a href="{{ route('vlans.index', request()->except('search', 'page')) }}" class="vlan-clear-btn" title="{{ __('messages.reset_search') }}">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Table Body -->
    <div class="table-responsive p-0" style="overflow-x: auto;">
        <table class="vlan-table">
            <thead>
                <tr>
                    <th width="50" class="text-center">{{ __('messages.no') }}</th>
                    <th width="120">{{ __('messages.vlan_number') }}</th>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.subnet') }}</th>
                    <th>{{ __('messages.gateway') }}</th>
                    <th class="text-center">{{ __('messages.ip_allocation') }}</th>
                    <th class="text-center" width="110">{{ __('messages.status') }}</th>
                    <th>{{ __('messages.notes') }}</th>
                    <th width="110" class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vlans as $vlan)
                <tr>
                    <td class="text-center text-muted mono-ip" style="font-size: 0.8rem;">
                        {{ (method_exists($vlans, 'firstItem') && $vlans->firstItem() ? $vlans->firstItem() + $loop->index : $loop->iteration) }}
                    </td>
                    <td>
                        <span class="vlan-id-badge">
                            VLAN {{ $vlan->vlan_number }}
                        </span>
                    </td>
                    <td class="font-weight-bold" style="color: var(--hl-text-bright);">
                        {{ $vlan->name }}
                    </td>
                    <td>
                        @if($vlan->subnet)
                            <span class="mono-ip" style="color: var(--hl-accent-cyan);">{{ $vlan->subnet }}</span>
                        @else
                            <span style="color: var(--hl-text-dim);">—</span>
                        @endif
                    </td>
                    <td>
                        @if($vlan->gateway)
                            <span class="mono-ip" style="color: var(--hl-accent-emerald);">{{ $vlan->gateway }}</span>
                        @else
                            <span style="color: var(--hl-text-dim);">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('ips.index', ['vlan_id' => $vlan->id]) }}" 
                           class="vlan-ip-pill" 
                           title="{{ __('messages.view_vlan_ips') }}">
                            <i class="fas fa-network-wired"></i>
                            {{ $vlan->ip_addresses_count ?? 0 }} IP
                        </a>
                    </td>
                    <td class="text-center">
                        <div class="dropdown d-inline-block">
                            @can('action_manage_network')
                            <button class="btn btn-sm dropdown-toggle status-btn p-0 border-0 bg-transparent" 
                                    type="button" data-toggle="dropdown" aria-expanded="false" 
                                    data-id="{{ $vlan->id }}" style="box-shadow: none;">
                                @if($vlan->status == 'Active')
                                    <span class="status-badge status-badge-active">
                                        <span class="live-dot-indigo" style="background: #10b981; box-shadow: 0 0 6px #10b981; width: 5px; height: 5px;"></span>
                                        {{ __('messages.active') }}
                                    </span>
                                @else
                                    <span class="status-badge status-badge-inactive">
                                        <span class="live-dot-indigo" style="background: #f43f5e; box-shadow: 0 0 6px #f43f5e; width: 5px; height: 5px;"></span>
                                        {{ __('messages.inactive') }}
                                    </span>
                                @endif
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" style="background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
                                <a class="dropdown-item status-change-btn text-success font-weight-bold" href="#" data-status="Active">
                                    <i class="fas fa-check-circle mr-2"></i>{{ __('messages.active') }}
                                </a>
                                <a class="dropdown-item status-change-btn text-danger font-weight-bold" href="#" data-status="Inactive">
                                    <i class="fas fa-ban mr-2"></i>{{ __('messages.inactive') }}
                                </a>
                            </div>
                            @else
                                @if($vlan->status == 'Active')
                                    <span class="status-badge status-badge-active">{{ __('messages.active') }}</span>
                                @else
                                    <span class="status-badge status-badge-inactive">{{ __('messages.inactive') }}</span>
                                @endif
                            @endcan
                        </div>
                    </td>
                    <td style="font-size: 0.82rem; color: var(--hl-text-muted); max-width: 220px;" class="text-truncate">
                        {{ $vlan->notes ?? $vlan->description ?? '—' }}
                    </td>
                    <td class="text-center">
                        <div class="action-btn-group">
                            @can('action_manage_network')
                            <a href="{{ route('vlans.edit', array_merge([$vlan->id], request()->query())) }}" 
                               class="action-btn action-btn-edit" 
                               title="{{ __('messages.edit') }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('vlans.destroy', array_merge([$vlan->id], request()->query())) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn action-btn-delete btn-delete" 
                                        title="{{ __('messages.delete') }}" 
                                        data-confirm-message="{{ __('messages.confirm_delete') }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="vlan-empty-state">
                            <div class="vlan-empty-icon">
                                <i class="fas fa-sitemap"></i>
                            </div>
                            <h6 class="font-weight-bold" style="color: var(--hl-text-bright); margin-bottom: 6px;">
                                {{ __('messages.no_data') }}
                            </h6>
                            <p style="color: var(--hl-text-dim); font-size: 0.84rem; max-width: 400px; margin: 0 auto 16px auto;">
                                {{ __('messages.no_vlans_match') }}
                            </p>
                            @if(request()->anyFilled(['search', 'status']))
                                <a href="{{ route('vlans.index') }}" class="btn-tactile-primary" style="padding: 6px 14px; font-size: 0.8rem;">
                                    <i class="fas fa-undo mr-1"></i> {{ __('messages.reset_filter') }}
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $(document).on('click', '.status-change-btn', function(e) {
        e.preventDefault();
        var btn = $(this);
        var newStatus = btn.data('status');
        var container = btn.closest('.dropdown');
        var id = container.find('.status-btn').data('id');
        var button = container.find('.status-btn');
        var badge = container.find('.status-badge');
        
        var originalHtml = badge.html();
        badge.html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: '{{ url("vlans") }}/' + id + '/status',
            type: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                if(response.success) {
                    if (newStatus === 'Active') {
                        badge.removeClass('status-badge-inactive').addClass('status-badge-active');
                        badge.html('<span class="live-dot-indigo" style="background: #10b981; box-shadow: 0 0 6px #10b981; width: 5px; height: 5px;"></span> Active');
                    } else {
                        badge.removeClass('status-badge-active').addClass('status-badge-inactive');
                        badge.html('<span class="live-dot-indigo" style="background: #f43f5e; box-shadow: 0 0 6px #f43f5e; width: 5px; height: 5px;"></span> Inactive');
                    }
                }
            },
            error: function(xhr) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ 
                        icon: 'error', 
                        text: 'Gagal memperbarui status VLAN.', 
                        toast: true, 
                        position: 'top-end', 
                        showConfirmButton: false, 
                        timer: 3000, 
                        background: '#0f172a', 
                        color: '#f8fafc', 
                        customClass: { popup: 'border border-danger' } 
                    });
                }
                badge.html(originalHtml);
            }
        });
    });
});
</script>
<style>
.status-btn::after {
    display: none !important;
}
</style>
@endpush
@endsection
