<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $app_name ?? 'ITAM Enterprise' }} | @yield('title', 'Dashboard')</title>
  
  <!-- Favicon & PWA App Manifest -->
  <link rel="icon" href="{{ vasset('logo.png') }}" type="image/png">
  <link rel="manifest" href="{{ vasset('manifest.json') }}">
  <meta name="theme-color" content="#0f172a">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="ITAM Enterprise">
  <link rel="apple-touch-icon" href="{{ vasset('logo.png') }}">

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
       ITAM Enterprise — NextGen Glassmorphic UI Theme
       ══════════════════════════════════════════════════════ -->
  <style>
    /* ── Design Tokens ──────────────────────────────────── */
    :root {
      --color-paper-0: oklch(98.4% 0.005 258);
      --color-paper-1: oklch(96.2% 0.010 258);
      --color-paper-2: oklch(93.0% 0.015 258);
      --color-paper-3: oklch(89.0% 0.020 258);
      --color-ink-0:   oklch(18.0% 0.030 258);
      --color-ink-1:   oklch(35.0% 0.025 258);
      --color-ink-2:   oklch(52.0% 0.018 258);
      --color-ink-3:   oklch(70.0% 0.012 258);

      --color-accent:       oklch(54.0% 0.220 268);
      --color-accent-soft:  oklch(72.0% 0.140 268);
      --color-accent-tint:  oklch(94.0% 0.040 268);
      --color-companion:    oklch(82.0% 0.180 130);
      --color-warning:      oklch(74.0% 0.180 50);
      --color-success:      oklch(68.0% 0.150 145);
      --color-danger:       oklch(58.0% 0.200 25);
      --color-focus:        oklch(54.0% 0.220 268);

      --font-display: 'Plus Jakarta Sans', system-ui, sans-serif;
      --font-body:    'Plus Jakarta Sans', system-ui, sans-serif;
      --font-mono:    'JetBrains Mono', ui-monospace, monospace;

      --space-xs:  4px;
      --space-sm:  8px;
      --space-md:  16px;
      --space-lg:  24px;
      --space-xl:  32px;
      --space-2xl: 48px;

      --radius-sm:   6px;
      --radius-md:   10px;
      --radius-lg:   16px;
      --radius-xl:   20px;
      --radius-pill: 999px;

      --rule-soft: 1px solid color-mix(in oklch, var(--color-ink-0) 9%, transparent);

      --shadow-sm:         0 1px 2px rgba(30, 30, 80, 0.06);
      --shadow-card:       0 1px 3px rgba(30,30,80,0.06), 0 4px 16px rgba(79,70,229,0.07);
      --shadow-card-hover: 0 4px 24px rgba(79,70,229,0.16);
      --shadow-nav:        0 1px 0 rgba(255,255,255,0.7) inset, 0 8px 30px -12px rgba(20,30,80,0.18);

      --dur-fast:    150ms;
      --dur-normal:  250ms;
      --ease-out:    cubic-bezier(0.16, 1, 0.3, 1);
      --ease-in-out: cubic-bezier(0.45, 0, 0.55, 1);

      /* ── Legacy Variables Mapping ───────────────────────── */
      --tech-bg: var(--color-paper-0);
      --tech-panel: var(--color-paper-0);
      --tech-border: color-mix(in oklch, var(--color-ink-0) 12%, transparent);
      --glass-blur: 0px;
      --neon-cyan: var(--color-accent);
      --neon-purple: oklch(65% 0.12 280);
      --text-main: var(--color-ink-0);
      --text-muted: var(--color-ink-2);
      --input-bg: var(--color-paper-1);
    }

    /* ── Base ───────────────────────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; }

    html, body {
      font-family: var(--font-body) !important;
      color: var(--color-ink-0) !important;
      -webkit-font-smoothing: antialiased;
      text-rendering: optimizeLegibility;
    }

    body {
      background: var(--color-paper-0) !important;
    }

    ::selection { background: var(--color-accent); color: var(--color-paper-0); }

    :focus-visible {
      outline: 2px solid var(--color-focus);
      outline-offset: 3px;
      border-radius: var(--radius-sm);
    }

    h1, h2, h3, h4, h5, h6 {
      font-family: var(--font-display) !important;
      font-weight: 600;
      letter-spacing: -0.02em;
      color: var(--color-ink-0) !important;
    }

    /* ── Scrollbar ──────────────────────────────────────── */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: var(--color-paper-1); }
    ::-webkit-scrollbar-thumb {
      background: color-mix(in oklch, var(--color-accent) 30%, var(--color-paper-2));
      border-radius: var(--radius-pill);
    }
    ::-webkit-scrollbar-thumb:hover { background: var(--color-accent-soft); }

    /* ── Top Navbar ─────────────────────────────────────── */
    .main-header.navbar {
      background: color-mix(in oklch, var(--color-paper-0) 85%, transparent) !important;
      -webkit-backdrop-filter: blur(20px);
      backdrop-filter: blur(20px);
      border-bottom: var(--rule-soft) !important;
      box-shadow: var(--shadow-sm) !important;
      min-height: 60px;
      padding: 0 var(--space-md);
    }

    .main-header .navbar-nav .nav-link {
      color: var(--color-ink-1) !important;
      font-size: 0.875rem;
      font-weight: 500;
      transition: color var(--dur-fast) var(--ease-out);
    }
    .main-header .navbar-nav .nav-link:hover {
      color: var(--color-accent) !important;
    }

    /* Navbar right-side pill buttons */
    .nav-pill-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 14px;
      height: 36px;
      border-radius: var(--radius-pill);
      font-size: 0.8125rem;
      font-weight: 500;
      background: var(--color-paper-1);
      border: var(--rule-soft);
      color: var(--color-ink-1) !important;
      transition: background var(--dur-fast) var(--ease-out), color var(--dur-fast) var(--ease-out), border-color var(--dur-fast) var(--ease-out);
      text-decoration: none;
    }
    .nav-pill-btn:hover {
      background: var(--color-accent-tint);
      color: var(--color-accent) !important;
      border-color: color-mix(in oklch, var(--color-accent) 25%, transparent);
      text-decoration: none;
    }
    .nav-pill-btn.danger {
      background: oklch(97% 0.015 25);
      color: var(--color-danger) !important;
      border-color: color-mix(in oklch, var(--color-danger) 20%, transparent);
    }
    .nav-pill-btn.danger:hover {
      background: oklch(94% 0.030 25);
    }

    /* ── Main Sidebar ───────────────────────────────────── */
    .main-sidebar, .main-sidebar::before {
      background: var(--color-paper-0) !important;
      border-right: var(--rule-soft) !important;
      box-shadow: 2px 0 8px rgba(30,30,80,0.04) !important;
    }

    /* Override AdminLTE dark-sidebar class */
    .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active,
    .sidebar-light-primary .nav-sidebar > .nav-item > .nav-link.active {
      background: var(--color-accent-tint) !important;
      color: var(--color-accent) !important;
      box-shadow: none !important;
    }

    .brand-link {
      background: var(--color-paper-0) !important;
      border-bottom: var(--rule-soft) !important;
      height: 60px;
      display: flex;
      align-items: center;
      padding: 0 var(--space-md);
      transition: background var(--dur-fast) var(--ease-out);
    }

    .brand-link:hover { background: var(--color-paper-1) !important; }

    .brand-text {
      font-family: var(--font-display) !important;
      font-weight: 700;
      font-size: 0.95rem;
      letter-spacing: -0.02em;
      color: var(--color-ink-0) !important;
      -webkit-text-fill-color: unset !important;
      background: none !important;
      -webkit-background-clip: unset !important;
    }

    /* Sidebar nav items */
    .nav-sidebar .nav-item > .nav-link {
      border-radius: var(--radius-md) !important;
      margin: 2px 8px !important;
      padding: 9px 14px !important;
      color: var(--color-ink-1) !important;
      font-size: 0.875rem;
      font-weight: 500;
      transition: background var(--dur-fast) var(--ease-out), color var(--dur-fast) var(--ease-out);
    }
    .nav-sidebar .nav-item > .nav-link:hover {
      background: var(--color-paper-2) !important;
      color: var(--color-ink-0) !important;
    }
    .nav-sidebar .nav-item > .nav-link.active {
      background: var(--color-accent-tint) !important;
      color: var(--color-accent) !important;
    }
    .nav-sidebar .nav-item > .nav-link .nav-icon {
      color: inherit !important;
      opacity: 0.7;
      width: 1.4em;
    }
    .nav-sidebar .nav-item > .nav-link.active .nav-icon {
      opacity: 1;
    }

    /* Sidebar submenu */
    .nav-treeview > .nav-item > .nav-link {
      border-radius: var(--radius-md) !important;
      margin: 1px 8px 1px 24px !important;
      padding: 7px 12px !important;
      color: var(--color-ink-2) !important;
      font-size: 0.8125rem;
    }
    .nav-treeview > .nav-item > .nav-link:hover {
      background: var(--color-paper-2) !important;
      color: var(--color-ink-0) !important;
    }

    /* Shared Theme Scroll Container for all Tables */
    .theme-scroll-container,
    .health-scroll-container,
    .anomaly-scroll-container,
    .budget-scroll-container,
    .activity-scroll-container {
      max-height: calc(100vh - 220px);
      overflow-y: auto;
    }
    .theme-scroll-container::-webkit-scrollbar {
      width: 6px;
      height: 6px;
    }
    .theme-scroll-container::-webkit-scrollbar-track {
      background: rgba(0, 0, 0, 0.15);
      border-radius: 8px;
    }
    .theme-scroll-container::-webkit-scrollbar-thumb {
      background: var(--color-accent-soft, #3b82f6);
      border-radius: 8px;
    }
    .theme-scroll-container thead th {
      position: sticky;
      top: 0;
      z-index: 10;
      background: var(--color-paper-1, #1e293b) !important;
      box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    /* Flexbox & Fixed Layout - Eliminates Sidebar/Footer Misalignment */
    html, body {
      height: 100% !important;
      min-height: 100% !important;
      margin: 0 !important;
      padding: 0 !important;
      overflow-x: hidden !important;
    }
    .wrapper {
      display: flex !important;
      flex-direction: column !important;
      min-height: 100vh !important;
      position: relative !important;
    }
    .main-sidebar {
      position: fixed !important;
      top: 0 !important;
      bottom: 0 !important;
      left: 0 !important;
      height: 100vh !important;
      height: 100dvh !important;
      z-index: 1038 !important;
      display: flex !important;
      flex-direction: column !important;
    }
    .sidebar {
      flex: 1 1 auto !important;
      overflow-y: auto !important;
      overflow-x: hidden !important;
      -webkit-overflow-scrolling: touch !important;
      padding-bottom: 24px !important;
    }
    .content-wrapper {
      flex: 1 0 auto !important;
      display: flex !important;
      flex-direction: column !important;
      min-height: calc(100vh - 60px) !important;
    }
    .content {
      flex: 1 0 auto !important;
      display: flex !important;
      flex-direction: column !important;
    }
    .content > .container-fluid {
      flex: 1 0 auto !important;
    }
    .main-footer {
      position: relative !important;
      bottom: 0 !important;
      margin-top: auto !important;
      margin-left: 0 !important;
      width: 100% !important;
      flex-shrink: 0 !important;
      background: var(--color-paper-0) !important;
      border-top: var(--rule-soft) !important;
      padding: 12px 20px !important;
      z-index: 1020 !important;
    }
    body.dark-mode .main-footer {
      background: #090d16 !important;
      border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
    }

    /* Mobile Responsive Optimizations for Modern Feature Components */
    @media (max-width: 768px) {
      .content-header {
        padding: 6px 10px !important;
      }
      .content-header h1 {
        font-size: 1.1rem !important;
      }
      .container-fluid {
        padding-left: 6px !important;
        padding-right: 6px !important;
        padding-bottom: 0 !important;
      }
      .theme-scroll-container,
      .health-scroll-container,
      .anomaly-scroll-container,
      .budget-scroll-container,
      .activity-scroll-container {
        max-height: calc(100vh - 280px) !important;
        min-height: 250px !important;
        -webkit-overflow-scrolling: touch;
      }
      .content-wrapper, .content {
        padding-bottom: 0 !important;
      }
      .card {
        margin-bottom: 8px !important;
      }
      .card-header {
        padding: 8px 12px !important;
      }
      .card-header.d-flex,
      .card-header .d-flex {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 6px !important;
      }
      .card-tools {
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        gap: 4px !important;
      }
      .table th, .table td {
        white-space: nowrap !important;
        padding: 6px 8px !important;
        font-size: 0.78rem !important;
      }
      .metric-value,
      .budget-value {
        font-size: 1.15rem !important;
      }
      .health-metric-card,
      .anomaly-metric-card,
      .budget-metric-card,
      .timeline-card {
        padding: 10px 12px !important;
        margin-bottom: 8px !important;
      }
      .main-footer {
        padding: 8px 10px !important;
        text-align: center !important;
      }
      .main-footer .float-right {
        float: none !important;
        display: block !important;
        margin-bottom: 2px;
      }
      .modal-dialog {
        margin: 6px !important;
      }
      .modal-content {
        border-radius: var(--radius-md) !important;
      }
    }

    .nav-treeview > .nav-item > .nav-link.active {
      background: var(--color-accent-tint) !important;
      color: var(--color-accent) !important;
    }
    .nav-sidebar .nav-item > .nav-link .far.fa-circle {
      color: inherit !important;
    }

    .nav-header {
      color: var(--color-ink-3) !important;
      font-family: var(--font-mono) !important;
      font-size: 0.6875rem !important;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      padding: 12px 16px 4px !important;
    }

    /* Treeview arrow */
    .nav-sidebar .right.fa-angle-left { color: var(--color-ink-3) !important; }

    /* ── Content Wrapper ────────────────────────────────── */
    .content-wrapper {
      background: transparent !important;
    }

    .content-header h1 {
      font-size: 1.5rem !important;
      font-weight: 700;
      letter-spacing: -0.03em;
      color: var(--color-ink-0) !important;
    }

    /* ── Cards ──────────────────────────────────────────── */
    .card, .info-box, .small-box {
      background: var(--color-paper-0) !important;
      border: var(--rule-soft) !important;
      border-radius: var(--radius-lg) !important;
      box-shadow: var(--shadow-card) !important;
      backdrop-filter: none;
      -webkit-backdrop-filter: none;
      transition: all var(--dur-normal) var(--ease-out);
    }
    .card:hover, .small-box:hover {
      box-shadow: var(--shadow-card-hover) !important;
      transform: translateY(-3px);
    }

    /* ── Clean Enterprise Dark Mode ────────────────────────── */
    body.dark-mode {
      background: #090d16 !important;
      color: #f8fafc !important;
    }

    body.dark-mode .card, 
    body.dark-mode .info-box, 
    body.dark-mode .small-box,
    body.dark-mode .cat-mini-card {
      background: #0f172a !important;
      border: 1px solid #1e293b !important;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2) !important;
    }

    body.dark-mode .card:hover,
    body.dark-mode .cat-mini-card:hover {
      border-color: #334155 !important;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3) !important;
      transform: translateY(-1px);
    }

    /* ── NextGen Vibrant Badges ────────────────────────── */
    .badge {
      font-family: var(--font-body) !important;
      font-weight: 600;
      padding: 6px 12px;
      border-radius: var(--radius-pill);
      font-size: 0.75rem;
      letter-spacing: 0.3px;
    }
    .badge-primary, .badge-indigo {
      background: rgba(99, 102, 241, 0.18) !important;
      color: #818cf8 !important;
      border: 1px solid rgba(99, 102, 241, 0.35) !important;
    }
    .badge-success {
      background: rgba(16, 185, 129, 0.18) !important;
      color: #34d399 !important;
      border: 1px solid rgba(16, 185, 129, 0.35) !important;
    }
    .badge-warning {
      background: rgba(245, 158, 11, 0.18) !important;
      color: #fbbf24 !important;
      border: 1px solid rgba(245, 158, 11, 0.35) !important;
    }
    .badge-danger {
      background: rgba(239, 68, 68, 0.18) !important;
      color: #f87171 !important;
      border: 1px solid rgba(239, 68, 68, 0.35) !important;
    }
    .badge-info, .badge-cyan {
      background: rgba(6, 182, 212, 0.18) !important;
      color: #38bdf8 !important;
      border: 1px solid rgba(6, 182, 212, 0.35) !important;
    }
    .badge-secondary {
      background: rgba(148, 163, 184, 0.18) !important;
      color: #cbd5e1 !important;
      border: 1px solid rgba(148, 163, 184, 0.35) !important;
    }

    /* ── Clean Enterprise Button Styling ───────────────────────── */
    .btn-primary {
      background: #4f46e5 !important;
      border: 1px solid #4338ca !important;
      color: #ffffff !important;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
      transition: all 0.15s ease;
    }
    .btn-primary:hover {
      background: #4338ca !important;
      border-color: #3730a3 !important;
      box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25) !important;
      transform: translateY(-1px);
    }

    .card-header {
      background: transparent !important;
      border-bottom: var(--rule-soft) !important;
      padding: var(--space-md) var(--space-lg);
    }
    .card-title {
      color: var(--color-ink-0) !important;
      font-weight: 600;
      font-size: 0.9375rem;
    }
    .card-body { color: var(--color-ink-1) !important; }

    /* ── Small Stat Boxes ───────────────────────────────── */
    .small-box { overflow: hidden; }
    .small-box > .inner { padding: var(--space-lg); }
    .small-box > .inner h3 {
      font-size: 2.25rem !important;
      font-weight: 700;
      letter-spacing: -0.04em;
      color: var(--color-ink-0) !important;
    }
    .small-box > .inner p {
      font-size: 0.8125rem;
      font-weight: 500;
      color: var(--color-ink-2) !important;
      text-transform: uppercase;
      letter-spacing: 0.06em;
    }
    .small-box > .icon > i {
      font-size: 70px !important;
      color: color-mix(in oklch, var(--color-ink-0) 6%, transparent) !important;
    }
    .small-box-footer {
      background: var(--color-paper-2) !important;
      color: var(--color-ink-2) !important;
      font-size: 0.8125rem;
      font-weight: 500;
      border-bottom-left-radius: var(--radius-lg) !important;
      border-bottom-right-radius: var(--radius-lg) !important;
      padding: 10px var(--space-lg);
      transition: background var(--dur-fast), color var(--dur-fast);
    }
    .small-box-footer:hover {
      background: var(--color-accent-tint) !important;
      color: var(--color-accent) !important;
    }

    /* Accent left borders for stat boxes */
    .small-box.accent-indigo  { border-left: 3px solid var(--color-accent) !important; }
    .small-box.accent-lime    { border-left: 3px solid var(--color-companion) !important; }
    .small-box.accent-success { border-left: 3px solid var(--color-success) !important; }
    .small-box.accent-warning { border-left: 3px solid var(--color-warning) !important; }
    .small-box.accent-soft    { border-left: 3px solid var(--color-accent-soft) !important; }
    .small-box.accent-danger  { border-left: 3px solid var(--color-danger) !important; }

    /* ── Tables ─────────────────────────────────────────── */
    .table, .theme-table {
      color: var(--color-ink-1) !important;
    }
    .table thead th {
      font-family: var(--font-mono) !important;
      font-size: 0.6875rem;
      font-weight: 500;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      color: var(--color-ink-2) !important;
      border-bottom: var(--rule-soft) !important;
      border-top: none !important;
      padding: 10px 12px;
    }
    .table tbody td {
      border-top: var(--rule-soft) !important;
      padding: 10px 12px;
      color: var(--color-ink-1) !important;
      font-size: 0.875rem;
      vertical-align: middle;
    }
    .table tbody tr:hover td {
      background: var(--color-paper-1) !important;
    }
    .table-striped tbody tr:nth-of-type(odd) {
      background: transparent !important;
    }

    /* ── Sticky Action Column (Right Side) ──────────────── */
    .table thead th:last-child {
      position: sticky !important;
      right: -1px !important; /* -1px to avoid 1px gap from sub-pixel rendering */
      top: 0 !important; 
      z-index: 20 !important; 
      background: var(--color-paper-1, #1e293b) !important;
      border-left: var(--rule-soft) !important;
      box-shadow: -4px 0 8px rgba(0,0,0,0.04) !important;
    }
    
    .table tbody td:last-child {
      position: sticky !important;
      right: -1px !important;
      z-index: 5 !important;
      background: var(--color-paper-0) !important;
      border-left: var(--rule-soft) !important;
      box-shadow: -4px 0 8px rgba(0,0,0,0.02) !important;
    }
    
    /* Ensure hover state still applies to sticky cell */
    .table tbody tr:hover td:last-child {
      background: var(--color-paper-1) !important;
    }

    /* ── Sticky First Column (Left Side - Numbering) ────── */
    .table thead th:first-child {
      position: sticky !important;
      left: -1px !important; 
      top: 0 !important; 
      z-index: 20 !important; 
      background: var(--color-paper-1, #1e293b) !important;
      border-right: var(--rule-soft) !important;
      box-shadow: 4px 0 8px rgba(0,0,0,0.04) !important;
    }
    
    .table tbody td:first-child {
      position: sticky !important;
      left: -1px !important;
      z-index: 5 !important;
      background: var(--color-paper-0) !important;
      border-right: var(--rule-soft) !important;
      box-shadow: 4px 0 8px rgba(0,0,0,0.02) !important;
    }
    
    .table tbody tr:hover td:first-child {
      background: var(--color-paper-1) !important;
    }

    /* ── Forms ──────────────────────────────────────────── */
    .form-control, .custom-select, select.form-control {
      background: var(--color-paper-0) !important;
      border: var(--rule-soft) !important;
      border-radius: var(--radius-md) !important;
      color: var(--color-ink-0) !important;
      font-family: var(--font-body) !important;
      font-size: 0.875rem;
      transition: border-color var(--dur-fast), box-shadow var(--dur-fast);
    }
    .form-control:focus, .custom-select:focus, select.form-control:focus {
      background: var(--color-paper-0) !important;
      color: var(--color-ink-0) !important;
      border-color: var(--color-accent) !important;
      box-shadow: 0 0 0 3px color-mix(in oklch, var(--color-accent) 15%, transparent) !important;
    }
    .form-control::placeholder { color: var(--color-ink-3) !important; }
    label { color: var(--color-ink-1) !important; font-size: 0.8125rem; font-weight: 500; }

    .input-group-text {
      background: var(--color-paper-1) !important;
      border: var(--rule-soft) !important;
      color: var(--color-ink-2) !important;
    }

    /* ── Buttons ────────────────────────────────────────── */
    .btn {
      font-family: var(--font-body) !important;
      font-weight: 500;
      border-radius: var(--radius-pill) !important;
      transition: all var(--dur-fast) var(--ease-out);
      font-size: 0.875rem;
      letter-spacing: 0.01em;
    }

    /* Primary = dark pill */
    .btn-primary {
      background: var(--color-ink-0) !important;
      border-color: var(--color-ink-0) !important;
      color: var(--color-paper-0) !important;
      box-shadow: 0 1px 2px rgba(0,0,0,0.15);
    }
    .btn-primary:hover {
      background: var(--color-accent) !important;
      border-color: var(--color-accent) !important;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px color-mix(in oklch, var(--color-accent) 30%, transparent);
    }
    .btn-primary:active { transform: translateY(0); }

    /* Secondary */
    .btn-secondary, .btn-default {
      background: var(--color-paper-1) !important;
      border: var(--rule-soft) !important;
      color: var(--color-ink-1) !important;
    }
    .btn-secondary:hover, .btn-default:hover {
      background: var(--color-paper-2) !important;
      color: var(--color-ink-0) !important;
    }

    /* Danger */
    .btn-danger {
      background: oklch(95% 0.025 25) !important;
      border-color: color-mix(in oklch, var(--color-danger) 25%, transparent) !important;
      color: var(--color-danger) !important;
    }
    .btn-danger:hover {
      background: var(--color-danger) !important;
      border-color: var(--color-danger) !important;
      color: white !important;
    }

    /* Success */
    .btn-success {
      background: oklch(95% 0.025 145) !important;
      border-color: color-mix(in oklch, var(--color-success) 25%, transparent) !important;
      color: oklch(38% 0.120 145) !important;
    }
    .btn-success:hover {
      background: var(--color-success) !important;
      border-color: var(--color-success) !important;
      color: white !important;
    }

    /* Warning */
    .btn-warning {
      background: oklch(96% 0.035 50) !important;
      border-color: color-mix(in oklch, var(--color-warning) 30%, transparent) !important;
      color: oklch(42% 0.140 50) !important;
    }
    .btn-warning:hover {
      background: var(--color-warning) !important;
      border-color: var(--color-warning) !important;
      color: white !important;
    }

    /* Info */
    .btn-info {
      background: var(--color-accent-tint) !important;
      border-color: color-mix(in oklch, var(--color-accent) 25%, transparent) !important;
      color: var(--color-accent) !important;
    }
    .btn-info:hover {
      background: var(--color-accent) !important;
      border-color: var(--color-accent) !important;
      color: white !important;
    }

    /* Action icon buttons */
    .action-btn {
      width: 32px !important;
      height: 32px !important;
      border-radius: var(--radius-md) !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      transition: all var(--dur-fast) var(--ease-out);
      padding: 0 !important;
      font-size: 0.875rem !important;
    }
    .action-btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    /* ── Badges ─────────────────────────────────────────── */
    .badge {
      font-family: var(--font-mono) !important;
      font-size: 0.6875rem !important;
      font-weight: 500;
      letter-spacing: 0.04em;
      padding: 4px 10px !important;
      border-radius: var(--radius-pill) !important;
    }
    .badge-primary   { background: var(--color-accent-tint) !important; color: var(--color-accent) !important; }
    .badge-success   { background: oklch(93% 0.050 145) !important; color: oklch(35% 0.120 145) !important; }
    .badge-danger    { background: oklch(94% 0.030 25) !important;  color: oklch(45% 0.180 25) !important; }
    .badge-warning   { background: oklch(95% 0.050 50) !important;  color: oklch(42% 0.160 50) !important; }
    .badge-info      { background: var(--color-accent-tint) !important; color: var(--color-accent) !important; }
    .badge-secondary { background: var(--color-paper-2) !important; color: var(--color-ink-2) !important; }

    /* ── Modals & Mobile Responsiveness Fix ─────────────── */
    body.modal-open {
      overflow: hidden !important;
    }
    .modal {
      position: fixed !important;
      top: 0 !important;
      right: 0 !important;
      bottom: 0 !important;
      left: 0 !important;
      z-index: 1060 !important;
      overflow-x: hidden !important;
      overflow-y: auto !important;
      -webkit-overflow-scrolling: touch !important;
      padding-right: 0 !important;
    }
    .modal-backdrop {
      position: fixed !important;
      top: 0 !important;
      right: 0 !important;
      bottom: 0 !important;
      left: 0 !important;
      z-index: 1050 !important;
      background-color: rgba(0, 0, 0, 0.7) !important;
      -webkit-backdrop-filter: blur(5px);
      backdrop-filter: blur(5px);
    }
    .modal-dialog {
      position: relative !important;
      z-index: 1061 !important;
      margin: 1rem auto !important;
      max-width: 500px !important;
      width: calc(100% - 1.5rem) !important;
      pointer-events: auto !important;
    }
    .modal-dialog-centered {
      display: flex !important;
      align-items: center !important;
      min-height: calc(100% - 2rem) !important;
    }
    @media (max-width: 575.98px) {
      .modal-dialog {
        margin: 0.5rem auto !important;
        max-width: calc(100% - 1rem) !important;
        width: calc(100% - 1rem) !important;
      }
      .modal-dialog-centered {
        min-height: calc(100% - 1rem) !important;
      }
    }
    .modal-content {
      background: var(--color-paper-0) !important;
      border: var(--rule-soft) !important;
      border-radius: var(--radius-xl) !important;
      box-shadow: 0 25px 70px rgba(0, 0, 0, 0.5) !important;
      color: var(--color-ink-0) !important;
      overflow: hidden;
      pointer-events: auto !important;
    }
    .modal-header {
      border-bottom: var(--rule-soft) !important;
      padding: var(--space-md) var(--space-lg) !important;
      align-items: center;
    }
    .modal-body {
      padding: var(--space-lg) !important;
    }
    .modal-footer {
      border-top: var(--rule-soft) !important;
      padding: var(--space-md) var(--space-lg) !important;
    }
    .modal-title { color: var(--color-ink-0) !important; font-weight: 600; font-size: 1.1rem; }
    .close { color: var(--color-ink-2) !important; opacity: 0.8; }
    .close:hover { color: var(--color-ink-0) !important; opacity: 1; }

    /* Custom Mobile-Friendly File Upload Dropzone */
    .file-dropzone {
      border: 2px dashed color-mix(in oklch, var(--color-accent) 40%, var(--color-paper-2));
      border-radius: var(--radius-lg);
      padding: 1.5rem 1rem;
      text-align: center;
      background: color-mix(in oklch, var(--color-accent) 4%, transparent);
      cursor: pointer;
      position: relative;
      transition: all var(--dur-fast) var(--ease-out);
      user-select: none;
      -webkit-user-select: none;
      touch-action: manipulation;
    }
    .file-dropzone:hover, .file-dropzone.active {
      border-color: var(--color-accent);
      background: color-mix(in oklch, var(--color-accent) 10%, transparent);
    }
    .file-dropzone input[type="file"] {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      opacity: 0;
      cursor: pointer;
      z-index: 10;
      display: block !important;
    }
    .file-dropzone .icon-box {
      width: 48px;
      height: 48px;
      border-radius: var(--radius-pill);
      background: var(--color-accent-tint);
      color: var(--color-accent);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 1.25rem;
      margin-bottom: 0.75rem;
    }

    /* ── Alerts ─────────────────────────────────────────── */
    .alert {
      border-radius: var(--radius-md) !important;
      border: none !important;
      font-size: 0.875rem;
    }
    .alert-success { background: oklch(93% 0.050 145) !important; color: oklch(35% 0.120 145) !important; }
    .alert-danger  { background: oklch(94% 0.030 25) !important;  color: oklch(40% 0.180 25) !important; }
    .alert-warning { background: oklch(95% 0.050 50) !important;  color: oklch(38% 0.160 50) !important; }
    .alert-info    { background: var(--color-accent-tint) !important; color: var(--color-accent) !important; }

    /* ── Pagination Spacing & Modern Pill Style ───────────── */
    .pagination {
      display: flex !important;
      flex-wrap: wrap !important;
      gap: 6px !important;
      justify-content: center !important;
      align-items: center !important;
      margin: 0.5rem 0 !important;
      padding: 0 !important;
      list-style: none !important;
    }
    .pagination .page-item {
      margin: 0 !important;
    }
    .pagination .page-link {
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      min-width: 36px !important;
      height: 36px !important;
      padding: 0 12px !important;
      color: var(--color-ink-1) !important;
      background: var(--color-paper-0) !important;
      border: var(--rule-soft) !important;
      border-radius: var(--radius-md) !important;
      font-size: 0.875rem !important;
      font-weight: 500 !important;
      transition: all var(--dur-fast) var(--ease-out) !important;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
    }
    .pagination .page-link:hover {
      background: var(--color-accent-tint) !important;
      color: var(--color-accent) !important;
      border-color: color-mix(in oklch, var(--color-accent) 30%, transparent) !important;
      transform: translateY(-1px) !important;
      box-shadow: 0 3px 8px color-mix(in oklch, var(--color-accent) 15%, transparent) !important;
    }
    .pagination .page-item.active .page-link {
      background: var(--color-accent) !important;
      border-color: var(--color-accent) !important;
      color: #ffffff !important;
      box-shadow: 0 4px 12px color-mix(in oklch, var(--color-accent) 35%, transparent) !important;
    }
    .pagination .page-item.disabled .page-link {
      background: var(--color-paper-1) !important;
      color: var(--color-ink-3) !important;
      opacity: 0.5 !important;
      cursor: not-allowed !important;
      box-shadow: none !important;
    }
    @media (max-width: 575.98px) {
      .pagination {
        gap: 4px !important;
      }
      .pagination .page-link {
        min-width: 32px !important;
        height: 32px !important;
        padding: 0 8px !important;
        font-size: 0.8125rem !important;
        border-radius: var(--radius-sm) !important;
      }
    }

    /* ── Select2 ────────────────────────────────────────── */
    .select2-container--default .select2-selection--single {
      background: var(--color-paper-0) !important;
      border: var(--rule-soft) !important;
      border-radius: var(--radius-md) !important;
      height: 38px !important;
      display: flex;
      align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
      color: var(--color-ink-0) !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 36px !important;
    }
    .select2-dropdown {
      background: var(--color-paper-0) !important;
      border: var(--rule-soft) !important;
      border-radius: var(--radius-md) !important;
      box-shadow: var(--shadow-card-hover) !important;
      color: var(--color-ink-0) !important;
    }
    .select2-container--default .select2-results__option--selected {
      background: var(--color-accent-tint) !important;
      color: var(--color-accent) !important;
    }
    .select2-container--default .select2-results__option--highlighted {
      background: var(--color-accent) !important;
      color: white !important;
    }
    .select2-search--dropdown .select2-search__field {
      background: var(--color-paper-1) !important;
      border: var(--rule-soft) !important;
      color: var(--color-ink-0) !important;
      border-radius: var(--radius-sm) !important;
    }
    select option {
      background: var(--color-paper-0) !important;
      color: var(--color-ink-0) !important;
    }

    /* ── Footer ─────────────────────────────────────────── */
    .main-footer {
      background: var(--color-paper-0) !important;
      border-top: var(--rule-soft) !important;
      color: var(--color-ink-3) !important;
      font-size: 0.8125rem;
    }
    .main-footer strong { color: var(--color-ink-1) !important; }

    /* ── Eyebrow / Mono labels ──────────────────────────── */
    .eyebrow, .mono-label {
      font-family: var(--font-mono) !important;
      font-size: 0.6875rem;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: var(--color-ink-2) !important;
    }

    /* ── Utility helpers ────────────────────────────────── */
    .theme-text  { color: var(--color-ink-0) !important; }
    .theme-muted { color: var(--color-ink-2) !important; }
    .theme-card  {
      background: var(--color-paper-0) !important;
      border: var(--rule-soft) !important;
      border-radius: var(--radius-lg) !important;
      box-shadow: var(--shadow-card) !important;
    }
    .theme-input {
      background: var(--color-paper-0) !important;
      border: var(--rule-soft) !important;
      color: var(--color-ink-0) !important;
    }
    .theme-input:focus {
      border-color: var(--color-accent) !important;
      box-shadow: 0 0 0 3px color-mix(in oklch, var(--color-accent) 15%, transparent) !important;
    }

    /* ── Category mini-cards (dashboard) ────────────────── */
    .cat-mini-card {
      background: var(--color-paper-0) !important;
      border: var(--rule-soft) !important;
      border-radius: var(--radius-lg) !important;
      box-shadow: var(--shadow-card) !important;
      transition: all var(--dur-normal) var(--ease-out);
    }
    .cat-mini-card:hover {
      box-shadow: var(--shadow-card-hover) !important;
      transform: translateY(-2px);
      border-color: color-mix(in oklch, var(--color-accent) 30%, transparent) !important;
    }
    .cat-mini-card h3, .cat-mini-card .cat-count {
      color: var(--color-accent) !important;
      font-weight: 700;
      font-size: 1.5rem;
    }
    .cat-mini-card span, .cat-mini-card .cat-label {
      color: var(--color-ink-2) !important;
      font-size: 0.75rem;
      font-weight: 500;
    }

    /* Remove old neon-cyan/purple text fills */
    .text-info { color: var(--color-accent) !important; }
    .text-purple { color: var(--color-accent-soft) !important; }
    .text-cyan { color: var(--color-accent) !important; }
    .text-success { color: var(--color-success) !important; }
    .text-warning { color: var(--color-warning) !important; }
    .text-danger { color: var(--color-danger) !important; }
    .text-muted { color: var(--color-ink-3) !important; }

    /* ── Breadcrumbs ────────────────────────────────────── */
    .breadcrumb {
      background: transparent !important;
      padding: 0;
    }
    .breadcrumb-item a { color: var(--color-accent) !important; }
    .breadcrumb-item.active { color: var(--color-ink-2) !important; }
    .breadcrumb-item + .breadcrumb-item::before { color: var(--color-ink-3) !important; }

    /* ── Dropdown menus ─────────────────────────────────── */
    .dropdown-menu {
      background: var(--color-paper-0) !important;
      border: var(--rule-soft) !important;
      border-radius: var(--radius-lg) !important;
      box-shadow: var(--shadow-card-hover) !important;
    }
    .dropdown-item {
      color: var(--color-ink-1) !important;
      font-size: 0.875rem;
      border-radius: var(--radius-sm) !important;
    }
    .dropdown-item:hover {
      background: var(--color-paper-1) !important;
      color: var(--color-ink-0) !important;
    }

    /* ── iCheck fix ─────────────────────────────────────── */
    .icheck-primary label { color: var(--color-ink-1) !important; }

    /* ══════════════════════════════════════════════════════
       Hallmark Tally · Dark Mode Variant
       paper-band: dark-navy · accent: cool-indigo (same)
       ═════════════════════════════════════════════════════ */
    body.dark-mode {
      --color-paper-0: oklch(14.0% 0.015 258);
      --color-paper-1: oklch(18.0% 0.018 258);
      --color-paper-2: oklch(22.0% 0.020 258);
      --color-paper-3: oklch(27.0% 0.022 258);
      --color-ink-0:   oklch(95.0% 0.005 258);
      --color-ink-1:   oklch(80.0% 0.010 258);
      --color-ink-2:   oklch(60.0% 0.015 258);
      --color-ink-3:   oklch(42.0% 0.018 258);

      /* Accent stays indigo — slightly brighter for dark bg */
      --color-accent:       oklch(62.0% 0.220 268);
      --color-accent-soft:  oklch(74.0% 0.140 268);
      --color-accent-tint:  oklch(22.0% 0.060 268);
      --color-companion:    oklch(70.0% 0.180 130);
      --color-success:      oklch(65.0% 0.150 145);
      --color-danger:       oklch(62.0% 0.200 25);
      --color-warning:      oklch(72.0% 0.180 50);

      --rule-soft:          1px solid color-mix(in oklch, var(--color-ink-0) 10%, transparent);
      --shadow-card:        0 1px 3px rgba(0,0,0,0.30), 0 4px 16px rgba(0,0,0,0.25);
      --shadow-card-hover:  0 4px 24px rgba(0,0,0,0.40);

      background: var(--color-paper-0) !important;
    }

    /* Dark mode component overrides */
    body.dark-mode .main-header.navbar {
      background: color-mix(in oklch, var(--color-paper-1) 90%, transparent) !important;
    }
    body.dark-mode .main-sidebar,
    body.dark-mode .main-sidebar::before {
      background: var(--color-paper-1) !important;
    }
    body.dark-mode .brand-link {
      background: var(--color-paper-1) !important;
    }
    body.dark-mode .brand-link:hover {
      background: var(--color-paper-2) !important;
    }
    body.dark-mode .card,
    body.dark-mode .info-box,
    body.dark-mode .small-box {
      background: var(--color-paper-1) !important;
    }
    body.dark-mode .card-header { background: transparent !important; }
    body.dark-mode .small-box-footer {
      background: var(--color-paper-2) !important;
    }
    body.dark-mode .modal-content {
      background: var(--color-paper-1) !important;
    }
    body.dark-mode .nav-pill-btn {
      background: var(--color-paper-2);
      border-color: color-mix(in oklch, var(--color-ink-0) 10%, transparent);
    }
    body.dark-mode .nav-pill-btn:hover {
      background: var(--color-accent-tint);
    }
    body.dark-mode .form-control,
    body.dark-mode .custom-select,
    body.dark-mode select.form-control {
      background: var(--color-paper-2) !important;
    }
    body.dark-mode .form-control:focus,
    body.dark-mode .custom-select:focus {
      background: var(--color-paper-2) !important;
    }
    body.dark-mode .input-group-text {
      background: var(--color-paper-2) !important;
    }
    body.dark-mode .select2-container--default .select2-selection--single,
    body.dark-mode .select2-dropdown,
    body.dark-mode .select2-search--dropdown .select2-search__field {
      background: var(--color-paper-2) !important;
    }
    body.dark-mode select option {
      background: var(--color-paper-1) !important;
    }
    body.dark-mode .main-footer {
      background: var(--color-paper-1) !important;
    }
    body.dark-mode .dropdown-menu {
      background: var(--color-paper-2) !important;
    }
    body.dark-mode .dropdown-item:hover {
      background: var(--color-paper-3) !important;
    }
    body.dark-mode .badge-success { background: oklch(20% 0.050 145) !important; color: oklch(72% 0.120 145) !important; }
    body.dark-mode .badge-danger  { background: oklch(20% 0.040 25) !important;  color: oklch(72% 0.180 25) !important; }
    body.dark-mode .badge-warning { background: oklch(20% 0.050 50) !important;  color: oklch(76% 0.160 50) !important; }
    body.dark-mode .badge-secondary { background: var(--color-paper-3) !important; color: var(--color-ink-2) !important; }
    body.dark-mode .btn-secondary, body.dark-mode .btn-default {
      background: var(--color-paper-2) !important;
    }
    body.dark-mode .btn-danger {
      background: oklch(20% 0.040 25) !important;
      color: oklch(72% 0.180 25) !important;
    }
    body.dark-mode .btn-danger:hover {
      background: var(--color-danger) !important;
      color: white !important;
    }
    body.dark-mode .btn-success {
      background: oklch(20% 0.050 145) !important;
      color: oklch(72% 0.120 145) !important;
    }
    body.dark-mode .btn-success:hover {
      background: var(--color-success) !important;
      color: white !important;
    }
    body.dark-mode .btn-warning {
      background: oklch(20% 0.050 50) !important;
      color: oklch(76% 0.160 50) !important;
    }
    body.dark-mode .btn-warning:hover {
      background: var(--color-warning) !important;
      color: white !important;
    }
    body.dark-mode .btn-info {
      background: var(--color-accent-tint) !important;
      color: var(--color-accent) !important;
    }
    body.dark-mode .cat-mini-card {
      background: var(--color-paper-1) !important;
    }
    body.dark-mode ::-webkit-scrollbar-track {
      background: var(--color-paper-0);
    }
    /* Dark mode SweetAlert */
    body.dark-mode .swal2-popup {
      background: var(--color-paper-1) !important;
      color: var(--color-ink-0) !important;
    }
    /* Dark mode Flatpickr */
    body.dark-mode .flatpickr-calendar {
      background: var(--color-paper-1);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
      border: 1px solid var(--color-paper-2);
    }
    body.dark-mode .flatpickr-calendar::before,
    body.dark-mode .flatpickr-calendar::after {
      border-bottom-color: var(--color-paper-1);
    }
    body.dark-mode .flatpickr-day,
    body.dark-mode .flatpickr-weekday,
    body.dark-mode .flatpickr-current-month .flatpickr-monthDropdown-months,
    body.dark-mode .flatpickr-current-month input.cur-year,
    body.dark-mode .flatpickr-time input,
    body.dark-mode .flatpickr-time .flatpickr-time-separator,
    body.dark-mode .flatpickr-time .flatpickr-am-pm {
      color: var(--color-ink-1);
    }
    body.dark-mode .flatpickr-day.prevMonthDay,
    body.dark-mode .flatpickr-day.nextMonthDay {
      color: var(--color-ink-3);
    }
    body.dark-mode .flatpickr-day:hover,
    body.dark-mode .flatpickr-day:focus {
      background: var(--color-paper-2);
      border-color: var(--color-paper-2);
    }
    body.dark-mode .flatpickr-day.selected {
      background: var(--color-accent);
      border-color: var(--color-accent);
      color: white;
    }
    body.dark-mode .flatpickr-months .flatpickr-prev-month svg,
    body.dark-mode .flatpickr-months .flatpickr-next-month svg {
      fill: var(--color-ink-1);
    }
    body.dark-mode .flatpickr-months .flatpickr-prev-month:hover svg,
    body.dark-mode .flatpickr-months .flatpickr-next-month:hover svg {
      fill: var(--color-accent);
    }
    body.dark-mode .flatpickr-time input:hover,
    body.dark-mode .flatpickr-time input:focus {
      background: var(--color-paper-2);
    }

    /* ══════════════════════════════════════════════════════
       Mobile & Tablet Responsive UI/UX Overhaul
       ═════════════════════════════════════════════════════ */
    
    /* Smooth Touch Scrolling for Horizontal Containers & Tables */
    .table-responsive,
    .card-body.p-0 {
      display: block;
      width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      border-radius: var(--radius-md);
      position: relative;
    }

    .table-responsive::-webkit-scrollbar,
    .card-body.p-0::-webkit-scrollbar {
      height: 6px;
    }
    .table-responsive::-webkit-scrollbar-thumb,
    .card-body.p-0::-webkit-scrollbar-thumb {
      background: color-mix(in oklch, var(--color-accent) 50%, transparent);
      border-radius: var(--radius-pill);
    }

    /* Sticky Table Headers on Scroll */
    .table-responsive table.table thead th,
    .card-body.p-0 > table.table thead th {
      position: sticky;
      top: 0;
      background: var(--color-paper-0) !important;
      z-index: 5;
      box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    body.dark-mode .table-responsive table.table thead th,
    body.dark-mode .card-body.p-0 > table.table thead th {
      background: var(--color-paper-1) !important;
    }

    /* Prevent mobile browser zoom on focus (minimum 16px font-size on mobile inputs) */
    @media (max-width: 767.98px) {
      input[type="text"],
      input[type="number"],
      input[type="search"],
      input[type="email"],
      input[type="password"],
      input[type="date"],
      select,
      textarea,
      .form-control,
      .custom-select {
        font-size: 16px !important;
      }

      /* Navbar Mobile Optimizations */
      .main-header.navbar {
        padding: 0 8px !important;
        min-height: 56px;
      }

      .main-header .navbar-nav {
        align-items: center;
      }

      .main-header .nav-link[data-widget="pushmenu"] {
        display: flex !important;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        padding: 0 !important;
        border-radius: var(--radius-pill);
      }

      .nav-pill-btn {
        height: 38px;
        padding: 6px 10px;
        font-size: 0.75rem;
      }

      /* User name hide long text on phone screens */
      .user-name-text {
        max-width: 90px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
      }

      /* Content Header Mobile Spacing */
      .content-header {
        padding: 12px 14px !important;
      }
      .content-header h1 {
        font-size: 1.25rem !important;
        margin-bottom: 6px;
      }

      /* Sticky First Column & Sticky Action Column on Mobile Tables */
      .table-responsive table.table th:first-child,
      .table-responsive table.table td:first-child,
      .card-body.p-0 > table.table th:first-child,
      .card-body.p-0 > table.table td:first-child {
        position: sticky;
        left: 0;
        background: var(--color-paper-0) !important;
        z-index: 4;
        box-shadow: 3px 0 6px -2px rgba(0,0,0,0.1);
      }
      body.dark-mode .table-responsive table.table th:first-child,
      body.dark-mode .table-responsive table.table td:first-child,
      body.dark-mode .card-body.p-0 > table.table th:first-child,
      body.dark-mode .card-body.p-0 > table.table td:first-child {
        background: var(--color-paper-1) !important;
      }

      .table-responsive table.table th:last-child,
      .table-responsive table.table td:last-child,
      .card-body.p-0 > table.table th:last-child,
      .card-body.p-0 > table.table td:last-child {
        position: sticky;
        right: 0;
        background: var(--color-paper-0) !important;
        z-index: 4;
        box-shadow: -3px 0 6px -2px rgba(0,0,0,0.1);
      }
      body.dark-mode .table-responsive table.table th:last-child,
      body.dark-mode .table-responsive table.table td:last-child,
      body.dark-mode .card-body.p-0 > table.table th:last-child,
      body.dark-mode .card-body.p-0 > table.table td:last-child {
        background: var(--color-paper-1) !important;
      }

      /* Card Headers & Toolbars Stacking */
      .card-header {
        display: flex;
        flex-direction: column;
        align-items: flex-start !important;
        gap: 12px;
        padding: 14px !important;
      }
      .card-header .card-tools,
      .card-header .card-title {
        width: 100%;
        margin-left: 0 !important;
        float: none !important;
      }
      .card-header .card-tools {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
      }
      .card-header .card-tools .input-group {
        width: 100% !important;
      }

      .card-body {
        padding: 14px !important;
      }

      /* Filter forms & flex-wrap containers on mobile across all pages */
      form .d-flex,
      .row.mb-3 form .d-flex {
        flex-wrap: wrap !important;
        gap: 8px !important;
      }
      form .d-flex > input,
      form .d-flex > select,
      form .d-flex > .select2-container,
      .d-flex.flex-wrap > input,
      .d-flex.flex-wrap > select,
      .d-flex.flex-wrap > .select2-container {
        width: 100% !important;
        max-width: 100% !important;
        flex: 1 1 100% !important;
      }
      .select2-container {
        width: 100% !important;
      }

      /* Action buttons row stacking on mobile */
      .row.mb-3 .col-12.text-right,
      .row.mb-3 .col-sm-6.text-right,
      .row.mb-3 .col-md-4.text-md-right,
      .row.mb-3 .col-12.d-flex {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
        justify-content: stretch !important;
        text-align: left !important;
        margin-top: 8px !important;
      }
      .row.mb-3 .col-12.text-right > .btn,
      .row.mb-3 .col-sm-6.text-right > .btn,
      .row.mb-3 .col-md-4.text-md-right > .btn,
      .row.mb-3 .col-12.d-flex > .btn,
      .row.mb-3 .col-12.d-flex > a {
        flex: 1 1 auto !important;
        margin-right: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-height: 40px !important;
      }

      /* Small stat box compact mobile styling */
      .small-box > .inner {
        padding: 14px !important;
      }
      .small-box > .inner h3 {
        font-size: 1.75rem !important;
      }
      .small-box > .inner p {
        font-size: 0.75rem !important;
      }
      .small-box > .icon > i {
        font-size: 48px !important;
        top: 10px;
        right: 10px;
      }

      .d-flex.flex-wrap > input,
      .d-flex.flex-wrap > select,
      .d-flex.flex-wrap > .select2-container {
        width: 100% !important;
        max-width: 100% !important;
        flex: 1 1 100% !important;
      }
      .d-flex.flex-wrap {
        gap: 8px !important;
      }
      .select2-container {
        width: 100% !important;
      }
      
      /* Card Body Table Auto-Scroll */
      .card-body.p-0 {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }

      /* Filter action row stacking */
      .col-12.text-right,
      .col-12.d-flex.justify-content-end {
        text-align: left !important;
        justify-content: flex-start !important;
        flex-wrap: wrap;
        gap: 8px !important;
      }
      .col-12.text-right > .btn,
      .col-12.d-flex.justify-content-end > .btn,
      .col-12.d-flex.justify-content-end > a {
        flex: 1 1 auto;
        margin-right: 0 !important;
        text-align: center;
      }

      /* Modals Responsive */
      .modal-dialog {
        margin: 12px auto !important;
        max-width: 95% !important;
      }
      .modal-content {
        border-radius: var(--radius-lg) !important;
      }
      .modal-header, .modal-footer {
        padding: 14px !important;
      }

      /* Mobile Data Table Cell Compact Styling */
      .table thead th {
        padding: 8px 10px;
        font-size: 0.65rem;
      }
      .table tbody td {
        padding: 8px 10px;
        font-size: 0.8125rem;
      }

      /* Lightweight GPU & Paint performance optimization for Mobile screens */
      *, *::before, *::after {
        text-rendering: optimizeSpeed;
      }

      /* Disable heavy realtime backdrop blur on mobile topbar for 60fps scrolling */
      .main-header.navbar {
        -webkit-backdrop-filter: none !important;
        backdrop-filter: none !important;
        background: var(--color-paper-0) !important;
      }
      body.dark-mode .main-header.navbar {
        background: var(--color-paper-1) !important;
      }

      /* Disable hover transitions on mobile to prevent paint lags */
      .card, .info-box, .small-box, .cat-mini-card, .btn {
        -webkit-backdrop-filter: none !important;
        backdrop-filter: none !important;
        transition: none !important;
      }

      /* Enable GPU Hardware Acceleration for mobile table & container scroll */
      .table-responsive,
      .card-body.p-0,
      .content-wrapper {
        transform: translateZ(0);
        will-change: scroll-position;
      }

      /* Simplify card shadows on mobile screens */
      .card, .small-box, .cat-mini-card {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
      }

      /* Mobile Drawer Backdrop overlay */
      .sidebar-open .wrapper::before {
        content: "";
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 1037;
      }
    }

    @media (max-width: 575.98px) {
      .nav-pill-btn span {
        display: inline-block;
      }
      .user-name-text {
        display: none !important;
      }
      .user-info-pill {
        padding: 0 6px !important;
      }

      /* Mini category grid adjustments */
      .cat-mini-card {
        padding: 12px 8px !important;
      }
      .cat-mini-card h3, .cat-mini-card .cat-count {
        font-size: 1.25rem !important;
      }
      .cat-mini-card span, .cat-mini-card .cat-label {
        font-size: 0.7rem !important;
      }
    }
  </style>
  @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed {{ session('theme', 'dark') }}-mode" style="min-height: 100vh;">
<!-- Site wrapper -->
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-light">
    <!-- Left: toggle -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button" style="color: var(--color-ink-1);">
          <i class="fas fa-bars" style="font-size: 1rem;"></i>
        </a>
      </li>
    </ul>

    <!-- Right: controls -->
    <ul class="navbar-nav ml-auto" style="gap: 8px; padding-right: 12px; display: flex; align-items: center; flex-direction: row;">

      @can('action_ai_assistant')
      <!-- Tanya ITAM AI Button -->
      <li class="nav-item">
        <button type="button" class="nav-pill-btn" data-toggle="modal" data-target="#aiSearchModal" style="border: 1px solid var(--color-accent-soft); background: var(--color-accent-tint); color: var(--color-accent) !important; font-weight: 600;">
          <i class="fas fa-magic" style="color: var(--color-accent);"></i>
          <span>{{ __('messages.tanya_itam_ai') }}</span>
        </button>
      </li>
      @endcan

      <!-- Language Dropdown -->
      <li class="nav-item dropdown">
        <a class="nav-pill-btn dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer; text-decoration: none;">
          <i class="fas fa-globe" style="font-size: 0.8rem;"></i>
          <span>{{ App::getLocale() == 'id' ? 'ID' : 'EN' }}</span>
          <i class="fas fa-chevron-down" style="font-size: 0.6rem; opacity: 0.7;"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right p-1" style="border-radius: 12px; min-width: 160px; background: #090d16; border: 1px solid rgba(255, 255, 255, 0.12); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5); overflow: hidden;">
          <a class="dropdown-item d-flex align-items-center py-2 px-3" href="{{ route('lang.switch', 'id') }}" style="gap: 8px; font-size: 0.85rem; border-radius: 8px; transition: all 0.2s; background: {{ App::getLocale() == 'id' ? 'var(--color-accent-tint)' : 'transparent' }}; color: {{ App::getLocale() == 'id' ? 'var(--color-accent) !important' : '#e2e8f0' }};">
            <i class="fas fa-check" style="font-size: 0.75rem; visibility: {{ App::getLocale() == 'id' ? 'visible' : 'hidden' }};"></i>
            <span>Bahasa Indonesia</span>
          </a>
          <div class="dropdown-divider my-1" style="border-top: 1px solid rgba(255, 255, 255, 0.08);"></div>
          <a class="dropdown-item d-flex align-items-center py-2 px-3" href="{{ route('lang.switch', 'en') }}" style="gap: 8px; font-size: 0.85rem; border-radius: 8px; transition: all 0.2s; background: {{ App::getLocale() == 'en' ? 'var(--color-accent-tint)' : 'transparent' }}; color: {{ App::getLocale() == 'en' ? 'var(--color-accent) !important' : '#e2e8f0' }};">
            <i class="fas fa-check" style="font-size: 0.75rem; visibility: {{ App::getLocale() == 'en' ? 'visible' : 'hidden' }};"></i>
            <span>English</span>
          </a>
        </div>
      </li>

      <!-- Theme Switch -->
      <li class="nav-item">
        <a class="nav-pill-btn"
           href="{{ session('theme', 'dark') == 'dark' ? route('theme.switch', 'light') : route('theme.switch', 'dark') }}"
           title="Toggle Theme">
          <i class="fas {{ session('theme', 'dark') == 'dark' ? 'fa-sun' : 'fa-moon' }}" style="font-size: 0.8rem;"></i>
        </a>
      </li>

      @auth
      <!-- User Profile Dropdown Menu -->
      <li class="nav-item dropdown user-menu">
        <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-toggle="dropdown" aria-expanded="false" style="gap: 8px; padding: 4px 10px; border-radius: 20px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.08);">
          <div class="user-avatar-badge" style="width: 28px; height: 28px; border-radius: 50%; background: #4f46e5; display: flex; align-items: center; justify-content: center; color: #ffffff; font-weight: 700; font-size: 0.8rem; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);">
            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
          </div>
          <span class="user-name-text" style="font-weight: 600; font-size: 0.875rem; color: var(--color-ink-1);">
            {{ Auth::user()->name ?? 'User' }}
          </span>
          <i class="fas fa-chevron-down ml-1" style="font-size: 0.7rem; color: var(--color-ink-3);"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-0" style="border-radius: 16px; overflow: hidden; background: #090d16; border: 1px solid rgba(255, 255, 255, 0.12); box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.85); min-width: 250px;">
          <!-- User Header Info -->
          <div class="p-3 text-center" style="background: rgba(79, 70, 229, 0.08); border-bottom: 1px solid rgba(255, 255, 255, 0.04);">
            <div class="mx-auto mb-2" style="width: 46px; height: 46px; border-radius: 50%; background: #4f46e5; display: flex; align-items: center; justify-content: center; color: #ffffff; font-weight: 700; font-size: 1.2rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);">
              {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
            </div>
            <div style="font-weight: 700; font-size: 0.92rem; color: #f8fafc;">{{ Auth::user()->name ?? 'User' }}</div>
            <div style="font-size: 0.75rem; color: #94a3b8; font-family: var(--font-mono);">{{ Auth::user()->email ?? '' }}</div>
            <div class="mt-2">
              <span class="badge badge-pill" style="background: rgba(99, 102, 241, 0.2); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3); font-size: 0.7rem; font-weight: 600; padding: 4px 10px;">
                <i class="fas fa-user-shield mr-1"></i> {{ Auth::user()->getRoleNames()->first() ?? 'User' }}
              </span>
            </div>
          </div>

          <!-- Menu Items -->
          <div class="p-2">
            <a href="#" class="dropdown-item d-flex align-items-center py-2 px-3 rounded-lg" data-toggle="modal" data-target="#changePasswordModal" style="gap: 10px; color: #e2e8f0; font-size: 0.85rem; border-radius: 8px; transition: all 0.2s;">
              <i class="fas fa-key text-warning" style="width: 18px;"></i>
              <span>Ganti Password</span>
            </a>

            <div class="dropdown-divider my-1" style="border-top: 1px solid rgba(255, 255, 255, 0.08);"></div>

            <a href="{{ route('logout') }}" class="dropdown-item d-flex align-items-center py-2 px-3 rounded-lg text-danger" style="gap: 10px; font-size: 0.85rem; border-radius: 8px; transition: all 0.2s;">
              <i class="fas fa-sign-out-alt" style="width: 18px;"></i>
              <span>Logout</span>
            </a>
          </div>
        </div>
      </li>


      @else
      <li class="nav-item">
        <a href="{{ route('login') }}" class="nav-pill-btn">
          <i class="fas fa-sign-in-alt" style="font-size: 0.8rem;"></i>
          <span>{{ __('messages.login') ?? 'Login' }}</span>
        </a>
      </li>
      @endauth
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar -->
  <aside class="main-sidebar sidebar-light-primary elevation-0">
    <!-- Brand -->
    <a href="{{ route('dashboard') }}" class="brand-link" style="text-decoration: none;">
      <img src="{{ vasset('logo.png') }}" alt="Logo" loading="lazy" decoding="async"
           style="max-height: 30px; max-width: 52px; object-fit: contain; margin-right: 12px;">
      <span class="brand-text">{{ $app_name ?? 'ITAM Enterprise' }}</span>
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
              @endphp
              @foreach($fixedCategories as $catName)
              <li class="nav-item">
                  <a href="{{ route('assets.index', ['category' => $catName]) }}" class="nav-link {{ request()->routeIs('assets.index') && request('category') == $catName ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ $catName }}</p>
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
          @endcanany


        </ul>
      </nav>
    </div>
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper" style="background: transparent;">
    <!-- Content Header -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">@yield('title')</h1>
          </div>
        </div>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid pb-2">
        @yield('content')
      </div>
    </section>

    <!-- Main Footer -->
    <footer class="main-footer">
      <!-- <div class="float-right d-none d-sm-block">
        <span style="font-family: var(--font-mono); font-size: 0.75rem; color: var(--color-ink-3);">v1.0.0</span>
      </div> -->
      <strong style="color: var(--color-ink-1);">
        &copy; {{ date('Y') }}
        <span style="color: var(--color-accent);">{{ $app_name ?? 'ITAM Enterprise' }}</span>
        — {{ $company_name ?? 'ITAM Enterprise' }}
      </strong>
      <span style="color: var(--color-ink-3); margin-left: 4px;">Asoyy Dev.</span>
    </footer>
  </div>
  <!-- /.content-wrapper -->
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
    var message = $(this).data('confirm-message') || "{{ __('messages.confirm_delete') ?? 'Are you sure you want to delete this data?' }}";
    var isDark = $('body').hasClass('dark-mode');
    Swal.fire({
      title: "{{ __('messages.deleting_data') ?? 'Deleting Data' }}",
      text: message,
      icon: 'warning',
      iconColor: 'oklch(58.0% 0.200 25)',
      showCancelButton: true,
      confirmButtonColor: 'oklch(58.0% 0.200 25)', // Red/Danger
      cancelButtonColor: isDark ? 'oklch(35.0% 0.025 258)' : 'oklch(89.0% 0.020 258)', // Muted
      confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> ' + ("{{ __('messages.yes_delete') ?? 'Yes, Delete' }}"),
      cancelButtonText: "{{ __('messages.cancel') ?? 'Cancel' }}",
      background: isDark ? 'oklch(18.0% 0.018 258)' : 'oklch(98.4% 0.005 258)',
      color: isDark ? 'oklch(95.0% 0.005 258)' : 'oklch(18.0% 0.030 258)',
      reverseButtons: true,
      customClass: { popup: 'rounded-xl shadow-lg border border-secondary', confirmButton: 'rounded-pill', cancelButton: 'rounded-pill' }
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
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...');

    $.ajax({
      url: "{{ route('ai.query-search') }}",
      type: "POST",
      data: {
        _token: "{{ csrf_token() }}",
        prompt: prompt
      },
      success: function(response) {
        btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Tanya AI');
        if (response.success && response.result) {
          const res = response.result;
          $('#ai-query-summary-title').text(res.summary);

          let containerHtml = '';
          if (res.answer) {
            containerHtml += '<div class="p-3 rounded mb-3 theme-text" style="background: var(--color-paper-1); font-size: 0.875rem; border-left: 3px solid var(--color-accent); line-height: 1.5; white-space: pre-wrap;">' + res.answer + '</div>';
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
                  <span class="badge ${item.badge} px-3 py-2">${item.badge_text}</span>
                </a>
              `;
            });
            containerHtml += '</div>';
          }

          $('#ai-query-items-container').html(containerHtml);
          $('#ai-query-output-box').slideDown();
        }
      },
      error: function(err) {
        btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Tanya AI');
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

<!-- AI Assistant Natural Language Search Modal -->
<div class="modal fade" id="aiSearchModal" tabindex="-1" role="dialog" aria-labelledby="aiSearchModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content" style="border-radius: var(--radius-xl) !important; background: var(--color-paper-0) !important; border: 1px solid var(--color-accent-soft) !important;">
      <div class="modal-header d-flex justify-content-between align-items-center" style="border-bottom: var(--rule-soft) !important;">
        <h5 class="modal-title font-weight-bold" id="aiSearchModalLabel" style="color: var(--color-accent); font-size: 1.1rem;">
          <i class="fas fa-robot mr-2"></i> {{ __('messages.ai_modal_title') }}
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline: none;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4">
        <!-- Input area -->
        <div class="form-group mb-3">
          <label class="theme-text mb-2">{{ __('messages.ai_input_label') }}</label>
          <div class="input-group">
            <input type="text" id="ai-prompt-input" class="form-control theme-input py-2" placeholder="{{ __('messages.ai_placeholder') }}" style="font-size: 0.95rem; border-radius: var(--radius-md) 0 0 var(--radius-md) !important;">
            <div class="input-group-append">
              <button class="btn btn-primary px-4" id="btn-submit-ai-query" type="button">
                <i class="fas fa-paper-plane mr-1"></i> {{ __('messages.ask_ai_btn') }}
              </button>
            </div>
          </div>
        </div>

        <!-- Quick prompt chips -->
        <div class="mb-3 d-flex flex-wrap" style="gap: 6px;">
          <small class="text-muted w-100 mb-1">{{ __('messages.quick_questions') }}</small>
          <button type="button" class="btn btn-xs btn-outline-info ai-chip-btn" data-prompt="{{ __('messages.prompt_expired_laptops') }}">{{ __('messages.chip_expired_laptops') }}</button>
          <button type="button" class="btn btn-xs btn-outline-info ai-chip-btn" data-prompt="{{ __('messages.prompt_available_ips') }}">{{ __('messages.chip_available_ips') }}</button>
          <button type="button" class="btn btn-xs btn-outline-info ai-chip-btn" data-prompt="{{ __('messages.prompt_open_tickets') }}">{{ __('messages.chip_open_tickets') }}</button>
          <button type="button" class="btn btn-xs btn-outline-info ai-chip-btn" data-prompt="{{ __('messages.prompt_printer_devices') }}">{{ __('messages.chip_printer_devices') }}</button>
        </div>

        <!-- AI Output Area -->
        <div id="ai-query-output-box" style="display: none;">
          <hr style="border-top: var(--rule-soft);">
          <div class="p-3 rounded mb-1" style="background: color-mix(in oklch, var(--color-accent-tint) 30%, var(--color-paper-0)); border: 1px solid color-mix(in oklch, var(--color-accent) 20%, transparent);">
            <div class="d-flex align-items-center mb-2" style="gap: 8px;">
              <i class="fas fa-sparkles text-info" style="font-size: 1.1rem;"></i>
              <strong id="ai-query-summary-title" class="theme-text" style="font-size: 0.9rem;"></strong>
            </div>
            <div id="ai-query-items-container" class="mt-3" style="max-height: 340px; overflow-y: auto; padding-right: 6px;"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  #ai-query-items-container::-webkit-scrollbar {
    width: 6px;
  }
  #ai-query-items-container::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 4px;
  }
  #ai-query-items-container::-webkit-scrollbar-thumb {
    background: rgba(0, 240, 255, 0.3);
    border-radius: 4px;
  }
  #ai-query-items-container::-webkit-scrollbar-thumb:hover {
    background: #00f0ff;
  }
</style>

<!-- PWA Mobile Install Banner -->
<div id="pwa-install-banner" class="shadow-lg p-3 rounded-lg" style="display: none; position: fixed; bottom: 20px; left: 20px; right: 20px; z-index: 99999; background: #0f172a; border: 1px solid #38bdf8; color: #f8fafc; border-radius: 14px;">
  <div class="d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center" style="gap: 12px;">
      <img src="{{ vasset('logo.png') }}" alt="ITAM" style="width: 42px; height: 42px; border-radius: 10px; object-fit: cover;">
      <div>
        <div class="font-weight-bold" style="font-size: 0.92rem; color: #ffffff;">Install Aplikasi ITAM Enterprise</div>
        <small class="text-muted" style="font-size: 0.75rem; color: #94a3b8 !important;">Akses cepat & pengalaman aplikasi native di HP Anda</small>
      </div>
    </div>
    <div class="d-flex align-items-center" style="gap: 8px;">
      <button id="pwa-install-btn" class="btn btn-sm btn-info font-weight-bold px-3 py-1" style="border-radius: 20px;"><i class="fas fa-download mr-1"></i> Install</button>
      <button id="pwa-close-btn" class="btn btn-sm btn-outline-secondary px-2 py-1" style="border-radius: 50%; width: 28px; height: 28px; padding: 0; display: flex; align-items: center; justify-content: center;"><i class="fas fa-times text-xs"></i></button>
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
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(255, 255, 255, 0.12); color: #f8fafc; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.85);">
      <div class="modal-header" style="border-bottom: 1px solid rgba(255, 255, 255, 0.08); padding: 18px 24px;">
        <h5 class="modal-title font-weight-bold d-flex align-items-center" id="changePasswordModalLabel" style="gap: 10px; font-size: 1.1rem; color: #ffffff;">
          <i class="fas fa-key text-warning"></i> Ganti Password
        </h5>
        <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="{{ route('password.update') }}">
        @csrf
        @method('put')
        <div class="modal-body" style="padding: 24px;">
          <!-- Password Saat Ini -->
          <div class="form-group mb-3">
            <label for="current_password" style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1;">Password Saat Ini</label>
            <input type="password" name="current_password" id="current_password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" placeholder="Masukkan password saat ini" required style="background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255, 255, 255, 0.12); color: #fff; border-radius: 12px; padding: 12px 14px;">
            @error('current_password', 'updatePassword')
              <small class="text-danger mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</small>
            @enderror
          </div>

          <!-- Password Baru -->
          <div class="form-group mb-3">
            <label for="password" style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1;">Password Baru</label>
            <input type="password" name="password" id="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" placeholder="Masukkan password baru (min 8 karakter)" required style="background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255, 255, 255, 0.12); color: #fff; border-radius: 12px; padding: 12px 14px;">
            @error('password', 'updatePassword')
              <small class="text-danger mt-1 d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</small>
            @enderror
          </div>

          <!-- Konfirmasi Password Baru -->
          <div class="form-group mb-0">
            <label for="password_confirmation" style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1;">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi password baru" required style="background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255, 255, 255, 0.12); color: #fff; border-radius: 12px; padding: 12px 14px;">
          </div>
        </div>
        <div class="modal-footer" style="border-top: 1px solid rgba(255, 255, 255, 0.08); padding: 14px 24px;">
          <button type="button" class="btn btn-secondary px-4" data-dismiss="modal" style="border-radius: 10px;">Batal</button>
          <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px; font-weight: 600;">
            <i class="fas fa-save mr-1"></i> Simpan Password
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
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
          timer: 3000,
          showConfirmButton: false,
          background: '#0f172a',
          color: '#f8fafc'
        });
      }
    });
  @endif
</script>

@stack('scripts')
</body>
</html>
