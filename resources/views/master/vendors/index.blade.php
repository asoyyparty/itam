@extends('layouts.admin')

@section('title', __('messages.manage') . ' ' . __('messages.vendor'))

@section('content')
<div class="division-actions vendor-actions d-flex justify-content-end align-items-center mb-3">
    <a href="{{ route('vendors.create') }}" class="btn btn-sm btn-primary" title="{{ __('messages.add_new') }}"><i class="fas fa-plus mr-1"></i> {{ __('messages.add_new') }}</a>
</div>

<div class="card theme-card">
    <div class="division-table-header vendor-table-header card-header border-0 d-flex align-items-center flex-wrap">
        <form action="{{ route('vendors.index') }}" method="GET" class="vendor-search-form" style="width: 100%;">
            <div class="vendor-search-controls d-flex align-items-center justify-content-start">
                <div class="vendor-search-input input-group">
                    <input type="text" name="search" class="form-control theme-input" placeholder="{{ __('messages.search') }}..." value="{{ request('search') }}" aria-label="{{ __('messages.search') }}">
                    @if(request('search'))
                        <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" title="Clear" aria-label="Clear"  style="min-width: 40px; height: 38px; padding: 0; display: flex !important; align-items: center !important; justify-content: center !important; border-color: rgba(148, 163, 184, 0.22);"><i class="fas fa-times"></i></a>
                    @endif
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
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.contact_person') }}</th>
                        <th>{{ __('messages.email') }}</th>
                        <th>{{ __('messages.phone') }}</th>
                        <th>{{ __('messages.address') }}</th>
                        <th class="division-action-column">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $item)
                    <tr>
                        <td class="theme-text">{{ $loop->iteration }}</td>
                        <td class="theme-text">{{ $item->name }}</td>
                        <td class="theme-text">{{ $item->contact_person }}</td>
                        <td class="theme-text">{{ $item->email }}</td>
                        <td class="theme-text">{{ $item->phone }}</td>
                        <td class="theme-text">{{ $item->address }}</td>
                        <td class="theme-text">
                            <div class="d-flex justify-content-center" style="gap: 8px;">
                                <a href="{{ route('vendors.edit', $item) }}" class="btn action-btn btn-outline-warning" style="border: 1px solid rgba(255, 193, 7, 0.3); background: rgba(255, 193, 7, 0.15); color: #ffc107;"  title="{{ __('messages.edit') }}"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('vendors.destroy', $item) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-delete action-btn btn-outline-danger" style="border: 1px solid rgba(220, 53, 69, 0.3); background: rgba(220, 53, 69, 0.15); color: #dc3545;"  title="{{ __('messages.delete') }}" data-confirm-message="{{ __('messages.confirm_delete') }}"><i class="fas fa-trash"></i></button>
                            </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted">{{ __('messages.no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
<style>
.vendor-actions { gap: 10px !important; }
.vendor-actions .btn {
    min-height: 42px;
    padding: 0.7rem 1.1rem;
    border-radius: 12px !important;
    font-weight: 600;
}
.vendor-search-form, .vendor-search-controls { width: 100%; }
.vendor-search-controls { gap: 10px; flex-wrap: nowrap; }
.vendor-search-input { min-width: 220px; flex: 0 0 250px; gap: 8px; }
.vendor-search-input .form-control {
    height: 42px !important;
    border-radius: 10px !important;
    border: 1px solid rgba(148, 163, 184, 0.22);
    background: rgba(15, 23, 42, 0.22);
    box-shadow: none !important;
}
.vendor-search-input .form-control { min-width: 0; flex: 1 1 auto; }
.vendor-search-input .btn { height: 42px; min-width: 42px; border-radius: 10px !important; }

@media (max-width: 767.98px) {
    .vendor-search-controls { flex-wrap: wrap; }
    .vendor-search-input { display: flex !important; flex-wrap: nowrap; width: 100% !important; flex-basis: 100%; }
    .vendor-search-input .form-control { width: auto !important; }
}
</style>
@endsection