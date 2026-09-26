<?php

namespace Database\Seeders;

use App\Models\ModuleFeature;
use App\Models\Permission_group;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Phase3ModuleAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure Phase 3 modules exist in module_features
        $modules = [
            [
                'module_code' => 'contract',
                'module_name' => 'Manajemen Kontrak Kerja',
                'category' => 'Core HR / Kepegawaian',
                'is_enabled' => true,
                'description' => 'Pengelolaan kontrak kerja (PKWT, PKWTT, Probation), masa berlaku, dokumen digital, dan notifikasi masa berakhir.',
                'config' => ['reminder_days_default' => 30, 'auto_expire' => true],
            ],
            [
                'module_code' => 'movement',
                'module_name' => 'Mutasi & Riwayat Karir',
                'category' => 'Core HR / Kepegawaian',
                'is_enabled' => true,
                'description' => 'Pencatatan mutasi cabang, rotasi departemen, promosi, demosi, perubahan atasan langsung, dan Surat Keputusan (SK).',
                'config' => ['require_sk_number' => false, 'auto_sync_employee' => true],
            ],
            [
                'module_code' => 'resignation',
                'module_name' => 'Resignasi & Offboarding',
                'category' => 'Core HR / Kepegawaian',
                'is_enabled' => true,
                'description' => 'Manajemen pemutusan hubungan kerja, pengunduran diri karyawan, serah terima tugas (clearance), dan riwayat nonaktif.',
                'config' => ['require_clearance' => true],
            ],
        ];

        foreach ($modules as $mod) {
            ModuleFeature::updateOrCreate(
                ['module_code' => $mod['module_code']],
                $mod
            );
        }

        // 2. Permission Groups & Permissions
        $permissionDefinitions = [
            'Manajemen Kontrak' => [
                'kontrak.index',
                'kontrak.create',
                'kontrak.edit',
                'kontrak.delete',
            ],
            'Mutasi & Riwayat Karir' => [
                'movement.index',
                'movement.create',
                'movement.show',
                'movement.delete',
            ],
            'Resignasi & Offboarding' => [
                'resignation.index',
                'resignation.create',
                'resignation.edit',
                'resignation.delete',
            ],
        ];

        $roles = Role::whereIn('name', ['super admin', 'master admin'])->get();

        foreach ($permissionDefinitions as $groupName => $perms) {
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
