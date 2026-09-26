<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOnboarding extends Model
{
    use HasFactory;

    protected $table = 'employee_onboardings';

    protected $fillable = [
        'nik',
        'onboarding_template_id',
        'start_date',
        'target_completion_date',
        'completed_at',
        'status',
        'mentor_nik',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'target_completion_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    public function mentor()
    {
        return $this->belongsTo(Karyawan::class, 'mentor_nik', 'nik');
    }

    public function template()
    {
        return $this->belongsTo(OnboardingTemplate::class, 'onboarding_template_id');
    }

    public function tasks()
    {
        return $this->hasMany(EmployeeOnboardingTask::class, 'employee_onboarding_id')->orderBy('sort_order');
    }

    public function getProgressPercentageAttribute(): int
    {
        $total = $this->tasks()->count();
        if ($total === 0) {
            return 0;
        }
        $completed = $this->tasks()->where('is_completed', true)->count();
        return (int) round(($completed / $total) * 100);
    }

    public function getStatusBadgeHtmlAttribute(): string
    {
        return match ($this->status) {
            'COMPLETED' => '<span class="badge bg-success-lt text-success fw-bold"><i class="ti ti-check me-1"></i>Selesai</span>',
            'IN_PROGRESS' => '<span class="badge bg-info-lt text-info fw-bold"><i class="ti ti-rotate-clockwise me-1"></i>Berjalan</span>',
            'OVERDUE' => '<span class="badge bg-danger-lt text-danger fw-bold"><i class="ti ti-alert-triangle me-1"></i>Terlambat</span>',
            default => '<span class="badge bg-light text-muted">' . e($this->status) . '</span>',
        };
    }
}
