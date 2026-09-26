<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecruitmentCandidate extends Model
{
    use HasFactory;

    protected $table = 'recruitment_candidates';

    protected $fillable = [
        'candidate_code',
        'recruitment_vacancy_id',
        'name',
        'email',
        'phone',
        'gender',
        'expected_salary',
        'resume_file',
        'portfolio_url',
        'notes',
        'stage',
        'interview_scheduled_at',
        'interview_notes',
        'offered_salary',
        'hired_nik',
        'hired_at',
    ];

    protected $casts = [
        'expected_salary' => 'decimal:2',
        'offered_salary' => 'decimal:2',
        'interview_scheduled_at' => 'datetime',
        'hired_at' => 'datetime',
    ];

    public const STAGES = [
        'APPLIED' => 'Lamaran Masuk',
        'SCREENING' => 'Screening CV',
        'INTERVIEW' => 'Wawancara',
        'OFFERING' => 'Offering Letter',
        'HIRED' => 'Diterima (Hired)',
        'REJECTED' => 'Ditolak',
    ];

    public function vacancy()
    {
        return $this->belongsTo(RecruitmentVacancy::class, 'recruitment_vacancy_id');
    }

    public function hiredEmployee()
    {
        return $this->belongsTo(Karyawan::class, 'hired_nik', 'nik');
    }

    public function getStageLabelAttribute(): string
    {
        return self::STAGES[$this->stage] ?? $this->stage;
    }

    public function getStageBadgeHtmlAttribute(): string
    {
        return match ($this->stage) {
            'HIRED' => '<span class="badge bg-success-lt text-success fw-bold"><i class="ti ti-user-check me-1"></i>Diterima</span>',
            'OFFERING' => '<span class="badge bg-primary-lt text-primary fw-bold"><i class="ti ti-mail-forward me-1"></i>Offering</span>',
            'INTERVIEW' => '<span class="badge bg-info-lt text-info fw-bold"><i class="ti ti-calendar-event me-1"></i>Wawancara</span>',
            'SCREENING' => '<span class="badge bg-warning-lt text-warning fw-bold"><i class="ti ti-filter me-1"></i>Screening</span>',
            'APPLIED' => '<span class="badge bg-secondary-lt text-secondary fw-bold"><i class="ti ti-inbox me-1"></i>Lamaran Masuk</span>',
            'REJECTED' => '<span class="badge bg-danger-lt text-danger fw-bold"><i class="ti ti-x me-1"></i>Ditolak</span>',
            default => '<span class="badge bg-light text-muted">' . e($this->stage) . '</span>',
        };
    }
}
