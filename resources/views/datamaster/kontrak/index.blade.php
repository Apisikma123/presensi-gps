@extends('layouts.app')
@section('titlepage', 'Manajemen Kontrak Kerja')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Data Karyawan</a></li>
    <li class="breadcrumb-item active">Kontrak Kerja</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <h4 class="page-title mb-1 d-flex align-items-center gap-2">
            <span>Manajemen Kontrak Kerja</span>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($kontraks->total()) }} Total
            </span>
        </h4>
        <p class="page-subtitle text-muted mb-0">Pengelolaan masa berlaku kontrak karyawan (PKWT, PKWTT, Probation), dokumen digital, dan notifikasi masa berakhir.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2 flex-wrap">
        @can('kontrak.create')
            <a href="{{ route('kontrak.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
                <i class="ti ti-plus" style="font-size: 16px;"></i>
                <span>Buat Kontrak Baru</span>
            </a>
        @endcan
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.05em;">Total Kontrak</div>
                <div class="fs-4 fw-bold font-mono text-dark">{{ number_format($stats['total']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.05em;">Kontrak Aktif</div>
                <div class="fs-4 fw-bold font-mono text-success">{{ number_format($stats['active']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.05em;">Segera Berakhir</div>
                <div class="fs-4 fw-bold font-mono text-warning">{{ number_format($stats['expiring']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.05em;">Kontrak Habis</div>
                <div class="fs-4 fw-bold font-mono text-danger">{{ number_format($stats['expired']) }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card admin-filter-toolbar mb-3">
    <form action="{{ route('kontrak.index') }}" method="GET" class="m-0 w-100">
        <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap w-100">
            <div class="flex-grow-1" style="min-width: 220px;">
                <x-input-with-icon label="" value="{{ request('search') }}" name="search"
                    icon="ti ti-search" placeholder="Cari NIK, Nama, No. Kontrak..." hideLabel="true" />
            </div>
            <div class="flex-shrink-0" style="min-width: 140px;">
                <select name="status" class="form-select form-select-sm" style="border-radius: 8px;">
                    <option value="">Semua Status</option>
                    <option value="ACTIVE" {{ request('status') === 'ACTIVE' ? 'selected' : '' }}>Aktif</option>
                    <option value="EXPIRING_SOON" {{ request('status') === 'EXPIRING_SOON' ? 'selected' : '' }}>Segera Berakhir</option>
                    <option value="EXPIRED" {{ request('status') === 'EXPIRED' ? 'selected' : '' }}>Habis</option>
                    <option value="RENEWED" {{ request('status') === 'RENEWED' ? 'selected' : '' }}>Diperpanjang</option>
                    <option value="TERMINATED" {{ request('status') === 'TERMINATED' ? 'selected' : '' }}>Diakhiri</option>
                </select>
            </div>
            <div class="flex-shrink-0" style="min-width: 140px;">
                <select name="jenis_kontrak" class="form-select form-select-sm" style="border-radius: 8px;">
                    <option value="">Semua Jenis</option>
                    <option value="PKWT" {{ request('jenis_kontrak') === 'PKWT' ? 'selected' : '' }}>PKWT</option>
                    <option value="PKWTT" {{ request('jenis_kontrak') === 'PKWTT' ? 'selected' : '' }}>PKWTT (Tetap)</option>
                    <option value="PROBATION" {{ request('jenis_kontrak') === 'PROBATION' ? 'selected' : '' }}>Probation</option>
                    <option value="INTERNSHIP" {{ request('jenis_kontrak') === 'INTERNSHIP' ? 'selected' : '' }}>Magang / Intern</option>
                    <option value="FREELANCE" {{ request('jenis_kontrak') === 'FREELANCE' ? 'selected' : '' }}>Freelance / Harian</option>
                </select>
            </div>
            <div class="flex-shrink-0" style="min-width: 150px;">
                <select name="kode_cabang" class="form-select form-select-sm" style="border-radius: 8px;">
                    <option value="">Semua Cabang</option>
                    @foreach($cabangs as $c)
                        <option value="{{ $c->kode_cabang }}" {{ request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>{{ $c->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1 px-3">
                    <i class="ti ti-search" style="font-size: 14px;"></i>
                    <span>Filter</span>
                </button>
                @if(request()->anyFilled(['search', 'status', 'jenis_kontrak', 'kode_cabang']))
                    <a href="{{ route('kontrak.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1 px-3" title="Reset Filter">
                        <i class="ti ti-refresh" style="font-size: 14px;"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="table-karyawan-wrapper mb-3 w-100 max-w-full" style="border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0; background: #FFFFFF !important; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
    <div class="table-responsive w-100 max-w-full" style="overflow-x: auto;">
        <table class="table table-hover align-middle w-100 mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">NO</th>
                    <th>NOMOR KONTRAK</th>
                    <th>KARYAWAN</th>
                    <th>JENIS KONTRAK</th>
                    <th>MASA BERLAKU</th>
                    <th>STATUS</th>
                    <th>DOKUMEN</th>
                    <th class="text-end" style="width: 140px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kontraks as $k)
                    <tr>
                        <td class="text-muted font-mono">{{ $loop->iteration + ($kontraks->currentPage() - 1) * $kontraks->perPage() }}</td>
                        <td>
                            <span class="fw-semibold text-dark">{{ $k->no_kontrak }}</span>
                            @if($k->jabatan)
                                <div class="text-muted small">{{ $k->jabatan }}</div>
                            @endif
                        </td>
                        <td>
                            @if($k->karyawan)
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-label-primary rounded-circle me-2 fw-bold d-flex align-items-center justify-content-center font-mono" style="width: 32px; height: 32px; font-size: 11px;">
                                        {{ strtoupper(substr($k->karyawan->nama_karyawan, 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('karyawan.show', Crypt::encrypt($k->karyawan->nik)) }}" class="fw-semibold text-dark text-decoration-none">
                                            {{ $k->karyawan->nama_karyawan }}
                                        </a>
                                        <div class="text-muted small font-mono">NIK: {{ $k->nik }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted font-mono">NIK: {{ $k->nik }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-label-info fw-semibold">{{ $k->jenis_kontrak }}</span>
                        </td>
                        <td>
                            <div>
                                <span class="text-dark font-mono small">{{ $k->tanggal_mulai ? $k->tanggal_mulai->format('d/m/Y') : '-' }}</span>
                                <span class="text-muted mx-1">s/d</span>
                                <span class="text-dark fw-semibold font-mono small">{{ $k->tanggal_selesai ? $k->tanggal_selesai->format('d/m/Y') : 'Selamanya (PKWTT)' }}</span>
                            </div>
                            @if($k->tanggal_selesai && in_array($k->status, ['ACTIVE', 'EXPIRING_SOON']))
                                @php $rem = $k->days_remaining; @endphp
                                @if($rem !== null)
                                    <small class="{{ $rem <= 7 ? 'text-danger fw-bold' : ($rem <= 30 ? 'text-warning fw-semibold' : 'text-muted') }}">
                                        @if($rem > 0)
                                            <i class="ti ti-clock me-0.5"></i>Sisa {{ $rem }} hari
                                        @elseif($rem === 0)
                                            <i class="ti ti-alert-triangle me-0.5"></i>Berakhir Hari Ini
                                        @else
                                            <i class="ti ti-alert-triangle me-0.5"></i>Lewat {{ abs($rem) }} hari
                                        @endif
                                    </small>
                                @endif
                            @endif
                        </td>
                        <td>
                            {!! $k->status_badge_html !!}
                        </td>
                        <td>
                            @if($k->dokumen)
                                <a href="{{ asset('storage/' . $k->dokumen) }}" target="_blank" class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1">
                                    <i class="ti ti-file-text"></i>Lihat PDF
                                </a>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                @can('kontrak.create')
                                    @if(in_array($k->status, ['ACTIVE', 'EXPIRING_SOON', 'EXPIRED']))
                                        <button type="button" class="btn btn-sm btn-outline-success btn-renew p-1" 
                                            data-id="{{ Crypt::encrypt($k->id) }}"
                                            data-nokontrak="{{ $k->no_kontrak }}"
                                            data-nama="{{ $k->karyawan ? $k->karyawan->nama_karyawan : $k->nik }}"
                                            title="Perpanjang Kontrak" style="border-radius: 6px; width: 28px; height: 28px;">
                                            <i class="ti ti-refresh fs-5"></i>
                                        </button>
                                    @endif
                                @endcan
                                @can('kontrak.edit')
                                    <a href="{{ route('kontrak.edit', Crypt::encrypt($k->id)) }}" class="btn btn-sm btn-outline-primary p-1" title="Edit" style="border-radius: 6px; width: 28px; height: 28px;">
                                        <i class="ti ti-edit fs-5"></i>
                                    </a>
                                @endcan
                                @can('kontrak.delete')
                                    <form action="{{ route('kontrak.delete', Crypt::encrypt($k->id)) }}" method="POST" class="d-inline form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-delete p-1" title="Hapus" style="border-radius: 6px; width: 28px; height: 28px;">
                                            <i class="ti ti-trash fs-5"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="ti ti-file-off fs-1 text-muted mb-2 d-block"></i>
                            <p class="fw-bold mb-1">Belum Ada Data Kontrak</p>
                            <p class="small text-muted mb-0">Gunakan tombol "Buat Kontrak Baru" untuk menambahkan riwayat kontrak karyawan.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination Footer Card -->
@if($kontraks->hasPages())
    <div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
        <div class="card-body py-2.5 px-3">
            {{ $kontraks->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif

<!-- Modal Renew Contract -->
<div class="modal fade" id="modalRenew" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="formRenew" action="" method="POST" enctype="multipart/form-data" class="modal-content" style="border-radius: 12px;">
            @csrf
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-dark mb-0">Perpanjang Kontrak Kerja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <div class="mb-3">
                    <label class="form-label text-muted small fw-semibold">Karyawan</label>
                    <div id="renewNamaKaryawan" class="fw-bold fs-5 text-dark">-</div>
                    <div id="renewOldNoKontrak" class="small text-muted font-mono">Kontrak Sebelumnya: -</div>
                </div>
                <div class="mb-3">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Nomor Kontrak Baru</label>
                    <input type="text" name="new_no_kontrak" class="form-control font-mono" required placeholder="Contoh: KTR/2026/001" style="border-radius: 8px;">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label required fw-semibold" style="font-size: 12px;">Jenis Kontrak Baru</label>
                        <select name="new_jenis_kontrak" class="form-select" required style="border-radius: 8px;">
                            <option value="PKWT">PKWT</option>
                            <option value="PKWTT">PKWTT (Tetap)</option>
                            <option value="PROBATION">Probation</option>
                            <option value="INTERNSHIP">Magang</option>
                            <option value="FREELANCE">Freelance</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold" style="font-size: 12px;">Gaji Pokok Baru (Opsional)</label>
                        <input type="number" name="new_gaji_pokok" class="form-control font-mono" placeholder="Rp" style="border-radius: 8px;">
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label required fw-semibold" style="font-size: 12px;">Tanggal Mulai Baru</label>
                        <input type="date" name="new_tanggal_mulai" class="form-control" required value="{{ date('Y-m-d') }}" style="border-radius: 8px;">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold" style="font-size: 12px;">Tanggal Selesai Baru</label>
                        <input type="date" name="new_tanggal_selesai" class="form-control" placeholder="Kosongkan jika PKWTT" style="border-radius: 8px;">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Lampiran Dokumen Baru (PDF / Scan)</label>
                    <input type="file" name="new_dokumen" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="border-radius: 8px;">
                </div>
            </div>
            <div class="modal-footer border-top py-2.5">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                <button type="submit" class="btn btn-primary" style="border-radius: 8px;">
                    <i class="ti ti-check me-1"></i>Terbitkan Perpanjangan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(document).ready(function() {
        $('.btn-renew').on('click', function() {
            var id = $(this).data('id');
            var noKontrak = $(this).data('nokontrak');
            var nama = $(this).data('nama');

            $('#renewNamaKaryawan').text(nama);
            $('#renewOldNoKontrak').text('Kontrak Sebelumnya: ' + noKontrak);
            $('#formRenew').attr('action', '/kontrak/' + id + '/renew');
            $('#modalRenew').modal('show');
        });

        $('.form-delete').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'Hapus Data Kontrak?',
                text: 'Data kontrak dan berkas terkait akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
