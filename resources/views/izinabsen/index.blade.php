@extends('layouts.app')
@section('titlepage', 'Izin Absen')

@section('content')
@section('navigasi')
    <span>Persetujuan Izin</span>
@endsection

<div class="row">
    <div class="col-12">
        <div class="nav-align-top mb-3">
            @include('layouts.navigation.nav_pengajuan_absen')
        </div>

        <!-- Top Header Toolbar -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div>
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <span>Pengajuan Izin Absen</span>
                    <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                        {{ $izinabsen->total() }} Total
                    </span>
                </h5>
                <small class="text-muted" style="font-size: 12px;">Daftar permohonan izin tidak masuk kerja karyawan outlet coffee shop.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                @can('izinabsen.create')
                    <a href="#" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5" id="btnCreate" style="height: 36px; border-radius: 8px;">
                        <i class="ti ti-plus"></i>
                        <span>Tambah Izin</span>
                    </a>
                @endcan
            </div>
        </div>

        <!-- Search & Filter Bar (Minimalist Single Seamless Card) -->
        <div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 10px; background: #FFFFFF; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);">
            <div class="card-body p-2.5">
                <form action="{{ route('izinabsen.index') }}" method="GET">
                    <div class="row g-2 align-items-center">
                        <div class="col-lg-2 col-md-4 col-6">
                            <x-input-with-icon label="" value="{{ Request('dari') }}" name="dari" icon="ti ti-calendar"
                                datepicker="flatpickr-date" placeholder="Dari Tanggal" hideLabel="true" />
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <x-input-with-icon label="" value="{{ Request('sampai') }}" name="sampai" icon="ti ti-calendar"
                                datepicker="flatpickr-date" placeholder="Sampai Tanggal" hideLabel="true" />
                        </div>
                        <div class="col-lg-3 col-md-4 col-12">
                            <x-input-with-icon label="" value="{{ Request('nama_karyawan') }}" name="nama_karyawan"
                                icon="ti ti-search" placeholder="Cari nama karyawan..." hideLabel="true" />
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <select name="status" id="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="0" {{ Request('status') === '0' ? 'selected' : '' }}>Pending</option>
                                <option value="1" {{ Request('status') == '1' ? 'selected' : '' }}>Disetujui</option>
                                <option value="2" {{ Request('status') == '2' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <select name="kode_cabang" id="kode_cabang" class="form-select">
                                <option value="">Semua Outlet</option>
                                @foreach ($cabang as $d)
                                    <option value="{{ $d->kode_cabang }}" {{ Request('kode_cabang') == $d->kode_cabang ? 'selected' : '' }}>
                                        {{ textUpperCase($d->nama_cabang) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-1 col-md-4 col-12 d-flex align-items-center gap-1.5">
                            <button type="submit" class="btn w-100 text-white fw-semibold d-inline-flex align-items-center justify-content-center gap-1 shadow-sm"
                                style="background-color: var(--theme-color-1, #1E4D3E); border: 1px solid #11382C; border-radius: 8px; height: 38px; font-size: 13px; transition: all 0.2s ease;" title="Cari">
                                <i class="ti ti-search" style="font-size: 15px;"></i>
                            </button>
                            @if (Request('dari') || Request('sampai') || Request('nama_karyawan') || Request('status') !== null || Request('kode_cabang'))
                                <a href="{{ route('izinabsen.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center p-0 flex-shrink-0"
                                    style="height: 38px; width: 38px; min-width: 38px; border-radius: 8px; border-color: #CBD5E1; color: #64748B;" title="Reset Filter">
                                    <i class="ti ti-refresh" style="font-size: 14px;"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
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
                                        @if (!empty($d->foto) && Storage::disk('public')->exists('/karyawan/' . $d->foto))
                                            <img src="{{ Storage::url('karyawan/'.$d->foto) }}" alt="Avatar" class="rounded-circle flex-shrink-0"
                                                style="width: 36px; height: 36px; object-fit: cover; border: 1px solid #E2E8F0;">
                                        @else
                                            <div class="rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center fw-bold"
                                                style="width: 36px; height: 36px; background: rgba(30, 77, 62, 0.08); color: #1E4D3E; font-size: 12px; border: 1px solid rgba(30, 77, 62, 0.15);">
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
                                        <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 10px;">{{ $d->kode_izin }}</span>
                                        <span class="badge bg-label-primary font-mono" style="font-size: 10px;">{{ $lama }} Hari</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted text-truncate d-inline-block" style="max-width: 220px; font-size: 12px;" title="{{ $d->keterangan }}">
                                        {{ $d->keterangan ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($d->status == 0)
                                        <span class="badge bg-label-warning font-mono" style="font-size: 10.5px;">Pending</span>
                                    @elseif ($d->status == 1)
                                        <span class="badge bg-label-success font-mono" style="font-size: 10.5px;">Disetujui</span>
                                    @elseif ($d->status == 2)
                                        <span class="badge bg-label-danger font-mono" style="font-size: 10.5px;">Ditolak</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        @can('izinabsen.approve')
                                            @if ($d->status == 0)
                                                <button type="button" class="btnEdit btnApprove" kode_izin="{{ Crypt::encrypt($d->kode_izin) }}" style="color: #059669; border-color: #A7F3D0;" title="Persetujuan Izin">
                                                    <i class="ti ti-check"></i>
                                                </button>
                                            @elseif ($d->status == 1)
                                                <form method="POST" name="cancelapproveform" class="d-inline m-0"
                                                    action="{{ route('izinabsen.cancelapprove', Crypt::encrypt($d->kode_izin)) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="delete-confirm" title="Batalkan Persetujuan">
                                                        <i class="ti ti-circle-minus"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan

                                        @can('izinabsen.index')
                                            <button type="button" class="btnEdit btnShow" kode_izin="{{ Crypt::encrypt($d->kode_izin) }}" title="Detail Izin">
                                                <i class="ti ti-file-description"></i>
                                            </button>
                                        @endcan

                                        @can('izinabsen.edit')
                                            @if ($d->status == 0)
                                                <button type="button" class="btnEdit" kode_izin="{{ Crypt::encrypt($d->kode_izin) }}" title="Edit Izin">
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
                                                    <button type="submit" class="delete-confirm" title="Hapus Izin">
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

        <!-- Pagination Footer with Slide Controls -->
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
            <div class="card-body py-2.5 px-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <!-- Counter info -->
                    <div class="text-muted" style="font-size: 12px;">
                        @if ($izinabsen->total() > 0)
                            Menampilkan <span class="fw-bold text-dark font-mono">{{ $izinabsen->firstItem() }}</span> - <span class="fw-bold text-dark font-mono">{{ $izinabsen->lastItem() }}</span> dari <span class="fw-bold text-dark font-mono">{{ $izinabsen->total() }}</span> total izin
                        @else
                            Menampilkan 0 data
                        @endif
                    </div>

                    <!-- Interactive Page Slider (Slide Selector) -->
                    @if ($izinabsen->lastPage() > 1)
                        <div class="page-slider-container">
                            <small class="text-muted fw-semibold" style="font-size: 11.5px;">Slide Halaman:</small>
                            <input type="range" class="page-slider-range" id="pageSlider"
                                min="1" max="{{ $izinabsen->lastPage() }}" value="{{ $izinabsen->currentPage() }}"
                                oninput="document.getElementById('sliderBadge').innerText = this.value"
                                onchange="navigatePage(this.value)">
                            <span class="badge bg-primary font-mono" id="sliderBadge">{{ $izinabsen->currentPage() }}</span>
                        </div>
                    @endif

                    <!-- Standard Pagination Links -->
                    <div class="d-flex align-items-center">
                        {{ $izinabsen->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-modal-form id="modal" size="" show="loadmodal" title="" />

@endsection

@push('myscript')
<script>
    function navigatePage(pageNum) {
        const url = new URL(window.location.href);
        url.searchParams.set('page', pageNum);
        window.location.href = url.toString();
    }

    $(function() {
        function loading() {
            $("#loadmodal").html(
                `<div class="sk-wave sk-primary" style="margin:auto">
                    <div class="sk-wave-rect"></div>
                    <div class="sk-wave-rect"></div>
                    <div class="sk-wave-rect"></div>
                    <div class="sk-wave-rect"></div>
                    <div class="sk-wave-rect"></div>
                </div>`
            );
        }

        $(document).on('click', '#btnCreate', function(e) {
            e.preventDefault();
            $("#modal").modal("show");
            loading();
            $("#modal").find(".modal-title").text("Buat Izin Absen");
            $("#loadmodal").load("/izinabsen/create");
        });

        $(document).on('click', '.btnApprove', function(e) {
            e.preventDefault();
            const kode_izin = $(this).attr("kode_izin");
            $("#modal").modal("show");
            loading();
            $("#modal").find(".modal-title").text("Approve Izin Absen");
            $("#loadmodal").load(`/izinabsen/${kode_izin}/approve`);
        });

        $(document).on('click', '.btnShow', function(e) {
            e.preventDefault();
            const kode_izin = $(this).attr("kode_izin");
            $("#modal").modal("show");
            loading();
            $("#modal").find(".modal-title").text("Detail Izin Absen");
            $("#loadmodal").load(`/izinabsen/${kode_izin}/show`);
        });

        $(document).on('click', '.btnEdit', function(e) {
            e.preventDefault();
            const kode_izin = $(this).attr("kode_izin");
            if (kode_izin) {
                $("#modal").modal("show");
                loading();
                $("#modal").find(".modal-title").text("Edit Izin Absen");
                $("#loadmodal").load(`/izinabsen/${kode_izin}/edit`);
            }
        });
    });
</script>
@endpush
