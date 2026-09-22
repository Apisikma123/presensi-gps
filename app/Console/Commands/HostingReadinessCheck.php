<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schedule;
use App\Models\Cabang;
use App\Models\Karyawan;
use App\Models\Presensi;

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
    public function handle()
    {
        $data = [];

        // 1. PHP Environment
        $data['php'] = $this->checkPhpEnvironment();

        // 2. OPcache
        $data['opcache'] = $this->checkOpcache();

        // 3. Laravel Production Setup
        $data['laravel'] = $this->checkLaravelSetup();

        // 4. Database Connection & Latency
        $data['database'] = $this->checkDatabase();

        // 5. Storage & Filesystem I/O
        $data['storage'] = $this->checkStorage();

        // 6. Disk & Inode
        $data['disk'] = $this->checkDisk();

        // 7. Scheduler & Cron
        $data['scheduler'] = $this->checkScheduler();

        // 8. Application Read-Only Queries
        $data['application'] = $this->checkApplicationReadiness();

        // 9. 2-Branch Capacity Profile
        $data['capacity_profile'] = $this->evaluateCapacityProfile($data);

        // 10. cPanel Manual Checklist
        $data['cpanel_checklist'] = $this->getCpanelChecklist();

        // 11. Final Verdict
        $data['verdict'] = $this->determineVerdict($data);

        if ($this->option('json')) {
            $this->line(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return 0;
        }

        $this->renderCliOutput($data);
        return 0;
    }

    /**
     * Check PHP version, limits, and extensions.
     */
    protected function checkPhpEnvironment(): array
    {
        $version = PHP_VERSION;
        $versionOk = version_compare($version, '8.2.0', '>=');
        $versionStatus = $versionOk ? 'PASS' : (version_compare($version, '8.1.0', '>=') ? 'WARNING' : 'FAIL');

        $memoryLimit = ini_get('memory_limit');
        $memoryBytes = $this->convertToBytes($memoryLimit);
        $memoryStatus = ($memoryBytes === -1 || $memoryBytes >= 268435456) ? 'PASS' : ($memoryBytes >= 134217728 ? 'WARNING' : 'FAIL');

        $maxExecution = (int) ini_get('max_execution_time');
        $execStatus = ($maxExecution === 0 || $maxExecution >= 60) ? 'PASS' : ($maxExecution >= 30 ? 'WARNING' : 'FAIL');

        $maxInputTime = ini_get('max_input_time');
        $uploadMax = ini_get('upload_max_filesize');
        $postMax = ini_get('post_max_size');

        $extensions = [
            'pdo_mysql' => extension_loaded('pdo_mysql'),
            'mbstring'  => extension_loaded('mbstring'),
            'openssl'   => extension_loaded('openssl'),
            'fileinfo'  => extension_loaded('fileinfo'),
            'gd'        => extension_loaded('gd'),
            'curl'      => extension_loaded('curl'),
            'zip'       => extension_loaded('zip'),
            'intl'      => extension_loaded('intl'),
            'opcache'   => extension_loaded('Zend OPcache'),
        ];

        $webpSupport = false;
        if ($extensions['gd']) {
            $gdInfo = function_exists('gd_info') ? gd_info() : [];
            $webpSupport = !empty($gdInfo['WebP Support']);
        }

        $missingRequired = [];
        foreach (['pdo_mysql', 'mbstring', 'openssl', 'fileinfo', 'gd', 'curl', 'zip'] as $ext) {
            if (!$extensions[$ext]) {
                $missingRequired[] = $ext;
            }
        }

        $overallStatus = 'PASS';
        if (!empty($missingRequired) || $versionStatus === 'FAIL') {
            $overallStatus = 'FAIL';
        } elseif ($versionStatus === 'WARNING' || $memoryStatus === 'WARNING' || $execStatus === 'WARNING' || !$webpSupport) {
            $overallStatus = 'WARNING';
        }

        return [
            'status'               => $overallStatus,
            'version'              => $version,
            'version_status'       => $versionStatus,
            'memory_limit'         => $memoryLimit,
            'memory_status'        => $memoryStatus,
            'max_execution_time'   => $maxExecution . 's',
            'execution_status'     => $execStatus,
            'max_input_time'       => $maxInputTime . 's',
            'upload_max_filesize'  => $uploadMax,
            'post_max_size'        => $postMax,
            'extensions'           => $extensions,
            'webp_support'         => $webpSupport,
            'missing_required'     => $missingRequired,
        ];
    }

    /**
     * Safely read OPcache status without crashing if restricted.
     */
    protected function checkOpcache(): array
    {
        if (!extension_loaded('Zend OPcache') || !ini_get('opcache.enable')) {
            return [
                'status'        => 'WARNING',
                'enabled'       => false,
                'note'          => 'OPcache disabled in php.ini. Highly recommended for production performance.',
                'memory_used'   => 'N/A',
                'memory_free'   => 'N/A',
                'hit_rate'      => 'N/A',
            ];
        }

        try {
            if (!function_exists('opcache_get_status')) {
                return [
                    'status'      => 'PASS',
                    'enabled'     => true,
                    'note'        => 'OPcache active (opcache_get_status restricted by hosting).',
                    'memory_used' => 'Check cPanel',
                    'memory_free' => 'Check cPanel',
                    'hit_rate'    => 'Check cPanel',
                ];
            }

            $status = @opcache_get_status(false);
            if (!is_array($status)) {
                return [
                    'status'      => 'PASS',
                    'enabled'     => true,
                    'note'        => 'OPcache enabled (status metrics restricted by CageFS).',
                    'memory_used' => 'Restricted',
                    'memory_free' => 'Restricted',
                    'hit_rate'    => 'Restricted',
                ];
            }

            $usedMb = isset($status['memory_usage']['used_memory'])
                ? round($status['memory_usage']['used_memory'] / 1048576, 2) . ' MB'
                : 'N/A';
            $freeMb = isset($status['memory_usage']['free_memory'])
                ? round($status['memory_usage']['free_memory'] / 1048576, 2) . ' MB'
                : 'N/A';
            $hitRate = isset($status['opcache_statistics']['opcache_hit_rate'])
                ? round($status['opcache_statistics']['opcache_hit_rate'], 1) . '%'
                : 'N/A';

            return [
                'status'      => 'PASS',
                'enabled'     => true,
                'memory_used' => $usedMb,
                'memory_free' => $freeMb,
                'hit_rate'    => $hitRate,
                'note'        => 'OPcache fully operational.',
            ];
        } catch (\Throwable $e) {
            return [
                'status'      => 'WARNING',
                'enabled'     => true,
                'note'        => 'OPcache enabled but metrics unreadable: ' . $e->getMessage(),
                'memory_used' => 'N/A',
                'memory_free' => 'N/A',
                'hit_rate'    => 'N/A',
            ];
        }
    }

    /**
     * Check Laravel production caching, environment flags, and Vite build.
     */
    protected function checkLaravelSetup(): array
    {
        $appEnv = config('app.env');
        $appDebug = config('app.debug');
        $appUrl = config('app.url');

        $configCached = app()->configurationIsCached();
        $routesCached = app()->routesAreCached();
        
        $viewFiles = glob(storage_path('framework/views/*.php'));
        $viewsCached = !empty($viewFiles);

        $hotExists = File::exists(public_path('hot'));
        $manifestExists = File::exists(public_path('build/manifest.json'));

        $isProduction = ($appEnv === 'production' && $appDebug === false);
        $status = ($isProduction && $manifestExists && !$hotExists) ? 'PASS' : 'WARNING';

        return [
            'status'          => $status,
            'env'             => $appEnv,
            'debug'           => $appDebug,
            'url'             => $appUrl,
            'config_cached'   => $configCached,
            'routes_cached'   => $routesCached,
            'views_cached'    => $viewsCached,
            'hot_file_absent' => !$hotExists,
            'vite_manifest'   => $manifestExists,
        ];
    }

    /**
     * Test database connection, read limits, and query latency.
     */
    protected function checkDatabase(): array
    {
        try {
            $startPing = microtime(true);
            DB::selectOne('SELECT 1');
            $pingMs = round((microtime(true) - $startPing) * 1000, 2);

            $versionRow = DB::selectOne('SELECT VERSION() as ver');
            $version = $versionRow ? $versionRow->ver : 'Unknown';

            $maxConn = 'NOT AVAILABLE / CHECK CPANEL MANUALLY';
            $maxUserConn = 'NOT AVAILABLE / CHECK CPANEL MANUALLY';

            try {
                $maxConnRow = DB::selectOne("SHOW VARIABLES LIKE 'max_connections'");
                if ($maxConnRow && isset($maxConnRow->Value)) {
                    $maxConn = $maxConnRow->Value;
                }
                $maxUserConnRow = DB::selectOne("SHOW VARIABLES LIKE 'max_user_connections'");
                if ($maxUserConnRow && isset($maxUserConnRow->Value)) {
                    $maxUserConn = $maxUserConnRow->Value;
                }
            } catch (\Throwable $t) {
                // Ignore variable restriction in shared hosting
            }

            // Benchmark safe read-only query
            $startQuery = microtime(true);
            $cabangCount = DB::table('cabang')->count();
            $queryMs = round((microtime(true) - $startQuery) * 1000, 2);

            $status = ($pingMs <= 50) ? 'PASS' : ($pingMs <= 150 ? 'WARNING' : 'FAIL');

            return [
                'status'               => $status,
                'connected'            => true,
                'version'              => $version,
                'ping_latency_ms'      => $pingMs,
                'read_query_latency_ms'=> $queryMs,
                'max_connections'      => $maxConn,
                'max_user_connections' => $maxUserConn,
            ];
        } catch (\Throwable $e) {
            return [
                'status'               => 'FAIL',
                'connected'            => false,
                'error'                => $e->getMessage(),
                'ping_latency_ms'      => 'N/A',
                'read_query_latency_ms'=> 'N/A',
                'max_connections'      => 'N/A',
                'max_user_connections' => 'N/A',
            ];
        }
    }

    /**
     * Benchmark storage write and read speeds using a safe temporary file.
     */
    protected function checkStorage(): array
    {
        $storageWritable = is_writable(storage_path());
        $bootstrapWritable = is_writable(base_path('bootstrap/cache'));
        $publicStorageExists = File::isDirectory(storage_path('app/public'));
        $storageLinkExists = File::exists(public_path('storage'));

        $writeSpeedMb = 0;
        $readSpeedMb = 0;
        $ioStatus = 'PASS';

        $tempFile = storage_path('framework/cache/hosting_io_benchmark_' . uniqid() . '.tmp');

        try {
            // Generate 2 MB random binary payload
            $sizeBytes = 2 * 1024 * 1024;
            $data = random_bytes(65536); // 64 KB block
            $blocks = $sizeBytes / 65536;

            $startWrite = microtime(true);
            $fp = fopen($tempFile, 'wb');
            if ($fp) {
                for ($i = 0; $i < $blocks; $i++) {
                    fwrite($fp, $data);
                }
                fflush($fp);
                fclose($fp);
            }
            $writeTime = microtime(true) - $startWrite;
            $writeSpeedMb = ($writeTime > 0) ? round(2 / $writeTime, 2) : 0;

            $startRead = microtime(true);
            $readData = file_get_contents($tempFile);
            $readTime = microtime(true) - $startRead;
            $readSpeedMb = ($readTime > 0) ? round(2 / $readTime, 2) : 0;
            unset($readData);
        } catch (\Throwable $e) {
            $ioStatus = 'WARNING';
        } finally {
            if (File::exists($tempFile)) {
                @unlink($tempFile);
            }
        }

        $overallStatus = ($storageWritable && $bootstrapWritable && $publicStorageExists) ? 'PASS' : 'FAIL';
        if ($writeSpeedMb < 10 || $readSpeedMb < 20) {
            $ioStatus = 'WARNING';
        }

        return [
            'status'               => $overallStatus,
            'storage_writable'     => $storageWritable,
            'bootstrap_writable'   => $bootstrapWritable,
            'public_storage_dir'   => $publicStorageExists,
            'storage_link'         => $storageLinkExists,
            'write_speed_mb_s'     => $writeSpeedMb,
            'read_speed_mb_s'      => $readSpeedMb,
            'io_status'            => $ioStatus,
        ];
    }

    /**
     * Check disk space and inode availability.
     */
    protected function checkDisk(): array
    {
        $freeBytes = @disk_free_space(base_path());
        $totalBytes = @disk_total_space(base_path());

        $freeFormatted = ($freeBytes !== false) ? round($freeBytes / 1073741824, 2) . ' GB' : 'NOT AVAILABLE / CHECK CPANEL MANUALLY';
        $totalFormatted = ($totalBytes !== false) ? round($totalBytes / 1073741824, 2) . ' GB' : 'NOT AVAILABLE / CHECK CPANEL MANUALLY';

        return [
            'disk_free'  => $freeFormatted,
            'disk_total' => $totalFormatted,
            'inode'      => 'NOT AVAILABLE / CHECK CPANEL MANUALLY',
        ];
    }

    /**
     * Verify scheduler configuration and registered commands.
     */
    protected function checkScheduler(): array
    {
        $schedule = app()->make(\Illuminate\Console\Scheduling\Schedule::class);
        $events = $schedule->events();

        $autoAlphaRegistered = false;
        $autoAlphaFrequency = 'N/A';
        $registeredCommands = [];

        foreach ($events as $event) {
            $desc = $event->command ?? $event->description ?? 'Closure';
            $registeredCommands[] = [
                'command'    => $desc,
                'expression' => $event->expression,
            ];

            if (str_contains($desc, 'presensi:auto-alpha')) {
                $autoAlphaRegistered = true;
                $autoAlphaFrequency = $event->expression;
            }
        }

        $status = $autoAlphaRegistered ? 'PASS' : 'WARNING';

        return [
            'status'                 => $status,
            'auto_alpha_registered'  => $autoAlphaRegistered,
            'auto_alpha_frequency'   => $autoAlphaFrequency,
            'total_scheduled_events' => count($events),
            'recommended_cron'       => '* * * * * cd ' . base_path() . ' && php artisan schedule:run >> /dev/null 2>&1',
        ];
    }

    /**
     * Safe read-only performance check of core application database queries.
     */
    protected function checkApplicationReadiness(): array
    {
        $queries = [];

        try {
            // 1. Branch lookup
            $start = microtime(true);
            $branches = Cabang::all();
            $queries['branch_lookup_ms'] = round((microtime(true) - $start) * 1000, 2);
            $branchCount = $branches->count();

            // 2. Active employees query
            $start = microtime(true);
            $empCount = Karyawan::where('status_aktif_karyawan', 1)->count();
            $queries['active_employee_query_ms'] = round((microtime(true) - $start) * 1000, 2);

            // 3. Today attendance summary
            $start = microtime(true);
            $today = date('Y-m-d');
            $presentToday = Presensi::where('tanggal', $today)->count();
            $queries['attendance_summary_query_ms'] = round((microtime(true) - $start) * 1000, 2);

            // 4. Dashboard aggregation simulation
            $start = microtime(true);
            DB::table('presensi')
                ->select('status', DB::raw('count(*) as total'))
                ->where('tanggal', $today)
                ->groupBy('status')
                ->get();
            $queries['dashboard_aggregation_ms'] = round((microtime(true) - $start) * 1000, 2);

            $status = 'PASS';
            foreach ($queries as $ms) {
                if ($ms > 100) {
                    $status = 'WARNING';
                }
            }

            return [
                'status'         => $status,
                'branch_count'   => $branchCount,
                'employee_count' => $empCount,
                'queries'        => $queries,
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => 'WARNING',
                'error'   => 'Application benchmark skipped: ' . $e->getMessage(),
                'queries' => [],
            ];
        }
    }

    /**
     * Calculate 2-Branch capacity profile separating measured vs estimated.
     */
    protected function evaluateCapacityProfile(array $data): array
    {
        $serverMeasured = [
            'php_version' => $data['php']['version'],
            'memory_limit' => $data['php']['memory_limit'],
            'io_write_mb_s' => $data['storage']['write_speed_mb_s'],
            'db_latency_ms' => $data['database']['ping_latency_ms'],
        ];

        $appMeasured = $data['application']['queries'] ?? [];

        $concurrencyEstimated = [
            'target_branches'          => 2,
            'target_total_employees'   => 30,
            'employees_per_branch'     => 15,
            'peak_simultaneous_window' => '11 concurrent requests (5 Branch A + 5 Branch B + 1 Admin)',
            'cpanel_ep_requirement'    => 'Min 15-20 Entry Processes (EP) recommended',
            'warning'                  => 'Estimated capacity based on internal execution speed. Real external concurrency requires load testing from external network.',
        ];

        return [
            'server_measured'       => $serverMeasured,
            'application_measured'  => $appMeasured,
            'concurrency_estimated' => $concurrencyEstimated,
        ];
    }

    /**
     * Checklist items that cannot be probed via CageFS and must be verified in cPanel.
     */
    protected function getCpanelChecklist(): array
    {
        return [
            'CPU Limit'                   => 'CHECK MANUALLY IN CPANEL (100% or 1 Core recommended)',
            'Physical Memory (RAM)'       => 'CHECK MANUALLY IN CPANEL (1 GB - 2 GB recommended)',
            'Entry Processes (EP)'        => 'CHECK MANUALLY IN CPANEL (Min 20 EP recommended for 11 peak requests)',
            'Number of Processes (NPROC)' => 'CHECK MANUALLY IN CPANEL (100 recommended)',
            'I/O Limit'                   => 'CHECK MANUALLY IN CPANEL (5-10 MB/s recommended)',
            'IOPS Limit'                  => 'CHECK MANUALLY IN CPANEL (1024 recommended)',
            'Inode Quota'                 => 'CHECK MANUALLY IN CPANEL (Unlimited S typically 250,000 - 500,000 inodes)',
            'MySQL Max User Connections'  => 'CHECK MANUALLY IN CPANEL (Default shared hosting usually 15 - 30)',
        ];
    }

    /**
     * Determine final readiness verdict.
     */
    protected function determineVerdict(array $data): string
    {
        if ($data['php']['status'] === 'FAIL' || $data['database']['status'] === 'FAIL' || $data['storage']['status'] === 'FAIL') {
            return 'NOT READY';
        }

        if (
            $data['php']['status'] === 'WARNING' ||
            $data['opcache']['status'] === 'WARNING' ||
            $data['laravel']['status'] === 'WARNING' ||
            $data['database']['status'] === 'WARNING' ||
            $data['scheduler']['status'] === 'WARNING'
        ) {
            return 'READY WITH WARNING';
        }

        return 'READY FOR REAL LOAD TEST';
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
        $this->renderDotLine('Storage Write Speed', $data['storage']['write_speed_mb_s'] . ' MB/s');
        $this->renderDotLine('Storage Read Speed', $data['storage']['read_speed_mb_s'] . ' MB/s');
        $this->renderDotLine('Database Ping Latency', $data['database']['ping_latency_ms'] . ' ms');
        $this->renderDotLine('OPcache Hit Rate', $data['opcache']['hit_rate']);
        $this->renderDotLine('Disk Free Space', $data['disk']['disk_free']);
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
            if ($value === 'PASS') {
                $color = 'bright-green';
            } elseif ($value === 'WARNING') {
                $color = 'bright-yellow';
            } elseif ($value === 'FAIL') {
                $color = 'bright-red';
            } else {
                $color = 'bright-white';
            }
        }

        $this->line("  <fg=white>{$label}</> <fg=gray>{$dots}</> <fg={$color};options=bold>{$value}</>");
    }

    /**
     * Convert memory string (e.g. 256M, 1G) to integer bytes.
     */
    protected function convertToBytes(string $val): int
    {
        $val = trim($val);
        if ($val === '-1') {
            return -1;
        }

        $last = strtolower($val[strlen($val) - 1] ?? '');
        $num = (int) $val;

        switch ($last) {
            case 'g':
                $num *= 1024;
            case 'm':
                $num *= 1024;
            case 'k':
                $num *= 1024;
        }

        return $num;
    }
}
