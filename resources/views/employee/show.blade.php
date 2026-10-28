@extends('layouts.admin')

@section('title', (__('messages.employee_profile') ?? 'Employee Profile') . ' - ' . $employee->name)

@push('styles')
<style>
    .master-header-banner {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.65), rgba(30, 41, 59, 0.45));
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 20px;
        padding: 16px 22px;
        backdrop-filter: blur(16px);
        margin-top: 0 !important;
        margin-bottom: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }
    body.light-mode .master-header-banner {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(241, 245, 249, 0.85));
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
    }

    .profile-hero-card {
        border-radius: 20px !important;
        overflow: hidden !important;
        border: 1px solid rgba(148, 163, 184, 0.14) !important;
        background: rgba(15, 23, 42, 0.45) !important;
        backdrop-filter: blur(14px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25) !important;
        transition: all 0.25s ease !important;
    }
    body.light-mode .profile-hero-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05) !important;
    }

    .user-avatar-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        color: #ffffff;
        font-size: 2.2rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.35);
        border: 3px solid rgba(255, 255, 255, 0.2);
        flex-shrink: 0;
    }

    .profile-meta-grid {
        background: rgba(15, 23, 42, 0.3);
        border-radius: 16px;
        padding: 16px 20px;
        border: 1px solid rgba(148, 163, 184, 0.12);
    }
    body.light-mode .profile-meta-grid {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
    }

    .meta-box-item {
        padding: 8px 12px;
    }
    .meta-box-label {
        font-size: 0.76rem;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    body.light-mode .meta-box-label {
        color: #64748b;
    }
    .meta-box-value {
        font-size: 0.92rem;
        font-weight: 700;
        color: var(--color-ink-0);
        word-break: break-word;
    }

    .tech-credential-card {
        background: rgba(15, 23, 42, 0.3);
        border-radius: 16px;
        padding: 16px 20px;
        border: 1px solid rgba(148, 163, 184, 0.12);
        height: 100%;
    }
    body.light-mode .tech-credential-card {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
    }

    .tech-param-pill {
        background: rgba(15, 23, 42, 0.4);
        border-radius: 12px;
        padding: 10px 14px;
        border: 1px solid rgba(148, 163, 184, 0.12);
    }
    body.light-mode .tech-param-pill {
        background: #ffffff !important;
        border-color: #cbd5e1 !important;
    }

    /* Desktop Table View Styling */
    @media (min-width: 768px) {
        .profile-desktop-table-wrapper {
            display: block !important;
            position: relative !important;
            width: 100% !important;
            overflow-x: auto !important;
        }
        .profile-mobile-card-wrapper {
            display: none !important;
        }

        .profile-locked-table {
            width: 100% !important;
            margin-bottom: 0 !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }
        .profile-locked-table th,
        .profile-locked-table td {
            white-space: nowrap !important;
            vertical-align: middle !important;
            padding: 14px 18px !important;
        }
        .profile-locked-table th {
            font-family: var(--font-mono);
            font-size: 0.72rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #94a3b8;
            background: rgba(15, 23, 42, 0.6) !important;
            border-bottom: 1px solid rgba(148, 163, 184, 0.12) !important;
        }
        body.light-mode .profile-locked-table th {
            color: #64748b;
            background: #f8fafc !important;
            border-bottom-color: #e2e8f0 !important;
        }
        .profile-locked-table th:first-child,
        .profile-locked-table td:first-child {
            position: static !important;
            left: auto !important;
            box-shadow: none !important;
            border-right: none !important;
        }
        .sticky-action-col {
            position: sticky !important;
            right: 0 !important;
            background: #0f172a !important;
            z-index: 10 !important;
            box-shadow: -8px 0 16px rgba(0, 0, 0, 0.4) !important;
            border-left: 1px solid rgba(148, 163, 184, 0.15) !important;
        }
        body.light-mode .sticky-action-col {
            background: #ffffff !important;
            box-shadow: -8px 0 16px rgba(15, 23, 42, 0.1) !important;
            border-left-color: #e2e8f0 !important;
        }
    }

    /* Mobile Mode Styling (< 768px): STRICTLY HIDE TABLE & SHOW ONLY CARDS */
    @media (max-width: 767.98px) {
        .profile-desktop-table-wrapper {
            display: none !important;
        }
        .profile-mobile-card-wrapper {
            display: block !important;
        }

        .profile-action-buttons {
            width: 100% !important;
            margin-top: 10px;
            gap: 8px !important;
        }
        .profile-action-buttons .btn {
            flex: 1 1 0% !important;
            width: 50% !important;
            justify-content: center !important;
            height: 42px !important;
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

    .card-header-single-row {
        display: flex !important;
        align-items: center !important;
        flex-wrap: nowrap !important;
        gap: 12px !important;
        width: 100% !important;
    }
</style>
@endpush

@section('content')

<!-- Hallmark Master Header Banner -->
<div class="master-header-banner">
    <div class="d-flex align-items-center justify-content-between flex-wrap flex-md-nowrap" style="gap: 16px;">
        <div class="d-flex align-items-center" style="gap: 14px; min-width: 0; flex: 1;">
            <div style="width: 44px; height: 44px; border-radius: 14px; background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(79, 70, 229, 0.08)); border: 1px solid rgba(99, 102, 241, 0.35); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 1.2rem; flex-shrink: 0; box-shadow: 0 0 14px rgba(99, 102, 241, 0.25);">
                <i class="fas fa-id-badge"></i>
            </div>
            <div style="flex: 1; min-width: 0;">
                <h5 class="mb-0 font-weight-bold theme-text text-truncate" style="font-size: 1.15rem; line-height: 1.2;">{{ __('messages.employee_profile') }}</h5>
                <small class="text-muted d-block" style="font-size: 0.8rem; margin-top: 2px; white-space: normal; line-height: 1.3;">{{ __('messages.employee_profile_subtitle') }}</small>
            </div>
        </div>

        <div class="d-flex align-items-center flex-nowrap profile-action-buttons ml-auto" style="gap: 10px; flex-shrink: 0;">
            <a href="{{ route('employees.index') }}" class="btn btn-sm btn-action-secondary text-nowrap">
                <i class="fas fa-arrow-left mr-1"></i> {{ __('messages.back') }}
            </a>
            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-action-primary text-nowrap">
                <i class="fas fa-edit mr-1"></i> {{ __('messages.edit_profile') }}
            </a>
        </div>
    </div>
</div>

<!-- Full-Width Horizontal Master Profile & Credentials Hero Card -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card profile-hero-card p-4">
            <div class="row align-items-center" style="gap: 16px 0;">
                <!-- Profile Avatar & NIK -->
                <div class="col-lg-3 col-md-4 text-center border-right-md pr-lg-4" style="border-color: rgba(148, 163, 184, 0.12) !important;">
                    <div class="mb-3 d-flex justify-content-center">
                        @if($employee->photo)
                            <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->name }}" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; box-shadow: 0 10px 25px rgba(79, 70, 229, 0.35); border: 3px solid rgba(99, 102, 241, 0.4);">
                        @else
                            <div class="user-avatar-circle">
                                {{ strtoupper(substr($employee->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <h5 class="font-weight-bold theme-text mb-1" style="font-size: 1.2rem;">{{ $employee->name }}</h5>
                    <div class="d-flex align-items-center justify-content-center flex-wrap" style="gap: 6px; margin-top: 6px;">
                        <span class="badge" style="background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3); font-size: 0.78rem; font-family: var(--font-mono); padding: 4px 12px; border-radius: 999px;">
                            NIK: {{ $employee->employee_id }}
                        </span>
                        @if($employee->status == 'Active')
                            <span class="badge px-3 py-1" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; font-size: 0.75rem; border-radius: 999px;">
                                <i class="fas fa-circle mr-1" style="font-size: 0.45rem; vertical-align: middle;"></i> {{ __('messages.active') }}
                            </span>
                        @else
                            <span class="badge px-3 py-1" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; font-size: 0.75rem; border-radius: 999px;">
                                <i class="fas fa-circle mr-1" style="font-size: 0.45rem; vertical-align: middle;"></i> {{ __('messages.inactive') }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Profile Metadata Grid -->
                <div class="col-lg-5 col-md-8 px-lg-4">
                    <div class="profile-meta-grid">
                        <div class="row">
                            <div class="col-6 mb-3 meta-box-item">
                                <div class="meta-box-label"><i class="fas fa-building text-warning"></i> {{ __('messages.department') }}</div>
                                <div class="meta-box-value">{{ $employee->department->name ?? '-' }}</div>
                            </div>
                            <div class="col-6 mb-3 meta-box-item">
                                <div class="meta-box-label"><i class="fas fa-map-marker-alt text-success"></i> {{ __('messages.location') }}</div>
                                <div class="meta-box-value">{{ $employee->location->name ?? '-' }}</div>
                            </div>
                            <div class="col-6 mb-3 meta-box-item">
                                <div class="meta-box-label"><i class="fas fa-user-shield text-indigo"></i> {{ __('messages.supervisor') }}</div>
                                <div class="meta-box-value">{{ $employee->supervisor->name ?? '-' }}</div>
                            </div>
                            <div class="col-6 mb-3 meta-box-item">
                                <div class="meta-box-label"><i class="fas fa-phone-alt text-info"></i> {{ __('messages.phone') }}</div>
                                <div class="meta-box-value">{{ $employee->phone ?? '-' }}</div>
                            </div>
                            <div class="col-6 meta-box-item">
                                <div class="meta-box-label"><i class="fas fa-envelope text-info"></i> {{ __('messages.email') }}</div>
                                <div class="meta-box-value" style="font-size: 0.86rem;">{{ $employee->email ?? '-' }}</div>
                            </div>
                            <div class="col-6 meta-box-item">
                                <div class="meta-box-label"><i class="fas fa-key text-warning"></i> {{ __('messages.email_password') }}</div>
                                <div class="meta-box-value d-flex align-items-center" style="font-size: 0.86rem;">
                                    @if($employee->email_password)
                                        <input type="password" value="{{ $employee->email_password }}" id="emp-email-pass" class="border-0 bg-transparent theme-text font-weight-bold p-0" readonly style="outline:none; width: 68px; font-family: var(--font-mono); font-size: 0.85rem;">
                                        <button class="btn btn-xs btn-outline-warning toggle-email-pass-btn ml-1" type="button" style="border-radius: 6px; padding: 2px 6px;" title="Lihat Password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-warning copy-license-btn ml-1" data-license-key="{{ $employee->email_password }}" data-label="Password Email" title="Copy Password Email" style="border-radius: 6px; padding: 2px 6px;">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- IT Remote & PC Credentials -->
                <div class="col-lg-4 col-md-12 pl-lg-3 mt-3 mt-lg-0">
                    <div class="tech-credential-card">
                        <div class="d-flex align-items-center mb-3" style="gap: 8px;">
                            <div style="width: 30px; height: 30px; border-radius: 8px; background: rgba(168, 85, 247, 0.15); border: 1px solid rgba(168, 85, 247, 0.3); color: #c084fc; display: flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                                <i class="fas fa-key"></i>
                            </div>
                            <h6 class="font-weight-bold theme-text mb-0" style="font-size: 0.9rem;">{{ __('messages.tech_connection_params') }}</h6>
                        </div>

                        <div class="row" style="gap: 8px 0;">
                            <!-- AnyDesk ID -->
                            <div class="col-6">
                                <div class="tech-param-pill d-flex align-items-center justify-content-between">
                                    <div class="text-truncate">
                                        <small class="text-muted d-block" style="font-size: 0.68rem; font-weight: 600;">ANYDESK ID</small>
                                        <span class="font-weight-bold text-success text-truncate d-block" style="font-family: var(--font-mono); font-size: 0.85rem;">
                                            {{ $employee->anydesk_id ?? '-' }}
                                        </span>
                                    </div>
                                    @if($employee->anydesk_id)
                                        <button type="button" class="btn btn-xs btn-outline-success copy-license-btn ml-1" data-license-key="{{ $employee->anydesk_id }}" data-label="AnyDesk ID" title="Copy AnyDesk ID" style="border-radius: 6px; padding: 2px 6px;">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- AnyDesk Password -->
                            <div class="col-6">
                                <div class="tech-param-pill d-flex align-items-center justify-content-between">
                                    <div class="text-truncate">
                                        <small class="text-muted d-block" style="font-size: 0.68rem; font-weight: 600;">ANYDESK PASS</small>
                                        @if($employee->anydesk_password)
                                            <input type="password" value="{{ $employee->anydesk_password }}" id="emp-anydesk-pass" class="border-0 bg-transparent theme-text font-weight-bold p-0" readonly style="outline:none; width: 65px; font-family: var(--font-mono); font-size: 0.85rem;">
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </div>
                                    @if($employee->anydesk_password)
                                        <button class="btn btn-xs btn-outline-info toggle-anydesk-btn ml-1" type="button" style="border-radius: 6px; padding: 2px 6px;"><i class="fas fa-eye"></i></button>
                                    @endif
                                </div>
                            </div>

                            <!-- PC Username -->
                            <div class="col-6 mt-2">
                                <div class="tech-param-pill d-flex align-items-center justify-content-between">
                                    <div class="text-truncate">
                                        <small class="text-muted d-block" style="font-size: 0.68rem; font-weight: 600;">PC USERNAME</small>
                                        <span class="font-weight-bold text-warning text-truncate d-block" style="font-family: var(--font-mono); font-size: 0.85rem;">
                                            {{ $employee->login_username ?? '-' }}
                                        </span>
                                    </div>
                                    @if($employee->login_username)
                                        <button type="button" class="btn btn-xs btn-outline-warning copy-license-btn ml-1" data-license-key="{{ $employee->login_username }}" data-label="PC Username" title="Copy PC Username" style="border-radius: 6px; padding: 2px 6px;">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- PC Password -->
                            <div class="col-6 mt-2">
                                <div class="tech-param-pill d-flex align-items-center justify-content-between">
                                    <div class="text-truncate">
                                        <small class="text-muted d-block" style="font-size: 0.68rem; font-weight: 600;">PC PASSWORD</small>
                                        @if($employee->login_password)
                                            <input type="password" value="{{ $employee->login_password }}" id="emp-login-pass" class="border-0 bg-transparent theme-text font-weight-bold p-0" readonly style="outline:none; width: 65px; font-family: var(--font-mono); font-size: 0.85rem;">
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </div>
                                    @if($employee->login_password)
                                        <button class="btn btn-xs btn-outline-warning toggle-login-btn ml-1" type="button" style="border-radius: 6px; padding: 2px 6px;"><i class="fas fa-eye"></i></button>
                                    @endif
                                </div>
                            </div>

                            <!-- HRIS Username -->
                            <div class="col-6 mt-2">
                                <div class="tech-param-pill d-flex align-items-center justify-content-between">
                                    <div class="text-truncate">
                                        <small class="text-muted d-block" style="font-size: 0.68rem; font-weight: 600;">HRIS USERNAME</small>
                                        <span class="font-weight-bold text-info text-truncate d-block" style="font-family: var(--font-mono); font-size: 0.85rem;">
                                            {{ $employee->hris_username ?? '-' }}
                                        </span>
                                    </div>
                                    @if($employee->hris_username)
                                        <button type="button" class="btn btn-xs btn-outline-info copy-license-btn ml-1" data-license-key="{{ $employee->hris_username }}" data-label="HRIS Username" title="Copy HRIS Username" style="border-radius: 6px; padding: 2px 6px;">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- HRIS Password -->
                            <div class="col-6 mt-2">
                                <div class="tech-param-pill d-flex align-items-center justify-content-between">
                                    <div class="text-truncate">
                                        <small class="text-muted d-block" style="font-size: 0.68rem; font-weight: 600;">HRIS PASSWORD</small>
                                        @if($employee->hris_password)
                                            <input type="password" value="{{ $employee->hris_password }}" id="emp-hris-pass" class="border-0 bg-transparent theme-text font-weight-bold p-0" readonly style="outline:none; width: 65px; font-family: var(--font-mono); font-size: 0.85rem;">
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </div>
                                    @if($employee->hris_password)
                                        <button class="btn btn-xs btn-outline-info toggle-hris-btn ml-1" type="button" style="border-radius: 6px; padding: 2px 6px;"><i class="fas fa-eye"></i></button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Full-Width Assigned Devices Card -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card profile-hero-card">
            <div class="card-header bg-transparent border-bottom p-3" style="border-color: rgba(148, 163, 184, 0.1) !important;">
                <div class="card-header-single-row">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.3); color: #60a5fa; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; flex-shrink: 0;">
                        <i class="fas fa-laptop"></i>
                    </div>
                    <h6 class="font-weight-bold theme-text mb-0 text-nowrap" style="font-size: 0.98rem; line-height: 1.2;">{{ __('messages.assigned_devices') }}</h6>
                </div>
            </div>
            <div class="card-body p-0">
                @php
                    $assignedAssignments = $employee->assignments->where('status', 'Assigned');
                @endphp

                <!-- Desktop Table View ONLY (>= 768px) -->
                <div class="profile-desktop-table-wrapper">
                    <table class="table table-striped table-hover m-0 theme-table profile-locked-table">
                        <thead>
                            <tr>
                                <th style="min-width: 140px;" class="pl-4">{{ __('messages.category') }}</th>
                                <th style="min-width: 280px;">{{ __('messages.asset_name') }}</th>
                                <th style="min-width: 150px;">{{ __('messages.asset_tag') }}</th>
                                <th style="min-width: 160px;">{{ __('messages.serial_number') }}</th>
                                <th style="min-width: 180px;">{{ __('messages.location') }}</th>
                                <th style="min-width: 110px;" class="text-center sticky-action-col pr-4">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignedAssignments as $assign)
                            <tr>
                                <td class="pl-4">
                                    <span class="badge" style="padding: 5px 12px; background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.25); color: #38bdf8; border-radius: 8px; font-size: 0.78rem;">
                                        {{ $assign->asset->category->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="theme-text font-weight-bold" style="white-space: normal !important; min-width: 280px;">
                                    <a href="{{ route('assets.show', $assign->asset_id) }}" class="text-info text-decoration-none">
                                        {{ $assign->asset->name }}
                                    </a>
                                </td>
                                <td class="theme-text"><code style="padding: 3px 8px; background: rgba(148, 163, 184, 0.1); border-radius: 6px; font-family: var(--font-mono);">{{ $assign->asset->asset_tag }}</code></td>
                                <td class="theme-text text-muted" style="font-size: 0.88rem;">{{ $assign->asset->serial_number ?? '-' }}</td>
                                <td class="theme-text text-muted" style="font-size: 0.88rem;">{{ $assign->asset->location->name ?? '-' }}</td>
                                <td class="text-center sticky-action-col pr-4">
                                    <a href="{{ route('assets.show', $assign->asset_id) }}" class="btn btn-xs btn-action-secondary" style="padding: 4px 14px; font-size: 0.78rem;">
                                        {{ __('messages.view') }}
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted p-4">{{ __('messages.no_devices_assigned') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View ONLY (< 768px) -->
                <div class="profile-mobile-card-wrapper p-3">
                    @forelse($assignedAssignments as $assign)
                    <div class="profile-mobile-card">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge" style="padding: 4px 10px; background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.25); color: #38bdf8; border-radius: 6px; font-size: 0.75rem;">
                                {{ $assign->asset->category->name ?? '-' }}
                            </span>
                            <code style="padding: 2px 6px; background: rgba(148, 163, 184, 0.1); border-radius: 4px; font-family: var(--font-mono); font-size: 0.78rem;">
                                {{ $assign->asset->asset_tag }}
                            </code>
                        </div>

                        <div class="mb-3">
                            <a href="{{ route('assets.show', $assign->asset_id) }}" class="font-weight-bold text-info text-decoration-none d-block" style="font-size: 0.95rem; line-height: 1.3;">
                                {{ $assign->asset->name }}
                            </a>
                        </div>

                        <div class="mobile-card-row">
                            <span class="mobile-card-label">{{ __('messages.serial_number') }}</span>
                            <span class="mobile-card-value" style="font-size: 0.82rem;">{{ $assign->asset->serial_number ?? '-' }}</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">{{ __('messages.location') }}</span>
                            <span class="mobile-card-value" style="font-size: 0.82rem;">{{ $assign->asset->location->name ?? '-' }}</span>
                        </div>

                        <div class="mt-3 pt-2 border-top d-flex justify-content-end" style="border-color: rgba(148, 163, 184, 0.1) !important;">
                            <a href="{{ route('assets.show', $assign->asset_id) }}" class="btn btn-sm btn-action-secondary w-100 text-center" style="height: 38px; font-size: 0.82rem; font-weight: 600;">
                                <i class="fas fa-eye mr-1"></i> {{ __('messages.view_asset_details') }}
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted p-4" style="font-size: 0.88rem;">{{ __('messages.no_devices_assigned') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Full-Width Associated IP Addresses Card -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card profile-hero-card">
            <div class="card-header bg-transparent border-bottom p-3" style="border-color: rgba(148, 163, 184, 0.1) !important;">
                <div class="card-header-single-row">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; flex-shrink: 0;">
                        <i class="fas fa-network-wired"></i>
                    </div>
                    <h6 class="font-weight-bold theme-text mb-0 text-nowrap" style="font-size: 0.98rem; line-height: 1.2;">{{ __('messages.associated_ips') }}</h6>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Desktop Table View ONLY (>= 768px) -->
                <div class="profile-desktop-table-wrapper">
                    <table class="table table-striped table-hover m-0 theme-table profile-locked-table">
                        <thead>
                            <tr>
                                <th style="min-width: 160px;" class="pl-4">{{ __('messages.ip_address') }}</th>
                                <th style="min-width: 170px;">{{ __('messages.mac_address') }}</th>
                                <th style="min-width: 110px;">{{ __('messages.status') }}</th>
                                <th style="min-width: 170px;">{{ __('messages.vlans') }}</th>
                                <th style="min-width: 160px;">{{ __('messages.gateway') }}</th>
                                <th style="min-width: 280px;" class="pr-4">{{ __('messages.notes') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employee->ipAddresses as $ip)
                            <tr>
                                <td class="pl-4 text-success font-weight-bold" style="font-family: var(--font-mono); font-size: 0.88rem;">
                                    {{ $ip->ip_address }}
                                </td>
                                <td class="theme-text"><code style="font-size: 0.84rem;">{{ $ip->mac_address ?? '-' }}</code></td>
                                <td class="theme-text">
                                    <span class="badge badge-pill badge-success" style="font-size: 0.75rem; padding: 4px 10px;">{{ $ip->status }}</span>
                                </td>
                                <td class="theme-text" style="font-size: 0.85rem;">{{ $ip->vlan ? 'VLAN ' . $ip->vlan->vlan_number . ' (' . $ip->vlan->name . ')' : '-' }}</td>
                                <td class="theme-text" style="font-size: 0.85rem;">{{ $ip->gateway ?? '-' }}</td>
                                <td class="theme-text pr-4 text-muted" style="font-size: 0.84rem; white-space: normal !important; min-width: 280px;">{{ $ip->notes ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted p-4">{{ __('messages.no_ips_mapped') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View ONLY (< 768px) -->
                <div class="profile-mobile-card-wrapper p-3">
                    @forelse($employee->ipAddresses as $ip)
                    <div class="profile-mobile-card">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-success font-weight-bold" style="font-family: var(--font-mono); font-size: 1.05rem;">
                                {{ $ip->ip_address }}
                            </span>
                            <span class="badge badge-pill badge-success" style="font-size: 0.72rem; padding: 3px 8px;">
                                {{ $ip->status }}
                            </span>
                        </div>

                        <div class="mobile-card-row">
                            <span class="mobile-card-label">{{ __('messages.mac_address') }}</span>
                            <span class="mobile-card-value"><code style="font-size: 0.8rem;">{{ $ip->mac_address ?? '-' }}</code></span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">{{ __('messages.vlans') }}</span>
                            <span class="mobile-card-value" style="font-size: 0.82rem;">{{ $ip->vlan ? 'VLAN ' . $ip->vlan->vlan_number . ' (' . $ip->vlan->name . ')' : '-' }}</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">{{ __('messages.gateway') }}</span>
                            <span class="mobile-card-value" style="font-size: 0.82rem;">{{ $ip->gateway ?? '-' }}</span>
                        </div>

                        @if($ip->notes)
                        <div class="mt-2 pt-2 border-top" style="border-color: rgba(148, 163, 184, 0.1) !important;">
                            <small class="text-muted d-block mb-1" style="font-size: 0.72rem; font-weight: 600;">NOTES</small>
                            <div class="theme-text" style="font-size: 0.82rem; line-height: 1.3;">
                                {{ $ip->notes }}
                            </div>
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="text-center text-muted p-4" style="font-size: 0.88rem;">{{ __('messages.no_ips_mapped') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Full-Width Managed Software Licenses Card -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card profile-hero-card">
            <div class="card-header bg-transparent border-bottom p-3" style="border-color: rgba(148, 163, 184, 0.1) !important;">
                <div class="card-header-single-row">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #fbbf24; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; flex-shrink: 0;">
                        <i class="fas fa-key"></i>
                    </div>
                    <h6 class="font-weight-bold theme-text mb-0 text-nowrap" style="font-size: 0.98rem; line-height: 1.2;">{{ __('messages.managed_software') }}</h6>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Desktop Table View ONLY (>= 768px) -->
                <div class="profile-desktop-table-wrapper">
                    <table class="table table-striped table-hover m-0 theme-table profile-locked-table">
                        <thead>
                            <tr>
                                <th style="min-width: 180px;" class="pl-4">{{ __('messages.name') }}</th>
                                <th style="min-width: 240px;">{{ __('messages.license_key') }}</th>
                                <th style="min-width: 150px;">{{ __('messages.expiry_date') }}</th>
                                <th style="min-width: 130px;">{{ __('messages.total_seats') }}</th>
                                <th style="min-width: 110px;" class="text-center sticky-action-col pr-4">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employee->softwareLicenses as $lic)
                            <tr>
                                <td class="pl-4 theme-text font-weight-bold text-info" style="font-size: 0.88rem;">{{ $lic->name }}</td>
                                <td class="theme-text">
                                    @if($lic->license_key)
                                        <div class="d-flex align-items-center" style="gap: 6px;">
                                            <code class="copy-license-btn" style="user-select: all; font-size: 0.84rem; cursor: pointer;" title="{{ __('messages.copy_license') }}: {{ $lic->license_key }}" data-license-key="{{ $lic->license_key }}" data-label="Lisensi {{ $lic->name }}">
                                                {{ Str::limit($lic->license_key, 28) }}
                                            </code>
                                            <button type="button" class="btn btn-xs btn-outline-success copy-license-btn" style="padding: 2px 6px; line-height: 1; border-radius: 4px;" title="{{ __('messages.copy_license') }}" data-license-key="{{ $lic->license_key }}" data-label="Lisensi {{ $lic->name }}">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="theme-text text-muted" style="font-size: 0.85rem;">{{ $lic->expiry_date ? $lic->expiry_date->format('d M Y') : 'Lifetime' }}</td>
                                <td class="theme-text text-muted" style="font-size: 0.85rem;">{{ $lic->total_seats }} Seats</td>
                                <td class="text-center sticky-action-col pr-4">
                                    <a href="{{ route('software_licenses.edit', $lic) }}" class="btn btn-xs btn-action-secondary" style="padding: 4px 14px; font-size: 0.78rem;">
                                        {{ __('messages.edit') }}
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted p-4">{{ __('messages.no_software_pic') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View ONLY (< 768px) -->
                <div class="profile-mobile-card-wrapper p-3">
                    @forelse($employee->softwareLicenses as $lic)
                    <div class="profile-mobile-card">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="font-weight-bold text-info" style="font-size: 0.95rem;">
                                {{ $lic->name }}
                            </span>
                            <span class="badge badge-pill badge-warning" style="font-size: 0.72rem; padding: 3px 8px;">
                                {{ $lic->total_seats }} Seats
                            </span>
                        </div>

                        @if($lic->license_key)
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">{{ __('messages.license_key') }}</span>
                            <div class="d-flex align-items-center" style="gap: 6px;">
                                <code class="copy-license-btn" style="user-select: all; font-size: 0.78rem; cursor: pointer;" data-license-key="{{ $lic->license_key }}" data-label="Lisensi {{ $lic->name }}">
                                    {{ Str::limit($lic->license_key, 20) }}
                                </code>
                                <button type="button" class="btn btn-xs btn-outline-success copy-license-btn" style="padding: 1px 5px; border-radius: 4px;" data-license-key="{{ $lic->license_key }}" data-label="Lisensi {{ $lic->name }}">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        @endif

                        <div class="mobile-card-row">
                            <span class="mobile-card-label">{{ __('messages.expiry_date') }}</span>
                            <span class="mobile-card-value" style="font-size: 0.82rem;">{{ $lic->expiry_date ? $lic->expiry_date->format('d M Y') : 'Lifetime' }}</span>
                        </div>

                        <div class="mt-3 pt-2 border-top d-flex justify-content-end" style="border-color: rgba(148, 163, 184, 0.1) !important;">
                            <a href="{{ route('software_licenses.edit', $lic) }}" class="btn btn-sm btn-action-secondary w-100 text-center" style="height: 38px; font-size: 0.82rem; font-weight: 600;">
                                <i class="fas fa-edit mr-1"></i> {{ __('messages.edit_license') }}
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted p-4" style="font-size: 0.88rem;">{{ __('messages.no_software_pic') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.toggle-anydesk-btn').click(function() {
        var input = $('#emp-anydesk-pass');
        var icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    $('.toggle-login-btn').click(function() {
        var input = $('#emp-login-pass');
        var icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    $('.toggle-hris-btn').click(function() {
        var input = $('#emp-hris-pass');
        var icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    $('.toggle-email-pass-btn').click(function() {
        var input = $('#emp-email-pass');
        var icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    /* Universal Copy to Clipboard with Visual Icon Swap & Localized Toastr Feedback */
    $(document).on('click', '.copy-license-btn', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var key = $btn.attr('data-license-key') || $btn.data('license-key');
        var label = $btn.attr('data-label') || $btn.data('label') || 'Teks';

        if (!key) return;

        function showCopySuccess() {
            var $icon = $btn.find('i');
            var originalClass = $icon.attr('class') || 'fas fa-copy';

            // Swap icon to green checkmark
            $icon.attr('class', 'fas fa-check text-success');
            $btn.addClass('border-success');

            // Show Toastr notification
            if (typeof toastr !== 'undefined') {
                toastr.success(label + ' berhasil disalin ke clipboard!', 'Berhasil!');
            }

            // Revert back after 2 seconds
            setTimeout(function() {
                $icon.attr('class', originalClass);
                $btn.removeClass('border-success');
            }, 2000);
        }

        function showCopyError() {
            if (typeof toastr !== 'undefined') {
                toastr.error('Gagal menyalin ' + label, 'Error!');
            }
        }

        // Try Clipboard API (HTTPS/Localhost), fallback to execCommand (HTTP/IP)
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(key).then(function() {
                showCopySuccess();
            }).catch(function() {
                execCopyFallback(key, showCopySuccess, showCopyError);
            });
        } else {
            execCopyFallback(key, showCopySuccess, showCopyError);
        }
    });

    function execCopyFallback(text, onSuccess, onError) {
        try {
            var $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(text).select();
            var successful = document.execCommand('copy');
            $temp.remove();
            if (successful) {
                onSuccess();
            } else {
                if (onError) onError();
            }
        } catch (err) {
            if (onError) onError();
        }
    }
});
</script>
@endpush

