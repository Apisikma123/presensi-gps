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
            'FINALIZED', 'PAID' => '<span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md" style="background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;"><span class="rounded-full" style="width: 6px; height: 6px; background: #16a34a;"></span>Final</span>',
            'CALCULATED', 'REVIEW' => '<span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md" style="background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;"><span class="rounded-full" style="width: 6px; height: 6px; background: #0284c7;"></span>Dihitung</span>',
            'DRAFT' => '<span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md" style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a;"><span class="rounded-full" style="width: 6px; height: 6px; background: #d97706;"></span>Draft</span>',
            'CANCELLED' => '<span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md" style="background: #fef2f2; color: #ba1a1a; border: 1px solid #fecdd3;"><span class="rounded-full" style="width: 6px; height: 6px; background: #ba1a1a;"></span>Dibatalkan</span>',
            default => '<span class="badge bg-secondary">' . e($this->status) . '</span>',
        };
    }
}
