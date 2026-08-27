@extends('layouts.admin')

@section('title', __('messages.system_settings'))

@section('content')
<style>
    /* Premium Glassmorphic Settings Nav Styling */
    .settings-card {
        border-radius: 16px;
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        overflow: hidden;
    }

    .settings-nav {
        padding: 8px;
    }

    .settings-nav .nav-link {
        display: flex;
        align-items: center;
        padding: 14px 16px;
        border-radius: 12px;
        color: #94a3b8;
        border: 1px solid transparent;
        margin-bottom: 8px;
        transition: all 0.25s ease-in-out;
        position: relative;
    }

    .settings-nav .nav-link:hover {
        background: rgba(255, 255, 255, 0.04);
        color: #f8fafc;
        transform: translateX(3px);
    }

    .settings-nav .nav-link.active {
        background: var(--active-bg, rgba(59, 130, 246, 0.12)) !important;
        border: 1px solid var(--active-border, rgba(59, 130, 246, 0.3)) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 15px var(--active-shadow, rgba(59, 130, 246, 0.15));
    }

    .settings-nav .nav-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 15%;
        height: 70%;
        width: 4px;
        border-radius: 0 4px 4px 0;
        background: var(--active-color, #3b82f6);
        box-shadow: 0 0 10px var(--active-color, #3b82f6);
    }

    .nav-icon-box {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        margin-right: 12px;
        background: var(--icon-bg, rgba(148, 163, 184, 0.1));
        color: var(--icon-color, #94a3b8);
        border: 1px solid var(--icon-border, rgba(148, 163, 184, 0.2));
        transition: all 0.25s ease;
    }

    .settings-nav .nav-link.active .nav-icon-box {
        background: var(--icon-active-bg, rgba(59, 130, 246, 0.2));
        color: var(--icon-active-color, #60a5fa);
        border-color: var(--icon-active-border, rgba(59, 130, 246, 0.4));
        transform: scale(1.05);
    }

    .nav-text-box {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        overflow: hidden;
    }

    .nav-title {
        font-weight: 600;
        font-size: 0.92rem;
        line-height: 1.2;
    }

    .nav-subtitle {
        font-size: 0.72rem;
        color: #64748b;
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .settings-nav .nav-link.active .nav-subtitle {
        color: #cbd5e1;
    }

    /* Badge Superadmin Custom */
    .badge-superadmin {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #ffffff;
        font-size: 0.65rem;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 600;
        letter-spacing: 0.3px;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
    }

    .group-header-card {
        background: rgba(30, 41, 59, 0.5);
        border-radius: 12px;
        padding: 16px 20px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        margin-bottom: 24px;
    }

    .setting-row-item {
        padding: 16px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .setting-row-item:last-child {
        border-bottom: none;
    }
</style>

<div class="row">
    <!-- Left Navigation Bar -->
    <div class="col-lg-3 col-md-4 mb-4">
        <div class="card settings-card">
            <div class="card-header bg-transparent border-0 pt-3 pb-2 px-3">
                <h6 class="text-uppercase text-muted font-weight-bold m-0" style="font-size: 0.72rem; letter-spacing: 1px;">
                    <i class="fas fa-sliders-h mr-1 text-info"></i> {{ __('messages.system_settings') ?? 'System Settings' }}
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="nav flex-column nav-pills settings-nav" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    @php 
                        $first = true; 
                        $isSuperAdmin = Auth::user() && (Auth::user()->hasRole('Super Admin') || Auth::user()->id === 1);

                        // Icon, color, and subtitle mapping for each settings group
                        $metaMap = [
                            'general' => [
                                'icon' => 'fas fa-sliders-h',
                                'color' => '#3b82f6',
                                'bg' => 'rgba(59, 130, 246, 0.15)',
                                'border' => 'rgba(59, 130, 246, 0.3)',
                                'subtitle' => 'Identitas & Opsi Umum',
                            ],
                            'email' => [
                                'icon' => 'fas fa-paper-plane',
                                'color' => '#06b6d4',
                                'bg' => 'rgba(6, 182, 212, 0.15)',
                                'border' => 'rgba(6, 182, 212, 0.3)',
                                'subtitle' => 'SMTP Server & Notifikasi Email',
                            ],
                            'system' => [
                                'icon' => 'fas fa-server',
                                'color' => '#f59e0b',
                                'bg' => 'rgba(245, 158, 11, 0.15)',
                                'border' => 'rgba(245, 158, 11, 0.3)',
                                'subtitle' => 'Mode Maintenance & Backup',
                            ],
                            'ai' => [
                                'icon' => 'fas fa-brain',
                                'color' => '#a855f7',
                                'bg' => 'rgba(168, 85, 247, 0.15)',
                                'border' => 'rgba(168, 85, 247, 0.3)',
                                'subtitle' => 'Google Gemini & OpenAI GPT',
                            ],
                            'whatsapp' => [
                                'icon' => 'fab fa-whatsapp',
                                'color' => '#10b981',
                                'bg' => 'rgba(16, 185, 129, 0.15)',
                                'border' => 'rgba(16, 185, 129, 0.3)',
                                'subtitle' => 'Gateway Fonnte, Wablas, & HTTP API',
                            ],
                            'telegram' => [
                                'icon' => 'fab fa-telegram-plane',
                                'color' => '#3b82f6',
                                'bg' => 'rgba(59, 130, 246, 0.15)',
                                'border' => 'rgba(59, 130, 246, 0.3)',
                                'subtitle' => 'Bot & Channel Notifikasi Telegram',
                            ],
                        ];
                    @endphp

                    @foreach($settings as $group => $items)
                        @php
                            $meta = $metaMap[$group] ?? [
                                'icon' => 'fas fa-cog',
                                'color' => '#38bdf8',
                                'bg' => 'rgba(56, 189, 248, 0.15)',
                                'border' => 'rgba(56, 189, 248, 0.3)',
                                'subtitle' => 'Konfigurasi Sistem',
                            ];
                        @endphp
                        <a class="nav-link {{ $first ? 'active' : '' }}" 
                           id="v-pills-{{ $group }}-tab" 
                           data-toggle="pill" 
                           href="#v-pills-{{ $group }}" 
                           role="tab" 
                           aria-controls="v-pills-{{ $group }}" 
                           aria-selected="{{ $first ? 'true' : 'false' }}"
                           style="--active-color: {{ $meta['color'] }}; 
                                  --active-bg: rgba({{ implode(',', sscanf($meta['color'], '#%02x%02x%02x')) }}, 0.12);
                                  --active-border: rgba({{ implode(',', sscanf($meta['color'], '#%02x%02x%02x')) }}, 0.35);
                                  --active-shadow: rgba({{ implode(',', sscanf($meta['color'], '#%02x%02x%02x')) }}, 0.2);
                                  --icon-color: {{ $meta['color'] }};
                                  --icon-bg: {{ $meta['bg'] }};
                                  --icon-border: {{ $meta['border'] }};
                                  --icon-active-color: #ffffff;
                                  --icon-active-bg: {{ $meta['color'] }};
                                  --icon-active-border: {{ $meta['color'] }};">
                            
                            <div class="nav-icon-box">
                                <i class="{{ $meta['icon'] }}"></i>
                            </div>
                            
                            <div class="nav-text-box">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="nav-title">{{ __('messages.' . $group . '_settings') ?? ucfirst($group) . ' Settings' }}</span>
                                    @if($group == 'ai')
                                        <span class="badge badge-superadmin ml-1"><i class="fas fa-lock mr-1"></i> Admin</span>
                                    @endif
                                </div>
                                <span class="nav-subtitle">{{ $meta['subtitle'] }}</span>
                            </div>
                        </a>
                        @php $first = false; @endphp
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Form Content -->
    <div class="col-lg-9 col-md-8">
        <div class="card settings-card">
            <form action="{{ route('settings.updateAll') }}" method="POST">
                @csrf
                <input type="hidden" name="active_tab" id="active_tab_input" value="{{ request('active_tab', '#v-pills-general') }}">
                <div class="card-body p-4">
                    <div class="tab-content" id="v-pills-tabContent">
                        @php $first = true; @endphp
                        @foreach($settings as $group => $items)
                            @php
                                $meta = $metaMap[$group] ?? [
                                    'icon' => 'fas fa-cog',
                                    'color' => '#38bdf8',
                                    'subtitle' => 'Konfigurasi Sistem',
                                ];
                            @endphp
                            <div class="tab-pane fade {{ $first ? 'show active' : '' }}" id="v-pills-{{ $group }}" role="tabpanel" aria-labelledby="v-pills-{{ $group }}-tab">
                                <!-- Tab Header -->
                                <div class="group-header-card d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="p-3 rounded-circle mr-3" style="background: rgba({{ implode(',', sscanf($meta['color'], '#%02x%02x%02x')) }}, 0.15); border: 1px solid {{ $meta['color'] }};">
                                            <i class="{{ $meta['icon'] }} fa-lg" style="color: {{ $meta['color'] }};"></i>
                                        </div>
                                        <div>
                                            <h4 class="m-0 font-weight-bold" style="color: #f8fafc;">
                                                {{ __('messages.' . $group . '_configuration') ?? ucfirst($group) . ' Configuration' }}
                                            </h4>
                                            <small class="text-muted">{{ $meta['subtitle'] }}</small>
                                        </div>
                                    </div>
                                    @if($group == 'ai')
                                        <span class="badge badge-warning px-3 py-2"><i class="fas fa-user-shield mr-1"></i> {{ __('messages.superadmin_only_ai') }}</span>
                                    @endif
                                </div>
                                
                                @if($group == 'ai' && !$isSuperAdmin)
                                    <div class="alert alert-warning border-0" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3);">
                                        <i class="fas fa-lock mr-2"></i> {{ __('messages.superadmin_only_ai') }}
                                    </div>
                                @endif

                                <!-- Form Fields -->
                                @foreach($items as $setting)
                                    <div class="form-group row align-items-center setting-row-item">
                                        <label class="col-sm-4 col-form-label theme-text font-weight-bold">
                                            {{ __('messages.' . $setting->key) ?? ucwords(str_replace('_', ' ', $setting->key)) }}
                                        </label>
                                        <div class="col-sm-8">
                                            @if($setting->key == 'ai_provider')
                                                <select name="{{ $setting->key }}" class="form-control theme-input" {{ ($group == 'ai' && !$isSuperAdmin) ? 'disabled' : '' }}>
                                                    <option value="gemini" {{ $setting->value == 'gemini' ? 'selected' : '' }}>Google Gemini API (Disarankan)</option>
                                                    <option value="openai" {{ $setting->value == 'openai' ? 'selected' : '' }}>OpenAI GPT API</option>
                                                    <option value="off" {{ $setting->value == 'off' ? 'selected' : '' }}>Smart Engine Internal (Tanpa API Key)</option>
                                                </select>
                                            @elseif($setting->key == 'whatsapp_provider')
                                                <select name="{{ $setting->key }}" class="form-control theme-input">
                                                    <option value="fonnte" {{ $setting->value == 'fonnte' ? 'selected' : '' }}>Fonnte API (Sangat Disarankan di Indonesia)</option>
                                                    <option value="wablas" {{ $setting->value == 'wablas' ? 'selected' : '' }}>Wablas API Gateway</option>
                                                    <option value="generic" {{ $setting->value == 'generic' ? 'selected' : '' }}>Custom HTTP Webhook Gateway</option>
                                                </select>
                                            @elseif(in_array($setting->key, ['gemini_api_key', 'openai_api_key', 'whatsapp_api_token', 'telegram_bot_token']))
                                                <div class="input-group">
                                                    <input type="password" id="input-{{ $setting->key }}" name="{{ $setting->key }}" class="form-control theme-input" value="{{ $setting->value }}" placeholder="Masukkan Token / API Key..." {{ ($group == 'ai' && !$isSuperAdmin) ? 'disabled' : '' }}>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary toggle-key-btn" type="button" data-target="input-{{ $setting->key }}">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @elseif($setting->type == 'textarea')
                                                <textarea name="{{ $setting->key }}" class="form-control theme-input" rows="3" {{ ($group == 'ai' && !$isSuperAdmin) ? 'disabled' : '' }}>{{ $setting->value }}</textarea>
                                            @elseif($setting->key == 'ip_offline_email_time')
                                                 <input type="time" name="{{ $setting->key }}" class="form-control theme-input" style="width: 160px;" value="{{ $setting->value ?: '08:00' }}">
                                                 <small class="text-muted d-block mt-1"><i class="fas fa-clock mr-1"></i> Jam eksekusi harian laporan email IP Offline (Default: 08:00)</small>
                                            @elseif($setting->key == 'ip_offline_email_recipients')
                                                 <input type="text" name="{{ $setting->key }}" class="form-control theme-input" value="{{ $setting->value }}" placeholder="Contoh: admin@perusahaan.com, it@perusahaan.com">
                                                 <small class="text-muted d-block mt-1"><i class="fas fa-envelope mr-1"></i> Alamat email tujuan (pisahkan dengan koma jika lebih dari satu). Kosongkan untuk mengirim ke semua Super Admin.</small>
                                            @elseif($setting->key == 'whatsapp_admin_phone')
                                                 <input type="text" name="{{ $setting->key }}" class="form-control theme-input" value="{{ $setting->value }}" placeholder="Contoh: 081234567890">
                                                 <small class="text-muted d-block mt-1"><i class="fas fa-phone-alt mr-1"></i> Nomor telepon WhatsApp Admin/Penerima Alert (misal: 081234567890)</small>
                                            @elseif($setting->key == 'whatsapp_api_url')
                                                 <input type="text" name="{{ $setting->key }}" class="form-control theme-input" value="{{ $setting->value }}" placeholder="https://api.fonnte.com/send">
                                                 <small class="text-muted d-block mt-1"><i class="fas fa-link mr-1"></i> URL Endpoint API (Fonnte: https://api.fonnte.com/send | Wablas: https://kudus.wablas.com/api/send-message)</small>
                                            @elseif($setting->key == 'telegram_bot_token')
                                                  <div class="input-group">
                                                      <input type="password" name="{{ $setting->key }}" id="input-{{ $setting->key }}" class="form-control theme-input" value="{{ $setting->value }}" placeholder="Contoh: 8863536570:AAFeE...">
                                                      <div class="input-group-append">
                                                          <button class="btn btn-outline-secondary toggle-key-btn" type="button" data-target="input-{{ $setting->key }}">
                                                              <i class="fas fa-eye"></i>
                                                          </button>
                                                      </div>
                                                  </div>
                                                  <small class="text-muted d-block mt-1"><i class="fas fa-robot mr-1"></i> Bot utama untuk log aktivitas sistem, tiket helpdesk, dan asisten AI.</small>
                                             @elseif($setting->key == 'telegram_chat_id')
                                                  <input type="text" name="{{ $setting->key }}" class="form-control theme-input" value="{{ $setting->value }}" placeholder="Contoh: 8679359045">
                                                  <small class="text-muted d-block mt-1"><i class="fas fa-users mr-1"></i> ID Grup / Channel atau User Telegram tujuan untuk Log Aktivitas Utama.</small>
                                             @elseif($setting->key == 'telegram_ip_bot_token')
                                                  <div class="input-group">
                                                      <input type="password" name="{{ $setting->key }}" id="input-{{ $setting->key }}" class="form-control theme-input" value="{{ $setting->value }}" placeholder="Kosongkan jika ingin menggunakan Bot Utama">
                                                      <div class="input-group-append">
                                                          <button class="btn btn-outline-secondary toggle-key-btn" type="button" data-target="input-{{ $setting->key }}">
                                                              <i class="fas fa-eye"></i>
                                                          </button>
                                                      </div>
                                                  </div>
                                                  <small class="text-muted d-block mt-1"><i class="fas fa-network-wired mr-1"></i> Bot terpisah khusus notifikasi IP Offline/Online (Kosongkan jika ingin gabung dengan Bot Utama).</small>
                                             @elseif($setting->key == 'telegram_ip_chat_id')
                                                  <input type="text" name="{{ $setting->key }}" class="form-control theme-input" value="{{ $setting->value }}" placeholder="Kosongkan jika ingin menggunakan Chat ID Utama">
                                                  <small class="text-muted d-block mt-1"><i class="fas fa-users-cog mr-1"></i> ID Grup / Channel Telegram khusus Notifikasi IP Address (Kosongkan jika ingin menggunakan Chat ID Utama).</small>
                                            @elseif($setting->type == 'boolean')
                                                <select name="{{ $setting->key }}" class="form-control theme-input" style="width: 150px;" {{ ($group == 'ai' && !$isSuperAdmin) ? 'disabled' : '' }}>
                                                    <option value="1"  {{ $setting->value == '1' ? 'selected' : '' }}>{{ __('messages.enabled') }}</option>
                                                    <option value="0"  {{ $setting->value == '0' ? 'selected' : '' }}>{{ __('messages.disabled') }}</option>
                                                </select>
                                            @else
                                                <input type="text" name="{{ $setting->key }}" class="form-control theme-input" value="{{ $setting->value }}" {{ ($group == 'ai' && !$isSuperAdmin) ? 'disabled' : '' }}>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                @if($group == 'whatsapp')
                                    <div class="mt-4 pt-3 border-top" style="border-color: rgba(255,255,255,0.08) !important;">
                                        <button type="button" id="btn-test-whatsapp" class="btn btn-outline-success px-4 py-2" style="border-radius: 10px; font-weight: 600;">
                                            <i class="fab fa-whatsapp mr-2 fa-lg"></i> {{ __('messages.test_whatsapp') ?? 'Kirim Pesan Pengujian WhatsApp' }}
                                        </button>
                                        <span id="test-wa-spinner" class="spinner-border spinner-border-sm text-success ml-2 d-none" role="status"></span>
                                        <div id="test-wa-result" class="mt-3"></div>
                                    </div>
                                @endif
                                @if($group == 'telegram')
                                    <div class="mt-4 pt-3 border-top" style="border-color: rgba(255,255,255,0.08) !important;">
                                        <button type="button" id="btn-test-telegram" class="btn btn-outline-primary px-4 py-2" style="border-radius: 10px; font-weight: 600; color: #3b82f6; border-color: #3b82f6;">
                                            <i class="fab fa-telegram-plane mr-2 fa-lg"></i> {{ __('messages.test_telegram') ?? 'Kirim Pesan Pengujian Telegram' }}
                                        </button>
                                        <span id="test-tg-spinner" class="spinner-border spinner-border-sm text-primary ml-2 d-none" role="status"></span>
                                        <div id="test-tg-result" class="mt-3"></div>
                                    </div>
                                @endif
                            </div>
                            @php $first = false; @endphp
                        @endforeach
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-right pb-4 pr-4">
                    <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: 10px; font-weight: 600; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);">
                        <i class="fas fa-save mr-2"></i> {{ __('messages.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Tab Persistence Logic
    $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
        const target = $(e.target).attr('href');
        if (target) {
            $('#active_tab_input').val(target);
            localStorage.setItem('active_settings_tab', target);
            if (history.replaceState) {
                history.replaceState(null, null, target);
            }
        }
    });

    const urlParams = new URLSearchParams(window.location.search);
    const activeTabFromUrl = urlParams.get('active_tab');
    let targetTab = activeTabFromUrl || window.location.hash || localStorage.getItem('active_settings_tab');

    if (targetTab && $('a[href="' + targetTab + '"]').length) {
        $('a[href="' + targetTab + '"]').tab('show');
        $('#active_tab_input').val(targetTab);
    }

    $('.toggle-key-btn').click(function() {
        const targetId = $(this).data('target');
        const input = $('#' + targetId);
        const icon = $(this).find('i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    $('#btn-test-telegram').click(function() {
        const botToken = $('input[name="telegram_bot_token"]').val();
        const chatId = $('input[name="telegram_chat_id"]').val();
        
        if (!botToken || !chatId) {
            Swal.fire({ icon: 'error', text: 'Harap isi Bot Token dan Chat ID Telegram terlebih dahulu!', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, background: '#0f172a', color: '#f8fafc', customClass: { popup: 'border border-danger' } });
            return;
        }

        const btn = $(this);
        const spinner = $('#test-tg-spinner');
        const resultDiv = $('#test-tg-result');

        btn.prop('disabled', true);
        spinner.removeClass('d-none');
        resultDiv.html('');

        $.ajax({
            url: '{{ route("settings.telegram.test") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                telegram_bot_token: botToken,
                telegram_chat_id: chatId
            },
            success: function(res) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                if (res.success) {
                    resultDiv.html('<div class="alert alert-success mt-2 mb-0" style="border-radius: 10px;"><i class="fas fa-check-circle mr-2"></i> ' + res.message + '</div>');
                } else {
                    resultDiv.html('<div class="alert alert-danger mt-2 mb-0" style="border-radius: 10px;"><i class="fas fa-exclamation-triangle mr-2"></i> ' + (res.message || 'Error') + '</div>');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                resultDiv.html('<div class="alert alert-danger mt-2 mb-0" style="border-radius: 10px;"><i class="fas fa-times-circle mr-2"></i> Terjadi kesalahan koneksi saat menguji pesan Telegram.</div>');
            }
        });
    });

    $('#btn-test-whatsapp').click(function() {
        const adminPhone = $('input[name="whatsapp_admin_phone"]').val();
        if (!adminPhone) {
            Swal.fire({ icon: 'error', text: 'Harap isi Nomor WhatsApp Admin Penerima Alert terlebih dahulu!', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, background: '#0f172a', color: '#f8fafc', customClass: { popup: 'border border-danger' } });
            return;
        }

        const btn = $(this);
        const spinner = $('#test-wa-spinner');
        const resultDiv = $('#test-wa-result');

        btn.prop('disabled', true);
        spinner.removeClass('d-none');
        resultDiv.html('');

        $.ajax({
            url: '{{ route("settings.whatsapp.test") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                whatsapp_admin_phone: adminPhone
            },
            success: function(res) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                if (res.success) {
                    resultDiv.html('<div class="alert alert-success mt-2 mb-0" style="border-radius: 10px;"><i class="fas fa-check-circle mr-2"></i> ' + res.message + '</div>');
                } else {
                    resultDiv.html('<div class="alert alert-danger mt-2 mb-0" style="border-radius: 10px;"><i class="fas fa-exclamation-triangle mr-2"></i> ' + res.message + '</div>');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                resultDiv.html('<div class="alert alert-danger mt-2 mb-0" style="border-radius: 10px;"><i class="fas fa-times-circle mr-2"></i> Terjadi kesalahan koneksi saat menguji pesan WhatsApp.</div>');
            }
        });
    });
});
</script>
@endpush

@endsection
