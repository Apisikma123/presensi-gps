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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>

    {{-- Template CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/template/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/toastr.min.css') }}" />
    <script src="{{ asset('assets/vendor/libs/toastr/toastr.js') }}"></script>

    {{-- Tailwind & App CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
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
            background-color: {{ $t['bg_body'] ?? '#e8f0ed' }};
            -webkit-tap-highlight-color: transparent;
        }
        .hero-bg {
            background-color: {{ $t['primary'] ?? '#2d5a4c' }};
            border-bottom-left-radius: 40px;
            border-bottom-right-radius: 40px;
            position: relative;
            padding-bottom: 80px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 12px;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #ffffff !important;
        }
        .glass-icon ion-icon {
            color: #ffffff !important;
        }
        .glass-icon:active { transform: scale(0.92); background: rgba(255, 255, 255, 0.2); }
        #jam {
            text-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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
            background: {{ $t['primary'] ?? '#1E4D3E' }} !important;
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

        /* Avatar Enhancement */
        .avatar-wrapper {
            position: relative;
            width: 84px;
            height: 84px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .avatar-wrapper:active { transform: scale(0.9); }
        .avatar-inner {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 3px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            overflow: hidden;
            position: relative;
            z-index: 2;
        }
        .avatar-pulse {
            position: absolute;
            top: -4px;
            left: -4px;
            right: -4px;
            bottom: -4px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            z-index: 1;
            animation: avatar-pulse 3s infinite;
        }
        @keyframes avatar-pulse {
            0% { transform: scale(1); opacity: 0.2; }
            50% { transform: scale(1.1); opacity: 0.1; }
            100% { transform: scale(1); opacity: 0.2; }
        }
        .alert-cream  { background-color: #fff3cd; border: 1px solid #ffeeba; }
        .alert-danger  { background-color: #f8d7da; border: 1px solid #f5c6cb; }
        .alert-info    { background-color: #e3f2fd; border: 1px solid #b8daff; }
        .dot { height: 6px; width: 6px; background: {{ ($t['primary'] ?? '#2d5a4c') }}33; border-radius: 50%; display: inline-block; margin: 0 4px; transition: all .3s; }
        .dot.active { width: 18px; border-radius: 10px; background: {{ $t['primary'] ?? '#2d5a4c' }}; }

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
            border-color: rgba(30, 77, 62, 0.35) !important;
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
    </style>
</head>
<body>
    <main id="appCapsule" class="max-w-lg mx-auto min-h-screen">

        {{-- ===== HERO SECTION ===== --}}
        <div class="hero-bg px-5 pt-6 pb-14 text-white overflow-hidden relative">
            {{-- Top Icons --}}
            <div class="flex justify-between items-center mb-3 relative z-10">
                <a href="{{ route('shortcut.index') }}" class="glass-icon relative">
                    <ion-icon name="grid-outline" style="font-size:24px;"></ion-icon>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="#" onclick="event.preventDefault(); this.closest('form').submit();" class="glass-icon">
                        <ion-icon name="exit-outline" style="font-size:24px;"></ion-icon>
                    </a>
                </form>
            </div>

            {{-- User Row --}}
            <div class="flex justify-between items-start mb-0 relative z-10">
                {{-- Left: Name --}}
                <div class="fade-in" style="animation-delay:.05s">
                    <h3 style="font-size:20px; font-weight:800; line-height:1.1; color:#ffffff !important;">{{ $karyawan->nama_karyawan }}</h3>
                    <span style="font-size:13px; font-weight:400; opacity:.85; display:block; margin-top:2px; color:#ffffff !important;">{{ $karyawan->nama_jabatan }} ({{ $karyawan->nama_dept }})</span>
                </div>
                {{-- Right: Avatar --}}
                <a href="{{ route('profile.index') }}" class="fade-in group" style="animation-delay:.1s">
                    <div class="avatar-wrapper">
                        <div class="avatar-pulse"></div>
                        <div class="avatar-inner">
                            @if (!empty($karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $karyawan->foto))
                                <div style="width:100%; height:100%; background-image:url({{ getfotoKaryawan($karyawan->foto) }}); background-size:cover; background-position:center;"></div>
                            @else
                                <img src="{{ asset('assets/template/img/sample/avatar/avatar1.jpg') }}" style="width:100%; height:100%; object-fit:cover;">
                            @endif
                        </div>
                    </div>
                </a>
            </div>

            {{-- Clock --}}
            <div class="text-center mt-0 mb-4 fade-in relative z-10" style="animation-delay:.15s">
                <h2 id="jam" style="font-size:44px; font-weight:900; letter-spacing:-2px; line-height:1; margin-bottom:6px; color:#ffffff !important;">0:00:00</h2>
                <span style="font-size:14px; font-weight:400; opacity:.9; color:#ffffff !important;">Hari ini : {{ getNamaHari(date('D')) }}, {{ DateToIndo(date('Y-m-d')) }}</span>
            </div>
        </div>

        {{-- ===== SHIFT INFO CARD ===== --}}
        <div style="margin-top:-60px; padding:0 20px; position:relative; z-index:10;">
            <div class="bg-white rounded-[15px] p-3 shadow-md border border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-[42px] h-[42px] rounded-xl flex items-center justify-center text-white" style="background: {{ $t['primary'] ?? '#1E4D3E' }};">
                        <ion-icon name="time-outline" style="font-size:24px;"></ion-icon>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Shift Hari Ini</div>
                        <div class="text-[14px] font-bold text-slate-800">{{ $karyawan->nama_jam_kerja ?? 'Shift Pagi (07:00)' }}</div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-block px-2.5 py-1 rounded-full text-[11px] font-bold font-mono {{ !empty($presensi->jam_in) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                        {{ !empty($presensi->jam_in) ? 'Sudah Absen Masuk' : 'Belum Absen Masuk' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- ===== ATTENDANCE SECTION ===== --}}
        <div class="px-5 mt-3 fade-in" style="animation-delay:.2s">
            <div class="bg-white rounded-[15px] py-6 px-5 shadow-sm border border-gray-100 flex items-center">
                {{-- Jam Masuk --}}
                <div class="flex-1 flex items-center gap-3">
                    <div class="flex items-center justify-center w-[40px] h-[40px] rounded-full overflow-hidden bg-gray-50 border border-gray-100">
                        @if (!empty($presensi->foto_in) && Storage::disk('public')->exists('/uploads/absensi/' . $presensi->foto_in))
                            <img src="{{ url('/storage/uploads/absensi/' . $presensi->foto_in) }}" style="width:100%; height:100%; object-fit:cover;">
                        @else
                            <ion-icon name="camera-outline" style="font-size:32px; color: {{ $t['primary'] ?? '#2d5a4c' }}; grayscale: 0.2;"></ion-icon>
                        @endif
                    </div>
                    <div>
                        <span class="block text-[14px] font-bold text-gray-800" style="letter-spacing:-0.2px; line-height: 1.2;">Jam Masuk</span>
                        <span class="block text-[16px] font-bold text-gray-400 mt-1" style="letter-spacing: 1px;">{{ !empty($presensi->jam_in) ? date('H:i', strtotime($presensi->jam_in)) : '-- : --' }}</span>
                    </div>
                </div>

                {{-- Vertical Separator --}}
                <div class="w-[1.5px] h-[35px] bg-gray-100 mx-2"></div>

                {{-- Jam Pulang --}}
                <div class="flex-1 flex items-center gap-3 pl-4">
                    <div class="flex items-center justify-center w-[40px] h-[40px] rounded-full overflow-hidden bg-gray-50 border border-gray-100">
                        @if (!empty($presensi->foto_out) && Storage::disk('public')->exists('/uploads/absensi/' . $presensi->foto_out))
                            <img src="{{ url('/storage/uploads/absensi/' . $presensi->foto_out) }}" style="width:100%; height:100%; object-fit:cover;">
                        @else
                            <ion-icon name="camera-outline" style="font-size:32px; color: {{ $t['primary'] ?? '#2d5a4c' }}; grayscale: 0.2;"></ion-icon>
                        @endif
                    </div>
                    <div>
                        <span class="block text-[14px] font-bold text-gray-800" style="letter-spacing:-0.2px; line-height: 1.2;">Jam Pulang</span>
                        <span class="block text-[16px] font-bold text-gray-400 mt-1" style="letter-spacing: 1px;">{{ !empty($presensi->jam_out) ? date('H:i', strtotime($presensi->jam_out)) : '-- : --' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== ATTENDANCE RECAP SECTION ===== --}}
        <div class="px-5 mt-3 fade-in" style="animation-delay:.25s">
            <div class="bg-white rounded-[15px] py-3 shadow-sm border border-gray-100 text-center">
                <h4 style="font-size:15px; font-weight:700; color:#444; margin-bottom:2px; letter-spacing:-0.2px;">Rekap Presensi Bulan {{ $bulan_skrg }}</h4>
                <span style="font-size:12px; font-weight:400; color:#999; display:block; margin-bottom:8px;">Update Terakhir: {{ date('H:i') }} WIB</span>

                <div class="flex items-center">
                    {{-- Hadir --}}
                    <div class="flex-1">
                        <span class="block text-[28px] font-bold" style="color: {{ $t['primary'] ?? '#2d5a4c' }}; line-height: 1.1;">{{ $rekappresensi->hadir ?? 0 }}</span>
                        <span class="block text-[12px] font-normal text-gray-400 mt-1">Hadir</span>
                    </div>

                    {{-- Separator --}}
                    <div class="w-[1px] h-[40px] bg-gray-100"></div>

                    {{-- Sakit --}}
                    <div class="flex-1">
                        <span class="block text-[28px] font-bold" style="color: #ff9800; line-height: 1.1;">{{ $rekappresensi->sakit ?? 0 }}</span>
                        <span class="block text-[12px] font-normal text-gray-400 mt-1">Sakit</span>
                    </div>

                    {{-- Separator --}}
                    <div class="w-[1px] h-[40px] bg-gray-100"></div>

                    {{-- Izin --}}
                    <div class="flex-1">
                        <span class="block text-[28px] font-bold" style="color: #2196f3; line-height: 1.1;">{{ $rekappresensi->izin ?? 0 }}</span>
                        <span class="block text-[12px] font-normal text-gray-400 mt-1">Izin</span>
                    </div>

                    {{-- Separator --}}
                    <div class="w-[1px] h-[40px] bg-gray-100"></div>

                    {{-- Cuti --}}
                    <div class="flex-1">
                        <span class="block text-[28px] font-bold" style="color: #ff5252; line-height: 1.1;">{{ $rekappresensi->cuti ?? 0 }}</span>
                        <span class="block text-[12px] font-normal text-gray-400 mt-1">Cuti</span>
                    </div>
                </div>
            </div>
        </div>


        @php
            $scheme = $general_setting?->mobile_theme_scheme ?? 'green';
            
            $quickMenus = [
                [
                    'href' => route('facerecognition.karyawan.create'),
                    'img' => 'assets/template/img/3d/scanwajah.png',
                    'icon' => 'scan-outline',
                    'title' => 'Wajah',
                    'id' => 'btnDaftarkanWajah',
                ],
                [
                    'href' => route('pengajuanizin.index'),
                    'img' => 'assets/template/img/3d/activity.png',
                    'icon' => 'calendar-outline',
                    'title' => 'Izin/Cuti',
                    'id' => null,
                ],
                [
                    'href' => route('dispensasi.index'),
                    'img' => 'assets/template/img/3d/clock.png',
                    'icon' => 'time-outline',
                    'title' => 'Dispensasi',
                    'id' => null,
                ],
                [
                    'href' => route('presensi.histori'),
                    'img' => 'assets/template/img/3d/maps.png',
                    'icon' => 'finger-print-outline',
                    'title' => 'Riwayat',
                    'id' => null,
                ],
            ];

            $menuCount = count($quickMenus);
            $gridColsClass = "grid-cols-4";
        @endphp
        {{-- ===== MENU GRID ===== --}}
        <div class="px-4 mt-4 fade-in" style="animation-delay:.3s">
            <div class="grid {{ $gridColsClass }} gap-2">
                @foreach ($quickMenus as $menu)
                    <a href="{{ $menu['href'] }}" @if(!empty($menu['id'])) id="{{ $menu['id'] }}" @endif class="dashboard-menu-card">
                        @if ($scheme == 'green' && !empty($menu['img']))
                            <img src="{{ asset($menu['img']) }}" alt="{{ $menu['title'] }}">
                        @else
                            <ion-icon name="{{ $menu['icon'] }}" style="color: {{ $t['primary'] ?? '#1E4D3E' }};"></ion-icon>
                        @endif
                        <span>{{ $menu['title'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ===== HISTORY LIST ===== --}}
        <div class="px-3 mt-5" style="margin-bottom:30px;">
            <div class="flex items-center justify-between mb-3 px-1">
                <span class="text-[13px] font-bold text-slate-700">30 Hari Terakhir</span>
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
                            $batas = !empty($d->batas_toleransi) ? $d->batas_toleransi : '07:05:00';
                            $is_late = date('H:i:s', strtotime($d->jam_in)) > date('H:i:s', strtotime($batas));
                        }
                    @endphp

                    <div class="press overflow-hidden cursor-pointer presensi-card bg-white rounded-2xl border border-slate-200/80 p-3.5 shadow-sm hover:border-slate-300 transition-all duration-150 active:scale-[0.99]"
                         data-tanggal="{{ DateToIndo($d->tanggal) }}"
                         data-jam-in="{{ $d->jam_in != null ? date('H:i', strtotime($d->jam_in)) : '-' }}"
                         data-jam-out="{{ $d->jam_out != null ? date('H:i', strtotime($d->jam_out)) : '-' }}"
                         data-foto-in="{{ !empty($d->foto_in) ? url('/storage/uploads/absensi/' . $d->foto_in) : '' }}"
                         data-foto-out="{{ !empty($d->foto_out) ? url('/storage/uploads/absensi/' . $d->foto_out) : '' }}"
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


        </div>

        </div>
    </div>


        {{-- ===== BIRTHDAY MODAL ===== --}}
        @if (isset($is_birthday) && $is_birthday)
            <div id="birthdayModal" class="fixed inset-0 z-[1000] flex items-center justify-center p-4" style="display:none;">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
                <div class="relative rounded-[30px] w-full max-w-[340px] overflow-hidden shadow-2xl animate-bounce-in" style="background:{{ $t['primary'] ?? '#2d5a4c' }};">
                    <div id="confetti-container" class="absolute inset-0 pointer-events-none"></div>
                    <div class="p-8 text-center relative z-10">
                        <button onclick="hideBirthday()" class="absolute top-4 right-4 text-white/50 hover:text-white">
                            <ion-icon name="close-circle-outline" style="font-size:28px;"></ion-icon>
                        </button>
                        <div class="mb-6 animate-bounce">
                            <span style="font-size:70px; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.3));">🎂</span>
                        </div>
                        <h2 class="text-2xl font-extrabold text-white mb-1">Selamat Ulang Tahun!</h2>
                        <h3 class="text-xl font-bold text-white/90 mb-4">{{ $karyawan->nama_karyawan }}</h3>
                        @if ($umur)
                            <p class="text-white/80 mb-6 leading-relaxed text-sm">Selamat ulang tahun yang ke-<strong class="text-white">{{ $umur }}</strong> tahun! Semoga sukses dan bahagia selalu. 🎊</p>
                        @endif
                        <div class="flex flex-col gap-2 mb-8 text-left max-w-[240px] mx-auto bg-white/10 p-4 rounded-2xl">
                            <div class="flex items-center gap-2 text-white">
                                <ion-icon name="sparkles" class="text-yellow-300"></ion-icon>
                                <span class="text-xs">Panjang umur & sehat selalu</span>
                            </div>
                            <div class="flex items-center gap-2 text-white">
                                <ion-icon name="sparkles" class="text-yellow-300"></ion-icon>
                                <span class="text-xs">Sukses dalam karir & rezeki</span>
                            </div>
                        </div>
                        <button onclick="hideBirthday()" class="w-full py-3 rounded-full bg-white text-{{ $t['primary'] ?? '#2d5a4c' }} font-bold shadow-lg transition-all active:scale-95">
                            Terima Kasih! 🙏
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

                        <div id="modalMesinSection" class="mb-4 p-3 rounded-2xl bg-indigo-50 border border-indigo-100 hidden">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white shrink-0">
                                    <ion-icon name="finger-print" style="font-size:20px;"></ion-icon>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-indigo-400 uppercase tracking-wider">Fingerprint Machine</span>
                                    <span id="modalNamaMesin" class="text-sm font-bold text-indigo-900"></span>
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
    <div style="height: 100px;"></div>
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
                'h': { text: 'Hadir', color: 'bg-emerald-500' },
                'i': { text: 'Izin', color: 'bg-blue-500' },
                's': { text: 'Sakit', color: 'bg-rose-500' },
                'c': { text: 'Cuti', color: 'bg-orange-500' },
                'a': { text: 'Alpha', color: 'bg-slate-500' }
            };
            
            const status = statusMap[data.status] || { text: 'Alpha', color: 'bg-slate-500' };
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
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };
    </script>

    @if ($message = Session::get('success'))
        <script>toastr.success("{{ $message }}");</script>
    @endif

    @if ($message = Session::get('error'))
        <script>toastr.error("{{ $message }}");</script>
    @endif

    @if ($message = Session::get('warning'))
        <script>toastr.warning("{{ $message }}");</script>
    @endif

    @if (isset($errors) && $errors->any())
        <script>
            @foreach ($errors->all() as $error)
                toastr.error("{{ addslashes($error) }}");
            @endforeach
        </script>
    @endif

    @if (($general_setting->face_recognition ?? 0) == 1)
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
</body>
</html>

