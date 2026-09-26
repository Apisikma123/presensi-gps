<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="{{ $t['primary'] }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>

    {{-- Template CSS & Theme --}}
    <link rel="stylesheet" href="{{ asset('assets/template/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme-custom.css') }}?v={{ file_exists(public_path('assets/css/theme-custom.css')) ? filemtime(public_path('assets/css/theme-custom.css')) : time() }}" />
    <script src="{{ asset('assets/external/js/sweetalert2@11.js') }}"></script>

    {{-- Tailwind & App CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --color-primary: {{ $t['primary'] ?? '#3C2A21' }};
            --color-primary-hover: {{ $theme['primary_hover'] ?? '#2A1D17' }};
            --color-primary-soft: {{ $theme['primary_soft'] ?? 'rgba(60, 42, 33, 0.08)' }};
            --color-primary-contrast: {{ $t['primary_contrast'] ?? '#FFFFFF' }};
            --color-primary-rgb: {{ $theme['primary_rgb'] ?? '60, 42, 33' }};
            --theme-color-1: {{ $t['primary'] ?? '#3C2A21' }};
            --theme-color-2: {{ $t['primary_light'] ?? $theme['secondary'] ?? '#634832' }};
            --theme-color-accent: {{ $theme['accent'] ?? '#4A6741' }};
            --theme-primary-contrast: {{ $t['primary_contrast'] ?? '#FFFFFF' }};
            --bs-primary: var(--theme-color-1);
            --bs-primary-rgb: {{ $theme['primary_rgb'] ?? '60, 42, 33' }};
            --theme-color-2-rgb: {{ $theme['secondary_rgb'] ?? '99, 72, 50' }};
        }
        .swal2-confirm:not(.btn-danger):not(.bg-danger) {
            background-color: var(--theme-color-1) !important;
            border-color: var(--theme-color-1) !important;
            color: var(--theme-primary-contrast, #FFFFFF) !important;
        }
        .swal2-confirm:not(.btn-danger):not(.bg-danger):hover,
        .swal2-confirm:not(.btn-danger):not(.bg-danger):focus {
            background-color: var(--color-primary-hover, var(--theme-color-2)) !important;
            border-color: var(--color-primary-hover, var(--theme-color-2)) !important;
            color: var(--theme-primary-contrast, #FFFFFF) !important;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        a, a:visited {
            color: inherit;
            text-decoration: none;
        }
        button, [type='button'], [type='submit'] {
            border: none;
            outline: none;
            font-family: inherit;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff !important;
            -webkit-tap-highlight-color: transparent;
            margin: 0;
            padding: 0;
        }
        @media (min-width: 481px) {
            body {
                background-color: #FAF9F8 !important;
            }
        }
        #appCapsule {
            max-width: 480px !important;
            margin-left: auto !important;
            margin-right: auto !important;
            min-height: 100vh;
            background-color: #ffffff !important;
            box-shadow: 0 0 35px rgba(0, 0, 0, 0.04);
            position: relative;
        }
        .hero-bg {
            background-color: var(--color-primary, {{ $t['primary'] ?? '#3C2A21' }});
            border-bottom-left-radius: 22px;
            border-bottom-right-radius: 22px;
            position: relative;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
            color: #ffffff !important;
        }
        .hero-bg h1,
        .hero-bg h2,
        .hero-bg h3,
        .hero-bg h4,
        .hero-bg h5,
        .hero-bg h6,
        .hero-bg span,
        .hero-bg p,
        .hero-bg div,
        .hero-bg a {
            color: #ffffff !important;
        }
        .glass-icon, .glass-icon:visited {
            background: rgba(255, 255, 255, 0.14);
            border-radius: 12px;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #ffffff !important;
        }
        .glass-icon ion-icon {
            color: #ffffff !important;
        }
        .glass-icon:active { transform: scale(0.92); background: rgba(255, 255, 255, 0.22); }
        #jam {
            letter-spacing: -0.02em;
        }
        .fade-in {
            animation: fadeIn 0.4s ease-out forwards;
            opacity: 0;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Mobile Header Consistency */
        header, .appHeader-modern {
            padding-top: env(safe-area-inset-top) !important;
            background: var(--color-primary, {{ $t['primary'] ?? '#3C2A21' }}) !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 999 !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
        }
        header .left a, header .right a {
            width: 34px !important;
            height: 34px !important;
            border-radius: 12px !important;
            background: rgba(255, 255, 255, 0.14) !important;
            color: #ffffff !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        header .left a:active, header .right a:active {
            transform: scale(0.92) !important;
            background: rgba(255, 255, 255, 0.25) !important;
        }
        header h1 {
            color: #ffffff !important;
        }

        /* Avatar Enhancement per DESIGN.md */
        .avatar-wrapper {
            position: relative;
            width: 52px;
            height: 52px;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .avatar-wrapper:active { transform: scale(0.92); }
        .avatar-inner {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 14px rgba(0,0,0,0.18);
            overflow: hidden;
            position: relative;
            z-index: 2;
        }
        .avatar-pulse {
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.25);
            z-index: 1;
        }
        .alert-cream  { background-color: #fff3cd; border: 1px solid #ffeeba; }
        .alert-danger  { background-color: #f8d7da; border: 1px solid #f5c6cb; }
        .alert-info    { background-color: #e3f2fd; border: 1px solid #b8daff; }
        .dot { height: 6px; width: 6px; background: rgba(var(--color-primary-rgb, 60, 42, 33), 0.2); border-radius: 50%; display: inline-block; margin: 0 4px; transition: all .3s; }
        .dot.active { width: 18px; border-radius: 10px; background: var(--color-primary, {{ $t['primary'] ?? '#3C2A21' }}); }

        /* Slide carousel */
        .carousel-wrapper { width: 100%; overflow: hidden; position: relative; border-radius: 15px; }
        .carousel-track { display: flex; transition: transform 0.5s ease; width: 100%; }
        .carousel-track .alert-slide { width: 100%; flex: 0 0 100%; flex-shrink: 0; box-sizing: border-box; }
        .alert-slide-content { display: grid; grid-template-columns: 36px minmax(0, 1fr); gap: 12px; align-items: start; width: 100%; }
        .alert-slide-text { min-width: 0; width: 100%; }
        .alert-slide-text-wrapper { width: 100%; display: block; }
        .alert-slide-text h4, .alert-slide-text p, .alert-slide-text span { 
            white-space: normal !important; 
            overflow-wrap: break-word !important; 
            word-wrap: break-word !important;
            word-break: normal !important;
            display: block;
        }

        /* =========================================================
           GLOBAL MINIMALIST DESIGN SYSTEM (DESIGN.md & minimalist-ui)
           ========================================================= */
        .card, .presensi-card {
            border: 1px solid rgba(15, 23, 42, 0.08) !important;
            border-radius: 16px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            background: #ffffff !important;
            padding: 14px !important;
        }
        .card:hover, .presensi-card:hover {
            border-color: rgba(var(--color-primary-rgb, 60, 42, 33), 0.35) !important;
        }
        .press { transition: transform 0.15s ease; }
        .press:active { transform: scale(0.97); }

        /* DESIGN.md Consistent Menu Cards */
        .dashboard-menu-card {
            background: #ffffff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 8px 4px 6px;
            height: 76px;
            text-align: center;
            transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1);
            text-decoration: none !important;
        }
        .dashboard-menu-card:active {
            transform: scale(0.93);
            background: #f8fafc;
        }
        .dashboard-menu-card img {
            width: 36px;
            height: 36px;
            object-fit: contain;
            margin: 0 auto 3px auto;
        }
        .dashboard-menu-card ion-icon {
            font-size: 32px;
            margin: 0 auto 3px auto;
        }
        .dashboard-menu-card span {
            font-size: 11px;
            font-weight: 500;
            color: #334155;
            line-height: 1.1;
        }

        /* =========================================================
           DASHBOARD SPACING & CARD RHYTHM (DESIGN.md Anti-Slop)
           ========================================================= */
        .dashboard-hero-bg {
            background-color: var(--color-primary, {{ $t['primary'] ?? '#3C2A21' }});
            border-bottom-left-radius: 22px;
            border-bottom-right-radius: 22px;
            position: relative;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
            color: #ffffff !important;
            padding-bottom: 50px !important;
        }
        .dashboard-card-hero-overlap {
            margin-top: -24px !important;
            position: relative;
            z-index: 10;
        }
        .dashboard-section-gap {
            margin-top: 12px !important;
        }
        .dashboard-menu-gap {
            margin-top: 14px !important;
        }
        .dashboard-history-gap {
            margin-top: 20px !important;
            margin-bottom: 28px !important;
        }
        
        /* Swiss Precision Surface Cards (DESIGN.md) */
        .dashboard-surface-card {
            background: #ffffff !important;
            border-radius: 18px !important;
            border: 1px solid rgba(15, 23, 42, 0.08) !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            transition: border-color 0.15s ease;
        }

        /* Attendance Standalone 2-Card Bento Grid */
        .attendance-grid-wrap {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 10px !important;
        }
        .attendance-card {
            padding: 12px 12px !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            min-width: 0 !important;
            transition: all 0.15s ease !important;
        }
        .attendance-card:hover {
            border-color: rgba(var(--color-primary-rgb, 60, 42, 33), 0.3) !important;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05) !important;
        }
        .attendance-card-icon {
            width: 40px !important;
            height: 40px !important;
            min-width: 40px !important;
            min-height: 40px !important;
            border-radius: 12px !important;
            overflow: hidden !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
        }
        .attendance-card-icon img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }
        .attendance-card-icon-in {
            background: #ECFDF5 !important;
            border: 1px solid #A7F3D0 !important;
            color: #059669 !important;
            font-size: 21px !important;
        }
        .attendance-card-icon-out {
            background: #FFFBEB !important;
            border: 1px solid #FDE68A !important;
            color: #D97706 !important;
            font-size: 21px !important;
        }
        .attendance-card-info {
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            min-width: 0 !important;
            flex: 1 !important;
        }
        .attendance-card-label {
            font-family: 'Inter', sans-serif !important;
            font-size: 10px !important;
            font-weight: 700 !important;
            letter-spacing: 0.06em !important;
            text-transform: uppercase !important;
            color: #64748B !important;
            line-height: 1 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        .attendance-card-time-wrap {
            display: flex !important;
            align-items: baseline !important;
            gap: 3px !important;
            margin-top: 4px !important;
        }
        .attendance-card-time {
            font-family: 'JetBrains Mono', monospace !important;
            font-size: 16px !important;
            font-weight: 700 !important;
            color: #0F172A !important;
            line-height: 1 !important;
            letter-spacing: -0.02em !important;
            font-variant-numeric: tabular-nums !important;
            white-space: nowrap !important;
        }
        .attendance-card-tz {
            font-family: 'Inter', sans-serif !important;
            font-size: 9px !important;
            font-weight: 600 !important;
            color: #94A3B8 !important;
            line-height: 1 !important;
        }

        /* Tactile Bento Metric Cards (DESIGN.md - 100% Centered & Grounded) */
        .rekap-metric-card {
            background: #FAF9F8;
            border: 1px solid rgba(15, 23, 42, 0.06);
            border-radius: 13px;
            padding: 10px 4px 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            transition: all 0.15s ease;
        }
        .rekap-metric-card:hover {
            background: #ffffff;
            border-color: rgba(var(--color-primary-rgb, 60, 42, 33), 0.2);
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
        }
        .rekap-metric-val {
            display: block;
            font-family: 'JetBrains Mono', monospace;
            font-size: 21px;
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: -0.02em;
            font-variant-numeric: tabular-nums;
        }
        .rekap-metric-lbl {
            display: block;
            font-family: 'Inter', sans-serif;
            font-size: 10.5px;
            font-weight: 600;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 4px;
            line-height: 1;
        }

        /* 3D Dashboard Menu Cards */
        .dashboard-menu-card {
            background: #ffffff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 18px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px 4px 8px;
            height: 82px;
            text-align: center;
            transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1);
            text-decoration: none !important;
        }
        .dashboard-menu-card:hover {
            border-color: rgba(var(--color-primary-rgb, 60, 42, 33), 0.25);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
        }
        .dashboard-menu-card:active {
            transform: scale(0.93);
            background: #f8fafc;
        }
        .dashboard-menu-card img {
            width: 38px;
            height: 38px;
            object-fit: contain;
            margin: 0 auto 5px auto;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.06));
            transition: transform 0.15s ease;
        }
        .dashboard-menu-card:hover img {
            transform: scale(1.08);
        }
        .dashboard-menu-card ion-icon {
            font-size: 32px;
            margin: 0 auto 5px auto;
            color: var(--color-primary, {{ $t['primary'] ?? '#3C2A21' }});
        }
        .dashboard-menu-card span {
            font-family: 'Inter', sans-serif;
            font-size: 11.5px;
            font-weight: 600;
            color: #334155;
            line-height: 1.1;
        }
    </style>
</head>
<body>
    <main id="appCapsule" class="max-w-lg mx-auto min-h-screen pb-[calc(100px+env(safe-area-inset-bottom,0px))]">

        {{-- ===== HERO SECTION ===== --}}
        <div class="dashboard-hero-bg px-5 pt-5 text-white overflow-hidden relative">
            {{-- Top Icons --}}
            <div class="flex justify-between items-center mb-4 relative z-10">
                <a href="{{ route('shortcut.index') }}" class="glass-icon relative" title="Menu Cepat">
                    <ion-icon name="grid-outline" style="font-size:22px;"></ion-icon>
                </a>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="openHelpDrawer('panduan_karyawan')" class="glass-icon" title="Pusat Bantuan">
                        <ion-icon name="help-circle-outline" style="font-size:22px;"></ion-icon>
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="#" onclick="event.preventDefault(); this.closest('form').submit();" class="glass-icon" title="Keluar">
                            <ion-icon name="exit-outline" style="font-size:22px;"></ion-icon>
                        </a>
                    </form>
                </div>
            </div>

            {{-- User Row (Spacious & Cleanly Aligned) --}}
            <div class="flex justify-between items-center mb-5 relative z-10">
                {{-- Left: Name & Role --}}
                <div class="fade-in pr-3 min-w-0" style="animation-delay:.05s">
                    <h3 style="font-family: 'Outfit', sans-serif; font-size: 21px; font-weight: 800; line-height: 1.2; color: #ffffff !important; letter-spacing: -0.01em; margin: 0;" class="truncate">{{ $karyawan->nama_karyawan }}</h3>
                    <span style="font-family: 'Inter', sans-serif; font-size: 13px; font-weight: 500; opacity: .88; display: block; margin-top: 4px; color: #ffffff !important;" class="truncate">{{ $karyawan->nama_jabatan }} &bull; {{ $karyawan->nama_dept }}</span>
                </div>
                {{-- Right: Avatar --}}
                <a href="{{ route('profile.index') }}" class="fade-in group shrink-0" style="animation-delay:.1s">
                    <div class="avatar-wrapper">
                        <div class="avatar-pulse"></div>
                        <div class="avatar-inner">
                            @if (!empty($karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $karyawan->foto))
                                <div style="width:100%; height:100%; background-image:url({{ getfotoKaryawan($karyawan->foto) }}); background-size:cover; background-position:center;"></div>
                            @else
                                <div class="w-full h-full flex items-center justify-center font-bold text-[15px] text-emerald-950 bg-emerald-100 font-mono tracking-wider">
                                    {{ strtoupper(substr(trim($karyawan->nama_karyawan), 0, 2)) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            </div>

            {{-- Clock Section (Harmonized Vertical Spacing) --}}
            <div class="text-center mt-2 mb-2 pb-0 fade-in relative z-10" style="animation-delay:.15s">
                <h2 id="jam" style="font-family: 'JetBrains Mono', monospace; font-size: 38px; font-weight: 800; letter-spacing: -1px; line-height: 1.1; margin-bottom: 10px; color: #ffffff !important; font-variant-numeric: tabular-nums;">0:00:00</h2>
                <div style="display: inline-flex; align-items: center; justify-content: center;">
                    <div style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 16px; border-radius: 9999px; background: rgba(255, 255, 255, 0.14); border: 1px solid rgba(255, 255, 255, 0.2); box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);">
                        <ion-icon name="calendar-outline" style="font-size: 13px; opacity: 0.9; color: #ffffff;"></ion-icon>
                        <span style="font-family: 'Inter', sans-serif; font-size: 12px; font-weight: 600; color: #ffffff !important; letter-spacing: 0.15px;">
                            {{ getNamaHari(date('D')) }}, {{ DateToIndo(date('Y-m-d')) }}
                        </span>
                        @if (!empty($hari_libur_hari_ini))
                            <span style="margin-left: 4px; padding: 1px 7px; border-radius: 6px; font-size: 9.5px; font-weight: 700; font-family: 'JetBrains Mono', monospace; background: rgba(239, 68, 68, 0.9); color: #ffffff !important; text-transform: uppercase;">
                                Libur
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if(module_enabled('attendance'))
        {{-- ===== SHIFT / SCHEDULE CARD (STANDALONE) ===== --}}
        <div class="px-4 dashboard-card-hero-overlap fade-in" style="animation-delay:.15s">
            <div class="dashboard-surface-card p-3.5 flex items-center justify-between gap-3">
                @if (!empty($hari_libur_hari_ini))
                    {{-- Operational Holiday Header --}}
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div style="width: 38px; height: 38px; min-width: 38px; min-height: 38px; border-radius: 12px; background: #F0FDF4; border: 1px solid #BBF7D0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <ion-icon name="calendar-outline" style="font-size: 20px; color: #15803D;"></ion-icon>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-emerald-800 font-sans leading-none">Hari Libur / OFF</span>
                            <span class="block text-[13.5px] font-bold text-slate-900 truncate mt-1" style="font-family: 'Outfit', sans-serif;">{{ $hari_libur_hari_ini->keterangan }}</span>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10.5px] font-bold {{ !empty($presensi->jam_in) ? 'bg-[#f0fdf4] text-[#15803d] border border-[#bbf7d0]' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }} shrink-0 whitespace-nowrap">
                        <span class="w-1.5 h-1.5 rounded-full {{ !empty($presensi->jam_in) ? 'bg-[#16a34a]' : 'bg-emerald-600' }}"></span>
                        {{ !empty($presensi->jam_in) ? 'Sudah Absen' : 'Bebas Absen' }}
                    </span>
                @else
                    {{-- Normal Shift Header (P1-3: Shift + Cabang Tugas) --}}
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div style="width: 38px; height: 38px; min-width: 38px; min-height: 38px; border-radius: 12px; background: {{ $t['primary'] ?? '#3C2A21' }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #ffffff;">
                            <ion-icon name="time-outline" style="font-size: 20px; color: #ffffff;"></ion-icon>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 font-sans leading-none">Shift Hari Ini</span>
                            <span class="block text-[13.5px] font-bold text-slate-900 truncate mt-1" style="font-family: 'Outfit', sans-serif;">
                                {{ ($karyawan->nama_jam_kerja ?? 'Shift Pagi') . (!empty($nama_cabang_tugas) ? ' • ' . $nama_cabang_tugas : '') }}
                            </span>
                            @if (!empty($karyawan->jam_masuk) && !empty($karyawan->jam_pulang))
                                <span class="block text-[11px] font-semibold text-slate-500 font-mono mt-0.5">
                                    {{ date('H:i', strtotime($karyawan->jam_masuk)) }} - {{ date('H:i', strtotime($karyawan->jam_pulang)) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10.5px] font-bold shrink-0 whitespace-nowrap {{ !empty($presensi->jam_in) ? 'bg-[#f0fdf4] text-[#15803d] border border-[#bbf7d0]' : 'bg-[#fffbeb] text-[#b45309] border border-[#fde68a]' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ !empty($presensi->jam_in) ? 'bg-[#16a34a]' : 'bg-[#d97706]' }}"></span>
                        {{ !empty($presensi->jam_in) ? 'Sudah Absen' : 'Belum Absen' }}
                    </span>
                @endif
            </div>
        </div>

        {{-- ===== ATTENDANCE CLOCK IN / OUT CARDS (STANDALONE 2-CARD BENTO GRID) ===== --}}
        <div class="px-4 dashboard-section-gap attendance-grid-wrap fade-in" style="animation-delay:.18s">
            {{-- Jam Masuk Card (Interactive Action Surface) --}}
            <a href="{{ route('presensi.create') }}" class="dashboard-surface-card attendance-card cursor-pointer active:scale-[0.98] transition-all text-decoration-none" title="Klik untuk Presensi Masuk">
                <div class="attendance-card-icon attendance-card-icon-in">
                    @if (!empty($presensi->foto_in))
                        <img src="{{ route('file.absensi', $presensi->foto_in) }}" alt="Foto Masuk">
                    @else
                        <ion-icon name="log-in-outline"></ion-icon>
                    @endif
                </div>
                <div class="attendance-card-info">
                    <span class="attendance-card-label">Jam Masuk</span>
                    <div class="attendance-card-time-wrap">
                        <span class="attendance-card-time">
                            {{ !empty($presensi->jam_in) ? date('H:i', strtotime($presensi->jam_in)) : '-- : --' }}
                        </span>
                        @if(!empty($presensi->jam_in))
                            <span class="attendance-card-tz">WIB</span>
                        @endif
                    </div>
                </div>
            </a>

            {{-- Jam Pulang Card (Interactive Action Surface) --}}
            <a href="{{ route('presensi.create') }}" class="dashboard-surface-card attendance-card cursor-pointer active:scale-[0.98] transition-all text-decoration-none" title="Klik untuk Presensi Pulang">
                <div class="attendance-card-icon attendance-card-icon-out">
                    @if (!empty($presensi->foto_out))
                        <img src="{{ route('file.absensi', $presensi->foto_out) }}" alt="Foto Pulang">
                    @else
                        <ion-icon name="log-out-outline"></ion-icon>
                    @endif
                </div>
                <div class="attendance-card-info">
                    <span class="attendance-card-label">Jam Pulang</span>
                    <div class="attendance-card-time-wrap">
                        <span class="attendance-card-time">
                            {{ !empty($presensi->jam_out) ? date('H:i', strtotime($presensi->jam_out)) : '-- : --' }}
                        </span>
                        @if(!empty($presensi->jam_out))
                            <span class="attendance-card-tz">WIB</span>
                        @endif
                    </div>
                </div>
            </a>
        </div>

        {{-- ===== REKAP PRESENSI BULAN INI ===== --}}
        <div class="px-4 dashboard-section-gap fade-in" style="animation-delay:.2s">
            <div class="dashboard-surface-card p-3.5 sm:p-4">
                <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        <h4 class="text-[13px] font-bold text-slate-800 tracking-tight m-0" style="font-family: 'Outfit', sans-serif;">
                            Rekap Presensi Bulan {{ $bulan_skrg }}
                        </h4>
                    </div>
                    <span class="text-[10.5px] font-semibold text-slate-400 font-mono">
                        {{ date('H:i') }} WIB
                    </span>
                </div>

                {{-- DESIGN.md Tactile Bento Metric Cells (100% Centered & Responsive) --}}
                <div class="grid {{ module_enabled('leave') ? 'grid-cols-4' : 'grid-cols-2' }} gap-2">
                    {{-- Hadir --}}
                    <div class="rekap-metric-card">
                        <span class="rekap-metric-val text-emerald-700">{{ $rekappresensi->hadir ?? 0 }}</span>
                        <span class="rekap-metric-lbl">Hadir</span>
                    </div>

                    @if(module_enabled('leave'))
                    {{-- Sakit --}}
                    <div class="rekap-metric-card">
                        <span class="rekap-metric-val text-amber-600">{{ $rekappresensi->sakit ?? 0 }}</span>
                        <span class="rekap-metric-lbl">Sakit</span>
                    </div>

                    {{-- Izin --}}
                    <div class="rekap-metric-card">
                        <span class="rekap-metric-val text-sky-600">{{ $rekappresensi->izin ?? 0 }}</span>
                        <span class="rekap-metric-lbl">Izin</span>
                    </div>

                    {{-- Cuti --}}
                    <div class="rekap-metric-card">
                        <span class="rekap-metric-val text-rose-600">{{ $rekappresensi->cuti ?? 0 }}</span>
                        <span class="rekap-metric-lbl">Cuti</span>
                    </div>
                    @else
                    {{-- Terlambat / Alpha jika Leave OFF --}}
                    <div class="rekap-metric-card">
                        <span class="rekap-metric-val text-amber-600">{{ $rekappresensi->terlambat ?? 0 }}</span>
                        <span class="rekap-metric-lbl">Terlambat</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @else
        {{-- ===== HR OVERVIEW CARD (When Attendance Module is Disabled) ===== --}}
        <div class="px-4 dashboard-card-hero-overlap fade-in" style="animation-delay:.15s">
            <div class="dashboard-surface-card p-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div style="width: 40px; height: 40px; border-radius: 12px; background: var(--color-primary, {{ $t['primary'] ?? '#3C2A21' }}); display: flex; align-items: center; justify-content: center; color: var(--theme-primary-contrast, #fff);">
                            <ion-icon name="briefcase-outline" style="font-size: 22px; color: var(--theme-primary-contrast, #fff);"></ion-icon>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Portal Karyawan</span>
                            <span class="block text-[14px] font-bold text-slate-800" style="font-family: 'Outfit', sans-serif;">{{ $company_setting->company_name ?? ($general_setting->nama_perusahaan ?? 'Universal HR') }}</span>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-md text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Aktif
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2.5 mt-3 text-xs">
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="block text-[10px] text-slate-400 font-semibold uppercase">Departemen</span>
                        <span class="block font-bold text-slate-700 mt-0.5 truncate">{{ $karyawan->nama_dept ?? 'Divisi Umum' }}</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="block text-[10px] text-slate-400 font-semibold uppercase">Cabang / Penempatan</span>
                        <span class="block font-bold text-slate-700 mt-0.5 truncate">{{ $karyawan->nama_cabang ?? 'Kantor Pusat' }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif


        @php
            $quickMenus = [];

            // 1. Wajah / Face Enrollment (Only when Face Recognition module is enabled & active)
            if (module_enabled('face_recognition') && ($general_setting->face_recognition ?? 0) == 1 && Route::has('facerecognition.karyawan.create')) {
                $quickMenus[] = [
                    'href' => route('facerecognition.karyawan.create'),
                    'img' => 'assets/template/img/3d/scanwajah.png',
                    'icon' => 'scan-outline',
                    'color' => '#6366F1',
                    'title' => 'Wajah',
                    'id' => 'btnDaftarkanWajah',
                ];
            }

            // 2. Izin & Cuti
            if (module_enabled('leave') && Route::has('pengajuanizin.index')) {
                $quickMenus[] = [
                    'href' => route('pengajuanizin.index'),
                    'img' => 'assets/template/img/3d/activity.png',
                    'icon' => 'calendar-outline',
                    'color' => '#10B981',
                    'title' => 'Izin/Cuti',
                    'id' => null,
                ];
            }

            // 3. Lembur / Overtime
            if (module_enabled('overtime') && Route::has('overtime.index')) {
                $quickMenus[] = [
                    'href' => route('overtime.index'),
                    'img' => null,
                    'icon' => 'time-outline',
                    'color' => '#F59E0B',
                    'title' => 'Lembur',
                    'id' => null,
                ];
            }

            // 4. Slip Gaji / Payslip
            if (module_enabled('payroll') && Route::has('payslip.my_payslips')) {
                $quickMenus[] = [
                    'href' => route('payslip.my_payslips'),
                    'img' => null,
                    'icon' => 'cash-outline',
                    'color' => '#059669',
                    'title' => 'Slip Gaji',
                    'id' => null,
                ];
            }

            // 5. Reimbursement
            if (module_enabled('reimbursement') && Route::has('reimbursement.index')) {
                $quickMenus[] = [
                    'href' => route('reimbursement.index'),
                    'img' => null,
                    'icon' => 'receipt-outline',
                    'color' => '#2563EB',
                    'title' => 'Klaim',
                    'id' => null,
                ];
            }

            // 6. Pinjaman / Loans
            if (module_enabled('loans') && Route::has('loan.index')) {
                $quickMenus[] = [
                    'href' => route('loan.index'),
                    'img' => null,
                    'icon' => 'wallet-outline',
                    'color' => '#7C3AED',
                    'title' => 'Pinjaman',
                    'id' => null,
                ];
            }

            // 7. Core Attendance Items
            if (module_enabled('attendance')) {
                if (Route::has('dispensasi.index')) {
                    $quickMenus[] = [
                        'href' => route('dispensasi.index'),
                        'img' => 'assets/template/img/3d/clock.png',
                        'icon' => 'hourglass-outline',
                        'color' => '#D97706',
                        'title' => 'Dispensasi',
                        'id' => null,
                    ];
                }
                if (Route::has('presensi.histori')) {
                    $quickMenus[] = [
                        'href' => route('presensi.histori'),
                        'img' => 'assets/template/img/3d/maps.png',
                        'icon' => 'finger-print-outline',
                        'color' => '#3B82F6',
                        'title' => 'Riwayat',
                        'id' => null,
                    ];
                }
            }

            // 8. Peraturan Perusahaan (Policy)
            if (module_enabled('policy') && Route::has('policy.index')) {
                $quickMenus[] = [
                    'href' => route('policy.index'),
                    'img' => null,
                    'icon' => 'shield-checkmark-outline',
                    'color' => '#0D9488',
                    'title' => 'Peraturan',
                    'id' => null,
                ];
            }

            // 9. Pengumuman Internal (Announcement)
            if (module_enabled('announcements') && Route::has('announcement.index')) {
                $quickMenus[] = [
                    'href' => route('announcement.index'),
                    'img' => null,
                    'icon' => 'megaphone-outline',
                    'color' => '#0284C7',
                    'title' => 'Pengumuman',
                    'id' => null,
                ];
            }

            // 10. Brankas Dokumen (Document)
            if (module_enabled('documents') && Route::has('document.index')) {
                $quickMenus[] = [
                    'href' => route('document.index'),
                    'img' => null,
                    'icon' => 'folder-outline',
                    'color' => '#475569',
                    'title' => 'Dokumen',
                    'id' => null,
                ];
            }
        @endphp
        {{-- ===== MENU GRID ===== --}}
        <div class="px-4 dashboard-menu-gap fade-in" style="animation-delay:.25s">
            <div class="grid grid-cols-4 gap-2.5">
                @foreach ($quickMenus as $menu)
                    <a href="{{ $menu['href'] }}" @if(!empty($menu['id'])) id="{{ $menu['id'] }}" @endif class="dashboard-menu-card">
                        @if (!empty($menu['img']))
                            <img src="{{ asset($menu['img']) }}" alt="{{ $menu['title'] }}">
                        @else
                            <div style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; margin: 0 auto 3px auto;">
                                <ion-icon name="{{ $menu['icon'] }}" style="font-size: 24px; color: {{ $menu['color'] ?? '#64748B' }};"></ion-icon>
                            </div>
                        @endif
                        <span>{{ $menu['title'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        @if(module_enabled('attendance'))
        {{-- ===== HISTORY LIST ===== --}}
        <div class="px-3 dashboard-history-gap">
            <div class="flex items-center justify-between mb-3 px-1">
                <span class="text-[13px] font-bold text-slate-700">3 Hari Terakhir</span>
                <a href="{{ route('presensi.histori') }}" class="text-[12px] font-medium text-emerald-600 hover:text-emerald-700">Lihat Semua</a>
            </div>

            {{-- Tab Content: Presensi --}}
            <div id="contentPresensi" class="space-y-2.5">
                @foreach ($datapresensi as $index => $d)
                    @php
                        $namahari = [
                            'Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu',
                            'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'
                        ];
                        $day_eng = date('D', strtotime($d->tanggal));
                        $day_indo = $namahari[$day_eng] ?? $day_eng;
                        $day_short = strtoupper(substr($day_indo, 0, 3));
                        $tgl = date('d', strtotime($d->tanggal));
                        $bulan_indo = getNamabulan((int)date('m', strtotime($d->tanggal)));
                        $tahun = date('Y', strtotime($d->tanggal));

                        $is_dispensasi = !empty($d->is_dispensasi);
                        $is_late = false;
                        if ($d->status == 'h' && !$is_dispensasi && !empty($d->jam_in)) {
                            if (isset($d->is_terlambat)) {
                                $is_late = (bool) $d->is_terlambat;
                            } else {
                                $batas = !empty($d->batas_toleransi) ? $d->batas_toleransi : (!empty($d->jam_masuk) ? $d->jam_masuk : '08:00:00');
                                $is_late = date('H:i:s', strtotime($d->jam_in)) > date('H:i:s', strtotime($batas));
                            }
                        }
                    @endphp

                    <div class="press overflow-hidden cursor-pointer presensi-card bg-white rounded-2xl border border-slate-200/80 p-3.5 shadow-sm hover:border-slate-300 transition-all duration-150 active:scale-[0.99]"
                         data-tanggal="{{ DateToIndo($d->tanggal) }}"
                         data-jam-in="{{ $d->jam_in != null ? date('H:i', strtotime($d->jam_in)) : '-' }}"
                         data-jam-out="{{ $d->jam_out != null ? date('H:i', strtotime($d->jam_out)) : '-' }}"
                         data-foto-in="{{ !empty($d->foto_in) ? route('file.absensi', $d->foto_in) : '' }}"
                         data-foto-out="{{ !empty($d->foto_out) ? route('file.absensi', $d->foto_out) : '' }}"
                         data-status="{{ $d->status }}"
                         data-jam-kerja="{{ $d->nama_jam_kerja }}"
                         data-keterangan="{{ $d->status == 'h' ? ($is_dispensasi ? 'Dispensasi' : ($is_late ? 'Telat' : 'Hadir')) : ($d->status == 'i' ? 'Izin: ' . $d->keterangan_izin : ($d->status == 's' ? 'Sakit: ' . $d->keterangan_izin_sakit : ($d->status == 'c' ? 'Cuti: ' . $d->keterangan_izin_cuti : 'Alpha'))) }}"
                         data-nama-mesin="{{ $d->nama_mesin }}">

                        <div class="flex items-center gap-3.5">
                            {{-- Date Badge --}}
                            <div class="shrink-0 w-12 h-12 flex flex-col items-center justify-center rounded-xl bg-white border border-slate-200 text-center">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 leading-none">{{ $day_short }}</span>
                                <span class="text-[16px] font-bold text-slate-800 font-mono leading-none mt-1">{{ $tgl }}</span>
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                {{-- Row 1: Tanggal & Shift --}}
                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                    <h3 class="text-[13.5px] font-bold text-slate-800 truncate m-0 leading-tight">
                                        {{ DateToIndo($d->tanggal) }}
                                    </h3>
                                    <span class="text-[10.5px] font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md shrink-0">
                                        {{ $d->nama_jam_kerja }}
                                    </span>
                                </div>

                                @if ($d->status == 'h')
                                    {{-- Row 2: Jam & Status Badges --}}
                                    <div class="flex items-center justify-between gap-2 flex-wrap">
                                        {{-- Jam In & Out --}}
                                        <div class="flex items-center gap-1.5 text-[12px] font-mono font-medium text-slate-700">
                                            <ion-icon name="time-outline" class="text-[13px] text-slate-400"></ion-icon>
                                            <span>{{ $d->jam_in ? date('H:i', strtotime($d->jam_in)) : '--:--' }}</span>
                                            <span class="text-slate-300 font-sans font-normal">—</span>
                                            <span>{{ $d->jam_out ? date('H:i', strtotime($d->jam_out)) : '--:--' }}</span>
                                        </div>

                                        {{-- Badge Cluster --}}
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            @if ($is_dispensasi)
                                                <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#ecfdf5] text-[#059669] border border-[#a7f3d0]">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[#10b981]"></span>
                                                    DISPENSASI
                                                </span>
                                            @elseif ($is_late)
                                                <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#fef2f2] text-[#e11d48] border border-[#fecdd3]">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[#e11d48]"></span>
                                                    TELAT
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#f0fdf4] text-[#15803d] border border-[#bbf7d0]">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[#16a34a]"></span>
                                                    HADIR
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @elseif ($d->status == 'i')
                                    <div class="flex items-center justify-between gap-2 flex-wrap">
                                        <div class="flex items-center gap-1.5 text-[12px] font-medium text-slate-600 truncate">
                                            <ion-icon name="document-text-outline" class="text-[13px] text-slate-400 shrink-0"></ion-icon>
                                            <span class="truncate">{{ !empty($d->keterangan_izin) ? $d->keterangan_izin : 'Izin Absen' }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 flex-wrap">
                                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#f0f9ff] text-[#0369a1] border border-[#bae6fd]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#0284c7]"></span>
                                                IZIN
                                            </span>
                                        </div>
                                    </div>
                                @elseif ($d->status == 's')
                                    <div class="flex items-center justify-between gap-2 flex-wrap">
                                        <div class="flex items-center gap-1.5 text-[12px] font-medium text-slate-600 truncate">
                                            <ion-icon name="medkit-outline" class="text-[13px] text-slate-400 shrink-0"></ion-icon>
                                            <span class="truncate">{{ !empty($d->keterangan_izin_sakit) ? $d->keterangan_izin_sakit : 'Izin Sakit' }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 flex-wrap">
                                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#fef2f2] text-[#be123c] border border-[#fecdd3]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#e11d48]"></span>
                                                SAKIT
                                            </span>
                                        </div>
                                    </div>
                                @elseif ($d->status == 'c')
                                    <div class="flex items-center justify-between gap-2 flex-wrap">
                                        <div class="flex items-center gap-1.5 text-[12px] font-medium text-slate-600 truncate">
                                            <ion-icon name="calendar-outline" class="text-[13px] text-slate-400 shrink-0"></ion-icon>
                                            <span class="truncate">{{ !empty($d->keterangan_izin_cuti) ? $d->keterangan_izin_cuti : 'Cuti' }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 flex-wrap">
                                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#fffbeb] text-[#b45309] border border-[#fde68a]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#d97706]"></span>
                                                CUTI
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center justify-between gap-2 flex-wrap">
                                        <div class="flex items-center gap-1.5 text-[12px] font-medium text-slate-600 truncate">
                                            <ion-icon name="close-circle-outline" class="text-[13px] text-slate-400 shrink-0"></ion-icon>
                                            <span class="truncate">Tanpa Keterangan</span>
                                        </div>
                                        <div class="flex items-center gap-1 flex-wrap">
                                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#f8fafc] text-[#475569] border border-[#e2e8f0]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#64748b]"></span>
                                                ALPHA
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Extra spacer so the last card is never hidden behind bottom nav --}}
            <div style="height: calc(100px + env(safe-area-inset-bottom, 0px)); width: 100%;"></div>
        </div>
        @else
            {{-- Spacer when attendance history is not displayed --}}
            <div style="height: calc(80px + env(safe-area-inset-bottom, 0px)); width: 100%;"></div>
        @endif

        </div>
    </div>


        {{-- ===== BIRTHDAY MODAL ===== --}}
        @if (isset($is_birthday) && $is_birthday)
            <div id="birthdayModal" class="fixed inset-0 z-[1000] flex items-center justify-center p-4" style="display:none;">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
                <div class="relative rounded-[30px] w-full max-w-[340px] overflow-hidden shadow-2xl animate-bounce-in" style="background:{{ $t['primary'] ?? '#3C2A21' }};">
                    <div id="confetti-container" class="absolute inset-0 pointer-events-none"></div>
                    <div class="p-8 text-center relative z-10">
                        <button onclick="hideBirthday()" class="absolute top-4 right-4 text-white/50 hover:text-white">
                            <ion-icon name="close-circle-outline" style="font-size:28px;"></ion-icon>
                        </button>
                        <div class="mb-5 flex items-center justify-center">
                            <div class="w-20 h-20 rounded-2xl bg-white/15 border border-white/25 flex items-center justify-center text-white">
                                <ion-icon name="ribbon-outline" style="font-size: 42px;"></ion-icon>
                            </div>
                        </div>
                        <h2 class="text-2xl font-bold text-white mb-1" style="font-family: 'Outfit', sans-serif;">Selamat Ulang Tahun</h2>
                        <h3 class="text-lg font-semibold text-white/90 mb-3">{{ $karyawan->nama_karyawan }}</h3>
                        @if ($umur)
                            <p class="text-white/80 mb-5 leading-relaxed text-sm">Selamat ulang tahun yang ke-<strong class="text-white">{{ $umur }}</strong> tahun. Semoga sehat, sukses, dan berkah selalu.</p>
                        @endif
                        <div class="flex flex-col gap-2 mb-6 text-left max-w-[240px] mx-auto bg-white/10 p-3.5 rounded-xl border border-white/10">
                            <div class="flex items-center gap-2 text-white">
                                <ion-icon name="checkmark-circle" class="text-emerald-300"></ion-icon>
                                <span class="text-xs">Kesehatan & kebahagiaan</span>
                            </div>
                            <div class="flex items-center gap-2 text-white">
                                <ion-icon name="checkmark-circle" class="text-emerald-300"></ion-icon>
                                <span class="text-xs">Dedikasi & kesuksesan karir</span>
                            </div>
                        </div>
                        <button onclick="hideBirthday()" class="w-full py-3 rounded-xl bg-white text-slate-800 font-bold shadow-md transition-all active:scale-95">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
            <style>
                @keyframes bounce-in {
                    0% { opacity:0; transform:scale(0.8) translateY(20px); }
                    70% { transform:scale(1.05) translateY(-5px); }
                    100% { opacity:1; transform:scale(1) translateY(0); }
                }
                .animate-bounce-in { animation: bounce-in 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
                .confetti { position: absolute; width:10px; height:10px; border-radius:3px; animation: confetti-fall 3s linear forwards; }
                @keyframes confetti-fall {
                    0% { transform: translateY(-20px) rotate(0deg); opacity: 1; }
                    100% { transform: translateY(300px) rotate(720deg); opacity: 0; }
                }
            </style>
        @endif

        {{-- ===== DETAIL PRESENSI MODAL ===== --}}
        <div id="detailPresensiModal" class="fixed inset-0 z-[1000] flex items-center justify-center p-4" style="display:none;">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm modal-close"></div>
            <div class="relative bg-white rounded-[30px] w-full max-w-[360px] overflow-hidden shadow-2xl transition-all">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-xl font-bold text-gray-800">Detail Presensi</h3>
                        <button class="text-gray-400 hover:text-gray-600 modal-close">
                            <ion-icon name="close-circle-outline" style="font-size:28px;"></ion-icon>
                        </button>
                    </div>

                    <div id="modalContent">
                        <div class="mb-4">
                            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Tanggal & Status</span>
                            <div class="flex justify-between items-center">
                                <span id="modalTanggal" class="text-lg font-bold text-gray-800"></span>
                                <span id="modalStatus" class="px-3 py-1 rounded-full text-xs font-bold text-white"></span>
                            </div>
                            <p id="modalKeterangan" class="text-sm text-gray-500 mt-1"></p>
                        </div>

                        <div id="modalMesinSection" class="mb-4 p-3 rounded-2xl bg-emerald-50 border border-emerald-100 hidden">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white shrink-0" style="background: var(--color-primary, {{ $t['primary'] ?? '#3C2A21' }});">
                                    <ion-icon name="finger-print" style="font-size:20px;"></ion-icon>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-emerald-700 uppercase tracking-wider">Fingerprint Machine</span>
                                    <span id="modalNamaMesin" class="text-sm font-bold text-slate-900"></span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            {{-- Foto Masuk --}}
                            <div class="text-center">
                                <span class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Foto Masuk</span>
                                <div class="aspect-square rounded-2xl overflow-hidden bg-gray-100 border border-gray-100 shadow-sm mb-2">
                                    <img id="modalImgIn" src="" class="w-full h-full object-cover hidden">
                                    <div id="modalNoImgIn" class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                                        <ion-icon name="camera-outline" style="font-size:32px;"></ion-icon>
                                        <span class="text-[10px] mt-1">No Photo</span>
                                    </div>
                                </div>
                                <span id="modalJamIn" class="text-sm font-bold text-gray-700"></span>
                            </div>
                            {{-- Foto Pulang --}}
                            <div class="text-center">
                                <span class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Foto Pulang</span>
                                <div class="aspect-square rounded-2xl overflow-hidden bg-gray-100 border border-gray-100 shadow-sm mb-2">
                                    <img id="modalImgOut" src="" class="w-full h-full object-cover hidden">
                                    <div id="modalNoImgOut" class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                                        <ion-icon name="camera-outline" style="font-size:32px;"></ion-icon>
                                        <span class="text-[10px] mt-1">No Photo</span>
                                    </div>
                                </div>
                                <span id="modalJamOut" class="text-sm font-bold text-gray-700"></span>
                            </div>
                        </div>

                        <button class="w-full py-4 rounded-2xl bg-gray-100 text-gray-600 font-bold modal-close active:scale-95 transition-all">
                            Tutup Detail
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- ===== BOTTOM NAV ===== --}}
    @include('layouts.mobile.bottomNav')

    {{-- ===== SCRIPTS ===== --}}
    <div id="spa-page-scripts" style="display:none;">
    <script>
        // Real-time clock
        function updateClock() {
            var d = new Date();
            var h = d.getHours();
            var m = d.getMinutes() < 10 ? '0' + d.getMinutes() : d.getMinutes();
            var s = d.getSeconds() < 10 ? '0' + d.getSeconds() : d.getSeconds();
            var el = document.getElementById('jam');
            if (el) el.textContent = h + ':' + m + ':' + s;
            setTimeout(updateClock, 1000);
        }
        updateClock();

        // Alert Carousel - Slide
        $(document).ready(function() {
            var track = $('.carousel-track');
            var slides = $('.alert-slide');
            var dots = $('.dot');
            if (slides.length > 1) {
                var current = 0;
                setInterval(function() {
                    current = (current + 1) % slides.length;
                    track.css('transform', 'translateX(-' + (current * 100) + '%)');
                    dots.removeClass('active');
                    $(dots[current]).addClass('active');
                }, 5000);
            }

            // Birthday logic
            @if (isset($is_birthday) && $is_birthday)
                setTimeout(function(){
                    $('#birthdayModal').fadeIn(400);
                    createConfetti();
                }, 1500);
            @endif
        });

        // Birthday Confetti
        function createConfetti() {
            var container = document.getElementById('confetti-container');
            if (!container) return;
            var colors = ['#ffd700', '#ff6b6b', '#4ecdc4', '#95e1d3', '#ffe66d'];
            for (var i = 0; i < 50; i++) {
                var c = document.createElement('div');
                c.className = 'confetti';
                c.style.left = Math.random() * 100 + '%';
                c.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                c.style.animationDelay = (Math.random() * 2) + 's';
                container.appendChild(c);
            }
        }

        function hideBirthday() {
            $('#birthdayModal').fadeOut(300);
        }




        // Presensi Detail Modal Handler (delegated)
        $(document).on('click', '.presensi-card', function() {
            const data = $(this).data();
            
            $("#modalTanggal").text(data.tanggal);
            $("#modalJamIn").text(data.jamIn);
            $("#modalJamOut").text(data.jamOut);
            $("#modalKeterangan").text(data.keterangan);
            
            // Machine Info
            if (data.namaMesin) {
                $("#modalNamaMesin").text(data.namaMesin);
                $("#modalMesinSection").show();
            } else {
                $("#modalMesinSection").hide();
            }

            // Status Badge
            const statusMap = {
                'h': { text: 'Hadir', color: 'bg-[#4A6741]' },
                'i': { text: 'Izin', color: 'bg-[#0284C7]' },
                's': { text: 'Sakit', color: 'bg-[#BA1A1A]' },
                'c': { text: 'Cuti', color: 'bg-[#B45309]' },
                'a': { text: 'Alpha', color: 'bg-[#755841]' }
            };
            
            const status = statusMap[data.status] || { text: 'Alpha', color: 'bg-[#755841]' };
            $("#modalStatus").text(status.text).removeClass().addClass('px-3 py-1 rounded-full text-xs font-bold text-white ' + status.color);

            // Photo In
            if (data.fotoIn) {
                $("#modalImgIn").attr('src', data.fotoIn).show();
                $("#modalNoImgIn").hide();
            } else {
                $("#modalImgIn").hide();
                $("#modalNoImgIn").show();
            }

            // Photo Out
            if (data.fotoOut) {
                $("#modalImgOut").attr('src', data.fotoOut).show();
                $("#modalNoImgOut").hide();
            } else {
                $("#modalImgOut").hide();
                $("#modalNoImgOut").show();
            }

            $("#detailPresensiModal").fadeIn(300);
        });

        $(document).on('click', '.modal-close', function() {
            $("#detailPresensiModal").fadeOut(200);
        });
    </script>

    <script>
        // Universal GlobalSwal Helper for Employee Dashboard (antislop-ui Compliant)
        window.GlobalSwal = {
            toast: function(icon, message, title) {
                Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: function(toast) {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                }).fire({
                    icon: icon || 'success',
                    title: title ? (title + ': ' + message) : message
                });
            },
            success: function(message, title) {
                Swal.fire({
                    icon: 'success',
                    title: title || 'Berhasil!',
                    text: message,
                    confirmButtonColor: "{{ $t['primary'] ?? '#3C2A21' }}",
                    confirmButtonText: 'Selesai',
                    timer: 2500,
                    timerProgressBar: true
                });
            },
            error: function(message, title) {
                Swal.fire({
                    icon: 'error',
                    title: title || 'Gagal',
                    html: message,
                    confirmButtonColor: "{{ $t['primary'] ?? '#3C2A21' }}",
                    confirmButtonText: 'Tutup'
                });
            },
            warning: function(message, title) {
                Swal.fire({
                    icon: 'warning',
                    title: title || 'Peringatan',
                    text: message,
                    confirmButtonColor: "{{ $t['primary'] ?? '#3C2A21' }}",
                    confirmButtonText: 'Mengerti'
                });
            }
        };

        // Universal Toastr Bridge
        window.toastr = {
            options: {},
            success: function(msg, title) { window.GlobalSwal.toast('success', msg, title); },
            error: function(msg, title) { window.GlobalSwal.error(msg, title); },
            warning: function(msg, title) { window.GlobalSwal.toast('warning', msg, title); },
            info: function(msg, title) { window.GlobalSwal.toast('info', msg, title); }
        };
    </script>

    @if ($message = Session::get('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.GlobalSwal.success(@json($message));
            });
        </script>
    @endif

    @if ($message = Session::get('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.GlobalSwal.error(@json($message));
            });
        </script>
    @endif

    @if ($message = Session::get('warning'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.GlobalSwal.warning(@json($message));
            });
        </script>
    @endif

    @if (isset($errors) && $errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.GlobalSwal.warning(@json(implode("\n", $errors->all())), 'Periksa Formulir');
            });
        </script>
    @endif

    @if (module_enabled('face_recognition') && ($general_setting->face_recognition ?? 0) == 1)
        <!-- Face Model Background Preloader for Instant Attendance Load -->
        <script src="{{ asset('assets/external/js/face-model-cache.js') }}?v={{ file_exists(public_path('assets/external/js/face-model-cache.js')) ? filemtime(public_path('assets/external/js/face-model-cache.js')) : time() }}"></script>
        <script>
            window.addEventListener('load', function() {
                setTimeout(function() {
                    if (window.FaceModelCache && typeof window.FaceModelCache.preloadFaceModels === 'function') {
                        window.FaceModelCache.preloadFaceModels('/models');
                    }
                }, 1000);
            });
        </script>
    @endif

    </div>

    @include('layouts.help_drawer')
</body>
</html>

