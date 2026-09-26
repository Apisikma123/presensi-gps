<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Division;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class OrgChartController extends Controller
{
    /**
     * Display visual organization chart
     */
    public function index(Request $request)
    {
        $selectedDept = $request->kode_dept;
        $selectedCabang = $request->kode_cabang;

        $cabangs = Cabang::orderBy('nama_cabang')->get();
        $departemens = Departemen::orderBy('nama_dept')->get();

        $query = Departemen::with(['divisi', 'karyawan' => function ($q) use ($selectedCabang) {
            $q->where('status_aktif_karyawan', '1');
            if ($selectedCabang) {
                $q->where('kode_cabang', $selectedCabang);
            }
            $q->with('jabatan')->orderBy('kode_jabatan');
        }]);

        if ($selectedDept) {
            $query->where('kode_dept', $selectedDept);
        }

        $orgTree = $query->get();

        return view('kepegawaian.org_chart.index', compact('orgTree', 'cabangs', 'departemens', 'selectedDept', 'selectedCabang'));
    }
}
