@extends('layouts.admin')

@section('title', __('messages.network_anomaly_title'))

@section('content')
<style>
    .anomaly-metric-card {
        background: var(--color-paper-0);
        border: var(--rule-soft);
        border-radius: var(--radius-lg);
        padding: 18px 20px;
        box-shadow: var(--shadow-card);
        transition: transform .2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow .2s;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .anomaly-metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
    }
    .metric-title {
        font-size: 11px;
        letter-spacing: .6px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .metric-value {
        font-size: 1.6rem;
        font-weight: 800;
        line-height: 1.2;
        margin: 6px 0 4px;
    }
    
    /* Scrollable Table Container */
    .anomaly-scroll-container {
        max-height: calc(100vh - 220px);
        overflow-y: auto;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        position: relative;
    }
    .anomaly-scroll-container::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .anomaly-scroll-container::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.15);
        border-radius: 8px;
    }
    .anomaly-scroll-container::-webkit-scrollbar-thumb {
        background: var(--color-accent-soft, #3b82f6);
        border-radius: 8px;
    }
    
    /* Header layout and filter pills */
    .anomaly-card-header {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 16px 20px;
        flex-wrap: wrap;
    }
    .anomaly-header-title {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0;
    }
    .anomaly-filter-tabs {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 2px;
    }
    .anomaly-filter-tabs::-webkit-scrollbar {
        display: none;
    }
    .anomaly-pill-btn {
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px;
        padding: 6px 14px !important;
        border-radius: 20px !important;
        font-size: 0.8rem !important;
        font-weight: 600 !important;
        white-space: nowrap !important;
        text-decoration: none !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        border: 1px solid rgba(148, 163, 184, 0.2) !important;
        background: rgba(15, 23, 42, 0.3) !important;
        color: #94a3b8 !important;
        cursor: pointer;
        flex-shrink: 0;
    }
    .anomaly-pill-btn:hover {
        transform: translateY(-1px);
        color: #f8fafc !important;
        background: rgba(30, 41, 59, 0.7) !important;
        border-color: rgba(148, 163, 184, 0.4) !important;
    }
    .anomaly-pill-btn .pill-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 2px 7px;
        border-radius: 10px;
        font-size: 0.72rem;
        font-weight: 700;
        background: rgba(255, 255, 255, 0.08);
    }
    .anomaly-pill-btn.active-all {
        background: rgba(59, 130, 246, 0.18) !important;
        border-color: rgba(59, 130, 246, 0.5) !important;
        color: #60a5fa !important;
        box-shadow: 0 0 12px rgba(59, 130, 246, 0.25);
    }
    .anomaly-pill-btn.active-all .pill-count {
        background: rgba(59, 130, 246, 0.3);
        color: #ffffff;
    }
    .anomaly-pill-btn.active-critical {
        background: rgba(239, 68, 68, 0.18) !important;
        border-color: rgba(239, 68, 68, 0.5) !important;
        color: #f87171 !important;
        box-shadow: 0 0 12px rgba(239, 68, 68, 0.25);
    }
    .anomaly-pill-btn.active-critical .pill-count {
        background: rgba(239, 68, 68, 0.3);
        color: #ffffff;
    }
    .anomaly-pill-btn.active-warning {
        background: rgba(245, 158, 11, 0.18) !important;
        border-color: rgba(245, 158, 11, 0.5) !important;
        color: #fbbf24 !important;
        box-shadow: 0 0 12px rgba(245, 158, 11, 0.25);
    }
    .anomaly-pill-btn.active-warning .pill-count {
        background: rgba(245, 158, 11, 0.3);
        color: #ffffff;
    }

    /* Anomaly Table & Sticky Action Column */
    .anomaly-table {
        min-width: 920px;
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    .anomaly-table thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background: var(--color-paper-1, #1e293b) !important;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        white-space: nowrap !important;
        padding: 14px 16px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.15) !important;
    }
    .anomaly-table tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        white-space: nowrap;
        background: var(--color-paper-0, #0f172a);
    }
    /* Reset global mobile sticky first column so ANOMALY TYPE can scroll sideways naturally */
    .anomaly-table th:first-child,
    .anomaly-table td:first-child,
    .table-responsive .anomaly-table th:first-child,
    .table-responsive .anomaly-table td:first-child,
    .card-body.p-0 > .anomaly-table th:first-child,
    .card-body.p-0 > .anomaly-table td:first-child {
        position: static !important;
        left: auto !important;
        box-shadow: none !important;
        z-index: auto !important;
    }

    /* ONLY Action Column Sticky Right */
    .anomaly-table th.col-sticky-action {
        position: sticky !important;
        right: 0 !important;
        top: 0 !important;
        z-index: 15 !important;
        background: var(--color-paper-1, #1e293b) !important;
        border-left: 1px solid rgba(148, 163, 184, 0.15) !important;
        box-shadow: -4px 0 10px rgba(0, 0, 0, 0.25);
    }
    .anomaly-table td.col-sticky-action {
        position: sticky !important;
        right: 0 !important;
        z-index: 5 !important;
        background: var(--color-paper-0, #0f172a) !important;
        border-left: 1px solid rgba(148, 163, 184, 0.15) !important;
        box-shadow: -4px 0 10px rgba(0, 0, 0, 0.25);
    }
    .anomaly-table tbody tr:hover td.col-sticky-action {
        background: var(--color-paper-1, #1e293b) !important;
    }

    .vlan-subnet-card {
        transition: transform .2s, box-shadow .2s;
    }
    .vlan-subnet-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    }

    @media (max-width: 767.98px) {
        .anomaly-card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
        }
        .anomaly-filter-tabs {
            width: 100%;
        }
    }

    @media (max-width: 575.98px) {
        .anomaly-metric-card {
            padding: 14px 16px;
        }
        .metric-value {
            font-size: 1.35rem;
        }
    }
</style>

<p class="text-muted small mb-4 mt-n2">{{ __('messages.network_anomaly_desc') }}</p>

<!-- Metric Cards Row -->
<div class="row mb-4">
    <div class="col-xl-3 col-sm-6 col-12 mb-3 mb-xl-0">
        <div class="anomaly-metric-card" style="border-left: 4px solid {{ $summary['health_color'] }} !important;">
            <div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="metric-title text-muted">{{ __('messages.network_health_score') }}</span>
                    <i class="fas fa-shield-alt text-info" style="font-size: 1.1rem;"></i>
                </div>
                <div class="metric-value" style="color: {{ $summary['health_color'] }};">
                    {{ $summary['health_score'] }}% <span style="font-size: 0.82rem; font-weight: 600;" class="badge badge-outline-secondary">{{ $summary['health_status'] }}</span>
                </div>
            </div>
            <small class="text-muted">{{ __('messages.subnet_it_performance') }}</small>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 col-12 mb-3 mb-xl-0">
        <div class="anomaly-metric-card" style="border-left: 4px solid #ef4444 !important;">
            <div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="metric-title text-danger">{{ __('messages.active_critical_anomalies') }}</span>
                    <i class="fas fa-exclamation-triangle text-danger" style="font-size: 1.1rem;"></i>
                </div>
                <div class="metric-value text-danger">{{ $summary['critical_count'] }}</div>
            </div>
            <small class="text-muted">{{ __('messages.ip_conflict_rogue') }}</small>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 col-12 mb-3 mb-xl-0">
        <div class="anomaly-metric-card" style="border-left: 4px solid #f59e0b !important;">
            <div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="metric-title text-warning">{{ __('messages.latency_warnings') }}</span>
                    <i class="fas fa-wifi text-warning" style="font-size: 1.1rem;"></i>
                </div>
                <div class="metric-value text-warning">{{ $summary['warning_count'] }}</div>
            </div>
            <small class="text-muted">{{ __('messages.high_latency_node_down') }}</small>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 col-12 mb-3 mb-xl-0">
        <div class="anomaly-metric-card">
            <div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="metric-title text-muted">{{ __('messages.avg_ping_latency') }}</span>
                    <i class="fas fa-tachometer-alt text-success" style="font-size: 1.1rem;"></i>
                </div>
                <div class="metric-value text-success">{{ $summary['avg_latency'] }} <span style="font-size: 0.9rem; font-weight: 500;">ms</span></div>
            </div>
            <small class="text-muted">{{ __('messages.total_monitored_ips') }} {{ $summary['total_ips'] }}</small>
        </div>
    </div>
</div>

<!-- VLAN Subnets Status Row -->
<div class="card theme-card shadow-sm mb-4">
    <div class="card-header bg-transparent py-3">
        <h5 class="card-title font-weight-bold mb-0 theme-text">
            <i class="fas fa-network-wired text-info mr-2"></i> {{ __('messages.subnet_utilization') }}
        </h5>
    </div>
    <div class="card-body py-3">
        <div class="row">
            @forelse($summary['vlans'] as $vlan)
                <div class="col-lg-4 col-sm-6 col-12 mb-3">
                    <div class="p-3 rounded vlan-subnet-card" style="background: var(--color-paper-1); border: var(--rule-soft);">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="theme-text" style="font-size: 0.92rem;">{{ $vlan->vlan_name }} (VLAN {{ $vlan->vlan_tag }})</strong>
                            <span class="badge badge-info px-2 py-1">{{ $vlan->ip_addresses_count }} IP</span>
                        </div>
                        <small class="text-muted d-block mb-2">Subnet: <code>{{ $vlan->subnet }}</code></small>
                        <div class="progress" style="height: 6px; background: rgba(255,255,255,0.08); border-radius: 10px;">
                            <div class="progress-bar bg-info" style="width: {{ min(100, max(15, $vlan->ip_addresses_count * 5)) }}%;"></div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-muted small py-2">{{ __('messages.no_vlans_configured') }}</div>
            @endforelse
        </div>
    </div>
</div>

<!-- Anomaly Alert Table Card -->
<div class="card theme-card shadow-sm mb-4">
    <div class="card-header bg-transparent p-0 border-bottom-0">
        <div class="anomaly-card-header">
            <h5 class="anomaly-header-title theme-text">
                <i class="fas fa-shield-virus text-danger"></i>
                <span>{{ __('messages.realtime_anomaly_list') }}</span>
            </h5>

            <!-- Filter Tabs (Horizontal Scrollable Pills) -->
            <div class="anomaly-filter-tabs">
                <a href="{{ route('network-anomalies.index', ['severity' => 'all']) }}" 
                   class="anomaly-pill-btn {{ $severityFilter == 'all' ? 'active-all' : '' }}">
                    <i class="fas fa-layer-group text-xs"></i>
                    <span>{{ __('messages.all_filter') }}</span>
                    <span class="pill-count">{{ $summary['total_anomalies'] }}</span>
                </a>
                <a href="{{ route('network-anomalies.index', ['severity' => 'Critical']) }}" 
                   class="anomaly-pill-btn {{ $severityFilter == 'Critical' ? 'active-critical' : '' }}">
                    <i class="fas fa-exclamation-circle text-xs"></i>
                    <span>Critical</span>
                    <span class="pill-count">{{ $summary['critical_count'] }}</span>
                </a>
                <a href="{{ route('network-anomalies.index', ['severity' => 'Warning']) }}" 
                   class="anomaly-pill-btn {{ $severityFilter == 'Warning' ? 'active-warning' : '' }}">
                    <i class="fas fa-wifi text-xs"></i>
                    <span>Warning</span>
                    <span class="pill-count">{{ $summary['warning_count'] }}</span>
                </a>
            </div>
        </div>
    </div>

    <div class="anomaly-scroll-container table-responsive">
        <table class="table align-middle mb-0 theme-table anomaly-table">
            <thead>
                <tr>
                    <th class="pl-4">{{ __('messages.anomaly_type') }}</th>
                    <th>{{ __('messages.risk_level') }}</th>
                    <th>{{ __('messages.ip_mac_address') }}</th>
                    <th>{{ __('messages.subnet_vlan') }}</th>
                    <th>{{ __('messages.diagnosis_description') }}</th>
                    <th>{{ __('messages.detected_time') }}</th>
                    <th class="text-right pr-4 col-sticky-action">{{ __('messages.remediation_action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($anomalies as $anom)
                    <tr>
                        <td class="pl-4">
                            <strong class="theme-text" style="font-size: 0.92rem;">{{ $anom['type'] }}</strong>
                            @if($anom['asset'])
                                <br><small class="text-info"><i class="fas fa-desktop mr-1"></i> {{ $anom['asset'] }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $anom['badge_class'] }} px-2 py-1">{{ $anom['severity'] }}</span>
                        </td>
                        <td>
                            <span class="font-weight-bold text-success" style="font-size: 0.9rem;">{{ $anom['ip_address'] }}</span>
                            <br>
                            <small class="text-muted">MAC: <code>{{ $anom['mac_address'] }}</code></small>
                        </td>
                        <td>
                            <span class="theme-text">{{ $anom['vlan'] }}</span>
                        </td>
                        <td class="col-description">
                            <small class="theme-text">{{ $anom['description'] }}</small>
                        </td>
                        <td>
                            <small class="text-muted">{{ $anom['detected_at'] }}</small>
                        </td>
                        <td class="text-right pr-4 col-sticky-action">
                            <div class="d-flex justify-content-end" style="gap: 6px;">
                                @if(str_contains($anom['type'], 'Rogue') || str_contains($anom['type'], 'Perangkat'))
                                    <button type="button" class="btn btn-sm btn-outline-info btn-register-rogue" data-ip-id="{{ $anom['ip_id'] }}" title="{{ __('messages.register_asset') }}">
                                        {{ __('messages.register_asset') }}
                                    </button>
                                @endif
                                <button type="button" class="btn btn-sm btn-outline-success btn-resolve-anomaly" data-ip-id="{{ $anom['ip_id'] }}" title="{{ __('messages.remediate') }}">
                                    <i class="fas fa-check-circle mr-1"></i> {{ __('messages.remediate') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 border-0">
                            <div class="d-flex flex-column align-items-center justify-content-center py-4 px-3 mx-auto" style="max-width: 500px; gap: 14px;">
                                <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(16, 185, 129, 0.14); border: 1px solid rgba(16, 185, 129, 0.35); display: flex; align-items: center; justify-content: center; color: #34d399; font-size: 1.75rem; box-shadow: 0 0 20px rgba(16, 185, 129, 0.25);">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="text-center">
                                    <h6 class="font-weight-bold theme-text mb-1" style="font-size: 1.05rem; color: #f8fafc;">{{ __('messages.network_condition_prime') }}</h6>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $(document).on('click', '.btn-resolve-anomaly', function() {
        const btn = $(this);
        const ipId = btn.data('ip-id');
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
                    btn.closest('tr').fadeOut(300, function() { $(this).remove(); });
                    toastr ? toastr.success(response.message) : Swal.fire({ icon: 'success', text: response.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, background: '#0f172a', color: '#f8fafc', customClass: { popup: 'border border-success' } });
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i> {{ __("messages.remediate") }}');
                Swal.fire({ icon: 'error', text: 'Gagal menyelesaikan anomali.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, background: '#0f172a', color: '#f8fafc', customClass: { popup: 'border border-danger' } });
            }
        });
    });

    $(document).on('click', '.btn-register-rogue', function() {
        const ipId = $(this).data('ip-id');
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
                }
            }
        });
    });
});
</script>
@endpush
@endsection
