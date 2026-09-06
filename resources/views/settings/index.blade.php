@extends('layouts.admin')

@section('title', __('messages.system_settings'))

@section('content')
<!-- Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 -->
<!-- Macrostructure: Workbench · Archetype: 05-Workbench -->
<style>
    /* ═══════════════════════════════════════════════════════════════════
       HALLMARK OBSIDIAN WORKBENCH: SYSTEM SETTINGS & GATEWAY DISPATCH
       Pre-emit: P5 H5 E5 S5 R5 V5 | Zero Slop, Locked Tokens, Pure Craft
       ═══════════════════════════════════════════════════════════════════ */
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
        --hl-accent-indigo: #6366f1;
        --hl-accent-emerald: #10b981;
        --hl-accent-amber: #f59e0b;
        --hl-accent-purple: #a855f7;
        --hl-accent-rose: #f43f5e;
        --hl-font-mono: 'JetBrains Mono', ui-monospace, monospace;
    }

    .content-header {
        display: none !important;
    }

    /* 1. Header Command Banner */
    .settings-header-card {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid var(--hl-border-subtle);
        border-radius: 16px;
        padding: 20px 24px;
        position: relative;
        overflow: hidden;
        margin-bottom: 20px;
    }
    .settings-header-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, #6366f1 0%, #38bdf8 35%, #10b981 70%, #a855f7 100%);
    }
    .settings-header-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .settings-header-left {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
        flex: 1 1 auto;
    }
    .settings-header-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: rgba(99, 102, 241, 0.12);
        border: 1px solid rgba(99, 102, 241, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #818cf8;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .settings-header-title {
        font-size: 1.25rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        color: var(--color-ink-0, #f8fafc);
        margin: 0;
        line-height: 1.25;
    }
    .settings-header-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.78rem;
        color: var(--color-ink-2, #94a3b8);
        margin-top: 4px;
        flex-wrap: wrap;
    }
    .settings-badge-live {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 2px 8px;
        border-radius: 9999px;
        background: rgba(16, 185, 129, 0.12);
        border: 1px solid rgba(16, 185, 129, 0.28);
        color: #34d399;
        font-family: var(--hl-font-mono);
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.04em;
    }
    .settings-badge-pulse {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 6px rgba(16, 185, 129, 0.8);
        animation: settingsPulse 2.4s infinite ease-in-out;
    }
    @keyframes settingsPulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.85); }
    }

    /* Clearance Badges */
    .badge-clearance-superadmin {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 10px;
        background: rgba(245, 158, 11, 0.12);
        border: 1px solid rgba(245, 158, 11, 0.35);
        color: #fbbf24;
        font-family: var(--hl-font-mono);
        font-size: 0.74rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        white-space: nowrap;
    }
    .badge-clearance-standard {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 10px;
        background: rgba(56, 189, 248, 0.1);
        border: 1px solid rgba(56, 189, 248, 0.25);
        color: #38bdf8;
        font-family: var(--hl-font-mono);
        font-size: 0.74rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        white-space: nowrap;
    }

    /* ═══════════════════════════════════════════════════════════════════
       2. WORKBENCH TWO-COLUMN ARCHITECTURE
       ═══════════════════════════════════════════════════════════════════ */
    .settings-workbench {
        display: grid;
        grid-template-columns: 290px minmax(0, 1fr);
        gap: 20px;
        width: 100%;
        max-width: 100%;
        align-items: start;
    }

    /* Left Command Rail */
    .settings-rail {
        display: flex;
        flex-direction: column;
        gap: 16px;
        position: sticky;
        top: 80px;
    }
    .rail-group-card {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid var(--hl-border-subtle);
        border-radius: 16px;
        padding: 12px;
        overflow: hidden;
    }
    .rail-group-header {
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--hl-text-dim);
        padding: 8px 12px 6px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .rail-nav-list {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .rail-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 10px;
        color: var(--hl-text-muted);
        text-decoration: none !important;
        border: 1px solid transparent;
        transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
        position: relative;
    }
    .rail-link:hover {
        background: rgba(255, 255, 255, 0.04);
        color: var(--hl-text-bright);
        transform: translateX(2px);
    }
    .rail-link.active {
        background: rgba(99, 102, 241, 0.1) !important;
        border-color: rgba(99, 102, 241, 0.32) !important;
        color: #ffffff !important;
    }
    .rail-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 18%;
        height: 64%;
        width: 3px;
        border-radius: 0 4px 4px 0;
        background: #6366f1;
        box-shadow: 0 0 8px #6366f1;
    }
    .rail-icon-box {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.92rem;
        flex-shrink: 0;
        transition: all 0.18s ease;
        background: rgba(148, 163, 184, 0.08);
        border: 1px solid rgba(148, 163, 184, 0.16);
        color: var(--hl-text-muted);
    }
    .rail-link.active .rail-icon-box {
        background: var(--rail-accent, #6366f1) !important;
        border-color: var(--rail-accent, #6366f1) !important;
        color: #ffffff !important;
        box-shadow: 0 2px 10px rgba(99, 102, 241, 0.35);
    }
    .rail-text-box {
        flex: 1;
        min-width: 0;
    }
    .rail-title {
        font-weight: 600;
        font-size: 0.85rem;
        line-height: 1.25;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .rail-desc {
        font-size: 0.7rem;
        color: var(--hl-text-dim);
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .rail-link.active .rail-desc {
        color: #cbd5e1;
    }
    .rail-status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 5px rgba(16, 185, 129, 0.7);
        flex-shrink: 0;
    }
    .rail-status-dot.dot-amber {
        background: #f59e0b;
        box-shadow: 0 0 5px rgba(245, 158, 11, 0.7);
    }
    .rail-status-dot.dot-dim {
        background: #64748b;
        box-shadow: none;
    }

    /* Right Configuration Canvas */
    .settings-canvas {
        display: flex;
        flex-direction: column;
        gap: 20px;
        min-width: 0;
    }

    /* Tab Header Panel */
    .canvas-tab-header {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid var(--hl-border-subtle);
        border-radius: 16px;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .canvas-tab-left {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }
    .canvas-tab-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .canvas-tab-title {
        font-size: 1.15rem;
        font-weight: 700;
        letter-spacing: -0.015em;
        color: var(--color-ink-0, #f8fafc);
        margin: 0;
    }
    .canvas-tab-subtitle {
        font-size: 0.78rem;
        color: var(--color-ink-2, #94a3b8);
        margin-top: 3px;
    }

    /* Bento Configuration Cards */
    .bento-config-card {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid var(--hl-border-subtle);
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 20px;
    }
    .bento-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--hl-border-subtle);
        background: rgba(255, 255, 255, 0.015);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .bento-header-title {
        font-size: 0.88rem;
        font-weight: 700;
        letter-spacing: -0.01em;
        color: var(--color-ink-0, #f8fafc);
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }
    .bento-header-badge {
        font-family: var(--hl-font-mono);
        font-size: 0.68rem;
        padding: 2px 7px;
        border-radius: 6px;
        background: rgba(148, 163, 184, 0.1);
        border: 1px solid rgba(148, 163, 184, 0.2);
        color: var(--hl-text-muted);
        font-weight: 500;
    }
    .bento-card-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    /* Field Rows */
    .config-field-row {
        display: grid;
        grid-template-columns: 240px minmax(0, 1fr);
        gap: 20px;
        align-items: center;
        padding-bottom: 16px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.08);
    }
    .config-field-row:last-child {
        padding-bottom: 0;
        border-bottom: none;
    }
    .config-label-box {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .config-label {
        font-size: 0.86rem;
        font-weight: 600;
        color: var(--color-ink-0, #f8fafc);
        margin: 0;
    }
    .config-hint {
        font-size: 0.74rem;
        color: var(--color-ink-2, #94a3b8);
        line-height: 1.35;
        margin: 0;
    }
    .config-control-box {
        min-width: 0;
    }

    /* Input Controls */
    .tactile-input {
        width: 100%;
        height: 42px;
        border-radius: 10px;
        border: 1px solid rgba(148, 163, 184, 0.22);
        background: rgba(15, 23, 42, 0.45);
        color: var(--color-ink-0, #f8fafc);
        font-size: 0.88rem;
        padding: 0 14px;
        outline: none;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .tactile-input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        background: rgba(15, 23, 42, 0.65);
    }
    .tactile-input:disabled {
        opacity: 0.55;
        cursor: not-allowed;
        background: rgba(15, 23, 42, 0.25);
    }
    .tactile-select {
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        background-size: 14px;
        padding-right: 38px;
        cursor: pointer;
    }
    .tactile-select option {
        background: #0f172a;
        color: #f8fafc;
        padding: 8px;
    }

    /* Vault Secret Input Group */
    .vault-secret-group {
        display: flex;
        align-items: stretch;
        width: 100%;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, 0.22);
        background: rgba(15, 23, 42, 0.45);
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .vault-secret-group:focus-within {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    }
    .vault-secret-input {
        flex: 1;
        min-width: 0;
        height: 42px;
        border: none;
        background: transparent;
        color: var(--color-ink-0, #f8fafc);
        font-family: var(--hl-font-mono);
        font-size: 0.84rem;
        padding: 0 14px;
        outline: none;
    }
    .vault-secret-input:disabled {
        opacity: 0.55;
        cursor: not-allowed;
    }
    .vault-btn-action {
        width: 42px;
        height: 42px;
        border: none;
        border-left: 1px solid rgba(148, 163, 184, 0.14);
        background: transparent;
        color: var(--hl-text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.15s ease, color 0.15s ease;
        outline: none;
    }
    .vault-btn-action:hover {
        background: rgba(255, 255, 255, 0.05);
        color: var(--color-ink-0, #f8fafc);
    }

    /* Mechanical Tactile Toggle Switch */
    .tactile-switch-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        user-select: none;
    }
    .tactile-switch-btn {
        width: 52px;
        height: 28px;
        border-radius: 9999px;
        background: rgba(148, 163, 184, 0.2);
        border: 1px solid rgba(148, 163, 184, 0.3);
        position: relative;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        outline: none;
        padding: 0;
    }
    .tactile-switch-knob {
        position: absolute;
        top: 2px;
        left: 2px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #cbd5e1;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .tactile-switch-btn.is-active {
        background: rgba(16, 185, 129, 0.25);
        border-color: rgba(16, 185, 129, 0.5);
    }
    .tactile-switch-btn.is-active .tactile-switch-knob {
        left: 26px;
        background: #10b981;
        box-shadow: 0 0 8px rgba(16, 185, 129, 0.6);
    }
    .tactile-switch-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .tactile-switch-label {
        font-family: var(--hl-font-mono);
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        color: var(--hl-text-dim);
    }
    .tactile-switch-btn.is-active + .tactile-switch-label {
        color: #34d399;
    }

    /* Live Telemetry Action Panel */
    .live-test-panel {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 14px 18px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px dashed var(--hl-border-subtle);
        flex-wrap: wrap;
    }
    .live-test-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .live-test-icon {
        font-size: 1.1rem;
        opacity: 0.8;
    }
    .live-test-text {
        font-size: 0.8rem;
        color: var(--hl-text-muted);
    }
    .btn-live-test {
        height: 38px;
        padding: 0 16px;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        border: 1px solid transparent;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .btn-live-test-emerald {
        background: rgba(16, 185, 129, 0.15);
        border-color: rgba(16, 185, 129, 0.35);
        color: #34d399;
    }
    .btn-live-test-emerald:hover {
        background: rgba(16, 185, 129, 0.25);
        color: #ffffff;
    }
    .btn-live-test-sky {
        background: rgba(56, 189, 248, 0.15);
        border-color: rgba(56, 189, 248, 0.35);
        color: #38bdf8;
    }
    .btn-live-test-sky:hover {
        background: rgba(56, 189, 248, 0.25);
        color: #ffffff;
    }

    /* Runtime Specs Table */
    .specs-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }
    .spec-item {
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid var(--hl-border-subtle);
        border-radius: 10px;
        padding: 10px 14px;
    }
    .spec-label {
        font-size: 0.68rem;
        color: var(--hl-text-dim);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
    }
    .spec-value {
        font-family: var(--hl-font-mono);
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--color-ink-0, #f8fafc);
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Bottom Sticky Action Bar */
    .canvas-actions-bar {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid var(--hl-border-subtle);
        border-radius: 16px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-top: 8px;
    }
    .actions-bar-note {
        font-size: 0.78rem;
        color: var(--hl-text-dim);
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .btn-tactile-save {
        height: 44px;
        padding: 0 28px;
        border-radius: 12px;
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        border: 1px solid rgba(99, 102, 241, 0.5);
        color: #ffffff;
        font-weight: 600;
        font-size: 0.88rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(99, 102, 241, 0.35);
        transition: all 0.18s ease;
    }
    .btn-tactile-save:hover {
        background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
        transform: translateY(-1px);
    }
    .btn-tactile-reset {
        height: 44px;
        padding: 0 20px;
        border-radius: 12px;
        background: transparent;
        border: 1px solid var(--hl-border-subtle);
        color: var(--hl-text-muted);
        font-weight: 600;
        font-size: 0.86rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-tactile-reset:hover {
        background: rgba(255, 255, 255, 0.04);
        color: var(--color-ink-0, #f8fafc);
        border-color: var(--hl-border-active);
    }

    /* ═══════════════════════════════════════════════════════════════════
       3. RESPONSIVE BREAKPOINTS (Ergonomics, Touch Targets & Proximity)
       ═══════════════════════════════════════════════════════════════════ */
    @media (max-width: 991.98px) {
        .settings-workbench {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        .settings-rail {
            position: static;
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            gap: 10px !important;
            padding: 4px 4px 12px 4px !important;
            margin-bottom: 4px !important;
            scrollbar-width: none;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior-x: contain;
            scroll-snap-type: x proximity;
            width: 100%;
        }
        .settings-rail::-webkit-scrollbar {
            display: none;
        }
        .rail-group-card {
            display: contents !important;
        }
        .rail-group-header {
            display: none !important;
        }
        .rail-nav-list {
            display: contents !important;
        }
        .rail-link {
            flex: 0 0 auto !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 10px !important;
            padding: 8px 16px !important;
            white-space: nowrap !important;
            background: rgba(15, 23, 42, 0.75) !important;
            border: 1px solid var(--hl-border-subtle) !important;
            border-radius: 12px !important;
            min-height: 46px !important;
            box-sizing: border-box !important;
            text-decoration: none !important;
            scroll-snap-align: start;
            transition: all 0.18s ease !important;
        }
        .rail-link.active {
            background: rgba(99, 102, 241, 0.18) !important;
            border-color: var(--rail-accent, #6366f1) !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35) !important;
        }
        .rail-link .rail-icon-box {
            width: 28px !important;
            height: 28px !important;
            font-size: 0.8rem !important;
            border-radius: 8px !important;
            flex-shrink: 0 !important;
        }
        .rail-link.active .rail-icon-box {
            background: var(--rail-accent, #6366f1) !important;
            border-color: var(--rail-accent, #6366f1) !important;
            color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3) !important;
        }
        .rail-link .rail-title {
            font-size: 0.82rem !important;
            font-weight: 600 !important;
            color: var(--hl-text-muted) !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
        }
        .rail-link.active .rail-title {
            color: #ffffff !important;
        }
        .rail-link:hover {
            transform: none !important;
        }
        .rail-link.active::before {
            display: none !important;
        }
        .rail-desc {
            display: none !important;
        }
        .rail-status-dot {
            margin-left: 2px !important;
        }
        .specs-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 767.98px) {
        .settings-header-card {
            padding: 16px;
            border-radius: 14px;
        }
        .settings-header-wrapper {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }
        .badge-clearance-superadmin,
        .badge-clearance-standard {
            width: 100%;
            justify-content: center;
        }
        .canvas-tab-header {
            padding: 14px 16px;
            border-radius: 14px;
        }
        .config-field-row {
            grid-template-columns: 1fr;
            gap: 8px;
            align-items: flex-start;
        }
        .tactile-input,
        .vault-secret-input {
            font-size: 16px !important; /* Prevents iOS Safari auto-zoom */
            height: 44px;
        }
        .vault-btn-action {
            height: 44px;
            width: 44px;
        }
        .canvas-actions-bar {
            flex-direction: column-reverse;
            align-items: stretch;
            padding: 14px;
            gap: 10px;
        }
        .actions-bar-buttons {
            display: flex;
            flex-direction: column-reverse;
            gap: 8px;
            width: 100%;
        }
        .btn-tactile-save,
        .btn-tactile-reset {
            width: 100%;
            height: 46px;
        }
        .actions-bar-note {
            justify-content: center;
            text-align: center;
        }
        .live-test-panel {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }
        .btn-live-test {
            width: 100%;
            justify-content: center;
            height: 44px;
        }
        .specs-grid {
            grid-template-columns: 1fr;
        }
    }

    /* ═══════════════════════════════════════════════════════════════════
       4. LIGHT MODE PURITY (Clean Paper Architectural Theme)
       ═══════════════════════════════════════════════════════════════════ */
    body.light-mode .settings-header-card,
    body.light-mode .rail-group-card,
    body.light-mode .canvas-tab-header,
    body.light-mode .bento-config-card,
    body.light-mode .canvas-actions-bar {
        background: #ffffff !important;
        border-color: #e2e8f0 !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04) !important;
    }
    body.light-mode .settings-header-title,
    body.light-mode .canvas-tab-title,
    body.light-mode .bento-header-title,
    body.light-mode .config-label,
    body.light-mode .spec-value {
        color: #0f172a !important;
    }
    body.light-mode .settings-header-meta,
    body.light-mode .canvas-tab-subtitle,
    body.light-mode .config-hint,
    body.light-mode .actions-bar-note,
    body.light-mode .rail-desc {
        color: #64748b !important;
    }
    body.light-mode .rail-link {
        color: #475569 !important;
    }
    body.light-mode .rail-link:hover {
        background: #f1f5f9 !important;
        color: #0f172a !important;
    }
    body.light-mode .rail-link.active {
        background: #eef2ff !important;
        border-color: #c7d2fe !important;
        color: #4338ca !important;
    }
    body.light-mode .tactile-input,
    body.light-mode .vault-secret-group {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }
    body.light-mode .tactile-input:focus,
    body.light-mode .vault-secret-group:focus-within {
        background: #ffffff !important;
        border-color: #6366f1 !important;
    }
    body.light-mode .tactile-select option {
        background: #ffffff !important;
        color: #0f172a !important;
    }
    body.light-mode .bento-card-header {
        background: #f8fafc !important;
        border-color: #e2e8f0 !important;
    }
    body.light-mode .spec-item {
        background: #f8fafc !important;
        border-color: #e2e8f0 !important;
    }
    body.light-mode .live-test-panel {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
    }
</style>

@php
    $user = Auth::user();
    $isSuperAdmin = $user && ($user->hasRole('Super Admin') || $user->id === 1);

    // Group items mapped to key collections
    $groups = [
        'general' => $settings->get('general', collect()),
        'system' => $settings->get('system', collect()),
        'email' => $settings->get('email', collect()),
        'whatsapp' => $settings->get('whatsapp', collect()),
        'telegram' => $settings->get('telegram', collect()),
        'ai' => $settings->get('ai', collect()),
    ];

    // Helper to extract setting by key
    $getSetting = function($groupName, $key) use ($groups) {
        return $groups[$groupName]->firstWhere('key', $key);
    };

    $smtpPassSetting = $getSetting('email', 'smtp_password');
    $geminiKeySetting = $getSetting('ai', 'gemini_api_key');
    $openAiKeySetting = $getSetting('ai', 'openai_api_key');
    $tgTokenSetting = $getSetting('telegram', 'telegram_bot_token');
    $tgIpTokenSetting = $getSetting('telegram', 'telegram_ip_bot_token');
    $waTokenSetting = $getSetting('whatsapp', 'whatsapp_api_token');
@endphp

<!-- 1. Header Command Banner -->
<div class="settings-header-card">
    <div class="settings-header-wrapper">
        <div class="settings-header-left">
            <div class="settings-header-icon">
                <i class="fas fa-sliders-h"></i>
            </div>
            <div>
                <h1 class="settings-header-title">{{ __('messages.system_settings') }}</h1>
                <div class="settings-header-meta">
                    <span class="settings-badge-live">
                        <span class="settings-badge-pulse"></span>
                        CORE PARAMETERS // GATEWAY DISPATCH
                    </span>
                    <span>&bull;</span>
                    <span>{{ __('messages.system_settings_desc') }}</span>
                </div>
            </div>
        </div>

        <div>
            @if($isSuperAdmin)
                <div class="badge-clearance-superadmin" title="Full configuration clearance active">
                    <i class="fas fa-shield-alt"></i>
                    <span>{{ __('messages.clearance_superadmin') }}</span>
                </div>
            @else
                <div class="badge-clearance-standard" title="Restricted settings protection active">
                    <i class="fas fa-user-shield"></i>
                    <span>{{ __('messages.access_standard_admin') }}</span>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Alert Feedback -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; border-radius: 12px; margin-bottom: 20px;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; border-radius: 12px; margin-bottom: 20px;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
    </div>
@endif

<!-- 2. The Workbench Console Layout -->
<form action="{{ route('settings.updateAll') }}" method="POST" id="settingsForm">
    @csrf
    <input type="hidden" name="active_tab" id="active_tab_input" value="{{ request('active_tab', '#pill-general') }}">

    <div class="settings-workbench">
        <!-- Left Command Rail -->
        <div class="settings-rail">
            <!-- Group 1: Platform Core -->
            <div class="rail-group-card">
                <div class="rail-group-header">
                    <span>{{ __('messages.platform_core') }}</span>
                    <i class="fas fa-microchip"></i>
                </div>
                <div class="rail-nav-list" role="tablist">
                    <!-- General -->
                    <a class="rail-link active" id="tab-general" data-toggle="pill" href="#pill-general" role="tab" style="--rail-accent: #6366f1;">
                        <div class="rail-icon-box">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <div class="rail-text-box">
                            <div class="rail-title">
                                <span>{{ __('messages.general_configuration') }}</span>
                            </div>
                            <div class="rail-desc">{{ __('messages.subtitle_general') }}</div>
                        </div>
                        <span class="rail-status-dot"></span>
                    </a>

                    <!-- System Core -->
                    <a class="rail-link" id="tab-system" data-toggle="pill" href="#pill-system" role="tab" style="--rail-accent: #f59e0b;">
                        <div class="rail-icon-box">
                            <i class="fas fa-server"></i>
                        </div>
                        <div class="rail-text-box">
                            <div class="rail-title">
                                <span>{{ __('messages.system_configuration') }}</span>
                            </div>
                            <div class="rail-desc">{{ __('messages.subtitle_system') }}</div>
                        </div>
                        <span class="rail-status-dot"></span>
                    </a>
                </div>
            </div>

            <!-- Group 2: Dispatch Relays -->
            <div class="rail-group-card">
                <div class="rail-group-header">
                    <span>{{ __('messages.dispatch_relays') }}</span>
                    <i class="fas fa-satellite-dish"></i>
                </div>
                <div class="rail-nav-list" role="tablist">
                    <!-- Mail Relay (SMTP) -->
                    <a class="rail-link" id="tab-email" data-toggle="pill" href="#pill-email" role="tab" style="--rail-accent: #06b6d4;">
                        <div class="rail-icon-box">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <div class="rail-text-box">
                            <div class="rail-title">
                                <span>{{ __('messages.email_configuration') }}</span>
                            </div>
                            <div class="rail-desc">{{ __('messages.subtitle_email') }}</div>
                        </div>
                        <span class="rail-status-dot {{ optional($smtpPassSetting)->value ? '' : 'dot-amber' }}"></span>
                    </a>

                    <!-- WhatsApp Gateway -->
                    <a class="rail-link" id="tab-whatsapp" data-toggle="pill" href="#pill-whatsapp" role="tab" style="--rail-accent: #10b981;">
                        <div class="rail-icon-box">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div class="rail-text-box">
                            <div class="rail-title">
                                <span>{{ __('messages.whatsapp_gateway') }}</span>
                            </div>
                            <div class="rail-desc">{{ __('messages.subtitle_whatsapp') }}</div>
                        </div>
                        @php $waEnabled = optional($getSetting('whatsapp', 'whatsapp_enabled'))->value == '1'; @endphp
                        <span class="rail-status-dot {{ $waEnabled ? '' : 'dot-dim' }}"></span>
                    </a>

                    <!-- Telegram Bot -->
                    <a class="rail-link" id="tab-telegram" data-toggle="pill" href="#pill-telegram" role="tab" style="--rail-accent: #38bdf8;">
                        <div class="rail-icon-box">
                            <i class="fab fa-telegram-plane"></i>
                        </div>
                        <div class="rail-text-box">
                            <div class="rail-title">
                                <span>{{ __('messages.telegram_dispatch') }}</span>
                            </div>
                            <div class="rail-desc">{{ __('messages.subtitle_telegram') }}</div>
                        </div>
                        @php $tgEnabled = optional($getSetting('telegram', 'telegram_enabled'))->value == '1'; @endphp
                        <span class="rail-status-dot {{ $tgEnabled ? '' : 'dot-dim' }}"></span>
                    </a>
                </div>
            </div>

            <!-- Group 3: Intelligence & Diagnostics -->
            <div class="rail-group-card">
                <div class="rail-group-header">
                    <span>{{ __('messages.neural_gateway') }}</span>
                    <i class="fas fa-brain"></i>
                </div>
                <div class="rail-nav-list" role="tablist">
                    <!-- AI Engine -->
                    <a class="rail-link" id="tab-ai" data-toggle="pill" href="#pill-ai" role="tab" style="--rail-accent: #a855f7;">
                        <div class="rail-icon-box">
                            <i class="fas fa-brain"></i>
                        </div>
                        <div class="rail-text-box">
                            <div class="rail-title">
                                <span>{{ __('messages.ai_configuration') }}</span>
                                @if(!$isSuperAdmin)
                                    <i class="fas fa-lock text-warning" style="font-size: 0.72rem;"></i>
                                @endif
                            </div>
                            <div class="rail-desc">{{ __('messages.subtitle_ai') }}</div>
                        </div>
                        @php $aiProvider = optional($getSetting('ai', 'ai_provider'))->value; @endphp
                        <span class="rail-status-dot {{ $aiProvider && $aiProvider !== 'off' ? '' : 'dot-dim' }}"></span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Configuration Canvas -->
        <div class="settings-canvas tab-content">

            <!-- ══════════════════════════════════════════════════════════
                 TAB 1: GENERAL SETTINGS
                 ══════════════════════════════════════════════════════════ -->
            <div class="tab-pane fade show active" id="pill-general" role="tabpanel" aria-labelledby="tab-general">
                <div class="canvas-tab-header">
                    <div class="canvas-tab-left">
                        <div class="canvas-tab-icon" style="background: rgba(99, 102, 241, 0.12); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3);">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <div>
                            <h2 class="canvas-tab-title">{{ __('messages.general_configuration') }}</h2>
                            <div class="canvas-tab-subtitle">{{ __('messages.subtitle_general') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Bento 1: Identity & Branding -->
                <div class="bento-config-card">
                    <div class="bento-card-header">
                        <h3 class="bento-header-title">
                            <i class="fas fa-cube text-indigo" style="color: #818cf8;"></i>
                            <span>{{ __('messages.app_name') }} &amp; {{ __('messages.company_name') }}</span>
                        </h3>
                        <span class="bento-header-badge">{{ __('messages.brand_identity') }}</span>
                    </div>
                    <div class="bento-card-body">
                        <!-- App Name -->
                        @php $appName = $getSetting('general', 'app_name'); @endphp
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label" for="setting-app_name">{{ __('messages.app_name') }}</label>
                                <p class="config-hint">{{ __('messages.app_name_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <input type="text" id="setting-app_name" name="app_name" class="tactile-input" value="{{ optional($appName)->value ?? 'ITAM Enterprise' }}" required>
                            </div>
                        </div>

                        <!-- Company Name -->
                        @php $companyName = $getSetting('general', 'company_name'); @endphp
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label" for="setting-company_name">{{ __('messages.company_name') }}</label>
                                <p class="config-hint">{{ __('messages.company_name_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <input type="text" id="setting-company_name" name="company_name" class="tactile-input" value="{{ optional($companyName)->value ?? 'Team IT Pabrik' }}" required>
                            </div>
                        </div>

                        <!-- Company Email -->
                        @php $companyEmail = $getSetting('general', 'company_email'); @endphp
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label" for="setting-company_email">{{ __('messages.company_email') }}</label>
                                <p class="config-hint">{{ __('messages.company_email_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <input type="email" id="setting-company_email" name="company_email" class="tactile-input" value="{{ optional($companyEmail)->value }}" placeholder="it@perusahaan.com">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bento 2: Honest System Telemetry -->
                <div class="bento-config-card">
                    <div class="bento-card-header">
                        <h3 class="bento-header-title">
                            <i class="fas fa-microchip text-cyan" style="color: #06b6d4;"></i>
                            <span>{{ __('messages.telemetry_environment') }}</span>
                        </h3>
                        <span class="bento-header-badge">{{ __('messages.live_specs') }}</span>
                    </div>
                    <div class="bento-card-body">
                        <div class="specs-grid">
                            <div class="spec-item">
                                <div class="spec-label">Environment</div>
                                <div class="spec-value">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981;"></span>
                                    <span>{{ app()->environment() }}</span>
                                </div>
                            </div>
                            <div class="spec-item">
                                <div class="spec-label">PHP Runtime</div>
                                <div class="spec-value">PHP {{ PHP_VERSION }}</div>
                            </div>
                            <div class="spec-item">
                                <div class="spec-label">Framework</div>
                                <div class="spec-value">Laravel {{ app()->version() }}</div>
                            </div>
                            <div class="spec-item">
                                <div class="spec-label">Timezone</div>
                                <div class="spec-value">{{ config('app.timezone') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════
                 TAB 2: SYSTEM CORE SETTINGS
                 ══════════════════════════════════════════════════════════ -->
            <div class="tab-pane fade" id="pill-system" role="tabpanel" aria-labelledby="tab-system">
                <div class="canvas-tab-header">
                    <div class="canvas-tab-left">
                        <div class="canvas-tab-icon" style="background: rgba(245, 158, 11, 0.12); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3);">
                            <i class="fas fa-server"></i>
                        </div>
                        <div>
                            <h2 class="canvas-tab-title">{{ __('messages.system_configuration') }}</h2>
                            <div class="canvas-tab-subtitle">{{ __('messages.subtitle_system') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Bento 1: Safety & Operational Flags -->
                <div class="bento-config-card">
                    <div class="bento-card-header">
                        <h3 class="bento-header-title">
                            <i class="fas fa-shield-alt text-amber" style="color: #f59e0b;"></i>
                            <span>{{ __('messages.operational_availability') }}</span>
                        </h3>
                        <span class="bento-header-badge">{{ __('messages.state_flags') }}</span>
                    </div>
                    <div class="bento-card-body">
                        <!-- Maintenance Mode -->
                        @php $maintSetting = $getSetting('system', 'maintenance_mode'); $isMaint = optional($maintSetting)->value == '1'; @endphp
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label">{{ __('messages.maintenance_mode') }}</label>
                                <p class="config-hint">{{ __('messages.maintenance_mode_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <input type="hidden" name="maintenance_mode" id="val-maintenance_mode" value="{{ $isMaint ? '1' : '0' }}">
                                <div class="tactile-switch-wrapper">
                                    <button type="button" class="tactile-switch-btn {{ $isMaint ? 'is-active' : '' }}" onclick="toggleSwitch('maintenance_mode')">
                                        <span class="tactile-switch-knob"></span>
                                    </button>
                                    <span class="tactile-switch-label" id="lbl-maintenance_mode">{{ $isMaint ? 'ON' : 'OFF' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Global Email Notifications -->
                        @php $emailNotif = $getSetting('system', 'email_notification'); $isEmailNotif = optional($emailNotif)->value == '1'; @endphp
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label">{{ __('messages.email_notification') }}</label>
                                <p class="config-hint">{{ __('messages.email_notification_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <input type="hidden" name="email_notification" id="val-email_notification" value="{{ $isEmailNotif ? '1' : '0' }}">
                                <div class="tactile-switch-wrapper">
                                    <button type="button" class="tactile-switch-btn {{ $isEmailNotif ? 'is-active' : '' }}" onclick="toggleSwitch('email_notification')">
                                        <span class="tactile-switch-knob"></span>
                                    </button>
                                    <span class="tactile-switch-label" id="lbl-email_notification">{{ $isEmailNotif ? 'ON' : 'OFF' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bento 2: Automated Disaster Recovery -->
                <div class="bento-config-card">
                    <div class="bento-card-header">
                        <h3 class="bento-header-title">
                            <i class="fas fa-database text-emerald" style="color: #10b981;"></i>
                            <span>{{ __('messages.auto_backup') }}</span>
                        </h3>
                        <span class="bento-header-badge">{{ __('messages.disaster_recovery') }}</span>
                    </div>
                    <div class="bento-card-body">
                        @php $autoBackup = $getSetting('system', 'auto_backup'); $isBackup = optional($autoBackup)->value == '1'; @endphp
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label">{{ __('messages.auto_backup') }}</label>
                                <p class="config-hint">{{ __('messages.auto_backup_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <input type="hidden" name="auto_backup" id="val-auto_backup" value="{{ $isBackup ? '1' : '0' }}">
                                <div class="tactile-switch-wrapper">
                                    <button type="button" class="tactile-switch-btn {{ $isBackup ? 'is-active' : '' }}" onclick="toggleSwitch('auto_backup')">
                                        <span class="tactile-switch-knob"></span>
                                    </button>
                                    <span class="tactile-switch-label" id="lbl-auto_backup">{{ $isBackup ? 'ON' : 'OFF' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════
                 TAB 3: EMAIL (SMTP RELAY) SETTINGS
                 ══════════════════════════════════════════════════════════ -->
            <div class="tab-pane fade" id="pill-email" role="tabpanel" aria-labelledby="tab-email">
                <div class="canvas-tab-header">
                    <div class="canvas-tab-left">
                        <div class="canvas-tab-icon" style="background: rgba(6, 182, 212, 0.12); color: #06b6d4; border: 1px solid rgba(6, 182, 212, 0.3);">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <div>
                            <h2 class="canvas-tab-title">{{ __('messages.email_configuration') }}</h2>
                            <div class="canvas-tab-subtitle">{{ __('messages.subtitle_email') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Bento 1: SMTP Relay Parameters -->
                <div class="bento-config-card">
                    <div class="bento-card-header">
                        <h3 class="bento-header-title">
                            <i class="fas fa-server text-cyan" style="color: #06b6d4;"></i>
                            <span>{{ __('messages.smtp_transport_credentials') }}</span>
                        </h3>
                        <span class="bento-header-badge">{{ __('messages.mail_relay') }}</span>
                    </div>
                    <div class="bento-card-body">
                        <!-- SMTP Host -->
                        @php $smtpHost = $getSetting('email', 'smtp_host'); @endphp
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label" for="setting-smtp_host">{{ __('messages.smtp_host') }}</label>
                                <p class="config-hint">{{ __('messages.smtp_host_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <input type="text" id="setting-smtp_host" name="smtp_host" class="tactile-input" value="{{ optional($smtpHost)->value }}" placeholder="smtp.gmail.com">
                            </div>
                        </div>

                        <!-- SMTP Port -->
                        @php $smtpPort = $getSetting('email', 'smtp_port'); @endphp
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label" for="setting-smtp_port">{{ __('messages.smtp_port') }}</label>
                                <p class="config-hint">{{ __('messages.smtp_port_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <input type="text" id="setting-smtp_port" name="smtp_port" class="tactile-input" value="{{ optional($smtpPort)->value ?? '587' }}" placeholder="587" style="max-width: 140px;">
                            </div>
                        </div>

                        <!-- SMTP Username -->
                        @php $smtpUser = $getSetting('email', 'smtp_username'); @endphp
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label" for="setting-smtp_username">{{ __('messages.smtp_username') }}</label>
                                <p class="config-hint">{{ __('messages.smtp_username_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <input type="text" id="setting-smtp_username" name="smtp_username" class="tactile-input" value="{{ optional($smtpUser)->value }}" placeholder="admin@perusahaan.com">
                            </div>
                        </div>

                        <!-- SMTP Password (Vault Input) -->
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label" for="setting-smtp_password">{{ __('messages.smtp_password') }}</label>
                                <p class="config-hint">{{ __('messages.smtp_password_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <div class="vault-secret-group">
                                    <input type="password" id="setting-smtp_password" name="smtp_password" class="vault-secret-input" value="{{ optional($smtpPassSetting)->value }}" placeholder="••••••••••••" autocomplete="new-password">
                                    <button type="button" class="vault-btn-action" onclick="toggleVaultVisibility('setting-smtp_password', this)" title="{{ __('messages.vault_view_tip') }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="vault-btn-action" onclick="copyFieldValue('setting-smtp_password', this)" title="{{ __('messages.vault_copy_tip') }}">
                                        <i class="far fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bento 2: Network IP Offline Email Dispatch -->
                <div class="bento-config-card">
                    <div class="bento-card-header">
                        <h3 class="bento-header-title">
                            <i class="fas fa-network-wired text-cyan" style="color: #06b6d4;"></i>
                            <span>{{ __('messages.scheduled_ip_offline_alerts') }}</span>
                        </h3>
                        <span class="bento-header-badge">{{ __('messages.scheduled_dispatch') }}</span>
                    </div>
                    <div class="bento-card-body">
                        <!-- Toggle IP Offline Alert -->
                        @php $ipEmailNotif = $getSetting('email', 'ip_offline_email_notification'); $isIpEmail = optional($ipEmailNotif)->value == '1'; @endphp
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label">{{ __('messages.ip_offline_email_notification') }}</label>
                                <p class="config-hint">{{ __('messages.ip_offline_email_notification_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <input type="hidden" name="ip_offline_email_notification" id="val-ip_offline_email_notification" value="{{ $isIpEmail ? '1' : '0' }}">
                                <div class="tactile-switch-wrapper">
                                    <button type="button" class="tactile-switch-btn {{ $isIpEmail ? 'is-active' : '' }}" onclick="toggleSwitch('ip_offline_email_notification')">
                                        <span class="tactile-switch-knob"></span>
                                    </button>
                                    <span class="tactile-switch-label" id="lbl-ip_offline_email_notification">{{ $isIpEmail ? 'ON' : 'OFF' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule Time -->
                        @php $ipEmailTime = $getSetting('email', 'ip_offline_email_time'); @endphp
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label" for="setting-ip_offline_email_time">{{ __('messages.ip_offline_email_time') }}</label>
                                <p class="config-hint">{{ __('messages.ip_offline_email_time_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <input type="time" id="setting-ip_offline_email_time" name="ip_offline_email_time" class="tactile-input" value="{{ optional($ipEmailTime)->value ?: '11:00' }}" style="max-width: 140px;">
                            </div>
                        </div>

                        <!-- Recipients -->
                        @php $ipEmailRecipients = $getSetting('email', 'ip_offline_email_recipients'); @endphp
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label" for="setting-ip_offline_email_recipients">{{ __('messages.ip_offline_email_recipients') }}</label>
                                <p class="config-hint">{{ __('messages.ip_offline_email_recipients_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <input type="text" id="setting-ip_offline_email_recipients" name="ip_offline_email_recipients" class="tactile-input" value="{{ optional($ipEmailRecipients)->value }}" placeholder="it-team@perusahaan.com, lead-infra@perusahaan.com">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════
                 TAB 4: WHATSAPP GATEWAY
                 ══════════════════════════════════════════════════════════ -->
            <div class="tab-pane fade" id="pill-whatsapp" role="tabpanel" aria-labelledby="tab-whatsapp">
                <div class="canvas-tab-header">
                    <div class="canvas-tab-left">
                        <div class="canvas-tab-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div>
                            <h2 class="canvas-tab-title">{{ __('messages.whatsapp_gateway') }}</h2>
                            <div class="canvas-tab-subtitle">{{ __('messages.subtitle_whatsapp') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Bento 1: Provider & Endpoint -->
                <div class="bento-config-card">
                    <div class="bento-card-header">
                        <h3 class="bento-header-title">
                            <i class="fab fa-whatsapp text-emerald" style="color: #10b981;"></i>
                            <span>{{ __('messages.gateway_provider_api') }}</span>
                        </h3>
                        <span class="bento-header-badge">{{ __('messages.gateway_protocol') }}</span>
                    </div>
                    <div class="bento-card-body">
                        <!-- Toggle WhatsApp -->
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label">{{ __('messages.whatsapp_enabled') }}</label>
                                <p class="config-hint">{{ __('messages.whatsapp_enabled_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <input type="hidden" name="whatsapp_enabled" id="val-whatsapp_enabled" value="{{ $waEnabled ? '1' : '0' }}">
                                <div class="tactile-switch-wrapper">
                                    <button type="button" class="tactile-switch-btn {{ $waEnabled ? 'is-active' : '' }}" onclick="toggleSwitch('whatsapp_enabled')">
                                        <span class="tactile-switch-knob"></span>
                                    </button>
                                    <span class="tactile-switch-label" id="lbl-whatsapp_enabled">{{ $waEnabled ? 'ON' : 'OFF' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Provider Selection -->
                        @php $waProvider = $getSetting('whatsapp', 'whatsapp_provider'); @endphp
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label" for="setting-whatsapp_provider">{{ __('messages.whatsapp_provider') }}</label>
                                <p class="config-hint">{{ __('messages.whatsapp_provider_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <select id="setting-whatsapp_provider" name="whatsapp_provider" class="tactile-input tactile-select">
                                    <option value="fonnte" {{ optional($waProvider)->value == 'fonnte' ? 'selected' : '' }}>{{ __('messages.opt_fonnte_desc') }}</option>
                                    <option value="wablas" {{ optional($waProvider)->value == 'wablas' ? 'selected' : '' }}>{{ __('messages.opt_wablas_desc') }}</option>
                                    <option value="generic" {{ optional($waProvider)->value == 'generic' ? 'selected' : '' }}>{{ __('messages.opt_generic_wa_desc') }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- API URL -->
                        @php $waUrl = $getSetting('whatsapp', 'whatsapp_api_url'); @endphp
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label" for="setting-whatsapp_api_url">{{ __('messages.whatsapp_api_url') }}</label>
                                <p class="config-hint">{{ __('messages.whatsapp_api_url_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <input type="text" id="setting-whatsapp_api_url" name="whatsapp_api_url" class="tactile-input" value="{{ optional($waUrl)->value ?? 'https://api.fonnte.com/send' }}" placeholder="https://api.fonnte.com/send">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bento 2: Token & Admin Recipient -->
                <div class="bento-config-card">
                    <div class="bento-card-header">
                        <h3 class="bento-header-title">
                            <i class="fas fa-key text-emerald" style="color: #10b981;"></i>
                            <span>{{ __('messages.auth_token_recipient') }}</span>
                        </h3>
                        <span class="bento-header-badge">{{ __('messages.credentials_label') }}</span>
                    </div>
                    <div class="bento-card-body">
                        <!-- Token Vault -->
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label" for="setting-whatsapp_api_token">{{ __('messages.whatsapp_api_token') }}</label>
                                <p class="config-hint">{{ __('messages.whatsapp_api_token_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <div class="vault-secret-group">
                                    <input type="password" id="setting-whatsapp_api_token" name="whatsapp_api_token" class="vault-secret-input" value="{{ optional($waTokenSetting)->value }}" placeholder="{{ __('messages.placeholder_wa_token') }}" autocomplete="new-password">
                                    <button type="button" class="vault-btn-action" onclick="toggleVaultVisibility('setting-whatsapp_api_token', this)" title="{{ __('messages.vault_view_tip') }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="vault-btn-action" onclick="copyFieldValue('setting-whatsapp_api_token', this)" title="{{ __('messages.vault_copy_tip') }}">
                                        <i class="far fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Admin Phone -->
                        @php $waPhone = $getSetting('whatsapp', 'whatsapp_admin_phone'); @endphp
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label" for="setting-whatsapp_admin_phone">{{ __('messages.whatsapp_admin_phone') }}</label>
                                <p class="config-hint">{{ __('messages.whatsapp_admin_phone_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <input type="text" id="setting-whatsapp_admin_phone" name="whatsapp_admin_phone" class="tactile-input" value="{{ optional($waPhone)->value }}" placeholder="081234567890">
                            </div>
                        </div>

                        <!-- Live Action: Test WhatsApp Dispatch -->
                        <div class="live-test-panel">
                            <div class="live-test-info">
                                <i class="fab fa-whatsapp live-test-icon text-emerald" style="color: #10b981;"></i>
                                <span class="live-test-text">{{ __('messages.test_whatsapp_hint') }}</span>
                            </div>
                            <button type="button" id="btn-test-whatsapp" class="btn-live-test btn-live-test-emerald">
                                <i class="fas fa-paper-plane mr-1"></i>
                                <span>{{ __('messages.send_test_whatsapp') }}</span>
                                <span id="test-wa-spinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                        <div id="test-wa-result"></div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════
                 TAB 5: TELEGRAM DISPATCH
                 ══════════════════════════════════════════════════════════ -->
            <div class="tab-pane fade" id="pill-telegram" role="tabpanel" aria-labelledby="tab-telegram">
                <div class="canvas-tab-header">
                    <div class="canvas-tab-left">
                        <div class="canvas-tab-icon" style="background: rgba(56, 189, 248, 0.12); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3);">
                            <i class="fab fa-telegram-plane"></i>
                        </div>
                        <div>
                            <h2 class="canvas-tab-title">{{ __('messages.telegram_bot_title') }}</h2>
                            <div class="canvas-tab-subtitle">{{ __('messages.subtitle_telegram') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Bento 1: Primary Bot (Activity Log & Tickets) -->
                <div class="bento-config-card">
                    <div class="bento-card-header">
                        <h3 class="bento-header-title">
                            <i class="fas fa-robot text-sky" style="color: #38bdf8;"></i>
                            <span>{{ __('messages.telegram_primary_bot_title') }}</span>
                        </h3>
                        <span class="bento-header-badge">{{ __('messages.primary_bot') }}</span>
                    </div>
                    <div class="bento-card-body">
                        <!-- Toggle Telegram -->
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label">{{ __('messages.telegram_enabled') }}</label>
                                <p class="config-hint">{{ __('messages.telegram_enabled_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <input type="hidden" name="telegram_enabled" id="val-telegram_enabled" value="{{ $tgEnabled ? '1' : '0' }}">
                                <div class="tactile-switch-wrapper">
                                    <button type="button" class="tactile-switch-btn {{ $tgEnabled ? 'is-active' : '' }}" onclick="toggleSwitch('telegram_enabled')">
                                        <span class="tactile-switch-knob"></span>
                                    </button>
                                    <span class="tactile-switch-label" id="lbl-telegram_enabled">{{ $tgEnabled ? 'ON' : 'OFF' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Primary Bot Token -->
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label" for="setting-telegram_bot_token">{{ __('messages.telegram_bot_token') }}</label>
                                <p class="config-hint">{{ __('messages.telegram_bot_token_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <div class="vault-secret-group">
                                    <input type="password" id="setting-telegram_bot_token" name="telegram_bot_token" class="vault-secret-input" value="{{ optional($tgTokenSetting)->value }}" placeholder="{{ __('messages.placeholder_tg_bot_token') }}" autocomplete="new-password">
                                    <button type="button" class="vault-btn-action" onclick="toggleVaultVisibility('setting-telegram_bot_token', this)" title="{{ __('messages.vault_view_tip') }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="vault-btn-action" onclick="copyFieldValue('setting-telegram_bot_token', this)" title="{{ __('messages.vault_copy_tip') }}">
                                        <i class="far fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Primary Chat ID -->
                        @php $tgChatId = $getSetting('telegram', 'telegram_chat_id'); @endphp
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label" for="setting-telegram_chat_id">{{ __('messages.telegram_chat_id') }}</label>
                                <p class="config-hint">{{ __('messages.telegram_chat_id_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <input type="text" id="setting-telegram_chat_id" name="telegram_chat_id" class="tactile-input" value="{{ optional($tgChatId)->value }}" placeholder="{{ __('messages.placeholder_tg_chat_id') }}">
                            </div>
                        </div>

                        <!-- Live Action: Test Telegram Bot -->
                        <div class="live-test-panel">
                            <div class="live-test-info">
                                <i class="fab fa-telegram-plane live-test-icon text-sky" style="color: #38bdf8;"></i>
                                <span class="live-test-text">{{ __('messages.test_telegram_hint') }}</span>
                            </div>
                            <button type="button" id="btn-test-telegram" class="btn-live-test btn-live-test-sky">
                                <i class="fas fa-paper-plane mr-1"></i>
                                <span>{{ __('messages.send_test_telegram') }}</span>
                                <span id="test-tg-spinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                        <div id="test-tg-result"></div>
                    </div>
                </div>

                <!-- Bento 2: Dedicated Network IP Alert Bot -->
                <div class="bento-config-card">
                    <div class="bento-card-header">
                        <h3 class="bento-header-title">
                            <i class="fas fa-network-wired text-sky" style="color: #38bdf8;"></i>
                            <span>{{ __('messages.telegram_ip_bot_title') }}</span>
                        </h3>
                        <span class="bento-header-badge">{{ __('messages.isolated_alert_channel') }}</span>
                    </div>
                    <div class="bento-card-body">
                        <!-- Dedicated IP Token -->
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label" for="setting-telegram_ip_bot_token">{{ __('messages.telegram_ip_bot_token') }}</label>
                                <p class="config-hint">{{ __('messages.telegram_ip_bot_token_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <div class="vault-secret-group">
                                    <input type="password" id="setting-telegram_ip_bot_token" name="telegram_ip_bot_token" class="vault-secret-input" value="{{ optional($tgIpTokenSetting)->value }}" placeholder="{{ __('messages.placeholder_tg_ip_bot_token') }}" autocomplete="new-password">
                                    <button type="button" class="vault-btn-action" onclick="toggleVaultVisibility('setting-telegram_ip_bot_token', this)" title="{{ __('messages.vault_view_tip') }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="vault-btn-action" onclick="copyFieldValue('setting-telegram_ip_bot_token', this)" title="{{ __('messages.vault_copy_tip') }}">
                                        <i class="far fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Dedicated IP Chat ID -->
                        @php $tgIpChatId = $getSetting('telegram', 'telegram_ip_chat_id'); @endphp
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label" for="setting-telegram_ip_chat_id">{{ __('messages.telegram_ip_chat_id') }}</label>
                                <p class="config-hint">{{ __('messages.telegram_ip_chat_id_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <input type="text" id="setting-telegram_ip_chat_id" name="telegram_ip_chat_id" class="tactile-input" value="{{ optional($tgIpChatId)->value }}" placeholder="{{ __('messages.placeholder_tg_ip_chat_id') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════
                 TAB 6: AI ENGINE (NEURAL GATEWAY)
                 ══════════════════════════════════════════════════════════ -->
            <div class="tab-pane fade" id="pill-ai" role="tabpanel" aria-labelledby="tab-ai">
                <div class="canvas-tab-header">
                    <div class="canvas-tab-left">
                        <div class="canvas-tab-icon" style="background: rgba(168, 85, 247, 0.12); color: #a855f7; border: 1px solid rgba(168, 85, 247, 0.3);">
                            <i class="fas fa-brain"></i>
                        </div>
                        <div>
                            <h2 class="canvas-tab-title">{{ __('messages.ai_configuration') }}</h2>
                            <div class="canvas-tab-subtitle">{{ __('messages.subtitle_ai') }}</div>
                        </div>
                    </div>
                    @if(!$isSuperAdmin)
                        <div class="badge-clearance-superadmin" style="background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.35); color: #f87171;">
                            <i class="fas fa-lock"></i>
                            <span>{{ __('messages.locked_superadmin_only') }}</span>
                        </div>
                    @endif
                </div>

                @if(!$isSuperAdmin)
                    <div class="alert alert-warning border-0" style="background: rgba(245, 158, 11, 0.12); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.28); border-radius: 12px; margin-bottom: 20px;">
                        <i class="fas fa-lock mr-2"></i> {{ __('messages.superadmin_only_ai') }}
                    </div>
                @endif

                <!-- Bento 1: AI Provider Selection -->
                <div class="bento-config-card">
                    <div class="bento-card-header">
                        <h3 class="bento-header-title">
                            <i class="fas fa-microchip text-purple" style="color: #a855f7;"></i>
                            <span>{{ __('messages.llm_engine_title') }}</span>
                        </h3>
                        <span class="bento-header-badge">{{ __('messages.ai_router') }}</span>
                    </div>
                    <div class="bento-card-body">
                        @php $aiProvider = $getSetting('ai', 'ai_provider'); @endphp
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label" for="setting-ai_provider">{{ __('messages.ai_provider') }}</label>
                                <p class="config-hint">{{ __('messages.ai_provider_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <select id="setting-ai_provider" name="ai_provider" class="tactile-input tactile-select" {{ !$isSuperAdmin ? 'disabled' : '' }}>
                                    <option value="gemini" {{ optional($aiProvider)->value == 'gemini' ? 'selected' : '' }}>{{ __('messages.opt_gemini_desc') }}</option>
                                    <option value="openai" {{ optional($aiProvider)->value == 'openai' ? 'selected' : '' }}>{{ __('messages.opt_openai_desc') }}</option>
                                    <option value="off" {{ optional($aiProvider)->value == 'off' ? 'selected' : '' }}>{{ __('messages.opt_offline_ai_desc') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bento 2: API Keys Vault -->
                <div class="bento-config-card">
                    <div class="bento-card-header">
                        <h3 class="bento-header-title">
                            <i class="fas fa-key text-purple" style="color: #a855f7;"></i>
                            <span>{{ __('messages.api_secret_vault_title') }}</span>
                        </h3>
                        <span class="bento-header-badge">{{ __('messages.vault_enclave') }}</span>
                    </div>
                    <div class="bento-card-body">
                        <!-- Google Gemini API Key -->
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label" for="setting-gemini_api_key">{{ __('messages.gemini_api_key') }}</label>
                                <p class="config-hint">{{ __('messages.gemini_api_key_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <div class="vault-secret-group">
                                    <input type="password" id="setting-gemini_api_key" name="gemini_api_key" class="vault-secret-input" value="{{ optional($geminiKeySetting)->value }}" placeholder="AIzaSy..." autocomplete="new-password" {{ !$isSuperAdmin ? 'disabled' : '' }}>
                                    <button type="button" class="vault-btn-action" onclick="toggleVaultVisibility('setting-gemini_api_key', this)" title="{{ __('messages.vault_view_tip') }}" {{ !$isSuperAdmin ? 'disabled' : '' }}>
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="vault-btn-action" onclick="copyFieldValue('setting-gemini_api_key', this)" title="{{ __('messages.vault_copy_tip') }}" {{ !$isSuperAdmin ? 'disabled' : '' }}>
                                        <i class="far fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- OpenAI API Key -->
                        <div class="config-field-row">
                            <div class="config-label-box">
                                <label class="config-label" for="setting-openai_api_key">{{ __('messages.openai_api_key') }}</label>
                                <p class="config-hint">{{ __('messages.openai_api_key_hint') }}</p>
                            </div>
                            <div class="config-control-box">
                                <div class="vault-secret-group">
                                    <input type="password" id="setting-openai_api_key" name="openai_api_key" class="vault-secret-input" value="{{ optional($openAiKeySetting)->value }}" placeholder="sk-proj-..." autocomplete="new-password" {{ !$isSuperAdmin ? 'disabled' : '' }}>
                                    <button type="button" class="vault-btn-action" onclick="toggleVaultVisibility('setting-openai_api_key', this)" title="{{ __('messages.vault_view_tip') }}" {{ !$isSuperAdmin ? 'disabled' : '' }}>
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="vault-btn-action" onclick="copyFieldValue('setting-openai_api_key', this)" title="{{ __('messages.vault_copy_tip') }}" {{ !$isSuperAdmin ? 'disabled' : '' }}>
                                        <i class="far fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Sticky Action Bar -->
            <div class="canvas-actions-bar">
                <div class="actions-bar-note">
                    <i class="fas fa-shield-alt text-indigo" style="color: #818cf8;"></i>
                    <span>{{ __('messages.settings_save_note') }}</span>
                </div>
                <div class="actions-bar-buttons d-flex align-items-center" style="gap: 10px;">
                    <a href="{{ route('settings.index') }}" class="btn-tactile-reset">
                        {{ __('messages.cancel') }}
                    </a>
                    <button type="submit" class="btn-tactile-save">
                        <i class="fas fa-save"></i>
                        <span>{{ __('messages.save') }}</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
// 1. Hallmark Robust Tab Switching & Persistence Engine
function switchSettingsTab(targetId, clickedEl) {
    if (!targetId) return;
    if (!targetId.startsWith('#')) targetId = '#' + targetId;

    const targetPane = $(targetId);
    if (!targetPane.length) return;

    // 1. Update all tab links
    $('a[data-toggle="pill"]').removeClass('active');
    const activeLink = clickedEl ? $(clickedEl) : $('a[data-toggle="pill"][href="' + targetId + '"]');
    activeLink.addClass('active');

    // 2. Switch tab panes cleanly
    $('.settings-canvas .tab-pane').removeClass('show active');
    targetPane.addClass('show active');

    // 3. Ribbon auto-scroll: smooth scroll active link to center
    if (activeLink.length && activeLink[0].scrollIntoView) {
        try {
            activeLink[0].scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        } catch(e){}
    }

    // 4. Update hidden input for form submission
    $('#active_tab_input').val(targetId);

    // 5. Persist to localStorage and browser URL hash
    try {
        localStorage.setItem('active_settings_tab', targetId);
        if (history.replaceState) {
            history.replaceState(null, null, targetId);
        }
    } catch(e){}

    // 6. Trigger event for compatibility
    $(activeLink).trigger('shown.bs.tab');
}

$(document).ready(function() {
    // Intercept click on tab pills
    $('a[data-toggle="pill"]').on('click', function(e) {
        e.preventDefault();
        const targetId = $(this).attr('href');
        switchSettingsTab(targetId, this);
    });

    // Restore tab from URL params, hash, or localStorage
    const urlParams = new URLSearchParams(window.location.search);
    const activeTabFromUrl = urlParams.get('active_tab');
    let targetTab = activeTabFromUrl || window.location.hash || localStorage.getItem('active_settings_tab') || '#pill-general';

    if (targetTab && $(targetTab).length) {
        switchSettingsTab(targetTab);
    }
});

// 2. Mechanical Tactile Switch Toggle Utility
function toggleSwitch(key) {
    const input = document.getElementById('val-' + key);
    if (!input) return;
    const wrapper = input.nextElementSibling;
    const btn = wrapper ? wrapper.querySelector('.tactile-switch-btn') : null;
    const label = document.getElementById('lbl-' + key);
    
    if (!btn) return;

    if (input.value === '1') {
        input.value = '0';
        btn.classList.remove('is-active');
        if (label) label.textContent = 'OFF';
    } else {
        input.value = '1';
        btn.classList.add('is-active');
        if (label) label.textContent = 'ON';
    }
}

// 3. Vault Secret Visibility with 15-second Auto-Hide Timer
function toggleVaultVisibility(inputId, button) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const icon = button.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
        
        // Clear previous timer if any
        if (input._hideTimeout) {
            clearTimeout(input._hideTimeout);
        }
        
        // Auto-hide after 15 seconds for screen security
        input._hideTimeout = setTimeout(function() {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }, 15000);
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
        if (input._hideTimeout) {
            clearTimeout(input._hideTimeout);
        }
    }
}

// 4. 1-Click Copy Utility
function copyFieldValue(inputId, button) {
    const input = document.getElementById(inputId);
    if (!input || !input.value) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                text: "{{ __('messages.vault_empty_info') }}",
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                background: '#0f172a',
                color: '#f8fafc'
            });
        }
        return;
    }

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(input.value);
    } else {
        const ta = document.createElement('textarea');
        ta.value = input.value;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
    }

    const icon = button.querySelector('i');
    const origClass = icon.className;
    icon.className = 'fas fa-check text-success';
    
    setTimeout(function() {
        icon.className = origClass;
    }, 1800);

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: "{{ __('messages.vault_copied_success') }}",
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1600,
            background: '#0f172a',
            color: '#f8fafc'
        });
    }
}

// 5. AJAX Live Telemetry Connection Tests
$(document).ready(function() {
    // Telegram Live Test
    $('#btn-test-telegram').click(function() {
        const botToken = $('input[name="telegram_bot_token"]').val();
        const chatId = $('input[name="telegram_chat_id"]').val();
        
        if (!botToken || !chatId) {
            Swal.fire({
                icon: 'error',
                text: "{{ __('messages.telegram_test_validation_err') }}",
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                background: '#0f172a',
                color: '#f8fafc',
                customClass: { popup: 'border border-danger' }
            });
            return;
        }

        const btn = $(this);
        const spinner = $('#test-tg-spinner');
        const resultDiv = $('#test-tg-result');

        btn.prop('disabled', true);
        spinner.removeClass('d-none');
        resultDiv.html('');

        $.ajax({
            url: '{{ route("settings.telegram.test") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                telegram_bot_token: botToken,
                telegram_chat_id: chatId
            },
            success: function(res) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                if (res.success) {
                    resultDiv.html('<div class="alert alert-success mt-2 mb-0" style="border-radius: 10px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399;"><i class="fas fa-check-circle mr-2"></i> ' + res.message + '</div>');
                } else {
                    resultDiv.html('<div class="alert alert-danger mt-2 mb-0" style="border-radius: 10px; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171;"><i class="fas fa-exclamation-triangle mr-2"></i> ' + (res.message || 'Error') + '</div>');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                resultDiv.html('<div class="alert alert-danger mt-2 mb-0" style="border-radius: 10px; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171;"><i class="fas fa-times-circle mr-2"></i> {{ __("messages.telegram_test_conn_err") }}</div>');
            }
        });
    });

    // WhatsApp Live Test
    $('#btn-test-whatsapp').click(function() {
        const adminPhone = $('input[name="whatsapp_admin_phone"]').val();
        if (!adminPhone) {
            Swal.fire({
                icon: 'error',
                text: "{{ __('messages.whatsapp_test_validation_err') }}",
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                background: '#0f172a',
                color: '#f8fafc',
                customClass: { popup: 'border border-danger' }
            });
            return;
        }

        const btn = $(this);
        const spinner = $('#test-wa-spinner');
        const resultDiv = $('#test-wa-result');

        btn.prop('disabled', true);
        spinner.removeClass('d-none');
        resultDiv.html('');

        $.ajax({
            url: '{{ route("settings.whatsapp.test") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                whatsapp_admin_phone: adminPhone
            },
            success: function(res) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                if (res.success) {
                    resultDiv.html('<div class="alert alert-success mt-2 mb-0" style="border-radius: 10px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399;"><i class="fas fa-check-circle mr-2"></i> ' + res.message + '</div>');
                } else {
                    resultDiv.html('<div class="alert alert-danger mt-2 mb-0" style="border-radius: 10px; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171;"><i class="fas fa-exclamation-triangle mr-2"></i> ' + (res.message || 'Error') + '</div>');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                resultDiv.html('<div class="alert alert-danger mt-2 mb-0" style="border-radius: 10px; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171;"><i class="fas fa-times-circle mr-2"></i> {{ __("messages.whatsapp_test_conn_err") }}</div>');
            }
        });
    });
});
</script>
@endpush
