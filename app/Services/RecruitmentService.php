<?php

namespace App\Services;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Karyawan;
use App\Models\RecruitmentCandidate;
use App\Models\RecruitmentVacancy;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RecruitmentService
{
    protected OnboardingService $onboardingService;

    public function __construct(OnboardingService $onboardingService)
    {
        $this->onboardingService = $onboardingService;
    }

    /**
     * Generate sequential vacancy code: VAC-YYYYMM-XXXX
     */
    public function generateVacancyCode(): string
    {
        $prefix = 'VAC-' . date('Ym') . '-';
        $last = RecruitmentVacancy::where('vacancy_code', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->value('vacancy_code');

        $nextNumber = 1;
        if ($last && preg_match('/-(\d{4})$/', $last, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }

        return $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create job vacancy
     */
    public function createVacancy(array $data, ?int $userId = null): RecruitmentVacancy
    {
        return RecruitmentVacancy::create([
            'vacancy_code' => $data['vacancy_code'] ?? $this->generateVacancyCode(),
            'title' => $data['title'],
            'kode_dept' => $data['kode_dept'] ?? null,
            'kode_cabang' => $data['kode_cabang'] ?? null,
            'employment_type' => $data['employment_type'] ?? 'PKWT',
            'quota' => $data['quota'] ?? 1,
            'min_experience_years' => $data['min_experience_years'] ?? 0,
            'salary_min' => $data['salary_min'] ?? null,
            'salary_max' => $data['salary_max'] ?? null,
            'description' => $data['description'] ?? null,
            'requirements' => $data['requirements'] ?? null,
            'deadline' => $data['deadline'] ?? null,
            'status' => $data['status'] ?? 'OPEN',
            'created_by' => $userId,
        ]);
    }

    /**
     * Generate sequential candidate code: CND-YYYYMM-XXXX
     */
    public function generateCandidateCode(): string
    {
        $prefix = 'CND-' . date('Ym') . '-';
        $last = RecruitmentCandidate::where('candidate_code', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->value('candidate_code');

        $nextNumber = 1;
        if ($last && preg_match('/-(\d{4})$/', $last, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }

        return $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Register new candidate
     */
    public function registerCandidate(int $vacancyId, array $data): RecruitmentCandidate
    {
        return RecruitmentCandidate::create([
            'candidate_code' => $data['candidate_code'] ?? $this->generateCandidateCode(),
            'recruitment_vacancy_id' => $vacancyId,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'gender' => $data['gender'] ?? 'L',
            'expected_salary' => $data['expected_salary'] ?? null,
            'resume_file' => $data['resume_file'] ?? null,
            'portfolio_url' => $data['portfolio_url'] ?? null,
            'notes' => $data['notes'] ?? null,
            'stage' => 'APPLIED',
        ]);
    }

    /**
     * Advance or update candidate pipeline stage
     */
    public function updateCandidateStage(int $candidateId, string $stage, array $extraData = []): RecruitmentCandidate
    {
        $candidate = RecruitmentCandidate::findOrFail($candidateId);

        $validStages = array_keys(RecruitmentCandidate::STAGES);
        if (!in_array($stage, $validStages)) {
            throw new \InvalidArgumentException("Tahapan rekrutmen tidak valid: {$stage}");
        }

        $updateData = ['stage' => $stage];

        if ($stage === 'INTERVIEW') {
            if (isset($extraData['interview_scheduled_at'])) {
                $updateData['interview_scheduled_at'] = $extraData['interview_scheduled_at'];
            }
            if (isset($extraData['interview_notes'])) {
                $updateData['interview_notes'] = $extraData['interview_notes'];
            }
        } elseif ($stage === 'OFFERING') {
            if (isset($extraData['offered_salary'])) {
                $updateData['offered_salary'] = $extraData['offered_salary'];
            }
        }

        $candidate->update($updateData);
        return $candidate;
    }

    /**
     * Convert candidate to employee (Hire)
     */
    public function hireCandidateToEmployee(int $candidateId, array $employeeData, ?int $onboardingTemplateId = null): array
    {
        return DB::transaction(function () use ($candidateId, $employeeData, $onboardingTemplateId) {
            $candidate = RecruitmentCandidate::with('vacancy')->findOrFail($candidateId);

            // 1. Determine NIK
            $nik = $employeeData['nik'] ?? null;
            if (!$nik) {
                // Auto-generate NIK if not specified: 5-digit number
                $maxNik = Karyawan::max(DB::raw('CAST(nik AS UNSIGNED)')) ?? 1000;
                $nik = (string) ($maxNik + 1);
            }

            // Ensure unique NIK
            if (Karyawan::where('nik', $nik)->exists()) {
                throw new \RuntimeException("NIK {$nik} sudah terdaftar dalam sistem.");
            }

            // 2. Create Karyawan
            $karyawan = Karyawan::create([
                'nik' => $nik,
                'nama_karyawan' => $candidate->name,
                'email' => $candidate->email,
                'no_hp' => $candidate->phone,
                'jenis_kelamin' => $candidate->gender,
                'kode_dept' => $employeeData['kode_dept'] ?? $candidate->vacancy->kode_dept ?? 'MKT',
                'kode_cabang' => $employeeData['kode_cabang'] ?? $candidate->vacancy->kode_cabang ?? 'PST',
                'kode_jabatan' => $employeeData['kode_jabatan'] ?? 'STF',
                'status_karyawan' => $employeeData['status_karyawan'] ?? 'K', // Kontrak
                'employment_type' => $employeeData['employment_type'] ?? $candidate->vacancy->employment_type ?? 'PKWT',
                'tanggal_masuk' => $employeeData['tanggal_masuk'] ?? Carbon::today(),
                'status_aktif_karyawan' => '1',
            ]);

            // 3. Create User Account if not exists
            $user = User::where('email', $candidate->email)->first();
            if (!$user) {
                $user = User::create([
                    'name' => $candidate->name,
                    'email' => $candidate->email,
                    'username' => $nik,
                    'password' => Hash::make($employeeData['password'] ?? 'password123'),
                ]);

                // Assign Karyawan role if role exists
                try {
                    $user->assignRole('karyawan');
                } catch (\Throwable $e) {
                    // Ignore if role not defined in spatie
                }
            }

            // 4. Update Candidate Stage to HIRED
            $candidate->update([
                'stage' => 'HIRED',
                'hired_nik' => $nik,
                'hired_at' => Carbon::now(),
            ]);

            // 5. Initialize Onboarding if template requested
            $onboarding = null;
            if ($onboardingTemplateId) {
                $onboarding = $this->onboardingService->assignOnboarding(
                    $nik,
                    $onboardingTemplateId,
                    Carbon::parse($employeeData['tanggal_masuk'] ?? Carbon::today()),
                    $employeeData['mentor_nik'] ?? null
                );
            }

            return [
                'candidate' => $candidate,
                'karyawan' => $karyawan,
                'user' => $user,
                'onboarding' => $onboarding,
            ];
        });
    }

    /**
     * Get recruitment pipeline metrics
     */
    public function getPipelineStats(): array
    {
        return [
            'total_vacancies' => RecruitmentVacancy::count(),
            'open_vacancies' => RecruitmentVacancy::where('status', 'OPEN')->count(),
            'total_candidates' => RecruitmentCandidate::count(),
            'interview_candidates' => RecruitmentCandidate::where('stage', 'INTERVIEW')->count(),
            'offering_candidates' => RecruitmentCandidate::where('stage', 'OFFERING')->count(),
            'hired_candidates' => RecruitmentCandidate::where('stage', 'HIRED')->count(),
        ];
    }
}
