<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Karyawan;
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
        $user = auth()->user();
        $query = PayrollDetail::with(['karyawan.dpt', 'karyawan.cabang', 'period'])->latest();

        // Branch and department scoping for non-superadmin users
        if ($user && !$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!empty($userCabangs)) {
                $query->whereHas('karyawan', fn($q) => $q->whereIn('kode_cabang', $userCabangs));
            }
            if (!empty($userDepartemens)) {
                $query->whereHas('karyawan', fn($q) => $q->whereIn('kode_dept', $userDepartemens));
            }
        }

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

        if ($user && $user->hasRole('karyawan')) {
            return view('keuangan.payslip.my_payslips-mobile', compact('payslips', 'nik'));
        }

        return view('keuangan.payslip.my_payslips', compact('payslips', 'nik'));
    }

    /**
     * Display a single payslip
     */
    public function show(PayrollDetail $detail): View
    {
        $this->authorizePayslipAccess($detail, 'show');

        $payslip = PayslipService::getPayslipData($detail);

        return view('keuangan.payslip.show', compact('payslip', 'detail'));
    }

    /**
     * Printable slip gaji layout
     */
    public function print(PayrollDetail $detail): View
    {
        $this->authorizePayslipAccess($detail, 'print');

        $payslip = PayslipService::getPayslipData($detail);

        return view('keuangan.payslip.print', compact('payslip', 'detail'));
    }

    /**
     * Authorize user access to a specific payslip record
     */
    protected function authorizePayslipAccess(PayrollDetail $detail, string $action = 'show'): void
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
            $userKaryawan = Userkaryawan::where('id_user', $user->id)->first();
            $nik = $userKaryawan?->nik ?? $user->nik ?? null;

            if (!$nik || $detail->nik !== $nik) {
                abort(403, 'Akses ditolak. Anda hanya berhak melihat slip gaji milik Anda sendiri.');
            }
            return;
        }

        // Admin/Staff access: check permission and regional branch/dept scope
        $permission = $action === 'print' ? 'payslip.print' : 'payslip.show';
        if (!$user->can($permission) && !$user->can('payslip.index')) {
            abort(403, 'Akses ditolak. Anda tidak memiliki wewenang untuk melihat slip gaji ini.');
        }

        $karyawan = $detail->karyawan ?? Karyawan::where('nik', $detail->nik)->first();
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
