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
        $query = EmployeeLoan::with(['karyawan.dpt', 'approver'])->latest();

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

        $stats = [
            'total_loans' => EmployeeLoan::count(),
            'active_loans' => EmployeeLoan::where('status', 'ACTIVE')->count(),
            'total_disbursed' => (float) EmployeeLoan::whereIn('status', ['ACTIVE', 'PAID_OFF'])->sum('total_amount'),
            'total_receivable' => (float) EmployeeLoan::where('status', 'ACTIVE')->sum('remaining_amount'),
            'total_remaining' => (float) EmployeeLoan::where('status', 'ACTIVE')->sum('remaining_amount'),
        ];

        return view('keuangan.loan.index', compact('loans', 'stats'));
    }

    /**
     * Show form for new loan / kasbon application
     */
    public function create(): View
    {
        $employees = Karyawan::where('status_aktif_karyawan', '1')->orderBy('nama_karyawan')->get();

        return view('keuangan.loan.create', compact('employees'));
    }

    /**
     * Store new loan application
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nik' => 'required|exists:karyawan,nik',
            'loan_amount' => 'required|numeric|min:50000',
            'installment_months' => 'required|integer|min:1|max:36',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $loan = EmployeeLoanService::applyLoan($validated);

        return redirect()->route('loan.show', $loan->id)
            ->with('success', "Pengajuan kasbon {$loan->loan_number} berhasil dicatat.");
    }

    /**
     * Display detail of a loan and its installment schedule
     */
    public function show(EmployeeLoan $loan): View
    {
        $loan->load(['karyawan.dpt', 'karyawan.jabatan', 'approver', 'installments']);

        return view('keuangan.loan.show', compact('loan'));
    }

    /**
     * Approve loan and generate installment schedules
     */
    public function approve(EmployeeLoan $loan): RedirectResponse
    {
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
        if (in_array($loan->status, ['ACTIVE', 'PAID_OFF'])) {
            return redirect()->back()->with('error', 'Pinjaman yang sudah aktif berjalan atau lunas tidak dapat dihapus.');
        }

        $loan->delete();

        return redirect()->route('loan.index')->with('success', 'Pengajuan pinjaman berhasil dihapus.');
    }
}
