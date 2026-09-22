@extends('layouts.mobile.modern')

@section('title', 'Data Biometrik Wajah')

@section('header_left')
    <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('dashboard.index') }}"
        onclick="if (window.history.length > 1 && document.referrer && document.referrer.indexOf(window.location.host) !== -1) { event.preventDefault(); window.history.back(); }"
        class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all"
        title="Kembali">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        .bio-container {
            padding: 8px 4px 32px 4px;
        }

        .bio-card {
            background: #ffffff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 18px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
            padding: 16px;
        }

        .bio-profile-header {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .bio-avatar {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            color: #1E4D3E;
            font-weight: 700;
            font-size: 15px;
        }

        .bio-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .bio-name {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
            margin: 0;
        }

        .bio-meta-row {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 5px;
            flex-wrap: wrap;
        }

        .bio-chip-nik {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 2px 7px;
        }

        .bio-chip-status {
            font-size: 11px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 6px;
        }

        .bio-chip-active {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .bio-chip-inactive {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .bio-chip-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        /* Clean Executive Security Footnote */
        .bio-notice {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 12px 14px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 14px;
        }

        .bio-notice-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: rgba(30, 77, 62, 0.08);
            color: #1E4D3E;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .bio-notice-title {
            font-size: 12.5px;
            font-weight: 600;
            color: #1e293b;
            line-height: 1.3;
        }

        .bio-notice-desc {
            font-size: 11.5px;
            color: #64748b;
            margin-top: 2px;
            line-height: 1.45;
        }

        /* Photo Grid */
        .bio-section-title {
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin: 18px 2px 10px 2px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .bio-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .bio-grid-card {
            background: #ffffff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
            transition: all 0.2s ease;
        }

        .bio-grid-card:active {
            transform: scale(0.98);
        }

        .bio-img-wrapper {
            position: relative;
            width: 100%;
            aspect-ratio: 1 / 1;
            background: #f1f5f9;
            overflow: hidden;
        }

        .bio-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .bio-img-badge {
            position: absolute;
            top: 7px;
            left: 7px;
            background: rgba(15, 23, 42, 0.72);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            padding: 2px 6px;
            border-radius: 6px;
            letter-spacing: 0.02em;
        }

        .bio-img-meta {
            padding: 8px 10px;
            background: #ffffff;
            border-top: 1px solid #f1f5f9;
        }

        .bio-img-meta-row {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 10.5px;
            color: #64748b;
            font-weight: 500;
        }

        .bio-empty-box {
            text-align: center;
            padding: 36px 20px;
            background: #ffffff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 18px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
            margin-top: 10px;
        }

        .bio-empty-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: #f1f5f9;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin: 0 auto 12px auto;
        }

        .bio-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            width: 100%;
            height: 44px;
            background: #1E4D3E !important;
            color: #ffffff !important;
            font-size: 13px;
            font-weight: 600;
            border-radius: 12px;
            text-decoration: none;
            margin-top: 16px;
            transition: all 0.15s ease;
        }

        .bio-btn-primary:active {
            transform: scale(0.98);
            background: #16382E !important;
        }
    </style>
@endpush

@section('content')
<div class="bio-container fade-up">
    {{-- Header Profile Card --}}
    <div class="bio-card">
        <div class="bio-profile-header">
            <div class="bio-avatar">
                @if(!empty($karyawan->foto))
                    <img src="{{ getfotoKaryawan($karyawan->foto) }}" alt="{{ $karyawan->nama_karyawan ?? 'Karyawan' }}">
                @else
                    {{ strtoupper(substr($karyawan->nama_karyawan ?? 'K', 0, 2)) }}
                @endif
            </div>
            <div style="flex: 1; min-width: 0;">
                <h3 class="bio-name">{{ $karyawan->nama_karyawan ?? 'Karyawan' }}</h3>
                <div class="bio-meta-row">
                    <span class="bio-chip-nik">NIK: {{ $nik }}</span>
                    @if($wajahList->count() > 0)
                        <span class="bio-chip-status bio-chip-active">
                            <span class="bio-chip-dot"></span>
                            Terdaftar
                        </span>
                    @else
                        <span class="bio-chip-status bio-chip-inactive">
                            <span class="bio-chip-dot"></span>
                            Belum Ada Sampel
                        </span>
                    @endif
                </div>
            </div>
        </div>

        @if($wajahList->count() > 0)
            {{-- Clean Executive Security Footnote --}}
            <div class="bio-notice">
                <div class="bio-notice-icon">
                    <ion-icon name="shield-checkmark-outline"></ion-icon>
                </div>
                <div>
                    <div class="bio-notice-title">Dataset Biometrik Aktif</div>
                    <div class="bio-notice-desc">
                        Data biometrik aktif digunakan untuk verifikasi presensi. Anda dapat merekam ulang wajah jika pencahayaan atau sampel wajah perlu diperbarui.
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if($wajahList->count() > 0)
        {{-- Section Title --}}
        <div class="bio-section-title">
            <span>Sampel Wajah Terdaftar</span>
            <span style="font-family: ui-monospace, monospace; font-size: 11px;">{{ $wajahList->count() }} Sampel</span>
        </div>

        {{-- Photo Grid --}}
        <div class="bio-grid">
            @foreach($wajahList as $index => $wajah)
                <div class="bio-grid-card">
                    <div class="bio-img-wrapper">
                        @if($wajah->file_exists && $wajah->image_url)
                            <img src="{{ $wajah->image_url }}" 
                                 alt="Sampel {{ $index + 1 }}" 
                                 loading="lazy"
                                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect fill=\'%23f1f5f9\' width=\'200\' height=\'200\'/%3E%3Ctext fill=\'%2394a3b8\' font-family=\'sans-serif\' font-size=\'11\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3ETidak ditemukan%3C/text%3E%3C/svg%3E';">
                        @else
                            <div style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8;">
                                <ion-icon name="image-outline" style="font-size: 28px; margin-bottom: 4px;"></ion-icon>
                                <span style="font-size: 11px; font-weight: 500;">Tidak ditemukan</span>
                            </div>
                        @endif
                        <span class="bio-img-badge">#{{ $index + 1 }}</span>
                    </div>
                    <div class="bio-img-meta">
                        <div class="bio-img-meta-row">
                            <ion-icon name="time-outline" style="font-size: 13px; color: #94a3b8; flex-shrink: 0;"></ion-icon>
                            <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ \Carbon\Carbon::parse($wajah->created_at)->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Action Area --}}
        <div style="margin-top: 18px;">
            <a href="{{ route('facerecognition.karyawan.create', ['re' => 1]) }}" class="bio-btn-primary" style="margin-top: 0;">
                <ion-icon name="camera-outline" style="font-size: 17px;"></ion-icon>
                Rekam Ulang Wajah
            </a>
        </div>
    @else
        {{-- Empty State --}}
        <div class="bio-empty-box">
            <div class="bio-empty-icon">
                <ion-icon name="scan-outline"></ion-icon>
            </div>
            <h4 style="font-size: 14px; font-weight: 700; color: #0f172a; margin: 0 0 4px 0;">Belum Ada Sampel Wajah</h4>
            <p style="font-size: 12px; color: #64748b; margin: 0; line-height: 1.45;">
                Perekaman sampel biometrik diperlukan agar sistem dapat memverifikasi kehadiran Anda secara otomatis.
            </p>
            <a href="{{ route('facerecognition.karyawan.create') }}" class="bio-btn-primary">
                <ion-icon name="camera-outline" style="font-size: 16px;"></ion-icon>
                Mulai Perekaman Wajah
            </a>
        </div>
    @endif
</div>
@endsection
