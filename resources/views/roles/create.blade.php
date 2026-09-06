@extends('layouts.admin')

@section('title', __('messages.create_role_title'))

@section('content')
<style>
    /* Hallmark · component: role-governance-console · genre: modern-minimal · theme: workbench
     * states: default · hover · focus-visible · active · disabled · checked
     * contrast: pass WCAG AA · mobile: 320/375/414/768px compliant
     */
    .content-header {
        display: none !important;
    }

    :root {
        --hm-card-bg: rgba(15, 23, 42, 0.55);
        --hm-card-border: rgba(148, 163, 184, 0.14);
        --hm-tile-bg: rgba(15, 23, 42, 0.38);
        --hm-tile-border: rgba(148, 163, 184, 0.12);
        --hm-tile-hover-bg: rgba(30, 41, 59, 0.55);
        --hm-tile-hover-border: rgba(96, 165, 250, 0.35);
        --hm-tile-checked-bg: rgba(59, 130, 246, 0.12);
        --hm-tile-checked-border: rgba(96, 165, 250, 0.5);
        --hm-dock-bg: rgba(15, 23, 42, 0.88);
        --hm-dock-border: rgba(148, 163, 184, 0.18);
        --hm-ink-primary: #f8fafc;
        --hm-ink-muted: #94a3b8;
    }

    body.light-mode {
        --hm-card-bg: #ffffff;
        --hm-card-border: #e2e8f0;
        --hm-tile-bg: #f8fafc;
        --hm-tile-border: #e2e8f0;
        --hm-tile-hover-bg: #f1f5f9;
        --hm-tile-hover-border: #cbd5e1;
        --hm-tile-checked-bg: #eff6ff;
        --hm-tile-checked-border: #93c5fd;
        --hm-dock-bg: rgba(255, 255, 255, 0.92);
        --hm-dock-border: #cbd5e1;
        --hm-ink-primary: #0f172a;
        --hm-ink-muted: #64748b;
    }

    /* Container Protection against horizontal overflow */
    .hallmark-console-container {
        width: 100%;
        max-width: 100%;
        overflow-x: clip;
        padding-bottom: calc(100px + env(safe-area-inset-bottom, 20px));
    }

    /* Header Styling */
    .hallmark-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 1.25rem;
    }
    .hallmark-back-btn {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: var(--hm-tile-bg);
        border: 1px solid var(--hm-tile-border);
        color: var(--hm-ink-muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.18s ease;
        flex-shrink: 0;
        text-decoration: none !important;
    }
    .hallmark-back-btn:hover {
        color: var(--hm-ink-primary);
        border-color: var(--hm-tile-hover-border);
        background: var(--hm-tile-hover-bg);
        transform: translateX(-2px);
    }
    .hallmark-tag-pill {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 9999px;
        background: rgba(99, 102, 241, 0.12);
        color: #818cf8;
        border: 1px solid rgba(99, 102, 241, 0.28);
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* Workbench Bento Cards */
    .workbench-card {
        background: var(--hm-card-bg);
        border: 1px solid var(--hm-card-border);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        transition: border-color 0.2s ease;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }
    .workbench-card-header {
        padding: 14px 20px;
        border-bottom: 1px solid var(--hm-card-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    /* Form Controls */
    .hallmark-input {
        height: 46px !important;
        border-radius: 12px !important;
        border: 1px solid var(--hm-card-border) !important;
        background: var(--hm-tile-bg) !important;
        color: var(--hm-ink-primary) !important;
        font-size: 0.92rem !important;
        font-weight: 500 !important;
        padding-left: 16px !important;
        padding-right: 16px !important;
        box-shadow: none !important;
        transition: all 0.18s ease !important;
    }
    .hallmark-input:focus {
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2) !important;
        background: rgba(15, 23, 42, 0.5) !important;
    }
    body.light-mode .hallmark-input:focus {
        background: #ffffff !important;
    }

    /* Live Telemetry Meter */
    .telemetry-gauge {
        background: var(--hm-tile-bg);
        border: 1px solid var(--hm-tile-border);
        border-radius: 14px;
        padding: 16px;
        position: relative;
        overflow: hidden;
    }
    .telemetry-pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #3b82f6;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
        animation: telemetryPulse 2s infinite;
    }
    @keyframes telemetryPulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(59, 130, 246, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
    }
    .meter-progress-track {
        height: 8px;
        border-radius: 9999px;
        background: rgba(148, 163, 184, 0.15);
        overflow: hidden;
        margin-top: 10px;
        position: relative;
    }
    .meter-progress-bar {
        height: 100%;
        width: 0%;
        border-radius: 9999px;
        background: linear-gradient(90deg, #60a5fa 0%, #3b82f6 50%, #818cf8 100%);
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), background 0.3s ease;
    }
    .tier-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        transition: all 0.25s ease;
    }
    .tier-restricted {
        background: rgba(148, 163, 184, 0.12);
        color: #94a3b8;
        border: 1px solid rgba(148, 163, 184, 0.25);
    }
    .tier-standard {
        background: rgba(14, 165, 233, 0.12);
        color: #38bdf8;
        border: 1px solid rgba(14, 165, 233, 0.3);
    }
    .tier-elevated {
        background: rgba(16, 185, 129, 0.12);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
    .tier-full-access {
        background: rgba(168, 85, 247, 0.14);
        color: #c084fc;
        border: 1px solid rgba(168, 85, 247, 0.4);
        box-shadow: 0 0 16px rgba(168, 85, 247, 0.2);
    }

    /* Quick Preset Accelerators */
    .presets-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }
    .preset-chip {
        border-radius: 12px;
        padding: 8px 14px;
        background: var(--hm-tile-bg);
        border: 1px solid var(--hm-tile-border);
        color: var(--hm-ink-primary);
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.16s ease;
        user-select: none;
    }
    .preset-chip:hover {
        background: var(--hm-tile-hover-bg);
        border-color: var(--hm-tile-hover-border);
        transform: translateY(-1px);
        color: #60a5fa;
    }
    .preset-chip:active {
        transform: translateY(0);
    }

    /* Integrated Search Filter */
    .search-filter-box {
        position: relative;
        width: 100%;
    }
    .search-filter-box .search-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--hm-ink-muted);
        pointer-events: none;
        font-size: 0.88rem;
    }
    .search-filter-input {
        height: 44px !important;
        padding-left: 42px !important;
        padding-right: 60px !important;
        border-radius: 12px !important;
        border: 1px solid var(--hm-card-border) !important;
        background: var(--hm-card-bg) !important;
        color: var(--hm-ink-primary) !important;
        font-size: 0.88rem !important;
        transition: all 0.2s ease !important;
    }
    .search-shortcut-tag {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.68rem;
        font-weight: 700;
        font-family: monospace;
        padding: 2px 6px;
        border-radius: 6px;
        background: rgba(148, 163, 184, 0.15);
        color: var(--hm-ink-muted);
        pointer-events: none;
        border: 1px solid rgba(148, 163, 184, 0.25);
    }

    /* Tactile Permission Tiles Grid */
    .perm-grid-workbench {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 10px;
        padding: 16px;
    }
    .perm-tile-workbench {
        background: var(--hm-tile-bg);
        border: 1px solid var(--hm-tile-border);
        border-radius: 12px;
        padding: 12px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        cursor: pointer;
        user-select: none;
        transition: all 0.16s ease;
        position: relative;
    }
    .perm-tile-workbench:hover {
        background: var(--hm-tile-hover-bg);
        border-color: var(--hm-tile-hover-border);
        transform: translateY(-1px);
    }
    .perm-tile-workbench:active {
        transform: scale(0.99);
    }
    .perm-tile-workbench.is-checked {
        background: var(--hm-tile-checked-bg);
        border-color: var(--hm-tile-checked-border);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08);
    }
    .perm-tile-workbench.is-checked .perm-title {
        color: #60a5fa !important;
    }
    body.light-mode .perm-tile-workbench.is-checked .perm-title {
        color: #1d4ed8 !important;
    }
    .perm-title {
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--hm-ink-primary);
        line-height: 1.3;
        margin-bottom: 2px;
        transition: color 0.15s ease;
    }
    .perm-code {
        font-size: 0.72rem;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        color: var(--hm-ink-muted);
        opacity: 0.8;
    }

    /* Domain Header Accents */
    .domain-icon-badge {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
    }
    .domain-nav-accent { background: rgba(14, 165, 233, 0.12); color: #38bdf8; border: 1px solid rgba(14, 165, 233, 0.3); }
    .domain-action-accent { background: rgba(16, 185, 129, 0.12); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
    .domain-tools-accent { background: rgba(168, 85, 247, 0.12); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3); }

    .domain-count-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 9999px;
        background: rgba(148, 163, 184, 0.12);
        color: var(--hm-ink-muted);
        border: 1px solid rgba(148, 163, 184, 0.22);
    }
    .btn-domain-toggle {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 8px;
        background: transparent;
        border: 1px solid var(--hm-tile-border);
        color: var(--hm-ink-muted);
        transition: all 0.15s ease;
    }
    .btn-domain-toggle:hover {
        background: var(--hm-tile-hover-bg);
        color: var(--hm-ink-primary);
        border-color: var(--hm-tile-hover-border);
    }

    .content-wrapper {
        transform: none !important;
    }
    .floating-quick-actions {
        bottom: calc(72px + env(safe-area-inset-bottom, 12px)) !important;
        transition: bottom 0.2s ease;
    }

    form .identity-header-wrap,
    .identity-header-wrap,
    form .domain-header-main,
    .domain-header-main {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        gap: 12px !important;
        width: 100% !important;
        min-width: 0 !important;
    }
    form .identity-header-wrap > div:last-child,
    .identity-header-wrap > div:last-child,
    form .domain-header-main > div:last-child,
    .domain-header-main > div:last-child {
        flex: 1 1 auto !important;
        min-width: 0 !important;
    }
    .domain-icon-badge {
        flex-shrink: 0 !important;
    }

    /* Bottom Action Bar (In-Flow Hallmark Workbench Card) */
    .command-dock {
        background: var(--hm-card-bg);
        border: 1px solid var(--hm-card-border);
        border-radius: 16px;
        padding: 16px 20px;
        margin-top: 1.5rem;
        margin-bottom: 2rem;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }
    .command-dock-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }
    .hallmark-btn-cancel {
        height: 42px;
        padding: 0 20px;
        border-radius: 12px;
        background: transparent;
        border: 1px solid var(--hm-tile-border);
        color: var(--hm-ink-muted);
        font-weight: 600;
        font-size: 0.88rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.16s ease;
        text-decoration: none !important;
    }
    .hallmark-btn-cancel:hover {
        background: var(--hm-tile-hover-bg);
        color: var(--hm-ink-primary);
        border-color: var(--hm-tile-hover-border);
    }
    .hallmark-btn-primary {
        height: 42px;
        padding: 0 24px;
        border-radius: 12px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border: none;
        color: #ffffff;
        font-weight: 600;
        font-size: 0.88rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.35);
        transition: all 0.18s ease;
        cursor: pointer;
    }
    .hallmark-btn-primary:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5);
        color: #ffffff;
    }
    .hallmark-btn-primary:active {
        transform: translateY(0);
    }
    .shortcut-pill {
        font-size: 0.68rem;
        background: rgba(255, 255, 255, 0.2);
        padding: 2px 6px;
        border-radius: 6px;
        font-family: monospace;
    }

    /* Live Preview Role Badge */
    .preview-role-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 10px;
        background: rgba(99, 102, 241, 0.12);
        color: #818cf8;
        border: 1px solid rgba(99, 102, 241, 0.28);
        font-weight: 600;
        font-size: 0.85rem;
    }

    /* Mobile Responsive Optimizations */
    @media (max-width: 767.98px) {
        .hallmark-console-container {
            padding-bottom: 24px;
        }
        .hallmark-header {
            gap: 12px;
            margin-bottom: 1.25rem;
        }
        .perm-grid-workbench {
            grid-template-columns: 1fr;
            padding: 10px;
            gap: 8px;
        }
        .perm-tile-workbench {
            min-height: 48px;
            padding: 10px 12px;
        }
        .perm-title {
            font-size: 0.84rem;
            overflow-wrap: anywhere;
        }
        .perm-code {
            font-size: 0.68rem;
            overflow-wrap: anywhere;
        }
        .presets-bar {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            scrollbar-width: none !important;
            gap: 8px !important;
            padding: 2px 2px 6px 2px !important;
            width: 100% !important;
        }
        .presets-bar::-webkit-scrollbar {
            display: none !important;
        }
        .preset-chip {
            flex: 0 0 auto !important;
            white-space: nowrap !important;
            padding: 7px 12px !important;
            font-size: 0.76rem !important;
            border-radius: 10px !important;
        }
        .workbench-card-header {
            padding: 12px 14px !important;
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 10px !important;
        }
        form .domain-header-main,
        .domain-header-main {
            width: 100% !important;
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            gap: 12px !important;
        }
        form .domain-header-meta,
        .domain-header-meta {
            width: 100% !important;
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            justify-content: space-between !important;
        }
        .command-dock {
            padding: 14px 16px !important;
            margin-top: 1.25rem !important;
            margin-bottom: 1.5rem !important;
        }
        .command-dock-inner {
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 8px !important;
            width: 100% !important;
        }
        .dock-counter-wrap {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            min-width: 0 !important;
            flex: 1 !important;
        }
        #dock-counter-text {
            font-size: 0.74rem !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        .dock-actions-wrap {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            flex-shrink: 0 !important;
        }
        .hallmark-btn-cancel {
            height: 38px !important;
            padding: 0 12px !important;
            font-size: 0.8rem !important;
            border-radius: 10px !important;
        }
        .hallmark-btn-primary {
            height: 38px !important;
            padding: 0 14px !important;
            font-size: 0.8rem !important;
            border-radius: 10px !important;
        }
    }
    @media (max-width: 480px) {
        #dock-tier-pill {
            display: none !important;
        }
        #dock-counter-text {
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            color: var(--hm-ink-primary) !important;
        }
    }
</style>

<div class="hallmark-console-container">
    <form action="{{ route('roles.store') }}" method="POST" id="form-create-role">
        @csrf

        <!-- Console Header Toolbar -->
        <div class="hallmark-header">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="hallmark-tag-pill">
                        <i class="fas fa-shield-alt"></i> {{ __('messages.access_governance_console') }}
                    </span>
                </div>
                <h5 class="mb-0 font-weight-bold" style="color: var(--hm-ink-primary); font-size: 1.15rem; line-height: 1.2;">
                    {{ __('messages.create_role_title') }}
                </h5>
                <small style="color: var(--hm-ink-muted); font-size: 0.78rem;">
                    {{ __('messages.create_role_subtitle') }}
                </small>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger border-0 mb-3" style="border-radius: 12px; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5;">
                <div class="d-flex align-items-center gap-2 font-weight-bold mb-1">
                    <i class="fas fa-exclamation-circle"></i> {{ App::getLocale() == 'en' ? 'Validation errors occurred:' : 'Terjadi kesalahan validasi:' }}
                </div>
                <ul class="mb-0 pl-4" style="font-size: 0.85rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Row 1: Role Identity & Live Clearance Telemetry -->
        <div class="row mb-3">
            <!-- Left: Role Identity -->
            <div class="col-lg-6 mb-3 mb-lg-0">
                <div class="workbench-card h-100">
                    <div class="workbench-card-header">
                        <div class="identity-header-wrap">
                            <div class="domain-icon-badge domain-nav-accent">
                                <i class="fas fa-id-badge"></i>
                            </div>
                            <div style="min-width: 0; flex: 1;">
                                <h6 class="mb-0 font-weight-bold" style="color: var(--hm-ink-primary); font-size: 0.92rem; line-height: 1.3;">
                                    {{ __('messages.role_meta_identity') }}
                                </h6>
                                <small style="color: var(--hm-ink-muted); font-size: 0.72rem; line-height: 1.3; display: block;">
                                    {{ __('messages.role_name_hint') }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="form-group mb-3">
                            <label for="role_name" class="font-weight-bold mb-2" style="color: var(--hm-ink-primary); font-size: 0.85rem;">
                                {{ __('messages.role_name_label') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control hallmark-input" id="role_name" name="name" value="{{ old('name') }}" required autofocus placeholder="{{ __('messages.role_name_placeholder') }}" autocomplete="off">
                        </div>

                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 border-top" style="border-color: var(--hm-tile-border) !important;">
                            <small style="color: var(--hm-ink-muted); font-size: 0.76rem;">
                                {{ __('messages.role_name_live_preview') }}:
                            </small>
                            <div class="preview-role-pill" id="live-role-badge">
                                <i class="fas fa-user-tag"></i>
                                <span id="badge-text">{{ old('name') ?: (App::getLocale() == 'en' ? 'Role Name' : 'Nama Peran') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Live Clearance Telemetry -->
            <div class="col-lg-6">
                <div class="workbench-card h-100">
                    <div class="workbench-card-header">
                        <div class="identity-header-wrap">
                            <div class="domain-icon-badge domain-action-accent">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                            <div style="min-width: 0; flex: 1;">
                                <h6 class="mb-0 font-weight-bold" style="color: var(--hm-ink-primary); font-size: 0.92rem; line-height: 1.3;">
                                    {{ __('messages.clearance_telemetry') }}
                                </h6>
                                <small style="color: var(--hm-ink-muted); font-size: 0.72rem; line-height: 1.3; display: block;">
                                    {{ __('messages.clearance_level') }} &amp; {{ __('messages.permissions_granted') }}
                                </small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-shrink-0" style="flex-wrap: nowrap !important;">
                            <span class="telemetry-pulse-dot" title="Live Telemetry Engine"></span>
                            <span class="tier-badge tier-restricted" id="telemetry-tier-badge">
                                {{ __('messages.tier_restricted') }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="telemetry-gauge">
                            <div class="d-flex align-items-baseline justify-content-between">
                                <div>
                                    <span style="font-size: 1.8rem; font-weight: 800; color: var(--hm-ink-primary); line-height: 1;" id="telemetry-percent-text">0%</span>
                                    <span style="font-size: 0.8rem; color: var(--hm-ink-muted); margin-left: 6px;" id="telemetry-ratio-text">0 / {{ $permissions->count() }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="tier-badge tier-restricted" id="telemetry-tier-pill" style="font-size: 0.7rem;">
                                        RESTRICTED TIER
                                    </span>
                                </div>
                            </div>
                            <div class="meter-progress-track">
                                <div class="meter-progress-bar" id="telemetry-meter-bar" style="width: 0%;"></div>
                            </div>
                        </div>

                        <div class="mt-3 d-flex align-items-center justify-content-between flex-wrap gap-2" style="font-size: 0.74rem; color: var(--hm-ink-muted);">
                            <span><i class="fas fa-lock mr-1"></i> Default: Zero Trust Policy</span>
                            <span id="active-summary-telemetry">0 {{ App::getLocale() == 'en' ? 'permissions selected' : 'izin terpilih' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Clearance Presets -->
        <div class="workbench-card mb-3">
            <div class="p-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2.5">
                    <div>
                        <span class="font-weight-bold" style="color: var(--hm-ink-primary); font-size: 0.85rem;">
                            <i class="fas fa-bolt text-warning mr-1.5"></i> {{ __('messages.presets_title') }}
                        </span>
                        <small style="color: var(--hm-ink-muted); font-size: 0.75rem; margin-left: 6px;">
                            {{ __('messages.presets_subtitle') }}
                        </small>
                    </div>
                </div>
                <div class="presets-bar">
                    <button type="button" class="preset-chip" id="preset-helpdesk" title="{{ __('messages.preset_helpdesk_desc') }}">
                        <i class="fas fa-headset text-info"></i> {{ __('messages.preset_helpdesk') }}
                    </button>
                    <button type="button" class="preset-chip" id="preset-network" title="{{ __('messages.preset_network_desc') }}">
                        <i class="fas fa-network-wired text-success"></i> {{ __('messages.preset_network') }}
                    </button>
                    <button type="button" class="preset-chip" id="preset-auditor" title="{{ __('messages.preset_auditor_desc') }}">
                        <i class="fas fa-clipboard-check text-primary"></i> {{ __('messages.preset_auditor') }}
                    </button>
                    <button type="button" class="preset-chip" id="preset-all" title="{{ App::getLocale() == 'en' ? 'Grant 100% full system clearance' : 'Berikan 100% akses penuh sistem' }}">
                        <i class="fas fa-check-double text-warning"></i> {{ __('messages.preset_all') }}
                    </button>
                    <button type="button" class="preset-chip" id="preset-none" title="{{ App::getLocale() == 'en' ? 'Deselect all permissions' : 'Kosongkan semua izin terpilih' }}">
                        <i class="fas fa-undo text-secondary"></i> {{ __('messages.preset_none') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Real-time Search Filter Bar -->
        <div class="d-flex align-items-center justify-content-between flex-wrap mb-3 gap-2">
            <div class="search-filter-box flex-grow-1" style="min-width: 220px;">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="search-permissions" class="form-control search-filter-input" placeholder="{{ __('messages.search_permissions_placeholder') }}" autocomplete="off">
                <span class="search-shortcut-tag d-none d-sm-inline">/</span>
            </div>
            <div class="flex-shrink-0 text-left">
                <span style="color: var(--hm-ink-muted); font-size: 0.8rem;">
                    {{ __('messages.showing_counter_text') }} <strong id="visible-perm-count" style="color: #60a5fa; font-weight: 700;">{{ $permissions->count() }}</strong> {{ __('messages.of_counter_text') }} <strong>{{ $permissions->count() }}</strong> {{ __('messages.permissions_counter_text') }}
                </span>
            </div>
        </div>

        @php
            $permissionLabels = [
                // Legacy / Space/Underscore Permission Names
                'view dashboard' => __('messages.perm_label_dashboard'),
                'view_dashboard' => __('messages.perm_label_dashboard'),
                'manage master data' => __('messages.perm_label_action_manage_master_data'),
                'manage_master_data' => __('messages.perm_label_action_manage_master_data'),
                'manage employees' => __('messages.perm_label_action_manage_employees'),
                'manage_employees' => __('messages.perm_label_action_manage_employees'),
                'manage assets' => __('messages.perm_label_action_manage_assets'),
                'manage_assets' => __('messages.perm_label_action_manage_assets'),
                'manage assignments' => __('messages.perm_label_action_manage_assignments'),
                'manage_assignments' => __('messages.perm_label_action_manage_assignments'),
                'manage maintenance' => __('messages.perm_label_action_manage_maintenance'),
                'manage_maintenance' => __('messages.perm_label_action_manage_maintenance'),
                'manage tickets' => __('messages.perm_label_action_manage_tickets'),
                'manage_tickets' => __('messages.perm_label_action_manage_tickets'),
                'manage network' => __('messages.perm_label_action_manage_network'),
                'manage_network' => __('messages.perm_label_action_manage_network'),
                'manage settings' => __('messages.perm_label_action_manage_settings'),
                'manage_settings' => __('messages.perm_label_action_manage_settings'),

                // Menu Permissions (Read/Access)
                'menu_dashboard' => __('messages.perm_label_dashboard'),
                'menu_assets' => __('messages.perm_label_assets'),
                'menu_assignments' => __('messages.perm_label_assignments'),
                'menu_tickets' => __('messages.perm_label_tickets'),
                'menu_maintenances' => __('messages.perm_label_maintenances'),
                'menu_predictive_health' => __('messages.perm_label_predictive_health'),
                'menu_departments' => __('messages.perm_label_departments'),
                'menu_brands' => __('messages.perm_label_brands'),
                'menu_locations' => __('messages.perm_label_locations'),
                'menu_categories' => __('messages.perm_label_categories'),
                'menu_vendors' => __('messages.perm_label_vendors'),
                'menu_employees' => __('messages.perm_label_employees'),
                'menu_users' => __('messages.perm_label_users'),
                'menu_vlans' => __('messages.perm_label_vlans'),
                'menu_ips' => __('messages.perm_label_ips'),
                'menu_network_anomalies' => __('messages.perm_label_network_anomalies'),
                'menu_software_licenses' => __('messages.perm_label_software_licenses'),
                'menu_password_vaults' => __('messages.perm_label_password_vaults'),
                'menu_budget_planner' => __('messages.perm_label_budget_planner'),
                'menu_settings' => __('messages.perm_label_settings'),
                'menu_roles' => __('messages.perm_label_roles'),
                'menu_pics' => __('messages.perm_label_pics'),

                // Action Permissions (Create, Edit, Update, Delete)
                'action_manage_assets' => __('messages.perm_label_action_manage_assets'),
                'action_manage_assignments' => __('messages.perm_label_action_manage_assignments'),
                'action_manage_tickets' => __('messages.perm_label_action_manage_tickets'),
                'action_manage_maintenance' => __('messages.perm_label_action_manage_maintenance'),
                'action_manage_employees' => __('messages.perm_label_action_manage_employees'),
                'action_manage_network' => __('messages.perm_label_action_manage_network'),
                'action_manage_master_data' => __('messages.perm_label_action_manage_master_data'),
                'action_manage_settings' => __('messages.perm_label_action_manage_settings'),
                'action_ocr_scan' => __('messages.perm_label_action_ocr_scan'),
                'action_ai_assistant' => __('messages.perm_label_action_ai_assistant'),
                'action_export_excel' => __('messages.perm_label_action_export_excel'),
                'action_import_excel' => __('messages.perm_label_action_import_excel'),
            ];

            $menuPermissions = $permissions->filter(function($p) { 
                return str_starts_with($p->name, 'menu_') || str_starts_with($p->name, 'view'); 
            })->sortBy('name');

            $actionManagePermissions = $permissions->filter(function($p) { 
                return str_starts_with($p->name, 'action_manage_') || str_starts_with($p->name, 'manage'); 
            })->sortBy('name');

            $actionOtherPermissions = $permissions->filter(function($p) use ($menuPermissions, $actionManagePermissions) { 
                return !$menuPermissions->contains('id', $p->id) && !$actionManagePermissions->contains('id', $p->id); 
            })->sortBy('name');
        @endphp

        <!-- Domain Bento Card 1: Navigation & System Modules -->
        <div class="workbench-card mb-3 perm-group-card" id="domain-card-nav">
            <div class="workbench-card-header">
                <div class="domain-header-main" style="display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; align-items: center !important; gap: 12px !important; width: 100% !important; min-width: 0 !important;">
                    <div class="domain-icon-badge domain-nav-accent" style="flex-shrink: 0 !important;">
                        <i class="fas fa-compass"></i>
                    </div>
                    <div style="flex: 1 1 auto !important; min-width: 0 !important;">
                        <h6 class="mb-0 font-weight-bold" style="color: var(--hm-ink-primary); font-size: 0.92rem; line-height: 1.25;">
                            {{ __('messages.perm_group_nav') }}
                        </h6>
                        <small class="d-block" style="color: var(--hm-ink-muted); font-size: 0.72rem; line-height: 1.3;">
                            {{ __('messages.perm_group_nav_desc') }}
                        </small>
                    </div>
                </div>
                <div class="domain-header-meta flex-shrink-0" style="display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; align-items: center !important; gap: 8px !important;">
                    <span class="domain-count-badge" id="domain-nav-active-count">0 / {{ $menuPermissions->count() }}</span>
                    <button type="button" class="btn-domain-toggle" data-target=".menu-perm-switch">
                        {{ __('messages.toggle_domain') }}
                    </button>
                </div>
            </div>
            <div class="perm-grid-workbench">
                @foreach($menuPermissions as $permission)
                    @php
                        $cleanName = strtolower(trim($permission->name));
                        $label = $permissionLabels[$cleanName] ?? ucwords(str_replace(['_', '-'], ' ', $permission->name));
                    @endphp
                    <div class="perm-tile-workbench" data-label="{{ mb_strtolower($label . ' ' . $permission->name) }}" data-perm-name="{{ $permission->name }}" tabindex="0" role="checkbox" aria-checked="false">
                        <div class="pr-2" style="min-width: 0;">
                            <div class="perm-title text-truncate">{{ $label }}</div>
                            <div class="perm-code text-truncate">{{ $permission->name }}</div>
                        </div>
                        <div class="custom-control custom-switch flex-shrink-0">
                            <input type="checkbox" class="custom-control-input perm-switch menu-perm-switch" id="perm_{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}">
                            <label class="custom-control-label" for="perm_{{ $permission->id }}"></label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Domain Bento Card 2: Execution & Operational Authority -->
        <div class="workbench-card mb-3 perm-group-card" id="domain-card-action">
            <div class="workbench-card-header">
                <div class="domain-header-main" style="display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; align-items: center !important; gap: 12px !important; width: 100% !important; min-width: 0 !important;">
                    <div class="domain-icon-badge domain-action-accent" style="flex-shrink: 0 !important;">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div style="flex: 1 1 auto !important; min-width: 0 !important;">
                        <h6 class="mb-0 font-weight-bold" style="color: var(--hm-ink-primary); font-size: 0.92rem; line-height: 1.25;">
                            {{ __('messages.perm_group_action') }}
                        </h6>
                        <small class="d-block" style="color: var(--hm-ink-muted); font-size: 0.72rem; line-height: 1.3;">
                            {{ __('messages.perm_group_action_desc') }}
                        </small>
                    </div>
                </div>
                <div class="domain-header-meta flex-shrink-0" style="display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; align-items: center !important; gap: 8px !important;">
                    <span class="domain-count-badge" id="domain-action-active-count">0 / {{ $actionManagePermissions->count() }}</span>
                    <button type="button" class="btn-domain-toggle" data-target=".manage-perm-switch">
                        {{ __('messages.toggle_domain') }}
                    </button>
                </div>
            </div>
            <div class="perm-grid-workbench">
                @foreach($actionManagePermissions as $permission)
                    @php
                        $cleanName = strtolower(trim($permission->name));
                        $label = $permissionLabels[$cleanName] ?? ucwords(str_replace(['_', '-'], ' ', $permission->name));
                    @endphp
                    <div class="perm-tile-workbench" data-label="{{ mb_strtolower($label . ' ' . $permission->name) }}" data-perm-name="{{ $permission->name }}" tabindex="0" role="checkbox" aria-checked="false">
                        <div class="pr-2" style="min-width: 0;">
                            <div class="perm-title text-truncate">{{ $label }}</div>
                            <div class="perm-code text-truncate">{{ $permission->name }}</div>
                        </div>
                        <div class="custom-control custom-switch flex-shrink-0">
                            <input type="checkbox" class="custom-control-input perm-switch manage-perm-switch" id="perm_{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}">
                            <label class="custom-control-label" for="perm_{{ $permission->id }}"></label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Domain Bento Card 3: Elevated Admin & AI Gateway -->
        @if($actionOtherPermissions->count() > 0)
        <div class="workbench-card mb-3 perm-group-card" id="domain-card-tools">
            <div class="workbench-card-header">
                <div class="domain-header-main" style="display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; align-items: center !important; gap: 12px !important; width: 100% !important; min-width: 0 !important;">
                    <div class="domain-icon-badge domain-tools-accent" style="flex-shrink: 0 !important;">
                        <i class="fas fa-brain"></i>
                    </div>
                    <div style="flex: 1 1 auto !important; min-width: 0 !important;">
                        <h6 class="mb-0 font-weight-bold" style="color: var(--hm-ink-primary); font-size: 0.92rem; line-height: 1.25;">
                            {{ __('messages.perm_group_tools') }}
                        </h6>
                        <small class="d-block" style="color: var(--hm-ink-muted); font-size: 0.72rem; line-height: 1.3;">
                            {{ __('messages.perm_group_tools_desc') }}
                        </small>
                    </div>
                </div>
                <div class="domain-header-meta flex-shrink-0" style="display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; align-items: center !important; gap: 8px !important;">
                    <span class="domain-count-badge" id="domain-tools-active-count">0 / {{ $actionOtherPermissions->count() }}</span>
                    <button type="button" class="btn-domain-toggle" data-target=".tools-perm-switch">
                        {{ __('messages.toggle_domain') }}
                    </button>
                </div>
            </div>
            <div class="perm-grid-workbench">
                @foreach($actionOtherPermissions as $permission)
                    @php
                        $cleanName = strtolower(trim($permission->name));
                        $label = $permissionLabels[$cleanName] ?? ucwords(str_replace(['_', '-'], ' ', $permission->name));
                    @endphp
                    <div class="perm-tile-workbench" data-label="{{ mb_strtolower($label . ' ' . $permission->name) }}" data-perm-name="{{ $permission->name }}" tabindex="0" role="checkbox" aria-checked="false">
                        <div class="pr-2" style="min-width: 0;">
                            <div class="perm-title text-truncate">{{ $label }}</div>
                            <div class="perm-code text-truncate">{{ $permission->name }}</div>
                        </div>
                        <div class="custom-control custom-switch flex-shrink-0">
                            <input type="checkbox" class="custom-control-input perm-switch tools-perm-switch" id="perm_{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}">
                            <label class="custom-control-label" for="perm_{{ $permission->id }}"></label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Sticky Bottom Command Dock -->
        <div class="command-dock">
            <div class="command-dock-inner">
                <div class="dock-counter-wrap d-flex align-items-center gap-2">
                    <span class="tier-badge tier-restricted" id="dock-tier-pill">RESTRICTED</span>
                    <span style="color: var(--hm-ink-muted); font-size: 0.84rem;" id="dock-counter-text">
                        0 / {{ $permissions->count() }} {{ __('messages.permissions_granted') }}
                    </span>
                </div>
                <div class="dock-actions-wrap d-flex align-items-center gap-2">
                    <a href="{{ route('roles.index') }}" class="hallmark-btn-cancel">
                        {{ __('messages.cancel') }}
                    </a>
                    <button type="submit" class="hallmark-btn-primary" id="btn-submit-role">
                        <i class="fas fa-check-circle mr-1.5"></i> {{ __('messages.create_role_submit') }}
                        <span class="shortcut-pill d-none d-md-inline ml-2">Ctrl+S</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    const totalPermissions = {{ $permissions->count() }};
    const totalNav = {{ $menuPermissions->count() }};
    const totalAction = {{ $actionManagePermissions->count() }};
    const totalTools = {{ $actionOtherPermissions->count() }};

    const tierLabels = {
        restricted: "{{ __('messages.tier_restricted') }}",
        standard: "{{ __('messages.tier_standard') }}",
        elevated: "{{ __('messages.tier_elevated') }}",
        full: "{{ __('messages.tier_full_access') }}"
    };

    const isEn = "{{ App::getLocale() }}" === 'en';
    const fallbackBadgeName = isEn ? 'Role Name' : 'Nama Peran';
    const selectedPermText = isEn ? ' permissions selected' : ' izin terpilih';

    // Live Role Name Badge Sync
    $('#role_name').on('input keyup change', function() {
        const val = $(this).val().trim();
        $('#badge-text').text(val || fallbackBadgeName);
    });

    // Update Telemetry Engine
    function updateTelemetry() {
        const checkedCount = $('.perm-switch:checked').length;
        const percent = totalPermissions > 0 ? Math.round((checkedCount / totalPermissions) * 100) : 0;

        $('#telemetry-percent-text').text(percent + '%');
        $('#telemetry-ratio-text').text(checkedCount + ' / ' + totalPermissions);
        $('#telemetry-meter-bar').css('width', percent + '%');
        $('#dock-counter-text').text(checkedCount + ' / ' + totalPermissions + " {{ __('messages.permissions_granted') }}");
        $('#active-summary-telemetry').text(checkedCount + selectedPermText);

        // Domain Counters
        const navChecked = $('.menu-perm-switch:checked').length;
        const actionChecked = $('.manage-perm-switch:checked').length;
        const toolsChecked = $('.tools-perm-switch:checked').length;

        $('#domain-nav-active-count').text(navChecked + ' / ' + totalNav);
        $('#domain-action-active-count').text(actionChecked + ' / ' + totalAction);
        $('#domain-tools-active-count').text(toolsChecked + ' / ' + totalTools);

        // Determine Clearance Tier
        let tierKey = 'restricted';
        let tierClass = 'tier-restricted';
        let tierName = tierLabels.restricted;

        if (percent === 100) {
            tierKey = 'full';
            tierClass = 'tier-full-access';
            tierName = tierLabels.full;
        } else if (percent > 60) {
            tierKey = 'elevated';
            tierClass = 'tier-elevated';
            tierName = tierLabels.elevated;
        } else if (percent > 25) {
            tierKey = 'standard';
            tierClass = 'tier-standard';
            tierName = tierLabels.standard;
        }

        // Apply Tier Classes
        const tierBadges = $('#telemetry-tier-badge, #telemetry-tier-pill, #dock-tier-pill');
        tierBadges.removeClass('tier-restricted tier-standard tier-elevated tier-full-access').addClass(tierClass);
        $('#telemetry-tier-badge').text(tierName);
        $('#telemetry-tier-pill, #dock-tier-pill').text(tierName.toUpperCase());
    }

    // Tile Click Toggle
    $(document).on('click', '.perm-tile-workbench', function(e) {
        if ($(e.target).is('input[type="checkbox"]') || $(e.target).is('label')) {
            return;
        }
        const checkbox = $(this).find('.perm-switch');
        const newState = !checkbox.is(':checked');
        checkbox.prop('checked', newState).trigger('change');
    });

    // Keyboard accessibility for tiles (Enter or Space)
    $(document).on('keydown', '.perm-tile-workbench', function(e) {
        if (e.key === ' ' || e.key === 'Enter') {
            e.preventDefault();
            const checkbox = $(this).find('.perm-switch');
            const newState = !checkbox.is(':checked');
            checkbox.prop('checked', newState).trigger('change');
        }
    });

    // Sync Checkbox Changes
    $(document).on('change', '.perm-switch', function() {
        const tile = $(this).closest('.perm-tile-workbench');
        const isChecked = $(this).is(':checked');
        if (isChecked) {
            tile.addClass('is-checked').attr('aria-checked', 'true');
        } else {
            tile.removeClass('is-checked').attr('aria-checked', 'false');
        }
        updateTelemetry();
    });

    // Quick Clearance Presets
    const helpdeskPerms = [
        'menu_dashboard', 'view dashboard',
        'menu_assets', 'action_manage_assets', 'manage assets',
        'menu_assignments', 'action_manage_assignments', 'manage assignments',
        'menu_tickets', 'action_manage_tickets', 'manage tickets',
        'menu_maintenances', 'action_manage_maintenance', 'manage maintenance'
    ];

    const networkPerms = [
        'menu_dashboard', 'view dashboard',
        'menu_vlans', 'menu_ips', 'menu_network_anomalies',
        'action_manage_network', 'manage network'
    ];

    $('#preset-helpdesk').on('click', function() {
        $('.perm-switch').each(function() {
            const val = $(this).val();
            const shouldCheck = helpdeskPerms.includes(val);
            $(this).prop('checked', shouldCheck).trigger('change');
        });
    });

    $('#preset-network').on('click', function() {
        $('.perm-switch').each(function() {
            const val = $(this).val();
            const shouldCheck = networkPerms.includes(val);
            $(this).prop('checked', shouldCheck).trigger('change');
        });
    });

    $('#preset-auditor').on('click', function() {
        $('.perm-switch').each(function() {
            const val = $(this).val();
            const isMenuOrView = val.startsWith('menu_') || val.startsWith('view');
            $(this).prop('checked', isMenuOrView).trigger('change');
        });
    });

    $('#preset-all').on('click', function() {
        $('.perm-switch').prop('checked', true).trigger('change');
    });

    $('#preset-none').on('click', function() {
        $('.perm-switch').prop('checked', false).trigger('change');
    });

    // Domain Toggle Buttons
    $('.btn-domain-toggle').on('click', function() {
        const target = $(this).data('target');
        const switches = $(target);
        const allChecked = switches.filter(':checked').length === switches.length;
        switches.prop('checked', !allChecked).trigger('change');
    });

    // Search Filter with '/' Shortcut
    $(document).on('keydown', function(e) {
        if (e.key === '/' && !$(e.target).is('input, textarea, select')) {
            e.preventDefault();
            $('#search-permissions').focus();
        }
        // Ctrl+S or Cmd+S to submit
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
            e.preventDefault();
            $('#form-create-role').submit();
        }
    });

    $('#search-permissions').on('keyup input change', function() {
        const query = $(this).val().toLowerCase().trim();
        let visibleCount = 0;

        $('.perm-tile-workbench').each(function() {
            const label = ($(this).attr('data-label') || '').toLowerCase();
            if (!query || label.indexOf(query) !== -1) {
                $(this).removeClass('d-none');
                visibleCount++;
            } else {
                $(this).addClass('d-none');
            }
        });

        $('#visible-perm-count').text(visibleCount);

        // Hide Domain Bento cards that have no matching visible tiles
        $('.perm-group-card').each(function() {
            const hasVisible = $(this).find('.perm-tile-workbench:not(.d-none)').length > 0;
            if (hasVisible) {
                $(this).removeClass('d-none');
            } else {
                $(this).addClass('d-none');
            }
        });
    });

    // Initial Telemetry Run
    updateTelemetry();
});
</script>
@endpush
@endsection
