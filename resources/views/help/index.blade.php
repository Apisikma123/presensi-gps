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
        <h4 class="page-title mb-1" style="font-family: 'Outfit', sans-serif; font-weight: 700; color: #1E293B;">
            Pusat Bantuan & Panduan Operasional
        </h4>
        <p class="page-subtitle text-muted mb-0" style="font-size: 13.5px;">
            Panduan ringkas pengelolaan presensi, jadwal kerja, permohonan staf, dan modul SDM untuk admin kantor.
        </p>
    </div>
    <div>
        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2" onclick="openHelpDrawer('panduan_admin_roster')" style="border-radius: 10px; font-weight: 600; padding: 8px 16px;">
            <i class="ti ti-help-hexagon fs-5"></i>
            <span>Buka Panduan Cepat</span>
        </button>
    </div>
</div>

{{-- Search Guide Bar --}}
<div class="card mb-4" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 14px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-3 mb-lg-0">
                <h5 class="fw-bold text-dark mb-1" style="font-size: 16px;">Cari Petunjuk Operasional</h5>
                <p class="text-muted small mb-0">Ketik kata kunci untuk menemukan solusi cepat alur kerja dan kendala staf.</p>
            </div>
            <div class="col-lg-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0" style="border-color: #CBD5E1;"><i class="ti ti-search text-muted"></i></span>
                    <input type="text" id="helpSearch" class="form-control border-start-0" placeholder="Cari topik (contoh: koreksi absen, tukar shift, cuti, lembur, gaji, kasbon)..." style="border-color: #CBD5E1; font-size: 13.5px;">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Grid of Topics --}}
<div class="row g-3 mb-4" id="helpTopics">

    {{-- 1. Roster & Jadwal Shift --}}
    @if(module_enabled('attendance'))
    <div class="col-md-6 col-lg-4 help-card" data-keywords="roster jadwal shift jam kerja ganti shift tukar shift libur off minggu kalender">
        <div class="card h-100 d-flex flex-column" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 14px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: rgba(59, 130, 246, 0.1); color: #2563eb;">
                        <i class="ti ti-calendar-event fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">Roster & Jadwal Shift</h6>
                        <small class="text-muted">Jadwal Mingguan & Tukar Shift</small>
                    </div>
                </div>
                <p class="text-muted small mb-3" style="line-height: 1.55;">
                    Pengaturan pola jam kerja 7 hari staf dan jadwal khusus per tanggal untuk kebutuhan tukar shift atau perbantuan cabang.
                </p>
                <ul class="list-unstyled small text-muted mb-4 d-flex flex-column gap-1.5 flex-grow-1">
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Jadwal Khusus Tanggal selalu mengalahkan Roster Mingguan.</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Hari libur staf bisa diatur kapan saja, tidak harus hari Minggu.</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Pindah cabang harian otomatis mengunci GPS ke cabang baru.</span></li>
                </ul>
                <button type="button" class="btn btn-sm btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-1.5" onclick="openHelpDrawer('panduan_admin_roster')" style="border-radius: 8px; font-weight: 600;">
                    <i class="ti ti-notes"></i>
                    <span>Buka Panduan Roster</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- 2. Presensi GPS & Biometrik --}}
    @if(module_enabled('attendance'))
    <div class="col-md-6 col-lg-4 help-card" data-keywords="presensi gps absensi biometrik wajah foto selfie toleransi terlambat radius koreksi lupa absen">
        <div class="card h-100 d-flex flex-column" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 14px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary);">
                        <i class="ti ti-map-pin-check fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">Presensi GPS & Foto Wajah</h6>
                        <small class="text-muted">Kehadiran & Koreksi Absen</small>
                    </div>
                </div>
                <p class="text-muted small mb-3" style="line-height: 1.55;">
                    Validasi radius jarak kantor, verifikasi wajah kamera anti-titip absen, serta koreksi jam absen manual oleh admin.
                </p>
                <ul class="list-unstyled small text-muted mb-4 d-flex flex-column gap-1.5 flex-grow-1">
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Radius standar kantor: 50 sampai 100 meter dari titik GPS.</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Koreksi jam absen via tombol pensil hijau (wajib isi alasan).</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Aplikasi memblokir penggunaan Fake GPS secara otomatis.</span></li>
                </ul>
                <button type="button" class="btn btn-sm btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-1.5" onclick="openHelpDrawer('presensi')" style="border-radius: 8px; font-weight: 600;">
                    <i class="ti ti-notes"></i>
                    <span>Buka Panduan Presensi</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- 3. Cuti & Izin --}}
    @if(module_enabled('leave'))
    <div class="col-md-6 col-lg-4 help-card" data-keywords="cuti tahunan kuota saldo izin sakit sid surat dokter izin absen approval persetujuan">
        <div class="card h-100 d-flex flex-column" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 14px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: rgba(74, 103, 65, 0.1); color: #4A6741;">
                        <i class="ti ti-calendar-stats fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">Cuti & Izin Karyawan</h6>
                        <small class="text-muted">Persetujuan & Saldo Kuota</small>
                    </div>
                </div>
                <p class="text-muted small mb-3" style="line-height: 1.55;">
                    Pengelolaan permohonan izin absen, izin sakit dengan bukti surat dokter, serta pemotongan otomatis saldo cuti tahunan.
                </p>
                <ul class="list-unstyled small text-muted mb-4 d-flex flex-column gap-1.5 flex-grow-1">
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Saldo cuti otomatis berkurang saat permohonan disetujui.</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Izin sakit wajib melampirkan foto Surat Izin Dokter (SID).</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Sistem menolak approval jika staf sudah tercatat hadir fisik.</span></li>
                </ul>
                <button type="button" class="btn btn-sm btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-1.5" onclick="openHelpDrawer('izincuti')" style="border-radius: 8px; font-weight: 600;">
                    <i class="ti ti-notes"></i>
                    <span>Buka Panduan Cuti</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- 4. Lembur (Overtime) --}}
    @if(module_enabled('overtime'))
    <div class="col-md-6 col-lg-4 help-card" data-keywords="lembur overtime depnaker multiplier spk surat perintah kerja perhitungan lembur jam kerja">
        <div class="card h-100 d-flex flex-column" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 14px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: rgba(255, 159, 67, 0.12); color: #ff9f43;">
                        <i class="ti ti-clock-play fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">Lembur & SPK (Overtime)</h6>
                        <small class="text-muted">Perintah Lembur & Pengali Upah</small>
                    </div>
                </div>
                <p class="text-muted small mb-3" style="line-height: 1.55;">
                    Pembuatan surat perintah lembur, verifikasi tugas, dan perhitungan pengali upah lembur resmi sesuai ketentuan ketenagakerjaan.
                </p>
                <ul class="list-unstyled small text-muted mb-4 d-flex flex-column gap-1.5 flex-grow-1">
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Hari kerja normal: 1.5x (jam pertama) dan 2x (jam berikutnya).</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Hari libur resmi otomatis menggunakan pengali libur (2x sampai 4x).</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Upah lembur otomatis masuk ke slip gaji bulanan saat payroll.</span></li>
                </ul>
                <button type="button" class="btn btn-sm btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-1.5" onclick="openHelpDrawer('lembur')" style="border-radius: 8px; font-weight: 600;">
                    <i class="ti ti-notes"></i>
                    <span>Buka Panduan Lembur</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- 5. Payroll & Penggajian --}}
    @if(module_enabled('payroll'))
    <div class="col-md-6 col-lg-4 help-card" data-keywords="payroll gaji slip gaji pph 21 bpjs denda telat potongan alpha thr cetak slip">
        <div class="card h-100 d-flex flex-column" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 14px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: rgba(234, 84, 85, 0.1); color: #ea5455;">
                        <i class="ti ti-report-money fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">Penggajian & Slip Gaji</h6>
                        <small class="text-muted">Kalkulasi Otomatis & Slip PDF</small>
                    </div>
                </div>
                <p class="text-muted small mb-3" style="line-height: 1.55;">
                    Perhitungan gaji bulanan otomatis berdasarkan rekapan absensi, potongan telat atau alpha, BPJS, cicilan kasbon, dan cetak slip gaji.
                </p>
                <ul class="list-unstyled small text-muted mb-4 d-flex flex-column gap-1.5 flex-grow-1">
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Kalkulasi gaji otomatis dari tanggal cut-off absensi.</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Otomatis memotong denda keterlambatan dan cicilan kasbon.</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Cetak slip gaji massal format PDF atau kirim ke aplikasi staf.</span></li>
                </ul>
                <button type="button" class="btn btn-sm btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-1.5" onclick="openHelpDrawer('payroll')" style="border-radius: 8px; font-weight: 600;">
                    <i class="ti ti-notes"></i>
                    <span>Buka Panduan Payroll</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- 6. Pinjaman & Kasbon --}}
    @if(module_enabled('loans'))
    <div class="col-md-6 col-lg-4 help-card" data-keywords="pinjaman kasbon utang cicilan tenor potong gaji kredit pinjam uang">
        <div class="card h-100 d-flex flex-column" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 14px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <i class="ti ti-cash fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">Pinjaman & Kasbon Staf</h6>
                        <small class="text-muted">Cicilan & Potong Payroll</small>
                    </div>
                </div>
                <p class="text-muted small mb-3" style="line-height: 1.55;">
                    Pencatatan kasbon staf dengan pilihan tenor cicilan per bulan yang otomatis memotong gaji staf hingga lunas.
                </p>
                <ul class="list-unstyled small text-muted mb-4 d-flex flex-column gap-1.5 flex-grow-1">
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Cicilan bulanan otomatis memotong slip gaji saat proses payroll.</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Pantau sisa saldo kasbon dan histori cicilan secara transparan.</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Sisa kasbon staf resign otomatis dipotong dari hak akhir pesangon.</span></li>
                </ul>
                <button type="button" class="btn btn-sm btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-1.5" onclick="openHelpDrawer('loan')" style="border-radius: 8px; font-weight: 600;">
                    <i class="ti ti-notes"></i>
                    <span>Buka Panduan Kasbon</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- 7. Klaim Reimbursement --}}
    @if(module_enabled('reimbursement'))
    <div class="col-md-6 col-lg-4 help-card" data-keywords="reimbursement klaim kuitansi nota bon pengeluaran uang bensin belanja biaya">
        <div class="card h-100 d-flex flex-column" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 14px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                        <i class="ti ti-receipt fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">Klaim Reimbursement</h6>
                        <small class="text-muted">Biaya Operasional & Nota</small>
                    </div>
                </div>
                <p class="text-muted small mb-3" style="line-height: 1.55;">
                    Pemeriksaan klaim biaya operasional yang ditalangi staf, lengkap dengan bukti foto nota atau kuitansi asli pembayaran.
                </p>
                <ul class="list-unstyled small text-muted mb-4 d-flex flex-column gap-1.5 flex-grow-1">
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Staf wajib melampirkan foto nota kuitansi fisik yang jelas.</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Admin dapat menyetujui penuh atau menyesuaikan nominal klaim.</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Pembayaran dapat ditransfer langsung atau digabung ke slip gaji.</span></li>
                </ul>
                <button type="button" class="btn btn-sm btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-1.5" onclick="openHelpDrawer('reimbursement')" style="border-radius: 8px; font-weight: 600;">
                    <i class="ti ti-notes"></i>
                    <span>Buka Panduan Klaim</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- 8. Disiplin & SP --}}
    @if(module_enabled('warning') || module_enabled('discipline'))
    <div class="col-md-6 col-lg-4 help-card" data-keywords="disiplin surat peringatan sp pelanggaran sanksi tata tertib sp1 sp2 sp3">
        <div class="card h-100 d-flex flex-column" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 14px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                        <i class="ti ti-alert-triangle fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">Disiplin & Surat Peringatan</h6>
                        <small class="text-muted">Penerbitan SP 1, SP 2, SP 3</small>
                    </div>
                </div>
                <p class="text-muted small mb-3" style="line-height: 1.55;">
                    Penerbitan sanksi disiplin bagi staf yang melanggar peraturan perusahaan, dengan masa berlaku otomatis tepat 6 bulan.
                </p>
                <ul class="list-unstyled small text-muted mb-4 d-flex flex-column gap-1.5 flex-grow-1">
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Masa berlaku SP otomatis 6 bulan sejak tanggal diterbitkan.</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Cetak dokumen resmi SP untuk ditandatangani staf dan atasan.</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Riwayat sanksi tersimpan rapi untuk pertimbangan promosi.</span></li>
                </ul>
                <button type="button" class="btn btn-sm btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-1.5" onclick="openHelpDrawer('warning')" style="border-radius: 8px; font-weight: 600;">
                    <i class="ti ti-notes"></i>
                    <span>Buka Panduan SP</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- 9. Karyawan Keluar & Pesangon --}}
    @if(module_enabled('resignation'))
    <div class="col-md-6 col-lg-4 help-card" data-keywords="pesangon upmk uph phk resign offboarding karyawan keluar exit clearance">
        <div class="card h-100 d-flex flex-column" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 14px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: rgba(30, 41, 59, 0.08); color: #1e293b;">
                        <i class="ti ti-user-x fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">Karyawan Keluar & Pesangon</h6>
                        <small class="text-muted">Exit Clearance & PP 35/2021</small>
                    </div>
                </div>
                <p class="text-muted small mb-3" style="line-height: 1.55;">
                    Pengelolaan proses karyawan mengundurkan diri (resign), pengembalian inventaris kantor, pemotongan kasbon, dan rujukan pesangon.
                </p>
                <ul class="list-unstyled small text-muted mb-4 d-flex flex-column gap-1.5 flex-grow-1">
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Checklist pengembalian aset kantor sebelum hak akhir diserahkan.</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Kalkulator rujukan pesangon (UP, UPMK, UPH) PP 35/2021.</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Nonaktifkan akun staf pada tanggal efektif keluar.</span></li>
                </ul>
                <button type="button" class="btn btn-sm btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-1.5" onclick="openHelpDrawer('offboarding')" style="border-radius: 8px; font-weight: 600;">
                    <i class="ti ti-notes"></i>
                    <span>Buka Panduan Offboarding</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- 10. Rekrutmen & Onboarding --}}
    @if(module_enabled('recruitment') || module_enabled('onboarding'))
    <div class="col-md-6 col-lg-4 help-card" data-keywords="rekrutmen pelamar lowongan kerja onboarding staf baru berkas ktp seragam">
        <div class="card h-100 d-flex flex-column" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 14px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                        <i class="ti ti-user-plus fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">Rekrutmen & Onboarding</h6>
                        <small class="text-muted">Lowongan & Karyawan Baru</small>
                    </div>
                </div>
                <p class="text-muted small mb-3" style="line-height: 1.55;">
                    Penerimaan staf baru mulai dari lowongan kerja, seleksi pelamar, hingga checklist persiapan kerja di hari pertama.
                </p>
                <ul class="list-unstyled small text-muted mb-4 d-flex flex-column gap-1.5 flex-grow-1">
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Pelamar diterima langsung dikonversi menjadi data karyawan.</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Checklist berkas (KTP, NPWP, foto wajah, seragam).</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Daftarkan foto wajah di hari pertama agar langsung bisa absen.</span></li>
                </ul>
                <button type="button" class="btn btn-sm btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-1.5" onclick="openHelpDrawer('recruitment')" style="border-radius: 8px; font-weight: 600;">
                    <i class="ti ti-notes"></i>
                    <span>Buka Panduan Rekrutmen</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- 11. Laporan & Rekapitulasi Presensi --}}
    @if(module_enabled('attendance'))
    <div class="col-md-6 col-lg-4 help-card" data-keywords="laporan presensi rekapitulasi kehadiran cetak pdf export excel multi cabang">
        <div class="card h-100 d-flex flex-column" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 14px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: rgba(168, 85, 247, 0.1); color: #a855f7;">
                        <i class="ti ti-printer fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">Laporan Rekapitulasi Presensi</h6>
                        <small class="text-muted">Cetak PDF & Export Excel</small>
                    </div>
                </div>
                <p class="text-muted small mb-3" style="line-height: 1.55;">
                    Penarikan rekap kehadiran bulanan seluruh cabang atau per staf siap cetak bertandatangan atau diolah di Excel.
                </p>
                <ul class="list-unstyled small text-muted mb-4 d-flex flex-column gap-1.5 flex-grow-1">
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Laporan berbasis cabang penugasan aktual staf harian.</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Rincian hadir tepat waktu, menit telat, izin, sakit, dan alpha.</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Filter fleksibel per bulan, tahun, cabang, atau perorangan.</span></li>
                </ul>
                <button type="button" class="btn btn-sm btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-1.5" onclick="openHelpDrawer('laporan_presensi')" style="border-radius: 8px; font-weight: 600;">
                    <i class="ti ti-notes"></i>
                    <span>Buka Panduan Laporan</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- 12. Pengaturan & Akun Admin --}}
    <div class="col-md-6 col-lg-4 help-card" data-keywords="pengaturan umum logo nama perusahaan akun admin supervisor backup database">
        <div class="card h-100 d-flex flex-column" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 14px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: rgba(100, 116, 139, 0.1); color: #475569;">
                        <i class="ti ti-settings fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">Pengaturan & Akun Admin</h6>
                        <small class="text-muted">Identitas & Keamanan Data</small>
                    </div>
                </div>
                <p class="text-muted small mb-3" style="line-height: 1.55;">
                    Pengaturan identitas perusahaan, logo kantor pada laporan, manajemen akun supervisor, dan pencadangan database.
                </p>
                <ul class="list-unstyled small text-muted mb-4 d-flex flex-column gap-1.5 flex-grow-1">
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Ganti logo dan nama perusahaan untuk tampilan sistem.</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Atur hak akses supervisor agar hanya mengelola cabangnya.</span></li>
                    <li class="d-flex align-items-start gap-1.5"><i class="ti ti-check text-success mt-0.5"></i><span>Lakukan backup database secara rutin sebulan sekali.</span></li>
                </ul>
                <button type="button" class="btn btn-sm btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-1.5" onclick="openHelpDrawer('generalsetting')" style="border-radius: 8px; font-weight: 600;">
                    <i class="ti ti-notes"></i>
                    <span>Buka Panduan Pengaturan</span>
                </button>
            </div>
        </div>
    </div>

</div>

{{-- Statutory Cheat Sheet Table --}}
@if(module_enabled('resignation'))
<div class="card mb-4" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 14px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02); overflow: hidden;">
    <div class="card-header py-3 px-4 bg-transparent border-bottom">
        <h6 class="card-title fw-bold text-dark mb-0" style="font-size: 14.5px;">Tabel Rujukan Cepat: Uang Pesangon & UPMK (PP 35/2021)</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr class="table-light">
                    <th style="font-size: 12px; font-weight: 700; color: #475569;">MASA KERJA KARYAWAN</th>
                    <th style="font-size: 12px; font-weight: 700; color: #475569;">UANG PESANGON (UP) STANDAR</th>
                    <th style="font-size: 12px; font-weight: 700; color: #475569;">UANG PENGHARGAAN MASA KERJA (UPMK)</th>
                </tr>
            </thead>
            <tbody class="font-mono" style="font-size: 13px;">
                <tr><td>Kurang dari 1 Tahun</td><td>1 Bulan Upah</td><td>-</td></tr>
                <tr><td>1 Tahun sampai kurang dari 2 Tahun</td><td>2 Bulan Upah</td><td>-</td></tr>
                <tr><td>2 Tahun sampai kurang dari 3 Tahun</td><td>3 Bulan Upah</td><td>-</td></tr>
                <tr><td>3 Tahun sampai kurang dari 4 Tahun</td><td>4 Bulan Upah</td><td>2 Bulan Upah</td></tr>
                <tr><td>4 Tahun sampai kurang dari 5 Tahun</td><td>5 Bulan Upah</td><td>2 Bulan Upah</td></tr>
                <tr><td>5 Tahun sampai kurang dari 6 Tahun</td><td>6 Bulan Upah</td><td>2 Bulan Upah</td></tr>
                <tr><td>6 Tahun sampai kurang dari 7 Tahun</td><td>7 Bulan Upah</td><td>3 Bulan Upah</td></tr>
                <tr><td>7 Tahun sampai kurang dari 8 Tahun</td><td>8 Bulan Upah</td><td>3 Bulan Upah</td></tr>
                <tr><td>8 Tahun atau Lebih</td><td>9 Bulan Upah (Maksimal)</td><td>Sesuai jenjang masa kerja (hingga 10 bulan)</td></tr>
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
