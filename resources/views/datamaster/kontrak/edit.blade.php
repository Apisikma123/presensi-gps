@extends('layouts.app')
@section('titlepage', 'Edit Kontrak Kerja')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Data Karyawan</a></li>
    <li class="breadcrumb-item"><a href="{{ route('kontrak.index') }}">Kontrak Kerja</a></li>
    <li class="breadcrumb-item active">Edit Kontrak</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <a href="{{ route('kontrak.index') }}" class="text-decoration-none text-muted mb-1 d-inline-flex align-items-center gap-1" style="font-size: 13px;">
            <i class="ti ti-arrow-left"></i> Kembali ke Daftar Kontrak
        </a>
        <h4 class="page-title mb-1">Edit Data Kontrak Kerja</h4>
        <p class="page-subtitle text-muted mb-0">Karyawan: {{ $kontrak->karyawan ? $kontrak->karyawan->nama_karyawan : $kontrak->nik }} (NIK: {{ $kontrak->nik }})</p>
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
        <h5 class="card-title fw-bold text-dark mb-0">Perubahan Data Kontrak</h5>
    </div>
    <form action="{{ route('kontrak.update', Crypt::encrypt($kontrak->id)) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Nomor Kontrak</label>
                    <input type="text" name="no_kontrak" class="form-control font-mono @error('no_kontrak') is-invalid @enderror" value="{{ old('no_kontrak', $kontrak->no_kontrak) }}" required style="border-radius: 8px;">
                    @error('no_kontrak')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Status Kontrak</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required style="border-radius: 8px;">
                        <option value="ACTIVE" {{ old('status', $kontrak->status) === 'ACTIVE' ? 'selected' : '' }}>Aktif</option>
                        <option value="EXPIRING_SOON" {{ old('status', $kontrak->status) === 'EXPIRING_SOON' ? 'selected' : '' }}>Segera Berakhir</option>
                        <option value="EXPIRED" {{ old('status', $kontrak->status) === 'EXPIRED' ? 'selected' : '' }}>Habis</option>
                        <option value="RENEWED" {{ old('status', $kontrak->status) === 'RENEWED' ? 'selected' : '' }}>Diperpanjang</option>
                        <option value="TERMINATED" {{ old('status', $kontrak->status) === 'TERMINATED' ? 'selected' : '' }}>Diakhiri</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Jenis Kontrak</label>
                    <select name="jenis_kontrak" class="form-select @error('jenis_kontrak') is-invalid @enderror" required style="border-radius: 8px;">
                        <option value="PKWT" {{ old('jenis_kontrak', $kontrak->jenis_kontrak) === 'PKWT' ? 'selected' : '' }}>PKWT</option>
                        <option value="PKWTT" {{ old('jenis_kontrak', $kontrak->jenis_kontrak) === 'PKWTT' ? 'selected' : '' }}>PKWTT (Tetap)</option>
                        <option value="PROBATION" {{ old('jenis_kontrak', $kontrak->jenis_kontrak) === 'PROBATION' ? 'selected' : '' }}>Probation</option>
                        <option value="INTERNSHIP" {{ old('jenis_kontrak', $kontrak->jenis_kontrak) === 'INTERNSHIP' ? 'selected' : '' }}>Magang</option>
                        <option value="FREELANCE" {{ old('jenis_kontrak', $kontrak->jenis_kontrak) === 'FREELANCE' ? 'selected' : '' }}>Freelance</option>
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Gaji Pokok Disepakati (Rp)</label>
                    <input type="number" name="gaji_pokok" class="form-control font-mono" value="{{ old('gaji_pokok', $kontrak->gaji_pokok ? (int)$kontrak->gaji_pokok : '') }}" style="border-radius: 8px;">
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai', $kontrak->tanggal_mulai ? $kontrak->tanggal_mulai->format('Y-m-d') : '') }}" required style="border-radius: 8px;">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" value="{{ old('tanggal_selesai', $kontrak->tanggal_selesai ? $kontrak->tanggal_selesai->format('Y-m-d') : '') }}" style="border-radius: 8px;">
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Jabatan dalam Kontrak</label>
                    <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $kontrak->jabatan) }}" style="border-radius: 8px;">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Notifikasi Pengingat (Hari sebelum berakhir)</label>
                    <input type="number" name="reminder_days" class="form-control font-mono" value="{{ old('reminder_days', $kontrak->reminder_days) }}" min="1" max="180" style="border-radius: 8px;">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Ganti Berkas Dokumen (Opsional)</label>
                    <input type="file" name="dokumen" class="form-control @error('dokumen') is-invalid @enderror" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="border-radius: 8px;">
                    @if($kontrak->dokumen)
                        <div class="mt-2 small">
                            Berkas tersimpan: <a href="{{ asset('storage/' . $kontrak->dokumen) }}" target="_blank" class="fw-semibold text-primary"><i class="ti ti-paperclip me-1"></i>Unduh/Lihat Berkas Saat Ini</a>
                        </div>
                    @endif
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Keterangan / Klausul Tambahan</label>
                    <textarea name="keterangan" class="form-control" rows="3" style="border-radius: 8px;">{{ old('keterangan', $kontrak->keterangan) }}</textarea>
                </div>
            </div>
        </div>

        <div class="card-footer bg-light d-flex justify-content-end gap-2 py-3 px-4 border-top">
            <a href="{{ route('kontrak.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">Batal</a>
            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5 px-4">
                <i class="ti ti-device-floppy"></i>
                <span>Perbarui Kontrak</span>
            </button>
        </div>
    </form>
</div>
@endsection
