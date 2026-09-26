<?php

namespace App\Services;

use App\Models\EmployeeLoan;
use App\Models\EmployeeLoanInstallment;
use Carbon\Carbon;

class EmployeeLoanService
{
    /**
     * Auto generate loan number (e.g. KSB-202609-0001)
     */
    public static function generateLoanNumber(): string
    {
        $prefix = 'KSB-' . date('Ym') . '-';
        $last = EmployeeLoan::where('loan_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $lastSeq = (int) substr($last->loan_number, -4);
            $seq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $seq = '0001';
        }

        return $prefix . $seq;
    }

    /**
     * Create/Apply for a new loan / kasbon
     */
    public static function applyLoan(array $data): EmployeeLoan
    {
        $amount = (float) $data['loan_amount'];
        $interest = (float) ($data['interest_rate'] ?? 0.00);
        $months = max(1, (int) ($data['installment_months'] ?? 1));

        $totalAmount = $amount + ($amount * ($interest / 100));
        $monthlyInstallment = round($totalAmount / $months, 2);

        $data['loan_number'] = self::generateLoanNumber();
        $data['total_amount'] = $totalAmount;
        $data['installment_months'] = $months;
        $data['monthly_installment'] = $monthlyInstallment;
        $data['remaining_amount'] = $totalAmount;
        $data['status'] = 'PENDING';

        return EmployeeLoan::create($data);
    }

    /**
     * Approve loan and generate installment schedules
     */
    public static function approveLoan(EmployeeLoan $loan, int $approverId): bool
    {
        $startDate = Carbon::parse($loan->start_date);
        $months = $loan->installment_months;
        $monthly = $loan->monthly_installment;
        $total = $loan->total_amount;
        $accumulated = 0.0;

        // Generate installments
        for ($i = 1; $i <= $months; $i++) {
            $dueDate = $startDate->copy()->addMonths($i - 1);
            $instAmount = ($i === $months) ? ($total - $accumulated) : $monthly;
            $accumulated += $instAmount;

            EmployeeLoanInstallment::create([
                'employee_loan_id' => $loan->id,
                'installment_number' => $i,
                'due_date' => $dueDate,
                'amount' => $instAmount,
                'paid_amount' => 0.00,
                'status' => 'UNPAID',
            ]);
        }

        return $loan->update([
            'status' => 'ACTIVE',
            'approved_by' => $approverId,
            'approved_at' => Carbon::now(),
        ]);
    }

    /**
     * Record payment of an installment
     */
    public static function recordRepayment(EmployeeLoanInstallment $installment, ?float $amount = null, ?int $payrollPeriodId = null): bool
    {
        $payAmount = $amount ?? $installment->amount;
        $installment->update([
            'paid_amount' => $payAmount,
            'paid_at' => Carbon::now(),
            'payroll_period_id' => $payrollPeriodId,
            'status' => 'PAID',
        ]);

        $loan = $installment->loan;
        $newRemaining = max(0.0, $loan->remaining_amount - $payAmount);

        $loan->update([
            'remaining_amount' => $newRemaining,
            'status' => ($newRemaining <= 0) ? 'PAID_OFF' : 'ACTIVE',
        ]);

        return true;
    }
}
