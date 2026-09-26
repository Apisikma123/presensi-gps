@php
    $helpPages = config('admin_help.pages', []);
@endphp

@foreach ($helpPages as $topicKey => $page)
    @php
        $topicModule = match($topicKey) {
            'presensi', 'trackingpresensi', 'harilibur', 'jamkerja', 'dispensasi', 'laporan_presensi' => 'attendance',
            'cuti', 'izinabsen', 'izinsakit', 'izincuti', 'laporan_cuti' => 'leave',
            default => null,
        };
    @endphp
    @if($topicModule && !module_enabled($topicModule))
        @continue
    @endif
    <div class="help-topic-section {{ $topicKey === 'panduan_karyawan' ? '' : 'd-none' }}" 
         id="help-topic-{{ $topicKey }}" 
         data-topic="{{ $topicKey }}"
         data-title="{{ $page['title'] ?? '' }}"
         data-subtitle="{{ $page['subtitle'] ?? 'Petunjuk operasional sistem' }}"
         data-badge="{{ $page['badge'] ?? 'Sistem' }}">
        
        {{-- 1. Header Card (Title, Category Badge, About) --}}
        <div class="help-card mb-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="badge" style="background: rgba(var(--bs-primary-rgb, 60, 42, 33), 0.08); color: var(--theme-color-1, #3C2A21); border: 1px solid rgba(var(--bs-primary-rgb, 60, 42, 33), 0.2); font-size: 10.5px; font-weight: 700; border-radius: 6px; padding: 3px 8px; text-transform: uppercase; letter-spacing: 0.04em;">
                    {{ $page['badge'] ?? 'Modul Sistem' }}
                </span>
                <small class="text-muted font-mono" style="font-size: 11px;">Presensi GPS</small>
            </div>
            <h5 class="fw-bold text-dark mb-2" style="font-size: 16px; letter-spacing: -0.01em;">
                {{ $page['title'] ?? '' }}
            </h5>
            @if (!empty($page['about']))
                <p class="text-muted mb-0" style="font-size: 12.5px; line-height: 1.6; color: #475569 !important;">
                    {{ $page['about'] }}
                </p>
            @endif
        </div>

        {{-- 2. Yang Bisa Dilakukan --}}
        @if (!empty($page['actions']) && count($page['actions']) > 0)
            <div class="help-card">
                <div class="help-card-label">
                    <i class="ti ti-list-check"></i>
                    <span>Poin Penting & Yang Bisa Dilakukan</span>
                </div>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size: 12.5px;">
                    @foreach ($page['actions'] as $action)
                        <li class="d-flex align-items-start gap-2 text-dark" style="line-height: 1.45;">
                            <i class="ti ti-check flex-shrink-0" style="color: var(--theme-color-1, #3C2A21); font-size: 15px; margin-top: 2px;"></i>
                            <span style="color: #334155;">{{ $action }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 3. Cara Menggunakan (Langkah-Langkah) --}}
        @if (!empty($page['steps']) && count($page['steps']) > 0)
            <div class="help-card">
                <div class="help-card-label">
                    <i class="ti ti-compass"></i>
                    <span>Langkah-Langkah Penggunaan</span>
                </div>
                <div class="d-flex flex-column gap-2.5">
                    @foreach ($page['steps'] as $idx => $step)
                        <div class="d-flex align-items-start gap-2.5">
                            <span class="help-step-number">{{ $idx + 1 }}</span>
                            <div class="text-dark" style="font-size: 12.5px; line-height: 1.55; padding-top: 1px; color: #334155 !important;">
                                {{ $step }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- 4. Penjelasan Bagian & Ketentuan --}}
        @if (!empty($page['components']) && count($page['components']) > 0)
            <div class="help-card">
                <div class="help-card-label">
                    <i class="ti ti-layers-subtract"></i>
                    <span>Penjelasan Bagian & Ketentuan</span>
                </div>
                <div>
                    @foreach ($page['components'] as $cName => $cDesc)
                        <div class="help-comp-box">
                            <div class="help-comp-title">
                                <span class="help-comp-dot"></span>
                                <span>{{ $cName }}</span>
                            </div>
                            <p class="help-comp-text">{{ $cDesc }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- 5. Tips Praktis Lapangan --}}
        @if (!empty($page['tips']) && count($page['tips']) > 0)
            <div class="help-box-tip">
                <div class="tip-label">
                    <i class="ti ti-bulb" style="color: var(--theme-color-1, #3C2A21); font-size: 15px;"></i>
                    <span>Tips Praktis Lapangan</span>
                </div>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-1.5" style="font-size: 12px;">
                    @foreach ($page['tips'] as $tip)
                        <li class="d-flex align-items-start gap-2" style="line-height: 1.45; color: #1E293B;">
                            <i class="ti ti-point flex-shrink-0" style="color: var(--theme-color-1, #3C2A21); font-size: 15px; margin-top: 1px;"></i>
                            <span>{{ $tip }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 6. Perhatian / Tindakan Sensitif --}}
        @if (!empty($page['warnings']))
            <div class="help-box-warning">
                <div class="warning-label">
                    <i class="ti ti-alert-triangle" style="font-size: 15px; color: #D97706;"></i>
                    <span>PENTING & HARAP DIPERHATIKAN</span>
                </div>
                <p class="mb-0" style="font-size: 12px; line-height: 1.55; color: #78350F !important;">
                    {{ $page['warnings'] }}
                </p>
            </div>
        @endif
    </div>
@endforeach
