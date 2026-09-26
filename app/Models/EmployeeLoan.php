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
            'PAID_OFF' => '<span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2 py-0.5 rounded-md" style="background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;"><span class="rounded-full" style="width: 6px; height: 6px; background: #16a34a;"></span>Lunas</span>',
            'ACTIVE' => '<span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2 py-0.5 rounded-md" style="background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;"><span class="rounded-full" style="width: 6px; height: 6px; background: #0284c7;"></span>Aktif Berjalan</span>',
            'APPROVED' => '<span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2 py-0.5 rounded-md" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;"><span class="rounded-full" style="width: 6px; height: 6px; background: #2563eb;"></span>Disetujui</span>',
            'REJECTED' => '<span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2 py-0.5 rounded-md" style="background: #fef2f2; color: #ba1a1a; border: 1px solid #fecdd3;"><span class="rounded-full" style="width: 6px; height: 6px; background: #dc2626;"></span>Ditolak</span>',
            default => '<span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2 py-0.5 rounded-md" style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a;"><span class="rounded-full" style="width: 6px; height: 6px; background: #d97706;"></span>Menunggu</span>',
        };
    }
}
