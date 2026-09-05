<?php

namespace App\Http\Controllers;

use App\Models\Izinabsen;
use App\Models\Izincuti;
use App\Models\Izinsakit;
use App\Models\Userkaryawan;
use App\Models\Pengaturanumum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengajuanizinController extends Controller
{
    public function index()
    {
        $nik = auth()->user()->userkaryawan->nik ?? '';
        if (!$nik) {
            $userkaryawan = Userkaryawan::where('id_user', auth()->id())->first();
            $nik = $userkaryawan->nik ?? '';
        }

        $izinabsen = Izinabsen::where('nik', $nik)
            ->select('kode_izin as kode', 'tanggal', 'keterangan', 'dari', 'sampai', DB::raw('\'i\' as ket'), 'status as status_izin');

        $izinsakit = Izinsakit::where('nik', $nik)
            ->select('kode_izin_sakit as kode', 'tanggal', 'keterangan', 'dari', 'sampai', DB::raw('\'s\' as ket'), 'status as status_izin');

        $izincuti = Izincuti::where('nik', $nik)
            ->select('kode_izin_cuti as kode', 'tanggal', 'keterangan', 'dari', 'sampai', DB::raw('\'c\' as ket'), 'status as status_izin');

        $pengajuan_izin = $izinabsen->union($izinsakit)->union($izincuti)
            ->orderBy('tanggal', 'desc')
            ->limit(50)
            ->get();
        $data['pengajuan_izin'] = $pengajuan_izin;

        $currentYear = date('Y');
        $currentMonth = date('m');
        $startMonth = "{$currentYear}-{$currentMonth}-01";
        $endMonth = date('Y-m-t', strtotime($startMonth));

        // Monthly leave quota requirement from client (configurable in pengaturan_umum)
        $setting = Pengaturanumum::getSetting();
        $monthlyQuota = $setting->monthly_leave_quota ?? 3;

        $cutiBulanIniTerpakai = \App\Models\Approveizincuti::join('presensi', 'presensi_izincuti_approve.id_presensi', '=', 'presensi.id')
            ->where('presensi.nik', $nik)
            ->whereBetween('presensi.tanggal', [$startMonth, $endMonth])
            ->count();

        $cutiBulanIniPending = (int) Izincuti::where('nik', $nik)
            ->where('status', '0')
            ->whereBetween('dari', [$startMonth, $endMonth])
            ->sum(DB::raw('DATEDIFF(sampai, dari) + 1'));

        $sisaCuti = max(0, $monthlyQuota - $cutiBulanIniTerpakai);

        $data['sisa_cuti_info'] = [
            'tahun' => date('F Y'),
            'kuota' => $monthlyQuota,
            'terpakai' => $cutiBulanIniTerpakai,
            'pending' => $cutiBulanIniPending,
            'sisa' => $sisaCuti,
            'jenis_cuti_nama' => 'Jatah Cuti Bulanan',
            'kode_cuti' => 'C01'
        ];

        return view('pengajuanizin.index', $data);
    }
}
