@extends('layouts.admin')

@section('title', __('messages.user_profile') ?? 'User Profile')

@push('styles')
<style>
    /* â”€â”€ Hallmark User Profile Design System â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
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

    .profile-card-box {
        border-radius: 20px !important;
        overflow: hidden !important;
        border: 1px solid rgba(148, 163, 184, 0.14) !important;
        background: rgba(15, 23, 42, 0.45) !important;
        backdrop-filter: blur(14px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25) !important;
        transition: all 0.25s ease !important;
        margin-bottom: 20px;
    }
    body.light-mode .profile-card-box {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05) !important;
    }

    .prof-avatar-hero-glow {
        width: 104px;
        height: 104px;
        border-radius: 50%;
        margin: 0 auto;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        color: #ffffff;
        font-size: 2.8rem;
        font-weight: 800;
        box-shadow: 0 12px 35px rgba(79, 70, 229, 0.45), 0 0 25px rgba(99, 102, 241, 0.3);
        border: 4px solid rgba(255, 255, 255, 0.2);
    }
    .prof-avatar-hero-img {
        width: 104px;
        height: 104px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto;
        box-shadow: 0 12px 35px rgba(79, 70, 229, 0.45), 0 0 25px rgba(99, 102, 241, 0.3);
        border: 4px solid rgba(99, 102, 241, 0.5);
    }

    .profile-info-grid {
        background: rgba(15, 23, 42, 0.3);
        border-radius: 16px;
        padding: 16px;
        border: 1px solid rgba(148, 163, 184, 0.12);
    }
    body.light-mode .profile-info-grid {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
    }

    .custom-prof-input {
        height: 44px !important;
        border-radius: 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.3) !important;
        box-shadow: none !important;
        font-size: 0.9rem !important;
        color: var(--color-ink-0) !important;
        transition: all 0.2s ease !important;
    }
    body.light-mode .custom-prof-input {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        color: #0f172a !important;
    }
    .custom-prof-input:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
    }

    /* Input Group Seamless Eye Button Fix */
    .input-group > .custom-prof-input {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }
    .input-group > .input-group-append > .btn-eye-toggle {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
        border-top-right-radius: 10px !important;
        border-bottom-right-radius: 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        border-left: none !important;
        background: rgba(15, 23, 42, 0.3) !important;
        color: #94a3b8 !important;
        padding: 0 14px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    body.light-mode .input-group > .input-group-append > .btn-eye-toggle {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #64748b !important;
    }

    /* Card Header Single Row Layout */
    .header-single-row {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        gap: 12px !important;
        width: 100% !important;
    }

    .avatar-upload-dropcard {
        padding: 16px 20px !important;
        background: rgba(15, 23, 42, 0.3);
        border: 1px dashed rgba(148, 163, 184, 0.25);
        border-radius: 14px;
        transition: all 0.2s ease;
    }
    body.light-mode .avatar-upload-dropcard {
        background: #f8fafc !important;
        border: 1px dashed #cbd5e1 !important;
    }

    /* Mobile Responsive Optimizations (< 768px) */
    @media (max-width: 767.98px) {
        .profile-card-box {
            border-radius: 16px !important;
            margin-bottom: 16px !important;
        }
        .prof-action-btn-box {
            flex-direction: column;
            width: 100%;
            gap: 10px !important;
        }
        .prof-action-btn-box .btn {
            width: 100% !important;
            justify-content: center !important;
            height: 44px !important;
            border-radius: 999px !important;
        }
    }
</style>
@endpush

@section('content')

<!-- Hallmark Master Header Banner -->
<div class="master-header-banner">
    <div class="d-flex align-items-center justify-content-between flex-nowrap w-100" style="display: flex !important; flex-direction: row !important; align-items: center !important; justify-content: space-between !important; flex-wrap: nowrap !important; gap: 12px; width: 100% !important;">
        <div class="d-flex align-items-center min-w-0" style="gap: 12px; min-width: 0; flex: 1 1 auto;">
            <div style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(79, 70, 229, 0.08)); border: 1px solid rgba(99, 102, 241, 0.35); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 1.15rem; flex-shrink: 0; box-shadow: 0 0 14px rgba(99, 102, 241, 0.25);">
                <i class="fas fa-user-cog"></i>
            </div>
            <div class="text-truncate" style="min-width: 0;">
                <h5 class="mb-0 font-weight-bold theme-text text-truncate" style="font-size: 1.05rem; line-height: 1.2;">{{ __('messages.user_profile') }}</h5>
                <small class="text-muted d-block" style="font-size: 0.78rem; margin-top: 2px; white-space: normal; line-height: 1.3;">{{ __('messages.edit_profile_desc') }}</small>
            </div>
        </div>

        <div class="d-flex align-items-center ml-auto flex-shrink-0" style="margin-left: auto !important;">
            <span class="badge text-nowrap" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); padding: 5px 12px; border-radius: 999px; font-size: 0.75rem; font-weight: 600;">
                <i class="fas fa-circle mr-1" style="font-size: 0.42rem; vertical-align: middle;"></i> {{ __('messages.active_session') }}
            </span>
        </div>
    </div>
</div>

<div class="row">
    <!-- User Overview Avatar Hero Card -->
    <div class="col-lg-4 mb-4">
        <div class="card profile-card-box text-center p-4">
            <div class="mb-3 position-relative">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" class="prof-avatar-hero-img" alt="{{ $user->name }}">
                @else
                    <div class="prof-avatar-hero-glow">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <h4 class="font-weight-bold theme-text mb-1" style="font-size: 1.25rem;">{{ $user->name }}</h4>
            <div class="mb-2">
                <code style="font-size: 0.88rem; padding: 3px 10px; background: rgba(99, 102, 241, 0.12); border-radius: 6px; color: #818cf8; font-family: var(--font-mono);">
                    @ {{ $user->username }}
                </code>
            </div>

            <div class="mb-4">
                <span class="badge" style="background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3); font-size: 0.78rem; padding: 6px 16px; font-weight: 600; border-radius: 999px;">
                    <i class="fas fa-user-shield mr-1"></i> {{ $user->roles->first()?->name ?? 'User' }}
                </span>
            </div>

            <div class="profile-info-grid text-left" style="font-size: 0.86rem;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted"><i class="fas fa-envelope text-info mr-2"></i> Email</span>
                    <span class="font-weight-bold theme-text text-truncate ml-2" style="max-width: 180px;">{{ $user->email }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted"><i class="fas fa-check-circle text-success mr-2"></i> {{ __('messages.status') }}</span>
                    <span class="badge badge-pill badge-success" style="font-size: 0.72rem; padding: 3px 10px;">{{ __('messages.verified') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted"><i class="fas fa-calendar-alt text-warning mr-2"></i> {{ __('messages.created') }}</span>
                    <span class="font-weight-bold theme-text" style="font-family: var(--font-mono); font-size: 0.82rem;">
                        {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Forms Column -->
    <div class="col-lg-8">
        <!-- Update Profile Information Card -->
        <div class="card profile-card-box">
            <div class="card-header bg-transparent border-bottom p-3" style="border-color: rgba(148, 163, 184, 0.1) !important;">
                <div class="header-single-row">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.3); color: #60a5fa; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; flex-shrink: 0;">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <div class="header-single-row-text">
                        <h6 class="font-weight-bold theme-text mb-0" style="font-size: 0.98rem; line-height: 1.2;">{{ __('messages.edit_profile') }}</h6>
                        <small class="text-muted d-block" style="font-size: 0.76rem;">{{ __('messages.edit_profile_subtitle') }}</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-3 p-md-4">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('patch')

                    <!-- Profile Photo / Avatar Upload -->
                    <div class="form-group mb-4">
                        <label class="form-label font-weight-bold theme-text mb-2" style="font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8;">
                            {{ __('messages.profile_photo_avatar') }}
                        </label>
                        <div class="avatar-upload-dropcard">
                            <!-- Avatar Preview + Kamera + Berkas Buttons in 1 Single Horizontal Row -->
                            <div class="d-flex align-items-center flex-nowrap mb-2" style="gap: 12px; width: 100%;">
                                <!-- Avatar Preview Circle -->
                                <div class="flex-shrink-0 position-relative d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; background: rgba(99, 102, 241, 0.1); border: 2px solid rgba(99, 102, 241, 0.4); overflow: hidden; box-shadow: 0 4px 14px rgba(99, 102, 241, 0.25);">
                                    <img id="avatar-preview" src="{{ $user->avatar ? asset('storage/' . $user->avatar) : '#' }}" class="rounded-circle" style="width: 48px; height: 48px; object-fit: cover; {{ $user->avatar ? '' : 'display:none;' }}">
                                    <div id="avatar-fallback" class="d-flex align-items-center justify-content-center rounded-circle font-weight-bold text-white" style="width: 48px; height: 48px; font-size: 1.15rem; background: linear-gradient(135deg, #4f46e5, #6366f1); {{ $user->avatar ? 'display:none;' : '' }}">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                </div>

                                <!-- Camera Input Button -->
                                <div class="position-relative flex-shrink-0">
                                    <input type="file" name="avatar" id="avatar-camera-input" class="custom-file-input-field @error('avatar') is-invalid @enderror" accept="image/*" capture="environment" style="position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer; z-index: 2;" onchange="previewSelectedImage(this, 'avatar-preview', 'avatar-fallback')">
                                    <label for="avatar-camera-input" class="btn btn-sm mb-0 px-3 py-2 text-nowrap" style="background: linear-gradient(135deg, #6366f1, #4f46e5); color: #ffffff; border-radius: 999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);">
                                        <i class="fas fa-camera"></i> {{ __('messages.camera') }}
                                    </label>
                                </div>

                                <!-- File / Gallery Input Button -->
                                <div class="position-relative flex-shrink-0">
                                    <input type="file" name="avatar" id="avatar-file-input" class="custom-file-input-field @error('avatar') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp" style="position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer; z-index: 2;" onchange="previewSelectedImage(this, 'avatar-preview', 'avatar-fallback')">
                                    <label for="avatar-file-input" class="btn btn-sm btn-outline-secondary mb-0 px-3 py-2 text-nowrap" style="border: 1px solid rgba(148, 163, 184, 0.3); background: rgba(148, 163, 184, 0.12); color: #cbd5e1; border-radius: 999px; font-size: 0.82rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fas fa-folder-open"></i> {{ __('messages.file') }}
                                    </label>
                                </div>
                            </div>

                            <div class="text-muted text-truncate" style="font-size: 0.73rem;">
                                <i class="fas fa-info-circle mr-1"></i> {{ __('messages.avatar_upload_hint') }}
                            </div>
                            @error('avatar')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold theme-text mb-1" style="font-size: 0.85rem;">{{ __('messages.full_name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control custom-prof-input theme-input @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autocomplete="name">
                            @error('name') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold theme-text mb-1" style="font-size: 0.85rem;">{{ __('messages.username') }} <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control custom-prof-input theme-input @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" required autocomplete="username">
                            @error('username') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold theme-text mb-1" style="font-size: 0.85rem;">{{ __('messages.email') }} <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control custom-prof-input theme-input @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="email">
                        @error('email') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                    </div>

                    <div class="d-flex align-items-center justify-content-end prof-action-btn-box" style="gap: 10px;">
                        @if (session('status') === 'profile-updated')
                            <span class="text-success font-weight-bold mr-2" style="font-size: 0.85rem;"><i class="fas fa-check-circle mr-1"></i> {{ __('messages.profile_updated_success') }}</span>
                        @endif
                        <button type="submit" class="btn btn-action-primary rounded-pill px-4" style="height: 42px; font-weight: 600;">
                            <i class="fas fa-save mr-1"></i> {{ __('messages.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Update Password Card -->
        <div class="card profile-card-box">
            <div class="card-header bg-transparent border-bottom p-3" style="border-color: rgba(148, 163, 184, 0.1) !important;">
                <div class="header-single-row">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #fbbf24; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; flex-shrink: 0;">
                        <i class="fas fa-key"></i>
                    </div>
                    <div class="header-single-row-text">
                        <h6 class="font-weight-bold theme-text mb-0" style="font-size: 0.98rem; line-height: 1.2;">{{ __('messages.change_password') }}</h6>
                        <small class="text-muted d-block" style="font-size: 0.76rem;">{{ __('messages.change_password_desc') }}</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-3 p-md-4">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')

                    <!-- Current Password -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold theme-text mb-1" style="font-size: 0.85rem;">{{ __('messages.current_password') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="current_password" id="current_password" class="form-control custom-prof-input theme-input @error('current_password', 'updatePassword') is-invalid @enderror" placeholder="{{ __('messages.current_password_placeholder') }}" required autocomplete="current-password">
                            <div class="input-group-append">
                                <button class="btn btn-eye-toggle toggle-pass-btn" type="button" data-target="current_password"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        @error('current_password', 'updatePassword') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                    </div>

                    <div class="row">
                        <!-- New Password -->
                        <div class="col-md-6 form-group mb-4">
                            <label class="font-weight-bold theme-text mb-1" style="font-size: 0.85rem;">{{ __('messages.new_password') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="new_password" class="form-control custom-prof-input theme-input @error('password', 'updatePassword') is-invalid @enderror" placeholder="{{ __('messages.new_password_placeholder') }}" required autocomplete="new-password">
                                <div class="input-group-append">
                                    <button class="btn btn-eye-toggle toggle-pass-btn" type="button" data-target="new_password"><i class="fas fa-eye"></i></button>
                                </div>
                            </div>
                            @error('password', 'updatePassword') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-md-6 form-group mb-4">
                            <label class="font-weight-bold theme-text mb-1" style="font-size: 0.85rem;">{{ __('messages.confirm_new_password') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control custom-prof-input theme-input" placeholder="{{ __('messages.confirm_new_password_placeholder') }}" required autocomplete="new-password">
                                <div class="input-group-append">
                                    <button class="btn btn-eye-toggle toggle-pass-btn" type="button" data-target="password_confirmation"><i class="fas fa-eye"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-end prof-action-btn-box">
                        <button type="submit" class="btn btn-action-primary rounded-pill px-4" style="height: 42px; font-weight: 600;">
                            <i class="fas fa-key mr-1"></i> {{ __('messages.update_password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewSelectedImage(input, previewId, fallbackId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#' + previewId).attr('src', e.target.result).show();
            $('#' + fallbackId).hide();
        };
        reader.readAsDataURL(input.files[0]);
    }
}

$(document).ready(function() {
    $('.toggle-pass-btn').click(function() {
        var targetId = $(this).data('target');
        var input = $('#' + targetId);
        var icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
});
</script>
@endpush

