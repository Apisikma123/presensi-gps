<?php

namespace App\Services;

use App\Models\CompanySetting;
use App\Models\ModuleFeature;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ClientPresetService
{
    protected AuditLogService $auditService;

    public function __construct(AuditLogService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Get all predefined client configuration presets
     */
    public function getPresets(): array
    {
        return [
            'A' => [
                'code' => 'A',
                'name' => 'Preset A: Small Cafe & F&B',
                'subtitle' => '2 branches, rotating shifts, GPS + Face recognition, leave, no payroll',
                'badge' => 'Cafe / Resto',
                'icon' => 'ti-coffee',
                'color' => '#3c2a21',
                'enabled_modules' => ['attendance', 'gps', 'face_recognition', 'leave', 'reports', 'settings', 'organization'],
                'disabled_modules' => ['payroll', 'recruitment', 'performance', 'loans', 'reimbursement', 'overtime', 'contracts', 'movements', 'resignations', 'onboarding', 'offboarding', 'governance', 'documents', 'announcements'],
                'settings' => [
                    'company_type' => 'Food & Beverage',
                    'attendance_require_gps' => '1',
                    'attendance_require_face' => '1',
                    'attendance_radius_meters' => '50',
                ],
                'must_work' => ['Boot cleanly', 'Clock in/out with GPS+Face', 'Cuti / Leave submission', 'Attendance Reports'],
            ],
            'B' => [
                'code' => 'B',
                'name' => 'Preset B: Corporate Office',
                'subtitle' => '1 office, Mon-Fri, GPS only, leave, full statutory payroll (PPh 21, BPJS, THR)',
                'badge' => 'Kantor / Korporasi',
                'icon' => 'ti-briefcase',
                'color' => '#3c2a21',
                'enabled_modules' => ['attendance', 'gps', 'leave', 'payroll', 'contracts', 'reports', 'settings', 'organization', 'documents', 'announcements'],
                'disabled_modules' => ['face_recognition', 'recruitment', 'loans', 'performance', 'reimbursement', 'overtime', 'movements', 'resignations', 'onboarding', 'offboarding', 'governance'],
                'settings' => [
                    'company_type' => 'Corporate / Agency',
                    'attendance_require_gps' => '1',
                    'attendance_require_face' => '0',
                    'work_days_per_week' => '5',
                ],
                'must_work' => ['Full monthly payroll generation', 'PPh 21 TER calculation', 'BPJS TK & Kes deductions', 'THR calculation'],
            ],
            'C' => [
                'code' => 'C',
                'name' => 'Preset C: Retail & Chain Stores',
                'subtitle' => 'Multiple branches, weekend shifts, rotating OFF, Depnaker overtime, payroll',
                'badge' => 'Retail / Toko',
                'icon' => 'ti-shopping-cart',
                'color' => '#3c2a21',
                'enabled_modules' => ['attendance', 'gps', 'leave', 'overtime', 'payroll', 'contracts', 'reports', 'settings', 'organization', 'announcements'],
                'disabled_modules' => ['face_recognition', 'recruitment', 'loans', 'performance', 'reimbursement', 'movements', 'resignations', 'onboarding', 'offboarding', 'governance', 'documents'],
                'settings' => [
                    'company_type' => 'Retail / Chain Stores',
                    'attendance_require_gps' => '1',
                    'attendance_require_face' => '0',
                    'overtime_depnaker_rules' => '1',
                ],
                'must_work' => ['Multi-branch rosters', 'Depnaker overtime tiers (1.5x, 2.0x, 3.0x, 4.0x)', 'Payroll with overtime snapshot'],
            ],
            'D' => [
                'code' => 'D',
                'name' => 'Preset D: Professional Service Company',
                'subtitle' => '1 location, standard attendance, no face, leave, employee reimbursements, payroll',
                'badge' => 'Jasa / Konsultan',
                'icon' => 'ti-headset',
                'color' => '#3c2a21',
                'enabled_modules' => ['attendance', 'gps', 'leave', 'reimbursement', 'payroll', 'contracts', 'reports', 'settings', 'organization', 'documents'],
                'disabled_modules' => ['face_recognition', 'recruitment', 'loans', 'performance', 'overtime', 'movements', 'resignations', 'onboarding', 'offboarding', 'governance', 'announcements'],
                'settings' => [
                    'company_type' => 'Professional Services',
                    'attendance_require_gps' => '1',
                    'attendance_require_face' => '0',
                ],
                'must_work' => ['Operational expense claims (Reimbursement)', 'Claim approval flow', 'Payroll slip payout integration'],
            ],
            'E' => [
                'code' => 'E',
                'name' => 'Preset E: HR-Only / Remote Team (No Attendance)',
                'subtitle' => 'Core HR, contracts, performance, documents, payroll, attendance module completely OFF',
                'badge' => 'Remote / HR Core',
                'icon' => 'ti-user-off',
                'color' => '#3c2a21',
                'enabled_modules' => ['contracts', 'movements', 'resignations', 'payroll', 'performance', 'documents', 'reports', 'settings', 'organization', 'announcements'],
                'disabled_modules' => ['attendance', 'gps', 'face_recognition', 'overtime', 'reimbursement', 'loans', 'recruitment', 'onboarding', 'offboarding', 'governance'],
                'settings' => [
                    'company_type' => 'Tech Startup / Remote',
                    'attendance_enabled' => '0',
                    'attendance_require_gps' => '0',
                    'attendance_require_face' => '0',
                ],
                'must_work' => ['App boots and operates with zero attendance dependencies', 'Contracts & Document storage', 'KPI & Talent Reviews', 'Independent Payroll'],
            ],
        ];
    }

    /**
     * Apply preset configuration to database
     */
    public function applyPreset(string $presetKey, ?int $userId = null): array
    {
        $presetKey = strtoupper(trim($presetKey));
        $presets = $this->getPresets();

        if (!isset($presets[$presetKey])) {
            throw new \InvalidArgumentException("Preset '{$presetKey}' tidak ditemukan.");
        }

        $preset = $presets[$presetKey];

        DB::transaction(function () use ($preset, $presetKey, $userId) {
            // Entitlement Service ensures preset NEVER grants unpurchased entitlements
            $entitlementService = app(\App\Services\ModuleEntitlementService::class);
            $currentEntitled = $entitlementService->getEntitledModules();

            // 1. Enable specified modules (ONLY IF ENTITLED)
            foreach ($preset['enabled_modules'] as $mod) {
                $canonical = ModuleFeature::canonicalCode($mod);
                $isEntitled = in_array($canonical, $currentEntitled, true);
                $enableThis = $isEntitled; // Never enable unentitled module

                ModuleFeature::updateOrCreate(
                    ['module_code' => $canonical],
                    [
                        'module_name' => ucfirst(str_replace('_', ' ', $canonical)),
                        'is_entitled' => $isEntitled,
                        'is_enabled' => $enableThis,
                    ]
                );
                $aliases = ModuleFeature::$aliasMap[$canonical] ?? [];
                if (!empty($aliases)) {
                    ModuleFeature::whereIn('module_code', $aliases)->update([
                        'is_entitled' => $isEntitled,
                        'is_enabled' => $enableThis,
                    ]);
                }
            }

            // 2. Disable specified modules
            foreach ($preset['disabled_modules'] as $mod) {
                $canonical = ModuleFeature::canonicalCode($mod);
                $isEntitled = in_array($canonical, $currentEntitled, true);

                ModuleFeature::updateOrCreate(
                    ['module_code' => $canonical],
                    [
                        'module_name' => ucfirst(str_replace('_', ' ', $canonical)),
                        'is_entitled' => $isEntitled,
                        'is_enabled' => false,
                    ]
                );
                $aliases = ModuleFeature::$aliasMap[$canonical] ?? [];
                if (!empty($aliases)) {
                    ModuleFeature::whereIn('module_code', $aliases)->update([
                        'is_entitled' => $isEntitled,
                        'is_enabled' => false,
                    ]);
                }
            }

            // 3. Update CompanySetting business_type
            try {
                $company = CompanySetting::getSetting();
                if ($company && isset($preset['settings']['company_type'])) {
                    $company->update(['business_type' => $preset['settings']['company_type']]);
                }
            } catch (\Throwable $e) {}

            // 4. Update Pengaturanumum (General Setting)
            try {
                $general = \App\Models\Pengaturanumum::getSetting();
                if ($general) {
                    $genUpdate = [];
                    if (isset($preset['settings']['attendance_require_face'])) {
                        $genUpdate['face_recognition'] = (int) $preset['settings']['attendance_require_face'];
                    }
                    if (!empty($genUpdate)) {
                        $general->update($genUpdate);
                    }
                }
            } catch (\Throwable $e) {}

            // 5. Update AttendancePolicy
            try {
                $policy = \App\Models\AttendancePolicy::getActivePolicy();
                if ($policy) {
                    $polUpdate = [];
                    if (isset($preset['settings']['attendance_require_gps'])) {
                        $polUpdate['require_gps'] = (bool) $preset['settings']['attendance_require_gps'];
                    }
                    if (isset($preset['settings']['attendance_require_face'])) {
                        $polUpdate['require_face_recognition'] = (bool) $preset['settings']['attendance_require_face'];
                    }
                    if (isset($preset['settings']['attendance_radius_meters'])) {
                        $polUpdate['max_out_of_radius_meters'] = (int) $preset['settings']['attendance_radius_meters'];
                    }
                    if (!empty($polUpdate)) {
                        $policy->update($polUpdate);
                    }
                }
            } catch (\Throwable $e) {}

            // Record in audit log
            $this->auditService->log(
                'APPLY_PRESET',
                'settings',
                $presetKey,
                [
                    'preset_name' => $preset['name'],
                    'enabled' => $preset['enabled_modules'],
                    'disabled' => $preset['disabled_modules'],
                ],
                $userId
            );

            // Save active preset marker
            Cache::forever('active_client_preset_code', $presetKey);

            // Flush relevant caches immediately
            Cache::forget('module_features_all');
            Cache::forget('active_attendance_policy');
            Cache::forget('company_settings_first');
            Cache::forget('pengaturan_umum_first');
            Cache::forget('global_general_setting');
            ThemeResolver::forgetCache();
        });

        return [
            'success' => true,
            'preset' => $preset,
            'message' => "Konfigurasi '{$preset['name']}' berhasil diterapkan secara universal.",
        ];
    }

    /**
     * Get currently active preset code (A - E)
     */
    public function getActivePresetCode(): string
    {
        $cached = Cache::get('active_client_preset_code');
        if ($cached && in_array($cached, ['A', 'B', 'C', 'D', 'E'])) {
            return $cached;
        }

        // Auto-detect based on active module features
        $isAttendance = is_module_enabled('attendance', true);
        if (!$isAttendance) {
            return 'E';
        }

        $isFace = is_module_enabled('face_recognition', false);
        $isPayroll = is_module_enabled('payroll', false);
        $isOvertime = is_module_enabled('overtime', false);
        $isReimbursement = is_module_enabled('reimbursement', false);

        if ($isFace && !$isPayroll) {
            return 'A';
        }
        if (!$isFace && $isOvertime) {
            return 'C';
        }
        if (!$isFace && $isReimbursement) {
            return 'D';
        }
        if (!$isFace && $isPayroll) {
            return 'B';
        }

        return 'A';
    }

    /**
     * Validate that preset modules and requirements are clean
     */
    public function validatePreset(string $presetKey): array
    {
        $presets = $this->getPresets();
        if (!isset($presets[$presetKey])) {
            return ['valid' => false, 'error' => "Preset {$presetKey} not recognized."];
        }

        $preset = $presets[$presetKey];
        $allFeatures = ModuleFeature::pluck('is_enabled', 'module_code')->toArray();

        $missingOrMismatch = [];
        foreach ($preset['enabled_modules'] as $mod) {
            if (isset($allFeatures[$mod]) && !$allFeatures[$mod]) {
                $missingOrMismatch[] = "Module '{$mod}' is currently disabled but should be enabled in Preset {$presetKey}.";
            }
        }

        return [
            'valid' => empty($missingOrMismatch),
            'mismatches' => $missingOrMismatch,
            'preset' => $preset,
        ];
    }
}
