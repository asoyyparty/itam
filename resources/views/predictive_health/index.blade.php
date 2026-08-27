@extends('layouts.admin')

@section('title', __('messages.predictive_health_title'))

@section('content')
<style>
    .health-metric-card {
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
    .health-metric-card:hover {
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
    .health-score-bar-bg {
        background: rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        height: 8px;
        overflow: hidden;
    }
    .health-score-bar-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 0.6s ease-in-out;
    }
    .health-scroll-container {
        max-height: calc(100vh - 220px);
        overflow-y: auto;
    }
    .health-scroll-container::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .health-scroll-container::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.15);
        border-radius: 8px;
    }
    .health-scroll-container::-webkit-scrollbar-thumb {
        background: var(--color-accent-soft, #3b82f6);
        border-radius: 8px;
    }
    .health-scroll-container thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background: var(--color-paper-1, #1e293b) !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
</style>

<p class="text-muted small mb-4 mt-n2">{{ __('messages.predictive_health_desc') }}</p>

<!-- Metric Cards Row -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="health-metric-card">
            <div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="metric-title text-muted">{{ __('messages.total_analyzed_assets') }}</span>
                    <i class="fas fa-microchip text-info" style="font-size: 1.1rem;"></i>
                </div>
                <div class="metric-value theme-text">{{ $summary['total_assets'] }} <span style="font-size: 0.9rem; font-weight: 500;">{{ __('messages.unit') }}</span></div>
            </div>
            <small class="text-muted">{{ __('messages.registered_assets_desc') }}</small>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="health-metric-card" style="border-left: 4px solid #ef4444 !important;">
            <div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="metric-title text-danger">{{ __('messages.high_risk_critical') }}</span>
                    <i class="fas fa-exclamation-triangle text-danger" style="font-size: 1.1rem;"></i>
                </div>
                <div class="metric-value text-danger">{{ $summary['critical_count'] }} <span style="font-size: 0.9rem; font-weight: 500;">{{ __('messages.unit') }}</span></div>
            </div>
            <small class="text-muted">{{ __('messages.critical_risk_desc') }}</small>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="health-metric-card" style="border-left: 4px solid #f59e0b !important;">
            <div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="metric-title text-warning">{{ __('messages.warning_level') }}</span>
                    <i class="fas fa-shield-virus text-warning" style="font-size: 1.1rem;"></i>
                </div>
                <div class="metric-value text-warning">{{ $summary['warning_count'] }} <span style="font-size: 0.9rem; font-weight: 500;">{{ __('messages.unit') }}</span></div>
            </div>
            <small class="text-muted">{{ __('messages.warning_risk_desc') }}</small>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="health-metric-card" style="border-left: 4px solid #10b981 !important;">
            <div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="metric-title text-success">{{ __('messages.est_replacement_budget') }}</span>
                    <i class="fas fa-coins text-success" style="font-size: 1.1rem;"></i>
                </div>
                <div class="metric-value text-success" style="font-size: 1.35rem;">Rp {{ number_format($summary['estimated_replacement_budget'], 0, ',', '.') }}</div>
            </div>
            <small class="text-muted">{{ __('messages.est_budget_desc') }}</small>
        </div>
    </div>
</div>

<!-- Asset Health Table Card -->
<div class="card theme-card shadow-sm mb-4">
    <style>
        .filter-tabs-container::-webkit-scrollbar { display: none; }
        .mobile-health-card {
            background: var(--color-paper-0);
            border: var(--rule-soft);
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        body.dark-mode .mobile-health-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
        }
    </style>

    <div class="card-header bg-transparent d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center py-3">
        <h5 class="card-title font-weight-bold mb-3 mb-md-0 theme-text">
            <i class="fas fa-list-ol mr-2 text-info"></i> {{ __('messages.asset_health_risk_list') }}
        </h5>
        
        <!-- Filter Tabs -->
        <div class="filter-tabs-container" style="width: 100%; overflow-x: auto; white-space: nowrap; -webkit-overflow-scrolling: touch; padding-bottom: 8px;">
            <a href="{{ route('predictive-health.index', ['status' => 'all']) }}" class="btn btn-sm {{ $statusFilter == 'all' ? 'btn-info' : 'btn-outline-secondary' }}" style="display: inline-block; border-radius: 20px; font-weight: 600; padding: 4px 14px; margin-right: 6px;">{{ __('messages.all') }} ({{ $summary['total_assets'] }})</a>
            <a href="{{ route('predictive-health.index', ['status' => 'Critical']) }}" class="btn btn-sm {{ $statusFilter == 'Critical' ? 'btn-danger' : 'btn-outline-danger' }}" style="display: inline-block; border-radius: 20px; font-weight: 600; padding: 4px 14px; margin-right: 6px;">Critical ({{ $summary['critical_count'] }})</a>
            <a href="{{ route('predictive-health.index', ['status' => 'Warning']) }}" class="btn btn-sm {{ $statusFilter == 'Warning' ? 'btn-warning' : 'btn-outline-warning' }}" style="display: inline-block; border-radius: 20px; font-weight: 600; padding: 4px 14px; margin-right: 6px;">Warning ({{ $summary['warning_count'] }})</a>
            <a href="{{ route('predictive-health.index', ['status' => 'Healthy']) }}" class="btn btn-sm {{ $statusFilter == 'Healthy' ? 'btn-success' : 'btn-outline-success' }}" style="display: inline-block; border-radius: 20px; font-weight: 600; padding: 4px 14px;">Healthy ({{ $summary['healthy_count'] }})</a>
        </div>
    </div>

    <!-- Desktop Table View -->
    <div class="health-scroll-container table-responsive d-none d-md-block">
        <table class="table table-hover align-middle mb-0 theme-table">
            <thead class="bg-light">
                <tr>
                    <th class="pl-4">{{ __('messages.asset_and_tag') }}</th>
                    <th>{{ __('messages.category_and_brand') }}</th>
                    <th>{{ __('messages.assigned_employee') }}</th>
                    <th>{{ __('messages.health_score_header') }}</th>
                    <th>{{ __('messages.remaining_useful_life') }}</th>
                    <th class="text-right pr-4">{{ __('messages.ai_audit_action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assets as $asset)
                    @php $h = $asset->health_data; @endphp
                    <tr>
                        <td class="pl-4">
                            <a href="{{ route('assets.show', $asset->id) }}" class="font-weight-bold theme-text text-decoration-none">
                                {{ $asset->name }}
                            </a>
                            <br>
                            <span class="badge badge-secondary px-2 py-1 small">{{ $asset->asset_tag }}</span>
                        </td>
                        <td>
                            <span class="theme-text">{{ $asset->category->name ?? __('messages.asset') }}</span>
                            <br>
                            <small class="text-muted">{{ $asset->brand->name ?? '-' }}</small>
                        </td>
                        <td>
                            @if($asset->currentAssignment && $asset->currentAssignment->employee)
                                <span class="theme-text"><i class="far fa-user mr-1 text-info"></i> {{ $asset->currentAssignment->employee->name }}</span>
                            @else
                                <span class="badge badge-outline-secondary">{{ __('messages.not_assigned') }}</span>
                            @endif
                        </td>
                        <td style="min-width: 220px;">
                            <div class="d-flex align-items-center mb-1" style="gap: 8px;">
                                <strong style="color: {{ $h['text_color'] }}; font-size: 1rem;">{{ $h['health_score'] }}%</strong>
                                <span class="badge {{ $h['badge_class'] }} px-2 py-1">{{ $h['status'] }}</span>
                            </div>
                            <div class="health-score-bar-bg">
                                <div class="health-score-bar-fill" style="width: {{ $h['health_score'] }}%; background-color: {{ $h['text_color'] }};"></div>
                            </div>
                        </td>
                        <td>
                            <span class="font-weight-bold theme-text" style="font-size: 0.875rem;">{{ $h['remaining_life'] }}</span>
                            <br>
                            <small class="text-muted">{{ __('messages.age') }}: {{ $h['age_years'] }} {{ __('messages.years_short') }}</small>
                        </td>
                        <td class="text-right pr-4">
                            <button type="button" class="btn btn-sm btn-outline-info btn-audit-asset" data-asset-id="{{ $asset->id }}">
                                <i class="fas fa-magic mr-1"></i> {{ __('messages.ai_audit') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            {{ __('messages.no_data') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Mobile Feed View -->
    <div class="d-block d-md-none p-3" style="max-height: calc(100vh - 220px); overflow-y: auto; -webkit-overflow-scrolling: touch;">
        @forelse($assets as $asset)
            @php $h = $asset->health_data; @endphp
            <div class="mobile-health-card p-3 mb-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div style="flex: 1; padding-right: 10px;">
                        <a href="{{ route('assets.show', $asset->id) }}" class="font-weight-bold theme-text d-block text-truncate" style="font-size: 0.95rem; max-width: 200px;">
                            {{ $asset->name }}
                        </a>
                        <div class="mt-1 d-flex flex-wrap" style="gap: 4px;">
                            <span class="badge badge-secondary px-2 py-1" style="font-size: 0.7rem;">{{ $asset->asset_tag }}</span>
                            <span class="badge badge-outline-secondary px-2 py-1" style="font-size: 0.7rem;">{{ $asset->category->name ?? __('messages.asset') }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <strong style="color: {{ $h['text_color'] }}; font-size: 1.1rem;">{{ $h['health_score'] }}%</strong>
                        <div class="mt-1">
                            <span class="badge {{ $h['badge_class'] }} px-2 py-1" style="font-size: 0.7rem;">{{ $h['status'] }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="health-score-bar-bg my-3" style="height: 6px;">
                    <div class="health-score-bar-fill" style="width: {{ $h['health_score'] }}%; background-color: {{ $h['text_color'] }};"></div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div style="font-size: 0.8rem;">
                        <div class="text-muted mb-1">{{ __('messages.remaining_useful_life') }}: <span class="font-weight-bold theme-text">{{ $h['remaining_life'] }}</span></div>
                        <div class="text-muted">
                            <i class="far fa-user mr-1"></i> 
                            @if($asset->currentAssignment && $asset->currentAssignment->employee)
                                {{ $asset->currentAssignment->employee->name }}
                            @else
                                {{ __('messages.not_assigned') }}
                            @endif
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn btn-sm btn-outline-info btn-block btn-audit-asset" data-asset-id="{{ $asset->id }}" style="border-radius: 8px;">
                    <i class="fas fa-magic mr-1"></i> {{ __('messages.ai_audit') }}
                </button>
            </div>
        @empty
            <div class="text-center py-4 text-muted">
                {{ __('messages.no_data') }}
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Audit Kesehatan Aset AI -->
<div class="modal fade" id="assetHealthModal" tabindex="-1" role="dialog" aria-labelledby="assetHealthModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg asset-health-modal-dialog" role="document">
        <div class="modal-content" style="border-radius: var(--radius-xl); background: var(--color-paper-0); border: 1px solid var(--color-accent-soft);">
            <div class="modal-header d-flex justify-content-between align-items-center py-3 px-3 px-md-4" style="border-bottom: var(--rule-soft);">
                <h5 class="modal-title font-weight-bold m-0" id="assetHealthModalLabel" style="color: var(--color-accent);">
                    <i class="fas fa-heartbeat mr-2"></i> {{ __('messages.ai_health_audit_report') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <div id="modal-loading-spinner" class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-2x text-info mb-2"></i>
                    <p class="text-muted small mb-0">{{ __('messages.analyzing_health_diagnosis') }}</p>
                </div>

                <div id="modal-health-content" style="display: none;">
                    <!-- Audit Summary Box -->
                    <div class="audit-summary-box mb-4">
                        <div class="audit-info-left">
                            <h4 id="audit-asset-name" class="font-weight-bold theme-text mb-1"></h4>
                            <p id="audit-asset-sub" class="text-muted small mb-0"></p>
                        </div>
                        <div class="audit-score-wrapper text-md-right">
                            <span class="small text-muted d-block uppercase font-weight-bold mb-1">{{ __('messages.health_score_title') }}</span>
                            <div class="d-flex align-items-center justify-content-md-end">
                                <span id="audit-score-number" class="audit-score-number font-weight-bold"></span>
                                <span id="audit-status-badge" class="badge px-3 py-2 ml-2" style="font-size: 0.85rem;"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Risk Factors Checklist -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold text-info mb-2 mb-md-3 fs-7"><i class="fas fa-stethoscope mr-2"></i> {{ __('messages.risk_factors_degradation') }}:</h6>
                        <ul id="audit-reasons-list" class="list-group list-group-flush shadow-sm" style="border-radius: var(--radius-md);">
                        </ul>
                    </div>

                    <!-- AI Recommendation Box -->
                    <div class="p-3 rounded audit-recommendation-box" style="background: color-mix(in oklch, var(--color-accent-tint) 25%, var(--color-paper-0)); border: 1px solid var(--color-accent-soft);">
                        <h6 class="font-weight-bold text-info mb-2 fs-7"><i class="fas fa-lightbulb mr-2"></i> {{ __('messages.ai_action_recommendation') }}:</h6>
                        <p id="audit-recommendation-text" class="theme-text mb-0" style="font-size: 0.88rem; line-height: 1.5;"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.asset-health-modal-dialog {
    max-width: 720px;
}

.audit-summary-box {
    background: var(--color-paper-1, #1e293b);
    border: 1px solid var(--rule-soft, rgba(255, 255, 255, 0.1));
    border-radius: 12px;
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
}

.audit-score-number {
    font-size: 2.4rem;
    font-weight: 800;
    line-height: 1;
}

@media (max-width: 767.98px) {
    #assetHealthModal .modal-dialog {
        margin: 10px auto;
        max-width: calc(100% - 20px);
    }

    #assetHealthModal .modal-title {
        font-size: 0.92rem !important;
    }

    .audit-summary-box {
        flex-direction: column;
        align-items: flex-start;
        padding: 14px 14px;
        gap: 12px;
    }

    .audit-score-wrapper {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 10px;
        border-top: 1px dashed rgba(255, 255, 255, 0.12);
    }

    .audit-score-number {
        font-size: 1.85rem;
    }

    #audit-asset-name {
        font-size: 0.98rem !important;
        word-break: break-word;
    }

    #audit-asset-sub {
        font-size: 0.78rem !important;
        line-height: 1.4;
    }

    .audit-recommendation-box {
        padding: 12px 12px !important;
    }

    #audit-recommendation-text {
        font-size: 0.84rem !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    const langBrand = "{{ __('messages.brand') }}";
    const langLocation = "{{ __('messages.location') }}";
    const langUser = "{{ __('messages.assigned_to') }}";
    const langNoRisk = "{{ __('messages.no_significant_risk_factors') }}";

    $(document).on('click', '.btn-audit-asset', function() {
        const assetId = $(this).data('asset-id');
        const modal = $('#assetHealthModal');
        
        $('#modal-loading-spinner').show();
        $('#modal-health-content').hide();
        modal.modal('show');

        $.ajax({
            url: "/predictive-health/" + assetId + "/analyze",
            type: "GET",
            success: function(response) {
                if (response.success) {
                    const a = response.asset;
                    const h = response.health;

                    $('#audit-asset-name').text(a.name + ' [' + a.asset_tag + ']');
                    $('#audit-asset-sub').text(a.category + ' • ' + langBrand + ': ' + a.brand + ' • ' + langLocation + ': ' + a.location + ' • ' + langUser + ': ' + a.user);
                    $('#audit-score-number').text(h.health_score + '%').css('color', h.text_color);
                    $('#audit-status-badge').attr('class', 'badge ' + h.badge_class).text(h.status);

                    let reasonsHtml = '';
                    if (h.reasons && h.reasons.length > 0) {
                        h.reasons.forEach(reason => {
                            reasonsHtml += `<li class="list-group-item bg-transparent text-danger border-bottom-0 py-2 px-2 px-md-3" style="font-size: 0.85rem;"><i class="fas fa-exclamation-circle mr-2"></i> ${reason}</li>`;
                        });
                    } else {
                        reasonsHtml = `<li class="list-group-item bg-transparent text-success border-bottom-0 py-2 px-2 px-md-3" style="font-size: 0.85rem;"><i class="fas fa-check-circle mr-2"></i> ${langNoRisk}</li>`;
                    }
                    $('#audit-reasons-list').html(reasonsHtml);
                    $('#audit-recommendation-text').text(h.recommendation);

                    $('#modal-loading-spinner').hide();
                    $('#modal-health-content').fadeIn();
                }
            },
            error: function(err) {
                console.error(err);
                modal.modal('hide');
            }
        });
    });
});
</script>
@endpush
@endsection
