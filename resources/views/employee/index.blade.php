@extends('layouts.admin')

@section('title', __('messages.manage') . ' ' . __('messages.employee'))

@section('content')
<div class="row mb-3">
    <div class="col-12 mb-3">
        <form action="{{ route('employees.index') }}" method="GET">
            <div class="d-flex flex-wrap align-items-center" style="gap: 10px;">
            <div class="position-relative" style="width: 250px; max-width: 100%; flex: 1; min-width: 200px;">
                <input type="text" name="search" class="form-control theme-input" placeholder="{{ __('messages.search_employee') }}" value="{{ request('search') }}" style="width: 100%; padding-right: 75px; border-radius: 30px; height: 40px; box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);">
                <div class="position-absolute d-flex align-items-center" style="top: 50%; right: 5px; transform: translateY(-50%); gap: 4px;">
                    @if(request('search') || request('department_id') || request('supervisor_id') || request('status'))
                        <a href="{{ route('employees.index') }}" class="text-muted" style="display: flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; text-decoration: none;"><i class="fas fa-times"></i></a>
                    @endif
                    <button class="btn btn-info rounded-circle" type="submit" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border: none; box-shadow: 0 2px 6px rgba(23,162,184,0.4);"><i class="fas fa-search text-xs"></i></button>
                </div>
            </div>
                <select name="department_id" class="form-control select2 theme-input" style="width: 150px;">
                    <option value="" >{{ __('messages.all_department') }}</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}"  {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
                <select name="supervisor_id" class="form-control select2 theme-input" style="width: 150px;">
                    <option value="" >{{ __('messages.all_supervisor') }}</option>
                    @foreach($supervisors as $sup)
                        <option value="{{ $sup->id }}"  {{ request('supervisor_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="form-control select2 theme-input" style="width: 150px;">
                    <option value="" >{{ __('messages.all_status') }}</option>
                    <option value="Active"  {{ request('status') == 'Active' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                    <option value="Inactive"  {{ request('status') == 'Inactive' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                </select>
            
        </div>
    </form>
    </div>
    <div class="col-12 text-right">
        @can('action_manage_employees')
<button type="button" class="btn btn-sm btn-success mr-2" data-toggle="modal" data-target="#importModal" style="box-shadow: 0 0 10px rgba(40,167,69,0.3);">
            {{ __('messages.import_excel') }}
        </button>
@endcan
        <a href="{{ route('employees.export', request()->query()) }}" class="btn btn-sm btn-warning mr-2" style="box-shadow: 0 0 10px rgba(255,193,7,0.3);">
            {{ __('messages.export') }}
        </a>
        @can('action_manage_employees')
<a href="{{ route('employees.create') }}" class="btn btn-sm btn-primary" >
            {{ __('messages.add') }} {{ __('messages.employee') }}
        </a>
@endcan
    </div>
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

<div class="card theme-card">
    <div class="card-body p-0">
        <div class="theme-scroll-container table-responsive">
            <table class="table table-striped table-hover m-0 theme-table">
                <thead>
                    <tr >
                        <th width="50">{{ __('messages.no') }}</th>
                        <th>{{ __('messages.nik') }}</th>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.email') }}</th>
                        <th>{{ __('messages.anydesk_id') ?? 'AnyDesk ID' }}</th>
                        <th>AnyDesk Password</th>
                        <th>{{ __('messages.pc_username') }}</th>
                        <th>PC Password</th>
                        <th>{{ __('messages.status') }}</th>
                        <th width="150" class="text-center">{{ __('messages.actions') }}</th>
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
                        <td class="theme-text text-success"><code>{{ $emp->anydesk_password ?? '-' }}</code></td>
                        <td class="theme-text text-warning font-weight-bold"><code>{{ $emp->login_username ?? '-' }}</code></td>
                        <td class="theme-text text-warning"><code>{{ $emp->login_password ?? '-' }}</code></td>
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
            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap py-2" style="background: transparent; border-top: 1px solid rgba(255,255,255,0.08); gap: 10px;">
                <div class="text-muted small">
                    Showing {{ $employees->firstItem() ?? 0 }} to {{ $employees->lastItem() ?? 0 }} of {{ $employees->total() }} entries
                </div>
                <div>
                    {{ $employees->links() }}
                </div>
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
});
</script>
<style>
.status-btn::after {
    display: none !important;
}
.status-change-btn:hover {
    background-color: rgba(255, 255, 255, 0.1);
}
</style>
@endpush
@endsection
