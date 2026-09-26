<?php

namespace App\Services;

use App\Models\Lembur;
use App\Models\OvertimePolicy;
use Carbon\Carbon;

class OvertimeCalculatorService
{
    /**
     * Calculate equivalent rate hours for overtime according to statutory rules (PP 35/2021)
     *
     * @param float $durationHours Duration in decimal hours (e.g. 2.5)
     * @param string $dayType WORKDAY, OFFDAY_5DAYS, OFFDAY_6DAYS, PUBLIC_HOLIDAY
     * @param OvertimePolicy|null $policy
     * @return array
     */
    public function calculateRateHours(
        float $durationHours,
        string $dayType = 'WORKDAY',
        ?OvertimePolicy $policy = null
    ): array {
        if ($durationHours <= 0) {
            return [
                'total_duration_hours' => 0.0,
                'day_type' => $dayType,
                'rate_hours' => 0.0,
                'tiers_breakdown' => [],
                'meal_allowance_eligible' => false,
                'policy_name' => 'None',
                'source_reference' => '',
            ];
        }

        $policy = $policy ?? OvertimePolicy::getDefaultPolicy();
        $rules = $policy->rules ?? OvertimePolicy::defaultDepnakerRules();

        $tierConfig = $rules[$dayType] ?? ($rules['WORKDAY'] ?? [
            ['from_hour' => 0, 'to_hour' => 1, 'multiplier' => 1.5],
            ['from_hour' => 1, 'to_hour' => null, 'multiplier' => 2.0],
        ]);

        $totalRateHours = 0.0;
        $breakdown = [];
        $remaining = $durationHours;

        foreach ($tierConfig as $tier) {
            $from = (float) $tier['from_hour'];
            $to = isset($tier['to_hour']) ? (float) $tier['to_hour'] : null;
            $multiplier = (float) $tier['multiplier'];

            if ($durationHours <= $from) {
                continue;
            }

            // Calculate hours applicable in this tier
            if ($to !== null) {
                $tierCapacity = $to - $from;
                $tierHours = min($remaining, $tierCapacity);
            } else {
                $tierHours = $remaining;
            }

            if ($tierHours > 0) {
                $subtotal = round($tierHours * $multiplier, 2);
                $totalRateHours += $subtotal;
                $breakdown[] = [
                    'from_hour' => $from,
                    'to_hour' => $to,
                    'hours' => round($tierHours, 2),
                    'multiplier' => $multiplier,
                    'subtotal_rate_hours' => $subtotal,
                ];
                $remaining -= $tierHours;
            }

            if ($remaining <= 0) {
                break;
            }
        }

        $mealEligible = $policy->requires_meal_allowance_after_4h && ($durationHours >= 4.0);

        return [
            'total_duration_hours' => round($durationHours, 2),
            'day_type' => $dayType,
            'rate_hours' => round($totalRateHours, 2),
            'tiers_breakdown' => $breakdown,
            'meal_allowance_eligible' => $mealEligible,
            'policy_name' => $policy->name,
            'source_reference' => $policy->source_reference ?? '',
        ];
    }

    /**
     * Calculate statutory hourly overtime wage rate:
     * Upah per jam = 1/173 x Upah Sebulan (PP 35/2021 Pasal 32)
     */
    public function calculateHourlyWage(float $monthlySalary): float
    {
        if ($monthlySalary <= 0) {
            return 0.0;
        }
        return round($monthlySalary / 173, 2);
    }

    /**
     * Calculate estimated nominal overtime pay
     */
    public function calculateEstimatedPay(float $rateHours, float $monthlySalary): float
    {
        $hourlyWage = $this->calculateHourlyWage($monthlySalary);
        return round($rateHours * $hourlyWage, 0);
    }

    /**
     * Check compliance limits (max 4h daily, max 18h weekly)
     */
    public function validateLimits(string $nik, string $date, float $newHours, ?OvertimePolicy $policy = null): array
    {
        $policy = $policy ?? OvertimePolicy::getDefaultPolicy();
        $dateObj = Carbon::parse($date);
        $dayStart = $dateObj->copy()->startOfDay();
        $dayEnd = $dateObj->copy()->endOfDay();
        $weekStart = $dateObj->copy()->startOfWeek();
        $weekEnd = $dateObj->copy()->endOfWeek();

        // Existing approved or pending hours today
        $existingTodayMinutes = Lembur::where('nik', $nik)
            ->whereDate('tanggal', $date)
            ->whereIn('status', ['APPROVED', 'PENDING', 'COMPLETED'])
            ->sum('planned_duration_minutes');
        $existingTodayHours = $existingTodayMinutes / 60;
        $totalTodayHours = $existingTodayHours + $newHours;

        // Existing hours this week
        $existingWeekMinutes = Lembur::where('nik', $nik)
            ->whereBetween('tanggal', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->whereIn('status', ['APPROVED', 'PENDING', 'COMPLETED'])
            ->sum('planned_duration_minutes');
        $existingWeekHours = $existingWeekMinutes / 60;
        $totalWeekHours = $existingWeekHours + $newHours;

        $dailyLimit = $policy->max_hours_per_day ?? 4.0;
        $weeklyLimit = $policy->max_hours_per_week ?? 18.0;

        $warnings = [];
        $exceedsDaily = $totalTodayHours > $dailyLimit;
        $exceedsWeekly = $totalWeekHours > $weeklyLimit;

        if ($exceedsDaily) {
            $warnings[] = "Total lembur hari ini ({$totalTodayHours} jam) melebihi batas regulasi ({$dailyLimit} jam/hari).";
        }
        if ($exceedsWeekly) {
            $warnings[] = "Total lembur pekan ini ({$totalWeekHours} jam) melebihi batas regulasi ({$weeklyLimit} jam/pekan).";
        }

        return [
            'valid' => empty($warnings),
            'exceeds_daily' => $exceedsDaily,
            'exceeds_weekly' => $exceedsWeekly,
            'total_today_hours' => round($totalTodayHours, 2),
            'total_week_hours' => round($totalWeekHours, 2),
            'daily_limit' => $dailyLimit,
            'weekly_limit' => $weeklyLimit,
            'warnings' => $warnings,
        ];
    }
}
