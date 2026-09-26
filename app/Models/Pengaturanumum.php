<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Pengaturanumum extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_umum';
    protected $guarded = [];

    private static ?self $memoryCache = null;

    protected static function booted()
    {
        static::saved(function ($model) {
            static::$memoryCache = null;
            Cache::forget('pengaturan_umum_first');
            Cache::forget('global_general_setting');
            Cache::forget('global_app_logo_relative_path');
            Cache::forget('company_settings_first');
            \App\Services\ThemeResolver::forgetCache();

            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('company_settings')) {
                    $cs = \App\Models\CompanySetting::first();
                    if ($cs) {
                        $cs->updateQuietly([
                            'company_name' => $model->nama_perusahaan ?: $cs->company_name,
                            'app_name' => $model->nama_aplikasi ?: $cs->app_name,
                            'address' => $model->alamat ?: $cs->address,
                            'phone' => $model->telepon ?: $cs->phone,
                            'timezone' => $model->timezone ?: $cs->timezone,
                            'logo' => $model->logo ?: $cs->logo,
                            'theme_color_primary' => $model->theme_color_1 ?: $cs->theme_color_primary,
                            'theme_color_secondary' => $model->theme_color_2 ?: $cs->theme_color_secondary,
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                // Ignore sync errors during initial boot
            }
        });
        static::deleted(function () {
            static::$memoryCache = null;
            Cache::forget('pengaturan_umum_first');
            Cache::forget('global_general_setting');
            Cache::forget('global_app_logo_relative_path');
            Cache::forget('company_settings_first');
            \App\Services\ThemeResolver::forgetCache();
        });
    }

    /**
     * Get cached general settings
     *
     * @return \App\Models\Pengaturanumum|null
     */
    public static function getSetting()
    {
        if (static::$memoryCache !== null) {
            return static::$memoryCache;
        }

        return static::$memoryCache = Cache::remember('pengaturan_umum_first', 3600, function () {
            return static::where('id', 1)->first() ?? static::first();
        });
    }
}
