<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $table = 'leave_types';

    protected $fillable = [
        'code',
        'name',
        'is_paid',
        'requires_attachment',
        'requires_approval',
        'uses_quota',
        'quota_type',
        'default_quota',
        'gender_restriction',
        'min_notice_days',
        'max_consecutive_days',
        'is_active',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'requires_attachment' => 'boolean',
        'requires_approval' => 'boolean',
        'uses_quota' => 'boolean',
        'is_active' => 'boolean',
        'default_quota' => 'decimal:1',
        'min_notice_days' => 'integer',
        'max_consecutive_days' => 'integer',
    ];

    protected static function booted()
    {
        static::saved(function ($model) {
            // Keep legacy cuti table in sync for backward compatibility
            try {
                $legacyCode = strtoupper(substr($model->code, 0, 3));
                if (strlen($legacyCode) > 0) {
                    Cuti::updateOrCreate(
                        ['kode_cuti' => $legacyCode],
                        [
                            'jenis_cuti' => $model->name,
                            'jumlah_hari' => (int) $model->default_quota,
                        ]
                    );
                }
            } catch (\Throwable $e) {
                // Ignore sync errors
            }
        });
    }

    /**
     * Relationship to employee quotas
     */
    public function quotas()
    {
        return $this->hasMany(LeaveQuota::class, 'leave_type_id');
    }

    /**
     * Scope for active leave types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
