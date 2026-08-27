@extends('layouts.admin')

@section('title', __('messages.edit') . ' ' . __('messages.location'))

@section('content')
<style>
    .location-form-card {
        border-radius: 16px !important;
        overflow: hidden !important;
        border: var(--rule-soft);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    .location-form-card .card-header {
        border-top-left-radius: 16px !important;
        border-top-right-radius: 16px !important;
    }
    .location-card-header {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        width: 100% !important;
    }
    .location-header-title-box {
        display: flex !important;
        align-items: center !important;
        flex-shrink: 0 !important;
        gap: 12px !important;
    }
    .custom-location-input {
        height: 44px !important;
        border-radius: 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.22) !important;
        box-shadow: none !important;
        font-size: 0.9rem !important;
        color: var(--color-ink-0) !important;
        transition: all 0.2s ease !important;
    }
    .custom-location-input:focus,
    .custom-location-textarea:focus {
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2) !important;
        background: rgba(15, 23, 42, 0.35) !important;
    }
    .custom-location-textarea {
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
        .location-card-header {
            padding: 12px 14px !important;
        }
        .location-header-title-box h5 {
            font-size: 0.98rem !important;
        }
        .location-header-title-box small {
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

<div class="card theme-card location-form-card">
    <form action="{{ route('locations.update', $location) }}" method="POST">
        @csrf @method('PUT')
        <!-- Header Toolbar -->
        <div class="card-header bg-transparent border-bottom location-card-header py-3 px-3 px-md-4" style="border-color: rgba(148, 163, 184, 0.12) !important;">
            <div class="location-header-title-box">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.3); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-size: 1.1rem; flex-shrink: 0;">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <h5 class="mb-0 font-weight-bold theme-text" style="font-size: 1.08rem; line-height: 1.2;">{{ __('messages.edit') }} {{ __('messages.location') }}</h5>
                    <small class="text-muted d-block" style="font-size: 0.78rem;">Perbarui informasi lokasi {{ $location->name }}</small>
                </div>
            </div>
        </div>

        <div class="card-body p-3 p-md-4">
            <!-- Location Name Input -->
            <div class="form-group mb-4">
                <label class="form-label form-label-title theme-text">
                    {{ __('messages.name') }} <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" class="form-control theme-input custom-location-input @error('name') is-invalid @enderror" value="{{ old('name', $location->name) }}" placeholder="Masukkan nama lokasi..." required autocomplete="off">
                @error('name') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
            </div>

            <!-- Address Input -->
            <div class="form-group mb-4">
                <label class="form-label form-label-title theme-text">
                    {{ __('messages.address') }}
                </label>
                <textarea name="address" class="form-control theme-input custom-location-textarea @error('address') is-invalid @enderror" rows="3" placeholder="Tuliskan alamat lengkap lokasi...">{{ old('address', $location->address) }}</textarea>
                @error('address') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
            </div>

            <!-- Description Input -->
            <div class="form-group mb-2">
                <label class="form-label form-label-title theme-text">
                    {{ __('messages.description') }}
                </label>
                <textarea name="description" class="form-control theme-input custom-location-textarea @error('description') is-invalid @enderror" rows="3" placeholder="Tuliskan catatan atau deskripsi tambahan...">{{ old('description', $location->description) }}</textarea>
                @error('description') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="card-footer bg-transparent py-3 px-3 px-md-4 border-top d-flex align-items-center justify-content-end card-footer-actions" style="gap: 10px; border-color: rgba(148, 163, 184, 0.12) !important;">
            <a href="{{ route('locations.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">
                {{ __('messages.cancel') }}
            </a>
            <button type="submit" class="btn btn-primary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">
                {{ __('messages.update') }}
            </button>
        </div>
    </form>
</div>
@endsection