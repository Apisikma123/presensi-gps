<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeLoan extends Model
{
    use HasFactory;

    protected $table = 'employee_loans';

    protected $fillable = [
        'loan_number',
        'nik',
        'loan_amount',
        'interest_rate',
        'total_amount',
        'installment_months',
        'monthly_installment',
        'remaining_amount',
        'start_date',
        'status',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'loan_amount' => 'float',
        'interest_rate' => 'float',
        'total_amount' => 'float',
        'installment_months' => 'integer',
        'monthly_installment' => 'float',
        'remaining_amount' => 'float',
        'start_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(EmployeeLoanInstallment::class, 'employee_loan_id');
    }

    public function getStatusBadgeHtmlAttribute(): string
    {
        return match ($this->status) {
            'PAID_OFF' => '<span class="badge bg-label-success">Lunas</span>',
            'ACTIVE' => '<span class="badge bg-label-info">Aktif Berjalan</span>',
            'APPROVED' => '<span class="badge bg-label-primary">Disetujui</span>',
            'REJECTED' => '<span class="badge bg-label-danger">Ditolak</span>',
            default => '<span class="badge bg-label-warning">Menunggu</span>',
        };
    }
}
