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
        transition: transform .2s, box-shadow .2s;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .anomaly-metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }
    .metric-title {
        font-size: 11px;
        letter-spacing: .6px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .metric-value {
        font-size: 1.55rem;
        font-weight: 800;
        line-height: 1.2;
        margin: 6px 0 4px;
    }
    .anomaly-scroll-container {
        max-height: calc(100vh - 220px);
        overflow-y: auto;
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
    .anomaly-scroll-container thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background: var(--color-paper-1, #1e293b) !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
</style>

<p class="text-muted small mb-4 mt-n2">{{ __('messages.network_anomaly_desc') }}</p>

<!-- Metric Cards Row -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="anomaly-metric-card" style="border-left: 4px solid {{ $summary['health_color'] }} !important;">
            <div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="metric-title text-muted">{{ __('messages.network_health_score') }}</span>
                    <i class="fas fa-shield-alt text-info" style="font-size: 1.1rem;"></i>
                </div>
                <div class="metric-value" style="color: {{ $summary['health_color'] }};">
                    {{ $summary['health_score'] }}% <span style="font-size: 0.85rem; font-weight: 600;" class="badge badge-outline-secondary">{{ $summary['health_status'] }}</span>
                </div>
            </div>
            <small class="text-muted">{{ __('messages.subnet_it_performance') }}</small>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
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

    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
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

    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
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
                <div class="col-md-4 mb-3">
                    <div class="p-3 rounded" style="background: var(--color-paper-1); border: var(--rule-soft);">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="theme-text">{{ $vlan->vlan_name }} (VLAN {{ $vlan->vlan_tag }})</strong>
                            <span class="badge badge-info">{{ $vlan->ip_addresses_count }} IP</span>
                        </div>
                        <small class="text-muted d-block mb-2">Subnet: <code>{{ $vlan->subnet }}</code></small>
                        <div class="progress" style="height: 6px; background: rgba(255,255,255,0.08); border-radius: 10px;">
                            <div class="progress-bar bg-info" style="width: {{ min(100, max(15, $vlan->ip_addresses_count * 5)) }}%;"></div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-muted small">{{ __('messages.no_vlans_configured') }}</div>
            @endforelse
        </div>
    </div>
</div>

<!-- Anomaly Alert Table Card -->
<div class="card theme-card shadow-sm mb-4">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
        <h5 class="card-title font-weight-bold mb-0 theme-text">
            <i class="fas fa-shield-virus mr-2 text-danger"></i> {{ __('messages.realtime_anomaly_list') }}
        </h5>

        <!-- Filter Tabs -->
        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
            <a href="{{ route('network-anomalies.index', ['severity' => 'all']) }}" class="btn btn-sm {{ $severityFilter == 'all' ? 'btn-info' : 'btn-outline-secondary' }}" style="border-radius: 20px; font-weight: 600; padding: 4px 14px;">{{ __('messages.all_filter') }} ({{ $summary['total_anomalies'] }})</a>
            <a href="{{ route('network-anomalies.index', ['severity' => 'Critical']) }}" class="btn btn-sm {{ $severityFilter == 'Critical' ? 'btn-danger' : 'btn-outline-danger' }}" style="border-radius: 20px; font-weight: 600; padding: 4px 14px;">Critical ({{ $summary['critical_count'] }})</a>
            <a href="{{ route('network-anomalies.index', ['severity' => 'Warning']) }}" class="btn btn-sm {{ $severityFilter == 'Warning' ? 'btn-warning' : 'btn-outline-warning' }}" style="border-radius: 20px; font-weight: 600; padding: 4px 14px;">Warning ({{ $summary['warning_count'] }})</a>
        </div>
    </div>

    <div class="anomaly-scroll-container table-responsive">
        <table class="table table-hover align-middle mb-0 theme-table">
            <thead class="bg-light">
                <tr>
                    <th class="pl-4">{{ __('messages.anomaly_type') }}</th>
                    <th>{{ __('messages.risk_level') }}</th>
                    <th>{{ __('messages.ip_mac_address') }}</th>
                    <th>{{ __('messages.subnet_vlan') }}</th>
                    <th>{{ __('messages.diagnosis_description') }}</th>
                    <th>{{ __('messages.detected_time') }}</th>
                    <th class="text-right pr-4">{{ __('messages.remediation_action') }}</th>
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
                        <td style="max-width: 280px;">
                            <small class="theme-text">{{ $anom['description'] }}</small>
                        </td>
                        <td>
                            <small class="text-muted">{{ $anom['detected_at'] }}</small>
                        </td>
                        <td class="text-right pr-4">
                            <div class="d-flex justify-content-end" style="gap: 6px;">
                                @if(str_contains($anom['type'], 'Rogue') || str_contains($anom['type'], 'Perangkat'))
                                    <button type="button" class="btn btn-sm btn-outline-info btn-register-rogue" data-ip-id="{{ $anom['ip_id'] }}" title="{{ __('messages.register_asset') }}">
                                        <i class="fas fa-plus-circle mr-1"></i> {{ __('messages.register_asset') }}
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
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle text-success mr-1"></i> {{ __('messages.network_condition_prime') }}
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
                    toastr ? toastr.success(response.message) : alert(response.message);
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i> {{ __("messages.remediate") }}');
                alert('Gagal menyelesaikan anomali.');
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
