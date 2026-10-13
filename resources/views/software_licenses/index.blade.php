@extends('layouts.admin')

@section('title', __('messages.software_licenses'))

@section('content')
<!-- Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 -->
<style>
    /* ═══════════════════════════════════════════════════════════════════
       HALLMARK WORKBENCH: SOFTWARE LICENSES & COMPLIANCE COMMAND CENTER
       ═══════════════════════════════════════════════════════════════════ */
    .lic-workbench {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        overflow-x: clip;
    }

    /* 1. Header Command Banner */
    .lic-header-card {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 16px;
        padding: 20px 24px;
        position: relative;
        overflow: hidden;
    }
    .lic-header-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, #6366f1 0%, #38bdf8 50%, transparent 100%);
    }
    .lic-header-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .lic-header-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: rgba(99, 102, 241, 0.12);
        border: 1px solid rgba(99, 102, 241, 0.28);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #818cf8;
        font-size: 1.35rem;
        flex-shrink: 0;
        box-shadow: 0 0 20px rgba(99, 102, 241, 0.2);
    }
    .lic-vault-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(56, 189, 248, 0.1);
        border: 1px solid rgba(56, 189, 248, 0.25);
        color: #38bdf8;
        padding: 2px 10px;
        border-radius: 9999px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .lic-pulse-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #38bdf8;
        box-shadow: 0 0 8px #38bdf8;
        animation: licPulse 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
    }
    @keyframes licPulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.85); }
    }
    .btn-add-license {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        padding: 0.6rem 1.35rem;
        font-weight: 600;
        font-size: 0.88rem;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none !important;
    }
    .btn-add-license:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.5);
        background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    }

    /* 2. Bento KPI Matrix */
    .lic-bento-card {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 14px;
        padding: 16px 18px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease, border-color 0.2s ease;
    }
    .lic-bento-card:hover {
        transform: translateY(-2px);
        border-color: rgba(99, 102, 241, 0.35);
    }
    .lic-bento-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .lic-bento-label {
        font-size: 0.76rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--color-ink-2, #94a3b8);
    }
    .lic-bento-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
    }
    .lic-bento-val {
        font-family: 'JetBrains Mono', monospace;
        font-size: 1.85rem;
        font-weight: 700;
        line-height: 1.1;
        margin-bottom: 4px;
    }
    .lic-bento-footer {
        font-size: 0.74rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Bento Colors */
    .bento-total .lic-bento-icon { background: rgba(99, 102, 241, 0.12); color: #818cf8; }
    .bento-total .lic-bento-val { color: #f8fafc; }
    
    .bento-seats .lic-bento-icon { background: rgba(56, 189, 248, 0.12); color: #38bdf8; }
    .bento-seats .lic-bento-val { color: #38bdf8; }

    .bento-warning .lic-bento-icon { background: rgba(245, 158, 11, 0.14); color: #f59e0b; }
    .bento-warning .lic-bento-val { color: #f59e0b; }

    .bento-danger .lic-bento-icon { background: rgba(239, 68, 68, 0.14); color: #ef4444; }
    .bento-danger .lic-bento-val { color: #ef4444; }

    /* 3. Tactical Filter Ribbon */
    .lic-ribbon-card {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 14px;
        padding: 14px 18px;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        box-sizing: border-box;
        overflow: hidden;
    }
    .lic-ribbon-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        width: 100%;
        max-width: 100%;
        min-width: 0;
    }
    .lic-status-tabs-container {
        min-width: 0;
        flex: 1 1 auto;
        max-width: 100%;
        overflow: hidden;
        position: relative;
    }
    .lic-status-tabs {
        display: flex;
        align-items: center;
        gap: 6px;
        overflow-x: auto;
        overflow-y: hidden;
        padding-bottom: 2px;
        -webkit-overflow-scrolling: touch;
        touch-action: pan-x;
        overscroll-behavior-x: contain;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    .lic-status-tabs::-webkit-scrollbar {
        display: none;
    }
    .lic-status-tab {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 10px;
        background: rgba(15, 23, 42, 0.4);
        border: 1px solid rgba(148, 163, 184, 0.16);
        color: #94a3b8;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none !important;
        white-space: nowrap;
        flex: 0 0 auto;
        flex-shrink: 0;
        user-select: none;
        transition: all 0.18s ease;
    }
    .lic-status-tab:hover {
        background: rgba(15, 23, 42, 0.8);
        color: #f1f5f9;
        border-color: rgba(148, 163, 184, 0.3);
    }
    .lic-status-tab.active-all {
        background: rgba(99, 102, 241, 0.18);
        border-color: rgba(99, 102, 241, 0.5);
        color: #a5b4fc;
    }
    .lic-status-tab.active-active {
        background: rgba(16, 185, 129, 0.18);
        border-color: rgba(16, 185, 129, 0.5);
        color: #6ee7b7;
    }
    .lic-status-tab.active-warning {
        background: rgba(245, 158, 11, 0.18);
        border-color: rgba(245, 158, 11, 0.5);
        color: #fcd34d;
    }
    .lic-status-tab.active-danger {
        background: rgba(239, 68, 68, 0.18);
        border-color: rgba(239, 68, 68, 0.5);
        color: #fca5a5;
    }
    .lic-status-tab.active-lifetime {
        background: rgba(56, 189, 248, 0.18);
        border-color: rgba(56, 189, 248, 0.5);
        color: #7dd3fc;
    }
    .lic-tab-count {
        background: rgba(0, 0, 0, 0.28);
        border-radius: 999px;
        padding: 1px 7px;
        font-size: 0.72rem;
        font-family: 'JetBrains Mono', monospace;
    }

    /* Filter Controls Search & PIC */
    .lic-search-controls {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: nowrap;
    }
    .lic-search-input-box {
        position: relative;
        min-width: 220px;
    }
    .lic-search-input-box i.search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 0.85rem;
        pointer-events: none;
    }
    .lic-search-input-box input {
        width: 100%;
        box-sizing: border-box;
        height: 38px;
        padding-left: 36px;
        padding-right: 32px;
        border-radius: 10px;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(148, 163, 184, 0.2);
        color: #f8fafc;
        font-size: 0.85rem;
    }
    .lic-search-input-box input:focus {
        background: rgba(15, 23, 42, 0.8);
        border-color: #6366f1;
        outline: none;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    }
    .lic-clear-search {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.8rem;
    }
    .lic-pic-select {
        height: 38px;
        border-radius: 10px;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(148, 163, 184, 0.2);
        color: #f8fafc;
        font-size: 0.85rem;
        padding: 0 12px;
    }

    /* 4. Desktop Data Grid Table */
    .lic-table-card {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 16px;
        overflow: hidden;
    }
    .lic-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    .lic-table thead th {
        background: rgba(15, 23, 42, 0.85);
        color: #94a3b8;
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 14px 18px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.12);
    }
    .lic-table tbody tr {
        transition: background-color 0.15s ease;
    }
    .lic-table tbody tr:hover {
        background-color: rgba(99, 102, 241, 0.05);
    }
    .lic-table tbody td {
        padding: 14px 18px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(148, 163, 184, 0.08);
        font-size: 0.86rem;
    }
    .lic-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* License Key Mono Chip */
    .lic-key-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 8px;
        padding: 4px 8px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.80rem;
        color: #cbd5e1;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .lic-key-chip:hover {
        border-color: #6366f1;
        background: rgba(99, 102, 241, 0.12);
        color: #ffffff;
    }
    .lic-copy-btn {
        background: transparent;
        border: none;
        color: #94a3b8;
        padding: 2px 4px;
        font-size: 0.78rem;
        cursor: pointer;
        border-radius: 4px;
        transition: all 0.15s ease;
    }
    .lic-copy-btn:hover {
        color: #38bdf8;
    }

    /* Status Badges */
    .lic-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        border-radius: 8px;
        font-size: 0.74rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .lic-badge-active {
        background: rgba(16, 185, 129, 0.12);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #10b981;
    }
    .lic-badge-warning {
        background: rgba(245, 158, 11, 0.12);
        border: 1px solid rgba(245, 158, 11, 0.3);
        color: #f59e0b;
    }
    .lic-badge-danger {
        background: rgba(239, 68, 68, 0.12);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #ef4444;
    }
    .lic-badge-lifetime {
        background: rgba(56, 189, 248, 0.12);
        border: 1px solid rgba(56, 189, 248, 0.3);
        color: #38bdf8;
    }
    .lic-seats-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 2px 8px;
        border-radius: 6px;
        background: rgba(99, 102, 241, 0.12);
        border: 1px solid rgba(99, 102, 241, 0.25);
        color: #818cf8;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.78rem;
        font-weight: 600;
    }

    /* Action Buttons */
    .lic-action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        transition: all 0.15s ease;
    }
    .lic-btn-edit {
        background: rgba(245, 158, 11, 0.12);
        border: 1px solid rgba(245, 158, 11, 0.3);
        color: #fbbf24;
    }
    .lic-btn-edit:hover {
        background: rgba(245, 158, 11, 0.25);
        color: #fef3c7;
    }
    .lic-btn-del {
        background: rgba(239, 68, 68, 0.12);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #f87171;
    }
    .lic-btn-del:hover {
        background: rgba(239, 68, 68, 0.25);
        color: #fee2e2;
    }

    /* 5. Mobile Tactile Feed Cards */
    .lic-mobile-card {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 14px;
        padding: 15px 16px;
        margin-bottom: 12px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
        transition: transform 0.15s ease, border-color 0.15s ease;
    }
    .lic-mobile-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
    }
    .lic-mobile-card.border-active::before { background: #10b981; }
    .lic-mobile-card.border-warning::before { background: #f59e0b; }
    .lic-mobile-card.border-danger::before { background: #ef4444; }
    .lic-mobile-card.border-lifetime::before { background: #38bdf8; }

    /* Top Bar: #No + Seats on Left, Status Badge on Right */
    .lic-m-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 8px;
    }
    .lic-m-num {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.72rem;
        font-weight: 700;
        background: rgba(148, 163, 184, 0.15);
        border: 1px solid rgba(148, 163, 184, 0.25);
        color: #e2e8f0;
        padding: 2px 7px;
        border-radius: 6px;
    }

    /* Software Name: Full width row with icon */
    .lic-m-name {
        font-weight: 700;
        font-size: 0.98rem;
        line-height: 1.35;
        color: #f8fafc;
        margin-bottom: 10px;
        word-break: break-word;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .lic-m-name i {
        color: #818cf8;
        font-size: 0.95rem;
        flex-shrink: 0;
    }

    /* License Key Box (Interactive 1-Click Copy Box) */
    .lic-m-key-box {
        background: rgba(15, 23, 42, 0.75);
        border: 1px solid rgba(148, 163, 184, 0.16);
        border-radius: 10px;
        padding: 9px 12px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        cursor: pointer;
        transition: all 0.18s ease;
    }
    .lic-m-key-box:hover, .lic-m-key-box:active {
        background: rgba(99, 102, 241, 0.12);
        border-color: rgba(99, 102, 241, 0.4);
    }
    .lic-m-key-left {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
        flex: 1 1 auto;
    }
    .lic-m-key-text {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.80rem;
        color: #e2e8f0;
        word-break: break-all;
    }
    .lic-m-copy-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
        background: rgba(56, 189, 248, 0.12);
        border: 1px solid rgba(56, 189, 248, 0.28);
        color: #38bdf8;
        white-space: nowrap;
        flex-shrink: 0;
        transition: all 0.15s ease;
    }

    /* 2x2 Grid Metadata */
    .lic-m-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        row-gap: 10px;
        column-gap: 12px;
        background: rgba(0, 0, 0, 0.28);
        border: 1px solid rgba(148, 163, 184, 0.08);
        border-radius: 10px;
        padding: 11px 13px;
        margin-bottom: 12px;
    }
    .lic-m-meta-item {
        min-width: 0;
    }
    .lic-m-meta-label {
        font-size: 0.66rem;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.04em;
        margin-bottom: 3px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .lic-m-meta-val {
        font-size: 0.82rem;
        color: #e2e8f0;
        font-weight: 600;
        word-break: break-word;
    }

    /* Actions Footer */
    .lic-m-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        border-top: 1px solid rgba(148, 163, 184, 0.12);
        padding-top: 12px;
    }
    .lic-m-btn-action {
        flex: 1 1 50%;
        height: 40px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 0.84rem;
        font-weight: 600;
        text-decoration: none !important;
        border: 1px solid transparent;
        transition: all 0.15s ease;
    }
    .lic-m-btn-edit {
        background: rgba(245, 158, 11, 0.14);
        border-color: rgba(245, 158, 11, 0.3);
        color: #fbbf24 !important;
    }
    .lic-m-btn-edit:hover, .lic-m-btn-edit:active {
        background: rgba(245, 158, 11, 0.28);
    }
    .lic-m-btn-del {
        background: rgba(239, 68, 68, 0.14);
        border-color: rgba(239, 68, 68, 0.3);
        color: #f87171 !important;
        width: 100%;
    }
    .lic-m-btn-del:hover, .lic-m-btn-del:active {
        background: rgba(239, 68, 68, 0.28);
    }

    /* Responsive Adjustments */
    @media (max-width: 767.98px) {
        .lic-header-card {
            padding: 14px 16px;
            border-radius: 14px;
        }
        .lic-header-wrapper {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }
        .lic-header-left {
            display: flex !important;
            align-items: flex-start !important;
            gap: 12px !important;
        }
        .lic-header-icon {
            width: 40px !important;
            height: 40px !important;
            font-size: 1.15rem !important;
            margin-top: 2px !important;
            flex-shrink: 0 !important;
        }
        .lic-title-row {
            display: flex !important;
            align-items: center !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
            margin-bottom: 4px !important;
        }
        .lic-title-text {
            font-size: 1.15rem !important;
            line-height: 1.25 !important;
        }
        .lic-subtitle-text {
            font-size: 0.78rem !important;
            line-height: 1.35 !important;
            white-space: normal !important;
        }
        .lic-vault-badge {
            font-size: 0.68rem !important;
            padding: 2px 8px !important;
            white-space: nowrap !important;
        }
        .lic-header-wrapper > div:last-child {
            width: 100%;
        }
        .btn-add-license {
            width: 100%;
            justify-content: center;
            height: 42px;
            font-size: 0.86rem;
            border-radius: 10px;
        }

        /* 2x2 Bento KPI Grid */
        .lic-bento-row {
            margin-left: -4px !important;
            margin-right: -4px !important;
        }
        .lic-bento-col {
            padding-left: 4px !important;
            padding-right: 4px !important;
            margin-bottom: 8px !important;
        }
        .lic-bento-card {
            padding: 10px 12px !important;
            border-radius: 12px !important;
        }
        .lic-bento-top {
            margin-bottom: 4px !important;
        }
        .lic-bento-label {
            font-size: 0.64rem !important;
            letter-spacing: 0.03em !important;
        }
        .lic-bento-icon {
            width: 28px !important;
            height: 28px !important;
            font-size: 0.8rem !important;
            border-radius: 8px !important;
        }
        .lic-bento-val {
            font-size: 1.35rem !important;
            margin-bottom: 2px !important;
        }
        .lic-bento-footer {
            font-size: 0.67rem !important;
            line-height: 1.25 !important;
        }

        /* Tactical Filter Ribbon on Mobile */
        .lic-ribbon-card {
            padding: 12px 14px !important;
            border-radius: 14px !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
            overflow: hidden !important;
        }
        .lic-ribbon-wrapper {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
        }
        .lic-status-tabs-container {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            overflow: hidden !important;
            position: relative !important;
        }
        .lic-status-tabs {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            overflow-x: auto !important;
            overflow-y: hidden !important;
            -webkit-overflow-scrolling: touch !important;
            touch-action: pan-x !important;
            overscroll-behavior-x: contain !important;
            gap: 8px !important;
            padding-bottom: 6px !important;
            padding-right: 16px !important;
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
            cursor: grab;
        }
        .lic-status-tabs:active {
            cursor: grabbing;
        }
        .lic-status-tabs::-webkit-scrollbar {
            display: none !important;
        }
        .lic-status-tab {
            flex: 0 0 auto !important;
            flex-shrink: 0 !important;
            padding: 7px 13px !important;
            font-size: 0.78rem !important;
            border-radius: 8px !important;
            white-space: nowrap !important;
        }
        .lic-search-controls {
            flex-direction: column !important;
            width: 100% !important;
            gap: 8px !important;
        }
        .lic-search-input-box {
            width: 100% !important;
            min-width: 0 !important;
        }
        .lic-search-input-box input {
            width: 100% !important;
            display: block !important;
            box-sizing: border-box !important;
            height: 40px !important;
            font-size: 0.82rem !important;
            padding-left: 36px !important;
            padding-right: 32px !important;
        }
        .lic-pic-select {
            width: 100% !important;
            max-width: 100% !important;
            display: block !important;
            box-sizing: border-box !important;
            height: 40px !important;
            font-size: 0.82rem !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 14px center !important;
            padding-right: 36px !important;
        }
        .lic-pic-select option {
            background-color: #0f172a;
            color: #f8fafc;
        }
    }

</style>

<div class="lic-workbench">
    <!-- 1. Header Command Banner -->
    <div class="lic-header-card">
        <div class="lic-header-wrapper">
            <div class="lic-header-left">
                <div class="lic-header-icon">
                    <i class="fas fa-certificate"></i>
                </div>
                <div style="min-width: 0; flex: 1 1 auto;">
                    <div class="lic-title-row">
                        <h4 class="mb-0 font-weight-bold theme-text lic-title-text">
                            {{ __('messages.software_licenses') }}
                        </h4>
                        <span class="lic-vault-badge">
                            <span class="lic-pulse-dot"></span>
                            {{ __('messages.license_vault_tagline') }}
                        </span>
                    </div>
                    <p class="text-muted mb-0 lic-subtitle-text">
                        {{ __('messages.license_subtitle') }}
                    </p>
                </div>
            </div>

            @can('action_manage_settings')
            <div>
                <a href="{{ route('software_licenses.create') }}" class="btn-add-license">
                    <i class="fas fa-plus-circle"></i>
                    <span>{{ __('messages.add_license') }}</span>
                </a>
            </div>
            @endcan
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.35); color: #34d399; border-radius: 12px;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="color: #34d399;">×</button>
            <i class="icon fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-0" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.35); color: #f87171; border-radius: 12px;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="color: #f87171;">×</button>
            <i class="icon fas fa-ban mr-2"></i> {{ session('error') }}
        </div>
    @endif

    <!-- 2. Bento KPI Matrix (2x2 on mobile, 4 in a row on desktop) -->
    <div class="row g-2 g-md-3 lic-bento-row">
        <!-- Total Licenses -->
        <div class="col-6 col-xl-3 lic-bento-col mb-2 mb-xl-0">
            <div class="lic-bento-card bento-total">
                <div>
                    <div class="lic-bento-top">
                        <span class="lic-bento-label">{{ __('messages.total_licenses') }}</span>
                        <div class="lic-bento-icon">
                            <i class="fas fa-cubes"></i>
                        </div>
                    </div>
                    <div class="lic-bento-val">{{ $summary['total_licenses'] }}</div>
                </div>
                <div class="lic-bento-footer">
                    <i class="fas fa-check text-success"></i>
                    <span>{{ $summary['active_count'] }} {{ __('messages.active_licenses_count') }}</span>
                </div>
            </div>
        </div>

        <!-- Total Seats -->
        <div class="col-6 col-xl-3 lic-bento-col mb-2 mb-xl-0">
            <div class="lic-bento-card bento-seats">
                <div>
                    <div class="lic-bento-top">
                        <span class="lic-bento-label">{{ __('messages.total_allocated_seats') }}</span>
                        <div class="lic-bento-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="lic-bento-val">{{ $summary['total_seats'] }}</div>
                </div>
                <div class="lic-bento-footer">
                    <i class="fas fa-infinity text-info"></i>
                    <span>{{ $summary['lifetime_count'] }} {{ __('messages.lifetime_licenses_count') }}</span>
                </div>
            </div>
        </div>

        <!-- Expiring Soon (<= 30 Days) -->
        <div class="col-6 col-xl-3 lic-bento-col mb-2 mb-xl-0">
            <div class="lic-bento-card bento-warning">
                <div>
                    <div class="lic-bento-top">
                        <span class="lic-bento-label">{{ __('messages.expiring_soon_count') }}</span>
                        <div class="lic-bento-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="lic-bento-val">{{ $summary['expiring_soon'] }}</div>
                </div>
                <div class="lic-bento-footer">
                    <i class="fas fa-hourglass-half text-warning"></i>
                    <span>≤ 30 {{ __('messages.days_remaining') }}</span>
                </div>
            </div>
        </div>

        <!-- Expired -->
        <div class="col-6 col-xl-3 lic-bento-col mb-2 mb-xl-0">
            <div class="lic-bento-card bento-danger">
                <div>
                    <div class="lic-bento-top">
                        <span class="lic-bento-label">{{ __('messages.expired_licenses_count') }}</span>
                        <div class="lic-bento-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                    <div class="lic-bento-val">{{ $summary['expired_count'] }}</div>
                </div>
                <div class="lic-bento-footer">
                    <i class="fas fa-sync text-danger"></i>
                    <span>{{ __('messages.software_license_renewals') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Tactical Filter Ribbon & Search Bar -->
    <div class="lic-ribbon-card">
        <div class="lic-ribbon-wrapper">
            <!-- Status Tabs Container with Mobile Swipe Hint -->
            <div class="lic-status-tabs-container">
                <div class="d-flex align-items-center justify-content-between d-md-none mb-2 px-1">
                    <span class="text-muted font-weight-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">
                        <i class="fas fa-filter mr-1 text-indigo"></i> {{ __('messages.status') }}
                    </span>
                    <span class="text-muted font-weight-bold" style="font-size: 0.70rem;">
                        <i class="fas fa-arrows-alt-h text-info mr-1"></i> {{ __('messages.swipe_horizontal') }}
                    </span>
                </div>
                <div class="lic-status-tabs">
                    <a href="{{ route('software_licenses.index', array_merge(request()->except('status'), ['status' => 'all'])) }}"
                       class="lic-status-tab {{ $statusFilter == 'all' ? 'active-all' : '' }}">
                        <i class="fas fa-layer-group text-xs"></i>
                        <span>{{ __('messages.filter_all_licenses') }}</span>
                        <span class="lic-tab-count">{{ $summary['total_licenses'] }}</span>
                    </a>
                    <a href="{{ route('software_licenses.index', array_merge(request()->except('status'), ['status' => 'active'])) }}"
                       class="lic-status-tab {{ $statusFilter == 'active' ? 'active-active' : '' }}">
                        <i class="fas fa-check-circle text-xs"></i>
                        <span>{{ __('messages.filter_active') }}</span>
                        <span class="lic-tab-count">{{ $summary['active_count'] }}</span>
                    </a>
                    <a href="{{ route('software_licenses.index', array_merge(request()->except('status'), ['status' => 'expiring_soon'])) }}"
                       class="lic-status-tab {{ $statusFilter == 'expiring_soon' ? 'active-warning' : '' }}">
                        <i class="fas fa-clock text-xs"></i>
                        <span>{{ __('messages.filter_expiring_soon') }}</span>
                        <span class="lic-tab-count">{{ $summary['expiring_soon'] }}</span>
                    </a>
                    <a href="{{ route('software_licenses.index', array_merge(request()->except('status'), ['status' => 'expired'])) }}"
                       class="lic-status-tab {{ $statusFilter == 'expired' ? 'active-danger' : '' }}">
                        <i class="fas fa-ban text-xs"></i>
                        <span>{{ __('messages.filter_expired') }}</span>
                        <span class="lic-tab-count">{{ $summary['expired_count'] }}</span>
                    </a>
                    <a href="{{ route('software_licenses.index', array_merge(request()->except('status'), ['status' => 'lifetime'])) }}"
                       class="lic-status-tab {{ $statusFilter == 'lifetime' ? 'active-lifetime' : '' }}">
                        <i class="fas fa-infinity text-xs"></i>
                        <span>{{ __('messages.filter_lifetime') }}</span>
                        <span class="lic-tab-count">{{ $summary['lifetime_count'] }}</span>
                    </a>
                    <div style="flex: 0 0 16px; width: 16px; height: 1px; pointer-events: none;"></div>
                </div>
            </div>

            <!-- Search and PIC Filter Controls -->
            <form action="{{ route('software_licenses.index') }}" method="GET" class="lic-search-controls" id="lic-filter-form">
                @if($statusFilter != 'all')
                    <input type="hidden" name="status" value="{{ $statusFilter }}">
                @endif
                <div class="lic-search-input-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" placeholder="{{ __('messages.search_license') }}" value="{{ request('search') }}" autocomplete="off">
                    @if(request('search'))
                        <a href="{{ route('software_licenses.index', request()->except('search')) }}" class="lic-clear-search" title="{{ __('messages.clear') }}">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
                <select name="pic_id" class="lic-pic-select" onchange="document.getElementById('lic-filter-form').submit();">
                    <option value="">{{ __('messages.all_filter') }} {{ __('messages.pic') }}</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('pic_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <!-- 4. Data View: Dual-Mode Desktop Table & Mobile Feed Cards -->
    <!-- DESKTOP TABLE VIEW (d-none d-md-block) -->
    <div class="lic-table-card d-none d-md-block">
        <div class="table-responsive">
            <table class="lic-table">
                <thead>
                    <tr>
                        <th width="60" class="text-center">{{ __('messages.no') }}</th>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.license_key') }}</th>
                        <th>{{ __('messages.expiry_date') }}</th>
                        <th>{{ __('messages.total_seats') }}</th>
                        <th>{{ __('messages.pic') }}</th>
                        <th>{{ __('messages.notes') }}</th>
                        <th width="120" class="text-center">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php $today = now()->startOfDay(); @endphp
                    @forelse($software_licenses as $lic)
                        @php
                            $isLifetime = is_null($lic->expiry_date);
                            $diffDays = $lic->expiry_date ? $today->diffInDays($lic->expiry_date, false) : null;
                        @endphp
                        <tr>
                            <!-- No -->
                            <td class="text-center text-muted font-weight-bold" style="font-family: 'JetBrains Mono', monospace; font-size: 0.82rem;">
                                {{ $loop->iteration }}
                            </td>

                            <!-- Software Name -->
                            <td>
                                <div class="d-flex align-items-center" style="gap: 10px;">
                                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(56, 189, 248, 0.12); border: 1px solid rgba(56, 189, 248, 0.25); display: flex; align-items: center; justify-content: center; color: #38bdf8; font-size: 0.85rem; flex-shrink: 0;">
                                        <i class="fas fa-cube"></i>
                                    </div>
                                    <div>
                                        <span class="font-weight-bold theme-text d-block" style="font-size: 0.92rem;">
                                            {{ $lic->name }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- License Key with 1-Click Copy -->
                            <td>
                                @if($lic->license_key)
                                    <div class="lic-key-chip copy-license-chip" data-license-key="{{ $lic->license_key }}" title="{{ __('messages.copy_license') }}: {{ $lic->license_key }}">
                                        <i class="fas fa-key text-muted text-xs"></i>
                                        <span>{{ Str::limit($lic->license_key, 22) }}</span>
                                        <button type="button" class="lic-copy-btn" title="{{ __('messages.copy_license') }}">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-muted" style="font-size: 0.8rem;">—</span>
                                @endif
                            </td>

                            <!-- Expiry Date & Status Badge -->
                            <td>
                                @if($isLifetime)
                                    <span class="lic-badge lic-badge-lifetime">
                                        <i class="fas fa-infinity text-xs"></i>
                                        <span>{{ __('messages.perpetual_active') }}</span>
                                    </span>
                                @elseif($diffDays < 0)
                                    <span class="lic-badge lic-badge-danger" title="{{ $lic->expiry_date->format('d M Y') }}">
                                        <i class="fas fa-times-circle text-xs"></i>
                                        <span>{{ __('messages.filter_expired') }} ({{ abs($diffDays) }} {{ __('messages.days_remaining') }} {{ __('messages.expired_ago') }})</span>
                                    </span>
                                @elseif($diffDays <= 30)
                                    <span class="lic-badge lic-badge-warning" title="{{ $lic->expiry_date->format('d M Y') }}">
                                        <i class="fas fa-hourglass-half text-xs"></i>
                                        <span>{{ $diffDays == 0 ? __('messages.today') : $diffDays . ' ' . __('messages.days_remaining') }}</span>
                                    </span>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">{{ $lic->expiry_date->format('d M Y') }}</small>
                                @else
                                    <span class="lic-badge lic-badge-active">
                                        <i class="fas fa-check-circle text-xs"></i>
                                        <span>{{ $lic->expiry_date->format('d M Y') }}</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Total Seats -->
                            <td>
                                <span class="lic-seats-badge">
                                    <i class="fas fa-user-check text-xs"></i>
                                    {{ $lic->total_seats }} {{ __('messages.seats') }}
                                </span>
                            </td>

                            <!-- PIC -->
                            <td>
                                @if($lic->pic)
                                    <div class="d-flex align-items-center" style="gap: 8px;">
                                        <div style="width: 26px; height: 26px; border-radius: 50%; background: rgba(99, 102, 241, 0.18); color: #818cf8; display: flex; align-items: center; justify-content: center; font-size: 0.72rem; font-weight: 700;">
                                            {{ strtoupper(substr($lic->pic->name, 0, 1)) }}
                                        </div>
                                        <span class="theme-text text-truncate" style="max-width: 140px; font-size: 0.84rem;">
                                            {{ $lic->pic->name }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-muted" style="font-size: 0.8rem;">—</span>
                                @endif
                            </td>

                            <!-- Notes -->
                            <td>
                                <span class="text-muted text-truncate d-inline-block" style="max-width: 180px; font-size: 0.80rem;" title="{{ $lic->notes }}">
                                    {{ $lic->notes ?: '—' }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="text-center">
                                <div class="d-flex justify-content-center" style="gap: 8px;">
                                    @can('action_manage_settings')
                                    <a href="{{ route('software_licenses.edit', array_merge([$lic->id], request()->query())) }}"
                                       class="lic-action-btn lic-btn-edit"
                                       title="{{ __('messages.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('software_licenses.destroy', array_merge([$lic->id], request()->query())) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="lic-action-btn lic-btn-del btn-delete"
                                                title="{{ __('messages.delete') }}"
                                                data-confirm-message="{{ __('messages.confirm_delete') }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div style="color: #64748b;">
                                    <i class="fas fa-certificate fa-2x mb-3" style="opacity: 0.4;"></i>
                                    <p class="mb-0 font-weight-bold" style="font-size: 0.95rem;">{{ __('messages.no_data') }}</p>
                                    <small>{{ __('messages.license_subtitle') }}</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MOBILE TACTILE FEED CARDS (d-block d-md-none) -->
    <div class="d-block d-md-none lic-mobile-feed">
        @php $today = now()->startOfDay(); @endphp
        @forelse($software_licenses as $lic)
            @php
                $isLifetime = is_null($lic->expiry_date);
                $diffDays = $lic->expiry_date ? $today->diffInDays($lic->expiry_date, false) : null;
                $borderClass = $isLifetime ? 'border-lifetime' : ($diffDays < 0 ? 'border-danger' : ($diffDays <= 30 ? 'border-warning' : 'border-active'));
            @endphp
            <div class="lic-mobile-card {{ $borderClass }}">
                <!-- Card Top Bar: #No & Seats on Left, Status Badge on Right -->
                <div class="lic-m-topbar">
                    <div class="d-flex align-items-center" style="gap: 6px;">
                        <span class="lic-m-num">#{{ $loop->iteration }}</span>
                        <span class="lic-seats-badge">
                            <i class="fas fa-users mr-1"></i>{{ $lic->total_seats }} {{ __('messages.seats') }}
                        </span>
                    </div>
                    <div>
                        @if($isLifetime)
                            <span class="lic-badge lic-badge-lifetime">
                                <i class="fas fa-infinity text-xs"></i>
                                <span>{{ __('messages.filter_lifetime') }}</span>
                            </span>
                        @elseif($diffDays < 0)
                            <span class="lic-badge lic-badge-danger">
                                <i class="fas fa-times-circle text-xs"></i>
                                <span>{{ __('messages.filter_expired') }}</span>
                            </span>
                        @elseif($diffDays <= 30)
                            <span class="lic-badge lic-badge-warning">
                                <i class="fas fa-hourglass-half text-xs"></i>
                                <span>{{ $diffDays == 0 ? __('messages.today') : $diffDays . ' ' . __('messages.days_remaining') }}</span>
                            </span>
                        @else
                            <span class="lic-badge lic-badge-active">
                                <i class="fas fa-check-circle text-xs"></i>
                                <span>{{ __('messages.filter_active') }}</span>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Software Name (Full Row - Never cramped) -->
                <div class="lic-m-name">
                    <i class="fas fa-cube"></i>
                    <span>{{ $lic->name }}</span>
                </div>

                <!-- License Key Box (Interactive 1-Click Copy Box) -->
                @if($lic->license_key)
                    <div class="lic-m-key-box copy-license-chip" data-license-key="{{ $lic->license_key }}" role="button" title="{{ __('messages.copy_license') }}">
                        <div class="lic-m-key-left">
                            <i class="fas fa-key text-muted text-xs"></i>
                            <span class="lic-m-key-text">{{ $lic->license_key }}</span>
                        </div>
                        <span class="lic-m-copy-pill">
                            <i class="far fa-copy"></i>
                            <span class="copy-pill-text">{{ __('messages.copy_license') }}</span>
                        </span>
                    </div>
                @endif

                <!-- 2x2 Grid Metadata -->
                <div class="lic-m-grid">
                    <!-- Expiry Date -->
                    <div class="lic-m-meta-item">
                        <span class="lic-m-meta-label">
                            <i class="fas fa-calendar-alt"></i> {{ __('messages.expiry_date') }}
                        </span>
                        <div class="lic-m-meta-val">
                            @if($isLifetime)
                                <span class="text-info font-weight-bold">
                                    <i class="fas fa-infinity text-xs mr-1"></i>{{ __('messages.perpetual_active') }}
                                </span>
                            @else
                                <span class="font-weight-bold text-white">
                                    {{ $lic->expiry_date->format('d M Y') }}
                                </span>
                                <small class="d-block font-weight-normal text-muted" style="font-size: 0.70rem; margin-top: 1px;">
                                    @if($diffDays < 0)
                                        <span class="text-danger">({{ abs($diffDays) }} {{ __('messages.days_remaining') }} {{ __('messages.expired_ago') }})</span>
                                    @elseif($diffDays == 0)
                                        <span class="text-warning font-weight-bold">({{ __('messages.today') }})</span>
                                    @else
                                        <span>({{ $diffDays }} {{ __('messages.days_remaining') }})</span>
                                    @endif
                                </small>
                            @endif
                        </div>
                    </div>

                    <!-- PIC -->
                    <div class="lic-m-meta-item">
                        <span class="lic-m-meta-label">
                            <i class="fas fa-user-shield"></i> {{ __('messages.pic') }}
                        </span>
                        <div class="lic-m-meta-val font-weight-bold">
                            @if($lic->pic)
                                <span class="text-white">{{ $lic->pic->name }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                    </div>

                    <!-- Notes (Span 2 Columns) -->
                    <div class="lic-m-meta-item" style="grid-column: span 2;">
                        <span class="lic-m-meta-label">
                            <i class="fas fa-sticky-note"></i> {{ __('messages.notes') }}
                        </span>
                        <div class="lic-m-meta-val text-muted" style="font-weight: 500; font-size: 0.78rem;">
                            {{ $lic->notes ?: '—' }}
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                @can('action_manage_settings')
                <div class="lic-m-actions">
                    <a href="{{ route('software_licenses.edit', array_merge([$lic->id], request()->query())) }}"
                       class="lic-m-btn-action lic-m-btn-edit">
                        <i class="fas fa-edit mr-1"></i>
                        <span>{{ __('messages.edit') }}</span>
                    </a>
                    <form action="{{ route('software_licenses.destroy', array_merge([$lic->id], request()->query())) }}" method="POST" class="d-inline m-0 flex-grow-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="lic-m-btn-action lic-m-btn-del btn-delete"
                                data-confirm-message="{{ __('messages.confirm_delete') }}">
                            <i class="fas fa-trash-alt mr-1"></i>
                            <span>{{ __('messages.delete') }}</span>
                        </button>
                    </form>
                </div>
                @endcan
            </div>
        @empty
            <div class="text-center py-5 lic-mobile-card">
                <i class="fas fa-certificate fa-2x mb-3" style="opacity: 0.4; color: #64748b;"></i>
                <p class="mb-0 font-weight-bold theme-text" style="font-size: 0.95rem;">{{ __('messages.no_data') }}</p>
                <small class="text-muted">{{ __('messages.license_subtitle') }}</small>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
// Robust Cross-Browser Clipboard Helper
function copyTextToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        return navigator.clipboard.writeText(text);
    } else {
        var textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        return new Promise(function(resolve, reject) {
            try {
                var successful = document.execCommand('copy');
                document.body.removeChild(textArea);
                if (successful) resolve();
                else reject(new Error('execCommand copy failed'));
            } catch (err) {
                document.body.removeChild(textArea);
                reject(err);
            }
        });
    }
}

$(document).ready(function() {
    // 1-Click Copy License Key with Visual State Feedback
    $(document).on('click', '.copy-license-chip', function(e) {
        e.preventDefault();
        var key = $(this).data('license-key');
        var chip = $(this);
        var icon = chip.find('i.fa-copy, i.fa-key, i.far');
        var textEl = chip.find('.copy-pill-text');
        
        if (!key) return;

        copyTextToClipboard(key).then(function() {
            var origIconClass = icon.attr('class');
            var origText = textEl.text();

            icon.removeClass('fa-copy far fa-key').addClass('fa-check fas text-success');
            if (textEl.length) {
                textEl.text("{{ __('messages.copied') }}").addClass('text-success font-weight-bold');
            }
            
            setTimeout(function() {
                icon.attr('class', origIconClass);
                if (textEl.length) {
                    textEl.text(origText).removeClass('text-success font-weight-bold');
                }
            }, 1800);
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1600,
                    icon: 'success',
                    title: "{{ __('messages.copy_license_success') }}",
                    background: 'rgba(15, 23, 42, 0.95)',
                    color: '#f8fafc'
                });
            }
        }).catch(function(err) {
            console.error('Could not copy license key: ', err);
        });
    });

    // Horizontal Drag-to-Scroll & Auto-Align for Status Tabs
    (function() {
        var tabsEl = document.querySelector('.lic-status-tabs');
        if (!tabsEl) return;

        var isDown = false;
        var startX = 0;
        var scrollLeft = 0;
        var hasMoved = false;

        // Auto-scroll active tab into view on page load if needed
        var activeTab = tabsEl.querySelector('.lic-status-tab[class*="active-"]');
        if (activeTab) {
            var tabLeft = activeTab.offsetLeft;
            var tabWidth = activeTab.offsetWidth;
            var containerWidth = tabsEl.clientWidth;
            if (tabLeft + tabWidth > containerWidth || tabLeft < tabsEl.scrollLeft) {
                tabsEl.scrollLeft = Math.max(0, tabLeft - 16);
            }
        }

        tabsEl.addEventListener('mousedown', function(e) {
            if (e.button !== 0) return;
            isDown = true;
            hasMoved = false;
            startX = e.pageX - tabsEl.offsetLeft;
            scrollLeft = tabsEl.scrollLeft;
            tabsEl.style.cursor = 'grabbing';
            tabsEl.style.userSelect = 'none';
        });

        tabsEl.addEventListener('mouseleave', function() {
            if (!isDown) return;
            isDown = false;
            tabsEl.style.cursor = '';
            tabsEl.style.userSelect = '';
        });

        tabsEl.addEventListener('mouseup', function() {
            if (!isDown) return;
            isDown = false;
            tabsEl.style.cursor = '';
            tabsEl.style.userSelect = '';
        });

        tabsEl.addEventListener('mousemove', function(e) {
            if (!isDown) return;
            var x = e.pageX - tabsEl.offsetLeft;
            var walk = (x - startX);
            if (Math.abs(walk) > 5) {
                hasMoved = true;
                e.preventDefault();
                tabsEl.scrollLeft = scrollLeft - walk;
            }
        });

        // Prevent accidental link click during mouse drag
        tabsEl.addEventListener('click', function(e) {
            if (hasMoved) {
                e.preventDefault();
                e.stopPropagation();
                hasMoved = false;
            }
        }, true);
    })();
});
</script>
@endpush


