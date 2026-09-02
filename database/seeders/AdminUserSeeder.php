<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cabang;
use App\Models\Departemen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure roles exist in order (1: super admin, 2: admin, 3: karyawan)
        $role = Role::firstOrCreate(['name' => 'super admin']);
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'karyawan']);

        // Create or update user adamadifa
        $user = User::updateOrCreate(
            ['username' => 'adamadifa'],
            [
                'name' => 'adamadifa',
                'email' => 'adamadifa@gmail.com', // Default email
                'password' => Hash::make('adamadifa#311766'),
            ]
        );

        // Assign role
        if (!$user->hasRole('super admin')) {
            $user->assignRole($role);
        }

        // Ensure Cabang Medan Besar exists
        Cabang::firstOrCreate(
            ['kode_cabang' => 'MDN'],
            [
                'nama_cabang' => 'Cabang Medan Besar',
                'alamat_cabang' => 'Jl. Gatot Subroto No. 45, Kota Medan, Sumatera Utara',
                'telepon_cabang' => '0614512345',
                'lokasi_cabang' => '3.5951956,98.6722227',
                'radius_cabang' => 500000,
                'timezone' => 'Asia/Jakarta'
            ]
        );

        // Sync all branches and departments as standard for super admin
        $allCabangs = Cabang::pluck('kode_cabang')->toArray();
        $allDepartemens = Departemen::pluck('kode_dept')->toArray();
        
        $user->cabangs()->sync($allCabangs);
        $user->departemens()->sync($allDepartemens);

        // Create or update user sakip
        $sakipUser = User::updateOrCreate(
            ['username' => 'sakip'],
            [
                'name' => 'Admin SAKIP',
                'email' => 'sakip@presensi.com',
                'password' => Hash::make('sakip123'),
            ]
        );

        if (!$sakipUser->hasRole('super admin')) {
            $sakipUser->assignRole($role);
        }
        if (!$sakipUser->hasRole('admin')) {
            $sakipUser->assignRole(Role::where('name', 'admin')->first());
        }

        $sakipUser->cabangs()->sync($allCabangs);
        $sakipUser->departemens()->sync($allDepartemens);
    }
}
