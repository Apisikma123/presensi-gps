<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Facerecognition;
use App\Models\Jamkerja;
use App\Models\Karyawan;
use App\Models\Pengaturanumum;
use App\Models\Presensi;
use App\Models\User;
use App\Models\Userkaryawan;
use App\Services\AttendanceService;
use Carbon\Carbon;
use CURLFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{

    public function index(Request $request)
    {
       
        $user = auth()->user();

        $tanggal = !empty($request->tanggal) ? $request->tanggal : date('Y-m-d');
        $presensi = Presensi::join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->select(
                'presensi.id',
                'presensi.nik',
                'presensi.tanggal',
                'presensi.kode_jam_kerja',
                'nama_jam_kerja',
                'jam_masuk',
                'jam_pulang',
                'istirahat',
                'jam_awal_istirahat',
                'jam_akhir_istirahat',
                'jam_in',
                'foto_in',
                'jam_out',
                'foto_out',
                'status',
                'lintashari',
                'total_jam',
                'is_dispensasi',
                'presensi_jamkerja.batas_toleransi'
            )
            ->where('presensi.tanggal', $tanggal);

        $query = Karyawan::query();
        $query->where(function($q) use ($tanggal) {
            $q->where('karyawan.status_aktif_karyawan', 1)
              ->orWhere('karyawan.tanggal_nonaktif', '>=', $tanggal);
        });
        $query->select(
            'presensi.id',
            'karyawan.nik',
            'karyawan.nik_show',
            'nama_karyawan',
            'kode_dept',
            'kode_cabang',
            'presensi.tanggal as tanggal_presensi',
            'presensi.jam_in',
            'presensi.kode_jam_kerja',
            'nama_jam_kerja',
            'jam_masuk',
            'jam_pulang',
            'istirahat',
            'jam_awal_istirahat',
            'jam_akhir_istirahat',
            'jam_in',
            'jam_out',
            'status',
            'foto_in',
            'foto_out',
            'lintashari',
            'karyawan.pin',
            'total_jam',
            'presensi.is_dispensasi',
            'presensi.batas_toleransi'
        );
        $query->leftjoinSub($presensi, 'presensi', function ($join) {
            $join->on('karyawan.nik', '=', 'presensi.nik');
        });
        
        // Filter berdasarkan akses cabang dan departemen jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!empty($userCabangs)) {
                $query->whereIn('karyawan.kode_cabang', $userCabangs);
            } else {
                $query->whereRaw('1 = 0');
            }
            
            if (!empty($userDepartemens)) {
                $query->whereIn('karyawan.kode_dept', $userDepartemens);
            } else {
                $query->whereRaw('1 = 0');
            }
        }
        
        $query->orderBy('nama_karyawan');
        if (!empty($request->kode_cabang)) {
            $query->where('karyawan.kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->nama_karyawan)) {
            $query->where('nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }

        if (!empty($request->status)) {
            if ($request->status == 'h') {
                $query->where('presensi.status', 'h');
            } elseif ($request->status == 'telat') {
                $query->where('presensi.status', 'h')
                    ->where(function ($q) {
                        $q->whereNull('presensi.is_dispensasi')->orWhere('presensi.is_dispensasi', '!=', 1);
                    })
                    ->where(function ($q) {
                        $q->where('presensi.is_terlambat', 1)
                            ->orWhere(function ($sub) {
                                $sub->whereNull('presensi.is_terlambat')
                                    ->whereRaw("TIME(presensi.jam_in) > COALESCE(presensi.batas_toleransi, '08:00:00')");
                            });
                    });
            } elseif ($request->status == 'tepat') {
                $query->where('presensi.status', 'h')
                    ->where(function ($q) {
                        $q->where('presensi.is_dispensasi', 1)
                            ->orWhere('presensi.is_terlambat', 0)
                            ->orWhere(function ($sub) {
                                $sub->whereNull('presensi.is_terlambat')
                                    ->whereRaw("TIME(presensi.jam_in) <= COALESCE(presensi.batas_toleransi, '08:00:00')");
                            });
                    });
            } elseif (in_array($request->status, ['i', 's', 'c'])) {
                $query->where('presensi.status', $request->status);
            } elseif ($request->status == 'alpa') {
                $query->where(function ($q) {
                    $q->whereNull('presensi.status')
                        ->orWhere('presensi.status', 'a');
                });
            }
        }

        $karyawan = $query->paginate(10);
        $karyawan->appends(request()->all());
        $data['karyawan'] = $karyawan;
        $data['cabang'] = $user->getCabang();

        // Read dynamic archive metadata from manifest if present
        $archiveManifestPath = storage_path('app/private/attendance-archive/index.json');
        $archives = [];
        if (file_exists($archiveManifestPath)) {
            $archives = json_decode(file_get_contents($archiveManifestPath), true) ?: [];
        }
        $data['archives'] = $archives;

        return view('presensi.index', $data);
    }
    public function create(Request $request)
    {
        $kode_jam_kerja = $request->kode_jam_kerja ?? null;

        // Get Data Karyawan By User
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }
        $userkaryawan = $user->userkaryawan ?? Userkaryawan::where('id_user', $user->id)->first();
        
        if (!$userkaryawan) {
            return redirect()->route('dashboard.index')->with(['error' => 'Halaman Presensi Masuk/Pulang khusus untuk akun karyawan. Akun Anda tidak terhubung dengan data karyawan.']);
        }
        
        $karyawan = Karyawan::where('nik', $userkaryawan->nik)->first();
        if (!$karyawan) {
            return redirect()->route('dashboard.index')->with(['error' => 'Data master karyawan Anda tidak ditemukan di sistem.']);
        }

        $general_setting = Pengaturanumum::getSetting();

        if ($karyawan->lock_jam_kerja == 0 && $kode_jam_kerja == null) {
            $cabang = Cabang::where('kode_cabang', $karyawan->kode_cabang)->first();
            $timezone_cabang = $cabang->timezone ?? $general_setting->timezone ?? config('app.timezone');
            $carbon_now = Carbon::now($timezone_cabang);
            $tgl_cabang = $carbon_now->format('Y-m-d');

            $presensi = Presensi::where('nik', $karyawan->nik)->where('tanggal', $tgl_cabang)->first();
            if ($presensi != null) {
                return redirect('/presensi/create?kode_jam_kerja=' . $presensi->kode_jam_kerja);
            }
            $data['jamkerja'] = Jamkerja::orderBy('jam_masuk')->get();
            return view('presensi.pilih_jam_kerja', $data);
        }

        // Cek Lokasi Kantor
        $lokasi_kantor = Cabang::where('kode_cabang', $karyawan->kode_cabang)->first();
        if (!$lokasi_kantor) {
            $lokasi_kantor = Cabang::first();
        }

        // Ambil timezone dari cabang (jika ada), jika tidak gunakan default sistem
        $timezone_cabang = $lokasi_kantor->timezone ?? $general_setting->timezone ?? config('app.timezone');

        // Gunakan Carbon dengan timezone cabang untuk mendapatkan waktu lokal cabang
        $carbon_now = Carbon::now($timezone_cabang);
        $hariini = $carbon_now->format('Y-m-d');
        $jamsekarang = $carbon_now->format('H:i');
        $tgl_sebelumnya = $carbon_now->copy()->subDay()->format('Y-m-d');
        $cekpresensi_sebelumnya = Presensi::join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->where('tanggal', $tgl_sebelumnya)
            ->where('nik', $karyawan->nik)
            ->first();

        // dd($cekpresensi_sebelumnya);
        $ceklintashari_presensi = $cekpresensi_sebelumnya != null  ? $cekpresensi_sebelumnya->lintashari : 0;

        if ($ceklintashari_presensi == 1 && ($cekpresensi_sebelumnya->jam_out == null)) {
            // Tentukan batas: prioritas dari jam kerja, fallback ke general setting
            $batas_lh = $cekpresensi_sebelumnya->batas_presensi_pulang ?? $general_setting->batas_presensi_lintashari;
            if ($jamsekarang < $batas_lh) {
                $hariini = $tgl_sebelumnya;
            }
        }

        $namahari = getnamaHari(date('D', strtotime($hariini)));

        $kode_dept = $karyawan->kode_dept;

        //Cek Presensi
        $presensi = Presensi::where('nik', $karyawan->nik)->where('tanggal', $hariini)->first();

        // Enforce Effective Schedule from AttendanceService
        $effectiveSchedule = \App\Services\AttendanceService::getEffectiveSchedule($karyawan->nik, $hariini, $karyawan);

        // Phase 3 UI Guard: If scheduled OFF and not yet clocked in, block presensi form
        if ($effectiveSchedule['is_off'] && ($presensi == null || $presensi->jam_in == null)) {
            $data['keterangan_libur'] = $effectiveSchedule['keterangan'] ?? 'Hari ini Anda dijadwalkan LIBUR / OFF. Tidak ada kewajiban presensi.';
            $data['hariini'] = $hariini;
            $data['karyawan'] = $karyawan;
            return view('presensi.notif_libur', $data);
        }

        if (!$effectiveSchedule['is_off'] && !empty($effectiveSchedule['jam_kerja'])) {
            $jamkerja = $effectiveSchedule['jam_kerja'];
        } elseif ($kode_jam_kerja != null) {
            $jamkerja = Jamkerja::where('kode_jam_kerja', $kode_jam_kerja)->first();
        } else {
            $kode_jk = $karyawan->kode_jam_kerja ?: 'JK01';
            $jamkerja = Jamkerja::where('kode_jam_kerja', $kode_jk)->first();
        }

        if ($presensi != null && $presensi->status != 'h') {
            return view('presensi.notif_izin');
        } else if ($jamkerja == null) {
            return view('presensi.notif_jamkerja');
        }

        // Phase 4: Enforce Expected Branch from Schedule
        $expectedCabangCode = !empty($effectiveSchedule['kode_cabang']) ? $effectiveSchedule['kode_cabang'] : $karyawan->kode_cabang;
        $scheduledCabang = Cabang::getByCode($expectedCabangCode) ?? Cabang::where('kode_cabang', $expectedCabangCode)->first();
        if ($scheduledCabang) {
            $lokasi_kantor = $scheduledCabang;
        }

        // Lock dropdown to scheduled branch
        $data['cabang'] = collect([$lokasi_kantor]);

        $data['hariini'] = $hariini;
        $data['jam_kerja'] = $jamkerja;
        $data['lokasi_kantor'] = $lokasi_kantor;
        $data['presensi'] = $presensi;
        $data['karyawan'] = $karyawan;
        $user_wajah = Facerecognition::where('nik', $karyawan->nik)->select('id', 'nik', 'wajah', 'descriptor')->get();
        $data['wajah'] = $user_wajah->count();
        $data['user_wajah'] = $user_wajah;

        // Cek apakah hari ini Hari Libur Resmi
        $hari_libur = DB::table('hari_libur_detail')
            ->join('hari_libur', 'hari_libur_detail.kode_libur', '=', 'hari_libur.kode_libur')
            ->where('hari_libur_detail.nik', $karyawan->nik)
            ->where('hari_libur.tanggal', $hariini)
            ->select('hari_libur.*')
            ->first();

        if (!$hari_libur) {
            $hari_libur = DB::table('hari_libur')
                ->where('tanggal', $hariini)
                ->where(function ($q) use ($karyawan) {
                    $q->where('kode_cabang', $karyawan->kode_cabang ?? '')
                        ->orWhere('kode_cabang', 'ALL');
                })
                ->first();
        }
        $attendanceNonce = bin2hex(random_bytes(16));
        session(['attendance_nonce' => $attendanceNonce, 'attendance_nonce_time' => now()->timestamp]);
        $data['attendance_nonce'] = $attendanceNonce;

        return view('presensi.create', $data);
    }

    public function store(Request $request, AttendanceService $attendanceService)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $imageInput = $request->hasFile('image') ? $request->file('image') : $request->image;
        $data = [
            'status' => $request->status,
            'lokasi' => $request->lokasi,
            'kode_jam_kerja' => $request->kode_jam_kerja,
            'lokasi_cabang' => $request->lokasi_cabang,
            'image' => $imageInput,
            'is_mock' => $request->is_mock,
            'face_descriptor' => $request->face_descriptor,
            'attendance_nonce' => $request->attendance_nonce,
            'early_out_reason' => $request->early_out_reason,
        ];

        if ($request->status == 1) {
            $result = $attendanceService->clockIn($user, $data);
        } else {
            $result = $attendanceService->clockOut($user, $data);
        }

        if (!$result['success']) {
            return response()->json([
                'status' => false,
                'message' => $result['message'],
                'notifikasi' => $result['notifikasi'] ?? null,
                'suara' => $result['suara'] ?? null,
                'new_nonce' => $result['new_nonce'] ?? null,
            ], $result['code'] ?? 400);
        }

        return response()->json([
            'status' => true,
            'message' => $result['message'],
            'notifikasi' => $result['notifikasi'] ?? null,
            'is_terlambat' => $result['is_terlambat'] ?? false,
            'menit_terlambat' => $result['menit_terlambat'] ?? 0,
            'suara' => $result['suara'] ?? null,
            'new_nonce' => $result['new_nonce'] ?? null,
        ], $result['code'] ?? 200);
    }


    function sendwa($no_hp, $message)
    {
        // WA Gateway disabled
        return;
    }
    public function edit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $nik = Crypt::decrypt($request->nik);
        $tanggal = $request->tanggal;

        $karyawan = Karyawan::where('nik', $nik)->first();
        if (!$karyawan) {
            return '<div class="alert alert-danger">Karyawan tidak ditemukan</div>';
        }

        $presensi = Presensi::where('nik', $nik)->where('tanggal', $tanggal)->first();
        $effectiveSchedule = AttendanceService::getEffectiveSchedule($nik, $tanggal, $karyawan);
        $resolvedDutyBranch = !empty($effectiveSchedule['kode_cabang']) ? $effectiveSchedule['kode_cabang'] : null;
        $attendanceBranch = !empty($presensi?->kode_cabang) ? $presensi->kode_cabang : null;
        $targetBranch = $attendanceBranch ?: ($resolvedDutyBranch ?: $karyawan->kode_cabang);

        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            $isBranchAuthorized = in_array($targetBranch, $userCabangs) || in_array($karyawan->kode_cabang, $userCabangs);
            $isDeptAuthorized = empty($userDepartemens) || in_array($karyawan->kode_dept, $userDepartemens);

            if (!$isBranchAuthorized || !$isDeptAuthorized) {
                return '<div class="alert alert-danger">Anda tidak memiliki akses ke data presensi penugasan cabang ini.</div>';
            }
        }

        $jam_kerja = Jamkerja::all();
        if ($presensi && $presensi->status_potongan !== null) {
            return '<div class="alert alert-warning">Data Presensi Sudah Dikunci, Hubungi Admin Untuk Membuka Kunci Laporan</div>';
        }
        $data['presensi'] = $presensi;
        $data['karyawan'] = $karyawan;
        $data['jam_kerja'] = $jam_kerja;
        $data['tanggal'] = $tanggal;

        return view('presensi.edit', $data);
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $request->validate([
            'nik' => 'required',
            'tanggal' => 'required',
            'kode_jam_kerja' => 'required',
            'status' => 'required',
            'alasan_koreksi' => 'required|string|max:500',
        ], [
            'alasan_koreksi.required' => 'Alasan koreksi wajib diisi.',
        ]);

        $nik = Crypt::decrypt($request->nik);
        $karyawan = Karyawan::where('nik', $nik)->first();
        if (!$karyawan) {
            return Redirect::back()->with(messageError('Karyawan tidak ditemukan'));
        }

        $tanggal = $request->tanggal;
        $presensi = Presensi::where('nik', $nik)->where('tanggal', $tanggal)->first();
        $effectiveSchedule = AttendanceService::getEffectiveSchedule($nik, $tanggal, $karyawan);
        $resolvedDutyBranch = !empty($effectiveSchedule['kode_cabang']) ? $effectiveSchedule['kode_cabang'] : null;
        $attendanceBranch = !empty($presensi?->kode_cabang) ? $presensi->kode_cabang : null;
        $targetBranch = $attendanceBranch ?: ($resolvedDutyBranch ?: $karyawan->kode_cabang);

        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            $isBranchAuthorized = in_array($targetBranch, $userCabangs) || in_array($karyawan->kode_cabang, $userCabangs);
            $isDeptAuthorized = empty($userDepartemens) || in_array($karyawan->kode_dept, $userDepartemens);

            if (!$isBranchAuthorized || !$isDeptAuthorized) {
                return Redirect::back()->with(messageError('Anda tidak memiliki akses ke data presensi penugasan cabang ini'));
            }
        }

        if ($presensi && $presensi->status_potongan !== null) {
            return redirect()->back()->with(['warning' => 'Data Presensi Sudah Dikunci, Hubungi Admin Untuk Membuka Kunci Laporan']);
        }

        $kode_jam_kerja = $request->kode_jam_kerja;
        $status = $request->status;

        $jam_in = !empty($request->jam_in) ? (strlen($request->jam_in) <= 8 ? ($tanggal . ' ' . $request->jam_in) : $request->jam_in) : null;
        $jam_out = !empty($request->jam_out) ? (strlen($request->jam_out) <= 8 ? ($tanggal . ' ' . $request->jam_out) : $request->jam_out) : null;
        $istirahat_out = null;
        $istirahat_in = null;

        if ($status !== 'h') {
            $jam_in = null;
            $jam_out = null;
        }

        // P1-2: Audit Trail koreksi manual presensi & resolving duty branch
        $effectiveSchedule = AttendanceService::getEffectiveSchedule($nik, $tanggal, $karyawan);
        $resolvedBranch = !empty($effectiveSchedule['kode_cabang']) ? $effectiveSchedule['kode_cabang'] : ($karyawan->kode_cabang ?? null);

        $auditData = [
            'last_corrected_by' => $user?->id ?? auth()->id(),
            'last_correction_reason' => $request->alasan_koreksi,
            'last_corrected_at' => Carbon::now(config('app.timezone')),
        ];

        try {
            $cekpresensi = Presensi::where('nik', $nik)->where('tanggal', $tanggal)->first();
            if (!empty($cekpresensi)) {
                $updateData = array_merge([
                    'jam_in' => $jam_in,
                    'jam_out' => $jam_out,
                    'istirahat_out' => $istirahat_out,
                    'istirahat_in' => $istirahat_in,
                    'status' => $status,
                    'kode_jam_kerja' => $kode_jam_kerja,
                ], $auditData);

                if (empty($cekpresensi->kode_cabang) && !empty($resolvedBranch)) {
                    $updateData['kode_cabang'] = $resolvedBranch;
                }

                $cekpresensi->update($updateData);
            } else {
                Presensi::create(array_merge([
                    'nik' => $nik,
                    'tanggal' => $tanggal,
                    'jam_in' => $jam_in,
                    'jam_out' => $jam_out,
                    'istirahat_out' => $istirahat_out,
                    'istirahat_in' => $istirahat_in,
                    'kode_jam_kerja' => $kode_jam_kerja,
                    'kode_cabang' => $resolvedBranch,
                    'status' => $status,
                ], $auditData));
            }

            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }


    public function show($id, $status)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $presensi = Presensi::where('presensi.id', $id)
            ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->select('presensi.*', 'karyawan.nama_karyawan', 'karyawan.kode_cabang', 'karyawan.kode_dept', 'departemen.nama_dept', 'jabatan.nama_jabatan', 'cabang.nama_cabang', 'cabang.lokasi_cabang')
            ->first();

        if (!$presensi) {
            abort(404, 'Data presensi tidak ditemukan.');
        }

        if (!$user->isSuperAdmin() && !$user->can('presensi.index')) {
            $userkaryawan = $user->userkaryawan ?? Userkaryawan::where('id_user', $user->id)->first();
            if (!$userkaryawan || $presensi->nik !== $userkaryawan->nik) {
                abort(403, 'Anda tidak memiliki hak akses ke data presensi ini.');
            }
        } elseif (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            if (empty($userCabangs) || !in_array($presensi->kode_cabang, $userCabangs)) {
                abort(403, 'Anda tidak memiliki akses ke cabang presensi ini.');
            }
            if (empty($userDepartemens) || !in_array($presensi->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke departemen presensi ini.');
            }
        }

        $cabang = Cabang::where('kode_cabang', $presensi->kode_cabang)->first();
        $lokasi = explode(',', $cabang->lokasi_cabang);
        $data['latitude'] = $lokasi[0];
        $data['longitude'] = $lokasi[1];
        // if (!empty($presensi->lokasi_cabang)) {
        //     $lokasi = explode(',', $presensi->lokasi_cabang);
        //     $data['latitude'] = $lokasi[0];
        //     $data['longitude'] = $lokasi[1];
        // } else {
        //     $data['latitude'] = $cabang->latitude_cabang;
        //     $data['longitude'] = $cabang->longitude_cabang;
        // }
        $data['presensi'] = $presensi;
        $data['status'] = $status;
        $data['cabang'] = $cabang;

        return view('presensi.show', $data);
    }





    public function histori(Request $request)
    {
        $userkaryawan = auth()->user()->userkaryawan ?? Userkaryawan::where('id_user', auth()->user()->id)->first();
        $data['datapresensi'] = Presensi::join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
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
                'presensi_jamkerja.total_jam',
                'presensi_jamkerja.lintashari',
                'presensi_izinabsen.keterangan as keterangan_izin',
                'presensi_izinsakit.keterangan as keterangan_izin_sakit',
                'presensi_izincuti.keterangan as keterangan_izin_cuti'
            )
            ->when(!empty($request->dari) && !empty($request->sampai), function ($q) use ($request) {
                $q->whereBetween('presensi.tanggal', [$request->dari, $request->sampai]);
            })
            ->orderBy('presensi.tanggal', 'desc')
            ->paginate(10)
            ->withQueryString();
            
        $data['namasettings'] = Pengaturanumum::getSetting();
        return view('presensi.histori', $data);
    }


    public function updatefrommachine(Request $request, $pin, $status_scan)
    {
        $pin = Crypt::decrypt($pin);
        $scan = $request->scan_date;

        $karyawan       = Karyawan::where('pin', $pin)->first();

        if ($karyawan == null) {
            return Redirect::back()->with(messageError('Karyawan Tidak Ditemukan'));
            $nik = "";
        } else {
            $nik = $karyawan->nik;
        }

        // Ambil timezone dari cabang
        $cabang = Cabang::where('kode_cabang', $karyawan->kode_cabang)->first();
        $generalsetting = Pengaturanumum::where('id', 1)->first();
        $timezone_cabang = $cabang->timezone ?? $generalsetting->timezone ?? config('app.timezone');

        // Konversi waktu scan ke timezone cabang
        $carbon_scan = Carbon::parse($scan)->setTimezone($timezone_cabang);
        $tanggal_sekarang = $carbon_scan->format('Y-m-d');
        $jam_sekarang = $carbon_scan->format('H:i');
        $tanggal_kemarin = $carbon_scan->copy()->subDay()->format('Y-m-d');
        $tanggal_besok = $carbon_scan->copy()->addDay()->format('Y-m-d');

        //Cek Presensi Kemarin
        $presensi_kemarin = Presensi::where('nik', $karyawan->nik)
            ->join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->where('nik', $karyawan->nik)
            ->where('tanggal', $tanggal_kemarin)->first();

        $lintas_hari = $presensi_kemarin ? $presensi_kemarin->lintashari : 0;

        //Jika Presensi Kemarin Status Lintas Hari nya 1 Makan Tanggal Presensi Sekarang adalah Tanggal Kemarin
        $tanggal_presensi = $lintas_hari == 1 ? $tanggal_kemarin : $tanggal_sekarang;
        $tanggal_pulang = $lintas_hari == 1 ? $tanggal_besok : $tanggal_sekarang;


        $namahari = getnamaHari(date('D', strtotime($tanggal_presensi)));
        $kode_jk = $karyawan->kode_jam_kerja ?: 'JK01';
        $jamkerja = Jamkerja::where('kode_jam_kerja', $kode_jk)->first();

        //Cek Presensi
        $presensi = Presensi::where('nik', $karyawan->nik)->where('tanggal', $tanggal_presensi)->first();

        //Cek Jika Laporan Sudah Dikunci
        if ($presensi != null && $presensi->status_potongan !== null) {
             return Redirect::back()->with(messageError('Data Presensi Sudah Dikunci'));
        }

        if ($presensi != null && $presensi->status != 'h') {
            return Redirect::back()->with(messageError('Sudah Melakukan Presesni'));
        } else if ($jamkerja == null) {
            return Redirect::back()->with(messageError('Tidak Memiliki Jadwal'));
        }

        $kode_jam_kerja = $jamkerja->kode_jam_kerja;
        $jam_kerja = Jamkerja::where('kode_jam_kerja', $kode_jam_kerja)->first();

        $jam_presensi = $tanggal_sekarang . " " . $jam_sekarang;

        $jam_masuk = $tanggal_presensi . " " . date('H:i', strtotime($jam_kerja->jam_masuk));

        $presensi_hariini = Presensi::where('nik', $karyawan->nik)
            ->where('tanggal', $tanggal_presensi)
            ->first();

        if (in_array($status_scan, [0, 2, 4, 6, 8])) {
            if ($presensi_hariini && $presensi_hariini->jam_in != null) {
                return Redirect::back()->with(messageError('Sudah Melakukan Presensi Masuk'));
            } else {
                try {
                    if ($presensi_hariini != null) {
                        Presensi::where('id', $presensi_hariini->id)->update([
                            'jam_in' => $jam_presensi,
                        ]);
                    } else {
                        Presensi::create([
                            'nik' => $karyawan->nik,
                            'tanggal' => $tanggal_presensi,
                            'jam_in' => $jam_presensi,
                            'jam_out' => null,
                            'lokasi_out' => null,
                            'foto_out' => null,
                            'kode_jam_kerja' => $kode_jam_kerja,
                            'status' => 'h'
                        ]);
                    }


                    return Redirect::back()->with(messageSuccess('Berhasil Melakukan Presensi Masuk'));
                } catch (\Exception $e) {
                    return Redirect::back()->with(messageError($e->getMessage()));
                }
            }
        } else {
            try {
                if ($presensi_hariini != null) {
                    Presensi::where('id', $presensi_hariini->id)->update([
                        'jam_out' => $jam_presensi,
                    ]);
                } else {
                    Presensi::create([
                        'nik' => $karyawan->nik,
                        'tanggal' => $tanggal_presensi,
                        'jam_in' => null,
                        'jam_out' => $jam_presensi,
                        'lokasi_in' => null,
                        'foto_in' => null,
                        'kode_jam_kerja' => $kode_jam_kerja,
                        'status' => 'h'
                    ]);
                }
                return Redirect::back()->with(messageSuccess('Berhasil Melakukan Presensi Pulang'));
            } catch (\Exception $e) {
                return Redirect::back()->with(messageError($e->getMessage()));
            }
        }
    }

    public function destroy($id)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $presensi = Presensi::join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->select('presensi.*', 'karyawan.kode_cabang', 'karyawan.kode_dept')
            ->where('presensi.id', $id)
            ->first();

        if ($presensi) {
            if (!$user->isSuperAdmin()) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();
                if (!in_array($presensi->kode_cabang, $userCabangs) || !in_array($presensi->kode_dept, $userDepartemens)) {
                    abort(403, 'Anda tidak memiliki akses untuk menghapus presensi cabang ini.');
                }
            }

            if ($presensi->status_potongan != null) {
                return Redirect::back()->with(['warning' => 'Data Presensi Sudah Dikunci, Hubungi Admin Untuk Membuka Kunci Laporan']);
            }
            try {
                $folderPath = "public/uploads/absensi/";
                if ($presensi->foto_in) Storage::delete($folderPath . $presensi->foto_in);
                if ($presensi->foto_out) Storage::delete($folderPath . $presensi->foto_out);
                Presensi::where('id', $id)->delete();
                return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
            } catch (\Exception $e) {
                return Redirect::back()->with(messageError($e->getMessage()));
            }
        } else {
            return Redirect::back()->with(messageError('Data Tidak Ditemukan'));
        }
    }

    /**
     * Trigger generate otomatis status Tanpa Keterangan / Alpha ('a') dari web admin
     */
    public function generateAutoAlpha(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->can('presensi.edit')) {
            abort(403, 'Anda tidak memiliki hak akses untuk memproses kehadiran.');
        }

        $tanggal = $request->input('tanggal') ?: date('Y-m-d');
        $result = AttendanceService::generateAutoAlpha($tanggal, false);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return Redirect::back()->with(messageSuccess($result['message']));
        }

        return Redirect::back()->with(messageError($result['message']));
    }

    /**
     * Download attendance photos as ZIP for testing or export
     */
    public function downloadZip(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->can('presensi.index')) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengunduh arsip foto presensi.');
        }

        $tanggal = $request->input('tanggal') ?: date('Y-m-d');
        $recordsQuery = Presensi::where('tanggal', $tanggal)
            ->where(function ($q) {
                $q->whereNotNull('foto_in')->where('foto_in', '!=', '')
                  ->orWhereNotNull('foto_out')->where('foto_out', '!=', '');
            });

        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $recordsQuery->where(function ($q) use ($userCabangs) {
                $q->whereIn('presensi.kode_cabang', $userCabangs)
                  ->orWhereHas('karyawan', function ($sub) use ($userCabangs) {
                      $sub->whereIn('karyawan.kode_cabang', $userCabangs);
                  });
            });
        }

        $records = $recordsQuery->get();

        if ($records->isEmpty()) {
            return Redirect::back()->with(messageError("Tidak ada foto presensi untuk tanggal {$tanggal}"));
        }

        $uploadDir = storage_path('app/public/uploads/absensi');
        $tmpZip = tempnam(sys_get_temp_dir(), 'presensi_zip_');
        $zip = new \ZipArchive();

        if ($zip->open($tmpZip, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return Redirect::back()->with(messageError('Gagal membuat file ZIP di server'));
        }

        $added = 0;
        foreach ($records as $r) {
            if (!empty($r->foto_in) && file_exists($uploadDir . '/' . $r->foto_in)) {
                $zip->addFile($uploadDir . '/' . $r->foto_in, 'masuk/' . $r->foto_in);
                $added++;
            }
            if (!empty($r->foto_out) && file_exists($uploadDir . '/' . $r->foto_out)) {
                $zip->addFile($uploadDir . '/' . $r->foto_out, 'pulang/' . $r->foto_out);
                $added++;
            }
        }

        $zip->close();

        if ($added === 0) {
            @unlink($tmpZip);
            return Redirect::back()->with(messageError('Tidak ada file foto fisik yang ditemukan di folder penyimpanan'));
        }

        $filename = "foto_presensi_{$tanggal}.zip";
        return response()->download($tmpZip, $filename, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }
}
