<?php

namespace App\Services;

use App\Models\EmployeeSalaryAssignment;
use App\Models\Karyawan;
use App\Models\Lembur;
use App\Models\PayrollDetail;
use App\Models\PayrollPeriod;
use App\Models\SalaryComponent;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    /**
     * Create or retrieve payroll period for given month/year with standard Indonesian 26-25 cutoff
     */
    public function getOrCreatePeriod(int $month, int $year, ?string $cutoffStart = null, ?string $cutoffEnd = null, ?string $paymentDate = null): PayrollPeriod
    {
        $period = PayrollPeriod::where('period_year', $year)
            ->where('period_month', $month)
            ->first();

        if ($period) {
            return $period;
        }

        // Standard Indonesian payroll cutoff: 26th of prev month to 25th of current month
        $currentMonthDate = Carbon::createFromDate($year, $month, 1);
        $prevMonthDate = $currentMonthDate->copy()->subMonth();

        $defaultCutoffStart = $cutoffStart ?? $prevMonthDate->copy()->day(26)->toDateString();
        $defaultCutoffEnd = $cutoffEnd ?? $currentMonthDate->copy()->day(25)->toDateString();
        $defaultPaymentDate = $paymentDate ?? $currentMonthDate->copy()->endOfMonth()->toDateString();

        return PayrollPeriod::create([
            'period_month' => $month,
            'period_year' => $year,
            'cutoff_start' => $defaultCutoffStart,
            'cutoff_end' => $defaultCutoffEnd,
            'payment_date' => $defaultPaymentDate,
            'status' => 'DRAFT',
        ]);
    }

    /**
     * Calculate monthly payroll for all active employees and store snapshot via batch upsert
     */
    public function calculatePeriod(PayrollPeriod $period): array
    {
        return DB::transaction(function () use ($period) {
            $employees = Karyawan::where('status_aktif_karyawan', '1')->get();
            $niks = $employees->pluck('nik')->all();

            // Batch pre-fetch salary assignments with active components (1 single query)
            $allAssignments = !empty($niks) ? EmployeeSalaryAssignment::with('component')
                ->whereIn('nik', $niks)
                ->where('is_active', true)
                ->get()
                ->groupBy('nik') : collect();

            // Batch pre-fetch approved overtime records within period (1 single query)
            $allOvertimes = !empty($niks) ? Lembur::whereIn('nik', $niks)
                ->whereBetween('tanggal', [$period->cutoff_start->toDateString(), $period->cutoff_end->toDateString()])
                ->where('status', 'APPROVED')
                ->get()
                ->groupBy('nik') : collect();

            $totalEmployees = 0;
            $totalGross = 0;
            $totalNet = 0;
            $batchRecords = [];

            foreach ($employees as $employee) {
                $employeeAssignments = $allAssignments->get($employee->nik, collect());
                $employeeOvertimes = $allOvertimes->get($employee->nik, collect());

                $data = $this->computeEmployeePayrollData($period, $employee, $employeeAssignments, $employeeOvertimes);
                $totalEmployees++;
                $totalGross += $data['gross_salary'];
                $totalNet += $data['take_home_pay'];
                unset($data['gross_salary']);
                $batchRecords[] = $data;
            }

            // Batch upsert in chunks of 250 records (High-performance, minimal queries)
            foreach (array_chunk($batchRecords, 250) as $chunk) {
                PayrollDetail::upsert($chunk, ['payroll_period_id', 'nik'], [
                    'basic_salary',
                    'total_allowances',
                    'total_overtime_pay',
                    'total_deductions',
                    'take_home_pay',
                    'components_breakdown',
                    'status',
                    'updated_at',
                ]);
            }

            $period->update([
                'status' => 'CALCULATED',
            ]);

            return [
                'period' => $period->fresh(),
                'total_employees' => $totalEmployees,
                'total_gross' => $totalGross,
                'total_net' => $totalNet,
            ];
        });
    }

    /**
     * Compute raw payroll calculation data in memory without DB write
     */
    public function computeEmployeePayrollData(
        PayrollPeriod $period,
        Karyawan $employee,
        $preloadedAssignments = null,
        $preloadedOvertime = null
    ): array {
        // 1. Fetch assigned salary components (use preloaded if available)
        $assignments = $preloadedAssignments !== null ? $preloadedAssignments : EmployeeSalaryAssignment::with('component')
            ->where('nik', $employee->nik)
            ->where('is_active', true)
            ->get();

        $basicSalary = 0.0;
        $allowances = [];
        $deductions = [];

        foreach ($assignments as $assignment) {
            $component = $assignment->component;
            if (!$component || !$component->is_active) continue;

            $amount = (float) $assignment->amount;

            if ($component->code === 'BASIC_SALARY') {
                $basicSalary = $amount;
            } elseif ($component->type === 'EARNING') {
                $allowances[] = [
                    'code' => $component->code,
                    'name' => $component->name,
                    'amount' => $amount,
                    'is_fixed' => $component->is_fixed,
                ];
            } elseif ($component->type === 'DEDUCTION') {
                $deductions[] = [
                    'code' => $component->code,
                    'name' => $component->name,
                    'amount' => $amount,
                ];
            }
        }

        // 2. Calculate approved overtime pay from lembur within cutoff dates
        $overtimeRecords = $preloadedOvertime !== null ? $preloadedOvertime : Lembur::where('nik', $employee->nik)
            ->whereBetween('tanggal', [$period->cutoff_start->toDateString(), $period->cutoff_end->toDateString()])
            ->where('status', 'APPROVED')
            ->get();

        $totalRateHours = (float) $overtimeRecords->sum('calculated_rate_hours');

        // Upah 1 jam = 1/173 x (Gaji Pokok + Tunjangan Tetap) PP 35/2021
        $fixedAllowancesTotal = collect($allowances)->where('is_fixed', true)->sum('amount');
        $hourlyRateBasis = $basicSalary + $fixedAllowancesTotal;
        $hourlyWage = $hourlyRateBasis > 0 ? round($hourlyRateBasis / 173, 2) : 0.0;
        $overtimePay = round($totalRateHours * $hourlyWage, 0);

        // 3. Totals
        $totalAllowances = collect($allowances)->sum('amount');
        $totalDeductions = collect($deductions)->sum('amount');
        $takeHomePay = max(0, ($basicSalary + $totalAllowances + $overtimePay) - $totalDeductions);

        // 4. Construct components breakdown snapshot
        $breakdown = [
            'earnings' => array_merge(
                [['code' => 'BASIC_SALARY', 'name' => 'Gaji Pokok', 'amount' => $basicSalary]],
                $allowances,
                [['code' => 'OVERTIME', 'name' => 'Upah Lembur', 'amount' => $overtimePay, 'rate_hours' => $totalRateHours, 'hourly_wage' => $hourlyWage, 'count_spk' => $overtimeRecords->count()]]
            ),
            'deductions' => $deductions,
            'summary' => [
                'basic_salary' => $basicSalary,
                'total_allowances' => $totalAllowances,
                'total_overtime' => $overtimePay,
                'gross_salary' => $basicSalary + $totalAllowances + $overtimePay,
                'total_deductions' => $totalDeductions,
                'net_salary' => $takeHomePay,
            ],
            'calculated_at' => now()->toIso8601String(),
        ];

        return [
            'payroll_period_id' => $period->id,
            'nik' => $employee->nik,
            'basic_salary' => $basicSalary,
            'total_allowances' => $totalAllowances,
            'total_overtime_pay' => $overtimePay,
            'total_deductions' => $totalDeductions,
            'take_home_pay' => $takeHomePay,
            'components_breakdown' => json_encode($breakdown),
            'status' => 'DRAFT',
            'created_at' => now(),
            'updated_at' => now(),
            'gross_salary' => $basicSalary + $totalAllowances + $overtimePay,
        ];
    }

    /**
     * Calculate payroll for a single employee and persist snapshot
     */
    public function calculateEmployeePayroll(
        PayrollPeriod $period,
        Karyawan $employee,
        $preloadedAssignments = null,
        $preloadedOvertime = null
    ): PayrollDetail {
        $data = $this->computeEmployeePayrollData($period, $employee, $preloadedAssignments, $preloadedOvertime);
        unset($data['gross_salary']);
        $data['components_breakdown'] = json_decode($data['components_breakdown'], true);

        // Update or create snapshot record
        return PayrollDetail::updateOrCreate(
            [
                'payroll_period_id' => $period->id,
                'nik' => $employee->nik,
            ],
            $data
        );
    }

    /**
     * Finalize payroll period (lock calculations)
     */
    public function finalizePeriod(PayrollPeriod $period, int $userId): PayrollPeriod
    {
        $period->update([
            'status' => 'FINALIZED',
            'finalized_by' => $userId,
            'finalized_at' => now(),
        ]);

        // Lock all detail records
        $period->details()->update(['status' => 'VERIFIED']);

        return $period->fresh();
    }

    /**
     * Reopen payroll period back to draft/review
     */
    public function reopenPeriod(PayrollPeriod $period): PayrollPeriod
    {
        $period->update([
            'status' => 'REVIEW',
            'finalized_by' => null,
            'finalized_at' => null,
        ]);

        return $period->fresh();
    }
}
