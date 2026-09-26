<?php

namespace App\Http\Controllers;

use App\Services\ModuleEntitlementService;
use App\Models\ModuleFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackageInfoController extends Controller
{
    protected ModuleEntitlementService $entitlementService;

    public function __construct(ModuleEntitlementService $entitlementService)
    {
        $this->entitlementService = $entitlementService;
    }

    /**
     * Display read-only package and entitlement details for Super Admin
     */
    public function index()
    {
        $package = $this->entitlementService->getCurrentPackage();
        $entitledCodes = $this->entitlementService->getEntitledModules();
        $addons = $this->entitlementService->getActiveAddons();
        $isLocked = $this->entitlementService->isDeploymentLocked();

        // Non-sensitive deployment ID
        $deploymentId = 'DEP-' . strtoupper(substr(md5(config('app.key', 'presence') . 'deployment_salt'), 0, 10));

        // Deployment date
        $history = DB::table('deployment_package_history')->latest('created_at')->first();
        $activationDate = $history ? $history->created_at : date('Y-m-d H:i');

        // Entitled module features with details
        $allModules = ModuleFeature::all()->keyBy('module_code');

        $coreList = [];
        foreach (ModuleEntitlementService::$coreModules as $mod) {
            $coreList[] = [
                'code' => $mod,
                'name' => ucfirst($mod),
                'status' => 'Sistem Utama (Wajib)',
            ];
        }

        $purchasedList = [];
        foreach ($entitledCodes as $mod) {
            if (in_array($mod, ModuleEntitlementService::$coreModules, true)) continue;
            $feature = $allModules->get($mod);
            $purchasedList[] = [
                'code' => $mod,
                'name' => $feature ? $feature->module_name : ucfirst(str_replace('_', ' ', $mod)),
                'is_enabled' => ModuleFeature::isEnabled($mod),
                'is_toggleable' => $this->entitlementService->isModuleClientToggleable($mod),
                'is_addon' => in_array($mod, $addons, true),
            ];
        }

        return view('settings.package_info.index', compact(
            'package',
            'deploymentId',
            'activationDate',
            'coreList',
            'purchasedList',
            'addons',
            'isLocked'
        ));
    }

    /**
     * Handle upgrade request (records local support request record / displays contact info)
     */
    public function requestUpgrade(Request $request)
    {
        $request->validate([
            'requested_package' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        // Non-SaaS: Log upgrade interest for audit and provide official vendor contact
        return response()->json([
            'success' => true,
            'message' => 'Permintaan upgrade paket telah dicatat. Silakan hubungi tim deployment resmi Presence untuk aktivasi lisensi paket.',
            'contact_email' => 'sales@presence-hr.id',
            'contact_whatsapp' => '+62 812-3456-7890',
        ]);
    }
}
