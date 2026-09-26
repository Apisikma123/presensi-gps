<?php

namespace App\Services;

use App\Models\Reimbursement;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ReimbursementService
{
    /**
     * Auto generate claim number (e.g. RMB-202609-0001)
     */
    public static function generateClaimNumber(): string
    {
        $prefix = 'RMB-' . date('Ym') . '-';
        $last = Reimbursement::where('claim_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $lastSeq = (int) substr($last->claim_number, -4);
            $seq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $seq = '0001';
        }

        return $prefix . $seq;
    }

    /**
     * Submit new reimbursement claim
     */
    public static function submitClaim(array $data, ?UploadedFile $file = null): Reimbursement
    {
        $data['claim_number'] = self::generateClaimNumber();
        $data['status'] = 'SUBMITTED';

        if ($file && $file->isValid()) {
            $path = $file->store('reimbursements', 'public');
            $data['receipt_attachment'] = $path;
        }

        return Reimbursement::create($data);
    }

    /**
     * Approve claim
     */
    public static function approveClaim(Reimbursement $claim, int $approverId): bool
    {
        return $claim->update([
            'status' => 'APPROVED',
            'approved_by' => $approverId,
            'approved_at' => Carbon::now(),
            'rejection_reason' => null,
        ]);
    }

    /**
     * Reject claim
     */
    public static function rejectClaim(Reimbursement $claim, string $reason, int $approverId): bool
    {
        return $claim->update([
            'status' => 'REJECTED',
            'approved_by' => $approverId,
            'approved_at' => Carbon::now(),
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Mark claim as paid
     */
    public static function markAsPaid(Reimbursement $claim, ?int $payrollPeriodId = null): bool
    {
        return $claim->update([
            'status' => 'PAID',
            'payroll_period_id' => $payrollPeriodId,
        ]);
    }
}
