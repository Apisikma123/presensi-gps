<?php

namespace App\Services;

use App\Models\IndonesiaPolicyRule;

class IndonesiaTaxService
{
    /**
     * Determine TER Category (A, B, C) based on PTKP Status
     * PP 58/2023 & PMK 168/2023
     */
    public static function getTerCategory(string $ptkpStatus): string
    {
        $status = strtoupper(trim($ptkpStatus));

        return match ($status) {
            'TK/0', 'TK/1', 'K/0' => 'A',
            'TK/2', 'TK/3', 'K/1', 'K/2' => 'B',
            'K/3' => 'C',
            default => 'A',
        };
    }

    /**
     * Calculate Monthly PPh 21 TER based on Gross Salary and PTKP status
     */
    public static function calculatePph21Ter(float $grossSalary, string $ptkpStatus = 'TK/0', bool $hasNpwp = true): array
    {
        $category = self::getTerCategory($ptkpStatus);
        $brackets = self::getBrackets($category);

        $matchedRate = 0.0;
        foreach ($brackets as $b) {
            $max = $b['max'] ?? PHP_FLOAT_MAX;
            if ($grossSalary <= $max) {
                $matchedRate = $b['rate'];
                break;
            }
        }

        // Penalty 20% higher if without NPWP (pre-2024 rule, but kept as optional flag)
        $effectiveRate = $hasNpwp ? $matchedRate : round($matchedRate * 1.2, 4);
        $taxAmount = round($grossSalary * $effectiveRate, 0);

        return [
            'gross_salary' => $grossSalary,
            'ptkp_status' => strtoupper(trim($ptkpStatus)),
            'category' => $category,
            'rate' => $matchedRate,
            'rate_percent' => ($matchedRate * 100) . '%',
            'has_npwp' => $hasNpwp,
            'effective_rate' => $effectiveRate,
            'tax_amount' => $taxAmount,
            'net_after_tax' => $grossSalary - $taxAmount,
        ];
    }

    /**
     * Get Bracket Tables (DB active rule or fallback to official PP 58/2023)
     */
    public static function getBrackets(string $category): array
    {
        $category = strtoupper($category);
        $rule = IndonesiaPolicyRule::getActiveRule('TAX');
        if ($rule && isset($rule->config['ter_' . strtolower($category)])) {
            return $rule->config['ter_' . strtolower($category)];
        }

        return self::defaultBrackets($category);
    }

    public static function defaultBrackets(string $category): array
    {
        return match ($category) {
            'A' => [
                ['max' => 5400000, 'rate' => 0.00],
                ['max' => 5650000, 'rate' => 0.0025],
                ['max' => 5950000, 'rate' => 0.005],
                ['max' => 6300000, 'rate' => 0.0075],
                ['max' => 6750000, 'rate' => 0.01],
                ['max' => 7500000, 'rate' => 0.0125],
                ['max' => 8550000, 'rate' => 0.015],
                ['max' => 9650000, 'rate' => 0.0175],
                ['max' => 10050000, 'rate' => 0.02],
                ['max' => 10350000, 'rate' => 0.0225],
                ['max' => 10700000, 'rate' => 0.025],
                ['max' => 11050000, 'rate' => 0.03],
                ['max' => 11600000, 'rate' => 0.035],
                ['max' => 12500000, 'rate' => 0.04],
                ['max' => 13750000, 'rate' => 0.05],
                ['max' => 15100000, 'rate' => 0.06],
                ['max' => 16950000, 'rate' => 0.07],
                ['max' => 19750000, 'rate' => 0.08],
                ['max' => 24150000, 'rate' => 0.09],
                ['max' => 26450000, 'rate' => 0.10],
                ['max' => 28000000, 'rate' => 0.11],
                ['max' => 30050000, 'rate' => 0.12],
                ['max' => 32400000, 'rate' => 0.13],
                ['max' => 35400000, 'rate' => 0.14],
                ['max' => 39100000, 'rate' => 0.15],
                ['max' => 43850000, 'rate' => 0.16],
                ['max' => 47800000, 'rate' => 0.17],
                ['max' => 51400000, 'rate' => 0.18],
                ['max' => 56300000, 'rate' => 0.19],
                ['max' => 62200000, 'rate' => 0.20],
                ['max' => 68600000, 'rate' => 0.21],
                ['max' => 77500000, 'rate' => 0.22],
                ['max' => 89000000, 'rate' => 0.23],
                ['max' => 103000000, 'rate' => 0.24],
                ['max' => 125000000, 'rate' => 0.25],
                ['max' => 157000000, 'rate' => 0.26],
                ['max' => 206000000, 'rate' => 0.27],
                ['max' => 337000000, 'rate' => 0.28],
                ['max' => 454000000, 'rate' => 0.29],
                ['max' => 550000000, 'rate' => 0.30],
                ['max' => 695000000, 'rate' => 0.31],
                ['max' => 910000000, 'rate' => 0.32],
                ['max' => 1400000000, 'rate' => 0.33],
                ['max' => null, 'rate' => 0.34],
            ],
            'B' => [
                ['max' => 6200000, 'rate' => 0.00],
                ['max' => 6500000, 'rate' => 0.0025],
                ['max' => 6850000, 'rate' => 0.005],
                ['max' => 7300000, 'rate' => 0.0075],
                ['max' => 9200000, 'rate' => 0.01],
                ['max' => 10750000, 'rate' => 0.015],
                ['max' => 11250000, 'rate' => 0.02],
                ['max' => 11600000, 'rate' => 0.025],
                ['max' => 12600000, 'rate' => 0.03],
                ['max' => 13600000, 'rate' => 0.04],
                ['max' => 14950000, 'rate' => 0.05],
                ['max' => 16400000, 'rate' => 0.06],
                ['max' => 18450000, 'rate' => 0.07],
                ['max' => 21850000, 'rate' => 0.08],
                ['max' => 26000000, 'rate' => 0.09],
                ['max' => 27700000, 'rate' => 0.10],
                ['max' => 29350000, 'rate' => 0.11],
                ['max' => 31450000, 'rate' => 0.12],
                ['max' => 33950000, 'rate' => 0.13],
                ['max' => 37100000, 'rate' => 0.14],
                ['max' => 41100000, 'rate' => 0.15],
                ['max' => 45800000, 'rate' => 0.16],
                ['max' => 49500000, 'rate' => 0.17],
                ['max' => 53800000, 'rate' => 0.18],
                ['max' => 58500000, 'rate' => 0.19],
                ['max' => 64000000, 'rate' => 0.20],
                ['max' => 71000000, 'rate' => 0.21],
                ['max' => 80000000, 'rate' => 0.22],
                ['max' => 93000000, 'rate' => 0.23],
                ['max' => 109000000, 'rate' => 0.24],
                ['max' => 129000000, 'rate' => 0.25],
                ['max' => 163000000, 'rate' => 0.26],
                ['max' => 211000000, 'rate' => 0.27],
                ['max' => 374000000, 'rate' => 0.28],
                ['max' => 459000000, 'rate' => 0.29],
                ['max' => 555000000, 'rate' => 0.30],
                ['max' => 704000000, 'rate' => 0.31],
                ['max' => 957000000, 'rate' => 0.32],
                ['max' => 1405000000, 'rate' => 0.33],
                ['max' => null, 'rate' => 0.34],
            ],
            'C' => [
                ['max' => 6600000, 'rate' => 0.00],
                ['max' => 6950000, 'rate' => 0.0025],
                ['max' => 7350000, 'rate' => 0.005],
                ['max' => 7800000, 'rate' => 0.0075],
                ['max' => 8850000, 'rate' => 0.01],
                ['max' => 10900000, 'rate' => 0.015],
                ['max' => 11200000, 'rate' => 0.02],
                ['max' => 12050000, 'rate' => 0.03],
                ['max' => 12950000, 'rate' => 0.04],
                ['max' => 14150000, 'rate' => 0.05],
                ['max' => 15550000, 'rate' => 0.06],
                ['max' => 17050000, 'rate' => 0.07],
                ['max' => 19500000, 'rate' => 0.08],
                ['max' => 22700000, 'rate' => 0.09],
                ['max' => 24700000, 'rate' => 0.10],
                ['max' => 26350000, 'rate' => 0.11],
                ['max' => 28000000, 'rate' => 0.12],
                ['max' => 30450000, 'rate' => 0.13],
                ['max' => 32700000, 'rate' => 0.14],
                ['max' => 36000000, 'rate' => 0.15],
                ['max' => 40100000, 'rate' => 0.16],
                ['max' => 44550000, 'rate' => 0.17],
                ['max' => 48500000, 'rate' => 0.18],
                ['max' => 52800000, 'rate' => 0.19],
                ['max' => 56500000, 'rate' => 0.20],
                ['max' => 62200000, 'rate' => 0.21],
                ['max' => 69500000, 'rate' => 0.22],
                ['max' => 79000000, 'rate' => 0.23],
                ['max' => 92000000, 'rate' => 0.24],
                ['max' => 107000000, 'rate' => 0.25],
                ['max' => 128000000, 'rate' => 0.26],
                ['max' => 161000000, 'rate' => 0.27],
                ['max' => 211000000, 'rate' => 0.28],
                ['max' => 374000000, 'rate' => 0.29],
                ['max' => 459000000, 'rate' => 0.30],
                ['max' => 555000000, 'rate' => 0.31],
                ['max' => 704000000, 'rate' => 0.32],
                ['max' => 957000000, 'rate' => 0.33],
                ['max' => null, 'rate' => 0.34],
            ],
            default => [],
        };
    }
}
