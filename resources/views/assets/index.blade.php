@extends('layouts.admin')

@section('title', __('messages.manage') . ' ' . __('messages.it_assets'))

@section('content')
<div class="division-actions d-flex align-items-center justify-content-between mb-3" style="gap: 12px; width: 100%;">
    <div class="d-flex align-items-center" style="gap: 10px; margin-left: auto;">
        @can('action_manage_assets')
        <button type="button" class="btn btn-sm btn-info text-nowrap flex-shrink-0" data-toggle="modal" data-target="#importModal" title="{{ __('messages.import') }}" aria-label="{{ __('messages.import') }}" style="border-radius: 999px; padding: 0.55rem 1rem; font-weight: 600;"><i class="fas fa-file-import mr-1"></i> {{ __('messages.import') }}</button>
        @endcan
        <a href="{{ route('assets.export', request()->query()) }}" class="btn btn-sm btn-success text-nowrap flex-shrink-0" title="{{ __('messages.export') }}" style="border-radius: 999px; padding: 0.55rem 1rem; font-weight: 600;"><i class="fas fa-file-export mr-1"></i> {{ __('messages.export') }}</a>
        <a href="{{ route('assets.create', request()->query()) }}" class="btn btn-sm btn-primary text-nowrap flex-shrink-0" title="{{ __('messages.add_new') }} {{ __('messages.asset') }}" style="border-radius: 999px; padding: 0.55rem 1rem; font-weight: 600;"><i class="fas fa-plus mr-1"></i> {{ __('messages.add_new') }} {{ __('messages.asset') }}</a>
    </div>
</div>

<style>
    .division-actions {
        margin-bottom: 18px;
    }

    .division-actions .btn {
        border-radius: 12px;
        min-height: 42px;
        padding: 0.7rem 1.1rem;
        font-weight: 600;
        letter-spacing: 0.01em;
        transition: transform 0.18s ease, box-shadow 0.18s ease, opacity 0.18s ease;
    }

    .division-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.12);
    }

    .asset-search-form {
        width: 100%;
    }

    .asset-search-controls {
        gap: 10px;
        flex-wrap: nowrap;
        width: 100%;
    }

    .asset-filter,
    .asset-search-input .form-control {
        height: 42px;
        border-radius: 10px;
        border: 1px solid rgba(148, 163, 184, 0.22);
        background: rgba(15, 23, 42, 0.22);
        color: #e5e7eb;
        box-shadow: none;
    }

    .asset-filter {
        width: 150px;
        min-width: 120px;
        max-width: 150px;
        flex-shrink: 0;
    }

    .asset-search-input {
        min-width: 220px;
        flex: 1 1 auto;
        flex-shrink: 1;
    }

    .asset-search-input .form-control {
        min-width: 140px;
        flex: 1 1 auto;
    }

    .asset-search-input .btn {
        height: 42px;
        min-width: 42px;
        border-radius: 10px;
    }

    @media (max-width: 767px) {
        .division-actions {
            flex-direction: column !important;
            align-items: flex-start !important;
            justify-content: flex-start !important;
        }

        .division-actions > div {
            width: 100% !important;
            margin-left: 0 !important;
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px !important;
        }

        .division-actions .btn {
            width: 100% !important;
            justify-content: center !important;
            text-align: center !important;
        }

        .division-actions .btn:last-child {
            grid-column: 1 / -1;
        }

        .asset-search-form {
            width: 100% !important;
            overflow: visible !important;
        }

        .asset-search-controls {
            flex-wrap: wrap !important;
            overflow: visible !important;
        }

        .asset-filter,
        .asset-search-input {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }

        .asset-search-input {
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: center;
            gap: 10px !important;
            width: 100% !important;
            max-width: 100% !important;
            margin-left: 0 !important;
            box-sizing: border-box;
            overflow: visible !important;
        }

        .asset-search-input .form-control {
            width: auto !important;
            min-width: 0 !important;
            flex: 1 1 auto !important;
        }

        .asset-search-input .btn {
            flex: 0 0 42px !important;
            width: 42px !important;
            min-width: 42px !important;
            padding: 0 !important;
        }
    }
</style>

<div class="card theme-card">
    <div class="division-table-header asset-table-header card-header border-0 d-flex align-items-center flex-wrap" style="gap: 14px;">
        <form action="{{ route('assets.index') }}" method="GET" class="asset-search-form" style="width: 100%; max-width: 100%; overflow: hidden;">
            <div class="asset-search-controls d-flex align-items-center" style="gap: 10px; flex-wrap: nowrap; width: 100%; overflow: hidden;">
                <select name="date_received" class="form-control select2 theme-input asset-filter" title="{{ __('messages.filter') }}" style="width: 150px; min-width: 120px; max-width: 150px; flex-shrink: 0;">
                    <option value="">{{ __('messages.all_year') ?? 'Semua Tahun' }}</option>
                    @foreach($years as $yr)
                        <option value="{{ $yr }}" {{ (request('date_received') == $yr || request('year') == $yr) ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>
                <select name="category_id" class="form-control select2 theme-input asset-filter" style="width: 150px; min-width: 120px; max-width: 150px; flex-shrink: 0;">
                    <option value="" >{{ __('messages.all_category') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"  {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select name="brand_id" class="form-control select2 theme-input asset-filter" style="width: 150px; min-width: 120px; max-width: 150px; flex-shrink: 0;">
                    <option value="" >{{ __('messages.all_brand') }}</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}"  {{ request('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                    @endforeach
                </select>
                <select name="location_id" class="form-control select2 theme-input asset-filter" style="width: 150px; min-width: 120px; max-width: 150px; flex-shrink: 0;">
                    <option value="" >{{ __('messages.all_location') }}</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}"  {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="form-control select2 theme-input asset-filter" style="width: 150px; min-width: 120px; max-width: 150px; flex-shrink: 0;">
                    <option value="" >{{ __('messages.all_status') }}</option>
                    <option value="Available"  {{ str_contains(request('status') ?? '', 'Available') ? 'selected' : '' }}>{{ __('messages.available') }}</option>
                    <option value="Assigned"  {{ request('status') == 'Assigned' ? 'selected' : '' }}>{{ __('messages.assigned') }}</option>
                    <option value="Maintenance"  {{ request('status') == 'Maintenance' ? 'selected' : '' }}>{{ __('messages.maintenance') }}</option>
                    <option value="Retired"  {{ request('status') == 'Retired' ? 'selected' : '' }}>{{ __('messages.retired') }}</option>
                    <option value="Missing"  {{ request('status') == 'Missing' ? 'selected' : '' }}>{{ __('messages.missing') }}</option>
                </select>
                <div class="input-group asset-search-input" style="display: flex; align-items: center; gap: 10px; min-width: 220px; flex: 1 1 auto; flex-shrink: 1; margin-left: 2px;">
                    <input type="text" name="search" class="form-control theme-input" placeholder="{{ __('messages.search_asset') }}" value="{{ request('search') }}" aria-label="{{ __('messages.search_asset') }}" style="min-width: 150px; flex: 1 1 auto;">
                    @if(request('search') || request('category_id') || request('brand_id') || request('location_id') || request('status') || request('date_received') || request('year'))
                        <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" title="{{ __('messages.clear') }}" aria-label="{{ __('messages.clear') }}"  style="min-width: 40px; height: 38px; padding: 0; display: flex !important; align-items: center !important; justify-content: center !important; border-color: rgba(148, 163, 184, 0.22);"><i class="fas fa-times"></i></a>
                    @endif
                    <button class="btn btn-info d-flex align-items-center justify-content-center" type="submit" title="{{ __('messages.search') }}" aria-label="{{ __('messages.search') }}"   style="margin-left: 2px; min-width: 40px; height: 38px; padding: 0; display: flex !important; align-items: center !important; justify-content: center !important;"><i class="fas fa-search"></i></button>
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

    <div class="card-body p-0">
        <div class="theme-scroll-container table-responsive">
            <table class="table table-striped table-hover m-0 theme-table division-table asset-table">
                <thead>
                    <tr >
                        <th class="division-no-column">{{ __('messages.no') }}</th>
                        <th>{{ __('messages.asset_tag') }}</th>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.category') }}</th>
                        <th>{{ __('messages.received') }}</th>
                        <th>{{ __('messages.location') }}</th>
                        <th>{{ __('messages.user') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.health') }}</th>
                        <th class="division-action-column text-center">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                    <tr>
                        <td class="theme-text">{{ $loop->iteration }}</td>
                        <td class="font-weight-bold"><a href="{{ route('assets.show', $asset) }}" class="text-info font-weight-bold">{{ $asset->asset_tag }}</a></td>
                        <td class="theme-text"><a href="{{ route('assets.show', $asset) }}" class="theme-text">{{ $asset->name }}</a></td>
                        <td class="theme-text">{{ $asset->category->name ?? '-' }}</td>
                        <td class="theme-text">{{ $asset->date_received ? \Carbon\Carbon::parse($asset->date_received)->format('d M Y') : '-' }}</td>
                        <td class="theme-text">{{ $asset->location->name ?? '-' }}</td>
                        <td class="theme-text">
                            @if($asset->currentAssignment && $asset->currentAssignment->employee)
                                <a href="{{ route('employees.show', $asset->currentAssignment->employee_id) }}" class="text-info font-weight-bold">{{ $asset->currentAssignment->employee->name }}</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="theme-text">
                            <div class="dropdown">
                                @can('action_manage_assets')
<button class="btn btn-sm dropdown-toggle status-btn p-0 border-0 bg-transparent" type="button" data-toggle="dropdown" aria-expanded="false" data-id="{{ $asset->id }}" style="box-shadow: none;">
                                    @switch($asset->status)
                                        @case('Available')
                                            <span class="badge badge-success status-badge" style="box-shadow: 0 0 8px rgba(40,167,69,0.5);">{{ __('messages.available') }}</span>
                                            @break
                                        @case('Assigned')
                                            <span class="badge badge-primary status-badge" style="box-shadow: 0 0 8px rgba(0,123,255,0.5);">{{ __('messages.assigned') }}</span>
                                            @break
                                        @case('Maintenance')
                                            <span class="badge badge-warning status-badge" style="box-shadow: 0 0 8px rgba(255,193,7,0.5);">{{ __('messages.maintenance') }}</span>
                                            @break
                                        @case('Retired')
                                            <span class="badge badge-secondary status-badge">{{ __('messages.retired') }}</span>
                                            @break
                                        @case('Missing')
                                            <span class="badge badge-danger status-badge" style="box-shadow: 0 0 8px rgba(220,53,69,0.5);">{{ __('messages.missing') }}</span>
                                            @break
                                    @endswitch
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" >
                                    <a class="dropdown-item status-change-btn text-success" href="#" data-status="Available">{{ __('messages.available') }}</a>
                                    <a class="dropdown-item status-change-btn text-primary" href="#" data-status="Assigned">{{ __('messages.assigned') }}</a>
                                    <a class="dropdown-item status-change-btn text-warning" href="#" data-status="Maintenance">{{ __('messages.maintenance') }}</a>
                                    <a class="dropdown-item status-change-btn text-secondary" href="#" data-status="Retired">{{ __('messages.retired') }}</a>
                                    <a class="dropdown-item status-change-btn text-danger" href="#" data-status="Missing">{{ __('messages.missing') }}</a>
                                </div>
@else
<button class="btn btn-sm dropdown-toggle status-btn p-0 border-0 bg-transparent" type="button" data-toggle="dropdown" aria-expanded="false" data-id="{{ $asset->id }}" style="box-shadow: none;">
                                    @switch($asset->status)
                                        @case('Available')
                                            <span class="badge badge-success status-badge" style="box-shadow: 0 0 8px rgba(40,167,69,0.5);">{{ __('messages.available') }}</span>
                                            @break
                                        @case('Assigned')
                                            <span class="badge badge-primary status-badge" style="box-shadow: 0 0 8px rgba(0,123,255,0.5);">{{ __('messages.assigned') }}</span>
                                            @break
                                        @case('Maintenance')
                                            <span class="badge badge-warning status-badge" style="box-shadow: 0 0 8px rgba(255,193,7,0.5);">{{ __('messages.maintenance') }}</span>
                                            @break
                                        @case('Retired')
                                            <span class="badge badge-secondary status-badge">{{ __('messages.retired') }}</span>
                                            @break
                                        @case('Missing')
                                            <span class="badge badge-danger status-badge" style="box-shadow: 0 0 8px rgba(220,53,69,0.5);">{{ __('messages.missing') }}</span>
                                            @break
                                    @endswitch
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" >
                                    <a class="dropdown-item status-change-btn text-success" href="#" data-status="Available">{{ __('messages.available') }}</a>
                                    <a class="dropdown-item status-change-btn text-primary" href="#" data-status="Assigned">{{ __('messages.assigned') }}</a>
                                    <a class="dropdown-item status-change-btn text-warning" href="#" data-status="Maintenance">{{ __('messages.maintenance') }}</a>
                                    <a class="dropdown-item status-change-btn text-secondary" href="#" data-status="Retired">{{ __('messages.retired') }}</a>
                                    <a class="dropdown-item status-change-btn text-danger" href="#" data-status="Missing">{{ __('messages.missing') }}</a>
                                </div>
@endcan
                            </div>
                        </td>
                        <td class="theme-text">
                            @if(isset($asset->health_data))
                                <a href="{{ route('predictive-health.index') }}" class="badge {{ $asset->health_data['badge_class'] }} px-2 py-1" title="Skor Kesehatan Prediktif">
                                    <i class="fas fa-heartbeat mr-1"></i> {{ $asset->health_data['health_score'] }}%
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="theme-text">
                            <div class="d-flex justify-content-center" style="gap: 8px;">
                                @can('action_manage_assets')
                                    <a href="{{ route('assets.edit', array_merge(['asset' => $asset->id], request()->query())) }}" class="btn action-btn btn-outline-warning" title="{{ __('messages.edit') }}" aria-label="{{ __('messages.edit') }}"><i class="fas fa-edit"></i></a>
@endcan
                                @can('action_manage_assets')
                                <form action="{{ route('assets.destroy', array_merge(['asset' => $asset->id], request()->query())) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-delete action-btn btn-outline-danger" title="{{ __('messages.delete') }}" aria-label="{{ __('messages.delete') }}" data-confirm-message="{{ __('messages.confirm_delete') }}"><i class="fas fa-trash"></i></button>
                                </form>
@endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">{{ __('messages.no_data') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($assets->hasPages())
            <div class="pagination-footer card-footer d-flex align-items-center flex-wrap py-2" style="background: transparent; border-top: 1px solid rgba(255,255,255,0.08); gap: 10px;">
                {{ $assets->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('assets.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel"><i class="fas fa-file-import text-info mr-2"></i> {{ __('messages.import') }} {{ __('messages.asset') }}</h5>
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
                            {{ __('messages.import') }} (.xlsx, .xls, .csv)
                        </div>
                        <div class="text-muted small file-name-display">
                            Sentuh di sini untuk memilih file
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-upload mr-1"></i> {{ __('messages.import') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
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
            url: '{{ url("assets") }}/' + id + '/status',
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
                        case 'Available':
                            badge.addClass('badge-success');
                            badge.css('box-shadow', '0 0 8px rgba(40,167,69,0.5)');
                            badge.text('{{ __("messages.available") }}');
                            break;
                        case 'Assigned':
                            badge.addClass('badge-primary');
                            badge.css('box-shadow', '0 0 8px rgba(0,123,255,0.5)');
                            badge.text('{{ __("messages.assigned") }}');
                            break;
                        case 'Maintenance':
                            badge.addClass('badge-warning');
                            badge.css('box-shadow', '0 0 8px rgba(255,193,7,0.5)');
                            badge.text('{{ __("messages.maintenance") }}');
                            break;
                        case 'Retired':
                            badge.addClass('badge-secondary');
                            badge.text('{{ __("messages.retired") }}');
                            break;
                        case 'Missing':
                            badge.addClass('badge-danger');
                            badge.css('box-shadow', '0 0 8px rgba(220,53,69,0.5)');
                            badge.text('{{ __("messages.missing") }}');
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
.status-btn::after { display: none !important; }
.status-change-btn:hover { background-color: rgba(255, 255, 255, 0.1); }
</style>
@endpush
@endsection
