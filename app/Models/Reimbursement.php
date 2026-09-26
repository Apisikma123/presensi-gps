<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reimbursement extends Model
{
    use HasFactory;

    protected $table = 'reimbursements';

    protected $fillable = [
        'claim_number',
        'nik',
        'reimbursement_type_id',
        'claim_date',
        'amount',
        'description',
        'receipt_attachment',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'payroll_period_id',
    ];

    protected $casts = [
        'claim_date' => 'date',
        'amount' => 'float',
        'approved_at' => 'datetime',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ReimbursementType::class, 'reimbursement_type_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function getStatusBadgeHtmlAttribute(): string
    {
        return match ($this->status) {
            'PAID' => '<span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2 py-0.5 rounded-md" style="background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;"><span class="rounded-full" style="width: 6px; height: 6px; background: #16a34a;"></span>Terbayar</span>',
            'APPROVED' => '<span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2 py-0.5 rounded-md" style="background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;"><span class="rounded-full" style="width: 6px; height: 6px; background: #0284c7;"></span>Disetujui</span>',
            'REJECTED' => '<span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2 py-0.5 rounded-md" style="background: #fef2f2; color: #ba1a1a; border: 1px solid #fecdd3;"><span class="rounded-full" style="width: 6px; height: 6px; background: #dc2626;"></span>Ditolak</span>',
            default => '<span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2 py-0.5 rounded-md" style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a;"><span class="rounded-full" style="width: 6px; height: 6px; background: #d97706;"></span>Diajukan</span>',
        };
    }
}
