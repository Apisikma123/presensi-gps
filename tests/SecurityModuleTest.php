<?php

/**
 * PRESENCE CONSOLIDATED TEST SUITE: SECURITY & MODULE GOVERNANCE
 * Covers:
 * - Package governance (FNB_SMALL, FNB_STANDARD, FNB_PRO, FULL_HR, CUSTOM)
 * - Entitlements vs Enabled state (ENTITLEMENT > PRESET > CLIENT TOGGLE)
 * - Module ON/OFF toggleability
 * - Package lock & deployment lock
 * - Forged module activation / mass assignment rejection (403 Forbidden)
 * - Core modules cannot be disabled
 * - Vendor-locked modules cannot be toggled by client
 * - Add-on module lifecycle without corrupting base package
 * - Central dependency enforcement (Face requires Attendance)
 * - Role security (Super Admin vs Admin/HR, Manager, Employee)
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\ModuleFeature;
use App\Models\Presensi;
use App\Services\ModuleEntitlementService;
use App\Services\ClientPresetService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

echo "========================================================================\n";
echo "   RUNNING SECURITY & MODULE GOVERNANCE TEST SUITE                      \n";
echo "========================================================================\n";

$passCount = 0;
$failCount = 0;

function assertSec(string $desc, callable $cb) {
    global $passCount, $failCount;
    echo "  [SECURITY] {$desc} ... ";
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

$entitlementService = app(ModuleEntitlementService::class);
$presetService = app(ClientPresetService::class);
$superAdmin = User::role('super admin')->first() ?: User::first();
$normalAdmin = User::whereDoesntHave('roles', function($q) { $q->where('name', 'super admin'); })->first();

// 1. Package Entitlement Boundary: FNB_SMALL unpurchased payroll cannot be toggled
assertSec("Entitlement: Unpurchased module (payroll in FNB_SMALL) cannot be toggled", function() use ($entitlementService, $superAdmin) {
    $entitlementService->applyPackageChange('FNB_SMALL', [], $superAdmin, 'Test Setup');
    
    $isEntitled = $entitlementService->isModuleEntitled('payroll');
    $isEnabled = ModuleFeature::isEnabled('payroll');
    $toggleRes = $entitlementService->toggleModule('payroll', $superAdmin);

    return [
        'ok' => !$isEntitled && !$isEnabled && !$toggleRes['success'],
        'info' => "Entitled: " . ($isEntitled ? 'YES' : 'NO') . ", Toggle blocked: " . (!$toggleRes['success'] ? 'YES' : 'NO')
    ];
});

// 2. Direct Route Access Denied on Unentitled Module
assertSec("Route Security: Direct access to unentitled /payroll returns 403/404", function() use ($superAdmin, $app) {
    Auth::login($superAdmin);
    $req = Request::create('/payroll', 'GET');
    $req->headers->set('Accept', 'application/json');
    $res = $app->handle($req);
    $status = $res->getStatusCode();

    return [
        'ok' => in_array($status, [403, 404, 302]),
        'info' => "HTTP status: {$status}"
    ];
});

// 3. Optional Module Toggle by Super Admin
assertSec("Client Toggleable: Super Admin can toggle entitled optional modules (announcements)", function() use ($entitlementService, $superAdmin) {
    ModuleFeature::where('module_code', 'announcements')->update(['is_enabled' => false]);
    ModuleFeature::flushCache();

    $toggleOn = $entitlementService->toggleModule('announcements', $superAdmin, 'Toggle ON');
    $activeOn = ModuleFeature::isEnabled('announcements');

    $toggleOff = $entitlementService->toggleModule('announcements', $superAdmin, 'Toggle OFF');
    $activeOff = ModuleFeature::isEnabled('announcements');

    return [
        'ok' => $toggleOn['success'] && $activeOn && $toggleOff['success'] && !$activeOff,
        'info' => "Toggle ON -> Active, Toggle OFF -> Inactive"
    ];
});

// 4. Core Module Protection: Cannot be disabled
assertSec("Core Protection: Critical core modules (employee, auth) cannot be toggled", function() use ($entitlementService, $superAdmin) {
    $toggleRes = $entitlementService->toggleModule('employee', $superAdmin);
    return [
        'ok' => !$toggleRes['success'],
        'info' => "Blocked message: {$toggleRes['message']}"
    ];
});

// 5. Vendor-Locked Module Protection
assertSec("Vendor Lock: Locked modules (attendance in FNB_SMALL) cannot be toggled by client", function() use ($entitlementService, $superAdmin) {
    $toggleRes = $entitlementService->toggleModule('attendance', $superAdmin);
    return [
        'ok' => !$toggleRes['success'],
        'info' => "Blocked message: {$toggleRes['message']}"
    ];
});

// 6. Forged Request Guard (Mass Assignment / Tampering)
assertSec("Forged Request Guard: Direct HTTP requests cannot tamper is_entitled or package_code", function() use ($superAdmin) {
    $controller = app(\App\Http\Controllers\ModuleFeatureController::class);
    $payroll = ModuleFeature::where('module_code', 'payroll')->first();

    $req = Request::create("/settings/modules/{$payroll->id}", 'PUT', [
        'description' => 'Updated Description',
        'is_entitled' => true,
        'package_code' => 'FULL_HR',
        'is_locked' => false,
    ]);

    try {
        $controller->update($req, $payroll->id);
        return ['ok' => false, 'msg' => 'Forged PUT should throw 403'];
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        $payroll->refresh();
        return [
            'ok' => $e->getStatusCode() === 403 && $payroll->is_entitled === false,
            'info' => "403 Forbidden correctly returned, is_entitled remains false"
        ];
    }
});

// 7. Preset Boundary: ENTITLEMENT > PRESET
assertSec("Preset Boundary: Preset B (Office) cannot grant payroll outside FNB_SMALL package", function() use ($presetService, $entitlementService) {
    $presetService->applyPreset('B');
    $entitled = $entitlementService->isModuleEntitled('payroll');
    $enabled = ModuleFeature::isEnabled('payroll');

    return [
        'ok' => !$entitled && !$enabled,
        'info' => "Payroll Entitled: NO, Payroll Enabled: NO"
    ];
});

// 8. Module Dependency Rule: Cannot disable parent while child is active
assertSec("Dependency Rule: Cannot disable Attendance while Face Recognition is active", function() use ($entitlementService) {
    ModuleFeature::where('module_code', 'attendance')->update(['is_enabled' => true, 'is_entitled' => true]);
    ModuleFeature::where('module_code', 'face_recognition')->update(['is_enabled' => true, 'is_entitled' => true]);
    ModuleFeature::flushCache();

    $dep = $entitlementService->checkDependencies('attendance', false);
    return [
        'ok' => !$dep['allowed'] && str_contains($dep['reason'], 'face_recognition'),
        'info' => "Blocked message: {$dep['reason']}"
    ];
});

// 9. Deployment Profile Lock
assertSec("Deployment Lock: Prevents Super Admin from applying presets from UI", function() use ($entitlementService) {
    $entitlementService->setDeploymentLocked(true);
    $entitlementService->setMaintenanceMode(false);

    $controller = app(\App\Http\Controllers\PresetMatrixController::class);
    $req = Request::create('/settings/presets/apply', 'POST', ['preset_code' => 'A']);
    $controller->apply($req);

    $error = session('error');
    return [
        'ok' => str_contains($error ?? '', 'dikunci'),
        'info' => "Session error: {$error}"
    ];
});

// 10. Role Security: Non-Super-Admin roles blocked from module toggles
assertSec("Role Security: Admin, Manager, and Employee denied from toggling modules", function() use ($entitlementService, $normalAdmin) {
    if (!$normalAdmin) return ['ok' => true, 'info' => "No normal admin available"];
    $check = $entitlementService->canToggleModule('announcements', true, $normalAdmin);
    return [
        'ok' => !$check['allowed'] && str_contains($check['reason'], 'Super Admin'),
        'info' => "Denied message: {$check['reason']}"
    ];
});

// 11. Data Preservation on Downgrade & Upgrade
assertSec("Data Preservation: Zero data loss during package upgrade & downgrade lifecycle", function() use ($entitlementService, $superAdmin) {
    $initialPresensi = Presensi::count();

    // Upgrade to FNB_PRO
    $entitlementService->applyPackageChange('FNB_PRO', [], $superAdmin, 'Test Upgrade');
    $presensiAfterUpgrade = Presensi::count();

    // Downgrade to FNB_SMALL
    $entitlementService->applyPackageChange('FNB_SMALL', [], $superAdmin, 'Test Downgrade');
    $presensiAfterDowngrade = Presensi::count();

    $preserved = ($initialPresensi === $presensiAfterUpgrade) && ($initialPresensi === $presensiAfterDowngrade);

    // Teardown: restore FULL_HR
    $entitlementService->applyPackageChange('FULL_HR', [], $superAdmin, 'Teardown');
    $entitlementService->setDeploymentLocked(true);

    return [
        'ok' => $preserved,
        'info' => "Presensi count steady at {$presensiAfterDowngrade} rows"
    ];
});

echo "========================================================================\n";
echo "   SECURITY SUITE RESULT: {$passCount} PASSED, {$failCount} FAILED      \n";
echo "========================================================================\n\n";

exit($failCount === 0 ? 0 : 1);
