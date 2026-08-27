@extends('layouts.admin')

@section('title', __('messages.manage') . ' ' . __('messages.employee'))

@section('content')
<div class="division-actions employee-actions d-flex justify-content-end align-items-center mb-3">
    @can('action_manage_employees')
    <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#importModal" title="Import" aria-label="Import"><i class="fas fa-file-import mr-1"></i> Import</button>
    @endcan
    <a href="{{ route('employees.export', request()->query()) }}" class="btn btn-sm btn-success" title="{{ __('messages.export') }}"><i class="fas fa-file-export mr-1"></i> {{ __('messages.export') }}</a>
    @can('action_manage_employees')
    <a href="{{ route('employees.create') }}" class="btn btn-sm btn-primary" title="{{ __('messages.add') }} {{ __('messages.employee') }}"><i class="fas fa-plus mr-1"></i> {{ __('messages.add') }} {{ __('messages.employee') }}</a>
    @endcan
</div>

<div class="card theme-card">
    <div class="division-table-header employee-table-header card-header border-0 d-flex align-items-center flex-wrap">
        <form action="{{ route('employees.index') }}" method="GET" class="employee-search-form" style="width: 100%;">
            <div class="employee-search-controls d-flex align-items-center">
                <select name="department_id" class="form-control select2 theme-input employee-filter">
                    <option value="" >{{ __('messages.all_department') }}</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}"  {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
                <select name="supervisor_id" class="form-control select2 theme-input employee-filter">
                    <option value="" >{{ __('messages.all_supervisor') }}</option>
                    @foreach($supervisors as $sup)
                        <option value="{{ $sup->id }}"  {{ request('supervisor_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="form-control select2 theme-input employee-filter">
                    <option value="" >{{ __('messages.all_status') }}</option>
                    <option value="Active"  {{ request('status') == 'Active' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                    <option value="Inactive"  {{ request('status') == 'Inactive' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                </select>
                <div class="input-group employee-search-input">
                    <input type="text" name="search" class="form-control theme-input" placeholder="{{ __('messages.search_employee') }}" value="{{ request('search') }}" aria-label="{{ __('messages.search_employee') }}">
                    @if(request('search') || request('department_id') || request('supervisor_id') || request('status'))
                        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" title="Clear" aria-label="Clear"  style="min-width: 40px; height: 38px; padding: 0; display: flex !important; align-items: center !important; justify-content: center !important; border-color: rgba(148, 163, 184, 0.22);"><i class="fas fa-times"></i></a>
                    @endif
                    <button class="btn btn-info d-flex align-items-center justify-content-center" type="submit" title="{{ __('messages.search') }}" aria-label="{{ __('messages.search') }}"  style="min-width: 40px; height: 38px; padding: 0; display: flex !important; align-items: center !important; justify-content: center !important;"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>
    </div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible" style="background: rgba(40,167,69,0.2); border: 1px solid rgba(40,167,69,0.5); color: #28a745;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <i class="icon fas fa-check"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible" style="background: rgba(220,53,69,0.2); border: 1px solid rgba(220,53,69,0.5); color: #dc3545;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <i class="icon fas fa-ban"></i> {{ session('error') }}
    </div>
@endif

    <div class="card-body p-0">
        <div class="theme-scroll-container table-responsive">
            <table class="table table-striped table-hover m-0 theme-table division-table employee-table">
                <thead>
                    <tr >
                        <th class="division-no-column">{{ __('messages.no') }}</th>
                        <th>{{ __('messages.nik') }}</th>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.email') }}</th>
                        <th>{{ __('messages.anydesk_id') ?? 'AnyDesk ID' }}</th>
                        <th>AnyDesk Password</th>
                        <th>{{ __('messages.pc_username') }}</th>
                        <th>PC Password</th>
                        <th>{{ __('messages.status') }}</th>
                        <th class="division-action-column text-center">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    <tr>
                        <td class="theme-text">{{ $employees->firstItem() + $loop->index }}</td>
                        <td class="text-info font-weight-bold">{{ $emp->employee_id }}</td>
                        <td class="theme-text"><a href="{{ route('employees.show', $emp) }}" class="text-info font-weight-bold">{{ $emp->name }}</a></td>
                        <td class="theme-text">{{ $emp->email ?? '-' }}</td>
                        <td class="theme-text text-success font-weight-bold"><code>{{ $emp->anydesk_id ?? '-' }}</code></td>
                        <td class="theme-text text-success"><code class="sensitive-value" data-value="{{ $emp->anydesk_password ?? '-' }}">******</code> <button type="button" class="btn btn-sm btn-link p-0 sensitive-toggle" title="Show password" aria-label="Show password"><i class="fas fa-eye"></i></button></td>
                        <td class="theme-text text-warning font-weight-bold"><code>{{ $emp->login_username ?? '-' }}</code></td>
                        <td class="theme-text text-warning"><code class="sensitive-value" data-value="{{ $emp->login_password ?? '-' }}">******</code> <button type="button" class="btn btn-sm btn-link p-0 sensitive-toggle" title="Show password" aria-label="Show password"><i class="fas fa-eye"></i></button></td>
                        <td class="theme-text">
                            <div class="dropdown">
                                @can('action_manage_employees')
<button class="btn btn-sm dropdown-toggle status-btn p-0 border-0 bg-transparent" type="button" data-toggle="dropdown" aria-expanded="false" data-id="{{ $emp->id }}" style="box-shadow: none;">
                                    @if($emp->status == 'Active')
                                        <span class="badge badge-success status-badge" style="box-shadow: 0 0 8px rgba(40,167,69,0.5);">{{ __('messages.active') }}</span>
                                    @else
                                        <span class="badge badge-danger status-badge" style="box-shadow: 0 0 8px rgba(220,53,69,0.5);">{{ __('messages.inactive') }}</span>
                                    @endif
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" >
                                    <a class="dropdown-item status-change-btn text-success" href="#" data-status="Active">{{ __('messages.active') }}</a>
                                    <a class="dropdown-item status-change-btn text-danger" href="#" data-status="Inactive">{{ __('messages.inactive') }}</a>
                                </div>
@else
<button class="btn btn-sm dropdown-toggle status-btn p-0 border-0 bg-transparent" type="button" data-toggle="dropdown" aria-expanded="false" data-id="{{ $emp->id }}" style="box-shadow: none;">
                                    @if($emp->status == 'Active')
                                        <span class="badge badge-success status-badge" style="box-shadow: 0 0 8px rgba(40,167,69,0.5);">{{ __('messages.active') }}</span>
                                    @else
                                        <span class="badge badge-danger status-badge" style="box-shadow: 0 0 8px rgba(220,53,69,0.5);">{{ __('messages.inactive') }}</span>
                                    @endif
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" >
                                    <a class="dropdown-item status-change-btn text-success" href="#" data-status="Active">{{ __('messages.active') }}</a>
                                    <a class="dropdown-item status-change-btn text-danger" href="#" data-status="Inactive">{{ __('messages.inactive') }}</a>
                                </div>
@endcan
                            </div>
                        </td>
                        <td class="theme-text">
                            <div class="d-flex justify-content-center" style="gap: 8px;">
                                @can('action_manage_employees')
<a href="{{ route('employees.edit', array_merge([$emp->id], request()->query())) }}" class="btn action-btn btn-outline-warning" style="border: 1px solid rgba(255, 193, 7, 0.3); background: rgba(255, 193, 7, 0.15); color: #ffc107;"  title="{{ __('messages.edit') }}"><i class="fas fa-edit"></i></a>
@endcan
                            @can('action_manage_employees')
<form action="{{ route('employees.destroy', array_merge([$emp->id], request()->query())) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-delete action-btn btn-outline-danger" style="border: 1px solid rgba(220, 53, 69, 0.3); background: rgba(220, 53, 69, 0.15); color: #dc3545;"  title="{{ __('messages.delete') }}" data-confirm-message="{{ __('messages.confirm_delete') }}"><i class="fas fa-trash"></i></button>
                            </form>
@endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">{{ __('messages.no_data') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($employees->hasPages())
            <div class="pagination-footer card-footer d-flex justify-content-end align-items-center flex-wrap py-2" style="background: transparent; border-top: 1px solid rgba(255,255,255,0.08); gap: 10px;">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="importModalLabel"><i class="fas fa-file-import text-info mr-2"></i> {{ __('messages.import_excel') }} {{ __('messages.employee') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="file-dropzone">
                <input type="file" name="file" class="import-file-input" accept=".xlsx,.xls,.csv" required>
                <div class="icon-box">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <div class="file-label-text font-weight-bold text-main mb-1">
                    {{ __('messages.import_excel') }} (.xlsx, .xls, .csv)
                </div>
                <div class="text-muted small file-name-display">
                    {{ __('messages.select_file_hint') }}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.cancel') }}</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-upload mr-1"></i> {{ __('messages.import_excel') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
    var fileInput = document.querySelector('.import-file-input');
    if (fileInput) {
        fileInput.addEventListener('change',function(e){
            var fileName = e.target.files[0].name;
            var nextSibling = e.target.nextElementSibling;
            if (nextSibling && nextSibling.classList.contains('file-label-text')) {
                nextSibling.innerText = fileName;
            }
        });
    }

$(document).ready(function() {
    $(document).on('click', '.status-change-btn', function(e) {
        e.preventDefault();
        var btn = $(this);
        var newStatus = btn.data('status');
        var container = btn.closest('.dropdown');
        var id = container.find('.status-btn').data('id');
        var badge = container.find('.status-badge');
        
        var originalHtml = badge.html();
        badge.html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: '{{ url("employees") }}/' + id + '/status',
            type: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                if(response.success) {
                    badge.removeClass('badge-primary badge-warning badge-success badge-danger badge-secondary badge-info badge-dark');
                    badge.css('box-shadow', 'none');
                    
                    switch(newStatus) {
                        case 'Active':
                            badge.addClass('badge-success');
                            badge.css('box-shadow', '0 0 8px rgba(40,167,69,0.5)');
                            badge.text('{{ __("messages.active") }}');
                            break;
                        case 'Inactive':
                            badge.addClass('badge-danger');
                            badge.css('box-shadow', '0 0 8px rgba(220,53,69,0.5)');
                            badge.text('{{ __("messages.inactive") }}');
                            break;
                    }
                }
            },
            error: function(xhr) {
                Swal.fire({ icon: 'error', text: 'Error updating status.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, background: '#0f172a', color: '#f8fafc', customClass: { popup: 'border border-danger' } });
                badge.html(originalHtml);
            }
        });
    });

    $(document).on('click', '.sensitive-toggle', function() {
        var toggle = $(this);
        var value = toggle.prev('.sensitive-value');
        var isHidden = value.text() === '******';
        value.text(isHidden ? value.data('value') : '******');
        toggle.find('i').attr('class', isHidden ? 'fas fa-eye-slash' : 'fas fa-eye');
    });
});
</script>
<style>
.employee-actions { gap: 10px !important; }
.employee-actions .btn {
    min-height: 42px;
    padding: 0.7rem 1.1rem;
    border-radius: 12px !important;
    font-weight: 600;
}
.employee-search-form, .employee-search-controls { width: 100%; }
.employee-search-controls { gap: 10px; flex-wrap: nowrap; }
.employee-search-input { min-width: 220px; flex: 1 1 auto; gap: 8px; }
.employee-search-input .form-control, .employee-filter {
    height: 42px !important;
    border-radius: 10px !important;
    border: 1px solid rgba(148, 163, 184, 0.22);
    background: rgba(15, 23, 42, 0.22);
    box-shadow: none !important;
}
.employee-search-input .form-control { min-width: 0; flex: 1 1 auto; }
.employee-search-input .btn { height: 42px; min-width: 42px; border-radius: 10px !important; }
.employee-filter { width: 150px; min-width: 140px; flex: 0 1 150px; }

@media (max-width: 767.98px) {
    .employee-search-controls { flex-wrap: wrap; }
    .employee-search-input, .employee-filter {
        width: 100% !important; min-width: 0 !important; flex-basis: 100%; max-width: 100%;
    }
    .employee-search-input { display: flex !important; flex-wrap: nowrap; }
    .employee-search-input .form-control { width: auto !important; }
}

.status-btn::after {
    display: none !important;
}
.status-change-btn:hover {
    background-color: rgba(255, 255, 255, 0.1);
}
</style>
@endpush
@endsection
