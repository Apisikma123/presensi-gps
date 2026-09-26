@extends('layouts.app')
@section('titlepage', 'Buat Evaluasi Kinerja')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item"><a href="{{ route('performance.index') }}">Penilaian Kinerja</a></li>
    <li class="breadcrumb-item active">Buat Evaluasi</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <a href="{{ route('performance.index') }}" class="text-decoration-none text-muted mb-1 d-inline-flex align-items-center gap-1" style="font-size: 13px;">
            <i class="ti ti-arrow-left"></i> Kembali ke Daftar Evaluasi
        </a>
        <h4 class="page-title mb-1">Formulir Evaluasi Kinerja Karyawan</h4>
        <p class="page-subtitle text-muted mb-0">Input penilaian indikator KPI objektif, bobot pencapaian, dan feedback pengembangan karir.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('performance.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i>
            <span>Batal</span>
        </a>
    </div>
</div>

<div class="card mx-auto shadow-sm" style="max-width: 950px; border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF;">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="card-title fw-bold text-dark mb-0">Informasi Penilaian & Periode</h5>
    </div>
    <form action="{{ route('performance.store') }}" method="POST">
        @csrf
        <div class="card-body p-4">
            @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Kode Evaluasi</label>
                    <input type="text" name="review_code" class="form-control font-mono" value="{{ old('review_code', $newCode) }}" required readonly style="border-radius: 8px;">
                </div>
                <div class="col-md-8">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Karyawan yang Dinilai</label>
                    <select name="nik" class="form-select" required style="border-radius: 8px;">
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($karyawans as $k)
                            <option value="{{ $k->nik }}" {{ old('nik') === $k->nik ? 'selected' : '' }}>
                                {{ $k->nik }} - {{ $k->nama_karyawan }} ({{ $k->departemen->nama_dept ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Judul / Periode Evaluasi</label>
                    <input type="text" name="period_title" class="form-control" placeholder="Contoh: Evaluasi Q3 2026, Penilaian Akhir Tahun" value="{{ old('period_title') }}" required style="border-radius: 8px;">
                </div>
                <div class="col-md-4">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Mulai Periode</label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', date('Y-m-01')) }}" required style="border-radius: 8px;">
                </div>
                <div class="col-md-4">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Selesai Periode</label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date', date('Y-m-t')) }}" required style="border-radius: 8px;">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Penilai (Atasan Langsung)</label>
                    <select name="reviewer_nik" class="form-select" style="border-radius: 8px;">
                        <option value="">-- Pilih Penilai (Opsional) --</option>
                        @foreach($karyawans as $r)
                            <option value="{{ $r->nik }}" {{ old('reviewer_nik') === $r->nik ? 'selected' : '' }}>
                                {{ $r->nama_karyawan }} ({{ $r->jabatan->nama_jabatan ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Status Pengajuan</label>
                    <select name="status" class="form-select" style="border-radius: 8px;">
                        <option value="DRAFT">Simpan sebagai Draft</option>
                        <option value="SUBMITTED">Langsung Ajukan (Submitted)</option>
                        <option value="APPROVED">Setujui Langsung (Approved)</option>
                    </select>
                </div>
            </div>

            <!-- Dynamic KPI Indicators -->
            <div class="border-top pt-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Indikator Kinerja Utama (KPI)</h6>
                        <div class="text-muted small" style="font-size: 11.5px;">Total bobot sebaiknya berjumlah 100%. Nilai berkisar 0 - 100.</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" id="btnAddKpi">
                        <i class="ti ti-plus"></i>
                        <span>Tambah KPI</span>
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tableKpi" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 8px;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 35%;">INDIKATOR KPI</th>
                                <th style="width: 15%;">BOBOT (%)</th>
                                <th style="width: 20%;">TARGET & REALISASI</th>
                                <th style="width: 15%;">SKOR (0-100)</th>
                                <th style="width: 10%;">SKOR BOBOT</th>
                                <th style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody id="kpiRows">
                            <tr>
                                <td>
                                    <input type="text" name="kpis[0][kpi_name]" class="form-control form-control-sm" placeholder="Contoh: Pencapaian Target Penjualan" required style="border-radius: 6px;">
                                </td>
                                <td>
                                    <input type="number" name="kpis[0][weight]" class="form-control form-control-sm font-mono kpi-weight" value="50" min="1" max="100" required style="border-radius: 6px;">
                                </td>
                                <td>
                                    <input type="text" name="kpis[0][target_value]" class="form-control form-control-sm mb-1" placeholder="Target: 100 Jt" style="border-radius: 6px;">
                                    <input type="text" name="kpis[0][actual_value]" class="form-control form-control-sm" placeholder="Realisasi: 95 Jt" style="border-radius: 6px;">
                                </td>
                                <td>
                                    <input type="number" name="kpis[0][score]" class="form-control form-control-sm font-mono kpi-score" value="95" min="0" max="100" required style="border-radius: 6px;">
                                </td>
                                <td class="text-center font-mono fw-bold kpi-weighted">
                                    47.5
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-icon remove-kpi" disabled style="border-radius: 6px;">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" name="kpis[1][kpi_name]" class="form-control form-control-sm" placeholder="Contoh: Kedisiplinan & Presensi Kehadiran" value="Kedisiplinan & Kehadiran" required style="border-radius: 6px;">
                                </td>
                                <td>
                                    <input type="number" name="kpis[1][weight]" class="form-control form-control-sm font-mono kpi-weight" value="50" min="1" max="100" required style="border-radius: 6px;">
                                </td>
                                <td>
                                    <input type="text" name="kpis[1][target_value]" class="form-control form-control-sm mb-1" placeholder="Target: >= 98%" value=">= 98%" style="border-radius: 6px;">
                                    <input type="text" name="kpis[1][actual_value]" class="form-control form-control-sm" placeholder="Realisasi: 99%" value="99%" style="border-radius: 6px;">
                                </td>
                                <td>
                                    <input type="number" name="kpis[1][score]" class="form-control form-control-sm font-mono kpi-score" value="90" min="0" max="100" required style="border-radius: 6px;">
                                </td>
                                <td class="text-center font-mono fw-bold kpi-weighted">
                                    45.0
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-icon remove-kpi" style="border-radius: 6px;">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="4" class="text-end">Total Skor Akhir:</td>
                                <td class="text-center font-mono text-success fs-4" id="grandScore">92.5</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Feedback & Goals -->
            <div class="row g-3 border-top pt-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Kelebihan & Prestasi Karyawan (Strengths)</label>
                    <textarea name="strengths" rows="3" class="form-control" placeholder="Pencapaian luar biasa atau kompetensi utama..." style="border-radius: 8px;">{{ old('strengths') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Area yang Perlu Ditingkatkan (Improvements)</label>
                    <textarea name="areas_for_improvement" rows="3" class="form-control" placeholder="Hal-hal yang membutuhkan bimbingan atau pelatihan..." style="border-radius: 8px;">{{ old('areas_for_improvement') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Target & Sasaran Periode Mendatang (Goals)</label>
                    <textarea name="goals_next_period" rows="2" class="form-control" placeholder="Target kerja atau proyek pengembangan untuk periode berikutnya..." style="border-radius: 8px;">{{ old('goals_next_period') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card-footer bg-light d-flex justify-content-end gap-2 py-3 px-4 border-top">
            <a href="{{ route('performance.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">Batal</a>
            <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-1.5">
                <i class="ti ti-check"></i>
                <span>Simpan Penilaian Kinerja</span>
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let kpiIndex = 2;
    const btnAddKpi = document.getElementById('btnAddKpi');
    const kpiRows = document.getElementById('kpiRows');
    const grandScoreEl = document.getElementById('grandScore');

    function recalculate() {
        let total = 0;
        document.querySelectorAll('#kpiRows tr').forEach(tr => {
            const w = parseFloat(tr.querySelector('.kpi-weight')?.value) || 0;
            const s = parseFloat(tr.querySelector('.kpi-score')?.value) || 0;
            const weighted = (w * s) / 100;
            const wEl = tr.querySelector('.kpi-weighted');
            if (wEl) wEl.textContent = weighted.toFixed(1);
            total += weighted;
        });
        grandScoreEl.textContent = total.toFixed(1);
    }

    btnAddKpi.addEventListener('click', function () {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <input type="text" name="kpis[${kpiIndex}][kpi_name]" class="form-control form-control-sm" placeholder="Nama indikator KPI..." required style="border-radius: 6px;">
            </td>
            <td>
                <input type="number" name="kpis[${kpiIndex}][weight]" class="form-control form-control-sm font-mono kpi-weight" value="20" min="1" max="100" required style="border-radius: 6px;">
            </td>
            <td>
                <input type="text" name="kpis[${kpiIndex}][target_value]" class="form-control form-control-sm mb-1" placeholder="Target..." style="border-radius: 6px;">
                <input type="text" name="kpis[${kpiIndex}][actual_value]" class="form-control form-control-sm" placeholder="Realisasi..." style="border-radius: 6px;">
            </td>
            <td>
                <input type="number" name="kpis[${kpiIndex}][score]" class="form-control form-control-sm font-mono kpi-score" value="80" min="0" max="100" required style="border-radius: 6px;">
            </td>
            <td class="text-center font-mono fw-bold kpi-weighted">
                16.0
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-icon remove-kpi" style="border-radius: 6px;">
                    <i class="ti ti-trash"></i>
                </button>
            </td>
        `;
        taskRows = kpiRows.appendChild(tr);
        kpiIndex++;
        recalculate();
    });

    kpiRows.addEventListener('input', function (e) {
        if (e.target.classList.contains('kpi-weight') || e.target.classList.contains('kpi-score')) {
            recalculate();
        }
    });

    kpiRows.addEventListener('click', function (e) {
        if (e.target.closest('.remove-kpi')) {
            const btn = e.target.closest('.remove-kpi');
            if (!btn.disabled && kpiRows.children.length > 1) {
                btn.closest('tr').remove();
                recalculate();
            }
        }
    });

    recalculate();
});
</script>
@endsection
