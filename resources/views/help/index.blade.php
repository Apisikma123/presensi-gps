@extends('layouts.app')
@section('titlepage', 'Pusat Bantuan & Panduan Sistem')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Pusat Bantuan & Panduan</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="page-title mb-1">Pusat Bantuan & Pedoman PRESENCE HR</h4>
        <p class="page-subtitle text-muted mb-0">Dokumentasi operasional modul HR, standar kepatuhan regulasi ketenagakerjaan Indonesia, dan SOP sistem.</p>
    </div>
</div>

{{-- Search Guide Bar --}}
<div class="card mb-4" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-3 mb-lg-0">
                <h5 class="fw-bold text-dark mb-1">Pusat Pengetahuan & Panduan Operasional SDM</h5>
                <p class="text-muted small mb-0">Temukan petunjuk alur kerja modul, ketentuan undang-undang ketenagakerjaan, serta referensi hitungan.</p>
            </div>
            <div class="col-lg-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0" style="border-color: #E2E8F0;"><i class="ti ti-search text-muted"></i></span>
                    <input type="text" id="helpSearch" class="form-control border-start-0" placeholder="Cari topik panduan (contoh: pesangon, lembur, pph 21, sp)..." style="border-color: #E2E8F0;">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Grid of Topics --}}
<div class="row g-3 mb-4" id="helpTopics">
    {{-- 1. Presensi & GPS --}}
    @if(module_enabled('attendance'))
    <div class="col-md-6 col-lg-4 help-card" data-keywords="presensi gps absensi biometrik wajah toleransi terlambat radius">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 40px; height: 40px; background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary);">
                        <i class="ti ti-map-pin-check fs-4"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0">Presensi GPS & Biometrik</h6>
                </div>
                <p class="text-muted small mb-3">
                    Panduan validasi lokasi GPS dalam radius meter kantor, biometrik pengenalan wajah anti-spoofing, dan dispensasi keterlambatan.
                </p>
                <ul class="list-unstyled small text-muted mb-0">
                    <li class="mb-1"><i class="ti ti-check text-success me-1"></i>Radius default: 50–100 meter.</li>
                    <li class="mb-1"><i class="ti ti-check text-success me-1"></i>Toleransi keterlambatan otomatis.</li>
                    <li><i class="ti ti-check text-success me-1"></i>Job auto-alpha presensi harian.</li>
                </ul>
            </div>
        </div>
    </div>
    @endif

    {{-- 2. Cuti & Izin --}}
    @if(module_enabled('leave'))
    <div class="col-md-6 col-lg-4 help-card" data-keywords="cuti tahunan kuota saldo izin sakit sid keterangan dokter">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 40px; height: 40px; background: rgba(74, 103, 65, 0.1); color: #4A6741;">
                        <i class="ti ti-calendar-event fs-4"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0">Cuti & Kuota Tahunan</h6>
                </div>
                <p class="text-muted small mb-3">
                    Aturan cuti tahunan (12 hari kerja per UU Cipta Kerja), izin sakit berlampirkan Surat Keterangan Dokter (SID).
                </p>
                <ul class="list-unstyled small text-muted mb-0">
                    <li class="mb-1"><i class="ti ti-check text-success me-1"></i>Ledger saldo kuota cuti otomatis.</li>
                    <li><i class="ti ti-check text-success me-1"></i>Lampiran foto Surat Keterangan Dokter.</li>
                </ul>
            </div>
        </div>
    </div>
    @endif

    {{-- 3. Lembur Depnaker --}}
    @if(module_enabled('overtime'))
    <div class="col-md-6 col-lg-4 help-card" data-keywords="lembur overtime depnaker multiplier spk perhitungan permenaker">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 40px; height: 40px; background: rgba(255, 159, 67, 0.12); color: #ff9f43;">
                        <i class="ti ti-clock-play fs-4"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0">Lembur & Multiplier Depnaker</h6>
                </div>
                <p class="text-muted small mb-3">
                    Penetapan upah lembur resmi Permenaker No. 102/2004 & PP 35/2021 dengan pengali 1.5x (jam ke-1) dan 2x (jam berikutnya).
                </p>
                <ul class="list-unstyled small text-muted mb-0">
                    <li class="mb-1"><i class="ti ti-check text-success me-1"></i>Hari libur/weekend: 2x, 3x, 4x.</li>
                    <li><i class="ti ti-check text-success me-1"></i>Dasar hitung: 1/173 × Gaji Sebulan.</li>
                </ul>
            </div>
        </div>
    </div>
    @endif

    {{-- 4. Payroll, PPh 21 TER, BPJS --}}
    @if(module_enabled('payroll'))
    <div class="col-md-6 col-lg-4 help-card" data-keywords="payroll gaji pph 21 ter bpjs ketenagakerjaan kesehatan thr slip">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 40px; height: 40px; background: rgba(234, 84, 85, 0.1); color: #ea5455;">
                        <i class="ti ti-report-money fs-4"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0">Payroll, PPh 21 & BPJS</h6>
                </div>
                <p class="text-muted small mb-3">
                    Sistem tarif efektif rata-rata (TER) PPh 21 (PP 58/2023), batas upah BPJS Kesehatan (12 Juta), dan iuran BPJS Ketenagakerjaan.
                </p>
                <ul class="list-unstyled small text-muted mb-0">
                    <li class="mb-1"><i class="ti ti-check text-success me-1"></i>Kategori TER A, B, C otomatis dari PTKP.</li>
                    <li class="mb-1"><i class="ti ti-check text-success me-1"></i>JHT, JKK, JKM, JP, dan BPJS Kesehatan.</li>
                    <li><i class="ti ti-check text-success me-1"></i>THR Keagamaan wajib bayar H-7.</li>
                </ul>
            </div>
        </div>
    </div>
    @endif

    {{-- 5. Disiplin & SP --}}
    @if(module_enabled('discipline'))
    <div class="col-md-6 col-lg-4 help-card" data-keywords="disiplin surat peringatan sp pelanggaran sanksi skorsing tata tertib">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 40px; height: 40px; background: rgba(115, 103, 240, 0.1); color: #7367f0;">
                        <i class="ti ti-alert-triangle fs-4"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0">Disiplin & Surat Peringatan</h6>
                </div>
                <p class="text-muted small mb-3">
                    Aturan penerbitan SP 1, SP 2, SP 3 sesuai Pasal 161 UU Ketenagakerjaan dengan masa berlaku tepat 6 (enam) bulan sejak diterbitkan.
                </p>
                <ul class="list-unstyled small text-muted mb-0">
                    <li class="mb-1"><i class="ti ti-check text-success me-1"></i>Otomatis kedaluwarsa setelah 6 bulan.</li>
                    <li><i class="ti ti-check text-success me-1"></i>Rujukan pasal Peraturan Perusahaan.</li>
                </ul>
            </div>
        </div>
    </div>
    @endif

    {{-- 6. Pesangon & Offboarding --}}
    @if(module_enabled('resignation'))
    <div class="col-md-6 col-lg-4 help-card" data-keywords="pesangon upmk uph phk resign offboarding pesangon pp 35 2021">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 40px; height: 40px; background: rgba(30, 41, 59, 0.08); color: #1e293b;">
                        <i class="ti ti-user-x fs-4"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0">Pesangon & Hak PHK (PP 35/2021)</h6>
                </div>
                <p class="text-muted small mb-3">
                    Kalkulator otomatis Uang Pesangon (UP), Uang Penghargaan Masa Kerja (UPMK), dan Uang Penggantian Hak (UPH) serta pemotongan kasbon.
                </p>
                <ul class="list-unstyled small text-muted mb-0">
                    <li class="mb-1"><i class="ti ti-check text-success me-1"></i>Formula tabel masa kerja 1 s.d >8 tahun.</li>
                    <li><i class="ti ti-check text-success me-1"></i>Lembar clearance 4 departemen.</li>
                </ul>
            </div>
        </div>
    </div>
    @endif

    {{-- 7. Paket & Tata Kelola Modul --}}
    <div class="col-md-6 col-lg-4 help-card" data-keywords="paket modul entitlement upgrade lisensi fitur konfigurasi aktivasi">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 40px; height: 40px; background: rgba(59, 130, 246, 0.1); color: #2563eb;">
                        <i class="ti ti-box fs-4"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0">Paket & Tata Kelola Modul</h6>
                </div>
                <p class="text-muted small mb-3">
                    Modul yang tersedia ditentukan berdasarkan paket Presence perusahaan. Anda hanya dapat mengaktifkan atau menonaktifkan modul opsional yang termasuk dalam paket.
                </p>
                <ul class="list-unstyled small text-muted mb-0">
                    <li class="mb-1"><i class="ti ti-check text-success me-1"></i>Modul inti (Core) selalu aktif & terlindungi.</li>
                    <li class="mb-1"><i class="ti ti-check text-success me-1"></i>Modul opsional dapat diatur Super Admin.</li>
                    <li><i class="ti ti-info-circle text-primary me-1"></i>Perubahan paket dilakukan melalui prosedur upgrade.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Statutory Cheat Sheet Table --}}
@if(module_enabled('resignation'))
<div class="card mb-4" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02); overflow: hidden;">
    <div class="card-header py-3 px-4 bg-transparent border-bottom">
        <h5 class="card-title fw-bold text-dark mb-0">Tabel Rujukan Cepat: Uang Pesangon & UPMK (PP 35/2021)</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>MASA KERJA KARYAWAN</th>
                    <th>UANG PESANGON (UP) STANDAR</th>
                    <th>UANG PENGHARGAAN MASA KERJA (UPMK)</th>
                </tr>
            </thead>
            <tbody class="font-mono" style="font-size: 13px;">
                <tr><td>&lt; 1 Tahun</td><td>1 Bulan Upah</td><td>-</td></tr>
                <tr><td>1 Tahun s.d &lt; 2 Tahun</td><td>2 Bulan Upah</td><td>-</td></tr>
                <tr><td>2 Tahun s.d &lt; 3 Tahun</td><td>3 Bulan Upah</td><td>-</td></tr>
                <tr><td>3 Tahun s.d &lt; 4 Tahun</td><td>4 Bulan Upah</td><td>2 Bulan Upah</td></tr>
                <tr><td>4 Tahun s.d &lt; 5 Tahun</td><td>5 Bulan Upah</td><td>2 Bulan Upah</td></tr>
                <tr><td>5 Tahun s.d &lt; 6 Tahun</td><td>6 Bulan Upah</td><td>2 Bulan Upah</td></tr>
                <tr><td>6 Tahun s.d &lt; 7 Tahun</td><td>7 Bulan Upah</td><td>3 Bulan Upah</td></tr>
                <tr><td>7 Tahun s.d &lt; 8 Tahun</td><td>8 Bulan Upah</td><td>3 Bulan Upah</td></tr>
                <tr><td>&gt;= 8 Tahun</td><td>9 Bulan Upah (Maksimal)</td><td>Sesuai jenjang (s.d 10 bln)</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endif

@push('myscript')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('helpSearch');
        const cards = document.querySelectorAll('.help-card');

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const q = this.value.toLowerCase().trim();
                cards.forEach(card => {
                    const kw = card.getAttribute('data-keywords') || '';
                    const text = card.textContent.toLowerCase();
                    if (!q || kw.includes(q) || text.includes(q)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endpush
@endsection
