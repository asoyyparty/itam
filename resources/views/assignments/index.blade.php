@extends('layouts.admin')

@section('title', __('messages.asset_assignment') ?? 'Asset Assignment')

@section('content')
<div class="division-actions assignment-actions d-flex flex-nowrap justify-content-end align-items-center mb-3">
    @can('action_manage_assignments')
    <a href="{{ route('assignments.create') }}" class="btn btn-sm btn-primary" title="{{ __('messages.assign_asset') }}">{{ __('messages.assign_asset') }}</a>
    @endcan
</div>

<div class="card theme-card">
    <div class="division-table-header assignment-table-header card-header border-0 d-flex align-items-center flex-wrap">
        <form action="{{ route('assignments.index') }}" method="GET" class="assignment-search-form" id="assignment-filter-form" style="width: 100%;">
            <div class="assignment-search-controls d-flex align-items-center justify-content-start">
                <div class="assignment-date-range">
                    <label for="assignment-date-from" class="sr-only">{{ __('messages.from_date') ?? 'From date' }}</label>
                    <input id="assignment-date-from" type="text" name="date_from" class="form-control theme-input assignment-date-input" value="{{ request('date_from') }}" placeholder="dd/mm/yyyy" title="{{ __('messages.from_date') ?? 'From date' }}" aria-label="{{ __('messages.from_date') ?? 'From date' }}" autocomplete="off">
                    <span class="assignment-date-separator">-</span>
                    <label for="assignment-date-to" class="sr-only">{{ __('messages.to_date') ?? 'To date' }}</label>
                    <input id="assignment-date-to" type="text" name="date_to" class="form-control theme-input assignment-date-input" value="{{ request('date_to') }}" placeholder="dd/mm/yyyy" title="{{ __('messages.to_date') ?? 'To date' }}" aria-label="{{ __('messages.to_date') ?? 'To date' }}" autocomplete="off">
                </div>
                <select name="status" class="form-control select2 theme-input assignment-filter">
                    <option value="" >{{ __('messages.all_status') }}</option>
                    <option value="Assigned"  {{ request('status') == 'Assigned' ? 'selected' : '' }}>{{ __('messages.assigned') }}</option>
                    <option value="Returned"  {{ request('status') == 'Returned' ? 'selected' : '' }}>{{ __('messages.returned') ?? 'Returned' }}</option>
                </select>
                <div class="input-group assignment-search-input">
                    <input type="text" name="search" class="form-control theme-input" placeholder="{{ __('messages.search') }}..." value="{{ request('search') }}" aria-label="{{ __('messages.search') }}">
                    @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
                        <a href="{{ route('assignments.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" title="Clear" aria-label="Clear"  style="min-width: 40px; height: 38px; padding: 0; display: flex !important; align-items: center !important; justify-content: center !important; border-color: rgba(148, 163, 184, 0.22);"><i class="fas fa-times"></i></a>
                    @endif
                    <button class="btn btn-info d-flex align-items-center justify-content-center" type="submit" title="{{ __('messages.search') }}" aria-label="{{ __('messages.search') }}"  style="min-width: 40px; height: 38px; padding: 0; display: flex !important; align-items: center !important; justify-content: center !important;"><i class="fas fa-search"></i></button>
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
                        <th>{{ __('messages.asset') }}</th>
                        <th>{{ __('messages.employee') }}</th>
                        <th>{{ __('messages.assigned_date') }}</th>
                        <th>{{ __('messages.return_date') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th width="150" class="text-center">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                    <tr>
                        <td class="theme-text">{{ $assignments->firstItem() + $loop->index }}</td>
                        <td class="font-weight-bold" >
                            {{ $assignment->asset->name ?? '-' }}<br>
                            <small class="text-muted">{{ $assignment->asset->asset_tag ?? '-' }}</small>
                        </td>
                        <td class="theme-text">{{ $assignment->employee->name ?? '-' }}</td>
                        <td class="theme-text">{{ \Carbon\Carbon::parse($assignment->assigned_date)->locale(app()->getLocale())->translatedFormat('l, d F Y') }}</td>
                        <td class="theme-text">{{ $assignment->return_date ? \Carbon\Carbon::parse($assignment->return_date)->locale(app()->getLocale())->translatedFormat('l, d F Y') : '-' }}</td>
                        <td class="theme-text">
                            <div class="dropdown">
                                @can('action_manage_assignments')
<button class="btn btn-sm dropdown-toggle status-btn p-0 border-0 bg-transparent" type="button" data-toggle="dropdown" aria-expanded="false" data-id="{{ $assignment->id }}" style="box-shadow: none;">
                                    @if($assignment->status == 'Assigned')
                                        <span class="badge badge-primary status-badge" style="box-shadow: 0 0 8px rgba(0,123,255,0.5);">{{ __('messages.assigned') }}</span>
                                    @else
                                        <span class="badge badge-success status-badge" style="box-shadow: 0 0 8px rgba(40,167,69,0.5);">{{ __('messages.returned') ?? 'Returned' }}</span>
                                    @endif
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" >
                                    <a class="dropdown-item status-change-btn text-primary" href="#" data-status="Assigned">{{ __('messages.assigned') }}</a>
                                    <a class="dropdown-item status-change-btn text-success" href="#" data-status="Returned">{{ __('messages.returned') ?? 'Returned' }}</a>
                                </div>
@else
<button class="btn btn-sm dropdown-toggle status-btn p-0 border-0 bg-transparent" type="button" data-toggle="dropdown" aria-expanded="false" data-id="{{ $assignment->id }}" style="box-shadow: none;">
                                    @if($assignment->status == 'Assigned')
                                        <span class="badge badge-primary status-badge" style="box-shadow: 0 0 8px rgba(0,123,255,0.5);">{{ __('messages.assigned') }}</span>
                                    @else
                                        <span class="badge badge-success status-badge" style="box-shadow: 0 0 8px rgba(40,167,69,0.5);">{{ __('messages.returned') ?? 'Returned' }}</span>
                                    @endif
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" >
                                    <a class="dropdown-item status-change-btn text-primary" href="#" data-status="Assigned">{{ __('messages.assigned') }}</a>
                                    <a class="dropdown-item status-change-btn text-success" href="#" data-status="Returned">{{ __('messages.returned') ?? 'Returned' }}</a>
                                </div>
@endcan
                            </div>
                        </td>
                        <td class="theme-text">
                            <div class="d-flex justify-content-center" style="gap: 8px;">
                                @if($assignment->status == 'Assigned')
                                    <button type="button" class="btn action-btn btn-return-tech" title="{{ __('messages.return_asset') }}" data-toggle="modal" data-target="#returnModal{{ $assignment->id }}"><i class="fas fa-undo"></i></button>
                                @else
                                    <button type="button" class="btn action-btn" style="visibility: hidden;"><i class="fas fa-undo"></i></button>
                                @endif
                                @can('action_manage_assignments')
<a href="{{ route('assignments.edit', array_merge([$assignment->id], request()->query())) }}" class="btn action-btn btn-outline-warning" style="border: 1px solid rgba(255, 193, 7, 0.3); background: rgba(255, 193, 7, 0.15); color: #ffc107;"  title="{{ __('messages.edit') }}"><i class="fas fa-edit"></i></a>
@endcan
                                @can('action_manage_assignments')
<form action="{{ route('assignments.destroy', array_merge([$assignment->id], request()->query())) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-delete action-btn btn-outline-danger" style="border: 1px solid rgba(220, 53, 69, 0.3); background: rgba(220, 53, 69, 0.15); color: #dc3545;"  title="{{ __('messages.delete') }}" data-confirm-message="{{ __('messages.confirm_delete') }}"><i class="fas fa-trash"></i></button>
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
        @if($assignments->hasPages())
            <div class="pagination-footer card-footer d-flex justify-content-end align-items-center flex-wrap py-2" style="background: transparent; border-top: 1px solid rgba(255,255,255,0.08); gap: 10px;">
                {{ $assignments->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Return Modals placed outside of table-responsive to prevent clipping -->
@foreach($assignments as $assignment)
    @if($assignment->status == 'Assigned')
    <div class="modal fade" id="returnModal{{ $assignment->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content theme-card">
                <form action="{{ route('assignments.return', $assignment) }}" method="POST">
                    @csrf
                    <div class="modal-header border-0">
                        <h5 class="modal-title theme-text">{{ __('messages.return_asset') }}</h5>
                        <button type="button" class="close theme-text" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="theme-text">{{ __('messages.return_asset') }} <strong>{{ $assignment->asset->name ?? '' }}</strong></p>
                        <div class="form-group">
                            <label class="theme-text">{{ __('messages.return_date') }} *</label>
                            <input type="text" name="return_date" class="form-control theme-input assignment-return-date" value="{{ date('Y-m-d') }}" placeholder="dd/mm/yyyy" autocomplete="off" required >
                        </div>
                        <div class="form-group">
                            <label class="theme-text">{{ __('messages.return_notes') }}</label>
                            <textarea name="return_notes" class="form-control theme-input" rows="2"  ></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-success">{{ __('messages.mark_returned') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

@push('scripts')
<script>
$(document).ready(function() {
    flatpickr('.assignment-date-input', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        allowInput: true,
        onChange: function() {
            this.input.form.submit();
        },
    });

    flatpickr('.assignment-return-date', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        allowInput: true,
        appendTo: document.body,
        disableMobile: true,
        defaultDate: '{{ date('Y-m-d') }}',
        locale: {
            firstDayOfWeek: 0,
            weekdays: {
                shorthand: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                longhand: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
            },
            months: {
                shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                longhand: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
            }
        },
        onOpen: function(selectedDates, dateStr, instance) {
            var inputEl = instance.altInput || instance.input;
            if (!inputEl) return;

            var rect = inputEl.getBoundingClientRect();
            var calendar = instance.calendarContainer;
            var width = Math.min(300, window.innerWidth - 24);
            var height = calendar.offsetHeight || 330;
            var left = Math.min(
                Math.max(12, rect.left),
                window.innerWidth - width - 12
            );
            var top = Math.min(
                Math.max(12, rect.bottom + 6),
                window.innerHeight - height - 12
            );

            calendar.style.position = 'fixed';
            calendar.style.left = left + 'px';
            calendar.style.top = top + 'px';
            calendar.style.right = 'auto';
            calendar.style.bottom = 'auto';
            calendar.style.width = width + 'px';
        }
    });

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
            url: '{{ url("assignments") }}/' + id + '/status',
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
                        case 'Assigned':
                            badge.addClass('badge-primary');
                            badge.css('box-shadow', '0 0 8px rgba(0,123,255,0.5)');
                            badge.text('{{ __("messages.assigned") }}');
                            break;
                        case 'Returned':
                            badge.addClass('badge-success');
                            badge.css('box-shadow', '0 0 8px rgba(40,167,69,0.5)');
                            badge.text('{{ __("messages.returned") ?? "Returned" }}');
                            break;
                    }
                    setTimeout(function() { location.reload(); }, 1000); // Reload to update action buttons and date correctly
                } else {
                    Swal.fire({ icon: 'error', text: response.message || 'Error updating status.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, background: '#0f172a', color: '#f8fafc', customClass: { popup: 'border border-danger' } });
                    badge.html(originalHtml);
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
.assignment-return-date,
.assignment-return-date + .form-control {
    cursor: pointer;
}
.flatpickr-calendar {
    z-index: 99999 !important;
    width: 300px !important;
    max-width: calc(100vw - 24px) !important;
    box-shadow: 0 14px 26px rgba(2, 6, 23, 0.42) !important;
    overflow: visible !important;
    position: fixed !important;
    background: rgba(12, 18, 29, 0.98) !important;
    border: 1px solid rgba(148, 163, 184, 0.18) !important;
    border-radius: 12px !important;
    color: #e2e8f0 !important;
    font-family: inherit !important;
    padding: 0 !important;
}

.flatpickr-calendar .flatpickr-months {
    border-bottom: 1px solid rgba(148, 163, 184, 0.18) !important;
    padding: 10px 10px 6px !important;
    min-height: 42px !important;
}

.flatpickr-calendar .flatpickr-weekdays {
    padding: 0 10px !important;
    background: transparent !important;
}

.flatpickr-calendar .flatpickr-weekdaycontainer {
    justify-content: space-between !important;
}

.flatpickr-calendar .flatpickr-month,
.flatpickr-calendar .flatpickr-monthDropdown-months,
.flatpickr-calendar .flatpickr-current-month .cur-month,
.flatpickr-calendar .flatpickr-current-month .cur-year,
.flatpickr-calendar .flatpickr-weekday,
.flatpickr-calendar .flatpickr-day,
.flatpickr-calendar .flatpickr-time {
    color: #e2e8f0 !important;
}

.flatpickr-calendar .flatpickr-month {
    font-size: 14px !important;
    font-weight: 600 !important;
}

.flatpickr-calendar .flatpickr-current-month {
    display: flex !important;
    position: static !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    min-height: 30px !important;
    width: auto !important;
    padding: 0 !important;
}

.flatpickr-calendar .flatpickr-current-month .cur-month,
.flatpickr-calendar .flatpickr-current-month .cur-year {
    font-size: 14px !important;
    font-weight: 600 !important;
    color: #f8fafc !important;
}

.flatpickr-calendar .flatpickr-prev-month,
.flatpickr-calendar .flatpickr-next-month {
    fill: #cbd5e1 !important;
    color: #cbd5e1 !important;
    width: 18px !important;
    height: 18px !important;
}

.flatpickr-calendar .flatpickr-weekday {
    color: #9aa8bd !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    letter-spacing: 0.03em !important;
    padding: 0 0 2px !important;
    flex: 1 1 0 !important;
    text-align: center !important;
}

.flatpickr-calendar .flatpickr-days {
    width: 100% !important;
    padding: 0 9px 9px !important;
}

.flatpickr-calendar .dayContainer {
    min-width: 0 !important;
    width: 100% !important;
    max-width: none !important;
    justify-content: space-between !important;
}

.flatpickr-calendar .flatpickr-day {
    border-radius: 8px !important;
    border: 0 !important;
    background: transparent !important;
    color: #e2e8f0 !important;
    height: 30px !important;
    line-height: 30px !important;
    max-width: 34px !important;
    width: 34px !important;
    margin: 1px 1px 1px !important;
    font-size: 13px !important;
    flex: 0 0 auto !important;
}

.flatpickr-calendar .flatpickr-day:hover,
.flatpickr-calendar .flatpickr-day:focus {
    background: rgba(148, 163, 184, 0.09) !important;
}

.flatpickr-calendar .flatpickr-day.selected {
    background: #3b82f6 !important;
    border-color: #3b82f6 !important;
    color: #fff !important;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.15) !important;
    font-weight: 700 !important;
}

.flatpickr-calendar .flatpickr-day.selected:hover {
    background: #2563eb !important;
}

.flatpickr-calendar .flatpickr-day.today {
    border: 1px solid rgba(96, 165, 250, 0.75) !important;
    color: #f8fafc !important;
    background: rgba(59, 130, 246, 0.1) !important;
}

.flatpickr-calendar .flatpickr-day.today.selected {
    border-color: rgba(255, 255, 255, 0.2) !important;
}

.flatpickr-calendar .flatpickr-day.prevMonthDay,
.flatpickr-calendar .flatpickr-day.nextMonthDay {
    color: rgba(148, 163, 184, 0.75) !important;
}

.flatpickr-calendar .flatpickr-innerContainer,
.flatpickr-calendar .flatpickr-rContainer {
    background: transparent !important;
}

@media (max-width: 420px) {
    .flatpickr-calendar {
        width: min(290px, calc(100vw - 16px)) !important;
    }
}
</style>
@endpush
@endsection
