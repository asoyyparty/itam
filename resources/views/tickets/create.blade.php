@extends('layouts.admin')

@section('title', __('messages.create_ticket'))

@section('content')
<style>
/* Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 */
:root {
    --hl-bg-canvas: #090d16;
    --hl-bg-surface: #0f172a;
    --hl-bg-card: rgba(15, 23, 42, 0.82);
    --hl-bg-card-sub: rgba(15, 23, 42, 0.55);
    --hl-border-subtle: rgba(148, 163, 184, 0.14);
    --hl-border-active: rgba(148, 163, 184, 0.28);
    --hl-text-bright: #f8fafc;
    --hl-text-muted: #94a3b8;
    --hl-text-dim: #64748b;
    --hl-accent-cyan: #06b6d4;
    --hl-accent-cyan-glow: rgba(6, 182, 212, 0.2);
    --hl-accent-indigo: #6366f1;
    --hl-accent-purple: #a855f7;
    --hl-status-open: #38bdf8;
    --hl-status-progress: #f59e0b;
    --hl-status-resolved: #10b981;
    --hl-priority-critical: #f43f5e;
    --hl-font-mono: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}

.ticket-create-container {
    width: 100%;
    margin-bottom: 2.5rem;
    overflow-x: clip;
}

/* Header Banner */
.ticket-create-banner {
    position: relative;
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.85) 100%);
    border: 1px solid var(--hl-border-subtle);
    border-radius: 16px;
    padding: 20px 24px;
    margin-bottom: 22px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    backdrop-filter: blur(12px);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}

.ticket-banner-info {
    display: flex;
    align-items: center;
    gap: 14px;
}

.ticket-banner-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: rgba(99, 102, 241, 0.15);
    border: 1px solid rgba(99, 102, 241, 0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #818cf8;
    font-size: 1.25rem;
    flex-shrink: 0;
    box-shadow: 0 0 16px rgba(99, 102, 241, 0.2);
}

.ticket-pulse-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-family: var(--hl-font-mono);
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--hl-status-open);
    background: rgba(56, 189, 248, 0.12);
    border: 1px solid rgba(56, 189, 248, 0.3);
    padding: 4px 10px;
    border-radius: 9999px;
}

.pulse-dot-cyan {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background-color: var(--hl-status-open);
    box-shadow: 0 0 8px var(--hl-status-open);
    animation: pulse-glow 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
}

@keyframes pulse-glow {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(0.85); }
}

.ticket-banner-controls {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.btn-ai-telemetry {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 18px;
    border-radius: 10px;
    font-size: 0.84rem;
    font-weight: 700;
    color: #f3e8ff !important;
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.25) 0%, rgba(99, 102, 241, 0.25) 100%);
    border: 1px solid rgba(168, 85, 247, 0.45);
    box-shadow: 0 0 14px rgba(168, 85, 247, 0.2);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    cursor: pointer;
}

.btn-ai-telemetry:hover {
    transform: translateY(-1px);
    border-color: rgba(168, 85, 247, 0.7);
    box-shadow: 0 4px 18px rgba(168, 85, 247, 0.35);
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.35) 0%, rgba(99, 102, 241, 0.35) 100%);
    color: #ffffff !important;
}

.btn-tactile-back {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 16px;
    border-radius: 10px;
    font-size: 0.84rem;
    font-weight: 600;
    color: var(--hl-text-bright) !important;
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid var(--hl-border-active);
    transition: all 0.2s;
    text-decoration: none !important;
}

.btn-tactile-back:hover {
    background: rgba(30, 41, 59, 0.9);
    border-color: rgba(148, 163, 184, 0.45);
    transform: translateY(-1px);
}

/* AI Diagnostic HUD Drawer */
.ai-diagnostic-hud {
    border-radius: 14px;
    border: 1px solid rgba(168, 85, 247, 0.35);
    background: linear-gradient(135deg, rgba(30, 27, 75, 0.6) 0%, rgba(15, 23, 42, 0.85) 100%);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35);
    padding: 20px 22px;
    margin-bottom: 22px;
    backdrop-filter: blur(12px);
}

.ai-hud-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    padding-bottom: 14px;
    border-bottom: 1px solid rgba(168, 85, 247, 0.2);
    margin-bottom: 16px;
}

.ai-hud-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #d8b4fe;
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    letter-spacing: 0.02em;
}

.btn-apply-ai {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 700;
    color: #ffffff;
    background: #059669;
    border: 1px solid #10b981;
    box-shadow: 0 2px 10px rgba(16, 185, 129, 0.25);
    cursor: pointer;
    transition: all 0.2s;
}

.btn-apply-ai:hover {
    background: #047857;
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4);
}

.ai-hud-meta-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 14px;
    background: rgba(0, 0, 0, 0.25);
    border: 1px solid rgba(148, 163, 184, 0.1);
    border-radius: 10px;
    padding: 12px 16px;
}

.ai-hud-field-label {
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--hl-text-dim);
    margin-bottom: 4px;
}

.ai-hud-field-val {
    font-size: 0.92rem;
    font-weight: 600;
    color: var(--hl-text-bright);
}

.ai-hud-box {
    background: rgba(0, 0, 0, 0.22);
    border: 1px solid rgba(148, 163, 184, 0.1);
    border-radius: 10px;
    padding: 12px 16px;
    margin-bottom: 14px;
}

.ai-hud-steps-list {
    margin: 0;
    padding-left: 20px;
    color: var(--hl-text-bright);
    font-size: 0.84rem;
    line-height: 1.6;
}

.ai-hud-steps-list li {
    margin-bottom: 4px;
}

.ai-hud-reply-box {
    font-family: var(--hl-font-mono);
    font-size: 0.82rem;
    color: #e2e8f0;
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid rgba(148, 163, 184, 0.16);
    border-radius: 10px;
    padding: 12px 16px;
    margin: 0;
    white-space: pre-wrap;
    word-break: break-word;
}

/* Bento Form Card */
.ticket-form-bento {
    background: var(--hl-bg-card);
    border: 1px solid var(--hl-border-subtle);
    border-radius: 16px;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.25);
    backdrop-filter: blur(12px);
    overflow: visible;
}

.form-bento-body {
    padding: 24px 28px;
}

/* Bento Section Separation */
.form-bento-section {
    padding-bottom: 22px;
    margin-bottom: 22px;
    border-bottom: 1px solid var(--hl-border-subtle);
}

.form-bento-section:last-child {
    padding-bottom: 0;
    margin-bottom: 0;
    border-bottom: none;
}

.bento-section-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.76rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--hl-accent-cyan);
    margin-bottom: 16px;
}

.bento-section-title i {
    font-size: 0.85rem;
}

.form-field-group {
    margin-bottom: 16px;
}

.form-label-tactile {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 0.84rem;
    font-weight: 600;
    color: var(--hl-text-bright);
    margin-bottom: 8px;
}

.form-label-tactile .req-star {
    color: var(--hl-priority-critical);
}

/* Custom Tactical Inputs */
.tactile-input {
    width: 100%;
    height: 44px !important;
    border-radius: 10px !important;
    border: 1px solid var(--hl-border-subtle) !important;
    background: rgba(15, 23, 42, 0.6) !important;
    color: var(--hl-text-bright) !important;
    font-size: 0.88rem !important;
    padding: 0 14px !important;
    outline: none !important;
    transition: all 0.2s ease !important;
}

.tactile-input:focus {
    border-color: var(--hl-accent-cyan) !important;
    box-shadow: 0 0 0 2px var(--hl-accent-cyan-glow) !important;
    background: rgba(15, 23, 42, 0.9) !important;
}

.tactile-textarea {
    width: 100%;
    border-radius: 12px !important;
    border: 1px solid var(--hl-border-subtle) !important;
    background: rgba(15, 23, 42, 0.6) !important;
    color: var(--hl-text-bright) !important;
    font-size: 0.88rem !important;
    padding: 12px 14px !important;
    outline: none !important;
    transition: all 0.2s ease !important;
    resize: vertical;
    min-height: 120px;
}

.tactile-textarea:focus {
    border-color: var(--hl-accent-cyan) !important;
    box-shadow: 0 0 0 2px var(--hl-accent-cyan-glow) !important;
    background: rgba(15, 23, 42, 0.9) !important;
}

/* Title Template Selector */
.title-input-wrapper {
    position: relative;
    width: 100%;
}

.btn-toggle-templates {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    height: 30px;
    padding: 0 10px;
    border-radius: 6px;
    background: rgba(6, 182, 212, 0.12);
    border: 1px solid rgba(6, 182, 212, 0.25);
    color: #67e8f9;
    font-size: 0.74rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.15s;
    z-index: 5;
}

.btn-toggle-templates:hover {
    background: rgba(6, 182, 212, 0.22);
    color: #ffffff;
}

.template-dropdown-menu {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    right: 0;
    z-index: 1080;
    background: #0f172a;
    border: 1px solid var(--hl-border-active);
    border-radius: 12px;
    box-shadow: 0 14px 35px rgba(0, 0, 0, 0.6);
    padding: 8px;
    max-height: 260px;
    overflow-y: auto;
    display: none;
}

.template-dropdown-menu.show {
    display: block;
}

.template-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    border-radius: 8px;
    font-size: 0.84rem;
    color: var(--hl-text-bright);
    text-decoration: none !important;
    transition: background 0.15s, color 0.15s;
    cursor: pointer;
}

.template-item:hover {
    background: rgba(99, 102, 241, 0.2);
    color: #a5b4fc;
}

/* Select2 Theme Calibration */
.ticket-form-bento .select2-container--default .select2-selection--single {
    height: 44px !important;
    border-radius: 10px !important;
    border: 1px solid var(--hl-border-subtle) !important;
    background: rgba(15, 23, 42, 0.6) !important;
    box-shadow: none !important;
    font-size: 0.88rem !important;
    color: var(--hl-text-bright) !important;
    transition: border-color 0.2s;
}

.ticket-form-bento .select2-container--default.select2-container--open .select2-selection--single,
.ticket-form-bento .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: var(--hl-accent-cyan) !important;
    box-shadow: 0 0 0 2px var(--hl-accent-cyan-glow) !important;
}

.ticket-form-bento .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 42px !important;
    padding-left: 14px !important;
    color: var(--hl-text-bright) !important;
}

.ticket-form-bento .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 42px !important;
    right: 8px !important;
}

/* Flatpickr input group */
.tactile-input-group {
    display: flex;
    align-items: center;
    width: 100%;
}

.tactile-input-addon {
    height: 44px;
    padding: 0 14px;
    border-radius: 10px 0 0 10px;
    border: 1px solid var(--hl-border-subtle);
    border-right: none;
    background: rgba(15, 23, 42, 0.4);
    color: var(--hl-text-dim);
    display: flex;
    align-items: center;
    justify-content: center;
}

.tactile-input-grouped {
    border-radius: 0 10px 10px 0 !important;
}

/* Category Description Box */
.category-desc-box {
    margin-top: 8px;
    padding: 8px 12px;
    border-radius: 8px;
    background: rgba(6, 182, 212, 0.08);
    border: 1px solid rgba(6, 182, 212, 0.2);
    font-size: 0.78rem;
    color: #67e8f9;
    display: none;
}

/* Form Footer Actions */
.form-bento-footer {
    padding: 18px 28px;
    border-top: 1px solid var(--hl-border-subtle);
    background: rgba(15, 23, 42, 0.4);
    border-bottom-left-radius: 16px;
    border-bottom-right-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
}

.btn-tactile-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 24px;
    border-radius: 10px;
    font-size: 0.86rem;
    font-weight: 700;
    color: #ffffff !important;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    border: 1px solid rgba(129, 140, 248, 0.4);
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.3);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    cursor: pointer;
    min-width: 140px;
}

.btn-tactile-submit:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.45);
    filter: brightness(1.08);
}

.btn-tactile-cancel {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 0.86rem;
    font-weight: 600;
    color: var(--hl-text-muted) !important;
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid var(--hl-border-subtle);
    transition: all 0.2s;
    text-decoration: none !important;
}

.btn-tactile-cancel:hover {
    background: rgba(30, 41, 59, 0.8);
    color: var(--hl-text-bright) !important;
    border-color: var(--hl-border-active);
}

/* Mobile Responsiveness */
@media (max-width: 767.98px) {
    .ticket-create-banner {
        padding: 14px 16px;
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
        border-radius: 14px;
        margin-bottom: 14px;
    }

    .ticket-banner-icon {
        width: 38px;
        height: 38px;
        font-size: 1.1rem;
    }

    .ticket-banner-controls {
        width: 100%;
        display: flex;
        gap: 8px;
    }

    .btn-ai-telemetry,
    .btn-tactile-back {
        flex: 1 1 50%;
        justify-content: center;
        padding: 8px 10px;
        font-size: 0.78rem;
    }

    .form-bento-body {
        padding: 16px 14px;
    }

    .ai-diagnostic-hud {
        padding: 14px 14px;
        margin-bottom: 16px;
    }

    .ai-hud-meta-grid {
        grid-template-columns: 1fr;
        gap: 10px;
        padding: 10px 12px;
    }

    .form-bento-section {
        padding-bottom: 16px;
        margin-bottom: 16px;
    }

    .form-bento-footer {
        padding: 14px 14px;
        flex-direction: column-reverse;
        gap: 10px;
    }

    .btn-tactile-submit,
    .btn-tactile-cancel {
        width: 100%;
        height: 44px;
    }
}
</style>

<div class="ticket-create-container">
    <!-- Command Header Banner -->
    <div class="ticket-create-banner">
        <div class="ticket-banner-info">
            <div class="ticket-banner-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <h5 class="m-0 font-weight-bold" style="font-size: 1.15rem; color: var(--hl-text-bright); line-height: 1.2;">
                        {{ __('messages.create_ticket') }}
                    </h5>
                    <span class="ticket-pulse-badge">
                        <span class="pulse-dot-cyan"></span>
                        {{ __('messages.ticket_intake_console') }}
                    </span>
                </div>
                <small style="font-family: var(--hl-font-mono); font-size: 0.74rem; color: var(--hl-text-dim);">
                    {{ __('messages.ticket_intake_sub') }}
                </small>
            </div>
        </div>

        <div class="ticket-banner-controls">
            <button type="button" id="btn-ai-analyze" class="btn-ai-telemetry">
                <i class="fas fa-magic"></i>
                <span>{{ __('messages.ai_assistant_analysis') }}</span>
            </button>
            <a href="{{ route('tickets.index') }}" class="btn-tactile-back">
                <i class="fas fa-arrow-left mr-1"></i>
                <span>{{ __('messages.back') }}</span>
            </a>
        </div>
    </div>

    <!-- AI Diagnostic HUD Drawer (Hidden by default, triggered on demand) -->
    <div id="ai-analysis-result" class="ai-diagnostic-hud" style="display: none;">
        <div class="ai-hud-top">
            <h6 class="ai-hud-title">
                <i class="fas fa-robot text-purple-400"></i>
                {{ __('messages.ai_diagnosis_recommendations') }}
            </h6>
            <button type="button" id="btn-apply-ai-suggestions" class="btn-apply-ai">
                <i class="fas fa-check-double"></i>
                <span>{{ __('messages.apply_ai_suggestions') }}</span>
            </button>
        </div>

        <div class="ai-hud-meta-grid">
            <div>
                <div class="ai-hud-field-label">{{ __('messages.recommended_category') }}</div>
                <div id="ai-cat-res" class="ai-hud-field-val text-cyan" style="color: #67e8f9;">-</div>
            </div>
            <div>
                <div class="ai-hud-field-label">{{ __('messages.recommended_priority') }}</div>
                <div id="ai-priority-container">
                    <span id="ai-priority-badge" class="badge px-3 py-1" style="font-size: 0.76rem; border-radius: 6px;">-</span>
                </div>
            </div>
        </div>

        <div class="ai-hud-box">
            <div class="ai-hud-field-label">{{ __('messages.root_cause_diagnosis') }}</div>
            <p id="ai-diagnosis-res" class="mb-0" style="font-size: 0.86rem; color: var(--hl-text-bright); line-height: 1.5; font-style: normal;"></p>
        </div>

        <div class="ai-hud-box">
            <div class="ai-hud-field-label mb-2">{{ __('messages.it_support_steps') }}</div>
            <ul id="ai-steps-res" class="ai-hud-steps-list"></ul>
        </div>

        <div>
            <div class="d-flex justify-content-between align-items-center mb-1.5">
                <span class="ai-hud-field-label mb-0">{{ __('messages.draft_user_reply') }}</span>
                <button type="button" id="btn-copy-ai-reply" class="btn-tactile-back py-1 px-2" style="font-size: 0.72rem; height: 28px;">
                    <i class="far fa-copy mr-1"></i> {{ __('messages.copy_reply') }}
                </button>
            </div>
            <pre id="ai-reply-res" class="ai-hud-reply-box"></pre>
        </div>
    </div>

    <!-- Main Bento Form Card -->
    <div class="ticket-form-bento">
        <form action="{{ route('tickets.store') }}" method="POST">
            @csrf

            <div class="form-bento-body">
                <!-- Section 1: Subject & Templates -->
                <div class="form-bento-section">
                    <div class="bento-section-title">
                        <i class="fas fa-heading"></i>
                        <span>{{ __('messages.incident_subject') }}</span>
                    </div>

                    <div class="form-field-group mb-0">
                        <label class="form-label-tactile">
                            <span>{{ __('messages.title_subject') }}</span>
                            <span class="req-star">*</span>
                        </label>
                        <div class="title-input-wrapper">
                            <input type="text" id="ticket_title" name="title" class="tactile-input @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="{{ __('messages.ticket_title_placeholder') }}" required style="padding-right: 140px !important;" autocomplete="off">
                            
                            <button id="btn-toggle-title-templates" class="btn-toggle-templates" type="button" title="{{ __('messages.select_title_template') }}">
                                <i class="fas fa-layer-group"></i>
                                <span>Templates</span>
                                <i class="fas fa-chevron-down" style="font-size: 0.65rem;"></i>
                            </button>

                            <div id="title-template-menu" class="template-dropdown-menu">
                                <div class="px-2 py-1 text-muted text-uppercase font-mono" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.05em; border-bottom: 1px solid var(--hl-border-subtle); margin-bottom: 4px;">
                                    {{ __('messages.title_templates') }}
                                </div>
                                @foreach($templates as $template)
                                    <div class="template-item template-title-item" data-title="{{ $template }}">
                                        <i class="fas fa-tag text-cyan mr-1" style="font-size: 0.72rem; color: var(--hl-accent-cyan);"></i>
                                        <span>{{ $template }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @error('title') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Section 2: Reporter & Linked Infrastructure -->
                <div class="form-bento-section">
                    <div class="bento-section-title">
                        <i class="fas fa-users-cog"></i>
                        <span>{{ __('messages.reporter') }} &amp; {{ __('messages.asset') }}</span>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 form-field-group">
                            <label class="form-label-tactile">
                                <span>{{ __('messages.reporter_employee') }}</span>
                                <span class="req-star">*</span>
                            </label>
                            <select name="employee_id" class="form-control select2 @error('employee_id') is-invalid @enderror" required>
                                <option value="">-- {{ __('messages.select_employee') }} --</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->employee_id }} — {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-lg-6 form-field-group">
                            <label class="form-label-tactile">
                                <span>{{ __('messages.related_asset_optional') }}</span>
                                <span class="text-muted small ml-1">({{ __('messages.optional') }})</span>
                            </label>
                            <select name="asset_id" class="form-control select2 @error('asset_id') is-invalid @enderror">
                                <option value="">-- {{ __('messages.none') }} --</option>
                                @foreach($assets as $asset)
                                    <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                        [{{ $asset->asset_tag }}] {{ $asset->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('asset_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 3: Triage Classification -->
                <div class="form-bento-section">
                    <div class="bento-section-title">
                        <i class="fas fa-sitemap"></i>
                        <span>{{ __('messages.report_category') }} &amp; {{ __('messages.priority') }}</span>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 form-field-group">
                            <label class="form-label-tactile">
                                <span>{{ __('messages.report_category') }}</span>
                                <span class="req-star">*</span>
                            </label>
                            <select name="category" id="category_select" class="form-control select2 @error('category') is-invalid @enderror" required>
                                <option value="">-- {{ __('messages.select_category') }} --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category['name'] }}" {{ old('category') == $category['name'] ? 'selected' : '' }}>
                                        {{ $category['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="category_description_box" class="category-desc-box">
                                <i class="fas fa-info-circle mr-1"></i>
                                <span id="category_description_text"></span>
                            </div>
                            @error('category') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-lg-6 form-field-group">
                            <label class="form-label-tactile">
                                <span>{{ __('messages.priority') }}</span>
                                <span class="req-star">*</span>
                            </label>
                            <select name="priority" class="form-control select2 @error('priority') is-invalid @enderror" required>
                                <option value="Low" {{ old('priority', 'Low') == 'Low' ? 'selected' : '' }}>{{ __('messages.low') }}</option>
                                <option value="Medium" {{ old('priority', 'Medium') == 'Medium' ? 'selected' : '' }}>{{ __('messages.medium') }}</option>
                                <option value="High" {{ old('priority', 'High') == 'High' ? 'selected' : '' }}>{{ __('messages.high') }}</option>
                                <option value="Critical" {{ old('priority', 'Critical') == 'Critical' ? 'selected' : '' }}>{{ __('messages.critical') }}</option>
                            </select>
                            @error('priority') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 4: Operational Assignment & Timeline -->
                <div class="form-bento-section">
                    <div class="bento-section-title">
                        <i class="fas fa-user-check"></i>
                        <span>{{ __('messages.pic_assigned') }} &amp; {{ __('messages.timeline') }}</span>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 form-field-group">
                            <label class="form-label-tactile">
                                <span>{{ __('messages.pic_assigned') }}</span>
                                <span class="text-muted small ml-1">({{ __('messages.optional') }})</span>
                            </label>
                            <select name="pic_id" id="pic_id" class="form-control select2 @error('pic_id') is-invalid @enderror">
                                <option value="">-- {{ __('messages.unassigned') }} --</option>
                                @foreach($pics as $pic)
                                    <option value="{{ $pic->id }}" {{ old('pic_id') == $pic->id ? 'selected' : '' }}>
                                        {{ $pic->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pic_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-lg-6 form-field-group">
                            <label class="form-label-tactile">
                                <span>{{ __('messages.report_time_created') }}</span>
                            </label>
                            <div class="tactile-input-group">
                                <div class="tactile-input-addon">
                                    <i class="far fa-clock" style="color: var(--hl-status-open);"></i>
                                </div>
                                <input type="text" name="created_at" class="tactile-input tactile-input-grouped flatpickr-datetime font-mono" value="{{ old('created_at', now()->format('Y-m-d\TH:i')) }}">
                            </div>
                            <small class="text-muted mt-1 d-block" style="font-size: 0.74rem;">
                                {{ __('messages.leave_blank_for_current_time') }}
                            </small>
                            @error('created_at') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 5: Incident Description -->
                <div class="form-bento-section">
                    <div class="bento-section-title">
                        <i class="fas fa-align-left"></i>
                        <span>{{ __('messages.description_details') }}</span>
                    </div>

                    <div class="form-field-group mb-0">
                        <label class="form-label-tactile">
                            <span>{{ __('messages.description') }}</span>
                            <span class="req-star">*</span>
                        </label>
                        <textarea name="description" class="tactile-textarea @error('description') is-invalid @enderror" rows="5" placeholder="{{ __('messages.description_placeholder') }}" required>{{ old('description') }}</textarea>
                        @error('description') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Bento Footer -->
            <div class="form-bento-footer">
                <a href="{{ route('tickets.index') }}" class="btn-tactile-cancel">
                    {{ __('messages.cancel') }}
                </a>
                <button type="submit" class="btn-tactile-submit">
                    <i class="fas fa-save mr-1"></i>
                    <span>{{ __('messages.save_ticket') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Select2 Initialization
    $('.select2').select2({ width: '100%' });

    const categoriesData = @json($categories);
    const categorySelect = document.getElementById('category_select');
    const categoryDescBox = document.getElementById('category_description_box');
    const categoryDescText = document.getElementById('category_description_text');

    function updateCategoryDescription() {
        if (!categorySelect) return;
        const selectedValue = categorySelect.value;
        const category = categoriesData.find(c => c.name === selectedValue);
        if (category && category.description) {
            categoryDescText.innerHTML = '<strong>' + @json(__('messages.task_description')) + '</strong> ' + category.description;
            categoryDescBox.style.display = 'block';
        } else {
            categoryDescBox.style.display = 'none';
        }
    }

    if (categorySelect) {
        $(categorySelect).on('select2:select change', updateCategoryDescription);
        updateCategoryDescription();
    }

    // Toggle Title Template Menu
    $('#btn-toggle-title-templates').on('click', function(e) {
        e.stopPropagation();
        $('#title-template-menu').toggleClass('show');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.title-input-wrapper').length) {
            $('#title-template-menu').removeClass('show');
        }
    });

    // Template Title Selection
    $(document).on('click', '.template-title-item', function(e) {
        e.preventDefault();
        const title = $(this).attr('data-title');
        $('#ticket_title').val(title).focus();
        $('#title-template-menu').removeClass('show');
    });

    // AI Telemetry Analysis Handler
    let latestAiResult = null;
    $('#btn-ai-analyze').click(function() {
        const title = $('#ticket_title').val();
        const description = $('textarea[name="description"]').val();
        const employeeId = $('select[name="employee_id"]').val();
        const assetId = $('select[name="asset_id"]').val();

        if (!title && !description) {
            Swal.fire({
                icon: 'warning',
                title: @json(__('messages.incident_subject')),
                text: @json(__('messages.empty_title_desc_warning')),
                background: 'rgba(15, 23, 42, 0.95)',
                color: '#f8fafc'
            });
            return;
        }

        const btn = $(this);
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> ' + @json(__('messages.analyzing_ai')));

        $.ajax({
            url: "{{ route('ai.analyze-ticket') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                title: title,
                description: description,
                employee_id: employeeId,
                asset_id: assetId
            },
            success: function(response) {
                btn.prop('disabled', false).html(originalHtml);
                if (response.success && response.data) {
                    latestAiResult = response.data;
                    const res = response.data;

                    $('#ai-cat-res').text(res.suggested_category || 'Hardware & Software Support');
                    
                    const badgePriorityClass = res.suggested_priority === 'Critical' ? 'badge-priority-critical' : 
                                              (res.suggested_priority === 'High' ? 'badge-priority-high' : 
                                              (res.suggested_priority === 'Medium' ? 'badge-priority-medium' : 'badge-priority-low'));
                    
                    $('#ai-priority-badge').attr('class', 'badge ' + badgePriorityClass).text(res.suggested_priority || 'Medium');
                    $('#ai-diagnosis-res').text(res.diagnosis || '');

                    let stepsHtml = '';
                    if (res.resolution_steps && Array.isArray(res.resolution_steps)) {
                        res.resolution_steps.forEach(step => {
                            stepsHtml += '<li>' + step + '</li>';
                        });
                    }
                    $('#ai-steps-res').html(stepsHtml);

                    $('#ai-reply-res').text(res.reply_draft || '');
                    $('#ai-analysis-result').slideDown(250);

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        icon: 'success',
                        title: @json(__('messages.ai_analysis_complete')),
                        background: 'rgba(15, 23, 42, 0.95)',
                        color: '#f8fafc'
                    });
                }
            },
            error: function(err) {
                btn.prop('disabled', false).html(originalHtml);
                console.error(err);
            }
        });
    });

    // Apply AI Category & Priority
    $('#btn-apply-ai-suggestions').click(function() {
        if (!latestAiResult) return;
        
        if (latestAiResult.suggested_category) {
            $('#category_select').val(latestAiResult.suggested_category).trigger('change');
        }
        if (latestAiResult.suggested_priority) {
            $('select[name="priority"]').val(latestAiResult.suggested_priority).trigger('change');
        }

        Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500,
            icon: 'success',
            title: @json(__('messages.ai_suggestions_applied')),
            background: 'rgba(15, 23, 42, 0.95)',
            color: '#f8fafc'
        });
    });

    // Copy Draft Reply
    $('#btn-copy-ai-reply').click(function() {
        const text = $('#ai-reply-res').text();
        if (!text) return;
        copyToClipboard(text).then(function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    icon: 'success',
                    title: @json(__('messages.reply_draft_copied')),
                    background: 'rgba(15, 23, 42, 0.95)',
                    color: '#f8fafc'
                });
            }
        }).catch(function(err) {
            console.error('Could not copy reply draft: ', err);
        });
    });
});
</script>
@endpush
@endsection
