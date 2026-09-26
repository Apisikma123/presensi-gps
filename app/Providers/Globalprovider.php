<?php

namespace App\Providers;

use App\Models\Izinabsen;
use App\Models\Izincuti;
use App\Models\Izinsakit;
use App\Models\PresensiDispensasi;
use App\Models\Pengaturanumum;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class Globalprovider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(Guard $auth): void
    {
        try {
            $companySetting = \App\Models\CompanySetting::getSetting();
            View::share('company_setting', $companySetting);

            $settings = Pengaturanumum::getSetting();
            View::share('general_setting', $settings);

            $logoPath = Cache::remember('global_app_logo_relative_path', 3600, function () use ($settings, $companySetting) {
                $logoFile = $companySetting?->logo ?: $settings?->logo;
                if (!empty($logoFile)) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists('logo/' . $logoFile)) {
                        return 'storage/logo/' . $logoFile;
                    }
                    if (file_exists(public_path($logoFile))) {
                        return $logoFile;
                    }
                }
                if (file_exists(public_path('assets/login/images/logoweb-1.png'))) {
                    return 'assets/login/images/logoweb-1.png';
                }
                if (file_exists(public_path('logo.png'))) {
                    return 'logo.png';
                }
                return null;
            });
            View::share('app_logo_url', $logoPath ? asset($logoPath) : null);

            // Dynamic Brand Palette via ThemeResolver with auto-contrast
            $theme = \App\Services\ThemeResolver::resolve();
            View::share('theme', $theme);

            $t = [
                'primary' => $theme['primary'],
                'primary_light' => $theme['secondary'],
                'bg_body' => $theme['canvas'],
                'surface' => $theme['surface'],
                'text_primary' => $theme['text_primary'],
                'text_secondary' => $theme['text_secondary'],
                'border' => $theme['border'],
                'amber' => $theme['warning'],
                'crimson' => $theme['danger'],
                'matcha' => $theme['accent'],
                'primary_contrast' => $theme['primary_contrast'],
            ];
            View::share('t', $t);
            View::share('isDark', false);
        } catch (\Exception $e) {
            View::share('company_setting', null);
            View::share('general_setting', null);
            $defaultTheme = [
                'primary' => '#3C2A21',
                'primary_rgb' => '60, 42, 33',
                'primary_contrast' => '#FFFFFF',
                'primary_hover' => '#2A1D17',
                'primary_soft' => 'rgba(60, 42, 33, 0.08)',
                'primary_border' => 'rgba(60, 42, 33, 0.18)',
                'secondary' => '#634832',
                'secondary_rgb' => '99, 72, 50',
                'secondary_contrast' => '#FFFFFF',
                'secondary_hover' => '#4D3827',
                'secondary_soft' => 'rgba(99, 72, 50, 0.08)',
                'accent' => '#4A6741',
                'accent_rgb' => '74, 103, 65',
                'success' => '#4A6741',
                'danger' => '#BA1A1A',
                'warning' => '#B45309',
                'info' => '#2563EB',
                'canvas' => '#FAF9F8',
                'surface' => '#FFFFFF',
                'text_primary' => '#1A1C1C',
                'text_secondary' => '#755841',
                'border' => 'rgba(60, 42, 33, 0.08)',
                'border_hover' => 'rgba(60, 42, 33, 0.16)',
            ];
            View::share('theme', $defaultTheme);
            View::share('t', [
                'primary' => '#3C2A21',
                'primary_light' => '#634832',
                'bg_body' => '#FAF9F8',
                'surface' => '#FFFFFF',
                'text_primary' => '#1A1C1C',
                'text_secondary' => '#755841',
                'border' => 'rgba(60, 42, 33, 0.08)',
                'amber' => '#B45309',
                'crimson' => '#BA1A1A',
                'matcha' => '#4A6741',
                'primary_contrast' => '#FFFFFF',
            ]);
            View::share('isDark', false);
        }

        view()->composer('*', function ($view) use ($auth) {
            static $composed = false;
            if ($composed) {
                return;
            }
            $composed = true;

            if ($auth->check()) {
                /** @var \App\Models\User $user */
                $user = $auth->user();

                if ($user->hasRole('karyawan')) {
                    $shareddata = [
                        'notifikasi_izinabsen' => 0,
                        'notifikasi_izinsakit' => 0,
                        'notifikasi_izincuti' => 0,
                        'notifikasi_dispensasi' => 0,
                        'notifikasi_ajuan_absen' => 0,
                        'notifikasi_unread' => 0,
                        'notifications_list' => collect([]),
                        'data_izin' => collect([]),
                        'data_reimbursement_pending' => collect([]),
                    ];
                    View::share($shareddata);
                    return;
                }

                $cacheKey = 'user_global_notif_' . $user->id;

                $shareddata = Cache::remember($cacheKey, 60, function () use ($user) {
                    $isSuperAdmin = $user->isSuperAdmin();
                    $userCabangs = $isSuperAdmin ? [] : $user->getCabangCodes();
                    $userDepartemens = $isSuperAdmin ? [] : $user->getDepartemenCodes();

                    $applyFilter = function ($query) use ($isSuperAdmin, $userCabangs, $userDepartemens) {
                        if (!$isSuperAdmin) {
                            $table = $query->getModel()->getTable();
                            $query->join('karyawan', $table . '.nik', '=', 'karyawan.nik');
                            if (!empty($userCabangs)) {
                                $query->whereIn('karyawan.kode_cabang', $userCabangs);
                            }
                            if (!empty($userDepartemens)) {
                                $query->whereIn('karyawan.kode_dept', $userDepartemens);
                            }
                        }
                    };

                    // Whitelist notification counts: Izin, Sakit, Cuti, Dispensasi
                    $q_izinabsen = Izinabsen::where('presensi_izinabsen.status', 0);
                    $applyFilter($q_izinabsen);
                    $notifikasi_izinabsen = $q_izinabsen->count();

                    $q_izinsakit = Izinsakit::where('presensi_izinsakit.status', 0);
                    $applyFilter($q_izinsakit);
                    $notifikasi_izinsakit = $q_izinsakit->count();

                    $q_izincuti = Izincuti::where('presensi_izincuti.status', 0);
                    $applyFilter($q_izincuti);
                    $notifikasi_izincuti = $q_izincuti->count();

                    $q_dispensasi = PresensiDispensasi::where('presensi_dispensasi.status', 'PENDING');
                    $applyFilter($q_dispensasi);
                    $notifikasi_dispensasi = $q_dispensasi->count();

                    // Notification list for top navbar
                    $data_izinabsen = Izinabsen::select('presensi_izinabsen.nik', 'nama_karyawan', DB::raw('"i" as status'), 'presensi_izinabsen.created_at')
                        ->where('presensi_izinabsen.status', 0)
                        ->join('karyawan', 'presensi_izinabsen.nik', '=', 'karyawan.nik');

                    if (!$isSuperAdmin) {
                        if (!empty($userCabangs)) {
                            $data_izinabsen->whereIn('karyawan.kode_cabang', $userCabangs);
                        }
                        if (!empty($userDepartemens)) {
                            $data_izinabsen->whereIn('karyawan.kode_dept', $userDepartemens);
                        }
                    }

                    $data_izinsakit = Izinsakit::select('presensi_izinsakit.nik', 'nama_karyawan', DB::raw('"s" as status'), 'presensi_izinsakit.created_at')
                        ->where('presensi_izinsakit.status', 0)
                        ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik');
                    if (!$isSuperAdmin) {
                        if (!empty($userCabangs)) {
                            $data_izinsakit->whereIn('karyawan.kode_cabang', $userCabangs);
                        }
                        if (!empty($userDepartemens)) {
                            $data_izinsakit->whereIn('karyawan.kode_dept', $userDepartemens);
                        }
                    }

                    $data_izincuti = Izincuti::select('presensi_izincuti.nik', 'nama_karyawan', DB::raw('"c" as status'), 'presensi_izincuti.created_at')
                        ->where('presensi_izincuti.status', 0)
                        ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik');
                    if (!$isSuperAdmin) {
                        if (!empty($userCabangs)) {
                            $data_izincuti->whereIn('karyawan.kode_cabang', $userCabangs);
                        }
                        if (!empty($userDepartemens)) {
                            $data_izincuti->whereIn('karyawan.kode_dept', $userDepartemens);
                        }
                    }

                    $data_izin = $data_izinabsen->unionAll($data_izinsakit)->unionAll($data_izincuti)->limit(10)->get();

                    $notifikasi_ajuan_absen = $notifikasi_izinabsen + $notifikasi_izincuti + $notifikasi_izinsakit + $notifikasi_dispensasi;
                    $notifikasi_unread = 0;
                    $notifications_list = collect([]);

                    return [
                        'notifikasi_izinabsen' => $notifikasi_izinabsen,
                        'notifikasi_izinsakit' => $notifikasi_izinsakit,
                        'notifikasi_izincuti' => $notifikasi_izincuti,
                        'notifikasi_dispensasi' => $notifikasi_dispensasi,
                        'notifikasi_ajuan_absen' => $notifikasi_ajuan_absen,
                        'notifikasi_unread' => $notifikasi_unread,
                        'notifications_list' => $notifications_list,
                        'data_izin' => $data_izin,
                        'data_reimbursement_pending' => collect([]),
                    ];
                });

                View::share($shareddata);
            }
        });
    }
}
