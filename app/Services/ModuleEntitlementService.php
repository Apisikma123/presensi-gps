<?php

namespace App\Services;

use App\Models\ModuleFeature;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class ModuleEntitlementService
{
    const CONTROL_CORE_LOCKED = 'core_locked';
    const CONTROL_VENDOR_LOCKED = 'vendor_locked';
    const CONTROL_CLIENT_TOGGLEABLE = 'client_toggleable';

    /**
     * Core Architectural Modules that cannot be disabled
     */
    public static array $coreModules = [
        'employee',
        'organization',
        'settings',
        'security',
        'reports',
        'auth',
    ];

    /**
     * Direct dependency graph: key depends on value (module => required_parent)
     */
    public static array $dependencies = [
        'face_recognition' => 'attendance',
        'gps'              => 'attendance',
        'overtime'         => 'attendance',
        'pph21'            => 'payroll',
        'bpjs'             => 'payroll',
        'thr'              => 'payroll',
        'compliance'       => 'payroll',
        'payroll'          => 'employee',
        'leave'            => 'employee',
        'reimbursement'    => 'employee',
        'loans'            => 'employee',
        'recruitment'      => 'employee',
        'onboarding'       => 'employee',
        'offboarding'      => 'employee',
        'performance'      => 'employee',
        'training'         => 'employee',
        'contracts'        => 'employee',
        'movements'        => 'employee',
        'documents'        => 'employee',
        'asset_assignment' => 'employee',
        'announcements'    => 'employee',
        'discipline'       => 'employee',
        'governance'       => 'employee',
    ];

    /**
     * Centralized Package Definitions
     */
    public static function getDefinedPackages(): array
    {
        return [
            'FNB_SMALL' => [
                'code' => 'FNB_SMALL',
                'name' => 'F&B Small',
                'category' => 'Food & Beverage',
                'description' => 'Designed for cafes, small restaurants, and kiosks (1-2 branches). Core attendance, GPS, shift schedules, leave, announcements.',
                'entitled_modules' => [
                    'employee', 'organization', 'settings', 'security', 'reports',
                    'attendance', 'gps', 'leave', 'announcements', 'face_recognition',
                ],
                'default_enabled' => [
                    'employee', 'organization', 'settings', 'security', 'reports',
                    'attendance', 'gps', 'leave', 'announcements',
                ],
                'client_toggleable' => [
                    'face_recognition', 'announcements',
                ],
                'locked_modules' => [
                    'attendance', 'gps', 'leave',
                ],
            ],
            'FNB_STANDARD' => [
                'code' => 'FNB_STANDARD',
                'name' => 'F&B Standard',
                'category' => 'Food & Beverage',
                'description' => 'F&B chain package with overtime tracking, expense reimbursement, and employment contract management.',
                'entitled_modules' => [
                    'employee', 'organization', 'settings', 'security', 'reports',
                    'attendance', 'gps', 'leave', 'announcements', 'face_recognition',
                    'overtime', 'reimbursement', 'contracts',
                ],
                'default_enabled' => [
                    'employee', 'organization', 'settings', 'security', 'reports',
                    'attendance', 'gps', 'leave', 'announcements', 'overtime',
                ],
                'client_toggleable' => [
                    'face_recognition', 'announcements', 'reimbursement', 'contracts',
                ],
                'locked_modules' => [
                    'attendance', 'gps', 'leave', 'overtime',
                ],
            ],
            'FNB_PRO' => [
                'code' => 'FNB_PRO',
                'name' => 'F&B Pro',
                'category' => 'Food & Beverage',
                'description' => 'Complete enterprise F&B operations with statutory Indonesian payroll, PPh 21 TER, BPJS TK/Kes, THR, and digital documents.',
                'entitled_modules' => [
                    'employee', 'organization', 'settings', 'security', 'reports',
                    'attendance', 'gps', 'leave', 'announcements', 'face_recognition',
                    'overtime', 'reimbursement', 'contracts',
                    'payroll', 'pph21', 'bpjs', 'thr', 'compliance', 'documents', 'loans',
                ],
                'default_enabled' => [
                    'employee', 'organization', 'settings', 'security', 'reports',
                    'attendance', 'gps', 'leave', 'announcements', 'overtime',
                    'payroll', 'pph21', 'bpjs', 'thr', 'compliance', 'documents',
                ],
                'client_toggleable' => [
                    'face_recognition', 'announcements', 'reimbursement', 'loans', 'documents',
                ],
                'locked_modules' => [
                    'attendance', 'gps', 'leave', 'overtime', 'payroll', 'pph21', 'bpjs', 'thr', 'contracts',
                ],
            ],
            'RETAIL_SMALL' => [
                'code' => 'RETAIL_SMALL',
                'name' => 'Retail Small',
                'category' => 'Retail',
                'description' => 'Designed for boutique retail stores with multi-shift scheduling and Depnaker overtime calculation.',
                'entitled_modules' => [
                    'employee', 'organization', 'settings', 'security', 'reports',
                    'attendance', 'gps', 'leave', 'overtime', 'announcements',
                ],
                'default_enabled' => [
                    'employee', 'organization', 'settings', 'security', 'reports',
                    'attendance', 'gps', 'leave', 'overtime', 'announcements',
                ],
                'client_toggleable' => [
                    'announcements',
                ],
                'locked_modules' => [
                    'attendance', 'gps', 'leave', 'overtime',
                ],
            ],
            'OFFICE_STANDARD' => [
                'code' => 'OFFICE_STANDARD',
                'name' => 'Corporate Office Standard',
                'category' => 'Corporate',
                'description' => 'Corporate office package: regular Mon-Fri attendance, full Indonesian statutory payroll, contracts, and documents.',
                'entitled_modules' => [
                    'employee', 'organization', 'settings', 'security', 'reports',
                    'attendance', 'gps', 'leave', 'payroll', 'pph21', 'bpjs', 'thr',
                    'compliance', 'contracts', 'documents', 'announcements',
                ],
                'default_enabled' => [
                    'employee', 'organization', 'settings', 'security', 'reports',
                    'attendance', 'gps', 'leave', 'payroll', 'pph21', 'bpjs', 'thr',
                    'compliance', 'contracts', 'documents',
                ],
                'client_toggleable' => [
                    'documents', 'announcements',
                ],
                'locked_modules' => [
                    'attendance', 'gps', 'leave', 'payroll', 'contracts',
                ],
            ],
            'FULL_HR' => [
                'code' => 'FULL_HR',
                'name' => 'Full Universal HR Enterprise',
                'category' => 'Enterprise',
                'description' => 'Full suite covering all 12 Phases: Attendance, GPS, Face Biometrics, Overtime, Payroll, Statutory Compliance, Loans, Reimbursement, Recruitment, Talent Governance, Analytics.',
                'entitled_modules' => [
                    'employee', 'organization', 'settings', 'security', 'reports',
                    'attendance', 'gps', 'face_recognition', 'leave', 'overtime',
                    'payroll', 'pph21', 'bpjs', 'thr', 'compliance', 'reimbursement',
                    'loans', 'recruitment', 'onboarding', 'offboarding', 'performance',
                    'training', 'contracts', 'movements', 'documents', 'asset_assignment',
                    'announcements', 'discipline', 'governance',
                ],
                'default_enabled' => [
                    'employee', 'organization', 'settings', 'security', 'reports',
                    'attendance', 'gps', 'face_recognition', 'leave', 'overtime',
                    'payroll', 'pph21', 'bpjs', 'thr', 'compliance', 'reimbursement',
                    'loans', 'recruitment', 'onboarding', 'offboarding', 'performance',
                    'training', 'contracts', 'movements', 'documents', 'asset_assignment',
                    'announcements', 'discipline', 'governance',
                ],
                'client_toggleable' => [
                    'face_recognition', 'reimbursement', 'loans', 'recruitment',
                    'onboarding', 'performance', 'training', 'documents',
                    'asset_assignment', 'announcements', 'discipline', 'governance',
                ],
                'locked_modules' => [
                    'attendance', 'gps', 'leave', 'overtime', 'payroll', 'contracts',
                ],
            ],
            'CUSTOM' => [
                'code' => 'CUSTOM',
                'name' => 'Custom Entitlement Package',
                'category' => 'Custom',
                'description' => 'Custom bespoke combination configured during deployment onboarding.',
                'entitled_modules' => [
                    'employee', 'organization', 'settings', 'security', 'reports',
                ],
                'default_enabled' => [
                    'employee', 'organization', 'settings', 'security', 'reports',
                ],
                'client_toggleable' => [],
                'locked_modules' => [],
            ],
        ];
    }

    protected ?string $memoryPackageCode = null;
    protected ?array $memoryAddons = null;
    protected ?bool $memoryLocked = null;
    protected ?array $memoryEntitled = null;

    /**
     * Flush in-memory request-level cache
     */
    public function flushMemoryCache(): void
    {
        $this->memoryPackageCode = null;
        $this->memoryAddons = null;
        $this->memoryLocked = null;
        $this->memoryEntitled = null;
    }

    /**
     * Get active package code from deployment_settings (cached)
     */
    public function getCurrentPackageCode(): string
    {
        if ($this->memoryPackageCode !== null) {
            return $this->memoryPackageCode;
        }

        return $this->memoryPackageCode = Cache::remember('presence_active_package_code', 3600, function () {
            if (DB::getSchemaBuilder()->hasTable('deployment_settings')) {
                $pkg = DB::table('deployment_settings')->where('setting_key', 'deployment_package_code')->value('setting_value');
                if (!empty($pkg)) {
                    return $pkg;
                }
            }
            return 'FULL_HR';
        });
    }

    /**
     * Get active package definition
     */
    public function getCurrentPackage(): array
    {
        $code = $this->getCurrentPackageCode();
        $packages = self::getDefinedPackages();
        return $packages[$code] ?? $packages['FULL_HR'];
    }

    /**
     * Get active add-ons (cached)
     */
    public function getActiveAddons(): array
    {
        if ($this->memoryAddons !== null) {
            return $this->memoryAddons;
        }

        return $this->memoryAddons = Cache::remember('presence_active_addons', 3600, function () {
            if (DB::getSchemaBuilder()->hasTable('deployment_settings')) {
                $raw = DB::table('deployment_settings')->where('setting_key', 'deployment_addons')->value('setting_value');
                if (!empty($raw)) {
                    $decoded = json_decode($raw, true);
                    if (is_array($decoded)) {
                        return array_map([ModuleFeature::class, 'canonicalCode'], $decoded);
                    }
                }
            }
            return [];
        });
    }

    /**
     * Check if deployment profile is locked
     */
    public function isDeploymentLocked(): bool
    {
        if ($this->memoryLocked !== null) {
            return $this->memoryLocked;
        }

        return $this->memoryLocked = Cache::remember('presence_deployment_locked', 3600, function () {
            if (DB::getSchemaBuilder()->hasTable('deployment_settings')) {
                $val = DB::table('deployment_settings')->where('setting_key', 'deployment_locked')->value('setting_value');
                if ($val !== null) {
                    return (bool) $val;
                }
            }
            return true; // Default locked for security
        });
    }

    /**
     * Set deployment lock state
     */
    public function setDeploymentLocked(bool $locked): void
    {
        $this->flushMemoryCache();
        $this->setSetting('deployment_locked', $locked ? '1' : '0');
        Cache::forget('presence_deployment_locked');
    }

    /**
     * Check if package maintenance mode is active
     */
    public function isMaintenanceMode(): bool
    {
        if (DB::getSchemaBuilder()->hasTable('deployment_settings')) {
            $val = DB::table('deployment_settings')->where('setting_key', 'package_maintenance_mode')->value('setting_value');
            return (bool) $val;
        }
        return false;
    }

    /**
     * Set package maintenance mode
     */
    public function setMaintenanceMode(bool $active): void
    {
        $this->setSetting('package_maintenance_mode', $active ? '1' : '0');
        Cache::forget('presence_package_maintenance_mode');
    }

    /**
     * Get list of all currently entitled canonical module codes (Base Package + Add-ons)
     */
    public function getEntitledModules(): array
    {
        if ($this->memoryEntitled !== null) {
            return $this->memoryEntitled;
        }

        $pkg = $this->getCurrentPackage();
        $base = array_map([ModuleFeature::class, 'canonicalCode'], $pkg['entitled_modules'] ?? []);
        $addons = $this->getActiveAddons();
        $core = self::$coreModules;

        return $this->memoryEntitled = array_values(array_unique(array_merge($core, $base, $addons)));
    }

    /**
     * Check if a specific module is entitled for this deployment
     */
    public function isModuleEntitled(string $moduleCode): bool
    {
        $canonical = ModuleFeature::canonicalCode($moduleCode);
        $entitled = $this->getEntitledModules();
        return in_array($canonical, $entitled, true);
    }

    /**
     * Check if a module is client toggleable (can be turned ON/OFF by Super Admin)
     */
    public function isModuleClientToggleable(string $moduleCode): bool
    {
        $canonical = ModuleFeature::canonicalCode($moduleCode);

        // Core modules are NEVER toggleable
        if (in_array($canonical, self::$coreModules, true)) {
            return false;
        }

        // Must be entitled
        if (!$this->isModuleEntitled($canonical)) {
            return false;
        }

        $pkg = $this->getCurrentPackage();
        $toggleable = array_map([ModuleFeature::class, 'canonicalCode'], $pkg['client_toggleable'] ?? []);
        $addons = $this->getActiveAddons();

        // Any purchased add-on is toggleable by client by default
        return in_array($canonical, $toggleable, true) || in_array($canonical, $addons, true);
    }

    /**
     * Get module control level
     */
    public function getModuleControlLevel(string $moduleCode): string
    {
        $canonical = ModuleFeature::canonicalCode($moduleCode);

        if (in_array($canonical, self::$coreModules, true)) {
            return self::CONTROL_CORE_LOCKED;
        }

        if (!$this->isModuleEntitled($canonical)) {
            return self::CONTROL_VENDOR_LOCKED;
        }

        if ($this->isModuleClientToggleable($canonical)) {
            return self::CONTROL_CLIENT_TOGGLEABLE;
        }

        return self::CONTROL_VENDOR_LOCKED;
    }

    /**
     * Validate dependencies before enabling/disabling
     */
    public function checkDependencies(string $moduleCode, bool $targetState): array
    {
        $canonical = ModuleFeature::canonicalCode($moduleCode);

        // 1. If ENABLING: check if parent dependency is enabled
        if ($targetState === true) {
            $parent = self::$dependencies[$canonical] ?? null;
            if ($parent && !in_array($parent, self::$coreModules, true)) {
                $parentCanonical = ModuleFeature::canonicalCode($parent);
                // Check if parent is entitled
                if (!$this->isModuleEntitled($parentCanonical)) {
                    return [
                        'allowed' => false,
                        'reason' => "Modul '{$canonical}' membutuhkan modul '{$parentCanonical}' yang tidak termasuk dalam paket perusahaan.",
                        'required_parent' => $parentCanonical,
                    ];
                }

                // Check if parent is currently enabled
                if (!ModuleFeature::isEnabled($parentCanonical)) {
                    return [
                        'allowed' => false,
                        'reason' => "Modul '{$canonical}' membutuhkan modul '{$parentCanonical}' aktif terlebih dahulu.",
                        'required_parent' => $parentCanonical,
                    ];
                }
            }
        }

        // 2. If DISABLING: check if any active child module depends on this
        if ($targetState === false) {
            $dependentChildren = [];
            foreach (self::$dependencies as $child => $parent) {
                if (ModuleFeature::canonicalCode($parent) === $canonical) {
                    $childCanonical = ModuleFeature::canonicalCode($child);
                    if ($this->isModuleEntitled($childCanonical) && ModuleFeature::isEnabled($childCanonical)) {
                        $dependentChildren[] = $childCanonical;
                    }
                }
            }

            if (!empty($dependentChildren)) {
                $list = implode(', ', array_unique($dependentChildren));
                return [
                    'allowed' => false,
                    'reason' => "Modul '{$canonical}' tidak dapat dinonaktifkan karena sedang dibutuhkan oleh modul aktif: {$list}.",
                    'dependents' => $dependentChildren,
                ];
            }
        }

        return ['allowed' => true, 'reason' => ''];
    }

    /**
     * Check if user/actor can toggle this module
     */
    public function canToggleModule(string $moduleCode, bool $desiredState, ?User $actor = null): array
    {
        $canonical = ModuleFeature::canonicalCode($moduleCode);

        // 1. Core locked check
        if (in_array($canonical, self::$coreModules, true)) {
            return [
                'allowed' => false,
                'reason' => "Modul '{$canonical}' adalah modul inti arsitektur sistem dan tidak dapat dinonaktifkan.",
            ];
        }

        // 2. Entitlement check
        if (!$this->isModuleEntitled($canonical)) {
            return [
                'allowed' => false,
                'reason' => "Modul '{$canonical}' tidak termasuk dalam paket langganan/lisensi yang dibeli.",
            ];
        }

        // 3. Client toggleable check
        if (!$this->isModuleClientToggleable($canonical)) {
            return [
                'allowed' => false,
                'reason' => "Modul '{$canonical}' dikunci oleh vendor/deployment dan hanya dapat diubah melalui prosedur perubahan paket.",
            ];
        }

        // 4. Role authorization: only Super Admin can toggle modules
        if ($actor && !$actor->hasRole('super admin')) {
            return [
                'allowed' => false,
                'reason' => "Hanya Super Admin yang memiliki wewenang untuk mengubah status modul.",
            ];
        }

        // 5. Dependency check
        $depCheck = $this->checkDependencies($canonical, $desiredState);
        if (!$depCheck['allowed']) {
            return $depCheck;
        }

        return ['allowed' => true, 'reason' => ''];
    }

    /**
     * Perform module toggle with audit logging & cache flush
     */
    public function toggleModule(string $moduleCode, ?User $actor = null, string $notes = ''): array
    {
        $canonical = ModuleFeature::canonicalCode($moduleCode);
        $current = ModuleFeature::isEnabled($canonical);
        $target = !$current;

        $check = $this->canToggleModule($canonical, $target, $actor);
        if (!$check['allowed']) {
            return [
                'success' => false,
                'message' => $check['reason'],
            ];
        }

        // Find primary record
        $feature = ModuleFeature::where('module_code', $canonical)->first();
        $oldState = $feature ? [
            'is_enabled' => $feature->is_enabled,
            'is_entitled' => $feature->is_entitled,
            'control_level' => $feature->control_level,
        ] : null;

        // Perform transactional update
        DB::transaction(function () use ($canonical, $target, $feature, $oldState, $actor, $notes) {
            $aliases = ModuleFeature::$aliasMap[$canonical] ?? [];
            $allCodes = array_unique(array_merge([$canonical], $aliases));

            ModuleFeature::whereIn('module_code', $allCodes)->update([
                'is_enabled' => $target,
                'activated_at' => $target ? now() : ($feature->activated_at ?? null),
                'deactivated_at' => !$target ? now() : null,
            ]);

            // Record to module_state_history
            if (DB::getSchemaBuilder()->hasTable('module_state_history')) {
                DB::table('module_state_history')->insert([
                    'module_code' => $canonical,
                    'old_state' => json_encode($oldState),
                    'new_state' => json_encode(['is_enabled' => $target, 'is_entitled' => true]),
                    'action' => 'CLIENT_TOGGLE',
                    'actor_id' => $actor ? $actor->id : Auth::id(),
                    'actor_name' => $actor ? ($actor->name ?? $actor->email) : 'System',
                    'ip_address' => request()->ip() ?? '127.0.0.1',
                    'notes' => $notes ?: ($target ? 'Diaktifkan oleh Super Admin' : 'Dinonaktifkan oleh Super Admin'),
                    'created_at' => now(),
                ]);
            }
        });

        // Invalidate caches
        ModuleFeature::flushCache();

        return [
            'success' => true,
            'module_code' => $canonical,
            'is_enabled' => $target,
            'message' => 'Status modul ' . ($feature->module_name ?? $canonical) . ' berhasil diubah menjadi ' . ($target ? 'Aktif' : 'Nonaktif'),
        ];
    }

    /**
     * Preview package change diff
     */
    public function previewPackageChange(string $newPackageCode, array $addons = []): array
    {
        $packages = self::getDefinedPackages();
        if (!isset($packages[$newPackageCode])) {
            throw new \InvalidArgumentException("Kode paket '{$newPackageCode}' tidak valid.");
        }

        $currentPkg = $this->getCurrentPackage();
        $targetPkg = $packages[$newPackageCode];

        $currentEntitled = $this->getEntitledModules();
        $targetBase = array_map([ModuleFeature::class, 'canonicalCode'], $targetPkg['entitled_modules'] ?? []);
        $targetAddons = array_map([ModuleFeature::class, 'canonicalCode'], $addons);
        $targetEntitled = array_values(array_unique(array_merge(self::$coreModules, $targetBase, $targetAddons)));

        $added = array_values(array_diff($targetEntitled, $currentEntitled));
        $removed = array_values(array_diff($currentEntitled, $targetEntitled));
        $retained = array_values(array_intersect($currentEntitled, $targetEntitled));

        return [
            'current_package' => $currentPkg['code'],
            'target_package' => $targetPkg['code'],
            'action' => count($removed) > 0 ? 'DOWNGRADE' : (count($added) > 0 ? 'UPGRADE' : 'SAME'),
            'added_entitlements' => $added,
            'removed_entitlements' => $removed,
            'retained_entitlements' => $retained,
            'addons' => $targetAddons,
            'data_impact' => 'NONE (Semua data transaksi historis tetap 100% aman dan tersimpan utuh di database)',
            'requires_migration' => false,
        ];
    }

    /**
     * Apply package change with strict transactional safety and non-destructive data handling
     */
    public function applyPackageChange(string $newPackageCode, array $addons = [], ?User $actor = null, string $notes = ''): array
    {
        $preview = $this->previewPackageChange($newPackageCode, $addons);
        $targetPkg = self::getDefinedPackages()[$newPackageCode];

        $targetBase = array_map([ModuleFeature::class, 'canonicalCode'], $targetPkg['entitled_modules'] ?? []);
        $targetAddons = array_map([ModuleFeature::class, 'canonicalCode'], $addons);
        $targetEntitled = array_values(array_unique(array_merge(self::$coreModules, $targetBase, $targetAddons)));

        $defaultEnabled = array_map([ModuleFeature::class, 'canonicalCode'], $targetPkg['default_enabled'] ?? []);
        $clientToggleable = array_map([ModuleFeature::class, 'canonicalCode'], $targetPkg['client_toggleable'] ?? []);
        $lockedModules = array_map([ModuleFeature::class, 'canonicalCode'], $targetPkg['locked_modules'] ?? []);

        DB::transaction(function () use ($newPackageCode, $addons, $preview, $actor, $notes, $targetEntitled, $defaultEnabled, $clientToggleable, $lockedModules) {
            // 1. Update company settings
            $this->setSetting('deployment_package_code', $newPackageCode);
            $this->setSetting('deployment_addons', json_encode(array_values($addons)));

            // 2. Synchronize module_features table
            $allModules = ModuleFeature::all();
            foreach ($allModules as $mod) {
                $canonical = ModuleFeature::canonicalCode($mod->module_code);
                $isEntitled = in_array($canonical, $targetEntitled, true);

                $controlLevel = in_array($canonical, self::$coreModules, true)
                    ? self::CONTROL_CORE_LOCKED
                    : (in_array($canonical, $clientToggleable, true) ? self::CONTROL_CLIENT_TOGGLEABLE : self::CONTROL_VENDOR_LOCKED);

                $isLocked = in_array($canonical, $lockedModules, true) || in_array($canonical, self::$coreModules, true);

                // If not entitled: MUST BE DISABLED! If entitled: enable according to package defaultEnabled
                $isEnabled = $isEntitled && in_array($canonical, $defaultEnabled, true);

                $mod->is_entitled = $isEntitled;
                $mod->control_level = $controlLevel;
                $mod->is_locked = $isLocked;
                $mod->is_enabled = $isEnabled;
                $mod->package_code = $newPackageCode;
                $mod->save();
            }

            // 3. Record package history
            if (DB::getSchemaBuilder()->hasTable('deployment_package_history')) {
                DB::table('deployment_package_history')->insert([
                    'old_package_code' => $preview['current_package'],
                    'new_package_code' => $newPackageCode,
                    'action' => $preview['action'],
                    'actor_id' => $actor ? $actor->id : Auth::id(),
                    'actor_name' => $actor ? ($actor->name ?? $actor->email) : 'Deployment Admin',
                    'notes' => $notes ?: "Perubahan paket ke {$newPackageCode}",
                    'diff_summary' => json_encode($preview),
                    'created_at' => now(),
                ]);
            }
        });

        // 4. Invalidate all caches
        $this->flushMemoryCache();
        Cache::forget('presence_active_package_code');
        Cache::forget('presence_active_addons');
        ModuleFeature::flushCache();

        return [
            'success' => true,
            'package_code' => $newPackageCode,
            'message' => "Paket berhasil diubah ke {$newPackageCode}.",
            'diff' => $preview,
        ];
    }

    /**
     * Add or remove a specific add-on module
     */
    public function manageAddon(string $addonCode, bool $enable, ?User $actor = null, string $notes = ''): array
    {
        $canonical = ModuleFeature::canonicalCode($addonCode);
        $addons = $this->getActiveAddons();

        if ($enable && !in_array($canonical, $addons, true)) {
            $addons[] = $canonical;
            $action = 'ADDON_ADDED';
        } elseif (!$enable && in_array($canonical, $addons, true)) {
            $addons = array_values(array_diff($addons, [$canonical]));
            $action = 'ADDON_REMOVED';
        } else {
            return [
                'success' => true,
                'message' => 'Status add-on sudah sesuai.',
            ];
        }

        // Reapply with existing package code and modified add-ons
        $pkgCode = $this->getCurrentPackageCode();
        $this->applyPackageChange($pkgCode, $addons, $actor, $notes ?: "Perubahan add-on {$canonical} ({$action})");

        return [
            'success' => true,
            'addon' => $canonical,
            'active_addons' => $addons,
            'message' => "Add-on '{$canonical}' berhasil " . ($enable ? 'ditambahkan' : 'dicabut') . '.',
        ];
    }

    /**
     * Synchronize module features table to match current package entitlements
     */
    public function syncModuleFeaturesTable(): void
    {
        $pkg = $this->getCurrentPackage();
        $entitled = $this->getEntitledModules();
        $clientToggleable = array_map([ModuleFeature::class, 'canonicalCode'], $pkg['client_toggleable'] ?? []);
        $lockedModules = array_map([ModuleFeature::class, 'canonicalCode'], $pkg['locked_modules'] ?? []);

        foreach (ModuleFeature::all() as $mod) {
            $canonical = ModuleFeature::canonicalCode($mod->module_code);
            $isEntitled = in_array($canonical, $entitled, true);
            $controlLevel = in_array($canonical, self::$coreModules, true)
                ? self::CONTROL_CORE_LOCKED
                : (in_array($canonical, $clientToggleable, true) ? self::CONTROL_CLIENT_TOGGLEABLE : self::CONTROL_VENDOR_LOCKED);
            $isLocked = in_array($canonical, $lockedModules, true) || in_array($canonical, self::$coreModules, true);

            $mod->is_entitled = $isEntitled;
            $mod->control_level = $controlLevel;
            $mod->is_locked = $isLocked;
            if (!$isEntitled) {
                $mod->is_enabled = false;
            }
            $mod->save();
        }

        ModuleFeature::flushCache();
    }

    /**
     * Helper to set setting in deployment_settings
     */
    protected function setSetting(string $key, string $value): void
    {
        if (DB::getSchemaBuilder()->hasTable('deployment_settings')) {
            DB::table('deployment_settings')->updateOrInsert(
                ['setting_key' => $key],
                ['setting_value' => $value, 'updated_at' => now()]
            );
        }
    }
}
