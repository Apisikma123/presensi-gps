 <!-- Core CSS -->
 <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
 <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-semi-dark.css') }}" />
 <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

 <!-- Essential Vendors CSS -->
 <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
 <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
 <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
 <link rel="stylesheet" href="{{ asset('assets/vendor/css/toastr.min.css') }}" />
 <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
 <link rel="stylesheet" href="{{ asset('assets/vendor/libs/spinkit/spinkit.css') }}" />
 <link rel="stylesheet" href="{{ asset('assets/vendor/css/leaflet.css') }}" />


 <!-- Page & Minimalist Design System CSS -->
 <style>
     :root {
         /* Dynamic Theme Palette (Brew & Beam / Espresso Emerald) */
         --theme-color-1: {{ $general_setting->theme_color_1 ?? '#1E4D3E' }};
         --theme-color-2: {{ $general_setting->theme_color_2 ?? '#32745E' }};
         --theme-canvas: #EEF2F0; /* Calibrated Warm Stone Canvas with High Contrast vs White */
         --theme-surface: #FFFFFF; /* Pure Crisp Surface for all cards and tables */
         --theme-text-primary: #0F172A;
         --theme-text-secondary: #64748B;
         --theme-border: #E2E8F0;
         --theme-border-hover: #CBD5E1;
         
         --bs-primary: var(--theme-color-1);
         --bs-primary-rgb: 30, 77, 62;
     }

     html, body {
         background-color: var(--theme-canvas) !important;
         color: var(--theme-text-primary) !important;
         font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
         -webkit-font-smoothing: antialiased;
     }

     /* Global Card & Surface Definition - Zero Blending with Canvas */
     .card {
         background-color: #FFFFFF !important;
         border: 1px solid #E2E8F0 !important;
         box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04), 0 1px 2px rgba(15, 23, 42, 0.02) !important;
         border-radius: 12px !important;
     }

     .card-header {
         background-color: #FFFFFF !important;
         border-bottom: 1px solid #F1F5F9 !important;
     }

     .card-header[style*="var(--theme-color-1)"],
     .card-header[style*="var(--theme-color-1)"] *,
     .card-header[style*="background-color: var(--theme-color-1)"],
     .card-header[style*="background-color: var(--theme-color-1)"] *,
     .card-header.bg-primary,
     .card-header.bg-primary *,
     .card-header .text-white,
     .card-header .card-title.text-white {
         color: #FFFFFF !important;
     }

     .card-footer {
         background-color: #FAFAFA !important;
         border-top: 1px solid #F1F5F9 !important;
     }

     .content-wrapper, .layout-page, .layout-container, .layout-wrapper {
         background-color: var(--theme-canvas) !important;
     }

     .form-group {
         margin-bottom: 8px !important;
     }

     /* =======================================================
        GLOBAL ADMIN MOBILE RESPONSIVENESS SYSTEM (/ponytail)
        ======================================================= */

     /* 1. Global Viewport & Container Containment (No Horizontal Page Overflow) */
     html, body {
         max-width: 100vw !important;
         overflow-x: hidden !important;
         position: relative;
     }

     .layout-wrapper,
     .layout-container,
     .layout-page,
     .content-wrapper,
     .container-xxl,
     #spa-content-area {
         min-width: 0 !important;
         max-width: 100% !important;
         width: 100% !important;
         box-sizing: border-box !important;
     }

     /* 2. Global Table Responsiveness & Isolated Horizontal Scroll */
     .table-responsive {
         width: 100% !important;
         max-width: 100% !important;
         overflow-x: auto !important;
         -webkit-overflow-scrolling: touch !important;
         min-width: 0 !important;
         display: block !important;
         border-radius: 10px;
         margin-bottom: 1rem;
     }

     /* Custom touch scrollbar for table-responsive */
     .table-responsive::-webkit-scrollbar {
         height: 5px;
     }
     .table-responsive::-webkit-scrollbar-thumb {
         background: #CBD5E1;
         border-radius: 4px;
     }
     .table-responsive::-webkit-scrollbar-track {
         background: #F1F5F9;
     }

     /* Ensure data tables inside responsive container maintain readable tabular spacing */
     .table-responsive > .table {
         width: 100% !important;
         min-width: 650px;
         margin-bottom: 0 !important;
     }

     /* Keep simple 2-column or modal/card tables fluid without forced min-width */
     .table-sm,
     .table-compact,
     .modal .table,
     .card-body > .table:not(.table-responsive > .table) {
         min-width: 100% !important;
     }

     .table th, .table td {
         vertical-align: middle;
     }
     .table td .badge {
         white-space: nowrap;
     }

     /* 3. Global Pagination Responsiveness (Never overflows screen) */
     .pagination {
         display: flex !important;
         flex-wrap: wrap !important;
         justify-content: center !important;
         align-items: center !important;
         gap: 4px !important;
         margin-bottom: 0 !important;
         padding-left: 0 !important;
         list-style: none !important;
         max-width: 100% !important;
     }
     .page-item {
         margin: 0 !important;
     }
     .page-item .page-link {
         padding: 5px 9px !important;
         font-size: 12px !important;
         border-radius: 8px !important;
         min-width: 32px !important;
         height: 32px !important;
         display: inline-flex !important;
         align-items: center !important;
         justify-content: center !important;
     }

     /* 4. Action Buttons & Button Groups Flex Wrap */
     .table td .d-flex,
     .btn-group,
     .action-buttons {
         display: flex !important;
         flex-wrap: wrap !important;
         gap: 6px !important;
         align-items: center !important;
     }

     /* 5. Comprehensive Mobile Breakpoints (320px, 375px, 390px, 430px, 768px) */
     @media (max-width: 767.98px) {
         .container-xxl {
             padding-left: 12px !important;
             padding-right: 12px !important;
             padding-top: 12px !important;
         }

         .layout-navbar {
             padding: 0 14px !important;
             height: 62px !important;
             min-height: 62px !important;
             display: flex !important;
             align-items: center !important;
             margin-bottom: 12px !important;
         }

         .card {
             border-radius: 10px !important;
             margin-bottom: 12px !important;
         }
         .card-body {
             padding: 14px !important;
         }

         .nav-pills {
             scrollbar-width: none !important;
             -ms-overflow-style: none !important;
         }
         .nav-pills::-webkit-scrollbar {
             display: none !important;
         }

         /* Page Headers & Toolbars */
         .content-wrapper .d-flex.justify-content-between:not(.navbar *):not(.card-body *):not(.card-header *),
         .card-toolbar {
             flex-direction: column !important;
             align-items: flex-start !important;
             gap: 10px !important;
             width: 100% !important;
         }

         .content-wrapper .d-flex.justify-content-between:not(.navbar *):not(.card-body *) > .d-flex,
         .card-toolbar > .d-flex {
             width: 100% !important;
             flex-wrap: wrap !important;
             gap: 6px !important;
         }

         .content-wrapper .d-flex.justify-content-between:not(.navbar *):not(.card-body *) .btn,
         .card-toolbar .btn {
             flex: 1 1 auto !important;
             min-height: 36px !important;
             justify-content: center !important;
         }

         /* Global Search & Filter: Stack Vertically for field containers ONLY, NEVER for buttons */
         .card-filter-bar form > .d-flex:not(.input-group *),
         form[id*="filter"] > .d-flex:not(.input-group *),
         form[action*="index"] > .d-flex:not(.input-group *),
         .filter-wrapper form > .d-flex:not(.input-group *) {
             flex-direction: column !important;
             align-items: stretch !important;
             width: 100% !important;
             gap: 8px !important;
         }

         /* Buttons must ALWAYS stay horizontal with icon and text side-by-side */
         .btn,
         .btn.d-flex,
         button.d-flex,
         a.btn.d-flex,
         form .btn,
         form .btn.d-flex,
         form button[type="submit"] {
             flex-direction: row !important;
             display: inline-flex !important;
             align-items: center !important;
             justify-content: center !important;
             white-space: nowrap !important;
             gap: 6px !important;
         }

         .btn > *,
         .btn.d-flex > *,
         button.d-flex > *,
         form .btn > * {
             width: auto !important;
             max-width: none !important;
             flex-grow: 0 !important;
             display: inline-block !important;
         }

         /* Filter row action buttons container (search + reset): keep side-by-side */
         form div.d-flex.align-items-center,
         form div[class*="col-"] > .d-flex {
             flex-direction: row !important;
             display: flex !important;
             align-items: center !important;
         }

         /* Forms in grid rows inside filters */
         form[id*="filter"] .row > [class*="col-"],
         form[action*="index"] .row > [class*="col-"] {
             width: 100% !important;
             margin-bottom: 6px !important;
         }

         /* Modals on Mobile: Full screen margins & scrollable body */
         .modal-dialog {
             margin: 8px !important;
             max-width: calc(100% - 16px) !important;
             width: calc(100% - 16px) !important;
         }
         .modal-content {
             border-radius: 12px !important;
         }
         .modal-body {
             padding: 14px !important;
             max-height: calc(85vh - 120px) !important;
             overflow-y: auto !important;
             -webkit-overflow-scrolling: touch !important;
         }
         .modal-footer {
             flex-direction: column-reverse !important;
             gap: 8px !important;
             padding: 10px 14px !important;
         }
         .modal-footer > * {
             width: 100% !important;
             margin: 0 !important;
         }

         /* Dashboard Bento Grid: 2 columns on 375px-430px */
         .stat-grid-minimal {
             grid-template-columns: repeat(2, 1fr) !important;
             gap: 8px !important;
         }
         .stat-card-min {
             padding: 12px 10px !important;
             min-height: 100px !important;
         }
     }

     /* Extra Small Mobile (320px - 360px) */
     @media (max-width: 360px) {
         .stat-grid-minimal {
             grid-template-columns: 1fr !important;
         }
         .container-xxl {
             padding-left: 6px !important;
             padding-right: 6px !important;
         }
         .page-slider-container {
             flex-direction: column !important;
             align-items: flex-start !important;
         }
     }

     /* ===================================
        SIDEBAR NAVIGATION ENHANCEMENTS
        =================================== */
     #layout-menu {
         background: #11382C !important;
         color: #fff !important;
         border-right: 1px solid rgba(255, 255, 255, 0.06) !important;
         box-shadow: 2px 0 16px rgba(0, 0, 0, 0.08) !important;
     }

     #layout-menu .app-brand-link,
     #layout-menu .app-brand-text,
     #layout-menu .menu-link,
     #layout-menu .menu-link .menu-icon,
     #layout-menu .menu-header,
     #layout-menu .menu-sub .menu-link {
         color: rgba(255, 255, 255, 0.9) !important;
         transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
     }

     #layout-menu .menu-item:not(.menu-sub .menu-item) > .menu-link {
         border-radius: 10px;
         margin: 2px 12px;
         padding: 9px 14px;
     }

     #layout-menu .menu-sub .menu-item > .menu-link {
         border-radius: 8px;
         margin: 2px 12px 2px 14px;
         padding: 8px 12px 8px 40px !important;
         position: relative;
         font-size: 13px;
     }

     #layout-menu .menu-sub .menu-item > .menu-link::before {
         left: 18px !important;
         width: 5px !important;
         height: 5px !important;
         border-radius: 50% !important;
         background-color: rgba(255, 255, 255, 0.45) !important;
         content: "" !important;
         position: absolute !important;
         top: 50% !important;
         transform: translateY(-50%) !important;
         transition: all 0.2s ease;
     }

     #layout-menu .menu-item .menu-link:hover {
         background: rgba(255, 255, 255, 0.08) !important;
         color: #ffffff !important;
     }

     #layout-menu .menu-inner>.menu-item.active>.menu-link,
     #layout-menu .menu-sub>.menu-item.active>.menu-link {
         background: var(--theme-color-2, #32745E) !important;
         color: #ffffff !important;
         font-weight: 600;
         box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
     }

     #layout-menu .menu-sub>.menu-item.active>.menu-link::before,
     #layout-menu .menu-sub>.menu-item:hover>.menu-link::before {
         background-color: #ffffff !important;
         box-shadow: 0 0 6px rgba(255, 255, 255, 0.8) !important;
     }

     #layout-menu .menu-inner>.menu-item.open>.menu-link {
         background: rgba(255, 255, 255, 0.06) !important;
         color: #ffffff !important;
     }

     #layout-menu .menu-header {
         color: rgba(255, 255, 255, 0.45) !important;
         font-size: 10.5px !important;
         font-weight: 700 !important;
         letter-spacing: 0.08em !important;
         text-transform: uppercase !important;
         padding-left: 24px !important;
     }

     #layout-menu .menu-inner-shadow {
         display: none !important;
     }

     /* ===================================
        NAVBAR & DETACHED HEADER
        =================================== */
     .layout-navbar {
         background: rgba(255, 255, 255, 0.92) !important;
         backdrop-filter: blur(12px) !important;
         -webkit-backdrop-filter: blur(12px) !important;
         border: 1px solid rgba(15, 23, 42, 0.07) !important;
         border-radius: 16px !important;
         box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.04) !important;
         height: 62px !important;
         min-height: 62px !important;
         display: flex !important;
         align-items: center !important;
     }

     /* ===================================
        CARDS & SURFACE CONTAINERS
        =================================== */
     .card {
         background: #FFFFFF !important;
         border: 1px solid rgba(15, 23, 42, 0.08) !important;
         border-radius: 16px !important;
         box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px 0 rgba(0, 0, 0, 0.02) !important;
         transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
     }

     .card-header {
         background: transparent !important;
         border-bottom: 1px solid rgba(15, 23, 42, 0.06) !important;
         padding: 1.25rem 1.5rem !important;
     }

     .card-body {
         padding: 1.5rem !important;
     }

     .card-footer {
         background: transparent !important;
         border-top: 1px solid rgba(15, 23, 42, 0.06) !important;
     }

     /* ===================================
        TACTILE BUTTONS & ACTIONS
        =================================== */
     .btn {
         border-radius: 10px !important;
         font-weight: 600 !important;
         font-size: 13.5px !important;
         padding: 8px 16px !important;
         letter-spacing: 0.01em !important;
         transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1) !important;
     }

     .btn:active {
         transform: scale(0.98) !important;
     }

     .btn-primary {
         background-color: var(--theme-color-1, #1E4D3E) !important;
         border-color: var(--theme-color-1, #1E4D3E) !important;
         color: #FFFFFF !important;
         box-shadow: 0 2px 6px rgba(30, 77, 62, 0.2) !important;
     }

     .btn-primary:hover,
     .btn-primary:focus {
         background-color: var(--theme-color-2, #32745E) !important;
         border-color: var(--theme-color-2, #32745E) !important;
         color: #FFFFFF !important;
         box-shadow: 0 4px 12px rgba(30, 77, 62, 0.3) !important;
     }

     .btn-outline-primary {
         color: var(--theme-color-1, #1E4D3E) !important;
         border-color: rgba(30, 77, 62, 0.35) !important;
         background: transparent !important;
     }

     .btn-outline-primary:hover,
     .btn-outline-primary:focus {
         background-color: var(--theme-color-1, #1E4D3E) !important;
         border-color: var(--theme-color-1, #1E4D3E) !important;
         color: #FFFFFF !important;
     }

     .btn-outline-secondary {
         color: #475569 !important;
         border-color: #CBD5E1 !important;
         background: #FFFFFF !important;
     }

     .btn-outline-secondary:hover {
         background-color: #F1F5F9 !important;
         border-color: #94A3B8 !important;
         color: #0F172A !important;
     }

     /* ===================================
        FORM CONTROLS & INPUTS
        =================================== */
     .form-control,
     .form-select,
     .input-group-text {
         border-radius: 10px !important;
         border: 1px solid #CBD5E1 !important;
         background-color: #F8FAFC !important;
         font-size: 13.5px !important;
         color: #0F172A !important;
         padding: 8px 14px !important;
         transition: all 0.2s ease !important;
     }

     .form-control:focus,
     .form-select:focus {
         background-color: #FFFFFF !important;
         border-color: var(--theme-color-2, #32745E) !important;
         box-shadow: 0 0 0 3px rgba(50, 116, 94, 0.15) !important;
         outline: none !important;
     }

     .form-label {
         font-size: 12.5px !important;
         font-weight: 600 !important;
         color: #475569 !important;
         margin-bottom: 4px !important;
     }

     /* ===================================
        DATA TABLES & REFINED GRID
        =================================== */
     .table {
         color: #1E293B !important;
         border-color: rgba(15, 23, 42, 0.06) !important;
     }

     .table thead th {
         background-color: #F8FAFC !important;
         color: #64748B !important;
         font-size: 11px !important;
         font-weight: 700 !important;
         text-transform: uppercase !important;
         letter-spacing: 0.05em !important;
         border-bottom: 1px solid #E2E8F0 !important;
         padding: 12px 16px !important;
     }

     .table tbody td {
         padding: 12px 16px !important;
         vertical-align: middle !important;
         border-bottom: 1px solid rgba(15, 23, 42, 0.05) !important;
     }

     .table-hover tbody tr:hover {
         background-color: #F8FAF8 !important;
     }

     /* ===================================
        BADGES & STATUS PILLS
        =================================== */
     .badge {
         font-weight: 600 !important;
         letter-spacing: 0.02em !important;
         padding: 0.35rem 0.65rem !important;
         border-radius: 9999px !important;
     }

     .text-primary, a.text-primary, a.text-primary:visited, a.text-primary:hover {
         color: var(--theme-color-1, #1E4D3E) !important;
     }

     .bg-label-primary {
         background-color: rgba(30, 77, 62, 0.1) !important;
         color: var(--theme-color-1, #1E4D3E) !important;
     }

     .bg-label-success {
         background-color: #ECFDF5 !important;
         color: #059669 !important;
     }

     .bg-label-warning {
         background-color: #FFFBEB !important;
         color: #D97706 !important;
     }

     .bg-label-danger {
         background-color: #FEF2F2 !important;
         color: #DC2626 !important;
     }

     .bg-label-info {
         background-color: #F0FDF4 !important;
         color: #16A34A !important;
     }

     .bg-label-secondary {
         background-color: #F1F5F9 !important;
         color: #64748B !important;
     }

     /* ===================================
        SWEETALERT2 MODAL THEME
        =================================== */
     .swal2-container {
         z-index: 99999 !important;
         backdrop-filter: blur(6px) !important;
         -webkit-backdrop-filter: blur(6px) !important;
     }

     .swal2-popup {
         font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif !important;
         border-radius: 20px !important;
         padding: 24px 20px !important;
         box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
         background: #ffffff !important;
         border: 1px solid rgba(15, 23, 42, 0.08) !important;
     }

     .swal2-title {
         font-size: 18px !important;
         font-weight: 700 !important;
         color: #0F172A !important;
         margin-bottom: 8px !important;
     }

     .swal2-html-container {
         font-size: 13.5px !important;
         color: #64748B !important;
         line-height: 1.5 !important;
     }

     .swal2-confirm {
         background-color: var(--theme-color-1, #1E4D3E) !important;
         border-color: var(--theme-color-1, #1E4D3E) !important;
         border-radius: 10px !important;
         font-weight: 600 !important;
         font-size: 13.5px !important;
         padding: 10px 24px !important;
         box-shadow: 0 4px 14px rgba(30, 77, 62, 0.3) !important;
     }

     .swal2-confirm:hover, .swal2-confirm:focus {
         background-color: var(--theme-color-2, #32745E) !important;
         border-color: var(--theme-color-2, #32745E) !important;
         transform: translateY(-1px) !important;
     }

     .swal2-cancel {
         background-color: #F1F5F9 !important;
         color: #475569 !important;
         border: 1px solid #CBD5E1 !important;
         border-radius: 10px !important;
         font-weight: 600 !important;
         font-size: 13.5px !important;
         padding: 10px 22px !important;
     }

     /* Nav-pills dynamic color */
     .nav-pills .nav-link.active,
     .nav-pills .show > .nav-link {
         background-color: var(--theme-color-1, #1E4D3E) !important;
         color: #ffffff !important;
         border-radius: 10px !important;
     }

     /* Breadcrumbs */
     .breadcrumb-item a {
         color: #64748B !important;
         text-decoration: none !important;
     }

     .breadcrumb-item a:hover {
         color: var(--theme-color-1, #1E4D3E) !important;
     }

     .breadcrumb-item.active {
         color: #0F172A !important;
         font-weight: 600 !important;
     }

     /* ===================================
       GLOBAL MINIMALIST DATA TABLE SYSTEM
       =================================== */
    .table-responsive {
        overflow-x: auto;
        overflow-y: visible !important;
        border-radius: 12px;
    }

    table.table {
        margin-bottom: 0 !important;
        width: 100% !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
    }

    table.table thead,
    table.table thead th,
    .table-responsive thead th {
        background-color: #F8FAFC !important;
        color: #475569 !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        padding: 12px 16px !important;
        border-bottom: 1px solid #E2E8F0 !important;
        border-top: none !important;
        white-space: nowrap !important;
    }

    table.table tbody td {
        padding: 12px 16px !important;
        vertical-align: middle !important;
        border-bottom: 1px solid #F1F5F9 !important;
        border-top: none !important;
        font-size: 13px !important;
        color: #1E293B !important;
    }

    table.table-hover tbody tr:hover {
        background-color: #F8FAF8 !important;
        transition: background-color 0.15s ease !important;
    }

    /* Standard Card Header on Legacy Tables */
    .card .card-header[style*="background-color: var(--theme-color-1)"] {
        background: #FFFFFF !important;
        color: #0F172A !important;
        border-bottom: 1px solid #F1F5F9 !important;
    }

    .card .card-header[style*="background-color: var(--theme-color-1)"] h6,
    .card .card-header[style*="background-color: var(--theme-color-1)"] h5,
    .card .card-header[style*="background-color: var(--theme-color-1)"] i {
        color: #0F172A !important;
    }

    /* =========================================================
       GLOBAL TABLE MICRO-ACTION BUTTONS (CLEAN & MINIMALIST)
       ========================================================= */
    .d-inline-flex.border.rounded.overflow-hidden,
    table .btn-group,
    table td .btn-group,
    table td .d-inline-flex,
    table td .d-flex {
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }

    /* Minimalist Table Detail Action Button */
    .btn-table-detail {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        padding: 5px 12px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        color: #1E4D3E !important;
        background: #FFFFFF !important;
        border: 1px solid #CBD5E1 !important;
        border-radius: 6px !important;
        text-decoration: none !important;
        transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1) !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important;
        cursor: pointer !important;
    }

    .btn-table-detail:hover {
        background: #1E4D3E !important;
        color: #FFFFFF !important;
        border-color: #1E4D3E !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 3px 8px rgba(30, 77, 62, 0.25) !important;
    }

    .btn-table-detail i {
        font-size: 13px !important;
        transition: transform 0.15s ease !important;
    }

    .btn-table-detail:hover i {
        transform: translateX(2px) !important;
        color: #FFFFFF !important;
    }

    /* Individual Micro Action Button - Edit */
    table td .btnEdit,
    table td .btn-action-edit,
    table td a[title="Edit"],
    table td button[title="Edit"],
    table td .btn-outline-secondary,
    table td .btn-label-primary,
    table td .editCabang {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 30px !important;
        height: 30px !important;
        min-width: 30px !important;
        padding: 0 !important;
        border-radius: 8px !important;
        border: 1px solid #E2E8F0 !important;
        background: #FFFFFF !important;
        color: #475569 !important;
        font-size: 14px !important;
        transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1) !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important;
        cursor: pointer !important;
    }

    table td .btnEdit:hover,
    table td .btn-action-edit:hover,
    table td a[title="Edit"]:hover,
    table td button[title="Edit"]:hover,
    table td .btn-outline-secondary:hover,
    table td .btn-label-primary:hover,
    table td .editCabang:hover {
        background: #F1F5F9 !important;
        border-color: #CBD5E1 !important;
        color: #0F172A !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 2px 4px rgba(15, 23, 42, 0.08) !important;
    }

    /* Individual Micro Action Button - Delete */
    table td .delete-confirm,
    table td a[title="Hapus"],
    table td button[title="Hapus"],
    table td .btn-outline-danger,
    table td .btn-label-danger {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 30px !important;
        height: 30px !important;
        min-width: 30px !important;
        padding: 0 !important;
        border-radius: 8px !important;
        border: 1px solid #FEE2E2 !important;
        background: #FFFFFF !important;
        color: #EF4444 !important;
        font-size: 14px !important;
        transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1) !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important;
        cursor: pointer !important;
    }

    table td .delete-confirm:hover,
    table td a[title="Hapus"]:hover,
    table td button[title="Hapus"]:hover,
    table td .btn-outline-danger:hover,
    table td .btn-label-danger:hover {
        background: #FEF2F2 !important;
        border-color: #FECACA !important;
        color: #DC2626 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 2px 4px rgba(220, 38, 38, 0.12) !important;
    }

    table td form.deleteform,
    table td form.d-inline {
        margin: 0 !important;
        display: inline-flex !important;
    }

    /* =========================================================
       GLOBAL UNIFIED INPUT GROUP & FILTER SYSTEM (36px STANDARD)
       ========================================================= */
    .card-filter-bar {
        border: 1px solid rgba(15, 23, 42, 0.08) !important;
        border-radius: 10px !important;
        background: #FFFFFF !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
    }

    .form-group {
        margin-bottom: 0 !important;
    }
    
    .form-group.mb-3 {
        margin-bottom: 1rem !important;
    }

    .form-label {
        font-size: 12.5px !important;
        font-weight: 600 !important;
        color: #334155 !important;
        margin-bottom: 5px !important;
        display: inline-block;
    }

    /* Outer Wrapper as a single seamless pill/card */
    .input-group,
    .input-group-merge {
        display: flex !important;
        align-items: stretch !important;
        width: 100% !important;
        height: 36px !important;
        border: 1px solid #E2E8F0 !important;
        border-radius: 8px !important;
        background-color: #FFFFFF !important;
        overflow: hidden !important;
        transition: all 0.15s ease !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
    }

    .input-group-sm {
        height: 32px !important;
        border-radius: 6px !important;
    }

    /* Focus State on the unified card */
    .input-group:focus-within,
    .input-group-merge:focus-within {
        border-color: var(--theme-color-1, #1E4D3E) !important;
        box-shadow: 0 0 0 3px rgba(30, 77, 62, 0.12) !important;
        background-color: #FFFFFF !important;
    }

    /* Icon inside unified card */
    .input-group > .input-group-text,
    .input-group-merge > .input-group-text,
    .input-group-text {
        border: none !important;
        background: transparent !important;
        background-color: transparent !important;
        color: #94A3B8 !important;
        padding: 0 4px 0 10px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 15px !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        transition: color 0.15s ease !important;
    }

    .input-group:focus-within > .input-group-text,
    .input-group-merge:focus-within > .input-group-text {
        color: var(--theme-color-1, #1E4D3E) !important;
    }

    /* Text Input inside unified card */
    .input-group > .form-control,
    .input-group-merge > .form-control {
        border: none !important;
        background: transparent !important;
        background-color: transparent !important;
        box-shadow: none !important;
        padding: 6px 12px 6px 6px !important;
        height: 34px !important;
        border-radius: 0 !important;
        font-size: 12.5px !important;
        color: #0F172A !important;
        flex: 1 1 auto !important;
    }

    .input-group-sm > .form-control {
        height: 30px !important;
        padding: 4px 8px 4px 4px !important;
        font-size: 12px !important;
    }

    .input-group > .form-control:focus,
    .input-group-merge > .form-control:focus {
        box-shadow: none !important;
        border: none !important;
        background: transparent !important;
        background-color: transparent !important;
        outline: none !important;
    }

    .input-group > .form-control::placeholder,
    .form-control::placeholder {
        color: #94A3B8 !important;
        font-size: 12.5px !important;
    }

    .input-group > .form-control[type="color"],
    .form-control[type="color"] {
        height: 36px !important;
        padding: 4px 8px !important;
        cursor: pointer !important;
        border-radius: 8px !important;
    }

    /* Buttons inside input groups */
    .input-group > .btn {
        border: none !important;
        border-radius: 0 !important;
        height: auto !important;
        margin: 0 !important;
        box-shadow: none !important;
    }

    /* Standalone Form Controls */
    .form-control:not(.input-group .form-control):not(.input-group-merge .form-control),
    .form-select {
        border: 1px solid #E2E8F0 !important;
        border-radius: 8px !important;
        background-color: #FFFFFF !important;
        color: #0F172A !important;
        height: 36px !important;
        padding: 6px 12px !important;
        font-size: 12.5px !important;
        transition: all 0.15s ease !important;
    }

    .form-control:not(.input-group .form-control):not(.input-group-merge .form-control):focus,
    .form-select:focus {
        border-color: var(--theme-color-1, #1E4D3E) !important;
        box-shadow: 0 0 0 3px rgba(30, 77, 62, 0.12) !important;
        background-color: #FFFFFF !important;
        outline: none !important;
    }

    /* Select2 Container Integration */
    .select2-container .select2-selection--single {
        height: 36px !important;
        border: 1px solid #E2E8F0 !important;
        border-radius: 8px !important;
        background-color: #FFFFFF !important;
        display: flex !important;
        align-items: center !important;
    }

    .select2-container--bootstrap-5 .select2-selection--single,
    .select2-container--default .select2-selection--single {
        height: 36px !important;
        border: 1px solid #E2E8F0 !important;
        border-radius: 8px !important;
        background-color: #FFFFFF !important;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        color: #0F172A !important;
        font-size: 12.5px !important;
        line-height: 34px !important;
        padding-left: 12px !important;
    }

    .select2-container .select2-selection--single .select2-selection__arrow {
        height: 34px !important;
        right: 8px !important;
    }

    /* Standardize All Search, Filter, and Action Buttons in Filter Rows */
    form[action*="index"] button[type="submit"],
    form[action*="presensi"] button[type="submit"],
    .filter-card button[type="submit"],
    .filter-card .btn,
    .card-body form .btn {
        height: 36px !important;
        display: inline-flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: center !important;
        white-space: nowrap !important;
        font-size: 12.5px !important;
        font-weight: 600 !important;
        border-radius: 8px !important;
        gap: 6px !important;
        padding: 0 14px !important;
    }

    /* Table micro action buttons remain untouched at 30x30px */
    table td .btn-table-detail {
        height: auto !important;
    }
    table td .btnEdit,
    table td .delete-confirm {
        height: 30px !important;
    }

    /* Utility Monospace Data */
    .font-mono {
        font-family: 'JetBrains Mono', monospace !important;
    }

    /* ==========================================================================
       BREW & BEAM DESIGN SYSTEM: TABS & NAVIGATION (DESIGN.md)
       Primary Brand: Espresso Emerald (#1E4D3E)
       Primary Accent: Jade Roast (#32745E)
       Banned: Generic Bootstrap Blue (#0d6efd / #1a6bd1)
       ========================================================================== */
    .nav-tabs,
    .nav-align-top .nav-tabs {
        border-bottom: 2px solid #E2E8F0 !important;
        background: transparent !important;
        gap: 4px;
        padding-left: 2px;
    }

    .nav-tabs .nav-item {
        margin-bottom: -2px;
    }

    .nav-tabs .nav-link,
    .nav-align-top .nav-tabs .nav-link {
        color: var(--theme-text-secondary, #64748B) !important;
        font-weight: 500 !important;
        font-size: 13.5px !important;
        border: none !important;
        border-bottom: 2.5px solid transparent !important;
        background: transparent !important;
        padding: 0.65rem 1.15rem !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        border-radius: 8px 8px 0 0 !important;
        box-shadow: none !important;
    }

    .nav-tabs .nav-link i,
    .nav-tabs .nav-link .tf-icons,
    .nav-align-top .nav-tabs .nav-link i,
    .nav-align-top .nav-tabs .nav-link .tf-icons {
        color: var(--theme-text-secondary, #64748B) !important;
        transition: color 0.2s ease !important;
    }

    .nav-tabs .nav-link:hover,
    .nav-align-top .nav-tabs .nav-link:hover {
        color: var(--theme-color-2, #32745E) !important;
        background-color: rgba(30, 77, 62, 0.05) !important;
        border-bottom-color: rgba(30, 77, 62, 0.35) !important;
    }

    .nav-tabs .nav-link:hover i,
    .nav-tabs .nav-link:hover .tf-icons,
    .nav-align-top .nav-tabs .nav-link:hover i,
    .nav-align-top .nav-tabs .nav-link:hover .tf-icons {
        color: var(--theme-color-2, #32745E) !important;
    }

    /* Active Tab: Espresso Emerald Indicator */
    .nav-tabs .nav-link.active,
    .nav-tabs .nav-item.show .nav-link,
    .nav-align-top .nav-tabs .nav-link.active,
    .nav-align-top .nav-tabs .nav-link.active:hover,
    .nav-align-top .nav-tabs .nav-link.active:focus {
        color: var(--theme-color-1, #1E4D3E) !important;
        font-weight: 600 !important;
        background-color: #FFFFFF !important;
        border: none !important;
        border-bottom: 2.5px solid var(--theme-color-1, #1E4D3E) !important;
        box-shadow: 0 -2.5px 0 0 var(--theme-color-1, #1E4D3E) inset !important;
    }

    .nav-tabs .nav-link.active i,
    .nav-tabs .nav-link.active .tf-icons,
    .nav-align-top .nav-tabs .nav-link.active i,
    .nav-align-top .nav-tabs .nav-link.active .tf-icons {
        color: var(--theme-color-1, #1E4D3E) !important;
    }

    /* Nav Pills System */
    .nav-pills .nav-link {
        color: var(--theme-text-secondary, #64748B) !important;
        font-weight: 500 !important;
        border-radius: 8px !important;
        transition: all 0.2s ease !important;
    }

    .nav-pills .nav-link:hover {
        color: var(--theme-color-1, #1E4D3E) !important;
        background-color: rgba(30, 77, 62, 0.06) !important;
    }

    .nav-pills .nav-link.active,
    .nav-pills .nav-link.active:hover,
    .nav-pills .nav-link.active:focus {
        background-color: var(--theme-color-1, #1E4D3E) !important;
        color: #FFFFFF !important;
        box-shadow: 0 2px 6px rgba(30, 77, 62, 0.2) !important;
    }

    .nav-pills .nav-link.active i,
    .nav-pills .nav-link.active .tf-icons {
        color: #FFFFFF !important;
    }

    /* Primary buttons matching DESIGN.md */
    .btn-primary {
        background-color: var(--theme-color-1, #1E4D3E) !important;
        border-color: #163C30 !important;
        color: #FFFFFF !important;
    }

    .btn-primary:hover,
    .btn-primary:focus,
    .btn-primary:active {
        background-color: var(--theme-color-2, #32745E) !important;
        border-color: #27624F !important;
        color: #FFFFFF !important;
    }

    /* Pagination active indicator matching DESIGN.md */
    .page-item.active .page-link,
    .pagination .active > .page-link {
        background-color: var(--theme-color-1, #1E4D3E) !important;
        border-color: var(--theme-color-1, #1E4D3E) !important;
        color: #FFFFFF !important;
        box-shadow: 0 2px 4px rgba(30, 77, 62, 0.2) !important;
    }

    /* Form Checkbox & Radios */
    .form-check-input:checked {
        background-color: var(--theme-color-1, #1E4D3E) !important;
        border-color: var(--theme-color-1, #1E4D3E) !important;
    }
</style>
