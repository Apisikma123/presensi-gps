@extends('layouts.app')
@section('titlepage', 'Lembur')

@section('content')
@section('navigasi')
    <span>Lembur</span>
@endsection

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                @can('lembur.create')
                    <a href="#" class="btn btn-primary" id="btnCreate"><i class="fa fa-plus me-2"></i> Tambah Data</a>
                @endcan
            </div>
            
            <div class="card-body">
                <form action="{{ route('lembur.index') }}" class="mb-3">
                    <div class="row g-2 mb-1">
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <x-input-with-icon label="Dari" value="{{ Request('dari') }}" name="dari" icon="ti ti-calendar"
                                datepicker="flatpickr-date" hideLabel />
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <x-input-with-icon label="Sampai" value="{{ Request('sampai') }}" name="sampai" icon="ti ti-calendar"
                                datepicker="flatpickr-date" hideLabel />
                        </div>
                    </div>
                    
                    <div class="row g-2 mb-1">
                        <div class="col-12">
                            <x-input-with-icon label="Nama Karyawan" value="{{ Request('nama_karyawan') }}" name="nama_karyawan"
                                icon="ti ti-search" hideLabel />
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            <div class="form-group">
                                <select name="status" id="status" class="form-select">
                                    <option value="">Status</option>
                                    <option value="0" {{ Request('status') === '0' ? 'selected' : '' }}>Pending</option>
                                    <option value="1" {{ Request('status') === '1' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="2" {{ Request('status') === '2' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12">
                             <select name="kode_cabang" id="kode_cabang" class="form-select">
                                <option value="">Semua Cabang</option>
                                @foreach ($cabang as $d)
                                    <option value="{{ $d->kode_cabang }}" {{ Request('kode_cabang') == $d->kode_cabang ? 'selected' : '' }}>
                                        {{ textUpperCase($d->nama_cabang) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <select name="kode_dept" id="kode_dept" class="form-select">
                                <option value="">Semua Departemen</option>
                                @foreach ($departemen as $d)
                                    <option value="{{ $d->kode_dept }}" {{ Request('kode_dept') == $d->kode_dept ? 'selected' : '' }}>
                                        {{ textUpperCase($d->nama_dept) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-1 col-md-12 col-sm-12">
                            <button class="btn btn-primary w-100"><i class="ti ti-search"></i></button>
                        </div>
                    </div>
                </form>

        <div class="row mt-2">
            <div class="col-12">
                <div class="row g-2">
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
                                $real_duration = ROUND(hitungJam($d->lembur_in, $d->lembur_out), 2) . "j";
                            }

                            $words = explode(' ', $d->nama_karyawan);
                            $initials = '';
                            foreach ($words as $w) {
                                if (isset($w[0])) $initials .= $w[0];
                            }
                            $initials = strtoupper(substr($initials, 0, 2));
                        @endphp
                        <div class="col-12">
                            <div class="card mb-2 shadow-sm border" style="border-radius: 12px; border-color: #e2e8f0; transition: all 0.2s ease;">
                                <div class="card-body p-3">
                                    <div class="row align-items-center g-2">
                                        <!-- Avatar & Identity -->
                                        <div class="col-lg-5 col-md-12 d-flex align-items-center gap-3">
                                            @php
                                                $path = Storage::url('karyawan/'.$d->foto);
                                            @endphp
                                            @if (!empty($d->foto) && Storage::disk('public')->exists('/karyawan/' . $d->foto))
                                                <img src="{{ url($path) }}" alt="Avatar" class="rounded-circle shadow-sm flex-shrink-0" style="width: 44px; height: 44px; object-fit: cover; border: 2px solid #e2e8f0;">
                                            @else
                                                <div class="rounded-circle shadow-sm flex-shrink-0 d-flex align-items-center justify-content-center fw-bold"
                                                    style="width: 44px; height: 44px; background: rgba(50, 116, 94, 0.12); color: #32745e; font-size: 14px; border: 2px solid rgba(50, 116, 94, 0.2);">
                                                    {{ $initials }}
                                                </div>
                                            @endif

                                            <div class="overflow-hidden">
                                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                                    <span class="fw-bold text-dark" style="font-size: 14px;">{{ $d->nama_karyawan }}</span>
                                                    <span class="badge" style="background: #f1f5f9; color: #475569; font-size: 11px; font-weight: 600;">
                                                        <i class="ti ti-id me-1"></i>{{ $d->nik_show ?? $d->nik }}
                                                    </span>
                                                </div>
                                                <div class="mt-1 d-flex flex-wrap gap-1">
                                                    <span class="badge" style="background: #eff6ff; color: #1d4ed8; font-size: 10.5px; font-weight: 600;">{{ $d->nama_jabatan }}</span>
                                                    <span class="badge" style="background: #f0fdf4; color: #15803d; font-size: 10.5px; font-weight: 600;">{{ $d->nama_dept }}</span>
                                                    <span class="badge" style="background: #fdf4ff; color: #86198f; font-size: 10.5px; font-weight: 600;">{{ $d->nama_cabang }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Date & Duration -->
                                        <div class="col-lg-3 col-md-6 text-center d-flex flex-column align-items-center justify-content-center">
                                            <div class="fw-bold text-dark" style="font-size: 12.5px;">
                                                <i class="ti ti-calendar me-1 text-primary"></i>
                                                {{ DateToIndo($d->tanggal) }}
                                            </div>
                                            <div class="text-muted mt-1" style="font-size: 11px;">
                                                <span class="text-success fw-bold">{{ date('H:i', strtotime($d->lembur_mulai)) }}</span> - 
                                                <span class="text-danger fw-bold">{{ date('H:i', strtotime($d->lembur_selesai)) }}</span>
                                                <span class="text-slate-400 mx-1">•</span>
                                                <span class="fw-semibold text-dark">{{ $duration }}</span>
                                            </div>
                                            @if($d->lembur_in && $d->lembur_out)
                                                <div class="badge mt-1" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; font-size: 10.5px; font-weight: 600;">
                                                    Aktual: {{ $real_duration }}
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Status -->
                                        <div class="col-lg-2 col-md-6 text-center">
                                            @if ($d->status == 0)
                                                <span class="badge rounded-pill px-2.5 py-1" style="background: #fffbeb; color: #d97706; border: 1px solid #fde68a; font-size: 11px; font-weight: 600;">
                                                    <i class="ti ti-hourglass-empty me-1"></i> Pending
                                                </span>
                                            @elseif ($d->status == 1)
                                                <span class="badge rounded-pill px-2.5 py-1" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; font-size: 11px; font-weight: 600;">
                                                    <i class="ti ti-check me-1"></i> Disetujui
                                                </span>
                                            @elseif ($d->status == 2)
                                                <span class="badge rounded-pill px-2.5 py-1" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; font-size: 11px; font-weight: 600;">
                                                    <i class="ti ti-x me-1"></i> Ditolak
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <!-- Actions -->
                                        <div class="col-lg-2 col-md-12 text-lg-end text-center">
                                            <div class="btn-group shadow-sm" role="group">
                                                @can('lembur.approve')
                                                    @if ($d->status == 0)
                                                        <a href="#" class="btn btn-sm btn-outline-primary btnApprove py-1 px-2"
                                                            id="{{ Crypt::encrypt($d->id) }}" title="Approve">
                                                            <i class="ti ti-external-link"></i>
                                                        </a>
                                                    @elseif($d->status == 1 || $d->status == 2)
                                                        <form method="POST" action="{{ route('lembur.cancelapprove', Crypt::encrypt($d->id)) }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-warning cancel-confirm rounded-0 py-1 px-2" title="Batalkan Approval">
                                                                <i class="ti ti-arrow-back-up"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endcan
                                                
                                                @can('lembur.edit')
                                                    <a href="#" class="btn btn-sm btn-outline-success btnEdit py-1 px-2" id="{{ Crypt::encrypt($d->id) }}" title="Edit">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                @endcan
                                                
                                                @can('lembur.index')
                                                    <a href="#" class="btn btn-sm btn-outline-info btnShow py-1 px-2" id="{{ Crypt::encrypt($d->id) }}" title="Detail">
                                                        <i class="ti ti-file-description"></i>
                                                    </a>
                                                @endcan
                                                
                                                @can('lembur.delete')
                                                    @if ($d->status == 0)
                                                        <form method="POST" action="{{ route('lembur.delete', Crypt::encrypt($d->id)) }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm rounded-0 rounded-end py-1 px-2" title="Hapus">
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="card border-0 shadow-sm text-center py-5" style="border-radius: 12px;">
                                <div class="d-flex flex-column align-items-center opacity-75">
                                    <i class="ti ti-file-off fs-1 text-muted mb-2"></i>
                                    <h6 class="fw-bold mb-1">Tidak Ada Data Lembur</h6>
                                    <small class="text-muted">Gunakan filter pencarian di atas untuk menemukan data.</small>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
                <div class="d-flex justify-content-end mt-3">
                    {{ $lembur->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<x-modal-form id="modal" show="loadmodal" />

@endsection

@push('myscript')
<script>
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

        // Initialize Flatpickr
        flatpickr(".flatpickr-date", {
            dateFormat: "Y-m-d",
            allowInput: true
        });

        $("#btnCreate").click(function() {
            $("#modal").modal("show");
            $(".modal-title").text("Tambah Data Lembur");
            loading();
            $("#loadmodal").load("{{ route('lembur.create') }}");
        });

        $(".btnEdit").click(function() {
            const id = $(this).attr("id");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Data Lembur");
            loading();
            $("#loadmodal").load(`/lembur/${id}/edit`);
        });

        $(".btnApprove").click(function(e) {
            e.preventDefault();
            let id = $(this).attr("id");
            $("#modal").modal("show");
            $(".modal-title").text("Persetujuan Lembur");
            loading();
            $("#loadmodal").load(`/lembur/${id}/approve`);
        });
        
         $(".btnShow").click(function(e) {
            e.preventDefault();
            let id = $(this).attr("id");
            $("#modal").modal("show");
            loading();
            $("#modal").find(".modal-title").text("Detail Lembur");
            $("#loadmodal").load(`/lembur/${id}/show`);
        });

        // Delete Confirm
        $(".delete-confirm").click(function(e) {
            var form = $(this).closest("form");
            e.preventDefault();
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Data ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        });

        // Cancel Confirm
        $(".cancel-confirm").click(function(e) {
            var form = $(this).closest("form");
            e.preventDefault();
            Swal.fire({
                title: 'Batalkan Persetujuan?',
                text: "Status akan kembali menjadi Pending!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Batalkan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        });
    });
</script>
@endpush
