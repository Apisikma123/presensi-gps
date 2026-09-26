@extends('layouts.app')
@section('titlepage', 'Buat SPK Lembur')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item"><a href="{{ route('overtime.index') }}">SPK Lembur</a></li>
    <li class="breadcrumb-item active">Buat SPK</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="page-title mb-1">Buat Surat Perintah Kerja (SPK) Lembur</h4>
        <p class="page-subtitle text-muted mb-0">Penugasan lembur karyawan dengan kepatuhan PP 35/2021 dan simulasi live formula Depnaker.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('overtime.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible mb-4" role="alert">
        <div class="fw-bold mb-1">Periksa kembali isian form:</div>
        <ul class="mb-0 ps-3" style="font-size: 13px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    {{-- Form Column --}}
    <div class="col-12 col-lg-7">
        <div class="card h-100" style="background: #FFFFFF; border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-header border-bottom py-3" style="background: #FAF9F8;">
                <h5 class="card-title mb-0" style="font-family: 'Outfit', sans-serif; font-size: 15px; color: #3C2A21;">
                    <i class="ti ti-file-pencil me-1.5 text-primary"></i> Data Penugasan Lembur
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('overtime.store') }}" method="POST" id="formOvertime">
                        @csrf

                        {{-- Karyawan --}}
                        <div class="mb-3">
                            <label class="form-label" style="font-size: 12px; font-weight: 600; color: #4f4540; text-transform: uppercase;">
                                Karyawan Ditugaskan <span class="text-danger">*</span>
                            </label>
                            <select name="nik" id="nik" class="form-select" required style="border-radius: 8px; font-size: 13.5px;">
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->nik }}" {{ old('nik') == $emp->nik ? 'selected' : '' }}>
                                        {{ $emp->nama_karyawan }} ({{ $emp->nik }}) &bull; {{ $emp->departemen->nama_dept ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tanggal & Tipe Hari --}}
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label" style="font-size: 12px; font-weight: 600; color: #4f4540; text-transform: uppercase;">
                                    Tanggal Lembur <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="tanggal" id="tanggal" class="form-control"
                                    value="{{ old('tanggal', date('Y-m-d')) }}" required style="border-radius: 8px; font-size: 13.5px;">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" style="font-size: 12px; font-weight: 600; color: #4f4540; text-transform: uppercase;">
                                    Tipe Hari <span class="text-danger">*</span>
                                </label>
                                <select name="day_type" id="day_type" class="form-select" required style="border-radius: 8px; font-size: 13.5px;">
                                    <option value="WORKDAY" {{ old('day_type') == 'WORKDAY' ? 'selected' : '' }}>Hari Kerja</option>
                                    <option value="OFFDAY_5DAYS" {{ old('day_type') == 'OFFDAY_5DAYS' ? 'selected' : '' }}>Libur Mingguan (5 Hari Kerja/Minggu)</option>
                                    <option value="OFFDAY_6DAYS" {{ old('day_type') == 'OFFDAY_6DAYS' ? 'selected' : '' }}>Libur Mingguan (6 Hari Kerja/Minggu)</option>
                                    <option value="PUBLIC_HOLIDAY" {{ old('day_type') == 'PUBLIC_HOLIDAY' ? 'selected' : '' }}>Hari Libur Resmi / Nasional</option>
                                </select>
                            </div>
                        </div>

                        {{-- Jam Mulai & Jam Selesai --}}
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label" style="font-size: 12px; font-weight: 600; color: #4f4540; text-transform: uppercase;">
                                    Waktu Mulai <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local" name="lembur_mulai" id="lembur_mulai" class="form-control"
                                    value="{{ old('lembur_mulai', date('Y-m-d') . 'T17:00') }}" required
                                    style="border-radius: 8px; font-family: 'JetBrains Mono', monospace; font-size: 13px;">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" style="font-size: 12px; font-weight: 600; color: #4f4540; text-transform: uppercase;">
                                    Waktu Selesai <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local" name="lembur_selesai" id="lembur_selesai" class="form-control"
                                    value="{{ old('lembur_selesai', date('Y-m-d') . 'T19:00') }}" required
                                    style="border-radius: 8px; font-family: 'JetBrains Mono', monospace; font-size: 13px;">
                            </div>
                        </div>

                        {{-- Kebijakan Lembur --}}
                        <div class="mb-3">
                            <label class="form-label" style="font-size: 12px; font-weight: 600; color: #4f4540; text-transform: uppercase;">
                                Skema Kebijakan Pengali
                            </label>
                            <select name="overtime_policy_id" id="overtime_policy_id" class="form-select" style="border-radius: 8px; font-size: 13.5px;">
                                @foreach ($policies as $pol)
                                    <option value="{{ $pol->id }}" {{ old('overtime_policy_id', $defaultPolicy->id) == $pol->id ? 'selected' : '' }}>
                                        {{ $pol->name }} ({{ $pol->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Uraian Tugas --}}
                        <div class="mb-4">
                            <label class="form-label" style="font-size: 12px; font-weight: 600; color: #4f4540; text-transform: uppercase;">
                                Uraian Pekerjaan / Tugas Lembur <span class="text-danger">*</span>
                            </label>
                            <textarea name="keterangan" rows="3" class="form-control" required
                                placeholder="Jelaskan secara spesifik tugas dan urgensi pekerjaan lembur..."
                                style="border-radius: 8px; font-size: 13px;">{{ old('keterangan') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                            <a href="{{ route('overtime.index') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5">
                                <i class="ti ti-send"></i>
                                <span>Terbitkan SPK Lembur</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Live Depnaker Calculation Simulator & Compliance Check --}}
        <div class="col-12 col-lg-5">
            <div class="card h-100" style="background: #faf9f8; border: 1px solid rgba(60, 42, 33, 0.08); border-radius: 12px;">
                <div class="card-header border-bottom py-3" style="background: #f4f3f2;">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0" style="font-family: 'Outfit', sans-serif; font-size: 15px; color: #25160e;">
                            Kalkulator Depnaker (PP 35/2021)
                        </h5>
                        <span class="badge" style="background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; font-size: 10px;">
                            Simulasi Live
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    {{-- Summary Bento --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-3 bg-white rounded-3 border" style="border-color: rgba(60,42,33,0.08) !important;">
                                <div class="text-muted text-uppercase" style="font-size: 10.5px; font-weight: 600; letter-spacing: 0.04em;">Durasi Rencana</div>
                                <div class="fs-4 fw-bold mt-1" id="simPlannedHours" style="font-family: 'JetBrains Mono', monospace; color: #25160e;">
                                    2.0 <span style="font-size: 12px; font-weight: normal; color: #755841;">jam</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-white rounded-3 border" style="border-color: rgba(60,42,33,0.08) !important;">
                                <div class="text-muted text-uppercase" style="font-size: 10.5px; font-weight: 600; letter-spacing: 0.04em;">Jam Bayar Pengali</div>
                                <div class="fs-4 fw-bold mt-1" id="simRateHours" style="font-family: 'JetBrains Mono', monospace; color: #15803d;">
                                    3.5 <span style="font-size: 12px; font-weight: normal; color: #15803d;">jam</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Meal Allowance Badge --}}
                    <div id="simMealBox" class="alert mb-3 py-2 px-3 d-flex align-items-center justify-content-between" style="border-radius: 8px; background: #ffffff; border: 1px solid rgba(60,42,33,0.08); font-size: 12px;">
                        <span class="d-flex align-items-center gap-1.5 text-muted">
                            <i class="ti ti-soup"></i> Hak Makanan / Minuman (≥1.400 kkal):
                        </span>
                        <span id="simMealBadge" class="badge" style="background: #f4f3f2; color: #755841;">Tidak</span>
                    </div>

                    {{-- Tier Breakdown Table --}}
                    <div class="mb-3">
                        <label class="form-label text-muted text-uppercase mb-1" style="font-size: 11px; font-weight: 600; letter-spacing: 0.04em;">
                            Rincian Pengali Tier Regulasi
                        </label>
                        <div class="bg-white rounded-3 border overflow-hidden" style="border-color: rgba(60,42,33,0.08) !important;">
                            <table class="table table-sm table-borderless mb-0" style="font-size: 12px;">
                                <thead style="background: #f4f3f2; border-bottom: 1px solid rgba(60,42,33,0.06);">
                                    <tr>
                                        <th class="ps-3 py-1.5 text-muted">Jam Ke-</th>
                                        <th class="py-1.5 text-muted">Durasi</th>
                                        <th class="py-1.5 text-muted">Faktor</th>
                                        <th class="pe-3 py-1.5 text-end text-muted">Jam Bayar</th>
                                    </tr>
                                </thead>
                                <tbody id="simBreakdownBody">
                                    <tr>
                                        <td class="ps-3 py-1.5">Jam ke-1</td>
                                        <td class="py-1.5">1.0 jam</td>
                                        <td class="py-1.5"><span class="badge bg-light text-dark">1.5x</span></td>
                                        <td class="pe-3 py-1.5 text-end fw-semibold">1.50 jam</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-3 py-1.5">Jam ke-2 dst</td>
                                        <td class="py-1.5">1.0 jam</td>
                                        <td class="py-1.5"><span class="badge bg-light text-dark">2.0x</span></td>
                                        <td class="pe-3 py-1.5 text-end fw-semibold">2.00 jam</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Compliance Limits Alert Box --}}
                    <div id="simComplianceBox" class="alert mb-0" style="display: none; border-radius: 8px; font-size: 12px;">
                        <div class="d-flex align-items-center gap-1.5 fw-semibold mb-1" id="simComplianceTitle">
                            <i class="ti ti-alert-triangle"></i> Peringatan Batas Regulasi
                        </div>
                        <div id="simComplianceText"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputMulai = document.getElementById('lembur_mulai');
    const inputSelesai = document.getElementById('lembur_selesai');
    const selectDayType = document.getElementById('day_type');
    const selectPolicy = document.getElementById('overtime_policy_id');
    const selectNik = document.getElementById('nik');

    function updatePreview() {
        const mulai = inputMulai.value;
        const selesai = inputSelesai.value;
        const dayType = selectDayType.value;
        const policyId = selectPolicy.value;
        const nik = selectNik.value;

        if (!mulai || !selesai) return;

        fetch('{{ route('overtime.preview') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                lembur_mulai: mulai,
                lembur_selesai: selesai,
                day_type: dayType,
                overtime_policy_id: policyId,
                nik: nik
            })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.calculation) return;

            const calc = data.calculation;
            document.getElementById('simPlannedHours').innerHTML = calc.total_duration_hours + ' <span style="font-size: 12px; font-weight: normal; color: #755841;">jam</span>';
            document.getElementById('simRateHours').innerHTML = calc.rate_hours.toFixed(2) + ' <span style="font-size: 12px; font-weight: normal; color: #15803d;">jam</span>';

            // Meal allowance
            const mealBadge = document.getElementById('simMealBadge');
            if (calc.meal_allowance_eligible) {
                mealBadge.textContent = 'Wajib Disediakan';
                mealBadge.className = 'badge';
                mealBadge.style.background = '#f0fdf4';
                mealBadge.style.color = '#15803d';
                mealBadge.style.border = '1px solid #bbf7d0';
            } else {
                mealBadge.textContent = 'Tidak';
                mealBadge.className = 'badge';
                mealBadge.style.background = '#f4f3f2';
                mealBadge.style.color = '#755841';
                mealBadge.style.border = 'none';
            }

            // Breakdown
            const tbody = document.getElementById('simBreakdownBody');
            tbody.innerHTML = '';
            calc.tiers_breakdown.forEach(tier => {
                const label = tier.to_hour ? `Jam ke-${tier.from_hour + 1} s/d ${tier.to_hour}` : `Jam ke-${tier.from_hour + 1} dst`;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="ps-3 py-1.5">${label}</td>
                    <td class="py-1.5">${tier.hours} jam</td>
                    <td class="py-1.5"><span class="badge bg-light text-dark">${tier.multiplier}x</span></td>
                    <td class="pe-3 py-1.5 text-end fw-semibold">${tier.subtotal_rate_hours.toFixed(2)} jam</td>
                `;
                tbody.appendChild(tr);
            });

            // Compliance
            const compBox = document.getElementById('simComplianceBox');
            if (data.compliance && data.compliance.warnings && data.compliance.warnings.length > 0) {
                compBox.style.display = 'block';
                compBox.className = 'alert alert-warning mb-0 border';
                compBox.style.background = '#fffbeb';
                compBox.style.borderColor = '#fde68a';
                compBox.style.color = '#b45309';
                document.getElementById('simComplianceText').innerHTML = data.compliance.warnings.join('<br>');
            } else {
                compBox.style.display = 'none';
            }
        })
        .catch(err => console.error(err));
    }

    [inputMulai, inputSelesai, selectDayType, selectPolicy, selectNik].forEach(el => {
        el.addEventListener('change', updatePreview);
    });

    // Initial trigger
    updatePreview();
});
</script>
@endpush
