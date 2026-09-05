<!-- Core CSS -->
<link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-semi-dark.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

<!-- Essential Vendors CSS -->
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/css/toastr.min.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />

<!-- Dynamic Theme Variables -->
<style>
    :root {
        --theme-color-1: {{ $general_setting->theme_color_1 ?? '#1E4D3E' }};
        --theme-color-2: {{ $general_setting->theme_color_2 ?? '#32745E' }};
        --theme-canvas: #EEF2F0;
        --theme-surface: #FFFFFF;
        --theme-text-primary: #0F172A;
        --theme-text-secondary: #64748B;
        --theme-border: #E2E8F0;
        --theme-border-hover: #CBD5E1;
        --bs-primary: var(--theme-color-1);
        --bs-primary-rgb: 30, 77, 62;
    }

    /* Flatpickr Calendar Fix: always appear above modals and sticky headers */
    .flatpickr-calendar {
        z-index: 99999 !important;
    }
    .input-group:has(.flatpickr-date, [datepicker="flatpickr-date"], .flatpickr-input) .input-group-text,
    .input-group-text:has(.ti-calendar, .ti-clock) {
        cursor: pointer !important;
        user-select: none;
    }

    /* Toastr Notification: display cleanly below the floating navbar */
    #toast-container {
        top: 78px !important;
        z-index: 999999 !important;
    }
</style>

<!-- Theme Custom & Minimalist Design System CSS (Cached) -->
<link rel="stylesheet" href="{{ asset('assets/css/theme-custom.css') }}" />

@stack('mystyle')

