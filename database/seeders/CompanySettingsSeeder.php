<?php

namespace Database\Seeders;

use App\Models\CompanySetting;
use App\Models\Pengaturanumum;
use App\Models\Permission_group;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CompanySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $existing = CompanySetting::first();
        if (!$existing) {
            $umum = Pengaturanumum::first();
            CompanySetting::create([
                'company_name' => $umum?->nama_perusahaan ?? 'Presence Universal HR',
                'legal_name' => $umum?->nama_perusahaan ? 'PT ' . $umum->nama_perusahaan : 'PT Presence Universal HR',
                'app_name' => 'Presence',
                'app_tagline' => 'Universal HR Management System',
                'business_type' => 'General',
                'npwp' => null,
                'nib' => null,
                'address' => $umum?->alamat ?? 'Jl. Jenderal Sudirman Kav. 1, Jakarta',
                'province' => 'DKI Jakarta',
                'city' => 'Jakarta Selatan',
                'postal_code' => '12190',
                'phone' => $umum?->telepon ?? '021-12345678',
                'email' => 'hr@' . ($umum?->domain_email ?: 'hrpresence.com'),
                'website' => 'https://hrpresence.com',
                'timezone' => $umum?->timezone ?? 'Asia/Jakarta',
                'locale' => 'id',
                'currency' => 'IDR',
                'date_format' => 'd-m-Y',
                'payroll_cutoff_date' => $umum?->periode_laporan_dari ?? 20,
                'payroll_payment_date' => $umum?->periode_laporan_sampai ?? 25,
                'theme_color_primary' => $umum?->theme_color_1 ?? '#3C2A21',
                'theme_color_secondary' => $umum?->theme_color_2 ?? '#634832',
                'logo' => $umum?->logo ?? null,
            ]);
        }

        // Permissions for Company Settings
        $permissiongroup = Permission_group::firstOrCreate(['name' => 'Company Settings']);
        $perms = [
            'company_settings.index',
            'company_settings.edit',
        ];

        foreach ($perms as $pName) {
            Permission::firstOrCreate(['name' => $pName], ['id_permission_group' => $permissiongroup->id]);
        }

        // Assign to super admin and master admin
        $roles = Role::whereIn('name', ['super admin', 'master admin'])->get();
        foreach ($roles as $role) {
            foreach ($perms as $pName) {
                $p = Permission::where('name', $pName)->first();
                if ($p && !$role->hasPermissionTo($p)) {
                    $role->givePermissionTo($p);
                }
            }
        }
    }
}
