@section('titlepage', 'Detail Dokumen SPK Lembur')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item"><a href="{{ route('overtime.index') }}">SPK Lembur</a></li>
    <li class="breadcrumb-item active">Detail SPK</li>
@endsection

@section('content')
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 d-print-none">
    <div>
        <a href="{{ route('overtime.index') }}" class="text-decoration-none text-muted mb-1 d-inline-flex align-items-center gap-1" style="font-size: 13px;">
            <i class="ti ti-arrow-left"></i> Kembali ke Daftar SPK
        </a>
        <h4 class="page-title mb-0">Detail Dokumen SPK Lembur</h4>
        <p class="page-subtitle mb-0">Pratinjau resmi surat perintah kerja lembur format cetak Depnaker.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-printer fs-5"></i>
            <span>Cetak SPK</span>
        </button>
        @if ($lembur->status === 'PENDING')
            @can('overtime.approve')
                <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1.5"
                    data-bs-toggle="modal" data-bs-target="#modalApproveShow">
                    <i class="ti ti-check fs-5"></i>
                    <span>Setujui SPK</span>
                </button>
            @endcan
        @endif
    </div>
</div>

    {{-- Printable SPK Document Card --}}
    <div class="card mx-auto shadow-sm" style="max-width: 820px; background: #ffffff; border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px;">
        <div class="card-body p-4 p-md-5">
            {{-- Document Header --}}
            <div class="border-bottom pb-4 mb-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif; color: var(--theme-text-primary, #0F172A); letter-spacing: -0.01em;">
                            {{ company_setting('company_name') ?? 'PRESENCE ENTERPRISE' }}
                        </h4>
                        <div class="text-muted" style="font-size: 12px;">
                            {{ company_setting('address') ?? 'Universal Indonesian HR Management System' }}
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge mb-2 px-2.5 py-1 text-uppercase" style="background: #f4f3f2; color: var(--theme-text-primary, #0F172A); font-family: 'JetBrains Mono', monospace; font-size: 12px; font-weight: 600; border: 1px solid rgba(15, 23, 42, 0.1);">
                            {{ $lembur->no_spk }}
                        </span>
                        <div>{!! $lembur->status_badge_html !!}</div>
                    </div>
                </div>

                <div class="text-center mt-3 pt-2">
                    <h5 class="fw-bold text-uppercase mb-1" style="font-family: 'Outfit', sans-serif; letter-spacing: 0.05em; color: var(--theme-text-primary, #0F172A);">
                        Surat Perintah Kerja (SPK) Lembur
                    </h5>
                    <div class="text-muted" style="font-size: 11.5px;">
                        Dasar Regulasi: PP No. 35 Tahun 2021 & Kepmenakertrans No. KEP.102/MEN/VI/2004
                    </div>
                </div>
            </div>

            {{-- 1. Data Karyawan --}}
            <div class="mb-4">
                <div class="text-uppercase fw-bold mb-2 pb-1 border-bottom" style="font-size: 11px; color: var(--theme-color-1, #0F172A); letter-spacing: 0.06em;">
                    I. Data Karyawan Yang Ditugaskan
                </div>
                <table class="table table-sm table-borderless mb-0" style="font-size: 13px;">
                    <tr>
                        <td style="width: 180px; color: #4f4540;">Nama Karyawan</td>
                        <td style="width: 15px;">:</td>
                        <td class="fw-bold" style="color: var(--theme-text-primary, #0F172A);">{{ $lembur->karyawan->nama_karyawan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="color: #4f4540;">Nomor Induk Karyawan (NIK)</td>
                        <td>:</td>
                        <td style="font-family: 'JetBrains Mono', monospace;">{{ $lembur->nik }}</td>
                    </tr>
                    <tr>
                        <td style="color: #4f4540;">Departemen / Divisi</td>
                        <td>:</td>
                        <td>{{ $lembur->karyawan->departemen->nama_dept ?? '-' }} / {{ $lembur->karyawan->division->nama_divisi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="color: #4f4540;">Jabatan</td>
                        <td>:</td>
                        <td>{{ $lembur->karyawan->jabatan->nama_jabatan ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            {{-- 2. Jadwal & Pelaksanaan --}}
            <div class="mb-4">
                <div class="text-uppercase fw-bold mb-2 pb-1 border-bottom" style="font-size: 11px; color: #755841; letter-spacing: 0.06em;">
                    II. Jadwal & Pelaksanaan Lembur
                </div>
                <table class="table table-sm table-borderless mb-0" style="font-size: 13px;">
                    <tr>
                        <td style="width: 180px; color: #4f4540;">Hari, Tanggal</td>
                        <td style="width: 15px;">:</td>
                        <td class="fw-medium">{{ $lembur->tanggal->translatedFormat('l, d F Y') }}</td>
                    </tr>
                    <tr>
                        <td style="color: #4f4540;">Klasifikasi Hari</td>
                        <td>:</td>
                        <td><span class="badge" style="background: #f4f3f2; color: #4f4540; font-size: 11px; border: 1px solid #d3c3bd;">{{ $lembur->day_type_label }}</span></td>
                    </tr>
                    <tr>
                        <td style="color: #4f4540;">Jadwal Rencana Lembur</td>
                        <td>:</td>
                        <td style="font-family: 'JetBrains Mono', monospace;">
                            {{ $lembur->lembur_mulai->format('H:i') }} - {{ $lembur->lembur_selesai->format('H:i') }} WIB ({{ $lembur->formatted_duration }})
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #4f4540;">Realisasi Presensi Lembur</td>
                        <td>:</td>
                        <td style="font-family: 'JetBrains Mono', monospace;">
                            @if ($lembur->lembur_in && $lembur->lembur_out)
                                {{ $lembur->lembur_in->format('H:i') }} - {{ $lembur->lembur_out->format('H:i') }} WIB
                                (Aktual: {{ round($lembur->actual_duration_minutes / 60, 2) }} jam)
                            @else
                                <span class="text-muted fst-italic">Belum ada catatan presensi lembur mandiri</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            {{-- 3. Uraian Pekerjaan --}}
            <div class="mb-4">
                <div class="text-uppercase fw-bold mb-2 pb-1 border-bottom" style="font-size: 11px; color: #755841; letter-spacing: 0.06em;">
                    III. Uraian Tugas & Target Lembur
                </div>
                <div class="p-3 rounded-2" style="background: #faf9f8; border: 1px solid rgba(60, 42, 33, 0.08); font-size: 13px; line-height: 1.6; color: #1a1c1c;">
                    {{ $lembur->keterangan }}
                </div>
            </div>

            {{-- 4. Perhitungan Kompensasi Depnaker --}}
            <div class="mb-5">
                <div class="text-uppercase fw-bold mb-2 pb-1 border-bottom" style="font-size: 11px; color: #755841; letter-spacing: 0.06em;">
                    IV. Formula Kompensasi Upah Lembur (PP 35/2021)
                </div>
                <div class="border rounded-2 overflow-hidden mb-2" style="border-color: rgba(60, 42, 33, 0.08) !important;">
                    <table class="table table-sm table-striped mb-0" style="font-size: 12.5px;">
                        <thead style="background: #f4f3f2;">
                            <tr>
                                <th class="ps-3 py-2">Tier Jam</th>
                                <th class="py-2">Durasi Efektif</th>
                                <th class="py-2">Faktor Pengali</th>
                                <th class="pe-3 py-2 text-end">Jam Bayar Ekuivalen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($calculation['tiers_breakdown'] as $tier)
                                <tr>
                                    <td class="ps-3 py-1.5">
                                        {{ $tier['to_hour'] ? "Jam ke-".($tier['from_hour']+1)." s/d {$tier['to_hour']}" : "Jam ke-".($tier['from_hour']+1)." dst" }}
                                    </td>
                                    <td class="py-1.5" style="font-family: 'JetBrains Mono', monospace;">{{ $tier['hours'] }} jam</td>
                                    <td class="py-1.5"><span class="badge bg-light text-dark">{{ $tier['multiplier'] }}x</span></td>
                                    <td class="pe-3 py-1.5 text-end fw-semibold" style="font-family: 'JetBrains Mono', monospace;">{{ number_format($tier['subtotal_rate_hours'], 2) }} jam</td>
                                </tr>
                            @endforeach
                            <tr style="background: #f4f3f2; border-top: 1px solid rgba(60,42,33,0.12);">
                                <td colspan="3" class="ps-3 py-2 fw-bold text-uppercase" style="font-size: 11.5px; color: #25160e;">Total Jam Bayar Kompensasi:</td>
                                <td class="pe-3 py-2 text-end fw-bold" style="font-family: 'JetBrains Mono', monospace; font-size: 13px; color: #15803d;">
                                    {{ number_format($calculation['rate_hours'], 2) }} jam
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center text-muted px-1" style="font-size: 11px;">
                    <div>Upah per jam = 1/173 x Upah Bulanan (Gaji Pokok + Tunjangan Tetap)</div>
                    @if ($calculation['meal_allowance_eligible'])
                        <div class="text-success fw-semibold"><i class="ti ti-check"></i> Wajib Konsumsi/Makan (≥1.400 kkal)</div>
                    @endif
                </div>
            </div>

            {{-- 5. Signature Boxes (3 Kolom) --}}
            <div class="pt-4 border-top">
                <div class="row text-center" style="font-size: 12.5px;">
                    <div class="col-4">
                        <div class="text-muted mb-5">Karyawan Ditugaskan,</div>
                        <div class="fw-bold mt-4" style="color: #25160e;">{{ $lembur->karyawan->nama_karyawan ?? '-' }}</div>
                        <div class="text-muted" style="font-size: 11px;">Tanggal: {{ $lembur->created_at->format('d/m/Y') }}</div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted mb-5">Atasan Langsung / SPV,</div>
                        <div class="fw-bold mt-4" style="color: #25160e;">{{ $lembur->approver->name ?? '( ...................................... )' }}</div>
                        <div class="text-muted" style="font-size: 11px;">
                            {{ $lembur->approved_at ? 'Disetujui: ' . $lembur->approved_at->format('d/m/Y') : 'Status: Menunggu' }}
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted mb-5">Mengetahui HRD,</div>
                        <div class="fw-bold mt-4" style="color: #25160e;">( Human Resources )</div>
                        <div class="text-muted" style="font-size: 11px;">Presence Universal HR</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

{{-- Approval Modal --}}
@if ($lembur->status === 'PENDING')
    <div class="modal fade" id="modalApproveShow" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('overtime.approve', $lembur->id) }}" method="POST" class="modal-content" style="border-radius: 12px;">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold" style="font-family: 'Outfit', sans-serif; color: #25160e;">
                        Setujui SPK Lembur {{ $lembur->no_spk }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 12px; font-weight: 600;">Durasi Disetujui (Menit)</label>
                        <input type="number" name="approved_duration_minutes" class="form-control"
                            value="{{ $lembur->approved_duration_minutes ?? $lembur->planned_duration_minutes }}"
                            min="1" required style="border-radius: 8px; font-family: 'JetBrains Mono', monospace;">
                    </div>
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 12px; font-weight: 600;">Catatan Approval (Opsional)</label>
                        <textarea name="notes" rows="2" class="form-control" style="border-radius: 8px;" placeholder="Catatan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn text-white" style="background: #15803d; border-radius: 8px;">
                        Konfirmasi Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
@endsection
