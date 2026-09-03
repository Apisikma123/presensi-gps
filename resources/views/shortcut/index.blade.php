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
            
            {{-- Personal / Dokumen --}}
            @if(isset($karyawan) && $karyawan->status_karyawan == 'K' && \App\Models\KaryawanMenuSetting::isActive('kontrak'))
                <div class="text-[11px] font-bold text-slate-400 tracking-wider uppercase px-1 mb-2">
                    Dokumen & Profil
                </div>
                
                <div class="menu-group divide-y divide-slate-100/90">
                    <a href="{{ route('kontrak.index') }}" class="menu-row">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-amber-50 text-amber-600 border border-amber-100">
                                <ion-icon name="document-attach-outline" class="text-xl"></ion-icon>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <h3 class="text-[13px] font-bold text-slate-800 leading-snug truncate m-0">Dokumen Kontrak</h3>
                                <span class="text-[11px] text-slate-500 truncate">Masa berlaku & detail perjanjian</span>
                            </div>
                        </div>
                        <ion-icon name="chevron-forward-outline" class="text-slate-300 text-base shrink-0 ml-2"></ion-icon>
                    </a>
                </div>
            @endif
            
            {{-- Absensi & Operasional --}}
            @if(\App\Models\KaryawanMenuSetting::isActive('wajah') || \App\Models\KaryawanMenuSetting::isActive('absen_istirahat') || \App\Models\KaryawanMenuSetting::isActive('lembur'))
                <div class="text-[11px] font-bold text-slate-400 tracking-wider uppercase px-1 mb-2">
                    Presensi & Operasional
                </div>

                <div class="menu-group divide-y divide-slate-100/90">
                    @if(\App\Models\KaryawanMenuSetting::isActive('wajah'))
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
                    @endif

                    @if(\App\Models\KaryawanMenuSetting::isActive('absen_istirahat'))
                        <a href="{{ route('presensiistirahat.create') }}" class="menu-row">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-orange-50 text-orange-600 border border-orange-100">
                                    <ion-icon name="cafe-outline" class="text-xl"></ion-icon>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <h3 class="text-[13px] font-bold text-slate-800 leading-snug truncate m-0">Absen Istirahat</h3>
                                    <span class="text-[11px] text-slate-500 truncate">Pencatatan jeda istirahat shift</span>
                                </div>
                            </div>
                            <ion-icon name="chevron-forward-outline" class="text-slate-300 text-base shrink-0 ml-2"></ion-icon>
                        </a>
                    @endif

                    @if(\App\Models\KaryawanMenuSetting::isActive('lembur'))
                        <a href="{{ route('lembur.index') }}" class="menu-row">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-purple-50 text-purple-600 border border-purple-100">
                                    <ion-icon name="time-outline" class="text-xl"></ion-icon>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <h3 class="text-[13px] font-bold text-slate-800 leading-snug truncate m-0">Lembur Harian</h3>
                                    <span class="text-[11px] text-slate-500 truncate">Pengajuan & riwayat jam lembur</span>
                                </div>
                            </div>
                            <ion-icon name="chevron-forward-outline" class="text-slate-300 text-base shrink-0 ml-2"></ion-icon>
                        </a>
                    @endif
                </div>
            @endif

            {{-- Persetujuan & Informasi --}}
            @if((isset($hasApprovalAccess) && $hasApprovalAccess && \App\Models\KaryawanMenuSetting::isActive('hak_approval')) || \App\Models\KaryawanMenuSetting::isActive('pengumuman'))
                <div class="text-[11px] font-bold text-slate-400 tracking-wider uppercase px-1 mb-2">
                    Persetujuan & Informasi
                </div>

                <div class="menu-group divide-y divide-slate-100/90">
                    @if(isset($hasApprovalAccess) && $hasApprovalAccess && \App\Models\KaryawanMenuSetting::isActive('hak_approval'))
                        <a href="{{ route('karyawan-approval.index') }}" class="menu-row">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-teal-50 text-teal-700 border border-teal-100">
                                    <ion-icon name="checkmark-done-circle-outline" class="text-xl"></ion-icon>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <h3 class="text-[13px] font-bold text-slate-800 leading-snug truncate m-0">Hak Approval</h3>
                                    <span class="text-[11px] text-slate-500 truncate">Persetujuan permohonan tim</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0 ml-2">
                                @if(isset($pendingApprovalCount) && $pendingApprovalCount > 0)
                                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full bg-rose-50 text-rose-600 border border-rose-200 text-[10px] font-bold font-mono">
                                        {{ $pendingApprovalCount }}
                                    </span>
                                @endif
                                <ion-icon name="chevron-forward-outline" class="text-slate-300 text-base"></ion-icon>
                            </div>
                        </a>
                    @endif

                    @if(\App\Models\KaryawanMenuSetting::isActive('pengumuman'))
                        <a href="{{ route('pengumuman.index') }}" class="menu-row">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-rose-50 text-rose-600 border border-rose-100">
                                    <ion-icon name="megaphone-outline" class="text-xl"></ion-icon>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <h3 class="text-[13px] font-bold text-slate-800 leading-snug truncate m-0">Pengumuman</h3>
                                    <span class="text-[11px] text-slate-500 truncate">Pusat informasi outlet & cafe</span>
                                </div>
                            </div>
                            <ion-icon name="chevron-forward-outline" class="text-slate-300 text-base shrink-0 ml-2"></ion-icon>
                        </a>
                    @endif
                </div>
            @endif

        </div>
    </div>
@endsection
