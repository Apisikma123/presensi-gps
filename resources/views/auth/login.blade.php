<!DOCTYPE html>

<html lang="en" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('/assets/') }}"
    data-template="vertical-menu-template-no-customizer">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Login | {{ $company_setting->app_name ?? ($general_setting->nama_aplikasi ?? 'Presence') }}</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ $app_logo_url ?? asset('logo.png') }}?v={{ $general_setting?->updated_at?->timestamp ?? time() }}" />
    <link rel="shortcut icon" href="{{ $app_logo_url ?? asset('favicon.ico') }}?v={{ $general_setting?->updated_at?->timestamp ?? time() }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('/assets/vendor/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/vendor/fonts/tabler-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/vendor/fonts/flag-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/rtl/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/rtl/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/typeahead-js/typeahead.css') }}" />
    <!-- Vendor -->
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/page-auth.css') }}" />

    <!-- Theme Custom Properties with Auto-Contrast -->
    <style>
        :root {
            --color-primary: {{ $theme['primary'] ?? ($t['primary'] ?? '#3C2A21') }};
            --bs-primary: {{ $theme['primary'] ?? ($t['primary'] ?? '#3C2A21') }};
            --theme-color-1: {{ $theme['primary'] ?? ($t['primary'] ?? '#3C2A21') }};
            --theme-primary-contrast: {{ $theme['primary_contrast'] ?? '#FFFFFF' }};
        }
        .btn-primary {
            background-color: var(--theme-color-1) !important;
            border-color: var(--theme-color-1) !important;
            color: var(--theme-primary-contrast, #FFFFFF) !important;
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: {{ $theme['primary_hover'] ?? '#2A1D17' }} !important;
            border-color: {{ $theme['primary_hover'] ?? '#2A1D17' }} !important;
            color: var(--theme-primary-contrast, #FFFFFF) !important;
        }
        .form-control:focus {
            border-color: var(--theme-color-1) !important;
            box-shadow: 0 0 0 0.25rem {{ $theme['primary_soft'] ?? 'rgba(60,42,33,0.15)' }} !important;
        }
    </style>

    <!-- Helpers -->
    <script src="{{ asset('/assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('/assets/js/config.js') }}"></script>
</head>
<!-- Content -->

<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
            <!-- Login -->
            <div class="card shadow-sm border-0" style="border-radius: 14px;">
                <div class="card-body p-4">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mb-3 mt-2 text-center">
                        @if (!empty($app_logo_url))
                            <img src="{{ $app_logo_url }}" alt="Logo" style="max-height: 52px; max-width: 180px; object-fit: contain;">
                        @else
                            <div class="d-inline-flex p-2.5 rounded-3 shadow-sm" style="background: {{ $theme['primary'] ?? ($t['primary'] ?? '#3C2A21') }}; color: {{ $theme['primary_contrast'] ?? '#FFFFFF' }};">
                                <i class="ti ti-fingerprint" style="font-size: 28px;"></i>
                            </div>
                        @endif
                    </div>
                    <!-- /Logo -->
                    <h4 class="mb-1 pt-1 text-center" style="font-family: 'Outfit', sans-serif; font-weight: 700;">{{ $company_setting->app_name ?? ($general_setting->nama_aplikasi ?? 'Presence') }}</h4>
                    <p class="mb-3 text-muted text-center" style="font-size: 13px;">{{ $company_setting->company_name ?? ($general_setting->nama_perusahaan ?? 'Universal HR Management System') }}</p>
                    <x-alert-error :messages="$errors->get('id_user')" class="mt-2" />
                    <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="id_user" class="form-label fw-semibold" style="font-size: 12.5px;">Email or Username</label>
                            <input type="text" class="form-control" id="id_user" name="id_user" placeholder="Enter your email or username"
                                autofocus />
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <div class="d-flex justify-content-between">
                                <label class="form-label fw-semibold" for="password" style="font-size: 12.5px;">Password</label>
                            </div>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control" name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password"
                                    autocomplete="current-password" />
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember-me" />
                                <label class="form-check-label" for="remember-me"> Remember Me </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-primary d-grid w-100 py-2 fw-semibold" type="submit">Sign in</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /Register -->
        </div>
    </div>
</div>

<!-- / Content -->

<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->

<script src="{{ asset('/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('/assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/node-waves/node-waves.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/hammer/hammer.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/i18n/i18n.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
<script src="{{ asset('/assets/vendor/js/menu.js') }}"></script>

<!-- endbuild -->

<!-- Vendors JS -->
<script src="{{ asset('/assets/vendor/libs/@form-validation/umd/bundle/popular.min.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>

<!-- Main JS -->
<script src="{{ asset('/assets/js/main.js') }}"></script>

<!-- Page JS -->
<script src="{{ asset('/assets/js/pages-auth.js') }}"></script>

<!-- Global Action Loading Overlay -->
@include('components.global-loading')
