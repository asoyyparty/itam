@extends('layouts.admin')

@section('title', __('messages.manage_users'))

@section('content')
<div class="division-actions user-actions d-flex justify-content-end align-items-center mb-3">
    @can('action_manage_settings')
    <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary" title="{{ __('messages.add_user') }}"><i class="fas fa-user-plus mr-1"></i> {{ __('messages.add_user') }}</a>
    @endcan
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
    <div class="division-table-header user-table-header card-header border-0 d-flex align-items-center flex-wrap">
        <form action="{{ route('users.index') }}" method="GET" class="user-search-form" style="width: 100%;">
            <div class="user-search-controls d-flex align-items-center justify-content-start">
                <select name="role_id" class="form-control select2 theme-input user-filter">
                    <option value="">{{ __('messages.all_role') ?? 'Semua Role' }}</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
                <div class="user-search-input input-group">
                    <input type="text" name="search" class="form-control theme-input" placeholder="{{ __('messages.search_user') }}" value="{{ request('search') }}" aria-label="{{ __('messages.search_user') }}">
                    @if(request('search') || request('role_id'))
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" title="Clear" aria-label="Clear"  style="min-width: 40px; height: 38px; padding: 0; display: flex !important; align-items: center !important; justify-content: center !important; border-color: rgba(148, 163, 184, 0.22);"><i class="fas fa-times"></i></a>
                    @endif
                    <button class="btn btn-info d-flex align-items-center justify-content-center" type="submit" title="{{ __('messages.search') }}" aria-label="{{ __('messages.search') }}"  style="min-width: 40px; height: 38px; padding: 0; display: flex !important; align-items: center !important; justify-content: center !important;"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="theme-scroll-container table-responsive">
            <table class="table table-striped table-hover m-0 theme-table division-table user-table">
                <thead>
                    <tr>
                        <th class="division-no-column">{{ __('messages.no') }}</th>
                        <th class="division-name-column">{{ __('messages.name') }}</th>
                        <th>{{ __('messages.username') }}</th>
                        <th>{{ __('messages.email') }}</th>
                        <th>{{ __('messages.role') }}</th>
                        <th>Anydesk Password</th>
                        <th>PC Password</th>
                        <th class="division-action-column text-center">{{ __('messages.actions') }}</th>
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
                        <td class="theme-text"><span class="sensitive-value" data-value="{{ $user->anydesk_password ?? '-' }}">******</span> <button type="button" class="btn btn-sm btn-link p-0 sensitive-toggle" title="Show password" aria-label="Show password"><i class="fas fa-eye"></i></button></td>
                        <td class="theme-text"><span class="sensitive-value" data-value="{{ $user->pc_password ?? '-' }}">******</span> <button type="button" class="btn btn-sm btn-link p-0 sensitive-toggle" title="Show password" aria-label="Show password"><i class="fas fa-eye"></i></button></td>
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
<script>
document.addEventListener('click', function (event) {
    var toggle = event.target.closest('.sensitive-toggle');
    if (!toggle) return;
    var value = toggle.previousElementSibling;
    var isHidden = value.textContent === '******';
    value.textContent = isHidden ? value.dataset.value : '******';
    toggle.querySelector('i').className = isHidden ? 'fas fa-eye-slash' : 'fas fa-eye';
});
</script>
<style>
.user-actions { gap: 10px !important; }
.user-actions .btn {
    min-height: 42px;
    padding: 0.7rem 1.1rem;
    border-radius: 12px !important;
    font-weight: 600;
}
.user-search-form, .user-search-controls { width: 100%; }
.user-search-controls { gap: 10px; flex-wrap: nowrap; }
.user-search-input { min-width: 220px; flex: 1 1 auto; gap: 8px; }
.user-search-input .form-control, .user-filter {
    height: 42px !important;
    border-radius: 10px !important;
    border: 1px solid rgba(148, 163, 184, 0.22);
    background: rgba(15, 23, 42, 0.22);
    box-shadow: none !important;
}
.user-search-input .form-control { min-width: 0; flex: 1 1 auto; }
.user-search-input .btn { height: 42px; min-width: 42px; border-radius: 10px !important; }
.user-filter { width: 170px; min-width: 140px; flex: 0 1 170px; }

@media (max-width: 767.98px) {
    .user-search-controls { flex-wrap: wrap; }
    .user-search-input, .user-filter {
        width: 100% !important; min-width: 0 !important; flex-basis: 100%; max-width: 100%;
    }
    .user-search-input { display: flex !important; flex-wrap: nowrap; }
    .user-search-input .form-control { width: auto !important; }
}
</style>
@endsection
