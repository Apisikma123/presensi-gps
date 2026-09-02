@extends('layouts.app')
@section('titlepage', 'Kiosk Face Recognition')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Kiosk Face Recognition
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Pusat manajemen terminal absensi wajah outlet, status biometrik AI, dan pembuatan QR Card ID karyawan.
            </div>
        </div>
        <nav aria-label="breadcrumb" class="d-none d-md-block" style="font-size: 0.75rem;">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard.index') }}">
                        <i class="ti ti-home-2 ti-xs"></i>
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">
                        <i class="ti ti-camera ti-xs me-1"></i> Presensi
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="ti ti-scan ti-xs me-1"></i> Kiosk Face Recognition
                </li>
            </ol>
        </nav>
    </div>
@endsection

<!-- Stat Counters Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar avatar-lg bg-label-primary rounded p-2">
                    <i class="ti ti-users ti-md"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">{{ $total_karyawan ?? 0 }}</h5>
                    <small class="text-muted">Karyawan Aktif Outlet</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar avatar-lg bg-label-success rounded p-2">
                    <i class="ti ti-face-id ti-md"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">{{ $total_biometric ?? 0 }}</h5>
                    <small class="text-muted">Sample Dataset Wajah AI</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar avatar-lg bg-label-info rounded p-2">
                    <i class="ti ti-device-tablet ti-md"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-success">Live Ready</h5>
                    <small class="text-muted">Engine Face API v0.22</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Cards Grid -->
<div class="row g-4">
    <!-- Left Column: Kiosk Launcher -->
    <div class="col-lg-7 col-md-12">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center py-3">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                    <i class="ti ti-device-tablet text-primary"></i>
                    <span>Terminal Kiosk Absensi Outlet</span>
                </h5>
                <span class="badge bg-label-success">Kamera Siap</span>
            </div>
            <div class="card-body d-flex flex-col justify-content-between pt-4">
                <div>
                    <p class="text-muted mb-4" style="line-height: 1.6;">
                        Gunakan mode terminal ini pada tablet / layar kasir outlet. Karyawan (barista, kasir, kitchen) cukup berdiri di depan kamera untuk langsung diverifikasi wajahnya tanpa perlu login manual.
                    </p>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-4">
                            <div class="p-3 bg-light rounded text-center">
                                <i class="ti ti-bolt text-warning ti-md mb-1"></i>
                                <div class="fw-bold fs-6">Instan</div>
                                <small class="text-muted" style="font-size: 0.75rem;">Deteksi < 1 Detik</small>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 bg-light rounded text-center">
                                <i class="ti ti-shield-check text-success ti-md mb-1"></i>
                                <div class="fw-bold fs-6">Anti-Spoof</div>
                                <small class="text-muted" style="font-size: 0.75rem;">Verifikasi Liveness</small>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 bg-light rounded text-center">
                                <i class="ti ti-clock text-primary ti-md mb-1"></i>
                                <div class="fw-bold fs-6">Auto Shift</div>
                                <small class="text-muted" style="font-size: 0.75rem;">Masuk & Pulang</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-top">
                    <a href="{{ route('facerecognition-presensi.scan_any') }}" target="_blank" class="btn btn-primary w-100 py-2 fs-6 fw-semibold d-flex align-items-center justify-content-center gap-2">
                        <i class="ti ti-camera ti-sm"></i>
                        <span>Luncurkan Terminal Kiosk Absen</span>
                        <i class="ti ti-external-link ti-sm ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: QR Code ID Generator -->
    <div class="col-lg-5 col-md-12">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header border-bottom py-3">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                    <i class="ti ti-qrcode text-primary"></i>
                    <span>Generate QR Card Karyawan</span>
                </h5>
            </div>
            <div class="card-body pt-4">
                <p class="text-muted mb-3" style="font-size: 0.85rem;">
                    Pilih karyawan untuk melihat dan mencetak QR Code ID Badge untuk absensi scan.
                </p>

                <form id="qrForm" class="mb-3">
                    <div class="form-group mb-3">
                        <label class="form-label fw-semibold" for="select_karyawan">PILIH KARYAWAN</label>
                        <select class="form-select select2" id="select_karyawan" name="nik" required>
                            <option value="">-- Cari Nama / NIK Karyawan --</option>
                            @foreach ($karyawan as $k)
                                <option value="{{ $k->nik }}">{{ $k->nama_karyawan }} ({{ $k->nik }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-label-primary w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="ti ti-qrcode"></i>
                        <span>Tampilkan QR Code</span>
                    </button>
                </form>

                <div id="errorMessage" class="alert alert-danger d-none py-2 px-3 mb-3" style="font-size: 0.8rem;"></div>

                <div id="qrResult" class="d-none text-center p-3 bg-light rounded border">
                    <div id="qrCode" class="d-inline-block p-2 bg-white rounded border shadow-sm mb-2"></div>
                    <div id="employeeInfo" class="mt-1"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(document).ready(function() {
        if ($('.select2').length) {
            $('.select2').select2({
                dropdownParent: $('body')
            });
        }

        $('#qrForm').on('submit', function(e) {
            e.preventDefault();
            const nik = $('#select_karyawan').val();
            const $error = $('#errorMessage');
            const $result = $('#qrResult');

            $error.addClass('d-none');
            $result.addClass('d-none');

            if (!nik) {
                $error.text('Silakan pilih karyawan terlebih dahulu').removeClass('d-none');
                return;
            }

            $.ajax({
                url: '/facerecognition-presensi/generate/' + nik,
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mengambil data QR Code karyawan',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    Swal.close();
                    if (response.status) {
                        $('#qrCode').html('<img src="data:image/png;base64,' + response.qr_code + '" alt="QR Code" class="img-fluid" style="width: 140px; height: 140px;">');
                        $('#employeeInfo').html(
                            '<div class="fw-bold text-dark fs-6">' + response.karyawan.nama_karyawan + '</div>' +
                            '<div class="text-muted font-monospace small">NIK: ' + response.karyawan.nik + '</div>' +
                            '<div class="mt-2"><span class="badge bg-label-' + (response.karyawan.status_aktif_karyawan == '1' ? 'success' : 'danger') + '">' +
                            (response.karyawan.status_aktif_karyawan == '1' ? 'Aktif' : 'Non-Aktif') + '</span></div>'
                        );
                        $result.removeClass('d-none');
                    } else {
                        $error.text(response.message || 'Karyawan tidak ditemukan').removeClass('d-none');
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    $error.text('Terjadi kesalahan saat memproses data').removeClass('d-none');
                }
            });
        });
    });
</script>
@endpush
