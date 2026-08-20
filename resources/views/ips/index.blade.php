@extends('layouts.admin')

@section('title', __('messages.ip_management'))

@section('content')
    <div class="row mb-3">
        <div class="col-12 mb-3">
            <form action="{{ route('ips.index') }}" method="GET">
                <div class="d-flex flex-wrap align-items-center" style="gap: 10px;">
            <div class="position-relative" style="width: 250px; max-width: 100%; flex: 1; min-width: 200px;">
                <input type="text" name="search" class="form-control theme-input" placeholder="{{ __('messages.search_ip') }}" value="{{ request('search') }}" style="width: 100%; padding-right: 75px; border-radius: 30px; height: 40px; box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);">
                <div class="position-absolute d-flex align-items-center" style="top: 50%; right: 5px; transform: translateY(-50%); gap: 4px;">
                    @if(request()->anyFilled(['search', 'status', 'vlan_id', 'ping_status']))
                        <a href="{{ route('ips.index') }}" class="text-muted" style="display: flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; text-decoration: none;"><i class="fas fa-times"></i></a>
                    @endif
                    <button class="btn btn-info rounded-circle" type="submit" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border: none; box-shadow: 0 2px 6px rgba(23,162,184,0.4);"><i class="fas fa-search text-xs"></i></button>
                </div>
            </div>
                    <select name="status" class="form-control select2 theme-input" style="width: 150px;">
                        <option value="" >{{ __('messages.all_status') }}</option>
                        <option value="Available"  {{ request('status') == 'Available' ? 'selected' : '' }}>{{ __('messages.available') }}</option>
                        <option value="Used"  {{ request('status') == 'Used' ? 'selected' : '' }}>
                            {{ __('messages.used') }}</option>
                        <option value="Reserved"  {{ request('status') == 'Reserved' ? 'selected' : '' }}>
                            {{ __('messages.reserved') }}</option>
                    </select>
                    <select name="ping_status" class="form-control select2 theme-input" style="width: 165px;">
                        <option value="">{{ __('messages.all_ping_status') }}</option>
                        <option value="online" {{ request('ping_status') == 'online' ? 'selected' : '' }}>Online</option>
                        <option value="offline" {{ request('ping_status') == 'offline' ? 'selected' : '' }}>Offline</option>
                        <option value="unchecked" {{ request('ping_status') == 'unchecked' ? 'selected' : '' }}>Unchecked</option>
                    </select>
                    <select name="vlan_id" class="form-control select2 theme-input" style="width: 180px;">
                        <option value="">{{ __('messages.all_vlans') ?? 'Semua VLAN' }}</option>
                        @foreach($vlans as $vlan)
                            <option value="{{ $vlan->id }}" {{ request('vlan_id') == $vlan->id ? 'selected' : '' }}>
                                VLAN {{ $vlan->vlan_number }} - {{ $vlan->name }}
                            </option>
                        @endforeach
                    </select>
                
        </div>
    </form>
        </div>

        <div class="col-12 d-flex justify-content-end align-items-center flex-wrap" style="gap: 10px;">
            <a href="{{ route('ips.export', request()->query()) }}" class="btn btn-sm btn-success">
                {{ __('messages.export') }}</a>
            @can('action_manage_network')
                <button type="button" id="pingAllUsedBtn" class="btn btn-sm btn-info font-weight-bold" style="box-shadow: 0 2px 6px rgba(23,162,184,0.4);">
                    <i class="fas fa-network-wired mr-1"></i> {{ __('messages.ping_all_used') }}
                </button>
                <a href="{{ route('ips.create') }}" class="btn btn-sm btn-primary">
                    {{ __('messages.add_ip') }}</a>
            @endcan
        </div>
    </div>

    <div class="card theme-card">
        <div class="card-body p-0">
            <div class="theme-scroll-container table-responsive">
                <table class="table table-hover mb-0 theme-table">
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
            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap py-2" style="background: transparent; border-top: 1px solid rgba(255,255,255,0.08); gap: 10px;">
                <div class="text-muted small">
                    Showing {{ $ips->firstItem() ?? 0 }} to {{ $ips->lastItem() ?? 0 }} of {{ $ips->total() }} entries
                </div>
                <div>
                    {{ $ips->links() }}
                </div>
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

            // Real-time Batch Ping for All Used IPs
            $(document).on('click', '#pingAllUsedBtn', function () {
                var btn = $(this);
                var originalText = btn.html();

                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> {{ __("messages.ping_in_progress") ?? "Memindai Ping..." }}');

                $.ajax({
                    url: '{{ route("ips.ping-batch") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        get_used_ids: 1
                    },
                    success: function (res) {
                        var targetIds = res.ip_ids || [];

                        // Fallback to visible rows if no specific IDs returned
                        if (!targetIds.length) {
                            $('.ip-row').each(function () {
                                targetIds.push($(this).data('ip-id'));
                            });
                        }

                        if (!targetIds.length) {
                            btn.prop('disabled', false).html(originalText);
                            Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'Tidak ada IP Address Used untuk dipindai.', showConfirmButton: false, timer: 3000 });
                            return;
                        }

                        // Show temporary spinner in badges for target IPs
                        targetIds.forEach(function (id) {
                            $('.ping-status-badge-' + id).html('<span class="badge badge-info px-2 py-1" style="font-size: 0.7rem;"><i class="fas fa-spinner fa-spin text-xs mr-1"></i> Pinging...</span>');
                        });

                        var total = targetIds.length;
                        var processed = 0;
                        var onlineCount = 0;
                        var offlineCount = 0;
                        var chunkSize = 5; // Chunk size for fast concurrent requests

                        var chunks = [];
                        for (var i = 0; i < total; i += chunkSize) {
                            chunks.push(targetIds.slice(i, i + chunkSize));
                        }

                        function processChunk(index) {
                            if (index >= chunks.length) {
                                btn.prop('disabled', false).html(originalText);
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 4000,
                                    icon: 'success',
                                    title: '{{ __("messages.ping_batch_completed") ?? "Ping Batch Selesai!" }} ' + onlineCount + ' Online, ' + offlineCount + ' Offline.'
                                });
                                return;
                            }

                            btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Pinging (' + Math.min(processed, total) + '/' + total + ')...');

                            $.ajax({
                                url: '{{ route("ips.ping-batch") }}',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    ip_ids: chunks[index]
                                },
                                success: function (response) {
                                    if (response.success && response.results) {
                                        response.results.forEach(function (item) {
                                            processed++;
                                            if (item.online) onlineCount++; else offlineCount++;
                                            updateIpBadge(item.id, item.online, item.last_ping_at);
                                        });
                                    }
                                    processChunk(index + 1);
                                },
                                error: function () {
                                    processed += chunks[index].length;
                                    processChunk(index + 1);
                                }
                            });
                        }

                        processChunk(0);
                    },
                    error: function () {
                        btn.prop('disabled', false).html(originalText);
                        Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Gagal memulai batch ping.', showConfirmButton: false, timer: 3000 });
                    }
                });
            });

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
    </style>
@endpush