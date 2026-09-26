<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Reimbursement;
use App\Models\ReimbursementType;
use App\Services\ReimbursementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReimbursementController extends Controller
{
    /**
     * Display list of reimbursement claims
     */
    public function index(Request $request): View
    {
        $query = Reimbursement::with(['karyawan.dpt', 'type', 'approver'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type_id')) {
            $query->where('reimbursement_type_id', $request->type_id);
        } elseif ($request->filled('reimbursement_type_id')) {
            $query->where('reimbursement_type_id', $request->reimbursement_type_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('karyawan', function ($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $reimbursements = $query->paginate(15)->withQueryString();
        $types = ReimbursementType::active()->get();

        $stats = [
            'total_claims' => Reimbursement::count(),
            'total_submitted' => Reimbursement::where('status', 'SUBMITTED')->count(),
            'total_approved' => Reimbursement::where('status', 'APPROVED')->count(),
            'approved_amount' => (float) Reimbursement::where('status', 'APPROVED')->sum('amount'),
            'pending_amount' => (float) Reimbursement::where('status', 'SUBMITTED')->sum('amount'),
            'paid_amount' => (float) Reimbursement::where('status', 'PAID')->sum('amount'),
            'total_amount' => (float) Reimbursement::whereIn('status', ['APPROVED', 'PAID'])->sum('amount'),
        ];

        return view('keuangan.reimbursement.index', compact('reimbursements', 'types', 'stats'));
    }

    /**
     * Show form for creating a reimbursement claim
     */
    public function create(): View
    {
        $types = ReimbursementType::active()->get();
        $employees = Karyawan::where('status_aktif_karyawan', '1')->orderBy('nama_karyawan')->get();

        return view('keuangan.reimbursement.create', compact('types', 'employees'));
    }

    /**
     * Store new reimbursement claim
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nik' => 'required|exists:karyawan,nik',
            'reimbursement_type_id' => 'required|exists:reimbursement_types,id',
            'claim_date' => 'required|date',
            'amount' => 'required|numeric|min:1000',
            'description' => 'nullable|string|max:500',
            'receipt' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $claim = ReimbursementService::submitClaim($validated, $request->file('receipt'));

        return redirect()->route('reimbursement.index')
            ->with('success', "Klaim reimbursement {$claim->claim_number} berhasil diajukan.");
    }

    /**
     * Approve claim
     */
    public function approve(Reimbursement $reimbursement): RedirectResponse
    {
        ReimbursementService::approveClaim($reimbursement, auth()->id());

        return redirect()->back()->with('success', "Klaim {$reimbursement->claim_number} telah disetujui.");
    }

    /**
     * Reject claim
     */
    public function reject(Request $request, Reimbursement $reimbursement): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        ReimbursementService::rejectClaim($reimbursement, $request->rejection_reason, auth()->id());

        return redirect()->back()->with('success', "Klaim {$reimbursement->claim_number} telah ditolak.");
    }

    /**
     * Delete claim
     */
    public function destroy(Reimbursement $reimbursement): RedirectResponse
    {
        if (in_array($reimbursement->status, ['APPROVED', 'PAID'])) {
            return redirect()->back()->with('error', 'Klaim yang sudah disetujui atau terbayar tidak dapat dihapus.');
        }

        $reimbursement->delete();

        return redirect()->back()->with('success', 'Klaim berhasil dihapus.');
    }
}
