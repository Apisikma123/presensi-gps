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
     * Display presets matrix list
     */
    public function index()
    {
        $presets = $this->presetService->getPresets();
        $activeFeatures = ModuleFeature::where('is_enabled', true)->pluck('module_code')->toArray();
        $activePresetCode = $this->presetService->getActivePresetCode();
        $isLocked = $this->entitlementService->isDeploymentLocked();
        $currentPackage = $this->entitlementService->getCurrentPackage();

        return view('settings.presets.index', compact('presets', 'activeFeatures', 'activePresetCode', 'isLocked', 'currentPackage'));
    }

    /**
     * Apply selected preset configuration.
     * Enforces deployment lock and entitlement boundaries.
     */
    public function apply(Request $request)
    {
        $request->validate([
            'preset_code' => 'required|string|in:A,B,C,D,E',
        ]);

        // 1. Deployment lock protection
        if ($this->entitlementService->isDeploymentLocked() && !$this->entitlementService->isMaintenanceMode()) {
            return redirect()->route('settings.presets.index')->with('error', 'Profil deployment telah dikunci. Penerapan preset baru hanya dapat dilakukan melalui mode pemeliharaan deployment.');
        }

        try {
            $result = $this->presetService->applyPreset($request->preset_code, Auth::id());
            return redirect()->route('settings.presets.index')->with('success', $result['message']);
        } catch (\Throwable $e) {
            return redirect()->route('settings.presets.index')->with('error', 'Gagal menerapkan preset: ' . $e->getMessage());
        }
    }
}
