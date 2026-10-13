@extends('layouts.admin')

@section('title', __('messages.bulk_print_barcode_title'))

@section('content')
<!-- Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 -->
<!-- Hallmark · genre: modern-minimal · macrostructure: workbench · design-system: design.md · designed-as-app -->
<style>
    /* Hide Floating Performance HUD & AI Chatbot Widget on page and print */
    #itam-perf-hud, .itam-hud-pill, #itam-hud-drawer-backdrop, #itam-hud-drawer,
    .ai-chat-floating-btn, #ai-assistant-fab, .ai-fab-btn, [class*="ai-chat"], [id*="ai-modal"],
    button[title*="AI"], button[onclick*="Ai"] {
        display: none !important;
    }

    /* Non-printable Control Header */
    .print-control-header {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.9), rgba(30, 41, 59, 0.8));
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 18px;
        padding: 16px 22px;
        margin-bottom: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    }
    .print-control-input {
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(148, 163, 184, 0.22);
        border-radius: 10px;
        color: #f8fafc;
        font-size: 0.84rem;
        padding: 6px 12px;
        height: 38px;
    }
    .print-btn-main {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        padding: 0 22px;
        height: 38px;
        font-weight: 700;
        font-size: 0.86rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.4);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .print-btn-main:hover {
        background: linear-gradient(135deg, #4338ca, #4f46e5);
        transform: translateY(-1px);
        color: #ffffff;
    }
    .print-btn-secondary {
        background: rgba(148, 163, 184, 0.15);
        color: #e2e8f0;
        border: 1px solid rgba(148, 163, 184, 0.25);
        border-radius: 10px;
        padding: 0 16px;
        height: 38px;
        font-weight: 700;
        font-size: 0.86rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .print-btn-secondary:hover {
        background: rgba(148, 163, 184, 0.28);
        color: #ffffff;
        text-decoration: none;
        transform: translateY(-1px);
    }

    /* 3-Column × 20-Row Full-Width Grid (Screen View) */
    .printable-sheet {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        width: 100%;
        max-width: 1280px;
        margin: 0 auto 30px auto;
        background: #ffffff;
        padding: 12px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }

    /* Individual Compact Label Card - 3×20 Proportions */
    .asset-label-card {
        width: 100%;
        height: 84px;
        background: #ffffff;
        color: #0f172a;
        border: 1.5px solid #0f172a;
        border-radius: 4px;
        padding: 5px 7px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        box-sizing: border-box;
        overflow: hidden;
        position: relative;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .label-inner-layout {
        display: flex;
        align-items: center;
        gap: 8px;
        height: 100%;
        width: 100%;
    }

    .label-qr-box {
        width: 48px;
        height: 48px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #ffffff;
    }
    .label-qr-box canvas, .label-qr-box img {
        width: 48px !important;
        height: 48px !important;
    }

    .label-details {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }

    .label-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #cbd5e1;
        padding-bottom: 1px;
    }
    .label-tag-code {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.78rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        letter-spacing: 0.01em;
    }
    .label-cat-badge {
        font-size: 0.44rem;
        font-weight: 800;
        text-transform: uppercase;
        background: #0f172a;
        color: #ffffff;
        padding: 1px 4px;
        border-radius: 2px;
        font-family: monospace;
        white-space: nowrap;
        max-width: 25mm;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
    }

    .label-asset-name {
        font-size: 0.58rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.15;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        margin: 1px 0;
    }

    .label-bottom-meta {
        font-size: 0.48rem;
        color: #64748b;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        border-top: 1px dashed #e2e8f0;
        padding-top: 1px;
    }

    /* Print Specific Media Styles - A4 5mm Margin 3-Column × 20-Row Grid (60 Labels per Page FULL) */
    @media print {
        @page {
            size: A4 portrait;
            margin: 5mm !important;
        }

        /* Force pure white background on all parent containers & eliminate AdminLTE layout offsets */
        *, *::before, *::after {
            box-shadow: none !important;
            text-shadow: none !important;
        }

        html, body,
        .wrapper,
        .layout-navbar-fixed .wrapper,
        .layout-navbar-fixed .wrapper .content-wrapper,
        div.content-wrapper,
        div.content-wrapper[style],
        section.content,
        section.content[style],
        div.container-fluid,
        div.container-fluid[style],
        .main-header, .main-sidebar, .main-footer, .content-header, .content, .container-fluid {
            background: #ffffff !important;
            background-color: #ffffff !important;
            color: #000000 !important;
            margin: 0mm !important;
            padding: 0mm !important;
            margin-top: 0mm !important;
            padding-top: 0mm !important;
            margin-left: 0mm !important;
            padding-left: 0mm !important;
            padding-right: 0mm !important;
            padding-bottom: 0mm !important;
            margin-bottom: 0mm !important;
            border: none !important;
            width: 100% !important;
            min-height: 0 !important;
            height: auto !important;
            box-shadow: none !important;
        }

        .main-header, .main-sidebar, .main-footer, .print-control-header, .content-header,
        #itam-perf-hud, .itam-hud-pill, #itam-hud-drawer-backdrop, #itam-hud-drawer,
        .ai-chat-floating-btn, #ai-assistant-fab, .ai-fab-btn, [class*="ai-chat"], [id*="ai-modal"],
        button[title*="AI"], button[onclick*="Ai"] {
            display: none !important;
        }

        /* 20 rows scaled to fill 100% of A4 page down to the bottom margin */
        .printable-sheet {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            grid-template-rows: repeat(20, 17.0mm) !important;
            column-gap: 3.0mm !important;
            row-gap: 1.6mm !important;
            padding: 0mm !important;
            margin: 0mm !important;
            width: 100% !important;
            box-sizing: border-box !important;
            background: #ffffff !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            page-break-after: always !important;
            break-after: page !important;
        }

        .printable-sheet:last-child {
            page-break-after: avoid !important;
            break-after: avoid !important;
        }

        .asset-label-card {
            width: 100% !important;
            height: 17.0mm !important;
            min-height: 17.0mm !important;
            max-height: 17.0mm !important;
            border: 1px solid #000000 !important;
            border-radius: 2px !important;
            box-shadow: none !important;
            padding: 1.0mm 2.0mm !important;
            box-sizing: border-box !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            overflow: hidden !important;
            background: #ffffff !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
        }

        .label-inner-layout {
            display: flex !important;
            align-items: center !important;
            gap: 2.5mm !important;
            height: 15.0mm !important;
            min-height: 15.0mm !important;
            max-height: 15.0mm !important;
            width: 100% !important;
        }

        /* 15mm QR Code matches scaled card inner height */
        .label-qr-box {
            width: 15.0mm !important;
            height: 15.0mm !important;
            min-width: 15.0mm !important;
            min-height: 15.0mm !important;
            flex-shrink: 0 !important;
        }
        .label-qr-box canvas, .label-qr-box img {
            width: 15.0mm !important;
            height: 15.0mm !important;
        }

        .label-details {
            flex: 1 !important;
            min-width: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            height: 15.0mm !important;
            min-height: 15.0mm !important;
            max-height: 15.0mm !important;
        }

        .label-header-row {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            border-bottom: 0.5px solid #cbd5e1 !important;
            padding-bottom: 0.3mm !important;
        }

        .label-tag-code {
            font-family: 'JetBrains Mono', monospace !important;
            font-size: 0.78rem !important;
            font-weight: 800 !important;
            color: #000000 !important;
            line-height: 1 !important;
            letter-spacing: 0.01em !important;
        }

        .label-cat-badge {
            font-size: 0.44rem !important;
            font-weight: 800 !important;
            background: #000000 !important;
            color: #ffffff !important;
            padding: 0.3mm 1.2mm !important;
            border-radius: 2px !important;
            line-height: 1 !important;
            max-width: 25mm !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            display: inline-block !important;
        }

        .label-asset-name {
            font-size: 0.54rem !important;
            font-weight: 700 !important;
            line-height: 1.15 !important;
            color: #0f172a !important;
            max-height: 6.5mm !important;
            margin: 0.3mm 0 !important;
            display: -webkit-box !important;
            -webkit-line-clamp: 2 !important;
            -webkit-box-orient: vertical !important;
            overflow: hidden !important;
        }

        .label-bottom-meta {
            font-size: 0.46rem !important;
            color: #475569 !important;
            font-weight: 600 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            border-top: 0.5px dashed #cbd5e1 !important;
            padding-top: 0.3mm !important;
            line-height: 1 !important;
        }
    }

    /* ── ITAM Multi-Select (print_barcodes) ─────────────────────────── */
    .itam-ms-wrap {
        position: relative;
        user-select: none;
    }
    .itam-ms-trigger {
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        padding: 0 10px 0 12px;
        border-radius: 10px;
        border: 1px solid rgba(148, 163, 184, 0.22);
        background: rgba(15, 23, 42, 0.5);
        color: #f8fafc;
        font-size: 0.84rem;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
        overflow: hidden;
        width: 100%;
    }
    .itam-ms-trigger:hover,
    .itam-ms-wrap.open .itam-ms-trigger {
        border-color: #6366f1;
        box-shadow: 0 0 0 2px rgba(99,102,241,0.25);
        outline: none;
    }
    .itam-ms-wrap.has-value .itam-ms-trigger {
        border-color: rgba(99,102,241,0.6);
        background: rgba(99,102,241,0.12);
    }
    .itam-ms-label { flex: 1; overflow: hidden; text-overflow: ellipsis; text-align: left; font-weight: 500; }
    .itam-ms-chevron { font-size: 0.7rem; flex-shrink: 0; transition: transform 0.2s ease; opacity: 0.55; }
    .itam-ms-wrap.open .itam-ms-chevron { transform: rotate(180deg); opacity: 1; }
    .itam-ms-panel {
        display: none;
        position: absolute;
        top: calc(100% + 5px);
        left: 0;
        z-index: 2000;
        min-width: 220px;
        max-width: 280px;
        background: rgba(15, 23, 42, 0.98);
        border: 1px solid rgba(99,102,241,0.4);
        border-radius: 12px;
        box-shadow: 0 16px 40px rgba(0,0,0,0.55);
        overflow: hidden;
        flex-direction: column;
    }
    .itam-ms-wrap.open .itam-ms-panel { display: flex; }
    .itam-ms-search-wrap { position: relative; padding: 8px 8px 5px; border-bottom: 1px solid rgba(148,163,184,0.12); }
    .itam-ms-search-icon { position: absolute; left: 18px; top: 50%; transform: translateY(-38%); font-size: 0.72rem; color: rgba(148,163,184,0.65); }
    .itam-ms-search { width: 100%; height: 30px; padding: 0 6px 0 26px; border-radius: 7px; border: 1px solid rgba(148,163,184,0.2); background: rgba(255,255,255,0.05); color: #f8fafc; font-size: 0.78rem; outline: none; }
    .itam-ms-search:focus { border-color: #6366f1; }
    .itam-ms-select-all-row { padding: 1px 4px 0; border-bottom: 1px solid rgba(148,163,184,0.1); }
    .itam-ms-list { max-height: 200px; overflow-y: auto; padding: 4px; scrollbar-width: thin; scrollbar-color: rgba(99,102,241,0.4) transparent; }
    .itam-ms-list::-webkit-scrollbar { width: 3px; }
    .itam-ms-list::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.4); border-radius: 3px; }
    .itam-ms-item { display: flex; align-items: center; gap: 7px; padding: 5px 9px; border-radius: 7px; cursor: pointer; font-size: 0.8rem; color: #cbd5e1; transition: background 0.12s ease; margin: 1px 0; }
    .itam-ms-item:hover { background: rgba(99,102,241,0.12); color: #f8fafc; }
    .itam-ms-item input[type="checkbox"] { width: 14px; height: 14px; border-radius: 3px; accent-color: #6366f1; flex-shrink: 0; cursor: pointer; }
    .itam-ms-item.itam-ms-selectall { font-weight: 600; color: #e2e8f0; }
    .itam-ms-footer { display: flex; align-items: center; justify-content: space-between; padding: 7px 9px; border-top: 1px solid rgba(148,163,184,0.1); gap: 6px; }
    .itam-ms-reset-btn { background: transparent; border: 1px solid rgba(148,163,184,0.28); border-radius: 7px; color: #94a3b8; font-size: 0.75rem; padding: 3px 10px; cursor: pointer; transition: all 0.14s ease; }
    .itam-ms-reset-btn:hover { border-color: #ef4444; color: #ef4444; }
    .itam-ms-apply-btn { background: linear-gradient(135deg, #4f46e5, #6366f1); border: none; border-radius: 7px; color: #fff; font-size: 0.75rem; font-weight: 700; padding: 3px 12px; cursor: pointer; transition: all 0.14s ease; }
    .itam-ms-apply-btn:hover { background: linear-gradient(135deg, #4338ca, #4f46e5); }
</style>

<div class="container-fluid">
    <!-- Non-printable Control Bar -->
    <div class="print-control-header d-print-none" id="print-barcode-control">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3" style="gap: 16px;">
            <div>
                <h4 class="h5 font-weight-bold text-white mb-1">
                    <i class="fas fa-print text-indigo mr-2"></i> {{ __('messages.bulk_print_barcode_title') }}
                </h4>
                <p class="text-muted small mb-0">
                    {{ __('messages.print_barcodes_subtitle', ['count' => count($assets)]) }}
                </p>
                @php
                    $selPrintYears  = (array) request('date_received', []);
                    $selPrintCats   = (array) request('category_id', []);
                    $selPrintBrands = (array) request('brand_id', []);
                    $selPrintLocs   = (array) request('location_id', []);

                    $activeFilterTags = [];
                    if (!empty($selPrintYears)) {
                        $activeFilterTags[] = __('messages.year') . ': ' . implode(', ', $selPrintYears);
                    } elseif (request('year')) {
                        $activeFilterTags[] = __('messages.year') . ': ' . request('year');
                    }
                    if (!empty($selPrintCats)) {
                        $catNames = $categories->whereIn('id', $selPrintCats)->pluck('name')->toArray();
                        $activeFilterTags[] = __('messages.category') . ': ' . implode(', ', $catNames ?: $selPrintCats);
                    } elseif (request('category')) {
                        $activeFilterTags[] = __('messages.category') . ': ' . request('category');
                    }
                    if (!empty($selPrintBrands) && isset($brands)) {
                        $brandNames = $brands->whereIn('id', $selPrintBrands)->pluck('name')->toArray();
                        $activeFilterTags[] = __('messages.brand') . ': ' . implode(', ', $brandNames ?: $selPrintBrands);
                    }
                    if (!empty($selPrintLocs)) {
                        $locNames = $locations->whereIn('id', $selPrintLocs)->pluck('name')->toArray();
                        $activeFilterTags[] = __('messages.location') . ': ' . implode(', ', $locNames ?: $selPrintLocs);
                    }
                    if (request('status')) {
                        $activeFilterTags[] = __('messages.status') . ': ' . request('status');
                    }
                    if (request('search')) {
                        $activeFilterTags[] = __('messages.search') . ': "' . request('search') . '"';
                    }
                @endphp
                @if(count($activeFilterTags) > 0)
                <div class="mt-2 d-flex align-items-center flex-wrap" style="gap: 6px;">
                    <span class="text-info small font-weight-bold" style="font-size: 0.78rem;">
                        <i class="fas fa-filter mr-1"></i> {{ __('messages.active_filters') }}:
                    </span>
                    @foreach($activeFilterTags as $filterTag)
                        <span class="badge" style="background: rgba(99, 102, 241, 0.25); color: #c7d2fe; border: 1px solid rgba(99, 102, 241, 0.4); padding: 3px 8px; font-weight: 600; border-radius: 6px; font-size: 0.76rem;">
                            {{ $filterTag }}
                        </span>
                    @endforeach
                    <a href="{{ route('assets.print-barcodes') }}" class="badge badge-secondary ml-1" style="text-decoration: none; padding: 4px 8px; font-size: 0.74rem;" title="{{ __('messages.reset_all_filters') }}">
                        <i class="fas fa-times mr-1"></i> {{ __('messages.reset') }}
                    </a>
                </div>
                @endif
            </div>
            <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                <a href="{{ route('assets.index', request()->query()) }}" class="print-btn-secondary" title="{{ __('messages.back') }}">
                    <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                </a>

                <form method="GET" action="{{ route('assets.print-barcodes') }}" id="print-filter-form" class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('sort_order'))
                        <input type="hidden" name="sort_order" value="{{ request('sort_order') }}">
                    @endif
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif
                    @if(request('direction'))
                        <input type="hidden" name="direction" value="{{ request('direction') }}">
                    @endif

                    {{-- Tahun --}}
                    @if(!empty($years))
                    <div class="itam-ms-wrap" data-name="date_received" data-placeholder="{{ __('messages.all_years') }}" style="min-width:110px; max-width:140px;">
                        <button type="button" class="itam-ms-trigger"><span class="itam-ms-label">{{ !empty($selPrintYears) ? __('messages.selected_count', ['count' => count($selPrintYears)]) : __('messages.all_years') }}</span><i class="fas fa-chevron-down itam-ms-chevron"></i></button>
                        <div class="itam-ms-panel">
                            <div class="itam-ms-search-wrap"><i class="fas fa-search itam-ms-search-icon"></i><input type="text" class="itam-ms-search" placeholder="{{ __('messages.search_year') }}"></div>
                            <div class="itam-ms-select-all-row"><label class="itam-ms-item itam-ms-selectall"><input type="checkbox" class="itam-ms-selectall-cb"> {{ __('messages.select_all') }}</label></div>
                            <div class="itam-ms-list">
                                @foreach($years as $yr)
                                <label class="itam-ms-item" data-label="{{ $yr }}">
                                    <input type="checkbox" name="date_received[]" value="{{ $yr }}" {{ in_array((string)$yr, $selPrintYears) ? 'checked' : '' }}>{{ $yr }}
                                </label>
                                @endforeach
                            </div>
                            <div class="itam-ms-footer"><button type="button" class="itam-ms-reset-btn">{{ __('messages.reset') }}</button><button type="button" class="itam-ms-apply-btn">{{ __('messages.apply') }}</button></div>
                        </div>
                    </div>
                    @endif

                    {{-- Kategori --}}
                    <div class="itam-ms-wrap" data-name="category_id" data-placeholder="{{ __('messages.all_categories') }}" style="min-width:120px; max-width:160px;">
                        <button type="button" class="itam-ms-trigger"><span class="itam-ms-label">{{ !empty($selPrintCats) ? __('messages.selected_count', ['count' => count($selPrintCats)]) : __('messages.all_categories') }}</span><i class="fas fa-chevron-down itam-ms-chevron"></i></button>
                        <div class="itam-ms-panel">
                            <div class="itam-ms-search-wrap"><i class="fas fa-search itam-ms-search-icon"></i><input type="text" class="itam-ms-search" placeholder="{{ __('messages.search_category') }}"></div>
                            <div class="itam-ms-select-all-row"><label class="itam-ms-item itam-ms-selectall"><input type="checkbox" class="itam-ms-selectall-cb"> {{ __('messages.select_all') }}</label></div>
                            <div class="itam-ms-list">
                                @foreach($categories as $cat)
                                <label class="itam-ms-item" data-label="{{ $cat->name }}">
                                    <input type="checkbox" name="category_id[]" value="{{ $cat->id }}" {{ (in_array((string)$cat->id, $selPrintCats) || request('category') == $cat->name) ? 'checked' : '' }}>{{ $cat->name }}
                                </label>
                                @endforeach
                            </div>
                            <div class="itam-ms-footer"><button type="button" class="itam-ms-reset-btn">{{ __('messages.reset') }}</button><button type="button" class="itam-ms-apply-btn">{{ __('messages.apply') }}</button></div>
                        </div>
                    </div>

                    {{-- Brand --}}
                    @if(!empty($brands))
                    <div class="itam-ms-wrap" data-name="brand_id" data-placeholder="{{ __('messages.all_brands') }}" style="min-width:110px; max-width:150px;">
                        <button type="button" class="itam-ms-trigger"><span class="itam-ms-label">{{ !empty($selPrintBrands) ? __('messages.selected_count', ['count' => count($selPrintBrands)]) : __('messages.all_brands') }}</span><i class="fas fa-chevron-down itam-ms-chevron"></i></button>
                        <div class="itam-ms-panel">
                            <div class="itam-ms-search-wrap"><i class="fas fa-search itam-ms-search-icon"></i><input type="text" class="itam-ms-search" placeholder="{{ __('messages.search_brand') }}"></div>
                            <div class="itam-ms-select-all-row"><label class="itam-ms-item itam-ms-selectall"><input type="checkbox" class="itam-ms-selectall-cb"> {{ __('messages.select_all') }}</label></div>
                            <div class="itam-ms-list">
                                @foreach($brands as $brand)
                                <label class="itam-ms-item" data-label="{{ $brand->name }}">
                                    <input type="checkbox" name="brand_id[]" value="{{ $brand->id }}" {{ in_array((string)$brand->id, $selPrintBrands) ? 'checked' : '' }}>{{ $brand->name }}
                                </label>
                                @endforeach
                            </div>
                            <div class="itam-ms-footer"><button type="button" class="itam-ms-reset-btn">{{ __('messages.reset') }}</button><button type="button" class="itam-ms-apply-btn">{{ __('messages.apply') }}</button></div>
                        </div>
                    </div>
                    @endif

                    {{-- Lokasi --}}
                    <div class="itam-ms-wrap" data-name="location_id" data-placeholder="{{ __('messages.all_locations') }}" style="min-width:120px; max-width:165px;">
                        <button type="button" class="itam-ms-trigger"><span class="itam-ms-label">{{ !empty($selPrintLocs) ? __('messages.selected_count', ['count' => count($selPrintLocs)]) : __('messages.all_locations') }}</span><i class="fas fa-chevron-down itam-ms-chevron"></i></button>
                        <div class="itam-ms-panel">
                            <div class="itam-ms-search-wrap"><i class="fas fa-search itam-ms-search-icon"></i><input type="text" class="itam-ms-search" placeholder="{{ __('messages.search_location') }}"></div>
                            <div class="itam-ms-select-all-row"><label class="itam-ms-item itam-ms-selectall"><input type="checkbox" class="itam-ms-selectall-cb"> {{ __('messages.select_all') }}</label></div>
                            <div class="itam-ms-list">
                                @foreach($locations as $loc)
                                <label class="itam-ms-item" data-label="{{ $loc->name }}">
                                    <input type="checkbox" name="location_id[]" value="{{ $loc->id }}" {{ in_array((string)$loc->id, $selPrintLocs) ? 'checked' : '' }}>{{ $loc->name }}
                                </label>
                                @endforeach
                            </div>
                            <div class="itam-ms-footer"><button type="button" class="itam-ms-reset-btn">{{ __('messages.reset') }}</button><button type="button" class="itam-ms-apply-btn">{{ __('messages.apply') }}</button></div>
                        </div>
                    </div>
                </form>

                <button type="button" onclick="window.print()" class="print-btn-main">
                    <i class="fas fa-print"></i> {{ __('messages.print_labels') }} (Ctrl+P)
                </button>
            </div>
        </div>
    </div>

    <!-- Printable 3-Column × 20-Row Grid (Chunked by 60 labels per page) -->
    @forelse($assets->chunk(60) as $chunkIndex => $assetChunk)
    <div class="printable-sheet" id="printableSheet-{{ $chunkIndex }}">
        @foreach($assetChunk as $asset)
        <div class="asset-label-card">
            <div class="label-inner-layout">
                <!-- Vector QR Code -->
                <div class="label-qr-box" id="qr-box-{{ $asset->id }}" data-url="{{ route('assets.show', $asset->id) }}"></div>

                <!-- Asset Information -->
                <div class="label-details">
                    <div class="label-header-row">
                        <span class="label-tag-code">{{ $asset->asset_tag }}</span>
                        <span class="label-cat-badge" title="{{ $asset->category->name ?? 'ASSET' }}">{{ strtoupper($asset->category->name ?? 'ASSET') }}</span>
                    </div>
                    <div class="label-asset-name" title="{{ $asset->name }}">{{ $asset->name }}</div>
                    <div class="label-bottom-meta">
                        <i class="fas fa-map-marker-alt"></i> {{ $asset->location->name ?? 'Root' }}
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @empty
    <div class="col-12 py-5 text-center text-muted d-print-none">
        <i class="fas fa-barcode fa-3x mb-3 text-secondary opacity-50"></i>
        <div class="h5 font-weight-bold text-white">{{ __('messages.no_matching_assets') }}</div>
        <p class="small text-muted">{{ __('messages.print_barcodes_empty_hint') }}</p>
    </div>
    @endforelse
</div>

@push('scripts')
<!-- QRCode.js Library -->
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Generate QR Codes (High Res Canvas)
        document.querySelectorAll('.label-qr-box').forEach(box => {
            const url = box.getAttribute('data-url');
            if (url) {
                new QRCode(box, {
                    text: url,
                    width: 64,
                    height: 64,
                    colorDark : "#0f172a",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.M
                });
            }
        });

        // Reset AdminLTE inline style padding-top on print so Page 1 starts at exact top margin
        window.addEventListener('beforeprint', function() {
            const cw = document.querySelector('.content-wrapper');
            if (cw) {
                cw.setAttribute('data-orig-pt', cw.style.paddingTop);
                cw.style.setProperty('padding-top', '0px', 'important');
                cw.style.setProperty('margin-top', '0px', 'important');
            }
            const sc = document.querySelector('section.content');
            if (sc) {
                sc.setAttribute('data-orig-pt', sc.style.paddingTop);
                sc.style.setProperty('padding-top', '0px', 'important');
            }
            document.querySelectorAll('.container-fluid').forEach(c => {
                c.setAttribute('data-orig-pt', c.style.paddingTop);
                c.style.setProperty('padding-top', '0px', 'important');
                c.style.setProperty('padding-bottom', '0px', 'important');
            });
        });

        window.addEventListener('afterprint', function() {
            const cw = document.querySelector('.content-wrapper');
            if (cw && cw.getAttribute('data-orig-pt')) {
                cw.style.setProperty('padding-top', cw.getAttribute('data-orig-pt'), 'important');
            }
            const sc = document.querySelector('section.content');
            if (sc && sc.getAttribute('data-orig-pt')) {
                sc.style.setProperty('padding-top', sc.getAttribute('data-orig-pt'), 'important');
            }
            document.querySelectorAll('.container-fluid').forEach(c => {
                if (c.getAttribute('data-orig-pt')) {
                    c.style.setProperty('padding-top', c.getAttribute('data-orig-pt'), 'important');
                }
            });
        });
    });

    // ── ITAM Custom Multi-Select (print_barcodes) ────────────────────────────
    (function() {
        var selectedCountTpl = @json(__('messages.selected_count'));
        function itamUpdateLabel(wrap) {
            var checked = wrap.querySelectorAll('.itam-ms-list input[type="checkbox"]:checked');
            var label   = wrap.querySelector('.itam-ms-label');
            var placeholder = wrap.dataset.placeholder || wrap.getAttribute('data-placeholder') || '...';
            if (checked.length === 0) {
                label.textContent = placeholder;
                wrap.classList.remove('has-value');
            } else if (checked.length === 1) {
                var lbl = checked[0].closest('[data-label]');
                label.textContent = lbl ? lbl.dataset.label : checked[0].value;
                wrap.classList.add('has-value');
            } else {
                label.textContent = selectedCountTpl.replace(':count', checked.length);
                wrap.classList.add('has-value');
            }
        }
        function itamInitWrap(wrap) {
            var trigger  = wrap.querySelector('.itam-ms-trigger');
            var search   = wrap.querySelector('.itam-ms-search');
            var items    = wrap.querySelectorAll('.itam-ms-list .itam-ms-item');
            var allCb    = wrap.querySelector('.itam-ms-selectall-cb');
            var resetBtn = wrap.querySelector('.itam-ms-reset-btn');
            var applyBtn = wrap.querySelector('.itam-ms-apply-btn');
            var formId   = 'print-filter-form';

            itamUpdateLabel(wrap);

            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                var isOpen = wrap.classList.contains('open');
                document.querySelectorAll('.itam-ms-wrap.open').forEach(function(w) { if (w !== wrap) w.classList.remove('open'); });
                wrap.classList.toggle('open', !isOpen);
                if (!isOpen && search) setTimeout(function(){ search.focus(); }, 50);
            });
            if (search) {
                search.addEventListener('input', function() {
                    var q = this.value.toLowerCase().trim();
                    items.forEach(function(item) {
                        var lbl = (item.dataset.label || item.textContent).toLowerCase();
                        item.style.display = (q === '' || lbl.includes(q)) ? '' : 'none';
                    });
                    if (allCb) {
                        var vis = wrap.querySelectorAll('.itam-ms-list .itam-ms-item:not([style*="display: none"]) input[type="checkbox"]');
                        var chk = wrap.querySelectorAll('.itam-ms-list .itam-ms-item:not([style*="display: none"]) input[type="checkbox"]:checked');
                        allCb.checked = vis.length > 0 && vis.length === chk.length;
                        allCb.indeterminate = chk.length > 0 && chk.length < vis.length;
                    }
                });
            }
            if (allCb) {
                allCb.addEventListener('change', function() {
                    var vis = wrap.querySelectorAll('.itam-ms-list .itam-ms-item:not([style*="display: none"]) input[type="checkbox"]');
                    vis.forEach(function(cb) { cb.checked = allCb.checked; });
                    itamUpdateLabel(wrap);
                });
            }
            items.forEach(function(item) {
                var cb = item.querySelector('input[type="checkbox"]');
                if (!cb) return;
                cb.addEventListener('change', function() {
                    if (allCb) {
                        var vis = wrap.querySelectorAll('.itam-ms-list .itam-ms-item:not([style*="display: none"]) input[type="checkbox"]');
                        var chk = wrap.querySelectorAll('.itam-ms-list .itam-ms-item:not([style*="display: none"]) input[type="checkbox"]:checked');
                        allCb.checked = vis.length > 0 && vis.length === chk.length;
                        allCb.indeterminate = chk.length > 0 && chk.length < vis.length;
                    }
                    itamUpdateLabel(wrap);
                });
            });
            if (resetBtn) {
                resetBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    wrap.querySelectorAll('input[type="checkbox"]').forEach(function(cb){ cb.checked = false; });
                    if (allCb) { allCb.checked = false; allCb.indeterminate = false; }
                    itamUpdateLabel(wrap);
                });
            }
            if (applyBtn) {
                applyBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    wrap.classList.remove('open');
                    var f = document.getElementById(formId);
                    if (f) f.submit();
                });
            }
        }

        document.querySelectorAll('#print-barcode-control .itam-ms-wrap').forEach(itamInitWrap);
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.itam-ms-wrap')) {
                document.querySelectorAll('.itam-ms-wrap.open').forEach(function(w) { w.classList.remove('open'); });
            }
        });
    })();
</script>
@endpush
@endsection
