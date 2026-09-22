<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\Karyawan;
use App\Models\Userkaryawan;
use App\Models\Presensi;
use App\Models\Jamkerja;
use App\Models\Pengaturanumum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PresensiController extends Controller
{
    /**
     * Check-In (Absen Masuk)
     */
    public function masuk(Request $request)
    {
        return $this->storePresence($request, 1); // 1 = Masuk
    }

    /**
     * Check-Out (Absen Pulang)
     */
    public function pulang(Request $request)
    {
        return $this->storePresence($request, 2); // 2 = Pulang
    }

    /**
     * Store Presence entry (Common logic for check-in and check-out)
     */
    private function storePresence(Request $request, $status)
    {
        $user = $request->user();
        $userKaryawan = $user->userkaryawan;

        if (!$userKaryawan) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak terdaftar sebagai karyawan'
            ], 403);
        }

        $karyawan = Karyawan::where('nik', $userKaryawan->nik)->first();
        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Profil data karyawan tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'lokasi' => 'required|string', // "latitude,longitude"
            'kode_jam_kerja' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $generalsetting = Pengaturanumum::getSetting();
            $status_lock_location = $karyawan->lock_location;
            $lokasi = $request->input('lokasi');

            // Robust GPS Coordinate Validation
            $koordinat_user = explode(",", $lokasi);
            if (count($koordinat_user) !== 2 || !is_numeric(trim($koordinat_user[0])) || !is_numeric(trim($koordinat_user[1]))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format koordinat lokasi GPS tidak valid.'
                ], 400);
            }

            $latitude_user = (float) trim($koordinat_user[0]);
            $longitude_user = (float) trim($koordinat_user[1]);
            if ($latitude_user < -90 || $latitude_user > 90 || $longitude_user < -180 || $longitude_user > 180) {
                return response()->json([
                    'success' => false,
                    'message' => 'Titik koordinat GPS berada di luar rentang koordinat bumi yang valid.'
                ], 400);
            }

            // Enforce locked shift schedule if employee lock_jam_kerja is active
            if ($karyawan->lock_jam_kerja == 1 && !empty($karyawan->kode_jam_kerja)) {
                $kode_jam_kerja = $karyawan->kode_jam_kerja;
            } else {
                $kode_jam_kerja = $request->input('kode_jam_kerja');
            }

            $cabang = Cabang::getByCode($karyawan->kode_cabang);
            if (!$cabang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data cabang lokasi kantor tidak ditemukan'
                ], 404);
            }

            $lokasi_kantor = $cabang->lokasi_cabang; // "latitude,longitude"
            $timezone_cabang = $cabang->timezone ?? $generalsetting->timezone ?? config('app.timezone');

            $carbon_now = Carbon::now($timezone_cabang);
            $tanggal_sekarang = $carbon_now->format('Y-m-d');
            $jam_sekarang = $carbon_now->format('H:i');
            $tanggal_kemarin = $carbon_now->copy()->subDay()->format('Y-m-d');
            $tanggal_besok = $carbon_now->copy()->addDay()->format('Y-m-d');

            // Cek Presensi Kemarin untuk lintas hari
            $presensi_kemarin = Presensi::where('nik', $karyawan->nik)
                ->join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->where('presensi.nik', $karyawan->nik)
                ->where('presensi.tanggal', $tanggal_kemarin)
                ->first();

            $batas_presensi_lintashari = ($presensi_kemarin && $presensi_kemarin->batas_presensi_pulang)
                ? $presensi_kemarin->batas_presensi_pulang
                : ($generalsetting->batas_presensi_lintashari ?? '08:00');

            $jam_kerja = Jamkerja::getByCode($kode_jam_kerja);
            if (!$jam_kerja) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data jam kerja tidak ditemukan'
                ], 404);
            }

            // Penentuan tanggal presensi
            $tanggal_presensi = $tanggal_sekarang;
            $jam_kerja_pulang = $jam_kerja->jam_pulang;
            $tanggal_pulang = $jam_kerja->lintashari == 1 ? $tanggal_besok : $tanggal_sekarang;

            if ($presensi_kemarin && $presensi_kemarin->lintashari == 1 && $presensi_kemarin->jam_out == null) {
                if ($jam_sekarang < $batas_presensi_lintashari) {
                    $tanggal_presensi = $tanggal_kemarin;
                    $tanggal_pulang = $tanggal_sekarang;
                    $jam_kerja_pulang = $presensi_kemarin->jam_pulang;
                }
            }

            // Calculate distance
            $koordinat_kantor = explode(",", $lokasi_kantor);
            if (count($koordinat_kantor) !== 2 || !is_numeric(trim($koordinat_kantor[0])) || !is_numeric(trim($koordinat_kantor[1]))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Titik lokasi kantor cabang belum diatur dengan benar oleh administrator.'
                ], 400);
            }
            $latitude_kantor = (float) trim($koordinat_kantor[0]);
            $longitude_kantor = (float) trim($koordinat_kantor[1]);

            $jarak = hitungjarak($latitude_kantor, $longitude_kantor, $latitude_user, $longitude_user);
            $radius = round($jarak["meters"]);

            // Geofence lock check
            if ($status_lock_location == 1 && $radius > $cabang->radius_cabang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda berada di luar radius kantor. Jarak Anda ' . $radius . ' meter dari kantor.'
                ], 400);
            }

            // Limits checking parameters
            $batas_jam_absen = $generalsetting->batas_jam_absen * 60;
            $batas_jam_absen_pulang = $generalsetting->batas_jam_absen_pulang * 60;

            $jam_masuk_string = $tanggal_presensi . " " . $jam_kerja->jam_masuk;
            $jam_masuk_carbon = Carbon::parse($jam_masuk_string, $timezone_cabang);
            $jam_mulai_masuk_carbon = $jam_masuk_carbon->copy()->subMinutes($batas_jam_absen);
            $jam_akhir_masuk_carbon = $jam_masuk_carbon->copy()->addMinutes($batas_jam_absen);

            $jam_pulang_string = $tanggal_pulang . " " . $jam_kerja_pulang;
            $jam_pulang_carbon = Carbon::parse($jam_pulang_string, $timezone_cabang);
            $jam_mulai_pulang_carbon = $jam_pulang_carbon->copy()->subMinutes($batas_jam_absen_pulang);

            // === FAST PRE-CHECK (Zero I/O, Zero Python overhead on duplicate/invalid attempts) ===
            $presensi_hariini = Presensi::where('nik', $karyawan->nik)
                ->where('tanggal', $tanggal_presensi)
                ->first();

            if ($status == 1) {
                // Check-In Pre-Check
                if ($presensi_hariini && $presensi_hariini->jam_in != null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda sudah absen masuk hari ini'
                    ], 400);
                }

                if ($carbon_now->lt($jam_mulai_masuk_carbon) && $generalsetting->batasi_absen == 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Maaf, belum waktunya absen masuk. Dimulai pukul ' . $jam_mulai_masuk_carbon->format('H:i')
                    ], 400);
                }

                if ($carbon_now->gt($jam_akhir_masuk_carbon) && $generalsetting->batasi_absen == 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Maaf, batas waktu absen masuk sudah habis'
                    ], 400);
                }
            } else {
                // Check-Out Pre-Check
                if ($presensi_hariini && $presensi_hariini->jam_out != null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda sudah absen pulang hari ini'
                    ], 400);
                }

                if ($carbon_now->lt($jam_mulai_pulang_carbon) && $generalsetting->batasi_absen == 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Maaf, belum waktunya absen pulang. Dimulai pukul ' . $jam_mulai_pulang_carbon->format('H:i')
                    ], 400);
                }
            }

            $in_out = $status == 1 ? "in" : "out";
            $jam_presensi = $tanggal_sekarang . " " . $carbon_now->format('H:i:s');
            $formatName = $karyawan->nik . "-" . $tanggal_presensi . "-" . $in_out;

            // Save uploaded selfie with WebP optimization & downscaling
            $imageInput = $request->hasFile('image') ? $request->file('image') : $request->input('image');
            $fileName = \App\Helpers\ImageOptimizer::saveAsWebp($imageInput, 'public/uploads/absensi', $formatName, 80, 800);

            // Face Recognition verification (pure PHP biometrics, zero python process spawn)
            if (isset($generalsetting->face_recognition) && $generalsetting->face_recognition == 1) {
                $masterFace = \App\Models\Facerecognition::where('nik', $karyawan->nik)->first();
                if (!$masterFace) {
                    Storage::delete("public/uploads/absensi/" . $fileName);
                    return response()->json([
                        'success' => false,
                        'message' => 'Data biometrik wajah Anda belum terdaftar di sistem. Silakan daftarkan wajah terlebih dahulu.'
                    ], 400);
                }

                if ($request->filled('face_descriptor') && !empty($masterFace->descriptor)) {
                    $clientDesc = is_string($request->input('face_descriptor')) ? json_decode($request->input('face_descriptor'), true) : $request->input('face_descriptor');
                    $masterDesc = is_string($masterFace->descriptor) ? json_decode($masterFace->descriptor, true) : $masterFace->descriptor;

                    if (is_array($clientDesc) && is_array($masterDesc) && count($clientDesc) === 128 && count($masterDesc) === 128) {
                        $distance = 0.0;
                        for ($i = 0; $i < 128; $i++) {
                            $diff = (float)$clientDesc[$i] - (float)$masterDesc[$i];
                            $distance += $diff * $diff;
                        }
                        $distance = sqrt($distance);

                        if ($distance > 0.50) {
                            Storage::delete("public/uploads/absensi/" . $fileName);
                            return response()->json([
                                'success' => false,
                                'message' => 'Verifikasi wajah gagal. Wajah Anda tidak cocok dengan data master terdaftar.'
                            ], 400);
                        }
                    }
                }
            }

            // === ATOMIC PERSISTENCE WITH ROW-LEVEL LOCK ===
            DB::transaction(function () use ($karyawan, $tanggal_presensi, $jam_presensi, $lokasi, $fileName, $kode_jam_kerja, $status, &$presensi_hariini) {
                $locked = Presensi::where('nik', $karyawan->nik)
                    ->where('tanggal', $tanggal_presensi)
                    ->lockForUpdate()
                    ->first();

                if ($status == 1) {
                    if ($locked && $locked->jam_in != null) {
                        throw new \Exception('Anda sudah absen masuk hari ini');
                    }

                    if ($locked != null) {
                        $locked->update([
                            'jam_in' => $jam_presensi,
                            'lokasi_in' => $lokasi,
                            'foto_in' => $fileName
                        ]);
                        $presensi_hariini = $locked;
                    } else {
                        $presensi_hariini = Presensi::create([
                            'nik' => $karyawan->nik,
                            'tanggal' => $tanggal_presensi,
                            'jam_in' => $jam_presensi,
                            'jam_out' => null,
                            'lokasi_in' => $lokasi,
                            'lokasi_out' => null,
                            'foto_in' => $fileName,
                            'foto_out' => null,
                            'kode_jam_kerja' => $kode_jam_kerja,
                            'status' => 'h'
                        ]);
                    }
                } else {
                    if ($locked && $locked->jam_out != null) {
                        throw new \Exception('Anda sudah absen pulang hari ini');
                    }

                    if ($locked != null) {
                        $locked->update([
                            'jam_out' => $jam_presensi,
                            'lokasi_out' => $lokasi,
                            'foto_out' => $fileName
                        ]);
                        $presensi_hariini = $locked;
                    } else {
                        $presensi_hariini = Presensi::create([
                            'nik' => $karyawan->nik,
                            'tanggal' => $tanggal_presensi,
                            'jam_in' => null,
                            'jam_out' => $jam_presensi,
                            'lokasi_in' => null,
                            'lokasi_out' => $lokasi,
                            'foto_in' => null,
                            'foto_out' => $fileName,
                            'kode_jam_kerja' => $kode_jam_kerja,
                            'status' => 'h'
                        ]);
                    }
                }
            });

            if ($status == 1) {
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil absen masuk',
                    'data' => [
                        'jam_in' => $carbon_now->format('H:i'),
                        'foto_in' => asset('storage/uploads/absensi/' . $fileName)
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil absen pulang',
                    'data' => [
                        'jam_out' => $carbon_now->format('H:i'),
                        'foto_out' => asset('storage/uploads/absensi/' . $fileName)
                    ]
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get monthly presence history list
     */
    public function riwayat(Request $request)
    {
        $user = $request->user();
        $userKaryawan = $user->userkaryawan;

        if (!$userKaryawan) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak terdaftar sebagai karyawan'
            ], 403);
        }

        $bulan = $request->query('bulan', Carbon::now()->month);
        $tahun = $request->query('tahun', Carbon::now()->year);

        $riwayat = Presensi::join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->where('presensi.nik', $userKaryawan->nik)
            ->whereRaw('MONTH(presensi.tanggal) = ?', [$bulan])
            ->whereRaw('YEAR(presensi.tanggal) = ?', [$tahun])
            ->leftJoin('presensi_izinabsen_approve', 'presensi.id', '=', 'presensi_izinabsen_approve.id_presensi')
            ->leftJoin('presensi_izinabsen', 'presensi_izinabsen_approve.kode_izin', '=', 'presensi_izinabsen.kode_izin')
            ->leftJoin('presensi_izinsakit_approve', 'presensi.id', '=', 'presensi_izinsakit_approve.id_presensi')
            ->leftJoin('presensi_izinsakit', 'presensi_izinsakit_approve.kode_izin_sakit', '=', 'presensi_izinsakit.kode_izin_sakit')
            ->leftJoin('presensi_izincuti_approve', 'presensi.id', '=', 'presensi_izincuti_approve.id_presensi')
            ->leftJoin('presensi_izincuti', 'presensi_izincuti_approve.kode_izin_cuti', '=', 'presensi_izincuti.kode_izin_cuti')
            ->select(
                'presensi.id',
                'presensi.tanggal',
                'presensi.jam_in',
                'presensi.jam_out',
                'presensi.status',
                'presensi_jamkerja.nama_jam_kerja',
                'presensi_jamkerja.jam_masuk',
                'presensi_jamkerja.jam_pulang',
                'presensi_izinabsen.keterangan as keterangan_izin',
                'presensi_izinsakit.keterangan as keterangan_izin_sakit',
                'presensi_izincuti.keterangan as keterangan_izin_cuti'
            )
            ->orderBy('presensi.tanggal', 'desc')
            ->get();

        $riwayatFormatted = $riwayat->map(function ($item) use ($userKaryawan) {
            $keterangan = null;
            if ($item->status == 'i') $keterangan = $item->keterangan_izin;
            if ($item->status == 's') $keterangan = $item->keterangan_izin_sakit;
            if ($item->status == 'c') $keterangan = $item->keterangan_izin_cuti;

            return [
                'id' => $item->id,
                'tanggal' => $item->tanggal,
                'jam_in' => $item->jam_in ? date('H:i', strtotime($item->jam_in)) : null,
                'jam_out' => $item->jam_out ? date('H:i', strtotime($item->jam_out)) : null,
                'status' => $item->status,
                'nama_jam_kerja' => $item->nama_jam_kerja,
                'jam_masuk' => date('H:i', strtotime($item->jam_masuk)),
                'jam_pulang' => date('H:i', strtotime($item->jam_pulang)),
                'keterangan' => $keterangan,
                'foto_in' => $item->jam_in ? asset('storage/uploads/absensi/' . $userKaryawan->nik . '-' . $item->tanggal . '-in.png') : null,
                'foto_out' => $item->jam_out ? asset('storage/uploads/absensi/' . $userKaryawan->nik . '-' . $item->tanggal . '-out.png') : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $riwayatFormatted
        ]);
    }
}
