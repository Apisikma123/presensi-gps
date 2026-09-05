<!doctype html>
<html lang="en">

@php
    $scheme = $general_setting->mobile_theme_scheme ?? 'green';
    $colors = [
        'green' => [
            'bg_body' => '#dff9fb',
            'bg_nav' => '#ffffff',
            'color_nav' => '#32745e',
            'color_nav_active' => '#58907D',
            'bg_indicator' => '#32745e',
            'color_nav_hover' => '#3ab58c',
        ],
        'blue' => [
            'bg_body' => '#e3f2fd',
            'bg_nav' => '#ffffff',
            'color_nav' => '#0d47a1',
            'color_nav_active' => '#1976d2',
            'bg_indicator' => '#0d47a1',
            'color_nav_hover' => '#2196f3',
        ],
        'red' => [
            'bg_body' => '#ffebee',
            'bg_nav' => '#ffffff',
            'color_nav' => '#b71c1c',
            'color_nav_active' => '#d32f2f',
            'bg_indicator' => '#b71c1c',
            'color_nav_hover' => '#ef5350',
        ],
        'purple' => [
            'bg_body' => '#f3e5f5',
            'bg_nav' => '#ffffff',
            'color_nav' => '#4a148c',
            'color_nav_active' => '#7b1fa2',
            'bg_indicator' => '#4a148c',
            'color_nav_hover' => '#ab47bc',
        ],
        'orange' => [
            'bg_body' => '#fff3e0',
            'bg_nav' => '#ffffff',
            'color_nav' => '#e65100',
            'color_nav_active' => '#f57c00',
            'bg_indicator' => '#e65100',
            'color_nav_hover' => '#ff9800',
        ],
        'rose' => [
            'bg_body' => '#fff5f7',
            'bg_nav' => '#ffffff',
            'color_nav' => '#ce8291',
            'color_nav_active' => '#ef95a6',
            'bg_indicator' => '#ce8291',
            'color_nav_hover' => '#ef95a6',
        ],
        'dark' => [
            'bg_body' => '#121212',
            'bg_nav' => '#1e1e1e',
            'color_nav' => '#e0e0e0', // Light text
            'color_nav_active' => '#bb86fc', // Purple accent
            'bg_indicator' => '#bb86fc',
            'color_nav_hover' => '#cf6679',
        ],
    ];
    $c = $colors[$scheme] ?? $colors['green'];

    if (!function_exists('hexToRgb')) {
        function hexToRgb($hex)
        {
            $hex = str_replace('#', '', $hex);
            if (strlen($hex) == 3) {
                $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
                $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
                $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
            } else {
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
            }
            return "$r, $g, $b";
        }
    }
@endphp
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="{{ $c['color_nav'] }}">
    <title>{{ $general_setting->nama_aplikasi ?? 'Dashboard' }}</title>
    <meta name="description" content="Mobilekit HTML Mobile UI Kit">
    <meta name="keywords" content="bootstrap 4, mobile template, cordova, phonegap, mobile, html" />

    <!-- DNS Prefetch untuk external resources -->
    <link rel="dns-prefetch" href="https://ajax.googleapis.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://unpkg.com">
    <link rel="dns-prefetch" href="https://cdn.amcharts.com">

    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}" sizes="32x32">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/template/css/style.css') }}">

    <link rel="manifest" href="{{ asset('manifest.json') }}?v={{ file_exists(public_path('manifest.json')) ? filemtime(public_path('manifest.json')) : time() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />


    <style>
        :root {
            --bg-body: {{ $c['bg_body'] }};
            --bg-nav: {{ $c['bg_nav'] }};
            --color-nav: {{ $c['color_nav'] }};
            --color-nav-rgb: {{ hexToRgb($c['color_nav']) }};
            --color-nav-active: {{ $c['color_nav_active'] }};
            --color-nav-active-rgb: {{ hexToRgb($c['color_nav_active']) }};
            --bg-indicator: {{ $c['bg_indicator'] }};
            --color-nav-hover: {{ $c['color_nav_hover'] }};
        }

        /* Apply background to body if needed, currently set in :root usually consumed by body style */
        body {
            background-color: var(--bg-body);
        }

        /* Smart scroll fix: Hanya apply jika ada #content-section (halaman dengan header fixed) */
        #content-section {
            height: calc(100vh - 70px - 80px);
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Dynamic Theme Overrides */
        .bg-primary {
            background-color: var(--color-nav) !important;
        }

        .btn-primary {
            background-color: var(--color-nav) !important;
            border-color: var(--color-nav) !important;
        }

        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active {
            background-color: var(--color-nav-active) !important;
            border-color: var(--color-nav-active) !important;
        }

        .text-primary {
            color: var(--color-nav) !important;
        }

        .historicontent {
            display: flex;
            justify-content: space-between;
            padding: 20px
        }

        .historibordergreen {
            border: 1px solid var(--color-nav) !important;
        }

        .historiborderred {
            border: 1px solid rgb(171, 18, 18);
        }

        /* FAB Button Overrides */
        .fab-button .fab {
            background-color: var(--color-nav) !important;
        }

        .fab-button .fab:hover,
        .fab-button .fab:active {
            background-color: var(--color-nav-active) !important;
        }

        .fab-button .dropdown-menu .dropdown-item {
            background-color: var(--color-nav) !important;
        }

        .fab-button .dropdown-menu .dropdown-item:hover,
        .fab-button .dropdown-menu .dropdown-item:active {
            background-color: var(--color-nav-active) !important;
        }

        /* Nav Tabs Overrides */
        .nav-tabs.style1 .nav-item .nav-link.active {
            color: var(--color-nav) !important;
        }

        .nav-tabs.style1 .nav-item .nav-link {
            color: var(--color-nav);
            opacity: 0.7;
        }

        /* Card Text Override */
        .card-body {
            color: var(--color-nav);
        }

        .historidetail1 {
            display: flex;
        }

        .historidetail2 h4 {
            margin-bottom: 0;
        }



        .datepresence {
            margin-left: 10px;
        }

        .datepresence h4 {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 0;
        }

        .timepresence {
            font-size: 14px;
        }


        /* SweetAlert2 Unified Cohesive Theme */
        .swal2-container {
            z-index: 99999 !important;
            backdrop-filter: blur(4px) !important;
            -webkit-backdrop-filter: blur(4px) !important;
        }

        .swal2-popup {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
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
            background-color: #32745e !important;
            border-color: #32745e !important;
            border-radius: 12px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            padding: 10px 24px !important;
            box-shadow: 0 4px 14px rgba(50, 116, 94, 0.35) !important;
            transition: all 0.2s ease !important;
        }

        .swal2-confirm:hover, .swal2-confirm:focus {
            background-color: #265a49 !important;
            border-color: #265a49 !important;
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
            border-color: #32745e !important;
            color: #32745e !important;
        }

        .swal2-icon.swal2-success [class^='swal2-success-line'] {
            background-color: #32745e !important;
        }

        .swal2-icon.swal2-success .swal2-success-ring {
            border-color: rgba(50, 116, 94, 0.3) !important;
        }
    </style>
    {{-- <style>
        .selectmaterialize,
        textarea {
            display: block;
            background-color: transparent !important;
            border: 0px !important;
            border-bottom: 1px solid #9e9e9e !important;
            border-radius: 0 !important;
            outline: none !important;
            height: 3rem !important;
            width: 100% !important;
            font-size: 16px !important;
            margin: 0 0 8px 0 !important;
            padding: 0 !important;
            color: #495057;

        }

        textarea {
            height: 80px !important;
            color: #495057 !important;
            padding: 20px !important;
        }
    </style> --}}
</head>

<body>



    @yield('header')

    <!-- App Capsule -->
    <div id="appCapsule">
        @yield('content')
    </div>
    <!-- * App Capsule -->


    @include('layouts.mobile.bottomNav')


    @include('layouts.mobile.script')

    <!-- Global Preloader Component -->
    @include('layouts.global_preloader')

</body>

</html>
