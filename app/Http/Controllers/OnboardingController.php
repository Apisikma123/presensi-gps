<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\EmployeeOnboarding;
use App\Models\EmployeeOnboardingTask;
use App\Models\Karyawan;
use App\Models\OnboardingTemplate;
use App\Services\OnboardingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    protected OnboardingService $onboardingService;

    public function __construct(OnboardingService $onboardingService)
    {
        $this->onboardingService = $onboardingService;
    }

    /**
     * Active employee onboardings overview
     */
    public function index(Request $request)
    {
        $query = EmployeeOnboarding::with(['karyawan.departemen', 'karyawan.cabang', 'template', 'tasks']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nik', 'like', "%{$s}%")
                  ->orWhereHas('karyawan', function ($sub) use ($s) {
                      $sub->where('nama_karyawan', 'like', "%{$s}%");
                  });
            });
        }

        $onboardings = $query->orderBy('created_at', 'desc')->paginate(10);
        $stats = $this->onboardingService->getStats();

        return view('kepegawaian.onboarding.index', compact('onboardings', 'stats'));
    }

    /**
     * Assign onboarding form
     */
    public function create()
    {
        $karyawans = Karyawan::where('status_aktif_karyawan', '1')->orderBy('nama_karyawan')->get();
        $templates = OnboardingTemplate::where('is_active', true)->orderBy('name')->get();

        return view('kepegawaian.onboarding.create', compact('karyawans', 'templates'));
    }

    /**
     * Store new employee onboarding
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|exists:karyawan,nik',
            'onboarding_template_id' => 'nullable|integer|exists:onboarding_templates,id',
            'start_date' => 'required|date',
            'mentor_nik' => 'nullable|string|exists:karyawan,nik',
        ]);

        $this->onboardingService->assignOnboarding(
            $request->nik,
            $request->onboarding_template_id ? (int) $request->onboarding_template_id : null,
            Carbon::parse($request->start_date),
            $request->mentor_nik
        );

        return redirect()->route('onboarding.index')->with('success', 'Program onboarding berhasil ditugaskan ke karyawan.');
    }

    /**
     * Show onboarding detail and interactive checklist
     */
    public function show($id)
    {
        $onboarding = EmployeeOnboarding::with(['karyawan.departemen', 'karyawan.cabang', 'mentor', 'template', 'tasks.completedBy'])->findOrFail($id);

        $tasksByCategory = $onboarding->tasks->groupBy('category');

        return view('kepegawaian.onboarding.show', compact('onboarding', 'tasksByCategory'));
    }

    /**
     * Toggle completion of an onboarding task
     */
    public function toggleTask(Request $request, $taskId)
    {
        $task = EmployeeOnboardingTask::findOrFail($taskId);
        $completed = $request->boolean('is_completed', !$task->is_completed);

        $this->onboardingService->toggleTask(
            (int) $taskId,
            Auth::id(),
            $completed,
            $request->get('notes')
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'is_completed' => $completed,
                'progress' => $task->onboarding->fresh()->progress_percentage,
                'status' => $task->onboarding->fresh()->status,
            ]);
        }

        return redirect()->back()->with('success', 'Status checklist onboarding berhasil diperbarui.');
    }

    /**
     * Template management overview
     */
    public function templates()
    {
        $templates = OnboardingTemplate::with(['department', 'tasks'])->orderBy('created_at', 'desc')->get();
        return view('kepegawaian.onboarding.templates', compact('templates'));
    }

    /**
     * Create template form
     */
    public function createTemplate()
    {
        $departemens = Departemen::orderBy('nama_dept')->get();
        return view('kepegawaian.onboarding.template_create', compact('departemens'));
    }

    /**
     * Store new template with custom tasks
     */
    public function storeTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'kode_dept' => 'nullable|string|exists:departemen,kode_dept',
            'tasks' => 'required|array|min:1',
            'tasks.*.task_name' => 'required|string|max:200',
            'tasks.*.category' => 'required|string|in:DOCUMENT,IT_ACCESS,HR_BRIEFING,TRAINING,ASSET',
            'tasks.*.day_offset' => 'required|integer|min:0',
        ]);

        $this->onboardingService->createTemplate($request->only('name', 'description', 'kode_dept'), $request->tasks);

        return redirect()->route('onboarding.templates')->with('success', 'Template checklist onboarding berhasil disimpan.');
    }

    /**
     * Delete onboarding assignment
     */
    public function destroy($id)
    {
        $onboarding = EmployeeOnboarding::findOrFail($id);
        $onboarding->delete();

        return redirect()->route('onboarding.index')->with('success', 'Program onboarding berhasil dihapus.');
    }
}
