<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\PerformanceReview;
use App\Services\PerformanceReviewService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerformanceReviewController extends Controller
{
    protected PerformanceReviewService $reviewService;

    public function __construct(PerformanceReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * Performance evaluation list
     */
    public function index(Request $request)
    {
        $query = PerformanceReview::with(['karyawan.departemen', 'reviewer']);

        if ($request->filled('rating_grade')) {
            $query->where('rating_grade', $request->rating_grade);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nik', 'like', "%{$s}%")
                  ->orWhere('review_code', 'like', "%{$s}%")
                  ->orWhereHas('karyawan', fn($sub) => $sub->where('nama_karyawan', 'like', "%{$s}%"));
            });
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(10);
        $stats = $this->reviewService->getStats();

        return view('kepegawaian.performance.index', compact('reviews', 'stats'));
    }

    /**
     * Create review form
     */
    public function create()
    {
        $karyawans = Karyawan::where('status_aktif_karyawan', '1')->orderBy('nama_karyawan')->get();
        $newCode = $this->reviewService->generateReviewCode();

        return view('kepegawaian.performance.create', compact('karyawans', 'newCode'));
    }

    /**
     * Store performance review with KPIs
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|exists:karyawan,nik',
            'reviewer_nik' => 'nullable|string|exists:karyawan,nik',
            'period_title' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'kpis' => 'required|array|min:1',
            'kpis.*.kpi_name' => 'required|string|max:150',
            'kpis.*.weight' => 'required|numeric|min:1',
            'kpis.*.score' => 'required|numeric|min:0|max:100',
        ]);

        $this->reviewService->createReview($request->all(), $request->kpis);

        return redirect()->route('performance.index')->with('success', 'Penilaian kinerja & KPI berhasil disimpan.');
    }

    /**
     * Show detailed appraisal score card
     */
    public function show($id)
    {
        $review = PerformanceReview::with(['karyawan.departemen', 'karyawan.cabang', 'reviewer', 'kpis'])->findOrFail($id);

        return view('kepegawaian.performance.show', compact('review'));
    }

    /**
     * Approve review
     */
    public function approve($id)
    {
        $this->reviewService->approveReview((int) $id);

        return redirect()->back()->with('success', 'Penilaian kinerja berhasil disetujui (Approved).');
    }

    /**
     * Delete review
     */
    public function destroy($id)
    {
        $review = PerformanceReview::findOrFail($id);
        $review->delete();

        return redirect()->route('performance.index')->with('success', 'Data penilaian kinerja berhasil dihapus.');
    }
}
