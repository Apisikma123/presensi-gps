<?php

namespace App\Http\Controllers;

use App\Models\IndonesiaPolicyRule;
use App\Services\BPJSService;
use App\Services\IndonesiaTaxService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IndonesiaComplianceController extends Controller
{
    /**
     * Statutory Compliance Overview & Live Simulation
     */
    public function index(Request $request): View|JsonResponse
    {
        $taxRule = IndonesiaPolicyRule::getActiveRule('TAX');
        $bpjsKesRule = IndonesiaPolicyRule::getActiveRule('BPJS_KES');
        $bpjsTkRule = IndonesiaPolicyRule::getActiveRule('BPJS_TK');
        $thrRule = IndonesiaPolicyRule::getActiveRule('THR');

        // Check if live simulation requested
        if ($request->wantsJson() || $request->has('simulate')) {
            $salary = (float) $request->input('salary', 7500000);
            $ptkp = $request->input('ptkp_status', 'TK/0');
            $hasNpwp = filter_var($request->input('has_npwp', true), FILTER_VALIDATE_BOOLEAN);

            $taxResult = IndonesiaTaxService::calculatePph21Ter($salary, $ptkp, $hasNpwp);
            $bpjsResult = BPJSService::calculateBPJS($salary);

            $takeHomePay = $salary - $taxResult['tax_amount'] - $bpjsResult['total_employee_deduction'];

            return response()->json([
                'success' => true,
                'input' => [
                    'salary' => $salary,
                    'ptkp_status' => $ptkp,
                    'has_npwp' => $hasNpwp,
                ],
                'tax' => $taxResult,
                'bpjs' => $bpjsResult,
                'take_home_pay' => $takeHomePay,
            ]);
        }

        // Default sample calculation for page load
        $sampleSalary = 7500000;
        $samplePtkp = 'TK/0';
        $sampleTax = IndonesiaTaxService::calculatePph21Ter($sampleSalary, $samplePtkp);
        $sampleBpjs = BPJSService::calculateBPJS($sampleSalary);

        return view('keuangan.compliance.index', compact(
            'taxRule',
            'bpjsKesRule',
            'bpjsTkRule',
            'thrRule',
            'sampleSalary',
            'sampleTax',
            'sampleBpjs'
        ));
    }

    /**
     * Update statutory policy rule
     */
    public function update(Request $request, IndonesiaPolicyRule $rule): RedirectResponse
    {
        $validated = $request->validate([
            'source_reference' => 'nullable|string|max:255',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'is_active' => 'boolean',
        ]);

        $rule->update($validated);

        return redirect()->route('compliance.index')->with('success', 'Peraturan regulasi berhasil diperbarui.');
    }
}
