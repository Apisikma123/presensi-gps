<?php

namespace App\Http\Controllers;

use App\Models\ModuleFeature;
use App\Services\ClientPresetService;
use App\Services\ModuleEntitlementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresetMatrixController extends Controller
{
    protected ClientPresetService $presetService;
    protected ModuleEntitlementService $entitlementService;

    public function __construct(ClientPresetService $presetService, ModuleEntitlementService $entitlementService)
    {
        $this->presetService = $presetService;
        $this->entitlementService = $entitlementService;
    }

    /**
     * Display presets matrix list.
     * Developer/vendor-owned only. Rejects normal client access with 404.
     */
    public function index()
    {
        if (!$this->entitlementService->isMaintenanceMode()) {
            abort(404);
        }

        $presets = $this->presetService->getPresets();
        $activeFeatures = ModuleFeature::where('is_enabled', true)->pluck('module_code')->toArray();
        $activePresetCode = $this->presetService->getActivePresetCode();
        $isLocked = $this->entitlementService->isDeploymentLocked();
        $currentPackage = $this->entitlementService->getCurrentPackage();

        return view('settings.presets.index', compact('presets', 'activeFeatures', 'activePresetCode', 'isLocked', 'currentPackage'));
    }

    /**
     * Apply selected preset configuration.
     * Developer/vendor-owned only. Rejects normal client access with 404/403.
     */
    public function apply(Request $request)
    {
        if (!$this->entitlementService->isMaintenanceMode()) {
            abort(404);
        }

        $request->validate([
            'preset_code' => 'required|string|in:A,B,C,D,E',
        ]);

        // 1. Deployment lock protection
        if ($this->entitlementService->isDeploymentLocked() && !$this->entitlementService->isMaintenanceMode()) {
            abort(403, 'Profil deployment telah dikunci. Penerapan preset baru hanya dapat dilakukan melalui mode pemeliharaan deployment.');
        }

        try {
            $result = $this->presetService->applyPreset($request->preset_code, Auth::id());
            return redirect()->route('settings.presets.index')->with('success', $result['message']);
        } catch (\Throwable $e) {
            return redirect()->route('settings.presets.index')->with('error', 'Gagal menerapkan preset: ' . $e->getMessage());
        }
    }
}
