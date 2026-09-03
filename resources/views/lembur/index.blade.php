@extends('layouts.app')
@section('titlepage', 'Lembur Karyawan')

@section('content')
@section('navigasi')
    <span>Pengajuan Lembur</span>
@endsection

<!-- Top Header Toolbar -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <span>Pengajuan Lembur Karyawan</span>
            <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                {{ $lembur->total() }} Total
            </span>
        </h5>
        <small class="text-muted" style="font-size: 12px;">Daftar permohonan surat perintah lembur (SPL), jam lembur, dan verifikasi kehadiran lembur.</small>
    </div>
    <div class="d-flex align-items-center gap-2">
        @can('lembur.create')
            <a href="#" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5" id="btnCreate" style="height: 36px; border-radius: 8px;">
                <i class="ti ti-plus"></i>
                <span>Tambah Lembur</span>
            </a>
        @endcan
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 10px; background: #FFFFFF; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);">
    <div class="card-body p-2.5">
        <form action="{{ route('lembur.index') }}" method="GET">
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
                <div class="col-lg-1 col-md-4 col-12 d-flex align-items-center gap-1">
                    <button type="submit" class="btn w-100 text-white fw-semibold d-flex align-items-center justify-content-center gap-1 shadow-sm"
                        style="background-color: #1E4D3E; border: 1px solid #11382C; border-radius: 8px; height: 36px; font-size: 12.5px;" title="Cari">
                        <i class="ti ti-search"></i>
                    </button>
                    @if (Request('dari') || Request('sampai') || Request('nama_karyawan') || Request('status') !== null || Request('kode_cabang'))
                        <a href="{{ route('lembur.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center p-0"
                            style="height: 36px; width: 36px; min-width: 36px; border-radius: 8px;" title="Reset Filter">
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
                    <th>TANGGAL & JADWAL LEMBUR</th>
                    <th>REALISASI PRESENSI</th>
                    <th class="text-center">STATUS APPROVAL</th>
                    <th class="text-end" style="width: 120px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($lembur as $d)
                    @php
                        $start = strtotime($d->lembur_mulai);
                        $end = strtotime($d->lembur_selesai);
                        $diff = $end - $start;
                        $hours = floor($diff / 3600);
                        $minutes = floor(($diff % 3600) / 60);
                        $duration = $hours . "j " . ($minutes > 0 ? $minutes . "m" : "");
                        
                        $real_duration = "-";
                        if($d->lembur_in && $d->lembur_out) {
                            $real_duration = ROUND(hitungJam($d->lembur_in, $d->lembur_out), 2) . " Jam";
                        }

                        $words = explode(' ', $d->nama_karyawan);
                        $initials = '';
                        foreach ($words as $w) {
                            if (isset($w[0])) $initials .= $w[0];
                        }
                        $initials = strtoupper(substr($initials, 0, 2));
                    @endphp
                    <tr>
                        <td class="text-center font-mono text-muted" style="font-size: 12px;">
                            {{ $loop->iteration + ($lembur->currentPage() - 1) * $lembur->perPage() }}
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
                                {{ date('d M Y', strtotime($d->tanggal)) }}
                            </span>
                            <div class="d-flex align-items-center gap-1 mt-0.5">
                                <span class="font-mono text-muted" style="font-size: 11px;">
                                    {{ date('H:i', strtotime($d->lembur_mulai)) }} - {{ date('H:i', strtotime($d->lembur_selesai)) }}
                                </span>
                                <span class="badge bg-label-primary font-mono" style="font-size: 10px;">{{ $duration }}</span>
                            </div>
                        </td>
                        <td>
                            @if ($d->lembur_in && $d->lembur_out)
                                <span class="font-mono text-success fw-semibold d-block" style="font-size: 11.5px;">
                                    {{ date('H:i', strtotime($d->lembur_in)) }} - {{ date('H:i', strtotime($d->lembur_out)) }}
                                </span>
                                <span class="badge bg-label-success font-mono" style="font-size: 10px;">{{ $real_duration }}</span>
                            @else
                                <span class="badge bg-light text-muted font-mono" style="border: 1px solid #E2E8F0; font-size: 10.5px;">Belum Presensi</span>
                            @endif
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
                                @can('lembur.approve')
                                    <button type="button" class="btnEdit btnApprove" id="{{ Crypt::encrypt($d->id) }}" style="color: #059669; border-color: #A7F3D0;" title="Persetujuan Lembur">
                                        <i class="ti ti-check"></i>
                                    </button>
                                @endcan

                                @can('lembur.index')
                                    <button type="button" class="btnEdit btnShow" id="{{ Crypt::encrypt($d->id) }}" title="Detail Lembur">
                                        <i class="ti ti-file-description"></i>
                                    </button>
                                @endcan

                                @can('lembur.edit')
                                    @if ($d->status == 0)
                                        <button type="button" class="btnEdit" id="{{ Crypt::encrypt($d->id) }}" title="Edit Lembur">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                    @endif
                                @endcan

                                @can('lembur.delete')
                                    @if ($d->status == 0)
                                        <form method="POST" name="deleteform" class="deleteform d-inline m-0"
                                            action="{{ route('lembur.delete', Crypt::encrypt($d->id)) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete-confirm" title="Hapus Lembur">
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
                            <i class="ti ti-clock-off text-muted fs-1 d-block mb-2" style="opacity: 0.4;"></i>
                            <h6 class="mb-1 text-dark fw-semibold">Tidak Ada Data Pengajuan Lembur</h6>
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
                @if ($lembur->total() > 0)
                    Menampilkan <span class="fw-bold text-dark font-mono">{{ $lembur->firstItem() }}</span> - <span class="fw-bold text-dark font-mono">{{ $lembur->lastItem() }}</span> dari <span class="fw-bold text-dark font-mono">{{ $lembur->total() }}</span> total lembur
                @else
                    Menampilkan 0 data
                @endif
            </div>

            <!-- Interactive Page Slider (Slide Selector) -->
            @if ($lembur->lastPage() > 1)
                <div class="page-slider-container">
                    <small class="text-muted fw-semibold" style="font-size: 11.5px;">Slide Halaman:</small>
                    <input type="range" class="page-slider-range" id="pageSlider"
                        min="1" max="{{ $lembur->lastPage() }}" value="{{ $lembur->currentPage() }}"
                        oninput="document.getElementById('sliderBadge').innerText = this.value"
                        onchange="navigatePage(this.value)">
                    <span class="badge bg-primary font-mono" id="sliderBadge">{{ $lembur->currentPage() }}</span>
                </div>
            @endif

            <!-- Standard Pagination Links -->
            <div class="d-flex align-items-center">
                {{ $lembur->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<x-modal-form id="modal" show="loadmodal" />

@endsection

@push('myscript')
<script>
    function navigatePage(pageNum) {
        const url = new URL(window.location.href);
        url.searchParams.set('page', pageNum);
        window.location.href = url.toString();
    }

    $(function() {
        const loading = () => {
             $("#loadmodal").html(`<div class="sk-wave sk-primary" style="margin:auto">
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
            </div>`);
        };

        flatpickr(".flatpickr-date", {
            dateFormat: "Y-m-d",
            allowInput: true
        });

        $("#btnCreate").click(function(e) {
            e.preventDefault();
            $("#modal").modal("show");
            $(".modal-title").text("Tambah Data Lembur");
            loading();
            $("#loadmodal").load("{{ route('lembur.create') }}");
        });

        $(document).on('click', '.btnEdit', function(e) {
            e.preventDefault();
            const id = $(this).attr("id");
            if (id) {
                $("#modal").modal("show");
                $(".modal-title").text("Edit Data Lembur");
                loading();
                $("#loadmodal").load(`/lembur/${id}/edit`);
            }
        });

        $(document).on('click', '.btnApprove', function(e) {
            e.preventDefault();
            let id = $(this).attr("id");
            $("#modal").modal("show");
            $(".modal-title").text("Persetujuan Lembur");
            loading();
            $("#loadmodal").load(`/lembur/${id}/approve`);
        });
        
        $(document).on('click', '.btnShow', function(e) {
            e.preventDefault();
            let id = $(this).attr("id");
            $("#modal").modal("show");
            loading();
            $("#modal").find(".modal-title").text("Detail Lembur");
            $("#loadmodal").load(`/lembur/${id}/show`);
        });
    });
</script>
@endpush
