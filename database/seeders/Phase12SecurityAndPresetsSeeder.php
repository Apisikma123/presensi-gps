<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use App\Services\AuditLogService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Phase12SecurityAndPresetsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'Audit Trail & Keamanan' => [
                'audit_logs.index',
                'audit_logs.export',
            ],
            'Matriks Preset Klien' => [
                'presets.index',
                'presets.apply',
            ],
        ];

        $allPermissionNames = [];

        foreach ($permissions as $groupName => $perms) {
            $group = Permission_group::firstOrCreate(['name' => $groupName]);

            foreach ($perms as $permName) {
                Permission::firstOrCreate(
                    ['name' => $permName, 'guard_name' => 'web'],
                    ['id_permission_group' => $group->id]
                );
                $allPermissionNames[] = $permName;
            }
        }

        // Assign to super admin and admin
        $superAdmin = Role::where('name', 'super admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($allPermissionNames);
        }

        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $admin->givePermissionTo($allPermissionNames);
        }

        // Record initial security verification audit log
        try {
            $auditService = app(AuditLogService::class);
            $auditService->log(
                'SYSTEM_INIT',
                'security',
                'PHASE_12',
                [
                    'description' => 'Presence Universal HR System: Phase 12 Security Audit & Preset Matrix Initialized.',
                    'status' => 'VERIFIED_ACTIVE',
                ],
                1
            );
        } catch (\Throwable $e) {
            // Silently pass
        }
    }
}
