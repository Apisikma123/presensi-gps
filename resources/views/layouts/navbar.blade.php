<nav class="layout-navbar navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar"
    style="height: 62px !important; min-height: 62px !important; display: flex !important; align-items: center !important; overflow: visible !important;">
    
    <!-- Mobile Menu Toggle Button -->
    <div class="layout-menu-toggle navbar-nav align-items-center me-2 d-xl-none" style="width: 38px; height: 38px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
        <a class="nav-item nav-link text-dark d-flex align-items-center justify-content-center p-0 m-0" href="javascript:void(0)"
            style="background: #F8FAFC; border: 1px solid #E2E8F0; width: 38px; height: 38px; border-radius: 10px; transition: all 0.2s ease;"
            aria-label="Toggle Navigation">
            <i class="ti ti-menu-2" style="font-size: 18px; color: #475569;"></i>
        </a>
    </div>

    @php
        $searchFeatures = \App\Services\NavigationService::getSearchableFeatures();
    @endphp

    <!-- Topbar Center/Left: Global Feature Search -->
    <div class="navbar-nav-right d-flex align-items-center justify-content-between flex-grow-1" id="navbar-collapse" style="min-width: 0; height: 38px; overflow: visible !important;">
        
        <!-- Search Bar (Visible on All Devices: Desktop, Tablet & Mobile) -->
        <div class="d-flex align-items-center flex-grow-1 me-2" style="max-width: 480px; min-width: 0;">
            <div class="navbar-search-wrapper position-relative flex-grow-1" id="navbarSearchWrapper" style="min-width: 80px;">
                <div class="input-group input-group-merge" style="height: 38px;">
                    <span class="input-group-text bg-white border-end-0 py-0 ps-2.5 ps-sm-3" style="border-color: #E2E8F0; border-radius: 10px 0 0 10px;">
                        <i class="ti ti-search text-muted" style="font-size: 15px;"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 border-end-0 py-0 text-dark bg-white font-sans" id="navbarSearchInput"
                        placeholder="Cari fitur..." autocomplete="off"
                        style="height: 38px; border-color: #E2E8F0; font-size: 13px; font-weight: 500; box-shadow: none;" />
                    <span class="input-group-text bg-white border-start-0 py-0 pe-2" style="border-color: #E2E8F0; border-radius: 0 10px 10px 0;">
                        <button type="button" class="p-0 border-0 text-muted d-none me-1" id="navbarSearchClear" aria-label="Clear Search" style="line-height: 1; background: transparent !important; background-color: transparent !important; box-shadow: none !important; outline: none !important; width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">
                            <i class="ti ti-x" style="font-size: 14px; color: #94A3B8;"></i>
                        </button>
                        <kbd class="navbar-search-kbd d-none d-sm-inline-block font-mono" style="font-size: 10px; padding: 2px 5px; background: #F1F5F9; color: #64748B; border: 1px solid #CBD5E1; border-radius: 5px; font-weight: 600;">Ctrl K</kbd>
                    </span>
                </div>

                <!-- Floating Search Results Dropdown (Shared for Desktop & Mobile) -->
                <div class="navbar-search-results shadow-lg" id="navbarSearchResults"
                    style="display: none; position: absolute; top: calc(100% + 8px); left: 0; width: 100%; min-width: 320px; max-width: 480px; max-height: 400px; overflow-y: auto; background: #FFFFFF; border: 1px solid rgba(15,23,42,0.1); border-radius: 14px; z-index: 1090;">
                    
                    <!-- Search Header / Query Info -->
                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom" style="background: #FAF9F8;">
                        <span class="text-muted font-mono" style="font-size: 11px;"><i class="ti ti-layout-grid me-1"></i>Fitur & Menu Sistem</span>
                        <span class="badge rounded-pill bg-label-primary font-mono" style="font-size: 10px;" id="navbarSearchCount">{{ count($searchFeatures) }} Menu</span>
                    </div>

                    <!-- Search Results Content List -->
                    <div id="navbarSearchList" class="p-1">
                        @foreach ($searchFeatures as $f)
                            <a href="{{ $f['url'] }}" class="navbar-search-item"
                                data-name="{{ strtolower($f['name']) }}"
                                data-keywords="{{ strtolower($f['keywords']) }}"
                                data-category="{{ strtolower($f['category']) }}">
                                <div class="d-flex align-items-center justify-content-center rounded-circle me-2.5 flex-shrink-0" style="width: 32px; height: 32px; background: rgba(var(--bs-primary-rgb), 0.08); color: var(--theme-color-1, #3C2A21);">
                                    <i class="ti {{ $f['icon'] }}" style="font-size: 16px;"></i>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="item-title text-truncate" style="font-size: 13px; font-weight: 600; color: #1E293B;">{{ $f['name'] }}</span>
                                        <span class="badge font-mono" style="font-size: 9.5px; background: #F1F5F9; color: #64748B; border: 1px solid #E2E8F0; padding: 1px 5px;">{{ $f['category'] }}</span>
                                    </div>
                                    <small class="text-muted d-block text-truncate" style="font-size: 11px;">{{ $f['desc'] }}</small>
                                </div>
                                <i class="ti ti-chevron-right text-muted ms-auto" style="font-size: 14px;"></i>
                            </a>
                        @endforeach
                        <div id="navbarSearchDynamic" style="display: none;"></div>
                    </div>

                    <!-- Search Empty State -->
                    <div id="navbarSearchEmpty" class="p-4 text-center" style="display: none;">
                        <i class="ti ti-search-off text-muted mb-2 d-block" style="font-size: 2rem; opacity: 0.4;"></i>
                        <p class="mb-0 fw-semibold text-dark" style="font-size: 13px;">Fitur Tidak Ditemukan</p>
                        <small class="text-muted">Ketik nama menu, halaman, atau pengaturan yang ingin dibuka.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Topbar Right: Help & Notification -->
        <ul class="navbar-nav flex-row align-items-center ms-auto gap-1 gap-sm-2 m-0 p-0" style="flex-shrink: 0;">

            <!-- Global Help Button -->
            @if(auth()->check() && (auth()->user()->hasAnyRole(['admin', 'super admin', 'gm administrasi', 'admin pusat']) || auth()->user()->can('dashboard.index')))
            <li class="nav-item d-flex align-items-center justify-content-center">
                <button type="button" class="btn btn-help-navbar d-flex align-items-center justify-content-center gap-1.5 m-0"
                    data-bs-toggle="offcanvas" data-bs-target="#offcanvasHelp" aria-controls="offcanvasHelp"
                    title="Bantuan halaman ini" aria-label="Bantuan Halaman Ini"
                    style="background: #F8FAFC; border: 1px solid #E2E8F0; height: 38px; border-radius: 10px; transition: all 0.2s ease; padding: 0 12px;">
                    <i class="ti ti-help-circle" style="font-size: 18px; color: var(--theme-color-1, #3C2A21);"></i>
                    <span class="navbar-btn-label d-none d-md-inline fw-semibold" style="font-size: 12.5px; color: #1E293B; letter-spacing: -0.01em;">Bantuan</span>
                </button>
            </li>
            @endif

            <!-- Notification Dropdown -->
            @if(module_enabled('leave'))
            @php
                $total_notif = ($notifikasi_ajuan_absen ?? 0);
            @endphp
            <li class="nav-item dropdown-notifications navbar-dropdown dropdown d-flex align-items-center justify-content-center me-1">
                <a class="nav-link dropdown-toggle hide-arrow position-relative d-flex align-items-center justify-content-center p-0 m-0" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                    aria-expanded="false" title="Notifikasi Pengajuan" style="background: #F8FAFC; border: 1px solid #E2E8F0; width: 38px; height: 38px; border-radius: 10px; transition: all 0.2s ease;">
                    <i class="ti ti-bell" style="font-size: 18px; color: #475569;"></i>
                    @if($total_notif > 0)
                        <span class="badge bg-danger rounded-pill position-absolute" style="top: -4px; right: -4px; font-size: 10px; padding: 2px 5px;">{{ $total_notif }}</span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end py-0 shadow-lg" style="min-width: 320px; border-radius: 14px; border: 1px solid rgba(15,23,42,0.08); overflow: hidden;">
                    <li class="dropdown-menu-header border-bottom" style="background: #F8FAFC;">
                        <div class="dropdown-header d-flex align-items-center py-2.5 px-3">
                            <h6 class="text-dark mb-0 me-auto fw-bold" style="font-size: 13.5px;">Notifikasi Pengajuan</h6>
                            @if($total_notif > 0)
                                <span class="badge rounded-pill bg-label-primary font-mono" style="font-size: 10.5px;">{{ $total_notif }} Menunggu</span>
                            @endif
                        </div>
                    </li>
                    <li class="dropdown-notifications-list scrollable-container" style="max-height: 320px; overflow-y: auto;">
                        <ul class="list-group list-group-flush">
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
                                        }
                                    @endphp
                                    <li class="list-group-item list-group-item-action dropdown-notifications-item p-3 border-bottom">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-shrink-0 me-2.5">
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

                            @if($total_notif == 0)
                                <li class="list-group-item text-center py-4">
                                    <i class="ti ti-bell-off text-muted mb-2 d-block" style="font-size: 2rem; opacity: 0.4;"></i>
                                    <p class="mb-0 fw-semibold text-dark" style="font-size: 13px;">Tidak ada permohonan pending</p>
                                    <small class="text-muted">Semua permohonan izin sudah diproses.</small>
                                </li>
                            @endif
                        </ul>
                    </li>
                    <li class="dropdown-menu-footer border-top" style="background: #FAF9F8; border-color: #E2E8F0 !important;">
                        <a href="{{ route('izinabsen.index') }}"
                            class="dropdown-item d-flex justify-content-center p-2.5 h-px-40 align-items-center fw-semibold text-decoration-none" style="font-size: 12.5px; color: var(--theme-color-1, #3C2A21) !important;">
                            Buka Halaman Persetujuan <i class="ti ti-arrow-right ms-1"></i>
                        </a>
                    </li>
                </ul>
            </li>
            @endif
            <!--/ Notification -->
        </ul>
    </div>
</nav>

<style>
    #layout-navbar {
        border-radius: 12px !important;
        border: 1px solid rgba(15, 23, 42, 0.08) !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02) !important;
        padding: 0 1.5rem !important;
    }
    .layout-navbar-fixed #layout-navbar {
        left: calc(16.25rem + 1.5rem) !important;
        right: 1.5rem !important;
        width: auto !important;
        max-width: none !important;
        margin: 1rem 0 0 0 !important;
    }
    .layout-navbar-fixed.layout-menu-collapsed #layout-navbar {
        left: calc(5.25rem + 1.5rem) !important;
        right: 1.5rem !important;
        width: auto !important;
        max-width: none !important;
    }
    .navbar-search-wrapper .input-group:focus-within {
        border-color: var(--theme-color-1, #3C2A21) !important;
        box-shadow: 0 0 0 2px rgba(var(--bs-primary-rgb), 0.15) !important;
        border-radius: 10px;
    }
    .navbar-search-wrapper .input-group:focus-within .input-group-text,
    .navbar-search-wrapper .input-group:focus-within input {
        border-color: var(--theme-color-1, #3C2A21) !important;
    }
    #navbarSearchClear,
    #navbarSearchClear:hover,
    #navbarSearchClear:focus,
    #navbarSearchClear:active {
        background: transparent !important;
        background-color: transparent !important;
        box-shadow: none !important;
        border: none !important;
        outline: none !important;
    }
    #navbarSearchClear:hover i {
        color: #0F172A !important;
    }
    .navbar-search-item {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        margin: 2px 4px;
        border-radius: 8px;
        text-decoration: none;
        color: #0F172A;
        transition: all 0.15s ease;
    }
    .navbar-search-item:hover, .navbar-search-item.active-item {
        background: #F1F5F9;
        color: var(--theme-color-1, #3C2A21);
    }
    .navbar-search-item:hover .item-title, .navbar-search-item.active-item .item-title {
        color: var(--theme-color-1, #3C2A21) !important;
    }
    @media (max-width: 1199.98px) {
        .layout-navbar-fixed #layout-navbar,
        #layout-navbar {
            left: 1.5rem !important;
            right: 1.5rem !important;
            width: auto !important;
            max-width: none !important;
            margin: 1rem 0 0 0 !important;
            padding: 0 14px !important;
        }
    }
    @media (max-width: 767.98px) {
        .layout-navbar-fixed #layout-navbar,
        #layout-navbar {
            left: 10px !important;
            right: 10px !important;
            width: auto !important;
            max-width: none !important;
            margin: 8px 0 0 0 !important;
            padding: 0 10px !important;
            height: 56px !important;
            min-height: 56px !important;
        }
        .btn-help-navbar {
            width: 38px !important;
            min-width: 38px !important;
            height: 38px !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .btn-help-navbar .navbar-btn-label {
            display: none !important;
        }
        #navbarSearchWrapper {
            margin: 0 2px !important;
            min-width: 100px !important;
        }
        #navbarSearchWrapper .input-group {
            height: 36px !important;
        }
        #navbarSearchInput {
            height: 36px !important;
            font-size: 12px !important;
            padding: 0 4px !important;
        }
        #navbarSearchWrapper .input-group-text {
            padding: 0 8px !important;
        }
        .navbar-search-results {
            position: fixed !important;
            top: 68px !important;
            left: 10px !important;
            right: 10px !important;
            width: calc(100% - 20px) !important;
            max-width: none !important;
            min-width: 0 !important;
            z-index: 1099 !important;
            border-radius: 12px !important;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.16) !important;
        }
        .dropdown-notifications .dropdown-menu {
            position: fixed !important;
            top: 68px !important;
            left: 10px !important;
            right: 10px !important;
            width: calc(100% - 20px) !important;
            max-width: 360px !important;
            margin-left: auto !important;
            z-index: 1099 !important;
        }
    }
</style>

<!-- Admin Feature Search Controller Script -->
<script>
    (function() {
        const input = document.getElementById('navbarSearchInput');
        const wrapper = document.getElementById('navbarSearchWrapper');
        const dropdown = document.getElementById('navbarSearchResults');
        const clearBtn = document.getElementById('navbarSearchClear');
        const list = document.getElementById('navbarSearchList');
        const empty = document.getElementById('navbarSearchEmpty');
        const countBadge = document.getElementById('navbarSearchCount');

        if (!dropdown || !list) return;

        let activeIndex = -1;

        function getVisibleItems() {
            return Array.from(list.querySelectorAll('.navbar-search-item')).filter(el => el.style.display !== 'none');
        }

        function filterFeatures(query) {
            query = (query || '').trim().toLowerCase();

            if (clearBtn) {
                clearBtn.classList.toggle('d-none', query.length === 0);
            }

            const allItems = list.querySelectorAll('.navbar-search-item');
            let matchCount = 0;

            allItems.forEach(el => {
                const name = (el.getAttribute('data-name') || '').toLowerCase();
                const kw = (el.getAttribute('data-keywords') || '').toLowerCase();
                const cat = (el.getAttribute('data-category') || '').toLowerCase();

                const isMatch = query === '' || name.includes(query) || kw.includes(query) || cat.includes(query);
                el.style.display = isMatch ? 'flex' : 'none';
                el.classList.remove('active-item');
                if (isMatch) matchCount++;
            });

            if (matchCount === 0) {
                empty.style.display = 'block';
                list.style.display = 'none';
                if (countBadge) countBadge.textContent = '0 Menu';
            } else {
                empty.style.display = 'none';
                list.style.display = 'block';
                if (countBadge) countBadge.textContent = matchCount + ' Menu';
            }

            activeIndex = -1;
            const visible = getVisibleItems();
            if (visible.length > 0 && query.length > 0) {
                activeIndex = 0;
                visible[0].classList.add('active-item');
            }
        }

        let searchAbortController = null;
        let searchDebounceTimer = null;

        function escapeHtml(str) {
            if (!str) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        async function fetchRemoteSearch(query) {
            if (searchAbortController) {
                searchAbortController.abort();
            }
            searchAbortController = new AbortController();

            const dynamicContainer = document.getElementById('navbarSearchDynamic');
            if (!dynamicContainer) return;

            query = (query || '').trim();
            if (query.length < 2) {
                dynamicContainer.innerHTML = '';
                dynamicContainer.style.display = 'none';
                return;
            }

            try {
                const res = await fetch(`{{ route('dashboard.global-search') }}?q=${encodeURIComponent(query)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: searchAbortController.signal
                });

                if (res.status === 429) {
                    console.warn('[GlobalSearch] Rate limit reached (429). Keeping current search view intact.');
                    return;
                }

                if (!res.ok) return;

                const data = await res.json();
                renderDynamicResults(data, dynamicContainer);
            } catch (err) {
                if (err.name === 'AbortError') return;
                console.warn('[GlobalSearch] Error fetching results:', err);
            }
        }

        function renderDynamicResults(data, container) {
            let html = '';

            // Render Karyawan Results
            if (data && data.karyawan && data.karyawan.length > 0) {
                html += '<div class="px-3 py-1.5 border-top border-bottom text-muted font-mono" style="font-size: 10.5px; background: #FAF9F8;"><i class="ti ti-users me-1"></i>Data Karyawan</div>';
                data.karyawan.forEach(k => {
                    html += `<a href="${escapeHtml(k.url)}" class="navbar-search-item" data-name="${escapeHtml(k.nama.toLowerCase())}" data-keywords="${escapeHtml(k.nik.toLowerCase())}" data-category="karyawan">
                        <div class="d-flex align-items-center justify-content-center rounded-circle me-2.5 flex-shrink-0" style="width: 30px; height: 30px; background: #E0F2FE; color: #0284C7;">
                            <i class="ti ti-user" style="font-size: 15px;"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex align-items-center gap-1.5">
                                <span class="item-title text-truncate" style="font-size: 12.5px; font-weight: 600; color: #1E293B;">${escapeHtml(k.nama)}</span>
                                <span class="badge font-mono" style="font-size: 9px; background: #F0FDF4; color: #166534; border: 1px solid #DCFCE7;">${escapeHtml(k.nik)}</span>
                            </div>
                            <small class="text-muted d-block text-truncate" style="font-size: 10.5px;">${escapeHtml(k.dept)} • ${escapeHtml(k.jabatan)}</small>
                        </div>
                        <i class="ti ti-chevron-right text-muted ms-auto" style="font-size: 13px;"></i>
                    </a>`;
                });
            }

            // Render Presensi Results
            if (data && data.presensi && data.presensi.length > 0) {
                html += '<div class="px-3 py-1.5 border-top border-bottom text-muted font-mono" style="font-size: 10.5px; background: #FAF9F8;"><i class="ti ti-calendar-check me-1"></i>Monitoring Presensi</div>';
                data.presensi.forEach(p => {
                    html += `<a href="${escapeHtml(p.url)}" class="navbar-search-item" data-name="${escapeHtml(p.nama.toLowerCase())}" data-keywords="${escapeHtml(p.tanggal.toLowerCase())}" data-category="presensi">
                        <div class="d-flex align-items-center justify-content-center rounded-circle me-2.5 flex-shrink-0" style="width: 30px; height: 30px; background: #FEF3C7; color: #D97706;">
                            <i class="ti ti-clock" style="font-size: 15px;"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex align-items-center gap-1.5">
                                <span class="item-title text-truncate" style="font-size: 12.5px; font-weight: 600; color: #1E293B;">${escapeHtml(p.nama)}</span>
                                <span class="badge font-mono" style="font-size: 9px; background: #FEF3C7; color: #92400E;">${escapeHtml(p.status_label)}</span>
                            </div>
                            <small class="text-muted d-block text-truncate" style="font-size: 10.5px;">${escapeHtml(p.tanggal)} • Masuk: ${escapeHtml(p.jam_in)}</small>
                        </div>
                        <i class="ti ti-chevron-right text-muted ms-auto" style="font-size: 13px;"></i>
                    </a>`;
                });
            }

            if (html) {
                container.innerHTML = html;
                container.style.display = 'block';
                if (empty) empty.style.display = 'none';
            } else {
                container.innerHTML = '';
                container.style.display = 'none';
            }
        }

        // Desktop Search Events
        if (input) {
            input.addEventListener('input', function() {
                dropdown.style.display = 'block';
                filterFeatures(this.value);

                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(() => {
                    fetchRemoteSearch(this.value);
                }, 300);
            });

            input.addEventListener('focus', function() {
                dropdown.style.display = 'block';
                filterFeatures(this.value);
                if (this.value && this.value.trim().length >= 2) {
                    fetchRemoteSearch(this.value);
                }
            });

            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    input.value = '';
                    if (searchAbortController) searchAbortController.abort();
                    const dynamicContainer = document.getElementById('navbarSearchDynamic');
                    if (dynamicContainer) {
                        dynamicContainer.innerHTML = '';
                        dynamicContainer.style.display = 'none';
                    }
                    filterFeatures('');
                    input.focus();
                });
            }

            input.addEventListener('keydown', function(e) {
                handleKeydown(e, input);
            });
        }

        // Shared Keydown Handler
        function handleKeydown(e, sourceInput) {
            const items = getVisibleItems();

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (!items.length) return;
                if (activeIndex < items.length - 1) {
                    if (activeIndex >= 0 && items[activeIndex]) items[activeIndex].classList.remove('active-item');
                    activeIndex++;
                    items[activeIndex].classList.add('active-item');
                    items[activeIndex].scrollIntoView({ block: 'nearest' });
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (!items.length) return;
                if (activeIndex > 0) {
                    if (items[activeIndex]) items[activeIndex].classList.remove('active-item');
                    activeIndex--;
                    items[activeIndex].classList.add('active-item');
                    items[activeIndex].scrollIntoView({ block: 'nearest' });
                }
            } else if (e.key === 'Enter') {
                e.preventDefault();
                let targetUrl = null;
                if (activeIndex >= 0 && items[activeIndex]) {
                    targetUrl = items[activeIndex].getAttribute('href');
                } else if (items.length > 0) {
                    targetUrl = items[0].getAttribute('href');
                }
                if (targetUrl) {
                    dropdown.style.display = 'none';
                    sourceInput.blur();
                    window.location.href = targetUrl;
                }
            } else if (e.key === 'Escape') {
                dropdown.style.display = 'none';
                sourceInput.blur();
            }
        }

        // Click on Item: Direct Navigation
        list.addEventListener('click', function(e) {
            const a = e.target.closest('.navbar-search-item');
            if (a) {
                const url = a.getAttribute('href');
                dropdown.style.display = 'none';
                if (input) input.value = '';
                if (clearBtn) clearBtn.classList.add('d-none');
                if (url && url !== '#' && !url.startsWith('javascript:')) {
                    window.location.href = url;
                }
            }
        });

        // Click outside closes dropdown
        document.addEventListener('click', function(e) {
            if (wrapper && !wrapper.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });

        // Global Keyboard Shortcut (Ctrl+K or ⌘K or /)
        document.addEventListener('keydown', function(e) {
            if (!input) return;

            if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
                e.preventDefault();
                input.focus();
                input.select();
                dropdown.style.display = 'block';
                filterFeatures(input.value);
            } else if (e.key === '/' && document.activeElement !== input && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
                e.preventDefault();
                input.focus();
                input.select();
                dropdown.style.display = 'block';
                filterFeatures(input.value);
            }
        });
    })();
</script>
