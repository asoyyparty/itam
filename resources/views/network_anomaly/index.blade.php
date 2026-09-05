@extends('layouts.admin')

@section('title', __('messages.network_anomaly_title'))

@section('content')
<!-- Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 -->
<style>
    /* ═══════════════════════════════════════════════════════════════════
       NOC CYBER ARCHITECTURE & TELEMETRY STYLES (HALLMARK ANTI-AI-SLOP)
       ═══════════════════════════════════════════════════════════════════ */
    :root {
        --noc-bg-dark: #0b1120;
        --noc-card-bg: #111c35;
        --noc-card-hover: #162244;
        --noc-cyan: #06b6d4;
        --noc-cyan-glow: rgba(6, 182, 212, 0.25);
        --noc-emerald: #10b981;
        --noc-emerald-glow: rgba(16, 185, 129, 0.25);
        --noc-amber: #f59e0b;
        --noc-amber-glow: rgba(245, 158, 11, 0.25);
        --noc-crimson: #ef4444;
        --noc-crimson-glow: rgba(239, 68, 68, 0.25);
        --noc-indigo: #6366f1;
    }

    /* Command Station Header */
    .noc-header-card {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.85) 100%);
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 16px;
        padding: 20px 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
    }
    .noc-header-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #06b6d4 0%, #6366f1 50%, #10b981 100%);
    }
    .noc-header-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: rgba(6, 182, 212, 0.12);
        border: 1px solid rgba(6, 182, 212, 0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--noc-cyan);
        font-size: 1.35rem;
        box-shadow: 0 0 20px var(--noc-cyan-glow);
        flex-shrink: 0;
    }
    .noc-live-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        border-radius: 20px;
        background: rgba(16, 185, 129, 0.14);
        border: 1px solid rgba(16, 185, 129, 0.35);
        color: #34d399;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        font-family: 'JetBrains Mono', monospace;
    }
    .noc-pulse-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 10px #10b981;
        animation: nocPulse 2s infinite ease-in-out;
    }
    @keyframes nocPulse {
        0%, 100% { opacity: 1; transform: scale(1); box-shadow: 0 0 8px #10b981; }
        50% { opacity: 0.4; transform: scale(0.85); box-shadow: 0 0 2px #10b981; }
    }
    .noc-time-stamp {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 10px;
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(148, 163, 184, 0.18);
        color: #94a3b8;
        font-size: 0.78rem;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 600;
        min-height: 40px;
    }
    .noc-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 0.84rem;
        font-weight: 600;
        text-decoration: none !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid rgba(148, 163, 184, 0.2);
        background: rgba(30, 41, 59, 0.7);
        color: #e2e8f0;
        cursor: pointer;
        min-height: 40px;
    }
    .noc-action-btn:hover {
        background: rgba(51, 65, 85, 0.9);
        color: #ffffff;
        transform: translateY(-1px);
        border-color: rgba(148, 163, 184, 0.4);
    }
    .noc-action-btn.btn-cyan {
        background: rgba(6, 182, 212, 0.15);
        border-color: rgba(6, 182, 212, 0.4);
        color: #38bdf8;
    }
    .noc-action-btn.btn-cyan:hover {
        background: rgba(6, 182, 212, 0.25);
        color: #ffffff;
        box-shadow: 0 0 16px var(--noc-cyan-glow);
    }

    /* Bento Telemetry Metric Cards */
    .noc-metric-card {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 14px;
        padding: 20px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s;
    }
    .noc-metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(0, 0, 0, 0.35);
        border-color: rgba(148, 163, 184, 0.28);
    }
    .noc-metric-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
    }
    .noc-card-health::after { background: linear-gradient(90deg, #10b981, #06b6d4); }
    .noc-card-critical::after { background: linear-gradient(90deg, #ef4444, #f43f5e); }
    .noc-card-warning::after { background: linear-gradient(90deg, #f59e0b, #d97706); }
    .noc-card-telemetry::after { background: linear-gradient(90deg, #06b6d4, #6366f1); }

    .noc-metric-label {
        font-size: 0.72rem;
        letter-spacing: 0.08em;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .noc-metric-val {
        font-family: 'JetBrains Mono', monospace;
        font-size: 1.85rem;
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -0.02em;
        margin-bottom: 8px;
        display: flex;
        align-items: baseline;
        gap: 8px;
    }
    .noc-metric-footer {
        font-size: 0.78rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Circular Radial Gauge for Health */
    .health-gauge-box {
        position: relative;
        width: 56px;
        height: 56px;
        flex-shrink: 0;
    }
    .health-gauge-svg {
        transform: rotate(-90deg);
        width: 56px;
        height: 56px;
    }
    .health-gauge-bg {
        fill: none;
        stroke: rgba(148, 163, 184, 0.15);
        stroke-width: 5;
    }
    .health-gauge-fill {
        fill: none;
        stroke-width: 5;
        stroke-linecap: round;
        transition: stroke-dashoffset 1s ease-in-out;
    }
    .health-gauge-center {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.82rem;
        font-weight: 800;
    }

    /* Subnet Capacity Matrix Header */
    .vlan-header-strip {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: space-between !important;
        flex-wrap: nowrap !important;
        padding: 14px 20px !important;
        border-bottom: 1px solid rgba(148, 163, 184, 0.12) !important;
        background: transparent !important;
        width: 100% !important;
        box-sizing: border-box !important;
        gap: 12px;
    }
    .vlan-title-row {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        flex-wrap: nowrap !important;
        gap: 10px !important;
        min-width: 0 !important;
        flex: 1 1 auto !important;
    }
    .vlan-icon-box {
        width: 30px !important;
        height: 30px !important;
        border-radius: 8px !important;
        background: rgba(99, 102, 241, 0.15) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        color: #818cf8 !important;
        flex-shrink: 0 !important;
        font-size: 0.85rem !important;
    }
    .vlan-title-text {
        min-width: 0 !important;
        flex: 1 1 auto !important;
    }
    .vlan-swipe-hint {
        display: flex !important;
        align-items: center !important;
        flex-shrink: 0 !important;
        margin-left: 8px !important;
    }
    .vlan-matrix-card {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 12px;
        padding: 16px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .vlan-matrix-card:hover {
        transform: translateY(-2px);
        border-color: rgba(99, 102, 241, 0.4);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
    }
    .vlan-tag-badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        border-radius: 6px;
        background: rgba(99, 102, 241, 0.14);
        border: 1px solid rgba(99, 102, 241, 0.3);
        color: #818cf8;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.74rem;
        font-weight: 700;
    }
    .vlan-cidr-box {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.8rem;
        color: #cbd5e1;
        background: rgba(15, 23, 42, 0.6);
        padding: 3px 8px;
        border-radius: 6px;
        border: 1px solid rgba(148, 163, 184, 0.12);
        display: inline-block;
    }
    .capacity-progress {
        height: 6px;
        border-radius: 10px;
        background: rgba(148, 163, 184, 0.12);
        overflow: hidden;
        margin-top: 8px;
    }
    .capacity-bar {
        height: 100%;
        border-radius: 10px;
        transition: width 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Live Incident Feed & Filter Pills */
    .incident-card {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        overflow: hidden;
    }
    .incident-header {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 24px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.12);
        flex-wrap: wrap;
        background: rgba(15, 23, 42, 0.4);
    }
    .incident-tabs {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 2px;
    }
    .incident-pill-btn {
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px;
        padding: 7px 16px !important;
        border-radius: 20px !important;
        font-size: 0.82rem !important;
        font-weight: 600 !important;
        white-space: nowrap !important;
        text-decoration: none !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        border: 1px solid rgba(148, 163, 184, 0.2) !important;
        background: rgba(15, 23, 42, 0.4) !important;
        color: #94a3b8 !important;
        cursor: pointer;
        min-height: 40px;
    }
    .incident-pill-btn:hover {
        transform: translateY(-1px);
        color: #f8fafc !important;
        background: rgba(30, 41, 59, 0.7) !important;
        border-color: rgba(148, 163, 184, 0.4) !important;
    }
    .incident-pill-btn .count-badge {
        padding: 2px 7px;
        border-radius: 10px;
        font-size: 0.72rem;
        font-weight: 700;
        font-family: 'JetBrains Mono', monospace;
        background: rgba(255, 255, 255, 0.08);
    }
    .incident-pill-btn.active-all {
        background: rgba(6, 182, 212, 0.15) !important;
        border-color: rgba(6, 182, 212, 0.5) !important;
        color: #38bdf8 !important;
        box-shadow: 0 0 14px var(--noc-cyan-glow);
    }
    .incident-pill-btn.active-all .count-badge {
        background: rgba(6, 182, 212, 0.3);
        color: #ffffff;
    }
    .incident-pill-btn.active-critical {
        background: rgba(239, 68, 68, 0.15) !important;
        border-color: rgba(239, 68, 68, 0.5) !important;
        color: #f87171 !important;
        box-shadow: 0 0 14px var(--noc-crimson-glow);
    }
    .incident-pill-btn.active-critical .count-badge {
        background: rgba(239, 68, 68, 0.3);
        color: #ffffff;
    }
    .incident-pill-btn.active-warning {
        background: rgba(245, 158, 11, 0.15) !important;
        border-color: rgba(245, 158, 11, 0.5) !important;
        color: #fbbf24 !important;
        box-shadow: 0 0 14px var(--noc-amber-glow);
    }
    .incident-pill-btn.active-warning .count-badge {
        background: rgba(245, 158, 11, 0.3);
        color: #ffffff;
    }

    /* Anomaly Table Layout (Desktop) */
    .noc-table {
        min-width: 980px;
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    .noc-table thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background: var(--color-paper-1, #1e293b) !important;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        white-space: nowrap !important;
        padding: 14px 18px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.15) !important;
        color: #94a3b8;
    }
    .noc-table tbody td {
        padding: 14px 18px;
        vertical-align: middle;
        white-space: nowrap;
        background: var(--color-paper-0, #0f172a);
        border-bottom: 1px solid rgba(148, 163, 184, 0.08);
        transition: background-color 0.15s;
    }
    .noc-table tbody tr:hover td {
        background: rgba(30, 41, 59, 0.45);
    }
    .noc-table td.col-number {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 700;
        font-size: 0.85rem;
        color: #64748b;
        text-align: center;
        width: 50px;
    }

    /* Sticky Action Column (Desktop) */
    .noc-table th.col-action {
        position: sticky !important;
        right: 0 !important;
        top: 0 !important;
        z-index: 15 !important;
        background: var(--color-paper-1, #1e293b) !important;
        border-left: 1px solid rgba(148, 163, 184, 0.15) !important;
        box-shadow: -4px 0 10px rgba(0, 0, 0, 0.25);
    }
    .noc-table td.col-action {
        position: sticky !important;
        right: 0 !important;
        z-index: 5 !important;
        background: var(--color-paper-0, #0f172a) !important;
        border-left: 1px solid rgba(148, 163, 184, 0.15) !important;
        box-shadow: -4px 0 10px rgba(0, 0, 0, 0.25);
    }
    .noc-table tbody tr:hover td.col-action {
        background: var(--color-paper-1, #1e293b) !important;
    }

    /* Threat Severity Badges */
    .threat-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 8px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.74rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .threat-critical {
        background: rgba(239, 68, 68, 0.15);
        border: 1px solid rgba(239, 68, 68, 0.4);
        color: #f87171;
    }
    .threat-critical .threat-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #ef4444;
        box-shadow: 0 0 8px #ef4444;
        animation: threatPulse 1.5s infinite;
    }
    @keyframes threatPulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.3; transform: scale(0.8); }
    }
    .threat-warning {
        background: rgba(245, 158, 11, 0.15);
        border: 1px solid rgba(245, 158, 11, 0.4);
        color: #fbbf24;
    }
    .threat-warning .threat-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #f59e0b;
    }

    /* Monospace IP & MAC Pill */
    .telemetry-ip-pill {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.88rem;
        font-weight: 700;
        color: #38bdf8;
        letter-spacing: 0.02em;
    }
    .telemetry-mac-pill {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.74rem;
        color: #94a3b8;
        background: rgba(15, 23, 42, 0.6);
        padding: 2px 6px;
        border-radius: 4px;
        border: 1px solid rgba(148, 163, 184, 0.12);
        display: inline-block;
    }

    /* Diagnostics Callout */
    .diagnosis-callout {
        max-width: 320px;
        font-size: 0.8rem;
        line-height: 1.35;
        color: #cbd5e1;
        white-space: normal;
        word-break: break-word;
    }

    /* ═══════════════════════════════════════════════════════════════════
       MOBILE DEDICATED ARCHITECTURE & CARDS (ANTI-SLOP SCREEN ADAPTATION)
       ═══════════════════════════════════════════════════════════════════ */
    .noc-mobile-card {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 14px;
        padding: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
        position: relative;
        overflow: hidden;
        margin-bottom: 12px;
    }
    .mobile-num-pill {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.74rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 6px;
        background: rgba(148, 163, 184, 0.12);
        color: #94a3b8;
    }
    .mobile-telemetry-box {
        background: rgba(15, 23, 42, 0.7);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 10px;
        padding: 10px 12px;
    }
    .mobile-diagnosis-box {
        background: rgba(30, 41, 59, 0.4);
        border-left: 2px solid rgba(148, 163, 184, 0.3);
        border-radius: 0 8px 8px 0;
        padding: 8px 12px;
        font-size: 0.8rem;
        line-height: 1.4;
        color: #cbd5e1;
    }

    /* Responsive adjustments */
    @media (max-width: 991.98px) {
        .noc-header-card {
            padding: 16px 18px;
        }
        .noc-metric-val {
            font-size: 1.55rem;
        }
    }
    @media (max-width: 767.98px) {
        .incident-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
        }
        .incident-tabs {
            width: 100%;
        }
        .noc-header-icon {
            width: 42px;
            height: 42px;
            font-size: 1.15rem;
        }
        /* VLAN Subnet Matrix Horizontal Swipe Rack on Mobile */
        .vlan-header-strip {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: nowrap !important;
            padding: 10px 14px !important;
            gap: 8px !important;
        }
        .vlan-title-row {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
            gap: 10px !important;
            min-width: 0 !important;
        }
        .vlan-matrix-row {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            gap: 12px !important;
            padding: 4px 4px 12px 4px !important;
            -webkit-overflow-scrolling: touch !important;
            scroll-snap-type: x mandatory;
        }
        .vlan-matrix-row::-webkit-scrollbar {
            height: 4px;
        }
        .vlan-matrix-row::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.4);
            border-radius: 4px;
        }
        .vlan-matrix-row::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.4);
            border-radius: 4px;
        }
        .vlan-matrix-col {
            flex: 0 0 240px !important;
            max-width: 240px !important;
            scroll-snap-align: start;
            margin-bottom: 0 !important;
        }
    }

    @media (max-width: 575.98px) {
        .vlan-header-strip {
            padding: 10px 12px !important;
        }
        .noc-header-card {
            padding: 14px 16px;
            margin-bottom: 16px;
            border-radius: 14px;
        }
        .noc-header-wrapper {
            flex-direction: column;
            align-items: stretch !important;
            gap: 14px;
        }
        .noc-header-toolbar {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .noc-header-toolbar .noc-time-stamp {
            flex: 1 1 100%;
            justify-content: center;
        }
        .noc-header-toolbar .btn-rescan {
            flex: 1 1 auto;
            justify-content: center;
        }
        .noc-header-toolbar .noc-nav-link {
            flex: 0 0 42px;
            width: 42px;
            height: 42px;
            padding: 0;
            justify-content: center;
        }
        /* Compact 2x2 Telemetry Metric Cards */
        .noc-metric-card {
            padding: 12px 14px;
            border-radius: 12px;
        }
        .noc-metric-label {
            font-size: 0.65rem;
            margin-bottom: 4px;
        }
        .noc-metric-val {
            font-size: 1.35rem;
            margin-bottom: 4px;
        }
        .health-gauge-box {
            width: 38px;
            height: 38px;
        }
        .health-gauge-svg {
            width: 38px;
            height: 38px;
        }
        .health-gauge-center {
            font-size: 0.72rem;
        }
        .noc-metric-footer {
            font-size: 0.68rem;
            line-height: 1.2;
        }
    }
</style>

<!-- ═══════════════════════════════════════════════════════════════════
     1. NOC COMMAND STATION HEADER
     ═══════════════════════════════════════════════════════════════════ -->
<div class="noc-header-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap noc-header-wrapper" style="gap: 16px;">
        <!-- Title & Telemetry State -->
        <div class="d-flex align-items-center noc-header-left" style="gap: 14px; min-width: 0;">
            <div class="noc-header-icon">
                <i class="fas fa-satellite-dish"></i>
            </div>
            <div style="min-width: 0;">
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px; margin-bottom: 4px;">
                    <h4 class="mb-0 font-weight-bold theme-text" style="font-size: 1.25rem; letter-spacing: -0.01em;">
                        {{ __('messages.network_anomaly_title') }}
                    </h4>
                    <span class="noc-live-badge">
                        <span class="noc-pulse-dot"></span>
                        {{ __('messages.live_telemetry_engine') }}
                    </span>
                </div>
                <p class="text-muted mb-0" style="font-size: 0.82rem; line-height: 1.35; max-width: 720px;">
                    {{ __('messages.network_anomaly_desc') }}
                </p>
            </div>
        </div>

        <!-- Toolbar & Quick Navigation -->
        <div class="d-flex align-items-center flex-wrap noc-header-toolbar" style="gap: 8px;">
            <div class="noc-time-stamp" title="{{ __('messages.realtime_sweep') }}">
                <i class="far fa-clock text-info"></i>
                <span id="noc-live-clock">{{ now()->setTimezone('Asia/Jakarta')->format('H:i:s') }} WIB</span>
            </div>
            <button type="button" class="noc-action-btn btn-cyan btn-rescan" id="btn-rescan-network" title="{{ __('messages.refresh_telemetry') }}">
                <i class="fas fa-sync-alt" id="rescan-spinner"></i>
                <span>{{ __('messages.refresh_telemetry') }}</span>
            </button>
            <a href="{{ route('ips.index') }}" class="noc-action-btn noc-nav-link" title="{{ __('messages.quick_nav_ips') }}">
                <i class="fas fa-network-wired text-info"></i>
                <span class="d-none d-sm-inline">{{ __('messages.ip_address') }}</span>
            </a>
            <a href="{{ route('vlans.index') }}" class="noc-action-btn noc-nav-link" title="{{ __('messages.quick_nav_vlans') }}">
                <i class="fas fa-sitemap text-indigo" style="color: #818cf8;"></i>
                <span class="d-none d-sm-inline">{{ __('messages.vlan_config') }}</span>
            </a>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════
     2. BENTO TELEMETRY METRIC CARDS (2x2 Grid on Mobile)
     ═══════════════════════════════════════════════════════════════════ -->
<div class="row mb-4 g-2 g-md-3">
    <!-- Health Score Card with Radial Gauge -->
    <div class="col-6 col-xl-3 mb-3">
        <div class="noc-metric-card noc-card-health">
            <div>
                <div class="noc-metric-label">
                    <span>{{ __('messages.network_health_score') }}</span>
                    <i class="fas fa-shield-alt" style="color: {{ $summary['health_color'] }};"></i>
                </div>
                <div class="d-flex align-items-center justify-content-between my-1">
                    <div>
                        <div class="noc-metric-val" style="color: {{ $summary['health_color'] }}; margin-bottom: 4px;">
                            {{ $summary['health_score'] }}%
                        </div>
                        <span class="threat-badge" style="background: {{ $summary['health_color'] }}20; border: 1px solid {{ $summary['health_color'] }}50; color: {{ $summary['health_color'] }}; font-size: 0.7rem; padding: 2px 6px;">
                            @if($summary['health_status'] === 'Optimal')
                                {{ __('messages.health_optimal') }}
                            @elseif($summary['health_status'] === 'Degraded')
                                {{ __('messages.health_degraded') }}
                            @else
                                {{ __('messages.health_critical') }}
                            @endif
                        </span>
                    </div>
                    <!-- Mini Radial SVG -->
                    <div class="health-gauge-box">
                        <svg class="health-gauge-svg" viewBox="0 0 40 40">
                            <circle class="health-gauge-bg" cx="20" cy="20" r="15.5"></circle>
                            @php
                                $circumference = 2 * pi() * 15.5;
                                $dashoffset = $circumference - ($summary['health_score'] / 100) * $circumference;
                            @endphp
                            <circle class="health-gauge-fill" cx="20" cy="20" r="15.5"
                                    style="stroke: {{ $summary['health_color'] }}; stroke-dasharray: {{ $circumference }}; stroke-dashoffset: {{ $dashoffset }};">
                            </circle>
                        </svg>
                        <div class="health-gauge-center" style="color: {{ $summary['health_color'] }};">
                            <i class="fas {{ $summary['health_score'] >= 85 ? 'fa-check' : ($summary['health_score'] >= 60 ? 'fa-exclamation' : 'fa-times') }}"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="noc-metric-footer mt-2">
                <i class="fas fa-info-circle text-muted d-none d-sm-inline"></i>
                <span class="text-truncate">{{ __('messages.subnet_it_performance') }}</span>
            </div>
        </div>
    </div>

    <!-- Critical Incidents Card -->
    <div class="col-6 col-xl-3 mb-3">
        <div class="noc-metric-card noc-card-critical">
            <div>
                <div class="noc-metric-label">
                    <span class="text-danger">{{ __('messages.active_critical_anomalies') }}</span>
                    <i class="fas fa-radiation text-danger" style="font-size: 0.95rem;"></i>
                </div>
                <div class="noc-metric-val text-danger">
                    {{ $summary['critical_count'] }}
                    @if($summary['critical_count'] > 0)
                        <span class="threat-badge threat-critical d-none d-sm-inline-flex" style="font-size: 0.68rem; padding: 2px 6px;">
                            <span class="threat-dot"></span> {{ __('messages.critical_alert') }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="noc-metric-footer">
                <i class="fas fa-bolt text-danger d-none d-sm-inline"></i>
                <span class="text-truncate">{{ __('messages.ip_conflict_rogue') }}</span>
            </div>
        </div>
    </div>

    <!-- Latency & Node Warnings Card -->
    <div class="col-6 col-xl-3 mb-3">
        <div class="noc-metric-card noc-card-warning">
            <div>
                <div class="noc-metric-label">
                    <span class="text-warning">{{ __('messages.latency_warnings') }}</span>
                    <i class="fas fa-wifi text-warning" style="font-size: 0.95rem;"></i>
                </div>
                <div class="noc-metric-val text-warning">
                    {{ $summary['warning_count'] }}
                    @if($summary['warning_count'] > 0)
                        <span class="threat-badge threat-warning d-none d-sm-inline-flex" style="font-size: 0.68rem; padding: 2px 6px;">
                            {{ __('messages.warning_alert') }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="noc-metric-footer">
                <i class="fas fa-exclamation-circle text-warning d-none d-sm-inline"></i>
                <span class="text-truncate">{{ __('messages.high_latency_node_down') }}</span>
            </div>
        </div>
    </div>

    <!-- Active RTT & Telemetry Card -->
    <div class="col-6 col-xl-3 mb-3">
        <div class="noc-metric-card noc-card-telemetry">
            <div>
                <div class="noc-metric-label">
                    <span style="color: #38bdf8;">{{ __('messages.avg_ping_latency') }}</span>
                    <i class="fas fa-wave-square" style="color: #38bdf8; font-size: 0.95rem;"></i>
                </div>
                <div class="noc-metric-val" style="color: #38bdf8;">
                    {{ $summary['avg_latency'] }} <span style="font-size: 0.9rem; font-weight: 500; color: #94a3b8;">ms</span>
                </div>
            </div>
            <div class="noc-metric-footer">
                <i class="fas fa-server text-info d-none d-sm-inline"></i>
                <span class="text-truncate">{{ __('messages.total_monitored_ips') }} <strong class="text-white">{{ $summary['total_ips'] }}</strong></span>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════
     3. SUBNET & VLAN CAPACITY MATRIX (Horizontal Rack on Mobile)
     ═══════════════════════════════════════════════════════════════════ -->
<div class="card theme-card shadow-sm mb-4" style="border: 1px solid rgba(148, 163, 184, 0.14); border-radius: 16px; overflow: hidden;">
    <div class="vlan-header-strip">
        <div class="vlan-title-row">
            <div class="vlan-icon-box">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div class="vlan-title-text">
                <h6 class="mb-0 font-weight-bold theme-text text-truncate" style="font-size: 0.92rem; line-height: 1.2;">
                    {{ __('messages.subnet_capacity_title') }}
                </h6>
                <small class="text-muted d-none d-md-block text-truncate" style="font-size: 0.74rem; line-height: 1.2;">{{ __('messages.subnet_capacity_desc') }}</small>
            </div>
        </div>
        <div class="vlan-swipe-hint">
            <small class="text-muted d-block d-md-none" style="font-size: 0.72rem; font-weight: 600; white-space: nowrap;">
                <i class="fas fa-arrows-alt-h mr-1 text-info"></i> {{ __('messages.swipe_horizontal') }}
            </small>
        </div>
    </div>
    <div class="card-body p-3">
        <div class="row vlan-matrix-row">
            @forelse($summary['vlans'] as $vlan)
                @php
                    $cidr = 24;
                    if (preg_match('/\/(\d+)$/', $vlan->subnet ?? '', $m)) {
                        $cidr = (int) $m[1];
                    }
                    $maxIps = ($cidr >= 16 && $cidr <= 30) ? (pow(2, 32 - $cidr) - 2) : 254;
                    $maxIps = max(1, $maxIps);
                    $utilPct = min(100, round(($vlan->ip_addresses_count / $maxIps) * 100));
                    $tierColor = $utilPct >= 90 ? '#ef4444' : ($utilPct >= 70 ? '#f59e0b' : '#10b981');
                @endphp
                <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-3 vlan-matrix-col">
                    <div class="vlan-matrix-card">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="vlan-tag-badge">VLAN {{ $vlan->vlan_number }}</span>
                                <span class="badge" style="background: {{ $tierColor }}20; color: {{ $tierColor }}; font-family: 'JetBrains Mono', monospace; font-size: 0.72rem; font-weight: 700;">
                                    {{ $utilPct }}%
                                </span>
                            </div>
                            <h6 class="font-weight-bold mb-1 theme-text text-truncate" title="{{ $vlan->name }}" style="font-size: 0.92rem;">
                                {{ $vlan->name }}
                            </h6>
                            <div class="mb-2">
                                <span class="vlan-cidr-box">{{ $vlan->subnet ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <div>
                            <div class="d-flex justify-content-between align-items-center" style="font-size: 0.75rem; color: #94a3b8;">
                                <span>{{ __('messages.ips_allocated') }}</span>
                                <span class="font-weight-bold theme-text" style="font-family: 'JetBrains Mono', monospace;">
                                    {{ $vlan->ip_addresses_count }} / {{ $maxIps }}
                                </span>
                            </div>
                            <div class="capacity-progress">
                                <div class="capacity-bar" style="width: {{ max(6, $utilPct) }}%; background: {{ $tierColor }};"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-muted small py-3 text-center">
                    <i class="fas fa-info-circle mr-1"></i> {{ __('messages.no_vlans_configured') }}
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════
     4. LIVE INCIDENT DETECTIONS (Dual-Mode: Desktop Table & Mobile Cards)
     ═══════════════════════════════════════════════════════════════════ -->
<div class="incident-card mb-4">
    <!-- Header with Filter Tabs -->
    <div class="incident-header">
        <div class="d-flex align-items-center" style="gap: 10px;">
            <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(239, 68, 68, 0.14); border: 1px solid rgba(239, 68, 68, 0.3); display: flex; align-items: center; justify-content: center; color: #f87171;">
                <i class="fas fa-shield-virus"></i>
            </div>
            <div>
                <h5 class="mb-0 font-weight-bold theme-text" style="font-size: 1.05rem;">
                    {{ __('messages.realtime_anomaly_list') }}
                </h5>
                <small class="text-muted d-block" style="font-size: 0.76rem;">{{ __('messages.all_filter') }}: {{ $summary['total_anomalies'] }} {{ __('messages.active_findings') }}</small>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="incident-tabs">
            <a href="{{ route('network-anomalies.index', ['severity' => 'all']) }}" 
               class="incident-pill-btn {{ $severityFilter == 'all' ? 'active-all' : '' }}">
                <i class="fas fa-layer-group text-xs"></i>
                <span>{{ __('messages.all_filter') }}</span>
                <span class="count-badge">{{ $summary['total_anomalies'] }}</span>
            </a>
            <a href="{{ route('network-anomalies.index', ['severity' => 'Critical']) }}" 
               class="incident-pill-btn {{ $severityFilter == 'Critical' ? 'active-critical' : '' }}">
                <i class="fas fa-radiation text-xs"></i>
                <span>{{ __('messages.critical_alert') }}</span>
                <span class="count-badge">{{ $summary['critical_count'] }}</span>
            </a>
            <a href="{{ route('network-anomalies.index', ['severity' => 'Warning']) }}" 
               class="incident-pill-btn {{ $severityFilter == 'Warning' ? 'active-warning' : '' }}">
                <i class="fas fa-wifi text-xs"></i>
                <span>{{ __('messages.warning_alert') }}</span>
                <span class="count-badge">{{ $summary['warning_count'] }}</span>
            </a>
        </div>
    </div>

    <!-- Mode Desktop: High-Density Table with Sticky Action Column (d-none d-md-block) -->
    <div class="d-none d-md-block table-responsive" style="margin-bottom: 0;">
        <table class="table align-middle mb-0 noc-table">
            <thead>
                <tr>
                    <th class="text-center col-number">{{ __('messages.no') }}</th>
                    <th>{{ __('messages.anomaly_type') }}</th>
                    <th>{{ __('messages.risk_level') }}</th>
                    <th>{{ __('messages.ip_mac_address') }}</th>
                    <th>{{ __('messages.subnet_vlan') }}</th>
                    <th>{{ __('messages.diagnosis_description') }}</th>
                    <th>{{ __('messages.detected_time') }}</th>
                    <th class="text-right pr-4 col-action">{{ __('messages.remediation_action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($anomalies as $index => $anom)
                    <tr id="anomaly-row-{{ $anom['ip_id'] }}">
                        <td class="col-number">{{ $loop->iteration }}</td>
                        <td>
                            <strong class="theme-text" style="font-size: 0.9rem; display: block;">{{ $anom['type'] }}</strong>
                            @if($anom['asset'])
                                <small class="text-info d-inline-flex align-items-center mt-1" style="gap: 4px;">
                                    <i class="fas fa-desktop" style="font-size: 0.72rem;"></i> {{ $anom['asset'] }}
                                </small>
                            @else
                                <small class="text-muted d-inline-flex align-items-center mt-1" style="gap: 4px;">
                                    <i class="fas fa-question-circle" style="font-size: 0.72rem;"></i> {{ __('messages.unregistered_host') }}
                                </small>
                            @endif
                        </td>
                        <td>
                            @if($anom['severity'] === 'Critical')
                                <span class="threat-badge threat-critical">
                                    <span class="threat-dot"></span> {{ __('messages.critical_alert') }}
                                </span>
                            @else
                                <span class="threat-badge threat-warning">
                                    <span class="threat-dot"></span> {{ __('messages.warning_alert') }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="telemetry-ip-pill">{{ $anom['ip_address'] }}</div>
                            <div class="telemetry-mac-pill mt-1">MAC: {{ $anom['mac_address'] }}</div>
                        </td>
                        <td>
                            <span class="vlan-tag-badge" style="background: rgba(99, 102, 241, 0.1); border-color: rgba(99, 102, 241, 0.25);">
                                {{ $anom['vlan'] }}
                            </span>
                        </td>
                        <td>
                            <div class="diagnosis-callout">
                                {{ $anom['description'] }}
                            </div>
                        </td>
                        <td>
                            <small class="text-muted" style="font-family: 'JetBrains Mono', monospace; font-size: 0.76rem;">
                                <i class="far fa-clock mr-1"></i> {{ $anom['detected_at'] }}
                            </small>
                        </td>
                        <td class="text-right pr-4 col-action">
                            <div class="d-flex justify-content-end align-items-center" style="gap: 6px;">
                                @if(str_contains($anom['type'], 'Rogue') || str_contains($anom['type'], 'Perangkat'))
                                    <button type="button" class="btn btn-sm btn-outline-info btn-register-rogue" 
                                            data-ip-id="{{ $anom['ip_id'] }}" 
                                            title="{{ __('messages.register_asset') }}"
                                            style="border-radius: 8px; font-weight: 600; font-size: 0.78rem; padding: 5px 10px; min-height: 38px;">
                                        <i class="fas fa-plus-circle mr-1"></i> {{ __('messages.register_asset') }}
                                    </button>
                                @endif
                                <button type="button" class="btn btn-sm btn-outline-success btn-resolve-anomaly" 
                                        data-ip-id="{{ $anom['ip_id'] }}" 
                                        title="{{ __('messages.remediate') }}"
                                        style="border-radius: 8px; font-weight: 600; font-size: 0.78rem; padding: 5px 12px; min-height: 38px;">
                                    <i class="fas fa-check-circle mr-1"></i> {{ __('messages.remediate') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 border-0">
                            <div class="d-flex flex-column align-items-center justify-content-center py-5 px-3 mx-auto" style="max-width: 520px; gap: 16px;">
                                <div style="width: 72px; height: 72px; border-radius: 50%; background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.35); display: flex; align-items: center; justify-content: center; color: #34d399; font-size: 2.2rem; box-shadow: 0 0 25px rgba(16, 185, 129, 0.2);">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="text-center">
                                    <h5 class="font-weight-bold theme-text mb-1" style="font-size: 1.15rem; color: #f8fafc;">
                                        {{ __('messages.all_systems_nominal') }}
                                    </h5>
                                    <p class="text-muted mb-0" style="font-size: 0.84rem; line-height: 1.4;">
                                        {{ __('messages.all_systems_nominal_desc') }}
                                    </p>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mode Mobile: Tactile Incident Cards Feed (d-block d-md-none) -->
    <div class="d-block d-md-none p-2 p-sm-3 noc-mobile-incident-feed">
        @forelse($anomalies as $index => $anom)
            <div class="noc-mobile-card" id="mobile-anomaly-row-{{ $anom['ip_id'] }}" style="border-left: 4px solid {{ $anom['severity'] === 'Critical' ? '#ef4444' : '#f59e0b' }};">
                <!-- Top Row: #, Risk Badge, and Timestamp -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center" style="gap: 6px;">
                        <span class="mobile-num-pill">{{ __('messages.no') }} {{ $loop->iteration }}</span>
                        @if($anom['severity'] === 'Critical')
                            <span class="threat-badge threat-critical" style="font-size: 0.68rem; padding: 2px 7px;">
                                <span class="threat-dot"></span> {{ __('messages.critical_alert') }}
                            </span>
                        @else
                            <span class="threat-badge threat-warning" style="font-size: 0.68rem; padding: 2px 7px;">
                                <span class="threat-dot"></span> {{ __('messages.warning_alert') }}
                            </span>
                        @endif
                    </div>
                    <small class="text-muted" style="font-family: 'JetBrains Mono', monospace; font-size: 0.72rem;">
                        <i class="far fa-clock mr-1"></i> {{ $anom['detected_at'] }}
                    </small>
                </div>

                <!-- Incident Type & Associated Device -->
                <div class="mb-2">
                    <h6 class="font-weight-bold theme-text mb-1" style="font-size: 0.94rem; line-height: 1.25;">
                        {{ $anom['type'] }}
                    </h6>
                    @if($anom['asset'])
                        <small class="text-info d-inline-flex align-items-center" style="gap: 4px; font-weight: 600;">
                            <i class="fas fa-desktop" style="font-size: 0.72rem;"></i> {{ $anom['asset'] }}
                        </small>
                    @else
                        <small class="text-muted d-inline-flex align-items-center" style="gap: 4px;">
                            <i class="fas fa-question-circle" style="font-size: 0.72rem;"></i> {{ __('messages.unregistered_host') }}
                        </small>
                    @endif
                </div>

                <!-- Host Telemetry Box (Monospace IP, MAC, VLAN) -->
                <div class="mobile-telemetry-box mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="telemetry-ip-pill" style="font-size: 0.95rem;">{{ $anom['ip_address'] }}</span>
                        <span class="vlan-tag-badge" style="font-size: 0.7rem;">{{ $anom['vlan'] }}</span>
                    </div>
                    <div class="telemetry-mac-pill">MAC: {{ $anom['mac_address'] }}</div>
                </div>

                <!-- Diagnosis Callout -->
                <div class="mobile-diagnosis-box mb-3">
                    {{ $anom['description'] }}
                </div>

                <!-- Quick Action Buttons -->
                <div class="d-flex align-items-center justify-content-end" style="gap: 8px;">
                    @if(str_contains($anom['type'], 'Rogue') || str_contains($anom['type'], 'Perangkat'))
                        <button type="button" class="btn btn-sm btn-outline-info btn-register-rogue" 
                                data-ip-id="{{ $anom['ip_id'] }}" 
                                title="{{ __('messages.register_asset') }}"
                                style="border-radius: 8px; font-weight: 600; font-size: 0.8rem; min-height: 40px; padding: 7px 12px; flex: 1;">
                            <i class="fas fa-plus-circle mr-1"></i> {{ __('messages.register_asset') }}
                        </button>
                    @endif
                    <button type="button" class="btn btn-sm btn-outline-success btn-resolve-anomaly" 
                            data-ip-id="{{ $anom['ip_id'] }}" 
                            title="{{ __('messages.remediate') }}"
                            style="border-radius: 8px; font-weight: 600; font-size: 0.8rem; min-height: 40px; padding: 7px 14px; flex: 1;">
                        <i class="fas fa-check-circle mr-1"></i> {{ __('messages.remediate') }}
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-5 px-3">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.35); display: flex; align-items: center; justify-content: center; color: #34d399; font-size: 1.8rem; margin: 0 auto 12px;">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h6 class="font-weight-bold theme-text mb-1" style="color: #f8fafc;">{{ __('messages.all_systems_nominal') }}</h6>
                <small class="text-muted">{{ __('messages.all_systems_nominal_desc') }}</small>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // 1. Remediate Anomaly via AJAX (Handles both desktop row & mobile card)
    $(document).on('click', '.btn-resolve-anomaly', function() {
        const btn = $(this);
        const ipId = btn.data('ip-id');
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: "{{ route('network-anomalies.resolve') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                ip_id: ipId
            },
            success: function(response) {
                if (response.success) {
                    $('#anomaly-row-' + ipId + ', #mobile-anomaly-row-' + ipId).fadeOut(350, function() { 
                        $(this).remove(); 
                        // If no more incidents left, reload to refresh KPI strip
                        if ($('.noc-table tbody tr').length === 0 && $('.noc-mobile-incident-feed .noc-mobile-card').length === 0) {
                            window.location.reload();
                        }
                    });
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ 
                            icon: 'success', 
                            text: response.message || "{{ __('messages.remediation_success') }}", 
                            toast: true, 
                            position: 'top-end', 
                            showConfirmButton: false, 
                            timer: 3000, 
                            background: '#0f172a', 
                            color: '#f8fafc', 
                            customClass: { popup: 'border border-success' } 
                        });
                    } else if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    }
                }
            },
            error: function() {
                btn.prop('disabled', false).html(originalHtml);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ 
                        icon: 'error', 
                        text: "{{ __('messages.remediation_failed') }}", 
                        toast: true, 
                        position: 'top-end', 
                        showConfirmButton: false, 
                        timer: 3000, 
                        background: '#0f172a', 
                        color: '#f8fafc', 
                        customClass: { popup: 'border border-danger' } 
                    });
                }
            }
        });
    });

    // 2. Register Rogue Device as Asset
    $(document).on('click', '.btn-register-rogue', function() {
        const btn = $(this);
        const ipId = btn.data('ip-id');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: "{{ route('network-anomalies.register-rogue') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                ip_id: ipId
            },
            success: function(response) {
                if (response.success && response.redirect_url) {
                    window.location.href = response.redirect_url;
                } else {
                    btn.prop('disabled', false).html('<i class="fas fa-plus-circle mr-1"></i> {{ __("messages.register_asset") }}');
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<i class="fas fa-plus-circle mr-1"></i> {{ __("messages.register_asset") }}');
            }
        });
    });

    // 3. Rescan Telemetry Button Action
    $('#btn-rescan-network').on('click', function() {
        const btn = $(this);
        const spinner = $('#rescan-spinner');
        btn.prop('disabled', true);
        spinner.addClass('fa-spin');
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: "{{ __('messages.scanning_telemetry') }}",
                text: "{{ __('messages.realtime_sweep') }}...",
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                background: '#0f172a',
                color: '#f8fafc',
                customClass: { popup: 'border border-info' }
            });
        }

        setTimeout(function() {
            window.location.reload();
        }, 800);
    });
});
</script>
@endpush
@endsection
