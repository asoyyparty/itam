@extends('layouts.admin')

@section('title', __('messages.create_ticket'))

@section('content')
<div class="card theme-card">
    <form action="{{ route('tickets.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-info m-0"><i class="fas fa-ticket-alt"></i> {{ __('messages.ticket_details') }}</h5>
                <button type="button" id="btn-ai-analyze" class="btn btn-sm" style="background: var(--color-accent-tint); color: var(--color-accent); border: 1px solid var(--color-accent-soft); font-weight: 600;">
                    <i class="fas fa-magic mr-1"></i> Analisis AI Assistant
                </button>
            </div>

            <!-- AI Analysis Result Box -->
            <div id="ai-analysis-result" class="card theme-card mb-3" style="display: none; border-left: 4px solid var(--color-accent) !important; background: color-mix(in oklch, var(--color-accent-tint) 30%, var(--color-paper-0)) !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="m-0 font-weight-bold" style="color: var(--color-accent);"><i class="fas fa-robot mr-2"></i> Diagnosa & Rekomendasi AI Assistant</h6>
                        <button type="button" id="btn-apply-ai-suggestions" class="btn btn-xs btn-success px-3">
                            <i class="fas fa-check-circle mr-1"></i> Terapkan Kategori & Prioritas AI
                        </button>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Rekomendasi Kategori:</small>
                            <strong id="ai-cat-res" class="text-info"></strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Rekomendasi Prioritas:</small>
                            <span id="ai-priority-badge" class="badge"></span>
                        </div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Akar Penyebab Masalah (Diagnosis):</small>
                        <p id="ai-diagnosis-res" class="theme-text mb-1" style="font-size: 0.875rem; font-style: italic;"></p>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Langkah-langkah Penanganan IT Support:</small>
                        <ul id="ai-steps-res" class="pl-3 mb-1 theme-text" style="font-size: 0.8125rem;"></ul>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">Draft Balasan User:</small>
                            <button type="button" id="btn-copy-ai-reply" class="btn btn-xs btn-outline-secondary" style="padding: 1px 6px; font-size: 0.7rem;"><i class="fas fa-copy"></i> Salin Balasan</button>
                        </div>
                        <pre id="ai-reply-res" class="p-2 theme-input rounded" style="font-family: var(--font-body); font-size: 0.8rem; white-space: pre-wrap; margin-bottom: 0;"></pre>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <label class="theme-text d-flex justify-content-between align-items-center w-100" style="margin-bottom: 0.5rem;">
                        <span>{{ __('messages.title_subject') }} *</span>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-info dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false" style="padding: 2px 8px; font-size: 0.8rem;">
                                <i class="fas fa-list"></i> Template
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow" style="max-height: 250px; overflow-y: auto;">
                                @foreach($templates as $template)
                                    <a class="dropdown-item template-title-item" href="#" data-title="{{ $template }}"><small>{{ $template }}</small></a>
                                @endforeach
                            </div>
                        </div>
                    </label>
                    <input type="text" id="ticket_title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required >
                    @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label  class="theme-text">{{ __('messages.reporter_employee') }} *</label>
                    <select name="employee_id" class="form-control select2 @error('employee_id') is-invalid @enderror" required >
                        <option value="" >{{ __('messages.select_employee') }}</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}"  {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->employee_id }} - {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6 form-group">
                    <label  class="theme-text">{{ __('messages.related_asset_optional') }}</label>
                    <select name="asset_id" class="form-control select2 @error('asset_id') is-invalid @enderror" >
                        <option value="" >{{ __('messages.none') }}</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}"  {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                {{ $asset->asset_tag }} - {{ $asset->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('asset_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label  class="theme-text">Kategori Pelaporan *</label>
                    <select name="category" id="category_select" class="form-control select2 @error('category') is-invalid @enderror" required >
                        <option value="">Pilih Kategori...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category['name'] }}" {{ old('category') == $category['name'] ? 'selected' : '' }}>
                                {{ $category['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <small id="category_description" class="form-text text-info mt-2"></small>
                    @error('category') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6 form-group">
                    <label  class="theme-text">{{ __('messages.priority') }} *</label>
                    <select name="priority" class="form-control @error('priority') is-invalid @enderror" required >
                        <option value="Low"  {{ old('priority') == 'Low' ? 'selected' : '' }}>{{ __('messages.low') }}</option>
                        <option value="Medium"  {{ old('priority') == 'Medium' ? 'selected' : '' }}>{{ __('messages.medium') }}</option>
                        <option value="High"  {{ old('priority') == 'High' ? 'selected' : '' }}>{{ __('messages.high') }}</option>
                        <option value="Critical"  {{ old('priority') == 'Critical' ? 'selected' : '' }}>{{ __('messages.critical') }}</option>
                    </select>
                    @error('priority') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label  class="theme-text">PIC (Penanggung Jawab)</label>
                    <select name="pic_id" id="pic_id" class="form-control select2 w-100 theme-input">
                        <option value="">Belum di-assign</option>
                        @foreach($pics as $pic)
                            <option value="{{ $pic->id }}" {{ old('pic_id') == $pic->id ? 'selected' : '' }}>
                                {{ $pic->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('pic_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6 form-group">
                    <label class="theme-text">Waktu Laporan Dibuat</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-transparent border-right-0"><i class="far fa-calendar-alt"></i></span>
                        </div>
                        <input type="text" name="created_at" class="form-control theme-input border-left-0 flatpickr-datetime" style="background: transparent;" value="{{ old('created_at', now()->format('Y-m-d\TH:i')) }}">
                    </div>
                    <small class="text-muted">Biarkan jika ingin menggunakan waktu saat ini.</small>
                    @error('created_at') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <label  class="theme-text">{{ __('messages.description_details') }} *</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5" required >{{ old('description') }}</textarea>
                    @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        <div class="card-footer bg-transparent border-0">
            <button type="submit" class="btn btn-primary" ><i class="fas fa-save"></i> {{ __('messages.save') }}</button>
            <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary ml-2" >{{ __('messages.cancel') }}</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const categoriesData = @json($categories);
    const categorySelect = document.getElementById('category_select');
    const categoryDesc = document.getElementById('category_description');

    function updateCategoryDescription() {
        const selectedValue = categorySelect.value;
        const category = categoriesData.find(c => c.name === selectedValue);
        if (category) {
            categoryDesc.innerHTML = '<strong>Deskripsi Tugas:</strong> ' + category.description;
        } else {
            categoryDesc.innerHTML = '';
        }
    }

    // Trigger on change
    if(categorySelect) {
        // If select2 is used, we need to listen to its event
        $(categorySelect).on('select2:select', updateCategoryDescription);
        $(categorySelect).on('change', updateCategoryDescription);
        // Initial load
        updateCategoryDescription();
    }

    // Template Title Selection
    document.querySelectorAll('.template-title-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('ticket_title').value = this.getAttribute('data-title');
        });
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
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menganalisis AI...');

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
                btn.prop('disabled', false).html('<i class="fas fa-magic mr-1"></i> Analisis AI Assistant');
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
                            stepsHtml += '<li>' + step + '</li>';
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
                btn.prop('disabled', false).html('<i class="fas fa-magic mr-1"></i> Analisis AI Assistant');
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
        navigator.clipboard.writeText(text).then(function() {
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
        });
    });
</script>
@endpush
@endsection
