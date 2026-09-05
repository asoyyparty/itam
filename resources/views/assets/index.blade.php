@extends('layouts.admin')

@section('title', __('messages.manage') . ' ' . __('messages.it_assets'))

@section('content')
<style>
    /* â”€â”€ Hallmark Master Header Banner â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
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
        background: rgba(15, 23, 42, 0.35) !important;
        color: var(--color-ink-0) !important;
        font-size: 0.84rem !important;
        padding: 0 12px !important;
        transition: all 0.2s ease !important;
        flex: 1 1 130px;
        min-width: 120px;
        max-width: 170px;
    }
    body.light-mode .custom-filter-select {
        background: #f8fafc !important;
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
        .asset-filter-container {
            display: flex !important;
            flex-direction: column !important;
            width: 100% !important;
            gap: 12px !important;
        }
        .asset-filter-container > * {
            width: 100% !important;
            max-width: 100% !important;
            margin-bottom: 12px !important;
        }
        .asset-filter-container > *:last-child {
            margin-bottom: 0 !important;
        }
        .custom-filter-select {
            width: 100% !important;
            max-width: 100% !important;
            height: 44px !important;
        }
        .asset-filter-container .sleek-search-wrapper {
            width: 100% !important;
            max-width: 100% !important;
            margin-bottom: 0 !important;
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
        .custom-filter-select {
            max-width: 100% !important;
            flex: 0 0 auto !important;
            width: 100% !important;
        }
        .sleek-search-wrapper {
            width: 100% !important;
            flex: 0 0 auto !important;
        }
    }

    .status-btn::after {
        display: none !important;
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
                {{ number_format($totalAssetsCount ?? $assets->total()) }} RECORDED
            </span>
            @can('action_manage_assets')
            <button type="button" class="btn btn-sm btn-action-secondary text-nowrap" data-toggle="modal" data-target="#importModal" title="{{ __('messages.import') }}">
                <i class="fas fa-file-import mr-1"></i> {{ __('messages.import') }}
            </button>
            @endcan
            <a href="{{ route('assets.export', request()->query()) }}" class="btn btn-sm btn-action-secondary text-nowrap" title="{{ __('messages.export') }}">
                <i class="fas fa-file-export mr-1"></i> {{ __('messages.export') }}
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
        <form action="{{ route('assets.index') }}" method="GET" style="width: 100%;">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="d-flex align-items-center flex-wrap gap-2 asset-filter-container">
                <!-- Year Filter -->
                <select name="date_received" class="form-control custom-filter-select" onchange="this.form.submit()" title="{{ __('messages.filter') }}">
                    <option value="">{{ __('messages.all_year') ?? 'Semua Tahun' }}</option>
                    @foreach($years as $yr)
                        <option value="{{ $yr }}" {{ (request('date_received') == $yr || request('year') == $yr) ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>

                <!-- Category Filter -->
                <select name="category_id" class="form-control custom-filter-select" onchange="this.form.submit()">
                    <option value="">{{ __('messages.all_category') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>

                <!-- Brand Filter -->
                <select name="brand_id" class="form-control custom-filter-select" onchange="this.form.submit()">
                    <option value="">{{ __('messages.all_brand') }}</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                    @endforeach
                </select>

                <!-- Location Filter -->
                <select name="location_id" class="form-control custom-filter-select" onchange="this.form.submit()">
                    <option value="">{{ __('messages.all_location') }}</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>

                <!-- Search Input -->
                <div class="sleek-search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" class="form-control theme-input sleek-search-input" placeholder="{{ __('messages.search_asset') }} (Tag, Serial, Nama)..." value="{{ request('search') }}" autocomplete="off">
                    @if(request('search') || request('category_id') || request('brand_id') || request('location_id') || request('date_received') || request('year'))
                        <a href="{{ route('assets.index', request()->only('status')) }}" class="clear-search-btn" title="{{ __('messages.clear') }}">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="theme-scroll-container table-responsive">
            <table class="table table-striped table-hover m-0 theme-table division-table asset-table">
                <thead>
                    <tr style="font-family: var(--font-mono); font-size: 0.72rem; letter-spacing: 0.08em; text-transform: uppercase;">
                        <th width="50" class="pl-4">{{ __('messages.no') }}</th>
                        <th>{{ __('messages.asset_tag') }}</th>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.category') }}</th>
                        <th>{{ __('messages.received') }}</th>
                        <th>{{ __('messages.location') }}</th>
                        <th>{{ __('messages.user') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.health') }}</th>
                        <th width="100" class="text-center pr-4">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                    <tr>
                        <td class="theme-text pl-4 text-muted" style="font-family: var(--font-mono); font-size: 0.82rem;">
                            {{ (method_exists($assets, 'firstItem') && $assets->firstItem() ? $assets->firstItem() + $loop->index : $loop->iteration) }}
                        </td>
                        <td>
                            <a href="{{ route('assets.show', $asset) }}" class="asset-tag-pill" title="Lihat Detail Aset">
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
                                <a href="{{ route('predictive-health.index') }}" class="badge {{ $asset->health_data['badge_class'] }} px-2 py-1.5" style="border-radius: 8px; font-family: var(--font-mono); font-size: 0.74rem;" title="Skor Kesehatan Prediktif">
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
                        <td colspan="10" class="text-center py-5">
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
                            {{ __('messages.select_file_hint') }} (Maks. 10MB)
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
        $('#dropzoneLabel').text(fileName);
        $('#dropzoneHint').text((input.files[0].size / 1024 / 1024).toFixed(2) + ' MB â€” Siap diunggah');
        $('#excelDropzone').css('border-color', '#10b981').css('background', 'rgba(16, 185, 129, 0.08)');
    }
}

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
                        window.showHallmarkToast('success', 'Status Berhasil Diperbarui', 'Status aset telah diubah menjadi ' + (labelMap[newStatus] || newStatus));
                    }
                }
            },
            error: function(xhr) {
                badge.html(originalHtml);
                if (window.showHallmarkToast) {
                    window.showHallmarkToast('error', 'Gagal Memperbarui', 'Terjadi kesalahan saat memperbarui status aset.');
                } else {
                    Swal.fire({ icon: 'error', text: 'Error updating status.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, background: '#0f172a', color: '#f8fafc' });
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
});
</script>
@endpush
@endsection

