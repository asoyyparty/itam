@extends('layouts.admin')

@section('title', __('messages.add_ip'))

@section('content')
<style>
/* Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 */
:root {
    --hl-bg-canvas: #090d16;
    --hl-bg-surface: #0f172a;
    --hl-bg-card: rgba(15, 23, 42, 0.78);
    --hl-border-subtle: rgba(148, 163, 184, 0.14);
    --hl-border-active: rgba(148, 163, 184, 0.28);
    --hl-text-bright: #f8fafc;
    --hl-text-muted: #94a3b8;
    --hl-text-dim: #64748b;
    --hl-accent-cyan: #06b6d4;
    --hl-accent-indigo: #6366f1;
    --hl-font-mono: ui-monospace, "JetBrains Mono", SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}

/* Master Header Banner */
.master-header-banner {
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.92) 0%, rgba(30, 41, 59, 0.82) 100%);
    border: 1px solid var(--hl-border-subtle);
    border-radius: 16px;
    padding: 18px 22px;
    margin-bottom: 18px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    backdrop-filter: blur(12px);
}
body.light-mode .master-header-banner {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(241, 245, 249, 0.95));
    border: 1px solid rgba(226, 232, 240, 0.9);
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
}

.master-banner-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
}

.master-banner-title-box {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
    flex: 1 1 300px;
}

.master-banner-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: rgba(6, 182, 212, 0.15);
    border: 1px solid rgba(6, 182, 212, 0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #38bdf8;
    font-size: 1.25rem;
    flex-shrink: 0;
    box-shadow: 0 0 16px rgba(6, 182, 212, 0.2);
}

.btn-tactile-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 9px 20px;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 700;
    color: #ffffff !important;
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    border: 1px solid rgba(56, 189, 248, 0.4);
    box-shadow: 0 4px 14px rgba(2, 132, 199, 0.3);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    text-decoration: none !important;
    white-space: nowrap;
}
.btn-tactile-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(2, 132, 199, 0.45);
    filter: brightness(1.08);
}

.btn-tactile-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 9px 18px;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--hl-text-bright) !important;
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid var(--hl-border-active);
    transition: all 0.2s;
    text-decoration: none !important;
    white-space: nowrap;
}
.btn-tactile-secondary:hover {
    background: rgba(30, 41, 59, 0.9);
    border-color: rgba(148, 163, 184, 0.45);
    color: #ffffff !important;
    transform: translateY(-1px);
}
body.light-mode .btn-tactile-secondary {
    background: #ffffff;
    border-color: #cbd5e1;
    color: #334155 !important;
}
body.light-mode .btn-tactile-secondary:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
}

/* Master Form Bento Section */
.master-form-card {
    background: var(--hl-bg-card);
    border: 1px solid var(--hl-border-subtle);
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.22);
    margin-bottom: 18px;
    overflow: hidden;
    backdrop-filter: blur(12px);
}
body.light-mode .master-form-card {
    background: #ffffff;
    border-color: #e2e8f0;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
}

.form-section-header {
    padding: 12px 18px;
    background: rgba(15, 23, 42, 0.4);
    border-bottom: 1px solid var(--hl-border-subtle);
    display: flex;
    align-items: center;
    gap: 10px;
}
body.light-mode .form-section-header {
    background: #f8fafc;
    border-bottom-color: #e2e8f0;
}

.form-section-icon {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
}

.form-section-title {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--hl-text-bright);
    margin: 0;
}
body.light-mode .form-section-title {
    color: #0f172a;
}

.form-section-body {
    padding: 18px 20px;
}

/* Form Inputs */
.custom-ip-input,
.master-form-card .select2-container--default .select2-selection--single {
    height: 44px !important;
    border-radius: 10px !important;
    border: 1px solid var(--hl-border-active) !important;
    background: rgba(15, 23, 42, 0.6) !important;
    box-shadow: none !important;
    font-size: 0.88rem !important;
    color: var(--hl-text-bright) !important;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
}
.master-form-card .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 42px !important;
    padding-left: 12px !important;
    color: var(--hl-text-bright) !important;
}
.master-form-card .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 42px !important;
}
.custom-ip-input:focus,
.master-form-card .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #06b6d4 !important;
    box-shadow: 0 0 0 2px rgba(6, 182, 212, 0.2) !important;
    background: rgba(15, 23, 42, 0.85) !important;
}
body.light-mode .custom-ip-input,
body.light-mode .master-form-card .select2-container--default .select2-selection--single {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}
body.light-mode .master-form-card .select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #0f172a !important;
}

.custom-ip-textarea {
    border-radius: 10px !important;
    border: 1px solid var(--hl-border-active) !important;
    background: rgba(15, 23, 42, 0.6) !important;
    font-size: 0.88rem !important;
    color: var(--hl-text-bright) !important;
    padding: 12px !important;
    transition: all 0.2s ease !important;
}
.custom-ip-textarea:focus {
    border-color: #06b6d4 !important;
    box-shadow: 0 0 0 2px rgba(6, 182, 212, 0.2) !important;
    background: rgba(15, 23, 42, 0.85) !important;
}
body.light-mode .custom-ip-textarea {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}

.form-label-title {
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--hl-text-bright);
    margin-bottom: 7px;
    letter-spacing: 0.01em;
}
body.light-mode .form-label-title {
    color: #1e293b;
}

/* Footer Actions */
.form-footer-bar {
    background: var(--hl-bg-card);
    border: 1px solid var(--hl-border-subtle);
    border-radius: 16px;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    margin-bottom: 24px;
}
body.light-mode .form-footer-bar {
    background: #ffffff;
    border-color: #e2e8f0;
}

@media (max-width: 767.98px) {
    .master-header-banner {
        padding: 14px 16px;
        border-radius: 14px;
        margin-bottom: 14px;
    }
    .master-banner-top {
        flex-direction: column !important;
        align-items: stretch !important;
        justify-content: flex-start !important;
        gap: 12px !important;
    }
    .master-banner-title-box {
        width: 100% !important;
        flex: 0 0 auto !important;
    }
    .master-banner-icon {
        width: 38px;
        height: 38px;
        font-size: 1.1rem;
    }
    .form-section-body {
        padding: 14px 12px;
    }
    .form-footer-bar {
        flex-direction: column-reverse;
        padding: 14px 16px;
        gap: 8px;
    }
    .form-footer-bar .btn-tactile-secondary,
    .form-footer-bar .btn-tactile-primary {
        width: 100% !important;
        height: 42px;
        justify-content: center;
    }
}
</style>

<!-- Hallmark Master Header Banner -->
<div class="master-header-banner">
    <div class="master-banner-top">
        <div class="master-banner-title-box">
            <div class="master-banner-icon">
                <i class="fas fa-network-wired"></i>
            </div>
            <div style="flex: 1; min-width: 0;">
                <h5 class="mb-0 font-weight-bold theme-text text-truncate" style="font-size: 1.12rem; line-height: 1.2;">
                    {{ __('messages.add_ip') }}
                </h5>
                <small class="text-muted d-block" style="font-size: 0.78rem; margin-top: 3px; line-height: 1.35;">
                    {{ __('messages.add_ip_subtitle') }}
                </small>
            </div>
        </div>

        <div>
            <a href="{{ route('ips.index') }}" class="btn btn-tactile-secondary" title="{{ __('messages.back_to_ips') }}">
                <i class="fas fa-arrow-left mr-1"></i>
                <span>{{ __('messages.back_to_ips') }}</span>
            </a>
        </div>
    </div>
</div>

<form action="{{ route('ips.store') }}" method="POST">
    @csrf

    <!-- Section 1: IP Addressing & Status -->
    <div class="master-form-card">
        <div class="form-section-header">
            <div class="form-section-icon" style="background: rgba(6, 182, 212, 0.15); color: #38bdf8;">
                <i class="fas fa-ethernet"></i>
            </div>
            <h6 class="form-section-title">{{ __('messages.ip_address_config') }}</h6>
        </div>
        <div class="form-section-body">
            <div class="row">
                <div class="col-md-6 form-group mb-3 mb-md-0">
                    <label class="form-label form-label-title">
                        {{ __('messages.ip_address') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="ip_address" class="form-control theme-input custom-ip-input @error('ip_address') is-invalid @enderror" style="font-family: var(--hl-font-mono);" value="{{ old('ip_address') }}" required placeholder="{{ __('messages.ip_placeholder') }}" autocomplete="off">
                    @error('ip_address') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-0">
                    <label class="form-label form-label-title">
                        {{ __('messages.status') }} <span class="text-danger">*</span>
                    </label>
                    <select name="status" class="form-control select2 @error('status') is-invalid @enderror" required>
                        <option value="Available" {{ old('status') == 'Available' ? 'selected' : '' }}>{{ __('messages.available') }}</option>
                        <option value="Used" {{ old('status', 'Used') == 'Used' ? 'selected' : '' }}>{{ __('messages.used') }}</option>
                        <option value="Reserved" {{ old('status') == 'Reserved' ? 'selected' : '' }}>{{ __('messages.reserved') }}</option>
                    </select>
                    @error('status') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: VLAN, Gateway & DNS Parameters -->
    <div class="master-form-card">
        <div class="form-section-header">
            <div class="form-section-icon" style="background: rgba(99, 102, 241, 0.15); color: #818cf8;">
                <i class="fas fa-sitemap"></i>
            </div>
            <h6 class="form-section-title">{{ __('messages.routing_subnet_config') }}</h6>
        </div>
        <div class="form-section-body">
            <div class="row">
                <div class="col-md-4 form-group mb-3 mb-md-0">
                    <label class="form-label form-label-title">VLAN</label>
                    <select name="vlan_id" id="vlan_select" class="form-control select2 @error('vlan_id') is-invalid @enderror">
                        <option value="" data-vlan-number="" data-gateway="">{{ __('messages.none_standard_vlan') }}</option>
                        @foreach($vlans as $vlan)
                            <option value="{{ $vlan->id }}" data-vlan-number="{{ $vlan->vlan_number }}" data-gateway="{{ $vlan->gateway ?: '192.168.'.$vlan->vlan_number.'.254' }}" {{ old('vlan_id') == $vlan->id ? 'selected' : '' }}>
                                VLAN {{ $vlan->vlan_number }} - {{ $vlan->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('vlan_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-4 form-group mb-3 mb-md-0">
                    <label class="form-label form-label-title">{{ __('messages.gateway') }}</label>
                    <input type="text" name="gateway" id="gateway_input" class="form-control theme-input custom-ip-input @error('gateway') is-invalid @enderror" style="font-family: var(--hl-font-mono);" value="{{ old('gateway') }}" placeholder="{{ __('messages.gateway_placeholder') }}" autocomplete="off">
                    @error('gateway') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-4 form-group mb-0">
                    <label class="form-label form-label-title">DNS Server</label>
                    <input type="text" name="dns" class="form-control theme-input custom-ip-input @error('dns') is-invalid @enderror" style="font-family: var(--hl-font-mono);" value="{{ old('dns') }}" placeholder="{{ __('messages.dns_placeholder') }}" autocomplete="off">
                    @error('dns') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Asset & Employee Binding -->
    <div class="master-form-card">
        <div class="form-section-header">
            <div class="form-section-icon" style="background: rgba(16, 185, 129, 0.15); color: #34d399;">
                <i class="fas fa-link"></i>
            </div>
            <h6 class="form-section-title">{{ __('messages.device_user_binding') }}</h6>
        </div>
        <div class="form-section-body">
            <div class="row">
                <div class="col-md-6 form-group mb-3 mb-md-0">
                    <label class="form-label form-label-title">
                        {{ __('messages.assigned_asset') }} ({{ __('messages.optional') }})
                    </label>
                    <select name="asset_id" class="form-control select2 @error('asset_id') is-invalid @enderror">
                        <option value="">{{ __('messages.select_asset_optional') }}</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                {{ $asset->asset_tag }} - {{ $asset->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('asset_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-0">
                    <label class="form-label form-label-title">
                        {{ __('messages.assigned_employee') }} ({{ __('messages.optional') }})
                    </label>
                    <select name="employee_id" class="form-control select2 @error('employee_id') is-invalid @enderror">
                        <option value="">{{ __('messages.select_employee_optional') }}</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->employee_id }} - {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Section 4: Technical Notes & Documentation -->
    <div class="master-form-card">
        <div class="form-section-header">
            <div class="form-section-icon" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24;">
                <i class="fas fa-file-alt"></i>
            </div>
            <h6 class="form-section-title">{{ __('messages.technical_notes') }}</h6>
        </div>
        <div class="form-section-body">
            <div class="form-group mb-0">
                <label class="form-label form-label-title">{{ __('messages.notes') }}</label>
                <textarea name="notes" class="form-control theme-input custom-ip-textarea @error('notes') is-invalid @enderror" rows="3" placeholder="{{ __('messages.ip_notes_placeholder') }}">{{ old('notes') }}</textarea>
                @error('notes') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <!-- Footer Actions Bar -->
    <div class="form-footer-bar">
        <a href="{{ route('ips.index') }}" class="btn btn-tactile-secondary">
            <i class="fas fa-times mr-1"></i> {{ __('messages.cancel') }}
        </a>
        <button type="submit" class="btn btn-tactile-primary">
            <i class="fas fa-check mr-1"></i> {{ __('messages.save_ip') }}
        </button>
    </div>
</form>

@push('scripts')
<script>
$(document).ready(function() {
    $('.select2').select2({ width: '100%' });

    $('#vlan_select').on('change', function() {
        var selected = $(this).find(':selected');
        var gw = selected.data('gateway');
        if (gw) {
            $('#gateway_input').val(gw);
        }
    });

    $('input[name="ip_address"]').on('blur change', function() {
        var ip = $(this).val().trim();
        if (ip && !$('#gateway_input').val()) {
            var parts = ip.split('.');
            if (parts.length === 4) {
                $('#gateway_input').val(parts[0] + '.' + parts[1] + '.' + parts[2] + '.254');
            }
        }
    });
});
</script>
@endpush
@endsection
