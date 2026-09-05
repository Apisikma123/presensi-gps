<!DOCTYPE html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-wide" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('/assets/') }}" data-template="vertical-menu-template-no-customizer"
    style="background-color: #EEF2F0; color-scheme: light;">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <style>
        /* Instant anti-flash inline background before any external CSS is parsed */
        html, body, .layout-wrapper, .layout-container, .layout-page, .content-wrapper {
            background-color: #EEF2F0 !important;
        }
    </style>

    <title>@yield('titlepage') | {{ $general_setting->nama_aplikasi ?? '' }}</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('logo.png') }}" />

    <!-- DNS Prefetch for external resources -->
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://unpkg.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

    @include('layouts.fonts')

    @include('layouts.icons')

    @include('layouts.styles')

    <!-- Tailwind & Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Helpers (Deferred to eliminate render-blocking parser pause) -->
    <script defer src="{{ asset('/assets/vendor/js/helpers.js') }}"></script>
    <script defer src="{{ asset('/assets/js/config.js') }}"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA Meta Tags -->
    <meta name="application-name" content="{{ $general_setting->nama_aplikasi ?? 'E-Presensi GPS V2' }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ $general_setting->nama_aplikasi ?? 'E-Presensi' }}">
    <meta name="description" content="Aplikasi {{ $general_setting->nama_aplikasi ?? 'Presensi GPS' }} untuk Karyawan">
    <meta name="format-detection" content="telephone=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="msapplication-config" content="/assets/img/icons/browserconfig.xml">
    <meta name="msapplication-TileColor" content="#1E4D3E">
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="theme-color" content="#1E4D3E">

    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" href="/assets/img/icons/pwa/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/assets/img/icons/pwa/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="512x512" href="/assets/img/icons/pwa/icon-512x512.png">

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json?v={{ file_exists(public_path('manifest.json')) ? filemtime(public_path('manifest.json')) : time() }}">

    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .catch(function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
            });
        }
    </script>
</head>

<body style="background-color: #EEF2F0; margin: 0; padding: 0;">
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar" style="background-color: #EEF2F0;">
        <div class="layout-container" style="background-color: #EEF2F0;">
            <!-- Sidebar -->
            @include('layouts.sidebar')
            <!-- / Sidebar-->
            <!-- Layout container -->
            <div class="layout-page" style="background-color: #EEF2F0;">
                <!-- Navbar -->
                @include('layouts.navbar')
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper" style="background-color: #EEF2F0;">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        @hasSection('navigasi')
                            <div class="mb-3">
                                @yield('navigasi')
                            </div>
                        @endif
                        @yield('content')
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    @include('layouts.footer')
                    <!-- / Footer -->
                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    @include('layouts.scripts')
</body>

</html>
