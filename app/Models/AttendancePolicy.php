<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AttendancePolicy extends Model
{
    use HasFactory;

    protected $table = 'attendance_policies';

    protected $fillable = [
        'name',
        'is_default',
        'require_gps',
        'require_face_recognition',
        'require_photo',
        'allow_late_tolerance_minutes',
        'max_out_of_radius_meters',
        'allow_out_of_radius',
        'allow_overnight',
        'enable_early_checkout_penalty',
        'description',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'require_gps' => 'boolean',
        'require_face_recognition' => 'boolean',
        'require_photo' => 'boolean',
        'allow_out_of_radius' => 'boolean',
        'allow_overnight' => 'boolean',
        'enable_early_checkout_penalty' => 'boolean',
        'allow_late_tolerance_minutes' => 'integer',
        'max_out_of_radius_meters' => 'integer',
    ];

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('active_attendance_policy');
        });
        static::deleted(function () {
            Cache::forget('active_attendance_policy');
        });
    }

    /**
     * Retrieve the active attendance policy (cached)
     */
    public static function getActivePolicy(): self
    {
        return Cache::remember('active_attendance_policy', 3600, function () {
            return static::where('is_default', true)->first() 
                ?? static::first() 
                ?? static::create([
                    'name' => 'Kebijakan Presensi Standar',
                    'is_default' => true,
                    'require_gps' => true,
                    'require_face_recognition' => true,
                    'require_photo' => true,
                    'allow_late_tolerance_minutes' => 5,
                    'max_out_of_radius_meters' => 50,
                    'allow_out_of_radius' => false,
                    'allow_overnight' => true,
                ]);
        });
    }
}
