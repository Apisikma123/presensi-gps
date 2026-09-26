@extends('layouts.app')
@section('titlepage', 'Pusat Kasus & Insiden HR')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item active">Kasus & Insiden</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Pusat Kasus, Insiden & Keluhan HR</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($incidents->total() ?? 0) }} Total
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Hubungan industrial, mediasi internal, pelaporan insiden, dan catatan penyelesaian perselisihan kerja.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalAddIncident" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-plus" style="font-size: 16px;"></i>
            <span>Laporkan Kasus / Insiden</span>
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible mb-3" role="alert" style="border-radius: 10px; border: 1px solid #bbf7d0; background: #f0fdf4; color: #15803d;">
        <div class="d-flex align-items-center">
            <i class="ti ti-circle-check fs-5 me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
    </div>
@endif

<!-- Incidents Table Card -->
<div class="table-karyawan-wrapper mb-3 w-100 max-w-full" style="border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0; background: #FFFFFF !important; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
    <div class="table-responsive w-100 max-w-full" style="overflow-x: auto;">
        <table class="table table-hover align-middle w-100 mb-0">
            <thead class="table-light">
                <tr>
                    <th>NO. KASUS & JUDUL</th>
                    <th>KATEGORI</th>
                    <th>TGL KEJADIAN</th>
                    <th>PIHAK TERKAIT</th>
                    <th>STATUS</th>
                    <th>PENYELESAIAN</th>
                    <th class="text-end">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incidents as $inc)
                <tr>
                    <td>
                        <span class="font-mono text-muted small d-block">{{ $inc->case_number }}</span>
                        <span class="fw-semibold text-dark">{{ $inc->title }}</span>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark font-mono">{{ $inc->category_label }}</span>
                    </td>
                    <td>{{ $inc->incident_date->format('d M Y') }}</td>
                    <td>
                        @if($inc->subject)
                            <div><span class="text-muted small">Subjek:</span> <span class="fw-semibold text-dark">{{ $inc->subject->nama_karyawan }}</span></div>
                        @endif
                        @if($inc->reporter)
                            <div><span class="text-muted small">Pelapor:</span> {{ $inc->reporter->nama_karyawan }}</div>
                        @endif
                        @if(!$inc->subject && !$inc->reporter)
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>{!! $inc->status_badge_html !!}</td>
                    <td>
                        @if($inc->status === 'RESOLVED')
                            <div class="text-success small fw-semibold"><i class="ti ti-check me-1"></i>Selesai</div>
                            <div class="text-muted small">{{ $inc->resolved_at ? $inc->resolved_at->format('d M Y') : '-' }}</div>
                        @else
                            <span class="text-muted small">Dalam Penanganan</span>
                        @endif
                    </td>
                    <td class="text-end">
                        @if($inc->status !== 'RESOLVED' && $inc->status !== 'CLOSED')
                        <button type="button" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modalResolveIncident" 
                            data-incident-id="{{ $inc->id }}" 
                            data-incident-case="{{ $inc->case_number }}" 
                            data-incident-title="{{ $inc->title }}"
                            style="border-radius: 6px;">
                            <i class="ti ti-check"></i>
                            <span>Selesaikan</span>
                        </button>
                        @else
                        <span class="badge bg-light text-muted">Tuntas</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat kasus atau insiden tercatat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($incidents->hasPages())
<div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
    <div class="card-body py-2.5 px-3">
        {{ $incidents->links('pagination::bootstrap-5') }}
    </div>
</div>
@endif

<!-- Modal Add Incident -->
<div class="modal fade" id="modalAddIncident" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 12px;">
            <form action="{{ route('incident.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-dark mb-0">Registrasi Kasus / Insiden HR</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label required fw-semibold" style="font-size: 12px;">Judul Ringkas Kasus</label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Keterlambatan Berulang Shift Pagi, Kerusakan Mesin Kasir" required style="border-radius: 8px;">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label required fw-semibold" style="font-size: 12px;">Kategori Masalah</label>
                            <select name="category" class="form-select" required style="border-radius: 8px;">
                                @foreach($categories as $code => $label)
                                    <option value="{{ $code }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label required fw-semibold" style="font-size: 12px;">Tanggal Kejadian</label>
                            <input type="date" name="incident_date" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size: 12px;">Karyawan Terlapor / Terkait (Subjek)</label>
                            <select name="subject_nik" class="form-select" style="border-radius: 8px;">
                                <option value="">-- Tidak Ditentukan / Anonim --</option>
                                @foreach($karyawans as $k)
                                    <option value="{{ $k->nik }}">{{ $k->nama_karyawan }} (NIK: {{ $k->nik }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size: 12px;">Karyawan Pelapor</label>
                            <select name="reporter_nik" class="form-select" style="border-radius: 8px;">
                                <option value="">-- Tidak Ditentukan / Internal --</option>
                                @foreach($karyawans as $k)
                                    <option value="{{ $k->nik }}">{{ $k->nama_karyawan }} (NIK: {{ $k->nik }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required fw-semibold" style="font-size: 12px;">Kronologi & Deskripsi Kejadian</label>
                        <textarea name="description" rows="4" class="form-control" placeholder="Uraikan fakta kejadian, saksi, dan dampak yang ditimbulkan secara objektif..." required style="border-radius: 8px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 8px;">Daftarkan Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Resolve Incident -->
<div class="modal fade" id="modalResolveIncident" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px;">
            <form id="formResolveIncident" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-dark mb-0">Penyelesaian Kasus / Insiden</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <div class="text-muted small">No. Kasus:</div>
                        <div class="font-mono fw-bold text-dark" id="resolveCaseNumber">-</div>
                        <div class="text-secondary small" id="resolveIncidentTitle">-</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required fw-semibold" style="font-size: 12px;">Tindakan Penyelesaian / Solusi HR</label>
                        <textarea name="resolution_notes" rows="4" class="form-control" placeholder="Uraikan tindak lanjut mediasi, sanksi yang disepakati, atau solusi pencegahan..." required style="border-radius: 8px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-success" style="border-radius: 8px;">Selesaikan Kasus (Resolved)</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('myscript')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalResolve = document.getElementById('modalResolveIncident');
        if (modalResolve) {
            modalResolve.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const incidentId = button.getAttribute('data-incident-id');
                const caseNumber = button.getAttribute('data-incident-case');
                const title = button.getAttribute('data-incident-title');
                
                document.getElementById('resolveCaseNumber').textContent = caseNumber;
                document.getElementById('resolveIncidentTitle').textContent = title;
                document.getElementById('formResolveIncident').action = `/incidents/${incidentId}/resolve`;
            });
        }
    });
</script>
@endpush
@endsection
