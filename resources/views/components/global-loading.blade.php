@php
    $companyName = $general_setting->nama_perusahaan ?? $general_setting->nama_aplikasi ?? config('app.name', 'E-Presensi');
    $companyLogo = $app_logo_url ?? asset('assets/login/images/logoweb-1.png');
    $primaryColor = $general_setting->theme_color_1 ?? '#1E4D3E';
    $secondaryColor = $general_setting->theme_color_2 ?? '#32745E';
@endphp

<!-- Global Server-Wait Action Loading Overlay (Full Pure White, Zero Card, High Performance) -->
<div id="global-action-loading" class="global-loading-screen" aria-hidden="true" role="status" aria-live="polite">
    <div class="global-loading-content">
        @if(!empty($companyLogo))
            <img src="{{ $companyLogo }}" alt="{{ $companyName }}" class="global-loading-logo" onerror="this.style.display='none'; document.getElementById('global-loading-fallback').style.display='block';" />
        @endif
        <div id="global-loading-fallback" class="global-loading-fallback" style="{{ !empty($companyLogo) ? 'display: none;' : 'display: block;' }}">
            {{ strtoupper(substr($companyName, 0, 1)) }}
        </div>

        <div class="global-loading-brand">{{ $companyName }}</div>

        <!-- Ultra-minimalist 2px line track -->
        <div class="global-loading-track">
            <div class="global-loading-indicator" style="background: linear-gradient(90deg, transparent, {{ $primaryColor }}, transparent);"></div>
        </div>

        <div id="global-loading-text" class="global-loading-message">Memproses...</div>
    </div>
</div>

<style>
    /* Full Pure White Screen (Zero Card, Zero Shadow, Zero Blur - 100% Lightweight) */
    .global-loading-screen {
        position: fixed;
        inset: 0;
        z-index: 999999;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.15s ease, visibility 0.15s ease;
        contain: strict;
    }

    .global-loading-screen.is-active {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    .global-loading-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 20px;
        max-width: 320px;
    }

    /* Logo diam / static - no pulse, no border box */
    .global-loading-logo {
        max-width: 68px;
        max-height: 68px;
        object-fit: contain;
        display: block;
        animation: none !important;
        transform: none !important;
    }

    .global-loading-fallback {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 32px;
        color: {{ $primaryColor }};
        line-height: 1;
        animation: none !important;
        transform: none !important;
    }

    .global-loading-brand {
        margin-top: 14px;
        font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
        letter-spacing: -0.01em;
        text-align: center;
        max-width: 260px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Minimal Hairline Line Indicator */
    .global-loading-track {
        width: 120px;
        height: 2.5px;
        background: #f1f5f9;
        border-radius: 999px;
        overflow: hidden;
        position: relative;
        margin-top: 14px;
    }

    .global-loading-indicator {
        position: absolute;
        top: 0;
        left: -50%;
        height: 100%;
        width: 50%;
        border-radius: 999px;
        animation: globalSweep 1.1s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    }

    @keyframes globalSweep {
        0% { left: -50%; }
        100% { left: 100%; }
    }

    .global-loading-message {
        margin-top: 10px;
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        font-weight: 500;
        color: #64748b;
        letter-spacing: 0.01em;
        text-align: center;
    }
</style>

<script>
    (function () {
        'use strict';

        if (window.__globalLoadingInitialized) return;
        window.__globalLoadingInitialized = true;

        window.isGlobalLoadingActive = false;
        let safetyTimeout = null;

        /**
         * Show Global Action Loading Overlay
         * @param {string} message Text to display (e.g. 'Menyimpan data...', 'Memproses...')
         */
        window.showGlobalLoading = function (message) {
            const overlay = document.getElementById('global-action-loading');
            const textEl = document.getElementById('global-loading-text');

            if (textEl && message) {
                textEl.textContent = message;
            } else if (textEl && !message) {
                textEl.textContent = 'Memproses...';
            }

            if (overlay) {
                overlay.classList.add('is-active');
                overlay.setAttribute('aria-hidden', 'false');
            }

            window.isGlobalLoadingActive = true;

            // Failsafe auto-timeout: 20 seconds maximum to guarantee UI is never permanently stuck
            if (safetyTimeout) clearTimeout(safetyTimeout);
            safetyTimeout = setTimeout(function () {
                window.hideGlobalLoading();
            }, 20000);
        };

        /**
         * Hide Global Action Loading Overlay
         */
        window.hideGlobalLoading = function () {
            const overlay = document.getElementById('global-action-loading');
            if (overlay) {
                overlay.classList.remove('is-active');
                overlay.setAttribute('aria-hidden', 'true');
            }
            window.isGlobalLoadingActive = false;
            if (safetyTimeout) {
                clearTimeout(safetyTimeout);
                safetyTimeout = null;
            }
        };

        // Hide overlay on browser back/forward cache navigation
        window.addEventListener('pageshow', function () {
            window.hideGlobalLoading();
        });

        // 1. Universal Form Submit Interception (POST, PUT, DELETE, PATCH, uploads, auth)
        document.addEventListener('submit', function (e) {
            if (e.defaultPrevented) return;
            const form = e.target;
            if (!form || form.nodeName !== 'FORM') return;

            // DO NOT trigger on GET forms (search filters, pagination, etc. are normal navigations)
            const method = (form.getAttribute('method') || 'GET').toUpperCase();
            if (method === 'GET' && !form.hasAttribute('data-loading')) return;

            // Allow forms to opt-out explicitly
            if (form.hasAttribute('data-no-loading') || form.classList.contains('no-loading')) return;

            // Do not show if HTML5 validation fails
            if (typeof form.checkValidity === 'function' && !form.checkValidity()) return;

            // Anti double-submit guard
            if (window.isGlobalLoadingActive) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }

            // Determine appropriate action message
            let msg = form.getAttribute('data-loading-text') || form.getAttribute('data-loading-msg');
            if (!msg) {
                const action = (form.getAttribute('action') || '').toLowerCase();
                const methodField = (form.querySelector('input[name="_method"]')?.value || '').toUpperCase();

                if (action.includes('/login') || action.includes('login')) {
                    msg = 'Memproses autentikasi...';
                } else if (action.includes('/logout') || action.includes('logout')) {
                    msg = 'Memproses logout...';
                } else if (methodField === 'DELETE' || action.includes('/delete') || action.includes('/hapus')) {
                    msg = 'Menghapus data...';
                } else if (action.includes('/approve') || action.includes('approve')) {
                    msg = 'Menyetujui pengajuan...';
                } else if (action.includes('/reject') || action.includes('reject') || action.includes('/tolak')) {
                    msg = 'Menolak pengajuan...';
                } else if (form.querySelector('input[type="file"]') && form.querySelector('input[type="file"]').files?.length > 0) {
                    msg = 'Mengunggah berkas...';
                } else if (action.includes('/update') || methodField === 'PUT' || methodField === 'PATCH') {
                    msg = 'Memperbarui data...';
                } else {
                    msg = 'Menyimpan data...';
                }
            }

            window.showGlobalLoading(msg);
        }, false);

        // 2. Intercept programmatic form.submit() calls (e.g. from SweetAlert confirm handlers)
        if (typeof HTMLFormElement !== 'undefined' && HTMLFormElement.prototype) {
            const origSubmit = HTMLFormElement.prototype.submit;
            HTMLFormElement.prototype.submit = function () {
                const form = this;
                const method = (form.getAttribute('method') || 'GET').toUpperCase();

                if (method !== 'GET' && !form.hasAttribute('data-no-loading') && !form.classList.contains('no-loading')) {
                    if (window.isGlobalLoadingActive) return; // Anti double submit

                    let msg = form.getAttribute('data-loading-text');
                    if (!msg) {
                        const action = (form.getAttribute('action') || '').toLowerCase();
                        if (action.includes('/login')) msg = 'Memproses autentikasi...';
                        else if (action.includes('/logout')) msg = 'Memproses logout...';
                        else if (action.includes('/delete') || action.includes('hapus')) msg = 'Menghapus data...';
                        else if (action.includes('/approve')) msg = 'Menyetujui pengajuan...';
                        else if (action.includes('/reject') || action.includes('tolak')) msg = 'Menolak pengajuan...';
                        else msg = 'Memproses...';
                    }
                    window.showGlobalLoading(msg);
                }
                return origSubmit.apply(this, arguments);
            };
        }

        // 3. jQuery AJAX Global Hooks (for AJAX requests requesting loading or with showLoading: true)
        if (window.jQuery) {
            $(document).ajaxSend(function (event, jqXHR, ajaxOptions) {
                if (ajaxOptions.showLoading === true || ajaxOptions.globalLoading === true) {
                    window.showGlobalLoading(ajaxOptions.loadingText || 'Memproses permintaan...');
                }
            });
            $(document).ajaxComplete(function (event, jqXHR, ajaxOptions) {
                if (ajaxOptions.showLoading === true || ajaxOptions.globalLoading === true) {
                    window.hideGlobalLoading();
                }
            });
            $(document).ajaxError(function (event, jqXHR, ajaxOptions) {
                if (ajaxOptions.showLoading === true || ajaxOptions.globalLoading === true) {
                    window.hideGlobalLoading();
                }
            });
        }

        // 4. Manual elements with data-loading="true"
        document.addEventListener('click', function (e) {
            const el = e.target.closest('[data-loading="true"]');
            if (el && !el.closest('form')) {
                const msg = el.getAttribute('data-loading-text') || 'Memproses...';
                window.showGlobalLoading(msg);
            }
        });
    })();
</script>
