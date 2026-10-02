@php
    $companyName = $general_setting->nama_perusahaan ?? $general_setting->nama_aplikasi ?? config('app.name', 'E-Presensi');
    $companyLogo = $app_logo_url ?? asset('assets/login/images/logoweb-1.png');
    $themeData = \App\Services\ThemeResolver::resolve();
    $primaryColor = $themeData['primary'] ?? '#3C2A21';
    $secondaryColor = $themeData['secondary'] ?? '#634832';
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
            <div class="global-loading-indicator" style="background: linear-gradient(90deg, transparent, var(--color-primary, {{ $primaryColor }}), var(--theme-color-2, {{ $secondaryColor }}), transparent);"></div>
        </div>

        <div id="global-loading-text" class="global-loading-message">Memproses...</div>
    </div>
</div>

<style>
    /* Full Responsive Screen with Theme Canvas */
    .global-loading-screen {
        position: fixed;
        inset: 0;
        z-index: 999999;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--theme-canvas, #FAF9F8);
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
        color: var(--color-primary, {{ $primaryColor }});
        line-height: 1;
        animation: none !important;
        transform: none !important;
    }

    .global-loading-brand {
        margin-top: 14px;
        font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
        font-size: 15px;
        font-weight: 600;
        color: var(--theme-text-primary, #1A1C1C);
        letter-spacing: -0.01em;
        text-align: center;
        max-width: 260px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Minimal Hairline Line Indicator with Theme Background */
    .global-loading-track {
        width: 120px;
        height: 2.5px;
        background: var(--bs-primary-bg-subtle, rgba(var(--bs-primary-rgb, 60, 42, 33), 0.12));
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
        color: var(--theme-color-2, {{ $secondaryColor }});
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
         * Show Global Action Loading Overlay (for explicit heavy operations: export, backup, etc.)
         * @param {string} message Text to display (e.g. 'Mengekspor data...', 'Memproses...')
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

            // Failsafe auto-timeout: 20 seconds maximum
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

        /**
         * Universal Request-State Button System (Inline feedback, zero layout shift)
         */
        window.setButtonSubmitting = function (btn, message) {
            if (!btn || btn._isSubmitting) return;

            btn._isSubmitting = true;
            btn.classList.add('btn-processing', 'is-submitting-btn');

            // Store original width and HTML to prevent layout jump
            const rect = btn.getBoundingClientRect();
            if (rect.width > 0 && !btn.dataset.originalWidth) {
                btn.dataset.originalWidth = rect.width + 'px';
                btn.style.minWidth = rect.width + 'px';
            }

            if (btn.tagName === 'INPUT') {
                if (!btn.dataset.originalVal) btn.dataset.originalVal = btn.value;
                btn.value = message || 'Memproses...';
            } else {
                if (!btn.dataset.originalHtml) btn.dataset.originalHtml = btn.innerHTML;
                btn.innerHTML = `<span class="btn-spinner" role="status" aria-hidden="true"></span><span>${message || 'Memproses...'}</span>`;
            }

            btn.disabled = true;

            // Safety timeout to restore button if form submit halts (12s failsafe)
            setTimeout(function() {
                if (btn._isSubmitting) {
                    window.resetButtonState(btn);
                }
            }, 12000);
        };

        window.resetButtonState = function (btnOrForm) {
            if (!btnOrForm) return;

            let btns = [];
            if (btnOrForm.tagName === 'FORM') {
                btns = Array.from(btnOrForm.querySelectorAll('.btn-processing, .is-submitting-btn, [data-original-html], [data-original-val]'));
            } else if (btnOrForm.classList) {
                btns = [btnOrForm];
            }

            btns.forEach(function (btn) {
                btn._isSubmitting = false;
                btn.classList.remove('btn-processing', 'is-submitting-btn');
                btn.disabled = false;

                if (btn.dataset.originalWidth) {
                    btn.style.minWidth = '';
                    delete btn.dataset.originalWidth;
                }

                if (btn.tagName === 'INPUT' && btn.dataset.originalVal) {
                    btn.value = btn.dataset.originalVal;
                    delete btn.dataset.originalVal;
                } else if (btn.dataset.originalHtml) {
                    btn.innerHTML = btn.dataset.originalHtml;
                    delete btn.dataset.originalHtml;
                }
            });
        };

        // Auto-restore on back/forward cache navigation
        window.addEventListener('pageshow', function () {
            window.hideGlobalLoading();
            document.querySelectorAll('.btn-processing, .is-submitting-btn').forEach(function (btn) {
                window.resetButtonState(btn);
            });
        });

        function resolveActionMessage(form) {
            let msg = form.getAttribute('data-loading-text') || form.getAttribute('data-loading-msg');
            if (msg) return msg;

            const action = (form.getAttribute('action') || '').toLowerCase();
            const methodField = (form.querySelector('input[name="_method"]')?.value || '').toUpperCase();

            if (action.includes('/login') || action.includes('login')) {
                return 'Memproses...';
            } else if (action.includes('/logout') || action.includes('logout')) {
                return 'Keluar...';
            } else if (action.includes('/calculate') || action.includes('kalkulasi') || action.includes('hitung')) {
                return 'Menghitung...';
            } else if (action.includes('/finalize') || action.includes('finalisasi') || action.includes('kunci')) {
                return 'Mengunci...';
            } else if (action.includes('/reopen')) {
                return 'Membuka Revisi...';
            } else if (methodField === 'DELETE' || action.includes('/delete') || action.includes('/hapus')) {
                return 'Menghapus...';
            } else if (action.includes('/approve') || action.includes('approve')) {
                return 'Menyetujui...';
            } else if (action.includes('/reject') || action.includes('reject') || action.includes('/tolak')) {
                return 'Menolak...';
            } else if (form.querySelector('input[type="file"]') && form.querySelector('input[type="file"]').files?.length > 0) {
                return 'Mengunggah...';
            } else if (action.includes('/update') || methodField === 'PUT' || methodField === 'PATCH') {
                return 'Memperbarui...';
            }
            return 'Menyimpan...';
        }

        // Track last clicked submit button within form
        let activeSubmitter = null;
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('button[type="submit"], input[type="submit"], button:not([type]), .btn-submit');
            if (btn && btn.closest('form')) {
                activeSubmitter = btn;
            }
        }, true);

        // 1. Universal Form Submit Interception (Inline Request-State feedback)
        document.addEventListener('submit', function (e) {
            if (e.defaultPrevented) return;
            const form = e.target;
            if (!form || form.nodeName !== 'FORM') return;

            // DO NOT trigger on GET forms (search filters, pagination, etc.)
            const method = (form.getAttribute('method') || 'GET').toUpperCase();
            if (method === 'GET' && !form.hasAttribute('data-loading')) return;

            // Allow forms to opt-out explicitly
            if (form.hasAttribute('data-no-loading') || form.classList.contains('no-loading')) return;

            // Do not intercept if HTML5 validation fails
            if (typeof form.checkValidity === 'function' && !form.checkValidity()) return;

            const msg = resolveActionMessage(form);

            // If explicitly marked for full-screen global loading (e.g. database backup or export)
            if (form.hasAttribute('data-global-loading') || form.classList.contains('global-loading')) {
                if (window.isGlobalLoadingActive) {
                    e.preventDefault();
                    return false;
                }
                window.showGlobalLoading(msg);
                return;
            }

            // Normal Request: Use inline Request-State Button
            const submitBtn = (activeSubmitter && form.contains(activeSubmitter))
                ? activeSubmitter
                : form.querySelector('button[type="submit"], input[type="submit"], .btn-submit, button.btn-primary');

            if (submitBtn) {
                if (submitBtn._isSubmitting) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
                window.setButtonSubmitting(submitBtn, msg);
            }
        }, false);

        // 2. Programmatic form.submit() interception (e.g. from SweetAlert confirm)
        if (typeof HTMLFormElement !== 'undefined' && HTMLFormElement.prototype) {
            const origSubmit = HTMLFormElement.prototype.submit;
            HTMLFormElement.prototype.submit = function () {
                const form = this;
                const method = (form.getAttribute('method') || 'GET').toUpperCase();

                if (method !== 'GET' && !form.hasAttribute('data-no-loading') && !form.classList.contains('no-loading')) {
                    const msg = resolveActionMessage(form);
                    if (form.hasAttribute('data-global-loading') || form.classList.contains('global-loading')) {
                        if (!window.isGlobalLoadingActive) window.showGlobalLoading(msg);
                    } else {
                        const submitBtn = form.querySelector('button[type="submit"], input[type="submit"], .btn-submit, button.btn-primary');
                        if (submitBtn) window.setButtonSubmitting(submitBtn, msg);
                    }
                }
                return origSubmit.apply(this, arguments);
            };
        }

        // 3. jQuery AJAX Global Hooks (Only trigger global loading if explicitly asked)
        if (window.jQuery) {
            $(document).ajaxSend(function (event, jqXHR, ajaxOptions) {
                if (ajaxOptions.globalLoading === true) {
                    window.showGlobalLoading(ajaxOptions.loadingText || 'Memproses permintaan...');
                }
            });
            $(document).ajaxComplete(function (event, jqXHR, ajaxOptions) {
                if (ajaxOptions.globalLoading === true) {
                    window.hideGlobalLoading();
                }
            });
            $(document).ajaxError(function (event, jqXHR, ajaxOptions) {
                if (ajaxOptions.globalLoading === true) {
                    window.hideGlobalLoading();
                }
                // Auto-restore any submitting buttons in document if AJAX errored
                document.querySelectorAll('.btn-processing, .is-submitting-btn').forEach(function (btn) {
                    window.resetButtonState(btn);
                });
            });
        }
    })();
</script>
