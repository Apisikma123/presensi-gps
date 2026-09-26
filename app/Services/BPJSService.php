<?php

namespace App\Services;

use App\Models\IndonesiaPolicyRule;

class BPJSService
{
    /**
     * Calculate BPJS Kesehatan and BPJS Ketenagakerjaan contributions
     * based on statutory regulations.
     */
    public static function calculateBPJS(float $salary, array $customConfig = []): array
    {
        $kesRule = IndonesiaPolicyRule::getActiveRule('BPJS_KES');
        $tkRule = IndonesiaPolicyRule::getActiveRule('BPJS_TK');

        $kesConfig = array_merge([
            'employee_rate' => 0.01,
            'employer_rate' => 0.04,
            'ceiling' => 12000000.00,
        ], $kesRule?->config ?? [], $customConfig['kes'] ?? []);

        $tkConfig = array_merge([
            'jht_employee_rate' => 0.02,
            'jht_employer_rate' => 0.037,
            'jp_employee_rate' => 0.01,
            'jp_employer_rate' => 0.02,
            'jp_ceiling' => 10042300.00,
            'jkk_employer_rate' => 0.0024,
            'jkm_employer_rate' => 0.003,
        ], $tkRule?->config ?? [], $customConfig['tk'] ?? []);

        // 1. BPJS Kesehatan
        $kesBase = min($salary, $kesConfig['ceiling']);
        $kesEmployee = round($kesBase * $kesConfig['employee_rate'], 0);
        $kesEmployer = round($kesBase * $kesConfig['employer_rate'], 0);

        // 2. BPJS Ketenagakerjaan
        // JHT (No ceiling)
        $jhtEmployee = round($salary * $tkConfig['jht_employee_rate'], 0);
        $jhtEmployer = round($salary * $tkConfig['jht_employer_rate'], 0);

        // JP (With ceiling)
        $jpBase = min($salary, $tkConfig['jp_ceiling']);
        $jpEmployee = round($jpBase * $tkConfig['jp_employee_rate'], 0);
        $jpEmployer = round($jpBase * $tkConfig['jp_employer_rate'], 0);

        // JKK (Employer only)
        $jkkEmployer = round($salary * $tkConfig['jkk_employer_rate'], 0);

        // JKM (Employer only)
        $jkmEmployer = round($salary * $tkConfig['jkm_employer_rate'], 0);

        $tkTotalEmployee = $jhtEmployee + $jpEmployee;
        $tkTotalEmployer = $jhtEmployer + $jpEmployer + $jkkEmployer + $jkmEmployer;

        return [
            'gross_salary' => $salary,
            'kesehatan' => [
                'base' => $kesBase,
                'ceiling' => $kesConfig['ceiling'],
                'employee_rate' => $kesConfig['employee_rate'],
                'employer_rate' => $kesConfig['employer_rate'],
                'employee_amount' => $kesEmployee,
                'employer_amount' => $kesEmployer,
                'total' => $kesEmployee + $kesEmployer,
            ],
            'ketenagakerjaan' => [
                'jht' => [
                    'base' => $salary,
                    'employee_rate' => $tkConfig['jht_employee_rate'],
                    'employer_rate' => $tkConfig['jht_employer_rate'],
                    'employee_amount' => $jhtEmployee,
                    'employer_amount' => $jhtEmployer,
                    'total' => $jhtEmployee + $jhtEmployer,
                ],
                'jp' => [
                    'base' => $jpBase,
                    'ceiling' => $tkConfig['jp_ceiling'],
                    'employee_rate' => $tkConfig['jp_employee_rate'],
                    'employer_rate' => $tkConfig['jp_employer_rate'],
                    'employee_amount' => $jpEmployee,
                    'employer_amount' => $jpEmployer,
                    'total' => $jpEmployee + $jpEmployer,
                ],
                'jkk' => [
                    'base' => $salary,
                    'employer_rate' => $tkConfig['jkk_employer_rate'],
                    'employer_amount' => $jkkEmployer,
                ],
                'jkm' => [
                    'base' => $salary,
                    'employer_rate' => $tkConfig['jkm_employer_rate'],
                    'employer_amount' => $jkmEmployer,
                ],
                'total_employee' => $tkTotalEmployee,
                'total_employer' => $tkTotalEmployer,
                'total' => $tkTotalEmployee + $tkTotalEmployer,
            ],
            'total_employee_deduction' => $kesEmployee + $tkTotalEmployee,
            'total_employer_contribution' => $kesEmployer + $tkTotalEmployer,
            'grand_total' => $kesEmployee + $kesEmployer + $tkTotalEmployee + $tkTotalEmployer,
        ];
    }
}
