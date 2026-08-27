@extends('layouts.admin')

@section('title', __('messages.edit_ip'))

@section('content')
<style>
    .ip-form-card {
        border-radius: 16px !important;
        overflow: hidden !important;
        border: var(--rule-soft);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    .ip-form-card .card-header {
        border-top-left-radius: 16px !important;
        border-top-right-radius: 16px !important;
    }
    .ip-card-header {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        width: 100% !important;
    }
    .ip-header-title-box {
        display: flex !important;
        align-items: center !important;
        flex-shrink: 0 !important;
        gap: 12px !important;
    }
    .custom-ip-input,
    .ip-form-card .select2-container--default .select2-selection--single {
        height: 44px !important;
        border-radius: 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.22) !important;
        box-shadow: none !important;
        font-size: 0.9rem !important;
        color: var(--color-ink-0) !important;
        transition: all 0.2s ease !important;
    }
    .ip-form-card .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 42px !important;
        padding-left: 12px !important;
        color: var(--color-ink-0) !important;
    }
    .ip-form-card .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
    }
    .custom-ip-input:focus {
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2) !important;
        background: rgba(15, 23, 42, 0.35) !important;
    }
    .custom-ip-textarea {
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
        .ip-card-header {
            padding: 12px 14px !important;
        }
        .ip-header-title-box h5 {
            font-size: 0.98rem !important;
        }
        .ip-header-title-box small {
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

<div class="card theme-card ip-form-card">
    <form action="{{ route('ips.update', array_merge([$ip->id], request()->query())) }}" method="POST">
        @csrf @method('PUT')
        <!-- Header Toolbar -->
        <div class="card-header bg-transparent border-bottom ip-card-header py-3 px-3 px-md-4" style="border-color: rgba(148, 163, 184, 0.12) !important;">
            <div class="ip-header-title-box">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.3); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-size: 1.1rem; flex-shrink: 0;">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <h5 class="mb-0 font-weight-bold theme-text" style="font-size: 1.08rem; line-height: 1.2;">{{ __('messages.edit_ip') }}</h5>
                    <small class="text-muted d-block" style="font-size: 0.78rem;">Perbarui pengalokasian IP Address {{ $ip->ip_address }}</small>
                </div>
            </div>
        </div>

        <div class="card-body p-3 p-md-4">
            <!-- Row 1: IP Address & Status -->
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.ip_address') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="ip_address" class="form-control theme-input custom-ip-input @error('ip_address') is-invalid @enderror" value="{{ old('ip_address', $ip->ip_address) }}" required placeholder="Contoh: 192.168.1.100..." autocomplete="off">
                    @error('ip_address') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.status') }} <span class="text-danger">*</span>
                    </label>
                    <select name="status" class="form-control select2 @error('status') is-invalid @enderror" required>
                        <option value="Available" {{ old('status', $ip->status) == 'Available' ? 'selected' : '' }}>{{ __('messages.available') }}</option>
                        <option value="Used" {{ old('status', $ip->status) == 'Used' ? 'selected' : '' }}>{{ __('messages.used') }}</option>
                        <option value="Reserved" {{ old('status', $ip->status) == 'Reserved' ? 'selected' : '' }}>{{ __('messages.reserved') }}</option>
                    </select>
                    @error('status') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Row 2: VLAN, Gateway & DNS -->
            <div class="row">
                <div class="col-md-4 form-group mb-4">
                    <label class="form-label form-label-title theme-text">VLAN</label>
                    <select name="vlan_id" id="vlan_select" class="form-control select2 @error('vlan_id') is-invalid @enderror">
                        <option value="" data-vlan-number="" data-gateway="">None / Standard</option>
                        @foreach($vlans as $vlan)
                            <option value="{{ $vlan->id }}" data-vlan-number="{{ $vlan->vlan_number }}" data-gateway="{{ $vlan->gateway ?: '192.168.'.$vlan->vlan_number.'.254' }}" {{ old('vlan_id', $ip->vlan_id) == $vlan->id ? 'selected' : '' }}>
                                VLAN {{ $vlan->vlan_number }} - {{ $vlan->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('vlan_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-4 form-group mb-4">
                    <label class="form-label form-label-title theme-text">Gateway</label>
                    <input type="text" name="gateway" id="gateway_input" class="form-control theme-input custom-ip-input @error('gateway') is-invalid @enderror" value="{{ old('gateway', $ip->gateway) }}" placeholder="192.168.1.254..." autocomplete="off">
                    @error('gateway') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-4 form-group mb-4">
                    <label class="form-label form-label-title theme-text">DNS Server</label>
                    <input type="text" name="dns" class="form-control theme-input custom-ip-input @error('dns') is-invalid @enderror" value="{{ old('dns', $ip->dns) }}" placeholder="8.8.8.8, 1.1.1.1..." autocomplete="off">
                    @error('dns') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Row 3: Assigned Employee & Asset -->
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">{{ __('messages.assigned_employee') }} ({{ __('messages.optional') }})</label>
                    <select name="employee_id" class="form-control select2 @error('employee_id') is-invalid @enderror">
                        <option value="">{{ __('messages.none') }}</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id', $ip->employee_id) == $employee->id ? 'selected' : '' }}>
                                {{ $employee->employee_id }} - {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6 form-group mb-4">
                    <label class="form-label form-label-title theme-text">{{ __('messages.assigned_asset') }} ({{ __('messages.optional') }})</label>
                    <select name="asset_id" class="form-control select2 @error('asset_id') is-invalid @enderror">
                        <option value="">{{ __('messages.none') }}</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}" {{ old('asset_id', $ip->asset_id) == $asset->id ? 'selected' : '' }}>
                                {{ $asset->asset_tag }} - {{ $asset->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('asset_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Row 4: Notes -->
            <div class="row">
                <div class="col-md-12 form-group mb-2">
                    <label class="form-label form-label-title theme-text">{{ __('messages.notes') }}</label>
                    <textarea name="notes" class="form-control theme-input custom-ip-textarea @error('notes') is-invalid @enderror" rows="3" placeholder="Tuliskan catatan alokasi IP...">{{ old('notes', $ip->notes) }}</textarea>
                    @error('notes') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="card-footer bg-transparent py-3 px-3 px-md-4 border-top d-flex align-items-center justify-content-end card-footer-actions" style="gap: 10px; border-color: rgba(148, 163, 184, 0.12) !important;">
            <a href="{{ route('ips.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">
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

        $('#vlan_select').on('change', function() {
            var selected = $(this).find(':selected');
            var gw = selected.data('gateway');
            if (gw) {
                $('#gateway_input').val(gw);
            }
        });

        $('input[name="ip_address"]').on('blur change', function() {
            var ip = $(this).val().trim();
            if (ip && !$('#gateway_input').val()) {
                var parts = ip.split('.');
                if (parts.length === 4) {
                    $('#gateway_input').val(parts[0] + '.' + parts[1] + '.' + parts[2] + '.254');
                }
            }
        });
    });
</script>
@endpush
@endsection
