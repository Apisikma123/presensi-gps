@extends('layouts.app')
@section('titlepage', 'Monitoring Presensi')

@section('content')
@section('navigasi')
    <span>Monitoring Presensi</span>
@endsection
<style>
    .presensi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    }
    .presensi-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
        transform: translateY(-1px);
    }
    .metric-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr 1fr 1.2fr 1.2fr 1fr 1fr;
        gap: 8px;
        align-items: stretch;
    }
    @media (max-width: 1200px) {
        .metric-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }
    @media (max-width: 768px) {
        .metric-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    .metric-chip {
        background: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 10px;
        padding: 6px 10px;
        display: flex;
        align-items: center;
        gap: 9px;
        min-height: 48px;
        transition: all 0.15s ease;
    }
    .metric-chip:hover {
        background: #f1f5f9;
        border-color: #e2e8f0;
    }
    .metric-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }
    .metric-content {
        display: flex;
        flex-direction: column;
        min-width: 0;
        overflow: hidden;
    }
    .metric-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #94a3b8;
        line-height: 1;
        margin-bottom: 2px;
    }
    .metric-value {
        font-size: 12.5px;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card shadow-sm border-0" style="border-radius: 16px;">
            <div class="card-header border-0 pb-0">
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-12">
                        <form action="{{ route('presensi.index') }}">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-3 col-md-12 col-sm-12">
                                    <x-input-with-icon label="" value="{{ Request('tanggal') }}" name="tanggal" icon="ti ti-calendar"
                                        datepicker="flatpickr-date" placeholder="Tanggal" />
                                </div>
                                <div class="col-lg-3 col-md-12 col-sm-12">
                                    <div class="form-group mb-0">
                                        <x-select label="" name="kode_cabang" :data="$cabang" key="kode_cabang" textShow="nama_cabang"
                                            selected="{{ Request('kode_cabang') }}" upperCase="true" select2="select2Kodecabangsearch"
                                            placeholder="Cabang" />
                                    </div>
                                </div>
                                <div class="col-lg-5 col-md-12 col-sm-12">
                                    <x-input-with-icon label="" value="{{ Request('nama_karyawan') }}" name="nama_karyawan" icon="ti ti-search"
                                        placeholder="Cari Nama Karyawan..." />
                                </div>
                                <div class="col-lg-1 col-md-12 col-sm-12">
                                    <div class="form-group mb-0">
                                        <button class="btn btn-primary w-100 rounded-3" style="height: 38px;"><i class="ti ti-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="row g-2">
                            @foreach ($karyawan as $d)
                                @php
                                    $tanggal_presensi = !empty(Request('tanggal')) ? Request('tanggal') : date('Y-m-d');
                                    $jam_masuk = $tanggal_presensi . ' ' . $d->jam_masuk;
                                    $terlambat = hitungjamterlambat($d->jam_in, $jam_masuk);
                                    $potongan_tidak_hadir = $d->status == 'a' ? $d->total_jam : 0;
                                    $pulangcepat = hitungpulangcepat(
                                        $tanggal_presensi,
                                        $d->jam_out,
                                        $d->jam_pulang,
                                        $d->istirahat,
                                        $d->jam_awal_istirahat,
                                        $d->jam_akhir_istirahat,
                                        $d->lintashari,
                                    );

                                    // Jika denda sudah ada di tabel presensi (laporan sudah dikunci), gunakan nilai tersebut
                                    if ($d->denda !== null) {
                                        $denda = $d->denda;
                                        if ($terlambat != null) {
                                            $potongan_jam_terlambat =
                                                $terlambat['desimal_terlambat'] >= 1 ? $terlambat['desimal_terlambat'] : 0;
                                        } else {
                                            $potongan_jam_terlambat = 0;
                                        }
                                    } else {
                                        if ($terlambat != null) {
                                            if ($terlambat['desimal_terlambat'] < 1) {
                                                $potongan_jam_terlambat = 0;
                                                $denda = hitungdenda($denda_list, $terlambat['menitterlambat']);
                                            } else {
                                                $potongan_jam_terlambat = $terlambat['desimal_terlambat'];
                                                $denda = 0;
                                            }
                                        } else {
                                            $potongan_jam_terlambat = 0;
                                            $denda = 0;
                                        }
                                    }
                                    
                                    $total_potongan_jam = $pulangcepat + $potongan_jam_terlambat + $potongan_tidak_hadir;

                                    // Avatar helper
                                    $words = explode(' ', $d->nama_karyawan);
                                    $initials = '';
                                    foreach ($words as $w) {
                                        if (isset($w[0])) $initials .= $w[0];
                                    }
                                    $initials = strtoupper(substr($initials, 0, 2));
                                @endphp
                                <div class="col-12">
                                    <div class="card presensi-card mb-2">
                                        <div class="card-body p-3">
                                            {{-- Row 1: Header Info & Status --}}
                                            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                                <div class="d-flex align-items-center flex-wrap gap-2">
                                                    @if (!empty($d->foto) && Storage::disk('public')->exists('/karyawan/' . $d->foto))
                                                        <img src="{{ getfotoKaryawan($d->foto) }}" alt="Avatar"
                                                            class="rounded-circle shadow-sm"
                                                            style="width: 36px; height: 36px; object-fit: cover; border: 1px solid #e2e8f0;">
                                                    @else
                                                        <div class="rounded-circle shadow-sm d-flex align-items-center justify-content-center fw-bold"
                                                            style="width: 36px; height: 36px; background: rgba(50, 116, 94, 0.12); color: #32745e; font-size: 13px;">
                                                            {{ $initials }}
                                                        </div>
                                                    @endif

                                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                                        <span class="fw-bold text-dark" style="font-size: 14px;">{{ $d->nama_karyawan }}</span>
                                                        <span class="badge" style="background: #f1f5f9; color: #475569; font-size: 11px; font-weight: 600;">
                                                            <i class="ti ti-id me-1"></i>{{ $d->nik_show ?? $d->nik }}
                                                        </span>
                                                        <span class="badge" style="background: #eff6ff; color: #1e40af; font-size: 11px; font-weight: 600;">
                                                            <i class="ti ti-building me-1"></i>{{ $d->kode_dept }}
                                                        </span>
                                                        <span class="badge" style="background: #fdf4ff; color: #86198f; font-size: 11px; font-weight: 600;">
                                                            <i class="ti ti-map-pin me-1"></i>{{ $d->kode_cabang }}
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-flex align-items-center gap-2">
                                                    <div>
                                                        @if ($d->status == 'h')
                                                            <span class="badge rounded-pill px-2.5 py-1" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; font-size: 11.5px; font-weight: 600;">
                                                                <i class="ti ti-circle-check me-1"></i>Hadir
                                                            </span>
                                                        @elseif($d->status == 'i')
                                                            <span class="badge rounded-pill px-2.5 py-1" style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; font-size: 11.5px; font-weight: 600;">
                                                                <i class="ti ti-file-description me-1"></i>Izin
                                                            </span>
                                                        @elseif($d->status == 's')
                                                            <span class="badge rounded-pill px-2.5 py-1" style="background: #fffbeb; color: #d97706; border: 1px solid #fde68a; font-size: 11.5px; font-weight: 600;">
                                                                <i class="ti ti-ambulance me-1"></i>Sakit
                                                            </span>
                                                        @elseif($d->status == 'a')
                                                            <span class="badge rounded-pill px-2.5 py-1" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; font-size: 11.5px; font-weight: 600;">
                                                                <i class="ti ti-x me-1"></i>Alpa
                                                            </span>
                                                        @elseif($d->status == 'c')
                                                            <span class="badge rounded-pill px-2.5 py-1" style="background: #f5f3ff; color: #7c3aed; border: 1px solid #ddd6fe; font-size: 11.5px; font-weight: 600;">
                                                                <i class="ti ti-calendar-event me-1"></i>Cuti
                                                            </span>
                                                        @else
                                                            <span class="badge rounded-pill px-2.5 py-1" style="background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; font-size: 11.5px; font-weight: 600;">
                                                                <i class="ti ti-clock me-1"></i>Belum Absen
                                                            </span>
                                                        @endif
                                                    </div>

                                                    {{-- Actions --}}
                                                    <div class="d-flex gap-1">
                                                        @if (isset($d->status_potongan))
                                                            <button class="btn btn-sm btn-icon btn-dark rounded-2" disabled title="Terkunci"><i class="ti ti-lock"></i></button>
                                                        @else
                                                            <a href="#" class="btn btn-sm btn-icon btn-outline-success koreksiPresensi rounded-2" nik="{{ Crypt::encrypt($d->nik) }}"
                                                                tanggal="{{ $tanggal_presensi }}" title="Koreksi"><i class="ti ti-edit"></i></a>

                                                            @if(!empty($d->id))
                                                            <form action="{{ route('presensi.delete', $d->id) }}" method="POST"
                                                                style="display:inline-block;" class="delete-form">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-icon btn-outline-danger delete-confirm rounded-2"
                                                                    title="Hapus"><i class="ti ti-trash"></i></button>
                                                            </form>
                                                            @endif
                                                        @endif
                                    
                                                        <a href="#" class="btn btn-sm btn-icon btn-outline-primary btngetDatamesin rounded-2" pin="{{ $d->pin }}"
                                                            tanggal="{{ !empty(Request('tanggal')) ? Request('tanggal') : date('Y-m-d') }}" title="Log Mesin">
                                                            <i class="ti ti-device-desktop"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="my-2" style="border-top: 1px dashed #e2e8f0;"></div>

                                            {{-- Row 2: Refined Metrics Grid --}}
                                            <div class="metric-grid">
                                                {{-- 1. Jadwal --}}
                                                <div class="metric-chip">
                                                    <div class="metric-icon" style="background: rgba(100, 116, 139, 0.1); color: #475569;">
                                                        <i class="ti ti-clock"></i>
                                                    </div>
                                                    <div class="metric-content">
                                                        <span class="metric-label">Jadwal</span>
                                                        @if ($d->kode_jam_kerja != null)
                                                            <span class="metric-value text-primary" style="font-size: 11.5px;">{{ $d->nama_jam_kerja }}</span>
                                                            <span class="text-muted" style="font-size: 11px; font-weight: 600;">
                                                                {{ date('H:i', strtotime($d->jam_masuk)) }} - {{ date('H:i', strtotime($d->jam_pulang)) }}
                                                            </span>
                                                        @else
                                                            <span class="metric-value text-muted">-</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- 2. Jam Masuk --}}
                                                <div class="metric-chip">
                                                    <div class="metric-icon" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                                                        <i class="ti ti-login"></i>
                                                    </div>
                                                    <div class="metric-content">
                                                        <span class="metric-label">Masuk</span>
                                                        @if ($d->jam_in != null)
                                                            <div class="d-flex align-items-center">
                                                                <a href="#" class="btnShowpresensi_in metric-value text-dark text-decoration-none" id="{{ $d->id }}" status="in">
                                                                    {{ date('H:i', strtotime($d->jam_in)) }}
                                                                </a>
                                                                @if (!empty($d->foto_in))
                                                                    <i class="ti ti-photo text-primary ms-1" style="font-size:12px" title="Ada Foto"></i>
                                                                @endif
                                                                @if ($potongan_jam_terlambat > 0)
                                                                    <span class="text-danger ms-1 fw-bold" style="font-size:10.5px">(-{{ $potongan_jam_terlambat }})</span>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <span class="metric-value text-muted">-</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- 3. Jam Pulang --}}
                                                <div class="metric-chip">
                                                    <div class="metric-icon" style="background: rgba(239, 68, 68, 0.1); color: #dc2626;">
                                                        <i class="ti ti-logout"></i>
                                                    </div>
                                                    <div class="metric-content">
                                                        <span class="metric-label">Pulang</span>
                                                        @if ($d->jam_out != null)
                                                            <div class="d-flex align-items-center">
                                                                <a href="#" class="btnShowpresensi_out metric-value text-dark text-decoration-none" id="{{ $d->id }}" status="out">
                                                                    {{ date('H:i', strtotime($d->jam_out)) }}
                                                                </a>
                                                                @if (!empty($d->foto_out))
                                                                    <i class="ti ti-photo text-primary ms-1" style="font-size:12px" title="Ada Foto"></i>
                                                                @endif
                                                                @if ($pulangcepat > 0)
                                                                    <span class="text-danger ms-1 fw-bold" style="font-size:10.5px">(-{{ $pulangcepat }})</span>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <span class="metric-value text-muted">-</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- 4. Istirahat --}}
                                                <div class="metric-chip">
                                                    <div class="metric-icon" style="background: rgba(14, 165, 233, 0.1); color: #0284c7;">
                                                        <i class="ti ti-coffee"></i>
                                                    </div>
                                                    <div class="metric-content">
                                                        <span class="metric-label">Istirahat</span>
                                                        @if ($d->istirahat_out != null && $d->istirahat_in != null)
                                                            <span class="metric-value text-dark" style="font-size: 11.5px;">
                                                                {{ date('H:i', strtotime($d->istirahat_out)) }} - {{ date('H:i', strtotime($d->istirahat_in)) }}
                                                            </span>
                                                        @elseif($d->istirahat_out != null)
                                                            <span class="metric-value text-warning" style="font-size: 11.5px;">
                                                                {{ date('H:i', strtotime($d->istirahat_out)) }} - ...
                                                            </span>
                                                        @else
                                                            <span class="metric-value text-muted">-</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- 5. Terlambat --}}
                                                <div class="metric-chip">
                                                    <div class="metric-icon" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                                                        <i class="ti ti-clock-exclamation"></i>
                                                    </div>
                                                    <div class="metric-content">
                                                        <span class="metric-label">Terlambat</span>
                                                        @if($terlambat != null)
                                                            <span class="metric-value text-danger" style="font-size: 11.5px;">{!! $terlambat['show'] !!}</span>
                                                        @else
                                                            <span class="metric-value text-success" style="font-size: 11.5px;">
                                                                <i class="ti ti-check"></i> Tepat Waktu
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- 6. Denda --}}
                                                <div class="metric-chip">
                                                    <div class="metric-icon" style="background: rgba(225, 29, 72, 0.1); color: #e11d48;">
                                                        <i class="ti ti-coin"></i>
                                                    </div>
                                                    <div class="metric-content">
                                                        <span class="metric-label">Denda</span>
                                                        <span class="metric-value {{ empty($denda) ? 'text-dark' : 'text-danger' }}">
                                                            {{ empty($denda) ? 'Rp 0' : 'Rp ' . formatAngka($denda) }}
                                                        </span>
                                                    </div>
                                                </div>

                                                {{-- 7. Potongan Jam --}}
                                                <div class="metric-chip">
                                                    <div class="metric-icon" style="background: rgba(100, 116, 139, 0.1); color: #334155;">
                                                        <i class="ti ti-cut"></i>
                                                    </div>
                                                    <div class="metric-content">
                                                        <span class="metric-label">Potongan</span>
                                                        @if ($total_potongan_jam > 0)
                                                            <span class="badge bg-danger rounded-pill px-2 py-0.5" style="font-size: 10.5px;">
                                                                {{ formatAngkaDesimal($total_potongan_jam) }} Jam
                                                            </span>
                                                        @else
                                                            <span class="metric-value text-success" style="font-size: 11.5px;">0 Jam</span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            {{ $karyawan->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<x-modal-form id="modal" size="modal-xl" show="loadmodal" title="" />
@endsection
@push('myscript')
<script>
    $(function() {
        $(document).on('click', '.koreksiPresensi', function() {
            let nik = $(this).attr('nik');
            let tanggal = $(this).attr('tanggal');
            $.ajax({
                type: 'POST',
                url: "{{ route('presensi.edit') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    nik: nik,
                    tanggal: tanggal
                },
                cache: false,
                success: function(res) {
                    $('#modal').modal('show');
                    $('#modal').find('.modal-title').text('Koreksi Presensi');
                    $('#loadmodal').html(res);
                }
            });
        });

        $(".btnShowpresensi_in, .btnShowpresensi_out").click(function(e) {
            e.preventDefault();
            const id = $(this).attr("id");
            const status = $(this).attr("status");
            $("#loadmodal").html(`<div class="sk-wave sk-primary" style="margin:auto">
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
            </div>`);
            $("#modal").modal("show");
            $(".modal-title").text("Data Presensi");
            $("#loadmodal").load(`/presensi/${id}/${status}/show`);
        });

        $(".btngetDatamesin").click(function(e) {
            e.preventDefault();
            var pin = $(this).attr("pin");
            var tanggal = $(this).attr("tanggal");
            $("#loadmodal").html(`<div class="sk-wave sk-primary" style="margin:auto">
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            </div>`);
            $("#modal").modal("show");
            $(".modal-title").text("Get Data Mesin");
            $.ajax({
                type: 'POST',
                url: '/presensi/getdatamesin',
                data: {
                    _token: "{{ csrf_token() }}",
                    pin: pin,
                    tanggal: tanggal,
                },
                cache: false,
                success: function(respond) {
                    console.log(respond);
                    $("#loadmodal").html(respond);
                }
            });
        });

        $(".delete-confirm").click(function(e) {
            var form = $(this).closest('form');
            e.preventDefault();
            Swal.fire({
                title: 'Apakah Anda Yakin Data Ini Akan Dihapus ?',
                text: "Jika Dihapus Maka Data Akan Hilang ",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#32745e',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus Saja!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        });
    });
</script>
@endpush
