<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use App\Models\ModuleFeature;
use App\Models\AttendancePolicy;
use App\Models\OvertimePolicy;
use App\Models\IndonesiaPolicyRule;

class SettingsHubController extends Controller
{
    /**
     * Display centralized settings directory
     */
    public function index()
    {
        $setting = CompanySetting::first();
        $totalModules = ModuleFeature::count();
        $enabledModules = ModuleFeature::where('is_enabled', true)->count();
        $attendancePolicy = AttendancePolicy::first();
        $overtimePolicy = OvertimePolicy::first();
        $activeRulesCount = IndonesiaPolicyRule::where('is_active', true)->count();

        return view('settings.hub', compact(
            'setting',
            'totalModules',
            'enabledModules',
            'attendancePolicy',
            'overtimePolicy',
            'activeRulesCount'
        ));
    }
}
