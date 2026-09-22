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
                    <span class="badge" style="background: rgba(30, 77, 62, 0.08); color: #1E4D3E; border: 1px solid rgba(30, 77, 62, 0.15); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                        {{ number_format($dispensasi->total(), 0, ',', '.') }} Total
                    </span>
                </h5>
                <small class="text-muted" style="font-size: 12px;">Dispensasi batas toleransi keterlambatan untuk jam masuk shift.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-1.5" id="btnCreateDispensasi" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
                    <i class="ti ti-plus" style="font-size: 16px;"></i>
                    <span>Tambah Dispensasi</span>
                </a>
            </div>
        </div>

        <!-- Search & Filter Bar (Standardized Compact Admin Filter Toolbar) -->
        <div class="card admin-filter-toolbar mb-3">
            <form action="{{ route('dispensasi.index') }}" method="GET" class="m-0">
                <div class="row g-2 align-items-center">
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-6 col-12">
                        <x-input-with-icon label="" value="{{ Request('dari') }}" name="dari" icon="ti ti-calendar"
                            datepicker="flatpickr-date" placeholder="Dari Tanggal" hideLabel="true" />
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-6 col-12">
                        <x-input-with-icon label="" value="{{ Request('sampai') }}" name="sampai" icon="ti ti-calendar"
                            datepicker="flatpickr-date" placeholder="Sampai Tanggal" hideLabel="true" />
                    </div>
                    <div class="col-xl col-lg col-md-6 col-12">
                        <x-input-with-icon label="" value="{{ Request('nama_karyawan') }}" name="nama_karyawan"
                            icon="ti ti-search" placeholder="Cari nama karyawan..." hideLabel="true" />
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
                        <select name="status" id="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="PENDING" {{ Request('status') === 'PENDING' ? 'selected' : '' }}>Pending</option>
                            <option value="APPROVED" {{ Request('status') === 'APPROVED' ? 'selected' : '' }}>Disetujui</option>
                            <option value="REJECTED" {{ Request('status') === 'REJECTED' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex align-items-center gap-1.5">
                            <button class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5" type="submit">
                                <i class="ti ti-search" style="font-size: 14px;"></i>
                                <span>Cari Data</span>
                            </button>
                            @if (Request('dari') || Request('sampai') || Request('nama_karyawan') || Request('status'))
                                <a href="{{ route('dispensasi.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1" title="Reset Filter">
                                    <i class="ti ti-refresh" style="font-size: 14px;"></i>
                                    <span>Reset</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
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
                                        <span class="badge-status badge-status-approved">
                                            <span class="badge-status-dot"></span>Disetujui
                                        </span>
                                    @elseif ($d->status === 'REJECTED')
                                        <span class="badge-status badge-status-rejected">
                                            <span class="badge-status-dot"></span>Ditolak
                                        </span>
                                    @else
                                        <span class="badge-status badge-status-pending">
                                            <span class="badge-status-dot"></span>Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 text-end">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        @php
                                            $isSelfDispensasi = (auth()->user()->userkaryawan?->nik === $d->nik) || (auth()->user()->username === $d->nik);
                                        @endphp
                                        @if ($isSelfDispensasi)
                                            <span class="badge bg-secondary-subtle text-secondary py-1 px-2" style="font-size: 11px;" title="Pengajuan Anda sendiri (Self-approval dilarang)">
                                                <i class="ti ti-user-x me-1"></i>Self
                                            </span>
                                        @else
                                            <button class="btnApprove btn-action-tbl btn-action-approve" id_dispensasi="{{ $d->id }}" title="Approval">
                                                <i class="ti ti-check"></i>
                                            </button>
                                        @endif
                                        <form action="{{ route('dispensasi.destroy', $d->id) }}" method="POST" class="d-inline deleteform m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btnDelete btn-action-tbl btn-action-delete delete-confirm" type="submit" title="Hapus">
                                                <i class="ti ti-trash"></i>
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

@endsection

@push('myscript')
<script>
    $(function() {
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
