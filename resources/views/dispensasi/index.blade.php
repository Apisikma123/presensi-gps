@extends('layouts.app')
@section('titlepage', 'Dispensasi Keterlambatan')

@section('content')
@section('navigasi')
    <span>Persetujuan Izin & Dispensasi</span>
@endsection

<div class="row">
    <div class="col-12">
        <div class="nav-align-top mb-3">
            @include('layouts.navigation.nav_pengajuan_absen')
        </div>

        <div id="izin-tab-pane" data-no-spa="true" style="position: relative; min-height: 300px; transition: opacity 0.15s ease;">
        <!-- Top Header Toolbar -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div>
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <span>Dispensasi Keterlambatan</span>
                    <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                        {{ $dispensasi->total() }} Total
                    </span>
                </h5>
                <small class="text-muted" style="font-size: 12px;">Dispensasi batas toleransi keterlambatan untuk jam masuk shift.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="#" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5" id="btnCreateDispensasi" style="height: 36px; border-radius: 8px;">
                    <i class="ti ti-plus"></i>
                    <span>Tambah Dispensasi</span>
                </a>
            </div>
        </div>

        <!-- Search & Filter Bar (Standardized Admin Filter Toolbar) -->
        <div class="card admin-filter-toolbar mb-3">
            <div class="card-body p-3">
                <form action="{{ route('dispensasi.index') }}" method="GET">
                    <div class="row g-2 align-items-center">
                        <div class="col-lg-3 col-md-4 col-6">
                            <label class="form-label text-xs fw-bold text-muted mb-1 d-block">Dari Tanggal</label>
                            <x-input-with-icon label="" value="{{ Request('dari') }}" name="dari" icon="ti ti-calendar"
                                datepicker="flatpickr-date" placeholder="Dari Tanggal" hideLabel="true" />
                        </div>
                        <div class="col-lg-3 col-md-4 col-6">
                            <label class="form-label text-xs fw-bold text-muted mb-1 d-block">Sampai Tanggal</label>
                            <x-input-with-icon label="" value="{{ Request('sampai') }}" name="sampai" icon="ti ti-calendar"
                                datepicker="flatpickr-date" placeholder="Sampai Tanggal" hideLabel="true" />
                        </div>
                        <div class="col-lg-3 col-md-4 col-12">
                            <label class="form-label text-xs fw-bold text-muted mb-1 d-block">Cari Karyawan</label>
                            <x-input-with-icon label="" value="{{ Request('nama_karyawan') }}" name="nama_karyawan"
                                icon="ti ti-search" placeholder="Cari nama karyawan..." hideLabel="true" />
                        </div>
                        <div class="col-lg-3 col-md-4 col-6">
                            <label class="form-label text-xs fw-bold text-muted mb-1 d-block">Status Dispensasi</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="PENDING" {{ Request('status') === 'PENDING' ? 'selected' : '' }}>Pending</option>
                                <option value="APPROVED" {{ Request('status') === 'APPROVED' ? 'selected' : '' }}>Disetujui</option>
                                <option value="REJECTED" {{ Request('status') === 'REJECTED' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2 pt-1">
                            @if (Request('dari') || Request('sampai') || Request('nama_karyawan') || Request('status'))
                                <a href="{{ route('dispensasi.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1" style="height: 38px; padding: 0 14px;" title="Reset Filter">
                                    <i class="ti ti-refresh" style="font-size: 14px;"></i>
                                    <span>Reset</span>
                                </a>
                            @endif
                            <button class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5" type="submit" style="height: 38px; min-width: 130px;">
                                <i class="ti ti-search" style="font-size: 15px;"></i>
                                <span>Cari Data</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 10px; overflow: hidden; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                    <thead style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0;">
                        <tr>
                            <th class="text-uppercase font-mono text-muted py-2.5 px-3" style="font-size: 11px; width: 50px;">No</th>
                            <th class="text-uppercase font-mono text-muted py-2.5 px-3" style="font-size: 11px;">Tanggal</th>
                            <th class="text-uppercase font-mono text-muted py-2.5 px-3" style="font-size: 11px;">Karyawan</th>
                            <th class="text-uppercase font-mono text-muted py-2.5 px-3" style="font-size: 11px;">Batas Toleransi</th>
                            <th class="text-uppercase font-mono text-muted py-2.5 px-3" style="font-size: 11px;">Alasan</th>
                            <th class="text-uppercase font-mono text-muted py-2.5 px-3 text-center" style="font-size: 11px;">Status</th>
                            <th class="text-uppercase font-mono text-muted py-2.5 px-3 text-end" style="font-size: 11px; width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($dispensasi as $d)
                            <tr>
                                <td class="px-3 font-mono text-muted">{{ $loop->iteration + $dispensasi->firstItem() - 1 }}</td>
                                <td class="px-3 fw-medium text-dark font-mono">
                                    {{ date('d/m/Y', strtotime($d->tanggal)) }}
                                </td>
                                <td class="px-3">
                                    <div class="fw-semibold text-dark">{{ $d->karyawan->nama_karyawan ?? $d->nik }}</div>
                                    <small class="text-muted font-mono">{{ $d->nik }}</small>
                                </td>
                                <td class="px-3 font-mono fw-bold text-primary">
                                    {{ $d->batas_dispensasi }}
                                </td>
                                <td class="px-3 text-secondary" style="max-width: 250px;">
                                    {{ $d->alasan }}
                                </td>
                                <td class="px-3 text-center">
                                    @if ($d->status === 'APPROVED')
                                        <span class="badge badge-status-approved">Disetujui</span>
                                    @elseif ($d->status === 'REJECTED')
                                        <span class="badge badge-status-rejected">Ditolak</span>
                                    @else
                                        <span class="badge badge-status-pending">Pending</span>
                                    @endif
                                </td>
                                <td class="px-3 text-end">
                                    <div class="d-inline-flex gap-1">
                                        <button class="btn btn-xs btn-outline-primary btnApproveDispensasi" data-id="{{ $d->id }}" title="Approval">
                                            <i class="ti ti-check" style="font-size: 14px;"></i>
                                        </button>
                                        <form action="{{ route('dispensasi.destroy', $d->id) }}" method="POST" class="d-inline deleteform">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-xs btn-outline-danger btnDelete" type="submit" title="Hapus">
                                                <i class="ti ti-trash" style="font-size: 14px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="ti ti-clock-off fs-2 mb-2 d-block"></i>
                                    Tidak ada data permohonan dispensasi keterlambatan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($dispensasi->hasPages())
                <div class="p-3 border-top">
                    {{ $dispensasi->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
        </div>
    </div>
</div>

<x-modal-form id="modalDispensasi" size="modal-md" title="Dispensasi Keterlambatan" />

@endsection

@push('myscript')
<script>
    $(function() {
        $('#btnCreateDispensasi').click(function(e) {
            e.preventDefault();
            $('#modalDispensasi').modal('show');
            $('#modalDispensasi').find('.modal-title').text('Tambah Dispensasi Keterlambatan');
            $('#modalDispensasi').find('#loadmodal').load('{{ route("dispensasi.create") }}');
        });

        $('.btnApproveDispensasi').click(function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            $('#modalDispensasi').modal('show');
            $('#modalDispensasi').find('.modal-title').text('Persetujuan Dispensasi Keterlambatan');
            $('#modalDispensasi').find('#loadmodal').load(`/dispensasi/${id}/approve`);
        });

        $('.deleteform').submit(function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Data dispensasi ini akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
