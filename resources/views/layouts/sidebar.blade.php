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
                     {{ $company_setting->app_tagline ?? 'Sistem HRIS' }}
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

         <!-- 2. KARYAWAN -->
        @if (auth()->user()->hasAnyPermission(['karyawan.index', 'departemen.index', 'jabatan.index', 'cabang.index']))
            <li class="menu-header small text-uppercase px-3 py-2 text-white-50 mt-2" style="font-size: 10.5px; font-weight: 700; letter-spacing: 0.08em;">
                <span>KARYAWAN</span>
            </li>
            <li class="menu-item {{ request()->is(['karyawan', 'karyawan/*', 'departemen', 'departemen/*', 'divisi', 'divisi/*', 'jabatan', 'jabatan/*', 'cabang', 'cabang/*']) ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-users"></i>
                    <div>Karyawan</div>
                </a>
                <ul class="menu-sub">
                    @can('karyawan.index')
                        <li class="menu-item {{ request()->is(['karyawan', 'karyawan/*']) ? 'active' : '' }}">
                            <a href="{{ route('karyawan.index') }}" class="menu-link">
                                <div>Data Karyawan</div>
                            </a>
                        </li>
                    @endcan
                    @can('departemen.index')
                        <li class="menu-item {{ request()->is(['departemen', 'departemen/*', 'divisi', 'divisi/*']) ? 'active' : '' }}">
                            <a href="{{ route('departemen.index') }}" class="menu-link">
                                <div>Departemen</div>
                            </a>
                        </li>
                    @endcan
                    @can('jabatan.index')
                        <li class="menu-item {{ request()->is(['jabatan', 'jabatan/*']) ? 'active' : '' }}">
                            <a href="{{ route('jabatan.index') }}" class="menu-link">
                                <div>Jabatan</div>
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
                </ul>
            </li>
        @endif

        <!-- 3. PRESENSI -->
        @module('attendance')
        @if (auth()->user()->hasAnyPermission(['presensi.index', 'trackingpresensi.index', 'dispensasi.index']))
            <li class="menu-header small text-uppercase px-3 py-2 text-white-50 mt-2" style="font-size: 10.5px; font-weight: 700; letter-spacing: 0.08em;">
                <span>PRESENSI</span>
            </li>
            <li class="menu-item {{ request()->is(['presensi', 'presensi/*', 'trackingpresensi', 'trackingpresensi/*', 'dispensasi', 'dispensasi/*']) ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-map-pin-check"></i>
                    <div>Presensi</div>
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
                    @can('dispensasi.index')
                        <li class="menu-item {{ request()->is(['dispensasi', 'dispensasi/*']) ? 'active' : '' }}">
                            <a href="{{ route('dispensasi.index') }}" class="menu-link">
                                <div>Dispensasi Terlambat</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif
        @endmodule

        <!-- 4. IZIN & CUTI -->
        @module('leave')
        @if (auth()->user()->hasAnyPermission(['izinabsen.index', 'izinsakit.index', 'izincuti.index', 'leave_quotas.index']))
            <li class="menu-header small text-uppercase px-3 py-2 text-white-50 mt-2" style="font-size: 10.5px; font-weight: 700; letter-spacing: 0.08em;">
                <span>IZIN & CUTI</span>
            </li>
            <li class="menu-item {{ request()->is(['izinabsen', 'izinabsen/*', 'izinsakit', 'izinsakit/*', 'izincuti', 'izincuti/*', 'leave-quotas', 'leave-quotas/*']) ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-calendar-event"></i>
                    <div>Izin & Cuti</div>
                    @php
                        $badge_izin = 0;
                        if (auth()->user()->can('izinabsen.index')) $badge_izin += ($notifikasi_izinabsen ?? 0);
                        if (auth()->user()->can('izinsakit.index')) $badge_izin += ($notifikasi_izinsakit ?? 0);
                        if (auth()->user()->can('izincuti.index')) $badge_izin += ($notifikasi_izincuti ?? 0);
                        if (auth()->user()->can('dispensasi.index')) $badge_izin += ($notifikasi_dispensasi ?? 0);
                    @endphp
                    @if ($badge_izin > 0)
                        <span class="badge bg-danger rounded-pill ms-auto sidebar-badge-counter" style="margin-right: 1.5rem !important; font-size: 11px; font-weight: 700; padding: 2px 7px; background-color: #E11D48 !important; color: #FFFFFF !important; border: none !important; line-height: 1.2;">{{ $badge_izin }}</span>
                    @endif
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is(['izinabsen', 'izinabsen/*', 'izinsakit', 'izinsakit/*', 'izincuti', 'izincuti/*']) ? 'active' : '' }}">
                        <a href="{{ route('izinabsen.index') }}" class="menu-link">
                            <div>Persetujuan Izin</div>
                        </a>
                    </li>
                    @can('leave_quotas.index')
                        <li class="menu-item {{ request()->is(['leave-quotas', 'leave-quotas/*']) ? 'active' : '' }}">
                            <a href="{{ route('leave_quotas.index') }}" class="menu-link">
                                <div>Saldo Kuota Cuti</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif
        @endmodule

        <!-- 5. LEMBUR -->
        @module('overtime')
        @can('overtime.index')
            <li class="menu-header small text-uppercase px-3 py-2 text-white-50 mt-2" style="font-size: 10.5px; font-weight: 700; letter-spacing: 0.08em;">
                <span>LEMBUR</span>
            </li>
            <li class="menu-item {{ request()->is(['overtime', 'overtime/*']) ? 'active' : '' }}">
                <a href="{{ route('overtime.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-clock-play"></i>
                    <div>Lembur & SPK</div>
                </a>
            </li>
        @endcan
        @endmodule

        <!-- 6. PAYROLL -->
        @module('payroll')
        @if (auth()->user()->hasAnyPermission(['payroll.index', 'payslip.index']))
            <li class="menu-header small text-uppercase px-3 py-2 text-white-50 mt-2" style="font-size: 10.5px; font-weight: 700; letter-spacing: 0.08em;">
                <span>PAYROLL</span>
            </li>
            <li class="menu-item {{ request()->is(['payroll', 'payroll/*', 'payslips', 'payslips/*', 'employee-salary', 'employee-salary/*', 'salary-components', 'salary-components/*', 'compliance', 'compliance/*', 'thr', 'thr/*']) ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-wallet"></i>
                    <div>Payroll</div>
                </a>
                <ul class="menu-sub">
                    @can('payroll.index')
                        <li class="menu-item {{ request()->is(['payroll', 'payroll/*', 'employee-salary', 'employee-salary/*', 'salary-components', 'salary-components/*', 'compliance', 'compliance/*', 'thr', 'thr/*']) ? 'active' : '' }}">
                            <a href="{{ route('payroll.index') }}" class="menu-link">
                                <div>Kelola Payroll</div>
                            </a>
                        </li>
                    @endcan
                    @can('payslip.index')
                        <li class="menu-item {{ request()->is(['payslips', 'payslips/*']) ? 'active' : '' }}">
                            <a href="{{ route('payslip.index') }}" class="menu-link">
                                <div>Slip Gaji</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif
        @endmodule

        <!-- 7. LAPORAN -->
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
            <li class="menu-item {{ request()->is(['laporan', 'laporan/*', 'reports', 'reports/*']) ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-file-analytics"></i>
                    <div>Laporan</div>
                </a>
                <ul class="menu-sub">
                    @can('laporan.presensi')
                        <li class="menu-item {{ request()->is(['laporan/presensi', 'laporan/presensi/*']) ? 'active' : '' }}">
                            <a href="{{ route('laporan.presensi') }}" class="menu-link">
                                <div>Laporan Presensi</div>
                            </a>
                        </li>
                    @endcan
                    @can('laporan.cuti')
                        <li class="menu-item {{ request()->is(['laporan/cuti', 'laporan/cuti/*']) ? 'active' : '' }}">
                            <a href="{{ route('laporan.cuti') }}" class="menu-link">
                                <div>Rekap Cuti</div>
                            </a>
                        </li>
                    @endcan
                    @can('reports.index')
                        <li class="menu-item {{ request()->is(['reports', 'reports/*']) ? 'active' : '' }}">
                            <a href="{{ route('reports.index') }}" class="menu-link">
                                <div>Laporan & Analitik HR</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        <!-- 8. PENGATURAN -->
        @if (auth()->user()->hasRole('super admin') || auth()->user()->hasAnyPermission(['jamkerja.index', 'harilibur.index', 'users.index']))
            <li class="menu-header small text-uppercase px-3 py-2 text-white-50 mt-2" style="font-size: 10.5px; font-weight: 700; letter-spacing: 0.08em;">
                <span>PENGATURAN</span>
            </li>
            <li class="menu-item {{ request()->is(['settings', 'settings/*', 'generalsetting*', 'jamkerja', 'jamkerja/*', 'harilibur', 'harilibur/*', 'users', 'users/*']) ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-settings"></i>
                    <div>Pengaturan</div>
                </a>
                <ul class="menu-sub">
                    @module('attendance')
                    @can('jamkerja.index')
                        <li class="menu-item {{ request()->is(['jamkerja', 'jamkerja/*']) ? 'active' : '' }}">
                            <a href="{{ route('jamkerja.index') }}" class="menu-link">
                                <div>Shift Kerja</div>
                            </a>
                        </li>
                    @endcan
                    @endmodule
                    @can('harilibur.index')
                        <li class="menu-item {{ request()->is(['harilibur', 'harilibur/*']) ? 'active' : '' }}">
                            <a href="{{ route('harilibur.index') }}" class="menu-link">
                                <div>Hari Libur</div>
                            </a>
                        </li>
                    @endcan
                    @if (auth()->user()->hasRole('super admin'))
                        <li class="menu-item {{ (request()->is(['settings', 'settings/company*', 'settings/modules*', 'settings/attendance*', 'settings/overtime*', 'generalsetting*']) && !request()->is(['settings/audit-logs*'])) ? 'active' : '' }}">
                            <a href="{{ route('settings.hub') }}" class="menu-link">
                                <div>Pengaturan Sistem</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->is(['users', 'users/*', 'permissions', 'permissions/*']) ? 'active' : '' }}">
                            <a href="{{ route('users.index') }}" class="menu-link">
                                <div>Manajemen Akun User</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
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
