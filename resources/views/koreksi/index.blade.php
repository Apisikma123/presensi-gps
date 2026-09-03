@extends('layouts.app')
@section('titlepage', 'Koreksi Absen')

@section('content')
@section('navigasi')
    <span>Koreksi Absen</span>
@endsection
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="nav-align-top nav-tabs-shadow mb-4">
            @include('layouts.navigation.nav_pengajuan_absen')
            <div class="tab-content">
                <div class="tab-pane fade active show" id="navs-justified-home" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @can('koreksi.create')
                                <a href="#" class="btn btn-primary" id="btnCreate"><i class="fa fa-plus me-2"></i>
                                    Tambah Data</a>
                            @endcan
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <form action="{{ route('koreksi.index') }}">
                                <div class="row g-2">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <x-input-with-icon label="Dari" value="{{ Request('dari') }}" name="dari" icon="ti ti-calendar"
                                            datepicker="flatpickr-date" hideLabel />
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <x-input-with-icon label="Sampai" value="{{ Request('sampai') }}" name="sampai" icon="ti ti-calendar"
                                            datepicker="flatpickr-date" hideLabel />
                                    </div>
                                </div>
                                <div class="row g-2">
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
                                                <option value="1" {{ Request('status') == '1' ? 'selected' : '' }}>Disetujui</option>
                                                <option value="2" {{ Request('status') == '2' ? 'selected' : '' }}>Ditolak</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <x-select label="Cabang" name="kode_cabang" :data="$cabang" key="kode_cabang" textShow="nama_cabang"
                                            selected="{{ Request('kode_cabang') }}" upperCase="true" hideLabel />
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <x-select label="Departemen" name="kode_dept" :data="$departemen" key="kode_dept" textShow="nama_dept"
                                            selected="{{ Request('kode_dept') }}" upperCase="true" hideLabel />
                                    </div>
                                    <div class="col-lg-1 col-md-12 col-sm-12">
                                        <button class="btn btn-primary w-100"><i class="ti ti-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="row g-2">
                                @forelse ($koreksi as $d)
                                    @php
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

                                                    <!-- Date & Times -->
                                                    <div class="col-lg-3 col-md-6 text-center d-flex flex-column align-items-center justify-content-center">
                                                        <div class="fw-bold text-dark" style="font-size: 12.5px;">
                                                            <i class="ti ti-calendar me-1 text-primary"></i>
                                                            {{ date('d M Y', strtotime($d->tanggal)) }}
                                                        </div>
                                                        <div class="text-muted mt-1" style="font-size: 11px;">
                                                            <span class="badge" style="background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; font-size: 10px;">{{ $d->kode_koreksi }}</span>
                                                            <span class="text-slate-400 mx-1">•</span>
                                                            <span class="fw-semibold text-dark">{{ $d->nama_jam_kerja }}</span>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2 mt-1">
                                                            <span class="badge rounded-pill px-2 py-0.5" style="background: #ecfdf5; color: #059669; font-size: 10.5px;">
                                                                In: {{ $d->jam_in ?? '-' }}
                                                            </span>
                                                            <span class="badge rounded-pill px-2 py-0.5" style="background: #fef2f2; color: #dc2626; font-size: 10.5px;">
                                                                Out: {{ $d->jam_out ?? '-' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Status -->
                                                    <div class="col-lg-2 col-md-6 text-center">
                                                        @if ($d->status == 0)
                                                            @php
                                                                $nextLayer = $d->getNextApprovalLayer();
                                                            @endphp
                                                            <span class="badge rounded-pill px-2.5 py-1" style="background: #fffbeb; color: #d97706; border: 1px solid #fde68a; font-size: 11px; font-weight: 600;">
                                                                <i class="ti ti-hourglass-empty me-1"></i> Pending
                                                            </span>
                                                            @if ($nextLayer)
                                                                <div class="text-muted mt-1" style="font-size: 10px; line-height: 1.2;">
                                                                    Menunggu: <span class="fw-semibold">{{ $nextLayer->role_name }}</span>
                                                                </div>
                                                            @endif
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
                                                            @can('koreksi.approve')
                                                                @if ($d->status == 0)
                                                                    <a href="#" class="btn btn-sm btn-outline-primary py-1 px-2 btnApprove" 
                                                                        kode_koreksi="{{ Crypt::encrypt($d->kode_koreksi) }}" title="Approve">
                                                                        <i class="ti ti-external-link"></i>
                                                                    </a>
                                                                @endif
                                                            @endcan
                                                            
                                                            @can('koreksi.index')
                                                                <a href="#" class="btn btn-sm btn-outline-info btnShow py-1 px-2" kode_koreksi="{{ Crypt::encrypt($d->kode_koreksi) }}" title="Detail">
                                                                    <i class="ti ti-file-description"></i>
                                                                </a>
                                                            @endcan
                                                            
                                                            @can('koreksi.delete')
                                                                <form method="POST" action="{{ route('koreksi.delete', Crypt::encrypt($d->kode_koreksi)) }}" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm py-1 px-2 rounded-0 rounded-end" title="Hapus">
                                                                        <i class="ti ti-trash"></i>
                                                                    </button>
                                                                </form>
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
                                                <h6 class="fw-bold mb-1">Tidak Ada Data Koreksi Absen</h6>
                                                <small class="text-muted">Gunakan filter pencarian di atas untuk menemukan data.</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        {{ $koreksi->links() }}
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

        $("#btnCreate").click(function() {
            $("#modal").modal("show");
            loading();
            $("#modal").find(".modal-title").text("Buat Pengajuan Koreksi");
            $("#loadmodal").load("/koreksi/create");
        });

        $(".btnShow").click(function() {
            const kode_koreksi = $(this).attr("kode_koreksi");
            $("#modal").modal("show");
            loading();
            $("#modal").find(".modal-title").text("Detail Koreksi Presensi");
            $("#loadmodal").load(`/koreksi/${kode_koreksi}/show`);
        });

        $(".btnApprove").click(function() {
            const kode_koreksi = $(this).attr("kode_koreksi");
            $("#modal").modal("show");
            loading();
            $("#modal").find(".modal-title").text("Approve Koreksi Presensi");
            $("#loadmodal").load(`/koreksi/${kode_koreksi}/approve`);
        });
    });
</script>
@endpush
