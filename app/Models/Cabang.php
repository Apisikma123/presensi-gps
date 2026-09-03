<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Cabang extends Model
{
    use HasFactory;
    protected $table = "cabang";
    protected $primaryKey = "kode_cabang";
    public $incrementing = false;
    protected $guarded = [];

    protected static function booted()
    {
        static::saved(function ($model) {
            Cache::forget('cabang_code_' . $model->kode_cabang);
        });
        static::deleted(function ($model) {
            Cache::forget('cabang_code_' . $model->kode_cabang);
        });
    }

    /**
     * Get cached branch by code
     */
    public static function getByCode(?string $kode_cabang): ?self
    {
        if (empty($kode_cabang)) {
            return null;
        }

        return Cache::remember('cabang_code_' . $kode_cabang, 3600, function () use ($kode_cabang) {
            return static::where('kode_cabang', $kode_cabang)->first();
        });
    }

    // Relasi dengan User (Many to Many)
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_cabang_access', 'kode_cabang', 'user_id', 'kode_cabang', 'id');
    }
}
