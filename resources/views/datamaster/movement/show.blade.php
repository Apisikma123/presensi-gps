@extends('layouts.app')
@section('titlepage', 'Detail Riwayat Mutasi / SK')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Data Karyawan</a></li>
    <li class="breadcrumb-item"><a href="{{ route('movement.index') }}">Mutasi & Karir</a></li>
    <li class="breadcrumb-item active">Detail SK</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <a href="{{ route('movement.index') }}" class="text-decoration-none text-muted mb-1 d-inline-flex align-items-center gap-1" style="font-size: 13px;">
            <i class="ti ti-arrow-left"></i> Kembali ke Daftar Mutasi
        </a>
        <h4 class="page-title mb-1">Lembar Keputusan Perubahan Status / Mutasi</h4>
        <p class="page-subtitle text-muted mb-0">Nomor SK: {{ $movement->no_sk ?: '(Tanpa Nomor SK)' }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('movement.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>
</div>

<div class="card mx-auto shadow-sm" style="max-width: 850px; border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF;">
    <div class="card-body p-4">
        <!-- Employee Summary Header -->
        <div class="p-3 bg-light rounded d-flex align-items-center justify-content-between mb-4" style="border-radius: 8px;">
            <div class="d-flex align-items-center">
                <div class="avatar avatar-md bg-label-primary rounded-circle me-3 fw-bold fs-4 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    {{ $movement->karyawan ? strtoupper(substr($movement->karyawan->nama_karyawan, 0, 2)) : '??' }}
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">{{ $movement->karyawan ? $movement->karyawan->nama_karyawan : 'Karyawan: ' . $movement->nik }}</h5>
                    <div class="text-muted small font-mono">NIK: {{ $movement->nik }}</div>
                </div>
            </div>
            <div class="text-end">
                <div class="mb-1">{!! $movement->status_badge_html !!}</div>
                <span class="badge bg-label-info fw-semibold">{{ $movement->movement_type_label }}</span>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4">
                <div class="text-muted small text-uppercase" style="font-size: 11px;">Tanggal Efektif</div>
                <div class="fw-semibold text-dark font-mono">{{ $movement->effective_date ? $movement->effective_date->format('d F Y') : '-' }}</div>
            </div>
            <div class="col-6 col-md-4">
                <div class="text-muted small text-uppercase" style="font-size: 11px;">Dicatat Oleh</div>
                <div class="fw-semibold text-dark">{{ $movement->creator ? $movement->creator->name : 'Sistem' }}</div>
            </div>
            <div class="col-12 col-md-4">
                <div class="text-muted small text-uppercase" style="font-size: 11px;">Dokumen Pendukung</div>
                <div>
                    @if($movement->document_path)
                        <a href="{{ asset('storage/' . $movement->document_path) }}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                            <i class="ti ti-download"></i>Unduh Berkas SK
                        </a>
                    @else
                        <span class="text-muted small">Tidak ada lampiran berkas</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Comparison Table -->
        <h6 class="fw-bold text-primary mb-3"><i class="ti ti-arrows-exchange me-1"></i>Perbandingan Posisi & Penugasan</h6>
        <div class="table-responsive mb-4">
            <table class="table table-hover align-middle mb-0" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 8px;">
                <thead class="table-light">
                    <tr>
                        <th>BIDANG PENUGASAN</th>
                        <th>POSISI SEBELUMNYA (LAMA)</th>
                        <th>POSISI BARU (EFEKTIF)</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($movement->new_values))
                        @foreach($movement->new_values as $key => $newVal)
                            @php $oldVal = $movement->old_values[$key] ?? '-'; @endphp
                            <tr>
                                <td class="fw-semibold text-dark">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                                <td class="text-muted text-decoration-line-through">{{ $oldVal ?: '(kosong)' }}</td>
                                <td class="fw-bold text-success">{{ $newVal ?: '(dikosongkan)' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" class="text-center text-muted py-3">Tidak ada rincian perubahan atribut</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @if($movement->reason)
            <div class="mb-2">
                <div class="text-muted small fw-semibold" style="font-size: 11px;">Alasan & Pertimbangan Manajemen:</div>
                <div class="p-3 bg-light rounded text-dark mt-1" style="font-size: 13px; line-height: 1.5; border-radius: 8px;">{{ $movement->reason }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
