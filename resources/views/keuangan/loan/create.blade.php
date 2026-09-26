@extends('layouts.app')
@section('titlepage', 'Pengajuan Kasbon Baru')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Keuangan & Payroll</a></li>
    <li class="breadcrumb-item"><a href="{{ route('loan.index') }}">Kasbon & Pinjaman</a></li>
    <li class="breadcrumb-item active">Ajukan Baru</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="page-title mb-1">Pengajuan Pinjaman & Kasbon Baru</h4>
        <p class="page-subtitle text-muted mb-0">Tentukan karyawan pemohon, nominal pinjaman, tenor pengembalian bulanan, dan tanggal mulai cicilan.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('loan.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
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
                <i class="ti ti-cash fs-5 text-primary"></i>
                <h6 class="fw-bold mb-0 text-dark">Formulir Kasbon Karyawan</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('loan.store') }}" method="POST">
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
                            <label class="form-label fw-bold text-dark required">Nominal Pinjaman (IDR)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">Rp</span>
                                <input type="number" id="loanAmount" name="loan_amount" class="form-control font-mono border-start-0 @error('loan_amount') is-invalid @enderror"
                                    value="{{ old('loan_amount', 1000000) }}" min="100000" step="50000" required oninput="calculateEstimates()">
                            </div>
                            @error('loan_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark required">Tenor Angsuran (Bulan)</label>
                            <select id="tenorMonths" name="installment_months" class="form-select font-mono @error('installment_months') is-invalid @enderror" required onchange="calculateEstimates()">
                                @for ($m = 1; $m <= 24; $m++)
                                    <option value="{{ $m }}" {{ old('installment_months', 3) == $m ? 'selected' : '' }}>
                                        {{ $m }} Bulan
                                    </option>
                                @endfor
                            </select>
                            @error('installment_months')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Suku Bunga (%) <small class="text-muted fw-normal">(Opsional)</small></label>
                            <div class="input-group">
                                <input type="number" id="interestRate" name="interest_rate" class="form-control font-mono border-end-0 @error('interest_rate') is-invalid @enderror"
                                    value="{{ old('interest_rate', 0) }}" min="0" max="100" step="0.1" oninput="calculateEstimates()">
                                <span class="input-group-text bg-white border-start-0">%</span>
                            </div>
                            @error('interest_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark required">Mulai Pembayaran</label>
                            <input type="date" name="start_date" class="form-control font-mono @error('start_date') is-invalid @enderror"
                                value="{{ old('start_date', date('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Realtime Calculation Preview --}}
                    <div class="p-3 mb-3 rounded-2" style="background: #FAF9F8; border: 1px dashed #E2E8F0; font-size: 12.5px;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Total Kewajiban Pelunasan:</span>
                            <span id="previewTotal" class="fw-bold font-mono text-dark">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Estimasi Angsuran per Bulan:</span>
                            <span id="previewInstallment" class="fw-bold font-mono text-success">Rp 0 / bln</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">Catatan Tambahan</label>
                        <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                            placeholder="Tuliskan keperluan pinjaman atau kesepakatan kasbon...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('loan.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">Batal</a>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5">
                            <i class="ti ti-check"></i>
                            <span>Simpan Pengajuan Kasbon</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('myscript')
<script>
function formatRupiah(num) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num);
}

function calculateEstimates() {
    const amount = parseFloat(document.getElementById('loanAmount').value) || 0;
    const months = parseInt(document.getElementById('tenorMonths').value) || 1;
    const interest = parseFloat(document.getElementById('interestRate').value) || 0;

    const total = amount + (amount * (interest / 100));
    const monthly = total / Math.max(1, months);

    document.getElementById('previewTotal').innerText = formatRupiah(total);
    document.getElementById('previewInstallment').innerText = formatRupiah(monthly) + ' / bln';
}

document.addEventListener('DOMContentLoaded', calculateEstimates);
</script>
@endpush
@endsection
