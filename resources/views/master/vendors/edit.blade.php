@extends('layouts.admin')

@section('title', __('messages.edit') . ' ' . __('messages.vendor'))

@section('content')
<style>
    .vendor-form-card {
        border-radius: 16px !important;
        overflow: hidden !important;
        border: var(--rule-soft);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    .vendor-form-card .card-header {
        border-top-left-radius: 16px !important;
        border-top-right-radius: 16px !important;
    }
    .vendor-card-header {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        width: 100% !important;
    }
    .vendor-header-title-box {
        display: flex !important;
        align-items: center !important;
        flex-shrink: 0 !important;
        gap: 12px !important;
    }
    .custom-vendor-input {
        height: 44px !important;
        border-radius: 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.22) !important;
        box-shadow: none !important;
        font-size: 0.9rem !important;
        color: var(--color-ink-0) !important;
        transition: all 0.2s ease !important;
    }
    .custom-vendor-input:focus,
    .custom-vendor-textarea:focus {
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2) !important;
        background: rgba(15, 23, 42, 0.35) !important;
    }
    .custom-vendor-textarea {
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
        .vendor-card-header {
            padding: 12px 14px !important;
        }
        .vendor-header-title-box h5 {
            font-size: 0.98rem !important;
        }
        .vendor-header-title-box small {
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

<div class="card theme-card vendor-form-card">
    <form action="{{ route('vendors.update', $vendor) }}" method="POST">
        @csrf @method('PUT')
        <!-- Header Toolbar -->
        <div class="card-header bg-transparent border-bottom vendor-card-header py-3 px-3 px-md-4" style="border-color: rgba(148, 163, 184, 0.12) !important;">
            <div class="vendor-header-title-box">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.3); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-size: 1.1rem; flex-shrink: 0;">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <h5 class="mb-0 font-weight-bold theme-text" style="font-size: 1.08rem; line-height: 1.2;">{{ __('messages.edit') }} {{ __('messages.vendor') }}</h5>
                    <small class="text-muted d-block" style="font-size: 0.78rem;">Perbarui informasi vendor {{ $vendor->name }}</small>
                </div>
            </div>
        </div>

        <div class="card-body p-3 p-md-4">
            <!-- Vendor Name Input -->
            <div class="form-group mb-4">
                <label class="form-label form-label-title theme-text">
                    {{ __('messages.name') }} <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" class="form-control theme-input custom-vendor-input @error('name') is-invalid @enderror" value="{{ old('name', $vendor->name) }}" placeholder="Masukkan nama perusahaan vendor..." required autocomplete="off">
                @error('name') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
            </div>

            <!-- Row 1: Contact Person & Phone -->
            <div class="row">
                <div class="col-lg-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.contact_person') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="contact_person" class="form-control theme-input custom-vendor-input @error('contact_person') is-invalid @enderror" value="{{ old('contact_person', $vendor->contact_person) }}" placeholder="Nama penanggung jawab / sales..." required autocomplete="off">
                    @error('contact_person') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-lg-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.phone') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="phone" class="form-control theme-input custom-vendor-input @error('phone') is-invalid @enderror" value="{{ old('phone', $vendor->phone) }}" placeholder="Nomor telepon / WhatsApp..." required autocomplete="off">
                    @error('phone') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Email Input -->
            <div class="form-group mb-4">
                <label class="form-label form-label-title theme-text">
                    {{ __('messages.email') }} <span class="text-danger">*</span>
                </label>
                <input type="email" name="email" class="form-control theme-input custom-vendor-input @error('email') is-invalid @enderror" value="{{ old('email', $vendor->email) }}" placeholder="Alamat email resmi vendor..." required autocomplete="off">
                @error('email') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
            </div>

            <!-- Address Input -->
            <div class="form-group mb-2">
                <label class="form-label form-label-title theme-text">
                    {{ __('messages.address') }}
                </label>
                <textarea name="address" class="form-control theme-input custom-vendor-textarea @error('address') is-invalid @enderror" rows="3" placeholder="Tuliskan alamat kantor vendor...">{{ old('address', $vendor->address) }}</textarea>
                @error('address') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="card-footer bg-transparent py-3 px-3 px-md-4 border-top d-flex align-items-center justify-content-end card-footer-actions" style="gap: 10px; border-color: rgba(148, 163, 184, 0.12) !important;">
            <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">
                {{ __('messages.cancel') }}
            </a>
            <button type="submit" class="btn btn-primary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">
                {{ __('messages.update') }}
            </button>
        </div>
    </form>
</div>
@endsection