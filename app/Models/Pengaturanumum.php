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

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('pengaturan_umum_first');
            Cache::forget('global_general_setting');
            Cache::forget('global_app_logo_relative_path');
        });
        static::deleted(function () {
            Cache::forget('pengaturan_umum_first');
            Cache::forget('global_general_setting');
            Cache::forget('global_app_logo_relative_path');
        });
    }

    /**
     * Get cached general settings
     *
     * @return \App\Models\Pengaturanumum|null
     */
    public static function getSetting()
    {
        return Cache::remember('pengaturan_umum_first', 3600, function () {
            return static::where('id', 1)->first() ?? static::first();
        });
    }
}
