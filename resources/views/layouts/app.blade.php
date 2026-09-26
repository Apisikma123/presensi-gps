<!DOCTYPE html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-wide" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('/assets/') }}" data-template="vertical-menu-template-no-customizer"
    style="background-color: #FAF9F8; color-scheme: light;">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <style>
        /* Instant anti-flash inline background before any external CSS is parsed */
        html, body, .layout-wrapper, .layout-container, .layout-page, .content-wrapper {
            background-color: #FAF9F8 !important;
        }
        @media (min-width: 1200px) {
            #layout-menu {
                position: fixed !important;
                top: 0 !important;
                bottom: 0 !important;
                left: 0 !important;
                width: 16.25rem !important;
                z-index: 1075 !important;
            }
            .layout-page {
                padding-left: 16.25rem !important;
            }
        }
    </style>

    <title>@yield('titlepage') | {{ $company_setting->app_name ?? ($general_setting->nama_aplikasi ?? 'Presence') }}</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ $app_logo_url ?? asset('logo.png') }}?v={{ $general_setting?->updated_at?->timestamp ?? time() }}" />
    <link rel="shortcut icon" href="{{ $app_logo_url ?? asset('favicon.ico') }}?v={{ $general_setting?->updated_at?->timestamp ?? time() }}" />
    <link rel="apple-touch-icon" href="{{ $app_logo_url ?? asset('logo.png') }}?v={{ $general_setting?->updated_at?->timestamp ?? time() }}" />

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

    <!-- Helpers & Theme Config (Must execute before core theme scripts) -->
    <script src="{{ asset('/assets/vendor/js/helpers.js') }}"></script>
    <script>
        if (window.Helpers) {
            window.Helpers.scrollToActive = function() {};
            window.Helpers._scrollToActive = function() {};
        }
    </script>
    <script src="{{ asset('/assets/js/config.js') }}"></script>

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
    <meta name="msapplication-TileColor" content="#3C2A21">
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="theme-color" content="#3C2A21">

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

<body style="background-color: #FAF9F8; margin: 0; padding: 0;">
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar" style="background-color: #FAF9F8;">
        <div class="layout-container" style="background-color: #FAF9F8;">
            <!-- Sidebar -->
            @include('layouts.sidebar')
            <!-- / Sidebar-->
            <!-- Layout container -->
            <div class="layout-page" style="background-color: #FAF9F8;">
                <!-- Navbar -->
                @include('layouts.navbar')
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper" style="background-color: #FAF9F8;">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y" id="app-main-content">
                        @hasSection('navigasi')
                            <nav aria-label="breadcrumb" class="admin-breadcrumb-nav mb-3">
                                <ol class="breadcrumb admin-breadcrumb mb-0 align-items-center">
                                    @yield('navigasi')
                                </ol>
                            </nav>
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

    <!-- Global Help Drawer (Admin Area Only) -->
    @if(auth()->check() && !auth()->user()->hasRole('karyawan') && (auth()->user()->hasAnyRole(['admin', 'super admin', 'gm administrasi', 'admin pusat']) || auth()->user()->can('dashboard.index')))
        @include('layouts.help_drawer')
    @endif

    <!-- Core JS -->
    @include('layouts.scripts')

    <!-- Global Action Loading Overlay -->
    @include('components.global-loading')
</body>

</html>
