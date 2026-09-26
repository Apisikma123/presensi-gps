<?php

namespace App\Services;

use App\Models\CompanySetting;
use App\Models\PayrollDetail;

class PayslipService
{
    /**
     * Retrieve structured payslip detail from snapshot
     */
    public static function getPayslipData(PayrollDetail $detail): array
    {
        $detail->load(['karyawan.dpt', 'karyawan.jabatan', 'karyawan.cabang', 'period']);

        $company = CompanySetting::getSetting();
        $employee = $detail->karyawan;
        $period = $detail->period;

        $components = $detail->components_breakdown ?? $detail->component_snapshot ?? [];
        $attendanceSnapshot = $detail->attendance_snapshot ?? $components['attendance'] ?? [];

        // Parse earnings & deductions
        $earnings = $components['earnings'] ?? [];
        $deductions = $components['deductions'] ?? [];

        // Format amounts
        $basicSalary = (float) $detail->basic_salary;
        $grossSalary = (float) ($detail->gross_salary ?? ($basicSalary + ($detail->total_allowances ?? 0) + ($detail->total_overtime_pay ?? 0)));
        $totalDeductions = (float) $detail->total_deductions;
        $netSalary = (float) ($detail->net_salary ?? $detail->take_home_pay);

        $periodCode = $period ? ($period->period_code ?? "{$period->period_year}-{$period->period_month}") : 'P-CURRENT';
        $hash = strtoupper(substr(hash('sha256', "{$detail->id}-{$detail->nik}-{$netSalary}-{$periodCode}"), 0, 16));

        return [
            'detail_id' => $detail->id,
            'company' => [
                'name' => $company->company_name ?? 'Presence Company',
                'address' => $company->address ?? '-',
                'phone' => $company->phone ?? '-',
                'logo_url' => $company->logo_path ?? null,
            ],
            'employee' => [
                'nik' => $employee?->nik ?? $detail->nik,
                'name' => $employee?->nama_karyawan ?? 'Karyawan',
                'department' => $employee?->dpt?->nama_dept ?? '-',
                'job_title' => $employee?->jabatan?->nama_jabatan ?? '-',
                'branch' => $employee?->cabang?->nama_cabang ?? '-',
                'bank_name' => $employee?->nama_bank ?? '-',
                'bank_account' => $employee?->masked_bank_account ?? ($employee?->no_rekening ?? '-'),
                'ptkp_status' => $employee?->ptkp_status ?? 'TK/0',
            ],
            'period' => [
                'code' => $periodCode,
                'month' => $period?->period_month ?? date('m'),
                'year' => $period?->period_year ?? date('Y'),
                'formatted' => $period?->formatted_period ?? date('F Y'),
                'start_date' => $period?->cutoff_start ? $period->cutoff_start->format('d/m/Y') : '-',
                'end_date' => $period?->cutoff_end ? $period->cutoff_end->format('d/m/Y') : '-',
            ],
            'attendance' => $attendanceSnapshot,
            'earnings' => $earnings,
            'deductions' => $deductions,
            'basic_salary' => $basicSalary,
            'gross_salary' => $grossSalary,
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary,
            'terbilang' => self::terbilang($netSalary) . ' Rupiah',
            'verification_code' => $hash,
            'status' => $detail->status,
        ];
    }

    /**
     * Indonesian number to words conversion (Terbilang)
     */
    public static function terbilang(float $number): string
    {
        $number = abs((int) $number);
        $words = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];

        if ($number === 0) {
            return "";
        }

        if ($number < 12) {
            $result = " " . $words[$number];
        } elseif ($number < 20) {
            $result = self::terbilang($number - 10) . " Belas";
        } elseif ($number < 100) {
            $result = self::terbilang((int) ($number / 10)) . " Puluh " . self::terbilang($number % 10);
        } elseif ($number < 200) {
            $result = " Seratus " . self::terbilang($number - 100);
        } elseif ($number < 1000) {
            $result = self::terbilang((int) ($number / 100)) . " Ratus " . self::terbilang($number % 100);
        } elseif ($number < 2000) {
            $result = " Seribu " . self::terbilang($number - 1000);
        } elseif ($number < 1000000) {
            $result = self::terbilang((int) ($number / 1000)) . " Ribu " . self::terbilang($number % 1000);
        } elseif ($number < 1000000000) {
            $result = self::terbilang((int) ($number / 1000000)) . " Juta " . self::terbilang($number % 1000000);
        } elseif ($number < 1000000000000) {
            $result = self::terbilang((int) ($number / 1000000000)) . " Miliar " . self::terbilang(fmod($number, 1000000000));
        } else {
            $result = self::terbilang((int) ($number / 1000000000000)) . " Triliun " . self::terbilang(fmod($number, 1000000000000));
        }

        return trim(preg_replace('/\s+/', ' ', $result));
    }
}
