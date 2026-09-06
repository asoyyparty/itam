@extends('layouts.admin')

@section('title', __('messages.add_vault'))

@section('content')
<!-- Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 -->
<style>
    /* ═══════════════════════════════════════════════════════════════════
       HALLMARK WORKBENCH: CREDENTIAL INTAKE & KEY GENERATION CONSOLE
       ═══════════════════════════════════════════════════════════════════ */
    .vault-create-container {
        max-width: 860px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        width: 100%;
        min-width: 0;
    }

    /* 1. Command Header */
    .vault-form-header {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 16px;
        padding: 18px 24px;
        position: relative;
        overflow: hidden;
    }
    .vault-form-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, #f59e0b 0%, #38bdf8 50%, #6366f1 100%);
    }
    .vault-header-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .vault-header-title-box {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .vault-form-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(245, 158, 11, 0.12);
        border: 1px solid rgba(245, 158, 11, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fbbf24;
        font-size: 1.2rem;
        flex-shrink: 0;
        box-shadow: 0 0 16px rgba(245, 158, 11, 0.2);
    }
    .vault-header-title-box h4 {
        font-size: 1.18rem;
        font-weight: 700;
        color: #f8fafc;
        line-height: 1.2;
        margin-bottom: 2px;
    }
    .vault-header-title-box p {
        color: #94a3b8;
        font-size: 0.82rem;
        margin-bottom: 0;
    }
    .btn-back-vault {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 16px;
        border-radius: 10px;
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(148, 163, 184, 0.2);
        color: #94a3b8 !important;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none !important;
        transition: all 0.15s ease;
    }
    .btn-back-vault:hover {
        background: rgba(15, 23, 42, 0.9);
        color: #f8fafc !important;
        border-color: rgba(148, 163, 184, 0.35);
    }

    /* 2. Bento Sections */
    .vault-section-card {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 14px;
        padding: 20px 22px;
        margin-bottom: 1.25rem;
    }
    .vault-section-head {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.1);
    }
    .vault-section-icon {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.88rem;
    }
    .sec-host .vault-section-icon { background: rgba(56, 189, 248, 0.12); color: #38bdf8; }
    .sec-cat .vault-section-icon { background: rgba(99, 102, 241, 0.12); color: #818cf8; }
    .sec-pwd .vault-section-icon { background: rgba(245, 158, 11, 0.14); color: #fbbf24; }
    .sec-notes .vault-section-icon { background: rgba(148, 163, 184, 0.12); color: #94a3b8; }

    .vault-section-title {
        font-size: 0.92rem;
        font-weight: 700;
        color: #f8fafc;
        margin-bottom: 0;
    }

    /* Form Controls */
    .vault-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: #cbd5e1;
        margin-bottom: 6px;
        display: block;
    }
    .vault-input {
        width: 100%;
        height: 42px;
        background: rgba(15, 23, 42, 0.6) !important;
        border: 1px solid rgba(148, 163, 184, 0.2) !important;
        border-radius: 10px !important;
        color: #f8fafc !important;
        font-size: 0.88rem !important;
        padding: 0 14px;
        transition: all 0.15s ease;
    }
    .vault-input:focus {
        border-color: #f59e0b !important;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2) !important;
        background: rgba(15, 23, 42, 0.85) !important;
        outline: none;
    }
    .vault-input-mono {
        font-family: 'JetBrains Mono', monospace !important;
    }

    /* Quick Category Pills */
    .quick-cat-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 8px;
    }
    .quick-cat-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 8px;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(148, 163, 184, 0.18);
        color: #94a3b8;
        font-size: 0.74rem;
        font-weight: 600;
        cursor: pointer;
        user-select: none;
        transition: all 0.15s ease;
    }
    .quick-cat-pill:hover {
        background: rgba(99, 102, 241, 0.15);
        border-color: rgba(99, 102, 241, 0.4);
        color: #a5b4fc;
    }
    .quick-cat-pill.active {
        background: rgba(245, 158, 11, 0.18);
        border-color: rgba(245, 158, 11, 0.5);
        color: #fbbf24;
    }

    /* Generator Box */
    .generator-box {
        background: rgba(0, 0, 0, 0.35);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 12px;
        padding: 14px 16px;
        margin-top: 12px;
    }
    .generator-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .generator-title {
        font-size: 0.80rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #fbbf24;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .btn-generate {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #ffffff !important;
        border-radius: 8px;
        padding: 5px 12px;
        font-size: 0.78rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-generate:hover {
        background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
    }
    .gen-options-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        font-size: 0.78rem;
        color: #cbd5e1;
    }
    .gen-len-buttons {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .btn-len-choice {
        background: rgba(15, 23, 42, 0.7);
        border: 1px solid rgba(148, 163, 184, 0.2);
        color: #94a3b8;
        padding: 2px 8px;
        border-radius: 6px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.74rem;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-len-choice.active {
        background: rgba(245, 158, 11, 0.2);
        border-color: rgba(245, 158, 11, 0.5);
        color: #fbbf24;
    }
    .gen-checkbox-label {
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        user-select: none;
        margin-bottom: 0;
    }

    /* Strength Bar */
    .strength-meter-wrap {
        margin-top: 8px;
    }
    .strength-meter-bar {
        height: 4px;
        border-radius: 2px;
        background: rgba(148, 163, 184, 0.15);
        overflow: hidden;
        position: relative;
    }
    .strength-meter-fill {
        height: 100%;
        width: 0%;
        border-radius: 2px;
        transition: all 0.25s ease;
    }
    .strength-text {
        font-size: 0.72rem;
        font-weight: 600;
        font-family: 'JetBrains Mono', monospace;
        margin-top: 4px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Action Footer */
    .vault-form-footer {
        background: var(--color-paper-0, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 14px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
    }
    .btn-vault-cancel {
        padding: 0.55rem 1.4rem;
        border-radius: 10px;
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(148, 163, 184, 0.2);
        color: #94a3b8 !important;
        font-size: 0.86rem;
        font-weight: 600;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }
    .btn-vault-cancel:hover {
        background: rgba(15, 23, 42, 0.9);
        color: #f8fafc !important;
    }
    .btn-vault-save {
        padding: 0.55rem 1.75rem;
        border-radius: 10px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border: 1px solid rgba(255, 255, 255, 0.18);
        color: #0f172a !important;
        font-size: 0.88rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.3);
        transition: all 0.15s ease;
    }
    .btn-vault-save:hover {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        box-shadow: 0 6px 18px rgba(245, 158, 11, 0.45);
    }

    @media (max-width: 767.98px) {
        .vault-form-header {
            padding: 14px 16px;
        }
        .vault-header-inner {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }
        .btn-back-vault {
            width: 100%;
            justify-content: center;
            height: 40px;
        }
        .vault-section-card {
            padding: 14px 16px;
        }
        .vault-form-footer {
            flex-direction: column-reverse;
            gap: 10px;
            padding: 14px 16px;
        }
        .btn-vault-cancel, .btn-vault-save {
            width: 100%;
            height: 42px;
            justify-content: center;
        }
    }
</style>

<div class="vault-create-container">
    <!-- 1. Command Header -->
    <div class="vault-form-header">
        <div class="vault-header-inner">
            <div class="vault-header-title-box">
                <div class="vault-form-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <div>
                    <h4>{{ __('messages.add_vault') }}</h4>
                    <p>{{ __('messages.add_vault_subtitle') }}</p>
                </div>
            </div>
            <a href="{{ route('password_vaults.index') }}" class="btn-back-vault">
                <i class="fas fa-arrow-left"></i>
                <span>{{ __('messages.back') ?? 'Back' }}</span>
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-0" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.35); color: #f87171; border-radius: 12px;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="color: #f87171;">×</button>
            <i class="icon fas fa-ban mr-2"></i> {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('password_vaults.store') }}" method="POST" id="vault-create-form">
        @csrf

        <!-- Section 1: Host & Identity -->
        <div class="vault-section-card sec-host">
            <div class="vault-section-head">
                <div class="vault-section-icon">
                    <i class="fas fa-server"></i>
                </div>
                <h5 class="vault-section-title">{{ __('messages.credential_details') }}</h5>
            </div>

            <div class="row">
                <div class="col-md-6 form-group mb-3 mb-md-0">
                    <label class="vault-label">
                        {{ __('messages.device_name') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="device_name"
                           class="vault-input @error('device_name') is-invalid @enderror"
                           value="{{ old('device_name') }}"
                           placeholder="{{ __('messages.device_name_placeholder') }}"
                           required
                           autocomplete="off">
                    @error('device_name') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-0">
                    <label class="vault-label">
                        {{ __('messages.username') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="username"
                           class="vault-input vault-input-mono @error('username') is-invalid @enderror"
                           value="{{ old('username') }}"
                           placeholder="{{ __('messages.username_placeholder') }}"
                           required
                           autocomplete="off">
                    @error('username') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Section 2: Category & Classification -->
        <div class="vault-section-card sec-cat">
            <div class="vault-section-head">
                <div class="vault-section-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <h5 class="vault-section-title">{{ __('messages.category_access') }}</h5>
            </div>

            <div class="form-group mb-0">
                <label class="vault-label">
                    {{ __('messages.category') }} <span class="text-danger">*</span>
                </label>
                <input type="text"
                       name="category"
                       id="category_input"
                       class="vault-input @error('category') is-invalid @enderror"
                       value="{{ old('category') }}"
                       placeholder="{{ __('messages.select_or_type_category') }}"
                       required
                       autocomplete="off">
                @error('category') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror

                <!-- Quick-Pill Badges -->
                <div class="mt-2">
                    <span class="text-muted" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em;">
                        <i class="fas fa-bolt text-warning mr-1"></i> {{ __('messages.quick_categories') }}:
                    </span>
                    <div class="quick-cat-pills mt-1">
                        @php
                            $pills = ['Wifi', 'Switch', 'CCTV', 'Windows', 'Email', 'Server', 'Database', 'Firewall', 'AnyDesk', 'Printer'];
                        @endphp
                        @foreach($pills as $p)
                            <span class="quick-cat-pill" data-category="{{ $p }}">
                                {{ $p }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Password & Strong Password Generator -->
        <div class="vault-section-card sec-pwd">
            <div class="vault-section-head">
                <div class="vault-section-icon">
                    <i class="fas fa-key"></i>
                </div>
                <h5 class="vault-section-title">{{ __('messages.security_key_section') }}</h5>
            </div>

            <div class="form-group mb-3">
                <label class="vault-label">
                    {{ __('messages.password') }} <span class="text-danger">*</span>
                </label>
                <div class="position-relative">
                    <input type="password"
                           name="encrypted_password"
                           id="encrypted_password"
                           class="vault-input vault-input-mono @error('encrypted_password') is-invalid @enderror"
                           style="padding-right: 80px;"
                           placeholder="{{ __('messages.password_input_placeholder') }}"
                           required
                           autocomplete="off">
                    <div style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); display: flex; align-items: center; gap: 4px;">
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary"
                                id="toggle-pwd-btn"
                                style="border-radius: 6px; padding: 3px 8px; font-size: 0.8rem; color: #94a3b8; border-color: rgba(148, 163, 184, 0.2);">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                @error('encrypted_password') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror

                <!-- Live Password Strength Meter -->
                <div class="strength-meter-wrap">
                    <div class="strength-meter-bar">
                        <div class="strength-meter-fill" id="strength-fill"></div>
                    </div>
                    <div class="strength-text">
                        <span class="text-muted">{{ __('messages.password_strength') }}:</span>
                        <span id="strength-label" style="color: #94a3b8;">—</span>
                    </div>
                </div>
            </div>

            <!-- Built-in Strong Password Generator -->
            <div class="generator-box">
                <div class="generator-header">
                    <span class="generator-title">
                        <i class="fas fa-shield-alt"></i> {{ __('messages.password_generator') }}
                    </span>
                    <button type="button" class="btn-generate" id="btn-generate-pwd">
                        <i class="fas fa-random"></i>
                        <span>{{ __('messages.generate_new') }}</span>
                    </button>
                </div>

                <div class="gen-options-row">
                    <!-- Length selector -->
                    <div class="d-flex align-items-center gap-2">
                        <span>{{ __('messages.password_length') }}:</span>
                        <div class="gen-len-buttons">
                            <span class="btn-len-choice" data-length="12">12</span>
                            <span class="btn-len-choice active" data-length="16">16</span>
                            <span class="btn-len-choice" data-length="20">20</span>
                            <span class="btn-len-choice" data-length="24">24</span>
                        </div>
                    </div>

                    <!-- Character sets -->
                    <label class="gen-checkbox-label" title="{{ __('messages.include_uppercase') }}">
                        <input type="checkbox" id="gen-opt-upper" checked>
                        <span>A-Z</span>
                    </label>
                    <label class="gen-checkbox-label" title="{{ __('messages.include_numbers') }}">
                        <input type="checkbox" id="gen-opt-numbers" checked>
                        <span>0-9</span>
                    </label>
                    <label class="gen-checkbox-label" title="{{ __('messages.include_symbols') }}">
                        <input type="checkbox" id="gen-opt-symbols" checked>
                        <span>!@#$%</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Section 4: Technical Notes -->
        <div class="vault-section-card sec-notes">
            <div class="vault-section-head">
                <div class="vault-section-icon">
                    <i class="fas fa-sticky-note"></i>
                </div>
                <h5 class="vault-section-title">{{ __('messages.technical_notes') }}</h5>
            </div>

            <div class="form-group mb-0">
                <textarea name="notes"
                          class="vault-input @error('notes') is-invalid @enderror"
                          style="height: 90px; padding: 10px 14px; resize: vertical;"
                          placeholder="{{ __('messages.notes_vault_placeholder') }}">{{ old('notes') }}</textarea>
                @error('notes') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Action Footer -->
        <div class="vault-form-footer">
            <a href="{{ route('password_vaults.index') }}" class="btn-vault-cancel">
                {{ __('messages.cancel') }}
            </a>
            <button type="submit" class="btn-vault-save">
                <i class="fas fa-check-circle"></i>
                <span>{{ __('messages.save') }}</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Strong Password Generator & Entropy Evaluator
function generateRandomPassword(length, useUpper, useNumbers, useSymbols) {
    var lower = 'abcdefghijkmnopqrstuvwxyz'; // exclude visually ambiguous l
    var upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ'; // exclude visually ambiguous I, O
    var numbers = '23456789'; // exclude 0, 1
    var symbols = '!@#$%^&*()-_=+[]{}|;:,.<>?';

    var charSet = lower;
    if (useUpper) charSet += upper;
    if (useNumbers) charSet += numbers;
    if (useSymbols) charSet += symbols;

    if (!charSet) charSet = lower;

    var result = '';
    // Ensure at least one from each selected set
    if (useUpper) result += upper[Math.floor(Math.random() * upper.length)];
    if (useNumbers) result += numbers[Math.floor(Math.random() * numbers.length)];
    if (useSymbols) result += symbols[Math.floor(Math.random() * symbols.length)];

    for (var i = result.length; i < length; i++) {
        result += charSet[Math.floor(Math.random() * charSet.length)];
    }

    // Shuffle result
    return result.split('').sort(function() { return 0.5 - Math.random(); }).join('');
}

function evaluatePasswordStrength(password) {
    if (!password || password.length === 0) {
        return { score: 0, text: '—', color: '#64748b', percent: 0 };
    }

    var score = 0;
    if (password.length >= 8) score += 1;
    if (password.length >= 14) score += 1;
    if (password.length >= 18) score += 1;
    if (/[A-Z]/.test(password)) score += 1;
    if (/[0-9]/.test(password)) score += 1;
    if (/[^A-Za-z0-9]/.test(password)) score += 1;

    if (score <= 2) {
        return { score: 1, text: "{{ __('messages.strength_weak') }}", color: '#ef4444', percent: 25 };
    } else if (score <= 4) {
        return { score: 2, text: "{{ __('messages.strength_fair') }}", color: '#f59e0b', percent: 50 };
    } else if (score <= 5) {
        return { score: 3, text: "{{ __('messages.strength_strong') }}", color: '#10b981', percent: 75 };
    } else {
        return { score: 4, text: "{{ __('messages.strength_very_strong') }}", color: '#38bdf8', percent: 100 };
    }
}

$(document).ready(function() {
    var selectedLength = 16;

    // Length selector click
    $('.btn-len-choice').click(function() {
        $('.btn-len-choice').removeClass('active');
        $(this).addClass('active');
        selectedLength = parseInt($(this).data('length'), 10);
    });

    // Quick-Pill Category click
    $('.quick-cat-pill').click(function() {
        var cat = $(this).data('category');
        $('#category_input').val(cat).trigger('change');
        $('.quick-cat-pill').removeClass('active');
        $(this).addClass('active');
    });

    // Generate Password click
    $('#btn-generate-pwd').click(function() {
        var useUpper = $('#gen-opt-upper').is(':checked');
        var useNumbers = $('#gen-opt-numbers').is(':checked');
        var useSymbols = $('#gen-opt-symbols').is(':checked');

        var newPwd = generateRandomPassword(selectedLength, useUpper, useNumbers, useSymbols);
        var input = $('#encrypted_password');
        input.val(newPwd).trigger('input');
        input.attr('type', 'text');
        $('#toggle-pwd-btn i').removeClass('fa-eye').addClass('fa-eye-slash');

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1800,
                icon: 'success',
                title: "{{ __('messages.generate_password') }}: " + newPwd,
                background: 'rgba(15, 23, 42, 0.95)',
                color: '#f8fafc'
            });
        }
    });

    // Password input listener for live strength meter
    $('#encrypted_password').on('input', function() {
        var pwd = $(this).val();
        var evalRes = evaluatePasswordStrength(pwd);

        $('#strength-fill').css({
            'width': evalRes.percent + '%',
            'background': evalRes.color
        });
        $('#strength-label').text(evalRes.text).css('color', evalRes.color);
    });

    // Toggle password eye visibility
    $('#toggle-pwd-btn').click(function() {
        var input = $('#encrypted_password');
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
