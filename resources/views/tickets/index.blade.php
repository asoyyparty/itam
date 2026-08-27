@extends('layouts.admin')

@section('title', __('messages.ticket'))

@section('content')
<div class="division-actions ticket-actions d-flex justify-content-end align-items-center mb-3">
    <a href="{{ route('tickets.export', request()->query()) }}" class="btn btn-sm btn-success" title="{{ __('messages.export') }}">
        <i class="fas fa-file-export mr-1"></i>{{ __('messages.export') }}
    </a>
    @can('action_manage_tickets')
    <a href="{{ route('tickets.create') }}" class="btn btn-sm btn-primary" title="{{ __('messages.create_ticket') }}">
        <i class="fas fa-plus mr-1"></i>{{ __('messages.create_ticket') }}
    </a>
    @endcan
</div>

<div class="card theme-card ticket-filter-card">
    <div class="division-table-header ticket-table-header card-header border-0 d-flex align-items-center flex-wrap">
        <form action="{{ route('tickets.index') }}" method="GET" class="ticket-search-form" style="width: 100%;">
            <div class="ticket-search-controls d-flex align-items-center justify-content-start">
                <select name="category" class="form-control select2 theme-input ticket-filter">
                    <option value="">{{ __('messages.all_category') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat['name'] }}" {{ request('category') == $cat['name'] ? 'selected' : '' }}>
                            {{ $cat['name'] }}
                        </option>
                    @endforeach
                </select>
                <select name="status" class="form-control select2 theme-input ticket-filter">
                    <option value="" >{{ __('messages.all_status') }}</option>
                    <option value="Open"  {{ str_contains(request('status') ?? '', 'Open') ? 'selected' : '' }}>{{ __('messages.open') }}</option>
                    <option value="In Progress"  {{ request('status') == 'In Progress' ? 'selected' : '' }}>{{ __('messages.in_progress') }}</option>
                    <option value="Resolved"  {{ request('status') == 'Resolved' ? 'selected' : '' }}>{{ __('messages.resolved') }}</option>
                    <option value="Closed"  {{ request('status') == 'Closed' ? 'selected' : '' }}>{{ __('messages.closed') }}</option>
                </select>
                <select name="priority" class="form-control select2 theme-input ticket-filter">
                    <option value="" >{{ __('messages.all_priority') }}</option>
                    <option value="Low"  {{ request('priority') == 'Low' ? 'selected' : '' }}>{{ __('messages.low') }}</option>
                    <option value="Medium"  {{ request('priority') == 'Medium' ? 'selected' : '' }}>{{ __('messages.medium') }}</option>
                    <option value="High"  {{ request('priority') == 'High' ? 'selected' : '' }}>{{ __('messages.high') }}</option>
                    <option value="Critical"  {{ request('priority') == 'Critical' ? 'selected' : '' }}>{{ __('messages.critical') }}</option>
                </select>
                <div class="ticket-search-input input-group">
                    <input type="text" name="search" class="form-control theme-input" placeholder="{{ __('messages.search_ticket') ?? 'Search ticket, title, name, PIC, or asset...' }}" value="{{ request('search') }}" aria-label="{{ __('messages.search_ticket') }}">
                    @if(request()->anyFilled(['search', 'status', 'priority', 'category']))
                        <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" title="{{ __('messages.clear') }}" aria-label="{{ __('messages.clear') }}"  style="min-width: 40px; height: 38px; padding: 0; display: flex !important; align-items: center !important; justify-content: center !important; border-color: rgba(148, 163, 184, 0.22);"><i class="fas fa-times"></i></a>
                    @endif
                    <button class="btn btn-info d-flex align-items-center justify-content-center" type="submit" title="{{ __('messages.search') }}" aria-label="{{ __('messages.search') }}"  style="min-width: 40px; height: 38px; padding: 0; display: flex !important; align-items: center !important; justify-content: center !important;"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card theme-card ticket-table-card">
    <div class="card-body p-0">
        <div class="theme-scroll-container table-responsive">
            <table class="table table-striped table-hover m-0 theme-table division-table">
                <thead>
                    <tr>
                        <th width="50">{{ __('messages.no') }}</th>
                        <th>{{ __('messages.ticket_number') }}</th>
                        <th>{{ __('messages.title') }}</th>
                        <th>{{ __('messages.reporter') }}</th>
                        <th>{{ __('messages.category') }}</th>
                        <th>PIC</th>
                        <th>{{ __('messages.priority') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>TIMELINE</th>
                        <th width="150" class="text-center">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr>
                        <td class="theme-text">{{ $loop->iteration }}</td>
                        <td class="font-weight-bold" >{{ $ticket->ticket_number }}</td>
                        <td class="theme-text">
                            {{ $ticket->title }}
                            @if($ticket->asset)
                            <br><small class="text-muted"><i class="fas fa-laptop"></i> {{ $ticket->asset->asset_tag }}</small>
                            @endif
                        </td>
                        <td class="theme-text">{{ $ticket->employee->name ?? '-' }}</td>
                        <td class="theme-text"><small class="text-info">{{ $ticket->category ?? '-' }}</small></td>
                        <td class="theme-text">{{ $ticket->pic->name ?? __('messages.unassigned') }}</td>
                        <td class="theme-text">
                            @switch($ticket->priority)
                                @case('Low') <span class="badge badge-info">{{ __('messages.low') }}</span> @break
                                @case('Medium') <span class="badge badge-warning">{{ __('messages.medium') }}</span> @break
                                @case('High') <span class="badge badge-danger">{{ __('messages.high') }}</span> @break
                                @case('Critical') <span class="badge badge-dark">{{ __('messages.critical') }}</span> @break
                            @endswitch
                        </td>
                        <td class="theme-text">
                            <div class="dropdown">
                                @can('action_manage_tickets')
<button class="btn btn-sm dropdown-toggle status-btn p-0 border-0 bg-transparent" type="button" data-toggle="dropdown" aria-expanded="false" data-ticket-id="{{ $ticket->id }}" style="box-shadow: none;">
                                    @switch($ticket->status)
                                        @case('Open') <span class="badge badge-primary status-badge" style="box-shadow: 0 0 8px rgba(0,123,255,0.5);">{{ __('messages.open') ?? 'Open' }}</span> @break
                                        @case('In Progress') <span class="badge badge-warning status-badge" style="box-shadow: 0 0 8px rgba(255,193,7,0.5);">{{ __('messages.in_progress') ?? 'In Progress' }}</span> @break
                                        @case('Resolved') <span class="badge badge-success status-badge" style="box-shadow: 0 0 8px rgba(40,167,69,0.5);">{{ __('messages.resolved') ?? 'Resolved' }}</span> @break
                                        @case('Closed') <span class="badge badge-secondary status-badge">{{ __('messages.closed') ?? 'Closed' }}</span> @break
                                    @endswitch
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" >
                                    <a class="dropdown-item status-change-btn text-primary" href="#" data-status="Open">{{ __('messages.open') ?? 'Open' }}</a>
                                    <a class="dropdown-item status-change-btn text-warning" href="#" data-status="In Progress">{{ __('messages.in_progress') ?? 'In Progress' }}</a>
                                    <a class="dropdown-item status-change-btn text-success" href="#" data-status="Resolved">{{ __('messages.resolved') ?? 'Resolved' }}</a>
                                    <a class="dropdown-item status-change-btn text-secondary" href="#" data-status="Closed">{{ __('messages.closed') ?? 'Closed' }}</a>
                                </div>
@else
<button class="btn btn-sm dropdown-toggle status-btn p-0 border-0 bg-transparent" type="button" data-toggle="dropdown" aria-expanded="false" data-ticket-id="{{ $ticket->id }}" style="box-shadow: none;">
                                    @switch($ticket->status)
                                        @case('Open') <span class="badge badge-primary status-badge" style="box-shadow: 0 0 8px rgba(0,123,255,0.5);">{{ __('messages.open') ?? 'Open' }}</span> @break
                                        @case('In Progress') <span class="badge badge-warning status-badge" style="box-shadow: 0 0 8px rgba(255,193,7,0.5);">{{ __('messages.in_progress') ?? 'In Progress' }}</span> @break
                                        @case('Resolved') <span class="badge badge-success status-badge" style="box-shadow: 0 0 8px rgba(40,167,69,0.5);">{{ __('messages.resolved') ?? 'Resolved' }}</span> @break
                                        @case('Closed') <span class="badge badge-secondary status-badge">{{ __('messages.closed') ?? 'Closed' }}</span> @break
                                    @endswitch
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" >
                                    <a class="dropdown-item status-change-btn text-primary" href="#" data-status="Open">{{ __('messages.open') ?? 'Open' }}</a>
                                    <a class="dropdown-item status-change-btn text-warning" href="#" data-status="In Progress">{{ __('messages.in_progress') ?? 'In Progress' }}</a>
                                    <a class="dropdown-item status-change-btn text-success" href="#" data-status="Resolved">{{ __('messages.resolved') ?? 'Resolved' }}</a>
                                    <a class="dropdown-item status-change-btn text-secondary" href="#" data-status="Closed">{{ __('messages.closed') ?? 'Closed' }}</a>
                                </div>
@endcan
                            </div>
                        </td>
                        <td class="theme-text" style="white-space: nowrap;">
                            <small class="d-block text-muted" style="margin-bottom: 2px;" title="{{ __('messages.created') }}"><i class="fas fa-plus-circle text-primary" style="width:14px"></i> {{ $ticket->created_at->format('Y-m-d H:i') }}</small>
                            @if($ticket->completed_at)
                                <small class="d-block text-success" title="{{ __('messages.completed') }}"><i class="fas fa-check-circle" style="width:14px"></i> {{ $ticket->completed_at->format('Y-m-d H:i') }}</small>
                            @endif
                        </td>
                        <td class="theme-text">
                            <div class="d-flex justify-content-center" style="gap: 8px;">
                                @can('action_manage_tickets')
<a href="{{ route('tickets.edit', array_merge(['ticket' => $ticket->id], request()->query())) }}" class="btn action-btn btn-outline-warning" style="border: 1px solid rgba(255, 193, 7, 0.3); background: rgba(255, 193, 7, 0.15); color: #ffc107;"  title="{{ __('messages.edit') }}"><i class="fas fa-edit"></i></a>
@endcan
                                @can('action_manage_tickets')
<form action="{{ route('tickets.destroy', array_merge(['ticket' => $ticket->id], request()->query())) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-delete action-btn btn-outline-danger" style="border: 1px solid rgba(220, 53, 69, 0.3); background: rgba(220, 53, 69, 0.15); color: #dc3545;"  title="{{ __('messages.delete') }}" data-confirm-message="{{ __('messages.confirm_delete') }}"><i class="fas fa-trash"></i></button>
                                </form>
@endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">{{ __('messages.no_data') }}</td>
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
        var ticketId = container.find('.status-btn').data('ticket-id');
        var badge = container.find('.status-badge');
        
        // Optional: Add a loading state
        var originalHtml = badge.html();
        badge.html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: '{{ url("tickets") }}/' + ticketId + '/status',
            type: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                if(response.success) {
                    badge.removeClass('badge-primary badge-warning badge-success badge-secondary badge-dark');
                    badge.css('box-shadow', 'none');
                    
                    switch(newStatus) {
                        case 'Open':
                            badge.addClass('badge-primary');
                            badge.css('box-shadow', '0 0 8px rgba(0,123,255,0.5)');
                            badge.text('{{ __("messages.open") ?? "Open" }}');
                            break;
                        case 'In Progress':
                            badge.addClass('badge-warning');
                            badge.css('box-shadow', '0 0 8px rgba(255,193,7,0.5)');
                            badge.text('{{ __("messages.in_progress") ?? "In Progress" }}');
                            break;
                        case 'Resolved':
                            badge.addClass('badge-success');
                            badge.css('box-shadow', '0 0 8px rgba(40,167,69,0.5)');
                            badge.text('{{ __("messages.resolved") ?? "Resolved" }}');
                            break;
                        case 'Closed':
                            badge.addClass('badge-secondary');
                            badge.text('{{ __("messages.closed") ?? "Closed" }}');
                            break;
                    }
                }
            },
            error: function(xhr) {
                Swal.fire({ icon: 'error', text: 'Error updating status. Please try again.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, background: '#0f172a', color: '#f8fafc', customClass: { popup: 'border border-danger' } });
                badge.html(originalHtml);
            }
        });
    });
});
</script>
<style>
/* Hide default dropdown arrow for status button */
.status-btn::after {
    display: none !important;
}

.ticket-actions {
    gap: 10px !important;
}

.ticket-actions .btn {
    min-height: 42px;
    padding: 0.7rem 1.1rem;
    border-radius: 12px !important;
    font-weight: 600;
}

.ticket-search-form,
.ticket-search-controls {
    width: 100%;
}

.ticket-search-controls {
    gap: 10px;
    flex-wrap: nowrap;
}

.ticket-search-input {
    min-width: 220px;
    flex: 1 1 auto;
    gap: 8px;
}

.ticket-search-input .form-control,
.ticket-filter {
    height: 42px !important;
    border-radius: 10px !important;
    border: 1px solid rgba(148, 163, 184, 0.22);
    background: rgba(15, 23, 42, 0.22);
    box-shadow: none !important;
}

.ticket-search-input .form-control {
    min-width: 0;
    flex: 1 1 auto;
}

.ticket-search-input .btn {
    height: 42px;
    min-width: 42px;
    border-radius: 10px !important;
}

.ticket-filter {
    width: 170px;
    min-width: 140px;
    flex: 0 1 170px;
}

@media (max-width: 767.98px) {
    .ticket-search-controls {
        flex-wrap: wrap;
    }

    .ticket-search-input,
    .ticket-filter {
        width: 100% !important;
        min-width: 0 !important;
        flex-basis: 100%;
    }

    .ticket-search-input {
        display: flex !important;
        flex-wrap: nowrap;
    }

    .ticket-search-input .form-control {
        width: auto !important;
    }
}
.status-change-btn:hover {
    background-color: rgba(255, 255, 255, 0.1);
}
</style>
@endpush
@endsection
