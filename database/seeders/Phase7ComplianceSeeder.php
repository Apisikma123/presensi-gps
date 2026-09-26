<?php

namespace Database\Seeders;

use App\Models\IndonesiaPolicyRule;
use App\Models\ModuleFeature;
use App\Models\Permission_group;
use App\Services\IndonesiaTaxService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Phase7ComplianceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Indonesian Statutory Policy Rules
        IndonesiaPolicyRule::updateOrCreate(
            ['policy_type' => 'TAX', 'version' => 'PP 58/2023'],
            [
                'name' => 'Tarif Efektif Rata-Rata (TER) PPh 21',
                'effective_from' => '2024-01-01',
                'source_reference' => 'PP No. 58 Tahun 2023 & PMK No. 168 Tahun 2023',
                'config' => [
                    'ter_a' => IndonesiaTaxService::defaultBrackets('A'),
                    'ter_b' => IndonesiaTaxService::defaultBrackets('B'),
                    'ter_c' => IndonesiaTaxService::defaultBrackets('C'),
                ],
                'is_active' => true,
            ]
        );

        IndonesiaPolicyRule::updateOrCreate(
            ['policy_type' => 'BPJS_KES', 'version' => 'Perpres 64/2020'],
            [
                'name' => 'BPJS Kesehatan Standar Badan Usaha',
                'effective_from' => '2020-07-01',
                'source_reference' => 'Peraturan Presiden Republik Indonesia Nomor 64 Tahun 2020',
                'config' => [
                    'employee_rate' => 0.01,
                    'employer_rate' => 0.04,
                    'ceiling' => 12000000.00,
                ],
                'is_active' => true,
            ]
        );

        IndonesiaPolicyRule::updateOrCreate(
            ['policy_type' => 'BPJS_TK', 'version' => 'PP 44/45 2015'],
            [
                'name' => 'BPJS Ketenagakerjaan (JHT, JP, JKK, JKM)',
                'effective_from' => '2015-07-01',
                'source_reference' => 'PP No. 44 Tahun 2015 & PP No. 45 Tahun 2015',
                'config' => [
                    'jht_employee_rate' => 0.02,
                    'jht_employer_rate' => 0.037,
                    'jp_employee_rate' => 0.01,
                    'jp_employer_rate' => 0.02,
                    'jp_ceiling' => 10042300.00,
                    'jkk_employer_rate' => 0.0024,
                    'jkm_employer_rate' => 0.003,
                ],
                'is_active' => true,
            ]
        );

        IndonesiaPolicyRule::updateOrCreate(
            ['policy_type' => 'THR', 'version' => 'Permenaker 6/2016'],
            [
                'name' => 'Tunjangan Hari Raya Keagamaan',
                'effective_from' => '2016-03-08',
                'source_reference' => 'PP No. 36 Tahun 2021 & Permenaker No. 6 Tahun 2016',
                'config' => [
                    'full_tenure_months' => 12,
                    'minimum_service_months' => 1,
                    'prorata_formula' => 'service_months / 12 * base_salary',
                ],
                'is_active' => true,
            ]
        );

        // 2. Feature Flag
        ModuleFeature::updateOrCreate(
            ['module_code' => 'compliance'],
            [
                'module_name' => 'Statutory Compliance & THR',
                'category' => 'FINANCE',
                'description' => 'Kepatuhan regulasi Indonesia: PPh 21 TER (PP 58/2023), BPJS TK & Kesehatan, serta kalkulasi THR Keagamaan',
                'is_enabled' => true,
            ]
        );

        // 3. Permissions & Permission Groups
        $permissionGroups = [
            'Statutory Compliance' => [
                'compliance.index',
                'compliance.edit',
            ],
            'THR Keagamaan' => [
                'thr.index',
                'thr.create',
                'thr.calculate',
                'thr.finalize',
                'thr.export',
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
