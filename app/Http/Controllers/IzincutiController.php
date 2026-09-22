<?php

namespace App\Http\Controllers;

use App\Models\Approveizincuti;
use App\Models\Cabang;
use App\Models\Cuti;
use App\Models\Departemen;
use App\Models\Izinabsen;
use App\Models\Izincuti;
use App\Models\Izinsakit;
use App\Models\Jamkerja;
use App\Models\Karyawan;
use App\Models\Pengaturanumum;
use App\Models\Presensi;
use App\Models\User;
use App\Models\Userkaryawan;
use App\Services\ApprovalService;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class IzincutiController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $qcuti = Izincuti::query();
        $qcuti->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik');
        $qcuti->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan');
        $qcuti->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept');
        $qcuti->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang');
        $qcuti->join('cuti', 'presensi_izincuti.kode_cuti', '=', 'cuti.kode_cuti');
        
        // Filter berdasarkan akses cabang dan departemen jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!empty($userCabangs)) {
                $qcuti->whereIn('karyawan.kode_cabang', $userCabangs);
            } else {
                $qcuti->whereRaw('1 = 0');
            }
            
            if (!empty($userDepartemens)) {
                $qcuti->whereIn('karyawan.kode_dept', $userDepartemens);
            } else {
                $qcuti->whereRaw('1 = 0');
            }
        }
        
        $qcuti->select('presensi_izincuti.*', 'karyawan.nama_karyawan', 'karyawan.nik_show', 'jabatan.nama_jabatan', 'departemen.nama_dept', 'cabang.nama_cabang', 'presensi_izincuti.keterangan as nama_cuti');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $qcuti->whereBetween('presensi_izincuti.dari', [$request->dari, $request->sampai]);
        }
        if (!empty($request->nama_karyawan)) {
            $qcuti->where('karyawan.nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }
        if (!empty($request->kode_cabang)) {
            $qcuti->where('karyawan.kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->kode_dept)) {
            $qcuti->where('karyawan.kode_dept', $request->kode_dept);
        }
        
        $qcuti->addSelect('karyawan.kode_dept');

        if (!empty($request->status) || $request->status === '0') {
            $qcuti->where('presensi_izincuti.status', $request->status);
        }

        $qcuti->orderBy('presensi_izincuti.status');
        $qcuti->orderBy('presensi_izincuti.dari', 'desc');
        $cuti = $qcuti->paginate(15);
        $cuti->appends($request->all());
        $data['izincuti'] = $cuti;
        $data['cabang'] = $user->getCabang();
        $data['departemen'] = $user->getDepartemen();
        return view('izincuti.index', $data);
    }


    public function create(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $qkaryawan = Karyawan::query();
        $qkaryawan->select('karyawan.nik', 'karyawan.nama_karyawan');
        
        // Filter karyawan berdasarkan akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!empty($userCabangs)) {
                $qkaryawan->whereIn('kode_cabang', $userCabangs);
            } else {
                $qkaryawan->whereRaw('1 = 0');
            }
            
            if (!empty($userDepartemens)) {
                $qkaryawan->whereIn('kode_dept', $userDepartemens);
            } else {
                $qkaryawan->whereRaw('1 = 0');
            }
        }
        
        $karyawan = $qkaryawan->get();
        $data['jenis_cuti'] = Cuti::orderBy('kode_cuti')->get();
        $data['karyawan'] = $karyawan;
        $data['general_setting'] = Pengaturanumum::first();

        if ($user->hasRole('karyawan')) {
            $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
            $nik = $userkaryawan?->nik ?? '';
            $currentYear = date('Y');

            $cutiTahunan = Cuti::where('kode_cuti', 'C01')->first();
            $kuotaTahunan = $cutiTahunan->jumlah_hari ?? 12;

            $cutiTahunanTerpakai = Approveizincuti::join('presensi', 'presensi_izincuti_approve.id_presensi', '=', 'presensi.id')
                ->join('presensi_izincuti', 'presensi_izincuti_approve.kode_izin_cuti', '=', 'presensi_izincuti.kode_izin_cuti')
                ->where('presensi.nik', $nik)
                ->where('presensi_izincuti.kode_cuti', 'C01')
                ->whereRaw("YEAR(presensi.tanggal) = ?", [$currentYear])
                ->count();

            $cutiTahunanPending = (int) Izincuti::where('nik', $nik)
                ->where('kode_cuti', 'C01')
                ->where('status', '0')
                ->whereRaw("YEAR(dari) = ?", [$currentYear])
                ->sum(DB::raw('DATEDIFF(sampai, dari) + 1'));

            $sisaCutiTahunan = max(0, $kuotaTahunan - $cutiTahunanTerpakai);

            $data['sisa_cuti_info'] = [
                'tahun' => $currentYear,
                'kuota' => $kuotaTahunan,
                'terpakai' => $cutiTahunanTerpakai,
                'pending' => $cutiTahunanPending,
                'sisa' => $sisaCutiTahunan,
                'jenis_cuti_nama' => $cutiTahunan->jenis_cuti ?? 'Cuti Tahunan',
                'kode_cuti' => 'C01'
            ];

            $breakdown = [];
            foreach ($data['jenis_cuti'] as $jc) {
                if ($jc->kode_cuti == 'C01') {
                    $breakdown[$jc->kode_cuti] = [
                        'nama' => $jc->jenis_cuti,
                        'max' => $jc->jumlah_hari,
                        'terpakai' => $cutiTahunanTerpakai,
                        'sisa' => $sisaCutiTahunan,
                    ];
                } else {
                    $used = Approveizincuti::join('presensi', 'presensi_izincuti_approve.id_presensi', '=', 'presensi.id')
                        ->join('presensi_izincuti', 'presensi_izincuti_approve.kode_izin_cuti', '=', 'presensi_izincuti.kode_izin_cuti')
                        ->where('presensi.nik', $nik)
                        ->where('presensi_izincuti.kode_cuti', $jc->kode_cuti)
                        ->whereRaw("YEAR(presensi.tanggal) = ?", [$currentYear])
                        ->count();
                    $breakdown[$jc->kode_cuti] = [
                        'nama' => $jc->jenis_cuti,
                        'max' => $jc->jumlah_hari,
                        'terpakai' => $used,
                        'sisa' => max(0, $jc->jumlah_hari - $used),
                    ];
                }
            }
            $data['cuti_breakdown'] = $breakdown;

            return view('izincuti.create-mobile', $data);
        }
        if ($request->ajax()) {
            return view('izincuti.create-modal', $data);
        }
        return view('izincuti.create', $data);
    }

    public function store(Request $request)
    {
        $user = User::findorfail(auth()->user()->id);
        $role = $user->getRoleNames()->first();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();

        if ($user->hasRole('karyawan')) {
            if (!$userkaryawan || empty($userkaryawan->nik)) {
                return Redirect::back()->withInput()->with(messageError('Akun Anda belum terhubung dengan data karyawan. Silakan hubungi admin.'));
            }
            $nik = $userkaryawan->nik;
            $request->validate([
                'dari' => 'required|date',
                'sampai' => 'required|date',
                'keterangan' => 'required',
                'kode_cuti' => 'required',
                'pelimpahan_tugas' => 'nullable|string|max:255',
                'nama_kepala_divisi' => 'nullable|string|max:255',
            ]);
        } else {
            $nik = $request->nik;
            $request->validate([
                'nik' => 'required',
                'dari' => 'required|date',
                'sampai' => 'required|date',
                'keterangan' => 'required',
                'kode_cuti' => 'required',
                'pelimpahan_tugas' => 'nullable|string|max:255',
                'nama_kepala_divisi' => 'nullable|string|max:255',
            ]);
        }


        $format = "IC" . date('ym', strtotime($request->dari));
        DB::beginTransaction();
        try {
            $cek_izin_absen = Izinabsen::where('nik', $nik)
                ->where(function ($q) use ($request) {
                    $q->where('dari', '<=', $request->sampai)
                      ->where('sampai', '>=', $request->dari);
                })
                ->where('status', '!=', '2')
                ->first();

            $cek_izin_sakit = Izinsakit::where('nik', $nik)
                ->where(function ($q) use ($request) {
                    $q->where('dari', '<=', $request->sampai)
                      ->where('sampai', '>=', $request->dari);
                })
                ->where('status', '!=', '2')
                ->first();

            $cek_izin_cuti = Izincuti::where('nik', $nik)
                ->where(function ($q) use ($request) {
                    $q->where('dari', '<=', $request->sampai)
                      ->where('sampai', '>=', $request->dari);
                })
                ->where('status', '!=', '2')
                ->first();

            if ($cek_izin_absen || $cek_izin_sakit || $cek_izin_cuti) {
                $msg = 'Anda Sudah Mengajukan Izin Absen/Sakit/Cuti Pada Rentang Tanggal Tersebut!';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return Redirect::back()->withInput()->with(messageError($msg));
            }
            $lastizincuti = Izincuti::select('kode_izin_cuti')
                ->whereRaw('LEFT(kode_izin_cuti,6) = ?', [$format])
                ->orderBy("kode_izin_cuti", "desc")
                ->first();
            $last_kode_izin_cuti = $lastizincuti != null ? $lastizincuti->kode_izin_cuti : '';
            $kode_izin_cuti  = buatkode($last_kode_izin_cuti, "IC"  . date('ym', strtotime($request->dari)), 4);


            $jmlhari = hitungHari($request->dari, $request->sampai, $nik);
            $cuti = Cuti::where('kode_cuti', $request->kode_cuti)->first();
            $max_cuti = $cuti->jumlah_hari;
            $tahun_cuti = date('Y', strtotime($request->dari));
            $cek_cuti_dipakai = Approveizincuti::join('presensi', 'presensi_izincuti_approve.id_presensi', '=', 'presensi.id')
                ->join('presensi_izincuti', 'presensi_izincuti_approve.kode_izin_cuti', '=', 'presensi_izincuti.kode_izin_cuti')
                ->where('presensi.nik', $nik)
                ->where('presensi_izincuti.kode_cuti', $request->kode_cuti)
                ->whereRaw("YEAR(presensi.tanggal) = ?", [$tahun_cuti])
                ->count();
            if ($request->kode_cuti == "C01") {
                $sisa_cuti = $max_cuti - $cek_cuti_dipakai;
                if ($jmlhari > $sisa_cuti) {
                    $msg = 'Jumlah Hari Melebihi Sisa Cuti ' . $cuti->jenis_cuti . ' Anda, Sisa Cuti Anda Adalah ' . $sisa_cuti . ' Hari Lagi!';
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => $msg], 422);
                    }
                    return Redirect::back()->withInput()->with(messageError($msg));
                }
            } else {
                if ($jmlhari > $max_cuti) {
                    $msg = 'Jumlah Hari Melebihi Maksimal Cuti ' . $cuti->jenis_cuti . ' Yaitu ' . $max_cuti . ' Hari!';
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => $msg], 422);
                    }
                    return Redirect::back()->withInput()->with(messageError($msg));
                }
            }

            $general_setting = Pengaturanumum::first();
            if (!empty($general_setting->monthly_leave_quota) && $general_setting->monthly_leave_quota > 0) {
                $startMonth = date('Y-m-01', strtotime($request->dari));
                $endMonth = date('Y-m-t', strtotime($request->dari));
                $cutiBulanIni = Approveizincuti::join('presensi', 'presensi_izincuti_approve.id_presensi', '=', 'presensi.id')
                    ->where('presensi.nik', $nik)
                    ->whereBetween('presensi.tanggal', [$startMonth, $endMonth])
                    ->count();
                if (($cutiBulanIni + $jmlhari) > $general_setting->monthly_leave_quota) {
                    $msg = 'Pengambilan cuti melebihi batas kuota bulanan (' . $general_setting->monthly_leave_quota . ' hari/bulan). Bulan ini sudah terpakai ' . $cutiBulanIni . ' hari!';
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => $msg], 422);
                    }
                    return Redirect::back()->withInput()->with(messageError($msg));
                }
            }

            $dataizincuti = [
                'kode_izin_cuti' => $kode_izin_cuti,
                'nik' => $nik,
                'tanggal' => $request->dari,
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'kode_cuti' => $request->kode_cuti,
                'keterangan' => $request->keterangan,
                'pelimpahan_tugas' => $request->pelimpahan_tugas,
                'nama_kepala_divisi' => $request->nama_kepala_divisi,
                'status' => 0,
                'approval_step' => 1,
                'id_user' => $user->id,
                'keterangan_hrd' => null,
            ];

            Izincuti::create($dataizincuti);
            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Data Berhasil Disimpan']);
            }

            if ($user->hasRole('karyawan')) {
                return Redirect::route('pengajuanizin.index')->with(messageSuccess('Data Berhasil Disimpan'));
            } else {
                return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return Redirect::back()->withInput()->with(messageError($e->getMessage()));
        }
    }


    public function edit($kode_izin_cuti)
    {
        /** @var User $user */
        $user = auth()->user();
        $kode_izin_cuti = Crypt::decrypt($kode_izin_cuti);
        $izincuti = Izincuti::where('kode_izin_cuti', $kode_izin_cuti)
            ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $karyawanData = Karyawan::where('nik', $izincuti->nik)->first();
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($karyawanData->kode_cabang, $userCabangs) || !in_array($karyawanData->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin cuti ini.');
            }
        }
        
        $qkaryawan = Karyawan::query();
        $qkaryawan->select('karyawan.nik', 'karyawan.nama_karyawan');
        
        // Filter karyawan berdasarkan akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!empty($userCabangs)) {
                $qkaryawan->whereIn('kode_cabang', $userCabangs);
            } else {
                $qkaryawan->whereRaw('1 = 0');
            }
            
            if (!empty($userDepartemens)) {
                $qkaryawan->whereIn('kode_dept', $userDepartemens);
            } else {
                $qkaryawan->whereRaw('1 = 0');
            }
        }
        
        $karyawan = $qkaryawan->get();
        $data['karyawan'] = $karyawan;
        $data['izincuti'] = $izincuti;
        $data['jenis_cuti'] = Cuti::orderBy('kode_cuti')->get();
        return view('izincuti.edit', $data);
    }


    public function update(Request $request, $kode_izin_cuti)
    {
        $kode_izin_cuti = Crypt::decrypt($kode_izin_cuti);
        $izincuti = Izincuti::where('kode_izin_cuti', $kode_izin_cuti)
            ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
            ->first();

        if (!$izincuti) {
            abort(404, 'Data izin cuti tidak ditemukan.');
        }

        if ($izincuti->status == 1) {
            $msg = 'Pengajuan izin cuti yang sudah disetujui tidak dapat diedit langsung. Batalkan persetujuan terlebih dahulu.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return Redirect::back()->with(messageError($msg));
        }

        /** @var User $user */
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            if (empty($userCabangs) || !in_array($izincuti->kode_cabang, $userCabangs)) {
                abort(403, 'Anda tidak memiliki akses ke cabang izin cuti ini.');
            }
            if (empty($userDepartemens) || !in_array($izincuti->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke departemen izin cuti ini.');
            }
        }

        $request->validate([
            'nik' => 'required',
            'dari' => 'required',
            'sampai' => 'required',
            'keterangan' => 'required',
            'kode_cuti' => 'required',
            'pelimpahan_tugas' => 'required',
            'nama_kepala_divisi' => 'required',
        ]);
        DB::beginTransaction();
        try {
            Izincuti::where('kode_izin_cuti', $kode_izin_cuti)->update([
                'nik' => $request->nik,
                'tanggal' => $request->dari,
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'keterangan' => $request->keterangan,
                'kode_cuti' => $request->kode_cuti,
                'pelimpahan_tugas' => $request->pelimpahan_tugas,
                'nama_kepala_divisi' => $request->nama_kepala_divisi,
            ]);
            DB::commit();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Data Berhasil Disimpan']);
            }
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }


    public function approve($kode_izin_cuti)
    {
        /** @var User $user */
        $user = auth()->user();
        
        $kode_izin_cuti = Crypt::decrypt($kode_izin_cuti);
        $izincuti = Izincuti::where('kode_izin_cuti', $kode_izin_cuti)
            ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($izincuti->kode_cabang, $userCabangs) || !in_array($izincuti->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin cuti ini.');
            }
        }

        // Anti Self-Approval Protection
        $approvalService = app(ApprovalService::class);
        if ($approvalService->isSelfApproval($user, $izincuti->nik)) {
            abort(403, 'Akses ditolak. Anda tidak diizinkan menyetujui (approve) pengajuan cuti milik Anda sendiri.');
        }

        $data['izincuti'] = $izincuti;
        return view('izincuti.approve', $data);
    }


    public function storeapprove(Request $request, $kode_izin_cuti)
    {
        /** @var User $user */
        $user = auth()->user();
        $approvalService = app(ApprovalService::class);
        
        $kode_izin_cuti = Crypt::decrypt($kode_izin_cuti);
        $izincuti = Izincuti::where('kode_izin_cuti', $kode_izin_cuti)
            ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
            ->select('presensi_izincuti.*', 'karyawan.kode_dept', 'karyawan.kode_cabang', 'karyawan.kode_jabatan')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            // Untuk delegasi, gunakan cabang/dept admin
            $accessUser = $user->getApprovalAdmin() ?? $user;
            $userCabangs = $accessUser->getCabangCodes();
            $userDepartemens = $accessUser->getDepartemenCodes();
            
            if (!in_array($izincuti->kode_cabang, $userCabangs) || !in_array($izincuti->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin cuti ini.');
            }
        }

        // Anti Self-Approval Protection: Applicant cannot approve their own request
        if ($approvalService->isSelfApproval($user, $izincuti->nik)) {
            abort(403, 'Akses ditolak. Anda tidak diizinkan menyetujui (approve) pengajuan cuti milik Anda sendiri.');
        }
        $dari = $izincuti->dari;
        $sampai = $izincuti->sampai;
        $nik = $izincuti->nik;
        $kode_dept = $izincuti->kode_dept;
        $kode_jabatan = $izincuti->kode_jabatan;
        $kode_cabang = $izincuti->kode_cabang;
        $currentStep = $izincuti->approval_step;
        $userRole = $user->getRoleNames()->first();
        $approvalUserId = $approvalService->getApprovalUserId($user);
        $approvalAdmin = $approvalUserId != $user->id ? User::find($approvalUserId) : $user;
        $error = '';

        // Check Authorization using Service
        if (!$approvalService->canApprove('IZIN', $currentStep, $userRole, $kode_dept, $kode_jabatan, $user, $kode_cabang)) {
             if (!$user->isSuperAdmin()) {
                 return Redirect::back()->with(messageError('Anda tidak memiliki wewenang untuk approval tahap ke-' . $currentStep));
             }
        }

        DB::beginTransaction();
        try {
            if (isset($request->approve)) {
                // P1-1: Cegah approval cuti menimpa presensi hadir aktual
                $hasActualAttendance = Presensi::where('nik', $nik)
                    ->whereBetween('tanggal', [$dari, $sampai])
                    ->where('status', 'h')
                    ->whereNotNull('jam_in')
                    ->exists();

                if ($hasActualAttendance) {
                    throw new \Exception('Karyawan sudah tercatat hadir pada tanggal ini. Izin/Sakit/Cuti tidak dapat disetujui sebelum data presensi dikoreksi.');
                }

                Izincuti::where('kode_izin_cuti', $kode_izin_cuti)->update([
                    'status' => 1,
                    'approval_step' => 1
                ]);

                $karyawan = Karyawan::where('nik', $izincuti->nik)->first();
                $kode_jam_kerja = !empty($karyawan?->kode_jam_kerja) ? $karyawan->kode_jam_kerja : 'JK01';
                $jamkerja = Jamkerja::where('kode_jam_kerja', $kode_jam_kerja)->first()
                    ?? Jamkerja::where('kode_jam_kerja', 'JK01')->first();

                $schedules = AttendanceService::getEffectiveSchedulesBatch([$nik], $dari, $sampai);

                $curr = $dari;
                while (strtotime($curr) <= strtotime($sampai)) {
                    $eff = $schedules[$nik][$curr] ?? null;
                    $isOff = $eff ? $eff['is_off'] : false;

                    if (!$isOff) {
                        $jkCode = $eff && $eff['jam_kerja'] ? $eff['jam_kerja']->kode_jam_kerja : ($jamkerja ? $jamkerja->kode_jam_kerja : 'JK01');
                        
                        $existing = Presensi::where('nik', $nik)->where('tanggal', $curr)->first();
                        $auditNote = null;
                        if ($existing && $existing->status === 'a') {
                            $auditNote = "[IZIN_SUSULAN] Diubah dari ALPHA (a) ke CUTI. Approved by: " . ($user->name ?? 'Admin') . " on " . now()->format('Y-m-d H:i:s') . ". Ref: " . $kode_izin_cuti;
                        }

                        $presensi = Presensi::updateOrCreate(
                            ['nik' => $nik, 'tanggal' => $curr],
                            [
                                'kode_jam_kerja' => $jkCode,
                                'status' => 'c',
                                'keterangan' => $auditNote ?? ($izincuti->keterangan ?? 'Izin Cuti'),
                            ]
                        );

                        Approveizincuti::updateOrCreate(
                            ['kode_izin_cuti' => $kode_izin_cuti, 'id_presensi' => $presensi->id],
                            ['id_presensi' => $presensi->id, 'kode_izin_cuti' => $kode_izin_cuti]
                        );
                    }

                    $curr = date('Y-m-d', strtotime($curr . ' +1 day'));
                }
            } else {
                Izincuti::where('kode_izin_cuti', $kode_izin_cuti)->update([
                    'status' => 2,
                    'approval_step' => 1
                ]);
            }
            if (!empty($error)) {
                DB::rollBack();
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $error], 422);
                }
                return Redirect::back()->with(messageError($error));
            }
            DB::commit();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Data Berhasil Disimpan']);
            }
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function cancelapprove($kode_izin_cuti)
    {
        /** @var User $user */
        $user = auth()->user();
        
        $kode_izin_cuti = Crypt::decrypt($kode_izin_cuti);
        $izincuti = Izincuti::where('kode_izin_cuti', $kode_izin_cuti)
            ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
            ->select('presensi_izincuti.*', 'karyawan.kode_cabang', 'karyawan.kode_dept')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($izincuti->kode_cabang, $userCabangs) || !in_array($izincuti->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin cuti ini.');
            }
        }

        // Anti Self-Approval Protection: Applicant cannot cancel approval/rejection of own request
        $approvalService = app(ApprovalService::class);
        if ($approvalService->isSelfApproval($user, $izincuti->nik)) {
            abort(403, 'Akses ditolak. Anda tidak diizinkan membatalkan persetujuan pengajuan milik Anda sendiri.');
        }
        
        DB::beginTransaction();
        try {
            if ($izincuti->status == 1) {
                $approves = Approveizincuti::where('kode_izin_cuti', $kode_izin_cuti)->get();
                foreach ($approves as $appr) {
                    $p = Presensi::find($appr->id_presensi);
                    if ($p) {
                        if (str_contains($p->keterangan ?? '', '[IZIN_SUSULAN]')) {
                            $p->update([
                                'status' => 'a',
                                'keterangan' => 'Tanpa Keterangan (Alpha) - Dibatalkan dari Cuti Susulan Ref: ' . $kode_izin_cuti,
                                'jam_in' => null,
                                'jam_out' => null
                            ]);
                        } else {
                            $p->delete();
                        }
                    }
                }
                Approveizincuti::where('kode_izin_cuti', $kode_izin_cuti)->delete();

                Izincuti::where('kode_izin_cuti', $kode_izin_cuti)->update([
                    'status' => 0,
                    'approval_step' => 0
                ]);
                DB::commit();
                return Redirect::back()->with(messageSuccess('Persetujuan izin cuti berhasil dibatalkan'));
            } else if ($izincuti->status == 2) {
                Izincuti::where('kode_izin_cuti', $kode_izin_cuti)->update([
                    'status' => 0,
                    'approval_step' => 0
                ]);
                DB::commit();
                return Redirect::back()->with(messageSuccess('Penolakan Berhasil Dibatalkan'));
            }
            return Redirect::back()->with(messageError('Status tidak valid untuk pembatalan.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function destroy(Request $request, $kode_izin_cuti)
    {
        /** @var User $user */
        $user = auth()->user();
        
        $kode_izin_cuti = Crypt::decrypt($kode_izin_cuti);
        $izincuti = Izincuti::where('kode_izin_cuti', $kode_izin_cuti)
            ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
            ->first();
        
        if (!$izincuti) {
            abort(404, 'Data izin cuti tidak ditemukan.');
        }

        if ($izincuti->status != 0) {
            $msg = 'Pengajuan izin cuti yang sudah diproses tidak dapat dihapus langsung. Batalkan persetujuan terlebih dahulu.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return Redirect::back()->with(messageError($msg));
        }
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            // Cek apakah user adalah pemilik izin (untuk karyawan)
            $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
            $isOwner = $userkaryawan && $userkaryawan->nik == $izincuti->nik;
            
            // Jika bukan pemilik, cek akses cabang/dept
            if (!$isOwner) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();
                
                if (!in_array($izincuti->kode_cabang, $userCabangs) || !in_array($izincuti->kode_dept, $userDepartemens)) {
                    abort(403, 'Anda tidak memiliki akses ke izin cuti ini.');
                }
            }
        }
        
        try {
            Izincuti::where('kode_izin_cuti', $kode_izin_cuti)->delete();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Data Berhasil Dihapus']);
            }
            return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function show($kode_izin_cuti)
    {
        /** @var User $user */
        $user = auth()->user();
        
        $kode_izin_cuti = Crypt::decrypt($kode_izin_cuti);
        $izincuti = Izincuti::where('kode_izin_cuti', $kode_izin_cuti)
            ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($izincuti->kode_cabang, $userCabangs) || !in_array($izincuti->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin cuti ini.');
            }
        }

        $data['izincuti'] = $izincuti;
        return view('izincuti.show', $data);
    }

    public function print($kode_izin_cuti)
    {
        /** @var User $user */
        $user = auth()->user();
        
        $kode_izin_cuti = Crypt::decrypt($kode_izin_cuti);
        $izincuti = Izincuti::where('kode_izin_cuti', $kode_izin_cuti)
            ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->join('cuti', 'presensi_izincuti.kode_cuti', '=', 'cuti.kode_cuti')
            ->select('presensi_izincuti.*', 'karyawan.nama_karyawan', 'karyawan.nik_show', 'karyawan.tanggal_masuk', 'karyawan.alamat', 'jabatan.nama_jabatan', 'departemen.nama_dept', 'cabang.nama_cabang', 'cuti.jenis_cuti', 'cuti.jumlah_hari as jatah_cuti_max')
            ->first();
            
        if (!$izincuti) {
            abort(404, 'Data tidak ditemukan.');
        }
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($izincuti->kode_cabang, $userCabangs) || !in_array($izincuti->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin cuti ini.');
            }
        }

        // Calculate leave statistics (annual leave)
        $tahun_cuti = date('Y', strtotime($izincuti->dari));
        $cek_cuti_dipakai = Approveizincuti::join('presensi', 'presensi_izincuti_approve.id_presensi', '=', 'presensi.id')
            ->where('presensi.nik', $izincuti->nik)
            ->whereRaw("YEAR(presensi.tanggal) = ?", [$tahun_cuti])
            ->count();
            
        $data['izincuti'] = $izincuti;
        $data['generalsetting'] = Pengaturanumum::where('id', 1)->first();
        
        // If it's C01 (Cuti Tahunan), calculate sisa
        if ($izincuti->kode_cuti == 'C01') {
            $data['sisa_cuti'] = $izincuti->jatah_cuti_max - $cek_cuti_dipakai;
            $data['cuti_dipakai'] = $cek_cuti_dipakai;
        } else {
            $data['sisa_cuti'] = null;
            $data['cuti_dipakai'] = null;
        }

        return view('izincuti.print', $data);
    }

    public function printReport(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $qcuti = Izincuti::query();
        $qcuti->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik');
        $qcuti->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan');
        $qcuti->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept');
        $qcuti->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang');
        $qcuti->join('cuti', 'presensi_izincuti.kode_cuti', '=', 'cuti.kode_cuti');
        
        // Filter berdasarkan akses cabang dan departemen jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!empty($userCabangs)) {
                $qcuti->whereIn('karyawan.kode_cabang', $userCabangs);
            } else {
                $qcuti->whereRaw('1 = 0');
            }
            
            if (!empty($userDepartemens)) {
                $qcuti->whereIn('karyawan.kode_dept', $userDepartemens);
            } else {
                $qcuti->whereRaw('1 = 0');
            }
        }
        
        $qcuti->select(
            'presensi_izincuti.*',
            'karyawan.nama_karyawan',
            'karyawan.nik_show',
            'jabatan.nama_jabatan',
            'departemen.nama_dept',
            'cabang.nama_cabang',
            'cuti.jenis_cuti',
            'presensi_izincuti.keterangan as nama_cuti'
        );

        if (!empty($request->dari) && !empty($request->sampai)) {
            $qcuti->whereBetween('presensi_izincuti.dari', [$request->dari, $request->sampai]);
        }
        if (!empty($request->nama_karyawan)) {
            $qcuti->where('karyawan.nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }
        if (!empty($request->kode_cabang)) {
            $qcuti->where('karyawan.kode_cabang', $request->kode_cabang);
        }
        if (!empty($request->kode_dept)) {
            $qcuti->where('karyawan.kode_dept', $request->kode_dept);
        }
        if (!empty($request->status) || $request->status === '0') {
            $qcuti->where('presensi_izincuti.status', $request->status);
        }

        $qcuti->orderBy('presensi_izincuti.status');
        $qcuti->orderBy('presensi_izincuti.dari', 'desc');
        
        $izincuti = $qcuti->get();

        // Get filter descriptions for reporting
        $filter_dari = $request->dari;
        $filter_sampai = $request->sampai;
        $filter_karyawan = $request->nama_karyawan;
        
        $filter_cabang = 'Semua Cabang';
        if (!empty($request->kode_cabang)) {
            $cab = Cabang::where('kode_cabang', $request->kode_cabang)->first();
            if ($cab) $filter_cabang = $cab->nama_cabang;
        }
        
        $filter_dept = 'Semua Departemen';
        if (!empty($request->kode_dept)) {
            $dept = Departemen::where('kode_dept', $request->kode_dept)->first();
            if ($dept) $filter_dept = $dept->nama_dept;
        }

        $filter_status = 'Semua Status';
        if ($request->status === '0') $filter_status = 'Pending';
        elseif ($request->status == '1') $filter_status = 'Disetujui';
        elseif ($request->status == '2') $filter_status = 'Ditolak';

        $data['izincuti'] = $izincuti;
        $data['generalsetting'] = Pengaturanumum::where('id', 1)->first();
        $data['filters'] = [
            'dari' => $filter_dari,
            'sampai' => $filter_sampai,
            'karyawan' => $filter_karyawan,
            'cabang' => $filter_cabang,
            'dept' => $filter_dept,
            'status' => $filter_status
        ];

        return view('izincuti.print_report', $data);
    }

    public function getsisaharicuti(Request $request)
    {
        $user = User::findorfail(auth()->user()->id);
        $role = $user->getRoleNames()->first();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
        $nik = $user->hasRole('karyawan') ? $userkaryawan->nik : $request->nik;
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $tahun_cuti = date('Y', strtotime($tanggal));
        $kode_cuti = $request->kode_cuti;
        $cuti = Cuti::where('kode_cuti', $kode_cuti)->first();
        if (!$cuti) {
            return response()->json(['status' => false, 'message' => 'Data jenis cuti tidak ditemukan']);
        }
        $jml_hari_max = $cuti->jumlah_hari;
        $cek_cuti_dipakai = Approveizincuti::join('presensi', 'presensi_izincuti_approve.id_presensi', '=', 'presensi.id')
            ->join('presensi_izincuti', 'presensi_izincuti_approve.kode_izin_cuti', '=', 'presensi_izincuti.kode_izin_cuti')
            ->where('presensi.nik', $nik)
            ->where('presensi_izincuti.kode_cuti', $cuti->kode_cuti)
            ->whereRaw("YEAR(presensi.tanggal) = ?", [$tahun_cuti])
            ->count();
        $sisa_cuti = max(0, $jml_hari_max - $cek_cuti_dipakai);
        $terpakai = $cek_cuti_dipakai;
        $message = ($cuti->kode_cuti == "C01")
            ? 'Sisa Cuti ' . $cuti->jenis_cuti . ' Anda Adalah ' . $sisa_cuti . ' Hari Lagi'
            : 'Batas Maksimal Cuti ' . $cuti->jenis_cuti . ' Anda Adalah ' . $jml_hari_max . ' Hari (Sisa: ' . $sisa_cuti . ' Hari)';
        return response()->json([
            'status' => true,
            'sisa_cuti' => $sisa_cuti,
            'kuota' => $jml_hari_max,
            'terpakai' => $terpakai,
            'nama_cuti' => $cuti->jenis_cuti,
            'message' => $message
        ]);
    }

    /**
     * Hitung Hari Kerja Efektif (Phase 7 Single Source of Truth Leave Preview)
     */
    public function hitungHariAjax(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;

        if (empty($dari) || empty($sampai)) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter tanggal dari dan sampai wajib diisi.',
                'jumlah_hari' => 0,
                'hari_kerja' => [],
                'hari_off' => []
            ], 400);
        }

        /** @var User $user */
        $user = auth()->user();
        $nik = null;
        if ($user && $user->hasRole('karyawan')) {
            $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
            $nik = $userkaryawan ? $userkaryawan->nik : null;
        } else {
            $nik = $request->nik;
            if (empty($nik) && $user) {
                $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
                $nik = $userkaryawan ? $userkaryawan->nik : null;
            }
        }

        try {
            $start = new \DateTime($dari);
            $end = new \DateTime($sampai);
            if ($end < $start) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal sampai tidak boleh sebelum tanggal dari.',
                    'jumlah_hari' => 0,
                    'hari_kerja' => [],
                    'hari_off' => []
                ], 422);
            }

            $hariKerja = [];
            $hariOff = [];

            if (!empty($nik)) {
                $schedules = AttendanceService::getEffectiveSchedulesBatch([$nik], $dari, $sampai);
                $curr = clone $start;
                while ($curr <= $end) {
                    $tglStr = $curr->format('Y-m-d');
                    $sched = $schedules[$nik][$tglStr] ?? null;
                    $isOff = $sched ? $sched['is_off'] : false;

                    if ($isOff) {
                        $hariOff[] = $tglStr;
                    } else {
                        $hariKerja[] = $tglStr;
                    }
                    $curr->modify('+1 day');
                }
            } else {
                $setting = Pengaturanumum::first();
                $sistem = $setting->sistem_hari_kerja ?? '6';
                $curr = clone $start;
                while ($curr <= $end) {
                    $tglStr = $curr->format('Y-m-d');
                    $w = (int)$curr->format('w');
                    $isOff = ($w === 0) || ($sistem === '5' && $w === 6);
                    if ($isOff) {
                        $hariOff[] = $tglStr;
                    } else {
                        $hariKerja[] = $tglStr;
                    }
                    $curr->modify('+1 day');
                }
            }

            return response()->json([
                'success' => true,
                'jumlah_hari' => count($hariKerja),
                'hari_kerja' => $hariKerja,
                'hari_off' => $hariOff
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung hari: ' . $e->getMessage(),
                'jumlah_hari' => 0,
                'hari_kerja' => [],
                'hari_off' => []
            ], 500);
        }
    }
}
