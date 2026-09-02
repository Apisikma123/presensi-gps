@extends('layouts.app')
@section('titlepage', 'Dashboard')


<style>
    .digital-clock {
        background: rgba(255, 255, 255, 0.15);
        padding: 1rem 1.5rem;
        border-radius: 20px;
        color: #fff;
        font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
        gap: 1.25rem;
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        position: absolute;
        right: 2rem;
        top: 50%;
        transform: translateY(-50%);
        min-width: 220px;
        transition: all 0.3s ease;
    }

    .digital-clock:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-52%);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }

    .clock-icon {
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        box-shadow: inset 0 0 10px rgba(255,255,255,0.1);
    }

    .clock-content {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .clock-time {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -1px;
        margin-bottom: 0.2rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .clock-format {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        opacity: 0.9;
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 1.25rem;
        margin-top: 1.5rem;
    }

    .stat-card {
        border-radius: 20px;
        padding: 1.5rem;
        background: #fff;
        border: 1px solid rgba(15, 23, 42, 0.08);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        display: flex;
        flex-direction: column;
        gap: 1rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        min-height: 170px;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
    }

    .stat-card--highlight {
        background: var(--theme-color-1);
        color: #fff;
        border: none;
        box-shadow: 0 25px 45px rgba(0, 0, 0, 0.15);
    }

    .stat-card__top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .stat-card__icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: rgba(15, 23, 42, 0.08);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #0f172a;
    }

    .stat-card--highlight .stat-card__icon {
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
    }

    .stat-card__title {
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: rgba(15, 23, 42, 0.65);
        margin-bottom: 0.35rem;
    }

    .stat-card__value {
        font-size: 2.4rem;
        font-weight: 700;
        margin: 0;
    }

    .stat-card__meta {
        margin: 0;
        font-size: 0.92rem;
        color: rgba(15, 23, 42, 0.6);
    }

    .stat-card__trend {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-weight: 600;
        color: var(--stat-accent, var(--theme-color-1));
    }

    .stat-card__trend i {
        font-size: 1rem;
    }

    .stat-card--highlight .stat-card__title,
    .stat-card--highlight .stat-card__meta,
    .stat-card--highlight .stat-card__trend {
        color: rgba(255, 255, 255, 0.85);
    }

    .stat-card--highlight .stat-card__value {
        color: #fff;
    }

    .contract-card {
        border-radius: 24px;
        border: 1px solid rgba(15, 23, 42, 0.08);
        box-shadow: 0 25px 45px rgba(15, 23, 42, 0.08);
    }

    .contract-header h4 {
        font-weight: 700;
        margin-bottom: 0.35rem;
    }

    .contract-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 1.25rem;
    }

    .contract-summary__item {
        flex: 1 1 140px;
        border-radius: 14px;
        padding: 0.75rem 1rem;
        background: var(--contract-summary-bg, #ffffff);
        border: none;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
        color: #ffffff;
    }

    .contract-summary__icon {
        width: 32px;
        height: 32px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: #fff;
        background: rgba(15, 23, 42, 0.25);
    }

    .contract-summary__count {
        font-size: 1.4rem;
        font-weight: 700;
        margin: 0;
        line-height: 1.1;
    }

    .contract-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        padding: 0.85rem 1.5rem;
        font-weight: 600;
        color: #475569;
    }

    .contract-tabs .nav-link.active {
        color: #0f172a;
        border-color: var(--contract-accent, #0f9f6e);
        background: transparent;
    }

    .contract-table-wrapper {
        border-radius: 18px;
        border: 1px solid rgba(15, 23, 42, 0.06);
        box-shadow: inset 0 1px 0 rgba(15, 23, 42, 0.03);
        overflow: hidden;
        margin: 0;
    }

    .contract-table thead {
        background: #002e65;
        color: #fff;
    }

    .contract-table th {
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.05em;
    }

    .contract-row--overdue {
        background: #fee2e2;
    }

    .contract-pill {
        border-radius: 999px;
        padding: 0.35rem 0.85rem;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .contract-pill--danger {
        background: rgba(220, 38, 38, 0.15);
        color: #b91c1c;
    }

    .contract-pill--safe {
        background: rgba(34, 197, 94, 0.15);
        color: #15803d;
    }

    .contract-empty {
        padding: 3rem 1rem;
        text-align: center;
        color: #94a3b8;
    }

    .welcome-card {
        border-radius: 24px;
        padding: 2rem;
        background: var(--theme-color-1);
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 25px 45px rgba(0, 0, 0, 0.15);
        margin-top: 1.5rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .welcome-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .welcome-card__content {
        position: relative;
        z-index: 1;
    }

    .welcome-card__greeting {
        font-size: 1.1rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 0.5rem;
        letter-spacing: 0.02em;
    }

    .welcome-card__name {
        font-size: 2rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 0.75rem;
        line-height: 1.2;
    }

    .welcome-card__date {
        font-size: 0.95rem;
        color: rgba(255, 255, 255, 0.85);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .welcome-card__icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.25);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: #ffffff;
        position: absolute;
        right: 2rem;
        top: 50%;
        transform: translateY(-50%);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
</style>


@section('content')
@section('navigasi')
    <span>Dashboard</span>
@endsection

<div class="d-flex justify-content-end mt-3">
    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterDashboardModal">
        <i class="ti ti-filter me-1"></i> Filter
    </button>
</div>

<!-- Modal Filter -->
<div class="modal fade" id="filterDashboardModal" tabindex="-1" aria-labelledby="filterDashboardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterDashboardModalLabel">Filter Kehadiran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="">
                <div class="modal-body">
                    <div class="row">
                        <x-input-with-icon label="Tanggal" icon="ti ti-calendar" name="tanggal" datepicker="flatpickr-date"
                            value="{{ Request('tanggal') }}" />
                        <x-select label="Cabang" name="kode_cabang" :data="$cabang" key="kode_cabang" textShow="nama_cabang"
                            selected="{{ Request('kode_cabang') }}" />
                        <x-select label="Departemen" name="kode_dept" :data="$departemen" key="kode_dept" textShow="nama_dept"
                            selected="{{ Request('kode_dept') }}" upperCase="true" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                    <button class="btn btn-primary"><i class="ti ti-search me-1"></i> Terapkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Karyawan Presensi -->
<div class="modal fade" id="modalKaryawanPresensi" tabindex="-1" aria-labelledby="modalKaryawanPresensiLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalKaryawanPresensiLabel">Detail Kehadiran Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalKaryawanPresensiContent">
                <!-- Data loaded via Ajax -->
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@php
    $authUser = auth()->user();
    $fullName = $authUser->name ?? 'Pengguna';
    $userName = explode(' ', $fullName)[0]; // Ambil nama depan saja
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
    <div class="alert alert-danger d-flex align-items-center mb-3" role="alert" style="border-radius: 15px; border: none; box-shadow: 0 10px 20px rgba(0,0,0,0.05); padding: 1.25rem;">
        <span class="alert-icon text-danger me-3" style="font-size: 2rem;">
            <i class="ti ti-alert-triangle"></i>
        </span>
        <div>
            <h6 class="alert-heading mb-1" style="font-weight: 700; color: inherit;">
                {{ $expired_alert['is_expired'] ? 'Aplikasi Telah Kadaluarsa!' : 'Peringatan Masa Aktif Aplikasi!' }}
            </h6>
            <span>
                @if($expired_alert['is_expired'])
                    Masa aktif aplikasi ini telah berakhir pada <strong>{{ $expired_alert['date'] }}</strong>. Silakan perpanjang lisensi Anda agar seluruh pengguna tetap dapat menggunakan aplikasi ini.
                @else
                    Masa aktif aplikasi ini akan berakhir dalam <strong>{{ $expired_alert['days_left'] }} hari</strong> lagi (pada tanggal <strong>{{ $expired_alert['date'] }}</strong>).
                @endif
            </span>
        </div>
    </div>
@endif

<!-- Welcome Card -->
<div class="welcome-card">
    <div class="welcome-card__content">
        <div class="welcome-card__greeting">{{ $greeting }},</div>
        <div class="welcome-card__name">{{ $userName }}</div>
        <div class="welcome-card__date">
            <i class="ti ti-calendar"></i>
            <span>{{ $tanggalHariIni }}</span>
        </div>
    </div>
    <div class="digital-clock" id="digital-clock">
        <div class="clock-icon" id="clock-icon">
            <i class="ti ti-sun"></i>
        </div>
        <div class="clock-content">
            <div class="clock-time">
                <span id="hours">00</span>:<span id="minutes">00</span>:<span id="seconds">00</span>
            </div>
            <div class="clock-format">
                <span id="ampm">AM</span>
            </div>
        </div>
    </div>
</div>

@php
    $presenceStats = [
        [
            'title' => 'Total Hadir',
            'value' => $rekappresensi->hadir ?? 0,
            'meta' => 'Karyawan hadir hari ini',
            'trend' => 'Live update',
            'icon' => 'ti ti-user-check',
            'class' => 'stat-card--highlight ',
            'status' => 'h',
        ],
        [
            'title' => 'Izin',
            'value' => $rekappresensi->izin ?? 0,
            'meta' => 'Sedang izin resmi',
            'trend' => 'Terverifikasi',
            'icon' => 'ti ti-file-description',
            'accent' => '#2563eb',
            'status' => 'i',
        ],
        [
            'title' => 'Sakit',
            'value' => $rekappresensi->sakit ?? 0,
            'meta' => 'Sedang sakit',
            'trend' => 'Realtime update',
            'icon' => 'ti ti-ambulance',
            'accent' => '#d97706',
            'status' => 's',
        ],
        [
            'title' => 'Cuti',
            'value' => $rekappresensi->cuti ?? 0,
            'meta' => 'Sedang cuti ',
            'trend' => 'Terjadwal',
            'icon' => 'ti ti-briefcase',
            'accent' => '#7c3aed',
            'status' => 'c',
        ],
    ];

    if (isset($storage_info) && $authUser->hasRole('master admin')) {
        $storageColor = '#22c55e'; // Green
        $storageBg = 'rgba(34, 197, 94, 0.1)';
        if ($storage_info['percentage'] >= 90) {
            $storageColor = '#ef4444'; // Red
            $storageBg = 'rgba(239, 68, 68, 0.1)';
        } elseif ($storage_info['percentage'] >= 70) {
            $storageColor = '#f59e0b'; // Yellow
            $storageBg = 'rgba(245, 158, 11, 0.1)';
        }

        $presenceStats[] = [
            'title' => 'Server Storage',
            'value' => $storage_info['percentage'] . '%',
            'meta' => $storage_info['used'] . ' / ' . $storage_info['total'],
            'trend' => $storage_info['free'] . ' Tersedia',
            'icon' => 'ti ti-database',
            'accent' => $storageColor,
            'is_storage' => true,
            'storage_bg' => $storageBg,
        ];
    }
@endphp

<div class="stat-grid">
    @foreach ($presenceStats as $stat)
        <div class="stat-card {{ $stat['class'] ?? '' }} {{ isset($stat['status']) ? 'stat-card-clickable' : '' }}" 
            style="--stat-accent: {{ $stat['accent'] ?? 'var(--theme-color-1)' }}; {{ isset($stat['status']) ? 'cursor: pointer;' : '' }}"
            @if(isset($stat['status'])) data-status="{{ $stat['status'] }}" @endif>
            <div class="stat-card__top">
                <div>
                    <p class="stat-card__title">{{ $stat['title'] }}</p>
                    <h3 class="stat-card__value">{{ $stat['value'] }}</h3>
                </div>
                <div class="stat-card__icon">
                    <i class="{{ $stat['icon'] }}"></i>
                </div>
            </div>
            <div>
                @if (isset($stat['is_storage']))
                    <div class="progress mb-2" style="height: 8px; background: {{ $stat['storage_bg'] }}">
                        <div class="progress-bar" role="progressbar" style="width: {{ $stat['value'] }}; background: {{ $stat['accent'] }};"
                            aria-valuenow="{{ str_replace('%', '', $stat['value']) }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <p class="stat-card__meta mb-0 d-flex justify-content-between">
                        <span><i class="ti ti-server me-1"></i> {{ $stat['meta'] }}</span>
                        <span class="fw-bold" style="color: {{ $stat['accent'] }}">{{ $stat['trend'] }}</span>
                    </p>
                @else
                    <p class="stat-card__meta mb-1">
                        <i class="ti ti-broadcast me-1"></i>
                        {{ $stat['meta'] }}
                    </p>
                @endif
            </div>
        </div>
    @endforeach
</div>

<div class="row mt-3">
    <div class="col-lg-12 col-sm-12 col-xs-12">
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
        <div class="card mb-6">
            <div class="card-widget-separator-wrapper">
                <div class="card-body card-widget-separator">
                    <div class="row gy-4 gy-sm-1">
                        <div class="{{ $colClass }}">
                            <div class="d-flex justify-content-between align-items-start card-widget-1 {{ $totalCards > 1 ? 'border-end' : '' }} pb-4 pb-sm-0 pe-sm-3">
                                <div>
                                    <p class="mb-1">Data Karyawan Aktif</p>
                                    <h4 class="mb-1">{{ $status_karyawan->jml_aktif }}</h4>
                                </div>
                                <img src="{{ asset('assets/img/illustrations/karyawan1.png') }}" height="70" alt="view sales" class="me-3">
                            </div>
                        </div>

                        @foreach ($status_karyawan->rekap_status as $rekap)
                            @php
                                // Cycle through images karyawan2, karyawan3, karyawan4
                                $imgIndex = ($loop->index % 3) + 2;
                                $ext = ($imgIndex == 2 || $imgIndex == 4) ? 'webp' : 'png';
                                $borderClass = ($loop->last) ? '' : 'border-end';
                                $widgetClass = 'card-widget-' . (($loop->iteration % 4) + 1);
                            @endphp
                            <div class="{{ $colClass }}">
                                <div class="d-flex justify-content-between align-items-start {{ $borderClass }} pb-4 pb-sm-0 {{ $widgetClass }} ps-sm-2 pe-sm-3">
                                    <div>
                                        <p class="mb-1">{{ $rekap->nama_status_karyawan }}</p>
                                        <h4 class="mb-1">{{ $rekap->total }}</h4>
                                    </div>
                                    <img src="{{ asset('assets/img/illustrations/karyawan' . $imgIndex . '.' . $ext) }}" height="70" alt="view sales" class="me-3">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


<div class="row mt-3 g-4">
    {{-- Left Column: Kontrak & Ulang Tahun (col-xl-7 col-lg-7 col-12) --}}
    <div class="col-xl-7 col-lg-7 col-12">
        @php
            $contractTabs = [
                [
                    'id' => 'lewatjatuhtempo',
                    'label' => 'Lewat Jatuh Tempo',
                    'badge' => 'bg-label-danger',
                    'icon' => 'ti ti-alert-octagon',
                    'items' => $kontrak_lewat,
                    'showRemaining' => false,
                    'accent' => '#dc2626',
                    'active' => false,
                ],
                [
                    'id' => 'bulanini',
                    'label' => 'Bulan Ini',
                    'badge' => 'bg-label-danger',
                    'icon' => 'ti ti-calendar-event',
                    'items' => $kontrak_bulanini,
                    'showRemaining' => true,
                    'accent' => '#f97316',
                    'active' => true,
                ],
                [
                    'id' => 'bulandepan',
                    'label' => 'Bulan Depan',
                    'badge' => 'bg-label-warning',
                    'icon' => 'ti ti-calendar-stats',
                    'items' => $kontrak_bulandepan,
                    'showRemaining' => true,
                    'accent' => '#eab308',
                    'active' => false,
                ],
                [
                    'id' => 'duabulan',
                    'label' => '2 Bulan Lagi',
                    'badge' => 'bg-label-success',
                    'icon' => 'ti ti-calendar-time',
                    'items' => $kontrak_duabulan,
                    'showRemaining' => true,
                    'accent' => '#16a34a',
                    'active' => false,
                ],
            ];

            $contractSummary = [
                [
                    'label' => 'Lewat Tempo',
                    'count' => count($kontrak_lewat),
                    'icon' => 'ti ti-alert-triangle',
                    'bg' => '#fee2e2',
                    'color' => '#dc2626',
                ],
                [
                    'label' => 'Bulan Ini',
                    'count' => count($kontrak_bulanini),
                    'icon' => 'ti ti-calendar-event',
                    'bg' => '#ffedd5',
                    'color' => '#ea580c',
                ],
                [
                    'label' => 'Bulan Depan',
                    'count' => count($kontrak_bulandepan),
                    'icon' => 'ti ti-calendar-stats',
                    'bg' => '#fef9c3',
                    'color' => '#ca8a04',
                ],
                [
                    'label' => '2 Bulan',
                    'count' => count($kontrak_duabulan),
                    'icon' => 'ti ti-calendar-time',
                    'bg' => '#dcfce7',
                    'color' => '#16a34a',
                ],
            ];
        @endphp

        {{-- Card 1: Monitoring Kontrak Karyawan --}}
        <div class="card contract-card mb-4">
            <div class="card-header contract-header d-flex flex-column flex-sm-row align-items-sm-center justify-content-between pb-2">
                <div class="d-flex align-items-center mb-2 mb-sm-0">
                    <div class="avatar me-3">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class="ti ti-briefcase-off fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Monitoring Kontrak Kerja</h5>
                        <small class="text-muted">Pantau masa berlaku kontrak karyawan outlet</small>
                    </div>
                </div>
                <span class="badge bg-label-success rounded-pill">
                    Total {{ count($kontrak_lewat) + count($kontrak_bulanini) + count($kontrak_bulandepan) + count($kontrak_duabulan) }} Kontrak
                </span>
            </div>
            <div class="card-body pt-2">
                <div class="row g-2 mb-3">
                    @foreach ($contractSummary as $summary)
                        <div class="col-6 col-sm-3">
                            <div class="d-flex align-items-center p-2 rounded-3" style="background: {{ $summary['bg'] }}; color: {{ $summary['color'] }};">
                                <div class="avatar avatar-xs me-2 d-flex align-items-center justify-content-center rounded-circle" style="background: rgba(255,255,255,0.6);">
                                    <i class="{{ $summary['icon'] }} fs-6"></i>
                                </div>
                                <div>
                                    <small class="d-block fw-semibold text-uppercase" style="font-size: 10px;">{{ $summary['label'] }}</small>
                                    <span class="fw-bold fs-6">{{ $summary['count'] }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="contract-tabs nav-align-top">
                    <ul class="nav nav-tabs nav-fill" role="tablist">
                        @foreach ($contractTabs as $tab)
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link py-2 {{ $tab['active'] ? 'active' : '' }}" role="tab"
                                    data-bs-toggle="tab" data-bs-target="#{{ $tab['id'] }}" aria-controls="{{ $tab['id'] }}"
                                    aria-selected="{{ $tab['active'] ? 'true' : 'false' }}">
                                    <i class="{{ $tab['icon'] }} me-1"></i>
                                    {{ $tab['label'] }}
                                    <span class="badge rounded-pill {{ $tab['badge'] }} ms-1">
                                        {{ count($tab['items']) }}
                                    </span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                    <div class="tab-content mt-2 p-0 border-0">
                        @foreach ($contractTabs as $tab)
                            <div class="tab-pane fade {{ $tab['active'] ? 'show active' : '' }}" id="{{ $tab['id'] }}"
                                role="tabpanel">
                                @if (count($tab['items']) === 0)
                                    <div class="text-center py-4 text-muted">
                                        <i class="ti ti-circle-check fs-2 text-success mb-1 d-block"></i>
                                        <span class="fs-6">Tidak ada kontrak pada kategori ini.</span>
                                    </div>
                                @else
                                    <div class="table-responsive contract-table-wrapper">
                                        <table class="table table-hover align-middle mb-0 contract-table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>No. Kontrak</th>
                                                    <th>NIK</th>
                                                    <th>Nama Karyawan</th>
                                                    <th>Jabatan</th>
                                                    <th>Cabang</th>
                                                    <th>Akhir Kontrak</th>
                                                    @if ($tab['showRemaining'])
                                                        <th class="text-center">Sisa Waktu</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($tab['items'] as $d)
                                                    @php
                                                        $sisahari = hitungSisahari($d->sampai);
                                                        $isLate = $sisahari < 0;
                                                    @endphp
                                                    <tr class="{{ $isLate ? 'contract-row--overdue' : '' }}">
                                                        <td>{{ $d->no_kontrak }}</td>
                                                        <td>{{ $d->nik }}</td>
                                                        <td class="fw-semibold">{{ formatName($d->nama_karyawan) }}</td>
                                                        <td>{{ singkatString($d->nama_jabatan) }}</td>
                                                        <td>{{ textupperCase($d->kode_cabang) }}</td>
                                                        <td>{{ formatIndo($d->sampai) }}</td>
                                                        @if ($tab['showRemaining'])
                                                            <td class="text-center">
                                                                <span class="badge {{ $isLate ? 'bg-danger' : 'bg-success' }}">
                                                                    {{ $sisahari }} Hari
                                                                </span>
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Karyawan Ulang Tahun --}}
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between pb-2">
                <div class="d-flex align-items-center">
                    <div class="avatar me-3">
                        <span class="avatar-initial rounded bg-label-warning">
                            <i class="ti ti-cake fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Karyawan Ulang Tahun Hari Ini</h5>
                        <small class="text-muted">Notifikasi hari lahir karyawan</small>
                    </div>
                </div>
                <span class="badge bg-label-warning rounded-pill">{{ count($birthday) }} Karyawan</span>
            </div>
            <div class="card-body pt-2">
                @if (count($birthday) > 0)
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <h6 class="mb-0 fw-bold">Kirim Ucapan Ulang Tahun</h6>
                            <small class="text-muted">Kirim ucapan otomatis via WhatsApp ke semua yang berulang tahun</small>
                        </div>
                        <div>
                            <button type="button" class="btn btn-success btn-sm" id="btnKirimUcapan" onclick="kirimUcapanSemua()">
                                <i class="ti ti-brand-whatsapp me-1"></i>
                                <span id="btnText">Kirim Ucapan</span>
                                <span id="btnLoading" class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
                            </button>
                        </div>
                    </div>
                    <div class="row g-3">
                        @foreach ($birthday as $d)
                            @php
                                $umur = \Carbon\Carbon::parse($d->tanggal_lahir)->age;
                                $colors = ['primary', 'success', 'info', 'warning', 'danger'];
                                $colorIndex = $loop->index % count($colors);
                                $color = $colors[$colorIndex];
                            @endphp
                            <div class="col-12">
                                <div class="card card-border-shadow-{{ $color }} p-2">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3" style="width: 50px; height: 50px;">
                                            @if (!empty($d->foto) && Storage::disk('public')->exists('/karyawan/' . $d->foto))
                                                <img src="{{ getfotoKaryawan($d->foto) }}" alt="{{ $d->nama_karyawan }}" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="avatar-initial rounded-circle bg-label-{{ $color }} d-flex align-items-center justify-content-center" style="font-size: 20px;">
                                                    <i class="ti ti-user"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h6 class="mb-0 fw-bold">{{ $d->nama_karyawan }}</h6>
                                                <span class="badge bg-label-{{ $color }}">{{ $umur }} Tahun</span>
                                            </div>
                                            <small class="text-muted">{{ $d->nama_jabatan }} • {{ textupperCase($d->nama_cabang) }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="d-flex align-items-center p-3 rounded-3" style="background: #f8fafc; border: 1px dashed #e2e8f0;">
                        <div class="avatar avatar-sm me-3">
                            <span class="avatar-initial rounded-circle bg-label-secondary">
                                <i class="ti ti-cake fs-5"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted fw-semibold">Tidak ada karyawan yang berulang tahun hari ini</h6>
                            <small class="text-muted">Notifikasi ucapan akan muncul otomatis saat ada karyawan yang berulang tahun.</small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right Column: Demografi Charts (col-xl-5 col-lg-5 col-12) --}}
    <div class="col-xl-5 col-lg-5 col-12">
        {{-- Card 1: Rasio Jenis Kelamin --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between pb-0">
                <div class="card-title mb-0">
                    <h5 class="m-0 me-2 fw-bold">Komposisi Jenis Kelamin</h5>
                    <small class="text-muted">Rasio Karyawan Pria & Wanita</small>
                </div>
                <div class="avatar avatar-sm">
                    <span class="avatar-initial rounded bg-label-primary">
                        <i class="ti ti-users fs-5"></i>
                    </span>
                </div>
            </div>
            <div class="card-body">
                {!! $jkchart->container() !!}
            </div>
        </div>

        {{-- Card 2: Tingkat Pendidikan Karyawan --}}
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between pb-0">
                <div class="card-title mb-0">
                    <h5 class="m-0 me-2 fw-bold">Tingkat Pendidikan</h5>
                    <small class="text-muted">Distribusi Jenjang Pendidikan</small>
                </div>
                <div class="avatar avatar-sm">
                    <span class="avatar-initial rounded bg-label-info">
                        <i class="ti ti-school fs-5"></i>
                    </span>
                </div>
            </div>
            <div class="card-body">
                {!! $pddchart->container() !!}
            </div>
        </div>
    </div>
</div>

@endsection
@push('myscript')
<script src="{{ $chart->cdn() }}"></script>
{{ $chart->script() }}
{{ $jkchart->script() }}
{{ $pddchart->script() }}
<script>
    // Fungsi untuk mengirim ucapan ulang tahun ke semua karyawan menggunakan job
    function kirimUcapanSemua() {
        const btnKirim = document.getElementById('btnKirimUcapan');
        const btnText = document.getElementById('btnText');
        const btnLoading = document.getElementById('btnLoading');

        // Disable button dan tampilkan loading
        btnKirim.disabled = true;
        btnText.textContent = 'Mengirim...';
        btnLoading.classList.remove('d-none');

        // Ambil filter dari URL atau form
        const urlParams = new URLSearchParams(window.location.search);
        const kodeCabang = urlParams.get('kode_cabang') || '';
        const kodeDept = urlParams.get('kode_dept') || '';

        // Kirim request ke server
        fetch('{{ route('dashboard.kirim.ucapan.birthday') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    kode_cabang: kodeCabang,
                    kode_dept: kodeDept
                })
            })
            .then(response => response.json())
            .then(data => {
                // Enable button kembali
                btnKirim.disabled = false;
                btnText.textContent = 'Kirim ke Semua';
                btnLoading.classList.add('d-none');

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                // Enable button kembali
                btnKirim.disabled = false;
                btnText.textContent = 'Kirim ke Semua';
                btnLoading.classList.add('d-none');

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat mengirim ucapan: ' + error.message
                });
            });
    }

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

        document.getElementById('hours').textContent = hoursStr;
        document.getElementById('minutes').textContent = minutes;
        document.getElementById('seconds').textContent = seconds;
        document.getElementById('ampm').textContent = ampm;

        // Dynamic Icon & Theme Logic
        const iconContainer = document.getElementById('clock-icon');
        let iconClass = '';
        let iconColor = '';
        
        if (hours24 >= 5 && hours24 < 10) {
            iconClass = 'ti ti-sunrise';
            iconColor = '#ffb74d'; // Morning orange
        } else if (hours24 >= 10 && hours24 < 15) {
            iconClass = 'ti ti-sun';
            iconColor = '#ffd54f'; // Day yellow
        } else if (hours24 >= 15 && hours24 < 18) {
            iconClass = 'ti ti-sunset';
            iconColor = '#fb8c00'; // Sunset orange
        } else {
            iconClass = 'ti ti-moon-stars';
            iconColor = '#e1f5fe'; // Night blue
        }
        
        if (iconContainer) {
            iconContainer.innerHTML = `<i class="${iconClass}" style="color: ${iconColor};"></i>`;
        }
    }

    setInterval(updateClock, 1000);
    updateClock(); // Initial call

    $(function() {
        $('.stat-card-clickable').on('click', function() {
            var status = $(this).data('status');
            
            // Ambil filter dari URL query parameters atau filter modal input jika diubah
            var urlParams = new URLSearchParams(window.location.search);
            var tanggal = urlParams.get('tanggal') || '{{ Request("tanggal") }}' || '';
            var kode_cabang = urlParams.get('kode_cabang') || '{{ Request("kode_cabang") }}' || '';
            var kode_dept = urlParams.get('kode_dept') || '{{ Request("kode_dept") }}' || '';

            // Tampilkan spinner loading
            $('#modalKaryawanPresensiContent').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="text-muted mt-2">Memuat data...</div>
                </div>
            `);
            
            // Set default title
            $('#modalKaryawanPresensiLabel').text('Detail Kehadiran Karyawan');
            
            // Tampilkan modal
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

        // Filter table rows by search input (Name or NIK)
        $(document).on('keyup', '#searchKaryawanNama', function() {
            var value = $(this).val().toLowerCase();
            $("#modalKaryawanPresensiContent table tbody tr").filter(function() {
                var nameText = $(this).find('td:eq(3)').text().toLowerCase();
                var nikText = $(this).find('td:eq(2)').text().toLowerCase();
                
                // Only filter actual employee data rows
                if ($(this).find('td').length > 1) {
                    $(this).toggle(nameText.indexOf(value) > -1 || nikText.indexOf(value) > -1);
                }
            });
        });
    });
</script>
@endpush
