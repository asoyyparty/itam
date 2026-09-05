@extends('layouts.admin')

@section('title', __('messages.manage_permissions') . ': ' . $role->name)

@section('content')
<style>
    .content-header {
        display: none !important;
    }

    .roles-edit-card {
        border-radius: 16px !important;
        overflow: hidden !important;
        border: var(--rule-soft);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    /* Helper class to reliably hide elements when searching */
    .perm-tile.d-none, .roles-edit-card.d-none {
        display: none !important;
    }
    
    /* Sleek Integrated Search Input */
    .sleek-search-wrapper {
        position: relative;
    }
    .sleek-search-wrapper .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.9rem;
        pointer-events: none;
    }
    .sleek-search-input {
        height: 42px !important;
        padding-left: 44px !important;
        padding-right: 18px !important;
        border-radius: 14px !important;
        border: 1px solid rgba(148, 163, 184, 0.2) !important;
        background: rgba(15, 23, 42, 0.35) !important;
        box-shadow: none !important;
        font-size: 0.88rem !important;
        color: var(--color-ink-0) !important;
        transition: all 0.2s ease !important;
    }
    .sleek-search-input::placeholder {
        color: #94a3b8 !important;
        opacity: 0.8;
    }
    .sleek-search-input:focus {
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2) !important;
        background: rgba(15, 23, 42, 0.5) !important;
    }

    /* 2-Column Grid for Permission Tiles */
    .perm-grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 10px;
        padding: 14px;
    }
    .perm-tile {
        border-radius: 12px;
        background: rgba(15, 23, 42, 0.25);
        border: 1px solid rgba(148, 163, 184, 0.14);
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.18s ease;
        cursor: pointer;
        user-select: none;
    }
    .perm-tile:hover {
        background: rgba(59, 130, 246, 0.08);
        border-color: rgba(96, 165, 250, 0.3);
        transform: translateY(-1px);
    }
    .perm-tile.is-checked {
        background: rgba(59, 130, 246, 0.12) !important;
        border-color: rgba(96, 165, 250, 0.4) !important;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08);
    }

    /* Light Mode Support for Permission Edit View */
    body.light-mode .perm-tile {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    body.light-mode .perm-tile:hover {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
    }
    body.light-mode .perm-tile.is-checked {
        background: #eff6ff !important;
        border-color: #bfdbfe !important;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.12) !important;
    }
    body.light-mode .perm-title {
        color: #0f172a !important;
    }
    body.light-mode .perm-tile.is-checked .perm-title {
        color: #1e40af !important;
    }
    body.light-mode .perm-code {
        color: #64748b !important;
        opacity: 0.9 !important;
    }
    body.light-mode .perm-tile.is-checked .perm-code {
        color: #3b82f6 !important;
    }

    /* Light Mode Cards */
    body.light-mode .roles-edit-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important;
    }
    body.light-mode .roles-edit-card .card-header {
        background: #f8fafc !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }
    body.light-mode .roles-edit-card .card-header-title {
        color: #0f172a !important;
    }

    /* Light Mode Search Input */
    body.light-mode .sleek-search-input {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        color: #0f172a !important;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04) !important;
    }
    body.light-mode .sleek-search-input::placeholder {
        color: #64748b !important;
        opacity: 1 !important;
    }
    body.light-mode .sleek-search-wrapper .search-icon {
        color: #64748b !important;
    }

    /* Light Mode Header & Counter */
    body.light-mode .custom-header-title {
        color: #0f172a !important;
    }
    body.light-mode .custom-header-subtitle {
        color: #475569 !important;
        opacity: 1 !important;
    }
    body.light-mode .btn-back-oval {
        background: #ffffff !important;
        border-color: #cbd5e1 !important;
        color: #475569 !important;
    }
    body.light-mode .perm-counter-text {
        color: #475569 !important;
    }

    .avatar-badge {
        width: 32px !important;
        height: 32px !important;
        border-radius: 8px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 0.85rem !important;
        flex-shrink: 0 !important;
    }
    .bg-info-soft { background: rgba(14, 165, 233, 0.12); border: 1px solid rgba(14, 165, 233, 0.25); }
    .bg-success-soft { background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.25); }
    .bg-warning-soft { background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.25); }

    /* Custom Matched Action Buttons (Batal & Simpan) */
    .rounded-oval-btn {
        border-radius: 14px !important;
        height: 42px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .custom-btn-cancel {
        background: rgba(148, 163, 184, 0.12) !important;
        border: 1px solid rgba(148, 163, 184, 0.28) !important;
        color: #cbd5e1 !important;
        transition: all 0.2s ease !important;
    }
    .custom-btn-cancel:hover {
        background: rgba(148, 163, 184, 0.22) !important;
        color: #ffffff !important;
        border-color: rgba(148, 163, 184, 0.4) !important;
    }

    body.light-mode .custom-btn-cancel {
        background: #f1f5f9 !important;
        border: 1px solid #cbd5e1 !important;
        color: #475569 !important;
    }
    body.light-mode .custom-btn-cancel:hover {
        background: #e2e8f0 !important;
        color: #0f172a !important;
    }

    .custom-btn-save {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
        border: none !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.35) !important;
        transition: all 0.2s ease !important;
    }
    .custom-btn-save:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 6px 18px rgba(59, 130, 246, 0.5) !important;
        transform: translateY(-1px);
    }

    @media (max-width: 767.98px) {
        .perm-grid-container {
            grid-template-columns: 1fr;
            padding: 10px;
            gap: 8px;
        }
        .role-perm-header {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 10px !important;
        }
        .role-perm-global-btns {
            width: 100% !important;
            display: flex !important;
            gap: 8px !important;
        }
        .role-perm-global-btns .btn {
            flex: 1 !important;
            height: 38px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .sleek-search-wrapper {
            max-width: 100% !important;
        }
        .counter-badge-wrap {
            width: 100% !important;
            text-align: left !important;
        }
        .card-header-title {
            font-size: 0.82rem !important;
        }
        .toggle-group-btn {
            padding: 3px 8px !important;
            font-size: 0.68rem !important;
        }
        .role-perm-save-bar {
            flex-direction: column-reverse !important;
            width: 100% !important;
            gap: 10px !important;
        }
        .role-perm-save-bar .btn {
            width: 100% !important;
            height: 44px !important;
        }
    }
</style>

<!-- Clean Header Toolbar -->
<div class="row mb-3 align-items-center" style="margin-top: 0 !important; margin-bottom: 0.75rem !important;">
    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap role-perm-header gap-2">
        <div class="d-flex align-items-center" style="gap: 10px;">
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; border-radius: 12px !important; border-color: rgba(148, 163, 184, 0.25);" title="{{ __('messages.back') }}">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h5 class="mb-0 font-weight-bold theme-text" style="font-size: 1.05rem; line-height: 1.2;">
                    {{ __('messages.manage_role_permissions') }}: <span class="text-primary">{{ $role->name }}</span>
                </h5>
                <small class="text-muted d-block" style="font-size: 0.76rem;">{{ __('messages.manage_role_permissions_desc') }}</small>
            </div>
        </div>

        <div class="d-flex align-items-center role-perm-global-btns" style="gap: 8px;">
            <button type="button" class="btn btn-outline-primary rounded-oval-btn px-4" id="btn-select-all-global" style="height: 38px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                {{ __('messages.select_all') }}
            </button>
            <button type="button" class="btn btn-outline-secondary rounded-oval-btn px-4" id="btn-deselect-all-global" style="height: 38px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                {{ __('messages.deselect_all') }}
            </button>
        </div>
    </div>
</div>

<!-- Borderless Search & Counter Bar with Comfortable Spacing -->
<div class="d-flex align-items-center justify-content-between flex-wrap mb-3.5 gap-2" style="margin-bottom: 1rem !important;">
    <div class="sleek-search-wrapper flex-grow-1" style="min-width: 220px; max-width: calc(100% - 220px);">
        <i class="fas fa-search search-icon"></i>
        <input type="text" id="search-permissions" class="form-control theme-input sleek-search-input" placeholder="{{ __('messages.search_permissions_placeholder') }}" autocomplete="off">
    </div>
    <div class="counter-badge-wrap flex-shrink-0 text-left">
        <span class="text-muted font-weight-normal perm-counter-text" style="font-size: 0.82rem;">
            {{ __('messages.showing_counter_text') }} <strong id="visible-perm-count" class="text-primary font-weight-bold" style="font-size: 0.88rem;">{{ $permissions->count() }}</strong> {{ __('messages.of_counter_text') }} <strong>{{ $permissions->count() }}</strong> {{ __('messages.permissions_counter_text') }}
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

    // Partition permissions logically into 3 groups
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

<form action="{{ route('roles.update', $role->id) }}" method="POST" id="form-manage-permissions" class="mb-3">
    @csrf
    @method('PUT')

    <!-- 1. Navigation Menu Access Group -->
    <div class="card theme-card shadow-sm mb-3 roles-edit-card perm-group-card">
        <div class="card-header bg-transparent py-2.5 px-3 px-md-4" style="border-bottom: 1px solid var(--rule-soft);">
            <div style="display: flex !important; flex-direction: row !important; align-items: center !important; justify-content: space-between !important; flex-wrap: nowrap !important; width: 100%;">
                <div style="display: flex !important; flex-direction: row !important; align-items: center !important; flex-wrap: nowrap !important; min-width: 0; flex: 1; overflow: hidden;">
                    <span class="avatar-badge bg-info-soft text-info" style="flex-shrink: 0 !important; margin-right: 10px !important;"><i class="fas fa-layer-group"></i></span>
                    <h6 class="theme-text font-weight-bold card-header-title" style="font-size: 0.88rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0 !important;">{{ __('messages.perm_group_nav') }}</h6>
                </div>
                <button type="button" class="btn btn-xs btn-outline-info rounded-pill font-weight-bold toggle-group-btn" data-target=".menu-perm-switch" style="font-size: 0.72rem; padding: 4px 10px; border-color: rgba(14, 165, 233, 0.35); flex-shrink: 0 !important; margin-left: 8px !important; white-space: nowrap;">
                    {{ __('messages.toggle_group') }}
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="perm-grid-container">
                @foreach($menuPermissions as $permission)
                    @php
                        $cleanName = strtolower(trim($permission->name));
                        $label = $permissionLabels[$cleanName] ?? ucwords(str_replace(['_', '-'], ' ', $permission->name));
                        $isChecked = in_array($permission->name, $rolePermissions);
                    @endphp
                    <div class="perm-tile {{ $isChecked ? 'is-checked' : '' }}" data-label="{{ mb_strtolower($label . ' ' . $permission->name) }}">
                        <div class="perm-label-wrap pr-2" style="min-width: 0;">
                            <span class="perm-title theme-text font-weight-bold d-block text-truncate mb-0" style="font-size: 0.88rem; color: var(--color-ink-0);">{{ $label }}</span>
                            <span class="perm-code text-muted d-block text-truncate" style="font-size: 0.72rem; font-family: monospace; opacity: 0.75;">{{ $permission->name }}</span>
                        </div>
                        <div class="custom-control custom-switch flex-shrink-0 ml-2">
                            <input type="checkbox" class="custom-control-input perm-switch menu-perm-switch" id="perm_{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}" {{ $isChecked ? 'checked' : '' }}>
                            <label class="custom-control-label" for="perm_{{ $permission->id }}"></label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- 2. Edit, Create, Delete Action Access Group -->
    <div class="card theme-card shadow-sm mb-3 roles-edit-card perm-group-card">
        <div class="card-header bg-transparent py-2.5 px-3 px-md-4" style="border-bottom: 1px solid var(--rule-soft);">
            <div style="display: flex !important; flex-direction: row !important; align-items: center !important; justify-content: space-between !important; flex-wrap: nowrap !important; width: 100%;">
                <div style="display: flex !important; flex-direction: row !important; align-items: center !important; flex-wrap: nowrap !important; min-width: 0; flex: 1; overflow: hidden;">
                    <span class="avatar-badge bg-success-soft text-success" style="flex-shrink: 0 !important; margin-right: 10px !important;"><i class="fas fa-shield-alt"></i></span>
                    <h6 class="theme-text font-weight-bold card-header-title" style="font-size: 0.88rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0 !important;">{{ __('messages.perm_group_action') }}</h6>
                </div>
                <button type="button" class="btn btn-xs btn-outline-success rounded-pill font-weight-bold toggle-group-btn" data-target=".manage-perm-switch" style="font-size: 0.72rem; padding: 4px 10px; border-color: rgba(16, 185, 129, 0.35); flex-shrink: 0 !important; margin-left: 8px !important; white-space: nowrap;">
                    {{ __('messages.toggle_group') }}
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="perm-grid-container">
                @foreach($actionManagePermissions as $permission)
                    @php
                        $cleanName = strtolower(trim($permission->name));
                        $label = $permissionLabels[$cleanName] ?? ucwords(str_replace(['_', '-'], ' ', $permission->name));
                        $isChecked = in_array($permission->name, $rolePermissions);
                    @endphp
                    <div class="perm-tile {{ $isChecked ? 'is-checked' : '' }}" data-label="{{ mb_strtolower($label . ' ' . $permission->name) }}">
                        <div class="perm-label-wrap pr-2" style="min-width: 0;">
                            <span class="perm-title theme-text font-weight-bold d-block text-truncate mb-0" style="font-size: 0.88rem; color: var(--color-ink-0);">{{ $label }}</span>
                            <span class="perm-code text-muted d-block text-truncate" style="font-size: 0.72rem; font-family: monospace; opacity: 0.75;">{{ $permission->name }}</span>
                        </div>
                        <div class="custom-control custom-switch flex-shrink-0 ml-2">
                            <input type="checkbox" class="custom-control-input perm-switch manage-perm-switch" id="perm_{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}" {{ $isChecked ? 'checked' : '' }}>
                            <label class="custom-control-label" for="perm_{{ $permission->id }}"></label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- 3. Tools & AI Features Group -->
    @if($actionOtherPermissions->count() > 0)
    <div class="card theme-card shadow-sm mb-3 roles-edit-card perm-group-card">
        <div class="card-header bg-transparent py-2.5 px-3 px-md-4" style="border-bottom: 1px solid var(--rule-soft);">
            <div style="display: flex !important; flex-direction: row !important; align-items: center !important; justify-content: space-between !important; flex-wrap: nowrap !important; width: 100%;">
                <div style="display: flex !important; flex-direction: row !important; align-items: center !important; flex-wrap: nowrap !important; min-width: 0; flex: 1; overflow: hidden;">
                    <span class="avatar-badge bg-warning-soft text-warning" style="flex-shrink: 0 !important; margin-right: 10px !important;"><i class="fas fa-sliders-h"></i></span>
                    <h6 class="theme-text font-weight-bold card-header-title" style="font-size: 0.88rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0 !important;">{{ __('messages.perm_group_tools') }}</h6>
                </div>
                <button type="button" class="btn btn-xs btn-outline-warning rounded-pill font-weight-bold toggle-group-btn" data-target=".tools-perm-switch" style="font-size: 0.72rem; padding: 4px 10px; border-color: rgba(245, 158, 11, 0.35); flex-shrink: 0 !important; margin-left: 8px !important; white-space: nowrap;">
                    {{ __('messages.toggle_group') }}
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="perm-grid-container">
                @foreach($actionOtherPermissions as $permission)
                    @php
                        $cleanName = strtolower(trim($permission->name));
                        $label = $permissionLabels[$cleanName] ?? ucwords(str_replace(['_', '-'], ' ', $permission->name));
                        $isChecked = in_array($permission->name, $rolePermissions);
                    @endphp
                    <div class="perm-tile {{ $isChecked ? 'is-checked' : '' }}" data-label="{{ mb_strtolower($label . ' ' . $permission->name) }}">
                        <div class="perm-label-wrap pr-2" style="min-width: 0;">
                            <span class="perm-title theme-text font-weight-bold d-block text-truncate mb-0" style="font-size: 0.88rem; color: var(--color-ink-0);">{{ $label }}</span>
                            <span class="perm-code text-muted d-block text-truncate" style="font-size: 0.72rem; font-family: monospace; opacity: 0.75;">{{ $permission->name }}</span>
                        </div>
                        <div class="custom-control custom-switch flex-shrink-0 ml-2">
                            <input type="checkbox" class="custom-control-input perm-switch tools-perm-switch" id="perm_{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}" {{ $isChecked ? 'checked' : '' }}>
                            <label class="custom-control-label" for="perm_{{ $permission->id }}"></label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Save Action Bar (Unified Desktop & Mobile) -->
    <div class="role-perm-save-bar d-flex justify-content-end align-items-center mt-3 mb-4" style="gap: 12px;">
        <a href="{{ route('roles.index') }}" class="btn custom-btn-cancel rounded-oval-btn px-4">
            {{ __('messages.cancel') }}
        </a>
        <button type="submit" class="btn custom-btn-save rounded-oval-btn px-4">
            {{ __('messages.save') }}
        </button>
    </div>
</form>

@push('scripts')
<script>
  $(document).ready(function() {
    $(document).on('click', '.perm-tile', function(e) {
      if ($(e.target).is('input[type="checkbox"]') || $(e.target).is('label')) {
        return;
      }
      const checkbox = $(this).find('.perm-switch');
      const newState = !checkbox.is(':checked');
      checkbox.prop('checked', newState).trigger('change');
    });

    $(document).on('change', '.perm-switch', function() {
      const tile = $(this).closest('.perm-tile');
      if ($(this).is(':checked')) {
        tile.addClass('is-checked');
      } else {
        tile.removeClass('is-checked');
      }
    });

    // Realtime Search Filter with Class-based Toggle
    $('#search-permissions').on('keyup input change', function() {
      const val = $(this).val().toLowerCase().trim();
      let visibleCount = 0;

      $('.perm-tile').each(function() {
        const text = ($(this).attr('data-label') || $(this).text()).toLowerCase();
        if (!val || text.indexOf(val) !== -1) {
          $(this).removeClass('d-none');
          visibleCount++;
        } else {
          $(this).addClass('d-none');
        }
      });

      $('#visible-perm-count').text(visibleCount);

      // Hide empty card groups when searching
      $('.perm-group-card').each(function() {
        const hasVisible = $(this).find('.perm-tile:not(.d-none)').length > 0;
        if (hasVisible) {
          $(this).removeClass('d-none');
        } else {
          $(this).addClass('d-none');
        }
      });
    });

    $('.toggle-group-btn').on('click', function() {
      const targetSelector = $(this).data('target');
      const switches = $(targetSelector);
      const allChecked = switches.filter(':checked').length === switches.length;
      switches.prop('checked', !allChecked).trigger('change');
    });

    $('#btn-select-all-global').on('click', function() {
      $('.perm-switch').prop('checked', true).trigger('change');
    });

    $('#btn-deselect-all-global').on('click', function() {
      $('.perm-switch').prop('checked', false).trigger('change');
    });
  });
</script>
@endpush
@endsection
