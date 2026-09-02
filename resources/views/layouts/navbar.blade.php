<nav class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="ti ti-menu-2 ti-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <!-- Search -->
        <div class="navbar-nav align-items-center">
            <div class="nav-item navbar-search-wrapper mb-0">
                <a class="nav-item nav-link search-toggler d-flex align-items-center px-0" href="javascript:void(0);">
                    <i class="ti ti-search ti-md me-2"></i>
                    <span class="d-none d-md-inline-block text-muted">Search (Ctrl+/)</span>
                </a>
            </div>
        </div>
        <!-- /Search -->

        <ul class="navbar-nav flex-row align-items-center ms-auto">


            <!-- Quick links
            <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-2 me-xl-0">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                    aria-expanded="false">
                    <i class="ti ti-layout-grid-add ti-md"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end py-0">
                    <div class="dropdown-menu-header border-bottom">
                        <div class="dropdown-header d-flex align-items-center py-3">
                            <h5 class="text-body mb-0 me-auto">Shortcuts</h5>
                            <a href="javascript:void(0)" class="dropdown-shortcuts-add text-body" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="Add shortcuts"><i class="ti ti-sm ti-apps"></i></a>
                        </div>
                    </div>
                    <div class="dropdown-shortcuts-list scrollable-container">
                        <div class="row row-bordered overflow-visible g-0">
                            <div class="dropdown-shortcuts-item col">
                                <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                    <i class="ti ti-calendar fs-4"></i>
                                </span>
                                <a href="app-calendar.html" class="stretched-link">Calendar</a>
                                <small class="text-muted mb-0">Appointments</small>
                            </div>
                            <div class="dropdown-shortcuts-item col">
                                <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                    <i class="ti ti-file-invoice fs-4"></i>
                                </span>
                                <a href="app-invoice-list.html" class="stretched-link">Invoice
                                    App</a>
                                <small class="text-muted mb-0">Manage Accounts</small>
                            </div>
                        </div>
                        <div class="row row-bordered overflow-visible g-0">
                            <div class="dropdown-shortcuts-item col">
                                <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                    <i class="ti ti-users fs-4"></i>
                                </span>
                                <a href="app-user-list.html" class="stretched-link">User App</a>
                                <small class="text-muted mb-0">Manage Users</small>
                            </div>
                            <div class="dropdown-shortcuts-item col">
                                <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                    <i class="ti ti-lock fs-4"></i>
                                </span>
                                <a href="app-access-roles.html" class="stretched-link">Role
                                    Management</a>
                                <small class="text-muted mb-0">Permission</small>
                            </div>
                        </div>
                        <div class="row row-bordered overflow-visible g-0">
                            <div class="dropdown-shortcuts-item col">
                                <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                    <i class="ti ti-chart-bar fs-4"></i>
                                </span>
                                <a href="index.html" class="stretched-link">Dashboard</a>
                                <small class="text-muted mb-0">User Profile</small>
                            </div>
                            <div class="dropdown-shortcuts-item col">
                                <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                    <i class="ti ti-settings fs-4"></i>
                                </span>
                                <a href="pages-account-settings-account.html" class="stretched-link">Setting</a>
                                <small class="text-muted mb-0">Account Settings</small>
                            </div>
                        </div>
                        <div class="row row-bordered overflow-visible g-0">
                            <div class="dropdown-shortcuts-item col">
                                <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                    <i class="ti ti-help fs-4"></i>
                                </span>
                                <a href="pages-faq.html" class="stretched-link">FAQs</a>
                                <small class="text-muted mb-0">FAQs & Articles</small>
                            </div>
                            <div class="dropdown-shortcuts-item col">
                                <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                    <i class="ti ti-square fs-4"></i>
                                </span>
                                <a href="modal-examples.html" class="stretched-link">Modals</a>
                                <small class="text-muted mb-0">Useful Popups</small>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
             Quick links -->

            <!-- Notification -->
            @php
                $total_notif = ($notifikasi_ajuan_absen ?? 0) + ($notifikasi_reimbursement ?? 0) + (auth()->check() ? auth()->user()->unreadNotifications->where('type', '!=', 'App\Notifications\PengumumanNotification')->count() : 0);
            @endphp
            <li class="nav-item dropdown-notifications navbar-dropdown dropdown">
                <a class="nav-link dropdown-toggle hide-arrow position-relative" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                    aria-expanded="false" title="Notifikasi">
                    <i class="ti ti-bell ti-md"></i>
                    @if($total_notif > 0)
                        <span class="badge bg-danger rounded-pill badge-notifications">{{ $total_notif }}</span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end py-0" style="min-width: 330px;">
                    <li class="dropdown-menu-header border-bottom">
                        <div class="dropdown-header d-flex align-items-center py-3">
                            <h5 class="text-body mb-0 me-auto fs-6 fw-bold">Notifikasi</h5>
                            @if($total_notif > 0)
                                <span class="badge rounded-pill bg-label-primary font-mono">{{ $total_notif }} Baru</span>
                            @endif
                        </div>
                    </li>
                    <li class="dropdown-notifications-list scrollable-container">
                        <ul class="list-group list-group-flush">
                            @forelse (auth()->user()->unreadNotifications->where('type', '!=', 'App\Notifications\PengumumanNotification') as $notification)
                                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <span class="avatar-initial rounded-circle bg-label-primary"><i class="ti {{ $notification->data['icon'] ?? 'ti-bell' }}"></i></span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $notification->data['title'] ?? 'Notification' }}</h6>
                                            <p class="mb-0">{{ $notification->data['message'] ?? '' }}</p>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="{{ $notification->data['url'] ?? '#' }}" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                                        </div>
                                    </div>
                                </li>
                            @empty
                            @endforelse

                            @if(isset($data_izin) && count($data_izin) > 0)
                                @foreach ($data_izin as $d)
                                    @php
                                        $bgcolor = 'primary';
                                        $link = route('izinabsen.index');
                                        $keterangan = 'Izin Absen';
                                        if ($d->status == 'i') {
                                            $keterangan = 'Izin Absen';
                                            $bgcolor = 'info';
                                            $link = route('izinabsen.index');
                                        } elseif ($d->status == 's') {
                                            $keterangan = 'Izin Sakit';
                                            $bgcolor = 'warning';
                                            $link = route('izinsakit.index');
                                        } elseif ($d->status == 'c') {
                                            $keterangan = 'Izin Cuti';
                                            $bgcolor = 'success';
                                            $link = route('izincuti.index');
                                        } elseif ($d->status == 'd') {
                                            $keterangan = 'Izin Dinas';
                                            $bgcolor = 'primary';
                                            $link = route('izindinas.index');
                                        }
                                    @endphp
                                    <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar">
                                                    <span class="avatar-initial rounded-circle bg-label-{{ $bgcolor }} fw-bold">{{ textUpperCase($d->status) }}</span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <a href="{{ $link }}" class="stretched-link text-body fw-semibold">{{ $d->nama_karyawan }}</a>
                                                </h6>
                                                <p class="mb-0 text-muted" style="font-size: 12px;">Mengajukan {{ $keterangan }}</p>
                                                <small class="text-muted" style="font-size: 11px;">
                                                    {{ \Carbon\Carbon::parse($d->created_at)->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            @endif

                            @if(isset($data_reimbursement_pending) && count($data_reimbursement_pending) > 0)
                                @foreach ($data_reimbursement_pending as $dr)
                                    <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar">
                                                    <span class="avatar-initial rounded-circle bg-label-danger fw-bold">RM</span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <a href="{{ route('reimbursement.index') }}" class="stretched-link text-body fw-semibold">{{ $dr->nama_karyawan }}</a>
                                                </h6>
                                                <p class="mb-0 text-muted" style="font-size: 12px;">Pengajuan Reimbursement (Rp {{ number_format($dr->total_nominal, 0, ',', '.') }})</p>
                                                <small class="text-muted" style="font-size: 11px;">
                                                    {{ \Carbon\Carbon::parse($dr->created_at)->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            @endif

                            @if($total_notif == 0)
                                <li class="list-group-item text-center py-4">
                                    <i class="ti ti-bell-off text-muted mb-2 d-block" style="font-size: 2.5rem; opacity: 0.4;"></i>
                                    <p class="mb-0 fw-semibold text-dark" style="font-size: 13px;">Tidak ada notifikasi baru</p>
                                    <small class="text-muted">Semua pengajuan dan aktivitas sudah diperbarui.</small>
                                </li>
                            @endif
                        </ul>
                    </li>
                    <li class="dropdown-menu-footer border-top">
                        <a href="{{ route('izinabsen.index') }}"
                            class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40 align-items-center fw-semibold" style="font-size: 12.5px;">
                            Lihat Semua Pengajuan
                        </a>
                    </li>
                </ul>
            </li>
            <!--/ Notification -->
        </ul>
    </div>

    <!-- Search Small Screens -->
    <div class="navbar-search-wrapper search-input-wrapper d-none">
        <input type="text" class="form-control search-input container-fluid border-0" placeholder="Search..." aria-label="Search..." />
        <i class="ti ti-x ti-sm search-toggler cursor-pointer"></i>
    </div>
</nav>
