<?php

namespace App\Console\Commands;

use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ArchiveAttendancePhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:archive-attendance-photos
                            {--execute : Execute actual archiving and safe cleanup of source files (default is dry-run)}
                            {--month= : Specific eligible month to archive in YYYY-MM format}
                            {--json : Output report in JSON format}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Safely archive attendance photos older than retention window into monthly private ZIPs (Max 1 month per run).';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isExecute = (bool) $this->option('execute');
        $isJson = (bool) $this->option('json');

        // 1. Check if archiving is enabled in config
        $archiveEnabled = (bool) config('attendance.archive_enabled', true);
        if (!$archiveEnabled) {
            if ($isJson) {
                $this->line(json_encode(['status' => 'disabled', 'message' => 'Attendance photo archiving is disabled in config.']));
            } else {
                $this->warn('ATTENDANCE_PHOTO_ARCHIVE_ENABLED is set to false. No files were modified.');
            }
            return 0;
        }

        // 2. Check ZipArchive extension availability
        if (!class_exists(ZipArchive::class)) {
            $this->error('ERROR: PHP ZipArchive extension is NOT installed/enabled on this server.');
            $this->line('Archiving aborted to ensure zero data loss. Please enable ext-zip in PHP configuration.');
            return 1;
        }

        $hotMonths = (int) config('attendance.hot_months', 12);
        if ($hotMonths < 1) {
            $hotMonths = 12;
        }

        // Cutoff month: any month strictly older than hotMonths before the start of the current month
        // Example: If current month is 2028-09 and hot_months=12, cutoff month is 2027-09.
        // Months eligible for archiving are <= 2027-08 (strictly < 2027-09).
        $currentMonthStart = Carbon::now()->startOfMonth();
        $cutoffMonthDate = $currentMonthStart->copy()->subMonths($hotMonths);
        $cutoffMonthStr = $cutoffMonthDate->format('Y-m');

        // 3. Scan physical attendance files on disk
        $attendanceDir = storage_path('app/public/uploads/absensi');
        $totalDiskFiles = 0;
        $totalDiskBytes = 0;
        $oldestFileTime = null;
        $oldestFileName = null;

        if (is_dir($attendanceDir)) {
            $iterator = new \DirectoryIterator($attendanceDir);
            foreach ($iterator as $fileInfo) {
                if ($fileInfo->isFile()) {
                    $name = $fileInfo->getFilename();
                    if ($name === '.gitignore' || str_starts_with($name, '.')) {
                        continue;
                    }
                    $totalDiskFiles++;
                    $size = $fileInfo->getSize();
                    $totalDiskBytes += $size;
                    $mtime = $fileInfo->getMTime();
                    if ($oldestFileTime === null || $mtime < $oldestFileTime) {
                        $oldestFileTime = $mtime;
                        $oldestFileName = $name;
                    }
                }
            }
        }

        // 4. Scan existing archives in private storage
        $archiveDir = config('attendance.archive_disk_path', storage_path('app/private/attendance-archive'));
        if (!is_dir($archiveDir)) {
            @mkdir($archiveDir, 0755, true);
        }

        $archivedZipCount = 0;
        $archivedZipBytes = 0;
        if (is_dir($archiveDir)) {
            foreach (glob($archiveDir . '/*.zip') as $zipFile) {
                if (is_file($zipFile)) {
                    $archivedZipCount++;
                    $archivedZipBytes += filesize($zipFile);
                }
            }
        }

        // Read manifest index.json if present
        $manifestPath = $archiveDir . '/index.json';
        $manifest = [];
        if (file_exists($manifestPath)) {
            $manifestContent = file_get_contents($manifestPath);
            $manifest = json_decode($manifestContent, true) ?: [];
        }

        // 5. Determine target eligible month (MAX 1 MONTH PER RUN)
        $targetMonth = null;
        $specifiedMonth = $this->option('month');

        if (!empty($specifiedMonth)) {
            if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $specifiedMonth)) {
                $this->error("Invalid month format '{$specifiedMonth}'. Expected format: YYYY-MM (e.g. 2027-01).");
                return 1;
            }
            if ($specifiedMonth >= $cutoffMonthStr) {
                $this->error("Month '{$specifiedMonth}' is still within the {$hotMonths}-month hot storage window (cutoff: < {$cutoffMonthStr}). Archiving is blocked.");
                return 1;
            }
            $targetMonth = $specifiedMonth;
        } else {
            // Find oldest eligible unarchived month from database
            $oldestRecord = Presensi::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') < ?", [$cutoffMonthStr])
                ->where('is_archived', 0)
                ->where(function ($q) {
                    $q->whereNotNull('foto_in')->where('foto_in', '!=', '')
                      ->orWhereNotNull('foto_out')->where('foto_out', '!=', '');
                })
                ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as month_str, MIN(tanggal) as min_date")
                ->groupBy(DB::raw("DATE_FORMAT(tanggal, '%Y-%m')"))
                ->orderBy('month_str', 'asc')
                ->first();

            if ($oldestRecord) {
                $targetMonth = $oldestRecord->month_str;
            }
        }

        // Count hot photos in active period
        $hotPhotoCount = Presensi::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') >= ?", [$cutoffMonthStr])
            ->where(function ($q) {
                $q->whereNotNull('foto_in')->where('foto_in', '!=', '')
                  ->orWhereNotNull('foto_out')->where('foto_out', '!=', '');
            })
            ->count();

        // 6. If no eligible month found
        if (!$targetMonth) {
            $report = [
                'status' => 'NO_ELIGIBLE_MONTH',
                'message' => "All attendance records are within the {$hotMonths}-month hot storage period. No archiving needed.",
                'mode' => $isExecute ? 'EXECUTE' : 'DRY RUN',
                'hot_months_threshold' => $hotMonths,
                'cutoff_month' => $cutoffMonthStr,
                'storage' => [
                    'attendance_photos_count' => $totalDiskFiles,
                    'attendance_photos_mb' => round($totalDiskBytes / 1024 / 1024, 2),
                    'oldest_photo_file' => $oldestFileName ?? 'NONE',
                    'oldest_photo_date' => $oldestFileTime ? date('Y-m-d H:i:s', $oldestFileTime) : 'N/A',
                    'hot_photo_records' => $hotPhotoCount,
                    'archived_months_count' => $archivedZipCount,
                    'archived_zip_total_mb' => round($archivedZipBytes / 1024 / 1024, 2),
                    'inode_status' => 'CHECK CPANEL FILE USAGE',
                ],
            ];

            if ($isJson) {
                $this->line(json_encode($report, JSON_PRETTY_PRINT));
                return 0;
            }

            $this->info("==================================================");
            $this->info("  HR PRESENCE - LONG-TERM STORAGE ARCHIVE");
            $this->info("==================================================");
            $this->line("Mode:                 " . ($isExecute ? "<fg=red;options=bold>EXECUTE</>" : "<fg=yellow;options=bold>DRY RUN</>"));
            $this->line("Hot Retention:        <fg=green>{$hotMonths} Months</> (Eligible archives strictly before: <fg=cyan>{$cutoffMonthStr}</>)");
            $this->line("Attendance Photos:    {$totalDiskFiles} files (" . round($totalDiskBytes / 1024 / 1024, 2) . " MB)");
            $this->line("Hot Period Records:   {$hotPhotoCount}");
            $this->line("Archived Months:      {$archivedZipCount} ZIPs (" . round($archivedZipBytes / 1024 / 1024, 2) . " MB)");
            $this->line("Server Inodes:        <fg=yellow>CHECK CPANEL FILE USAGE</>");
            $this->newLine();
            $this->comment("No eligible month found for archiving. All data is within the hot period.");
            $this->info("==================================================");
            return 0;
        }

        // 7. Query eligible attendance photos for target month
        $records = Presensi::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$targetMonth])
            ->where(function ($q) {
                $q->whereNotNull('foto_in')->where('foto_in', '!=', '')
                  ->orWhereNotNull('foto_out')->where('foto_out', '!=', '');
            })
            ->select('id', 'nik', 'tanggal', 'foto_in', 'foto_out', 'is_archived')
            ->get();

        $photoFilenames = [];
        foreach ($records as $r) {
            if (!empty($r->foto_in)) {
                $safeIn = basename($r->foto_in);
                $photoFilenames[$safeIn] = true;
            }
            if (!empty($r->foto_out)) {
                $safeOut = basename($r->foto_out);
                $photoFilenames[$safeOut] = true;
            }
        }

        // Identify which source files physically exist on disk
        $existingSourceFiles = [];
        $totalEligibleBytes = 0;
        foreach (array_keys($photoFilenames) as $fn) {
            $filePath = $attendanceDir . '/' . $fn;
            if (is_file($filePath)) {
                $fSize = filesize($filePath);
                $existingSourceFiles[$fn] = [
                    'path' => $filePath,
                    'size' => $fSize,
                ];
                $totalEligibleBytes += $fSize;
            }
        }

        $filesFoundCount = count($existingSourceFiles);
        $finalZipName = $targetMonth . '.zip';
        $finalZipPath = $archiveDir . '/' . $finalZipName;

        // 8. DRY RUN Output
        if (!$isExecute) {
            $report = [
                'status' => 'DRY_RUN',
                'eligible_month' => $targetMonth,
                'files_found' => $filesFoundCount,
                'estimated_size_mb' => round($totalEligibleBytes / 1024 / 1024, 2),
                'archive_target' => 'storage/app/private/attendance-archive/' . $finalZipName,
                'storage' => [
                    'attendance_photos_count' => $totalDiskFiles,
                    'attendance_photos_mb' => round($totalDiskBytes / 1024 / 1024, 2),
                    'oldest_photo_date' => $oldestFileTime ? date('Y-m-d H:i:s', $oldestFileTime) : 'N/A',
                    'hot_photo_records' => $hotPhotoCount,
                    'archived_months_count' => $archivedZipCount,
                    'archived_zip_total_mb' => round($archivedZipBytes / 1024 / 1024, 2),
                    'inode_status' => 'CHECK CPANEL FILE USAGE',
                ],
                'note' => 'NO FILES MODIFIED - DRY RUN',
            ];

            if ($isJson) {
                $this->line(json_encode($report, JSON_PRETTY_PRINT));
                return 0;
            }

            $this->info("==================================================");
            $this->info("  HR PRESENCE - LONG-TERM STORAGE ARCHIVE");
            $this->info("==================================================");
            $this->line("Mode:                 <fg=yellow;options=bold>DRY RUN (Preview)</>");
            $this->line("Eligible month:       <fg=cyan;options=bold>{$targetMonth}</>");
            $this->line("Files found:          <fg=green>{$filesFoundCount}</>");
            $this->line("Estimated size:       " . round($totalEligibleBytes / 1024 / 1024, 2) . " MB");
            $this->line("Archive target:       <fg=blue>storage/app/private/attendance-archive/{$finalZipName}</>");
            $this->newLine();
            $this->info("--- STORAGE METRICS ---");
            $this->line("Attendance photo count:      {$totalDiskFiles} files");
            $this->line("Attendance storage size:     " . round($totalDiskBytes / 1024 / 1024, 2) . " MB");
            $this->line("Oldest photo date:           " . ($oldestFileTime ? date('Y-m-d H:i:s', $oldestFileTime) : 'N/A'));
            $this->line("Hot photo count:             {$hotPhotoCount} records");
            $this->line("Archived month count:        {$archivedZipCount} ZIPs");
            $this->line("Archive ZIP size total:      " . round($archivedZipBytes / 1024 / 1024, 2) . " MB");
            $this->line("Hosting Inode Usage:         <fg=yellow>CHECK CPANEL FILE USAGE</>");
            $this->newLine();
            $this->warn("NO FILES MODIFIED - DRY RUN");
            $this->line("To execute archiving: <fg=green>php artisan maintenance:archive-attendance-photos --execute</>");
            $this->info("==================================================");
            return 0;
        }

        // 9. EXECUTE MODE: Strict 10-Step Zero Data Loss Flow
        $this->info("Starting archive execution for month: {$targetMonth}...");

        if ($filesFoundCount === 0) {
            // If rows exist but no files on disk, mark DB rows as archived
            Presensi::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$targetMonth])
                ->update([
                    'is_archived' => 1,
                    'foto_in_archived' => 1,
                    'foto_out_archived' => 1,
                ]);
            $this->warn("No physical files found on disk for month {$targetMonth}. Database rows marked as archived.");
            return 0;
        }

        // If target archive already exists and is valid, handle duplicate execution safely
        if (file_exists($finalZipPath)) {
            $checkExisting = new ZipArchive();
            if ($checkExisting->open($finalZipPath, ZipArchive::CHECKCONS) === true) {
                if ($checkExisting->numFiles >= $filesFoundCount) {
                    $checkExisting->close();
                    $this->comment("Archive {$finalZipName} already exists and is valid ({$filesFoundCount} entries). Updating database flags and cleaning source files safely...");
                    Presensi::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$targetMonth])
                        ->update([
                            'is_archived' => 1,
                            'foto_in_archived' => 1,
                            'foto_out_archived' => 1,
                        ]);
                    foreach ($existingSourceFiles as $fn => $meta) {
                        @unlink($meta['path']);
                    }
                    $this->info("Archive synchronization complete for month {$targetMonth}.");
                    return 0;
                }
                $checkExisting->close();
            }
        }

        // Step 3: Create TEMPORARY ZIP
        if (!is_dir($archiveDir)) {
            if (!@mkdir($archiveDir, 0755, true) && !is_dir($archiveDir)) {
                $this->error("ABORT: Failed to ensure archive directory exists: {$archiveDir}. Zero files deleted.");
                return 1;
            }
        }

        $tmpZipName = $targetMonth . '.zip.tmp.' . time() . '.' . uniqid();
        $tmpZipPath = $archiveDir . '/' . $tmpZipName;

        $zip = new ZipArchive();
        try {
            $zipOpenResult = @$zip->open($tmpZipPath, ZipArchive::CREATE | ZipArchive::EXCL);
        } catch (\Throwable $e) {
            $zipOpenResult = false;
        }

        if ($zipOpenResult !== true) {
            $this->error("ABORT: Failed to create temporary ZIP archive: {$tmpZipPath}. Zero files deleted.");
            return 1;
        }

        // Add files strictly with prefix uploads/absensi/{filename}
        $addedCount = 0;
        foreach ($existingSourceFiles as $filename => $meta) {
            if ($zip->addFile($meta['path'], 'uploads/absensi/' . $filename)) {
                $addedCount++;
            } else {
                $zip->close();
                @unlink($tmpZipPath);
                $this->error("ABORT: Failed adding {$filename} to ZIP archive. Zero files deleted.");
                return 1;
            }
        }

        // Step 4: Close ZIP properly
        if (!$zip->close()) {
            @unlink($tmpZipPath);
            $this->error("ABORT: Failed to properly close temporary ZIP archive. Zero files deleted.");
            return 1;
        }

        // Step 5: Re-open ZIP
        $verifyZip = new ZipArchive();
        try {
            $reopenResult = @$verifyZip->open($tmpZipPath, ZipArchive::CHECKCONS);
        } catch (\Throwable $e) {
            $reopenResult = false;
        }

        // Step 6: Verify ZIP not corrupt
        if ($reopenResult !== true) {
            @unlink($tmpZipPath);
            $this->error("ABORT: Verification failed. Re-opened ZIP is corrupt or unreadable. Zero files deleted.");
            return 1;
        }

        // Step 7: Count entries
        $entryCount = $verifyZip->numFiles;

        // Step 8: Verify entry count matches source files count
        if ($entryCount !== $filesFoundCount) {
            $verifyZip->close();
            @unlink($tmpZipPath);
            $this->error("ABORT: Entry count mismatch! ZIP entries ({$entryCount}) != source files ({$filesFoundCount}). Zero files deleted.");
            return 1;
        }

        $verifyZip->close();

        // Calculate SHA-256 and size
        $sha256 = hash_file('sha256', $tmpZipPath);
        $finalSize = filesize($tmpZipPath);

        // Step 9: Rename/move ZIP to final location
        if (!rename($tmpZipPath, $finalZipPath)) {
            @unlink($tmpZipPath);
            $this->error("ABORT: Failed to move temporary archive to final path: {$finalZipPath}. Zero files deleted.");
            return 1;
        }

        // Update manifest index.json
        $manifest[$targetMonth] = [
            'month' => $targetMonth,
            'filename' => $finalZipName,
            'file_count' => $filesFoundCount,
            'archive_size_bytes' => $finalSize,
            'archive_size_mb' => round($finalSize / 1024 / 1024, 2),
            'sha256' => $sha256,
            'created_at' => Carbon::now()->toDateTimeString(),
        ];
        file_put_contents($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT));

        // Update database rows
        Presensi::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$targetMonth])
            ->update([
                'is_archived' => 1,
                'foto_in_archived' => 1,
                'foto_out_archived' => 1,
            ]);

        // Step 10: Delete individual source files
        $deletedCount = 0;
        foreach ($existingSourceFiles as $filename => $meta) {
            if (@unlink($meta['path'])) {
                $deletedCount++;
            }
        }

        Log::info("attendance-archive: archived month {$targetMonth} ({$filesFoundCount} files, " . round($finalSize / 1024 / 1024, 2) . " MB, SHA-256: {$sha256}). Cleaned {$deletedCount} source files.");

        $this->info("SUCCESS: Archived month {$targetMonth} into {$finalZipName}.");
        $this->line("Files archived:       <fg=green>{$filesFoundCount}</>");
        $this->line("Archive size:         " . round($finalSize / 1024 / 1024, 2) . " MB");
        $this->line("SHA-256 checksum:     <fg=cyan>{$sha256}</>");
        $this->line("Source files deleted: <fg=yellow>{$deletedCount}</>");
        $this->line("Database rows:        <fg=green>100% INTACT (is_archived=1)</>");

        return 0;
    }
}
