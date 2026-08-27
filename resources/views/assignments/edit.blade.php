@extends('layouts.admin')

@section('title', __('messages.edit_assignment'))

@section('content')
<div class="card theme-card assignment-form-card">
    <form action="{{ route('assignments.update', array_merge([$assignment->id], request()->query())) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body assignment-form-body">
            <div class="assignment-form-header">
                <h5 class="assignment-form-title">
                    <i class="fas fa-edit text-info mr-2"></i>{{ __('messages.edit_assignment') }}
                </h5>
            </div>

            <div class="row">
                <!-- Asset (Col 6 on Desktop, 12 on Mobile) -->
                <div class="col-md-6 form-group mb-3">
                    <label class="theme-text form-label">{{ __('messages.asset') }}</label>
                    <div class="position-relative">
                        <i class="fas fa-lock position-absolute text-muted readonly-asset-icon"></i>
                        <input type="text" class="form-control theme-input readonly-asset-input" value="{{ $assignment->asset->asset_tag ?? '' }} - {{ $assignment->asset->name ?? '' }}" readonly>
                    </div>
                    <small class="text-muted d-block mt-1 form-hint">
                        <i class="fas fa-info-circle mr-1"></i>{{ __('messages.asset_cannot_be_changed') }}
                    </small>
                </div>

                <!-- User (Col 6 on Desktop, 12 on Mobile) -->
                <div class="col-md-6 form-group mb-3">
                    <label class="theme-text form-label">{{ __('messages.employee') }} <span class="text-danger">*</span></label>
                    <select name="employee_id" class="form-control select2 @error('employee_id') is-invalid @enderror" required>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id', $assignment->employee_id) == $employee->id ? 'selected' : '' }}>
                                {{ $employee->employee_id }} - {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row">
                <!-- Handover Date (Col 6 on Desktop, 12 on Mobile) -->
                <div class="col-md-6 form-group mb-3">
                    <label class="theme-text form-label">{{ __('messages.assigned_date') }} <span class="text-danger">*</span></label>
                    <input type="text" name="assigned_date" class="form-control assignment-form-date @error('assigned_date') is-invalid @enderror" value="{{ old('assigned_date', $assignment->assigned_date) }}" placeholder="dd/mm/yyyy" autocomplete="off" required>
                    @error('assigned_date') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Notes (Col 6 on Desktop, 12 on Mobile) -->
                <div class="col-md-6 form-group mb-3">
                    <label class="theme-text form-label">{{ __('messages.notes') }}</label>
                    <textarea name="notes" class="form-control theme-input @error('notes') is-invalid @enderror" rows="2" placeholder="...">{{ old('notes', $assignment->notes) }}</textarea>
                    @error('notes') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="card-footer assignment-form-actions bg-transparent border-0">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-save mr-1"></i> {{ __('messages.update') }}
            </button>
            <a href="{{ route('assignments.index', request()->query()) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-times mr-1"></i> {{ __('messages.cancel') }}
            </a>
        </div>
    </form>
</div>

@push('styles')
<style>
.assignment-form-card {
    width: 100%;
    overflow: hidden;
    border-radius: 8px;
}

.assignment-form-body {
    padding: 16px 20px 8px;
}

.assignment-form-header {
    margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--rule-soft, rgba(255, 255, 255, 0.1));
}

.assignment-form-title {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--color-accent-soft, #60a5fa);
    display: flex;
    align-items: center;
}

.assignment-form-card .form-label {
    margin-bottom: 4px;
    font-size: 0.82rem;
    font-weight: 600;
    display: block;
}

.assignment-form-card .form-hint {
    font-size: 0.75rem;
    opacity: 0.8;
}

.assignment-form-card .readonly-asset-input {
    background-color: rgba(0, 0, 0, 0.25) !important;
    color: #cbd5e1 !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    padding-left: 30px !important;
    cursor: not-allowed;
}

.assignment-form-card .readonly-asset-icon {
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0.6;
    font-size: 0.78rem;
    pointer-events: none;
    z-index: 5;
}

.assignment-form-card .form-control,
.assignment-form-card .select2-container .select2-selection--single {
    min-height: 34px;
    height: 34px;
    padding: 4px 10px;
    font-size: 0.84rem;
}

.assignment-form-card textarea.form-control {
    min-height: 56px;
    height: auto;
    resize: vertical;
}

/* Select2 Specific Alignments */
.assignment-form-card .select2-container {
    width: 100% !important;
}

.assignment-form-card .select2-container .select2-selection--single {
    display: flex;
    align-items: center;
}

.assignment-form-card .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: normal;
    padding-left: 0;
}

.assignment-form-card .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 32px;
}

.assignment-form-date {
    cursor: pointer;
}

.assignment-form-date:focus {
    border-color: var(--color-accent, #3b82f6) !important;
    box-shadow: 0 0 0 3px color-mix(in oklch, var(--color-accent, #3b82f6) 15%, transparent) !important;
}

.assignment-form-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 20px 16px;
}

.assignment-form-actions .btn {
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
    .assignment-form-body {
        padding: 14px 14px 6px;
    }

    .assignment-form-actions {
        padding: 8px 14px 14px;
        flex-direction: column;
    }

    .assignment-form-actions .btn {
        width: 100%;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
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
