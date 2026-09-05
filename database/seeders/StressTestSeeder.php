<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class StressTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Generates 1,050 employees, 1,050 user accounts, and 5,000+ attendance records
     * for high-load performance and stress testing.
     */
    public function run(): void
    {
        $this->command->info('=== Memulai Generate Data Dummy Stress Test (1000+ Karyawan & Presensi) ===');

        $totalEmployees = 1050;
        $startNik = 26000001;

        // 1. Cache Master Data Reference
        $cabangs = DB::table('cabang')->pluck('lokasi_cabang', 'kode_cabang')->toArray();
        if (empty($cabangs)) {
            $cabangs = ['CS1' => '-6.229728,106.807464'];
        }
        $kodeCabangs = array_keys($cabangs);

        $depts = DB::table('departemen')->pluck('kode_dept')->toArray();
        if (empty($depts)) {
            $depts = ['PRD', 'SDM', 'KUA', 'IT'];
        }

        $jabatans = DB::table('jabatan')->pluck('kode_jabatan')->toArray();
        if (empty($jabatans)) {
            $jabatans = ['J01', 'J02', 'J03', 'J04', 'J05'];
        }

        $statusKawinList = ['TK', 'K0', 'K1', 'K2'];

        // Pre-computed Bcrypt hash for password '12345' to ensure fast execution
        $defaultPasswordHash = Hash::make('12345');
        $now = Carbon::now();

        // 2. Realistic Indonesian Name Generators
        $firstNamesL = [
            'Ahmad', 'Budi', 'Chandra', 'Dedi', 'Eko', 'Fajar', 'Gunawan', 'Hendra', 'Indra', 'Joko',
            'Kurniawan', 'Lukman', 'Muhammad', 'Nur', 'Oki', 'Prasetyo', 'Rian', 'Surya', 'Teguh', 'Wahyu',
            'Yudi', 'Zainal', 'Agus', 'Bambang', 'Danang', 'Gilang', 'Hadi', 'Ilham', 'Reza', 'Rizki'
        ];
        $firstNamesP = [
            'Siti', 'Dewi', 'Rina', 'Maya', 'Putri', 'Ayu', 'Fitri', 'Nita', 'Mega', 'Lestari',
            'Kartika', 'Indah', 'Wulan', 'Dian', 'Sari', 'Anisa', 'Citra', 'Ratna', 'Tia', 'Yuni',
            'Amalia', 'Desi', 'Eka', 'Hani', 'Intan', 'Melati', 'Nadia', 'Ratih', 'Safitri', 'Widya'
        ];
        $lastNames = [
            'Saputra', 'Pratama', 'Hidayat', 'Kusuma', 'Wijaya', 'Santoso', 'Permana', 'Nugroho', 'Setiawan', 'Wibowo',
            'Firmansyah', 'Ramadhan', 'Putra', 'Utomo', 'Siregar', 'Nasution', 'Lubis', 'Harahap', 'Pasaribu', 'Simanjuntak',
            'Hutapea', 'Sitorus', 'Panjaitan', 'Nababan', 'Tanjung', 'Sihombing', 'Manurung', 'Sinaga', 'Gultom', 'Pardede',
            'Lestari', 'Wulandari', 'Anggraini', 'Safitri', 'Handayani', 'Rahmawati', 'Purnama', 'Oktaviani', 'Kurnia', 'Susanti'
        ];

        $cities = ['Jakarta', 'Bandung', 'Surabaya', 'Medan', 'Semarang', 'Makassar', 'Palembang', 'Tasikmalaya', 'Yogyakarta', 'Denpasar'];

        $karyawanList = [];
        $userList = [];
        $niks = [];

        $this->command->info("1/4 Menyiapkan data {$totalEmployees} karyawan dan akun...");

        for ($i = 0; $i < $totalEmployees; $i++) {
            $currentNik = (string) ($startNik + $i);
            $niks[] = $currentNik;

            $isMale = ($i % 2 === 0);
            $gender = $isMale ? 'L' : 'P';
            $firstPool = $isMale ? $firstNamesL : $firstNamesP;

            $fn = $firstPool[$i % count($firstPool)];
            $ln = $lastNames[($i + intdiv($i, count($firstPool))) % count($lastNames)];
            $fullName = $fn . ' ' . $ln;

            $cabangKode = $kodeCabangs[$i % count($kodeCabangs)];
            $deptKode = $depts[$i % count($depts)];
            $jabatanKode = $jabatans[$i % count($jabatans)];
            $statusKawin = $statusKawinList[$i % count($statusKawinList)];
            $statusKaryawan = ($i % 4 === 0) ? 'K' : 'T'; // 25% Kontrak, 75% Tetap
            $city = $cities[$i % count($cities)];

            $ktp = '32' . str_pad((string)($i + 1), 14, '0', STR_PAD_LEFT);
            $hp = '081' . str_pad((string)($startNik + $i), 9, '0', STR_PAD_RIGHT);

            $karyawanList[] = [
                'nik' => $currentNik,
                'nik_show' => $currentNik,
                'no_ktp' => $ktp,
                'npwp' => null,
                'hitung_pph21' => 1,
                'nama_karyawan' => $fullName,
                'tempat_lahir' => $city,
                'tanggal_lahir' => Carbon::create(1990 + ($i % 12), ($i % 12) + 1, ($i % 28) + 1)->toDateString(),
                'alamat' => "Jl. Karyawan Sejahtera No. " . ($i + 1) . ", " . $city,
                'alamat_sesuai_ktp' => null,
                'no_hp' => $hp,
                'email' => "user{$currentNik}@stresstest.local",
                'kontak_darurat' => null,
                'hubungan_kontak_darurat' => null,
                'nama_bank' => 'BCA',
                'no_rekening' => '88' . str_pad((string)$i, 8, '0', STR_PAD_LEFT),
                'nama_rekening' => $fullName,
                'jenis_kelamin' => $gender,
                'kode_status_kawin' => $statusKawin,
                'pendidikan_terakhir' => ($i % 3 === 0) ? 'S1' : (($i % 3 === 1) ? 'D3' : 'SMA'),
                'jurusan' => null,
                'kode_cabang' => $cabangKode,
                'kode_cabang_array' => null,
                'kode_dept' => $deptKode,
                'kode_jabatan' => $jabatanKode,
                'tanggal_masuk' => Carbon::create(2022 + ($i % 4), ($i % 12) + 1, 1)->toDateString(),
                'status_karyawan' => $statusKaryawan,
                'foto' => null,
                'kode_jadwal' => null,
                'pin' => '0000',
                'rfid_uid' => null,
                'tanggal_nonaktif' => null,
                'tanggal_off_gaji' => null,
                'lock_location' => '0',
                'lock_jam_kerja' => '1',
                'status_aktif_karyawan' => '1',
                'password' => $defaultPasswordHash,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $userList[] = [
                'name' => $fullName,
                'username' => $currentNik,
                'email' => "user{$currentNik}@stresstest.local",
                'password' => $defaultPasswordHash,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insert Karyawan in chunks of 250
        $this->command->info("2/4 Memasukkan data karyawan ke database...");
        foreach (array_chunk($karyawanList, 250) as $chunk) {
            DB::table('karyawan')->upsert($chunk, ['nik']);
        }
        $this->command->info("-> Berhasil menyimpan {$totalEmployees} karyawan.");

        // Insert Users in chunks of 250 and link with users_karyawan
        $this->command->info("3/4 Membuat user accounts & role mapping...");
        $userIdMap = [];
        foreach (array_chunk($userList, 250) as $chunk) {
            DB::table('users')->upsert($chunk, ['email'], ['name', 'username', 'password', 'updated_at']);
        }

        // Fetch inserted user IDs for linkage
        $createdUsers = DB::table('users')
            ->where('email', 'like', '%@stresstest.local')
            ->select('id', 'email', 'username')
            ->get();

        $userKaryawanList = [];
        $modelHasRolesList = [];

        foreach ($createdUsers as $u) {
            $nik = (string) $u->username;
            $userKaryawanList[] = [
                'nik' => $nik,
                'id_user' => $u->id,
                'approval_admin_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $modelHasRolesList[] = [
                'role_id' => 3, // Role 'karyawan'
                'model_type' => 'App\\Models\\User',
                'model_id' => $u->id,
            ];
        }

        foreach (array_chunk($userKaryawanList, 250) as $chunk) {
            DB::table('users_karyawan')->upsert($chunk, ['nik', 'id_user']);
        }

        // Insert model_has_roles
        foreach (array_chunk($modelHasRolesList, 250) as $chunk) {
            DB::table('model_has_roles')->insertOrIgnore($chunk);
        }
        $this->command->info("-> Berhasil membuat dan menghubungkan {$createdUsers->count()} akun login.");

        // 4. Generate Attendance (Presensi) Records for Stress Testing (Last 5 Workdays)
        $this->command->info("4/4 Mengenerate 5,000+ data presensi (absen masuk & pulang)...");
        $dates = [
            '2026-08-31',
            '2026-09-01',
            '2026-09-02',
            '2026-09-03',
            '2026-09-04',
        ];

        $presensiList = [];
        $totalPresensi = 0;

        foreach ($karyawanList as $k) {
            $branchCoords = $cabangs[$k['kode_cabang']] ?? '-6.229728,106.807464';

            foreach ($dates as $date) {
                // Add slight minute variance for realistic traffic
                $minuteIn = str_pad((string)(rand(45, 59)), 2, '0', STR_PAD_LEFT);
                $minuteOut = str_pad((string)(rand(5, 30)), 2, '0', STR_PAD_LEFT);

                $presensiList[] = [
                    'nik' => $k['nik'],
                    'tanggal' => $date,
                    'jam_in' => "{$date} 07:{$minuteIn}:00",
                    'jam_out' => "{$date} 17:{$minuteOut}:00",
                    'foto_in' => null,
                    'foto_out' => null,
                    'id_mesin' => null,
                    'lokasi_in' => $branchCoords,
                    'lokasi_out' => $branchCoords,
                    'kode_jam_kerja' => 'JK01',
                    'status' => 'h',
                    'denda' => null,
                    'jam_lembur_aktual' => null,
                    'jam_lembur_netto' => null,
                    'nominal_lembur' => null,
                    'is_lembur_khusus' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $totalPresensi++;

                // Flush chunk every 500 rows to keep memory usage low
                if (count($presensiList) >= 500) {
                    DB::table('presensi')->insert($presensiList);
                    $presensiList = [];
                }
            }
        }

        if (!empty($presensiList)) {
            DB::table('presensi')->insert($presensiList);
        }

        $this->command->info("-> Berhasil mengenerate {$totalPresensi} data presensi.");

        // 5. Generate Sample Izin Absen & Lembur to test approvals
        $this->command->info("Menambahkan sample pengajuan izin absen & lembur...");
        $izinList = [];
        for ($j = 0; $j < 60; $j++) {
            $k = $karyawanList[$j];
            $kodeIzin = "IA" . date('ym') . str_pad((string)($j + 100), 4, '0', STR_PAD_LEFT);
            $izinList[] = [
                'kode_izin' => $kodeIzin,
                'tanggal' => '2026-09-04',
                'dari' => '2026-09-04',
                'sampai' => '2026-09-05',
                'nik' => $k['nik'],
                'keterangan' => 'Keperluan keluarga mendesak (Data Dummy Stress Test)',
                'keterangan_hrd' => null,
                'status' => ($j % 3 === 0) ? '1' : '0', // Sebagian pending, sebagian disetujui
                'approval_step' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('presensi_izinabsen')->upsert($izinList, ['kode_izin']);

        $this->command->newLine();
        $this->command->info('===============================================================');
        $this->command->info('  STRESS TEST DATA BERHASIL DIGENERATE!');
        $this->command->info("  - Total Karyawan Dummy : {$totalEmployees}");
        $this->command->info("  - Total Akun User      : {$totalEmployees} (Password: 12345)");
        $this->command->info("  - Total Data Presensi  : {$totalPresensi} records");
        $this->command->info("  - Total Pengajuan Izin : 60 records");
        $this->command->info('===============================================================');
    }
}
