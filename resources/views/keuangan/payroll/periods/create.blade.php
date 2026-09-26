@extends('layouts.app')
@section('titlepage', 'Buka Periode Payroll')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Keuangan & Payroll</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Periode Penggajian</a></li>
    <li class="breadcrumb-item active">Buka Periode Baru</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="page-title mb-1">Buka Periode Penggajian Baru</h4>
        <p class="page-subtitle text-muted mb-0">Konfigurasi rentang siklus cutoff presensi, lembur, dan tanggal pembayaran gaji.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible mb-4" role="alert">
        <div class="fw-bold mb-1">Periksa kembali data periode:</div>
        <ul class="mb-0 ps-3" style="font-size: 13px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-header border-bottom py-3" style="background: #FAF9F8;">
                <h5 class="card-title mb-0" style="font-family: 'Outfit', sans-serif; font-size: 15px; color: #3C2A21;">
                    <i class="ti ti-calendar-event me-1.5 text-primary"></i> Konfigurasi Siklus Penggajian
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('payroll.store') }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size: 12px; font-weight: 600; color: #4F4540; text-transform: uppercase;">
                                Bulan Periode <span class="text-danger">*</span>
                            </label>
                            <select name="period_month" class="form-select" required style="border-radius: 8px; font-size: 13.5px;">
                                @php
                                    $months = [
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                    ];
                                @endphp
                                @foreach ($months as $num => $name)
                                    <option value="{{ $num }}" {{ old('period_month', $suggestedMonth) == $num ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size: 12px; font-weight: 600; color: #4F4540; text-transform: uppercase;">
                                Tahun Periode <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="period_year" class="form-control"
                                value="{{ old('period_year', $suggestedYear) }}" required min="2020" max="2035"
                                style="border-radius: 8px; font-family: 'JetBrains Mono', monospace; font-size: 13.5px;">
                        </div>
                    </div>

                    {{-- Cutoff Dates --}}
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size: 12px; font-weight: 600; color: #4F4540; text-transform: uppercase;">
                                Mulai Cutoff Presensi / Lembur <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="cutoff_start" class="form-control"
                                value="{{ old('cutoff_start', $defaultCutoffStart) }}" required style="border-radius: 8px; font-size: 13.5px;">
                            <span class="text-muted" style="font-size: 11px;">Default tanggal 26 bulan sebelumnya.</span>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size: 12px; font-weight: 600; color: #4F4540; text-transform: uppercase;">
                                Selesai Cutoff Presensi / Lembur <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="cutoff_end" class="form-control"
                                value="{{ old('cutoff_end', $defaultCutoffEnd) }}" required style="border-radius: 8px; font-size: 13.5px;">
                            <span class="text-muted" style="font-size: 11px;">Default tanggal 25 bulan berjalan.</span>
                        </div>
                    </div>

                    {{-- Tanggal Pembayaran --}}
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; color: #4F4540; text-transform: uppercase;">
                            Tanggal Pembayaran (Payday) <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="payment_date" class="form-control"
                            value="{{ old('payment_date', $defaultPaymentDate) }}" required style="border-radius: 8px; font-size: 13.5px;">
                    </div>

                    {{-- Catatan --}}
                    <div class="mb-4">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; color: #4F4540; text-transform: uppercase;">
                            Catatan Internal Periode (Opsional)
                        </label>
                        <textarea name="notes" rows="2" class="form-control" style="border-radius: 8px; font-size: 13px;"
                            placeholder="Keterangan tambahan untuk siklus ini...">{{ old('notes') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5">
                            <i class="ti ti-check"></i>
                            <span>Buka Periode</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
