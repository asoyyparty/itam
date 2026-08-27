@extends('layouts.admin')

@section('title', __('messages.edit_vault') ?? 'Edit Vault Password')

@section('content')
<style>
    .vault-form-card {
        border-radius: 16px !important;
        overflow: hidden !important;
        border: var(--rule-soft);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        max-width: 720px;
        margin: 0 auto;
    }
    .vault-form-card .card-header {
        border-top-left-radius: 16px !important;
        border-top-right-radius: 16px !important;
    }
    .vault-card-header {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        width: 100% !important;
    }
    .vault-header-title-box {
        display: flex !important;
        align-items: center !important;
        flex-shrink: 0 !important;
        gap: 12px !important;
    }
    .custom-vault-input,
    .vault-form-card .select2-container--default .select2-selection--single {
        height: 44px !important;
        border-radius: 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.22) !important;
        box-shadow: none !important;
        font-size: 0.9rem !important;
        color: var(--color-ink-0) !important;
        transition: all 0.2s ease !important;
    }
    .vault-form-card .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 42px !important;
        padding-left: 12px !important;
        color: var(--color-ink-0) !important;
    }
    .vault-form-card .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
    }
    .custom-vault-input:focus {
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2) !important;
        background: rgba(15, 23, 42, 0.35) !important;
    }
    .vault-form-card .input-group-text {
        height: 44px !important;
        border-radius: 0 10px 10px 0 !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.25) !important;
        color: #94a3b8 !important;
    }
    .custom-vault-textarea {
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
        .vault-card-header {
            padding: 12px 14px !important;
        }
        .vault-header-title-box h5 {
            font-size: 0.98rem !important;
        }
        .vault-header-title-box small {
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

<div class="card theme-card vault-form-card">
    <form action="{{ route('password_vaults.update', array_merge([$passwordVault->id], request()->query())) }}" method="POST">
        @csrf @method('PUT')
        <!-- Header Toolbar -->
        <div class="card-header bg-transparent border-bottom vault-card-header py-3 px-3 px-md-4" style="border-color: rgba(148, 163, 184, 0.12) !important;">
            <div class="vault-header-title-box">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.3); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-size: 1.1rem; flex-shrink: 0;">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <h5 class="mb-0 font-weight-bold theme-text" style="font-size: 1.08rem; line-height: 1.2;">{{ __('messages.edit_vault') ?? 'Edit Vault Password' }}</h5>
                    <small class="text-muted d-block" style="font-size: 0.78rem;">Perbarui kredensial perangkat {{ $passwordVault->device_name }}</small>
                </div>
            </div>
        </div>

        <div class="card-body p-3 p-md-4">
            <!-- Row 1: Device Name & Username -->
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.device_name') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="device_name" class="form-control theme-input custom-vault-input @error('device_name') is-invalid @enderror" value="{{ old('device_name', $passwordVault->device_name) }}" required placeholder="Misal: CCTV NVR Lab..." autocomplete="off">
                    @error('device_name') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.username') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="username" class="form-control theme-input custom-vault-input @error('username') is-invalid @enderror" value="{{ old('username', $passwordVault->username) }}" required placeholder="Misal: admin, root..." autocomplete="off">
                    @error('username') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Row 2: Password & Category -->
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.password') }} <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="password" name="encrypted_password" id="encrypted_password" class="form-control theme-input custom-vault-input border-right-0 @error('encrypted_password') is-invalid @enderror" value="{{ old('encrypted_password', $passwordVault->encrypted_password) }}" required autocomplete="off">
                        <div class="input-group-append">
                            <button type="button" class="btn input-group-text border-left-0" id="toggle-pwd-visibility">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    @error('encrypted_password') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.category') }} <span class="text-danger">*</span>
                    </label>
                    <select name="category" class="form-control select2-tags @error('category') is-invalid @enderror" required>
                        <option value="">Pilih / Ketik Kategori Baru...</option>
                        @php
                            $defaultCategories = ['AnyDesk', 'Windows', 'Email', 'CCTV', 'Switch', 'Printer', 'SAP'];
                            $allCategories = collect($defaultCategories)->merge($categories ?? [])->push($passwordVault->category)->unique()->filter()->sort();
                        @endphp
                        @foreach($allCategories as $cat)
                            <option value="{{ $cat }}" {{ old('category', $passwordVault->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('category') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Row 3: Notes -->
            <div class="row">
                <div class="col-md-12 form-group mb-2">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.notes') }}
                    </label>
                    <textarea name="notes" class="form-control theme-input custom-vault-textarea @error('notes') is-invalid @enderror" rows="3" placeholder="Misal: info ekstensi, kode backup, instruksi khusus...">{{ old('notes', $passwordVault->notes) }}</textarea>
                    @error('notes') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="card-footer bg-transparent py-3 px-3 px-md-4 border-top d-flex align-items-center justify-content-end card-footer-actions" style="gap: 10px; border-color: rgba(148, 163, 184, 0.12) !important;">
            <a href="{{ route('password_vaults.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">
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
    $('#toggle-pwd-visibility').click(function() {
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

    $('.select2-tags').select2({
        tags: true,
        placeholder: "Pilih atau ketik untuk membuat kategori baru",
        allowClear: true,
        width: '100%'
    });
});
</script>
@endpush
@endsection
