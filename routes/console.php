<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use App\Models\User;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('audit:routes {--spa}', function () {
    $user = User::role('super admin')->first() ?? User::first();
    if (!$user) {
        $this->error('User not found');
        return 1;
    }
    Auth::login($user);
    View::share('errors', new \Illuminate\Support\ViewErrorBag());

    $this->info("Logged in as: {$user->name} ({$user->roles->pluck('name')->first()}) [Blade Navigation]");

    $benchmarks = [
        'Dashboard' => function() {
            $c = app(\App\Http\Controllers\DashboardController::class);
            $view = $c->index(new Request());
            return $view->render();
        },
        'Employees (Karyawan)' => function() {
            $c = app(\App\Http\Controllers\KaryawanController::class);
            $view = $c->index(new Request());
            return $view->render();
        },
        'Attendance (Presensi)' => function() {
            $c = app(\App\Http\Controllers\PresensiController::class);
            $view = $c->index(new Request());
            return $view->render();
        },
        'Leave (Izin Absen)' => function() {
            $c = app(\App\Http\Controllers\IzinabsenController::class);
            $view = $c->index(new Request());
            return $view->render();
        },
        'Reports (Laporan Presensi)' => function() {
            $c = app(\App\Http\Controllers\LaporanController::class);
            $view = $c->presensi(new Request());
            return $view->render();
        },
        'Profile' => function() {
            $c = app(\App\Http\Controllers\ProfileController::class);
            $view = $c->editprofile();
            return $view->render();
        },
    ];

    $results = [];

    foreach ($benchmarks as $name => $fn) {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $start = microtime(true);
        try {
            $html = $fn();
            $duration = round((microtime(true) - $start) * 1000, 1);
            $queries = DB::getQueryLog();
            $qCount = count($queries);
            $qTime = round(array_sum(array_column($queries, 'time')), 1);
            $sizeKb = round(strlen($html) / 1024, 1);

            // Check for duplicates
            $queryStrings = array_column($queries, 'query');
            $duplicates = count($queryStrings) - count(array_unique($queryStrings));

            $results[] = [
                'Route' => $name,
                'Total (ms)' => $duration . ' ms',
                'Queries' => $qCount,
                'Duplicates' => $duplicates > 0 ? "⚠️ {$duplicates}" : "0",
                'DB Time' => $qTime . ' ms',
                'Payload' => $sizeKb . ' KB',
            ];

            // If there are slow queries > 15ms
            $slowQueries = array_filter($queries, fn($q) => $q['time'] > 15);
            if (!empty($slowQueries)) {
                $this->warn("Slow queries in {$name}:");
                foreach ($slowQueries as $sq) {
                    $this->line("   [{$sq['time']} ms] " . substr(preg_replace('/\s+/', ' ', $sq['query']), 0, 130) . "...");
                }
            }
        } catch (\Throwable $e) {
            $results[] = [
                'Route' => $name,
                'Total (ms)' => 'ERROR',
                'Queries' => '-',
                'Duplicates' => '-',
                'DB Time' => '-',
                'Payload' => substr($e->getMessage(), 0, 40),
            ];
            $this->error("Error on {$name}: " . $e->getMessage());
        }
    }

    $this->table(['Route', 'Total Time', 'Queries', 'Duplicates', 'DB Time', 'Render Size'], $results);
    return 0;
});
