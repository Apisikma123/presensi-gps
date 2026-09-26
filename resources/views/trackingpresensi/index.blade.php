@extends('layouts.app')
@section('titlepage', 'Tracking Presensi')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('presensi.index') }}">Monitoring Presensi</a></li>
    <li class="breadcrumb-item active">Live Tracking GPS</li>
@endsection

@section('content')
@push('mystyle')
<link rel="stylesheet" href="{{ asset('assets/vendor/css/leaflet.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/css/MarkerCluster.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/css/MarkerCluster.Default.css') }}" />
<style>
    /* Map container styling */
    #map-wrapper {
        position: relative;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(37, 22, 14, 0.06);
        border: 1px solid rgba(60, 42, 33, 0.12);
        background: #F4F3F2;
    }
    #map {
        height: 640px;
        width: 100%;
        background: #ECE9E6;
    }

    /* Leaflet Control Overrides (Swiss Precision) */
    .leaflet-bar {
        border: 1px solid rgba(60, 42, 33, 0.15) !important;
        border-radius: 8px !important;
        overflow: hidden;
        box-shadow: 0 4px 14px rgba(var(--bs-primary-rgb, 37, 22, 14), 0.08) !important;
    }
    .leaflet-bar a {
        background-color: #FFFFFF !important;
        color: var(--theme-color-1, #25160E) !important;
        border-bottom: 1px solid var(--theme-border, rgba(60, 42, 33, 0.08)) !important;
        transition: background-color 0.15s ease;
    }
    .leaflet-bar a:hover {
        background-color: #FAF9F8 !important;
        color: #000000 !important;
    }
    .leaflet-control-layers {
        border: 1px solid rgba(60, 42, 33, 0.15) !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 14px rgba(37, 22, 14, 0.08) !important;
        font-family: 'Inter', sans-serif !important;
        font-size: 12px !important;
    }

    /* Fast cluster styling - Modern Roastery & Swiss Precision (DESIGN.md) */
    .custom-cluster-marker,
    .marker-cluster-small,
    .marker-cluster-medium,
    .marker-cluster-large {
        background: transparent !important;
        border: none !important;
    }
    .custom-cluster-marker .cluster-inner {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: #FFFFFF;
        font-weight: 700;
        font-family: 'JetBrains Mono', monospace;
        box-shadow: 0 4px 14px rgba(37, 22, 14, 0.35);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        will-change: transform;
    }
    .custom-cluster-marker:hover .cluster-inner {
        transform: scale(1.12);
        box-shadow: 0 6px 20px rgba(37, 22, 14, 0.45);
    }
    .marker-cluster-small .cluster-inner {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, var(--theme-color-2), var(--color-primary-hover)) !important;
        border: 2.5px solid #FFFFFF !important;
        font-size: 13px;
    }
    .marker-cluster-medium .cluster-inner {
        width: 46px;
        height: 46px;
        background: linear-gradient(135deg, var(--theme-color-2), var(--theme-color-1)) !important;
        border: 2.5px solid #FFFFFF !important;
        font-size: 14px;
    }
    .marker-cluster-large .cluster-inner {
        width: 54px;
        height: 54px;
        background: linear-gradient(135deg, var(--theme-color-1), var(--color-primary-hover)) !important;
        border: 3px solid #FFFFFF !important;
        font-size: 15px;
        box-shadow: 0 6px 22px rgba(var(--bs-primary-rgb, 37, 22, 14), 0.5) !important;
    }

    /* Individual Marker Styling */
    .emp-marker {
        background: transparent !important;
        border: none !important;
    }
    .emp-marker-pin {
        position: relative;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid var(--theme-color-1, #3C2A21);
        background: #FFFFFF;
        box-shadow: 0 3px 10px rgba(var(--bs-primary-rgb, 37, 22, 14), 0.22);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        will-change: transform;
    }
    .emp-marker-pin:hover {
        transform: scale(1.18);
        z-index: 9999;
    }
    .emp-marker-pin img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }
    .emp-marker-pin .emp-initials {
        font-size: 12px;
        font-weight: 700;
        color: var(--theme-color-1, #3C2A21);
        font-family: 'Outfit', sans-serif;
        text-transform: uppercase;
    }
    .emp-status-dot {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #4A6741;
        border: 2px solid #FFFFFF;
    }
    .emp-name-tag {
        position: absolute;
        bottom: -22px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--theme-color-1, #3C2A21);
        color: var(--theme-primary-contrast, #FAF9F8);
        padding: 2px 7px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 600;
        font-family: 'Inter', sans-serif;
        white-space: nowrap;
        pointer-events: none;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        max-width: 120px;
        overflow: hidden;
        text-overflow: ellipsis;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    /* Leaflet Popup Styling (Modern Roastery Theme) */
    .leaflet-popup-content-wrapper {
        border-radius: 14px !important;
        padding: 0 !important;
        overflow: hidden !important;
        box-shadow: 0 16px 36px rgba(37, 22, 14, 0.2) !important;
        border: 1px solid rgba(60, 42, 33, 0.12) !important;
        background: #FFFFFF !important;
    }
    .leaflet-popup-content {
        margin: 0 !important;
        line-height: 1.4 !important;
    }
    .leaflet-container a.leaflet-popup-close-button {
        top: 10px !important;
        right: 12px !important;
        color: rgba(255, 255, 255, 0.8) !important;
        font-size: 18px !important;
        padding: 0 !important;
        width: 24px !important;
        height: 24px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 6px !important;
        transition: all 0.15s ease !important;
    }
    .leaflet-container a.leaflet-popup-close-button:hover {
        background: rgba(255, 255, 255, 0.2) !important;
        color: #FFFFFF !important;
    }
    .leaflet-popup-tip {
        background: #FFFFFF !important;
        box-shadow: 0 4px 12px rgba(37, 22, 14, 0.12) !important;
    }
    .popup-header {
        background: linear-gradient(135deg, var(--color-primary-hover, var(--theme-color-2)) 0%, var(--theme-color-1) 100%) !important;
        color: var(--theme-primary-contrast, #FAF9F8) !important;
        padding: 13px 16px !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    }
    .popup-body {
        padding: 14px 16px !important;
        background: #FFFFFF !important;
    }

    /* Floating Search Bar */
    .map-search-bar {
        position: absolute;
        top: 14px;
        left: 60px;
        z-index: 1000;
        background: #FFFFFF;
        padding: 7px 14px;
        border-radius: 8px;
        box-shadow: 0 4px 16px rgba(var(--bs-primary-rgb, 37, 22, 14), 0.08);
        border: 1px solid var(--theme-border, rgba(60, 42, 33, 0.12));
        display: flex;
        align-items: center;
        gap: 8px;
        width: 290px;
    }
    .map-search-bar input {
        border: none;
        outline: none;
        background: transparent;
        font-size: 13px;
        width: 100%;
        color: var(--theme-text-primary, #0F172A);
        font-family: 'Inter', sans-serif;
    }
    .map-search-bar input::placeholder {
        color: #81756F;
    }

    /* Quick stats chip bar (Bento Minimalist) */
    .stats-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .stat-chip {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #FAF9F8;
        padding: 8px 14px;
        border-radius: 10px;
        border: 1px solid rgba(60, 42, 33, 0.08);
        box-shadow: 0 1px 3px rgba(37, 22, 14, 0.03);
    }
    .stat-chip-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }
    .stat-chip-label {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #755841;
        font-family: 'Inter', sans-serif;
    }
    .stat-chip-val {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 700;
        font-size: 15px;
        color: var(--theme-text-primary, #0F172A);
    }

    /* Tactile button interaction */
    .btn-tactile {
        transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-tactile:active {
        transform: translateY(1px);
    }
</style>
@endpush

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 14px; border: 1px solid rgba(60, 42, 33, 0.08); background: #FFFFFF;">
            <div class="card-header bg-transparent border-bottom pb-3 pt-3" style="border-bottom-color: rgba(60, 42, 33, 0.08) !important;">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="card-title mb-1 fw-bold" style="font-family: 'Outfit', sans-serif; color: var(--theme-color-1, #25160E); letter-spacing: -0.01em;">Live Tracking Presensi Karyawan</h5>
                        <p class="small mb-0" style="color: #755841; font-size: 12.5px;">Pemetaan lokasi kehadiran karyawan secara real-time dan berkinerja tinggi</p>
                    </div>
                    <!-- Quick Stats Chips -->
                    <div class="stats-chips">
                        <div class="stat-chip">
                            <div class="stat-chip-icon" style="background: var(--bs-primary-bg-subtle, rgba(var(--bs-primary-rgb), 0.1)); color: var(--theme-color-1, #3C2A21);">
                                <i class="ti ti-users"></i>
                            </div>
                            <div>
                                <div class="stat-chip-label">Total Presensi</div>
                                <div class="stat-chip-val" id="stat-total">0</div>
                            </div>
                        </div>
                        <div class="stat-chip">
                            <div class="stat-chip-icon" style="background: #F0F6EE; color: #3D5A35;">
                                <i class="ti ti-map-pin"></i>
                            </div>
                            <div>
                                <div class="stat-chip-label">Titik Lokasi</div>
                                <div class="stat-chip-val" id="stat-locations">0</div>
                            </div>
                        </div>
                        <div class="stat-chip">
                            <div class="stat-chip-icon" style="background: #F4F3F2; color: #755841;">
                                <i class="ti ti-stack"></i>
                            </div>
                            <div>
                                <div class="stat-chip-label">Cluster Overlap</div>
                                <div class="stat-chip-val" id="stat-overlap">0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Form (Responsive Auto-Fit) -->
                <div class="row g-2 align-items-end mb-3">
                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                        <label for="tanggal" class="form-label mb-1" style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #755841;">Tanggal Presensi</label>
                        <div class="input-group input-group-merge" style="height: 38px;">
                            <span class="input-group-text border-end-0" style="background: #FAF9F8; border-color: rgba(60, 42, 33, 0.14);"><i class="ti ti-calendar" style="color: #755841;"></i></span>
                            <input type="text" class="form-control flatpickr-date border-start-0" id="tanggal" name="tanggal"
                                value="{{ $tanggal }}" placeholder="Pilih tanggal"
                                style="height: 38px; background: #FAF9F8; border-color: rgba(60, 42, 33, 0.14); color: var(--theme-text-primary, #0F172A); font-weight: 500;">
                        </div>
                    </div>
                    <div class="col-lg col-md col-sm-6 col-12">
                        <label for="kode_cabang" class="form-label mb-1" style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #755841;">Filter Cabang</label>
                        <select class="form-select" id="kode_cabang" name="kode_cabang"
                            style="height: 38px; background: #FAF9F8; border-color: rgba(60, 42, 33, 0.14); color: var(--theme-text-primary, #0F172A); font-weight: 500;">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabangs as $cabang)
                                <option value="{{ $cabang->kode_cabang }}">{{ $cabang->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex align-items-center gap-2 flex-wrap" style="height: 38px;">
                            <button type="button" class="btn text-white btn-tactile d-inline-flex align-items-center justify-content-center gap-1 shadow-sm px-3" id="btn-filter"
                                style="background-color: var(--theme-color-1, #25160E) !important; border-color: var(--theme-color-1, #25160E) !important; color: var(--theme-primary-contrast, #FFFFFF) !important; border-radius: 8px; font-weight: 600; height: 38px;">
                                <i class="ti ti-filter me-1"></i><span>Terapkan</span>
                            </button>
                            <button type="button" class="btn btn-tactile d-inline-flex align-items-center justify-content-center gap-1 shadow-sm px-3" id="btn-reset"
                                style="background-color: #FFFFFF !important; border: 1px solid var(--theme-border, rgba(60, 42, 33, 0.18)) !important; color: var(--theme-color-1, #3C2A21) !important; border-radius: 8px; font-weight: 600; height: 38px;">
                                <i class="ti ti-refresh me-1"></i><span>Reset</span>
                            </button>
                            <button type="button" class="btn text-white btn-tactile d-inline-flex align-items-center justify-content-center gap-1 shadow-sm px-3" id="btn-toggle-radius"
                                style="background-color: var(--theme-color-2, #634832) !important; border-color: var(--theme-color-2, #634832) !important; color: var(--theme-primary-contrast, #FFFFFF) !important; border-radius: 8px; font-weight: 600; height: 38px;">
                                <i class="ti ti-circle me-1"></i><span>Radius Kantor</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Map Container -->
                <div id="map-wrapper">
                    <!-- Quick search inside map -->
                    <div class="map-search-bar">
                        <i class="ti ti-search" style="color: #755841; font-size: 15px;"></i>
                        <input type="text" id="map-emp-search" placeholder="Cari nama karyawan / NIK...">
                        <span id="search-clear" style="cursor: pointer; display: none;"><i class="ti ti-x" style="color: #755841;"></i></span>
                    </div>
                    <div id="map"></div>
                </div>

                <!-- Guidance footer -->
                <div class="d-flex justify-content-between align-items-center mt-3 small flex-wrap gap-2 p-2 rounded"
                     style="background: #FAF9F8; border: 1px solid rgba(60, 42, 33, 0.06); color: #634832;">
                    <div>
                        <i class="ti ti-info-circle me-1" style="color: #755841;"></i>
                        <strong>Petunjuk Navigasi:</strong> Klik angka cluster untuk memperbesar area presensi. Klik marker untuk rincian presensi & foto absensi.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk menampilkan foto dalam ukuran besar -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden; border: 1px solid rgba(60, 42, 33, 0.12);">
            <div class="modal-header py-3" style="background: linear-gradient(135deg, var(--color-primary-hover, var(--theme-color-2)) 0%, var(--theme-color-1) 100%); color: var(--theme-primary-contrast, #FFFFFF); border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                <h6 class="modal-title text-white fw-bold mb-0" id="imageModalTitle" style="font-family: 'Outfit', sans-serif; font-size: 14px;">
                    <i class="ti ti-photo me-2" style="color: #FAF9F8;"></i>Foto Presensi
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3" style="background: #FAF9F8;">
                <img id="modalImage" src="" alt="Foto Presensi" class="img-fluid rounded"
                    style="max-height: 65vh; object-fit: contain; border: 1px solid rgba(60, 42, 33, 0.08); box-shadow: 0 4px 16px rgba(37, 22, 14, 0.06);">
            </div>
            <div class="modal-footer py-2 bg-white" style="border-top: 1px solid rgba(60, 42, 33, 0.08);">
                <button type="button" class="btn btn-sm btn-tactile" data-bs-dismiss="modal"
                    style="background: #FFFFFF; border: 1px solid var(--theme-border, rgba(60, 42, 33, 0.18)); color: var(--theme-color-1, #3C2A21); font-weight: 500; border-radius: 6px;">Tutup</button>
                <a id="downloadImage" href="" download class="btn btn-sm text-white btn-tactile"
                    style="background: var(--theme-color-1, #3C2A21); border: 1px solid var(--theme-color-1, #3C2A21); color: var(--theme-primary-contrast, #FFFFFF); font-weight: 600; border-radius: 6px;">
                    <i class="ti ti-download me-1"></i>Unduh Foto
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script src="{{ asset('assets/external/js/leaflet.js') }}"></script>
<script src="{{ asset('assets/vendor/js/leaflet.markercluster.js') }}"></script>
<script>
    $(document).ready(function() {
        var themePrimary = getComputedStyle(document.documentElement).getPropertyValue('--theme-color-1').trim() || '{{ $general_setting->theme_color_1 ?? "#3C2A21" }}';
        var themeSecondary = getComputedStyle(document.documentElement).getPropertyValue('--theme-color-2').trim() || '{{ $general_setting->theme_color_2 ?? "#634832" }}';

        // Initialize flatpickr for date input
        $('.flatpickr-date').flatpickr({
            dateFormat: 'Y-m-d',
            defaultDate: '{{ $tanggal }}'
        });

        // Initialize map with smooth panning and strictly 1 world bounds
        var worldBounds = L.latLngBounds([[-85.05112878, -180], [85.05112878, 180]]);
        var map = L.map('map', {
            zoomControl: false,
            preferCanvas: true,
            minZoom: 3,
            maxBounds: worldBounds,
            maxBoundsViscosity: 1.0,
            worldCopyJump: false
        }).setView([-6.2088, 106.8456], 12);

        // Position zoom control on top right
        L.control.zoom({ position: 'topright' }).addTo(map);

        // Define base tile layers with noWrap: true to prevent repeating worlds
        var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            noWrap: true,
            bounds: worldBounds,
            attribution: '© OpenStreetMap contributors'
        });

        var googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            noWrap: true,
            bounds: worldBounds,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });

        var googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            noWrap: true,
            bounds: worldBounds,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });

        var googleSat = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            noWrap: true,
            bounds: worldBounds,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });

        // Default layer: Google Streets (bersih, nama jalan jelas, bebas simbol aneh)
        googleStreets.addTo(map);

        // Layer control on top right
        var baseMaps = {
            "Google Streets": googleStreets,
            "OpenStreetMap": osm,
            "Google Hybrid": googleHybrid,
            "Google Satellite": googleSat
        };
        L.control.layers(baseMaps, null, { position: 'topright' }).addTo(map);

        // Setup high-performance MarkerClusterGroup
        var markerClusterGroup = L.markerClusterGroup({
            chunkedLoading: true,
            chunkInterval: 50,
            chunkDelay: 20,
            maxClusterRadius: 45,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true,
            disableClusteringAtZoom: 18,
            polygonOptions: {
                fillColor: themePrimary,
                color: themePrimary,
                weight: 2,
                opacity: 0.8,
                fillOpacity: 0.14
            },
            spiderLegPolylineOptions: {
                weight: 1.5,
                color: themePrimary,
                opacity: 0.6
            },
            iconCreateFunction: function(cluster) {
                var count = cluster.getChildCount();
                var c = 'marker-cluster-small';
                var size = 38;
                if (count > 50) {
                    c = 'marker-cluster-large';
                    size = 50;
                } else if (count > 10) {
                    c = 'marker-cluster-medium';
                    size = 44;
                }
                return new L.DivIcon({
                    html: '<div class="cluster-inner"><span>' + count + '</span></div>',
                    className: 'custom-cluster-marker ' + c,
                    iconSize: new L.Point(size, size)
                });
            }
        });

        map.addLayer(markerClusterGroup);

        var radiusCircles = [];
        var allMarkersMap = {}; // mapping nik -> marker for quick search jump
        var presensiData = @json($presensis);
        var cabangRadiusData = @json($cabangRadius);

        // Helper to get initials
        function getInitials(name) {
            if (!name) return 'U';
            var parts = name.trim().split(/\s+/);
            if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
            return (parts[0][0] + parts[1][0]).toUpperCase();
        }

        // Lazy Popup Content Generator (Only generated on click!) - Anti-slop Swiss Precision
        function generatePopupHtml(p) {
            var formattedIn = p.jam_in ? p.jam_in : '-';
            var fotoInSrc = p.foto_in ? '/files/absensi/' + p.foto_in : null;
            var fotoOutSrc = p.foto_out ? '/files/absensi/' + p.foto_out : null;

            return `
                <div style="width: 300px; font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;">
                    <div class="popup-header">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; font-family: 'Outfit', sans-serif; color: #FAF9F8; flex-shrink: 0;">
                                ${getInitials(p.nama_karyawan)}
                            </div>
                            <div style="overflow: hidden; padding-right: 18px;">
                                <h6 class="mb-0 text-white fw-bold text-truncate" style="font-size: 13.5px; font-family: 'Outfit', sans-serif; letter-spacing: -0.01em;" title="${p.nama_karyawan}">${p.nama_karyawan}</h6>
                                <span style="font-size: 11px; color: rgba(250, 249, 248, 0.75);">NIK: ${p.nik} &bull; ${p.nama_cabang || '-'}</span>
                            </div>
                        </div>
                    </div>
                    <div class="popup-body">
                        <div class="row g-2 mb-2" style="font-size: 11.5px;">
                            <div class="col-6">
                                <div class="p-2 rounded" style="background: #FAF9F8; border: 1px solid rgba(60, 42, 33, 0.08);">
                                    <div style="font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: #755841; margin-bottom: 2px;">JAM MASUK</div>
                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; font-weight: 600; color: #3D5A35;">${formattedIn}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded" style="background: #FAF9F8; border: 1px solid rgba(60, 42, 33, 0.08);">
                                    <div style="font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: #755841; margin-bottom: 2px;">JAM KELUAR</div>
                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; font-weight: 600; color: #0F172A;">${formattedOut}</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-1.5 py-1 px-2 rounded mb-2" style="background: #F4F3F2; border: 1px solid rgba(60, 42, 33, 0.06); font-size: 11px; color: #4F4540;">
                            <i class="ti ti-map-pin" style="color: #755841; font-size: 13px;"></i>
                            <span class="text-truncate" style="font-family: 'JetBrains Mono', monospace; font-size: 10.5px;">${p.lokasi_in || '-'}</span>
                        </div>

                        <!-- Foto Presensi -->
                        <div class="pt-2" style="border-top: 1px solid rgba(60, 42, 33, 0.08);">
                            <div class="d-flex gap-3 justify-content-center">
                                ${fotoInSrc ? `
                                    <div class="text-center">
                                        <div style="border-radius: 8px; overflow: hidden; border: 1px solid rgba(60, 42, 33, 0.12); box-shadow: 0 2px 6px rgba(37, 22, 14, 0.04);">
                                            <img src="${fotoInSrc}"
                                                 style="width: 82px; height: 82px; object-fit: cover; display: block; cursor: pointer; transition: transform 0.15s ease;"
                                                 onmouseover="this.style.transform='scale(1.05)'"
                                                 onmouseout="this.style.transform='scale(1)'"
                                                 onclick="showImageModal('${fotoInSrc}', 'Foto Masuk - ${p.nama_karyawan}')"
                                                 onerror="this.parentElement.innerHTML='<div class=\\'text-muted small p-2\\'>Foto tdk ada</div>'">
                                        </div>
                                        <div style="margin-top: 4px;">
                                            <span style="display: inline-block; font-size: 9.5px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; color: #3D5A35; background: #F0F6EE; border: 1px solid #D2E4CC; padding: 1.5px 7px; border-radius: 4px;">Masuk</span>
                                        </div>
                                    </div>
                                ` : `
                                    <div class="text-center">
                                        <div style="width: 82px; height: 82px; background: #FAF9F8; border-radius: 8px; border: 1px dashed rgba(60, 42, 33, 0.15); display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 10px; color: #81756F;">
                                            <i class="ti ti-photo-off mb-1" style="font-size: 16px;"></i>No Foto
                                        </div>
                                        <div style="margin-top: 4px;">
                                            <span style="display: inline-block; font-size: 9.5px; font-weight: 600; color: #81756F;">Masuk</span>
                                        </div>
                                    </div>
                                `}

                                ${fotoOutSrc ? `
                                    <div class="text-center">
                                        <div style="border-radius: 8px; overflow: hidden; border: 1px solid rgba(60, 42, 33, 0.12); box-shadow: 0 2px 6px rgba(37, 22, 14, 0.04);">
                                            <img src="${fotoOutSrc}"
                                                 style="width: 82px; height: 82px; object-fit: cover; display: block; cursor: pointer; transition: transform 0.15s ease;"
                                                 onmouseover="this.style.transform='scale(1.05)'"
                                                 onmouseout="this.style.transform='scale(1)'"
                                                 onclick="showImageModal('${fotoOutSrc}', 'Foto Keluar - ${p.nama_karyawan}')"
                                                 onerror="this.parentElement.innerHTML='<div class=\\'text-muted small p-2\\'>Foto tdk ada</div>'">
                                        </div>
                                        <div style="margin-top: 4px;">
                                            <span style="display: inline-block; font-size: 9.5px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; color: var(--theme-color-2); background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.1)); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.2)); padding: 1.5px 7px; border-radius: 4px;">Keluar</span>
                                        </div>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Add Radius Circles for Offices (Warm Roastery Palette)
        function addRadiusCirclesToMap(data) {
            radiusCircles.forEach(function(circle) {
                map.removeLayer(circle);
            });
            radiusCircles = [];

            if (data && data.length > 0) {
                data.forEach(function(cabang) {
                    if (cabang.latitude && cabang.longitude && cabang.radius_cabang) {
                        var circle = L.circle([cabang.latitude, cabang.longitude], {
                            color: themeSecondary,
                            fillColor: themeSecondary,
                            fillOpacity: 0.08,
                            weight: 2,
                            radius: cabang.radius_cabang
                        }).addTo(map);

                        circle.bindPopup(`
                            <div style="padding: 8px 10px; min-width: 190px; font-family: 'Inter', sans-serif;">
                                <h6 class="fw-bold mb-1" style="color: ${themePrimary}; font-family: 'Outfit', sans-serif; font-size: 13.5px;"><i class="ti ti-building me-1" style="color: ${themeSecondary};"></i>${cabang.nama_cabang}</h6>
                                <p class="small text-muted mb-0" style="font-size: 11.5px;">Radius Absen: <strong style="color: ${themePrimary}; font-family: 'JetBrains Mono', monospace;">${cabang.radius_cabang} meter</strong></p>
                            </div>
                        `);

                        radiusCircles.push(circle);
                    }
                });
            }
        }

        // Add Presensi Markers via Cluster
        function addMarkersToMap(data) {
            markerClusterGroup.clearLayers();
            allMarkersMap = {};

            if (!data || data.length === 0) {
                updateStatistics(0, 0, 0);
                return;
            }

            var bounds = L.latLngBounds();
            var uniqueLocations = new Set();
            var overlapCount = 0;
            var markersToAdd = [];

            data.forEach(function(presensi) {
                if (presensi.latitude && presensi.longitude) {
                    var coordKey = (presensi.original_latitude || presensi.latitude) + ',' + (presensi.original_longitude || presensi.longitude);
                    if (uniqueLocations.has(coordKey)) {
                        overlapCount++;
                    } else {
                        uniqueLocations.add(coordKey);
                    }

                    var initials = getInitials(presensi.nama_karyawan);
                    var iconHtml = `
                        <div class="emp-marker-pin" title="${presensi.nama_karyawan}">
                            <span class="emp-initials">${initials}</span>
                            <div class="emp-status-dot"></div>
                            <div class="emp-name-tag">${presensi.nama_karyawan}</div>
                        </div>
                    `;

                    var customIcon = L.divIcon({
                        className: 'emp-marker',
                        html: iconHtml,
                        iconSize: [40, 40],
                        iconAnchor: [20, 20],
                        popupAnchor: [0, -20]
                    });

                    var marker = L.marker([presensi.latitude, presensi.longitude], {
                        icon: customIcon
                    });

                    // Lazy popup attachment
                    marker.bindPopup(function() {
                        return generatePopupHtml(presensi);
                    }, {
                        maxWidth: 320,
                        minWidth: 280,
                        autoPanPadding: [30, 30]
                    });

                    markersToAdd.push(marker);
                    allMarkersMap[presensi.nik] = {
                        marker: marker,
                        data: presensi
                    };

                    bounds.extend([presensi.latitude, presensi.longitude]);
                }
            });

            // Fast batch addition
            markerClusterGroup.addLayers(markersToAdd);

            // Update stats
            updateStatistics(markersToAdd.length, uniqueLocations.size, overlapCount);

            // Fit map
            if (markersToAdd.length > 0 && bounds.isValid()) {
                if (bounds.getNorthEast().distanceTo(bounds.getSouthWest()) < 500) {
                    map.setView(bounds.getCenter(), 14);
                } else {
                    map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });
                }
            }
        }

        // Center Map on Cabang
        function centerMapOnCabang(cabangData) {
            if (cabangData && cabangData.length > 0) {
                if (cabangData.length === 1) {
                    var c = cabangData[0];
                    if (c.latitude && c.longitude) {
                        map.setView([c.latitude, c.longitude], 15, { animate: true });
                    }
                } else {
                    var b = L.latLngBounds();
                    cabangData.forEach(function(c) {
                        if (c.latitude && c.longitude) b.extend([c.latitude, c.longitude]);
                    });
                    if (b.isValid()) {
                        if (b.getNorthEast().distanceTo(b.getSouthWest()) < 500) {
                            map.setView(b.getCenter(), 14);
                        } else {
                            map.fitBounds(b, { padding: [30, 30], maxZoom: 15 });
                        }
                    }
                }
            }
        }

        // Update Statistics
        function updateStatistics(total, locations, overlap) {
            $('#stat-total').text(total.toLocaleString('id-ID'));
            $('#stat-locations').text(locations.toLocaleString('id-ID'));
            $('#stat-overlap').text(overlap.toLocaleString('id-ID'));
        }

        // Modal Photo Zoom
        window.showImageModal = function(imageSrc, title) {
            $('#imageModalTitle').html('<i class="ti ti-photo me-2"></i>' + title);
            $('#modalImage').attr('src', imageSrc);
            $('#downloadImage').attr('href', imageSrc);
            $('#imageModal').modal('show');
        };

        // Quick Search Employee In Map
        var searchDebounce = null;
        $('#map-emp-search').on('input', function() {
            var query = $(this).val().toLowerCase().trim();
            if (query.length > 0) {
                $('#search-clear').show();
            } else {
                $('#search-clear').hide();
            }

            clearTimeout(searchDebounce);
            searchDebounce = setTimeout(function() {
                if (!query) return;

                var found = null;
                for (var nik in allMarkersMap) {
                    var item = allMarkersMap[nik];
                    if (item.data.nama_karyawan.toLowerCase().includes(query) || item.data.nik.toLowerCase().includes(query)) {
                        found = item;
                        break;
                    }
                }

                if (found) {
                    var targetLatLng = found.marker.getLatLng();
                    map.setView(targetLatLng, 18, { animate: true });
                    markerClusterGroup.zoomToShowLayer(found.marker, function() {
                        found.marker.openPopup();
                    });
                }
            }, 300);
        });

        $('#search-clear').click(function() {
            $('#map-emp-search').val('').trigger('input');
        });

        // Initial Load
        addMarkersToMap(presensiData);
        addRadiusCirclesToMap(cabangRadiusData);

        // Filter Button
        $('#btn-filter').click(function() {
            var tanggal = $('#tanggal').val();
            var kode_cabang = $('#kode_cabang').val();

            if (!tanggal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan tentukan tanggal presensi',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // AJAX request with smooth loading indicator
            var $btn = $(this);
            var origHtml = $btn.html();
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Memuat...');

            $.ajax({
                url: '{{ route('trackingpresensi.getData') }}',
                type: 'GET',
                data: {
                    tanggal: tanggal,
                    kode_cabang: kode_cabang
                },
                success: function(response) {
                    $btn.prop('disabled', false).html(origHtml);
                    addMarkersToMap(response.presensis);
                    addRadiusCirclesToMap(response.cabangRadius);

                    if (kode_cabang && response.cabangRadius) {
                        centerMapOnCabang(response.cabangRadius);
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html(origHtml);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat memuat data presensi',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        // Reset Button
        $('#btn-reset').click(function() {
            $('#tanggal').val('{{ $tanggal }}');
            $('#kode_cabang').val('');
            $('#map-emp-search').val('').trigger('input');
            $('#btn-filter').click();
        });

        // Toggle Radius Button (Cohesive Roasted Walnut & Secondary Styling)
        var radiusVisible = true;
        $('#btn-toggle-radius').click(function() {
            if (radiusVisible) {
                radiusCircles.forEach(function(circle) {
                    map.removeLayer(circle);
                });
                $(this).html('<i class="ti ti-circle-off me-1"></i><span>Radius Sembunyi</span>');
                $(this).attr('style', 'background-color: #FFFFFF !important; border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.18)) !important; color: var(--theme-color-1) !important; border-radius: 8px; font-weight: 600; height: 38px;');
                radiusVisible = false;
            } else {
                addRadiusCirclesToMap(cabangRadiusData);
                $(this).html('<i class="ti ti-circle me-1"></i><span>Radius Kantor</span>');
                $(this).attr('style', 'background-color: var(--theme-color-2) !important; border-color: var(--theme-color-2) !important; color: var(--theme-primary-contrast, #FFFFFF) !important; border-radius: 8px; font-weight: 600; height: 38px;');
                radiusVisible = true;
            }
        });

        // Cabang dropdown change
        $('#kode_cabang').change(function() {
            var selectedCabang = $(this).val();
            if (selectedCabang) {
                var filtered = cabangRadiusData.filter(function(c) {
                    return c.kode_cabang === selectedCabang;
                });
                if (filtered.length > 0) {
                    centerMapOnCabang(filtered);
                }
            }
        });
    });
</script>
@endpush
