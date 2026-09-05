@extends('layouts.admin')

@section('title', __('messages.predictive_health_title'))

@section('content')
<style>
/* Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 */
:root {
    --hl-bg-canvas: #090d16;
    --hl-bg-surface: #0f172a;
    --hl-bg-card: rgba(15, 23, 42, 0.75);
    --hl-bg-card-hover: rgba(30, 41, 59, 0.85);
    --hl-border-subtle: rgba(148, 163, 184, 0.12);
    --hl-border-active: rgba(148, 163, 184, 0.28);
    --hl-text-bright: #f8fafc;
    --hl-text-muted: #94a3b8;
    --hl-text-dim: #64748b;
    --hl-accent-cyan: #06b6d4;
    --hl-accent-cyan-glow: rgba(6, 182, 212, 0.2);
    --hl-accent-indigo: #6366f1;
    --hl-status-healthy: #10b981;
    --hl-status-healthy-glow: rgba(16, 185, 129, 0.2);
    --hl-status-warning: #f59e0b;
    --hl-status-warning-glow: rgba(245, 158, 11, 0.2);
    --hl-status-critical: #f43f5e;
    --hl-status-critical-glow: rgba(244, 63, 94, 0.22);
    --hl-font-mono: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}

/* Command Center Container */
.telemetry-dashboard {
    width: 100%;
    margin-bottom: 2rem;
    overflow-x: clip;
}

/* Telemetry Header Banner */
.telemetry-banner {
    position: relative;
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.8) 100%);
    border: 1px solid var(--hl-border-subtle);
    border-radius: 16px;
    padding: 20px 24px;
    margin-bottom: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    backdrop-filter: blur(12px);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}

.telemetry-banner-title {
    display: flex;
    align-items: center;
    gap: 14px;
}

.telemetry-banner-icon {
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

.telemetry-badge-pulse {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-family: var(--hl-font-mono);
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--hl-accent-cyan);
    background: rgba(6, 182, 212, 0.12);
    border: 1px solid rgba(6, 182, 212, 0.3);
    padding: 4px 10px;
    border-radius: 9999px;
}

.pulse-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background-color: var(--hl-accent-cyan);
    box-shadow: 0 0 8px var(--hl-accent-cyan);
    animation: pulse-ring 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
}

@keyframes pulse-ring {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(0.85); }
}

/* Bento KPI Grid */
.bento-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.bento-kpi-card {
    background: var(--hl-bg-card);
    border: 1px solid var(--hl-border-subtle);
    border-radius: 14px;
    padding: 18px 20px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s, box-shadow 0.2s;
    backdrop-filter: blur(8px);
}

.bento-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
}

.bento-kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: transparent;
    transition: background 0.2s;
}

.bento-kpi-card.kpi-total::before { background: var(--hl-accent-cyan); }
.bento-kpi-card.kpi-critical::before { background: var(--hl-status-critical); }
.bento-kpi-card.kpi-warning::before { background: var(--hl-status-warning); }
.bento-kpi-card.kpi-budget::before { background: var(--hl-status-healthy); }

.kpi-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.kpi-card-label {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--hl-text-muted);
}

.kpi-card-icon {
    font-size: 1.1rem;
    opacity: 0.85;
}

.kpi-card-value {
    font-family: var(--hl-font-mono);
    font-size: 1.75rem;
    font-weight: 800;
    line-height: 1.1;
    color: var(--hl-text-bright);
    margin-bottom: 8px;
    letter-spacing: -0.02em;
}

.kpi-card-desc {
    font-size: 0.76rem;
    color: var(--hl-text-dim);
    line-height: 1.4;
    margin: 0;
}

/* Status Ribbon */
.status-ribbon-wrapper {
    width: 100%;
    margin-bottom: 18px;
    overflow: hidden;
}

.status-ribbon-scroll {
    display: flex;
    align-items: center;
    gap: 10px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    padding-bottom: 4px;
    scrollbar-width: none;
}

.status-ribbon-scroll::-webkit-scrollbar {
    display: none;
}

.ribbon-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 16px;
    border-radius: 9999px;
    font-size: 0.8rem;
    font-weight: 600;
    white-space: nowrap;
    text-decoration: none !important;
    border: 1px solid var(--hl-border-subtle);
    background: rgba(15, 23, 42, 0.6);
    color: var(--hl-text-muted);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    flex-shrink: 0;
}

.ribbon-pill:hover {
    transform: translateY(-1px);
    color: var(--hl-text-bright);
    border-color: var(--hl-border-active);
    background: rgba(30, 41, 59, 0.8);
}

.ribbon-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 2px 7px;
    border-radius: 9999px;
    font-family: var(--hl-font-mono);
    font-size: 0.7rem;
    font-weight: 700;
    background: rgba(255, 255, 255, 0.08);
}

.ribbon-pill.active-all {
    background: rgba(6, 182, 212, 0.15);
    border-color: rgba(6, 182, 212, 0.45);
    color: #67e8f9;
    box-shadow: 0 0 14px var(--hl-accent-cyan-glow);
}
.ribbon-pill.active-all .ribbon-count {
    background: rgba(6, 182, 212, 0.35);
    color: #ffffff;
}

.ribbon-pill.active-critical {
    background: var(--hl-status-critical-glow);
    border-color: rgba(244, 63, 94, 0.45);
    color: #fda4af;
    box-shadow: 0 0 14px var(--hl-status-critical-glow);
}
.ribbon-pill.active-critical .ribbon-count {
    background: rgba(244, 63, 94, 0.4);
    color: #ffffff;
}

.ribbon-pill.active-warning {
    background: var(--hl-status-warning-glow);
    border-color: rgba(245, 158, 11, 0.45);
    color: #fde68a;
    box-shadow: 0 0 14px var(--hl-status-warning-glow);
}
.ribbon-pill.active-warning .ribbon-count {
    background: rgba(245, 158, 11, 0.4);
    color: #ffffff;
}

.ribbon-pill.active-healthy {
    background: var(--hl-status-healthy-glow);
    border-color: rgba(16, 185, 129, 0.45);
    color: #6ee7b7;
    box-shadow: 0 0 14px var(--hl-status-healthy-glow);
}
.ribbon-pill.active-healthy .ribbon-count {
    background: rgba(16, 185, 129, 0.4);
    color: #ffffff;
}

/* Fleet Telemetry Container Card */
.fleet-telemetry-panel {
    background: var(--hl-bg-card);
    border: 1px solid var(--hl-border-subtle);
    border-radius: 16px;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.25);
    backdrop-filter: blur(12px);
    overflow: hidden;
}

.fleet-panel-header {
    padding: 18px 24px;
    border-bottom: 1px solid var(--hl-border-subtle);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}

.fleet-panel-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--hl-text-bright);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
    letter-spacing: 0.02em;
}

/* Telemetry Table (Desktop View) */
.telemetry-table-wrapper {
    max-height: calc(100vh - 240px);
    overflow-y: auto;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.telemetry-table-wrapper::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.telemetry-table-wrapper::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.2);
}
.telemetry-table-wrapper::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.25);
    border-radius: 4px;
}

.telemetry-table {
    width: 100%;
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
}

.telemetry-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #0f172a !important;
    border-bottom: 1px solid var(--hl-border-active) !important;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--hl-text-muted);
    padding: 14px 18px;
    white-space: nowrap;
}

.telemetry-table tbody td {
    padding: 14px 18px;
    vertical-align: middle;
    border-bottom: 1px solid var(--hl-border-subtle);
    font-size: 0.85rem;
}

.telemetry-table tbody tr {
    transition: background-color 0.15s ease;
}

.telemetry-table tbody tr:hover {
    background-color: rgba(30, 41, 59, 0.4) !important;
}

/* Calibrated Health Gauge */
.gauge-metric-container {
    min-width: 190px;
}

.gauge-readout {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
}

.gauge-score {
    font-family: var(--hl-font-mono);
    font-weight: 800;
    font-size: 0.95rem;
}

.gauge-track {
    width: 100%;
    height: 6px;
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.08);
    overflow: hidden;
    position: relative;
}

.gauge-fill {
    height: 100%;
    border-radius: 9999px;
    transition: width 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

/* Tactile AI Audit Action Button */
.btn-telemetry-audit {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-family: var(--hl-font-mono);
    font-size: 0.76rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    padding: 6px 14px;
    border-radius: 9999px;
    background: rgba(6, 182, 212, 0.12);
    color: #67e8f9 !important;
    border: 1px solid rgba(6, 182, 212, 0.35);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    white-space: nowrap;
    cursor: pointer;
}

.btn-telemetry-audit:hover {
    background: rgba(6, 182, 212, 0.22);
    border-color: rgba(6, 182, 212, 0.6);
    box-shadow: 0 0 14px var(--hl-accent-cyan-glow);
    transform: translateY(-1px);
}

/* Mobile Bento Cards */
.mobile-bento-card {
    background: var(--hl-bg-card);
    border: 1px solid var(--hl-border-subtle);
    border-radius: 14px;
    padding: 16px;
    margin-bottom: 14px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
    transition: transform 0.15s, border-color 0.15s;
}

.mobile-bento-card:hover {
    border-color: var(--hl-border-active);
}

.mobile-bento-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 12px;
}

.mobile-bento-title {
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--hl-text-bright);
    margin: 0 0 4px 0;
    overflow-wrap: anywhere;
    min-width: 0;
}

.mobile-bento-meta {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 10px;
    padding: 10px 12px;
    margin: 12px 0;
}

.mobile-meta-item {
    min-width: 0;
}

.mobile-meta-label {
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--hl-text-dim);
    margin-bottom: 2px;
}

.mobile-meta-value {
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--hl-text-bright);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* AI Diagnostic Modal Telemetry Styling */
.modal-telemetry-content {
    background: #0f172a;
    border: 1px solid var(--hl-border-active);
    border-radius: 16px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
    overflow: hidden;
}

.modal-telemetry-header {
    background: rgba(15, 23, 42, 0.9);
    border-bottom: 1px solid var(--hl-border-subtle);
    padding: 16px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.diagnostic-overview-tile {
    background: rgba(30, 41, 59, 0.5);
    border: 1px solid var(--hl-border-subtle);
    border-radius: 12px;
    padding: 18px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
}

.diagnostic-score-box {
    text-align: right;
    flex-shrink: 0;
}

.diagnostic-score-val {
    font-family: var(--hl-font-mono);
    font-size: 2.3rem;
    font-weight: 800;
    line-height: 1;
}

.diagnostic-reasons-container {
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid var(--hl-border-subtle);
    border-radius: 12px;
    padding: 12px 16px;
    margin-bottom: 20px;
}

.diagnostic-recommendation-tile {
    background: rgba(6, 182, 212, 0.08);
    border: 1px solid rgba(6, 182, 212, 0.25);
    border-radius: 12px;
    padding: 16px 18px;
}

/* Responsive Rules (320px, 375px, 414px, 768px, 1200px) */
@media (max-width: 1199.98px) {
    .bento-kpi-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 767.98px) {
    .telemetry-dashboard {
        margin-bottom: 1.25rem;
    }

    .telemetry-banner {
        padding: 16px;
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }

    .bento-kpi-grid {
        grid-template-columns: minmax(0, 1fr);
        gap: 12px;
    }

    .bento-kpi-card {
        padding: 14px 16px;
    }

    .kpi-card-value {
        font-size: 1.45rem;
    }

    .diagnostic-overview-tile {
        flex-direction: column;
        align-items: flex-start;
        padding: 14px;
        gap: 14px;
    }

    .diagnostic-score-box {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 10px;
        border-top: 1px dashed var(--hl-border-subtle);
    }

    .diagnostic-score-val {
        font-size: 1.85rem;
    }
}
</style>

<div class="telemetry-dashboard">
    <!-- Telemetry Header Banner -->
    <div class="telemetry-banner">
        <div class="telemetry-banner-title">
            <div class="telemetry-banner-icon">
                <i class="fas fa-heartbeat"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <h5 class="m-0 font-weight-bold" style="font-size: 1.15rem; color: var(--hl-text-bright); line-height: 1.2;">
                        {{ __('messages.predictive_health_title') }}
                    </h5>
                    <span class="telemetry-badge-pulse">
                        <span class="pulse-dot"></span>
                        {{ __('messages.live_telemetry') }}
                    </span>
                </div>
                <small style="font-family: var(--hl-font-mono); font-size: 0.74rem; color: var(--hl-text-dim);">
                    {{ __('messages.predictive_telemetry_sub') }}
                </small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge px-3 py-2" style="background: rgba(255, 255, 255, 0.05); border: 1px solid var(--hl-border-subtle); color: var(--hl-text-muted); font-size: 0.76rem; font-family: var(--hl-font-mono);">
                <i class="fas fa-clock mr-1"></i> {{ now()->format('d M Y · H:i') }}
            </span>
        </div>
    </div>

    <!-- Bento KPI Matrix -->
    <div class="bento-kpi-grid">
        <!-- 1. Total Analyzed Assets -->
        <div class="bento-kpi-card kpi-total">
            <div>
                <div class="kpi-card-header">
                    <span class="kpi-card-label">{{ __('messages.total_analyzed_assets') }}</span>
                    <i class="fas fa-microchip kpi-card-icon" style="color: var(--hl-accent-cyan);"></i>
                </div>
                <div class="kpi-card-value">
                    {{ $summary['total_assets'] }}
                    <span style="font-size: 0.85rem; font-weight: 500; color: var(--hl-text-dim);">{{ __('messages.unit') }}</span>
                </div>
            </div>
            <p class="kpi-card-desc">{{ __('messages.registered_assets_desc') }}</p>
        </div>

        <!-- 2. Critical Risk Level -->
        <div class="bento-kpi-card kpi-critical">
            <div>
                <div class="kpi-card-header">
                    <span class="kpi-card-label" style="color: var(--hl-status-critical);">{{ __('messages.high_risk_critical') }}</span>
                    <i class="fas fa-exclamation-triangle kpi-card-icon" style="color: var(--hl-status-critical);"></i>
                </div>
                <div class="kpi-card-value" style="color: var(--hl-status-critical);">
                    {{ $summary['critical_count'] }}
                    <span style="font-size: 0.85rem; font-weight: 500; color: var(--hl-text-dim);">{{ __('messages.unit') }}</span>
                </div>
            </div>
            <p class="kpi-card-desc">{{ __('messages.critical_risk_desc') }}</p>
        </div>

        <!-- 3. Warning Level -->
        <div class="bento-kpi-card kpi-warning">
            <div>
                <div class="kpi-card-header">
                    <span class="kpi-card-label" style="color: var(--hl-status-warning);">{{ __('messages.warning_level') }}</span>
                    <i class="fas fa-shield-virus kpi-card-icon" style="color: var(--hl-status-warning);"></i>
                </div>
                <div class="kpi-card-value" style="color: var(--hl-status-warning);">
                    {{ $summary['warning_count'] }}
                    <span style="font-size: 0.85rem; font-weight: 500; color: var(--hl-text-dim);">{{ __('messages.unit') }}</span>
                </div>
            </div>
            <p class="kpi-card-desc">{{ __('messages.warning_risk_desc') }}</p>
        </div>

        <!-- 4. Replacement CapEx Budget -->
        <div class="bento-kpi-card kpi-budget">
            <div>
                <div class="kpi-card-header">
                    <span class="kpi-card-label" style="color: var(--hl-status-healthy);">{{ __('messages.est_replacement_budget') }}</span>
                    <i class="fas fa-coins kpi-card-icon" style="color: var(--hl-status-healthy);"></i>
                </div>
                <div class="kpi-card-value" style="color: var(--hl-status-healthy); font-size: 1.45rem;">
                    Rp {{ number_format($summary['estimated_replacement_budget'], 0, ',', '.') }}
                </div>
            </div>
            <p class="kpi-card-desc">{{ __('messages.est_budget_desc') }}</p>
        </div>
    </div>

    <!-- Status Ribbon (Tactile Tabs) -->
    <div class="status-ribbon-wrapper">
        <div class="status-ribbon-scroll">
            <a href="{{ route('predictive-health.index', ['status' => 'all']) }}" 
               class="ribbon-pill {{ $statusFilter == 'all' ? 'active-all' : '' }}">
                <span>{{ __('messages.all') }}</span>
                <span class="ribbon-count">{{ $summary['total_assets'] }}</span>
            </a>
            <a href="{{ route('predictive-health.index', ['status' => 'Critical']) }}" 
               class="ribbon-pill {{ $statusFilter == 'Critical' ? 'active-critical' : '' }}">
                <span style="color: var(--hl-status-critical);">&#9679;</span>
                <span>{{ __('messages.critical') }}</span>
                <span class="ribbon-count">{{ $summary['critical_count'] }}</span>
            </a>
            <a href="{{ route('predictive-health.index', ['status' => 'Warning']) }}" 
               class="ribbon-pill {{ $statusFilter == 'Warning' ? 'active-warning' : '' }}">
                <span style="color: var(--hl-status-warning);">&#9679;</span>
                <span>{{ __('messages.warning') }}</span>
                <span class="ribbon-count">{{ $summary['warning_count'] }}</span>
            </a>
            <a href="{{ route('predictive-health.index', ['status' => 'Healthy']) }}" 
               class="ribbon-pill {{ $statusFilter == 'Healthy' ? 'active-healthy' : '' }}">
                <span style="color: var(--hl-status-healthy);">&#9679;</span>
                <span>{{ __('messages.healthy') }}</span>
                <span class="ribbon-count">{{ $summary['healthy_count'] }}</span>
            </a>
        </div>
    </div>

    <!-- Fleet Telemetry Matrix Panel -->
    <div class="fleet-telemetry-panel">
        <div class="fleet-panel-header">
            <h6 class="fleet-panel-title">
                <i class="fas fa-layer-group" style="color: var(--hl-accent-cyan);"></i>
                {{ __('messages.asset_health_risk_list') }}
            </h6>
            <div style="font-family: var(--hl-font-mono); font-size: 0.74rem; color: var(--hl-text-dim);">
                {{ __('messages.displaying_nodes_count', ['count' => count($assets)]) }}
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="telemetry-table-wrapper d-none d-md-block">
            <table class="telemetry-table">
                <thead>
                    <tr>
                        <th style="padding-left: 24px;">{{ __('messages.asset_and_tag') }}</th>
                        <th>{{ __('messages.category_and_brand') }}</th>
                        <th>{{ __('messages.assigned_employee') }}</th>
                        <th>{{ __('messages.health_score_header') }}</th>
                        <th>{{ __('messages.remaining_useful_life') }}</th>
                        <th class="text-right" style="padding-right: 24px;">{{ __('messages.ai_audit_action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                        @php $h = $asset->health_data; @endphp
                        <tr>
                            <td style="padding-left: 24px;">
                                <a href="{{ route('assets.show', $asset->id) }}" class="font-weight-bold text-decoration-none" style="color: var(--hl-text-bright);">
                                    {{ $asset->name }}
                                </a>
                                <div class="mt-1">
                                    <span class="badge" style="font-family: var(--hl-font-mono); font-size: 0.72rem; background: rgba(255, 255, 255, 0.07); border: 1px solid var(--hl-border-subtle); color: var(--hl-text-muted);">
                                        {{ $asset->asset_tag }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div style="color: var(--hl-text-bright); font-weight: 500;">
                                    {{ $asset->category->name ?? __('messages.asset') }}
                                </div>
                                <small style="color: var(--hl-text-dim);">{{ $asset->brand->name ?? '-' }}</small>
                            </td>
                            <td>
                                @if($asset->currentAssignment && $asset->currentAssignment->employee)
                                    <div class="d-flex align-items-center gap-1" style="color: var(--hl-text-bright);">
                                        <i class="far fa-user mr-1" style="color: var(--hl-accent-cyan);"></i>
                                        <span>{{ $asset->currentAssignment->employee->name }}</span>
                                    </div>
                                @else
                                    <span class="badge" style="background: rgba(148, 163, 184, 0.1); border: 1px solid var(--hl-border-subtle); color: var(--hl-text-dim); font-size: 0.72rem;">
                                        {{ __('messages.not_assigned') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="gauge-metric-container">
                                    <div class="gauge-readout">
                                        <span class="gauge-score" style="color: {{ $h['text_color'] }};">{{ $h['health_score'] }}%</span>
                                        <span class="badge {{ $h['badge_class'] }}" style="font-size: 0.7rem; padding: 3px 8px; border-radius: 6px;">
                                            {{ $h['status'] }}
                                        </span>
                                    </div>
                                    <div class="gauge-track">
                                        <div class="gauge-fill" style="width: {{ $h['health_score'] }}%; background-color: {{ $h['text_color'] }};"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-family: var(--hl-font-mono); font-weight: 700; color: var(--hl-text-bright); font-size: 0.85rem;">
                                    {{ $h['remaining_life'] }}
                                </div>
                                <small style="color: var(--hl-text-dim);">
                                    {{ __('messages.age') }}: {{ $h['age_years'] }} {{ __('messages.years_short') }}
                                </small>
                            </td>
                            <td class="text-right" style="padding-right: 24px;">
                                <button type="button" class="btn-telemetry-audit btn-audit-asset" data-asset-id="{{ $asset->id }}">
                                    <i class="fas fa-stethoscope"></i>
                                    <span>{{ __('messages.ai_audit') }}</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5" style="color: var(--hl-text-dim);">
                                <i class="fas fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                                {{ __('messages.no_data') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Bento Feed View -->
        <div class="d-block d-md-none p-3" style="max-height: calc(100vh - 240px); overflow-y: auto; -webkit-overflow-scrolling: touch;">
            @forelse($assets as $asset)
                @php $h = $asset->health_data; @endphp
                <div class="mobile-bento-card">
                    <div class="mobile-bento-header">
                        <div style="flex: 1; min-width: 0;">
                            <a href="{{ route('assets.show', $asset->id) }}" class="mobile-bento-title d-block text-truncate text-decoration-none">
                                {{ $asset->name }}
                            </a>
                            <div class="d-flex align-items-center gap-1 flex-wrap mt-1">
                                <span class="badge" style="font-family: var(--hl-font-mono); font-size: 0.68rem; background: rgba(255, 255, 255, 0.08); color: var(--hl-text-muted);">
                                    {{ $asset->asset_tag }}
                                </span>
                                <span class="badge" style="font-size: 0.68rem; background: rgba(148, 163, 184, 0.1); color: var(--hl-text-dim);">
                                    {{ $asset->category->name ?? __('messages.asset') }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="badge {{ $h['badge_class'] }}" style="font-size: 0.72rem; padding: 4px 8px;">
                                {{ $h['status'] }}
                            </span>
                        </div>
                    </div>

                    <!-- Calibrated Gauge -->
                    <div class="my-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--hl-text-dim);">{{ __('messages.condition_index') }}</span>
                            <span style="font-family: var(--hl-font-mono); font-weight: 800; font-size: 0.9rem; color: {{ $h['text_color'] }};">{{ $h['health_score'] }}%</span>
                        </div>
                        <div class="gauge-track">
                            <div class="gauge-fill" style="width: {{ $h['health_score'] }}%; background-color: {{ $h['text_color'] }};"></div>
                        </div>
                    </div>

                    <!-- Meta Bento -->
                    <div class="mobile-bento-meta">
                        <div class="mobile-meta-item">
                            <div class="mobile-meta-label">{{ __('messages.remaining_useful_life') }}</div>
                            <div class="mobile-meta-value" style="font-family: var(--hl-font-mono); color: var(--hl-text-bright);">
                                {{ $h['remaining_life'] }}
                            </div>
                        </div>
                        <div class="mobile-meta-item">
                            <div class="mobile-meta-label">{{ __('messages.assigned_employee') }}</div>
                            <div class="mobile-meta-value">
                                @if($asset->currentAssignment && $asset->currentAssignment->employee)
                                    {{ $asset->currentAssignment->employee->name }}
                                @else
                                    <span style="color: var(--hl-text-dim);">{{ __('messages.not_assigned') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Action Trigger -->
                    <button type="button" class="btn-telemetry-audit btn-audit-asset w-100 justify-content-center" data-asset-id="{{ $asset->id }}" style="padding: 9px 14px; font-size: 0.82rem;">
                        <i class="fas fa-stethoscope mr-1"></i>
                        <span>{{ __('messages.ai_audit') }}</span>
                    </button>
                </div>
            @empty
                <div class="text-center py-5" style="color: var(--hl-text-dim);">
                    <i class="fas fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                    {{ __('messages.no_data') }}
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Audit Kesehatan Aset AI (Hallmark Obsidian Telemetry) -->
<div class="modal fade" id="assetHealthModal" tabindex="-1" role="dialog" aria-labelledby="assetHealthModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 720px;">
        <div class="modal-content modal-telemetry-content">
            <div class="modal-telemetry-header">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(6, 182, 212, 0.15); border: 1px solid rgba(6, 182, 212, 0.35); display: flex; align-items: center; justify-content: center; color: var(--hl-accent-cyan); font-size: 0.95rem;">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <div>
                        <h6 class="modal-title font-weight-bold m-0" id="assetHealthModalLabel" style="color: var(--hl-text-bright); font-size: 0.95rem;">
                            {{ __('messages.ai_health_audit_report') }}
                        </h6>
                    </div>
                </div>
                <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close" style="opacity: 0.7; text-shadow: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <div id="modal-loading-spinner" class="text-center py-5">
                    <i class="fas fa-circle-notch fa-spin fa-2x mb-3" style="color: var(--hl-accent-cyan);"></i>
                    <p style="font-family: var(--hl-font-mono); font-size: 0.8rem; color: var(--hl-text-dim); text-transform: uppercase; letter-spacing: 0.06em; margin: 0;">
                        {{ __('messages.analyzing_health_diagnosis') }}
                    </p>
                </div>

                <div id="modal-health-content" style="display: none;">
                    <!-- Overview Tile -->
                    <div class="diagnostic-overview-tile">
                        <div style="flex: 1; min-width: 0;">
                            <h5 id="audit-asset-name" class="font-weight-bold mb-1" style="color: var(--hl-text-bright); font-size: 1.05rem; overflow-wrap: anywhere;"></h5>
                            <p id="audit-asset-sub" style="color: var(--hl-text-dim); font-size: 0.78rem; line-height: 1.4; margin: 0;"></p>
                        </div>
                        <div class="diagnostic-score-box">
                            <span style="font-family: var(--hl-font-mono); font-size: 0.68rem; font-weight: 700; color: var(--hl-text-dim); letter-spacing: 0.06em; text-transform: uppercase; display: block; margin-bottom: 2px;">
                                {{ __('messages.health_score_title') }}
                            </span>
                            <div class="d-flex align-items-center justify-content-end gap-2">
                                <span id="audit-score-number" class="diagnostic-score-val"></span>
                                <span id="audit-status-badge" class="badge px-3 py-2" style="font-size: 0.8rem; border-radius: 8px;"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Risk Factors Checklist -->
                    <div class="mb-4">
                        <h6 style="font-family: var(--hl-font-mono); font-size: 0.76rem; font-weight: 700; color: var(--hl-accent-cyan); letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 8px;">
                            <i class="fas fa-stethoscope mr-1"></i> {{ __('messages.risk_factors_degradation') }}:
                        </h6>
                        <div class="diagnostic-reasons-container">
                            <ul id="audit-reasons-list" class="list-unstyled m-0">
                            </ul>
                        </div>
                    </div>

                    <!-- Action Recommendation Tile -->
                    <div class="diagnostic-recommendation-tile">
                        <h6 style="font-family: var(--hl-font-mono); font-size: 0.76rem; font-weight: 700; color: var(--hl-accent-cyan); letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 8px;">
                            <i class="fas fa-lightbulb mr-1"></i> {{ __('messages.ai_action_recommendation') }}:
                        </h6>
                        <p id="audit-recommendation-text" style="color: var(--hl-text-bright); font-size: 0.88rem; line-height: 1.55; margin: 0;"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    const langBrand = "{{ __('messages.brand') }}";
    const langLocation = "{{ __('messages.location') }}";
    const langUser = "{{ __('messages.assigned_to') }}";
    const langNoRisk = "{{ __('messages.no_significant_risk_factors') }}";

    $(document).on('click', '.btn-audit-asset', function() {
        const assetId = $(this).data('asset-id');
        const modal = $('#assetHealthModal');
        
        $('#modal-loading-spinner').show();
        $('#modal-health-content').hide();
        modal.modal('show');

        $.ajax({
            url: "/predictive-health/" + assetId + "/analyze",
            type: "GET",
            success: function(response) {
                if (response.success) {
                    const a = response.asset;
                    const h = response.health;

                    $('#audit-asset-name').text(a.name + ' [' + a.asset_tag + ']');
                    $('#audit-asset-sub').text(a.category + ' • ' + langBrand + ': ' + a.brand + ' • ' + langLocation + ': ' + a.location + ' • ' + langUser + ': ' + a.user);
                    $('#audit-score-number').text(h.health_score + '%').css('color', h.text_color);
                    $('#audit-status-badge').attr('class', 'badge ' + h.badge_class).text(h.status);

                    let reasonsHtml = '';
                    if (h.reasons && h.reasons.length > 0) {
                        h.reasons.forEach(reason => {
                            reasonsHtml += `<li class="d-flex align-items-start py-2" style="font-size: 0.84rem; color: #fda4af; border-bottom: 1px solid rgba(244, 63, 94, 0.1);"><i class="fas fa-exclamation-triangle mr-2 mt-1" style="color: var(--hl-status-critical); font-size: 0.8rem;"></i><span>${reason}</span></li>`;
                        });
                    } else {
                        reasonsHtml = `<li class="d-flex align-items-center py-2" style="font-size: 0.84rem; color: #6ee7b7;"><i class="fas fa-check-circle mr-2" style="color: var(--hl-status-healthy); font-size: 0.85rem;"></i><span>${langNoRisk}</span></li>`;
                    }
                    $('#audit-reasons-list').html(reasonsHtml);
                    $('#audit-recommendation-text').text(h.recommendation);

                    $('#modal-loading-spinner').hide();
                    $('#modal-health-content').fadeIn();
                }
            },
            error: function(err) {
                console.error(err);
                modal.modal('hide');
            }
        });
    });
});
</script>
@endpush
@endsection
