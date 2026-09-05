<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="{{ $t['primary'] }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>@yield('title')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.css" rel="stylesheet">
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>

    {{-- Template CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/template/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/toastr.min.css') }}" />

    {{-- Tailwind & App CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <style>
        :root {
            --color-nav: {{ $t['primary'] }};
            --color-nav-active: {{ $t['primary_light'] }};
            --bg-indicator: {{ $t['primary'] }};
            --color-nav-hover: {{ $t['primary_light'] }};
            --bg-nav: #ffffff;
        }
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
            border-color: rgba(30, 77, 62, 0.35) !important;
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
            border-color: #1E4D3E !important;
            box-shadow: 0 0 0 3px rgba(30, 77, 62, 0.1) !important;
        }

        .air-datepicker-global-container { z-index: 100000 !important; }
        .air-datepicker-overlay { z-index: 99999 !important; backdrop-filter: blur(2px) !important; -webkit-backdrop-filter: blur(2px) !important; }
        .air-datepicker { z-index: 100001 !important; font-family: 'Inter', sans-serif !important; border-radius: 16px !important; border: none !important; box-shadow: 0 20px 60px rgba(0,0,0,0.15) !important; }
        .air-datepicker-cell.-selected- { background: {{ $t['primary'] }} !important; }
        .air-datepicker-cell.-current- { color: {{ $t['primary'] }} !important; }
        .air-datepicker-button { color: {{ $t['primary'] }} !important; }

        /* SweetAlert2 Unified Cohesive Theme */
        .swal2-container {
            z-index: 99999 !important;
            backdrop-filter: blur(4px) !important;
            -webkit-backdrop-filter: blur(4px) !important;
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
            border-radius: 12px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            padding: 10px 24px !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15) !important;
            transition: all 0.2s ease !important;
        }

        .swal2-confirm:hover, .swal2-confirm:focus {
            opacity: 0.9 !important;
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
    <header style="padding-top: env(safe-area-inset-top); background: {{ $t['primary'] ?? '#1E4D3E' }} !important; position: fixed; top: 0; left: 0; right: 0; z-index: 999; box-shadow: 0 2px 8px rgba(0,0,0,0.08);" class="appHeader-modern">
        <div class="flex items-center justify-between px-4 h-14">
            <div class="left">
                @yield('header_left')
            </div>
            <h1 class="text-[14px] font-bold text-white tracking-wide">@yield('title')</h1>
            <div class="right w-8">
                @yield('header_right')
            </div>
        </div>
    </header>

    <main id="appCapsule" class="pt-[calc(4rem+env(safe-area-inset-top))] pb-24 px-3 max-w-lg mx-auto">
        @yield('content')
    </main>

    @include('layouts.mobile.bottomNav')

    {{-- Core JS dependencies from old layout --}}
    <script src="{{ asset('assets/template/js/lib/popper.min.js') }}"></script>
    <script src="{{ asset('assets/template/js/lib/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/template/js/base.js') }}?v=2.0"></script>
    <script src="{{ asset('assets/vendor/libs/toastr/toastr.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js" defer></script>

    {{-- Session Flash Notifications --}}
    <style>.toast-bottom-full-width { bottom: 5rem }</style>
    @if ($message = Session::get('success'))
        <script>
            toastr.options.showEasing = 'swing'; toastr.options.hideEasing = 'linear';
            toastr.options.progressBar = true; toastr.options.positionClass = 'toast-bottom-full-width';
            toastr.success("Berhasil", "{{ $message }}", { timeOut: 3000 });
        </script>
    @endif
    @if ($message = Session::get('error'))
        <script>
            toastr.options.showEasing = 'swing'; toastr.options.hideEasing = 'linear';
            toastr.options.progressBar = true; toastr.options.positionClass = 'toast-bottom-full-width';
            toastr.error("Gagal", "{{ $message }}", { timeOut: 3000 });
        </script>
    @endif
    @if ($message = Session::get('warning'))
        <script>
            toastr.options.showEasing = 'swing'; toastr.options.hideEasing = 'linear';
            toastr.options.progressBar = true;
            toastr.warning("Warning", "{{ $message }}", { timeOut: 3000 });
        </script>
    @endif
    @if (isset($errors) && $errors->any())
        @php $err = ''; @endphp
        @foreach ($errors->all() as $error) @php $err .= $error . ' '; @endphp @endforeach
        <script>
            toastr.options.showEasing = 'swing'; toastr.options.hideEasing = 'linear';
            toastr.options.progressBar = true;
            toastr.error("Gagal", "{{ addslashes(trim($err)) }}", { timeOut: 3000 });
        </script>
    @endif



    @stack('myscript')
</body>
</html>
