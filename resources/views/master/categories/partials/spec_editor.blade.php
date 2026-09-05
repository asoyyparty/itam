<div class="form-group mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <label class="form-label form-label-title theme-text mb-0">
            <i class="fas fa-sliders-h mr-1 text-primary"></i> Custom Specifications <small class="text-muted font-weight-normal">(Optional)</small>
        </label>
        <button type="button" class="btn btn-sm rounded-pill px-3" id="add-spec-btn" style="background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.4); color: #60a5fa; font-weight: 600;">
            <i class="fas fa-plus mr-1"></i> Add Field
        </button>
    </div>
    
    <div id="specs-body" class="d-flex flex-column" style="gap: 12px;">
        <!-- Dynamic cards go here -->
    </div>

    <!-- Hidden input to store JSON -->
    <input type="hidden" name="spec_definitions" id="spec_definitions_input" value="{{ old('spec_definitions', isset($category) && $category->spec_definitions ? json_encode($category->spec_definitions) : '[]') }}">
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let specDefinitions = [];
    try {
        let val = $('#spec_definitions_input').val();
        specDefinitions = val ? JSON.parse(val) : [];
    } catch (e) {
        specDefinitions = [];
    }

    function renderSpecs() {
        let tbody = $('#specs-body');
        tbody.empty();
        
        if(specDefinitions.length === 0) {
            tbody.append('<div class="text-center p-4 rounded-lg" style="background: rgba(15, 23, 42, 0.2); border: 1px dashed rgba(148, 163, 184, 0.2);"><small class="text-muted">No custom specifications defined.</small></div>');
        }

        specDefinitions.forEach((spec, index) => {
            let row = `
            <div class="p-3 rounded-lg" style="background: rgba(15, 23, 42, 0.3); border: 1px solid rgba(148, 163, 184, 0.15);">
                <div class="row mx-n2 align-items-end">
                    <div class="col-md-4 px-2 mb-3 mb-md-0">
                        <label class="text-muted d-block mb-1" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Field Key</label>
                        <input type="text" class="form-control theme-input custom-master-input spec-key" data-index="${index}" value="${spec.name || ''}" placeholder="e.g. ram_size" style="height: 40px !important;">
                    </div>
                    <div class="col-md-4 px-2 mb-3 mb-md-0">
                        <label class="text-muted d-block mb-1" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Field Label</label>
                        <input type="text" class="form-control theme-input custom-master-input spec-label" data-index="${index}" value="${spec.label || ''}" placeholder="e.g. RAM Size" style="height: 40px !important;">
                    </div>
                    <div class="col-md-3 px-2 mb-3 mb-md-0">
                        <label class="text-muted d-block mb-1" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Input Type</label>
                        <select class="form-control theme-input custom-master-input spec-type" data-index="${index}" style="height: 40px !important;">
                            <option value="text" ${spec.type === 'text' ? 'selected' : ''}>Text</option>
                            <option value="number" ${spec.type === 'number' ? 'selected' : ''}>Number</option>
                            <option value="date" ${spec.type === 'date' ? 'selected' : ''}>Date</option>
                        </select>
                    </div>
                    <div class="col-md-1 px-2">
                        <button type="button" class="btn btn-outline-danger remove-spec-btn w-100 d-flex align-items-center justify-content-center" data-index="${index}" style="height: 40px; border-radius: 10px;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>`;
            tbody.append(row);
        });
        
        updateHiddenInput();
    }

    function updateHiddenInput() {
        $('#spec_definitions_input').val(JSON.stringify(specDefinitions));
    }

    $('#add-spec-btn').click(function() {
        specDefinitions.push({ name: '', label: '', type: 'text' });
        renderSpecs();
    });

    $(document).on('click', '.remove-spec-btn', function() {
        let idx = $(this).data('index');
        specDefinitions.splice(idx, 1);
        renderSpecs();
    });

    $(document).on('change keyup', '.spec-key, .spec-label, .spec-type', function() {
        let idx = $(this).data('index');
        if($(this).hasClass('spec-key')) specDefinitions[idx].name = $(this).val().toLowerCase().replace(/[^a-z0-9_]/g, '');
        if($(this).hasClass('spec-label')) specDefinitions[idx].label = $(this).val();
        if($(this).hasClass('spec-type')) specDefinitions[idx].type = $(this).val();
        updateHiddenInput();
    });

    // Ensure format on submit
    $('form').submit(function() {
        // filter out empty keys
        specDefinitions = specDefinitions.filter(s => s.name.trim() !== '');
        updateHiddenInput();
    });

    renderSpecs();
});
</script>
@endpush
