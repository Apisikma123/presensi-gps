@php
    $helpPages = config('admin_help.pages', []);
@endphp

@once
    <link rel="stylesheet" href="{{ asset('/assets/vendor/fonts/tabler-icons.css') }}" />
@endonce

<!-- Global Help Offcanvas Drawer (Enterprise UI - 100% Theme Dynamic, Single Scrollbar, Pre-rendered Blade View) -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasHelp" aria-labelledby="offcanvasHelpLabel"
    data-bs-backdrop="false" data-bs-scroll="false"
    style="width: 540px; max-width: 95vw; z-index: 1075; border-left: 1px solid #E2E8F0; box-shadow: -8px 0 32px rgba(15, 23, 42, 0.1); background: #F8FAFC; overscroll-behavior: contain !important;">
    
    <!-- Drawer Header -->
    <div class="offcanvas-header border-bottom py-3 px-4 bg-white sticky-top">
        <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                style="width: 38px; height: 38px; background: rgba(var(--bs-primary-rgb, 60, 42, 33), 0.08); color: var(--theme-color-1, #3C2A21); border: 1px solid rgba(var(--bs-primary-rgb, 60, 42, 33), 0.18);">
                <i class="ti ti-help-hexagon" style="font-size: 20px;"></i>
            </div>
            <div class="min-w-0">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="offcanvas-title mb-0 fw-bold text-dark" id="offcanvasHelpLabel" style="font-size: 15px; letter-spacing: -0.01em;">
                        Pusat Bantuan & Panduan
                    </h6>
                    <span class="badge" id="helpRoleBadge" style="background: rgba(var(--bs-primary-rgb, 60, 42, 33), 0.08); color: var(--theme-color-1, #3C2A21); border: 1px solid rgba(var(--bs-primary-rgb, 60, 42, 33), 0.2); font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.05em;">
                        Panduan Karyawan
                    </span>
                </div>
                <small class="text-muted text-truncate d-block" id="helpCurrentPageSubtitle" style="font-size: 11.5px; margin-top: 1px;">
                    Petunjuk lengkap presensi masuk/pulang, shift tugas, izin, dan kendala operasional staf
                </small>
            </div>
        </div>
        <button type="button" class="btn-close text-reset ms-2 flex-shrink-0" data-bs-dismiss="offcanvas" onclick="closeHelpDrawer()" aria-label="Tutup Bantuan" title="Tutup Bantuan (Esc)"></button>
    </div>

    <!-- Topic / Role Navigation Bar (DESIGN.md Clean Architecture) -->
    <div class="px-4 pt-3 pb-2 bg-white border-bottom flex-shrink-0">
        <!-- Quick Switcher Tabs -->
        <div class="d-flex align-items-center gap-1 p-1 rounded-3 bg-light border mb-2" id="helpTabsBar" style="font-size: 12px;">
            <button type="button" class="btn btn-sm flex-fill fw-semibold py-1.5 px-2 rounded-2 text-center help-nav-pill" id="tabCurrentPage">
                <i class="ti ti-layout-grid me-1"></i>Halaman Ini
            </button>
            <button type="button" class="btn btn-sm flex-fill fw-semibold py-1.5 px-2 rounded-2 text-center help-nav-pill active" id="tabKaryawan">
                <i class="ti ti-user me-1"></i>Karyawan
            </button>
            <button type="button" class="btn btn-sm flex-fill fw-semibold py-1.5 px-2 rounded-2 text-center help-nav-pill" id="tabAdmin">
                <i class="ti ti-calendar-event me-1"></i>Admin & Roster
            </button>
        </div>

        <!-- Topic Dropdown Selector -->
        <div class="d-flex align-items-center gap-2">
            <label for="helpTopicSelect" class="text-muted small fw-medium flex-shrink-0" style="font-size: 11.5px;">Topik:</label>
            <select class="form-select form-select-sm" id="helpTopicSelect" style="font-size: 12px; border-radius: 8px; border-color: #CBD5E1; color: #1E293B;">
                <optgroup label="Panduan Alur Lengkap">
                    <option value="panduan_karyawan" selected>Panduan Operasional Karyawan</option>
                    <option value="panduan_admin_roster">Panduan Roster & Supervisor Admin</option>
                </optgroup>
                <optgroup label="Operasional Kehadiran">
                    <option value="dashboard">Dashboard Presensi</option>
                    @if(module_enabled('attendance'))
                    <option value="presensi">Monitoring Presensi Harian</option>
                    <option value="trackingpresensi">Live Tracking GPS Presensi</option>
                    <option value="harilibur">Hari Libur & Tanggal Merah</option>
                    <option value="jamkerja">Shift & Jam Kerja</option>
                    @endif
                </optgroup>
                <optgroup label="Data Master & Kepegawaian">
                    <option value="karyawan">Data Karyawan & Penugasan</option>
                    <option value="karyawan_detail">Detail Profil{{ module_enabled('face_recognition') ? ' & Biometrik Wajah' : '' }}</option>
                    <option value="cabang">Outlet & Cabang Kantor</option>
                    <option value="departemen">Departemen / Divisi</option>
                    <option value="jabatan">Jabatan Pekerjaan</option>
                    @if(module_enabled('leave'))
                    <option value="cuti">Jenis Cuti & Kuota</option>
                    @endif
                </optgroup>
                <optgroup label="Pengajuan & Persetujuan">
                    @if(module_enabled('leave'))
                    <option value="izinabsen">Persetujuan Izin Absen</option>
                    <option value="izinsakit">Persetujuan Izin Sakit (SID)</option>
                    <option value="izincuti">Persetujuan Cuti Karyawan</option>
                    @endif
                    @if(module_enabled('attendance'))
                    <option value="dispensasi">Dispensasi Keterlambatan</option>
                    @endif
                </optgroup>
                <optgroup label="Laporan & Sistem">
                    @if(module_enabled('attendance'))
                    <option value="laporan_presensi">Laporan Multi-Cabang Presensi</option>
                    @endif
                    @if(module_enabled('leave'))
                    <option value="laporan_cuti">Laporan Rekap Cuti</option>
                    @endif
                    <option value="generalsetting">Pengaturan Umum Sistem</option>
                    <option value="users">Manajemen Pengguna Admin</option>
                    <option value="permissiongroups">Grup Hak Akses</option>
                    <option value="permissions">Daftar Izin & Hak Akses</option>
                    <option value="backup">Backup & Restore Database</option>
                </optgroup>
            </select>
        </div>
    </div>

    <!-- Drawer Content Body -->
    <div class="offcanvas-body p-4" id="helpDrawerBody" style="overflow-y: auto; background: #F8FAFC;">
        <!-- Lazy-loaded Help Drawer Body: 0KB on page load, loaded on demand on open -->
        <div id="helpContentContainer" data-loaded="false">
            <div id="helpLoadingPlaceholder" class="p-4 text-center text-muted" style="font-size: 13px;">
                <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                <div>Memuat panduan sistem...</div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Offcanvas universal sliding & placement */
    #offcanvasHelp {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        transform: translateX(100%);
        transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.28s ease;
        visibility: hidden;
        display: flex;
        flex-direction: column;
    }
    #offcanvasHelp.show {
        transform: translateX(0) !important;
        visibility: visible !important;
    }

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

    /* Topic Pill Buttons */
    .help-nav-pill {
        background: transparent;
        border: none;
        color: #64748B;
        transition: all 0.15s ease;
    }
    .help-nav-pill:hover {
        color: #1E293B;
    }
    .help-nav-pill.active {
        background: #FFFFFF !important;
        color: var(--theme-color-1, #3C2A21) !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    /* Help Cards (DESIGN.md Surface Hierarchy) */
    .help-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 1.15rem;
        margin-bottom: 0.85rem;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }
    .help-card-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748B;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .help-card-label i {
        font-size: 14px;
        color: var(--theme-color-1, #3C2A21);
    }
    .help-step-number {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: rgba(var(--bs-primary-rgb, 60, 42, 33), 0.08);
        color: var(--theme-color-1, #3C2A21);
        font-size: 11px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid rgba(var(--bs-primary-rgb, 60, 42, 33), 0.2);
    }
    .help-comp-box {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        padding: 0.65rem 0.85rem;
        margin-bottom: 0.5rem;
    }
    .help-comp-box:last-child {
        margin-bottom: 0;
    }
    .help-comp-title {
        font-size: 12.5px;
        font-weight: 600;
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
        background: var(--theme-color-1, #3C2A21);
        flex-shrink: 0;
    }
    .help-comp-text {
        font-size: 11.5px;
        line-height: 1.45;
        color: #475569;
        margin-bottom: 0;
    }
    .help-box-tip {
        background: rgba(var(--bs-primary-rgb, 60, 42, 33), 0.04);
        border: 1px solid rgba(var(--bs-primary-rgb, 60, 42, 33), 0.15);
        border-radius: 12px;
        padding: 1rem 1.15rem;
        margin-bottom: 0.85rem;
    }
    .help-box-tip .tip-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--theme-color-1, #3C2A21);
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

    let currentDetectedKey = 'panduan_karyawan';
    let viewingKey = 'panduan_karyawan';

    // Cleanup any stray backdrop immediately so the page is NEVER dimmed
    function purgeStrayBackdrops() {
        document.querySelectorAll('.offcanvas-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
    }

    // Route / Path Resolver
    function resolveHelpKey(path) {
        let cleanPath = (path || window.location.pathname || '').replace(/^\/+|\/+$/g, '').toLowerCase();

        // 1. Specific sub-pages check first
        if (cleanPath.includes('setjamkerja')) {
            return 'panduan_admin_roster';
        }
        if (cleanPath.startsWith('karyawan/') && cleanPath.endsWith('/show')) {
            return 'karyawan_detail';
        }
        if (cleanPath.startsWith('karyawan/daftarkan-wajah') || cleanPath.startsWith('facerecognition')) {
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
        if (cleanPath.startsWith('users')) {
            return 'users';
        }
        if (cleanPath.startsWith('dispensasi')) {
            return 'dispensasi';
        }
        if (cleanPath.startsWith('harilibur')) {
            return 'harilibur';
        }
        if (cleanPath.startsWith('jamkerja')) {
            return 'jamkerja';
        }
        if (cleanPath.startsWith('izinabsen')) {
            return 'izinabsen';
        }
        if (cleanPath.startsWith('izinsakit')) {
            return 'izinsakit';
        }
        if (cleanPath.startsWith('izincuti')) {
            return 'izincuti';
        }
        if (cleanPath.startsWith('cabang')) {
            return 'cabang';
        }
        if (cleanPath.startsWith('departemen')) {
            return 'departemen';
        }
        if (cleanPath.startsWith('jabatan')) {
            return 'jabatan';
        }
        if (cleanPath.startsWith('cuti')) {
            return 'cuti';
        }
        if (cleanPath.startsWith('backup')) {
            return 'backup';
        }

        // 2. Base segments check
        const segments = cleanPath.split('/');
        const firstSegment = segments[0] || 'dashboard';

        const directTarget = document.getElementById('help-topic-' + firstSegment);
        if (directTarget) {
            return firstSegment;
        }

        return 'panduan_karyawan';
    }

    // Sync Tabs and Dropdown UI
    function updateTopicNavigationUI(key) {
        // Dropdown
        const selectEl = document.getElementById('helpTopicSelect');
        if (selectEl && selectEl.value !== key) {
            selectEl.value = key;
        }

        // Tabs pills active state
        const tabCurrent = document.getElementById('tabCurrentPage');
        const tabKaryawan = document.getElementById('tabKaryawan');
        const tabAdmin = document.getElementById('tabAdmin');

        if (tabCurrent) tabCurrent.classList.remove('active');
        if (tabKaryawan) tabKaryawan.classList.remove('active');
        if (tabAdmin) tabAdmin.classList.remove('active');

        if (key === 'panduan_karyawan') {
            if (tabKaryawan) tabKaryawan.classList.add('active');
        } else if (key === 'panduan_admin_roster') {
            if (tabAdmin) tabAdmin.classList.add('active');
        } else if (key === currentDetectedKey) {
            if (tabCurrent) tabCurrent.classList.add('active');
        }
    }

    // Render Help Topic: Ultra-fast Toggle of Blade Pre-rendered Section
    function renderHelp(key) {
        let targetEl = document.getElementById('help-topic-' + key);
        if (!targetEl) {
            key = 'panduan_karyawan';
            targetEl = document.getElementById('help-topic-panduan_karyawan');
        }
        viewingKey = key;

        updateTopicNavigationUI(key);

        // Hide all topics, show target
        document.querySelectorAll('.help-topic-section').forEach(el => el.classList.add('d-none'));
        if (targetEl) {
            targetEl.classList.remove('d-none');

            // Header metadata update
            const subtitle = targetEl.getAttribute('data-subtitle');
            const badge = targetEl.getAttribute('data-badge');

            const subtitleEl = document.getElementById('helpCurrentPageSubtitle');
            if (subtitleEl && subtitle) subtitleEl.textContent = subtitle;

            const roleBadgeEl = document.getElementById('helpRoleBadge');
            if (roleBadgeEl && badge) roleBadgeEl.textContent = badge;
        }

        // Scroll drawer body to top
        const bodyEl = document.getElementById('helpDrawerBody');
        if (bodyEl) bodyEl.scrollTop = 0;
    }

    // Tab Switcher Handler
    function switchHelpTab(tabKey) {
        if (tabKey === 'current') {
            renderHelp(currentDetectedKey);
        } else {
            renderHelp(tabKey);
        }
    }

    // Dropdown Switcher Handler
    function switchHelpTopic(selectedKey) {
        if (selectedKey) {
            renderHelp(selectedKey);
        }
    }

    let isHelpLoading = false;
    function loadHelpContent(callback) {
        const container = document.getElementById('helpContentContainer');
        if (!container) return;
        if (container.getAttribute('data-loaded') === 'true') {
            if (callback) callback();
            return;
        }
        if (isHelpLoading) return;
        isHelpLoading = true;

        fetch('{{ route("help.drawer-content") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            container.innerHTML = html;
            container.setAttribute('data-loaded', 'true');
            isHelpLoading = false;
            if (callback) callback();
        })
        .catch(err => {
            isHelpLoading = false;
            container.innerHTML = '<div class="p-4 text-center text-danger" style="font-size:12.5px;">Gagal memuat panduan. Silakan muat ulang halaman.</div>';
        });
    }

    // Global APIs for opening / closing drawer anywhere
    window.openHelpDrawer = function(topicKey) {
        const offcanvasEl = document.getElementById('offcanvasHelp');
        if (!offcanvasEl) return;

        purgeStrayBackdrops();
        document.body.classList.add('help-drawer-open');

        if (window.bootstrap && window.bootstrap.Offcanvas) {
            let bsOffcanvas = window.bootstrap.Offcanvas.getInstance(offcanvasEl);
            if (!bsOffcanvas) {
                bsOffcanvas = new window.bootstrap.Offcanvas(offcanvasEl, { backdrop: false, scroll: false });
            }
            bsOffcanvas.show();
        } else {
            offcanvasEl.classList.add('show');
            offcanvasEl.style.visibility = 'visible';
            offcanvasEl.style.transform = 'none';
        }

        const targetKey = topicKey || resolveHelpKey(window.location.pathname);
        currentDetectedKey = targetKey;
        loadHelpContent(function() {
            renderHelp(targetKey);
        });
        setTimeout(purgeStrayBackdrops, 50);
    };

    window.closeHelpDrawer = function() {
        const offcanvasEl = document.getElementById('offcanvasHelp');
        if (!offcanvasEl) return;

        if (window.bootstrap && window.bootstrap.Offcanvas) {
            let bsOffcanvas = window.bootstrap.Offcanvas.getInstance(offcanvasEl);
            if (bsOffcanvas) bsOffcanvas.hide();
        } else {
            offcanvasEl.classList.remove('show');
            offcanvasEl.style.transform = '';
            offcanvasEl.style.visibility = '';
        }

        document.body.classList.remove('help-drawer-open');
        purgeStrayBackdrops();
    };

    // Refresh context when drawer opens via standard Bootstrap triggers
    function onDrawerOpen() {
        purgeStrayBackdrops();
        document.body.classList.add('help-drawer-open');
        currentDetectedKey = resolveHelpKey(window.location.pathname);
        loadHelpContent(function() {
            renderHelp(currentDetectedKey);
        });
        setTimeout(purgeStrayBackdrops, 50);
    }

    function onDrawerClose() {
        document.body.classList.remove('help-drawer-open');
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
        });
        offcanvasEl.addEventListener('hidden.bs.offcanvas', onDrawerClose);

        // Bind Topic Switchers
        const tabCurrent = document.getElementById('tabCurrentPage');
        if (tabCurrent) tabCurrent.addEventListener('click', () => switchHelpTab('current'));

        const tabKaryawan = document.getElementById('tabKaryawan');
        if (tabKaryawan) tabKaryawan.addEventListener('click', () => switchHelpTab('panduan_karyawan'));

        const tabAdmin = document.getElementById('tabAdmin');
        if (tabAdmin) tabAdmin.addEventListener('click', () => switchHelpTab('panduan_admin_roster'));

        const selectTopic = document.getElementById('helpTopicSelect');
        if (selectTopic) selectTopic.addEventListener('change', (e) => switchHelpTopic(e.target.value));
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHelpDrawer);
    } else {
        initHelpDrawer();
    }

    // Dismiss on click outside or Escape
    document.addEventListener('click', function(e) {
        const offcanvasEl = document.getElementById('offcanvasHelp');
        if (!offcanvasEl || !offcanvasEl.classList.contains('show')) return;
        if (!offcanvasEl.contains(e.target) && !e.target.closest('[data-bs-target="#offcanvasHelp"]') && !e.target.closest('[onclick*="openHelpDrawer"]')) {
            window.closeHelpDrawer();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.closeHelpDrawer();
        }
    });

    // Also purge on navigation / state changes
    window.addEventListener('popstate', purgeStrayBackdrops);
})();
</script>
