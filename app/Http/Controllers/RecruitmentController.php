<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\OnboardingTemplate;
use App\Models\RecruitmentCandidate;
use App\Models\RecruitmentVacancy;
use App\Services\RecruitmentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RecruitmentController extends Controller
{
    protected RecruitmentService $recruitmentService;

    public function __construct(RecruitmentService $recruitmentService)
    {
        $this->recruitmentService = $recruitmentService;
    }

    /**
     * Vacancy overview & list
     */
    public function index(Request $request)
    {
        $query = RecruitmentVacancy::with(['department', 'branch', 'candidates']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('vacancy_code', 'like', "%{$s}%");
            });
        }

        $vacancies = $query->orderBy('created_at', 'desc')->paginate(10);
        $stats = $this->recruitmentService->getPipelineStats();

        return view('kepegawaian.recruitment.index', compact('vacancies', 'stats'));
    }

    /**
     * Create vacancy form
     */
    public function create()
    {
        $departemens = Departemen::orderBy('nama_dept')->get();
        $cabangs = Cabang::orderBy('nama_cabang')->get();
        $newCode = $this->recruitmentService->generateVacancyCode();

        return view('kepegawaian.recruitment.create', compact('departemens', 'cabangs', 'newCode'));
    }

    /**
     * Store new vacancy
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'kode_dept' => 'nullable|string|exists:departemen,kode_dept',
            'kode_cabang' => 'nullable|string|exists:cabang,kode_cabang',
            'employment_type' => 'required|string|in:PKWT,PKWTT,PROBATION,INTERNSHIP,FREELANCE',
            'quota' => 'required|integer|min:1',
            'min_experience_years' => 'nullable|integer|min:0',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|gte:salary_min',
            'deadline' => 'nullable|date',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
        ]);

        $this->recruitmentService->createVacancy($request->all(), Auth::id());

        return redirect()->route('recruitment.index')->with('success', 'Lowongan kerja berhasil dipublikasikan.');
    }

    /**
     * Show vacancy details and applicant pipeline
     */
    public function show($id)
    {
        $vacancy = RecruitmentVacancy::with(['department', 'branch', 'candidates'])->findOrFail($id);

        $candidatesByStage = [
            'APPLIED' => $vacancy->candidates->where('stage', 'APPLIED'),
            'SCREENING' => $vacancy->candidates->where('stage', 'SCREENING'),
            'INTERVIEW' => $vacancy->candidates->where('stage', 'INTERVIEW'),
            'OFFERING' => $vacancy->candidates->where('stage', 'OFFERING'),
            'HIRED' => $vacancy->candidates->where('stage', 'HIRED'),
            'REJECTED' => $vacancy->candidates->where('stage', 'REJECTED'),
        ];

        return view('kepegawaian.recruitment.show', compact('vacancy', 'candidatesByStage'));
    }

    /**
     * Store a candidate application for a vacancy
     */
    public function storeCandidate(Request $request, $vacancyId)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:50',
            'gender' => 'required|in:L,P',
            'expected_salary' => 'nullable|numeric|min:0',
            'resume_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'portfolio_url' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
        ]);

        $resumePath = null;
        if ($request->hasFile('resume_file')) {
            $resumePath = $request->file('resume_file')->store('resumes', 'public');
        }

        $data = $request->all();
        $data['resume_file'] = $resumePath;

        $this->recruitmentService->registerCandidate((int) $vacancyId, $data);

        return redirect()->route('recruitment.show', $vacancyId)->with('success', 'Kandidat pelamar berhasil didaftarkan.');
    }

    /**
     * Update candidate stage (e.g. Schedule interview, set offer, reject)
     */
    public function updateCandidateStage(Request $request, $candidateId)
    {
        $request->validate([
            'stage' => 'required|string|in:APPLIED,SCREENING,INTERVIEW,OFFERING,HIRED,REJECTED',
            'interview_scheduled_at' => 'nullable|date',
            'interview_notes' => 'nullable|string',
            'offered_salary' => 'nullable|numeric|min:0',
        ]);

        $candidate = $this->recruitmentService->updateCandidateStage(
            (int) $candidateId,
            $request->stage,
            $request->all()
        );

        return redirect()->route('recruitment.show', $candidate->recruitment_vacancy_id)
            ->with('success', "Status kandidat {$candidate->name} diperbarui menjadi {$candidate->stage_label}.");
    }

    /**
     * Show hire form to convert candidate to employee
     */
    public function hireForm($candidateId)
    {
        $candidate = RecruitmentCandidate::with('vacancy')->findOrFail($candidateId);
        $departemens = Departemen::orderBy('nama_dept')->get();
        $cabangs = Cabang::orderBy('nama_cabang')->get();
        $jabatans = Jabatan::orderBy('nama_jabatan')->get();
        $templates = OnboardingTemplate::where('is_active', true)->orderBy('name')->get();

        return view('kepegawaian.recruitment.hire', compact('candidate', 'departemens', 'cabangs', 'jabatans', 'templates'));
    }

    /**
     * Process hiring and convert candidate into official employee
     */
    public function hire(Request $request, $candidateId)
    {
        $request->validate([
            'nik' => 'nullable|string|max:9|unique:karyawan,nik',
            'kode_dept' => 'required|string|exists:departemen,kode_dept',
            'kode_cabang' => 'required|string|exists:cabang,kode_cabang',
            'kode_jabatan' => 'required|string|exists:jabatan,kode_jabatan',
            'employment_type' => 'required|string|in:PKWT,PKWTT,PROBATION,INTERNSHIP',
            'tanggal_masuk' => 'required|date',
            'onboarding_template_id' => 'nullable|integer|exists:onboarding_templates,id',
        ]);

        $result = $this->recruitmentService->hireCandidateToEmployee(
            (int) $candidateId,
            $request->all(),
            $request->onboarding_template_id ? (int) $request->onboarding_template_id : null
        );

        $nik = $result['karyawan']->nik;
        return redirect()->route('karyawan.index')
            ->with('success', "Kandidat {$result['candidate']->name} berhasil diangkat menjadi Karyawan Resmi dengan NIK {$nik}.");
    }

    /**
     * Close or delete vacancy
     */
    public function destroy($id)
    {
        $vacancy = RecruitmentVacancy::findOrFail($id);
        $vacancy->delete();

        return redirect()->route('recruitment.index')->with('success', 'Lowongan kerja berhasil dihapus.');
    }
}
