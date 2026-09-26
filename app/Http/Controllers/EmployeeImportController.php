<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Services\EmployeeImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EmployeeImportController extends Controller
{
    protected EmployeeImportService $importService;

    public function __construct(EmployeeImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Display import & bulk management view
     */
    public function index()
    {
        $karyawans = Karyawan::with(['departemen', 'cabang', 'jabatan'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $cabangs = Cabang::orderBy('nama_cabang')->get();
        $departemens = Departemen::orderBy('nama_dept')->get();
        $jabatans = Jabatan::orderBy('nama_jabatan')->get();

        return view('kepegawaian.import.index', compact('karyawans', 'cabangs', 'departemens', 'jabatans'));
    }

    /**
     * Download sample CSV template
     */
    public function downloadTemplate(): Response
    {
        $csv = $this->importService->generateTemplate();

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="template_import_karyawan.csv"',
        ]);
    }

    /**
     * Process CSV file upload
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('csv_file');
        $result = $this->importService->importFromCsv($file->getRealPath(), Auth::id());

        if ($result['failed_count'] > 0 && $result['success_count'] === 0) {
            return redirect()->back()->with('error', 'Gagal mengimpor data karyawan: ' . implode(' ', array_slice($result['errors'], 0, 3)));
        }

        $msg = "Berhasil mengimpor {$result['success_count']} karyawan.";
        if ($result['failed_count'] > 0) {
            $msg .= " ({$result['failed_count']} data dilewati karena kesalahan format/duplikasi).";
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Bulk update employees
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'niks' => 'required|array|min:1',
            'niks.*' => 'string|exists:karyawan,nik',
            'action_type' => 'required|string|in:change_dept,change_branch,change_status',
        ]);

        $attributes = [];
        if ($request->action_type === 'change_dept' && $request->filled('target_dept')) {
            $attributes['kode_dept'] = $request->target_dept;
        } elseif ($request->action_type === 'change_branch' && $request->filled('target_branch')) {
            $attributes['kode_cabang'] = $request->target_branch;
        } elseif ($request->action_type === 'change_status' && $request->filled('target_status')) {
            $attributes['status_aktif_karyawan'] = $request->target_status;
        }

        $count = $this->importService->bulkUpdate($request->niks, $attributes);

        return redirect()->back()->with('success', "Berhasil memperbarui data {$count} karyawan secara serentak.");
    }
}
