@extends('layouts.mobile.modern')
@section('title', 'Semua Menu')

@section('header_left')
    <a href="{{ route('dashboard.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        body {
            background-color: #f8fafc;
        }
        
        .card.press {
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
            margin-bottom: 0;
            display: block;
            text-decoration: none !important;
        }

        .card.press:active {
            transform: scale(0.98);
            background: #f8fafc;
        }

        .fade-up {
            animation: fadeUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(10px);
        }

        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }

        .badge.modern-badge {
            background: #ef4444;
            color: white;
            padding: 2px 6px;
            font-size: 10px;
            border-radius: 6px;
            font-weight: 700;
        }
    </style>
@endpush

@section('content')
    <div class="px-2 pt-2 pb-24">
        
        <div class="space-y-2 pt-1" id="shortcut-list">
            
            {{-- Personal --}}
            @if(\App\Models\KaryawanMenuSetting::isActive('idcard') || \App\Models\KaryawanMenuSetting::isActive('kontrak'))
            <div class="text-[11px] font-bold text-slate-400 tracking-wider uppercase px-2 pt-2 pb-0.5 fade-up" style="animation-delay: 0.05s">Dokumen & Profil</div>
            
            @if(\App\Models\KaryawanMenuSetting::isActive('idcard'))
            <a href="{{ route('karyawan.idcard', Crypt::encrypt($karyawan->nik)) }}" class="card press fade-up" style="animation-delay: 0.1s">
                <div class="card-body p-2 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-[10px]" style="background: #e0f2fe; color: #0284c7;">
                            <ion-icon name="id-card-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">ID Card</h3>
                            <span class="text-[11px] text-slate-500 font-medium">Lihat Kartu Identitas Digital</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-lg mr-2"></ion-icon>
                </div>
            </a>
            @endif

            @if(\App\Models\KaryawanMenuSetting::isActive('kontrak'))
            <a href="{{ route('kontrak.index') }}" class="card press fade-up" style="animation-delay: 0.15s">
                <div class="card-body p-2 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-[10px]" style="background: #fef3c7; color: #d97706;">
                            <ion-icon name="document-attach-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">Dokumen Kontrak</h3>
                            <span class="text-[11px] text-slate-500 font-medium">Lihat Masa Berlaku Kontrak</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-lg mr-2"></ion-icon>
                </div>
            </a>
            @endif
            @endif
            
            {{-- Absensi & Wajah --}}
            @if(\App\Models\KaryawanMenuSetting::isActive('wajah') || \App\Models\KaryawanMenuSetting::isActive('absen_istirahat') || \App\Models\KaryawanMenuSetting::isActive('lembur'))
            <div class="text-[11px] font-bold text-slate-400 tracking-wider uppercase px-2 pt-3 pb-0.5 fade-up" style="animation-delay: 0.2s">Presensi & Absensi</div>

            @if(\App\Models\KaryawanMenuSetting::isActive('wajah'))
            <a href="{{ route('facerecognition.karyawan.create') }}" class="card press fade-up" style="animation-delay: 0.25s">
                <div class="card-body p-2 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-[10px]" style="background: #f0fdf4; color: #16a34a;">
                            <ion-icon name="scan-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">Daftar Face ID</h3>
                            <span class="text-[11px] text-slate-500 font-medium">Rekam Data Wajah Absensi</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-lg mr-2"></ion-icon>
                </div>
            </a>
            @endif

            @if(\App\Models\KaryawanMenuSetting::isActive('absen_istirahat'))
            <a href="{{ route('presensiistirahat.create') }}" class="card press fade-up" style="animation-delay: 0.3s">
                <div class="card-body p-2 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-[10px]" style="background: #ffedd5; color: #ea580c;">
                            <ion-icon name="cafe-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">Absen Istirahat</h3>
                            <span class="text-[11px] text-slate-500 font-medium">Catat Keluar Masuk Istirahat</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-lg mr-2"></ion-icon>
                </div>
            </a>
            @endif

            @if(\App\Models\KaryawanMenuSetting::isActive('lembur'))
            <a href="{{ route('lembur.index') }}" class="card press fade-up" style="animation-delay: 0.35s">
                <div class="card-body p-2 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-[10px]" style="background: #f3e8ff; color: #9333ea;">
                            <ion-icon name="time-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">Lembur Harian</h3>
                            <span class="text-[11px] text-slate-500 font-medium">Riwayat Pekerjaan Lembur</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-lg mr-2"></ion-icon>
                </div>
            </a>
            @endif
            @endif

            {{-- Approval / Khusus --}}
            @if(isset($hasApprovalAccess) && $hasApprovalAccess && \App\Models\KaryawanMenuSetting::isActive('hak_approval'))
            <div class="text-[11px] font-bold text-slate-400 tracking-wider uppercase px-2 pt-3 pb-0.5 fade-up" style="animation-delay: 0.4s">Persetujuan</div>
            
            <a href="{{ route('karyawan-approval.index') }}" class="card press fade-up" style="animation-delay: 0.45s">
                <div class="card-body p-2 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-[10px]" style="background: #ccfbf1; color: #0f766e;">
                            <ion-icon name="checkmark-done-circle-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">Hak Approval</h3>
                            <span class="text-[11px] text-slate-500 font-medium">Persetujuan Izin / Cuti Karyawan</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if(isset($pendingApprovalCount) && $pendingApprovalCount > 0)
                            <span class="badge modern-badge">{{ $pendingApprovalCount }}</span>
                        @endif
                        <ion-icon name="chevron-forward-outline" class="text-slate-300 text-lg mr-2"></ion-icon>
                    </div>
                </div>
            </a>
            @endif

            {{-- Informasi & Pengumuman --}}
            @if(\App\Models\KaryawanMenuSetting::isActive('pengumuman'))
            <div class="text-[11px] font-bold text-slate-400 tracking-wider uppercase px-2 pt-3 pb-0.5 fade-up" style="animation-delay: 0.5s">Informasi</div>

            <a href="{{ route('pengumuman.index') }}" class="card press fade-up" style="animation-delay: 0.55s">
                <div class="card-body p-2 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-[10px]" style="background: #fdf4ff; color: #a21caf;">
                            <ion-icon name="megaphone-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[14px] font-bold text-slate-800 leading-tight">Pengumuman</h3>
                            <span class="text-[11px] text-slate-500 font-medium">Pusat Informasi Outlet & Cafe</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-lg mr-2"></ion-icon>
                </div>
            </a>
            @endif

        </div>
    </div>
@endsection
