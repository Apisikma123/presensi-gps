<?php

namespace App\Http\Controllers;

use App\Models\ModuleFeature;
use App\Services\ModuleEntitlementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleFeatureController extends Controller
{
    protected ModuleEntitlementService $entitlementService;

    public function __construct(ModuleEntitlementService $entitlementService)
    {
        $this->entitlementService = $entitlementService;
    }

    /**
     * Display module list for Super Admin.
     * Only displays entitled modules in operational control sections.
     */
    public function index()
    {
        $currentPackage = $this->entitlementService->getCurrentPackage();
        $entitledCodes = $this->entitlementService->getEntitledModules();
        $addons = $this->entitlementService->getActiveAddons();
        $isLocked = $this->entitlementService->isDeploymentLocked();

        // Query only canonical or active records
        $allModules = ModuleFeature::orderBy('category')->orderBy('id')->get();

        // Filter: Entitled modules
        $entitledModules = $allModules->filter(function ($m) use ($entitledCodes) {
            $canonical = ModuleFeature::canonicalCode($m->module_code);
            return in_array($canonical, $entitledCodes, true);
        })->unique(function ($m) {
            return ModuleFeature::canonicalCode($m->module_code);
        });

        // Group entitled modules by control level
        $coreModules = $entitledModules->filter(function ($m) {
            return in_array(ModuleFeature::canonicalCode($m->module_code), ModuleEntitlementService::$coreModules, true)
                || $m->control_level === ModuleEntitlementService::CONTROL_CORE_LOCKED;
        });

        $activeToggleable = $entitledModules->filter(function ($m) {
            $canonical = ModuleFeature::canonicalCode($m->module_code);
            return !in_array($canonical, ModuleEntitlementService::$coreModules, true)
                && $m->is_enabled
                && $this->entitlementService->isModuleClientToggleable($canonical);
        });

        $disabledToggleable = $entitledModules->filter(function ($m) {
            $canonical = ModuleFeature::canonicalCode($m->module_code);
            return !in_array($canonical, ModuleEntitlementService::$coreModules, true)
                && !$m->is_enabled
                && $this->entitlementService->isModuleClientToggleable($canonical);
        });

        $vendorLockedModules = $entitledModules->filter(function ($m) {
            $canonical = ModuleFeature::canonicalCode($m->module_code);
            return !in_array($canonical, ModuleEntitlementService::$coreModules, true)
                && !$this->entitlementService->isModuleClientToggleable($canonical);
        });

        // Unpurchased modules (read-only count/summary)
        $unpurchasedModules = $allModules->filter(function ($m) use ($entitledCodes) {
            $canonical = ModuleFeature::canonicalCode($m->module_code);
            return !in_array($canonical, $entitledCodes, true);
        })->unique(function ($m) {
            return ModuleFeature::canonicalCode($m->module_code);
        });

        $totalEntitled = $entitledModules->count();
        $totalActive = $entitledModules->where('is_enabled', true)->count();
        $totalUnpurchased = $unpurchasedModules->count();

        return view('settings.modules.index', compact(
            'currentPackage',
            'addons',
            'isLocked',
            'coreModules',
            'activeToggleable',
            'disabledToggleable',
            'vendorLockedModules',
            'unpurchasedModules',
            'totalEntitled',
            'totalActive',
            'totalUnpurchased'
        ));
    }

    /**
     * Toggle module status.
     * Strictly verifies entitlement, client-toggleability, dependencies, and role authority.
     */
    public function toggle(Request $request, $id)
    {
        $module = ModuleFeature::find($id);
        if (!$module) {
            // Also allow passing module_code directly
            $module = ModuleFeature::where('module_code', $id)->firstOrFail();
        }

        $canonical = ModuleFeature::canonicalCode($module->module_code);

        // Security check: Entitlement boundary enforcement (deny forged requests)
        if (!$this->entitlementService->isModuleEntitled($canonical)) {
            abort(403, "Akses ditolak: Modul '{$canonical}' tidak termasuk dalam paket yang dibeli.");
        }

        $result = $this->entitlementService->toggleModule($canonical, Auth::user());

        if (!$result['success']) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'code' => 403,
                    'message' => $result['message'],
                ], 403);
            }
            return redirect()->route('module_features.index')->with('error', $result['message']);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'module_code' => $result['module_code'],
                'is_enabled' => $result['is_enabled'],
                'message' => $result['message'],
            ]);
        }

        return redirect()->route('module_features.index')->with('success', $result['message']);
    }

    /**
     * Update module metadata.
     * Prevents mass assignment or tampering with entitlement fields.
     */
    public function update(Request $request, $id)
    {
        $module = ModuleFeature::findOrFail($id);
        $canonical = ModuleFeature::canonicalCode($module->module_code);

        if (!$this->entitlementService->isModuleEntitled($canonical)) {
            abort(403, "Akses ditolak: Modul tidak termasuk dalam paket perusahaan.");
        }

        $request->validate([
            'description' => 'nullable|string|max:500',
            'config' => 'nullable|array',
        ]);

        // Explicit field whitelist: NEVER allow updating is_entitled, control_level, or package_code
        $module->description = $request->description;
        if ($request->has('config')) {
            $module->config = $request->config;
        }
        $module->save();

        ModuleFeature::flushCache();

        return redirect()->route('module_features.index')->with('success', 'Konfigurasi modul ' . $module->module_name . ' berhasil disimpan!');
    }
}
