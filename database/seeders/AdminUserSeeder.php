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

        // Sync all branches and departments as standard for super admin
        $allCabangs = Cabang::pluck('kode_cabang')->toArray();
        $allDepartemens = Departemen::pluck('kode_dept')->toArray();
        
        $user->cabangs()->sync($allCabangs);
        $user->departemens()->sync($allDepartemens);
    }
}
