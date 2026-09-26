@extends('layouts.app')
@section('titlepage', 'Payroll ' . $period->formatted_period)

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Keuangan & Payroll</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Periode Penggajian</a></li>
    <li class="breadcrumb-item active">{{ $period->formatted_period }}</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Payroll {{ $period->formatted_period }}</h4>
            {!! $period->status_badge_html !!}
        </div>
        <p class="page-subtitle text-muted mb-0">
            Cutoff: <span class="fw-semibold text-dark font-mono">{{ $period->cutoff_start->format('d M Y') }} &ndash; {{ $period->cutoff_end->format('d M Y') }}</span>
            &bull; Bayar: <span class="fw-semibold text-dark">{{ $period->payment_date->translatedFormat('d F Y') }}</span>
            @if ($period->finalized_at)
                &bull; Final: <span class="fw-semibold text-dark">{{ $period->finalizer->name ?? 'Admin' }} ({{ $period->finalized_at->format('d/m/Y H:i') }})</span>
            @endif
        </p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i>
            <span>Kembali</span>
        </a>

        @if ($period->status !== 'FINALIZED')
            @can('payroll.calculate')
                <form action="{{ route('payroll.calculate', $period->id) }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5">
                        <i class="ti ti-calculator"></i>
                        <span>{{ $period->details->count() > 0 ? 'Hitung Ulang' : 'Jalankan Kalkulasi' }}</span>
                    </button>
                </form>
            @endcan

            @if ($period->details->count() > 0)
                @can('payroll.finalize')
                    <form action="{{ route('payroll.finalize', $period->id) }}" method="POST" class="m-0 form-confirm"
                        data-title="Finalisasi & Kunci Payroll"
                        data-message="Kunci & finalisasi payroll periode {{ $period->formatted_period }}? Setelah dikunci, data tidak dapat diubah tanpa izin buka revisi.">
                        @csrf
                        <button type="submit" class="btn btn-success d-inline-flex align-items-center gap-1.5">
                            <i class="ti ti-lock"></i>
                            <span>Finalisasi & Kunci</span>
                        </button>
                    </form>
                @endcan
            @endif
        @else
            @can('payroll.reopen')
                <form action="{{ route('payroll.reopen', $period->id) }}" method="POST" class="m-0 form-confirm"
                    data-title="Buka Revisi Payroll"
                    data-message="Buka kembali periode {{ $period->formatted_period }} untuk perbaikan/revisi?">
                    @csrf
                    <button type="submit" class="btn btn-outline-warning d-inline-flex align-items-center gap-1.5">
                        <i class="ti ti-lock-open"></i>
                        <span>Buka Revisi</span>
                    </button>
                </form>
            @endcan
        @endif
    </div>
</div>

{{-- Alerts --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="ti ti-circle-check fs-5 me-2"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="ti ti-alert-triangle fs-5 me-2"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Metric Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card h-100 p-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-uppercase text-muted" style="font-size: 11px; font-weight: 600; letter-spacing: 0.05em;">Gaji Pokok & Tunj.</span>
                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary);">
                    <i class="ti ti-wallet"></i>
                </div>
            </div>
            <div class="fs-4 fw-bold font-mono text-dark">
                Rp {{ number_format($period->details->sum('basic_salary') + $period->details->sum('total_allowances'), 0, ',', '.') }}
            </div>
            <span class="text-muted mt-1" style="font-size: 11.5px;">Komponen upah terstruktur</span>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card h-100 p-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-uppercase text-muted" style="font-size: 11px; font-weight: 600; letter-spacing: 0.05em;">Upah Lembur</span>
                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; background: rgba(2, 132, 199, 0.08); color: #0284C7;">
                    <i class="ti ti-clock-dollar"></i>
                </div>
            </div>
            <div class="fs-4 fw-bold font-mono text-dark">
                Rp {{ number_format($period->details->sum('total_overtime_pay'), 0, ',', '.') }}
            </div>
            <span class="text-muted mt-1" style="font-size: 11.5px;">Realisasi PP 35/2021</span>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card h-100 p-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-uppercase text-muted" style="font-size: 11px; font-weight: 600; letter-spacing: 0.05em;">Total Potongan</span>
                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; background: rgba(186, 26, 26, 0.08); color: #BA1A1A;">
                    <i class="ti ti-arrow-down-right"></i>
                </div>
            </div>
            <div class="fs-4 fw-bold font-mono text-danger">
                Rp {{ number_format($period->details->sum('total_deductions'), 0, ',', '.') }}
            </div>
            <span class="text-muted mt-1" style="font-size: 11.5px;">Kasbon, disiplin, dll</span>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card h-100 p-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-uppercase text-muted" style="font-size: 11px; font-weight: 600; letter-spacing: 0.05em;">Take Home Pay (THP)</span>
                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; background: rgba(21, 128, 61, 0.08); color: #15803D;">
                    <i class="ti ti-cash"></i>
                </div>
            </div>
            <div class="fs-4 fw-bold font-mono text-success">
                Rp {{ number_format($period->total_net, 0, ',', '.') }}
            </div>
            <span class="text-muted mt-1" style="font-size: 11.5px;">{{ $period->employee_count }} karyawan terhitung</span>
        </div>
    </div>
</div>

{{-- Search & Filter Toolbar --}}
<div class="card admin-filter-toolbar mb-3">
    <form method="GET" action="{{ route('payroll.show', $period->id) }}" class="m-0">
        <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap">
            <div class="flex-grow-1" style="min-width: 240px;">
                <x-input-with-icon label="" value="{{ request('search') }}" name="search"
                    icon="ti ti-search" placeholder="Cari nama atau NIK karyawan..." hideLabel="true" />
            </div>
            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5">
                    <i class="ti ti-search" style="font-size: 14px;"></i>
                    <span>Cari</span>
                </button>
                @if (request()->hasAny(['search', 'kode_dept']))
                    <a href="{{ route('payroll.show', $period->id) }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1" title="Reset">
                        <i class="ti ti-refresh" style="font-size: 14px;"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                <thead style="background: #f4f3f2; border-bottom: 1px solid rgba(60, 42, 33, 0.08);">
                    <tr>
                        <th style="color: #4f4540; font-weight: 600; font-size: 11px; letter-spacing: 0.04em;">KARYAWAN</th>
                        <th style="color: #4f4540; font-weight: 600; font-size: 11px; letter-spacing: 0.04em;">GAJI POKOK</th>
                        <th style="color: #4f4540; font-weight: 600; font-size: 11px; letter-spacing: 0.04em;">TUNJANGAN</th>
                        <th style="color: #4f4540; font-weight: 600; font-size: 11px; letter-spacing: 0.04em;">UPAH LEMBUR</th>
                        <th style="color: #4f4540; font-weight: 600; font-size: 11px; letter-spacing: 0.04em;">POTONGAN</th>
                        <th style="color: #4f4540; font-weight: 600; font-size: 11px; letter-spacing: 0.04em;">TAKE HOME PAY (NET)</th>
                        <th style="color: #4f4540; font-weight: 600; font-size: 11px; letter-spacing: 0.04em;">STATUS</th>
                        <th class="text-end" style="color: #4f4540; font-weight: 600; font-size: 11px; letter-spacing: 0.04em;">RINCIAN</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($details as $detail)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2"
                                        style="width: 32px; height: 32px; background: #f4f3f2; color: #25160e; font-weight: 600; font-size: 12px; border: 1px solid rgba(60, 42, 33, 0.1);">
                                        {{ strtoupper(substr($detail->karyawan->nama_karyawan ?? 'K', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-truncate" style="max-width: 170px; color: #25160e;">
                                            {{ $detail->karyawan->nama_karyawan ?? $detail->nik }}
                                        </div>
                                        <div class="text-muted" style="font-size: 11.5px; font-family: 'JetBrains Mono', monospace;">
                                            {{ $detail->nik }} &bull; {{ $detail->karyawan->departemen->nama_dept ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-family: 'JetBrains Mono', monospace; font-size: 12.5px;">
                                    Rp {{ number_format($detail->basic_salary, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span style="font-family: 'JetBrains Mono', monospace; font-size: 12.5px;">
                                    Rp {{ number_format($detail->total_allowances, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span style="font-family: 'JetBrains Mono', monospace; font-size: 12.5px; color: {{ $detail->total_overtime_pay > 0 ? '#15803d' : '#81756f' }}; font-weight: {{ $detail->total_overtime_pay > 0 ? '600' : 'normal' }};">
                                    Rp {{ number_format($detail->total_overtime_pay, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span style="font-family: 'JetBrains Mono', monospace; font-size: 12.5px; color: {{ $detail->total_deductions > 0 ? '#ba1a1a' : '#81756f' }};">
                                    {{ $detail->total_deductions > 0 ? '- Rp ' . number_format($detail->total_deductions, 0, ',', '.') : '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold" style="font-family: 'JetBrains Mono', monospace; font-size: 13.5px; color: #15803d;">
                                    Rp {{ number_format($detail->take_home_pay, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                {!! $detail->status_badge_html !!}
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalBreakdown{{ $detail->id }}" style="border-radius: 6px; font-size: 12px; padding: 4px 10px;">
                                    <i class="ti ti-file-analytics me-1"></i> Rincian
                                </button>

                                {{-- Modal Breakdown Snapshot --}}
                                <div class="modal fade" id="modalBreakdown{{ $detail->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered text-start" style="max-width: 580px;">
                                        <div class="modal-content" style="border-radius: 12px; border: 1px solid rgba(60, 42, 33, 0.1);">
                                            <div class="modal-header border-bottom py-3" style="background: #faf9f8;">
                                                <div>
                                                    <h5 class="modal-title fw-bold mb-0" style="font-family: 'Outfit', sans-serif; font-size: 16px; color: #25160e;">
                                                        Slip Rincian Upah &mdash; {{ $period->formatted_period }}
                                                    </h5>
                                                    <span class="text-muted" style="font-size: 12px;">
                                                        {{ $detail->karyawan->nama_karyawan }} ({{ $detail->nik }})
                                                    </span>
                                                </div>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                {{-- Earnings Section --}}
                                                <div class="mb-3">
                                                    <div class="text-uppercase fw-bold mb-2 pb-1 border-bottom" style="font-size: 11px; color: #15803d; letter-spacing: 0.06em;">
                                                        I. Komponen Penghasilan (Earnings)
                                                    </div>
                                                    <table class="table table-sm table-borderless mb-0" style="font-size: 13px;">
                                                        @if (isset($detail->components_breakdown['earnings']))
                                                            @foreach ($detail->components_breakdown['earnings'] as $earn)
                                                                <tr>
                                                                    <td style="color: #4f4540;">
                                                                        {{ $earn['name'] }}
                                                                        @if (!empty($earn['rate_hours']))
                                                                            <span class="badge bg-light text-dark ms-1" style="font-family: 'JetBrains Mono', monospace; font-size: 10px;">
                                                                                {{ $earn['rate_hours'] }}j ekuivalen
                                                                            </span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-end fw-semibold" style="font-family: 'JetBrains Mono', monospace; color: #25160e;">
                                                                        Rp {{ number_format($earn['amount'], 0, ',', '.') }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                        <tr style="border-top: 1px dashed rgba(60,42,33,0.15);">
                                                            <td class="fw-bold" style="color: #25160e;">Total Penghasilan Bruto</td>
                                                            <td class="text-end fw-bold" style="font-family: 'JetBrains Mono', monospace; color: #25160e;">
                                                                Rp {{ number_format($detail->gross_salary, 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>

                                                {{-- Deductions Section --}}
                                                <div class="mb-3">
                                                    <div class="text-uppercase fw-bold mb-2 pb-1 border-bottom" style="font-size: 11px; color: #ba1a1a; letter-spacing: 0.06em;">
                                                        II. Komponen Potongan (Deductions)
                                                    </div>
                                                    <table class="table table-sm table-borderless mb-0" style="font-size: 13px;">
                                                        @if (isset($detail->components_breakdown['deductions']) && count($detail->components_breakdown['deductions']) > 0)
                                                            @foreach ($detail->components_breakdown['deductions'] as $ded)
                                                                <tr>
                                                                    <td style="color: #4f4540;">{{ $ded['name'] }}</td>
                                                                    <td class="text-end fw-semibold text-danger" style="font-family: 'JetBrains Mono', monospace;">
                                                                        - Rp {{ number_format($ded['amount'], 0, ',', '.') }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="2" class="text-muted fst-italic py-1" style="font-size: 12px;">Tidak ada potongan</td>
                                                            </tr>
                                                        @endif
                                                        <tr style="border-top: 1px dashed rgba(60,42,33,0.15);">
                                                            <td class="fw-bold text-danger">Total Potongan</td>
                                                            <td class="text-end fw-bold text-danger" style="font-family: 'JetBrains Mono', monospace;">
                                                                - Rp {{ number_format($detail->total_deductions, 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>

                                                {{-- Net Summary --}}
                                                <div class="p-3 rounded-2 d-flex justify-content-between align-items-center" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                                                    <div>
                                                        <div class="text-uppercase fw-bold" style="font-size: 11px; color: #15803d; letter-spacing: 0.05em;">Take Home Pay (THP)</div>
                                                        <span class="text-muted" style="font-size: 11px;">Gaji Bersih Diterima</span>
                                                    </div>
                                                    <div class="fs-4 fw-bold" style="font-family: 'JetBrains Mono', monospace; color: #15803d;">
                                                        Rp {{ number_format($detail->take_home_pay, 0, ',', '.') }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top py-2.5">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted" style="font-size: 14px;">
                                    <i class="ti ti-calculator fs-1 d-block mb-2 text-secondary"></i>
                                    Kalkulasi payroll untuk periode ini belum dijalankan.<br>
                                    @can('payroll.calculate')
                                        <form action="{{ route('payroll.calculate', $period->id) }}" method="POST" class="mt-2">
                                            @csrf
                                            <button type="submit" class="btn btn-sm text-white" style="background: #25160e; border-radius: 8px;">
                                                <i class="ti ti-player-play me-1"></i> Jalankan Kalkulasi Sekarang
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($details->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center py-2 px-3 border-top" style="background: #faf9f8;">
                <span class="text-muted" style="font-size: 12px;">
                    Menampilkan {{ $details->firstItem() }} - {{ $details->lastItem() }} dari {{ $details->total() }} karyawan
                </span>
                <div>
                    {{ $details->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection
