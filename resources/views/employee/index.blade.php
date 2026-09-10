@extends('layouts.admin')

@section('title', __('messages.manage') . ' ' . __('messages.employee'))

@section('content')
<style>
    /* Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 */
    .master-header-banner {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(30, 41, 59, 0.85));
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 20px;
        padding: 18px 24px;
        margin-top: 0 !important;
        margin-bottom: 18px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.22);
    }
    body.light-mode .master-header-banner {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(241, 245, 249, 0.88));
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
    }

    /* Quick Status Ribbon (Bento) */
    .quick-status-ribbon {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 18px;
        overflow-x: auto;
        padding-bottom: 4px;
        -webkit-overflow-scrolling: touch;
    }
    .status-ribbon-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 999px;
        font-size: 0.84rem;
        font-weight: 600;
        background: rgba(15, 23, 42, 0.35);
        border: 1px solid rgba(148, 163, 184, 0.18);
        color: var(--color-ink-1);
        text-decoration: none !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        white-space: nowrap;
        user-select: none;
    }
    body.light-mode .status-ribbon-pill {
        background: #f8fafc;
        border-color: #e2e8f0;
        color: var(--color-ink-1);
    }
    .status-ribbon-pill:hover {
        transform: translateY(-1px);
        border-color: rgba(99, 102, 241, 0.5);
        color: var(--color-ink-0);
        box-shadow: 0 3px 12px rgba(15, 23, 42, 0.1);
    }
    .status-ribbon-pill.active {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.22), rgba(79, 70, 229, 0.14));
        border-color: #6366f1;
        color: #ffffff;
        box-shadow: 0 0 14px rgba(99, 102, 241, 0.25);
    }
    body.light-mode .status-ribbon-pill.active {
        background: #4f46e5;
        color: #ffffff;
        border-color: #4338ca;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
    }
    .status-ribbon-count {
        font-family: var(--font-mono);
        font-size: 0.74rem;
        padding: 2px 8px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        font-weight: 700;
    }
    body.light-mode .status-ribbon-pill:not(.active) .status-ribbon-count {
        background: #e2e8f0;
        color: #1e293b;
    }

    .master-table-card {
        border-radius: 20px !important;
        overflow: hidden !important;
        border: 1px solid rgba(148, 163, 184, 0.14) !important;
        background: rgba(15, 23, 42, 0.85) !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    }
    body.light-mode .master-table-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
    }

    .master-table-card .card-header {
        background: transparent !important;
        border-bottom: 1px solid rgba(148, 163, 184, 0.1) !important;
        padding: 16px 20px !important;
    }
    body.light-mode .master-table-card .card-header {
        border-bottom: 1px solid #f1f5f9 !important;
    }

    .employee-filter-container {
        display: flex !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 12px !important;
        width: 100% !important;
    }
    .custom-filter-select {
        height: 44px !important;
        border-radius: 12px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background-color: rgba(15, 23, 42, 0.3) !important;
        color: var(--color-ink-0) !important;
        font-size: 0.88rem !important;
        padding: 0 32px 0 14px !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        background-repeat: no-repeat !important;
        background-position: right 12px center !important;
        background-size: 14px 14px !important;
        transition: all 0.2s ease !important;
        width: auto !important;
        min-width: 150px !important;
        max-width: 200px !important;
        flex: 0 1 180px !important;
        margin: 0 !important;
    }
    body.light-mode .custom-filter-select {
        background-color: #ffffff !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }
    .custom-filter-select:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
    }
    .employee-filter-container .sleek-search-wrapper {
        min-width: 220px !important;
        flex: 1 1 240px !important;
        margin: 0 !important;
        height: 44px !important;
    }
    .employee-filter-container .sleek-search-input {
        height: 44px !important;
        border-radius: 12px !important;
    }

    .modal-content {
        border-radius: 20px !important;
        overflow: hidden !important;
        background: #0f172a !important;
        border: 1px solid rgba(148, 163, 184, 0.18) !important;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5) !important;
    }
    body.light-mode .modal-content {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
    }

    @media (max-width: 767.98px) {
        .master-header-banner {
            padding: 16px !important;
            margin-bottom: 16px !important;
        }
        .master-header-banner > .d-flex {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 16px !important;
        }
        .employee-action-buttons {
            width: 100% !important;
            margin-left: 0 !important;
            justify-content: space-between !important;
            margin-top: 4px !important;
            gap: 10px !important;
            display: flex !important;
            flex-wrap: wrap !important;
        }
        .employee-action-buttons > span {
            flex: 0 0 auto !important;
            height: 38px !important;
            display: inline-flex !important;
            align-items: center !important;
        }
        .employee-action-buttons .btn-action-secondary {
            flex: 1 1 auto !important;
            height: 40px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 0.85rem !important;
        }
        .employee-action-buttons .btn-add-main,
        .employee-action-buttons .btn-action-primary {
            width: 100% !important;
            flex: 1 1 100% !important;
            height: 44px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 0.9rem !important;
            order: 10 !important;
            margin-top: 4px !important;
        }
        .employee-filter-container {
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
            width: 100% !important;
            gap: 10px !important;
        }
        .custom-filter-select {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 auto !important;
            height: 46px !important;
            border-radius: 12px !important;
            border: 1px solid var(--rule-soft, rgba(148, 163, 184, 0.22)) !important;
            margin: 0 !important;
            font-size: 0.95rem !important;
        }
        .employee-filter-container .sleek-search-wrapper {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 auto !important;
            margin: 0 !important;
            height: 46px !important;
        }
        .employee-filter-container .sleek-search-input {
            height: 46px !important;
            font-size: 0.95rem !important;
        }
        .employee-table {
            min-width: 760px !important;
        }
    }

    .status-btn::after {
        display: none !important;
    }
</style>

<!-- Hallmark Master Header Banner -->
<div class="master-header-banner">
    <div class="d-flex align-items-center justify-content-between flex-wrap flex-md-nowrap" style="gap: 16px;">
        <div class="d-flex align-items-center" style="gap: 14px; min-width: 0; flex: 1;">
            <div style="width: 44px; height: 44px; border-radius: 14px; background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(79, 70, 229, 0.08)); border: 1px solid rgba(99, 102, 241, 0.35); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 1.2rem; flex-shrink: 0; box-shadow: 0 0 14px rgba(99, 102, 241, 0.25);">
                <i class="fas fa-id-badge"></i>
            </div>
            <div style="flex: 1; min-width: 0;">
                <h5 class="mb-0 font-weight-bold theme-text text-truncate" style="font-size: 1.15rem; line-height: 1.2;">{{ __('messages.manage') }} {{ __('messages.employee') }}</h5>
                <small class="text-muted d-block" style="font-size: 0.8rem; margin-top: 2px; white-space: normal; line-height: 1.3;">{{ __('messages.employee_subtitle') }}</small>
            </div>
        </div>

        <div class="d-flex align-items-center flex-wrap flex-md-nowrap employee-action-buttons ml-auto" style="gap: 10px; flex-shrink: 0;">
            <span style="font-family: var(--font-mono); font-size: 0.72rem; padding: 7px 14px; background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.25); color: #818cf8; border-radius: 999px; display: inline-flex; align-items: center; gap: 6px; font-weight: 600; white-space: nowrap;">
                <span class="live-dot" style="width: 7px; height: 7px; border-radius: 50%; background: #6366f1; box-shadow: 0 0 8px #6366f1; display: inline-block;"></span>
                {{ $employees->total() }} RECORDED
            </span>
            @can('action_manage_employees')
            <button type="button" class="btn btn-sm btn-action-secondary text-nowrap" data-toggle="modal" data-target="#importModal" title="{{ __('messages.import') }}">
                <i class="fas fa-file-import mr-1"></i> {{ __('messages.import') }}
            </button>
            @endcan
            <a href="{{ route('employees.export', request()->query()) }}" class="btn btn-sm btn-action-secondary text-nowrap" title="{{ __('messages.export') }}">
                <i class="fas fa-file-export mr-1"></i> {{ __('messages.export') }}
            </a>
            @can('action_manage_employees')
            <a href="{{ route('employees.create') }}" class="btn btn-sm btn-action-primary text-nowrap btn-add-main" title="{{ __('messages.add') }} {{ __('messages.employee') }}">
                <i class="fas fa-user-plus mr-1"></i> {{ __('messages.add') }} {{ __('messages.employee') }}
            </a>
            @endcan
        </div>
    </div>
</div>

<!-- Hallmark Quick Status Ribbon -->
<div class="quick-status-ribbon">
    <a href="{{ route('employees.index', request()->except(['status', 'page'])) }}" class="status-ribbon-pill {{ !request('status') ? 'active' : '' }}">
        <span>{{ __('messages.all') ?? 'Semua' }}</span>
        <span class="status-ribbon-count">{{ $employees->total() }}</span>
    </a>
    <a href="{{ route('employees.index', array_merge(request()->except(['status', 'page']), ['status' => 'Active'])) }}" class="status-ribbon-pill {{ request('status') == 'Active' ? 'active' : '' }}">
        <span style="width: 7px; height: 7px; border-radius: 50%; background: #10b981; box-shadow: 0 0 6px #10b981; display: inline-block;"></span>
        <span>{{ __('messages.active') }}</span>
    </a>
    <a href="{{ route('employees.index', array_merge(request()->except(['status', 'page']), ['status' => 'Inactive'])) }}" class="status-ribbon-pill {{ request('status') == 'Inactive' ? 'active' : '' }}">
        <span style="width: 7px; height: 7px; border-radius: 50%; background: #ef4444; box-shadow: 0 0 6px #ef4444; display: inline-block;"></span>
        <span>{{ __('messages.inactive') }}</span>
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; border-radius: 14px;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; border-radius: 14px;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
    </div>
@endif

<!-- Main Table Card -->
<div class="card theme-card master-table-card">
    <div class="card-header border-0 py-3 px-3 px-md-4" style="border-bottom: 1px solid rgba(148, 163, 184, 0.12) !important;">
        <form action="{{ route('employees.index') }}" method="GET" style="width: 100%;">
            <div class="d-flex align-items-center flex-wrap gap-2 employee-filter-container">
                <select name="department_id" class="form-control custom-filter-select" onchange="this.form.submit()">
                    <option value="">{{ __('messages.all_department') }}</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>

                <select name="supervisor_id" class="form-control custom-filter-select" onchange="this.form.submit()">
                    <option value="">{{ __('messages.all_supervisor') }}</option>
                    @foreach($supervisors as $sup)
                        <option value="{{ $sup->id }}" {{ request('supervisor_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                    @endforeach
                </select>

                <select name="status" class="form-control custom-filter-select" onchange="this.form.submit()">
                    <option value="">{{ __('messages.all_status') }}</option>
                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                    <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                </select>

                <div class="sleek-search-wrapper flex-grow-1" style="min-width: 220px;">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" class="form-control theme-input sleek-search-input" placeholder="{{ __('messages.search_employee') }}" value="{{ request('search') }}" autocomplete="off">
                    @if(request('search') || request('department_id') || request('supervisor_id') || request('status'))
                        <a href="{{ route('employees.index') }}" class="clear-search-btn" title="{{ __('messages.clear') }}">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="theme-scroll-container table-responsive">
            <table class="table table-striped table-hover m-0 theme-table division-table employee-table">
                <thead>
                    <tr style="font-family: var(--font-mono); font-size: 0.72rem; letter-spacing: 0.08em; text-transform: uppercase;">
                        <th width="60" class="pl-4">{{ __('messages.no') }}</th>
                        <th>{{ __('messages.nik') }}</th>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.email') }}</th>
                        <th>{{ __('messages.anydesk_id') ?? 'AnyDesk ID' }}</th>
                        <th>{{ __('messages.anydesk_password') }}</th>
                        <th>{{ __('messages.pc_username') }}</th>
                        <th>{{ __('messages.pc_password') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th width="140" class="text-center pr-4">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    <tr>
                        <td class="theme-text pl-4 font-weight-bold" style="font-family: var(--font-mono); font-size: 0.85rem;">{{ $employees->firstItem() + $loop->index }}</td>
                        <td>
                            <span style="font-family: var(--font-mono); font-size: 0.84rem; padding: 4px 10px; background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.25); color: #38bdf8; border-radius: 8px; font-weight: 600; display: inline-block;">
                                {{ $emp->employee_id }}
                            </span>
                        </td>
                        <td class="font-weight-bold theme-text" style="font-size: 0.92rem;">
                            <a href="{{ route('employees.show', $emp) }}" class="theme-text text-decoration-none hover-highlight">
                                <div class="d-flex align-items-center" style="gap: 8px;">
                                    <div style="width: 28px; height: 28px; border-radius: 50%; background: rgba(99, 102, 241, 0.15); color: #818cf8; display: flex; align-items: center; justify-content: center; font-size: 0.78rem; flex-shrink: 0; font-weight: 700;">
                                        {{ strtoupper(substr($emp->name, 0, 1)) }}
                                    </div>
                                    <span>{{ $emp->name }}</span>
                                </div>
                            </a>
                        </td>
                        <td class="theme-text text-muted" style="font-size: 0.85rem;">{{ $emp->email ?? '-' }}</td>
                        <td class="theme-text" style="font-size: 0.85rem;">
                            @if($emp->anydesk_id)
                                <code style="padding: 3px 8px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.25); color: #34d399; border-radius: 6px; font-family: var(--font-mono);">{{ $emp->anydesk_id }}</code>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="theme-text" style="font-size: 0.85rem;">
                            @if($emp->anydesk_password)
                                <div class="d-flex align-items-center" style="gap: 6px;">
                                    <code class="sensitive-value" data-value="{{ $emp->anydesk_password }}" style="padding: 3px 8px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.25); color: #34d399; border-radius: 6px; font-family: var(--font-mono);">******</code>
                                    <button type="button" class="btn btn-sm p-0 sensitive-toggle text-muted" style="background: none; border: none; font-size: 0.82rem;" title="{{ __('messages.toggle_password') }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="theme-text" style="font-size: 0.85rem;">
                            @if($emp->login_username)
                                <code style="padding: 3px 8px; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.25); color: #fbbf24; border-radius: 6px; font-family: var(--font-mono);">{{ $emp->login_username }}</code>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="theme-text" style="font-size: 0.85rem;">
                            @if($emp->login_password)
                                <div class="d-flex align-items-center" style="gap: 6px;">
                                    <code class="sensitive-value" data-value="{{ $emp->login_password }}" style="padding: 3px 8px; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.25); color: #fbbf24; border-radius: 6px; font-family: var(--font-mono);">******</code>
                                    <button type="button" class="btn btn-sm p-0 sensitive-toggle text-muted" style="background: none; border: none; font-size: 0.82rem;" title="{{ __('messages.toggle_password') }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="theme-text">
                            <div class="dropdown">
                                <button class="btn btn-sm dropdown-toggle status-btn p-0 border-0 bg-transparent" type="button" data-toggle="dropdown" aria-expanded="false" data-id="{{ $emp->id }}" style="box-shadow: none;">
                                    @if($emp->status == 'Active')
                                        <span class="badge status-badge" style="padding: 5px 12px; border-radius: 999px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; font-weight: 600; font-size: 0.75rem;">
                                            <i class="fas fa-circle mr-1" style="font-size: 0.5rem; vertical-align: middle;"></i> {{ __('messages.active') }}
                                        </span>
                                    @else
                                        <span class="badge status-badge" style="padding: 5px 12px; border-radius: 999px; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; font-weight: 600; font-size: 0.75rem;">
                                            <i class="fas fa-circle mr-1" style="font-size: 0.5rem; vertical-align: middle;"></i> {{ __('messages.inactive') }}
                                        </span>
                                    @endif
                                </button>
                                @can('action_manage_employees')
                                <div class="dropdown-menu dropdown-menu-right" style="background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.15); border-radius: 12px;">
                                    <a class="dropdown-item status-change-btn text-success" href="#" data-status="Active"><i class="fas fa-check mr-2"></i> {{ __('messages.active') }}</a>
                                    <a class="dropdown-item status-change-btn text-danger" href="#" data-status="Inactive"><i class="fas fa-ban mr-2"></i> {{ __('messages.inactive') }}</a>
                                </div>
                                @endcan
                            </div>
                        </td>
                        <td class="theme-text pr-4">
                            <div class="d-flex justify-content-center" style="gap: 6px;">
                                <a href="{{ route('employees.show', $emp) }}" class="btn action-btn btn-outline-info" style="border: 1px solid rgba(56, 189, 248, 0.3); background: rgba(56, 189, 248, 0.15); color: #38bdf8;" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('action_manage_employees')
                                <a href="{{ route('employees.edit', array_merge([$emp->id], request()->query())) }}" class="btn action-btn btn-outline-warning" style="border: 1px solid rgba(245, 158, 11, 0.3); background: rgba(245, 158, 11, 0.15); color: #fbbf24;" title="{{ __('messages.edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('employees.destroy', array_merge([$emp->id], request()->query())) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-delete action-btn btn-outline-danger" style="border: 1px solid rgba(239, 68, 68, 0.3); background: rgba(239, 68, 68, 0.15); color: #f87171;" title="{{ __('messages.delete') }}" data-confirm-message="{{ __('messages.confirm_delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-5">{{ __('messages.no_data') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($employees->hasPages())
            <div class="card-footer d-flex justify-content-end align-items-center py-3 px-4" style="background: transparent; border-top: 1px solid rgba(148, 163, 184, 0.1);">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Import Modal -->
@can('action_manage_employees')
<div class="modal fade theme-modal" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data" style="width: 100%;">
            @csrf
            <div class="modal-content">
                <div class="modal-header py-3 px-4" style="border-color: rgba(148, 163, 184, 0.12) !important;">
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <div style="width: 38px; height: 38px; border-radius: 12px; background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 1.1rem;">
                            <i class="fas fa-file-import"></i>
                        </div>
                        <h5 class="modal-title font-weight-bold theme-text" id="importModalLabel" style="font-size: 1.05rem;">Import {{ __('messages.employee') }}</h5>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="file-dropzone" style="border: 2px dashed rgba(99, 102, 241, 0.3); border-radius: 16px; padding: 24px; text-align: center; background: rgba(15, 23, 42, 0.3);">
                        <input type="file" name="file" class="import-file-input" accept=".xlsx,.xls,.csv" required>
                        <div class="icon-box mb-2">
                            <i class="fas fa-cloud-upload-alt text-info fa-2x"></i>
                        </div>
                        <div class="file-label-text font-weight-bold theme-text mb-1">
                            {{ __('messages.import_excel') }} (.xlsx, .xls, .csv)
                        </div>
                        <div class="text-muted small file-name-display">
                            {{ __('messages.select_file_hint') }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4 d-flex align-items-center justify-content-end" style="border-color: rgba(148, 163, 184, 0.12) !important; gap: 10px;">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;" data-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #4f46e5, #6366f1); border: none;">Import</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan

@push('scripts')
<script>
    var fileInput = document.querySelector('.import-file-input');
    if (fileInput) {
        fileInput.addEventListener('change', function(e){
            var fileName = e.target.files[0].name;
            var nextSibling = e.target.nextElementSibling;
            if (nextSibling && nextSibling.classList.contains('file-label-text')) {
                nextSibling.innerText = fileName;
            }
        });
    }

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
            url: '{{ url("employees") }}/' + id + '/status',
            type: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                if(response.success) {
                    if (newStatus === 'Active') {
                        badge.css({
                            'background': 'rgba(16, 185, 129, 0.15)',
                            'border': '1px solid rgba(16, 185, 129, 0.3)',
                            'color': '#34d399'
                        });
                        badge.html('<i class="fas fa-circle mr-1" style="font-size: 0.5rem; vertical-align: middle;"></i> {{ __("messages.active") }}');
                    } else {
                        badge.css({
                            'background': 'rgba(239, 68, 68, 0.15)',
                            'border': '1px solid rgba(239, 68, 68, 0.3)',
                            'color': '#f87171'
                        });
                        badge.html('<i class="fas fa-circle mr-1" style="font-size: 0.5rem; vertical-align: middle;"></i> {{ __("messages.inactive") }}');
                    }
                }
            },
            error: function(xhr) {
                Swal.fire({ icon: 'error', text: 'Error updating status.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, background: '#0f172a', color: '#f8fafc', customClass: { popup: 'border border-danger' } });
                badge.html(originalHtml);
            }
        });
    });

    $(document).on('click', '.sensitive-toggle', function() {
        var toggle = $(this);
        var value = toggle.prev('.sensitive-value');
        var isHidden = value.text() === '******';
        value.text(isHidden ? value.data('value') : '******');
        toggle.find('i').attr('class', isHidden ? 'fas fa-eye-slash' : 'fas fa-eye');
    });
});
</script>
@endpush
@endsection
