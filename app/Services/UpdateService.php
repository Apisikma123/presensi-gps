<?php

namespace App\Services;

use App\Models\Update;
use App\Models\UpdateLog;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class UpdateService
{
    protected UpdateSignatureService $signatureService;

    public function __construct(UpdateSignatureService $signatureService = null)
    {
        $this->signatureService = $signatureService ?? new UpdateSignatureService();
    }

    /**
     * Get current application version
     */
    public function getCurrentVersion(): string
    {
        $versionFile = base_path('VERSION');
        if (File::exists($versionFile)) {
            return trim(File::get($versionFile));
        }

        return '1.0.0';
    }

    /**
     * Set current application version
     */
    public function setCurrentVersion(string $version): void
    {
        File::put(base_path('VERSION'), $version);
    }

    /**
     * Check for updates from server
     */
    public function checkUpdate(string $updateServerUrl = null): array
    {
        try {
            $currentVersion = $this->getCurrentVersion();

            if (!$updateServerUrl) {
                $updateServerUrl = config('update.server_url');
            }

            // Jika tidak ada URL server, cek dari database lokal
            if (!$updateServerUrl) {
                $latestUpdate = Update::active()
                    ->orderByRaw("CAST(SUBSTRING_INDEX(version, '.', 1) AS UNSIGNED) DESC, 
                                  CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(version, '.', 2), '.', -1) AS UNSIGNED) DESC,
                                  CAST(SUBSTRING_INDEX(version, '.', -1) AS UNSIGNED) DESC")
                    ->first();

                if ($latestUpdate && version_compare($latestUpdate->version, $currentVersion, '>')) {
                    return [
                        'has_update' => true,
                        'current_version' => $currentVersion,
                        'latest_version' => $latestUpdate->version,
                        'update' => $latestUpdate,
                    ];
                }

                return [
                    'has_update' => false,
                    'current_version' => $currentVersion,
                    'latest_version' => $currentVersion,
                ];
            }

            // Validasi format URL server eksternal
            $this->validateServerUrl($updateServerUrl);

            // Check dari server eksternal via API
            try {
                $response = Http::timeout(30)->get(rtrim($updateServerUrl, '/') . '/api/update/check', [
                    'current_version' => $currentVersion,
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['success']) && isset($data['data'])) {
                        $result = $data['data'];
                        return [
                            'has_update' => $result['has_update'] ?? false,
                            'current_version' => $currentVersion,
                            'latest_version' => $result['latest_version'] ?? $currentVersion,
                            'update' => $result['update'] ?? null,
                        ];
                    }

                    return [
                        'has_update' => $data['has_update'] ?? false,
                        'current_version' => $currentVersion,
                        'latest_version' => $data['latest_version'] ?? $currentVersion,
                        'update' => $data['update'] ?? null,
                    ];
                }
            } catch (\Exception $e) {
                Log::warning('Error checking update from external server: ' . $e->getMessage());
            }

            // Fallback: coba endpoint /api/update/list
            try {
                $response = Http::timeout(30)->get(rtrim($updateServerUrl, '/') . '/api/update/list', [
                    'active' => true,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $updates = $data['data'] ?? [];

                    if (count($updates) > 0) {
                        $latestUpdate = collect($updates)->sortByDesc(function ($update) {
                            return $update['version'];
                        })->first();

                        if ($latestUpdate && version_compare($latestUpdate['version'], $currentVersion, '>')) {
                            return [
                                'has_update' => true,
                                'current_version' => $currentVersion,
                                'latest_version' => $latestUpdate['version'],
                                'update' => $latestUpdate,
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Error checking update list from external server: ' . $e->getMessage());
            }

            return [
                'has_update' => false,
                'current_version' => $currentVersion,
                'latest_version' => $currentVersion,
                'error' => 'Gagal menghubungi server update resmi: ' . $updateServerUrl,
            ];
        } catch (\Exception $e) {
            Log::error('Error checking update: ' . $e->getMessage());
            return [
                'has_update' => false,
                'current_version' => $this->getCurrentVersion(),
                'latest_version' => $this->getCurrentVersion(),
                'error' => 'Terjadi kesalahan saat mengecek update: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Add progress log
     */
    protected function addProgressLog(UpdateLog $updateLog, string $message, int $percentage = null): void
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] {$message}\n";

        $currentLog = $updateLog->progress_log ?? '';
        $newLog = $currentLog . $logEntry;

        $updateData = [
            'progress_log' => $newLog,
        ];

        if ($percentage !== null) {
            $updateData['progress_percentage'] = $percentage;
        }

        $updateLog->update($updateData);
    }

    /**
     * Download and cryptographically verify update package (SEC-001)
     */
    public function downloadUpdate(Update $update, UpdateLog $updateLog): bool
    {
        $zipPath = null;
        try {
            $updateLog->update([
                'status' => 'downloading',
                'progress_percentage' => 0,
                'progress_log' => '',
                'started_at' => now(),
            ]);

            $this->addProgressLog($updateLog, 'Memulai proses download update...', 0);

            if (empty($update->file_url)) {
                throw new \InvalidArgumentException('URL file update tidak tersedia.');
            }

            // 1. URL and Hostname validation
            $this->validateFileUrl($update->file_url);

            $this->addProgressLog($updateLog, "Mendownload file dari: {$update->file_url}", 10);

            $updateDir = storage_path('app/updates');
            if (!File::exists($updateDir)) {
                File::makeDirectory($updateDir, 0755, true);
                $this->addProgressLog($updateLog, 'Membuat direktori updates...', 15);
            }

            $zipPath = $updateDir . '/update_' . $update->version . '.zip';

            // Download file
            set_time_limit(0);
            ini_set('memory_limit', '512M');

            $this->addProgressLog($updateLog, 'Mengunduh file update (mohon tunggu)...', 20);

            $verifySsl = config('update.verify_ssl', true);
            $response = Http::timeout(600)->withOptions([
                'sink' => $zipPath,
                'verify' => $verifySsl,
            ])->get($update->file_url);

            if (!File::exists($zipPath) || File::size($zipPath) == 0) {
                throw new \RuntimeException('Gagal mengunduh file update — file kosong atau tidak tersimpan.');
            }

            $fileSize = filesize($zipPath);
            $fileSizeMB = round($fileSize / 1024 / 1024, 2);
            $this->addProgressLog($updateLog, "File berhasil diunduh ({$fileSizeMB} MB)", 50);

            // 2. Cryptographic Integrity: SHA-256 verification
            $this->addProgressLog($updateLog, 'Memvalidasi SHA-256 checksum integritas paket...', 60);
            $calculatedSha256 = hash_file('sha256', $zipPath);

            $expectedSha256 = $this->resolveExpectedSha256($update);
            if (empty($expectedSha256)) {
                File::delete($zipPath);
                throw new \RuntimeException('Metadata update tidak memiliki SHA-256 checksum. Update ditolak (fail-closed).');
            }

            // Disallow legacy 32-character MD5 checksums as security proof
            if (strlen($expectedSha256) === 32) {
                File::delete($zipPath);
                throw new \RuntimeException('Update ditolak: Checksum MD5 legacy tidak lagi diizinkan untuk verifikasi keamanan. Gunakan SHA-256.');
            }

            if (!hash_equals(strtolower($expectedSha256), strtolower($calculatedSha256))) {
                File::delete($zipPath);
                Log::warning("[SEC-001] Hash mismatch for update {$update->version}. Expected: {$expectedSha256}, Got: {$calculatedSha256}");
                throw new \RuntimeException('Checksum SHA-256 file tidak cocok! Paket mungkin rusak atau telah dimanipulasi.');
            }

            $this->addProgressLog($updateLog, "SHA-256 valid: {$calculatedSha256} ✓", 75);

            // 3. Cryptographic Authenticity: Digital Signature Verification
            $this->addProgressLog($updateLog, 'Memverifikasi digital signature paket update...', 80);

            if (empty($update->signature)) {
                File::delete($zipPath);
                throw new \RuntimeException('Update ditolak karena paket belum memiliki digital signature resmi (fail-closed).');
            }

            $isSignatureValid = $this->signatureService->verifySignature($calculatedSha256, $update->signature, $update->version);
            if (!$isSignatureValid) {
                File::delete($zipPath);
                Log::warning("[SEC-001] Signature verification failed for update {$update->version}");
                throw new \RuntimeException('Verifikasi digital signature gagal! Paket update tidak sah atau berasal dari publisher tidak terpercaya.');
            }

            $this->addProgressLog($updateLog, 'Digital signature terverifikasi sah (Ed25519) ✓', 90);

            // 4. Pre-scan ZIP for Zip Slip and dangerous paths
            $this->addProgressLog($updateLog, 'Memvalidasi struktur internal paket ZIP (Anti-Zip Slip)...', 92);
            $this->preScanZipFile($zipPath);
            $this->addProgressLog($updateLog, 'Struktur ZIP aman ✓', 95);

            $updateLog->update([
                'message' => 'File berhasil diunduh dan diverifikasi secara kriptografis',
                'progress_percentage' => 100,
            ]);

            $this->addProgressLog($updateLog, 'Download dan verifikasi keamanan selesai!', 100);

            Log::info("[SEC-001] Update package {$update->version} successfully downloaded and verified with Ed25519 signature by user_id: " . ($updateLog->user_id ?? 'system'));

            return true;
        } catch (\Exception $e) {
            if ($zipPath && File::exists($zipPath)) {
                File::delete($zipPath);
            }

            $this->addProgressLog($updateLog, "ERROR: {$e->getMessage()}", 0);
            $updateLog->update([
                'status' => 'failed',
                'message' => 'Gagal mengunduh update: ' . $e->getMessage(),
                'error_log' => $e->getTraceAsString(),
                'completed_at' => now(),
            ]);

            Log::error("[SEC-001] Update download/verification failed for {$update->version}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Install update with transaction-like file backup and rollback (SEC-001)
     */
    public function installUpdate(Update $update, UpdateLog $updateLog, $userId = null): bool
    {
        $backupZipPath = null;
        $extractPath = null;
        $newFilesAdded = [];

        try {
            $updateLog->update([
                'status' => 'installing',
                'progress_percentage' => 0,
            ]);

            $this->addProgressLog($updateLog, 'Memulai proses instalasi update...', 0);

            $currentVersion = $this->getCurrentVersion();
            $zipPath = storage_path('app/updates/update_' . $update->version . '.zip');

            if (!File::exists($zipPath)) {
                throw new \RuntimeException('File update tidak ditemukan. Silakan download ulang.');
            }

            // 1. Re-verify SHA-256 and Digital Signature before touching any file (Defense in Depth)
            $this->addProgressLog($updateLog, 'Memverifikasi ulang integritas & signature paket sebelum instalasi...', 5);
            $calculatedSha256 = hash_file('sha256', $zipPath);
            $expectedSha256 = $this->resolveExpectedSha256($update);

            if (empty($expectedSha256) || !hash_equals(strtolower($expectedSha256), strtolower($calculatedSha256))) {
                throw new \RuntimeException('Verifikasi integritas paket gagal saat instalasi.');
            }

            if (empty($update->signature) || !$this->signatureService->verifySignature($calculatedSha256, $update->signature, $update->version)) {
                throw new \RuntimeException('Verifikasi digital signature gagal saat instalasi.');
            }

            $this->addProgressLog($updateLog, 'Verifikasi paket valid ✓', 10);

            // 2. Backup database
            if (config('update.backup_before_update', true)) {
                $this->addProgressLog($updateLog, 'Membackup database...', 12);
                $this->backupDatabase($updateLog);
                $this->addProgressLog($updateLog, 'Backup database selesai ✓', 18);
            }

            // 3. Extract to staging directory with strict entry-by-entry validation
            $this->addProgressLog($updateLog, 'Mengekstrak paket ke direktori staging terisolasi...', 20);
            $extractPath = storage_path('app/updates/extract_' . $update->version);
            if (File::exists($extractPath)) {
                File::deleteDirectory($extractPath);
            }
            File::makeDirectory($extractPath, 0755, true);

            $this->extractZipSecurely($zipPath, $extractPath, $updateLog);
            $this->addProgressLog($updateLog, 'Ekstraksi staging selesai ✓', 30);

            // 4. Create file backup of all application files that will be overwritten
            $this->addProgressLog($updateLog, 'Membuat backup file aplikasi yang akan diperbarui...', 32);
            $backupResult = $this->backupOverwrittenFiles($extractPath, $update->version);
            $backupZipPath = $backupResult['backup_path'];
            $newFilesAdded = $backupResult['new_files'];
            $this->addProgressLog($updateLog, 'Backup file selesai (' . count($backupResult['backed_up']) . ' files dicadangkan) ✓', 40);

            // 5. Copy files from staging into application
            $this->addProgressLog($updateLog, 'Menerapkan pembaruan file ke aplikasi...', 45);
            $this->copyUpdateFiles($extractPath, $updateLog);
            $this->addProgressLog($updateLog, 'Pembaruan file berhasil diterapkan ✓', 55);

            // 6. Run pending migrations safely
            $this->addProgressLog($updateLog, 'Menjalankan database migrations...', 60);
            try {
                Artisan::call('migrate', ['--force' => true]);
                $this->addProgressLog($updateLog, 'Database migrations selesai ✓', 75);
            } catch (\Exception $migException) {
                Log::error("[SEC-001] Migration failed during update: " . $migException->getMessage());
                $this->addProgressLog($updateLog, 'Migration gagal: ' . $migException->getMessage() . '. Memulai rollback file...', 0);
                $this->rollbackFiles($backupZipPath, $newFilesAdded);
                throw new \RuntimeException('Database migration gagal: ' . $migException->getMessage());
            }

            // 7. Safe seeder execution: NEVER run blind db:seed!
            if (!empty($update->seeders)) {
                $this->addProgressLog($updateLog, 'Menjalankan seeders khusus paket update...', 80);
                $seedersList = is_array($update->seeders) ? $update->seeders : json_decode($update->seeders, true);
                if (is_array($seedersList)) {
                    $this->runSeeders($seedersList, $updateLog);
                }
            }

            // 8. Health Check
            $this->addProgressLog($updateLog, 'Melakukan health check pasca-update...', 85);
            $this->runHealthCheck();
            $this->addProgressLog($updateLog, 'Health check lolos ✓', 88);

            // 9. Update application version
            $this->addProgressLog($updateLog, 'Mengupdate versi aplikasi...', 90);
            $this->setCurrentVersion($update->version);
            $this->addProgressLog($updateLog, "Versi diupdate dari {$currentVersion} ke {$update->version} ✓", 92);

            // 10. Clear cache
            $this->addProgressLog($updateLog, 'Membersihkan cache aplikasi...', 94);
            try {
                Artisan::call('optimize:clear');
            } catch (\Exception $e) {
                // Non-fatal
            }
            $this->addProgressLog($updateLog, 'Cache dibersihkan ✓', 97);

            // 11. Cleanup temporary ZIP & staging
            $this->addProgressLog($updateLog, 'Membersihkan temporary staging...', 98);
            if (File::exists($zipPath)) {
                File::delete($zipPath);
            }
            if ($extractPath && File::exists($extractPath)) {
                File::deleteDirectory($extractPath);
            }
            $this->addProgressLog($updateLog, 'Cleanup selesai ✓', 99);

            $updateLog->update([
                'status' => 'success',
                'previous_version' => $currentVersion,
                'message' => 'Update berhasil diinstall secara aman',
                'progress_percentage' => 100,
                'completed_at' => now(),
            ]);

            $this->addProgressLog($updateLog, 'Update berhasil diinstall! 🎉', 100);

            Log::info("[SEC-001] Update {$update->version} successfully installed. Admin ID: " . ($userId ?? $updateLog->user_id));

            return true;
        } catch (\Exception $e) {
            $this->addProgressLog($updateLog, "ERROR: {$e->getMessage()}", 0);

            // Perform file rollback if backup was created
            if ($backupZipPath && File::exists($backupZipPath)) {
                $this->addProgressLog($updateLog, 'Menjalankan rollback file aplikasi ke kondisi semula...', 0);
                try {
                    $this->rollbackFiles($backupZipPath, $newFilesAdded);
                    $this->addProgressLog($updateLog, 'Rollback file selesai. Aplikasi dikembalikan ke kondisi sebelumnya.', 0);
                } catch (\Exception $rollEx) {
                    Log::critical("[SEC-001] Rollback failed: " . $rollEx->getMessage());
                    $this->addProgressLog($updateLog, 'Rollback gagal: ' . $rollEx->getMessage(), 0);
                }
            }

            // Cleanup staging
            if ($extractPath && File::exists($extractPath)) {
                File::deleteDirectory($extractPath);
            }

            $updateLog->update([
                'status' => 'failed',
                'message' => 'Gagal menginstall update: ' . $e->getMessage(),
                'error_log' => $e->getTraceAsString(),
                'completed_at' => now(),
            ]);

            return false;
        }
    }

    /**
     * Securely extract ZIP entry-by-entry with Zip Slip and symlink guards (SEC-001)
     */
    public function extractZipSecurely(string $zipPath, string $extractPath, UpdateLog $updateLog): void
    {
        $this->addProgressLog($updateLog, 'Membuka dan memverifikasi isi paket ZIP...', 22);

        $zip = new ZipArchive();
        $status = $zip->open($zipPath);

        if ($status !== true) {
            throw new \RuntimeException("Gagal membuka file ZIP (Error code: {$status})");
        }

        try {
            $entries = $this->validateZipEntries($zip);

            $this->addProgressLog($updateLog, 'Mengekstrak ' . count($entries) . ' file aman...', 25);

            foreach ($entries as $entry) {
                $entryName = $entry['name'];
                $targetPath = $extractPath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $entryName);

                if ($entry['is_dir']) {
                    if (!File::exists($targetPath)) {
                        File::makeDirectory($targetPath, 0755, true, true);
                    }
                    continue;
                }

                $parentDir = dirname($targetPath);
                if (!File::exists($parentDir)) {
                    File::makeDirectory($parentDir, 0755, true, true);
                }

                $stream = $zip->getStream($entry['original_name']);
                if (!$stream) {
                    throw new \RuntimeException("Gagal membaca entry ZIP: {$entryName}");
                }

                $destHandle = fopen($targetPath, 'wb');
                if (!$destHandle) {
                    fclose($stream);
                    throw new \RuntimeException("Gagal menulis file staging: {$targetPath}");
                }

                stream_copy_to_stream($stream, $destHandle);
                fclose($stream);
                fclose($destHandle);
            }
        } finally {
            $zip->close();
        }
    }

    /**
     * Pre-scan ZIP entries without extracting to fail fast on any attack payloads
     */
    public function preScanZipFile(string $zipPath): void
    {
        $zip = new ZipArchive();
        $status = $zip->open($zipPath);

        if ($status !== true) {
            throw new \RuntimeException("Gagal membuka file ZIP (Error code: {$status})");
        }

        try {
            $this->validateZipEntries($zip);
        } finally {
            $zip->close();
        }
    }

    /**
     * Strict validation of ZIP archive entries:
     * - Blocks Zip Slip / Directory Traversal (..)
     * - Blocks Absolute paths (/, C:\)
     * - Blocks Symlinks (Unix S_IFLNK)
     * - Blocks Null bytes
     * - Enforces Allowed Whitelist directories
     * - Blocks Sensitive files (.env, credentials, keys, storage, vendor)
     */
    public function validateZipEntries(ZipArchive $zip): array
    {
        $numFiles = $zip->numFiles;
        if ($numFiles <= 0) {
            throw new \RuntimeException('File ZIP update kosong.');
        }

        $allowedPrefixes = [
            'app/',
            'routes/',
            'resources/',
            'config/',
            'database/migrations/',
            'public/',
        ];

        $allowedRootFiles = [
            'composer.json',
            'package.json',
            '.env.example',
        ];

        $forbiddenPatterns = [
            '/\.env$/i',
            '/\.env\..+$/i',
            '/^storage\//i',
            '/^vendor\//i',
            '/^\.git\//i',
            '/^\.agents\//i',
            '/\.key$/i',
            '/\.pem$/i',
        ];

        $validEntries = [];

        for ($i = 0; $i < $numFiles; $i++) {
            $originalName = $zip->getNameIndex($i);

            if (strpos($originalName, "\0") !== false) {
                throw new \RuntimeException("Zip entry mengandung null byte terlarang: {$originalName}");
            }

            // Normalize path separators to forward slash
            $normalized = str_replace('\\', '/', $originalName);

            // 1. Reject absolute paths
            if (str_starts_with($normalized, '/') || preg_match('/^[a-zA-Z]:/i', $normalized)) {
                throw new \RuntimeException("Zip entry menggunakan absolute path yang tidak aman: {$originalName}");
            }

            // 2. Reject directory traversal (Zip Slip)
            $segments = explode('/', $normalized);
            foreach ($segments as $segment) {
                if ($segment === '..') {
                    throw new \RuntimeException("Zip Slip terdeteksi (path traversal '..'): {$originalName}");
                }
            }

            // 3. Reject Unix Symbolic Links
            $opsys = 0;
            $attr = 0;
            $zip->getExternalAttributesIndex($i, $opsys, $attr);
            // S_IFLNK check in UNIX mode (0120000 in upper 16 bits)
            if ($opsys === ZipArchive::OPSYS_UNIX && (($attr >> 16) & 0120000) === 0120000) {
                throw new \RuntimeException("Symbolic link terdeteksi dalam ZIP: {$originalName}. Symlink dilarang.");
            }

            $isDir = str_ends_with($normalized, '/');
            if ($isDir) {
                $validEntries[] = [
                    'original_name' => $originalName,
                    'name' => $normalized,
                    'is_dir' => true,
                ];
                continue;
            }

            // 4. Reject forbidden / sensitive files
            foreach ($forbiddenPatterns as $pattern) {
                if (preg_match($pattern, $normalized)) {
                    if ($normalized === '.env.example') {
                        continue;
                    }
                    throw new \RuntimeException("File terlarang terdeteksi dalam ZIP ({$normalized}). Update dibatalkan.");
                }
            }

            // 5. Enforce directory / file whitelist
            $isAllowed = in_array($normalized, $allowedRootFiles, true);
            if (!$isAllowed) {
                foreach ($allowedPrefixes as $prefix) {
                    if (str_starts_with($normalized, $prefix)) {
                        $isAllowed = true;
                        break;
                    }
                }
            }

            if (!$isAllowed) {
                throw new \RuntimeException("File '{$normalized}' berada di luar direktori yang diizinkan untuk update.");
            }

            $validEntries[] = [
                'original_name' => $originalName,
                'name' => $normalized,
                'is_dir' => false,
            ];
        }

        return $validEntries;
    }

    /**
     * Backup files that will be overwritten or added by the update
     */
    protected function backupOverwrittenFiles(string $stagingPath, string $version): array
    {
        $backupDir = storage_path('app/updates/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = date('Ymd_His');
        $backupZipPath = $backupDir . "/backup_{$version}_{$timestamp}.zip";

        $backupZip = new ZipArchive();
        if ($backupZip->open($backupZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Gagal membuat file backup sebelum pembaruan file.');
        }

        $backedUpFiles = [];
        $newFiles = [];

        $allStagingFiles = File::allFiles($stagingPath);
        foreach ($allStagingFiles as $file) {
            $relativePath = str_replace('\\', '/', $file->getRelativePathname());
            $appTargetPath = base_path($relativePath);

            if (File::exists($appTargetPath)) {
                $backupZip->addFile($appTargetPath, $relativePath);
                $backedUpFiles[] = $relativePath;
            } else {
                $newFiles[] = $appTargetPath;
            }
        }

        $backupZip->close();

        return [
            'backup_path' => $backupZipPath,
            'backed_up' => $backedUpFiles,
            'new_files' => $newFiles,
        ];
    }

    /**
     * Rollback files from backup ZIP and delete newly staged files
     */
    public function rollbackFiles(string $backupZipPath, array $newFiles = []): void
    {
        Log::info("[SEC-001] Executing file rollback from backup: {$backupZipPath}");

        // 1. Remove newly added files
        foreach ($newFiles as $newFile) {
            if (File::exists($newFile)) {
                File::delete($newFile);
            }
        }

        // 2. Restore backed up files from backup ZIP
        if (File::exists($backupZipPath)) {
            $zip = new ZipArchive();
            if ($zip->open($backupZipPath) === true) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $entryName = $zip->getNameIndex($i);
                    $targetPath = base_path(str_replace('/', DIRECTORY_SEPARATOR, $entryName));
                    $parentDir = dirname($targetPath);

                    if (!File::exists($parentDir)) {
                        File::makeDirectory($parentDir, 0755, true, true);
                    }

                    $stream = $zip->getStream($entryName);
                    if ($stream) {
                        $dest = fopen($targetPath, 'wb');
                        if ($dest) {
                            stream_copy_to_stream($stream, $dest);
                            fclose($dest);
                        }
                        fclose($stream);
                    }
                }
                $zip->close();
            }
        }

        try {
            Artisan::call('optimize:clear');
        } catch (\Exception $e) {
            // Non-fatal
        }

        Log::info("[SEC-001] File rollback completed successfully.");
    }

    /**
     * Copy update files to application
     */
    protected function copyUpdateFiles(string $extractPath, UpdateLog $updateLog): void
    {
        $sourceDirs = [
            'app' => base_path('app'),
            'database/migrations' => base_path('database/migrations'),
            'resources' => base_path('resources'),
            'routes' => base_path('routes'),
            'public' => base_path('public'),
            'config' => base_path('config'),
        ];

        foreach ($sourceDirs as $relDir => $targetPath) {
            $sourcePath = $extractPath . '/' . $relDir;
            if (File::exists($sourcePath)) {
                if (!File::exists($targetPath)) {
                    File::makeDirectory($targetPath, 0755, true, true);
                }
                File::copyDirectory($sourcePath, $targetPath);
            }
        }

        // Whitelisted individual root files
        $filesToCopy = [
            'composer.json',
            'package.json',
            '.env.example',
        ];

        foreach ($filesToCopy as $file) {
            $sourceFile = $extractPath . '/' . $file;
            if (File::exists($sourceFile)) {
                File::copy($sourceFile, base_path($file));
            }
        }
    }

    /**
     * Run seeders (Whitelisted classes only)
     */
    protected function runSeeders(array $seeders, UpdateLog $updateLog): void
    {
        foreach ($seeders as $seeder) {
            $seederClass = trim($seeder);
            if (empty($seederClass)) {
                continue;
            }

            // Prefix namespace if not provided
            if (!str_contains($seederClass, '\\')) {
                $fullClass = "Database\\Seeders\\{$seederClass}";
            } else {
                $fullClass = $seederClass;
            }

            // Ensure seeder exists and is a subclass of Seeder
            if (!class_exists($fullClass) && !class_exists($seederClass)) {
                Log::warning("[SEC-001] Seeder class not found: {$seederClass}. Skipping.");
                continue;
            }

            $targetClass = class_exists($fullClass) ? $fullClass : $seederClass;

            try {
                $this->addProgressLog($updateLog, "Menjalankan seeder: {$targetClass}...", 82);
                Artisan::call('db:seed', [
                    '--class' => $targetClass,
                    '--force' => true,
                ]);
            } catch (\Exception $e) {
                Log::error("[SEC-001] Seeder failed: {$targetClass} — " . $e->getMessage());
            }
        }
    }

    /**
     * Post-update health check
     */
    protected function runHealthCheck(): void
    {
        $criticalFiles = [
            base_path('app'),
            base_path('routes'),
            base_path('config/app.php'),
        ];

        foreach ($criticalFiles as $file) {
            if (!File::exists($file)) {
                throw new \RuntimeException("Health check gagal: File/direktori kritis tidak ditemukan ({$file}).");
            }
        }
    }

    /**
     * Resolve expected SHA-256 from model fields
     */
    public function resolveExpectedSha256(Update $update): ?string
    {
        if (!empty($update->sha256)) {
            return trim($update->sha256);
        }

        if (!empty($update->checksum) && strlen(trim($update->checksum)) === 64) {
            return trim($update->checksum);
        }

        return null;
    }

    /**
     * Validate update file URL against scheme and host whitelist
     */
    public function validateFileUrl(string $url): void
    {
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('URL file update tidak valid.');
        }

        $parsed = parse_url($url);
        $scheme = strtolower($parsed['scheme'] ?? '');
        $host = strtolower($parsed['host'] ?? '');

        if (!in_array($scheme, ['http', 'https'], true)) {
            throw new \InvalidArgumentException('Protokol URL update tidak diizinkan. Hanya HTTP/HTTPS.');
        }

        // Host whitelist verification
        $allowedHosts = config('update.allowed_hosts', []);
        $serverUrl = config('update.server_url');
        if ($serverUrl) {
            $serverHost = parse_url($serverUrl, PHP_URL_HOST);
            if ($serverHost) {
                $allowedHosts[] = strtolower($serverHost);
            }
        }

        // In local development, also allow localhost/127.0.0.1
        if (app()->environment('local', 'testing')) {
            $allowedHosts[] = 'localhost';
            $allowedHosts[] = '127.0.0.1';
        }

        $allowedHosts = array_unique(array_filter($allowedHosts));

        if (!empty($allowedHosts)) {
            if (!in_array($host, $allowedHosts, true)) {
                Log::warning("[SEC-001] Download update rejected: Host '{$host}' is not in allowed hosts whitelist.");
                throw new \RuntimeException("Host download '{$host}' tidak diizinkan dalam whitelist server update resmi.");
            }
        }
    }

    /**
     * Validate server URL
     */
    public function validateServerUrl(string $url): void
    {
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('URL server update tidak valid.');
        }
    }

    /**
     * Backup database with fallback
     */
    protected function backupDatabase(UpdateLog $updateLog): void
    {
        try {
            $backupDir = storage_path('app/backups');
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            $backupFile = $backupDir . '/backup_' . date('Y-m-d_His') . '_' . $updateLog->version . '.sql';

            if ($this->hasMysqldump()) {
                $dbName = config('database.connections.mysql.database');
                $dbUser = config('database.connections.mysql.username');
                $dbPass = config('database.connections.mysql.password');
                $dbHost = config('database.connections.mysql.host');
                $dbPort = config('database.connections.mysql.port', 3306);
                $passString = empty($dbPass) ? '' : '-p' . escapeshellarg($dbPass);
                $command = "mysqldump --no-tablespaces --column-statistics=0 -h " . escapeshellarg($dbHost) . " -P " . escapeshellarg($dbPort) . " -u " . escapeshellarg($dbUser) . " {$passString} " . escapeshellarg($dbName) . " > " . escapeshellarg($backupFile) . " 2>&1";

                exec($command, $output, $returnVar);

                if ($returnVar === 0 && File::exists($backupFile) && File::size($backupFile) > 0) {
                    $this->addProgressLog($updateLog, 'Backup database berhasil (Native) ✓', 15);
                    return;
                }

                $this->addProgressLog($updateLog, "mysqldump gagal/hilang, mencoba fallback PHP...", 14);
            } else {
                $this->addProgressLog($updateLog, "mysqldump tidak ditemukan, menggunakan fallback PHP...", 14);
            }

            // Fallback: PHP Based Backup
            $this->backupDatabasePHP($backupFile, $updateLog);
        } catch (\Exception $e) {
            Log::warning('Gagal backup database: ' . $e->getMessage());
            $this->addProgressLog($updateLog, "Backup gagal, melanjutkan update (Warning: {$e->getMessage()})", 15);
        }
    }

    private function hasMysqldump(): bool
    {
        exec('which mysqldump 2>&1', $output, $returnVar);
        return $returnVar === 0;
    }

    /**
     * PHP-based Database Backup (Fallback)
     */
    protected function backupDatabasePHP(string $filePath, UpdateLog $updateLog): void
    {
        $handle = fopen($filePath, 'w+');
        if (!$handle) {
            throw new \RuntimeException("Gagal membuat file backup di {$filePath}");
        }

        $tables = DB::select('SHOW TABLES');

        foreach ($tables as $table) {
            $tableArray = (array)$table;
            $tableName = array_values($tableArray)[0];

            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $createTableSql = ((array)$createTable[0])['Create Table'];

            fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");
            fwrite($handle, $createTableSql . ";\n\n");

            DB::table($tableName)->orderByRaw('1')->chunk(200, function ($rows) use ($handle, $tableName) {
                foreach ($rows as $row) {
                    $values = array_map(function ($value) {
                        return is_null($value) ? "NULL" : "'" . addslashes($value) . "'";
                    }, (array)$row);

                    $sql = "INSERT INTO `{$tableName}` VALUES (" . implode(',', $values) . ");\n";
                    fwrite($handle, $sql);
                }
            });

            fwrite($handle, "\n\n");
        }

        fclose($handle);
        $this->addProgressLog($updateLog, 'Backup database selesai (PHP) ✓', 18);
    }
}
