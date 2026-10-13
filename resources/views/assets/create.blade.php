@extends('layouts.admin')

@section('title', __('messages.add') . ' ' . __('messages.asset'))

@section('content')

<!-- Hallmark Master Header Banner -->
<div class="master-header-banner">
    <div class="d-flex align-items-center justify-content-between flex-wrap flex-md-nowrap" style="gap: 16px;">
        <div class="d-flex align-items-center" style="gap: 14px; min-width: 0; flex: 1;">
            <div style="width: 44px; height: 44px; border-radius: 14px; background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(79, 70, 229, 0.08)); border: 1px solid rgba(99, 102, 241, 0.35); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 1.2rem; flex-shrink: 0; box-shadow: 0 0 14px rgba(99, 102, 241, 0.25);">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div style="flex: 1; min-width: 0;">
                <h5 class="mb-0 font-weight-bold theme-text text-truncate" style="font-size: 1.15rem; line-height: 1.2;">{{ __('messages.add') }} {{ __('messages.asset') }}</h5>
                <small class="text-muted d-block" style="font-size: 0.8rem; margin-top: 2px; white-space: normal; line-height: 1.3;">{{ __('messages.asset_create_subtitle') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="card theme-card master-form-card asset-form-card">
    @if(session('error'))
        <div class="alert alert-danger mx-4 mt-4 mb-0" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; border-radius: 14px; backdrop-filter: blur(10px);">
            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mx-4 mt-4 mb-0" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; border-radius: 14px; backdrop-filter: blur(10px);">
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ __('messages.input_error_occurred') }}
            <ul class="mb-0 mt-1 pl-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Hallmark Smart OCR Hub Widget -->
    <div class="ocr-scan-widget p-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex align-items-center" style="gap: 12px;">
                <div style="width: 40px; height: 40px; min-width: 40px; min-height: 40px; flex-shrink: 0; aspect-ratio: 1 / 1; border-radius: 12px; background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 1.05rem;">
                    <i class="fas fa-camera"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold theme-text mb-0" style="font-size: 0.98rem; line-height: 1.2;">
                        {{ __('messages.ocr_title') }}
                    </h5>
                    <small class="text-muted" style="font-size: 0.78rem;">{{ __('messages.ocr_subtitle') }}</small>
                </div>
            </div>
        </div>
        <input type="file" id="ocrFileInput" accept="image/*,.pdf" style="display: none;">
        <div class="ocr-dropzone mt-3 text-center p-3" id="ocrDropzone">
            <div id="ocrInitialState">
                <i class="fas fa-cloud-upload-alt text-info mb-2" style="font-size: 2rem;"></i>
                <p class="theme-text font-weight-bold mb-1">{{ __('messages.ocr_dropzone_title') }}</p>
                <small class="text-muted">{{ __('messages.ocr_dropzone_formats') }}</small>
            </div>
            <div id="ocrLoadingState" style="display: none;">
                <i class="fas fa-spinner fa-spin text-info mb-2" style="font-size: 2rem;"></i>
                <p class="theme-text font-weight-bold mb-1">{{ __('messages.ocr_loading_title') }}</p>
                <small class="text-muted">{{ __('messages.ocr_loading_subtitle') }}</small>
            </div>
        </div>

        <!-- Hallmark Multi-Item OCR Selection Panel (Hidden by default) -->
        <div id="ocrMultiItemPanel" class="mt-3 p-3 p-md-4 ocr-multi-panel position-relative" style="display: none;">
            <!-- Panel Loading Overlay for Rescan / Switch Document -->
            <div id="ocrPanelLoadingOverlay" style="display: none; position: absolute; inset: 0; background: rgba(15, 23, 42, 0.88); backdrop-filter: blur(8px); border-radius: 16px; z-index: 20; align-items: center; justify-content: center; flex-direction: column;">
                <i class="fas fa-spinner fa-spin text-info mb-3" style="font-size: 2.4rem; color: #818cf8;"></i>
                <h6 class="font-weight-bold theme-text mb-1" style="font-size: 0.98rem;">{{ __('messages.ocr_scanning_overlay_title') }}</h6>
                <small class="text-muted" style="font-size: 0.8rem;">{{ __('messages.ocr_scanning_overlay_subtitle') }}</small>
            </div>

            <!-- Document Meta Header -->
            <div class="d-flex align-items-center justify-content-between flex-wrap pb-3 mb-3 ocr-panel-header" style="border-bottom: 1px dashed rgba(148, 163, 184, 0.2); gap: 12px;">
                <div style="flex: 1; min-width: 260px;">
                    <div class="d-flex align-items-center flex-wrap" style="gap: 8px; margin-bottom: 8px;">
                        <span class="badge badge-indigo px-2.5 py-1" style="font-size: 0.78rem; display: inline-flex; align-items: center;">
                            <i class="fas fa-file-invoice mr-1.5"></i> <span id="ocrMetaDo">{{ __('messages.ocr_delivery_order') }}</span>
                        </span>
                        <span id="ocrMetaPoWrap" style="display: none;">
                            <span class="badge badge-secondary font-mono px-2 py-1" style="font-size: 0.75rem;" id="ocrMetaPo"></span>
                        </span>
                    </div>
                    <div id="ocrMetaSummary" class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                        <!-- Dynamic metadata chips -->
                    </div>
                </div>

                <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                    <!-- Ganti / Pindai Dokumen Lain -->
                    <button type="button" class="btn btn-sm ocr-btn-rescan" id="btnRescanOcr" title="{{ __('messages.ocr_change_rescan') }}">
                        <i class="fas fa-file-upload mr-1.5"></i> {{ __('messages.ocr_change_rescan') }}
                    </button>

                    <!-- Tutup Dokumen -->
                    <button type="button" class="btn btn-sm ocr-btn-close" id="btnCloseOcrPanel" title="{{ __('messages.ocr_close') }}">
                        <i class="fas fa-times mr-1"></i> {{ __('messages.ocr_close') }}
                    </button>
                </div>
            </div>
            
            <!-- Active Item Banner / Tracker -->
            <div class="ocr-active-banner d-flex align-items-center justify-content-between px-3 py-2.5 mb-3 flex-wrap" style="gap: 10px;">
                <div class="d-flex align-items-center" style="gap: 10px; min-width: 0; flex: 1;">
                    <span class="ocr-pulse-dot"></span>
                    <div class="text-truncate" style="min-width: 0;">
                        <span class="text-muted" style="font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('messages.ocr_filling_form') }}</span>
                        <strong class="theme-text ml-1" style="font-size: 0.86rem;" id="ocrActiveItemName">-</strong>
                    </div>
                </div>
                <div class="d-flex align-items-center" style="gap: 10px; flex-shrink: 0;">
                    <span class="badge badge-indigo" id="ocrActiveItemBadge" style="font-size: 0.74rem;">{{ __('messages.ocr_item_selected', ['num' => 1]) }}</span>
                    <span class="text-muted" style="font-size: 0.76rem;"><span id="ocrItemCount">0</span> <span id="ocrItemCountLabel">{{ __('messages.ocr_items_found') }}</span></span>
                </div>
            </div>

            <!-- Clean Card Grid View -->
            <div id="ocrItemsGrid" class="ocr-items-grid-container">
                <!-- Dynamic Item Cards injected via JS -->
            </div>
        </div>
    </div>

    <form action="{{ route('assets.store', request()->query()) }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- ===== BASIC INFORMATION ===== --}}
        <div class="form-section">
            <div class="section-title section-cyan">
                <span class="section-badge badge-cyan"><i class="fas fa-info-circle"></i></span>
                <div>
                    <div class="section-name">{{ __('messages.basic_info') }}</div>
                    <div class="section-sub">{{ __('messages.asset_tag') }}, {{ __('messages.asset_name') }}, Serial, Qty</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 form-group mb-3">
                    <label class="field-label">{{ __('messages.asset_tag') }} <span class="text-danger">*</span></label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="fas fa-qrcode"></i></span>
                        <input type="text" name="asset_tag"
                            class="field-input font-mono @error('asset_tag') is-invalid @enderror"
                            value="{{ old('asset_tag') }}" required
                            placeholder="e.g., IT-2024-001">
                    </div>
                    @error('asset_tag')<div class="field-error text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3 form-group mb-3">
                    <label class="field-label">{{ __('messages.asset_name') }} <span class="text-danger">*</span></label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="fas fa-laptop"></i></span>
                        <input type="text" name="name"
                            class="field-input @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required
                            placeholder="e.g., Lenovo ThinkPad E14">
                    </div>
                    @error('name')<div class="field-error text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-2 form-group mb-3">
                    <label class="field-label">UoM / Satuan</label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="fas fa-box"></i></span>
                        <input type="text" name="uom"
                            class="field-input font-mono @error('uom') is-invalid @enderror"
                            value="{{ old('uom') }}"
                            placeholder="e.g., Pieces, Unit">
                    </div>
                    @error('uom')<div class="field-error text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-2 form-group mb-3">
                    <label class="field-label">{{ __('messages.serial_number') }}</label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="fas fa-barcode"></i></span>
                        <input type="text" name="serial_number"
                            class="field-input font-mono @error('serial_number') is-invalid @enderror"
                            value="{{ old('serial_number') }}"
                            placeholder="e.g., SN-XXXX-XXXX">
                    </div>
                    @error('serial_number')<div class="field-error text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-2 form-group mb-3">
                    <label class="field-label">{{ __('messages.quantity') }} <small class="text-muted">({{ __('messages.bulk_add') }})</small></label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="fas fa-layer-group"></i></span>
                        <input type="number" name="quantity"
                            class="field-input font-mono @error('quantity') is-invalid @enderror"
                            value="{{ old('quantity', 1) }}" min="1" required>
                    </div>
                    <div class="field-hint text-muted" style="font-size: 0.72rem; margin-top: 4px;"><i class="fas fa-info-circle mr-1"></i> {{ __('messages.bulk_add_hint') }}</div>
                    @error('quantity')<div class="field-error text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12 form-group mt-2 mb-2">
                    <label class="field-label" style="font-size: 0.82rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: var(--color-ink-1); margin-bottom: 8px;">
                        {{ __('messages.asset_image') }} <small class="text-muted font-weight-normal">({{ __('messages.optional') }})</small>
                    </label>
                    <div class="custom-file-upload-card rounded-lg" style="padding: 16px 20px !important; background: rgba(15, 23, 42, 0.35); border: 1px dashed rgba(148, 163, 184, 0.25); border-radius: 12px; transition: all 0.2s ease;">
                        <div class="d-flex align-items-center flex-nowrap" style="gap: 16px;">
                            <div class="flex-shrink-0 position-relative d-flex align-items-center justify-content-center rounded-lg" style="width: 48px; height: 48px; min-width: 48px; min-height: 48px; flex-shrink: 0; aspect-ratio: 1 / 1; background: rgba(99, 102, 241, 0.08); border: 1px dashed rgba(99, 102, 241, 0.3); color: #818cf8; font-size: 1.15rem; overflow: hidden;">
                                <img id="create-asset-img-preview" src="#" alt="Preview" class="rounded-lg shadow-sm" style="width: 48px; height: 48px; object-fit: cover; display: none;">
                                <i id="create-asset-img-icon" class="fas fa-camera"></i>
                            </div>
                            <div class="flex-grow-1" style="min-width: 0;">
                                <div class="d-flex align-items-center flex-nowrap" style="gap: 8px;">
                                    <!-- Camera Direct Input -->
                                    <div class="position-relative">
                                        <input type="file" name="image" id="create-asset-camera-input" class="custom-file-input-field @error('image') is-invalid @enderror" accept="image/*" capture="environment" style="position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer; z-index: 2;" onchange="previewSelectedImage(this, 'create-asset-img-preview', 'create-asset-img-icon')">
                                        <label for="create-asset-camera-input" class="btn btn-sm btn-indigo mb-0 px-3 py-1.5 text-nowrap" style="background: linear-gradient(135deg, #6366f1, #4f46e5); color: #ffffff; border-radius: 8px; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 3px 10px rgba(99, 102, 241, 0.35);">
                                            <i class="fas fa-camera"></i> {{ __('messages.camera') }}
                                        </label>
                                    </div>
                                    <!-- File / Gallery Input -->
                                    <div class="position-relative">
                                        <input type="file" name="image" id="create-asset-file-input" class="custom-file-input-field @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp" style="position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer; z-index: 2;" onchange="previewSelectedImage(this, 'create-asset-img-preview', 'create-asset-img-icon')">
                                        <label for="create-asset-file-input" class="btn btn-sm btn-outline-secondary mb-0 px-3 py-1.5 text-nowrap" style="border: 1px solid rgba(148, 163, 184, 0.3); background: rgba(148, 163, 184, 0.1); color: var(--color-ink-1); border-radius: 8px; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                                            <i class="fas fa-folder-open"></i> {{ __('messages.file') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="field-hint text-muted text-truncate" style="font-size: 0.72rem; margin-top: 4px;">
                                    <i class="fas fa-info-circle mr-1"></i> {{ __('messages.camera') }} / {{ strtolower(__('messages.file')) }} ({{ App::getLocale() === 'en' ? 'Max 4MB' : 'Maks. 4MB' }})
                                </div>
                                @error('image')<div class="field-error text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== CLASSIFICATION & LOCATION ===== --}}
        <div class="form-section">
            <div class="section-title section-purple">
                <span class="section-badge badge-purple"><i class="fas fa-tags"></i></span>
                <div>
                    <div class="section-name">{{ __('messages.classification_location') }}</div>
                    <div class="section-sub">{{ __('messages.category') }}, {{ __('messages.brand') }}, {{ __('messages.location') }}</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 form-group mb-3">
                    <label class="field-label">{{ __('messages.category') }}</label>
                    <select id="select-category" name="category_id"
                        class="field-select select2-field custom-filter-select @error('category_id') is-invalid @enderror">
                        <option value="">{{ __('messages.select') }}</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label class="field-label">{{ __('messages.brand') }}</label>
                    <select id="select-brand" name="brand_id"
                        class="field-select select2-field custom-filter-select @error('brand_id') is-invalid @enderror">
                        <option value="">{{ __('messages.select') }}</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label class="field-label">{{ __('messages.location') }}</label>
                    <select id="select-location" name="location_id"
                        class="field-select select2-field custom-filter-select @error('location_id') is-invalid @enderror">
                        <option value="">{{ __('messages.select') }}</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ===== ACQUISITION ===== --}}
        <div class="form-section">
            <div class="section-title section-green">
                <span class="section-badge badge-green"><i class="fas fa-truck"></i></span>
                <div>
                    <div class="section-name">{{ __('messages.acquisition') }}</div>
                    <div class="section-sub">{{ __('messages.date_received') }}, DO, {{ __('messages.warranty_months') }}, {{ __('messages.status') }}, {{ __('messages.notes') }}</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 form-group mb-3">
                    <label class="field-label">{{ __('messages.date_received') }}</label>
                    <input type="text" id="assetDateReceived" name="date_received"
                        class="field-input font-mono asset-date-input @error('date_received') is-invalid @enderror"
                        value="{{ old('date_received') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                </div>

                <div class="col-md-3 form-group mb-3">
                    <label class="field-label">{{ __('messages.delivery_order') }}</label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="fas fa-file-invoice"></i></span>
                        <input type="text" name="delivery_order_number"
                            class="field-input font-mono @error('delivery_order_number') is-invalid @enderror"
                            value="{{ old('delivery_order_number') }}"
                            placeholder="e.g., DO/2024/001">
                    </div>
                </div>

                <div class="col-md-3 form-group mb-3">
                    <label class="field-label">{{ __('messages.warranty_months') }} <small class="text-muted">({{ __('messages.months') }})</small></label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="fas fa-shield-alt"></i></span>
                        <input type="number" name="warranty_months"
                            class="field-input font-mono @error('warranty_months') is-invalid @enderror"
                            value="{{ old('warranty_months', 60) }}" min="0"
                            placeholder="{{ __('messages.warranty_placeholder') }}">
                    </div>
                </div>

                <div class="col-md-3 form-group mb-3">
                    <label class="field-label">{{ __('messages.status') }} <span class="text-danger">*</span></label>
                    <select name="status" class="field-select select2-field custom-filter-select @error('status') is-invalid @enderror" required>
                        <option value="Available" {{ old('status','Available') == 'Available' ? 'selected' : '' }}>{{ __('messages.available') }}</option>
                        <option value="Assigned" {{ old('status') == 'Assigned' ? 'selected' : '' }}>{{ __('messages.assigned') }}</option>
                        <option value="Maintenance" {{ old('status') == 'Maintenance' ? 'selected' : '' }}>{{ __('messages.maintenance') }}</option>
                        <option value="Retired" {{ old('status') == 'Retired' ? 'selected' : '' }}>{{ __('messages.retired') }}</option>
                        <option value="Missing" {{ old('status') == 'Missing' ? 'selected' : '' }}>{{ __('messages.missing') }}</option>
                    </select>
                </div>

                <div class="col-md-12 form-group mb-2">
                    <label class="field-label">{{ __('messages.notes') }}</label>
                    <textarea name="notes" class="field-input @error('notes') is-invalid @enderror"
                        rows="2" placeholder="{{ __('messages.notes') }}...">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ===== DYNAMIC SPECIFICATIONS ===== --}}
        <div id="specifications-section" style="display:none;">
            <div class="form-section">
                <div class="section-title section-amber">
                    <span class="section-badge badge-amber"><i class="fas fa-cogs"></i></span>
                    <div>
                        <div class="section-name">{{ __('messages.category_specifications') }}</div>
                        <div class="section-sub">{{ __('messages.spec_based_on_category') }}</div>
                    </div>
                </div>

                {{-- Dynamic Custom Specifications --}}
                <div class="spec-group" id="spec-dynamic" style="display:none;">
                    <div class="row" id="dynamic-spec-container"></div>
                </div>

                {{-- Computer/Laptop --}}
                <div class="spec-group" id="spec-computer" style="display:none;">
                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label class="field-label">{{ __('messages.cpu_spec') }}</label>
                            <input type="text" name="cpu" class="field-input font-mono" placeholder="e.g., Core i5-10400 / AMD Ryzen 5">
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="field-label">{{ __('messages.ram_size') }} (GB)</label>
                            <input type="number" name="ram" class="field-input font-mono" placeholder="e.g. 8, 16, 32">
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="field-label">{{ __('messages.gpu_spec') }}</label>
                            <input type="text" name="gpu" class="field-input font-mono" placeholder="e.g., Nvidia GTX 1650 / Intel UHD">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label class="field-label">{{ __('messages.ssd_size') }} (GB)</label>
                            <input type="number" name="ssd" class="field-input font-mono" placeholder="e.g. 256, 512">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label class="field-label">{{ __('messages.hdd_size') }} (GB)</label>
                            <input type="number" name="hdd" class="field-input font-mono" placeholder="e.g. 1000">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label class="field-label">{{ __('messages.os') }}</label>
                            <input type="text" name="os" class="field-input font-mono" placeholder="e.g., Windows 11 Pro">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label class="field-label">{{ __('messages.office') }}</label>
                            <input type="text" name="office" class="field-input font-mono" placeholder="e.g., Office Home & Business 2021">
                        </div>
                    </div>
                </div>

                {{-- Printer --}}
                <div class="spec-group" id="spec-printer" style="display:none;">
                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label class="field-label">{{ __('messages.printer_type') }}</label>
                            <select name="printer_type" class="field-select custom-filter-select">
                                <option value="Laser">{{ __('messages.printer_type_laser') }}</option>
                                <option value="Inkjet">{{ __('messages.printer_type_inkjet') }}</option>
                                <option value="Thermal">{{ __('messages.printer_type_thermal') }}</option>
                                <option value="DotMatrix">{{ __('messages.printer_type_dotmatrix') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="field-label">{{ __('messages.connection') }}</label>
                            <select name="connection_type" class="field-select custom-filter-select">
                                <option value="USB">{{ __('messages.printer_conn_usb') }}</option>
                                <option value="Network">{{ __('messages.printer_conn_network') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group mb-3 d-flex align-items-end pb-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="has_scanner" value="1" class="custom-control-input" id="has_scanner">
                                <label class="custom-control-label theme-text" for="has_scanner">{{ __('messages.has_scanner') }}</label>
                            </div>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="field-label">{{ __('messages.initial_counter') }}</label>
                            <input type="number" name="counter_print" class="field-input font-mono" value="0">
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="field-label">{{ __('messages.toner_status') }}</label>
                            <input type="text" name="toner_status" class="field-input font-mono" placeholder="{{ __('messages.toner_status_placeholder') }}">
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="field-label">{{ __('messages.drum_status') }}</label>
                            <input type="text" name="drum_status" class="field-input font-mono" placeholder="{{ __('messages.drum_status_placeholder') }}">
                        </div>
                    </div>
                </div>

                {{-- Monitor --}}
                <div class="spec-group" id="spec-monitor" style="display:none;">
                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label class="field-label">{{ __('messages.monitor_size') }} (inch)</label>
                            <input type="number" step="0.1" name="monitor_size" class="field-input font-mono" placeholder="e.g., 21.5 / 24">
                        </div>
                    </div>
                </div>

                {{-- Network --}}
                <div class="spec-group" id="spec-network" style="display:none;">
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="field-label">{{ __('messages.firmware_version') }}</label>
                            <input type="text" name="network_firmware" class="field-input font-mono" placeholder="e.g., RouterOS v7.2 / IOS 15.2">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="field-label">{{ __('messages.controller_cloud') }}</label>
                            <input type="text" name="controller" class="field-input font-mono" placeholder="e.g., UniFi Controller / standalone">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label class="field-label">{{ __('messages.total_ports') }}</label>
                            <input type="number" name="port_count" class="field-input font-mono" placeholder="e.g., 24 / 48">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label class="field-label">{{ __('messages.active_ports') }}</label>
                            <input type="number" name="active_ports" class="field-input font-mono" placeholder="e.g., 18">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label class="field-label">{{ __('messages.wifi_ssid') }}</label>
                            <input type="text" name="ssid" class="field-input font-mono" placeholder="SSID Name">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label class="field-label">{{ __('messages.wifi_password') }}</label>
                            <input type="text" name="wifi_password" class="field-input font-mono" placeholder="WiFi Password">
                        </div>
                        <div class="col-md-12 form-group mb-3">
                            <label class="field-label">{{ __('messages.backup_config') }}</label>
                            <input type="text" name="backup_config_path" class="field-input font-mono" placeholder="e.g., \\cba-nas\configs\switch-core.backup">
                        </div>
                    </div>
                </div>

                {{-- CCTV --}}
                <div class="spec-group" id="spec-cctv" style="display:none;">
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="field-label">{{ __('messages.nvr_channel') }}</label>
                            <input type="number" name="nvr_channel" class="field-input font-mono" placeholder="e.g., 32">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="field-label">{{ __('messages.firmware_version') }}</label>
                            <input type="text" name="cctv_firmware" class="field-input font-mono" placeholder="e.g., v4.31.000">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="field-label">{{ __('messages.account_username') }}</label>
                            <input type="text" name="cctv_username" class="field-input font-mono" placeholder="admin">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="field-label">{{ __('messages.account_password') }}</label>
                            <input type="text" name="cctv_password" class="field-input font-mono" placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== STICKY FOOTER ===== --}}
        <div class="form-footer">
            <a href="{{ route('assets.index', request()->query()) }}" class="btn btn-sm btn-action-secondary px-4 py-2">
                {{ __('messages.cancel') }}
            </a>
            <button type="submit" class="btn btn-sm btn-action-primary px-4 py-2 btn-save">
                <i class="fas fa-save mr-1"></i> {{ __('messages.save') }}
            </button>
        </div>
    </form>
</div>

<style>
/* â”€â”€â”€ Master Form Card â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.asset-form-card {
    background: var(--color-paper-0);
    border: var(--rule-soft);
    border-radius: 20px !important;
    overflow: hidden !important;
    box-shadow: var(--shadow-card);
    margin-top: 0 !important;
}

/* â”€â”€â”€ Sections â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.form-section {
    padding: 22px 26px 12px;
    border-bottom: var(--rule-soft);
}
.form-section:last-of-type { border-bottom: none; }

.section-title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 18px;
}
.section-badge {
    width: 34px; height: 34px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.88rem;
    flex-shrink: 0;
}
.badge-cyan   { background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.25); }
.badge-purple { background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.25); }
.badge-green  { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.25); }
.badge-amber  { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.25); }

.section-name {
    font-size: 0.95rem; font-weight: 700; color: var(--color-ink-0);
    letter-spacing: .1px; line-height: 1.2;
}
.section-sub {
    font-size: 0.78rem; color: var(--color-ink-2); margin-top: 1px;
}

/* â”€â”€â”€ Field Labels & Inputs â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.field-label {
    display: block;
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: var(--color-ink-2);
    margin-bottom: 6px;
}

.font-mono {
    font-family: var(--font-mono) !important;
}

.input-wrap {
    position: relative;
}
.input-wrap .input-icon {
    position: absolute;
    left: 12px; top: 50%; transform: translateY(-50%);
    color: var(--color-accent);
    font-size: 0.84rem;
    pointer-events: none;
    z-index: 4;
}
.input-wrap .field-input {
    padding-left: 36px !important;
}
textarea.field-input {
    padding-left: 14px !important;
}

.field-input,
.field-select {
    width: 100%;
    background: rgba(15, 23, 42, 0.35) !important;
    border: 1px solid rgba(148, 163, 184, 0.2) !important;
    border-radius: 10px !important;
    color: var(--color-ink-0) !important;
    padding: 9px 14px;
    font-size: 0.86rem;
    transition: all 0.18s ease;
}
body.light-mode .field-input,
body.light-mode .field-select {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}
.field-input:focus,
.field-select:focus {
    outline: none;
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
}
.field-input::placeholder { color: var(--color-ink-3, #64748b); font-size: 0.82rem; }

/* â”€â”€â”€ OCR Widget â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.ocr-scan-widget {
    background: rgba(15, 23, 42, 0.3) !important;
    border-bottom: var(--rule-soft) !important;
    border-radius: 0 !important;
}
body.light-mode .ocr-scan-widget {
    background: rgba(241, 245, 249, 0.5) !important;
}
.ocr-dropzone {
    border: 2px dashed rgba(99, 102, 241, 0.35) !important;
    border-radius: 14px !important;
    background: rgba(15, 23, 42, 0.25) !important;
    transition: all 0.2s ease;
    cursor: pointer;
}
body.light-mode .ocr-dropzone {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
}
.ocr-dropzone:hover {
    border-color: #6366f1 !important;
    background: rgba(99, 102, 241, 0.06) !important;
}

/* ─── OCR Multi-Item Panel & Manifest Styling ────────────────── */
.ocr-multi-panel {
    background: rgba(15, 23, 42, 0.55) !important;
    border: 1px solid rgba(99, 102, 241, 0.25) !important;
    border-radius: 16px !important;
    backdrop-filter: blur(12px);
    overflow: hidden !important;
}
body.light-mode .ocr-multi-panel {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}

.ocr-meta-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 10px;
    border-radius: 8px;
    font-size: 0.76rem;
    font-weight: 500;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(148, 163, 184, 0.15);
    color: var(--color-ink-1, #cbd5e1);
}
body.light-mode .ocr-meta-chip {
    background: #f1f5f9;
    border-color: #e2e8f0;
    color: #475569;
}

.ocr-active-banner {
    background: rgba(99, 102, 241, 0.08);
    border: 1px solid rgba(99, 102, 241, 0.2);
    border-radius: 10px;
}
body.light-mode .ocr-active-banner {
    background: #eef2ff;
    border-color: #c7d2fe;
}
.ocr-pulse-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    animation: ocrPulse 1.8s infinite;
    flex-shrink: 0;
}
@keyframes ocrPulse {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

/* Card Grid View (Exclusive View) */
.ocr-items-grid-container {
    display: grid !important;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)) !important;
    gap: 16px !important;
    max-height: 460px !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    padding: 6px !important;
}

@media (max-width: 640px) {
    .ocr-items-grid-container {
        grid-template-columns: 1fr !important;
        gap: 12px !important;
        max-height: 400px !important;
    }
}

.ocr-item-card {
    border-radius: 12px !important;
    padding: 13px 15px !important;
    background: rgba(30, 41, 59, 0.75) !important;
    border: 1px solid rgba(148, 163, 184, 0.22) !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25) !important;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    min-height: 132px;
    cursor: pointer;
    position: relative;
    overflow: hidden !important;
    box-sizing: border-box !important;
}
body.light-mode .ocr-item-card {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06) !important;
}
.ocr-item-card:hover {
    transform: translateY(-2px);
    border-color: #818cf8 !important;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35) !important;
}
body.light-mode .ocr-item-card:hover {
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.15) !important;
}
.ocr-item-card.active-ocr-card {
    background: rgba(99, 102, 241, 0.16) !important;
    border: 1.5px solid #6366f1 !important;
    box-shadow: 0 0 0 1px #6366f1, 0 6px 20px rgba(99, 102, 241, 0.3) !important;
}
body.light-mode .ocr-item-card.active-ocr-card {
    background: #eef2ff !important;
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 1px #6366f1, 0 4px 16px rgba(99, 102, 241, 0.15) !important;
}
.ocr-item-card.saved-ocr-card {
    opacity: 0.55;
}
.ocr-item-title {
    font-size: 0.86rem;
    font-weight: 600;
    line-height: 1.42;
    margin-top: 6px;
    margin-bottom: 12px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    min-height: 2.45em;
    word-break: break-word;
    color: var(--color-ink-0, #f8fafc);
}
body.light-mode .ocr-item-title {
    color: #0f172a;
}


.badge-outline-primary {
    background: transparent !important;
    border: 1px solid rgba(99, 102, 241, 0.4) !important;
    color: #818cf8 !important;
}
body.light-mode .badge-outline-primary {
    border-color: #6366f1 !important;
    color: #4f46e5 !important;
}

.ocr-btn-rescan {
    height: 32px !important;
    padding: 0 14px !important;
    font-size: 0.75rem !important;
    font-weight: 600 !important;
    border-radius: 16px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    background: rgba(99, 102, 241, 0.15) !important;
    border: 1px solid rgba(99, 102, 241, 0.35) !important;
    color: #818cf8 !important;
    cursor: pointer;
    transition: all 0.2s ease !important;
}
.ocr-btn-rescan:hover {
    background: #6366f1 !important;
    border-color: #6366f1 !important;
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.35);
}
body.light-mode .ocr-btn-rescan {
    background: #eef2ff !important;
    border-color: #c7d2fe !important;
    color: #4f46e5 !important;
}
body.light-mode .ocr-btn-rescan:hover {
    background: #6366f1 !important;
    border-color: #6366f1 !important;
    color: #ffffff !important;
}

.ocr-btn-close {
    height: 32px !important;
    padding: 0 12px !important;
    font-size: 0.75rem !important;
    font-weight: 600 !important;
    border-radius: 16px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    background: rgba(239, 68, 68, 0.1) !important;
    border: 1px solid rgba(239, 68, 68, 0.25) !important;
    color: #f87171 !important;
    cursor: pointer;
    transition: all 0.2s ease !important;
}
.ocr-btn-close:hover {
    background: #ef4444 !important;
    border-color: #ef4444 !important;
    color: #ffffff !important;
    transform: translateY(-1px);
}
body.light-mode .ocr-btn-close {
    background: #fef2f2 !important;
    border-color: #fecaca !important;
    color: #dc2626 !important;
}
body.light-mode .ocr-btn-close:hover {
    background: #ef4444 !important;
    border-color: #ef4444 !important;
    color: #ffffff !important;
}

.ocr-multi-panel.drag-over {
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.4), 0 8px 24px rgba(99, 102, 241, 0.2) !important;
}

/* â”€â”€â”€ Sticky Footer Dock â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.form-footer {
    padding: 14px 26px;
    background: rgba(15, 23, 42, 0.8) !important;
    backdrop-filter: blur(14px);
    border-top: var(--rule-soft) !important;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
    position: sticky;
    bottom: 0;
    z-index: 10;
}
body.light-mode .form-footer {
    background: rgba(255, 255, 255, 0.9) !important;
    border-top-color: #e2e8f0 !important;
}

@media (max-width: 575.98px) {
    .form-footer {
        padding: 12px 16px !important;
        flex-direction: column-reverse !important;
        gap: 8px !important;
    }
    .form-footer .btn {
        width: 100% !important;
        justify-content: center !important;
    }
}
</style>

@push('scripts')
<script>
$(document).ready(function () {

    flatpickr('#assetDateReceived', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        allowInput: true,
        appendTo: document.body,
        disableMobile: true
    });

    // Init Select2 only on classification selects (not Status)
    $('.select2-field').select2({
        width: '100%',
        allowClear: true,
        placeholder: '{{ __("messages.select") }}'
    });

    // Category â†’ spec section switcher
    var catMap = {
        'komputer'     : 'computer',
        'laptop'       : 'computer',
        'mini pc'      : 'computer',
        'thin client'  : 'computer',
        'server'       : 'computer',
        'printer'      : 'printer',
        'monitor'      : 'monitor',
        'switch'       : 'network',
        'switch network': 'network',
        'router'       : 'network',
        'access point' : 'network',
        'firewall'     : 'network',
        'cctv'         : 'cctv',
        'camera'       : 'cctv'
    };

    var catSpecs = {!! json_encode($categories->mapWithKeys(function ($cat) {
        return [$cat->id => $cat->spec_definitions];
    })) !!};

    $('select[name="category_id"]').on('change', function () {
        var selectedOpt = $(this).find('option:selected');
        var selected = selectedOpt.text().trim().toLowerCase();
        var categoryId = $(this).val();

        $('.spec-group').hide();
        $('#specifications-section').hide();
        $('#dynamic-spec-container').empty();

        var group = catMap[selected];
        var dynamicSpecs = catSpecs[categoryId];

        if (dynamicSpecs && dynamicSpecs.length > 0) {
            $('#specifications-section').slideDown(200);
            $('#spec-dynamic').show();
            
            dynamicSpecs.forEach(function(spec) {
                var inputType = spec.type === 'date' ? 'date' : (spec.type === 'number' ? 'number' : 'text');
                var html = `
                    <div class="col-md-4 form-group mb-3">
                        <label class="field-label">${spec.label}</label>
                        <input type="${inputType}" name="spec_data[${spec.name}]" class="field-input font-mono" placeholder="${spec.label}">
                    </div>
                `;
                $('#dynamic-spec-container').append(html);
            });
        } else if (group) {
            $('#specifications-section').slideDown(200);
            $('#spec-' + group).show();
        }

        // Generate asset tag automatically based on category
        var currentTag = $('input[name="asset_tag"]').val();
        
        if (categoryId && (!currentTag || $(this).data('user-changed'))) {
            $.ajax({
                url: '{{ route("assets.generate-tag") }}',
                type: 'GET',
                data: { category_id: categoryId },
                success: function(response) {
                    if (response.tag) {
                        $('input[name="asset_tag"]').val(response.tag);
                    }
                },
                error: function() {
                    console.error('Failed to fetch generated tag');
                }
            });
        } else if (!categoryId && $(this).data('user-changed')) {
            $('input[name="asset_tag"]').val('');
        }
        
        $(this).data('user-changed', false);
    });

    $('select[name="category_id"]').on('select2:select select2:clear', function () {
        $(this).data('user-changed', true);
        $(this).trigger('change');
    });

    $('select[name="category_id"]').trigger('change');

    // OCR & Save Localization Helper
    const i18n = {
        saving: @json(__('messages.saving')),
        saved: @json(__('messages.ocr_saved')),
        savedSuccess: @json(__('messages.item_saved_success')),
        saveFailed: @json(__('messages.save_failed')),
        saveFailedMsg: @json(__('messages.save_failed_msg')),
        scanning: @json(__('messages.ocr_scanning')),
        changeRescan: @json(__('messages.ocr_change_rescan')),
        selectItem: @json(__('messages.ocr_select_item')),
        selected: @json(__('messages.ocr_selected')),
        noBrand: @json(__('messages.ocr_no_brand')),
        noItems: @json(__('messages.ocr_no_items')),
        deliveryOrder: @json(__('messages.ocr_delivery_order')),
        closeConfirmTitle: @json(__('messages.ocr_close_confirm_title')),
        closeConfirmText: @json(__('messages.ocr_close_confirm_text')),
        closeConfirmBtn: @json(__('messages.ocr_close_confirm_btn')),
        cancel: @json(__('messages.cancel')),
        closedToastTitle: @json(__('messages.ocr_closed_toast_title')),
        closedToastDesc: @json(__('messages.ocr_closed_toast_desc')),
        scanFailed: @json(__('messages.ocr_scan_failed')),
        scanFailedDefault: @json(__('messages.ocr_scan_failed_default')),
        itemSelectedTemplate: @json(__('messages.ocr_item_selected')),
        itemSavedTemplate: @json(__('messages.ocr_item_saved')),
        itemFilledToastTemplate: @json(__('messages.ocr_item_filled_toast')),
        itemsFound: @json(__('messages.ocr_items_found')),
        itemFoundSingular: @json(__('messages.ocr_item_found_singular')),
        date: @json(App::getLocale() === 'en' ? 'Date:' : 'Tanggal:'),
        recipient: @json(App::getLocale() === 'en' ? 'Recipient:' : 'Penerima:'),
        vendor: @json('Vendor:'),
        docReady: @json(App::getLocale() === 'en' ? 'Document Ready to Process' : 'Dokumen Siap Diproses'),
    };

    // OCR Auto-Scan Document Handler (Event Delegation)
    let currentOcrData = null;

    $(document).on('click', '#ocrDropzone', function(e) {
        document.getElementById('ocrFileInput').click();
    });

    $(document).on('dragover dragenter', '#ocrDropzone', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).css('background', 'rgba(99, 102, 241, 0.08)');
    });

    $(document).on('dragleave drop', '#ocrDropzone', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).css('background', 'rgba(15, 23, 42, 0.25)');
    });

    $(document).on('drop', '#ocrDropzone', function(e) {
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            processOcrScanFile(files[0]);
        }
    });

    $(document).on('change', '#ocrFileInput', function() {
        if (this.files && this.files.length > 0) {
            processOcrScanFile(this.files[0]);
            this.value = '';
        }
    });

    function processOcrScanFile(file) {
        const formData = new FormData();
        formData.append('document', file);
        formData.append('_token', '{{ csrf_token() }}');

        const isPanelOpen = $('#ocrMultiItemPanel').is(':visible');
        if (isPanelOpen) {
            $('#ocrPanelLoadingOverlay').css('display', 'flex').hide().fadeIn(200);
            $('#btnRescanOcr').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1.5"></i> ' + escapeHtml(i18n.scanning));
        } else {
            $('#ocrInitialState').hide();
            $('#ocrLoadingState').show();
            $('#ocrMultiItemPanel').slideUp();
        }

        $.ajax({
            url: "{{ route('assets.scan-ocr') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#ocrLoadingState').hide();
                $('#ocrInitialState').show();
                $('#ocrPanelLoadingOverlay').fadeOut(200);
                $('#btnRescanOcr').prop('disabled', false).html('<i class="fas fa-file-upload mr-1.5"></i> ' + escapeHtml(i18n.changeRescan));

                if (response.success && response.data) {
                    currentOcrData = response.data;
                    renderOcrMultiItemPanel(currentOcrData);
                }
            },
            error: function(xhr) {
                $('#ocrLoadingState').hide();
                $('#ocrInitialState').show();
                $('#ocrPanelLoadingOverlay').fadeOut(200);
                $('#btnRescanOcr').prop('disabled', false).html('<i class="fas fa-file-upload mr-1.5"></i> ' + escapeHtml(i18n.changeRescan));
                const msg = xhr.responseJSON ? xhr.responseJSON.message : i18n.scanFailedDefault;
                if (window.showHallmarkToast) {
                    window.showHallmarkToast('error', i18n.scanFailed, msg);
                } else {
                    Swal.fire({ icon: 'error', text: msg, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, background: '#0f172a', color: '#f8fafc' });
                }
            }
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    let currentSelectedItemIndex = 0;
    let savedItemIndices = new Set();

    function cleanOcrItemName(name) {
        if (!name) return '';
        let str = String(name);
        str = str.replace(/^(?:Pieces|Pcs|Unit|Box|Set|Kg)\s*\d*\s*/gi, '');
        str = str.replace(/(?:\bPieces\b|\bPcs\b|\bUnit\b|\bBox\b|\bSet\b|\bKg\b)/gi, '');
        str = str.replace(/[*%#@^]+$/g, '');
        return str.trim();
    }

    function selectOcrItem(idx) {
        currentSelectedItemIndex = idx;

        // Sync Card View
        $('.ocr-item-card').removeClass('active-ocr-card');
        $('.ocr-item-card').each(function() {
            const i = parseInt($(this).data('item-index'), 10);
            if (!savedItemIndices.has(i)) {
                $(this).find('.btn-select-item').html('<i class="far fa-circle mr-1"></i> ' + escapeHtml(i18n.selectItem)).removeClass('text-success').addClass('text-primary');
            }
        });
        const $activeCard = $(`.ocr-item-card[data-item-index="${idx}"]`);
        $activeCard.addClass('active-ocr-card');
        if (!savedItemIndices.has(idx)) {
            $activeCard.find('.btn-select-item').html('<i class="fas fa-check-circle mr-1"></i> ' + escapeHtml(i18n.selected)).removeClass('text-primary').addClass('text-success');
        }

        // Update Active Tracker Banner
        const items = (currentOcrData && currentOcrData.items) ? currentOcrData.items : [];
        const item = items[idx] || {};
        const cleanName = cleanOcrItemName(item.name || currentOcrData?.name || 'Item #' + (idx + 1));
        $('#ocrActiveItemName').text(cleanName);
        if (savedItemIndices.has(idx)) {
            $('#ocrActiveItemBadge').html(`<i class="fas fa-check-double mr-1"></i> ${i18n.itemSavedTemplate.replace(':num', idx + 1)}`).removeClass('badge-indigo').addClass('badge-info');
        } else {
            $('#ocrActiveItemBadge').html(i18n.itemSelectedTemplate.replace(':num', idx + 1)).removeClass('badge-info').addClass('badge-indigo');
        }

        populateFormWithItem(idx);
    }

    function markItemAsSaved(idx) {
        savedItemIndices.add(idx);

        // Update card
        const $card = $(`.ocr-item-card[data-item-index="${idx}"]`);
        $card.addClass('saved-ocr-card').removeClass('active-ocr-card');
        $card.find('.btn-select-item').html('<i class="fas fa-check-double mr-1"></i> ' + escapeHtml(i18n.saved)).removeClass('text-success text-primary').addClass('text-info font-weight-bold');

        // Update active banner
        $('#ocrActiveItemBadge').html(`<i class="fas fa-check-circle mr-1"></i> ${i18n.itemSavedTemplate.replace(':num', idx + 1)}`).removeClass('badge-indigo').addClass('badge-success');
    }

    function renderOcrMultiItemPanel(data) {
        savedItemIndices.clear();
        const items = data.items || [];
        
        // Document Numbers
        $('#ocrMetaDo').text(data.delivery_order_number ? (i18n.deliveryOrder + ': ' + data.delivery_order_number) : i18n.deliveryOrder);
        if (data.po_number) {
            $('#ocrMetaPo').text('PO: ' + data.po_number);
            $('#ocrMetaPoWrap').show();
        } else {
            $('#ocrMetaPoWrap').hide();
        }
        
        // Metadata Chips
        let chipsHtml = '';
        if (data.date_received) {
            chipsHtml += `<span class="ocr-meta-chip"><i class="far fa-calendar-alt text-accent"></i> <span>${escapeHtml(i18n.date)} <strong>${escapeHtml(data.date_received)}</strong></span></span>`;
        }
        const cleanRecipient = (data.recipient || '').replace(/(?:Tanggal|Date)[.:\s].*/gi, '').trim();
        if (cleanRecipient) {
            chipsHtml += `<span class="ocr-meta-chip"><i class="fas fa-user-check text-info"></i> <span>${escapeHtml(i18n.recipient)} <strong>${escapeHtml(cleanRecipient)}</strong></span></span>`;
        }
        if (data.vendor) {
            chipsHtml += `<span class="ocr-meta-chip"><i class="fas fa-store text-success"></i> <span>${escapeHtml(i18n.vendor)} <strong>${escapeHtml(data.vendor)}</strong></span></span>`;
        }
        $('#ocrMetaSummary').html(chipsHtml || `<span class="ocr-meta-chip"><i class="fas fa-check-circle text-success"></i> ${escapeHtml(i18n.docReady)}</span>`);
        
        $('#ocrItemCount').text(items.length);
        $('#ocrItemCountLabel').text(items.length === 1 ? i18n.itemFoundSingular : i18n.itemsFound);

        const $grid = $('#ocrItemsGrid');
        $grid.empty();

        if (items.length === 0) {
            $grid.append(`<div class="text-muted py-4 px-2 col-12 text-center">${escapeHtml(i18n.noItems)}</div>`);
            populateFormWithItem(0);
        } else {
            items.forEach(function(item, idx) {
                const cleanName = cleanOcrItemName(item.name || data.name || '');
                const codeText = item.code ? escapeHtml(item.code) : '';
                const codeBadgeCard = item.code ? `<span class="badge badge-secondary font-mono text-truncate" style="font-size:0.68rem; max-width: 120px; padding: 2px 6px; border-radius: 6px;" title="${codeText}"><i class="fas fa-barcode mr-1 opacity-70"></i>${codeText}</span>` : '';
                
                // Format UOM cleanly (normalize Pieces -> Pcs or localized)
                let rawUom = (item.uom || 'Pcs').trim();
                if (/^pieces?$/i.test(rawUom)) {
                    rawUom = 'Pcs';
                }
                const qtyVal = item.quantity || 1;
                const qtyBadgeCard = `<span class="badge badge-info text-nowrap" style="font-size:0.69rem; font-weight: 600; padding: 2.5px 7px; border-radius: 6px;"><i class="fas fa-boxes mr-1 opacity-70"></i>Qty: ${qtyVal} ${escapeHtml(rawUom)}</span>`;
                
                const hasBrand = item.brand && item.brand !== 'Generic' && item.brand.toLowerCase() !== 'non-merk';
                const brandBadgeCard = hasBrand 
                    ? `<span class="badge badge-cyan text-truncate" style="font-size:0.69rem; font-weight: 600; max-width: 130px; padding: 2.5px 7px; border-radius: 6px;" title="${escapeHtml(item.brand)}"><i class="fas fa-tag mr-1 opacity-70"></i>${escapeHtml(item.brand)}</span>` 
                    : `<span class="text-muted text-truncate" style="font-size:0.72rem; display: inline-flex; align-items: center; gap: 4px;"><i class="fas fa-box-open opacity-70"></i> ${escapeHtml(i18n.noBrand)}</span>`;

                const isFirst = idx === 0;
                const selectBtnHtml = isFirst
                    ? `<span class="font-weight-bold btn-select-item text-success" style="font-size: 0.76rem; display: inline-flex; align-items: center; gap: 4px;"><i class="fas fa-check-circle"></i> ${escapeHtml(i18n.selected)}</span>`
                    : `<span class="font-weight-bold btn-select-item text-primary" style="font-size: 0.76rem; display: inline-flex; align-items: center; gap: 4px;"><i class="far fa-circle"></i> ${escapeHtml(i18n.selectItem)}</span>`;

                // Card View with flex-wrap header and Hallmark styling
                const cardHtml = `
                    <div class="ocr-item-card ${isFirst ? 'active-ocr-card' : ''}" data-item-index="${idx}">
                        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap" style="gap: 6px 8px;">
                            <div class="d-flex align-items-center flex-wrap" style="gap: 5px; min-width: 0;">
                                <span class="badge badge-primary px-2 py-0.5" style="font-size: 0.68rem; font-weight: 700; border-radius: 6px; letter-spacing: 0.2px;">Item #${idx + 1}</span>
                                ${codeBadgeCard}
                            </div>
                            <div style="flex-shrink: 0;">
                                ${qtyBadgeCard}
                            </div>
                        </div>
                        <div class="ocr-item-title theme-text" title="${escapeHtml(cleanName)}">${escapeHtml(cleanName)}</div>
                        <div class="d-flex align-items-center justify-content-between pt-2.5 mt-auto" style="border-top: 1px solid rgba(148, 163, 184, 0.12); gap: 8px;">
                            <div class="d-flex align-items-center" style="min-width: 0; flex: 1;">
                                ${brandBadgeCard}
                            </div>
                            <div style="flex-shrink: 0;">
                                ${selectBtnHtml}
                            </div>
                        </div>
                    </div>
                `;
                $grid.append(cardHtml);
            });

            selectOcrItem(0);
        }

        $('#ocrMultiItemPanel').slideDown(300);
    }
    window.renderOcrMultiItemPanel = renderOcrMultiItemPanel;
    window.selectOcrItem = selectOcrItem;

    // Interactive Selection on Click
    $(document).on('click', '.ocr-item-card', function(e) {
        const idx = parseInt($(this).data('item-index'), 10);
        selectOcrItem(idx);
    });

    $(document).on('click', '#btnRescanOcr', function() {
        const fileInput = document.getElementById('ocrFileInput');
        if (fileInput) {
            fileInput.value = '';
            fileInput.click();
        }
    });

    $(document).on('click', '#btnCloseOcrPanel', function() {
        Swal.fire({
            title: i18n.closeConfirmTitle,
            text: i18n.closeConfirmText,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: i18n.closeConfirmBtn,
            cancelButtonText: i18n.cancel,
            background: '#0f172a',
            color: '#f8fafc'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#ocrMultiItemPanel').slideUp(300);
                currentOcrData = null;
                savedItemIndices.clear();

                // Reset form fields
                $('input[name="name"]').val('').trigger('input');
                $('input[name="uom"]').val('').trigger('input');
                $('input[name="quantity"]').val(1).trigger('input');
                $('input[name="delivery_order_number"]').val('').trigger('input');
                $('input[name="date_received"]').val('').trigger('input');
                $('textarea[name="notes"]').val('').trigger('input');
                $('input[name="serial_number"]').val('').trigger('input');
                $('select[name="brand_id"]').val('').trigger('change');
                $('select[name="category_id"]').val('').trigger('change');
                $('#ocrFileInput').val('');

                if (window.showHallmarkToast) {
                    window.showHallmarkToast('info', i18n.closedToastTitle, i18n.closedToastDesc);
                }
            }
        });
    });

    // Support Drag and drop directly onto ocrMultiItemPanel
    $(document).on('dragover dragenter', '#ocrMultiItemPanel', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('drag-over');
    });

    $(document).on('dragleave drop', '#ocrMultiItemPanel', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('drag-over');
    });

    $(document).on('drop', '#ocrMultiItemPanel', function(e) {
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            processOcrScanFile(files[0]);
        }
    });

    function populateFormWithItem(index) {
        if (!currentOcrData) return;
        const d = currentOcrData;
        const items = d.items || [];
        const item = items[index] || {};

        const rawName = item.name || d.name || '';
        const cleanItemName = cleanOcrItemName(rawName);

        if (cleanItemName) $('input[name="name"]').val(cleanItemName).trigger('input');
        
        let uomVal = item.uom ? item.uom.trim() : '';
        if (/^pieces?$/i.test(uomVal)) {
            uomVal = 'Pcs';
        }
        if (uomVal) $('input[name="uom"]').val(uomVal).trigger('input');
        if (item.quantity) $('input[name="quantity"]').val(item.quantity).trigger('input');
        if (d.delivery_order_number) $('input[name="delivery_order_number"]').val(d.delivery_order_number).trigger('input');
        if (d.date_received) $('input[name="date_received"]').val(d.date_received).trigger('input');

        const notesUom = uomVal || 'Pcs';
        const notesText = (d.notes || '') + (item.quantity ? ` [Item #${index+1}: ${cleanItemName} - Qty: ${item.quantity} ${notesUom}]` : '');
        $('textarea[name="notes"]').val(notesText).trigger('input');

        const brandId = item.brand_id || d.brand_id;
        if (brandId) {
            $('select[name="brand_id"]').val(brandId).trigger('change');
        }

        if (item.serial_number) {
            $('input[name="serial_number"]').val(item.serial_number).trigger('input');
        }

        if (item.cpu) $('input[name="cpu"]').val(item.cpu);
        if (item.ram) $('input[name="ram"]').val(item.ram);
        if (item.ssd) $('input[name="ssd"]').val(item.ssd);
        if (item.hdd) $('input[name="hdd"]').val(item.hdd);
        if (item.os) $('input[name="os"]').val(item.os);

        if (window.showHallmarkToast) {
            window.showHallmarkToast('success', i18n.itemFilledToastTemplate.replace(':num', index + 1), cleanItemName);
        } else {
            Swal.fire({
                icon: 'success',
                title: i18n.itemFilledToastTemplate.replace(':num', index + 1),
                text: cleanItemName,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2200,
                background: '#0f172a',
                color: '#f8fafc'
            });
        }
    }

    $('form').on('submit', function(e) {
        if ($('#ocrMultiItemPanel').is(':visible')) {
            e.preventDefault();
            
            var $form = $(this);
            var formData = new FormData(this);
            var $btn = $form.find('button[type="submit"], .btn-save');
            var originalText = $btn.html();
            $btn.html('<i class="fas fa-spinner fa-spin mr-2"></i> ' + escapeHtml(i18n.saving)).prop('disabled', true);

            $.ajax({
                url: $form.attr('action') || '{{ route("assets.store") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'Accept': 'application/json'
                },
                success: function(response) {
                    $btn.html(originalText).prop('disabled', false);
                    
                    if (window.showHallmarkToast) {
                        window.showHallmarkToast('success', i18n.saved + '!', response.message || i18n.savedSuccess);
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: i18n.saved + '!',
                            text: response.message || i18n.savedSuccess,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            background: '#0f172a',
                            color: '#f8fafc'
                        });
                    }

                    if (typeof currentSelectedItemIndex === 'number') {
                        markItemAsSaved(currentSelectedItemIndex);
                    }
                    
                    $form.find('input[name="name"]').val('');
                    $form.find('input[name="uom"]').val('');
                    $form.find('input[name="serial_number"]').val('');
                    $('input[name="serial_number"]').val('');
                    $('input[name="quantity"]').val(1);
                    $('#create-asset-img-preview').hide().attr('src', '#');
                    $('#create-asset-img-icon').show();
                    $('input[type="file"][name="image"]').val('');
                    
                    $('select[name="category_id"]').data('user-changed', true).trigger('change');
                },
                error: function(xhr) {
                    $btn.html(originalText).prop('disabled', false);
                    var msg = i18n.saveFailedMsg;
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).map(e => e[0]).join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    
                    if (window.showHallmarkToast) {
                        window.showHallmarkToast('error', i18n.saveFailed, msg);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            html: msg,
                            background: '#0f172a',
                            color: '#f8fafc'
                        });
                    }
                }
            });
        }
    });
});

function previewSelectedImage(input, previewId, iconId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#' + previewId).attr('src', e.target.result).show();
            $('#' + iconId).hide();
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection

