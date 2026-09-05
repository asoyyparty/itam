@extends('layouts.admin')

@section('title', __('messages.add') . ' ' . __('messages.department'))

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

    .custom-master-input {
        height: 46px !important;
        border-radius: 12px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.3) !important;
        box-shadow: none !important;
        font-size: 0.92rem !important;
        color: var(--color-ink-0) !important;
        transition: all 0.2s ease !important;
    }
    body.light-mode .custom-master-input {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
    }
    .custom-master-input:focus,
    .custom-master-textarea:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
        background: rgba(15, 23, 42, 0.45) !important;
    }
    body.light-mode .custom-master-input:focus,
    body.light-mode .custom-master-textarea:focus {
        background: #ffffff !important;
    }

    .custom-master-textarea {
        border-radius: 14px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.3) !important;
        font-size: 0.92rem !important;
        color: var(--color-ink-0) !important;
        transition: all 0.2s ease !important;
    }
    body.light-mode .custom-master-textarea {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
    }

    .form-label-title {
        font-size: 0.88rem;
        font-weight: 600;
        letter-spacing: 0.01em;
        margin-bottom: 8px !important;
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
        <div style="width: 44px; height: 44px; border-radius: 14px; background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(79, 70, 229, 0.08)); border: 1px solid rgba(99, 102, 241, 0.35); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 1.2rem; flex-shrink: 0; box-shadow: 0 0 14px rgba(99, 102, 241, 0.25);">
            <i class="fas fa-building"></i>
        </div>
        <div>
            <h5 class="mb-0 font-weight-bold theme-text" style="font-size: 1.15rem; line-height: 1.2;">{{ __('messages.add') }} {{ __('messages.department') }}</h5>
            <small class="text-muted d-block" style="font-size: 0.8rem; margin-top: 2px;">{{ __('messages.add_department_subtitle') }}</small>
        </div>
    </div>
</div>

<div class="card theme-card master-form-card">
    <form action="{{ route('departments.store') }}" method="POST">
        @csrf
        <div class="card-body p-3 p-md-4">
            <!-- Division Name Input -->
            <div class="form-group mb-4">
                <label class="form-label form-label-title theme-text">
                    <i class="fas fa-sitemap mr-1 text-primary"></i> {{ __('messages.name') }} <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" class="form-control theme-input custom-master-input @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="{{ __('messages.enter_dept_name_placeholder') }}" required autocomplete="off">
                @error('name') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
            </div>

            <!-- Division Description Input -->
            <div class="form-group mb-2">
                <label class="form-label form-label-title theme-text">
                    <i class="fas fa-align-left mr-1 text-info"></i> {{ __('messages.description') }}
                </label>
                <textarea name="description" class="form-control theme-input custom-master-textarea @error('description') is-invalid @enderror" rows="4" placeholder="{{ __('messages.enter_dept_desc_placeholder') }}">{{ old('description') }}</textarea>
                @error('description') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="card-footer bg-transparent py-3 px-3 px-md-4 border-top d-flex align-items-center justify-content-end card-footer-actions" style="gap: 10px; border-color: rgba(148, 163, 184, 0.12) !important;">
            <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">
                {{ __('messages.cancel') }}
            </a>
            <button type="submit" class="btn btn-primary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #4f46e5, #6366f1); border: none;">
                <i class="fas fa-save mr-1"></i> {{ __('messages.save') }}
            </button>
        </div>
    </form>
</div>
@endsection