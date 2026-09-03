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

    <!-- Helpers -->
    <script src="{{ asset('/assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
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
                    <div class="container-xxl flex-grow-1 container-p-y" id="spa-content-area">
                        @hasSection('navigasi')
                            <div class="mb-3" id="spa-navigasi-area">
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
    <!-- Page JS -->

    <!-- Top Progress Bar Feedback & Instant Navigation Engine -->
    <style>
        #page-progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            width: 0%;
            background: linear-gradient(90deg, var(--theme-color-1, #1E4D3E), #10b981, #34d399);
            z-index: 999999;
            transition: width 0.2s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.25s ease;
            pointer-events: none;
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
            opacity: 0;
        }
        #spa-content-area {
            transition: opacity 0.12s ease-out;
        }
    </style>
    <div id="page-progress-bar"></div>
    <script>
        (function() {
            const bar = document.getElementById('page-progress-bar');
            let progressTimer = null;

            function startProgress() {
                if (!bar) return;
                clearInterval(progressTimer);
                bar.style.transition = 'none';
                bar.style.width = '0%';
                bar.style.opacity = '1';
                
                requestAnimationFrame(() => {
                    bar.style.transition = 'width 0.25s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.2s ease';
                    bar.style.width = '55%';
                    
                    let current = 55;
                    progressTimer = setInterval(() => {
                        if (current < 92) {
                            current += (92 - current) * 0.2;
                            bar.style.width = Math.round(current) + '%';
                        }
                    }, 100);
                });
            }

            function finishProgress() {
                if (!bar) return;
                clearInterval(progressTimer);
                bar.style.transition = 'width 0.12s ease, opacity 0.18s ease';
                bar.style.width = '100%';
                setTimeout(() => { bar.style.opacity = '0'; }, 120);
                setTimeout(() => { bar.style.width = '0%'; }, 300);
            }

            window.startPageProgress = startProgress;
            window.finishPageProgress = finishProgress;

            // Immediate 1st-click feedback on genuine navigations (never preventDefault!)
            document.addEventListener('click', function(e) {
                if (e.defaultPrevented) return;
                if (e.ctrlKey || e.shiftKey || e.metaKey || e.button !== 0) return;

                const a = e.target.closest('a[href]');
                if (!a) return;

                const href = a.getAttribute('href');
                if (!href || href === '#' || href === 'javascript:void(0);' || href.startsWith('javascript:')) {
                    return;
                }

                if (a.target === '_blank' || a.hasAttribute('download') || a.hasAttribute('data-bs-toggle') || a.hasAttribute('data-bs-target')) return;
                if (a.classList.contains('menu-toggle') || a.classList.contains('btnEdit') || a.classList.contains('delete-confirm')) return;

                // Start progress feedback immediately!
                startProgress();
            }, false);

            // Universal Fast Pagination Handler
            window.navigatePage = function(pageNum) {
                startProgress();
                const url = new URL(window.location.href);
                url.searchParams.set('page', pageNum);
                window.location.href = url.toString();
            };

            // Form submit progress feedback
            document.addEventListener('submit', function(e) {
                const form = e.target.closest('form');
                if (form && !form.target) {
                    startProgress();
                }
            });

            window.addEventListener('beforeunload', function() {
                startProgress();
            });

            window.addEventListener('pageshow', function() {
                finishProgress();
            });
        })();
    </script>
</body>

</html>
