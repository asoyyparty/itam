@extends('layouts.admin')

@section('title', __('messages.add_vlan'))

@section('content')
<style>
    .vlan-form-card {
        border-radius: 16px !important;
        overflow: hidden !important;
        border: var(--rule-soft);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    .vlan-form-card .card-header {
        border-top-left-radius: 16px !important;
        border-top-right-radius: 16px !important;
    }
    .vlan-card-header {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        width: 100% !important;
    }
    .vlan-header-title-box {
        display: flex !important;
        align-items: center !important;
        flex-shrink: 0 !important;
        gap: 12px !important;
    }
    .custom-vlan-input {
        height: 44px !important;
        border-radius: 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.22) !important;
        box-shadow: none !important;
        font-size: 0.9rem !important;
        color: var(--color-ink-0) !important;
        transition: all 0.2s ease !important;
    }
    .custom-vlan-input:focus,
    .custom-vlan-textarea:focus {
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2) !important;
        background: rgba(15, 23, 42, 0.35) !important;
    }
    .custom-vlan-textarea {
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
        .vlan-card-header {
            padding: 12px 14px !important;
        }
        .vlan-header-title-box h5 {
            font-size: 0.98rem !important;
        }
        .vlan-header-title-box small {
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

<div class="card theme-card vlan-form-card">
    <form action="{{ route('vlans.store') }}" method="POST">
        @csrf
        <!-- Header Toolbar -->
        <div class="card-header bg-transparent border-bottom vlan-card-header py-3 px-3 px-md-4" style="border-color: rgba(148, 163, 184, 0.12) !important;">
            <div class="vlan-header-title-box">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(59, 130, 246, 0.12); border: 1px solid rgba(59, 130, 246, 0.3); display: flex; align-items: center; justify-content: center; color: #60a5fa; font-size: 1.1rem; flex-shrink: 0;">
                    <i class="fas fa-network-wired"></i>
                </div>
                <div>
                    <h5 class="mb-0 font-weight-bold theme-text" style="font-size: 1.08rem; line-height: 1.2;">{{ __('messages.add_vlan') }}</h5>
                    <small class="text-muted d-block" style="font-size: 0.78rem;">Tambah segmen VLAN baru untuk jaringan IT</small>
                </div>
            </div>
        </div>

        <div class="card-body p-3 p-md-4">
            <!-- Row 1: VLAN ID & Name -->
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.vlan_number') }} <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="vlan_number" class="form-control theme-input custom-vlan-input @error('vlan_number') is-invalid @enderror" value="{{ old('vlan_number') }}" required placeholder="Misal: 10, 20, 100..." autocomplete="off">
                    @error('vlan_number') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.vlan_name') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" class="form-control theme-input custom-vlan-input @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Misal: VLAN Server, VLAN Staff, VLAN Guest..." autocomplete="off">
                    @error('name') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Row 2: Subnet & Gateway -->
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">Subnet</label>
                    <input type="text" name="subnet" class="form-control theme-input custom-vlan-input @error('subnet') is-invalid @enderror" value="{{ old('subnet') }}" placeholder="192.168.10.0/24..." autocomplete="off">
                    @error('subnet') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">Gateway</label>
                    <input type="text" name="gateway" class="form-control theme-input custom-vlan-input @error('gateway') is-invalid @enderror" value="{{ old('gateway') }}" placeholder="192.168.10.254..." autocomplete="off">
                    @error('gateway') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Description Input -->
            <div class="form-group mb-2">
                <label class="form-label form-label-title theme-text">{{ __('messages.description') }}</label>
                <textarea name="description" class="form-control theme-input custom-vlan-textarea @error('description') is-invalid @enderror" rows="3" placeholder="Tuliskan deskripsi mengenai segmen VLAN ini...">{{ old('description') }}</textarea>
                @error('description') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="card-footer bg-transparent py-3 px-3 px-md-4 border-top d-flex align-items-center justify-content-end card-footer-actions" style="gap: 10px; border-color: rgba(148, 163, 184, 0.12) !important;">
            <a href="{{ route('vlans.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">
                {{ __('messages.cancel') }}
            </a>
            <button type="submit" class="btn btn-primary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">
                {{ __('messages.save') }}
            </button>
        </div>
    </form>
</div>
@endsection
