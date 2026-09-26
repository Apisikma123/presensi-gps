<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\ThrPayment;
use App\Models\ThrPaymentDetail;
use App\Services\THRService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ThrPaymentController extends Controller
{
    /**
     * Display list of THR payment events
     */
    public function index(Request $request): View
    {
        $query = ThrPayment::with('creator')->latest();

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->paginate(15)->withQueryString();
        $years = ThrPayment::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

        return view('keuangan.thr.index', compact('payments', 'years'));
    }

    /**
     * Show form for creating a new THR event
     */
    public function create(): View
    {
        $currentYear = (int) date('Y');

        return view('keuangan.thr.create', compact('currentYear'));
    }

    /**
     * Store new THR event and auto-calculate details
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:150',
            'year' => 'required|integer|min:2020|max:2050',
            'distribution_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'DRAFT';

        $payment = ThrPayment::create($validated);

        // Auto process calculation
        THRService::processEventCalculation($payment);

        return redirect()->route('thr.show', $payment->id)
            ->with('success', 'Event THR berhasil dibuat dan kalkulasi otomatis masa kerja telah selesai.');
    }

    /**
     * Display detail of a THR event and employee allocations
     */
    public function show(Request $request, ThrPayment $thr): View
    {
        $thr->load('creator');

        $query = ThrPaymentDetail::where('thr_payment_id', $thr->id)
            ->with(['karyawan.dpt', 'karyawan.cabang']);

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

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $details = $query->paginate(20)->withQueryString();
        $departemen = Departemen::orderBy('nama_dept')->get();
        $cabang = Cabang::orderBy('nama_cabang')->get();

        $stats = [
            'total_full' => ThrPaymentDetail::where('thr_payment_id', $thr->id)->where('category', 'FULL')->count(),
            'total_prorata' => ThrPaymentDetail::where('thr_payment_id', $thr->id)->where('category', 'PRORATA')->count(),
            'total_excluded' => ThrPaymentDetail::where('thr_payment_id', $thr->id)->where('category', 'EXCLUDED')->count(),
        ];

        return view('keuangan.thr.show', compact('thr', 'details', 'departemen', 'cabang', 'stats'));
    }

    /**
     * Recalculate event details
     */
    public function calculate(ThrPayment $thr): RedirectResponse
    {
        if (in_array($thr->status, ['FINALIZED', 'PAID'])) {
            return redirect()->back()->with('error', 'Event THR yang sudah final atau terbayar tidak dapat dihitung ulang.');
        }

        THRService::processEventCalculation($thr);

        return redirect()->back()->with('success', 'Kalkulasi THR seluruh karyawan aktif berhasil diperbarui.');
    }

    /**
     * Finalize THR event
     */
    public function finalize(ThrPayment $thr): RedirectResponse
    {
        $thr->update(['status' => 'FINALIZED']);

        return redirect()->back()->with('success', 'Status Event THR berhasil di-finalisasi.');
    }

    /**
     * Export THR details to CSV
     */
    public function export(ThrPayment $thr): Response
    {
        $details = ThrPaymentDetail::where('thr_payment_id', $thr->id)
            ->with(['karyawan.dpt', 'karyawan.cabang'])
            ->get();

        $filename = 'THR_' . str_replace(' ', '_', $thr->event_name) . '_' . date('Ymd_His') . '.csv';

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, [
            'NIK',
            'Nama Karyawan',
            'Departemen',
            'Cabang',
            'Tanggal Masuk',
            'Masa Kerja (Bulan)',
            'Gaji Pokok / Upah Dasar',
            'Pengali',
            'Nominal THR (IDR)',
            'Kategori',
        ]);

        foreach ($details as $detail) {
            fputcsv($handle, [
                $detail->nik,
                $detail->karyawan?->nama_karyawan ?? '-',
                $detail->karyawan?->dpt?->nama_dept ?? '-',
                $detail->karyawan?->cabang?->nama_cabang ?? '-',
                $detail->hire_date ? $detail->hire_date->format('Y-m-d') : '-',
                $detail->service_months,
                $detail->base_salary,
                $detail->multiplier,
                $detail->thr_amount,
                $detail->category,
            ]);
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
