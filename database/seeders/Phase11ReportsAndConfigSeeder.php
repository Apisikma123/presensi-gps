<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Phase11ReportsAndConfigSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'Laporan & Analitik' => [
                'reports.index',
                'reports.export',
            ],
            'Bagan Organisasi' => [
                'org_chart.index',
            ],
            'Import & Kelola Massal' => [
                'karyawan.import',
            ],
            'Pusat Bantuan & Panduan' => [
                'help.index',
            ],
            'Pusat Pengaturan' => [
                'settings.index',
            ],
        ];

        foreach ($permissions as $groupName => $perms) {
            $group = Permission_group::firstOrCreate(['name' => $groupName]);

            foreach ($perms as $permName) {
                Permission::firstOrCreate(
                    ['name' => $permName, 'guard_name' => 'web'],
                    ['id_permission_group' => $group->id]
                );
            }
        }

        // Assign to Super Admin and Admin
        $superAdmin = Role::where('name', 'super admin')->first();
        if ($superAdmin) {
            $allPerms = Permission::all();
            $superAdmin->syncPermissions($allPerms);
        }

        $adminRole = Role::where('name', 'administrator')->orWhere('name', 'admin')->first();
        if ($adminRole) {
            $phase11Perms = Permission::whereIn('name', [
                'reports.index', 'reports.export', 'org_chart.index', 'karyawan.import', 'help.index', 'settings.index'
            ])->get();
            $adminRole->givePermissionTo($phase11Perms);
        }
    }
}
