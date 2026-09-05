@extends('layouts.admin')

@section('title', __('messages.edit_assignment'))

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
                    <h5 class="mb-0 font-weight-bold theme-text text-truncate" style="font-size: 1.15rem; line-height: 1.2;">{{ __('messages.edit_assignment') }}</h5>
                    @if($assignment->status == 'Assigned')
                        <span class="hallmark-status-badge badge-status-assigned" style="font-size: 0.72rem; padding: 2px 8px;">
                            <span style="width: 5px; height: 5px; border-radius: 50%; background: currentColor; display: inline-block;"></span>
                            {{ __('messages.assigned') }}
                        </span>
                    @else
                        <span class="hallmark-status-badge badge-status-available" style="font-size: 0.72rem; padding: 2px 8px;">
                            <span style="width: 5px; height: 5px; border-radius: 50%; background: currentColor; display: inline-block;"></span>
                            {{ __('messages.returned') ?? 'Returned' }}
                        </span>
                    @endif
                </div>
                <small class="text-muted d-block font-mono" style="font-size: 0.8rem; margin-top: 2px; white-space: normal; line-height: 1.3;">
                    TAG: {{ $assignment->asset->asset_tag ?? '-' }} &bull; {{ $assignment->asset->name ?? '-' }} &bull; {{ __('messages.recipient') }}: {{ $assignment->employee->name ?? '-' }}
                </small>
            </div>
        </div>
    </div>
</div>

<div class="card theme-card master-form-card handover-form-card">
    <form action="{{ route('assignments.update', array_merge([$assignment->id], request()->query())) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        @if(session('error'))
            <div class="alert alert-danger mx-4 mt-4 mb-0" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; border-radius: 14px; backdrop-filter: blur(10px);">
                <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger mx-4 mt-4 mb-0" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; border-radius: 14px; backdrop-filter: blur(10px);">
                <i class="fas fa-exclamation-triangle mr-1"></i> Terjadi kesalahan input:
                <ul class="mb-0 mt-1 pl-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ===== SECTION 1: UNIT & RECIPIENT ===== --}}
        <div class="form-section">
            <div class="section-title section-cyan">
                <span class="section-badge badge-cyan"><i class="fas fa-laptop-house"></i></span>
                <div>
                    <div class="section-name">{{ __('messages.unit_asset_and_recipient') }}</div>
                    <div class="section-sub">{{ __('messages.unit_recipient_edit_subtitle') }}</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group mb-3">
                    <label class="field-label">
                        {{ __('messages.asset') }}
                    </label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="fas fa-lock text-muted"></i></span>
                        <input type="text" class="field-input font-mono text-muted" value="[{{ $assignment->asset->asset_tag ?? '-' }}] {{ $assignment->asset->name ?? '-' }}" readonly style="cursor: not-allowed; opacity: 0.85;">
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                        <i class="fas fa-info-circle mr-1"></i>{{ __('messages.asset_cannot_be_changed') }}
                    </small>
                </div>

                <div class="col-md-6 form-group mb-3">
                    <label class="field-label">
                        {{ __('messages.employee') }} <span class="text-danger">*</span>
                    </label>
                    <select name="employee_id" id="select_employee_id" class="field-select select2-field custom-filter-select @error('employee_id') is-invalid @enderror" required>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id', $assignment->employee_id) == $employee->id ? 'selected' : '' }}>
                                [{{ $employee->employee_id }}] {{ $employee->name }} {{ $employee->department ? 'â€” '.$employee->department->name : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-12 form-group mt-2 mb-1">
                    <label class="field-label" style="font-size: 0.82rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: var(--color-ink-1); margin-bottom: 8px;">
                        {{ __('messages.handover_photo') }} <small class="text-muted font-weight-normal">({{ __('messages.optional') }})</small>
                    </label>
                    <div class="custom-file-upload-card rounded-lg" style="padding: 16px 20px !important; background: rgba(15, 23, 42, 0.35); border: 1px dashed rgba(148, 163, 184, 0.25); border-radius: 12px; transition: all 0.2s ease;">
                        <div class="d-flex align-items-center flex-nowrap" style="gap: 16px;">
                            <div class="flex-shrink-0 position-relative d-flex align-items-center justify-content-center rounded-lg" style="width: 48px; height: 48px; min-width: 48px; min-height: 48px; flex-shrink: 0; aspect-ratio: 1 / 1; background: rgba(99, 102, 241, 0.08); border: 1px dashed rgba(99, 102, 241, 0.3); color: #818cf8; font-size: 1.15rem; overflow: hidden;">
                                @if($assignment->photo)
                                    <img id="handover_photo_preview" src="{{ asset('storage/' . $assignment->photo) }}" alt="Preview" class="rounded-lg shadow-sm" style="width: 48px; height: 48px; object-fit: cover; border: 2px solid rgba(99, 102, 241, 0.3);">
                                    <i id="handover_photo_icon" class="fas fa-camera" style="display: none;"></i>
                                @else
                                    <img id="handover_photo_preview" src="#" alt="Preview" class="rounded-lg shadow-sm" style="width: 48px; height: 48px; object-fit: cover; display: none;">
                                    <i id="handover_photo_icon" class="fas fa-camera"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1" style="min-width: 0;">
                                <div class="d-flex align-items-center flex-nowrap" style="gap: 8px;">
                                    <!-- Camera Direct Input -->
                                    <div class="position-relative">
                                        <input type="file" name="photo" id="handover_photo_camera" class="custom-file-input-field @error('photo') is-invalid @enderror" accept="image/*" capture="environment" style="position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer; z-index: 2;" onchange="previewSelectedImage(this, 'handover_photo_preview', 'handover_photo_icon')">
                                        <label for="handover_photo_camera" class="btn btn-sm btn-indigo mb-0 px-3 py-1.5 text-nowrap" style="background: linear-gradient(135deg, #6366f1, #4f46e5); color: #ffffff; border-radius: 8px; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 3px 10px rgba(99, 102, 241, 0.35);">
                                            <i class="fas fa-camera"></i> {{ __('messages.camera') }}
                                        </label>
                                    </div>
                                    <!-- File / Gallery Input -->
                                    <div class="position-relative">
                                        <input type="file" name="photo" id="handover_photo_file" class="custom-file-input-field @error('photo') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp" style="position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer; z-index: 2;" onchange="previewSelectedImage(this, 'handover_photo_preview', 'handover_photo_icon')">
                                        <label for="handover_photo_file" class="btn btn-sm btn-outline-secondary mb-0 px-3 py-1.5 text-nowrap" style="border: 1px solid rgba(148, 163, 184, 0.3); background: rgba(148, 163, 184, 0.1); color: var(--color-ink-1); border-radius: 8px; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                                            <i class="fas fa-folder-open"></i> {{ __('messages.file') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="field-hint text-muted text-truncate" style="font-size: 0.72rem; margin-top: 4px;">
                                    <i class="fas fa-info-circle mr-1"></i> {{ __('messages.camera_file_hint') }}
                                </div>
                                @error('photo')<div class="field-error text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== SECTION 2: SCHEDULE & NOTES ===== --}}
        <div class="form-section">
            <div class="section-title section-purple">
                <span class="section-badge badge-purple"><i class="fas fa-calendar-alt"></i></span>
                <div>
                    <div class="section-name">{{ __('messages.schedule_and_notes') }}</div>
                    <div class="section-sub">{{ __('messages.schedule_notes_edit_subtitle') }}</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group mb-3">
                    <label class="field-label">
                        {{ __('messages.assigned_date') }} <span class="text-danger">*</span>
                    </label>
                    <div class="input-wrap">
                        <span class="input-icon"><i class="far fa-calendar-alt"></i></span>
                        <input type="text" name="assigned_date" class="field-input font-mono assignment-form-date @error('assigned_date') is-invalid @enderror" value="{{ old('assigned_date', $assignment->assigned_date) }}" placeholder="dd/mm/yyyy" autocomplete="off" required>
                    </div>
                    @error('assigned_date') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-3">
                    <label class="field-label">
                        {{ __('messages.notes') }}
                    </label>
                    <textarea name="notes" class="field-input @error('notes') is-invalid @enderror" rows="2" placeholder="{{ __('messages.notes_placeholder_assignment_edit') }}">{{ old('notes', ($assignment->notes && $assignment->notes !== 'null') ? $assignment->notes : '') }}</textarea>
                    @error('notes') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- ===== STICKY FOOTER ===== --}}
        <div class="form-footer">
            <a href="{{ route('assignments.index', request()->query()) }}" class="btn btn-sm btn-action-secondary px-4 py-2">
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
.badge-status-assigned  { background: rgba(99, 102, 241, 0.12); color: #818cf8; border-color: rgba(99, 102, 241, 0.25); }

/* â”€â”€â”€ Master Form Card â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.handover-form-card {
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

.section-name {
    font-size: 0.95rem; font-weight: 700; color: var(--color-ink-0);
    letter-spacing: .1px; line-height: 1.2;
}
.section-sub {
    font-size: 0.78rem; color: var(--color-ink-2); margin-top: 1px;
}

/* â”€â”€â”€ Field Labels & Inputs â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
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

@media (max-width: 767.98px) {
    .master-header-banner {
        padding: 14px 16px !important;
        border-radius: 14px !important;
        margin-bottom: 12px !important;
    }
    .form-section {
        padding: 16px 14px 8px !important;
    }
    .section-title {
        gap: 10px !important;
        margin-bottom: 14px !important;
    }
    .section-name {
        font-size: 0.88rem !important;
    }
    .section-sub {
        font-size: 0.74rem !important;
    }
    .custom-file-upload-card {
        padding: 12px 14px !important;
    }
    .form-footer {
        padding: 12px 16px !important;
        flex-direction: column-reverse !important;
        gap: 8px !important;
    }
    .form-footer .btn {
        width: 100% !important;
        justify-content: center !important;
        height: 42px !important;
    }
}
</style>

@push('scripts')
<script>
$(document).ready(function() {
    $('.select2-field').select2({
        width: '100%',
        placeholder: 'Pilih...',
        allowClear: true
    });

    flatpickr('.assignment-form-date', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        allowInput: true,
        appendTo: document.body,
        disableMobile: true
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

