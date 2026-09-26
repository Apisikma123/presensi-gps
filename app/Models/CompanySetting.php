<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CompanySetting extends Model
{
    use HasFactory;

    protected $table = 'company_settings';
    protected $guarded = [];

    private static ?self $memoryCache = null;

    protected static function booted()
    {
        static::saved(function ($setting) {
            static::$memoryCache = null;
            Cache::forget('company_settings_first');
            Cache::forget('global_app_logo_relative_path');
            Cache::forget('global_general_setting');
            \App\Services\ThemeResolver::forgetCache();

            // Backward compatibility: keep pengaturan_umum in sync if it exists
            try {
                $umum = Pengaturanumum::where('id', 1)->first();
                if ($umum) {
                    $umum->updateQuietly([
                        'nama_perusahaan' => $setting->company_name ?? $umum->nama_perusahaan,
                        'nama_aplikasi' => $setting->app_name ?? $umum->nama_aplikasi,
                        'alamat' => $setting->address ?? $umum->alamat,
                        'telepon' => $setting->phone ?? $umum->telepon,
                        'timezone' => $setting->timezone ?? $umum->timezone,
                        'logo' => $setting->logo ?? $umum->logo,
                        'theme_color_1' => $setting->theme_color_primary ?? $umum->theme_color_1,
                        'theme_color_2' => $setting->theme_color_secondary ?? $umum->theme_color_2,
                    ]);
                    Cache::forget('pengaturan_umum_first');
                }
            } catch (\Throwable $e) {
                // Ignore sync errors during migrations or bootstrap
            }
        });

        static::deleted(function () {
            static::$memoryCache = null;
            Cache::forget('company_settings_first');
            Cache::forget('global_app_logo_relative_path');
            \App\Services\ThemeResolver::forgetCache();
        });
    }

    /**
     * Get cached company settings (always returns a valid model instance)
     *
     * @return \App\Models\CompanySetting
     */
    public static function getSetting(): self
    {
        if (static::$memoryCache !== null) {
            return static::$memoryCache;
        }

        return static::$memoryCache = Cache::remember('company_settings_first', 3600, function () {
            return static::first() ?? new static([
                'company_name' => 'Presence Universal HR',
                'app_name' => 'Presence',
                'app_tagline' => 'Universal HR Management System',
                'business_type' => 'General',
                'timezone' => 'Asia/Jakarta',
                'locale' => 'id',
                'currency' => 'IDR',
                'date_format' => 'd-m-Y',
                'payroll_cutoff_date' => 20,
                'payroll_payment_date' => 25,
                'theme_color_primary' => '#3C2A21',
                'theme_color_secondary' => '#634832',
            ]);
        });
    }

    /**
     * Alias of getSetting()
     */
    public static function getActive(): self
    {
        return static::getSetting();
    }
}
