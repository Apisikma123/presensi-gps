<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLoan;
use App\Models\EmployeeLoanInstallment;
use App\Models\Karyawan;
use App\Services\EmployeeLoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeLoanController extends Controller
{
    /**
     * Display list of employee loans / kasbon
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $isKaryawan = $user && $user->hasRole('karyawan');
        $userKaryawan = $isKaryawan ? \App\Models\Userkaryawan::where('id_user', $user->id)->first() : null;
        $employeeNik = $userKaryawan?->nik ?? $user?->nik;

        $query = EmployeeLoan::with(['karyawan.dpt', 'approver'])->latest();

        if ($isKaryawan && $employeeNik) {
            $query->where('nik', $employeeNik);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('karyawan', function ($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $loans = $query->paginate(15)->withQueryString();

        $statsQuery = EmployeeLoan::query();
        if ($isKaryawan && $employeeNik) {
            $statsQuery->where('nik', $employeeNik);
        }

        // Branch and department scoping for non-superadmin staff
        if (!$isKaryawan && $user && !$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!empty($userCabangs)) {
                $query->whereHas('karyawan', fn($q) => $q->whereIn('kode_cabang', $userCabangs));
                $statsQuery->whereHas('karyawan', fn($q) => $q->whereIn('kode_cabang', $userCabangs));
            }
            if (!empty($userDepartemens)) {
                $query->whereHas('karyawan', fn($q) => $q->whereIn('kode_dept', $userDepartemens));
                $statsQuery->whereHas('karyawan', fn($q) => $q->whereIn('kode_dept', $userDepartemens));
            }
        }

        $stats = [
            'total_loans' => (clone $statsQuery)->count(),
            'active_loans' => (clone $statsQuery)->where('status', 'ACTIVE')->count(),
            'total_disbursed' => (float) (clone $statsQuery)->whereIn('status', ['ACTIVE', 'PAID_OFF'])->sum('total_amount'),
            'total_receivable' => (float) (clone $statsQuery)->where('status', 'ACTIVE')->sum('remaining_amount'),
            'total_remaining' => (float) (clone $statsQuery)->where('status', 'ACTIVE')->sum('remaining_amount'),
        ];

        if ($isKaryawan) {
            return view('keuangan.loan.index-mobile', compact('loans', 'stats'));
        }

        return view('keuangan.loan.index', compact('loans', 'stats'));
    }

    /**
     * Show form for new loan / kasbon application
     */
    public function create(): View
    {
        $user = auth()->user();
        if ($user && $user->hasRole('karyawan')) {
            $userKaryawan = \App\Models\Userkaryawan::where('id_user', $user->id)->first();
            $currentKaryawan = $userKaryawan ? Karyawan::where('nik', $userKaryawan->nik)->first() : null;
            return view('keuangan.loan.create-mobile', compact('currentKaryawan'));
        }

        $employees = Karyawan::where('status_aktif_karyawan', '1')->orderBy('nama_karyawan')->get();

        return view('keuangan.loan.create', compact('employees'));
    }

    /**
     * Store new loan application
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $isKaryawan = $user && $user->hasRole('karyawan');
        if ($isKaryawan) {
            $userKaryawan = \App\Models\Userkaryawan::where('id_user', $user->id)->first();
            $request->merge([
                'nik' => $userKaryawan?->nik ?? $user->nik,
                'interest_rate' => 0,
            ]);
        }

        $validated = $request->validate([
            'nik' => 'required|exists:karyawan,nik',
            'loan_amount' => 'required|numeric|min:50000',
            'installment_months' => 'required|integer|min:1|max:36',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $loan = EmployeeLoanService::applyLoan($validated);

        if ($isKaryawan) {
            return redirect()->route('loan.index')
                ->with('success', "Pengajuan kasbon {$loan->loan_number} berhasil dikirim dan menunggu persetujuan.");
        }

        return redirect()->route('loan.show', $loan->id)
            ->with('success', "Pengajuan kasbon {$loan->loan_number} berhasil dicatat.");
    }

    /**
     * Display detail of a loan and its installment schedule
     */
    public function show(EmployeeLoan $loan): View
    {
        $this->authorizeLoanAccess($loan, 'view');

        $loan->load(['karyawan.dpt', 'karyawan.jabatan', 'approver', 'installments']);

        $user = auth()->user();
        if ($user && $user->hasRole('karyawan')) {
            return view('keuangan.loan.show-mobile', compact('loan'));
        }

        return view('keuangan.loan.show', compact('loan'));
    }

    /**
     * Approve loan and generate installment schedules
     */
    public function approve(EmployeeLoan $loan): RedirectResponse
    {
        $this->authorizeLoanAccess($loan, 'approve');

        // Anti-self-approval protection
        $user = auth()->user();
        $userKaryawan = $user ? \App\Models\Userkaryawan::where('id_user', $user->id)->first() : null;
        $myNik = $userKaryawan?->nik ?? $user?->nik;
        if ($myNik && $loan->nik === $myNik) {
            return redirect()->back()->with('error', 'Anda tidak dapat menyetujui pengajuan kasbon milik Anda sendiri.');
        }

        if ($loan->status !== 'PENDING') {
            return redirect()->back()->with('error', 'Hanya pengajuan dengan status Menunggu yang dapat disetujui.');
        }

        EmployeeLoanService::approveLoan($loan, auth()->id());

        return redirect()->back()->with('success', "Pinjaman {$loan->loan_number} telah disetujui dan jadwal cicilan telah diterbitkan.");
    }

    /**
     * Record payment of an installment
     */
    public function repayInstallment(Request $request, EmployeeLoanInstallment $installment): RedirectResponse
    {
        $this->authorizeLoanAccess($installment->loan, 'repay');

        if ($installment->status === 'PAID') {
            return redirect()->back()->with('error', 'Cicilan ini sudah lunas.');
        }

        EmployeeLoanService::recordRepayment($installment);

        return redirect()->back()->with('success', "Pembayaran cicilan ke-{$installment->installment_number} berhasil dicatat.");
    }

    /**
     * Delete loan
     */
    public function destroy(EmployeeLoan $loan): RedirectResponse
    {
        $this->authorizeLoanAccess($loan, 'delete');

        if (in_array($loan->status, ['ACTIVE', 'PAID_OFF'])) {
            return redirect()->back()->with('error', 'Pinjaman yang sudah aktif berjalan atau lunas tidak dapat dihapus.');
        }

        $loan->delete();

        return redirect()->route('loan.index')->with('success', 'Pengajuan pinjaman berhasil dihapus.');
    }

    /**
     * Authorize user access to a specific loan record
     */
    protected function authorizeLoanAccess(EmployeeLoan $loan, string $action = 'view'): void
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        if ($user->isSuperAdmin()) {
            return;
        }

        // Employee access: strictly check ownership (NIK match)
        if ($user->hasRole('karyawan')) {
            $userKaryawan = \App\Models\Userkaryawan::where('id_user', $user->id)->first();
            $employeeNik = $userKaryawan?->nik ?? $user->nik ?? null;

            if (!$employeeNik || $loan->nik !== $employeeNik) {
                abort(403, 'Akses ditolak. Anda hanya berhak melihat pengajuan kasbon milik Anda sendiri.');
            }
            return;
        }

        // Admin/Staff access: check branch and department scoping
        $karyawan = $loan->karyawan ?? Karyawan::where('nik', $loan->nik)->first();
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
}
