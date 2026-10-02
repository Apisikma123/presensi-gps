<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollPeriod extends Model
{
    use HasFactory;

    protected $table = 'payroll_periods';

    protected $fillable = [
        'period_month',
        'period_year',
        'cutoff_start',
        'cutoff_end',
        'payment_date',
        'status',
        'finalized_by',
        'finalized_at',
        'notes',
    ];

    protected $casts = [
        'period_month' => 'integer',
        'period_year' => 'integer',
        'cutoff_start' => 'date',
        'cutoff_end' => 'date',
        'payment_date' => 'date',
        'finalized_at' => 'datetime',
    ];

    // Relations
    public function details(): HasMany
    {
        return $this->hasMany(PayrollDetail::class, 'payroll_period_id');
    }

    public function finalizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    // Scopes
    public function scopeForYear(Builder $query, int $year): Builder
    {
        return $query->where('period_year', $year);
    }

    public function scopeFinalized(Builder $query): Builder
    {
        return $query->where('status', 'FINALIZED');
    }

    // Accessors
    public function getFormattedPeriodAttribute(): string
    {
        $date = Carbon::createFromDate($this->period_year, $this->period_month, 1);
        return $date->translatedFormat('F Y');
    }

    public function getFormattedPeriodShortAttribute(): string
    {
        $date = Carbon::createFromDate($this->period_year, $this->period_month, 1);
        return $date->translatedFormat('M Y');
    }

    public function getTotalGrossAttribute(): float
    {
        return (float) $this->details->sum(function ($d) {
            return $d->basic_salary + $d->total_allowances + $d->total_overtime_pay;
        });
    }

    public function getTotalNetAttribute(): float
    {
        return (float) $this->details->sum('take_home_pay');
    }

    public function getTotalDeductionsAttribute(): float
    {
        return (float) $this->details->sum('total_deductions');
    }

    public function getEmployeeCountAttribute(): int
    {
        return $this->details->count();
    }

    /**
     * DESIGN.md compliant status badge
     */
    public function getStatusBadgeHtmlAttribute(): string
    {
        return match ($this->status) {
            'FINALIZED', 'PAID' => '<span class="badge bg-label-success">Final</span>',
            'CALCULATED', 'REVIEW' => '<span class="badge bg-label-info">Dihitung</span>',
            'DRAFT' => '<span class="badge bg-label-warning">Draft</span>',
            'CANCELLED' => '<span class="badge bg-label-danger">Dibatalkan</span>',
            default => '<span class="badge bg-label-secondary">' . e($this->status) . '</span>',
        };
    }
}
