@extends('layouts.app')
@section('titlepage', 'Kepatuhan Regulasi Indonesia')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Keuangan & Payroll</a></li>
    <li class="breadcrumb-item active">Kepatuhan Regulasi</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Kepatuhan Regulasi Indonesia</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                Resmi RI
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Parameter resmi PPh 21 TER (PP 58/2023), BPJS TK & Kesehatan, serta simulator kalkulasi terpadu.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <a href="{{ route('thr.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-gift" style="font-size: 16px;"></i>
            <span>Kelola THR</span>
        </a>
        <a href="{{ route('payroll_periods.index') }}" class="btn btn-primary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-cash" style="font-size: 16px;"></i>
            <span>Periode Payroll</span>
        </a>
    </div>
</div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible mb-4" role="alert" style="border-radius: 10px; border: 1px solid #bbf7d0; background: #f0fdf4; color: #15803d;">
            <div class="d-flex align-items-center">
                <i class="ti ti-circle-check fs-5 me-2"></i>
                <span style="font-size: 13.5px;">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Metric Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase text-muted" style="font-size: 11px; font-weight: 600; letter-spacing: 0.5px;">PPh 21 TER Bulanan</span>
                        <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 10px; border-radius: 6px;">PP 58/2023</span>
                    </div>
                    <div class="h4 fw-bold mb-1 font-mono text-dark">
                        Kat. A / B / C
                    </div>
                    <div class="text-muted small">Tarif progresif efektif 0% s/d 34%</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase text-muted" style="font-size: 11px; font-weight: 600; letter-spacing: 0.5px;">BPJS Kesehatan</span>
                        <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 10px; border-radius: 6px;">Perpres 64/2020</span>
                    </div>
                    <div class="h4 fw-bold mb-1 font-mono text-dark">
                        5.0% <span style="font-size: 13px; font-weight: normal; color: #78716c;">(1% + 4%)</span>
                    </div>
                    <div class="text-muted small">Plafon upah maks: Rp 12.000.000</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase text-muted" style="font-size: 11px; font-weight: 600; letter-spacing: 0.5px;">BPJS Ketenagakerjaan</span>
                        <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 10px; border-radius: 6px;">PP 44/45 2015</span>
                    </div>
                    <div class="h4 fw-bold mb-1 font-mono text-dark">
                        4 Program
                    </div>
                    <div class="text-muted small">JHT (5.7%), JP (3%), JKK (0.24%), JKM (0.3%)</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase text-muted" style="font-size: 11px; font-weight: 600; letter-spacing: 0.5px;">THR Keagamaan</span>
                        <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 10px; border-radius: 6px;">Permenaker 6/2016</span>
                    </div>
                    <div class="h4 fw-bold mb-1 font-mono text-dark">
                        Masa Kerja
                    </div>
                    <div class="text-muted small">&ge; 12 Bln (1x Upah) | 1-12 Bln (Prorata)</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="row g-4">
        {{-- Left: Live Salary & Tax Simulator --}}
        <div class="col-lg-6">
            <div class="card h-100" style="border: 1px solid #e5ded4; border-radius: 12px;">
                <div class="card-header bg-transparent py-3" style="border-bottom: 1px solid #f0ece1;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ti ti-calculator fs-4" style="color: var(--color-primary);"></i>
                        <h6 class="fw-bold mb-0" style="font-family: 'Outfit', sans-serif; color: var(--theme-text-primary, #0F172A);">
                            Simulator Potongan Pajak PPh 21 & BPJS
                        </h6>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form id="simulatorForm" onsubmit="event.preventDefault(); runSimulation();">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size: 13px; color: var(--theme-text-primary, #0F172A);">Penghasilan Bruto Sebulan (IDR)</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background: #f8f6f0; border-color: #e5ded4; font-family: 'JetBrains Mono';">Rp</span>
                                <input type="number" id="simSalary" class="form-control" value="7500000" min="0" step="50000" style="font-family: 'JetBrains Mono'; border-color: #e5ded4;">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="font-size: 13px; color: var(--theme-text-primary, #0F172A);">Status PTKP</label>
                                <select id="simPtkp" class="form-select" style="border-color: #e5ded4;">
                                    <optgroup label="Kategori A">
                                        <option value="TK/0" selected>TK/0 - Tidak Kawin, 0 Tanggungan</option>
                                        <option value="TK/1">TK/1 - Tidak Kawin, 1 Tanggungan</option>
                                        <option value="K/0">K/0 - Kawin, 0 Tanggungan</option>
                                    </optgroup>
                                    <optgroup label="Kategori B">
                                        <option value="TK/2">TK/2 - Tidak Kawin, 2 Tanggungan</option>
                                        <option value="TK/3">TK/3 - Tidak Kawin, 3 Tanggungan</option>
                                        <option value="K/1">K/1 - Kawin, 1 Tanggungan</option>
                                        <option value="K/2">K/2 - Kawin, 2 Tanggungan</option>
                                    </optgroup>
                                    <optgroup label="Kategori C">
                                        <option value="K/3">K/3 - Kawin, 3 Tanggungan</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="font-size: 13px; color: var(--theme-text-primary, #0F172A);">Status NPWP</label>
                                <select id="simNpwp" class="form-select" style="border-color: #e5ded4;">
                                    <option value="1" selected>Memiliki NPWP Valid</option>
                                    <option value="0">Tidak Memiliki NPWP</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-4" style="border-radius: 8px; font-weight: 500;">
                            <i class="ti ti-refresh fs-5 me-1"></i> Hitung Simulasi
                        </button>
                    </form>

                    {{-- Simulation Results Display --}}
                    <div id="simResults" class="p-3" style="background: #faf9f8; border-radius: 10px; border: 1px solid #e5ded4;">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2" style="border-bottom: 1px dashed #d6cebe;">
                            <span class="text-muted" style="font-size: 12.5px;">Kategori TER PPh 21:</span>
                            <span id="resCategory" class="badge" style="background: var(--theme-color-1); color: var(--theme-primary-contrast, #FFFFFF); font-family: 'JetBrains Mono'; font-size: 12px;">TER Kategori A (1.25%)</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span style="font-size: 13px; color: var(--theme-text-secondary, #475569);">Potongan PPh 21 Bulanan:</span>
                            <span id="resTax" class="fw-bold" style="font-family: 'JetBrains Mono'; color: #ba1a1a;">- Rp 93.750</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span style="font-size: 13px; color: var(--theme-text-secondary, #475569);">BPJS Kesehatan (Pekerja 1%):</span>
                            <span id="resBpjsKes" class="fw-bold" style="font-family: 'JetBrains Mono'; color: #ba1a1a;">- Rp 75.000</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span style="font-size: 13px; color: var(--theme-text-secondary, #475569);">BPJS TK JHT (Pekerja 2%):</span>
                            <span id="resBpjsJht" class="fw-bold" style="font-family: 'JetBrains Mono'; color: #ba1a1a;">- Rp 150.000</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2" style="border-bottom: 1px dashed #d6cebe;">
                            <span style="font-size: 13px; color: var(--theme-text-secondary, #475569);">BPJS TK JP (Pekerja 1%):</span>
                            <span id="resBpjsJp" class="fw-bold" style="font-family: 'JetBrains Mono'; color: #ba1a1a;">- Rp 75.000</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" style="font-size: 13.5px; color: var(--theme-text-primary, #0F172A);">Total Potongan Karyawan:</span>
                            <span id="resTotalDed" class="fw-bold" style="font-family: 'JetBrains Mono'; color: #ba1a1a; font-size: 14px;">- Rp 393.750</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                            <span class="fw-bold" style="font-size: 14px; color: #15803d;">Estimasi Take-Home Pay (THP):</span>
                            <span id="resThp" class="fw-bold" style="font-family: 'JetBrains Mono'; color: #15803d; font-size: 16px;">Rp 7.106.250</span>
                        </div>

                        <div class="mt-3 pt-2" style="border-top: 1px dashed #d6cebe;">
                            <small class="text-muted d-block mb-1" style="font-size: 11.5px; font-weight: 600;">BEBAN PEMBERI KERJA (COMPANY COST):</small>
                            <div class="d-flex justify-content-between text-muted" style="font-size: 12px;">
                                <span>Iuran BPJS Perusahaan (Kes + JHT + JP + JKK + JKM):</span>
                                <span id="resEmployer" style="font-family: 'JetBrains Mono'; color: var(--theme-text-primary, #0F172A);">+ Rp 535.500</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Official Policy Registry --}}
        <div class="col-lg-6">
            <div class="card h-100" style="border: 1px solid #e5ded4; border-radius: 12px;">
                <div class="card-header bg-transparent py-3" style="border-bottom: 1px solid #f0ece1;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ti ti-file-certificate fs-4" style="color: var(--color-primary);"></i>
                            <h6 class="fw-bold mb-0" style="font-family: 'Outfit', sans-serif; color: var(--theme-text-primary, #0F172A);">
                                Dasar Hukum & Konfigurasi Aktif
                            </h6>
                        </div>
                        <span class="badge" style="background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; font-size: 11px;">Aktif Terverifikasi</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="accordion" id="rulesAccordion">
                        {{-- Rule 1: TAX --}}
                        <div class="accordion-item mb-3" style="border: 1px solid #e5ded4; border-radius: 8px; overflow: hidden;">
                            <h2 class="accordion-header" id="headingTax">
                                <button class="accordion-button collapsed py-2.5 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTax" aria-expanded="false" style="background: #faf9f8; font-size: 13.5px; font-weight: 600; color: var(--theme-text-primary, #0F172A);">
                                    <div class="d-flex justify-content-between w-100 pe-3 align-items-center">
                                        <span>Tarif Efektif Rata-Rata (TER) PPh 21</span>
                                        <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15));">{{ $taxRule?->version ?? 'PP 58/2023' }}</span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapseTax" class="accordion-collapse collapse" data-bs-parent="#rulesAccordion">
                                <div class="accordion-body p-3" style="font-size: 12.5px; color: #44403c; background: #fff;">
                                    <p class="mb-2"><strong>Referensi Resmi:</strong> {{ $taxRule?->source_reference ?? 'PP No. 58/2023 & PMK No. 168/2023' }}</p>
                                    <p class="mb-2"><strong>Mulai Berlaku:</strong> {{ $taxRule?->effective_from?->format('d M Y') ?? '01 Jan 2024' }}</p>
                                    <p class="mb-0 text-muted">Pemotongan pajak gaji bulanan masa Januari s/d November menggunakan TER Kategori A, B, atau C sesuai status PTKP pegawai.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Rule 2: BPJS Kesehatan --}}
                        <div class="accordion-item mb-3" style="border: 1px solid #e5ded4; border-radius: 8px; overflow: hidden;">
                            <h2 class="accordion-header" id="headingKes">
                                <button class="accordion-button collapsed py-2.5 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseKes" aria-expanded="false" style="background: #faf9f8; font-size: 13.5px; font-weight: 600; color: var(--theme-text-primary, #0F172A);">
                                    <div class="d-flex justify-content-between w-100 pe-3 align-items-center">
                                        <span>BPJS Kesehatan Badan Usaha</span>
                                        <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15));">{{ $bpjsKesRule?->version ?? 'Perpres 64/2020' }}</span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapseKes" class="accordion-collapse collapse" data-bs-parent="#rulesAccordion">
                                <div class="accordion-body p-3" style="font-size: 12.5px; color: #44403c; background: #fff;">
                                    <p class="mb-2"><strong>Referensi Resmi:</strong> {{ $bpjsKesRule?->source_reference ?? 'Peraturan Presiden No. 64/2020' }}</p>
                                    <p class="mb-2"><strong>Tarif Iuran:</strong> Pekerja: 1%, Pemberi Kerja: 4% (Total 5%)</p>
                                    <p class="mb-0"><strong>Batas Upah Maksimum:</strong> Rp 12.000.000 / bulan</p>
                                </div>
                            </div>
                        </div>

                        {{-- Rule 3: BPJS Ketenagakerjaan --}}
                        <div class="accordion-item mb-3" style="border: 1px solid #e5ded4; border-radius: 8px; overflow: hidden;">
                            <h2 class="accordion-header" id="headingTk">
                                <button class="accordion-button collapsed py-2.5 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTk" aria-expanded="false" style="background: #faf9f8; font-size: 13.5px; font-weight: 600; color: var(--theme-text-primary, #0F172A);">
                                    <div class="d-flex justify-content-between w-100 pe-3 align-items-center">
                                        <span>BPJS Ketenagakerjaan (4 Program)</span>
                                        <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15));">{{ $bpjsTkRule?->version ?? 'PP 44/45 2015' }}</span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapseTk" class="accordion-collapse collapse" data-bs-parent="#rulesAccordion">
                                <div class="accordion-body p-3" style="font-size: 12.5px; color: #44403c; background: #fff;">
                                    <p class="mb-1"><strong>JHT (Jaminan Hari Tua):</strong> Pekerja 2%, Perusahaan 3.7%</p>
                                    <p class="mb-1"><strong>JP (Jaminan Pensiun):</strong> Pekerja 1%, Perusahaan 2% (Plafon: Rp 10.042.300)</p>
                                    <p class="mb-1"><strong>JKK (Kecelakaan Kerja):</strong> Perusahaan 0.24% (Risiko Sangat Rendah)</p>
                                    <p class="mb-0"><strong>JKM (Kematian):</strong> Perusahaan 0.30%</p>
                                </div>
                            </div>
                        </div>

                        {{-- Rule 4: THR --}}
                        <div class="accordion-item" style="border: 1px solid #e5ded4; border-radius: 8px; overflow: hidden;">
                            <h2 class="accordion-header" id="headingThr">
                                <button class="accordion-button collapsed py-2.5 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThr" aria-expanded="false" style="background: #faf9f8; font-size: 13.5px; font-weight: 600; color: var(--theme-text-primary, #0F172A);">
                                    <div class="d-flex justify-content-between w-100 pe-3 align-items-center">
                                        <span>Tunjangan Hari Raya (THR) Keagamaan</span>
                                        <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15));">{{ $thrRule?->version ?? 'Permenaker 6/2016' }}</span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapseThr" class="accordion-collapse collapse" data-bs-parent="#rulesAccordion">
                                <div class="accordion-body p-3" style="font-size: 12.5px; color: #44403c; background: #fff;">
                                    <p class="mb-2"><strong>Dasar Regulasi:</strong> PP No. 36/2021 & Permenaker No. 6/2016</p>
                                    <p class="mb-1"><strong>Masa Kerja &ge; 12 Bulan:</strong> Diberikan 1 (satu) bulan upah penuh.</p>
                                    <p class="mb-0"><strong>Masa Kerja 1 s/d &lt; 12 Bulan:</strong> Diberikan proporsional (Masa Kerja / 12 &times; 1 Bulan Upah).</p>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

@push('myscript')
<script>
function formatRupiah(number) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(number);
}

function runSimulation() {
    const salary = parseFloat(document.getElementById('simSalary').value) || 0;
    const ptkp = document.getElementById('simPtkp').value;
    const hasNpwp = document.getElementById('simNpwp').value === '1';

    fetch(`{{ route('compliance.index') }}?simulate=1&salary=${salary}&ptkp_status=${encodeURIComponent(ptkp)}&has_npwp=${hasNpwp}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('resCategory').innerText = `TER Kat. ${data.tax.category} (${data.tax.rate_percent})`;
            document.getElementById('resTax').innerText = `- ${formatRupiah(data.tax.tax_amount)}`;
            document.getElementById('resBpjsKes').innerText = `- ${formatRupiah(data.bpjs.kesehatan.employee_amount)}`;
            document.getElementById('resBpjsJht').innerText = `- ${formatRupiah(data.bpjs.ketenagakerjaan.jht.employee_amount)}`;
            document.getElementById('resBpjsJp').innerText = `- ${formatRupiah(data.bpjs.ketenagakerjaan.jp.employee_amount)}`;
            document.getElementById('resTotalDed').innerText = `- ${formatRupiah(data.tax.tax_amount + data.bpjs.total_employee_deduction)}`;
            document.getElementById('resThp').innerText = formatRupiah(data.take_home_pay);
            document.getElementById('resEmployer').innerText = `+ ${formatRupiah(data.bpjs.total_employer_contribution)}`;
        }
    })
    .catch(err => {
        console.error('Simulation error:', err);
    });
}
</script>
@endpush
@endsection
