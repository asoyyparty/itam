@extends('layouts.admin')

@section('title', __('messages.document_vault') ?? 'Arsip Dokumen Digital')

@section('content')
<!-- Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 -->
<!-- Hallmark · genre: modern-minimal · macrostructure: workbench · design-system: design.md · designed-as-app -->
<style>
    .doc-index-container {
        padding-bottom: 50px;
    }
    .doc-card {
        background: var(--color-paper-1, #1e293b);
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }
    .doc-stat-card {
        background: var(--color-paper-1, #1e293b);
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 16px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 180ms ease, box-shadow 180ms ease;
    }
    .doc-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
    }
    .doc-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .doc-input {
        background: rgba(15, 23, 42, 0.35);
        border: 1px solid rgba(148, 163, 184, 0.22);
        border-radius: 10px;
        color: var(--color-ink-0, #f8fafc);
        font-size: 0.84rem;
        padding: 7px 11px;
        height: 40px;
        width: 100%;
    }
    .doc-input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
        outline: none;
    }
    .doc-btn-action {
        height: 40px;
        padding: 0 18px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.84rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 180ms cubic-bezier(0.16, 1, 0.3, 1);
        border: none;
        cursor: pointer;
        text-decoration: none !important;
        white-space: nowrap;
    }
    .doc-btn-primary {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
    }
    .doc-btn-primary:hover {
        background: linear-gradient(135deg, #4338ca, #4f46e5);
        color: #ffffff;
        transform: translateY(-1px);
    }
    .doc-btn-secondary {
        background: rgba(148, 163, 184, 0.15);
        color: var(--color-ink-0, #f8fafc);
        border: 1px solid rgba(148, 163, 184, 0.25);
    }
    .doc-btn-secondary:hover {
        background: rgba(148, 163, 184, 0.25);
        color: var(--color-ink-0, #f8fafc);
    }

    /* Folder Cards Grid */
    .folder-card {
        background: rgba(15, 23, 42, 0.45);
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 14px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.18s ease;
        text-decoration: none !important;
    }
    .folder-card:hover {
        background: rgba(99, 102, 241, 0.12);
        border-color: rgba(99, 102, 241, 0.35);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }
    .folder-icon-box {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    /* Breadcrumbs Bar */
    .folder-breadcrumb-bar {
        background: rgba(15, 23, 42, 0.35);
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 12px;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .folder-breadcrumb-item {
        color: #818cf8;
        font-weight: 600;
        font-size: 0.88rem;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .folder-breadcrumb-item:hover {
        color: #a5b4fc;
    }
    .folder-breadcrumb-item.active {
        color: var(--color-ink-0, #f8fafc);
        font-weight: 700;
        cursor: default;
    }
    .folder-breadcrumb-sep {
        color: #64748b;
        font-size: 0.75rem;
        margin: 0 6px;
    }

    /* Date Range Pill Component */
    .ba-daterange-pill {
        display: inline-flex;
        align-items: center;
        background: rgba(15, 23, 42, 0.45);
        border: 1px solid rgba(148, 163, 184, 0.22);
        border-radius: 12px;
        padding: 3px 8px;
        gap: 6px;
        height: 40px;
        width: 100%;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .ba-daterange-pill:focus-within {
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    }
    .ba-daterange-pill i {
        color: #818cf8;
        font-size: 0.88rem;
        margin-left: 4px;
        flex-shrink: 0;
    }
    .ba-daterange-pill input[type="date"] {
        background: transparent !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        color: var(--color-ink-0, #f8fafc) !important;
        font-size: 0.8rem !important;
        font-family: inherit;
        padding: 0 4px !important;
        height: 32px !important;
        width: 100%;
        min-width: 0;
    }
    .ba-daterange-pill input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(0.8);
        cursor: pointer;
        opacity: 0.7;
    }

    /* Table Action Buttons */
    .doc-action-btn {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        transition: all 0.15s ease;
        border: 1px solid transparent;
        text-decoration: none !important;
        cursor: pointer;
    }
    .doc-action-preview {
        background: rgba(99, 102, 241, 0.12);
        color: #818cf8;
        border-color: rgba(99, 102, 241, 0.25);
    }
    .doc-action-preview:hover {
        background: #6366f1;
        color: #ffffff;
    }
    .doc-action-download {
        background: rgba(16, 185, 129, 0.12);
        color: #34d399;
        border-color: rgba(16, 185, 129, 0.25);
    }
    .doc-action-download:hover {
        background: #10b981;
        color: #ffffff;
    }
    .doc-action-delete {
        background: rgba(239, 68, 68, 0.12);
        color: #f87171;
        border-color: rgba(239, 68, 68, 0.25);
    }
    .doc-action-delete:hover {
        background: #ef4444;
        color: #ffffff;
    }

    /* Upload Dropzone */
    .doc-dropzone {
        border: 2px dashed rgba(99, 102, 241, 0.4);
        border-radius: 14px;
        background: rgba(15, 23, 42, 0.35);
        padding: 30px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .doc-dropzone:hover, .doc-dropzone.dragover {
        border-color: #818cf8;
        background: rgba(99, 102, 241, 0.08);
    }
    .doc-dropzone-icon {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background: rgba(99, 102, 241, 0.15);
        color: #818cf8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 12px;
    }

    /* Authentic Excel Spreadsheet Simulator Styling */
    .excel-modal-dialog {
        max-width: 95vw !important;
        width: 95% !important;
        margin: 15px auto !important;
    }
    .excel-spreadsheet-container {
        background: #0f172a;
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 640px;
        width: 100%;
    }
    .excel-sheet-tabs-bar {
        background: #1e293b;
        border-bottom: 1px solid rgba(148, 163, 184, 0.15);
        padding: 8px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .excel-tab-btn {
        background: transparent;
        border: none;
        color: #94a3b8;
        font-size: 0.82rem;
        font-weight: 600;
        padding: 6px 16px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .excel-tab-btn:hover {
        background: rgba(255, 255, 255, 0.06);
        color: #f8fafc;
    }
    .excel-tab-btn.active {
        background: rgba(16, 124, 65, 0.25);
        color: #34d399;
        font-weight: 700;
        box-shadow: inset 0 -3px 0 #107c41;
    }
    .excel-table-scroll {
        flex-grow: 1;
        overflow: auto;
        background: #0f172a;
    }
    .excel-grid-table {
        border-collapse: separate;
        border-spacing: 0;
        font-family: 'Segoe UI', Tahoma, monospace, sans-serif;
        font-size: 0.83rem;
        color: #f8fafc;
        width: max-content;
        min-width: 100%;
    }
    .excel-grid-table th, .excel-grid-table td {
        border-right: 1px solid rgba(148, 163, 184, 0.18);
        border-bottom: 1px solid rgba(148, 163, 184, 0.18);
        padding: 5px 12px;
        white-space: nowrap;
    }
    .excel-col-header {
        background: #1e293b !important;
        color: #94a3b8 !important;
        font-weight: 700 !important;
        text-align: center !important;
        position: sticky;
        top: 0;
        z-index: 10;
        font-size: 0.78rem;
        user-select: none;
    }
    .excel-row-header {
        background: #1e293b !important;
        color: #94a3b8 !important;
        font-weight: 700 !important;
        text-align: center !important;
        position: sticky;
        left: 0;
        z-index: 9;
        font-size: 0.76rem;
        min-width: 42px;
        user-select: none;
    }
    .excel-corner-header {
        background: #0f172a !important;
        position: sticky;
        top: 0;
        left: 0;
        z-index: 20;
        min-width: 42px;
    }
    .excel-cell {
        background: rgba(15, 23, 42, 0.6);
    }
    .excel-grid-table tr:hover .excel-cell {
        background: rgba(16, 124, 65, 0.18) !important;
    }
    .excel-status-bar {
        background: #0d462b;
        color: #a7f3d0;
        font-size: 0.75rem;
        padding: 5px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-weight: 600;
    }

    /* Word Document Preview Paper Styling */
    .word-preview-paper {
        background: #ffffff;
        color: #1e293b;
        padding: 40px 50px;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        max-width: 850px;
        margin: 0 auto;
        font-family: 'Calibri', 'Segoe UI', 'Arial', sans-serif;
        font-size: 1rem;
        line-height: 1.6;
        min-height: 500px;
        max-height: 580px;
        overflow-y: auto;
    }
    .word-preview-paper h1, .word-preview-paper h2, .word-preview-paper h3 {
        color: #0f172a;
        margin-top: 1.2rem;
        margin-bottom: 0.6rem;
    }
    .word-preview-paper table {
        border-collapse: collapse;
        width: 100%;
        margin: 1rem 0;
    }
    .word-preview-paper th, .word-preview-paper td {
        border: 1px solid #cbd5e1;
        padding: 8px 12px;
    }

    /* Zip Archive Inspector Styling */
    .zip-preview-container {
        background: #0f172a;
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 12px;
        padding: 20px;
        max-width: 900px;
        margin: 0 auto;
        width: 100%;
    }
    .zip-file-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.1);
        border-radius: 6px;
        transition: background 0.15s ease;
    }
    .zip-file-item:hover {
        background: rgba(99, 102, 241, 0.12);
    }

    /* SweetAlert2 rounded modal override */
    .swal2-minimal-rounded {
        border-radius: 18px !important;
        background: var(--color-paper-1, #1e293b) !important;
        color: var(--color-ink-0, #f8fafc) !important;
        border: 1px solid rgba(148, 163, 184, 0.2) !important;
    }
    .swal2-minimal-rounded .swal2-title {
        color: var(--color-ink-0, #f8fafc) !important;
        font-size: 1.15rem !important;
    }
</style>

<div class="container-fluid doc-index-container">
    <!-- Page Header & Action Buttons -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4" style="gap: 16px;">
        <div>
            <h1 class="h3 font-weight-bold mb-1 text-white">
                <i class="fas fa-folder-open text-primary mr-2"></i> {{ __('messages.document_vault') ?? 'Arsip Dokumen Digital' }}
            </h1>
            <p class="text-muted small mb-0">
                Penyimpanan terorganisir dokumen hasil scan, folder direktori, dan kategori dokumen ITAM.
            </p>
        </div>
        @can('menu_documents')
        <div class="d-flex flex-wrap align-items-center" style="gap: 10px;">
            <button type="button" class="doc-btn-action doc-btn-secondary" data-toggle="modal" data-target="#createFolderModal">
                <i class="fas fa-folder-plus text-warning"></i>
                <span>+ Folder Baru</span>
            </button>
            <button type="button" class="doc-btn-action doc-btn-secondary" data-toggle="modal" data-target="#manageCategoriesModal">
                <i class="fas fa-tags text-info"></i>
                <span>Kelola Kategori</span>
            </button>
            <button type="button" class="doc-btn-action doc-btn-primary" data-toggle="modal" data-target="#uploadDocumentModal">
                <i class="fas fa-cloud-upload-alt"></i>
                <span>{{ __('messages.upload_document') ?? 'Unggah Dokumen' }}</span>
            </button>
        </div>
        @endcan
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-lg border-0 shadow-sm mb-4" role="alert" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3) !important; color: #34d399;">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-lg border-0 shadow-sm mb-4" role="alert" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3) !important; color: #f87171;">
        <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
        <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Stat Cards Grid -->
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3 mb-md-0">
            <div class="doc-stat-card">
                <div class="doc-stat-icon" style="background: rgba(99, 102, 241, 0.12); color: #818cf8;">
                    <i class="fas fa-folder-open"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase font-weight-bold" style="font-size: 0.7rem;">Total Dokumen</div>
                    <div class="h4 font-weight-bold mb-0 text-white">{{ number_format($totalDocuments) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3 mb-md-0">
            <div class="doc-stat-card">
                <div class="doc-stat-icon" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase font-weight-bold" style="font-size: 0.7rem;">Dokumen PDF</div>
                    <div class="h4 font-weight-bold mb-0 text-white">{{ number_format($totalPdf) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="doc-stat-card">
                <div class="doc-stat-icon" style="background: rgba(34, 197, 94, 0.12); color: #22c55e;">
                    <i class="fas fa-file-excel"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase font-weight-bold" style="font-size: 0.7rem;">Dokumen Excel</div>
                    <div class="h4 font-weight-bold mb-0 text-white">{{ number_format($totalExcel) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="doc-stat-card">
                <div class="doc-stat-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                    <i class="fas fa-hdd"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase font-weight-bold" style="font-size: 0.7rem;">Storage Terpakai</div>
                    <div class="h4 font-weight-bold mb-0 text-white">{{ $formattedTotalStorage }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="doc-card p-3 mb-4">
        <form method="GET" action="{{ route('documents.index') }}">
            @if(request('folder_id'))
            <input type="hidden" name="folder_id" value="{{ request('folder_id') }}">
            @endif
            <div class="row align-items-center" style="row-gap: 12px;">
                <!-- Search Keyword -->
                <div class="col-12 col-md-4">
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute" style="left: 12px; top: 12px; color: #94a3b8; font-size: 0.85rem;"></i>
                        <input type="text" name="search" class="doc-input" style="padding-left: 36px;" placeholder="Cari judul, no. dokumen, tag, file..." value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Category Dropdown -->
                <div class="col-12 col-md-3">
                    <select name="category" class="doc-input">
                        <option value="">-- Semua Kategori --</option>
                        @foreach($categoriesList as $cat)
                        <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range Pill -->
                <div class="col-12 col-md-3">
                    <div class="ba-daterange-pill">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" title="Start Date">
                        <span class="ba-daterange-separator">➔</span>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" title="End Date">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="col-12 col-md-2 d-flex gap-2 justify-content-end" style="gap: 8px;">
                    <button type="submit" class="doc-btn-action doc-btn-primary flex-grow-1">
                        <i class="fas fa-filter"></i> <span>Filter</span>
                    </button>
                    @if(request()->anyFilled(['search', 'category', 'start_date', 'end_date']))
                    <a href="{{ route('documents.index', ['folder_id' => request('folder_id')]) }}" class="doc-btn-action doc-btn-secondary" title="Reset Filter">
                        <i class="fas fa-undo"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Folder Directory Breadcrumbs Bar -->
    <div class="folder-breadcrumb-bar">
        <div class="d-flex align-items-center flex-wrap" style="gap: 4px;">
            <a href="{{ route('documents.index') }}" class="folder-breadcrumb-item {{ !$currentFolder ? 'active' : '' }}">
                <i class="fas fa-home"></i> <span>Root / Arsip Dokumen</span>
            </a>
            @foreach($breadcrumbs as $bFolder)
            <span class="folder-breadcrumb-sep">/</span>
            <a href="{{ route('documents.index', ['folder_id' => $bFolder->id]) }}" class="folder-breadcrumb-item {{ $currentFolder && $currentFolder->id == $bFolder->id ? 'active' : '' }}">
                <span>{{ $bFolder->name }}</span>
            </a>
            @endforeach
        </div>
        @if($currentFolder)
        <a href="{{ route('documents.index', ['folder_id' => $currentFolder->parent_id]) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size: 0.78rem; font-weight: 600;">
            <i class="fas fa-level-up-alt mr-1"></i> Ke Folder Atas
        </a>
        @endif
    </div>

    <!-- Folders Directory Grid (If any subfolders present) -->
    @if(count($folders) > 0)
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="font-weight-bold text-white mb-0" style="font-size: 0.92rem;">
                <i class="fas fa-folder text-warning mr-1"></i> Folder Direktori ({{ count($folders) }})
            </h6>
        </div>
        <div class="row" style="row-gap: 12px;">
            @foreach($folders as $folder)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="folder-card" onclick="navigateFolderCard(event, '{{ route('documents.index', ['folder_id' => $folder->id]) }}')">
                    <div class="d-flex align-items-center" style="gap: 12px; min-width: 0;">
                        <div class="folder-icon-box" style="background: {{ $folder->color }}20; color: {{ $folder->color }};">
                            <i class="fas fa-folder"></i>
                        </div>
                        <div style="min-width: 0;">
                            <div class="font-weight-bold text-white text-truncate" style="font-size: 0.88rem;" title="{{ $folder->name }}">
                                {{ $folder->name }}
                            </div>
                            <div class="text-muted small" style="font-size: 0.74rem;">
                                {{ $folder->total_documents_count ?? $folder->documents_count }} dokumen • {{ $folder->children_count }} subfolder
                            </div>
                        </div>
                    </div>
                    @can('menu_documents')
                    <div class="dropdown">
                        <button type="button" class="btn btn-link text-muted p-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size: 0.9rem;">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" style="background: var(--color-paper-1, #1e293b); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 10px;">
                            <button type="button" class="dropdown-item text-white small" onclick="openEditFolderModal({{ $folder->id }}, '{{ addslashes($folder->name) }}', '{{ $folder->color }}', '{{ addslashes($folder->description ?? '') }}')">
                                <i class="fas fa-edit mr-2 text-info"></i> Ubah Nama
                            </button>
                            <form action="{{ route('documents.folders.destroy', $folder->id) }}" method="POST" id="delete-folder-form-{{ $folder->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="dropdown-item text-danger small" onclick="confirmDeleteFolder({{ $folder->id }}, '{{ addslashes($folder->name) }}')">
                                    <i class="fas fa-trash-alt mr-2"></i> Hapus Folder
                                </button>
                            </form>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($currentFolder && $currentFolder->children_count > 0 && ($currentFolder->total_documents_count ?? 0) > 0)
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3 px-3 py-2 rounded" style="background: rgba(30, 41, 59, 0.5); border: 1px solid rgba(148, 163, 184, 0.16); gap: 10px;">
        <span class="small text-muted d-flex align-items-center">
            <i class="fas fa-layer-group text-info mr-2"></i>
            <span>Folder <strong>{{ $currentFolder->name }}</strong> memuat <strong>{{ $currentFolder->total_documents_count }} dokumen</strong> (tersebar di {{ $currentFolder->children_count }} subfolder).</span>
        </span>
        <a href="{{ route('documents.index', array_merge(request()->query(), ['folder_id' => $currentFolder->id, 'all_subfolders' => $includeSubfolders ? 0 : 1])) }}" class="btn btn-sm {{ $includeSubfolders ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3 py-1 text-nowrap" style="font-size: 0.76rem; font-weight: 600;">
            <i class="fas {{ $includeSubfolders ? 'fa-folder' : 'fa-folder-open' }} mr-1"></i>
            {{ $includeSubfolders ? 'Tampilkan Dokumen Langsung Saja' : 'Lihat Semua ' . $currentFolder->total_documents_count . ' Dokumen Subfolder' }}
        </a>
    </div>
    @endif

    <!-- Main Documents Table (Desktop View) -->
    <div class="doc-card d-none d-md-block">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="border-color: rgba(148, 163, 184, 0.12) !important;">
            <h6 class="font-weight-bold text-white mb-0" style="font-size: 0.92rem;">
                <i class="fas fa-file-alt text-indigo mr-1"></i> Berkas Dokumen ({{ $documents->total() }})
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="color: var(--color-ink-0, #f8fafc);">
                <thead style="background: rgba(15, 23, 42, 0.6); border-bottom: 1px solid rgba(148, 163, 184, 0.15);">
                    <tr>
                        <th class="py-3 px-3 text-muted small text-uppercase" style="width: 50px;">#</th>
                        <th class="py-3 px-3 text-muted small text-uppercase">No. Dokumen</th>
                        <th class="py-3 px-3 text-muted small text-uppercase">Judul & Kategori</th>
                        <th class="py-3 px-3 text-muted small text-uppercase">Folder / Direktori</th>
                        <th class="py-3 px-3 text-muted small text-uppercase">File & Ukuran</th>
                        <th class="py-3 px-3 text-muted small text-uppercase">Tgl Dokumen</th>
                        <th class="py-3 px-3 text-muted small text-uppercase">Pengunggah</th>
                        <th class="py-3 px-3 text-muted small text-uppercase text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $index => $doc)
                    <tr style="border-bottom: 1px solid rgba(148, 163, 184, 0.08);">
                        <td class="py-3 px-3 text-muted small">{{ $documents->firstItem() + $index }}</td>
                        <td class="py-3 px-3">
                            <span class="badge" style="background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3); font-family: var(--font-mono, monospace); font-size: 0.78rem; padding: 5px 9px;">
                                {{ $doc->document_number }}
                            </span>
                        </td>
                        <td class="py-3 px-3">
                            <div class="font-weight-bold text-white mb-1" style="font-size: 0.9rem;">{{ $doc->title }}</div>
                            <span class="badge badge-pill" style="background: {{ $doc->categoryModel ? $doc->categoryModel->color . '25' : 'rgba(148, 163, 184, 0.12)' }}; color: {{ $doc->categoryModel ? $doc->categoryModel->color : '#cbd5e1' }}; font-size: 0.72rem; padding: 3px 8px; border: 1px solid {{ $doc->categoryModel ? $doc->categoryModel->color . '40' : 'transparent' }};">
                                <i class="fas fa-tag mr-1"></i> {{ $doc->category }}
                            </span>
                            @if($doc->tags)
                            <span class="text-muted small ml-2" style="font-size: 0.75rem;">{{ $doc->tags }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-3 text-muted small">
                            @if($doc->folder)
                            <a href="{{ route('documents.index', ['folder_id' => $doc->folder->id]) }}" class="badge" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.25); font-size: 0.75rem; text-decoration: none !important;">
                                <i class="fas fa-folder mr-1"></i> {{ $doc->folder->name }}
                            </a>
                            @else
                            <span class="text-muted font-italic" style="font-size: 0.75rem;">Root</span>
                            @endif
                        </td>
                        <td class="py-3 px-3">
                            @php $iconData = $doc->file_icon; @endphp
                            <div class="d-flex align-items-center" style="gap: 10px;">
                                <div style="width: 34px; height: 34px; border-radius: 8px; background: {{ $iconData['bg'] }}; color: {{ $iconData['color'] }}; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                                    <i class="{{ $iconData['icon'] }}"></i>
                                </div>
                                <div style="min-width: 0;">
                                    <div class="text-truncate text-white font-weight-600" style="max-width: 170px; font-size: 0.83rem;" title="{{ $doc->file_name }}">{{ $doc->file_name }}</div>
                                    <div class="text-muted small" style="font-size: 0.73rem;">{{ strtoupper($doc->file_type) }} • {{ $doc->formatted_size }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-3 text-muted small" style="white-space: nowrap;">
                            <i class="far fa-calendar-alt mr-1 text-info"></i> {{ $doc->document_date ? $doc->document_date->format('d M Y') : '-' }}
                        </td>
                        <td class="py-3 px-3 text-muted small">
                            <div class="d-flex align-items-center" style="gap: 6px;">
                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.65rem; font-weight: 700;">
                                    {{ strtoupper(substr($doc->uploadedBy->name ?? 'U', 0, 1)) }}
                                </div>
                                <span>{{ $doc->uploadedBy->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td class="py-3 px-3 text-center">
                            <div class="d-inline-flex align-items-center justify-content-center" style="gap: 8px;">
                                <button type="button" class="doc-action-btn doc-action-preview" onclick="openPreviewModal({{ $doc->id }})" title="{{ __('messages.preview_document') }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('documents.download', $doc->id) }}" class="doc-action-btn doc-action-download" title="{{ __('messages.download_document') }}">
                                    <i class="fas fa-download"></i>
                                </a>
                                @can('menu_documents')
                                <button type="button" class="doc-action-btn doc-action-delete" onclick="confirmDeleteDoc({{ $doc->id }}, '{{ addslashes($doc->title) }}')" title="Hapus Dokumen">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                <form id="delete-doc-form-{{ $doc->id }}" action="{{ route('documents.destroy', $doc->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-5 text-center text-muted">
                            <i class="fas fa-folder-open fa-3x mb-3 text-secondary opacity-50"></i>
                            <div class="h6 font-weight-bold text-white">Belum Ada Dokumen di Folder Ini</div>
                            <p class="small text-muted mb-3">Dokumen hasil scan yang Anda unggah ke folder ini akan muncul di sini.</p>
                            @can('menu_documents')
                            <button type="button" class="doc-btn-action doc-btn-primary" data-toggle="modal" data-target="#uploadDocumentModal">
                                <i class="fas fa-cloud-upload-alt"></i> Unggah Dokumen Ke Folder Ini
                            </button>
                            @endcan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($documents->hasPages())
        <div class="p-3 border-top" style="border-color: rgba(148, 163, 184, 0.12) !important;">
            {{ $documents->links() }}
        </div>
        @endif
    </div>

    <!-- Mobile Card Deck View -->
    <div class="d-block d-md-none">
        @forelse($documents as $doc)
        <div class="doc-card p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="badge" style="background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3); font-family: var(--font-mono, monospace); font-size: 0.75rem;">
                    {{ $doc->document_number }}
                </span>
                <span class="badge badge-pill" style="background: {{ $doc->categoryModel ? $doc->categoryModel->color . '25' : 'rgba(148, 163, 184, 0.12)' }}; color: {{ $doc->categoryModel ? $doc->categoryModel->color : '#cbd5e1' }}; font-size: 0.7rem;">
                    {{ $doc->category }}
                </span>
            </div>
            <h6 class="font-weight-bold text-white mb-2">{{ $doc->title }}</h6>
            
            @php $iconData = $doc->file_icon; @endphp
            <div class="d-flex align-items-center mb-3 p-2 rounded" style="background: rgba(15, 23, 42, 0.4); gap: 10px;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: {{ $iconData['bg'] }}; color: {{ $iconData['color'] }}; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0;">
                    <i class="{{ $iconData['icon'] }}"></i>
                </div>
                <div style="min-width: 0;">
                    <div class="text-truncate text-white font-weight-600" style="font-size: 0.8rem;">{{ $doc->file_name }}</div>
                    <div class="text-muted small" style="font-size: 0.7rem;">{{ strtoupper($doc->file_type) }} • {{ $doc->formatted_size }}</div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="border-color: rgba(148, 163, 184, 0.1) !important;">
                <div class="text-muted small" style="font-size: 0.75rem;">
                    <i class="far fa-calendar-alt mr-1"></i> {{ $doc->document_date ? $doc->document_date->format('d M Y') : '-' }}
                </div>
                <div class="d-flex align-items-center" style="gap: 8px;">
                    <button type="button" class="doc-action-btn doc-action-preview" onclick="openPreviewModal({{ $doc->id }})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <a href="{{ route('documents.download', $doc->id) }}" class="doc-action-btn doc-action-download">
                        <i class="fas fa-download"></i>
                    </a>
                    @can('menu_documents')
                    <button type="button" class="doc-action-btn doc-action-delete" onclick="confirmDeleteDoc({{ $doc->id }}, '{{ addslashes($doc->title) }}')">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    @endcan
                </div>
            </div>
        </div>
        @empty
        <div class="doc-card p-4 text-center text-muted">
            <i class="fas fa-folder-open fa-2x mb-2 text-secondary opacity-50"></i>
            <div class="small font-weight-bold text-white">Belum Ada Dokumen di Folder Ini</div>
        </div>
        @endforelse

        @if($documents->hasPages())
        <div class="mt-3">
            {{ $documents->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal 1: Create Folder Modal -->
<div class="modal fade" id="createFolderModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="background: var(--color-paper-1, #1e293b); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 18px; color: var(--color-ink-0, #f8fafc);">
            <div class="modal-header border-bottom py-3 px-4" style="border-color: rgba(148, 163, 184, 0.15) !important;">
                <h5 class="modal-title font-weight-bold text-white d-flex align-items-center">
                    <i class="fas fa-folder-plus text-warning mr-2"></i> Buat Folder Baru
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('documents.folders.store') }}" method="POST">
                @csrf
                <input type="hidden" name="parent_id" value="{{ request('folder_id') }}">
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold text-white small">Nama Folder <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="doc-input" placeholder="misal: Berita Acara 2026" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold text-white small">Warna Ikon Folder</label>
                        <div class="d-flex align-items-center" style="gap: 12px;">
                            <label class="mb-0 cursor-pointer"><input type="radio" name="color" value="#818cf8" checked> <span class="badge p-2" style="background:#818cf8; color:#fff;">Indigo</span></label>
                            <label class="mb-0 cursor-pointer"><input type="radio" name="color" value="#38bdf8"> <span class="badge p-2" style="background:#38bdf8; color:#fff;">Sky</span></label>
                            <label class="mb-0 cursor-pointer"><input type="radio" name="color" value="#34d399"> <span class="badge p-2" style="background:#34d399; color:#fff;">Emerald</span></label>
                            <label class="mb-0 cursor-pointer"><input type="radio" name="color" value="#fbbf24"> <span class="badge p-2" style="background:#fbbf24; color:#000;">Amber</span></label>
                            <label class="mb-0 cursor-pointer"><input type="radio" name="color" value="#f472b6"> <span class="badge p-2" style="background:#f472b6; color:#fff;">Pink</span></label>
                            <label class="mb-0 cursor-pointer"><input type="radio" name="color" value="#a78bfa"> <span class="badge p-2" style="background:#a78bfa; color:#fff;">Purple</span></label>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label font-weight-bold text-white small">Deskripsi (Opsional)</label>
                        <textarea name="description" class="doc-input" rows="2" placeholder="Catatan singkat peruntukan folder ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4" style="border-color: rgba(148, 163, 184, 0.15) !important;">
                    <button type="button" class="doc-btn-action doc-btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="doc-btn-action doc-btn-primary">
                        <i class="fas fa-save mr-1"></i> Buat Folder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 2: Manage Categories Modal -->
<div class="modal fade" id="manageCategoriesModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="background: var(--color-paper-1, #1e293b); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 18px; color: var(--color-ink-0, #f8fafc);">
            <div class="modal-header border-bottom py-3 px-4" style="border-color: rgba(148, 163, 184, 0.15) !important;">
                <h5 class="modal-title font-weight-bold text-white d-flex align-items-center">
                    <i class="fas fa-tags text-info mr-2"></i> Kelola Kategori Dokumen
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <!-- Add New Category Form -->
                <form action="{{ route('documents.categories.store') }}" method="POST" class="mb-4 p-3 rounded" style="background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(148, 163, 184, 0.15);">
                    @csrf
                    <h6 class="font-weight-bold text-white mb-3" style="font-size: 0.88rem;">+ Tambah Kategori Baru</h6>
                    <div class="row align-items-end" style="row-gap: 10px;">
                        <div class="col-md-5">
                            <label class="form-label font-weight-bold text-white small">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="doc-input" placeholder="misal: Dokumen ISO / Audit" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold text-white small">Warna Badge</label>
                            <input type="color" name="color" class="doc-input px-1 py-1" value="#6366f1">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="doc-btn-action doc-btn-primary w-100">
                                <i class="fas fa-plus"></i> Tambah
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Existing Categories Table -->
                <h6 class="font-weight-bold text-white mb-2" style="font-size: 0.88rem;">Daftar Kategori Terdaftar</h6>
                <div class="table-responsive rounded" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm table-hover text-white mb-0" style="font-size: 0.84rem;">
                        <thead style="background: rgba(15, 23, 42, 0.8);">
                            <tr>
                                <th>#</th>
                                <th>Kategori</th>
                                <th>Warna</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categoriesList as $idx => $cat)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <span class="badge" style="background: {{ $cat->color }}25; color: {{ $cat->color }}; border: 1px solid {{ $cat->color }}40; font-size: 0.78rem;">
                                        {{ $cat->name }}
                                    </span>
                                </td>
                                <td><span class="d-inline-block rounded-circle" style="width: 14px; height: 14px; background: {{ $cat->color }};"></span> {{ $cat->color }}</td>
                                <td>
                                    <div class="d-inline-flex align-items-center" style="gap: 12px;">
                                        <button type="button" class="btn btn-sm text-info p-0" onclick="openEditCategoryModal({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ $cat->color }}')" title="Ubah Kategori">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('documents.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ addslashes($cat->name) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm text-danger p-0" title="Hapus Kategori">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top py-3 px-4" style="border-color: rgba(148, 163, 184, 0.15) !important;">
                <button type="button" class="doc-btn-action doc-btn-secondary" data-dismiss="modal">Selesai</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 2b: Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="background: var(--color-paper-1, #1e293b); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 18px; color: var(--color-ink-0, #f8fafc);">
            <div class="modal-header border-bottom py-3 px-4" style="border-color: rgba(148, 163, 184, 0.15) !important;">
                <h5 class="modal-title font-weight-bold text-white d-flex align-items-center">
                    <i class="fas fa-edit text-info mr-2"></i> Ubah Kategori Dokumen
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editCategoryForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold text-white small">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editCategoryName" class="doc-input" required>
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label font-weight-bold text-white small">Warna Badge</label>
                        <input type="color" name="color" id="editCategoryColor" class="doc-input px-1 py-1" value="#6366f1">
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4" style="border-color: rgba(148, 163, 184, 0.15) !important;">
                    <button type="button" class="doc-btn-action doc-btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="doc-btn-action doc-btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 3: Upload Document Modal -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" role="dialog" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="background: var(--color-paper-1, #1e293b); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 18px; color: var(--color-ink-0, #f8fafc);">
            <div class="modal-header border-bottom py-3 px-4" style="border-color: rgba(148, 163, 184, 0.15) !important;">
                <h5 class="modal-title font-weight-bold text-white d-flex align-items-center" id="uploadDocumentModalLabel">
                    <i class="fas fa-cloud-upload-alt text-primary mr-2"></i> {{ __('messages.upload_document') ?? 'Unggah Dokumen Hasil Scan' }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <!-- Judul Dokumen -->
                        <div class="col-md-8 form-group mb-3">
                            <label class="form-label font-weight-bold text-white small">
                                {{ __('messages.document_title') ?? 'Judul Dokumen' }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title" class="doc-input" placeholder="misal: Berita Acara Disposal Laptop Cikande" required>
                        </div>

                        <!-- Target Folder -->
                        <div class="col-md-4 form-group mb-3">
                            <label class="form-label font-weight-bold text-white small">Target Folder</label>
                            <select name="folder_id" class="doc-input">
                                <option value="">Root / Tanpa Folder</option>
                                @foreach($allFoldersList as $fList)
                                <option value="{{ $fList->id }}" {{ (request('folder_id') == $fList->id) ? 'selected' : '' }}>
                                    📁 {{ $fList->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Kategori Dinamis -->
                        <div class="col-md-6 form-group mb-3">
                            <label class="form-label font-weight-bold text-white small">
                                {{ __('messages.document_category') ?? 'Kategori' }} <span class="text-danger">*</span>
                            </label>
                            <select name="category" class="doc-input" required>
                                @foreach($categoriesList as $cat)
                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tanggal Dokumen -->
                        <div class="col-md-6 form-group mb-3">
                            <label class="form-label font-weight-bold text-white small">
                                {{ __('messages.document_date') ?? 'Tanggal Dokumen / Scan' }} <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="document_date" class="doc-input" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <!-- Tags / Kata Kunci -->
                        <div class="col-12 form-group mb-3">
                            <label class="form-label font-weight-bold text-white small">
                                {{ __('messages.document_tags') ?? 'Tag / Kata Kunci (Opsional)' }}
                            </label>
                            <input type="text" name="tags" class="doc-input" placeholder="misal: #disposal, #cikande, #laptop">
                        </div>

                        <!-- Deskripsi -->
                        <div class="col-12 form-group mb-3">
                            <label class="form-label font-weight-bold text-white small">
                                {{ __('messages.document_description') ?? 'Catatan / Deskripsi' }}
                            </label>
                            <textarea name="description" class="doc-input" rows="2" style="height: auto;" placeholder="Catatan ringkas mengenai dokumen hasil scan..."></textarea>
                        </div>

                        <!-- Drag & Drop Dropzone File Upload -->
                        <div class="col-12 form-group mb-2">
                            <label class="form-label font-weight-bold text-white small">
                                {{ __('messages.document_file') ?? 'File Dokumen' }} <span class="text-danger">*</span>
                            </label>
                            <div class="doc-dropzone" id="docDropzone" onclick="document.getElementById('docFileInput').click()">
                                <input type="file" name="document_file" id="docFileInput" class="d-none" accept=".pdf,.png,.jpg,.jpeg,.gif,.webp,.doc,.docx,.xls,.xlsx,.zip,.rar,.7z" required onchange="handleDocFileSelect(this)">
                                <div class="doc-dropzone-icon">
                                    <i class="fas fa-file-upload"></i>
                                </div>
                                <div class="h6 font-weight-bold text-white mb-1" id="docDropzoneTitle">Klik atau Tarik File ke Sini</div>
                                <p class="text-muted small mb-0" id="docDropzoneDesc">Mendukung format PDF, JPG, PNG, DOCX, XLSX, ZIP (Maks. 20 MB)</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4" style="border-color: rgba(148, 163, 184, 0.15) !important;">
                    <button type="button" class="doc-btn-action doc-btn-secondary" data-dismiss="modal">
                        {{ __('messages.cancel') }}
                    </button>
                    <button type="submit" class="doc-btn-action doc-btn-primary">
                        <i class="fas fa-save mr-1"></i> {{ __('messages.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 4: Edit Folder Modal -->
<div class="modal fade" id="editFolderModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="background: var(--color-paper-1, #1e293b); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 18px; color: var(--color-ink-0, #f8fafc);">
            <div class="modal-header border-bottom py-3 px-4" style="border-color: rgba(148, 163, 184, 0.15) !important;">
                <h5 class="modal-title font-weight-bold text-white d-flex align-items-center">
                    <i class="fas fa-edit text-info mr-2"></i> Ubah Folder
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editFolderForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold text-white small">Nama Folder <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editFolderName" class="doc-input" required>
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label font-weight-bold text-white small">Deskripsi (Opsional)</label>
                        <textarea name="description" id="editFolderDesc" class="doc-input" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4" style="border-color: rgba(148, 163, 184, 0.15) !important;">
                    <button type="button" class="doc-btn-action doc-btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="doc-btn-action doc-btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 5: Live Preview Modal -->
<div class="modal fade" id="previewDocumentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" id="previewModalDialog" role="document">
        <div class="modal-content" style="background: var(--color-paper-1, #1e293b); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 18px; color: var(--color-ink-0, #f8fafc);">
            <div class="modal-header border-bottom py-3 px-4" style="border-color: rgba(148, 163, 184, 0.15) !important;">
                <div class="d-flex align-items-center" style="gap: 12px;">
                    <div id="previewHeaderIcon" style="width: 36px; height: 36px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bold text-white mb-0" id="previewDocTitle">Pratinjau Dokumen</h5>
                        <div class="text-muted small" id="previewDocSub"></div>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3" style="min-height: 450px; background: rgba(15, 23, 42, 0.6); display: flex; align-items: center; justify-content: center;">
                <!-- PDF Viewer -->
                <iframe id="pdfPreviewFrame" src="" style="width: 100%; height: 600px; border: none; border-radius: 10px; display: none;"></iframe>

                <!-- Image Viewer -->
                <div id="imagePreviewContainer" class="text-center w-100" style="display: none;">
                    <img id="imagePreviewImg" src="" alt="Scan Preview" style="max-width: 100%; max-height: 550px; border-radius: 10px; object-fit: contain; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                </div>

                <!-- Video Player (MP4 / WebM) -->
                <div id="videoPreviewContainer" class="text-center w-100" style="display: none;">
                    <video id="videoPreviewPlayer" controls style="max-width: 100%; max-height: 550px; border-radius: 10px; background: #000; box-shadow: 0 10px 30px rgba(0,0,0,0.5);" src=""></video>
                </div>

                <!-- Plain Text Viewer -->
                <div id="textPreviewContainer" class="w-100 p-2" style="display: none; max-height: 550px; overflow-y: auto;">
                    <pre id="textPreviewContent" style="background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(148, 163, 184, 0.15); border-radius: 10px; padding: 15px; color: #e2e8f0; font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; white-space: pre-wrap; word-break: break-all; margin: 0;"></pre>
                </div>

                <!-- Excel Spreadsheet Viewer (Authentic Excel UI) -->
                <div id="excelPreviewContainer" class="w-100" style="display: none;">
                    <div class="excel-spreadsheet-container">
                        <!-- Excel Sheet Tabs & Toolbar -->
                        <div class="excel-sheet-tabs-bar">
                            <div id="excelSheetTabs" class="d-flex align-items-center" style="gap: 6px; overflow-x: auto;"></div>
                            <div style="min-width: 220px;">
                                <div class="position-relative">
                                    <i class="fas fa-search position-absolute" style="left: 10px; top: 9px; color: #94a3b8; font-size: 0.75rem;"></i>
                                    <input type="text" id="excelSearchInput" class="doc-input py-1" style="height: 32px; font-size: 0.78rem; padding-left: 30px;" placeholder="Cari di spreadsheet..." onkeyup="filterExcelTable()">
                                </div>
                            </div>
                        </div>

                        <!-- Spreadsheet Scroll Area -->
                        <div id="excelTableWrapper" class="excel-table-scroll">
                            <div class="text-center p-5 text-muted">
                                <i class="fas fa-spinner fa-spin fa-2x mb-2 text-success"></i>
                                <div>Membaca lembar kerja Excel...</div>
                            </div>
                        </div>

                        <!-- Status Bar -->
                        <div class="excel-status-bar">
                            <span id="excelStatusBarInfo">READY</span>
                            <span><i class="fas fa-file-excel mr-1 text-success"></i> Microsoft Excel Simulator</span>
                        </div>
                    </div>
                </div>

                <!-- Word Document Viewer -->
                <div id="wordPreviewContainer" class="w-100 p-2" style="display: none; max-height: 600px; overflow-y: auto;">
                    <div id="wordPreviewPaper" class="word-preview-paper">
                        <div class="text-center p-5 text-muted">
                            <i class="fas fa-spinner fa-spin fa-2x mb-2 text-primary"></i>
                            <div>Membaca dokumen Word...</div>
                        </div>
                    </div>
                </div>

                <!-- Zip Archive Inspector Viewer -->
                <div id="zipPreviewContainer" class="w-100 p-2" style="display: none;">
                    <div class="zip-preview-container">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color: rgba(148, 163, 184, 0.15) !important;">
                            <h6 class="text-white font-weight-bold mb-0" id="zipArchiveTitle">
                                <i class="fas fa-file-archive text-warning mr-2"></i> Isi Berkas ZIP Archive
                            </h6>
                            <span class="badge badge-pill badge-secondary" id="zipTotalFilesBadge">0 berkas</span>
                        </div>
                        <div id="zipFileList" class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                            <div class="text-center p-5 text-muted">
                                <i class="fas fa-spinner fa-spin fa-2x mb-2 text-warning"></i>
                                <div>Membaca arsip ZIP...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fallback Container for Non-previewable Files (DOCX, ZIP, etc.) -->
                <div id="fallbackPreviewContainer" class="text-center p-5" style="display: none;">
                    <div id="fallbackIconBox" class="mx-auto mb-3" style="width: 72px; height: 72px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 2.2rem;">
                        <i class="fas fa-file"></i>
                    </div>
                    <h5 class="text-white font-weight-bold mb-2" id="fallbackFileName">Nama File</h5>
                    <p class="text-muted small mb-4">File format ini tidak dapat dipratinjau langsung di browser. Silakan unduh file untuk melihat isi lengkapnya.</p>
                    <a id="fallbackDownloadBtn" href="#" class="doc-btn-action doc-btn-primary px-4 py-2" style="font-size: 0.9rem;">
                        <i class="fas fa-download mr-1"></i> Unduh File Ini
                    </a>
                </div>
            </div>
            <div class="modal-footer border-top py-3 px-4 d-flex justify-content-between align-items-center" style="border-color: rgba(148, 163, 184, 0.15) !important;">
                <div class="text-muted small">
                    <span id="previewDocMeta"></span>
                </div>
                <div class="d-flex gap-2" style="gap: 8px;">
                    <button type="button" class="doc-btn-action doc-btn-secondary" data-dismiss="modal">Tutup</button>
                    <a id="previewDownloadBtn" href="#" class="doc-btn-action doc-btn-primary">
                        <i class="fas fa-download"></i> Unduh Dokumen
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- SheetJS Library for Client-side Excel Viewer -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<!-- Mammoth.js Library for Client-side Word (.docx) Viewer -->
<script src="https://cdn.jsdelivr.net/npm/mammoth@1.6.0/mammoth.browser.min.js"></script>
<!-- JSZip Library for Client-side ZIP Archive Inspector -->
<script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
<script>
    let currentExcelWorkbook = null;

    // Helper to dynamically load libraries from fallbacks if CDN blocked or delayed
    function ensureLibraryLoaded(globalVar, urls) {
        if (typeof window[globalVar] !== 'undefined') {
            return Promise.resolve(true);
        }
        return new Promise((resolve) => {
            let attempt = 0;
            function tryNext() {
                if (attempt >= urls.length) {
                    resolve(typeof window[globalVar] !== 'undefined');
                    return;
                }
                const url = urls[attempt++];
                const script = document.createElement('script');
                script.src = url;
                script.onload = () => {
                    if (typeof window[globalVar] !== 'undefined') {
                        resolve(true);
                    } else {
                        tryNext();
                    }
                };
                script.onerror = () => tryNext();
                document.head.appendChild(script);
            }
            tryNext();
        });
    }

    function getExcelColName(n) {
        let s = "";
        while (n >= 0) {
            s = String.fromCharCode((n % 26) + 65) + s;
            n = Math.floor(n / 26) - 1;
        }
        return s;
    }

    function renderExcelSheet(sheetName) {
        if (!currentExcelWorkbook || !currentExcelWorkbook.Sheets[sheetName]) return;
        
        // Update active tab button style
        document.querySelectorAll('#excelSheetTabs .excel-tab-btn').forEach(btn => {
            if (btn.getAttribute('data-sheet') === sheetName) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        const worksheet = currentExcelWorkbook.Sheets[sheetName];
        const data = XLSX.utils.sheet_to_json(worksheet, { header: 1, defval: "" });

        if (!data || data.length === 0) {
            document.getElementById('excelTableWrapper').innerHTML = `<div class="text-center p-5 text-muted">Lembar kerja ini kosong.</div>`;
            document.getElementById('excelStatusBarInfo').innerText = `READY • 0 Baris • Sheet: ${sheetName}`;
            return;
        }

        let maxCols = 0;
        data.forEach(row => {
            if (row.length > maxCols) maxCols = row.length;
        });
        if (maxCols === 0) maxCols = 1;

        let html = '<table class="excel-grid-table" id="renderedExcelTable">';
        
        // Column Headers (Corner, A, B, C, D...)
        html += '<thead><tr>';
        html += '<th class="excel-corner-header"></th>';
        for (let c = 0; c < maxCols; c++) {
            html += `<th class="excel-col-header">${getExcelColName(c)}</th>`;
        }
        html += '</tr></thead>';

        // Data Rows (1, 2, 3...)
        html += '<tbody>';
        for (let r = 0; r < data.length; r++) {
            html += '<tr>';
            html += `<td class="excel-row-header">${r + 1}</td>`;
            const rowData = data[r];
            for (let c = 0; c < maxCols; c++) {
                const val = (rowData && rowData[c] !== undefined && rowData[c] !== null) ? rowData[c] : '';
                html += `<td class="excel-cell">${val}</td>`;
            }
            html += '</tr>';
        }
        html += '</tbody></table>';

        const wrapper = document.getElementById('excelTableWrapper');
        wrapper.innerHTML = html;

        document.getElementById('excelStatusBarInfo').innerText = `READY • ${data.length} Baris • ${maxCols} Kolom • Sheet: ${sheetName}`;

        const searchInput = document.getElementById('excelSearchInput');
        if (searchInput) searchInput.value = '';
    }

    function filterExcelTable() {
        const query = document.getElementById('excelSearchInput').value.toLowerCase();
        const table = document.getElementById('renderedExcelTable');
        if (!table) return;

        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        for (let i = 0; i < rows.length; i++) {
            const rowText = rows[i].textContent || rows[i].innerText;
            if (rowText.toLowerCase().indexOf(query) > -1) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }

    function navigateFolderCard(e, url) {
        if (e.target.closest('.dropdown') || e.target.closest('.dropdown-menu')) {
            return;
        }
        window.location.href = url;
    }

    // Drag & Drop handling for document upload dropzone
    const dropzone = document.getElementById('docDropzone');
    if (dropzone) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropzone.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropzone.classList.remove('dragover');
            }, false);
        });

        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                document.getElementById('docFileInput').files = files;
                handleDocFileSelect(document.getElementById('docFileInput'));
            }
        });
    }

    function handleDocFileSelect(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const name = file.name;
            const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            document.getElementById('docDropzoneTitle').innerText = name;
            document.getElementById('docDropzoneDesc').innerText = `Ukuran file: ${sizeMB} MB • Siap diunggah`;
            
            // Auto fill title if empty
            const titleInput = document.querySelector('#uploadDocumentModal input[name="title"]');
            if (titleInput && !titleInput.value) {
                const cleanName = name.substring(0, name.lastIndexOf('.')) || name;
                titleInput.value = cleanName;
            }
        }
    }

    function openEditFolderModal(id, name, color, desc) {
        document.getElementById('editFolderForm').action = `/documents/folders/${id}`;
        document.getElementById('editFolderName').value = name;
        document.getElementById('editFolderDesc').value = desc;
        $('#editFolderModal').modal('show');
    }

    function openEditCategoryModal(id, name, color) {
        document.getElementById('editCategoryForm').action = `/documents/categories/${id}`;
        document.getElementById('editCategoryName').value = name;
        document.getElementById('editCategoryColor').value = color;
        $('#editCategoryModal').modal('show');
    }

    function confirmDeleteFolder(id, name) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Konfirmasi Hapus Folder',
                html: `Apakah Anda yakin ingin menghapus folder <b>"${name}"</b> beserta isinya?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Folder',
                cancelButtonText: 'Batal',
                customClass: { popup: 'swal2-minimal-rounded' }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-folder-form-${id}`).submit();
                }
            });
        } else {
            if (confirm(`Apakah Anda yakin ingin menghapus folder "${name}"?`)) {
                document.getElementById(`delete-folder-form-${id}`).submit();
            }
        }
    }

    // Open Live Preview Modal via AJAX
    function openPreviewModal(docId) {
        const modalDialog = document.getElementById('previewModalDialog');
        if (modalDialog) modalDialog.classList.remove('excel-modal-dialog');

        $('#previewDocumentModal').modal('show');
        
        // Reset state
        const vid = document.getElementById('videoPreviewPlayer');
        if (vid) { vid.pause(); vid.src = ''; }
        document.getElementById('videoPreviewContainer').style.display = 'none';
        document.getElementById('textPreviewContainer').style.display = 'none';
        document.getElementById('pdfPreviewFrame').style.display = 'none';
        document.getElementById('imagePreviewContainer').style.display = 'none';
        document.getElementById('excelPreviewContainer').style.display = 'none';
        document.getElementById('wordPreviewContainer').style.display = 'none';
        document.getElementById('zipPreviewContainer').style.display = 'none';
        document.getElementById('fallbackPreviewContainer').style.display = 'none';
        document.getElementById('previewDocTitle').innerText = 'Memuat Dokumen...';

        fetch(`/documents/${docId}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('previewDocTitle').innerText = data.title;
                document.getElementById('previewDocSub').innerText = `${data.document_number} • ${data.category} • Folder: ${data.folder_name}`;
                document.getElementById('previewDocMeta').innerText = `Diunggah oleh ${data.uploaded_by} pada ${data.document_date} (${data.formatted_size})`;
                document.getElementById('previewDownloadBtn').href = `/documents/${docId}/download`;

                const iconBox = document.getElementById('previewHeaderIcon');
                iconBox.style.background = data.file_icon.bg;
                iconBox.style.color = data.file_icon.color;
                iconBox.innerHTML = `<i class="${data.file_icon.icon}"></i>`;

                const ext = data.file_type.toLowerCase();

                if (ext === 'pdf') {
                    const iframe = document.getElementById('pdfPreviewFrame');
                    iframe.src = data.file_url;
                    iframe.style.display = 'block';
                } else if (['png', 'jpg', 'jpeg', 'webp', 'gif', 'svg'].includes(ext)) {
                    const img = document.getElementById('imagePreviewImg');
                    img.src = data.file_url;
                    document.getElementById('imagePreviewContainer').style.display = 'block';
                } else if (['xls', 'xlsx', 'csv'].includes(ext)) {
                    if (modalDialog) modalDialog.classList.add('excel-modal-dialog');
                    document.getElementById('excelPreviewContainer').style.display = 'block';
                    document.getElementById('excelTableWrapper').innerHTML = `<div class="text-center p-5 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2 text-success"></i><div>Membaca lembar kerja Excel...</div></div>`;
                    document.getElementById('excelSheetTabs').innerHTML = '';

                    ensureLibraryLoaded('XLSX', [
                        'https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js',
                        'https://cdnjs.cloudflare.net/ajax/libs/xlsx/0.18.5/xlsx.full.min.js',
                        'https://unpkg.com/xlsx@0.18.5/dist/xlsx.full.min.js'
                    ]).then(loaded => {
                        if (!loaded) {
                            document.getElementById('excelTableWrapper').innerHTML = `<div class="text-center p-5 text-warning"><i class="fas fa-exclamation-triangle fa-2x mb-2"></i><div>Gagal memuat library Excel Viewer dari CDN. Silakan unduh file.</div></div>`;
                            return;
                        }
                        fetch(data.file_url)
                            .then(res => res.arrayBuffer())
                            .then(buffer => {
                                currentExcelWorkbook = XLSX.read(buffer, { type: 'array' });
                                const sheetNames = currentExcelWorkbook.SheetNames;
                                
                                const tabsContainer = document.getElementById('excelSheetTabs');
                                tabsContainer.innerHTML = '';
                                sheetNames.forEach((sName, idx) => {
                                    const btn = document.createElement('button');
                                    btn.type = 'button';
                                    btn.className = idx === 0 ? 'excel-tab-btn active' : 'excel-tab-btn';
                                    btn.setAttribute('data-sheet', sName);
                                    btn.innerText = sName;
                                    btn.onclick = () => renderExcelSheet(sName);
                                    tabsContainer.appendChild(btn);
                                });

                                if (sheetNames.length > 0) {
                                    renderExcelSheet(sheetNames[0]);
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                document.getElementById('excelTableWrapper').innerHTML = `<div class="text-center p-5 text-danger"><i class="fas fa-exclamation-circle fa-2x mb-2"></i><div>Gagal membaca file Excel ini.</div></div>`;
                            });
                    });
                } else if (['doc', 'docx'].includes(ext)) {
                    document.getElementById('wordPreviewContainer').style.display = 'block';
                    document.getElementById('wordPreviewPaper').innerHTML = `<div class="text-center p-5 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2 text-primary"></i><div>Memuat library Word Viewer...</div></div>`;

                    ensureLibraryLoaded('mammoth', [
                        'https://cdn.jsdelivr.net/npm/mammoth@1.6.0/mammoth.browser.min.js',
                        'https://cdnjs.cloudflare.net/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js',
                        'https://unpkg.com/mammoth@1.6.0/mammoth.browser.min.js'
                    ]).then(loaded => {
                        if (!loaded) {
                            document.getElementById('wordPreviewPaper').innerHTML = `<div class="text-center text-warning p-4"><i class="fas fa-exclamation-triangle fa-2x mb-2"></i><div>Gagal memuat library Word Viewer dari CDN. Silakan unduh dokumen.</div></div>`;
                            return;
                        }
                        document.getElementById('wordPreviewPaper').innerHTML = `<div class="text-center p-5 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2 text-primary"></i><div>Membaca dokumen Word...</div></div>`;
                        fetch(data.file_url)
                            .then(res => res.arrayBuffer())
                            .then(buffer => {
                                window.mammoth.convertToHtml({ arrayBuffer: buffer })
                                    .then(result => {
                                        document.getElementById('wordPreviewPaper').innerHTML = result.value || '<div class="text-muted text-center p-4">Dokumen Word ini kosong.</div>';
                                    })
                                    .catch(err => {
                                        console.error(err);
                                        document.getElementById('wordPreviewPaper').innerHTML = `<div class="text-center text-danger p-4"><i class="fas fa-exclamation-circle fa-2x mb-2"></i><div>Gagal mengonversi dokumen Word. Silakan unduh file.</div></div>`;
                                    });
                            })
                            .catch(err => {
                                console.error(err);
                                document.getElementById('wordPreviewPaper').innerHTML = `<div class="text-center text-danger p-4">Gagal mengunduh file Word dari server.</div>`;
                            });
                    });
                } else if (['zip', 'rar', '7z'].includes(ext)) {
                    document.getElementById('zipPreviewContainer').style.display = 'block';
                    document.getElementById('zipArchiveTitle').innerHTML = `<i class="fas fa-file-archive text-warning mr-2"></i> Isi Berkas ZIP (${data.file_name})`;
                    document.getElementById('zipFileList').innerHTML = `<div class="text-center p-5 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2 text-warning"></i><div>Memuat library ZIP Inspector...</div></div>`;

                    ensureLibraryLoaded('JSZip', [
                        'https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js',
                        'https://cdnjs.cloudflare.net/ajax/libs/jszip/3.10.1/jszip.min.js',
                        'https://unpkg.com/jszip@3.10.1/dist/jszip.min.js'
                    ]).then(loaded => {
                        if (!loaded) {
                            document.getElementById('zipFileList').innerHTML = `<div class="text-center text-warning p-4"><i class="fas fa-exclamation-triangle fa-2x mb-2"></i><div>Gagal memuat library ZIP Inspector dari CDN. Silakan unduh file.</div></div>`;
                            return;
                        }
                        document.getElementById('zipFileList').innerHTML = `<div class="text-center p-5 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2 text-warning"></i><div>Membaca arsip ZIP...</div></div>`;
                        fetch(data.file_url)
                            .then(res => res.arrayBuffer())
                            .then(buffer => {
                                window.JSZip.loadAsync(buffer).then(zip => {
                                    let html = '<div class="d-flex flex-column" style="gap: 4px;">';
                                    let count = 0;
                                    zip.forEach((relativePath, zipEntry) => {
                                        count++;
                                        const isDir = zipEntry.dir;
                                        const icon = isDir ? 'fas fa-folder text-warning' : 'fas fa-file-alt text-info';
                                        html += `
                                            <div class="zip-file-item">
                                                <div class="d-flex align-items-center" style="gap: 10px;">
                                                    <i class="${icon}"></i>
                                                    <span class="text-white font-weight-600" style="font-size: 0.84rem;">${relativePath}</span>
                                                </div>
                                                <span class="text-muted small">${isDir ? 'Folder' : 'File'}</span>
                                            </div>
                                        `;
                                    });
                                    html += '</div>';
                                    document.getElementById('zipFileList').innerHTML = html;
                                    document.getElementById('zipTotalFilesBadge').innerText = `${count} berkas`;
                                }).catch(err => {
                                    console.error(err);
                                    document.getElementById('zipFileList').innerHTML = `<div class="text-center text-danger p-4"><i class="fas fa-exclamation-circle fa-2x mb-2"></i><div>Gagal membaca struktur berkas ZIP ini.</div></div>`;
                                });
                            })
                            .catch(err => {
                                console.error(err);
                                document.getElementById('zipFileList').innerHTML = `<div class="text-center text-danger p-4">Gagal mengunduh berkas ZIP dari server.</div>`;
                            });
                    });
                } else if (['mp4', 'webm', 'mov'].includes(ext)) {
                    const video = document.getElementById('videoPreviewPlayer');
                    video.src = data.file_url;
                    document.getElementById('videoPreviewContainer').style.display = 'block';
                } else if (['txt', 'log'].includes(ext)) {
                    document.getElementById('textPreviewContainer').style.display = 'block';
                    document.getElementById('textPreviewContent').innerText = 'Memuat isi berkas teks...';
                    fetch(data.file_url)
                        .then(res => res.text())
                        .then(text => {
                            document.getElementById('textPreviewContent').innerText = text;
                        })
                        .catch(() => {
                            document.getElementById('textPreviewContent').innerText = 'Gagal memuat isi berkas teks.';
                        });
                } else {
                    document.getElementById('fallbackFileName').innerText = data.file_name;
                    document.getElementById('fallbackDownloadBtn').href = `/documents/${docId}/download`;
                    const fbIcon = document.getElementById('fallbackIconBox');
                    fbIcon.style.background = data.file_icon.bg;
                    fbIcon.style.color = data.file_icon.color;
                    fbIcon.innerHTML = `<i class="${data.file_icon.icon}"></i>`;
                    document.getElementById('fallbackPreviewContainer').style.display = 'block';
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('previewDocTitle').innerText = 'Gagal memuat detail dokumen';
            });
    }

    // Stop video playback when preview modal is closed
    $('#previewDocumentModal').on('hidden.bs.modal', function () {
        const vid = document.getElementById('videoPreviewPlayer');
        if (vid) { vid.pause(); vid.src = ''; }
    });

    // Confirm Delete Document using SweetAlert2
    function confirmDeleteDoc(id, title) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Konfirmasi Hapus Dokumen',
                html: `Apakah Anda yakin ingin menghapus dokumen <b>"${title}"</b>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: { popup: 'swal2-minimal-rounded' }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-doc-form-${id}`).submit();
                }
            });
        } else {
            if (confirm(`Apakah Anda yakin ingin menghapus dokumen "${title}"?`)) {
                document.getElementById(`delete-doc-form-${id}`).submit();
            }
        }
    }
</script>
@endpush
@endsection
