<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\PayrollDetail;
use App\Models\PayrollPeriod;
use App\Models\Userkaryawan;
use App\Services\PayslipService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayslipController extends Controller
{
    /**
     * Admin view of all payslips
     */
    public function index(Request $request): View
    {
        $query = PayrollDetail::with(['karyawan.dpt', 'karyawan.cabang', 'period'])->latest();

        if ($request->filled('payroll_period_id')) {
            $query->where('payroll_period_id', $request->payroll_period_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('karyawan', function ($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kode_dept')) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('kode_dept', $request->kode_dept);
            });
        }

        $payslips = $query->paginate(15)->withQueryString();
        $periods = PayrollPeriod::orderBy('period_year', 'desc')->orderBy('period_month', 'desc')->get();
        $departemen = Departemen::orderBy('nama_dept')->get();

        return view('keuangan.payslip.index', compact('payslips', 'periods', 'departemen'));
    }

    /**
     * Employee Self-Service (ESS) payslips view
     */
    public function myPayslips(): View
    {
        $user = auth()->user();
        $userKaryawan = Userkaryawan::where('id_user', $user->id)->first();
        $nik = $userKaryawan?->nik ?? $user->nik ?? null;

        $payslips = collect();
        if ($nik) {
            $payslips = PayrollDetail::where('nik', $nik)
                ->with('period')
                ->whereHas('period', function ($q) {
                    $q->whereIn('status', ['CALCULATED', 'FINALIZED', 'PAID']);
                })
                ->latest()
                ->paginate(12);
        }

        return view('keuangan.payslip.my_payslips', compact('payslips', 'nik'));
    }

    /**
     * Display a single payslip
     */
    public function show(PayrollDetail $detail): View
    {
        $payslip = PayslipService::getPayslipData($detail);

        return view('keuangan.payslip.show', compact('payslip', 'detail'));
    }

    /**
     * Printable slip gaji layout
     */
    public function print(PayrollDetail $detail): View
    {
        $payslip = PayslipService::getPayslipData($detail);

        return view('keuangan.payslip.print', compact('payslip', 'detail'));
    }
}
