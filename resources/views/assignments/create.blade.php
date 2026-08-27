@extends('layouts.admin')

@section('title', __('messages.assign_asset'))

@section('content')
<div class="card theme-card assignment-form-card">
    <form action="{{ route('assignments.store') }}" method="POST">
        @csrf
        <div class="card-body assignment-form-body">
            <h5 class="assignment-form-title">{{ __('messages.assignment_details') }}</h5>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label  class="theme-text">{{ __('messages.asset') }} *</label>
                    <select name="asset_id" class="form-control select2 @error('asset_id') is-invalid @enderror" required >
                        <option value="" >{{ __('messages.select_available_asset') }}</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}"  {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                {{ $asset->asset_tag }} - {{ $asset->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('asset_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6 form-group">
                    <label  class="theme-text">{{ __('messages.employee') }} *</label>
                    <select name="employee_id" class="form-control select2 @error('employee_id') is-invalid @enderror" required >
                        <option value="" >{{ __('messages.select_employee') }}</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}"  {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->employee_id }} - {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label  class="theme-text">{{ __('messages.assigned_date') }} *</label>
                    <input type="text" name="assigned_date" class="form-control assignment-form-date @error('assigned_date') is-invalid @enderror" value="{{ old('assigned_date', date('Y-m-d')) }}" placeholder="dd/mm/yyyy" autocomplete="off" required >
                    @error('assigned_date') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6 form-group">
                    <label  class="theme-text">{{ __('messages.notes') }}</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2" >{{ old('notes') }}</textarea>
                    @error('notes') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        <div class="card-footer assignment-form-actions bg-transparent border-0">
            <button type="submit" class="btn btn-primary" >{{ __('messages.save') }}</button>
            <a href="{{ route('assignments.index') }}" class="btn btn-outline-secondary" >{{ __('messages.cancel') }}</a>
        </div>
    </form>
</div>
@push('styles')
<style>
.assignment-form-card {
    overflow: hidden;
}

.assignment-form-body {
    padding: 16px 16px 4px;
}

.assignment-form-title {
    margin: 0 0 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid var(--rule-soft);
    color: var(--color-accent-soft);
    font-size: 0.9rem;
    font-weight: 700;
}

.assignment-form-card label {
    margin-bottom: 4px;
    font-size: 0.85rem;
}

.assignment-form-card .form-group {
    margin-bottom: 12px;
}

.assignment-form-card .form-control,
.assignment-form-card .select2-container .select2-selection--single {
    min-height: 32px;
    padding: 4px 10px;
    font-size: 0.85rem;
}

.assignment-form-card textarea.form-control {
    min-height: 56px;
    resize: vertical;
}

.assignment-form-date {
    cursor: pointer;
}

.assignment-form-date:focus {
    border-color: var(--color-accent) !important;
    box-shadow: 0 0 0 3px color-mix(in oklch, var(--color-accent) 15%, transparent) !important;
}

.assignment-form-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    padding: 12px 16px 16px;
}

.assignment-form-actions .btn {
    min-height: 32px;
    padding: 4px 12px;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 0.85rem;
}

@media (max-width: 767.98px) {
    .assignment-form-body {
        padding: 12px 12px 2px;
    }

    .assignment-form-title {
        margin-bottom: 10px;
    }

    .assignment-form-card .form-group {
        margin-bottom: 10px;
    }

    .assignment-form-actions {
        padding: 8px 12px 12px;
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
