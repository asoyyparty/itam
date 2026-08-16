@extends('layouts.admin')

@section('title', __('messages.budget_planner_title'))

@section('content')
<style>
    .budget-metric-card {
        background: var(--color-paper-0);
        border: var(--rule-soft);
        border-radius: var(--radius-lg);
        padding: 20px;
        box-shadow: var(--shadow-card);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .budget-value {
        font-size: 1.45rem;
        font-weight: 800;
        margin: 8px 0 4px;
    }
    .year-pill-btn {
        border-radius: 20px;
        font-weight: 600;
        padding: 5px 18px;
    }
    .timeline-card {
        border-radius: 16px;
        padding: 18px 14px;
        transition: transform .2s ease, box-shadow .2s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .timeline-card:hover {
        transform: translateY(-3px);
    }
    .budget-scroll-container {
        max-height: calc(100vh - 220px);
        overflow-y: auto;
        overflow-x: auto;
    }
    .budget-scroll-container::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .budget-scroll-container::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.15);
        border-radius: 8px;
    }
    .budget-scroll-container::-webkit-scrollbar-thumb {
        background: var(--color-accent-soft, #3b82f6);
        border-radius: 8px;
    }
    .budget-scroll-container thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background: var(--color-paper-1, #1e293b) !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    @media (max-width: 768px) {
        .year-pill-btn {
            padding: 4px 10px;
            font-size: 0.8rem;
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

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap" style="gap: 12px;">
    <div>
        <p class="text-muted small mb-0">{{ __('messages.budget_planner_desc') }}</p>
    </div>
    <div class="d-flex align-items-center" style="gap: 8px;">
        <span class="text-muted small font-weight-bold mr-1"><i class="fas fa-calendar-alt mr-1"></i> {{ __('messages.fiscal_year') }}:</span>
        @foreach([2026, 2027, 2028, 2029, 2030] as $yr)
            <a href="{{ route('budget-planner.index', ['year' => $yr]) }}" class="btn btn-sm year-pill-btn {{ $selectedYear == $yr ? 'btn-primary' : 'btn-outline-secondary' }}">
                {{ $yr }}
            </a>
        @endforeach
        <a href="{{ route('budget-planner.export', ['year' => $selectedYear]) }}" class="btn btn-sm btn-success year-pill-btn ml-2" title="{{ __('messages.export_budget') }} CSV">
            <i class="fas fa-file-excel mr-1"></i> {{ __('messages.export_budget') }} {{ $selectedYear }}
        </a>
    </div>
</div>

<!-- Metric Cards Row -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="budget-metric-card" style="border-left: 4px solid #3b82f6 !important;">
            <div>
                <span class="text-muted small font-weight-bold text-uppercase">{{ __('messages.total_projected_budget') }} {{ $selectedYear }}</span>
                <div class="budget-value text-info">Rp {{ number_format($current['grand_total'], 0, ',', '.') }}</div>
            </div>
            <small class="text-muted">{{ __('messages.total_it_forecast') }}</small>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="budget-metric-card" style="border-left: 4px solid #ef4444 !important;">
            <div>
                <span class="text-muted small font-weight-bold text-uppercase">{{ __('messages.unit_replacement_budget') }}</span>
                <div class="budget-value text-danger">Rp {{ number_format($current['replacement_cost'], 0, ',', '.') }}</div>
            </div>
            <small class="text-muted">{{ $current['replacement_count'] }} {{ __('messages.replacement_units') }}</small>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="budget-metric-card" style="border-left: 4px solid #f59e0b !important;">
            <div>
                <span class="text-muted small font-weight-bold text-uppercase">{{ __('messages.maintenance_service_budget') }}</span>
                <div class="budget-value text-warning">Rp {{ number_format($current['maintenance_cost'], 0, ',', '.') }}</div>
            </div>
            <small class="text-muted">{{ __('messages.service_repairs') }}</small>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="budget-metric-card" style="border-left: 4px solid #10b981 !important;">
            <div>
                <span class="text-muted small font-weight-bold text-uppercase">{{ __('messages.software_license_renewals') }}</span>
                <div class="budget-value text-success">Rp {{ number_format($current['license_cost'], 0, ',', '.') }}</div>
            </div>
            <small class="text-muted">{{ __('messages.os_office_antivirus') }}</small>
        </div>
    </div>
</div>

<!-- 5-Year Projection Bar Comparison Card -->
<div class="card theme-card shadow-sm mb-4">
    <div class="card-header bg-transparent py-3">
        <h5 class="card-title font-weight-bold mb-0 theme-text">
            <i class="fas fa-chart-line text-info mr-2"></i> {{ __('messages.five_year_timeline') }}
        </h5>
    </div>
    <div class="card-body py-4">
        <div class="row">
            @foreach($projections as $yr => $proj)
                @php
                    $isActive = ($selectedYear == $yr);
                    $formattedTotal = formatBudgetString($proj['grand_total']);
                @endphp
                <div class="col-md mb-3 mb-md-0">
                    <div class="timeline-card text-center" 
                         style="{{ $isActive 
                                     ? 'background: linear-gradient(135deg, #1e40af, #2563eb); border: 1px solid #3b82f6; box-shadow: 0 6px 20px rgba(37, 99, 235, 0.35);' 
                                     : 'background: var(--color-paper-1); border: 1px solid rgba(255, 255, 255, 0.08);' }}">
                        <div>
                            <h6 class="font-weight-bold mb-2" style="{{ $isActive ? 'color: #ffffff;' : 'color: var(--color-paper-contrast);' }} font-size: 1.1rem;">
                                {{ $yr }}
                            </h6>
                            <div class="font-weight-extrabold mb-1" style="{{ $isActive ? 'color: #93c5fd;' : 'color: #3b82f6;' }} font-size: 1.15rem; font-weight: 800;">
                                Rp {{ $formattedTotal }}
                            </div>
                            <small class="d-block mb-3" style="{{ $isActive ? 'color: #e0e7ff;' : 'color: #9ca3af;' }} font-weight: 500;">
                                <i class="fas fa-microchip mr-1"></i> {{ $proj['replacement_count'] }} {{ __('messages.replacement_units') }}
                            </small>
                        </div>
                        <div>
                            @if($isActive)
                                <span class="btn btn-xs font-weight-bold w-100" style="background: #ffffff; color: #1e3a8a; border-radius: 20px; padding: 5px 12px; font-weight: 700; box-shadow: 0 2px 6px rgba(0,0,0,0.2);">
                                    <i class="fas fa-check-circle mr-1 text-primary"></i> {{ __('messages.complete') }}
                                </span>
                            @else
                                <a href="{{ route('budget-planner.index', ['year' => $yr]) }}" class="btn btn-xs btn-outline-info font-weight-bold w-100" style="border-radius: 20px; padding: 5px 12px;">
                                    {{ __('messages.more_info') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="row">
    <!-- Department Budget Allocation Table -->
    <div class="col-lg-6 mb-4">
        <div class="card theme-card shadow-sm h-100">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title font-weight-bold mb-0 theme-text">
                    <i class="fas fa-sitemap text-warning mr-2"></i> {{ __('messages.dept_allocation_breakdown') }} ({{ $selectedYear }})
                </h5>
                <span class="badge badge-warning px-2 py-1" style="border-radius: 12px;">{{ count($deptBreakdown) }} {{ __('messages.dept_short') }}</span>
            </div>
            <div class="budget-scroll-container table-responsive">
                <table class="table table-hover align-middle mb-0 theme-table">
                    <thead class="bg-light">
                        <tr>
                            <th class="pl-4">{{ __('messages.department') }}</th>
                            <th class="text-center">{{ __('messages.employee') }}</th>
                            <th class="text-center">{{ __('messages.asset') }}</th>
                            <th>{{ __('messages.estimated_cost') }}</th>
                            <th class="text-right pr-4">{{ __('messages.ratio') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deptBreakdown as $dept)
                            <tr>
                                <td class="pl-4">
                                    <strong class="theme-text">{{ $dept['department_name'] }}</strong>
                                </td>
                                <td class="text-center">{{ $dept['employee_count'] }}</td>
                                <td class="text-center">{{ $dept['asset_count'] }}</td>
                                <td>
                                    <strong class="text-info">Rp {{ number_format($dept['allocated_budget'], 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-right pr-4">
                                    <span class="badge badge-info px-2 py-1" style="border-radius: 12px; font-weight: 600;">{{ $dept['percentage'] }}%</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">{{ __('messages.no_data') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scheduled Asset Replacements Table -->
    <div class="col-lg-6 mb-4">
        <div class="card theme-card shadow-sm h-100">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title font-weight-bold mb-0 theme-text">
                    <i class="fas fa-sync-alt text-danger mr-2"></i> {{ __('messages.scheduled_replacements') }} ({{ $selectedYear }})
                </h5>
                <span class="badge badge-danger px-2 py-1" style="border-radius: 12px;">{{ count($current['replacement_assets']) }} {{ __('messages.unit') }}</span>
            </div>
            <div class="budget-scroll-container table-responsive">
                <table class="table table-hover align-middle mb-0 theme-table">
                    <thead class="bg-light">
                        <tr>
                            <th class="pl-4">{{ __('messages.asset_name') }}</th>
                            <th>{{ __('messages.category') }}</th>
                            <th>{{ __('messages.department') }}</th>
                            <th class="text-right pr-4">{{ __('messages.estimated_unit_price') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($current['replacement_assets'] as $asset)
                            <tr>
                                <td class="pl-4">
                                    <strong class="theme-text" style="font-size: 0.9rem;">{{ $asset['asset_name'] }}</strong>
                                    <br><small class="text-muted">Tag: <code>{{ $asset['asset_tag'] }}</code></small>
                                </td>
                                <td>
                                    <span class="badge badge-secondary px-2 py-1" style="border-radius: 12px;">{{ $asset['category'] }}</span>
                                </td>
                                <td>
                                    <small class="theme-text">{{ $asset['department'] }}</small>
                                </td>
                                <td class="text-right pr-4">
                                    <strong class="text-danger">Rp {{ number_format($asset['estimated_cost'], 0, ',', '.') }}</strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">{{ __('messages.no_replacement_assets_year', ['year' => $selectedYear]) }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
