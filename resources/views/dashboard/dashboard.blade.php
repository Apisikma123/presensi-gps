@extends('layouts.app')
@section('titlepage', 'Dashboard')

<style>
    /* Minimalist Dashboard Typography & Surfaces */
    .dashboard-header-card {
        background: #FFFFFF;
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .digital-clock-chip {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        padding: 0.5rem 0.9rem;
        border-radius: 10px;
        font-family: 'JetBrains Mono', monospace;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #0F172A;
        font-weight: 700;
        font-size: 0.95rem;
    }

    .stat-grid-minimal {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 1rem;
    }

    .stat-card-min {
        background: #FFFFFF;
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 125px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .stat-card-min:hover {
        transform: translateY(-2px);
        border-color: rgba(30, 77, 62, 0.25);
        box-shadow: 0 6px 16px -2px rgba(15, 23, 42, 0.06);
    }

    .stat-card-min .stat-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748B;
        margin-bottom: 4px;
    }

    .stat-card-min .stat-value {
        font-size: 2.15rem;
        font-weight: 800;
        font-family: 'JetBrains Mono', monospace;
        color: #0F172A;
        line-height: 1.1;
        margin: 0;
        letter-spacing: -0.02em;
    }

    .stat-card-min .stat-chip-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }

    .stat-card-min .stat-desc {
        font-size: 11.5px;
        color: #64748B;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Contract Segmented Tabs */
    .nav-segment-tabs {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 10px;
        padding: 4px;
        display: flex;
        gap: 4px;
    }

    .nav-segment-tabs .nav-link {
        border-radius: 8px;
        border: none;
        padding: 6px 14px;
        font-size: 12.5px;
        font-weight: 600;
        color: #64748B;
        transition: all 0.15s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        flex: 1;
    }

    .nav-segment-tabs .nav-link.active {
        background: #FFFFFF;
        color: #0F172A;
        font-weight: 700;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    }

    .empty-state-box {
        background: #F8FAF8;
        border: 1px dashed #E2E8F0;
        border-radius: 10px;
        padding: 2.25rem 1rem;
        text-align: center;
    }
</style>

@section('content')

@php
    $authUser = auth()->user();
    $fullName = $authUser->name ?? 'Pengguna';
    $userName = explode(' ', $fullName)[0];
    $currentHour = (int) date('H');

    if ($currentHour >= 5 && $currentHour < 12) {
        $greeting = 'Selamat Pagi';
    } elseif ($currentHour >= 12 && $currentHour < 15) {
        $greeting = 'Selamat Siang';
    } elseif ($currentHour >= 15 && $currentHour < 19) {
        $greeting = 'Selamat Sore';
    } else {
        $greeting = 'Selamat Malam';
    }

    $tanggalHariIni = getnamaHari(date('D')) . ', ' . DateToIndo(date('Y-m-d'));
@endphp

@if(isset($expired_alert) && $expired_alert !== null)
    <div class="alert alert-danger d-flex align-items-center mb-3" role="alert" style="border-radius: 12px; border: 1px solid #FECACA; padding: 1rem 1.25rem;">
        <span class="alert-icon text-danger me-3 fs-3">
            <i class="ti ti-alert-triangle"></i>
        </span>
        <div>
            <h6 class="alert-heading mb-1 fw-bold text-danger">
                {{ $expired_alert['is_expired'] ? 'Aplikasi Telah Kadaluarsa!' : 'Peringatan Masa Aktif Aplikasi!' }}
            </h6>
            <span class="text-dark" style="font-size: 13px;">
                @if($expired_alert['is_expired'])
                    Masa aktif aplikasi ini telah berakhir pada <strong>{{ $expired_alert['date'] }}</strong>. Silakan perpanjang lisensi Anda.
                @else
                    Masa aktif aplikasi ini akan berakhir dalam <strong>{{ $expired_alert['days_left'] }} hari</strong> lagi (tanggal <strong>{{ $expired_alert['date'] }}</strong>).
                @endif
            </span>
        </div>
    </div>
@endif

<!-- Top Header Card (Greeting, Live Clock & Quick Filter) -->
<div class="dashboard-header-card mb-3">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar avatar-md rounded-3 d-flex align-items-center justify-content-center"
                style="background: rgba(30, 77, 62, 0.08); color: #1E4D3E; width: 44px; height: 44px; border: 1px solid rgba(30, 77, 62, 0.15);">
                <i class="ti ti-coffee fs-3"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.01em;">{{ $greeting }}, {{ $userName }}</h5>
                <small class="text-muted d-flex align-items-center gap-1.5 mt-0.5" style="font-size: 12px;">
                    <i class="ti ti-calendar text-muted"></i>
                    <span>{{ $tanggalHariIni }}</span>
                    <span class="text-muted mx-1">•</span>
                    <span>Monitoring Operasional Presensi Outlet</span>
                </small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <!-- Digital Clock Chip -->
            <div class="digital-clock-chip" id="digital-clock">
                <span id="clock-icon" class="text-muted"><i class="ti ti-clock"></i></span>
                <span id="hours">00</span>:<span id="minutes">00</span>:<span id="seconds">00</span>
                <span id="ampm" class="badge bg-light text-muted px-1 py-0.5 rounded" style="font-size: 9.5px;">AM</span>
            </div>
            <!-- Quick Filter Modal Trigger -->
            <button class="btn btn-outline-secondary d-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#filterDashboardModal">
                <i class="ti ti-adjustments-horizontal" style="font-size: 15px;"></i>
                <span>Filter</span>
            </button>
        </div>
    </div>
</div>

@php
    $presenceStats = [
        [
            'title' => 'Hadir Hari Ini',
            'value' => $rekappresensi->hadir ?? 0,
            'meta' => 'Karyawan hadir & terverifikasi',
            'icon' => 'ti ti-user-check',
            'bg' => '#ECFDF5',
            'color' => '#059669',
            'status' => 'h',
        ],
        [
            'title' => 'Izin Resmi',
            'value' => $rekappresensi->izin ?? 0,
            'meta' => 'Sedang izin absen resmi',
            'icon' => 'ti ti-file-description',
            'bg' => '#EFF6FF',
            'color' => '#1D4ED8',
            'status' => 'i',
        ],
        [
            'title' => 'Sakit',
            'value' => $rekappresensi->sakit ?? 0,
            'meta' => 'Sedang sakit dengan surat',
            'icon' => 'ti ti-ambulance',
            'bg' => '#FFFBEB',
            'color' => '#D97706',
            'status' => 's',
        ],
        [
            'title' => 'Cuti',
            'value' => $rekappresensi->cuti ?? 0,
            'meta' => 'Sedang cuti terjadwal',
            'icon' => 'ti ti-calendar-off',
            'bg' => '#FDF4FF',
            'color' => '#9333EA',
            'status' => 'c',
        ],
    ];

    if (isset($storage_info) && $authUser->hasRole('master admin')) {
        $storageColor = '#059669';
        $storageBg = '#ECFDF5';
        if ($storage_info['percentage'] >= 90) {
            $storageColor = '#DC2626';
            $storageBg = '#FEF2F2';
        } elseif ($storage_info['percentage'] >= 70) {
            $storageColor = '#D97706';
            $storageBg = '#FFFBEB';
        }

        $presenceStats[] = [
            'title' => 'Server Storage',
            'value' => $storage_info['percentage'] . '%',
            'meta' => $storage_info['used'] . ' / ' . $storage_info['total'],
            'icon' => 'ti ti-database',
            'bg' => $storageBg,
            'color' => $storageColor,
            'is_storage' => true,
        ];
    }
@endphp

<!-- 4 Key Daily Attendance Cards -->
<div class="stat-grid-minimal mb-3">
    @foreach ($presenceStats as $stat)
        <div class="stat-card-min {{ isset($stat['status']) ? 'stat-card-clickable' : '' }}" 
            style="{{ isset($stat['status']) ? 'cursor: pointer;' : '' }}"
            @if(isset($stat['status'])) data-status="{{ $stat['status'] }}" @endif>
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">{{ $stat['title'] }}</div>
                    <h3 class="stat-value">{{ $stat['value'] }}</h3>
                </div>
                <div class="stat-chip-icon" style="background: {{ $stat['bg'] }}; color: {{ $stat['color'] }};">
                    <i class="{{ $stat['icon'] }}"></i>
                </div>
            </div>
            <div>
                @if (isset($stat['is_storage']))
                    <div class="progress my-1" style="height: 6px; background: #F1F5F9; border-radius: 999px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $stat['value'] }}; background: {{ $stat['color'] }}; border-radius: 999px;"
                            aria-valuenow="{{ str_replace('%', '', $stat['value']) }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                @endif
                <div class="stat-desc">
                    <span class="badge-dot" style="width: 6px; height: 6px; border-radius: 50%; background: {{ $stat['color'] }};"></span>
                    <span>{{ $stat['meta'] }}</span>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Karyawan Overview Bento Strip -->
@php
    $rekapStatusCount = count($status_karyawan->rekap_status ?? []);
    $totalCards = 1 + $rekapStatusCount;
    $colClass = match($totalCards) {
        1 => 'col-12',
        2 => 'col-12 col-sm-6',
        3 => 'col-12 col-sm-6 col-md-4',
        4 => 'col-12 col-sm-6 col-md-3',
        default => 'col-12 col-sm-6 col-md-4 col-lg'
    };
@endphp
<div class="card mb-4" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px;">
    <div class="card-body py-2.5 px-3">
        <div class="row gy-3 align-items-center">
            <div class="{{ $colClass }}">
                <div class="d-flex align-items-center justify-content-between {{ $totalCards > 1 ? 'border-end' : '' }} pe-3 py-1">
                    <div>
                        <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 10.5px; letter-spacing: 0.04em;">Data Karyawan Aktif</small>
                        <h4 class="mb-0 fw-bold font-mono text-dark">{{ $status_karyawan->jml_aktif }}</h4>
                    </div>
                    <div class="avatar avatar-sm rounded-2 d-flex align-items-center justify-content-center"
                        style="background: rgba(30, 77, 62, 0.08); color: #1E4D3E; width: 36px; height: 36px;">
                        <i class="ti ti-users fs-5"></i>
                    </div>
                </div>
            </div>

            @foreach ($status_karyawan->rekap_status as $rekap)
                @php
                    $borderClass = ($loop->last) ? '' : 'border-end';
                    $icons = ['ti-file-certificate', 'ti-user-check', 'ti-id-badge-2', 'ti-briefcase'];
                    $iconName = $icons[$loop->index % count($icons)];
                    $tints = [
                        ['bg' => '#FFFBEB', 'color' => '#D97706'],
                        ['bg' => '#ECFDF5', 'color' => '#059669'],
                        ['bg' => '#EFF6FF', 'color' => '#1D4ED8'],
                        ['bg' => '#FDF4FF', 'color' => '#9333EA'],
                    ];
                    $tint = $tints[$loop->index % count($tints)];
                @endphp
                <div class="{{ $colClass }}">
                    <div class="d-flex align-items-center justify-content-between {{ $borderClass }} ps-sm-3 pe-3 py-1">
                        <div>
                            <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 10.5px; letter-spacing: 0.04em;">{{ $rekap->nama_status_karyawan }}</small>
                            <h4 class="mb-0 fw-bold font-mono text-dark">{{ $rekap->total }}</h4>
                        </div>
                        <div class="avatar avatar-sm rounded-2 d-flex align-items-center justify-content-center"
                            style="background: {{ $tint['bg'] }}; color: {{ $tint['color'] }}; width: 36px; height: 36px;">
                            <i class="ti {{ $iconName }} fs-5"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Monitoring Shift Kerja Coffee Shop Hari Ini -->
<div class="card mb-4" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px;">
    <div class="card-header d-flex align-items-center justify-content-between pb-2">
        <div class="d-flex align-items-center gap-2">
            <div class="avatar avatar-sm rounded-2 d-flex align-items-center justify-content-center"
                style="background: rgba(30, 77, 62, 0.08); color: #1E4D3E; width: 34px; height: 34px;">
                <i class="ti ti-clock-play fs-5"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold text-dark">Roster Shift Operasional Outlet Hari Ini</h6>
                <small class="text-muted" style="font-size: 11.5px;">Monitoring penugasan shift barista, cashier & staf outlet coffee</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if(!empty($pending_tukar_shift) && $pending_tukar_shift > 0)
                <a href="{{ route('ajuanjadwal.index') }}" class="badge bg-warning text-dark font-mono text-decoration-none px-2 py-1">
                    <i class="ti ti-arrows-left-right me-1"></i> {{ $pending_tukar_shift }} Ajuan Tukar Shift
                </a>
            @endif
            <a href="{{ route('jamkerja.index') }}" class="btn btn-xs btn-outline-secondary d-flex align-items-center gap-1" style="font-size: 11.5px; padding: 4px 10px;">
                <i class="ti ti-settings-2"></i> Kelola Shift
            </a>
        </div>
    </div>
    <div class="card-body pt-2">
        <div class="row g-3">
            @forelse ($shift_operasional as $shift)
                @php
                    $totalShiftKaryawan = count($shift['karyawan']);
                    $percent = $totalShiftKaryawan > 0 ? round(($shift['hadir'] / $totalShiftKaryawan) * 100) : 0;
                @endphp
                <div class="col-xl-6 col-lg-6 col-12">
                    <div class="p-3 rounded-3 border h-100" style="background: #FAFCFA; border-color: #E2E8F0 !important;">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge" style="background: rgba(30, 77, 62, 0.1); color: #1E4D3E; font-size: 10px; font-weight: 700;">{{ $shift['kode'] }}</span>
                                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 13.5px;">{{ $shift['nama'] }}</h6>
                                </div>
                                <small class="text-muted font-mono d-flex align-items-center gap-1 mt-1" style="font-size: 11.5px;">
                                    <i class="ti ti-clock"></i> {{ $shift['jam_masuk'] }} - {{ $shift['jam_pulang'] }} WIB ({{ $shift['total_jam'] ?? 8 }} Jam)
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-white text-dark border font-mono" style="font-size: 11px;">
                                    <strong class="text-success">{{ $shift['hadir'] }}</strong> / {{ $totalShiftKaryawan }} Hadir
                                </span>
                            </div>
                        </div>

                        <!-- Mini Progress -->
                        <div class="progress my-2" style="height: 5px; background: #E2E8F0; border-radius: 999px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $percent }}%; background: #1E4D3E;"
                                aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>

                        <!-- Employee avatars & presence chip list -->
                        <div class="mt-2.5">
                            @if($totalShiftKaryawan > 0)
                                <div class="d-flex flex-wrap gap-1.5 align-items-center">
                                    @foreach ($shift['karyawan'] as $staf)
                                        @php
                                            $isHadir = $staf['status'] === 'h';
                                        @endphp
                                        <div class="d-flex align-items-center gap-1.5 py-1 px-2 rounded-2 border bg-white" style="font-size: 11px; border-color: #E2E8F0 !important;">
                                            <span class="badge-dot" style="width: 7px; height: 7px; border-radius: 50%; background: {{ $isHadir ? '#059669' : '#CBD5E1' }};"></span>
                                            <span class="fw-semibold text-dark">{{ $staf['nama_karyawan'] }}</span>
                                            <span class="text-muted" style="font-size: 10px;">({{ $staf['nama_jabatan'] }})</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <small class="text-muted fst-italic">Belum ada karyawan yang ditugaskan pada shift ini hari ini.</small>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-3 text-muted">
                    Belum ada master shift operasional.
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Main 2-Column Section -->
<div class="row g-4">
    {{-- Left Column: Kontrak & Ulang Tahun (col-lg-7) --}}
    <div class="col-xl-7 col-lg-7 col-12">
        @php
            $contractTabs = [
                [
                    'id' => 'lewatjatuhtempo',
                    'label' => 'Lewat Tempo',
                    'count' => count($kontrak_lewat),
                    'badge' => 'bg-danger text-white',
                    'icon' => 'ti ti-alert-triangle',
                    'items' => $kontrak_lewat,
                    'showRemaining' => false,
                    'active' => false,
                ],
                [
                    'id' => 'bulanini',
                    'label' => 'Bulan Ini',
                    'count' => count($kontrak_bulanini),
                    'badge' => 'bg-warning text-dark',
                    'icon' => 'ti ti-calendar-event',
                    'items' => $kontrak_bulanini,
                    'showRemaining' => true,
                    'active' => true,
                ],
                [
                    'id' => 'bulandepan',
                    'label' => 'Bulan Depan',
                    'count' => count($kontrak_bulandepan),
                    'badge' => 'bg-secondary text-white',
                    'icon' => 'ti ti-calendar-stats',
                    'items' => $kontrak_bulandepan,
                    'showRemaining' => true,
                    'active' => false,
                ],
                [
                    'id' => 'duabulan',
                    'label' => '2 Bulan Lagi',
                    'count' => count($kontrak_duabulan),
                    'badge' => 'bg-secondary text-white',
                    'icon' => 'ti ti-calendar-time',
                    'items' => $kontrak_duabulan,
                    'showRemaining' => true,
                    'active' => false,
                ],
            ];
            $totalSemuaKontrak = count($kontrak_lewat) + count($kontrak_bulanini) + count($kontrak_bulandepan) + count($kontrak_duabulan);
        @endphp

        {{-- Card: Monitoring Kontrak Kerja --}}
        <div class="card mb-4" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px;">
            <div class="card-header d-flex align-items-center justify-content-between pb-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm rounded-2 d-flex align-items-center justify-content-center"
                        style="background: rgba(30, 77, 62, 0.08); color: #1E4D3E; width: 32px; height: 32px;">
                        <i class="ti ti-briefcase fs-5"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">Monitoring Kontrak Kerja</h6>
                        <small class="text-muted" style="font-size: 11.5px;">Masa berlaku kontrak karyawan outlet</small>
                    </div>
                </div>
                <span class="badge bg-label-primary font-mono" style="font-size: 11px;">
                    Total {{ $totalSemuaKontrak }} Kontrak
                </span>
            </div>
            <div class="card-body pt-2">
                <!-- Segmented Tabs (Clean, single-row pill design) -->
                <ul class="nav nav-segment-tabs mb-3" role="tablist">
                    @foreach ($contractTabs as $tab)
                        <li class="nav-item flex-grow-1" role="presentation">
                            <button type="button" class="nav-link w-100 {{ $tab['active'] ? 'active' : '' }}" role="tab"
                                data-bs-toggle="tab" data-bs-target="#tab-{{ $tab['id'] }}" aria-selected="{{ $tab['active'] ? 'true' : 'false' }}">
                                <i class="{{ $tab['icon'] }}" style="font-size: 14px;"></i>
                                <span>{{ $tab['label'] }}</span>
                                <span class="badge {{ $tab['badge'] }} rounded-pill font-mono px-1.5 py-0.5" style="font-size: 10px;">{{ $tab['count'] }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>

                <!-- Tab Content Tables -->
                <div class="tab-content p-0">
                    @foreach ($contractTabs as $tab)
                        <div class="tab-pane fade {{ $tab['active'] ? 'show active' : '' }}" id="tab-{{ $tab['id'] }}" role="tabpanel">
                            @if ($tab['count'] > 0)
                                <div class="table-responsive rounded-2 border" style="border-color: #E2E8F0 !important;">
                                    <table class="table table-hover table-sm mb-0 align-middle">
                                        <thead>
                                            <tr>
                                                <th class="py-2">Karyawan</th>
                                                <th class="py-2">Departemen</th>
                                                <th class="py-2">Sampai Tanggal</th>
                                                @if ($tab['showRemaining'])
                                                    <th class="py-2 text-center">Sisa Waktu</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tab['items'] as $item)
                                                @php
                                                    $today = \Carbon\Carbon::now(config('app.timezone'));
                                                    $endDate = \Carbon\Carbon::parse($item->sampai_tanggal);
                                                    $daysDiff = $today->diffInDays($endDate, false);
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="fw-bold text-dark" style="font-size: 13px;">{{ $item->nama_karyawan }}</div>
                                                        <small class="text-muted font-mono" style="font-size: 11px;">{{ $item->nik }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-label-secondary" style="font-size: 10.5px;">{{ $item->nama_dept ?? '-' }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="font-mono fw-semibold text-dark" style="font-size: 12px;">{{ date('d M Y', strtotime($item->sampai_tanggal)) }}</span>
                                                    </td>
                                                    @if ($tab['showRemaining'])
                                                        <td class="text-center">
                                                            @if ($daysDiff < 0)
                                                                <span class="badge bg-label-danger font-mono" style="font-size: 10.5px;">Lewat {{ abs(round($daysDiff)) }} hari</span>
                                                            @else
                                                                <span class="badge bg-label-warning font-mono" style="font-size: 10.5px;">{{ round($daysDiff) }} hari lagi</span>
                                                            @endif
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="empty-state-box">
                                    <i class="ti ti-check-circle text-success fs-2 d-block mb-1"></i>
                                    <h6 class="mb-0 text-dark fw-semibold" style="font-size: 13px;">Tidak ada kontrak pada kategori ini</h6>
                                    <small class="text-muted">Semua kontrak karyawan termonitor dengan baik.</small>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Card: Karyawan Ulang Tahun Hari Ini --}}
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px;">
            <div class="card-header d-flex align-items-center justify-content-between pb-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm rounded-2 d-flex align-items-center justify-content-center"
                        style="background: #FFFBEB; color: #D97706; width: 32px; height: 32px;">
                        <i class="ti ti-cake fs-5"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">Karyawan Ulang Tahun Hari Ini</h6>
                        <small class="text-muted" style="font-size: 11.5px;">Notifikasi hari lahir karyawan outlet</small>
                    </div>
                </div>
                <span class="badge bg-label-warning font-mono" style="font-size: 11px;">
                    {{ count($birthday) }} Karyawan
                </span>
            </div>
            <div class="card-body pt-2">
                @if (count($birthday) > 0)
                    <div class="row g-2">
                        @foreach ($birthday as $d)
                            @php
                                $umur = \Carbon\Carbon::parse($d->tanggal_lahir)->age;
                            @endphp
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between p-2.5 rounded-2 border" style="background: #FFFFFF; border-color: #E2E8F0 !important;">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="avatar avatar-sm">
                                            @if (!empty($d->foto) && Storage::disk('public')->exists('/karyawan/' . $d->foto))
                                                <img src="{{ getfotoKaryawan($d->foto) }}" alt="{{ $d->nama_karyawan }}" class="rounded-circle" style="object-fit: cover;">
                                            @else
                                                <div class="avatar-initial rounded-circle bg-label-warning fw-bold" style="font-size: 11px;">
                                                    {{ strtoupper(substr($d->nama_karyawan, 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark" style="font-size: 13px;">{{ $d->nama_karyawan }}</h6>
                                            <small class="text-muted" style="font-size: 11px;">{{ $d->nama_jabatan }} • {{ textupperCase($d->nama_cabang) }}</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-label-warning font-mono">{{ $umur }} Tahun</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state-box">
                        <i class="ti ti-cake text-muted fs-2 d-block mb-1" style="opacity: 0.4;"></i>
                        <h6 class="mb-0 text-muted fw-semibold" style="font-size: 13px;">Tidak ada karyawan yang berulang tahun hari ini</h6>
                        <small class="text-muted">Notifikasi ucapan akan muncul otomatis saat ada yang berulang tahun.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right Column: Demografi Charts (col-lg-5) --}}
    <div class="col-xl-5 col-lg-5 col-12">
        {{-- Card 1: Rasio Jenis Kelamin --}}
        <div class="card mb-4" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px;">
            <div class="card-header d-flex align-items-center justify-content-between pb-0">
                <div>
                    <h6 class="mb-0 fw-bold text-dark">Komposisi Jenis Kelamin</h6>
                    <small class="text-muted" style="font-size: 11.5px;">Rasio Karyawan Pria & Wanita</small>
                </div>
                <div class="avatar avatar-sm rounded-2 d-flex align-items-center justify-content-center"
                    style="background: rgba(30, 77, 62, 0.08); color: #1E4D3E; width: 32px; height: 32px;">
                    <i class="ti ti-users fs-5"></i>
                </div>
            </div>
            <div class="card-body pt-2">
                {!! $jkchart->container() !!}
            </div>
        </div>

        {{-- Card 2: Tingkat Pendidikan Karyawan --}}
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px;">
            <div class="card-header d-flex align-items-center justify-content-between pb-0">
                <div>
                    <h6 class="mb-0 fw-bold text-dark">Tingkat Pendidikan</h6>
                    <small class="text-muted" style="font-size: 11.5px;">Distribusi Jenjang Pendidikan Karyawan</small>
                </div>
                <div class="avatar avatar-sm rounded-2 d-flex align-items-center justify-content-center"
                    style="background: #EFF6FF; color: #1D4ED8; width: 32px; height: 32px;">
                    <i class="ti ti-school fs-5"></i>
                </div>
            </div>
            <div class="card-body pt-2">
                {!! $pddchart->container() !!}
            </div>
        </div>
    </div>
</div>

<!-- Modal Filter Kehadiran -->
<div class="modal fade" id="filterDashboardModal" tabindex="-1" aria-labelledby="filterDashboardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: 1px solid rgba(15,23,42,0.08);">
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold text-dark" id="filterDashboardModalLabel">
                    <i class="ti ti-adjustments-horizontal me-1 text-primary"></i> Filter Data Dashboard
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="">
                <div class="modal-body py-3">
                    <div class="row g-3">
                        <div class="col-12">
                            <x-input-with-icon label="Tanggal Presensi" icon="ti ti-calendar" name="tanggal" datepicker="flatpickr-date"
                                value="{{ Request('tanggal') }}" />
                        </div>
                        <div class="col-12">
                            <x-select label="Cabang Outlet" name="kode_cabang" :data="$cabang" key="kode_cabang" textShow="nama_cabang"
                                selected="{{ Request('kode_cabang') }}" />
                        </div>
                        <div class="col-12">
                            <x-select label="Divisi / Departemen" name="kode_dept" :data="$departemen" key="kode_dept" textShow="nama_dept"
                                selected="{{ Request('kode_dept') }}" upperCase="true" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary"><i class="ti ti-search me-1"></i> Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Karyawan Presensi -->
<div class="modal fade" id="modalKaryawanPresensi" tabindex="-1" aria-labelledby="modalKaryawanPresensiLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: 1px solid rgba(15,23,42,0.08);">
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold text-dark" id="modalKaryawanPresensiLabel">Detail Kehadiran Karyawan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalKaryawanPresensiContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top py-2.5">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
{{ $chart->script() }}
{{ $jkchart->script() }}
{{ $pddchart->script() }}
<script>
    function updateClock() {
        const now = new Date();
        const hours24 = now.getHours();
        let hours = hours24;
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';

        hours = hours % 12;
        hours = hours ? hours : 12;
        const hoursStr = String(hours).padStart(2, '0');

        const hEl = document.getElementById('hours');
        const mEl = document.getElementById('minutes');
        const sEl = document.getElementById('seconds');
        const aEl = document.getElementById('ampm');
        if (hEl) hEl.textContent = hoursStr;
        if (mEl) mEl.textContent = minutes;
        if (sEl) sEl.textContent = seconds;
        if (aEl) aEl.textContent = ampm;

        const iconContainer = document.getElementById('clock-icon');
        let iconClass = 'ti ti-sun';
        
        if (hours24 >= 5 && hours24 < 11) {
            iconClass = 'ti ti-sunrise text-warning';
        } else if (hours24 >= 11 && hours24 < 15) {
            iconClass = 'ti ti-sun text-warning';
        } else if (hours24 >= 15 && hours24 < 18) {
            iconClass = 'ti ti-sunset text-warning';
        } else {
            iconClass = 'ti ti-moon-stars text-primary';
        }
        
        if (iconContainer) {
            iconContainer.innerHTML = `<i class="${iconClass}"></i>`;
        }
    }

    setInterval(updateClock, 1000);
    updateClock();

    $(function() {
        $('.stat-card-clickable').on('click', function() {
            var status = $(this).data('status');
            
            var urlParams = new URLSearchParams(window.location.search);
            var tanggal = urlParams.get('tanggal') || '{{ Request("tanggal") }}' || '';
            var kode_cabang = urlParams.get('kode_cabang') || '{{ Request("kode_cabang") }}' || '';
            var kode_dept = urlParams.get('kode_dept') || '{{ Request("kode_dept") }}' || '';

            $('#modalKaryawanPresensiContent').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="text-muted mt-2">Memuat data kehadiran...</div>
                </div>
            `);
            
            $('#modalKaryawanPresensiLabel').text('Detail Kehadiran Karyawan');
            $('#modalKaryawanPresensi').modal('show');

            $.ajax({
                type: 'POST',
                url: '{{ route("dashboard.get.karyawan.presensi") }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status,
                    tanggal: tanggal,
                    kode_cabang: kode_cabang,
                    kode_dept: kode_dept
                },
                success: function(response) {
                    if(response.success) {
                        $('#modalKaryawanPresensiLabel').text(response.title);
                        $('#modalKaryawanPresensiContent').html(response.html);
                    } else {
                        $('#modalKaryawanPresensiContent').html(`<div class="alert alert-danger m-3">Gagal memuat data.</div>`);
                    }
                },
                error: function(xhr, status, error) {
                    $('#modalKaryawanPresensiContent').html(`<div class="alert alert-danger m-3">Terjadi kesalahan: ${error}</div>`);
                }
            });
        });

        $(document).on('keyup', '#searchKaryawanNama', function() {
            var value = $(this).val().toLowerCase();
            $("#modalKaryawanPresensiContent table tbody tr").filter(function() {
                var nameText = $(this).find('td:eq(3)').text().toLowerCase();
                var nikText = $(this).find('td:eq(2)').text().toLowerCase();
                if ($(this).find('td').length > 1) {
                    $(this).toggle(nameText.indexOf(value) > -1 || nikText.indexOf(value) > -1);
                }
            });
        });
    });
</script>
@endpush
