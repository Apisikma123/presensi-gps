<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLoanInstallment extends Model
{
    use HasFactory;

    protected $table = 'employee_loan_installments';

    protected $fillable = [
        'employee_loan_id',
        'installment_number',
        'due_date',
        'amount',
        'paid_amount',
        'paid_at',
        'payroll_period_id',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'float',
        'paid_amount' => 'float',
        'paid_at' => 'datetime',
        'installment_number' => 'integer',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(EmployeeLoan::class, 'employee_loan_id');
    }

    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function getStatusBadgeHtmlAttribute(): string
    {
        return match ($this->status) {
            'PAID' => '<span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2 py-0.5 rounded-md" style="background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;"><span class="rounded-full" style="width: 6px; height: 6px; background: #16a34a;"></span>Lunas</span>',
            default => '<span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2 py-0.5 rounded-md" style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a;"><span class="rounded-full" style="width: 6px; height: 6px; background: #d97706;"></span>Belum Bayar</span>',
        };
    }
}
