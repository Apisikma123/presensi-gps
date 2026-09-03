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

        // Default Coffee Shop Branches
        Cabang::firstOrCreate(
            ['kode_cabang' => 'CS1'],
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

        // Default Coffee Shop Departments (max 3 chars)
        Departemen::firstOrCreate(['kode_dept' => 'BAR'], ['nama_dept' => 'Bar & Beverage']);
        Departemen::firstOrCreate(['kode_dept' => 'KIT'], ['nama_dept' => 'Kitchen & Pastry']);
        Departemen::firstOrCreate(['kode_dept' => 'SRV'], ['nama_dept' => 'Service & Cashier']);
        Departemen::firstOrCreate(['kode_dept' => 'MGT'], ['nama_dept' => 'Store Management']);

        // Default Coffee Shop Positions (Jabatan max 3 chars)
        \App\Models\Jabatan::firstOrCreate(['kode_jabatan' => 'MGR'], ['nama_jabatan' => 'Store Manager']);
        \App\Models\Jabatan::firstOrCreate(['kode_jabatan' => 'HBD'], ['nama_jabatan' => 'Head Barista']);
        \App\Models\Jabatan::firstOrCreate(['kode_jabatan' => 'BAR'], ['nama_jabatan' => 'Barista']);
        \App\Models\Jabatan::firstOrCreate(['kode_jabatan' => 'KAS'], ['nama_jabatan' => 'Cashier']);
        \App\Models\Jabatan::firstOrCreate(['kode_jabatan' => 'KIT'], ['nama_jabatan' => 'Kitchen Crew']);

        // Default Coffee Shop Work Shifts (Jam Kerja)
        \App\Models\Jamkerja::firstOrCreate(
            ['kode_jam_kerja' => 'JK01'],
            [
                'nama_jam_kerja' => 'Shift 1 Pagi (07:00 - 15:00)',
                'jam_masuk' => '07:00:00',
                'jam_pulang' => '15:00:00',
                'istirahat' => '0',
                'total_jam' => 8,
                'lintashari' => '0',
                'keterangan' => 'Shift Pagi Outlet Coffee Shop',
                'color' => '#106f62',
            ]
        );

        \App\Models\Jamkerja::firstOrCreate(
            ['kode_jam_kerja' => 'JK02'],
            [
                'nama_jam_kerja' => 'Shift 2 Siang/Malam (14:00 - 22:00)',
                'jam_masuk' => '14:00:00',
                'jam_pulang' => '22:00:00',
                'istirahat' => '0',
                'total_jam' => 8,
                'lintashari' => '0',
                'keterangan' => 'Shift Malam Outlet Coffee Shop',
                'color' => '#e67e22',
            ]
        );

        // Sync all branches and departments as standard for super admin
        $allCabangs = Cabang::pluck('kode_cabang')->toArray();
        $allDepartemens = Departemen::pluck('kode_dept')->toArray();

        // ==========================================
        // 1. SUPER ADMIN ACCOUNT (admin / admin123)
        // ==========================================
        $adminUser = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Super Administrator',
                'email' => 'admin@coffeeshop.com',
                'password' => Hash::make('admin123'),
            ]
        );
        if (!$adminUser->hasRole('super admin')) {
            $adminUser->assignRole($role);
        }
        $adminUser->cabangs()->sync($allCabangs);
        $adminUser->departemens()->sync($allDepartemens);

        // ==========================================
        // 2. STORE MANAGER ACCOUNT (manager / manager123)
        // ==========================================
        $roleAdmin = Role::where('name', 'admin')->first();
        $managerUser = User::updateOrCreate(
            ['username' => 'manager'],
            [
                'name' => 'Store Manager Outlet',
                'email' => 'manager@coffeeshop.com',
                'password' => Hash::make('manager123'),
            ]
        );
        if (!$managerUser->hasRole('admin')) {
            $managerUser->assignRole($roleAdmin);
        }
        $managerUser->cabangs()->sync($allCabangs);
        $managerUser->departemens()->sync($allDepartemens);

        // ==========================================
        // 3. BARISTA ACCOUNT (barista / barista123)
        // ==========================================
        $roleKaryawan = Role::where('name', 'karyawan')->first();
        
        \App\Models\Karyawan::updateOrCreate(
            ['nik' => '250100001'],
            [
                'nik_show' => 'BAR-001',
                'no_ktp' => '3171012345670001',
                'nama_karyawan' => 'Budi Barista',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1998-05-15',
                'alamat' => 'Jl. Kopi No. 12, Jakarta',
                'jenis_kelamin' => 'L',
                'no_hp' => '081234567890',
                'kode_status_kawin' => 'TK',
                'pendidikan_terakhir' => 'SMA',
                'kode_cabang' => 'CS1',
                'kode_dept' => 'BAR',
                'kode_jabatan' => 'BAR',
                'tanggal_masuk' => '2024-01-01',
                'status_karyawan' => 'K',
                'status_aktif_karyawan' => '1',
                'lock_location' => '1',
                'password' => Hash::make('barista123'),
            ]
        );

        $baristaUser = User::updateOrCreate(
            ['username' => 'barista'],
            [
                'name' => 'Budi Barista',
                'email' => 'barista@coffeeshop.com',
                'password' => Hash::make('barista123'),
            ]
        );
        if (!$baristaUser->hasRole('karyawan')) {
            $baristaUser->assignRole($roleKaryawan);
        }
        \App\Models\Userkaryawan::updateOrCreate(
            ['id_user' => $baristaUser->id],
            [
                'nik' => '250100001',
                'approval_admin_id' => $managerUser->id,
            ]
        );

        // ==========================================
        // 4. KASIR ACCOUNT (kasir / kasir123)
        // ==========================================
        \App\Models\Karyawan::updateOrCreate(
            ['nik' => '250100002'],
            [
                'nik_show' => 'KAS-002',
                'no_ktp' => '3171012345670002',
                'nama_karyawan' => 'Siti Kasir',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '2000-08-20',
                'alamat' => 'Jl. Melati No. 8, Jakarta',
                'jenis_kelamin' => 'P',
                'no_hp' => '081298765432',
                'kode_status_kawin' => 'TK',
                'pendidikan_terakhir' => 'SMA',
                'kode_cabang' => 'CS1',
                'kode_dept' => 'SRV',
                'kode_jabatan' => 'KAS',
                'tanggal_masuk' => '2024-02-01',
                'status_karyawan' => 'K',
                'status_aktif_karyawan' => '1',
                'lock_location' => '1',
                'password' => Hash::make('kasir123'),
            ]
        );

        $kasirUser = User::updateOrCreate(
            ['username' => 'kasir'],
            [
                'name' => 'Siti Kasir',
                'email' => 'kasir@coffeeshop.com',
                'password' => Hash::make('kasir123'),
            ]
        );
        if (!$kasirUser->hasRole('karyawan')) {
            $kasirUser->assignRole($roleKaryawan);
        }
        \App\Models\Userkaryawan::updateOrCreate(
            ['id_user' => $kasirUser->id],
            [
                'nik' => '250100002',
                'approval_admin_id' => $managerUser->id,
            ]
        );

        // Default Coffee Shop Cuti Types
        \App\Models\Cuti::firstOrCreate(['kode_cuti' => 'C01'], ['jenis_cuti' => 'Cuti Tahunan', 'jumlah_hari' => 12]);
        \App\Models\Cuti::firstOrCreate(['kode_cuti' => 'C02'], ['jenis_cuti' => 'Cuti Khusus / Penting', 'jumlah_hari' => 3]);
        \App\Models\Cuti::firstOrCreate(['kode_cuti' => 'C03'], ['jenis_cuti' => 'Cuti Menikah', 'jumlah_hari' => 3]);
        \App\Models\Cuti::firstOrCreate(['kode_cuti' => 'C04'], ['jenis_cuti' => 'Cuti Melahirkan', 'jumlah_hari' => 90]);

        // ==========================================
        // Sync Role Permissions
        // ==========================================
        // 5. SEED & SYNC FULL PERMISSIONS
        // ==========================================
        $groupMap = [
            'karyawan' => 18,
            'izinabsen' => 10,
            'izincuti' => 11,
            'izindinas' => 12,
            'izinsakit' => 13,
            'koreksi' => 27,
            'presensi' => 28,
            'cuti' => 11,
            'cabang' => 47,
            'departemen' => 18,
            'jabatan' => 14,
            'jamkerja' => 16,
            'jamkerjabydept' => 15,
            'harilibur' => 9,
            'generalsetting' => 24,
            'laporan' => 21,
            'aktivitaskaryawan' => 3,
            'kunjungan' => 20,
            'lembur' => 22,
            'users' => 18,
            'roles' => 18,
            'trackingpresensi' => 30,
            'facerecognition' => 28,
        ];

        // Ensure roles & users permissions exist
        $extraPermissions = [
            'roles.index', 'roles.create', 'roles.edit', 'roles.delete',
            'roles.createrolepermission', 'roles.storerolepermission',
        ];
        foreach ($extraPermissions as $perm) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $perm], ['id_permission_group' => 18]);
        }

        // 1. Karyawan Role Permissions (Self-service mobile & attendance features)
        $karyawanPermissions = [
            'presensi.index', 'presensi.create',
            'izinabsen.index', 'izinabsen.create', 'izinabsen.delete',
            'izinsakit.index', 'izinsakit.create', 'izinsakit.delete',
            'izincuti.index', 'izincuti.create', 'izincuti.delete',
            'izindinas.index', 'izindinas.create', 'izindinas.delete',
            'koreksi.index', 'koreksi.create', 'koreksi.delete',
            'aktivitaskaryawan.create', 'aktivitaskaryawan.delete', 'aktivitaskaryawan.edit', 'aktivitaskaryawan.index',
            'kunjungan.create', 'kunjungan.index',
            'lembur.index', 'lembur.create',
        ];
        foreach ($karyawanPermissions as $perm) {
            $prefix = explode('.', $perm)[0];
            $groupId = $groupMap[$prefix] ?? 18;
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $perm], ['id_permission_group' => $groupId]);
        }
        $roleKaryawan->syncPermissions($karyawanPermissions);

        // 2. Store Manager / Admin Role Permissions (Full management for outlet)
        $adminPermissions = [
            'karyawan.index', 'karyawan.create', 'karyawan.edit', 'karyawan.delete', 'karyawan.show', 'karyawan.setjamkerja', 'karyawan.setcabang',
            'departemen.index', 'departemen.create', 'departemen.edit', 'departemen.delete',
            'cabang.index', 'cabang.create', 'cabang.edit', 'cabang.delete',
            'jabatan.index', 'jabatan.create', 'jabatan.edit', 'jabatan.delete',
            'cuti.index', 'cuti.create', 'cuti.edit', 'cuti.delete',
            'presensi.index', 'presensi.create', 'presensi.edit', 'presensi.delete', 'trackingpresensi.index',
            'izinabsen.index', 'izinabsen.create', 'izinabsen.edit', 'izinabsen.delete', 'izinabsen.approve',
            'izinsakit.index', 'izinsakit.create', 'izinsakit.edit', 'izinsakit.delete', 'izinsakit.approve',
            'izincuti.index', 'izincuti.create', 'izincuti.edit', 'izincuti.delete', 'izincuti.approve',
            'izindinas.index', 'izindinas.create', 'izindinas.edit', 'izindinas.delete', 'izindinas.approve',
            'koreksi.index', 'koreksi.create', 'koreksi.delete', 'koreksi.approve',
            'harilibur.index', 'harilibur.create', 'harilibur.edit', 'harilibur.delete', 'harilibur.setharilibur',
            'jamkerja.index', 'jamkerja.create', 'jamkerja.edit', 'jamkerja.delete',
            'jamkerjabydept.index', 'jamkerjabydept.create', 'jamkerjabydept.edit', 'jamkerjabydept.delete',
            'generalsetting.index', 'generalsetting.edit',
            'laporan.presensi', 'laporan.cuti',
            'users.index', 'users.create', 'users.edit', 'users.delete',
            'roles.index', 'roles.create', 'roles.edit', 'roles.delete', 'roles.createrolepermission', 'roles.storerolepermission',
        ];
        foreach ($adminPermissions as $perm) {
            $prefix = explode('.', $perm)[0];
            $groupId = $groupMap[$prefix] ?? 18;
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $perm], ['id_permission_group' => $groupId]);
        }
        $roleAdmin->syncPermissions($adminPermissions);

        // 3. Super Admin & Master Admin Role Permissions (Every permission in the system)
        $roleMasterAdmin = Role::firstOrCreate(['name' => 'master admin']);
        $allPermissions = \Spatie\Permission\Models\Permission::all();
        $role->syncPermissions($allPermissions);
        $roleMasterAdmin->syncPermissions($allPermissions);
    }
}
