@extends('layouts.admin')

@section('title', __('messages.manage') . ' ' . __('messages.vendor'))

@section('content')
<style>
    .vendor-card {
        border-radius: 16px !important;
        overflow: hidden !important;
        border: var(--rule-soft);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    .custom-search-input {
        height: 44px !important;
        border-radius: 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.22) !important;
        box-shadow: none !important;
        font-size: 0.9rem !important;
        color: var(--color-ink-0) !important;
        transition: all 0.2s ease !important;
    }
    .custom-search-input:focus {
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2) !important;
        background: rgba(15, 23, 42, 0.35) !important;
    }
    .modal-content {
        border-radius: 16px !important;
        overflow: hidden !important;
        background: var(--color-paper-0) !important;
        border: var(--rule-soft) !important;
    }

    /* Mobile Responsive Optimization */
    @media (max-width: 767.98px) {
        .vendor-toolbar-box {
            width: 100%;
        }
        .vendor-action-buttons {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 8px !important;
            width: 100% !important;
            margin-top: 10px !important;
        }
        .vendor-action-buttons .btn-add-main {
            grid-column: span 2 !important;
            order: -1 !important;
            height: 44px !important;
        }
        .vendor-action-buttons .btn {
            height: 42px !important;
            font-size: 0.88rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 12px !important;
        }
        .vendor-search-box {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            width: 100% !important;
            gap: 8px !important;
        }
        .vendor-search-input {
            flex: 1 1 auto !important;
            width: 100% !important;
            height: 44px !important;
        }
        .vendor-search-btn {
            width: 44px !important;
            height: 44px !important;
            min-width: 44px !important;
            flex-shrink: 0 !important;
            border-radius: 10px !important;
            background: #3b82f6 !important;
            color: #ffffff !important;
            border: none !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .modal-footer-actions {
            flex-direction: column-reverse;
            width: 100%;
            gap: 10px !important;
        }
        .modal-footer-actions .btn {
            width: 100% !important;
            justify-content: center !important;
            height: 44px !important;
        }
    }
</style>

<!-- Header Toolbar -->
<div class="row mb-3 vendor-toolbar-box">
    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center" style="gap: 10px;">
            <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(59, 130, 246, 0.12); border: 1px solid rgba(59, 130, 246, 0.3); display: flex; align-items: center; justify-content: center; color: #60a5fa; font-size: 1.1rem; flex-shrink: 0;">
                <i class="fas fa-store"></i>
            </div>
            <div>
                <h5 class="mb-0 font-weight-bold theme-text" style="font-size: 1.08rem; line-height: 1.2;">Manage Vendor</h5>
                <small class="text-muted d-block" style="font-size: 0.78rem;">Kelola data vendor & penyedia layanan IT</small>
            </div>
        </div>

        <div class="d-flex align-items-center vendor-action-buttons" style="gap: 8px;">
            <a href="{{ route('vendors.create') }}" class="btn btn-primary rounded-pill px-4 btn-add-main" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">
                Add Vendor
            </a>
            <button type="button" class="btn btn-outline-info rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;" data-toggle="modal" data-target="#importModal">
                Import
            </button>
            <a href="{{ route('vendors.export', request()->query()) }}" class="btn btn-outline-success rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">
                {{ __('messages.export') }}
            </a>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card theme-card vendor-card">
    <div class="card-header border-bottom py-3 px-3 px-md-4" style="border-color: rgba(148, 163, 184, 0.12) !important;">
        <form action="{{ route('vendors.index') }}" method="GET" style="width: 100%;">
            <div class="vendor-search-box">
                <input type="text" name="search" class="form-control theme-input custom-search-input vendor-search-input" placeholder="Search..." value="{{ request('search') }}" autocomplete="off">
                @if(request('search'))
                    <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="min-width: 44px; height: 44px; border-radius: 10px; border-color: rgba(148, 163, 184, 0.22);" title="Clear">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
                <button class="btn btn-primary vendor-search-btn" type="submit" title="{{ __('messages.search') }}">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="theme-scroll-container table-responsive">
            <table class="table table-striped table-hover m-0 theme-table division-table">
                <thead>
                    <tr>
                        <th width="60" class="pl-4">{{ __('messages.no') }}</th>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.phone') }}</th>
                        <th>{{ __('messages.email') }}</th>
                        <th width="120" class="text-center pr-4">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $item)
                    <tr>
                        <td class="theme-text pl-4">{{ $loop->iteration }}</td>
                        <td class="font-weight-bold theme-text">{{ $item->name }}</td>
                        <td class="theme-text">{{ $item->phone ?: '-' }}</td>
                        <td class="theme-text">{{ $item->email ?: '-' }}</td>
                        <td class="theme-text pr-4">
                            <div class="d-flex justify-content-center" style="gap: 8px;">
                                <a href="{{ route('vendors.edit', $item) }}" class="btn action-btn btn-outline-warning" style="border: 1px solid rgba(255, 193, 7, 0.3); background: rgba(255, 193, 7, 0.15); color: #ffc107;" title="{{ __('messages.edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('vendors.destroy', $item) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-delete action-btn btn-outline-danger" style="border: 1px solid rgba(220, 53, 69, 0.3); background: rgba(220, 53, 69, 0.15); color: #dc3545;" title="{{ __('messages.delete') }}" data-confirm-message="{{ __('messages.confirm_delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">{{ __('messages.no_data') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade theme-modal" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('vendors.import') }}" method="POST" enctype="multipart/form-data" style="width: 100%;">
            @csrf
            <div class="modal-content">
                <div class="modal-header py-3 px-4" style="border-color: rgba(148, 163, 184, 0.12) !important;">
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(59, 130, 246, 0.12); border: 1px solid rgba(59, 130, 246, 0.3); display: flex; align-items: center; justify-content: center; color: #60a5fa; font-size: 1rem;">
                            <i class="fas fa-file-import"></i>
                        </div>
                        <h5 class="modal-title font-weight-bold theme-text" id="importModalLabel" style="font-size: 1.05rem;">Import {{ __('messages.vendor') }}</h5>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="file-dropzone">
                        <input type="file" name="file" class="import-file-input" accept=".xlsx,.xls,.csv" required>
                        <div class="icon-box mb-2">
                            <i class="fas fa-cloud-upload-alt text-info fa-2x"></i>
                        </div>
                        <div class="file-label-text font-weight-bold theme-text mb-1">
                            Pilih file (.xlsx, .xls, .csv)
                        </div>
                        <div class="text-muted small file-name-display">
                            Sentuh atau seret file ke sini
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4 d-flex align-items-center justify-content-end modal-footer-actions" style="border-color: rgba(148, 163, 184, 0.12) !important; gap: 10px;">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;" data-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">Import</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection