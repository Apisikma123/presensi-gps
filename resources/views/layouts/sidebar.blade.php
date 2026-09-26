 <!-- Menu -->

 <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
     <div class="app-brand demo px-3" style="height: 76px !important;">
         <a href="{{ route('dashboard.index') }}" class="app-brand-link d-flex align-items-center text-decoration-none">
             <span class="app-brand-logo rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                style="background: rgba(255, 255, 255, 0.12); width: 40px; height: 40px !important; overflow: hidden; border: 1px solid rgba(255,255,255,0.18);">
                @if (!empty($app_logo_url))
                    <img src="{{ $app_logo_url }}" alt="Logo" class="w-100 h-100"
                        style="object-fit: contain; padding: 2px;"
                        onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                    <div class="w-100 h-100 align-items-center justify-content-center text-white" style="display: none; background: rgba(255,255,255,0.15);">
                        <i class="ti ti-fingerprint" style="font-size: 22px;"></i>
                    </div>
                @else
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-white" style="background: rgba(255,255,255,0.15);">
                        <i class="ti ti-fingerprint" style="font-size: 22px;"></i>
                    </div>
                @endif
            </span>
             <div class="d-flex flex-column ms-3">
                 <span class="fw-bold text-white lh-1" style="font-size: 15px; letter-spacing: -0.01em;">
                     {{ $company_setting->app_name ?? ($general_setting->nama_aplikasi ?? 'Presence') }}
                 </span>
                 <span class="text-white-50 mt-1" style="font-size: 11px; letter-spacing: 0.02em;">
                     {{ $company_setting->app_tagline ?? 'Universal HR Management System' }}
                 </span>
             </div>
         </a>

         <a href="javascript:void(0);" class="layout-menu-close menu-link text-large ms-auto d-xl-none d-flex align-items-center justify-content-center" id="btn-close-sidebar" aria-label="Tutup Menu" style="width: 36px; height: 36px; border-radius: 8px; cursor: pointer; text-decoration: none;">
             <i class="ti ti-x ti-sm align-middle text-white"></i>
         </a>
     </div>

     @php
         $authUser = auth()->user();
         $fullName = $authUser->name ?? 'Pengguna';
         $userName = explode(' ', $fullName)[0];
         $userEmail = $authUser->email ?? '-';
         $userRoleText = $authUser ? ($authUser->getRoleNames()->first() ?? 'User') : 'Guest';

         $userPhoto = null;
         if ($authUser) {
             $userPhoto = \Illuminate\Support\Facades\Cache::remember('sidebar_user_photo_' . $authUser->id, 300, function() use ($authUser) {
                 $userKaryawan = \App\Models\Userkaryawan::where('id_user', $authUser->id)->first();
                 if ($userKaryawan) {
                     $sidebarKaryawan = \App\Models\Karyawan::where('nik', $userKaryawan->nik)->first();
                     if ($sidebarKaryawan && $sidebarKaryawan->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('/karyawan/' . $sidebarKaryawan->foto)) {
                         return getfotoKaryawan($sidebarKaryawan->foto);
                     }
                 }
                 return null;
             });
         }
     @endphp

     <div class="px-3 py-2 mb-2">
         <div class="d-flex align-items-center p-2.5"
             style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px;">
             <div class="flex-shrink-0 position-relative">
                 @if ($userPhoto)
                     <div class="rounded-circle shadow-sm"
                         style="width: 40px; height: 40px; background-image: url('{{ $userPhoto }}'); background-size: cover; background-position: center; border: 2px solid rgba(255,255,255,0.25);">
                     </div>
                 @else
                     <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                         style="width: 40px; height: 40px; font-size: 18px; background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.2);">
                         <i class="ti ti-user"></i>
                     </div>
                 @endif
             </div>
             <div class="flex-grow-1 ms-2.5 overflow-hidden">
                 <div class="fw-bold text-white text-truncate mb-0" style="font-size: 13.5px;">{{ $userName }}</div>
                 <span class="badge px-1.5 py-0.5 mt-0.5 text-uppercase"
                     style="font-size: 9.5px; background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.9); font-weight: 600; letter-spacing: 0.04em;">
                     {{ $userRoleText }}
                 </span>
             </div>
             <a href="{{ route('profile.editprofile') }}" class="btn btn-sm p-1.5 ms-1 text-white rounded-2"
                 style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.15);"
                 data-bs-toggle="tooltip" title="Pengaturan Profil">
                 <i class="ti ti-settings" style="font-size: 15px;"></i>
             </a>
         </div>
     </div>

      <div class="menu-inner-shadow"></div>

     <ul class="menu-inner py-1">
         <!-- 1. UTAMA -->
         <li class="menu-header small text-uppercase px-3 py-2 text-white-50" style="font-size: 10.5px; font-weight: 700; letter-spacing: 0.08em;">
             <span>UTAMA</span>
         </li>
         <li class="menu-item {{ request()->is(['dashboard', 'dashboard/*']) ? 'active' : '' }}">
             <a href="{{ route('dashboard.index') }}" class="menu-link">
                 <i class="menu-icon tf-icons ti ti-home"></i>
                 <div>Dashboard</div>
             </a>
         </li>

         <!-- 2. DATA MASTER -->
         @if (auth()->user()->hasAnyPermission(['karyawan.index', 'departemen.index', 'divisi.index', 'cabang.index', 'cuti.index', 'leave_types.index', 'jabatan.index', 'jamkerja.index', 'harilibur.index']))
             <li class="menu-header small text-uppercase px-3 py-2 text-white-50 mt-2" style="font-size: 10.5px; font-weight: 700; letter-spacing: 0.08em;">
                 <span>DATA MASTER</span>
             </li>
             <li class="menu-item {{ request()->is(['karyawan', 'karyawan/*', 'departemen', 'departemen/*', 'divisi', 'divisi/*', 'cabang', 'cabang/*', 'cuti', 'cuti/*', 'leave-types', 'leave-types/*', 'jabatan', 'jabatan/*', 'jamkerja', 'jamkerja/*', 'harilibur', 'harilibur/*']) ? 'open' : '' }}">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-database"></i>
                     <div>Data Master</div>
                 </a>
                 <ul class="menu-sub">
                     @can('karyawan.index')
                         <li class="menu-item {{ request()->is(['karyawan', 'karyawan/*']) ? 'active' : '' }}">
                             <a href="{{ route('karyawan.index') }}" class="menu-link">
                                 <div>{{ is_module_enabled('face_recognition', false) ? 'Karyawan & Wajah' : 'Data Karyawan' }}</div>
                             </a>
                         </li>
                     @endcan
                     @module('attendance')
                     @can('jamkerja.index')
                         <li class="menu-item {{ request()->is(['jamkerja', 'jamkerja/*']) ? 'active' : '' }}">
                             <a href="{{ route('jamkerja.index') }}" class="menu-link">
                                 <div>Shift Kerja (Pagi & Siang)</div>
                             </a>
                         </li>
                     @endcan
                     @endmodule
                     @can('harilibur.index')
                         <li class="menu-item {{ request()->is(['harilibur', 'harilibur/*']) ? 'active' : '' }}">
                             <a href="{{ route('harilibur.index') }}" class="menu-link">
                                 <div>Hari Libur / Tanggal Merah</div>
                             </a>
                         </li>
                     @endcan
                     @can('cabang.index')
                         <li class="menu-item {{ request()->is(['cabang', 'cabang/*']) ? 'active' : '' }}">
                             <a href="{{ route('cabang.index') }}" class="menu-link">
                                 <div>Outlet / Cabang</div>
                             </a>
                         </li>
                     @endcan
                     @can('departemen.index')
                         <li class="menu-item {{ request()->is(['departemen', 'departemen/*']) ? 'active' : '' }}">
                             <a href="{{ route('departemen.index') }}" class="menu-link">
                                 <div>Departemen</div>
                             </a>
                         </li>
                     @endcan
                     @can('divisi.index')
                         <li class="menu-item {{ request()->is(['divisi', 'divisi/*']) ? 'active' : '' }}">
                             <a href="{{ route('divisi.index') }}" class="menu-link">
                                 <div>Divisi & Tim</div>
                             </a>
                         </li>
                     @endcan
                     @can('jabatan.index')
                         <li class="menu-item {{ request()->is(['jabatan', 'jabatan/*']) ? 'active' : '' }}">
                             <a href="{{ route('jabatan.index') }}" class="menu-link">
                                 <div>Jabatan / Posisi</div>
                             </a>
                         </li>
                     @endcan
                     @module('leave')
                         @if(auth()->user()->can('leave_types.index'))
                             <li class="menu-item {{ request()->is(['leave-types', 'leave-types/*', 'cuti', 'cuti/*']) ? 'active' : '' }}">
                                 <a href="{{ route('leave_types.index') }}" class="menu-link">
                                     <div>Jenis Cuti</div>
                                 </a>
                             </li>
                         @elseif(auth()->user()->can('cuti.index'))
                             <li class="menu-item {{ request()->is(['cuti', 'cuti/*']) ? 'active' : '' }}">
                                 <a href="{{ route('cuti.index') }}" class="menu-link">
                                     <div>Jenis Cuti</div>
                                 </a>
                             </li>
                         @endif
                     @endmodule
                 </ul>
             </li>
         @endif

         <!-- 2.5 KEPEGAWAIAN -->
         @php
             $showKepegawaian = (module_enabled('contract') && auth()->user()->can('kontrak.index'))
                 || (module_enabled('movement') && auth()->user()->can('movement.index'))
                 || (module_enabled('resignation') && auth()->user()->can('resignation.index'))
                 || (module_enabled('leave') && auth()->user()->can('leave_quotas.index'))
                 || (module_enabled('overtime') && auth()->user()->can('overtime.index'))
                 || (module_enabled('recruitment') && auth()->user()->can('recruitment.index'))
                 || (module_enabled('onboarding') && auth()->user()->can('onboarding.index'))
                 || auth()->user()->can('org_chart.index')
                 || auth()->user()->can('karyawan.import');

             $showTataKelola = (module_enabled('performance') && auth()->user()->can('performance.index'))
                 || (module_enabled('training') && auth()->user()->can('training.index'))
                 || (module_enabled('warning') && auth()->user()->can('warning.index'))
                 || (module_enabled('document') && auth()->user()->can('document.index'))
                 || module_enabled('policy')
                 || (module_enabled('asset') && auth()->user()->can('asset.index'))
                 || (module_enabled('announcement') && auth()->user()->can('announcement.index'))
                 || (module_enabled('incident') && auth()->user()->can('incident.index'));
         @endphp

         @if ($showKepegawaian || $showTataKelola)
             <li class="menu-header small text-uppercase px-3 py-2 text-white-50 mt-2" style="font-size: 10.5px; font-weight: 700; letter-spacing: 0.08em;">
                 <span>KEPEGAWAIAN</span>
             </li>
             @if ($showKepegawaian)
             <li class="menu-item {{ request()->is(['kontrak', 'kontrak/*', 'movement', 'movement/*', 'resignation', 'resignation/*', 'leave-quotas', 'leave-quotas/*', 'overtime', 'overtime/*', 'recruitment', 'recruitment/*', 'onboarding', 'onboarding/*']) ? 'open' : '' }}">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-users-group"></i>
                     <div>Kepegawaian & Karir</div>
                 </a>
                 <ul class="menu-sub">
                     @module('contract')
                         @can('kontrak.index')
                             <li class="menu-item {{ request()->is(['kontrak', 'kontrak/*']) ? 'active' : '' }}">
                                 <a href="{{ route('kontrak.index') }}" class="menu-link">
                                     <div>Kontrak Kerja</div>
                                 </a>
                             </li>
                         @endcan
                     @endmodule
                     @module('movement')
                         @can('movement.index')
                             <li class="menu-item {{ request()->is(['movement', 'movement/*']) ? 'active' : '' }}">
                                 <a href="{{ route('movement.index') }}" class="menu-link">
                                     <div>Mutasi & Karir</div>
                                 </a>
                             </li>
                         @endcan
                     @endmodule
                     @module('resignation')
                         @can('resignation.index')
                             <li class="menu-item {{ request()->is(['resignation', 'resignation/*']) ? 'active' : '' }}">
                                 <a href="{{ route('resignation.index') }}" class="menu-link">
                                     <div>Resign & Offboarding</div>
                                 </a>
                             </li>
                         @endcan
                     @endmodule
                     @module('leave')
@can('leave_quotas.index')
                         <li class="menu-item {{ request()->is(['leave-quotas', 'leave-quotas/*']) ? 'active' : '' }}">
                             <a href="{{ route('leave_quotas.index') }}" class="menu-link">
                                 <div>Saldo Kuota Cuti</div>
                             </a>
                         </li>
                     @endcan
                    @endmodule
                     @module('overtime')
                         @can('overtime.index')
                             <li class="menu-item {{ request()->is(['overtime', 'overtime/*']) ? 'active' : '' }}">
                                 <a href="{{ route('overtime.index') }}" class="menu-link">
                                     <div>Lembur & SPK</div>
                                 </a>
                             </li>
                         @endcan
                     @endmodule
                     @module('recruitment')
@can('recruitment.index')
                         <li class="menu-item {{ request()->is(['recruitment', 'recruitment/*']) ? 'active' : '' }}">
                             <a href="{{ route('recruitment.index') }}" class="menu-link">
                                 <div>Rekrutmen & Pelamar</div>
                             </a>
                         </li>
                     @endcan
                    @endmodule
                     @module('onboarding')
@can('onboarding.index')
                         <li class="menu-item {{ request()->is(['onboarding', 'onboarding/*']) ? 'active' : '' }}">
                             <a href="{{ route('onboarding.index') }}" class="menu-link">
                                 <div>Onboarding Karyawan</div>
                             </a>
                         </li>
                     @endcan
                    @endmodule
                     @can('org_chart.index')
                         <li class="menu-item {{ request()->is(['org-chart', 'org-chart/*']) ? 'active' : '' }}">
                             <a href="{{ route('org_chart.index') }}" class="menu-link">
                                 <div>Bagan Organisasi</div>
                             </a>
                         </li>
                     @endcan
                     @can('karyawan.import')
                         <li class="menu-item {{ request()->is(['karyawan-import', 'karyawan-import/*']) ? 'active' : '' }}">
                             <a href="{{ route('karyawan.import.index') }}" class="menu-link">
                                 <div>Import & Kelola Massal</div>
                             </a>
                         </li>
                     @endcan
                 </ul>
             </li>
             @endif

             @if ($showTataKelola)
             <li class="menu-item {{ request()->is(['performance', 'performance/*', 'trainings', 'trainings/*', 'warnings', 'warnings/*', 'employee-documents', 'employee-documents/*', 'company-policies', 'company-policies/*', 'assets', 'assets/*', 'announcements', 'announcements/*', 'incidents', 'incidents/*']) ? 'open' : '' }}">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-award"></i>
                     <div>Kinerja & Tata Kelola</div>
                 </a>
                 <ul class="menu-sub">
                     @module('performance')
@can('performance.index')
                         <li class="menu-item {{ request()->is(['performance', 'performance/*']) ? 'active' : '' }}">
                             <a href="{{ route('performance.index') }}" class="menu-link">
                                 <div>Penilaian Kinerja & KPI</div>
                             </a>
                         </li>
                     @endcan
                    @endmodule
                     @module('training')
@can('training.index')
                         <li class="menu-item {{ request()->is(['trainings', 'trainings/*']) ? 'active' : '' }}">
                             <a href="{{ route('training.index') }}" class="menu-link">
                                 <div>Pelatihan & Sertifikasi</div>
                             </a>
                         </li>
                     @endcan
                    @endmodule
                     @module('warning')
@can('warning.index')
                         <li class="menu-item {{ request()->is(['warnings', 'warnings/*']) ? 'active' : '' }}">
                             <a href="{{ route('warning.index') }}" class="menu-link">
                                 <div>Disiplin & Surat Peringatan</div>
                             </a>
                         </li>
                     @endcan
                    @endmodule
                     @module('document')
@can('document.index')
                         <li class="menu-item {{ request()->is(['employee-documents', 'employee-documents/*']) ? 'active' : '' }}">
                             <a href="{{ route('document.index') }}" class="menu-link">
                                 <div>Brankas Dokumen</div>
                             </a>
                         </li>
                     @endcan
                    @endmodule
                     @module('policy')
<li class="menu-item {{ request()->is(['company-policies', 'company-policies/*']) ? 'active' : '' }}">
                         <a href="{{ route('policy.index') }}" class="menu-link">
                             <div>Peraturan & SOP</div>
                         </a>
                     </li>
                    @endmodule
                     @module('asset')
@can('asset.index')
                         <li class="menu-item {{ request()->is(['assets', 'assets/*']) ? 'active' : '' }}">
                             <a href="{{ route('asset.index') }}" class="menu-link">
                                 <div>Aset & Fasilitas</div>
                             </a>
                         </li>
                     @endcan
                    @endmodule
                     @module('announcement')
@can('announcement.index')
                         <li class="menu-item {{ request()->is(['announcements', 'announcements/*']) ? 'active' : '' }}">
                             <a href="{{ route('announcement.index') }}" class="menu-link">
                                 <div>Pengumuman Internal</div>
                             </a>
                         </li>
                     @endcan
                    @endmodule
                     @module('incident')
@can('incident.index')
                         <li class="menu-item {{ request()->is(['incidents', 'incidents/*']) ? 'active' : '' }}">
                             <a href="{{ route('incident.index') }}" class="menu-link">
                                 <div>Kasus & Insiden HR</div>
                             </a>
                         </li>
                     @endcan
                    @endmodule
                 </ul>
             </li>
             @endif
         @endif

         <!-- 3. ABSENSI -->
        @module('attendance')
         @if (auth()->user()->hasAnyPermission(['presensi.index', 'trackingpresensi.index', 'dispensasi.index']))
             <li class="menu-header small text-uppercase px-3 py-2 text-white-50 mt-2" style="font-size: 10.5px; font-weight: 700; letter-spacing: 0.08em;">
                 <span>ABSENSI</span>
             </li>
             <li class="menu-item {{ request()->is(['presensi', 'presensi/*', 'trackingpresensi', 'trackingpresensi/*', 'dispensasi', 'dispensasi/*']) ? 'open' : '' }}">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-map-pin-check"></i>
                     <div>Kehadiran & Absensi</div>
                 </a>
                 <ul class="menu-sub">
                     @can('presensi.index')
                         <li class="menu-item {{ request()->is(['presensi', 'presensi/*']) ? 'active' : '' }}">
                             <a href="{{ route('presensi.index') }}" class="menu-link">
                                 <div>Monitoring Presensi</div>
                             </a>
                         </li>
                     @endcan
                     @can('trackingpresensi.index')
                         <li class="menu-item {{ request()->is(['trackingpresensi', 'trackingpresensi/*']) ? 'active' : '' }}">
                             <a href="{{ route('trackingpresensi.index') }}" class="menu-link">
                                 <div>Live Tracking GPS</div>
                             </a>
                         </li>
                     @endcan
                     <li class="menu-item {{ request()->is(['dispensasi', 'dispensasi/*']) ? 'active' : '' }}">
                         <a href="{{ route('dispensasi.index') }}" class="menu-link">
                             <div>Dispensasi Terlambat</div>
                         </a>
                     </li>
                 </ul>
             </li>
         @endif

                 @endmodule

        <!-- 4. PENGAJUAN -->
        @module('leave')
         @if (auth()->user()->hasAnyPermission(['izinabsen.index', 'izinsakit.index', 'izincuti.index']))
             <li class="menu-header small text-uppercase px-3 py-2 text-white-50 mt-2" style="font-size: 10.5px; font-weight: 700; letter-spacing: 0.08em;">
                 <span>PENGAJUAN</span>
             </li>
             <li class="menu-item {{ request()->is(['izinabsen', 'izinabsen/*', 'izinsakit', 'izinsakit/*', 'izincuti', 'izincuti/*']) ? 'active' : '' }}">
                 <a href="{{ route('izinabsen.index') }}" class="menu-link">
                     <i class="menu-icon tf-icons ti ti-calendar-event"></i>
                     <div>Persetujuan Izin</div>
                     @php
                         $badge_izin = 0;
                         if (auth()->user()->can('izinabsen.index')) $badge_izin += ($notifikasi_izinabsen ?? 0);
                         if (auth()->user()->can('izinsakit.index')) $badge_izin += ($notifikasi_izinsakit ?? 0);
                         if (auth()->user()->can('izincuti.index')) $badge_izin += ($notifikasi_izincuti ?? 0);
                     @endphp
                     @if ($badge_izin > 0)
                         <div class="badge bg-danger rounded-pill ms-auto" style="font-size: 11px; padding: 2px 7px;">{{ $badge_izin }}</div>
                     @endif
                 </a>
             </li>
         @endif

                 @endmodule

        <!-- 4.5 KEUANGAN & PAYROLL -->
         @if (auth()->user()->hasAnyPermission(['payroll.index', 'salary_component.index', 'employee_salary.index', 'compliance.index', 'thr.index', 'payslip.index', 'reimbursement.index', 'loan.index']))
             @module('payroll')
                 <li class="menu-header small text-uppercase px-3 py-2 text-white-50 mt-2" style="font-size: 10.5px; font-weight: 700; letter-spacing: 0.08em;">
                     <span>KEUANGAN & PAYROLL</span>
                 </li>
                 <li class="menu-item {{ request()->is(['payroll', 'payroll/*', 'salary-components', 'salary-components/*', 'employee-salary', 'employee-salary/*', 'compliance', 'compliance/*', 'thr', 'thr/*', 'payslips', 'payslips/*', 'reimbursements', 'reimbursements/*', 'loans', 'loans/*']) ? 'open' : '' }}">
                     <a href="javascript:void(0);" class="menu-link menu-toggle">
                         <i class="menu-icon tf-icons ti ti-wallet"></i>
                         <div>Penggajian & Upah</div>
                     </a>
                     <ul class="menu-sub">
                         @can('payroll.index')
                             <li class="menu-item {{ request()->is(['payroll', 'payroll/*']) ? 'active' : '' }}">
                                 <a href="{{ route('payroll.index') }}" class="menu-link">
                                     <div>Periode Payroll</div>
                                 </a>
                             </li>
                         @endcan
                         @can('payslip.index')
                             <li class="menu-item {{ request()->is(['payslips', 'payslips/*']) ? 'active' : '' }}">
                                 <a href="{{ route('payslip.index') }}" class="menu-link">
                                     <div>Slip Gaji (Payslip)</div>
                                 </a>
                             </li>
                         @endcan
                         @can('employee_salary.index')
                             <li class="menu-item {{ request()->is(['employee-salary', 'employee-salary/*']) ? 'active' : '' }}">
                                 <a href="{{ route('employee_salary.index') }}" class="menu-link">
                                     <div>Struktur Gaji Karyawan</div>
                                 </a>
                             </li>
                         @endcan
                         @can('salary_component.index')
                             <li class="menu-item {{ request()->is(['salary-components', 'salary-components/*']) ? 'active' : '' }}">
                                 <a href="{{ route('salary_components.index') }}" class="menu-link">
                                     <div>Komponen Gaji</div>
                                 </a>
                             </li>
                         @endcan
                         @can('compliance.index')
                             <li class="menu-item {{ request()->is(['compliance', 'compliance/*']) ? 'active' : '' }}">
                                 <a href="{{ route('compliance.index') }}" class="menu-link">
                                     <div>Kepatuhan & Pajak TER</div>
                                 </a>
                             </li>
                         @endcan
                         @can('thr.index')
                             <li class="menu-item {{ request()->is(['thr', 'thr/*']) ? 'active' : '' }}">
                                 <a href="{{ route('thr.index') }}" class="menu-link">
                                     <div>THR Keagamaan</div>
                                 </a>
                             </li>
                         @endcan
                         @module('reimbursement')
@can('reimbursement.index')
                             <li class="menu-item {{ request()->is(['reimbursements', 'reimbursements/*']) ? 'active' : '' }}">
                                 <a href="{{ route('reimbursement.index') }}" class="menu-link">
                                     <div>Reimbursement & Klaim</div>
                                 </a>
                             </li>
                         @endcan
                    @endmodule
                         @module('loans')
@can('loan.index')
                             <li class="menu-item {{ request()->is(['loans', 'loans/*']) ? 'active' : '' }}">
                                 <a href="{{ route('loan.index') }}" class="menu-link">
                                     <div>Pinjaman & Kasbon</div>
                                 </a>
                             </li>
                         @endcan
                    @endmodule
                     </ul>
                 </li>
             @endmodule
         @endif

         <!-- 5. LAPORAN -->
         @php
             $showLaporanPresensi = module_enabled('attendance') && auth()->user()->can('laporan.presensi');
             $showLaporanCuti = module_enabled('leave') && auth()->user()->can('laporan.cuti');
             $showLaporanReports = auth()->user()->can('reports.index');
             $showLaporanSection = $showLaporanPresensi || $showLaporanCuti || $showLaporanReports;
         @endphp
         @if ($showLaporanSection)
             <li class="menu-header small text-uppercase px-3 py-2 text-white-50 mt-2" style="font-size: 10.5px; font-weight: 700; letter-spacing: 0.08em;">
                 <span>LAPORAN</span>
             </li>
             <li class="menu-item {{ request()->is(['laporan', 'laporan/*', 'reports', 'reports/*']) ? 'open' : '' }} ">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-file-analytics"></i>
                     <div>Rekap & Laporan</div>
                 </a>
                 <ul class="menu-sub">
                     @can('reports.index')
                         <li class="menu-item {{ request()->is(['reports', 'reports/*']) ? 'active' : '' }}">
                             <a href="{{ route('reports.index') }}" class="menu-link">
                                 <div>Laporan & Analitik HR</div>
                             </a>
                         </li>
                     @endcan
                     @module('attendance')
@can('laporan.presensi')
                         <li class="menu-item {{ request()->is(['laporan/presensi']) ? 'active' : '' }}">
                             <a href="{{ route('laporan.presensi') }}" class="menu-link">
                                 <div>Laporan Presensi (Excel)</div>
                             </a>
                         </li>
                     @endcan
                    @endmodule
                     @module('leave')
@can('laporan.cuti')
                         <li class="menu-item {{ request()->is(['laporan/cuti']) ? 'active' : '' }}">
                             <a href="{{ route('laporan.cuti') }}" class="menu-link">
                                 <div>Rekap Cuti Karyawan</div>
                             </a>
                         </li>
                     @endcan
                    @endmodule
                 </ul>
             </li>
         @endif

         <!-- 6. PENGATURAN SISTEM (Super Admin) -->
         @if (auth()->user()->hasRole('super admin'))
             <li class="menu-header small text-uppercase px-3 py-2 text-white-50 mt-2" style="font-size: 10.5px; font-weight: 700; letter-spacing: 0.08em;">
                 <span>PENGATURAN</span>
             </li>
             <li class="menu-item {{ request()->is(['generalsetting', 'generalsetting/*', 'settings/*', 'users', 'users/*']) ? 'open' : '' }}">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-settings"></i>
                     <div>Pengaturan Sistem</div>
                 </a>
                 <ul class="menu-sub">
                     @can('settings.index')
                         <li class="menu-item {{ request()->is(['settings']) ? 'active' : '' }}">
                             <a href="{{ route('settings.hub') }}" class="menu-link">
                                 <div>Pusat Direktori Pengaturan</div>
                             </a>
                         </li>
                     @endcan
                     @can('company_settings.index')
                         <li class="menu-item {{ request()->is(['settings/company', 'settings/company/*']) ? 'active' : '' }}">
                             <a href="{{ route('company_settings.index') }}" class="menu-link">
                                 <div>Profil Perusahaan & Branding</div>
                             </a>
                         </li>
                     @endcan
                     @can('module_features.index')
                         <li class="menu-item {{ request()->is(['settings/modules', 'settings/modules/*']) ? 'active' : '' }}">
                             <a href="{{ route('module_features.index') }}" class="menu-link">
                                 <div>Modul & Feature Flags</div>
                             </a>
                         </li>
                     @endcan
                      @module('attendance')
@can('attendance_policy.index')
                          <li class="menu-item {{ request()->is(['settings/attendance', 'settings/attendance/*']) ? 'active' : '' }}">
                              <a href="{{ route('attendance_policy.index') }}" class="menu-link">
                                  <div>Kebijakan Presensi</div>
                              </a>
                          </li>
                      @endcan
                    @endmodule
                      @module('overtime')
@can('overtime_policy.index')
                          <li class="menu-item {{ request()->is(['settings/overtime', 'settings/overtime/*']) ? 'active' : '' }}">
                              <a href="{{ route('overtime_policy.index') }}" class="menu-link">
                                  <div>Kebijakan Lembur</div>
                              </a>
                          </li>
                      @endcan
                    @endmodule
                     @if (auth()->user()->hasRole('super admin') && auth()->user()->can('generalsetting.index'))
                         <li class="menu-item {{ request()->is(['generalsetting', 'generalsetting/*']) ? 'active' : '' }}">
                             <a href="{{ route('generalsetting.index') }}" class="menu-link">
                                 <div>Pengaturan Operasional & GPS</div>
                             </a>
                         </li>
                     @endif
                     @if (auth()->user()->hasRole('super admin'))
                         <li class="menu-item {{ request()->is(['users', 'users/*']) ? 'active' : '' }}">
                             <a href="{{ route('users.index') }}" class="menu-link">
                                 <div>Manajemen Akun User</div>
                             </a>
                         </li>
                     @endif
                 </ul>
             </li>
         @endif

                   @can('audit_logs.index')
              <li class="menu-item {{ request()->is(['settings/audit-logs', 'settings/audit-logs/*']) ? 'active' : '' }}">
                  <a href="{{ route('settings.audit_logs.index') }}" class="menu-link">
                      <i class="menu-icon tf-icons ti ti-history"></i>
                      <div>Log Jejak Audit</div>
                  </a>
              </li>
          @endcan
          @if(!app(\App\Services\ModuleEntitlementService::class)->isDeploymentLocked())
              @can('presets.index')
                  <li class="menu-item {{ request()->is(['settings/presets', 'settings/presets/*']) ? 'active' : '' }}">
                      <a href="{{ route('settings.presets.index') }}" class="menu-link">
                          <i class="menu-icon tf-icons ti ti-layout-grid"></i>
                          <div>Matriks Preset Klien</div>
                      </a>
                  </li>
              @endcan
          @else
              @can('module_features.index')
                  <li class="menu-item {{ request()->is(['settings/package-info', 'settings/package-info/*']) ? 'active' : '' }}">
                      <a href="{{ route('settings.package_info.index') }}" class="menu-link">
                          <i class="menu-icon tf-icons ti ti-box"></i>
                          <div>Informasi Paket Lisensi</div>
                      </a>
                  </li>
              @endcan
          @endif

          <li class="menu-item mt-3 pt-2 border-top" style="border-color: rgba(255, 255, 255, 0.1) !important;">
             <a href="{{ route('help.index') }}" class="menu-link {{ request()->is('help') ? 'active' : '' }}">
                 <i class="menu-icon tf-icons ti ti-help"></i>
                 <div>Pusat Bantuan & Panduan</div>
             </a>
         </li>

         <li class="menu-item">
             <form method="POST" action="{{ route('logout') }}" id="formSidebarLogout" onsubmit="try { sessionStorage.removeItem('sidebar_scroll_pos'); } catch(e) {}">
                 @csrf
                 <a href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('formSidebarLogout').submit();" class="menu-link text-danger">
                     <i class="menu-icon tf-icons ti ti-logout"></i>
                     <div class="fw-semibold">Keluar / Log Out</div>
                 </a>
             </form>
         </li>
     </ul>
     <script>
         (function() {
             try {
                 var inner = document.querySelector('#layout-menu .menu-inner');
                 if (inner) {
                     var saved = sessionStorage.getItem('sidebar_scroll_pos');
                     if (saved !== null) {
                         inner.scrollTop = parseInt(saved, 10);
                     }
                     inner.addEventListener('scroll', function() {
                         sessionStorage.setItem('sidebar_scroll_pos', inner.scrollTop);
                     }, { passive: true });
                 }
             } catch (e) {}
         })();
     </script>
 </aside>
 <!-- / Menu -->
