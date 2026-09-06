@extends('layouts.admin')

@section('title', __('messages.edit_license'))

@section('content')
<!-- Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 -->
<style>
    /* ═══════════════════════════════════════════════════════════════════
       HALLMARK BENTO FORM: SOFTWARE LICENSE LIFECYCLE & ASSET CONTROLLER
       ═══════════════════════════════════════════════════════════════════ */
    .lic-form-container {
        max-width: 820px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    /* 1. Header Command Banner */
    .lic-form-header {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 16px;
        padding: 18px 24px;
        position: relative;
        overflow: hidden;
    }
    .lic-form-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, #fbbf24 0%, #6366f1 100%);
    }
    .lic-form-header-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }
    .lic-form-header-left {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }
    .lic-form-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(245, 158, 11, 0.12);
        border: 1px solid rgba(245, 158, 11, 0.28);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fbbf24;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .lic-name-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(99, 102, 241, 0.12);
        border: 1px solid rgba(99, 102, 241, 0.25);
        color: #a5b4fc;
        padding: 2px 10px;
        border-radius: 9999px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.76rem;
        font-weight: 700;
    }

    /* 2. Compliance Status HUD */
    .lic-hud-card {
        background: rgba(15, 23, 42, 0.7);
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 14px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }

    /* 3. Bento Form Cards */
    .lic-form-bento {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 16px;
        padding: 20px 24px;
        position: relative;
    }
    .lic-form-bento-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 14px;
        margin-bottom: 18px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.1);
    }
    .lic-form-bento-num {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.76rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .lic-form-bento-title {
        font-size: 0.96rem;
        font-weight: 700;
        color: #f8fafc;
        margin-bottom: 0;
    }
    .lic-form-bento-desc {
        font-size: 0.74rem;
        color: #64748b;
        margin-top: 2px;
    }

    /* Inputs & Fields */
    .lic-field-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: #cbd5e1;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .lic-input-custom {
        height: 44px;
        border-radius: 10px;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(148, 163, 184, 0.2);
        color: #f8fafc;
        font-size: 0.88rem;
        padding: 0 14px;
        transition: all 0.2s ease;
    }
    .lic-input-custom:focus {
        background: rgba(15, 23, 42, 0.85);
        border-color: #fbbf24;
        outline: none;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
    }
    .lic-mono-textarea {
        border-radius: 10px;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(148, 163, 184, 0.2);
        color: #f8fafc;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.86rem;
        line-height: 1.5;
        padding: 12px 14px;
        transition: all 0.2s ease;
        resize: vertical;
    }
    .lic-mono-textarea:focus {
        background: rgba(15, 23, 42, 0.85);
        border-color: #6366f1;
        outline: none;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    }
    .lic-helper-text {
        font-size: 0.74rem;
        color: #64748b;
        margin-top: 4px;
        display: block;
    }

    /* Interactive Lifetime Switcher */
    .lic-lifetime-box {
        background: rgba(15, 23, 42, 0.4);
        border: 1px solid rgba(148, 163, 184, 0.16);
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .lic-lifetime-box:hover {
        border-color: rgba(56, 189, 248, 0.35);
        background: rgba(15, 23, 42, 0.6);
    }
    .lic-lifetime-box.is-checked {
        background: rgba(56, 189, 248, 0.1);
        border-color: rgba(56, 189, 248, 0.4);
    }

    /* Live Preview HUD */
    .lic-preview-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 600;
    }

    /* Footer Buttons */
    .lic-footer-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        padding-top: 8px;
    }
    .btn-lic-cancel {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 44px;
        padding: 0 20px;
        border-radius: 12px;
        background: transparent;
        border: 1px solid rgba(148, 163, 184, 0.25);
        color: #94a3b8;
        font-weight: 600;
        font-size: 0.88rem;
        text-decoration: none !important;
        transition: all 0.18s ease;
    }
    .btn-lic-cancel:hover {
        background: rgba(15, 23, 42, 0.6);
        color: #f8fafc;
        border-color: rgba(148, 163, 184, 0.4);
    }
    .btn-lic-update {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 44px;
        padding: 0 24px;
        border-radius: 12px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #ffffff;
        font-weight: 600;
        font-size: 0.88rem;
        box-shadow: 0 4px 14px rgba(217, 119, 6, 0.35);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
    }
    .btn-lic-update:hover {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(217, 119, 6, 0.5);
    }

    @media (max-width: 767.98px) {
        .lic-form-header {
            padding: 14px 16px;
            border-radius: 14px;
        }
        .lic-form-header-wrapper {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }
        .lic-form-header-wrapper > div:last-child {
            width: 100%;
        }
        .lic-form-header-wrapper .btn-lic-cancel {
            width: 100% !important;
            justify-content: center;
            height: 40px;
        }
        .lic-form-icon-box {
            width: 38px;
            height: 38px;
            font-size: 1.1rem;
        }
        .lic-name-badge {
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .lic-hud-card {
            padding: 12px 14px;
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
        }
        .lic-hud-card .text-right {
            text-align: left !important;
        }
        .lic-form-bento {
            padding: 14px 16px;
            border-radius: 14px;
        }
        .lic-footer-actions {
            flex-direction: column-reverse;
            width: 100%;
            gap: 10px;
        }
        .btn-lic-cancel, .btn-lic-update {
            width: 100% !important;
            height: 44px;
        }
    }
</style>

<div class="lic-form-container">
    <!-- Header Command Banner -->
    <div class="lic-form-header">
        <div class="lic-form-header-wrapper">
            <div class="lic-form-header-left">
                <div class="lic-form-icon-box">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center flex-wrap" style="gap: 8px; margin-bottom: 2px;">
                        <h5 class="mb-0 font-weight-bold theme-text" style="font-size: 1.15rem;">
                            {{ __('messages.edit_license') }}
                        </h5>
                        <span class="lic-name-badge">
                            <i class="fas fa-cube mr-1 text-xs"></i> {{ $softwareLicense->name }}
                        </span>
                    </div>
                    <small class="text-muted d-block">
                        {{ __('messages.edit_license_subtitle') }}
                    </small>
                </div>
            </div>
            <div>
                <a href="{{ route('software_licenses.index', request()->query()) }}" class="btn-lic-cancel" style="height: 38px; padding: 0 16px; font-size: 0.82rem;">
                    <i class="fas fa-arrow-left mr-2"></i> {{ __('messages.software_licenses') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Compliance Status HUD Banner -->
    @php
        $today = now()->startOfDay();
        $isLifetime = is_null($softwareLicense->expiry_date);
        $diffDays = $softwareLicense->expiry_date ? $today->diffInDays($softwareLicense->expiry_date, false) : null;
    @endphp
    <div class="lic-hud-card">
        <div class="d-flex align-items-center" style="gap: 10px;">
            <i class="fas fa-shield-alt text-info" style="font-size: 1.1rem;"></i>
            <div>
                <div class="text-muted" style="font-size: 0.74rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.04em;">
                    {{ __('messages.current_compliance_status') }}
                </div>
                <div class="theme-text font-weight-bold" style="font-size: 0.90rem;">
                    @if($isLifetime)
                        <span class="text-info"><i class="fas fa-infinity mr-1"></i> {{ __('messages.perpetual_active') }}</span>
                    @elseif($diffDays < 0)
                        <span class="text-danger"><i class="fas fa-times-circle mr-1"></i> {{ __('messages.filter_expired') }} ({{ abs($diffDays) }} {{ __('messages.days_remaining') }} {{ __('messages.expired_ago') }})</span>
                    @elseif($diffDays <= 30)
                        <span class="text-warning"><i class="fas fa-hourglass-half mr-1"></i> {{ __('messages.filter_expiring_soon') }} ({{ $diffDays }} {{ __('messages.days_remaining') }})</span>
                    @else
                        <span class="text-success"><i class="fas fa-check-circle mr-1"></i> {{ __('messages.filter_active') }} ({{ $diffDays }} {{ __('messages.days_remaining') }})</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center" style="gap: 12px;">
            <div class="text-right">
                <div class="text-muted" style="font-size: 0.70rem; text-transform: uppercase; font-weight: 600;">{{ __('messages.total_seats') }}</div>
                <div style="font-family: 'JetBrains Mono', monospace; font-size: 0.92rem; font-weight: 700; color: #818cf8;">
                    {{ $softwareLicense->total_seats }} {{ __('messages.seats') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <form action="{{ route('software_licenses.update', array_merge([$softwareLicense->id], request()->query())) }}" method="POST" id="license-edit-form">
        @csrf
        @method('PUT')

        <!-- Card 1: Software Identity & Seat Allocation -->
        <div class="lic-form-bento mb-3">
            <div class="lic-form-bento-header">
                <div class="lic-form-bento-num">1</div>
                <div>
                    <h6 class="lic-form-bento-title">{{ __('messages.section_software_identity') }}</h6>
                    <div class="lic-form-bento-desc">{{ __('messages.section_software_identity_desc') }}</div>
                </div>
            </div>

            <div class="row">
                <!-- Software Name -->
                <div class="col-md-8 mb-3">
                    <label class="lic-field-label">
                        <span>{{ __('messages.name') }} <span class="text-danger">*</span></span>
                    </label>
                    <input type="text"
                           name="name"
                           class="form-control lic-input-custom @error('name') is-invalid @enderror"
                           value="{{ old('name', $softwareLicense->name) }}"
                           placeholder="Microsoft 365 E5, Windows 11 Enterprise..."
                           required
                           autocomplete="off">
                    @error('name')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Total Seats -->
                <div class="col-md-4 mb-3">
                    <label class="lic-field-label">
                        <span>{{ __('messages.total_seats') }} <span class="text-danger">*</span></span>
                        <span class="badge badge-info" style="font-size: 0.70rem; font-family: 'JetBrains Mono', monospace;">{{ __('messages.seats') }}</span>
                    </label>
                    <input type="number"
                           name="total_seats"
                           class="form-control lic-input-custom @error('total_seats') is-invalid @enderror"
                           value="{{ old('total_seats', $softwareLicense->total_seats) }}"
                           min="1"
                           required
                           autocomplete="off">
                    <span class="lic-helper-text">{{ __('messages.allocated_seats_helper') }}</span>
                    @error('total_seats')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Card 2: License Key & Security Vault -->
        <div class="lic-form-bento mb-3">
            <div class="lic-form-bento-header">
                <div class="lic-form-bento-num">2</div>
                <div>
                    <h6 class="lic-form-bento-title">{{ __('messages.section_license_vault') }}</h6>
                    <div class="lic-form-bento-desc">{{ __('messages.section_license_vault_desc') }}</div>
                </div>
            </div>

            <div class="mb-2">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="lic-field-label mb-0">
                        <span>{{ __('messages.license_key') }}</span>
                    </label>
                    @if($softwareLicense->license_key)
                        <button type="button"
                                class="btn btn-xs btn-outline-info rounded-pill px-3 copy-license-chip"
                                data-license-key="{{ $softwareLicense->license_key }}"
                                style="font-size: 0.74rem; font-weight: 600; border-radius: 6px;">
                            <i class="far fa-copy mr-1"></i> {{ __('messages.copy_license') }}
                        </button>
                    @endif
                </div>
                <textarea name="license_key"
                          class="form-control lic-mono-textarea @error('license_key') is-invalid @enderror"
                          rows="3"
                          placeholder="XXXXX-XXXXX-XXXXX-XXXXX-XXXXX...">{{ old('license_key', ($softwareLicense->license_key && $softwareLicense->license_key !== 'null') ? $softwareLicense->license_key : '') }}</textarea>
                <span class="lic-helper-text">
                    <i class="fas fa-shield-alt text-info mr-1"></i> {{ __('messages.license_security_notice') }}
                </span>
                @error('license_key')
                    <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Card 3: Lifecycle & Expiry Timeline -->
        <div class="lic-form-bento mb-3">
            <div class="lic-form-bento-header">
                <div class="lic-form-bento-num">3</div>
                <div>
                    <h6 class="lic-form-bento-title">{{ __('messages.section_lifecycle_timeline') }}</h6>
                    <div class="lic-form-bento-desc">{{ __('messages.section_lifecycle_timeline_desc') }}</div>
                </div>
            </div>

            @php
                $currentIsLifetime = old('is_lifetime', is_null($softwareLicense->expiry_date) ? '1' : '');
            @endphp

            <!-- Lifetime Interactive Toggle -->
            <div class="lic-lifetime-box" id="lifetime-toggle-box">
                <div class="d-flex align-items-center" style="gap: 12px;">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox"
                               class="custom-control-input"
                               id="is_lifetime"
                               name="is_lifetime"
                               value="1"
                               {{ $currentIsLifetime ? 'checked' : '' }}>
                        <label class="custom-control-label font-weight-bold" for="is_lifetime" style="cursor: pointer; color: #f8fafc; font-size: 0.88rem;">
                            {{ __('messages.lifetime_checkbox_label') }}
                        </label>
                    </div>
                </div>
                <div id="lifetime-status-indicator">
                    <span class="lic-preview-pill" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3);">
                        <i class="fas fa-infinity mr-1"></i> {{ __('messages.perpetual_active') }}
                    </span>
                </div>
            </div>

            <!-- Expiry Date Picker & Live Preview -->
            <div id="expiry-date-group" style="{{ $currentIsLifetime ? 'display: none;' : '' }}">
                <div class="row">
                    <div class="col-md-7 mb-2">
                        <label class="lic-field-label">
                            <span>{{ __('messages.expiry_date') }}</span>
                            <span class="text-warning" style="font-size: 0.72rem;">{{ __('messages.leave_empty_lifetime') }}</span>
                        </label>
                        <input type="date"
                               id="expiry_date_input"
                               name="expiry_date"
                               class="form-control lic-input-custom @error('expiry_date') is-invalid @enderror"
                               value="{{ old('expiry_date', $softwareLicense->expiry_date ? $softwareLicense->expiry_date->format('Y-m-d') : '') }}">
                        @error('expiry_date')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-5 mb-2 d-flex flex-column justify-content-center">
                        <div class="lic-field-label">
                            <span>{{ __('messages.live_status_preview') }}</span>
                        </div>
                        <div id="expiry-preview-widget" class="d-flex align-items-center" style="height: 44px;">
                            <span class="text-muted" style="font-size: 0.82rem;">{{ __('messages.select_date_calculate') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Stakeholder Ownership & Procurement Notes -->
        <div class="lic-form-bento mb-3">
            <div class="lic-form-bento-header">
                <div class="lic-form-bento-num">4</div>
                <div>
                    <h6 class="lic-form-bento-title">{{ __('messages.section_ownership_notes') }}</h6>
                    <div class="lic-form-bento-desc">{{ __('messages.section_ownership_notes_desc') }}</div>
                </div>
            </div>

            <div class="row">
                <!-- PIC Select -->
                <div class="col-md-6 mb-3">
                    <label class="lic-field-label">
                        <span>{{ __('messages.pic') }}</span>
                    </label>
                    <select name="pic_id" class="form-control select2 @error('pic_id') is-invalid @enderror">
                        <option value="">{{ __('messages.select') }} {{ __('messages.pic') }}...</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('pic_id', $softwareLicense->pic_id) == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }} ({{ $emp->employee_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('pic_id')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="col-md-6 mb-3">
                    <label class="lic-field-label">
                        <span>{{ __('messages.notes') }}</span>
                    </label>
                    <textarea name="notes"
                              class="form-control lic-mono-textarea @error('notes') is-invalid @enderror"
                              rows="3"
                              placeholder="{{ __('messages.notes_license_placeholder') }}">{{ old('notes', ($softwareLicense->notes && $softwareLicense->notes !== 'null') ? $softwareLicense->notes : '') }}</textarea>
                    @error('notes')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="lic-footer-actions">
            <a href="{{ route('software_licenses.index', request()->query()) }}" class="btn-lic-cancel">
                {{ __('messages.cancel') }}
            </a>
            <button type="submit" class="btn-lic-update">
                <i class="fas fa-check-circle"></i>
                <span>{{ __('messages.update') }}</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.select2').select2({ width: '100%' });

    var $checkbox = $('#is_lifetime');
    var $expiryGroup = $('#expiry-date-group');
    var $dateInput = $('#expiry_date_input');
    var $box = $('#lifetime-toggle-box');
    var $previewWidget = $('#expiry-preview-widget');

    function updateLifetimeState() {
        if ($checkbox.is(':checked')) {
            $expiryGroup.slideUp(180);
            $dateInput.val('');
            $box.addClass('is-checked');
        } else {
            $expiryGroup.slideDown(180);
            $box.removeClass('is-checked');
            updateExpiryPreview();
        }
    }

    function updateExpiryPreview() {
        var val = $dateInput.val();
        if (!val) {
            $previewWidget.html('<span class="text-muted" style="font-size: 0.82rem;">' + "{{ __('messages.select_date_calculate') }}" + '</span>');
            return;
        }

        var selected = new Date(val);
        var now = new Date();
        now.setHours(0, 0, 0, 0);

        var diffTime = selected.getTime() - now.getTime();
        var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays < 0) {
            $previewWidget.html(
                '<span class="lic-preview-pill" style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.35);">' +
                '<i class="fas fa-times-circle mr-1"></i> ' + "{{ __('messages.filter_expired') }}" + ' (' + Math.abs(diffDays) + ' ' + "{{ __('messages.days_remaining') }}" + ' ' + "{{ __('messages.expired_ago') }}" + ')' +
                '</span>'
            );
        } else if (diffDays <= 30) {
            $previewWidget.html(
                '<span class="lic-preview-pill" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.35);">' +
                '<i class="fas fa-hourglass-half mr-1"></i> ' + (diffDays === 0 ? "{{ __('messages.today') }}" : diffDays + ' ' + "{{ __('messages.days_remaining') }}") +
                '</span>'
            );
        } else {
            $previewWidget.html(
                '<span class="lic-preview-pill" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.35);">' +
                '<i class="fas fa-check-circle mr-1"></i> ' + "{{ __('messages.filter_active') }}" + ' (' + diffDays + ' ' + "{{ __('messages.days_remaining') }}" + ')' +
                '</span>'
            );
        }
    }

    $checkbox.on('change', updateLifetimeState);
    $dateInput.on('input change', updateExpiryPreview);

    // Robust Cross-Browser Clipboard Helper
    function copyTextToClipboard(text) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text);
        } else {
            var textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            return new Promise(function(resolve, reject) {
                try {
                    var successful = document.execCommand('copy');
                    document.body.removeChild(textArea);
                    if (successful) resolve();
                    else reject(new Error('execCommand copy failed'));
                } catch (err) {
                    document.body.removeChild(textArea);
                    reject(err);
                }
            });
        }
    }

    // 1-Click Copy License Key with Toast
    $(document).on('click', '.copy-license-chip', function() {
        var key = $(this).data('license-key');
        if (!key) return;

        copyTextToClipboard(key).then(function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    icon: 'success',
                    title: "{{ __('messages.copy_license_success') }}",
                    background: 'rgba(15, 23, 42, 0.95)',
                    color: '#f8fafc'
                });
            }
        });
    });

    // Initial state
    if ($dateInput.val()) {
        $checkbox.prop('checked', false);
    }
    updateLifetimeState();
});
</script>
@endpush

