<?php

namespace Database\Seeders;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\Jamkerja;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\Setjamkerjabyday;
use App\Models\User;
use App\Models\Userkaryawan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class MvpPresentationSeeder extends Seeder
{
    /**
     * Run the database seeds for MVP Presentation.
     */
    public function run(): void
    {
        $this->command->info('=== 1. Menghapus Seluruh File Gambar Fisik ===');
        
        // Hapus folder upload fisik
        $pathsToClean = [
            storage_path('app/public/uploads/facerecognition'),
            storage_path('app/public/uploads/absensi'),
            storage_path('app/public/karyawan'),
            storage_path('app/public/uploads'),
            storage_path('app/public/public'),
        ];

        foreach ($pathsToClean as $path) {
            if (File::exists($path)) {
                File::deleteDirectory($path);
                $this->command->info("Deleted directory: {$path}");
            }
        }

        // Buat folder kosong bersih
        File::makeDirectory(storage_path('app/public/uploads/facerecognition'), 0755, true, true);
        File::makeDirectory(storage_path('app/public/uploads/absensi'), 0755, true, true);
        File::makeDirectory(storage_path('app/public/karyawan'), 0755, true, true);

        $this->command->info('=== 2. Membersihkan Data Database Lama ===');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate tabel data
        DB::table('presensi')->truncate();
        DB::table('presensi_izinabsen')->truncate();
        DB::table('presensi_izincuti')->truncate();
        DB::table('presensi_izinsakit')->truncate();
        DB::table('presensi_izindinas')->truncate();
        DB::table('karyawan_wajah')->truncate();
        DB::table('users_karyawan')->truncate();
        DB::table('presensi_jamkerja_byday')->truncate();
        DB::table('presensi_jamkerja_bydate')->truncate();
        DB::table('karyawan')->truncate();
        DB::table('cabang')->truncate();

        // Hapus user selain admin
        DB::table('users')->whereNotIn('username', ['admin', 'adamadifa'])->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('=== 3. Membuat 1 Cabang Utama di Medan (Radius Se-Sumatera Utara) ===');
        // Radius 500,000 meter (500 km) mencakup seluruh Provinsi Sumatera Utara
        $cabangMedan = Cabang::create([
            'kode_cabang' => 'MDN',
            'nama_cabang' => 'Kantor Cabang Medan (Sumatera Utara)',
            'alamat_cabang' => 'Jl. Gatot Subroto No. 45, Kota Medan, Sumatera Utara',
            'telepon_cabang' => '0614512345',
            'lokasi_cabang' => '3.5951956,98.6722227',
            'radius_cabang' => '500000', // 500 KM (Se-Sumatera Utara)
        ]);

        // Departemen & Jabatan
        $departemens = [
            ['kode_dept' => 'IT', 'nama_dept' => 'Teknologi Informasi'],
            ['kode_dept' => 'HRD', 'nama_dept' => 'Human Resources'],
            ['kode_dept' => 'KUA', 'nama_dept' => 'Keuangan & Akuntansi'],
            ['kode_dept' => 'PRD', 'nama_dept' => 'Operasional Produksi'],
        ];
        foreach ($departemens as $d) {
            Departemen::updateOrCreate(['kode_dept' => $d['kode_dept']], $d);
        }

        $jabatans = [
            ['kode_jabatan' => 'J01', 'nama_jabatan' => 'Manager'],
            ['kode_jabatan' => 'J02', 'nama_jabatan' => 'Supervisor'],
            ['kode_jabatan' => 'J03', 'nama_jabatan' => 'Staff'],
        ];
        foreach ($jabatans as $j) {
            Jabatan::updateOrCreate(['kode_jabatan' => $j['kode_jabatan']], $j);
        }

        // Jam Kerja
        Jamkerja::updateOrCreate(
            ['kode_jam_kerja' => 'JK01'],
            [
                'nama_jam_kerja' => 'Shift Regular Pagi',
                'jam_masuk' => '08:00:00',
                'jam_pulang' => '17:00:00',
                'total_jam' => 8,
                'istirahat' => 0,
                'lintashari' => 0,
                'keterangan' => 'Shift Pagi Standard',
                'color' => '#32745e'
            ]
        );

        $this->command->info('=== 4. Mengupdate Akun Admin: Development MVP ===');
        $superRole = Role::firstOrCreate(['name' => 'super admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $karyawanRole = Role::firstOrCreate(['name' => 'karyawan']);

        $defaultPassword = Hash::make('123456');

        // Admin Utama: Development MVP (username: admin)
        $adminUser = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Development MVP',
                'email' => 'admin@presensi.com',
                'password' => $defaultPassword,
            ]
        );
        if (!$adminUser->hasRole('super admin')) {
            $adminUser->assignRole($superRole);
        }
        $adminUser->cabangs()->sync(['MDN']);
        $adminUser->departemens()->sync(['IT', 'HRD', 'KUA', 'PRD']);

        // Akun Adam Adifa diset juga namanya ke Development MVP
        $adamUser = User::updateOrCreate(
            ['username' => 'adamadifa'],
            [
                'name' => 'Development MVP',
                'email' => 'adamadifa@gmail.com',
                'password' => $defaultPassword,
            ]
        );
        if (!$adamUser->hasRole('super admin')) {
            $adamUser->assignRole($superRole);
        }
        $adamUser->cabangs()->sync(['MDN']);
        $adamUser->departemens()->sync(['IT', 'HRD', 'KUA', 'PRD']);

        $this->command->info('=== 5. Membuat HANYA 2 Karyawan di Medan ===');
        $twoEmployees = [
            [
                'nik' => '1001',
                'no_ktp' => '1271010101900001',
                'nama_karyawan' => 'Ahmad Rizki',
                'tempat_lahir' => 'Medan',
                'tanggal_lahir' => '1990-01-15',
                'alamat' => 'Jl. Gatot Subroto No. 10, Medan',
                'no_hp' => '081234561001',
                'jenis_kelamin' => 'L',
                'kode_status_kawin' => 'K1',
                'pendidikan_terakhir' => 'S1',
                'kode_cabang' => 'MDN',
                'kode_dept' => 'IT',
                'kode_jabatan' => 'J01',
                'tanggal_masuk' => '2021-01-01',
                'status_karyawan' => 'T',
                'lock_location' => 0,
                'status_aktif_karyawan' => 1,
                'password' => $defaultPassword,
            ],
            [
                'nik' => '1002',
                'no_ktp' => '1271010101920002',
                'nama_karyawan' => 'Siti Nurhaliza',
                'tempat_lahir' => 'Medan',
                'tanggal_lahir' => '1992-03-20',
                'alamat' => 'Jl. Sisingamangaraja No. 45, Medan',
                'no_hp' => '081234561002',
                'jenis_kelamin' => 'P',
                'kode_status_kawin' => 'TK',
                'pendidikan_terakhir' => 'S1',
                'kode_cabang' => 'MDN',
                'kode_dept' => 'HRD',
                'kode_jabatan' => 'J02',
                'tanggal_masuk' => '2021-02-15',
                'status_karyawan' => 'T',
                'lock_location' => 0,
                'status_aktif_karyawan' => 1,
                'password' => $defaultPassword,
            ],
        ];

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        foreach ($twoEmployees as $empData) {
            $karyawan = Karyawan::create($empData);

            $kUser = User::create([
                'name' => $karyawan->nama_karyawan,
                'email' => $karyawan->nik . '@presensi.com',
                'username' => $karyawan->nik,
                'password' => $defaultPassword,
            ]);
            $kUser->assignRole($karyawanRole);

            Userkaryawan::create([
                'id_user' => $kUser->id,
                'nik' => $karyawan->nik,
            ]);

            foreach ($days as $day) {
                Setjamkerjabyday::create([
                    'nik' => $karyawan->nik,
                    'hari' => $day,
                    'kode_jam_kerja' => 'JK01',
                ]);
            }
        }

        $this->command->info('=== 6. Generate Sample Presensi 7 Hari Terakhir untuk 2 Karyawan ===');
        $today = Carbon::today('Asia/Jakarta');
        $startDate = $today->copy()->subDays(6);

        foreach ([$twoEmployees[0]['nik'], $twoEmployees[1]['nik']] as $nik) {
            $curr = $startDate->copy();
            while ($curr->lte($today)) {
                $dateStr = $curr->format('Y-m-d');
                $dayOfWeek = $curr->dayOfWeek;

                if ($dayOfWeek !== 0) { // Bukan Minggu
                    Presensi::create([
                        'nik' => $nik,
                        'tanggal' => $dateStr,
                        'jam_in' => "{$dateStr} 07:45:00",
                        'jam_out' => "{$dateStr} 17:05:00",
                        'foto_in' => null,
                        'foto_out' => null,
                        'lokasi_in' => '3.5951956,98.6722227',
                        'lokasi_out' => '3.5951956,98.6722227',
                        'kode_jam_kerja' => 'JK01',
                        'status' => 'h'
                    ]);
                }
                $curr->addDay();
            }
        }

        $this->command->info('=== MVP Presentation Seeder Berhasil Dijalankan! ===');
    }
}
