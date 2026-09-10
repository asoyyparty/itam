@extends('layouts.admin')

@section('title', __('messages.performance_console'))
@section('hide_content_header', true)

@push('styles')
    <!-- Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 · Industrial APM Workbench -->
    <style>
        .apm-console-wrapper {
            padding-bottom: 90px;
            overflow-x: clip;
        }

        /* Hallmark Header Banner */
        .apm-header-banner {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.75), rgba(30, 41, 59, 0.55));
            border: 1px solid rgba(148, 163, 184, 0.15);
            border-radius: 20px;
            padding: 22px 26px;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            margin-bottom: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            position: relative;
        }
        body.light-mode .apm-header-banner {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(241, 245, 249, 0.92));
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
        }

        .apm-banner-title {
            font-size: 1.45rem;
            font-weight: 800;
            color: #f8fafc;
            margin: 0;
            letter-spacing: -0.02em;
        }
        body.light-mode .apm-banner-title {
            color: #0f172a !important;
        }

        .apm-banner-desc {
            font-size: 0.85rem;
            color: #94a3b8;
            margin: 4px 0 0 0;
        }
        body.light-mode .apm-banner-desc {
            color: #475569 !important;
        }

        .apm-env-badge {
            background: rgba(255, 255, 255, 0.08);
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            color: #cbd5e1;
            border-radius: 6px;
            padding: 4px 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        body.light-mode .apm-env-badge {
            background: rgba(15, 23, 42, 0.05);
            color: #475569;
            border-color: rgba(203, 213, 225, 0.9);
        }

        .apm-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 9999px;
            background: rgba(99, 102, 241, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.35);
            color: #818cf8;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        body.light-mode .apm-tag {
            background: rgba(99, 102, 241, 0.1);
            border-color: rgba(99, 102, 241, 0.3);
            color: #4f46e5;
        }
        .apm-live-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 8px #10b981;
            display: inline-block;
            animation: apmPulse 2s infinite;
        }
        @keyframes apmPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        /* Time Range Segmented Control */
        .apm-range-control {
            display: inline-flex;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 12px;
            padding: 3px;
            gap: 2px;
        }
        body.light-mode .apm-range-control {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }
        .apm-range-btn {
            padding: 6px 14px;
            border-radius: 9px;
            font-size: 0.78rem;
            font-weight: 600;
            color: #94a3b8;
            border: none;
            background: transparent;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .apm-range-btn:hover {
            color: #f1f5f9;
            background: rgba(255, 255, 255, 0.05);
        }
        body.light-mode .apm-range-btn:hover {
            color: #0f172a;
            background: rgba(0, 0, 0, 0.05);
        }
        .apm-range-btn.active {
            background: #4f46e5;
            color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.4);
        }

        /* Grid Rows & Card Spacing */
        .apm-cards-row {
            margin-left: -10px;
            margin-right: -10px;
            row-gap: 20px;
        }
        .apm-cards-row > [class*="col-"] {
            padding-left: 10px;
            padding-right: 10px;
            margin-bottom: 20px;
        }

        .apm-panel-col {
            margin-bottom: 24px;
        }

        /* Bento KPI Cards */
        .apm-card {
            background: rgba(15, 23, 42, 0.55);
            border: 1px solid rgba(148, 163, 184, 0.16);
            border-radius: 18px;
            padding: 22px 24px;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.25);
        }
        body.light-mode .apm-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
        }
        .apm-card:hover {
            transform: translateY(-3px);
            border-color: rgba(99, 102, 241, 0.35);
            box-shadow: 0 14px 30px -6px rgba(0, 0, 0, 0.45);
        }
        body.light-mode .apm-card:hover {
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        }

        .apm-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .apm-card-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            font-weight: 600;
        }
        body.light-mode .apm-card-title {
            color: #64748b;
        }

        .apm-card-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .apm-metric-val {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.85rem;
            font-weight: 700;
            line-height: 1.1;
            margin: 0 0 10px 0;
            letter-spacing: -0.02em;
        }

        .apm-card-sub {
            font-size: 0.8rem;
            color: #94a3b8;
            line-height: 1.45;
        }
        body.light-mode .apm-card-sub {
            color: #64748b;
        }

        /* Visualizations Section */
        .apm-panel {
            background: rgba(15, 23, 42, 0.55);
            border: 1px solid rgba(148, 163, 184, 0.16);
            border-radius: 18px;
            padding: 24px;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.25);
            height: 100%;
        }
        body.light-mode .apm-panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
        }

        .apm-panel-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        body.light-mode .apm-panel-title {
            color: #0f172a;
        }

        .apm-panel-desc {
            font-size: 0.78rem;
            color: #94a3b8;
            margin-bottom: 20px;
            line-height: 1.45;
        }
        body.light-mode .apm-panel-desc {
            color: #64748b;
        }

        /* Tables & Lists */
        .apm-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.82rem;
        }
        .apm-table th {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            font-weight: 600;
            padding: 12px 14px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.14);
        }
        .apm-table td {
            padding: 13px 14px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.08);
            vertical-align: middle;
        }
        .apm-table tr:last-child td {
            border-bottom: none;
        }
        .apm-table tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }
        body.light-mode .apm-table th {
            color: #64748b;
            border-bottom-color: #e2e8f0;
        }
        body.light-mode .apm-table td {
            border-bottom-color: #f1f5f9;
            color: #1e293b;
        }
        body.light-mode .apm-table tr:hover td {
            background: #f8fafc;
        }

        .apm-route-name {
            color: #f1f5f9;
        }
        body.light-mode .apm-route-name {
            color: #0f172a !important;
        }

        .apm-no-slow-title {
            color: #f1f5f9;
        }
        body.light-mode .apm-no-slow-title {
            color: #0f172a !important;
        }

        .apm-query-route {
            color: #93c5fd;
        }
        body.light-mode .apm-query-route {
            color: #2563eb !important;
        }

        body.light-mode #btn-live-refresh {
            background: rgba(99, 102, 241, 0.1) !important;
            border-color: rgba(99, 102, 241, 0.3) !important;
            color: #4f46e5 !important;
        }
        body.light-mode #btn-live-refresh:hover {
            background: rgba(99, 102, 241, 0.18) !important;
            color: #4338ca !important;
        }

        .apm-query-card {
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid rgba(148, 163, 184, 0.12);
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 12px;
            transition: all 0.2s;
        }
        body.light-mode .apm-query-card {
            background: #f8fafc;
            border-color: #e2e8f0;
        }
        .apm-query-card:hover {
            border-color: rgba(99, 102, 241, 0.3);
        }

        .apm-sql-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.74rem;
            color: #cbd5e1;
            background: rgba(0, 0, 0, 0.25);
            padding: 9px 12px;
            border-radius: 8px;
            word-break: break-all;
            white-space: pre-wrap;
            margin-top: 8px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        body.light-mode .apm-sql-code {
            background: #f1f5f9;
            color: #334155;
            border-color: #cbd5e1;
        }

        /* Runtime Specs Bento Grid */
        .apm-specs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
        }
        .apm-spec-tile {
            background: rgba(15, 23, 42, 0.35);
            border: 1px solid rgba(148, 163, 184, 0.12);
            border-radius: 12px;
            padding: 14px 16px;
        }
        body.light-mode .apm-spec-tile {
            background: #f8fafc;
            border-color: #e2e8f0;
        }
        .apm-spec-label {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #94a3b8;
            margin-bottom: 3px;
        }
        .apm-spec-val {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.88rem;
            font-weight: 600;
            color: #f1f5f9;
        }
        body.light-mode .apm-spec-val {
            color: #0f172a;
        }

        @media (max-width: 768px) {
            .apm-console-wrapper {
                padding-bottom: 100px;
            }
            .apm-header-banner {
                padding: 18px 20px;
                margin-bottom: 20px;
            }
            .apm-range-control {
                width: 100%;
                justify-content: space-between;
                margin-top: 12px;
            }
            .apm-range-btn {
                flex: 1;
                text-align: center;
                justify-content: center;
                padding: 6px 8px;
            }
            .apm-cards-row {
                margin-left: -6px;
                margin-right: -6px;
                row-gap: 16px;
            }
            .apm-cards-row > [class*="col-"] {
                padding-left: 6px;
                padding-right: 6px;
                margin-bottom: 16px;
            }
            .apm-card {
                padding: 18px 20px;
                border-radius: 16px;
            }
            .apm-metric-val {
                font-size: 1.6rem;
            }
            .apm-panel {
                padding: 18px;
                border-radius: 16px;
            }
            .apm-panel-col {
                margin-bottom: 20px;
            }
        }
    </style>
@endpush

@section('content')
<div class="apm-console-wrapper">
    <!-- Header Banner -->
    <div class="apm-header-banner">
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between" style="gap: 16px;">
            <div>
                <div class="d-flex align-items-center mb-2" style="gap: 8px;">
                    <span class="apm-tag">
                        <span class="apm-live-dot"></span>
                        {{ __('messages.perf_live_telemetry') }}
                    </span>
                    <span class="apm-env-badge">
                        {{ $serverEnv['server_os'] }} &bull; PHP {{ $serverEnv['php_version'] }}
                    </span>
                </div>
                <h1 class="apm-banner-title">
                    {{ __('messages.performance_console') }}
                </h1>
                <p class="apm-banner-desc">
                    {{ __('messages.performance_console_desc') }}
                </p>
            </div>

            <!-- Controls (Range Selector, Refresh, Purge) -->
            <div class="d-flex flex-wrap align-items-center" style="gap: 10px;">
                <!-- Time Range Filter -->
                <div class="apm-range-control">
                    <a href="{{ route('system-performance.index', ['range' => '1h']) }}" class="apm-range-btn {{ $selectedRange === '1h' ? 'active' : '' }}">
                        {{ App::getLocale() === 'id' ? '1 Jam' : '1H' }}
                    </a>
                    <a href="{{ route('system-performance.index', ['range' => '24h']) }}" class="apm-range-btn {{ $selectedRange === '24h' ? 'active' : '' }}">
                        {{ App::getLocale() === 'id' ? '24 Jam' : '24H' }}
                    </a>
                    <a href="{{ route('system-performance.index', ['range' => '7d']) }}" class="apm-range-btn {{ $selectedRange === '7d' ? 'active' : '' }}">
                        {{ App::getLocale() === 'id' ? '7 Hari' : '7D' }}
                    </a>
                    <a href="{{ route('system-performance.index', ['range' => '30d']) }}" class="apm-range-btn {{ $selectedRange === '30d' ? 'active' : '' }}">
                        {{ App::getLocale() === 'id' ? '30 Hari' : '30D' }}
                    </a>
                </div>

                <!-- Auto-refresh button -->
                <button type="button" id="btn-live-refresh" class="btn btn-sm" onclick="triggerManualRefresh()" style="background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.35); color: #818cf8; border-radius: 10px; font-weight: 600; padding: 7px 12px; font-size: 0.78rem;">
                    <i class="fas fa-sync-alt mr-1" id="refresh-icon"></i>
                    <span id="refresh-text">{{ __('messages.perf_refresh_now') }}</span>
                </button>

                <!-- Purge Telemetry Logs Button -->
                <form action="{{ route('system-performance.clear') }}" method="POST" onsubmit="return confirm('{{ __('messages.perf_purge_confirm') }}')" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 10px; font-weight: 600; padding: 7px 12px; font-size: 0.78rem;" title="{{ __('messages.perf_purge_logs') }}">
                        <i class="fas fa-trash-alt mr-1"></i> {{ __('messages.perf_purge_logs') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- 4 Bento KPI Metric Cards (Clean Breathing Room & Spacing) -->
    <div class="row apm-cards-row">
        <!-- 1: Avg Response Time -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="apm-card">
                <div class="apm-card-header">
                    <span class="apm-card-title">
                        {{ __('messages.perf_avg_latency') }}
                    </span>
                    <div class="apm-card-icon" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3);">
                        <i class="fas fa-bolt"></i>
                    </div>
                </div>
                <div>
                    <div id="kpi-avg-duration" class="apm-metric-val" style="color: {{ $avgDuration > 500 ? '#f43f5e' : ($avgDuration > 250 ? '#fbbf24' : '#34d399') }};">
                        {{ $avgDuration }} <span style="font-size: 1rem; font-weight: 500; color: #94a3b8;">ms</span>
                    </div>
                    <div class="apm-card-sub">
                        <i class="fas fa-exclamation-triangle mr-1 {{ $slowRequestsCount > 0 ? 'text-warning' : 'text-success' }}"></i>
                        <span>{{ __('messages.perf_slow_requests') }}: <strong id="kpi-slow-reqs">{{ $slowRequestsCount }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2: Database Query Health -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="apm-card">
                <div class="apm-card-header">
                    <span class="apm-card-title">
                        {{ __('messages.perf_db_queries') }}
                    </span>
                    <div class="apm-card-icon" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3);">
                        <i class="fas fa-database"></i>
                    </div>
                </div>
                <div>
                    <div id="kpi-avg-queries" class="apm-metric-val" style="color: #38bdf8;">
                        {{ $avgQueryCount }} <span style="font-size: 1rem; font-weight: 500; color: #94a3b8;">Q/req</span>
                    </div>
                    <div class="apm-card-sub">
                        <i class="far fa-clock mr-1 text-info"></i>
                        <span>{{ __('messages.perf_avg') }}: <strong id="kpi-avg-qtime">{{ $avgQueryTime }}ms</strong> &bull; {{ __('messages.perf_slow_queries_label') }}: <strong id="kpi-slow-queries" class="{{ $slowQueriesCount > 0 ? 'text-danger' : 'text-success' }}">{{ $slowQueriesCount }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3: Server RAM & Hardware -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="apm-card">
                <div class="apm-card-header">
                    <span class="apm-card-title">
                        {{ __('messages.perf_server_memory') }}
                    </span>
                    <div class="apm-card-icon" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3);">
                        <i class="fas fa-memory"></i>
                    </div>
                </div>
                <div>
                    <div id="kpi-peak-ram" class="apm-metric-val" style="color: #34d399;">
                        {{ $peakMemory }} <span style="font-size: 1rem; font-weight: 500; color: #94a3b8;">MB</span>
                    </div>
                    <div class="apm-card-sub">
                        <i class="fas fa-hdd mr-1 text-success"></i>
                        <span>{{ __('messages.perf_spec_storage') }}: {{ $serverEnv['disk_free_gb'] }} GB {{ __('messages.perf_disk_free') }} ({{ $serverEnv['disk_used_percent'] }}% {{ __('messages.perf_disk_used') }})</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4: HTTP Reliability & Traffic -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="apm-card">
                <div class="apm-card-header">
                    <span class="apm-card-title">
                        {{ __('messages.perf_reliability') }}
                    </span>
                    <div class="apm-card-icon" style="background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3);">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                </div>
                <div>
                    <div id="kpi-reliability" class="apm-metric-val" style="color: #c084fc;">
                        {{ $reliabilityScore }}<span style="font-size: 1rem; font-weight: 500; color: #94a3b8;">%</span>
                    </div>
                    <div class="apm-card-sub">
                        <i class="fas fa-exchange-alt mr-1 text-primary"></i>
                        <span>{{ __('messages.perf_total') }}: <strong id="kpi-total-reqs">{{ number_format($totalRequests) }}</strong> (2xx: {{ $status2xx }} | 5xx: {{ $status5xx }})</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section: Latency Trend Line & HTTP Status Doughnut -->
    <div class="row">
        <!-- Latency Trend -->
        <div class="col-12 col-lg-8 apm-panel-col">
            <div class="apm-panel">
                <div class="apm-panel-title">
                    <i class="fas fa-chart-line text-warning"></i>
                    {{ __('messages.perf_latency_trend') }}
                </div>
                <div class="apm-panel-desc">
                    {{ __('messages.perf_latency_trend_desc') }}
                </div>
                <div style="position: relative; height: 260px; width: 100%;">
                    <canvas id="chartLatencyTrend"></canvas>
                </div>
            </div>
        </div>

        <!-- HTTP Status Breakdown -->
        <div class="col-12 col-lg-4 apm-panel-col">
            <div class="apm-panel">
                <div class="apm-panel-title">
                    <i class="fas fa-chart-pie text-info"></i>
                    {{ __('messages.perf_status_distribution') }}
                </div>
                <div class="apm-panel-desc">
                    {{ __('messages.perf_status_distribution_desc') }}
                </div>
                <div style="position: relative; height: 200px; width: 100%; display: flex; align-items: center; justify-content: center;">
                    <canvas id="chartStatusCodes"></canvas>
                </div>
                <div class="d-flex justify-content-around mt-3 text-center flex-wrap" style="font-family: 'JetBrains Mono', monospace; font-size: 0.74rem; gap: 6px;">
                    <div><span class="badge badge-success">{{ __('messages.perf_status_2xx') }}: {{ $status2xx }}</span></div>
                    <div><span class="badge badge-info">{{ __('messages.perf_status_3xx') }}: {{ $status3xx }}</span></div>
                    <div><span class="badge badge-warning">{{ __('messages.perf_status_4xx') }}: {{ $status4xx }}</span></div>
                    <div><span class="badge badge-danger">{{ __('messages.perf_status_5xx') }}: {{ $status5xx }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Diagnostic Details: Top Slowest Routes & Slow Query Inspector -->
    <div class="row">
        <!-- Top Slowest Routes -->
        <div class="col-12 col-lg-6 apm-panel-col">
            <div class="apm-panel">
                <div class="apm-panel-title">
                    <i class="fas fa-tachometer-alt text-danger"></i>
                    {{ __('messages.perf_slowest_routes') }}
                </div>
                <div class="apm-panel-desc">
                    {{ __('messages.perf_slowest_routes_desc') }}
                </div>

                <div class="table-responsive">
                    <table class="apm-table">
                        <thead>
                            <tr>
                                <th>{{ __('messages.perf_method_route') }}</th>
                                <th class="text-center">{{ __('messages.perf_calls') }}</th>
                                <th class="text-right">{{ __('messages.perf_avg_ms') }}</th>
                                <th class="text-right">{{ __('messages.perf_max_ms') }}</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-slowest-routes">
                            @forelse($slowestRoutes as $route)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center" style="gap: 8px;">
                                            <span class="badge" style="background: {{ $route['method'] === 'GET' ? 'rgba(56, 189, 248, 0.2)' : 'rgba(16, 185, 129, 0.2)' }}; color: {{ $route['method'] === 'GET' ? '#38bdf8' : '#34d399' }}; font-family: 'JetBrains Mono', monospace; font-size: 0.68rem; padding: 2px 6px; border-radius: 4px;">
                                                {{ $route['method'] }}
                                            </span>
                                            <span class="font-weight-600 text-truncate apm-route-name" style="max-width: 240px;" title="{{ $route['route_name'] }}">
                                                {{ $route['route_name'] }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center" style="font-family: 'JetBrains Mono', monospace; color: #94a3b8;">
                                        {{ $route['count'] }}
                                    </td>
                                    <td class="text-right font-weight-bold" style="font-family: 'JetBrains Mono', monospace; color: {{ $route['avg_duration'] > 400 ? '#f43f5e' : ($route['avg_duration'] > 200 ? '#fbbf24' : '#34d399') }};">
                                        {{ $route['avg_duration'] }} ms
                                    </td>
                                    <td class="text-right text-muted" style="font-family: 'JetBrains Mono', monospace; font-size: 0.76rem;">
                                        {{ $route['max_duration'] }} ms
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="far fa-smile mr-1"></i> {{ __('messages.perf_no_records') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Slow Database Query Inspector -->
        <div class="col-12 col-lg-6 apm-panel-col">
            <div class="apm-panel">
                <div class="apm-panel-title">
                    <i class="fas fa-search-plus text-warning"></i>
                    {{ __('messages.perf_slow_query_inspector') }}
                </div>
                <div class="apm-panel-desc">
                    {{ __('messages.perf_slow_query_desc') }}
                </div>

                <div id="container-slow-queries" style="max-height: 380px; overflow-y: auto;">
                    @forelse($slowQueries as $sq)
                        <div class="apm-query-card">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div class="d-flex align-items-center" style="gap: 6px;">
                                    <span class="badge" style="background: rgba(244, 63, 94, 0.2); border: 1px solid rgba(244, 63, 94, 0.4); color: #f87171; font-family: 'JetBrains Mono', monospace; font-size: 0.72rem;">
                                        <i class="fas fa-stopwatch mr-1"></i> {{ $sq['duration_ms'] }} ms
                                    </span>
                                    <span class="text-muted" style="font-size: 0.72rem;">&bull; {{ __('messages.perf_route_label') }}: <code class="apm-query-route" style="color:#93c5fd;">{{ $sq['route_name'] }}</code></span>
                                </div>
                                <span class="text-muted" style="font-size: 0.7rem; font-family: 'JetBrains Mono', monospace;">
                                    {{ $sq['time_ago'] }}
                                </span>
                            </div>
                            <div class="apm-sql-code">{{ $sq['sql'] }}</div>
                        </div>
                    @empty
                        <div class="text-center py-5" style="color: #94a3b8;">
                            <i class="fas fa-check-circle text-success" style="font-size: 2.2rem; margin-bottom: 10px; display: block;"></i>
                            <div class="apm-no-slow-title" style="font-weight: 600; font-size: 0.9rem;">{{ __('messages.perf_no_slow_queries') }}</div>
                            <small class="text-muted">{{ __('messages.perf_no_slow_queries_desc') }}</small>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Server Runtime Specs Bento Grid -->
    <div class="apm-panel mb-0">
        <div class="apm-panel-title">
            <i class="fas fa-server text-success"></i>
            {{ __('messages.perf_system_runtime') }}
        </div>
        <div class="apm-panel-desc">
            {{ __('messages.perf_runtime_specs_desc') }}
        </div>

        <div class="apm-specs-grid">
            <div class="apm-spec-tile">
                <div class="apm-spec-label">{{ __('messages.perf_spec_php') }}</div>
                <div class="apm-spec-val">{{ $serverEnv['php_version'] }}</div>
            </div>
            <div class="apm-spec-tile">
                <div class="apm-spec-label">{{ __('messages.perf_spec_laravel') }}</div>
                <div class="apm-spec-val">v{{ $serverEnv['laravel_version'] }}</div>
            </div>
            <div class="apm-spec-tile">
                <div class="apm-spec-label">{{ __('messages.perf_spec_memory_limit') }}</div>
                <div class="apm-spec-val">{{ $serverEnv['memory_limit'] }}</div>
            </div>
            <div class="apm-spec-tile">
                <div class="apm-spec-label">{{ __('messages.perf_spec_opcache') }}</div>
                <div class="apm-spec-val">
                    <span class="badge {{ $serverEnv['opcache_enabled'] ? 'badge-success' : 'badge-secondary' }}" style="font-size:0.75rem;">
                        {{ $serverEnv['opcache_enabled'] ? __('messages.perf_spec_active') : __('messages.perf_spec_off') }}
                    </span>
                </div>
            </div>
            <div class="apm-spec-tile">
                <div class="apm-spec-label">{{ __('messages.perf_spec_db') }}</div>
                <div class="apm-spec-val">{{ ucfirst($serverEnv['db_driver']) }} {{ Str::limit($serverEnv['db_version'], 12) }}</div>
            </div>
            <div class="apm-spec-tile">
                <div class="apm-spec-label">{{ __('messages.perf_spec_cache') }}</div>
                <div class="apm-spec-val">{{ ucfirst($serverEnv['cache_driver']) }} / {{ ucfirst($serverEnv['session_driver']) }}</div>
            </div>
            <div class="apm-spec-tile">
                <div class="apm-spec-label">{{ __('messages.perf_spec_storage') }}</div>
                <div class="apm-spec-val">{{ $serverEnv['disk_free_gb'] }} GB / {{ $serverEnv['disk_total_gb'] }} GB</div>
            </div>
            <div class="apm-spec-tile">
                <div class="apm-spec-label">{{ __('messages.perf_spec_timestamp') }}</div>
                <div class="apm-spec-val" style="font-size: 0.78rem;">{{ $serverEnv['current_time'] }}</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var chartLabels = @json($chartLabels);
    var chartLatency = @json($chartLatency);
    var chartThroughput = @json($chartThroughput);

    var status2xx = {{ $status2xx }};
    var status3xx = {{ $status3xx }};
    var status4xx = {{ $status4xx }};
    var status5xx = {{ $status5xx }};

    var isDark = document.body.classList.contains('dark-mode') || !document.body.classList.contains('light-mode');
    var gridColor = isDark ? 'rgba(255, 255, 255, 0.06)' : 'rgba(0, 0, 0, 0.06)';
    var textColor = isDark ? '#94a3b8' : '#64748b';

    var latencyText = '{{ __("messages.perf_chart_latency") }}';
    var throughputText = '{{ __("messages.perf_chart_throughput") }}';

    // Latency & Throughput Trend Chart
    var ctxTrend = document.getElementById('chartLatencyTrend');
    var trendChart = null;

    if (ctxTrend) {
        trendChart = new Chart(ctxTrend.getContext('2d'), {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: latencyText,
                        data: chartLatency,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        borderWidth: 2,
                        tension: 0.35,
                        fill: true,
                        yAxisID: 'y',
                        pointBackgroundColor: '#f59e0b',
                        pointRadius: 3,
                    },
                    {
                        label: throughputText,
                        data: chartThroughput,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.05)',
                        borderWidth: 1.5,
                        borderDash: [4, 4],
                        tension: 0.35,
                        yAxisID: 'y1',
                        pointBackgroundColor: '#6366f1',
                        pointRadius: 2,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    x: {
                        grid: { color: gridColor },
                        ticks: { color: textColor, font: { family: 'JetBrains Mono', size: 10 } }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: latencyText, color: textColor, font: { size: 10 } },
                        grid: { color: gridColor },
                        ticks: { color: textColor, font: { family: 'JetBrains Mono', size: 10 } }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: { display: true, text: throughputText, color: textColor, font: { size: 10 } },
                        grid: { drawOnChartArea: false },
                        ticks: { color: textColor, font: { family: 'JetBrains Mono', size: 10 }, stepSize: 1 }
                    }
                },
                plugins: {
                    legend: {
                        labels: { color: textColor, font: { family: 'Plus Jakarta Sans', size: 11 } }
                    }
                }
            }
        });
    }

    // Status Codes Doughnut Chart
    var ctxStatus = document.getElementById('chartStatusCodes');
    var statusChart = null;

    if (ctxStatus) {
        var totalStatus = status2xx + status3xx + status4xx + status5xx;
        var dataValues = totalStatus > 0 ? [status2xx, status3xx, status4xx, status5xx] : [1, 0, 0, 0];
        var bgColors = totalStatus > 0 
            ? ['#10b981', '#38bdf8', '#f59e0b', '#f43f5e'] 
            : ['rgba(148, 163, 184, 0.2)', '#38bdf8', '#f59e0b', '#f43f5e'];

        var label2xx = '{{ __("messages.perf_status_2xx") }}';
        var label3xx = '{{ __("messages.perf_status_3xx") }}';
        var label4xx = '{{ __("messages.perf_status_4xx") }}';
        var label5xx = '{{ __("messages.perf_status_5xx") }}';

        statusChart = new Chart(ctxStatus.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: [label2xx, label3xx, label4xx, label5xx],
                datasets: [{
                    data: dataValues,
                    backgroundColor: bgColors,
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '72%'
            }
        });
    }

    // Manual / Live Auto-Refresh AJAX function
    window.triggerManualRefresh = function() {
        var icon = document.getElementById('refresh-icon');
        var text = document.getElementById('refresh-text');
        if (icon) icon.classList.add('fa-spin');
        if (text) text.textContent = '{{ App::getLocale() === "id" ? "Memperbarui..." : "Updating..." }}';

        var url = "{{ route('system-performance.api-stats') }}?range={{ $selectedRange }}";
        fetch(url)
            .then(function(res) { return res.json(); })
            .then(function(data) {
                // Update KPI Cards
                var avgDurEl = document.getElementById('kpi-avg-duration');
                if (avgDurEl) avgDurEl.innerHTML = data.avgDuration + ' <span style="font-size: 1rem; font-weight: 500; color: #94a3b8;">ms</span>';

                var slowReqEl = document.getElementById('kpi-slow-reqs');
                if (slowReqEl) slowReqEl.textContent = data.slowRequestsCount;

                var avgQEl = document.getElementById('kpi-avg-queries');
                if (avgQEl) avgQEl.innerHTML = data.avgQueryCount + ' <span style="font-size: 1rem; font-weight: 500; color: #94a3b8;">Q/req</span>';

                var avgQTimeEl = document.getElementById('kpi-avg-qtime');
                if (avgQTimeEl) avgQTimeEl.textContent = data.avgQueryTime + 'ms';

                var slowQEl = document.getElementById('kpi-slow-queries');
                if (slowQEl) slowQEl.textContent = data.slowQueriesCount;

                var ramEl = document.getElementById('kpi-peak-ram');
                if (ramEl) ramEl.innerHTML = data.peakMemory + ' <span style="font-size: 1rem; font-weight: 500; color: #94a3b8;">MB</span>';

                var relEl = document.getElementById('kpi-reliability');
                if (relEl) relEl.innerHTML = data.reliabilityScore + '<span style="font-size: 1rem; font-weight: 500; color: #94a3b8;">%</span>';

                var totalReqEl = document.getElementById('kpi-total-reqs');
                if (totalReqEl) totalReqEl.textContent = data.totalRequests;

                // Update Charts
                if (trendChart && data.chartLabels) {
                    trendChart.data.labels = data.chartLabels;
                    trendChart.data.datasets[0].data = data.chartLatency;
                    trendChart.data.datasets[1].data = data.chartThroughput;
                    trendChart.update();
                }

                if (statusChart) {
                    var tot = data.status2xx + data.status3xx + data.status4xx + data.status5xx;
                    statusChart.data.datasets[0].data = tot > 0 
                        ? [data.status2xx, data.status3xx, data.status4xx, data.status5xx]
                        : [1, 0, 0, 0];
                    statusChart.update();
                }

                if (icon) icon.classList.remove('fa-spin');
                if (text) text.textContent = '{{ __("messages.perf_refresh_now") }}';
            })
            .catch(function(err) {
                console.error('Failed to refresh metrics:', err);
                if (icon) icon.classList.remove('fa-spin');
                if (text) text.textContent = '{{ __("messages.perf_refresh_now") }}';
            });
    };
});
</script>
@endpush
