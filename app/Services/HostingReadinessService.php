<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Models\Cabang;
use App\Models\Karyawan;
use App\Models\Presensi;

class HostingReadinessService
{
    /**
     * Run all hosting readiness and environment audits.
     */
    public function runAudit(): array
    {
        $data = [];

        // 1. PHP Environment
        $data['php'] = $this->checkPhpEnvironment();

        // 2. OPcache
        $data['opcache'] = $this->checkOpcache();

        // 3. Laravel Framework Setup
        $data['laravel'] = $this->checkLaravelSetup();

        // 4. Database Connection & Latency
        $data['database'] = $this->checkDatabase();

        // 5. Storage & Filesystem I/O
        $data['storage'] = $this->checkStorage();

        // 6. Disk Free Space
        $data['disk'] = $this->checkDisk();

        // 7. Scheduler & Cron Heartbeat
        $data['scheduler'] = $this->checkScheduler();

        // 8. Application Read-Only Benchmark
        $data['application'] = $this->checkApplicationReadiness();

        // 9. 2-Branch Capacity Profile
        $data['capacity_profile'] = $this->evaluateCapacityProfile($data);

        // 10. cPanel Manual Checklist
        $data['cpanel_checklist'] = $this->getCpanelChecklist();

        // 11. Final Verdict
        $data['verdict'] = $this->determineVerdict($data);

        return $data;
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
            'bcmath'    => extension_loaded('bcmath'),
            'gmp'       => extension_loaded('gmp'),
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
                'note'          => 'OPcache disabled in php.ini. Recommended for production performance.',
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
     * Check Laravel production caching, environment flags, cache, and session.
     */
    protected function checkLaravelSetup(): array
    {
        $appEnv = config('app.env');
        $appDebug = (bool) config('app.debug');

        $configCached = app()->configurationIsCached();
        $routesCached = app()->routesAreCached();
        
        $viewFiles = glob(storage_path('framework/views/*.php'));
        $viewsCached = !empty($viewFiles);

        $hotExists = File::exists(public_path('hot'));
        $manifestExists = File::exists(public_path('build/manifest.json'));

        // Cache probe
        $cacheWorking = false;
        try {
            $testKey = 'readiness_probe_' . uniqid();
            Cache::put($testKey, 'ok', 10);
            $cacheWorking = (Cache::get($testKey) === 'ok');
            Cache::forget($testKey);
        } catch (\Throwable $e) {
            $cacheWorking = false;
        }

        // Session driver & Queue config
        $sessionDriver = config('session.driver', 'file');
        $queueDriver = config('queue.default', 'sync');

        $isProduction = ($appEnv === 'production' && $appDebug === false);
        $status = ($isProduction && $manifestExists && !$hotExists && $cacheWorking) ? 'PASS' : 'WARNING';

        return [
            'status'          => $status,
            'env'             => $appEnv,
            'debug'           => $appDebug,
            'config_cached'   => $configCached,
            'routes_cached'   => $routesCached,
            'views_cached'    => $viewsCached,
            'hot_file_absent' => !$hotExists,
            'vite_manifest'   => $manifestExists,
            'cache_driver'    => config('cache.default', 'file'),
            'cache_working'   => $cacheWorking,
            'session_driver'  => $sessionDriver,
            'queue_driver'    => $queueDriver,
        ];
    }

    /**
     * Test database connection, latency, and read benchmarks.
     */
    protected function checkDatabase(): array
    {
        try {
            $startPing = microtime(true);
            DB::selectOne('SELECT 1');
            $pingMs = round((microtime(true) - $startPing) * 1000, 2);

            $versionRow = DB::selectOne('SELECT VERSION() as ver');
            $version = $versionRow ? $versionRow->ver : 'Unknown';

            $maxConn = 'NOT AVAILABLE / CHECK CPANEL';
            $maxUserConn = 'NOT AVAILABLE / CHECK CPANEL';

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

            $startQuery = microtime(true);
            $hasCabang = Schema::hasTable('cabang');
            $cabangCount = $hasCabang ? DB::table('cabang')->count() : 0;
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
     * Benchmark storage write and read speeds, and verify specific folder write permissions.
     */
    protected function checkStorage(): array
    {
        $storageWritable = is_writable(storage_path());
        $bootstrapWritable = is_writable(base_path('bootstrap/cache'));
        $publicStorageExists = File::isDirectory(storage_path('app/public'));

        // Attendance upload directory check
        $absensiDir = storage_path('app/public/uploads/absensi');
        if (!is_dir($absensiDir)) @mkdir($absensiDir, 0755, true);
        $absensiWritable = is_writable($absensiDir);

        // Archive directory check
        $archiveDir = storage_path('app/private/attendance-archive');
        if (!is_dir($archiveDir)) @mkdir($archiveDir, 0755, true);
        $archiveWritable = is_writable($archiveDir);

        $writeSpeedMb = 0;
        $readSpeedMb = 0;
        $ioStatus = 'PASS';

        $tempFile = storage_path('framework/cache/hosting_io_benchmark_' . uniqid() . '.tmp');

        try {
            $sizeBytes = 2 * 1024 * 1024;
            $data = random_bytes(65536);
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

        $overallStatus = ($storageWritable && $bootstrapWritable && $absensiWritable && $archiveWritable) ? 'PASS' : 'FAIL';
        if ($writeSpeedMb < 10 || $readSpeedMb < 20) {
            $ioStatus = 'WARNING';
        }

        return [
            'status'               => $overallStatus,
            'storage_writable'     => $storageWritable,
            'bootstrap_writable'   => $bootstrapWritable,
            'public_storage_dir'   => $publicStorageExists,
            'absensi_writable'     => $absensiWritable,
            'archive_writable'     => $archiveWritable,
            'write_speed_mb_s'     => $writeSpeedMb,
            'read_speed_mb_s'      => $readSpeedMb,
            'io_status'            => $ioStatus,
        ];
    }

    /**
     * Check disk space availability.
     */
    protected function checkDisk(): array
    {
        $freeBytes = @disk_free_space(base_path());
        $totalBytes = @disk_total_space(base_path());

        $freeFormatted = ($freeBytes !== false) ? round($freeBytes / 1073741824, 2) . ' GB' : 'NOT AVAILABLE / CHECK CPANEL';
        $totalFormatted = ($totalBytes !== false) ? round($totalBytes / 1073741824, 2) . ' GB' : 'NOT AVAILABLE / CHECK CPANEL';

        return [
            'disk_free'  => $freeFormatted,
            'disk_total' => $totalFormatted,
            'inode'      => 'NOT AVAILABLE / CHECK CPANEL',
        ];
    }

    /**
     * Verify scheduler configuration and check the last recorded heartbeat.
     */
    protected function checkScheduler(): array
    {
        $schedule = app()->make(\Illuminate\Console\Scheduling\Schedule::class);
        $events = $schedule->events();

        $autoAlphaRegistered = false;
        $registeredCommands = [];

        foreach ($events as $event) {
            $desc = $event->command ?? $event->description ?? 'Closure';
            $registeredCommands[] = [
                'command'    => $desc,
                'expression' => $event->expression,
            ];

            if (str_contains($desc, 'presensi:auto-alpha')) {
                $autoAlphaRegistered = true;
            }
        }

        // Heartbeat check
        $lastHeartbeat = Cache::get('scheduler_last_heartbeat');
        $heartbeatStatus = 'NOT_RECORDED';
        $heartbeatAgo = 'Belum pernah terekam';

        if ($lastHeartbeat) {
            $diffSeconds = now()->timestamp - (int) $lastHeartbeat;
            $diffMinutes = round($diffSeconds / 60);

            if ($diffMinutes < 60) {
                $heartbeatStatus = 'NORMAL';
                $heartbeatAgo = $diffMinutes . ' menit yang lalu';
            } elseif ($diffMinutes <= 120) {
                $heartbeatStatus = 'WARNING';
                $heartbeatAgo = $diffMinutes . ' menit yang lalu';
            } else {
                $heartbeatStatus = 'BERMASALAH';
                $heartbeatAgo = $diffMinutes . ' menit yang lalu';
            }
        }

        $status = ($autoAlphaRegistered && ($heartbeatStatus === 'NORMAL' || $heartbeatStatus === 'NOT_RECORDED')) ? 'PASS' : 'WARNING';

        return [
            'status'                 => $status,
            'auto_alpha_registered'  => $autoAlphaRegistered,
            'total_scheduled_events' => count($events),
            'last_heartbeat'         => $heartbeatAgo,
            'heartbeat_status'       => $heartbeatStatus,
        ];
    }

    /**
     * Safe read-only performance check of core application database queries.
     */
    protected function checkApplicationReadiness(): array
    {
        $queries = [];

        try {
            if (Schema::hasTable('cabang')) {
                $start = microtime(true);
                Cabang::all();
                $queries['branch_lookup_ms'] = round((microtime(true) - $start) * 1000, 2);
            }

            if (Schema::hasTable('karyawan')) {
                $start = microtime(true);
                Karyawan::where('status_aktif_karyawan', 1)->count();
                $queries['active_employee_query_ms'] = round((microtime(true) - $start) * 1000, 2);
            }

            if (Schema::hasTable('presensi')) {
                $start = microtime(true);
                Presensi::where('tanggal', date('Y-m-d'))->count();
                $queries['attendance_summary_query_ms'] = round((microtime(true) - $start) * 1000, 2);
            }
        } catch (\Throwable $e) {
            // Ignore benchmark failures if database is empty/not migrated
        }

        return [
            'queries' => $queries,
        ];
    }

    /**
     * Evaluate 2-Branch capacity profile.
     */
    protected function evaluateCapacityProfile(array $data): array
    {
        return [
            'branches'            => 2,
            'target_employees'    => '30 - 50 karyawan',
            'peak_concurrency'    => '11 simultaneous requests',
            'recommended_ep'      => 'Min 20 Entry Processes (EP)',
        ];
    }

    /**
     * Checklist items that cannot be probed via CageFS and must be verified in cPanel.
     */
    protected function getCpanelChecklist(): array
    {
        return [
            'CPU Limit'                   => 'CHECK IN CPANEL (100% or 1 Core recommended)',
            'Physical Memory (RAM)'       => 'CHECK IN CPANEL (1 GB - 2 GB recommended)',
            'Entry Processes (EP)'        => 'CHECK IN CPANEL (Min 20 EP recommended)',
            'Number of Processes (NPROC)' => 'CHECK IN CPANEL (100 recommended)',
            'I/O Limit'                   => 'CHECK IN CPANEL (5-10 MB/s recommended)',
            'IOPS Limit'                  => 'CHECK IN CPANEL (1024 recommended)',
            'Inode Quota'                 => 'CHECK IN CPANEL (Typically 250,000 - 500,000)',
            'MySQL Max User Connections'  => 'CHECK IN CPANEL (Typically 15 - 30)',
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
