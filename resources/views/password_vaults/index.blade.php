@extends('layouts.admin')

@section('title', __('messages.password_vault'))

@section('content')
<!-- Header Toolbar -->
<div class="row mb-3 password-vault-toolbar-box" style="margin-top: 0 !important;">
    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center" style="gap: 10px;">
            <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(99, 102, 241, 0.12); border: 1px solid rgba(99, 102, 241, 0.3); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 1.1rem; flex-shrink: 0;">
                <i class="fas fa-key"></i>
            </div>
            <div>
                <h5 class="mb-0 font-weight-bold theme-text" style="font-size: 1.08rem; line-height: 1.2;">{{ __('messages.password_vault') }}</h5>
                <small class="text-muted d-block" style="font-size: 0.78rem;">{{ __('messages.vault_subtitle') }}</small>
            </div>
        </div>
        @can('action_manage_settings')
        <div class="d-flex align-items-center flex-wrap vault-action-buttons header-action-buttons ml-auto" style="gap: 8px;">
            <a href="{{ route('password_vaults.create') }}" class="btn btn-action-primary px-4 btn-add-main">
                {{ __('messages.add_vault') }}
            </a>
        </div>
        @endcan
    </div>
</div>

<div class="card theme-card">
    <div class="division-table-header vault-table-header card-header border-0 d-flex align-items-center flex-wrap">
        <form action="{{ route('password_vaults.index') }}" method="GET" class="vault-search-form" style="width: 100%;">
            <div class="vault-search-controls d-flex align-items-center justify-content-start">
                <select name="category" class="form-control select2 theme-input vault-filter" onchange="this.form.submit()">
                    <option value="">{{ __('messages.all_category') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                <div class="sleek-search-wrapper vault-search-input" style="flex: 1; min-width: 0;">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" class="form-control theme-input sleek-search-input" placeholder="{{ __('messages.search_vault') }}" value="{{ request('search') }}" aria-label="{{ __('messages.search_vault') }}">
                    @if(request()->anyFilled(['search', 'category']))
                        <a href="{{ route('password_vaults.index') }}" class="clear-search-btn" title="Clear" aria-label="Clear">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="theme-scroll-container table-responsive">
            <table class="table table-striped table-hover m-0 theme-table division-table">
                <thead>
                    <tr>
                        <th width="50">{{ __('messages.no') }}</th>
                        <th>{{ __('messages.device_name') }}</th>
                        <th>{{ __('messages.username') }}</th>
                        <th>{{ __('messages.password') }}</th>
                        <th>{{ __('messages.category') }}</th>
                        <th>{{ __('messages.notes') }}</th>
                        <th width="180" class="text-center">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($passwords as $pwd)
                    <tr>
                        <td class="theme-text">{{ (method_exists($passwords, 'firstItem') && $passwords->firstItem() ? $passwords->firstItem() + $loop->index : $loop->iteration) }}</td>
                        <td class="text-info font-weight-bold">{{ $pwd->device_name }}</td>
                        <td class="theme-text"><code style="font-family: 'JetBrains Mono', monospace; font-size: 0.88rem; font-variant-numeric: tabular-nums; color: var(--color-info, #38bdf8);">{{ $pwd->username }}</code></td>
                        <td>
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <input type="password" value="{{ $pwd->encrypted_password }}" class="password-field border-0 bg-transparent theme-text" style="outline: none; width: 100px; font-family: 'JetBrains Mono', monospace; font-size: 0.88rem; font-variant-numeric: tabular-nums;" readonly>
                                <button type="button" class="btn btn-xs btn-outline-info toggle-password-btn" style="padding: 2px 6px;" title="{{ __('messages.toggle_password') ?? 'Toggle Password' }}"><i class="fas fa-eye"></i></button>
                                <button type="button" class="btn btn-xs btn-outline-success copy-password-btn" style="padding: 2px 6px;" title="{{ __('messages.copy') ?? 'Copy' }}" data-password="{{ $pwd->encrypted_password }}"><i class="fas fa-copy"></i></button>
                            </div>
                        </td>
                        <td class="theme-text">
                            <span class="badge badge-secondary" style="box-shadow: 0 0 5px rgba(255,255,255,0.1);">{{ $pwd->category }}</span>
                        </td>
                        <td class="theme-text">{{ Str::limit($pwd->notes, 30) }}</td>
                        <td class="theme-text">
                            <div class="d-flex justify-content-center" style="gap: 8px;">
                                @can('action_manage_settings')
<a href="{{ route('password_vaults.edit', array_merge([$pwd->id], request()->query())) }}" class="btn action-btn btn-outline-warning" style="border: 1px solid rgba(255, 193, 7, 0.3); background: rgba(255, 193, 7, 0.15); color: #ffc107;" title="{{ __('messages.edit') }}"><i class="fas fa-edit"></i></a>
@endcan
                                @can('action_manage_settings')
<form action="{{ route('password_vaults.destroy', array_merge([$pwd->id], request()->query())) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-delete action-btn btn-outline-danger" style="border: 1px solid rgba(220, 53, 69, 0.3); background: rgba(220, 53, 69, 0.15); color: #dc3545;" title="{{ __('messages.delete') }}" data-confirm-message="{{ __('messages.confirm_delete') }}"><i class="fas fa-trash"></i></button>
                                </form>
@endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted p-4">{{ __('messages.no_data') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle Password Visibility
    $(document).on('click', '.toggle-password-btn', function() {
        var input = $(this).siblings('.password-field');
        var icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Copy Password to Clipboard
    $(document).on('click', '.copy-password-btn', function() {
        var pwd = $(this).data('password');
        var button = $(this);
        var icon = button.find('i');
        
        copyToClipboard(pwd).then(function() {
            icon.removeClass('fa-copy').addClass('fa-check');
            button.removeClass('btn-outline-success').addClass('btn-success');
            
            setTimeout(function() {
                icon.removeClass('fa-check').addClass('fa-copy');
                button.removeClass('btn-success').addClass('btn-outline-success');
            }, 1500);
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    icon: 'success',
                    title: "{{ __('messages.copy_password_success') ?? 'Password copied!' }}",
                    background: 'rgba(30, 41, 59, 0.95)',
                    color: '#f8f9fa'
                });
            }
        }).catch(function(err) {
            console.error('Could not copy password: ', err);
        });
    });
});
</script>
@endpush
