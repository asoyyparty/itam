@extends('layouts.admin')

@section('title', __('messages.manage_permissions') . ': ' . $role->name)

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 pb-3" style="border-bottom: 1px solid var(--rule-soft);">
    <div class="mb-3 mb-md-0">
        <div class="d-flex align-items-center mb-1" style="gap: 10px;">
            <a href="{{ route('roles.index') }}" class="btn btn-sm btn-icon-only text-muted p-0" title="{{ __('messages.back') }}">
                <i class="fas fa-arrow-left" style="font-size: 1rem;"></i>
            </a>
            <h4 class="theme-text font-weight-bold m-0" style="font-size: 1.18rem; letter-spacing: -0.01em;">
                Kelola Hak Akses: <span class="text-primary">{{ $role->name }}</span>
            </h4>
        </div>
        <p class="theme-text text-muted mb-0 pl-md-4" style="font-size: 0.82rem;">Atur menu navigasi dan izin tindakan sistem secara terstruktur.</p>
    </div>
    <div class="d-flex align-items-center" style="gap: 8px;">
        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-select-all-global" style="border-radius: 6px; font-size: 0.78rem; font-weight: 600;">
            Pilih Semua
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-deselect-all-global" style="border-radius: 6px; font-size: 0.78rem; font-weight: 600;">
            Kosongkan
        </button>
    </div>
</div>

<!-- Search & Status Summary Bar -->
<div class="card theme-card shadow-xs mb-3" style="border-radius: 10px; border: 1px solid var(--rule-soft);">
    <div class="card-body p-2 px-3">
        <div class="row align-items-center">
            <div class="col-md-8 col-12 py-1">
                <div class="d-flex align-items-center" style="gap: 8px;">
                    <i class="fas fa-search text-muted" style="font-size: 0.88rem;"></i>
                    <input type="text" id="search-permissions" class="form-control border-0 bg-transparent p-0" placeholder="Cari hak akses..." style="font-size: 0.85rem; outline: none; box-shadow: none; height: auto;">
                </div>
            </div>
            <div class="col-md-4 col-12 text-md-right text-left py-1" style="border-top: 1px solid var(--rule-soft) !important; border-top-width: 0 !important; border-top-style: none !important;">
                <span class="text-muted" style="font-size: 0.78rem;">
                    Menampilkan <strong id="visible-perm-count" class="text-primary">{{ $permissions->count() }}</strong> dari <strong>{{ $permissions->count() }}</strong> Hak Akses
                </span>
            </div>
        </div>
    </div>
</div>

@php
    $permissionLabels = [
        // Menu Permissions (Read/Access)
        'menu_dashboard' => 'Dashboard Overview',
        'menu_assets' => 'Katalog Aset IT',
        'menu_assignments' => 'Penyerahan & Peminjaman Aset',
        'menu_tickets' => 'Tiket IT Support & Helpdesk',
        'menu_maintenances' => 'Perawatan & Maintenance',
        'menu_predictive_health' => 'Pemeliharaan Prediktif',
        'menu_departments' => 'Data Departemen',
        'menu_brands' => 'Data Merk / Brand',
        'menu_locations' => 'Data Lokasi & Ruangan',
        'menu_categories' => 'Data Kategori Aset',
        'menu_vendors' => 'Data Vendor / Supplier',
        'menu_employees' => 'Data Karyawan / Pengguna',
        'menu_users' => 'Manajemen Pengguna Sistem',
        'menu_vlans' => 'Subnet & VLAN',
        'menu_ips' => 'Alamat IP & Telemetri',
        'menu_network_anomalies' => 'Monitoring Jaringan',
        'menu_software_licenses' => 'Lisensi Software',
        'menu_password_vaults' => 'Brankas Sandi (Vault)',
        'menu_budget_planner' => 'Anggaran IT',
        'menu_settings' => 'Pengaturan Sistem',
        'menu_roles' => 'Peran & Hak Akses',
        'menu_pics' => 'PIC Perangkat & Penanggung Jawab',

        // Action Permissions (Create, Edit, Update, Delete)
        'action_manage_assets' => 'Kelola & Edit Aset IT',
        'action_manage_assignments' => 'Kelola & Edit Penyerahan Aset',
        'action_manage_tickets' => 'Kelola & Edit Tiket IT Support',
        'action_manage_maintenance' => 'Kelola & Edit Riwayat Perawatan (Maintenance)',
        'action_manage_employees' => 'Kelola & Edit Data Karyawan',
        'action_manage_network' => 'Kelola & Edit Jaringan (IP & VLAN)',
        'action_manage_master_data' => 'Kelola & Edit Master Data',
        'action_manage_settings' => 'Kelola & Edit Pengaturan & Pengguna',
        'action_ocr_scan' => 'Fitur Scan OCR 1-Klik',
        'action_ai_assistant' => 'Fitur ITAM AI Chatbot',
        'action_export_excel' => 'Fitur Ekspor Data Excel',
        'action_import_excel' => 'Fitur Impor Data Excel',
    ];

    $menuPermissions = $permissions->filter(function($p) { return str_starts_with($p->name, 'menu_'); })->sortBy('name');
    $actionManagePermissions = $permissions->filter(function($p) { return str_starts_with($p->name, 'action_manage_'); })->sortBy('name');
    $actionOtherPermissions = $permissions->filter(function($p) { return !str_starts_with($p->name, 'menu_') && !str_starts_with($p->name, 'action_manage_'); })->sortBy('name');
@endphp

<form action="{{ route('roles.update', $role->id) }}" method="POST" id="form-manage-permissions" class="pb-5 pb-md-0">
    @csrf
    @method('PUT')

    <!-- 1. Navigation Menu Access Group -->
    <div class="card theme-card shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid var(--rule-soft);">
        <div class="card-header bg-transparent d-flex align-items-center justify-content-between py-3 px-3 px-md-4" style="border-bottom: 1px solid var(--rule-soft);">
            <div class="d-flex align-items-center" style="gap: 10px;">
                <span class="avatar-badge bg-info-soft text-info"><i class="fas fa-layer-group"></i></span>
                <div>
                    <h6 class="theme-text font-weight-bold m-0" style="font-size: 0.9rem;">Akses Navigasi & Menu Halaman</h6>
                    <small class="text-muted" style="font-size: 0.72rem;">Halaman modul yang diizinkan untuk diakses</small>
                </div>
            </div>
            <button type="button" class="btn btn-xs btn-link text-info p-0 font-weight-bold toggle-group-btn" data-target=".menu-perm-switch" style="font-size: 0.75rem;">
                Toggle Group
            </button>
        </div>
        <div class="card-body p-0">
            <div class="perm-list-group">
                @foreach($menuPermissions as $permission)
                    @php
                        $label = $permissionLabels[$permission->name] ?? ucwords(str_replace('_', ' ', str_replace('menu_', '', $permission->name)));
                        $isChecked = in_array($permission->name, $rolePermissions);
                    @endphp
                    <div class="perm-list-row d-flex align-items-center justify-content-between py-3 px-3 px-md-4 {{ $isChecked ? 'is-checked' : '' }}" data-label="{{ mb_strtolower($label . ' ' . $permission->name) }}">
                        <div class="perm-label-wrap pr-3">
                            <span class="perm-title theme-text font-weight-bold d-block mb-0" style="font-size: 0.85rem; color: var(--color-ink-0);">{{ $label }}</span>
                            <span class="perm-code text-muted d-block" style="font-size: 0.7rem; font-family: monospace;">{{ $permission->name }}</span>
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input perm-switch menu-perm-switch" id="perm_{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}" {{ $isChecked ? 'checked' : '' }}>
                            <label class="custom-control-label" for="perm_{{ $permission->id }}"></label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- 2. Edit, Create, Delete Action Access Group -->
    <div class="card theme-card shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid var(--rule-soft);">
        <div class="card-header bg-transparent d-flex align-items-center justify-content-between py-3 px-3 px-md-4" style="border-bottom: 1px solid var(--rule-soft);">
            <div class="d-flex align-items-center" style="gap: 10px;">
                <span class="avatar-badge bg-success-soft text-success"><i class="fas fa-shield-alt"></i></span>
                <div>
                    <h6 class="theme-text font-weight-bold m-0" style="font-size: 0.9rem;">Akses Pengelolaan & Edit Data</h6>
                    <small class="text-muted" style="font-size: 0.72rem;">Izin untuk menambah, mengubah, dan menghapus data</small>
                </div>
            </div>
            <button type="button" class="btn btn-xs btn-link text-success p-0 font-weight-bold toggle-group-btn" data-target=".manage-perm-switch" style="font-size: 0.75rem;">
                Toggle Group
            </button>
        </div>
        <div class="card-body p-0">
            <div class="perm-list-group">
                @foreach($actionManagePermissions as $permission)
                    @php
                        $label = $permissionLabels[$permission->name] ?? ucwords(str_replace('_', ' ', str_replace('action_manage_', '', $permission->name)));
                        $isChecked = in_array($permission->name, $rolePermissions);
                    @endphp
                    <div class="perm-list-row d-flex align-items-center justify-content-between py-3 px-3 px-md-4 {{ $isChecked ? 'is-checked' : '' }}" data-label="{{ mb_strtolower($label . ' ' . $permission->name) }}">
                        <div class="perm-label-wrap pr-3">
                            <span class="perm-title theme-text font-weight-bold d-block mb-0" style="font-size: 0.85rem; color: var(--color-ink-0);">{{ $label }}</span>
                            <span class="perm-code text-muted d-block" style="font-size: 0.7rem; font-family: monospace;">{{ $permission->name }}</span>
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input perm-switch manage-perm-switch" id="perm_{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}" {{ $isChecked ? 'checked' : '' }}>
                            <label class="custom-control-label" for="perm_{{ $permission->id }}"></label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- 3. Tools & AI Features Group -->
    @if($actionOtherPermissions->count() > 0)
    <div class="card theme-card shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid var(--rule-soft);">
        <div class="card-header bg-transparent d-flex align-items-center justify-content-between py-3 px-3 px-md-4" style="border-bottom: 1px solid var(--rule-soft);">
            <div class="d-flex align-items-center" style="gap: 10px;">
                <span class="avatar-badge bg-warning-soft text-warning"><i class="fas fa-sliders-h"></i></span>
                <div>
                    <h6 class="theme-text font-weight-bold m-0" style="font-size: 0.9rem;">Fitur Tambahan & Alat Sistem</h6>
                    <small class="text-muted" style="font-size: 0.72rem;">Akses ke fitur chatbot, OCR, dan ekspor/impor data</small>
                </div>
            </div>
            <button type="button" class="btn btn-xs btn-link text-warning p-0 font-weight-bold toggle-group-btn" data-target=".tools-perm-switch" style="font-size: 0.75rem;">
                Toggle Group
            </button>
        </div>
        <div class="card-body p-0">
            <div class="perm-list-group">
                @foreach($actionOtherPermissions as $permission)
                    @php
                        $label = $permissionLabels[$permission->name] ?? ucwords(str_replace('_', ' ', str_replace('action_', '', $permission->name)));
                        $isChecked = in_array($permission->name, $rolePermissions);
                    @endphp
                    <div class="perm-list-row d-flex align-items-center justify-content-between py-3 px-3 px-md-4 {{ $isChecked ? 'is-checked' : '' }}" data-label="{{ mb_strtolower($label . ' ' . $permission->name) }}">
                        <div class="perm-label-wrap pr-3">
                            <span class="perm-title theme-text font-weight-bold d-block mb-0" style="font-size: 0.85rem; color: var(--color-ink-0);">{{ $label }}</span>
                            <span class="perm-code text-muted d-block" style="font-size: 0.7rem; font-family: monospace;">{{ $permission->name }}</span>
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input perm-switch tools-perm-switch" id="perm_{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}" {{ $isChecked ? 'checked' : '' }}>
                            <label class="custom-control-label" for="perm_{{ $permission->id }}"></label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Save Action Bar (Desktop) -->
    <div class="d-none d-md-flex justify-content-between align-items-center mb-5 p-3 rounded-lg theme-card" style="border: 1px solid var(--rule-soft);">
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary px-4" style="border-radius: 6px; font-weight: 500;">
            {{ __('messages.cancel') }}
        </a>
        <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm" style="border-radius: 6px;">
            <i class="fas fa-save mr-1"></i> {{ __('messages.save_permissions') }}
        </button>
    </div>

    <!-- Sticky Save Bar for Mobile (Side-by-Side Clean Layout) -->
    <div class="mobile-sticky-save-bar d-flex d-md-none align-items-center" style="gap: 12px;">
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary py-2 flex-grow-1" style="border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
            Batal
        </a>
        <button type="submit" class="btn btn-primary py-2 flex-grow-1 font-weight-bold" style="border-radius: 20px; font-size: 0.85rem;">
            Simpan
        </button>
    </div>
</form>

<style>
  /* Clean Enterprise Row List Layout */
  .perm-list-row {
    border-bottom: 1px solid var(--rule-soft, rgba(0, 0, 0, 0.05));
    transition: background-color 0.15s ease, border-color 0.15s ease;
    cursor: pointer;
    user-select: none;
  }

  .perm-list-row:last-child {
    border-bottom: none;
  }

  .perm-list-row:hover {
    background-color: var(--color-paper-1, #f8fafc);
  }

  body.dark-mode .perm-list-row:hover {
    background-color: rgba(30, 41, 59, 0.4);
  }

  .perm-list-row.is-checked {
    background-color: rgba(99, 102, 241, 0.03);
    border-left: 3px solid #6366f1;
  }

  body.dark-mode .perm-list-row.is-checked {
    background-color: rgba(99, 102, 241, 0.06);
    border-left: 3px solid #818cf8;
  }

  .avatar-badge {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.88rem;
  }
  .bg-info-soft { background: rgba(14, 165, 233, 0.1); }
  .bg-success-soft { background: rgba(16, 185, 129, 0.1); }
  .bg-warning-soft { background: rgba(245, 158, 11, 0.1); }

  /* Premium Mobile Sticky Action Bar */
  .mobile-sticky-save-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 1040;
    background: var(--color-paper-0, #ffffff);
    padding: 10px 16px;
    border-top: 1px solid var(--rule-soft, rgba(0, 0, 0, 0.1));
    box-shadow: 0 -8px 24px rgba(15, 23, 42, 0.12);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
  }

  body.dark-mode .mobile-sticky-save-bar {
    background: rgba(15, 23, 42, 0.94);
    border-color: rgba(255, 255, 255, 0.12);
    box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.6);
  }

  #search-permissions::placeholder {
    color: var(--color-ink-3, #94a3b8);
    opacity: 0.9;
  }

  @media (max-width: 767.98px) {
    .perm-list-row {
      padding: 11px 14px !important;
    }
    .perm-title {
      font-size: 0.84rem !important;
    }
    body {
      padding-bottom: 80px !important;
    }
    .main-footer {
      padding-bottom: 80px !important;
      margin-bottom: 0 !important;
    }
  }
</style>

@push('scripts')
<script>
  $(document).ready(function() {
    // Row Click to Toggle Switch
    $(document).on('click', '.perm-list-row', function(e) {
      if ($(e.target).is('input[type="checkbox"]') || $(e.target).is('label')) {
        return;
      }
      const checkbox = $(this).find('.perm-switch');
      const newState = !checkbox.is(':checked');
      checkbox.prop('checked', newState).trigger('change');
    });

    // Update Checked Row Highlight Style
    $(document).on('change', '.perm-switch', function() {
      const row = $(this).closest('.perm-list-row');
      if ($(this).is(':checked')) {
        row.addClass('is-checked');
      } else {
        row.removeClass('is-checked');
      }
    });

    // Live Search Filter
    $('#search-permissions').on('keyup', function() {
      const val = $(this).val().toLowerCase().trim();
      let visible = 0;
      $('.perm-list-row').each(function() {
        const text = $(this).data('label');
        if (text.indexOf(val) !== -1) {
          $(this).show();
          visible++;
        } else {
          $(this).hide();
        }
      });
      $('#visible-perm-count').text(visible);
    });

    // Group Toggle Buttons
    $('.toggle-group-btn').on('click', function() {
      const targetSelector = $(this).data('target');
      const switches = $(targetSelector);
      const allChecked = switches.filter(':checked').length === switches.length;
      switches.prop('checked', !allChecked).trigger('change');
    });

    // Global Select All Buttons
    $('#btn-select-all-global').on('click', function() {
      $('.perm-switch').prop('checked', true).trigger('change');
    });

    $('#btn-deselect-all-global').on('click', function() {
      $('.perm-switch').prop('checked', false).trigger('change');
    });
  });
</script>
@endpush
@endsection
