@extends('layouts.admin')

@section('title', __('messages.asset_details') . ' - ' . $asset->asset_tag)

@push('styles')
<style>
    /* Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 · genre: modern-minimal · theme: Midnight */
    .master-header-banner {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.75), rgba(30, 41, 59, 0.55));
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 20px;
        padding: 18px 24px;
        backdrop-filter: blur(16px);
        margin-top: 0 !important;
        margin-bottom: 22px !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        overflow: hidden !important;
        box-sizing: border-box !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    body.light-mode .master-header-banner {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(241, 245, 249, 0.85));
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
    }

    .asset-header-flex {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }

    .asset-show-identity {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        min-width: 0;
        flex: 1 1 320px;
        max-width: 100%;
        box-sizing: border-box;
    }

    .asset-show-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(79, 70, 229, 0.08));
        border: 1px solid rgba(99, 102, 241, 0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #818cf8;
        font-size: 1.2rem;
        flex-shrink: 0;
        box-shadow: 0 0 14px rgba(99, 102, 241, 0.25);
        margin-top: 2px;
    }
    body.light-mode .asset-show-icon {
        background: rgba(99, 102, 241, 0.12);
        border-color: rgba(99, 102, 241, 0.3);
        color: #4f46e5;
    }

    .asset-show-meta {
        flex: 1 1 0%;
        min-width: 0;
        max-width: 100%;
        box-sizing: border-box;
    }

    .asset-show-title-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px 10px;
        width: 100%;
        min-width: 0;
        margin-bottom: 4px;
    }

    .asset-show-title {
        font-size: 1.18rem;
        font-weight: 700;
        line-height: 1.3;
        color: var(--color-ink-0);
        margin: 0;
        overflow-wrap: anywhere;
        word-break: break-word;
        min-width: 0;
    }

    .asset-show-subline {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px 8px;
        font-size: 0.8rem;
        line-height: 1.4;
        color: #94a3b8;
        margin-top: 4px;
        width: 100%;
        min-width: 0;
    }
    body.light-mode .asset-show-subline {
        color: #64748b;
    }

    .asset-show-tag-code {
        padding: 2px 7px;
        background: rgba(99, 102, 241, 0.12);
        color: #818cf8;
        border: 1px solid rgba(99, 102, 241, 0.25);
        border-radius: 6px;
        font-family: var(--font-mono);
        font-weight: 600;
        font-size: 0.78rem;
        letter-spacing: 0.02em;
    }
    body.light-mode .asset-show-tag-code {
        background: rgba(99, 102, 241, 0.08);
        color: #4f46e5;
        border-color: rgba(99, 102, 241, 0.2);
    }

    .asset-show-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        flex-shrink: 0;
        margin-left: auto;
    }

    @media (max-width: 767.98px) {
        .master-header-banner {
            padding: 14px 16px !important;
            border-radius: 16px !important;
            margin-bottom: 16px !important;
        }

        .asset-header-flex {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 14px !important;
        }

        .asset-show-identity {
            gap: 12px !important;
            width: 100% !important;
            flex: 1 1 100% !important;
        }

        .asset-show-icon {
            width: 38px !important;
            height: 38px !important;
            font-size: 1rem !important;
            border-radius: 11px !important;
            margin-top: 1px !important;
        }

        .asset-show-title {
            font-size: 1.05rem !important;
            line-height: 1.35 !important;
        }

        .asset-show-actions {
            width: 100% !important;
            margin-left: 0 !important;
            gap: 8px !important;
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
        }

        .master-header-banner .asset-show-actions .btn,
        .master-header-banner .asset-show-actions .btn-action-primary,
        .master-header-banner .asset-show-actions .btn-action-secondary {
            flex: 1 1 130px !important;
            width: auto !important;
            height: 40px !important;
            order: unset !important;
            margin-top: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 0.82rem !important;
            font-weight: 600 !important;
            white-space: nowrap !important;
            border-radius: 10px !important;
            min-width: 0 !important;
        }
    }

    .detail-card {
        border-radius: 20px !important;
        overflow: hidden !important;
        border: 1px solid rgba(148, 163, 184, 0.14) !important;
        background: rgba(15, 23, 42, 0.45) !important;
        backdrop-filter: blur(14px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25) !important;
        margin-bottom: 22px !important;
    }
    body.light-mode .detail-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05) !important;
    }

    .detail-card-header {
        background: transparent !important;
        border-bottom: 1px solid rgba(148, 163, 184, 0.12) !important;
        padding: 16px 20px !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    body.light-mode .detail-card-header {
        border-bottom-color: #e2e8f0 !important;
    }

    .qr-container-wrapper {
        background: rgba(15, 23, 42, 0.35);
        border: 1px dashed rgba(99, 102, 241, 0.35);
        border-radius: 16px;
        padding: 16px;
        display: inline-block;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    }
    body.light-mode .qr-container-wrapper {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .meta-param-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border-radius: 12px;
        background: rgba(15, 23, 42, 0.3);
        border: 1px solid rgba(148, 163, 184, 0.1);
        margin-bottom: 8px;
        font-size: 0.86rem;
    }
    body.light-mode .meta-param-row {
        background: #f8fafc;
        border-color: #e2e8f0;
    }

    .meta-param-label {
        color: #94a3b8;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    body.light-mode .meta-param-label {
        color: #64748b;
    }

    .meta-param-value {
        font-weight: 700;
        color: var(--color-ink-0);
        text-align: right;
    }

    .spec-grid-card {
        background: rgba(15, 23, 42, 0.3);
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 14px;
        padding: 14px 16px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    body.light-mode .spec-grid-card {
        background: #f8fafc;
        border-color: #e2e8f0;
    }

    .spec-icon-badge {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(99, 102, 241, 0.14);
        border: 1px solid rgba(99, 102, 241, 0.3);
        color: #818cf8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
    }

    /* Tabs Styling */
    .hallmark-nav-tabs {
        border-bottom: 1px solid rgba(148, 163, 184, 0.12) !important;
        gap: 8px;
    }
    body.light-mode .hallmark-nav-tabs {
        border-bottom-color: #e2e8f0 !important;
    }

    .hallmark-nav-tabs .nav-link {
        border: none !important;
        background: transparent !important;
        color: #94a3b8 !important;
        font-weight: 600 !important;
        font-size: 0.88rem !important;
        padding: 10px 18px !important;
        border-radius: 12px !important;
        transition: all 0.2s ease !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }

    .hallmark-nav-tabs .nav-link.active {
        background: rgba(99, 102, 241, 0.15) !important;
        color: #818cf8 !important;
        border: 1px solid rgba(99, 102, 241, 0.3) !important;
    }
    body.light-mode .hallmark-nav-tabs .nav-link.active {
        background: rgba(99, 102, 241, 0.1) !important;
        color: #4f46e5 !important;
    }

    /* Desktop vs Mobile Toggle */
    @media (min-width: 768px) {
        .profile-desktop-table-wrapper {
            display: block !important;
        }
        .profile-mobile-card-wrapper {
            display: none !important;
        }
    }
    @media (max-width: 767.98px) {
        .profile-desktop-table-wrapper {
            display: none !important;
        }
        .profile-mobile-card-wrapper {
            display: block !important;
        }
    }

    /* Mobile Card Layout Styling */
    .profile-mobile-card {
        background: rgba(15, 23, 42, 0.4);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 14px;
        padding: 14px 16px;
        margin-bottom: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    body.light-mode .profile-mobile-card {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    }

    .mobile-card-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        border-bottom: 1px dashed rgba(148, 163, 184, 0.12);
        font-size: 0.84rem;
    }
    body.light-mode .mobile-card-row {
        border-bottom-color: #e2e8f0 !important;
    }
    .mobile-card-row:last-child {
        border-bottom: none;
    }

    .mobile-card-label {
        color: #94a3b8;
        font-weight: 600;
        font-size: 0.78rem;
    }
    body.light-mode .mobile-card-label {
        color: #64748b;
    }

    .mobile-card-value {
        color: var(--color-ink-0);
        font-weight: 600;
        text-align: right;
    }
</style>
@endpush

@section('content')

<!-- Hallmark Master Header Banner -->
<div class="master-header-banner">
    <div class="asset-header-flex">
        <div class="asset-show-identity">
            <div class="asset-show-icon">
                <i class="fas fa-laptop-house"></i>
            </div>
            <div class="asset-show-meta">
                <h5 class="asset-show-title">{{ $asset->name }}</h5>
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
                <div class="asset-show-subline">
                    <span class="hallmark-status-badge {{ $currentClass }}" style="font-size: 0.72rem; padding: 3px 10px; flex-shrink: 0; white-space: nowrap;">
                        <span style="width: 5px; height: 5px; border-radius: 50%; background: currentColor; display: inline-block;"></span>
                        {{ __('messages.' . strtolower($asset->status)) ?? $asset->status }}
                    </span>
                    <span class="opacity-40">&bull;</span>
                    <span>TAG: <code class="asset-show-tag-code">{{ $asset->asset_tag }}</code></span>
                    <span class="opacity-40">&bull;</span>
                    <span><i class="fas fa-folder text-info mr-1" style="font-size: 0.72rem;"></i> {{ $asset->category->name ?? __('messages.it_assets') }}</span>
                    <span class="opacity-40">&bull;</span>
                    <span><i class="fas fa-map-marker-alt text-success mr-1" style="font-size: 0.72rem;"></i> {{ $asset->location->name ?? '-' }}</span>
                </div>
            </div>
        </div>

        <div class="asset-show-actions">
            <a href="{{ route('assets.index') }}" class="btn btn-sm btn-action-secondary text-nowrap">
                <i class="fas fa-arrow-left mr-1"></i> {{ __('messages.back_to_catalog') }}
            </a>
            @auth
            <a href="{{ route('assets.edit', $asset) }}" class="btn btn-sm btn-action-primary text-nowrap">
                <i class="fas fa-edit mr-1"></i> {{ __('messages.edit_asset') }}
            </a>
            @endauth
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Identity, Image, QR Code & Health AI -->
    <div class="col-lg-4 col-md-5">
        <!-- Asset Photo & QR Card -->
        <div class="card detail-card">
            <div class="card-body text-center p-4">
                @if($asset->image)
                    <div class="mb-3 position-relative rounded-lg overflow-hidden" style="border: 1px solid rgba(148, 163, 184, 0.15);">
                        <img src="{{ asset('storage/' . $asset->image) }}" alt="{{ $asset->name }}" class="img-fluid" style="max-height: 200px; width: 100%; object-fit: cover;">
                    </div>
                @endif

                <div class="mb-3">
                    <div class="qr-container-wrapper">
                        <div id="qrcode" style="padding: 6px; background: #ffffff; border-radius: 8px; display: inline-block;"></div>
                    </div>
                </div>

                <div class="text-muted small font-mono mb-3">
                    <i class="fas fa-qrcode mr-1 text-primary"></i> {{ __('messages.scan_asset_qr') }}
                </div>

                <!-- Core Metadata List -->
                <div class="mt-3">
                    <div class="meta-param-row">
                        <span class="meta-param-label"><i class="fas fa-folder text-info"></i> {{ __('messages.category') }}</span>
                        <span class="meta-param-value text-info">{{ $asset->category->name ?? '-' }}</span>
                    </div>
                    <div class="meta-param-row">
                        <span class="meta-param-label"><i class="fas fa-tag text-warning"></i> {{ __('messages.brand') }}</span>
                        <span class="meta-param-value text-warning">{{ $asset->brand->name ?? '-' }}</span>
                    </div>
                    <div class="meta-param-row">
                        <span class="meta-param-label"><i class="fas fa-map-marker-alt text-success"></i> {{ __('messages.current_location') }}</span>
                        <span class="meta-param-value text-success">{{ $asset->location->name ?? '-' }}</span>
                    </div>
                    <div class="meta-param-row">
                        <span class="meta-param-label"><i class="fas fa-user-check text-indigo"></i> {{ __('messages.assigned_to') }}</span>
                        <span class="meta-param-value">
                            @if($asset->currentAssignment)
                                <a href="{{ route('employees.show', $asset->currentAssignment->employee_id) }}" class="text-indigo text-decoration-none">
                                    {{ $asset->currentAssignment->employee->name }}
                                </a>
                            @else
                                <span class="text-muted font-weight-normal">-</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Predictive Health AI Card -->
        @php
            $health = app(\App\Services\PredictiveHealthService::class)->calculateAssetHealth($asset);
        @endphp
        <div class="card detail-card" style="border-left: 4px solid {{ $health['text_color'] }} !important;">
            <div class="detail-card-header">
                <div class="d-flex align-items-center" style="gap: 10px;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(239, 68, 68, 0.15); color: #f87171; display: flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h6 class="font-weight-bold theme-text mb-0" style="font-size: 0.92rem;">{{ __('messages.health_score_ai') }}</h6>
                </div>
                <span class="badge {{ $health['badge_class'] }} px-2 py-1" style="font-size: 0.75rem; border-radius: 6px;">{{ $health['status'] }}</span>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="theme-text font-weight-bold" style="font-size: 0.85rem;">{{ __('messages.health_score_label') }}:</span>
                    <strong style="font-size: 1.35rem; color: {{ $health['text_color'] }}; font-family: var(--font-mono);">{{ $health['health_score'] }}%</strong>
                </div>
                <div class="progress mb-3" style="height: 8px; background: rgba(148, 163, 184, 0.15); border-radius: 999px;">
                    <div class="progress-bar" style="width: {{ $health['health_score'] }}%; background-color: {{ $health['text_color'] }}; border-radius: 999px;"></div>
                </div>

                <div class="d-flex justify-content-between align-items-center text-muted small mb-3">
                    <span>{{ __('messages.remaining_life') }}:</span>
                    <strong class="theme-text">{{ $health['remaining_life'] }}</strong>
                </div>

                <div class="p-3 rounded-lg" style="background: rgba(15, 23, 42, 0.3); border: 1px solid rgba(148, 163, 184, 0.12);">
                    <small class="text-muted d-block mb-1 font-weight-bold" style="font-size: 0.72rem; letter-spacing: 0.04em;">{{ strtoupper(__('messages.ai_recommendation')) }}:</small>
                    <p class="theme-text small mb-0" style="line-height: 1.3;">{{ $health['recommendation'] }}</p>
                </div>

                <div class="mt-3">
                    <a href="{{ route('predictive-health.index') }}" class="btn btn-sm btn-action-secondary w-100 text-center" style="height: 38px; font-size: 0.82rem; font-weight: 600;">
                        <i class="fas fa-sparkles mr-1 text-info"></i> {{ __('messages.predictive_health') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- IP Address Management for this Asset -->
        <div class="card detail-card">
            <div class="detail-card-header">
                <div class="d-flex align-items-center" style="gap: 10px;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(16, 185, 129, 0.15); color: #34d399; display: flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                        <i class="fas fa-network-wired"></i>
                    </div>
                    <h6 class="font-weight-bold theme-text mb-0" style="font-size: 0.92rem;">{{ __('messages.network_ip') }}</h6>
                </div>
            </div>
            <div class="card-body p-3">
                @forelse($asset->ipAddresses as $ip)
                    <div class="d-flex align-items-center justify-content-between p-2 mb-2 rounded" style="background: rgba(15, 23, 42, 0.3); border: 1px solid rgba(148, 163, 184, 0.1);">
                        <div>
                            <div class="font-weight-bold text-success font-mono" style="font-size: 0.88rem;">{{ $ip->ip_address }}</div>
                            <small class="text-muted font-mono" style="font-size: 0.72rem;">MAC: {{ $ip->mac_address ?? '-' }}</small>
                        </div>
                        <span class="badge badge-success px-2 py-1" style="font-size: 0.7rem;">{{ $ip->status }}</span>
                    </div>
                @empty
                    <div class="text-center text-muted p-3 small">{{ __('messages.no_ips_linked') }}</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column: Specs & History Tabs -->
    <div class="col-lg-8 col-md-7">
        <!-- Specifications & Info Card -->
        <div class="card detail-card">
            <div class="detail-card-header">
                <div class="d-flex align-items-center" style="gap: 10px;">
                    <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(6, 182, 212, 0.15); border: 1px solid rgba(6, 182, 212, 0.3); color: #38bdf8; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h6 class="font-weight-bold theme-text mb-0" style="font-size: 0.96rem;">{{ __('messages.specifications_info') }}</h6>
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Basic Parameter Grid -->
                <div class="row" style="gap: 12px 0;">
                    <div class="col-sm-6 mb-3">
                        <div class="spec-grid-card">
                            <div>
                                <small class="text-muted d-block text-uppercase font-weight-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">{{ __('messages.serial_number') }}</small>
                                <span class="theme-text font-weight-bold font-mono" style="font-size: 0.9rem;">{{ $asset->serial_number ?? '-' }}</span>
                            </div>
                            <div class="spec-icon-badge"><i class="fas fa-barcode"></i></div>
                        </div>
                    </div>

                    <div class="col-sm-6 mb-3">
                        <div class="spec-grid-card">
                            <div>
                                <small class="text-muted d-block text-uppercase font-weight-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">{{ __('messages.date_received') }}</small>
                                <span class="theme-text font-weight-bold font-mono" style="font-size: 0.9rem;">
                                    {{ $asset->date_received ? \Carbon\Carbon::parse($asset->date_received)->format('d M Y') : '-' }}
                                </span>
                            </div>
                            <div class="spec-icon-badge"><i class="fas fa-calendar-alt"></i></div>
                        </div>
                    </div>

                    <div class="col-sm-6 mb-3">
                        <div class="spec-grid-card">
                            <div>
                                <small class="text-muted d-block text-uppercase font-weight-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">{{ __('messages.delivery_order') }}</small>
                                <span class="theme-text font-weight-bold font-mono" style="font-size: 0.88rem;">{{ $asset->delivery_order_number ?? '-' }}</span>
                            </div>
                            <div class="spec-icon-badge"><i class="fas fa-file-invoice"></i></div>
                        </div>
                    </div>

                    <div class="col-sm-6 mb-3">
                        <div class="spec-grid-card">
                            <div style="min-width: 0;">
                                <small class="text-muted d-block text-uppercase font-weight-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">{{ __('messages.notes') }}</small>
                                <span class="theme-text text-truncate d-block" style="font-size: 0.86rem;">{{ $asset->notes ?? '-' }}</span>
                            </div>
                            <div class="spec-icon-badge"><i class="fas fa-sticky-note"></i></div>
                        </div>
                    </div>
                </div>

                @php
                    $catName = strtolower($asset->category->name ?? '');
                @endphp

                <!-- 1. Computers & Laptops -->
                @if(in_array($catName, ['komputer', 'laptop', 'mini pc', 'thin client', 'server']) && $asset->computer)
                    <div class="mt-4 pt-3 border-top" style="border-color: rgba(148, 163, 184, 0.12) !important;">
                        <h6 class="text-info font-weight-bold mb-3 d-flex align-items-center" style="font-size: 0.92rem; gap: 8px;">
                            <i class="fas fa-microchip"></i> <span>{{ __('messages.hardware_specs') }}</span>
                        </h6>
                        <div class="row" style="gap: 8px 0;">
                            <div class="col-sm-6 mb-2">
                                <div class="meta-param-row">
                                    <span class="meta-param-label"><i class="fas fa-microchip text-info"></i> CPU</span>
                                    <span class="meta-param-value" style="font-size: 0.84rem;">{{ $asset->computer->cpu ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <div class="meta-param-row">
                                    <span class="meta-param-label"><i class="fas fa-tv text-info"></i> GPU</span>
                                    <span class="meta-param-value" style="font-size: 0.84rem;">{{ $asset->computer->gpu ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <div class="meta-param-row">
                                    <span class="meta-param-label"><i class="fas fa-memory text-info"></i> RAM</span>
                                    <span class="meta-param-value font-mono">{{ $asset->computer->ram ? $asset->computer->ram . ' GB' : '-' }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <div class="meta-param-row">
                                    <span class="meta-param-label"><i class="fas fa-hdd text-info"></i> SSD</span>
                                    <span class="meta-param-value font-mono">{{ $asset->computer->ssd ? $asset->computer->ssd . ' GB' : '-' }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <div class="meta-param-row">
                                    <span class="meta-param-label"><i class="fas fa-database text-info"></i> HDD</span>
                                    <span class="meta-param-value font-mono">{{ $asset->computer->hdd ? $asset->computer->hdd . ' GB' : '-' }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <div class="meta-param-row">
                                    <span class="meta-param-label"><i class="fab fa-windows text-info"></i> OS</span>
                                    <span class="meta-param-value" style="font-size: 0.84rem;">{{ $asset->computer->os ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- 2. Printers -->
                @if($catName == 'printer' && $asset->printer)
                    <div class="mt-4 pt-3 border-top" style="border-color: rgba(148, 163, 184, 0.12) !important;">
                        <h6 class="text-info font-weight-bold mb-3 d-flex align-items-center" style="font-size: 0.92rem; gap: 8px;">
                            <i class="fas fa-print"></i> <span>{{ __('messages.printer_parameters') }}</span>
                        </h6>
                        <div class="row" style="gap: 8px 0;">
                            <div class="col-sm-6 mb-2">
                                <div class="meta-param-row">
                                    <span class="meta-param-label"><i class="fas fa-print text-info"></i> {{ __('messages.type') }}</span>
                                    <span class="meta-param-value">{{ $asset->printer->type }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <div class="meta-param-row">
                                    <span class="meta-param-label"><i class="fas fa-link text-info"></i> {{ __('messages.connection') }}</span>
                                    <span class="meta-param-value">{{ $asset->printer->connection_type }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <div class="meta-param-row">
                                    <span class="meta-param-label"><i class="fas fa-calculator text-info"></i> {{ __('messages.initial_counter') }}</span>
                                    <span class="meta-param-value font-mono">{{ $asset->printer->counter_print }} {{ __('messages.prints') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Dynamic Custom Specs -->
                @if($asset->category && $asset->category->spec_definitions && !empty($asset->spec_data))
                    <div class="mt-4 pt-3 border-top" style="border-color: rgba(148, 163, 184, 0.12) !important;">
                        <h6 class="text-info font-weight-bold mb-3 d-flex align-items-center" style="font-size: 0.92rem; gap: 8px;">
                            <i class="fas fa-sliders-h"></i> <span>{{ __('messages.custom_specifications') }}</span>
                        </h6>
                        <div class="row" style="gap: 8px 0;">
                            @foreach($asset->category->spec_definitions as $spec)
                                <div class="col-sm-6 mb-2">
                                    <div class="meta-param-row">
                                        <span class="meta-param-label"><i class="fas fa-tag text-info"></i> {{ $spec['label'] }}</span>
                                        <span class="meta-param-value" style="font-size: 0.84rem;">{{ $asset->spec_data[$spec['name']] ?? '-' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- History Ledger Tabs (Handover & Maintenance) -->
        <div class="card detail-card">
            <div class="card-header bg-transparent p-3 border-bottom-0">
                <ul class="nav nav-tabs hallmark-nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="assignments-tab" data-toggle="tab" href="#assignments" role="tab" aria-selected="true">
                            <i class="fas fa-history text-warning"></i> {{ __('messages.asset_assignment') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="maintenances-tab" data-toggle="tab" href="#maintenances" role="tab" aria-selected="false">
                            <i class="fas fa-tools text-danger"></i> {{ __('messages.log_maintenance') }}
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body p-0">
                <div class="tab-content">
                    <!-- Handover Tab -->
                    <div class="tab-pane fade show active" id="assignments" role="tabpanel">
                        <!-- Desktop Table View ONLY -->
                        <div class="profile-desktop-table-wrapper">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover m-0 theme-table">
                                    <thead>
                                        <tr style="font-family: var(--font-mono); font-size: 0.72rem; letter-spacing: 0.08em; text-transform: uppercase;">
                                            <th class="pl-4">{{ __('messages.assigned_date') }}</th>
                                            <th>{{ __('messages.employee') }}</th>
                                            <th>{{ __('messages.return_date') }}</th>
                                            <th>{{ __('messages.status') }}</th>
                                            <th class="pr-4">{{ __('messages.notes') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($asset->assignments as $assign)
                                        <tr>
                                            <td class="pl-4 theme-text font-mono" style="font-size: 0.84rem;">
                                                {{ \Carbon\Carbon::parse($assign->assigned_date)->format('d M Y') }}
                                            </td>
                                            <td class="theme-text">
                                                <a href="{{ route('employees.show', $assign->employee_id) }}" class="text-info font-weight-bold text-decoration-none">
                                                    {{ $assign->employee->name }}
                                                </a>
                                            </td>
                                            <td class="theme-text font-mono" style="font-size: 0.84rem;">
                                                {{ $assign->return_date ? \Carbon\Carbon::parse($assign->return_date)->format('d M Y') : '-' }}
                                            </td>
                                            <td>
                                                <span class="badge badge-pill {{ $assign->status == 'Returned' ? 'badge-info' : 'badge-success' }}" style="padding: 4px 10px; font-size: 0.74rem;">
                                                    {{ __('messages.' . strtolower($assign->status)) ?? $assign->status }}
                                                </span>
                                            </td>
                                            <td class="theme-text pr-4 text-muted" style="font-size: 0.84rem;">{{ $assign->notes ?? '-' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">{{ __('messages.no_data') }}</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile Card View ONLY -->
                        <div class="profile-mobile-card-wrapper p-3">
                            @forelse($asset->assignments as $assign)
                            <div class="profile-mobile-card">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom" style="border-color: rgba(148, 163, 184, 0.12) !important;">
                                    <a href="{{ route('employees.show', $assign->employee_id) }}" class="font-weight-bold text-info text-decoration-none" style="font-size: 0.95rem;">
                                        <i class="fas fa-user-circle mr-1"></i> {{ $assign->employee->name }}
                                    </a>
                                    <span class="badge badge-pill {{ $assign->status == 'Returned' ? 'badge-info' : 'badge-success' }}" style="padding: 4px 10px; font-size: 0.74rem;">
                                        {{ __('messages.' . strtolower($assign->status)) ?? $assign->status }}
                                    </span>
                                </div>
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label">{{ __('messages.assigned_date') }}</span>
                                    <span class="mobile-card-value font-mono" style="font-size: 0.84rem;">
                                        {{ \Carbon\Carbon::parse($assign->assigned_date)->format('d M Y') }}
                                    </span>
                                </div>
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label">{{ __('messages.return_date') }}</span>
                                    <span class="mobile-card-value font-mono" style="font-size: 0.84rem;">
                                        {{ $assign->return_date ? \Carbon\Carbon::parse($assign->return_date)->format('d M Y') : '-' }}
                                    </span>
                                </div>
                                @if($assign->notes)
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label">{{ __('messages.notes') }}</span>
                                    <span class="mobile-card-value text-muted" style="font-size: 0.82rem;">
                                        {{ $assign->notes }}
                                    </span>
                                </div>
                                @endif
                            </div>
                            @empty
                            <div class="text-center text-muted p-4" style="font-size: 0.88rem;">{{ __('messages.no_data') }}</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Maintenance Tab -->
                    <div class="tab-pane fade" id="maintenances" role="tabpanel">
                        <!-- Desktop Table View ONLY -->
                        <div class="profile-desktop-table-wrapper">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover m-0 theme-table">
                                    <thead>
                                        <tr style="font-family: var(--font-mono); font-size: 0.72rem; letter-spacing: 0.08em; text-transform: uppercase;">
                                            <th class="pl-4">{{ __('messages.start_date') }}</th>
                                            <th>{{ __('messages.end_date') }}</th>
                                            <th>{{ __('messages.type') }}</th>
                                            <th>{{ __('messages.description') }}</th>
                                            <th>{{ __('messages.cost') }}</th>
                                            <th class="pr-4">{{ __('messages.status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($asset->maintenances as $maint)
                                        <tr>
                                            <td class="pl-4 theme-text font-mono" style="font-size: 0.84rem;">
                                                {{ \Carbon\Carbon::parse($maint->start_date)->format('d M Y') }}
                                            </td>
                                            <td class="theme-text font-mono" style="font-size: 0.84rem;">
                                                {{ $maint->end_date ? \Carbon\Carbon::parse($maint->end_date)->format('d M Y') : '-' }}
                                            </td>
                                            <td class="theme-text font-weight-bold text-info" style="font-size: 0.86rem;">{{ $maint->type }}</td>
                                            <td class="theme-text" style="font-size: 0.84rem;">{{ $maint->description }}</td>
                                            <td class="theme-text text-danger font-mono" style="font-size: 0.84rem;">
                                                {{ $maint->cost ? 'Rp ' . number_format($maint->cost, 0, ',', '.') : '-' }}
                                            </td>
                                            <td class="pr-4">
                                                <span class="badge badge-pill {{ $maint->status == 'Completed' ? 'badge-success' : ($maint->status == 'Ongoing' ? 'badge-warning' : 'badge-secondary') }}" style="padding: 4px 10px; font-size: 0.74rem;">
                                                    {{ __('messages.' . strtolower($maint->status)) ?? $maint->status }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">{{ __('messages.no_data') }}</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile Card View ONLY -->
                        <div class="profile-mobile-card-wrapper p-3">
                            @forelse($asset->maintenances as $maint)
                            <div class="profile-mobile-card">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom" style="border-color: rgba(148, 163, 184, 0.12) !important;">
                                    <span class="font-weight-bold text-info" style="font-size: 0.95rem;">
                                        <i class="fas fa-tools mr-1"></i> {{ $maint->type }}
                                    </span>
                                    <span class="badge badge-pill {{ $maint->status == 'Completed' ? 'badge-success' : ($maint->status == 'Ongoing' ? 'badge-warning' : 'badge-secondary') }}" style="padding: 4px 10px; font-size: 0.74rem;">
                                        {{ __('messages.' . strtolower($maint->status)) ?? $maint->status }}
                                    </span>
                                </div>
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label">{{ __('messages.start_date') }}</span>
                                    <span class="mobile-card-value font-mono" style="font-size: 0.84rem;">
                                        {{ \Carbon\Carbon::parse($maint->start_date)->format('d M Y') }}
                                    </span>
                                </div>
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label">{{ __('messages.end_date') }}</span>
                                    <span class="mobile-card-value font-mono" style="font-size: 0.84rem;">
                                        {{ $maint->end_date ? \Carbon\Carbon::parse($maint->end_date)->format('d M Y') : '-' }}
                                    </span>
                                </div>
                                @if($maint->description)
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label">{{ __('messages.description') }}</span>
                                    <span class="mobile-card-value theme-text" style="font-size: 0.84rem;">
                                        {{ $maint->description }}
                                    </span>
                                </div>
                                @endif
                                @if($maint->cost)
                                <div class="mobile-card-row">
                                    <span class="mobile-card-label">{{ __('messages.cost') }}</span>
                                    <span class="mobile-card-value text-danger font-mono" style="font-size: 0.84rem;">
                                        Rp {{ number_format($maint->cost, 0, ',', '.') }}
                                    </span>
                                </div>
                                @endif
                            </div>
                            @empty
                            <div class="text-center text-muted p-4" style="font-size: 0.88rem;">{{ __('messages.no_data') }}</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
$(document).ready(function() {
    var qrElement = document.getElementById("qrcode");
    if(qrElement) {
        new QRCode(qrElement, {
            text: "https://itam.asoyydev.id/assets/{{ $asset->id }}",
            width: 130,
            height: 130,
            colorDark : "#0f172a",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    }
});
</script>
@endpush
