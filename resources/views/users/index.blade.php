@extends('layouts.admin')

@section('title', __('messages.manage_users'))

@section('content')
<!-- Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 -->
<style>
    /* ═══════════════════════════════════════════════════════════════════
       HALLMARK OBSIDIAN IAM CONSOLE: USER & LOGIN MANAGEMENT
       ═══════════════════════════════════════════════════════════════════ */
    .iam-workbench {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        overflow-x: clip;
    }

    .content-header {
        display: none !important;
    }

    .iam-workbench {
        padding-bottom: 20px;
    }

    /* 1. Header Command Banner */
    .iam-header-card {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 16px;
        padding: 20px 24px;
        position: relative;
        overflow: hidden;
    }
    .iam-header-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, #6366f1 0%, #38bdf8 50%, #10b981 100%);
    }
    .iam-header-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .iam-header-left {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
        flex: 1 1 auto;
    }
    .iam-header-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: rgba(99, 102, 241, 0.12);
        border: 1px solid rgba(99, 102, 241, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #818cf8;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .iam-header-title {
        font-size: 1.25rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        color: var(--color-ink-0, #f8fafc);
        margin: 0;
        line-height: 1.25;
    }
    .iam-header-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.78rem;
        color: var(--color-ink-2, #94a3b8);
        margin-top: 4px;
        flex-wrap: wrap;
    }
    .iam-badge-live {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 2px 8px;
        border-radius: 9999px;
        background: rgba(16, 185, 129, 0.12);
        border: 1px solid rgba(16, 185, 129, 0.28);
        color: #34d399;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.04em;
    }
    .iam-badge-pulse {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 8px #10b981;
        animation: iam-pulse 2s infinite;
    }
    @keyframes iam-pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.85); }
    }

    .iam-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
    }
    .btn-iam-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 42px;
        padding: 0 18px;
        border-radius: 10px;
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        border: 1px solid rgba(99, 102, 241, 0.4);
        color: #ffffff !important;
        font-weight: 600;
        font-size: 0.86rem;
        box-shadow: 0 2px 10px rgba(99, 102, 241, 0.3);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none !important;
        cursor: pointer;
    }
    .btn-iam-primary:hover {
        background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
        box-shadow: 0 4px 16px rgba(99, 102, 241, 0.45);
        transform: translateY(-1px);
    }

    /* 2. Bento KPI Matrix */
    .iam-bento-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }
    .iam-bento-tile {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 14px;
        padding: 16px 18px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        transition: border-color 0.2s ease, transform 0.2s ease;
    }
    .iam-bento-tile:hover {
        border-color: rgba(99, 102, 241, 0.3);
        transform: translateY(-1px);
    }
    .iam-bento-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .iam-bento-label {
        font-size: 0.76rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--color-ink-2, #94a3b8);
        margin: 0;
    }
    .iam-bento-icon {
        font-size: 0.95rem;
        color: #818cf8;
        opacity: 0.8;
    }
    .iam-bento-val {
        font-size: 1.55rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        color: var(--color-ink-0, #f8fafc);
        font-family: 'JetBrains Mono', monospace;
        line-height: 1.1;
    }
    .iam-bento-sub {
        font-size: 0.74rem;
        color: var(--color-ink-2, #64748b);
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }
    .iam-role-pill-sm {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 1px 6px;
        border-radius: 4px;
        background: rgba(148, 163, 184, 0.1);
        border: 1px solid rgba(148, 163, 184, 0.2);
        font-size: 0.68rem;
        font-family: 'JetBrains Mono', monospace;
        color: #cbd5e1;
    }
    .iam-progress-bar {
        height: 5px;
        border-radius: 9999px;
        background: rgba(148, 163, 184, 0.15);
        overflow: hidden;
        margin-top: 8px;
        width: 100%;
    }
    .iam-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #38bdf8, #10b981);
        border-radius: 9999px;
        transition: width 0.4s ease;
    }

    /* 3. Control & Toolbar Card */
    .iam-toolbar-card {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 14px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }
    .iam-search-box {
        position: relative;
        width: 100%;
        max-width: 420px;
        height: 42px;
        display: flex;
        align-items: center;
        flex: 0 1 auto;
    }
    .iam-search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--color-ink-2, #64748b);
        font-size: 0.85rem;
        pointer-events: none;
        z-index: 2;
        line-height: 1;
    }
    .iam-search-input {
        width: 100%;
        height: 100%;
        padding: 0 36px 0 38px;
        border-radius: 10px;
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(148, 163, 184, 0.18);
        color: var(--color-ink-0, #f8fafc);
        font-size: 0.86rem;
        transition: all 0.2s ease;
    }
    .iam-search-input:focus {
        background: rgba(15, 23, 42, 0.9);
        border-color: #6366f1;
        outline: none;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    }
    .iam-search-clear {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.8rem;
        cursor: pointer;
        padding: 4px;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .iam-search-clear:hover {
        color: #f87171;
    }

    /* Segmented Role Filter Pills */
    .iam-pills-bar {
        display: flex;
        align-items: center;
        gap: 6px;
        overflow-x: auto;
        padding-bottom: 2px;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
    }
    .iam-pills-bar::-webkit-scrollbar {
        display: none;
    }
    .iam-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 34px;
        padding: 0 12px;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 600;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(148, 163, 184, 0.16);
        color: var(--color-ink-2, #94a3b8);
        text-decoration: none !important;
        white-space: nowrap;
        transition: all 0.15s ease;
    }
    .iam-pill:hover {
        background: rgba(99, 102, 241, 0.1);
        border-color: rgba(99, 102, 241, 0.35);
        color: #cbd5e1;
    }
    .iam-pill.active {
        background: rgba(99, 102, 241, 0.18);
        border-color: rgba(99, 102, 241, 0.5);
        color: #a5b4fc;
    }
    .iam-pill-badge {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.7rem;
        background: rgba(148, 163, 184, 0.12);
        padding: 1px 5px;
        border-radius: 4px;
    }
    .iam-pill.active .iam-pill-badge {
        background: rgba(99, 102, 241, 0.3);
        color: #e0e7ff;
    }

    /* 4. Main Directory Card */
    .iam-table-card {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 16px;
        overflow: hidden;
    }

    /* High-density Desktop Table */
    .iam-table {
        width: 100%;
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
        color: var(--color-ink-0, #f8fafc);
    }
    .iam-table thead th {
        background: rgba(15, 23, 42, 0.85);
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--color-ink-2, #94a3b8);
        padding: 14px 18px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.14);
        white-space: nowrap;
    }
    .iam-table tbody tr {
        border-bottom: 1px solid rgba(148, 163, 184, 0.08);
        transition: background-color 0.15s ease;
    }
    .iam-table tbody tr:last-child {
        border-bottom: none;
    }
    .iam-table tbody tr:hover {
        background: rgba(99, 102, 241, 0.04);
    }
    .iam-table tbody td {
        padding: 14px 18px;
        font-size: 0.88rem;
        vertical-align: middle;
        border-bottom: 1px solid rgba(148, 163, 184, 0.08);
    }

    /* User Avatar & Identity */
    .iam-user-ident {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .iam-avatar-circle {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.88rem;
        letter-spacing: 0.02em;
        flex-shrink: 0;
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }
    .iam-user-name {
        font-weight: 600;
        color: var(--color-ink-0, #f8fafc);
        line-height: 1.25;
    }
    .iam-user-email {
        font-size: 0.76rem;
        color: var(--color-ink-2, #94a3b8);
        margin-top: 2px;
    }

    /* Username Mono Chip with 1-Click Copy */
    .iam-username-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(15, 23, 42, 0.7);
        border: 1px solid rgba(148, 163, 184, 0.22);
        padding: 4px 9px;
        border-radius: 7px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.78rem;
        color: #f472b6;
        cursor: pointer;
        transition: all 0.15s ease;
        user-select: none;
    }
    .iam-username-chip:hover {
        background: rgba(244, 114, 182, 0.12);
        border-color: rgba(244, 114, 182, 0.4);
    }
    .iam-username-chip i {
        font-size: 0.72rem;
        opacity: 0.6;
        transition: opacity 0.15s ease;
    }
    .iam-username-chip:hover i {
        opacity: 1;
    }

    /* Role Badges */
    .iam-role-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.74rem;
        font-weight: 600;
        letter-spacing: 0.02em;
    }
    .iam-role-admin {
        background: rgba(245, 158, 11, 0.15);
        border: 1px solid rgba(245, 158, 11, 0.35);
        color: #fbbf24;
    }
    .iam-role-user {
        background: rgba(59, 130, 246, 0.12);
        border: 1px solid rgba(59, 130, 246, 0.3);
        color: #60a5fa;
    }
    .iam-role-other {
        background: rgba(148, 163, 184, 0.12);
        border: 1px solid rgba(148, 163, 184, 0.25);
        color: #cbd5e1;
    }

    /* Verification Badges */
    .iam-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 600;
    }
    .iam-status-verified {
        background: rgba(16, 185, 129, 0.12);
        border: 1px solid rgba(16, 185, 129, 0.28);
        color: #34d399;
    }
    .iam-status-unverified {
        background: rgba(148, 163, 184, 0.1);
        border: 1px solid rgba(148, 163, 184, 0.2);
        color: #94a3b8;
    }

    /* Action Buttons */
    .iam-actions-group {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
    }
    .btn-iam-action {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.84rem;
        transition: all 0.15s ease;
        text-decoration: none !important;
        border: 1px solid transparent;
        cursor: pointer;
    }
    .btn-iam-edit {
        background: rgba(245, 158, 11, 0.12);
        border-color: rgba(245, 158, 11, 0.28);
        color: #fbbf24;
    }
    .btn-iam-edit:hover {
        background: rgba(245, 158, 11, 0.22);
        border-color: rgba(245, 158, 11, 0.5);
        color: #fef08a;
    }
    .btn-iam-delete {
        background: rgba(239, 68, 68, 0.12);
        border-color: rgba(239, 68, 68, 0.28);
        color: #f87171;
    }
    .btn-iam-delete:hover {
        background: rgba(239, 68, 68, 0.22);
        border-color: rgba(239, 68, 68, 0.5);
        color: #fca5a5;
    }
    .btn-iam-disabled {
        background: rgba(148, 163, 184, 0.08);
        border-color: rgba(148, 163, 184, 0.15);
        color: #64748b;
        cursor: not-allowed;
    }

    /* 5. Mobile Tactile Feed Cards */
    .iam-mobile-list {
        display: none;
        flex-direction: column;
        gap: 10px;
        padding: 12px;
    }
    .iam-m-card {
        background: rgba(15, 23, 42, 0.7);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 12px;
        padding: 14px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .iam-m-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }
    .iam-m-ident {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }
    .iam-m-name {
        font-weight: 700;
        font-size: 0.92rem;
        color: var(--color-ink-0, #f8fafc);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .iam-m-email {
        font-size: 0.76rem;
        color: var(--color-ink-2, #94a3b8);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .iam-m-mid {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 8px 0;
        border-top: 1px dashed rgba(148, 163, 184, 0.12);
        border-bottom: 1px dashed rgba(148, 163, 184, 0.12);
        flex-wrap: wrap;
    }
    .iam-m-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        margin-top: 2px;
    }
    .iam-m-actions .btn {
        flex: 1;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 0.82rem;
        font-weight: 600;
        border-radius: 8px;
    }

    /* 6. Toast Notification */
    .iam-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: rgba(15, 23, 42, 0.95);
        border: 1px solid rgba(99, 102, 241, 0.4);
        color: #ffffff;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 0.84rem;
        font-weight: 600;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 9999;
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
    }
    .iam-toast.show {
        transform: translateY(0);
        opacity: 1;
    }
    .iam-toast-icon {
        color: #34d399;
        font-size: 1rem;
    }

    /* Responsive Breakpoints */
    @media (max-width: 991.98px) {
        .iam-bento-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 767.98px) {
        .iam-header-wrapper {
            flex-direction: column;
            align-items: stretch;
        }
        .iam-header-actions {
            width: 100%;
        }
        .btn-iam-primary {
            width: 100%;
            justify-content: center;
        }
        .iam-bento-grid {
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .iam-bento-tile {
            padding: 12px 14px;
        }
        .iam-bento-val {
            font-size: 1.35rem;
        }
        .iam-table-desktop {
            display: none !important;
        }
        .iam-mobile-list {
            display: flex;
        }
        .iam-toolbar-card {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
            padding: 12px 14px !important;
        }
        .iam-search-box {
            flex: none !important;
            width: 100% !important;
            max-width: 100% !important;
            height: 44px !important;
        }
        .iam-search-input {
            height: 100% !important;
            font-size: 16px !important;
        }
        .iam-pills-bar {
            width: 100% !important;
            overflow-x: auto !important;
            padding-bottom: 2px !important;
            margin-top: 0 !important;
        }
        .iam-workbench {
            padding-bottom: 0 !important;
            margin-bottom: 0 !important;
            gap: 12px !important;
        }
        .iam-table-card {
            margin-bottom: 0 !important;
        }
    }
</style>

<div class="iam-workbench">
    <!-- 1. Header Command Banner -->
    <div class="iam-header-card">
        <div class="iam-header-wrapper">
            <div class="iam-header-left">
                <div class="iam-header-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <h1 class="iam-header-title">{{ __('messages.user_management') }}</h1>
                    <div class="iam-header-meta">
                        <span class="iam-badge-live">
                            <span class="iam-badge-pulse"></span>
                            IAM ACTIVE DIRECTORY
                        </span>
                        <span>&bull;</span>
                        <span>{{ __('messages.user_subtitle') }}</span>
                    </div>
                </div>
            </div>
            @can('action_manage_settings')
            <div class="iam-header-actions">
                <a href="{{ route('users.create') }}" class="btn-iam-primary">
                    <i class="fas fa-user-plus"></i>
                    <span>{{ __('messages.add_user') }}</span>
                </a>
            </div>
            @endcan
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; border-radius: 12px; margin: 0;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; border-radius: 12px; margin: 0;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
        </div>
    @endif

    <!-- 2. Bento KPI Matrix -->
    <div class="iam-bento-grid">
        <!-- Tile 1: Total Accounts -->
        <div class="iam-bento-tile">
            <div class="iam-bento-top">
                <span class="iam-bento-label">{{ __('messages.total_login_accounts') }}</span>
                <i class="fas fa-users iam-bento-icon"></i>
            </div>
            <div class="iam-bento-val">{{ $totalUsersCount ?? count($users) }}</div>
            <div class="iam-bento-sub">
                <span>{{ __('messages.manage_users') }}</span>
            </div>
        </div>

        <!-- Tile 2: Active Roles Distribution -->
        <div class="iam-bento-tile">
            <div class="iam-bento-top">
                <span class="iam-bento-label">{{ __('messages.active_roles') }}</span>
                <i class="fas fa-user-tag iam-bento-icon"></i>
            </div>
            <div class="iam-bento-val">{{ isset($rolesWithCount) ? $rolesWithCount->count() : count($roles) }}</div>
            <div class="iam-bento-sub">
                @if(isset($rolesWithCount) && $rolesWithCount->count() > 0)
                    @foreach($rolesWithCount as $r)
                        <span class="iam-role-pill-sm">{{ $r->name }}: {{ $r->users_count }}</span>
                    @endforeach
                @else
                    <span>{{ __('messages.all_roles') }}</span>
                @endif
            </div>
        </div>

        <!-- Tile 3: Verification Health -->
        <div class="iam-bento-tile">
            <div class="iam-bento-top">
                <span class="iam-bento-label">{{ __('messages.verified_accounts') }}</span>
                <i class="fas fa-check-double iam-bento-icon"></i>
            </div>
            @php
                $tot = $totalUsersCount ?? count($users);
                $ver = $verifiedUsersCount ?? 0;
                $pct = $tot > 0 ? round(($ver / $tot) * 100) : 100;
            @endphp
            <div class="iam-bento-val">{{ $ver }} <small style="font-size: 0.9rem; color: #94a3b8; font-weight: normal;">/ {{ $tot }}</small></div>
            <div class="iam-progress-bar">
                <div class="iam-progress-fill" style="width: {{ $pct }}%;"></div>
            </div>
        </div>

        <!-- Tile 4: Active Session -->
        <div class="iam-bento-tile">
            <div class="iam-bento-top">
                <span class="iam-bento-label">{{ __('messages.current_user_tag') }}</span>
                <i class="fas fa-fingerprint iam-bento-icon"></i>
            </div>
            <div class="iam-bento-val" style="font-size: 1.15rem; word-break: break-all;">
                {{ auth()->user()->username ?? auth()->user()->name }}
            </div>
            <div class="iam-bento-sub">
                <span class="iam-role-badge iam-role-admin" style="padding: 2px 7px; font-size: 0.68rem;">
                    {{ auth()->user()->roles->pluck('name')->first() ?? 'Admin' }}
                </span>
            </div>
        </div>
    </div>

    <!-- 3. Toolbar & Role Segmented Pills -->
    <div class="iam-toolbar-card">
        <!-- Live Instant Search -->
        <div class="iam-search-box">
            <i class="fas fa-search iam-search-icon"></i>
            <input type="text" id="iamSearchInput" class="iam-search-input" placeholder="{{ __('messages.search_users_placeholder') }}" value="{{ request('search') }}" autocomplete="off">
            @if(request('search'))
                <a href="{{ route('users.index', array_filter(['role_id' => request('role_id')])) }}" class="iam-search-clear" title="{{ __('messages.clear') }}">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </div>

        <!-- Segmented Role Filter Pills -->
        <div class="iam-pills-bar">
            <a href="{{ route('users.index', array_filter(['search' => request('search')])) }}" class="iam-pill {{ !request('role_id') ? 'active' : '' }}">
                <span>{{ __('messages.all_roles') }}</span>
                <span class="iam-pill-badge">{{ $totalUsersCount ?? count($users) }}</span>
            </a>
            @if(isset($rolesWithCount))
                @foreach($rolesWithCount as $roleItem)
                    <a href="{{ route('users.index', array_filter(['role_id' => $roleItem->id, 'search' => request('search')])) }}" class="iam-pill {{ request('role_id') == $roleItem->id ? 'active' : '' }}">
                        <span>{{ $roleItem->name }}</span>
                        <span class="iam-pill-badge">{{ $roleItem->users_count }}</span>
                    </a>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Helper for Deterministic Monogram Colors -->
    @php
        $avatarThemes = [
            ['bg' => 'rgba(99, 102, 241, 0.2)', 'border' => 'rgba(99, 102, 241, 0.4)', 'color' => '#a5b4fc'],
            ['bg' => 'rgba(6, 182, 212, 0.2)', 'border' => 'rgba(6, 182, 212, 0.4)', 'color' => '#67e8f9'],
            ['bg' => 'rgba(16, 185, 129, 0.2)', 'border' => 'rgba(16, 185, 129, 0.4)', 'color' => '#6ee7b7'],
            ['bg' => 'rgba(245, 158, 11, 0.2)', 'border' => 'rgba(245, 158, 11, 0.4)', 'color' => '#fcd34d'],
            ['bg' => 'rgba(244, 114, 182, 0.2)', 'border' => 'rgba(244, 114, 182, 0.4)', 'color' => '#f9a8d4'],
            ['bg' => 'rgba(168, 85, 247, 0.2)', 'border' => 'rgba(168, 85, 247, 0.4)', 'color' => '#d8b4fe'],
        ];
    @endphp

    <!-- 4. Directory Container -->
    <div class="iam-table-card">
        <!-- Desktop High-Density Table -->
        <div class="iam-table-desktop table-responsive">
            <table class="table iam-table">
                <thead>
                    <tr>
                        <th width="50" class="text-center">#</th>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.username') }}</th>
                        <th>{{ __('messages.role') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th width="120" class="text-right pr-4">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody id="iamTableBody">
                    @forelse($users as $index => $user)
                        @php
                            $colorIndex = abs(crc32($user->username ?? $user->name)) % count($avatarThemes);
                            $avatarStyle = $avatarThemes[$colorIndex];
                            $initials = strtoupper(substr($user->name, 0, 2));
                            $roleName = $user->roles->pluck('name')->first() ?? 'User';
                            $isAdmin = str_contains(strtolower($roleName), 'admin');
                        @endphp
                        <tr class="iam-user-row" data-search="{{ strtolower($user->name . ' ' . $user->username . ' ' . $user->email . ' ' . $roleName) }}">
                            <td class="text-center text-muted font-weight-bold" style="font-family: 'JetBrains Mono', monospace; font-size: 0.8rem;">
                                {{ $index + 1 }}
                            </td>
                            <td>
                                <div class="iam-user-ident">
                                    <div class="iam-avatar-circle" style="background: {{ $avatarStyle['bg'] }}; border-color: {{ $avatarStyle['border'] }}; color: {{ $avatarStyle['color'] }};">
                                        {{ $initials }}
                                    </div>
                                    <div>
                                        <div class="iam-user-name">{{ $user->name }}</div>
                                        <div class="iam-user-email">
                                            <i class="far fa-envelope mr-1"></i> {{ $user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="iam-username-chip" onclick="copyToClipboard('{{ $user->username }}', '{{ __('messages.username_copied') }}')" title="{{ __('messages.copy_username') }}">
                                    <code>{{ $user->username }}</code>
                                    <i class="far fa-copy"></i>
                                </div>
                            </td>
                            <td>
                                <span class="iam-role-badge {{ $isAdmin ? 'iam-role-admin' : 'iam-role-user' }}">
                                    <i class="fas {{ $isAdmin ? 'fa-shield-alt' : 'fa-user' }}"></i>
                                    {{ $roleName }}
                                </span>
                            </td>
                            <td>
                                @if($user->email_verified_at)
                                    <span class="iam-status-pill iam-status-verified">
                                        <i class="fas fa-check-circle"></i>
                                        {{ __('messages.verified') }}
                                    </span>
                                @else
                                    <span class="iam-status-pill iam-status-unverified">
                                        <i class="far fa-clock"></i>
                                        {{ __('messages.unverified') }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-right pr-4">
                                <div class="iam-actions-group">
                                    @can('action_manage_settings')
                                        <a href="{{ route('users.edit', array_merge([$user->id], request()->query())) }}" class="btn-iam-action btn-iam-edit" title="{{ __('messages.edit') }}">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                    @endcan
                                    @if(auth()->id() !== $user->id)
                                        @can('action_manage_settings')
                                            <form action="{{ route('users.destroy', array_merge([$user->id], request()->query())) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn-iam-action btn-iam-delete btn-delete" data-confirm-message="{{ __('messages.delete_user_confirm') }}" title="{{ __('messages.delete') }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    @else
                                        <span class="btn-iam-action btn-iam-disabled" title="{{ __('messages.cannot_delete_self') }}">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="iamNoDataRow">
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div style="font-size: 2rem; opacity: 0.4; margin-bottom: 8px;"><i class="fas fa-users-slash"></i></div>
                                <div>{{ __('messages.no_data') }}</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Tactile Feed Cards -->
        <div class="iam-mobile-list" id="iamMobileList">
            @forelse($users as $index => $user)
                @php
                    $colorIndex = abs(crc32($user->username ?? $user->name)) % count($avatarThemes);
                    $avatarStyle = $avatarThemes[$colorIndex];
                    $initials = strtoupper(substr($user->name, 0, 2));
                    $roleName = $user->roles->pluck('name')->first() ?? 'User';
                    $isAdmin = str_contains(strtolower($roleName), 'admin');
                @endphp
                <div class="iam-m-card iam-user-row" data-search="{{ strtolower($user->name . ' ' . $user->username . ' ' . $user->email . ' ' . $roleName) }}">
                    <div class="iam-m-top">
                        <div class="iam-m-ident">
                            <div class="iam-avatar-circle" style="background: {{ $avatarStyle['bg'] }}; border-color: {{ $avatarStyle['border'] }}; color: {{ $avatarStyle['color'] }};">
                                {{ $initials }}
                            </div>
                            <div style="min-width: 0;">
                                <div class="iam-m-name">{{ $user->name }}</div>
                                <div class="iam-m-email">{{ $user->email }}</div>
                            </div>
                        </div>
                        <span class="iam-role-badge {{ $isAdmin ? 'iam-role-admin' : 'iam-role-user' }}">
                            {{ $roleName }}
                        </span>
                    </div>

                    <div class="iam-m-mid">
                        <div class="iam-username-chip" onclick="copyToClipboard('{{ $user->username }}', '{{ __('messages.username_copied') }}')">
                            <code>{{ $user->username }}</code>
                            <i class="far fa-copy"></i>
                        </div>
                        @if($user->email_verified_at)
                            <span class="iam-status-pill iam-status-verified">
                                <i class="fas fa-check-circle"></i> {{ __('messages.verified') }}
                            </span>
                        @else
                            <span class="iam-status-pill iam-status-unverified">
                                <i class="far fa-clock"></i> {{ __('messages.unverified') }}
                            </span>
                        @endif
                    </div>

                    <div class="iam-m-actions">
                        @can('action_manage_settings')
                            <a href="{{ route('users.edit', array_merge([$user->id], request()->query())) }}" class="btn btn-iam-edit">
                                <i class="fas fa-pen mr-1"></i> {{ __('messages.edit') }}
                            </a>
                        @endcan
                        @if(auth()->id() !== $user->id)
                            @can('action_manage_settings')
                                <form action="{{ route('users.destroy', array_merge([$user->id], request()->query())) }}" method="POST" style="flex: 1;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-iam-delete btn-delete" data-confirm-message="{{ __('messages.delete_user_confirm') }}" style="width: 100%;">
                                        <i class="fas fa-trash-alt mr-1"></i> {{ __('messages.delete') }}
                                    </button>
                                </form>
                            @endcan
                        @else
                            <button class="btn btn-iam-disabled" disabled style="opacity: 0.6;">
                                <i class="fas fa-lock mr-1"></i> {{ __('messages.current_user_tag') }}
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <div style="font-size: 2rem; opacity: 0.4; margin-bottom: 8px;"><i class="fas fa-users-slash"></i></div>
                    <div>{{ __('messages.no_data') }}</div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Copy Toast Notification -->
<div id="iamToast" class="iam-toast">
    <i class="fas fa-check-circle iam-toast-icon"></i>
    <span id="iamToastMessage">Copied</span>
</div>

@push('scripts')
<script>
    // 1-Click Copy Utility
    function copyToClipboard(text, message) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text);
        } else {
            const ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
        }
        showToast(message);
    }

    // Toast Trigger
    function showToast(message) {
        const toast = document.getElementById('iamToast');
        const msgSpan = document.getElementById('iamToastMessage');
        msgSpan.textContent = message;
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 2200);
    }

    // Live Client-side Instant Filter
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('iamSearchInput');
        if (!searchInput) return;

        searchInput.addEventListener('input', function (e) {
            const term = e.target.value.toLowerCase().trim();
            const rows = document.querySelectorAll('.iam-user-row');

            rows.forEach(row => {
                const searchData = row.getAttribute('data-search') || '';
                if (term === '' || searchData.includes(term)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // If user hits enter on search, submit server-side GET query
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const url = new URL(window.location.href);
                if (this.value.trim() !== '') {
                    url.searchParams.set('search', this.value.trim());
                } else {
                    url.searchParams.delete('search');
                }
                window.location.href = url.toString();
            }
        });
    });
</script>
@endpush
@endsection