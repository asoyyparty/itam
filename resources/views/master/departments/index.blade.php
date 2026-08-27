@extends('layouts.admin')

@section('title', __('messages.manage') . ' ' . __('messages.department'))

@section('content')
<div class="division-actions master-actions d-flex justify-content-end align-items-center mb-3">
  <button type="button" class="btn btn-sm btn-info text-nowrap flex-shrink-0" data-toggle="modal" data-target="#importModal" title="Import" aria-label="Import">
    <i class="fas fa-file-import mr-1"></i> Import
  </button>
  <a href="{{ route('departments.export', request()->query()) }}" class="btn btn-sm btn-success text-nowrap flex-shrink-0" title="{{ __('messages.export') }}">
    <i class="fas fa-file-export mr-1"></i> {{ __('messages.export') }}
  </a>
  <a href="{{ route('departments.create') }}" class="btn btn-sm btn-primary text-nowrap flex-shrink-0" title="{{ __('messages.add_new') }}">
    <i class="fas fa-plus mr-1"></i> {{ __('messages.add_new') }}
  </a>
</div>

<div class="card theme-card">
      <div class="division-table-header card-header border-0 d-flex align-items-center flex-wrap">
        <form action="{{ route('departments.index') }}" method="GET" class="master-search-form" style="width: 100%;">
            <div class="master-search-controls d-flex align-items-center justify-content-start">
                <div class="master-search-input input-group">
                    <input type="text" name="search" class="form-control theme-input" placeholder="{{ __('messages.search') }}..." value="{{ request('search') }}" aria-label="{{ __('messages.search') }}">
                    @if(request('search'))<a href="{{ route('departments.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" title="Clear" aria-label="Clear"  style="min-width: 40px; height: 38px; padding: 0; display: flex !important; align-items: center !important; justify-content: center !important; border-color: rgba(148, 163, 184, 0.22);"><i class="fas fa-times"></i></a>@endif
                    <button class="btn btn-info d-flex align-items-center justify-content-center" type="submit" title="{{ __('messages.search') }}" aria-label="{{ __('messages.search') }}"  style="min-width: 40px; height: 38px; padding: 0; display: flex !important; align-items: center !important; justify-content: center !important;"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="theme-scroll-container table-responsive">
            <table class="table table-striped table-hover m-0 theme-table division-table">
                <thead>
                    <tr>
                  <th class="division-no-column">{{ __('messages.no') }}</th>
                  <th class="division-name-column">{{ __('messages.name') }}</th>
                  <th>{{ __('messages.description') }}</th>
                  <th class="division-action-column">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $item)
                    <tr>
                        <td class="theme-text">{{ $loop->iteration }}</td>
                        <td class="theme-text">{{ $item->name }}</td>
                        <td class="theme-text">{{ $item->description }}</td>
                        <td class="theme-text">
                            <div class="d-flex justify-content-center" style="gap: 8px;">
                                <a href="{{ route('departments.edit', $item) }}" class="btn action-btn btn-outline-warning" style="border: 1px solid rgba(255, 193, 7, 0.3); background: rgba(255, 193, 7, 0.15); color: #ffc107;"  title="{{ __('messages.edit') }}"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('departments.destroy', $item) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-delete action-btn btn-outline-danger" style="border: 1px solid rgba(220, 53, 69, 0.3); background: rgba(220, 53, 69, 0.15); color: #dc3545;"  title="{{ __('messages.delete') }}" data-confirm-message="{{ __('messages.confirm_delete') }}"><i class="fas fa-trash"></i></button>
                            </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted">{{ __('messages.no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form action="{{ route('departments.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="importModalLabel"><i class="fas fa-file-import text-info mr-2"></i> Import {{ __('messages.department') }}</h5>
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
              Pilih file (.xlsx, .xls, .csv)
            </div>
            <div class="text-muted small file-name-display">
              Sentuh di sini untuk memilih file
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.cancel') }}</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-upload mr-1"></i> Import</button>
        </div>
      </form>
    </div>
  </div>
</div>
<style>
.master-actions { gap: 10px !important; }
.master-actions .btn {
    min-height: 42px;
    padding: 0.7rem 1.1rem;
    border-radius: 12px !important;
    font-weight: 600;
}
.master-search-form, .master-search-controls { width: 100%; }
.master-search-controls { gap: 10px; flex-wrap: nowrap; }
.master-search-input { min-width: 220px; flex: 0 0 250px; gap: 8px; }
.master-search-input .form-control {
    height: 42px !important;
    border-radius: 10px !important;
    border: 1px solid rgba(148, 163, 184, 0.22);
    background: rgba(15, 23, 42, 0.22);
    box-shadow: none !important;
}
.master-search-input .form-control { min-width: 0; flex: 1 1 auto; }
.master-search-input .btn { height: 42px; min-width: 42px; border-radius: 10px !important; }

@media (max-width: 767.98px) {
    .master-search-controls { flex-wrap: wrap; }
    .master-search-input { display: flex !important; flex-wrap: nowrap; width: 100% !important; flex-basis: 100%; }
    .master-search-input .form-control { width: auto !important; }
}
</style>
@endsection