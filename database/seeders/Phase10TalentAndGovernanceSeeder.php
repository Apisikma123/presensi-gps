<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\CompanyPolicy;
use App\Models\EmployeeAsset;
use App\Models\EmployeeDocument;
use App\Models\EmployeeIncident;
use App\Models\EmployeeTraining;
use App\Models\EmployeeWarning;
use App\Models\Karyawan;
use App\Models\ModuleFeature;
use App\Models\PerformanceKpi;
use App\Models\PerformanceReview;
use App\Models\Permission_group;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Phase10TalentAndGovernanceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Enable Module Features
        $modules = [
            [
                'module_code' => 'performance',
                'module_name' => 'Modul Penilaian Kinerja & KPI',
                'description' => 'Evaluasi berkala, target KPI per divisi, penilaian atasan, dan grading kinerja karyawan.',
                'category' => 'TALENT',
                'is_enabled' => true,
            ],
            [
                'module_code' => 'training',
                'module_name' => 'Modul Pelatihan & Sertifikasi',
                'description' => 'Pencatatan kursus, sertifikasi profesi, jam pelatihan, dan pengembangan kompetensi SDM.',
                'category' => 'TALENT',
                'is_enabled' => true,
            ],
            [
                'module_code' => 'discipline',
                'module_name' => 'Modul Disiplin & Surat Peringatan (SP)',
                'description' => 'Penerbitan surat peringatan (SP 1, SP 2, SP 3), skorsing, dan penegakan tata tertib perusahaan.',
                'category' => 'GOVERNANCE',
                'is_enabled' => true,
            ],
            [
                'module_code' => 'asset',
                'module_name' => 'Modul Aset & Fasilitas Kerja',
                'description' => 'Peminjaman dan pengembalian inventaris kantor, laptop, kendaraan dinas, dan kartu akses.',
                'category' => 'OPERATIONAL',
                'is_enabled' => true,
            ],
            [
                'module_code' => 'announcement',
                'module_name' => 'Modul Pengumuman Perusahaan',
                'description' => 'Penyebaran pengumuman internal, surat edaran direksi, dan informasi hari libur.',
                'category' => 'COMMUNICATION',
                'is_enabled' => true,
            ],
        ];

        foreach ($modules as $m) {
            ModuleFeature::updateOrCreate(['module_code' => $m['module_code']], $m);
        }

        // 2. Seed Permissions
        $permissionGroups = [
            'Kinerja & KPI' => [
                'performance.index',
                'performance.create',
                'performance.approve',
            ],
            'Pelatihan SDM' => [
                'training.index',
                'training.create',
                'training.delete',
            ],
            'Disiplin & SP' => [
                'warning.index',
                'warning.create',
                'warning.delete',
            ],
            'Brankas Dokumen' => [
                'document.index',
                'document.upload',
                'document.delete',
            ],
            'Aset & Fasilitas' => [
                'asset.index',
                'asset.create',
                'asset.return',
            ],
            'Pengumuman Internal' => [
                'announcement.index',
                'announcement.create',
                'announcement.delete',
            ],
            'Kasus & Insiden' => [
                'incident.index',
                'incident.create',
                'incident.resolve',
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

        // 3. Seed Standard Company Policies & SOPs
        $policies = [
            [
                'policy_code' => 'PP-2026',
                'title' => 'Buku Peraturan Perusahaan PRESENCE (Periode 2026 - 2028)',
                'category' => 'PERATURAN_PERUSAHAAN',
                'effective_date' => Carbon::create(2026, 1, 1),
                'version' => '1.0',
                'description' => 'Pedoman umum hak dan kewajiban pekerja, jam kerja, tata tertib, dan sanksi disiplin resmi yang telah disahkan Disnaker.',
                'is_active' => true,
            ],
            [
                'policy_code' => 'SOP-HR-001',
                'title' => 'SOP Pengajuan Cuti, Izin Sakit & Dispensasi Kehadiran',
                'category' => 'SOP_OPERASIONAL',
                'effective_date' => Carbon::create(2026, 1, 1),
                'version' => '2.1',
                'description' => 'Prosedur operasional standar tata cara pengajuan cuti tahunan, cuti khusus, dan izin sakit dengan surat dokter.',
                'is_active' => true,
            ],
            [
                'policy_code' => 'SOP-IT-002',
                'title' => 'SOP Keamanan Siber & Penggunaan Perangkat Laptop Perusahaan',
                'category' => 'CODE_OF_CONDUCT',
                'effective_date' => Carbon::create(2026, 2, 1),
                'version' => '1.2',
                'description' => 'Standar perlindungan data pribadi, kerahasiaan password, dan larangan instalasi software bajakan pada aset kantor.',
                'is_active' => true,
            ],
        ];

        foreach ($policies as $p) {
            CompanyPolicy::updateOrCreate(['policy_code' => $p['policy_code']], $p);
        }

        // 4. Seed Sample Governance Data if empty
        $emp = Karyawan::where('status_aktif_karyawan', '1')->first();
        if ($emp && PerformanceReview::count() === 0) {
            $reviewer = Karyawan::where('status_aktif_karyawan', '1')->where('nik', '!=', $emp->nik)->first() ?? $emp;

            // Sample Review
            $review = PerformanceReview::create([
                'review_code' => 'PRF-' . date('Ym') . '-0001',
                'nik' => $emp->nik,
                'reviewer_nik' => $reviewer->nik,
                'period_title' => 'Evaluasi Kinerja Q3 2026',
                'start_date' => Carbon::create(2026, 7, 1),
                'end_date' => Carbon::create(2026, 9, 30),
                'overall_score' => 88.50,
                'rating_grade' => 'MEETS',
                'strengths' => 'Disiplin waktu sangat baik, komunikasi tim lancar, selalu mencapai target harian.',
                'areas_for_improvement' => 'Ditingkatkan lagi inisiatif dalam penyelesaian kendala teknis mandiri.',
                'goals_next_period' => 'Mengikuti pelatihan hospitality lanjutan dan memimpin proyek perbaikan gerai.',
                'status' => 'APPROVED',
            ]);

            PerformanceKpi::create([
                'performance_review_id' => $review->id,
                'kpi_name' => 'Tingkat Kehadiran & Ketepatan Waktu (Presensi)',
                'weight' => 30.00,
                'target_value' => '>= 98%',
                'actual_value' => '99.2%',
                'score' => 95.00,
                'weighted_score' => 28.50,
            ]);

            PerformanceKpi::create([
                'performance_review_id' => $review->id,
                'kpi_name' => 'Pencapaian Target Output & Kualitas Kerja',
                'weight' => 40.00,
                'target_value' => '100 Unit/Bulan',
                'actual_value' => '92 Unit/Bulan',
                'score' => 85.00,
                'weighted_score' => 34.00,
            ]);

            PerformanceKpi::create([
                'performance_review_id' => $review->id,
                'kpi_name' => 'Kerjasama Tim & Inisiatif',
                'weight' => 30.00,
                'target_value' => 'Rating 4/5',
                'actual_value' => 'Rating 4.3/5',
                'score' => 86.67,
                'weighted_score' => 26.00,
            ]);

            // Sample Training
            EmployeeTraining::create([
                'training_code' => 'TRN-' . date('Ym') . '-0001',
                'nik' => $emp->nik,
                'title' => 'Pelatihan Service Excellence & Hospitality Barista',
                'provider' => 'Indonesian Coffee Academy',
                'start_date' => Carbon::now()->subMonths(1),
                'end_date' => Carbon::now()->subMonths(1)->addDays(2),
                'duration_hours' => 16,
                'cost' => 1500000,
                'certificate_number' => 'CERT/ICA/2026/0881',
                'status' => 'COMPLETED',
                'score' => 92.00,
            ]);

            // Sample Asset
            EmployeeAsset::create([
                'asset_code' => 'AST-0001',
                'nik' => $emp->nik,
                'name' => 'Laptop Lenovo ThinkPad E14 Gen 4',
                'category' => 'HARDWARE',
                'serial_number' => 'PF-39X1882',
                'assigned_date' => Carbon::now()->subMonths(3),
                'condition' => 'EXCELLENT',
                'notes' => 'Disertai tas laptop dan charger original 65W',
                'status' => 'ASSIGNED',
            ]);

            // Sample Announcement
            Announcement::create([
                'title' => 'Pengumuman: Penyesuaian Jadwal Operasional & Libur Nasional',
                'content' => 'Diberitahukan kepada seluruh karyawan bahwa sehubungan dengan Libur Nasional, seluruh kantor cabang akan beroperasi dengan jadwal piket roster yang telah ditentukan oleh masing-masing Store Manager.',
                'category' => 'HOLIDAY',
                'published_at' => Carbon::now(),
                'is_pinned' => true,
            ]);
        }
    }
}
