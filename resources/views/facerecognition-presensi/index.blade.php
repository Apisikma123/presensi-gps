@extends('layouts.app')
@section('titlepage', 'Kiosk Face Recognition')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="ti ti-scan text-primary"></i>
                <span>Kiosk Face Recognition</span>
            </h5>
            <div class="text-muted mt-1" style="font-size: 0.8rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Pusat manajemen terminal absensi biometrik wajah outlet, verifikasi liveness AI, dan pemantauan dataset wajah karyawan.
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

<!-- Stat Counters Row (Bento Grid) -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar avatar-lg rounded p-2" style="background-color: rgba(30, 77, 62, 0.1); color: var(--theme-color-1, #1E4D3E);">
                    <i class="ti ti-users ti-md"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold text-dark">{{ $total_karyawan ?? 0 }}</h4>
                    <small class="text-muted fw-semibold">Total Karyawan Aktif</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar avatar-lg rounded p-2" style="background-color: #ECFDF5; color: #059669;">
                    <i class="ti ti-face-id ti-md"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h4 class="mb-0 fw-bold text-dark">{{ $total_terdaftar_wajah ?? 0 }}</h4>
                        <span class="badge bg-label-success" style="font-size: 11px;">
                            {{ $total_karyawan > 0 ? round(($total_terdaftar_wajah / $total_karyawan) * 100) : 0 }}%
                        </span>
                    </div>
                    <small class="text-muted fw-semibold">Wajah Terdaftar AI</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar avatar-lg rounded p-2" style="background-color: #FFFBEB; color: #D97706;">
                    <i class="ti ti-user-exclamation ti-md"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold text-dark">{{ $total_belum_daftar ?? 0 }}</h4>
                    <small class="text-muted fw-semibold">Belum Registrasi Wajah</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar avatar-lg rounded p-2" style="background-color: #F0FDF4; color: #16A34A;">
                    <i class="ti ti-database ti-md"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold text-dark">{{ $total_biometric ?? 0 }}</h4>
                    <small class="text-muted fw-semibold">Total Sample Dataset AI</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Section: Kiosk Terminal & Biometric Action -->
<div class="row g-4 mb-4">
    <!-- Left Column: Kiosk Launcher -->
    <div class="col-lg-7 col-md-12">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center py-3">
                <h6 class="card-title mb-0 d-flex align-items-center gap-2 fw-bold text-dark">
                    <i class="ti ti-device-tablet text-primary"></i>
                    <span>Terminal Kiosk Absensi Outlet</span>
                </h6>
                <span class="badge bg-label-success d-inline-flex align-items-center gap-1">
                    <span class="badge-dot bg-success" style="width: 8px; height: 8px; border-radius: 50%; display: inline-block;"></span>
                    <span>AI Engine Siap</span>
                </span>
            </div>
            <div class="card-body d-flex flex-column justify-content-between pt-4">
                <div>
                    <p class="text-muted mb-4" style="line-height: 1.6; font-size: 13.5px;">
                        Gunakan mode terminal ini pada perangkat tablet atau layar kasir outlet. Karyawan (barista, kasir, kitchen) cukup berdiri di depan kamera tablet untuk diverifikasi wajahnya secara otomatis tanpa perlu login manual.
                    </p>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-4">
                            <div class="p-3 rounded text-center border" style="background-color: #F8FAFC;">
                                <i class="ti ti-bolt text-warning ti-md mb-2"></i>
                                <div class="fw-bold text-dark" style="font-size: 13px;">Deteksi Instan</div>
                                <small class="text-muted" style="font-size: 11.5px;">Respon < 1 Detik</small>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 rounded text-center border" style="background-color: #F8FAFC;">
                                <i class="ti ti-shield-check text-success ti-md mb-2"></i>
                                <div class="fw-bold text-dark" style="font-size: 13px;">Anti-Spoofing</div>
                                <small class="text-muted" style="font-size: 11.5px;">Verifikasi Liveness</small>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 rounded text-center border" style="background-color: #F8FAFC;">
                                <i class="ti ti-clock-check text-primary ti-md mb-2"></i>
                                <div class="fw-bold text-dark" style="font-size: 13px;">Auto Shift</div>
                                <small class="text-muted" style="font-size: 11.5px;">Masuk & Pulang</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-top">
                    <a href="{{ route('facerecognition-presensi.scan_any') }}" target="_blank" class="btn btn-primary w-100 py-2 fs-6 fw-semibold d-flex align-items-center justify-content-center gap-2 shadow-sm">
                        <i class="ti ti-camera ti-sm"></i>
                        <span>Luncurkan Terminal Kiosk Absen</span>
                        <i class="ti ti-external-link ti-sm ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Quick Registrasi Wajah Karyawan -->
    <div class="col-lg-5 col-md-12">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header border-bottom py-3">
                <h6 class="card-title mb-0 d-flex align-items-center gap-2 fw-bold text-dark">
                    <i class="ti ti-camera-plus text-primary"></i>
                    <span>Registrasi Wajah Cepat</span>
                </h6>
            </div>
            <div class="card-body pt-4">
                <p class="text-muted mb-3" style="font-size: 13px; line-height: 1.5;">
                    Pilih karyawan untuk memeriksa dataset biometrik wajah atau melakukan perekaman dataset sample wajah baru.
                </p>

                <div class="form-group mb-3">
                    <label class="form-label fw-semibold" for="select_karyawan">PILIH KARYAWAN</label>
                    <select class="form-select select2" id="select_karyawan" name="nik">
                        <option value="">-- Cari Nama / NIK Karyawan --</option>
                        @foreach ($karyawan as $k)
                            <option value="{{ $k->nik }}" 
                                data-hasface="{{ $k->has_face ? '1' : '0' }}" 
                                data-facecount="{{ $k->face_count }}" 
                                data-nama="{{ $k->nama_karyawan }}" 
                                data-cabang="{{ $k->nama_cabang ?? '-' }}" 
                                data-dept="{{ $k->nama_dept ?? '-' }}"
                                data-url="{{ route('karyawan.show', Crypt::encrypt($k->nik)) }}">
                                {{ $k->nama_karyawan }} ({{ $k->nik }}) {{ $k->has_face ? '✓ Terdaftar' : '⚠ Belum' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="quickEmployeeStatus" class="p-3 bg-light rounded border mb-3 d-none">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-dark" id="statusNamaKaryawan">-</span>
                        <span id="statusBadgeBiometrik" class="badge bg-label-secondary">-</span>
                    </div>
                    <div class="text-muted small mb-2" id="statusInfoKaryawan">-</div>
                    <div class="d-flex gap-2 mt-3">
                        <a href="#" id="btnManageFace" class="btn btn-sm btn-primary w-100 d-flex align-items-center justify-content-center gap-1">
                            <i class="ti ti-face-id ti-xs"></i>
                            <span>Buka Manajemen Wajah</span>
                        </a>
                    </div>
                </div>

                <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 12.5px; border-radius: 8px;">
                    <div class="d-flex gap-2">
                        <i class="ti ti-info-circle fs-5 mt-1 text-primary"></i>
                        <div>
                            <strong>Tips Akurasi AI:</strong> Rekam minimal 3-5 sample wajah per karyawan dengan variasi ekspresi dan pencahayaan yang jelas tanpa masker/kacamata hitam.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Section: Daftar Status Biometrik Karyawan -->
<div class="card border-0 shadow-sm">
    <div class="card-header border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h6 class="card-title mb-0 d-flex align-items-center gap-2 fw-bold text-dark">
            <i class="ti ti-list-check text-primary"></i>
            <span>Daftar Status Biometrik Wajah Karyawan</span>
        </h6>
        <div class="d-flex align-items-center gap-2">
            <div class="input-group input-group-sm" style="width: 220px;">
                <span class="input-group-text"><i class="ti ti-search"></i></span>
                <input type="text" id="searchTable" class="form-control" placeholder="Cari karyawan...">
            </div>
            <select id="filterFaceStatus" class="form-select form-select-sm" style="width: 150px;">
                <option value="all">Semua Status</option>
                <option value="registered">Terdaftar Wajah</option>
                <option value="unregistered">Belum Terdaftar</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="biometricTable">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Karyawan</th>
                    <th>Cabang</th>
                    <th>Departemen</th>
                    <th>Status Biometrik</th>
                    <th class="text-end" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($karyawan as $index => $k)
                    <tr data-status="{{ $k->has_face ? 'registered' : 'unregistered' }}">
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar avatar-sm">
                                    @if (!empty($k->foto) && Storage::disk('public')->exists('karyawan/' . $k->foto))
                                        <img src="{{ asset('storage/karyawan/' . $k->foto) }}" alt="Avatar" class="rounded-circle object-fit-cover">
                                    @else
                                        <span class="avatar-initial rounded-circle bg-label-primary fw-bold">
                                            {{ substr($k->nama_karyawan, 0, 2) }}
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $k->nama_karyawan }}</div>
                                    <small class="text-muted font-monospace">NIK: {{ $k->nik }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-dark">{{ $k->nama_cabang ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="text-dark">{{ $k->nama_dept ?? '-' }}</span>
                        </td>
                        <td>
                            @if ($k->has_face)
                                <span class="badge bg-label-success d-inline-flex align-items-center gap-1">
                                    <i class="ti ti-check ti-xs"></i>
                                    <span>Terdaftar ({{ $k->face_count }} Sample)</span>
                                </span>
                            @else
                                <span class="badge bg-label-warning d-inline-flex align-items-center gap-1">
                                    <i class="ti ti-alert-triangle ti-xs"></i>
                                    <span>Belum Registrasi</span>
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('karyawan.show', Crypt::encrypt($k->nik)) }}#wajah" class="btn btn-sm {{ $k->has_face ? 'btn-outline-primary' : 'btn-primary' }} d-inline-flex align-items-center gap-1" title="{{ $k->has_face ? 'Kelola Sample Wajah' : 'Daftarkan Wajah' }}">
                                <i class="ti {{ $k->has_face ? 'ti-settings' : 'ti-camera-plus' }} ti-xs"></i>
                                <span>{{ $k->has_face ? 'Kelola' : 'Daftarkan' }}</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="ti ti-database-off fs-2 d-block mb-1"></i>
                            Tidak ada data karyawan aktif.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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

        // Quick Select Handler
        $('#select_karyawan').on('change', function() {
            const $selected = $(this).find(':selected');
            const nik = $(this).val();
            const $box = $('#quickEmployeeStatus');

            if (!nik) {
                $box.addClass('d-none');
                return;
            }

            const nama = $selected.data('nama');
            const cabang = $selected.data('cabang');
            const dept = $selected.data('dept');
            const hasFace = $selected.data('hasface') == '1';
            const faceCount = $selected.data('facecount');
            const url = $selected.data('url') + '#wajah';

            $('#statusNamaKaryawan').text(nama + ' (' + nik + ')');
            $('#statusInfoKaryawan').text('Cabang: ' + cabang + ' • Departemen: ' + dept);
            
            if (hasFace) {
                $('#statusBadgeBiometrik')
                    .removeClass('bg-label-warning bg-label-secondary')
                    .addClass('bg-label-success')
                    .html('<i class="ti ti-check ti-xs me-1"></i>Terdaftar (' + faceCount + ' Sample)');
                $('#btnManageFace').html('<i class="ti ti-settings ti-xs me-1"></i>Kelola Sample Wajah');
            } else {
                $('#statusBadgeBiometrik')
                    .removeClass('bg-label-success bg-label-secondary')
                    .addClass('bg-label-warning')
                    .html('<i class="ti ti-alert-triangle ti-xs me-1"></i>Belum Registrasi');
                $('#btnManageFace').html('<i class="ti ti-camera-plus ti-xs me-1"></i>Daftarkan Wajah Sekarang');
            }

            $('#btnManageFace').attr('href', url);
            $box.removeClass('d-none');
        });

        // Filter Table Search & Status
        function filterBiometricTable() {
            const searchTerm = $('#searchTable').val().toLowerCase();
            const filterStatus = $('#filterFaceStatus').val();

            $('#biometricTable tbody tr').each(function() {
                const text = $(this).text().toLowerCase();
                const rowStatus = $(this).data('status');

                const matchesSearch = text.indexOf(searchTerm) > -1;
                const matchesStatus = (filterStatus === 'all') || (rowStatus === filterStatus);

                if (matchesSearch && matchesStatus) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        $('#searchTable').on('keyup', filterBiometricTable);
        $('#filterFaceStatus').on('change', filterBiometricTable);
    });
</script>
@endpush
