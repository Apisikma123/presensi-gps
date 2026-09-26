<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLoan;
use App\Models\EmployeeResignation;
use App\Models\EmployeeSalaryAssignment;
use App\Models\OffboardingClearance;
use App\Services\OffboardingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OffboardingController extends Controller
{
    protected OffboardingService $offboardingService;

    public function __construct(OffboardingService $offboardingService)
    {
        $this->offboardingService = $offboardingService;
    }

    /**
     * Show detailed clearance and settlement page for an employee exit
     */
    public function show($id)
    {
        $resignation = EmployeeResignation::with([
            'karyawan.departemen',
            'karyawan.cabang',
            'karyawan.jabatan',
            'clearances.clearedByUser',
            'approver',
        ])->findOrFail($id);

        // If no clearances generated yet, initialize defaults
        if ($resignation->clearances->isEmpty()) {
            $this->offboardingService->generateDefaultClearances($resignation);
            $resignation->load('clearances.clearedByUser');
        }

        $clearancesByDept = $resignation->clearances->groupBy('department');

        // Check active loan balance
        $activeLoanBalance = (float) EmployeeLoan::where('nik', $resignation->nik)
            ->where('status', 'ACTIVE')
            ->sum('remaining_amount');

        // Fetch employee basic salary for settlement calculator
        $basicSalary = (float) EmployeeSalaryAssignment::where('nik', $resignation->nik)
            ->whereHas('component', fn($q) => $q->where('code', 'BASIC_SALARY'))
            ->value('amount') ?? 5000000.00;

        return view('kepegawaian.offboarding.show', compact('resignation', 'clearancesByDept', 'activeLoanBalance', 'basicSalary'));
    }

    /**
     * Toggle exit clearance checklist item
     */
    public function toggleClearance(Request $request, $clearanceId)
    {
        $clearance = OffboardingClearance::findOrFail($clearanceId);
        $cleared = $request->boolean('is_cleared', !$clearance->is_cleared);

        $this->offboardingService->toggleClearanceItem(
            (int) $clearanceId,
            Auth::id(),
            $cleared,
            $request->get('notes')
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'is_cleared' => $cleared,
                'resignation_status' => $clearance->resignation->fresh()->status_clearance,
            ]);
        }

        return redirect()->back()->with('success', 'Status checklist clearance diperbarui.');
    }

    /**
     * Calculate statutory Indonesian severance preview via AJAX
     */
    public function calculateSettlement(Request $request, $id)
    {
        $resignation = EmployeeResignation::with('karyawan')->findOrFail($id);

        $monthlyWage = (float) $request->get('monthly_wage', 5000000);
        $hireDate = $resignation->karyawan->tanggal_masuk ?? Carbon::now()->subYears(2);
        $exitDate = $resignation->tanggal_keluar ?? Carbon::now();

        $calculation = $this->offboardingService->calculateSeverance(
            $resignation->nik,
            $monthlyWage,
            $hireDate,
            $exitDate,
            $resignation->kategori_keluar
        );

        return response()->json([
            'success' => true,
            'calculation' => $calculation,
        ]);
    }

    /**
     * Save exit settlement details
     */
    public function saveSettlement(Request $request, $id)
    {
        $request->validate([
            'severance_pay' => 'required|numeric|min:0',
            'service_pay' => 'required|numeric|min:0',
            'compensation_pay' => 'required|numeric|min:0',
            'final_salary_pay' => 'required|numeric|min:0',
            'deductions_pay' => 'required|numeric|min:0',
        ]);

        $this->offboardingService->saveSettlement((int) $id, $request->all());

        return redirect()->back()->with('success', 'Rincian hak akhir & pesangon berhasil disimpan.');
    }

    /**
     * Finalize offboarding and mark settlement as paid
     */
    public function finalize(Request $request, $id)
    {
        $this->offboardingService->finalizeOffboarding((int) $id);

        return redirect()->route('resignation.index')
            ->with('success', 'Proses offboarding dan clearance karyawan berhasil diselesaikan (FINALIZED).');
    }

    /**
     * Print exit clearance certificate and settlement receipt
     */
    public function printClearance($id)
    {
        $resignation = EmployeeResignation::with([
            'karyawan.departemen',
            'karyawan.cabang',
            'karyawan.jabatan',
            'clearances.clearedByUser',
            'approver',
        ])->findOrFail($id);

        $company = \App\Models\CompanySetting::getActive();
        $clearancesByDept = $resignation->clearances->groupBy('department');

        return view('kepegawaian.offboarding.print', compact('resignation', 'clearancesByDept', 'company'));
    }
}
