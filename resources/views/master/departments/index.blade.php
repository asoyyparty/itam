@extends('layouts.admin')

@section('title', __('messages.manage') . ' ' . __('messages.department'))

@section('content')
<style>
/* Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 */
:root {
    --hl-bg-canvas: #090d16;
    --hl-bg-surface: #0f172a;
    --hl-bg-card: rgba(15, 23, 42, 0.78);
    --hl-border-subtle: rgba(148, 163, 184, 0.14);
    --hl-border-active: rgba(148, 163, 184, 0.28);
    --hl-text-bright: #f8fafc;
    --hl-text-muted: #94a3b8;
    --hl-text-dim: #64748b;
    --hl-accent-indigo: #6366f1;
    --hl-accent-cyan: #06b6d4;
    --hl-font-mono: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}

.master-header-banner {
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.92) 0%, rgba(30, 41, 59, 0.82) 100%);
    border: 1px solid var(--hl-border-subtle);
    border-radius: 16px;
    padding: 18px 22px;
    margin-bottom: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    backdrop-filter: blur(12px);
}
body.light-mode .master-header-banner {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(241, 245, 249, 0.95));
    border: 1px solid rgba(226, 232, 240, 0.9);
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
}

.master-banner-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
}

.master-banner-title-box {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
    flex: 1 1 300px;
}

.master-banner-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: rgba(99, 102, 241, 0.15);
    border: 1px solid rgba(99, 102, 241, 0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #818cf8;
    font-size: 1.25rem;
    flex-shrink: 0;
    box-shadow: 0 0 16px rgba(99, 102, 241, 0.2);
}

.master-count-pill {
    font-family: var(--hl-font-mono);
    font-size: 0.72rem;
    padding: 5px 12px;
    background: rgba(99, 102, 241, 0.12);
    border: 1px solid rgba(99, 102, 241, 0.3);
    color: #818cf8;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 700;
    letter-spacing: 0.06em;
    white-space: nowrap;
}

.live-dot-indigo {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #6366f1;
    box-shadow: 0 0 8px #6366f1;
    display: inline-block;
}

.master-banner-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-tactile-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 0.82rem;
    font-weight: 700;
    color: #ffffff !important;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    border: 1px solid rgba(129, 140, 248, 0.4);
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.3);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    text-decoration: none !important;
    white-space: nowrap;
}
.btn-tactile-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.45);
    filter: brightness(1.08);
}

.btn-tactile-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--hl-text-bright) !important;
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid var(--hl-border-active);
    transition: all 0.2s;
    text-decoration: none !important;
    white-space: nowrap;
}
.btn-tactile-secondary:hover {
    background: rgba(30, 41, 59, 0.9);
    border-color: rgba(148, 163, 184, 0.45);
    transform: translateY(-1px);
}
body.light-mode .btn-tactile-secondary {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #1e293b !important;
}

/* Master Table Card */
.master-table-card {
    border-radius: 16px !important;
    overflow: hidden !important;
    border: 1px solid var(--hl-border-subtle) !important;
    background: var(--hl-bg-card) !important;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.25);
    backdrop-filter: blur(12px);
}
body.light-mode .master-table-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
}

.master-card-header {
    background: rgba(15, 23, 42, 0.4) !important;
    border-bottom: 1px solid var(--hl-border-subtle) !important;
    padding: 14px 18px !important;
}
body.light-mode .master-card-header {
    background: #f8fafc !important;
    border-bottom-color: #e2e8f0 !important;
}

.sleek-search-wrapper {
    position: relative;
    width: 100%;
}
.sleek-search-wrapper .search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--hl-text-dim);
    font-size: 0.85rem;
    pointer-events: none;
}
.sleek-search-input {
    height: 42px !important;
    padding-left: 38px !important;
    padding-right: 38px !important;
    border-radius: 10px !important;
    border: 1px solid var(--hl-border-subtle) !important;
    background: rgba(15, 23, 42, 0.6) !important;
    color: var(--hl-text-bright) !important;
    font-size: 0.84rem !important;
    transition: all 0.2s !important;
}
.sleek-search-input:focus {
    border-color: var(--hl-accent-cyan) !important;
    box-shadow: 0 0 0 2px rgba(6, 182, 212, 0.2) !important;
    background: rgba(15, 23, 42, 0.9) !important;
}
body.light-mode .sleek-search-input {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    color: #1e293b !important;
}

/* Action Icon Buttons */
.btn-action-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.82rem;
    transition: all 0.15s;
    text-decoration: none !important;
    border: 1px solid transparent;
}
.btn-action-edit {
    background: rgba(245, 158, 11, 0.12);
    border-color: rgba(245, 158, 11, 0.25);
    color: #fbbf24 !important;
}
.btn-action-edit:hover {
    background: rgba(245, 158, 11, 0.25);
    border-color: rgba(245, 158, 11, 0.5);
    transform: translateY(-1px);
}
.btn-action-delete {
    background: rgba(244, 63, 94, 0.12);
    border-color: rgba(244, 63, 94, 0.25);
    color: #f43f5e !important;
}
.btn-action-delete:hover {
    background: rgba(244, 63, 94, 0.25);
    border-color: rgba(244, 63, 94, 0.5);
    transform: translateY(-1px);
}

/* Mobile Tactile Feed */
.master-mobile-feed {
    padding: 10px 12px;
}

.master-mobile-card {
    background: rgba(15, 23, 42, 0.7);
    border: 1px solid var(--hl-border-subtle);
    border-radius: 12px;
    padding: 12px 14px;
    margin-bottom: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    transition: border-color 0.15s;
}
body.light-mode .master-mobile-card {
    background: #ffffff;
    border-color: #e2e8f0;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
}

.master-mobile-card:last-child {
    margin-bottom: 0;
}

.master-mobile-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.master-mobile-idx {
    font-family: var(--hl-font-mono);
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--hl-text-dim);
    background: rgba(148, 163, 184, 0.1);
    padding: 2px 6px;
    border-radius: 6px;
    flex-shrink: 0;
}

.master-mobile-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(99, 102, 241, 0.12);
    color: #818cf8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    flex-shrink: 0;
}

.master-mobile-name {
    font-size: 0.94rem;
    font-weight: 700;
    color: var(--hl-text-bright);
    line-height: 1.35;
    word-break: break-word;
    overflow-wrap: anywhere;
    min-width: 0;
}
body.light-mode .master-mobile-name {
    color: #0f172a;
}

.master-mobile-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}

.master-mobile-desc {
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px solid rgba(148, 163, 184, 0.1);
    font-size: 0.8rem;
    color: var(--hl-text-muted);
    line-height: 1.4;
    word-break: break-word;
}
body.light-mode .master-mobile-desc {
    color: #64748b;
}

/* Responsive Rules */
@media (max-width: 767.98px) {
    .master-header-banner {
        padding: 14px 16px;
        border-radius: 14px;
        margin-bottom: 14px;
    }

    .master-banner-top {
        flex-direction: column !important;
        align-items: stretch !important;
        justify-content: flex-start !important;
        gap: 12px !important;
    }

    .master-banner-title-box {
        width: 100% !important;
        flex: 0 0 auto !important;
    }

    .master-banner-icon {
        width: 38px;
        height: 38px;
        font-size: 1.1rem;
    }

    .master-banner-actions {
        width: 100%;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .master-banner-actions .btn-tactile-secondary {
        width: 100%;
        height: 38px;
        font-size: 0.78rem;
    }

    .master-banner-actions .btn-tactile-primary {
        grid-column: 1 / -1;
        width: 100%;
        height: 42px;
        font-size: 0.84rem;
        order: 3;
    }

    .master-card-header {
        padding: 12px 14px !important;
    }

    .btn-action-icon {
        width: 36px;
        height: 36px;
        font-size: 0.88rem;
    }
}
</style>

<!-- Hallmark Master Header Banner -->
<div class="master-header-banner">
    <div class="master-banner-top">
        <div class="master-banner-title-box">
            <div class="master-banner-icon">
                <i class="fas fa-sitemap"></i>
            </div>
            <div style="flex: 1; min-width: 0;">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h5 class="mb-0 font-weight-bold theme-text text-truncate" style="font-size: 1.12rem; line-height: 1.2;">
                        {{ __('messages.manage') }} {{ __('messages.department') }}
                    </h5>
                    <span class="master-count-pill">
                        <span class="live-dot-indigo"></span>
                        {{ count($departments) }} {{ __('messages.recorded') ?? 'RECORDED' }}
                    </span>
                </div>
                <small class="text-muted d-block" style="font-size: 0.78rem; margin-top: 3px; line-height: 1.35;">
                    {{ __('messages.department_subtitle') }}
                </small>
            </div>
        </div>

        <div class="master-banner-actions">
            <button type="button" class="btn btn-tactile-secondary" data-toggle="modal" data-target="#importModal" title="{{ __('messages.import') }}">
                <i class="fas fa-file-import mr-1"></i>
                <span>{{ __('messages.import') }}</span>
            </button>
            <a href="{{ route('departments.export', request()->query()) }}" class="btn btn-tactile-secondary" title="{{ __('messages.export') }}">
                <i class="fas fa-file-export mr-1"></i>
                <span>{{ __('messages.export') }}</span>
            </a>
            <a href="{{ route('departments.create') }}" class="btn btn-tactile-primary" title="{{ __('messages.add_department') }}">
                <i class="fas fa-plus mr-1"></i>
                <span>{{ __('messages.add_department') }}</span>
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; border-radius: 12px;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; border-radius: 12px;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
    </div>
@endif

<!-- Main Table Card -->
<div class="card theme-card master-table-card">
    <div class="card-header border-0 master-card-header">
        <form action="{{ route('departments.index') }}" method="GET" class="w-100 m-0">
            <div class="sleek-search-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" class="form-control theme-input sleek-search-input" placeholder="{{ __('messages.search_department_placeholder') }}" value="{{ request('search') }}" autocomplete="off">
                @if(request('search'))
                    <a href="{{ route('departments.index') }}" class="clear-search-btn" title="Clear">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <!-- Desktop Table View (>= 768px) -->
        <div class="d-none d-md-block theme-scroll-container table-responsive">
            <table class="table table-striped table-hover m-0 theme-table division-table">
                <thead>
                    <tr style="font-family: var(--hl-font-mono); font-size: 0.72rem; letter-spacing: 0.08em; text-transform: uppercase;">
                        <th width="70" class="pl-4">{{ __('messages.no') }}</th>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.description') }}</th>
                        <th width="120" class="text-center pr-4">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $item)
                    <tr>
                        <td class="theme-text pl-4 font-weight-bold" style="font-family: var(--hl-font-mono); font-size: 0.85rem;">{{ $loop->iteration }}</td>
                        <td class="font-weight-bold theme-text" style="font-size: 0.92rem;">
                            <div class="d-flex align-items-center" style="gap: 10px;">
                                <div style="width: 30px; height: 30px; border-radius: 8px; background: rgba(99, 102, 241, 0.12); color: #818cf8; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; flex-shrink: 0;">
                                    <i class="fas fa-building"></i>
                                </div>
                                <span style="line-height: 1.35; word-break: break-word;">{{ $item->name }}</span>
                            </div>
                        </td>
                        <td class="theme-text text-muted" style="font-size: 0.85rem; line-height: 1.4;">{{ $item->description ?: '-' }}</td>
                        <td class="theme-text pr-4">
                            <div class="d-flex justify-content-center" style="gap: 8px;">
                                <a href="{{ route('departments.edit', $item) }}" class="btn-action-icon btn-action-edit" title="{{ __('messages.edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('departments.destroy', $item) }}" method="POST" class="d-inline m-0">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-action-icon btn-action-delete btn-delete border-0" title="{{ __('messages.delete') }}" data-confirm-message="{{ __('messages.confirm_delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">{{ __('messages.no_data') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Tactile Feed View (< 768px) - Zero Truncation -->
        <div class="d-block d-md-none master-mobile-feed">
            @forelse($departments as $item)
            <div class="master-mobile-card">
                <div class="master-mobile-header">
                    <div class="d-flex align-items-center" style="gap: 10px; min-width: 0; flex: 1;">
                        <span class="master-mobile-idx">#{{ $loop->iteration }}</span>
                        <div class="master-mobile-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="master-mobile-name">
                            {{ $item->name }}
                        </div>
                    </div>
                    <div class="master-mobile-actions">
                        <a href="{{ route('departments.edit', $item) }}" class="btn-action-icon btn-action-edit" title="{{ __('messages.edit') }}">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('departments.destroy', $item) }}" method="POST" class="d-inline m-0">
                            @csrf @method('DELETE')
                            <button type="button" class="btn-action-icon btn-action-delete btn-delete border-0" title="{{ __('messages.delete') }}" data-confirm-message="{{ __('messages.confirm_delete') }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @if($item->description)
                <div class="master-mobile-desc">
                    {{ $item->description }}
                </div>
                @endif
            </div>
            @empty
            <div class="text-center text-muted py-5">
                <i class="fas fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                {{ __('messages.no_data') }}
            </div>
            @endforelse
        </div>
    </div>
</div>

@if(method_exists($departments, 'links'))
    <div class="d-flex justify-content-end mt-3">
        {{ $departments->links() }}
    </div>
@endif

<!-- Import Modal -->
<div class="modal fade theme-modal" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('departments.import') }}" method="POST" enctype="multipart/form-data" style="width: 100%;">
            @csrf
            <div class="modal-content" style="border-radius: 16px; background: rgba(15, 23, 42, 0.98); border: 1px solid var(--hl-border-active); box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);">
                <div class="modal-header py-3 px-4" style="border-color: rgba(148, 163, 184, 0.12) !important;">
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 1.1rem;">
                            <i class="fas fa-file-import"></i>
                        </div>
                        <h5 class="modal-title font-weight-bold theme-text" id="importModalLabel" style="font-size: 1.05rem;">Import {{ __('messages.department') }}</h5>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="file-dropzone" style="border: 2px dashed rgba(99, 102, 241, 0.3); border-radius: 14px; padding: 24px; text-align: center; background: rgba(15, 23, 42, 0.3);">
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
                <div class="modal-footer border-top py-3 px-4 d-flex align-items-center justify-content-end" style="border-color: rgba(148, 163, 184, 0.12) !important; gap: 10px;">
                    <button type="button" class="btn btn-tactile-secondary" data-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-tactile-primary">Import</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
