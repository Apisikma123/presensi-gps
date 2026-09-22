@php
    $helpPages = config('admin_help.pages', []);
@endphp

<!-- Global Help Offcanvas Drawer (Enterprise UI - 100% Theme Dynamic, Single Scrollbar, No Scroll Chaining) -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasHelp" aria-labelledby="offcanvasHelpLabel"
    data-bs-backdrop="false" data-bs-scroll="false"
    style="width: 540px; max-width: 95vw; z-index: 1075; border-left: 1px solid #E2E8F0; box-shadow: -8px 0 32px rgba(15, 23, 42, 0.1); background: #F8FAFC; overscroll-behavior: contain !important;">
    
    <!-- Drawer Header -->
    <div class="offcanvas-header border-bottom py-3 px-4 bg-white sticky-top">
        <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                style="width: 38px; height: 38px; background: rgba(var(--bs-primary-rgb), 0.08); color: var(--theme-color-1); border: 1px solid rgba(var(--bs-primary-rgb), 0.18);">
                <i class="ti ti-help-hexagon" style="font-size: 20px;"></i>
            </div>
            <div class="min-w-0">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="offcanvas-title mb-0 fw-bold text-dark" id="offcanvasHelpLabel" style="font-size: 15px; letter-spacing: -0.01em;">
                        Panduan Halaman Admin
                    </h6>
                    <span class="badge" style="background: rgba(var(--bs-primary-rgb), 0.08); color: var(--theme-color-1); border: 1px solid rgba(var(--bs-primary-rgb), 0.2); font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.05em;">
                        Sistem
                    </span>
                </div>
                <small class="text-muted text-truncate d-block" id="helpCurrentPageSubtitle" style="font-size: 11.5px; margin-top: 1px;">
                    Petunjuk penggunaan fitur operasional
                </small>
            </div>
        </div>
        <button type="button" class="btn-close text-reset ms-2 flex-shrink-0" data-bs-dismiss="offcanvas" aria-label="Tutup Bantuan" title="Tutup Bantuan (Esc)"></button>
    </div>



    <!-- Drawer Content Body -->
    <div class="offcanvas-body p-4" id="helpDrawerBody" style="overflow-y: auto; background: #F8FAFC;">
        <!-- Dynamic Help Content Container -->
        <div id="helpContentContainer">
            <!-- Rendered by JavaScript -->
        </div>
    </div>
</div>

<style>
    /* Strictly suppress offcanvas backdrop: ensures page is NEVER dimmed and ALWAYS clickable */
    .offcanvas-backdrop,
    .offcanvas-backdrop.fade,
    .offcanvas-backdrop.show {
        display: none !important;
        opacity: 0 !important;
        pointer-events: none !important;
        visibility: hidden !important;
        width: 0 !important;
        height: 0 !important;
    }

    /* Isolate scrolling strictly to drawer body and eliminate scroll chaining */
    #offcanvasHelp,
    #helpDrawerBody {
        overscroll-behavior: contain !important;
    }

    /* Sleek minimalist scrollbar for drawer body (replaces thick default browser bar) */
    #helpDrawerBody::-webkit-scrollbar {
        width: 6px;
    }
    #helpDrawerBody::-webkit-scrollbar-track {
        background: transparent;
    }
    #helpDrawerBody::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 9999px;
    }
    #helpDrawerBody::-webkit-scrollbar-thumb:hover {
        background: #94A3B8;
    }

    /* Lock background page scrolling when drawer is open: eliminates double scrollbars & stops page moving */
    body.help-drawer-open {
        overflow: hidden !important;
    }

    /* Enterprise Clean Card System (100% Theme Variable Driven) */
    .help-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 1.15rem 1.25rem;
        margin-bottom: 0.85rem;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
        transition: border-color 0.15s ease;
    }
    .help-card:hover {
        border-color: #CBD5E1;
    }
    .help-card-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748B;
        margin-bottom: 0.65rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .help-card-label i {
        color: var(--theme-color-1);
        font-size: 14px;
    }
    .help-step-number {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        background: rgba(var(--bs-primary-rgb), 0.09);
        color: var(--theme-color-1);
        border: 1px solid rgba(var(--bs-primary-rgb), 0.18);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 11.5px;
        font-family: 'JetBrains Mono', monospace;
        flex-shrink: 0;
        margin-top: 1px;
    }
    .help-comp-box {
        padding: 0.65rem 0.85rem;
        background: #F8FAFC;
        border: 1px solid #F1F5F9;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }
    .help-comp-title {
        font-weight: 700;
        font-size: 11.5px;
        color: #0F172A;
        margin-bottom: 2px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .help-comp-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--theme-color-1);
        display: inline-block;
        flex-shrink: 0;
    }
    .help-comp-text {
        font-size: 12px;
        color: #475569;
        line-height: 1.5;
        margin-bottom: 0;
    }
    .help-box-tip {
        background: rgba(var(--bs-primary-rgb), 0.04);
        border: 1px solid rgba(var(--bs-primary-rgb), 0.15);
        border-radius: 12px;
        padding: 1rem 1.15rem;
        margin-bottom: 0.85rem;
    }
    .help-box-tip .tip-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--theme-color-1);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .help-box-warning {
        background: #FFFBEB;
        border: 1px solid #FDE68A;
        border-radius: 12px;
        padding: 1rem 1.15rem;
        margin-bottom: 0.85rem;
    }
    .help-box-warning .warning-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #B45309;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }
</style>

<script>
(function() {
    'use strict';

    const HELP_PAGES = @json($helpPages);
    let currentDetectedKey = 'dashboard';
    let viewingKey = 'dashboard';

    // Cleanup any stray backdrop immediately so the page is NEVER dimmed
    function purgeStrayBackdrops() {
        document.querySelectorAll('.offcanvas-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
    }

    // Route / Path Resolver
    function resolveHelpKey(path) {
        let cleanPath = (path || window.location.pathname || '').replace(/^\/+|\/+$/g, '').toLowerCase();

        // 1. Specific sub-pages check first
        if (cleanPath.startsWith('karyawan/') && cleanPath.endsWith('/show')) {
            return 'karyawan_detail';
        }
        if (cleanPath.startsWith('laporan/presensi')) {
            return 'laporan_presensi';
        }
        if (cleanPath.startsWith('laporan/cuti')) {
            return 'laporan_cuti';
        }
        if (cleanPath.startsWith('trackingpresensi')) {
            return 'trackingpresensi';
        }
        if (cleanPath.startsWith('generalsetting')) {
            return 'generalsetting';
        }
        if (cleanPath.startsWith('permissiongroups')) {
            return 'permissiongroups';
        }
        if (cleanPath.startsWith('permissions')) {
            return 'permissions';
        }

        // 2. Main base modules check
        const segments = cleanPath.split('/');
        const firstSegment = segments[0] || 'dashboard';

        if (HELP_PAGES[firstSegment]) {
            return firstSegment;
        }

        // 3. Fallback matching
        for (const [key, data] of Object.entries(HELP_PAGES)) {
            if (data.pattern) {
                for (const p of data.pattern) {
                    const cleanP = p.replace(/\/\*$/, '').replace(/^\/+|\/+$/g, '');
                    if (cleanPath === cleanP || cleanPath.startsWith(cleanP + '/')) {
                        return key;
                    }
                }
            }
        }

        return 'dashboard';
    }

    // Escape HTML to prevent injection
    function escapeHtml(str) {
        if (!str) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(str).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Render Help Content into Drawer
    function renderHelp(key) {
        const data = HELP_PAGES[key] || HELP_PAGES['dashboard'];
        viewingKey = key;

        const container = document.getElementById('helpContentContainer');
        if (!container) return;

        // Update Header subtitle and select value
        const subtitleEl = document.getElementById('helpCurrentPageSubtitle');
        if (subtitleEl) subtitleEl.textContent = data.subtitle || 'Petunjuk penggunaan sistem';



        // Build Clean Enterprise HTML
        let html = '';

        // 1. Header Card (Title, Category Badge, About)
        html += `
            <div class="help-card mb-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="badge" style="background: rgba(var(--bs-primary-rgb), 0.08); color: var(--theme-color-1); border: 1px solid rgba(var(--bs-primary-rgb), 0.2); font-size: 10.5px; font-weight: 700; border-radius: 6px; padding: 3px 8px; text-transform: uppercase; letter-spacing: 0.04em;">
                        ${escapeHtml(data.badge || 'Modul Admin')}
                    </span>
                    <small class="text-muted font-mono" style="font-size: 11px;">Presensi GPS</small>
                </div>
                <h5 class="fw-bold text-dark mb-2" style="font-size: 16px; letter-spacing: -0.01em;">
                    ${escapeHtml(data.title)}
                </h5>
                ${data.about ? `
                    <p class="text-muted mb-0" style="font-size: 12.5px; line-height: 1.6; color: #475569 !important;">
                        ${escapeHtml(data.about)}
                    </p>
                ` : ''}
            </div>
        `;

        // 2. Yang Bisa Dilakukan
        if (data.actions && data.actions.length > 0) {
            html += `
                <div class="help-card">
                    <div class="help-card-label">
                        <i class="ti ti-list-check"></i>
                        <span>Yang Bisa Dilakukan</span>
                    </div>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size: 12.5px;">
                        ${data.actions.map(action => `
                            <li class="d-flex align-items-start gap-2 text-dark" style="line-height: 1.45;">
                                <i class="ti ti-check flex-shrink-0" style="color: var(--theme-color-1); font-size: 15px; margin-top: 2px;"></i>
                                <span style="color: #334155;">${escapeHtml(action)}</span>
                            </li>
                        `).join('')}
                    </ul>
                </div>
            `;
        }

        // 3. Cara Menggunakan
        if (data.steps && data.steps.length > 0) {
            html += `
                <div class="help-card">
                    <div class="help-card-label">
                        <i class="ti ti-compass"></i>
                        <span>Cara Menggunakan</span>
                    </div>
                    <div class="d-flex flex-column gap-2.5">
                        ${data.steps.map((step, idx) => `
                            <div class="d-flex align-items-start gap-2.5">
                                <span class="help-step-number">${idx + 1}</span>
                                <div class="text-dark" style="font-size: 12.5px; line-height: 1.55; padding-top: 1px; color: #334155 !important;">
                                    ${escapeHtml(step)}
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        // 4. Penjelasan Tombol & Bagian Halaman
        if (data.components && Object.keys(data.components).length > 0) {
            html += `
                <div class="help-card">
                    <div class="help-card-label">
                        <i class="ti ti-layers-subtract"></i>
                        <span>Penjelasan Tombol & Filter</span>
                    </div>
                    <div>
                        ${Object.entries(data.components).map(([name, desc]) => `
                            <div class="help-comp-box">
                                <div class="help-comp-title">
                                    <span class="help-comp-dot"></span>
                                    <span>${escapeHtml(name)}</span>
                                </div>
                                <p class="help-comp-text">${escapeHtml(desc)}</p>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        // 5. Tips Penggunaan (Driven by Theme Color)
        if (data.tips && data.tips.length > 0) {
            html += `
                <div class="help-box-tip">
                    <div class="tip-label">
                        <i class="ti ti-bulb" style="color: var(--theme-color-1); font-size: 15px;"></i>
                        <span>Tips Praktis</span>
                    </div>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-1.5" style="font-size: 12px;">
                        ${data.tips.map(tip => `
                            <li class="d-flex align-items-start gap-2" style="line-height: 1.45; color: #1E293B;">
                                <i class="ti ti-point flex-shrink-0" style="color: var(--theme-color-1); font-size: 15px; margin-top: 1px;"></i>
                                <span>${escapeHtml(tip)}</span>
                            </li>
                        `).join('')}
                    </ul>
                </div>
            `;
        }

        // 6. Perhatian / Tindakan Sensitif
        if (data.warnings) {
            html += `
                <div class="help-box-warning">
                    <div class="warning-label">
                        <i class="ti ti-alert-triangle" style="font-size: 15px; color: #D97706;"></i>
                        <span>Hal yang Perlu Diperhatikan</span>
                    </div>
                    <p class="mb-0" style="font-size: 12px; line-height: 1.55; color: #78350F !important;">
                        ${escapeHtml(data.warnings)}
                    </p>
                </div>
            `;
        }

        container.innerHTML = html;
        container.style.display = 'block';

    }

    // Refresh context when drawer opens
    function onDrawerOpen() {
        purgeStrayBackdrops();
        document.body.classList.add('help-drawer-open');
        document.body.style.overflow = 'hidden';
        currentDetectedKey = resolveHelpKey(window.location.pathname);
        renderHelp(currentDetectedKey);
        setTimeout(purgeStrayBackdrops, 50);
    }

    function onDrawerClose() {
        document.body.classList.remove('help-drawer-open');
        document.body.style.overflow = '';
        purgeStrayBackdrops();
    }

    // Initialize Event Listeners
    function initHelpDrawer() {
        purgeStrayBackdrops();

        const offcanvasEl = document.getElementById('offcanvasHelp');
        if (!offcanvasEl) return;

        // Bootstrap show and shown events
        offcanvasEl.addEventListener('show.bs.offcanvas', onDrawerOpen);
        offcanvasEl.addEventListener('shown.bs.offcanvas', function() {
            purgeStrayBackdrops();
            document.body.classList.add('help-drawer-open');
            document.body.style.overflow = 'hidden';
        });
        offcanvasEl.addEventListener('hidden.bs.offcanvas', onDrawerClose);


    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHelpDrawer);
    } else {
        initHelpDrawer();
    }

    // Also purge on navigation / state changes
    window.addEventListener('popstate', purgeStrayBackdrops);
})();
</script>
