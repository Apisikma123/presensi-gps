<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecruitmentVacancy extends Model
{
    use HasFactory;

    protected $table = 'recruitment_vacancies';

    protected $fillable = [
        'vacancy_code',
        'title',
        'kode_dept',
        'kode_cabang',
        'employment_type',
        'quota',
        'min_experience_years',
        'salary_min',
        'salary_max',
        'description',
        'requirements',
        'deadline',
        'status',
        'created_by',
    ];

    protected $casts = [
        'deadline' => 'date',
        'quota' => 'integer',
        'min_experience_years' => 'integer',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
    ];

    public function department()
    {
        return $this->belongsTo(Departemen::class, 'kode_dept', 'kode_dept');
    }

    public function branch()
    {
        return $this->belongsTo(Cabang::class, 'kode_cabang', 'kode_cabang');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function candidates()
    {
        return $this->hasMany(RecruitmentCandidate::class, 'recruitment_vacancy_id');
    }

    public function getStatusBadgeHtmlAttribute(): string
    {
        return match ($this->status) {
            'OPEN' => '<span class="badge bg-success-lt text-success fw-bold">Dibuka</span>',
            'DRAFT' => '<span class="badge bg-secondary-lt text-secondary fw-bold">Draft</span>',
            'CLOSED' => '<span class="badge bg-dark-lt text-dark fw-bold">Ditutup</span>',
            'CANCELLED' => '<span class="badge bg-danger-lt text-danger fw-bold">Dibatalkan</span>',
            default => '<span class="badge bg-light text-muted">' . e($this->status) . '</span>',
        };
    }
}
