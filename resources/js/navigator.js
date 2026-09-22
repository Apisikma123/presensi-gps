/**
 * Global Instant Navigation System (Native Reload + Frame-1 Skeleton + Prefetch)
 * Memberikan feedback instan frame-1 dengan reload native browser yang super cepat.
 */

(function() {
    'use strict';

    if (window.AppNavigator) return;

    // --------------------------------------------------------------------------
    // 1. SKELETON GENERATORS (Clean CSS, Frame-1 immediate visual feedback)
    // --------------------------------------------------------------------------

    function getSkeletonType(url) {
        let path = '';
        try {
            const a = document.createElement('a');
            a.href = url;
            path = a.pathname.toLowerCase();
        } catch (e) {
            path = (url || '').toLowerCase();
        }

        if (path.includes('/dashboard')) return 'dashboard';
        if (path.includes('/trackingpresensi')) return 'map';
        if (path.includes('/laporan') || path.includes('/generalsetting') || path.includes('/profile') || path.endsWith('/create') || path.includes('/edit') || path.endsWith('/import')) return 'form';
        return 'table';
    }

    function renderSkeletonHtml(type) {
        switch (type) {
            case 'dashboard':
                return `
                <div class="skeleton-layout-dashboard py-2">
                    <div class="skeleton-card p-3 p-sm-4 mb-4 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="app-skeleton-bone rounded-circle" style="width: 48px; height: 48px;"></div>
                            <div>
                                <div class="app-skeleton-bone mb-2" style="width: 180px; height: 18px;"></div>
                                <div class="app-skeleton-bone" style="width: 240px; height: 12px;"></div>
                            </div>
                        </div>
                        <div class="app-skeleton-bone" style="width: 140px; height: 38px; border-radius: 8px;"></div>
                    </div>
                    <div class="row g-3 mb-4">
                        ${[1, 2, 3, 4].map(() => `
                            <div class="col-12 col-sm-6 col-xl-3">
                                <div class="skeleton-card p-3 d-flex flex-column justify-content-between" style="height: 120px;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="app-skeleton-bone" style="width: 90px; height: 12px;"></div>
                                        <div class="app-skeleton-bone rounded-circle" style="width: 32px; height: 32px;"></div>
                                    </div>
                                    <div class="app-skeleton-bone mb-2" style="width: 70px; height: 26px;"></div>
                                    <div class="app-skeleton-bone" style="width: 120px; height: 10px;"></div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-lg-8">
                            <div class="skeleton-card p-4" style="min-height: 280px;">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="app-skeleton-bone" style="width: 160px; height: 16px;"></div>
                                    <div class="app-skeleton-bone" style="width: 90px; height: 28px; border-radius: 6px;"></div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between pt-4" style="height: 180px;">
                                    ${[40, 70, 55, 85, 60, 95, 75].map(h => `
                                        <div class="app-skeleton-bone" style="width: 8%; height: ${h}%; border-radius: 6px 6px 0 0;"></div>
                                    `).join('')}
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="skeleton-card p-4" style="min-height: 280px;">
                                <div class="app-skeleton-bone mb-4" style="width: 130px; height: 16px;"></div>
                                <div class="d-flex justify-content-center my-3">
                                    <div class="app-skeleton-bone rounded-circle" style="width: 140px; height: 140px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;

            case 'table':
                return `
                <div class="skeleton-layout-table py-2">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="app-skeleton-bone" style="width: 140px; height: 20px;"></div>
                            <div class="app-skeleton-bone" style="width: 60px; height: 14px;"></div>
                        </div>
                        <div class="app-skeleton-bone" style="width: 120px; height: 36px; border-radius: 8px;"></div>
                    </div>
                    <div class="skeleton-card p-3 mb-3">
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-4">
                                <div class="app-skeleton-bone w-100" style="height: 38px; border-radius: 8px;"></div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="app-skeleton-bone w-100" style="height: 38px; border-radius: 8px;"></div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="app-skeleton-bone w-100" style="height: 38px; border-radius: 8px;"></div>
                            </div>
                            <div class="col-12 col-md-2 d-flex gap-2">
                                <div class="app-skeleton-bone flex-grow-1" style="height: 38px; border-radius: 8px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="skeleton-card overflow-hidden">
                        <div class="skeleton-table-row bg-light" style="border-bottom: 2px solid #E2E8F0;">
                            <div class="app-skeleton-bone" style="width: 30px; height: 14px;"></div>
                            <div class="app-skeleton-bone" style="width: 180px; height: 14px;"></div>
                            <div class="app-skeleton-bone d-none d-md-block" style="width: 120px; height: 14px;"></div>
                            <div class="app-skeleton-bone d-none d-lg-block" style="width: 110px; height: 14px;"></div>
                            <div class="app-skeleton-bone ms-auto" style="width: 80px; height: 14px;"></div>
                        </div>
                        ${[1, 2, 3, 4, 5, 6, 7].map(() => `
                            <div class="skeleton-table-row">
                                <div class="app-skeleton-bone rounded-circle flex-shrink-0" style="width: 36px; height: 36px;"></div>
                                <div class="flex-grow-1" style="max-width: 220px;">
                                    <div class="app-skeleton-bone mb-1" style="width: 85%; height: 14px;"></div>
                                    <div class="app-skeleton-bone" style="width: 55%; height: 10px;"></div>
                                </div>
                                <div class="app-skeleton-bone d-none d-md-block" style="width: 110px; height: 13px;"></div>
                                <div class="app-skeleton-bone d-none d-lg-block" style="width: 90px; height: 20px; border-radius: 20px;"></div>
                                <div class="ms-auto d-flex gap-2">
                                    <div class="app-skeleton-bone" style="width: 28px; height: 28px; border-radius: 6px;"></div>
                                    <div class="app-skeleton-bone" style="width: 28px; height: 28px; border-radius: 6px;"></div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>`;

            case 'form':
                return `
                <div class="skeleton-layout-form py-2">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="app-skeleton-bone" style="width: 180px; height: 22px;"></div>
                        <div class="app-skeleton-bone" style="width: 80px; height: 34px; border-radius: 8px;"></div>
                    </div>
                    <div class="skeleton-card p-4">
                        <div class="row g-4">
                            ${[1, 2, 3, 4].map(() => `
                                <div class="col-12 col-md-6">
                                    <div class="app-skeleton-bone mb-2" style="width: 110px; height: 13px;"></div>
                                    <div class="app-skeleton-bone w-100" style="height: 40px; border-radius: 8px;"></div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>`;

            case 'map':
                return `
                <div class="skeleton-layout-map py-2">
                    <div class="skeleton-card p-3 mb-3 d-flex justify-content-between align-items-center">
                        <div class="app-skeleton-bone" style="width: 200px; height: 20px;"></div>
                    </div>
                    <div class="skeleton-card position-relative overflow-hidden" style="height: 500px; background: #E2E8F0;">
                        <div class="w-100 h-100 app-skeleton-bone"></div>
                    </div>
                </div>`;

            default:
                return `<div class="p-5 text-center"><div class="spinner-border text-primary" role="status"></div></div>`;
        }
    }

    // --------------------------------------------------------------------------
    // 2. SLIM TOP PROGRESS BAR
    // --------------------------------------------------------------------------

    function ensureProgressBar() {
        let bar = document.getElementById('app-top-progress');
        if (!bar) {
            bar = document.createElement('div');
            bar.id = 'app-top-progress';
            document.body.appendChild(bar);
        }
        return bar;
    }

    function startProgress() {
        const bar = ensureProgressBar();
        bar.classList.add('is-active');
        bar.style.width = '35%';
        setTimeout(() => {
            bar.style.width = '75%';
        }, 100);
    }

    function finishProgress() {
        const bar = document.getElementById('app-top-progress');
        if (!bar) return;
        bar.style.width = '100%';
        setTimeout(() => {
            bar.classList.remove('is-active');
            setTimeout(() => {
                bar.style.width = '0%';
            }, 180);
        }, 80);
    }

    // --------------------------------------------------------------------------
    // 3. SIDEBAR ACTIVE SYNC (Tactile visual feedback)
    // --------------------------------------------------------------------------

    function syncSidebarState(targetUrl) {
        const sidebar = document.getElementById('layout-menu');
        if (!sidebar) return;

        let targetPath = '';
        try {
            const a = document.createElement('a');
            a.href = targetUrl;
            targetPath = a.pathname.replace(/\/$/, '') || '/';
        } catch (e) {
            return;
        }

        sidebar.querySelectorAll('.menu-item.active').forEach(el => el.classList.remove('active'));

        const allLinks = Array.from(sidebar.querySelectorAll('a.menu-link[href]'));
        for (const link of allLinks) {
            const href = link.getAttribute('href');
            if (!href || href === '#' || href.startsWith('javascript:')) continue;
            try {
                const a = document.createElement('a');
                a.href = href;
                const linkPath = a.pathname.replace(/\/$/, '') || '/';
                if (linkPath === targetPath) {
                    const item = link.closest('.menu-item');
                    if (item) item.classList.add('active');
                    break;
                }
            } catch (e) {}
        }
    }

    // --------------------------------------------------------------------------
    // 4. INSTANT FRAME-1 NAVIGATION DISPATCHER
    // --------------------------------------------------------------------------

    function prepareInstantTransition(url) {
        // Preserve sidebar scroll position before frame-1 swap
        const menuInner = document.querySelector('#layout-menu .menu-inner') || document.querySelector('.menu-inner');
        if (menuInner) {
            sessionStorage.setItem('sidebar_scroll_pos', menuInner.scrollTop);
            sessionStorage.setItem('sidebar_last_path', window.location.pathname.replace(/\/$/, '') || '/');
        }

        // Frame 1: Immediate Skeleton
        const main = document.getElementById('app-main-content');
        if (main) {
            const skeletonType = getSkeletonType(url);
            main.innerHTML = renderSkeletonHtml(skeletonType);
        }
        startProgress();
        syncSidebarState(url);
        document.body.classList.add('is-navigating');

        // Close mobile drawer if open
        if (window.jQuery) {
            window.jQuery('html').removeClass('layout-menu-expanded');
        }
    }

    function visit(url) {
        if (!url || url === window.location.href) return;
        prepareInstantTransition(url);
        requestAnimationFrame(function() {
            window.location.href = url;
        });
    }

    // --------------------------------------------------------------------------
    // 5. NAVIGATION LINK VALIDATION & CLICK HANDLER
    // --------------------------------------------------------------------------

    function isInternalValidLink(anchor) {
        if (!anchor || anchor.tagName !== 'A') return false;
        const href = anchor.getAttribute('href');
        if (!href || href === '#' || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return false;
        if (anchor.target === '_blank' || anchor.hasAttribute('download') || anchor.hasAttribute('data-no-pjax') || anchor.hasAttribute('data-native-nav')) return false;
        if (href.includes('logout') || anchor.closest('form')) return false;

        const lowerHref = href.toLowerCase();
        if (lowerHref.includes('/export') || lowerHref.includes('export') || lowerHref.includes('/download') || lowerHref.includes('download') ||
            lowerHref.endsWith('.xlsx') || lowerHref.endsWith('.xls') || lowerHref.endsWith('.csv') ||
            lowerHref.endsWith('.pdf') || lowerHref.endsWith('.sql')) {
            return false;
        }

        try {
            const urlObj = new URL(anchor.href, window.location.origin);
            if (urlObj.origin !== window.location.origin) return false;
        } catch (e) {
            return false;
        }

        return true;
    }

    // Intercept Link Click -> Render Frame 1 UI Immediately, then let Native Reload follow!
    document.addEventListener('click', function(e) {
        if (e.button !== 0) return;
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        if (e.defaultPrevented) return;

        const a = e.target.closest ? e.target.closest('a') : null;
        if (!isInternalValidLink(a)) return;

        const targetUrl = a.href;
        const currentUrl = window.location.href.split('#')[0];
        const destUrl = targetUrl.split('#')[0];
        if (destUrl === currentUrl) return;

        // Prevent standard delayed freeze so frame-1 paints immediately
        e.preventDefault();

        // 1. Frame 1: Immediate visual feedback (skeleton, progress bar, active menu highlight)
        prepareInstantTransition(targetUrl);

        // 2. Next animation frame: browser paints Frame 1, then executes clean native navigation
        requestAnimationFrame(function() {
            window.location.href = targetUrl;
        });
    }, { capture: true });

    // Intercept GET Filter Forms for Instant Frame 1 Skeleton
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form || form.tagName !== 'FORM') return;
        if (form.closest('.modal')) return;

        const method = (form.getAttribute('method') || 'GET').toUpperCase();
        if (method === 'GET') {
            const action = form.getAttribute('action') || window.location.href;
            prepareInstantTransition(action);
        }
    }, { capture: true });

    // On Page Load Arrival -> Finish Progress Bar
    if (document.readyState === 'complete') {
        finishProgress();
    } else {
        window.addEventListener('load', finishProgress);
    }

    // Export API
    window.AppNavigator = {
        visit: visit,
        prepare: prepareInstantTransition
    };

})();
