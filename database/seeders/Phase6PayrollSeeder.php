<?php

namespace Database\Seeders;

use App\Models\EmployeeSalaryAssignment;
use App\Models\Karyawan;
use App\Models\ModuleFeature;
use App\Models\Permission_group;
use App\Models\SalaryComponent;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Phase6PayrollSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Standard Indonesian Salary Components
        $components = [
            [
                'code' => 'BASIC_SALARY',
                'name' => 'Gaji Pokok',
                'type' => 'EARNING',
                'is_fixed' => true,
                'is_taxable' => true,
                'is_bpjs_basis' => true,
                'is_recurring' => true,
                'default_amount' => 5000000.00,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'TUNJANGAN_JABATAN',
                'name' => 'Tunjangan Jabatan',
                'type' => 'EARNING',
                'is_fixed' => true,
                'is_taxable' => true,
                'is_bpjs_basis' => true,
                'is_recurring' => true,
                'default_amount' => 1000000.00,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'TUNJANGAN_TRANSPORT',
                'name' => 'Tunjangan Transportasi',
                'type' => 'EARNING',
                'is_fixed' => false,
                'is_taxable' => true,
                'is_bpjs_basis' => false,
                'is_recurring' => true,
                'default_amount' => 500000.00,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'code' => 'TUNJANGAN_MAKAN',
                'name' => 'Tunjangan Uang Makan',
                'type' => 'EARNING',
                'is_fixed' => false,
                'is_taxable' => true,
                'is_bpjs_basis' => false,
                'is_recurring' => true,
                'default_amount' => 600000.00,
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'code' => 'POTONGAN_KEDISIPLINAN',
                'name' => 'Potongan Keterlambatan / Disiplin',
                'type' => 'DEDUCTION',
                'is_fixed' => false,
                'is_taxable' => false,
                'is_bpjs_basis' => false,
                'is_recurring' => false,
                'default_amount' => 0.00,
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'code' => 'POTONGAN_KASBON',
                'name' => 'Potongan Cicilan Kasbon / Pinjaman',
                'type' => 'DEDUCTION',
                'is_fixed' => false,
                'is_taxable' => false,
                'is_bpjs_basis' => false,
                'is_recurring' => false,
                'default_amount' => 0.00,
                'sort_order' => 11,
                'is_active' => true,
            ],
        ];

        foreach ($components as $c) {
            SalaryComponent::updateOrCreate(['code' => $c['code']], $c);
        }

        // 2. Ensure active employees have sample basic salary if none exists
        $basicComp = SalaryComponent::where('code', 'BASIC_SALARY')->first();
        $tunjJabatanComp = SalaryComponent::where('code', 'TUNJANGAN_JABATAN')->first();

        if ($basicComp) {
            $employees = Karyawan::where('status_aktif_karyawan', '1')->get();
            foreach ($employees as $emp) {
                EmployeeSalaryAssignment::firstOrCreate(
                    [
                        'nik' => $emp->nik,
                        'salary_component_id' => $basicComp->id,
                    ],
                    [
                        'amount' => 5000000.00,
                        'effective_date' => '2026-01-01',
                        'is_active' => true,
                    ]
                );

                if ($tunjJabatanComp) {
                    EmployeeSalaryAssignment::firstOrCreate(
                        [
                            'nik' => $emp->nik,
                            'salary_component_id' => $tunjJabatanComp->id,
                        ],
                        [
                            'amount' => 750000.00,
                            'effective_date' => '2026-01-01',
                            'is_active' => true,
                        ]
                    );
                }
            }
        }

        // 3. Module feature flag
        ModuleFeature::updateOrCreate(
            ['module_code' => 'payroll'],
            [
                'module_name' => 'Payroll & Penggajian Core',
                'category' => 'FINANCE',
                'description' => 'Manajemen periode penggajian, komponen upah, penugasan gaji, dan snapshot THP bulanan',
                'is_enabled' => true,
            ]
        );

        // 4. Permissions & Permission Groups
        $permissionGroups = [
            'Payroll Core' => [
                'payroll.index',
                'payroll.create',
                'payroll.calculate',
                'payroll.finalize',
                'payroll.reopen',
                'payroll.delete',
            ],
            'Struktur Gaji' => [
                'salary_component.index',
                'salary_component.create',
                'salary_component.edit',
                'salary_component.delete',
                'employee_salary.index',
                'employee_salary.edit',
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
