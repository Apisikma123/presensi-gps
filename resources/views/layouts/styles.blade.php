<!-- Core CSS -->
<link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}?v={{ config('app.asset_version', '2.5.0') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-semi-dark.css') }}?v={{ config('app.asset_version', '2.5.0') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

<!-- Essential Vendors CSS -->
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/css/toastr.min.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/external/css/cropper.min.css') }}" />

@php
    $theme = \App\Services\ThemeResolver::resolve();
    $theme1 = $theme['primary'] ?? '#1A5276';
    $theme2 = $theme['secondary'] ?? '#2980b9';
    $themeAccent = $theme['accent'] ?? '#2980b9';
    $primaryRgb = $theme['primary_rgb'] ?? '26, 82, 118';
    $secondaryRgb = $theme['secondary_rgb'] ?? '41, 128, 185';
    $primaryContrast = $theme['primary_contrast'] ?? '#FFFFFF';
    $primaryHover = $theme['primary_hover'] ?? '#154360';
    $primarySoft = $theme['primary_soft'] ?? 'rgba(var(--bs-primary-rgb, 26, 82, 118), 0.08)';
    $primaryBorder = $theme['primary_border'] ?? 'rgba(var(--bs-primary-rgb, 26, 82, 118), 0.18)';
@endphp
<!-- Universal Dynamic Theme Variables & Anti-Slop System -->
<style>
    :root {
        --color-primary: {{ $theme1 }};
        --color-primary-hover: {{ $primaryHover }};
        --color-primary-soft: {{ $primarySoft }};
        --color-primary-contrast: {{ $primaryContrast }};
        --color-secondary: {{ $theme2 }};
        --theme-color-1: {{ $theme1 }};
        --theme-color-2: {{ $theme2 }};
        --theme-color-accent: {{ $themeAccent }};
        --theme-primary-contrast: {{ $primaryContrast }};
        --theme-canvas: {{ $theme['canvas'] ?? '#F8FAFC' }};
        --theme-surface: {{ $theme['surface'] ?? '#FFFFFF' }};
        --theme-text-primary: {{ $theme['text_primary'] ?? '#0F172A' }};
        --theme-text-secondary: {{ $theme['text_secondary'] ?? '#475569' }};
        --theme-border: {{ $theme['border'] ?? '#E2E8F0' }};
        --theme-border-hover: {{ $theme['border_hover'] ?? '#CBD5E1' }};
        --bs-primary: var(--theme-color-1);
        --bs-primary-rgb: {{ $primaryRgb }};
        --theme-color-2-rgb: {{ $secondaryRgb }};
        --bs-secondary: var(--theme-color-2);
        --bs-purple: var(--theme-color-1);
        --bs-link-color: var(--theme-color-1);
        --bs-link-hover-color: var(--theme-color-2);
        --bs-primary-text-emphasis: var(--theme-color-1);
        --bs-primary-bg-subtle: {{ $primarySoft }};
        --bs-primary-border-subtle: {{ $primaryBorder }};

        /* Dynamic Sidebar Custom Properties */
        --sidebar-bg: {{ $theme['sidebar_bg'] ?? '#1E293B' }};
        --sidebar-sub-bg: {{ $theme['sidebar_sub_bg'] ?? '#0F172A' }};
        --sidebar-active-bg: var(--color-primary);
        --sidebar-active-color: var(--theme-primary-contrast, #FFFFFF);
        --sidebar-text: {{ $theme['sidebar_text'] ?? '#94A3B8' }};
        --sidebar-header: {{ $theme['sidebar_header'] ?? '#64748B' }};
        --sidebar-border: {{ $theme['sidebar_border'] ?? 'rgba(255, 255, 255, 0.08)' }};

        /* Enterprise Motion Tokens (DESIGN.md - Swiss Precision & Tactile Feedback) */
        --motion-duration-micro: 120ms;
        --motion-duration-fast: 160ms;
        --motion-duration-normal: 200ms;
        --motion-duration-modal: 200ms;
        --motion-ease-out: cubic-bezier(0.16, 1, 0.3, 1);
        --motion-ease-in-out: cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>

<!-- Custom Anti-Slop & Enterprise Design System CSS (Browser Cached) -->
<link rel="stylesheet" href="{{ asset('assets/css/app-custom.css') }}?v={{ config('app.asset_version', '2.5.0') }}" />

<!-- Theme Custom & Minimalist Design System CSS (Browser Cached) -->
<link rel="stylesheet" href="{{ asset('assets/css/theme-custom.css') }}?v={{ config('app.asset_version', '2.5.0') }}" />

<!-- Global Mobile Responsive CSS (Browser Cached) -->
<link rel="stylesheet" href="{{ asset('assets/css/mobile-responsive.css') }}?v={{ config('app.asset_version', '2.5.0') }}" />

@stack('mystyle')
