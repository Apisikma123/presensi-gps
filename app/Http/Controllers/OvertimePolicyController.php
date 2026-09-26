<?php

namespace App\Http\Controllers;

use App\Models\OvertimePolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OvertimePolicyController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:overtime_policy.index')->only(['index']);
        $this->middleware('permission:overtime_policy.edit')->only(['update']);
    }

    /**
     * Display Overtime Policy Settings & Statutory Rules
     */
    public function index(): View
    {
        $policy = OvertimePolicy::getDefaultPolicy();
        $policies = OvertimePolicy::orderBy('id', 'desc')->get();

        return view('settings.overtime.index', compact('policy', 'policies'));
    }

    /**
     * Update Overtime Policy
     */
    public function update(Request $request, OvertimePolicy $policy): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'max_hours_per_day' => 'required|numeric|min:1|max:12',
            'max_hours_per_week' => 'required|numeric|min:1|max:40',
            'requires_meal_allowance_after_4h' => 'required|boolean',
            'is_active' => 'required|boolean',
        ]);

        $policy->update([
            'name' => $request->name,
            'max_hours_per_day' => $request->max_hours_per_day,
            'max_hours_per_week' => $request->max_hours_per_week,
            'requires_meal_allowance_after_4h' => (bool) $request->requires_meal_allowance_after_4h,
            'is_active' => (bool) $request->is_active,
        ]);

        return redirect()->route('overtime_policy.index')
            ->with('success', 'Kebijakan lembur berhasil diperbarui.');
    }
}
