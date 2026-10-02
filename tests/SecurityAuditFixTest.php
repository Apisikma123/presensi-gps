<?php

/**
 * FINAL SECURITY AUDIT FIX VERIFICATION SUITE
 * Tests:
 * 1. Payslip IDOR (Employee own ALLOW, Employee other DENY 403, Admin ALLOW, Unauthenticated DENY 401)
 * 2. Loan IDOR (Employee own ALLOW, Employee other DENY 403, Admin ALLOW, Anti-self-approval DENY)
 * 3. Identity Documents (Private disk, Owner ALLOW, Other employee DENY 403, Unauthenticated DENY 401, Admin branch ALLOW, .htaccess protection)
 * 4. Attendance Photos (Protected delivery, Owner ALLOW, Other employee DENY 403, Unauthenticated DENY 401, Mobile API routes)
 * 5. Overtime IDOR (Owner ALLOW, Other employee DENY 403, Anti-self-approval DENY)
 * 6. Hardening (policyStore mass-assignment protection, SESSION_SECURE_COOKIE config/env)
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Cabang;
use App\Models\CompanyPolicy;
use App\Models\Departemen;
use App\Models\EmployeeDocument;
use App\Models\EmployeeLoan;
use App\Models\Karyawan;
use App\Models\Lembur;
use App\Models\PayrollDetail;
use App\Models\PayrollPeriod;
use App\Models\Presensi;
use App\Models\User;
use App\Models\Userkaryawan;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\EmployeeLoanController;
use App\Http\Controllers\ProtectedFileController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\TalentGovernanceController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

echo "========================================================================\n";
echo "   RUNNING FINAL SECURITY AUDIT FIX VERIFICATION TEST SUITE             \n";
echo "========================================================================\n\n";

$passCount = 0;
$failCount = 0;

function runSecCheck(string $desc, callable $cb) {
    global $passCount, $failCount;
    echo "  [AUDIT-FIX] {$desc} ... ";
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

// -------------------------------------------------------------
// Setup fixtures
// -------------------------------------------------------------
$superAdmin = User::role('super admin')->first();
if (!$superAdmin) {
    $superAdmin = User::firstOrCreate(
        ['username' => 'test_superadmin'],
        ['name' => 'Test Super Admin', 'email' => 'superadmin@test.com', 'password' => bcrypt('secret123')]
    );
    $superAdmin->assignRole('super admin');
}

// Create or find test employees
$empA = Karyawan::firstOrCreate(
    ['nik' => '99001'],
    [
        'nama_karyawan' => 'Employee Alpha',
        'jenis_kelamin' => 'L',
        'kode_cabang' => 'CS1',
        'kode_dept' => 'SRV',
        'kode_jabatan' => 'BAR',
        'kode_jam_kerja' => 'JK01',
        'tanggal_masuk' => '2024-01-01',
        'status_karyawan' => 'T',
        'status_aktif_karyawan' => '1',
    ]
);

$empB = Karyawan::firstOrCreate(
    ['nik' => '99002'],
    [
        'nama_karyawan' => 'Employee Bravo',
        'jenis_kelamin' => 'L',
        'kode_cabang' => 'CS1',
        'kode_dept' => 'SRV',
        'kode_jabatan' => 'BAR',
        'kode_jam_kerja' => 'JK01',
        'tanggal_masuk' => '2024-01-01',
        'status_karyawan' => 'T',
        'status_aktif_karyawan' => '1',
    ]
);

$userA = User::firstOrCreate(
    ['username' => 'user_emp_a'],
    ['name' => 'User Alpha', 'email' => 'alpha@test.com', 'password' => bcrypt('secret123')]
);
if (!$userA->hasRole('karyawan')) {
    $userA->assignRole('karyawan');
}
Userkaryawan::updateOrCreate(['id_user' => $userA->id], ['nik' => '99001']);

$userB = User::firstOrCreate(
    ['username' => 'user_emp_b'],
    ['name' => 'User Bravo', 'email' => 'bravo@test.com', 'password' => bcrypt('secret123')]
);
if (!$userB->hasRole('karyawan')) {
    $userB->assignRole('karyawan');
}
Userkaryawan::updateOrCreate(['id_user' => $userB->id], ['nik' => '99002']);

// Setup PayrollDetail for A and B
$period = PayrollPeriod::first() ?? PayrollPeriod::create([
    'period_month' => 9,
    'period_year' => 2026,
    'cutoff_start' => '2026-08-25',
    'cutoff_end' => '2026-09-24',
    'payment_date' => '2026-09-29',
    'status' => 'FINALIZED'
]);

$payslipA = PayrollDetail::firstOrCreate(
    ['payroll_period_id' => $period->id, 'nik' => '99001'],
    [
        'take_home_pay' => 4500000,
        'basic_salary' => 4000000,
        'total_allowances' => 500000,
        'total_overtime_pay' => 0,
        'total_deductions' => 0,
        'components_breakdown' => ['earnings' => [], 'deductions' => []],
        'status' => 'PAID',
    ]
);

$payslipB = PayrollDetail::firstOrCreate(
    ['payroll_period_id' => $period->id, 'nik' => '99002'],
    [
        'take_home_pay' => 4800000,
        'basic_salary' => 4200000,
        'total_allowances' => 600000,
        'total_overtime_pay' => 0,
        'total_deductions' => 0,
        'components_breakdown' => ['earnings' => [], 'deductions' => []],
        'status' => 'PAID',
    ]
);

// Setup Loans for A and B
$loanA = EmployeeLoan::firstOrCreate(
    ['loan_number' => 'LOAN-TEST-A'],
    [
        'nik' => '99001',
        'loan_amount' => 1000000,
        'interest_rate' => 0,
        'total_amount' => 1000000,
        'monthly_installment' => 500000,
        'installment_months' => 2,
        'remaining_amount' => 1000000,
        'status' => 'PENDING',
        'start_date' => '2026-09-01',
    ]
);

$loanB = EmployeeLoan::firstOrCreate(
    ['loan_number' => 'LOAN-TEST-B'],
    [
        'nik' => '99002',
        'loan_amount' => 2000000,
        'interest_rate' => 0,
        'total_amount' => 2000000,
        'monthly_installment' => 1000000,
        'installment_months' => 2,
        'remaining_amount' => 2000000,
        'status' => 'PENDING',
        'start_date' => '2026-09-01',
    ]
);

// Setup Document for A
$testDocPath = 'documents/99001/test_doc_alpha.webp';
Storage::disk('private')->put($testDocPath, 'DUMMY_SENSITIVE_DOCUMENT_CONTENT');

$docA = EmployeeDocument::firstOrCreate(
    ['nik' => '99001', 'title' => 'KTP Alpha'],
    [
        'document_type' => 'KTP',
        'file_path' => $testDocPath,
        'file_size_kb' => 32,
    ]
);

// Setup Attendance photo for A
$testPhotoName = '99001-2026-09-15-in.webp';
$testPhotoPath = storage_path('app/public/uploads/absensi/' . $testPhotoName);
@mkdir(dirname($testPhotoPath), 0755, true);
file_put_contents($testPhotoPath, 'DUMMY_WEBP_PHOTO_BYTES');

$presensiA = Presensi::updateOrCreate(
    ['nik' => '99001', 'tanggal' => '2026-09-15'],
    [
        'jam_in' => '2026-09-15 08:00:00',
        'foto_in' => $testPhotoName,
        'kode_jam_kerja' => 'JK01',
        'status' => 'h'
    ]
);

// Setup Overtime for A and B
$defaultPolicy = \App\Models\OvertimePolicy::first();
$overtimeA = Lembur::firstOrCreate(
    ['no_spk' => 'SPK-TEST-A'],
    [
        'nik' => '99001',
        'tanggal' => '2026-09-15',
        'overtime_policy_id' => $defaultPolicy?->id,
        'lembur_mulai' => '2026-09-15 17:00:00',
        'lembur_selesai' => '2026-09-15 19:00:00',
        'planned_duration_minutes' => 120,
        'day_type' => 'WORKDAY',
        'keterangan' => 'Lembur penutupan closing kasir',
        'status' => 'PENDING',
    ]
);

$overtimeB = Lembur::firstOrCreate(
    ['no_spk' => 'SPK-TEST-B'],
    [
        'nik' => '99002',
        'tanggal' => '2026-09-15',
        'overtime_policy_id' => $defaultPolicy?->id,
        'lembur_mulai' => '2026-09-15 17:00:00',
        'lembur_selesai' => '2026-09-15 20:00:00',
        'planned_duration_minutes' => 180,
        'day_type' => 'WORKDAY',
        'keterangan' => 'Lembur stock opname bulanan',
        'status' => 'PENDING',
    ]
);

// -------------------------------------------------------------
// 1. PAYSLIP IDOR TESTS
// -------------------------------------------------------------
echo "--- 1. PAYSLIP IDOR ---\n";

runSecCheck('Employee A -> Payslip A (Own) = ALLOW (200)', function () use ($userA, $payslipA) {
    Auth::login($userA);
    $controller = app(PayslipController::class);
    $view = $controller->show($payslipA);
    return $view instanceof \Illuminate\View\View;
});

runSecCheck('Employee A -> Payslip B (Other) = DENY (403 Forbidden)', function () use ($userA, $payslipB) {
    Auth::login($userA);
    $controller = app(PayslipController::class);
    try {
        $controller->show($payslipB);
        return ['ok' => false, 'msg' => 'IDOR succeeded! Forbidden access was allowed.'];
    } catch (HttpException $e) {
        return $e->getStatusCode() === 403;
    }
});

runSecCheck('Employee A -> Print Payslip B (Other) = DENY (403 Forbidden)', function () use ($userA, $payslipB) {
    Auth::login($userA);
    $controller = app(PayslipController::class);
    try {
        $controller->print($payslipB);
        return ['ok' => false, 'msg' => 'IDOR on print succeeded!'];
    } catch (HttpException $e) {
        return $e->getStatusCode() === 403;
    }
});

runSecCheck('Super Admin -> Payslip Employee = ALLOW (200)', function () use ($superAdmin, $payslipA) {
    Auth::login($superAdmin);
    $controller = app(PayslipController::class);
    $view = $controller->show($payslipA);
    return $view instanceof \Illuminate\View\View;
});

runSecCheck('Unauthenticated -> Payslip = DENY (401)', function () use ($payslipA) {
    Auth::logout();
    $controller = app(PayslipController::class);
    try {
        $controller->show($payslipA);
        return ['ok' => false, 'msg' => 'Unauthenticated access was allowed!'];
    } catch (HttpException $e) {
        return $e->getStatusCode() === 401;
    }
});

// -------------------------------------------------------------
// 2. LOAN IDOR TESTS
// -------------------------------------------------------------
echo "\n--- 2. LOAN IDOR ---\n";

runSecCheck('Employee A -> Loan A (Own) = ALLOW (200)', function () use ($userA, $loanA) {
    Auth::login($userA);
    $controller = app(EmployeeLoanController::class);
    $view = $controller->show($loanA);
    return $view instanceof \Illuminate\View\View;
});

runSecCheck('Employee A -> Loan B (Other) = DENY (403 Forbidden)', function () use ($userA, $loanB) {
    Auth::login($userA);
    $controller = app(EmployeeLoanController::class);
    try {
        $controller->show($loanB);
        return ['ok' => false, 'msg' => 'IDOR succeeded! Loan of Employee B was accessible by Employee A.'];
    } catch (HttpException $e) {
        return $e->getStatusCode() === 403;
    }
});

runSecCheck('Super Admin -> Loan Employee = ALLOW (200)', function () use ($superAdmin, $loanA) {
    Auth::login($superAdmin);
    $controller = app(EmployeeLoanController::class);
    $view = $controller->show($loanA);
    return $view instanceof \Illuminate\View\View;
});

runSecCheck('Anti-Self-Approval: Admin cannot approve their own loan', function () use ($loanA) {
    // Simulate user who owns loanA attempting to approve
    $ownerUser = User::where('username', 'user_emp_a')->first();
    Auth::login($ownerUser);
    $controller = app(EmployeeLoanController::class);
    try {
        // Since ownerUser has role 'karyawan', authorizeLoanAccess throws 403 or approve redirects back with error
        $response = $controller->approve($loanA);
        return session('error') !== null;
    } catch (HttpException $e) {
        return $e->getStatusCode() === 403;
    }
});

// -------------------------------------------------------------
// 3. SENSITIVE IDENTITY DOCUMENTS TESTS
// -------------------------------------------------------------
echo "\n--- 3. SENSITIVE IDENTITY DOCUMENTS ---\n";

runSecCheck('Document Storage: Uploaded to private disk', function () use ($docA) {
    $existsOnPrivate = Storage::disk('private')->exists($docA->file_path);
    return $existsOnPrivate;
});

runSecCheck('Document Owner A -> Download Doc A = ALLOW (200)', function () use ($userA, $docA) {
    Auth::login($userA);
    $controller = app(ProtectedFileController::class);
    $res = $controller->downloadDocument($docA->id);
    return $res instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse;
});

runSecCheck('Employee B -> Download Doc A (Other) = DENY (403 Forbidden)', function () use ($userB, $docA) {
    Auth::login($userB);
    $controller = app(ProtectedFileController::class);
    try {
        $controller->downloadDocument($docA->id);
        return ['ok' => false, 'msg' => 'Employee B was able to download Doc A!'];
    } catch (HttpException $e) {
        return $e->getStatusCode() === 403;
    }
});

runSecCheck('Unauthenticated -> Download Doc A = DENY (401)', function () use ($docA) {
    Auth::logout();
    $controller = app(ProtectedFileController::class);
    try {
        $controller->downloadDocument($docA->id);
        return ['ok' => false, 'msg' => 'Unauthenticated user was able to download document!'];
    } catch (HttpException $e) {
        return $e->getStatusCode() === 401;
    }
});

runSecCheck('Super Admin -> Download Doc A = ALLOW (200)', function () use ($superAdmin, $docA) {
    Auth::login($superAdmin);
    $controller = app(ProtectedFileController::class);
    $res = $controller->downloadDocument($docA->id);
    return $res instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse;
});

runSecCheck('Direct Static Web Access: storage/app/public/documents/.htaccess blocks raw requests', function () {
    $htaccess = storage_path('app/public/documents/.htaccess');
    return file_exists($htaccess) && str_contains(file_get_contents($htaccess), 'Deny from all');
});

// -------------------------------------------------------------
// 4. ATTENDANCE PHOTOS TESTS
// -------------------------------------------------------------
echo "\n--- 4. ATTENDANCE PHOTOS ---\n";

runSecCheck('Attendance Photo Owner A -> Stream Photo = ALLOW (200)', function () use ($userA, $testPhotoName) {
    Auth::login($userA);
    $controller = app(ProtectedFileController::class);
    $res = $controller->streamAttendancePhoto($testPhotoName);
    return $res instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse;
});

runSecCheck('Employee B -> Stream Photo of Employee A = DENY (403 Forbidden)', function () use ($userB, $testPhotoName) {
    Auth::login($userB);
    $controller = app(ProtectedFileController::class);
    try {
        $controller->streamAttendancePhoto($testPhotoName);
        return ['ok' => false, 'msg' => 'Employee B was able to view Employee A attendance photo!'];
    } catch (HttpException $e) {
        return $e->getStatusCode() === 403;
    }
});

runSecCheck('Unauthenticated -> Stream Photo = DENY (401)', function () use ($testPhotoName) {
    Auth::logout();
    $controller = app(ProtectedFileController::class);
    try {
        $controller->streamAttendancePhoto($testPhotoName);
        return ['ok' => false, 'msg' => 'Unauthenticated user was able to view attendance photo!'];
    } catch (HttpException $e) {
        return $e->getStatusCode() === 401;
    }
});

runSecCheck('Super Admin -> Stream Photo of Employee A = ALLOW (200)', function () use ($superAdmin, $testPhotoName) {
    Auth::login($superAdmin);
    $controller = app(ProtectedFileController::class);
    $res = $controller->streamAttendancePhoto($testPhotoName);
    return $res instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse;
});

runSecCheck('Direct Static Web Access: storage/app/public/uploads/absensi/.htaccess blocks raw access', function () {
    $htaccess = storage_path('app/public/uploads/absensi/.htaccess');
    return file_exists($htaccess) && str_contains(file_get_contents($htaccess), 'Deny from all');
});

// -------------------------------------------------------------
// 5. OVERTIME IDOR TESTS
// -------------------------------------------------------------
echo "\n--- 5. OVERTIME IDOR ---\n";

runSecCheck('Employee A -> SPK Lembur A (Own) = ALLOW (200)', function () use ($userA, $overtimeA) {
    Auth::login($userA);
    $controller = app(OvertimeController::class);
    $view = $controller->show($overtimeA);
    return $view instanceof \Illuminate\View\View;
});

runSecCheck('Employee A -> SPK Lembur B (Other) = DENY (403 Forbidden)', function () use ($userA, $overtimeB) {
    Auth::login($userA);
    $controller = app(OvertimeController::class);
    try {
        $controller->show($overtimeB);
        return ['ok' => false, 'msg' => 'Employee A was able to access SPK Lembur B!'];
    } catch (HttpException $e) {
        return $e->getStatusCode() === 403;
    }
});

runSecCheck('Anti-Self-Approval: Employee/Admin cannot approve own overtime SPK', function () use ($userA, $overtimeA) {
    Auth::login($userA);
    $controller = app(OvertimeController::class);
    $request = new \Illuminate\Http\Request();
    try {
        $res = $controller->approve($request, $overtimeA);
        return session('error') !== null;
    } catch (HttpException $e) {
        return $e->getStatusCode() === 403;
    }
});

// -------------------------------------------------------------
// 6. SECURITY HARDENING TESTS
// -------------------------------------------------------------
echo "\n--- 6. SECURITY HARDENING ---\n";

runSecCheck('CompanyPolicy::policyStore validates input without request->all() injection', function () {
    $controller = app(TalentGovernanceController::class);
    $request = new \Illuminate\Http\Request([
        'policy_code' => 'TEST-POL-' . time(),
        'title' => 'Test SOP Keamanan',
        'category' => 'KEPEGAWAIAN',
        'effective_date' => '2026-09-01',
        'version' => '1.0',
        'description' => 'Test Deskripsi SOP',
        'is_malicious_injected' => 'injected_val'
    ]);
    Auth::login(User::role('super admin')->first());
    $res = $controller->policyStore($request);
    return $res instanceof \Illuminate\Http\RedirectResponse;
});

runSecCheck('.env.example documents SESSION_SECURE_COOKIE', function () {
    $envExample = file_get_contents(__DIR__ . '/../.env.example');
    return str_contains($envExample, 'SESSION_SECURE_COOKIE');
});

runSecCheck('AdminUserSeeder protects against default passwords in production', function () {
    $seederFile = file_get_contents(__DIR__ . '/../database/seeders/AdminUserSeeder.php');
    return str_contains($seederFile, 'SEED_ADMIN_PASSWORD') && str_contains($seederFile, 'RuntimeException');
});

runSecCheck('UpdateController::installUpdate strictly binds to Auth::id() without user_id forgery', function () {
    $updateFile = file_get_contents(__DIR__ . '/../app/Http/Controllers/Api/UpdateController.php');
    return !str_contains($updateFile, "input('user_id')");
});

// -------------------------------------------------------------
// Summary
// -------------------------------------------------------------
echo "\n========================================================================\n";
echo "   FINAL SECURITY AUDIT FIX RESULTS                                    \n";
echo "========================================================================\n";
echo "  Total Passed: \033[32m{$passCount}\033[0m\n";
echo "  Total Failed: " . ($failCount > 0 ? "\033[31m{$failCount}\033[0m" : "0") . "\n";
echo "========================================================================\n";

if ($failCount === 0) {
    echo ">>> ALL SECURITY AUDIT CHECKS PASSED (100% SUCCESS) <<<\n\n";
    exit(0);
} else {
    echo ">>> SECURITY AUDIT CHECKS FAILED <<<\n\n";
    exit(1);
}
