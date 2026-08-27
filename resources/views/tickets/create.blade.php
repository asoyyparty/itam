@extends('layouts.admin')

@section('title', __('messages.create_ticket'))

@section('content')
<style>
    .ticket-form-card {
        border-radius: 16px !important;
        overflow: visible !important;
        border: var(--rule-soft);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    .ticket-form-card .card-header {
        border-top-left-radius: 16px !important;
        border-top-right-radius: 16px !important;
    }
    .ticket-card-header {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        width: 100% !important;
    }
    .ticket-header-title-box {
        display: flex !important;
        align-items: center !important;
        flex-shrink: 0 !important;
        gap: 12px !important;
    }
    .ticket-header-actions {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        flex-shrink: 0 !important;
        gap: 8px !important;
    }
    .ticket-header-actions .btn {
        white-space: nowrap !important;
    }
    .position-relative-title-wrapper {
        position: relative !important;
        width: 100% !important;
    }
    .title-template-dropdown {
        position: absolute !important;
        top: calc(100% + 4px) !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        min-width: 100% !important;
        max-width: 100% !important;
        transform: none !important;
        z-index: 1070 !important;
        background: var(--color-paper-1, #1e293b) !important;
        border: 1px solid rgba(148, 163, 184, 0.25) !important;
        border-radius: 14px !important;
        box-shadow: 0 16px 45px rgba(0, 0, 0, 0.6) !important;
        padding: 8px !important;
        margin: 0 !important;
        max-height: 280px !important;
        overflow-y: auto !important;
    }
    .title-template-dropdown .dropdown-header {
        text-align: left !important;
        font-size: 0.72rem !important;
        letter-spacing: 0.05em !important;
        color: #94a3b8 !important;
        border-bottom: 1px solid rgba(148, 163, 184, 0.12) !important;
        padding: 8px 12px 10px 12px !important;
        margin-bottom: 6px !important;
    }
    .title-template-dropdown .dropdown-item {
        display: flex !important;
        align-items: flex-start !important;
        gap: 10px !important;
        padding: 10px 14px !important;
        border-radius: 10px !important;
        font-size: 0.88rem !important;
        line-height: 1.45 !important;
        color: var(--color-ink-0, #f8f9fa) !important;
        white-space: normal !important;
        word-break: break-word !important;
        transition: all 0.18s ease !important;
    }
    .title-template-dropdown .dropdown-item:hover,
    .title-template-dropdown .dropdown-item:focus {
        background: rgba(59, 130, 246, 0.15) !important;
        color: #60a5fa !important;
        transform: translateX(2px);
    }
    .title-template-dropdown::-webkit-scrollbar {
        width: 6px;
    }
    .title-template-dropdown::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.3);
        border-radius: 4px;
    }
    .custom-ticket-input,
    .ticket-form-card .select2-container--default .select2-selection--single {
        height: 44px !important;
        border-radius: 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.22) !important;
        box-shadow: none !important;
        font-size: 0.9rem !important;
        color: var(--color-ink-0) !important;
    }
    .ticket-form-card .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 42px !important;
        padding-left: 12px !important;
        color: var(--color-ink-0) !important;
    }
    .ticket-form-card .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
    }
    .ticket-form-card .input-group-text {
        height: 44px !important;
        border-radius: 10px 0 0 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        background: rgba(15, 23, 42, 0.25) !important;
        color: #94a3b8 !important;
    }
    .ticket-form-card input.flatpickr-datetime {
        border-radius: 0 10px 10px 0 !important;
    }
    .btn-ai-sparkle {
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    .btn-ai-sparkle:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(168, 85, 247, 0.35) !important;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.35), rgba(168, 85, 247, 0.35)) !important;
        color: #ffffff !important;
    }
    .ai-analysis-panel {
        border-radius: 14px;
        border-left: 4px solid #8b5cf6 !important;
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.08), rgba(59, 130, 246, 0.05)) !important;
        border-top: 1px solid rgba(139, 92, 246, 0.2);
        border-right: 1px solid rgba(139, 92, 246, 0.2);
        border-bottom: 1px solid rgba(139, 92, 246, 0.2);
    }
    .form-label-title {
        font-size: 0.88rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        margin-bottom: 8px !important;
    }

    @media (max-width: 767.98px) {
        .ticket-card-header {
            overflow-x: auto !important;
            padding: 12px 14px !important;
        }
        .ticket-header-title-box h5 {
            font-size: 0.98rem !important;
        }
        .ticket-header-title-box small {
            font-size: 0.72rem !important;
        }
        .ticket-header-actions .btn {
            font-size: 0.78rem !important;
            padding: 6px 10px !important;
            height: 36px !important;
        }
        .ticket-form-group {
            margin-bottom: 20px !important;
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

<div class="card theme-card ticket-form-card">
    <form action="{{ route('tickets.store') }}" method="POST">
        @csrf
        <!-- Header Toolbar -->
        <div class="card-header bg-transparent border-bottom ticket-card-header py-3 px-3 px-md-4" style="border-color: rgba(148, 163, 184, 0.12) !important;">
            <div class="ticket-header-title-box">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(59, 130, 246, 0.12); border: 1px solid rgba(59, 130, 246, 0.3); display: flex; align-items: center; justify-content: center; color: #60a5fa; font-size: 1.1rem; flex-shrink: 0;">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div>
                    <h5 class="mb-0 font-weight-bold theme-text" style="font-size: 1.08rem; line-height: 1.2;">{{ __('messages.create_ticket') }}</h5>
                    <small class="text-muted d-block" style="font-size: 0.78rem;">{{ __('messages.ticket_details') }}</small>
                </div>
            </div>
            
            <div class="ticket-header-actions">
                <button type="button" id="btn-ai-analyze" class="btn btn-sm btn-ai-sparkle rounded-pill px-3" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(168, 85, 247, 0.2)); color: #a78bfa; border: 1px solid rgba(168, 85, 247, 0.4); font-weight: 600; height: 38px; display: inline-flex; align-items: center; justify-content: center;">
                    Analisis AI Assistant
                </button>
            </div>
        </div>

        <div class="card-body p-3 p-md-4">
            <!-- AI Analysis Result Box -->
            <div id="ai-analysis-result" class="card ai-analysis-panel mb-4" style="display: none;">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3" style="gap: 10px;">
                        <h6 class="m-0 font-weight-bold d-flex align-items-center" style="color: #a78bfa; font-size: 1rem; gap: 8px;">
                            Diagnosa & Rekomendasi AI Assistant
                        </h6>
                        <button type="button" id="btn-apply-ai-suggestions" class="btn btn-xs btn-success rounded-pill px-3 py-1" style="font-weight: 600;">
                            Terapkan Kategori & Prioritas AI
                        </button>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <small class="text-muted d-block uppercase text-xs font-weight-bold">Rekomendasi Kategori:</small>
                            <strong id="ai-cat-res" class="text-info" style="font-size: 0.95rem;"></strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block uppercase text-xs font-weight-bold">Rekomendasi Prioritas:</small>
                            <span id="ai-priority-badge" class="badge px-3 py-1" style="font-size: 0.8rem; border-radius: 10px;"></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block uppercase text-xs font-weight-bold mb-1">Akar Penyebab Masalah (Diagnosis):</small>
                        <p id="ai-diagnosis-res" class="theme-text mb-0 p-2 rounded" style="font-size: 0.88rem; font-style: italic; background: rgba(15, 23, 42, 0.3); border-left: 3px solid #a78bfa;"></p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block uppercase text-xs font-weight-bold mb-1">Langkah-langkah Penanganan IT Support:</small>
                        <ul id="ai-steps-res" class="pl-3 mb-0 theme-text" style="font-size: 0.85rem; line-height: 1.6;"></ul>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted uppercase text-xs font-weight-bold">Draft Balasan User:</small>
                            <button type="button" id="btn-copy-ai-reply" class="btn btn-xs btn-outline-secondary rounded-pill px-2" style="font-size: 0.72rem;">Salin Balasan</button>
                        </div>
                        <pre id="ai-reply-res" class="p-3 theme-input rounded" style="font-family: var(--font-body); font-size: 0.82rem; white-space: pre-wrap; margin-bottom: 0; background: rgba(15, 23, 42, 0.3); border: 1px solid rgba(148, 163, 184, 0.15);"></pre>
                    </div>
                </div>
            </div>

            <!-- Title / Subject with Absolute Locked Dropdown -->
            <div class="form-group ticket-form-group mb-4">
                <label class="form-label form-label-title theme-text">
                    {{ __('messages.title_subject') }} <span class="text-danger">*</span>
                </label>
                <div class="position-relative-title-wrapper">
                    <input type="text" id="ticket_title" name="title" class="form-control theme-input custom-ticket-input @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Ketik judul masalah atau pilih dari dropdown..." required style="padding-right: 44px !important;" autocomplete="off">
                    
                    <button id="btn-toggle-title-templates" class="btn btn-sm text-info p-0 d-flex align-items-center justify-content-center position-absolute" type="button" style="top: 50%; right: 12px; transform: translateY(-50%); width: 28px; height: 28px; background: transparent; border: none; z-index: 6;" title="Pilih Template Judul">
                        <i class="fas fa-chevron-down" style="font-size: 0.85rem;"></i>
                    </button>

                    <div id="title-template-menu" class="dropdown-menu title-template-dropdown shadow-lg border-0">
                        <div class="dropdown-header text-uppercase font-weight-bold">
                            TEMPLATE JUDUL:
                        </div>
                        @foreach($templates as $template)
                            <a class="dropdown-item template-title-item" href="#" data-title="{{ $template }}">
                                <span>{{ $template }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
                @error('title') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
            </div>

            <!-- Row 1: Reporter & Related Asset -->
            <div class="row">
                <div class="col-lg-6 form-group ticket-form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.reporter_employee') }} <span class="text-danger">*</span>
                    </label>
                    <select name="employee_id" class="form-control select2 @error('employee_id') is-invalid @enderror" required>
                        <option value="">{{ __('messages.select_employee') }}</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->employee_id }} - {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-lg-6 form-group ticket-form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.related_asset_optional') }}
                    </label>
                    <select name="asset_id" class="form-control select2 @error('asset_id') is-invalid @enderror">
                        <option value="">{{ __('messages.none') }}</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                {{ $asset->asset_tag }} - {{ $asset->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('asset_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Row 2: Category & Priority -->
            <div class="row">
                <div class="col-lg-6 form-group ticket-form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        Kategori Pelaporan <span class="text-danger">*</span>
                    </label>
                    <select name="category" id="category_select" class="form-control select2 @error('category') is-invalid @enderror" required>
                        <option value="">Pilih Kategori...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category['name'] }}" {{ old('category') == $category['name'] ? 'selected' : '' }}>
                                {{ $category['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <small id="category_description" class="form-text text-info mt-2"></small>
                    @error('category') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-lg-6 form-group ticket-form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        {{ __('messages.priority') }} <span class="text-danger">*</span>
                    </label>
                    <select name="priority" class="form-control select2 @error('priority') is-invalid @enderror" required>
                        <option value="Low" {{ old('priority', 'Low') == 'Low' ? 'selected' : '' }}>{{ __('messages.low') }}</option>
                        <option value="Medium" {{ old('priority') == 'Medium' ? 'selected' : '' }}>{{ __('messages.medium') }}</option>
                        <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>{{ __('messages.high') }}</option>
                        <option value="Critical" {{ old('priority') == 'Critical' ? 'selected' : '' }}>{{ __('messages.critical') }}</option>
                    </select>
                    @error('priority') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Row 3: PIC & Created At -->
            <div class="row">
                <div class="col-lg-6 form-group ticket-form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        PIC (Penanggung Jawab)
                    </label>
                    <select name="pic_id" id="pic_id" class="form-control select2 @error('pic_id') is-invalid @enderror">
                        <option value="">Belum di-assign</option>
                        @foreach($pics as $pic)
                            <option value="{{ $pic->id }}" {{ old('pic_id') == $pic->id ? 'selected' : '' }}>
                                {{ $pic->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('pic_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="col-lg-6 form-group ticket-form-group mb-4">
                    <label class="form-label form-label-title theme-text">
                        Waktu Laporan Dibuat
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                        </div>
                        <input type="text" name="created_at" class="form-control theme-input custom-ticket-input border-left-0 flatpickr-datetime" style="background: transparent;" value="{{ old('created_at', now()->format('Y-m-d\TH:i')) }}">
                    </div>
                    <small class="text-muted mt-1 d-block" style="font-size: 0.78rem;">Biarkan jika ingin menggunakan waktu saat ini.</small>
                    @error('created_at') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="form-group ticket-form-group mb-2">
                <label class="form-label form-label-title theme-text">
                    {{ __('messages.description_details') }} <span class="text-danger">*</span>
                </label>
                <textarea name="description" class="form-control theme-input @error('description') is-invalid @enderror" rows="5" placeholder="Tuliskan deskripsi lengkap mengenai masalah atau permintaan yang dialami..." style="border-radius: 12px !important; border: 1px solid rgba(148, 163, 184, 0.22); background: rgba(15, 23, 42, 0.22);" required>{{ old('description') }}</textarea>
                @error('description') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="card-footer bg-transparent py-3 px-3 px-md-4 border-top d-flex align-items-center justify-content-end card-footer-actions" style="gap: 10px; border-color: rgba(148, 163, 184, 0.12) !important;">
            <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="height: 42px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;">
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
    // Select2 Initialization with 100% width
    $('.select2').select2({ width: '100%' });

    const categoriesData = @json($categories);
    const categorySelect = document.getElementById('category_select');
    const categoryDesc = document.getElementById('category_description');

    function updateCategoryDescription() {
        if (!categorySelect) return;
        const selectedValue = categorySelect.value;
        const category = categoriesData.find(c => c.name === selectedValue);
        if (category) {
            categoryDesc.innerHTML = '<strong>Deskripsi Tugas:</strong> ' + category.description;
        } else {
            categoryDesc.innerHTML = '';
        }
    }

    if(categorySelect) {
        $(categorySelect).on('select2:select change', updateCategoryDescription);
        updateCategoryDescription();
    }

    // Toggle Title Template Menu manually to bypass Popper.js displacement
    $('#ticket_title, #btn-toggle-title-templates').on('click', function(e) {
        e.stopPropagation();
        $('#title-template-menu').toggleClass('show');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.position-relative-title-wrapper').length) {
            $('#title-template-menu').removeClass('show');
        }
    });

    // Template Title Selection
    $(document).on('click', '.template-title-item', function(e) {
        e.preventDefault();
        const title = $(this).attr('data-title');
        $('#ticket_title').val(title).focus();
        $('#title-template-menu').removeClass('show');
    });

    // AI Analysis Handler
    let latestAiResult = null;
    $('#btn-ai-analyze').click(function() {
        const title = $('#ticket_title').val();
        const description = $('textarea[name="description"]').val();
        const employeeId = $('select[name="employee_id"]').val();
        const assetId = $('select[name="asset_id"]').val();

        if (!title && !description) {
            Swal.fire({
                icon: 'warning',
                title: 'Judul atau Deskripsi Kosong',
                text: 'Harap isi judul atau deskripsi tiket sebelum meminta analisis AI.',
                background: 'rgba(30, 41, 59, 0.95)',
                color: '#f8f9fa'
            });
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true).text('Menganalisis AI...');

        $.ajax({
            url: "{{ route('ai.analyze-ticket') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                title: title,
                description: description,
                employee_id: employeeId,
                asset_id: assetId
            },
            success: function(response) {
                btn.prop('disabled', false).text('Analisis AI Assistant');
                if (response.success && response.data) {
                    latestAiResult = response.data;
                    const res = response.data;

                    $('#ai-cat-res').text(res.suggested_category || 'Hardware & Software Support');
                    
                    const badgeClass = res.suggested_priority === 'Critical' ? 'badge-danger' : 
                                      (res.suggested_priority === 'High' ? 'badge-warning' : 
                                      (res.suggested_priority === 'Medium' ? 'badge-info' : 'badge-secondary'));
                    $('#ai-priority-badge').attr('class', 'badge ' + badgeClass).text(res.suggested_priority || 'Medium');

                    $('#ai-diagnosis-res').text('"' + res.diagnosis + '"');

                    let stepsHtml = '';
                    if (res.resolution_steps && Array.isArray(res.resolution_steps)) {
                        res.resolution_steps.forEach(step => {
                            stepsHtml += '<li class="mb-1">' + step + '</li>';
                        });
                    }
                    $('#ai-steps-res').html(stepsHtml);

                    $('#ai-reply-res').text(res.reply_draft || '');
                    $('#ai-analysis-result').slideDown();

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        icon: 'success',
                        title: 'Analisis AI selesai!',
                        background: 'rgba(30, 41, 59, 0.95)',
                        color: '#f8f9fa'
                    });
                }
            },
            error: function(err) {
                btn.prop('disabled', false).text('Analisis AI Assistant');
                console.error(err);
            }
        });
    });

    // Apply AI Category & Priority
    $('#btn-apply-ai-suggestions').click(function() {
        if (!latestAiResult) return;
        
        if (latestAiResult.suggested_category) {
            $('#category_select').val(latestAiResult.suggested_category).trigger('change');
        }
        if (latestAiResult.suggested_priority) {
            $('select[name="priority"]').val(latestAiResult.suggested_priority).trigger('change');
        }

        Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500,
            icon: 'success',
            title: 'Kategori & Prioritas AI berhasil diterapkan!',
            background: 'rgba(30, 41, 59, 0.95)',
            color: '#f8f9fa'
        });
    });

    // Copy Reply Draft
    $('#btn-copy-ai-reply').click(function() {
        const text = $('#ai-reply-res').text();
        if (!text) return;
        copyToClipboard(text).then(function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    icon: 'success',
                    title: 'Draft balasan disalin!',
                    background: 'rgba(30, 41, 59, 0.95)',
                    color: '#f8f9fa'
                });
            }
        }).catch(function(err) {
            console.error('Could not copy reply draft: ', err);
        });
    });
});
</script>
@endpush
@endsection
