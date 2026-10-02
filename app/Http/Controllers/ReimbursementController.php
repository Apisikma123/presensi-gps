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
        $user = auth()->user();
        $isKaryawan = $user && $user->hasRole('karyawan');
        $userKaryawan = $isKaryawan ? \App\Models\Userkaryawan::where('id_user', $user->id)->first() : null;
        $employeeNik = $userKaryawan?->nik ?? $user?->nik;

        $query = Reimbursement::with(['karyawan.dpt', 'type', 'approver'])->latest();

        if ($isKaryawan && $employeeNik) {
            $query->where('nik', $employeeNik);
        }

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

        $statsQuery = Reimbursement::query();
        if ($isKaryawan && $employeeNik) {
            $statsQuery->where('nik', $employeeNik);
        }

        $stats = [
            'total_claims' => (clone $statsQuery)->count(),
            'total_submitted' => (clone $statsQuery)->where('status', 'SUBMITTED')->count(),
            'total_approved' => (clone $statsQuery)->where('status', 'APPROVED')->count(),
            'approved_amount' => (float) (clone $statsQuery)->where('status', 'APPROVED')->sum('amount'),
            'pending_amount' => (float) (clone $statsQuery)->where('status', 'SUBMITTED')->sum('amount'),
            'paid_amount' => (float) (clone $statsQuery)->where('status', 'PAID')->sum('amount'),
            'total_amount' => (float) (clone $statsQuery)->whereIn('status', ['APPROVED', 'PAID'])->sum('amount'),
        ];

        if ($isKaryawan) {
            return view('keuangan.reimbursement.index-mobile', compact('reimbursements', 'types', 'stats'));
        }

        return view('keuangan.reimbursement.index', compact('reimbursements', 'types', 'stats'));
    }

    /**
     * Show form for creating a reimbursement claim
     */
    public function create(): View
    {
        $user = auth()->user();
        $types = ReimbursementType::active()->get();

        if ($user && $user->hasRole('karyawan')) {
            $userKaryawan = \App\Models\Userkaryawan::where('id_user', $user->id)->first();
            $currentKaryawan = $userKaryawan ? Karyawan::where('nik', $userKaryawan->nik)->first() : null;
            return view('keuangan.reimbursement.create-mobile', compact('types', 'currentKaryawan'));
        }

        $employees = Karyawan::where('status_aktif_karyawan', '1')->orderBy('nama_karyawan')->get();

        return view('keuangan.reimbursement.create', compact('types', 'employees'));
    }

    /**
     * Store new reimbursement claim
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $isKaryawan = $user && $user->hasRole('karyawan');
        if ($isKaryawan) {
            $userKaryawan = \App\Models\Userkaryawan::where('id_user', $user->id)->first();
            $request->merge(['nik' => $userKaryawan?->nik ?? $user->nik]);
        }

        $validated = $request->validate([
            'nik' => 'required|exists:karyawan,nik',
            'reimbursement_type_id' => 'required|exists:reimbursement_types,id',
            'claim_date' => 'required|date',
            'amount' => 'required|numeric|min:1000',
            'description' => 'nullable|string|max:500',
            'receipt' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:2048',
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
        $user = auth()->user();
        $userKaryawan = $user ? \App\Models\Userkaryawan::where('id_user', $user->id)->first() : null;
        $myNik = $userKaryawan?->nik ?? $user?->nik;
        if ($myNik && $reimbursement->nik === $myNik) {
            return redirect()->back()->with('error', 'Anda tidak dapat menyetujui klaim reimbursement milik Anda sendiri.');
        }

        if ($user && !$user->isSuperAdmin()) {
            $karyawan = $reimbursement->karyawan ?? \App\Models\Karyawan::where('nik', $reimbursement->nik)->first();
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

        ReimbursementService::approveClaim($reimbursement, auth()->id());

        return redirect()->back()->with('success', "Klaim {$reimbursement->claim_number} telah disetujui.");
    }

    /**
     * Reject claim
     */
    public function reject(Request $request, Reimbursement $reimbursement): RedirectResponse
    {
        $user = auth()->user();
        $userKaryawan = $user ? \App\Models\Userkaryawan::where('id_user', $user->id)->first() : null;
        $myNik = $userKaryawan?->nik ?? $user?->nik;
        if ($myNik && $reimbursement->nik === $myNik) {
            return redirect()->back()->with('error', 'Anda tidak dapat menolak klaim reimbursement milik Anda sendiri.');
        }

        if ($user && !$user->isSuperAdmin()) {
            $karyawan = $reimbursement->karyawan ?? \App\Models\Karyawan::where('nik', $reimbursement->nik)->first();
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
