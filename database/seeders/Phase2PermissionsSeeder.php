<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Phase2PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Division permissions
        $divGroup = Permission_group::firstOrCreate(['name' => 'Divisi & Tim']);
        $divPerms = [
            'divisi.index',
            'divisi.create',
            'divisi.edit',
            'divisi.delete',
        ];

        foreach ($divPerms as $perm) {
            Permission::firstOrCreate(['name' => $perm], ['id_permission_group' => $divGroup->id]);
        }

        // 2. Sensitive employee data permission
        $empGroup = Permission_group::firstOrCreate(['name' => 'Karyawan']);
        Permission::firstOrCreate(['name' => 'employee.sensitive_data'], ['id_permission_group' => $empGroup->id]);

        // Assign to super admin and master admin
        $roles = Role::whereIn('name', ['super admin', 'master admin', 'admin'])->get();
        foreach ($roles as $role) {
            foreach ($divPerms as $perm) {
                $p = Permission::where('name', $perm)->first();
                if ($p && !$role->hasPermissionTo($p)) {
                    $role->givePermissionTo($p);
                }
            }
            // Give sensitive data access to super admin and master admin only
            if (in_array($role->name, ['super admin', 'master admin'])) {
                $pSensitive = Permission::where('name', 'employee.sensitive_data')->first();
                if ($pSensitive && !$role->hasPermissionTo($pSensitive)) {
                    $role->givePermissionTo($pSensitive);
                }
            }
        }
    }
}
