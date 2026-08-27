@extends('layouts.admin')

@section('title', __('messages.roles_permissions'))

@section('content')
<style>
    .content-header {
        display: none !important;
    }

    .roles-card {
        border-radius: 16px !important;
        overflow: hidden !important;
        border: var(--rule-soft);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    .custom-modal-input {
        height: 44px !important;
        border-radius: 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.22) !important;
        box-shadow: none !important;
        font-size: 0.9rem !important;
        color: var(--color-ink-0) !important;
        transition: all 0.2s ease !important;
    }
    .custom-modal-input:focus {
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2) !important;
        background: rgba(15, 23, 42, 0.35) !important;
    }
    .modal-content {
        border-radius: 16px !important;
        overflow: hidden !important;
        background: var(--color-paper-0) !important;
        border: var(--rule-soft) !important;
    }

    @media (max-width: 767.98px) {
        .roles-toolbar-box {
            width: 100%;
        }
        .roles-toolbar-box .btn {
            width: 100% !important;
            justify-content: center !important;
            height: 44px !important;
        }
        .modal-footer-actions {
            flex-direction: column-reverse;
            width: 100%;
            gap: 10px !important;
        }
        .modal-footer-actions .btn {
            width: 100% !important;
            justify-content: center !important;
            height: 44px !important;
        }
    }

    /* Light Mode Overrides */
    body.light-mode .roles-table thead th {
        color: #475569 !important;
        background: #f8fafc !important;
        border-bottom: 1px solid #e2e8f0 !important;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.75rem;
    }
    body.light-mode .roles-table td {
        border-bottom: 1px solid #e2e8f0 !important;
        color: #334155 !important;
    }
    body.light-mode .theme-text {
        color: #1e293b !important;
    }
    body.light-mode .text-muted {
        color: #64748b !important;
    }
    body.light-mode .btn-add-role {
        background-color: #3b82f6 !important;
        border-color: #3b82f6 !important;
        color: #ffffff !important;
    }
    body.light-mode .roles-table .badge-secondary {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        border: 1px solid #e2e8f0 !important;
    }
    body.light-mode .roles-table .badge-success {
        background-color: #dcfce7 !important;
        color: #15803d !important;
        border: 1px solid #bbf7d0 !important;
    }
    body.light-mode .roles-table .badge-info {
        background-color: #e0f2fe !important;
        color: #0369a1 !important;
        border: 1px solid #bae6fd !important;
    }
    body.light-mode .roles-table .btn-outline-info {
        background: #e0f2fe !important;
        border-color: #bae6fd !important;
        color: #0284c7 !important;
    }
    body.light-mode .roles-table .btn-outline-danger {
        background: #fee2e2 !important;
        border-color: #fecaca !important;
        color: #dc2626 !important;
    }
    body.light-mode .avatar-sm {
        background: #eef2ff !important;
        border-color: #e0e7ff !important;
    }
    body.light-mode .avatar-sm i {
        color: #4f46e5 !important;
    }
    body.light-mode .modal-content {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1) !important;
    }
    body.light-mode .custom-modal-input {
        background: #f8fafc !important;
        border: 1px solid #cbd5e1 !important;
        color: #1e293b !important;
    }
    body.light-mode .custom-modal-input:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
        background: #ffffff !important;
    }
    body.light-mode .modal-header {
        border-bottom: 1px solid #e2e8f0 !important;
        background: #f8fafc !important;
    }
    body.light-mode .modal-header .modal-title {
        color: #1e293b !important;
    }
    body.light-mode .modal-footer {
        border-top: 1px solid #e2e8f0 !important;
        background: #f8fafc !important;
    }
</style>

<!-- Header Toolbar -->
<div class="row mb-3 roles-toolbar-box" style="margin-top: 1rem !important;">
    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center" style="gap: 10px;">
            <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(99, 102, 241, 0.12); border: 1px solid rgba(99, 102, 241, 0.3); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 1.1rem; flex-shrink: 0;">
                <i class="fas fa-user-tag"></i>
            </div>
            <div>
                <h5 class="mb-0 font-weight-bold theme-text" style="font-size: 1.08rem; line-height: 1.2;">{{ __('messages.roles_permissions') }}</h5>
                <small class="text-muted d-block" style="font-size: 0.78rem;">{{ __('messages.roles_desc') }}</small>
            </div>
        </div>
        @can('action_manage_settings')
        <button type="button" class="btn btn-primary btn-add-role px-4" style="height: 42px; border-radius: 14px !important; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;" data-toggle="modal" data-target="#addRoleModal">
            {{ __('messages.add_role') }}
        </button>
        @endcan
    </div>
</div>

<!-- Roles Table Card -->
<div class="card theme-card roles-card">
    <div class="card-body p-0">
        <div class="theme-scroll-container table-responsive">
            <table class="table table-striped table-hover m-0 theme-table roles-table align-middle">
                <thead>
                    <tr>
                        <th class="py-3 pl-4">{{ __('messages.role') }}</th>
                        <th class="py-3 px-4">{{ __('messages.user_count') }}</th>
                        <th class="py-3 px-4">{{ __('messages.total_permissions') }}</th>
                        <th width="120" class="py-3 pr-4 text-center">{{ __('messages.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr>
                        <td class="py-3 pl-4">
                            <div class="d-flex align-items-center" style="gap: 10px;">
                                <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(99, 102, 241, 0.12); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.25);">
                                    <i class="fas {{ $role->name === 'Super Admin' ? 'fa-user-astronaut' : ($role->name === 'Admin' ? 'fa-user-shield' : 'fa-user-tag') }}"></i>
                                </div>
                                <div>
                                    <div class="theme-text font-weight-bold" style="font-size: 0.95rem;">{{ $role->name }}</div>
                                    @if(in_array($role->name, ['Super Admin', 'Admin', 'User']))
                                        <small class="text-muted" style="font-size: 0.72rem;">{{ __('messages.system_default_role') }}</small>
                                    @else
                                        <small class="text-muted" style="font-size: 0.72rem;">{{ __('messages.custom_role') }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="badge badge-secondary px-3 py-1" style="font-size: 0.8rem; font-weight: 500; border-radius: 10px;">
                                <i class="fas fa-users mr-1"></i> {{ $role->users_count }} {{ $role->users_count == 1 ? __('messages.user_badge_count') : __('messages.users_badge_count') }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            @if($role->name === 'Super Admin')
                                <span class="badge badge-success px-3 py-1" style="font-size: 0.8rem; font-weight: 600; border-radius: 10px;">
                                    <i class="fas fa-check-circle mr-1"></i> {{ __('messages.full_access_100') }}
                                </span>
                            @else
                                <span class="badge badge-info px-3 py-1" style="font-size: 0.8rem; font-weight: 600; border-radius: 10px;">
                                    <i class="fas fa-key mr-1"></i> {{ $role->permissions_count }} {{ __('messages.permissions') }}
                                </span>
                            @endif
                        </td>
                        <td class="py-3 pr-4 text-center">
                            @if($role->name === 'Super Admin')
                                <span class="text-muted" title="{{ App::getLocale() == 'en' ? 'Cannot be modified' : 'Tidak dapat diubah' }}" aria-label="Not Editable"><i class="fas fa-times"></i></span>
                            @elseif(!auth()->user()->can('action_manage_settings'))
                                <span class="text-muted" title="{{ App::getLocale() == 'en' ? 'Cannot be modified' : 'Tidak dapat diubah' }}" aria-label="Not Editable"><i class="fas fa-times"></i></span>
                            @else
                                <div class="d-flex justify-content-center" style="gap: 8px;">
                                    <a href="{{ route('roles.edit', $role->id) }}" class="btn action-btn btn-outline-info" style="border: 1px solid rgba(23, 162, 184, 0.3); background: rgba(23, 162, 184, 0.15); color: #17a2b8;" title="{{ __('messages.manage_permissions') }}">
                                        <i class="fas fa-user-shield"></i>
                                    </a>

                                    @if(!in_array($role->name, ['Super Admin', 'Admin', 'User']))
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-delete action-btn btn-outline-danger" style="border: 1px solid rgba(220, 53, 69, 0.3); background: rgba(220, 53, 69, 0.15); color: #dc3545;" title="{{ __('messages.delete') }}" data-confirm-message="{{ App::getLocale() == 'en' ? 'Are you sure you want to delete role' : 'Apakah Anda yakin ingin menghapus peran' }} '{{ $role->name }}'?">
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
</div>

<!-- Modal Tambah Peran -->
<div class="modal fade theme-modal" id="addRoleModal" tabindex="-1" role="dialog" aria-labelledby="addRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('roles.store') }}" method="POST" style="width: 100%;">
            @csrf
            <div class="modal-content">
                <div class="modal-header py-3 px-4" style="border-color: rgba(148, 163, 184, 0.12) !important;">
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(99, 102, 241, 0.12); border: 1px solid rgba(99, 102, 241, 0.3); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 1rem;">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h5 class="modal-title font-weight-bold theme-text" id="addRoleModalLabel" style="font-size: 1.05rem;">{{ __('messages.add_role') }}</h5>
                    </div>
                    <button type="button" class="close theme-close" data-dismiss="modal" aria-label="Close" style="color: var(--color-ink-0); opacity: 0.7;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-0">
                        <label for="name" class="theme-text font-weight-bold mb-2" style="font-size: 0.88rem;">{{ __('messages.role_name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control theme-input custom-modal-input" id="name" name="name" required placeholder="{{ __('messages.role_name_placeholder') }}" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4 d-flex align-items-center justify-content-end modal-footer-actions" style="border-color: rgba(148, 163, 184, 0.12) !important; gap: 10px;">
                    <button type="button" class="btn btn-outline-secondary px-4" style="border-radius: 14px !important; height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;" data-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary btn-add-role px-4" style="border-radius: 14px !important; height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">{{ __('messages.save') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
