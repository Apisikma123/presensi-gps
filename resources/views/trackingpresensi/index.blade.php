@extends('layouts.app')
@section('titlepage', 'Tracking Presensi')

@section('content')
@section('navigasi')
    <span>Tracking Presensi</span>
@endsection
@push('mystyle')
<link rel="stylesheet" href="{{ asset('assets/vendor/css/leaflet.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/css/MarkerCluster.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/css/MarkerCluster.Default.css') }}" />
<style>
    /* Map container styling */
    #map-wrapper {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
    }
    #map {
        height: 640px;
        width: 100%;
        background: #f8fafc;
    }

    /* Fast cluster styling */
    .custom-cluster-marker {
        background: transparent !important;
        border: none !important;
    }
    .custom-cluster-marker .cluster-inner {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: #ffffff;
        font-weight: 700;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
        transition: transform 0.15s ease;
        will-change: transform;
    }
    .custom-cluster-marker:hover .cluster-inner {
        transform: scale(1.12);
    }
    .marker-cluster-small .cluster-inner {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, #10B981, #059669);
        border: 3px solid rgba(255, 255, 255, 0.9);
        font-size: 13px;
    }
    .marker-cluster-medium .cluster-inner {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #1E4D3E, #14352B);
        border: 3px solid rgba(255, 255, 255, 0.95);
        font-size: 14px;
    }
    .marker-cluster-large .cluster-inner {
        width: 52px;
        height: 52px;
        background: linear-gradient(135deg, #F59E0B, #D97706);
        border: 3px solid rgba(255, 255, 255, 0.95);
        font-size: 15px;
    }

    /* Individual Marker Styling (GPU accelerated & lightweight) */
    .emp-marker {
        background: transparent !important;
        border: none !important;
    }
    .emp-marker-pin {
        position: relative;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2.5px solid #1E4D3E;
        background: #ffffff;
        box-shadow: 0 3px 10px rgba(0,0,0,0.22);
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
        color: #1E4D3E;
        text-transform: uppercase;
    }
    .emp-status-dot {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 13px;
        height: 13px;
        border-radius: 50%;
        background: #10B981;
        border: 2px solid #ffffff;
    }
    .emp-name-tag {
        position: absolute;
        bottom: -22px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(15, 23, 42, 0.88);
        color: #ffffff;
        padding: 2px 7px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 600;
        white-space: nowrap;
        pointer-events: none;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        max-width: 120px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Popup Styling */
    .leaflet-popup-content-wrapper {
        border-radius: 12px;
        padding: 0;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
    }
    .leaflet-popup-content {
        margin: 0;
        line-height: 1.4;
    }
    .popup-header {
        background: linear-gradient(135deg, #1E4D3E, #2d6a56);
        color: white;
        padding: 12px 16px;
    }
    .popup-body {
        padding: 14px 16px;
        background: #ffffff;
    }

    /* Search floating overlay */
    .map-search-bar {
        position: absolute;
        top: 14px;
        left: 60px;
        z-index: 1000;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        padding: 6px 12px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
        border: 1px solid rgba(226, 232, 240, 0.8);
        display: flex;
        align-items: center;
        gap: 8px;
        width: 280px;
    }
    .map-search-bar input {
        border: none;
        outline: none;
        background: transparent;
        font-size: 13px;
        width: 100%;
        color: #1e293b;
    }
    .map-search-bar input::placeholder {
        color: #94a3b8;
    }

    /* Quick stats chip bar */
    .stats-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }
    .stat-chip {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        padding: 8px 16px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .stat-chip-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }
</style>
@endpush

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-transparent border-bottom pb-3 pt-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="card-title mb-1 text-dark fw-bold">Live Tracking Presensi Karyawan</h5>
                        <p class="text-muted small mb-0">Pemetaan lokasi kehadiran karyawan secara real-time dan berkinerja tinggi</p>
                    </div>
                    <!-- Quick Stats Chips -->
                    <div class="stats-chips">
                        <div class="stat-chip">
                            <div class="stat-chip-icon" style="background: rgba(30, 77, 62, 0.1); color: #1E4D3E;">
                                <i class="ti ti-users"></i>
                            </div>
                            <div>
                                <div class="text-muted" style="font-size: 11px; font-weight: 500;">Total Presensi</div>
                                <div class="fw-bold text-dark" id="stat-total" style="font-size: 15px;">0</div>
                            </div>
                        </div>
                        <div class="stat-chip">
                            <div class="stat-chip-icon" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">
                                <i class="ti ti-map-pin"></i>
                            </div>
                            <div>
                                <div class="text-muted" style="font-size: 11px; font-weight: 500;">Titik Lokasi</div>
                                <div class="fw-bold text-dark" id="stat-locations" style="font-size: 15px;">0</div>
                            </div>
                        </div>
                        <div class="stat-chip">
                            <div class="stat-chip-icon" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B;">
                                <i class="ti ti-stack"></i>
                            </div>
                            <div>
                                <div class="text-muted" style="font-size: 11px; font-weight: 500;">Cluster Overlap</div>
                                <div class="fw-bold text-dark" id="stat-overlap" style="font-size: 15px;">0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label for="tanggal" class="form-label fw-semibold text-muted small">Tanggal Presensi</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text bg-light border-end-0"><i class="ti ti-calendar text-muted"></i></span>
                            <input type="text" class="form-control flatpickr-date bg-light border-start-0" id="tanggal" name="tanggal"
                                value="{{ $tanggal }}" placeholder="Pilih tanggal">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="kode_cabang" class="form-label fw-semibold text-muted small">Filter Cabang</label>
                        <select class="form-select bg-light" id="kode_cabang" name="kode_cabang">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabangs as $cabang)
                                <option value="{{ $cabang->kode_cabang }}">{{ $cabang->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5 d-flex align-items-end">
                        <div class="d-flex gap-2 w-100 flex-wrap">
                            <button type="button" class="btn text-white d-inline-flex align-items-center gap-1 shadow-sm px-3" id="btn-filter"
                                style="background-color: #1E4D3E !important; border-color: #1E4D3E !important; border-radius: 8px; font-weight: 600;">
                                <i class="ti ti-filter me-1"></i><span>Terapkan</span>
                            </button>
                            <button type="button" class="btn text-white d-inline-flex align-items-center gap-1 shadow-sm px-3" id="btn-reset"
                                style="background-color: #64748B !important; border-color: #64748B !important; border-radius: 8px; font-weight: 600;">
                                <i class="ti ti-refresh me-1"></i><span>Reset</span>
                            </button>
                            <button type="button" class="btn text-white d-inline-flex align-items-center gap-1 shadow-sm px-3 ms-auto" id="btn-toggle-radius"
                                style="background-color: #0284C7 !important; border-color: #0284C7 !important; border-radius: 8px; font-weight: 600;">
                                <i class="ti ti-circle me-1"></i><span>Radius Kantor</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Map Container -->
                <div id="map-wrapper">
                    <!-- Quick search inside map -->
                    <div class="map-search-bar">
                        <i class="ti ti-search text-muted" style="font-size: 15px;"></i>
                        <input type="text" id="map-emp-search" placeholder="Cari nama karyawan / NIK...">
                        <span id="search-clear" style="cursor: pointer; display: none;"><i class="ti ti-x text-muted"></i></span>
                    </div>
                    <div id="map"></div>
                </div>

                <!-- Guidance footer -->
                <div class="d-flex justify-content-between align-items-center mt-3 text-muted small flex-wrap gap-2">
                    <div>
                        <i class="ti ti-bulb text-warning me-1"></i> <strong>Tips:</strong> Klik angka cluster untuk memperbesar area kelompok presensi. Klik marker untuk rincian presensi & foto.
                    </div>
                    <div>
                        <span class="badge bg-label-success me-1"><i class="ti ti-circle-filled me-1" style="font-size: 8px;"></i> Cluster Cepat Aktif</span>
                        <span class="badge bg-label-primary"><i class="ti ti-sparkles me-1"></i> GPU Virtualized</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk menampilkan foto dalam ukuran besar -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header py-3" style="background: #1E4D3E; color: white;">
                <h6 class="modal-title text-white fw-bold mb-0" id="imageModalTitle"><i class="ti ti-photo me-2"></i>Foto Presensi</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3 bg-light">
                <img id="modalImage" src="" alt="Foto Presensi" class="img-fluid rounded shadow-sm"
                    style="max-height: 65vh; object-fit: contain;">
            </div>
            <div class="modal-footer py-2 bg-white">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                <a id="downloadImage" href="" download class="btn btn-sm text-white" style="background: #1E4D3E;">
                    <i class="ti ti-download me-1"></i>Download Foto
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
        // Initialize flatpickr for date input
        $('.flatpickr-date').flatpickr({
            dateFormat: 'Y-m-d',
            defaultDate: '{{ $tanggal }}'
        });

        // Initialize map with smooth panning
        var map = L.map('map', {
            zoomControl: false,
            preferCanvas: true
        }).setView([-6.2088, 106.8456], 10);

        // Position zoom control on top right
        L.control.zoom({ position: 'topright' }).addTo(map);

        // Define base tile layers
        var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        });

        var googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });

        var googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });

        var googleSat = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });

        // Add default layer
        osm.addTo(map);

        // Layer control on top right
        var baseMaps = {
            "OpenStreetMap": osm,
            "Google Streets": googleStreets,
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

        // Lazy Popup Content Generator (Only generated on click!)
        function generatePopupHtml(p) {
            var formattedIn = p.jam_in ? p.jam_in : '-';
            var formattedOut = p.jam_out ? p.jam_out : '-';
            var fotoInSrc = p.foto_in ? '/storage/uploads/absensi/' + p.foto_in : null;
            var fotoOutSrc = p.foto_out ? '/storage/uploads/absensi/' + p.foto_out : null;

            return `
                <div style="width: 290px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                    <div class="popup-header">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px;">
                                ${getInitials(p.nama_karyawan)}
                            </div>
                            <div style="overflow: hidden;">
                                <h6 class="mb-0 text-white fw-bold text-truncate" style="font-size: 13.5px;" title="${p.nama_karyawan}">${p.nama_karyawan}</h6>
                                <span style="font-size: 11px; opacity: 0.85;">NIK: ${p.nik} &bull; ${p.nama_cabang || '-'}</span>
                            </div>
                        </div>
                    </div>
                    <div class="popup-body">
                        <div class="row g-2 mb-2" style="font-size: 11.5px;">
                            <div class="col-6">
                                <div class="p-2 rounded" style="background: #ecfdf5; border: 1px solid #d1fae5;">
                                    <div class="text-muted" style="font-size: 10px;">JAM MASUK</div>
                                    <div class="fw-bold text-success">${formattedIn}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <div class="text-muted" style="font-size: 10px;">JAM KELUAR</div>
                                    <div class="fw-bold text-dark">${formattedOut}</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-1 text-muted mb-2" style="font-size: 11px;">
                            <i class="ti ti-map-pin text-danger"></i>
                            <span class="text-truncate">${p.lokasi_in || '-'}</span>
                        </div>

                        <!-- Foto Presensi -->
                        <div class="pt-2 border-top">
                            <div class="d-flex gap-2 justify-content-center">
                                ${fotoInSrc ? `
                                    <div class="text-center">
                                        <img src="${fotoInSrc}"
                                             style="width: 75px; height: 75px; object-fit: cover; border-radius: 8px; border: 2px solid #10B981; cursor: pointer;"
                                             onclick="showImageModal('${fotoInSrc}', 'Foto Masuk - ${p.nama_karyawan}')"
                                             onerror="this.parentElement.innerHTML='<div class=\\'text-muted small\\'>Foto tdk ada</div>'">
                                        <div style="font-size: 9.5px; margin-top: 3px; font-weight: 600; color: #10B981;">MASUK</div>
                                    </div>
                                ` : `
                                    <div style="width: 75px; height: 75px; background: #f1f5f9; border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 10px; color: #94a3b8;">
                                        <i class="ti ti-photo-off mb-1"></i>No Foto
                                    </div>
                                `}

                                ${fotoOutSrc ? `
                                    <div class="text-center">
                                        <img src="${fotoOutSrc}"
                                             style="width: 75px; height: 75px; object-fit: cover; border-radius: 8px; border: 2px solid #0284C7; cursor: pointer;"
                                             onclick="showImageModal('${fotoOutSrc}', 'Foto Keluar - ${p.nama_karyawan}')"
                                             onerror="this.parentElement.innerHTML='<div class=\\'text-muted small\\'>Foto tdk ada</div>'">
                                        <div style="font-size: 9.5px; margin-top: 3px; font-weight: 600; color: #0284C7;">KELUAR</div>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Add Radius Circles for Offices
        function addRadiusCirclesToMap(data) {
            radiusCircles.forEach(function(circle) {
                map.removeLayer(circle);
            });
            radiusCircles = [];

            if (data && data.length > 0) {
                data.forEach(function(cabang) {
                    if (cabang.latitude && cabang.longitude && cabang.radius_cabang) {
                        var circle = L.circle([cabang.latitude, cabang.longitude], {
                            color: '#1E4D3E',
                            fillColor: '#10B981',
                            fillOpacity: 0.12,
                            weight: 2,
                            radius: cabang.radius_cabang
                        }).addTo(map);

                        circle.bindPopup(`
                            <div style="padding: 10px; min-width: 180px;">
                                <h6 class="fw-bold mb-1" style="color: #1E4D3E;"><i class="ti ti-building me-1"></i>${cabang.nama_cabang}</h6>
                                <p class="small text-muted mb-0">Radius Absen: <strong>${cabang.radius_cabang} meter</strong></p>
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
                map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });
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
                    if (b.isValid()) map.fitBounds(b, { padding: [30, 30] });
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

        // Toggle Radius
        var radiusVisible = true;
        $('#btn-toggle-radius').click(function() {
            if (radiusVisible) {
                radiusCircles.forEach(function(circle) {
                    map.removeLayer(circle);
                });
                $(this).html('<i class="ti ti-circle-off me-1"></i><span>Radius Sembunyi</span>');
                $(this).css('background-color', '#64748B !important');
                radiusVisible = false;
            } else {
                addRadiusCirclesToMap(cabangRadiusData);
                $(this).html('<i class="ti ti-circle me-1"></i><span>Radius Kantor</span>');
                $(this).css('background-color', '#0284C7 !important');
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

