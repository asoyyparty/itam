@extends('layouts.admin')

@section('title', __('messages.maintenance'))

@section('content')
@php
    $totalCount = count($maintenances);
    $ongoingCount = $maintenances->where('status', 'Ongoing')->count();
    $completedCount = $maintenances->where('status', 'Completed')->count();
    $cancelledCount = $maintenances->where('status', 'Cancelled')->count();
@endphp

<style>
    /* Hallmark Â· pre-emit critique: P5 H5 E5 S5 R5 V5 */
    /* Hallmark Maintenance Header Banner */
    .master-header-banner {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(30, 41, 59, 0.85));
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 20px;
        padding: 18px 24px;
        margin-top: 0 !important;
        margin-bottom: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }
    body.light-mode .master-header-banner {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(241, 245, 249, 0.85));
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
    }

    /* Quick Status Ribbon (Bento) */
    .quick-status-ribbon {
        width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        margin-bottom: 18px;
    }
    .status-ribbon-scroll {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        width: 100% !important;
        padding-bottom: 6px !important;
        scrollbar-width: none !important;
    }
    .status-ribbon-scroll::-webkit-scrollbar {
        display: none !important;
    }
    .status-ribbon-pill {
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        padding: 8px 16px !important;
        border-radius: 999px !important;
        font-size: 0.84rem !important;
        font-weight: 600 !important;
        background: rgba(15, 23, 42, 0.35) !important;
        border: 1px solid rgba(148, 163, 184, 0.18) !important;
        color: var(--color-ink-1) !important;
        text-decoration: none !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        white-space: nowrap !important;
        flex-shrink: 0 !important;
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
        padding: 2px 8px;
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

    /* Hallmark Master Table Card */
    .master-table-card {
        border-radius: 20px !important;
        overflow: hidden !important;
        border: 1px solid rgba(148, 163, 184, 0.14) !important;
        background: rgba(15, 23, 42, 0.95) !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    }
    body.light-mode .master-table-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
    }

    /* Filter Toolbar Container */
    .maintenance-filter-container {
        display: flex !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 12px !important;
        width: 100% !important;
    }
    .custom-filter-select {
        height: 44px !important;
        border-radius: 12px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background-color: rgba(15, 23, 42, 0.3) !important;
        color: var(--color-ink-0) !important;
        font-size: 0.88rem !important;
        padding: 0 32px 0 14px !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        background-repeat: no-repeat !important;
        background-position: right 12px center !important;
        background-size: 14px 14px !important;
        transition: all 0.2s ease !important;
        width: auto !important;
        min-width: 160px !important;
        max-width: 200px !important;
        flex: 0 1 180px !important;
        margin: 0 !important;
    }
    body.light-mode .custom-filter-select {
        background-color: #ffffff !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }
    .custom-filter-select:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
    }

    .sleek-search-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        min-width: 220px;
        flex: 1 1 240px;
        margin: 0 !important;
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
        height: 44px !important;
        padding-left: 38px !important;
        padding-right: 36px !important;
        border-radius: 12px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.3) !important;
        color: var(--color-ink-0) !important;
        font-size: 0.88rem !important;
        width: 100%;
        transition: all 0.2s ease;
    }
    body.light-mode .sleek-search-input {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
    }
    .sleek-search-input:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
    }

    /* Modal Styling */
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

    /* Mobile Card Styles */
    .maintenance-mobile-card {
        border-radius: 16px !important;
        background: rgba(30, 41, 59, 0.65) !important;
        border: 1px solid rgba(148, 163, 184, 0.18) !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25) !important;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    body.light-mode .maintenance-mobile-card {
        background: #ffffff !important;
        border-color: #e2e8f0 !important;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.05) !important;
    }
    .maintenance-mobile-card:hover {
        border-color: rgba(99, 102, 241, 0.4) !important;
    }
    .maintenance-asset-icon {
        width: 38px;
        height: 38px;
        min-width: 38px;
        border-radius: 10px;
        background: rgba(245, 158, 11, 0.12);
        border: 1px solid rgba(245, 158, 11, 0.28);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fbbf24;
        font-size: 0.95rem;
    }
    body.light-mode .maintenance-asset-icon {
        background: #fef3c7;
        border-color: #fde68a;
        color: #d97706;
    }
    .maintenance-recorded-badge {
        font-family: var(--font-mono);
        font-size: 0.72rem;
        padding: 4px 10px;
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.25);
        color: #fbbf24;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        white-space: nowrap;
    }

    @media (max-width: 767.98px) {
        .master-header-banner {
            padding: 14px 16px !important;
            margin-bottom: 14px !important;
        }
        .maintenance-action-buttons {
            width: 100% !important;
            display: flex !important;
            flex-wrap: nowrap !important;
            margin-top: 12px !important;
            gap: 10px !important;
        }
        .maintenance-action-buttons .btn {
            flex: 1 1 50% !important;
            text-align: center !important;
            justify-content: center !important;
            height: 42px !important;
            border-radius: 12px !important;
            font-size: 0.82rem !important;
            font-weight: 600 !important;
            display: inline-flex !important;
            align-items: center !important;
        }
        .maintenance-action-buttons .btn-add-main {
            flex: 1 1 50% !important;
        }
        .quick-status-ribbon {
            margin-bottom: 14px !important;
            width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
        }
        .status-ribbon-scroll {
            gap: 8px !important;
            padding: 2px 2px 8px 2px !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            scrollbar-width: none !important;
        }
        .status-ribbon-scroll::-webkit-scrollbar {
            display: none !important;
        }
        .status-ribbon-pill {
            padding: 7px 14px !important;
            font-size: 0.8rem !important;
            flex-shrink: 0 !important;
        }
        .card-header {
            padding: 12px 14px !important;
        }
        .maintenance-filter-container {
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
            width: 100% !important;
            gap: 10px !important;
            margin: 0 !important;
        }
        .custom-filter-select {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 44px !important;
            height: 44px !important;
            border-radius: 12px !important;
            margin: 0 !important;
        }
        .sleek-search-wrapper {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 44px !important;
            height: 44px !important;
            margin: 0 !important;
        }
        .sleek-search-input {
            height: 44px !important;
            border-radius: 12px !important;
        }
        .master-table-card .card-footer {
            flex-direction: column !important;
            gap: 12px !important;
            align-items: center !important;
            text-align: center !important;
            padding: 14px 16px !important;
        }
    }

    .status-btn::after { display: none !important; }
    .status-change-btn:hover { background-color: rgba(255, 255, 255, 0.1); }
</style>

<!-- Hallmark Master Header Banner -->
<div class="master-header-banner">
    <div class="d-flex align-items-center justify-content-between flex-wrap flex-md-nowrap" style="gap: 16px;">
        <div class="d-flex align-items-center" style="gap: 14px; min-width: 0; flex: 1;">
            <div style="width: 44px; height: 44px; border-radius: 14px; background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(217, 119, 6, 0.08)); border: 1px solid rgba(245, 158, 11, 0.35); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-size: 1.2rem; flex-shrink: 0; box-shadow: 0 0 14px rgba(245, 158, 11, 0.25);">
                <i class="fas fa-tools"></i>
            </div>
            <div style="flex: 1; min-width: 0;">
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <h5 class="mb-0 font-weight-bold theme-text text-truncate" style="font-size: 1.15rem; line-height: 1.2;">{{ __('messages.maintenance') }}</h5>
                    <span class="maintenance-recorded-badge">
                        <span class="live-dot" style="width: 6px; height: 6px; border-radius: 50%; background: #f59e0b; box-shadow: 0 0 8px #f59e0b; display: inline-block;"></span>
                        {{ $totalCount }} RECORDED
                    </span>
                </div>
                <small class="text-muted d-block" style="font-size: 0.8rem; margin-top: 2px; white-space: normal; line-height: 1.3;">{{ __('messages.maintenance_subtitle') }}</small>
            </div>
        </div>

        <div class="d-flex align-items-center flex-wrap flex-md-nowrap maintenance-action-buttons ml-auto" style="gap: 10px; flex-shrink: 0;">
            <a href="{{ route('maintenances.export', request()->query()) }}" class="btn btn-sm btn-action-secondary text-nowrap" title="{{ __('messages.export') }}">
                <i class="fas fa-file-export mr-1"></i> {{ __('messages.export') }}
            </a>
            @can('action_manage_maintenance')
            <a href="{{ route('maintenances.create') }}" class="btn btn-sm btn-action-primary text-nowrap btn-add-main" title="{{ __('messages.log_maintenance') }}">
                <i class="fas fa-plus mr-1"></i> {{ __('messages.log_maintenance') }}
            </a>
            @endcan
        </div>
    </div>
</div>

<!-- Quick Status Ribbon (Bento) -->
<div class="quick-status-ribbon">
    <div class="status-ribbon-scroll">
        <a href="{{ route('maintenances.index') }}" class="status-ribbon-pill {{ !request('status') ? 'active' : '' }}">
            <span>{{ __('messages.all') ?? 'Semua' }}</span>
            <span class="status-ribbon-count">{{ $totalCount }}</span>
        </a>
        <a href="{{ route('maintenances.index', ['status' => 'Ongoing']) }}" class="status-ribbon-pill {{ request('status') == 'Ongoing' ? 'active' : '' }}">
            <span style="width: 7px; height: 7px; border-radius: 50%; background: #f59e0b; box-shadow: 0 0 6px #f59e0b; display: inline-block;"></span>
            <span>{{ __('messages.ongoing') }}</span>
            <span class="status-ribbon-count">{{ $ongoingCount }}</span>
        </a>
        <a href="{{ route('maintenances.index', ['status' => 'Completed']) }}" class="status-ribbon-pill {{ request('status') == 'Completed' ? 'active' : '' }}">
            <span style="width: 7px; height: 7px; border-radius: 50%; background: #10b981; box-shadow: 0 0 6px #10b981; display: inline-block;"></span>
            <span>{{ __('messages.completed') }}</span>
            <span class="status-ribbon-count">{{ $completedCount }}</span>
        </a>
        <a href="{{ route('maintenances.index', ['status' => 'Cancelled']) }}" class="status-ribbon-pill {{ request('status') == 'Cancelled' ? 'active' : '' }}">
            <span style="width: 7px; height: 7px; border-radius: 50%; background: #64748b; display: inline-block;"></span>
            <span>{{ __('messages.cancelled') }}</span>
            <span class="status-ribbon-count">{{ $cancelledCount }}</span>
        </a>
    </div>
</div>

<!-- Main Table Card -->
<div class="card theme-card master-table-card">
    <div class="card-header border-0 py-3 px-3 px-md-4" style="border-bottom: 1px solid rgba(148, 163, 184, 0.12) !important;">
        <form action="{{ route('maintenances.index') }}" method="GET" style="width: 100%;">
            <div class="d-flex align-items-center flex-wrap gap-2 maintenance-filter-container">
                <select name="status" class="form-control custom-filter-select" onchange="this.form.submit()">
                    <option value="">{{ __('messages.all_status') }}</option>
                    <option value="Ongoing" {{ request('status') == 'Ongoing' ? 'selected' : '' }}>{{ __('messages.ongoing') }}</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>{{ __('messages.completed') }}</option>
                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>{{ __('messages.cancelled') }}</option>
                </select>

                <div class="sleek-search-wrapper flex-grow-1" style="min-width: 220px;">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" class="form-control theme-input sleek-search-input" placeholder="{{ __('messages.search') }} (Aset, Tag, Deskripsi)..." value="{{ request('search') }}" autocomplete="off">
                    @if(request()->anyFilled(['search', 'status']))
                        <a href="{{ route('maintenances.index') }}" class="clear-search-btn" title="{{ __('messages.clear') }}">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <!-- Desktop Table View -->
        <div class="theme-scroll-container table-responsive d-none d-md-block">
            <table class="table table-striped table-hover m-0 theme-table division-table maintenance-table">
                <thead>
                    <tr style="font-family: var(--font-mono); font-size: 0.72rem; letter-spacing: 0.08em; text-transform: uppercase;">
                        <th width="60" class="pl-4">{{ __('messages.no') }}</th>
                        <th style="min-width: 200px;">{{ __('messages.asset') }}</th>
                        <th style="min-width: 140px;">{{ __('messages.type') }}</th>
                        <th style="min-width: 220px;">{{ __('messages.description') }}</th>
                        <th style="min-width: 170px;">{{ __('messages.dates') }}</th>
                        <th style="min-width: 130px;">{{ __('messages.status') }}</th>
                        <th width="140" class="text-center">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($maintenances as $maintenance)
                    <tr>
                        <td class="pl-4 theme-text font-mono" style="font-size: 0.82rem;">
                            {{ (method_exists($maintenances, 'firstItem') && $maintenances->firstItem() ? $maintenances->firstItem() + $loop->index : $loop->iteration) }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center" style="gap: 10px;">
                                <div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-size: 0.85rem; flex-shrink: 0;">
                                    <i class="fas fa-desktop"></i>
                                </div>
                                <div>
                                    <a href="{{ $maintenance->asset_id ? route('assets.show', $maintenance->asset_id) : '#' }}" class="font-weight-bold theme-text d-block text-decoration-none hover-highlight" style="font-size: 0.88rem; line-height: 1.2;">{{ $maintenance->asset->name ?? '-' }}</a>
                                    <span style="font-family: var(--font-mono); font-size: 0.75rem; color: #38bdf8; font-weight: 600;">{{ $maintenance->asset->asset_tag ?? '-' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($maintenance->type == 'Routine')
                                <span class="badge" style="background: rgba(59, 130, 246, 0.12); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.25); border-radius: 6px; padding: 4px 8px; font-weight: 600;">
                                    <i class="fas fa-sync-alt mr-1"></i> {{ __('messages.routine_maintenance') }}
                                </span>
                            @elseif($maintenance->type == 'Repair')
                                <span class="badge" style="background: rgba(239, 68, 68, 0.12); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.25); border-radius: 6px; padding: 4px 8px; font-weight: 600;">
                                    <i class="fas fa-wrench mr-1"></i> {{ __('messages.repair_broken') }}
                                </span>
                            @else
                                <span class="badge" style="background: rgba(168, 85, 247, 0.12); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.25); border-radius: 6px; padding: 4px 8px; font-weight: 600;">
                                    <i class="fas fa-microchip mr-1"></i> {{ __('messages.hardware_upgrade') }}
                                </span>
                            @endif
                        </td>
                        <td class="theme-text" style="font-size: 0.85rem; max-width: 240px; white-space: normal;">
                            {{ Str::limit($maintenance->description, 50) }}
                        </td>
                        <td>
                            <div style="font-family: var(--font-mono); font-size: 0.78rem; display: flex; flex-direction: column; gap: 3px;">
                                <span class="text-muted" title="{{ __('messages.start_date') }}">
                                    <i class="far fa-calendar-alt text-warning mr-1"></i> {{ $maintenance->start_date }}
                                </span>
                                @if($maintenance->end_date)
                                <span style="color: #34d399;" title="{{ __('messages.end_date') }}">
                                    <i class="far fa-calendar-check mr-1"></i> {{ $maintenance->end_date }}
                                </span>
                                @else
                                <span class="text-muted" style="font-style: italic;">
                                    <i class="far fa-clock mr-1"></i> {{ __('messages.in_progress') ?? 'In Progress' }}
                                </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                @can('action_manage_maintenance')
                                <button class="btn btn-sm dropdown-toggle status-btn p-0 border-0 bg-transparent" type="button" data-toggle="dropdown" aria-expanded="false" data-id="{{ $maintenance->id }}" style="box-shadow: none;">
                                    @if($maintenance->status == 'Ongoing')
                                        <span class="badge status-badge" style="background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.35); color: #fbbf24; box-shadow: 0 0 10px rgba(245, 158, 11, 0.25); padding: 5px 10px; border-radius: 999px; font-weight: 600;">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #f59e0b; box-shadow: 0 0 6px #f59e0b; display: inline-block; margin-right: 4px;"></span>
                                            {{ __('messages.ongoing') }}
                                        </span>
                                    @elseif($maintenance->status == 'Completed')
                                        <span class="badge status-badge" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.35); color: #34d399; box-shadow: 0 0 10px rgba(16, 185, 129, 0.25); padding: 5px 10px; border-radius: 999px; font-weight: 600;">
                                            <i class="fas fa-check mr-1"></i> {{ __('messages.completed') }}
                                        </span>
                                    @else
                                        <span class="badge status-badge" style="background: rgba(100, 116, 139, 0.15); border: 1px solid rgba(100, 116, 139, 0.3); color: #94a3b8; padding: 5px 10px; border-radius: 999px; font-weight: 600;">
                                            {{ __('messages.cancelled') }}
                                        </span>
                                    @endif
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" style="background: rgba(15, 23, 42, 0.98); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 12px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);">
                                    <a class="dropdown-item status-change-btn text-warning" href="#" data-status="Ongoing" style="font-size: 0.85rem; font-weight: 600;"><i class="fas fa-clock mr-1"></i> {{ __('messages.ongoing') }}</a>
                                    <a class="dropdown-item status-change-btn text-success" href="#" data-status="Completed" style="font-size: 0.85rem; font-weight: 600;"><i class="fas fa-check-circle mr-1"></i> {{ __('messages.completed') }}</a>
                                    <a class="dropdown-item status-change-btn text-secondary" href="#" data-status="Cancelled" style="font-size: 0.85rem; font-weight: 600;"><i class="fas fa-ban mr-1"></i> {{ __('messages.cancelled') }}</a>
                                </div>
                                @else
                                <button class="btn btn-sm status-btn p-0 border-0 bg-transparent" type="button" style="box-shadow: none; cursor: default;">
                                    @if($maintenance->status == 'Ongoing')
                                        <span class="badge status-badge" style="background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.35); color: #fbbf24; padding: 5px 10px; border-radius: 999px; font-weight: 600;">{{ __('messages.ongoing') }}</span>
                                    @elseif($maintenance->status == 'Completed')
                                        <span class="badge status-badge" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.35); color: #34d399; padding: 5px 10px; border-radius: 999px; font-weight: 600;">{{ __('messages.completed') }}</span>
                                    @else
                                        <span class="badge status-badge" style="background: rgba(100, 116, 139, 0.15); border: 1px solid rgba(100, 116, 139, 0.3); color: #94a3b8; padding: 5px 10px; border-radius: 999px; font-weight: 600;">{{ __('messages.cancelled') }}</span>
                                    @endif
                                </button>
                                @endcan
                            </div>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center" style="gap: 8px;">
                                @if($maintenance->status == 'Ongoing')
                                    <button type="button" class="btn btn-sm d-flex align-items-center justify-content-center" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 10px; width: 34px; height: 34px; transition: all 0.2s ease;" title="{{ __('messages.complete_maintenance') }}" data-toggle="modal" data-target="#completeModal{{ $maintenance->id }}"><i class="fas fa-check"></i></button>
                                @endif
                                @can('action_manage_maintenance')
                                <a href="{{ route('maintenances.edit', array_merge([$maintenance->id], request()->query())) }}" class="btn btn-sm d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 10px; width: 34px; height: 34px; transition: all 0.2s ease;" title="{{ __('messages.edit') }}"><i class="fas fa-edit"></i></a>
                                @endcan
                                @can('action_manage_maintenance')
                                <form action="{{ route('maintenances.destroy', array_merge([$maintenance->id], request()->query())) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-delete d-flex align-items-center justify-content-center" style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 10px; width: 34px; height: 34px; transition: all 0.2s ease;" title="{{ __('messages.delete') }}" data-confirm-message="{{ __('messages.confirm_delete') }}"><i class="fas fa-trash"></i></button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5" style="font-size: 0.9rem;">
                            <i class="fas fa-inbox d-block mb-2" style="font-size: 2rem; opacity: 0.5;"></i>
                            {{ __('messages.no_data') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="d-block d-md-none p-3 maintenance-mobile-list">
            @forelse($maintenances as $maintenance)
            <div class="maintenance-mobile-card mb-3 p-3">
                <!-- Top row: Number, Asset info, Status badge -->
                <div class="d-flex justify-content-between align-items-start mb-2.5" style="gap: 10px;">
                    <div class="d-flex align-items-center" style="gap: 10px; min-width: 0; flex: 1;">
                        <div class="maintenance-asset-icon flex-shrink-0">
                            <i class="fas fa-desktop"></i>
                        </div>
                        <div style="min-width: 0;">
                            <a href="{{ $maintenance->asset_id ? route('assets.show', $maintenance->asset_id) : '#' }}" class="font-weight-bold theme-text text-truncate d-block text-decoration-none" style="font-size: 0.92rem; line-height: 1.25;" title="{{ $maintenance->asset->name ?? '-' }}">
                                {{ $maintenance->asset->name ?? '-' }}
                            </a>
                            <div class="d-flex align-items-center flex-wrap mt-0.5" style="gap: 6px;">
                                <span class="badge font-mono" style="background: rgba(56, 189, 248, 0.12); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.25); font-size: 0.72rem; padding: 2px 7px;">
                                    {{ $maintenance->asset->asset_tag ?? '-' }}
                                </span>
                                <span class="text-muted font-mono" style="font-size: 0.72rem;">
                                    #{{ (method_exists($maintenances, 'firstItem') && $maintenances->firstItem() ? $maintenances->firstItem() + $loop->index : $loop->iteration) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Status dropdown / badge -->
                    <div class="dropdown flex-shrink-0">
                        @can('action_manage_maintenance')
                        <button class="btn btn-sm dropdown-toggle status-btn p-0 border-0 bg-transparent" type="button" data-toggle="dropdown" aria-expanded="false" data-id="{{ $maintenance->id }}" style="box-shadow: none;">
                            @if($maintenance->status == 'Ongoing')
                                <span class="badge status-badge" style="background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.35); color: #fbbf24; box-shadow: 0 0 10px rgba(245, 158, 11, 0.25); padding: 5px 10px; border-radius: 999px; font-weight: 600; font-size: 0.75rem;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #f59e0b; box-shadow: 0 0 6px #f59e0b; display: inline-block; margin-right: 4px;"></span>
                                    {{ __('messages.ongoing') }}
                                </span>
                            @elseif($maintenance->status == 'Completed')
                                <span class="badge status-badge" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.35); color: #34d399; box-shadow: 0 0 10px rgba(16, 185, 129, 0.25); padding: 5px 10px; border-radius: 999px; font-weight: 600; font-size: 0.75rem;">
                                    <i class="fas fa-check mr-1"></i> {{ __('messages.completed') }}
                                </span>
                            @else
                                <span class="badge status-badge" style="background: rgba(100, 116, 139, 0.15); border: 1px solid rgba(100, 116, 139, 0.3); color: #94a3b8; padding: 5px 10px; border-radius: 999px; font-weight: 600; font-size: 0.75rem;">
                                    {{ __('messages.cancelled') }}
                                </span>
                            @endif
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" style="background: rgba(15, 23, 42, 0.98); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 12px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);">
                            <a class="dropdown-item status-change-btn text-warning" href="#" data-status="Ongoing" style="font-size: 0.85rem; font-weight: 600;"><i class="fas fa-clock mr-1"></i> {{ __('messages.ongoing') }}</a>
                            <a class="dropdown-item status-change-btn text-success" href="#" data-status="Completed" style="font-size: 0.85rem; font-weight: 600;"><i class="fas fa-check-circle mr-1"></i> {{ __('messages.completed') }}</a>
                            <a class="dropdown-item status-change-btn text-secondary" href="#" data-status="Cancelled" style="font-size: 0.85rem; font-weight: 600;"><i class="fas fa-ban mr-1"></i> {{ __('messages.cancelled') }}</a>
                        </div>
                        @else
                        <button class="btn btn-sm status-btn p-0 border-0 bg-transparent" type="button" style="box-shadow: none; cursor: default;">
                            @if($maintenance->status == 'Ongoing')
                                <span class="badge status-badge" style="background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.35); color: #fbbf24; padding: 5px 10px; border-radius: 999px; font-weight: 600; font-size: 0.75rem;">{{ __('messages.ongoing') }}</span>
                            @elseif($maintenance->status == 'Completed')
                                <span class="badge status-badge" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.35); color: #34d399; padding: 5px 10px; border-radius: 999px; font-weight: 600; font-size: 0.75rem;">{{ __('messages.completed') }}</span>
                            @else
                                <span class="badge status-badge" style="background: rgba(100, 116, 139, 0.15); border: 1px solid rgba(100, 116, 139, 0.3); color: #94a3b8; padding: 5px 10px; border-radius: 999px; font-weight: 600; font-size: 0.75rem;">{{ __('messages.cancelled') }}</span>
                            @endif
                        </button>
                        @endcan
                    </div>
                </div>

                <!-- Middle: Type badge & Description -->
                <div class="mb-2">
                    <div class="d-flex align-items-center flex-wrap mb-1.5" style="gap: 6px;">
                        @if($maintenance->type == 'Routine')
                            <span class="badge" style="background: rgba(59, 130, 246, 0.12); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.25); border-radius: 6px; padding: 3px 8px; font-weight: 600; font-size: 0.72rem;">
                                <i class="fas fa-sync-alt mr-1"></i> {{ __('messages.routine_maintenance') }}
                            </span>
                        @elseif($maintenance->type == 'Repair')
                            <span class="badge" style="background: rgba(239, 68, 68, 0.12); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.25); border-radius: 6px; padding: 3px 8px; font-weight: 600; font-size: 0.72rem;">
                                <i class="fas fa-wrench mr-1"></i> {{ __('messages.repair_broken') }}
                            </span>
                        @else
                            <span class="badge" style="background: rgba(168, 85, 247, 0.12); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.25); border-radius: 6px; padding: 3px 8px; font-weight: 600; font-size: 0.72rem;">
                                <i class="fas fa-microchip mr-1"></i> {{ __('messages.hardware_upgrade') }}
                            </span>
                        @endif
                    </div>
                    @if($maintenance->description)
                    <div class="theme-text" style="font-size: 0.84rem; line-height: 1.4; color: var(--color-ink-1, #cbd5e1);">
                        {{ $maintenance->description }}
                    </div>
                    @endif
                </div>

                <!-- Dates row -->
                <div class="d-flex align-items-center justify-content-between pt-2 mb-3" style="border-top: 1px dashed rgba(148, 163, 184, 0.15); font-family: var(--font-mono); font-size: 0.75rem;">
                    <div class="text-muted" title="{{ __('messages.start_date') }}">
                        <i class="far fa-calendar-alt text-warning mr-1"></i> {{ $maintenance->start_date }}
                    </div>
                    <div>
                        @if($maintenance->end_date)
                            <span style="color: #34d399;" title="{{ __('messages.end_date') }}">
                                <i class="far fa-calendar-check mr-1"></i> {{ $maintenance->end_date }}
                            </span>
                        @else
                            <span class="text-muted" style="font-style: italic;">
                                <i class="far fa-clock mr-1"></i> {{ __('messages.in_progress') ?? 'In Progress' }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Bottom Action Bar -->
                <div class="d-flex align-items-center" style="gap: 8px;">
                    @if($maintenance->status == 'Ongoing')
                        <button type="button" class="btn btn-sm flex-grow-1 d-flex align-items-center justify-content-center" style="background: rgba(16, 185, 129, 0.16); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.35); border-radius: 10px; height: 38px; font-weight: 600; font-size: 0.8rem; gap: 6px; transition: all 0.2s ease;" data-toggle="modal" data-target="#completeModal{{ $maintenance->id }}">
                            <i class="fas fa-check-circle"></i> Selesaikan
                        </button>
                    @endif

                    @can('action_manage_maintenance')
                    <a href="{{ route('maintenances.edit', array_merge([$maintenance->id], request()->query())) }}" class="btn btn-sm d-flex align-items-center justify-content-center {{ $maintenance->status == 'Ongoing' ? '' : 'flex-grow-1' }}" style="background: rgba(245, 158, 11, 0.14); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 10px; height: 38px; min-width: 44px; font-size: 0.82rem; font-weight: 600; gap: 6px; transition: all 0.2s ease;" title="{{ __('messages.edit') }}">
                        <i class="fas fa-edit"></i>
                        @if($maintenance->status != 'Ongoing')
                            Edit
                        @endif
                    </a>
                    @endcan

                    @can('action_manage_maintenance')
                    <form action="{{ route('maintenances.destroy', array_merge([$maintenance->id], request()->query())) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-delete d-flex align-items-center justify-content-center" style="background: rgba(239, 68, 68, 0.14); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 10px; height: 38px; width: 44px; transition: all 0.2s ease;" title="{{ __('messages.delete') }}" data-confirm-message="{{ __('messages.confirm_delete') }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-5" style="font-size: 0.9rem;">
                <i class="fas fa-inbox d-block mb-2" style="font-size: 2rem; opacity: 0.5;"></i>
                {{ __('messages.no_data') }}
            </div>
            @endforelse
        </div>
    </div>

    @if(method_exists($maintenances, 'hasPages') && $maintenances->hasPages())
    <div class="card-footer border-0 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap" style="background: transparent; border-top: 1px solid rgba(148, 163, 184, 0.12) !important; gap: 10px;">
        <div class="small text-muted font-mono">
            Showing {{ $maintenances->firstItem() }} to {{ $maintenances->lastItem() }} of {{ $maintenances->total() }} records
        </div>
        <div>
            {{ $maintenances->links() }}
        </div>
    </div>
    @endif
</div>

<!-- Complete Modals placed outside of table-responsive to prevent clipping -->
@foreach($maintenances as $maintenance)
    @if($maintenance->status == 'Ongoing')
    <div class="modal fade" id="completeModal{{ $maintenance->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content theme-card">
                <form action="{{ route('maintenances.complete', $maintenance) }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <div class="d-flex align-items-center" style="gap: 10px;">
                            <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); display: flex; align-items: center; justify-content: center; color: #34d399;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h5 class="modal-title theme-text font-weight-bold" style="font-size: 1.05rem;">{{ __('messages.complete_maintenance') }}</h5>
                        </div>
                        <button type="button" class="close theme-text" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body pt-3">
                        <p class="theme-text mb-3" style="font-size: 0.88rem;">{{ __('messages.completing_maintenance_for') ?? 'Completing maintenance for' }} <strong style="color: #38bdf8; font-family: var(--font-mono);">{{ $maintenance->asset->asset_tag ?? '' }} - {{ $maintenance->asset->name ?? '' }}</strong></p>
                        <div class="form-group mb-0">
                            <label class="theme-text font-weight-bold" style="font-size: 0.84rem;">{{ __('messages.end_date') }} *</label>
                            <input type="date" name="end_date" class="form-control theme-input" value="{{ date('Y-m-d') }}" style="height: 44px; border-radius: 10px;" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 10px; padding: 8px 16px;">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-success" style="border-radius: 10px; padding: 8px 18px; font-weight: 600;">{{ __('messages.mark_completed') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

@push('scripts')
<script>
$(document).ready(function() {
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
            url: '{{ url("maintenances") }}/' + id + '/status',
            type: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                if(response.success) {
                    badge.removeClass('badge-primary badge-warning badge-success badge-danger badge-secondary badge-info badge-dark');
                    badge.css('box-shadow', 'none');
                    
                    switch(newStatus) {
                        case 'Ongoing':
                            badge.css({'background': 'rgba(245, 158, 11, 0.15)', 'border': '1px solid rgba(245, 158, 11, 0.35)', 'color': '#fbbf24', 'box-shadow': '0 0 10px rgba(245, 158, 11, 0.25)'});
                            badge.html('<span style="width: 6px; height: 6px; border-radius: 50%; background: #f59e0b; box-shadow: 0 0 6px #f59e0b; display: inline-block; margin-right: 4px;"></span> {{ __("messages.ongoing") }}');
                            break;
                        case 'Completed':
                            badge.css({'background': 'rgba(16, 185, 129, 0.15)', 'border': '1px solid rgba(16, 185, 129, 0.35)', 'color': '#34d399', 'box-shadow': '0 0 10px rgba(16, 185, 129, 0.25)'});
                            badge.html('<i class="fas fa-check mr-1"></i> {{ __("messages.completed") }}');
                            break;
                        case 'Cancelled':
                            badge.css({'background': 'rgba(100, 116, 139, 0.15)', 'border': '1px solid rgba(100, 116, 139, 0.3)', 'color': '#94a3b8'});
                            badge.html('{{ __("messages.cancelled") }}');
                            break;
                    }
                    setTimeout(function() { location.reload(); }, 800);
                } else {
                    Swal.fire({ icon: 'error', text: response.message || 'Error updating status.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, background: '#0f172a', color: '#f8fafc', customClass: { popup: 'border border-danger' } });
                    badge.html(originalHtml);
                }
            },
            error: function(xhr) {
                Swal.fire({ icon: 'error', text: 'Error updating status.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, background: '#0f172a', color: '#f8fafc', customClass: { popup: 'border border-danger' } });
                badge.html(originalHtml);
            }
        });
    });
});
</script>
@endpush
@endsection

