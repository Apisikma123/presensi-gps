<?php

namespace App\Http\Controllers;

use App\Models\Approveizinsakit;
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
use Illuminate\Support\Facades\Storage;

class IzinsakitController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $qizin = Izinsakit::query();
        $qizin->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik');
        $qizin->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan');
        $qizin->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept');
        $qizin->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang');

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

        $qizin->select('presensi_izinsakit.*', 'karyawan.nama_karyawan', 'karyawan.nik_show', 'jabatan.nama_jabatan', 'departemen.nama_dept', 'cabang.nama_cabang', 'karyawan.kode_dept');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $qizin->whereBetween('presensi_izinsakit.tanggal', [$request->dari, $request->sampai]);
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
            $qizin->where('presensi_izinsakit.status', $request->status);
        }

        $qizin->orderBy('presensi_izinsakit.status');
        $qizin->orderBy('presensi_izinsakit.tanggal', 'desc');
        $izinsakit = $qizin->paginate(15);
        $izinsakit->appends($request->all());

        $data['cabang'] = $user->getCabang();
        $data['departemen'] = $user->getDepartemen();
        $data['izinsakit'] = $izinsakit;
        return view('izinsakit.index', $data);
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

        $data['karyawan'] = $karyawan;
        $data['general_setting'] = Pengaturanumum::first();

        if ($user->hasRole('karyawan')) {
            return view('izinsakit.create-mobile', $data);
        }

        if ($request->ajax()) {
            return view('izinsakit.create-modal', $data);
        }

        return view('izinsakit.create', $data);
    }

    public function edit($kode_izin_sakit)
    {
        /** @var User $user */
        $user = auth()->user();
        $kode_izin_sakit = Crypt::decrypt($kode_izin_sakit);
        $izinsakit = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $karyawanData = Karyawan::where('nik', $izinsakit->nik)->first();
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($karyawanData->kode_cabang, $userCabangs) || !in_array($karyawanData->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin sakit ini.');
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
        $data['izinsakit'] = $izinsakit;

        return view('izinsakit.edit', $data);
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
                'sid' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);
        } else {
            $nik = $request->nik;
            $request->validate([
                'nik' => 'required',
                'dari' => 'required|date',
                'sampai' => 'required|date',
                'keterangan' => 'required',
                'sid' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);
        }

        $dari = $request->dari;
        $sampai = $request->sampai;
        $jmlhari = hitungHari($request->dari, $request->sampai, $nik);
        $setting = Pengaturanumum::first();
        $batasi_hari_izin = $setting->batasi_hari_izin ?? 0;
        $jml_hari_izin_max = $setting->jml_hari_izin_max ?? 0;

        if ($jmlhari > $jml_hari_izin_max && $batasi_hari_izin == 1) {
            $msg = 'Tidak Boleh Lebih dari ' . $jml_hari_izin_max . ' Hari!';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return Redirect::back()->withInput()->with(messageError($msg));
        }

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
            $lastizinsakit = Izinsakit::select('kode_izin_sakit')
                ->whereRaw('YEAR(tanggal) = ?', [date('Y', strtotime($request->dari))])
                ->whereRaw('MONTH(tanggal) = ?', [date('m', strtotime($request->dari))])
                ->orderBy("kode_izin_sakit", "desc")
                ->first();
            $last_kode_izin_sakit = $lastizinsakit != null ? $lastizinsakit->kode_izin_sakit : '';
            $kode_izin_sakit  = buatkode($last_kode_izin_sakit, "IS"  . date('ym', strtotime($request->dari)), 4);

            $data_sid = [];
            if ($request->hasfile('sid')) {
                $sid_name = \App\Helpers\ImageOptimizer::saveAsWebp(
                    $request->file('sid'),
                    'uploads/sid',
                    $kode_izin_sakit . '_' . \Illuminate\Support\Str::random(24),
                    80,
                    1280,
                    'private'
                );
                $data_sid = [
                    'doc_sid' => $sid_name,
                ];
            }

            $dataizinsakit = [
                'kode_izin_sakit' => $kode_izin_sakit,
                'nik' => $nik,
                'tanggal' => $request->dari,
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'keterangan' => $request->keterangan,
                'status' => 0,
                'approval_step' => 1,
                'id_user' => $user->id,
            ];

            $data = array_merge($dataizinsakit, $data_sid);
            $simpandatasakit = Izinsakit::create($data);
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

    public function approve($kode_izin_sakit)
    {
        /** @var User $user */
        $user = auth()->user();
        
        $kode_izin_sakit = Crypt::decrypt($kode_izin_sakit);
        $izinabsen = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($izinabsen->kode_cabang, $userCabangs) || !in_array($izinabsen->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin sakit ini.');
            }
        }

        // Anti Self-Approval Protection
        $approvalService = app(ApprovalService::class);
        if ($approvalService->isSelfApproval($user, $izinabsen->nik)) {
            abort(403, 'Akses ditolak. Anda tidak diizinkan menyetujui (approve) pengajuan izin sakit milik Anda sendiri.');
        }

        $data['izinsakit'] = $izinabsen;
        return view('izinsakit.approve', $data);
    }

    public function storeapprove(Request $request, $kode_izin_sakit)
    {
        /** @var User $user */
        $user = auth()->user();
        
        $kode_izin_sakit = Crypt::decrypt($kode_izin_sakit);
        $izinsakit = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
            ->select('presensi_izinsakit.*', 'karyawan.kode_dept', 'karyawan.kode_cabang', 'karyawan.kode_jabatan')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            // Untuk delegasi, gunakan cabang/dept admin
            $accessUser = $user->getApprovalAdmin() ?? $user;
            $userCabangs = $accessUser->getCabangCodes();
            $userDepartemens = $accessUser->getDepartemenCodes();
            
            if (!in_array($izinsakit->kode_cabang, $userCabangs) || !in_array($izinsakit->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin sakit ini.');
            }
        }

        // Anti Self-Approval Protection: Applicant cannot approve their own request
        $approvalService = app(ApprovalService::class);
        if ($approvalService->isSelfApproval($user, $izinsakit->nik)) {
            abort(403, 'Akses ditolak. Anda tidak diizinkan menyetujui (approve) pengajuan izin sakit milik Anda sendiri.');
        }

        // Dynamic Approval Logic
        $userRole = $user->getRoleNames()->first();
        $currentStep = $izinsakit->approval_step;
        $approvalUserId = $approvalService->getApprovalUserId($user);
        $approvalAdmin = $approvalUserId != $user->id ? User::find($approvalUserId) : $user;

        // Check Authorization using Service
        $kode_cabang = $izinsakit->kode_cabang;
        if (!$approvalService->canApprove('IZIN', $currentStep, $userRole, $izinsakit->kode_dept, $izinsakit->kode_jabatan, $user, $kode_cabang)) {
             if (!$user->isSuperAdmin()) {
                 return Redirect::back()->with(messageError('Anda tidak memiliki wewenang untuk approval tahap ke-' . $currentStep));
             }
        }
        
        $dari = $izinsakit->dari;
        $sampai = $izinsakit->sampai;
        $nik = $izinsakit->nik;
        $kode_dept = $izinsakit->kode_dept;
        $kode_jabatan = $izinsakit->kode_jabatan;
        $error = '';
        DB::beginTransaction();
        try {
            if (isset($request->approve)) {
                // P1-1: Cegah approval sakit menimpa presensi hadir aktual
                $hasActualAttendance = Presensi::where('nik', $nik)
                    ->whereBetween('tanggal', [$dari, $sampai])
                    ->where('status', 'h')
                    ->whereNotNull('jam_in')
                    ->exists();

                if ($hasActualAttendance) {
                    throw new \Exception('Karyawan sudah tercatat hadir pada tanggal ini. Izin/Sakit/Cuti tidak dapat disetujui sebelum data presensi dikoreksi.');
                }

                Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->update([
                    'status' => 1,
                    'approval_step' => 1
                ]);

                $karyawan = Karyawan::where('nik', $izinsakit->nik)->first();
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
                        
                        if ($existing && ($existing->status === 'h' || !empty($existing->jam_in))) {
                            // Anti-Overwrite: Physical attendance must NEVER be replaced by leave approval
                            $presensi = $existing;
                        } else {
                            $auditNote = null;
                            if ($existing && $existing->status === 'a') {
                                $auditNote = "[IZIN_SUSULAN] Diubah dari ALPHA (a) ke SAKIT. Approved by: " . ($user->name ?? 'Admin') . " on " . now()->format('Y-m-d H:i:s') . ". Ref: " . $kode_izin_sakit;
                            }

                            $presensi = Presensi::updateOrCreate(
                                ['nik' => $nik, 'tanggal' => $curr],
                                [
                                    'kode_jam_kerja' => $jkCode,
                                    'status' => 's',
                                    'keterangan' => $auditNote ?? ($izinsakit->keterangan ?? 'Izin Sakit'),
                                ]
                            );
                        }

                        Approveizinsakit::updateOrCreate(
                            ['kode_izin_sakit' => $kode_izin_sakit, 'id_presensi' => $presensi->id],
                            ['id_presensi' => $presensi->id, 'kode_izin_sakit' => $kode_izin_sakit]
                        );
                    }

                    $curr = date('Y-m-d', strtotime($curr . ' +1 day'));
                }
            } else {
                Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->update([
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


    public function cancelapprove($kode_izin_sakit)
    {
        /** @var User $user */
        $user = auth()->user();
        
        $kode_izin_sakit = Crypt::decrypt($kode_izin_sakit);
        $izinsakit = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
            ->select('presensi_izinsakit.*', 'karyawan.kode_cabang', 'karyawan.kode_dept')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($izinsakit->kode_cabang, $userCabangs) || !in_array($izinsakit->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin sakit ini.');
            }
        }
        
        // Anti Self-Approval Protection: Applicant cannot cancel approval of own request
        $approvalService = app(ApprovalService::class);
        if ($approvalService->isSelfApproval($user, $izinsakit->nik)) {
            abort(403, 'Akses ditolak. Anda tidak diizinkan membatalkan persetujuan pengajuan milik Anda sendiri.');
        }
        
        DB::beginTransaction();
        try {
            if ($izinsakit->status == 1) {
                $approves = Approveizinsakit::where('kode_izin_sakit', $kode_izin_sakit)->get();
                foreach ($approves as $appr) {
                    $p = Presensi::find($appr->id_presensi);
                    if ($p) {
                        if ($p->status === 'h' || !empty($p->jam_in)) {
                            // Retain actual physical attendance intact
                        } elseif (str_contains($p->keterangan ?? '', '[IZIN_SUSULAN]')) {
                            $p->update([
                                'status' => 'a',
                                'keterangan' => 'Tanpa Keterangan (Alpha) - Dibatalkan dari Izin Sakit Susulan Ref: ' . $kode_izin_sakit,
                                'jam_in' => null,
                                'jam_out' => null
                            ]);
                        } else {
                            $p->delete();
                        }
                    }
                }
                Approveizinsakit::where('kode_izin_sakit', $kode_izin_sakit)->delete();

                Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->update([
                    'status' => 0,
                    'approval_step' => 0
                ]);
                DB::commit();
                return Redirect::back()->with(messageSuccess('Persetujuan izin sakit berhasil dibatalkan'));
            } else if ($izinsakit->status == 2) {
                Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->update([
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

    public function update(Request $request, $kode_izin_sakit)
    {
        $kode_izin_sakit = Crypt::decrypt($kode_izin_sakit);
        $izinsakit = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
            ->first();

        if (!$izinsakit) {
            abort(404, 'Data izin sakit tidak ditemukan.');
        }

        if ($izinsakit->status == 1) {
            $msg = 'Pengajuan izin sakit yang sudah disetujui tidak dapat diedit langsung. Batalkan persetujuan terlebih dahulu.';
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
            if (empty($userCabangs) || !in_array($izinsakit->kode_cabang, $userCabangs)) {
                abort(403, 'Anda tidak memiliki akses ke cabang izin sakit ini.');
            }
            if (empty($userDepartemens) || !in_array($izinsakit->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke departemen izin sakit ini.');
            }
        }

        $request->validate([
            'nik' => 'required',
            'dari' => 'required',
            'sampai' => 'required',
            'keterangan' => 'required',
            'sid' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);
        DB::beginTransaction();
        try {
            $data_sid = [];
            if ($request->hasfile('sid')) {
                if (!empty($izinsakit->doc_sid)) {
                    if (Storage::disk('private')->exists('uploads/sid/' . $izinsakit->doc_sid)) {
                        Storage::disk('private')->delete('uploads/sid/' . $izinsakit->doc_sid);
                    }
                    if (Storage::disk('public')->exists('uploads/sid/' . $izinsakit->doc_sid)) {
                        Storage::disk('public')->delete('uploads/sid/' . $izinsakit->doc_sid);
                    }
                }

                $sid_name = \App\Helpers\ImageOptimizer::saveAsWebp(
                    $request->file('sid'),
                    'uploads/sid',
                    $kode_izin_sakit . '_' . \Illuminate\Support\Str::random(24),
                    80,
                    1280,
                    'private'
                );
                $data_sid = [
                    'doc_sid' => $sid_name,
                ];
            }

            $dataizinsakit = [
                'nik' => $request->nik,
                'tanggal' => $request->dari,
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'keterangan' => $request->keterangan,
            ];

            $data = array_merge($dataizinsakit, $data_sid);

            $simpandatasakit = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->update($data);
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


    public function destroy(Request $request, $kode_izin_sakit)
    {
        /** @var User $user */
        $user = auth()->user();
        
        $kode_izin_sakit = Crypt::decrypt($kode_izin_sakit);
        $izinsakit = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
            ->first();
        
        if (!$izinsakit) {
            abort(404, 'Data izin sakit tidak ditemukan.');
        }

        if ($izinsakit->status != 0) {
            $msg = 'Pengajuan izin sakit yang sudah diproses tidak dapat dihapus langsung. Batalkan persetujuan terlebih dahulu.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return Redirect::back()->with(messageError($msg));
        }
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            // Cek apakah user adalah pemilik izin (untuk karyawan)
            $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
            $isOwner = $userkaryawan && $userkaryawan->nik == $izinsakit->nik;
            
            // Jika bukan pemilik, cek akses cabang/dept
            if (!$isOwner) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();
                
                if (!in_array($izinsakit->kode_cabang, $userCabangs) || !in_array($izinsakit->kode_dept, $userDepartemens)) {
                    abort(403, 'Anda tidak memiliki akses ke izin sakit ini.');
                }
            }
        }
        
        try {
            if (!empty($izinsakit->doc_sid)) {
                if (Storage::disk('private')->exists('uploads/sid/' . $izinsakit->doc_sid)) {
                    Storage::disk('private')->delete('uploads/sid/' . $izinsakit->doc_sid);
                }
                if (Storage::disk('public')->exists('uploads/sid/' . $izinsakit->doc_sid)) {
                    Storage::disk('public')->delete('uploads/sid/' . $izinsakit->doc_sid);
                }
            }
            Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->delete();
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


    public function show($kode_izin_sakit)
    {
        /** @var User $user */
        $user = auth()->user();
        
        $kode_izin_sakit = Crypt::decrypt($kode_izin_sakit);
        $izinabsen = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($izinabsen->kode_cabang, $userCabangs) || !in_array($izinabsen->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin sakit ini.');
            }
        }

        $data['izinsakit'] = $izinabsen;
        return view('izinsakit.show', $data);
    }
}
