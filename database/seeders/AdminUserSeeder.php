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

        // Default Coffee Shop Branches
        Cabang::firstOrCreate(
            ['kode_cabang' => 'CS01'],
            [
                'nama_cabang' => 'Coffee Shop Outlet Utama',
                'alamat_cabang' => 'Jl. Senopati No. 18, Jakarta Selatan',
                'telepon_cabang' => '0217281920',
                'lokasi_cabang' => '-6.229728,106.807464',
                'radius_cabang' => 100, // 100 meters GPS radius
                'timezone' => 'Asia/Jakarta'
            ]
        );

        Cabang::firstOrCreate(
            ['kode_cabang' => 'MDN'],
            [
                'nama_cabang' => 'Coffee Shop Outlet Medan',
                'alamat_cabang' => 'Jl. Gatot Subroto No. 45, Kota Medan, Sumatera Utara',
                'telepon_cabang' => '0614512345',
                'lokasi_cabang' => '3.5951956,98.6722227',
                'radius_cabang' => 500,
                'timezone' => 'Asia/Jakarta'
            ]
        );

        // Default Coffee Shop Departments
        Departemen::firstOrCreate(['kode_dept' => 'BAR'], ['nama_dept' => 'Bar & Beverage']);
        Departemen::firstOrCreate(['kode_dept' => 'KIT'], ['nama_dept' => 'Kitchen & Pastry']);
        Departemen::firstOrCreate(['kode_dept' => 'SRV'], ['nama_dept' => 'Service & Cashier']);
        Departemen::firstOrCreate(['kode_dept' => 'MGT'], ['nama_dept' => 'Store Management']);

        // Default Coffee Shop Positions (Jabatan)
        \App\Models\Jabatan::firstOrCreate(['kode_jabatan' => 'MGR'], ['nama_jabatan' => 'Store Manager']);
        \App\Models\Jabatan::firstOrCreate(['kode_jabatan' => 'HBAR'], ['nama_jabatan' => 'Head Barista']);
        \App\Models\Jabatan::firstOrCreate(['kode_jabatan' => 'BAR'], ['nama_jabatan' => 'Barista']);
        \App\Models\Jabatan::firstOrCreate(['kode_jabatan' => 'KAS'], ['nama_jabatan' => 'Cashier']);
        \App\Models\Jabatan::firstOrCreate(['kode_jabatan' => 'KIT'], ['nama_jabatan' => 'Kitchen Crew']);

        // Default Coffee Shop Work Shifts (Jam Kerja)
        \App\Models\Jamkerja::firstOrCreate(
            ['kode_jam_kerja' => 'S1'],
            [
                'nama_jam_kerja' => 'Shift 1 Pagi (07:00 - 15:00)',
                'jam_masuk' => '07:00',
                'jam_pulang' => '15:00',
                'total_jam' => 8,
                'lintashari' => 0
            ]
        );

        \App\Models\Jamkerja::firstOrCreate(
            ['kode_jam_kerja' => 'S2'],
            [
                'nama_jam_kerja' => 'Shift 2 Siang/Malam (14:00 - 22:00)',
                'jam_masuk' => '14:00',
                'jam_pulang' => '22:00',
                'total_jam' => 8,
                'lintashari' => 0
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
