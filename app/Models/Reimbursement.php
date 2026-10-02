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
            'PAID' => '<span class="badge bg-label-success">Terbayar</span>',
            'APPROVED' => '<span class="badge bg-label-info">Disetujui</span>',
            'REJECTED' => '<span class="badge bg-label-danger">Ditolak</span>',
            default => '<span class="badge bg-label-warning">Diajukan</span>',
        };
    }
}
