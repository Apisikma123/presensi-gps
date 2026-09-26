<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\LeaveQuota;
use App\Models\LeaveQuotaTransaction;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeaveQuotaService
{
    /**
     * Initialize annual statutory quotas for a specific employee.
     */
    public function initializeEmployeeQuotas(string $nik, ?int $year = null): array
    {
        $year = $year ?: (int) date('Y');
        $karyawan = Karyawan::where('nik', $nik)->first();
        if (!$karyawan) {
            return [];
        }

        $activeTypes = LeaveType::where('is_active', true)
            ->where('uses_quota', true)
            ->where('quota_type', 'ANNUAL')
            ->get();

        $created = [];

        DB::transaction(function () use ($karyawan, $activeTypes, $year, &$created) {
            foreach ($activeTypes as $type) {
                // Check gender restrictions
                if ($type->gender_restriction && $karyawan->jenis_kelamin !== $type->gender_restriction) {
                    continue;
                }

                $quota = LeaveQuota::firstOrCreate(
                    [
                        'nik' => $karyawan->nik,
                        'leave_type_id' => $type->id,
                        'year' => $year,
                    ],
                    [
                        'opening_balance' => 0.0,
                        'earned' => $type->default_quota,
                        'used' => 0.0,
                        'adjustment' => 0.0,
                        'carry_forward' => 0.0,
                        'expired' => 0.0,
                        'closing_balance' => $type->default_quota,
                    ]
                );

                if ($quota->wasRecentlyCreated) {
                    LeaveQuotaTransaction::create([
                        'leave_quota_id' => $quota->id,
                        'type' => 'OPENING',
                        'amount' => $type->default_quota,
                        'notes' => 'Hak cuti tahunan awal periode ' . $year,
                    ]);
                    $created[] = $quota;
                }
            }
        });

        return $created;
    }

    /**
     * Retrieve quota record for an employee and leave code.
     */
    public function getQuota(string $nik, string $code, ?int $year = null): ?LeaveQuota
    {
        $year = $year ?: (int) date('Y');
        $type = LeaveType::where('code', $code)->first();
        if (!$type || !$type->uses_quota) {
            return null; // Unlimited or quota not tracked
        }

        return LeaveQuota::where('nik', $nik)
            ->where('leave_type_id', $type->id)
            ->where('year', $year)
            ->first();
    }

    /**
     * Deduct quota days and log transaction.
     */
    public function deductQuota(
        string $nik,
        string $code,
        float $days,
        ?string $refType = null,
        $refId = null,
        ?string $notes = null,
        ?int $userId = null
    ): bool {
        $quota = $this->getQuota($nik, $code);
        if (!$quota) {
            return true; // No quota enforcement for this type
        }

        return DB::transaction(function () use ($quota, $days, $refType, $refId, $notes, $userId) {
            $quota->used += $days;
            $quota->recalculateBalance();

            LeaveQuotaTransaction::create([
                'leave_quota_id' => $quota->id,
                'type' => 'USED',
                'amount' => -$days,
                'reference_type' => $refType,
                'reference_id' => $refId ? (string) $refId : null,
                'notes' => $notes ?: 'Pengambilan cuti',
                'created_by' => $userId,
            ]);

            return true;
        });
    }

    /**
     * Restore quota days upon cancellation or rejection.
     */
    public function restoreQuota(
        string $nik,
        string $code,
        float $days,
        ?string $refType = null,
        $refId = null,
        ?string $notes = null,
        ?int $userId = null
    ): bool {
        $quota = $this->getQuota($nik, $code);
        if (!$quota) {
            return true;
        }

        return DB::transaction(function () use ($quota, $days, $refType, $refId, $notes, $userId) {
            $quota->used = max(0.0, $quota->used - $days);
            $quota->recalculateBalance();

            LeaveQuotaTransaction::create([
                'leave_quota_id' => $quota->id,
                'type' => 'ADJUSTMENT',
                'amount' => $days,
                'reference_type' => $refType,
                'reference_id' => $refId ? (string) $refId : null,
                'notes' => $notes ?: 'Pengembalian kuota cuti dibatalkan/ditolak',
                'created_by' => $userId,
            ]);

            return true;
        });
    }

    /**
     * Manually adjust quota balance.
     */
    public function adjustQuota(
        int $leaveQuotaId,
        float $amount,
        string $reason,
        ?int $userId = null
    ): bool {
        $quota = LeaveQuota::findOrFail($leaveQuotaId);

        return DB::transaction(function () use ($quota, $amount, $reason, $userId) {
            $quota->adjustment += $amount;
            $quota->recalculateBalance();

            LeaveQuotaTransaction::create([
                'leave_quota_id' => $quota->id,
                'type' => 'ADJUSTMENT',
                'amount' => $amount,
                'notes' => $reason,
                'created_by' => $userId,
            ]);

            return true;
        });
    }

    /**
     * Bulk generate annual quotas for all active employees.
     */
    public function generateQuotasForAllActive(int $year): int
    {
        $karyawans = Karyawan::where('status_aktif_karyawan', '1')->pluck('nik');
        $count = 0;

        foreach ($karyawans as $nik) {
            $created = $this->initializeEmployeeQuotas($nik, $year);
            if (!empty($created)) {
                $count++;
            }
        }

        return $count;
    }
}
