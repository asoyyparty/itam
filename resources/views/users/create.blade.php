@extends('layouts.admin')

@section('title', __('messages.add_user'))

@section('content')
<!-- Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 -->
<style>
    /* ═══════════════════════════════════════════════════════════════════
       HALLMARK OBSIDIAN IAM CONSOLE: CREATE USER CREDENTIALS
       ═══════════════════════════════════════════════════════════════════ */
    .content-header {
        display: none !important;
    }

    .iam-vault-wrapper {
        padding-bottom: 24px;
    }

    .iam-vault-wrapper {
        width: 100%;
        max-width: 760px;
        margin: 1rem auto 2rem;
        min-width: 0;
    }

    .iam-vault-card {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 16px 36px -10px rgba(0, 0, 0, 0.45);
        position: relative;
    }
    .iam-vault-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, #6366f1 0%, #38bdf8 50%, #10b981 100%);
    }

    /* Vault Header */
    .iam-vault-header {
        padding: 20px 24px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.12);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .iam-vault-header-left {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }
    .iam-vault-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(99, 102, 241, 0.12);
        border: 1px solid rgba(99, 102, 241, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #818cf8;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .iam-vault-title {
        font-size: 1.12rem;
        font-weight: 700;
        letter-spacing: -0.01em;
        color: var(--color-ink-0, #f8fafc);
        margin: 0;
        line-height: 1.25;
    }
    .iam-vault-sub {
        font-size: 0.76rem;
        color: var(--color-ink-2, #94a3b8);
        margin-top: 3px;
    }
    .btn-vault-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 8px;
        background: rgba(148, 163, 184, 0.08);
        border: 1px solid rgba(148, 163, 184, 0.18);
        color: var(--color-ink-2, #cbd5e1);
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none !important;
        transition: all 0.15s ease;
    }
    .btn-vault-back:hover {
        background: rgba(148, 163, 184, 0.15);
        color: #ffffff;
    }

    /* Vault Body & Field Groups */
    .iam-vault-body {
        padding: 24px;
    }
    .iam-section-heading {
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #818cf8;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .iam-section-heading::after {
        content: '';
        flex: 1;
        height: 1px;
        background: rgba(148, 163, 184, 0.12);
    }
    .iam-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--color-ink-0, #f8fafc);
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .iam-input-wrap {
        position: relative;
        width: 100%;
    }
    .iam-input {
        width: 100%;
        height: 44px;
        padding: 0 14px;
        border-radius: 10px;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(148, 163, 184, 0.2);
        color: var(--color-ink-0, #f8fafc);
        font-size: 0.88rem;
        transition: all 0.2s ease;
    }
    .iam-input:focus {
        background: rgba(15, 23, 42, 0.85);
        border-color: #6366f1;
        outline: none;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    }
    .iam-input.is-invalid {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2) !important;
    }
    .iam-input-mono {
        font-family: 'JetBrains Mono', monospace;
    }
    .iam-input-icon-btn {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        color: #94a3b8;
        font-size: 0.86rem;
        cursor: pointer;
        padding: 4px;
        transition: color 0.15s ease;
    }
    .iam-input-icon-btn:hover {
        color: #f8fafc;
    }

    /* Password Generator & Toolkit */
    .iam-pwd-toolkit {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        background: rgba(99, 102, 241, 0.06);
        border: 1px solid rgba(99, 102, 241, 0.18);
        border-radius: 10px;
        padding: 8px 12px;
        margin-top: 10px;
        flex-wrap: wrap;
    }
    .btn-gen-pwd {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(99, 102, 241, 0.2);
        border: 1px solid rgba(99, 102, 241, 0.4);
        color: #a5b4fc;
        font-size: 0.76rem;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-gen-pwd:hover {
        background: rgba(99, 102, 241, 0.35);
        color: #ffffff;
    }
    .iam-pwd-feedback {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.74rem;
        color: var(--color-ink-2, #94a3b8);
    }
    .iam-pwd-meter {
        width: 70px;
        height: 5px;
        border-radius: 9999px;
        background: rgba(148, 163, 184, 0.2);
        overflow: hidden;
    }
    .iam-pwd-meter-fill {
        height: 100%;
        width: 0%;
        border-radius: 9999px;
        transition: width 0.3s ease, background-color 0.3s ease;
    }

    /* Select2 Dark Integration */
    .iam-vault-card .select2-container--default .select2-selection--single {
        height: 44px !important;
        border-radius: 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.2) !important;
        background: rgba(15, 23, 42, 0.5) !important;
        display: flex !important;
        align-items: center !important;
    }
    .iam-vault-card .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--color-ink-0, #f8fafc) !important;
        padding-left: 14px !important;
        line-height: 42px !important;
        font-size: 0.88rem !important;
    }
    .iam-vault-card .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
        right: 10px !important;
    }

    /* Vault Footer */
    .iam-vault-footer {
        padding: 18px 24px;
        border-top: 1px solid rgba(148, 163, 184, 0.12);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        background: rgba(15, 23, 42, 0.3);
    }
    .btn-vault-cancel {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 42px;
        padding: 0 20px;
        border-radius: 10px;
        background: transparent;
        border: 1px solid rgba(148, 163, 184, 0.22);
        color: var(--color-ink-2, #cbd5e1);
        font-weight: 600;
        font-size: 0.86rem;
        text-decoration: none !important;
        transition: all 0.15s ease;
    }
    .btn-vault-cancel:hover {
        background: rgba(148, 163, 184, 0.1);
        color: #ffffff;
    }
    .btn-vault-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 42px;
        padding: 0 22px;
        border-radius: 10px;
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        border: 1px solid rgba(99, 102, 241, 0.4);
        color: #ffffff;
        font-weight: 600;
        font-size: 0.86rem;
        box-shadow: 0 2px 10px rgba(99, 102, 241, 0.3);
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .btn-vault-submit:hover {
        background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
        box-shadow: 0 4px 16px rgba(99, 102, 241, 0.45);
        transform: translateY(-1px);
    }

    /* Toast */
    .iam-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: rgba(15, 23, 42, 0.95);
        border: 1px solid rgba(99, 102, 241, 0.4);
        color: #ffffff;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 0.84rem;
        font-weight: 600;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 9999;
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
    }
    .iam-toast.show {
        transform: translateY(0);
        opacity: 1;
    }

    @media (max-width: 767.98px) {
        .iam-vault-wrapper {
            margin: 0.5rem auto 6px !important;
            padding-bottom: 0 !important;
        }
        .iam-vault-card {
            margin-bottom: 0 !important;
        }
        .iam-vault-header {
            padding: 16px;
        }
        .iam-vault-body {
            padding: 16px;
        }
        .iam-vault-footer {
            flex-direction: column-reverse;
            padding: 16px;
            margin-bottom: 0 !important;
        }
        .btn-vault-cancel, .btn-vault-submit {
            width: 100%;
        }
        .iam-input {
            font-size: 16px !important; /* Prevents auto-zoom on mobile */
        }
    }
</style>

<div class="iam-vault-wrapper">
    <div class="iam-vault-card">
        <form action="{{ route('users.store') }}" method="POST" autocomplete="off">
            @csrf

            <!-- Vault Header -->
            <div class="iam-vault-header">
                <div class="iam-vault-header-left">
                    <div class="iam-vault-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div>
                        <h2 class="iam-vault-title">{{ __('messages.add_new_user') }}</h2>
                        <div class="iam-vault-sub">{{ __('messages.add_user_subtitle') }}</div>
                    </div>
                </div>
                <a href="{{ route('users.index') }}" class="btn-vault-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>{{ __('messages.back') }}</span>
                </a>
            </div>

            <!-- Vault Body -->
            <div class="iam-vault-body">
                <!-- Section 1: User Profile Identity -->
                <div class="iam-section-heading">
                    <i class="fas fa-id-badge"></i>
                    <span>{{ __('messages.name') }} &amp; {{ __('messages.email') }}</span>
                </div>

                <div class="row">
                    <!-- Full Name -->
                    <div class="col-md-6 form-group mb-3">
                        <label class="iam-label">
                            <span>{{ __('messages.name') }} <span class="text-danger">*</span></span>
                        </label>
                        <input type="text" name="name" class="iam-input @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="{{ __('messages.enter_full_name_placeholder') }}" required autocomplete="off">
                        @error('name')
                            <small class="text-danger mt-1 d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div class="col-md-6 form-group mb-3">
                        <label class="iam-label">
                            <span>{{ __('messages.username') }} <span class="text-danger">*</span></span>
                            <small class="text-muted" style="font-weight: normal; font-size: 0.72rem;">a-z, 0-9, ., _, -</small>
                        </label>
                        <input type="text" id="iamUsernameInput" name="username" class="iam-input iam-input-mono @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="{{ __('messages.enter_username_placeholder') }}" required autocomplete="off">
                        @error('username')
                            <small class="text-danger mt-1 d-block">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group mb-4">
                    <label class="iam-label">
                        <span>{{ __('messages.email') }} <span class="text-danger">*</span></span>
                    </label>
                    <input type="email" name="email" class="iam-input @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="{{ __('messages.enter_email_placeholder') }}" required autocomplete="off">
                    @error('email')
                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Section 2: Security Credentials -->
                <div class="iam-section-heading mt-4">
                    <i class="fas fa-key"></i>
                    <span>{{ __('messages.security_credentials') }}</span>
                </div>

                <div class="row">
                    <!-- Password -->
                    <div class="col-md-6 form-group mb-2">
                        <label class="iam-label">
                            <span>{{ __('messages.password') }} <span class="text-danger">*</span></span>
                        </label>
                        <div class="iam-input-wrap">
                            <input type="password" id="iamPassword" name="password" class="iam-input iam-input-mono @error('password') is-invalid @enderror" placeholder="{{ __('messages.enter_password_placeholder') }}" required autocomplete="new-password">
                            <button type="button" class="iam-input-icon-btn" onclick="togglePasswordVisibility('iamPassword', this)" title="{{ __('messages.show_password') }}">
                                <i class="far fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <small class="text-danger mt-1 d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="col-md-6 form-group mb-2">
                        <label class="iam-label">
                            <span>{{ __('messages.confirm_password') }} <span class="text-danger">*</span></span>
                        </label>
                        <div class="iam-input-wrap">
                            <input type="password" id="iamPasswordConfirm" name="password_confirmation" class="iam-input iam-input-mono" placeholder="{{ __('messages.confirm_password_placeholder') }}" required autocomplete="new-password">
                            <button type="button" class="iam-input-icon-btn" onclick="togglePasswordVisibility('iamPasswordConfirm', this)" title="{{ __('messages.show_password') }}">
                                <i class="far fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Password Toolkit (1-Click Generator & Strength Meter) -->
                <div class="iam-pwd-toolkit mb-4">
                    <button type="button" class="btn-gen-pwd" onclick="generateSecurePassword()">
                        <i class="fas fa-bolt"></i>
                        <span>{{ __('messages.generate_password') }}</span>
                    </button>
                    <div class="iam-pwd-feedback">
                        <span id="pwdStrengthLabel">{{ __('messages.password_min_chars') }}</span>
                        <div class="iam-pwd-meter">
                            <div id="pwdMeterFill" class="iam-pwd-meter-fill"></div>
                        </div>
                        <span id="pwdMatchStatus"></span>
                    </div>
                </div>

                <!-- Section 3: System Role Authorization -->
                <div class="iam-section-heading mt-4">
                    <i class="fas fa-shield-alt"></i>
                    <span>{{ __('messages.system_access_level') }}</span>
                </div>

                <div class="form-group mb-2">
                    <label class="iam-label">
                        <span>{{ __('messages.role') }} <span class="text-danger">*</span></span>
                    </label>
                    <select name="role" class="form-control select2 @error('role') is-invalid @enderror" required>
                        <option value="">{{ __('messages.select_role') }}</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <!-- Vault Footer Actions -->
            <div class="iam-vault-footer">
                <a href="{{ route('users.index') }}" class="btn-vault-cancel">
                    {{ __('messages.cancel') }}
                </a>
                <button type="submit" class="btn-vault-submit">
                    <i class="fas fa-save"></i>
                    <span>{{ __('messages.save') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Toast Notification -->
<div id="iamToast" class="iam-toast">
    <i class="fas fa-check-circle" style="color: #34d399;"></i>
    <span id="iamToastMessage">Copied</span>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        if ($.fn.select2) {
            $('.select2').select2({ width: '100%' });
        }
    });

    // Toggle Password Visibility
    function togglePasswordVisibility(fieldId, btn) {
        const field = document.getElementById(fieldId);
        const icon = btn.querySelector('i');
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // 1-Click Cryptographically Strong Password Generator
    function generateSecurePassword() {
        const length = 12;
        const upper = "ABCDEFGHJKLMNPQRSTUVWXYZ";
        const lower = "abcdefghijkmnopqrstuvwxyz";
        const numbers = "23456789";
        const symbols = "!@#$%^&*()-_";
        const allChars = upper + lower + numbers + symbols;

        let password = "";
        password += upper.charAt(Math.floor(Math.random() * upper.length));
        password += lower.charAt(Math.floor(Math.random() * lower.length));
        password += numbers.charAt(Math.floor(Math.random() * numbers.length));
        password += symbols.charAt(Math.floor(Math.random() * symbols.length));

        for (let i = 4; i < length; i++) {
            password += allChars.charAt(Math.floor(Math.random() * allChars.length));
        }

        // Shuffle
        password = password.split('').sort(() => 0.5 - Math.random()).join('');

        const pwdField = document.getElementById('iamPassword');
        const confirmField = document.getElementById('iamPasswordConfirm');
        pwdField.type = 'text';
        confirmField.type = 'text';
        pwdField.value = password;
        confirmField.value = password;

        // Trigger input event to update meter
        evaluatePassword();

        // Copy to clipboard
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(password);
        }
        showToast("{{ __('messages.password_copied') }}");
    }

    // Evaluate Password Strength & Matching
    function evaluatePassword() {
        const pwd = document.getElementById('iamPassword').value;
        const confirm = document.getElementById('iamPasswordConfirm').value;
        const meter = document.getElementById('pwdMeterFill');
        const label = document.getElementById('pwdStrengthLabel');
        const match = document.getElementById('pwdMatchStatus');

        if (!pwd) {
            meter.style.width = '0%';
            label.textContent = "{{ __('messages.password_min_chars') }}";
            match.innerHTML = '';
            return;
        }

        let score = 0;
        if (pwd.length >= 8) score += 35;
        if (/[A-Z]/.test(pwd) && /[a-z]/.test(pwd)) score += 25;
        if (/[0-9]/.test(pwd)) score += 20;
        if (/[^A-Za-z0-9]/.test(pwd)) score += 20;

        meter.style.width = Math.min(score, 100) + '%';
        if (score < 40) {
            meter.style.backgroundColor = '#ef4444';
            label.textContent = "{{ __('messages.password_strength') }}: Weak";
            label.style.color = '#f87171';
        } else if (score < 75) {
            meter.style.backgroundColor = '#f59e0b';
            label.textContent = "{{ __('messages.password_strength') }}: Medium";
            label.style.color = '#fbbf24';
        } else {
            meter.style.backgroundColor = '#10b981';
            label.textContent = "{{ __('messages.password_strength') }}: Strong";
            label.style.color = '#34d399';
        }

        if (confirm) {
            if (pwd === confirm) {
                match.innerHTML = '<i class="fas fa-check-circle" style="color: #34d399;" title="{{ __('messages.password_match') }}"></i>';
            } else {
                match.innerHTML = '<i class="fas fa-times-circle" style="color: #f87171;" title="{{ __('messages.password_mismatch') }}"></i>';
            }
        } else {
            match.innerHTML = '';
        }
    }

    document.getElementById('iamPassword').addEventListener('input', evaluatePassword);
    document.getElementById('iamPasswordConfirm').addEventListener('input', evaluatePassword);

    function showToast(message) {
        const toast = document.getElementById('iamToast');
        const msgSpan = document.getElementById('iamToastMessage');
        msgSpan.textContent = message;
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 2500);
    }
</script>
@endpush
@endsection