<!-- Core CSS -->
<link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}?v={{ file_exists(public_path('assets/vendor/css/core.css')) ? filemtime(public_path('assets/vendor/css/core.css')) : time() }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-semi-dark.css') }}?v={{ file_exists(public_path('assets/vendor/css/theme-semi-dark.css')) ? filemtime(public_path('assets/vendor/css/theme-semi-dark.css')) : time() }}" />
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
    $theme1 = $theme['primary'] ?? '#3C2A21';
    $theme2 = $theme['secondary'] ?? '#634832';
    $themeAccent = $theme['accent'] ?? '#4A6741';
    $primaryRgb = $theme['primary_rgb'] ?? '60, 42, 33';
    $secondaryRgb = $theme['secondary_rgb'] ?? '99, 72, 50';
    $primaryContrast = $theme['primary_contrast'] ?? '#FFFFFF';
    $primaryHover = $theme['primary_hover'] ?? '#2A1D17';
    $primarySoft = $theme['primary_soft'] ?? 'rgba(60, 42, 33, 0.08)';
    $primaryBorder = $theme['primary_border'] ?? 'rgba(60, 42, 33, 0.18)';
@endphp
<!-- Universal Dynamic Theme Variables & Anti-Slop System -->
<style>
    :root {
        --color-primary: {{ $theme1 }};
        --color-primary-hover: {{ $primaryHover }};
        --color-primary-soft: {{ $primarySoft }};
        --color-primary-contrast: {{ $primaryContrast }};
        --theme-color-1: {{ $theme1 }};
        --theme-color-2: {{ $theme2 }};
        --theme-color-accent: {{ $themeAccent }};
        --theme-primary-contrast: {{ $primaryContrast }};
        --theme-canvas: {{ $theme['canvas'] ?? '#FAF9F8' }};
        --theme-surface: {{ $theme['surface'] ?? '#FFFFFF' }};
        --theme-text-primary: {{ $theme['text_primary'] ?? '#1A1C1C' }};
        --theme-text-secondary: {{ $theme['text_secondary'] ?? '#755841' }};
        --theme-border: {{ $theme['border'] ?? 'rgba(' . $primaryRgb . ', 0.08)' }};
        --theme-border-hover: {{ $theme['border_hover'] ?? 'rgba(' . $primaryRgb . ', 0.16)' }};
        --bs-primary: var(--theme-color-1);
        --bs-primary-rgb: {{ $primaryRgb }};
        --theme-color-2-rgb: {{ $secondaryRgb }};
        --bs-purple: var(--theme-color-1);
        --bs-link-color: var(--theme-color-1);
        --bs-link-hover-color: var(--theme-color-2);
        --bs-primary-text-emphasis: var(--theme-color-1);
        --bs-primary-bg-subtle: {{ $primarySoft }};
        --bs-primary-border-subtle: {{ $primaryBorder }};

        /* Dynamic Sidebar Custom Properties */
        --sidebar-bg: {{ $theme['sidebar_bg'] ?? '#25160E' }};
        --sidebar-sub-bg: {{ $theme['sidebar_sub_bg'] ?? '#1C1009' }};
        --sidebar-active-bg: var(--color-primary);
        --sidebar-active-color: var(--theme-primary-contrast, #FFFFFF);
        --sidebar-text: {{ $theme['sidebar_text'] ?? '#D3C3BD' }};
        --sidebar-header: {{ $theme['sidebar_header'] ?? '#AA9084' }};
        --sidebar-border: {{ $theme['sidebar_border'] ?? 'rgba(' . $primaryRgb . ', 0.15)' }};
    }

    /* Universal Typography Rules (DESIGN.md Swiss Precision) */
    html, body, .layout-wrapper, .layout-container {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
    }
    h1, h2, h3, h4, h5, h6, .card-title, .page-title, .modal-title, .swal2-title {
        font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif !important;
        letter-spacing: -0.01em;
    }
    .font-mono, .metric-value, .stat-value, .attendance-card-time, .rekap-metric-val, .data-mono, table td.font-mono, table td.numeric {
        font-family: 'JetBrains Mono', 'SF Mono', monospace !important;
        font-variant-numeric: tabular-nums !important;
    }

    /* Dynamic Button and Controls with Auto-Contrast */
    .btn-primary,
    .btn-primary:focus {
        background-color: var(--theme-color-1) !important;
        border-color: var(--theme-color-1) !important;
        color: var(--theme-primary-contrast, #FFFFFF) !important;
    }
    .btn-primary:hover,
    .btn-primary:active {
        background-color: var(--color-primary-hover, var(--theme-color-2)) !important;
        border-color: var(--color-primary-hover, var(--theme-color-2)) !important;
        color: var(--theme-primary-contrast, #FFFFFF) !important;
    }
    .btn-outline-primary {
        color: var(--theme-color-1) !important;
        border-color: var(--theme-color-1) !important;
    }
    .btn-outline-primary:hover {
        background-color: var(--theme-color-1) !important;
        border-color: var(--theme-color-1) !important;
        color: var(--theme-primary-contrast, #FFFFFF) !important;
    }
    .swal2-confirm.btn-primary,
    .swal2-styled.swal2-confirm {
        background-color: var(--theme-color-1) !important;
        border-color: var(--theme-color-1) !important;
        color: var(--theme-primary-contrast, #FFFFFF) !important;
    }
    .swal2-confirm.btn-primary:hover,
    .swal2-styled.swal2-confirm:hover,
    .swal2-confirm.btn-primary:focus,
    .swal2-styled.swal2-confirm:focus {
        background-color: var(--color-primary-hover, var(--theme-color-2)) !important;
        border-color: var(--color-primary-hover, var(--theme-color-2)) !important;
        color: var(--theme-primary-contrast, #FFFFFF) !important;
    }
    .form-control:focus,
    .form-select:focus {
        border-color: var(--theme-color-1) !important;
        box-shadow: 0 0 0 0.25rem var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.15)) !important;
    }
    .page-item.active .page-link {
        background-color: var(--theme-color-1) !important;
        border-color: var(--theme-color-1) !important;
        color: var(--theme-primary-contrast, #FFFFFF) !important;
    }

    /* Permanent suppression of any dark offcanvas backdrop to keep page 100% bright and clickable */
    .offcanvas-backdrop,
    .offcanvas-backdrop.fade,
    .offcanvas-backdrop.show {
        display: none !important;
        opacity: 0 !important;
        pointer-events: none !important;
        visibility: hidden !important;
        width: 0 !important;
        height: 0 !important;
    }

    /* Flatpickr Calendar Fix: always appear above modals and sticky headers */
    .flatpickr-calendar {
        z-index: 99999 !important;
    }

    /* Sidebar Scroll Containment & Instant Placement (Zero Shift) */
    #layout-menu .menu-inner {
        overscroll-behavior: contain !important;
        scroll-behavior: auto !important;
    }

    /* ==========================================================================
       DYNAMIC ENTERPRISE NAVIGATION (Zero Hardcode Settings Resolver)
       ========================================================================== */
    #layout-menu,
    .layout-menu,
    .bg-menu-theme {
        background-color: var(--sidebar-bg) !important;
        background: var(--sidebar-bg) !important;
        color: var(--sidebar-text) !important;
        border-right: 1px solid var(--sidebar-border) !important;
    }
    .bg-menu-theme .menu-link,
    .bg-menu-theme .menu-horizontal-prev,
    .bg-menu-theme .menu-horizontal-next {
        color: var(--sidebar-text) !important;
    }
    .bg-menu-theme .menu-link:hover,
    .bg-menu-theme .menu-link:focus {
        color: #FFFFFF !important;
        background-color: rgba(255, 255, 255, 0.08) !important;
    }
    .bg-menu-theme .menu-header {
        color: var(--sidebar-header) !important;
    }
    .bg-menu-theme .menu-inner-shadow {
        background: linear-gradient(var(--sidebar-bg) 41%, rgba(0, 0, 0, 0.11) 95%, rgba(0, 0, 0, 0)) !important;
    }
    .bg-menu-theme .menu-inner .menu-item.open > .menu-link.menu-toggle {
        background: rgba(255, 255, 255, 0.06) !important;
        color: #FFFFFF !important;
    }
    .bg-menu-theme .menu-inner > .menu-item.open > .menu-sub {
        background: var(--sidebar-sub-bg) !important;
    }
    .bg-menu-theme .menu-inner .menu-sub .menu-item:not(.active) > .menu-link:hover {
        background: rgba(255, 255, 255, 0.05) !important;
        color: #FFFFFF !important;
    }
    .bg-menu-theme.menu-vertical .menu-item.active > .menu-link:not(.menu-toggle),
    .bg-menu-theme .menu-item.active > .menu-link {
        background: var(--sidebar-active-bg) !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25) !important;
        color: var(--sidebar-active-color) !important;
    }
    .bg-menu-theme .menu-inner .menu-sub .menu-item.active > .menu-link {
        color: var(--sidebar-active-bg) !important;
        font-weight: 700 !important;
    }
    .bg-menu-theme .menu-inner .menu-sub .menu-item.active > .menu-link::before {
        background-color: var(--sidebar-active-bg) !important;
        border-color: var(--sidebar-active-bg) !important;
    }

    /* ==========================================================================
       BREWSYNC ENTERPRISE GLOBAL COMPONENTS (DESIGN.md)
       ========================================================================== */
    /* Cards */
    .card {
        background-color: #FFFFFF !important;
        border: 1px solid var(--theme-border, rgba(60, 42, 33, 0.08)) !important;
        border-radius: 14px !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
    }
    .card:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04) !important;
    }
    .card-header {
        border-bottom: 1px solid var(--theme-border, rgba(60, 42, 33, 0.06)) !important;
        color: #1A1C1C !important;
    }

    /* Modern Table Header */
    table thead, thead, .table thead th, thead th {
        background: #F4F3F2 !important;
        background-color: #F4F3F2 !important;
        color: #4F4540 !important;
        border-bottom: 1px solid var(--theme-border, rgba(60, 42, 33, 0.08)) !important;
    }
    table thead th.text-white, thead th.text-white {
        color: #4F4540 !important;
    }
    table tbody tr:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.03) !important;
    }

    /* Form Controls */
    .form-check-input:checked {
        background-color: var(--color-primary) !important;
        border-color: var(--color-primary) !important;
    }

    /* Pagination */
    .page-item.active .page-link {
        background-color: var(--color-primary) !important;
        border-color: var(--color-primary) !important;
        color: var(--theme-primary-contrast, #FFFFFF) !important;
        box-shadow: 0 2px 4px rgba(var(--bs-primary-rgb), 0.2) !important;
    }
    .page-link {
        color: var(--color-primary) !important;
    }
    .page-link:hover {
        color: var(--theme-color-2) !important;
        background-color: #F4F3F2 !important;
    }

    /* Badges */
    .badge.bg-primary {
        background-color: var(--color-primary) !important;
        color: var(--theme-primary-contrast, #FFFFFF) !important;
    }
    .badge.bg-label-primary {
        background-color: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)) !important;
        color: var(--color-primary) !important;
    }
    .badge.bg-success {
        background-color: #4A6741 !important;
        color: #FFFFFF !important;
    }
    .badge.bg-label-success {
        background-color: rgba(74, 103, 65, 0.1) !important;
        color: #4A6741 !important;
    }
    .badge.bg-warning {
        background-color: #B45309 !important;
        color: #FFFFFF !important;
    }
    .badge.bg-label-warning {
        background-color: rgba(180, 83, 9, 0.1) !important;
        color: #B45309 !important;
    }
    .badge.bg-danger {
        background-color: #BA1A1A !important;
        color: #FFFFFF !important;
    }
    .badge.bg-label-danger {
        background-color: rgba(186, 26, 26, 0.1) !important;
        color: #BA1A1A !important;
    }

    /* Nav Tabs & Pills */
    .nav-pills .nav-link.active {
        background-color: var(--color-primary) !important;
        color: var(--theme-primary-contrast, #FFFFFF) !important;
    }
    .nav-tabs .nav-link.active {
        border-bottom-color: var(--color-primary) !important;
        color: var(--color-primary) !important;
    }
    /* ==========================================================================
       GLOBAL SEAMLESS SEARCH & INPUT GROUPS (UNIVERSAL ADMIN PANEL)
       (Completely eliminates double lines, inner borders, and vertical divider lines)
       ========================================================================== */
    .admin-filter-toolbar .input-group,
    .navbar-search-box,
    .input-group.seamless-search,
    .input-group:has(.ti-search),
    .input-group:has(i[class*="search"]) {
        border: 1px solid #CBD5E1 !important;
        border-radius: 8px !important;
        background: #FFFFFF !important;
        overflow: hidden !important;
        box-shadow: none !important;
        display: flex !important;
        align-items: stretch !important;
        width: 100% !important;
        transition: border-color 0.18s ease, box-shadow 0.18s ease !important;
    }

    .admin-filter-toolbar .flex-grow-1 {
        flex: 1 1 240px !important;
        min-width: 200px !important;
    }

    .admin-filter-toolbar .input-group:focus-within,
    .navbar-search-box:focus-within,
    .input-group.seamless-search:focus-within,
    .input-group:has(.ti-search):focus-within,
    .input-group:has(i[class*="search"]):focus-within {
        border-color: var(--color-primary) !important;
        box-shadow: 0 0 0 3px rgba(var(--bs-primary-rgb), 0.14) !important;
    }

    /* ALL child elements inside search input groups MUST have zero borders, zero shadows, transparent bg */
    .admin-filter-toolbar .input-group > *,
    .admin-filter-toolbar .input-group .input-group-text,
    .admin-filter-toolbar .input-group .form-control,
    .admin-filter-toolbar .input-group input,
    .navbar-search-box > *,
    .navbar-search-box .input-group-text,
    .navbar-search-box .form-control,
    .input-group.seamless-search > *,
    .input-group.seamless-search .input-group-text,
    .input-group.seamless-search .form-control,
    .input-group:has(.ti-search) > *,
    .input-group:has(.ti-search) .input-group-text,
    .input-group:has(.ti-search) .form-control,
    .input-group:has(i[class*="search"]) > *,
    .input-group:has(i[class*="search"]) .input-group-text,
    .input-group:has(i[class*="search"]) .form-control {
        border: 0 !important;
        border-left: 0 !important;
        border-right: 0 !important;
        border-top: 0 !important;
        border-bottom: 0 !important;
        border-color: transparent !important;
        box-shadow: none !important;
        outline: none !important;
        background: transparent !important;
        background-color: transparent !important;
        border-radius: 0 !important;
        margin: 0 !important;
    }

    /* Input Focus State: STRICTLY NO INNER BORDER / NO INNER SHADOW */
    .admin-filter-toolbar .input-group input:focus,
    .admin-filter-toolbar .input-group .form-control:focus,
    .navbar-search-box input:focus,
    .navbar-search-box .form-control:focus,
    .input-group.seamless-search input:focus,
    .input-group.seamless-search .form-control:focus,
    .input-group:has(.ti-search) input:focus,
    .input-group:has(.ti-search) .form-control:focus,
    .input-group:has(i[class*="search"]) input:focus,
    .input-group:has(i[class*="search"]) .form-control:focus {
        border: 0 !important;
        border-left: 0 !important;
        border-right: 0 !important;
        border-top: 0 !important;
        border-bottom: 0 !important;
        border-color: transparent !important;
        box-shadow: none !important;
        outline: none !important;
        --tw-ring-shadow: none !important;
        --tw-ring-offset-shadow: none !important;
        z-index: 1 !important;
    }

    /* Icon padding & Input padding */
    .admin-filter-toolbar .input-group .input-group-text,
    .input-group:has(.ti-search) .input-group-text,
    .input-group:has(i[class*="search"]) .input-group-text {
        padding-left: 11px !important;
        padding-right: 5px !important;
        color: #64748B !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .admin-filter-toolbar .input-group .form-control,
    .admin-filter-toolbar .input-group input,
    .input-group:has(.ti-search) .form-control,
    .input-group:has(i[class*="search"]) .form-control {
        padding-left: 3px !important;
        padding-right: 12px !important;
    }


    /* ==========================================================================
       MATERIAL DESIGN TIME PICKER (GLOBAL FLATPICKR TIME STYLE)
       ========================================================================== */
    .flatpickr-calendar.noCalendar {
        width: 280px !important;
        background: #FFFFFF !important;
        border-radius: 16px !important;
        box-shadow: 0 16px 40px -6px rgba(15, 23, 42, 0.18), 0 0 0 1px rgba(15, 23, 42, 0.08) !important;
        border: none !important;
        padding: 14px 16px 14px 16px !important;
        box-sizing: border-box !important;
        z-index: 999999 !important;
        overflow: hidden !important;
    }

    .flatpickr-calendar.noCalendar::before {
        content: "PILIH WAKTU" !important;
        display: block !important;
        text-align: center !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        letter-spacing: 1px !important;
        text-transform: uppercase !important;
        color: #475569 !important;
        padding-bottom: 8px !important;
        margin-bottom: 8px !important;
        border-bottom: 1px solid rgba(15, 23, 42, 0.08) !important;
    }

    .flatpickr-calendar.noCalendar .flatpickr-time {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        height: auto !important;
        max-height: none !important;
        line-height: normal !important;
        background: transparent !important;
        border: none !important;
        padding: 4px 0 !important;
        box-shadow: none !important;
    }

    /* Digital display boxes for Hour & Minute (Generous 88x84px with Vertical Top/Bottom Stepper) */
    .flatpickr-calendar.noCalendar .flatpickr-time .numInputWrapper {
        flex: 0 0 88px !important;
        width: 88px !important;
        height: 84px !important;
        background: #F8FAFC !important;
        border: 1.5px solid #E2E8F0 !important;
        border-radius: 12px !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        position: relative !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        overflow: hidden !important;
    }

    .flatpickr-calendar.noCalendar .flatpickr-time .numInputWrapper:hover,
    .flatpickr-calendar.noCalendar .flatpickr-time .numInputWrapper:focus-within {
        background: #FFFFFF !important;
        border-color: var(--bs-primary, #3C2A21) !important;
        box-shadow: 0 0 0 3px rgba(60, 42, 33, 0.12) !important;
    }

    /* Numbers centered cleanly in the middle between top and bottom arrows */
    .flatpickr-calendar.noCalendar .flatpickr-time input.numInput {
        width: 100% !important;
        height: 38px !important;
        line-height: 38px !important;
        font-size: 30px !important;
        font-weight: 700 !important;
        font-family: 'JetBrains Mono', 'SF Mono', 'Roboto Mono', Menlo, monospace !important;
        color: #0F172A !important;
        text-align: center !important;
        background: transparent !important;
        border: none !important;
        outline: none !important;
        padding: 0 !important;
        margin: 0 !important;
        cursor: default !important;
        pointer-events: none !important;
        user-select: none !important;
        box-shadow: none !important;
        z-index: 1 !important;
    }

    /* Colon Separator */
    .flatpickr-calendar.noCalendar .flatpickr-time-separator {
        flex: 0 0 24px !important;
        width: 24px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 32px !important;
        font-weight: 800 !important;
        color: var(--bs-primary, #3C2A21) !important;
        line-height: 84px !important;
        user-select: none !important;
        height: 84px !important;
    }

    /* Up / Down Arrow Controls: Placed Centered at TOP and BOTTOM - ZERO NUMBER OVERLAP */
    .flatpickr-calendar.noCalendar .numInputWrapper span.arrowUp,
    .flatpickr-calendar.noCalendar .numInputWrapper span.arrowDown {
        position: absolute !important;
        left: 4px !important;
        right: 4px !important;
        width: calc(100% - 8px) !important;
        height: 20px !important;
        padding: 0 !important;
        margin: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border: none !important;
        background: rgba(15, 23, 42, 0.05) !important;
        border-radius: 6px !important;
        cursor: pointer !important;
        z-index: 5 !important;
        opacity: 0.8 !important;
        transition: all 0.15s ease !important;
    }

    .flatpickr-calendar.noCalendar .numInputWrapper span.arrowUp {
        top: 3px !important;
        bottom: auto !important;
    }

    .flatpickr-calendar.noCalendar .numInputWrapper span.arrowDown {
        bottom: 3px !important;
        top: auto !important;
    }

    .flatpickr-calendar.noCalendar .numInputWrapper span svg {
        display: none !important;
    }

    .flatpickr-calendar.noCalendar .numInputWrapper span.arrowUp::after {
        position: static !important;
        display: inline-block !important;
        content: "" !important;
        border-left: 5px solid transparent !important;
        border-right: 5px solid transparent !important;
        border-bottom: 5px solid #475569 !important;
        border-top: none !important;
        margin: 0 !important;
    }

    .flatpickr-calendar.noCalendar .numInputWrapper span.arrowDown::after {
        position: static !important;
        display: inline-block !important;
        content: "" !important;
        border-left: 5px solid transparent !important;
        border-right: 5px solid transparent !important;
        border-top: 5px solid #475569 !important;
        border-bottom: none !important;
        margin: 0 !important;
    }

    .flatpickr-calendar.noCalendar .numInputWrapper span.arrowUp:hover,
    .flatpickr-calendar.noCalendar .numInputWrapper span.arrowDown:hover {
        background: var(--bs-primary, #3C2A21) !important;
        opacity: 1 !important;
    }

    .flatpickr-calendar.noCalendar .numInputWrapper span.arrowUp:hover::after {
        border-bottom-color: #FFFFFF !important;
    }

    .flatpickr-calendar.noCalendar .numInputWrapper span.arrowDown:hover::after {
        border-top-color: #FFFFFF !important;
    }

    /* Toastr Notification: display cleanly below the floating navbar */
    #toast-container {
        top: 78px !important;
        z-index: 999999 !important;
    }

    /* ==========================================================================
       GLOBAL PRIMARY COLOR & BUTTON STATES (PURPLE ERADICATION)
       ========================================================================== */
    .btn-primary,
    button.btn-primary,
    input[type="submit"].btn-primary,
    button[type="submit"]:not(.btn-secondary):not(.btn-danger):not(.btn-warning):not(.btn-info):not(.btn-success):not(.btn-dark):not(.btn-light) {
        background-color: var(--bs-primary, #3C2A21) !important;
        border-color: var(--bs-primary, #3C2A21) !important;
        color: #FFFFFF !important;
    }

    .btn-primary:hover,
    .btn-primary:focus,
    .btn-primary.focus,
    button.btn-primary:hover,
    button.btn-primary:focus {
        background-color: var(--color-primary-hover, var(--theme-color-2)) !important;
        border-color: var(--color-primary-hover, var(--theme-color-2)) !important;
        color: var(--theme-primary-contrast, #FFFFFF) !important;
    }

    .btn-check:checked + .btn-primary,
    .btn-check:active + .btn-primary,
    .btn-primary:active,
    .btn-primary.active,
    .show > .btn-primary.dropdown-toggle {
        background-color: var(--color-primary-hover, var(--theme-color-2)) !important;
        border-color: var(--color-primary-hover, var(--theme-color-2)) !important;
        color: var(--theme-primary-contrast, #FFFFFF) !important;
    }

    /* Disabled & Saving State - Absolutely NO Purple (#7367f0) */
    .btn-primary.disabled,
    .btn-primary:disabled,
    button:disabled.btn-primary,
    button[type="submit"]:disabled,
    .btn:disabled.btn-primary,
    .btn.disabled.btn-primary {
        background-color: var(--bs-primary, #3C2A21) !important;
        border-color: var(--bs-primary, #3C2A21) !important;
        color: #FFFFFF !important;
        opacity: 0.75 !important;
        box-shadow: none !important;
        cursor: not-allowed !important;
    }

    /* Button Group Borders */
    .btn-group .btn-primary,
    .input-group .btn-primary {
        border-right: var(--bs-border-width, 1px) solid var(--theme-color-2, #634832) !important;
        border-left: var(--bs-border-width, 1px) solid var(--theme-color-2, #634832) !important;
    }
    .btn-group-vertical .btn-primary {
        border-top-color: var(--theme-color-2, #634832) !important;
        border-bottom-color: var(--theme-color-2, #634832) !important;
    }

    /* Outline Primary */
    .btn-outline-primary {
        color: var(--bs-primary, #3C2A21) !important;
        border-color: var(--bs-primary, #3C2A21) !important;
        background: transparent !important;
    }
    .btn-outline-primary:hover,
    .btn-outline-primary:focus,
    .btn-outline-primary:active,
    .btn-outline-primary.active {
        background-color: var(--bs-primary, #3C2A21) !important;
        border-color: var(--bs-primary, #3C2A21) !important;
        color: #FFFFFF !important;
    }
    .btn-outline-primary.disabled,
    .btn-outline-primary:disabled {
        color: var(--bs-primary, #3C2A21) !important;
        border-color: var(--bs-primary, #3C2A21) !important;
        background-color: transparent !important;
        opacity: 0.6 !important;
    }

    /* Label Primary */
    .btn-label-primary {
        color: var(--bs-primary, #3C2A21) !important;
        background-color: rgba(var(--bs-primary-rgb, 60, 42, 33), 0.1) !important;
        border-color: transparent !important;
    }
    .btn-label-primary:hover,
    .btn-label-primary:focus,
    .btn-label-primary:active {
        background-color: rgba(var(--bs-primary-rgb, 60, 42, 33), 0.18) !important;
        color: var(--bs-primary, #3C2A21) !important;
    }
    .btn-label-primary.disabled,
    .btn-label-primary:disabled {
        color: var(--bs-primary, #3C2A21) !important;
        background-color: rgba(var(--bs-primary-rgb, 60, 42, 33), 0.08) !important;
        border-color: transparent !important;
        opacity: 0.7 !important;
    }

    /* Spinners & Loading State */
    .spinner-border.text-primary,
    .spinner-grow.text-primary {
        color: var(--bs-primary, #3C2A21) !important;
    }
    .btn-primary .spinner-border,
    .btn-primary .spinner-grow {
        color: #FFFFFF !important;
    }

    /* Spinkit Loaders (Vuexy sk-primary) */
    .sk-primary.sk-plane,
    .sk-primary .sk-chase-dot:before,
    .sk-primary .sk-bounce-dot,
    .sk-primary .sk-wave-rect,
    .sk-primary.sk-pulse,
    .sk-primary .sk-swing-dot,
    .sk-primary .sk-circle-dot:before,
    .sk-primary .sk-circle-fade-dot:before,
    .sk-primary .sk-grid-cube,
    .sk-primary .sk-fold-cube:before {
        background-color: var(--bs-primary, #3C2A21) !important;
    }

    /* ==========================================================================
       SELECT2 EMERALD THEME (ZERO PURPLE GUARANTEE)
       ========================================================================== */
    .select2-container--default.select2-container--focus .select2-selection,
    .select2-container--default.select2-container--open .select2-selection {
        border-color: var(--bs-primary, #3C2A21) !important;
        box-shadow: 0 0 0 3px rgba(var(--bs-primary-rgb, 60, 42, 33), 0.12) !important;
    }
    .select2-dropdown {
        border-color: rgba(var(--bs-primary-rgb, 60, 42, 33), 0.25) !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: var(--bs-primary, #3C2A21) !important;
        box-shadow: 0 0 0 2px rgba(var(--bs-primary-rgb, 60, 42, 33), 0.15) !important;
        outline: none !important;
    }
    /* Selected option resting in dropdown */
    .select2-results__option[aria-selected="true"],
    .select2-results__option[role=option][aria-selected=true],
    .select2-container--default .select2-results__option[aria-selected=true],
    .select2-container--default .select2-results__option--selected {
        background-color: var(--bs-primary, #3C2A21) !important;
        color: #FFFFFF !important;
    }
    /* Highlighted / Hovered option */
    .select2-container--default .select2-results__option--highlighted,
    .select2-container--default .select2-results__option--highlighted[aria-selected],
    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable,
    .select2-results__option--highlighted {
        background-color: var(--bs-primary, #3C2A21) !important;
        color: #FFFFFF !important;
    }
    /* Highlighted but not currently selected */
    .select2-container--default .select2-results__option--highlighted:not([aria-selected=true]),
    .select2-container--default .select2-results__option--highlighted[aria-selected="false"] {
        background-color: rgba(var(--bs-primary-rgb, 60, 42, 33), 0.12) !important;
        color: var(--bs-primary, #3C2A21) !important;
    }
    /* Highlighted AND selected (darker feedback) */
    .select2-container--default .select2-results__option--highlighted[aria-selected="true"] {
        background-color: var(--color-primary-hover, var(--theme-color-2)) !important;
        color: var(--theme-primary-contrast, #FFFFFF) !important;
    }
    /* Multi-select tag choice badges */
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: rgba(var(--bs-primary-rgb, 60, 42, 33), 0.12) !important;
        color: var(--bs-primary, #3C2A21) !important;
        border: 1px solid rgba(var(--bs-primary-rgb, 60, 42, 33), 0.2) !important;
    }

    /* Instant Admin Modal Transitions (Zero Sluggish Delay) */
    .modal.fade {
        transition: opacity 0.08s linear !important;
    }
    .modal.fade .modal-dialog {
        transition: transform 0.08s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    /* Modern Skeleton Loader for Fast Form Loading (Zero Ugly Wave Spinner) */
    .modal-skeleton {
        padding: 0.5rem 0.25rem;
    }
    .modal-skeleton .placeholder {
        display: block;
        background: linear-gradient(90deg, rgba(60, 42, 33, 0.05) 25%, rgba(60, 42, 33, 0.11) 50%, rgba(60, 42, 33, 0.05) 75%);
        background-size: 200% 100%;
        animation: modalSkeletonGlow 1.2s ease-in-out infinite;
        border-radius: 6px;
    }
    /* ==========================================================================
       GLOBAL SWEETALERT2 THEME PALETTE (PRIMARY & SECONDARY FROM SETTING)
       ========================================================================== */
    .swal2-popup {
        border-radius: 14px !important;
        font-family: inherit !important;
    }
    .swal2-title {
        color: var(--theme-color-1, #3C2A21) !important;
        font-weight: 700 !important;
    }
    .swal2-actions {
        margin-top: 1.25rem !important;
        margin-bottom: 0.25rem !important;
        gap: 8px !important;
    }
    .swal2-confirm:not(.btn-danger):not(.bg-danger) {
        background: var(--theme-color-1, #3C2A21) !important;
        background-color: var(--theme-color-1, #3C2A21) !important;
        background-image: none !important;
        border: 1px solid var(--color-primary-hover, var(--theme-color-2, #25160E)) !important;
        color: var(--theme-primary-contrast, #FFFFFF) !important;
        font-weight: 600 !important;
        font-size: 13.5px !important;
        letter-spacing: 0.01em !important;
        min-width: 110px !important;
        padding: 9px 24px !important;
        border-radius: 8px !important;
        box-shadow: 0 1px 2px rgba(var(--bs-primary-rgb, 15, 23, 42), 0.08) !important;
        transition: background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease !important;
    }
    .swal2-confirm:not(.btn-danger):not(.bg-danger):hover,
    .swal2-confirm:not(.btn-danger):not(.bg-danger):focus {
        background: var(--color-primary-hover, var(--theme-color-2)) !important;
        background-color: var(--color-primary-hover, var(--theme-color-2)) !important;
        background-image: none !important;
        border-color: var(--color-primary-hover, var(--theme-color-2)) !important;
        color: var(--theme-primary-contrast, #FFFFFF) !important;
        box-shadow: 0 4px 12px rgba(var(--bs-primary-rgb, 15, 23, 42), 0.35) !important;
        transform: translateY(-1px) !important;
    }
    .swal2-confirm:not(.btn-danger):not(.bg-danger):active {
        background: var(--color-primary-hover, var(--theme-color-2)) !important;
        background-color: var(--color-primary-hover, var(--theme-color-2)) !important;
        background-image: none !important;
        border-color: var(--color-primary-hover, var(--theme-color-2)) !important;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.2) !important;
        transform: none !important;
    }
    .swal2-confirm:not(.btn-danger):not(.bg-danger):focus-visible {
        outline: 2px solid var(--theme-color-1, #3C2A21) !important;
        outline-offset: 2px !important;
    }
    .swal2-cancel {
        background: #FFFFFF !important;
        background-color: #FFFFFF !important;
        border: 1px solid #CBD5E1 !important;
        color: #475569 !important;
        border-radius: 8px !important;
        font-weight: 500 !important;
        font-size: 13.5px !important;
        padding: 9px 20px !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05) !important;
        transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease !important;
    }
    .swal2-cancel:hover {
        background: #F4F3F2 !important;
        background-color: #F4F3F2 !important;
        border-color: #94A3B8 !important;
        color: #0F172A !important;
    }
    .swal2-icon.swal2-success {
        border-color: var(--theme-color-accent, #4A6741) !important;
        color: var(--theme-color-accent, #4A6741) !important;
    }
    .swal2-icon.swal2-success [class^='swal2-success-line'] {
        background-color: var(--theme-color-accent, #4A6741) !important;
    }
    .swal2-icon.swal2-success .swal2-success-ring {
        border-color: rgba(74, 103, 65, 0.3) !important;
    }
    .swal2-icon.swal2-info {
        border-color: var(--theme-color-1, #3C2A21) !important;
        color: var(--theme-color-1, #3C2A21) !important;
    }

    /* Export Excel Button & Elements */
    .btn-export-excel {
        border-color: #CBD5E1 !important;
        color: #1E293B !important;
        background: #FFFFFF !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05) !important;
        transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease, box-shadow 0.15s ease !important;
    }
    .btn-export-excel:hover {
        background: #F4F3F2 !important;
        background-image: none !important;
        border-color: var(--theme-color-1, #3C2A21) !important;
        color: var(--theme-color-1, #3C2A21) !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08) !important;
        transform: none !important;
    }
    .export-file-badge {
        background: rgba(60, 42, 33, 0.05) !important;
        border: 1px dashed var(--theme-color-2, #634832) !important;
        color: var(--theme-color-1, #3C2A21) !important;
    }
    .export-loading-spinner {
        border-color: var(--theme-color-1, #3C2A21) !important;
        border-right-color: transparent !important;
    }

    /* Admin Global Dynamic Overrides for Hardcoded Residuals */
    .stat-chip-icon {
        background: rgba(var(--bs-primary-rgb), 0.1) !important;
        color: var(--theme-color-1, #3C2A21) !important;
    }
    .emp-marker-pin {
        border-color: var(--theme-color-1, #3C2A21) !important;
    }
    .marker-cluster-small .cluster-inner,
    .marker-cluster-medium .cluster-inner,
    .marker-cluster-large .cluster-inner {
        background: linear-gradient(135deg, var(--theme-color-1, #3C2A21), var(--theme-color-2, #634832)) !important;
    }
    #imageModal .modal-header {
        background: var(--theme-color-1, #3C2A21) !important;
    }
    #imageModal #downloadImage {
        background: var(--theme-color-1, #3C2A21) !important;
    }

    /* ==========================================================================
       PRESENCE UNIVERSAL HR - GLOBAL BREADCRUMB SYSTEM (DESIGN.md & Antislop)
       ========================================================================== */
    .admin-breadcrumb-nav {
        margin-bottom: 0.85rem;
    }

    .breadcrumb,
    .admin-breadcrumb {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        list-style: none !important;
        list-style-type: none !important;
        padding: 0 !important;
        margin: 0 !important;
        gap: 0 !important;
        background: transparent !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
        font-size: 13px !important;
        line-height: 1.5 !important;
    }

    .breadcrumb li,
    .breadcrumb .breadcrumb-item,
    .admin-breadcrumb li,
    .admin-breadcrumb .breadcrumb-item {
        display: inline-flex !important;
        align-items: center !important;
        list-style: none !important;
        list-style-type: none !important;
        margin: 0 !important;
        padding: 0 !important;
        color: #64748B !important;
        font-size: 13px !important;
    }

    .breadcrumb li::marker,
    .breadcrumb .breadcrumb-item::marker,
    .admin-breadcrumb li::marker,
    .admin-breadcrumb .breadcrumb-item::marker {
        display: none !important;
        content: "" !important;
        font-size: 0 !important;
    }

    .breadcrumb .breadcrumb-item + .breadcrumb-item,
    .admin-breadcrumb .breadcrumb-item + .breadcrumb-item {
        padding-left: 0 !important;
    }

    .breadcrumb .breadcrumb-item + .breadcrumb-item::before,
    .admin-breadcrumb .breadcrumb-item + .breadcrumb-item::before {
        content: "›" !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        float: none !important;
        padding: 0 8px !important;
        color: #94A3B8 !important;
        font-size: 14px !important;
        font-weight: 500 !important;
        line-height: 1 !important;
        opacity: 0.85;
    }

    .breadcrumb .breadcrumb-item a,
    .admin-breadcrumb .breadcrumb-item a {
        color: #64748B !important;
        text-decoration: none !important;
        font-weight: 500 !important;
        transition: color 0.15s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
    }

    .breadcrumb .breadcrumb-item a:hover,
    .admin-breadcrumb .breadcrumb-item a:hover {
        color: var(--theme-color-1, #3C2A21) !important;
    }

    .breadcrumb .breadcrumb-item.active,
    .admin-breadcrumb .breadcrumb-item.active,
    .breadcrumb .breadcrumb-item:last-child,
    .admin-breadcrumb .breadcrumb-item:last-child {
        color: #1A1C1C !important;
        font-weight: 600 !important;
    }

    /* Fallback for legacy views outputting spans */
    .admin-breadcrumb > span,
    .admin-breadcrumb > a {
        display: inline-flex !important;
        align-items: center !important;
        color: #64748B !important;
        font-size: 13px !important;
    }
</style>

<!-- Theme Custom & Minimalist Design System CSS (Auto Cache-Busting) -->
<link rel="stylesheet" href="{{ asset('assets/css/theme-custom.css') }}?v={{ file_exists(public_path('assets/css/theme-custom.css')) ? filemtime(public_path('assets/css/theme-custom.css')) : time() }}" />

<!-- Global Mobile Responsive CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/mobile-responsive.css') }}?v={{ file_exists(public_path('assets/css/mobile-responsive.css')) ? filemtime(public_path('assets/css/mobile-responsive.css')) : time() }}" />

@stack('mystyle')

