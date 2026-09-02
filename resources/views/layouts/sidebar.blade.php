 <!-- Menu -->

 <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
     <div class="app-brand demo" style="height: 85px !important">
         <a href="{{ route('dashboard.index') }}" class="app-brand-link">
             <span class="app-brand-logo rounded-circle demo d-flex align-items-center justify-content-center"
                 style="background: var(--theme-color-2); width: 46px; height: 46px !important; overflow: hidden;">
                 @if (!empty($general_setting->logo) && Storage::disk('public')->exists('logo/' . $general_setting->logo))
                     <img src="{{ asset('storage/logo/' . $general_setting->logo) }}" alt="Logo" class="w-100 h-100"
                         style="object-fit: cover;">
                 @else
                     <img src="{{ asset('assets/login/images/logoweb-1.png') }}" alt="Logo" class="w-100 h-100"
                         style="object-fit: cover;">
                 @endif
             </span>
             <span class="app-brand-text demo menu-text fw-bold d-flex flex-column ms-2"
                 style="letter-spacing: 1px; color: #fff;">
                 <span style="font-size: 18px;">{{ $general_setting->nama_aplikasi ?? 'HR Presence' }}</span>
                 <small class="mt-1" style="font-size: 11px; letter-spacing: 0.5px; color: rgba(255, 255, 255, 0.7);">
                     Smart Attendance System
                 </small>
                 <small class="mt-1">Version 1.0 MVP</small>
             </span>
         </a>

         <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
             <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-top mb-4"></i>
             <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
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

     <div class="px-3 pb-3 py-3">
         <div class="d-flex align-items-center rounded-3 p-3 shadow-sm"
             style="background: var(--theme-color-2); border: 1px solid rgba(0,0,0,0.05);">
             <div class="flex-shrink-0 position-relative">
                 @if ($userPhoto)
                     <div class="rounded-circle border border-3 shadow"
                         style="width: 48px; height: 48px; background-image: url('{{ $userPhoto }}'); background-size: cover; background-position: center; border-color: rgba(0,0,0,0.08) !important;">
                     </div>
                 @else
                     <div class="rounded-circle d-flex align-items-center justify-content-center shadow"
                         style="width: 48px; height: 48px; font-size: 22px; background: #fff; color: var(--theme-color-2) !important;">
                         <i class="ti ti-user"></i>
                     </div>
                 @endif
             </div>
             <div class="flex-grow-1 ms-3">
                 <div class="fw-bold mb-0" style="font-size: 14px; color: #fff;">{{ $userName }}</div>
                 <small class="text-uppercase"
                     style="letter-spacing: 0.5px; font-size: 11px; color: rgba(255,255,255,0.8); font-weight: 500;">{{ $userRoleText }}</small>
             </div>
             <a href="{{ route('profile.editprofile') }}" class="btn btn-sm rounded-2 shadow-sm"
                 style="background: {{ $general_setting->theme_color_1 }}; border: none; color: #fff; padding: 6px 12px;"
                 data-bs-toggle="tooltip" title="Edit Profile">
                 <i class="ti ti-settings" style="font-size: 16px;"></i>
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

         <!-- Presensi & GPS (Monitoring, Tracking, Face Kiosk) -->
         @if (auth()->user()->hasAnyPermission(['presensi.index', 'trackingpresensi.index']))
             <li class="menu-item {{ request()->is(['presensi', 'presensi/*', 'trackingpresensi', 'trackingpresensi/*', 'facerecognition-presensi*']) ? 'open' : '' }}">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-fingerprint"></i>
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
                     <li class="menu-item {{ request()->is(['facerecognition-presensi', 'facerecognition-presensi/*']) ? 'active' : '' }}">
                         <a href="{{ route('facerecognition-presensi.index') }}" class="menu-link" target="_blank">
                             <div>Kiosk Face Recognition ↗</div>
                         </a>
                     </li>
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

         <!-- Konfigurasi Jam Kerja & Outlet -->
         @if (auth()->user()->hasAnyPermission(['generalsetting.index', 'jamkerja.index', 'harilibur.index']))
             <li class="menu-item {{ request()->is(['generalsetting', 'generalsetting/*', 'jamkerja', 'jamkerja/*', 'harilibur', 'harilibur/*']) ? 'open' : '' }}">
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
                     @can('jamkerja.index')
                         <li class="menu-item {{ request()->is(['jamkerja', 'jamkerja/*']) ? 'active' : '' }}">
                             <a href="{{ route('jamkerja.index') }}" class="menu-link">
                                 <div>Jam Kerja & Shift</div>
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

         <!-- User Management -->
         @if (auth()->user()->hasRole(['super admin']))
             <li class="menu-item {{ request()->is(['users', 'users/*', 'roles', 'roles/*']) ? 'open' : '' }} ">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-shield-lock"></i>
                     <div>Manajemen Akun</div>
                 </a>
                 <ul class="menu-sub">
                     <li class="menu-item {{ request()->is(['users', 'users/*']) ? 'active' : '' }}">
                         <a href="{{ route('users.index') }}" class="menu-link">
                             <div>Data User</div>
                         </a>
                     </li>
                     <li class="menu-item {{ request()->is(['roles', 'roles/*']) ? 'active' : '' }}">
                         <a href="{{ route('roles.index') }}" class="menu-link">
                             <div>Hak Akses / Role</div>
                         </a>
                     </li>
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
