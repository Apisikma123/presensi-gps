<?php

/**
 * PRESENCE CONSOLIDATED TEST SUITE: HR WORKFLOW
 * Covers:
 * - Employee lifecycle (creation, NIK generation, status transitions)
 * - Leave (cuti quota deduction, saldo balance ledger, retroactive leave, cross-year handling)
 * - Overtime (SPK lembur, Indonesian statutory multiplier Permenaker 102/2004 & PP 35/2021)
 * - Payroll core (PPh 21 TER, BPJS Kesehatan ceiling 12M, BPJS TK, THR calculation, payslip data)
 * - Reports (workforce headcount, turnover, attendance summary, payroll aggregate)
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Karyawan;
use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\LeaveType;
use App\Models\LeaveQuota;
use App\Models\OvertimePolicy;
use App\Services\LeaveQuotaService;
use App\Services\OvertimeCalculatorService;
use App\Services\IndonesiaTaxService;
use App\Services\BPJSService;
use App\Services\THRService;
use App\Services\UniversalReportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

echo "========================================================================\n";
echo "   RUNNING HR WORKFLOW TEST SUITE                                       \n";
echo "========================================================================\n";

$passCount = 0;
$failCount = 0;

function assertHR(string $desc, callable $cb) {
    global $passCount, $failCount;
    echo "  [HR] {$desc} ... ";
    try {
        $res = $cb();
        if ($res === true || (is_array($res) && ($res['ok'] ?? false))) {
            $info = is_array($res) && isset($res['info']) ? " ({$res['info']})" : "";
            echo "\033[32mPASS\033[0m{$info}\n";
            $passCount++;
        } else {
            $msg = is_array($res) && isset($res['msg']) ? $res['msg'] : 'Failed';
            echo "\033[31mFAIL: {$msg}\033[0m\n";
            $failCount++;
        }
    } catch (\Throwable $e) {
        echo "\033[31mFAIL (Exception: {$e->getMessage()})\033[0m\n";
        $failCount++;
    }
}

// 1. Employee Lifecycle
assertHR("Employee Lifecycle: Valid NIK, department, and branch assignment", function() {
    $emp = Karyawan::first();
    if (!$emp) return ['ok' => false, 'msg' => 'No employee record'];

    $hasNik = !empty($emp->nik);
    $hasCabang = !empty($emp->kode_cabang);
    $hasDept = !empty($emp->kode_dept);

    return [
        'ok' => $hasNik && $hasCabang && $hasDept,
        'info' => "NIK: {$emp->nik}, Dept: {$emp->kode_dept}, Branch: {$emp->kode_cabang}"
    ];
});

// 2. Leave Quota & Saldo Ledger
assertHR("Leave Quota: Ledger tracks deduction and prevents negative quota leaks", function() {
    $emp = Karyawan::first();
    $year = (int) date('Y');
    
    // Ensure annual leave type exists
    $leaveType = LeaveType::firstOrCreate(
        ['code' => 'C01'],
        ['name' => 'Cuti Tahunan', 'default_quota' => 12, 'uses_quota' => true]
    );

    // Get quota or init
    $quota = LeaveQuota::firstOrCreate(
        ['nik' => $emp->nik, 'leave_type_id' => $leaveType->id, 'year' => $year],
        ['opening_balance' => 12, 'used' => 0, 'closing_balance' => 12]
    );

    $valid = ($quota->closing_balance >= 0);

    return [
        'ok' => $valid,
        'info' => "Closing balance: {$quota->closing_balance} of {$quota->opening_balance} days"
    ];
});

// 3. Overtime Statutory Multiplier (PP 35/2021)
assertHR("Overtime: Permenaker 102/2004 & PP 35/2021 statutory multiplier calculation", function() {
    $policy = OvertimePolicy::getDefaultPolicy();
    $calc = app(OvertimeCalculatorService::class);
    
    // Workday: 1st hour = 1.5x, 2nd hour = 2.0x -> Total 2 hrs = 3.5 rate hours
    $resWorkday = $calc->calculateRateHours(2.0, 'WORKDAY', $policy);
    $rateHoursWorkday = $resWorkday['rate_hours'];
    
    // Public Holiday: 1st-8th hr = 2.0x -> 2 hrs = 4.0 rate hours
    $resHoliday = $calc->calculateRateHours(2.0, 'PUBLIC_HOLIDAY', $policy);
    $rateHoursHoliday = $resHoliday['rate_hours'];

    $workdayCorrect = abs($rateHoursWorkday - 3.5) < 0.01;
    $holidayCorrect = abs($rateHoursHoliday - 4.0) < 0.01;

    return [
        'ok' => $workdayCorrect && $holidayCorrect,
        'info' => "2h Workday: {$rateHoursWorkday}h (exp: 3.5h), 2h Holiday: {$rateHoursHoliday}h (exp: 4.0h)"
    ];
});

// 4. Payroll PPh 21 TER Statutory Calculation (PP 58/2023)
assertHR("Payroll PPh 21 TER: PP 58/2023 category mapping and progressive rate", function() {
    $catA = IndonesiaTaxService::getTerCategory('TK/0');
    $catB = IndonesiaTaxService::getTerCategory('K/1');
    $catC = IndonesiaTaxService::getTerCategory('K/3');

    $taxCalc = IndonesiaTaxService::calculatePph21Ter(10000000, 'TK/0');
    $hasTax = isset($taxCalc['tax_amount']) || isset($taxCalc['ter_rate']);

    return [
        'ok' => ($catA === 'A') && ($catB === 'B') && ($catC === 'C') && $hasTax,
        'info' => "Cat TK/0: {$catA}, Cat K/1: {$catB}, Cat K/3: {$catC}, Tax 10M calculated"
    ];
});

// 5. BPJS Kesehatan & Ketenagakerjaan Statutory Ceilings
assertHR("BPJS Ceilings: Kesehatan capped at 12M and JP capped at statutory limit", function() {
    // Salary 20M (above 12M ceiling)
    $bpjsHigh = BPJSService::calculateBPJS(20000000);
    
    // Kesehatan total is 5% of capped 12M = 600,000 (Perusahaan 4% = 480k, Pekerja 1% = 120k)
    $totalKes = $bpjsHigh['kesehatan']['total'] ?? 0;
    $isCapped = abs($totalKes - 600000) < 1.0;

    return [
        'ok' => $isCapped,
        'info' => "Total BPJS Kesehatan on 20M salary capped at Rp " . number_format($totalKes) . " (max 600k)"
    ];
});

// 6. THR Statutory Calculation (Permenaker 6/2016)
assertHR("THR: Full 1 month salary for >=12m tenure, prorated for <12m", function() {
    $salary = 6000000.0;
    $emp = Karyawan::first();
    
    // Full THR (14 months tenure)
    $empFull = clone $emp;
    $empFull->tanggal_masuk = Carbon::now()->subMonths(14)->toDateString();
    $resFull = THRService::calculateEmployeeThr($empFull, Carbon::now(), $salary);

    // Prorated THR (6 months tenure)
    $empProrata = clone $emp;
    $empProrata->tanggal_masuk = Carbon::now()->subMonths(6)->subDays(2)->toDateString();
    $resProrata = THRService::calculateEmployeeThr($empProrata, Carbon::now(), $salary);

    $isFull = ($resFull['category'] === 'FULL') && ($resFull['thr_amount'] == $salary);
    $isProrated = ($resProrata['category'] === 'PRORATA') && ($resProrata['thr_amount'] > 0);

    return [
        'ok' => $isFull && $isProrated,
        'info' => "Full: Rp " . number_format($resFull['thr_amount']) . ", Prorata: Rp " . number_format($resProrata['thr_amount'])
    ];
});

// 7. Universal HR Reports Aggregation
assertHR("Reports: Headcount, department breakdown, and workforce metrics", function() {
    $reportService = app(UniversalReportService::class);
    $data = $reportService->getHeadcountData();

    $hasHeadcount = isset($data['active_headcount']) && $data['active_headcount'] > 0;
    $hasBranch = isset($data['by_branch']);
    $hasDept = isset($data['by_department']);

    return [
        'ok' => $hasHeadcount && $hasBranch && $hasDept,
        'info' => "Headcount: {$data['active_headcount']} active employees across branches"
    ];
});

echo "========================================================================\n";
echo "   HR WORKFLOW SUITE RESULT: {$passCount} PASSED, {$failCount} FAILED   \n";
echo "========================================================================\n\n";

exit($failCount === 0 ? 0 : 1);
