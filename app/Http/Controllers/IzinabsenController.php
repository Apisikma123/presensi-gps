<?php

namespace App\Http\Controllers;

use App\Models\Approveizinabsen;
use App\Models\Cabang;
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
use Jenssegers\Agent\Agent;


class IzinabsenController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $qizin = Izinabsen::query();
        $qizin->join('karyawan', 'presensi_izinabsen.nik', '=', 'karyawan.nik');
        $qizin->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan');
        $qizin->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept');
        $qizin->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang');

        // Filter berdasarkan akses cabang dan departemen jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!empty($userCabangs)) {
                $qizin->whereIn('karyawan.kode_cabang', $userCabangs);
            } else {
                $qizin->whereRaw('1 = 0');
            }
            
            if (!empty($userDepartemens)) {
                $qizin->whereIn('karyawan.kode_dept', $userDepartemens);
            } else {
                $qizin->whereRaw('1 = 0');
            }
        }

        if (!empty($request->dari) && !empty($request->sampai)) {
            $qizin->whereBetween('presensi_izinabsen.tanggal', [$request->dari, $request->sampai]);
        }
        if (!empty($request->nama_karyawan)) {
            $qizin->where('karyawan.nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }

        if (!empty($request->kode_cabang)) {
            $qizin->where('karyawan.kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->kode_dept)) {
            $qizin->where('karyawan.kode_dept', $request->kode_dept);
        }

        if (!empty($request->status) || $request->status === '0') {
            $qizin->where('presensi_izinabsen.status', $request->status);
        }
        $qizin->select(
            'presensi_izinabsen.*',
            'karyawan.nama_karyawan',
            'karyawan.nik_show',
            'jabatan.nama_jabatan',
            'karyawan.kode_dept',
            'karyawan.kode_cabang',
            'departemen.nama_dept',
            'cabang.nama_cabang'
        );
        $qizin->orderBy('presensi_izinabsen.status');
        $qizin->orderBy('presensi_izinabsen.tanggal', 'desc');
        $izinabsen = $qizin->paginate(15);
        $izinabsen->appends($request->all());

        $data['izinabsen'] = $izinabsen;
        $data['cabang'] = $user->getCabang();
        $data['departemen'] = $user->getDepartemen();
        return view('izinabsen.index', $data);
    }

    public function create(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();
        $agent = new Agent();
        $general_setting = Pengaturanumum::first();
        if ($user->hasRole('karyawan')) {
            return view('izinabsen.create-mobile', compact('general_setting'));
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
        $data['general_setting'] = $general_setting;

        if ($request->ajax()) {
            return view('izinabsen.create-modal', $data);
        }

        return view('izinabsen.create', $data);
    }

    public function edit($kode_izin)
    {
        /** @var User $user */
        $user = auth()->user();
        $kode_izin = Crypt::decrypt($kode_izin);
        $izinabsen = Izinabsen::where('kode_izin', $kode_izin)
            ->join('karyawan', 'presensi_izinabsen.nik', '=', 'karyawan.nik')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $karyawanData = Karyawan::where('nik', $izinabsen->nik)->first();
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($karyawanData->kode_cabang, $userCabangs) || !in_array($karyawanData->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin absen ini.');
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
        $data['izinabsen'] = $izinabsen;

        return view('izinabsen.edit', $data);
    }

    public function store(Request $request)
    {
        $user = User::findorfail(auth()->user()->id);
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
        $role = $user->getRoleNames()->first();
        $general_setting = Pengaturanumum::where('id', 1)->first();

        if ($user->hasRole('karyawan')) {
            if (!$userkaryawan || empty($userkaryawan->nik)) {
                return Redirect::back()->withInput()->with(messageError('Akun Anda belum terhubung dengan data karyawan. Silakan hubungi admin.'));
            }
            $nik = $userkaryawan->nik;
            $request->validate([
                'dari' => 'required|date',
                'sampai' => 'required|date',
                'keterangan' => 'required',
            ]);
        } else {
            $nik = $request->nik;
            $request->validate([
                'nik' => 'required',
                'dari' => 'required|date',
                'sampai' => 'required|date',
                'keterangan' => 'required',
            ]);
        }

        DB::beginTransaction();
        try {
            $jmlhari = hitungHari($request->dari, $request->sampai, $request->nik);
            $batasi_hari_izin = $general_setting->batasi_hari_izin ?? 0;
            $jml_hari_izin_max = $general_setting->jml_hari_izin_max ?? 0;

            if ($jmlhari > $jml_hari_izin_max && $batasi_hari_izin == 1) {
                $msg = 'Tidak Boleh Lebih dari ' . $jml_hari_izin_max . ' Hari!';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return Redirect::back()->withInput()->with(messageError($msg));
            }

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
            $lastizin = Izinabsen::select('kode_izin')
                ->whereRaw('YEAR(dari) = ?', [date('Y', strtotime($request->dari))])
                ->whereRaw('MONTH(dari) = ?', [date('m', strtotime($request->dari))])
                ->orderBy("kode_izin", "desc")
                ->first();
            $last_kode_izin = $lastizin != null ? $lastizin->kode_izin : '';
            $kode_izin  = buatkode($last_kode_izin, "IA"  . date('ym', strtotime($request->dari)), 4);

            $izin = new Izinabsen();
            $izin->kode_izin = $kode_izin;
            $izin->nik = $nik;
            $izin->tanggal = $request->dari;
            $izin->dari = $request->dari;
            $izin->sampai = $request->sampai;
            $izin->keterangan = $request->keterangan;
            $izin->status = 0;
            $izin->approval_step = 1;
            $izin->save();
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

    public function approve($kode_izin)
    {
        /** @var User $user */
        $user = auth()->user();
        
        $kode_izin = Crypt::decrypt($kode_izin);
        $izinabsen = Izinabsen::where('kode_izin', $kode_izin)
            ->join('karyawan', 'presensi_izinabsen.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($izinabsen->kode_cabang, $userCabangs) || !in_array($izinabsen->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin absen ini.');
            }
        }

        // Anti Self-Approval Protection
        $approvalService = app(ApprovalService::class);
        if ($approvalService->isSelfApproval($user, $izinabsen->nik)) {
            abort(403, 'Akses ditolak. Anda tidak diizinkan menyetujui (approve) pengajuan izin absen milik Anda sendiri.');
        }

        $data['izinabsen'] = $izinabsen;
        return view('izinabsen.approve', $data);
    }

    public function storeapprove(Request $request, $kode_izin, ApprovalService $approvalService)
    {
        /** @var User $user */
        $user = auth()->user();
        
        $kode_izin = Crypt::decrypt($kode_izin);
        $izinabsen = Izinabsen::where('kode_izin', $kode_izin)
            ->join('karyawan', 'presensi_izinabsen.nik', '=', 'karyawan.nik')
            ->select('presensi_izinabsen.*', 'karyawan.kode_cabang', 'karyawan.kode_dept', 'karyawan.kode_jabatan')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            // Untuk delegasi, gunakan cabang/dept admin
            $accessUser = $user->getApprovalAdmin() ?? $user;
            $userCabangs = $accessUser->getCabangCodes();
            $userDepartemens = $accessUser->getDepartemenCodes();
            
            if (!in_array($izinabsen->kode_cabang, $userCabangs) || !in_array($izinabsen->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin absen ini.');
            }
        }

        // Anti Self-Approval Protection: Applicant cannot approve their own request
        if ($approvalService->isSelfApproval($user, $izinabsen->nik)) {
            abort(403, 'Akses ditolak. Anda tidak diizinkan menyetujui (approve) pengajuan izin absen milik Anda sendiri.');
        }
        $dari = $izinabsen->dari;
        $sampai = $izinabsen->sampai;
        $nik = $izinabsen->nik;
        $kode_dept = $izinabsen->kode_dept;
        $kode_jabatan = $izinabsen->kode_jabatan;
        $kode_cabang = $izinabsen->kode_cabang;
        $error = '';
        
        // Dynamic Approval Logic
        $userRole = $user->getRoleNames()->first();
        $currentStep = $izinabsen->approval_step;
        $approvalUserId = $approvalService->getApprovalUserId($user);
        $approvalAdmin = $approvalUserId != $user->id ? User::find($approvalUserId) : $user;

        // Check Authorization using Service
        if (!$approvalService->canApprove('IZIN', $currentStep, $userRole, $kode_dept, $kode_jabatan, $user, $kode_cabang)) {
             if (!$user->isSuperAdmin()) {
                 return Redirect::back()->with(messageError('Anda tidak memiliki wewenang untuk approval tahap ke-' . $currentStep));
             }
        }

        DB::beginTransaction();
        try {
            if (isset($request->approve)) {
                // P1-1: Cegah approval izin menimpa presensi hadir aktual
                $hasActualAttendance = Presensi::where('nik', $nik)
                    ->whereBetween('tanggal', [$dari, $sampai])
                    ->where('status', 'h')
                    ->whereNotNull('jam_in')
                    ->exists();

                if ($hasActualAttendance) {
                    throw new \Exception('Karyawan sudah tercatat hadir pada tanggal ini. Izin/Sakit/Cuti tidak dapat disetujui sebelum data presensi dikoreksi.');
                }

                Izinabsen::where('kode_izin', $kode_izin)->update([
                    'status' => 1,
                    'approval_step' => 1
                ]);

                $karyawan = Karyawan::where('nik', $izinabsen->nik)->first();
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
                            $auditNote = "[IZIN_SUSULAN] Diubah dari ALPHA (a) ke IZIN. Approved by: " . ($user->name ?? 'Admin') . " on " . now()->format('Y-m-d H:i:s') . ". Ref: " . $kode_izin;
                        }

                        $presensi = Presensi::updateOrCreate(
                            ['nik' => $nik, 'tanggal' => $curr],
                            [
                                'kode_jam_kerja' => $jkCode,
                                'status' => 'i',
                                'keterangan' => $auditNote ?? ($izinabsen->keterangan ?? 'Izin Absen'),
                            ]
                        );

                        Approveizinabsen::updateOrCreate(
                            ['kode_izin' => $kode_izin, 'id_presensi' => $presensi->id],
                            ['id_presensi' => $presensi->id, 'kode_izin' => $kode_izin]
                        );
                    }

                    $curr = date('Y-m-d', strtotime($curr . ' +1 day'));
                }
            } else {
                Izinabsen::where('kode_izin', $kode_izin)->update([
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

    public function cancelapprove($kode_izin)
    {
        /** @var User $user */
        $user = auth()->user();
        
        $kode_izin = Crypt::decrypt($kode_izin);
        $izinabsen = Izinabsen::where('kode_izin', $kode_izin)
            ->join('karyawan', 'presensi_izinabsen.nik', '=', 'karyawan.nik')
            ->select('presensi_izinabsen.*', 'karyawan.kode_cabang', 'karyawan.kode_dept')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($izinabsen->kode_cabang, $userCabangs) || !in_array($izinabsen->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin absen ini.');
            }
        }
        
        // Anti Self-Approval Protection: Applicant cannot cancel approval of own request
        $approvalService = app(ApprovalService::class);
        if ($approvalService->isSelfApproval($user, $izinabsen->nik)) {
            abort(403, 'Akses ditolak. Anda tidak diizinkan membatalkan persetujuan pengajuan milik Anda sendiri.');
        }
        
        DB::beginTransaction();
        try {
            if ($izinabsen->status == 1) {
                $approves = Approveizinabsen::where('kode_izin', $kode_izin)->get();
                foreach ($approves as $appr) {
                    $p = Presensi::find($appr->id_presensi);
                    if ($p) {
                        if (str_contains($p->keterangan ?? '', '[IZIN_SUSULAN]')) {
                            // Revert back to Alpha if originally converted from Alpha
                            $p->update([
                                'status' => 'a',
                                'keterangan' => 'Tanpa Keterangan (Alpha) - Dibatalkan dari Izin Susulan Ref: ' . $kode_izin,
                                'jam_in' => null,
                                'jam_out' => null
                            ]);
                        } else {
                            $p->delete();
                        }
                    }
                }
                Approveizinabsen::where('kode_izin', $kode_izin)->delete();

                Izinabsen::where('kode_izin', $kode_izin)->update([
                    'status' => 0,
                    'approval_step' => 0
                ]);
                DB::commit();
                return Redirect::back()->with(messageSuccess('Persetujuan izin absen berhasil dibatalkan'));
            } else if ($izinabsen->status == 2) {
                Izinabsen::where('kode_izin', $kode_izin)->update([
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

    public function destroy(Request $request, $kode_izin)
    {
        /** @var User $user */
        $user = auth()->user();
        
        $kode_izin = Crypt::decrypt($kode_izin);
        $izinabsen = Izinabsen::where('kode_izin', $kode_izin)
            ->join('karyawan', 'presensi_izinabsen.nik', '=', 'karyawan.nik')
            ->first();
        
        if (!$izinabsen) {
            abort(404, 'Data izin absen tidak ditemukan.');
        }

        if ($izinabsen->status != 0) {
            $msg = 'Pengajuan izin absen yang sudah diproses tidak dapat dihapus langsung. Batalkan persetujuan terlebih dahulu.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return Redirect::back()->with(messageError($msg));
        }
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            // Cek apakah user adalah pemilik izin (untuk karyawan)
            $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
            $isOwner = $userkaryawan && $userkaryawan->nik == $izinabsen->nik;
            
            // Jika bukan pemilik, cek akses cabang/dept
            if (!$isOwner) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();
                
                if (!in_array($izinabsen->kode_cabang, $userCabangs) || !in_array($izinabsen->kode_dept, $userDepartemens)) {
                    abort(403, 'Anda tidak memiliki akses ke izin absen ini.');
                }
            }
        }
        
        try {
            Izinabsen::where('kode_izin', $kode_izin)->delete();
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

    public function update(Request $request, $kode_izin)
    {
        $kode_izin = Crypt::decrypt($kode_izin);
        $izinabsen = Izinabsen::where('kode_izin', $kode_izin)
            ->join('karyawan', 'presensi_izinabsen.nik', '=', 'karyawan.nik')
            ->first();

        if (!$izinabsen) {
            abort(404, 'Data izin tidak ditemukan.');
        }

        if ($izinabsen->status == 1) {
            $msg = 'Pengajuan izin absen yang sudah disetujui tidak dapat diedit langsung. Batalkan persetujuan terlebih dahulu.';
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
            if (empty($userCabangs) || !in_array($izinabsen->kode_cabang, $userCabangs)) {
                abort(403, 'Anda tidak memiliki akses ke cabang izin ini.');
            }
            if (empty($userDepartemens) || !in_array($izinabsen->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke departemen izin ini.');
            }
        }

        $request->validate([
            'nik' => 'required',
            'dari' => 'required',
            'sampai' => 'required',
            'keterangan' => 'required',
        ]);
        DB::beginTransaction();
        try {
            Izinabsen::where('kode_izin', $kode_izin)->update([
                'nik' => $request->nik,
                'tanggal' => $request->dari,
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'keterangan' => $request->keterangan
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


    public function show($kode_izin)
    {
        /** @var User $user */
        $user = auth()->user();
        
        $kode_izin = Crypt::decrypt($kode_izin);
        $izinabsen = Izinabsen::where('kode_izin', $kode_izin)
            ->join('karyawan', 'presensi_izinabsen.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($izinabsen->kode_cabang, $userCabangs) || !in_array($izinabsen->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin absen ini.');
            }
        }

        $data['izinabsen'] = $izinabsen;
        return view('izinabsen.show', $data);
    }
}
