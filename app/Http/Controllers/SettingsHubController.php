<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use App\Models\ModuleFeature;
use App\Models\AttendancePolicy;
use App\Models\OvertimePolicy;
use App\Models\Pengaturanumum;
use App\Models\GlobalJamkerja;
use App\Models\Jamkerja;
use App\Models\KaryawanMenuSetting;
use App\Services\ModuleEntitlementService;
use Illuminate\Http\Request;

class SettingsHubController extends Controller
{
    protected ModuleEntitlementService $entitlementService;

    public function __construct(ModuleEntitlementService $entitlementService)
    {
        $this->entitlementService = $entitlementService;
    }

    /**
     * Display Unified Tabbed Settings Page
     */
    public function index(Request $request)
    {
        // Active tab from query param or default to 'company'
        $activeTab = $request->query('tab', 'company');

        // 1. Profil Perusahaan
        $companySetting = CompanySetting::getSetting();
        $businessTypes = [
            'General / Multi-industry',
            'F&B / Restaurant & Cafe',
            'Retail / E-Commerce',
            'Manufacturing & Factory',
            'Healthcare & Clinic',
            'Logistics & Transportation',
            'Technology / Software',
            'Construction & Real Estate',
            'Education & Training',
            'Hospitality & Hotel',
            'Professional Services & Consulting',
        ];

        $timezones = [
            'Asia/Jakarta' => 'WIB — Waktu Indonesia Barat (Asia/Jakarta)',
            'Asia/Makassar' => 'WITA — Waktu Indonesia Tengah (Asia/Makassar)',
            'Asia/Jayapura' => 'WIT — Waktu Indonesia Timur (Asia/Jayapura)',
        ];

        $currencies = [
            'IDR' => 'IDR — Rupiah (Rp)',
            'USD' => 'USD — US Dollar ($)',
            'SGD' => 'SGD — Singapore Dollar (S$)',
        ];

        $dateFormats = [
            'd-m-Y' => 'DD-MM-YYYY (Contoh: 25-09-2026)',
            'd/m/Y' => 'DD/MM/YYYY (Contoh: 25/09/2026)',
            'Y-m-d' => 'YYYY-MM-DD (Contoh: 2026-09-25)',
            'd M Y' => 'DD Mon YYYY (Contoh: 25 Sep 2026)',
        ];

        // 2. Kebijakan Presensi & GPS
        $attendancePolicy = AttendancePolicy::getActivePolicy();

        // 3. Kebijakan Lembur Depnaker
        $overtimePolicy = OvertimePolicy::getDefaultPolicy();
        $overtimePolicies = OvertimePolicy::orderBy('id', 'desc')->get();

        // 4. Modul & Fitur HR
        $currentPackage = $this->entitlementService->getCurrentPackage();
        $entitledCodes = $this->entitlementService->getEntitledModules();
        $addons = $this->entitlementService->getActiveAddons();
        $isLocked = $this->entitlementService->isDeploymentLocked();

        $allModules = ModuleFeature::orderBy('category')->orderBy('id')->get();
        $entitledModules = $allModules->filter(function ($m) use ($entitledCodes) {
            $canonical = ModuleFeature::canonicalCode($m->module_code);
            return in_array($canonical, $entitledCodes, true);
        })->unique(function ($m) {
            return ModuleFeature::canonicalCode($m->module_code);
        });

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

        $totalEntitled = $entitledModules->count();
        $totalActive = $entitledModules->where('is_enabled', true)->count();

        // 5. Server & Notifikasi
        $generalSetting = Pengaturanumum::where('id', 1)->first();
        $global_jamkerja = GlobalJamkerja::all()->keyBy('hari');
        $jamkerja_list = Jamkerja::orderBy('jam_masuk')->get();
        $karyawan_menus = KaryawanMenuSetting::orderBy('id')->get();

        return view('settings.hub', compact(
            'activeTab',
            'companySetting',
            'businessTypes',
            'timezones',
            'currencies',
            'dateFormats',
            'attendancePolicy',
            'overtimePolicy',
            'overtimePolicies',
            'currentPackage',
            'addons',
            'isLocked',
            'coreModules',
            'activeToggleable',
            'disabledToggleable',
            'vendorLockedModules',
            'totalEntitled',
            'totalActive',
            'generalSetting',
            'global_jamkerja',
            'jamkerja_list',
            'karyawan_menus'
        ));
    }
}
