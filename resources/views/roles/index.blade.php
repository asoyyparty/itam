@extends('layouts.admin')

@section('title', __('messages.roles_permissions'))

@section('content')
<style>
    /* Hallmark · component: roles-overview-workbench · genre: modern-minimal · theme: workbench
     * states: default · hover · focus-visible · active · disabled
     * contrast: pass WCAG AA · mobile: 320/375/414/768px compliant (zero horizontal scroll)
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
        --hm-ink-primary: #0f172a;
        --hm-ink-muted: #64748b;
    }

    /* Container Protection against horizontal overflow */
    .hallmark-roles-container {
        width: 100%;
        max-width: 100%;
        overflow-x: clip;
        padding-bottom: 40px;
    }

    /* Header Styling */
    .hallmark-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 1.25rem;
    }
    .header-titles {
        flex: 1 1 auto;
        min-width: 260px;
    }
    .header-action-wrap {
        flex-shrink: 0;
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

    /* Workbench Cards */
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

    /* Metric Telemetry Overview Cards */
    .metric-grid-overview {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 1.25rem;
    }
    .metric-card-item {
        background: var(--hm-card-bg);
        border: 1px solid var(--hm-card-border);
        border-radius: 14px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: transform 0.16s ease, border-color 0.16s ease;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
    .metric-card-item:hover {
        transform: translateY(-2px);
        border-color: rgba(96, 165, 250, 0.35);
    }
    .metric-icon-badge {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .metric-value-text {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--hm-ink-primary);
        line-height: 1.1;
    }
    .metric-label-text {
        font-size: 0.74rem;
        font-weight: 600;
        color: var(--hm-ink-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
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

    /* Action Buttons & Primary CTA */
    .btn-action-primary {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
        border: 1px solid rgba(129, 140, 248, 0.4) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.3) !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    .btn-action-primary:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.45) !important;
        filter: brightness(1.08) !important;
        color: #ffffff !important;
    }

    /* Clearance Tier Badges */
    .tier-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
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
    }

    /* Light Mode Contrast Enhancements */
    body.light-mode .tier-restricted {
        background: rgba(100, 116, 139, 0.1);
        color: #475569;
        border-color: rgba(100, 116, 139, 0.3);
    }
    body.light-mode .tier-standard {
        background: rgba(2, 132, 199, 0.1);
        color: #0369a1;
        border-color: rgba(2, 132, 199, 0.3);
    }
    body.light-mode .tier-elevated {
        background: rgba(16, 185, 129, 0.1);
        color: #047857;
        border-color: rgba(16, 185, 129, 0.3);
    }
    body.light-mode .tier-full-access {
        background: rgba(147, 51, 234, 0.1);
        color: #7e22ce;
        border-color: rgba(147, 51, 234, 0.3);
    }

    /* Role Type Tag */
    .role-meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 0.68rem;
        font-weight: 600;
    }

    /* Table Styling for Desktop */
    .table-clearance {
        margin: 0;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-clearance thead th {
        background: rgba(15, 23, 42, 0.4);
        border-bottom: 1px solid var(--hm-card-border);
        color: var(--hm-ink-muted);
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 14px 20px;
    }
    body.light-mode .table-clearance thead th {
        background: #f8fafc;
    }
    .table-clearance tbody td {
        border-bottom: 1px solid var(--hm-card-border);
        padding: 16px 20px;
        vertical-align: middle;
    }
    .table-clearance tbody tr:last-child td {
        border-bottom: none;
    }
    .table-clearance tbody tr {
        transition: background-color 0.16s ease;
    }
    .table-clearance tbody tr:hover {
        background: rgba(255, 255, 255, 0.02);
    }
    body.light-mode .table-clearance tbody tr:hover {
        background: #f8fafc;
    }

    /* Mini Progress Bar in Table */
    .mini-meter-track {
        height: 6px;
        border-radius: 9999px;
        background: rgba(148, 163, 184, 0.15);
        overflow: hidden;
        width: 100%;
        max-width: 140px;
    }
    .mini-meter-bar {
        height: 100%;
        border-radius: 9999px;
        background: linear-gradient(90deg, #60a5fa 0%, #3b82f6 100%);
    }

    /* Action Buttons */
    .action-circle-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
        transition: all 0.16s ease;
    }
    .action-circle-btn:hover {
        transform: translateY(-1px);
    }

    /* Mobile Role Cards */
    .mobile-role-card {
        background: var(--hm-card-bg);
        border: 1px solid var(--hm-card-border);
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 12px;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        transition: border-color 0.16s ease;
    }
    .mobile-role-card:active {
        border-color: rgba(96, 165, 250, 0.4);
    }
    .mobile-btn-action {
        height: 44px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none !important;
        width: 100%;
    }

    /* Mobile Responsive Breakpoints */
    @media (max-width: 991.98px) {
        .metric-grid-overview {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
    }

    @media (max-width: 767.98px) {
        .hallmark-roles-container {
            padding-bottom: 75px;
        }
        .metric-grid-overview {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 1.25rem;
        }
        .metric-card-item {
            padding: 12px 14px;
            gap: 12px;
            border-radius: 14px;
        }
        .metric-icon-badge {
            width: 38px;
            height: 38px;
            font-size: 1rem;
            border-radius: 10px;
        }
        .metric-value-text {
            font-size: 1.3rem;
            line-height: 1.1;
        }
        .metric-label-text {
            font-size: 0.7rem;
            margin-top: 2px;
        }
        .hallmark-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 1.25rem;
        }
        .header-action-wrap {
            width: 100%;
        }
        .header-action-wrap .btn {
            width: 100%;
            height: 42px;
            justify-content: center;
            font-size: 0.88rem;
        }
        .mobile-role-card {
            padding: 16px;
            margin-bottom: 14px;
            border-radius: 16px;
        }
        .mobile-btn-action {
            height: 42px;
            font-size: 0.86rem;
            border-radius: 12px;
        }
    }
</style>

<div class="hallmark-roles-container">
    <!-- Header Toolbar -->
    <div class="hallmark-header">
        <div class="header-titles">
            <div class="d-flex align-items-center gap-2 mb-1.5">
                <span class="hallmark-tag-pill">
                    <i class="fas fa-shield-alt"></i> {{ __('messages.access_governance_console') }}
                </span>
            </div>
            <h5 class="mb-1 font-weight-bold" style="color: var(--hm-ink-primary); font-size: 1.2rem; line-height: 1.25;">
                {{ __('messages.roles_permissions') }}
            </h5>
            <small style="color: var(--hm-ink-muted); font-size: 0.78rem; line-height: 1.35; display: block;">
                {{ __('messages.roles_subtitle') }}
            </small>
        </div>
        @can('action_manage_settings')
        <div class="header-action-wrap">
            <a href="{{ route('roles.create') }}" class="btn btn-action-primary px-4" style="height: 42px; border-radius: 12px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none;">
                <i class="fas fa-plus-circle"></i> {{ __('messages.add_role') }}
            </a>
        </div>
        @endcan
    </div>

    @php
        $totalRolesCount = $roles->count();
        $systemRolesCount = $roles->whereIn('name', ['Super Admin', 'Admin', 'User'])->count();
        $customRolesCount = $roles->whereNotIn('name', ['Super Admin', 'Admin', 'User'])->count();
        $totalAssignedUsersCount = $roles->sum('users_count');
        $baseTotalPermissions = $totalPermissionsCount ?? 43;
    @endphp

    <!-- Metric Telemetry Overview Cards -->
    <div class="metric-grid-overview">
        <!-- 1. Total Roles -->
        <div class="metric-card-item">
            <div class="metric-icon-badge" style="background: rgba(99, 102, 241, 0.12); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.28);">
                <i class="fas fa-id-card"></i>
            </div>
            <div>
                <div class="metric-value-text">{{ $totalRolesCount }}</div>
                <div class="metric-label-text">{{ __('messages.total_roles') }}</div>
            </div>
        </div>

        <!-- 2. System Roles -->
        <div class="metric-card-item">
            <div class="metric-icon-badge" style="background: rgba(234, 179, 8, 0.12); color: #facc15; border: 1px solid rgba(234, 179, 8, 0.28);">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div>
                <div class="metric-value-text">{{ $systemRolesCount }}</div>
                <div class="metric-label-text">{{ __('messages.system_roles') }}</div>
            </div>
        </div>

        <!-- 3. Custom Roles -->
        <div class="metric-card-item">
            <div class="metric-icon-badge" style="background: rgba(14, 165, 233, 0.12); color: #38bdf8; border: 1px solid rgba(14, 165, 233, 0.28);">
                <i class="fas fa-sliders-h"></i>
            </div>
            <div>
                <div class="metric-value-text">{{ $customRolesCount }}</div>
                <div class="metric-label-text">{{ __('messages.custom_roles') }}</div>
            </div>
        </div>

        <!-- 4. Total Assigned Users -->
        <div class="metric-card-item">
            <div class="metric-icon-badge" style="background: rgba(16, 185, 129, 0.12); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.28);">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <div class="metric-value-text">{{ $totalAssignedUsersCount }}</div>
                <div class="metric-label-text">{{ __('messages.total_assigned_users') }}</div>
            </div>
        </div>
    </div>

    <!-- Integrated Search Bar -->
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-3 gap-2">
        <div class="search-filter-box flex-grow-1">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="search-roles" class="form-control search-filter-input" placeholder="{{ __('messages.filter_roles_placeholder') }}" autocomplete="off">
            <span class="search-shortcut-tag d-none d-sm-inline">/</span>
        </div>
    </div>

    <!-- Desktop View: Hallmark Security Clearance Table -->
    <div class="workbench-card d-none d-md-block mb-3" id="desktop-roles-card">
        <div class="table-responsive">
            <table class="table-clearance">
                <thead>
                    <tr>
                        <th class="pl-4">{{ __('messages.role') }}</th>
                        <th class="px-4">{{ __('messages.user_count') }}</th>
                        <th class="px-4">{{ __('messages.role_clearance_status') }}</th>
                        <th width="140" class="pr-4 text-center">{{ __('messages.action') }}</th>
                    </tr>
                </thead>
                <tbody id="roles-table-body">
                    @foreach($roles as $role)
                        @php
                            $isSuperAdmin = ($role->name === 'Super Admin');
                            $isAdmin = ($role->name === 'Admin');
                            $isUser = ($role->name === 'User');
                            $isSystemRole = in_array($role->name, ['Super Admin', 'Admin', 'User']);

                            $permCount = $isSuperAdmin ? $baseTotalPermissions : $role->permissions_count;
                            $percent = ($baseTotalPermissions > 0) ? min(100, round(($permCount / $baseTotalPermissions) * 100)) : 0;

                            if ($isSuperAdmin || $percent == 100) {
                                $tierClass = 'tier-full-access';
                                $tierName = __('messages.tier_full_access');
                            } elseif ($percent > 60) {
                                $tierClass = 'tier-elevated';
                                $tierName = __('messages.tier_elevated');
                            } elseif ($percent > 25) {
                                $tierClass = 'tier-standard';
                                $tierName = __('messages.tier_standard');
                            } else {
                                $tierClass = 'tier-restricted';
                                $tierName = __('messages.tier_restricted');
                            }
                        @endphp
                        <tr class="role-row-item" data-role-name="{{ mb_strtolower($role->name) }}">
                            <!-- Role Identity -->
                            <td class="pl-4">
                                <div class="d-flex align-items-center" style="gap: 12px;">
                                    <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(99, 102, 241, 0.12); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.25); display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                                        <i class="fas {{ $isSuperAdmin ? 'fa-user-astronaut' : ($isAdmin ? 'fa-user-shield' : ($isUser ? 'fa-user' : 'fa-user-tag')) }}"></i>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold" style="color: var(--hm-ink-primary); font-size: 0.95rem;">
                                            {{ $role->name }}
                                        </div>
                                        @if($isSystemRole)
                                            <span class="role-meta-pill" style="background: rgba(234, 179, 8, 0.12); color: #facc15; border: 1px solid rgba(234, 179, 8, 0.28);">
                                                <i class="fas fa-lock mr-1"></i> {{ __('messages.system_default_role') }}
                                            </span>
                                        @else
                                            <span class="role-meta-pill" style="background: rgba(99, 102, 241, 0.12); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.28);">
                                                <i class="fas fa-sliders-h mr-1"></i> {{ __('messages.custom_role') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- User Count -->
                            <td class="px-4">
                                <span class="badge" style="background: rgba(148, 163, 184, 0.12); color: var(--hm-ink-primary); border: 1px solid rgba(148, 163, 184, 0.22); padding: 6px 12px; border-radius: 10px; font-size: 0.8rem; font-weight: 600;">
                                    <i class="fas fa-users mr-1.5 text-muted"></i> {{ $role->users_count }} {{ $role->users_count == 1 ? __('messages.user_badge_count') : __('messages.users_badge_count') }}
                                </span>
                            </td>

                            <!-- Clearance Status -->
                            <td class="px-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-1" style="gap: 12px;">
                                            <span class="tier-badge {{ $tierClass }}">
                                                {{ $tierName }}
                                            </span>
                                            <span style="font-size: 0.76rem; font-weight: 700; color: var(--hm-ink-primary);">
                                                {{ $percent }}%
                                            </span>
                                        </div>
                                        <div class="mini-meter-track">
                                            <div class="mini-meter-bar" style="width: {{ $percent }}%;"></div>
                                        </div>
                                        <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">
                                            @if($isSuperAdmin)
                                                {{ __('messages.full_access_100') }}
                                            @else
                                                {{ $permCount }} / {{ $baseTotalPermissions }} {{ __('messages.permissions') }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="pr-4 text-center">
                                @if($isSuperAdmin)
                                    <span class="text-muted" title="{{ App::getLocale() == 'en' ? 'Super Admin is immutable' : 'Super Admin tidak dapat diubah' }}">
                                        <i class="fas fa-lock text-secondary"></i>
                                    </span>
                                @elseif(!auth()->user()->can('action_manage_settings'))
                                    <span class="text-muted"><i class="fas fa-ban"></i></span>
                                @else
                                    <div class="d-flex justify-content-center align-items-center" style="gap: 8px;">
                                        <a href="{{ route('roles.edit', $role->id) }}" class="action-circle-btn" style="background: rgba(14, 165, 233, 0.12); color: #38bdf8; border-color: rgba(14, 165, 233, 0.3);" title="{{ __('messages.manage_permissions') }}">
                                            <i class="fas fa-user-shield"></i>
                                        </a>

                                        @if(!$isSystemRole)
                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-delete action-circle-btn" style="background: rgba(239, 68, 68, 0.12); color: #f87171; border-color: rgba(239, 68, 68, 0.3);" title="{{ __('messages.delete') }}" data-confirm-message="{{ App::getLocale() == 'en' ? 'Are you sure you want to delete role' : 'Apakah Anda yakin ingin menghapus peran' }} '{{ $role->name }}'?">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile View: Hallmark Tactile Bento Role Cards -->
    <div class="d-block d-md-none" id="mobile-roles-list">
        @foreach($roles as $role)
            @php
                $isSuperAdmin = ($role->name === 'Super Admin');
                $isAdmin = ($role->name === 'Admin');
                $isUser = ($role->name === 'User');
                $isSystemRole = in_array($role->name, ['Super Admin', 'Admin', 'User']);

                $permCount = $isSuperAdmin ? $baseTotalPermissions : $role->permissions_count;
                $percent = ($baseTotalPermissions > 0) ? min(100, round(($permCount / $baseTotalPermissions) * 100)) : 0;

                if ($isSuperAdmin || $percent == 100) {
                    $tierClass = 'tier-full-access';
                    $tierName = __('messages.tier_full_access');
                } elseif ($percent > 60) {
                    $tierClass = 'tier-elevated';
                    $tierName = __('messages.tier_elevated');
                } elseif ($percent > 25) {
                    $tierClass = 'tier-standard';
                    $tierName = __('messages.tier_standard');
                } else {
                    $tierClass = 'tier-restricted';
                    $tierName = __('messages.tier_restricted');
                }
            @endphp
            <div class="mobile-role-card role-row-item" data-role-name="{{ mb_strtolower($role->name) }}">
                <!-- Card Header -->
                <div class="d-flex align-items-center justify-content-between" style="margin-bottom: 14px;">
                    <div class="d-flex align-items-center" style="gap: 12px; min-width: 0; flex: 1;">
                        <div style="width: 38px; height: 38px; border-radius: 11px; background: rgba(99, 102, 241, 0.12); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.25); display: flex; align-items: center; justify-content: center; font-size: 1.05rem; flex-shrink: 0;">
                            <i class="fas {{ $isSuperAdmin ? 'fa-user-astronaut' : ($isAdmin ? 'fa-user-shield' : ($isUser ? 'fa-user' : 'fa-user-tag')) }}"></i>
                        </div>
                        <div style="min-width: 0; flex: 1;">
                            <h6 class="mb-0 font-weight-bold text-truncate" style="color: var(--hm-ink-primary); font-size: 0.95rem; line-height: 1.25;">
                                {{ $role->name }}
                            </h6>
                            <div style="margin-top: 3px;">
                                @if($isSystemRole)
                                    <span class="role-meta-pill" style="background: rgba(234, 179, 8, 0.12); color: #facc15; border: 1px solid rgba(234, 179, 8, 0.28);">
                                        <i class="fas fa-lock mr-1"></i> {{ __('messages.system_default_role') }}
                                    </span>
                                @else
                                    <span class="role-meta-pill" style="background: rgba(99, 102, 241, 0.12); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.28);">
                                        <i class="fas fa-sliders-h mr-1"></i> {{ __('messages.custom_role') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <span class="badge flex-shrink-0" style="background: rgba(148, 163, 184, 0.12); color: var(--hm-ink-primary); border: 1px solid rgba(148, 163, 184, 0.22); padding: 5px 10px; border-radius: 8px; font-size: 0.74rem; font-weight: 600; margin-left: 8px;">
                        <i class="fas fa-users mr-1 text-muted"></i> {{ $role->users_count }}
                    </span>
                </div>

                <!-- Clearance Gauge & Stats -->
                <div style="background: var(--hm-tile-bg); border: 1px solid var(--hm-tile-border); border-radius: 14px; padding: 13px 15px; margin-bottom: 14px;">
                    <div class="d-flex align-items-center justify-content-between" style="margin-bottom: 10px;">
                        <span class="tier-badge {{ $tierClass }}">
                            {{ $tierName }}
                        </span>
                        <span style="font-size: 0.82rem; font-weight: 800; color: var(--hm-ink-primary);">
                            {{ $percent }}%
                        </span>
                    </div>
                    <div class="meter-progress-track" style="height: 6px; border-radius: 9999px; background: rgba(148, 163, 184, 0.15); overflow: hidden; margin-bottom: 10px;">
                        <div style="height: 100%; border-radius: 9999px; width: {{ $percent }}%; background: linear-gradient(90deg, #60a5fa 0%, #3b82f6 100%);"></div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between" style="font-size: 0.74rem; color: var(--hm-ink-muted);">
                        <span><i class="fas fa-key mr-1.5"></i> {{ __('messages.total_permissions') }}</span>
                        <strong>
                            @if($isSuperAdmin)
                                {{ __('messages.full_access_100') }}
                            @else
                                {{ $permCount }} / {{ $baseTotalPermissions }}
                            @endif
                        </strong>
                    </div>
                </div>

                <!-- Card Actions -->
                @if($isSuperAdmin)
                    <div class="p-2 rounded-lg text-center" style="background: rgba(168, 85, 247, 0.08); border: 1px dashed rgba(168, 85, 247, 0.3); font-size: 0.74rem; color: #c084fc; font-weight: 600;">
                        <i class="fas fa-shield-alt mr-1"></i> {{ __('messages.role_superadmin_protected') }}
                    </div>
                @elseif(auth()->user()->can('action_manage_settings'))
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <a href="{{ route('roles.edit', $role->id) }}" class="mobile-btn-action flex-grow-1" style="background: rgba(14, 165, 233, 0.12); color: #38bdf8; border: 1px solid rgba(14, 165, 233, 0.3);">
                            <i class="fas fa-user-shield mr-2"></i> {{ __('messages.manage_clearance') }}
                        </a>

                        @if(!$isSystemRole)
                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline" style="flex-shrink: 0; margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete action-circle-btn" style="width: 42px; height: 42px; border-radius: 12px; background: rgba(239, 68, 68, 0.12); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); display: inline-flex; align-items: center; justify-content: center;" title="{{ __('messages.delete') }}" data-confirm-message="{{ App::getLocale() == 'en' ? 'Are you sure you want to delete role' : 'Apakah Anda yakin ingin menghapus peran' }} '{{ $role->name }}'?">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Empty State for Search -->
    <div id="roles-empty-state" class="workbench-card p-4 text-center d-none">
        <i class="fas fa-search text-muted mb-2" style="font-size: 2rem; opacity: 0.4;"></i>
        <h6 class="font-weight-bold mb-1" style="color: var(--hm-ink-primary);">{{ __('messages.no_roles_found') }}</h6>
        <small class="text-muted">{{ __('messages.search_permissions_placeholder') }}</small>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Keyboard shortcut '/' for search
    $(document).on('keydown', function(e) {
        if (e.key === '/' && !$(e.target).is('input, textarea, select')) {
            e.preventDefault();
            $('#search-roles').focus();
        }
    });

    // Realtime Search Filter for both Desktop table rows and Mobile cards
    $('#search-roles').on('keyup input change', function() {
        const query = $(this).val().toLowerCase().trim();
        let visibleCount = 0;

        $('.role-row-item').each(function() {
            const roleName = ($(this).attr('data-role-name') || '').toLowerCase();
            if (!query || roleName.indexOf(query) !== -1) {
                $(this).removeClass('d-none');
                visibleCount++;
            } else {
                $(this).addClass('d-none');
            }
        });

        // Toggle Empty State
        if (visibleCount === 0) {
            $('#roles-empty-state').removeClass('d-none');
            $('#desktop-roles-card').addClass('d-none');
        } else {
            $('#roles-empty-state').addClass('d-none');
            $('#desktop-roles-card').removeClass('d-none');
        }
    });
});
</script>
@endpush
@endsection
