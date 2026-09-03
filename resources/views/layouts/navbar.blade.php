<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar"
    style="height: 62px !important; min-height: 62px !important; padding: 0 14px !important; display: flex !important; align-items: center !important;">
    <div class="layout-menu-toggle navbar-nav align-items-center me-0 d-xl-none" style="width: 38px; height: 38px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
        <a class="nav-item nav-link text-dark d-flex align-items-center justify-content-center p-0 m-0" href="javascript:void(0)"
            style="background: #F8FAFC; border: 1px solid #E2E8F0; width: 38px; height: 38px; border-radius: 10px; transition: all 0.2s ease;">
            <i class="ti ti-menu-2" style="font-size: 18px; color: #475569;"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center justify-content-between flex-grow-1" id="navbar-collapse" style="min-width: 0; height: 38px;">
        @php
            $searchFeatures = [
                [
                    'name' => 'Dashboard',
                    'url' => route('dashboard.index'),
                    'icon' => 'ti-home',
                    'category' => 'Dashboard',
                    'keywords' => 'dashboard beranda home utama'
                ],
            ];
            if (auth()->user()->can('karyawan.index')) {
                $searchFeatures[] = [
                    'name' => 'Data Karyawan & Wajah',
                    'url' => route('karyawan.index'),
                    'icon' => 'ti-users',
                    'category' => 'Manajemen Karyawan',
                    'keywords' => 'karyawan data pegawai staff wajah foto nik'
                ];
            }
            if (auth()->user()->can('jabatan.index')) {
                $searchFeatures[] = [
                    'name' => 'Jabatan / Posisi',
                    'url' => route('jabatan.index'),
                    'icon' => 'ti-id',
                    'category' => 'Manajemen Karyawan',
                    'keywords' => 'jabatan posisi role pangkat'
                ];
            }
            if (auth()->user()->can('departemen.index')) {
                $searchFeatures[] = [
                    'name' => 'Divisi / Departemen',
                    'url' => route('departemen.index'),
                    'icon' => 'ti-building',
                    'category' => 'Manajemen Karyawan',
                    'keywords' => 'divisi departemen unit bagian'
                ];
            }
            if (auth()->user()->can('cabang.index')) {
                $searchFeatures[] = [
                    'name' => 'Cabang / Outlet Coffee',
                    'url' => route('cabang.index'),
                    'icon' => 'ti-coffee',
                    'category' => 'Manajemen Karyawan',
                    'keywords' => 'cabang outlet toko coffee shop lokasi'
                ];
            }
            if (auth()->user()->can('cuti.index')) {
                $searchFeatures[] = [
                    'name' => 'Jenis Cuti',
                    'url' => route('cuti.index'),
                    'icon' => 'ti-calendar-off',
                    'category' => 'Manajemen Karyawan',
                    'keywords' => 'jenis cuti tahunan libur'
                ];
            }
            if (auth()->user()->can('jamkerja.index')) {
                $searchFeatures[] = [
                    'name' => 'Master Shift Kerja',
                    'url' => route('jamkerja.index'),
                    'icon' => 'ti-clock',
                    'category' => 'Manajemen Shift',
                    'keywords' => 'master shift jam kerja jadwal masuk pulang roster'
                ];
            }
            if (auth()->user()->can('jamkerjabydept.index')) {
                $searchFeatures[] = [
                    'name' => 'Jadwal Shift Departemen',
                    'url' => route('jamkerjabydept.index'),
                    'icon' => 'ti-clock-edit',
                    'category' => 'Manajemen Shift',
                    'keywords' => 'jadwal shift departemen divisi kelompok grup roster'
                ];
            }
            if (auth()->user()->can('ajuanjadwal.index')) {
                $searchFeatures[] = [
                    'name' => 'Pengajuan Tukar Shift',
                    'url' => route('ajuanjadwal.index'),
                    'icon' => 'ti-calendar-plus',
                    'category' => 'Manajemen Shift',
                    'keywords' => 'pengajuan tukar ganti shift ajuan jadwal'
                ];
            }
            if (auth()->user()->can('presensi.index')) {
                $searchFeatures[] = [
                    'name' => 'Monitoring Presensi',
                    'url' => route('presensi.index'),
                    'icon' => 'ti-map-pin-check',
                    'category' => 'Presensi & GPS',
                    'keywords' => 'monitoring presensi absen hari ini absensi gps peta'
                ];
            }
            if (auth()->user()->can('trackingpresensi.index')) {
                $searchFeatures[] = [
                    'name' => 'Live Tracking GPS',
                    'url' => route('trackingpresensi.index'),
                    'icon' => 'ti-radar',
                    'category' => 'Presensi & GPS',
                    'keywords' => 'live tracking gps peta maps lokasi lacak rute'
                ];
            }
            if (auth()->user()->hasAnyPermission(['izinabsen.index', 'izinsakit.index', 'izincuti.index'])) {
                $searchFeatures[] = [
                    'name' => 'Persetujuan Izin',
                    'url' => route('izinabsen.index'),
                    'icon' => 'ti-calendar-event',
                    'category' => 'Persetujuan Izin',
                    'keywords' => 'persetujuan izin absen sakit cuti permohonan pengajuan'
                ];
            }
            if (auth()->user()->can('laporan.presensi')) {
                $searchFeatures[] = [
                    'name' => 'Laporan Presensi (Excel)',
                    'url' => route('laporan.presensi'),
                    'icon' => 'ti-file-analytics',
                    'category' => 'Rekap & Laporan',
                    'keywords' => 'laporan presensi excel cetak rekap hadir download'
                ];
            }
            if (auth()->user()->can('laporan.cuti')) {
                $searchFeatures[] = [
                    'name' => 'Rekap Cuti Karyawan',
                    'url' => route('laporan.cuti'),
                    'icon' => 'ti-file-report',
                    'category' => 'Rekap & Laporan',
                    'keywords' => 'rekap cuti karyawan sisa cuti laporan'
                ];
            }
            if (auth()->user()->can('generalsetting.index')) {
                $searchFeatures[] = [
                    'name' => 'Pengaturan Umum & GPS',
                    'url' => route('generalsetting.index'),
                    'icon' => 'ti-settings',
                    'category' => 'Pengaturan Outlet',
                    'keywords' => 'pengaturan umum gps setting radius lokasi outlet jam'
                ];
            }
            if (auth()->user()->can('harilibur.index')) {
                $searchFeatures[] = [
                    'name' => 'Hari Libur Outlet',
                    'url' => route('harilibur.index'),
                    'icon' => 'ti-calendar-event',
                    'category' => 'Pengaturan Outlet',
                    'keywords' => 'hari libur outlet kalender nasional tanggal merah'
                ];
            }
            if (auth()->user()->hasRole(['super admin'])) {
                $searchFeatures[] = [
                    'name' => 'Data User',
                    'url' => route('users.index'),
                    'icon' => 'ti-user-cog',
                    'category' => 'Manajemen Akun',
                    'keywords' => 'data user pengguna akun login admin'
                ];

            }
        @endphp

        <!-- Unified Single Global Search Box in Navbar (Only Sidebar Features) -->
        <div class="navbar-nav align-items-center justify-content-center flex-grow-1 mx-2" style="min-width: 0;">
            <div class="nav-item position-relative mb-0 w-100" id="navbarSearchWrapper">
                <div class="d-flex align-items-center px-3"
                    style="height: 38px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; transition: all 0.2s ease;">
                    <i class="ti ti-search text-muted me-2" style="font-size: 15px; flex-shrink: 0;"></i>
                    <input type="text" id="navbarSearchInput"
                        placeholder="Cari fitur..."
                        style="border: none !important; outline: none !important; background: transparent !important; box-shadow: none !important; font-size: 13px; color: #1E293B; width: 100%; padding: 0 !important; margin: 0 !important; height: 38px !important; line-height: 38px !important;"
                        autocomplete="off">
                    <span class="text-muted font-mono ms-2 d-none d-md-inline" style="font-size: 10px; opacity: 0.6; white-space: nowrap; flex-shrink: 0;">Ctrl+/</span>
                </div>

                <!-- Dropdown results attached directly beneath the input -->
                <div id="navbarSearchResults" class="shadow-lg p-1.5 border-0"
                    style="display: none; position: absolute; top: calc(100% + 6px); left: 0; width: 100%; min-width: 280px; max-width: min(380px, 90vw); max-height: 360px; overflow-y: auto; border-radius: 12px; z-index: 99999; background: #FFFFFF; border: 1px solid #E2E8F0 !important; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.12), 0 8px 10px -6px rgba(0,0,0,0.08);">
                    
                    <div id="navbarSearchHint" class="px-2.5 py-1 text-muted d-flex align-items-center justify-content-between" style="font-size: 10.5px; border-bottom: 1px solid #F1F5F9; margin-bottom: 4px;">
                        <span><i class="ti ti-layout-sidebar me-1"></i>Fitur Sidebar</span>
                        <span class="font-mono text-muted">{{ count($searchFeatures) }} Menu</span>
                    </div>

                    @foreach ($searchFeatures as $item)
                        <a href="{{ $item['url'] }}" class="navbar-search-result-item d-flex align-items-center justify-content-between p-2 rounded-2 text-decoration-none text-dark"
                            data-name="{{ strtolower($item['name']) }}"
                            data-keywords="{{ strtolower($item['keywords']) }}"
                            data-category="{{ strtolower($item['category']) }}"
                            style="transition: background 0.15s ease; cursor: pointer;">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 32px; height: 32px; background: rgba(30, 77, 62, 0.08); color: #1E4D3E;">
                                    <i class="ti {{ $item['icon'] }}" style="font-size: 17px;"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark search-item-title" style="font-size: 12.5px; line-height: 1.2;">{{ $item['name'] }}</div>
                                    <span class="badge bg-light text-muted font-mono mt-0.5" style="font-size: 9.5px; padding: 1px 5px; border: 1px solid #E2E8F0;">
                                        {{ $item['category'] }}
                                    </span>
                                </div>
                            </div>
                            <i class="ti ti-chevron-right text-muted" style="font-size: 13px;"></i>
                        </a>
                    @endforeach

                    <div id="navbarSearchEmpty" class="text-center py-3" style="display: none;">
                        <i class="ti ti-search-off text-muted mb-1" style="font-size: 20px; opacity: 0.6;"></i>
                        <div class="text-muted fw-semibold" style="font-size: 12px;">Menu tidak ditemukan di sidebar</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Search -->

        <ul class="navbar-nav flex-row align-items-center m-0 p-0" style="width: 38px; height: 38px; flex-shrink: 0; justify-content: center;">
            <!-- Notification -->
            @php
                $total_notif = ($notifikasi_ajuan_absen ?? 0) + ($notifikasi_reimbursement ?? 0) + ($notifikasi_unread ?? 0);
            @endphp
            <li class="nav-item dropdown-notifications navbar-dropdown dropdown d-flex align-items-center justify-content-center">
                <a class="nav-link dropdown-toggle hide-arrow position-relative d-flex align-items-center justify-content-center p-0 m-0" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                    aria-expanded="false" title="Notifikasi" style="background: #F8FAFC; border: 1px solid #E2E8F0; width: 38px; height: 38px; border-radius: 10px; transition: all 0.2s ease;">
                    <i class="ti ti-bell" style="font-size: 18px; color: #475569;"></i>
                    @if($total_notif > 0)
                        <span class="badge bg-danger rounded-pill position-absolute" style="top: -4px; right: -4px; font-size: 10px; padding: 2px 5px;">{{ $total_notif }}</span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end py-0 shadow-lg" style="min-width: 340px; border-radius: 16px; border: 1px solid rgba(15,23,42,0.08); overflow: hidden;">
                    <li class="dropdown-menu-header border-bottom" style="background: #F8FAFC;">
                        <div class="dropdown-header d-flex align-items-center py-3 px-3">
                            <h6 class="text-dark mb-0 me-auto fw-bold" style="font-size: 14px;">Notifikasi</h6>
                            @if($total_notif > 0)
                                <span class="badge rounded-pill bg-label-primary font-mono" style="font-size: 11px;">{{ $total_notif }} Baru</span>
                            @endif
                        </div>
                    </li>
                    <li class="dropdown-notifications-list scrollable-container" style="max-height: 360px; overflow-y: auto;">
                        <ul class="list-group list-group-flush">
                            @forelse ($notifications_list ?? [] as $notification)
                                <li class="list-group-item list-group-item-action dropdown-notifications-item p-3 border-bottom">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar avatar-sm">
                                                <span class="avatar-initial rounded-circle bg-label-primary"><i class="ti {{ $notification->data['icon'] ?? 'ti-bell' }} fs-6"></i></span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-semibold text-dark" style="font-size: 13px;">{{ $notification->data['title'] ?? 'Notification' }}</h6>
                                            <p class="mb-1 text-muted" style="font-size: 12px; line-height: 1.4;">{{ $notification->data['message'] ?? '' }}</p>
                                            <small class="text-muted font-mono" style="font-size: 10.5px;">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions ms-2">
                                            <a href="{{ $notification->data['url'] ?? '#' }}" class="dropdown-notifications-read"><span class="badge badge-dot bg-primary"></span></a>
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
                                    <li class="list-group-item list-group-item-action dropdown-notifications-item p-3 border-bottom">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar avatar-sm">
                                                    <span class="avatar-initial rounded-circle bg-label-{{ $bgcolor }} fw-bold" style="font-size: 11px;">{{ textUpperCase($d->status) }}</span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <a href="{{ $link }}" class="stretched-link text-dark fw-semibold text-decoration-none" style="font-size: 13px;">{{ $d->nama_karyawan }}</a>
                                                </h6>
                                                <p class="mb-1 text-muted" style="font-size: 12px;">Mengajukan <span class="fw-semibold text-dark">{{ $keterangan }}</span></p>
                                                <small class="text-muted font-mono" style="font-size: 10.5px;">
                                                    {{ \Carbon\Carbon::parse($d->created_at)->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            @endif

                            @if(isset($data_reimbursement_pending) && count($data_reimbursement_pending) > 0)
                                @foreach ($data_reimbursement_pending as $dr)
                                    <li class="list-group-item list-group-item-action dropdown-notifications-item p-3 border-bottom">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar avatar-sm">
                                                    <span class="avatar-initial rounded-circle bg-label-danger fw-bold" style="font-size: 10px;">RM</span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <a href="{{ route('reimbursement.index') }}" class="stretched-link text-dark fw-semibold text-decoration-none" style="font-size: 13px;">{{ $dr->nama_karyawan }}</a>
                                                </h6>
                                                <p class="mb-1 text-muted" style="font-size: 12px;">Pengajuan Reimbursement <span class="fw-bold font-mono text-dark">(Rp {{ number_format($dr->total_nominal, 0, ',', '.') }})</span></p>
                                                <small class="text-muted font-mono" style="font-size: 10.5px;">
                                                    {{ \Carbon\Carbon::parse($dr->created_at)->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            @endif

                            @if($total_notif == 0)
                                <li class="list-group-item text-center py-4">
                                    <i class="ti ti-bell-off text-muted mb-2 d-block" style="font-size: 2.2rem; opacity: 0.35;"></i>
                                    <p class="mb-0 fw-semibold text-dark" style="font-size: 13px;">Tidak ada notifikasi baru</p>
                                    <small class="text-muted">Semua pengajuan dan aktivitas sudah diperbarui.</small>
                                </li>
                            @endif
                        </ul>
                    </li>
                    <li class="dropdown-menu-footer border-top" style="background: #F8FAF8; border-color: #E2E8F0 !important;">
                        <a href="{{ route('izinabsen.index') }}"
                            class="dropdown-item d-flex justify-content-center p-2.5 h-px-40 align-items-center fw-semibold text-decoration-none" style="font-size: 12.5px; color: #1E4D3E !important;">
                            Lihat Semua Pengajuan <i class="ti ti-arrow-right ms-1"></i>
                        </a>
                    </li>
                </ul>
            </li>
            <!--/ Notification -->
        </ul>
    </div>

</nav>

<style>
    .navbar-search-result-item:hover, .navbar-search-result-item.active-item {
        background: #F1F5F9 !important;
    }
    .navbar-search-result-item:hover .search-item-title, .navbar-search-result-item.active-item .search-item-title {
        color: #1E4D3E !important;
    }
    .navbar-search-result-item:hover .ti-chevron-right, .navbar-search-result-item.active-item .ti-chevron-right {
        color: #1E4D3E !important;
        transform: translateX(2px);
    }
</style>

<script>
    (function() {
        const input = document.getElementById('navbarSearchInput');
        const results = document.getElementById('navbarSearchResults');
        const empty = document.getElementById('navbarSearchEmpty');
        const wrapper = document.getElementById('navbarSearchWrapper');
        if (!input || !results) return;

        let activeIndex = -1;

        function filterNavSearch(q) {
            q = (q || '').trim().toLowerCase();
            const items = results.querySelectorAll('.navbar-search-result-item');
            let visible = 0;

            items.forEach(item => {
                const name = item.getAttribute('data-name') || '';
                const keywords = item.getAttribute('data-keywords') || '';
                const category = item.getAttribute('data-category') || '';

                if (!q || name.includes(q) || keywords.includes(q) || category.includes(q)) {
                    item.style.display = 'flex';
                    visible++;
                } else {
                    item.style.display = 'none';
                    item.classList.remove('active-item');
                }
            });

            if (empty) {
                empty.style.display = (visible === 0) ? 'block' : 'none';
            }

            // Always display dropdown container cleanly
            results.style.display = 'block';

            // Select first visible
            activeIndex = -1;
            const visibleItems = Array.from(items).filter(el => el.style.display !== 'none');
            if (visibleItems.length > 0 && q.length > 0) {
                activeIndex = 0;
                visibleItems[0].classList.add('active-item');
            }
        }

        input.addEventListener('focus', function() {
            filterNavSearch(this.value);
        });

        input.addEventListener('input', function() {
            filterNavSearch(this.value);
        });

        // Keyboard navigation: ArrowDown, ArrowUp, Enter, Escape
        input.addEventListener('keydown', function(e) {
            const items = results.querySelectorAll('.navbar-search-result-item');
            const visibleItems = Array.from(items).filter(el => el.style.display !== 'none');

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (!visibleItems.length) return;
                if (activeIndex < visibleItems.length - 1) {
                    if (activeIndex >= 0 && visibleItems[activeIndex]) visibleItems[activeIndex].classList.remove('active-item');
                    activeIndex++;
                    visibleItems[activeIndex].classList.add('active-item');
                    visibleItems[activeIndex].scrollIntoView({ block: 'nearest' });
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (!visibleItems.length) return;
                if (activeIndex > 0) {
                    if (visibleItems[activeIndex]) visibleItems[activeIndex].classList.remove('active-item');
                    activeIndex--;
                    visibleItems[activeIndex].classList.add('active-item');
                    visibleItems[activeIndex].scrollIntoView({ block: 'nearest' });
                }
            } else if (e.key === 'Enter') {
                e.preventDefault();
                let targetUrl = null;
                if (activeIndex >= 0 && visibleItems[activeIndex]) {
                    targetUrl = visibleItems[activeIndex].getAttribute('href');
                } else if (visibleItems.length > 0) {
                    targetUrl = visibleItems[0].getAttribute('href');
                }
                if (targetUrl) {
                    results.style.display = 'none';
                    input.blur();
                    if (window.startPageProgress) window.startPageProgress();
                    window.location.href = targetUrl;
                }
            } else if (e.key === 'Escape') {
                results.style.display = 'none';
                input.blur();
            }
        });

        // Click outside closes dropdown
        document.addEventListener('click', function(e) {
            if (!wrapper.contains(e.target)) {
                results.style.display = 'none';
            }
        });

        // Click on item navigates and closes dropdown
        results.addEventListener('click', function(e) {
            const item = e.target.closest('.navbar-search-result-item');
            if (item) {
                e.preventDefault();
                results.style.display = 'none';
                input.value = '';
                const url = item.getAttribute('href');
                if (window.startPageProgress) window.startPageProgress();
                window.location.href = url;
            }
        });

        // Ctrl+/ shortcut focuses the input directly
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && (e.key === '/' || e.code === 'Slash')) {
                e.preventDefault();
                input.focus();
                input.select();
                filterNavSearch(input.value);
            }
        });
    })();
</script>
