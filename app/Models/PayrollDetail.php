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
            'PAID', 'VERIFIED' => '<span class="badge bg-label-success">Terverifikasi</span>',
            'DRAFT' => '<span class="badge bg-label-warning">Draft</span>',
            default => '<span class="badge bg-label-secondary">' . e($this->status) . '</span>',
        };
    }
}
