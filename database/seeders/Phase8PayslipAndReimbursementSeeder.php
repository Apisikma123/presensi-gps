<?php

namespace Database\Seeders;

use App\Models\ModuleFeature;
use App\Models\Permission_group;
use App\Models\ReimbursementType;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Phase8PayslipAndReimbursementSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Standard Reimbursement Types
        $types = [
            [
                'code' => 'MEDIS',
                'name' => 'Klaim Medis & Rawat Jalan',
                'max_limit' => 2000000.00,
                'is_active' => true,
            ],
            [
                'code' => 'TRANSPORT',
                'name' => 'Transportasi & Bensin Dinas',
                'max_limit' => null,
                'is_active' => true,
            ],
            [
                'code' => 'KONSUMSI',
                'name' => 'Uang Konsumsi Lembur / Meeting',
                'max_limit' => null,
                'is_active' => true,
            ],
            [
                'code' => 'OPERASIONAL',
                'name' => 'Biaya Operasional Lapangan',
                'max_limit' => null,
                'is_active' => true,
            ],
            [
                'code' => 'PERJALANAN_DINAS',
                'name' => 'Perjalanan Dinas & Akomodasi',
                'max_limit' => null,
                'is_active' => true,
            ],
        ];

        foreach ($types as $t) {
            ReimbursementType::updateOrCreate(['code' => $t['code']], $t);
        }

        // 2. Feature Flags
        ModuleFeature::updateOrCreate(
            ['module_code' => 'reimbursement'],
            [
                'module_name' => 'Reimbursement & Klaim',
                'category' => 'FINANCE',
                'description' => 'Pengajuan dan persetujuan klaim biaya karyawan beserta lampiran kuitansi/nota',
                'is_enabled' => true,
            ]
        );

        ModuleFeature::updateOrCreate(
            ['module_code' => 'loan'],
            [
                'module_name' => 'Pinjaman & Kasbon Karyawan',
                'category' => 'FINANCE',
                'description' => 'Pengajuan dana kasbon, persetujuan, jadwal angsuran tenor, dan pemotongan gaji otomatis',
                'is_enabled' => true,
            ]
        );

        // 3. Permissions & Permission Groups
        $permissionGroups = [
            'Slip Gaji' => [
                'payslip.index',
                'payslip.show',
                'payslip.print',
            ],
            'Reimbursement' => [
                'reimbursement.index',
                'reimbursement.create',
                'reimbursement.approve',
                'reimbursement.delete',
            ],
            'Pinjaman & Kasbon' => [
                'loan.index',
                'loan.create',
                'loan.approve',
                'loan.delete',
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
