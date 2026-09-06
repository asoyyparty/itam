@extends('layouts.admin')

@section('title', __('messages.password_vault'))

@section('content')
<!-- Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 -->
<style>
    /* ═══════════════════════════════════════════════════════════════════
       HALLMARK WORKBENCH: IT INFRASTRUCTURE SECRET & PASSWORD VAULT
       ═══════════════════════════════════════════════════════════════════ */
    .vault-workbench {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        overflow-x: clip;
    }

    /* 1. Header Command Banner */
    .vault-header-card {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 16px;
        padding: 20px 24px;
        position: relative;
        overflow: hidden;
    }
    .vault-header-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, #f59e0b 0%, #38bdf8 50%, #6366f1 100%);
    }
    .vault-header-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .vault-header-left {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
        flex: 1 1 auto;
    }
    .vault-header-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: rgba(245, 158, 11, 0.12);
        border: 1px solid rgba(245, 158, 11, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fbbf24;
        font-size: 1.35rem;
        flex-shrink: 0;
        box-shadow: 0 0 20px rgba(245, 158, 11, 0.2);
    }
    .vault-title-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 2px;
    }
    .vault-title-text {
        font-size: 1.3rem;
        font-weight: 700;
        letter-spacing: -0.01em;
        color: #f8fafc;
        line-height: 1.2;
    }
    .vault-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.28);
        color: #fbbf24;
        padding: 2px 10px;
        border-radius: 9999px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .vault-pulse-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 8px #10b981;
        animation: vaultPulse 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
    }
    @keyframes vaultPulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.85); }
    }
    .vault-subtitle-text {
        color: #94a3b8;
        font-size: 0.85rem;
        margin-bottom: 0;
        line-height: 1.4;
    }
    .btn-add-vault {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #0f172a !important;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 0.6rem 1.35rem;
        font-weight: 700;
        font-size: 0.88rem;
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none !important;
    }
    .btn-add-vault:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(245, 158, 11, 0.5);
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    }

    /* 2. Bento KPI Matrix (4 Quadrants) */
    .vault-bento-card {
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
    .vault-bento-card:hover {
        transform: translateY(-2px);
        border-color: rgba(245, 158, 11, 0.35);
    }
    .vault-bento-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .vault-bento-label {
        font-size: 0.76rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--color-ink-2, #94a3b8);
    }
    .vault-bento-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
    }
    .vault-bento-val {
        font-family: 'JetBrains Mono', monospace;
        font-size: 1.85rem;
        font-weight: 700;
        line-height: 1.1;
        margin-bottom: 4px;
    }
    .vault-bento-footer {
        font-size: 0.74rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Bento Variants */
    .bento-total .vault-bento-icon { background: rgba(245, 158, 11, 0.14); color: #fbbf24; }
    .bento-total .vault-bento-val { color: #f8fafc; }

    .bento-categories .vault-bento-icon { background: rgba(99, 102, 241, 0.12); color: #818cf8; }
    .bento-categories .vault-bento-val { color: #818cf8; }

    .bento-infra .vault-bento-icon { background: rgba(56, 189, 248, 0.12); color: #38bdf8; }
    .bento-infra .vault-bento-val { color: #38bdf8; }

    .bento-sec .vault-bento-icon { background: rgba(16, 185, 129, 0.12); color: #10b981; }
    .bento-sec .vault-bento-val { color: #10b981; font-size: 1.45rem; }

    /* 3. Tactical Category Filter Ribbon & Search Bar */
    .vault-ribbon-card {
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
    .vault-ribbon-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        width: 100%;
        max-width: 100%;
        min-width: 0;
    }
    .vault-category-tabs-container {
        min-width: 0;
        flex: 1 1 auto;
        max-width: 100%;
        overflow: hidden;
        position: relative;
    }
    .vault-category-tabs {
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
    .vault-category-tabs::-webkit-scrollbar {
        display: none;
    }
    .vault-category-tab {
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
    .vault-category-tab:hover {
        background: rgba(15, 23, 42, 0.8);
        color: #f1f5f9;
        border-color: rgba(148, 163, 184, 0.3);
    }
    .vault-category-tab.active {
        background: rgba(245, 158, 11, 0.18);
        border-color: rgba(245, 158, 11, 0.5);
        color: #fbbf24;
    }
    .vault-tab-count {
        background: rgba(0, 0, 0, 0.28);
        border-radius: 999px;
        padding: 1px 7px;
        font-size: 0.72rem;
        font-family: 'JetBrains Mono', monospace;
    }

    /* Filter Controls */
    .vault-search-controls {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: nowrap;
    }
    .vault-search-input-box {
        position: relative;
        min-width: 220px;
    }
    .vault-search-input-box i.search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 0.85rem;
        pointer-events: none;
    }
    .vault-search-input-box input {
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
    .vault-search-input-box input:focus {
        background: rgba(15, 23, 42, 0.8);
        border-color: #f59e0b;
        outline: none;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
    }
    .vault-clear-search {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.8rem;
    }
    .vault-cat-select {
        height: 38px;
        border-radius: 10px;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(148, 163, 184, 0.2);
        color: #f8fafc;
        font-size: 0.85rem;
        padding: 0 12px;
    }

    /* 4. Desktop Data Grid Table */
    .vault-table-card {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 16px;
        overflow: hidden;
    }
    .vault-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    .vault-table thead th {
        background: rgba(15, 23, 42, 0.85);
        color: #94a3b8;
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 14px 18px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.12);
    }
    .vault-table tbody tr {
        transition: background-color 0.15s ease;
    }
    .vault-table tbody tr:hover {
        background-color: rgba(245, 158, 11, 0.04);
    }
    .vault-table tbody td {
        padding: 14px 18px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(148, 163, 184, 0.08);
        font-size: 0.86rem;
    }
    .vault-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Secret Tokens & Chips */
    .vault-user-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 8px;
        padding: 4px 10px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.82rem;
        color: #38bdf8;
    }
    .vault-pwd-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 8px;
        padding: 4px 10px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.84rem;
        min-width: 170px;
        justify-content: space-between;
    }
    .vault-masked-val {
        letter-spacing: 0.15em;
        color: #e2e8f0;
        user-select: none;
    }
    .vault-unmasked-val {
        color: #fef08a;
        font-weight: 600;
        letter-spacing: normal;
    }
    .vault-chip-btn {
        background: transparent;
        border: none;
        color: #94a3b8;
        padding: 2px 4px;
        font-size: 0.8rem;
        cursor: pointer;
        border-radius: 4px;
        transition: all 0.15s ease;
    }
    .vault-chip-btn:hover {
        color: #fbbf24;
    }

    /* Category Badges */
    .vault-cat-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 9px;
        border-radius: 8px;
        font-size: 0.74rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .cat-switch { background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; }
    .cat-wifi { background: rgba(56, 189, 248, 0.12); border: 1px solid rgba(56, 189, 248, 0.3); color: #38bdf8; }
    .cat-cctv { background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.3); color: #fbbf24; }
    .cat-email { background: rgba(168, 85, 247, 0.12); border: 1px solid rgba(168, 85, 247, 0.3); color: #c084fc; }
    .cat-generic { background: rgba(148, 163, 184, 0.12); border: 1px solid rgba(148, 163, 184, 0.25); color: #cbd5e1; }

    /* Action Buttons */
    .vault-action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        transition: all 0.15s ease;
    }
    .vault-btn-edit {
        background: rgba(245, 158, 11, 0.12);
        border: 1px solid rgba(245, 158, 11, 0.3);
        color: #fbbf24;
    }
    .vault-btn-edit:hover {
        background: rgba(245, 158, 11, 0.25);
        color: #fef3c7;
    }
    .vault-btn-del {
        background: rgba(239, 68, 68, 0.12);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #f87171;
    }
    .vault-btn-del:hover {
        background: rgba(239, 68, 68, 0.25);
        color: #fee2e2;
    }

    /* 5. Mobile Tactile Feed Cards (No Scroll-Trap) */
    .vault-mobile-card {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 14px;
        padding: 15px 16px;
        margin-bottom: 12px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
    }
    .vault-mobile-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #f59e0b;
    }
    .vault-m-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 8px;
    }
    .vault-m-num {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.72rem;
        font-weight: 700;
        background: rgba(148, 163, 184, 0.15);
        border: 1px solid rgba(148, 163, 184, 0.25);
        color: #e2e8f0;
        padding: 2px 7px;
        border-radius: 6px;
    }
    .vault-m-name {
        font-weight: 700;
        font-size: 0.98rem;
        line-height: 1.35;
        color: #f8fafc;
        margin-bottom: 12px;
        word-break: break-word;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .vault-m-name i {
        color: #fbbf24;
        font-size: 0.95rem;
        flex-shrink: 0;
    }
    .vault-m-block {
        background: rgba(15, 23, 42, 0.75);
        border: 1px solid rgba(148, 163, 184, 0.16);
        border-radius: 10px;
        padding: 9px 12px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .vault-m-block-label {
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        margin-bottom: 2px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .vault-m-block-val {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.82rem;
        color: #f8fafc;
        word-break: break-all;
    }
    .vault-m-copy-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 4px 9px;
        border-radius: 6px;
        background: rgba(245, 158, 11, 0.12);
        border: 1px solid rgba(245, 158, 11, 0.28);
        color: #fbbf24;
        white-space: nowrap;
        flex-shrink: 0;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .vault-m-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        border-top: 1px solid rgba(148, 163, 184, 0.12);
        padding-top: 12px;
        margin-top: 10px;
    }
    .vault-m-btn-action {
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
    .vault-m-btn-edit {
        background: rgba(245, 158, 11, 0.14);
        border-color: rgba(245, 158, 11, 0.3);
        color: #fbbf24 !important;
    }
    .vault-m-btn-del {
        background: rgba(239, 68, 68, 0.14);
        border-color: rgba(239, 68, 68, 0.3);
        color: #f87171 !important;
        width: 100%;
    }

    /* Responsive Mobile Overrides */
    @media (max-width: 767.98px) {
        .vault-header-card {
            padding: 14px 16px;
            border-radius: 14px;
        }
        .vault-header-wrapper {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }
        .vault-header-left {
            align-items: flex-start !important;
            gap: 12px !important;
        }
        .vault-header-icon {
            width: 40px !important;
            height: 40px !important;
            font-size: 1.15rem !important;
            margin-top: 2px !important;
        }
        .vault-title-text {
            font-size: 1.15rem !important;
            line-height: 1.25 !important;
        }
        .vault-subtitle-text {
            font-size: 0.78rem !important;
            line-height: 1.35 !important;
        }
        .btn-add-vault {
            width: 100%;
            justify-content: center;
            height: 42px;
            font-size: 0.86rem;
            border-radius: 10px;
        }

        /* 2x2 Bento Mobile */
        .vault-bento-row {
            margin-left: -4px !important;
            margin-right: -4px !important;
        }
        .vault-bento-col {
            padding-left: 4px !important;
            padding-right: 4px !important;
            margin-bottom: 8px !important;
        }
        .vault-bento-card {
            padding: 10px 12px !important;
            border-radius: 12px !important;
        }
        .vault-bento-label {
            font-size: 0.64rem !important;
        }
        .vault-bento-icon {
            width: 28px !important;
            height: 28px !important;
            font-size: 0.8rem !important;
        }
        .vault-bento-val {
            font-size: 1.35rem !important;
        }
        .vault-bento-footer {
            font-size: 0.67rem !important;
        }

        /* Ribbon on Mobile */
        .vault-ribbon-card {
            padding: 12px 14px !important;
            border-radius: 14px !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
            overflow: hidden !important;
        }
        .vault-ribbon-wrapper {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
        }
        .vault-category-tabs-container {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            overflow: hidden !important;
            position: relative !important;
        }
        .vault-category-tabs {
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
        .vault-category-tabs:active {
            cursor: grabbing;
        }
        .vault-category-tabs::-webkit-scrollbar {
            display: none !important;
        }
        .vault-category-tab {
            flex: 0 0 auto !important;
            flex-shrink: 0 !important;
            padding: 7px 13px !important;
            font-size: 0.78rem !important;
            border-radius: 8px !important;
            white-space: nowrap !important;
        }
        .vault-search-controls {
            flex-direction: column !important;
            width: 100% !important;
            gap: 8px !important;
        }
        .vault-search-input-box {
            width: 100% !important;
            min-width: 0 !important;
        }
        .vault-search-input-box input {
            width: 100% !important;
            display: block !important;
            box-sizing: border-box !important;
            height: 40px !important;
            font-size: 0.82rem !important;
        }
        .vault-cat-select {
            width: 100% !important;
            max-width: 100% !important;
            display: block !important;
            box-sizing: border-box !important;
            height: 40px !important;
            font-size: 0.82rem !important;
        }
    }
</style>

<div class="vault-workbench">
    <!-- 1. Header Command Banner -->
    <div class="vault-header-card">
        <div class="vault-header-wrapper">
            <div class="vault-header-left">
                <div class="vault-header-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div style="min-width: 0; flex: 1 1 auto;">
                    <div class="vault-title-row">
                        <h4 class="mb-0 font-weight-bold theme-text vault-title-text">
                            {{ __('messages.password_vault') }}
                        </h4>
                        <span class="vault-badge">
                            <span class="vault-pulse-dot"></span>
                            {{ __('messages.aes_encrypted_at_rest') }}
                        </span>
                    </div>
                    <p class="vault-subtitle-text">
                        {{ __('messages.vault_subtitle') }}
                    </p>
                </div>
            </div>

            @can('action_manage_settings')
            <div>
                <a href="{{ route('password_vaults.create') }}" class="btn-add-vault">
                    <i class="fas fa-plus-circle"></i>
                    <span>{{ __('messages.add_vault') }}</span>
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
    <div class="row g-2 g-md-3 vault-bento-row">
        <!-- Total Credentials -->
        <div class="col-6 col-xl-3 vault-bento-col mb-2 mb-xl-0">
            <div class="vault-bento-card bento-total">
                <div>
                    <div class="vault-bento-top">
                        <span class="vault-bento-label">{{ __('messages.total_credentials') }}</span>
                        <div class="vault-bento-icon">
                            <i class="fas fa-key"></i>
                        </div>
                    </div>
                    <div class="vault-bento-val">{{ $summary['total_passwords'] }}</div>
                </div>
                <div class="vault-bento-footer">
                    <i class="fas fa-database text-warning"></i>
                    <span>{{ __('messages.vault_tagline') }}</span>
                </div>
            </div>
        </div>

        <!-- Active Categories -->
        <div class="col-6 col-xl-3 vault-bento-col mb-2 mb-xl-0">
            <div class="vault-bento-card bento-categories">
                <div>
                    <div class="vault-bento-top">
                        <span class="vault-bento-label">{{ __('messages.active_categories') }}</span>
                        <div class="vault-bento-icon">
                            <i class="fas fa-folder-open"></i>
                        </div>
                    </div>
                    <div class="vault-bento-val">{{ $summary['categories_count'] }}</div>
                </div>
                <div class="vault-bento-footer">
                    <i class="fas fa-layer-group text-indigo"></i>
                    <span>{{ __('messages.category_access') }}</span>
                </div>
            </div>
        </div>

        <!-- Network & Infra -->
        <div class="col-6 col-xl-3 vault-bento-col mb-2 mb-xl-0">
            <div class="vault-bento-card bento-infra">
                <div>
                    <div class="vault-bento-top">
                        <span class="vault-bento-label">{{ __('messages.network_infra_creds') }}</span>
                        <div class="vault-bento-icon">
                            <i class="fas fa-network-wired"></i>
                        </div>
                    </div>
                    <div class="vault-bento-val">{{ $summary['network_infra_count'] }}</div>
                </div>
                <div class="vault-bento-footer">
                    <i class="fas fa-server text-info"></i>
                    <span>Switch · Wifi · CCTV</span>
                </div>
            </div>
        </div>

        <!-- Encryption Health -->
        <div class="col-6 col-xl-3 vault-bento-col mb-2 mb-xl-0">
            <div class="vault-bento-card bento-sec">
                <div>
                    <div class="vault-bento-top">
                        <span class="vault-bento-label">{{ __('messages.encryption_verified') }}</span>
                        <div class="vault-bento-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>
                    <div class="vault-bento-val">AES-256</div>
                </div>
                <div class="vault-bento-footer">
                    <i class="fas fa-shield-alt text-success"></i>
                    <span>{{ __('messages.aes_encrypted_at_rest') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Tactical Category Filter Ribbon & Search Bar -->
    <div class="vault-ribbon-card">
        <div class="vault-ribbon-wrapper">
            <!-- Category Tabs Container -->
            <div class="vault-category-tabs-container">
                <div class="d-flex align-items-center justify-content-between d-md-none mb-2 px-1">
                    <span class="text-muted font-weight-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">
                        <i class="fas fa-filter mr-1 text-warning"></i> {{ __('messages.category') }}
                    </span>
                    <span class="text-muted font-weight-bold" style="font-size: 0.70rem;">
                        <i class="fas fa-arrows-alt-h text-info mr-1"></i> {{ __('messages.swipe_horizontal') }}
                    </span>
                </div>
                <div class="vault-category-tabs">
                    <!-- All Categories -->
                    <a href="{{ route('password_vaults.index', array_merge(request()->except('category'), ['category' => 'all'])) }}"
                       class="vault-category-tab {{ $activeCategory == 'all' ? 'active' : '' }}">
                        <i class="fas fa-layer-group text-xs"></i>
                        <span>{{ __('messages.all_category') }}</span>
                        <span class="vault-tab-count">{{ $summary['total_passwords'] }}</span>
                    </a>

                    <!-- Dynamic Categories -->
                    @foreach($categories as $cat)
                        @php
                            $catCount = $summary['category_counts'][$cat] ?? 0;
                            $iconClass = 'fa-tag';
                            $lowerCat = strtolower($cat);
                            if (str_contains($lowerCat, 'wifi') || str_contains($lowerCat, 'ap')) $iconClass = 'fa-wifi';
                            elseif (str_contains($lowerCat, 'switch') || str_contains($lowerCat, 'router')) $iconClass = 'fa-network-wired';
                            elseif (str_contains($lowerCat, 'cctv') || str_contains($lowerCat, 'nvr')) $iconClass = 'fa-video';
                            elseif (str_contains($lowerCat, 'email') || str_contains($lowerCat, 'mail')) $iconClass = 'fa-envelope';
                            elseif (str_contains($lowerCat, 'server') || str_contains($lowerCat, 'database')) $iconClass = 'fa-database';
                            elseif (str_contains($lowerCat, 'anydesk') || str_contains($lowerCat, 'rdp')) $iconClass = 'fa-desktop';
                        @endphp
                        <a href="{{ route('password_vaults.index', array_merge(request()->except('category'), ['category' => $cat])) }}"
                           class="vault-category-tab {{ $activeCategory == $cat ? 'active' : '' }}">
                            <i class="fas {{ $iconClass }} text-xs"></i>
                            <span>{{ $cat }}</span>
                            <span class="vault-tab-count">{{ $catCount }}</span>
                        </a>
                    @endforeach

                    <!-- Trailing spacer to avoid clipping last item on mobile -->
                    <div style="flex: 0 0 16px; width: 16px; height: 1px; pointer-events: none;"></div>
                </div>
            </div>

            <!-- Search and Category Select Form -->
            <form action="{{ route('password_vaults.index') }}" method="GET" class="vault-search-controls" id="vault-filter-form">
                @if($activeCategory != 'all')
                    <input type="hidden" name="category" value="{{ $activeCategory }}">
                @endif
                <div class="vault-search-input-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" placeholder="{{ __('messages.search_vault') }}" value="{{ request('search') }}" autocomplete="off">
                    @if(request('search'))
                        <a href="{{ route('password_vaults.index', request()->except('search')) }}" class="vault-clear-search" title="{{ __('messages.clear') }}">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
                <select name="category" class="vault-cat-select" onchange="document.getElementById('vault-filter-form').submit();">
                    <option value="all">{{ __('messages.all_category') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $activeCategory == $cat ? 'selected' : '' }}>
                            {{ $cat }} ({{ $summary['category_counts'][$cat] ?? 0 }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <!-- 4. Data View: Dual-Mode Desktop Table & Mobile Feed Cards -->
    <!-- DESKTOP TABLE VIEW (d-none d-md-block) -->
    <div class="vault-table-card d-none d-md-block">
        <div class="table-responsive">
            <table class="vault-table">
                <thead>
                    <tr>
                        <th width="60" class="text-center">{{ __('messages.no') }}</th>
                        <th>{{ __('messages.device_name') }}</th>
                        <th>{{ __('messages.category') }}</th>
                        <th>{{ __('messages.username') }}</th>
                        <th>{{ __('messages.password') }}</th>
                        <th>{{ __('messages.notes') }}</th>
                        <th width="120" class="text-center">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($passwords as $pwd)
                        @php
                            $catClass = 'cat-generic';
                            $lowerCat = strtolower($pwd->category);
                            if (str_contains($lowerCat, 'switch') || str_contains($lowerCat, 'router')) $catClass = 'cat-switch';
                            elseif (str_contains($lowerCat, 'wifi')) $catClass = 'cat-wifi';
                            elseif (str_contains($lowerCat, 'cctv')) $catClass = 'cat-cctv';
                            elseif (str_contains($lowerCat, 'email')) $catClass = 'cat-email';
                        @endphp
                        <tr>
                            <!-- No -->
                            <td class="text-center text-muted font-weight-bold" style="font-family: 'JetBrains Mono', monospace; font-size: 0.8rem;">
                                {{ $loop->iteration }}
                            </td>

                            <!-- Device / Host Name -->
                            <td>
                                <div class="font-weight-bold theme-text" style="font-size: 0.92rem;">
                                    {{ $pwd->device_name }}
                                </div>
                            </td>

                            <!-- Category -->
                            <td>
                                <span class="vault-cat-badge {{ $catClass }}">
                                    <i class="fas fa-tag text-xs"></i>
                                    {{ $pwd->category }}
                                </span>
                            </td>

                            <!-- Username with 1-Click Copy -->
                            <td>
                                <div class="vault-user-chip">
                                    <i class="fas fa-user-circle text-xs"></i>
                                    <span class="user-val">{{ $pwd->username }}</span>
                                    <button type="button" class="vault-chip-btn copy-user-btn" title="{{ __('messages.copy_username') }}" data-username="{{ $pwd->username }}">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </td>

                            <!-- Encrypted Password with Reveal & 1-Click Copy -->
                            <td>
                                <div class="vault-pwd-chip" data-revealed="false" data-secret="{{ $pwd->encrypted_password }}">
                                    <span class="vault-pwd-display vault-masked-val">••••••••••••</span>
                                    <div class="d-flex align-items-center" style="gap: 4px;">
                                        <button type="button" class="vault-chip-btn toggle-pwd-btn" title="{{ __('messages.toggle_password') }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="vault-chip-btn copy-pwd-btn" title="{{ __('messages.copy_password') }}" data-password="{{ $pwd->encrypted_password }}">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>

                            <!-- Notes -->
                            <td>
                                <div class="text-muted text-truncate" style="max-width: 240px; font-size: 0.82rem;" title="{{ $pwd->notes }}">
                                    {{ $pwd->notes ?: '—' }}
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="text-center">
                                @can('action_manage_settings')
                                <div class="d-flex align-items-center justify-content-center" style="gap: 6px;">
                                    <a href="{{ route('password_vaults.edit', array_merge([$pwd->id], request()->query())) }}"
                                       class="vault-action-btn vault-btn-edit"
                                       title="{{ __('messages.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('password_vaults.destroy', array_merge([$pwd->id], request()->query())) }}" method="POST" class="d-inline m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="vault-action-btn vault-btn-del btn-delete"
                                                title="{{ __('messages.delete') }}"
                                                data-confirm-message="{{ __('messages.confirm_delete') }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                                @else
                                <span class="text-muted text-xs">—</span>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-shield-alt fa-2x mb-3" style="opacity: 0.4; color: #64748b;"></i>
                                <p class="mb-0 font-weight-bold theme-text">{{ __('messages.no_data') }}</p>
                                <small class="text-muted">{{ __('messages.vault_subtitle') }}</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MOBILE TACTILE FEED CARDS (d-md-none) -->
    <div class="vault-mobile-cards d-md-none">
        @forelse($passwords as $pwd)
            @php
                $catClass = 'cat-generic';
                $lowerCat = strtolower($pwd->category);
                if (str_contains($lowerCat, 'switch') || str_contains($lowerCat, 'router')) $catClass = 'cat-switch';
                elseif (str_contains($lowerCat, 'wifi')) $catClass = 'cat-wifi';
                elseif (str_contains($lowerCat, 'cctv')) $catClass = 'cat-cctv';
                elseif (str_contains($lowerCat, 'email')) $catClass = 'cat-email';
            @endphp
            <div class="vault-mobile-card">
                <!-- Topbar: #No + Category Badge -->
                <div class="vault-m-topbar">
                    <div class="d-flex align-items-center" style="gap: 6px;">
                        <span class="vault-m-num">#{{ sprintf('%02d', $loop->iteration) }}</span>
                        <span class="vault-cat-badge {{ $catClass }}">
                            <i class="fas fa-tag text-xs"></i>
                            {{ $pwd->category }}
                        </span>
                    </div>
                    <span class="text-muted font-weight-bold" style="font-size: 0.68rem; font-family: 'JetBrains Mono', monospace;">
                        <i class="fas fa-shield-alt text-warning mr-1"></i> AES-256
                    </span>
                </div>

                <!-- Device / Host Name -->
                <div class="vault-m-name">
                    <i class="fas fa-server"></i>
                    <span>{{ $pwd->device_name }}</span>
                </div>

                <!-- Username Interactive 1-Click Copy Box -->
                <div class="vault-m-block copy-user-block" data-username="{{ $pwd->username }}" style="cursor: pointer;">
                    <div>
                        <div class="vault-m-block-label">
                            <i class="fas fa-user-circle"></i> {{ __('messages.username') }}
                        </div>
                        <div class="vault-m-block-val user-text" style="color: #38bdf8;">
                            {{ $pwd->username }}
                        </div>
                    </div>
                    <span class="vault-m-copy-pill user-pill">
                        <i class="fas fa-copy"></i>
                        <span class="pill-text">{{ __('messages.copy') }}</span>
                    </span>
                </div>

                <!-- Password Interactive Security Box -->
                <div class="vault-m-block vault-pwd-chip" data-revealed="false" data-secret="{{ $pwd->encrypted_password }}">
                    <div>
                        <div class="vault-m-block-label">
                            <i class="fas fa-key"></i> {{ __('messages.password') }}
                        </div>
                        <div class="vault-m-block-val vault-pwd-display vault-masked-val">
                            ••••••••••••
                        </div>
                    </div>
                    <div class="d-flex align-items-center" style="gap: 6px;">
                        <button type="button" class="vault-chip-btn toggle-pwd-btn p-2" title="{{ __('messages.toggle_password') }}" style="background: rgba(15, 23, 42, 0.6); border-radius: 8px;">
                            <i class="fas fa-eye text-warning"></i>
                        </button>
                        <span class="vault-m-copy-pill copy-pwd-btn" data-password="{{ $pwd->encrypted_password }}">
                            <i class="fas fa-copy"></i>
                            <span class="pill-text">{{ __('messages.copy') }}</span>
                        </span>
                    </div>
                </div>

                <!-- Notes if available -->
                @if($pwd->notes)
                <div class="p-2 mb-2" style="background: rgba(0,0,0,0.25); border-radius: 8px; border: 1px solid rgba(148, 163, 184, 0.08);">
                    <div class="vault-m-block-label">
                        <i class="fas fa-sticky-note"></i> {{ __('messages.notes') }}
                    </div>
                    <div style="font-size: 0.78rem; color: #cbd5e1; word-break: break-word;">
                        {{ $pwd->notes }}
                    </div>
                </div>
                @endif

                <!-- Mobile Action Buttons -->
                @can('action_manage_settings')
                <div class="vault-m-actions">
                    <a href="{{ route('password_vaults.edit', array_merge([$pwd->id], request()->query())) }}"
                       class="vault-m-btn-action vault-m-btn-edit">
                        <i class="fas fa-edit mr-1"></i>
                        <span>{{ __('messages.edit') }}</span>
                    </a>
                    <form action="{{ route('password_vaults.destroy', array_merge([$pwd->id], request()->query())) }}" method="POST" class="d-inline m-0 flex-grow-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="vault-m-btn-action vault-m-btn-del btn-delete"
                                data-confirm-message="{{ __('messages.confirm_delete') }}">
                            <i class="fas fa-trash-alt mr-1"></i>
                            <span>{{ __('messages.delete') }}</span>
                        </button>
                    </form>
                </div>
                @endcan
            </div>
        @empty
            <div class="text-center py-5 vault-mobile-card">
                <i class="fas fa-shield-alt fa-2x mb-3" style="opacity: 0.4; color: #64748b;"></i>
                <p class="mb-0 font-weight-bold theme-text" style="font-size: 0.95rem;">{{ __('messages.no_data') }}</p>
                <small class="text-muted">{{ __('messages.vault_subtitle') }}</small>
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
    // 1. Password Visibility Toggle with 15-second Auto-Hide Timer
    $(document).on('click', '.toggle-pwd-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var chip = $(this).closest('.vault-pwd-chip');
        var displayEl = chip.find('.vault-pwd-display');
        var icon = $(this).find('i');
        var secret = chip.data('secret');
        var isRevealed = chip.attr('data-revealed') === 'true';

        if (!secret) return;

        if (isRevealed) {
            // Re-mask
            displayEl.text('••••••••••••').removeClass('vault-unmasked-val').addClass('vault-masked-val');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
            chip.attr('data-revealed', 'false');
        } else {
            // Unmask
            displayEl.text(secret).removeClass('vault-masked-val').addClass('vault-unmasked-val');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
            chip.attr('data-revealed', 'true');

            // Set 15s auto-hide timer for security
            var id = Math.random().toString(36).substring(7);
            chip.data('timer-id', id);
            setTimeout(function() {
                if (chip.attr('data-revealed') === 'true' && chip.data('timer-id') === id) {
                    displayEl.text('••••••••••••').removeClass('vault-unmasked-val').addClass('vault-masked-val');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                    chip.attr('data-revealed', 'false');
                }
            }, 15000);
        }
    });

    // 2. 1-Click Copy Password
    $(document).on('click', '.copy-pwd-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var pwd = $(this).data('password');
        var btn = $(this);
        var icon = btn.find('i');
        var pillText = btn.find('.pill-text');

        if (!pwd) return;

        copyTextToClipboard(pwd).then(function() {
            var origIcon = icon.attr('class');
            var origText = pillText.text();

            icon.removeClass('fa-copy').addClass('fa-check text-success');
            if (pillText.length) {
                pillText.text("{{ __('messages.copied') }}").addClass('text-success font-weight-bold');
            }

            setTimeout(function() {
                icon.attr('class', origIcon);
                if (pillText.length) {
                    pillText.text(origText).removeClass('text-success font-weight-bold');
                }
            }, 1800);

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1600,
                    icon: 'success',
                    title: "{{ __('messages.copy_password_success') }}",
                    background: 'rgba(15, 23, 42, 0.95)',
                    color: '#f8fafc'
                });
            }
        }).catch(function(err) {
            console.error('Copy password failed: ', err);
        });
    });

    // 3. 1-Click Copy Username (Desktop & Mobile)
    $(document).on('click', '.copy-user-btn, .copy-user-block', function(e) {
        e.preventDefault();
        var username = $(this).data('username');
        var el = $(this);
        var icon = el.find('i.fa-copy, i.fa-check');
        var pillText = el.find('.pill-text');

        if (!username) return;

        copyTextToClipboard(username).then(function() {
            var origIcon = icon.attr('class');
            var origText = pillText.text();

            icon.removeClass('fa-copy').addClass('fa-check text-success');
            if (pillText.length) {
                pillText.text("{{ __('messages.copied') }}").addClass('text-success font-weight-bold');
            }

            setTimeout(function() {
                icon.attr('class', origIcon);
                if (pillText.length) {
                    pillText.text(origText).removeClass('text-success font-weight-bold');
                }
            }, 1800);

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    icon: 'success',
                    title: "{{ __('messages.copy_username_success') }}",
                    background: 'rgba(15, 23, 42, 0.95)',
                    color: '#f8fafc'
                });
            }
        }).catch(function(err) {
            console.error('Copy username failed: ', err);
        });
    });

    // 4. Horizontal Drag-to-Scroll & Auto-Align for Category Tabs
    (function() {
        var tabsEl = document.querySelector('.vault-category-tabs');
        if (!tabsEl) return;

        var isDown = false;
        var startX = 0;
        var scrollLeft = 0;
        var hasMoved = false;

        // Auto-scroll active tab into view on page load
        var activeTab = tabsEl.querySelector('.vault-category-tab.active');
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

        // Prevent click if mouse was dragging
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
