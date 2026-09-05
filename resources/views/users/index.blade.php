@extends('layouts.admin')

@section('title', __('messages.manage_users'))

@section('content')
<style>
    .content-header {
        display: none !important;
    }

    .user-card {
        border-radius: 16px !important;
        overflow: hidden !important;
        border: var(--rule-soft);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    .custom-search-input {
        height: 44px !important;
        border-radius: 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.22) !important;
        box-shadow: none !important;
        font-size: 0.9rem !important;
        color: var(--color-ink-0) !important;
        transition: background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease !important;
    }
    .custom-search-input:focus {
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2) !important;
        background: rgba(15, 23, 42, 0.35) !important;
    }
    .user-search-input-box {
        width: 100%;
        display: flex;
        gap: 8px;
    }

    @media (max-width: 767.98px) {
        .user-toolbar-box {
            width: 100% !important;
        }
        .user-toolbar-box > .col-12 {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
        }
        .user-action-buttons,
        .header-action-buttons {
            width: 100% !important;
            margin-left: 0 !important;
        }
        .user-toolbar-box .btn {
            width: 100% !important;
            justify-content: center !important;
            height: 44px !important;
        }
        .user-table {
            min-width: 650px !important;
        }
    }
</style>

<!-- Toolbar Header -->
<div class="row mb-3 user-toolbar-box" style="margin-top: 0 !important;">
    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center" style="gap: 10px;">
            <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(99, 102, 241, 0.12); border: 1px solid rgba(99, 102, 241, 0.3); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 1.1rem; flex-shrink: 0;">
                <i class="fas fa-users-cog"></i>
            </div>
            <div>
                <h5 class="mb-0 font-weight-bold theme-text" style="font-size: 1.08rem; line-height: 1.2;">{{ __('messages.user_management') }}</h5>
                <small class="text-muted d-block" style="font-size: 0.78rem;">{{ __('messages.user_subtitle') }}</small>
            </div>
        </div>
        @can('action_manage_settings')
        <div class="d-flex align-items-center flex-wrap user-action-buttons header-action-buttons ml-auto" style="gap: 8px;">
            <a href="{{ route('users.create') }}" class="btn btn-action-primary px-4 btn-add-main">
                {{ __('messages.add_user') }}
            </a>
        </div>
        @endcan
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; border-radius: 12px;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; border-radius: 12px;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {{ session('error') }}
    </div>
@endif

<!-- Main Table Card -->
<div class="card theme-card user-card">
    <!-- Clean Search Bar -->
    <div class="division-table-header user-table-header card-header border-0 d-flex align-items-center flex-wrap">
        <form action="{{ route('users.index') }}" method="GET" style="width: 100%;">
            <div class="sleek-search-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" class="form-control theme-input sleek-search-input" placeholder="{{ __('messages.search_users_placeholder') }}" value="{{ request('search') }}" autocomplete="off">
                @if(request('search'))
                    <a href="{{ route('users.index') }}" class="clear-search-btn" title="{{ __('messages.clear') }}">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="theme-scroll-container table-responsive">
            <table class="table table-striped table-hover m-0 theme-table user-table">
                <thead>
                    <tr>
                        <th width="60" class="pl-4">{{ __('messages.no') }}</th>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.username') }}</th>
                        <th>{{ __('messages.email') }}</th>
                        <th>{{ __('messages.role') }}</th>
                        <th width="120" class="text-center pr-4">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="theme-text pl-4">{{ (method_exists($users, 'firstItem') && $users->firstItem() ? $users->firstItem() + $loop->index : $loop->iteration) }}</td>
                        <td class="font-weight-bold theme-text">{{ $user->name }}</td>
                        <td class="theme-text"><code style="color: #f472b6;">{{ $user->username }}</code></td>
                        <td class="theme-text">{{ $user->email }}</td>
                        <td class="theme-text">
                            @foreach($user->roles as $role)
                                <span class="badge badge-info" style="box-shadow: 0 0 8px rgba(59, 130, 246, 0.3); border-radius: 10px; padding: 5px 10px; font-weight: 600;">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td class="theme-text pr-4">
                            <div class="d-flex justify-content-center" style="gap: 8px;">
                                @can('action_manage_settings')
                                <a href="{{ route('users.edit', array_merge([$user->id], request()->query())) }}" class="btn action-btn btn-outline-warning" style="border: 1px solid rgba(255, 193, 7, 0.3); background: rgba(255, 193, 7, 0.15); color: #ffc107;" title="{{ __('messages.edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @if(auth()->id() !== $user->id)
                                    @can('action_manage_settings')
                                    <form action="{{ route('users.destroy', array_merge([$user->id], request()->query())) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-delete action-btn btn-outline-danger" style="border: 1px solid rgba(220, 53, 69, 0.3); background: rgba(220, 53, 69, 0.15); color: #dc3545;" title="{{ __('messages.delete') }}" data-confirm-message="{{ __('messages.confirm_delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
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
                        <td colspan="6" class="text-center text-muted py-4">{{ __('messages.no_data') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
