<?php

namespace App\Services;

use App\Models\EmployeeSalaryAssignment;
use App\Models\IndonesiaPolicyRule;
use App\Models\Karyawan;
use App\Models\ThrPayment;
use App\Models\ThrPaymentDetail;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class THRService
{
    /**
     * Calculate individual employee THR based on Permenaker No. 6/2016
     */
    public static function calculateEmployeeThr(
        Karyawan $employee,
        Carbon|string $distributionDate,
        ?float $customBaseSalary = null
    ): array {
        $distDate = is_string($distributionDate) ? Carbon::parse($distributionDate) : $distributionDate->copy();
        $hireDate = $employee->tanggal_masuk ? Carbon::parse($employee->tanggal_masuk) : $distDate->copy()->subYears(1);

        // Calculate service duration
        $diffYears = $hireDate->diffInYears($distDate);
        $diffMonthsTotal = $hireDate->diffInMonths($distDate);
        $diffDaysTotal = $hireDate->diffInDays($distDate);

        // Precise service months for prorata calculation
        // e.g. 6.5 months
        $serviceMonths = round($diffDaysTotal / 30.4375, 1);

        // Resolve base salary
        $baseSalary = $customBaseSalary ?? self::resolveBaseSalary($employee);

        if ($diffMonthsTotal >= 12) {
            $multiplier = 1.000;
            $category = 'FULL';
            $thrAmount = round($baseSalary, 2);
        } elseif ($diffMonthsTotal >= 1) {
            // Prorata: months / 12
            // We use standard months / 12 rounded
            $exactMonths = min(12, max(1, $diffMonthsTotal));
            $multiplier = round($exactMonths / 12, 3);
            $category = 'PRORATA';
            $thrAmount = round($baseSalary * ($exactMonths / 12), 2);
            $serviceMonths = $exactMonths;
        } else {
            $multiplier = 0.000;
            $category = 'EXCLUDED'; // < 1 month service
            $thrAmount = 0.00;
        }

        return [
            'nik' => $employee->nik,
            'nama_karyawan' => $employee->nama_karyawan,
            'hire_date' => $hireDate->toDateString(),
            'service_months' => $serviceMonths,
            'base_salary' => $baseSalary,
            'multiplier' => $multiplier,
            'thr_amount' => $thrAmount,
            'category' => $category,
        ];
    }

    /**
     * Batch calculate and record THR details for a ThrPayment event
     */
    public static function processEventCalculation(ThrPayment $payment, array $filters = []): array
    {
        $query = Karyawan::where('status_aktif_karyawan', '1');

        if (!empty($filters['kode_cabang'])) {
            $query->where('kode_cabang', $filters['kode_cabang']);
        }
        if (!empty($filters['kode_dept'])) {
            $query->where('kode_dept', $filters['kode_dept']);
        }

        $employees = $query->get();
        $totalAmount = 0.0;
        $eligibleCount = 0;

        \Illuminate\Support\Facades\DB::transaction(function () use ($employees, $payment, &$totalAmount, &$eligibleCount) {
            foreach ($employees as $employee) {
                $calc = self::calculateEmployeeThr($employee, $payment->distribution_date);

                ThrPaymentDetail::updateOrCreate(
                    [
                        'thr_payment_id' => $payment->id,
                        'nik' => $employee->nik,
                    ],
                    [
                        'hire_date' => $calc['hire_date'],
                        'service_months' => $calc['service_months'],
                        'base_salary' => $calc['base_salary'],
                        'multiplier' => $calc['multiplier'],
                        'thr_amount' => $calc['thr_amount'],
                        'category' => $calc['category'],
                    ]
                );

                if ($calc['thr_amount'] > 0) {
                    $totalAmount += $calc['thr_amount'];
                    $eligibleCount++;
                }
            }

            $payment->update([
                'total_amount' => $totalAmount,
                'employee_count' => $eligibleCount,
                'status' => 'CALCULATED',
            ]);
        });

        return [
            'event_id' => $payment->id,
            'total_amount' => $totalAmount,
            'employee_count' => $eligibleCount,
            'total_processed' => $employees->count(),
        ];
    }

    /**
     * Resolve monthly base wage from active payroll assignments or default
     */
    public static function resolveBaseSalary(Karyawan $employee): float
    {
        // Try to sum active earnings from PayrollComponentAssignment
        $assignments = EmployeeSalaryAssignment::where('nik', $employee->nik)
            ->where('is_active', true)
            ->whereHas('component', function ($q) {
                $q->where('type', 'EARNING');
            })
            ->get();

        if ($assignments->isNotEmpty()) {
            return (float) $assignments->sum('amount');
        }

        // Standard default if not set
        return 5000000.00;
    }
}
