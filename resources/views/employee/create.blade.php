@extends('layouts.admin')

@section('title', __('messages.add') . ' ' . __('messages.employee'))

@section('content')
<style>
    .emp-form-card {
        border-radius: 16px !important;
        overflow: hidden !important;
        border: var(--rule-soft);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    .emp-form-card .card-header {
        border-top-left-radius: 16px !important;
        border-top-right-radius: 16px !important;
    }
    .emp-card-header {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        width: 100% !important;
    }
    .emp-header-title-box {
        display: flex !important;
        align-items: center !important;
        flex-shrink: 0 !important;
        gap: 12px !important;
    }
    .custom-emp-input,
    .emp-form-card .select2-container--default .select2-selection--single {
        height: 44px !important;
        border-radius: 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.22) !important;
        box-shadow: none !important;
        font-size: 0.9rem !important;
        color: var(--color-ink-0) !important;
        transition: all 0.2s ease !important;
    }
    .emp-form-card .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 42px !important;
        padding-left: 12px !important;
        color: var(--color-ink-0) !important;
    }
    .emp-form-card .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
    }
    .custom-emp-input:focus {
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2) !important;
        background: rgba(15, 23, 42, 0.35) !important;
    }
    .emp-form-card .input-group-text {
        height: 44px !important;
        border-radius: 0 10px 10px 0 !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.25) !important;
        color: #94a3b8 !important;
    }
    .form-label-title {
        font-size: 0.88rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        margin-bottom: 8px !important;
    }

    @media (max-width: 767.98px) {
        .emp-card-header {
            padding: 12px 14px !important;
        }
        .emp-header-title-box h5 {
            font-size: 0.98rem !important;
        }
        .emp-header-title-box small {
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

<div class="card theme-card emp-form-card">
    <form action="{{ route('employees.store') }}" method="POST">
        @csrf
        <!-- Header Toolbar -->
        <div class="card-header bg-transparent border-bottom emp-card-header py-3 px-3 px-md-4" style="border-color: rgba(148, 163, 184, 0.12) !important;">
            <div class="emp-header-title-box">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(59, 130, 246, 0.12); border: 1px solid rgba(59, 130, 246, 0.3); display: flex; align-items: center; justify-content: center; color: #60a5fa; font-size: 1.1rem; flex-shrink: 0;">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div>
                    <h5 class="mb-0 font-weight-bold theme-text" style="font-size: 1.08rem; line-height: 1.2;">{{ __('messages.add') }} {{ __('messages.employee') }}</h5>
                    <small class="text-muted d-block" style="font-size: 0.78rem;">Registrasi data pegawai baru ke dalam sistem ITAM</small>
                </div>
            </div>
        </div>

        <div class="card-body p-3 p-md-4">
            <!-- Row 1: NIK & Full Name -->
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.employee_id') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="employee_id" class="form-control theme-input custom-emp-input @error('employee_id') is-invalid @enderror" value="{{ old('employee_id') }}" placeholder="Contoh: EMP-2026-001..." required autocomplete="off">
                    @error('employee_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.full_name') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" class="form-control theme-input custom-emp-input @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Masukkan nama lengkap pegawai..." required autocomplete="off">
                    @error('name') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Row 2: Email & Phone -->
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.email') }}
                    </label>
                    <input type="email" name="email" class="form-control theme-input custom-emp-input @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Alamat email resmi..." autocomplete="off">
                    @error('email') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.phone') }}
                    </label>
                    <input type="text" name="phone" class="form-control theme-input custom-emp-input @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="Nomor telepon / WhatsApp..." autocomplete="off">
                    @error('phone') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Row 3: Department, Location & Supervisor -->
            <div class="row">
                <div class="col-md-4 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.department') }}
                    </label>
                    <select name="department_id" class="form-control select2 @error('department_id') is-invalid @enderror">
                        <option value="">Pilih {{ __('messages.department') }}...</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-4 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.location') }}
                    </label>
                    <select name="location_id" class="form-control select2 @error('location_id') is-invalid @enderror">
                        <option value="">Pilih {{ __('messages.location') }}...</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                    @error('location_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-4 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.supervisor') ?? 'Supervisor' }}
                    </label>
                    <select name="supervisor_id" class="form-control select2 @error('supervisor_id') is-invalid @enderror">
                        <option value="">Pilih {{ __('messages.supervisor') ?? 'Supervisor' }}...</option>
                        @foreach($supervisors as $sup)
                            <option value="{{ $sup->id }}" {{ old('supervisor_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                    @error('supervisor_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Row 4: AnyDesk ID & AnyDesk Password -->
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.anydesk_id') ?? 'AnyDesk ID' }}
                    </label>
                    <input type="text" name="anydesk_id" class="form-control theme-input custom-emp-input @error('anydesk_id') is-invalid @enderror" value="{{ old('anydesk_id') }}" placeholder="Misal: 987 654 321..." autocomplete="off">
                    @error('anydesk_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.anydesk_password') ?? 'AnyDesk Password' }}
                    </label>
                    <input type="text" name="anydesk_password" class="form-control theme-input custom-emp-input @error('anydesk_password') is-invalid @enderror" value="{{ old('anydesk_password') }}" placeholder="Password AnyDesk..." autocomplete="off">
                    @error('anydesk_password') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Row 5: PC Username & PC Password -->
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">PC Username</label>
                    <input type="text" name="login_username" class="form-control theme-input custom-emp-input @error('login_username') is-invalid @enderror" value="{{ old('login_username') }}" placeholder="Domain / Local Username..." autocomplete="off">
                    @error('login_username') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">PC Password</label>
                    <div class="input-group">
                        <input type="password" id="login_password" name="login_password" class="form-control theme-input custom-emp-input border-right-0 @error('login_password') is-invalid @enderror" placeholder="PC/Login Password..." autocomplete="off">
                        <div class="input-group-append">
                            <span class="input-group-text cursor-pointer" onclick="togglePassword('login_password', this)">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    @error('login_password') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Row 6: Status -->
            <div class="row">
                <div class="col-md-6 form-group mb-2">
                    <label class="form-label form-label-title theme-text">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control select2 @error('status') is-invalid @enderror" required>
                        <option value="Active" {{ old('status', 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="card-footer bg-transparent py-3 px-3 px-md-4 border-top d-flex align-items-center justify-content-end card-footer-actions" style="gap: 10px; border-color: rgba(148, 163, 184, 0.12) !important;">
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">
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
