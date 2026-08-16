@extends('layouts.admin')

@section('title', __('messages.software_licenses'))

@section('content')
<div class="row mb-3">
    <div class="col-sm-6">
        <form action="{{ route('software_licenses.index') }}" method="GET">
            <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
            <div class="position-relative" style="width: 250px; max-width: 100%; flex: 1; min-width: 200px;">
                <input type="text" name="search" class="form-control theme-input" placeholder="{{ __('messages.search_license') }}" value="{{ request('search') }}" style="width: 100%; padding-right: 75px; border-radius: 30px; height: 40px; box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);">
                <div class="position-absolute d-flex align-items-center" style="top: 50%; right: 5px; transform: translateY(-50%); gap: 4px;">
                    @if(request('search'))
                        <a href="{{ route('software_licenses.index') }}" class="text-muted" style="display: flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; text-decoration: none;"><i class="fas fa-times"></i></a>
                    @endif
                    <button class="btn btn-info rounded-circle" type="submit" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border: none; box-shadow: 0 2px 6px rgba(23,162,184,0.4);"><i class="fas fa-search text-xs"></i></button>
                </div>
            </div>
            
        </div>
    </form>
    </div>
    <div class="col-sm-6 text-right">
        <a href="{{ route('software_licenses.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> {{ __('messages.add_license') }}
        </a>
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
                            <div class="d-flex align-items-center" style="gap: 6px;">
                                <code style="user-select: all; font-size: 0.85rem;">{{ Str::limit($lic->license_key, 25) ?? '-' }}</code>
                                @if($lic->license_key)
                                    <button type="button" class="btn btn-xs btn-outline-success copy-license-btn" style="padding: 1px 5px; line-height: 1;" title="{{ __('messages.copy_license') }}" data-license-key="{{ $lic->license_key }}">
                                        <i class="far fa-copy"></i>
                                    </button>
                                @endif
                            </div>
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
                                <a href="{{ route('software_licenses.edit', array_merge([$lic->id], request()->query())) }}" class="btn action-btn btn-outline-warning" style="border: 1px solid rgba(255, 193, 7, 0.3); background: rgba(255, 193, 7, 0.15); color: #ffc107;" title="{{ __('messages.edit') }}"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('software_licenses.destroy', array_merge([$lic->id], request()->query())) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-delete action-btn btn-outline-danger" style="border: 1px solid rgba(220, 53, 69, 0.3); background: rgba(220, 53, 69, 0.15); color: #dc3545;" title="{{ __('messages.delete') }}" data-confirm-message="{{ __('messages.confirm_delete') }}"><i class="fas fa-trash"></i></button>
                                </form>
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
        var key = $(this).data('license-key');
        var button = $(this);
        var icon = button.find('i');
        
        if (!key) return;

        navigator.clipboard.writeText(key).then(function() {
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
                    title: "{{ __('messages.copy_license_success') ?? 'License key copied!' }}",
                    background: 'rgba(30, 41, 59, 0.95)',
                    color: '#f8f9fa'
                });
            }
        }, function(err) {
            console.error('Could not copy license key: ', err);
        });
    });
});
</script>
@endpush
