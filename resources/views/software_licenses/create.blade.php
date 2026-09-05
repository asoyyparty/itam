@extends('layouts.admin')

@section('title', __('messages.add_license') ?? 'Add Software License')

@section('content')
<style>
    .license-form-card {
        border-radius: 16px !important;
        overflow: hidden !important;
        border: var(--rule-soft);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        max-width: 720px;
        margin: 1rem auto 0;
    }
    .license-form-card .card-header {
        border-top-left-radius: 16px !important;
        border-top-right-radius: 16px !important;
    }
    .license-card-header {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        width: 100% !important;
    }
    .license-header-title-box {
        display: flex !important;
        align-items: center !important;
        flex-shrink: 0 !important;
        gap: 12px !important;
    }
    .custom-license-input,
    .license-form-card .select2-container--default .select2-selection--single {
        height: 44px !important;
        border-radius: 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.22) !important;
        box-shadow: none !important;
        font-size: 0.9rem !important;
        color: var(--color-ink-0) !important;
        transition: all 0.2s ease !important;
    }
    .license-form-card .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 42px !important;
        padding-left: 12px !important;
        color: var(--color-ink-0) !important;
    }
    .license-form-card .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
    }
    .custom-license-input:focus {
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2) !important;
        background: rgba(15, 23, 42, 0.35) !important;
    }
    .custom-license-textarea {
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
        .license-card-header {
            padding: 12px 14px !important;
        }
        .license-header-title-box h5 {
            font-size: 0.98rem !important;
        }
        .license-header-title-box small {
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

<div class="card theme-card license-form-card">
    <form action="{{ route('software_licenses.store') }}" method="POST">
        @csrf
        <!-- Header Toolbar -->
        <div class="card-header bg-transparent border-bottom license-card-header py-3 px-3 px-md-4" style="border-color: rgba(148, 163, 184, 0.12) !important;">
            <div class="license-header-title-box">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(59, 130, 246, 0.12); border: 1px solid rgba(59, 130, 246, 0.3); display: flex; align-items: center; justify-content: center; color: #60a5fa; font-size: 1.1rem; flex-shrink: 0;">
                    <i class="fas fa-key"></i>
                </div>
                <div>
                    <h5 class="mb-0 font-weight-bold theme-text" style="font-size: 1.08rem; line-height: 1.2;">{{ __('messages.add_license') ?? 'Add Software License' }}</h5>
                    <small class="text-muted d-block" style="font-size: 0.78rem;">{{ __('messages.add_license_subtitle') }}</small>
                </div>
            </div>
        </div>

        <div class="card-body p-3 p-md-4">
            <!-- Software Name Input -->
            <div class="form-group mb-4">
                <label class="form-label form-label-title theme-text">
                    {{ __('messages.name') }} <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" class="form-control theme-input custom-license-input @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Microsoft Office 2021, Windows 11 Pro, Adobe CC..." required autocomplete="off">
                @error('name') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
            </div>

            <!-- License Key Textarea -->
            <div class="form-group mb-4">
                <label class="form-label form-label-title theme-text">
                    {{ __('messages.license_key') }}
                </label>
                <textarea name="license_key" class="form-control theme-input custom-license-textarea @error('license_key') is-invalid @enderror" rows="3" placeholder="XXXXX-XXXXX-XXXXX-XXXXX-XXXXX...">{{ old('license_key') }}</textarea>
                @error('license_key') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
            </div>

            <!-- Row 1: Expiry Date & Total Seats -->
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.expiry_date') }}
                    </label>
                    <input type="date" name="expiry_date" class="form-control theme-input custom-license-input @error('expiry_date') is-invalid @enderror" value="{{ old('expiry_date') }}">
                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">{{ __('messages.leave_empty_lifetime') ?? 'Kosongkan untuk lisensi seumur hidup (lifetime)' }}</small>
                    @error('expiry_date') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.total_seats') }} <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="total_seats" class="form-control theme-input custom-license-input @error('total_seats') is-invalid @enderror" value="{{ old('total_seats', 1) }}" min="1" required autocomplete="off">
                    @error('total_seats') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- PIC Select -->
            <div class="form-group mb-4">
                <label class="form-label form-label-title theme-text">
                    {{ __('messages.pic') }}
                </label>
                <select name="pic_id" class="form-control select2 @error('pic_id') is-invalid @enderror">
                    <option value="">{{ __('messages.select') }} {{ __('messages.pic') }}...</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ old('pic_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }} ({{ $emp->employee_id }})</option>
                    @endforeach
                </select>
                @error('pic_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
            </div>

            <!-- Notes Input -->
            <div class="form-group mb-2">
                <label class="form-label form-label-title theme-text">
                    {{ __('messages.notes') }}
                </label>
                <textarea name="notes" class="form-control theme-input custom-license-textarea @error('notes') is-invalid @enderror" rows="3" placeholder="Tuliskan catatan lisensi tambahan...">{{ old('notes') }}</textarea>
                @error('notes') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="card-footer bg-transparent py-3 px-3 px-md-4 border-top d-flex align-items-center justify-content-end card-footer-actions" style="gap: 10px; border-color: rgba(148, 163, 184, 0.12) !important;">
            <a href="{{ route('software_licenses.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">
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
    $(document).ready(function() {
        $('.select2').select2({ width: '100%' });
    });
</script>
@endpush
@endsection
