/**
 * Universal Admin Table Live Search
 * Provides instant live filtering as the user types without requiring Enter or button clicks.
 * ZERO cursor jump: The search input is NEVER replaced or re-rendered in the DOM.
 */

(function () {
    let currentAbortController = null;
    let debounceTimer = null;

    function initTableLiveSearch() {
        // Target admin filter toolbar forms
        const filterForms = document.querySelectorAll('.admin-filter-toolbar form, form[id*="filter"], form[id*="Filter"]');

        filterForms.forEach(form => {
            if (form.dataset.liveSearchAttached) return;
            form.dataset.liveSearchAttached = 'true';

            // Find search inputs
            const searchInputs = form.querySelectorAll('input[type="text"]:not([readonly]):not([disabled]), input[type="search"]');
            if (!searchInputs.length) return;

            // 1. Live input on search inputs (NO Enter search needed)
            searchInputs.forEach(input => {
                // Prevent Enter key from triggering search / submitting form
                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.keyCode === 13) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                });

                input.addEventListener('input', function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        executeLiveSearch(form, input);
                    }, 280); // 280ms debounce: ultra-fast and zero flicker
                });
            });

            // 2. Form submit -> Only execute if explicitly clicked via submit button, NOT via Enter key
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                // Ignore submit triggered by Enter keypress on input fields
                if (!e.submitter || (!e.submitter.matches('button, [type="submit"]') && e.submitter.id !== 'btnSearch')) {
                    return false;
                }
                clearTimeout(debounceTimer);
                executeLiveSearch(form);
            });

            // 3. Dropdowns change listener
            const selects = form.querySelectorAll('select');
            selects.forEach(sel => {
                sel.addEventListener('change', function () {
                    clearTimeout(debounceTimer);
                    executeLiveSearch(form);
                });
            });
        });

        // Universal safeguard: Block Enter key on all table search/filter inputs across the entire DOM
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                const target = e.target;
                if (!target || target.tagName !== 'INPUT') return;

                const isTableSearch = target.type === 'search' ||
                    target.closest('.admin-filter-toolbar, form[id*="filter" i], .table-search, .admin-table-search') ||
                    target.matches('input[name*="nama" i], input[name*="search" i], input[name*="cari" i], input[placeholder*="cari" i], input[placeholder*="search" i]');

                if (isTableSearch) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            }
        }, true); // Capture phase: intercepts BEFORE form submit event can be generated

        // 4. Delegate pagination clicks within document
        document.addEventListener('click', function (e) {
            const pageLink = e.target.closest('.pagination a.page-link, .pagination a');
            if (!pageLink) return;

            const url = pageLink.getAttribute('href');
            if (!url || url === '#' || url.startsWith('javascript:')) return;

            e.preventDefault();
            loadTableFromUrl(url, null, true);
        });
    }

    async function executeLiveSearch(form, activeInput = null) {
        if (currentAbortController) {
            currentAbortController.abort();
        }
        currentAbortController = new AbortController();

        const formData = new FormData(form);
        const params = new URLSearchParams();

        for (const [key, val] of formData.entries()) {
            if (val !== '') {
                params.append(key, val);
            }
        }

        const actionUrl = form.getAttribute('action') || window.location.pathname;
        const targetUrl = actionUrl + (params.toString() ? '?' + params.toString() : '');

        await loadTableFromUrl(targetUrl, currentAbortController.signal, false, form);
    }

    async function loadTableFromUrl(url, signal = null, scrollToTop = false, form = null) {
        const tableWrap = document.querySelector('.table-karyawan-wrapper, .table-responsive, table.table');
        if (!tableWrap) return;

        tableWrap.classList.add('table-live-loading');

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: signal
            });

            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            const html = await response.text();
            const parser = new DOMParser();
            const newDoc = parser.parseFromString(html, 'text/html');

            // 1. Update Table Rows (tbody) - keeps table and headers 100% stable
            const curTbody = document.querySelector('table tbody');
            const newTbody = newDoc.querySelector('table tbody');
            if (curTbody && newTbody) {
                curTbody.innerHTML = newTbody.innerHTML;
            } else {
                const curTable = document.querySelector('.table-karyawan-wrapper, .table-responsive');
                const newTable = newDoc.querySelector('.table-karyawan-wrapper, .table-responsive');
                if (curTable && newTable) {
                    curTable.innerHTML = newTable.innerHTML;
                }
            }

            // 2. Update Pagination
            const curPag = document.querySelector('.pagination');
            const newPag = newDoc.querySelector('.pagination');
            if (curPag && newPag) {
                const curPagContainer = curPag.closest('.card') || curPag.parentElement;
                const newPagContainer = newPag.closest('.card') || newPag.parentElement;
                if (curPagContainer && newPagContainer) {
                    curPagContainer.innerHTML = newPagContainer.innerHTML;
                }
            } else if (curPag && !newPag) {
                const curPagContainer = curPag.closest('.card') || curPag.parentElement;
                if (curPagContainer) curPagContainer.innerHTML = '';
            } else if (!curPag && newPag) {
                const newPagContainer = newPag.closest('.card') || newPag.parentElement;
                const tableSection = document.querySelector('.table-karyawan-wrapper, .table-responsive')?.parentElement;
                if (tableSection && newPagContainer) {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = newPagContainer.outerHTML;
                    tableSection.appendChild(tempDiv.firstElementChild);
                }
            }

            // 3. Update Total Count Badge
            const curBadge = document.querySelector('.page-title .badge, .header-title-group .badge');
            const newBadge = newDoc.querySelector('.page-title .badge, .header-title-group .badge');
            if (curBadge && newBadge) {
                curBadge.innerHTML = newBadge.innerHTML;
            }

            // 4. Update Reset Button in Filter Toolbar if present
            if (form) {
                const curActionGroup = form.querySelector('.col-auto .d-flex');
                const newActionGroup = newDoc.querySelector('.col-auto .d-flex');
                if (curActionGroup && newActionGroup) {
                    curActionGroup.innerHTML = newActionGroup.innerHTML;
                }
            }

            // 5. Update browser URL silently
            window.history.replaceState(null, '', url);

            // 6. Smooth scroll if requested (pagination clicks)
            if (scrollToTop && tableWrap) {
                tableWrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

        } catch (err) {
            if (err.name === 'AbortError') return; // Cancelled as expected
            console.warn('[TableLiveSearch] Error:', err);
        } finally {
            tableWrap.classList.remove('table-live-loading');
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTableLiveSearch);
    } else {
        initTableLiveSearch();
    }

    window.addEventListener('popstate', () => {
        loadTableFromUrl(window.location.href);
    });

    window.__initTableLiveSearch = initTableLiveSearch;
})();
