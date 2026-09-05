@extends('layouts.admin')

@section('title', __('messages.add_vlan'))

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
    --hl-accent-indigo: #6366f1;
    --hl-accent-indigo-light: #818cf8;
    --hl-accent-cyan: #06b6d4;
    --hl-accent-emerald: #10b981;
    --hl-accent-amber: #f59e0b;
    --hl-accent-crimson: #f43f5e;
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
    background: rgba(99, 102, 241, 0.15);
    border: 1px solid rgba(99, 102, 241, 0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #818cf8;
    font-size: 1.25rem;
    flex-shrink: 0;
    box-shadow: 0 0 16px rgba(99, 102, 241, 0.2);
}

.master-count-pill {
    font-family: var(--hl-font-mono);
    font-size: 0.72rem;
    padding: 4px 10px;
    background: rgba(99, 102, 241, 0.12);
    border: 1px solid rgba(99, 102, 241, 0.3);
    color: #818cf8;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 700;
    letter-spacing: 0.06em;
    white-space: nowrap;
}

.live-dot-indigo {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #6366f1;
    box-shadow: 0 0 8px #6366f1;
    display: inline-block;
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
    background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    border: 1px solid rgba(129, 140, 248, 0.4);
    box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    text-decoration: none !important;
    white-space: nowrap;
    cursor: pointer;
}
.btn-tactile-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(79, 70, 229, 0.5);
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
.custom-vlan-input {
    height: 44px !important;
    border-radius: 10px !important;
    border: 1px solid var(--hl-border-active) !important;
    background: rgba(15, 23, 42, 0.6) !important;
    box-shadow: none !important;
    font-size: 0.88rem !important;
    color: var(--hl-text-bright) !important;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
}
.custom-vlan-input:focus {
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2) !important;
    background: rgba(15, 23, 42, 0.85) !important;
}
body.light-mode .custom-vlan-input {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}

.custom-vlan-textarea {
    border-radius: 10px !important;
    border: 1px solid var(--hl-border-active) !important;
    background: rgba(15, 23, 42, 0.6) !important;
    font-size: 0.88rem !important;
    color: var(--hl-text-bright) !important;
    padding: 12px !important;
    transition: all 0.2s ease !important;
}
.custom-vlan-textarea:focus {
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2) !important;
    background: rgba(15, 23, 42, 0.85) !important;
}
body.light-mode .custom-vlan-textarea {
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

.input-helper-text {
    font-size: 0.75rem;
    color: var(--hl-text-dim);
    margin-top: 5px;
    display: block;
}

/* Tactile Radio Cards for Status */
.status-radio-group {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}
.status-radio-card {
    position: relative;
    border: 1px solid var(--hl-border-active);
    border-radius: 10px;
    padding: 10px 14px;
    cursor: pointer;
    background: rgba(15, 23, 42, 0.5);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex;
    align-items: center;
    gap: 10px;
}
.status-radio-card:hover {
    border-color: var(--hl-accent-indigo-light);
    background: rgba(30, 41, 59, 0.6);
}
.status-radio-card input[type="radio"] {
    display: none;
}
.status-radio-card.active-selected {
    border-color: rgba(16, 185, 129, 0.5);
    background: rgba(16, 185, 129, 0.1);
}
.status-radio-card.inactive-selected {
    border-color: rgba(244, 63, 94, 0.5);
    background: rgba(244, 63, 94, 0.1);
}
body.light-mode .status-radio-card {
    background: #ffffff;
    border-color: #cbd5e1;
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
    .master-banner-actions {
        width: 100% !important;
    }
    .master-banner-actions .btn-tactile-secondary {
        width: 100% !important;
        height: 42px !important;
    }
    .status-radio-group {
        grid-template-columns: 1fr;
        gap: 8px;
    }
    .form-footer-bar {
        flex-direction: column-reverse;
        gap: 10px;
        padding: 12px;
    }
    .form-footer-bar .btn-tactile-primary,
    .form-footer-bar .btn-tactile-secondary {
        width: 100% !important;
        height: 44px !important;
    }
}
</style>

<!-- Master Header Banner -->
<div class="master-header-banner">
    <div class="master-banner-top">
        <div class="master-banner-title-box">
            <div class="master-banner-icon">
                <i class="fas fa-network-wired"></i>
            </div>
            <div>
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <h4 class="mb-0 font-weight-bold" style="font-size: 1.12rem; color: var(--hl-text-bright); line-height: 1.2;">
                        {{ __('messages.add_vlan') }}
                    </h4>
                    <span class="master-count-pill">
                        <span class="live-dot-indigo"></span>
                        {{ __('messages.create_mode') }}
                    </span>
                </div>
                <small style="color: var(--hl-text-muted); font-size: 0.78rem; display: block; margin-top: 2px;">
                    {{ __('messages.add_vlan_subtitle') }}
                </small>
            </div>
        </div>
        <div class="master-banner-actions">
            <a href="{{ route('vlans.index') }}" class="btn-tactile-secondary">
                <i class="fas fa-arrow-left mr-1"></i> {{ __('messages.back_to_vlans') }}
            </a>
        </div>
    </div>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; border-radius: 12px;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="icon fas fa-ban mr-2"></i> {{ session('error') }}
    </div>
@endif

<form action="{{ route('vlans.store') }}" method="POST">
    @csrf

    <!-- Card 1: Identitas & Status VLAN -->
    <div class="master-form-card">
        <div class="form-section-header">
            <div class="form-section-icon" style="background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3);">
                <i class="fas fa-hashtag"></i>
            </div>
            <h6 class="form-section-title">{{ __('messages.vlan_identity_status') }}</h6>
        </div>
        <div class="form-section-body">
            <div class="row">
                <div class="col-md-4 form-group mb-3">
                    <label class="form-label form-label-title">
                        {{ __('messages.vlan_number') }} <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="vlan_number" min="1" max="4094"
                           class="form-control custom-vlan-input @error('vlan_number') is-invalid @enderror" 
                           value="{{ old('vlan_number') }}" required 
                           placeholder="{{ __('messages.vlan_number_placeholder') }}" 
                           autocomplete="off"
                           style="font-family: var(--hl-font-mono); font-weight: 700; font-size: 0.95rem;">
                    <span class="input-helper-text">{{ __('messages.vlan_number_hint') }}</span>
                    @error('vlan_number') 
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span> 
                    @enderror
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label class="form-label form-label-title">
                        {{ __('messages.vlan_name') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" 
                           class="form-control custom-vlan-input @error('name') is-invalid @enderror" 
                           value="{{ old('name') }}" required 
                           placeholder="{{ __('messages.vlan_name_placeholder') }}" 
                           autocomplete="off">
                    <span class="input-helper-text">{{ __('messages.vlan_name_hint') }}</span>
                    @error('name') 
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span> 
                    @enderror
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label class="form-label form-label-title">
                        {{ __('messages.status') }} <span class="text-danger">*</span>
                    </label>
                    <div class="status-radio-group">
                        <label class="status-radio-card {{ old('status', 'Active') === 'Active' ? 'active-selected' : '' }}" id="label_status_active">
                            <input type="radio" name="status" value="Active" {{ old('status', 'Active') === 'Active' ? 'checked' : '' }} onchange="updateRadioSelection()">
                            <span class="live-dot-indigo" style="background: #10b981; box-shadow: 0 0 8px #10b981; width: 8px; height: 8px;"></span>
                            <span style="font-size: 0.85rem; font-weight: 700; color: var(--hl-text-bright);">{{ __('messages.active') }}</span>
                        </label>
                        <label class="status-radio-card {{ old('status') === 'Inactive' ? 'inactive-selected' : '' }}" id="label_status_inactive">
                            <input type="radio" name="status" value="Inactive" {{ old('status') === 'Inactive' ? 'checked' : '' }} onchange="updateRadioSelection()">
                            <span class="live-dot-indigo" style="background: #f43f5e; box-shadow: 0 0 8px #f43f5e; width: 8px; height: 8px;"></span>
                            <span style="font-size: 0.85rem; font-weight: 700; color: var(--hl-text-bright);">{{ __('messages.inactive') }}</span>
                        </label>
                    </div>
                    @error('status') 
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span> 
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Subnet & Routing Jaringan -->
    <div class="master-form-card">
        <div class="form-section-header">
            <div class="form-section-icon" style="background: rgba(6, 182, 212, 0.15); color: #38bdf8; border: 1px solid rgba(6, 182, 212, 0.3);">
                <i class="fas fa-network-wired"></i>
            </div>
            <h6 class="form-section-title">{{ __('messages.vlan_routing_network') }}</h6>
        </div>
        <div class="form-section-body">
            <div class="row">
                <div class="col-md-6 form-group mb-3">
                    <label class="form-label form-label-title">
                        {{ __('messages.subnet_cidr') }}
                    </label>
                    <input type="text" name="subnet" 
                           class="form-control custom-vlan-input @error('subnet') is-invalid @enderror" 
                           value="{{ old('subnet') }}" 
                           placeholder="{{ __('messages.vlan_subnet_placeholder') }}" 
                           autocomplete="off"
                           style="font-family: var(--hl-font-mono); color: var(--hl-accent-cyan);">
                    <span class="input-helper-text">{{ __('messages.vlan_subnet_hint') }}</span>
                    @error('subnet') 
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span> 
                    @enderror
                </div>

                <div class="col-md-6 form-group mb-3">
                    <label class="form-label form-label-title">
                        {{ __('messages.default_gateway') }}
                    </label>
                    <input type="text" name="gateway" 
                           class="form-control custom-vlan-input @error('gateway') is-invalid @enderror" 
                           value="{{ old('gateway') }}" 
                           placeholder="{{ __('messages.vlan_gateway_placeholder') }}" 
                           autocomplete="off"
                           style="font-family: var(--hl-font-mono); color: var(--hl-accent-emerald);">
                    <span class="input-helper-text">{{ __('messages.vlan_gateway_hint') }}</span>
                    @error('gateway') 
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span> 
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Catatan Teknis & Dokumentasi -->
    <div class="master-form-card">
        <div class="form-section-header">
            <div class="form-section-icon" style="background: rgba(148, 163, 184, 0.15); color: #cbd5e1; border: 1px solid rgba(148, 163, 184, 0.3);">
                <i class="fas fa-sticky-note"></i>
            </div>
            <h6 class="form-section-title">{{ __('messages.vlan_technical_notes') }}</h6>
        </div>
        <div class="form-section-body">
            <div class="form-group mb-0">
                <textarea name="notes" rows="3" 
                          class="form-control custom-vlan-textarea @error('notes') is-invalid @enderror" 
                          placeholder="{{ __('messages.vlan_notes_placeholder') }}">{{ old('notes') }}</textarea>
                <span class="input-helper-text">{{ __('messages.vlan_notes_hint') }}</span>
                @error('notes') 
                    <span class="text-danger small mt-1 d-block">{{ $message }}</span> 
                @enderror
            </div>
        </div>
    </div>

    <!-- Sticky Tactile Action Bar -->
    <div class="form-footer-bar">
        <a href="{{ route('vlans.index') }}" class="btn-tactile-secondary">
            <i class="fas fa-times mr-1"></i> {{ __('messages.cancel') }}
        </a>
        <button type="submit" class="btn-tactile-primary">
            <i class="fas fa-save mr-1"></i> {{ __('messages.save_vlan') }}
        </button>
    </div>
</form>

@push('scripts')
<script>
function updateRadioSelection() {
    var statusVal = document.querySelector('input[name="status"]:checked').value;
    var activeCard = document.getElementById('label_status_active');
    var inactiveCard = document.getElementById('label_status_inactive');
    
    if (statusVal === 'Active') {
        activeCard.classList.add('active-selected');
        inactiveCard.classList.remove('inactive-selected');
    } else {
        inactiveCard.classList.add('inactive-selected');
        activeCard.classList.remove('active-selected');
    }
}
</script>
@endpush
@endsection
