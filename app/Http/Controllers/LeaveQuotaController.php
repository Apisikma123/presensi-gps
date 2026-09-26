<?php

namespace App\Http\Controllers;

use App\Models\LeaveQuota;
use App\Models\LeaveType;
use App\Services\LeaveQuotaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveQuotaController extends Controller
{
    protected LeaveQuotaService $quotaService;

    public function __construct(LeaveQuotaService $quotaService)
    {
        $this->quotaService = $quotaService;
    }

    public function index(Request $request)
    {
        $year = $request->filled('year') ? (int) $request->year : (int) date('Y');

        $query = LeaveQuota::with(['karyawan', 'leaveType'])
            ->where('year', $year);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                  ->orWhereHas('karyawan', function ($sub) use ($search) {
                      $sub->where('nama_karyawan', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        $quotas = $query->orderBy('nik')->paginate(15);
        $leaveTypes = LeaveType::where('is_active', true)->where('uses_quota', true)->orderBy('name')->get();

        $stats = [
            'total_allocated' => LeaveQuota::where('year', $year)->sum('earned'),
            'total_used' => LeaveQuota::where('year', $year)->sum('used'),
            'total_remaining' => LeaveQuota::where('year', $year)->sum('closing_balance'),
            'employees_count' => LeaveQuota::where('year', $year)->distinct('nik')->count('nik'),
        ];

        return view('datamaster.leave_quotas.index', compact('quotas', 'leaveTypes', 'year', 'stats'));
    }

    public function adjust(Request $request)
    {
        $request->validate([
            'leave_quota_id' => 'required|exists:leave_quotas,id',
            'amount' => 'required|numeric',
            'reason' => 'required|string|max:255',
        ]);

        $this->quotaService->adjustQuota(
            (int) $request->leave_quota_id,
            (float) $request->amount,
            trim($request->reason),
            Auth::id()
        );

        return redirect()->back()->with('success', 'Penyesuaian kuota cuti berhasil disimpan.');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2035',
        ]);

        $count = $this->quotaService->generateQuotasForAllActive((int) $request->year);

        return redirect()->back()->with('success', "Inisialisasi kuota cuti tahun {$request->year} berhasil diproses untuk {$count} karyawan aktif.");
    }
}
