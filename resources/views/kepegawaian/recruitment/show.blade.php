@extends('layouts.app')
@section('titlepage', 'Pipeline Rekrutmen')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item"><a href="{{ route('recruitment.index') }}">Rekrutmen</a></li>
    <li class="breadcrumb-item active">Pipeline Seleksi</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <a href="{{ route('recruitment.index') }}" class="text-decoration-none text-muted mb-1 d-inline-flex align-items-center gap-1" style="font-size: 13px;">
            <i class="ti ti-arrow-left"></i> Kembali ke Daftar Lowongan
        </a>
        <h4 class="page-title mb-1">
            {{ $vacancy->title }}
            <span class="font-mono text-muted fs-5 ms-2">[{{ $vacancy->vacancy_code }}]</span>
        </h4>
        <p class="page-subtitle text-muted mb-0">Pipeline tracking kandidat pelamar dan tahapan seleksi rekrutmen kerja.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('recruitment.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i>
            <span>Kembali</span>
        </a>
        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalAddCandidate">
            <i class="ti ti-user-plus"></i>
            <span>Daftarkan Pelamar</span>
        </button>
    </div>
</div>

<!-- Vacancy Detail Info Strip -->
<div class="card mb-4" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
    <div class="card-body p-3">
        <div class="row g-3 align-items-center">
            <div class="col-md-3">
                <div class="text-muted small text-uppercase" style="font-size: 11px;">Departemen / Cabang</div>
                <div class="fw-bold text-dark">{{ $vacancy->department->nama_dept ?? 'Semua Departemen' }}</div>
                <div class="text-muted small">{{ $vacancy->branch->nama_cabang ?? 'Kantor Pusat' }}</div>
            </div>
            <div class="col-md-2">
                <div class="text-muted small text-uppercase" style="font-size: 11px;">Hubungan Kerja</div>
                <div><span class="badge bg-light text-dark fw-bold">{{ $vacancy->employment_type }}</span></div>
            </div>
            <div class="col-md-2">
                <div class="text-muted small text-uppercase" style="font-size: 11px;">Kuota Formasi</div>
                <div class="fw-bold font-mono">{{ $vacancy->quota }} Posisi</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small text-uppercase" style="font-size: 11px;">Rentang Gaji Ditawarkan</div>
                <div class="fw-bold font-mono">
                    @if($vacancy->salary_min || $vacancy->salary_max)
                        Rp {{ number_format($vacancy->salary_min, 0, ',', '.') }} - {{ number_format($vacancy->salary_max, 0, ',', '.') }}
                    @else
                        <span class="text-muted">Kompetitif</span>
                    @endif
                </div>
            </div>
            <div class="col-md-2 text-md-end">
                <div class="text-muted small text-uppercase" style="font-size: 11px;">Status Formasi</div>
                <div>{!! $vacancy->status_badge_html !!}</div>
            </div>
        </div>
    </div>
</div>

<!-- Pipeline Stages (Horizontal Kanban) -->
<div class="row g-3 flex-nowrap overflow-auto pb-3">
    @php
        $stagesConfig = [
            'APPLIED' => ['title' => 'Lamaran Masuk', 'badge' => 'bg-label-secondary', 'icon' => 'ti-inbox'],
            'SCREENING' => ['title' => 'Screening CV', 'badge' => 'bg-label-warning', 'icon' => 'ti-filter'],
            'INTERVIEW' => ['title' => 'Wawancara', 'badge' => 'bg-label-info', 'icon' => 'ti-calendar-event'],
            'OFFERING' => ['title' => 'Offering Letter', 'badge' => 'bg-label-primary', 'icon' => 'ti-mail-forward'],
            'HIRED' => ['title' => 'Diterima (Hired)', 'badge' => 'bg-label-success', 'icon' => 'ti-user-check'],
        ];
    @endphp

    @foreach($stagesConfig as $stageKey => $conf)
    @php
        $candidates = $candidatesByStage[$stageKey] ?? collect();
    @endphp
    <div class="col-12 col-md-4 col-lg-3" style="min-width: 290px; max-width: 320px;">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background-color: #f8fafc; min-height: 520px;">
            <div class="card-header bg-white border-bottom py-2.5 px-3 d-flex justify-content-between align-items-center" style="border-radius: 12px 12px 0 0;">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge {{ $conf['badge'] }} p-1.5 rounded"><i class="ti {{ $conf['icon'] }}"></i></span>
                    <span class="fw-bold text-dark small" style="letter-spacing: 0.02em;">{{ $conf['title'] }}</span>
                </div>
                <span class="badge bg-light text-muted font-mono">{{ $candidates->count() }}</span>
            </div>

            <div class="card-body p-2.5 d-flex flex-column gap-2" style="overflow-y: auto; max-height: 650px;">
                @forelse($candidates as $c)
                <div class="card bg-white p-3 shadow-none" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 10px;">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <span class="font-mono text-muted" style="font-size: 0.75rem;">{{ $c->candidate_code }}</span>
                        <span class="badge bg-light text-dark" style="font-size: 0.7rem;">{{ $c->gender === 'L' ? 'Pria' : 'Wanita' }}</span>
                    </div>
                    <div class="fw-bold text-dark fs-6 mb-1">{{ $c->name }}</div>
                    <div class="text-muted small mb-2" style="font-size: 12px;">
                        <div><i class="ti ti-mail me-1"></i>{{ $c->email }}</div>
                        @if($c->phone)
                            <div><i class="ti ti-phone me-1"></i>{{ $c->phone }}</div>
                        @endif
                    </div>

                    @if($c->expected_salary)
                    <div class="small text-muted mb-2" style="font-size: 12px;">
                        Ekspektasi: <span class="fw-bold text-dark font-mono">Rp {{ number_format($c->expected_salary, 0, ',', '.') }}</span>
                    </div>
                    @endif

                    @if($c->stage === 'INTERVIEW' && $c->interview_scheduled_at)
                    <div class="alert alert-info py-1.5 px-2 small mb-2" style="border-radius: 6px;">
                        <i class="ti ti-clock me-1"></i> {{ $c->interview_scheduled_at->format('d M Y H:i') }}
                        @if($c->interview_notes)
                            <div class="text-muted mt-1" style="font-size: 0.75rem;">{{ Str::limit($c->interview_notes, 60) }}</div>
                        @endif
                    </div>
                    @endif

                    @if($c->stage === 'OFFERING' && $c->offered_salary)
                    <div class="alert alert-primary py-1.5 px-2 small mb-2" style="border-radius: 6px;">
                        <i class="ti ti-award me-1"></i> Penawaran: <span class="fw-bold font-mono">Rp {{ number_format($c->offered_salary, 0, ',', '.') }}</span>
                    </div>
                    @endif

                    @if($c->stage === 'HIRED' && $c->hired_nik)
                    <div class="alert alert-success py-1.5 px-2 small mb-2" style="border-radius: 6px;">
                        <i class="ti ti-id me-1"></i> NIK: <span class="fw-bold font-mono">{{ $c->hired_nik }}</span>
                    </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-auto">
                        @if($c->resume_file)
                            <a href="{{ Storage::url($c->resume_file) }}" target="_blank" class="btn btn-sm btn-outline-secondary py-0.5 px-2" title="Unduh CV/Resume" style="font-size: 11px;">
                                <i class="ti ti-file-text me-1"></i> CV
                            </a>
                        @else
                            <span></span>
                        @endif

                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle py-0.5 px-2" type="button" data-bs-toggle="dropdown" style="font-size: 11px;">
                                Pindah Tahap
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow-sm">
                                @if($stageKey !== 'SCREENING')
                                    <form action="{{ route('recruitment.candidate.stage', $c->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="stage" value="SCREENING">
                                        <button type="submit" class="dropdown-item">Screening CV</button>
                                    </form>
                                @endif
                                @if($stageKey !== 'INTERVIEW')
                                    <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalInterview{{ $c->id }}">
                                        Jadwalkan Wawancara...
                                    </a>
                                @endif
                                @if($stageKey !== 'OFFERING')
                                    <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalOffering{{ $c->id }}">
                                        Kirim Offering Letter...
                                    </a>
                                @endif
                                @if($stageKey !== 'HIRED')
                                    <a href="{{ route('recruitment.hire.form', $c->id) }}" class="dropdown-item text-success fw-bold">
                                        <i class="ti ti-user-check me-1"></i> Angkat Jadi Karyawan
                                    </a>
                                @endif
                                <div class="dropdown-divider"></div>
                                <form action="{{ route('recruitment.candidate.stage', $c->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="stage" value="REJECTED">
                                    <button type="button" class="dropdown-item text-danger btn-confirm-action" data-title="Tolak Kandidat?" data-message="Apakah Anda yakin ingin menolak kandidat pelamar ini?" data-destructive="true">
                                        <i class="ti ti-x me-1"></i> Tolak Pelamar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Interview --}}
                <div class="modal fade" id="modalInterview{{ $c->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content" style="border-radius: 12px;">
                            <form action="{{ route('recruitment.candidate.stage', $c->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="stage" value="INTERVIEW">
                                <div class="modal-header border-bottom py-3">
                                    <h5 class="modal-title fw-bold" style="color: #1e293b;">Jadwalkan Wawancara: {{ $c->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body py-3">
                                    <div class="mb-3">
                                        <label class="form-label required fw-semibold" style="font-size: 12px;">Waktu Wawancara</label>
                                        <input type="datetime-local" name="interview_scheduled_at" class="form-control" required style="border-radius: 8px;">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" style="font-size: 12px;">Catatan / Link Interview</label>
                                        <textarea name="interview_notes" rows="3" class="form-control" placeholder="Ruang meeting atau link Google Meet..." style="border-radius: 8px;"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-top py-2.5">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                                    <button type="submit" class="btn btn-primary" style="border-radius: 8px;">Simpan Jadwal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Modal Offering --}}
                <div class="modal fade" id="modalOffering{{ $c->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content" style="border-radius: 12px;">
                            <form action="{{ route('recruitment.candidate.stage', $c->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="stage" value="OFFERING">
                                <div class="modal-header border-bottom py-3">
                                    <h5 class="modal-title fw-bold" style="color: #1e293b;">Offering Letter: {{ $c->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body py-3">
                                    <div class="mb-3">
                                        <label class="form-label required fw-semibold" style="font-size: 12px;">Besaran Gaji yang Ditawarkan (Rp)</label>
                                        <input type="number" name="offered_salary" class="form-control font-mono" placeholder="Contoh: 5500000" value="{{ $c->expected_salary }}" required style="border-radius: 8px;">
                                    </div>
                                </div>
                                <div class="modal-footer border-top py-2.5">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                                    <button type="submit" class="btn btn-primary" style="border-radius: 8px;">Kirim Penawaran</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @empty
                <div class="text-center py-4 text-muted small">
                    Tidak ada kandidat di tahap ini
                </div>
                @endforelse
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Modal Add Candidate --}}
<div class="modal fade" id="modalAddCandidate" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px;">
            <form action="{{ route('recruitment.candidate.store', $vacancy->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold" style="color: #1e293b;">Daftarkan Pelamar Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label required fw-semibold" style="font-size: 12px;">Nama Lengkap Pelamar</label>
                            <input type="text" name="name" class="form-control" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required fw-semibold" style="font-size: 12px;">Email</label>
                            <input type="email" name="email" class="form-control" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size: 12px;">Nomor Telepon / WhatsApp</label>
                            <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx" style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required fw-semibold" style="font-size: 12px;">Jenis Kelamin</label>
                            <select name="gender" class="form-select" required style="border-radius: 8px;">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size: 12px;">Ekspektasi Gaji (Rp)</label>
                            <input type="number" name="expected_salary" class="form-control font-mono" placeholder="5000000" style="border-radius: 8px;">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size: 12px;">Unggah CV / Resume (PDF / DOCX)</label>
                            <input type="file" name="resume_file" class="form-control" accept=".pdf,.doc,.docx" style="border-radius: 8px;">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size: 12px;">Link Portofolio / LinkedIn</label>
                            <input type="url" name="portfolio_url" class="form-control" placeholder="https://..." style="border-radius: 8px;">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size: 12px;">Catatan Tambahan</label>
                            <textarea name="notes" rows="2" class="form-control" style="border-radius: 8px;"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 8px;">Simpan Pelamar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
