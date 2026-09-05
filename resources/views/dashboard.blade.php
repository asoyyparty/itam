@extends('layouts.admin')

@section('title', __('messages.system_overview'))
@section('hide_content_header', true)

@push('styles')
    <style>
        /* ── Hallmark Workbench Dashboard Styles ─────────────────── */
        .dashboard-header-banner {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.65), rgba(30, 41, 59, 0.45));
            border: 1px solid rgba(148, 163, 184, 0.15);
            border-radius: 20px;
            padding: 18px 24px;
            backdrop-filter: blur(16px);
            margin-top: 0 !important;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }
        body.light-mode .dashboard-header-banner {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(241, 245, 249, 0.85));
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
        }

        /* Hallmark KPI Stat Cards */
        .kpi-card {
            background: rgba(15, 23, 42, 0.45);
            border: 1px solid rgba(148, 163, 184, 0.14);
            border-radius: 18px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            backdrop-filter: blur(14px);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        body.light-mode .kpi-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
        }
        .kpi-card:hover {
            transform: translateY(-4px);
            border-color: rgba(99, 102, 241, 0.35);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3), 0 0 20px rgba(99, 102, 241, 0.15);
        }
        body.light-mode .kpi-card:hover {
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.1);
        }

        .kpi-icon-wrapper {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .kpi-icon-wrapper.success {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.25);
            color: #34d399;
        }
        .kpi-icon-wrapper.indigo {
            background: rgba(99, 102, 241, 0.12);
            border: 1px solid rgba(99, 102, 241, 0.25);
            color: #818cf8;
        }
        .kpi-icon-wrapper.warning {
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.25);
            color: #fbbf24;
        }
        .kpi-icon-wrapper.cyan {
            background: rgba(6, 182, 212, 0.12);
            border: 1px solid rgba(6, 182, 212, 0.25);
            color: #38bdf8;
        }

        .kpi-value {
            font-size: 2.2rem;
            font-weight: 800;
            line-height: 1;
            font-variant-numeric: tabular-nums;
            letter-spacing: -0.03em;
            color: #f8fafc;
            margin-top: 14px;
            margin-bottom: 4px;
        }
        body.light-mode .kpi-value {
            color: #0f172a;
        }

        .kpi-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #94a3b8;
            letter-spacing: 0.01em;
        }
        body.light-mode .kpi-label {
            color: #64748b;
        }

        .kpi-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            font-weight: 600;
            color: #818cf8;
            text-decoration: none !important;
            margin-top: 14px;
            transition: color 0.15s ease, transform 0.15s ease;
        }
        .kpi-link:hover {
            color: #a5b4fc;
            transform: translateX(3px);
        }

        /* Hallmark Category Cards Grid */
        .cat-mini-card {
            border-radius: 16px !important;
            transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1) !important;
            border: 1px solid rgba(148, 163, 184, 0.14) !important;
            background: rgba(15, 23, 42, 0.4) !important;
            padding: 16px 12px !important;
        }
        body.light-mode .cat-mini-card {
            border: 1px solid #e2e8f0 !important;
            background: #ffffff !important;
        }
        .cat-mini-card:hover {
            transform: translateY(-3px) scale(1.02) !important;
            border-color: rgba(99, 102, 241, 0.4) !important;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.25), 0 0 15px rgba(99, 102, 241, 0.15) !important;
        }
        .cat-count {
            font-size: 1.8rem !important;
            font-weight: 800 !important;
            font-variant-numeric: tabular-nums !important;
            background: linear-gradient(135deg, #38bdf8, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2px !important;
        }
        .cat-label {
            font-size: 0.78rem !important;
            font-weight: 600 !important;
            color: #94a3b8 !important;
        }
        body.light-mode .cat-label {
            color: #64748b !important;
        }

        /* Chart & Section Cards */
        .hallmark-dashboard-card {
            background: rgba(15, 23, 42, 0.45);
            border: 1px solid rgba(148, 163, 184, 0.14);
            border-radius: 18px;
            overflow: hidden;
            margin-bottom: 20px;
            backdrop-filter: blur(14px);
        }
        body.light-mode .hallmark-dashboard-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.04);
        }
        .hallmark-dashboard-card .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.1) !important;
            background: transparent !important;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        body.light-mode .hallmark-dashboard-card .card-header {
            border-bottom: 1px solid #f1f5f9 !important;
        }
        .hallmark-dashboard-card .card-title {
            font-size: 0.95rem !important;
            font-weight: 700 !important;
            color: #f8fafc !important;
            margin: 0 !important;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        body.light-mode .hallmark-dashboard-card .card-title {
            color: #0f172a !important;
        }

        /* Live Indicator Animation */
        .live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 8px #10b981;
            animation: pulse-dot 1.8s infinite ease-in-out;
            display: inline-block;
        }
        @keyframes pulse-dot {
            0% { transform: scale(0.95); opacity: 0.7; }
            50% { transform: scale(1.3); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.7; }
        }

        /* Activity Scroll & Mobile Card Styling */
        .activity-scroll-container {
            max-height: 320px !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            width: 100% !important;
        }
        .activity-scroll-container::-webkit-scrollbar,
        .activity-mobile-feed::-webkit-scrollbar {
            width: 4px;
        }
        .activity-scroll-container::-webkit-scrollbar-thumb,
        .activity-mobile-feed::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.4);
            border-radius: 4px;
        }

        .activity-mobile-feed {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
            max-height: 320px !important;
            -webkit-overflow-scrolling: touch !important;
        }

        .mobile-activity-card {
            background: rgba(15, 23, 42, 0.4) !important;
            border: 1px solid rgba(148, 163, 184, 0.14) !important;
            border-radius: 14px !important;
            padding: 14px !important;
            margin-bottom: 10px !important;
            width: 100% !important;
            max-width: 100% !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            overflow-x: hidden !important;
            word-break: break-word !important;
        }
        body.light-mode .mobile-activity-card {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04) !important;
        }
    </style>
@endpush

@section('content')

<!-- Dashboard Header Banner -->
<div class="dashboard-header-banner mt-0">
    <div class="d-flex align-items-center justify-content-between flex-nowrap w-100" style="display: flex !important; flex-direction: row !important; align-items: center !important; justify-content: space-between !important; flex-wrap: nowrap !important; gap: 12px; width: 100% !important;">
        <div class="d-flex align-items-center min-w-0" style="gap: 12px; min-width: 0; flex: 1 1 auto;">
            <div style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(79, 70, 229, 0.08)); border: 1px solid rgba(99, 102, 241, 0.35); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 1.15rem; flex-shrink: 0; box-shadow: 0 0 14px rgba(99, 102, 241, 0.25);">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div style="flex: 1; min-width: 0;">
                <h5 class="mb-0 font-weight-bold theme-text text-truncate" style="font-size: 1.05rem; line-height: 1.2;">{{ __('messages.system_overview') }}</h5>
                <small class="text-muted d-block" style="font-size: 0.78rem; margin-top: 2px; white-space: normal; line-height: 1.3;">{{ __('messages.dashboard_subtitle') }}</small>
            </div>
        </div>

        <div class="d-flex align-items-center ml-auto flex-shrink-0" style="margin-left: auto !important;">
            <span class="text-nowrap" style="font-family: var(--font-mono); font-size: 0.68rem; padding: 5px 12px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.25); color: #34d399; border-radius: 999px; display: inline-flex; align-items: center; gap: 6px; font-weight: 600;">
                <span class="live-dot" style="flex-shrink: 0;"></span> {{ strtoupper(__('messages.system_operational')) }}
            </span>
        </div>
    </div>
</div>

<!-- KPI Stat Cards Row -->
<div class="row mb-4">
    <!-- Asset Active -->
    <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
        <div class="kpi-card">
            <div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="kpi-label">{{ __('messages.asset_active') }}</span>
                    <div class="kpi-icon-wrapper success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="kpi-value">{{ $active_assets }}</div>
            </div>
            <a href="{{ route('assets.index', ['status' => 'Available,Assigned']) }}" class="kpi-link">
                {{ __('messages.more_info') }} <i class="fas fa-arrow-right" style="font-size: 0.75rem;"></i>
            </a>
        </div>
    </div>

    <!-- Handover / Assignment -->
    <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
        <div class="kpi-card">
            <div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="kpi-label">{{ __('messages.assignment') ?? 'Handover' }}</span>
                    <div class="kpi-icon-wrapper indigo">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                </div>
                <div class="kpi-value">{{ $assigned_assets }}</div>
            </div>
            <a href="{{ route('assets.index', ['status' => 'Assigned']) }}" class="kpi-link">
                {{ __('messages.more_info') }} <i class="fas fa-arrow-right" style="font-size: 0.75rem;"></i>
            </a>
        </div>
    </div>

    <!-- Open Ticket -->
    <div class="col-lg-3 col-sm-6 mb-3 mb-sm-0">
        <div class="kpi-card">
            <div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="kpi-label">{{ __('messages.open_ticket') ?? 'Open Ticket' }}</span>
                    <div class="kpi-icon-wrapper warning">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                </div>
                <div class="kpi-value">{{ $open_tickets }}</div>
            </div>
            <a href="{{ route('tickets.index', ['status' => 'Open,In Progress']) }}" class="kpi-link">
                {{ __('messages.more_info') }} <i class="fas fa-arrow-right" style="font-size: 0.75rem;"></i>
            </a>
        </div>
    </div>

    <!-- IP Used -->
    <div class="col-lg-3 col-sm-6">
        <div class="kpi-card">
            <div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="kpi-label">{{ __('messages.ip_used') ?? 'IP Used' }}</span>
                    <div class="kpi-icon-wrapper cyan">
                        <i class="fas fa-network-wired"></i>
                    </div>
                </div>
                <div class="kpi-value">{{ $used_ips }}</div>
            </div>
            <a href="{{ route('ips.index', ['status' => 'Used']) }}" class="kpi-link">
                {{ __('messages.more_info') }} <i class="fas fa-arrow-right" style="font-size: 0.75rem;"></i>
            </a>
        </div>
    </div>
</div>

<!-- Detailed Asset Categories Grid -->
<div class="d-flex align-items-center justify-content-between mb-3">
    <h6 class="font-weight-bold theme-text m-0" style="font-family: var(--font-mono); font-size: 0.75rem; letter-spacing: 0.08em; text-transform: uppercase; color: #64748b;">
        <i class="fas fa-boxes mr-1 text-primary"></i> {{ __('messages.it_categories') }}
    </h6>
</div>
<div class="row mb-4">
    <div class="col-lg-2 col-md-4 col-6 mb-3">
        <a href="{{ route('assets.index', ['category' => 'Accessories']) }}" style="text-decoration: none; display: block;" class="h-100">
            <div class="cat-mini-card text-center h-100">
                <div class="cat-count">{{ $accessories_count }}</div>
                <span class="cat-label">{{ __('messages.cat_accessories') }}</span>
            </div>
        </a>
    </div>
    <div class="col-lg-2 col-md-4 col-6 mb-3">
        <a href="{{ route('assets.index', ['category' => 'Printer']) }}" style="text-decoration: none; display: block;" class="h-100">
            <div class="cat-mini-card text-center h-100">
                <div class="cat-count">{{ $printer_count }}</div>
                <span class="cat-label">{{ __('messages.cat_printer') }}</span>
            </div>
        </a>
    </div>
    <div class="col-lg-2 col-md-4 col-6 mb-3">
        <a href="{{ route('assets.index', ['category' => 'Computer']) }}" style="text-decoration: none; display: block;" class="h-100">
            <div class="cat-mini-card text-center h-100">
                <div class="cat-count">{{ $computer_count }}</div>
                <span class="cat-label">{{ __('messages.cat_computer') }}</span>
            </div>
        </a>
    </div>
    <div class="col-lg-2 col-md-4 col-6 mb-3">
        <a href="{{ route('assets.index', ['category' => 'Network']) }}" style="text-decoration: none; display: block;" class="h-100">
            <div class="cat-mini-card text-center h-100">
                <div class="cat-count">{{ $network_count }}</div>
                <span class="cat-label">{{ __('messages.cat_network') }}</span>
            </div>
        </a>
    </div>
    <div class="col-lg-2 col-md-4 col-6 mb-3">
        <a href="{{ route('assets.index', ['category' => 'Storage']) }}" style="text-decoration: none; display: block;" class="h-100">
            <div class="cat-mini-card text-center h-100">
                <div class="cat-count">{{ $storage_count }}</div>
                <span class="cat-label">{{ __('messages.cat_storage') }}</span>
            </div>
        </a>
    </div>
    <div class="col-lg-2 col-md-4 col-6 mb-3">
        <a href="{{ route('assets.index', ['category' => 'Other IT Asset']) }}" style="text-decoration: none; display: block;" class="h-100">
            <div class="cat-mini-card text-center h-100">
                <div class="cat-count">{{ $other_it_asset_count }}</div>
                <span class="cat-label">{{ __('messages.cat_other_it_asset') }}</span>
            </div>
        </a>
    </div>
</div>

<!-- Graphs and Charts Bento Section -->
<div class="row">
    <!-- Age Distribution -->
    <div class="col-md-6">
        <div class="hallmark-dashboard-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history text-primary"></i> {{ __('messages.asset_age') }}
                </h3>
            </div>
            <div class="card-body p-3">
                <div style="height: 250px;"><canvas id="ageChart"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Location Distribution -->
    <div class="col-md-6">
        <div class="hallmark-dashboard-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-map-marker-alt text-success"></i> {{ __('messages.asset_location') }}
                </h3>
            </div>
            <div class="card-body p-3">
                <div style="height: 250px;"><canvas id="locationChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- IP Address Distribution -->
    <div class="col-md-6">
        <div class="hallmark-dashboard-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-network-wired text-info"></i> {{ __('messages.ip_distribution') ?? 'IP Address Distribution' }}
                </h3>
            </div>
            <div class="card-body p-3">
                <div style="height: 250px;"><canvas id="ipChart"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Ticket Status Distribution -->
    <div class="col-md-6">
        <div class="hallmark-dashboard-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie text-warning"></i> {{ __('messages.ticket_status_distribution') }}
                </h3>
            </div>
            <div class="card-body p-3">
                <div style="height: 250px;"><canvas id="ticketChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

@php
    $isSuperAdmin = Auth::user() && (Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('superadmin') || Auth::user()->id === 1);
@endphp

@if($isSuperAdmin)
<!-- Activity Log Section (Super Admin Only) -->
<div class="row mt-2">
    <div class="col-md-12">
        <div class="hallmark-dashboard-card">
            <div class="card-header d-flex align-items-center justify-content-between flex-nowrap w-100" style="display: flex !important; flex-direction: row !important; align-items: center !important; justify-content: space-between !important; flex-wrap: nowrap !important; gap: 10px; width: 100% !important; padding: 14px 18px !important;">
                <h3 class="card-title text-nowrap text-truncate m-0" style="font-size: 0.9rem; font-weight: 700; color: var(--color-ink-0); display: flex; align-items: center; gap: 6px; min-width: 0; flex: 1 1 auto;">
                    <i class="fas fa-stream text-primary" style="font-size: 0.95rem; flex-shrink: 0;"></i>
                    <span class="text-truncate">{{ __('messages.real_time_log') }}</span>
                </h3>
                <span class="text-nowrap ml-auto" style="font-family: var(--font-mono); font-size: 0.62rem; padding: 4px 10px; background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3); border-radius: 999px; display: inline-flex; align-items: center; gap: 5px; font-weight: 600; flex-shrink: 0; margin-left: auto !important;">
                    <span class="live-dot" style="flex-shrink: 0;"></span> LIVE AUDIT STREAM
                </span>
            </div>
            <div class="card-body p-0">
                <!-- Desktop Table View (Strict No Horizontal Scroll) -->
                <div class="activity-scroll-container d-none d-md-block" style="width: 100% !important; overflow-x: hidden !important;">
                    <table class="table table-striped table-hover m-0 theme-table division-table" style="width: 100% !important; table-layout: fixed !important; margin: 0 !important;">
                        <thead>
                            <tr class="text-uppercase text-muted" style="font-size: 11px; letter-spacing: 1px;">
                                <th style="width: 22%;" class="pl-4">{{ __('messages.timestamp') }}</th>
                                <th style="width: 20%;">{{ __('messages.operator') }}</th>
                                <th style="width: 18%;">{{ __('messages.status_event') }}</th>
                                <th style="width: 40%;" class="pr-4">{{ __('messages.activity_target') }}</th>
                            </tr>
                        </thead>
                        <tbody id="realtime-activity-tbody">
                            @forelse($recent_activities as $activity)
                                <tr>
                                    <td class="theme-text pl-4" style="font-size: 0.85rem; word-break: break-word;">
                                        <i class="far fa-clock text-muted mr-1"></i>
                                        @if($activity->date->isToday())
                                            {{ __('messages.today') }}, {{ $activity->date->format('H:i') }}
                                        @elseif($activity->date->isYesterday())
                                            {{ __('messages.yesterday') }}, {{ $activity->date->format('H:i') }}
                                        @else
                                            {{ $activity->date->format('d M, H:i') }}
                                        @endif
                                    </td>
                                    <td class="text-info font-weight-bold" style="font-size: 0.88rem; word-break: break-word;">{{ $activity->operator }}</td>
                                    <td>
                                        <span class="badge badge-{{ $activity->badge }}"
                                            style="box-shadow: 0 0 10px rgba(0,0,0,0.2); padding: 4px 8px; border-radius: 10px; font-size: 0.74rem;">
                                            {{ $activity->status_event }}
                                        </span>
                                    </td>
                                    <td class="theme-text pr-4" style="word-break: break-word; white-space: normal;">
                                        <div style="font-weight: 600;">{{ $activity->asset }}</div>
                                        @if(!empty($activity->details_text))
                                            <div class="mt-1" style="font-size: 0.76rem; color: var(--color-ink-3);">
                                                {{ $activity->details_text }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3 pl-4 pr-4">{{ __('messages.no_data') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile NextGen Activity Card Feed (Strictly fit width, no sideways scroll) -->
                <div class="d-block d-md-none p-3 activity-mobile-feed" id="realtime-activity-mobile-feed">
                    @forelse($recent_activities as $activity)
                        <div class="mobile-activity-card p-3 mb-2">
                            <div class="d-flex align-items-center justify-content-between flex-nowrap mb-2" style="gap: 8px;">
                                <div class="font-weight-bold text-info text-truncate" style="font-size: 0.85rem;">
                                    <i class="fas fa-user-circle mr-1 text-muted"></i>{{ $activity->operator }}
                                </div>
                                <span class="badge badge-{{ $activity->badge }} text-nowrap" style="font-size: 0.72rem; padding: 4px 8px; border-radius: 10px; flex-shrink: 0;">
                                    {{ $activity->status_event }}
                                </span>
                            </div>
                            <div class="mb-1" style="font-size: 0.875rem; font-weight: 600; color: var(--color-ink-0); word-break: break-word;">
                                <i class="fas fa-cube text-accent mr-1"></i> {{ $activity->asset }}
                            </div>
                            @if(!empty($activity->details_text))
                                <div class="mb-2" style="font-size: 0.76rem; color: var(--color-ink-3); word-break: break-word;">
                                    <i class="fas fa-info-circle text-info mr-1"></i>{{ $activity->details_text }}
                                </div>
                            @endif
                            <div class="d-flex align-items-center justify-content-between text-muted" style="font-size: 0.75rem; font-family: var(--font-mono);">
                                <span>
                                    <i class="far fa-clock mr-1"></i>
                                    @if($activity->date->isToday())
                                        {{ __('messages.today') }}, {{ $activity->date->format('H:i') }}
                                    @elseif($activity->date->isYesterday())
                                        {{ __('messages.yesterday') }}, {{ $activity->date->format('H:i') }}
                                    @else
                                        {{ $activity->date->format('d M, H:i') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-3 text-center text-muted">{{ __('messages.no_data') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script>
        $(document).ready(function () {
            // Register DataLabels
            Chart.register(ChartDataLabels);

            // Hallmark chart palette — adapts to dark/light mode
            var isDark = $('body').hasClass('dark-mode');
            var textClr = isDark ? '#94a3b8' : '#475569';
            var gridClr = isDark ? 'rgba(148, 163, 184, 0.08)' : 'rgba(203, 213, 225, 0.4)';
            var borderClr = isDark ? '#0f172a' : '#ffffff';

            Chart.defaults.color = textClr;
            Chart.defaults.font.family = 'Plus Jakarta Sans, system-ui, sans-serif';
            
            var datalabelsConfig = {
                color: isDark ? '#ffffff' : '#0f172a',
                font: { weight: 'bold', size: 11 },
                display: function(context) {
                    var val = context.dataset.data[context.dataIndex];
                    var total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                    return total > 0 && (val / total) >= 0.04;
                },
                formatter: function(value, context) {
                    var total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                    if (total === 0 || value === 0) return '';
                    var percentage = Math.round((value / total) * 100) + '%';
                    return percentage;
                }
            };

            function getPercentTooltip() {
                return {
                    backgroundColor: isDark ? 'rgba(15, 23, 42, 0.9)' : 'rgba(255, 255, 255, 0.95)',
                    titleColor: isDark ? '#ffffff' : '#0f172a',
                    bodyColor: isDark ? '#cbd5e1' : '#334155',
                    borderColor: isDark ? 'rgba(99, 102, 241, 0.3)' : 'rgba(99, 102, 241, 0.2)',
                    borderWidth: 1,
                    cornerRadius: 10,
                    padding: 10,
                    callbacks: {
                        label: function (context) {
                            var label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            var value = context.raw;
                            var total = context.chart.data.datasets[0].data.reduce(function (a, b) {
                                return a + b;
                            }, 0);
                            var percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            label += value + ' (' + percentage + '%)';
                            return label;
                        }
                    }
                };
            }

            // 1. Age Chart (Bar)
            var ctxAge = document.getElementById('ageChart').getContext('2d');
            new Chart(ctxAge, {
                type: 'bar',
                data: {
                    labels: [
                        '0-3 {{ __('messages.years') }}',
                        '4-6 {{ __('messages.years') }}',
                        '7-10 {{ __('messages.years') }}',
                        '>10 {{ __('messages.years') }}'
                    ],
                    datasets: [{
                        label: '{{ __("messages.total_asset") }}',
                        data: [
                        {{ $age_buckets['0_3'] }},
                        {{ $age_buckets['4_6'] }},
                        {{ $age_buckets['7_10'] }},
                            {{ $age_buckets['over_10'] }}
                        ],
                        backgroundColor: [
                            'rgba(99, 102, 241, 0.75)',
                            'rgba(16, 185, 129, 0.75)',
                            'rgba(6, 182, 212, 0.75)',
                            'rgba(245, 158, 11, 0.75)'
                        ],
                        borderColor: [
                            '#6366f1',
                            '#10b981',
                            '#06b6d4',
                            '#f59e0b'
                        ],
                        borderWidth: 1.5,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: getPercentTooltip(),
                        datalabels: datalabelsConfig
                    },
                    scales: {
                        y: {
                            grid: { color: gridClr },
                            ticks: { precision: 0 }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });

            // 2. Location Chart (Doughnut)
            var ctxLoc = document.getElementById('locationChart').getContext('2d');
            new Chart(ctxLoc, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($locations_data->pluck('name')->map(function($loc) { return $loc === 'Unassigned' ? __('messages.unassigned') : $loc; })) !!},
                    datasets: [{
                        data: {!! json_encode($locations_data->pluck('count')) !!},
                        backgroundColor: [
                            'rgba(99, 102, 241, 0.85)',
                            'rgba(16, 185, 129, 0.85)',
                            'rgba(6, 182, 212, 0.85)',
                            'rgba(245, 158, 11, 0.85)',
                            'rgba(239, 68, 68, 0.85)',
                            'rgba(168, 85, 247, 0.85)'
                        ],
                        borderWidth: 2,
                        borderColor: borderClr
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { 
                            position: 'right', 
                            labels: { 
                                boxWidth: 10,
                                font: { size: 11, weight: '600' },
                                padding: 12
                            }
                        },
                        tooltip: getPercentTooltip(),
                        datalabels: datalabelsConfig
                    }
                }
            });

            // 3. IP Chart (Pie)
            var ctxIP = document.getElementById('ipChart').getContext('2d');
            new Chart(ctxIP, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($ip_data->pluck('status')->map(function($status) { return __('messages.' . strtolower($status)) ?? $status; })) !!},
                    datasets: [{
                        data: {!! json_encode($ip_data->pluck('count')) !!},
                        backgroundColor: [
                            'rgba(99, 102, 241, 0.85)',
                            'rgba(6, 182, 212, 0.85)',
                            'rgba(16, 185, 129, 0.85)',
                            'rgba(245, 158, 11, 0.85)'
                        ],
                        borderWidth: 2,
                        borderColor: borderClr
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right', labels: { boxWidth: 12, font: { size: 11, weight: '600' }, padding: 12 } },
                        tooltip: getPercentTooltip(),
                        datalabels: datalabelsConfig
                    }
                }
            });

            // 4. Ticket Status Chart (Doughnut)
            var ctxTicket = document.getElementById('ticketChart').getContext('2d');
            new Chart(ctxTicket, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($ticket_data->pluck('status')->map(function($status) { return __('messages.' . \Illuminate\Support\Str::slug($status, '_')) ?? $status; })) !!},
                    datasets: [{
                        data: {!! json_encode($ticket_data->pluck('count')) !!},
                        backgroundColor: [
                            'rgba(99, 102, 241, 0.85)',
                            'rgba(245, 158, 11, 0.85)',
                            'rgba(16, 185, 129, 0.85)',
                            'rgba(6, 182, 212, 0.85)',
                            'rgba(239, 68, 68, 0.85)'
                        ],
                        borderWidth: 2,
                        borderColor: borderClr
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { position: 'right', labels: { boxWidth: 12, font: { size: 11, weight: '600' }, padding: 12 } },
                        tooltip: getPercentTooltip(),
                        datalabels: datalabelsConfig
                    }
                }
            });

            function fetchActivities() {
                $.ajax({
                    url: "{{ route('dashboard.activities') }}",
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        var tbody = $('#realtime-activity-tbody');
                        var mobileFeed = $('#realtime-activity-mobile-feed');

                        if (data.length === 0) {
                            tbody.html('<tr><td colspan="4" class="text-center text-muted py-3">{{ __("messages.no_data") }}</td></tr>');
                            mobileFeed.html('<div class="p-3 text-center text-muted">{{ __("messages.no_data") }}</div>');
                            return;
                        }

                        var tableHtml = '';
                        var mobileHtml = '';

                        data.forEach(function (activity) {
                            var detailsSubHtml = activity.details_text ? '<div class="mt-1" style="font-size: 0.76rem; color: var(--color-ink-3);"><i class="fas fa-info-circle text-info mr-1"></i>' + activity.details_text + '</div>' : '';

                            tableHtml += '<tr>' +
                                '<td class="theme-text pl-4" style="font-size: 0.85rem; word-break: break-word;">' +
                                '<i class="far fa-clock text-muted mr-1"></i>' + activity.timestamp +
                                '</td>' +
                                '<td class="text-info font-weight-bold" style="font-size: 0.88rem; word-break: break-word;"><i class="fas fa-user-circle mr-1 text-muted"></i>' + activity.operator + '</td>' +
                                '<td>' +
                                '<span class="badge badge-' + activity.badge + '" style="box-shadow: 0 0 10px rgba(0,0,0,0.2); padding: 4px 8px; border-radius: 10px; font-size: 0.74rem;">' +
                                activity.status_event +
                                '</span>' +
                                '</td>' +
                                '<td class="theme-text pr-4" style="word-break: break-word; white-space: normal;"><div style="font-weight: 600;">' + activity.asset + '</div>' + detailsSubHtml + '</td>' +
                                '</tr>';

                            mobileHtml += '<div class="mobile-activity-card p-3 mb-2">' +
                                '<div class="d-flex align-items-center justify-content-between mb-2">' +
                                '<div class="font-weight-bold text-info" style="font-size: 0.85rem;"><i class="fas fa-user-circle mr-1 text-muted"></i>' + activity.operator + '</div>' +
                                '<span class="badge badge-' + activity.badge + '" style="font-size: 0.72rem; padding: 4px 8px; border-radius: 10px;">' + activity.status_event + '</span>' +
                                '</div>' +
                                '<div class="mb-1" style="font-size: 0.875rem; font-weight: 600; color: var(--color-ink-0);"><i class="fas fa-cube text-accent mr-1"></i> ' + activity.asset + '</div>' +
                                (activity.details_text ? '<div class="mb-2" style="font-size: 0.76rem; color: var(--color-ink-3);"><i class="fas fa-info-circle text-info mr-1"></i>' + activity.details_text + '</div>' : '') +
                                '<div class="d-flex align-items-center justify-content-between text-muted" style="font-size: 0.75rem; font-family: var(--font-mono);">' +
                                '<span><i class="far fa-clock mr-1"></i>' + activity.timestamp + '</span>' +
                                '</div>' +
                                '</div>';
                        });

                        tbody.html(tableHtml);
                        mobileFeed.html(mobileHtml);
                    },
                    error: function (err) {
                        console.error("Error fetching activities:", err);
                    }
                });
            }

            @if($isSuperAdmin)
            // Poll every 5 seconds (Super Admin only)
            setInterval(fetchActivities, 5000);
            @endif
        });
    </script>
@endpush