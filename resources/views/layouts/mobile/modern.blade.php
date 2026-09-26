<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="{{ $t['primary'] }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>@yield('title')</title>
    <link rel="icon" type="image/png" href="{{ $app_logo_url ?? asset('logo.png') }}?v={{ $general_setting?->updated_at?->timestamp ?? time() }}">
    <link rel="shortcut icon" href="{{ $app_logo_url ?? asset('favicon.ico') }}?v={{ $general_setting?->updated_at?->timestamp ?? time() }}">
    <link rel="apple-touch-icon" href="{{ $app_logo_url ?? asset('logo.png') }}?v={{ $general_setting?->updated_at?->timestamp ?? time() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.css" rel="stylesheet">
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>

    {{-- Template CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/template/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme-custom.css') }}?v={{ file_exists(public_path('assets/css/theme-custom.css')) ? filemtime(public_path('assets/css/theme-custom.css')) : time() }}" />

    {{-- Tailwind & App CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <style>
        :root {
            --color-nav: {{ $t['primary'] }};
            --color-nav-active: {{ $t['primary_light'] }};
            --bg-indicator: {{ $t['primary'] }};
            --color-nav-hover: {{ $t['primary_light'] }};
            --bg-nav: #ffffff;
            --theme-color-1: {{ $t['primary'] }};
            --theme-color-2: {{ $t['primary_light'] }};
            --theme-primary-contrast: {{ $t['primary_contrast'] ?? '#FFFFFF' }};
        }

        /* Dynamic Tailwind Arbitrary Hex Overrides */
        .bg-\[\#3C2A21\], .bg-\[\#3C2A21\] { background-color: var(--color-nav, {{ $t['primary'] }}) !important; color: var(--theme-primary-contrast, #FFFFFF) !important; }
        .text-\[\#3C2A21\], .text-\[\#3C2A21\] { color: var(--color-nav, {{ $t['primary'] }}) !important; }
        .border-\[\#3C2A21\], .border-\[\#3C2A21\] { border-color: var(--color-nav, {{ $t['primary'] }}) !important; }
        .focus\:ring-\[\#3C2A21\]:focus, .focus\:ring-\[\#3C2A21\]:focus { --tw-ring-color: var(--color-nav, {{ $t['primary'] }}) !important; }
        .focus\:border-\[\#3C2A21\]:focus, .focus\:border-\[\#3C2A21\]:focus { border-color: var(--color-nav, {{ $t['primary'] }}) !important; }

        .bg-\[\#634832\], .bg-\[\#634832\] { background-color: var(--color-nav-active, {{ $t['primary_light'] }}) !important; }
        .text-\[\#634832\], .text-\[\#634832\] { color: var(--color-nav-active, {{ $t['primary_light'] }}) !important; }
        .border-\[\#634832\], .border-\[\#634832\] { border-color: var(--color-nav-active, {{ $t['primary_light'] }}) !important; }
        html { background: {{ $t['primary'] }}; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif !important;
            background: {{ $t['bg_body'] }} !important;
            min-height: 100vh;
            -webkit-tap-highlight-color: transparent;
            color: #1e293b;
        }

        /* PWA Header handling */
        header { 
            padding-top: env(safe-area-inset-top);
            background: {{ $t['primary'] }};
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
        }

        /* Standardized DESIGN.md 12px Squircle Header Buttons */
        header .left a, header .right a {
            width: 34px !important;
            height: 34px !important;
            border-radius: 12px !important;
            background: rgba(255, 255, 255, 0.14) !important;
            color: #ffffff !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        header .left a:active, header .right a:active {
            transform: scale(0.92) !important;
            background: rgba(255, 255, 255, 0.25) !important;
        }

        /* Override template resets that conflict */
        @keyframes shimmer {
            0% { background-position: -400px 0; }
            100% { background-position: 400px 0; }
        }
        .sk {
            background: linear-gradient(90deg,
                #e2e8f0 0%,
                #f1f5f9 40%,
                #f8fafc 50%,
                #f1f5f9 60%,
                #e2e8f0 100%);
            background-size: 800px 100%;
            animation: shimmer 1.5s infinite linear;
        }

        .press { transition: transform 0.15s ease; }
        .press:active { transform: scale(0.97); }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp 0.3s ease forwards; opacity: 0; }
        ::-webkit-scrollbar { width: 0; height: 0; }
        
        /* Global skeleton styles */
        .skeleton-avatar { width: 45px; height: 45px; border-radius: 12px; }
        .skeleton-text { height: 12px; border-radius: 4px; }

        /* =========================================================
           GLOBAL MINIMALIST DESIGN SYSTEM (DESIGN.md & minimalist-ui)
           ========================================================= */
        .card, .presensi-card {
            border: 1px solid rgba(15, 23, 42, 0.08) !important;
            border-radius: 16px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            background: #ffffff !important;
        }
        .card:hover, .presensi-card:hover {
            border-color: var(--theme-border-hover, rgba(var(--bs-primary-rgb), 0.35)) !important;
        }

        .form-label-group {
            position: relative;
            margin-bottom: 12px;
            background: #ffffff !important;
            border: 1px solid rgba(15, 23, 42, 0.12) !important;
            border-radius: 14px !important;
            overflow: hidden;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
        }
        .form-label-group:focus-within {
            border-color: var(--color-nav, {{ $t['primary'] }}) !important;
            box-shadow: 0 0 0 3px var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.1)) !important;
        }

        /* Global Form Required & Optional Indicators */
        .req-star {
            color: #e11d48 !important;
            font-weight: 700 !important;
            margin-left: 2px !important;
        }
        .opt-tag {
            font-size: 11px !important;
            font-weight: 400 !important;
            color: #94a3b8 !important;
            margin-left: 4px !important;
        }
        .auto-tag {
            font-size: 10.5px !important;
            font-weight: 400 !important;
            color: #94a3b8 !important;
            margin-left: 4px !important;
        }
        .form-label-group input:focus ~ label .opt-tag,
        .form-label-group input:not(:placeholder-shown) ~ label .opt-tag,
        .form-label-group textarea:focus ~ label .opt-tag,
        .form-label-group textarea:not(:placeholder-shown) ~ label .opt-tag,
        .form-label-group select:focus ~ label .opt-tag,
        .form-label-group select:valid ~ label .opt-tag,
        .form-label-group input:focus ~ label .auto-tag,
        .form-label-group input:not(:placeholder-shown) ~ label .auto-tag {
            font-size: 9px !important;
            opacity: 0.85;
        }

        /* App Capsule Base & Bottom Clearance (antislop-layoutmobile R-03, R-35) */
        #appCapsule {
            padding-top: calc(56px + env(safe-area-inset-top, 0px) + 14px) !important;
            padding-bottom: calc(100px + env(safe-area-inset-bottom, 0px)) !important;
            min-height: 100vh;
            box-sizing: border-box !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        /* Prevent fixed bottomNav from obscuring form action buttons */
        .form-container {
            padding-bottom: calc(110px + env(safe-area-inset-bottom, 0px)) !important;
            box-sizing: border-box !important;
        }

        .air-datepicker-global-container { z-index: 100000 !important; }
        .air-datepicker-overlay { z-index: 99999 !important; background: rgba(15, 23, 42, 0.45) !important; }
        .air-datepicker { 
            z-index: 100001 !important; 
            font-family: 'Inter', -apple-system, sans-serif !important; 
            border-radius: 18px !important; 
            border: 1px solid rgba(15, 23, 42, 0.08) !important; 
            box-shadow: 0 20px 60px rgba(0,0,0,0.15) !important;
            --adp-accent-color: {{ $t['primary'] ?? '#3C2A21' }};
            --adp-cell-background-color-selected: {{ $t['primary'] ?? '#3C2A21' }};
            --adp-cell-background-color-selected-hover: {{ $t['primary'] ?? '#3C2A21' }};
            --adp-color-current-date: {{ $t['primary'] ?? '#3C2A21' }};
            --adp-btn-color: {{ $t['primary'] ?? '#3C2A21' }};
            --adp-cell-border-radius: 8px;
        }
        .air-datepicker-cell {
            border-radius: 8px !important;
        }
        .air-datepicker-cell.-selected-,
        .air-datepicker-cell.-selected-.-current-,
        .air-datepicker-cell.-selected-.-focus- { 
            background: {{ $t['primary'] ?? '#3C2A21' }} !important; 
            color: #ffffff !important; 
            font-weight: 700 !important; 
        }
        .air-datepicker-cell.-current-:not(.-selected-) { 
            color: {{ $t['primary'] ?? 'var(--theme-color-1)' }} !important; 
            font-weight: 700 !important; 
            background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)) !important;
        }
        .air-datepicker-button { 
            color: {{ $t['primary'] ?? 'var(--theme-color-1)' }} !important; 
            font-weight: 600 !important; 
        }
        .air-datepicker-button:hover {
            background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)) !important;
            color: {{ $t['primary'] ?? 'var(--theme-color-1)' }} !important;
        }

        /* SweetAlert2 Unified Cohesive Theme */
        .swal2-container {
            z-index: 99999 !important;
            background-color: rgba(15, 23, 42, 0.45) !important;
        }

        .swal2-popup {
            font-family: 'Inter', sans-serif !important;
            border-radius: 20px !important;
            padding: 24px 20px !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2) !important;
            background: #ffffff !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
        }

        .swal2-title {
            font-size: 19px !important;
            font-weight: 700 !important;
            color: #1e293b !important;
            margin-bottom: 8px !important;
        }

        .swal2-html-container {
            font-size: 14px !important;
            color: #64748b !important;
            line-height: 1.5 !important;
        }

        .swal2-actions {
            margin-top: 20px !important;
            gap: 10px !important;
        }

        .swal2-confirm {
            background-color: {{ $t['primary'] }} !important;
            border-color: {{ $t['primary'] }} !important;
            border-radius: 10px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            padding: 10px 24px !important;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08) !important;
            transition: background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease !important;
        }

        .swal2-confirm:hover, .swal2-confirm:focus {
            background-color: var(--color-primary-hover, {{ $t['primary_light'] ?? 'var(--theme-color-2)' }}) !important;
            border-color: var(--color-primary-hover, {{ $t['primary_light'] ?? 'var(--theme-color-2)' }}) !important;
            color: {{ $t['primary_contrast'] ?? '#FFFFFF' }} !important;
            box-shadow: 0 4px 12px rgba(var(--bs-primary-rgb, 15, 23, 42), 0.35) !important;
            transform: translateY(-1px) !important;
        }

        .swal2-cancel {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            padding: 10px 22px !important;
            box-shadow: none !important;
            transition: all 0.2s ease !important;
        }

        .swal2-cancel:hover, .swal2-cancel:focus {
            background-color: #e2e8f0 !important;
            color: #1e293b !important;
        }

        .swal2-deny {
            background-color: #ef4444 !important;
            border-color: #ef4444 !important;
            border-radius: 12px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            padding: 10px 24px !important;
            box-shadow: 0 4px 14px rgba(239, 68, 68, 0.3) !important;
        }

        .swal2-icon.swal2-success {
            border-color: {{ $t['primary'] }} !important;
            color: {{ $t['primary'] }} !important;
        }

        .swal2-icon.swal2-success [class^='swal2-success-line'] {
            background-color: {{ $t['primary'] }} !important;
        }

        .swal2-icon.swal2-success .swal2-success-ring {
            border-color: rgba(0, 0, 0, 0.15) !important;
        }
    </style>

    @stack('mystyle')
</head>
<body>
    <header style="padding-top: env(safe-area-inset-top); background: {{ $t['primary'] ?? '#3C2A21' }} !important; position: fixed; top: 0; left: 0; right: 0; z-index: 999; box-shadow: 0 2px 8px rgba(0,0,0,0.08);" class="appHeader-modern">
        <div class="flex items-center justify-between px-4 h-14" style="color: var(--theme-primary-contrast, #FFFFFF) !important;">
            <div class="left">
                @yield('header_left')
            </div>
            <h1 class="text-[14px] font-bold tracking-wide" style="color: var(--theme-primary-contrast, #FFFFFF) !important;">@yield('title')</h1>
            <div class="right w-8">
                @yield('header_right')
            </div>
        </div>
    </header>

    <main id="appCapsule" class="px-3 max-w-lg mx-auto">
        @yield('content')
    </main>

    @include('layouts.mobile.bottomNav')

    {{-- Core JS dependencies from old layout --}}
    <script src="{{ asset('assets/template/js/lib/popper.min.js') }}"></script>
    <script src="{{ asset('assets/template/js/lib/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/template/js/base.js') }}?v=2.0"></script>
    <script src="{{ asset('assets/external/js/sweetalert2@11.js') }}"></script>

    {{-- Universal GlobalSwal Helper & Toastr Bridge (antislop-ui Compliant) --}}
    <script>
        window.GlobalSwal = {
            toast: function(icon, message, title) {
                var p = "{{ $t['primary'] ?? '#3C2A21' }}";
                Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: function(toast) {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                }).fire({
                    icon: icon || 'success',
                    title: title ? (title + ': ' + message) : message
                });
            },
            success: function(message, title) {
                Swal.fire({
                    icon: 'success',
                    title: title || 'Berhasil!',
                    text: message,
                    confirmButtonColor: "{{ $t['primary'] ?? '#3C2A21' }}",
                    confirmButtonText: 'Selesai',
                    timer: 2500,
                    timerProgressBar: true
                });
            },
            error: function(message, title) {
                Swal.fire({
                    icon: 'error',
                    title: title || 'Gagal',
                    html: message,
                    confirmButtonColor: "{{ $t['primary'] ?? '#3C2A21' }}",
                    confirmButtonText: 'Tutup'
                });
            },
            warning: function(message, title) {
                Swal.fire({
                    icon: 'warning',
                    title: title || 'Peringatan',
                    text: message,
                    confirmButtonColor: "{{ $t['primary'] ?? '#3C2A21' }}",
                    confirmButtonText: 'Mengerti'
                });
            }
        };

        // Universal Toastr Bridge
        window.toastr = {
            options: {},
            success: function(msg, title) { window.GlobalSwal.toast('success', msg, title); },
            error: function(msg, title) { window.GlobalSwal.error(msg, title); },
            warning: function(msg, title) { window.GlobalSwal.toast('warning', msg, title); },
            info: function(msg, title) { window.GlobalSwal.toast('info', msg, title); }
        };
    </script>

    @if ($message = Session::get('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.GlobalSwal.success(@json($message));
            });
        </script>
    @endif

    @if ($message = Session::get('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.GlobalSwal.error(@json($message));
            });
        </script>
    @endif

    @if ($message = Session::get('warning'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.GlobalSwal.warning(@json($message));
            });
        </script>
    @endif

    @if (isset($errors) && $errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.GlobalSwal.warning(@json(implode("\n", $errors->all())), 'Periksa Formulir');
            });
        </script>
    @endif



    @stack('myscript')

    <!-- Global Action Loading Overlay -->
    @include('components.global-loading')

    <!-- Global Help Drawer -->
    @include('layouts.help_drawer')
</body>
</html>
