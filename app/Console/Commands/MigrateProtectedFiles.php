<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MigrateProtectedFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:migrate-protected {--move : Move files to private storage and remove from public}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate sensitive biometric and SID files from public storage to private storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $move = $this->option('move');
        $action = $move ? 'Pemindahan (Move)' : 'Penyalinan (Copy)';
        $this->info("Memulai migrasi file sensitif ke private storage [Mode: {$action}]...");

        $publicDisk = Storage::disk('public');
        $privateDisk = Storage::disk('private');

        $totalMigrated = 0;

        // 1. Migrasi SID files
        $this->info('1. Memeriksa file SID...');
        if ($publicDisk->exists('uploads/sid')) {
            $sidFiles = $publicDisk->files('uploads/sid');
            $this->info("Ditemukan " . count($sidFiles) . " file SID di storage publik.");

            foreach ($sidFiles as $file) {
                $filename = basename($file);
                $targetPath = 'uploads/sid/' . $filename;
                $content = $publicDisk->get($file);
                $privateDisk->put($targetPath, $content);

                // Verifikasi validitas sebelum hapus file sumber
                if ($privateDisk->exists($targetPath) && $privateDisk->size($targetPath) === $publicDisk->size($file)) {
                    $totalMigrated++;
                    if ($move) {
                        $publicDisk->delete($file);
                    }
                } else {
                    $this->error("Gagal memvalidasi salinan private untuk {$file}! File publik dipertahankan.");
                }
            }
            if ($move && count($publicDisk->files('uploads/sid')) === 0) {
                $publicDisk->deleteDirectory('uploads/sid');
            }
            $this->info("Selesai memproses file SID.");
        } else {
            $this->line("Direktori uploads/sid di public storage kosong atau tidak ditemukan.");
        }

        // 2. Migrasi Face Recognition biometric files
        $this->info('2. Memeriksa file Face Recognition...');
        if ($publicDisk->exists('uploads/facerecognition')) {
            $directories = $publicDisk->directories('uploads/facerecognition');
            $this->info("Ditemukan " . count($directories) . " direktori wajah di storage publik.");

            foreach ($directories as $dir) {
                $folderName = basename($dir);
                $faceFiles = $publicDisk->files($dir);

                foreach ($faceFiles as $file) {
                    $filename = basename($file);
                    $targetPath = 'uploads/facerecognition/' . $folderName . '/' . $filename;
                    $content = $publicDisk->get($file);
                    $privateDisk->put($targetPath, $content);

                    // Verifikasi integritas ukuran sebelum hapus sumber
                    if ($privateDisk->exists($targetPath) && $privateDisk->size($targetPath) === $publicDisk->size($file)) {
                        $totalMigrated++;
                        if ($move) {
                            $publicDisk->delete($file);
                        }
                    } else {
                        $this->error("Gagal memvalidasi salinan private untuk {$file}! File publik dipertahankan.");
                    }
                }

                if ($move && count($publicDisk->files($dir)) === 0) {
                    $publicDisk->deleteDirectory($dir);
                }
            }

            if ($move && count($publicDisk->allFiles('uploads/facerecognition')) === 0) {
                $publicDisk->deleteDirectory('uploads/facerecognition');
            }
            $this->info("Selesai memproses file Face Recognition.");
        } else {
            $this->line("Direktori uploads/facerecognition di public storage kosong atau tidak ditemukan.");
        }

        $this->info("Migrasi selesai! Sebanyak {$totalMigrated} file berhasil diproses ke storage private.");
        if (!$move) {
            $this->comment("Catatan: File di storage publik tetap dipertahankan sebagai fallback. Gunakan opsi --move jika ingin membersihkan file di storage publik.");
        }

        return Command::SUCCESS;
    }
}
