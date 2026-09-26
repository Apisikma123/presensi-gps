@extends('layouts.app')
@section('titlepage', 'Buat Event THR')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Keuangan & Payroll</a></li>
    <li class="breadcrumb-item"><a href="{{ route('thr.index') }}">THR Keagamaan</a></li>
    <li class="breadcrumb-item active">Buat Event THR</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="page-title mb-1">Buat Event Pembagian THR</h4>
        <p class="page-subtitle text-muted mb-0">Tentukan nama hari raya keagamaan, tahun buku, dan tanggal cut-off kalkulasi masa kerja pegawai.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('thr.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>
</div>

{{-- Form Card --}}
<div class="row">
    <div class="col-lg-8 col-xl-6">
        <div class="card mb-4" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02); overflow: hidden;">
            <div class="card-header py-3 px-4 d-flex align-items-center gap-2" style="background: #FAF9F8; border-bottom: 1px solid #F1F5F9;">
                <i class="ti ti-gift fs-5 text-primary"></i>
                <h6 class="fw-bold mb-0 text-dark">Informasi Event THR</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('thr.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark required">Nama Event THR</label>
                        <input type="text" name="event_name" class="form-control @error('event_name') is-invalid @enderror"
                            value="{{ old('event_name', 'THR Hari Raya Idul Fitri ' . $currentYear) }}"
                            placeholder="Contoh: THR Hari Raya Idul Fitri 2026" required>
                        @error('event_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark required">Tahun Anggaran</label>
                            <input type="number" name="year" class="form-control font-mono @error('year') is-invalid @enderror"
                                value="{{ old('year', $currentYear) }}" min="2020" max="2050" required>
                            @error('year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark required">Tanggal Distribusi / Cutoff</label>
                            <input type="date" name="distribution_date" class="form-control font-mono @error('distribution_date') is-invalid @enderror"
                                value="{{ old('distribution_date', date('Y-m-d')) }}" required>
                            @error('distribution_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">Catatan Tambahan</label>
                        <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                            placeholder="Catatan internal departemen keuangan atau HR...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="p-3 mb-4 rounded-2" style="background: #FAF9F8; border: 1px dashed #E2E8F0; font-size: 12.5px; color: #475569;">
                        <div class="d-flex gap-2">
                            <i class="ti ti-info-circle fs-5 text-primary"></i>
                            <div>
                                <strong>Kalkulasi Otomatis:</strong> Setelah disimpan, sistem akan secara otomatis menghitung masa kerja seluruh karyawan aktif per tanggal distribusi dan mengalokasikan THR (Full 1x upah jika &ge; 12 bulan, prorata jika 1-12 bulan).
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('thr.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">Batal</a>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5">
                            <i class="ti ti-check"></i>
                            <span>Simpan & Kalkulasi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
