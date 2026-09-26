@extends('layouts.mobile.modern')

@section('title', 'E-Presensi')

@section('header_left')
    <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('dashboard.index') }}"
        onclick="if (window.history.length > 1 && document.referrer && document.referrer.indexOf(window.location.host) !== -1) { event.preventDefault(); window.history.back(); }"
        class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/15 text-white active:scale-90 transition-transform"
        title="Kembali">
        <ion-icon name="chevron-back-outline" class="text-base"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        /* Override modern layout main padding for camera view */
        main { padding-left: 0 !important; padding-right: 0 !important; padding-top: calc(3.5rem + env(safe-area-inset-top)) !important; }
    </style>
@endpush

@section('content')
    {{-- <style>
        :root {
            --bg-body: #dff9fb;
            --bg-nav: #ffffff;
            --color-nav: #634832;
            --color-nav-active: #58907D;
            --bg-indicator: #634832;
            --color-nav-hover: #3ab58c;
        }
    </style> --}}
    <style>
        /* CSS Variables untuk memudahkan penyesuaian */
        :root {
            --header-height: 60px;
            --info-section-height: 70px;
            --action-section-height: 65px;
            --bottomnav-height: 75px;
            --padding-total: 20px;
            /* Padding dikurangi agar button hampir menyentuh bottomnav */
        }

        /* Tambahan agar kamera portrait dan rounded di semua device */
        .webcam-capture {
            width: 100%;
            max-width: 98vw;
            /* Tinggi dinamis: viewport height dikurangi semua elemen lain */
            /* Formula menggunakan CSS variables untuk fleksibilitas */
            height: calc(100vh - var(--header-height) - var(--info-section-height) - var(--action-section-height) - var(--bottomnav-height) - var(--padding-total));
            min-height: 180px;
            max-height: 400px;
            margin: 0 auto;
            padding: 0;
            border-radius: 24px;
            overflow: hidden;
            background: #222;
            position: relative;
            box-shadow: 0 4px 24px rgba(44, 62, 80, 0.10);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .webcam-capture video,
        .webcam-capture canvas {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover;
            /* Pastikan proporsional */
            border-radius: 24px !important;
            display: block;
            /* background: #222; */
        }



        #map {
            height: 200px;
            width: 100%;
            margin-bottom: 10px;
            opacity: 0.8;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        canvas {
            position: absolute;
            border-radius: 0;
            box-shadow: none;
            pointer-events: none !important;
        }

        #facedetection {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            height: 100%;
            margin: 0 !important;
            /* Menghapus margin */
            padding: 0 !important;
            /* Menghapus padding */
            width: 100% !important;
            /* Memastikan lebar penuh */
        }

        /* Tambahkan style untuk indikator loading maps */
        #map-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 10px;
            border-radius: 5px;
        }

        /* Perbaikan untuk posisi content-section */
        #content-section {
            margin-top: 0 !important;
            padding: 0 !important;
            padding-bottom: 0 !important;
            /* Padding dihapus karena tinggi kamera sudah dihitung dengan calc() */
            position: relative;
            z-index: 1;
            overflow: visible;

            /* Ubah ke visible agar konten tidak terpotong */
        }

        /* Style untuk tombol scan */
        .scan-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 0 10px;
        }

        #listcabang {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            bottom: 20px;
            width: 92%;
            display: flex;
            justify-content: center;
            z-index: 20;
            margin-top: 0;
        }

        #listcabang .select-wrapper {
            position: relative;
            width: 90%;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
            }

            70% {
                box-shadow: 0 0 0 5px rgba(255, 255, 255, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
            }
        }

        #listcabang .select-wrapper::before {
            content: "";
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>');
            background-repeat: no-repeat;
            background-position: center;
            pointer-events: none;
        }

        #listcabang select {
            width: 100%;
            height: 45px;
            border-radius: 10px;
            background-color: rgba(15, 23, 42, 0.85);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 0 15px 0 45px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        #listcabang select:hover {
            background-color: rgba(0, 0, 0, 0.6);
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        #listcabang select:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.7);
            background-color: rgba(0, 0, 0, 0.7);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2);
        }

        #listcabang select option {
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
        }

        /* Tambahkan arrow icon kustom */
        #listcabang .select-wrapper::after {
            content: "";
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 12px;
            height: 12px;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>');
            background-repeat: no-repeat;
            background-position: center;
            pointer-events: none;
        }

        .scan-button {
            height: 45px !important;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            width: 42%;
        }

        .scan-button ion-icon {
            margin-right: 5px;
        }

        /* Style untuk jam digital */
        .jam-digital-malasngoding {
            background-color: rgba(39, 39, 39, 0.7);
            position: absolute;
            top: 65px;
            /* Di bawah header */
            right: 15px;
            /* Menambah margin kanan */
            z-index: 20;
            width: 150px;
            border-radius: 10px;
            padding: 5px;
        }

        .jam-digital-malasngoding p {
            color: #fff;
            font-size: 16px;
            text-align: left;
            margin-top: 0;
            margin-bottom: 0;
        }

        /* Style modern untuk box deteksi wajah */
        .face-detection-box {
            border: 2px solid #4CAF50;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(76, 175, 80, 0.5);
            transition: all 0.3s ease;
        }

        .face-detection-box.unknown {
            border-color: #F44336;
            box-shadow: 0 0 10px rgba(244, 67, 54, 0.5);
        }

        .face-detection-label {
            background-color: rgba(76, 175, 80, 0.8);
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .face-detection-label.unknown {
            background-color: rgba(244, 67, 54, 0.8);
        }

        /* Modern Minimalist Presensi Wrapper — DESIGN.md (No Outer Card Nesting) */
        .presensi-content-modern {
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 6px auto 14px auto;
            max-width: 440px;
            width: 100%;
            box-sizing: border-box;
        }

        .camera-section {
            padding: 0;
            margin-bottom: 10px;
            position: relative;
            width: 100%;
            border-radius: 18px;
            overflow: hidden;
            background: #0f172a;
        }

        /* Minimalist Viewfinder */
        .webcam-capture {
            width: 100% !important;
            height: 270px !important;
            min-height: 240px;
            max-height: 320px;
            margin: 0;
            padding: 0;
            border-radius: 18px;
            overflow: hidden;
            background: #0f172a;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .webcam-capture video,
        .webcam-capture canvas {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover;
            border-radius: 18px !important;
            display: block;
        }

        /* Frosted Glass Telemetry Badges */
        .abs-tanggal-modern {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 8px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 600;
            color: #ffffff;
            z-index: 25;
            letter-spacing: 0.3px;
        }

        .abs-jam-modern {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 8px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
            color: #ffffff;
            z-index: 25;
            font-family: 'JetBrains Mono', 'Geist Mono', monospace;
            letter-spacing: 0.5px;
        }

        /* Mini Radar GPS Map (Picture-In-Picture Bottom-Right) */
        .map-absolute-section {
            position: absolute;
            bottom: 10px;
            right: 10px;
            z-index: 25;
            width: 100px;
            height: 70px;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid #ffffff;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
            background: #e2e8f0;
        }

        .map-absolute-section #map {
            width: 100% !important;
            height: 100% !important;
            opacity: 1 !important;
            margin: 0 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }

        /* Minimalist Shift Info Card (Flexbox, Zero Negative Margins) */
        .shift-info-card {
            background: #3C2A21; /* Espresso Emerald */
            border-radius: 14px;
            padding: 8px 10px;
            margin-bottom: 8px;
            width: 100%;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(60, 42, 33, 0.12);
        }

        .shift-info-col {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            box-sizing: border-box;
        }

        .shift-info-col:not(:last-child) {
            border-right: 1px solid rgba(255, 255, 255, 0.15);
            padding-right: 4px;
        }

        .shift-info-col:not(:first-child) {
            padding-left: 4px;
        }

        .shift-info-col.col-shift-name {
            flex: 1.35;
        }

        .shift-info-lbl {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 2px;
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .shift-info-lbl ion-icon {
            font-size: 12px;
        }

        .shift-info-val {
            font-size: 14px;
            font-weight: 700;
            color: #ffffff;
            font-family: 'JetBrains Mono', 'Geist Mono', monospace;
            white-space: nowrap;
        }

        .shift-info-val.shift-title {
            font-family: 'Inter', 'Poppins', sans-serif;
            font-size: 12px;
            font-weight: 600;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }

        /* Dynamic Shift Status Bar */
        .shift-status-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 7px 10px;
            margin-bottom: 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            gap: 6px;
            text-align: center;
            line-height: 1.3;
            width: 100%;
            box-sizing: border-box;
        }

        .shift-status-bar.status-early {
            background: rgba(217, 119, 6, 0.08);
            color: #D97706;
            border: 1px solid rgba(217, 119, 6, 0.18);
        }

        .shift-status-bar.status-ontime {
            background: rgba(5, 150, 105, 0.08);
            color: #059669;
            border: 1px solid rgba(5, 150, 105, 0.18);
        }

        .shift-status-bar.status-late {
            background: rgba(217, 119, 6, 0.12);
            color: #B45309;
            border: 1px solid rgba(217, 119, 6, 0.25);
        }

        .shift-status-bar.status-closed {
            background: rgba(220, 38, 38, 0.08);
            color: #DC2626;
            border: 1px solid rgba(220, 38, 38, 0.18);
        }

        /* Action Buttons — Minimalist & Tactile */
        .action-section {
            display: flex;
            gap: 10px;
            width: 100%;
            box-sizing: border-box;
        }

        .action-section .scan-button {
            flex: 1;
            height: 48px !important;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 600;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
            box-sizing: border-box;
        }

        .action-section .scan-button:active:not(:disabled) {
            transform: scale(0.97) translateY(1px);
        }

        #absenmasuk {
            background: #3C2A21;
            color: #ffffff;
            box-shadow: 0 3px 12px rgba(60, 42, 33, 0.2);
        }

        #absenmasuk:disabled {
            background: #cbd5e1 !important;
            color: #64748b !important;
            box-shadow: none !important;
            cursor: not-allowed;
            opacity: 0.8;
        }

        #absenpulang {
            background: #ffffff;
            color: #3C2A21;
            border: 1.5px solid #3C2A21 !important;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
        }

        #absenpulang:disabled {
            background: #f8fafc !important;
            color: #94a3b8 !important;
            border-color: #e2e8f0 !important;
            box-shadow: none !important;
            cursor: not-allowed;
            opacity: 0.7;
        }

        /* Minimalist Pure White Card: Attendance Selesai */
        .presensi-selesai-card {
            background: #ffffff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 20px;
            padding: 28px 16px;
            box-shadow: 0 6px 24px rgba(15, 23, 42, 0.04);
            text-align: center;
            margin: 10px auto;
            max-width: 440px;
            box-sizing: border-box;
        }

        .selesai-badge {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(5, 150, 105, 0.08);
            border: 1px solid rgba(5, 150, 105, 0.15);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
        }

        .selesai-badge ion-icon {
            font-size: 34px;
            color: #059669;
        }

        .selesai-title {
            font-family: 'Outfit', 'Geist', 'Poppins', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: #0F172A;
            margin: 0 0 6px 0;
            letter-spacing: -0.01em;
            line-height: 1.3;
        }

        .selesai-subtitle {
            font-size: 13px;
            color: #64748B;
            margin: 0 0 4px 0;
            line-height: 1.5;
        }

        /* Seamless flat stats row (no card inside card) */
        .selesai-rekap-box {
            background: transparent;
            border-top: 1px solid rgba(15, 23, 42, 0.08);
            border-bottom: 1px solid rgba(15, 23, 42, 0.08);
            border-left: none;
            border-right: none;
            border-radius: 0;
            padding: 16px 4px;
            display: flex;
            align-items: center;
            justify-content: space-around;
            margin: 18px 0 16px;
            gap: 4px;
        }

        .selesai-rekap-col {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .selesai-rekap-divider {
            width: 1px;
            height: 28px;
            background: rgba(15, 23, 42, 0.08);
        }

        .selesai-rekap-lbl {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748B;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .selesai-rekap-val {
            font-size: 13px;
            font-weight: 700;
            color: #0F172A;
        }

        .selesai-rekap-val.font-mono {
            font-family: 'JetBrains Mono', 'Geist Mono', monospace;
            font-size: 15px;
            color: #3C2A21;
        }

        /* Minimalist verified inline indicator (no card box) */
        .selesai-verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: transparent;
            color: #059669;
            font-size: 12px;
            font-weight: 600;
            padding: 0;
            border-radius: 0;
            margin-bottom: 20px;
            border: none;
        }

        .btn-selesai-dashboard {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            height: 48px;
            background: #3C2A21;
            color: #ffffff !important;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none !important;
            box-shadow: 0 4px 14px rgba(60, 42, 33, 0.2);
            transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
            box-sizing: border-box;
        }

        .btn-selesai-dashboard:active {
            transform: scale(0.98);
        }

        /* Responsive untuk berbagai resolusi layar */
        /* Layar kecil (mobile portrait) - tinggi layar ≤ 667px */
        @media screen and (max-height: 667px) {
            .webcam-capture {
                /* Untuk layar kecil, kurangi padding agar kamera lebih besar */
                height: calc(100vh - 270px);
                min-height: 160px;
                max-height: 300px;
            }

            .presensi-content-modern {
                margin-bottom: 10px;
            }
        }

        /* Layar sedang (mobile landscape / tablet portrait) - tinggi 668px - 900px */
        @media screen and (min-height: 668px) and (max-height: 900px) {
            .webcam-capture {
                /* Untuk layar sedang, tinggi lebih besar dengan padding minimal */
                height: calc(100vh - 290px);
                min-height: 200px;
                max-height: 380px;
            }

            .presensi-content-modern {
                margin-bottom: 10px;
            }
        }

        /* Layar besar (tablet landscape / desktop) - tinggi ≥ 901px */
        @media screen and (min-height: 901px) {
            .webcam-capture {
                /* Untuk layar besar, tinggi maksimal dengan padding minimal */
                height: calc(100vh - 310px);
                min-height: 240px;
                max-height: 480px;
            }

            .presensi-content-modern {
                margin-bottom: 10px;
            }
        }

        /* Landscape orientation - tinggi layar ≤ 500px */
        @media screen and (orientation: landscape) and (max-height: 500px) {
            .webcam-capture {
                /* Untuk landscape, kurangi padding agar kamera lebih besar */
                height: calc(100vh - 250px);
                min-height: 140px;
                max-height: 220px;
            }

            .presensi-content-modern {
                margin-bottom: 10px;
            }
        }

        /* Pastikan action-section tidak tertutup bottomnav */
        .action-section {
            margin-bottom: 5px;
            padding-bottom: 0;
        }

        /* SKELETON LOADING STYLES */
        .content-hide {
            display: none !important;
        }

        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s ease-in-out infinite;
            border-radius: 8px;
        }

        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .skeleton-camera {
            width: 100%;
            /* Approximating the calc height for skeleton */
            height: calc(100vh - 270px); 
            min-height: 200px;
            border-radius: 24px;
            margin-bottom: 15px;
        }

        .skeleton-text {
            height: 16px;
            margin-bottom: 8px;
            border-radius: 4px;
        }

        .skeleton-block {
            display: block;
        }

        .skeleton-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .skeleton-col {
            flex: 1;
            height: 60px;
            border-radius: 12px;
        }

        .skeleton-btn {
            height: 45px;
            border-radius: 22px;
            flex: 1;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/leaflet.css') }}" />
    <!-- Import Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="{{ asset('assets/external/js/leaflet.js') }}"></script>
    <div id="content-section">
        @php
            $sudah_selesai = ($presensi && !empty($presensi->jam_in) && !empty($presensi->jam_out));
        @endphp

        @if (!$sudah_selesai)
            <!-- SKELETON LOADER -->
            <div id="skeleton-loader" class="presensi-content-modern" style="background: transparent; box-shadow: none;">
                <!-- Camera Skeleton -->
                <div class="skeleton skeleton-camera"></div>
                
                <!-- Info Skeleton -->
                <div class="info-section" style="background: white; margin-bottom: 15px;">
                    <div class="skeleton-row">
                        <div class="skeleton skeleton-col"></div>
                        <div class="skeleton skeleton-col"></div>
                        <div class="skeleton skeleton-col"></div>
                    </div>
                </div>

                <!-- Button Skeleton -->
                <div class="action-section">
                    <div class="skeleton skeleton-btn"></div>
                    <div class="skeleton skeleton-btn"></div>
                </div>
            </div>
        @endif

        <div id="real-content" class="presensi-content-modern {{ $sudah_selesai ? '' : 'content-hide' }}">
            @if ($sudah_selesai)
                {{-- TAMPILAN PUTIH MINIMALIST: SUDAH SELESAI BEKERJA HARI INI --}}
                <div class="presensi-selesai-card">
                    <div class="selesai-badge">
                        <ion-icon name="checkmark-done-circle-outline"></ion-icon>
                    </div>
                    <h2 class="selesai-title">Anda Telah Selesai Bekerja Hari Ini</h2>
                    <p class="selesai-subtitle">Presensi masuk dan pulang Anda telah tercatat lengkap.</p>
                    
                    <div class="selesai-rekap-box">
                        <div class="selesai-rekap-col">
                            <span class="selesai-rekap-lbl"><ion-icon name="calendar-outline"></ion-icon> Shift</span>
                            <span class="selesai-rekap-val">{{ $jam_kerja->nama_jam_kerja }}</span>
                        </div>
                        <div class="selesai-rekap-divider"></div>
                        <div class="selesai-rekap-col">
                            <span class="selesai-rekap-lbl"><ion-icon name="log-in-outline"></ion-icon> Masuk</span>
                            <span class="selesai-rekap-val font-mono">{{ date('H:i', strtotime($presensi->jam_in)) }}</span>
                        </div>
                        <div class="selesai-rekap-divider"></div>
                        <div class="selesai-rekap-col">
                            <span class="selesai-rekap-lbl"><ion-icon name="log-out-outline"></ion-icon> Pulang</span>
                            <span class="selesai-rekap-val font-mono">{{ date('H:i', strtotime($presensi->jam_out)) }}</span>
                        </div>
                    </div>

                    <div class="selesai-verified-badge">
                        <ion-icon name="shield-checkmark-outline"></ion-icon>
                        <span>Kehadiran Terverifikasi</span>
                    </div>

                    <a href="/dashboard" class="btn-selesai-dashboard">
                        <ion-icon name="home-outline"></ion-icon>
                        <span>Kembali ke Dashboard</span>
                    </a>
                </div>
            @else
                <div id="active-presensi-wrapper">
                    @if (!empty($hari_libur))
                        <div style="margin: 0 0 12px 0; padding: 12px 14px; background: #FFFFFF; border: 1px solid rgba(15, 23, 42, 0.08); border-left: 3px solid #3C2A21; border-radius: 12px; display: flex; align-items: center; gap: 12px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid rgba(60, 42, 33, 0.12);">
                                <ion-icon name="calendar-outline" style="font-size: 18px; color: #3C2A21;"></ion-icon>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 9.5px; font-weight: 800; color: #3C2A21; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'JetBrains Mono', monospace; line-height: 1;">HARI LIBUR OPERASIONAL</div>
                                <div style="font-size: 13px; font-weight: 800; color: #0F172A; margin-top: 2px; line-height: 1.25; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $hari_libur->keterangan }}</div>
                                <div style="font-size: 11px; color: #64748B; margin-top: 2px; line-height: 1.3;">Bebas kewajiban presensi. Kehadiran tercatat sebagai operasional khusus tanpa penalti keterlambatan.</div>
                            </div>
                        </div>
                    @endif
                    <div class="camera-section">
                        <div id="facedetection" style="position:relative;">
                            <!-- GPS Permission Button / Status -->
                            <button type="button" id="btn-request-gps" onclick="requestLocationPermission(true)" class="btn btn-sm" style="position: absolute; top: 10px; left: 10px; z-index: 30; pointer-events: auto; cursor: pointer; border-radius: 20px; font-weight: 600; font-size: 11px; padding: 4px 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); display: flex; align-items: center; gap: 5px; border: none; background: #f39c12; color: white;">
                                <ion-icon name="location-outline" style="font-size: 14px;"></ion-icon>
                                <span>Izinkan GPS</span>
                            </button>
                            <!-- Absolute Tanggal & Jam -->
                            <div class="abs-tanggal-modern">{{ DateToIndo(date('Y-m-d')) }}</div>
                            <div class="abs-jam-modern"><span id="jam"></span></div>
                            <div class="webcam-capture"></div>
                            
                            <!-- Mini Radar GPS Map (Corner Picture-In-Picture) -->
                            <div class="map-absolute-section">
                                <div id="map">
                                    <div id="map-loading">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($general_setting->multi_lokasi)
                                <div id="listcabang">
                                    <div class="select-wrapper">
                                        <select name="cabang" id="cabang" class="form-control">
                                            @foreach ($cabang as $item)
                                                <option {{ $item->kode_cabang == $karyawan->kode_cabang ? 'selected' : '' }}
                                                    value="{{ $item->lokasi_cabang }}">
                                                    {{ $item->nama_cabang }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Minimalist Shift Info Card -->
                    <div class="shift-info-card">
                        <div class="shift-info-col col-shift-name">
                            <span class="shift-info-lbl">
                                <ion-icon name="calendar-outline"></ion-icon> Shift
                            </span>
                            <span class="shift-info-val shift-title" title="{{ $jam_kerja->nama_jam_kerja }}">{{ $jam_kerja->nama_jam_kerja }}</span>
                        </div>
                        <div class="shift-info-col">
                            <span class="shift-info-lbl">
                                <ion-icon name="log-in-outline"></ion-icon> Masuk
                            </span>
                            <span class="shift-info-val">{{ date('H:i', strtotime($jam_kerja->jam_masuk)) }}</span>
                        </div>
                        <div class="shift-info-col">
                            <span class="shift-info-lbl">
                                <ion-icon name="log-out-outline"></ion-icon> Pulang
                            </span>
                            <span class="shift-info-val">{{ date('H:i', strtotime($jam_kerja->jam_pulang)) }}</span>
                        </div>
                    </div>

                    <!-- Dynamic Shift Status Bar -->
                    <div id="shift-status-bar" class="shift-status-bar status-early" style="display:none;">
                        <ion-icon name="time-outline" style="font-size: 15px; flex-shrink: 0;"></ion-icon>
                        <span id="shift-status-text">Menghitung jadwal shift...</span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-section" id="presensi-action-buttons">
                        <button class="scan-button" id="absenmasuk" statuspresensi="masuk">
                            <ion-icon name="finger-print-outline" style="font-size: 20px"></ion-icon>
                            <span>Masuk</span>
                        </button>
                        <button class="scan-button" id="absenpulang" statuspresensi="pulang">
                            <ion-icon name="log-out-outline" style="font-size: 20px"></ion-icon>
                            <span>Pulang</span>
                        </button>
                    </div>
                </div>

                <!-- Template Container Dynamic Completed (Hidden until Pulang completes) -->
                <div id="dynamic-selesai-card" style="display:none;">
                    <div class="presensi-selesai-card">
                        <div class="selesai-badge">
                            <ion-icon name="checkmark-done-circle-outline"></ion-icon>
                        </div>
                        <h2 class="selesai-title">Anda Telah Selesai Bekerja Hari Ini</h2>
                        <p class="selesai-subtitle">Presensi masuk dan pulang Anda telah tercatat lengkap.</p>
                        
                        <div class="selesai-rekap-box">
                            <div class="selesai-rekap-col">
                                <span class="selesai-rekap-lbl"><ion-icon name="calendar-outline"></ion-icon> Shift</span>
                                <span class="selesai-rekap-val">{{ $jam_kerja->nama_jam_kerja }}</span>
                            </div>
                            <div class="selesai-rekap-divider"></div>
                            <div class="selesai-rekap-col">
                                <span class="selesai-rekap-lbl"><ion-icon name="log-in-outline"></ion-icon> Masuk</span>
                                <span class="selesai-rekap-val font-mono" id="rekap-jam-in">{{ ($presensi && $presensi->jam_in) ? date('H:i', strtotime($presensi->jam_in)) : '-' }}</span>
                            </div>
                            <div class="selesai-rekap-divider"></div>
                            <div class="selesai-rekap-col">
                                <span class="selesai-rekap-lbl"><ion-icon name="log-out-outline"></ion-icon> Pulang</span>
                                <span class="selesai-rekap-val font-mono" id="rekap-jam-out">-</span>
                            </div>
                        </div>

                        <div class="selesai-verified-badge">
                            <ion-icon name="shield-checkmark-outline"></ion-icon>
                            <span>Kehadiran Terverifikasi</span>
                        </div>

                        <a href="/dashboard" class="btn-selesai-dashboard">
                            <ion-icon name="home-outline"></ion-icon>
                            <span>Kembali ke Dashboard</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>

    <audio id="notifikasi_radius">
        <source src="{{ asset('assets/sound/radius.mp3') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_mulaiabsen">
        <source src="{{ asset('assets/sound/mulaiabsen.wav') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_akhirabsen">
        <source src="{{ asset('assets/sound/akhirabsen.wav') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_sudahabsen">
        <source src="{{ asset('assets/sound/sudahabsen.wav') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_absenmasuk">
        <source src="{{ asset('assets/sound/absenmasuk.wav') }}" type="audio/mpeg">
    </audio>


    <!--Pulang-->
    <audio id="notifikasi_sudahabsenpulang">
        <source src="{{ asset('assets/sound/sudahabsenpulang.mp3') }}" type="audio/mpeg">
    </audio>
    <audio id="notifikasi_absenpulang">
        <source src="{{ asset('assets/sound/absenpulang.mp3') }}" type="audio/mpeg">
    </audio>
@endsection
@push('myscript')
    <!-- Face Recognition dengan Caching (Lazy Loaded when enabled) -->
    @php
        $activeAttendancePolicy = \App\Models\AttendancePolicy::getActivePolicy();
        $isFaceRecognitionRequired = (($general_setting->face_recognition ?? 0) == 1) 
            && is_module_enabled('face_recognition', true)
            && ($activeAttendancePolicy ? (bool)$activeAttendancePolicy->require_face_recognition : true);
        $isGpsRequired = is_module_enabled('gps', true);
    @endphp
    <!-- Face Recognition dengan Caching (Lazy Loaded when enabled & required) -->
    @if ($isFaceRecognitionRequired)
    <script src="{{ asset('assets/vendor/face-api.min.js') }}"></script>
    <script src="{{ asset('assets/external/js/face-model-cache.js') }}?v={{ file_exists(public_path('assets/external/js/face-model-cache.js')) ? filemtime(public_path('assets/external/js/face-model-cache.js')) : time() }}"></script>
    @endif
    <!-- Anti-Fake GPS & Mock Location Detector -->
    <script src="{{ asset('assets/js/anti-fake-gps.js') }}?v={{ file_exists(public_path('assets/js/anti-fake-gps.js')) ? filemtime(public_path('assets/js/anti-fake-gps.js')) : time() }}"></script>
    <script type="text/javascript">
        // Fungsi yang dijalankan ketika halaman selesai dimuat
        // Menggunakan DOMContentLoaded untuk memastikan DOM sudah siap
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                jam();
            });
        } else {
            // DOM sudah dimuat, langsung panggil jam()
            jam();
        }

        // Fungsi untuk menampilkan waktu secara real-time
        function jam() {
            // Mengambil elemen HTML dengan id 'jam'
            var e = document.getElementById('jam');

            // Cek apakah elemen ada sebelum mengatur innerHTML
            if (!e) {
                // Jika elemen belum tersedia, coba lagi setelah 100ms
                setTimeout(jam, 100);
                return;
            }

            // Membuat objek Date untuk mendapatkan waktu saat ini
            var d = new Date(),
                // Variabel untuk menampung jam, menit, dan detik
                h, m, s;
            // Mengambil jam dari objek Date
            h = d.getHours();
            // Mengambil menit dari objek Date dan menambahkan '0' di depan jika kurang dari 10
            m = set(d.getMinutes());
            // Mengambil detik dari objek Date dan menambahkan '0' di depan jika kurang dari 10
            s = set(d.getSeconds());

            // Menampilkan waktu dalam format HH:MM:SS
            e.innerHTML = h + ':' + m + ':' + s;

            // Mengatur waktu untuk memanggil fungsi jam() lagi setelah 1 detik
            setTimeout(jam, 1000);
        }

        // Fungsi untuk menambahkan '0' di depan angka jika kurang dari 10
        function set(e) {
            // Jika angka kurang dari 10, tambahkan '0' di depan
            e = e < 10 ? '0' + e : e;
            // Mengembalikan angka yang telah ditambahkan '0' di depan jika perlu
            return e;
        }
    </script>
    <script>
        // Fungsi yang dijalankan ketika dokumen siap
        $(function() {
            const themePrimary = '{{ $t['primary'] ?? '#3C2A21' }}';
            const themeSecondary = '{{ $t['primary_light'] ?? '#634832' }}';
            // Variabel untuk menampung lokasi
            let lokasi;
            // Variabel untuk menampung lokasi user
            let lokasi_user;
            let multi_lokasi = {{ $general_setting->multi_lokasi }};
            let lokasi_cabang = multi_lokasi ? document.getElementById('cabang').value :
                "{{ $lokasi_kantor->lokasi_cabang }}";
            // Variabel map global & pengecekan izin
            let map;
            let mapCircle = null; // Circle untuk lokasi cabang
            let mapMarker = null; // Marker user untuk update posisi
            let geoWatchId = null; // ID watchPosition untuk cleanup
            let lastRawPosition = null; // Raw GeolocationPosition object for mock detection
            let cameraPermissionGranted = false;
            let locationPermissionGranted = false;
            let cameraPermissionDenied = false;
            let locationPermissionDenied = false;
            let cameraPermissionAlertShown = false;
            let locationPermissionAlertShown = false;

            // =========================================================
            // PARALLEL GPS: Start geolocation IMMEDIATELY with watchPosition
            // Phase 1: Accept cached position (up to 30s old) for instant fix
            // Phase 2: watchPosition with high accuracy for continuous refinement
            // This runs in parallel with camera & face recognition init.
            // =========================================================
            let geoPositionPromise = null;
            let geoFirstPositionReceived = false;
            const geoStartTime = performance.now();

            const isGpsRequired = {{ $isGpsRequired ? 'true' : 'false' }};
            if (isGpsRequired && navigator.geolocation) {
                console.log('[GPS] Starting watchPosition early (parallel with camera)...');

                geoPositionPromise = new Promise((resolve) => {
                    // Start watchPosition with optimized options
                    geoWatchId = navigator.geolocation.watchPosition(
                        (position) => {
                            lastRawPosition = position;
                            if (window.AntiFakeGPS) {
                                window.AntiFakeGPS.recordSample(position);
                            }
                            const elapsed = (performance.now() - geoStartTime).toFixed(0);
                            const acc = position.coords.accuracy ? position.coords.accuracy.toFixed(0) : '?';
                            console.log(`[GPS] Position update: accuracy=${acc}m, elapsed=${elapsed}ms`);

                            // Always update lokasi with latest position
                            lokasi = position.coords.latitude + "," + position.coords.longitude;

                            // First position: resolve the promise for map init
                            if (!geoFirstPositionReceived) {
                                geoFirstPositionReceived = true;
                                console.log(`[GPS] First fix in ${elapsed}ms (accuracy: ${acc}m)`);
                                resolve({ success: true, position: position });
                            } else {
                                // Subsequent updates: move marker if map exists
                                if (mapMarker) {
                                    mapMarker.setLatLng([position.coords.latitude, position.coords.longitude]);
                                    console.log(`[GPS] Marker updated (accuracy: ${acc}m)`);
                                }
                            }

                            // Auto-stop watching once accuracy is good enough (< 30m)
                            if (position.coords.accuracy && position.coords.accuracy < 30 && geoFirstPositionReceived) {
                                console.log(`[GPS] Accuracy sufficient (${acc}m), stopping watchPosition`);
                                if (geoWatchId !== null) {
                                    navigator.geolocation.clearWatch(geoWatchId);
                                    geoWatchId = null;
                                }
                            }
                        },
                        (error) => {
                            const elapsed = (performance.now() - geoStartTime).toFixed(0);
                            console.warn(`[GPS] Error after ${elapsed}ms:`, error.message);
                            if (!geoFirstPositionReceived) {
                                geoFirstPositionReceived = true;
                                resolve({ success: false, error: error });
                            }
                        },
                        {
                            enableHighAccuracy: true,  // Use GPS on mobile for best accuracy
                            maximumAge: 30000,         // Accept cached position up to 30s old (instant first fix)
                            timeout: 15000             // Wait max 15s before error
                        }
                    );
                });
            }
            // Mengambil elemen HTML dengan id 'notifikasi_radius'
            let notifikasi_radius = document.getElementById('notifikasi_radius');
            // Mengambil elemen HTML dengan id 'notifikasi_mulaiabsen'
            let notifikasi_mulaiabsen = document.getElementById('notifikasi_mulaiabsen');
            // Mengambil elemen HTML dengan id 'notifikasi_akhirabsen'
            let notifikasi_akhirabsen = document.getElementById('notifikasi_akhirabsen');
            // Mengambil elemen HTML dengan id 'notifikasi_sudahabsen'
            let notifikasi_sudahabsen = document.getElementById('notifikasi_sudahabsen');
            // Mengambil elemen HTML dengan id 'notifikasi_absenmasuk'
            let notifikasi_absenmasuk = document.getElementById('notifikasi_absenmasuk');

            // Mengambil elemen HTML dengan id 'notifikasi_sudahabsenpulang'
            let notifikasi_sudahabsenpulang = document.getElementById('notifikasi_sudahabsenpulang');
            // Mengambil elemen HTML dengan id 'notifikasi_absenpulang'
            let notifikasi_absenpulang = document.getElementById('notifikasi_absenpulang');

            // Fungsi sintesis suara notifikasi
            function speakVoice(text) {
                if ('speechSynthesis' in window) {
                    try {
                        window.speechSynthesis.cancel();
                        const utterance = new SpeechSynthesisUtterance(text);
                        utterance.lang = 'id-ID';
                        utterance.rate = 0.95;
                        utterance.pitch = 1.0;
                        window.speechSynthesis.speak(utterance);
                    } catch (e) {
                        console.warn('SpeechSynthesis error:', e);
                    }
                }
            }

            // Variabel untuk menampung status face recognition
            let faceRecognitionDetected = 0; // Inisialisasi variabel face recognition detected
            // Mengambil nilai face recognition dari variabel $general_setting->face_recognition, policy, dan module toggle
            let faceRecognition = "{{ $isFaceRecognitionRequired ? 1 : 0 }}";

            // ===== DYNAMIC SHIFT TIME-GATING =====
            const shiftConfig = {
                jamMasuk: "{{ date('H:i', strtotime($jam_kerja->jam_masuk)) }}",
                jamPulang: "{{ date('H:i', strtotime($jam_kerja->jam_pulang)) }}",
                batasi: {{ $general_setting->batasi_absen ?? 0 }},
                batasMasukMenit: {{ ($general_setting->batas_jam_absen ?? 60) * 60 }}, // in seconds
                batasPulangMenit: {{ ($general_setting->batas_jam_absen_pulang ?? 60) * 60 }}, // in seconds
                sudahMasuk: {{ ($presensi && $presensi->jam_in) ? 'true' : 'false' }},
                sudahPulang: {{ ($presensi && $presensi->jam_out) ? 'true' : 'false' }},
                lintasHari: {{ $jam_kerja->lintashari ?? 0 }}
            };

            function parseTimeToday(timeStr) {
                const [h, m] = timeStr.split(':').map(Number);
                const d = new Date();
                d.setHours(h, m, 0, 0);
                return d;
            }

            function updateShiftStatus() {
                const now = new Date();
                const bar = document.getElementById('shift-status-bar');
                const text = document.getElementById('shift-status-text');
                const btnMasuk = document.getElementById('absenmasuk');
                const btnPulang = document.getElementById('absenpulang');
                if (!bar || !text || !btnMasuk || !btnPulang) return;

                bar.style.display = 'flex';

                const jamMasuk = parseTimeToday(shiftConfig.jamMasuk);
                const jamPulang = parseTimeToday(shiftConfig.jamPulang);
                const batasMulaiMasuk = new Date(jamMasuk.getTime() - shiftConfig.batasMasukMenit * 1000);
                const batasAkhirMasuk = new Date(jamMasuk.getTime() + shiftConfig.batasMasukMenit * 1000);
                const batasMulaiPulang = new Date(jamPulang.getTime() - shiftConfig.batasPulangMenit * 1000);

                // Handle lintas hari
                if (shiftConfig.lintasHari && jamPulang <= jamMasuk) {
                    jamPulang.setDate(jamPulang.getDate() + 1);
                    batasMulaiPulang.setDate(batasMulaiPulang.getDate() + 1);
                }

                // Reset classes
                bar.className = 'shift-status-bar';

                // 1. Kondisi: Sudah Absen Masuk DAN Sudah Absen Pulang
                if (shiftConfig.sudahMasuk && shiftConfig.sudahPulang) {
                    bar.classList.add('status-closed');
                    text.innerHTML = '<ion-icon name="checkmark-done-circle-outline"></ion-icon><span>Anda telah selesai bekerja hari ini</span>';
                    btnMasuk.disabled = true;
                    btnPulang.disabled = true;

                    // Switch ke card putih "Anda telah selesai bekerja hari ini"
                    const activeWrapper = document.getElementById('active-presensi-wrapper');
                    const dynamicCard = document.getElementById('dynamic-selesai-card');
                    if (activeWrapper && dynamicCard) {
                        activeWrapper.style.display = 'none';
                        dynamicCard.style.display = 'block';
                    }
                    return;
                }

                // 2. Kondisi: Sudah Absen Masuk, Belum Absen Pulang
                if (shiftConfig.sudahMasuk) {
                    btnMasuk.disabled = true;
                    btnMasuk.innerHTML = '<ion-icon name="checkmark-circle-outline" style="font-size:18px"></ion-icon><span>Sudah Masuk</span>';

                    // Waktu paling awal boleh absen pulang:
                    // Jika batasi_absen aktif dan ada batasPulangMenit > 0, gunakan batasMulaiPulang
                    // Jika tidak, gunakan jamPulang (tidak boleh pulang sebelum jam kerja selesai!)
                    const earliestPulang = (shiftConfig.batasi && shiftConfig.batasPulangMenit > 0) ? batasMulaiPulang : jamPulang;

                    if (now < earliestPulang) {
                        const diffMs = earliestPulang - now;
                        const diffM = Math.ceil(diffMs / 60000);
                        const h = Math.floor(diffM / 60);
                        const m = diffM % 60;
                        const sisaStr = (h > 0 ? h + ' jam ' : '') + m + ' menit';
                        bar.classList.add('status-early');
                        text.innerHTML = '<span>Belum waktunya pulang (Buka pkl ' + shiftConfig.jamPulang + ' &bull; ' + sisaStr + ' lagi)</span>';
                        btnPulang.disabled = true;
                    } else {
                        bar.classList.add('status-ontime');
                        text.innerHTML = '<span>Waktu shift selesai. Silakan presensi pulang</span>';
                        btnPulang.disabled = false;
                    }
                    return;
                }

                // 3. Kondisi: Belum Absen Masuk
                btnPulang.disabled = true; // Tidak bisa pulang jika belum masuk

                if (shiftConfig.batasi) {
                    if (now < batasMulaiMasuk) {
                        const diffMs = batasMulaiMasuk - now;
                        const diffM = Math.ceil(diffMs / 60000);
                        const h = Math.floor(diffM / 60);
                        const m = diffM % 60;
                        bar.classList.add('status-early');
                        text.innerHTML = '<span>Absen masuk dibuka dalam ' + (h > 0 ? h + ' jam ' : '') + m + ' menit</span>';
                        btnMasuk.disabled = true;
                    } else if (now > batasAkhirMasuk) {
                        bar.classList.add('status-closed');
                        text.innerHTML = '<span>Waktu absen masuk sudah ditutup</span>';
                        btnMasuk.disabled = true;
                    } else if (now > jamMasuk) {
                        const diffMs = now - jamMasuk;
                        const diffM = Math.floor(diffMs / 60000);
                        const h = Math.floor(diffM / 60);
                        const m = diffM % 60;
                        const waktuStr = (h > 0 ? h + ' jam ' : '') + (m > 0 || h === 0 ? m + ' menit' : '');
                        bar.classList.add('status-late');
                        text.innerHTML = '<span>Anda terlambat ' + waktuStr.trim() + '</span>';
                        btnMasuk.disabled = false;
                    } else {
                        bar.classList.add('status-ontime');
                        text.innerHTML = '<span>Silakan presensi masuk &bull; Tepat waktu</span>';
                        btnMasuk.disabled = false;
                    }
                } else {
                    if (now > jamMasuk) {
                        const diffMs = now - jamMasuk;
                        const diffM = Math.floor(diffMs / 60000);
                        const h = Math.floor(diffM / 60);
                        const m = diffM % 60;
                        const waktuStr = (h > 0 ? h + ' jam ' : '') + (m > 0 || h === 0 ? m + ' menit' : '');
                        bar.classList.add('status-late');
                        text.innerHTML = '<span>Anda terlambat ' + waktuStr.trim() + '</span>';
                    } else {
                        bar.classList.add('status-ontime');
                        text.innerHTML = '<span>Silakan presensi masuk</span>';
                    }
                    btnMasuk.disabled = false;
                }
            }

            // Jalankan segera dan update setiap 10 detik
            updateShiftStatus();
            setInterval(updateShiftStatus, 10000);

            // --- Tambahkan deteksi device mobile di awal script ---
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            // Fungsi untuk inisialisasi webcam
                // ===============================================
                // WEBCAM & FACE RECOGNITION MODERN SYSTEM
                // ===============================================
                // Logika webcam dan face recognition telah dipindahkan 
                // ke blok modular di bagian bawah script ini.
                // ===============================================


            // SKELETON LOADING LOGIC moved to App.init() for better synchronization


            // Map initialization moved to App.init() for robustness

            // Fungsi untuk memuat peta

            // Fungsi yang dijalankan ketika geolocation berhasil
            function successCallback(position) {
                locationPermissionGranted = true;
                locationPermissionDenied = false;
                locationPermissionAlertShown = false;
                
                lastRawPosition = position;
                if (window.AntiFakeGPS) {
                    window.AntiFakeGPS.recordSample(position);
                }

                // Update GPS status button
                const btnGps = document.getElementById('btn-request-gps');
                if (btnGps) {
                    btnGps.className = "btn btn-sm btn-success";
                    btnGps.style.background = "#27ae60";
                    btnGps.style.boxShadow = "0 4px 12px rgba(39, 174, 96, 0.35)";
                    btnGps.innerHTML = '<ion-icon name="checkmark-circle-outline" style="font-size: 15px;"></ion-icon><span>GPS Terhubung</span>';
                    btnGps.disabled = false;
                }

                // === ROBUSTNESS FIX: Check if map container exists ===
                const mapContainer = document.getElementById('map');
                if (!mapContainer) {
                    console.error("Map container '#map' not found in DOM.");
                    return;
                }

                // Update lokasi variable with latest position
                lokasi = position.coords.latitude + "," + position.coords.longitude;

                try {
                    // If map already exists, just update the marker position
                    if (map) {
                        if (mapMarker) {
                            mapMarker.setLatLng([position.coords.latitude, position.coords.longitude]);
                        }
                        map.setView([position.coords.latitude, position.coords.longitude], 18);
                        console.log('[GPS] Map marker updated with new position');
                        return;
                    }

                    // Initialize Leaflet map (first time only)
                    map = L.map('map', {
                        minZoom: 3,
                        maxBounds: [[-85.05112878, -180], [85.05112878, 180]],
                        maxBoundsViscosity: 1.0
                    }).setView([position.coords.latitude, position.coords.longitude], 18);
                    
                    var lokasi_kantor = lokasi_cabang;
                    var lok = lokasi_kantor.split(",");
                    var lat_kantor = lok[0];
                    var long_kantor = lok[1];
                    var radius = "{{ $lokasi_kantor->radius_cabang }}";

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        noWrap: true,
                        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(map);

                    // Store marker reference so watchPosition can update it
                    mapMarker = L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);
                    var circle = L.circle([lat_kantor, long_kantor], {
                        color: 'red',
                        fillColor: '#f03',
                        fillOpacity: 0.5,
                        radius: radius
                    }).addTo(map);
                    mapCircle = circle;

                    // Sembunyikan indikator loading (with null check)
                    const loader = document.getElementById('map-loading');
                    if (loader) loader.style.display = 'none';

                    setTimeout(function() {
                        if (map) map.invalidateSize();
                    }, 500);
                } catch (error) {
                    console.error("Error initializing map:", error);
                    const loader = document.getElementById('map-loading');
                    if (loader) loader.style.display = 'none';
                }
            }

            // Helper to manually request location permission
            function requestLocationPermission(showToast = false) {
                if (!navigator.geolocation) {
                    Swal.fire({
                        icon: 'error',
                        title: 'GPS Tidak Didukung',
                        text: 'Browser Anda tidak mendukung fitur Geolocation GPS.'
                    });
                    return;
                }

                const btnGps = document.getElementById('btn-request-gps');
                if (btnGps && showToast) {
                    btnGps.innerHTML = '<span class="spinner-border spinner-border-sm mr-1" role="status" style="width:12px;height:12px;"></span> Menghubungkan...';
                    btnGps.disabled = true;
                }

                const loader = document.getElementById('map-loading');
                if (loader) {
                    loader.style.display = 'block';
                    loader.innerHTML = '<div class="spinner-border text-primary mr-2" role="status"></div> Mengambil sinyal GPS...';
                }

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        successCallback(position);
                        if (showToast) {
                            Swal.fire({
                                icon: 'success',
                                title: 'GPS Berhasil Diizinkan',
                                text: 'Sinyal lokasi Anda berhasil terhubung.',
                                timer: 2000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        }
                    },
                    function(error) {
                        // Fallback attempt with enableHighAccuracy: false for instant resolution indoors / desktop
                        navigator.geolocation.getCurrentPosition(
                            function(fallbackPos) {
                                successCallback(fallbackPos);
                                if (showToast) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'GPS Terhubung',
                                        text: 'Lokasi Anda berhasil diverifikasi.',
                                        timer: 2000,
                                        showConfirmButton: false,
                                        toast: true,
                                        position: 'top-end'
                                    });
                                }
                            },
                            function(finalErr) {
                                if (btnGps) {
                                    btnGps.className = "btn btn-sm btn-warning";
                                    btnGps.style.background = "#f39c12";
                                    btnGps.innerHTML = '<ion-icon name="location-outline" style="font-size: 15px;"></ion-icon><span>Izinkan Lokasi GPS</span>';
                                    btnGps.disabled = false;
                                }
                                errorCallback(finalErr);
                            },
                            { enableHighAccuracy: false, timeout: 8000, maximumAge: 60000 }
                        );
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            }

            // Auto-trigger location permission request immediately
            if (navigator.geolocation) {
                requestLocationPermission(false);
            }

            $(document).on('click', '#btn-request-gps', function(e) {
                e.preventDefault();
                requestLocationPermission(true);
            });

            // Fungsi yang dijalankan ketika geolocation gagal
            function errorCallback(error) {
                console.error("Error getting geolocation:", error);
                
                const loader = document.getElementById('map-loading');
                if (loader) {
                    loader.innerHTML = '<div class="text-danger font-weight-bold" style="font-size:12px;">GPS belum aktif. <a href="javascript:void(0)" onclick="requestLocationPermission()" style="text-decoration:underline;">Klik untuk aktifkan</a></div>';
                }

                locationPermissionGranted = false;
                locationPermissionDenied = true;
                if (!locationPermissionAlertShown) {
                    locationPermissionAlertShown = true;
                    Swal.fire({
                        icon: 'info',
                        title: 'Izinkan Akses Lokasi (GPS)',
                        text: 'Akses GPS diperlukan untuk memverifikasi lokasi presensi. Mohon aktifkan GPS dan izinkan browser mengakses lokasi perangkat Anda.',
                        confirmButtonColor: themePrimary,
                        confirmButtonText: 'Izinkan / Coba Lagi'
                    }).then(() => {
                        requestLocationPermission();
                    });
                }

                // Coba inisialisasi peta dengan lokasi cabang default
                try {
                    const mapContainer = document.getElementById('map');
                    if (!mapContainer) return;

                    var lok = lokasi_cabang.split(",");
                    var lat_kantor = lok[0];
                    var long_kantor = lok[1];

                    map = L.map('map').setView([lat_kantor, long_kantor], 18);

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(map);

                    var radius = "{{ $lokasi_kantor->radius_cabang }}";
                    var circle = L.circle([lat_kantor, long_kantor], {
                        color: 'red',
                        fillColor: '#f03',
                        fillOpacity: 0.5,
                        radius: radius
                    }).addTo(map);
                    mapCircle = circle;

                    if (loader) loader.style.display = 'none';
                } catch (mapError) {
                    console.error("Error initializing fallback map:", mapError);
                }
            }

            // =========================================================
            // MODERN FACE RECOGNITION IMPLEMENTATION
            // =========================================================

            window.initialServerWajah = @json($user_wajah ?? []);
            const FaceConfig = {
                isEnabled: {{ $isFaceRecognitionRequired ? 1 : 0 }},
                modelsUrl: '/models',
                detection: {
                    // UNIVERSAL MOBILE OPTIMIZATION for all devices
                    interval: 200, 
                    intervalRecognized: 800,
                    inputSize: 224, 
                    scoreThreshold: 0.3, 
                    minConfidence: 0.45,
                    minStableFrames: 1, 
                    maxNoFaceFrames: 3,
                    mirror: false,
                    skipBrightnessNormalization: true
                },
                retry: {
                    maxAttempts: 10,
                    backoffFactor: 1.5
                },
                user: {
                    nik: "{{ $karyawan->nik }}",
                    name: "{{ getNamaDepan(strtolower($karyawan->nama_karyawan)) }}",
                    fullName: "{{ $karyawan->nama_karyawan }}",
                    wajahCount: parseInt("{{ $wajah }}") || 0
                }
            };

            /**
             * UI Controller Module
             */
            const UI = {
                els: {
                    container: document.getElementById('facedetection'),
                    video: null, // Will be set after camera init
                    canvas: null,
                    absenButtons: [document.getElementById('absenmasuk'), document.getElementById('absenpulang')]
                },
                
                showLoading(id, message) {
                    const el = document.getElementById('facedetection');
                    if(!el) return;

                    // Hapus SEMUA loading overlay yang ada agar tidak bertumpuk/double
                    el.querySelectorAll('.loading-overlay').forEach(item => item.remove());

                    const loader = document.createElement('div');
                    loader.id = id;
                    loader.className = 'loading-overlay';
                    loader.innerHTML = `
                        <div class="spinner-border text-light" role="status" style="width: 2.2rem; height: 2.2rem;"></div>
                        <div class="mt-2 text-light" style="font-weight: 500; text-shadow: 0 1px 3px rgba(0,0,0,0.8);">${message}</div>
                    `;
                    loader.style.position = 'absolute';
                    loader.style.top = '50%';
                    loader.style.left = '50%';
                    loader.style.transform = 'translate(-50%, -50%)';
                    loader.style.zIndex = '1000';
                    loader.style.textAlign = 'center';
                    loader.style.pointerEvents = 'none';
                    
                    el.appendChild(loader);
                },

                removeLoading(id) {
                    if (id) {
                        const el = document.getElementById(id);
                        if (el) el.remove();
                    }
                    const container = document.getElementById('facedetection');
                    if (container && !id) {
                        container.querySelectorAll('.loading-overlay').forEach(item => item.remove());
                    }
                },

                showError(message, isFatal = false) {
                    const el = document.getElementById('facedetection');
                    if(!el) return;

                    // Remove existing errors
                    const existing = el.querySelector('.alert-error-message');
                    if(existing) existing.remove();

                    if(message) {
                        const errorMsg = document.createElement('div');
                        errorMsg.className = 'alert-error-message';
                        errorMsg.innerHTML = `<div class="alert alert-danger">${message}</div>`;
                        errorMsg.style.position = 'absolute';
                        errorMsg.style.bottom = '10px';
                        errorMsg.style.width = '100%';
                        errorMsg.style.zIndex = '2000';
                        errorMsg.style.textAlign = 'center';
                        el.appendChild(errorMsg);
                    }
                    
                    if(isFatal) this.disableButtons();
                },

                disableButtons() {
                   if(this.els.absenButtons) {
                        this.els.absenButtons.forEach(btn => {
                            if(btn) btn.disabled = true;
                        });
                   }
                },

                enableButtons() {
                    if(this.els.absenButtons) {
                        this.els.absenButtons.forEach(btn => {
                            if(btn) btn.disabled = false;
                        });
                    }
                }
            };

            /**
             * Image Processing Helpers
             */
            const ImageProcessing = {
                /**
                 * Normalize brightness of an image element
                 * @param {HTMLImageElement|HTMLVideoElement|HTMLCanvasElement} img - Image/Video element to normalize
                 * @param {number} targetMean - Target mean brightness (default: 128)
                 * @returns {HTMLCanvasElement|HTMLImageElement} - Canvas with normalized image or original if invalid
                 */
                normalizeBrightness(img, targetMean = 128) {
                    try {
                        // Validate input dimensions
                        const width = img.width || img.videoWidth || 0;
                        const height = img.height || img.videoHeight || 0;
                        
                        if (width === 0 || height === 0) {
                            console.warn('Cannot normalize brightness: invalid dimensions', width, 'x', height);
                            return img; // Return original if dimensions invalid
                        }
                        
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        
                        canvas.width = width;
                        canvas.height = height;
                        
                        // Draw original image
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        // Get image data
                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const data = imageData.data;
                        
                        // Calculate current mean brightness
                        let sum = 0;
                        for (let i = 0; i < data.length; i += 4) {
                            sum += (data[i] + data[i + 1] + data[i + 2]) / 3;
                        }
                        const currentMean = sum / (data.length / 4);
                        
                        // Skip normalization if mean is already close to target
                        if (Math.abs(currentMean - targetMean) < 5) {
                            console.log(`Brightness already optimal: ${currentMean.toFixed(1)}`);
                            return canvas;
                        }
                        
                        // Calculate adjustment factor
                        const adjustment = targetMean / currentMean;
                        
                        // Apply brightness normalization
                        for (let i = 0; i < data.length; i += 4) {
                            data[i] = Math.min(255, data[i] * adjustment);     // R
                            data[i + 1] = Math.min(255, data[i + 1] * adjustment); // G
                            data[i + 2] = Math.min(255, data[i + 2] * adjustment); // B
                            // Alpha channel (i + 3) unchanged
                        }
                        
                        // Put normalized data back
                        ctx.putImageData(imageData, 0, 0);
                        
                        console.log(`Brightness normalized: ${currentMean.toFixed(1)} → ${targetMean}`);
                        
                        return canvas;
                    } catch (error) {
                        console.error('Error in brightness normalization:', error);
                        return img; // Return original on error
                    }
                }
            };

            /**
             * Camera Controller Module - Native WebRTC with progressive fallback
             */
            const Camera = {
                async init() {
                    const container = document.querySelector('.webcam-capture');
                    if (!container) {
                        console.log('No .webcam-capture element in DOM, skipping Camera.init');
                        return null;
                    }

                    // 1. Release lingering streams from previous navigation/session
                    if (typeof Webcam !== 'undefined' && Webcam.reset) {
                        try { Webcam.reset(); } catch (e) {}
                    }
                    if (container._stream) {
                        try { container._stream.getTracks().forEach(t => t.stop()); } catch (e) {}
                        container._stream = null;
                    }
                    container.innerHTML = '';

                    // 2. Insecure context check (browsers block getUserMedia over non-localhost HTTP)
                    if (window.isSecureContext === false && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                        throw new Error('Akses kamera diblokir browser: Memerlukan HTTPS atau localhost.');
                    }

                    // 3. Helper to attach native stream to video element
                    const attachNativeStream = (stream) => {
                        window.activeCameraStream = stream;
                        container._stream = stream;
                        let video = document.createElement('video');
                        video.setAttribute('autoplay', 'autoplay');
                        video.setAttribute('playsinline', 'playsinline');
                        video.setAttribute('muted', 'muted');
                        video.muted = true;
                        video.style.width = '100%';
                        video.style.height = '100%';
                        video.style.objectFit = 'cover';
                        container.appendChild(video);

                        video.srcObject = stream;
                        video.play().catch(() => {});
                        cameraPermissionGranted = true;
                        UI.els.video = video;

                        // Guarantee Webcam.snap compatibility for presensi capture
                        window.Webcam = window.Webcam || {};
                        window.Webcam.snap = function(cb) {
                            const canvas = document.createElement('canvas');
                            canvas.width = video.videoWidth || 640;
                            canvas.height = video.videoHeight || 480;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                            cb(canvas.toDataURL('image/jpeg', 0.95));
                        };

                        return new Promise((resolve) => {
                            if (video.readyState >= 2) {
                                resolve(video);
                            } else {
                                video.onloadedmetadata = () => resolve(video);
                                setTimeout(() => resolve(video), 1200);
                            }
                        });
                    };

                    // 4. Progressive getUserMedia (Front camera -> Any camera fallback)
                    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                        try {
                            const stream = await navigator.mediaDevices.getUserMedia({
                                video: {
                                    facingMode: 'user',
                                    width: { ideal: 640 },
                                    height: { ideal: 480 }
                                },
                                audio: false
                            }).catch(() => {
                                return navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                            });

                            return await attachNativeStream(stream);
                        } catch (err) {
                            console.error('Camera getUserMedia error:', err);
                            const name = (err?.name || '').toLowerCase();
                            if (name.includes('notallowed') || name.includes('permission')) {
                                cameraPermissionDenied = true;
                                throw new Error('Izin kamera ditolak. Silakan izinkan akses kamera di browser Anda.');
                            }
                            if (name.includes('notfound') || name.includes('devicesnotfound')) {
                                throw new Error('Kamera tidak ditemukan pada perangkat Anda.');
                            }
                            if (name.includes('notreadable') || name.includes('trackstart')) {
                                throw new Error('Kamera sedang digunakan aplikasi lain. Tutup aplikasi tersebut dan coba lagi.');
                            }
                            throw new Error('Gagal mengakses kamera: ' + (err.message || name));
                        }
                    }

                    throw new Error('Browser Anda tidak mendukung akses kamera.');
                }
            };

            /**
             * Face Service Module
             */
            const FaceService = {
                modelsLoaded: false,
                descriptors: null,
                matcher: null,
                isDetecting: false,
                
                async loadModels() {
                    UI.showLoading('model-loading', 'Memuat model wajah...');
                    
                    try {
                        const modelPath = FaceConfig.modelsUrl;
                        if (window.FaceModelCache && typeof window.FaceModelCache.loadModelWithCache === 'function') {
                            await Promise.all([
                                window.FaceModelCache.loadModelWithCache(faceapi.nets.tinyFaceDetector, modelPath),
                                window.FaceModelCache.loadModelWithCache(faceapi.nets.faceLandmark68Net, modelPath),
                                window.FaceModelCache.loadModelWithCache(faceapi.nets.faceRecognitionNet, modelPath)
                            ]);
                        } else {
                            await Promise.all([
                                faceapi.nets.tinyFaceDetector.loadFromUri(modelPath),
                                faceapi.nets.faceLandmark68Net.loadFromUri(modelPath),
                                faceapi.nets.faceRecognitionNet.loadFromUri(modelPath)
                            ]);
                        }
                        this.modelsLoaded = true;
                        UI.removeLoading('model-loading');
                        console.log('Models loaded');
                    } catch (e) {
                        console.error('Model load failed', e);
                        UI.removeLoading('model-loading');
                        throw e;
                    }
                },

                async loadDescriptors() {
                    UI.showLoading('data-loading', 'Memuat data wajah...');
                    
                    try {
                        const nik = FaceConfig.user.nik;
                        let descriptions = [];
                        const timestamp = new Date().getTime();
                        
                        // Gunakan server-inlined wajah jika tersedia (hemat 1 HTTP roundtrip)
                        let data = (window.initialServerWajah && Array.isArray(window.initialServerWajah) && window.initialServerWajah.length > 0)
                            ? window.initialServerWajah
                            : null;

                        // Fetch fallback jika data belum tersedia di inlined HTML
                        if (!data) {
                            try {
                                const response = await fetch(`/facerecognition/getwajah?t=${timestamp}`);
                                data = await response.json();
                            } catch (e) {
                                console.warn('Failed to fetch face list from server', e);
                            }
                        }

                        if (!data || data.length === 0) {
                            if (window.FaceModelCache && window.FaceModelCache.clearDescriptors) {
                                await window.FaceModelCache.clearDescriptors(nik);
                            }
                            UI.removeLoading('data-loading');
                            this.matcher = null;
                            return;
                        }

                        const serverWajahSig = data.map(d => d.id + '_' + d.wajah).join(',');
                        
                        // Cek Cache dengan signature server (bukan hanya jumlah file)
                        let cached = null;
                        try {
                            if (window.FaceModelCache && window.FaceModelCache.loadDescriptors) {
                                cached = await window.FaceModelCache.loadDescriptors(nik);
                            }
                        } catch(e) {
                            console.warn('Cache lookup failed', e);
                        }

                        // Cek apakah server sudah mengirim descriptor terhitung (0-download optimization)
                        const hasAllServerDescriptors = data.every(d => d.descriptor && Array.isArray(d.descriptor) && d.descriptor.length === 128);

                        if (hasAllServerDescriptors) {
                            console.log('[Presensi] Using instant precomputed descriptors from server (0 image download)');
                            descriptions = data.map(d => new Float32Array(d.descriptor));
                        } else if (cached && cached.wajahSig === serverWajahSig && cached.descriptors && cached.descriptors.length > 0) {
                            console.log('[Presensi] Using verified fast cached descriptors');
                            descriptions = cached.descriptors.map(d => d instanceof Float32Array ? d : new Float32Array(Object.values(d)));
                        } else {
                            console.log('[Presensi] Generating descriptors and auto-syncing to server...');
                            if (window.FaceModelCache && window.FaceModelCache.clearDescriptors) {
                                await window.FaceModelCache.clearDescriptors(nik);
                            }
                            
                            const label = `${FaceConfig.user.nik}-${FaceConfig.user.name}`;
                            const syncPayload = [];
                            const promises = data.map(async (faceData) => {
                                try {
                                    if (faceData.descriptor && Array.isArray(faceData.descriptor) && faceData.descriptor.length === 128) {
                                        return new Float32Array(faceData.descriptor);
                                    }
                                    const imgPath = `/files/facerecognition/${label}/${faceData.wajah}?t=${timestamp}`;
                                    const img = await faceapi.fetchImage(imgPath);
                                    const normalizedCanvas = ImageProcessing.normalizeBrightness(img, 128);
                                    const options = new faceapi.TinyFaceDetectorOptions({ inputSize: 224 });
                                    const detection = await faceapi.detectSingleFace(normalizedCanvas, options)
                                        .withFaceLandmarks()
                                        .withFaceDescriptor();
                                    if (detection && detection.descriptor) {
                                        syncPayload.push({ id: faceData.id, descriptor: Array.from(detection.descriptor) });
                                        return detection.descriptor;
                                    }
                                    return null;
                                } catch (err) {
                                    console.warn('Error processing face image', err);
                                    return null;
                                }
                            });

                            const results = await Promise.all(promises);
                            descriptions = results.filter(d => d !== null);

                            if (descriptions.length > 0 && window.FaceModelCache && window.FaceModelCache.saveDescriptors) {
                                await window.FaceModelCache.saveDescriptors(nik, descriptions, data.map(d=>d.wajah), serverWajahSig);
                                console.log('[Presensi] Fresh face descriptors generated and cached');
                            }

                            // Auto sync descriptors to server in background
                            if (syncPayload.length > 0) {
                                fetch('{{ route("facerecognition.syncDescriptors") }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({ descriptors: syncPayload })
                                }).catch(err => console.warn('Background descriptor sync failed', err));
                            }
                        }

                        if(descriptions.length > 0) {
                            const labelName = FaceConfig.user.fullName;
                            const labeledDescriptors = new faceapi.LabeledFaceDescriptors(labelName, descriptions);
                            // Threshold 0.42: Strict matching threshold (lower = stricter, preventing imposter matches)
                            this.matcher = new faceapi.FaceMatcher(labeledDescriptors, 0.42);
                        } else {
                            this.matcher = null; 
                        }
                        
                        UI.removeLoading('data-loading');
                        console.log('Descriptors loaded');
                    } catch (e) {
                        console.error('Descriptor load failed', e);
                        UI.showError('Gagal memuat data wajah.');
                    }
                },

                startDetection(video) {
                    if(this.isDetecting) return;
                    this.isDetecting = true;
                    
                    // === CRITICAL FIX: Match Canvas to DISPLAYED Video Size ===
                    // The video element is scaled by CSS (object-fit: cover)
                    // We need to match the canvas to the DISPLAYED size, not intrinsic size
                    const canvas = faceapi.createCanvasFromMedia(video);
                    UI.els.container.appendChild(canvas);
                    UI.els.canvas = canvas;
                    
                    // Get displayed dimensions (accounting for CSS scaling)
                    const rect = video.getBoundingClientRect();
                    const displaySize = { width: Math.round(rect.width), height: Math.round(rect.height) };
                    
                    // Set canvas to match EXACTLY the displayed video size
                    canvas.width = displaySize.width;
                    canvas.height = displaySize.height;
                    canvas.style.width = displaySize.width + 'px';
                    canvas.style.height = displaySize.height + 'px';
                    canvas.style.position = 'absolute';
                    canvas.style.top = '0';
                    canvas.style.left = '0';
                    canvas.style.objectFit = 'none'; // Critical: no scaling on canvas
                    
                    console.log('Video intrinsic:', video.videoWidth, 'x', video.videoHeight);
                    console.log('Video displayed:', displaySize.width, 'x', displaySize.height);

                    let stableCount = 0;
                    let noFaceCount = 0;
                    let isProcessing = false;
                    let lastRecognizedState = false;
                    
                    const loop = async () => {
                        if(!this.isDetecting) return;

                        if(!isProcessing) {
                            isProcessing = true;
                            try {
                                // === BRIGHTNESS NORMALIZATION (SKIP FOR PERFORMANCE/BATTERY) ===
                                const inputSource = video; // Bypass normalisasi
                                
                                // FORCE TINY FACE DETECTOR ON ALL DEVICES
                                const options = new faceapi.TinyFaceDetectorOptions({ 
                                    inputSize: FaceConfig.detection.inputSize, 
                                    scoreThreshold: FaceConfig.detection.scoreThreshold 
                                });

                                // Use inputSource (video or normalized canvas) for detection
                                let detection = await faceapi.detectSingleFace(inputSource, options)
                                    .withFaceLandmarks()
                                    .withFaceDescriptor();

                                const ctx = canvas.getContext('2d');
                                ctx.clearRect(0, 0, canvas.width, canvas.height);

                                if (detection) {
                                    if (detection.descriptor) {
                                        window.currentLiveDescriptor = Array.from(detection.descriptor);
                                    }
                                    noFaceCount = 0;
                                    stableCount++;

                                    if(stableCount >= FaceConfig.detection.minStableFrames) {
                                        // === FIX: Scale detection from VIDEO size to DISPLAY size ===
                                        // Critical for mobile: video intrinsic size != displayed size
                                        const videoSize = { width: video.videoWidth, height: video.videoHeight };
                                        const displaySize = { width: canvas.width, height: canvas.height };
                                        
                                        // Resize detection results to match displayed canvas
                                        const resizedDetections = faceapi.resizeResults(detection, displaySize);
                                        const detBox = resizedDetections.detection.box;
                                        
                                        // === CENTER POSITION VALIDATION ===
                                        const faceCenterX = detBox.x + detBox.width / 2;
                                        const faceCenterY = detBox.y + detBox.height / 2;
                                        const canvasCenterX = displaySize.width / 2;
                                        const canvasCenterY = displaySize.height / 2;

                                        // Allow maximum 30% offset from canvas center
                                        const maxOffX = displaySize.width * 0.30;
                                        const maxOffY = displaySize.height * 0.30;
                                        const isCentered = (Math.abs(faceCenterX - canvasCenterX) <= maxOffX) && (Math.abs(faceCenterY - canvasCenterY) <= maxOffY);

                                        let match = null;
                                        let isRecognized = false;
                                        let labelText = 'Wajah Tidak Dikenali';
                                        
                                        if(this.matcher) {
                                            // Use ORIGINAL descriptor (not resized) for matching
                                            match = this.matcher.findBestMatch(detection.descriptor);
                                            const matchesLabel = match.label !== 'unknown';
                                            
                                            if (!isCentered) {
                                                labelText = 'Posisikan Wajah di Tengah Lingkaran';
                                                isRecognized = false;
                                            } else if (matchesLabel) {
                                                isRecognized = true;
                                                labelText = `${match.label} (${match.distance.toFixed(2)})`;
                                            } else {
                                                isRecognized = false;
                                                labelText = `Wajah Tidak Dikenali (${match.distance.toFixed(2)})`;
                                            }
                                        }
                                        
                                        // Update Global State
                                        faceRecognitionDetected = isRecognized ? 1 : 0;
                                        lastRecognizedState = isRecognized;
                                        
                                        // === MODERN ROUNDED BOX DESIGN ===
                                        // Use RESIZED detection box for drawing
                                        let box = resizedDetections.detection.box;
                                        
                                        // === MAKE BOX SQUARE ===
                                        const size = Math.max(box.width, box.height);
                                        const squareBox = {
                                            x: box.x + (box.width - size) / 2,
                                            y: box.y + (box.height - size) / 2,
                                            width: size,
                                            height: size
                                        };
                                        box = squareBox;
                                        
                                        const color = isRecognized ? '#4CAF50' : '#FFC107';
                                        const ctx = canvas.getContext('2d');
                                        
                                        // Draw rounded rectangle with glow
                                        const cornerRadius = 16;
                                        const lineWidth = 4;
                                        
                                        // Shadow/Glow effect
                                        ctx.save();
                                        ctx.shadowColor = color;
                                        ctx.shadowBlur = 20;
                                        ctx.shadowOffsetX = 0;
                                        ctx.shadowOffsetY = 0;
                                        
                                        // Draw rounded box
                                        ctx.strokeStyle = color;
                                        ctx.lineWidth = lineWidth;
                                        ctx.lineJoin = 'round';
                                        ctx.lineCap = 'round';
                                        
                                        ctx.beginPath();
                                        ctx.moveTo(box.x + cornerRadius, box.y);
                                        ctx.lineTo(box.x + box.width - cornerRadius, box.y);
                                        ctx.quadraticCurveTo(box.x + box.width, box.y, box.x + box.width, box.y + cornerRadius);
                                        ctx.lineTo(box.x + box.width, box.y + box.height - cornerRadius);
                                        ctx.quadraticCurveTo(box.x + box.width, box.y + box.height, box.x + box.width - cornerRadius, box.y + box.height);
                                        ctx.lineTo(box.x + cornerRadius, box.y + box.height);
                                        ctx.quadraticCurveTo(box.x, box.y + box.height, box.x, box.y + box.height - cornerRadius);
                                        ctx.lineTo(box.x, box.y + cornerRadius);
                                        ctx.quadraticCurveTo(box.x, box.y, box.x + cornerRadius, box.y);
                                        ctx.closePath();
                                        ctx.stroke();
                                        ctx.restore();
                                        
                                        // === SCANNING ANIMATION ===
                                        // Animated scanning line that moves up and down
                                        const scanSpeed = 0.05; // Speed of animation
                                        const scanProgress = (Date.now() * scanSpeed) % (box.height * 2);
                                        const scanY = scanProgress < box.height 
                                            ? box.y + scanProgress 
                                            : box.y + (box.height * 2 - scanProgress);
                                        
                                        ctx.save();
                                        // Create gradient for scan line
                                        const scanGradient = ctx.createLinearGradient(box.x, scanY - 15, box.x, scanY + 15);
                                        scanGradient.addColorStop(0, 'rgba(255, 255, 255, 0)');
                                        scanGradient.addColorStop(0.5, `rgba(255, 255, 255, 0.6)`);
                                        scanGradient.addColorStop(1, 'rgba(255, 255, 255, 0)');
                                        
                                        ctx.strokeStyle = scanGradient;
                                        ctx.lineWidth = 3;
                                        ctx.shadowColor = '#FFFFFF';
                                        ctx.shadowBlur = 10;
                                        
                                        ctx.beginPath();
                                        ctx.moveTo(box.x + 10, scanY);
                                        ctx.lineTo(box.x + box.width - 10, scanY);
                                        ctx.stroke();
                                        ctx.restore();
                                        
                                        // Draw corner brackets for extra tech feel
                                        ctx.save();
                                        ctx.strokeStyle = color;
                                        ctx.lineWidth = 3;
                                        const bracketSize = 20;
                                        
                                        // Top-left
                                        ctx.beginPath();
                                        ctx.moveTo(box.x + cornerRadius, box.y);
                                        ctx.lineTo(box.x, box.y);
                                        ctx.lineTo(box.x, box.y + bracketSize);
                                        ctx.stroke();
                                        
                                        // Top-right
                                        ctx.beginPath();
                                        ctx.moveTo(box.x + box.width - cornerRadius, box.y);
                                        ctx.lineTo(box.x + box.width, box.y);
                                        ctx.lineTo(box.x + box.width, box.y + bracketSize);
                                        ctx.stroke();
                                        
                                        // Bottom-left
                                        ctx.beginPath();
                                        ctx.moveTo(box.x, box.y + box.height - bracketSize);
                                        ctx.lineTo(box.x, box.y + box.height);
                                        ctx.lineTo(box.x + cornerRadius, box.y + box.height);
                                        ctx.stroke();
                                        
                                        // Bottom-right
                                        ctx.beginPath();
                                        ctx.moveTo(box.x + box.width, box.y + box.height - bracketSize);
                                        ctx.lineTo(box.x + box.width, box.y + box.height);
                                        ctx.lineTo(box.x + box.width - cornerRadius, box.y + box.height);
                                        ctx.stroke();
                                        ctx.restore();
                                        
                                        // Draw modern label
                                        ctx.save();
                                        ctx.font = 'bold 14px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                                        ctx.textAlign = 'center';
                                        ctx.textBaseline = 'middle';
                                        
                                        const labelPadding = 8;
                                        const textMetrics = ctx.measureText(labelText);
                                        const labelWidth = textMetrics.width + labelPadding * 2;
                                        const labelHeight = 28;
                                        const labelX = box.x + box.width / 2 - labelWidth / 2;
                                        const labelY = box.y - labelHeight - 8;
                                        
                                        // Label background with gradient
                                        const gradient = ctx.createLinearGradient(labelX, labelY, labelX, labelY + labelHeight);
                                        gradient.addColorStop(0, color);
                                        gradient.addColorStop(1, color + 'CC'); // Add transparency
                                        
                                        ctx.fillStyle = gradient;
                                        ctx.shadowColor = 'rgba(0,0,0,0.3)';
                                        ctx.shadowBlur = 8;
                                        ctx.shadowOffsetY = 2;
                                        
                                        // Rounded label background
                                        const labelRadius = 6;
                                        ctx.beginPath();
                                        ctx.moveTo(labelX + labelRadius, labelY);
                                        ctx.lineTo(labelX + labelWidth - labelRadius, labelY);
                                        ctx.quadraticCurveTo(labelX + labelWidth, labelY, labelX + labelWidth, labelY + labelRadius);
                                        ctx.lineTo(labelX + labelWidth, labelY + labelHeight - labelRadius);
                                        ctx.quadraticCurveTo(labelX + labelWidth, labelY + labelHeight, labelX + labelWidth - labelRadius, labelY + labelHeight);
                                        ctx.lineTo(labelX + labelRadius, labelY + labelHeight);
                                        ctx.quadraticCurveTo(labelX, labelY + labelHeight, labelX, labelY + labelHeight - labelRadius);
                                        ctx.lineTo(labelX, labelY + labelRadius);
                                        ctx.quadraticCurveTo(labelX, labelY, labelX + labelRadius, labelY);
                                        ctx.closePath();
                                        ctx.fill();
                                        
                                        // Label text
                                        ctx.shadowBlur = 0;
                                        ctx.fillStyle = '#FFFFFF';
                                        ctx.fillText(labelText, labelX + labelWidth / 2, labelY + labelHeight / 2);
                                        ctx.restore();

                                        if (isRecognized) {
                                            UI.enableButtons();
                                        } else {
                                            UI.disableButtons();
                                        }
                                    }
                                } else {
                                    noFaceCount++;
                                    if(noFaceCount > FaceConfig.detection.maxNoFaceFrames) {
                                        stableCount = 0;
                                        faceRecognitionDetected = 0;
                                        lastRecognizedState = false;
                                        UI.disableButtons();
                                        
                                        // === DRAW NO FACE DETECTED ALERT ===
                                        const ctx = canvas.getContext('2d');
                                        
                                        // Alert box dimensions
                                        const alertWidth = 280;
                                        const alertHeight = 80;
                                        const alertX = (canvas.width - alertWidth) / 2;
                                        const alertY = (canvas.height - alertHeight) / 2;
                                        const alertRadius = 12;
                                        
                                        // Draw alert background with gradient
                                        ctx.save();
                                        const gradient = ctx.createLinearGradient(alertX, alertY, alertX, alertY + alertHeight);
                                        gradient.addColorStop(0, 'rgba(244, 67, 54, 0.95)'); // Red
                                        gradient.addColorStop(1, 'rgba(244, 67, 54, 0.85)');
                                        
                                        ctx.fillStyle = gradient;
                                        ctx.shadowColor = 'rgba(0, 0, 0, 0.4)';
                                        ctx.shadowBlur = 20;
                                        ctx.shadowOffsetY = 4;
                                        
                                        // Rounded rectangle
                                        ctx.beginPath();
                                        ctx.moveTo(alertX + alertRadius, alertY);
                                        ctx.lineTo(alertX + alertWidth - alertRadius, alertY);
                                        ctx.quadraticCurveTo(alertX + alertWidth, alertY, alertX + alertWidth, alertY + alertRadius);
                                        ctx.lineTo(alertX + alertWidth, alertY + alertHeight - alertRadius);
                                        ctx.quadraticCurveTo(alertX + alertWidth, alertY + alertHeight, alertX + alertWidth - alertRadius, alertY + alertHeight);
                                        ctx.lineTo(alertX + alertRadius, alertY + alertHeight);
                                        ctx.quadraticCurveTo(alertX, alertY + alertHeight, alertX, alertY + alertHeight - alertRadius);
                                        ctx.lineTo(alertX, alertY + alertRadius);
                                        ctx.quadraticCurveTo(alertX, alertY, alertX + alertRadius, alertY);
                                        ctx.closePath();
                                        ctx.fill();
                                        
                                        // Draw icon (!) 
                                        ctx.shadowBlur = 0;
                                        ctx.fillStyle = '#FFFFFF';
                                        ctx.font = 'bold 32px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                                        ctx.textAlign = 'center';
                                        ctx.textBaseline = 'middle';
                                        ctx.fillText('⚠️', alertX + alertWidth / 2, alertY + 28);
                                        
                                        // Draw text
                                        ctx.font = 'bold 16px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                                        ctx.fillText('Wajah Tidak Terdeteksi', alertX + alertWidth / 2, alertY + 58);
                                        
                                        ctx.restore();
                                    }
                                }
                            } catch (e) {
                                console.warn('Detection loop error', e);
                            }
                            isProcessing = false;
                        }

                        // Throttle with requestAnimationFrame (800ms when recognized to save CPU/battery, 200ms when searching)
                        const currentInterval = (lastRecognizedState && stableCount >= FaceConfig.detection.minStableFrames)
                            ? (FaceConfig.detection.intervalRecognized || 800)
                            : FaceConfig.detection.interval;
                        setTimeout(() => requestAnimationFrame(loop), currentInterval);
                    };

                    loop();
                }
            };

            /**
             * Main Application Orchestrator
             */
            const App = {
                async init() {
                    console.log('Initializing Modern App Logic...');

                    const revealUI = () => {
                        const skel = document.getElementById('skeleton-loader');
                        if (skel) skel.remove();
                        const real = document.getElementById('real-content');
                        if (real) {
                            real.classList.remove('content-hide');
                            real.style.display = 'block';
                        }
                    };

                    // Fast-path: jika sudah selesai bekerja atau tidak ada webcam, langsung tampilkan
                    if ((shiftConfig.sudahMasuk && shiftConfig.sudahPulang) || !document.querySelector('.webcam-capture')) {
                        revealUI();
                        return;
                    }

                    // Fallback timer: auto-reveal UI jika kamera memakan waktu izin
                    const fallbackTimer = setTimeout(() => {
                        console.warn('App.init fallback: auto-revealing UI');
                        revealUI();
                    }, 3500);

                    // 1. Pre-start Face Recognition models & descriptors in PARALLEL with camera immediately!
                    let faceInitPromise = null;
                    if (FaceConfig.isEnabled == 1) {
                        UI.disableButtons();
                        faceInitPromise = (async () => {
                            await FaceService.loadModels();
                            await FaceService.loadDescriptors();
                        })();
                    }

                    // 2. Start Map & Geolocation in parallel immediately
                    if (geoPositionPromise) {
                        console.log('[GPS] Using pre-fetched geolocation for map...');
                        geoPositionPromise.then(result => {
                            if (result.success) {
                                successCallback(result.position);
                            } else {
                                errorCallback(result.error);
                            }
                        });
                    } else if (navigator.geolocation) {
                        console.log('[GPS] Fallback: requesting geolocation now...');
                        navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
                    }

                    try {
                        const video = await Camera.init();
                        clearTimeout(fallbackTimer);
                        console.log('Camera initialized, revealing UI...');
                        
                        revealUI();

                        if (map) {
                            setTimeout(() => map.invalidateSize(), 300);
                        }

                        // 3. Start Face Detection once models and camera stream are both ready
                        if (FaceConfig.isEnabled == 1 && faceInitPromise) {
                            (async () => {
                                try {
                                    await Promise.race([
                                        faceInitPromise,
                                        new Promise((_, reject) => setTimeout(() => reject(new Error('Waktu muat model wajah habis (timeout)')), 30000))
                                    ]);
                                    if (video) {
                                        FaceService.startDetection(video);
                                    }
                                } catch (faceErr) {
                                    console.warn('Face Recognition Init Failed or Timed Out:', faceErr);
                                    UI.removeLoading();
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Biometrik Gagal Dimuat',
                                        text: 'Sistem biometrik wajah gagal dimuat. Periksa koneksi lalu coba muat ulang halaman. Jika tetap gagal, hubungi supervisor.',
                                        confirmButtonText: 'Muat Ulang Halaman',
                                        confirmButtonColor: themePrimary,
                                        showCancelButton: true,
                                        cancelButtonText: 'Tutup',
                                        allowOutsideClick: false
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            cleanupCameraResources();
                                            window.location.reload();
                                        }
                                    });
                                }
                            })();
                        } else {
                            console.log('Face Recognition is disabled, enabling buttons.');
                            UI.enableButtons();
                        }

                    } catch (e) {
                        clearTimeout(fallbackTimer);
                        console.error('App Init Error', e);
                        revealUI();
                        UI.showError(e?.message || 'Gagal memuat kamera. Silakan periksa izin browser atau refresh halaman.');
                    }
                }
            };

            // Start App
            App.init();

            // Release camera tracks & stop detection when leaving the page (Phase 9)
            const cleanupCameraResources = () => {
                FaceService.isDetecting = false;
                if (window.activeCameraStream) {
                    try {
                        window.activeCameraStream.getTracks().forEach(t => {
                            if (t && typeof t.stop === 'function') t.stop();
                        });
                    } catch (e) {}
                    window.activeCameraStream = null;
                }
                const container = document.querySelector('.webcam-capture');
                if (container && container._stream) {
                    try {
                        container._stream.getTracks().forEach(t => {
                            if (t && typeof t.stop === 'function') t.stop();
                        });
                    } catch (e) {}
                    container._stream = null;
                }
                const videoEl = document.querySelector('.webcam-capture video');
                if (videoEl && videoEl.srcObject) {
                    try {
                        videoEl.srcObject.getTracks().forEach(t => {
                            if (t && typeof t.stop === 'function') t.stop();
                        });
                    } catch (e) {}
                    videoEl.srcObject = null;
                }
            };
            window.addEventListener('pagehide', cleanupCameraResources);


            function showPermissionWarning(type) {
                const messages = {
                    camera: 'Akses kamera diperlukan untuk proses presensi. Mohon izinkan kamera terlebih dahulu.',
                    location: 'Akses lokasi diperlukan untuk proses presensi. Mohon aktifkan dan izinkan lokasi.'
                };
                Swal.fire({
                    icon: 'warning',
                    title: 'Izin Diperlukan',
                    text: messages[type] || 'Mohon lengkapi perizinan yang dibutuhkan.'
                });
            }


            // Helper function to convert dataURI to Blob
            function dataURItoBlob(dataURI) {
                var byteString = atob(dataURI.split(',')[1]);
                var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
                var ab = new ArrayBuffer(byteString.length);
                var ia = new Uint8Array(ab);
                for (var i = 0; i < byteString.length; i++) {
                    ia[i] = byteString.charCodeAt(i);
                }
                return new Blob([ab], {
                    type: mimeString
                });
            }

            // Flag pencegah double-submit presensi
            let isSubmittingPresensi = false;
            let currentAttendanceNonce = "{{ $attendance_nonce ?? '' }}";

            /**
             * Menambahkan watermark koordinat dan tanggal/waktu ke gambar langsung sebagai binary Blob
             * @param {string} imageDataURI - Data URI dari tangkapan kamera
             * @param {string} koordinat - String koordinat "lat,lng"
             * @returns {Promise<Blob>} - Blob JPEG dengan watermark (langsung multipart)
             */
            async function addWatermarkToBlob(imageDataURI, koordinat) {
                return new Promise((resolve) => {
                    try {
                        const img = new Image();
                        img.onload = function() {
                            const canvas = document.createElement('canvas');
                            const ctx = canvas.getContext('2d');

                            // Downscale to max 640px to reduce payload to ~40-60KB
                            let targetWidth = img.width;
                            let targetHeight = img.height;
                            const maxDim = 640;
                            if (targetWidth > maxDim || targetHeight > maxDim) {
                                if (targetWidth > targetHeight) {
                                    targetHeight = Math.round((targetHeight * maxDim) / targetWidth);
                                    targetWidth = maxDim;
                                } else {
                                    targetWidth = Math.round((targetWidth * maxDim) / targetHeight);
                                    targetHeight = maxDim;
                                }
                            }
                            canvas.width = targetWidth;
                            canvas.height = targetHeight;

                            // Gambar foto asli yang sudah di-resize
                            ctx.drawImage(img, 0, 0, targetWidth, targetHeight);

                            // Watermark Koordinat & Waktu (kiri bawah)
                            if (koordinat) {
                                const coords = koordinat.split(',');
                                const lat = coords[0] ? parseFloat(coords[0]).toFixed(6) : '-';
                                const lng = coords[1] ? parseFloat(coords[1]).toFixed(6) : '-';
                                const now = new Date();
                                const dateStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
                                const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

                                const fontSize = Math.max(12, Math.floor(canvas.width / 40));
                                const padding = 8;
                                const lineHeight = fontSize + 4;
                                const textLines = [
                                    `${lat}, ${lng}`,
                                    `${dateStr}  ${timeStr}`
                                ];

                                ctx.font = `bold ${fontSize}px Arial, sans-serif`;
                                let maxTextWidth = 0;
                                textLines.forEach(line => {
                                    const w = ctx.measureText(line).width;
                                    if (w > maxTextWidth) maxTextWidth = w;
                                });

                                const boxWidth = maxTextWidth + padding * 2;
                                const boxHeight = textLines.length * lineHeight + padding * 2;
                                const boxX = 8;
                                const boxY = canvas.height - boxHeight - 8;

                                // Background box semi-transparan
                                ctx.fillStyle = 'rgba(0, 0, 0, 0.6)';
                                ctx.beginPath();
                                const r = 8;
                                ctx.moveTo(boxX + r, boxY);
                                ctx.lineTo(boxX + boxWidth - r, boxY);
                                ctx.quadraticCurveTo(boxX + boxWidth, boxY, boxX + boxWidth, boxY + r);
                                ctx.lineTo(boxX + boxWidth, boxY + boxHeight - r);
                                ctx.quadraticCurveTo(boxX + boxWidth, boxY + boxHeight, boxX + boxWidth - r, boxY + boxHeight);
                                ctx.lineTo(boxX + r, boxY + boxHeight);
                                ctx.quadraticCurveTo(boxX, boxY + boxHeight, boxX, boxY + boxHeight - r);
                                ctx.lineTo(boxX, boxY + r);
                                ctx.quadraticCurveTo(boxX, boxY, boxX + r, boxY);
                                ctx.closePath();
                                ctx.fill();

                                // Teks koordinat & timestamp
                                ctx.fillStyle = '#FFFFFF';
                                ctx.font = `bold ${fontSize}px Arial, sans-serif`;
                                ctx.textBaseline = 'top';
                                textLines.forEach((line, i) => {
                                    ctx.fillText(line, boxX + padding, boxY + padding + (i * lineHeight));
                                });
                            }

                            // Native canvas.toBlob untuk efisiensi RAM/CPU maksimal
                            if (canvas.toBlob) {
                                canvas.toBlob(function(blob) {
                                    resolve(blob || dataURItoBlob(canvas.toDataURL('image/jpeg', 0.75)));
                                }, 'image/jpeg', 0.75);
                            } else {
                                resolve(dataURItoBlob(canvas.toDataURL('image/jpeg', 0.75)));
                            }
                        };
                        img.onerror = function() {
                            resolve(dataURItoBlob(imageDataURI));
                        };
                        img.src = imageDataURI;
                    } catch (error) {
                        console.error('Error menambahkan watermark:', error);
                        resolve(dataURItoBlob(imageDataURI));
                    }
                });
            }

            function resetButtonPresensi(btnId, iconName, labelText) {
                isSubmittingPresensi = false;
                $(btnId).prop('disabled', false).html(
                    `<ion-icon name="${iconName}" style="font-size: 20px"></ion-icon><span>${labelText}</span>`
                );
                $("#absenmasuk").prop('disabled', false);
                $("#absenpulang").prop('disabled', false);
                updateShiftStatus();
                // Resume deteksi wajah jika video masih aktif
                const videoEl = document.querySelector('.webcam-capture video');
                if (videoEl && !FaceService.isDetecting && FaceConfig.isEnabled == 1) {
                    FaceService.startDetection(videoEl);
                }
            }

            $("#absenmasuk").click(function() {
                if (isSubmittingPresensi) {
                    return false;
                }

                if (cameraPermissionDenied) {
                    showPermissionWarning('camera');
                    return;
                }
                if (locationPermissionDenied) {
                    showPermissionWarning('location');
                    return;
                }

                if (!lokasi || lokasi.includes('undefined') || lokasi === '') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Izinkan Akses Lokasi (GPS)',
                        text: 'Sistem sedang menunggu sinyal GPS lokasi Anda. Mohon klik "Aktifkan GPS" dan pastikan izin lokasi diizinkan pada browser.',
                        showCancelButton: true,
                        confirmButtonColor: themePrimary,
                        confirmButtonText: 'Aktifkan GPS',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            requestLocationPermission();
                        }
                    });
                    return false;
                }

                // === ANTI-FAKE GPS CHECK ===
                if (window.AntiFakeGPS) {
                    const mockCheck = AntiFakeGPS.analyze(lastRawPosition);
                    if (mockCheck.isMock) {
                        speakVoice("Terdeteksi menggunakan fake GPS. Silakan matikan fake GPS dan gunakan GPS asli.");
                        Swal.fire({
                            icon: 'error',
                            title: 'Fake GPS Terdeteksi',
                            html: '<p style="font-size:14px; margin-bottom:10px; color:#333;">Sistem mendeteksi indikasi aplikasi Fake GPS / Mock Location:</p>' +
                                  '<ul style="text-align:left; font-size:12px; color:#d32f2f; margin-bottom:12px; padding-left:20px;">' +
                                  mockCheck.reasons.map(r => `<li>${r}</li>`).join('') +
                                  '</ul>' +
                                  '<p style="font-size:12px; font-weight:600; color:#555;">Silakan matikan aplikasi Fake GPS dan gunakan sinyal GPS asli HP Anda.</p>',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'Tutup'
                        });
                        return false;
                    }
                }

                // === PENGECEKAN JADWAL MASUK SEBELUM FOTO ===
                if (shiftConfig.sudahMasuk) {
                    speakVoice("Anda sudah melakukan presensi masuk hari ini.");
                    Swal.fire({
                        icon: 'info',
                        title: 'Sudah Absen Masuk',
                        text: 'Anda sudah melakukan presensi masuk hari ini.',
                        confirmButtonColor: themePrimary,
                        confirmButtonText: 'Tutup'
                    });
                    return false;
                }

                if (shiftConfig.batasi) {
                    const nowCheck = new Date();
                    const jm = parseTimeToday(shiftConfig.jamMasuk);
                    const bMasuk = new Date(jm.getTime() - shiftConfig.batasMasukMenit * 1000);
                    const aMasuk = new Date(jm.getTime() + shiftConfig.batasMasukMenit * 1000);
                    if (nowCheck < bMasuk) {
                        speakVoice("Maaf, belum waktunya untuk presensi masuk.");
                        Swal.fire({
                            icon: 'warning',
                            title: 'Belum Waktunya Masuk',
                            text: 'Maaf, waktu absen masuk belum dibuka.',
                            confirmButtonColor: themePrimary,
                            confirmButtonText: 'Mengerti'
                        });
                        return false;
                    }
                    if (nowCheck > aMasuk) {
                        speakVoice("Maaf, waktu absen masuk sudah ditutup.");
                        Swal.fire({
                            icon: 'error',
                            title: 'Waktu Absen Habis',
                            text: 'Maaf, waktu absen masuk sudah ditutup.',
                            confirmButtonColor: '#DC2626',
                            confirmButtonText: 'Tutup'
                        });
                        return false;
                    }
                }

                // Kunci submit untuk mencegah double click & hentikan loop kamera untuk hemat CPU
                isSubmittingPresensi = true;
                FaceService.isDetecting = false;
                $("#absenmasuk").prop('disabled', true);
                $("#absenpulang").prop('disabled', true);
                $("#absenmasuk").html(
                    '<div class="spinner-border text-light mr-2" role="status"><span class="sr-only">Loading...</span></div> <span style="font-size:14px">Memproses...</span>'
                );
                let status = '1';
                Webcam.snap(function(uri) {
                    image = uri;
                });

                if (faceRecognitionDetected == 0 && faceRecognition == 1) {
                    isSubmittingPresensi = false;
                    swal.fire({
                        icon: 'error',
                        title: 'Wajah Tidak Terdeteksi',
                        text: 'Pastikan wajah Anda terlihat jelas di depan kamera.',
                        didClose: function() {
                            resetButtonPresensi('#absenmasuk', 'finger-print-outline', 'Masuk');
                            resetButtonPresensi('#absenpulang', 'log-out-outline', 'Pulang');
                        }
                    });
                    return false;
                } else {
                    addWatermarkToBlob(image, lokasi).then(function(blob) {
                        var formData = new FormData();
                        formData.append('image', blob, 'image.jpg');
                        formData.append('_token', "{{ csrf_token() }}");
                        formData.append('status', status);
                        formData.append('lokasi', lokasi);
                        formData.append('lokasi_cabang', lokasi_cabang);
                        formData.append('kode_jam_kerja', "{{ $jam_kerja->kode_jam_kerja }}");
                        formData.append('is_mock', (window.AntiFakeGPS && AntiFakeGPS.analyze(lastRawPosition).isMock) ? '1' : '0');
                        formData.append('mock_score', (window.AntiFakeGPS ? AntiFakeGPS.analyze(lastRawPosition).score : 0));
                        if (window.currentLiveDescriptor && Array.isArray(window.currentLiveDescriptor)) {
                            formData.append('face_descriptor', JSON.stringify(window.currentLiveDescriptor));
                        }
                        formData.append('attendance_nonce', currentAttendanceNonce);

                        $.ajax({
                            type: 'POST',
                            url: "{{ route('presensi.store') }}",
                            data: formData,
                            processData: false,
                            contentType: false,
                            cache: false,
                            success: function(data) {
                                if (data && data.new_nonce) {
                                    currentAttendanceNonce = data.new_nonce;
                                }
                                if (data.status == true) {
                                    if (data.suara) speakVoice(data.suara);
                                    shiftConfig.sudahMasuk = true;
                                    swal.fire({
                                        icon: data.is_terlambat ? 'warning' : 'success',
                                        title: data.is_terlambat ? 'Terlambat' : 'Tepat Waktu',
                                        text: data.message,
                                        showConfirmButton: true,
                                        confirmButtonText: 'Kembali ke Dashboard',
                                        confirmButtonColor: themePrimary,
                                        allowOutsideClick: false
                                    }).then(function(result) {
                                        if (result.isConfirmed) {
                                            window.location.href = '/dashboard';
                                        }
                                    });
                                }
                            },
                            error: function(xhr) {
                                const resp = xhr.responseJSON || {};
                                if (resp.new_nonce) {
                                    currentAttendanceNonce = resp.new_nonce;
                                }
                                if (resp.suara) speakVoice(resp.suara);
                                swal.fire({
                                    icon: 'error',
                                    title: (resp.notifikasi == "notifikasi_fakegps" || (resp.message && resp.message.toLowerCase().includes('fake gps'))) ? 'Fake GPS Terdeteksi' : 'Gagal Absen',
                                    text: resp.message || 'Terjadi kesalahan sistem.',
                                    didClose: function() {
                                        resetButtonPresensi('#absenmasuk', 'finger-print-outline', 'Masuk');
                                        resetButtonPresensi('#absenpulang', 'log-out-outline', 'Pulang');
                                    }
                                });
                            },
                            complete: function() {
                                isSubmittingPresensi = false;
                            }
                        });
                    });
                }
            });

            $("#absenpulang").click(async function() {
                if (isSubmittingPresensi) {
                    return false;
                }

                if (cameraPermissionDenied) {
                    showPermissionWarning('camera');
                    return;
                }
                if (locationPermissionDenied) {
                    showPermissionWarning('location');
                    return;
                }

                if (!lokasi || lokasi.includes('undefined') || lokasi === '') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Izinkan Akses Lokasi (GPS)',
                        text: 'Sistem sedang menunggu sinyal GPS lokasi Anda. Mohon klik "Aktifkan GPS" dan pastikan izin lokasi diizinkan pada browser.',
                        showCancelButton: true,
                        confirmButtonColor: themePrimary,
                        confirmButtonText: 'Aktifkan GPS',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            requestLocationPermission();
                        }
                    });
                    return false;
                }

                // === ANTI-FAKE GPS CHECK ===
                if (window.AntiFakeGPS) {
                    const mockCheck = AntiFakeGPS.analyze(lastRawPosition);
                    if (mockCheck.isMock) {
                        speakVoice("Terdeteksi menggunakan fake GPS. Silakan matikan fake GPS dan gunakan GPS asli.");
                        Swal.fire({
                            icon: 'error',
                            title: 'Fake GPS Terdeteksi',
                            html: '<p style="font-size:14px; margin-bottom:10px; color:#333;">Sistem mendeteksi indikasi aplikasi Fake GPS / Mock Location:</p>' +
                                  '<ul style="text-align:left; font-size:12px; color:#d32f2f; margin-bottom:12px; padding-left:20px;">' +
                                  mockCheck.reasons.map(r => `<li>${r}</li>`).join('') +
                                  '</ul>' +
                                  '<p style="font-size:12px; font-weight:600; color:#555;">Silakan matikan aplikasi Fake GPS dan gunakan sinyal GPS asli HP Anda.</p>',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'Tutup'
                        });
                        return false;
                    }
                }

                // === STRICT: CEK APAKAH SUDAH ABSEN MASUK ===
                if (!shiftConfig.sudahMasuk) {
                    speakVoice("Anda belum melakukan presensi masuk hari ini.");
                    Swal.fire({
                        icon: 'warning',
                        title: 'Belum Absen Masuk',
                        text: 'Anda harus melakukan presensi masuk terlebih dahulu sebelum presensi pulang.',
                        confirmButtonColor: themePrimary,
                        confirmButtonText: 'Mengerti'
                    });
                    return false;
                }

                // === STRICT: CEK APAKAH SUDAH SELESAI PULANG ===
                if (shiftConfig.sudahPulang) {
                    speakVoice("Anda telah selesai bekerja hari ini.");
                    Swal.fire({
                        icon: 'info',
                        title: 'Presensi Selesai',
                        text: 'Anda telah selesai bekerja hari ini.',
                        confirmButtonColor: themePrimary,
                        confirmButtonText: 'Tutup'
                    });
                    return false;
                }

                // === PHASE 8: EARLY CLOCK-OUT HANDLING ===
                const now = new Date();
                const jamMasuk = parseTimeToday(shiftConfig.jamMasuk);
                const jamPulang = parseTimeToday(shiftConfig.jamPulang);
                const batasMulaiPulang = new Date(jamPulang.getTime() - shiftConfig.batasPulangMenit * 1000);
                if (shiftConfig.lintasHari && jamPulang <= jamMasuk) {
                    jamPulang.setDate(jamPulang.getDate() + 1);
                    batasMulaiPulang.setDate(batasMulaiPulang.getDate() + 1);
                }
                const earliestPulang = (shiftConfig.batasi && shiftConfig.batasPulangMenit > 0) ? batasMulaiPulang : jamPulang;

                let earlyOutReason = '';
                if (now < earliestPulang) {
                    const confirmEarly = await Swal.fire({
                        title: 'Pulang Lebih Awal?',
                        text: 'Jam pulang shift Anda adalah pukul ' + shiftConfig.jamPulang + '. Anda akan tercatat pulang lebih awal.',
                        icon: 'warning',
                        input: 'text',
                        inputPlaceholder: 'Tuliskan alasan singkat pulang awal...',
                        showCancelButton: true,
                        confirmButtonColor: themePrimary,
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Pulang Sekarang',
                        cancelButtonText: 'Batal',
                        inputValidator: (value) => {
                            if (!value || !value.trim()) {
                                return 'Alasan pulang lebih awal wajib diisi!';
                            }
                        }
                    });
                    if (!confirmEarly.isConfirmed) {
                        return false;
                    }
                    earlyOutReason = confirmEarly.value ? confirmEarly.value.trim() : '';
                }

                // Kunci submit untuk mencegah double click & hentikan loop kamera untuk hemat CPU
                isSubmittingPresensi = true;
                FaceService.isDetecting = false;
                $("#absenmasuk").prop('disabled', true);
                $("#absenpulang").prop('disabled', true);
                $("#absenpulang").html(
                    '<div class="spinner-border text-light mr-2" role="status"><span class="sr-only">Loading...</span></div> <span style="font-size:14px">Memproses...</span>'
                );
                let status = '2';
                Webcam.snap(function(uri) {
                    image = uri;
                });
                if (faceRecognitionDetected == 0 && faceRecognition == 1) {
                    isSubmittingPresensi = false;
                    swal.fire({
                        icon: 'error',
                        title: 'Wajah Tidak Terdeteksi',
                        text: 'Pastikan wajah Anda terlihat jelas di depan kamera.',
                        didClose: function() {
                            resetButtonPresensi('#absenmasuk', 'finger-print-outline', 'Masuk');
                            resetButtonPresensi('#absenpulang', 'log-out-outline', 'Pulang');
                        }
                    });
                    return false;
                } else {
                    addWatermarkToBlob(image, lokasi).then(function(blob) {
                        var formData = new FormData();
                        formData.append('image', blob, 'image.jpg');
                        formData.append('_token', "{{ csrf_token() }}");
                        formData.append('status', status);
                        formData.append('lokasi', lokasi);
                        formData.append('lokasi_cabang', lokasi_cabang);
                        formData.append('kode_jam_kerja', "{{ $jam_kerja->kode_jam_kerja }}");
                        formData.append('early_out_reason', earlyOutReason);
                        formData.append('is_mock', (window.AntiFakeGPS && AntiFakeGPS.analyze(lastRawPosition).isMock) ? '1' : '0');
                        formData.append('mock_score', (window.AntiFakeGPS ? AntiFakeGPS.analyze(lastRawPosition).score : 0));
                        if (window.currentLiveDescriptor && Array.isArray(window.currentLiveDescriptor)) {
                            formData.append('face_descriptor', JSON.stringify(window.currentLiveDescriptor));
                        }
                        formData.append('attendance_nonce', currentAttendanceNonce);

                        $.ajax({
                            type: 'POST',
                            url: "{{ route('presensi.store') }}",
                            data: formData,
                            processData: false,
                            contentType: false,
                            cache: false,
                            success: function(data) {
                                if (data && data.new_nonce) {
                                    currentAttendanceNonce = data.new_nonce;
                                }
                                if (data.status == true) {
                                    if (data.suara) speakVoice(data.suara);
                                    
                                    shiftConfig.sudahPulang = true;

                                    const nowSuccess = new Date();
                                    const h = String(nowSuccess.getHours()).padStart(2, '0');
                                    const m = String(nowSuccess.getMinutes()).padStart(2, '0');
                                    const elRekapOut = document.getElementById('rekap-jam-out');
                                    if (elRekapOut) elRekapOut.textContent = h + ':' + m;

                                    const activeWrapper = document.getElementById('active-presensi-wrapper');
                                    const dynamicCard = document.getElementById('dynamic-selesai-card');
                                    if (activeWrapper && dynamicCard) {
                                        activeWrapper.style.display = 'none';
                                        dynamicCard.style.display = 'block';
                                    }

                                    swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil Pulang',
                                        text: data.message,
                                        showConfirmButton: true,
                                        confirmButtonText: 'Kembali ke Dashboard',
                                        confirmButtonColor: themePrimary,
                                        allowOutsideClick: false
                                    }).then(function(result) {
                                        if (result.isConfirmed) {
                                            window.location.href = '/dashboard';
                                        }
                                    });
                                }
                            },
                            error: function(xhr) {
                                const resp = xhr.responseJSON || {};
                                if (resp.new_nonce) {
                                    currentAttendanceNonce = resp.new_nonce;
                                }
                                if (resp.suara) speakVoice(resp.suara);
                                swal.fire({
                                    icon: 'error',
                                    title: (resp.notifikasi == "notifikasi_fakegps" || (resp.message && resp.message.toLowerCase().includes('fake gps'))) ? 'Fake GPS Terdeteksi' : 'Gagal Absen',
                                    text: resp.message || 'Terjadi kesalahan sistem.',
                                    didClose: function() {
                                        resetButtonPresensi('#absenmasuk', 'finger-print-outline', 'Masuk');
                                        resetButtonPresensi('#absenpulang', 'log-out-outline', 'Pulang');
                                    }
                                });
                            },
                            complete: function() {
                                isSubmittingPresensi = false;
                            }
                        });
                    });
                }
            });

            $("#cabang").change(function() {
                // Ambil nilai lokasi cabang yang dipilih
                lokasi_cabang = $(this).val();
                console.log("Lokasi cabang berubah: " + lokasi_cabang);

                // Ambil teks cabang yang dipilih
                let cabangText = $("#cabang option:selected").text();

                // Tampilkan notifikasi cabang berubah
                swal.fire({
                    icon: 'info',
                    title: 'Lokasi Berubah',
                    text: 'Lokasi cabang berubah menjadi: ' + cabangText,
                    showConfirmButton: false,
                    timer: 2000
                });

                try {
                    // Buat array dari string lokasi
                    var lok = lokasi_cabang.split(",");
                    var lat_kantor = lok[0];
                    var long_kantor = lok[1];

                    // Jika map dan circle sudah ada, cukup update posisi circlenya
                    if (map && mapCircle) {
                        mapCircle.setLatLng([lat_kantor, long_kantor]);
                        
                        // Sesuaikan view map agar marker user dan lokasi kantor terlihat
                        if (mapMarker) {
                            var group = new L.featureGroup([mapMarker, mapCircle]);
                            map.fitBounds(group.getBounds(), { padding: [30, 30] });
                        } else {
                            map.setView([lat_kantor, long_kantor], 18);
                        }
                    }
                } catch (error) {
                    console.error("Error updating map circle:", error);
                }
            });
        });
    </script>
@endpush