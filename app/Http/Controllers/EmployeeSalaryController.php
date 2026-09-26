<?php

namespace App\Http\Controllers;

use App\Models\EmployeeSalaryAssignment;
use App\Models\Karyawan;
use App\Models\SalaryComponent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EmployeeSalaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:employee_salary.index')->only(['index']);
        $this->middleware('permission:employee_salary.edit')->only(['edit', 'update']);
    }

    /**
     * Display list of employees with salary structures
     */
    public function index(Request $request): View
    {
        $query = Karyawan::with(['departemen', 'jabatan', 'salaryAssignments.component'])
            ->where('status_aktif_karyawan', '1');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_karyawan', 'like', "%{$s}%")
                  ->orWhere('nik', 'like', "%{$s}%");
            });
        }

        if ($request->filled('kode_dept')) {
            $query->where('kode_dept', $request->kode_dept);
        }

        $employees = $query->paginate(15)->withQueryString();
        $components = SalaryComponent::where('is_active', true)->orderBy('sort_order')->get();

        return view('keuangan.payroll.employee_salary.index', compact('employees', 'components'));
    }

    /**
     * Show form to edit employee salary structure
     */
    public function edit(Karyawan $karyawan): View
    {
        $karyawan->load(['departemen', 'jabatan', 'salaryAssignments.component']);
        $components = SalaryComponent::where('is_active', true)->orderBy('type')->orderBy('sort_order')->get();
        $assignments = $karyawan->salaryAssignments->keyBy('salary_component_id');

        return view('keuangan.payroll.employee_salary.edit', compact('karyawan', 'components', 'assignments'));
    }

    /**
     * Update employee salary assignments
     */
    public function update(Request $request, Karyawan $karyawan): RedirectResponse
    {
        $request->validate([
            'components' => 'required|array',
            'components.*' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request, $karyawan) {
                foreach ($request->components as $componentId => $amount) {
                    $amountVal = (float) $amount;

                    EmployeeSalaryAssignment::updateOrCreate(
                        [
                            'nik' => $karyawan->nik,
                            'salary_component_id' => $componentId,
                        ],
                        [
                            'amount' => $amountVal,
                            'effective_date' => now()->toDateString(),
                            'is_active' => $amountVal > 0,
                        ]
                    );
                }
            });

            return redirect()->route('employee_salary.index')
                ->with('success', "Struktur gaji untuk {$karyawan->nama_karyawan} berhasil diperbarui.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
}
