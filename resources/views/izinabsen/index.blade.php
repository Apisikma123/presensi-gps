@extends('layouts.app')
@section('titlepage', 'Izin Absen')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Persetujuan Izin Absen</li>
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
                    @if(request('tipe') === 'permisi')
                        <span>Pengajuan Izin Permisi (Jam Kerja & Pulang Cepat)</span>
                    @else
                        <span>Pengajuan Izin Absen (Seharian)</span>
                    @endif
                    <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                        {{ $izinabsen->total() }} Total
                    </span>
                </h5>
                <small class="text-muted" style="font-size: 12px;">
                    @if(request('tipe') === 'permisi')
                        Daftar permohonan permisi keluar kantor sementara atau pulang cepat karyawan.
                    @else
                        Daftar permohonan izin tidak masuk kerja seharian seluruh karyawan dan staf.
                    @endif
                </small>
            </div>
            <div class="d-flex align-items-center gap-2">
                @can('izinabsen.create')
                    <a href="#" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5" id="btnCreateIzinAbsen" style="height: 36px; border-radius: 8px;">
                        <i class="ti ti-plus"></i>
                        <span>Tambah Izin</span>
                    </a>
                @endcan
            </div>
        </div>

        <!-- Search & Filter Bar (Standardized Compact Admin Filter Toolbar) -->
        <div class="card admin-filter-toolbar mb-3">
            <form action="{{ route('izinabsen.index') }}" method="GET" class="m-0">
                @if(request('tipe'))
                    <input type="hidden" name="tipe" value="{{ request('tipe') }}">
                @endif
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
                                <a href="{{ route('izinabsen.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1" title="Reset Filter">
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
                            <th>KETERANGAN / ALASAN</th>
                            <th class="text-center">STATUS APPROVAL</th>
                            <th class="text-end" style="width: 120px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($izinabsen as $d)
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
                                    {{ $loop->iteration + ($izinabsen->currentPage() - 1) * $izinabsen->perPage() }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        @if (!empty($d->foto))
                                            <img src="{{ getfotoKaryawan($d->foto) }}" alt="Avatar" class="rounded-circle flex-shrink-0"
                                                style="width: 36px; height: 36px; object-fit: cover; border: 1px solid #E2E8F0;"
                                                onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                            <div class="rounded-circle flex-shrink-0 align-items-center justify-content-center fw-bold"
                                                style="display: none; width: 36px; height: 36px; background: var(--color-primary-soft, rgba(var(--bs-primary-rgb, 60, 42, 33), 0.08)); color: var(--color-primary, #3C2A21); font-size: 12px; border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb, 60, 42, 33), 0.15));">
                                                {{ $initials }}
                                            </div>
                                        @else
                                            <div class="rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center fw-bold"
                                                style="width: 36px; height: 36px; background: var(--color-primary-soft, rgba(var(--bs-primary-rgb, 60, 42, 33), 0.08)); color: var(--color-primary, #3C2A21); font-size: 12px; border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb, 60, 42, 33), 0.15));">
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
                                    @php
                                        $ketRaw = $d->keterangan ?? '';
                                        $isIzinJam = str_contains($ketRaw, '[Izin Jam:');
                                        $isPulangCepat = str_contains($ketRaw, '[Pulang Cepat:');
                                        
                                        $jamInfo = '';
                                        if ($isIzinJam && preg_match('/\[Izin Jam:\s*([^\]]+)\]/', $ketRaw, $m)) {
                                            $jamInfo = $m[1];
                                        } elseif ($isPulangCepat && preg_match('/\[Pulang Cepat:\s*([^\]]+)\]/', $ketRaw, $m)) {
                                            $jamInfo = 'Jam ' . $m[1];
                                        }
                                        $cleanKet = preg_replace('/^\[.*?\]\s*/', '', $ketRaw);
                                    @endphp
                                    <span class="font-mono text-dark fw-semibold d-block" style="font-size: 12px;">
                                        {{ date('d M Y', strtotime($d->dari)) }}
                                        @if($d->dari != $d->sampai) - {{ date('d M Y', strtotime($d->sampai)) }} @endif
                                    </span>
                                    <div class="d-flex align-items-center gap-1 mt-0.5 flex-wrap">
                                        <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 10px;">{{ $d->kode_izin }}</span>
                                        @if($isIzinJam)
                                            <span class="badge bg-label-info font-mono" style="font-size: 10px;"><i class="ti ti-clock me-0.5"></i>Permisi Jam ({{ $jamInfo }})</span>
                                        @elseif($isPulangCepat)
                                            <span class="badge bg-label-warning font-mono" style="font-size: 10px;"><i class="ti ti-door-exit me-0.5"></i>Pulang Cepat ({{ $jamInfo }})</span>
                                        @else
                                            <span class="badge bg-label-primary font-mono" style="font-size: 10px;">{{ $lama }} Hari</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="text-dark fw-medium text-truncate d-inline-block" style="max-width: 240px; font-size: 12px;" title="{{ $cleanKet }}">
                                        {{ $cleanKet ?: '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($d->status == 0)
                                        <span class="badge-status badge-status-pending">Pending</span>
                                    @elseif ($d->status == 1)
                                        <span class="badge-status badge-status-approved">Disetujui</span>
                                    @elseif ($d->status == 2)
                                        <span class="badge-status badge-status-rejected">Ditolak</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        @can('izinabsen.approve')
                                            @php
                                                $isSelf = (auth()->user()->userkaryawan?->nik === $d->nik) || (auth()->user()->username === $d->nik);
                                            @endphp
                                            @if ($isSelf)
                                                <span class="badge bg-secondary-subtle text-secondary py-1 px-2" style="font-size: 11px;" title="Pengajuan Anda sendiri (Self-approval dilarang)">
                                                    <i class="ti ti-user-x me-1"></i>Self
                                                </span>
                                            @elseif ($d->status == 0)
                                                <button type="button" class="btnApprove btn-action-tbl btn-action-approve" kode_izin="{{ Crypt::encrypt($d->kode_izin) }}" title="Persetujuan Izin">
                                                    <i class="ti ti-check"></i>
                                                </button>
                                            @elseif ($d->status == 1)
                                                <form method="POST" name="cancelapproveform" class="d-inline m-0"
                                                    action="{{ route('izinabsen.cancelapprove', Crypt::encrypt($d->kode_izin)) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="cancel-confirm btn-action-tbl btn-action-cancel" title="Batalkan Persetujuan">
                                                        <i class="ti ti-circle-minus"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan

                                        @can('izinabsen.index')
                                            <button type="button" class="btnShow btn-action-tbl btn-action-detail" kode_izin="{{ Crypt::encrypt($d->kode_izin) }}" title="Detail Izin">
                                                <i class="ti ti-file-description"></i>
                                            </button>
                                        @endcan

                                        @can('izinabsen.edit')
                                            @if ($d->status == 0)
                                                <button type="button" class="btnEdit btn-action-tbl btn-action-edit" kode_izin="{{ Crypt::encrypt($d->kode_izin) }}" title="Edit Izin">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                            @endif
                                        @endcan

                                        @can('izinabsen.delete')
                                            @if ($d->status == 0)
                                                <form method="POST" name="deleteform" class="deleteform d-inline m-0"
                                                    action="{{ route('izinabsen.delete', Crypt::encrypt($d->kode_izin)) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="delete-confirm btn-action-tbl btn-action-delete" title="Hapus Izin">
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
                                    <h6 class="mb-1 text-dark fw-semibold">Tidak Ada Data Pengajuan Izin</h6>
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
                {{ $izinabsen->links('pagination::bootstrap-5') }}
            </div>
        </div>
        </div>
    </div>
</div>
@endsection
