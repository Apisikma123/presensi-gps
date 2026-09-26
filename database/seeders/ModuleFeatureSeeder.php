<?php

namespace Database\Seeders;

use App\Models\ModuleFeature;
use App\Models\Permission_group;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ModuleFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'module_code' => 'attendance',
                'module_name' => 'Presensi / Kehadiran',
                'category' => 'Core Attendance',
                'is_enabled' => true,
                'description' => 'Pencatatan jam masuk, pulang, lintas hari, dan rekapitulasi kehadiran karyawan.',
                'config' => ['allow_selfie' => true, 'require_notes' => false],
            ],
            [
                'module_code' => 'gps',
                'module_name' => 'Validasi Lokasi GPS',
                'category' => 'Core Attendance',
                'is_enabled' => true,
                'description' => 'Geofencing radius presensi berbasis koordinat cabang atau lokasi penugasan.',
                'config' => ['strict_radius' => true, 'mock_location_detection' => true],
            ],
            [
                'module_code' => 'face_recognition',
                'module_name' => 'Face Recognition AI',
                'category' => 'Core Attendance',
                'is_enabled' => true,
                'description' => 'Verifikasi biometrik pengenalan wajah saat karyawan melakukan presensi.',
                'config' => ['threshold' => 0.5, 'anti_spoofing' => true],
            ],
            [
                'module_code' => 'leave',
                'module_name' => 'Manajemen Cuti & Izin',
                'category' => 'Time Management',
                'is_enabled' => true,
                'description' => 'Pengajuan, persetujuan berjenjang, dan pemotongan kuota cuti tahunan, sakit, dan izin absen.',
                'config' => ['allow_half_day' => false, 'require_attachment_for_sick' => true],
            ],
            [
                'module_code' => 'overtime',
                'module_name' => 'Lembur / Overtime',
                'category' => 'Time Management',
                'is_enabled' => true,
                'description' => 'Perhitungan lembur kerja, SPK lembur (Surat Perintah Kerja Lembur), dan formula Depnaker.',
                'config' => ['depnaker_multipliers' => true, 'approval_required' => true],
            ],
            [
                'module_code' => 'payroll',
                'module_name' => 'Payroll & Gaji',
                'category' => 'Payroll & Finance',
                'is_enabled' => true,
                'description' => 'Perhitungan gaji pokok, tunjangan tetap/tidak tetap, potongan absensi, dan penerbitan slip gaji.',
                'config' => ['prorate_new_hires' => true, 'show_tax_in_slip' => true],
            ],
            [
                'module_code' => 'pph21',
                'module_name' => 'Pajak PPh 21 (TER & Tarif Pasal 17)',
                'category' => 'Payroll & Finance',
                'is_enabled' => true,
                'description' => 'Kalkulasi otomatis potongan PPh 21 menggunakan mekanisme TER Bulanan (PP 58/2023) dan masa pajak Desember.',
                'config' => ['ter_enabled' => true, 'gross_up' => false],
            ],
            [
                'module_code' => 'bpjs',
                'module_name' => 'BPJS TK & Kesehatan',
                'category' => 'Payroll & Finance',
                'is_enabled' => true,
                'description' => 'Kalkulasi kontribusi perusahaan dan pekerja untuk JKK, JKM, JHT, JP, dan BPJS Kesehatan sesuai pagu upah.',
                'config' => ['include_jht' => true, 'include_jp' => true, 'include_kes' => true],
            ],
            [
                'module_code' => 'thr',
                'module_name' => 'Tunjangan Hari Raya (THR)',
                'category' => 'Payroll & Finance',
                'is_enabled' => true,
                'description' => 'Kalkulasi THR Keagamaan sesuai masa kerja karyawan (Permenaker No. 6/2016).',
                'config' => ['prorate_under_1_year' => true],
            ],
            [
                'module_code' => 'reimbursement',
                'module_name' => 'Klaim Reimbursement',
                'category' => 'Payroll & Finance',
                'is_enabled' => true,
                'description' => 'Pengajuan dan pencairan klaim medis, transportasi, kacamata, dan perjalanan dinas.',
                'config' => ['require_receipt' => true, 'max_submission_days' => 30],
            ],
            [
                'module_code' => 'loan',
                'module_name' => 'Pinjaman / Kasbon Karyawan',
                'category' => 'Payroll & Finance',
                'is_enabled' => false,
                'description' => 'Pencatatan kasbon atau pinjaman karyawan dengan cicilan otomatis via pemotongan gaji bulanan.',
                'config' => ['max_loan_multiple' => 2, 'max_tenor_months' => 12],
            ],
            [
                'module_code' => 'recruitment',
                'module_name' => 'Rekrutmen & ATS',
                'category' => 'Talent Acquisition',
                'is_enabled' => false,
                'description' => 'Manajemen lowongan kerja, pelamar, jadwal wawancara, dan pipeline rekrutmen.',
                'config' => ['public_portal' => false],
            ],
            [
                'module_code' => 'performance',
                'module_name' => 'Kinerja & KPI',
                'category' => 'Talent Management',
                'is_enabled' => false,
                'description' => 'Penilaian kinerja periodik, KPI (Key Performance Indicator), dan evaluasi berkala.',
                'config' => ['review_frequency' => 'quarterly'],
            ],
            [
                'module_code' => 'training',
                'module_name' => 'Pelatihan & Training',
                'category' => 'Talent Management',
                'is_enabled' => false,
                'description' => 'Pencatatan program pengembangan kapasitas, sertifikasi keahlian, dan anggaran pelatihan.',
                'config' => ['track_certification_expiry' => true],
            ],
            [
                'module_code' => 'documents',
                'module_name' => 'Dokumen Karyawan & Perusahaan',
                'category' => 'Employee Administration',
                'is_enabled' => false,
                'description' => 'Repositori berkas digital seperti KTP, NPWP, Ijazah, Peraturan Perusahaan, dan template surat.',
                'config' => ['max_file_size_mb' => 5],
            ],
            [
                'module_code' => 'asset_assignment',
                'module_name' => 'Aset & Inventaris Karyawan',
                'category' => 'Employee Administration',
                'is_enabled' => false,
                'description' => 'Pencatatan serah terima inventaris perusahaan seperti laptop, kendaraan operasional, dan ID card.',
                'config' => ['require_return_on_offboarding' => true],
            ],
            [
                'module_code' => 'announcements',
                'module_name' => 'Pengumuman Internal',
                'category' => 'Communication',
                'is_enabled' => false,
                'description' => 'Broadcasting informasi manajemen, memo direksi, dan event penting ke portal web dan ESS mobile.',
                'config' => ['popup_on_login' => false],
            ],
            [
                'module_code' => 'discipline',
                'module_name' => 'Disiplin & Surat Peringatan (SP)',
                'category' => 'Employee Relations',
                'is_enabled' => false,
                'description' => 'Pencatatan pelanggaran tata tertib dan penerbitan SP 1, SP 2, SP 3 sesuai ketentuan UU Ketenagakerjaan.',
                'config' => ['sp_validity_months' => 6],
            ],
        ];

        foreach ($modules as $mod) {
            ModuleFeature::updateOrCreate(
                ['module_code' => $mod['module_code']],
                $mod
            );
        }

        // Permissions for Module Features
        $permissiongroup = Permission_group::firstOrCreate(['name' => 'Module Features']);
        $perms = [
            'module_features.index',
            'module_features.edit',
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
