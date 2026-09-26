<?php

namespace App\Http\Controllers;

use App\Models\SalaryComponent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalaryComponentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:salary_component.index')->only(['index']);
        $this->middleware('permission:salary_component.create')->only(['store']);
        $this->middleware('permission:salary_component.edit')->only(['update']);
        $this->middleware('permission:salary_component.delete')->only(['destroy']);
    }

    /**
     * Display salary components
     */
    public function index(): View
    {
        $components = SalaryComponent::orderBy('type', 'asc')
            ->orderBy('sort_order', 'asc')
            ->get();

        $earnings = $components->where('type', 'EARNING');
        $deductions = $components->where('type', 'DEDUCTION');

        return view('keuangan.payroll.components.index', compact('components', 'earnings', 'deductions'));
    }

    /**
     * Store new component
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:salary_components,code|regex:/^[A-Z0-9_]+$/',
            'name' => 'required|string|max:100',
            'type' => 'required|in:EARNING,DEDUCTION',
            'is_fixed' => 'required|boolean',
            'is_taxable' => 'required|boolean',
            'is_bpjs_basis' => 'required|boolean',
            'default_amount' => 'required|numeric|min:0',
            'sort_order' => 'nullable|integer',
        ]);

        try {
            SalaryComponent::create([
                'code' => strtoupper($request->code),
                'name' => $request->name,
                'type' => $request->type,
                'is_fixed' => (bool) $request->is_fixed,
                'is_taxable' => (bool) $request->is_taxable,
                'is_bpjs_basis' => (bool) $request->is_bpjs_basis,
                'is_recurring' => true,
                'default_amount' => $request->default_amount,
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => true,
            ]);

            return redirect()->route('salary_components.index')
                ->with('success', "Komponen gaji {$request->name} berhasil ditambahkan.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan komponen: ' . $e->getMessage());
        }
    }

    /**
     * Update component
     */
    public function update(Request $request, SalaryComponent $component): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'is_fixed' => 'required|boolean',
            'is_taxable' => 'required|boolean',
            'is_bpjs_basis' => 'required|boolean',
            'default_amount' => 'required|numeric|min:0',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
        ]);

        try {
            $component->update([
                'name' => $request->name,
                'is_fixed' => (bool) $request->is_fixed,
                'is_taxable' => (bool) $request->is_taxable,
                'is_bpjs_basis' => (bool) $request->is_bpjs_basis,
                'default_amount' => $request->default_amount,
                'sort_order' => $request->sort_order ?? $component->sort_order,
                'is_active' => (bool) $request->is_active,
            ]);

            return redirect()->route('salary_components.index')
                ->with('success', "Komponen gaji {$component->name} berhasil diperbarui.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }
    }

    /**
     * Delete component
     */
    public function destroy(SalaryComponent $component): RedirectResponse
    {
        if (in_array($component->code, ['BASIC_SALARY'])) {
            return back()->with('error', 'Komponen Gaji Pokok (BASIC_SALARY) adalah komponen inti dan tidak boleh dihapus.');
        }

        $name = $component->name;
        $component->delete();

        return redirect()->route('salary_components.index')
            ->with('success', "Komponen {$name} berhasil dihapus.");
    }
}
