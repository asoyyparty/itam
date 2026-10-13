@extends('layouts.admin')

@section('title', __('messages.edit') . ' ' . __('messages.asset'))

@section('content')

<!-- Hallmark Master Header Banner -->
<div class="master-header-banner">
    <div class="d-flex align-items-center justify-content-between flex-wrap flex-md-nowrap" style="gap: 16px;">
        <div class="d-flex align-items-center" style="gap: 14px; min-width: 0; flex: 1;">
            <div style="width: 44px; height: 44px; border-radius: 14px; background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(217, 119, 6, 0.08)); border: 1px solid rgba(245, 158, 11, 0.35); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-size: 1.2rem; flex-shrink: 0; box-shadow: 0 0 14px rgba(245, 158, 11, 0.25);">
                <i class="fas fa-edit"></i>
            </div>
            <div style="flex: 1; min-width: 0;">
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <h5 class="mb-0 font-weight-bold theme-text text-truncate" style="font-size: 1.15rem; line-height: 1.2;">{{ __('messages.edit') }} {{ __('messages.asset') }}</h5>
                    @php
                        $statusClasses = [
                            'Available'   => 'badge-status-available',
                            'Assigned'    => 'badge-status-assigned',
                            'Maintenance' => 'badge-status-maintenance',
                            'Retired'     => 'badge-status-retired',
                            'Missing'     => 'badge-status-missing',
                        ];
                        $currentClass = $statusClasses[$asset->status] ?? 'badge-status-available';
                    @endphp
                    <span class="hallmark-status-badge {{ $currentClass }}" style="font-size: 0.72rem; padding: 2px 8px;">
                        <span style="width: 5px; height: 5px; border-radius: 50%; background: currentColor; display: inline-block;"></span>
                        {{ __('messages.' . strtolower($asset->status)) ?? $asset->status }}
                    </span>
                </div>
                <small class="text-muted d-block" style="font-size: 0.8rem; font-family: var(--font-mono); margin-top: 2px; white-space: normal; line-height: 1.3;">
                    TAG: {{ $asset->asset_tag }} &bull; {{ $asset->name }} @if($asset->serial_number) (SN: {{ $asset->serial_number }}) @endif
                </small>
            </div>
        </div>
    </div>
</div>

<div class="card theme-card master-form-card asset-form-card">
    <form action="{{ route('assets.update', array_merge(['asset' => $asset->id], request()->query())) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

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

        {{-- ===== BASIC INFORMATION ===== --}}
        <div class="form-section">
            <div class="section-title section-cyan">
                <span class="section-badge badge-cyan"><i class="fas fa-info-circle"></i></span>
                <div>
                    <div class="section-name">{{ __('messages.basic_info') }}</div>
                    <div class="section-sub">{{ __('messages.asset_tag') }}, {{ __('messages.asset_name') }}, Serial</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 form-group mb-3">
                    <label class="field-label">{{ __('messages.asset_tag') }} <span class="text-danger">*</span></label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="fas fa-qrcode"></i></span>
                        <input type="text" name="asset_tag"
                            class="field-input font-mono @error('asset_tag') is-invalid @enderror"
                            value="{{ old('asset_tag', $asset->asset_tag) }}" required>
                    </div>
                    @error('asset_tag')<div class="field-error text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label class="field-label">{{ __('messages.asset_name') }} <span class="text-danger">*</span></label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="fas fa-laptop"></i></span>
                        <input type="text" name="name"
                            class="field-input @error('name') is-invalid @enderror"
                            value="{{ old('name', $asset->name) }}" required>
                    </div>
                    @error('name')<div class="field-error text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-2 form-group mb-3">
                    <label class="field-label">{{ __('messages.uom') }}</label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="fas fa-box"></i></span>
                        <input type="text" name="uom"
                            class="field-input font-mono @error('uom') is-invalid @enderror"
                            value="{{ old('uom', $asset->uom) }}"
                            placeholder="e.g., Pieces, Unit">
                    </div>
                    @error('uom')<div class="field-error text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3 form-group mb-3">
                    <label class="field-label">{{ __('messages.serial_number') }}</label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="fas fa-barcode"></i></span>
                        <input type="text" name="serial_number"
                            class="field-input font-mono @error('serial_number') is-invalid @enderror"
                            value="{{ old('serial_number', $asset->serial_number) }}">
                    </div>
                    @error('serial_number')<div class="field-error text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12 form-group mt-2 mb-2">
                    <label class="field-label" style="font-size: 0.82rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: var(--color-ink-1); margin-bottom: 8px;">
                        {{ __('messages.asset_image') }} <small class="text-muted font-weight-normal">({{ __('messages.optional') }})</small>
                    </label>
                    <div class="custom-file-upload-card rounded-lg" style="padding: 16px 20px !important; background: rgba(15, 23, 42, 0.35); border: 1px dashed rgba(148, 163, 184, 0.25); border-radius: 12px; transition: all 0.2s ease;">
                        <div class="d-flex align-items-center flex-nowrap" style="gap: 16px;">
                            <div class="flex-shrink-0 position-relative d-flex align-items-center justify-content-center rounded-lg" style="width: 48px; height: 48px; min-width: 48px; min-height: 48px; flex-shrink: 0; aspect-ratio: 1 / 1; background: rgba(99, 102, 241, 0.08); border: 1px dashed rgba(99, 102, 241, 0.3); color: #818cf8; font-size: 1.15rem; overflow: hidden;">
                                @if($asset->image)
                                    <img id="edit-asset-img-preview" src="{{ asset('storage/' . $asset->image) }}" alt="{{ $asset->name }}" class="rounded-lg shadow-sm" style="width: 52px; height: 52px; object-fit: cover; border: 2px solid rgba(99, 102, 241, 0.3);">
                                    <i id="edit-asset-img-icon" class="fas fa-image" style="display: none;"></i>
                                @else
                                    <img id="edit-asset-img-preview" src="#" alt="Preview" class="rounded-lg shadow-sm" style="width: 52px; height: 52px; object-fit: cover; display: none;">
                                    <i id="edit-asset-img-icon" class="fas fa-image"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1" style="min-width: 0;">
                                <div class="d-flex align-items-center flex-nowrap" style="gap: 8px;">
                                    <!-- Camera Direct Input -->
                                    <div class="position-relative">
                                        <input type="file" name="image" id="edit-asset-camera-input" class="custom-file-input-field @error('image') is-invalid @enderror" accept="image/*" capture="environment" style="position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer; z-index: 2;" onchange="previewSelectedImage(this, 'edit-asset-img-preview', 'edit-asset-img-icon')">
                                        <label for="edit-asset-camera-input" class="btn btn-sm btn-indigo mb-0 px-3 py-1.5 text-nowrap" style="background: linear-gradient(135deg, #6366f1, #4f46e5); color: #ffffff; border-radius: 8px; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 3px 10px rgba(99, 102, 241, 0.35);">
                                            <i class="fas fa-camera"></i> {{ __('messages.camera') }}
                                        </label>
                                    </div>
                                    <!-- File / Gallery Input -->
                                    <div class="position-relative">
                                        <input type="file" name="image" id="edit-asset-file-input" class="custom-file-input-field @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp" style="position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer; z-index: 2;" onchange="previewSelectedImage(this, 'edit-asset-img-preview', 'edit-asset-img-icon')">
                                        <label for="edit-asset-file-input" class="btn btn-sm btn-outline-secondary mb-0 px-3 py-1.5 text-nowrap" style="border: 1px solid rgba(148, 163, 184, 0.3); background: rgba(148, 163, 184, 0.1); color: var(--color-ink-1); border-radius: 8px; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
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
                            <option value="{{ $cat->id }}" {{ old('category_id', $asset->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label class="field-label">{{ __('messages.brand') }}</label>
                    <select id="select-brand" name="brand_id"
                        class="field-select select2-field custom-filter-select @error('brand_id') is-invalid @enderror">
                        <option value="">{{ __('messages.select') }}</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id', $asset->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 form-group mb-3">
                    <label class="field-label">{{ __('messages.location') }}</label>
                    <select id="select-location" name="location_id"
                        class="field-select select2-field custom-filter-select @error('location_id') is-invalid @enderror">
                        <option value="">{{ __('messages.select') }}</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id', $asset->location_id) == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
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
                    <input type="date" name="date_received"
                        class="field-input font-mono @error('date_received') is-invalid @enderror"
                        value="{{ old('date_received', $asset->date_received ? \Carbon\Carbon::parse($asset->date_received)->format('Y-m-d') : '') }}">
                </div>

                <div class="col-md-3 form-group mb-3">
                    <label class="field-label">{{ __('messages.delivery_order') }}</label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="fas fa-file-invoice"></i></span>
                        <input type="text" name="delivery_order_number"
                            class="field-input font-mono @error('delivery_order_number') is-invalid @enderror"
                            value="{{ old('delivery_order_number', $asset->delivery_order_number) }}">
                    </div>
                </div>

                <div class="col-md-3 form-group mb-3">
                    <label class="field-label">{{ __('messages.warranty_months') }} <small class="text-muted">({{ __('messages.months') }})</small></label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="fas fa-shield-alt"></i></span>
                        <input type="number" name="warranty_months"
                            class="field-input font-mono @error('warranty_months') is-invalid @enderror"
                            value="{{ old('warranty_months', $asset->warranty_months) }}" min="0"
                            placeholder="e.g., 12, 24, 36">
                    </div>
                </div>

                <div class="col-md-3 form-group mb-3">
                    <label class="field-label">{{ __('messages.status') }} <span class="text-danger">*</span></label>
                    <select name="status" class="field-select select2-field custom-filter-select @error('status') is-invalid @enderror" required>
                        <option value="Available" {{ old('status', $asset->status) == 'Available' ? 'selected' : '' }}>{{ __('messages.available') }}</option>
                        <option value="Assigned" {{ old('status', $asset->status) == 'Assigned' ? 'selected' : '' }}>{{ __('messages.assigned') }}</option>
                        <option value="Maintenance" {{ old('status', $asset->status) == 'Maintenance' ? 'selected' : '' }}>{{ __('messages.maintenance') }}</option>
                        <option value="Retired" {{ old('status', $asset->status) == 'Retired' ? 'selected' : '' }}>{{ __('messages.retired') }}</option>
                        <option value="Missing" {{ old('status', $asset->status) == 'Missing' ? 'selected' : '' }}>{{ __('messages.missing') }}</option>
                    </select>
                </div>

                <div class="col-md-12 form-group mb-2">
                    <label class="field-label">{{ __('messages.notes') }}</label>
                    <textarea name="notes" class="field-input @error('notes') is-invalid @enderror"
                        rows="2">{{ old('notes', $asset->notes) }}</textarea>
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
                            <input type="text" name="cpu" class="field-input font-mono" value="{{ old('cpu', $asset->computer->cpu ?? '') }}" placeholder="e.g., Core i5-10400 / AMD Ryzen 5">
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="field-label">{{ __('messages.ram_size') }} (GB)</label>
                            <input type="number" name="ram" class="field-input font-mono" value="{{ old('ram', $asset->computer->ram ?? '') }}" placeholder="e.g. 8, 16, 32">
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="field-label">{{ __('messages.gpu_spec') }}</label>
                            <input type="text" name="gpu" class="field-input font-mono" value="{{ old('gpu', $asset->computer->gpu ?? '') }}" placeholder="e.g., Nvidia GTX 1650 / Intel UHD">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label class="field-label">{{ __('messages.ssd_size') }} (GB)</label>
                            <input type="number" name="ssd" class="field-input font-mono" value="{{ old('ssd', $asset->computer->ssd ?? '') }}" placeholder="e.g. 256, 512">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label class="field-label">{{ __('messages.hdd_size') }} (GB)</label>
                            <input type="number" name="hdd" class="field-input font-mono" value="{{ old('hdd', $asset->computer->hdd ?? '') }}" placeholder="e.g. 1000">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label class="field-label">{{ __('messages.os') }}</label>
                            <input type="text" name="os" class="field-input font-mono" value="{{ old('os', $asset->computer->os ?? '') }}" placeholder="e.g., Windows 11 Pro">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label class="field-label">{{ __('messages.office') }}</label>
                            <input type="text" name="office" class="field-input font-mono" value="{{ old('office', $asset->computer->office ?? '') }}" placeholder="e.g., Office Home & Business 2021">
                        </div>
                    </div>
                </div>

                {{-- Printer --}}
                <div class="spec-group" id="spec-printer" style="display:none;">
                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label class="field-label">{{ __('messages.printer_type') }}</label>
                            <select name="printer_type" class="field-select custom-filter-select">
                                <option value="Laser" {{ old('printer_type', $asset->printer->printer_type ?? '') == 'Laser' ? 'selected' : '' }}>{{ __('messages.printer_type_laser') }}</option>
                                <option value="Inkjet" {{ old('printer_type', $asset->printer->printer_type ?? '') == 'Inkjet' ? 'selected' : '' }}>{{ __('messages.printer_type_inkjet') }}</option>
                                <option value="Thermal" {{ old('printer_type', $asset->printer->printer_type ?? '') == 'Thermal' ? 'selected' : '' }}>{{ __('messages.printer_type_thermal') }}</option>
                                <option value="DotMatrix" {{ old('printer_type', $asset->printer->printer_type ?? '') == 'DotMatrix' ? 'selected' : '' }}>{{ __('messages.printer_type_dotmatrix') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="field-label">{{ __('messages.connection') }}</label>
                            <select name="connection_type" class="field-select custom-filter-select">
                                <option value="USB" {{ old('connection_type', $asset->printer->connection_type ?? '') == 'USB' ? 'selected' : '' }}>{{ __('messages.printer_conn_usb') }}</option>
                                <option value="Network" {{ old('connection_type', $asset->printer->connection_type ?? '') == 'Network' ? 'selected' : '' }}>{{ __('messages.printer_conn_network') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group mb-3 d-flex align-items-end pb-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="has_scanner" value="1" class="custom-control-input" id="has_scanner" {{ old('has_scanner', $asset->printer->has_scanner ?? 0) ? 'checked' : '' }}>
                                <label class="custom-control-label theme-text" for="has_scanner">{{ __('messages.has_scanner') }}</label>
                            </div>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="field-label">{{ __('messages.initial_counter') }}</label>
                            <input type="number" name="counter_print" class="field-input font-mono" value="{{ old('counter_print', $asset->printer->counter_print ?? 0) }}">
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="field-label">{{ __('messages.toner_status') }}</label>
                            <input type="text" name="toner_status" class="field-input font-mono" value="{{ old('toner_status', $asset->printer->toner_status ?? '') }}" placeholder="{{ __('messages.toner_status_placeholder') }}">
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="field-label">{{ __('messages.drum_status') }}</label>
                            <input type="text" name="drum_status" class="field-input font-mono" value="{{ old('drum_status', $asset->printer->drum_status ?? '') }}" placeholder="{{ __('messages.drum_status_placeholder') }}">
                        </div>
                    </div>
                </div>

                {{-- Network --}}
                <div class="spec-group" id="spec-network" style="display:none;">
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="field-label">{{ __('messages.firmware_version') }}</label>
                            <input type="text" name="network_firmware" class="field-input font-mono" value="{{ old('network_firmware', $asset->network->firmware_version ?? '') }}" placeholder="e.g., RouterOS v7.2 / IOS 15.2">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="field-label">{{ __('messages.controller_cloud') }}</label>
                            <input type="text" name="controller" class="field-input font-mono" value="{{ old('controller', $asset->network->controller ?? '') }}" placeholder="e.g., UniFi Controller / standalone">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label class="field-label">{{ __('messages.total_ports') }}</label>
                            <input type="number" name="port_count" class="field-input font-mono" value="{{ old('port_count', $asset->network->port_count ?? '') }}" placeholder="e.g., 24 / 48">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label class="field-label">{{ __('messages.active_ports') }}</label>
                            <input type="number" name="active_ports" class="field-input font-mono" value="{{ old('active_ports', $asset->network->active_ports ?? '') }}" placeholder="e.g., 18">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label class="field-label">{{ __('messages.wifi_ssid') }}</label>
                            <input type="text" name="ssid" class="field-input font-mono" value="{{ old('ssid', $asset->network->ssid ?? '') }}" placeholder="SSID Name">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label class="field-label">{{ __('messages.wifi_password') }}</label>
                            <input type="text" name="wifi_password" class="field-input font-mono" value="{{ old('wifi_password', $asset->network->wifi_password ?? '') }}" placeholder="WiFi Password">
                        </div>
                        <div class="col-md-12 form-group mb-3">
                            <label class="field-label">{{ __('messages.backup_config') }}</label>
                            <input type="text" name="backup_config_path" class="field-input font-mono" value="{{ old('backup_config_path', $asset->network->backup_config_path ?? '') }}" placeholder="e.g., \\cba-nas\configs\switch-core.backup">
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
/* â”€â”€â”€ Hallmark Status Badges â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.hallmark-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 0.76rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    white-space: nowrap;
    border: 1px solid transparent;
}
.badge-status-available { background: rgba(16, 185, 129, 0.12); color: #34d399; border-color: rgba(16, 185, 129, 0.25); }
.badge-status-assigned { background: rgba(99, 102, 241, 0.12); color: #818cf8; border-color: rgba(99, 102, 241, 0.25); }
.badge-status-maintenance { background: rgba(245, 158, 11, 0.12); color: #fbbf24; border-color: rgba(245, 158, 11, 0.25); }
.badge-status-retired { background: rgba(148, 163, 184, 0.12); color: #94a3b8; border-color: rgba(148, 163, 184, 0.22); }
.badge-status-missing { background: rgba(239, 68, 68, 0.12); color: #f87171; border-color: rgba(239, 68, 68, 0.25); }

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
    $('.select2-field').select2({
        width: '100%',
        allowClear: true,
        placeholder: '{{ __("messages.select") }}'
    });

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

    var existingSpecData = {!! json_encode($asset->spec_data ?? []) !!};

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
                var val = existingSpecData[spec.name] || '';
                var html = `
                    <div class="col-md-4 form-group mb-3">
                        <label class="field-label">${spec.label}</label>
                        <input type="${inputType}" name="spec_data[${spec.name}]" class="field-input font-mono" value="${val}" placeholder="${spec.label}">
                    </div>
                `;
                $('#dynamic-spec-container').append(html);
            });
        } else if (group) {
            $('#specifications-section').slideDown(200);
            $('#spec-' + group).show();
        }
    });

    $('select[name="category_id"]').trigger('change');
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

