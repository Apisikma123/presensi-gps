<?php

namespace Database\Seeders;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\ModuleFeature;
use App\Models\OnboardingTemplate;
use App\Models\OnboardingTemplateTask;
use App\Models\RecruitmentCandidate;
use App\Models\RecruitmentVacancy;
use App\Services\RecruitmentService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Phase9RecruitmentAndOnboardingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Enable Module Features
        $modules = [
            [
                'module_code' => 'recruitment',
                'module_name' => 'Modul Rekrutmen & Pelamar',
                'description' => 'Manajemen lowongan kerja, pipeline pelamar, tahapan seleksi, dan hiring karyawan baru.',
                'category' => 'CORE_HR',
                'is_enabled' => true,
            ],
            [
                'module_code' => 'onboarding',
                'module_name' => 'Modul Onboarding & Exit Clearance',
                'description' => 'Daftar periksa (checklist) orientasi karyawan baru dan prosedur clearance pengunduran diri.',
                'category' => 'CORE_HR',
                'is_enabled' => true,
            ],
        ];

        foreach ($modules as $m) {
            ModuleFeature::updateOrCreate(
                ['module_code' => $m['module_code']],
                $m
            );
        }

        // 2. Seed Permissions & Permission Groups
        $permissionGroups = [
            'Rekrutmen' => [
                'recruitment.index',
                'recruitment.create',
                'recruitment.edit',
                'recruitment.delete',
                'recruitment.hire',
            ],
            'Onboarding' => [
                'onboarding.index',
                'onboarding.create',
                'onboarding.edit',
                'onboarding.complete',
            ],
            'Offboarding' => [
                'offboarding.clearance',
                'offboarding.settlement',
            ],
        ];

        $roles = Role::whereIn('name', ['super admin', 'master admin'])->get();

        foreach ($permissionGroups as $groupName => $perms) {
            $group = \App\Models\Permission_group::firstOrCreate(['name' => $groupName]);

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

        // 3. Seed Standard Onboarding Templates
        $deptMkt = Departemen::first()->kode_dept ?? 'MKT';

        $templateUmum = OnboardingTemplate::updateOrCreate(
            ['name' => 'Orientasi Karyawan Baru (Umum)'],
            [
                'description' => 'Prosedur onboarding standar 30 hari pertama untuk staf kantor dan manajemen.',
                'kode_dept' => null,
                'is_active' => true,
            ]
        );

        $tasksUmum = [
            ['task_name' => 'Penyerahan Salinan KTP, NPWP, KK & Buku Tabungan Bank', 'category' => 'DOCUMENT', 'day_offset' => 1, 'sort_order' => 1],
            ['task_name' => 'Pembuatan Akun Email Perusahaan & Kredensial Sistem Presensi', 'category' => 'IT_ACCESS', 'day_offset' => 1, 'sort_order' => 2],
            ['task_name' => 'Orientasi Kantor, Fasilitas Kerja & Perkenalan Tim', 'category' => 'HR_BRIEFING', 'day_offset' => 1, 'sort_order' => 3],
            ['task_name' => 'Briefing Peraturan Perusahaan, Jam Kerja & Kebijakan Cuti', 'category' => 'HR_BRIEFING', 'day_offset' => 2, 'sort_order' => 4],
            ['task_name' => 'Serah Terima Laptop Kerja, Akses Kartu & Inventaris', 'category' => 'ASSET', 'day_offset' => 3, 'sort_order' => 5],
            ['task_name' => 'Pelatihan SOP Departemen & Penjelasan Key Performance Indicators (KPI)', 'category' => 'TRAINING', 'day_offset' => 7, 'sort_order' => 6],
            ['task_name' => 'Check-in 30 Hari & Evaluasi Awal Masa Percobaan', 'category' => 'TRAINING', 'day_offset' => 30, 'sort_order' => 7],
        ];

        foreach ($tasksUmum as $t) {
            OnboardingTemplateTask::updateOrCreate(
                [
                    'onboarding_template_id' => $templateUmum->id,
                    'task_name' => $t['task_name'],
                ],
                $t
            );
        }

        $templateOps = OnboardingTemplate::updateOrCreate(
            ['name' => 'Orientasi Tim Operasional & Lapangan'],
            [
                'description' => 'Prosedur onboarding tim operasional, barista, dan staf gerai/cabang.',
                'kode_dept' => $deptMkt,
                'is_active' => true,
            ]
        );

        $tasksOps = [
            ['task_name' => 'Pemeriksaan Berkas Identitas & Surat Keterangan Sehat', 'category' => 'DOCUMENT', 'day_offset' => 1, 'sort_order' => 1],
            ['task_name' => 'Penyerahan Seragam Kerja & Kartu Tanda Pengenal', 'category' => 'ASSET', 'day_offset' => 1, 'sort_order' => 2],
            ['task_name' => 'Pelatihan Standar Higienitas, Sanitasi & Keselamatan Kerja (K3)', 'category' => 'TRAINING', 'day_offset' => 2, 'sort_order' => 3],
            ['task_name' => 'Simulasi SOP Pelayanan & Operasional POS Kasir', 'category' => 'TRAINING', 'day_offset' => 3, 'sort_order' => 4],
            ['task_name' => 'Uji Kecakapan Operasional Lapangan 14 Hari', 'category' => 'TRAINING', 'day_offset' => 14, 'sort_order' => 5],
        ];

        foreach ($tasksOps as $t) {
            OnboardingTemplateTask::updateOrCreate(
                [
                    'onboarding_template_id' => $templateOps->id,
                    'task_name' => $t['task_name'],
                ],
                $t
            );
        }

        // 4. Seed Sample Vacancies & Candidates if empty
        if (RecruitmentVacancy::count() === 0) {
            $cabang = Cabang::first()->kode_cabang ?? 'PST';

            $vac1 = RecruitmentVacancy::create([
                'vacancy_code' => 'VAC-' . date('Ym') . '-0001',
                'title' => 'Talent Acquisition & HR Officer',
                'kode_dept' => $deptMkt,
                'kode_cabang' => $cabang,
                'employment_type' => 'PKWTT',
                'quota' => 1,
                'min_experience_years' => 2,
                'salary_min' => 6000000,
                'salary_max' => 8500000,
                'description' => 'Bertanggung jawab atas proses rekrutmen end-to-end, onboarding karyawan baru, dan hubungan industrial perusahaan.',
                'requirements' => 'Pendidikan S1 Psikologi/Hukum/Manajemen SDM. Berpengalaman min 2 tahun di bidang HR generalist.',
                'deadline' => Carbon::now()->addDays(20),
                'status' => 'OPEN',
            ]);

            $vac2 = RecruitmentVacancy::create([
                'vacancy_code' => 'VAC-' . date('Ym') . '-0002',
                'title' => 'Operational Staff / Barista',
                'kode_dept' => $deptMkt,
                'kode_cabang' => $cabang,
                'employment_type' => 'PKWT',
                'quota' => 3,
                'min_experience_years' => 1,
                'salary_min' => 4800000,
                'salary_max' => 5500000,
                'description' => 'Melayani pelanggan, mengoperasikan mesin dan menjaga standar kualitas kebersihan store.',
                'requirements' => 'Pria/Wanita, usia maks 28 tahun, ramah, jujur, mampu bekerja shift & akhir pekan.',
                'deadline' => Carbon::now()->addDays(15),
                'status' => 'OPEN',
            ]);

            RecruitmentCandidate::create([
                'candidate_code' => 'CND-' . date('Ym') . '-0001',
                'recruitment_vacancy_id' => $vac1->id,
                'name' => 'Dimas Prasetyo, S.Psi',
                'email' => 'dimas.prasetyo@example.com',
                'phone' => '081299887766',
                'gender' => 'L',
                'expected_salary' => 7000000,
                'stage' => 'INTERVIEW',
                'interview_scheduled_at' => Carbon::now()->addDays(2)->setHour(10)->setMinute(0),
                'interview_notes' => 'Kandidat memiliki pengalaman 3 tahun di retail HR. Komunikatif dan menguasai UU Ketenagakerjaan.',
            ]);

            RecruitmentCandidate::create([
                'candidate_code' => 'CND-' . date('Ym') . '-0002',
                'recruitment_vacancy_id' => $vac2->id,
                'name' => 'Siti Rahmawati',
                'email' => 'siti.rahmawati@example.com',
                'phone' => '081344556677',
                'gender' => 'P',
                'expected_salary' => 5000000,
                'stage' => 'OFFERING',
                'offered_salary' => 5200000,
                'notes' => 'Portofolio latte art sangat baik, lulus tes kebersihan store.',
            ]);
        }
    }
}
