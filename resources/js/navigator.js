/**
 * Global Instant Navigation System (High-Performance Native Navigation)
 * Lightweight non-blocking top progress indicator, instant menu sync, and zero artificial delays.
 */

(function() {
    'use strict';

    if (window.AppNavigator) return;

    let progressTimer = null;

    // --------------------------------------------------------------------------
    // 1. SLIM TOP PROGRESS BAR (Lightweight, non-blocking)
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
        if (progressTimer) clearTimeout(progressTimer);

        bar.classList.add('is-active');
        bar.style.width = '35%';

        progressTimer = setTimeout(() => {
            bar.style.width = '75%';
        }, 120);
    }

    function finishProgress() {
        if (progressTimer) {
            clearTimeout(progressTimer);
            progressTimer = null;
        }

        const bar = document.getElementById('app-top-progress');
        if (!bar) return;

        bar.style.width = '100%';
        setTimeout(() => {
            bar.classList.remove('is-active');
            setTimeout(() => {
                bar.style.width = '0%';
            }, 150);
        }, 80);

        document.body.classList.remove('is-navigating');
    }

    // --------------------------------------------------------------------------
    // 2. SIDEBAR ACTIVE SYNC (Tactile visual feedback)
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
    // 3. NAVIGATION LINK VALIDATION
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

    // --------------------------------------------------------------------------
    // 4. INSTANT EVENT LISTENERS (Non-blocking, native navigation)
    // --------------------------------------------------------------------------

    // On Link Click: Start progress immediately, sync sidebar, close mobile menu, and let browser navigate natively
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

        // Save current sidebar scroll position before leaving page
        const sidebarInner = document.querySelector('#layout-menu .menu-inner');
        if (sidebarInner) {
            try {
                sessionStorage.setItem('sidebar_scroll_pos', sidebarInner.scrollTop);
            } catch (e) {}
        }

        // Immediate tactile response
        startProgress();
        syncSidebarState(targetUrl);
        document.body.classList.add('is-navigating');

        // Close mobile drawer if open
        if (window.jQuery) {
            window.jQuery('html').removeClass('layout-menu-expanded');
        }

        // Native browser navigation proceeds immediately without artificial wait
    }, { capture: true });

    // Top progress for GET form submissions
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form || form.tagName !== 'FORM') return;
        if (form.closest('.modal')) return;

        const method = (form.getAttribute('method') || 'GET').toUpperCase();
        if (method === 'GET') {
            startProgress();
        }
    });

    // BFCache & Page Visibility Handling
    window.addEventListener('pageshow', function(e) {
        finishProgress();
        const sidebarInner = document.querySelector('#layout-menu .menu-inner');
        if (sidebarInner) {
            try {
                const savedPos = sessionStorage.getItem('sidebar_scroll_pos');
                if (savedPos !== null) {
                    sidebarInner.scrollTop = parseInt(savedPos, 10);
                }
            } catch (e) {}
        }
    });

    window.addEventListener('pagehide', function() {
        const sidebarInner = document.querySelector('#layout-menu .menu-inner');
        if (sidebarInner) {
            try {
                sessionStorage.setItem('sidebar_scroll_pos', sidebarInner.scrollTop);
            } catch (e) {}
        }
        finishProgress();
    });

    function initSidebarScrollKeeper() {
        const sidebarInner = document.querySelector('#layout-menu .menu-inner');
        if (!sidebarInner || sidebarInner._scrollBound) return;
        sidebarInner._scrollBound = true;

        try {
            const savedPos = sessionStorage.getItem('sidebar_scroll_pos');
            if (savedPos !== null) {
                sidebarInner.scrollTop = parseInt(savedPos, 10);
            }
        } catch (e) {}

        sidebarInner.addEventListener('scroll', function() {
            try {
                sessionStorage.setItem('sidebar_scroll_pos', sidebarInner.scrollTop);
            } catch (e) {}
        }, { passive: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSidebarScrollKeeper);
    } else {
        initSidebarScrollKeeper();
    }

    if (document.readyState === 'complete') {
        finishProgress();
    } else {
        window.addEventListener('load', finishProgress);
    }

    // Export API
    window.AppNavigator = {
        start: startProgress,
        finish: finishProgress,
        syncSidebar: syncSidebarState
    };

})();
