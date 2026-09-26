<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceKpi extends Model
{
    use HasFactory;

    protected $table = 'performance_kpis';

    protected $fillable = [
        'performance_review_id',
        'kpi_name',
        'weight',
        'target_value',
        'actual_value',
        'score',
        'weighted_score',
        'notes',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'score' => 'decimal:2',
        'weighted_score' => 'decimal:2',
    ];

    public function review()
    {
        return $this->belongsTo(PerformanceReview::class, 'performance_review_id');
    }
}
