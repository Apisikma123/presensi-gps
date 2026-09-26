<?php

namespace Database\Seeders;

use App\Models\ModuleFeature;
use App\Models\OvertimePolicy;
use App\Models\Permission_group;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Phase5OvertimeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure overtime policy exists
        OvertimePolicy::firstOrCreate(
            ['code' => 'DEPNAKER_STANDARD'],
            [
                'name' => 'Standar Depnaker (PP 35/2021 & Kepmenakertrans 102/2004)',
                'version' => '1.0',
                'effective_from' => '2021-02-02',
                'source_reference' => 'PP No. 35 Tahun 2021 Pasal 31 & Kepmenakertrans No. KEP.102/MEN/VI/2004',
                'max_hours_per_day' => 4.0,
                'max_hours_per_week' => 18.0,
                'requires_meal_allowance_after_4h' => true,
                'is_active' => true,
                'rules' => OvertimePolicy::defaultDepnakerRules(),
            ]
        );

        // 2. Ensure module feature is enabled
        ModuleFeature::updateOrCreate(
            ['module_code' => 'overtime'],
            [
                'module_name' => 'Lembur & SPK Overtime',
                'category' => 'ATTENDANCE',
                'description' => 'Surat Perintah Kerja (SPK) Lembur, kalkulasi formula Depnaker PP 35/2021 & approval',
                'is_enabled' => true,
            ]
        );

        // 3. Seed Phase 5 permissions with permission groups
        $permissionGroups = [
            'Lembur & SPK' => [
                'overtime.index',
                'overtime.create',
                'overtime.approve',
                'overtime.edit',
                'overtime.delete',
            ],
            'Kebijakan Lembur' => [
                'overtime_policy.index',
                'overtime_policy.edit',
            ],
        ];

        $roles = Role::whereIn('name', ['super admin', 'master admin'])->get();

        foreach ($permissionGroups as $groupName => $perms) {
            $group = Permission_group::firstOrCreate(['name' => $groupName]);

            foreach ($perms as $permName) {
                $permission = Permission::firstOrCreate(
                    ['name' => $permName],
                    ['id_permission_group' => $group->id]
                );

                foreach ($roles as $role) {
                    if (!$role->hasPermissionTo($permission)) {
                        $role->givePermissionTo($permission);
                    }
                }
            }
        }
    }
}
