<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Cuti;
use App\Models\Departemen;
use App\Models\Karyawan;
use App\Models\Pengaturanumum;
use App\Models\Presensi;
use App\Models\PresensiDispensasi;
use App\Models\Jamkerja;
use App\Models\User;
use App\Services\AttendanceService;
use App\Exports\PresensiExport;
use App\Exports\PresensiKaryawanExport;
use App\Exports\CutiExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    /**
     * View Rekap / Laporan Cuti
     */
    public function cuti()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $data['list_bulan'] = config('global.list_bulan');
        $data['start_year'] = config('global.start_year');
        $data['cabang'] = $user->getCabang();
        $data['departemen'] = $user->getDepartemen();
        $data['cuti'] = Cuti::orderBy('kode_cuti')->get();
        return view('laporan.cuti', $data);
    }

    /**
     * Cetak Rekap Cuti
     */
    public function cetakcuti(Request $request)
    {
        $tahun = $request->tahun;
        $kode_cabang = $request->kode_cabang;
        $kode_dept = $request->kode_dept;
        $kode_cuti = $request->kode_cuti;
        $generalsetting = Pengaturanumum::where('id', 1)->first();

        $query = Karyawan::query();
        $query->orderBy('nama_karyawan');

        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            if (!empty($userCabangs)) {
                $query->whereIn('karyawan.kode_cabang', $userCabangs);
            }
            if (!empty($userDepartemens)) {
                $query->whereIn('karyawan.kode_dept', $userDepartemens);
            }
        }

        if (!empty($kode_cabang)) {
            $query->where('karyawan.kode_cabang', $kode_cabang);
        }
        if (!empty($kode_dept)) {
            $query->where('karyawan.kode_dept', $kode_dept);
        }

        $cuti_approve = DB::table('presensi_izincuti_approve')
            ->join('presensi_izincuti', 'presensi_izincuti_approve.kode_izin_cuti', '=', 'presensi_izincuti.kode_izin_cuti')
            ->join('presensi', 'presensi_izincuti_approve.id_presensi', '=', 'presensi.id')
            ->whereYear('presensi.tanggal', $tahun);

        if (!empty($kode_cuti)) {
            $cuti_approve->where('presensi_izincuti.kode_cuti', $kode_cuti);
        }

        $cuti_per_bulan = $cuti_approve->select(
            'presensi.nik',
            DB::raw('MONTH(presensi.tanggal) as bulan'),
            DB::raw('COUNT(presensi.id) as total_cuti')
        )
            ->groupBy('presensi.nik', DB::raw('MONTH(presensi.tanggal)'))
            ->get();

        $rekap_cuti = [];
        foreach ($cuti_per_bulan as $item) {
            $rekap_cuti[$item->nik][$item->bulan] = $item->total_cuti;
        }

        $karyawan = $query->get();
        $data = [
            'tahun' => $tahun,
            'karyawan' => $karyawan,
            'generalsetting' => $generalsetting,
            'rekap_cuti' => $rekap_cuti,
            'monthly_quota' => $generalsetting->monthly_leave_quota ?? 3
        ];

        if ($request->has('exportButton')) {
            return Excel::download(new CutiExport($data), 'Rekap Cuti ' . $tahun . '.xlsx');
        }

        return view('laporan.cetak_cuti', $data);
    }

    /**
     * View Filter Laporan Presensi
     */
    public function presensi()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $data['list_bulan'] = config('global.list_bulan');
        $data['start_year'] = config('global.start_year');
        $data['cabang'] = $user->getCabang();
        $data['departemen'] = $user->getDepartemen();
        return view('laporan.presensi', $data);
    }

    /**
     * Cetak & Export Laporan Presensi
     */
    public function cetakpresensi(Request $request)
    {
        $generalsetting = Pengaturanumum::where('id', 1)->first();

        // Tentukan range tanggal periode
        if ($request->periode_laporan == 2) {
            $bulan = str_pad($request->bulan, 2, '0', STR_PAD_LEFT);
            $periode_dari = $request->tahun . '-' . $bulan . '-01';
            $periode_sampai = date('Y-m-t', strtotime($periode_dari));
        } elseif ($request->periode_laporan == 3 && !empty($request->dari) && !empty($request->sampai)) {
            $periode_dari = $request->dari;
            $periode_sampai = $request->sampai;
        } else {
            // Default periode bulanan
            $bulan = str_pad($request->bulan ?: date('m'), 2, '0', STR_PAD_LEFT);
            $tahun = $request->tahun ?: date('Y');
            $periode_dari = $tahun . '-' . $bulan . '-01';
            $periode_sampai = date('Y-m-t', strtotime($periode_dari));
        }

        // Query Master Jam Kerja (hanya 2 shift: JK01 & JK02)
        $jamkerja_map = Jamkerja::all()->keyBy('kode_jam_kerja');

        // Query Karyawan
        $queryKaryawan = Karyawan::with(['cabang', 'departemen', 'jabatan', 'jamkerja'])
            ->where('status_aktif_karyawan', '1');

        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            if (!empty($userCabangs)) {
                $queryKaryawan->whereIn('kode_cabang', $userCabangs);
            }
            if (!empty($userDepartemens)) {
                $queryKaryawan->whereIn('kode_dept', $userDepartemens);
            }
        }

        if (!empty($request->nik)) {
            $queryKaryawan->where('nik', $request->nik);
        }
        if (!empty($request->kode_cabang)) {
            $queryKaryawan->where('kode_cabang', $request->kode_cabang);
        }
        if (!empty($request->kode_dept)) {
            $queryKaryawan->where('kode_dept', $request->kode_dept);
        }

        $karyawanList = $queryKaryawan->orderBy('nama_karyawan')->get();

        // Query Presensi actual
        $presensiQuery = Presensi::leftJoin('presensi_izinabsen_approve', 'presensi.id', '=', 'presensi_izinabsen_approve.id_presensi')
            ->leftJoin('presensi_izinabsen', 'presensi_izinabsen_approve.kode_izin', '=', 'presensi_izinabsen.kode_izin')
            ->leftJoin('presensi_izinsakit_approve', 'presensi.id', '=', 'presensi_izinsakit_approve.id_presensi')
            ->leftJoin('presensi_izinsakit', 'presensi_izinsakit_approve.kode_izin_sakit', '=', 'presensi_izinsakit.kode_izin_sakit')
            ->leftJoin('presensi_izincuti_approve', 'presensi.id', '=', 'presensi_izincuti_approve.id_presensi')
            ->leftJoin('presensi_izincuti', 'presensi_izincuti_approve.kode_izin_cuti', '=', 'presensi_izincuti.kode_izin_cuti')
            ->select(
                'presensi.*',
                'presensi_izinabsen.keterangan as keterangan_izin',
                'presensi_izinsakit.keterangan as keterangan_sakit',
                'presensi_izincuti.keterangan as keterangan_cuti'
            )
            ->whereBetween('presensi.tanggal', [$periode_dari, $periode_sampai]);

        if (!empty($request->nik)) {
            $presensiQuery->where('presensi.nik', $request->nik);
        }

        $presensiRows = $presensiQuery->get();

        // Index presensi by nik|tanggal
        $presensiMap = [];
        foreach ($presensiRows as $p) {
            $presensiMap[$p->nik . '|' . $p->tanggal] = $p;
        }

        // Query Approved Dispensasi in period
        $dispensasiRows = PresensiDispensasi::where('status', 'APPROVED')
            ->whereBetween('tanggal', [$periode_dari, $periode_sampai])
            ->get();
        $dispensasiMap = [];
        foreach ($dispensasiRows as $d) {
            $dispensasiMap[$d->nik . '|' . $d->tanggal] = $d;
        }

        // Hari Libur
        $hariLibur = DB::table('hari_libur')
            ->whereBetween('tanggal_libur', [$periode_dari, $periode_sampai])
            ->get()
            ->keyBy('tanggal_libur');

        $data = [
            'generalsetting' => $generalsetting,
            'periode_dari' => $periode_dari,
            'periode_sampai' => $periode_sampai,
            'karyawanList' => $karyawanList,
            'presensiMap' => $presensiMap,
            'dispensasiMap' => $dispensasiMap,
            'jamkerja_map' => $jamkerja_map,
            'hariLibur' => $hariLibur,
        ];

        // Format 1: Per Karyawan Detail jika NIK dipilih
        if (!empty($request->nik)) {
            $data['karyawan'] = $karyawanList->first();
            if (!$data['karyawan']) {
                return redirect()->back()->with(messageError('Karyawan tidak ditemukan atau di luar wewenang cabang Anda.'));
            }
            if ($request->has('exportButton')) {
                return Excel::download(new PresensiKaryawanExport($data), 'Laporan_Presensi_' . $request->nik . '_' . $periode_dari . '.xlsx');
            }
            return view('laporan.presensi_karyawan_cetak', $data);
        }

        // Format Rekap Keseluruhan Karyawan
        if ($request->has('exportButton')) {
            return Excel::download(new PresensiExport($data, 'laporan.presensi_excel'), 'Rekap_Presensi_' . $periode_dari . '_sd_' . $periode_sampai . '.xlsx');
        }

        return view('laporan.presensi_cetak', $data);
    }
}
