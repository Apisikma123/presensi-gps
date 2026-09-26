@extends('layouts.app')
@section('titlepage', 'Exit Clearance & Settlement')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item"><a href="{{ route('resignation.index') }}">Resign & Terminasi</a></li>
    <li class="breadcrumb-item active">Clearance & Settlement</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <a href="{{ route('resignation.index') }}" class="text-decoration-none text-muted mb-1 d-inline-flex align-items-center gap-1" style="font-size: 13px;">
            <i class="ti ti-arrow-left"></i> Kembali ke Daftar Resign
        </a>
        <h4 class="page-title mb-1">
            {{ $resignation->karyawan->nama_karyawan ?? 'Karyawan' }}
            <span class="font-mono text-muted fs-5 ms-2">[{{ $resignation->nik }}]</span>
        </h4>
        <p class="page-subtitle text-muted mb-0">Prosedur pengunduran diri, checklist exit clearance antar departemen, dan kalkulator pesangon.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('resignation.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i>
            <span>Kembali</span>
        </a>
        <a href="{{ route('offboarding.print', $resignation->id) }}" target="_blank" class="btn btn-outline-primary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-printer"></i>
            <span>Cetak Dokumen Clearance</span>
        </a>
    </div>
</div>

<!-- Employee Exit Information Strip -->
<div class="card mb-4" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
    <div class="card-body p-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-md rounded bg-label-danger d-flex align-items-center justify-content-center fw-bold" style="width: 48px; height: 48px; font-size: 18px;">
                        {{ strtoupper(substr($resignation->karyawan->nama_karyawan ?? 'K', 0, 2)) }}
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">{{ $resignation->karyawan->nama_karyawan ?? 'Karyawan' }}</h5>
                        <div class="text-muted small">
                            {{ $resignation->karyawan->departemen->nama_dept ?? '-' }} &bull; {{ $resignation->karyawan->cabang->nama_cabang ?? '-' }}
                        </div>
                        <div class="text-muted small">
                            Jabatan: {{ $resignation->karyawan->jabatan->nama_jabatan ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="text-muted small text-uppercase" style="font-size: 11px;">Kategori Keluar</div>
                <div class="fw-bold text-dark">{{ $resignation->kategori_keluar_label }}</div>
            </div>
            <div class="col-md-2">
                <div class="text-muted small text-uppercase" style="font-size: 11px;">Tanggal Efektif Keluar</div>
                <div class="fw-bold font-mono text-danger">
                    <i class="ti ti-calendar-off me-1"></i>{{ $resignation->tanggal_keluar->format('d M Y') }}
                </div>
            </div>
            <div class="col-md-2">
                <div class="text-muted small text-uppercase" style="font-size: 11px;">Status Clearance</div>
                <div>{!! $resignation->clearance_badge_html !!}</div>
            </div>
            <div class="col-md-2 text-md-end">
                <div class="text-muted small text-uppercase" style="font-size: 11px;">Hak Akhir (Settlement)</div>
                <div class="fw-bold font-mono text-success fs-4">
                    Rp {{ number_format($resignation->total_settlement, 0, ',', '.') }}
                </div>
                <span class="badge bg-light text-muted font-mono">{{ $resignation->settlement_status }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Column Left: Exit Clearance Checklists by Department -->
    <div class="col-lg-7">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="ti ti-shield-check fs-4 text-primary"></i>
                    <h6 class="card-title fw-bold text-dark mb-0">Checklist Exit Clearance Antar Departemen</h6>
                </div>
                <span class="badge bg-light text-dark font-mono">
                    {{ $resignation->clearances->where('is_cleared', true)->count() }} / {{ $resignation->clearances->count() }} Selesai
                </span>
            </div>

            <div class="card-body p-0">
                @php
                    $deptTitles = [
                        'IT' => ['name' => 'Teknologi Informasi (IT)', 'icon' => 'ti-device-laptop', 'badge' => 'bg-label-primary'],
                        'HR' => ['name' => 'Human Resources (HR)', 'icon' => 'ti-users', 'badge' => 'bg-label-info'],
                        'FINANCE' => ['name' => 'Keuangan & Kasbon', 'icon' => 'ti-cash', 'badge' => 'bg-label-success'],
                        'OPERATIONAL' => ['name' => 'Operasional & GA', 'icon' => 'ti-briefcase', 'badge' => 'bg-label-warning'],
                    ];
                @endphp

                @foreach($deptTitles as $dKey => $dMeta)
                @php
                    $items = $clearancesByDept->get($dKey, collect());
                @endphp
                @if($items->isNotEmpty())
                <div class="p-3 bg-light border-bottom border-top text-uppercase fw-bold small text-muted d-flex align-items-center gap-2" style="font-size: 11px; letter-spacing: 0.05em;">
                    <span class="badge {{ $dMeta['badge'] }} p-1 rounded"><i class="ti {{ $dMeta['icon'] }}"></i></span>
                    {{ $dMeta['name'] }}
                </div>

                <div class="list-group list-group-flush">
                    @foreach($items as $item)
                    <div class="list-group-item p-3 d-flex align-items-center gap-3">
                        <form action="{{ route('offboarding.clearance.toggle', $item->id) }}" method="POST" class="m-0">
                            @csrf
                            <input type="hidden" name="is_cleared" value="{{ $item->is_cleared ? '0' : '1' }}">
                            <button type="submit" class="btn btn-sm {{ $item->is_cleared ? 'btn-success' : 'btn-outline-secondary' }} d-flex align-items-center justify-content-center p-0" style="border-radius: 6px; width: 28px; height: 28px;">
                                <i class="ti ti-check fs-5"></i>
                            </button>
                        </form>

                        <div class="flex-grow-1">
                            <div class="fw-semibold {{ $item->is_cleared ? 'text-decoration-line-through text-muted' : 'text-dark' }}" style="font-size: 13.5px;">
                                {{ $item->item_name }}
                            </div>
                            @if($item->is_cleared && $item->cleared_at)
                                <div class="text-success small mt-1" style="font-size: 12px;">
                                    <i class="ti ti-check-double me-1"></i> Disetujui {{ $item->cleared_at->format('d M Y H:i') }}
                                    @if($item->clearedByUser) ({{ $item->clearedByUser->name }}) @endif
                                </div>
                            @endif
                        </div>

                        <div>
                            @if($item->is_cleared)
                                <span class="badge bg-label-success fw-semibold">Cleared</span>
                            @else
                                <span class="badge bg-label-warning fw-semibold">Pending</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- Column Right: Final Settlement & Severance Calculation -->
    <div class="col-lg-5">
        <div class="card mb-4" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="ti ti-calculator fs-4 text-success"></i>
                    <h6 class="card-title fw-bold text-dark mb-0">Kalkulator Hak Akhir & Pesangon</h6>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-primary border-0 small mb-3" style="background-color: rgba(105, 108, 255, 0.08); color: #696cff; border-radius: 8px;">
                    Perhitungan statutory mengacu pada <strong>PP No. 35 Tahun 2021</strong> berdasarkan masa kerja, kategori terminasi, serta saldo kasbon/pinjaman aktif.
                </div>

                @if($activeLoanBalance > 0)
                <div class="alert alert-warning small mb-3" style="border-radius: 8px;">
                    <i class="ti ti-alert-triangle me-1"></i>
                    Karyawan memiliki pinjaman kasbon aktif sebesar <strong class="font-mono">Rp {{ number_format($activeLoanBalance, 0, ',', '.') }}</strong> yang akan dipotongkan ke hak akhir.
                </div>
                @endif

                <form action="{{ route('offboarding.settlement.save', $resignation->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 12px;">Upah Bulanan Dasar (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text font-mono" style="border-radius: 8px 0 0 8px;">Rp</span>
                            <input type="number" id="inputWage" class="form-control font-mono" value="{{ $basicSalary }}">
                            <button type="button" class="btn btn-outline-secondary" id="btnRecalculate" style="border-radius: 0 8px 8px 0;">Hitung Otomatis</button>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold" style="font-size: 11.5px;">Uang Pesangon (Rp)</label>
                            <input type="number" name="severance_pay" id="severancePay" class="form-control form-control-sm font-mono" value="{{ $resignation->severance_pay }}" style="border-radius: 6px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold" style="font-size: 11.5px;">Penghargaan Masa Kerja (UPMK)</label>
                            <input type="number" name="service_pay" id="servicePay" class="form-control form-control-sm font-mono" value="{{ $resignation->service_pay }}" style="border-radius: 6px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold" style="font-size: 11.5px;">Penggantian Hak (UPH)</label>
                            <input type="number" name="compensation_pay" id="compensationPay" class="form-control form-control-sm font-mono" value="{{ $resignation->compensation_pay }}" style="border-radius: 6px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold" style="font-size: 11.5px;">Gaji Terakhir / Prorata</label>
                            <input type="number" name="final_salary_pay" id="finalSalaryPay" class="form-control form-control-sm font-mono" value="{{ $resignation->final_salary_pay }}" style="border-radius: 6px;">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-danger" style="font-size: 11.5px;">Potongan Kasbon / Kewajiban (Rp)</label>
                            <input type="number" name="deductions_pay" id="deductionsPay" class="form-control form-control-sm font-mono text-danger" value="{{ $resignation->deductions_pay ?: $activeLoanBalance }}" style="border-radius: 6px;">
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-2 mb-3 d-flex justify-content-between align-items-center" style="border-radius: 8px;">
                        <span class="fw-bold text-dark" style="font-size: 13px;">Total Bersih Hak Akhir:</span>
                        <span class="fs-4 fw-bold font-mono text-success" id="totalPreview">
                            Rp {{ number_format($resignation->total_settlement, 0, ',', '.') }}
                        </span>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-1.5" style="border-radius: 8px;">
                        <i class="ti ti-device-floppy"></i>
                        <span>Simpan Hak Akhir</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Finalize Offboarding Box -->
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4 text-center">
                <h6 class="fw-bold text-dark mb-1">Finalisasi Keluar Karyawan</h6>
                <p class="text-muted small mb-3" style="font-size: 12px;">
                    Setelah clearance seluruh departemen selesai dan hak akhir dibayarkan, lakukan finalisasi untuk menonaktifkan status karyawan dan memutus kontrak aktif.
                </p>
                <form action="{{ route('offboarding.finalize', $resignation->id) }}" method="POST">
                    @csrf
                    <button type="button" class="btn btn-danger w-100 py-2 d-inline-flex align-items-center justify-content-center gap-2 btn-confirm-action" style="border-radius: 8px;" data-title="Finalisasi Offboarding?" data-message="Apakah Anda yakin ingin menyelesaikan proses offboarding ini? Status karyawan akan resmi dinonaktifkan." data-destructive="true">
                        <i class="ti ti-check-double"></i>
                        <span>Finalisasi Offboarding & Nonaktifkan</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnRecalculate = document.getElementById('btnRecalculate');
    const inputWage = document.getElementById('inputWage');
    const severancePay = document.getElementById('severancePay');
    const servicePay = document.getElementById('servicePay');
    const compensationPay = document.getElementById('compensationPay');
    const finalSalaryPay = document.getElementById('finalSalaryPay');
    const deductionsPay = document.getElementById('deductionsPay');
    const totalPreview = document.getElementById('totalPreview');

    function updateGrandTotal() {
        const s = parseFloat(severancePay.value) || 0;
        const u = parseFloat(servicePay.value) || 0;
        const c = parseFloat(compensationPay.value) || 0;
        const f = parseFloat(finalSalaryPay.value) || 0;
        const d = parseFloat(deductionsPay.value) || 0;
        const total = Math.max(0, (s + u + c + f) - d);
        totalPreview.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    [severancePay, servicePay, compensationPay, finalSalaryPay, deductionsPay].forEach(inp => {
        inp.addEventListener('input', updateGrandTotal);
    });

    btnRecalculate.addEventListener('click', function () {
        const wage = inputWage.value || 5000000;
        fetch(`{{ url('/offboarding/' . $resignation->id . '/calculate-settlement') }}?monthly_wage=${wage}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.calculation) {
                    const calc = data.calculation;
                    severancePay.value = calc.severance_pay;
                    servicePay.value = calc.service_pay;
                    compensationPay.value = calc.compensation_pay;
                    deductionsPay.value = calc.active_loan_deduction;
                    updateGrandTotal();
                }
            })
            .catch(err => console.error(err));
    });
});
</script>
@endsection
