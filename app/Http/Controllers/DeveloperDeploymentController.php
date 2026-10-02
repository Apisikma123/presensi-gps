<?php

namespace App\Http\Controllers;

use App\Models\ModuleFeature;
use App\Services\ClientPresetService;
use App\Services\ModuleEntitlementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

class DeveloperDeploymentController extends Controller
{
    protected ModuleEntitlementService $entitlementService;
    protected ClientPresetService $presetService;

    // Session authorization lifetime: 30 minutes (1800 seconds)
    const AUTH_TIMEOUT = 1800;

    public function __construct(ModuleEntitlementService $entitlementService, ClientPresetService $presetService)
    {
        $this->entitlementService = $entitlementService;
        $this->presetService = $presetService;
    }

    /**
     * Check if Developer Setup Console is enabled in deployment config.
     * Default: FALSE. When false, all vendor deployment endpoints return 404.
     */
    protected function isSetupEnabled(): bool
    {
        return (bool) config('presence_deployment.vendor_setup_enabled', false);
    }

    /**
     * Verify that current session has an active, unexpired vendor authorization.
     */
    protected function isSessionAuthorized(Request $request): bool
    {
        if (!$request->hasSession()) {
            return false;
        }

        $isAuthorized = $request->session()->get('vendor_deployment_authorized') === true;
        $authorizedAt = (int) $request->session()->get('vendor_authorized_at', 0);

        if ($isAuthorized && (time() - $authorizedAt) < self::AUTH_TIMEOUT) {
            return true;
        }

        // Expired or unauthorized
        $request->session()->forget(['vendor_deployment_authorized', 'vendor_authorized_at']);
        return false;
    }

    /**
     * Display Developer Setup Console or Minimal Verification Form
     */
    public function index(Request $request)
    {
        // 1. Must be enabled via config/env flag
        if (!$this->isSetupEnabled()) {
            abort(404);
        }

        // 2. Must be authenticated with super admin role
        if (!Auth::check() || !Auth::user()->hasRole('super admin')) {
            abort(404);
        }

        // 3. If session not authorized, show minimal vendor verification form
        if (!$this->isSessionAuthorized($request)) {
            return view('vendor.auth');
        }

        $currentPackage = $this->entitlementService->getCurrentPackage();
        $allPackages = ModuleEntitlementService::getDefinedPackages();
        $activePreset = $this->presetService->getActivePresetCode();
        $isLocked = $this->entitlementService->isDeploymentLocked();
        $packagePresetMap = ModuleEntitlementService::$packageDefaultPresetMap;

        $history = DB::getSchemaBuilder()->hasTable('deployment_package_history')
            ? DB::table('deployment_package_history')->latest('created_at')->take(5)->get()
            : collect([]);

        return view('vendor.deployment_setup', compact(
            'currentPackage',
            'allPackages',
            'activePreset',
            'isLocked',
            'packagePresetMap',
            'history'
        ));
    }

    /**
     * Verify Vendor Key (Submitted via POST body only, strict rate limit, constant-time compare)
     */
    public function authenticate(Request $request)
    {
        if (!$this->isSetupEnabled() || !Auth::check() || !Auth::user()->hasRole('super admin')) {
            abort(404);
        }

        // Rate limit: 5 attempts per minute per IP
        $throttleKey = 'vendor_auth_throttle:' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', "Terlalu banyak percobaan otorisasi. Silakan coba lagi dalam {$seconds} detik.");
        }
        RateLimiter::hit($throttleKey, 60);

        $request->validate([
            'vendor_key' => 'required|string',
        ]);

        $configuredKey = (string) config('presence_deployment.vendor_key');
        $inputKey = (string) $request->input('vendor_key');

        // Constant-time comparison. Never disclose key details.
        if (empty($configuredKey) || !hash_equals($configuredKey, $inputKey)) {
            return back()->with('error', 'Kunci otorisasi vendor deployment tidak valid.');
        }

        // Clear rate limiter upon successful authentication
        RateLimiter::clear($throttleKey);

        // Regenerate session ID and store vendor authorization marker with timestamp
        if ($request->hasSession()) {
            $request->session()->regenerate();
            $request->session()->put('vendor_deployment_authorized', true);
            $request->session()->put('vendor_authorized_at', time());
        }

        return redirect()->route('vendor.deployment.index')->with('success', 'Otorisasi vendor berhasil diverifikasi.');
    }

    /**
     * API Diff Preview for Package Selection
     */
    public function preview(Request $request)
    {
        if (!$this->isSetupEnabled() || !$this->isSessionAuthorized($request)) {
            abort(404);
        }

        $request->validate([
            'target_package' => 'required|string',
        ]);

        $targetCode = strtoupper(trim($request->target_package));

        try {
            $preview = $this->entitlementService->previewPackageChange($targetCode);

            return response()->json([
                'success' => true,
                'preview' => $preview,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Apply Selected Package, Entitlements, Default Preset, and Lock Deployment
     */
    public function apply(Request $request)
    {
        if (!$this->isSetupEnabled() || !$this->isSessionAuthorized($request)) {
            abort(404);
        }

        $request->validate([
            'target_package' => 'required|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $targetCode = strtoupper(trim($request->target_package));
        $notes = $request->notes ?: "Vendor Web Setup Console deployment: {$targetCode}";

        try {
            $result = $this->entitlementService->applyPackageChange($targetCode, [], Auth::user(), $notes);
            $defaultPreset = ModuleEntitlementService::$packageDefaultPresetMap[$targetCode] ?? 'None';

            // Invalidate vendor authorization session immediately after apply
            if ($request->hasSession()) {
                $request->session()->forget(['vendor_deployment_authorized', 'vendor_authorized_at']);
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'package_code' => $result['package_code'],
                    'preset' => "Preset {$defaultPreset}",
                    'message' => "Paket {$result['package_code']} berhasil diterapkan. Preset {$defaultPreset} diaplikasikan dan deployment telah otomatis DIKUNCI.",
                ]);
            }

            return redirect()->route('vendor.deployment.index')->with(
                'success',
                "Paket {$result['package_code']} berhasil diterapkan. Preset {$defaultPreset} diaplikasikan dan deployment telah otomatis DIKUNCI."
            );
        } catch (\Throwable $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menerapkan paket: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->route('vendor.deployment.index')->with('error', 'Gagal menerapkan paket: ' . $e->getMessage());
        }
    }
}
