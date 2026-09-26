@extends('layouts.app')
@section('titlepage', 'Kelola Gaji: ' . $karyawan->nama_karyawan)

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Keuangan & Payroll</a></li>
    <li class="breadcrumb-item"><a href="{{ route('employee_salary.index') }}">Struktur Gaji Karyawan</a></li>
    <li class="breadcrumb-item active">Kelola Gaji</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="page-title mb-1">Kelola Struktur Gaji: {{ $karyawan->nama_karyawan }}</h4>
        <p class="page-subtitle text-muted mb-0 font-mono" style="font-size: 12.5px;">
            NIK: {{ $karyawan->nik }} &bull; Dept: {{ $karyawan->departemen->nama_dept ?? '-' }} &bull; Jabatan: {{ $karyawan->jabatan->nama_jabatan ?? '-' }}
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('employee_salary.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible mb-4" role="alert">
        <div class="fw-bold mb-1">Periksa kembali data gaji:</div>
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
                    <i class="ti ti-wallet me-1.5 text-primary"></i> Komponen Upah & Potongan
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('employee_salary.update', $karyawan->nik) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Earnings --}}
                    <div class="mb-4">
                        <div class="text-uppercase fw-bold mb-2 pb-1 border-bottom" style="font-size: 11px; color: #15803D; letter-spacing: 0.06em;">
                            I. Komponen Penghasilan & Tunjangan
                        </div>
                        @foreach ($components->where('type', 'EARNING') as $comp)
                            @php
                                $val = old("components.{$comp->id}", optional($assignments->get($comp->id))->amount ?? $comp->default_amount);
                            @endphp
                            <div class="row align-items-center mb-2.5">
                                <div class="col-6">
                                    <label class="form-label mb-0 fw-medium" style="font-size: 13px; color: #3C2A21;">
                                        {{ $comp->name }}
                                    </label>
                                    <div class="text-muted" style="font-size: 11px;">
                                        {{ $comp->code }} &bull; {{ $comp->is_fixed ? 'Upah Tetap' : 'Tunjangan Variabel' }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white" style="font-size: 12px; color: #755841;">Rp</span>
                                        <input type="number" name="components[{{ $comp->id }}]" class="form-control"
                                            value="{{ (int) $val }}" min="0" step="1000"
                                            style="font-family: 'JetBrains Mono', monospace; font-size: 13px; border-radius: 0 6px 6px 0;">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Deductions --}}
                    <div class="mb-4">
                        <div class="text-uppercase fw-bold mb-2 pb-1 border-bottom" style="font-size: 11px; color: #BA1A1A; letter-spacing: 0.06em;">
                            II. Komponen Potongan Tetap (Jika Ada)
                        </div>
                        @foreach ($components->where('type', 'DEDUCTION') as $comp)
                            @php
                                $val = old("components.{$comp->id}", optional($assignments->get($comp->id))->amount ?? 0);
                            @endphp
                            <div class="row align-items-center mb-2.5">
                                <div class="col-6">
                                    <label class="form-label mb-0 fw-medium" style="font-size: 13px; color: #3C2A21;">
                                        {{ $comp->name }}
                                    </label>
                                    <div class="text-muted" style="font-size: 11px;">
                                        {{ $comp->code }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white" style="font-size: 12px; color: #BA1A1A;">- Rp</span>
                                        <input type="number" name="components[{{ $comp->id }}]" class="form-control"
                                            value="{{ (int) $val }}" min="0" step="1000"
                                            style="font-family: 'JetBrains Mono', monospace; font-size: 13px; border-radius: 0 6px 6px 0;">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="{{ route('employee_salary.index') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5">
                            <i class="ti ti-device-floppy"></i>
                            <span>Simpan Struktur Gaji</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
