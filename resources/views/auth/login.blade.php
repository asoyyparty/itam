<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>ITAM Enterprise | Sign In</title>

  <!-- Favicon -->
  <link rel="icon" href="{{ vasset('logo.png') }}" type="image/png">

  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  
  <style>
    :root {
      --color-bg: #0f172a;
      --color-card-bg: rgba(30, 41, 59, 0.4);
      --color-card-border: rgba(255, 255, 255, 0.1);
      --color-text-main: #f8fafc;
      --color-text-muted: #94a3b8;
      --color-primary: #6366f1;
      --color-primary-glow: rgba(99, 102, 241, 0.5);
      --color-primary-hover: #4f46e5;
      --color-input-bg: rgba(15, 23, 42, 0.6);
      --color-input-border: rgba(255, 255, 255, 0.08);
      
      --font-main: 'Inter', sans-serif;
      --radius-lg: 24px;
      --radius-md: 12px;
      --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: var(--font-main);
      color: var(--color-text-main);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #090d16;
      position: relative;
      overflow: hidden;
    }

    /* Glassmorphism Card */
    .login-container {
      width: 100%;
      max-width: 440px;
      padding: 20px;
      z-index: 10;
    }

    .glass-card {
      background: #0f172a;
      border: 1px solid #1e293b;
      border-radius: var(--radius-lg);
      padding: 44px 36px;
      box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.6);
      text-align: center;
    }

    /* Brand Header inside card */
    .brand-header {
      margin-bottom: 32px;
    }
    .brand-header img {
      height: 52px;
      margin-bottom: 16px;
      filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2));
    }
    .brand-header h1 {
      font-size: 1.5rem;
      font-weight: 700;
      letter-spacing: -0.02em;
      margin-bottom: 4px;
    }
    .brand-header h1 span {
      color: var(--color-primary);
    }
    .brand-header p {
      font-size: 0.875rem;
      color: var(--color-text-muted);
      font-weight: 500;
    }

    /* Form Styles */
    .form-group {
      text-align: left;
      margin-bottom: 20px;
      position: relative;
    }
    .form-group label {
      display: block;
      font-size: 0.875rem;
      font-weight: 500;
      margin-bottom: 8px;
      color: #e2e8f0;
    }

    .input-wrapper {
      position: relative;
    }
    .input-icon {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--color-text-muted);
      transition: var(--transition);
    }

    .form-control {
      width: 100%;
      background: var(--color-input-bg);
      border: 1px solid var(--color-input-border);
      border-radius: var(--radius-md);
      padding: 12px 16px 12px 44px;
      color: var(--color-text-main);
      font-family: var(--font-main);
      font-size: 0.95rem;
      transition: var(--transition);
      outline: none;
    }
    
    .form-control::placeholder {
      color: #475569;
    }

    .form-control:focus {
      border-color: var(--color-primary);
      background: rgba(15, 23, 42, 0.8);
      box-shadow: 0 0 0 4px var(--color-primary-glow);
    }
    .form-control:focus + .input-icon,
    .form-control:focus ~ .input-icon {
      color: var(--color-primary);
    }
    
    .toggle-password {
      position: absolute;
      right: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--color-text-muted);
      cursor: pointer;
      transition: var(--transition);
      z-index: 10;
    }
    .toggle-password:hover {
      color: var(--color-primary);
    }

    /* Invalid State */
    .form-control.is-invalid { border-color: #ef4444; }
    .invalid-feedback {
      display: block;
      color: #f87171;
      font-size: 0.8rem;
      margin-top: 6px;
      text-align: left;
    }

    /* Actions Row */
    .actions-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 24px;
      margin-bottom: 32px;
    }
    .remember-me {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.875rem;
      color: var(--color-text-muted);
      cursor: pointer;
    }
    .remember-me input {
      accent-color: var(--color-primary);
      width: 16px;
      height: 16px;
      cursor: pointer;
    }
    
    .forgot-link {
      font-size: 0.875rem;
      color: var(--color-primary);
      text-decoration: none;
      font-weight: 500;
      transition: var(--transition);
    }
    .forgot-link:hover {
      color: #818cf8;
    }

    /* Submit Button */
    .btn-submit {
      width: 100%;
      background: var(--color-primary);
      color: white;
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: var(--radius-md);
      padding: 14px;
      font-size: 1rem;
      font-weight: 600;
      font-family: var(--font-main);
      cursor: pointer;
      transition: var(--transition);
      box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
    }
    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
    }
    .btn-submit:active {
      transform: translateY(0);
    }

    /* Alerts */
    .alert {
      background: rgba(16, 185, 129, 0.1);
      border: 1px solid rgba(16, 185, 129, 0.2);
      color: #34d399;
      padding: 12px;
      border-radius: var(--radius-md);
      font-size: 0.875rem;
      margin-bottom: 24px;
      text-align: left;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      touch-action: manipulation;
      -webkit-text-size-adjust: 100%;
      -webkit-tap-highlight-color: transparent;
    }

    /* ══════════════════════════════════════════════════════
       Mobile Responsiveness & Touch Optimization
       ═════════════════════════════════════════════════════ */
    @media (max-width: 575.98px) {
      html, body {
        height: 100dvh !important;
        min-height: 100dvh !important;
        overflow-x: hidden !important;
      }

      body {
        padding: 12px 14px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        touch-action: manipulation !important;
      }

      body::before, body::after {
        width: 280px;
        height: 280px;
        filter: blur(40px) !important;
        animation: none !important;
        opacity: 0.2;
      }

      .login-container {
        padding: 0 !important;
        width: 100% !important;
        max-width: 390px !important;
        margin: 0 auto !important;
      }

      .glass-card {
        padding: 28px 20px !important;
        border-radius: 24px !important;
        backdrop-filter: blur(24px) !important;
        -webkit-backdrop-filter: blur(24px) !important;
        background: rgba(15, 23, 42, 0.88) !important;
        border: 1px solid rgba(255, 255, 255, 0.14) !important;
        box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.75) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease !important;
      }

      .brand-header {
        margin-bottom: 20px !important;
      }

      .brand-header img {
        height: 42px !important;
        margin-bottom: 10px !important;
      }

      .brand-header h1 {
        font-size: 1.3rem !important;
      }

      .brand-header p {
        font-size: 0.8rem !important;
      }

      .form-group {
        margin-bottom: 16px !important;
      }

      .form-group label {
        font-size: 0.8125rem !important;
        margin-bottom: 6px !important;
      }

      /* Mobile Input Sizing (Min 16px to prevent iOS Safari Auto-Zoom) */
      .form-control {
        font-size: 16px !important;
        padding: 12px 14px 12px 42px !important;
        height: 48px !important;
        border-radius: 14px !important;
        background: rgba(15, 23, 42, 0.75) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
      }

      .form-control:focus {
        background: rgba(15, 23, 42, 0.95) !important;
        border-color: #818cf8 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.35) !important;
      }

      .input-icon {
        left: 14px !important;
        font-size: 0.95rem !important;
      }

      .toggle-password {
        right: 12px !important;
        padding: 6px !important;
        font-size: 0.95rem !important;
      }

      /* Touch-friendly Actions Row */
      .actions-row {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-top: 16px !important;
        margin-bottom: 20px !important;
        font-size: 0.8125rem !important;
      }

      .remember-me {
        font-size: 0.8125rem;
        gap: 8px;
      }

      .remember-me input {
        width: 18px;
        height: 18px;
      }

      .forgot-link {
        font-size: 0.8125rem;
        white-space: nowrap;
      }

      /* Touch-friendly Submit Button with Tactile Press */
      .btn-submit {
        height: 48px !important;
        font-size: 0.95rem !important;
        border-radius: 14px !important;
        background: var(--color-primary) !important;
        box-shadow: 0 6px 18px rgba(99, 102, 241, 0.4) !important;
        transition: all 0.15s ease-in-out !important;
      }

      .btn-submit:active {
        transform: scale(0.96) !important;
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.4) !important;
        opacity: 0.9;
      }
    }

    @media (max-width: 380px) {
      .glass-card {
        padding: 24px 16px !important;
      }
      .actions-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
      }
    }
  </style>
</head>
<body>

<div class="login-container">
  <div class="glass-card">
    
    <!-- Brand Info Inside Card -->
    <div class="brand-header">
      <img src="{{ vasset('logo.png') }}" alt="ITAM Logo" loading="lazy" decoding="async">
      <h1>ITAM <span>Enterprise</span></h1>
      <p>Sign in to your account</p>
    </div>

    @if (session('status'))
      <div class="alert">
        <i class="fas fa-check-circle mr-2"></i> {{ session('status') }}
      </div>
    @endif
    @if (session('warning'))
      <div class="alert" style="background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #fbbf24;">
        <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('warning') }}
      </div>
    @endif
    @if (session('error'))
      <div class="alert" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171;">
        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
      </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf

      <!-- Username -->
      <div class="form-group">
        <label for="username">Username</label>
        <div class="input-wrapper">
          <i class="fas fa-user input-icon"></i>
          <input
            id="username"
            type="text"
            class="form-control @error('username') is-invalid @enderror"
            name="username"
            value="{{ old('username') }}"
            placeholder="Enter your username"
            required
            autofocus
            autocomplete="username">
        </div>
        @error('username')
          <span class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
        @enderror
      </div>

      <!-- Password -->
      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-wrapper">
          <i class="fas fa-lock input-icon"></i>
          <input
            id="password"
            type="password"
            class="form-control @error('password') is-invalid @enderror"
            name="password"
            placeholder="Enter your password"
            required
            autocomplete="current-password">
          <i class="fas fa-eye toggle-password" id="togglePassword" title="Show/Hide Password"></i>
        </div>
        @error('password')
          <span class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
        @enderror
      </div>

      <!-- Actions -->
      <div class="actions-row">
        <label class="remember-me">
          <input type="checkbox" name="remember" id="remember_me" {{ old('remember') ? 'checked' : '' }}>
          Remember me
        </label>
        
        @if (Route::has('password.request'))
          <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
        @endif
      </div>

      <button type="submit" class="btn-submit">
        Sign In <i class="fas fa-arrow-right"></i>
      </button>

    </form>

  </div>
</div>

<script>
  document.getElementById('togglePassword').addEventListener('click', function (e) {
    const password = document.getElementById('password');
    if (password.type === 'password') {
      password.type = 'text';
      this.classList.remove('fa-eye');
      this.classList.add('fa-eye-slash');
    } else {
      password.type = 'password';
      this.classList.remove('fa-eye-slash');
      this.classList.add('fa-eye');
    }
  });

  // Client-side username auto-remember for smooth UX
  document.addEventListener('DOMContentLoaded', function () {
    const usernameInput = document.getElementById('username');
    const rememberCheckbox = document.getElementById('remember_me');

    const rememberedUsername = localStorage.getItem('itam_remembered_username');
    if (rememberedUsername && !usernameInput.value) {
      usernameInput.value = rememberedUsername;
      rememberCheckbox.checked = true;
    }

    const form = document.querySelector('form');
    if (form) {
      form.addEventListener('submit', function () {
        if (rememberCheckbox.checked) {
          localStorage.setItem('itam_remembered_username', usernameInput.value);
        } else {
          localStorage.removeItem('itam_remembered_username');
        }
      });
    }
  });

  // Automatically reload page if idle for > 15 minutes or tab returns from background/sleep to ensure fresh CSRF token
  let lastActiveTime = Date.now();
  function checkSessionFreshness() {
    if (Date.now() - lastActiveTime > 15 * 60 * 1000) {
      window.location.reload();
    }
    lastActiveTime = Date.now();
  }

  window.addEventListener('focus', checkSessionFreshness);
  document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
      checkSessionFreshness();
    }
  });

  // Automatically reload page if restored from browser back-forward cache to get fresh CSRF token
  window.addEventListener('pageshow', function (event) {
    if (event.persisted) {
      window.location.reload();
    }
  });
</script>
</body>
</html>
