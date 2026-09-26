@extends('layouts.app')
@section('titlepage', 'Izin Sakit')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Persetujuan Izin Sakit</li>
@endsection

@section('content')

<div class="row">
    <div class="col-12">
        <div class="nav-align-top mb-3">
            @include('layouts.navigation.nav_pengajuan_absen')
        </div>

        <div id="izin-tab-pane" style="position: relative; min-height: 300px; transition: opacity 0.15s ease;">
        <!-- Top Header Toolbar -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div>
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <span>Pengajuan Izin Sakit</span>
                    <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                        {{ $izinsakit->total() }} Total
                    </span>
                </h5>
                <small class="text-muted" style="font-size: 12px;">Daftar permohonan izin sakit dan lampiran surat dokter karyawan.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                @can('izinsakit.create')
                    <a href="#" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5" id="btnCreateIzinSakit" style="height: 36px; border-radius: 8px;">
                        <i class="ti ti-plus"></i>
                        <span>Tambah Izin Sakit</span>
                    </a>
                @endcan
            </div>
        </div>

        <!-- Search & Filter Bar (Standardized Compact Admin Filter Toolbar) -->
        <div class="card admin-filter-toolbar mb-3">
            <form action="{{ route('izinsakit.index') }}" method="GET" class="m-0">
                <div class="row g-2 align-items-center">
                    <div class="col-xl-2 col-lg-2 col-md-3 col-6">
                        <x-input-with-icon label="" value="{{ Request('dari') }}" name="dari" icon="ti ti-calendar"
                            datepicker="flatpickr-date" placeholder="Dari Tanggal" hideLabel="true" />
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-3 col-6">
                        <x-input-with-icon label="" value="{{ Request('sampai') }}" name="sampai" icon="ti ti-calendar"
                            datepicker="flatpickr-date" placeholder="Sampai Tanggal" hideLabel="true" />
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-3 col-6">
                        <select name="status" id="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="0" {{ Request('status') === '0' ? 'selected' : '' }}>Pending</option>
                            <option value="1" {{ Request('status') == '1' ? 'selected' : '' }}>Disetujui</option>
                            <option value="2" {{ Request('status') == '2' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-3 col-6">
                        <select name="kode_cabang" id="kode_cabang" class="form-select">
                            <option value="">Semua Outlet</option>
                            @foreach ($cabang as $d)
                                <option value="{{ $d->kode_cabang }}" {{ Request('kode_cabang') == $d->kode_cabang ? 'selected' : '' }}>
                                    {{ textUpperCase($d->nama_cabang) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl col-lg col-md-6 col-12">
                        <x-input-with-icon label="" value="{{ Request('nama_karyawan') }}" name="nama_karyawan"
                            icon="ti ti-search" placeholder="Cari nama karyawan..." hideLabel="true" />
                    </div>
                    <div class="col-auto">
                        <div class="d-flex align-items-center gap-1.5">
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5">
                                <i class="ti ti-search" style="font-size: 14px;"></i>
                                <span>Cari</span>
                            </button>
                            @if (Request('dari') || Request('sampai') || Request('nama_karyawan') || Request('status') !== null || Request('kode_cabang'))
                                <a href="{{ route('izinsakit.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1" title="Reset Filter">
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
        <div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;" class="text-center">NO</th>
                            <th>KARYAWAN</th>
                            <th>OUTLET & JABATAN</th>
                            <th>PERIODE IZIN</th>
                            <th>KETERANGAN & SURAT DOKTER</th>
                            <th class="text-center">STATUS APPROVAL</th>
                            <th class="text-end" style="width: 120px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($izinsakit as $d)
                            @php
                                $lama = hitungHari($d->dari, $d->sampai);
                                $words = explode(' ', $d->nama_karyawan);
                                $initials = '';
                                foreach ($words as $w) {
                                    if (isset($w[0])) $initials .= $w[0];
                                }
                                $initials = strtoupper(substr($initials, 0, 2));
                            @endphp
                            <tr>
                                <td class="text-center font-mono text-muted" style="font-size: 12px;">
                                    {{ $loop->iteration + ($izinsakit->currentPage() - 1) * $izinsakit->perPage() }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        @if (!empty($d->foto))
                                            <img src="{{ getfotoKaryawan($d->foto) }}" alt="Avatar" class="rounded-circle flex-shrink-0"
                                                style="width: 36px; height: 36px; object-fit: cover; border: 1px solid #E2E8F0;"
                                                onerror="this.onerror=null;this.src='{{ asset('assets/img/avatars/default.png') }}';">
                                        @else
                                            <div class="rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center fw-bold"
                                                style="width: 36px; height: 36px; background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); font-size: 12px; border: 1px solid rgba(60, 42, 33, 0.15);">
                                                {{ $initials }}
                                            </div>
                                        @endif
                                        <div>
                                            <span class="fw-bold text-dark d-block" style="font-size: 13px;">{{ $d->nama_karyawan }}</span>
                                            <span class="badge bg-light text-muted font-mono" style="border: 1px solid #E2E8F0; font-size: 10.5px;">
                                                {{ $d->nik_show ?? $d->nik }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark d-block" style="font-size: 12.5px;">{{ $d->nama_cabang }}</span>
                                    <small class="text-muted" style="font-size: 11.5px;">{{ $d->nama_jabatan }} • {{ $d->nama_dept }}</small>
                                </td>
                                <td>
                                    <span class="font-mono text-dark fw-semibold d-block" style="font-size: 12px;">
                                        {{ date('d M Y', strtotime($d->dari)) }} - {{ date('d M Y', strtotime($d->sampai)) }}
                                    </span>
                                    <div class="d-flex align-items-center gap-1 mt-0.5">
                                        <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 10px;">{{ $d->kode_izin_sakit }}</span>
                                        <span class="badge bg-label-primary font-mono" style="font-size: 10px;">{{ $lama }} Hari</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted text-truncate d-inline-block" style="max-width: 220px; font-size: 12px;" title="{{ $d->keterangan }}">
                                        {{ $d->keterangan ?? '-' }}
                                    </span>
                                    @if (!empty($d->doc_sid))
                                        <div class="mt-0.5">
                                            <a href="{{ route('file.sid', ['filename' => $d->doc_sid]) }}" target="_blank" class="badge bg-label-info text-decoration-none d-inline-flex align-items-center gap-1" style="font-size: 10px;">
                                                <i class="ti ti-file-text"></i>
                                                <span>Lihat SID</span>
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($d->status == 0)
                                        <span class="badge-status badge-status-pending">
                                            <span class="badge-status-dot"></span>Pending
                                        </span>
                                    @elseif ($d->status == 1)
                                        <span class="badge-status badge-status-approved">
                                            <span class="badge-status-dot"></span>Disetujui
                                        </span>
                                    @elseif ($d->status == 2)
                                        <span class="badge-status badge-status-rejected">
                                            <span class="badge-status-dot"></span>Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        @can('izinsakit.approve')
                                            @php
                                                $isSelf = (auth()->user()->userkaryawan?->nik === $d->nik) || (auth()->user()->username === $d->nik);
                                            @endphp
                                            @if ($isSelf)
                                                <span class="badge bg-secondary-subtle text-secondary py-1 px-2" style="font-size: 11px;" title="Pengajuan Anda sendiri (Self-approval dilarang)">
                                                    <i class="ti ti-user-x me-1"></i>Self
                                                </span>
                                            @elseif ($d->status == 0)
                                                <button type="button" class="btnApprove btn-action-tbl btn-action-approve" kode_izin_sakit="{{ Crypt::encrypt($d->kode_izin_sakit) }}" title="Persetujuan Izin Sakit">
                                                    <i class="ti ti-check"></i>
                                                </button>
                                            @elseif ($d->status == 1 || $d->status == 2)
                                                <form method="POST" name="cancelapproveform" class="d-inline m-0"
                                                    action="{{ route('izinsakit.cancelapprove', Crypt::encrypt($d->kode_izin_sakit)) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="cancel-confirm btn-action-tbl btn-action-cancel" title="Batalkan Persetujuan">
                                                        <i class="ti ti-circle-minus"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan

                                        @can('izinsakit.index')
                                            <button type="button" class="btnShow btn-action-tbl btn-action-detail" kode_izin_sakit="{{ Crypt::encrypt($d->kode_izin_sakit) }}" title="Detail Izin Sakit">
                                                <i class="ti ti-file-description"></i>
                                            </button>
                                        @endcan

                                        @can('izinsakit.edit')
                                            @if ($d->status == 0)
                                                <button type="button" class="btnEdit btn-action-tbl btn-action-edit" kode_izin_sakit="{{ Crypt::encrypt($d->kode_izin_sakit) }}" title="Edit Izin Sakit">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                            @endif
                                        @endcan

                                        @can('izinsakit.delete')
                                            @if ($d->status == 0)
                                                <form method="POST" name="deleteform" class="deleteform d-inline m-0"
                                                    action="{{ route('izinsakit.delete', Crypt::encrypt($d->kode_izin_sakit)) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="delete-confirm btn-action-tbl btn-action-delete" title="Hapus Izin Sakit">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="ti ti-file-off text-muted fs-1 d-block mb-2" style="opacity: 0.4;"></i>
                                    <h6 class="mb-1 text-dark fw-semibold">Tidak Ada Data Pengajuan Izin Sakit</h6>
                                    <small class="text-muted">Gunakan filter pencarian di atas untuk menemukan data.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Footer -->
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
            <div class="card-body py-2.5 px-3">
                {{ $izinsakit->links('pagination::bootstrap-5') }}
            </div>
        </div>
        </div>
    </div>
</div>
@endsection
