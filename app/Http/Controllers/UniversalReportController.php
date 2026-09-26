<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Services\UniversalReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UniversalReportController extends Controller
{
    protected UniversalReportService $reportService;

    public function __construct(UniversalReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display unified multi-module reports & analytics
     */
    public function index(Request $request)
    {
        $cabang = $request->kode_cabang;
        $dept = $request->kode_dept;
        $year = (int) ($request->year ?: date('Y'));
        $month = (int) ($request->month ?: date('n'));

        $startDate = $request->start_date ?: Carbon::now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?: Carbon::now()->endOfMonth()->toDateString();

        $headcount = $this->reportService->getHeadcountData($cabang, $dept);
        $turnover = $this->reportService->getTurnoverData($year, $cabang, $dept);
        $attendance = $this->reportService->getAttendanceOvertimeSummary($startDate, $endDate, $cabang, $dept);
        $compensation = $this->reportService->getCompensationSummary($month, $year);
        $governance = $this->reportService->getGovernanceSummary();

        $cabangs = Cabang::orderBy('nama_cabang')->get();
        $departemens = Departemen::orderBy('nama_dept')->get();

        return view('reports.index', compact(
            'headcount',
            'turnover',
            'attendance',
            'compensation',
            'governance',
            'cabangs',
            'departemens',
            'year',
            'month',
            'startDate',
            'endDate',
            'cabang',
            'dept'
        ));
    }

    /**
     * Printable executive report view
     */
    public function print(Request $request)
    {
        $cabang = $request->kode_cabang;
        $dept = $request->kode_dept;
        $year = (int) ($request->year ?: date('Y'));
        $month = (int) ($request->month ?: date('n'));
        $startDate = $request->start_date ?: Carbon::now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?: Carbon::now()->endOfMonth()->toDateString();

        $headcount = $this->reportService->getHeadcountData($cabang, $dept);
        $turnover = $this->reportService->getTurnoverData($year, $cabang, $dept);
        $attendance = $this->reportService->getAttendanceOvertimeSummary($startDate, $endDate, $cabang, $dept);
        $compensation = $this->reportService->getCompensationSummary($month, $year);
        $governance = $this->reportService->getGovernanceSummary();

        return view('reports.print', compact(
            'headcount',
            'turnover',
            'attendance',
            'compensation',
            'governance',
            'year',
            'month',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export workforce data as CSV
     */
    public function export(Request $request): StreamedResponse
    {
        $cabang = $request->kode_cabang;
        $dept = $request->kode_dept;
        $headcount = $this->reportService->getHeadcountData($cabang, $dept);

        $response = new StreamedResponse(function () use ($headcount) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Laporan Ringkasan Ketenagakerjaan Presence HR']);
            fputcsv($handle, ['Total Terdaftar', $headcount['total_registered']]);
            fputcsv($handle, ['Karyawan Aktif', $headcount['active_headcount']]);
            fputcsv($handle, ['Karyawan Non-Aktif', $headcount['inactive_headcount']]);
            fputcsv($handle, []);
            fputcsv($handle, ['Departemen', 'Jumlah Karyawan']);
            foreach ($headcount['by_department'] as $d) {
                fputcsv($handle, [$d['label'], $d['count']]);
            }
            fputcsv($handle, []);
            fputcsv($handle, ['Cabang', 'Jumlah Karyawan']);
            foreach ($headcount['by_branch'] as $b) {
                fputcsv($handle, [$b['label'], $b['count']]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="rekap_tenaga_kerja_' . date('Ymd_His') . '.csv"');

        return $response;
    }
}
