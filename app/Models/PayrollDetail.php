<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollDetail extends Model
{
    use HasFactory;

    protected $table = 'payroll_details';

    protected $fillable = [
        'payroll_period_id',
        'nik',
        'basic_salary',
        'total_allowances',
        'total_overtime_pay',
        'total_deductions',
        'take_home_pay',
        'components_breakdown',
        'status',
        'notes',
    ];

    protected $casts = [
        'basic_salary' => 'float',
        'total_allowances' => 'float',
        'total_overtime_pay' => 'float',
        'total_deductions' => 'float',
        'take_home_pay' => 'float',
        'components_breakdown' => 'array',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    public function getGrossSalaryAttribute(): float
    {
        return $this->basic_salary + $this->total_allowances + $this->total_overtime_pay;
    }

    public function getNetSalaryAttribute(): float
    {
        return (float) $this->take_home_pay;
    }

    public function getStatusBadgeHtmlAttribute(): string
    {
        return match ($this->status) {
            'PAID', 'VERIFIED' => '<span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md" style="background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;"><span class="rounded-full" style="width: 6px; height: 6px; background: #16a34a;"></span>Terverifikasi</span>',
            'DRAFT' => '<span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md" style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a;"><span class="rounded-full" style="width: 6px; height: 6px; background: #d97706;"></span>Draft</span>',
            default => '<span class="badge bg-secondary">' . e($this->status) . '</span>',
        };
    }
}
