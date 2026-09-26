@extends('layouts.app')
@section('titlepage', 'Bagan Struktur Organisasi')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item active">Struktur Organisasi</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Bagan Struktur Organisasi</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                Struktur Perusahaan
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Visualisasi hierarki departemen, pembagian divisi, dan persebaran karyawan antar unit kerja.</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="card admin-filter-toolbar mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
    <div class="card-body p-3">
        <form action="{{ route('org_chart.index') }}" method="GET" class="m-0">
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <select name="kode_dept" class="form-select" style="height: 38px; border-radius: 8px;">
                        <option value="">Semua Departemen</option>
                        @foreach($departemens as $d)
                            <option value="{{ $d->kode_dept }}" {{ $selectedDept == $d->kode_dept ? 'selected' : '' }}>{{ $d->nama_dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="kode_cabang" class="form-select" style="height: 38px; border-radius: 8px;">
                        <option value="">Semua Kantor / Cabang</option>
                        @foreach($cabangs as $c)
                            <option value="{{ $c->kode_cabang }}" {{ $selectedCabang == $c->kode_cabang ? 'selected' : '' }}>{{ $c->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5 w-100" style="height: 38px; border-radius: 8px; font-weight: 600;">
                        <i class="ti ti-search" style="font-size: 15px;"></i>
                        <span>Cari Data</span>
                    </button>
                </div>
                @if($selectedDept || $selectedCabang)
                <div class="col-md-2">
                    <a href="{{ route('org_chart.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1 w-100" style="height: 38px; border-radius: 8px; font-weight: 600;">
                        <i class="ti ti-refresh" style="font-size: 15px;"></i>
                        <span>Reset</span>
                    </a>
                </div>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tree View per Department -->
<div class="row g-3">
    @forelse($orgTree as $dept)
    <div class="col-12 mb-3">
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-light text-dark font-mono mb-1">{{ $dept->kode_dept }}</span>
                    <h5 class="card-title fw-bold text-dark mb-0">{{ $dept->nama_dept }}</h5>
                </div>
                <span class="badge bg-label-primary font-mono fw-bold">{{ $dept->karyawan->count() }} Anggota Tim</span>
            </div>
            <div class="card-body p-4">
                {{-- Divisi tags if any --}}
                @if($dept->divisi && $dept->divisi->isNotEmpty())
                <div class="mb-3 d-flex flex-wrap gap-2">
                    <span class="text-muted small align-self-center">Divisi / Unit:</span>
                    @foreach($dept->divisi as $div)
                        <span class="badge bg-label-secondary">{{ $div->nama_divisi }}</span>
                    @endforeach
                </div>
                @endif

                {{-- Employee Chips Grid --}}
                <div class="row g-2">
                    @forelse($dept->karyawan as $k)
                    <div class="col-md-6 col-lg-4">
                        <div class="card p-2 bg-white h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 8px;">
                            <div class="d-flex align-items-center gap-3">
                                @if($k->foto && file_exists(public_path('storage/karyawan/' . $k->foto)))
                                    <img src="{{ asset('storage/karyawan/' . $k->foto) }}" class="avatar rounded-circle" alt="{{ $k->nama_karyawan }}" style="width: 38px; height: 38px; object-fit: cover;">
                                @else
                                    <div class="avatar rounded-circle bg-label-primary d-flex align-items-center justify-content-center fw-bold font-mono" style="width: 38px; height: 38px; font-size: 13px;">
                                        {{ strtoupper(substr($k->nama_karyawan, 0, 2)) }}
                                    </div>
                                @endif
                                <div class="overflow-hidden">
                                    <div class="fw-semibold text-dark text-truncate" style="font-size: 13.5px;">{{ $k->nama_karyawan }}</div>
                                    <div class="text-primary small fw-semibold text-truncate" style="font-size: 12px;">{{ $k->jabatan->nama_jabatan ?? 'Staf' }}</div>
                                    <div class="text-muted small font-mono" style="font-size: 11px;">NIK: {{ $k->nik }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="text-muted small py-2">Belum ada karyawan aktif di departemen ini.</div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card p-4 text-center text-muted" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
            Tidak ditemukan departemen dengan filter yang dipilih.
        </div>
    </div>
    @endforelse
</div>
@endsection
