@extends('layouts.app')
@section('titlepage', 'Buat Kontrak Kerja Baru')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Data Karyawan</a></li>
    <li class="breadcrumb-item"><a href="{{ route('kontrak.index') }}">Kontrak Kerja</a></li>
    <li class="breadcrumb-item active">Buat Baru</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <a href="{{ route('kontrak.index') }}" class="text-decoration-none text-muted mb-1 d-inline-flex align-items-center gap-1" style="font-size: 13px;">
            <i class="ti ti-arrow-left"></i> Kembali ke Daftar Kontrak
        </a>
        <h4 class="page-title mb-1">Penerbitan Kontrak Kerja Baru</h4>
        <p class="page-subtitle text-muted mb-0">Input perjanjian kerja waktu tertentu (PKWT), PKWTT tetap, atau masa percobaan karyawan.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('kontrak.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i>
            <span>Batal</span>
        </a>
    </div>
</div>

<div class="card mx-auto shadow-sm" style="max-width: 850px; border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF;">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="card-title fw-bold text-dark mb-0">Informasi Perjanjian Kontrak</h5>
    </div>
    <form action="{{ route('kontrak.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Nomor Kontrak</label>
                    <input type="text" name="no_kontrak" class="form-control font-mono @error('no_kontrak') is-invalid @enderror" value="{{ old('no_kontrak') }}" placeholder="Contoh: 001/HRD/PKWT/I/2026" required style="border-radius: 8px;">
                    @error('no_kontrak')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Pilih Karyawan</label>
                    <select name="nik" class="form-select @error('nik') is-invalid @enderror" required style="border-radius: 8px;">
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($karyawans as $k)
                            <option value="{{ $k->nik }}" {{ old('nik') == $k->nik ? 'selected' : '' }}>
                                {{ $k->nik }} - {{ $k->nama_karyawan }}
                            </option>
                        @endforeach
                    </select>
                    @error('nik')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Jenis Kontrak</label>
                    <select name="jenis_kontrak" class="form-select @error('jenis_kontrak') is-invalid @enderror" required id="selectJenisKontrak" style="border-radius: 8px;">
                        <option value="PKWT" {{ old('jenis_kontrak') == 'PKWT' ? 'selected' : '' }}>PKWT (Waktu Tertentu / Kontrak)</option>
                        <option value="PKWTT" {{ old('jenis_kontrak') == 'PKWTT' ? 'selected' : '' }}>PKWTT (Waktu Tidak Tertentu / Tetap)</option>
                        <option value="PROBATION" {{ old('jenis_kontrak') == 'PROBATION' ? 'selected' : '' }}>Probation (Masa Percobaan)</option>
                        <option value="INTERNSHIP" {{ old('jenis_kontrak') == 'INTERNSHIP' ? 'selected' : '' }}>Magang / Internship</option>
                        <option value="FREELANCE" {{ old('jenis_kontrak') == 'FREELANCE' ? 'selected' : '' }}>Freelance / Harian Lepas</option>
                    </select>
                    @error('jenis_kontrak')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Gaji Pokok Disepakati (Rp)</label>
                    <input type="number" name="gaji_pokok" class="form-control font-mono" value="{{ old('gaji_pokok') }}" placeholder="Contoh: 5000000" style="border-radius: 8px;">
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required style="border-radius: 8px;">
                    @error('tanggal_mulai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-md-6" id="wrapperTanggalSelesai">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" id="inputTanggalSelesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" value="{{ old('tanggal_selesai') }}" style="border-radius: 8px;">
                    <div class="form-text text-muted" style="font-size: 11px;">Dapat dikosongkan jika jenis kontrak adalah PKWTT (Tetap).</div>
                    @error('tanggal_selesai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Jabatan dalam Kontrak</label>
                    <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan') }}" placeholder="Contoh: Senior Developer" style="border-radius: 8px;">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Notifikasi Pengingat (Hari sebelum berakhir)</label>
                    <input type="number" name="reminder_days" class="form-control font-mono" value="{{ old('reminder_days', 30) }}" min="1" max="180" style="border-radius: 8px;">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Upload Berkas Kontrak (PDF / Scan Surat Perjanjian)</label>
                    <input type="file" name="dokumen" class="form-control @error('dokumen') is-invalid @enderror" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="border-radius: 8px;">
                    <div class="form-text text-muted" style="font-size: 11px;">Maksimal 5 MB (.pdf, .doc, .docx, .jpg, .png).</div>
                    @error('dokumen')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Keterangan / Klausul Tambahan</label>
                    <textarea name="keterangan" class="form-control" rows="3" placeholder="Catatan kesepakatan khusus..." style="border-radius: 8px;">{{ old('keterangan') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card-footer bg-light d-flex justify-content-end gap-2 py-3 px-4 border-top">
            <a href="{{ route('kontrak.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">Batal</a>
            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5 px-4">
                <i class="ti ti-device-floppy"></i>
                <span>Simpan Kontrak Kerja</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('myscript')
<script>
    $('#selectJenisKontrak').on('change', function() {
        if ($(this).val() === 'PKWTT') {
            $('#inputTanggalSelesai').val('').prop('disabled', true);
        } else {
            $('#inputTanggalSelesai').prop('disabled', false);
        }
    });
</script>
@endpush
