@extends('layouts.admin')

@section('title', __('messages.manage') . ' ' . __('messages.it_assets'))

@section('content')
<style>
    /* Hallmark · component: filter-bar · genre: modern-minimal · pre-emit critique: P5 H5 E5 S5 R5 V5 */
    /* ── Hallmark Master Header Banner ─────────────────────────────────── */
    .master-header-banner {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.85), rgba(30, 41, 59, 0.75));
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 20px;
        padding: 16px 22px;
        margin-top: 0 !important;
        margin-bottom: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }
    body.light-mode .master-header-banner {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(241, 245, 249, 0.95));
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
    }

    /* â”€â”€ Hallmark Quick Status Ribbon â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .hallmark-status-ribbon {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        padding: 2px 2px 8px 2px;
        margin-bottom: 16px;
        scrollbar-width: thin;
    }
    .hallmark-status-ribbon::-webkit-scrollbar {
        height: 4px;
    }
    .hallmark-status-ribbon::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.2);
        border-radius: 4px;
    }
    .status-ribbon-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--color-ink-1);
        background: rgba(15, 23, 42, 0.35);
        border: 1px solid rgba(148, 163, 184, 0.18);
        text-decoration: none !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        white-space: nowrap;
        user-select: none;
    }
    body.light-mode .status-ribbon-pill {
        background: #f8fafc;
        border-color: #e2e8f0;
        color: var(--color-ink-1);
    }
    .status-ribbon-pill:hover {
        transform: translateY(-1px);
        border-color: rgba(99, 102, 241, 0.5);
        color: var(--color-ink-0);
        box-shadow: 0 3px 12px rgba(15, 23, 42, 0.1);
    }
    .status-ribbon-pill.active {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.22), rgba(79, 70, 229, 0.14));
        border-color: #6366f1;
        color: #ffffff;
        box-shadow: 0 0 14px rgba(99, 102, 241, 0.25);
    }
    body.light-mode .status-ribbon-pill.active {
        background: #4f46e5;
        color: #ffffff;
        border-color: #4338ca;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
    }
    .status-ribbon-count {
        font-family: var(--font-mono);
        font-size: 0.74rem;
        padding: 2px 7px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        font-weight: 700;
    }
    body.light-mode .status-ribbon-pill:not(.active) .status-ribbon-count {
        background: #e2e8f0;
        color: #1e293b;
    }
    body.light-mode .status-ribbon-pill.active .status-ribbon-count {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    /* â”€â”€ Hallmark Master Table Card â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .master-table-card {
        border-radius: 20px !important;
        overflow: hidden !important;
        border: 1px solid rgba(148, 163, 184, 0.14) !important;
        background: rgba(15, 23, 42, 0.75) !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    }
    body.light-mode .master-table-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
    }

    .master-table-card .card-header {
        background: transparent !important;
        border-bottom: 1px solid rgba(148, 163, 184, 0.1) !important;
        padding: 16px 20px !important;
    }
    body.light-mode .master-table-card .card-header {
        border-bottom: 1px solid #f1f5f9 !important;
    }

    /* â”€â”€ Hallmark Filters & Search â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .custom-filter-select {
        height: 42px !important;
        border-radius: 12px !important;
        border: 1px solid rgba(148, 163, 184, 0.2) !important;
        background-color: rgba(15, 23, 42, 0.35) !important;
        color: var(--color-ink-0) !important;
        font-size: 0.84rem !important;
        padding: 0 32px 0 12px !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        background-repeat: no-repeat !important;
        background-position: right 12px center !important;
        background-size: 14px 14px !important;
        transition: all 0.2s ease !important;
        flex: 1 1 130px;
        min-width: 120px;
        max-width: 170px;
    }
    body.light-mode .custom-filter-select {
        background-color: #ffffff !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }
    .custom-filter-select:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
        outline: none;
    }

    .asset-filter-container {
        display: flex !important;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px !important;
        width: 100%;
    }
    @media (max-width: 767.98px) {
        .master-table-card .card-header {
            padding: 12px 14px !important;
        }
        .asset-filter-container {
            display: flex !important;
            flex-direction: column !important;
            width: 100% !important;
            gap: 8px !important;
            align-items: stretch !important;
        }
        .asset-filter-container > * {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            margin-bottom: 0 !important;
            flex: 0 0 auto !important;
        }
        .custom-filter-select {
            width: 100% !important;
            max-width: 100% !important;
            height: 40px !important;
            flex: 0 0 auto !important;
            margin: 0 !important;
        }
        .asset-filter-container .sleek-search-wrapper {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 auto !important;
            margin: 0 !important;
        }
    }
    @media (min-width: 768px) {
        .asset-filter-container {
            flex-direction: row !important;
            gap: 12px !important;
        }
        .asset-filter-container > * {
            margin-bottom: 0 !important;
        }
    }

    .sleek-search-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        min-width: 220px;
        flex: 2 1 240px;
    }
    .sleek-search-wrapper .search-icon {
        position: absolute;
        left: 14px;
        color: var(--color-accent);
        font-size: 0.88rem;
        pointer-events: none;
        z-index: 2;
    }
    .sleek-search-input {
        height: 42px !important;
        padding-left: 38px !important;
        padding-right: 36px !important;
        border-radius: 12px !important;
        border: 1px solid rgba(148, 163, 184, 0.2) !important;
        background: rgba(15, 23, 42, 0.35) !important;
        color: var(--color-ink-0) !important;
        font-size: 0.86rem !important;
        width: 100%;
        transition: all 0.2s ease;
    }
    body.light-mode .sleek-search-input {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }
    .sleek-search-input:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
        outline: none;
    }
    .clear-search-btn {
        position: absolute;
        right: 12px;
        color: var(--color-ink-2);
        font-size: 0.82rem;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none !important;
        transition: color 0.15s ease;
    }
    .clear-search-btn:hover {
        color: var(--color-danger, #ef4444);
    }

    .table-sort-link {
        color: inherit !important;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        transition: color 0.15s ease;
        user-select: none;
        cursor: pointer;
    }
    .table-sort-link:hover {
        color: #6366f1 !important;
    }
    body.light-mode .table-sort-link:hover {
        color: #4f46e5 !important;
    }
    .table-sort-link.active {
        color: #38bdf8 !important;
        font-weight: 700;
    }
    body.light-mode .table-sort-link.active {
        color: #0284c7 !important;
    }
    .table-sort-link .sort-neutral {
        opacity: 0.35;
        font-size: 0.68rem;
    }
    .table-sort-link:hover .sort-neutral {
        opacity: 0.8;
    }

    /* â”€â”€ High-Density Data Table â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .asset-tag-pill {
        font-family: var(--font-mono);
        font-size: 0.82rem;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
        background: rgba(56, 189, 248, 0.08);
        border: 1px solid rgba(56, 189, 248, 0.22);
        color: #38bdf8;
        display: inline-block;
        font-variant-numeric: tabular-nums;
        text-decoration: none !important;
        transition: all 0.18s ease;
    }
    .asset-tag-pill:hover {
        background: rgba(56, 189, 248, 0.18);
        border-color: #38bdf8;
        color: #7dd3fc;
        transform: translateY(-1px);
    }
    body.light-mode .asset-tag-pill {
        background: rgba(14, 165, 233, 0.1);
        border-color: rgba(14, 165, 233, 0.3);
        color: #0284c7;
    }

    .asset-thumb {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid rgba(148, 163, 184, 0.25);
        flex-shrink: 0;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .asset-thumb:hover {
        transform: scale(1.15);
        z-index: 2;
    }
    .asset-thumb-placeholder {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: rgba(99, 102, 241, 0.1);
        border: 1px solid rgba(99, 102, 241, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #818cf8;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    /* â”€â”€ Status Badges (Hallmark Discipline) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .hallmark-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.76rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        white-space: nowrap;
        border: 1px solid transparent;
        transition: all 0.15s ease;
    }
    .badge-status-available {
        background: rgba(16, 185, 129, 0.12);
        color: #34d399;
        border-color: rgba(16, 185, 129, 0.25);
    }
    .badge-status-assigned {
        background: rgba(99, 102, 241, 0.12);
        color: #818cf8;
        border-color: rgba(99, 102, 241, 0.25);
    }
    .badge-status-maintenance {
        background: rgba(245, 158, 11, 0.12);
        color: #fbbf24;
        border-color: rgba(245, 158, 11, 0.25);
    }
    .badge-status-retired {
        background: rgba(148, 163, 184, 0.12);
        color: #94a3b8;
        border-color: rgba(148, 163, 184, 0.22);
    }
    .badge-status-missing {
        background: rgba(239, 68, 68, 0.12);
        color: #f87171;
        border-color: rgba(239, 68, 68, 0.25);
    }

    /* â”€â”€ Action Buttons â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .btn-action-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.82rem;
        border: 1px solid rgba(148, 163, 184, 0.2);
        background: rgba(15, 23, 42, 0.25);
        color: var(--color-ink-1);
        transition: all 0.18s ease;
        text-decoration: none !important;
    }
    body.light-mode .btn-action-icon {
        background: #f8fafc;
        border-color: #cbd5e1;
    }
    .btn-action-icon:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
    }
    .btn-action-icon.btn-edit:hover {
        border-color: #fbbf24;
        color: #fbbf24;
        background: rgba(245, 158, 11, 0.1);
    }
    .btn-action-icon.btn-delete:hover {
        border-color: #ef4444;
        color: #ef4444;
        background: rgba(239, 68, 68, 0.1);
    }

    /* â”€â”€ Import Modal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .modal-content {
        border-radius: 20px !important;
        overflow: hidden !important;
        background: rgba(15, 23, 42, 0.98) !important;
        border: 1px solid rgba(148, 163, 184, 0.18) !important;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5) !important;
    }
    body.light-mode .modal-content {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
    }
    .file-dropzone {
        border: 2px dashed rgba(99, 102, 241, 0.35);
        border-radius: 16px;
        padding: 30px 20px;
        text-align: center;
        background: rgba(15, 23, 42, 0.3);
        position: relative;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .file-dropzone:hover {
        border-color: #6366f1;
        background: rgba(99, 102, 241, 0.06);
    }
    .file-dropzone input[type="file"] {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        opacity: 0;
        cursor: pointer;
    }

    @media (max-width: 767.98px) {
        .asset-action-buttons {
            width: 100% !important;
            justify-content: flex-start !important;
            margin-top: 10px !important;
            gap: 8px !important;
        }
        .asset-action-buttons .btn {
            flex: 1 1 auto;
            text-align: center;
            justify-content: center;
        }
        .itam-ms-wrap {
            max-width: 100% !important;
            flex: 0 0 auto !important;
            width: 100% !important;
            height: auto !important;
            margin: 0 !important;
        }
        .custom-filter-select {
            max-width: 100% !important;
            flex: 0 0 auto !important;
            width: 100% !important;
            height: 40px !important;
            margin: 0 !important;
        }
        .sleek-search-wrapper {
            width: 100% !important;
            flex: 0 0 auto !important;
            margin: 0 !important;
        }
        .sleek-search-input {
            height: 40px !important;
        }
    }

    .status-btn::after {
        display: none !important;
    }

    /* ── ITAM Custom Multi-Select ─────────────────────────────────────── */
    .itam-ms-wrap {
        position: relative;
        flex: 1 1 130px;
        min-width: 120px;
        max-width: 180px;
        user-select: none;
    }
    .itam-ms-trigger {
        width: 100%;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        padding: 0 10px 0 12px;
        border-radius: 12px;
        border: 1px solid rgba(148, 163, 184, 0.2);
        background: rgba(15, 23, 42, 0.35);
        color: var(--color-ink-0);
        font-size: 0.84rem;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
        overflow: hidden;
    }
    body.light-mode .itam-ms-trigger {
        background: #ffffff;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    .itam-ms-trigger:hover,
    .itam-ms-trigger:focus,
    .itam-ms-wrap.open .itam-ms-trigger {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        outline: none;
    }
    .itam-ms-wrap.has-value .itam-ms-trigger {
        border-color: rgba(99, 102, 241, 0.55);
        background: rgba(99, 102, 241, 0.1);
    }
    body.light-mode .itam-ms-wrap.has-value .itam-ms-trigger {
        border-color: #4f46e5;
        background: rgba(79, 70, 229, 0.06);
    }
    .itam-ms-label {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: left;
        font-weight: 500;
    }
    .itam-ms-chevron {
        font-size: 0.7rem;
        flex-shrink: 0;
        transition: transform 0.2s ease;
        opacity: 0.6;
    }
    .itam-ms-wrap.open .itam-ms-chevron {
        transform: rotate(180deg);
        opacity: 1;
    }
    .itam-ms-count-badge {
        background: #6366f1;
        color: #fff;
        font-size: 0.68rem;
        font-weight: 700;
        border-radius: 999px;
        padding: 1px 6px;
        margin-left: 2px;
        flex-shrink: 0;
    }
    .itam-ms-panel {
        display: none;
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        z-index: 1050;
        min-width: 240px;
        max-width: 300px;
        background: rgba(15, 23, 42, 0.97);
        border: 1px solid rgba(99, 102, 241, 0.35);
        border-radius: 14px;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45);
        overflow: hidden;
        flex-direction: column;
    }
    body.light-mode .itam-ms-panel {
        background: #ffffff;
        border-color: #c7d2fe;
        box-shadow: 0 8px 30px rgba(15, 23, 42, 0.1);
    }
    .itam-ms-wrap.open .itam-ms-panel {
        display: flex;
    }
    .itam-ms-search-wrap {
        position: relative;
        padding: 10px 10px 6px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.12);
    }
    .itam-ms-search-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-40%);
        font-size: 0.76rem;
        color: rgba(148, 163, 184, 0.7);
    }
    .itam-ms-search {
        width: 100%;
        height: 32px;
        padding: 0 8px 0 28px;
        border-radius: 8px;
        border: 1px solid rgba(148, 163, 184, 0.2);
        background: rgba(255, 255, 255, 0.05);
        color: var(--color-ink-0);
        font-size: 0.8rem;
        outline: none;
    }
    body.light-mode .itam-ms-search {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    .itam-ms-search:focus {
        border-color: #6366f1;
    }
    .itam-ms-select-all-row {
        padding: 2px 4px 0;
        border-bottom: 1px solid rgba(148, 163, 184, 0.1);
    }
    .itam-ms-list {
        max-height: 220px;
        overflow-y: auto;
        padding: 4px;
        scrollbar-width: thin;
        scrollbar-color: rgba(99, 102, 241, 0.4) transparent;
    }
    .itam-ms-list::-webkit-scrollbar { width: 4px; }
    .itam-ms-list::-webkit-scrollbar-thumb { background: rgba(99, 102, 241, 0.4); border-radius: 4px; }
    .itam-ms-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.82rem;
        color: var(--color-ink-1);
        transition: background 0.15s ease;
        margin: 1px 0;
    }
    .itam-ms-item:hover {
        background: rgba(99, 102, 241, 0.1);
        color: var(--color-ink-0);
    }
    body.light-mode .itam-ms-item:hover {
        background: rgba(79, 70, 229, 0.08);
    }
    .itam-ms-item input[type="checkbox"] {
        width: 15px;
        height: 15px;
        border-radius: 4px;
        accent-color: #6366f1;
        flex-shrink: 0;
        cursor: pointer;
    }
    .itam-ms-item.itam-ms-selectall {
        font-weight: 600;
        color: var(--color-ink-0);
        border-bottom: none;
    }
    .itam-ms-item[style*="display: none"] { display: none !important; }
    .itam-ms-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 10px;
        border-top: 1px solid rgba(148, 163, 184, 0.1);
        gap: 8px;
    }
    .itam-ms-reset-btn {
        background: transparent;
        border: 1px solid rgba(148, 163, 184, 0.3);
        border-radius: 8px;
        color: var(--color-ink-2);
        font-size: 0.78rem;
        padding: 4px 12px;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .itam-ms-reset-btn:hover {
        border-color: #ef4444;
        color: #ef4444;
    }
    .itam-ms-apply-btn {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        border: none;
        border-radius: 8px;
        color: #ffffff;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 4px 14px;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .itam-ms-apply-btn:hover {
        background: linear-gradient(135deg, #4338ca, #4f46e5);
    }
    @media (max-width: 767.98px) {
        .itam-ms-wrap {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 auto !important;
            height: auto !important;
            margin: 0 !important;
        }
        .itam-ms-trigger {
            height: 40px !important;
            padding: 0 12px !important;
        }
        .itam-ms-panel {
            min-width: 100% !important;
            max-width: 100% !important;
            width: 100% !important;
            left: 0 !important;
            right: 0 !important;
            top: calc(100% + 4px) !important;
        }
    }

    /* Hallmark · component: bulk-action-bar · genre: modern-minimal · pre-emit critique: P5 H5 E5 S5 R5 V5 */
    /* ── Hallmark Top Floating Bulk Action Bar ────────────────────────── */
    .asset-select-cb {
        width: 17px;
        height: 17px;
        cursor: pointer;
        accent-color: #6366f1;
        border-radius: 4px;
        vertical-align: middle;
        transition: transform 0.15s ease;
    }
    .asset-select-cb:hover {
        transform: scale(1.08);
    }
    .bulk-action-bar {
        position: fixed;
        top: 80px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1045;
        animation: bulkSlideDown 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
        max-width: calc(100vw - 32px);
        transition: left 0.3s cubic-bezier(0.16, 1, 0.3, 1), top 0.2s ease;
    }
    @media (min-width: 992px) {
        body:not(.sidebar-collapse) .bulk-action-bar {
            left: calc(50% + 125px);
        }
        body.sidebar-collapse .bulk-action-bar {
            left: calc(50% + 36px);
        }
    }
    @keyframes bulkSlideDown {
        from {
            opacity: 0;
            transform: translate(-50%, -18px) scale(0.96);
        }
        to {
            opacity: 1;
            transform: translate(-50%, 0) scale(1);
        }
    }
    .bulk-action-bar-content {
        pointer-events: auto;
        background: rgba(15, 23, 42, 0.94);
        border: 1px solid rgba(148, 163, 184, 0.22);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(99, 102, 241, 0.25), 0 4px 16px rgba(99, 102, 241, 0.15);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-radius: 999px;
        padding: 7px 12px 7px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: all 0.2s ease;
    }
    body.light-mode .bulk-action-bar-content {
        background: rgba(255, 255, 255, 0.96);
        border: 1px solid rgba(203, 213, 225, 0.9);
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(99, 102, 241, 0.2), 0 4px 12px rgba(99, 102, 241, 0.08);
    }
    .bulk-selection-summary {
        display: flex;
        align-items: center;
        gap: 10px;
        white-space: nowrap;
        user-select: none;
    }
    .bulk-count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 22px;
        height: 22px;
        padding: 0 7px;
        border-radius: 999px;
        font-family: var(--font-mono, 'JetBrains Mono', monospace);
        font-size: 0.74rem;
        font-weight: 700;
        color: #ffffff;
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.35);
    }
    .bulk-selected-label {
        font-size: 0.84rem;
        font-weight: 600;
        color: var(--color-ink-0, #f8fafc);
        letter-spacing: -0.01em;
    }
    body.light-mode .bulk-selected-label {
        color: #0f172a;
    }
    .bulk-action-divider {
        width: 1px;
        height: 20px;
        background: rgba(148, 163, 184, 0.2);
        flex-shrink: 0;
    }
    .bulk-action-buttons {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .bulk-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none !important;
        border: 1px solid transparent;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        white-space: nowrap;
    }
    .bulk-btn:focus-visible {
        outline: 2px solid #6366f1;
        outline-offset: 2px;
    }
    .bulk-btn:active {
        transform: translateY(1px) scale(0.98);
    }
    .bulk-btn-print {
        background: rgba(30, 41, 59, 0.65);
        border-color: rgba(148, 163, 184, 0.22);
        color: var(--color-ink-0, #f8fafc);
    }
    body.light-mode .bulk-btn-print {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    .bulk-btn-print:hover {
        background: rgba(56, 189, 248, 0.15);
        border-color: rgba(56, 189, 248, 0.5);
        color: #38bdf8;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(56, 189, 248, 0.2);
    }
    .bulk-btn-delete {
        background: rgba(239, 68, 68, 0.14);
        border-color: rgba(239, 68, 68, 0.4);
        color: #f87171;
    }
    body.light-mode .bulk-btn-delete {
        background: #fef2f2;
        border-color: #fecaca;
        color: #dc2626;
    }
    .bulk-btn-delete:hover {
        background: #ef4444;
        border-color: #dc2626;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35);
    }
    .bulk-btn-close {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 1px solid rgba(148, 163, 184, 0.2);
        background: rgba(255, 255, 255, 0.05);
        color: var(--color-ink-1, #94a3b8);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        font-size: 0.78rem;
    }
    body.light-mode .bulk-btn-close {
        background: #f8fafc;
        border-color: #e2e8f0;
        color: #64748b;
    }
    .bulk-btn-close:hover {
        transform: rotate(90deg) scale(1.05);
        background: rgba(239, 68, 68, 0.15);
        border-color: rgba(239, 68, 68, 0.4);
        color: #ef4444;
    }
    tr.asset-row-selected {
        background-color: rgba(99, 102, 241, 0.14) !important;
    }
    body.light-mode tr.asset-row-selected {
        background-color: rgba(99, 102, 241, 0.08) !important;
    }
    @media (max-width: 767.98px) {
        .bulk-action-bar {
            top: 72px;
            width: calc(100vw - 24px);
        }
        .bulk-action-bar-content {
            padding: 6px 10px 6px 14px;
            gap: 10px;
            justify-content: space-between;
            width: 100%;
        }
        .bulk-btn span {
            display: none;
        }
        .bulk-btn {
            padding: 6px 10px;
        }
        .bulk-action-divider {
            display: none;
        }
    }
</style>

<!-- Hallmark Master Header Banner -->
<div class="master-header-banner">
    <div class="d-flex align-items-center justify-content-between flex-wrap flex-md-nowrap" style="gap: 16px;">
        <div class="d-flex align-items-center" style="gap: 14px; min-width: 0; flex: 1;">
            <div style="width: 44px; height: 44px; border-radius: 14px; background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(79, 70, 229, 0.08)); border: 1px solid rgba(99, 102, 241, 0.35); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 1.2rem; flex-shrink: 0; box-shadow: 0 0 14px rgba(99, 102, 241, 0.25);">
                <i class="fas fa-laptop-house"></i>
            </div>
            <div style="flex: 1; min-width: 0;">
                <h5 class="mb-0 font-weight-bold theme-text text-truncate" style="font-size: 1.15rem; line-height: 1.2;">{{ __('messages.manage') }} {{ __('messages.it_assets') }}</h5>
                <small class="text-muted d-block" style="font-size: 0.8rem; margin-top: 2px; white-space: normal; line-height: 1.3;">{{ __('messages.asset_subtitle') }}</small>
            </div>
        </div>

        <div class="d-flex align-items-center flex-wrap flex-md-nowrap asset-action-buttons ml-auto" style="gap: 10px; flex-shrink: 0;">
            <span style="font-family: var(--font-mono); font-size: 0.72rem; padding: 7px 14px; background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.25); color: #818cf8; border-radius: 999px; display: inline-flex; align-items: center; gap: 6px; font-weight: 600; white-space: nowrap;">
                <span class="live-dot" style="width: 7px; height: 7px; border-radius: 50%; background: #6366f1; box-shadow: 0 0 8px #6366f1; display: inline-block;"></span>
                {{ number_format($totalAssetsCount ?? $assets->total()) }} {{ __('messages.recorded') }}
            </span>
            @can('action_manage_assets')
            <button type="button" class="btn btn-sm btn-action-secondary text-nowrap" data-toggle="modal" data-target="#importModal" title="{{ __('messages.import') }}">
                <i class="fas fa-file-import mr-1"></i> {{ __('messages.import') }}
            </button>
            @endcan
            <a href="{{ route('assets.export', request()->except('page')) }}" class="btn btn-sm btn-action-secondary text-nowrap" title="{{ __('messages.export') }}">
                <i class="fas fa-file-export mr-1"></i> {{ __('messages.export') }}
            </a>
            <a href="{{ route('assets.print-barcodes', request()->except('page')) }}" class="btn btn-sm btn-action-secondary text-nowrap" title="{{ __('messages.bulk_print_barcode_title') }}">
                <i class="fas fa-print mr-1 text-info"></i> {{ __('messages.bulk_print_barcode') }}
            </a>
            @can('action_manage_assets')
            <a href="{{ route('assets.create', request()->query()) }}" class="btn btn-sm btn-action-primary text-nowrap btn-add-main" title="{{ __('messages.add_new') }} {{ __('messages.asset') }}">
                <i class="fas fa-plus mr-1"></i> {{ __('messages.add_new') }} {{ __('messages.asset') }}
            </a>
            @endcan
        </div>
    </div>
</div>

@php
    $currentStatus = request('status', '');
    $statusList = [
        '' => ['label' => __('messages.all_status') ?? 'Semua', 'count' => $totalAssetsCount ?? $assets->total(), 'color' => 'indigo'],
        'Available' => ['label' => __('messages.available'), 'count' => $statusCounts['Available'] ?? 0, 'color' => 'success'],
        'Assigned' => ['label' => __('messages.assigned'), 'count' => $statusCounts['Assigned'] ?? 0, 'color' => 'primary'],
        'Maintenance' => ['label' => __('messages.maintenance'), 'count' => $statusCounts['Maintenance'] ?? 0, 'color' => 'warning'],
        'Retired' => ['label' => __('messages.retired'), 'count' => $statusCounts['Retired'] ?? 0, 'color' => 'secondary'],
        'Missing' => ['label' => __('messages.missing'), 'count' => $statusCounts['Missing'] ?? 0, 'color' => 'danger'],
    ];
@endphp

<!-- Hallmark Quick Status Ribbon -->
<div class="hallmark-status-ribbon">
    @foreach($statusList as $statusKey => $statusItem)
        @php
            $isActive = ($currentStatus === (string)$statusKey) || ($statusKey === '' && empty($currentStatus));
            $filterParams = array_merge(request()->except(['status', 'page']), $statusKey !== '' ? ['status' => $statusKey] : []);
        @endphp
        <a href="{{ route('assets.index', $filterParams) }}" class="status-ribbon-pill {{ $isActive ? 'active' : '' }}">
            <span>{{ $statusItem['label'] }}</span>
            <span class="status-ribbon-count">{{ number_format($statusItem['count']) }}</span>
        </a>
    @endforeach
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; border-radius: 14px;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; border-radius: 14px;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
    </div>
@endif

<!-- Main Table Card -->
<div class="card theme-card master-table-card">
    <div class="card-header border-0">
        <form action="{{ route('assets.index') }}" method="GET" style="width: 100%;" id="asset-filter-form">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
            @if(request('direction'))
                <input type="hidden" name="direction" value="{{ request('direction') }}">
            @endif
            @if(request('sort_order'))
                <input type="hidden" name="sort_order" value="{{ request('sort_order') }}">
            @endif

            @php
                $selYears    = (array) request('date_received', []);
                $selCats     = (array) request('category_id', []);
                $selBrands   = (array) request('brand_id', []);
                $selLocs     = (array) request('location_id', []);
            @endphp

            <div class="d-flex align-items-center flex-wrap gap-2 asset-filter-container">

                {{-- ── Tahun Multi-Select ── --}}
                <div class="itam-ms-wrap" data-name="date_received" data-placeholder="{{ __('messages.all_years') }}">
                    <button type="button" class="itam-ms-trigger">
                        <span class="itam-ms-label">{{ !empty($selYears) ? (count($selYears) === 1 ? $selYears[0] : __('messages.selected_count', ['count' => count($selYears)])) : __('messages.all_years') }}</span>
                        <i class="fas fa-chevron-down itam-ms-chevron"></i>
                    </button>
                    <div class="itam-ms-panel">
                        <div class="itam-ms-search-wrap">
                            <i class="fas fa-search itam-ms-search-icon"></i>
                            <input type="text" class="itam-ms-search" placeholder="{{ __('messages.search_year') }}">
                        </div>
                        <div class="itam-ms-select-all-row">
                            <label class="itam-ms-item itam-ms-selectall">
                                <input type="checkbox" class="itam-ms-selectall-cb"> {{ __('messages.select_all') }}
                            </label>
                        </div>
                        <div class="itam-ms-list">
                            @foreach($years as $yr)
                            <label class="itam-ms-item" data-label="{{ $yr }}">
                                <input type="checkbox" name="date_received[]" value="{{ $yr }}"
                                    {{ in_array((string)$yr, $selYears) ? 'checked' : '' }}>
                                {{ $yr }}
                            </label>
                            @endforeach
                        </div>
                        <div class="itam-ms-footer">
                            <button type="button" class="itam-ms-reset-btn">{{ __('messages.reset') }}</button>
                            <button type="button" class="itam-ms-apply-btn">{{ __('messages.apply') }}</button>
                        </div>
                    </div>
                </div>

                {{-- ── Kategori Multi-Select ── --}}
                <div class="itam-ms-wrap" data-name="category_id" data-placeholder="{{ __('messages.all_categories') }}">
                    <button type="button" class="itam-ms-trigger">
                        <span class="itam-ms-label">{{ !empty($selCats) ? (count($selCats) === 1 ? ($categories->firstWhere('id', $selCats[0])->name ?? __('messages.all_categories')) : __('messages.selected_count', ['count' => count($selCats)])) : __('messages.all_categories') }}</span>
                        <i class="fas fa-chevron-down itam-ms-chevron"></i>
                    </button>
                    <div class="itam-ms-panel">
                        <div class="itam-ms-search-wrap">
                            <i class="fas fa-search itam-ms-search-icon"></i>
                            <input type="text" class="itam-ms-search" placeholder="{{ __('messages.search_category') }}">
                        </div>
                        <div class="itam-ms-select-all-row">
                            <label class="itam-ms-item itam-ms-selectall">
                                <input type="checkbox" class="itam-ms-selectall-cb"> {{ __('messages.select_all') }}
                            </label>
                        </div>
                        <div class="itam-ms-list">
                            @foreach($categories as $cat)
                            <label class="itam-ms-item" data-label="{{ $cat->name }}">
                                <input type="checkbox" name="category_id[]" value="{{ $cat->id }}"
                                    {{ in_array((string)$cat->id, $selCats) ? 'checked' : '' }}>
                                {{ $cat->name }}
                            </label>
                            @endforeach
                        </div>
                        <div class="itam-ms-footer">
                            <button type="button" class="itam-ms-reset-btn">{{ __('messages.reset') }}</button>
                            <button type="button" class="itam-ms-apply-btn">{{ __('messages.apply') }}</button>
                        </div>
                    </div>
                </div>

                {{-- ── Brand Multi-Select ── --}}
                <div class="itam-ms-wrap" data-name="brand_id" data-placeholder="{{ __('messages.all_brands') }}">
                    <button type="button" class="itam-ms-trigger">
                        <span class="itam-ms-label">{{ !empty($selBrands) ? (count($selBrands) === 1 ? ($brands->firstWhere('id', $selBrands[0])->name ?? __('messages.all_brands')) : __('messages.selected_count', ['count' => count($selBrands)])) : __('messages.all_brands') }}</span>
                        <i class="fas fa-chevron-down itam-ms-chevron"></i>
                    </button>
                    <div class="itam-ms-panel">
                        <div class="itam-ms-search-wrap">
                            <i class="fas fa-search itam-ms-search-icon"></i>
                            <input type="text" class="itam-ms-search" placeholder="{{ __('messages.search_brand') }}">
                        </div>
                        <div class="itam-ms-select-all-row">
                            <label class="itam-ms-item itam-ms-selectall">
                                <input type="checkbox" class="itam-ms-selectall-cb"> {{ __('messages.select_all') }}
                            </label>
                        </div>
                        <div class="itam-ms-list">
                            @foreach($brands as $brand)
                            <label class="itam-ms-item" data-label="{{ $brand->name }}">
                                <input type="checkbox" name="brand_id[]" value="{{ $brand->id }}"
                                    {{ in_array((string)$brand->id, $selBrands) ? 'checked' : '' }}>
                                {{ $brand->name }}
                            </label>
                            @endforeach
                        </div>
                        <div class="itam-ms-footer">
                            <button type="button" class="itam-ms-reset-btn">{{ __('messages.reset') }}</button>
                            <button type="button" class="itam-ms-apply-btn">{{ __('messages.apply') }}</button>
                        </div>
                    </div>
                </div>

                {{-- ── Lokasi Multi-Select ── --}}
                <div class="itam-ms-wrap" data-name="location_id" data-placeholder="{{ __('messages.all_locations') }}">
                    <button type="button" class="itam-ms-trigger">
                        <span class="itam-ms-label">{{ !empty($selLocs) ? (count($selLocs) === 1 ? ($locations->firstWhere('id', $selLocs[0])->name ?? __('messages.all_locations')) : __('messages.selected_count', ['count' => count($selLocs)])) : __('messages.all_locations') }}</span>
                        <i class="fas fa-chevron-down itam-ms-chevron"></i>
                    </button>
                    <div class="itam-ms-panel">
                        <div class="itam-ms-search-wrap">
                            <i class="fas fa-search itam-ms-search-icon"></i>
                            <input type="text" class="itam-ms-search" placeholder="{{ __('messages.search_location') }}">
                        </div>
                        <div class="itam-ms-select-all-row">
                            <label class="itam-ms-item itam-ms-selectall">
                                <input type="checkbox" class="itam-ms-selectall-cb"> {{ __('messages.select_all') }}
                            </label>
                        </div>
                        <div class="itam-ms-list">
                            @foreach($locations as $loc)
                            <label class="itam-ms-item" data-label="{{ $loc->name }}">
                                <input type="checkbox" name="location_id[]" value="{{ $loc->id }}"
                                    {{ in_array((string)$loc->id, $selLocs) ? 'checked' : '' }}>
                                {{ $loc->name }}
                            </label>
                            @endforeach
                        </div>
                        <div class="itam-ms-footer">
                            <button type="button" class="itam-ms-reset-btn">{{ __('messages.reset') }}</button>
                            <button type="button" class="itam-ms-apply-btn">{{ __('messages.apply') }}</button>
                        </div>
                    </div>
                </div>

                {{-- ── Sort Order ── --}}
                <select name="sort_order" class="form-control custom-filter-select" onchange="this.form.submit()" title="{{ __('messages.sort_by') }}">
                    <option value="date_desc" {{ (($sortBy === 'date_received' || $sortBy === 'date') && $sortDir === 'desc') ? 'selected' : '' }}>
                        {{ __('messages.sort_date_desc') }}
                    </option>
                    <option value="date_asc" {{ (($sortBy === 'date_received' || $sortBy === 'date') && $sortDir === 'asc') ? 'selected' : '' }}>
                        {{ __('messages.sort_date_asc') }}
                    </option>
                    <option value="tag_asc" {{ ($sortBy === 'asset_tag' && $sortDir === 'asc') ? 'selected' : '' }}>
                        {{ __('messages.sort_tag_asc') }}
                    </option>
                    <option value="tag_desc" {{ ($sortBy === 'asset_tag' && $sortDir === 'desc') ? 'selected' : '' }}>
                        {{ __('messages.sort_tag_desc') }}
                    </option>
                    <option value="name_asc" {{ ($sortBy === 'name' && $sortDir === 'asc') ? 'selected' : '' }}>
                        {{ __('messages.sort_name_asc') }}
                    </option>
                    <option value="status" {{ ($sortBy === 'status') ? 'selected' : '' }}>
                        {{ __('messages.sort_status') }}
                    </option>
                </select>

                {{-- ── Search ── --}}
                <div class="sleek-search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" id="asset-search-input"
                        class="form-control theme-input sleek-search-input"
                        placeholder="{{ __('messages.search_asset') }} ({{ __('messages.search_asset_hint') }})..."
                        value="{{ request('search') }}" autocomplete="off">
                    @if(request('search') || !empty($selCats) || !empty($selBrands) || !empty($selLocs) || !empty($selYears) || request('sort_order') || request('sort'))
                        <a href="{{ route('assets.index', request()->only('status')) }}" class="clear-search-btn" title="{{ __('messages.clear') }}">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        @php
            $currentSort = $sortBy ?? request('sort', 'date_received');
            $currentDir = $sortDir ?? strtolower(request('direction', 'desc'));
            
            $makeSortUrl = function($field) use ($currentSort, $currentDir) {
                $nextDir = ($currentSort === $field && $currentDir === 'desc') ? 'asc' : 'desc';
                if ($currentSort !== $field && in_array($field, ['asset_tag', 'name'])) {
                    $nextDir = 'asc';
                }
                $params = request()->except(['sort', 'direction', 'sort_order', 'page']);
                $params['sort'] = $field;
                $params['direction'] = $nextDir;
                return route('assets.index', $params);
            };
        @endphp
        <div class="theme-scroll-container table-responsive">
            <table class="table table-striped table-hover m-0 theme-table division-table asset-table">
                <thead>
                    <tr style="font-family: var(--font-mono); font-size: 0.72rem; letter-spacing: 0.08em; text-transform: uppercase;">
                        <th width="42" class="pl-4 text-center">
                            <input type="checkbox" id="selectAllAssets" class="asset-select-cb" title="{{ __('messages.select_all_assets') }}">
                        </th>
                        <th width="50">{{ __('messages.no') }}</th>
                        <th>
                            <a href="{{ $makeSortUrl('asset_tag') }}" class="table-sort-link {{ $currentSort === 'asset_tag' ? 'active' : '' }}" title="{{ __('messages.sort_by') }} {{ __('messages.asset_tag') }}">
                                {{ __('messages.asset_tag') }}
                                @if($currentSort === 'asset_tag')
                                    <i class="fas {{ $currentDir === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 sort-neutral"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ $makeSortUrl('name') }}" class="table-sort-link {{ $currentSort === 'name' ? 'active' : '' }}" title="{{ __('messages.sort_by') }} {{ __('messages.name') }}">
                                {{ __('messages.name') }}
                                @if($currentSort === 'name')
                                    <i class="fas {{ $currentDir === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 sort-neutral"></i>
                                @endif
                            </a>
                        </th>
                        <th>{{ __('messages.category') }}</th>
                        <th>
                            <a href="{{ $makeSortUrl('date_received') }}" class="table-sort-link {{ in_array($currentSort, ['date_received', 'date']) ? 'active' : '' }}" title="{{ __('messages.sort_by') }} {{ __('messages.received') }}">
                                {{ __('messages.received') }}
                                @if(in_array($currentSort, ['date_received', 'date']))
                                    <i class="fas {{ $currentDir === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 sort-neutral"></i>
                                @endif
                            </a>
                        </th>
                        <th>{{ __('messages.location') }}</th>
                        <th>{{ __('messages.user') }}</th>
                        <th>
                            <a href="{{ $makeSortUrl('status') }}" class="table-sort-link {{ $currentSort === 'status' ? 'active' : '' }}" title="{{ __('messages.sort_by') }} {{ __('messages.status') }}">
                                {{ __('messages.status') }}
                                @if($currentSort === 'status')
                                    <i class="fas {{ $currentDir === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 sort-neutral"></i>
                                @endif
                            </a>
                        </th>
                        <th>{{ __('messages.health') }}</th>
                        <th width="100" class="text-center pr-4">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                    <tr id="asset-row-{{ $asset->id }}">
                        <td class="pl-4 text-center">
                            <input type="checkbox" class="asset-row-checkbox asset-select-cb" value="{{ $asset->id }}" aria-label="Select asset {{ $asset->asset_tag }}">
                        </td>
                        <td class="theme-text text-muted" style="font-family: var(--font-mono); font-size: 0.82rem;">
                            {{ (method_exists($assets, 'firstItem') && $assets->firstItem() ? $assets->firstItem() + $loop->index : $loop->iteration) }}
                        </td>
                        <td>
                            <a href="{{ route('assets.show', $asset) }}" class="asset-tag-pill" title="{{ __('messages.view_asset_details') }}">
                                {{ $asset->asset_tag }}
                            </a>
                        </td>
                        <td>
                            <div class="d-flex align-items-center" style="gap: 10px;">
                                @if($asset->image)
                                    <img src="{{ asset('storage/' . $asset->image) }}" alt="{{ $asset->name }}" class="asset-thumb">
                                @else
                                    <div class="asset-thumb-placeholder">
                                        <i class="fas fa-box-open"></i>
                                    </div>
                                @endif
                                <div style="min-width: 0;">
                                    <a href="{{ route('assets.show', $asset) }}" class="theme-text font-weight-bold text-decoration-none d-block text-truncate" style="max-width: 220px; font-size: 0.88rem;">
                                        {{ $asset->name }}
                                    </a>
                                    @if($asset->serial_number)
                                        <small class="text-muted d-block text-truncate" style="font-family: var(--font-mono); font-size: 0.72rem;">
                                            SN: {{ $asset->serial_number }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="theme-text" style="font-size: 0.85rem;">{{ $asset->category->name ?? '-' }}</td>
                        <td class="theme-text" style="font-family: var(--font-mono); font-size: 0.82rem;">
                            {{ $asset->date_received ? \Carbon\Carbon::parse($asset->date_received)->format('d M Y') : '-' }}
                        </td>
                        <td class="theme-text" style="font-size: 0.85rem;">{{ $asset->location->name ?? '-' }}</td>
                        <td class="theme-text" style="font-size: 0.85rem;">
                            @if($asset->currentAssignment && $asset->currentAssignment->employee)
                                <a href="{{ route('employees.show', $asset->currentAssignment->employee_id) }}" class="text-info font-weight-bold text-decoration-none hover-highlight">
                                    <i class="fas fa-user-circle mr-1"></i> {{ $asset->currentAssignment->employee->name }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                @php
                                    $statusClasses = [
                                        'Available'   => 'badge-status-available',
                                        'Assigned'    => 'badge-status-assigned',
                                        'Maintenance' => 'badge-status-maintenance',
                                        'Retired'     => 'badge-status-retired',
                                        'Missing'     => 'badge-status-missing',
                                    ];
                                    $currentClass = $statusClasses[$asset->status] ?? 'badge-status-available';
                                @endphp
                                @can('action_manage_assets')
                                    <button class="btn btn-sm dropdown-toggle status-btn p-0 border-0 bg-transparent" type="button" data-toggle="dropdown" aria-expanded="false" data-id="{{ $asset->id }}" style="box-shadow: none;">
                                        <span class="hallmark-status-badge {{ $currentClass }} status-badge">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: currentColor; display: inline-block;"></span>
                                            {{ __('messages.' . strtolower($asset->status)) ?? $asset->status }}
                                            <i class="fas fa-chevron-down ml-1" style="font-size: 0.65rem; opacity: 0.7;"></i>
                                        </span>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" style="border-radius: 12px; background: rgba(15, 23, 42, 0.98); border: 1px solid rgba(148, 163, 184, 0.2);">
                                        <a class="dropdown-item status-change-btn text-success" href="#" data-status="Available"><i class="fas fa-check-circle mr-2"></i> {{ __('messages.available') }}</a>
                                        <a class="dropdown-item status-change-btn text-primary" href="#" data-status="Assigned"><i class="fas fa-user-check mr-2"></i> {{ __('messages.assigned') }}</a>
                                        <a class="dropdown-item status-change-btn text-warning" href="#" data-status="Maintenance"><i class="fas fa-tools mr-2"></i> {{ __('messages.maintenance') }}</a>
                                        <a class="dropdown-item status-change-btn text-secondary" href="#" data-status="Retired"><i class="fas fa-archive mr-2"></i> {{ __('messages.retired') }}</a>
                                        <a class="dropdown-item status-change-btn text-danger" href="#" data-status="Missing"><i class="fas fa-exclamation-triangle mr-2"></i> {{ __('messages.missing') }}</a>
                                    </div>
                                @else
                                    <span class="hallmark-status-badge {{ $currentClass }}">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: currentColor; display: inline-block;"></span>
                                        {{ __('messages.' . strtolower($asset->status)) ?? $asset->status }}
                                    </span>
                                @endcan
                            </div>
                        </td>
                        <td>
                            @if(isset($asset->health_data))
                                <a href="{{ route('predictive-health.index') }}" class="badge {{ $asset->health_data['badge_class'] }} px-2 py-1.5" style="border-radius: 8px; font-family: var(--font-mono); font-size: 0.74rem;" title="{{ __('messages.predictive_health_score') }}">
                                    <i class="fas fa-heartbeat mr-1"></i> {{ $asset->health_data['health_score'] }}%
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center pr-4">
                            <div class="d-flex align-items-center justify-content-center" style="gap: 6px;">
                                @can('action_manage_assets')
                                    <a href="{{ route('assets.edit', array_merge(['asset' => $asset->id], request()->query())) }}" class="btn-action-icon btn-edit" title="{{ __('messages.edit') }}" aria-label="{{ __('messages.edit') }}">
                                        <i class="fas fa-pen" style="font-size: 0.78rem;"></i>
                                    </a>
                                    <form action="{{ route('assets.destroy', array_merge(['asset' => $asset->id], request()->query())) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-action-icon btn-delete btn-delete-confirm" title="{{ __('messages.delete') }}" aria-label="{{ __('messages.delete') }}" data-confirm-message="{{ __('messages.confirm_delete') }}">
                                            <i class="fas fa-trash-alt" style="font-size: 0.78rem;"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <div style="width: 56px; height: 56px; border-radius: 50%; background: rgba(99, 102, 241, 0.1); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 1.5rem; margin-bottom: 12px;">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <h6 class="theme-text font-weight-bold mb-1">{{ __('messages.no_data') }}</h6>
                                <p class="text-muted small mb-0">{{ __('messages.no_matching_assets') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assets->hasPages())
            <div class="pagination-footer card-footer d-flex align-items-center flex-wrap py-3 px-4" style="background: transparent; border-top: 1px solid rgba(148, 163, 184, 0.12); gap: 10px;">
                {{ $assets->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Floating Bulk Action Toolbar (Hallmark Anti-AI-Slop Top Bar) -->
<div id="bulkActionBar" class="bulk-action-bar d-none" role="region" aria-label="Bulk actions toolbar">
    <div class="bulk-action-bar-content">
        <div class="bulk-selection-summary">
            <span class="bulk-count-badge" id="bulkSelectedCountBadge">0</span>
            <span class="bulk-selected-label" id="bulkSelectedText">0 {{ __('messages.selected_assets_count', ['count' => 0]) }}</span>
        </div>
        <div class="bulk-action-divider" aria-hidden="true"></div>
        <div class="bulk-action-buttons">
            <button type="button" class="bulk-btn bulk-btn-print" id="btnBulkPrint" title="{{ __('messages.bulk_print_selected') }}">
                <i class="fas fa-print mr-1 text-info"></i> <span>{{ __('messages.bulk_print_selected') }}</span>
            </button>
            @can('action_manage_assets')
            <button type="button" class="bulk-btn bulk-btn-delete" id="btnBulkDelete" title="{{ __('messages.bulk_delete') }}">
                <i class="fas fa-trash-alt mr-1 text-danger"></i> <span>{{ __('messages.bulk_delete') }}</span>
            </button>
            @endcan
            <button type="button" class="bulk-btn-close" id="btnCancelSelection" title="{{ __('messages.cancel') }}" aria-label="{{ __('messages.cancel') }}">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</div>

<!-- Hidden Bulk Action Forms -->
<form id="bulkDeleteForm" action="{{ route('assets.bulk-delete', request()->query()) }}" method="POST" style="display: none;">
    @csrf
    <div id="bulkDeleteInputs"></div>
</form>

<form id="bulkPrintForm" action="{{ route('assets.print-barcodes') }}" method="GET" target="_blank" style="display: none;">
    <div id="bulkPrintInputs"></div>
</form>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('assets.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <div class="d-flex align-items-center" style="gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 1.1rem;">
                            <i class="fas fa-file-import"></i>
                        </div>
                        <div>
                            <h5 class="modal-title font-weight-bold theme-text" id="importModalLabel" style="font-size: 1.05rem;">{{ __('messages.import') }} {{ __('messages.asset') }}</h5>
                            <small class="text-muted">{{ __('messages.import_modal_subtitle') }}</small>
                        </div>
                    </div>
                    <button type="button" class="close text-muted" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="file-dropzone" id="excelDropzone">
                        <input type="file" name="file" id="excelFileInput" accept=".xlsx,.xls,.csv" required onchange="handleExcelFileName(this)">
                        <div style="font-size: 2.2rem; color: #818cf8; margin-bottom: 10px;">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="font-weight-bold theme-text mb-1" style="font-size: 0.95rem;" id="dropzoneLabel">
                            {{ __('messages.select_excel_csv') }}
                        </div>
                        <small class="text-muted d-block" id="dropzoneHint">
                            {{ __('messages.select_file_hint') }} ({{ App::getLocale() === 'en' ? 'Max 10MB' : 'Maks. 10MB' }})
                        </small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-action-secondary" data-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-sm btn-action-primary px-4">
                        <i class="fas fa-upload mr-1"></i> {{ __('messages.start_import') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function handleExcelFileName(input) {
    if (input.files && input.files[0]) {
        var fileName = input.files[0].name;
        var readyText = @json(__('messages.ready_to_upload'));
        $('#dropzoneLabel').text(fileName);
        $('#dropzoneHint').text((input.files[0].size / 1024 / 1024).toFixed(2) + ' MB — ' + readyText);
        $('#excelDropzone').css('border-color', '#10b981').css('background', 'rgba(16, 185, 129, 0.08)');
    }
}

// ── ITAM Custom Multi-Select ────────────────────────────────────────────────
(function() {
    var selectedCountTpl = @json(__('messages.selected_count'));
    function itamUpdateLabel(wrap) {
        var checked = wrap.querySelectorAll('.itam-ms-list input[type="checkbox"]:checked');
        var label   = wrap.querySelector('.itam-ms-label');
        var placeholder = wrap.dataset.placeholder || wrap.getAttribute('data-placeholder') || '...';
        // remove old badge
        var oldBadge = wrap.querySelector('.itam-ms-count-badge');
        if (oldBadge) oldBadge.remove();

        if (checked.length === 0) {
            label.textContent = placeholder;
            wrap.classList.remove('has-value');
        } else if (checked.length === 1) {
            var lbl = checked[0].closest('[data-label]');
            label.textContent = lbl ? lbl.dataset.label : checked[0].value;
            wrap.classList.add('has-value');
        } else {
            label.textContent = selectedCountTpl.replace(':count', checked.length);
            wrap.classList.add('has-value');
        }
    }

    function itamInitWrap(wrap) {
        var trigger  = wrap.querySelector('.itam-ms-trigger');
        var panel    = wrap.querySelector('.itam-ms-panel');
        var search   = wrap.querySelector('.itam-ms-search');
        var items    = wrap.querySelectorAll('.itam-ms-list .itam-ms-item');
        var allCb    = wrap.querySelector('.itam-ms-selectall-cb');
        var resetBtn = wrap.querySelector('.itam-ms-reset-btn');
        var applyBtn = wrap.querySelector('.itam-ms-apply-btn');

        // Initial label update
        itamUpdateLabel(wrap);

        // Toggle panel
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            var isOpen = wrap.classList.contains('open');
            // Close all other panels
            document.querySelectorAll('.itam-ms-wrap.open').forEach(function(w) {
                if (w !== wrap) w.classList.remove('open');
            });
            wrap.classList.toggle('open', !isOpen);
            if (!isOpen && search) { setTimeout(function(){ search.focus(); }, 50); }
        });

        // Search filter
        if (search) {
            search.addEventListener('input', function() {
                var q = this.value.toLowerCase().trim();
                items.forEach(function(item) {
                    var lbl = (item.dataset.label || item.textContent).toLowerCase();
                    item.style.display = (q === '' || lbl.includes(q)) ? '' : 'none';
                });
                // Update select-all state
                if (allCb) {
                    var visible  = wrap.querySelectorAll('.itam-ms-list .itam-ms-item:not([style*="display: none"]) input[type="checkbox"]');
                    var checked  = wrap.querySelectorAll('.itam-ms-list .itam-ms-item:not([style*="display: none"]) input[type="checkbox"]:checked');
                    allCb.checked = visible.length > 0 && visible.length === checked.length;
                    allCb.indeterminate = checked.length > 0 && checked.length < visible.length;
                }
            });
        }

        // Select all (only visible items)
        if (allCb) {
            allCb.addEventListener('change', function() {
                var visible = wrap.querySelectorAll('.itam-ms-list .itam-ms-item:not([style*="display: none"]) input[type="checkbox"]');
                visible.forEach(function(cb) { cb.checked = allCb.checked; });
                itamUpdateLabel(wrap);
            });
        }

        // Sync select-all state on individual check
        items.forEach(function(item) {
            var cb = item.querySelector('input[type="checkbox"]');
            if (!cb) return;
            cb.addEventListener('change', function() {
                if (allCb) {
                    var visible = wrap.querySelectorAll('.itam-ms-list .itam-ms-item:not([style*="display: none"]) input[type="checkbox"]');
                    var checked = wrap.querySelectorAll('.itam-ms-list .itam-ms-item:not([style*="display: none"]) input[type="checkbox"]:checked');
                    allCb.checked = visible.length > 0 && visible.length === checked.length;
                    allCb.indeterminate = checked.length > 0 && checked.length < visible.length;
                }
                itamUpdateLabel(wrap);
            });
        });

        // Reset
        if (resetBtn) {
            resetBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                wrap.querySelectorAll('input[type="checkbox"]').forEach(function(cb){ cb.checked = false; });
                if (allCb) { allCb.checked = false; allCb.indeterminate = false; }
                itamUpdateLabel(wrap);
            });
        }

        // Apply
        if (applyBtn) {
            applyBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                wrap.classList.remove('open');
                document.getElementById('asset-filter-form').submit();
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.itam-ms-wrap').forEach(itamInitWrap);

        // Close on outside click
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.itam-ms-wrap')) {
                document.querySelectorAll('.itam-ms-wrap.open').forEach(function(w) {
                    w.classList.remove('open');
                });
            }
        });

        // Search: submit on Enter
        document.querySelectorAll('#asset-search-input').forEach(function(inp) {
            inp.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('asset-filter-form').submit();
                }
            });
        });
    });
})();

$(document).ready(function() {
    // In-line Status Change via AJAX
    $(document).on('click', '.status-change-btn', function(e) {
        e.preventDefault();
        var btn = $(this);
        var newStatus = btn.data('status');
        var container = btn.closest('.dropdown');
        var id = container.find('.status-btn').data('id');
        var badge = container.find('.status-badge');
        
        var originalHtml = badge.html();
        badge.html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: '{{ url("assets") }}/' + id + '/status',
            type: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                if(response.success) {
                    badge.removeClass('badge-status-available badge-status-assigned badge-status-maintenance badge-status-retired badge-status-missing');
                    
                    var statusClassMap = {
                        'Available': 'badge-status-available',
                        'Assigned': 'badge-status-assigned',
                        'Maintenance': 'badge-status-maintenance',
                        'Retired': 'badge-status-retired',
                        'Missing': 'badge-status-missing'
                    };
                    
                    var labelMap = {
                        'Available': '{{ __("messages.available") }}',
                        'Assigned': '{{ __("messages.assigned") }}',
                        'Maintenance': '{{ __("messages.maintenance") }}',
                        'Retired': '{{ __("messages.retired") }}',
                        'Missing': '{{ __("messages.missing") }}'
                    };

                    badge.addClass(statusClassMap[newStatus] || 'badge-status-available');
                    badge.html('<span style="width: 6px; height: 6px; border-radius: 50%; background: currentColor; display: inline-block;"></span> ' + 
                               (labelMap[newStatus] || newStatus) + 
                               ' <i class="fas fa-chevron-down ml-1" style="font-size: 0.65rem; opacity: 0.7;"></i>');

                    if (window.showHallmarkToast) {
                        window.showHallmarkToast('success', '{{ __("messages.status_updated") }}', '{{ __("messages.status") }}: ' + (labelMap[newStatus] || newStatus));
                    }
                }
            },
            error: function(xhr) {
                badge.html(originalHtml);
                if (window.showHallmarkToast) {
                    window.showHallmarkToast('error', '{{ __("messages.update_failed") }}', '{{ __("messages.error_updating_status") }}');
                } else {
                    Swal.fire({ icon: 'error', text: '{{ __("messages.error_updating_status") }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, background: '#0f172a', color: '#f8fafc' });
                }
            }
        });
    });

    // Delete Confirmation with SweetAlert
    $('.btn-delete-confirm').on('click', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var msg = $(this).data('confirm-message') || '{{ __("messages.confirm_delete") }}';
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '{{ __("messages.delete_confirm_title") }}',
                text: msg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: '{{ __("messages.yes_delete") }}',
                cancelButtonText: '{{ __("messages.cancel") }}',
                background: '#0f172a',
                color: '#f8fafc'
            }).then(function(result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        } else {
            if (confirm(msg)) {
                form.submit();
            }
        }
    });

    // Bulk Selection & Actions Logic
    function updateBulkActionBar() {
        var checkedBoxes = $('.asset-row-checkbox:checked');
        var count = checkedBoxes.length;
        var totalBoxes = $('.asset-row-checkbox').length;

        if (count > 0) {
            $('#bulkSelectedCountBadge').text(count);
            var template = '{{ __("messages.selected_assets_count", ["count" => ":count"]) }}';
            var label = template.replace(':count', count);
            $('#bulkSelectedText').text(label);
            $('#bulkActionBar').removeClass('d-none');
        } else {
            $('#bulkActionBar').addClass('d-none');
        }

        var selectAll = $('#selectAllAssets');
        if (count === 0) {
            selectAll.prop('checked', false);
            selectAll.prop('indeterminate', false);
        } else if (count === totalBoxes && totalBoxes > 0) {
            selectAll.prop('checked', true);
            selectAll.prop('indeterminate', false);
        } else {
            selectAll.prop('checked', false);
            selectAll.prop('indeterminate', true);
        }
    }

    // Select All Checkbox change
    $('#selectAllAssets').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('.asset-row-checkbox').each(function() {
            $(this).prop('checked', isChecked);
            var row = $(this).closest('tr');
            if (isChecked) {
                row.addClass('asset-row-selected');
            } else {
                row.removeClass('asset-row-selected');
            }
        });
        updateBulkActionBar();
    });

    // Individual Row Checkbox change
    $(document).on('change', '.asset-row-checkbox', function() {
        var row = $(this).closest('tr');
        if ($(this).is(':checked')) {
            row.addClass('asset-row-selected');
        } else {
            row.removeClass('asset-row-selected');
        }
        updateBulkActionBar();
    });

    // Cancel Selection button
    $('#btnCancelSelection').on('click', function() {
        $('.asset-row-checkbox').prop('checked', false);
        $('#selectAllAssets').prop('checked', false).prop('indeterminate', false);
        $('.asset-table tbody tr').removeClass('asset-row-selected');
        updateBulkActionBar();
    });

    // Dismiss selection with Escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && !$('#bulkActionBar').hasClass('d-none')) {
            $('#btnCancelSelection').trigger('click');
        }
    });

    // Bulk Delete Action
    $('#btnBulkDelete').on('click', function(e) {
        e.preventDefault();
        var selectedIds = [];
        $('.asset-row-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            return;
        }

        var confirmTemplate = '{{ __("messages.confirm_bulk_delete", ["count" => ":count"]) }}';
        var confirmMsg = confirmTemplate.replace(':count', selectedIds.length);

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '{{ __("messages.bulk_delete_title") }}',
                text: confirmMsg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: '{{ __("messages.yes_delete") }}',
                cancelButtonText: '{{ __("messages.cancel") }}',
                background: '#0f172a',
                color: '#f8fafc'
            }).then(function(result) {
                if (result.isConfirmed) {
                    var container = $('#bulkDeleteInputs');
                    container.empty();
                    selectedIds.forEach(function(id) {
                        container.append('<input type="hidden" name="ids[]" value="' + id + '">');
                    });
                    $('#bulkDeleteForm').submit();
                }
            });
        } else {
            if (confirm(confirmMsg)) {
                var container = $('#bulkDeleteInputs');
                container.empty();
                selectedIds.forEach(function(id) {
                    container.append('<input type="hidden" name="ids[]" value="' + id + '">');
                });
                $('#bulkDeleteForm').submit();
            }
        }
    });

    // Bulk Print Barcode Action
    $('#btnBulkPrint').on('click', function(e) {
        e.preventDefault();
        var selectedIds = [];
        $('.asset-row-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            return;
        }

        var container = $('#bulkPrintInputs');
        container.empty();
        selectedIds.forEach(function(id) {
            container.append('<input type="hidden" name="ids[]" value="' + id + '">');
        });
        $('#bulkPrintForm').submit();
    });
});
</script>
@endpush
@endsection

