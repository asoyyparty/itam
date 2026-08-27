@extends('layouts.admin')

@section('title', __('messages.log_maintenance'))

@section('content')
<div class="card theme-card maintenance-form-card">
    <form action="{{ route('maintenances.store') }}" method="POST">
        @csrf
        <div class="card-body maintenance-form-body">
            <div class="maintenance-form-header">
                <h5 class="maintenance-form-title">
                    <i class="fas fa-tools text-info mr-2"></i>{{ __('messages.maintenance_details') }}
                </h5>
            </div>

            <div class="row">
                <!-- Asset (Col 6 on Desktop, 12 on Mobile) -->
                <div class="col-md-6 form-group mb-3">
                    <label class="theme-text form-label">{{ __('messages.asset') }} <span class="text-danger">*</span></label>
                    <select name="asset_id" class="form-control select2 @error('asset_id') is-invalid @enderror" required>
                        <option value="">{{ __('messages.select_asset') }}</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                {{ $asset->asset_tag }} - {{ $asset->name }} ({{ $asset->status }})
                            </option>
                        @endforeach
                    </select>
                    @error('asset_id') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Maintenance Type (Col 6 on Desktop, 12 on Mobile) -->
                <div class="col-md-6 form-group mb-3">
                    <label class="theme-text form-label">{{ __('messages.maintenance_type') }} <span class="text-danger">*</span></label>
                    <select name="type" class="form-control theme-select @error('type') is-invalid @enderror" required>
                        <option value="Routine" {{ old('type') == 'Routine' ? 'selected' : '' }}>{{ __('messages.routine_maintenance') }}</option>
                        <option value="Repair" {{ old('type') == 'Repair' ? 'selected' : '' }}>{{ __('messages.repair_broken') }}</option>
                        <option value="Upgrade" {{ old('type') == 'Upgrade' ? 'selected' : '' }}>{{ __('messages.hardware_upgrade') }}</option>
                    </select>
                    @error('type') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row">
                <!-- Start Date (Col 6 on Desktop, 12 on Mobile) -->
                <div class="col-md-6 form-group mb-3">
                    <label class="theme-text form-label">{{ __('messages.start_date') }} <span class="text-danger">*</span></label>
                    <input type="text" name="start_date" class="form-control maintenance-form-date @error('start_date') is-invalid @enderror" value="{{ old('start_date', date('Y-m-d')) }}" placeholder="dd/mm/yyyy" autocomplete="off" required>
                    @error('start_date') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Estimated Cost (Col 6 on Desktop, 12 on Mobile) -->
                <div class="col-md-6 form-group mb-3">
                    <label class="theme-text form-label">{{ __('messages.estimated_cost') }}</label>
                    <input type="number" name="cost" class="form-control theme-input @error('cost') is-invalid @enderror" value="{{ old('cost') }}" step="0.01" placeholder="0.00">
                    @error('cost') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row">
                <!-- Description / Issue (Col 12) -->
                <div class="col-md-12 form-group mb-2">
                    <label class="theme-text form-label">{{ __('messages.description_issue') }} <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control theme-input @error('description') is-invalid @enderror" rows="3" placeholder="..." required>{{ old('description') }}</textarea>
                    @error('description') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="card-footer maintenance-form-actions bg-transparent border-0">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-save mr-1"></i> {{ __('messages.save') }}
            </button>
            <a href="{{ route('maintenances.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-times mr-1"></i> {{ __('messages.cancel') }}
            </a>
        </div>
    </form>
</div>

@push('styles')
<style>
.maintenance-form-card {
    width: 100%;
    overflow: hidden;
    border-radius: 8px;
}

.maintenance-form-body {
    padding: 16px 20px 8px;
}

.maintenance-form-header {
    margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--rule-soft, rgba(255, 255, 255, 0.1));
}

.maintenance-form-title {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--color-accent-soft, #60a5fa);
    display: flex;
    align-items: center;
}

.maintenance-form-card .form-label {
    margin-bottom: 4px;
    font-size: 0.82rem;
    font-weight: 600;
    display: block;
}

.maintenance-form-card .form-control,
.maintenance-form-card .select2-container .select2-selection--single {
    min-height: 34px;
    height: 34px;
    padding: 4px 10px;
    font-size: 0.84rem;
}

.maintenance-form-card textarea.form-control {
    min-height: 70px;
    height: auto;
    resize: vertical;
}

/* Select2 Specific Alignments */
.maintenance-form-card .select2-container {
    width: 100% !important;
}

.maintenance-form-card .select2-container .select2-selection--single {
    display: flex;
    align-items: center;
}

.maintenance-form-card .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: normal;
    padding-left: 0;
}

.maintenance-form-card .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 32px;
}

.maintenance-form-date {
    cursor: pointer;
}

.maintenance-form-date:focus {
    border-color: var(--color-accent, #3b82f6) !important;
    box-shadow: 0 0 0 3px color-mix(in oklch, var(--color-accent, #3b82f6) 15%, transparent) !important;
}

.maintenance-form-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 20px 16px;
}

.maintenance-form-actions .btn {
    min-height: 34px;
    padding: 4px 14px;
    border-radius: var(--radius-md, 6px);
    font-weight: 600;
    font-size: 0.83rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

@media (max-width: 767.98px) {
    .maintenance-form-body {
        padding: 14px 14px 6px;
    }

    .maintenance-form-actions {
        padding: 8px 14px 14px;
        flex-direction: column;
    }

    .maintenance-form-actions .btn {
        width: 100%;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    flatpickr('.maintenance-form-date', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        allowInput: true,
    });
});
</script>
@endpush
@endsection
