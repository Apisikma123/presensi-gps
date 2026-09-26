<?php

namespace App\Services;

use App\Models\EmployeeLoan;
use App\Models\EmployeeResignation;
use App\Models\Karyawan;
use App\Models\OffboardingClearance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OffboardingService
{
    protected EmployeeLifecycleService $lifecycleService;

    public function __construct(EmployeeLifecycleService $lifecycleService)
    {
        $this->lifecycleService = $lifecycleService;
    }

    /**
     * Generate standard Indonesian exit clearances for a resignation record
     */
    public function generateDefaultClearances(EmployeeResignation $resignation): void
    {
        $items = [
            // IT
            ['item' => 'Pengembalian Laptop & Perangkat Kerja IT', 'dept' => 'IT'],
            ['item' => 'Penonaktifan Email Perusahaan, Akun Sistem & VPN', 'dept' => 'IT'],
            // HR
            ['item' => 'Penyerahan Kartu Identitas (ID Card) & Kunci Akses', 'dept' => 'HR'],
            ['item' => 'Pelaksanaan Exit Interview & Pengisian Feedback', 'dept' => 'HR'],
            ['item' => 'Penerbitan Surat Pengalaman Kerja (Paklaring)', 'dept' => 'HR'],
            // Finance
            ['item' => 'Pemeriksaan & Pelunasan Saldo Kasbon/Pinjaman Karyawan', 'dept' => 'FINANCE'],
            ['item' => 'Penyelesaian Klaim Reimbursement Terakhir', 'dept' => 'FINANCE'],
            // Operational
            ['item' => 'Serah Terima Dokumen & Tanggung Jawab Pekerjaan (Handover)', 'dept' => 'OPERATIONAL'],
        ];

        foreach ($items as $item) {
            OffboardingClearance::create([
                'employee_resignation_id' => $resignation->id,
                'item_name' => $item['item'],
                'department' => $item['dept'],
                'is_cleared' => false,
            ]);
        }
    }

    /**
     * Toggle or sign off clearance checklist item
     */
    public function toggleClearanceItem(int $clearanceId, ?int $userId = null, bool $cleared = true, ?string $notes = null): OffboardingClearance
    {
        return DB::transaction(function () use ($clearanceId, $userId, $cleared, $notes) {
            $clearance = OffboardingClearance::findOrFail($clearanceId);
            $clearance->is_cleared = $cleared;
            $clearance->cleared_by = $cleared ? $userId : null;
            $clearance->cleared_at = $cleared ? Carbon::now() : null;
            if ($notes !== null) {
                $clearance->notes = $notes;
            }
            $clearance->save();

            // Update parent resignation clearance status
            $resignation = $clearance->resignation;
            if ($resignation) {
                $pendingCount = $resignation->clearances()->where('is_cleared', false)->count();
                if ($pendingCount === 0) {
                    $resignation->status_clearance = 'CLEARED';
                } else {
                    $clearedCount = $resignation->clearances()->where('is_cleared', true)->count();
                    $resignation->status_clearance = $clearedCount > 0 ? 'IN_PROGRESS' : 'PENDING';
                }
                $resignation->save();
            }

            return $clearance;
        });
    }

    /**
     * Calculate statutory Indonesian exit settlement (Pesangon, UPMK, UPH per PP 35/2021)
     */
    public function calculateSeverance(string $nik, float $monthlyWage, Carbon $hireDate, Carbon $exitDate, string $category): array
    {
        $diffYears = $hireDate->diffInYears($exitDate);
        $diffMonths = $hireDate->diffInMonths($exitDate);

        // 1. Pesangon Standard Table (PP 35/2021 Pasal 40 Ayat 2)
        $severanceMonths = match (true) {
            $diffYears < 1 => 1,
            $diffYears < 2 => 2,
            $diffYears < 3 => 3,
            $diffYears < 4 => 4,
            $diffYears < 5 => 5,
            $diffYears < 6 => 6,
            $diffYears < 7 => 7,
            $diffYears < 8 => 8,
            default => 9,
        };

        // 2. UPMK Standard Table (PP 35/2021 Pasal 40 Ayat 3)
        $serviceMonths = match (true) {
            $diffYears < 3 => 0,
            $diffYears < 6 => 2,
            $diffYears < 9 => 3,
            $diffYears < 12 => 4,
            $diffYears < 15 => 5,
            $diffYears < 18 => 6,
            $diffYears < 21 => 7,
            $diffYears < 24 => 8,
            default => 10,
        };

        // 3. Category Multipliers per Indonesian Law (PP 35/2021)
        $severanceMultiplier = 0.0;
        $serviceMultiplier = 0.0;
        $compensationPay = 0.0;

        switch ($category) {
            case 'RESIGNED': // Mengundurkan Diri (Hak UPH + Uang Pisah bila ada, tanpa pesangon/UPMK per Ps 50)
                $severanceMultiplier = 0.0;
                $serviceMultiplier = 0.0;
                $compensationPay = round($monthlyWage * 0.15, 2); // Default hak penggantian hak dasar
                break;

            case 'END_OF_CONTRACT': // PKWT Habis: Uang Kompensasi PKWT (Ps 15: Masa Kerja / 12 * Upah)
                $severanceMonths = 0;
                $serviceMonths = 0;
                $compensationPay = round(($diffMonths / 12) * $monthlyWage, 2);
                break;

            case 'TERMINATED': // PHK Standar
                $severanceMultiplier = 1.0;
                $serviceMultiplier = 1.0;
                $compensationPay = round($monthlyWage * 0.15, 2);
                break;

            case 'RETIRED': // Pensiun (Ps 56: 1.75x Pesangon + 1x UPMK + UPH)
                $severanceMultiplier = 1.75;
                $serviceMultiplier = 1.0;
                $compensationPay = round($monthlyWage * 0.15, 2);
                break;

            case 'DECEASED': // Meninggal Dunia (Ps 57: 2x Pesangon + 1x UPMK + UPH)
                $severanceMultiplier = 2.0;
                $serviceMultiplier = 1.0;
                $compensationPay = round($monthlyWage * 0.15, 2);
                break;

            default:
                $severanceMultiplier = 1.0;
                $serviceMultiplier = 1.0;
                $compensationPay = 0.0;
                break;
        }

        $severancePay = round($severanceMonths * $monthlyWage * $severanceMultiplier, 2);
        $servicePay = round($serviceMonths * $monthlyWage * $serviceMultiplier, 2);

        // Check active loan deductions
        $activeLoanBalance = (float) EmployeeLoan::where('nik', $nik)
            ->where('status', 'ACTIVE')
            ->sum('remaining_amount');

        $totalSettlement = max(0, ($severancePay + $servicePay + $compensationPay) - $activeLoanBalance);

        return [
            'tenure_years' => $diffYears,
            'tenure_months' => $diffMonths,
            'monthly_wage' => $monthlyWage,
            'severance_months' => $severanceMonths,
            'severance_multiplier' => $severanceMultiplier,
            'severance_pay' => $severancePay,
            'service_months' => $serviceMonths,
            'service_multiplier' => $serviceMultiplier,
            'service_pay' => $servicePay,
            'compensation_pay' => $compensationPay,
            'active_loan_deduction' => $activeLoanBalance,
            'total_settlement' => $totalSettlement,
        ];
    }

    /**
     * Save exit settlement details to resignation record
     */
    public function saveSettlement(int $resignationId, array $data): EmployeeResignation
    {
        $resignation = EmployeeResignation::findOrFail($resignationId);

        $severance = (float) ($data['severance_pay'] ?? 0);
        $service = (float) ($data['service_pay'] ?? 0);
        $compensation = (float) ($data['compensation_pay'] ?? 0);
        $finalSalary = (float) ($data['final_salary_pay'] ?? 0);
        $deductions = (float) ($data['deductions_pay'] ?? 0);

        $total = ($severance + $service + $compensation + $finalSalary) - $deductions;

        $resignation->update([
            'severance_pay' => $severance,
            'service_pay' => $service,
            'compensation_pay' => $compensation,
            'final_salary_pay' => $finalSalary,
            'deductions_pay' => $deductions,
            'total_settlement' => max(0, $total),
            'settlement_status' => $data['settlement_status'] ?? 'CALCULATED',
        ]);

        return $resignation;
    }

    /**
     * Finalize offboarding, mark paid, and deactivate employee
     */
    public function finalizeOffboarding(int $resignationId): bool
    {
        return DB::transaction(function () use ($resignationId) {
            $resignation = EmployeeResignation::findOrFail($resignationId);
            $resignation->settlement_status = 'PAID';
            $resignation->status_clearance = 'CLEARED';
            $resignation->save();

            // Deactivate employee and terminate contracts
            return $this->lifecycleService->processResignation($resignation, true);
        });
    }
}
