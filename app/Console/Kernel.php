<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jalankan worker queue untuk memproses job antrian sesuai interval cron shared hosting
        $schedule->command('queue:work --queue=default --sleep=3 --tries=3 --stop-when-empty')
            ->everyThirtyMinutes()
            ->withoutOverlapping();

        // Cek dan generate otomatis status Tanpa Keterangan / Alpha ('a') setelah jam shift berakhir
        $schedule->command('presensi:auto-alpha')
            ->hourly()
            ->withoutOverlapping();

        // Pengarsipan foto presensi lama (>12 bulan) ke ZIP private (Max 1 bulan tertua per run, aman shared hosting)
        $schedule->command('maintenance:archive-attendance-photos --execute')
            ->dailyAt('02:00')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
