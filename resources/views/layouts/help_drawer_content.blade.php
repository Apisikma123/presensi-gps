@php
    $helpPages = config('admin_help.pages', []);
@endphp

@foreach ($helpPages as $topicKey => $page)
    @php
        $topicModule = match($topicKey) {
            'presensi', 'trackingpresensi', 'harilibur', 'jamkerja', 'dispensasi', 'laporan_presensi' => 'attendance',
            'cuti', 'izinabsen', 'izinsakit', 'izincuti', 'laporan_cuti' => 'leave',
            'lembur' => 'overtime',
            'payroll' => 'payroll',
            'loan' => 'loans',
            'reimbursement' => 'reimbursement',
            'warning' => 'warning',
            'recruitment' => 'recruitment',
            'onboarding' => 'onboarding',
            'performance' => 'performance',
            'offboarding' => 'resignation',
            default => null,
        };
    @endphp
    @if($topicModule && !module_enabled($topicModule))
        @continue
    @endif
    <div class="help-topic-section {{ $topicKey === 'panduan_admin_roster' ? '' : 'd-none' }}" 
         id="help-topic-{{ $topicKey }}" 
         data-topic="{{ $topicKey }}"
         data-title="{{ $page['title'] ?? '' }}"
         data-subtitle="{{ $page['subtitle'] ?? 'Petunjuk operasional sistem' }}"
         data-badge="{{ $page['badge'] ?? 'Sistem' }}">
        
        {{-- 1. Kartu Header: Ringkasan 1 Kalimat Inti --}}
        <div class="help-card mb-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="badge" style="background: rgba(var(--bs-primary-rgb, 60, 42, 33), 0.08); color: var(--theme-color-1, #3C2A21); border: 1px solid rgba(var(--bs-primary-rgb, 60, 42, 33), 0.2); font-size: 10.5px; font-weight: 700; border-radius: 6px; padding: 3px 8px; text-transform: uppercase; letter-spacing: 0.04em;">
                    {{ $page['badge'] ?? 'Modul Sistem' }}
                </span>
                <small class="text-muted font-mono" style="font-size: 11px;">Presensi GPS</small>
            </div>
            <h5 class="fw-bold text-dark mb-1" style="font-size: 15.5px; letter-spacing: -0.01em;">
                {{ $page['title'] ?? '' }}
            </h5>
            @if (!empty($page['about']))
                <p class="mb-0" style="font-size: 12.5px; line-height: 1.5; color: #475569;">
                    {{ $page['about'] }}
                </p>
            @endif
        </div>

        {{-- 2. Langkah Cepat (3-4 Langkah Aksi Langsung) --}}
        @if (!empty($page['steps']) && count($page['steps']) > 0)
            <div class="help-card">
                <div class="help-card-label">
                    <i class="ti ti-checklist"></i>
                    <span>Cara Cepat Pakai</span>
                </div>
                <div class="d-flex flex-column gap-2">
                    @foreach ($page['steps'] as $idx => $step)
                        <div class="d-flex align-items-start gap-2.5">
                            <span class="help-step-number">{{ $idx + 1 }}</span>
                            <div style="font-size: 12.5px; line-height: 1.5; color: #334155;">
                                {!! $step !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- 3. Poin Penting & Aturan Kritis --}}
        @if (!empty($page['components']) && count($page['components']) > 0)
            <div class="help-card">
                <div class="help-card-label">
                    <i class="ti ti-info-circle"></i>
                    <span>Poin Penting & Aturan</span>
                </div>
                <div class="d-flex flex-column gap-1.5">
                    @foreach ($page['components'] as $cName => $cDesc)
                        <div class="p-2 rounded-2" style="background: #F8FAFC; border: 1px solid #E2E8F0; font-size: 12px; line-height: 1.45;">
                            <strong class="text-dark">{{ $cName }}:</strong>
                            <span style="color: #475569;">{{ $cDesc }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- 4. Tips Praktis Lapangan --}}
        @if (!empty($page['tips']) && count($page['tips']) > 0)
            <div class="help-box-tip mb-3">
                <div class="tip-label">
                    <i class="ti ti-bulb" style="color: var(--theme-color-1, #3C2A21); font-size: 14px;"></i>
                    <span>Tips Praktis</span>
                </div>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-1" style="font-size: 12px;">
                    @foreach ($page['tips'] as $tip)
                        <li class="d-flex align-items-start gap-2" style="line-height: 1.45; color: #334155;">
                            <span style="color: var(--theme-color-1, #3C2A21); font-weight: bold; margin-top: -1px;">•</span>
                            <span>{{ $tip }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 5. Perhatian / Tindakan Sensitif --}}
        @if (!empty($page['warnings']))
            <div class="help-box-warning">
                <div class="warning-label">
                    <i class="ti ti-alert-triangle" style="font-size: 14px; color: #D97706;"></i>
                    <span>Perhatian</span>
                </div>
                <p class="mb-0" style="font-size: 12px; line-height: 1.5; color: #78350F !important;">
                    {{ $page['warnings'] }}
                </p>
            </div>
        @endif
    </div>
@endforeach
