<?php

namespace App\Services;

use App\Models\EmployeeOnboarding;
use App\Models\EmployeeOnboardingTask;
use App\Models\Karyawan;
use App\Models\OnboardingTemplate;
use App\Models\OnboardingTemplateTask;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OnboardingService
{
    /**
     * Create onboarding template with default tasks
     */
    public function createTemplate(array $data, array $tasks = []): OnboardingTemplate
    {
        return DB::transaction(function () use ($data, $tasks) {
            $template = OnboardingTemplate::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'kode_dept' => $data['kode_dept'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            foreach ($tasks as $index => $t) {
                OnboardingTemplateTask::create([
                    'onboarding_template_id' => $template->id,
                    'task_name' => $t['task_name'],
                    'category' => $t['category'] ?? 'HR_BRIEFING',
                    'day_offset' => $t['day_offset'] ?? 1,
                    'is_mandatory' => $t['is_mandatory'] ?? true,
                    'sort_order' => $t['sort_order'] ?? ($index + 1),
                ]);
            }

            return $template;
        });
    }

    /**
     * Assign onboarding checklist to employee
     */
    public function assignOnboarding(string $nik, ?int $templateId = null, ?Carbon $startDate = null, ?string $mentorNik = null): EmployeeOnboarding
    {
        return DB::transaction(function () use ($nik, $templateId, $startDate, $mentorNik) {
            $startDate = $startDate ?? Carbon::today();

            // Default 30 days target
            $targetDate = $startDate->copy()->addDays(30);

            $onboarding = EmployeeOnboarding::create([
                'nik' => $nik,
                'onboarding_template_id' => $templateId,
                'start_date' => $startDate,
                'target_completion_date' => $targetDate,
                'status' => 'IN_PROGRESS',
                'mentor_nik' => $mentorNik,
            ]);

            // If template specified, instantiate tasks
            if ($templateId) {
                $template = OnboardingTemplate::with('tasks')->find($templateId);
                if ($template && $template->tasks->isNotEmpty()) {
                    foreach ($template->tasks as $t) {
                        EmployeeOnboardingTask::create([
                            'employee_onboarding_id' => $onboarding->id,
                            'task_name' => $t->task_name,
                            'category' => $t->category,
                            'due_date' => $startDate->copy()->addDays($t->day_offset),
                            'is_completed' => false,
                            'sort_order' => $t->sort_order,
                        ]);
                    }
                }
            } else {
                // Default fallback starter tasks
                $defaultTasks = [
                    ['name' => 'Penyerahan KTP, NPWP & Buku Tabungan', 'cat' => 'DOCUMENT', 'offset' => 1],
                    ['name' => 'Pembuatan Akun Email Perusahaan & Akses Sistem', 'cat' => 'IT_ACCESS', 'offset' => 1],
                    ['name' => 'Pengenalan Rekan Tim & Fasilitas Kerja', 'cat' => 'HR_BRIEFING', 'offset' => 1],
                    ['name' => 'Briefing SOP, Jam Kerja & Peraturan Perusahaan', 'cat' => 'HR_BRIEFING', 'offset' => 3],
                    ['name' => 'Penyerahan Aset Laptop & Kelengkapan Kerja', 'cat' => 'ASSET', 'offset' => 3],
                    ['name' => 'Review Kinerja 30 Hari Pertama', 'cat' => 'TRAINING', 'offset' => 30],
                ];

                foreach ($defaultTasks as $idx => $dt) {
                    EmployeeOnboardingTask::create([
                        'employee_onboarding_id' => $onboarding->id,
                        'task_name' => $dt['name'],
                        'category' => $dt['cat'],
                        'due_date' => $startDate->copy()->addDays($dt['offset']),
                        'is_completed' => false,
                        'sort_order' => $idx + 1,
                    ]);
                }
            }

            return $onboarding;
        });
    }

    /**
     * Toggle or complete onboarding task
     */
    public function toggleTask(int $taskId, ?int $userId = null, bool $completed = true, ?string $notes = null): EmployeeOnboardingTask
    {
        return DB::transaction(function () use ($taskId, $userId, $completed, $notes) {
            $task = EmployeeOnboardingTask::findOrFail($taskId);
            $task->is_completed = $completed;
            $task->completed_at = $completed ? Carbon::now() : null;
            $task->completed_by = $completed ? $userId : null;
            if ($notes !== null) {
                $task->notes = $notes;
            }
            $task->save();

            // Check if all tasks for this onboarding are complete
            $onboarding = $task->onboarding;
            if ($onboarding) {
                $pendingCount = $onboarding->tasks()->where('is_completed', false)->count();
                if ($pendingCount === 0) {
                    $onboarding->status = 'COMPLETED';
                    $onboarding->completed_at = Carbon::now();
                } else {
                    $onboarding->status = 'IN_PROGRESS';
                    $onboarding->completed_at = null;
                }
                $onboarding->save();
            }

            return $task;
        });
    }

    /**
     * Get onboarding overview stats
     */
    public function getStats(): array
    {
        return [
            'total_onboardings' => EmployeeOnboarding::count(),
            'in_progress' => EmployeeOnboarding::where('status', 'IN_PROGRESS')->count(),
            'completed' => EmployeeOnboarding::where('status', 'COMPLETED')->count(),
            'templates_count' => OnboardingTemplate::where('is_active', true)->count(),
        ];
    }
}
