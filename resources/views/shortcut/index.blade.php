@extends('layouts.mobile.modern')
@section('title', 'Semua Menu')

@section('header_left')
    <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('dashboard.index') }}"
        onclick="if (window.history.length > 1 && document.referrer && document.referrer.indexOf(window.location.host) !== -1) { event.preventDefault(); window.history.back(); }"
        class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/15 text-white active:scale-90 transition-transform"
        title="Kembali">
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
            
            {{-- Presensi & Pengajuan --}}
            @if(module_enabled('attendance') || module_enabled('leave') || (module_enabled('face_recognition') && ($general_setting->face_recognition ?? 0) == 1))
            <div class="text-[11px] font-bold text-slate-400 tracking-wider uppercase px-1 mb-2">
                Presensi & Pengajuan
            </div>

            <div class="menu-group divide-y divide-slate-100/90">
                @if(module_enabled('face_recognition') && ($general_setting->face_recognition ?? 0) == 1 && Route::has('facerecognition.karyawan.create'))
                <a href="{{ route('facerecognition.karyawan.create') }}" class="menu-row">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100">
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

                @if(module_enabled('leave') && Route::has('pengajuanizin.index'))
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
                @endif

                @if(module_enabled('attendance') && Route::has('dispensasi.index'))
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
                @endif

                @if(module_enabled('attendance') && Route::has('presensi.histori'))
                <a href="{{ route('presensi.histori') }}" class="menu-row">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100">
                            <ion-icon name="finger-print-outline" class="text-xl"></ion-icon>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <h3 class="text-[13px] font-bold text-slate-800 leading-snug truncate m-0">Riwayat Kehadiran</h3>
                            <span class="text-[11px] text-slate-500 truncate">Histori absensi masuk & pulang</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-base shrink-0 ml-2"></ion-icon>
                </a>
                @endif
            </div>
            @endif

            {{-- Keuangan & Fasilitas (Module Guarded) --}}
            @if(module_enabled('payroll') || module_enabled('reimbursement') || module_enabled('loans') || module_enabled('overtime'))
            <div class="text-[11px] font-bold text-slate-400 tracking-wider uppercase px-1 mb-2">
                Keuangan & Fasilitas
            </div>

            <div class="menu-group divide-y divide-slate-100/90">
                @if(module_enabled('payroll') && Route::has('payslip.my_payslips'))
                <a href="{{ route('payslip.my_payslips') }}" class="menu-row">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100">
                            <ion-icon name="cash-outline" class="text-xl"></ion-icon>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <h3 class="text-[13px] font-bold text-slate-800 leading-snug truncate m-0">Slip Gaji Digital</h3>
                            <span class="text-[11px] text-slate-500 truncate">Rincian pendapatan bulanan dan potongan</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-base shrink-0 ml-2"></ion-icon>
                </a>
                @endif

                @if(module_enabled('reimbursement') && Route::has('reimbursement.index'))
                <a href="{{ route('reimbursement.index') }}" class="menu-row">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-600 border border-blue-100">
                            <ion-icon name="receipt-outline" class="text-xl"></ion-icon>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <h3 class="text-[13px] font-bold text-slate-800 leading-snug truncate m-0">Reimbursement & Klaim</h3>
                            <span class="text-[11px] text-slate-500 truncate">Klaim biaya operasional dan pengobatan</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-base shrink-0 ml-2"></ion-icon>
                </a>
                @endif

                @if(module_enabled('loans') && Route::has('loan.index'))
                <a href="{{ route('loan.index') }}" class="menu-row">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-purple-50 text-purple-600 border border-purple-100">
                            <ion-icon name="wallet-outline" class="text-xl"></ion-icon>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <h3 class="text-[13px] font-bold text-slate-800 leading-snug truncate m-0">Pinjaman & Kasbon</h3>
                            <span class="text-[11px] text-slate-500 truncate">Pengajuan dan status cicilan kasbon</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-base shrink-0 ml-2"></ion-icon>
                </a>
                @endif

                @if(module_enabled('overtime') && Route::has('overtime.index'))
                <a href="{{ route('overtime.index') }}" class="menu-row">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-amber-50 text-amber-600 border border-amber-100">
                            <ion-icon name="time-outline" class="text-xl"></ion-icon>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <h3 class="text-[13px] font-bold text-slate-800 leading-snug truncate m-0">Lembur & SPK</h3>
                            <span class="text-[11px] text-slate-500 truncate">Surat perintah dan pengajuan jam lembur</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-base shrink-0 ml-2"></ion-icon>
                </a>
                @endif
            </div>
            @endif

            {{-- Kebijakan & Dokumen (Module Guarded) --}}
            @if(module_enabled('policy') || module_enabled('announcements') || module_enabled('documents'))
            <div class="text-[11px] font-bold text-slate-400 tracking-wider uppercase px-1 mb-2">
                Informasi & Dokumen
            </div>

            <div class="menu-group divide-y divide-slate-100/90">
                @if(module_enabled('policy') && Route::has('policy.index'))
                <a href="{{ route('policy.index') }}" class="menu-row">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-teal-50 text-teal-600 border border-teal-100">
                            <ion-icon name="shield-checkmark-outline" class="text-xl"></ion-icon>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <h3 class="text-[13px] font-bold text-slate-800 leading-snug truncate m-0">Peraturan Perusahaan & SOP</h3>
                            <span class="text-[11px] text-slate-500 truncate">Kebijakan dan tata tertib kerja</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-base shrink-0 ml-2"></ion-icon>
                </a>
                @endif

                @if(module_enabled('announcements') && Route::has('announcement.index'))
                <a href="{{ route('announcement.index') }}" class="menu-row">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-sky-50 text-sky-600 border border-sky-100">
                            <ion-icon name="megaphone-outline" class="text-xl"></ion-icon>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <h3 class="text-[13px] font-bold text-slate-800 leading-snug truncate m-0">Pengumuman Internal</h3>
                            <span class="text-[11px] text-slate-500 truncate">Informasi resmi dari manajemen</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-base shrink-0 ml-2"></ion-icon>
                </a>
                @endif

                @if(module_enabled('documents') && Route::has('document.index'))
                <a href="{{ route('document.index') }}" class="menu-row">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-600 border border-slate-200">
                            <ion-icon name="folder-outline" class="text-xl"></ion-icon>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <h3 class="text-[13px] font-bold text-slate-800 leading-snug truncate m-0">Brankas Dokumen</h3>
                            <span class="text-[11px] text-slate-500 truncate">Arsip surat dan dokumen kepegawaian</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-base shrink-0 ml-2"></ion-icon>
                </a>
                @endif
            </div>
            @endif

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

            {{-- Pusat Bantuan & Panduan --}}
            <div class="text-[11px] font-bold text-slate-400 tracking-wider uppercase px-1 mb-2">
                Pusat Bantuan & Panduan
            </div>

            <div class="menu-group divide-y divide-slate-100/90">
                <a href="javascript:void(0)" onclick="openHelpDrawer('panduan_karyawan')" class="menu-row">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-amber-50 text-amber-700 border border-amber-100">
                            <ion-icon name="help-circle-outline" class="text-xl"></ion-icon>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <h3 class="text-[13px] font-bold text-slate-800 leading-snug truncate m-0">Panduan Presensi Karyawan</h3>
                            <span class="text-[11px] text-slate-500 truncate">Petunjuk shift, GPS, face ID, izin, cuti & kendala</span>
                        </div>
                    </div>
                    <ion-icon name="chevron-forward-outline" class="text-slate-300 text-base shrink-0 ml-2"></ion-icon>
                </a>
            </div>

        </div>
    </div>
@endsection
