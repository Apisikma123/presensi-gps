<?php

namespace Database\Seeders;

use App\Models\Permission_group;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Cabangpermissionseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissiongroup = Permission_group::firstOrCreate(['name' => 'Cabang']);

        $p1 = Permission::firstOrCreate(['name' => 'cabang.index'], ['id_permission_group' => $permissiongroup->id]);
        $p2 = Permission::firstOrCreate(['name' => 'cabang.create'], ['id_permission_group' => $permissiongroup->id]);
        $p3 = Permission::firstOrCreate(['name' => 'cabang.edit'], ['id_permission_group' => $permissiongroup->id]);
        $p4 = Permission::firstOrCreate(['name' => 'cabang.delete'], ['id_permission_group' => $permissiongroup->id]);

        $roles = Role::whereIn('name', ['super admin', 'master admin', 'admin'])->get();
        foreach ($roles as $role) {
            $role->givePermissionTo([$p1, $p2, $p3, $p4]);
        }
    }
}
