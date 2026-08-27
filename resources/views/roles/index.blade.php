@extends('layouts.admin')

@section('title', __('messages.roles_permissions'))

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <p class="theme-text text-muted mb-0">{{ __('messages.roles_desc') }}</p>
    </div>
    <div class="col-md-4 text-md-right mt-3 mt-md-0">
        @can('action_manage_settings')
        <button type="button" class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#addRoleModal" style="border-radius: var(--radius-md);">
            <i class="fas fa-plus mr-1"></i> {{ __('messages.add_role') }}
        </button>
        @endcan
    </div>
</div>

<div class="card theme-card shadow-sm" style="border-radius: var(--radius-lg); overflow: hidden;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table theme-table roles-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="py-3 px-4">{{ __('messages.role') }}</th>
                        <th class="py-3 px-4">Jumlah Pengguna</th>
                        <th class="py-3 px-4">{{ __('messages.total_permissions') }}</th>
                        <th width="100" class="py-3 px-2 text-center">{{ __('messages.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr>
                        <td class="py-3 px-4">
                            <div class="d-flex align-items-center" style="gap: 10px;">
                                <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(99, 102, 241, 0.12); color: #6366f1;">
                                    <i class="fas {{ $role->name === 'Super Admin' ? 'fa-user-astronaut' : ($role->name === 'Admin' ? 'fa-user-shield' : 'fa-user-tag') }}"></i>
                                </div>
                                <div>
                                    <div class="theme-text font-weight-bold" style="font-size: 0.95rem;">{{ $role->name }}</div>
                                    @if(in_array($role->name, ['Super Admin', 'Admin', 'User']))
                                        <small class="text-muted" style="font-size: 0.72rem;">Peran Bawaan Sistem</small>
                                    @else
                                        <small class="text-muted" style="font-size: 0.72rem;">Peran Kustom</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="badge badge-secondary px-3 py-1" style="font-size: 0.8rem; font-weight: 500;">
                                <i class="fas fa-users mr-1"></i> {{ $role->users_count }} Pengguna
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            @if($role->name === 'Super Admin')
                                <span class="badge badge-success px-3 py-1" style="font-size: 0.8rem; font-weight: 600;">
                                    <i class="fas fa-check-circle mr-1"></i> Akses Penuh (100%)
                                </span>
                            @else
                                <span class="badge badge-info px-3 py-1" style="font-size: 0.8rem; font-weight: 600;">
                                    <i class="fas fa-key mr-1"></i> {{ $role->permissions_count }} Hak Akses
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-2 text-center">
                            @if($role->name === 'Super Admin')
                                <span class="text-muted" title="Tidak Dapat Diubah" aria-label="Tidak Dapat Diubah"><i class="fas fa-times"></i></span>
                            @elseif(!auth()->user()->can('action_manage_settings'))
                                <span class="text-muted" title="Tidak Dapat Diubah" aria-label="Tidak Dapat Diubah"><i class="fas fa-times"></i></span>
                            @else
                                <div class="d-inline-flex align-items-center" style="gap: 4px;">
                                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-outline-primary" title="{{ __('messages.manage_permissions') }}" aria-label="{{ __('messages.manage_permissions') }}" style="border-radius: var(--radius-md); font-weight: 500;">
                                        <i class="fas fa-user-shield"></i>
                                    </a>

                                    @if(!in_array($role->name, ['Super Admin', 'Admin', 'User']))
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-delete btn-outline-danger" title="{{ __('messages.delete') }}" aria-label="{{ __('messages.delete') }}" data-confirm-message="Apakah Anda yakin ingin menghapus peran '{{ $role->name }}'?" style="border-radius: var(--radius-md);">
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
<div class="modal fade" id="addRoleModal" tabindex="-1" role="dialog" aria-labelledby="addRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content theme-card" style="border-radius: var(--radius-xl);">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title theme-text font-weight-bold d-flex align-items-center" id="addRoleModalLabel" style="gap: 8px;">
                    <i class="fas fa-user-plus text-primary"></i> {{ __('messages.add_role') }}
                </h5>
                <button type="button" class="close theme-text" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="form-group mb-0">
                        <label for="name" class="theme-text font-weight-bold mb-2">{{ __('messages.role_name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control theme-input py-2" id="name" name="name" required placeholder="Contoh: Teknisi IT, Support Staff, Auditor..." style="font-size: 0.92rem; border-radius: var(--radius-md);">
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-dismiss="modal" style="border-radius: var(--radius-md);">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary px-4 font-weight-bold" style="border-radius: var(--radius-md);">
                        <i class="fas fa-save mr-1"></i> {{ __('messages.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
