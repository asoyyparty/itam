@extends('layouts.admin')

@section('title', __('messages.vlan_management'))

@section('content')
<div class="row mb-3">
    <div class="col-sm-6">
        <form action="{{ route('vlans.index') }}" method="GET">
            <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
            <div class="position-relative" style="width: 250px; max-width: 100%; flex: 1; min-width: 200px;">
                <input type="text" name="search" class="form-control theme-input" placeholder="{{ __('messages.search_vlan') }}" value="{{ request('search') }}" style="width: 100%; padding-right: 75px; border-radius: 30px; height: 40px; box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);">
                <div class="position-absolute d-flex align-items-center" style="top: 50%; right: 5px; transform: translateY(-50%); gap: 4px;">
                    @if(request('search'))
                        <a href="{{ route('vlans.index') }}" class="text-muted" style="display: flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; text-decoration: none;"><i class="fas fa-times"></i></a>
                    @endif
                    <button class="btn btn-info rounded-circle" type="submit" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border: none; box-shadow: 0 2px 6px rgba(23,162,184,0.4);"><i class="fas fa-search text-xs"></i></button>
                </div>
            </div>
            
        </div>
    </form>
    </div>
    <div class="col-sm-6 text-right">
        @can('action_manage_network')
<a href="{{ route('vlans.create') }}" class="btn btn-sm btn-primary">
            {{ __('messages.add_vlan') }}
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
                        <th>{{ __('messages.vlan_number') }}</th>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.subnet') }}</th>
                        <th>{{ __('messages.gateway') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.notes') }}</th>
                        <th width="150" class="text-center">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vlans as $vlan)
                    <tr>
                        <td class="theme-text">{{ $loop->iteration }}</td>
                        <td class="text-info font-weight-bold">{{ $vlan->vlan_number }}</td>
                        <td class="theme-text">{{ $vlan->name }}</td>
                        <td class="theme-text">{{ $vlan->subnet ?? '-' }}</td>
                        <td class="theme-text">{{ $vlan->gateway ?? '-' }}</td>
                        <td class="theme-text">
                            <div class="dropdown">
                                @can('action_manage_network')
<button class="btn btn-sm dropdown-toggle status-btn p-0 border-0 bg-transparent" type="button" data-toggle="dropdown" aria-expanded="false" data-id="{{ $vlan->id }}" style="box-shadow: none;">
                                    @if($vlan->status == 'Active')
                                        <span class="badge badge-success status-badge" style="box-shadow: 0 0 8px rgba(40,167,69,0.5);">{{ __('messages.active') }}</span>
                                    @else
                                        <span class="badge badge-danger status-badge" style="box-shadow: 0 0 8px rgba(220,53,69,0.5);">{{ __('messages.inactive') }}</span>
                                    @endif
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" >
                                    <a class="dropdown-item status-change-btn text-success" href="#" data-status="Active">{{ __('messages.active') }}</a>
                                    <a class="dropdown-item status-change-btn text-danger" href="#" data-status="Inactive">{{ __('messages.inactive') }}</a>
                                </div>
@else
<button class="btn btn-sm dropdown-toggle status-btn p-0 border-0 bg-transparent" type="button" data-toggle="dropdown" aria-expanded="false" data-id="{{ $vlan->id }}" style="box-shadow: none;">
                                    @if($vlan->status == 'Active')
                                        <span class="badge badge-success status-badge" style="box-shadow: 0 0 8px rgba(40,167,69,0.5);">{{ __('messages.active') }}</span>
                                    @else
                                        <span class="badge badge-danger status-badge" style="box-shadow: 0 0 8px rgba(220,53,69,0.5);">{{ __('messages.inactive') }}</span>
                                    @endif
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" >
                                    <a class="dropdown-item status-change-btn text-success" href="#" data-status="Active">{{ __('messages.active') }}</a>
                                    <a class="dropdown-item status-change-btn text-danger" href="#" data-status="Inactive">{{ __('messages.inactive') }}</a>
                                </div>
@endcan
                            </div>
                        </td>
                        <td class="theme-text">{{ Str::limit($vlan->notes, 30) }}</td>
                        <td class="theme-text">
                            <div class="d-flex justify-content-center" style="gap: 8px;">
                                @can('action_manage_network')
<a href="{{ route('vlans.edit', array_merge([$vlan->id], request()->query())) }}" class="btn action-btn btn-outline-warning" style="border: 1px solid rgba(255, 193, 7, 0.3); background: rgba(255, 193, 7, 0.15); color: #ffc107;" title="{{ __('messages.edit') }}"><i class="fas fa-edit"></i></a>
@endcan
                                @can('action_manage_network')
<form action="{{ route('vlans.destroy', array_merge([$vlan->id], request()->query())) }}" method="POST" class="d-inline">
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

@push('scripts')
<script>
$(document).ready(function() {
    $(document).on('click', '.status-change-btn', function(e) {
        e.preventDefault();
        var btn = $(this);
        var newStatus = btn.data('status');
        var container = btn.closest('.dropdown');
        var id = container.find('.status-btn').data('id');
        var badge = container.find('.status-badge');
        
        var originalHtml = badge.html();
        badge.html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: '{{ url("vlans") }}/' + id + '/status',
            type: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                if(response.success) {
                    badge.removeClass('badge-primary badge-warning badge-success badge-danger badge-secondary badge-info badge-dark');
                    badge.css('box-shadow', 'none');
                    
                    switch(newStatus) {
                        case 'Active':
                            badge.addClass('badge-success');
                            badge.css('box-shadow', '0 0 8px rgba(40,167,69,0.5)');
                            badge.text('{{ __("messages.active") }}');
                            break;
                        case 'Inactive':
                            badge.addClass('badge-danger');
                            badge.css('box-shadow', '0 0 8px rgba(220,53,69,0.5)');
                            badge.text('{{ __("messages.inactive") }}');
                            break;
                    }
                }
            },
            error: function(xhr) {
                Swal.fire({ icon: 'error', text: 'Error updating status.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, background: '#0f172a', color: '#f8fafc', customClass: { popup: 'border border-danger' } });
                badge.html(originalHtml);
            }
        });
    });
});
</script>
<style>
.status-btn::after {
    display: none !important;
}
.status-change-btn:hover {
    background-color: rgba(255, 255, 255, 0.1);
}
</style>
@endpush
@endsection
