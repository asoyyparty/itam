@extends('layouts.admin')

@section('title', __('messages.ticket'))

@section('content')
@php
    $globalTotal = \App\Models\Ticket::count();
    $globalOpen = \App\Models\Ticket::where('status', 'Open')->count();
    $globalInProgress = \App\Models\Ticket::where('status', 'In Progress')->count();
    $globalResolved = \App\Models\Ticket::where('status', 'Resolved')->count();
    $globalClosed = \App\Models\Ticket::where('status', 'Closed')->count();
    $globalCritical = \App\Models\Ticket::whereIn('priority', ['Critical', 'High'])->whereIn('status', ['Open', 'In Progress'])->count();
    $activeQueue = $globalOpen + $globalInProgress;
@endphp

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
    --hl-status-open: #38bdf8;
    --hl-status-open-glow: rgba(56, 189, 248, 0.22);
    --hl-status-progress: #f59e0b;
    --hl-status-progress-glow: rgba(245, 158, 11, 0.22);
    --hl-status-resolved: #10b981;
    --hl-status-resolved-glow: rgba(16, 185, 129, 0.22);
    --hl-status-closed: #94a3b8;
    --hl-priority-critical: #f43f5e;
    --hl-priority-critical-glow: rgba(244, 63, 94, 0.25);
    --hl-font-mono: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}

/* Incident Operations Dashboard */
.ticket-dashboard {
    width: 100%;
    margin-bottom: 2rem;
    overflow-x: clip;
}

/* Command Center Banner */
.ticket-banner {
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

.ticket-banner-title {
    display: flex;
    align-items: center;
    gap: 14px;
}

.ticket-banner-icon {
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
    color: var(--hl-status-open);
    background: rgba(56, 189, 248, 0.12);
    border: 1px solid rgba(56, 189, 248, 0.3);
    padding: 4px 10px;
    border-radius: 9999px;
}

.pulse-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background-color: var(--hl-status-open);
    box-shadow: 0 0 8px var(--hl-status-open);
    animation: pulse-ring 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
}

@keyframes pulse-ring {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(0.85); }
}

.ticket-banner-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.btn-tactile-export {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--hl-text-bright) !important;
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid var(--hl-border-active);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    text-decoration: none !important;
}

.btn-tactile-export:hover {
    background: rgba(30, 41, 59, 0.9);
    border-color: rgba(148, 163, 184, 0.45);
    transform: translateY(-1px);
}

.btn-tactile-create {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 18px;
    border-radius: 10px;
    font-size: 0.82rem;
    font-weight: 700;
    color: #ffffff !important;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    border: 1px solid rgba(129, 140, 248, 0.4);
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.3);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    text-decoration: none !important;
}

.btn-tactile-create:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.45);
    filter: brightness(1.08);
}

/* Bento KPI Matrix */
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
}

.bento-kpi-card.kpi-active::before { background: var(--hl-status-open); }
.bento-kpi-card.kpi-critical::before { background: var(--hl-priority-critical); }
.bento-kpi-card.kpi-resolved::before { background: var(--hl-status-resolved); }
.bento-kpi-card.kpi-total::before { background: var(--hl-accent-indigo); }

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
    font-size: 1.15rem;
    opacity: 0.9;
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

/* Status Ribbon (Touch Horizontal Scroll) */
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
    background: rgba(99, 102, 241, 0.15);
    border-color: rgba(99, 102, 241, 0.45);
    color: #a5b4fc;
    box-shadow: 0 0 14px rgba(99, 102, 241, 0.25);
}
.ribbon-pill.active-all .ribbon-count {
    background: rgba(99, 102, 241, 0.35);
    color: #ffffff;
}

.ribbon-pill.active-open {
    background: var(--hl-status-open-glow);
    border-color: rgba(56, 189, 248, 0.45);
    color: #7dd3fc;
    box-shadow: 0 0 14px var(--hl-status-open-glow);
}
.ribbon-pill.active-open .ribbon-count {
    background: rgba(56, 189, 248, 0.4);
    color: #ffffff;
}

.ribbon-pill.active-progress {
    background: var(--hl-status-progress-glow);
    border-color: rgba(245, 158, 11, 0.45);
    color: #fde68a;
    box-shadow: 0 0 14px var(--hl-status-progress-glow);
}
.ribbon-pill.active-progress .ribbon-count {
    background: rgba(245, 158, 11, 0.4);
    color: #ffffff;
}

.ribbon-pill.active-resolved {
    background: var(--hl-status-resolved-glow);
    border-color: rgba(16, 185, 129, 0.45);
    color: #6ee7b7;
    box-shadow: 0 0 14px var(--hl-status-resolved-glow);
}
.ribbon-pill.active-resolved .ribbon-count {
    background: rgba(16, 185, 129, 0.4);
    color: #ffffff;
}

.ribbon-pill.active-closed {
    background: rgba(148, 163, 184, 0.15);
    border-color: rgba(148, 163, 184, 0.4);
    color: #e2e8f0;
}
.ribbon-pill.active-closed .ribbon-count {
    background: rgba(148, 163, 184, 0.3);
    color: #ffffff;
}

/* Incident Matrix Panel */
.ticket-panel {
    background: var(--hl-bg-card);
    border: 1px solid var(--hl-border-subtle);
    border-radius: 16px;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.25);
    backdrop-filter: blur(12px);
    overflow: hidden;
}

/* Filter Controls Toolbar */
.ticket-filter-toolbar {
    padding: 16px 20px;
    border-bottom: 1px solid var(--hl-border-subtle);
    background: rgba(15, 23, 42, 0.5);
}

.ticket-search-controls {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    width: 100%;
}

.filter-select {
    height: 40px !important;
    border-radius: 10px !important;
    border: 1px solid var(--hl-border-subtle) !important;
    background: rgba(15, 23, 42, 0.75) !important;
    color: var(--hl-text-bright) !important;
    font-size: 0.82rem !important;
    padding: 0 12px !important;
    min-width: 140px;
    flex: 0 1 160px;
    outline: none !important;
    transition: border-color 0.2s;
}

.filter-select:focus {
    border-color: var(--hl-accent-cyan) !important;
    box-shadow: 0 0 0 2px var(--hl-accent-cyan-glow) !important;
}

.ticket-search-input-box {
    position: relative;
    flex: 1 1 240px;
    min-width: 200px;
}

.ticket-search-input-box i.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--hl-text-dim);
    font-size: 0.85rem;
    pointer-events: none;
}

.ticket-search-field {
    width: 100%;
    height: 40px !important;
    padding-left: 36px !important;
    padding-right: 36px !important;
    border-radius: 10px !important;
    border: 1px solid var(--hl-border-subtle) !important;
    background: rgba(15, 23, 42, 0.75) !important;
    color: var(--hl-text-bright) !important;
    font-size: 0.82rem !important;
    outline: none !important;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.ticket-search-field:focus {
    border-color: var(--hl-accent-cyan) !important;
    box-shadow: 0 0 0 2px var(--hl-accent-cyan-glow) !important;
}

.ticket-clear-btn {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--hl-text-dim);
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 4px;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none !important;
}

.ticket-clear-btn:hover {
    color: var(--hl-status-critical);
}

/* Panel Meta Sub-Header */
.ticket-panel-subheader {
    padding: 12px 20px;
    border-bottom: 1px solid var(--hl-border-subtle);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    background: rgba(15, 23, 42, 0.3);
}

.ticket-panel-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--hl-text-bright);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
    letter-spacing: 0.02em;
}

/* Telemetry Table (Desktop View) */
.ticket-table-wrapper {
    max-height: calc(100vh - 240px);
    overflow-y: auto;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.ticket-table-wrapper::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.ticket-table-wrapper::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.2);
}
.ticket-table-wrapper::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.25);
    border-radius: 4px;
}

.ticket-telemetry-table {
    width: 100%;
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
}

.ticket-telemetry-table thead th {
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
    padding: 14px 16px;
    white-space: nowrap;
}

.ticket-telemetry-table tbody td {
    padding: 13px 16px;
    vertical-align: middle;
    border-bottom: 1px solid var(--hl-border-subtle);
    font-size: 0.85rem;
}

.ticket-telemetry-table tbody tr {
    transition: background-color 0.15s ease;
}

.ticket-telemetry-table tbody tr:hover {
    background-color: rgba(30, 41, 59, 0.4) !important;
}

/* Priority & Status Badges */
.badge-priority-low {
    background: rgba(56, 189, 248, 0.12);
    border: 1px solid rgba(56, 189, 248, 0.3);
    color: #38bdf8;
    font-size: 0.72rem;
    padding: 4px 8px;
    border-radius: 6px;
    font-weight: 600;
}

.badge-priority-medium {
    background: rgba(245, 158, 11, 0.12);
    border: 1px solid rgba(245, 158, 11, 0.3);
    color: #f59e0b;
    font-size: 0.72rem;
    padding: 4px 8px;
    border-radius: 6px;
    font-weight: 600;
}

.badge-priority-high {
    background: rgba(249, 115, 22, 0.14);
    border: 1px solid rgba(249, 115, 22, 0.35);
    color: #fb923c;
    font-size: 0.72rem;
    padding: 4px 8px;
    border-radius: 6px;
    font-weight: 700;
}

.badge-priority-critical {
    background: rgba(244, 63, 94, 0.16);
    border: 1px solid rgba(244, 63, 94, 0.4);
    color: #f43f5e;
    font-size: 0.72rem;
    padding: 4px 8px;
    border-radius: 6px;
    font-weight: 800;
    box-shadow: 0 0 10px rgba(244, 63, 94, 0.2);
}

/* Interactive Status Dropdown */
.status-dropdown-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 8px;
    border: 1px solid transparent;
    background: transparent;
    cursor: pointer;
    transition: transform 0.15s;
    outline: none !important;
}

.status-dropdown-btn:hover {
    transform: translateY(-1px);
}

.status-dropdown-btn::after {
    display: none !important;
}

.status-menu-tactile {
    background: #0f172a !important;
    border: 1px solid var(--hl-border-active) !important;
    border-radius: 12px !important;
    padding: 6px !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5) !important;
}

.status-menu-tactile .dropdown-item {
    padding: 8px 12px !important;
    border-radius: 8px !important;
    font-size: 0.8rem !important;
    font-weight: 600 !important;
    transition: background 0.15s !important;
}

.status-menu-tactile .dropdown-item:hover {
    background: rgba(30, 41, 59, 0.8) !important;
}

/* Tactical Action Icon Buttons */
.btn-tactile-action {
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

.btn-tactile-edit {
    background: rgba(245, 158, 11, 0.12);
    border-color: rgba(245, 158, 11, 0.25);
    color: #fbbf24 !important;
}

.btn-tactile-edit:hover {
    background: rgba(245, 158, 11, 0.25);
    border-color: rgba(245, 158, 11, 0.5);
    transform: translateY(-1px);
}

.btn-tactile-delete {
    background: rgba(244, 63, 94, 0.12);
    border-color: rgba(244, 63, 94, 0.25);
    color: #f43f5e !important;
}

.btn-tactile-delete:hover {
    background: rgba(244, 63, 94, 0.25);
    border-color: rgba(244, 63, 94, 0.5);
    transform: translateY(-1px);
}

/* Mobile Bento Cards */
.mobile-bento-card {
    background: rgba(15, 23, 42, 0.92);
    border: 1px solid rgba(148, 163, 184, 0.16);
    border-radius: 14px;
    padding: 15px 16px;
    margin-bottom: 12px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
    transition: transform 0.15s, border-color 0.15s;
}

.mobile-bento-card:hover {
    border-color: rgba(148, 163, 184, 0.3);
}

.mobile-bento-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 8px;
}

.mobile-bento-title {
    font-weight: 700;
    font-size: 0.94rem;
    line-height: 1.4;
    color: var(--hl-text-bright);
    margin: 0 0 8px 0;
    overflow-wrap: anywhere;
    min-width: 0;
}

.mobile-bento-meta {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    row-gap: 10px;
    column-gap: 12px;
    background: rgba(0, 0, 0, 0.28);
    border: 1px solid rgba(148, 163, 184, 0.08);
    border-radius: 10px;
    padding: 11px 13px;
    margin: 10px 0 12px 0;
}

.mobile-meta-item {
    min-width: 0;
}

.mobile-meta-label {
    font-size: 0.66rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--hl-text-dim);
    margin-bottom: 3px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.mobile-meta-value {
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--hl-text-bright);
    line-height: 1.35;
    word-break: break-word;
    overflow-wrap: anywhere;
}

.mobile-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding-top: 11px;
    border-top: 1px solid rgba(148, 163, 184, 0.12);
}

/* Responsive Rules (320px, 375px, 414px, 768px, 1200px) */
@media (max-width: 1199.98px) {
    .bento-kpi-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

.ticket-filter-selects-row {
    display: flex;
    align-items: center;
    gap: 12px;
}

@media (max-width: 767.98px) {
    .ticket-dashboard {
        margin-bottom: 1rem;
    }

    .ticket-banner {
        padding: 14px 16px;
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
        border-radius: 14px;
        margin-bottom: 14px;
    }

    .ticket-banner-icon {
        width: 38px;
        height: 38px;
        font-size: 1.1rem;
    }

    .ticket-banner-actions {
        width: 100%;
        display: flex;
        gap: 8px;
    }

    .btn-tactile-export,
    .btn-tactile-create {
        flex: 1 1 50%;
        justify-content: center;
        padding: 8px 12px;
        font-size: 0.78rem;
    }

    /* 2x2 Bento KPI Grid on Mobile */
    .bento-kpi-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 8px !important;
        margin-bottom: 14px !important;
    }

    .bento-kpi-card {
        padding: 10px 12px !important;
        border-radius: 12px !important;
    }

    .kpi-card-header {
        margin-bottom: 6px !important;
    }

    .kpi-card-label {
        font-size: 0.64rem !important;
        letter-spacing: 0.04em !important;
    }

    .kpi-card-icon {
        font-size: 0.95rem !important;
    }

    .kpi-card-value {
        font-size: 1.35rem !important;
        margin-bottom: 4px !important;
    }

    .kpi-card-desc {
        font-size: 0.67rem !important;
        line-height: 1.25 !important;
    }

    /* Mobile Search & Filter Toolbar */
    .ticket-filter-toolbar {
        padding: 12px 14px !important;
    }

    .ticket-search-controls {
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
    }

    .ticket-filter-selects-row {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 8px !important;
        width: 100% !important;
    }

    .ticket-filter-selects-row .filter-select {
        width: 100% !important;
        min-width: 0 !important;
        flex: 1 1 auto !important;
        height: 38px !important;
        font-size: 0.78rem !important;
        padding: 0 8px !important;
    }

    .ticket-search-input-box {
        width: 100% !important;
        min-width: 0 !important;
        flex-basis: auto !important;
    }

    .ticket-search-field {
        height: 38px !important;
        font-size: 0.8rem !important;
        padding-left: 34px !important;
    }

    .ticket-panel {
        border-radius: 14px !important;
    }

    .ticket-panel-subheader {
        padding: 10px 14px !important;
    }

    /* Mobile Cards Feed - Zero Scroll Trap */
    .ticket-mobile-feed {
        padding: 10px 12px !important;
    }

    .mobile-bento-card {
        padding: 13px 14px !important;
        margin-bottom: 10px !important;
        border-radius: 12px !important;
    }
}

/* ══════════════════════════════════════════════════════════
   Hallmark Light Mode — Incident Operations Dispatch
   ══════════════════════════════════════════════════════════ */
body.light-mode {
    --hl-bg-canvas: #f8fafc;
    --hl-bg-surface: #ffffff;
    --hl-bg-card: #ffffff;
    --hl-bg-card-hover: #f8fafc;
    --hl-border-subtle: #e2e8f0;
    --hl-border-active: #cbd5e1;
    --hl-text-bright: #0f172a;
    --hl-text-muted: #475569;
    --hl-text-dim: #64748b;
    --hl-accent-cyan: #0284c7;
    --hl-accent-cyan-glow: rgba(2, 132, 199, 0.15);
    --hl-status-open: #0284c7;
    --hl-status-open-glow: rgba(2, 132, 199, 0.12);
    --hl-status-progress: #d97706;
    --hl-status-progress-glow: rgba(217, 119, 6, 0.12);
    --hl-status-resolved: #059669;
    --hl-status-resolved-glow: rgba(5, 150, 105, 0.12);
    --hl-status-closed: #64748b;
}

body.light-mode .ticket-banner {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05) !important;
}

body.light-mode .ticket-banner-icon {
    background: rgba(99, 102, 241, 0.1) !important;
    border-color: rgba(99, 102, 241, 0.25) !important;
    color: #4f46e5 !important;
    box-shadow: 0 0 12px rgba(99, 102, 241, 0.12) !important;
}

body.light-mode .telemetry-badge-pulse {
    background: rgba(14, 165, 233, 0.1) !important;
    border-color: rgba(14, 165, 233, 0.3) !important;
    color: #0369a1 !important;
}

body.light-mode .btn-tactile-export {
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    color: #334155 !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
}
body.light-mode .btn-tactile-export:hover {
    background: #f1f5f9 !important;
    border-color: #94a3b8 !important;
    color: #0f172a !important;
}

body.light-mode .bento-kpi-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04) !important;
}
body.light-mode .bento-kpi-card:hover {
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08) !important;
    border-color: #cbd5e1 !important;
}

body.light-mode .bento-kpi-card.kpi-total .kpi-card-label {
    color: #4f46e5 !important;
}
body.light-mode .bento-kpi-card.kpi-total .kpi-card-icon {
    color: #4f46e5 !important;
}

body.light-mode .ribbon-pill {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    color: #475569 !important;
}
body.light-mode .ribbon-pill:hover {
    background: #f1f5f9 !important;
    color: #0f172a !important;
    border-color: #cbd5e1 !important;
}
body.light-mode .ribbon-count {
    background: #f1f5f9 !important;
    color: #475569 !important;
}
body.light-mode .ribbon-pill.active-all {
    background: rgba(99, 102, 241, 0.1) !important;
    border-color: rgba(99, 102, 241, 0.4) !important;
    color: #4338ca !important;
}
body.light-mode .ribbon-pill.active-all .ribbon-count {
    background: #4f46e5 !important;
    color: #ffffff !important;
}
body.light-mode .ribbon-pill.active-open {
    background: rgba(14, 165, 233, 0.1) !important;
    border-color: rgba(14, 165, 233, 0.4) !important;
    color: #0369a1 !important;
}
body.light-mode .ribbon-pill.active-open .ribbon-count {
    background: #0284c7 !important;
    color: #ffffff !important;
}
body.light-mode .ribbon-pill.active-progress {
    background: rgba(245, 158, 11, 0.1) !important;
    border-color: rgba(245, 158, 11, 0.4) !important;
    color: #b45309 !important;
}
body.light-mode .ribbon-pill.active-progress .ribbon-count {
    background: #d97706 !important;
    color: #ffffff !important;
}
body.light-mode .ribbon-pill.active-resolved {
    background: rgba(16, 185, 129, 0.1) !important;
    border-color: rgba(16, 185, 129, 0.4) !important;
    color: #047857 !important;
}
body.light-mode .ribbon-pill.active-resolved .ribbon-count {
    background: #059669 !important;
    color: #ffffff !important;
}
body.light-mode .ribbon-pill.active-closed {
    background: rgba(100, 116, 139, 0.1) !important;
    border-color: rgba(100, 116, 139, 0.4) !important;
    color: #334155 !important;
}
body.light-mode .ribbon-pill.active-closed .ribbon-count {
    background: #64748b !important;
    color: #ffffff !important;
}

body.light-mode .ticket-panel {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05) !important;
}
body.light-mode .ticket-filter-toolbar {
    background: #f8fafc !important;
    border-color: #e2e8f0 !important;
}
body.light-mode .filter-select {
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    color: #0f172a !important;
}
body.light-mode .ticket-search-field {
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    color: #0f172a !important;
}
body.light-mode .ticket-panel-subheader {
    background: #f8fafc !important;
    border-color: #e2e8f0 !important;
}
body.light-mode .ticket-telemetry-table thead th {
    background: #f1f5f9 !important;
    border-color: #e2e8f0 !important;
    color: #475569 !important;
}
body.light-mode .ticket-telemetry-table tbody td {
    border-color: #f1f5f9 !important;
    color: #1e293b !important;
}
body.light-mode .ticket-telemetry-table tbody tr:hover {
    background-color: #f8fafc !important;
}
body.light-mode .status-menu-tactile {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.1) !important;
}
body.light-mode .status-menu-tactile .dropdown-item {
    color: #334155 !important;
}
body.light-mode .status-menu-tactile .dropdown-item:hover {
    background: #f1f5f9 !important;
    color: #0f172a !important;
}
body.light-mode .mobile-bento-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.05) !important;
}
body.light-mode .mobile-bento-meta {
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
}
body.light-mode .mobile-card-footer {
    border-top-color: #f1f5f9 !important;
}
</style>

<div class="ticket-dashboard">
    <!-- Command Center Header Banner -->
    <div class="ticket-banner">
        <div class="ticket-banner-title">
            <div class="ticket-banner-icon">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <h5 class="m-0 font-weight-bold" style="font-size: 1.15rem; color: var(--hl-text-bright); line-height: 1.2;">
                        {{ __('messages.ticket') }}
                    </h5>
                    <span class="telemetry-badge-pulse">
                        <span class="pulse-dot"></span>
                        {{ __('messages.live_incident_dispatch') }}
                    </span>
                </div>
                <small style="font-family: var(--hl-font-mono); font-size: 0.74rem; color: var(--hl-text-dim);">
                    {{ __('messages.service_desk_telemetry_sub') }}
                </small>
            </div>
        </div>
        <div class="ticket-banner-actions">
            <a href="{{ route('tickets.export', request()->query()) }}" class="btn-tactile-export" title="{{ __('messages.export') }}">
                <i class="fas fa-file-export mr-1" style="color: var(--hl-accent-cyan);"></i>
                <span>{{ __('messages.export') }}</span>
            </a>
            @can('action_manage_tickets')
            <a href="{{ route('tickets.create') }}" class="btn-tactile-create" title="{{ __('messages.create_ticket') }}">
                <i class="fas fa-plus mr-1"></i>
                <span>{{ __('messages.create_ticket') }}</span>
            </a>
            @endcan
        </div>
    </div>

    <!-- Bento KPI Stat Matrix -->
    <div class="bento-kpi-grid">
        <!-- 1. Active Incident Queue -->
        <div class="bento-kpi-card kpi-active">
            <div>
                <div class="kpi-card-header">
                    <span class="kpi-card-label" style="color: var(--hl-status-open);">{{ __('messages.active_incident_queue') }}</span>
                    <i class="fas fa-bolt kpi-card-icon" style="color: var(--hl-status-open);"></i>
                </div>
                <div class="kpi-card-value" style="color: var(--hl-status-open);">
                    {{ $activeQueue }}
                    <span style="font-size: 0.85rem; font-weight: 500; color: var(--hl-text-dim);">{{ __('messages.unit') }}</span>
                </div>
            </div>
            <p class="kpi-card-desc">{{ __('messages.active_incident_queue_desc', ['open' => $globalOpen, 'progress' => $globalInProgress]) }}</p>
        </div>

        <!-- 2. Critical & High Priority -->
        <div class="bento-kpi-card kpi-critical">
            <div>
                <div class="kpi-card-header">
                    <span class="kpi-card-label" style="color: var(--hl-priority-critical);">{{ __('messages.high_critical_triage') }}</span>
                    <i class="fas fa-exclamation-circle kpi-card-icon" style="color: var(--hl-priority-critical);"></i>
                </div>
                <div class="kpi-card-value" style="color: var(--hl-priority-critical);">
                    {{ $globalCritical }}
                    <span style="font-size: 0.85rem; font-weight: 500; color: var(--hl-text-dim);">{{ __('messages.unit') }}</span>
                </div>
            </div>
            <p class="kpi-card-desc">{{ __('messages.high_critical_triage_desc') }}</p>
        </div>

        <!-- 3. Resolved Incidents -->
        <div class="bento-kpi-card kpi-resolved">
            <div>
                <div class="kpi-card-header">
                    <span class="kpi-card-label" style="color: var(--hl-status-resolved);">{{ __('messages.resolved_incidents') }}</span>
                    <i class="fas fa-check-double kpi-card-icon" style="color: var(--hl-status-resolved);"></i>
                </div>
                <div class="kpi-card-value" style="color: var(--hl-status-resolved);">
                    {{ $globalResolved }}
                    <span style="font-size: 0.85rem; font-weight: 500; color: var(--hl-text-dim);">{{ __('messages.unit') }}</span>
                </div>
            </div>
            <p class="kpi-card-desc">{{ __('messages.resolved_incidents_desc') }}</p>
        </div>

        <!-- 4. Total Logged Tickets -->
        <div class="bento-kpi-card kpi-total">
            <div>
                <div class="kpi-card-header">
                    <span class="kpi-card-label" style="color: #a5b4fc;">{{ __('messages.total_monitored_tickets') }}</span>
                    <i class="fas fa-layer-group kpi-card-icon" style="color: #818cf8;"></i>
                </div>
                <div class="kpi-card-value">
                    {{ $globalTotal }}
                    <span style="font-size: 0.85rem; font-weight: 500; color: var(--hl-text-dim);">{{ __('messages.unit') }}</span>
                </div>
            </div>
            <p class="kpi-card-desc">{{ __('messages.total_monitored_tickets_desc') }}</p>
        </div>
    </div>

    <!-- Tactile Status Ribbon (Filter Tabs) -->
    <div class="status-ribbon-wrapper">
        <div class="status-ribbon-scroll">
            <a href="{{ route('tickets.index', array_merge(request()->except(['status', 'page']), [])) }}" 
               class="ribbon-pill {{ !request('status') ? 'active-all' : '' }}">
                <span>{{ __('messages.all') }}</span>
                <span class="ribbon-count">{{ $globalTotal }}</span>
            </a>
            <a href="{{ route('tickets.index', array_merge(request()->except(['status', 'page']), ['status' => 'Open'])) }}" 
               class="ribbon-pill {{ request('status') == 'Open' ? 'active-open' : '' }}">
                <span style="color: var(--hl-status-open);">&#9679;</span>
                <span>{{ __('messages.open') }}</span>
                <span class="ribbon-count">{{ $globalOpen }}</span>
            </a>
            <a href="{{ route('tickets.index', array_merge(request()->except(['status', 'page']), ['status' => 'In Progress'])) }}" 
               class="ribbon-pill {{ request('status') == 'In Progress' ? 'active-progress' : '' }}">
                <span style="color: var(--hl-status-progress);">&#9679;</span>
                <span>{{ __('messages.in_progress') }}</span>
                <span class="ribbon-count">{{ $globalInProgress }}</span>
            </a>
            <a href="{{ route('tickets.index', array_merge(request()->except(['status', 'page']), ['status' => 'Resolved'])) }}" 
               class="ribbon-pill {{ request('status') == 'Resolved' ? 'active-resolved' : '' }}">
                <span style="color: var(--hl-status-resolved);">&#9679;</span>
                <span>{{ __('messages.resolved') }}</span>
                <span class="ribbon-count">{{ $globalResolved }}</span>
            </a>
            <a href="{{ route('tickets.index', array_merge(request()->except(['status', 'page']), ['status' => 'Closed'])) }}" 
               class="ribbon-pill {{ request('status') == 'Closed' ? 'active-closed' : '' }}">
                <span style="color: var(--hl-status-closed);">&#9679;</span>
                <span>{{ __('messages.closed') }}</span>
                <span class="ribbon-count">{{ $globalClosed }}</span>
            </a>
        </div>
    </div>

    <!-- Incident Matrix Panel -->
    <div class="ticket-panel">
        <!-- Filter Controls Toolbar -->
        <div class="ticket-filter-toolbar">
            <form action="{{ route('tickets.index') }}" method="GET" class="m-0" id="ticketFilterForm">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="ticket-search-controls">
                    <div class="ticket-filter-selects-row">
                        <!-- Category Select -->
                        <select name="category" class="filter-select" onchange="document.getElementById('ticketFilterForm').submit();">
                            <option value="">{{ __('messages.all_category') }}</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat['name'] }}" {{ request('category') == $cat['name'] ? 'selected' : '' }}>
                                    {{ $cat['name'] }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Priority Select -->
                        <select name="priority" class="filter-select" onchange="document.getElementById('ticketFilterForm').submit();">
                            <option value="">{{ __('messages.all_priority') }}</option>
                            <option value="Low" {{ request('priority') == 'Low' ? 'selected' : '' }}>{{ __('messages.low') }}</option>
                            <option value="Medium" {{ request('priority') == 'Medium' ? 'selected' : '' }}>{{ __('messages.medium') }}</option>
                            <option value="High" {{ request('priority') == 'High' ? 'selected' : '' }}>{{ __('messages.high') }}</option>
                            <option value="Critical" {{ request('priority') == 'Critical' ? 'selected' : '' }}>{{ __('messages.critical') }}</option>
                        </select>
                    </div>

                    <!-- Search Box -->
                    <div class="ticket-search-input-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" name="search" class="ticket-search-field" placeholder="{{ __('messages.search_ticket') ?? 'Cari nomor tiket, judul, pelapor, atau PIC...' }}" value="{{ request('search') }}" autocomplete="off">
                        @if(request()->anyFilled(['search', 'category', 'priority', 'status']))
                            <a href="{{ route('tickets.index') }}" class="ticket-clear-btn" title="{{ __('messages.clear') }}">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Panel Meta Sub-Header -->
        <div class="ticket-panel-subheader">
            <h6 class="ticket-panel-title">
                <i class="fas fa-list-ul" style="color: var(--hl-accent-cyan);"></i>
                {{ __('messages.incident_tickets_queue') }}
            </h6>
            <div style="font-family: var(--hl-font-mono); font-size: 0.74rem; color: var(--hl-text-dim);">
                {{ __('messages.displaying_tickets_count', ['count' => count($tickets)]) }}
            </div>
        </div>

        <!-- Desktop View (Data Table) -->
        <div class="ticket-table-wrapper d-none d-md-block">
            <table class="ticket-telemetry-table">
                <thead>
                    <tr>
                        <th style="padding-left: 20px; width: 60px;">{{ __('messages.no') }}</th>
                        <th style="width: 140px;">{{ __('messages.ticket_number') }}</th>
                        <th>{{ __('messages.title') }}</th>
                        <th>{{ __('messages.reporter') }}</th>
                        <th>{{ __('messages.category') }}</th>
                        <th>{{ __('messages.pic') }}</th>
                        <th>{{ __('messages.priority') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.timeline') }}</th>
                        <th class="text-right" style="padding-right: 20px; width: 110px;">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr>
                        <td class="text-muted" style="padding-left: 20px; font-family: var(--hl-font-mono); font-size: 0.78rem;">
                            {{ (method_exists($tickets, 'firstItem') && $tickets->firstItem() ? $tickets->firstItem() + $loop->index : $loop->iteration) }}
                        </td>
                        <td>
                            <span style="font-family: var(--hl-font-mono); font-weight: 700; color: var(--hl-status-open); font-size: 0.85rem; letter-spacing: 0.02em;">
                                {{ $ticket->ticket_number }}
                            </span>
                        </td>
                        <td>
                            <div class="font-weight-bold" style="color: var(--hl-text-bright); line-height: 1.35;">
                                {{ $ticket->title }}
                            </div>
                            @if($ticket->asset)
                            <div class="mt-1">
                                <a href="{{ route('assets.show', $ticket->asset->id) }}" class="text-decoration-none badge" style="font-family: var(--hl-font-mono); font-size: 0.7rem; background: rgba(255, 255, 255, 0.07); border: 1px solid var(--hl-border-subtle); color: var(--hl-text-muted);">
                                    <i class="fas fa-laptop mr-1" style="color: var(--hl-accent-cyan);"></i> {{ $ticket->asset->asset_tag }}
                                </a>
                            </div>
                            @endif
                        </td>
                        <td>
                            <span style="color: var(--hl-text-bright); font-weight: 500;">
                                {{ $ticket->employee->name ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge" style="font-size: 0.72rem; background: rgba(6, 182, 212, 0.1); border: 1px solid rgba(6, 182, 212, 0.25); color: #67e8f9;">
                                {{ $ticket->category ?? '-' }}
                            </span>
                        </td>
                        <td>
                            @if($ticket->pic)
                                <div class="d-flex align-items-center gap-1" style="color: var(--hl-text-bright);">
                                    <i class="far fa-user-circle mr-1" style="color: #818cf8;"></i>
                                    <span>{{ $ticket->pic->name }}</span>
                                </div>
                            @else
                                <span class="badge" style="background: rgba(148, 163, 184, 0.1); border: 1px solid var(--hl-border-subtle); color: var(--hl-text-dim); font-size: 0.72rem;">
                                    {{ __('messages.unassigned') }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @switch($ticket->priority)
                                @case('Low') <span class="badge-priority-low">{{ __('messages.low') }}</span> @break
                                @case('Medium') <span class="badge-priority-medium">{{ __('messages.medium') }}</span> @break
                                @case('High') <span class="badge-priority-high">{{ __('messages.high') }}</span> @break
                                @case('Critical') <span class="badge-priority-critical">{{ __('messages.critical') }}</span> @break
                            @endswitch
                        </td>
                        <td>
                            <div class="dropdown">
                                @can('action_manage_tickets')
                                <button class="status-dropdown-btn status-btn p-0 border-0" type="button" data-toggle="dropdown" aria-expanded="false" data-ticket-id="{{ $ticket->id }}">
                                    @switch($ticket->status)
                                        @case('Open') <span class="badge badge-primary status-badge" style="box-shadow: 0 0 8px rgba(0,123,255,0.5); font-size: 0.75rem; padding: 4px 8px;">{{ __('messages.open') ?? 'Open' }} <i class="fas fa-caret-down ml-1 opacity-75"></i></span> @break
                                        @case('In Progress') <span class="badge badge-warning status-badge" style="box-shadow: 0 0 8px rgba(255,193,7,0.5); font-size: 0.75rem; padding: 4px 8px;">{{ __('messages.in_progress') ?? 'In Progress' }} <i class="fas fa-caret-down ml-1 opacity-75"></i></span> @break
                                        @case('Resolved') <span class="badge badge-success status-badge" style="box-shadow: 0 0 8px rgba(40,167,69,0.5); font-size: 0.75rem; padding: 4px 8px;">{{ __('messages.resolved') ?? 'Resolved' }} <i class="fas fa-caret-down ml-1 opacity-75"></i></span> @break
                                        @case('Closed') <span class="badge badge-secondary status-badge" style="font-size: 0.75rem; padding: 4px 8px;">{{ __('messages.closed') ?? 'Closed' }} <i class="fas fa-caret-down ml-1 opacity-75"></i></span> @break
                                    @endswitch
                                </button>
                                <div class="dropdown-menu dropdown-menu-right status-menu-tactile">
                                    <a class="dropdown-item status-change-btn text-primary" href="#" data-status="Open"><i class="fas fa-dot-circle mr-2"></i> {{ __('messages.open') ?? 'Open' }}</a>
                                    <a class="dropdown-item status-change-btn text-warning" href="#" data-status="In Progress"><i class="fas fa-spinner mr-2"></i> {{ __('messages.in_progress') ?? 'In Progress' }}</a>
                                    <a class="dropdown-item status-change-btn text-success" href="#" data-status="Resolved"><i class="fas fa-check-circle mr-2"></i> {{ __('messages.resolved') ?? 'Resolved' }}</a>
                                    <a class="dropdown-item status-change-btn text-secondary" href="#" data-status="Closed"><i class="fas fa-archive mr-2"></i> {{ __('messages.closed') ?? 'Closed' }}</a>
                                </div>
                                @else
                                <div class="status-btn" data-ticket-id="{{ $ticket->id }}">
                                    @switch($ticket->status)
                                        @case('Open') <span class="badge badge-primary status-badge">{{ __('messages.open') ?? 'Open' }}</span> @break
                                        @case('In Progress') <span class="badge badge-warning status-badge">{{ __('messages.in_progress') ?? 'In Progress' }}</span> @break
                                        @case('Resolved') <span class="badge badge-success status-badge">{{ __('messages.resolved') ?? 'Resolved' }}</span> @break
                                        @case('Closed') <span class="badge badge-secondary status-badge">{{ __('messages.closed') ?? 'Closed' }}</span> @break
                                    @endswitch
                                </div>
                                @endcan
                            </div>
                        </td>
                        <td style="white-space: nowrap;">
                            <div style="font-family: var(--hl-font-mono); font-size: 0.74rem; color: var(--hl-text-dim);">
                                <i class="fas fa-clock mr-1" style="color: var(--hl-status-open);"></i> {{ $ticket->created_at->format('Y-m-d H:i') }}
                            </div>
                            @if($ticket->completed_at)
                                <div class="mt-1" style="font-family: var(--hl-font-mono); font-size: 0.74rem; color: var(--hl-status-resolved);">
                                    <i class="fas fa-check-circle mr-1"></i> {{ $ticket->completed_at->format('Y-m-d H:i') }}
                                </div>
                            @endif
                        </td>
                        <td class="text-right" style="padding-right: 20px;">
                            <div class="d-flex justify-content-end gap-1">
                                @can('action_manage_tickets')
                                <a href="{{ route('tickets.edit', array_merge(['ticket' => $ticket->id], request()->query())) }}" class="btn-tactile-action btn-tactile-edit" title="{{ __('messages.edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('tickets.destroy', array_merge(['ticket' => $ticket->id], request()->query())) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-tactile-action btn-tactile-delete btn-delete border-0" title="{{ __('messages.delete') }}" data-confirm-message="{{ __('messages.confirm_delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5" style="color: var(--hl-text-dim);">
                            <i class="fas fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                            {{ __('messages.no_data') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile View (Tactile Bento Cards) - Zero Scroll Trap -->
        <div class="d-block d-md-none ticket-mobile-feed">
            @forelse($tickets as $ticket)
            <div class="mobile-bento-card">
                <!-- Top Row: Ticket Number, Category, and Priority Badge -->
                <div class="mobile-bento-header">
                    <div style="min-width: 0;">
                        <div class="font-mono font-weight-bold" style="color: var(--hl-status-open); font-size: 0.88rem; letter-spacing: 0.03em; margin-bottom: 5px;">
                            #{{ $ticket->ticket_number }}
                        </div>
                        @if($ticket->category)
                            <div>
                                <span class="badge" style="font-size: 0.68rem; background: rgba(6, 182, 212, 0.12); color: #67e8f9; border: 1px solid rgba(6, 182, 212, 0.28); border-radius: 6px; padding: 2px 8px; font-weight: 600;">
                                    {{ $ticket->category }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-shrink-0" style="padding-top: 1px;">
                        @switch($ticket->priority)
                            @case('Low') <span class="badge-priority-low">{{ __('messages.low') }}</span> @break
                            @case('Medium') <span class="badge-priority-medium">{{ __('messages.medium') }}</span> @break
                            @case('High') <span class="badge-priority-high">{{ __('messages.high') }}</span> @break
                            @case('Critical') <span class="badge-priority-critical">{{ __('messages.critical') }}</span> @break
                        @endswitch
                    </div>
                </div>

                <!-- Title & Attached Asset Tag -->
                <div class="mobile-bento-title">
                    {{ $ticket->title }}
                </div>
                @if($ticket->asset)
                    <div class="mb-2">
                        <a href="{{ route('assets.show', $ticket->asset->id) }}" class="badge text-decoration-none" style="font-family: var(--hl-font-mono); font-size: 0.7rem; background: rgba(255, 255, 255, 0.08); border: 1px solid var(--hl-border-subtle); color: var(--hl-text-muted); border-radius: 6px; padding: 3px 8px;">
                            <i class="fas fa-laptop mr-1" style="color: var(--hl-accent-cyan);"></i> {{ $ticket->asset->asset_tag }}
                        </a>
                    </div>
                @endif

                <!-- 2x2 Meta Grid -->
                <div class="mobile-bento-meta">
                    <div class="mobile-meta-item">
                        <div class="mobile-meta-label"><i class="far fa-user" style="color: var(--hl-accent-cyan);"></i> {{ __('messages.reporter') }}</div>
                        <div class="mobile-meta-value">{{ $ticket->employee->name ?? '-' }}</div>
                    </div>
                    <div class="mobile-meta-item">
                        <div class="mobile-meta-label"><i class="far fa-user-circle" style="color: #818cf8;"></i> {{ __('messages.pic') }}</div>
                        <div class="mobile-meta-value">{{ $ticket->pic->name ?? __('messages.unassigned') }}</div>
                    </div>
                    <div class="mobile-meta-item">
                        <div class="mobile-meta-label"><i class="far fa-clock" style="color: var(--hl-status-open);"></i> {{ __('messages.created') }}</div>
                        <div class="mobile-meta-value font-mono">
                            <span>{{ $ticket->created_at->format('d/m/Y') }}</span>
                            <small class="text-muted font-weight-normal ml-1">{{ $ticket->created_at->format('H:i') }}</small>
                        </div>
                    </div>
                    <div class="mobile-meta-item">
                        <div class="mobile-meta-label"><i class="far fa-check-circle" style="color: var(--hl-status-resolved);"></i> {{ __('messages.completed') }}</div>
                        <div class="mobile-meta-value font-mono">
                            @if($ticket->completed_at)
                                <span>{{ $ticket->completed_at->format('d/m/Y') }}</span>
                                <small class="text-muted font-weight-normal ml-1">{{ $ticket->completed_at->format('H:i') }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Status Selector & Action Row -->
                <div class="mobile-card-footer">
                    <div class="dropdown flex-grow-1" style="min-width: 0;">
                        @can('action_manage_tickets')
                        <button class="status-dropdown-btn status-btn w-100 d-flex justify-content-between align-items-center px-2.5 py-1.5 rounded" type="button" data-toggle="dropdown" aria-expanded="false" data-ticket-id="{{ $ticket->id }}" style="background: rgba(15, 23, 42, 0.7); border: 1px solid var(--hl-border-active); height: 40px;">
                            <span class="d-flex align-items-center text-truncate mr-1" style="gap: 6px;">
                                <span class="text-muted text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em; font-weight: 700;">Status:</span>
                                @switch($ticket->status)
                                    @case('Open') <span class="badge badge-primary status-badge" style="font-size: 0.75rem; padding: 4px 8px;">{{ __('messages.open') ?? 'Open' }}</span> @break
                                    @case('In Progress') <span class="badge badge-warning status-badge" style="font-size: 0.75rem; padding: 4px 8px;">{{ __('messages.in_progress') ?? 'In Progress' }}</span> @break
                                    @case('Resolved') <span class="badge badge-success status-badge" style="font-size: 0.75rem; padding: 4px 8px;">{{ __('messages.resolved') ?? 'Resolved' }}</span> @break
                                    @case('Closed') <span class="badge badge-secondary status-badge" style="font-size: 0.75rem; padding: 4px 8px;">{{ __('messages.closed') ?? 'Closed' }}</span> @break
                                @endswitch
                            </span>
                            <i class="fas fa-chevron-down text-muted flex-shrink-0" style="font-size: 0.68rem;"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-left status-menu-tactile w-100">
                            <a class="dropdown-item status-change-btn text-primary" href="#" data-status="Open"><i class="fas fa-dot-circle mr-2"></i> {{ __('messages.open') ?? 'Open' }}</a>
                            <a class="dropdown-item status-change-btn text-warning" href="#" data-status="In Progress"><i class="fas fa-spinner mr-2"></i> {{ __('messages.in_progress') ?? 'In Progress' }}</a>
                            <a class="dropdown-item status-change-btn text-success" href="#" data-status="Resolved"><i class="fas fa-check-circle mr-2"></i> {{ __('messages.resolved') ?? 'Resolved' }}</a>
                            <a class="dropdown-item status-change-btn text-secondary" href="#" data-status="Closed"><i class="fas fa-archive mr-2"></i> {{ __('messages.closed') ?? 'Closed' }}</a>
                        </div>
                        @else
                        <div class="status-btn px-3 py-1.5 rounded d-inline-flex align-items-center w-100" data-ticket-id="{{ $ticket->id }}" style="background: rgba(15, 23, 42, 0.7); border: 1px solid var(--hl-border-subtle); height: 40px;">
                            <span class="text-muted mr-1.5 text-uppercase" style="font-size: 0.68rem; font-weight: 700;">Status:</span>
                            @switch($ticket->status)
                                @case('Open') <span class="badge badge-primary status-badge">{{ __('messages.open') ?? 'Open' }}</span> @break
                                @case('In Progress') <span class="badge badge-warning status-badge">{{ __('messages.in_progress') ?? 'In Progress' }}</span> @break
                                @case('Resolved') <span class="badge badge-success status-badge">{{ __('messages.resolved') ?? 'Resolved' }}</span> @break
                                @case('Closed') <span class="badge badge-secondary status-badge">{{ __('messages.closed') ?? 'Closed' }}</span> @break
                            @endswitch
                        </div>
                        @endcan
                    </div>

                    @can('action_manage_tickets')
                    <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                        <a href="{{ route('tickets.edit', array_merge(['ticket' => $ticket->id], request()->query())) }}" class="btn-tactile-action btn-tactile-edit" title="{{ __('messages.edit') }}" style="width: 40px; height: 40px; border-radius: 10px;">
                            <i class="fas fa-edit" style="font-size: 0.88rem;"></i>
                        </a>
                        <form action="{{ route('tickets.destroy', array_merge(['ticket' => $ticket->id], request()->query())) }}" method="POST" class="d-inline m-0">
                            @csrf @method('DELETE')
                            <button type="button" class="btn-tactile-action btn-tactile-delete btn-delete border-0" title="{{ __('messages.delete') }}" data-confirm-message="{{ __('messages.confirm_delete') }}" style="width: 40px; height: 40px; border-radius: 10px;">
                                <i class="fas fa-trash" style="font-size: 0.88rem;"></i>
                            </button>
                        </form>
                    </div>
                    @endcan
                </div>
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

@push('scripts')
<script>
$(document).ready(function() {
    $(document).on('click', '.status-change-btn', function(e) {
        e.preventDefault();
        var btn = $(this);
        var newStatus = btn.data('status');
        var container = btn.closest('.dropdown');
        var ticketId = container.find('.status-btn').data('ticket-id');
        var badge = container.find('.status-badge');
        
        var originalHtml = badge.html();
        badge.html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: '{{ url("tickets") }}/' + ticketId + '/status',
            type: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                if(response.success) {
                    badge.removeClass('badge-primary badge-warning badge-success badge-secondary badge-dark');
                    badge.css('box-shadow', 'none');
                    
                    switch(newStatus) {
                        case 'Open':
                            badge.addClass('badge-primary');
                            badge.css('box-shadow', '0 0 8px rgba(0,123,255,0.5)');
                            badge.html('{{ __("messages.open") ?? "Open" }} <i class="fas fa-caret-down ml-1 opacity-75"></i>');
                            break;
                        case 'In Progress':
                            badge.addClass('badge-warning');
                            badge.css('box-shadow', '0 0 8px rgba(255,193,7,0.5)');
                            badge.html('{{ __("messages.in_progress") ?? "In Progress" }} <i class="fas fa-caret-down ml-1 opacity-75"></i>');
                            break;
                        case 'Resolved':
                            badge.addClass('badge-success');
                            badge.css('box-shadow', '0 0 8px rgba(40,167,69,0.5)');
                            badge.html('{{ __("messages.resolved") ?? "Resolved" }} <i class="fas fa-caret-down ml-1 opacity-75"></i>');
                            break;
                        case 'Closed':
                            badge.addClass('badge-secondary');
                            badge.html('{{ __("messages.closed") ?? "Closed" }} <i class="fas fa-caret-down ml-1 opacity-75"></i>');
                            break;
                    }
                }
            },
            error: function(xhr) {
                Swal.fire({ 
                    icon: 'error', 
                    text: '{{ __("messages.status_update_error") }}', 
                    toast: true, 
                    position: 'top-end', 
                    showConfirmButton: false, 
                    timer: 3000, 
                    background: '#0f172a', 
                    color: '#f8fafc', 
                    customClass: { popup: 'border border-danger' } 
                });
                badge.html(originalHtml);
            }
        });
    });
});
</script>
@endpush
@endsection
