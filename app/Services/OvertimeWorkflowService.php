<?php

namespace App\Services;

use App\Models\Lembur;
use App\Models\OvertimePolicy;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OvertimeWorkflowService
{
    public function __construct(
        protected OvertimeCalculatorService $calculator
    ) {}

    /**
     * Generate unique SPK number e.g. SPK/202609/0001
     */
    public function generateSpkNumber(string $date): string
    {
        $prefix = 'SPK/' . Carbon::parse($date)->format('Ym') . '/';
        $last = Lembur::where('no_spk', 'like', $prefix . '%')
            ->orderBy('no_spk', 'desc')
            ->value('no_spk');

        if (!$last) {
            return $prefix . '0001';
        }

        $seq = (int) substr($last, -4);
        return $prefix . str_pad($seq + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create SPK Lembur record
     */
    public function createSpk(array $data): Lembur
    {
        return DB::transaction(function () use ($data) {
            $tanggal = $data['tanggal'];
            $noSpk = $data['no_spk'] ?? $this->generateSpkNumber($tanggal);

            $mulai = Carbon::parse($data['lembur_mulai']);
            $selesai = Carbon::parse($data['lembur_selesai']);
            $plannedMinutes = max(0, (int) $mulai->diffInMinutes($selesai, false));
            $plannedHours = $plannedMinutes / 60;

            $policyId = $data['overtime_policy_id'] ?? OvertimePolicy::getDefaultPolicy()->id;
            $policy = OvertimePolicy::find($policyId);
            $dayType = $data['day_type'] ?? 'WORKDAY';

            $calculation = $this->calculator->calculateRateHours($plannedHours, $dayType, $policy);

            return Lembur::create([
                'no_spk' => $noSpk,
                'tanggal' => $tanggal,
                'nik' => $data['nik'],
                'overtime_policy_id' => $policyId,
                'day_type' => $dayType,
                'lembur_mulai' => $mulai,
                'lembur_selesai' => $selesai,
                'planned_duration_minutes' => $plannedMinutes,
                'calculated_rate_hours' => $calculation['rate_hours'],
                'status' => $data['status'] ?? 'PENDING',
                'keterangan' => $data['keterangan'],
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }

    /**
     * Approve SPK Lembur
     */
    public function approve(
        Lembur $lembur,
        int $userId,
        ?int $approvedMinutes = null,
        ?string $notes = null
    ): Lembur {
        $minutes = $approvedMinutes ?? $lembur->actual_duration_minutes ?? $lembur->planned_duration_minutes;
        $hours = $minutes / 60;

        $calculation = $this->calculator->calculateRateHours(
            $hours,
            $lembur->day_type,
            $lembur->policy
        );

        $lembur->update([
            'status' => 'APPROVED',
            'approved_duration_minutes' => $minutes,
            'calculated_rate_hours' => $calculation['rate_hours'],
            'approved_by' => $userId,
            'approved_at' => now(),
            'notes' => $notes ?? $lembur->notes,
        ]);

        return $lembur->fresh();
    }

    /**
     * Reject SPK Lembur
     */
    public function reject(Lembur $lembur, int $userId, ?string $notes = null): Lembur
    {
        $lembur->update([
            'status' => 'REJECTED',
            'approved_by' => $userId,
            'approved_at' => now(),
            'notes' => $notes ?? $lembur->notes,
        ]);

        return $lembur->fresh();
    }

    /**
     * Cancel SPK Lembur
     */
    public function cancel(Lembur $lembur, ?string $notes = null): Lembur
    {
        $lembur->update([
            'status' => 'CANCELLED',
            'notes' => $notes ?? $lembur->notes,
        ]);

        return $lembur->fresh();
    }

    /**
     * Record actual check in / out for overtime
     */
    public function recordAttendance(
        Lembur $lembur,
        string $type, // 'in' or 'out'
        string $time,
        ?string $foto = null,
        ?string $lokasi = null
    ): Lembur {
        $timeObj = Carbon::parse($time);

        if ($type === 'in') {
            $lembur->lembur_in = $timeObj;
            if ($foto) $lembur->foto_lembur_in = $foto;
            if ($lokasi) $lembur->lokasi_lembur_in = $lokasi;
        } else {
            $lembur->lembur_out = $timeObj;
            if ($foto) $lembur->foto_lembur_out = $foto;
            if ($lokasi) $lembur->lokasi_lembur_out = $lokasi;

            if ($lembur->lembur_in) {
                $actualMinutes = max(0, (int) Carbon::parse($lembur->lembur_in)->diffInMinutes($timeObj, false));
                $lembur->actual_duration_minutes = $actualMinutes;
                
                // If already approved, recalculate based on actual vs planned
                if ($lembur->status === 'APPROVED') {
                    $calcHours = ($lembur->approved_duration_minutes ?? $actualMinutes) / 60;
                    $calc = $this->calculator->calculateRateHours($calcHours, $lembur->day_type, $lembur->policy);
                    $lembur->calculated_rate_hours = $calc['rate_hours'];
                }
            }
        }

        $lembur->save();
        return $lembur->fresh();
    }
}
