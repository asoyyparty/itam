@extends('layouts.admin')

@section('title', __('messages.edit_user'))

@section('content')
<style>
    .user-form-card {
        border-radius: 16px !important;
        overflow: hidden !important;
        border: var(--rule-soft);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        max-width: 720px;
        margin: 0 auto;
    }
    .user-form-card .card-header {
        border-top-left-radius: 16px !important;
        border-top-right-radius: 16px !important;
    }
    .user-card-header {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        width: 100% !important;
    }
    .user-header-title-box {
        display: flex !important;
        align-items: center !important;
        flex-shrink: 0 !important;
        gap: 12px !important;
    }
    .custom-user-input,
    .user-form-card .select2-container--default .select2-selection--single {
        height: 44px !important;
        border-radius: 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.22) !important;
        box-shadow: none !important;
        font-size: 0.9rem !important;
        color: var(--color-ink-0) !important;
        transition: all 0.2s ease !important;
    }
    .user-form-card .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 42px !important;
        padding-left: 12px !important;
        color: var(--color-ink-0) !important;
    }
    .user-form-card .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
    }
    .custom-user-input:focus {
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2) !important;
        background: rgba(15, 23, 42, 0.35) !important;
    }
    .form-label-title {
        font-size: 0.88rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        margin-bottom: 8px !important;
    }

    @media (max-width: 767.98px) {
        .user-card-header {
            padding: 12px 14px !important;
        }
        .user-header-title-box h5 {
            font-size: 0.98rem !important;
        }
        .user-header-title-box small {
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

<div class="card theme-card user-form-card">
    <form action="{{ route('users.update', array_merge([$user->id], request()->query())) }}" method="POST">
        @csrf @method('PUT')
        <!-- Header Toolbar -->
        <div class="card-header bg-transparent border-bottom user-card-header py-3 px-3 px-md-4" style="border-color: rgba(148, 163, 184, 0.12) !important;">
            <div class="user-header-title-box">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.3); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-size: 1.1rem; flex-shrink: 0;">
                    <i class="fas fa-user-edit"></i>
                </div>
                <div>
                    <h5 class="mb-0 font-weight-bold theme-text" style="font-size: 1.08rem; line-height: 1.2;">Edit Login Account</h5>
                    <small class="text-muted d-block" style="font-size: 0.78rem;">Perbarui kredensial dan peran (role) akun {{ $user->name }}</small>
                </div>
            </div>
        </div>

        <div class="card-body p-3 p-md-4">
            <!-- Row 1: Name & Username -->
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.name') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" class="form-control theme-input custom-user-input @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" placeholder="Enter full name..." required autocomplete="off">
                    @error('name') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.username') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="username" class="form-control theme-input custom-user-input @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" placeholder="Enter username..." required autocomplete="off">
                    @error('username') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Email -->
            <div class="form-group mb-4">
                <label class="form-label form-label-title theme-text">
                    {{ __('messages.email') }} <span class="text-danger">*</span>
                </label>
                <input type="email" name="email" class="form-control theme-input custom-user-input @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" placeholder="Enter email address..." required autocomplete="off">
                @error('email') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
            </div>

            <!-- Row 2: Password & Confirm Password -->
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.password') }} <small class="text-muted">({{ __('messages.leave_blank_password') }})</small>
                    </label>
                    <input type="password" name="password" class="form-control theme-input custom-user-input @error('password') is-invalid @enderror" placeholder="Leave blank to keep current..." autocomplete="new-password">
                    @error('password') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.confirm_password') }}
                    </label>
                    <input type="password" name="password_confirmation" class="form-control theme-input custom-user-input" placeholder="Confirm new password..." autocomplete="new-password">
                </div>
            </div>

            <!-- Role Select -->
            <div class="form-group mb-2">
                <label class="form-label form-label-title theme-text">
                    {{ __('messages.role') }} <span class="text-danger">*</span>
                </label>
                <select name="role" class="form-control select2 @error('role') is-invalid @enderror" required>
                    <option value="">Select Role...</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ (old('role') ?? $user->roles->first()?->name) == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="card-footer bg-transparent py-3 px-3 px-md-4 border-top d-flex align-items-center justify-content-end card-footer-actions" style="gap: 10px; border-color: rgba(148, 163, 184, 0.12) !important;">
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">
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
    });
</script>
@endpush
@endsection
