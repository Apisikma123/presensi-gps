<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OffboardingClearance extends Model
{
    use HasFactory;

    protected $table = 'offboarding_clearances';

    protected $fillable = [
        'employee_resignation_id',
        'item_name',
        'department',
        'is_cleared',
        'cleared_by',
        'cleared_at',
        'notes',
    ];

    protected $casts = [
        'is_cleared' => 'boolean',
        'cleared_at' => 'datetime',
    ];

    public const DEPARTMENTS = [
        'IT' => 'Teknologi Informasi (IT)',
        'HR' => 'Human Resources (HR)',
        'FINANCE' => 'Keuangan / Kasbon',
        'OPERATIONAL' => 'Operasional & GA',
    ];

    public function resignation()
    {
        return $this->belongsTo(EmployeeResignation::class, 'employee_resignation_id');
    }

    public function clearedByUser()
    {
        return $this->belongsTo(User::class, 'cleared_by');
    }

    public function getDepartmentLabelAttribute(): string
    {
        return self::DEPARTMENTS[$this->department] ?? $this->department;
    }
}
