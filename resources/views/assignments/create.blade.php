@extends('layouts.admin')

@section('title', __('messages.assign_asset'))

@section('content')
<style>
    .handover-form-card {
        border-radius: 16px !important;
        overflow: hidden !important;
        border: var(--rule-soft);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    .handover-form-card .card-header {
        border-top-left-radius: 16px !important;
        border-top-right-radius: 16px !important;
    }
    .handover-card-header {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        width: 100% !important;
    }
    .handover-header-title-box {
        display: flex !important;
        align-items: center !important;
        flex-shrink: 0 !important;
        gap: 12px !important;
    }
    .custom-handover-input,
    .handover-form-card .select2-container--default .select2-selection--single {
        height: 44px !important;
        border-radius: 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.22) !important;
        box-shadow: none !important;
        font-size: 0.9rem !important;
        color: var(--color-ink-0) !important;
        transition: all 0.2s ease !important;
    }
    .handover-form-card .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 42px !important;
        padding-left: 12px !important;
        color: var(--color-ink-0) !important;
    }
    .handover-form-card .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
    }
    .custom-handover-input:focus {
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2) !important;
        background: rgba(15, 23, 42, 0.35) !important;
    }
    .custom-handover-textarea {
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
        .handover-card-header {
            padding: 12px 14px !important;
        }
        .handover-header-title-box h5 {
            font-size: 0.98rem !important;
        }
        .handover-header-title-box small {
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

<div class="card theme-card handover-form-card">
    <form action="{{ route('assignments.store') }}" method="POST">
        @csrf
        <!-- Header Toolbar -->
        <div class="card-header bg-transparent border-bottom handover-card-header py-3 px-3 px-md-4" style="border-color: rgba(148, 163, 184, 0.12) !important;">
            <div class="handover-header-title-box">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(59, 130, 246, 0.12); border: 1px solid rgba(59, 130, 246, 0.3); display: flex; align-items: center; justify-content: center; color: #60a5fa; font-size: 1.1rem; flex-shrink: 0;">
                    <i class="fas fa-handshake"></i>
                </div>
                <div>
                    <h5 class="mb-0 font-weight-bold theme-text" style="font-size: 1.08rem; line-height: 1.2;">{{ __('messages.assign_asset') }}</h5>
                    <small class="text-muted d-block" style="font-size: 0.78rem;">Pemberian / Penyerahan aset IT kepada pegawai</small>
                </div>
            </div>
        </div>

        <div class="card-body p-3 p-md-4">
            <!-- Row 1: Asset & Employee -->
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.asset') }} <span class="text-danger">*</span>
                    </label>
                    <select name="asset_id" class="form-control select2 @error('asset_id') is-invalid @enderror" required>
                        <option value="">{{ __('messages.select_available_asset') }}...</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                {{ $asset->asset_tag }} - {{ $asset->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('asset_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.employee') }} <span class="text-danger">*</span>
                    </label>
                    <select name="employee_id" class="form-control select2 @error('employee_id') is-invalid @enderror" required>
                        <option value="">{{ __('messages.select_employee') }}...</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->employee_id }} - {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Row 2: Assigned Date & Notes -->
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.assigned_date') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="assigned_date" class="form-control theme-input custom-handover-input assignment-form-date @error('assigned_date') is-invalid @enderror" value="{{ old('assigned_date', date('Y-m-d')) }}" placeholder="dd/mm/yyyy" autocomplete="off" required>
                    @error('assigned_date') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-2">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.notes') }}
                    </label>
                    <textarea name="notes" class="form-control theme-input custom-handover-textarea @error('notes') is-invalid @enderror" rows="3" placeholder="Tuliskan catatan penyerahan aset jika ada...">{{ old('notes') }}</textarea>
                    @error('notes') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="card-footer bg-transparent py-3 px-3 px-md-4 border-top d-flex align-items-center justify-content-end card-footer-actions" style="gap: 10px; border-color: rgba(148, 163, 184, 0.12) !important;">
            <a href="{{ route('assignments.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">
                {{ __('messages.cancel') }}
            </a>
            <button type="submit" class="btn btn-primary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">
                {{ __('messages.save') }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({ width: '100%' });

        flatpickr('.assignment-form-date', {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            allowInput: true,
        });
    });
</script>
@endpush
@endsection
