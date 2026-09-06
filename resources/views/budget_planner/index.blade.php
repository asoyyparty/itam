@extends('layouts.admin')

@section('title', __('messages.budget_planner_title'))

@section('content')
<!-- Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 -->
<style>
    /* ═══════════════════════════════════════════════════════════════════
       HALLMARK WORKBENCH: IT CAPITAL & LIFECYCLE BUDGET COMMAND CENTER
       ═══════════════════════════════════════════════════════════════════ */
    .budget-workbench {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        overflow-x: clip;
    }

    /* 1. Command Header Banner */
    .budget-header-card {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 16px;
        padding: 20px 24px;
        position: relative;
        overflow: hidden;
    }
    .budget-header-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, #10b981 0%, #38bdf8 50%, #6366f1 100%);
    }
    .budget-header-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .budget-header-left {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
        flex: 1 1 auto;
    }
    .budget-header-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: rgba(16, 185, 129, 0.12);
        border: 1px solid rgba(16, 185, 129, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #34d399;
        font-size: 1.35rem;
        flex-shrink: 0;
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.2);
    }
    .budget-title-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 3px;
    }
    .budget-title-text {
        font-size: 1.3rem;
        font-weight: 700;
        letter-spacing: -0.01em;
        color: #f8fafc;
        line-height: 1.2;
    }
    .budget-tagline-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.28);
        color: #34d399;
        padding: 2px 10px;
        border-radius: 9999px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .budget-pulse-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 8px #10b981;
        animation: budgetPulse 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
    }
    @keyframes budgetPulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.35; transform: scale(0.85); }
    }
    .budget-subtitle-text {
        color: #94a3b8;
        font-size: 0.84rem;
        margin-bottom: 0;
        line-height: 1.4;
    }

    /* Fiscal Ribbon & Export */
    .budget-header-controls {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .fiscal-pills-bar {
        display: inline-flex;
        align-items: center;
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 12px;
        padding: 3px;
        gap: 2px;
    }
    .fiscal-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 9px;
        font-size: 0.82rem;
        font-weight: 600;
        font-family: 'JetBrains Mono', monospace;
        color: #94a3b8 !important;
        text-decoration: none !important;
        transition: all 0.15s ease;
    }
    .fiscal-pill:hover {
        color: #f8fafc !important;
        background: rgba(148, 163, 184, 0.1);
    }
    .fiscal-pill.active {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #064e3b !important;
        font-weight: 700;
        box-shadow: 0 2px 10px rgba(16, 185, 129, 0.35);
    }
    .btn-export-budget {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: rgba(16, 185, 129, 0.12);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #34d399 !important;
        border-radius: 12px;
        padding: 0.55rem 1.15rem;
        font-weight: 600;
        font-size: 0.82rem;
        text-decoration: none !important;
        transition: all 0.15s ease;
        height: 38px;
    }
    .btn-export-budget:hover {
        background: rgba(16, 185, 129, 0.22);
        color: #6ee7b7 !important;
        border-color: rgba(16, 185, 129, 0.5);
    }

    /* 2. Bento KPI Matrix (4 Quadrants) */
    .budget-bento-card {
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
    .budget-bento-card:hover {
        transform: translateY(-2px);
        border-color: rgba(148, 163, 184, 0.25);
    }
    .budget-bento-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 8px;
    }
    .budget-bento-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
    }
    .budget-bento-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }
    .bento-grand-total .budget-bento-icon { background: rgba(16, 185, 129, 0.12); color: #34d399; }
    .bento-capex .budget-bento-icon { background: rgba(56, 189, 248, 0.12); color: #38bdf8; }
    .bento-maint .budget-bento-icon { background: rgba(245, 158, 11, 0.12); color: #fbbf24; }
    .bento-lic .budget-bento-icon { background: rgba(99, 102, 241, 0.12); color: #818cf8; }

    .budget-bento-val {
        font-size: 1.42rem;
        font-weight: 800;
        font-family: 'JetBrains Mono', monospace;
        letter-spacing: -0.02em;
        line-height: 1.15;
        margin-bottom: 8px;
    }
    .bento-grand-total .budget-bento-val { color: #34d399; }
    .bento-capex .budget-bento-val { color: #38bdf8; }
    .bento-maint .budget-bento-val { color: #fbbf24; }
    .bento-lic .budget-bento-val { color: #818cf8; }

    .budget-bento-bar {
        width: 100%;
        height: 6px;
        border-radius: 3px;
        background: rgba(148, 163, 184, 0.14);
        display: flex;
        overflow: hidden;
        margin-bottom: 8px;
    }
    .budget-bar-capex {
        background: #38bdf8;
        height: 100%;
        transition: width 0.3s ease;
    }
    .budget-bar-opex {
        background: #fbbf24;
        height: 100%;
        transition: width 0.3s ease;
    }
    .budget-bento-footer {
        font-size: 0.72rem;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* 3. 5-Year Trajectory Ribbon */
    .budget-timeline-card {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 16px;
        padding: 16px 20px;
        position: relative;
    }
    .budget-timeline-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .budget-timeline-title {
        font-size: 0.88rem;
        font-weight: 700;
        color: #f8fafc;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .budget-timeline-scroll {
        display: flex;
        flex-direction: row;
        gap: 12px;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        touch-action: pan-x;
        overscroll-behavior-x: contain;
        scrollbar-width: none;
        padding-bottom: 4px;
        cursor: grab;
    }
    .budget-timeline-scroll:active {
        cursor: grabbing;
    }
    .budget-timeline-scroll::-webkit-scrollbar {
        display: none;
    }
    .traj-year-item {
        flex: 1 1 0;
        min-width: 190px;
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 14px;
        padding: 14px 16px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        text-decoration: none !important;
        transition: all 0.2s ease;
        position: relative;
        user-select: none;
    }
    .traj-year-item:hover {
        transform: translateY(-2px);
        border-color: rgba(56, 189, 248, 0.4);
        background: rgba(15, 23, 42, 0.85);
    }
    .traj-year-item.active {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.12) 0%, rgba(15, 23, 42, 0.95) 100%);
        border: 1px solid rgba(16, 185, 129, 0.45);
        box-shadow: 0 4px 18px rgba(16, 185, 129, 0.2);
    }
    .traj-year-item.active::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: #10b981;
        border-radius: 14px 14px 0 0;
    }
    .traj-year-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
    }
    .traj-year-num {
        font-family: 'JetBrains Mono', monospace;
        font-size: 1.05rem;
        font-weight: 700;
        color: #f8fafc;
    }
    .traj-status-pill {
        font-size: 0.66rem;
        font-weight: 700;
        font-family: 'JetBrains Mono', monospace;
        padding: 2px 7px;
        border-radius: 9999px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .traj-pill-active {
        background: rgba(16, 185, 129, 0.2);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.4);
    }
    .traj-pill-inactive {
        background: rgba(148, 163, 184, 0.1);
        color: #94a3b8;
        border: 1px solid rgba(148, 163, 184, 0.2);
    }
    .traj-year-amt {
        font-size: 1.05rem;
        font-weight: 800;
        font-family: 'JetBrains Mono', monospace;
        color: #38bdf8;
        margin-bottom: 8px;
    }
    .traj-year-item.active .traj-year-amt {
        color: #34d399;
    }
    .traj-micro-bar {
        display: flex;
        height: 5px;
        border-radius: 2.5px;
        overflow: hidden;
        background: rgba(148, 163, 184, 0.12);
        margin-bottom: 8px;
    }
    .traj-bar-item { height: 100%; }
    .traj-year-meta {
        font-size: 0.72rem;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* 4. Section Containers */
    .budget-section-card {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 16px;
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .budget-sec-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.1);
        flex-wrap: wrap;
        gap: 8px;
    }
    .budget-sec-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #f8fafc;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 0;
    }
    .budget-sec-icon {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.82rem;
    }
    .sec-dept-icon { background: rgba(245, 158, 11, 0.12); color: #fbbf24; }
    .sec-radar-icon { background: rgba(239, 68, 68, 0.12); color: #f87171; }

    /* Tables */
    .budget-table {
        width: 100%;
        border-collapse: collapse;
    }
    .budget-table th {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
        padding: 10px 12px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.12);
        background: rgba(15, 23, 42, 0.5);
    }
    .budget-table td {
        padding: 11px 12px;
        font-size: 0.84rem;
        color: #cbd5e1;
        border-bottom: 1px solid rgba(148, 163, 184, 0.08);
        vertical-align: middle;
    }
    .budget-table tr:hover td {
        background: rgba(148, 163, 184, 0.03);
    }

    /* Interactive Filter Controls */
    .radar-filter-bar {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 14px;
    }
    .radar-search-box {
        position: relative;
        width: 100%;
    }
    .radar-search-box input {
        width: 100%;
        height: 38px;
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 10px;
        color: #f8fafc;
        font-size: 0.82rem;
        padding-left: 36px;
        padding-right: 32px;
        outline: none;
        transition: border-color 0.15s ease;
    }
    .radar-search-box input:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.2);
    }
    .radar-search-box i.search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.8rem;
    }
    .radar-clear-btn {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        cursor: pointer;
        padding: 4px;
    }
    .radar-pills-row {
        display: flex;
        align-items: center;
        gap: 6px;
        overflow-x: auto;
        padding-bottom: 4px;
        scrollbar-width: none;
    }
    .radar-pills-row::-webkit-scrollbar {
        display: none;
    }
    .radar-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.74rem;
        font-weight: 600;
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(148, 163, 184, 0.18);
        color: #94a3b8 !important;
        text-decoration: none !important;
        white-space: nowrap;
        flex-shrink: 0;
        transition: all 0.15s ease;
    }
    .radar-pill:hover {
        color: #f8fafc !important;
        border-color: rgba(56, 189, 248, 0.4);
    }
    .radar-pill.active {
        background: rgba(56, 189, 248, 0.15);
        border-color: rgba(56, 189, 248, 0.5);
        color: #38bdf8 !important;
    }

    /* Tag Copy Chip */
    .asset-tag-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(148, 163, 184, 0.2);
        padding: 3px 8px;
        border-radius: 6px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.75rem;
        color: #38bdf8;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .asset-tag-chip:hover {
        border-color: rgba(56, 189, 248, 0.5);
        background: rgba(56, 189, 248, 0.1);
    }

    /* Category Badges */
    .radar-cat-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 600;
        font-family: 'JetBrains Mono', monospace;
        background: rgba(148, 163, 184, 0.1);
        border: 1px solid rgba(148, 163, 184, 0.2);
        color: #cbd5e1;
    }

    /* Mobile Cards */
    .budget-m-card {
        background: rgba(15, 23, 42, 0.7);
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 10px;
    }
    .budget-m-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
    }
    .budget-m-name {
        font-weight: 700;
        font-size: 0.88rem;
        color: #f8fafc;
        margin-bottom: 6px;
        word-break: break-word;
    }
    .budget-m-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.75rem;
        color: #94a3b8;
    }

    /* Responsive Mobile Overrides */
    @media (max-width: 767.98px) {
        .budget-header-card {
            padding: 14px 16px;
            border-radius: 14px;
        }
        .budget-header-wrapper {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }
        .budget-header-left {
            align-items: flex-start !important;
            gap: 12px !important;
        }
        .budget-header-icon {
            width: 40px !important;
            height: 40px !important;
            font-size: 1.15rem !important;
            margin-top: 2px !important;
        }
        .budget-title-text {
            font-size: 1.15rem !important;
            line-height: 1.25 !important;
        }
        .budget-subtitle-text {
            font-size: 0.78rem !important;
            line-height: 1.35 !important;
        }
        .budget-header-controls {
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
        }
        .fiscal-pills-bar {
            width: 100%;
            justify-content: space-between;
        }
        .fiscal-pill {
            flex: 1 1 0;
            padding: 6px 0;
            font-size: 0.76rem;
        }
        .btn-export-budget {
            width: 100%;
        }

        /* 2x2 Bento Mobile */
        .budget-bento-row {
            margin-left: -4px !important;
            margin-right: -4px !important;
        }
        .budget-bento-col {
            padding-left: 4px !important;
            padding-right: 4px !important;
            margin-bottom: 8px !important;
        }
        .budget-bento-card {
            padding: 10px 12px !important;
            border-radius: 12px !important;
        }
        .budget-bento-label {
            font-size: 0.64rem !important;
        }
        .budget-bento-icon {
            width: 28px !important;
            height: 28px !important;
            font-size: 0.8rem !important;
        }
        .budget-bento-val {
            font-size: 1.25rem !important;
        }
        .budget-bento-footer {
            font-size: 0.65rem !important;
        }

        /* Trajectory Mobile */
        .traj-year-item {
            min-width: 160px;
            padding: 12px 14px;
        }
        .traj-year-num {
            font-size: 0.95rem;
        }
        .traj-year-amt {
            font-size: 0.95rem;
        }

        /* Section Cards Mobile */
        .budget-section-card {
            padding: 14px 14px !important;
            border-radius: 14px !important;
        }
    }
</style>

@php
    if (!function_exists('formatBudgetString')) {
        function formatBudgetString($amount) {
            $isEn = app()->getLocale() === 'en';
            if ($isEn) {
                if ($amount >= 1000000000) {
                    return number_format($amount / 1000000000, 2, '.', ',') . ' Billion';
                }
                return number_format($amount / 1000000, 1, '.', ',') . ' M';
            } else {
                if ($amount >= 1000000000) {
                    return number_format($amount / 1000000000, 2, ',', '.') . ' Miliar';
                }
                return number_format($amount / 1000000, 1, ',', '.') . ' Jt';
            }
        }
    }
@endphp

<div class="budget-workbench">
    <!-- 1. Command Header Banner -->
    <div class="budget-header-card">
        <div class="budget-header-wrapper">
            <div class="budget-header-left">
                <div class="budget-header-icon">
                    <i class="fas fa-calculator"></i>
                </div>
                <div style="min-width: 0; flex: 1 1 auto;">
                    <div class="budget-title-row">
                        <h4 class="mb-0 font-weight-bold theme-text budget-title-text">
                            {{ __('messages.budget_planner_title') }}
                        </h4>
                        <span class="budget-tagline-badge">
                            <span class="budget-pulse-dot"></span>
                            {{ __('messages.budget_tagline') }}
                        </span>
                    </div>
                    <p class="budget-subtitle-text">
                        {{ __('messages.budget_planner_desc') }}
                    </p>
                </div>
            </div>

            <div class="budget-header-controls">
                <div class="fiscal-pills-bar">
                    @foreach([2026, 2027, 2028, 2029, 2030] as $yr)
                        <a href="{{ route('budget-planner.index', ['year' => $yr]) }}"
                           class="fiscal-pill {{ $selectedYear == $yr ? 'active' : '' }}">
                            {{ $yr }}
                        </a>
                    @endforeach
                </div>
                <a href="{{ route('budget-planner.export', ['year' => $selectedYear]) }}" class="btn-export-budget" title="{{ __('messages.export_budget') }} CSV">
                    <i class="fas fa-file-csv"></i>
                    <span>{{ __('messages.export_budget') }} {{ $selectedYear }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. Bento KPI Matrix (2x2 on mobile, 4 in a row on desktop) -->
    <div class="row g-2 g-md-3 budget-bento-row">
        <!-- Total Projected Budget -->
        <div class="col-6 col-xl-3 budget-bento-col mb-2 mb-xl-0">
            <div class="budget-bento-card bento-grand-total">
                <div>
                    <div class="budget-bento-top">
                        <span class="budget-bento-label">{{ __('messages.total_projected_budget') }}</span>
                        <div class="budget-bento-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                    <div class="budget-bento-val">Rp {{ number_format($current['grand_total'], 0, ',', '.') }}</div>
                    <!-- CapEx vs OpEx Distribution Bar -->
                    <div class="budget-bento-bar" title="CapEx: {{ $financialRatios['capex_percent'] }}% | OpEx: {{ $financialRatios['opex_percent'] }}%">
                        <div class="budget-bar-capex" style="width: {{ $financialRatios['capex_percent'] }}%;"></div>
                        <div class="budget-bar-opex" style="width: {{ $financialRatios['opex_percent'] }}%;"></div>
                    </div>
                </div>
                <div class="budget-bento-footer">
                    <span class="text-info font-weight-bold">{{ $financialRatios['capex_percent'] }}% CapEx</span>
                    <span>·</span>
                    <span class="text-warning font-weight-bold">{{ $financialRatios['opex_percent'] }}% OpEx</span>
                </div>
            </div>
        </div>

        <!-- Unit Replacement (CapEx) -->
        <div class="col-6 col-xl-3 budget-bento-col mb-2 mb-xl-0">
            <div class="budget-bento-card bento-capex">
                <div>
                    <div class="budget-bento-top">
                        <span class="budget-bento-label">{{ __('messages.unit_replacement_budget') }}</span>
                        <div class="budget-bento-icon">
                            <i class="fas fa-laptop"></i>
                        </div>
                    </div>
                    <div class="budget-bento-val">Rp {{ number_format($current['replacement_cost'], 0, ',', '.') }}</div>
                </div>
                <div class="budget-bento-footer">
                    <i class="fas fa-microchip text-info"></i>
                    <span>{{ $current['replacement_count'] }} {{ __('messages.replacement_units') }}</span>
                </div>
            </div>
        </div>

        <!-- Maintenance & Service (OpEx) -->
        <div class="col-6 col-xl-3 budget-bento-col mb-2 mb-xl-0">
            <div class="budget-bento-card bento-maint">
                <div>
                    <div class="budget-bento-top">
                        <span class="budget-bento-label">{{ __('messages.maintenance_service_budget') }}</span>
                        <div class="budget-bento-icon">
                            <i class="fas fa-tools"></i>
                        </div>
                    </div>
                    <div class="budget-bento-val">Rp {{ number_format($current['maintenance_cost'], 0, ',', '.') }}</div>
                </div>
                <div class="budget-bento-footer">
                    <i class="fas fa-wrench text-warning"></i>
                    <span>{{ __('messages.service_repairs') }}</span>
                </div>
            </div>
        </div>

        <!-- Software License Renewals (OpEx) -->
        <div class="col-6 col-xl-3 budget-bento-col mb-2 mb-xl-0">
            <div class="budget-bento-card bento-lic">
                <div>
                    <div class="budget-bento-top">
                        <span class="budget-bento-label">{{ __('messages.software_license_renewals') }}</span>
                        <div class="budget-bento-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                    </div>
                    <div class="budget-bento-val">Rp {{ number_format($current['license_cost'], 0, ',', '.') }}</div>
                </div>
                <div class="budget-bento-footer">
                    <i class="fas fa-shield-alt text-indigo"></i>
                    <span>{{ __('messages.os_office_antivirus') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. 5-Year Trajectory Timeline Ribbon -->
    <div class="budget-timeline-card">
        <div class="budget-timeline-head">
            <span class="budget-timeline-title">
                <i class="fas fa-chart-line text-info"></i>
                <span>{{ __('messages.five_year_timeline') }}</span>
            </span>
            <span class="text-muted d-none d-md-inline" style="font-size: 0.74rem;">
                <i class="fas fa-info-circle mr-1"></i> {{ __('messages.selected_fiscal_year') }}: <strong>{{ $selectedYear }}</strong>
            </span>
        </div>

        <div class="budget-timeline-scroll" id="timeline-scroll-strip">
            @foreach($projections as $yr => $proj)
                @php
                    $isActive = ($selectedYear == $yr);
                    $tot = max(1, $proj['grand_total']);
                    $capRatio = round(($proj['replacement_cost'] / $tot) * 100);
                    $maintRatio = round(($proj['maintenance_cost'] / $tot) * 100);
                    $licRatio = 100 - ($capRatio + $maintRatio);
                @endphp
                <a href="{{ route('budget-planner.index', ['year' => $yr]) }}"
                   class="traj-year-item {{ $isActive ? 'active' : '' }}">
                    <div>
                        <div class="traj-year-header">
                            <span class="traj-year-num">{{ $yr }}</span>
                            @if($isActive)
                                <span class="traj-status-pill traj-pill-active">{{ __('messages.selected_fiscal_year') }}</span>
                            @else
                                <span class="traj-status-pill traj-pill-inactive">{{ __('messages.all') }}</span>
                            @endif
                        </div>
                        <div class="traj-year-amt">
                            Rp {{ formatBudgetString($proj['grand_total']) }}
                        </div>
                        <!-- Micro Proportional Bar: CapEx, Maint, Lic -->
                        <div class="traj-micro-bar" title="CapEx: {{ $capRatio }}% | Maint: {{ $maintRatio }}% | Lic: {{ $licRatio }}%">
                            <div class="traj-bar-item" style="width: {{ $capRatio }}%; background: #38bdf8;"></div>
                            <div class="traj-bar-item" style="width: {{ $maintRatio }}%; background: #fbbf24;"></div>
                            <div class="traj-bar-item" style="width: {{ $licRatio }}%; background: #818cf8;"></div>
                        </div>
                    </div>
                    <div class="traj-year-meta">
                        <span><i class="fas fa-microchip mr-1"></i>{{ $proj['replacement_count'] }} {{ __('messages.replacement_units') }}</span>
                        <span class="font-weight-bold" style="font-family: 'JetBrains Mono', monospace;">{{ $capRatio }}% CapEx</span>
                    </div>
                </a>
            @endforeach
            <div style="flex: 0 0 16px; width: 16px; height: 1px; pointer-events: none;"></div>
        </div>
    </div>

    <!-- 4. Dual View: Department Allocation & Scheduled Replacements -->
    <div class="row">
        <!-- Department Budget Allocation -->
        <div class="col-lg-5 mb-3 mb-lg-0">
            <div class="budget-section-card">
                <div class="budget-sec-header">
                    <h5 class="budget-sec-title">
                        <span class="budget-sec-icon sec-dept-icon">
                            <i class="fas fa-sitemap"></i>
                        </span>
                        <span>{{ __('messages.dept_allocation_breakdown') }}</span>
                    </h5>
                    <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 8px; font-size: 0.72rem; font-family: 'JetBrains Mono', monospace;">
                        {{ count($deptBreakdown) }} {{ __('messages.dept_short') }}
                    </span>
                </div>

                <!-- Desktop Table View -->
                <div class="d-none d-md-block table-responsive">
                    <table class="budget-table">
                        <thead>
                            <tr>
                                <th>{{ __('messages.department') }}</th>
                                <th class="text-center" width="70">{{ __('messages.asset') }}</th>
                                <th>{{ __('messages.estimated_cost') }}</th>
                                <th class="text-right" width="80">{{ __('messages.ratio') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deptBreakdown as $dept)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold theme-text" style="font-size: 0.86rem;">
                                            {{ $dept['department_name'] }}
                                        </div>
                                        <small class="text-muted">{{ $dept['employee_count'] }} {{ __('messages.employee') }}</small>
                                    </td>
                                    <td class="text-center" style="font-family: 'JetBrains Mono', monospace;">
                                        <span class="badge" style="background: rgba(148, 163, 184, 0.12); color: #cbd5e1;">
                                            {{ $dept['asset_count'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-info" style="font-family: 'JetBrains Mono', monospace; font-size: 0.84rem;">
                                            Rp {{ number_format($dept['allocated_budget'], 0, ',', '.') }}
                                        </div>
                                        <div style="width: 100%; height: 3px; background: rgba(148, 163, 184, 0.12); border-radius: 2px; margin-top: 4px; overflow: hidden;">
                                            <div style="width: {{ $dept['percentage'] }}%; height: 100%; background: #38bdf8; border-radius: 2px;"></div>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <span class="badge" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8; border-radius: 6px; font-family: 'JetBrains Mono', monospace; font-size: 0.74rem;">
                                            {{ $dept['percentage'] }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">{{ __('messages.no_data') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Feed Cards -->
                <div class="d-md-none">
                    @forelse($deptBreakdown as $dept)
                        <div class="budget-m-card">
                            <div class="budget-m-top">
                                <span class="font-weight-bold theme-text" style="font-size: 0.86rem;">{{ $dept['department_name'] }}</span>
                                <span class="badge" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8; font-family: 'JetBrains Mono', monospace;">{{ $dept['percentage'] }}%</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted text-xs">{{ $dept['employee_count'] }} {{ __('messages.employee') }} · {{ $dept['asset_count'] }} {{ __('messages.asset') }}</span>
                                <span class="font-weight-bold text-info" style="font-family: 'JetBrains Mono', monospace; font-size: 0.86rem;">
                                    Rp {{ number_format($dept['allocated_budget'], 0, ',', '.') }}
                                </span>
                            </div>
                            <div style="width: 100%; height: 3px; background: rgba(148, 163, 184, 0.12); border-radius: 2px; overflow: hidden;">
                                <div style="width: {{ $dept['percentage'] }}%; height: 100%; background: #38bdf8; border-radius: 2px;"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">{{ __('messages.no_data') }}</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Scheduled Asset Replacements (Hardware Lifecycle Radar) -->
        <div class="col-lg-7">
            <div class="budget-section-card">
                <div class="budget-sec-header">
                    <h5 class="budget-sec-title">
                        <span class="budget-sec-icon sec-radar-icon">
                            <i class="fas fa-sync-alt"></i>
                        </span>
                        <span>{{ __('messages.scheduled_replacements') }}</span>
                    </h5>
                    <div class="d-flex align-items-center" style="gap: 6px;">
                        <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 8px; font-size: 0.72rem; font-family: 'JetBrains Mono', monospace;">
                            {{ count($replacementAssets) }} {{ __('messages.unit') }}
                        </span>
                    </div>
                </div>

                <!-- Tactical Filter Controls -->
                <div class="radar-filter-bar">
                    <!-- Search Input -->
                    <form action="{{ route('budget-planner.index') }}" method="GET" id="radar-search-form" class="m-0">
                        <input type="hidden" name="year" value="{{ $selectedYear }}">
                        @if($selectedCategory !== 'all')
                            <input type="hidden" name="category" value="{{ $selectedCategory }}">
                        @endif
                        <div class="radar-search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="search" placeholder="{{ __('messages.search_replacement_asset') }}" value="{{ $search }}" autocomplete="off">
                            @if($search)
                                <a href="{{ route('budget-planner.index', ['year' => $selectedYear, 'category' => $selectedCategory]) }}" class="radar-clear-btn" title="{{ __('messages.clear') }}">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </form>

                    <!-- Category Pills -->
                    @if(count($replacementCategories) > 0)
                    <div class="radar-pills-row">
                        <a href="{{ route('budget-planner.index', ['year' => $selectedYear, 'category' => 'all', 'search' => $search]) }}"
                           class="radar-pill {{ $selectedCategory === 'all' ? 'active' : '' }}">
                            <i class="fas fa-layer-group text-xs"></i>
                            <span>{{ __('messages.all') }}</span>
                        </a>
                        @foreach($replacementCategories as $cat)
                            <a href="{{ route('budget-planner.index', ['year' => $selectedYear, 'category' => $cat, 'search' => $search]) }}"
                               class="radar-pill {{ $selectedCategory === $cat ? 'active' : '' }}">
                                <span>{{ $cat }}</span>
                            </a>
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Desktop Table View -->
                <div class="d-none d-md-block table-responsive">
                    <table class="budget-table">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">{{ __('messages.no') }}</th>
                                <th>{{ __('messages.asset_name') }}</th>
                                <th>{{ __('messages.category') }}</th>
                                <th>{{ __('messages.department') }}</th>
                                <th class="text-right">{{ __('messages.estimated_unit_price') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($replacementAssets as $asset)
                                <tr>
                                    <td class="text-center text-muted" style="font-family: 'JetBrains Mono', monospace; font-size: 0.78rem;">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td>
                                        <div class="font-weight-bold theme-text" style="font-size: 0.88rem;">
                                            {{ $asset['asset_name'] }}
                                        </div>
                                        <div class="mt-1">
                                            <span class="asset-tag-chip copy-tag-btn" data-tag="{{ $asset['asset_tag'] }}" title="{{ __('messages.copy_asset_tag') }}">
                                                <i class="fas fa-tag text-xs"></i>
                                                <span>{{ $asset['asset_tag'] }}</span>
                                                <i class="far fa-copy text-xs ml-1 opacity-60"></i>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="radar-cat-badge">
                                            {{ $asset['category'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="theme-text" style="font-size: 0.82rem;">
                                            {{ $asset['department'] }}
                                        </div>
                                    </td>
                                    <td class="text-right font-weight-bold text-danger" style="font-family: 'JetBrains Mono', monospace; font-size: 0.88rem;">
                                        Rp {{ number_format($asset['estimated_cost'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        {{ __('messages.no_replacement_assets_year', ['year' => $selectedYear]) }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Feed Cards -->
                <div class="d-md-none">
                    @forelse($replacementAssets as $asset)
                        <div class="budget-m-card">
                            <div class="budget-m-top">
                                <div class="d-flex align-items-center" style="gap: 6px;">
                                    <span class="text-muted font-weight-bold" style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem;">
                                        #{{ sprintf('%02d', $loop->iteration) }}
                                    </span>
                                    <span class="radar-cat-badge">{{ $asset['category'] }}</span>
                                </div>
                                <span class="font-weight-bold text-danger" style="font-family: 'JetBrains Mono', monospace; font-size: 0.88rem;">
                                    Rp {{ number_format($asset['estimated_cost'], 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="budget-m-name">
                                {{ $asset['asset_name'] }}
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-1" style="border-top: 1px solid rgba(148, 163, 184, 0.08);">
                                <span class="asset-tag-chip copy-tag-btn" data-tag="{{ $asset['asset_tag'] }}" title="{{ __('messages.copy_asset_tag') }}">
                                    <i class="fas fa-tag text-xs"></i>
                                    <span>{{ $asset['asset_tag'] }}</span>
                                    <i class="far fa-copy text-xs ml-1"></i>
                                </span>
                                <span class="text-muted text-xs">{{ $asset['department'] }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            {{ __('messages.no_replacement_assets_year', ['year' => $selectedYear]) }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Cross-browser clipboard helper
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
    // 1-Click Copy for Asset Tag
    $(document).on('click', '.copy-tag-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var tag = $(this).data('tag');
        var btn = $(this);
        var icon = btn.find('i.far, i.fa-copy');

        if (!tag) return;

        copyTextToClipboard(tag).then(function() {
            var origClass = icon.attr('class');
            icon.removeClass('far fa-copy').addClass('fas fa-check text-success');

            setTimeout(function() {
                icon.attr('class', origClass);
            }, 1600);

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1400,
                    icon: 'success',
                    title: "{{ __('messages.asset_tag_copied') }} (" + tag + ")",
                    background: 'rgba(15, 23, 42, 0.95)',
                    color: '#f8fafc'
                });
            }
        });
    });

    // Horizontal Drag-to-Scroll & Auto-Align for 5-Year Trajectory
    (function() {
        var strip = document.getElementById('timeline-scroll-strip');
        if (!strip) return;

        var isDown = false;
        var startX = 0;
        var scrollLeft = 0;
        var hasMoved = false;

        // Auto-scroll active year card into view
        var activeItem = strip.querySelector('.traj-year-item.active');
        if (activeItem) {
            var itemLeft = activeItem.offsetLeft;
            var itemWidth = activeItem.offsetWidth;
            var containerWidth = strip.clientWidth;
            if (itemLeft + itemWidth > containerWidth || itemLeft < strip.scrollLeft) {
                strip.scrollLeft = Math.max(0, itemLeft - 16);
            }
        }

        strip.addEventListener('mousedown', function(e) {
            if (e.button !== 0) return;
            isDown = true;
            hasMoved = false;
            startX = e.pageX - strip.offsetLeft;
            scrollLeft = strip.scrollLeft;
            strip.style.cursor = 'grabbing';
            strip.style.userSelect = 'none';
        });

        strip.addEventListener('mouseleave', function() {
            if (!isDown) return;
            isDown = false;
            strip.style.cursor = '';
            strip.style.userSelect = '';
        });

        strip.addEventListener('mouseup', function() {
            if (!isDown) return;
            isDown = false;
            strip.style.cursor = '';
            strip.style.userSelect = '';
        });

        strip.addEventListener('mousemove', function(e) {
            if (!isDown) return;
            var x = e.pageX - strip.offsetLeft;
            var walk = (x - startX);
            if (Math.abs(walk) > 5) {
                hasMoved = true;
                e.preventDefault();
                strip.scrollLeft = scrollLeft - walk;
            }
        });

        strip.addEventListener('click', function(e) {
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