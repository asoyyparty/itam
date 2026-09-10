@extends('layouts.admin')

@section('title', __('messages.edit') . ' ' . __('messages.employee'))

@section('content')
<style>
    .master-header-banner {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.65), rgba(30, 41, 59, 0.45));
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 20px;
        padding: 16px 22px;
        backdrop-filter: blur(16px);
        margin-top: 0 !important;
        margin-bottom: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }
    body.light-mode .master-header-banner {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(241, 245, 249, 0.85));
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
    }

    .master-form-card {
        border-radius: 20px !important;
        overflow: hidden !important;
        border: 1px solid rgba(148, 163, 184, 0.14) !important;
        background: rgba(15, 23, 42, 0.45) !important;
        backdrop-filter: blur(14px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    }
    body.light-mode .master-form-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
    }

    .custom-emp-input,
    .emp-form-card .select2-container--default .select2-selection--single {
        height: 46px !important;
        border-radius: 12px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.3) !important;
        box-shadow: none !important;
        font-size: 0.92rem !important;
        color: var(--color-ink-0) !important;
        transition: all 0.2s ease !important;
    }
    body.light-mode .custom-emp-input,
    body.light-mode .emp-form-card .select2-container--default .select2-selection--single {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
    }

    .custom-emp-input:focus {
        border-color: #f59e0b !important;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2) !important;
        background: rgba(15, 23, 42, 0.45) !important;
    }
    body.light-mode .custom-emp-input:focus {
        background: #ffffff !important;
    }

    .form-section-title {
        font-size: 0.82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #fbbf24;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-label-title {
        font-size: 0.88rem;
        font-weight: 600;
        letter-spacing: 0.01em;
        margin-bottom: 8px !important;
    }

    .input-group > .custom-emp-input,
    .input-group > input {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
        border-right: none !important;
    }
    .input-group > .input-group-append > .input-group-text {
        height: 46px !important;
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
        border-top-right-radius: 12px !important;
        border-bottom-right-radius: 12px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        border-left: none !important;
        background: rgba(15, 23, 42, 0.3) !important;
        color: #94a3b8 !important;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 14px;
        transition: all 0.2s ease !important;
    }
    body.light-mode .input-group > .input-group-append > .input-group-text {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
        color: #64748b !important;
    }
    .input-group:focus-within > .custom-emp-input,
    .input-group:focus-within > input {
        border-color: #f59e0b !important;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2) !important;
    }
    .input-group:focus-within > .input-group-append > .input-group-text {
        border-color: #f59e0b !important;
        color: #fbbf24 !important;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2) !important;
    }

    @media (max-width: 767.98px) {
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

<!-- Hallmark Master Header Banner -->
<div class="master-header-banner">
    <div class="d-flex align-items-center" style="gap: 14px;">
        <div style="width: 44px; height: 44px; border-radius: 14px; background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(217, 119, 6, 0.08)); border: 1px solid rgba(245, 158, 11, 0.35); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-size: 1.2rem; flex-shrink: 0; box-shadow: 0 0 14px rgba(245, 158, 11, 0.25);">
            <i class="fas fa-user-edit"></i>
        </div>
        <div>
            <h5 class="mb-0 font-weight-bold theme-text" style="font-size: 1.15rem; line-height: 1.2;">{{ __('messages.edit') }} {{ __('messages.employee') }}</h5>
            <small class="text-muted d-block" style="font-size: 0.8rem; margin-top: 2px;">{{ __('messages.edit_employee_subtitle') }}</small>
        </div>
    </div>
</div>

<div class="card theme-card master-form-card emp-form-card">
    <form action="{{ route('employees.update', array_merge([$employee->id], request()->query())) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="card-body p-3 p-md-4">
            <!-- Photo Upload Section -->
            <div class="form-section-title">
                <i class="fas fa-camera"></i> <span>{{ __('messages.employee_photo_profile') }}</span>
            </div>
            <div class="form-group mb-4">
                <div class="custom-file-upload-card rounded-lg" style="padding: 18px 22px; background: rgba(15, 23, 42, 0.3); border: 1px dashed rgba(245, 158, 11, 0.3); border-radius: 16px;">
                    <div class="d-flex align-items-center flex-nowrap" style="gap: 16px;">
                        <div class="flex-shrink-0 position-relative d-flex align-items-center justify-content-center rounded-circle" style="width: 52px; height: 52px; background: rgba(245, 158, 11, 0.12); border: 1px dashed rgba(245, 158, 11, 0.4); color: #fbbf24; font-size: 1.2rem; overflow: hidden;">
                            @if($employee->photo)
                                <img id="edit-emp-photo-preview" src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->name }}" class="rounded-circle" style="width: 52px; height: 52px; object-fit: cover; border: 2px solid #f59e0b; box-shadow: 0 3px 10px rgba(245, 158, 11, 0.3);">
                                <i id="edit-emp-photo-icon" class="fas fa-user-circle" style="display: none;"></i>
                            @else
                                <img id="edit-emp-photo-preview" src="#" alt="Preview" class="rounded-circle" style="width: 52px; height: 52px; object-fit: cover; display: none;">
                                <i id="edit-emp-photo-icon" class="fas fa-user-circle"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1" style="min-width: 0;">
                            <div class="d-flex align-items-center flex-nowrap" style="gap: 8px;">
                                <div class="position-relative">
                                    <input type="file" name="photo" id="edit-emp-camera-input" class="custom-file-input-field @error('photo') is-invalid @enderror" accept="image/*" capture="environment" style="position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer; z-index: 2;" onchange="previewSelectedImage(this, 'edit-emp-photo-preview', 'edit-emp-photo-icon')">
                                    <label for="edit-emp-camera-input" class="btn btn-sm mb-0 px-3 py-2 text-nowrap" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: #ffffff; border-radius: 10px; font-size: 0.82rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fas fa-camera"></i> {{ __('messages.camera') }}
                                    </label>
                                </div>
                                <div class="position-relative">
                                    <input type="file" name="photo" id="edit-emp-file-input" class="custom-file-input-field @error('photo') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp" style="position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer; z-index: 2;" onchange="previewSelectedImage(this, 'edit-emp-photo-preview', 'edit-emp-photo-icon')">
                                    <label for="edit-emp-file-input" class="btn btn-sm btn-outline-secondary mb-0 px-3 py-2 text-nowrap" style="border: 1px solid rgba(148, 163, 184, 0.3); background: rgba(148, 163, 184, 0.1); color: #cbd5e1; border-radius: 10px; font-size: 0.82rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fas fa-folder-open"></i> {{ __('messages.file') }}
                                    </label>
                                </div>
                            </div>
                            <div class="field-hint text-muted text-truncate" style="font-size: 0.75rem; margin-top: 6px;">
                                {{ __('messages.employee_photo_hint') }}
                            </div>
                            @error('photo')<div class="field-error text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Identity Section -->
            <div class="form-section-title mt-4">
                <i class="fas fa-id-card"></i> <span>{{ __('messages.primary_identity') }}</span>
            </div>
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        <i class="fas fa-hashtag mr-1 text-warning"></i> {{ __('messages.employee_id') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="employee_id" class="form-control theme-input custom-emp-input @error('employee_id') is-invalid @enderror" value="{{ old('employee_id', $employee->employee_id) }}" placeholder="Contoh: EMP-2026-001..." required autocomplete="off">
                    @error('employee_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        <i class="fas fa-user mr-1 text-warning"></i> {{ __('messages.full_name') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" class="form-control theme-input custom-emp-input @error('name') is-invalid @enderror" value="{{ old('name', $employee->name) }}" placeholder="{{ __('messages.full_name') }}..." required autocomplete="off">
                    @error('name') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Contact Section -->
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        <i class="fas fa-envelope mr-1 text-info"></i> {{ __('messages.email') }}
                    </label>
                    <input type="email" name="email" class="form-control theme-input custom-emp-input @error('email') is-invalid @enderror" value="{{ old('email', ($employee->email && $employee->email !== 'null') ? $employee->email : '') }}" placeholder="{{ __('messages.email') }}..." autocomplete="off">
                    @error('email') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        <i class="fas fa-phone-alt mr-1 text-info"></i> {{ __('messages.phone') }}
                    </label>
                    <input type="text" name="phone" class="form-control theme-input custom-emp-input @error('phone') is-invalid @enderror" value="{{ old('phone', ($employee->phone && $employee->phone !== 'null') ? $employee->phone : '') }}" placeholder="{{ __('messages.phone') }}..." autocomplete="off">
                    @error('phone') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Organization Section -->
            <div class="form-section-title mt-4">
                <i class="fas fa-sitemap"></i> <span>{{ __('messages.organization_and_assignment') }}</span>
            </div>
            <div class="row">
                <div class="col-md-4 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        <i class="fas fa-building mr-1 text-warning"></i> {{ __('messages.department') }}
                    </label>
                    <select name="department_id" class="form-control select2 @error('department_id') is-invalid @enderror">
                        <option value="">{{ __('messages.select') }} {{ __('messages.department') }}...</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-4 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        <i class="fas fa-map-marker-alt mr-1 text-success"></i> {{ __('messages.location') }}
                    </label>
                    <select name="location_id" class="form-control select2 @error('location_id') is-invalid @enderror">
                        <option value="">{{ __('messages.select') }} {{ __('messages.location') }}...</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id', $employee->location_id) == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                    @error('location_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-4 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        <i class="fas fa-user-shield mr-1 text-indigo"></i> {{ __('messages.supervisor') ?? 'Supervisor' }}
                    </label>
                    <select name="supervisor_id" class="form-control select2 @error('supervisor_id') is-invalid @enderror">
                        <option value="">{{ __('messages.select') }} {{ __('messages.supervisor') ?? 'Supervisor' }}...</option>
                        @foreach($supervisors as $sup)
                            <option value="{{ $sup->id }}" {{ old('supervisor_id', $employee->supervisor_id) == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                    @error('supervisor_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- IT Credentials Section -->
            <div class="form-section-title mt-4">
                <i class="fas fa-key"></i> <span>{{ __('messages.it_credentials_and_remote') }}</span>
            </div>
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        <i class="fas fa-desktop mr-1 text-success"></i> {{ __('messages.anydesk_id') ?? 'AnyDesk ID' }}
                    </label>
                    <input type="text" name="anydesk_id" class="form-control theme-input custom-emp-input @error('anydesk_id') is-invalid @enderror" value="{{ old('anydesk_id', ($employee->anydesk_id && $employee->anydesk_id !== 'null') ? $employee->anydesk_id : '') }}" placeholder="987 654 321..." autocomplete="off">
                    @error('anydesk_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        <i class="fas fa-lock mr-1 text-success"></i> {{ __('messages.anydesk_password') }}
                    </label>
                    <div class="input-group">
                        <input type="password" id="anydesk_password" name="anydesk_password" class="form-control theme-input custom-emp-input border-right-0 @error('anydesk_password') is-invalid @enderror" value="{{ old('anydesk_password', ($employee->anydesk_password && $employee->anydesk_password !== 'null') ? $employee->anydesk_password : '') }}" placeholder="{{ __('messages.anydesk_password') }}..." autocomplete="off">
                        <div class="input-group-append">
                            <span class="input-group-text cursor-pointer" onclick="togglePassword('anydesk_password', this)">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    @error('anydesk_password') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        <i class="fas fa-user-circle mr-1 text-warning"></i> {{ __('messages.pc_username') }}
                    </label>
                    <input type="text" name="login_username" class="form-control theme-input custom-emp-input @error('login_username') is-invalid @enderror" value="{{ old('login_username', ($employee->login_username && $employee->login_username !== 'null') ? $employee->login_username : '') }}" placeholder="{{ __('messages.pc_username') }}..." autocomplete="off">
                    @error('login_username') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        <i class="fas fa-key mr-1 text-warning"></i> {{ __('messages.pc_password') }}
                    </label>
                    <div class="input-group">
                        <input type="password" id="login_password" name="login_password" class="form-control theme-input custom-emp-input border-right-0 @error('login_password') is-invalid @enderror" value="{{ old('login_password', ($employee->login_password && $employee->login_password !== 'null') ? $employee->login_password : '') }}" placeholder="{{ __('messages.pc_password') }}..." autocomplete="off">
                        <div class="input-group-append">
                            <span class="input-group-text cursor-pointer" onclick="togglePassword('login_password', this)">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    @error('login_password') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        <i class="fas fa-user-tie mr-1 text-info"></i> {{ __('messages.hris_username') }}
                    </label>
                    <input type="text" name="hris_username" class="form-control theme-input custom-emp-input @error('hris_username') is-invalid @enderror" value="{{ old('hris_username', ($employee->hris_username && $employee->hris_username !== 'null') ? $employee->hris_username : '') }}" placeholder="{{ __('messages.hris_username') }}..." autocomplete="off">
                    @error('hris_username') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        <i class="fas fa-shield-alt mr-1 text-info"></i> {{ __('messages.hris_password') }}
                    </label>
                    <div class="input-group">
                        <input type="password" id="hris_password" name="hris_password" class="form-control theme-input custom-emp-input border-right-0 @error('hris_password') is-invalid @enderror" value="{{ old('hris_password', ($employee->hris_password && $employee->hris_password !== 'null') ? $employee->hris_password : '') }}" placeholder="{{ __('messages.hris_password') }}..." autocomplete="off">
                        <div class="input-group-append">
                            <span class="input-group-text cursor-pointer" onclick="togglePassword('hris_password', this)">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    @error('hris_password') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group mb-2">
                    <label class="form-label form-label-title theme-text">
                        <i class="fas fa-toggle-on mr-1 text-primary"></i> Status <span class="text-danger">*</span>
                    </label>
                    <select name="status" class="form-control select2 @error('status') is-invalid @enderror" required>
                        <option value="Active" {{ old('status', $employee->status) == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status', $employee->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="card-footer bg-transparent py-3 px-3 px-md-4 border-top d-flex align-items-center justify-content-end card-footer-actions" style="gap: 10px; border-color: rgba(148, 163, 184, 0.12) !important;">
            <a href="{{ route('employees.index', request()->query()) }}" class="btn btn-outline-secondary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">
                {{ __('messages.cancel') }}
            </a>
            <button type="submit" class="btn btn-primary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f59e0b, #d97706); border: none; color: #ffffff;">
                <i class="fas fa-save mr-1"></i> {{ __('messages.update') }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function previewSelectedImage(input, previewId, iconId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
                document.getElementById(previewId).style.display = 'block';
                document.getElementById(iconId).style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function togglePassword(inputId, iconSpan) {
        const input = document.getElementById(inputId);
        const icon = iconSpan.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    $(document).ready(function() {
        $('.select2').select2({ width: '100%' });
    });
</script>
@endpush
@endsection
