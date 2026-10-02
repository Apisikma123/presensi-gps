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
            'PAID' => '<span class="badge bg-label-success">Lunas</span>',
            default => '<span class="badge bg-label-warning">Belum Bayar</span>',
        };
    }
}
