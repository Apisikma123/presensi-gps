<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Lembur;
use App\Models\OvertimePolicy;
use App\Services\OvertimeCalculatorService;
use App\Services\OvertimeWorkflowService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OvertimeController extends Controller
{
    public function __construct(
        protected OvertimeCalculatorService $calculator,
        protected OvertimeWorkflowService $workflow
    ) {
        $this->middleware('permission:overtime.index')->only(['index', 'show']);
        $this->middleware('permission:overtime.create')->only(['create', 'store', 'calculatePreview']);
        $this->middleware('permission:overtime.edit')->only(['edit', 'update']);
        $this->middleware('permission:overtime.approve')->only(['approve', 'reject']);
        $this->middleware('permission:overtime.delete')->only(['destroy']);
    }

    /**
     * Display listing of SPK Lembur with Swiss Precision bento metrics
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $isKaryawan = $user && $user->hasRole('karyawan');
        $userKaryawan = $isKaryawan ? \App\Models\Userkaryawan::where('id_user', $user->id)->first() : null;
        $employeeNik = $userKaryawan?->nik ?? $user?->nik;

        $query = Lembur::with(['karyawan.departemen', 'karyawan.jabatan', 'policy', 'approver'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc');

        if ($isKaryawan && $employeeNik) {
            $query->where('nik', $employeeNik);
        }

        if ($request->filled('nik')) {
            $query->where('nik', $request->nik);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('day_type')) {
            $query->where('day_type', $request->day_type);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $statsQuery = Lembur::query();
        if ($isKaryawan && $employeeNik) {
            $statsQuery->where('nik', $employeeNik);
        }

        // Statistics for Swiss Precision Bento Grid
        $stats = [
            'total_spk_month' => (clone $statsQuery)->whereYear('tanggal', $currentYear)->whereMonth('tanggal', $currentMonth)->count(),
            'pending_approval' => (clone $statsQuery)->where('status', 'PENDING')->count(),
            'approved_month' => (clone $statsQuery)->whereYear('tanggal', $currentYear)->whereMonth('tanggal', $currentMonth)->where('status', 'APPROVED')->count(),
            'total_rate_hours_month' => round(
                (float) (clone $statsQuery)->whereYear('tanggal', $currentYear)
                    ->whereMonth('tanggal', $currentMonth)
                    ->where('status', 'APPROVED')
                    ->sum('calculated_rate_hours'),
                2
            ),
        ];

        $lemburs = $query->paginate(15)->withQueryString();
        $employees = Karyawan::where('status_aktif_karyawan', '1')->orderBy('nama_karyawan')->get();
        $policies = OvertimePolicy::where('is_active', true)->get();

        if ($isKaryawan) {
            return view('kepegawaian.lembur.index-mobile', compact('lemburs', 'stats', 'policies'));
        }

        return view('kepegawaian.lembur.index', compact('lemburs', 'stats', 'employees', 'policies'));
    }

    /**
     * Show create modal / page
     */
    public function create(): View
    {
        $user = auth()->user();
        $policies = OvertimePolicy::where('is_active', true)->get();
        $defaultPolicy = OvertimePolicy::getDefaultPolicy();

        if ($user && $user->hasRole('karyawan')) {
            $userKaryawan = \App\Models\Userkaryawan::where('id_user', $user->id)->first();
            $currentKaryawan = $userKaryawan ? Karyawan::where('nik', $userKaryawan->nik)->first() : null;
            return view('kepegawaian.lembur.create-mobile', compact('currentKaryawan', 'policies', 'defaultPolicy'));
        }

        $employees = Karyawan::where('status_aktif_karyawan', '1')->orderBy('nama_karyawan')->get();

        return view('kepegawaian.lembur.create', compact('employees', 'policies', 'defaultPolicy'));
    }

    /**
     * Store new SPK Lembur
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $isKaryawan = $user && $user->hasRole('karyawan');
        if ($isKaryawan) {
            $userKaryawan = \App\Models\Userkaryawan::where('id_user', $user->id)->first();
            $request->merge(['nik' => $userKaryawan?->nik ?? $user->nik]);
        }

        $request->validate([
            'nik' => 'required|exists:karyawan,nik',
            'tanggal' => 'required|date',
            'day_type' => 'required|in:WORKDAY,OFFDAY_5DAYS,OFFDAY_6DAYS,PUBLIC_HOLIDAY',
            'lembur_mulai' => 'required|date_format:Y-m-d\TH:i',
            'lembur_selesai' => 'required|date_format:Y-m-d\TH:i|after:lembur_mulai',
            'keterangan' => 'required|string|max:1000',
            'overtime_policy_id' => 'nullable|exists:overtime_policies,id',
        ]);

        try {
            $lembur = $this->workflow->createSpk($request->all());

            return redirect()->route('overtime.index')
                ->with('success', "SPK Lembur {$lembur->no_spk} berhasil dibuat.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal membuat SPK Lembur: ' . $e->getMessage());
        }
    }

    /**
     * Show SPK Lembur detail / print view
     */
    public function show(Lembur $lembur): View
    {
        $this->authorizeOvertimeAccess($lembur, 'show');

        $lembur->load(['karyawan.departemen', 'karyawan.jabatan', 'policy', 'approver']);
        $calculation = $this->calculator->calculateRateHours(
            $lembur->duration_hours,
            $lembur->day_type,
            $lembur->policy
        );

        $user = auth()->user();
        if ($user && $user->hasRole('karyawan')) {
            return view('kepegawaian.lembur.show-mobile', compact('lembur', 'calculation'));
        }

        return view('kepegawaian.lembur.show', compact('lembur', 'calculation'));
    }

    /**
     * Edit SPK Lembur
     */
    public function edit(Lembur $lembur): View
    {
        $employees = Karyawan::where('status_aktif_karyawan', '1')->orderBy('nama_karyawan')->get();
        $policies = OvertimePolicy::where('is_active', true)->get();

        return view('kepegawaian.lembur.edit', compact('lembur', 'employees', 'policies'));
    }

    /**
     * Update SPK Lembur
     */
    public function update(Request $request, Lembur $lembur): RedirectResponse
    {
        $request->validate([
            'tanggal' => 'required|date',
            'day_type' => 'required|in:WORKDAY,OFFDAY_5DAYS,OFFDAY_6DAYS,PUBLIC_HOLIDAY',
            'lembur_mulai' => 'required|date_format:Y-m-d\TH:i',
            'lembur_selesai' => 'required|date_format:Y-m-d\TH:i|after:lembur_mulai',
            'keterangan' => 'required|string|max:1000',
            'overtime_policy_id' => 'nullable|exists:overtime_policies,id',
        ]);

        try {
            $mulai = Carbon::parse($request->lembur_mulai);
            $selesai = Carbon::parse($request->lembur_selesai);
            $plannedMinutes = max(0, (int) $mulai->diffInMinutes($selesai, false));
            $plannedHours = $plannedMinutes / 60;

            $policyId = $request->overtime_policy_id ?? $lembur->overtime_policy_id;
            $policy = OvertimePolicy::find($policyId);
            $calc = $this->calculator->calculateRateHours($plannedHours, $request->day_type, $policy);

            $lembur->update([
                'tanggal' => $request->tanggal,
                'day_type' => $request->day_type,
                'lembur_mulai' => $mulai,
                'lembur_selesai' => $selesai,
                'planned_duration_minutes' => $plannedMinutes,
                'calculated_rate_hours' => $calc['rate_hours'],
                'overtime_policy_id' => $policyId,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->route('overtime.index')
                ->with('success', "SPK Lembur {$lembur->no_spk} berhasil diperbarui.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui SPK Lembur: ' . $e->getMessage());
        }
    }

    /**
     * Delete SPK Lembur
     */
    public function destroy(Lembur $lembur): RedirectResponse
    {
        $noSpk = $lembur->no_spk;
        $lembur->delete();

        return redirect()->route('overtime.index')
            ->with('success', "SPK Lembur {$noSpk} berhasil dihapus.");
    }

    /**
     * Approve SPK Lembur
     */
    public function approve(Request $request, Lembur $lembur): RedirectResponse
    {
        $this->authorizeOvertimeAccess($lembur, 'approve');

        // Anti-self-approval protection
        $user = auth()->user();
        $userKaryawan = $user ? \App\Models\Userkaryawan::where('id_user', $user->id)->first() : null;
        $myNik = $userKaryawan?->nik ?? $user?->nik;
        if ($myNik && $lembur->nik === $myNik) {
            return back()->with('error', 'Anda tidak dapat menyetujui pengajuan SPK lembur milik Anda sendiri.');
        }

        $request->validate([
            'approved_duration_minutes' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $userId = auth()->id() ?? 1;
            $approvedMinutes = $request->filled('approved_duration_minutes')
                ? (int) $request->approved_duration_minutes
                : null;

            $this->workflow->approve($lembur, $userId, $approvedMinutes, $request->notes);

            return back()->with('success', "SPK Lembur {$lembur->no_spk} telah disetujui.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyetujui SPK Lembur: ' . $e->getMessage());
        }
    }

    /**
     * Reject SPK Lembur
     */
    public function reject(Request $request, Lembur $lembur): RedirectResponse
    {
        $this->authorizeOvertimeAccess($lembur, 'reject');

        // Anti-self-rejection/approval check
        $user = auth()->user();
        $userKaryawan = $user ? \App\Models\Userkaryawan::where('id_user', $user->id)->first() : null;
        $myNik = $userKaryawan?->nik ?? $user?->nik;
        if ($myNik && $lembur->nik === $myNik) {
            return back()->with('error', 'Anda tidak dapat memproses SPK lembur milik Anda sendiri.');
        }

        $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        try {
            $userId = auth()->id() ?? 1;
            $this->workflow->reject($lembur, $userId, $request->notes);

            return back()->with('success', "SPK Lembur {$lembur->no_spk} ditolak.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menolak SPK Lembur: ' . $e->getMessage());
        }
    }

    /**
     * Authorize user access to a specific overtime SPK
     */
    protected function authorizeOvertimeAccess(Lembur $lembur, string $action = 'show'): void
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        if ($user->isSuperAdmin()) {
            return;
        }

        if ($user->hasRole('karyawan')) {
            $userKaryawan = \App\Models\Userkaryawan::where('id_user', $user->id)->first();
            $myNik = $userKaryawan?->nik ?? $user->nik ?? null;

            if (!$myNik || $lembur->nik !== $myNik) {
                abort(403, 'Akses ditolak. Anda hanya berhak melihat SPK lembur milik Anda sendiri.');
            }
            return;
        }

        // Branch and department scoping for admin staff
        $karyawan = $lembur->karyawan ?? Karyawan::where('nik', $lembur->nik)->first();
        if ($karyawan) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!empty($userCabangs) && !in_array($karyawan->kode_cabang, $userCabangs)) {
                abort(403, 'Akses ditolak. Karyawan berada di luar cabang wewenang Anda.');
            }
            if (!empty($userDepartemens) && !in_array($karyawan->kode_dept, $userDepartemens)) {
                abort(403, 'Akses ditolak. Karyawan berada di luar departemen wewenang Anda.');
            }
        }
    }

    /**
     * Dynamic JSON endpoint for real-time preview of Depnaker calculation in form
     */
    public function calculatePreview(Request $request): JsonResponse
    {
        $request->validate([
            'lembur_mulai' => 'required|date_format:Y-m-d\TH:i',
            'lembur_selesai' => 'required|date_format:Y-m-d\TH:i',
            'day_type' => 'required|in:WORKDAY,OFFDAY_5DAYS,OFFDAY_6DAYS,PUBLIC_HOLIDAY',
            'overtime_policy_id' => 'nullable|exists:overtime_policies,id',
            'nik' => 'nullable|exists:karyawan,nik',
        ]);

        $mulai = Carbon::parse($request->lembur_mulai);
        $selesai = Carbon::parse($request->lembur_selesai);
        $minutes = max(0, (int) $mulai->diffInMinutes($selesai, false));
        $hours = $minutes / 60;

        $policy = $request->filled('overtime_policy_id')
            ? OvertimePolicy::find($request->overtime_policy_id)
            : OvertimePolicy::getDefaultPolicy();

        $calc = $this->calculator->calculateRateHours($hours, $request->day_type, $policy);

        $validation = null;
        if ($request->filled('nik')) {
            $validation = $this->calculator->validateLimits(
                $request->nik,
                $mulai->toDateString(),
                $hours,
                $policy
            );
        }

        return response()->json([
            'planned_minutes' => $minutes,
            'planned_hours' => round($hours, 2),
            'calculation' => $calc,
            'compliance' => $validation,
        ]);
    }
}
