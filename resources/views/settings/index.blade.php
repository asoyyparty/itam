@extends('layouts.admin')

@section('title', __('messages.system_settings'))

@section('content')
<style>
    .content-header {
        display: none !important;
    }

    /* Header Icon Box */
    .settings-header-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: rgba(99, 102, 241, 0.12);
        border: 1px solid rgba(99, 102, 241, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #818cf8;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    /* Premium Glassmorphic Settings Card & UI Styling */
    .settings-card {
        border-radius: 16px !important;
        overflow: hidden !important;
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(12px);
        border: var(--rule-soft);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
    }

    .settings-nav {
        padding: 8px;
    }

    .settings-nav .nav-link {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        border-radius: 12px;
        color: #94a3b8;
        border: 1px solid transparent;
        margin-bottom: 6px;
        transition: all 0.25s ease-in-out;
        position: relative;
    }

    .settings-nav .nav-link:hover {
        background: rgba(255, 255, 255, 0.05);
        color: #f8fafc;
        transform: translateX(3px);
    }

    .settings-nav .nav-link.active {
        background: var(--active-bg, rgba(59, 130, 246, 0.12)) !important;
        border: 1px solid var(--active-border, rgba(59, 130, 246, 0.3)) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 15px var(--active-shadow, rgba(59, 130, 246, 0.15));
    }

    .settings-nav .nav-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 15%;
        height: 70%;
        width: 4px;
        border-radius: 0 4px 4px 0;
        background: var(--active-color, #3b82f6);
        box-shadow: 0 0 10px var(--active-color, #3b82f6);
    }

    .nav-icon-box {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        margin-right: 12px;
        background: var(--icon-bg, rgba(148, 163, 184, 0.1));
        color: var(--icon-color, #94a3b8);
        border: 1px solid var(--icon-border, rgba(148, 163, 184, 0.2));
        transition: all 0.25s ease;
        flex-shrink: 0;
    }

    .settings-nav .nav-link.active .nav-icon-box {
        background: var(--icon-active-bg, rgba(59, 130, 246, 0.2));
        color: var(--icon-active-color, #60a5fa);
        border-color: var(--icon-active-border, rgba(59, 130, 246, 0.4));
        transform: scale(1.05);
    }

    .nav-text-box {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        overflow: hidden;
    }

    .nav-title {
        font-weight: 600;
        font-size: 0.9rem;
        line-height: 1.2;
    }

    .nav-subtitle {
        font-size: 0.72rem;
        color: #64748b;
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .settings-nav .nav-link.active .nav-subtitle {
        color: #cbd5e1;
    }

    /* Badge Superadmin Custom */
    .badge-superadmin {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #ffffff;
        font-size: 0.65rem;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 600;
        letter-spacing: 0.3px;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
    }

    .group-header-card {
        background: rgba(30, 41, 59, 0.5);
        border-radius: 14px;
        padding: 16px 20px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        margin-bottom: 24px;
    }

    .setting-row-item {
        padding: 16px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .setting-row-item:last-child {
        border-bottom: none;
    }

    /* Custom Input Controls & Sleek Select Dropdowns */
    .theme-input {
        height: 42px !important;
        border-radius: 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.35) !important;
        box-shadow: none !important;
        font-size: 0.88rem !important;
        color: var(--color-ink-0) !important;
        transition: all 0.2s ease !important;
    }
    .theme-input:focus {
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2) !important;
        background: rgba(15, 23, 42, 0.5) !important;
    }

    /* Custom Modern Select Dropdown Styling */
    select.theme-input {
        border-radius: 10px !important;
        padding: 0 38px 0 14px !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 14px center !important;
        background-size: 14px 14px !important;
        cursor: pointer !important;
        width: 100% !important;
        max-width: 320px !important;
    }
    select.theme-input.select-sm-custom {
        max-width: 220px !important;
    }
    select.theme-input option {
        background-color: #0f172a !important;
        color: #f8fafc !important;
        padding: 10px !important;
    }

    textarea.theme-input {
        height: auto !important;
    }

    /* Seamless Password Toggle & Input Group Joining Fix */
    .input-group {
        display: flex !important;
        flex-wrap: nowrap !important;
        align-items: stretch !important;
        width: 100% !important;
        position: relative !important;
    }

    .input-group .theme-input {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
        flex: 1 1 auto !important;
        width: 1% !important;
        margin-right: 0 !important;
    }

    .input-group .input-group-append {
        display: flex !important;
        margin-left: 0 !important;
    }

    .input-group .input-group-append .toggle-key-btn {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
        border-top-right-radius: 10px !important;
        border-bottom-right-radius: 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        border-left: none !important;
        background: rgba(15, 23, 42, 0.35) !important;
        color: #94a3b8 !important;
        transition: all 0.2s ease !important;
        height: 42px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 14px !important;
        box-shadow: none !important;
        outline: none !important;
    }

    .input-group:focus-within .theme-input {
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2) !important;
        z-index: 2 !important;
    }

    .input-group:focus-within .input-group-append .toggle-key-btn {
        border-color: #60a5fa !important;
        color: #60a5fa !important;
    }

    .toggle-key-btn:hover {
        background: rgba(59, 130, 246, 0.15) !important;
        color: #60a5fa !important;
    }

    /* Action Buttons Matching Roles Theme */
    .custom-btn-save {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
        border: none !important;
        color: #ffffff !important;
        border-radius: 14px !important;
        height: 42px;
        font-weight: 600;
        padding: 0 28px;
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.35) !important;
        transition: all 0.2s ease !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .custom-btn-save:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 6px 18px rgba(59, 130, 246, 0.5) !important;
        transform: translateY(-1px);
    }

    .custom-btn-outline {
        border-radius: 14px !important;
        height: 42px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease !important;
    }

    /* Responsive & Mobile Mode Styling */
    @media (max-width: 991.98px) {
        .settings-nav-col {
            margin-bottom: 1rem !important;
        }
        .settings-nav-card {
            border-radius: 14px !important;
        }
        .settings-nav-card .card-header-sub {
            display: none !important;
        }
        .settings-nav {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            padding: 8px !important;
            gap: 6px !important;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .settings-nav::-webkit-scrollbar {
            display: none;
        }
        .settings-nav .nav-link {
            flex: 0 0 auto !important;
            margin-bottom: 0 !important;
            padding: 9px 14px !important;
            border-radius: 10px !important;
            white-space: nowrap !important;
        }
        .settings-nav .nav-link:hover {
            transform: none !important;
        }
        .settings-nav .nav-link.active::before {
            display: none !important;
        }
        .nav-subtitle {
            display: none !important;
        }
        .nav-icon-box {
            width: 32px !important;
            height: 32px !important;
            font-size: 0.95rem !important;
            margin-right: 8px !important;
        }
        .nav-title {
            font-size: 0.84rem !important;
        }
        select.theme-input {
            max-width: 100% !important;
        }
    }

    @media (max-width: 767.98px) {
        .settings-toolbar-box {
            width: 100%;
            margin-top: 0.5rem !important;
        }
        .group-header-card {
            flex-direction: column !important;
            align-items: flex-start !important;
            padding: 14px !important;
            gap: 10px !important;
        }
        .group-header-card h4 {
            font-size: 1rem !important;
        }
        .setting-row-item {
            padding: 12px 0 !important;
        }
        .setting-row-item .col-sm-4 {
            margin-bottom: 6px !important;
            padding-bottom: 0 !important;
        }
        .card-footer-actions {
            width: 100%;
            display: flex;
            justify-content: flex-end;
            padding: 1rem !important;
        }
        .card-footer-actions .btn {
            width: 100% !important;
            justify-content: center !important;
            height: 44px !important;
        }
    }

    /* Light Mode Overrides */
    body.light-mode .settings-header-icon {
        background: #eef2ff !important;
        border-color: #e0e7ff !important;
        color: #4f46e5 !important;
    }
    body.light-mode .settings-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08) !important;
    }
    body.light-mode .settings-nav .nav-link {
        color: #475569 !important;
    }
    body.light-mode .settings-nav .nav-link:hover {
        background: #f1f5f9 !important;
        color: #0f172a !important;
    }
    body.light-mode .settings-nav .nav-link.active {
        color: #0f172a !important;
    }
    body.light-mode .nav-subtitle {
        color: #64748b !important;
    }
    body.light-mode .settings-nav .nav-link.active .nav-subtitle {
        color: #334155 !important;
    }
    body.light-mode .group-header-card {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
    }
    body.light-mode .group-header-card h4 {
        color: #0f172a !important;
    }
    body.light-mode .setting-row-item {
        border-bottom: 1px solid #e2e8f0 !important;
    }
    body.light-mode .theme-input {
        background: #f8fafc !important;
        border: 1px solid #cbd5e1 !important;
        color: #0f172a !important;
    }
    body.light-mode select.theme-input {
        background-color: #f8fafc !important;
        border: 1px solid #cbd5e1 !important;
        color: #0f172a !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23475569' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
    }
    body.light-mode select.theme-input option {
        background-color: #ffffff !important;
        color: #0f172a !important;
    }
    body.light-mode .theme-input:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
        background: #ffffff !important;
    }
    body.light-mode .input-group .input-group-append .toggle-key-btn {
        background: #f8fafc !important;
        border: 1px solid #cbd5e1 !important;
        border-left: none !important;
        color: #64748b !important;
    }
    body.light-mode .input-group .input-group-append .toggle-key-btn:hover {
        background: #e2e8f0 !important;
        color: #2563eb !important;
    }
    body.light-mode .input-group:focus-within .theme-input {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
    }
    body.light-mode .input-group:focus-within .input-group-append .toggle-key-btn {
        border-color: #3b82f6 !important;
        color: #2563eb !important;
    }
    body.light-mode .card-header-sub {
        background: #f8fafc !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }
</style>

<!-- Header Toolbar Matching Roles UI -->
<div class="row mb-3 settings-toolbar-box" style="margin-top: 0 !important;">
    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center" style="gap: 12px;">
            <div class="settings-header-icon">
                <i class="fas fa-sliders-h"></i>
            </div>
            <div>
                <h5 class="mb-0 font-weight-bold theme-text" style="font-size: 1.08rem; line-height: 1.2;">{{ __('messages.system_settings') }}</h5>
                <small class="text-muted d-block" style="font-size: 0.78rem;">{{ __('messages.system_settings_desc') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Navigation Bar -->
    <div class="col-lg-3 col-md-12 mb-4 settings-nav-col">
        <div class="card settings-card settings-nav-card">
            <div class="card-header bg-transparent card-header-sub border-0 pt-3 pb-2 px-3">
                <h6 class="text-uppercase text-muted font-weight-bold m-0" style="font-size: 0.72rem; letter-spacing: 1px;">
                    <i class="fas fa-sliders-h mr-1 text-info"></i> {{ __('messages.system_settings') ?? 'System Settings' }}
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="nav flex-column nav-pills settings-nav" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    @php 
                        $first = true; 
                        $isSuperAdmin = Auth::user() && (Auth::user()->hasRole('Super Admin') || Auth::user()->id === 1);

                        // Icon, color, and subtitle mapping for each settings group
                        $metaMap = [
                            'general' => [
                                'icon' => 'fas fa-sliders-h',
                                'color' => '#3b82f6',
                                'bg' => 'rgba(59, 130, 246, 0.15)',
                                'border' => 'rgba(59, 130, 246, 0.3)',
                                'subtitle' => __('messages.subtitle_general'),
                            ],
                            'email' => [
                                'icon' => 'fas fa-paper-plane',
                                'color' => '#06b6d4',
                                'bg' => 'rgba(6, 182, 212, 0.15)',
                                'border' => 'rgba(6, 182, 212, 0.3)',
                                'subtitle' => __('messages.subtitle_email'),
                            ],
                            'system' => [
                                'icon' => 'fas fa-server',
                                'color' => '#f59e0b',
                                'bg' => 'rgba(245, 158, 11, 0.15)',
                                'border' => 'rgba(245, 158, 11, 0.3)',
                                'subtitle' => __('messages.subtitle_system'),
                            ],
                            'ai' => [
                                'icon' => 'fas fa-brain',
                                'color' => '#a855f7',
                                'bg' => 'rgba(168, 85, 247, 0.15)',
                                'border' => 'rgba(168, 85, 247, 0.3)',
                                'subtitle' => __('messages.subtitle_ai'),
                            ],
                            'whatsapp' => [
                                'icon' => 'fab fa-whatsapp',
                                'color' => '#10b981',
                                'bg' => 'rgba(16, 185, 129, 0.15)',
                                'border' => 'rgba(16, 185, 129, 0.3)',
                                'subtitle' => __('messages.subtitle_whatsapp'),
                            ],
                            'telegram' => [
                                'icon' => 'fab fa-telegram-plane',
                                'color' => '#3b82f6',
                                'bg' => 'rgba(59, 130, 246, 0.15)',
                                'border' => 'rgba(59, 130, 246, 0.3)',
                                'subtitle' => __('messages.subtitle_telegram'),
                            ],
                        ];
                    @endphp

                    @foreach($settings as $group => $items)
                        @php
                            $meta = $metaMap[$group] ?? [
                                'icon' => 'fas fa-cog',
                                'color' => '#38bdf8',
                                'bg' => 'rgba(56, 189, 248, 0.15)',
                                'border' => 'rgba(56, 189, 248, 0.3)',
                                'subtitle' => __('messages.system_configuration'),
                            ];
                        @endphp
                        <a class="nav-link {{ $first ? 'active' : '' }}" 
                           id="v-pills-{{ $group }}-tab" 
                           data-toggle="pill" 
                           href="#v-pills-{{ $group }}" 
                           role="tab" 
                           aria-controls="v-pills-{{ $group }}" 
                           aria-selected="{{ $first ? 'true' : 'false' }}"
                           style="--active-color: {{ $meta['color'] }}; 
                                  --active-bg: rgba({{ implode(',', sscanf($meta['color'], '#%02x%02x%02x')) }}, 0.12);
                                  --active-border: rgba({{ implode(',', sscanf($meta['color'], '#%02x%02x%02x')) }}, 0.35);
                                  --active-shadow: rgba({{ implode(',', sscanf($meta['color'], '#%02x%02x%02x')) }}, 0.2);
                                  --icon-color: {{ $meta['color'] }};
                                  --icon-bg: {{ $meta['bg'] }};
                                  --icon-border: {{ $meta['border'] }};
                                  --icon-active-color: #ffffff;
                                  --icon-active-bg: {{ $meta['color'] }};
                                  --icon-active-border: {{ $meta['color'] }};">
                            
                            <div class="nav-icon-box">
                                <i class="{{ $meta['icon'] }}"></i>
                            </div>
                            
                            <div class="nav-text-box">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="nav-title theme-text">{{ __('messages.' . $group . '_settings') ?? ucfirst($group) . ' Settings' }}</span>
                                    @if($group == 'ai')
                                        <span class="badge badge-superadmin ml-1"><i class="fas fa-lock mr-1"></i> Admin</span>
                                    @endif
                                </div>
                                <span class="nav-subtitle">{{ $meta['subtitle'] }}</span>
                            </div>
                        </a>
                        @php $first = false; @endphp
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Form Content -->
    <div class="col-lg-9 col-md-12">
        <div class="card settings-card">
            <form action="{{ route('settings.updateAll') }}" method="POST">
                @csrf
                <input type="hidden" name="active_tab" id="active_tab_input" value="{{ request('active_tab', '#v-pills-general') }}">
                <div class="card-body p-3 p-md-4">
                    <div class="tab-content" id="v-pills-tabContent">
                        @php $first = true; @endphp
                        @foreach($settings as $group => $items)
                            @php
                                $meta = $metaMap[$group] ?? [
                                    'icon' => 'fas fa-cog',
                                    'color' => '#38bdf8',
                                    'subtitle' => 'Konfigurasi Sistem',
                                ];
                            @endphp
                            <div class="tab-pane fade {{ $first ? 'show active' : '' }}" id="v-pills-{{ $group }}" role="tabpanel" aria-labelledby="v-pills-{{ $group }}-tab">
                                <!-- Tab Header -->
                                <div class="group-header-card d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="p-3 rounded-circle mr-3 flex-shrink-0" style="background: rgba({{ implode(',', sscanf($meta['color'], '#%02x%02x%02x')) }}, 0.15); border: 1px solid {{ $meta['color'] }};">
                                            <i class="{{ $meta['icon'] }} fa-lg" style="color: {{ $meta['color'] }};"></i>
                                        </div>
                                        <div>
                                            <h4 class="m-0 font-weight-bold theme-text">
                                                {{ __('messages.' . $group . '_configuration') ?? ucfirst($group) . ' Configuration' }}
                                            </h4>
                                            <small class="text-muted">{{ $meta['subtitle'] }}</small>
                                        </div>
                                    </div>
                                    @if($group == 'ai')
                                        <div class="d-inline-flex align-items-center px-3 py-1.5" style="background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.35); color: #fbbf24; border-radius: 10px; font-size: 0.8rem; font-weight: 600; gap: 6px; box-shadow: 0 2px 10px rgba(245, 158, 11, 0.1);">
                                            <i class="fas fa-lock" style="font-size: 0.75rem;"></i> {{ __('messages.superadmin_only_ai') }}
                                        </div>
                                    @endif
                                </div>
                                
                                @if($group == 'ai' && !$isSuperAdmin)
                                    <div class="alert alert-warning border-0 mb-4" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 12px;">
                                        <i class="fas fa-lock mr-2"></i> {{ __('messages.superadmin_only_ai') }}
                                    </div>
                                @endif

                                <!-- Form Fields -->
                                @foreach($items as $setting)
                                    <div class="form-group row align-items-center setting-row-item">
                                        <label class="col-sm-4 col-form-label theme-text font-weight-bold">
                                            {{ __('messages.' . $setting->key) ?? ucwords(str_replace('_', ' ', $setting->key)) }}
                                        </label>
                                        <div class="col-sm-8">
                                            @if($setting->key == 'ai_provider')
                                                <select name="{{ $setting->key }}" class="form-control theme-input" {{ ($group == 'ai' && !$isSuperAdmin) ? 'disabled' : '' }}>
                                                    <option value="gemini" {{ $setting->value == 'gemini' ? 'selected' : '' }}>Google Gemini API (Disarankan)</option>
                                                    <option value="openai" {{ $setting->value == 'openai' ? 'selected' : '' }}>OpenAI GPT API</option>
                                                    <option value="off" {{ $setting->value == 'off' ? 'selected' : '' }}>Smart Engine Internal (Tanpa API Key)</option>
                                                </select>
                                            @elseif($setting->key == 'whatsapp_provider')
                                                <select name="{{ $setting->key }}" class="form-control theme-input">
                                                    <option value="fonnte" {{ $setting->value == 'fonnte' ? 'selected' : '' }}>Fonnte API (Sangat Disarankan di Indonesia)</option>
                                                    <option value="wablas" {{ $setting->value == 'wablas' ? 'selected' : '' }}>Wablas API Gateway</option>
                                                    <option value="generic" {{ $setting->value == 'generic' ? 'selected' : '' }}>Custom HTTP Webhook Gateway</option>
                                                </select>
                                            @elseif(in_array($setting->key, ['gemini_api_key', 'openai_api_key', 'whatsapp_api_token', 'telegram_bot_token', 'telegram_ip_bot_token', 'smtp_password', 'smtp_pass']))
                                                <div class="input-group">
                                                    <input type="password" id="input-{{ $setting->key }}" name="{{ $setting->key }}" class="form-control theme-input" value="{{ $setting->value }}" placeholder="Masukkan Token / API Key / Password..." {{ ($group == 'ai' && !$isSuperAdmin) ? 'disabled' : '' }}>
                                                    <div class="input-group-append">
                                                        <button class="btn toggle-key-btn" type="button" data-target="input-{{ $setting->key }}">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                @if($setting->key == 'telegram_bot_token')
                                                    <small class="text-muted d-block mt-1"><i class="fas fa-robot mr-1"></i> Bot utama untuk log aktivitas sistem, tiket helpdesk, dan asisten AI.</small>
                                                @elseif($setting->key == 'telegram_ip_bot_token')
                                                    <small class="text-muted d-block mt-1"><i class="fas fa-network-wired mr-1"></i> Bot terpisah khusus notifikasi IP Offline/Online (Kosongkan jika ingin gabung dengan Bot Utama).</small>
                                                @endif
                                            @elseif($setting->type == 'textarea')
                                                <textarea name="{{ $setting->key }}" class="form-control theme-input" rows="3" {{ ($group == 'ai' && !$isSuperAdmin) ? 'disabled' : '' }}>{{ $setting->value }}</textarea>
                                            @elseif($setting->key == 'ip_offline_email_time')
                                                 <input type="time" name="{{ $setting->key }}" class="form-control theme-input" style="max-width: 180px;" value="{{ $setting->value ?: '08:00' }}">
                                                 <small class="text-muted d-block mt-1"><i class="fas fa-clock mr-1"></i> Jam eksekusi harian laporan email IP Offline (Default: 08:00)</small>
                                            @elseif($setting->key == 'ip_offline_email_recipients')
                                                 <input type="text" name="{{ $setting->key }}" class="form-control theme-input" value="{{ $setting->value }}" placeholder="Contoh: admin@perusahaan.com, it@perusahaan.com">
                                                 <small class="text-muted d-block mt-1"><i class="fas fa-envelope mr-1"></i> Alamat email tujuan (pisahkan dengan koma jika lebih dari satu). Kosongkan untuk mengirim ke semua Super Admin.</small>
                                            @elseif($setting->key == 'whatsapp_admin_phone')
                                                 <input type="text" name="{{ $setting->key }}" class="form-control theme-input" value="{{ $setting->value }}" placeholder="Contoh: 081234567890">
                                                 <small class="text-muted d-block mt-1"><i class="fas fa-phone-alt mr-1"></i> Nomor telepon WhatsApp Admin/Penerima Alert (misal: 081234567890)</small>
                                            @elseif($setting->key == 'whatsapp_api_url')
                                                 <input type="text" name="{{ $setting->key }}" class="form-control theme-input" value="{{ $setting->value }}" placeholder="https://api.fonnte.com/send">
                                                 <small class="text-muted d-block mt-1"><i class="fas fa-link mr-1"></i> URL Endpoint API (Fonnte: https://api.fonnte.com/send | Wablas: https://kudus.wablas.com/api/send-message)</small>
                                            @elseif($setting->key == 'telegram_chat_id')
                                                 <input type="text" name="{{ $setting->key }}" class="form-control theme-input" value="{{ $setting->value }}" placeholder="Contoh: 8679359045">
                                                 <small class="text-muted d-block mt-1"><i class="fas fa-users mr-1"></i> ID Grup / Channel atau User Telegram tujuan untuk Log Aktivitas Utama.</small>
                                            @elseif($setting->key == 'telegram_ip_chat_id')
                                                 <input type="text" name="{{ $setting->key }}" class="form-control theme-input" value="{{ $setting->value }}" placeholder="Kosongkan jika ingin menggunakan Chat ID Utama">
                                                 <small class="text-muted d-block mt-1"><i class="fas fa-users-cog mr-1"></i> ID Grup / Channel Telegram khusus Notifikasi IP Address (Kosongkan jika ingin menggunakan Chat ID Utama).</small>
                                            @elseif($setting->type == 'boolean')
                                                <select name="{{ $setting->key }}" class="form-control theme-input select-sm-custom" {{ ($group == 'ai' && !$isSuperAdmin) ? 'disabled' : '' }}>
                                                    <option value="1"  {{ $setting->value == '1' ? 'selected' : '' }}>{{ __('messages.enabled') }}</option>
                                                    <option value="0"  {{ $setting->value == '0' ? 'selected' : '' }}>{{ __('messages.disabled') }}</option>
                                                </select>
                                            @else
                                                <input type="text" name="{{ $setting->key }}" class="form-control theme-input" value="{{ $setting->value }}" {{ ($group == 'ai' && !$isSuperAdmin) ? 'disabled' : '' }}>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                @if($group == 'whatsapp')
                                    <div class="mt-4 pt-3 border-top" style="border-color: rgba(255,255,255,0.08) !important;">
                                        <button type="button" id="btn-test-whatsapp" class="btn btn-outline-success custom-btn-outline px-4">
                                            {{ __('messages.test_whatsapp') ?? 'Kirim Pesan Pengujian WhatsApp' }}
                                        </button>
                                        <span id="test-wa-spinner" class="spinner-border spinner-border-sm text-success ml-2 d-none" role="status"></span>
                                        <div id="test-wa-result" class="mt-3"></div>
                                    </div>
                                @endif
                                @if($group == 'telegram')
                                    <div class="mt-4 pt-3 border-top" style="border-color: rgba(255,255,255,0.08) !important;">
                                        <button type="button" id="btn-test-telegram" class="btn btn-outline-primary custom-btn-outline px-4" style="color: #3b82f6; border-color: #3b82f6;">
                                            {{ __('messages.test_telegram') ?? 'Kirim Pesan Pengujian Telegram' }}
                                        </button>
                                        <span id="test-tg-spinner" class="spinner-border spinner-border-sm text-primary ml-2 d-none" role="status"></span>
                                        <div id="test-tg-result" class="mt-3"></div>
                                    </div>
                                @endif
                            </div>
                            @php $first = false; @endphp
                        @endforeach
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-right pb-4 pr-4 card-footer-actions">
                    <button type="submit" class="btn btn-primary custom-btn-save px-4">
                        {{ __('messages.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Tab Persistence Logic
    $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
        const target = $(e.target).attr('href');
        if (target) {
            $('#active_tab_input').val(target);
            localStorage.setItem('active_settings_tab', target);
            if (history.replaceState) {
                history.replaceState(null, null, target);
            }
        }
    });

    const urlParams = new URLSearchParams(window.location.search);
    const activeTabFromUrl = urlParams.get('active_tab');
    let targetTab = activeTabFromUrl || window.location.hash || localStorage.getItem('active_settings_tab');

    if (targetTab && $('a[href="' + targetTab + '"]').length) {
        $('a[href="' + targetTab + '"]').tab('show');
        $('#active_tab_input').val(targetTab);
    }

    $('.toggle-key-btn').click(function() {
        const targetId = $(this).data('target');
        const input = $('#' + targetId);
        const icon = $(this).find('i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    $('#btn-test-telegram').click(function() {
        const botToken = $('input[name="telegram_bot_token"]').val();
        const chatId = $('input[name="telegram_chat_id"]').val();
        
        if (!botToken || !chatId) {
            Swal.fire({ icon: 'error', text: 'Harap isi Bot Token dan Chat ID Telegram terlebih dahulu!', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, background: '#0f172a', color: '#f8fafc', customClass: { popup: 'border border-danger' } });
            return;
        }

        const btn = $(this);
        const spinner = $('#test-tg-spinner');
        const resultDiv = $('#test-tg-result');

        btn.prop('disabled', true);
        spinner.removeClass('d-none');
        resultDiv.html('');

        $.ajax({
            url: '{{ route("settings.telegram.test") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                telegram_bot_token: botToken,
                telegram_chat_id: chatId
            },
            success: function(res) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                if (res.success) {
                    resultDiv.html('<div class="alert alert-success mt-2 mb-0" style="border-radius: 10px;"><i class="fas fa-check-circle mr-2"></i> ' + res.message + '</div>');
                } else {
                    resultDiv.html('<div class="alert alert-danger mt-2 mb-0" style="border-radius: 10px;"><i class="fas fa-exclamation-triangle mr-2"></i> ' + (res.message || 'Error') + '</div>');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                resultDiv.html('<div class="alert alert-danger mt-2 mb-0" style="border-radius: 10px;"><i class="fas fa-times-circle mr-2"></i> Terjadi kesalahan koneksi saat menguji pesan Telegram.</div>');
            }
        });
    });

    $('#btn-test-whatsapp').click(function() {
        const adminPhone = $('input[name="whatsapp_admin_phone"]').val();
        if (!adminPhone) {
            Swal.fire({ icon: 'error', text: 'Harap isi Nomor WhatsApp Admin Penerima Alert terlebih dahulu!', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, background: '#0f172a', color: '#f8fafc', customClass: { popup: 'border border-danger' } });
            return;
        }

        const btn = $(this);
        const spinner = $('#test-wa-spinner');
        const resultDiv = $('#test-wa-result');

        btn.prop('disabled', true);
        spinner.removeClass('d-none');
        resultDiv.html('');

        $.ajax({
            url: '{{ route("settings.whatsapp.test") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                whatsapp_admin_phone: adminPhone
            },
            success: function(res) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                if (res.success) {
                    resultDiv.html('<div class="alert alert-success mt-2 mb-0" style="border-radius: 10px;"><i class="fas fa-check-circle mr-2"></i> ' + res.message + '</div>');
                } else {
                    resultDiv.html('<div class="alert alert-danger mt-2 mb-0" style="border-radius: 10px;"><i class="fas fa-exclamation-triangle mr-2"></i> ' + res.message + '</div>');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                resultDiv.html('<div class="alert alert-danger mt-2 mb-0" style="border-radius: 10px;"><i class="fas fa-times-circle mr-2"></i> Terjadi kesalahan koneksi saat menguji pesan WhatsApp.</div>');
            }
        });
    });
});
</script>
@endpush

@endsection
