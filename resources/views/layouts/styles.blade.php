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
    $theme1 = $general_setting->theme_color_1 ?? '#1E4D3E';
    $theme2 = $general_setting->theme_color_2 ?? '#32745E';
    $h = ltrim($theme1, '#');
    if (strlen($h) === 3) {
        $r = hexdec($h[0].$h[0]); $g = hexdec($h[1].$h[1]); $b = hexdec($h[2].$h[2]);
    } elseif (strlen($h) === 6) {
        $r = hexdec(substr($h, 0, 2)); $g = hexdec(substr($h, 2, 2)); $b = hexdec(substr($h, 4, 2));
    } else {
        $r = 30; $g = 77; $b = 62;
    }
    $primaryRgb = "$r, $g, $b";

    $h2 = ltrim($theme2, '#');
    if (strlen($h2) === 3) {
        $r2 = hexdec($h2[0].$h2[0]); $g2 = hexdec($h2[1].$h2[1]); $b2 = hexdec($h2[2].$h2[2]);
    } elseif (strlen($h2) === 6) {
        $r2 = hexdec(substr($h2, 0, 2)); $g2 = hexdec(substr($h2, 2, 2)); $b2 = hexdec(substr($h2, 4, 2));
    } else {
        $r2 = 50; $g2 = 116; $b2 = 94;
    }
    $secondaryRgb = "$r2, $g2, $b2";
@endphp
<!-- Dynamic Theme Variables -->
<style>
    :root {
        --theme-color-1: {{ $theme1 }};
        --theme-color-2: {{ $theme2 }};
        --theme-canvas: #EEF2F0;
        --theme-surface: #FFFFFF;
        --theme-text-primary: #0F172A;
        --theme-text-secondary: #64748B;
        --theme-border: #E2E8F0;
        --theme-border-hover: #CBD5E1;
        --bs-primary: var(--theme-color-1);
        --bs-primary-rgb: {{ $primaryRgb }};
        --theme-color-2-rgb: {{ $secondaryRgb }};
        --bs-purple: var(--theme-color-1);
        --bs-link-color: var(--theme-color-1);
        --bs-link-hover-color: var(--theme-color-2);
        --bs-primary-text-emphasis: var(--theme-color-1);
        --bs-primary-bg-subtle: rgba({{ $primaryRgb }}, 0.1);
        --bs-primary-border-subtle: rgba({{ $primaryRgb }}, 0.25);
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

    /* Sidebar Smooth Scroll & Containment */
    #layout-menu .menu-inner {
        overscroll-behavior: contain !important;
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
        transition: border-color 0.18s ease, box-shadow 0.18s ease !important;
    }

    .admin-filter-toolbar .input-group:focus-within,
    .navbar-search-box:focus-within,
    .input-group.seamless-search:focus-within,
    .input-group:has(.ti-search):focus-within,
    .input-group:has(i[class*="search"]):focus-within {
        border-color: #1E4D3E !important;
        box-shadow: 0 0 0 3px rgba(30, 77, 62, 0.14) !important;
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
        border-color: var(--bs-primary, #1E4D3E) !important;
        box-shadow: 0 0 0 3px rgba(30, 77, 62, 0.12) !important;
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
        color: var(--bs-primary, #1E4D3E) !important;
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
        background: var(--bs-primary, #1E4D3E) !important;
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
        background-color: var(--bs-primary, #1E4D3E) !important;
        border-color: var(--bs-primary, #1E4D3E) !important;
        color: #FFFFFF !important;
    }

    .btn-primary:hover,
    .btn-primary:focus,
    .btn-primary.focus,
    button.btn-primary:hover,
    button.btn-primary:focus {
        background-color: var(--theme-color-2, #163A2F) !important;
        border-color: var(--theme-color-2, #163A2F) !important;
        color: #FFFFFF !important;
    }

    .btn-check:checked + .btn-primary,
    .btn-check:active + .btn-primary,
    .btn-primary:active,
    .btn-primary.active,
    .show > .btn-primary.dropdown-toggle {
        background-color: var(--theme-color-2, #163A2F) !important;
        border-color: var(--theme-color-2, #163A2F) !important;
        color: #FFFFFF !important;
    }

    /* Disabled & Saving State - Absolutely NO Purple (#7367f0) */
    .btn-primary.disabled,
    .btn-primary:disabled,
    button:disabled.btn-primary,
    button[type="submit"]:disabled,
    .btn:disabled.btn-primary,
    .btn.disabled.btn-primary {
        background-color: var(--bs-primary, #1E4D3E) !important;
        border-color: var(--bs-primary, #1E4D3E) !important;
        color: #FFFFFF !important;
        opacity: 0.75 !important;
        box-shadow: none !important;
        cursor: not-allowed !important;
    }

    /* Button Group Borders */
    .btn-group .btn-primary,
    .input-group .btn-primary {
        border-right: var(--bs-border-width, 1px) solid var(--theme-color-2, #163A2F) !important;
        border-left: var(--bs-border-width, 1px) solid var(--theme-color-2, #163A2F) !important;
    }
    .btn-group-vertical .btn-primary {
        border-top-color: var(--theme-color-2, #163A2F) !important;
        border-bottom-color: var(--theme-color-2, #163A2F) !important;
    }

    /* Outline Primary */
    .btn-outline-primary {
        color: var(--bs-primary, #1E4D3E) !important;
        border-color: var(--bs-primary, #1E4D3E) !important;
        background: transparent !important;
    }
    .btn-outline-primary:hover,
    .btn-outline-primary:focus,
    .btn-outline-primary:active,
    .btn-outline-primary.active {
        background-color: var(--bs-primary, #1E4D3E) !important;
        border-color: var(--bs-primary, #1E4D3E) !important;
        color: #FFFFFF !important;
    }
    .btn-outline-primary.disabled,
    .btn-outline-primary:disabled {
        color: var(--bs-primary, #1E4D3E) !important;
        border-color: var(--bs-primary, #1E4D3E) !important;
        background-color: transparent !important;
        opacity: 0.6 !important;
    }

    /* Label Primary */
    .btn-label-primary {
        color: var(--bs-primary, #1E4D3E) !important;
        background-color: rgba(30, 77, 62, 0.1) !important;
        border-color: transparent !important;
    }
    .btn-label-primary:hover,
    .btn-label-primary:focus,
    .btn-label-primary:active {
        background-color: rgba(30, 77, 62, 0.18) !important;
        color: var(--bs-primary, #1E4D3E) !important;
    }
    .btn-label-primary.disabled,
    .btn-label-primary:disabled {
        color: var(--bs-primary, #1E4D3E) !important;
        background-color: rgba(30, 77, 62, 0.08) !important;
        border-color: transparent !important;
        opacity: 0.7 !important;
    }

    /* Spinners & Loading State */
    .spinner-border.text-primary,
    .spinner-grow.text-primary {
        color: var(--bs-primary, #1E4D3E) !important;
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
        background-color: var(--bs-primary, #1E4D3E) !important;
    }

    /* ==========================================================================
       SELECT2 EMERALD THEME (ZERO PURPLE GUARANTEE)
       ========================================================================== */
    .select2-container--default.select2-container--focus .select2-selection,
    .select2-container--default.select2-container--open .select2-selection {
        border-color: var(--bs-primary, #1E4D3E) !important;
        box-shadow: 0 0 0 3px rgba(30, 77, 62, 0.12) !important;
    }
    .select2-dropdown {
        border-color: rgba(30, 77, 62, 0.25) !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: var(--bs-primary, #1E4D3E) !important;
        box-shadow: 0 0 0 2px rgba(30, 77, 62, 0.15) !important;
        outline: none !important;
    }
    /* Selected option resting in dropdown */
    .select2-results__option[aria-selected="true"],
    .select2-results__option[role=option][aria-selected=true],
    .select2-container--default .select2-results__option[aria-selected=true],
    .select2-container--default .select2-results__option--selected {
        background-color: var(--bs-primary, #1E4D3E) !important;
        color: #FFFFFF !important;
    }
    /* Highlighted / Hovered option */
    .select2-container--default .select2-results__option--highlighted,
    .select2-container--default .select2-results__option--highlighted[aria-selected],
    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable,
    .select2-results__option--highlighted {
        background-color: var(--bs-primary, #1E4D3E) !important;
        color: #FFFFFF !important;
    }
    /* Highlighted but not currently selected */
    .select2-container--default .select2-results__option--highlighted:not([aria-selected=true]),
    .select2-container--default .select2-results__option--highlighted[aria-selected="false"] {
        background-color: rgba(30, 77, 62, 0.12) !important;
        color: var(--bs-primary, #1E4D3E) !important;
    }
    /* Highlighted AND selected (darker feedback) */
    .select2-container--default .select2-results__option--highlighted[aria-selected="true"] {
        background-color: var(--theme-color-2, #163A2F) !important;
        color: #FFFFFF !important;
    }
    /* Multi-select tag choice badges */
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: rgba(30, 77, 62, 0.12) !important;
        color: var(--bs-primary, #1E4D3E) !important;
        border: 1px solid rgba(30, 77, 62, 0.2) !important;
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
        background: linear-gradient(90deg, rgba(30, 77, 62, 0.05) 25%, rgba(30, 77, 62, 0.11) 50%, rgba(30, 77, 62, 0.05) 75%);
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
        color: var(--theme-color-1, #1E4D3E) !important;
        font-weight: 700 !important;
    }
    .swal2-confirm:not(.btn-danger):not(.bg-danger) {
        background: linear-gradient(135deg, var(--theme-color-1, #1E4D3E), var(--theme-color-2, #32745E)) !important;
        border-color: var(--theme-color-1, #1E4D3E) !important;
        color: #FFFFFF !important;
        font-weight: 600 !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 12px rgba(30, 77, 62, 0.25) !important;
        transition: all 0.2s ease !important;
    }
    .swal2-confirm:not(.btn-danger):not(.bg-danger):hover {
        background: linear-gradient(135deg, var(--theme-color-2, #32745E), var(--theme-color-1, #1E4D3E)) !important;
        box-shadow: 0 6px 16px rgba(30, 77, 62, 0.35) !important;
        transform: translateY(-1px);
    }
    .swal2-cancel {
        background: #F1F5F9 !important;
        border-color: #E2E8F0 !important;
        color: #475569 !important;
        border-radius: 8px !important;
        font-weight: 500 !important;
    }
    .swal2-cancel:hover {
        background: #E2E8F0 !important;
        color: #1E293B !important;
    }
    .swal2-icon.swal2-success {
        border-color: var(--theme-color-2, #32745E) !important;
        color: var(--theme-color-2, #32745E) !important;
    }
    .swal2-icon.swal2-success [class^='swal2-success-line'] {
        background-color: var(--theme-color-2, #32745E) !important;
    }
    .swal2-icon.swal2-success .swal2-success-ring {
        border-color: rgba(50, 116, 94, 0.3) !important;
    }
    .swal2-icon.swal2-info {
        border-color: var(--theme-color-1, #1E4D3E) !important;
        color: var(--theme-color-1, #1E4D3E) !important;
    }

    /* Export Excel Button & Elements */
    .btn-export-excel {
        border-color: var(--theme-color-2, #32745E) !important;
        color: var(--theme-color-1, #1E4D3E) !important;
        background: #FFFFFF !important;
        transition: all 0.2s ease-in-out !important;
    }
    .btn-export-excel:hover {
        background: linear-gradient(135deg, var(--theme-color-1, #1E4D3E), var(--theme-color-2, #32745E)) !important;
        border-color: var(--theme-color-1, #1E4D3E) !important;
        color: #FFFFFF !important;
        box-shadow: 0 4px 12px rgba(30, 77, 62, 0.25) !important;
    }
    .export-file-badge {
        background: rgba(30, 77, 62, 0.05) !important;
        border: 1px dashed var(--theme-color-2, #32745E) !important;
        color: var(--theme-color-1, #1E4D3E) !important;
    }
    .export-loading-spinner {
        border-color: var(--theme-color-1, #1E4D3E) !important;
        border-right-color: transparent !important;
    }

    /* Admin Global Dynamic Overrides for Hardcoded Residuals */
    .stat-chip-icon {
        background: rgba(var(--bs-primary-rgb), 0.1) !important;
        color: var(--theme-color-1, #1E4D3E) !important;
    }
    .emp-marker-pin {
        border-color: var(--theme-color-1, #1E4D3E) !important;
    }
    .marker-cluster-small .cluster-inner,
    .marker-cluster-medium .cluster-inner,
    .marker-cluster-large .cluster-inner {
        background: linear-gradient(135deg, var(--theme-color-1, #1E4D3E), var(--theme-color-2, #32745E)) !important;
    }
    #imageModal .modal-header {
        background: var(--theme-color-1, #1E4D3E) !important;
    }
    #imageModal #downloadImage {
        background: var(--theme-color-1, #1E4D3E) !important;
    }
</style>

<!-- Theme Custom & Minimalist Design System CSS (Auto Cache-Busting) -->
<link rel="stylesheet" href="{{ asset('assets/css/theme-custom.css') }}?v={{ file_exists(public_path('assets/css/theme-custom.css')) ? filemtime(public_path('assets/css/theme-custom.css')) : time() }}" />

<!-- Global Mobile Responsive CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/mobile-responsive.css') }}?v={{ file_exists(public_path('assets/css/mobile-responsive.css')) ? filemtime(public_path('assets/css/mobile-responsive.css')) : time() }}" />

@stack('mystyle')

