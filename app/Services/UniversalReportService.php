<?php

namespace App\Services;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\EmployeeAsset;
use App\Models\EmployeeLoan;
use App\Models\EmployeeMovement;
use App\Models\EmployeeResignation;
use App\Models\EmployeeTraining;
use App\Models\EmployeeWarning;
use App\Models\Karyawan;
use App\Models\Lembur;
use App\Models\PayrollPeriod;
use App\Models\Presensi;
use App\Models\Reimbursement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UniversalReportService
{
    /**
     * Get workforce headcount and demographics
     */
    public function getHeadcountData(?string $kodeCabang = null, ?string $kodeDept = null): array
    {
        $query = Karyawan::query();

        if ($kodeCabang) {
            $query->where('kode_cabang', $kodeCabang);
        }
        if ($kodeDept) {
            $query->where('kode_dept', $kodeDept);
        }

        $totalEmployees = (clone $query)->count();
        $activeEmployees = (clone $query)->where('status_aktif_karyawan', '1')->count();
        $inactiveEmployees = $totalEmployees - $activeEmployees;

        // By Department
        $byDept = (clone $query)
            ->where('status_aktif_karyawan', '1')
            ->select('kode_dept', DB::raw('count(*) as total'))
            ->groupBy('kode_dept')
            ->pluck('total', 'kode_dept')
            ->toArray();

        $deptNames = Departemen::pluck('nama_dept', 'kode_dept')->toArray();
        $deptDistribution = [];
        foreach ($byDept as $code => $count) {
            $name = $deptNames[$code] ?? ($code ?: 'Tanpa Departemen');
            $deptDistribution[] = ['label' => $name, 'count' => $count];
        }

        // By Branch
        $byBranch = (clone $query)
            ->where('status_aktif_karyawan', '1')
            ->select('kode_cabang', DB::raw('count(*) as total'))
            ->groupBy('kode_cabang')
            ->pluck('total', 'kode_cabang')
            ->toArray();

        $branchNames = Cabang::pluck('nama_cabang', 'kode_cabang')->toArray();
        $branchDistribution = [];
        foreach ($byBranch as $code => $count) {
            $name = $branchNames[$code] ?? ($code ?: 'Pusat');
            $branchDistribution[] = ['label' => $name, 'count' => $count];
        }

        // By Gender
        $genderL = (clone $query)->where('status_aktif_karyawan', '1')->where('jenis_kelamin', 'L')->count();
        $genderP = (clone $query)->where('status_aktif_karyawan', '1')->where('jenis_kelamin', 'P')->count();

        // By Tenure (Masa Kerja)
        $now = Carbon::now();
        $employees = (clone $query)->where('status_aktif_karyawan', '1')->select('tanggal_masuk')->get();

        $tenureUnder1Yr = 0;
        $tenure1to3Yr = 0;
        $tenure3to5Yr = 0;
        $tenureOver5Yr = 0;

        foreach ($employees as $emp) {
            if (!$emp->tanggal_masuk) {
                $tenureUnder1Yr++;
                continue;
            }
            $tgl = Carbon::parse($emp->tanggal_masuk);
            $years = $tgl->diffInYears($now);

            if ($years < 1) {
                $tenureUnder1Yr++;
            } elseif ($years < 3) {
                $tenure1to3Yr++;
            } elseif ($years < 5) {
                $tenure3to5Yr++;
            } else {
                $tenureOver5Yr++;
            }
        }

        return [
            'total_registered' => $totalEmployees,
            'active_headcount' => $activeEmployees,
            'inactive_headcount' => $inactiveEmployees,
            'by_department' => $deptDistribution,
            'by_branch' => $branchDistribution,
            'gender' => [
                'pria' => $genderL,
                'wanita' => $genderP,
            ],
            'tenure' => [
                'under_1_year' => $tenureUnder1Yr,
                '1_to_3_years' => $tenure1to3Yr,
                '3_to_5_years' => $tenure3to5Yr,
                'over_5_years' => $tenureOver5Yr,
            ],
        ];
    }

    /**
     * Get annual employee turnover and retention metrics
     */
    public function getTurnoverData(int $year, ?string $kodeCabang = null, ?string $kodeDept = null): array
    {
        $startOfYear = Carbon::createFromDate($year, 1, 1)->startOfDay();
        $endOfYear = Carbon::createFromDate($year, 12, 31)->endOfDay();

        // New Hires
        $hireQuery = Karyawan::whereBetween('tanggal_masuk', [$startOfYear->toDateString(), $endOfYear->toDateString()]);
        if ($kodeCabang) $hireQuery->where('kode_cabang', $kodeCabang);
        if ($kodeDept) $hireQuery->where('kode_dept', $kodeDept);
        $newHires = $hireQuery->count();

        // Resignations
        $exitQuery = EmployeeResignation::whereBetween('tanggal_keluar', [$startOfYear->toDateString(), $endOfYear->toDateString()]);
        if ($kodeCabang || $kodeDept) {
            $exitQuery->whereHas('karyawan', function ($q) use ($kodeCabang, $kodeDept) {
                if ($kodeCabang) $q->where('kode_cabang', $kodeCabang);
                if ($kodeDept) $q->where('kode_dept', $kodeDept);
            });
        }
        $exits = $exitQuery->count();

        $activeQuery = Karyawan::where('status_aktif_karyawan', '1');
        if ($kodeCabang) $activeQuery->where('kode_cabang', $kodeCabang);
        if ($kodeDept) $activeQuery->where('kode_dept', $kodeDept);
        $activeCount = $activeQuery->count();

        // Turnover rate = (exits / avg headcount) * 100
        $avgHeadcount = max(1, $activeCount + ($exits / 2));
        $turnoverRate = round(($exits / $avgHeadcount) * 100, 2);

        // Movements (Promosi & Mutasi)
        $movementsCount = EmployeeMovement::whereBetween('effective_date', [$startOfYear->toDateString(), $endOfYear->toDateString()])->count();

        return [
            'year' => $year,
            'active_headcount' => $activeCount,
            'new_hires' => $newHires,
            'exits' => $exits,
            'net_growth' => $newHires - $exits,
            'turnover_rate_percent' => $turnoverRate,
            'movements_promotions' => $movementsCount,
        ];
    }

    /**
     * Get attendance, tardiness, and overtime summary for date range
     */
    public function getAttendanceOvertimeSummary(string $startDate, string $endDate, ?string $kodeCabang = null, ?string $kodeDept = null): array
    {
        $presensiQuery = Presensi::whereBetween('tanggal', [$startDate, $endDate]);

        if ($kodeCabang || $kodeDept) {
            $presensiQuery->whereHas('karyawan', function ($q) use ($kodeCabang, $kodeDept) {
                if ($kodeCabang) $q->where('kode_cabang', $kodeCabang);
                if ($kodeDept) $q->where('kode_dept', $kodeDept);
            });
        }

        $totalHadir = (clone $presensiQuery)->where('status', 'h')->count();
        $totalTerlambat = (clone $presensiQuery)->where('status', 'h')->where('is_terlambat', 1)->count();
        $totalIzin = (clone $presensiQuery)->where('status', 'i')->count();
        $totalSakit = (clone $presensiQuery)->where('status', 's')->count();
        $totalCuti = (clone $presensiQuery)->where('status', 'c')->count();

        // Overtime Summary
        $lemburQuery = Lembur::whereBetween('tanggal', [$startDate, $endDate])->where('status', 'APPROVED');
        if ($kodeCabang || $kodeDept) {
            $lemburQuery->whereHas('karyawan', function ($q) use ($kodeCabang, $kodeDept) {
                if ($kodeCabang) $q->where('kode_cabang', $kodeCabang);
                if ($kodeDept) $q->where('kode_dept', $kodeDept);
            });
        }

        $totalLemburHours = (float) $lemburQuery->sum('calculated_rate_hours');
        $totalApprovedMinutes = (int) $lemburQuery->sum('approved_duration_minutes');

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'hadir_count' => $totalHadir,
            'terlambat_count' => $totalTerlambat,
            'izin_count' => $totalIzin,
            'sakit_count' => $totalSakit,
            'cuti_count' => $totalCuti,
            'overtime_approved_hours' => round($totalLemburHours, 2),
            'overtime_approved_minutes' => $totalApprovedMinutes,
        ];
    }

    /**
     * Get compensation, payroll & benefit summary
     */
    public function getCompensationSummary(int $month, int $year): array
    {
        $period = PayrollPeriod::with('details')->where('period_month', $month)->where('period_year', $year)->first();

        $grossTotal = 0.0;
        $deductionsTotal = 0.0;
        $netTotal = 0.0;
        $processedCount = 0;

        if ($period && $period->details->isNotEmpty()) {
            $grossTotal = (float) $period->details->sum('gross_salary');
            $deductionsTotal = (float) $period->details->sum('total_deductions');
            $netTotal = (float) $period->details->sum('net_salary');
            $processedCount = $period->details->count();
        }

        // Reimbursements in month
        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $end = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

        $reimbTotal = (float) Reimbursement::whereBetween('claim_date', [$start, $end])
            ->where('status', 'PAID')
            ->sum('amount');

        // Active Loans
        $loanOutstanding = (float) EmployeeLoan::where('status', 'ACTIVE')->sum('remaining_amount');

        return [
            'period_month' => $month,
            'period_year' => $year,
            'payroll_status' => $period->status ?? 'NOT_GENERATED',
            'employees_processed' => $processedCount,
            'total_gross' => $grossTotal,
            'total_deductions' => $deductionsTotal,
            'total_net_thp' => $netTotal,
            'reimbursement_paid' => $reimbTotal,
            'loan_receivable_balance' => $loanOutstanding,
        ];
    }

    /**
     * Get talent governance overview (training, discipline, assets)
     */
    public function getGovernanceSummary(): array
    {
        return [
            'total_training_hours' => (int) EmployeeTraining::where('status', 'COMPLETED')->sum('duration_hours'),
            'total_trainings_held' => EmployeeTraining::count(),
            'active_warnings' => EmployeeWarning::where('status', 'ACTIVE')->count(),
            'assigned_assets' => EmployeeAsset::where('status', 'ASSIGNED')->count(),
        ];
    }
}
