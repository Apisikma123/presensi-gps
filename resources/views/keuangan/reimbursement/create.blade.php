@extends('layouts.app')
@section('titlepage', 'Ajukan Klaim Reimbursement')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Keuangan & Payroll</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reimbursement.index') }}">Reimbursement</a></li>
    <li class="breadcrumb-item active">Ajukan Klaim</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <h4 class="page-title mb-1">Ajukan Klaim Reimbursement</h4>
        <p class="page-subtitle text-muted mb-0">Isi rincian pengeluaran operasional dan sertakan lampiran kuitansi/nota yang valid.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <a href="{{ route('reimbursement.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 500; padding: 0 14px;">
            <i class="ti ti-arrow-left" style="font-size: 16px;"></i>
            <span>Kembali</span>
        </a>
    </div>
</div>

{{-- Form Card --}}
<div class="row">
    <div class="col-lg-8 col-xl-6">
        <div class="card mb-4" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02); overflow: hidden;">
            <div class="card-header py-3 px-4 d-flex align-items-center gap-2" style="background: #FAF9F8; border-bottom: 1px solid #F1F5F9;">
                <i class="ti ti-receipt fs-5 text-primary"></i>
                <h6 class="fw-bold mb-0 text-dark">Formulir Pengajuan Klaim</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('reimbursement.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark required">Karyawan Pemohon</label>
                        <select name="nik" class="form-select @error('nik') is-invalid @enderror" required>
                            <option value="">Pilih Karyawan</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->nik }}" {{ old('nik') == $emp->nik ? 'selected' : '' }}>
                                    {{ $emp->nik }} - {{ $emp->nama_karyawan }}
                                </option>
                            @endforeach
                        </select>
                        @error('nik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark required">Jenis Reimbursement</label>
                            <select name="reimbursement_type_id" class="form-select @error('reimbursement_type_id') is-invalid @enderror" required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($types as $t)
                                    <option value="{{ $t->id }}" {{ old('reimbursement_type_id') == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('reimbursement_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark required">Tanggal Transaksi</label>
                            <input type="date" name="claim_date" class="form-control font-mono @error('claim_date') is-invalid @enderror"
                                value="{{ old('claim_date', date('Y-m-d')) }}" required>
                            @error('claim_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark required">Nominal Pengeluaran (IDR)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 font-mono">Rp</span>
                            <input type="number" name="amount" class="form-control font-mono border-start-0 @error('amount') is-invalid @enderror"
                                value="{{ old('amount') }}" min="1000" step="500" required placeholder="0">
                        </div>
                        @error('amount')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Keterangan & Keperluan</label>
                        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror"
                            placeholder="Jelaskan keperluan pengeluaran dan rincian transaksi...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">Lampiran Bukti / Nota (PDF / Gambar)</label>
                        <input type="file" name="receipt" class="form-control @error('receipt') is-invalid @enderror"
                            accept="image/jpeg,image/png,image/jpg,application/pdf">
                        <small class="text-muted d-block mt-1">Maksimal ukuran file: 5MB (JPG, PNG, PDF)</small>
                        @error('receipt')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('reimbursement.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 500; padding: 0 16px;">Batal</a>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 18px;">
                            <i class="ti ti-check" style="font-size: 16px;"></i>
                            <span>Kirim Pengajuan Klaim</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
