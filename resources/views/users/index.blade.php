@extends('layouts.admin')

@section('title', __('messages.manage_users'))

@section('content')
<div class="row mb-3">
    <div class="col-12 mb-3">
        <form action="{{ route('users.index') }}" method="GET">
            <div class="d-flex flex-wrap align-items-center" style="gap: 10px;">
            <div class="position-relative" style="width: 250px; max-width: 100%; flex: 1; min-width: 200px;">
                <input type="text" name="search" class="form-control theme-input" placeholder="{{ __('messages.search_user') }}" value="{{ request('search') }}" style="width: 100%; padding-right: 75px; border-radius: 30px; height: 40px; box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);">
                <div class="position-absolute d-flex align-items-center" style="top: 50%; right: 5px; transform: translateY(-50%); gap: 4px;">
                    @if(request('search'))
                        <a href="{{ route('users.index') }}" class="text-muted" style="display: flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; text-decoration: none;"><i class="fas fa-times"></i></a>
                    @endif
                    <button class="btn btn-info rounded-circle" type="submit" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border: none; box-shadow: 0 2px 6px rgba(23,162,184,0.4);"><i class="fas fa-search text-xs"></i></button>
                </div>
            </div>
            
        </div>
    </form>
    </div>
    <div class="col-12 text-right">
        @can('action_manage_settings')
<a href="{{ route('users.create') }}" class="btn btn-sm btn-primary" >
            <i class="fas fa-user-plus"></i> {{ __('messages.add_user') }}
        </a>
@endcan
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible" style="background: rgba(40,167,69,0.2); border: 1px solid rgba(40,167,69,0.5); color: #28a745;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <i class="icon fas fa-check"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible" style="background: rgba(220,53,69,0.2); border: 1px solid rgba(220,53,69,0.5); color: #dc3545;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <i class="icon fas fa-ban"></i> {{ session('error') }}
    </div>
@endif

<div class="card theme-card">
    <div class="card-body p-0">
        <div class="theme-scroll-container table-responsive">
            <table class="table table-striped table-hover m-0 theme-table">
                <thead>
                    <tr>
                        <th width="50">{{ __('messages.no') }}</th>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.username') }}</th>
                        <th>{{ __('messages.email') }}</th>
                        <th>{{ __('messages.role') }}</th>
                        <th>Anydesk Password</th>
                        <th>PC Password</th>
                        <th width="150" class="text-center">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="theme-text">{{ $loop->iteration }}</td>
                        <td class="theme-text">{{ $user->name }}</td>
                        <td class="theme-text">{{ $user->username }}</td>
                        <td class="theme-text">{{ $user->email }}</td>
                        <td class="theme-text">
                            @foreach($user->roles as $role)
                                <span class="badge badge-info" style="box-shadow: 0 0 8px rgba(23,162,184,0.5);">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td class="theme-text">{{ $user->anydesk_password ?? '-' }}</td>
                        <td class="theme-text">{{ $user->pc_password ?? '-' }}</td>
                        <td class="theme-text">
                            <div class="d-flex justify-content-center" style="gap: 8px;">
                                @can('action_manage_settings')
<a href="{{ route('users.edit', array_merge([$user->id], request()->query())) }}" class="btn action-btn btn-outline-warning" style="border: 1px solid rgba(255, 193, 7, 0.3); background: rgba(255, 193, 7, 0.15); color: #ffc107;" title="{{ __('messages.edit') }}"><i class="fas fa-edit"></i></a>
@endcan
                                @if(auth()->id() !== $user->id)
                                @can('action_manage_settings')
<form action="{{ route('users.destroy', array_merge([$user->id], request()->query())) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-delete action-btn btn-outline-danger" style="border: 1px solid rgba(220, 53, 69, 0.3); background: rgba(220, 53, 69, 0.15); color: #dc3545;" title="{{ __('messages.delete') }}" data-confirm-message="{{ __('messages.confirm_delete') }}"><i class="fas fa-trash"></i></button>
                                </form>
@endcan
                                @else
                                <div class="d-inline">
                                    <button class="btn action-btn" style="visibility: hidden;"><i class="fas fa-trash"></i></button>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">{{ __('messages.no_data') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
