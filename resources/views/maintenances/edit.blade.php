@extends('layouts.admin')

@section('title', __('messages.edit_maintenance'))

@section('content')
<style>
    .maint-form-card {
        border-radius: 16px !important;
        overflow: hidden !important;
        border: var(--rule-soft);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    .maint-form-card .card-header {
        border-top-left-radius: 16px !important;
        border-top-right-radius: 16px !important;
    }
    .maint-card-header {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        width: 100% !important;
    }
    .maint-header-title-box {
        display: flex !important;
        align-items: center !important;
        flex-shrink: 0 !important;
        gap: 12px !important;
    }
    .custom-maint-input,
    .maint-form-card .select2-container--default .select2-selection--single {
        height: 44px !important;
        border-radius: 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.22) !important;
        box-shadow: none !important;
        font-size: 0.9rem !important;
        color: var(--color-ink-0) !important;
        transition: all 0.2s ease !important;
    }
    .maint-form-card .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 42px !important;
        padding-left: 12px !important;
        color: var(--color-ink-0) !important;
    }
    .maint-form-card .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
    }
    .custom-maint-input:focus {
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2) !important;
        background: rgba(15, 23, 42, 0.35) !important;
    }
    .custom-maint-textarea {
        border-radius: 12px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.22) !important;
        font-size: 0.9rem !important;
        color: var(--color-ink-0) !important;
        transition: all 0.2s ease !important;
    }
    .form-label-title {
        font-size: 0.88rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        margin-bottom: 8px !important;
    }

    @media (max-width: 767.98px) {
        .maint-card-header {
            padding: 12px 14px !important;
        }
        .maint-header-title-box h5 {
            font-size: 0.98rem !important;
        }
        .maint-header-title-box small {
            font-size: 0.72rem !important;
        }
        .card-footer-actions {
            flex-direction: column-reverse;
            width: 100%;
            gap: 10px !important;
        }
        .card-footer-actions .btn,
        .card-footer-actions a {
            width: 100% !important;
            justify-content: center !important;
            height: 44px !important;
        }
    }
</style>

<div class="card theme-card maint-form-card">
    <form action="{{ route('maintenances.update', array_merge([$maintenance->id], request()->query())) }}" method="POST">
        @csrf @method('PUT')
        <!-- Header Toolbar -->
        <div class="card-header bg-transparent border-bottom maint-card-header py-3 px-3 px-md-4" style="border-color: rgba(148, 163, 184, 0.12) !important;">
            <div class="maint-header-title-box">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.3); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-size: 1.1rem; flex-shrink: 0;">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <h5 class="mb-0 font-weight-bold theme-text" style="font-size: 1.08rem; line-height: 1.2;">{{ __('messages.edit_maintenance') }}</h5>
                    <small class="text-muted d-block" style="font-size: 0.78rem;">Perbarui catatan maintenance perangkat {{ $maintenance->asset->asset_tag ?? '' }}</small>
                </div>
            </div>
        </div>

        <div class="card-body p-3 p-md-4">
            <!-- Row 1: Asset & Status -->
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.asset') }}
                    </label>
                    <input type="text" class="form-control theme-input custom-maint-input text-muted" value="{{ $maintenance->asset->asset_tag ?? '' }} - {{ $maintenance->asset->name ?? '' }}" readonly style="cursor: not-allowed; background: rgba(15, 23, 42, 0.4) !important;">
                </div>

                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.status') }} <span class="text-danger">*</span>
                    </label>
                    <select name="status" class="form-control select2 @error('status') is-invalid @enderror" required>
                        <option value="Ongoing" {{ old('status', $maintenance->status) == 'Ongoing' ? 'selected' : '' }}>{{ __('messages.ongoing') }}</option>
                        <option value="Completed" {{ old('status', $maintenance->status) == 'Completed' ? 'selected' : '' }}>{{ __('messages.completed') }}</option>
                        <option value="Cancelled" {{ old('status', $maintenance->status) == 'Cancelled' ? 'selected' : '' }}>{{ __('messages.cancelled') }}</option>
                    </select>
                    @error('status') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Row 2: Type & Start Date -->
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.maintenance_type') }} <span class="text-danger">*</span>
                    </label>
                    <select name="type" class="form-control select2 @error('type') is-invalid @enderror" required>
                        <option value="Routine" {{ old('type', $maintenance->type) == 'Routine' ? 'selected' : '' }}>{{ __('messages.routine_maintenance') }}</option>
                        <option value="Repair" {{ old('type', $maintenance->type) == 'Repair' ? 'selected' : '' }}>{{ __('messages.repair_broken') }}</option>
                        <option value="Upgrade" {{ old('type', $maintenance->type) == 'Upgrade' ? 'selected' : '' }}>{{ __('messages.hardware_upgrade') }}</option>
                    </select>
                    @error('type') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.start_date') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="start_date" class="form-control theme-input custom-maint-input maintenance-form-date @error('start_date') is-invalid @enderror" value="{{ old('start_date', $maintenance->start_date) }}" placeholder="dd/mm/yyyy" autocomplete="off" required>
                    @error('start_date') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Row 3: Estimated Cost & End Date -->
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.estimated_cost') }}
                    </label>
                    <input type="number" name="cost" class="form-control theme-input custom-maint-input @error('cost') is-invalid @enderror" value="{{ old('cost', $maintenance->cost) }}" step="0.01" placeholder="0.00" autocomplete="off">
                    @error('cost') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.end_date') }}
                    </label>
                    <input type="text" name="end_date" class="form-control theme-input custom-maint-input maintenance-form-date @error('end_date') is-invalid @enderror" value="{{ old('end_date', $maintenance->end_date) }}" placeholder="dd/mm/yyyy" autocomplete="off">
                    @error('end_date') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Row 4: Description / Issue -->
            <div class="row">
                <div class="col-md-12 form-group mb-2">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.description_issue') }} <span class="text-danger">*</span>
                    </label>
                    <textarea name="description" class="form-control theme-input custom-maint-textarea @error('description') is-invalid @enderror" rows="4" placeholder="Tuliskan rincian kendala atau solusi..." required>{{ old('description', $maintenance->description) }}</textarea>
                    @error('description') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="card-footer bg-transparent py-3 px-3 px-md-4 border-top d-flex align-items-center justify-content-end card-footer-actions" style="gap: 10px; border-color: rgba(148, 163, 184, 0.12) !important;">
            <a href="{{ route('maintenances.index', request()->query()) }}" class="btn btn-outline-secondary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">
                {{ __('messages.cancel') }}
            </a>
            <button type="submit" class="btn btn-primary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">
                {{ __('messages.update') }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({ width: '100%' });

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
