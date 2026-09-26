<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReview extends Model
{
    use HasFactory;

    protected $table = 'performance_reviews';

    protected $fillable = [
        'review_code',
        'nik',
        'reviewer_nik',
        'period_title',
        'start_date',
        'end_date',
        'overall_score',
        'rating_grade',
        'strengths',
        'areas_for_improvement',
        'goals_next_period',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'overall_score' => 'decimal:2',
    ];

    public const GRADES = [
        'EXCEEDS' => 'Sangat Memuaskan (Exceeds)',
        'MEETS' => 'Memenuhi Standar (Meets Expectations)',
        'NEEDS_IMPROVEMENT' => 'Perlu Peningkatan (Needs Improvement)',
        'POOR' => 'Kurang / Di Bawah Standar (Poor)',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    public function reviewer()
    {
        return $this->belongsTo(Karyawan::class, 'reviewer_nik', 'nik');
    }

    public function kpis()
    {
        return $this->hasMany(PerformanceKpi::class, 'performance_review_id');
    }

    public function getGradeBadgeHtmlAttribute(): string
    {
        return match ($this->rating_grade) {
            'EXCEEDS' => '<span class="badge bg-success-lt text-success fw-bold">Sangat Memuaskan</span>',
            'MEETS' => '<span class="badge bg-primary-lt text-primary fw-bold">Memenuhi Standar</span>',
            'NEEDS_IMPROVEMENT' => '<span class="badge bg-warning-lt text-warning fw-bold">Perlu Perbaikan</span>',
            'POOR' => '<span class="badge bg-danger-lt text-danger fw-bold">Di Bawah Standar</span>',
            default => '<span class="badge bg-light text-muted">' . e($this->rating_grade) . '</span>',
        };
    }

    public function getStatusBadgeHtmlAttribute(): string
    {
        return match ($this->status) {
            'APPROVED' => '<span class="badge bg-success-lt text-success fw-bold"><i class="ti ti-check me-1"></i>Disetujui</span>',
            'SUBMITTED' => '<span class="badge bg-info-lt text-info fw-bold"><i class="ti ti-send me-1"></i>Diajukan</span>',
            'ACKNOWLEDGED' => '<span class="badge bg-dark-lt text-dark fw-bold"><i class="ti ti-thumb-up me-1"></i>Diterima Karyawan</span>',
            'DRAFT' => '<span class="badge bg-secondary-lt text-secondary fw-bold">Draft</span>',
            default => '<span class="badge bg-light text-muted">' . e($this->status) . '</span>',
        };
    }
}
