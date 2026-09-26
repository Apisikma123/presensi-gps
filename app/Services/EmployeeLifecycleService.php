<?php

namespace App\Services;

use App\Models\EmployeeMovement;
use App\Models\EmployeeResignation;
use App\Models\Karyawan;
use App\Models\Kontrak;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeLifecycleService
{
    /**
     * Apply an employee movement record and optionally synchronize the karyawan table.
     */
    public function applyMovement(EmployeeMovement $movement, bool $syncEmployee = true): bool
    {
        return DB::transaction(function () use ($movement, $syncEmployee) {
            $karyawan = Karyawan::where('nik', $movement->nik)->first();
            if (!$karyawan) {
                return false;
            }

            $today = Carbon::today();
            $isEffective = $movement->effective_date->lte($today);

            if ($syncEmployee && $isEffective) {
                $newValues = $movement->new_values ?? [];
                $updates = [];

                if (isset($newValues['kode_cabang'])) {
                    $updates['kode_cabang'] = $newValues['kode_cabang'];
                }
                if (isset($newValues['kode_dept'])) {
                    $updates['kode_dept'] = $newValues['kode_dept'];
                }
                if (isset($newValues['kode_divisi'])) {
                    $updates['kode_divisi'] = $newValues['kode_divisi'];
                }
                if (isset($newValues['kode_jabatan'])) {
                    $updates['kode_jabatan'] = $newValues['kode_jabatan'];
                }
                if (isset($newValues['grade_level'])) {
                    $updates['grade_level'] = $newValues['grade_level'];
                }
                if (isset($newValues['direct_supervisor_nik'])) {
                    $updates['direct_supervisor_nik'] = $newValues['direct_supervisor_nik'];
                }
                if (isset($newValues['employment_type'])) {
                    $updates['employment_type'] = $newValues['employment_type'];
                }
                if (isset($newValues['status_karyawan'])) {
                    $updates['status_karyawan'] = $newValues['status_karyawan'];
                }

                if (!empty($updates)) {
                    $karyawan->update($updates);
                }

                $movement->status = 'APPLIED';
            } else {
                $movement->status = 'APPROVED';
            }

            $movement->save();
            return true;
        });
    }

    /**
     * Process employee exit / resignation.
     */
    public function processResignation(EmployeeResignation $resignation, bool $deactivateNow = true): bool
    {
        return DB::transaction(function () use ($resignation, $deactivateNow) {
            $karyawan = Karyawan::where('nik', $resignation->nik)->first();
            if (!$karyawan) {
                return false;
            }

            if ($deactivateNow) {
                $karyawan->status_aktif_karyawan = '0';
                $karyawan->tanggal_nonaktif = $resignation->tanggal_keluar;
                $karyawan->save();

                // Terminate active contracts
                Kontrak::where('nik', $resignation->nik)
                    ->whereIn('status', ['ACTIVE', 'EXPIRING_SOON'])
                    ->update(['status' => 'TERMINATED']);

                $uk = \App\Models\Userkaryawan::where('nik', $resignation->nik)->first();
                if ($uk) {
                    \Illuminate\Support\Facades\Cache::forget('user_karyawan_valid_' . $uk->id_user);
                    $targetUser = \App\Models\User::find($uk->id_user);
                    if ($targetUser && method_exists($targetUser, 'tokens')) {
                        $targetUser->tokens()->delete();
                    }
                }
            }

            $resignation->status = 'APPROVED';
            $resignation->save();

            return true;
        });
    }

    /**
     * Revert / cancel employee resignation.
     */
    public function revertResignation(EmployeeResignation $resignation): bool
    {
        return DB::transaction(function () use ($resignation) {
            $karyawan = Karyawan::where('nik', $resignation->nik)->first();
            if ($karyawan) {
                $karyawan->status_aktif_karyawan = '1';
                $karyawan->tanggal_nonaktif = null;
                $karyawan->save();
            }

            $resignation->delete();
            return true;
        });
    }

    /**
     * Recheck and dynamically update contract statuses.
     */
    public function recheckContractStatuses(): array
    {
        $today = Carbon::today();

        // 1. Mark expired contracts
        $expiredCount = Kontrak::whereIn('status', ['ACTIVE', 'EXPIRING_SOON'])
            ->whereNotNull('tanggal_selesai')
            ->where('tanggal_selesai', '<', $today)
            ->update(['status' => 'EXPIRED']);

        // 2. Mark contracts expiring soon (default 30 days)
        $expiringContracts = Kontrak::where('status', 'ACTIVE')
            ->whereNotNull('tanggal_selesai')
            ->whereBetween('tanggal_selesai', [$today, $today->copy()->addDays(30)])
            ->get();

        $expiringCount = 0;
        foreach ($expiringContracts as $c) {
            $c->update(['status' => 'EXPIRING_SOON']);
            $expiringCount++;
        }

        return [
            'expired_updated' => $expiredCount,
            'expiring_soon_updated' => $expiringCount,
        ];
    }
}
