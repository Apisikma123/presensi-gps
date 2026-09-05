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
                'total_jam'
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
            'total_jam'
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

        $karyawan = $query->paginate(10);
        $karyawan->appends(request()->all());
        $data['karyawan'] = $karyawan;
        $data['cabang'] = $user->getCabang();
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


        if ($kode_jam_kerja == null) {
            $kode_jk = $karyawan->kode_jam_kerja ?: 'JK01';
            $jamkerja = Jamkerja::where('kode_jam_kerja', $kode_jk)->first();
        } else {
            $jamkerja = Jamkerja::where('kode_jam_kerja', $kode_jam_kerja)->first();
        }

        if ($presensi != null && $presensi->status != 'h') {
            return view('presensi.notif_izin');
        } else if ($jamkerja == null) {
            return view('presensi.notif_jamkerja');
        }

        $kode_cabang_array = $karyawan->kode_cabang_array ?? [];
        $data['cabang'] = Cabang::WhereIn('kode_cabang', $kode_cabang_array)
            ->orWhere('kode_cabang', $karyawan->kode_cabang)
            ->get();

        $data['hariini'] = $hariini;
        $data['jam_kerja'] = $jamkerja;
        $data['lokasi_kantor'] = $lokasi_kantor;
        $data['presensi'] = $presensi;
        $data['karyawan'] = $karyawan;
        $data['wajah'] = Facerecognition::where('nik', $karyawan->nik)->count();

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
            ], $result['code'] ?? 400);
        }

        return response()->json([
            'status' => true,
            'message' => $result['message'],
            'notifikasi' => $result['notifikasi'] ?? null,
            'is_terlambat' => $result['is_terlambat'] ?? false,
            'menit_terlambat' => $result['menit_terlambat'] ?? 0,
            'suara' => $result['suara'] ?? null,
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

        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            if (!in_array($karyawan->kode_cabang, $userCabangs) || !in_array($karyawan->kode_dept, $userDepartemens)) {
                return '<div class="alert alert-danger">Anda tidak memiliki akses ke data presensi cabang ini.</div>';
            }
        }

        $jam_kerja = Jamkerja::all();
        $presensi = Presensi::where('nik', $nik)->where('tanggal', $tanggal)->first();
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
        ]);

        $nik = Crypt::decrypt($request->nik);
        $karyawan = Karyawan::where('nik', $nik)->first();
        if (!$karyawan) {
            return Redirect::back()->with(messageError('Karyawan tidak ditemukan'));
        }

        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            if (!in_array($karyawan->kode_cabang, $userCabangs) || !in_array($karyawan->kode_dept, $userDepartemens)) {
                return Redirect::back()->with(messageError('Anda tidak memiliki akses ke data presensi cabang ini'));
            }
        }

        $tanggal = $request->tanggal;
        $presensi = Presensi::where('nik', $nik)->where('tanggal', $tanggal)->first();
        if ($presensi && $presensi->status_potongan !== null) {
            return redirect()->back()->with(['warning' => 'Data Presensi Sudah Dikunci, Hubungi Admin Untuk Membuka Kunci Laporan']);
        }

        $kode_jam_kerja = $request->kode_jam_kerja;
        $jam_in = $request->jam_in;
        $jam_out = $request->jam_out;
        $istirahat_out = $request->istirahat_out;
        $istirahat_in = $request->istirahat_in;
        $status = $request->status;

        try {
            $cekpresensi = Presensi::where('nik', $nik)->where('tanggal', $tanggal)->first();
            if (!empty($cekpresensi)) {
                Presensi::where('nik', $nik)->where('tanggal', $tanggal)->update([
                    'jam_in' => $jam_in,
                    'jam_out' => $jam_out,
                    'istirahat_out' => $istirahat_out,
                    'istirahat_in' => $istirahat_in,
                    'status' => $status,
                    'kode_jam_kerja' => $kode_jam_kerja,
                ]);
            } else {
                Presensi::create([
                    'nik' => $nik,
                    'tanggal' => $tanggal,
                    'jam_in' => $jam_in,
                    'jam_out' => $jam_out,
                    'istirahat_out' => $istirahat_out,
                    'istirahat_in' => $istirahat_in,
                    'kode_jam_kerja' => $kode_jam_kerja,
                    'status' => $status
                ]);
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
            if (!empty($userCabangs) && !in_array($presensi->kode_cabang, $userCabangs)) {
                abort(403, 'Anda tidak memiliki akses ke cabang presensi ini.');
            }
            if (!empty($userDepartemens) && !in_array($presensi->kode_dept, $userDepartemens)) {
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
            ->leftJoin('mesin_fingerprints', 'presensi.id_mesin', '=', 'mesin_fingerprints.id')
            ->select(
                'presensi.*',
                'presensi_jamkerja.nama_jam_kerja',
                'presensi_jamkerja.jam_masuk',
                'presensi_jamkerja.jam_pulang',
                'presensi_jamkerja.total_jam',
                'presensi_jamkerja.lintashari',
                'presensi_izinabsen.keterangan as keterangan_izin',
                'presensi_izinsakit.keterangan as keterangan_izin_sakit',
                'presensi_izincuti.keterangan as keterangan_izin_cuti',
                'mesin_fingerprints.nama_mesin'
            )
            ->when(!empty($request->dari) && !empty($request->sampai), function ($q) use ($request) {
                $q->whereBetween('presensi.tanggal', [$request->dari, $request->sampai]);
            })
            ->orderBy('presensi.tanggal', 'desc')
            ->limit(30)
            ->get();
            
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
}
