<?php

namespace App\Http\Controllers;

use App\Models\PayrollPeriod;
use App\Models\PayrollDetail;
use App\Models\Karyawan;
use App\Services\PayrollService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollPeriodController extends Controller
{
    public function __construct(
        protected PayrollService $payrollService
    ) {
        $this->middleware('permission:payroll.index')->only(['index', 'show']);
        $this->middleware('permission:payroll.create')->only(['create', 'store']);
        $this->middleware('permission:payroll.calculate')->only(['calculate']);
        $this->middleware('permission:payroll.finalize')->only(['finalize']);
        $this->middleware('permission:payroll.reopen')->only(['reopen']);
        $this->middleware('permission:payroll.delete')->only(['destroy']);
    }

    /**
     * Display listing of payroll periods
     */
    public function index(Request $request): View
    {
        $year = (int) $request->get('year', Carbon::now()->year);

        $query = PayrollPeriod::with(['details', 'finalizer'])
            ->where('period_year', $year)
            ->orderBy('period_month', 'desc');

        $periods = $query->get();

        // Summary Bento statistics for the year
        $stats = [
            'total_periods' => $periods->count(),
            'total_employees_paid' => $periods->where('status', 'FINALIZED')->sum('employee_count'),
            'total_thp_year' => $periods->where('status', 'FINALIZED')->sum('total_net'),
            'pending_review' => $periods->whereIn('status', ['DRAFT', 'CALCULATED', 'REVIEW'])->count(),
        ];

        return view('keuangan.payroll.periods.index', compact('periods', 'stats', 'year'));
    }

    /**
     * Show form to create new payroll period
     */
    public function create(): View
    {
        $currentDate = Carbon::now();
        $nextMonth = $currentDate->copy()->addMonth();

        $suggestedMonth = $nextMonth->month;
        $suggestedYear = $nextMonth->year;

        // Default 26 - 25 cutoff dates
        $currMonthDate = Carbon::createFromDate($suggestedYear, $suggestedMonth, 1);
        $prevMonthDate = $currMonthDate->copy()->subMonth();

        $defaultCutoffStart = $prevMonthDate->copy()->day(26)->toDateString();
        $defaultCutoffEnd = $currMonthDate->copy()->day(25)->toDateString();
        $defaultPaymentDate = $currMonthDate->copy()->endOfMonth()->toDateString();

        return view('keuangan.payroll.periods.create', compact(
            'suggestedMonth',
            'suggestedYear',
            'defaultCutoffStart',
            'defaultCutoffEnd',
            'defaultPaymentDate'
        ));
    }

    /**
     * Store new payroll period
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'period_month' => 'required|integer|between:1,12',
            'period_year' => 'required|integer|between:2020,2035',
            'cutoff_start' => 'required|date',
            'cutoff_end' => 'required|date|after:cutoff_start',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $exists = PayrollPeriod::where('period_year', $request->period_year)
            ->where('period_month', $request->period_month)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', "Periode payroll untuk {$request->period_month}/{$request->period_year} sudah ada.");
        }

        try {
            $period = $this->payrollService->getOrCreatePeriod(
                (int) $request->period_month,
                (int) $request->period_year,
                $request->cutoff_start,
                $request->cutoff_end,
                $request->payment_date
            );

            if ($request->filled('notes')) {
                $period->update(['notes' => $request->notes]);
            }

            return redirect()->route('payroll.show', $period->id)
                ->with('success', "Periode {$period->formatted_period} berhasil dibuka.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal membuat periode: ' . $e->getMessage());
        }
    }

    /**
     * Display payroll calculation details for specific period
     */
    public function show(PayrollPeriod $period, Request $request): View
    {
        $period->load(['finalizer']);

        $query = PayrollDetail::with(['karyawan.departemen', 'karyawan.jabatan'])
            ->where('payroll_period_id', $period->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('karyawan', function ($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kode_dept')) {
            $dept = $request->kode_dept;
            $query->whereHas('karyawan', function ($q) use ($dept) {
                $q->where('kode_dept', $dept);
            });
        }

        $details = $query->paginate(20)->withQueryString();

        $stats = [
            'total_gross' => $period->total_gross,
            'total_net' => $period->total_net,
            'total_deductions' => $period->total_deductions,
            'total_employees' => $period->employee_count,
        ];

        return view('keuangan.payroll.periods.show', compact('period', 'details', 'stats'));
    }

    /**
     * Trigger batch calculation for the period
     */
    public function calculate(PayrollPeriod $period): RedirectResponse
    {
        if ($period->status === 'FINALIZED') {
            return back()->with('error', 'Periode yang sudah difinalisasi tidak dapat dihitung ulang.');
        }

        try {
            $result = $this->payrollService->calculatePeriod($period);

            return back()->with('success', "Kalkulasi payroll berhasil diproses untuk {$result['total_employees']} karyawan.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses kalkulasi: ' . $e->getMessage());
        }
    }

    /**
     * Finalize payroll period
     */
    public function finalize(PayrollPeriod $period): RedirectResponse
    {
        if ($period->details()->count() === 0) {
            return back()->with('error', 'Kalkulasi payroll harus dijalankan terlebih dahulu sebelum difinalisasi.');
        }

        try {
            $userId = auth()->id() ?? 1;
            $this->payrollService->finalizePeriod($period, $userId);

            return back()->with('success', "Periode payroll {$period->formatted_period} resmi difinalisasi dan dikunci.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal finalisasi: ' . $e->getMessage());
        }
    }

    /**
     * Reopen finalized period
     */
    public function reopen(PayrollPeriod $period): RedirectResponse
    {
        try {
            $this->payrollService->reopenPeriod($period);

            return back()->with('success', "Periode payroll {$period->formatted_period} dibuka kembali untuk revisi.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuka kembali periode: ' . $e->getMessage());
        }
    }

    /**
     * Delete payroll period (only if DRAFT)
     */
    public function destroy(PayrollPeriod $period): RedirectResponse
    {
        if ($period->status === 'FINALIZED') {
            return back()->with('error', 'Periode final tidak dapat dihapus.');
        }

        $formatted = $period->formatted_period;
        $period->delete();

        return redirect()->route('payroll.index')
            ->with('success', "Periode payroll {$formatted} berhasil dihapus.");
    }
}
