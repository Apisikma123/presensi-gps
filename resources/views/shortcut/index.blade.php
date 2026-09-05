@extends('layouts.mobile.modern')
@section('title', 'Semua Menu')

@section('header_left')
    <a href="{{ route('dashboard.index') }}" class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/15 text-white active:scale-90 transition-transform">
        <ion-icon name="chevron-back-outline" class="text-base"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        .menu-row {
            transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1);
            text-decoration: none !important;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
        }
        .menu-row:active {
            background-color: #f1f5f9;
            transform: scale(0.99);
        }
        .menu-group {
            background: #ffffff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
            overflow: hidden;
            margin-bottom: 16px;
        }
    </style>
@endpush

@section('content')
    <div class="px-1 pt-2 pb-24">
        <div id="shortcut-list">
            
            {{-- Presensi & Operasional --}}
            <div class="text-[11px] font-bold text-slate-400 tracking-wider uppercase px-1 mb-2">
                Presensi & Pengajuan
            </div>

            <div class="menu-group divide-y divide-slate-100/90">
                <a href="{{ route('facerecognition.karyawan.create') }}" class="menu-row">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-emerald-50 text-[#1E4D3E] border border-emerald-100">
                            <ion-icon name="scan-outline" class="text-xl"></ion-icon>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <h3 class="text-[13px] font-bold text-slate-800 leading-snug truncate m-0">Pendaftaran Face ID</h3>
                            <span class="text-[11px] text-slate-500 truncate">Perekaman data wajah biometrik</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-base shrink-0 ml-2"></ion-icon>
                </a>

                <a href="{{ route('pengajuanizin.index') }}" class="menu-row">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-600 border border-blue-100">
                            <ion-icon name="calendar-outline" class="text-xl"></ion-icon>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <h3 class="text-[13px] font-bold text-slate-800 leading-snug truncate m-0">Pengajuan Izin, Sakit & Cuti</h3>
                            <span class="text-[11px] text-slate-500 truncate">Formulir permohonan izin dan cuti</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-base shrink-0 ml-2"></ion-icon>
                </a>

                <a href="{{ route('dispensasi.index') }}" class="menu-row">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-amber-50 text-amber-600 border border-amber-100">
                            <ion-icon name="time-outline" class="text-xl"></ion-icon>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <h3 class="text-[13px] font-bold text-slate-800 leading-snug truncate m-0">Dispensasi Keterlambatan</h3>
                            <span class="text-[11px] text-slate-500 truncate">Permohonan dispensasi jam kehadiran</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-base shrink-0 ml-2"></ion-icon>
                </a>

                <a href="{{ route('presensi.histori') }}" class="menu-row">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-purple-50 text-purple-600 border border-purple-100">
                            <ion-icon name="finger-print-outline" class="text-xl"></ion-icon>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <h3 class="text-[13px] font-bold text-slate-800 leading-snug truncate m-0">Riwayat Kehadiran</h3>
                            <span class="text-[11px] text-slate-500 truncate">Histori absensi masuk & pulang</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-base shrink-0 ml-2"></ion-icon>
                </a>
            </div>

            {{-- Profil & Akun --}}
            <div class="text-[11px] font-bold text-slate-400 tracking-wider uppercase px-1 mb-2">
                Profil & Akun
            </div>

            <div class="menu-group divide-y divide-slate-100/90">
                <a href="{{ route('profile.index') }}" class="menu-row">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-600 border border-slate-200">
                            <ion-icon name="person-outline" class="text-xl"></ion-icon>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <h3 class="text-[13px] font-bold text-slate-800 leading-snug truncate m-0">Profil Karyawan</h3>
                            <span class="text-[11px] text-slate-500 truncate">Data akun dan informasi pribadi</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-base shrink-0 ml-2"></ion-icon>
                </a>
            </div>

        </div>
    </div>
@endsection
