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
            ->select(
                'kode_izin as kode',
                'tanggal',
                'dari',
                'sampai',
                'keterangan',
                'keterangan_hrd',
                'status as status_izin',
                DB::raw('\'i\' as ket'),
                DB::raw('NULL as doc_sid'),
                DB::raw('NULL as jenis_cuti'),
                DB::raw('NULL as pelimpahan_tugas'),
                DB::raw('NULL as nama_kepala_divisi')
            );

        $izinsakit = Izinsakit::where('nik', $nik)
            ->select(
                'kode_izin_sakit as kode',
                'tanggal',
                'dari',
                'sampai',
                'keterangan',
                'keterangan_hrd',
                'status as status_izin',
                DB::raw('\'s\' as ket'),
                'doc_sid',
                DB::raw('NULL as jenis_cuti'),
                DB::raw('NULL as pelimpahan_tugas'),
                DB::raw('NULL as nama_kepala_divisi')
            );

        $izincuti = Izincuti::where('nik', $nik)
            ->leftJoin('cuti', 'presensi_izincuti.kode_cuti', '=', 'cuti.kode_cuti')
            ->select(
                'presensi_izincuti.kode_izin_cuti as kode',
                'presensi_izincuti.tanggal',
                'presensi_izincuti.dari',
                'presensi_izincuti.sampai',
                'presensi_izincuti.keterangan',
                'presensi_izincuti.keterangan_hrd',
                'presensi_izincuti.status as status_izin',
                DB::raw('\'c\' as ket'),
                DB::raw('NULL as doc_sid'),
                'cuti.jenis_cuti',
                'presensi_izincuti.pelimpahan_tugas',
                'presensi_izincuti.nama_kepala_divisi'
            );

        $unionQuery = $izinabsen->union($izinsakit)->union($izincuti);
        $pengajuan_izin = DB::query()->fromSub($unionQuery, 'izin_union')
            ->orderBy('tanggal', 'desc')
            ->paginate(10)
            ->withQueryString();
        $data['pengajuan_izin'] = $pengajuan_izin;

        $currentYear = date('Y');
        $currentMonth = date('m');
        $startMonth = "{$currentYear}-{$currentMonth}-01";
        $endMonth = date('Y-m-t', strtotime($startMonth));

        // Master Cuti Tahunan (C01) - Disinkronkan langsung dengan Master Data Cuti Admin
        $cutiTahunan = \App\Models\Cuti::where('kode_cuti', 'C01')->first();
        $kuotaTahunan = $cutiTahunan ? (int)$cutiTahunan->jumlah_hari : 12;

        // Cuti tahunan terpakai (disetujui di tahun berjalan)
        $cutiTahunanTerpakai = \App\Models\Approveizincuti::join('presensi', 'presensi_izincuti_approve.id_presensi', '=', 'presensi.id')
            ->join('presensi_izincuti', 'presensi_izincuti_approve.kode_izin_cuti', '=', 'presensi_izincuti.kode_izin_cuti')
            ->where('presensi.nik', $nik)
            ->where('presensi_izincuti.kode_cuti', 'C01')
            ->whereRaw("YEAR(presensi.tanggal) = ?", [$currentYear])
            ->count();

        // Cuti tahunan pending (status = 0) di tahun berjalan
        $cutiTahunanPending = (int) Izincuti::where('nik', $nik)
            ->where('kode_cuti', 'C01')
            ->where('status', '0')
            ->whereRaw("YEAR(dari) = ?", [$currentYear])
            ->sum(DB::raw('DATEDIFF(sampai, dari) + 1'));

        $sisaCutiTahunan = max(0, $kuotaTahunan - $cutiTahunanTerpakai);

        // Batas kuota operasional bulanan (monthly_leave_quota) jika diatur
        $setting = Pengaturanumum::getSetting();
        $monthlyQuota = (int)($setting->monthly_leave_quota ?? 0);

        $cutiBulanIniTerpakai = \App\Models\Approveizincuti::join('presensi', 'presensi_izincuti_approve.id_presensi', '=', 'presensi.id')
            ->where('presensi.nik', $nik)
            ->whereBetween('presensi.tanggal', [$startMonth, $endMonth])
            ->count();

        $data['sisa_cuti_info'] = [
            'tahun' => $currentYear,
            'kuota' => $kuotaTahunan,
            'terpakai' => $cutiTahunanTerpakai,
            'pending' => $cutiTahunanPending,
            'sisa' => $sisaCutiTahunan,
            'jenis_cuti_nama' => $cutiTahunan->jenis_cuti ?? 'Cuti Tahunan',
            'kode_cuti' => 'C01',
            'monthly_quota' => $monthlyQuota,
            'cuti_bulan_ini' => $cutiBulanIniTerpakai
        ];

        return view('pengajuanizin.index', $data);
    }
}
