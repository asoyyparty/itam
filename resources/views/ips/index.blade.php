@extends('layouts.admin')

@section('title', __('messages.ip_management'))

@section('content')
<div class="division-actions ip-actions d-flex justify-content-end align-items-center mb-3">
    <a href="{{ route('ips.export', request()->query()) }}" class="btn btn-sm btn-success" title="{{ __('messages.export') }}">
        <i class="fas fa-file-export mr-1"></i>{{ __('messages.export') }}
    </a>
    @can('action_manage_network')
    <a href="{{ route('ips.create') }}" class="btn btn-sm btn-primary" title="{{ __('messages.add_ip') }}">
        <i class="fas fa-plus mr-1"></i>{{ __('messages.add_ip') }}
    </a>
    @endcan
</div>

<div class="card theme-card ip-filter-card">
    <div class="division-table-header ip-table-header card-header border-0 d-flex align-items-center flex-wrap">
        <form action="{{ route('ips.index') }}" method="GET" class="ip-search-form">
            <div class="ip-search-controls d-flex align-items-center">
                <select name="status" class="form-control select2 theme-input ip-filter">
                    <option value="">{{ __('messages.all_status') }}</option>
                    <option value="Available" {{ request('status') == 'Available' ? 'selected' : '' }}>{{ __('messages.available') }}</option>
                    <option value="Used" {{ request('status') == 'Used' ? 'selected' : '' }}>{{ __('messages.used') }}</option>
                    <option value="Reserved" {{ request('status') == 'Reserved' ? 'selected' : '' }}>{{ __('messages.reserved') }}</option>
                </select>
                <select name="ping_status" class="form-control select2 theme-input ip-filter">
                    <option value="">{{ __('messages.all_ping_status') ?? 'All Ping Status' }}</option>
                    <option value="online" {{ request('ping_status') == 'online' ? 'selected' : '' }}>Online</option>
                    <option value="offline" {{ request('ping_status') == 'offline' ? 'selected' : '' }}>Offline</option>
                    <option value="unchecked" {{ request('ping_status') == 'unchecked' ? 'selected' : '' }}>Unchecked</option>
                </select>
                <select name="vlan_id" class="form-control select2 theme-input ip-filter">
                    <option value="">{{ __('messages.all_vlans') ?? 'Semua VLAN' }}</option>
                    @foreach($vlans as $vlan)
                        <option value="{{ $vlan->id }}" {{ request('vlan_id') == $vlan->id ? 'selected' : '' }}>
                            VLAN {{ $vlan->vlan_number }} - {{ $vlan->name }}
                        </option>
                    @endforeach
                </select>
                <div class="ip-search-input input-group">
                    <input type="text" name="search" class="form-control theme-input" placeholder="{{ __('messages.search_ip') }}" value="{{ request('search') }}" aria-label="{{ __('messages.search_ip') }}">
                    @if(request()->anyFilled(['search', 'status', 'vlan_id', 'ping_status']))
                        <a href="{{ route('ips.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" title="Clear" style="min-width: 40px; height: 38px; padding: 0; display: flex !important; align-items: center !important; justify-content: center !important; border-color: rgba(148, 163, 184, 0.22);"><i class="fas fa-times"></i></a>
                    @endif
                    <button class="btn btn-info d-flex align-items-center justify-content-center" type="submit" style="min-width: 40px; height: 38px; padding: 0; display: flex !important; align-items: center !important; justify-content: center !important;"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>

    <div class="card theme-card">
        <div class="card-body p-0">
            <div class="theme-scroll-container table-responsive">
                <table class="table table-striped table-hover m-0 theme-table division-table">
                    <thead>
                        <tr>
                            <th width="50">{{ __('messages.no') }}</th>
                            <th>{{ __('messages.ip_address') }}</th>
                            <th>{{ __('messages.assigned_asset') }}</th>
                            <th>{{ __('messages.vlans') }}</th>
                            <th>{{ __('messages.assigned_employee') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th width="180" class="text-center">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ips as $ip)
                            <tr class="ip-row" data-ip-id="{{ $ip->id }}">
                                <td class="theme-text">{{ $ips->firstItem() + $loop->index }}</td>
                                <td class="font-weight-bold">
                                    <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                                        <span><i class="fas fa-network-wired text-muted"></i> {{ $ip->ip_address }}</span>
                                        <span class="ping-status-badge-{{ $ip->id }}">
                                            @if($ip->is_online === true)
                                                <span class="badge badge-success px-2 py-1" style="box-shadow: 0 0 8px rgba(40,167,69,0.5); font-size: 0.7rem;" title="{{ $ip->last_ping_at ? 'Last ping: ' . $ip->last_ping_at->diffForHumans() : '' }}">
                                                    <i class="fas fa-circle text-xs mr-1"></i> Online
                                                </span>
                                            @elseif($ip->is_online === false)
                                                <span class="badge badge-danger px-2 py-1" style="box-shadow: 0 0 8px rgba(220,53,69,0.5); font-size: 0.7rem;" title="{{ $ip->last_ping_at ? 'Last ping: ' . $ip->last_ping_at->diffForHumans() : '' }}">
                                                    <i class="fas fa-circle text-xs mr-1"></i> Offline
                                                </span>
                                            @else
                                                <span class="badge badge-secondary px-2 py-1" style="opacity: 0.6; font-size: 0.7rem;" title="Unchecked">
                                                    <i class="far fa-circle text-xs mr-1"></i> Unchecked
                                                </span>
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td class="theme-text">
                                    @if($ip->asset)
                                        <a href="{{ route('assets.show', $ip->asset_id) }}"
                                            class="text-info font-weight-bold">{{ $ip->asset->name }}</a><br><small
                                            class="text-muted">{{ $ip->asset->asset_tag }}</small>
                                    @elseif($ip->notes)
                                        <span class="theme-text">{{ $ip->notes }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="theme-text">
                                    @if($ip->vlan)
                                        <span class="badge badge-info">VLAN {{ $ip->vlan->vlan_number }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="theme-text">
                                    @if($ip->employee)
                                        <a href="{{ route('employees.show', $ip->employee_id) }}"
                                            class="text-info font-weight-bold">{{ $ip->employee->name }}</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="theme-text">
                                    <div class="dropdown">
                                        @can('action_manage_network')
<button class="btn btn-sm dropdown-toggle status-btn p-0 border-0 bg-transparent" type="button" data-toggle="dropdown" aria-expanded="false" data-id="{{ $ip->id }}" style="box-shadow: none;">
                                            @if($ip->status == 'Available')
                                                <span class="badge badge-success status-badge" style="box-shadow: 0 0 8px rgba(40,167,69,0.5);">{{ __('messages.available') }}</span>
                                            @elseif($ip->status == 'Used')
                                                <span class="badge badge-primary status-badge" style="box-shadow: 0 0 8px rgba(0,123,255,0.5);">{{ __('messages.used') }}</span>
                                            @else
                                                <span class="badge badge-warning status-badge" style="box-shadow: 0 0 8px rgba(255,193,7,0.5);">{{ __('messages.reserved') }}</span>
                                            @endif
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" >
                                            <a class="dropdown-item status-change-btn text-success" href="#" data-status="Available">{{ __('messages.available') }}</a>
                                            <a class="dropdown-item status-change-btn text-primary" href="#" data-status="Used">{{ __('messages.used') }}</a>
                                            <a class="dropdown-item status-change-btn text-warning" href="#" data-status="Reserved">{{ __('messages.reserved') }}</a>
                                        </div>
@else
<button class="btn btn-sm dropdown-toggle status-btn p-0 border-0 bg-transparent" type="button" data-toggle="dropdown" aria-expanded="false" data-id="{{ $ip->id }}" style="box-shadow: none;">
                                            @if($ip->status == 'Available')
                                                <span class="badge badge-success status-badge" style="box-shadow: 0 0 8px rgba(40,167,69,0.5);">{{ __('messages.available') }}</span>
                                            @elseif($ip->status == 'Used')
                                                <span class="badge badge-primary status-badge" style="box-shadow: 0 0 8px rgba(0,123,255,0.5);">{{ __('messages.used') }}</span>
                                            @else
                                                <span class="badge badge-warning status-badge" style="box-shadow: 0 0 8px rgba(255,193,7,0.5);">{{ __('messages.reserved') }}</span>
                                            @endif
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" >
                                            <a class="dropdown-item status-change-btn text-success" href="#" data-status="Available">{{ __('messages.available') }}</a>
                                            <a class="dropdown-item status-change-btn text-primary" href="#" data-status="Used">{{ __('messages.used') }}</a>
                                            <a class="dropdown-item status-change-btn text-warning" href="#" data-status="Reserved">{{ __('messages.reserved') }}</a>
                                        </div>
@endcan
                                    </div>
                                </td>
                                <td class="theme-text">
                                    <div class="d-flex justify-content-center" style="gap: 8px;">
                                        <button type="button" class="btn action-btn btn-outline-success ping-btn"
                                            data-id="{{ $ip->id }}"
                                            data-ping-url="{{ route('ips.ping', $ip) }}" title="{{ __('messages.ping_device') }}"
                                            style="border: 1px solid rgba(40, 167, 69, 0.3); background: rgba(40, 167, 69, 0.15); color: #28a745;"><i
                                                class="fas fa-play"></i></button>
                                        @can('action_manage_network')
<a href="{{ route('ips.edit', array_merge([$ip->id], request()->query())) }}" class="btn action-btn btn-outline-warning"
                                            style="border: 1px solid rgba(255, 193, 7, 0.3); background: rgba(255, 193, 7, 0.15); color: #ffc107;"
                                            title="{{ __('messages.edit') }}"><i class="fas fa-edit"></i></a>
@endcan
                                        @can('action_manage_network')
<form action="{{ route('ips.destroy', array_merge([$ip->id], request()->query())) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-delete action-btn btn-outline-danger"
                                                style="border: 1px solid rgba(220, 53, 69, 0.3); background: rgba(220, 53, 69, 0.15); color: #dc3545;"
                                                title="{{ __('messages.delete') }}"
                                                data-confirm-message="{{ __('messages.confirm_delete') }}"><i
                                                    class="fas fa-trash"></i></button>
                                        </form>
@endcan
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
        @if($ips->hasPages())
            <div class="pagination-footer card-footer d-flex justify-content-end align-items-center flex-wrap py-2" style="background: transparent; border-top: 1px solid rgba(255,255,255,0.08); gap: 10px;">
                {{ $ips->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $(document).on('click', '.ping-btn', function () {
                var button = $(this);
                var ipId = button.data('id');
                var icon = button.find('i');
                var originalClass = icon.attr('class');
                var pingUrl = button.data('ping-url');
                var statusContainer = $('.ping-status-badge-' + ipId);

                // Show spinner
                icon.attr('class', 'fas fa-spinner fa-spin');
                button.prop('disabled', true);
                statusContainer.html('<span class="badge badge-info px-2 py-1" style="font-size: 0.7rem;"><i class="fas fa-spinner fa-spin text-xs mr-1"></i> Pinging...</span>');

                $.ajax({
                    url: pingUrl,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        button.prop('disabled', false);
                        if (response.online) {
                            icon.attr('class', 'fas fa-check-circle');
                            button.css({
                                'background': 'rgba(40, 167, 69, 0.4)',
                                'color': '#28a745',
                                'border-color': '#28a745'
                            });
                            statusContainer.html('<span class="badge badge-success px-2 py-1" style="box-shadow: 0 0 8px rgba(40,167,69,0.5); font-size: 0.7rem;" title="Just now"><i class="fas fa-circle text-xs mr-1"></i> Online</span>');
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                icon: 'success',
                                title: "{{ __('messages.ping_online') ?? 'Ping Successful! Device is Online.' }}"
                            });
                        } else {
                            icon.attr('class', 'fas fa-times-circle');
                            button.css({
                                'background': 'rgba(220, 53, 69, 0.4)',
                                'color': '#dc3545',
                                'border-color': '#dc3545'
                            });
                            statusContainer.html('<span class="badge badge-danger px-2 py-1" style="box-shadow: 0 0 8px rgba(220,53,69,0.5); font-size: 0.7rem;" title="Just now"><i class="fas fa-circle text-xs mr-1"></i> Offline</span>');
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                icon: 'error',
                                title: "{{ __('messages.ping_offline') ?? 'Ping Failed! Device is Offline.' }}"
                            });
                        }
                        setTimeout(function () {
                            icon.attr('class', originalClass);
                            button.removeAttr('style');
                        }, 4000);
                    },
                    error: function (xhr) {
                        button.prop('disabled', false);
                        icon.attr('class', 'fas fa-exclamation-triangle');
                        button.css({
                            'background': 'rgba(255, 193, 7, 0.4)',
                            'color': '#ffc107',
                            'border-color': '#ffc107'
                        });
                        setTimeout(function () {
                            icon.attr('class', originalClass);
                            button.removeAttr('style');
                        }, 3000);

                        var msg = "{{ __('messages.ping_error') ?? 'Error executing ping command.' }}";
                        if (xhr.status === 419) {
                            msg = "Sesi halaman kedaluwarsa (419 CSRF). Silakan refresh halaman.";
                        } else if (xhr.status === 403) {
                            msg = "Akses ditolak (403 Forbidden).";
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000,
                            icon: 'warning',
                            title: msg
                        });
                    }
                });
            });

            // Helper to update individual IP status badge in real-time
            function updateIpBadge(ipId, isOnline, lastPingAt) {
                var statusContainer = $('.ping-status-badge-' + ipId);
                if (!statusContainer.length) return;

                var titleAttr = lastPingAt ? 'Last ping: ' + lastPingAt : 'Just now';
                var newBadgeHtml = '';

                if (isOnline === true) {
                    newBadgeHtml = '<span class="badge badge-success px-2 py-1" style="box-shadow: 0 0 8px rgba(40,167,69,0.5); font-size: 0.7rem;" title="' + titleAttr + '">' +
                                   '<i class="fas fa-circle text-xs mr-1"></i> Online</span>';
                } else if (isOnline === false) {
                    newBadgeHtml = '<span class="badge badge-danger px-2 py-1" style="box-shadow: 0 0 8px rgba(220,53,69,0.5); font-size: 0.7rem;" title="' + titleAttr + '">' +
                                   '<i class="fas fa-circle text-xs mr-1"></i> Offline</span>';
                } else {
                    newBadgeHtml = '<span class="badge badge-secondary px-2 py-1" style="opacity: 0.6; font-size: 0.7rem;" title="Unchecked">' +
                                   '<i class="far fa-circle text-xs mr-1"></i> Unchecked</span>';
                }

                statusContainer.html(newBadgeHtml);
            }



            // Live status polling every 15s for automatic background status updates
            setInterval(function() {
                var visibleIds = [];
                $('.ip-row').each(function() {
                    visibleIds.push($(this).data('ip-id'));
                });

                if (visibleIds.length > 0) {
                    $.ajax({
                        url: '{{ route("ips.live-status") }}',
                        method: 'GET',
                        data: { ip_ids: visibleIds },
                        success: function(res) {
                            if (res.success && res.ips) {
                                res.ips.forEach(function(item) {
                                    updateIpBadge(item.id, item.online, item.last_ping_at);
                                });
                            }
                        }
                    });
                }
            }, 15000);

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
                    url: '{{ url("ips") }}/' + id + '/status',
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
                                case 'Available':
                                    badge.addClass('badge-success');
                                    badge.css('box-shadow', '0 0 8px rgba(40,167,69,0.5)');
                                    badge.text('{{ __("messages.available") }}');
                                    break;
                                case 'Used':
                                    badge.addClass('badge-primary');
                                    badge.css('box-shadow', '0 0 8px rgba(0,123,255,0.5)');
                                    badge.text('{{ __("messages.used") }}');
                                    break;
                                case 'Reserved':
                                    badge.addClass('badge-warning');
                                    badge.css('box-shadow', '0 0 8px rgba(255,193,7,0.5)');
                                    badge.text('{{ __("messages.reserved") }}');
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
    .status-btn::after { display: none !important; }
    .status-change-btn:hover { background-color: rgba(255, 255, 255, 0.1); }

    .ip-actions { gap: 10px !important; }
    .ip-actions .btn {
        min-height: 42px;
        padding: 0.7rem 1.1rem;
        border-radius: 12px !important;
        font-weight: 600;
    }
    .ip-search-form, .ip-search-controls { width: 100%; }
    .ip-search-controls { gap: 10px; flex-wrap: nowrap; }
    .ip-search-input { min-width: 220px; flex: 1 1 auto; gap: 8px; }
    .ip-search-input .form-control, .ip-filter {
        height: 42px !important;
        border-radius: 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.22);
        background: rgba(15, 23, 42, 0.22);
        box-shadow: none !important;
    }
    .ip-search-input .form-control { min-width: 0; flex: 1 1 auto; }
    .ip-search-input .btn { height: 42px; min-width: 42px; border-radius: 10px !important; }
    .ip-filter { width: 170px; min-width: 140px; flex: 0 1 170px; }

    @media (max-width: 767.98px) {
        .ip-search-controls { flex-wrap: wrap; }
        .ip-search-input, .ip-filter {
            width: 100% !important; min-width: 0 !important; flex-basis: 100%;
        }
        .ip-search-input { display: flex !important; flex-wrap: nowrap; }
        .ip-search-input .form-control { width: auto !important; }
    }
    </style>
@endpush