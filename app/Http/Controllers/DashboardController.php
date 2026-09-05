<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\PresensiDispensasi;
use App\Models\User;
use App\Models\Userkaryawan;
use App\Models\Pengaturanumum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $agent = new Agent();
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $hari_ini = Carbon::now(config('app.timezone'))->format('Y-m-d');

        // 1. Dashboard Karyawan
        if ($user->hasRole('karyawan')) {
            $userkaryawan = $user->userkaryawan ?? Userkaryawan::where('id_user', $user->id)->first();
            $data['karyawan'] = Karyawan::where('nik', $userkaryawan->nik)
                ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
                ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                ->leftJoin('presensi_jamkerja', 'karyawan.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->select('karyawan.*', 'jabatan.nama_jabatan', 'departemen.nama_dept', 'cabang.nama_cabang', 'presensi_jamkerja.nama_jam_kerja')
                ->first();

            $data['presensi'] = Presensi::where('presensi.nik', $userkaryawan->nik)->where('presensi.tanggal', $hari_ini)->first();
            $data['datapresensi'] = Presensi::leftJoin('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->where('presensi.nik', $userkaryawan->nik)
                ->leftJoin('presensi_izinabsen_approve', 'presensi.id', '=', 'presensi_izinabsen_approve.id_presensi')
                ->leftJoin('presensi_izinabsen', 'presensi_izinabsen_approve.kode_izin', '=', 'presensi_izinabsen.kode_izin')
                ->leftJoin('presensi_izinsakit_approve', 'presensi.id', '=', 'presensi_izinsakit_approve.id_presensi')
                ->leftJoin('presensi_izinsakit', 'presensi_izinsakit_approve.kode_izin_sakit', '=', 'presensi_izinsakit.kode_izin_sakit')
                ->leftJoin('presensi_izincuti_approve', 'presensi.id', '=', 'presensi_izincuti_approve.id_presensi')
                ->leftJoin('presensi_izincuti', 'presensi_izincuti_approve.kode_izin_cuti', '=', 'presensi_izincuti.kode_izin_cuti')
                ->select(
                    'presensi.*',
                    'presensi_jamkerja.nama_jam_kerja',
                    'presensi_jamkerja.jam_masuk',
                    'presensi_jamkerja.jam_pulang',
                    'presensi_jamkerja.batas_toleransi',
                    'presensi_izinabsen.keterangan as keterangan_izin',
                    'presensi_izinsakit.keterangan as keterangan_izin_sakit',
                    'presensi_izincuti.keterangan as keterangan_izin_cuti'
                )
                ->orderBy('presensi.tanggal', 'desc')
                ->limit(30)
                ->get();

            $startOfMonth = Carbon::parse($hari_ini)->startOfMonth()->toDateString();
            $endOfMonth = Carbon::parse($hari_ini)->endOfMonth()->toDateString();
            $data['rekappresensi'] = Presensi::select(
                DB::raw("SUM(IF(status='h',1,0)) as hadir"),
                DB::raw("SUM(IF(status='i',1,0)) as izin"),
                DB::raw("SUM(IF(status='s',1,0)) as sakit"),
                DB::raw("SUM(IF(status='a',1,0)) as alpa"),
                DB::raw("SUM(IF(status='c',1,0)) as cuti")
            )
                ->where('presensi.nik', $userkaryawan->nik)
                ->whereBetween('presensi.tanggal', [$startOfMonth, $endOfMonth])
                ->first();

            $data['namasettings'] = Pengaturanumum::getSetting();
            $data['bulan_skrg'] = Carbon::parse($hari_ini)->translatedFormat('F');
            $data['tahun_skrg'] = Carbon::parse($hari_ini)->year;

            return view('dashboard.karyawan', $data);
        }

        // 2. Dashboard Admin
        $userCabangs = $user->getCabangCodes();
        $userDepartemens = $user->getDepartemenCodes();
        
        $selectedCabang = $request->kode_cabang;
        $selectedDept = $request->kode_dept;

        if (!$user->isSuperAdmin()) {
            if (empty($userCabangs) || empty($userDepartemens)) {
                $targetCabangs = ['INVALID'];
                $targetDepartemens = ['INVALID'];
            } else {
                $targetCabangs = !empty($selectedCabang) && in_array($selectedCabang, $userCabangs) ? [$selectedCabang] : $userCabangs;
                $targetDepartemens = !empty($selectedDept) && in_array($selectedDept, $userDepartemens) ? [$selectedDept] : $userDepartemens;
            }
        } else {
            $targetCabangs = !empty($selectedCabang) ? [$selectedCabang] : [];
            $targetDepartemens = !empty($selectedDept) ? [$selectedDept] : [];
        }

        $tglPresensi = !empty($request->tanggal) ? $request->tanggal : $hari_ini;

        // Query Total Karyawan Aktif
        $needsKaryawanJoin = !empty($targetCabangs) || !empty($targetDepartemens);

        // 1. Total Karyawan Aktif
        $totalAktifQuery = Karyawan::where('status_aktif_karyawan', '1');
        if (!empty($targetCabangs)) {
            $totalAktifQuery->whereIn('kode_cabang', $targetCabangs);
        }
        if (!empty($targetDepartemens)) {
            $totalAktifQuery->whereIn('kode_dept', $targetDepartemens);
        }
        $totalKaryawan = $totalAktifQuery->count();

        // 2. Presensi Hari Ini (Single-pass server aggregation, no PHP loop on all rows)
        $summaryQuery = Presensi::leftJoin('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->where('presensi.tanggal', $tglPresensi);

        if ($needsKaryawanJoin) {
            $summaryQuery->join('karyawan', 'presensi.nik', '=', 'karyawan.nik');
            if (!empty($targetCabangs)) {
                $summaryQuery->whereIn('karyawan.kode_cabang', $targetCabangs);
            }
            if (!empty($targetDepartemens)) {
                $summaryQuery->whereIn('karyawan.kode_dept', $targetDepartemens);
            }
        }

        $summary = $summaryQuery->select(
            DB::raw("SUM(CASE WHEN presensi.status = 'h' AND (presensi.is_dispensasi = 1 OR TIME(presensi.jam_in) <= COALESCE(presensi_jamkerja.batas_toleransi, '07:05:00')) THEN 1 ELSE 0 END) as jml_hadir"),
            DB::raw("SUM(CASE WHEN presensi.status = 'h' AND presensi.is_dispensasi != 1 AND TIME(presensi.jam_in) > COALESCE(presensi_jamkerja.batas_toleransi, '07:05:00') THEN 1 ELSE 0 END) as jml_telat"),
            DB::raw("SUM(CASE WHEN presensi.status = 'h' AND presensi.is_dispensasi = 1 THEN 1 ELSE 0 END) as jml_dispensasi"),
            DB::raw("SUM(CASE WHEN presensi.status = 'i' THEN 1 ELSE 0 END) as jml_izin"),
            DB::raw("SUM(CASE WHEN presensi.status = 's' THEN 1 ELSE 0 END) as jml_sakit"),
            DB::raw("SUM(CASE WHEN presensi.status = 'c' THEN 1 ELSE 0 END) as jml_cuti")
        )->first();

        $jmlHadir = (int) ($summary->jml_hadir ?? 0);
        $jmlTelat = (int) ($summary->jml_telat ?? 0);
        $jmlDispensasi = (int) ($summary->jml_dispensasi ?? 0);
        $jmlIzin = (int) ($summary->jml_izin ?? 0);
        $jmlSakit = (int) ($summary->jml_sakit ?? 0);
        $jmlCuti = (int) ($summary->jml_cuti ?? 0);

        $totalTercatat = $jmlHadir + $jmlTelat + $jmlIzin + $jmlSakit + $jmlCuti;
        $tidakHadir = max(0, $totalKaryawan - $totalTercatat);

        // 3. Query Pengajuan Pending (Cached for 30s)
        $cacheKeyPending = 'dashboard_pending_count_' . ($user->isSuperAdmin() ? 'all' : implode('_', $targetCabangs) . '_' . implode('_', $targetDepartemens));
        $pendingApproval = Cache::remember($cacheKeyPending, 30, function () use ($needsKaryawanJoin, $targetCabangs, $targetDepartemens) {
            $qIzin = DB::table('presensi_izinabsen')->where('status', '0');
            $qSakit = DB::table('presensi_izinsakit')->where('status', '0');
            $qCuti = DB::table('presensi_izincuti')->where('status', '0');
            $qDisp = PresensiDispensasi::where('status', 'PENDING');

            if ($needsKaryawanJoin) {
                $qIzin->join('karyawan', 'presensi_izinabsen.nik', '=', 'karyawan.nik');
                $qSakit->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik');
                $qCuti->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik');
                $qDisp->join('karyawan', 'presensi_dispensasi.nik', '=', 'karyawan.nik');

                if (!empty($targetCabangs)) {
                    $qIzin->whereIn('karyawan.kode_cabang', $targetCabangs);
                    $qSakit->whereIn('karyawan.kode_cabang', $targetCabangs);
                    $qCuti->whereIn('karyawan.kode_cabang', $targetCabangs);
                    $qDisp->whereIn('karyawan.kode_cabang', $targetCabangs);
                }
                if (!empty($targetDepartemens)) {
                    $qIzin->whereIn('karyawan.kode_dept', $targetDepartemens);
                    $qSakit->whereIn('karyawan.kode_dept', $targetDepartemens);
                    $qCuti->whereIn('karyawan.kode_dept', $targetDepartemens);
                    $qDisp->whereIn('karyawan.kode_dept', $targetDepartemens);
                }
            }

            return $qIzin->count() + $qSakit->count() + $qCuti->count() + $qDisp->count();
        });

        // 4. CHART 1: TREN KEHADIRAN (7 Hari Terakhir - conditional join)
        $startDate = Carbon::parse($tglPresensi)->subDays(6)->toDateString();
        $datePeriod = [];
        $dateLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $currDate = Carbon::parse($tglPresensi)->subDays($i);
            $dateStr = $currDate->toDateString();
            $datePeriod[] = $dateStr;
            $dateLabels[] = $currDate->translatedFormat('d M');
        }

        $trendQuery = Presensi::leftJoin('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->whereBetween('presensi.tanggal', [$startDate, $tglPresensi])
            ->where('presensi.status', 'h');

        if ($needsKaryawanJoin) {
            $trendQuery->join('karyawan', 'presensi.nik', '=', 'karyawan.nik');
            if (!empty($targetCabangs)) {
                $trendQuery->whereIn('karyawan.kode_cabang', $targetCabangs);
            }
            if (!empty($targetDepartemens)) {
                $trendQuery->whereIn('karyawan.kode_dept', $targetDepartemens);
            }
        }

        $trendAgg = $trendQuery->select(
            'presensi.tanggal',
            DB::raw("SUM(CASE WHEN (presensi.is_dispensasi = 1 OR TIME(presensi.jam_in) <= COALESCE(presensi_jamkerja.batas_toleransi, '07:05:00')) THEN 1 ELSE 0 END) as jml_hadir"),
            DB::raw("SUM(CASE WHEN (presensi.is_dispensasi != 1 AND TIME(presensi.jam_in) > COALESCE(presensi_jamkerja.batas_toleransi, '07:05:00')) THEN 1 ELSE 0 END) as jml_telat")
        )->groupBy('presensi.tanggal')->get()->keyBy('tanggal');

        $chart1Hadir = [];
        $chart1Telat = [];
        foreach ($datePeriod as $dStr) {
            $row = $trendAgg->get($dStr);
            $chart1Hadir[] = $row ? (int) $row->jml_hadir : 0;
            $chart1Telat[] = $row ? (int) $row->jml_telat : 0;
        }

        // 5. CHART 2: STATUS ABSENSI HARI INI (Donut)
        $chart2Labels = ['Hadir Tepat Waktu', 'Terlambat', 'Izin', 'Sakit', 'Cuti', 'Tidak Hadir'];
        $chart2Series = [$jmlHadir, $jmlTelat, $jmlIzin, $jmlSakit, $jmlCuti, $tidakHadir];

        // 6. CHART 3: PERBANDINGAN SHIFT HARI INI (Grouped Bar - conditional join)
        $shiftQuery = Presensi::join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->where('presensi.tanggal', $tglPresensi)
            ->where('presensi.status', 'h');

        if ($needsKaryawanJoin) {
            $shiftQuery->join('karyawan', 'presensi.nik', '=', 'karyawan.nik');
            if (!empty($targetCabangs)) {
                $shiftQuery->whereIn('karyawan.kode_cabang', $targetCabangs);
            }
            if (!empty($targetDepartemens)) {
                $shiftQuery->whereIn('karyawan.kode_dept', $targetDepartemens);
            }
        }

        $shiftAgg = $shiftQuery->select(
            'presensi_jamkerja.nama_jam_kerja',
            DB::raw("SUM(CASE WHEN (presensi.is_dispensasi = 1 OR TIME(presensi.jam_in) <= COALESCE(presensi_jamkerja.batas_toleransi, '07:05:00')) THEN 1 ELSE 0 END) as jml_hadir"),
            DB::raw("SUM(CASE WHEN (presensi.is_dispensasi != 1 AND TIME(presensi.jam_in) > COALESCE(presensi_jamkerja.batas_toleransi, '07:05:00')) THEN 1 ELSE 0 END) as jml_telat")
        )->groupBy('presensi_jamkerja.nama_jam_kerja')->get();

        $chart3Categories = [];
        $chart3Hadir = [];
        $chart3Telat = [];
        if ($shiftAgg->isNotEmpty()) {
            foreach ($shiftAgg as $s) {
                $chart3Categories[] = $s->nama_jam_kerja;
                $chart3Hadir[] = (int) $s->jml_hadir;
                $chart3Telat[] = (int) $s->jml_telat;
            }
        } else {
            $allShifts = Jamkerja::getAllShifts();
            foreach ($allShifts as $as) {
                $chart3Categories[] = $as->nama_jam_kerja;
                $chart3Hadir[] = 0;
                $chart3Telat[] = 0;
            }
        }

        // 7. CHART 4: IZIN, SAKIT & CUTI (7 Hari Terakhir - conditional join)
        $leaveQuery = Presensi::whereBetween('presensi.tanggal', [$startDate, $tglPresensi])
            ->whereIn('presensi.status', ['i', 's', 'c']);

        if ($needsKaryawanJoin) {
            $leaveQuery->join('karyawan', 'presensi.nik', '=', 'karyawan.nik');
            if (!empty($targetCabangs)) {
                $leaveQuery->whereIn('karyawan.kode_cabang', $targetCabangs);
            }
            if (!empty($targetDepartemens)) {
                $leaveQuery->whereIn('karyawan.kode_dept', $targetDepartemens);
            }
        }

        $leaveAgg = $leaveQuery->select(
            'presensi.tanggal',
            DB::raw("SUM(CASE WHEN presensi.status = 'i' THEN 1 ELSE 0 END) as jml_izin"),
            DB::raw("SUM(CASE WHEN presensi.status = 's' THEN 1 ELSE 0 END) as jml_sakit"),
            DB::raw("SUM(CASE WHEN presensi.status = 'c' THEN 1 ELSE 0 END) as jml_cuti")
        )->groupBy('presensi.tanggal')->get()->keyBy('tanggal');

        $chart4Izin = [];
        $chart4Sakit = [];
        $chart4Cuti = [];
        foreach ($datePeriod as $dStr) {
            $row = $leaveAgg->get($dStr);
            $chart4Izin[] = $row ? (int) $row->jml_izin : 0;
            $chart4Sakit[] = $row ? (int) $row->jml_sakit : 0;
            $chart4Cuti[] = $row ? (int) $row->jml_cuti : 0;
        }

        // 8. OPERATIONAL OVERVIEW: PENGAJUAN TERBARU (Pending Approvals - Cached 30s)
        $cacheKeyFeeds = 'dashboard_recent_pending_' . ($user->isSuperAdmin() ? 'all' : implode('_', $targetCabangs) . '_' . implode('_', $targetDepartemens));
        $pengajuanTerbaru = Cache::remember($cacheKeyFeeds, 30, function () use ($targetCabangs, $targetDepartemens, $needsKaryawanJoin) {
            $q1 = DB::table('presensi_izinabsen')
                ->join('karyawan', 'presensi_izinabsen.nik', '=', 'karyawan.nik')
                ->where('presensi_izinabsen.status', '0');
            $q2 = DB::table('presensi_izinsakit')
                ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
                ->where('presensi_izinsakit.status', '0');
            $q3 = DB::table('presensi_izincuti')
                ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
                ->where('presensi_izincuti.status', '0');

            if ($needsKaryawanJoin) {
                if (!empty($targetCabangs)) {
                    $q1->whereIn('karyawan.kode_cabang', $targetCabangs);
                    $q2->whereIn('karyawan.kode_cabang', $targetCabangs);
                    $q3->whereIn('karyawan.kode_cabang', $targetCabangs);
                }
                if (!empty($targetDepartemens)) {
                    $q1->whereIn('karyawan.kode_dept', $targetDepartemens);
                    $q2->whereIn('karyawan.kode_dept', $targetDepartemens);
                    $q3->whereIn('karyawan.kode_dept', $targetDepartemens);
                }
            }

            $pendingIzinList = $q1->select('presensi_izinabsen.kode_izin as id', 'karyawan.nama_karyawan', 'karyawan.foto', DB::raw("'Izin Absen' as tipe"), 'presensi_izinabsen.tanggal', 'presensi_izinabsen.keterangan', DB::raw("'i' as kode_tipe"))->limit(5)->get();
            $pendingSakitList = $q2->select('presensi_izinsakit.kode_izin_sakit as id', 'karyawan.nama_karyawan', 'karyawan.foto', DB::raw("'Izin Sakit' as tipe"), 'presensi_izinsakit.tanggal', 'presensi_izinsakit.keterangan', DB::raw("'s' as kode_tipe"))->limit(5)->get();
            $pendingCutiList = $q3->select('presensi_izincuti.kode_izin_cuti as id', 'karyawan.nama_karyawan', 'karyawan.foto', DB::raw("'Izin Cuti' as tipe"), 'presensi_izincuti.tanggal', 'presensi_izincuti.keterangan', DB::raw("'c' as kode_tipe"))->limit(5)->get();

            return collect()
                ->concat($pendingIzinList)
                ->concat($pendingSakitList)
                ->concat($pendingCutiList)
                ->sortByDesc('tanggal')
                ->take(5);
        });

        // 6. OPERATIONAL OVERVIEW: AKTIVITAS KEHADIRAN TERBARU
        $recentClockInQuery = Presensi::join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->leftJoin('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->where('presensi.tanggal', $tglPresensi)
            ->where('presensi.status', 'h');

        if (!empty($targetCabangs)) {
            $recentClockInQuery->whereIn('karyawan.kode_cabang', $targetCabangs);
        }
        if (!empty($targetDepartemens)) {
            $recentClockInQuery->whereIn('karyawan.kode_dept', $targetDepartemens);
        }

        $aktivitasTerbaru = $recentClockInQuery->select(
            'karyawan.nama_karyawan',
            'karyawan.foto',
            'karyawan.nik_show',
            'cabang.nama_cabang',
            'presensi_jamkerja.nama_jam_kerja',
            'presensi.jam_in',
            'presensi.jam_out',
            'presensi.is_dispensasi',
            'presensi_jamkerja.batas_toleransi'
        )
        ->orderBy('presensi.jam_in', 'desc')
        ->limit(6)
        ->get();

        $data = [
            'tanggal' => $tglPresensi,
            'total_karyawan' => $totalKaryawan,
            'hadir_hari_ini' => $jmlHadir,
            'telat_hari_ini' => $jmlTelat,
            'dispensasi_hari_ini' => $jmlDispensasi,
            'izin_hari_ini' => $jmlIzin,
            'sakit_hari_ini' => $jmlSakit,
            'cuti_hari_ini' => $jmlCuti,
            'tidak_hadir' => $tidakHadir,
            'pending_approval' => $pendingApproval,
            'cabang' => $user->getCabang(),
            'departemen' => $user->getDepartemen(),
            'selectedCabang' => $selectedCabang,
            'selectedDept' => $selectedDept,

            // Charts data
            'dateLabels' => $dateLabels,
            'chart1Hadir' => $chart1Hadir,
            'chart1Telat' => $chart1Telat,
            'chart2Labels' => $chart2Labels,
            'chart2Series' => $chart2Series,
            'chart3Categories' => $chart3Categories,
            'chart3Hadir' => $chart3Hadir,
            'chart3Telat' => $chart3Telat,
            'chart4Izin' => $chart4Izin,
            'chart4Sakit' => $chart4Sakit,
            'chart4Cuti' => $chart4Cuti,

            // Operational feeds
            'pengajuanTerbaru' => $pengajuanTerbaru,
            'aktivitasTerbaru' => $aktivitasTerbaru,
        ];

        return view('dashboard.dashboard', $data);
    }

    public function getKaryawanPresensi(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $tanggal = $request->tanggal ?: Carbon::now(config('app.timezone'))->format('Y-m-d');
        $status = $request->status;

        $query = Presensi::join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->where('presensi.tanggal', $tanggal)
            ->where('presensi.status', $status);

        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            $query->whereIn('karyawan.kode_cabang', !empty($userCabangs) ? $userCabangs : ['INVALID']);
            $query->whereIn('karyawan.kode_dept', !empty($userDepartemens) ? $userDepartemens : ['INVALID']);
        }

        $query->select(
                'karyawan.nik',
                'karyawan.nama_karyawan',
                'karyawan.foto',
                'jabatan.nama_jabatan',
                'departemen.nama_dept',
                'cabang.nama_cabang',
                'presensi.jam_in',
                'presensi.jam_out',
                'presensi.status',
                'presensi.is_dispensasi',
                'presensi_jamkerja.nama_jam_kerja'
            );

        $karyawanList = $query->orderBy('karyawan.nama_karyawan', 'asc')->get();

        $titles = [
            'h' => 'Daftar Karyawan Hadir',
            'i' => 'Daftar Karyawan Izin',
            's' => 'Daftar Karyawan Sakit',
            'c' => 'Daftar Karyawan Cuti',
        ];
        $title = $titles[$status] ?? 'Daftar Karyawan';

        $html = view('dashboard.karyawan_list_modal', compact('karyawanList', 'status'))->render();

        return response()->json([
            'success' => true,
            'title' => $title,
            'html' => $html
        ]);
    }

    /**
     * Admin Global Search API
     * Returns categorized results for Menus, Karyawan, Presensi, Pengajuan Izin/Cuti, and Dispensasi.
     */
    public function globalSearch(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $q = trim($request->input('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([
                'status' => true,
                'query' => $q,
                'menus' => [],
                'karyawan' => [],
                'presensi' => [],
                'pengajuan' => [],
                'dispensasi' => []
            ]);
        }

        $qLower = mb_strtolower($q);

        // 1. Navigation Menus & Features
        $allMenus = [
            ['name' => 'Dashboard Utama', 'url' => route('dashboard.index'), 'icon' => 'ti-home', 'category' => 'Menu Utama', 'keywords' => 'dashboard beranda home statistik ringkasan'],
            ['name' => 'Data Karyawan & Wajah', 'url' => route('karyawan.index'), 'icon' => 'ti-users', 'category' => 'Karyawan', 'keywords' => 'karyawan pegawai staff user foto nik wajah biometrik', 'can' => 'karyawan.index'],
            ['name' => 'Monitoring Presensi Hari Ini', 'url' => route('presensi.index'), 'icon' => 'ti-calendar-check', 'category' => 'Presensi', 'keywords' => 'presensi kehadiran absensi hadir telat monitoring clock in out', 'can' => 'presensi.index'],
            ['name' => 'Live Tracking GPS', 'url' => route('trackingpresensi.index'), 'icon' => 'ti-map-pin', 'category' => 'Presensi', 'keywords' => 'tracking gps peta radar lokasi koordinat live', 'can' => 'trackingpresensi.index'],
            ['name' => 'Persetujuan Izin Absen', 'url' => route('izinabsen.index'), 'icon' => 'ti-file-text', 'category' => 'Pengajuan', 'keywords' => 'izin absen permohonan dispensasi approval persetujuan', 'can' => 'izinabsen.index'],
            ['name' => 'Persetujuan Izin Sakit', 'url' => route('izinsakit.index'), 'icon' => 'ti-first-aid-kit', 'category' => 'Pengajuan', 'keywords' => 'sakit dokter surat izin sakit permohonan', 'can' => 'izinsakit.index'],
            ['name' => 'Persetujuan Izin Cuti', 'url' => route('izincuti.index'), 'icon' => 'ti-calendar-event', 'category' => 'Pengajuan', 'keywords' => 'cuti tahunan kuota cuti bersama permohonan libur', 'can' => 'izincuti.index'],
            ['name' => 'Dispensasi Keterlambatan', 'url' => route('dispensasi.index'), 'icon' => 'ti-clock-edit', 'category' => 'Pengajuan', 'keywords' => 'dispensasi terlambat macet telat persetujuan', 'can' => 'dispensasi.index'],
            ['name' => 'Shift & Jam Kerja', 'url' => route('jamkerja.index'), 'icon' => 'ti-clock', 'category' => 'Master Data', 'keywords' => 'jam kerja shift jadwal pagi malam waktu operasional', 'can' => 'jamkerja.index'],
            ['name' => 'Outlet Cabang Coffee', 'url' => route('cabang.index'), 'icon' => 'ti-building-store', 'category' => 'Master Data', 'keywords' => 'cabang outlet toko coffee shop lokasi radius geofence', 'can' => 'cabang.index'],
            ['name' => 'Divisi / Departemen', 'url' => route('departemen.index'), 'icon' => 'ti-building', 'category' => 'Master Data', 'keywords' => 'departemen divisi unit bagian kitchen bar service', 'can' => 'departemen.index'],
            ['name' => 'Jabatan & Posisi', 'url' => route('jabatan.index'), 'icon' => 'ti-id', 'category' => 'Master Data', 'keywords' => 'jabatan posisi role pangkat barista supervisor manager', 'can' => 'jabatan.index'],
            ['name' => 'Laporan Presensi Karyawan', 'url' => route('laporan.presensi'), 'icon' => 'ti-file-report', 'category' => 'Laporan', 'keywords' => 'laporan rekapitulasi kehadiran export excel pdf cetak', 'can' => 'laporan.presensi'],
            ['name' => 'Rekapitulasi Cuti Karyawan', 'url' => route('laporan.cuti'), 'icon' => 'ti-file-spreadsheet', 'category' => 'Laporan', 'keywords' => 'laporan rekap cuti saldo kuota tahunan', 'can' => 'laporan.cuti'],
            ['name' => 'Manajemen Akun User', 'url' => route('users.index'), 'icon' => 'ti-users-cog', 'category' => 'Pengaturan', 'keywords' => 'user pengguna role hak akses password akun', 'can' => 'users.index'],
        ];

        $matchedMenus = [];
        foreach ($allMenus as $m) {
            if (isset($m['can']) && !$user->can($m['can'])) {
                continue;
            }
            $targetStr = mb_strtolower($m['name'] . ' ' . $m['category'] . ' ' . $m['keywords']);
            if (str_contains($targetStr, $qLower)) {
                $matchedMenus[] = [
                    'name' => $m['name'],
                    'url' => $m['url'],
                    'icon' => $m['icon'],
                    'category' => $m['category']
                ];
            }
        }

        // 2. Data Karyawan
        $matchedKaryawan = [];
        if ($user->can('karyawan.index')) {
            $kQuery = Karyawan::query()
                ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
                ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                ->where(function ($sub) use ($q) {
                    $sub->where('karyawan.nama_karyawan', 'like', "%{$q}%")
                        ->orWhere('karyawan.nik', 'like', "%{$q}%");
                });

            if (!$user->isSuperAdmin()) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();
                if (!empty($userCabangs)) $kQuery->whereIn('karyawan.kode_cabang', $userCabangs);
                if (!empty($userDepartemens)) $kQuery->whereIn('karyawan.kode_dept', $userDepartemens);
            }

            $kResults = $kQuery->select(
                'karyawan.nik',
                'karyawan.nama_karyawan',
                'karyawan.foto',
                'departemen.nama_dept',
                'jabatan.nama_jabatan',
                'cabang.nama_cabang'
            )->limit(5)->get();

            foreach ($kResults as $k) {
                $matchedKaryawan[] = [
                    'nik' => $k->nik,
                    'nama' => $k->nama_karyawan,
                    'dept' => $k->nama_dept ?? '-',
                    'jabatan' => $k->nama_jabatan ?? '-',
                    'cabang' => $k->nama_cabang ?? '-',
                    'foto' => $k->foto ? asset('storage/uploads/karyawan/' . $k->foto) : null,
                    'url' => route('karyawan.index') . '?nama_karyawan_search=' . urlencode($k->nama_karyawan)
                ];
            }
        }

        // 3. Monitoring Presensi (Recent)
        $matchedPresensi = [];
        if ($user->can('presensi.index')) {
            $pQuery = Presensi::query()
                ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
                ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                ->where(function ($sub) use ($q) {
                    $sub->where('karyawan.nama_karyawan', 'like', "%{$q}%")
                        ->orWhere('presensi.nik', 'like', "%{$q}%");
                });

            if (!$user->isSuperAdmin()) {
                $userCabangs = $user->getCabangCodes();
                if (!empty($userCabangs)) $pQuery->whereIn('karyawan.kode_cabang', $userCabangs);
            }

            $pResults = $pQuery->select(
                'presensi.id',
                'presensi.nik',
                'karyawan.nama_karyawan',
                'presensi.tanggal',
                'presensi.jam_in',
                'presensi.jam_out',
                'presensi.status',
                'departemen.nama_dept'
            )->orderBy('presensi.tanggal', 'desc')->orderBy('presensi.jam_in', 'desc')->limit(5)->get();

            $statusMap = [
                'h' => 'Hadir',
                'i' => 'Izin',
                's' => 'Sakit',
                'c' => 'Cuti',
                'a' => 'Tidak Hadir'
            ];

            foreach ($pResults as $p) {
                $matchedPresensi[] = [
                    'nik' => $p->nik,
                    'nama' => $p->nama_karyawan,
                    'tanggal' => Carbon::parse($p->tanggal)->translatedFormat('d M Y'),
                    'jam_in' => $p->jam_in ? substr($p->jam_in, 0, 5) : '-',
                    'jam_out' => $p->jam_out ? substr($p->jam_out, 0, 5) : '-',
                    'status_label' => $statusMap[$p->status] ?? 'Hadir',
                    'dept' => $p->nama_dept ?? '-',
                    'url' => route('presensi.index') . '?dari=' . $p->tanggal . '&sampai=' . $p->tanggal . '&nama_karyawan_search=' . urlencode($p->nama_karyawan)
                ];
            }
        }

        // 4. Pengajuan Izin / Sakit / Cuti
        $matchedPengajuan = [];
        $uCabangs = !$user->isSuperAdmin() ? $user->getCabangCodes() : [];
        $uDepts = !$user->isSuperAdmin() ? $user->getDepartemenCodes() : [];

        if ($user->can('izinabsen.index')) {
            $iaQuery = DB::table('presensi_izinabsen')
                ->join('karyawan', 'presensi_izinabsen.nik', '=', 'karyawan.nik')
                ->where(function ($sub) use ($q) {
                    $sub->where('karyawan.nama_karyawan', 'like', "%{$q}%")
                        ->orWhere('presensi_izinabsen.nik', 'like', "%{$q}%")
                        ->orWhere('presensi_izinabsen.keterangan', 'like', "%{$q}%");
                });

            if (!$user->isSuperAdmin()) {
                $iaQuery->whereIn('karyawan.kode_cabang', !empty($uCabangs) ? $uCabangs : ['INVALID']);
                $iaQuery->whereIn('karyawan.kode_dept', !empty($uDepts) ? $uDepts : ['INVALID']);
            }

            $izinAbsen = $iaQuery->select('presensi_izinabsen.kode_izin as id', 'presensi_izinabsen.nik', 'karyawan.nama_karyawan', 'presensi_izinabsen.tanggal', 'presensi_izinabsen.keterangan', 'presensi_izinabsen.status_approved')
                ->orderBy('presensi_izinabsen.tanggal', 'desc')->limit(3)->get();

            foreach ($izinAbsen as $ia) {
                $matchedPengajuan[] = [
                    'tipe' => 'Izin Absen',
                    'tipe_badge' => 'info',
                    'nama' => $ia->nama_karyawan,
                    'tanggal' => Carbon::parse($ia->tanggal)->translatedFormat('d M Y'),
                    'keterangan' => Str::limit($ia->keterangan ?? 'Izin Absen', 35),
                    'status_approved' => $ia->status_approved,
                    'url' => route('izinabsen.index')
                ];
            }
        }

        if ($user->can('izinsakit.index')) {
            $isQuery = DB::table('presensi_izinsakit')
                ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
                ->where(function ($sub) use ($q) {
                    $sub->where('karyawan.nama_karyawan', 'like', "%{$q}%")
                        ->orWhere('presensi_izinsakit.nik', 'like', "%{$q}%")
                        ->orWhere('presensi_izinsakit.keterangan', 'like', "%{$q}%");
                });

            if (!$user->isSuperAdmin()) {
                $isQuery->whereIn('karyawan.kode_cabang', !empty($uCabangs) ? $uCabangs : ['INVALID']);
                $isQuery->whereIn('karyawan.kode_dept', !empty($uDepts) ? $uDepts : ['INVALID']);
            }

            $izinSakit = $isQuery->select('presensi_izinsakit.kode_izin_sakit as id', 'presensi_izinsakit.nik', 'karyawan.nama_karyawan', 'presensi_izinsakit.tanggal', 'presensi_izinsakit.keterangan', 'presensi_izinsakit.status_approved')
                ->orderBy('presensi_izinsakit.tanggal', 'desc')->limit(3)->get();

            foreach ($izinSakit as $is) {
                $matchedPengajuan[] = [
                    'tipe' => 'Izin Sakit',
                    'tipe_badge' => 'warning',
                    'nama' => $is->nama_karyawan,
                    'tanggal' => Carbon::parse($is->tanggal)->translatedFormat('d M Y'),
                    'keterangan' => Str::limit($is->keterangan ?? 'Izin Sakit', 35),
                    'status_approved' => $is->status_approved,
                    'url' => route('izinsakit.index')
                ];
            }
        }

        if ($user->can('izincuti.index')) {
            $icQuery = DB::table('presensi_izincuti')
                ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
                ->where(function ($sub) use ($q) {
                    $sub->where('karyawan.nama_karyawan', 'like', "%{$q}%")
                        ->orWhere('presensi_izincuti.nik', 'like', "%{$q}%")
                        ->orWhere('presensi_izincuti.keterangan', 'like', "%{$q}%");
                });

            if (!$user->isSuperAdmin()) {
                $icQuery->whereIn('karyawan.kode_cabang', !empty($uCabangs) ? $uCabangs : ['INVALID']);
                $icQuery->whereIn('karyawan.kode_dept', !empty($uDepts) ? $uDepts : ['INVALID']);
            }

            $izinCuti = $icQuery->select('presensi_izincuti.kode_izin_cuti as id', 'presensi_izincuti.nik', 'karyawan.nama_karyawan', 'presensi_izincuti.tanggal', 'presensi_izincuti.keterangan', 'presensi_izincuti.status_approved')
                ->orderBy('presensi_izincuti.tanggal', 'desc')->limit(3)->get();

            foreach ($izinCuti as $ic) {
                $matchedPengajuan[] = [
                    'tipe' => 'Izin Cuti',
                    'tipe_badge' => 'success',
                    'nama' => $ic->nama_karyawan,
                    'tanggal' => Carbon::parse($ic->tanggal)->translatedFormat('d M Y'),
                    'keterangan' => Str::limit($ic->keterangan ?? 'Izin Cuti', 35),
                    'status_approved' => $ic->status_approved,
                    'url' => route('izincuti.index')
                ];
            }
        }

        // 5. Dispensasi
        $matchedDispensasi = [];
        if ($user->can('dispensasi.index')) {
            $dispQuery = DB::table('presensi_dispensasi')
                ->join('karyawan', 'presensi_dispensasi.nik', '=', 'karyawan.nik')
                ->where(function ($sub) use ($q) {
                    $sub->where('karyawan.nama_karyawan', 'like', "%{$q}%")
                        ->orWhere('presensi_dispensasi.nik', 'like', "%{$q}%")
                        ->orWhere('presensi_dispensasi.keterangan', 'like', "%{$q}%");
                });

            if (!$user->isSuperAdmin()) {
                $dispQuery->whereIn('karyawan.kode_cabang', !empty($uCabangs) ? $uCabangs : ['INVALID']);
                $dispQuery->whereIn('karyawan.kode_dept', !empty($uDepts) ? $uDepts : ['INVALID']);
            }

            $dispensasiList = $dispQuery->select('presensi_dispensasi.id', 'presensi_dispensasi.nik', 'karyawan.nama_karyawan', 'presensi_dispensasi.tanggal', 'presensi_dispensasi.keterangan', 'presensi_dispensasi.status_approved')
                ->orderBy('presensi_dispensasi.tanggal', 'desc')->limit(3)->get();

            foreach ($dispensasiList as $d) {
                $matchedDispensasi[] = [
                    'nama' => $d->nama_karyawan,
                    'tanggal' => Carbon::parse($d->tanggal)->translatedFormat('d M Y'),
                    'keterangan' => Str::limit($d->keterangan ?? 'Dispensasi Keterlambatan', 35),
                    'status_approved' => $d->status_approved,
                    'url' => route('dispensasi.index')
                ];
            }
        }

        return response()->json([
            'status' => true,
            'query' => $q,
            'menus' => $matchedMenus,
            'karyawan' => $matchedKaryawan,
            'presensi' => $matchedPresensi,
            'pengajuan' => $matchedPengajuan,
            'dispensasi' => $matchedDispensasi
        ]);
    }
}
