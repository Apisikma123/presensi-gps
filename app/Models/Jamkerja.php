<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Jamkerja extends Model
{
    use HasFactory;
    protected $table = 'presensi_jamkerja';
    protected $primaryKey = 'kode_jam_kerja';
    protected $guarded = [];
    public $incrementing = false;

    protected static function booted()
    {
        static::saved(function ($model) {
            Cache::forget('jam_kerja_code_' . $model->kode_jam_kerja);
        });
        static::deleted(function ($model) {
            Cache::forget('jam_kerja_code_' . $model->kode_jam_kerja);
        });
    }

    /**
     * Get cached jam kerja by code
     */
    public static function getByCode(?string $kode_jam_kerja): ?self
    {
        if (empty($kode_jam_kerja)) {
            return null;
        }

        return Cache::remember('jam_kerja_code_' . $kode_jam_kerja, 3600, function () use ($kode_jam_kerja) {
            return static::where('kode_jam_kerja', $kode_jam_kerja)->first();
        });
    }
}
