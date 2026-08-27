@extends('layouts.admin')

@section('title', __('messages.pic_data'))

@section('content')
<style>
    .pic-card {
        border-radius: 16px !important;
        overflow: hidden !important;
        border: var(--rule-soft);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    .custom-modal-input {
        height: 44px !important;
        border-radius: 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.22) !important;
        box-shadow: none !important;
        font-size: 0.9rem !important;
        color: var(--color-ink-0) !important;
        transition: all 0.2s ease !important;
    }
    .custom-modal-input:focus {
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2) !important;
        background: rgba(15, 23, 42, 0.35) !important;
    }
    .modal-content {
        border-radius: 16px !important;
        overflow: hidden !important;
        background: var(--color-paper-0) !important;
        border: var(--rule-soft) !important;
    }
    .form-label-title {
        font-size: 0.88rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        margin-bottom: 8px !important;
    }

    @media (max-width: 767.98px) {
        .pic-toolbar-box {
            width: 100%;
        }
        .pic-toolbar-box .btn {
            width: 100% !important;
            justify-content: center !important;
            height: 44px !important;
        }
        .modal-footer-actions {
            flex-direction: column-reverse;
            width: 100%;
            gap: 10px !important;
        }
        .modal-footer-actions .btn {
            width: 100% !important;
            justify-content: center !important;
            height: 44px !important;
        }
    }
</style>

<!-- Toolbar Header -->
<div class="row mb-3 pic-toolbar-box">
    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center" style="gap: 10px;">
            <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(59, 130, 246, 0.12); border: 1px solid rgba(59, 130, 246, 0.3); display: flex; align-items: center; justify-content: center; color: #60a5fa; font-size: 1.1rem; flex-shrink: 0;">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <h5 class="mb-0 font-weight-bold theme-text" style="font-size: 1.08rem; line-height: 1.2;">{{ __('messages.pic_data') }}</h5>
                <small class="text-muted d-block" style="font-size: 0.78rem;">Kelola Person In Charge (PIC) penanggung jawab sistem IT</small>
            </div>
        </div>
        <button class="btn btn-primary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;" data-toggle="modal" data-target="#addPicModal">
            {{ __('messages.add_pic') }}
        </button>
    </div>
</div>

<!-- Main Table Card -->
<div class="card theme-card pic-card">
    <div class="card-body p-0">
        <div class="theme-scroll-container table-responsive">
            <table class="table table-striped table-hover m-0 theme-table division-table">
                <thead>
                    <tr>
                        <th width="60" class="pl-4">{{ __('messages.no') }}</th>
                        <th>{{ __('messages.pic_name') }}</th>
                        <th width="150" class="text-center pr-4">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pics as $pic)
                    <tr>
                        <td class="theme-text pl-4">{{ $loop->iteration }}</td>
                        <td class="font-weight-bold theme-text">{{ $pic->name }}</td>
                        <td class="theme-text pr-4">
                            <div class="d-flex justify-content-center" style="gap: 8px;">
                                <button type="button" class="btn action-btn btn-outline-info" style="border: 1px solid rgba(23, 162, 184, 0.3); background: rgba(23, 162, 184, 0.15); color: #17a2b8;" title="{{ __('messages.edit') }}" data-toggle="modal" data-target="#editPicModal-{{ $pic->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @can('action_manage_master_data')
                                <form action="{{ route('pics.destroy', $pic->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-delete action-btn btn-outline-danger" style="border: 1px solid rgba(220, 53, 69, 0.3); background: rgba(220, 53, 69, 0.15); color: #dc3545;" title="{{ __('messages.delete') }}" data-confirm-message="{{ __('messages.confirm_delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">{{ __('messages.no_data') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah PIC -->
<div class="modal fade theme-modal" id="addPicModal" tabindex="-1" role="dialog" aria-labelledby="addPicModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('pics.store') }}" method="POST" style="width: 100%;">
            @csrf
            <div class="modal-content">
                <div class="modal-header py-3 px-4" style="border-color: rgba(148, 163, 184, 0.12) !important;">
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(59, 130, 246, 0.12); border: 1px solid rgba(59, 130, 246, 0.3); display: flex; align-items: center; justify-content: center; color: #60a5fa; font-size: 1rem;">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h5 class="modal-title font-weight-bold theme-text" id="addPicModalLabel" style="font-size: 1.05rem;">{{ __('messages.add_pic') }}</h5>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-0">
                        <label class="form-label form-label-title theme-text">{{ __('messages.pic_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control theme-input custom-modal-input" required placeholder="{{ __('messages.enter_pic_name') }}" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4 d-flex align-items-center justify-content-end modal-footer-actions" style="border-color: rgba(148, 163, 184, 0.12) !important; gap: 10px;">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;" data-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">{{ __('messages.save') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@foreach($pics as $pic)
<!-- Modal Edit PIC -->
<div class="modal fade theme-modal" id="editPicModal-{{ $pic->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('pics.update', $pic->id) }}" method="POST" style="width: 100%;">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header py-3 px-4" style="border-color: rgba(148, 163, 184, 0.12) !important;">
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.3); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-size: 1rem;">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <h5 class="modal-title font-weight-bold theme-text" style="font-size: 1.05rem;">{{ __('messages.edit_pic') }}</h5>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-0">
                        <label class="form-label form-label-title theme-text">{{ __('messages.pic_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control theme-input custom-modal-input" value="{{ $pic->name }}" required placeholder="{{ __('messages.enter_pic_name') }}" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4 d-flex align-items-center justify-content-end modal-footer-actions" style="border-color: rgba(148, 163, 184, 0.12) !important; gap: 10px;">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;" data-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">{{ __('messages.save') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection
