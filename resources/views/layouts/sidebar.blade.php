 <!-- Menu -->

 <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
     <div class="app-brand demo px-3" style="height: 76px !important;">
         <a href="{{ route('dashboard.index') }}" class="app-brand-link d-flex align-items-center text-decoration-none">
             <span class="app-brand-logo rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                 style="background: rgba(255, 255, 255, 0.12); width: 40px; height: 40px !important; overflow: hidden; border: 1px solid rgba(255,255,255,0.18);">
                 @if (!empty($general_setting->logo) && Storage::disk('public')->exists('logo/' . $general_setting->logo))
                     <img src="{{ asset('storage/logo/' . $general_setting->logo) }}" alt="Logo" class="w-100 h-100"
                         style="object-fit: cover;">
                 @else
                     <img src="{{ asset('assets/login/images/logoweb-1.png') }}" alt="Logo" class="w-100 h-100"
                         style="object-fit: cover;">
                 @endif
             </span>
             <div class="d-flex flex-column ms-3">
                 <span class="fw-bold text-white lh-1" style="font-size: 15px; letter-spacing: -0.01em;">
                     {{ $general_setting->nama_aplikasi ?? 'HR Presence' }}
                 </span>
                 <span class="text-white-50 mt-1" style="font-size: 11px; letter-spacing: 0.02em;">
                     Smart Attendance System
                 </span>
             </div>
         </a>

         <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
             <i class="ti ti-x ti-sm align-middle text-white"></i>
         </a>
     </div>

     @php
         $authUser = auth()->user();
         $fullName = $authUser->name ?? 'Pengguna';
         $userName = explode(' ', $fullName)[0]; // Ambil nama depan saja
         $userEmail = $authUser->email ?? '-';
         $userRoleText = $authUser->getRoleNames()->first() ?? 'User';

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
     @endphp

     <div class="px-3 py-2 mb-2">
         <div class="d-flex align-items-center rounded-3 p-2.5"
             style="background: rgba(255, 255, 255, 0.07); border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(8px);">
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
         <!-- Dashboard -->
         <li class="menu-item {{ request()->is(['dashboard', 'dashboard/*']) ? 'active' : '' }}">
             <a href="{{ route('dashboard.index') }}" class="menu-link">
                 <i class="menu-icon tf-icons ti ti-home"></i>
                 <div>Dashboard</div>
             </a>
         </li>

         <!-- Data Master (Karyawan, Outlet, Departemen, Jabatan, Cuti) -->
         @if (auth()->user()->hasAnyPermission(['karyawan.index', 'departemen.index', 'cabang.index', 'cuti.index', 'jabatan.index']))
             <li class="menu-item {{ request()->is(['karyawan', 'karyawan/*', 'departemen', 'departemen/*', 'cabang', 'cabang/*', 'cuti', 'cuti/*', 'jabatan', 'jabatan/*']) ? 'open' : '' }}">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-users"></i>
                     <div>Manajemen Karyawan</div>
                 </a>
                 <ul class="menu-sub">
                     @can('karyawan.index')
                         <li class="menu-item {{ request()->is(['karyawan', 'karyawan/*']) ? 'active' : '' }}">
                             <a href="{{ route('karyawan.index') }}" class="menu-link">
                                 <div>Data Karyawan & Wajah</div>
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
                     @can('departemen.index')
                         <li class="menu-item {{ request()->is(['departemen', 'departemen/*']) ? 'active' : '' }}">
                             <a href="{{ route('departemen.index') }}" class="menu-link">
                                 <div>Divisi / Departemen</div>
                             </a>
                         </li>
                     @endcan
                     @can('cabang.index')
                         <li class="menu-item {{ request()->is(['cabang', 'cabang/*']) ? 'active' : '' }}">
                             <a href="{{ route('cabang.index') }}" class="menu-link">
                                 <div>Cabang / Outlet Coffee</div>
                             </a>
                         </li>
                     @endcan
                     @can('cuti.index')
                         <li class="menu-item {{ request()->is(['cuti', 'cuti/*']) ? 'active' : '' }}">
                             <a href="{{ route('cuti.index') }}" class="menu-link">
                                 <div>Jenis Cuti</div>
                             </a>
                         </li>
                     @endcan
                 </ul>
             </li>
         @endif

         <!-- Manajemen Shift & Jadwal Operasional (Coffee Shop Roster) -->
        @if (auth()->user()->hasAnyPermission(['jamkerja.index', 'jamkerjabydept.index', 'ajuanjadwal.index']))
            <li class="menu-item {{ request()->is(['jamkerja', 'jamkerja/*', 'jamkerjabydept', 'jamkerjabydept/*', 'ajuanjadwal', 'ajuanjadwal/*']) ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-clock-play"></i>
                    <div>Manajemen Shift</div>
                </a>
                <ul class="menu-sub">
                    @can('jamkerja.index')
                        <li class="menu-item {{ request()->is(['jamkerja', 'jamkerja/*']) ? 'active' : '' }}">
                            <a href="{{ route('jamkerja.index') }}" class="menu-link">
                                <div>Master Shift Kerja</div>
                            </a>
                        </li>
                    @endcan
                    @can('jamkerjabydept.index')
                        <li class="menu-item {{ request()->is(['jamkerjabydept', 'jamkerjabydept/*']) ? 'active' : '' }}">
                            <a href="{{ route('jamkerjabydept.index') }}" class="menu-link">
                                <div>Jadwal Shift Departemen</div>
                            </a>
                        </li>
                    @endcan
                    @can('ajuanjadwal.index')
                        <li class="menu-item {{ request()->is(['ajuanjadwal', 'ajuanjadwal/*']) ? 'active' : '' }}">
                            <a href="{{ route('ajuanjadwal.index') }}" class="menu-link">
                                <div>Pengajuan Tukar Shift</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        <!-- Presensi & GPS (Monitoring, Tracking) -->
         @if (auth()->user()->hasAnyPermission(['presensi.index', 'trackingpresensi.index']))
             <li class="menu-item {{ request()->is(['presensi', 'presensi/*', 'trackingpresensi', 'trackingpresensi/*']) ? 'open' : '' }}">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-map-pin-check"></i>
                     <div>Presensi & GPS</div>
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
                 </ul>
             </li>
         @endif

         <!-- Pengajuan & Persetujuan Izin (Absen, Sakit + Gambar Bukti, Cuti) -->
         @if (auth()->user()->hasAnyPermission(['izinabsen.index', 'izinsakit.index', 'izincuti.index']))
             <li class="menu-item {{ request()->is(['izinabsen', 'izinabsen/*', 'izinsakit', 'izinsakit/*', 'izincuti', 'izincuti/*']) ? 'active' : '' }}">
                 <a href="{{ route('izinabsen.index') }}" class="menu-link">
                     <i class="menu-icon tf-icons ti ti-calendar-event"></i>
                     <div>Persetujuan Izin</div>
                     @if (!empty($notifikasi_ajuan_absen))
                         <div class="badge bg-danger rounded-pill ms-auto">{{ $notifikasi_ajuan_absen }}</div>
                     @endif
                 </a>
             </li>
         @endif

         <!-- Rekap & Laporan Absensi (Excel) -->
         @if (auth()->user()->hasAnyPermission(['laporan.presensi', 'laporan.cuti']))
             <li class="menu-item {{ request()->is(['laporan', 'laporan/*']) ? 'open' : '' }} ">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-file-analytics"></i>
                     <div>Rekap & Laporan</div>
                 </a>
                 <ul class="menu-sub">
                     @can('laporan.presensi')
                         <li class="menu-item {{ request()->is(['laporan/presensi']) ? 'active' : '' }}">
                             <a href="{{ route('laporan.presensi') }}" class="menu-link">
                                 <div>Laporan Presensi (Excel)</div>
                             </a>
                         </li>
                     @endcan
                     @can('laporan.cuti')
                         <li class="menu-item {{ request()->is(['laporan/cuti']) ? 'active' : '' }}">
                             <a href="{{ route('laporan.cuti') }}" class="menu-link">
                                 <div>Rekap Cuti Karyawan</div>
                             </a>
                         </li>
                     @endcan
                 </ul>
             </li>
         @endif

        <!-- Konfigurasi Outlet -->
        @if (auth()->user()->hasAnyPermission(['generalsetting.index', 'harilibur.index']))
            <li class="menu-item {{ request()->is(['generalsetting', 'generalsetting/*', 'harilibur', 'harilibur/*']) ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-settings"></i>
                    <div>Pengaturan Outlet</div>
                </a>
                <ul class="menu-sub">
                    @can('generalsetting.index')
                        <li class="menu-item {{ request()->is(['generalsetting', 'generalsetting/*']) ? 'active' : '' }}">
                            <a href="{{ route('generalsetting.index') }}" class="menu-link">
                                <div>Pengaturan Umum & GPS</div>
                            </a>
                        </li>
                    @endcan
                    @can('harilibur.index')
                        <li class="menu-item {{ request()->is(['harilibur', 'harilibur/*']) ? 'active' : '' }}">
                            <a href="{{ route('harilibur.index') }}" class="menu-link">
                                <div>Hari Libur Outlet</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

         <li class="menu-item mt-3">
             <form method="POST" action="{{ route('logout') }}" id="formSidebarLogout">
                 @csrf
                 <a href="#" onclick="event.preventDefault(); document.getElementById('formSidebarLogout').submit();" class="menu-link text-danger">
                     <i class="menu-icon tf-icons ti ti-logout"></i>
                     <div>Keluar / Log Out</div>
                 </a>
             </form>
         </li>
     </ul>
 </aside>
 <!-- / Menu -->
