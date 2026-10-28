@extends('layouts.admin')

@section('title', __('messages.asset_assignment') ?? 'Asset Assignment')

@section('content')

<!-- Hallmark Master Header Banner -->
<div class="master-header-banner">
    <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 16px;">
        <div class="d-flex align-items-center" style="gap: 14px; min-width: 0; flex: 1;">
            <div style="width: 44px; height: 44px; border-radius: 14px; background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(79, 70, 229, 0.08)); border: 1px solid rgba(99, 102, 241, 0.35); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 1.2rem; flex-shrink: 0; box-shadow: 0 0 14px rgba(99, 102, 241, 0.25);">
                <i class="fas fa-handshake"></i>
            </div>
            <div style="flex: 1; min-width: 0;">
                <h5 class="mb-0 font-weight-bold theme-text text-truncate" style="font-size: 1.15rem; line-height: 1.2;">{{ __('messages.asset_assignment') }}</h5>
                <small class="text-muted d-block" style="font-size: 0.8rem; margin-top: 2px; white-space: normal; line-height: 1.3;">{{ __('messages.assignment_subtitle') }}</small>
            </div>
        </div>

        <div class="d-flex align-items-center flex-wrap handover-action-buttons ml-auto" style="gap: 10px; flex-shrink: 0;">
            <span style="font-family: var(--font-mono); font-size: 0.72rem; padding: 7px 14px; background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.25); color: #818cf8; border-radius: 999px; display: inline-flex; align-items: center; gap: 6px; font-weight: 600; white-space: nowrap;">
                <span class="live-dot" style="width: 7px; height: 7px; border-radius: 50%; background: #6366f1; box-shadow: 0 0 8px #6366f1; display: inline-block;"></span>
                {{ $activeCount ?? 0 }} {{ strtoupper(__('messages.active_borrowed')) }}
            </span>
            @can('action_manage_assignments')
            <a href="{{ route('assignments.create') }}" class="btn btn-sm btn-action-primary text-nowrap btn-add-main" title="{{ __('messages.assign_asset') }}">
                <i class="fas fa-plus mr-1"></i> {{ __('messages.assign_asset') }}
            </a>
            @endcan
        </div>
    </div>
</div>

<!-- Hallmark Quick Status Ribbon -->
@php
    $currentStatus = request('status', '');
    $baseParams = request()->except(['status', 'page']);
@endphp
<div class="quick-status-ribbon">
    <div class="status-ribbon-scroll">
        <a href="{{ route('assignments.index', $baseParams) }}" 
           class="status-pill-item {{ $currentStatus === '' ? 'active' : '' }}">
            <span class="status-pill-dot" style="background: #94a3b8;"></span>
            <span class="status-pill-label">{{ __('messages.all') }}</span>
            <span class="status-pill-count">{{ $totalAssignmentsCount ?? $assignments->total() }}</span>
        </a>

        <a href="{{ route('assignments.index', array_merge($baseParams, ['status' => 'Assigned'])) }}" 
           class="status-pill-item {{ $currentStatus === 'Assigned' ? 'active' : '' }}">
            <span class="status-pill-dot" style="background: #818cf8;"></span>
            <span class="status-pill-label">{{ __('messages.currently_borrowed') }}</span>
            <span class="status-pill-count">{{ $activeCount ?? 0 }}</span>
        </a>

        <a href="{{ route('assignments.index', array_merge($baseParams, ['status' => 'Returned'])) }}" 
           class="status-pill-item {{ $currentStatus === 'Returned' ? 'active' : '' }}">
            <span class="status-pill-dot" style="background: #34d399;"></span>
            <span class="status-pill-label">{{ __('messages.returned') }}</span>
            <span class="status-pill-count">{{ $returnedCount ?? 0 }}</span>
        </a>
    </div>
</div>

<!-- Main Table Card Container -->
<div class="card theme-card master-table-card">
    <!-- Hallmark Smart Filter Bar -->
    <div class="card-header border-0 py-3 px-3 px-md-4" style="border-bottom: 1px solid rgba(148, 163, 184, 0.12) !important;">
        <form action="{{ route('assignments.index') }}" method="GET" class="w-100" id="assignment-filter-form">
            <div class="smart-filter-grid">
                <!-- Date Range Pill -->
                <div class="assignment-date-range-pill">
                    <i class="far fa-calendar-alt date-range-icon"></i>
                    <label for="assignment-date-from" class="sr-only">{{ __('messages.start_date') }}</label>
                    <input id="assignment-date-from" type="text" name="date_from" class="date-pill-input assignment-date-input" value="{{ request('date_from') }}" placeholder="{{ __('messages.start_date') }}" title="{{ __('messages.start_date') }}" autocomplete="off">
                    <span class="date-range-arrow"><i class="fas fa-arrow-right"></i></span>
                    <label for="assignment-date-to" class="sr-only">{{ __('messages.end_date') }}</label>
                    <input id="assignment-date-to" type="text" name="date_to" class="date-pill-input assignment-date-input" value="{{ request('date_to') }}" placeholder="{{ __('messages.end_date') }}" title="{{ __('messages.end_date') }}" autocomplete="off">
                    @if(request()->filled('date_from') || request()->filled('date_to'))
                        <a href="{{ route('assignments.index', request()->except(['date_from', 'date_to'])) }}" class="date-range-clear-btn" title="{{ __('messages.clear_date_filter') }}">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>

                <!-- Status Filter Select -->
                <div class="filter-control-item" style="min-width: 170px;">
                    <select name="status" class="form-control custom-filter-select" onchange="this.form.submit()">
                        <option value="">{{ __('messages.all_status') }}</option>
                        <option value="Assigned" {{ request('status') == 'Assigned' ? 'selected' : '' }}>{{ __('messages.assigned') }}</option>
                        <option value="Returned" {{ request('status') == 'Returned' ? 'selected' : '' }}>{{ __('messages.returned') }}</option>
                    </select>
                </div>

                <!-- Search Input with Clear Button -->
                <div class="sleek-search-wrapper" style="flex: 1; min-width: 220px;">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" class="form-control sleek-search-input" placeholder="{{ __('messages.search_assignment_placeholder') }}" value="{{ request('search') }}" aria-label="{{ __('messages.search') }}">
                    @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
                        <a href="{{ route('assignments.index') }}" class="clear-search-btn" title="{{ __('messages.reset') }}">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- High-Density Handover Table -->
    <div class="card-body p-0">
        <div class="theme-scroll-container table-responsive">
            <table class="table table-hover m-0 theme-table master-data-table">
                <thead>
                    <tr style="font-family: var(--font-mono); font-size: 0.72rem; letter-spacing: 0.08em; text-transform: uppercase;">
                        <th width="50" class="pl-4">{{ __('messages.no') }}</th>
                        <th style="min-width: 240px;">{{ __('messages.asset') }}</th>
                        <th style="min-width: 180px;">{{ __('messages.employee') }}</th>
                        <th style="min-width: 150px;">{{ __('messages.assigned_date') }}</th>
                        <th style="min-width: 150px;">{{ __('messages.return_date') }}</th>
                        <th width="130">{{ __('messages.status') }}</th>
                        <th width="130" class="text-center">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                    <tr>
                        <td class="pl-4 text-muted font-mono" style="font-size: 0.82rem;">
                            {{ $assignments->firstItem() + $loop->index }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center" style="gap: 12px;">
                                @if($assignment->photo)
                                    <a href="{{ asset('storage/' . $assignment->photo) }}" target="_blank" class="asset-table-thumbnail-wrap" title="Lihat Foto Bukti Serah Terima">
                                        <img src="{{ asset('storage/' . $assignment->photo) }}" alt="Handover Photo" class="asset-table-thumbnail">
                                    </a>
                                @elseif($assignment->asset && $assignment->asset->image)
                                    <a href="{{ asset('storage/' . $assignment->asset->image) }}" target="_blank" class="asset-table-thumbnail-wrap" title="Foto Unit Aset">
                                        <img src="{{ asset('storage/' . $assignment->asset->image) }}" alt="Asset Photo" class="asset-table-thumbnail">
                                    </a>
                                @else
                                    <div class="asset-table-placeholder">
                                        <i class="fas fa-desktop"></i>
                                    </div>
                                @endif
                                <div class="text-truncate">
                                    <div class="font-weight-bold theme-text text-truncate" style="font-size: 0.88rem; line-height: 1.2;">
                                        {{ $assignment->asset->name ?? '-' }}
                                    </div>
                                    @if($assignment->asset && $assignment->asset->asset_tag)
                                        <span class="asset-tag-pill font-mono mt-1" style="display: inline-block;">
                                            {{ $assignment->asset->asset_tag }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center" style="gap: 10px;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: rgba(99, 102, 241, 0.12); border: 1px solid rgba(99, 102, 241, 0.25); color: #818cf8; display: flex; align-items: center; justify-content: center; font-size: 0.78rem; font-weight: 700; flex-shrink: 0;">
                                    {{ strtoupper(substr($assignment->employee->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="text-truncate">
                                    <div class="theme-text font-weight-bold text-truncate" style="font-size: 0.86rem; line-height: 1.2;">
                                        {{ $assignment->employee->name ?? '-' }}
                                    </div>
                                    @if($assignment->employee && $assignment->employee->department)
                                        <small class="text-muted d-block text-truncate" style="font-size: 0.72rem; margin-top: 1px;">
                                            {{ $assignment->employee->department->name ?? '' }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="theme-text font-mono" style="font-size: 0.82rem;">
                                <i class="far fa-calendar-alt text-warning mr-1"></i>
                                {{ \Carbon\Carbon::parse($assignment->assigned_date)->format('d/m/Y') }}
                            </div>
                            <small class="text-muted font-mono" style="font-size: 0.72rem;">
                                {{ \Carbon\Carbon::parse($assignment->assigned_date)->diffForHumans() }}
                            </small>
                        </td>
                        <td>
                            @if($assignment->return_date)
                                <div class="theme-text font-mono" style="font-size: 0.82rem;">
                                    <i class="far fa-calendar-check text-success mr-1"></i>
                                    {{ \Carbon\Carbon::parse($assignment->return_date)->format('d/m/Y') }}
                                </div>
                                <small class="text-success font-mono" style="font-size: 0.72rem;">{{ __('messages.returned') }}</small>
                            @else
                                <span class="badge" style="background: rgba(99, 102, 241, 0.12); border: 1px solid rgba(99, 102, 241, 0.28); color: #818cf8; font-size: 0.76rem; font-family: var(--font-mono); padding: 5px 10px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-clock" style="font-size: 0.7rem;"></i> {{ __('messages.currently_borrowed') }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                @can('action_manage_assignments')
                                <button class="btn btn-sm dropdown-toggle p-0 border-0 bg-transparent status-btn" type="button" data-toggle="dropdown" aria-expanded="false" data-id="{{ $assignment->id }}" style="box-shadow: none;">
                                    @if($assignment->status == 'Assigned')
                                        <span class="badge status-badge" style="background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.35); color: #818cf8; box-shadow: 0 0 10px rgba(99, 102, 241, 0.2); padding: 5px 10px; border-radius: 999px; font-weight: 600;">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #6366f1; box-shadow: 0 0 6px #6366f1; display: inline-block; margin-right: 4px;"></span>
                                            {{ __('messages.assigned') }}
                                        </span>
                                    @else
                                        <span class="badge status-badge" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.35); color: #34d399; box-shadow: 0 0 10px rgba(16, 185, 129, 0.2); padding: 5px 10px; border-radius: 999px; font-weight: 600;">
                                            <i class="fas fa-check mr-1"></i> {{ __('messages.returned') ?? 'Returned' }}
                                        </span>
                                    @endif
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" style="background: rgba(15, 23, 42, 0.98); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 12px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);">
                                    <a class="dropdown-item status-change-btn text-primary py-2" href="#" data-status="Assigned" style="font-size: 0.84rem; font-weight: 600;">
                                        <i class="fas fa-user-check mr-2"></i> {{ __('messages.assigned') }}
                                    </a>
                                    <a class="dropdown-item status-change-btn text-success py-2" href="#" data-status="Returned" style="font-size: 0.84rem; font-weight: 600;">
                                        <i class="fas fa-check-circle mr-2"></i> {{ __('messages.returned') ?? 'Returned' }}
                                    </a>
                                </div>
                                @else
                                    @if($assignment->status == 'Assigned')
                                        <span class="badge status-badge" style="background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.35); color: #818cf8; padding: 5px 10px; border-radius: 999px; font-weight: 600;">
                                            {{ __('messages.assigned') }}
                                        </span>
                                    @else
                                        <span class="badge status-badge" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.35); color: #34d399; padding: 5px 10px; border-radius: 999px; font-weight: 600;">
                                            {{ __('messages.returned') ?? 'Returned' }}
                                        </span>
                                    @endif
                                @endcan
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                                @if($assignment->status == 'Assigned')
                                    <button type="button" class="btn btn-sm d-flex align-items-center justify-content-center" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 10px; width: 34px; height: 34px; transition: all 0.2s ease;" title="{{ __('messages.return_asset') }}" data-toggle="modal" data-target="#returnModal{{ $assignment->id }}">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                @endif

                                @can('action_manage_assignments')
                                <a href="{{ route('assignments.edit', array_merge([$assignment->id], request()->query())) }}" class="btn btn-sm d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 10px; width: 34px; height: 34px; transition: all 0.2s ease;" title="{{ __('messages.edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('assignments.destroy', array_merge([$assignment->id], request()->query())) }}" method="POST" class="d-inline delete-assignment-form">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-delete d-flex align-items-center justify-content-center" style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 10px; width: 34px; height: 34px; transition: all 0.2s ease;" title="{{ __('messages.delete') }}" onclick="confirmDeleteAssignment(this)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <div style="opacity: 0.6;">
                                <i class="fas fa-inbox mb-2" style="font-size: 2rem;"></i>
                                <div style="font-size: 0.9rem;">{{ __('messages.no_data') }}</div>
                                <small>{{ __('messages.no_matching_assignments') }}</small>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assignments->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap py-3 px-3 px-md-4" style="background: transparent; border-top: var(--rule-soft); gap: 10px;">
                <small class="text-muted font-mono" style="font-size: 0.78rem;">
                    {{ __('messages.showing_counter_text') }} {{ $assignments->firstItem() ?? 0 }} - {{ $assignments->lastItem() ?? 0 }} {{ __('messages.of_counter_text') }} {{ $assignments->total() }}
                </small>
                <div>
                    {{ $assignments->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Hallmark Glassmorphic Return Modals -->
@foreach($assignments as $assignment)
    @if($assignment->status == 'Assigned')
    <div class="modal fade" id="returnModal{{ $assignment->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content theme-card hallmark-modal-card">
                <form action="{{ route('assignments.return', $assignment) }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <div class="d-flex align-items-center" style="gap: 12px;">
                            <div style="width: 38px; height: 38px; border-radius: 12px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); display: flex; align-items: center; justify-content: center; color: #34d399; font-size: 1.05rem;">
                                <i class="fas fa-undo"></i>
                            </div>
                            <div>
                                <h5 class="modal-title font-weight-bold theme-text mb-0" style="font-size: 1.05rem;">{{ __('messages.return_asset') }}</h5>
                                <small class="text-muted font-mono" style="font-size: 0.75rem;">TAG: {{ $assignment->asset->asset_tag ?? '-' }}</small>
                            </div>
                        </div>
                        <button type="button" class="close theme-text" data-dismiss="modal" aria-label="Close" style="opacity: 0.7;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body py-4">
                        <div class="p-3 mb-3 rounded-lg" style="background: rgba(15, 23, 42, 0.35); border: 1px solid rgba(148, 163, 184, 0.16);">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">{{ __('messages.asset') }}:</span>
                                <span class="font-weight-bold theme-text small">{{ $assignment->asset->name ?? '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <span class="text-muted small">{{ __('messages.employee') }}:</span>
                                <span class="theme-text small">{{ $assignment->employee->name ?? '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <span class="text-muted small">{{ __('messages.assigned_date') }}:</span>
                                <span class="font-mono theme-text small">{{ \Carbon\Carbon::parse($assignment->assigned_date)->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="field-label">{{ __('messages.return_date') }} <span class="text-danger">*</span></label>
                            <input type="text" name="return_date" class="form-control field-input font-mono assignment-return-date" value="{{ date('Y-m-d') }}" placeholder="dd/mm/yyyy" autocomplete="off" required>
                        </div>

                        <div class="form-group mb-0">
                            <label class="field-label">{{ __('messages.return_notes') }}</label>
                            <textarea name="return_notes" class="form-control field-input" rows="3" placeholder="{{ __('messages.return_notes_placeholder') }}"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-sm btn-action-secondary px-3" data-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-sm btn-success px-4 font-weight-bold" style="border-radius: 8px;">
                            <i class="fas fa-check mr-1"></i> {{ __('messages.mark_returned') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

<style>
/* Hallmark Â· pre-emit critique: P5 H5 E5 S5 R5 V5 */
/* â”€â”€â”€ Hallmark Master Header Banner â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.master-header-banner {
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(30, 41, 59, 0.85));
    border: 1px solid rgba(148, 163, 184, 0.15);
    border-radius: 20px;
    padding: 18px 24px;
    margin-top: 0 !important;
    margin-bottom: 18px !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}
body.light-mode .master-header-banner {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(241, 245, 249, 0.85));
    border: 1px solid rgba(226, 232, 240, 0.9);
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
}

/* â”€â”€â”€ Quick Status Ribbon â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.quick-status-ribbon {
    margin-bottom: 18px !important;
}
.status-ribbon-scroll {
    display: flex;
    align-items: center;
    gap: 10px;
    overflow-x: auto;
    padding: 2px 2px 8px;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
}
.status-ribbon-scroll::-webkit-scrollbar {
    display: none;
}
.status-pill-item {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 14px;
    background: rgba(15, 23, 42, 0.45);
    border: 1px solid rgba(148, 163, 184, 0.18);
    border-radius: 999px;
    color: var(--color-ink-1, #cbd5e1);
    text-decoration: none !important;
    font-size: 0.82rem;
    font-weight: 600;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    white-space: nowrap;
    flex-shrink: 0;
}
body.light-mode .status-pill-item {
    background: #ffffff;
    border-color: #cbd5e1;
    color: #475569;
}
.status-pill-item:hover {
    background: rgba(99, 102, 241, 0.1);
    border-color: rgba(99, 102, 241, 0.35);
    color: #818cf8;
    transform: translateY(-1px);
}
.status-pill-item.active {
    background: rgba(99, 102, 241, 0.18);
    border-color: #6366f1;
    color: #818cf8;
    box-shadow: 0 0 14px rgba(99, 102, 241, 0.25);
}
body.light-mode .status-pill-item.active {
    background: rgba(99, 102, 241, 0.12);
    border-color: #6366f1;
    color: #4f46e5;
}
.status-pill-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}
.status-pill-label {
    font-size: 0.82rem;
    line-height: 1;
}
.status-pill-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 1px 7px;
    border-radius: 999px;
    background: rgba(15, 23, 42, 0.5);
    border: 1px solid rgba(148, 163, 184, 0.2);
    font-family: var(--font-mono);
    font-size: 0.72rem;
    font-weight: 700;
    color: inherit;
}
body.light-mode .status-pill-count {
    background: #f1f5f9;
    border-color: #e2e8f0;
}

/* â”€â”€â”€ Master Table Card â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.master-table-card {
    background: var(--color-paper-0);
    border: var(--rule-soft);
    border-radius: 18px !important;
    overflow: hidden;
    box-shadow: var(--shadow-card);
}

/* â”€â”€â”€ Smart Filter Grid â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.smart-filter-grid {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

/* â”€â”€â”€ Date Range Pill â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.assignment-date-range-pill {
    position: relative;
    height: 44px !important;
    background: rgba(15, 23, 42, 0.35) !important;
    border: 1px solid rgba(148, 163, 184, 0.22) !important;
    border-radius: 12px !important;
    padding: 0 14px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 10px !important;
    transition: all 0.2s ease !important;
}
body.light-mode .assignment-date-range-pill {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
}
.assignment-date-range-pill:focus-within {
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
}
.assignment-date-range-pill .date-range-icon {
    color: #818cf8 !important;
    font-size: 0.95rem !important;
    flex-shrink: 0 !important;
}
.assignment-date-range-pill input,
.assignment-date-range-pill .form-control,
.assignment-date-range-pill .flatpickr-input,
.assignment-date-range-pill input.form-control,
.assignment-date-range-pill input.input,
.assignment-date-range-pill input.flatpickr-input {
    height: 100% !important;
    border: none !important;
    background: transparent !important;
    background-color: transparent !important;
    box-shadow: none !important;
    outline: none !important;
    padding: 0 4px !important;
    font-size: 0.85rem !important;
    color: var(--color-ink-0) !important;
    width: 98px !important;
    text-align: center !important;
    font-family: var(--font-mono) !important;
    font-weight: 500 !important;
}
.filter-control-item .custom-filter-select {
    height: 44px !important;
    border-radius: 12px !important;
    border: 1px solid rgba(148, 163, 184, 0.22) !important;
}
.sleek-search-wrapper .sleek-search-input {
    height: 44px !important;
    border-radius: 12px !important;
}
.assignment-date-range-pill input:focus,
.assignment-date-range-pill .form-control:focus,
.assignment-date-range-pill .flatpickr-input:focus,
.assignment-date-range-pill .flatpickr-input.active {
    background: transparent !important;
    background-color: transparent !important;
    border: none !important;
    box-shadow: none !important;
    outline: none !important;
}
.assignment-date-range-pill input::placeholder,
.assignment-date-range-pill .form-control::placeholder,
.assignment-date-range-pill .flatpickr-input::placeholder {
    color: #94a3b8 !important;
    -webkit-text-fill-color: #94a3b8 !important;
    opacity: 1 !important;
    font-weight: 500 !important;
    font-family: var(--font-mono) !important;
    text-align: center !important;
}
.assignment-date-range-pill .date-range-arrow {
    color: #818cf8 !important;
    font-size: 0.78rem !important;
    flex-shrink: 0 !important;
}
.assignment-date-range-pill .date-range-clear-btn {
    color: #94a3b8 !important;
    font-size: 0.85rem !important;
    padding: 2px 4px !important;
    text-decoration: none !important;
    flex-shrink: 0 !important;
}
.assignment-date-range-pill .date-range-clear-btn:hover {
    color: #f87171 !important;
}

/* â”€â”€â”€ Table Thumbnail â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.asset-table-thumbnail-wrap {
    width: 36px;
    height: 36px;
    border-radius: 9px;
    overflow: hidden;
    flex-shrink: 0;
    border: 1px solid rgba(148, 163, 184, 0.2);
    display: inline-block;
}
.asset-table-thumbnail {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.2s ease;
}
.asset-table-thumbnail-wrap:hover .asset-table-thumbnail {
    transform: scale(1.15);
}
.asset-table-placeholder {
    width: 36px;
    height: 36px;
    border-radius: 9px;
    background: rgba(99, 102, 241, 0.08);
    border: 1px solid rgba(99, 102, 241, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #818cf8;
    font-size: 0.85rem;
    flex-shrink: 0;
}

/* â”€â”€â”€ Asset Tag Pill â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.asset-tag-pill {
    display: inline-block;
    padding: 1px 7px;
    background: rgba(148, 163, 184, 0.1);
    border: 1px solid rgba(148, 163, 184, 0.18);
    border-radius: 5px;
    font-size: 0.72rem;
    color: var(--color-ink-2);
    letter-spacing: 0.02em;
}

/* â”€â”€â”€ Status Badges â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.hallmark-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 9px;
    border-radius: 999px;
    font-size: 0.74rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    white-space: nowrap;
    border: 1px solid transparent;
}
.badge-status-available { background: rgba(16, 185, 129, 0.12); color: #34d399; border-color: rgba(16, 185, 129, 0.25); }
.badge-status-assigned  { background: rgba(99, 102, 241, 0.12); color: #818cf8; border-color: rgba(99, 102, 241, 0.25); }

/* â”€â”€â”€ Action Icon Buttons â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.btn-icon-action {
    width: 30px;
    height: 30px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-size: 0.8rem;
    transition: all 0.18s ease;
    border: 1px solid transparent;
}
.btn-icon-return {
    background: rgba(16, 185, 129, 0.12);
    color: #34d399;
    border-color: rgba(16, 185, 129, 0.25);
}
.btn-icon-return:hover {
    background: #10b981;
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.35);
}
.btn-icon-edit {
    background: rgba(245, 158, 11, 0.12);
    color: #fbbf24;
    border-color: rgba(245, 158, 11, 0.25);
}
.btn-icon-edit:hover {
    background: #f59e0b;
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.35);
}
.btn-icon-delete {
    background: rgba(239, 68, 68, 0.12);
    color: #f87171;
    border-color: rgba(239, 68, 68, 0.25);
}
.btn-icon-delete:hover {
    background: #ef4444;
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.35);
}

/* â”€â”€â”€ Modal Card â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.hallmark-modal-card {
    background: var(--color-paper-0) !important;
    border: var(--rule-soft) !important;
    border-radius: 18px !important;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35) !important;
}

@media (max-width: 767.98px) {
    .master-header-banner {
        padding: 16px !important;
        border-radius: 16px !important;
        margin-bottom: 14px !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }
    .master-header-banner > .d-flex {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 12px !important;
        width: 100% !important;
    }
    .handover-action-buttons {
        width: 100% !important;
        margin-left: 0 !important;
        margin-top: 4px !important;
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
    }
    .handover-action-buttons .btn-action-primary,
    .master-header-banner .btn-action-primary {
        width: 100% !important;
        flex: 1 1 100% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        height: 42px !important;
        font-size: 0.9rem !important;
        order: 10 !important;
        margin-top: 2px !important;
    }
    .quick-status-ribbon {
        margin-bottom: 12px !important;
        width: 100% !important;
    }
    .status-ribbon-scroll {
        padding: 4px 4px 8px 4px !important;
        gap: 8px !important;
    }
    .status-pill-item {
        padding: 6px 12px !important;
        font-size: 0.78rem !important;
        flex-shrink: 0 !important;
    }
    .master-table-card {
        border-radius: 14px !important;
    }
    .smart-filter-grid {
        display: flex !important;
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 10px !important;
    }
    .smart-filter-grid > * {
        margin-bottom: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    .assignment-date-range-pill {
        width: 100% !important;
        height: 44px !important;
        justify-content: space-between !important;
        padding: 0 10px !important;
        display: flex !important;
        align-items: center !important;
        box-sizing: border-box !important;
    }
    .assignment-date-range-pill input:not([type="hidden"]),
    .assignment-date-range-pill .flatpickr-input:not([type="hidden"]) {
        width: 38% !important;
        max-width: 120px !important;
        font-size: 0.78rem !important;
        min-width: 0 !important;
        flex: 0 0 38% !important;
        text-align: center !important;
        padding: 0 2px !important;
    }
    .assignment-date-range-pill .date-range-arrow {
        flex: 0 0 auto !important;
        margin: 0 2px !important;
    }
    .assignment-date-range-pill .date-range-icon {
        flex: 0 0 auto !important;
        margin-right: 4px !important;
    }
    .filter-control-item,
    .filter-control-item select,
    .sleek-search-wrapper {
        width: 100% !important;
        min-width: 100% !important;
        height: 44px !important;
    }
    .master-data-table {
        min-width: 720px !important;
    }
    .modal-dialog {
        margin: 12px auto !important;
        max-width: calc(100vw - 24px) !important;
    }
    .modal-footer {
        flex-direction: column-reverse !important;
        gap: 8px !important;
    }
    .modal-footer .btn {
        width: 100% !important;
        justify-content: center !important;
        height: 40px !important;
    }
}
</style>

@push('scripts')
<script>
$(document).ready(function() {
    flatpickr('.assignment-date-input', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        allowInput: true,
        disableMobile: true,
        onReady: function(selectedDates, dateStr, instance) {
            if (instance.altInput && instance.input) {
                var ph = instance.input.getAttribute('placeholder');
                if (ph) {
                    instance.altInput.setAttribute('placeholder', ph);
                }
            }
        },
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
        defaultDate: '{{ date('Y-m-d') }}'
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
                    if (window.showHallmarkToast) {
                        window.showHallmarkToast('success', 'Status Diperbarui', response.message || 'Status penyerahan berhasil diubah.');
                    }
                    setTimeout(function() { location.reload(); }, 600);
                } else {
                    Swal.fire({ icon: 'error', text: response.message || 'Error updating status.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, background: '#0f172a', color: '#f8fafc' });
                    badge.html(originalHtml);
                }
            },
            error: function(xhr) {
                Swal.fire({ icon: 'error', text: 'Error updating status.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, background: '#0f172a', color: '#f8fafc' });
                badge.html(originalHtml);
            }
        });
    });
});

function confirmDeleteAssignment(btn) {
    Swal.fire({
        title: '{{ __("messages.delete_assignment_title") }}',
        text: '{{ __("messages.delete_assignment_text") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '{{ __("messages.yes_delete") }}',
        cancelButtonText: '{{ __("messages.cancel") }}',
        background: '#0f172a',
        color: '#f8fafc'
    }).then((result) => {
        if (result.isConfirmed) {
            btn.closest('form').submit();
        }
    });
}
</script>
@endpush
@endsection

