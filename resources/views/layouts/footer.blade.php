<footer class="content-footer footer bg-footer-theme py-3 mt-4" style="border-top: 1px solid rgba(15, 23, 42, 0.06); font-size: 12.5px;">
    <div class="container-xxl">
        <div class="footer-container d-flex align-items-center justify-content-between flex-md-row flex-column gap-2 text-muted">
            <div class="d-flex align-items-center gap-3">
                <span>© {{ date('Y') }} <span class="fw-bold text-dark">{{ $general_setting->nama_aplikasi ?? 'E-Presensi GPS' }}</span>. All rights reserved.</span>
                @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->hasRole('admin') || auth()->user()->can('presensi.index')))
                    @php
                        $lastHeartbeat = \Illuminate\Support\Facades\Cache::get('scheduler_last_heartbeat');
                        if ($lastHeartbeat) {
                            $diffMinutes = max(0, round((now()->timestamp - $lastHeartbeat) / 60));
                            if ($diffMinutes < 60) {
                                $badgeClass = 'bg-label-success text-success';
                                $statusText = 'Normal (' . $diffMinutes . 'm lalu)';
                            } elseif ($diffMinutes <= 120) {
                                $badgeClass = 'bg-label-warning text-warning';
                                $statusText = 'Warning (' . $diffMinutes . 'm lalu)';
                            } else {
                                $badgeClass = 'bg-label-danger text-danger';
                                $statusText = 'Bermasalah (>2 jam)';
                            }
                        } else {
                            $badgeClass = 'bg-label-secondary text-muted';
                            $statusText = 'Belum Aktif';
                        }
                    @endphp
                    <span class="d-none d-sm-inline-flex align-items-center gap-1 badge {{ $badgeClass }}" style="font-size: 10px; font-weight: 600; padding: 2px 7px;" title="Status Scheduler Cron Terakhir">
                        <i class="ti ti-activity" style="font-size: 11px;"></i> Cron: {{ $statusText }}
                    </span>
                @endif
            </div>
            <div>
                Developed by <a href="https://porto-aga.vercel.app/" target="_blank" rel="noopener noreferrer" class="fw-semibold text-decoration-none" style="color: var(--theme-color-1, #3C2A21);">Muhammad Aga Putra</a>
            </div>
        </div>
    </div>
</footer>
