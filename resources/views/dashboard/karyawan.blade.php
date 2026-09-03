<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="{{ $t['primary'] }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/toastr.min.css') }}" />
    <script src="{{ asset('assets/vendor/libs/toastr/toastr.js') }}"></script>

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
    <div class="max-w-lg mx-auto min-h-screen">

        {{-- ===== HERO SECTION ===== --}}
        <div class="hero-bg px-5 pt-6 pb-14 text-white overflow-hidden relative">
            {{-- Top Icons --}}
            <div class="flex justify-between items-center mb-3 relative z-10">
                <a href="{{ route('karyawan-approval.index') }}" class="glass-icon relative">
                    <ion-icon name="notifications-outline" style="font-size:24px;"></ion-icon>
                    @if (isset($pendingApprovalCount) && $pendingApprovalCount > 0)
                        <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-red-500 rounded-full border-2 border-[{{ $t['primary'] ?? '#2d5a4c' }}] text-[9px] font-bold flex items-center justify-center px-1 shadow-sm">{{ $pendingApprovalCount }}</span>
                    @endif
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
                    <h3 style="font-size:20px; font-weight:800; line-height:1.1;">{{ $karyawan->nama_karyawan }}</h3>
                    <span style="font-size:13px; font-weight:400; opacity:.8; display:block; margin-top:2px;">{{ $karyawan->nama_jabatan }} ({{ $karyawan->nama_dept }})</span>
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
                <h2 id="jam" style="font-size:44px; font-weight:900; letter-spacing:-2px; line-height:1; margin-bottom:6px;">0:00:00</h2>
                <span style="font-size:14px; font-weight:400; opacity:.85;">Hari ini : {{ getNamaHari(date('D')) }}, {{ DateToIndo(date('Y-m-d')) }}</span>
            </div>
        </div>

        {{-- ===== FLOATING ALERT CARD ===== --}}
        <div style="margin-top:-60px; padding:0 20px; position:relative; z-index:10;">
            @php
                $activeAlerts = [];
                if (!empty($pengumuman)) { $activeAlerts[] = 'pengumuman'; }
                if (!empty($notif_kontrak)) { $activeAlerts[] = 'kontrak'; }
                if (!empty($notif_sp)) { $activeAlerts[] = 'sp'; }
            @endphp

            @if (count($activeAlerts) > 0)
                <div id="alertCarousel" class="carousel-wrapper rounded-[15px] shadow-md">
                    <div class="carousel-track">
                    @foreach ($activeAlerts as $idx => $type)
                        <div class="alert-slide rounded-[15px] p-4" style="
                            @if($type == 'kontrak') background-color:#fff3cd; border:1px solid #ffeeba;
                            @elseif($type == 'sp') background-color:#f8d7da; border:1px solid #f5c6cb;
                            @elseif($type == 'pengumuman') background-color:#e3f2fd; border:1px solid #b8daff;
                            @endif">
                            <div class="alert-slide-content">
                                {{-- Icon --}}
                                <div class="shrink-0 w-[36px] h-[36px] rounded-full flex items-center justify-center"
                                    style="@if($type=='kontrak') background:rgba(255,193,7,.2); @elseif($type=='sp') background:rgba(220,53,69,.2); @else background:rgba(12,84,96,.15); @endif">
                                    @if($type == 'kontrak')
                                        <ion-icon name="alert-circle" style="font-size:24px; color:#ffc107;"></ion-icon>
                                    @elseif($type == 'sp')
                                        <ion-icon name="warning" style="font-size:24px; color:#dc3545;"></ion-icon>
                                    @else
                                        <ion-icon name="megaphone" style="font-size:24px; color:#0c5460;"></ion-icon>
                                    @endif
                                    </div>
                                {{-- Text --}}
                                <div class="alert-slide-text">
                                    <div class="alert-slide-text-wrapper">
                                    @if($type == 'kontrak')
                                        <h4 style="font-size:14px; font-weight:700; color:#856404; margin:0 0 4px 0; letter-spacing: -0.2px;">Masa Kontrak Segera Berakhir</h4>
                                        <p style="font-size:12px; color:#856404; opacity:.85; line-height:1.5; margin:0;">
                                            Sisa masa kontrak Anda adalah <strong>{{ $notif_kontrak['sisa_hari'] }} hari</strong>.<br> (Selesai: {{ $notif_kontrak['tanggal_akhir'] }}).
                                        </p>
                                        <span style="font-size:11px; color:#856404; opacity:.6; margin-top:6px; display:inline-block; font-weight: 500;">Mohon segera koordinasi dengan HRD.</span>
                                    @elseif($type == 'sp')
                                        <h4 style="font-size:14px; font-weight:700; color:#721c24; margin:0 0 4px 0; letter-spacing: -0.2px;">Peringatan Disiplin</h4>
                                        <p style="font-size:12px; color:#721c24; opacity:.85; line-height:1.5; margin:0;">
                                            Status aktif: <strong>{{ $notif_sp->jenis_sp }}</strong>.<br>
                                            Berlaku s/d: <strong>{{ \Carbon\Carbon::parse($notif_sp->sampai)->translatedFormat('d F Y') }}</strong>.
                                        </p>
                                        <span style="font-size:11px; color:#721c24; opacity:.6; margin-top:6px; display:inline-block; font-weight: 500;">Tetap jaga profesionalisme kerja Anda.</span>
                                    @elseif($type == 'pengumuman')
                                        <h4 style="font-size:14px; font-weight:700; color:#0c5460; margin:0 0 2px 0; letter-spacing: -0.2px;">{{ $pengumuman->judul }}</h4>
                                        <span style="font-size:10px; color:#0c5460; opacity:.6; font-weight: 600;">{{ \Carbon\Carbon::parse($pengumuman->created_at)->translatedFormat('d F Y') }}</span>
                                        <div style="font-size:12px; color:#0c5460; opacity:.85; margin-top:6px; line-height:1.5;">{!! nl2br(e(Str::limit($pengumuman->isi, 100))) !!}</div>
                                    @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>

                {{-- Dots --}}
                @if(count($activeAlerts) > 1)
                    <div class="flex justify-center mt-3 gap-1">
                        @foreach($activeAlerts as $idx => $type)
                            <span class="dot {{ $idx == 0 ? 'active' : '' }}" data-idx="{{ $idx }}"></span>
                        @endforeach
                    </div>
                @endif
            @endif
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
            
            $quickMenus = [];
            if (($general_setting?->absen_istirahat ?? 0) == 1 && \App\Models\KaryawanMenuSetting::isActive('absen_istirahat')) {
                $quickMenus[] = [
                    'href' => route('presensiistirahat.create'),
                    'img' => 'assets/template/img/3d/praying.png',
                    'icon' => 'cafe-outline',
                    'title' => 'Istirahat',
                    'id' => null,
                ];
            } elseif (isset($karyawan) && $karyawan->status_karyawan == 'K' && \App\Models\KaryawanMenuSetting::isActive('kontrak')) {
                $quickMenus[] = [
                    'href' => route('kontrak.index'),
                    'img' => 'assets/template/img/3d/kontrak.png',
                    'icon' => 'document-text-outline',
                    'title' => 'Kontrak',
                    'id' => null,
                ];
            }
            if (\App\Models\KaryawanMenuSetting::isActive('lembur')) {
                $quickMenus[] = [
                    'href' => route('lembur.index'),
                    'img' => 'assets/template/img/3d/clock.png',
                    'icon' => 'time-outline',
                    'title' => 'Lembur',
                    'id' => null,
                ];
            }
            if (\App\Models\KaryawanMenuSetting::isActive('slipgaji')) {
                $quickMenus[] = [
                    'href' => route('slipgaji.index'),
                    'img' => 'assets/template/img/3d/slipgaji.png',
                    'icon' => 'cash-outline',
                    'title' => 'Slip Gaji',
                    'id' => null,
                ];
            }
            if (auth()->user()->can('aktivitaskaryawan.index') && \App\Models\KaryawanMenuSetting::isActive('aktivitas')) {
                $quickMenus[] = [
                    'href' => route('aktivitaskaryawan.index'),
                    'img' => 'assets/template/img/3d/activity.png',
                    'icon' => 'pulse-outline',
                    'title' => 'Aktivitas',
                    'id' => null,
                ];
            }
            if (auth()->user()->can('kunjungan.index') && \App\Models\KaryawanMenuSetting::isActive('visit')) {
                $quickMenus[] = [
                    'href' => route('kunjungan.index'),
                    'img' => 'assets/template/img/3d/maps.png',
                    'icon' => 'map-outline',
                    'title' => 'Visit',
                    'id' => null,
                ];
            }
            if (\App\Models\KaryawanMenuSetting::isActive('wajah')) {
                $quickMenus[] = [
                    'href' => 'javascript:void(0)',
                    'img' => 'assets/template/img/3d/scanwajah.png',
                    'icon' => 'scan-outline',
                    'title' => 'Wajah',
                    'id' => 'btnDaftarkanWajah',
                ];
            }
            $quickMenus[] = [
                'href' => route('shortcut.index'),
                'img' => null,
                'icon' => 'apps-outline',
                'title' => 'Lainnya',
                'id' => null,
            ];

            $menuCount = count($quickMenus);
            $gridColsClass = $menuCount <= 4 ? "grid-cols-{$menuCount}" : "grid-cols-4";
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
        <div class="px-4 mt-5 fade-in" style="animation-delay:.35s; margin-bottom:30px;">
            {{-- Tabs: Segmented Squircle (DESIGN.md 12px) --}}
            <div class="flex p-1 rounded-xl border border-slate-200/80 mb-3 bg-slate-100 gap-1" style="background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 12px; padding: 4px; display: flex; gap: 4px;">
                <button type="button" id="tabPresensi" onclick="switchTab('presensi')" class="flex-1 py-2 text-center text-[13px] font-semibold transition-all rounded-lg" style="background:{{ $t['primary'] ?? '#1E4D3E' }}; color:white; border: none !important; outline: none !important; border-radius: 8px; padding: 8px 12px; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.06);">
                    30 Hari terakhir
                </button>
                <button type="button" id="tabLembur" onclick="switchTab('lembur')" class="flex-1 py-2 text-center text-[13px] font-medium transition-all rounded-lg flex items-center justify-center gap-1.5" style="background: transparent; color:#64748b; border: none !important; outline: none !important; border-radius: 8px; padding: 8px 12px; cursor: pointer;">
                    <span>Lembur</span>
                    @if(isset($notiflembur) && $notiflembur > 0)
                        <span class="inline-flex items-center justify-center px-1.5 py-0.2 rounded-md text-white text-[10px] font-bold font-mono" style="background:#ef4444;">{{ $notiflembur }}</span>
                    @endif
                </button>
            </div>

            {{-- Tab Content: Presensi --}}
            <div id="contentPresensi">
                @foreach ($datapresensi as $d)
                    @php
                        $namahari = ['Sun'=>'Minggu','Mon'=>'Senin','Tue'=>'Selasa','Wed'=>'Rabu','Thu'=>'Kamis','Fri'=>'Jumat','Sat'=>'Sabtu'];
                        $day_eng = date('D', strtotime($d->tanggal));
                        $day_indo = $namahari[$day_eng] ?? $day_eng;
                        $day_short = strtoupper(substr($day_indo, 0, 3));
                        $tgl = date('d', strtotime($d->tanggal));

                        $text_color = $d->status == 'h' ? ($t['primary'] ?? '#2d5a4c') : ($d->status == 'i' ? '#1e90ff' : ($d->status == 's' ? '#ff6384' : ($d->status == 'c' ? '#ff9f40' : '#e74c3c')));
                        $bg_color = $d->status == 'h' ? (($t['primary'] ?? '#2d5a4c') . '18') : ($d->status == 'i' ? '#1e90ff18' : ($d->status == 's' ? '#ff638418' : ($d->status == 'c' ? '#ff9f4018' : '#e74c3c18')));
                    @endphp
                    <div class="bg-white rounded-xl mb-2.5 p-3 flex items-center gap-3 cursor-pointer presensi-card border border-slate-200/80 shadow-[0_1px_3px_rgba(15,23,42,0.03)] hover:border-slate-300 hover:shadow-sm transition-all duration-150 active:scale-[0.99]" 
                        data-tanggal="{{ DateToIndo($d->tanggal) }}"
                        data-jam-in="{{ $d->jam_in != null ? date('H:i', strtotime($d->jam_in)) : '-' }}"
                        data-jam-out="{{ $d->jam_out != null ? date('H:i', strtotime($d->jam_out)) : '-' }}"
                        data-foto-in="{{ !empty($d->foto_in) ? url('/storage/uploads/absensi/' . $d->foto_in) : '' }}"
                        data-foto-out="{{ !empty($d->foto_out) ? url('/storage/uploads/absensi/' . $d->foto_out) : '' }}"
                        data-status="{{ $d->status }}"
                        data-jam-kerja="{{ $d->nama_jam_kerja }}"
                        data-keterangan="{{ $d->status == 'h' ? 'Hadir' : ($d->status == 'i' ? 'Izin: ' . $d->keterangan_izin : ($d->status == 's' ? 'Sakit: ' . $d->keterangan_izin_sakit : ($d->status == 'c' ? 'Cuti: ' . $d->keterangan_izin_cuti : 'Alpha'))) }}"
                        data-nama-mesin="{{ $d->nama_mesin }}">
                        {{-- Day Badge --}}
                        <div class="shrink-0 w-11 h-11 flex flex-col items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-center">
                            <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400 leading-none">{{ $day_short }}</span>
                            <span class="text-[16px] font-extrabold text-slate-800 font-mono leading-none mt-1">{{ $tgl }}</span>
                        </div>
                        {{-- Details --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <h5 class="text-[13px] font-bold text-slate-800 truncate m-0 leading-tight">{{ DateToIndo($d->tanggal) }}</h5>
                                <span class="text-[10px] font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md shrink-0">
                                    {{ $d->nama_jam_kerja }}
                                </span>
                            </div>
                            <div>
                                @if ($d->status == 'h')
                                    @php
                                        $jam_in_ts = strtotime($d->jam_in);
                                        $jam_masuk_ts = strtotime($d->tanggal . ' ' . $d->jam_masuk);
                                        $is_late = $jam_in_ts > $jam_masuk_ts;
                                        $jam_telat = 0; $menit_telat = 0; $desimal_terlambat = 0;
                                        if ($is_late) {
                                            $terlambat_selisih = $jam_in_ts - $jam_masuk_ts;
                                            $jam_telat = floor($terlambat_selisih / 3600);
                                            $sisa = $terlambat_selisih % 3600;
                                            $menit_telat = floor($sisa / 60);
                                            $desimal_terlambat = $jam_telat + round($menit_telat / 60, 2);
                                        }
                                        $denda_display = 0; $potongan_jam = 0; $potongan_jam_terlambat = 0; $pulangcepat = 0; $potongan_tidak_scan = 0;
                                        $denda_dari_db = !empty($d->denda) ? $d->denda : null;
                                        if ($denda_dari_db !== null) {
                                            $denda_display = $denda_dari_db;
                                            if ($is_late && $desimal_terlambat >= 1) { $potongan_jam_terlambat = $desimal_terlambat > $d->total_jam ? $d->total_jam : $desimal_terlambat; }
                                        } else {
                                            if ($is_late) {
                                                if ($desimal_terlambat < 1) { $denda_display = hitungdenda($denda_list, $menit_telat); } else { $potongan_jam_terlambat = $desimal_terlambat > $d->total_jam ? $d->total_jam : $desimal_terlambat; }
                                            }
                                        }
                                        $pulangcepat = hitungpulangcepat($d->tanggal, $d->jam_out, $d->jam_pulang, $d->istirahat, $d->jam_awal_istirahat, $d->jam_akhir_istirahat, $d->lintashari);
                                        $pulangcepat = $pulangcepat > $d->total_jam ? $d->total_jam : $pulangcepat;
                                        if ($d->tanggal != date('Y-m-d') && (empty($d->jam_out) || empty($d->jam_in))) { $potongan_tidak_scan = $d->total_jam; }
                                        $potongan_jam = $potongan_tidak_scan > 0 ? $potongan_tidak_scan : ($pulangcepat + $potongan_jam_terlambat);
                                        $status_potongan_row = isset($d->status_potongan) ? $d->status_potongan : $namasettings->status_potongan_jam;
                                        if ($status_potongan_row == 0) { $potongan_jam = 0; $denda_display = 0; }
                                    @endphp
                                    <div class="flex items-center justify-between gap-2 flex-wrap">
                                        <div class="flex items-center gap-1.5 text-[12px] font-mono font-semibold text-slate-700">
                                            <ion-icon name="time-outline" class="text-[13px] text-slate-400"></ion-icon>
                                            <span>{{ $d->jam_in != null ? date('H:i', strtotime($d->jam_in)) : '--:--' }}</span>
                                            <span class="text-slate-300 font-sans font-normal">—</span>
                                            <span>{{ $d->jam_out != null ? date('H:i', strtotime($d->jam_out)) : '--:--' }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 flex-wrap">
                                            @if ($is_late)
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-rose-50 text-rose-600 border border-rose-200/60 font-mono">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                    Telat {{ $jam_telat > 0 ? $jam_telat . 'j ' : '' }}{{ $menit_telat }}m
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Tepat Waktu
                                                </span>
                                            @endif
                                            @if ($pulangcepat > 0)
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200/60">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    Pulang Cepat
                                                </span>
                                            @endif
                                            @if ($denda_display > 0)
                                                <span class="inline-flex items-center text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-rose-50 text-rose-600 border border-rose-200/60 font-mono">
                                                    Denda Rp {{ number_format($denda_display) }}
                                                </span>
                                            @endif
                                            @if ($potongan_jam > 0 && ($d->jam_out != null || $d->tanggal != date('Y-m-d')))
                                                @if ($namasettings->status_potongan_jam == 1 || (isset($d->status_potongan) && $d->status_potongan == 1))
                                                    <span class="inline-flex items-center text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-rose-50 text-rose-600 border border-rose-200/60 font-mono">
                                                        PJ: {{ number_format($potongan_jam, 2) }} Jam
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @elseif ($d->status == 'i')
                                    <div class="flex items-center gap-1.5 text-[11px] font-semibold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-md border border-sky-100 w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                        <span class="truncate">Izin: {{ $d->keterangan_izin }}</span>
                                    </div>
                                @elseif ($d->status == 's')
                                    <div class="flex items-center gap-1.5 text-[11px] font-semibold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-100 w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span class="truncate">Sakit: {{ $d->keterangan_izin_sakit }}</span>
                                    </div>
                                @elseif ($d->status == 'c')
                                    <div class="flex items-center gap-1.5 text-[11px] font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-100 w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        <span class="truncate">Cuti: {{ $d->keterangan_izin_cuti }}</span>
                                    </div>
                                @elseif ($d->status == 'a')
                                    <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200 w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span>Alpha: Tanpa Keterangan</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Tab Content: Lembur --}}
            <div id="contentLembur" style="display:none;">
                @foreach ($lembur as $d)
                    <a href="{{ route('lembur.createpresensi', Crypt::encrypt($d->id)) }}" class="block">
                        <div class="bg-white rounded-[12px] mb-2 p-3 flex items-center gap-3" style="border:1px solid #1f7ee420; box-shadow: 0 1px 4px rgba(0,0,0,0.04);">
                            <div class="shrink-0 flex items-center justify-center rounded-[10px]" style="width:45px; height:45px; background:#1f7ee418;">
                                <ion-icon name="timer-outline" style="font-size:24px; color:#1f7ee4;"></ion-icon>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-center">
                                    <h5 style="font-size:14px; font-weight:600; color:#333; margin:0;">{{ DateToIndo($d->tanggal) }}</h5>
                                    @if ($d->status == 0)
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase" style="background:#fff7ed; color:#ea580c; border:1px solid #ffedd5;">Menunggu</span>
                                    @elseif($d->status == 1)
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase" style="background:#f0fdf4; color:#16a34a; border:1px solid #dcfce7;">Disetujui</span>
                                    @endif
                                </div>
                                <p style="font-size:12px; color:#888; margin:2px 0;">{{ $d->keterangan }}</p>
                                <div class="flex items-center gap-1 flex-wrap">
                                    @if ($d->lembur_in != null)
                                        <span style="background:#28a74518; color:#28a745; font-size:10px; padding:1px 6px; border-radius:10px;">{{ date('H:i', strtotime($d->lembur_in)) }}</span>
                                    @else
                                        <span style="background:#ff525218; color:#ff5252; font-size:10px; padding:1px 6px; border-radius:10px;">Belum Absen</span>
                                    @endif
                                    <span style="color:#ccc; font-size:10px;">-</span>
                                    @if ($d->lembur_out != null)
                                        <span style="background:#28a74518; color:#28a745; font-size:10px; padding:1px 6px; border-radius:10px;">{{ date('H:i', strtotime($d->lembur_out)) }}</span>
                                    @else
                                        <span style="background:#ff525218; color:#ff5252; font-size:10px; padding:1px 6px; border-radius:10px;">Belum Absen</span>
                                    @endif
                                    <span style="color:#999; font-size:10px; margin-left:4px;">
                                        ({{ date('H:i', strtotime($d->lembur_mulai)) }} - {{ date('H:i', strtotime($d->lembur_selesai)) }})
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
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

        {{-- ===== BOTTOM NAV ===== --}}
        <div style="height: 100px;"></div>
        @include('layouts.mobile.bottomNav')

    {{-- ===== SCRIPTS ===== --}}
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

        // History Tab Switching
        function switchTab(tab) {
            var primary = '{{ $t["primary"] ?? "#1E4D3E" }}';
            var tabPresensi = document.getElementById('tabPresensi');
            var tabLembur = document.getElementById('tabLembur');
            if (tab === 'presensi') {
                document.getElementById('contentPresensi').style.display = '';
                document.getElementById('contentLembur').style.display = 'none';
                tabPresensi.style.background = primary;
                tabPresensi.style.color = 'white';
                tabPresensi.style.boxShadow = '0 1px 2px rgba(0,0,0,0.06)';
                tabLembur.style.background = 'transparent';
                tabLembur.style.color = '#64748b';
                tabLembur.style.boxShadow = 'none';
            } else {
                document.getElementById('contentPresensi').style.display = 'none';
                document.getElementById('contentLembur').style.display = '';
                tabLembur.style.background = primary;
                tabLembur.style.color = 'white';
                tabLembur.style.boxShadow = '0 1px 2px rgba(0,0,0,0.06)';
                tabPresensi.style.background = 'transparent';
                tabPresensi.style.color = '#64748b';
                tabPresensi.style.boxShadow = 'none';
            }
        }

        // Face Recognition Registration Handler
        $("#btnDaftarkanWajah").click(function(e) {
            e.preventDefault();
            window.location.href = "{{ route('facerecognition.karyawan.create') }}";
        });

        // Presensi Detail Modal Handler
        $(".presensi-card").click(function() {
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

        $(".modal-close").click(function() {
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

    @if ($errors->any())
        <script>
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        </script>
    @endif
</body>
</html>

