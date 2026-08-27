@extends('layouts.admin')

@section('title', __('messages.software_licenses'))

@section('content')
<div class="division-actions license-actions d-flex justify-content-end align-items-center mb-3">
    @can('action_manage_settings')
    <a href="{{ route('software_licenses.create') }}" class="btn btn-sm btn-primary" title="{{ __('messages.add_license') }}">
        <i class="fas fa-plus mr-1"></i>{{ __('messages.add_license') }}
    </a>
    @endcan
</div>

<div class="card theme-card license-filter-card">
    <div class="division-table-header license-table-header card-header border-0 d-flex align-items-center flex-wrap">
        <form action="{{ route('software_licenses.index') }}" method="GET" class="license-search-form" style="width: 100%;">
            <div class="license-search-controls d-flex align-items-center justify-content-start">
                <div class="license-search-input input-group">
                    <input type="text" name="search" class="form-control theme-input" placeholder="{{ __('messages.search_license') }}" value="{{ request('search') }}" aria-label="{{ __('messages.search_license') }}">
                    @if(request('search'))
                        <a href="{{ route('software_licenses.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" title="Clear" style="min-width: 40px; height: 38px; padding: 0; display: flex !important; align-items: center !important; justify-content: center !important; border-color: rgba(148, 163, 184, 0.22);"><i class="fas fa-times"></i></a>
                    @endif
                    <button class="btn btn-info d-flex align-items-center justify-content-center" type="submit" style="min-width: 40px; height: 38px; padding: 0; display: flex !important; align-items: center !important; justify-content: center !important;"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>
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
            <table class="table table-striped table-hover m-0 theme-table division-table">
                <thead>
                    <tr>
                        <th width="50">{{ __('messages.no') }}</th>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.license_key') }}</th>
                        <th>{{ __('messages.expiry_date') }}</th>
                        <th>{{ __('messages.total_seats') }}</th>
                        <th>{{ __('messages.pic') }}</th>
                        <th>{{ __('messages.notes') }}</th>
                        <th width="150" class="text-center">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($software_licenses as $lic)
                    <tr>
                        <td class="theme-text">{{ $loop->iteration }}</td>
                        <td class="text-info font-weight-bold">{{ $lic->name }}</td>
                        <td class="theme-text">
                            @if($lic->license_key)
                                <div class="d-flex align-items-center" style="gap: 6px;">
                                    <code class="copy-license-btn" style="user-select: all; font-size: 0.85rem; cursor: pointer;" title="{{ __('messages.copy_license') }}: {{ $lic->license_key }}" data-license-key="{{ $lic->license_key }}">
                                        {{ Str::limit($lic->license_key, 25) }}
                                    </code>
                                    <button type="button" class="btn btn-xs btn-outline-success copy-license-btn" style="padding: 1px 5px; line-height: 1;" title="{{ __('messages.copy_license') }}" data-license-key="{{ $lic->license_key }}">
                                        <i class="far fa-copy"></i>
                                    </button>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="theme-text">
                            @if($lic->expiry_date)
                                {{ \Carbon\Carbon::parse($lic->expiry_date)->format('d M Y') }}
                            @else
                                <span class="text-muted">{{ __('messages.lifetime') }}</span>
                            @endif
                        </td>
                        <td class="theme-text">{{ $lic->total_seats }} {{ __('messages.seats') }}</td>
                        <td class="theme-text">{{ $lic->pic->name ?? '-' }}</td>
                        <td class="theme-text">{{ Str::limit($lic->notes, 35) }}</td>
                        <td class="theme-text">
                            <div class="d-flex justify-content-center" style="gap: 8px;">
                                @can('action_manage_settings')
                                <a href="{{ route('software_licenses.edit', array_merge([$lic->id], request()->query())) }}" class="btn action-btn btn-outline-warning" style="border: 1px solid rgba(255, 193, 7, 0.3); background: rgba(255, 193, 7, 0.15); color: #ffc107;" title="{{ __('messages.edit') }}"><i class="fas fa-edit"></i></a>
                                @endcan
                                @can('action_manage_settings')
                                <form action="{{ route('software_licenses.destroy', array_merge([$lic->id], request()->query())) }}" method="POST" class="d-inline">
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
                        <td colspan="8" class="text-center text-muted p-4">{{ __('messages.no_data') }}</td>
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
    $(document).on('click', '.copy-license-btn', function() {
        var key = $(this).data('license-key') || $(this).attr('data-license-key');
        var target = $(this);
        var container = target.closest('div');
        var button = container.find('button.copy-license-btn');
        var icon = button.find('i');
        
        if (!key) return;

        copyToClipboard(key).then(function() {
            if (icon.length) {
                icon.removeClass('fa-copy far').addClass('fa-check fas');
            }
            if (button.length) {
                button.removeClass('btn-outline-success').addClass('btn-success');
            }
            
            setTimeout(function() {
                if (icon.length) {
                    icon.removeClass('fa-check fas').addClass('fa-copy far');
                }
                if (button.length) {
                    button.removeClass('btn-success').addClass('btn-outline-success');
                }
            }, 1500);
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    icon: 'success',
                    title: "{{ __('messages.copy_license_success') ?? 'License key copied!' }}",
                    background: 'rgba(30, 41, 59, 0.95)',
                    color: '#f8f9fa'
                });
            }
        }).catch(function(err) {
            console.error('Could not copy license key: ', err);
        });
    });
});
</script>
<style>
.license-actions { gap: 10px !important; }
.license-actions .btn {
    min-height: 42px;
    padding: 0.7rem 1.1rem;
    border-radius: 12px !important;
    font-weight: 600;
}
.license-search-form, .license-search-controls { width: 100%; }
.license-search-controls { gap: 10px; flex-wrap: nowrap; }
.license-search-input { min-width: 220px; flex: 1 1 auto; max-width: 380px; gap: 8px; }
.license-search-input .form-control, .license-filter {
    height: 42px !important;
    border-radius: 10px !important;
    border: 1px solid rgba(148, 163, 184, 0.22);
    background: rgba(15, 23, 42, 0.22);
    box-shadow: none !important;
}
.license-search-input .form-control { min-width: 0; flex: 1 1 auto; }
.license-search-input .btn { height: 42px; min-width: 42px; border-radius: 10px !important; }
.license-filter { width: 180px; min-width: 150px; flex: 0 1 180px; }

@media (max-width: 767.98px) {
    .license-search-controls { flex-wrap: wrap; }
    .license-search-input, .license-filter {
        width: 100% !important; min-width: 0 !important; flex-basis: 100%; max-width: 100%;
    }
    .license-search-input { display: flex !important; flex-wrap: nowrap; }
    .license-search-input .form-control { width: auto !important; }
}
</style>
@endpush
