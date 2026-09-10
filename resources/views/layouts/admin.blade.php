<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $app_name ?? 'ITAM Enterprise' }} | @yield('title', 'Dashboard')</title>
  
  <!-- Favicon & PWA App Manifest -->
  <link rel="icon" href="{{ vasset('logo-192.png') }}" type="image/png" sizes="192x192">
  <link rel="shortcut icon" href="{{ vasset('logo-192.png') }}" type="image/png">
  <link rel="manifest" href="{{ vasset('manifest.json') }}">
  <meta name="theme-color" content="#0f172a">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="ITAM Enterprise">
  <link rel="apple-touch-icon" href="{{ vasset('logo-192.png') }}" sizes="192x192">

  <!-- Google Fonts: Plus Jakarta Sans + JetBrains Mono -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- AdminLTE -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <!-- Select2 -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
  <!-- Flatpickr -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

  <!-- ═══════════════════════════════════════════════════════
       ITAM Enterprise — NextGen UI Theme
       ══════════════════════════════════════════════════════ -->
  <link rel="stylesheet" href="{{ vasset('css/itam-theme.css') }}">
  @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed {{ session('theme', 'dark') }}-mode" style="min-height: 100vh;">
<!-- Site wrapper -->
<div class="wrapper">

  <!-- Top Navbar (Hallmark Anti-AI-Slop Redesign) -->
  <nav class="main-header navbar navbar-expand navbar-light">
    <!-- Left: toggle & mobile brand logo -->
    <ul class="navbar-nav align-items-center" style="display: flex; flex-direction: row; align-items: center; gap: 12px;">
      <li class="nav-item">
        <a class="nav-link pushmenu-btn" data-widget="pushmenu" href="#" role="button" title="Toggle Navigation Sidebar">
          <i class="fas fa-bars" style="font-size: 0.95rem;"></i>
        </a>
      </li>
      <li class="nav-item d-flex d-md-none align-items-center ml-1">
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-decoration-none" style="gap: 10px;">
          <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3); display: flex; align-items: center; justify-content: center;">
            <img src="{{ vasset('logo.png') }}" alt="Logo" loading="lazy" decoding="async" style="max-height: 20px; max-width: 20px; object-fit: contain;">
          </div>
          <div class="d-flex flex-column" style="line-height: 1.1;">
            <span style="font-weight: 700; font-size: 0.9rem; color: var(--color-ink-0);">{{ $app_name ?? 'ITAM' }}</span>
          </div>
        </a>
      </li>
    </ul>

    <!-- Right: controls -->
    <ul class="navbar-nav ml-auto" style="gap: 10px; padding-right: 4px; display: flex; align-items: center; flex-direction: row;">

      <!-- Real-Time Jakarta (WIB) Clock Pill -->
      <li class="nav-item d-none d-sm-block">
        <div class="header-clock-pill" title="{{ App::getLocale() == 'en' ? 'Jakarta Real-Time Clock (WIB)' : 'Waktu Nyata Jakarta (WIB)' }}">
          <span class="live-dot" style="width: 7px; height: 7px; border-radius: 50%; background: #10b981; box-shadow: 0 0 8px #10b981; display: inline-block;"></span>
          <i class="far fa-clock text-info" style="font-size: 0.8rem;"></i>
          <span id="header-jakarta-clock">--:--:-- WIB</span>
        </div>
      </li>

      <!-- Language Dropdown (Hallmark Redesign) -->
      <li class="nav-item dropdown">
        <a class="header-lang-btn" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <div class="lang-icon-tile">
            <i class="fas fa-globe"></i>
          </div>
          <span>{{ App::getLocale() == 'id' ? 'ID' : 'EN' }}</span>
          <i class="fas fa-chevron-down" style="font-size: 0.6rem; opacity: 0.7;"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right p-1 header-lang-menu" style="border-radius: 14px; min-width: 175px; overflow: hidden;">
          <a class="dropdown-item d-flex align-items-center py-2 px-3" href="{{ route('lang.switch', 'id') }}" style="gap: 10px; font-size: 0.85rem; border-radius: 8px; transition: all 0.2s; background: {{ App::getLocale() == 'id' ? 'rgba(99, 102, 241, 0.15)' : 'transparent' }}; color: {{ App::getLocale() == 'id' ? '#818cf8 !important' : 'var(--color-ink-0) !important' }}; font-weight: 600;">
            <i class="fas fa-check text-success" style="font-size: 0.75rem; visibility: {{ App::getLocale() == 'id' ? 'visible' : 'hidden' }};"></i>
            <span>Bahasa Indonesia</span>
          </a>
          <div class="dropdown-divider my-1"></div>
          <a class="dropdown-item d-flex align-items-center py-2 px-3" href="{{ route('lang.switch', 'en') }}" style="gap: 10px; font-size: 0.85rem; border-radius: 8px; transition: all 0.2s; background: {{ App::getLocale() == 'en' ? 'rgba(99, 102, 241, 0.15)' : 'transparent' }}; color: {{ App::getLocale() == 'en' ? '#818cf8 !important' : 'var(--color-ink-0) !important' }}; font-weight: 600;">
            <i class="fas fa-check text-success" style="font-size: 0.75rem; visibility: {{ App::getLocale() == 'en' ? 'visible' : 'hidden' }};"></i>
            <span>English</span>
          </a>
        </div>
      </li>

      @auth
      <!-- User Profile Dropdown Menu (Hallmark Redesign) -->
      <li class="nav-item dropdown user-menu">
        <a href="#" class="header-user-btn dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
          <div class="user-avatar-container">
            @if(Auth::user()->avatar)
              <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="user-avatar-ring">
            @else
              <div class="user-avatar-fallback">
                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
              </div>
            @endif
            <span class="user-online-dot"></span>
          </div>
          <span class="user-name-text theme-text" style="font-weight: 700; font-size: 0.85rem;">
            {{ Auth::user()->name ?? __('messages.user') }}
          </span>
          <i class="fas fa-chevron-down" style="font-size: 0.65rem; color: #94a3b8;"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right user-dropdown-menu">
          <!-- User Header Info Card -->
          <div class="p-3 text-center user-dropdown-header-card">
            @if(Auth::user()->avatar)
              <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="mx-auto mb-2 user-avatar-ring" style="width: 52px !important; height: 52px !important;">
            @else
              <div class="mx-auto mb-2 user-avatar-fallback" style="width: 52px !important; height: 52px !important; font-size: 1.3rem !important;">
                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
              </div>
            @endif
            <div class="user-dropdown-name">{{ Auth::user()->name ?? __('messages.user') }}</div>
            <div class="user-dropdown-email">{{ Auth::user()->email ?? '' }}</div>
            <div class="mt-2">
              <span class="badge badge-pill" style="background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3); font-size: 0.7rem; font-weight: 600; padding: 4px 12px; font-family: var(--font-mono);">
                <i class="fas fa-user-shield mr-1"></i> {{ Auth::user()->getRoleNames()->first() ?? 'User' }}
              </span>
            </div>
          </div>

          <!-- Menu Items -->
          <div class="p-2">
            <!-- Theme Toggle Option -->
            <a href="{{ session('theme', 'dark') == 'dark' ? route('theme.switch', 'light') : route('theme.switch', 'dark') }}" 
               class="user-dropdown-item justify-content-between">
              <div class="d-flex align-items-center" style="gap: 10px;">
                <div style="width: 28px; height: 28px; border-radius: 8px; background: rgba(99, 102, 241, 0.12); color: #818cf8; display: flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                  <i class="fas {{ session('theme', 'dark') == 'dark' ? 'fa-sun' : 'fa-moon' }}"></i>
                </div>
                <span>{{ session('theme', 'dark') == 'dark' ? __('messages.light_mode') : __('messages.dark_mode') }}</span>
              </div>
              <span class="badge badge-pill" style="font-size: 0.7rem; background: rgba(148, 163, 184, 0.12); color: #94a3b8; font-weight: 600; font-family: var(--font-mono);">
                {{ session('theme', 'dark') == 'dark' ? 'Light' : 'Dark' }}
              </span>
            </a>

            <!-- User Profile Link -->
            <a href="{{ route('profile.edit') }}" class="user-dropdown-item">
              <div style="width: 28px; height: 28px; border-radius: 8px; background: rgba(6, 182, 212, 0.12); color: #38bdf8; display: flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                <i class="fas fa-id-badge"></i>
              </div>
              <span>{{ __('messages.user_profile') }}</span>
            </a>

            <!-- Change Password Link -->
            <a href="#" class="user-dropdown-item" data-toggle="modal" data-target="#changePasswordModal">
              <div style="width: 28px; height: 28px; border-radius: 8px; background: rgba(245, 158, 11, 0.12); color: #fbbf24; display: flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                <i class="fas fa-key"></i>
              </div>
              <span>{{ __('messages.change_password') }}</span>
            </a>

            <div class="dropdown-divider my-1" style="border-top: 1px solid rgba(148, 163, 184, 0.12);"></div>

            <!-- Logout Link -->
            <a href="{{ route('logout') }}" class="user-dropdown-item text-danger">
              <div style="width: 28px; height: 28px; border-radius: 8px; background: rgba(239, 68, 68, 0.12); color: #f87171; display: flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                <i class="fas fa-sign-out-alt"></i>
              </div>
              <span>{{ __('messages.logout') }}</span>
            </a>
          </div>
        </div>
      </li>

      @else
      <li class="nav-item">
        <a href="{{ route('login') }}" class="header-lang-btn">
          <i class="fas fa-sign-in-alt text-primary" style="font-size: 0.85rem;"></i>
          <span>{{ __('messages.login') ?? 'Login' }}</span>
        </a>
      </li>
      @endauth
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar -->
  <aside class="main-sidebar sidebar-light-primary elevation-0">
    <!-- Brand Header (Hallmark Redesign) -->
    <a href="{{ route('dashboard') }}" class="brand-link d-flex align-items-center" style="text-decoration: none;">
      <div class="brand-logo-wrapper">
        <img src="{{ vasset('logo.png') }}" alt="Logo" loading="lazy" decoding="async" style="max-height: 24px; max-width: 24px; object-fit: contain;">
      </div>
      <div class="d-flex flex-column" style="line-height: 1.15; min-width: 0;">
        <span class="brand-text text-truncate">{{ $app_name ?? 'ITAM Enterprise' }}</span>
      </div>
    </a>

    <!-- Sidebar -->
    <div class="sidebar" style="background: transparent;">
      <nav class="mt-3">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

          @can('menu_dashboard')
          <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>{{ __('messages.dashboard') }}</p>
            </a>
          </li>
          @endcan

          
          @canany(['menu_departments', 'menu_brands', 'menu_locations', 'menu_categories'])
          <li class="nav-header">{{ __('messages.master_data') }}</li>

          <li class="nav-item {{ request()->routeIs('departments.*', 'positions.*', 'brands.*', 'locations.*', 'categories.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('departments.*', 'positions.*', 'brands.*', 'locations.*', 'categories.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-database"></i>
              <p>
                {{ __('messages.master_data') }}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @can('menu_departments')
              <li class="nav-item">
                <a href="{{ route('departments.index') }}" class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('messages.department') }}</p>
                </a>
              </li>
              @endcan
              @can('menu_brands')
              <li class="nav-item">
                <a href="{{ route('brands.index') }}" class="nav-link {{ request()->routeIs('brands.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('messages.brand') }}</p>
                </a>
              </li>
              @endcan
              @can('menu_locations')
              <li class="nav-item">
                <a href="{{ route('locations.index') }}" class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('messages.location') }}</p>
                </a>
              </li>
              @endcan
              @can('menu_categories')
              <li class="nav-item">
                <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('messages.category') }}</p>
                </a>
              </li>
              @endcan
            </ul>
          </li>
          @endcanany

          @can('menu_employees')
          <li class="nav-item">
            <a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-users"></i>
              <p>{{ __('messages.employee') }}</p>
            </a>
          </li>
          @endcan

          <li class="nav-header">{{ __('messages.it_assets') }}</li>

          <!-- Asset Menu -->
          @can('menu_assets')
          <li class="nav-item {{ request()->routeIs('assets.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-laptop"></i>
              <p>
                {{ __('messages.it_assets') }}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item"><a href="{{ route('assets.index') }}" class="nav-link {{ request()->routeIs('assets.index') && !request('category') && !request('status') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>{{ __('messages.all_assets') }}</p></a></li>
              @php
                  $fixedCategories = ['Accessories', 'Computer', 'Network', 'Printer', 'Storage', 'Other IT Asset'];
                  $catTranslationMap = [
                      'Accessories' => __('messages.cat_accessories'),
                      'Computer' => __('messages.cat_computer'),
                      'Network' => __('messages.cat_network'),
                      'Printer' => __('messages.cat_printer'),
                      'Storage' => __('messages.cat_storage'),
                      'Other IT Asset' => __('messages.cat_other_it_asset'),
                  ];
              @endphp
              @foreach($fixedCategories as $catName)
              <li class="nav-item">
                  <a href="{{ route('assets.index', ['category' => $catName]) }}" class="nav-link {{ request()->routeIs('assets.index') && request('category') == $catName ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ $catTranslationMap[$catName] ?? $catName }}</p>
                  </a>
              </li>
              @endforeach
            </ul>
          </li>

          @endcan

          <li class="nav-header">{{ __('messages.operations') }}</li>

          @can('menu_assignments')
          <li class="nav-item">
            <a href="{{ route('assignments.index') }}" class="nav-link {{ request()->routeIs('assignments.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-exchange-alt"></i>
              <p>{{ __('messages.assignment') }}</p>
            </a>
          </li>
          @endcan
          @can('menu_maintenances')
          <li class="nav-item">
            <a href="{{ route('maintenances.index') }}" class="nav-link {{ request()->routeIs('maintenances.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tools"></i>
              <p>{{ __('messages.maintenance') }}</p>
            </a>
          </li>
          @endcan
          @can('menu_predictive_health')
          <li class="nav-item">
            <a href="{{ route('predictive-health.index') }}" class="nav-link {{ request()->routeIs('predictive-health.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-chart-line text-info"></i>
              <p>{{ __('messages.predictive_health') }}</p>
            </a>
          </li>
          @endcan
          @can('menu_tickets')
          <li class="nav-item">
            <a href="{{ route('tickets.index') }}" class="nav-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-ticket-alt"></i>
              <p>{{ __('messages.ticket') ?? 'Ticket' }}</p>
            </a>
          </li>
          @endcan
          @can('menu_pics')
          <li class="nav-item">
            <a href="{{ route('pics.index') }}" class="nav-link {{ request()->routeIs('pics.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-tie"></i>
              <p>{{ __('messages.pic_data') }}</p>
            </a>
          </li>
          @endcan

          <!-- Network -->
          @canany(['menu_ips', 'menu_vlans', 'menu_network_anomalies'])
          <li class="nav-item {{ request()->routeIs('ips.*', 'vlans.*', 'network-anomalies.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('ips.*', 'vlans.*', 'network-anomalies.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-network-wired"></i>
              <p>
                {{ __('messages.network_ip') }}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @can('menu_ips')
              <li class="nav-item">
                <a href="{{ route('ips.index') }}" class="nav-link {{ request()->routeIs('ips.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('messages.ip_address') }}</p>
                </a>
              </li>
              @endcan
              @can('menu_vlans')
              <li class="nav-item">
                <a href="{{ route('vlans.index') }}" class="nav-link {{ request()->routeIs('vlans.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('messages.vlan_config') }}</p>
                </a>
              </li>
              @endcan
              @can('menu_network_anomalies')
              <li class="nav-item">
                <a href="{{ route('network-anomalies.index') }}" class="nav-link {{ request()->routeIs('network-anomalies.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon text-info"></i>
                  <p>{{ __('messages.network_anomalies') }}</p>
                </a>
              </li>
              @endcan
            </ul>
          </li>
          @endcanany

          @can('menu_software_licenses')
          <li class="nav-item">
            <a href="{{ route('software_licenses.index') }}" class="nav-link {{ request()->routeIs('software_licenses.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-key"></i>
              <p>{{ __('messages.software_license') }}</p>
            </a>
          </li>
          @endcan

          @can('menu_password_vaults')
          <li class="nav-item">
            <a href="{{ route('password_vaults.index') }}" class="nav-link {{ request()->routeIs('password_vaults.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-lock"></i>
              <p>{{ __('messages.password_vault') }}</p>
            </a>
          </li>
          @endcan

          @can('menu_budget_planner')
          <li class="nav-item">
            <a href="{{ route('budget-planner.index') }}" class="nav-link {{ request()->routeIs('budget-planner.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-calculator text-success"></i>
              <p>{{ __('messages.budget_planner') }}</p>
            </a>
          </li>
          @endcan

          
          @canany(['menu_users', 'menu_settings', 'menu_roles'])
          <li class="nav-header">{{ __('messages.system') }}</li>

          @can('menu_users')
          <li class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-shield"></i>
              <p>{{ __('messages.user_management') ?? 'Login Management' }}</p>
            </a>
          </li>
          @endcan
          @can('menu_settings')
          <li class="nav-item">
            <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-cogs"></i>
              <p>{{ __('messages.setting') ?? 'Setting' }}</p>
            </a>
          </li>
          @endcan

          @can('menu_roles')
          <li class="nav-item">
            <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-shield"></i>
              <p>{{ __('messages.roles_permissions') }}</p>
            </a>
          </li>
          @endcan

          @if(auth()->check() && (auth()->user()->hasRole('Super Admin') || auth()->user()->can('menu_settings')))
          <li class="nav-item">
            <a href="{{ route('system-performance.index') }}" class="nav-link {{ request()->routeIs('system-performance.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-bolt text-warning"></i>
              <p>{{ __('messages.system_performance') }}</p>
            </a>
          </li>
          @endif
          @endcanany


        </ul>
      </nav>
    </div>
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper" style="background: transparent; padding-top: 66px !important; margin-top: 0 !important;">
    <!-- Main content -->
    <section class="content" style="padding-top: 16px !important;">
      <div class="container-fluid pb-2">
        @yield('content')
      </div>
    </section>
  </div>
  <!-- /.content-wrapper -->

  <!-- Hallmark Anti-AI-Slop Footer (Border-Free Pure Typography Edition) -->
  <footer class="main-footer hallmark-footer">
    <div class="hallmark-footer-inner">
      <!-- Left: Identity & System Telemetry -->
      <div class="hallmark-foot-group hallmark-foot-left">
        <a href="{{ route('dashboard') }}" class="hallmark-brand-link">
          <span class="hallmark-brand-name">{{ $app_name ?? 'ITAM Enterprise' }}</span>
        </a>
        <span class="hallmark-ver-badge">v2.4 LTS</span>
        <span class="hallmark-foot-sep d-none d-sm-inline">·</span>
        <span class="hallmark-status-pill d-none d-sm-inline-flex" title="{{ __('messages.system_operational') }}">
          <span class="hallmark-live-dot"></span>
          <span class="hallmark-status-label">{{ __('messages.system_operational') }}</span>
        </span>
      </div>

      <!-- Right: Legal & System Meta -->
      <div class="hallmark-foot-group hallmark-foot-right">
        <div class="hallmark-foot-item hallmark-foot-legal">
          <span>&copy; {{ date('Y') }}</span>
          <span class="hallmark-company-name">{{ $company_name ?? 'Team IT Pabrik' }}</span>
          <span class="hallmark-foot-sep d-none d-xl-inline">·</span>
          <span class="hallmark-rights-text d-none d-xl-inline">{{ __('messages.all_rights_reserved') }}</span>
        </div>
        <span class="hallmark-foot-sep">·</span>
        <div class="hallmark-foot-item hallmark-foot-meta">
          <span class="hallmark-meta-item hallmark-sec-link d-none d-md-inline-flex">
            <i class="fas fa-shield-alt hallmark-sec-icon"></i>
            <span>{{ __('messages.security_privacy') }}</span>
          </span>
          <span class="hallmark-foot-sep hallmark-sec-sep d-none d-md-inline">·</span>
          <span class="hallmark-meta-item hallmark-creator-credit">
            <span class="hallmark-crafted-by d-none d-md-inline">{{ __('messages.crafted_by') }} </span>
            <strong class="hallmark-creator-name">Asoyy Dev</strong>
          </span>
        </div>
      </div>
    </div>
  </footer>
</div>
<!-- ./wrapper -->

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
  window.copyToClipboard = function(text) {
    if (navigator.clipboard && window.isSecureContext) {
      return navigator.clipboard.writeText(text);
    } else {
      return new Promise(function(resolve, reject) {
        try {
          var textArea = document.createElement("textarea");
          textArea.value = text;
          textArea.style.position = "fixed";
          textArea.style.left = "-999999px";
          textArea.style.top = "-999999px";
          textArea.setAttribute("readonly", "");
          document.body.appendChild(textArea);
          textArea.focus();
          textArea.select();
          var successful = document.execCommand('copy');
          document.body.removeChild(textArea);
          if (successful) {
            resolve();
          } else {
            reject(new Error('execCommand copy failed'));
          }
        } catch (err) {
          if (typeof textArea !== 'undefined' && textArea.parentNode) {
            document.body.removeChild(textArea);
          }
          reject(err);
        }
      });
    }
  };

  $(document).ready(function() {
    $('.select2').select2({ width: 'resolve' });
    
    // Initialize Flatpickr for elements with .flatpickr-datetime
    flatpickr(".flatpickr-datetime", {
        enableTime: true,
        dateFormat: "Y-m-d\\TH:i",
        time_24hr: true,
        altInput: true,
        altFormat: "d M Y H:i",
    });
    $(document).on('select2:open', () => {
      document.querySelector('.select2-search__field').focus();
    });
  });

  $(document).on('click', '.btn-delete', function(e) {
    e.preventDefault();
    var form = $(this).closest('form');
    var message = $(this).data('confirm-message') || "{{ __('messages.confirm_delete') ?? 'Apakah Anda yakin ingin menghapus data ini?' }}";
    Swal.fire({
      title: "{{ __('messages.deleting_data') ?? 'Hapus Data?' }}",
      text: message,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#64748b',
      confirmButtonText: "{{ __('messages.yes_delete') ?? 'Ya, Hapus' }}",
      cancelButtonText: "{{ __('messages.cancel') ?? 'Batal' }}",
      reverseButtons: true,
      customClass: {
        popup: 'swal2-minimal-rounded'
      }
    }).then((result) => {
      if (result.isConfirmed) { form.submit(); }
    });
  });

  // Fix modal stacking context & positioning by appending to document.body on open
  $(document).on('show.bs.modal', '.modal', function() {
    if (!$(this).parent().is('body')) {
      $(this).appendTo('body');
    }
  });

  $(document).on('click', '.file-dropzone', function(e) {
    if (!$(e.target).is('input[type="file"]')) {
      $(this).find('input[type="file"]').trigger('click');
    }
  });

  $(document).on('change', '.import-file-input', function(e) {
    var fileName = e.target.files[0] ? e.target.files[0].name : 'Sentuh di sini untuk memilih file';
    var dropzone = $(this).closest('.file-dropzone');
    dropzone.find('.file-name-display').text(fileName).addClass('text-info font-weight-bold').removeClass('text-muted');
    dropzone.addClass('active');
  });

  // Seamless AJAX search and pagination
  function loadTableData(url, btn) {
    let originalIcon = btn ? btn.html() : null;
    if (btn) btn.html('<i class="fas fa-spinner fa-spin"></i>');

    $.ajax({
      url: url,
      type: 'GET',
      success: function(response) {
        let parser = new DOMParser();
        let doc = parser.parseFromString(response, 'text/html');
        
        let newTable = $(doc).find('.table-responsive');
        let newPagination = $(doc).find('.card-footer');
        
        if (newTable.length) {
          $('.table-responsive').replaceWith(newTable);
          
          let currentFooter = $('.card-footer');
          if (currentFooter.length && newPagination.length) {
            currentFooter.replaceWith(newPagination);
          } else if (currentFooter.length) {
            currentFooter.remove(); // No pagination on new result
          } else if (newPagination.length) {
            $('.table-responsive').closest('.card').append(newPagination);
          }
          
          window.history.pushState({}, '', url);
        }
        if (btn) btn.html(originalIcon);
      },
      error: function() {
        if (btn) btn.closest('form').submit(); // Fallback
        else window.location.href = url;
      }
    });
  }

  function triggerAjaxSearch(form) {
    let url = form.attr('action') || window.location.href.split('?')[0];
    let params = form.serialize();
    loadTableData(url + '?' + params, form.find('button[type="submit"]'));
  }

  let searchTimeout;
  $(document).on('input', 'input[name="search"]', function() {
    let form = $(this).closest('form');
    if (form.length) {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(function() {
        triggerAjaxSearch(form);
      }, 500);
    }
  });

  $(document).on('change', 'form:has(input[name="search"]) select', function() {
    let form = $(this).closest('form');
    if (form.length) {
      triggerAjaxSearch(form);
    }
  });

  $(document).on('click', '.card-footer .pagination a', function(e) {
    e.preventDefault();
    loadTableData($(this).attr('href'), null);
  });

  // AI Search Modal Handler
  function executeAiQuery(prompt) {
    if (!prompt || prompt.trim().length < 2) return;

    const btn = $('#btn-submit-ai-query');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> {{ App::getLocale() == "id" ? "Memproses..." : "Processing..." }}');

    $.ajax({
      url: "{{ route('ai.query-search') }}",
      type: "POST",
      data: {
        _token: "{{ csrf_token() }}",
        prompt: prompt
      },
      success: function(response) {
        btn.prop('disabled', false).html('<i class="fas fa-search mr-1"></i> {{ __("messages.ask_ai_btn") }}');
        if (response.success && response.result) {
          const res = response.result;
          $('#ai-query-summary-title').text(res.summary);

          let containerHtml = '';
          if (res.answer) {
            containerHtml += '<div class="p-3 rounded mb-3 theme-text" style="background: var(--color-paper-1); font-size: 0.875rem; border-left: 3px solid #818cf8; line-height: 1.5; white-space: pre-wrap;">' + res.answer + '</div>';
          }

          if (res.items && res.items.length > 0) {
            containerHtml += '<div class="list-group shadow-sm" style="border-radius: var(--radius-md); overflow: hidden;">';
            res.items.forEach(item => {
              containerHtml += `
                <a href="${item.url}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3" style="background: var(--color-paper-0); border-color: var(--rule-soft);">
                  <div>
                    <h6 class="mb-1 font-weight-bold theme-text">${item.title}</h6>
                    <small class="text-muted">${item.subtitle}</small>
                  </div>
                  <span class="badge ${item.badge} px-3 py-2" style="font-weight: 600; font-size: 0.75rem;">${item.badge_text}</span>
                </a>
              `;
            });
            containerHtml += '</div>';
          }

          if (res.sources && res.sources.length > 0) {
            containerHtml += renderAiSources(res.sources);
          }

          $('#ai-query-items-container').html(containerHtml);
          $('#ai-query-output-box').slideDown();
        }
      },
      error: function(err) {
        btn.prop('disabled', false).html('<i class="fas fa-search mr-1"></i> {{ __("messages.ask_ai_btn") }}');
        console.error(err);
      }
    });
  }

  $(document).on('click', '#btn-submit-ai-query', function() {
    executeAiQuery($('#ai-prompt-input').val());
  });

  $(document).on('keypress', '#ai-prompt-input', function(e) {
    if (e.which === 13) {
      e.preventDefault();
      executeAiQuery($(this).val());
    }
  });

  $(document).on('click', '.ai-chip-btn', function() {
    const prompt = $(this).data('prompt');
    $('#ai-prompt-input').val(prompt);
    executeAiQuery(prompt);
  });
</script>

<!-- ITAM AI Conversational Chatbot Widget Drawer -->
<div id="aiChatbotDrawer" class="ai-chatbot-drawer" role="dialog" aria-modal="true" aria-label="{{ __('messages.ai_modal_title') }}">
  <!-- Mobile Drag / Handle Bar -->
  <div class="ai-chat-mobile-handle d-md-none">
    <span class="ai-handle-pill"></span>
  </div>

  <!-- Header -->
  <div class="ai-chat-header">
    <div class="ai-chat-header-info">
      <div class="ai-chat-avatar">
        <i class="fas fa-robot"></i>
      </div>
      <div>
        <div class="d-flex align-items-center" style="gap: 6px;">
          <h6 class="ai-chat-title">{{ __('messages.ai_modal_title') }}</h6>
          <span class="ai-model-tag">v2.5</span>
        </div>
        <div class="ai-chat-status">
          <span class="ai-status-pulse"></span>
          <span>{{ __('messages.online') }}</span>
        </div>
      </div>
    </div>
    <div class="ai-header-actions">
      <button type="button" class="ai-header-btn" id="btn-clear-chat-history" title="{{ __('messages.clear_chat_history') }}" aria-label="{{ __('messages.clear_chat_history') }}">
        <i class="fas fa-trash-alt"></i>
      </button>
      <button type="button" class="ai-header-btn ai-close-btn" id="btn-close-chat-drawer" title="{{ __('messages.close') }} (Esc)" aria-label="{{ __('messages.close') }}">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </div>

  <!-- Chat Stream Body -->
  <div class="ai-chat-body" id="ai-chat-stream-body" tabindex="0">
    <!-- Messages & Cards will be injected dynamically via JS -->
  </div>

  <!-- Footer Input Area -->
  <div class="ai-chat-footer">
    <div class="ai-chat-input-wrap">
      <input type="text" id="ai-chat-input" autocomplete="off" placeholder="{{ __('messages.ai_placeholder') }}" aria-label="{{ __('messages.ai_placeholder') }}">
      <button type="button" id="btn-send-chat" title="{{ __('messages.send_message') }}" aria-label="{{ __('messages.send_message') }}">
        <i class="fas fa-paper-plane"></i>
      </button>
    </div>
    <div class="ai-input-hint d-none d-md-flex justify-content-between align-items-center">
      <span>{{ __('messages.press_enter_hint') }}</span>
      <span class="ai-mono-hint">ITAM Intelligence</span>
    </div>
  </div>
</div>

<style>
  /* ==========================================================================
     Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5
     ITAM AI CONVERSATIONAL CHATBOT DRAWER (HALLMARK REDESIGN)
     ========================================================================== */
  .ai-chatbot-drawer {
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 420px;
    max-width: calc(100vw - 32px);
    height: 610px;
    max-height: calc(100vh - 90px);
    z-index: 1050;
    display: flex;
    flex-direction: column;
    border-radius: var(--radius-xl, 20px);
    background: var(--color-paper-0, #0f172a);
    border: 1px solid var(--rule-soft, rgba(148, 163, 184, 0.2));
    box-shadow: 0 24px 60px -12px rgba(15, 23, 42, 0.45), 0 0 0 1px rgba(148, 163, 184, 0.12);
    opacity: 0;
    visibility: hidden;
    transform: translateY(20px) scale(0.96);
    transition: opacity 0.22s cubic-bezier(0.16, 1, 0.3, 1),
                transform 0.22s cubic-bezier(0.16, 1, 0.3, 1),
                visibility 0.22s ease;
    pointer-events: none;
    overflow: hidden;
    font-family: var(--font-body, 'Plus Jakarta Sans', sans-serif);
  }

  body.light-mode .ai-chatbot-drawer {
    background: #ffffff;
    border-color: rgba(203, 213, 225, 0.85);
    box-shadow: 0 20px 50px -10px rgba(15, 23, 42, 0.18), 0 0 0 1px rgba(226, 232, 240, 0.9);
  }

  .ai-chatbot-drawer.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
    pointer-events: auto;
  }

  /* Mobile Handle Bar */
  .ai-chat-mobile-handle {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 8px 0 4px;
    background: var(--color-paper-1, #1e293b);
    cursor: pointer;
  }
  .ai-handle-pill {
    width: 36px;
    height: 4px;
    border-radius: 999px;
    background: var(--color-ink-3, #64748b);
    opacity: 0.5;
  }

  /* Header */
  .ai-chat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    background: var(--color-paper-1, #1e293b);
    border-bottom: 1px solid var(--rule-soft, rgba(148, 163, 184, 0.15));
    flex-shrink: 0;
  }

  body.light-mode .ai-chat-header {
    background: #f8fafc;
    border-color: #e2e8f0;
  }

  .ai-chat-header-info {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .ai-chat-avatar {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: rgba(99, 102, 241, 0.14);
    border: 1px solid rgba(99, 102, 241, 0.3);
    color: #818cf8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.05rem;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.18);
    flex-shrink: 0;
  }

  body.light-mode .ai-chat-avatar {
    background: rgba(79, 70, 229, 0.1);
    border-color: rgba(79, 70, 229, 0.25);
    color: #4f46e5;
  }

  .ai-chat-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--color-ink-0, #f8fafc);
    margin: 0;
    line-height: 1.2;
    letter-spacing: -0.01em;
  }

  body.light-mode .ai-chat-title {
    color: #0f172a;
  }

  .ai-model-tag {
    font-family: var(--font-mono, 'JetBrains Mono', monospace);
    font-size: 0.65rem;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 6px;
    background: rgba(99, 102, 241, 0.15);
    color: #818cf8;
    border: 1px solid rgba(99, 102, 241, 0.25);
    letter-spacing: 0.02em;
  }

  body.light-mode .ai-model-tag {
    background: rgba(79, 70, 229, 0.08);
    color: #4f46e5;
    border-color: rgba(79, 70, 229, 0.2);
  }

  .ai-chat-status {
    font-size: 0.72rem;
    color: #10b981;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 2px;
  }

  .ai-status-pulse {
    display: inline-block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 8px #10b981;
    animation: statusPulse 2s infinite ease-in-out;
  }

  @keyframes statusPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(0.8); }
  }

  .ai-header-actions {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .ai-header-btn {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: transparent;
    border: 1px solid transparent;
    color: var(--color-ink-2, #94a3b8);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.88rem;
    cursor: pointer;
    transition: all 0.18s ease;
    outline: none !important;
  }

  .ai-header-btn:hover {
    background: rgba(239, 68, 68, 0.14);
    border-color: rgba(239, 68, 68, 0.3);
    color: #ef4444;
    transform: translateY(-1px);
  }

  .ai-header-btn:active {
    transform: translateY(1px);
  }

  .ai-header-btn.ai-close-btn:hover {
    background: var(--color-paper-2, rgba(255,255,255,0.1));
    border-color: var(--rule-soft, rgba(148, 163, 184, 0.2));
    color: var(--color-ink-0, #f8fafc);
  }

  body.light-mode .ai-header-btn.ai-close-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
  }

  /* Stream Body */
  .ai-chat-body {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    background: var(--color-paper-0, #0f172a);
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
  }

  body.light-mode .ai-chat-body {
    background: #ffffff;
  }

  .ai-chat-body::-webkit-scrollbar {
    width: 6px;
  }
  .ai-chat-body::-webkit-scrollbar-track {
    background: transparent;
  }
  .ai-chat-body::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.25);
    border-radius: 4px;
  }
  .ai-chat-body::-webkit-scrollbar-thumb:hover {
    background: rgba(148, 163, 184, 0.4);
  }

  /* Messages */
  .chat-msg {
    display: flex;
    flex-direction: column;
    max-width: 92%;
    animation: fadeInMsg 0.22s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  }

  @keyframes fadeInMsg {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .chat-msg-user {
    align-self: flex-end;
  }

  .chat-msg-user .chat-bubble {
    background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    color: #ffffff;
    border-radius: 18px 18px 4px 18px;
    padding: 10px 15px;
    font-size: 0.88rem;
    line-height: 1.5;
    box-shadow: 0 4px 14px rgba(79, 70, 229, 0.28);
    border: 1px solid rgba(255, 255, 255, 0.12);
    word-break: break-word;
  }

  .chat-msg-bot {
    align-self: flex-start;
  }

  .chat-msg-bot .chat-bubble {
    background: var(--color-paper-1, #1e293b);
    color: var(--color-ink-0, #f8fafc);
    border-radius: 18px 18px 18px 4px;
    padding: 13px 16px;
    font-size: 0.88rem;
    line-height: 1.52;
    border: 1px solid var(--rule-soft, rgba(148, 163, 184, 0.15));
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.12);
    word-break: break-word;
  }

  body.light-mode .chat-msg-bot .chat-bubble {
    background: #f8fafc;
    color: #0f172a;
    border-color: #e2e8f0;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
  }

  .chat-msg-bot .chat-bubble code {
    font-family: var(--font-mono, 'JetBrains Mono', monospace);
    font-size: 0.8rem;
    padding: 2px 6px;
    border-radius: 6px;
    background: rgba(99, 102, 241, 0.12);
    color: #818cf8;
    border: 1px solid rgba(99, 102, 241, 0.2);
  }

  body.light-mode .chat-msg-bot .chat-bubble code {
    background: #eef2ff;
    color: #4f46e5;
    border-color: #c7d2fe;
  }

  /* Answer Callout Card */
  .ai-answer-callout {
    margin-top: 10px;
    padding: 10px 14px;
    border-radius: 10px;
    background: rgba(99, 102, 241, 0.08);
    border-left: 3px solid #6366f1;
    font-size: 0.84rem;
    line-height: 1.5;
    white-space: pre-wrap;
    color: var(--color-ink-1, #e2e8f0);
  }

  body.light-mode .ai-answer-callout {
    background: #f1f5f9;
    border-left-color: #4f46e5;
    color: #1e293b;
  }

  /* Quick Recommendations Chips */
  .ai-quick-recommendations {
    width: 100%;
    margin-top: 4px;
    margin-bottom: 8px;
  }

  .ai-chips-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
  }

  .ai-chip-btn {
    border: 1px solid var(--rule-soft, rgba(148, 163, 184, 0.2)) !important;
    background: var(--color-paper-1, #1e293b) !important;
    color: var(--color-ink-0, #f8fafc) !important;
    font-weight: 600 !important;
    border-radius: 12px !important;
    padding: 8px 12px !important;
    font-size: 0.78rem !important;
    line-height: 1.2 !important;
    transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1) !important;
    cursor: pointer;
    outline: none !important;
    user-select: none;
    display: inline-flex;
    align-items: center;
    min-height: 42px;
    text-align: left;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
  }

  body.light-mode .ai-chip-btn {
    background: #f8fafc !important;
    border-color: #e2e8f0 !important;
    color: #1e293b !important;
  }

  .ai-chip-btn:hover {
    background: rgba(99, 102, 241, 0.14) !important;
    border-color: rgba(99, 102, 241, 0.45) !important;
    color: #818cf8 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.18);
  }

  body.light-mode .ai-chip-btn:hover {
    background: #eef2ff !important;
    border-color: #818cf8 !important;
    color: #4f46e5 !important;
  }

  .ai-chip-btn:focus-visible {
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.35) !important;
  }

  .ai-chip-btn:active {
    transform: translateY(1px);
    background: rgba(99, 102, 241, 0.22) !important;
  }

  /* Embedded Item Cards */
  .chat-item-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 14px;
    margin-top: 8px;
    border-radius: 12px;
    background: var(--color-paper-0, #0f172a);
    border: 1px solid var(--rule-soft, rgba(148, 163, 184, 0.18));
    text-decoration: none !important;
    transition: all 0.18s ease;
  }

  body.light-mode .chat-item-card {
    background: #ffffff;
    border-color: #e2e8f0;
  }

  .chat-item-card:hover {
    transform: translateX(4px);
    border-color: #6366f1;
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.18);
  }

  .chat-item-title {
    font-size: 0.84rem;
    font-weight: 700;
    color: var(--color-ink-0, #f8fafc);
    margin: 0 0 2px 0;
  }

  body.light-mode .chat-item-title {
    color: #0f172a;
  }

  .chat-item-sub {
    font-size: 0.74rem;
    color: var(--color-ink-2, #94a3b8);
    margin: 0;
  }

  body.light-mode .chat-item-sub {
    color: #64748b;
  }

  /* Typing Indicator */
  .chat-typing-bubble {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 10px 16px;
  }

  .chat-typing-bubble span {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #818cf8;
    animation: typingBlink 1.4s infinite ease-in-out both;
  }
  .chat-typing-bubble span:nth-child(1) { animation-delay: 0s; }
  .chat-typing-bubble span:nth-child(2) { animation-delay: 0.2s; }
  .chat-typing-bubble span:nth-child(3) { animation-delay: 0.4s; }

  @keyframes typingBlink {
    0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
    40% { transform: scale(1); opacity: 1; }
  }

  /* Footer */
  .ai-chat-footer {
    padding: 12px 16px;
    background: var(--color-paper-1, #1e293b);
    border-top: 1px solid var(--rule-soft, rgba(148, 163, 184, 0.15));
    flex-shrink: 0;
  }

  body.light-mode .ai-chat-footer {
    background: #f8fafc;
    border-color: #e2e8f0;
  }

  .ai-chat-input-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--color-paper-0, #0f172a);
    border: 1px solid var(--rule-soft, rgba(148, 163, 184, 0.22));
    border-radius: 14px;
    padding: 4px 6px 4px 14px;
    min-height: 46px;
    transition: all 0.2s ease;
  }

  body.light-mode .ai-chat-input-wrap {
    background: #ffffff;
    border-color: #cbd5e1;
  }

  .ai-chat-input-wrap:focus-within {
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25) !important;
  }

  #ai-chat-input {
    flex: 1;
    border: none;
    background: transparent;
    outline: none !important;
    font-size: 0.88rem;
    color: var(--color-ink-0, #f8fafc);
    padding: 8px 0;
  }

  body.light-mode #ai-chat-input {
    color: #0f172a;
  }

  #ai-chat-input::placeholder {
    color: var(--color-ink-3, #94a3b8) !important;
    opacity: 0.85;
  }

  #btn-send-chat {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    color: #ffffff;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 2px 8px rgba(79, 70, 229, 0.35);
    flex-shrink: 0;
  }

  #btn-send-chat:hover {
    transform: scale(1.06);
    box-shadow: 0 4px 14px rgba(79, 70, 229, 0.45);
  }

  #btn-send-chat:active {
    transform: scale(0.94);
  }

  .ai-input-hint {
    margin-top: 6px;
    font-size: 0.72rem;
    color: var(--color-ink-3, #64748b);
  }

  .ai-mono-hint {
    font-family: var(--font-mono, 'JetBrains Mono', monospace);
    font-size: 0.68rem;
    opacity: 0.75;
  }

  /* Minimalist Rounded SweetAlert2 Popups */
  .swal2-popup.swal2-minimal-rounded {
    border-radius: 20px !important;
    padding: 1.25rem 1.5rem !important;
    width: 360px !important;
    max-width: 92vw !important;
    background: var(--color-paper-0, #0f172a) !important;
    color: var(--color-ink-0, #f8fafc) !important;
    border: 1px solid var(--rule-soft, rgba(148, 163, 184, 0.2)) !important;
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.4) !important;
    font-family: var(--font-body, 'Plus Jakarta Sans', sans-serif) !important;
  }

  body.light-mode .swal2-popup.swal2-minimal-rounded {
    background: #ffffff !important;
    color: #0f172a !important;
    border-color: #e2e8f0 !important;
    box-shadow: 0 15px 35px rgba(15, 23, 42, 0.15) !important;
  }

  .swal2-minimal-rounded .swal2-title {
    font-size: 1.05rem !important;
    font-weight: 700 !important;
    padding: 0 !important;
    margin-bottom: 0.4rem !important;
    color: var(--color-ink-0, #f8fafc) !important;
  }

  body.light-mode .swal2-minimal-rounded .swal2-title {
    color: #0f172a !important;
  }

  .swal2-minimal-rounded .swal2-html-container {
    font-size: 0.84rem !important;
    margin: 0 0 0.8rem 0 !important;
    color: var(--color-ink-2, #94a3b8) !important;
  }

  body.light-mode .swal2-minimal-rounded .swal2-html-container {
    color: #64748b !important;
  }

  .swal2-minimal-rounded .swal2-actions {
    margin-top: 0.6rem !important;
    gap: 8px !important;
  }

  .swal2-minimal-rounded .swal2-styled {
    border-radius: 10px !important;
    padding: 8px 18px !important;
    font-size: 0.84rem !important;
    font-weight: 600 !important;
    margin: 0 !important;
    min-height: 40px;
    box-shadow: none !important;
  }

  .swal2-minimal-rounded .swal2-icon {
    transform: scale(0.75) !important;
    margin: 0.25rem auto 0.5rem auto !important;
  }

  /* ==========================================================================
     MOBILE RESPONSIVENESS (320px - 768px)
     Transforms Drawer into a Full Mobile Sheet with Thumb Ergonomics
     ========================================================================== */
  @media (max-width: 767.98px) {
    .ai-chatbot-drawer {
      bottom: 0 !important;
      right: 0 !important;
      left: 0 !important;
      top: 0 !important;
      width: 100vw !important;
      max-width: 100vw !important;
      height: 100% !important;
      max-height: 100dvh !important;
      border-radius: 0 !important;
      border: none !important;
      transform: translateY(100%) !important;
      z-index: 1060 !important;
    }

    .ai-chatbot-drawer.active {
      transform: translateY(0) !important;
    }

    .ai-chat-header {
      padding: 12px 16px;
    }

    .ai-header-btn {
      width: 42px;
      height: 42px;
      font-size: 1rem;
    }

    .ai-chips-grid {
      grid-template-columns: 1fr;
      gap: 6px;
    }

    .ai-chip-btn {
      min-height: 44px;
      padding: 10px 14px !important;
      font-size: 0.82rem !important;
    }

    .ai-chat-footer {
      padding: 10px 14px calc(10px + env(safe-area-inset-bottom, 0px));
    }

    .ai-chat-input-wrap {
      min-height: 48px;
    }

    #btn-send-chat {
      width: 40px;
      height: 40px;
    }

    .swal2-minimal-rounded .swal2-actions {
      flex-direction: column-reverse;
      width: 100%;
    }
    .swal2-minimal-rounded .swal2-styled {
      width: 100%;
      min-height: 44px;
    }
  }
</style>

<script>
  $(document).ready(function() {
    const CHAT_STORAGE_KEY = 'itam_ai_chat_history_v1';
    let chatHistory = [];

    function loadChatHistory() {
      try {
        const saved = localStorage.getItem(CHAT_STORAGE_KEY);
        if (saved) {
          chatHistory = JSON.parse(saved);
        }
      } catch (e) {
        chatHistory = [];
      }
      renderChatStream();
    }

    function saveChatHistory() {
      try {
        localStorage.setItem(CHAT_STORAGE_KEY, JSON.stringify(chatHistory));
      } catch (e) {
        console.error('Failed to save chat history:', e);
      }
    }

    function scrollChatToBottom() {
      const container = document.getElementById('ai-chat-stream-body');
      if (container) {
        container.scrollTop = container.scrollHeight;
      }
    }

    function renderChatStream() {
      const container = $('#ai-chat-stream-body');
      container.empty();

      if (chatHistory.length === 0) {
        // Initial Welcome Message with Hallmark Bento Chips
        container.append(`
          <div class="chat-msg chat-msg-bot">
            <div class="chat-bubble">
              <div class="d-flex align-items-center mb-2" style="gap: 6px;">
                <span class="ai-model-tag">
                  <i class="fas fa-sparkles mr-1"></i> ITAM AI ASSISTANT
                </span>
              </div>
              <div style="font-size: 0.88rem; line-height: 1.5;">
                {!! __('messages.ai_welcome_text') !!}
              </div>
            </div>
          </div>
          <div class="ai-quick-recommendations my-2">
            <div class="d-flex align-items-center justify-content-between mb-2 px-1">
              <small class="font-weight-bold" style="font-size: 0.74rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-ink-3, #94a3b8);">
                <i class="far fa-compass mr-1 text-info"></i> {{ __('messages.quick_questions') }}
              </small>
            </div>
            <div class="ai-chips-grid">
              <button type="button" class="ai-chip-btn" data-prompt="{{ __('messages.prompt_expired_laptops') }}">
                <i class="fas fa-laptop mr-2" style="color: #60a5fa;"></i>
                <span>{{ __('messages.chip_expired_laptops') }}</span>
              </button>
              <button type="button" class="ai-chip-btn" data-prompt="{{ __('messages.prompt_available_ips') }}">
                <i class="fas fa-network-wired mr-2" style="color: #34d399;"></i>
                <span>{{ __('messages.chip_available_ips') }}</span>
              </button>
              <button type="button" class="ai-chip-btn" data-prompt="{{ __('messages.prompt_open_tickets') }}">
                <i class="fas fa-ticket-alt mr-2" style="color: #f59e0b;"></i>
                <span>{{ __('messages.chip_open_tickets') }}</span>
              </button>
              <button type="button" class="ai-chip-btn" data-prompt="{{ __('messages.prompt_printer_devices') }}">
                <i class="fas fa-print mr-2" style="color: #a78bfa;"></i>
                <span>{{ __('messages.chip_printer_devices') }}</span>
              </button>
            </div>
          </div>
        `);
      } else {
        chatHistory.forEach(msg => {
          if (msg.role === 'user') {
            container.append(`
              <div class="chat-msg chat-msg-user">
                <div class="chat-bubble">${escapeHtml(msg.content)}</div>
              </div>
            `);
          } else if (msg.role === 'bot') {
            let botContent = '';
            if (msg.summary) {
              botContent += `<div>${escapeHtml(msg.summary)}</div>`;
            }
            if (msg.answer) {
              botContent += `<div class="ai-answer-callout">${escapeHtml(msg.answer)}</div>`;
            }
            if (msg.items && msg.items.length > 0) {
              botContent += `<div class="mt-2">`;
              msg.items.forEach(item => {
                botContent += `
                  <a href="${item.url}" class="chat-item-card">
                    <div>
                      <div class="chat-item-title">${escapeHtml(item.title)}</div>
                      <div class="chat-item-sub">${escapeHtml(item.subtitle)}</div>
                    </div>
                    <span class="badge ${item.badge} px-2 py-1" style="font-family: var(--font-mono, monospace); font-size: 0.72rem;">${escapeHtml(item.badge_text)}</span>
                  </a>
                `;
              });
              botContent += `</div>`;
            }

            if (msg.sources && msg.sources.length > 0) {
              botContent += renderAiSources(msg.sources);
            }

            container.append(`
              <div class="chat-msg chat-msg-bot">
                <div class="chat-bubble">${botContent}</div>
              </div>
            `);
          }
        });
      }

      scrollChatToBottom();
    }

    function escapeHtml(text) {
      if (!text) return '';
      return String(text)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
    }

    function renderAiSources(sources) {
      if (!Array.isArray(sources) || sources.length === 0) return '';

      let html = '<div class="mt-2 pt-2" style="border-top: 1px solid var(--rule-soft, rgba(148, 163, 184, 0.16));">';
      html += '<small class="text-muted d-block mb-1 font-weight-bold" style="font-size: 0.72rem;"><i class="fas fa-globe mr-1 text-info"></i> {{ __("messages.web_sources") }}</small>';
      sources.slice(0, 4).forEach(function(source) {
        if (!source || !source.url) return;
        html += '<a href="' + escapeHtml(source.url) + '" target="_blank" rel="noopener noreferrer" class="d-block text-info text-truncate" style="font-size: 0.75rem; margin-top: 4px;">';
        html += '<i class="fas fa-external-link-alt mr-1"></i>' + escapeHtml(source.title || source.url);
        html += '</a>';
      });
      html += '</div>';
      return html;
    }

    function submitChatQuery(promptText) {
      const text = (promptText || $('#ai-chat-input').val() || '').trim();
      if (!text) return;

      $('#ai-chat-input').val('');
      chatHistory.push({ role: 'user', content: text });
      saveChatHistory();
      renderChatStream();

      // Typing Indicator
      const container = $('#ai-chat-stream-body');
      container.append(`
        <div class="chat-msg chat-msg-bot" id="chat-typing-indicator">
          <div class="chat-bubble chat-typing-bubble">
            <span></span><span></span><span></span>
          </div>
        </div>
      `);
      scrollChatToBottom();

      $.ajax({
        url: "{{ route('ai.query-search') }}",
        method: "POST",
        data: {
          _token: "{{ csrf_token() }}",
          prompt: text,
          history: chatHistory.slice(0, -1).slice(-10).map(function(message) {
            return {
              role: message.role,
              content: message.content || [message.summary, message.answer].filter(Boolean).join('\n')
            };
          })
        },
        success: function(response) {
          $('#chat-typing-indicator').remove();
          if (response.success && response.result) {
            const res = response.result;
            chatHistory.push({
              role: 'bot',
              summary: res.summary || '',
              answer: res.answer || null,
              items: res.items || [],
              sources: res.sources || []
            });
            saveChatHistory();
            renderChatStream();
          }
        },
        error: function(err) {
          $('#chat-typing-indicator').remove();
          chatHistory.push({
            role: 'bot',
            summary: "{{ __('messages.chat_error_summary') }}",
            answer: "{{ __('messages.chat_error_network') }}",
            items: []
          });
          saveChatHistory();
          renderChatStream();
        }
      });
    }

    // Toggle Chat Drawer Open/Close
    function toggleChatDrawer() {
      const drawer = $('#aiChatbotDrawer');
      drawer.toggleClass('active');
      if (drawer.hasClass('active')) {
        $('#ai-chat-input').focus();
        scrollChatToBottom();
      }
    }

    // Event Listeners
    $(document).on('click', '.btn-floating-ai, [data-target="#aiSearchModal"]', function(e) {
      e.preventDefault();
      toggleChatDrawer();
    });

    $(document).on('click', '#btn-close-chat-drawer, .ai-chat-mobile-handle', function() {
      $('#aiChatbotDrawer').removeClass('active');
    });

    $(document).on('click', '#btn-clear-chat-history', function() {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: "{{ __('messages.clear_chat_confirm_title') }}",
          text: "{{ __('messages.clear_chat_confirm_text') }}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#ef4444',
          cancelButtonColor: '#64748b',
          confirmButtonText: "{{ __('messages.yes_delete') }}",
          cancelButtonText: "{{ __('messages.cancel') }}",
          reverseButtons: true,
          customClass: {
            popup: 'swal2-minimal-rounded'
          }
        }).then((result) => {
          if (result.isConfirmed) {
            chatHistory = [];
            localStorage.removeItem(CHAT_STORAGE_KEY);
            renderChatStream();
            Swal.fire({
              title: "{{ __('messages.deleted') }}",
              text: "{{ __('messages.history_cleared') }}",
              icon: 'success',
              timer: 1500,
              showConfirmButton: false,
              customClass: {
                popup: 'swal2-minimal-rounded'
              }
            });
          }
        });
      } else {
        if (confirm("{{ __('messages.clear_chat_confirm_title') }}")) {
          chatHistory = [];
          localStorage.removeItem(CHAT_STORAGE_KEY);
          renderChatStream();
        }
      }
    });

    $(document).on('click', '#btn-send-chat', function() {
      submitChatQuery();
    });

    $(document).on('keypress', '#ai-chat-input', function(e) {
      if (e.which === 13) {
        e.preventDefault();
        submitChatQuery();
      }
    });

    $(document).on('click', '.ai-chip-btn', function() {
      const prompt = $(this).data('prompt');
      submitChatQuery(prompt);
    });

    // Global Shortcut Ctrl + K / Cmd + K
    $(document).on('keydown', function(e) {
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
        e.preventDefault();
        toggleChatDrawer();
      }
      if (e.key === 'Escape' && $('#aiChatbotDrawer').hasClass('active')) {
        $('#aiChatbotDrawer').removeClass('active');
      }
    });

    // Load Chat History on Init
    loadChatHistory();
  });
</script>

<!-- PWA Mobile Install Banner -->
<div id="pwa-install-banner" class="shadow-lg p-3 rounded-lg" style="display: none; position: fixed; bottom: 20px; left: 20px; right: 20px; z-index: 99999; background: var(--color-paper-1); border: 1px solid var(--color-accent-soft); color: var(--color-ink-0); border-radius: 14px; box-shadow: var(--shadow-lg);">
  <div class="d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center" style="gap: 12px;">
      <img src="{{ vasset('logo.png') }}" alt="ITAM" style="width: 42px; height: 42px; border-radius: 10px; object-fit: cover;">
      <div>
        <div class="font-weight-bold" style="font-size: 0.92rem; color: var(--color-ink-0);">{{ __('messages.install_pwa_title') }}</div>
        <small class="text-muted" style="font-size: 0.75rem; color: var(--color-ink-2) !important;">{{ __('messages.install_pwa_desc') }}</small>
      </div>
    </div>
    <div class="d-flex align-items-center" style="gap: 8px;">
      <button id="pwa-install-btn" class="btn btn-sm font-weight-bold px-3 py-1" style="border-radius: 20px; background: var(--color-accent); color: var(--color-paper-0); border: none;"><i class="fas fa-download mr-1"></i> {{ __('messages.install') }}</button>
      <button id="pwa-close-btn" class="btn btn-sm px-2 py-1" style="border-radius: 50%; width: 28px; height: 28px; padding: 0; display: flex; align-items: center; justify-content: center; background: color-mix(in oklch, var(--color-ink-0) 5%, transparent); border: none; color: var(--color-ink-1);"><i class="fas fa-times text-xs"></i></button>
    </div>
  </div>
</div>

<script>
  // Register Service Worker for PWA
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
      navigator.serviceWorker.register('/sw.js').then(function(registration) {
        registration.update();
        console.log('PWA ServiceWorker registered with scope:', registration.scope);
      }, function(err) {
        console.log('PWA ServiceWorker registration failed:', err);
      });
    });
  }

  // Handle PWA Install Prompt Banner
  let deferredPrompt;
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    const banner = document.getElementById('pwa-install-banner');
    if (banner && !localStorage.getItem('pwa_dismissed')) {
      banner.style.display = 'block';
    }
  });

  document.getElementById('pwa-install-btn')?.addEventListener('click', async () => {
    const banner = document.getElementById('pwa-install-banner');
    if (banner) banner.style.display = 'none';
    if (deferredPrompt) {
      deferredPrompt.prompt();
      const { outcome } = await deferredPrompt.userChoice;
      console.log('PWA install prompt result:', outcome);
      deferredPrompt = null;
    }
  });

  document.getElementById('pwa-close-btn')?.addEventListener('click', () => {
    const banner = document.getElementById('pwa-install-banner');
    if (banner) banner.style.display = 'none';
    localStorage.setItem('pwa_dismissed', '1');
  });
</script>

<!-- Modal Change Password -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 440px;">
    <div class="modal-content">
      
      <!-- Modal Header -->
      <div class="modal-header align-items-center py-3 px-4">
        <div class="d-flex align-items-center" style="gap: 12px;">
          <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(129, 140, 248, 0.3); color: #818cf8; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; flex-shrink: 0;">
            <i class="fas fa-key"></i>
          </div>
          <div>
            <h5 class="modal-title font-weight-bold" id="changePasswordModalLabel" style="font-size: 1.05rem; margin: 0; line-height: 1.2;">
              {{ __('messages.change_password') }}
            </h5>
            <small class="d-block modal-subtitle" style="font-size: 0.74rem;">{{ __('messages.change_password_desc') }}</small>
          </div>
        </div>
        <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close" style="outline: none !important;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form method="POST" action="{{ route('password.update') }}">
        @csrf
        @method('put')
        <div class="modal-body py-4 px-4">
          <!-- Password Saat Ini -->
          <div class="form-group mb-3">
            <label for="current_password" style="font-size: 0.82rem; font-weight: 600; margin-bottom: 6px; display: block;">{{ __('messages.current_password') }}</label>
            <div class="position-relative">
              <input type="password" name="current_password" id="current_password" class="form-control pw-input @error('current_password', 'updatePassword') is-invalid @enderror" placeholder="{{ __('messages.current_password_placeholder') }}" required style="border-radius: 14px; padding: 11px 40px 11px 16px; font-size: 0.88rem; box-shadow: none; transition: all 0.2s;">
              <button type="button" class="btn btn-link position-absolute btn-toggle-pw" data-target="#current_password" style="right: 6px; top: 50%; transform: translateY(-50%); padding: 4px 8px; font-size: 0.85rem; text-decoration: none; border: none; background: transparent;">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            @error('current_password', 'updatePassword')
              <small class="text-danger mt-1 d-block" style="font-size: 0.78rem;"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</small>
            @enderror
          </div>

          <!-- Password Baru -->
          <div class="form-group mb-3">
            <label for="password" style="font-size: 0.82rem; font-weight: 600; margin-bottom: 6px; display: block;">{{ __('messages.new_password') }}</label>
            <div class="position-relative">
              <input type="password" name="password" id="password" class="form-control pw-input @error('password', 'updatePassword') is-invalid @enderror" placeholder="{{ __('messages.new_password_placeholder') }}" required style="border-radius: 14px; padding: 11px 40px 11px 16px; font-size: 0.88rem; box-shadow: none; transition: all 0.2s;">
              <button type="button" class="btn btn-link position-absolute btn-toggle-pw" data-target="#password" style="right: 6px; top: 50%; transform: translateY(-50%); padding: 4px 8px; font-size: 0.85rem; text-decoration: none; border: none; background: transparent;">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            @error('password', 'updatePassword')
              <small class="text-danger mt-1 d-block" style="font-size: 0.78rem;"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</small>
            @enderror
          </div>

          <!-- Konfirmasi Password Baru -->
          <div class="form-group mb-0">
            <label for="password_confirmation" style="font-size: 0.82rem; font-weight: 600; margin-bottom: 6px; display: block;">{{ __('messages.confirm_new_password') }}</label>
            <div class="position-relative">
              <input type="password" name="password_confirmation" id="password_confirmation" class="form-control pw-input" placeholder="{{ __('messages.confirm_new_password_placeholder') }}" required style="border-radius: 14px; padding: 11px 40px 11px 16px; font-size: 0.88rem; box-shadow: none; transition: all 0.2s;">
              <button type="button" class="btn btn-link position-absolute btn-toggle-pw" data-target="#password_confirmation" style="right: 6px; top: 50%; transform: translateY(-50%); padding: 4px 8px; font-size: 0.85rem; text-decoration: none; border: none; background: transparent;">
                <i class="fas fa-eye"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Footer Buttons -->
        <div class="modal-footer py-3 px-4" style="display: flex; justify-content: flex-end; gap: 6px;">
          <button type="button" class="btn px-3.5 pw-cancel-btn" data-dismiss="modal" style="height: 38px; border-radius: 14px !important; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; padding-left: 16px !important; padding-right: 16px !important;">
            {{ __('messages.cancel') }}
          </button>
          <button type="submit" class="btn px-3.5" style="height: 38px; border-radius: 14px !important; font-weight: 600; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border: none; color: #ffffff; box-shadow: 0 4px 14px rgba(59, 130, 246, 0.35); display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; padding-left: 16px !important; padding-right: 16px !important;">
            {{ __('messages.save_password') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $(document).on('click', '.btn-toggle-pw', function(e) {
    e.preventDefault();
    const targetSelector = $(this).data('target');
    const input = $(targetSelector);
    const icon = $(this).find('i');
    if (input.attr('type') === 'password') {
      input.attr('type', 'text');
      icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
      input.attr('type', 'password');
      icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
  });

  @if($errors->updatePassword->any())
    $(document).ready(function() {
      $('#changePasswordModal').modal('show');
    });
  @endif

  @if(session('status') === 'password-updated')
    document.addEventListener('DOMContentLoaded', function() {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: 'Password Anda telah berhasil diperbarui.',
        });
      }
    });
  @endif

  function updateJakartaClock() {
    const clockEl = document.getElementById('header-jakarta-clock');
    if (!clockEl) return;
    const now = new Date();
    const isEn = '{{ App::getLocale() }}' === 'en';
    const localeStr = isEn ? 'en-US' : 'id-ID';
    try {
      const formatter = new Intl.DateTimeFormat(localeStr, {
        timeZone: 'Asia/Jakarta',
        weekday: 'short',
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
      });
      clockEl.textContent = formatter.format(now).replace(/\./g, ':') + ' WIB';
    } catch (e) {
      clockEl.textContent = now.toLocaleDateString(localeStr) + ' ' + now.toLocaleTimeString(localeStr) + ' WIB';
    }
  }
  setInterval(updateJakartaClock, 1000);
  document.addEventListener('DOMContentLoaded', updateJakartaClock);

  // Global helper for file/camera upload image preview
  window.previewSelectedImage = function(input, previewId, iconId) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        var img = document.getElementById(previewId);
        var icon = document.getElementById(iconId);
        if (img) {
          img.src = e.target.result;
          img.style.display = 'block';
        }
        if (icon) {
          icon.style.display = 'none';
        }
      };
      reader.readAsDataURL(input.files[0]);
    }
  };

  // Global Ctrl+K / Cmd+K shortcut to open ITAM AI Search Modal
  $(document).on('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
      e.preventDefault();
      $('#aiSearchModal').modal('toggle');
    }
  });

  // Global Hallmark Ultra-Sleek Toast Notification System
  window.showHallmarkToast = function(type, title, message) {
    let iconClass = 'fa-wifi';
    let badgeBg = 'rgba(34, 197, 94, 0.15)';
    let badgeBorder = 'rgba(34, 197, 94, 0.4)';
    let iconColor = '#4ade80';

    if (type === 'error' || type === 'danger') {
      iconClass = 'fa-exclamation-circle';
      badgeBg = 'rgba(239, 68, 68, 0.15)';
      badgeBorder = 'rgba(239, 68, 68, 0.4)';
      iconColor = '#f87171';
    } else if (type === 'warning') {
      iconClass = 'fa-exclamation-triangle';
      badgeBg = 'rgba(245, 158, 11, 0.15)';
      badgeBorder = 'rgba(245, 158, 11, 0.4)';
      iconColor = '#fbbf24';
    } else if (type === 'info') {
      iconClass = 'fa-info-circle';
      badgeBg = 'rgba(56, 189, 248, 0.15)';
      badgeBorder = 'rgba(56, 189, 248, 0.4)';
      iconColor = '#38bdf8';
    }

    const toastId = 'hallmark-toast-' + Date.now();
    const html = `
      <div id="${toastId}" class="hallmark-toast-card hallmark-toast-${type}">
        <div class="hallmark-toast-icon-wrapper" style="background: ${badgeBg}; border: 1px solid ${badgeBorder}; color: ${iconColor};">
          <i class="fas ${iconClass}"></i>
        </div>
        <div class="hallmark-toast-content">
          <div class="hallmark-toast-title">${title}</div>
          ${message ? `<div class="hallmark-toast-sub">${message}</div>` : ''}
        </div>
        <button type="button" class="hallmark-toast-close" onclick="document.getElementById('${toastId}').remove()">&times;</button>
      </div>
    `;

    let container = document.getElementById('hallmark-toast-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'hallmark-toast-container';
      document.body.appendChild(container);
    }
    
    const el = document.createElement('div');
    el.innerHTML = html;
    const child = el.firstElementChild;
    container.appendChild(child);

    setTimeout(() => {
      if (child && child.parentNode) {
        child.style.opacity = '0';
        child.style.transform = 'translateY(-10px) scale(0.95)';
        setTimeout(() => child.remove(), 300);
      }
    }, 3800);
  };

  // Auto wrap session flash alerts into #session-toast-container & auto dismiss after 4.5s
  $(document).ready(function() {
    var sessionAlerts = $('.content > .alert, .content > .alert-dismissible, .content-wrapper > .alert, div:not(.modal-body):not(.card-body) > .alert-dismissible');
    if (sessionAlerts.length > 0) {
      var container = $('#session-toast-container');
      if (container.length === 0) {
        container = $('<div id="session-toast-container"></div>').appendTo('body');
      }
      sessionAlerts.each(function() {
        $(this).removeClass('w-100').addClass('session-toast-alert').appendTo(container);
      });
    }

    setTimeout(function() {
      $('.session-toast-alert, #session-toast-container .alert').fadeOut(350, function() {
        $(this).remove();
      });
    }, 4500);
  });
</script>

<!-- PWA Service Worker Registration -->
<script>
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
      navigator.serviceWorker.register('/sw.js').then(function(registration) {
        console.log('[PWA] ServiceWorker registered successfully:', registration.scope);
      }).catch(function(err) {
        console.warn('[PWA] ServiceWorker registration failed:', err);
      });
    });
  }
</script>

<!-- Floating Quick Actions (ITAM AI Chatbot Floating Circle Button) -->
<div class="floating-quick-actions">
  @can('action_ai_assistant')
  <button type="button" 
          class="btn-floating-action btn-floating-ai" 
          data-toggle="modal" 
          data-target="#aiSearchModal" 
          title="{{ __('messages.ai_modal_title') }} (Ctrl+K)"
          aria-label="{{ __('messages.ai_modal_title') }} (Ctrl+K)">
    <div class="ai-circle-content">
      <i class="fas fa-robot chatbot-icon"></i>
    </div>
    <span class="ai-live-dot"></span>
  </button>
  @endcan
</div>

@if(auth()->check() && (auth()->user()->hasRole('Super Admin') || auth()->user()->can('menu_settings')))
<!-- Mini Diagnostic Floating HUD Capsule & Drawer for Super Admin -->
<div id="itam-perf-hud" style="display: none;">
  <!-- Floating Pill Capsule (Bottom-Left) -->
  <div id="itam-hud-pill" class="itam-hud-pill" onclick="toggleItamPerfDrawer()" title="{{ __('messages.perf_hud_title') }} &bull; {{ App::getLocale() === 'id' ? 'Klik untuk rincian' : 'Click for breakdown' }}">
    <span id="itam-hud-dot" class="itam-hud-dot"></span>
    <span class="itam-hud-stat"><i class="fas fa-bolt text-warning" style="font-size:0.72rem;"></i> <span id="itam-hud-time">--ms</span></span>
    <span class="itam-hud-sep"></span>
    <span class="itam-hud-stat"><i class="fas fa-database text-info" style="font-size:0.72rem;"></i> <span id="itam-hud-queries">--Q</span> (<span id="itam-hud-qtime">--ms</span>)</span>
    <span class="itam-hud-sep d-none d-sm-inline"></span>
    <span class="itam-hud-stat d-none d-sm-inline"><i class="fas fa-memory text-success" style="font-size:0.72rem;"></i> <span id="itam-hud-ram">--MB</span></span>
  </div>

  <!-- Slide-out Diagnostic Drawer / Modal Overlay -->
  <div id="itam-hud-drawer-backdrop" class="itam-hud-backdrop" onclick="toggleItamPerfDrawer(false)"></div>
  <div id="itam-hud-drawer" class="itam-hud-drawer">
    <div class="itam-hud-drawer-header">
      <div class="itam-hud-header-info">
        <span class="badge itam-hud-telemetry-badge">
          <i class="fas fa-chart-line mr-1"></i> {{ __('messages.perf_telemetry_badge') }}
        </span>
        <span id="itam-hud-route" class="itam-hud-route-title" title=""></span>
      </div>
      <button type="button" class="itam-hud-close-btn" onclick="toggleItamPerfDrawer(false)" aria-label="Close">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <div class="itam-hud-drawer-body">
      <!-- 3 Diagnostic Metric Cards -->
      <div class="row g-2 mb-3">
        <div class="col-4">
          <div class="itam-hud-card">
            <span class="itam-hud-label">{{ __('messages.perf_avg_latency') }}</span>
            <span id="itam-hud-card-duration" class="itam-hud-val text-warning">--</span>
          </div>
        </div>
        <div class="col-4">
          <div class="itam-hud-card">
            <span class="itam-hud-label">{{ __('messages.perf_db_queries') }}</span>
            <span id="itam-hud-card-queries" class="itam-hud-val text-info">--</span>
          </div>
        </div>
        <div class="col-4">
          <div class="itam-hud-card">
            <span class="itam-hud-label">{{ __('messages.perf_server_memory') }}</span>
            <span id="itam-hud-card-ram" class="itam-hud-val text-success">--</span>
          </div>
        </div>
      </div>

      <!-- Query Breakdown List -->
      <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="itam-hud-section-label">
          {{ __('messages.perf_hud_queries_breakdown') }}
        </span>
        <span id="itam-hud-query-count-badge" class="itam-hud-badge-pill">0 queries</span>
      </div>

      <div id="itam-hud-queries-list" class="itam-hud-queries-container">
        <!-- Rendered queries go here -->
      </div>
    </div>

    <div class="itam-hud-drawer-footer">
      <a href="{{ route('system-performance.index') }}" class="itam-hud-action-btn">
        <span class="d-inline-flex align-items-center" style="gap: 8px;">
          <i class="fas fa-tachometer-alt"></i> {{ __('messages.perf_hud_open_console') }}
        </span>
        <i class="fas fa-arrow-right" style="font-size: 0.75rem; opacity: 0.85;"></i>
      </a>
    </div>
  </div>
</div>

<style>
/* Hallmark Language & User Dropdown Harmonization */
.header-lang-menu {
  background: #0f172a;
  border: 1px solid rgba(148, 163, 184, 0.18);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
}
body.light-mode .header-lang-menu {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  box-shadow: 0 10px 30px rgba(15, 23, 42, 0.1) !important;
}
body.light-mode .header-lang-menu .dropdown-item {
  color: #334155 !important;
}
body.light-mode .header-lang-menu .dropdown-item:hover {
  background: #f1f5f9 !important;
  color: #4f46e5 !important;
}
body.light-mode .header-lang-menu .dropdown-divider {
  border-top: 1px solid #e2e8f0 !important;
}

.user-dropdown-header-card {
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.14), rgba(30, 41, 59, 0.6));
  border-bottom: 1px solid rgba(148, 163, 184, 0.12);
}
.user-dropdown-name {
  font-weight: 700;
  font-size: 0.95rem;
  color: #f8fafc;
}
.user-dropdown-email {
  font-size: 0.75rem;
  color: #94a3b8;
  font-family: var(--font-mono);
  margin-top: 2px;
}
body.light-mode .user-dropdown-header-card {
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.08), rgba(241, 245, 249, 0.95)) !important;
  border-bottom: 1px solid #e2e8f0 !important;
}
body.light-mode .user-dropdown-name {
  color: #0f172a !important;
}
body.light-mode .user-dropdown-email {
  color: #64748b !important;
}

/* Light Mode Telemetry HUD Drawer */
body.light-mode .itam-hud-drawer {
  background: #ffffff !important;
  border-color: #e2e8f0 !important;
  box-shadow: 0 20px 50px rgba(15, 23, 42, 0.15) !important;
}
body.light-mode .itam-hud-drawer-header {
  background: #f8fafc !important;
  border-bottom-color: #e2e8f0 !important;
}
body.light-mode .itam-hud-route-title {
  color: #0f172a !important;
}
body.light-mode .itam-hud-card {
  background: #f8fafc !important;
  border-color: #e2e8f0 !important;
}
body.light-mode .itam-hud-card:hover {
  background: #f1f5f9 !important;
}
body.light-mode .itam-hud-label {
  color: #64748b !important;
}
body.light-mode .itam-hud-section-label {
  color: #64748b !important;
}
body.light-mode .itam-hud-badge-pill {
  color: #334155 !important;
  background: #f1f5f9 !important;
  border-color: #cbd5e1 !important;
}
body.light-mode .itam-hud-drawer-footer {
  background: #f8fafc !important;
  border-top-color: #e2e8f0 !important;
}
body.light-mode .itam-hud-close-btn {
  background: #f1f5f9 !important;
  border-color: #e2e8f0 !important;
  color: #64748b !important;
}
body.light-mode .itam-hud-close-btn:hover {
  background: #e2e8f0 !important;
  color: #0f172a !important;
}
body.light-mode .itam-hud-query-item {
  background: #f8fafc !important;
  border-color: #e2e8f0 !important;
}
body.light-mode .itam-hud-query-sql {
  color: #334155 !important;
}

/* Hallmark HUD Styles */
.itam-hud-pill {
  position: fixed;
  bottom: 20px;
  left: 20px;
  z-index: 1040;
  background: rgba(15, 23, 42, 0.88);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 9999px;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
  padding: 6px 14px;
  font-family: 'JetBrains Mono', monospace;
  font-size: 0.74rem;
  color: #cbd5e1;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  user-select: none;
}
.itam-hud-pill:hover {
  background: rgba(15, 23, 42, 0.98);
  border-color: rgba(99, 102, 241, 0.4);
  transform: translateY(-2px);
  box-shadow: 0 14px 28px -4px rgba(0, 0, 0, 0.6), 0 0 12px rgba(99, 102, 241, 0.25);
}
.itam-hud-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #10b981;
  box-shadow: 0 0 8px #10b981;
  display: inline-block;
  animation: itamHudPulse 2s infinite;
}
@keyframes itamHudPulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.5; transform: scale(0.85); }
}
.itam-hud-sep {
  width: 1px;
  height: 12px;
  background: rgba(255, 255, 255, 0.15);
}
.itam-hud-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
  backdrop-filter: blur(3px);
  -webkit-backdrop-filter: blur(3px);
  z-index: 1048;
  display: none;
  opacity: 0;
  transition: opacity 0.25s ease;
}
.itam-hud-backdrop.open {
  display: block;
  opacity: 1;
}
.itam-hud-drawer {
  position: fixed;
  bottom: 65px;
  left: 20px;
  width: min(480px, calc(100vw - 40px));
  max-height: calc(100vh - 120px);
  background: #0f172a;
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 16px;
  box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.7), 0 0 0 1px rgba(255, 255, 255, 0.05);
  z-index: 1049;
  display: none;
  flex-direction: column;
  overflow: hidden;
  transform: translateY(12px) scale(0.97);
  opacity: 0;
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.itam-hud-drawer.open {
  display: flex;
  transform: translateY(0) scale(1);
  opacity: 1;
}
.itam-hud-drawer-header {
  padding: 12px 16px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: rgba(255, 255, 255, 0.02);
}
.itam-hud-header-info {
  display: flex;
  align-items: center;
  gap: 8px;
  min-width: 0;
  flex: 1 1 auto;
  margin-right: 8px;
}
.itam-hud-telemetry-badge {
  background: rgba(99, 102, 241, 0.2);
  border: 1px solid rgba(99, 102, 241, 0.4);
  color: #a5b4fc;
  font-family: 'JetBrains Mono', monospace;
  font-size: 0.68rem;
  padding: 3px 6px;
  border-radius: 6px;
  flex-shrink: 0;
  white-space: nowrap;
}
.itam-hud-route-title {
  color: #f1f5f9;
  font-size: 0.80rem;
  font-weight: 700;
  font-family: 'JetBrains Mono', monospace;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  min-width: 0;
}
.itam-hud-close-btn {
  width: 28px;
  height: 28px;
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  background: rgba(255, 255, 255, 0.05);
  color: #94a3b8;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.82rem;
  cursor: pointer;
  transition: all 0.15s ease;
  padding: 0;
  flex-shrink: 0;
}
.itam-hud-close-btn:hover {
  background: rgba(255, 255, 255, 0.12);
  color: #f8fafc;
  border-color: rgba(255, 255, 255, 0.2);
}
.itam-hud-drawer-body {
  padding: 14px 16px;
  overflow-y: auto;
  flex: 1;
  max-height: 420px;
  scrollbar-width: thin;
  scrollbar-color: rgba(255, 255, 255, 0.15) transparent;
}
.itam-hud-drawer-body::-webkit-scrollbar {
  width: 5px;
}
.itam-hud-drawer-body::-webkit-scrollbar-track {
  background: transparent;
}
.itam-hud-drawer-body::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.15);
  border-radius: 9999px;
}
.itam-hud-drawer-footer {
  padding: 12px 16px;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  background: rgba(255, 255, 255, 0.02);
}
.itam-hud-card {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.07);
  border-radius: 12px;
  padding: 8px 6px;
  text-align: center;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
  transition: background 0.15s ease;
}
.itam-hud-card:hover {
  background: rgba(255, 255, 255, 0.05);
}
.itam-hud-label {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.62rem;
  font-weight: 600;
  color: #94a3b8;
  min-height: 26px;
  line-height: 1.15;
  text-align: center;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  white-space: normal;
  word-break: normal;
  margin-bottom: 2px;
}
.itam-hud-val {
  font-family: 'JetBrains Mono', monospace;
  font-size: 0.88rem;
  font-weight: 700;
  white-space: nowrap;
}
.itam-hud-section-label {
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #94a3b8;
}
.itam-hud-badge-pill {
  font-family: 'JetBrains Mono', monospace;
  font-size: 0.68rem;
  color: #e2e8f0;
  background: rgba(255, 255, 255, 0.07);
  border: 1px solid rgba(255, 255, 255, 0.1);
  padding: 2px 8px;
  border-radius: 9999px;
}
.itam-hud-queries-container {
  max-height: 240px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding-right: 4px;
  scrollbar-width: thin;
  scrollbar-color: rgba(255, 255, 255, 0.15) transparent;
}
.itam-hud-queries-container::-webkit-scrollbar {
  width: 5px;
}
.itam-hud-queries-container::-webkit-scrollbar-track {
  background: transparent;
}
.itam-hud-queries-container::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.15);
  border-radius: 9999px;
}
.itam-hud-queries-container::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.3);
}
.itam-hud-query-item {
  background: rgba(255, 255, 255, 0.025);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 10px;
  padding: 7px 10px;
  font-size: 0.72rem;
  font-family: 'JetBrains Mono', monospace;
  cursor: pointer;
  transition: all 0.2s ease;
  position: relative;
}
.itam-hud-query-item:hover {
  background: rgba(255, 255, 255, 0.045);
  border-color: rgba(99, 102, 241, 0.3);
}
.itam-hud-query-item.is-expanded {
  background: rgba(99, 102, 241, 0.05);
  border-color: rgba(99, 102, 241, 0.4);
}
.itam-hud-query-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 3px;
}
.itam-hud-query-index {
  color: #64748b;
  font-size: 0.68rem;
  font-weight: 600;
}
.itam-hud-query-time {
  font-weight: 700;
  font-size: 0.72rem;
}
.itam-hud-query-sql {
  color: #cbd5e1;
  word-break: normal;
  overflow-wrap: anywhere;
  white-space: pre-wrap;
  line-height: 1.45;
  font-size: 0.71rem;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  transition: max-height 0.2s ease;
}
.itam-hud-query-item.is-expanded .itam-hud-query-sql {
  display: block;
  -webkit-line-clamp: unset;
  overflow: visible;
  color: #f8fafc;
}
.itam-hud-query-expand-hint {
  font-size: 0.62rem;
  color: #64748b;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 4px;
  margin-top: 4px;
  transition: color 0.15s ease;
}
.itam-hud-query-item:hover .itam-hud-query-expand-hint {
  color: #94a3b8;
}
.itam-hud-chevron {
  transition: transform 0.2s ease;
  font-size: 0.58rem;
}
.itam-hud-query-item.is-expanded .itam-hud-chevron {
  transform: rotate(180deg);
}
.itam-hud-action-btn {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  padding: 10px 16px;
  background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 12px;
  color: #ffffff !important;
  font-weight: 600;
  font-size: 0.82rem;
  text-decoration: none !important;
  box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.itam-hud-action-btn:hover {
  background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
  box-shadow: 0 6px 20px rgba(79, 70, 229, 0.45);
  transform: translateY(-1px);
  color: #ffffff !important;
}
@media (max-width: 576px) {
  .itam-hud-pill {
    bottom: 15px;
    left: 12px;
    padding: 5px 10px;
    font-size: 0.7rem;
  }
  .itam-hud-drawer {
    left: 10px;
    bottom: 60px;
    width: calc(100vw - 20px);
    max-height: calc(100vh - 80px);
  }
  .itam-hud-val {
    font-size: 0.75rem;
  }
  .itam-hud-card {
    padding: 8px 4px;
  }
}
</style>

<script>
window.toggleItamPerfDrawer = function(forceState) {
  var drawer = document.getElementById('itam-hud-drawer');
  var backdrop = document.getElementById('itam-hud-drawer-backdrop');
  if (!drawer || !backdrop) return;
  var isOpen = drawer.classList.contains('open');
  var targetState = (typeof forceState === 'boolean') ? forceState : !isOpen;
  if (targetState) {
    drawer.classList.add('open');
    backdrop.classList.add('open');
  } else {
    drawer.classList.remove('open');
    backdrop.classList.remove('open');
  }
};

function __itamEscapeHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

window.__itamRenderHud = function() {
  var data = window.__itamTelemetry;
  if (!data) return;
  var container = document.getElementById('itam-perf-hud');
  if (!container) return;
  container.style.display = 'block';

  // Populate Pill
  var timeEl = document.getElementById('itam-hud-time');
  var queriesEl = document.getElementById('itam-hud-queries');
  var qtimeEl = document.getElementById('itam-hud-qtime');
  var ramEl = document.getElementById('itam-hud-ram');
  var dotEl = document.getElementById('itam-hud-dot');

  if (timeEl) timeEl.textContent = data.duration_ms + 'ms';
  if (queriesEl) queriesEl.textContent = data.query_count + 'Q';
  if (qtimeEl) qtimeEl.textContent = data.query_time_ms + 'ms';
  if (ramEl) ramEl.textContent = data.memory_peak_mb + 'MB';

  if (dotEl) {
    if (data.duration_ms > 600) {
      dotEl.style.background = '#f43f5e';
      dotEl.style.boxShadow = '0 0 8px #f43f5e';
    } else if (data.duration_ms > 300) {
      dotEl.style.background = '#f59e0b';
      dotEl.style.boxShadow = '0 0 8px #f59e0b';
    } else {
      dotEl.style.background = '#10b981';
      dotEl.style.boxShadow = '0 0 8px #10b981';
    }
  }

  // Populate Drawer Details
  var routeEl = document.getElementById('itam-hud-route');
  var routeName = data.route || window.location.pathname;
  if (routeEl) {
    routeEl.textContent = routeName;
    routeEl.title = routeName;
  }

  var cardDurEl = document.getElementById('itam-hud-card-duration');
  var cardQEl = document.getElementById('itam-hud-card-queries');
  var cardRamEl = document.getElementById('itam-hud-card-ram');

  if (cardDurEl) cardDurEl.textContent = data.duration_ms + ' ms';
  if (cardQEl) cardQEl.textContent = data.query_count + ' (' + data.query_time_ms + ' ms)';
  if (cardRamEl) cardRamEl.textContent = data.memory_peak_mb + ' MB';

  var countBadge = document.getElementById('itam-hud-query-count-badge');
  var isId = '{{ App::getLocale() }}' === 'id';
  if (countBadge) countBadge.textContent = data.query_count + (isId ? ' query' : ' queries');

  // Populate Queries List
  var listEl = document.getElementById('itam-hud-queries-list');
  if (listEl) {
    listEl.innerHTML = '';
    if (!data.queries || data.queries.length === 0) {
      listEl.innerHTML = '<div class="text-center py-3 text-muted" style="font-size:0.75rem;">{{ __("messages.perf_hud_no_queries") }}</div>';
    } else {
      var expandText = isId ? 'Klik untuk perluas' : 'Click to expand';
      var collapseText = isId ? 'Klik untuk ciutkan' : 'Click to collapse';

      data.queries.forEach(function(q, idx) {
        var isSlow = q.time > 40;
        var badgeColor = isSlow ? '#f43f5e' : (q.time > 15 ? '#f59e0b' : '#10b981');
        var item = document.createElement('div');
        item.className = 'itam-hud-query-item';
        if (isSlow) item.style.borderColor = 'rgba(244, 63, 94, 0.35)';

        item.innerHTML = `
          <div class="itam-hud-query-meta">
            <span class="itam-hud-query-index">#${idx + 1}</span>
            <span class="itam-hud-query-time" style="color: ${badgeColor};">${q.time} ms</span>
          </div>
          <div class="itam-hud-query-sql">${__itamEscapeHtml(q.sql)}</div>
          <div class="itam-hud-query-expand-hint">
            <span class="hint-text">${expandText}</span>
            <i class="fas fa-chevron-down itam-hud-chevron"></i>
          </div>
        `;

        item.onclick = function() {
          var isExp = this.classList.toggle('is-expanded');
          var ht = this.querySelector('.hint-text');
          if (ht) ht.textContent = isExp ? collapseText : expandText;
        };

        listEl.appendChild(item);
      });
    }
  }
};

// Initial trigger if data already available
if (window.__itamTelemetry) {
  window.__itamRenderHud();
}
</script>
@endif

@stack('scripts')
</body>
</html>
