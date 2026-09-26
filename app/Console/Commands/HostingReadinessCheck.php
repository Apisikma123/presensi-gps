<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\HostingReadinessService;

class HostingReadinessCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hosting:readiness {--json : Output results in secure JSON format}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit real server & hosting environment readiness for HR Coffee Shop (2 Branches)';

    /**
     * Execute the console command.
     */
    public function handle(HostingReadinessService $service)
    {
        $data = $service->runAudit();

        if ($this->option('json')) {
            $this->line(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return 0;
        }

        $this->renderCliOutput($data);
        return 0;
    }

    /**
     * Helper to render formatted CLI output.
     */
    protected function renderCliOutput(array $data): void
    {
        $this->newLine();
        $this->line('<fg=bright-cyan;options=bold>========================================================================</>');
        $this->line('<fg=bright-white;options=bold>   RUMAHWEB HOSTING READINESS AUDIT (HR COFFEE SHOP 2 CABANG)           </>');
        $this->line('<fg=bright-cyan;options=bold>========================================================================</>');
        $this->newLine();

        // 1. Core Services Status
        $this->line('<fg=yellow;options=bold>--- 1. CORE SYSTEM HEALTH ---</>');
        $this->renderDotLine('PHP Environment', $data['php']['status']);
        $this->renderDotLine('MySQL Connection', $data['database']['status']);
        $this->renderDotLine('Storage Filesystem', $data['storage']['status']);
        $this->renderDotLine('OPcache Engine', $data['opcache']['status']);
        $this->renderDotLine('Laravel Production Mode', $data['laravel']['status']);
        $this->renderDotLine('Scheduler & Auto-Alpha', $data['scheduler']['status']);
        $this->newLine();

        // 2. Real Metrics
        $this->line('<fg=yellow;options=bold>--- 2. MEASURED SERVER METRICS (NO INVENTED DATA) ---</>');
        $this->renderDotLine('PHP Version', $data['php']['version']);
        $this->renderDotLine('PHP Memory Limit', $data['php']['memory_limit']);
        $this->renderDotLine('PHP Max Execution', $data['php']['max_execution_time']);
        $this->renderDotLine('PHP Upload Max Filesize', $data['php']['upload_max_filesize']);
        $this->renderDotLine('PHP Post Max Size', $data['php']['post_max_size']);
        $this->renderDotLine('Storage Write Speed', $data['storage']['write_speed_mb_s'] . ' MB/s');
        $this->renderDotLine('Storage Read Speed', $data['storage']['read_speed_mb_s'] . ' MB/s');
        $this->renderDotLine('Database Ping Latency', $data['database']['ping_latency_ms'] . ' ms');
        $this->renderDotLine('OPcache Hit Rate', $data['opcache']['hit_rate']);
        $this->renderDotLine('Disk Free Space', $data['disk']['disk_free']);
        $this->renderDotLine('Scheduler Heartbeat', $data['scheduler']['last_heartbeat']);
        $this->newLine();

        // 3. Application Performance
        $this->line('<fg=yellow;options=bold>--- 3. APPLICATION BENCHMARK (READ-ONLY) ---</>');
        if (!empty($data['application']['queries'])) {
            foreach ($data['application']['queries'] as $name => $ms) {
                $label = ucwords(str_replace(['_', 'ms'], [' ', ''], $name));
                $this->renderDotLine($label, $ms . ' ms');
            }
        } else {
            $this->line('  <fg=gray>Benchmark skipped / Database not initialized.</>');
        }
        $this->newLine();

        // 4. CloudLinux / cPanel Manual Checklist
        $this->line('<fg=yellow;options=bold>--- 4. CLOUDLINUX / CPANEL MANUAL CHECKLIST (CAGEFS RESTRICTED) ---</>');
        foreach ($data['cpanel_checklist'] as $key => $val) {
            $this->renderDotLine($key, 'CHECK CPANEL', 'yellow');
        }
        $this->newLine();

        // 5. Capacity Profile
        $this->line('<fg=yellow;options=bold>--- 5. 2-BRANCH CAPACITY PROFILE ---</>');
        $this->line("  <fg=white>Target Scope       :</> 2 Cabang (CS1 & JKT/MDN), ~30 Karyawan total (~15/cabang)");
        $this->line("  <fg=white>Estimated Peak     :</> 11 simultaneous requests (5 Branch A + 5 Branch B + 1 Admin)");
        $this->line("  <fg=white>Hosting Recom.     :</> Min 20 Entry Processes (EP) di cPanel agar tidak terkena HTTP 508");
        $this->newLine();

        // 6. Verdict Banner
        $this->line('<fg=bright-cyan;options=bold>========================================================================</>');
        $verdict = $data['verdict'];
        if ($verdict === 'READY FOR REAL LOAD TEST') {
            $this->line("  <fg=bright-white;options=bold>FINAL VERDICT : </><fg=bright-green;options=bold>[ READY FOR REAL LOAD TEST ]</>");
            $this->line("  <fg=green>Sistem siap untuk pengujian beban eksternal (external network concurrency test).</>");
        } elseif ($verdict === 'READY WITH WARNING') {
            $this->line("  <fg=bright-white;options=bold>FINAL VERDICT : </><fg=bright-yellow;options=bold>[ READY WITH WARNING ]</>");
            $this->line("  <fg=yellow>Sistem dapat berjalan, namun periksa item peringatan sebelum load testing.</>");
        } else {
            $this->line("  <fg=bright-white;options=bold>FINAL VERDICT : </><fg=bright-red;options=bold>[ NOT READY ]</>");
            $this->line("  <fg=red>Terdapat kegagalan pada dependensi kritis (PHP / Database / Storage).</>");
        }
        $this->line('<fg=bright-cyan;options=bold>========================================================================</>');
        $this->newLine();
    }

    /**
     * Helper to render aligned dot-leader lines.
     */
    protected function renderDotLine(string $label, string $value, string $color = null): void
    {
        $dots = str_repeat('.', max(2, 45 - strlen($label)));

        if (!$color) {
            if ($value === 'PASS' || $value === 'NORMAL') {
                $color = 'bright-green';
            } elseif ($value === 'WARNING') {
                $color = 'bright-yellow';
            } elseif ($value === 'FAIL' || $value === 'BERMASALAH') {
                $color = 'bright-red';
            } else {
                $color = 'bright-white';
            }
        }

        $this->line("  <fg=white>{$label}</> <fg=gray>{$dots}</> <fg={$color};options=bold>{$value}</>");
    }
}
