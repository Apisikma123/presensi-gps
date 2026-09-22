<?php

namespace App\Console\Commands;

use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class MaintenanceAttendanceStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:attendance-storage
                            {--execute : Execute actual file deletion and cleanup (default is dry-run)}
                            {--retention-months= : Override retention period in months (default from config)}
                            {--days= : Override retention period in days (takes precedence over months)}
                            {--chunk=500 : Batch size per execution to avoid shared hosting CPU/IO spikes}
                            {--clean-ephemeral : Also prune expired database sessions and cache}
                            {--json : Output report in JSON format}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit and safely clean up expired attendance photos and ephemeral system data without touching database records.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isExecute = (bool) $this->option('execute');
        $isJson = (bool) $this->option('json');
        $chunkSize = max(50, (int) $this->option('chunk'));

        $daysOption = $this->option('days');
        if ($daysOption !== null) {
            $retentionDays = max(1, (int) $daysOption);
            $retentionLabel = "{$retentionDays} Days";
            $cutoffDate = Carbon::now()->subDays($retentionDays)->toDateString();
        } else {
            $retentionMonths = (int) ($this->option('retention-months') ?: config('attendance.photo_retention_months', 12));
            if ($retentionMonths < 1) {
                $retentionMonths = 12;
            }
            $retentionLabel = "{$retentionMonths} Months";
            $cutoffDate = Carbon::now()->subMonths($retentionMonths)->toDateString();
        }

        // 1. Scan attendance files on disk
        $disk = Storage::disk('public');
        $attendanceRelDir = 'uploads/absensi';
        $diskPath = storage_path('app/public/' . $attendanceRelDir);

        $diskFiles = [];
        $totalDiskBytes = 0;
        $oldestFileTime = null;
        $oldestFileName = null;

        if (is_dir($diskPath)) {
            $iterator = new \DirectoryIterator($diskPath);
            foreach ($iterator as $fileInfo) {
                if ($fileInfo->isFile()) {
                    $name = $fileInfo->getFilename();
                    if ($name === '.gitignore' || str_starts_with($name, '.')) {
                        continue;
                    }
                    $size = $fileInfo->getSize();
                    $mtime = $fileInfo->getMTime();
                    $diskFiles[$name] = [
                        'size' => $size,
                        'mtime' => $mtime,
                    ];
                    $totalDiskBytes += $size;

                    if ($oldestFileTime === null || $mtime < $oldestFileTime) {
                        $oldestFileTime = $mtime;
                        $oldestFileName = $name;
                    }
                }
            }
        }

        $totalDiskCount = count($diskFiles);

        // 2. Query attendance records older than retention threshold
        $eligibleQuery = Presensi::where('tanggal', '<', $cutoffDate)
            ->where(function ($q) {
                $q->whereNotNull('foto_in')->where('foto_in', '!=', '')
                  ->orWhereNotNull('foto_out')->where('foto_out', '!=', '');
            })
            ->select('id', 'nik', 'tanggal', 'foto_in', 'foto_out');

        $eligibleFilesToDelete = [];
        $eligibleBytesRecoverable = 0;

        foreach ($eligibleQuery->cursor() as $record) {
            // Check foto_in
            if (!empty($record->foto_in) && isset($diskFiles[$record->foto_in])) {
                $eligibleFilesToDelete[$record->foto_in] = $diskFiles[$record->foto_in]['size'];
            }
            // Check foto_out
            if (!empty($record->foto_out) && isset($diskFiles[$record->foto_out])) {
                $eligibleFilesToDelete[$record->foto_out] = $diskFiles[$record->foto_out]['size'];
            }
        }

        $eligibleBytesRecoverable = array_sum($eligibleFilesToDelete);
        $eligibleCount = count($eligibleFilesToDelete);

        // 3. Orphan File Detection (Files on disk never referenced by ANY row in presensi)
        $dbReferencedPhotos = [];
        Presensi::whereNotNull('foto_in')->where('foto_in', '!=', '')->pluck('foto_in')->each(function ($f) use (&$dbReferencedPhotos) {
            $dbReferencedPhotos[$f] = true;
        });
        Presensi::whereNotNull('foto_out')->where('foto_out', '!=', '')->pluck('foto_out')->each(function ($f) use (&$dbReferencedPhotos) {
            $dbReferencedPhotos[$f] = true;
        });

        $orphanFiles = [];
        $orphanBytes = 0;
        foreach ($diskFiles as $filename => $meta) {
            if (!isset($dbReferencedPhotos[$filename])) {
                $orphanFiles[$filename] = $meta['size'];
                $orphanBytes += $meta['size'];
            }
        }

        // 4. Ephemeral Data Audit (Sessions, Logs, Cache)
        $expiredSessionsCount = 0;
        $sessionLifetimeMinutes = (int) config('session.lifetime', 10080);
        $sessionExpiryTimestamp = time() - ($sessionLifetimeMinutes * 60);

        if (Schema::hasTable('sessions')) {
            $expiredSessionsCount = DB::table('sessions')->where('last_activity', '<', $sessionExpiryTimestamp)->count();
        }

        $storageLogsPath = storage_path('logs');
        $logBytes = 0;
        $logFilesCount = 0;
        if (is_dir($storageLogsPath)) {
            foreach (glob($storageLogsPath . '/*.log') as $lf) {
                if (is_file($lf)) {
                    $logFilesCount++;
                    $logBytes += filesize($lf);
                }
            }
        }

        // 5. Execution Phase (if --execute)
        $deletedCount = 0;
        $deletedBytes = 0;
        $deletedSessions = 0;

        if ($isExecute) {
            $batchList = array_slice(array_keys($eligibleFilesToDelete), 0, $chunkSize);
            foreach ($batchList as $filename) {
                try {
                    $relPath = $attendanceRelDir . '/' . $filename;
                    if ($disk->exists($relPath)) {
                        $fSize = $disk->size($relPath);
                        $disk->delete($relPath);
                        $deletedCount++;
                        $deletedBytes += $fSize;
                    }
                } catch (\Throwable $e) {
                    $this->error("Failed deleting {$filename}: " . $e->getMessage());
                }
            }

            if ($this->option('clean-ephemeral') && Schema::hasTable('sessions') && $expiredSessionsCount > 0) {
                $deletedSessions = DB::table('sessions')->where('last_activity', '<', $sessionExpiryTimestamp)->delete();
            }

            Log::info("attendance-maintenance: cleaned {$deletedCount} photos (" . round($deletedBytes / 1024 / 1024, 2) . " MB), pruned {$deletedSessions} expired sessions.");
        }

        // 6. Output Reporting
        $report = [
            'mode' => $isExecute ? 'EXECUTE' : 'DRY RUN',
            'retention' => $retentionLabel,
            'cutoff_date' => $cutoffDate,
            'storage' => [
                'total_attendance_photos' => $totalDiskCount,
                'total_attendance_size_mb' => round($totalDiskBytes / 1024 / 1024, 2),
                'oldest_photo_file' => $oldestFileName ?? 'NONE',
                'oldest_photo_date' => $oldestFileTime ? date('Y-m-d H:i:s', $oldestFileTime) : 'N/A',
            ],
            'retention_cleanup' => [
                'files_eligible' => $eligibleCount,
                'estimated_recoverable_mb' => round($eligibleBytesRecoverable / 1024 / 1024, 2),
                'files_deleted' => $deletedCount,
                'bytes_deleted_mb' => round($deletedBytes / 1024 / 1024, 2),
            ],
            'orphan_audit' => [
                'orphan_files_count' => count($orphanFiles),
                'orphan_size_mb' => round($orphanBytes / 1024 / 1024, 2),
            ],
            'ephemeral_data' => [
                'expired_sessions' => $expiredSessionsCount,
                'deleted_sessions' => $deletedSessions,
                'log_files' => $logFilesCount,
                'log_size_mb' => round($logBytes / 1024 / 1024, 2),
            ],
        ];

        if ($isJson) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT));
            return 0;
        }

        $this->info("==================================================");
        $this->info("  HR PRESENCE - STORAGE & DATA RETENTION AUDIT");
        $this->info("==================================================");
        $this->line("Mode:            " . ($isExecute ? "<fg=red;options=bold>EXECUTE</>" : "<fg=yellow;options=bold>DRY RUN (safe preview)</>"));
        $this->line("Retention:       <fg=green>{$retentionLabel}</> (threshold date: <fg=cyan>{$cutoffDate}</>)");
        $this->line("Total Photos:    {$totalDiskCount} files (" . round($totalDiskBytes / 1024 / 1024, 2) . " MB)");
        $this->line("Oldest Photo:    " . ($oldestFileName ? "{$oldestFileName} (" . date('Y-m-d', $oldestFileTime) . ")" : 'None'));
        $this->newLine();

        $this->info("--- RETENTION ELIGIBILITY ---");
        $this->line("Files eligible:                 <fg=cyan>{$eligibleCount}</>");
        $this->line("Estimated space recoverable:    <fg=green>" . round($eligibleBytesRecoverable / 1024 / 1024, 2) . " MB</>");

        if ($isExecute) {
            $this->line("Files deleted:                  <fg=yellow>{$deletedCount}</> (limit chunk: {$chunkSize})");
            $this->line("Space recovered:                <fg=green>" . round($deletedBytes / 1024 / 1024, 2) . " MB</>");
        } else {
            $this->comment("No files deleted (dry-run mode). Use --execute to clean.");
        }

        $this->newLine();
        $this->info("--- ORPHAN UPLOAD AUDIT ---");
        $this->line("Files on disk not in DB:        " . count($orphanFiles) . " (" . round($orphanBytes / 1024 / 1024, 2) . " MB)");
        if (count($orphanFiles) > 0) {
            $sample = array_slice(array_keys($orphanFiles), 0, 5);
            $this->line("Sample orphans:                 " . implode(', ', $sample));
            $this->comment("Note: Orphans are retained for safety. Verify before deleting.");
        }

        $this->newLine();
        $this->info("--- EPHEMERAL SYSTEM DATA ---");
        $this->line("Expired database sessions:      {$expiredSessionsCount} rows");
        if ($isExecute && $deletedSessions > 0) {
            $this->line("Pruned sessions:                {$deletedSessions} rows");
        }
        $this->line("Active storage logs:            {$logFilesCount} files (" . round($logBytes / 1024 / 1024, 2) . " MB)");

        $this->newLine();
        $this->info("Database presensi history status: <fg=green;options=bold>100% INTACT (No rows deleted)</>");
        $this->info("==================================================");

        return 0;
    }
}
