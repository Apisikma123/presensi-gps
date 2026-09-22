<?php

namespace App\Console\Commands;

use App\Services\AttendanceService;
use Illuminate\Console\Command;

class GenerateAutoAlphaPresensi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'presensi:auto-alpha 
                            {--date= : Tanggal target presensi (format: YYYY-MM-DD), default hari ini}
                            {--dry-run : Uji coba kalkulasi tanpa menyimpan perubahan ke database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis menandai Tanpa Keterangan / Alpha (status = a) untuk karyawan yang tidak hadir setelah jam shift berakhir.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $date = $this->option('date');
        $dryRun = (bool)$this->option('dry-run');

        $this->info('🚀 Memulai proses pengecekan otomatis Tanpa Keterangan (Alpha)...');
        if ($dryRun) {
            $this->warn('⚠️ Mode DRY-RUN aktif: Data tidak akan disimpan ke database.');
        }

        $result = AttendanceService::generateAutoAlpha($date, $dryRun);

        if (!$result['success']) {
            $this->error('❌ ' . $result['message']);
            return Command::FAILURE;
        }

        $this->table(
            ['Parameter', 'Nilai'],
            [
                ['Tanggal Target', $result['date']],
                ['Kandidat Terdeteksi', $result['total_candidates']],
                ['Status Berhasil Ditandai Alpha', $result['marked_alpha']],
                ['Mode', $dryRun ? 'Simulasi (Dry Run)' : 'Produksi (Tersimpan)'],
            ]
        );

        $this->info('✅ ' . $result['message']);
        return Command::SUCCESS;
    }
}
