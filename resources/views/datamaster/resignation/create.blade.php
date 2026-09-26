@extends('layouts.app')
@section('titlepage', 'Proses Resign / Keluar Karyawan')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item"><a href="{{ route('resignation.index') }}">Resignasi & Offboarding</a></li>
    <li class="breadcrumb-item active">Proses Keluar</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="page-title mb-1"><i class="ti ti-user-x me-2 text-danger"></i>Proses Resign / Keluar Karyawan</h4>
        <p class="page-subtitle text-muted mb-0">Formulir pemutusan hubungan kerja, pencatatan status keluar, dan inisiasi clearance aset.</p>
    </div>
    <div>
        <a href="{{ route('resignation.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i>Kembali
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-header border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold text-dark">Data Pengunduran Diri / Pemutusan Hubungan Kerja</h5>
            </div>
            <form action="{{ route('resignation.store') }}" method="POST" enctype="multipart/form-data" class="card-body p-4">
                @csrf
                <div class="alert alert-warning mb-4" style="border-radius: 8px;">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-alert-triangle fs-3 me-2"></i>
                        <div>
                            <strong>Perhatian:</strong> Menyimpan formulir ini akan langsung menonaktifkan status karyawan (<code class="font-mono">status_aktif = 0</code>), mencatat tanggal nonaktif, dan menutup kontrak kerja aktif terkait.
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label required fw-medium">Pilih Karyawan</label>
                        <select name="nik" class="form-select @error('nik') is-invalid @enderror" required>
                            <option value="">-- Pilih Karyawan Aktif --</option>
                            @foreach($karyawans as $k)
                                <option value="{{ $k->nik }}" {{ old('nik', $selectedNik) == $k->nik ? 'selected' : '' }}>
                                    {{ $k->nik }} - {{ $k->nama_karyawan }}
                                </option>
                            @endforeach
                        </select>
                        @error('nik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label required fw-medium">Kategori Berhenti / Keluar</label>
                        <select name="kategori_keluar" class="form-select @error('kategori_keluar') is-invalid @enderror" required>
                            @foreach($categories as $code => $label)
                                <option value="{{ $code }}" {{ old('kategori_keluar') == $code ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('kategori_keluar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label required fw-medium">Tanggal Pengajuan Surat</label>
                        <input type="date" name="tanggal_pengajuan" class="form-control @error('tanggal_pengajuan') is-invalid @enderror" value="{{ old('tanggal_pengajuan', date('Y-m-d')) }}" required>
                        @error('tanggal_pengajuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label required fw-medium">Tanggal Terakhir Bekerja (Efektif Keluar)</label>
                        <input type="date" name="tanggal_keluar" class="form-control @error('tanggal_keluar') is-invalid @enderror" value="{{ old('tanggal_keluar', date('Y-m-d')) }}" required>
                        @error('tanggal_keluar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label required fw-medium">Status Clearance / Serah Terima Aset</label>
                        <select name="status_clearance" class="form-select @error('status_clearance') is-invalid @enderror" required>
                            <option value="PENDING" {{ old('status_clearance') === 'PENDING' ? 'selected' : '' }}>PENDING (Belum selesai)</option>
                            <option value="IN_PROGRESS" {{ old('status_clearance') === 'IN_PROGRESS' ? 'selected' : '' }}>IN_PROGRESS (Dalam proses)</option>
                            <option value="CLEARED" {{ old('status_clearance') === 'CLEARED' ? 'selected' : '' }}>CLEARED (Semua aset & tugas selesai diserahkan)</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-medium">Berkas Surat Resign / Paklaring (PDF/Scan)</label>
                        <input type="file" name="dokumen" class="form-control @error('dokumen') is-invalid @enderror" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-medium">Alasan Pengunduran Diri / Pemutusan Hubungan Kerja</label>
                        <textarea name="alasan" class="form-control" rows="3" placeholder="Uraikan alasan pengunduran diri atau catatan pemutusan kerja...">{{ old('alasan') }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-medium">Catatan Tim HR / Checklist Handover</label>
                        <textarea name="catatan_hr" class="form-control" rows="2" placeholder="Daftar inventaris, akun email, atau pengalihan tanggung jawab...">{{ old('catatan_hr') }}</textarea>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('resignation.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-danger d-inline-flex align-items-center gap-1.5">
                            <i class="ti ti-user-x"></i>Konfirmasi & Nonaktifkan Karyawan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
