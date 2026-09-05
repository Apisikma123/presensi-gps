<?php

namespace App\Services;

use App\Models\Cabang;
use App\Models\Facerecognition;
use App\Models\Jamkerja;
use App\Models\Karyawan;
use App\Models\Pengaturanumum;
use App\Models\Presensi;
use App\Models\PresensiDispensasi;
use App\Models\User;
use App\Models\Userkaryawan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AttendanceService
{
    /**
     * Standard status constants
     */
    public const STATUS_HADIR = 'HADIR';
    public const STATUS_TELAT = 'TELAT';
    public const STATUS_IZIN = 'IZIN';
    public const STATUS_SAKIT = 'SAKIT';
    public const STATUS_CUTI = 'CUTI';
    public const STATUS_TIDAK_HADIR = 'TIDAK HADIR';

    /**
     * Map database char code to readable label
     */
    public static function getStatusLabel(?string $statusCode, bool $isLate = false): string
    {
        return match ($statusCode) {
            'h' => $isLate ? self::STATUS_TELAT : self::STATUS_HADIR,
            'i' => self::STATUS_IZIN,
            's' => self::STATUS_SAKIT,
            'c' => self::STATUS_CUTI,
            'a' => self::STATUS_TIDAK_HADIR,
            default => self::STATUS_TIDAK_HADIR,
        };
    }

    /**
     * Get configurable monthly leave quota
     */
    public static function getMonthlyLeaveQuota(): int
    {
        $setting = \App\Models\Pengaturanumum::getSetting();
        return (int)($setting->monthly_leave_quota ?? 3);
    }

    /**
     * Check if employee has monthly leave quota remaining
     */
    public static function checkMonthlyLeaveQuota(string $nik, ?string $tanggal = null): array
    {
        $date = $tanggal ? \Carbon\Carbon::parse($tanggal) : now();
        $quota = self::getMonthlyLeaveQuota();
        
        $used = DB::table('presensi_izincuti')
            ->where('nik', $nik)
            ->where('status', 1)
            ->whereYear('dari', $date->year)
            ->whereMonth('dari', $date->month)
            ->count();

        $remaining = max(0, $quota - $used);

        return [
            'allowed' => $used < $quota,
            'quota' => $quota,
            'used' => $used,
            'remaining' => $remaining
        ];
    }

    /**
     * Centralized Attendance Status Source of Truth
     */
    public static function evaluateAttendanceStatus(string $nik, string $tanggal, ?string $jamIn, ?Jamkerja $jamKerja = null): array
    {
        // 1. Check Approved Izin
        $izin = DB::table('presensi_izinabsen')->where('nik', $nik)->where('dari', '<=', $tanggal)->where('sampai', '>=', $tanggal)->where('status', 1)->first();
        if ($izin) {
            return ['status' => self::STATUS_IZIN, 'is_late' => false, 'keterangan' => 'Izin: ' . ($izin->keterangan ?? '-')];
        }

        // 2. Check Approved Sakit
        $sakit = DB::table('presensi_izinsakit')->where('nik', $nik)->where('dari', '<=', $tanggal)->where('sampai', '>=', $tanggal)->where('status', 1)->first();
        if ($sakit) {
            return ['status' => self::STATUS_SAKIT, 'is_late' => false, 'keterangan' => 'Sakit: ' . ($sakit->keterangan ?? '-')];
        }

        // 3. Check Approved Cuti
        $cuti = DB::table('presensi_izincuti')->where('nik', $nik)->where('dari', '<=', $tanggal)->where('sampai', '>=', $tanggal)->where('status', 1)->first();
        if ($cuti) {
            return ['status' => self::STATUS_CUTI, 'is_late' => false, 'keterangan' => 'Cuti: ' . ($cuti->keterangan ?? '-')];
        }

        if (empty($jamIn)) {
            return ['status' => self::STATUS_TIDAK_HADIR, 'is_late' => false, 'keterangan' => 'Tidak Hadir'];
        }

        if (!$jamKerja) {
            $jamKerja = Jamkerja::where('kode_jam_kerja', 'JK01')->first() ?? (object)[
                'jam_masuk' => '07:00:00',
                'batas_toleransi' => '07:05:00',
                'toleransi_menit' => 5
            ];
        }

        $jamInCarbon = Carbon::parse($jamIn);
        $batasToleransi = $jamKerja->batas_toleransi ?? '07:05:00';
        $toleransiCarbon = Carbon::parse($tanggal . ' ' . $batasToleransi);

        // Jika jam masuk <= batas toleransi (misal 07:05), maka HADIR
        if ($jamInCarbon->lte($toleransiCarbon)) {
            return [
                'status' => self::STATUS_HADIR,
                'is_late' => false,
                'is_dispensasi' => false,
                'keterangan' => 'Hadir Tepat Waktu'
            ];
        }

        // Jika lewat batas toleransi, cek apakah ada DISPENSASI APPROVED
        $dispensasi = DB::table('presensi_dispensasi')
            ->where('nik', $nik)
            ->where('tanggal', $tanggal)
            ->where('status', 'APPROVED')
            ->first();

        if ($dispensasi) {
            $batasDispensasiCarbon = Carbon::parse($tanggal . ' ' . $dispensasi->batas_dispensasi);
            if ($jamInCarbon->lte($batasDispensasiCarbon)) {
                return [
                    'status' => self::STATUS_HADIR,
                    'is_late' => false,
                    'is_dispensasi' => true,
                    'dispensasi_id' => $dispensasi->id,
                    'keterangan' => 'DISPENSASI'
                ];
            }
        }

        $jamMasukCarbon = Carbon::parse($tanggal . ' ' . ($jamKerja->jam_masuk ?? '07:00:00'));
        $menitTerlambat = $jamInCarbon->diffInMinutes($jamMasukCarbon);

        return [
            'status' => self::STATUS_TELAT,
            'is_late' => true,
            'is_dispensasi' => false,
            'menit_terlambat' => $menitTerlambat,
            'keterangan' => 'Terlambat ' . $menitTerlambat . ' Menit'
        ];
    }

    /**
     * Handle Clock-In (Absen Masuk)
     *
     * @param User $user
     * @param array $data ['lokasi', 'kode_jam_kerja', 'image', 'is_mock']
     * @return array
     */
    public function clockIn(User $user, array $data): array
    {
        $context = $this->resolveAttendanceContext($user, $data, 1);
        if (!$context['success']) {
            return $context;
        }

        $karyawan = $context['karyawan'];
        $generalsetting = $context['generalsetting'];
        $jamKerja = $context['jam_kerja'];
        $tanggalPresensi = $context['tanggal_presensi'];
        $jamPresensi = $context['jam_presensi'];
        $jamPresensiCarbon = $context['jam_presensi_carbon'];
        $timezoneCabang = $context['timezone_cabang'];
        $lokasi = $data['lokasi'];

        // Cek Batasan Jam Mulai & Akhir Absen Masuk jika diatur
        $batasJamAbsen = ($generalsetting->batas_jam_absen ?? 0) * 60;
        $jamMasukCarbon = Carbon::parse($tanggalPresensi . ' ' . $jamKerja->jam_masuk, $timezoneCabang);
        $jamMulaiMasukCarbon = $jamMasukCarbon->copy()->subMinutes($batasJamAbsen);
        $jamAkhirMasukCarbon = $jamMasukCarbon->copy()->addMinutes($batasJamAbsen);

        if (($generalsetting->batasi_absen ?? 0) == 1) {
            if ($jamPresensiCarbon->lt($jamMulaiMasukCarbon)) {
                return [
                    'success' => false,
                    'code' => 400,
                    'message' => 'Maaf belum waktunya absen masuk. Waktu absen dimulai pukul ' . formatIndo3($jamMulaiMasukCarbon->format('Y-m-d H:i')),
                    'notifikasi' => 'notifikasi_mulaiabsen',
                    'suara' => 'Maaf, belum waktunya untuk melakukan presensi masuk.'
                ];
            }
            if ($jamPresensiCarbon->gt($jamAkhirMasukCarbon)) {
                return [
                    'success' => false,
                    'code' => 400,
                    'message' => 'Maaf waktu absen masuk sudah habis.',
                    'notifikasi' => 'notifikasi_akhirabsen',
                    'suara' => 'Maaf, waktu absen masuk sudah habis.'
                ];
            }
        }

        // Simpan File Foto Absensi
        $formatName = $karyawan->nik . '-' . $tanggalPresensi . '-in';
        $fileName = \App\Helpers\ImageOptimizer::saveAsWebp(
            $data['image'],
            'public/uploads/absensi',
            $formatName,
            80
        );

        // Atomic Database Lock & Write
        $presensiRecord = null;
        try {
            DB::transaction(function () use (
                $karyawan,
                $tanggalPresensi,
                $jamPresensi,
                $lokasi,
                $fileName,
                $jamKerja,
                &$presensiRecord
            ) {
                $locked = Presensi::where('nik', $karyawan->nik)
                    ->where('tanggal', $tanggalPresensi)
                    ->lockForUpdate()
                    ->first();

                if ($locked) {
                    if ($locked->jam_in != null) {
                        throw new \Exception('ALREADY_CLOCKED_IN');
                    }
                    $locked->update([
                        'jam_in' => $jamPresensi,
                        'lokasi_in' => $lokasi,
                        'foto_in' => $fileName,
                        'status' => 'h'
                    ]);
                    $presensiRecord = $locked;
                } else {
                    $presensiRecord = Presensi::create([
                        'nik' => $karyawan->nik,
                        'tanggal' => $tanggalPresensi,
                        'jam_in' => $jamPresensi,
                        'jam_out' => null,
                        'lokasi_in' => $lokasi,
                        'lokasi_out' => null,
                        'foto_in' => $fileName,
                        'foto_out' => null,
                        'kode_jam_kerja' => $jamKerja->kode_jam_kerja,
                        'status' => 'h'
                    ]);
                }
            });
        } catch (\Exception $e) {
            if ($e->getMessage() === 'ALREADY_CLOCKED_IN') {
                return [
                    'success' => false,
                    'code' => 400,
                    'message' => 'Anda sudah melakukan presensi masuk hari ini.',
                    'notifikasi' => 'notifikasi_sudahabsen',
                    'suara' => 'Anda sudah melakukan presensi masuk hari ini.'
                ];
            }
            return [
                'success' => false,
                'code' => 400,
                'message' => 'Gagal memproses presensi masuk: ' . $e->getMessage()
            ];
        }

        // Tentukan Keterlambatan sesuai Shift Tolerance (misal 07:05) & Dispensasi
        $batasToleransi = $jamKerja->batas_toleransi ?? '07:05:00';
        $batasToleransiCarbon = Carbon::parse($tanggalPresensi . ' ' . $batasToleransi, $timezoneCabang);
        
        $isTerlambat = false;
        $isDispensasi = false;
        $menitTerlambat = 0;
        $dispensasiId = null;

        if ($jamPresensiCarbon->gt($batasToleransiCarbon)) {
            // Cek dispensasi approved
            $dispensasi = PresensiDispensasi::where('nik', $karyawan->nik)
                ->where('tanggal', $tanggalPresensi)
                ->where('status', 'APPROVED')
                ->first();

            if ($dispensasi) {
                $batasDispensasiCarbon = Carbon::parse($tanggalPresensi . ' ' . $dispensasi->batas_dispensasi, $timezoneCabang);
                if ($jamPresensiCarbon->lte($batasDispensasiCarbon)) {
                    $isDispensasi = true;
                    $dispensasiId = $dispensasi->id;
                    $presensiRecord->update([
                        'is_dispensasi' => 1,
                        'dispensasi_id' => $dispensasiId,
                        'keterangan' => 'DISPENSASI'
                    ]);
                } else {
                    $isTerlambat = true;
                    $menitTerlambat = $jamPresensiCarbon->diffInMinutes($jamMasukCarbon);
                }
            } else {
                $isTerlambat = true;
                $menitTerlambat = $jamPresensiCarbon->diffInMinutes($jamMasukCarbon);
            }
        }

        $statusKehadiran = $isTerlambat ? self::STATUS_TELAT : self::STATUS_HADIR;

        $pesanSukses = $isDispensasi
            ? 'Berhasil Absen Masuk dengan DISPENSASI.'
            : ($isTerlambat
                ? 'Berhasil Absen Masuk. Anda Terlambat ' . $menitTerlambat . ' Menit.'
                : 'Berhasil Absen Masuk. Terima Kasih, Anda Tepat Waktu.');

        $suaraSukses = $isDispensasi
            ? 'Presensi masuk berhasil dengan dispensasi keterlambatan.'
            : ($isTerlambat
                ? 'Presensi masuk berhasil. Anda terlambat ' . $menitTerlambat . ' menit.'
                : 'Terima kasih, presensi masuk berhasil. Anda hadir tepat waktu.');

        return [
            'success' => true,
            'code' => 200,
            'message' => $pesanSukses,
            'notifikasi' => 'notifikasi_absenmasuk',
            'is_terlambat' => $isTerlambat,
            'is_dispensasi' => $isDispensasi,
            'menit_terlambat' => $menitTerlambat,
            'status_kehadiran' => $statusKehadiran,
            'suara' => $suaraSukses,
            'data' => $presensiRecord
        ];
    }

    /**
     * Handle Clock-Out (Absen Pulang)
     *
     * @param User $user
     * @param array $data ['lokasi', 'kode_jam_kerja', 'image', 'is_mock']
     * @return array
     */
    public function clockOut(User $user, array $data): array
    {
        $context = $this->resolveAttendanceContext($user, $data, 2);
        if (!$context['success']) {
            return $context;
        }

        $karyawan = $context['karyawan'];
        $generalsetting = $context['generalsetting'];
        $jamKerja = $context['jam_kerja'];
        $tanggalPresensi = $context['tanggal_presensi'];
        $tanggalPulang = $context['tanggal_pulang'];
        $jamKerjaPulang = $context['jam_kerja_pulang'];
        $jamPresensi = $context['jam_presensi'];
        $jamPresensiCarbon = $context['jam_presensi_carbon'];
        $timezoneCabang = $context['timezone_cabang'];
        $lokasi = $data['lokasi'];

        // Cek batas waktu paling awal boleh absen pulang
        $batasJamAbsenPulang = ($generalsetting->batas_jam_absen_pulang ?? 0) * 60;
        $jamPulangCarbon = Carbon::parse($tanggalPulang . ' ' . $jamKerjaPulang, $timezoneCabang);
        $jamMulaiPulangCarbon = $jamPulangCarbon->copy()->subMinutes($batasJamAbsenPulang);

        $waktuBolehPulang = (($generalsetting->batasi_absen ?? 0) == 1 && $batasJamAbsenPulang > 0)
            ? $jamMulaiPulangCarbon
            : $jamPulangCarbon;

        if ($jamPresensiCarbon->lt($waktuBolehPulang)) {
            $jamDisplay = (($generalsetting->batasi_absen ?? 0) == 1 && $batasJamAbsenPulang > 0)
                ? formatIndo3($jamMulaiPulangCarbon->format('Y-m-d H:i'))
                : date('H:i', strtotime($jamKerjaPulang));

            return [
                'success' => false,
                'code' => 400,
                'message' => 'Maaf belum waktunya absen pulang. Jam pulang shift Anda pukul ' . $jamDisplay,
                'notifikasi' => 'notifikasi_mulaiabsen',
                'suara' => 'Maaf, belum waktunya untuk presensi pulang.'
            ];
        }

        // Simpan File Foto Absensi Pulang
        $formatName = $karyawan->nik . '-' . $tanggalPresensi . '-out';
        $fileName = \App\Helpers\ImageOptimizer::saveAsWebp(
            $data['image'],
            'public/uploads/absensi',
            $formatName,
            80
        );

        // Atomic Database Lock & Write
        $presensiRecord = null;
        try {
            DB::transaction(function () use (
                $karyawan,
                $tanggalPresensi,
                $jamPresensi,
                $lokasi,
                $fileName,
                &$presensiRecord
            ) {
                $locked = Presensi::where('nik', $karyawan->nik)
                    ->where('tanggal', $tanggalPresensi)
                    ->lockForUpdate()
                    ->first();

                // Validasi 1: Karyawan HARUS sudah clock-in
                if (!$locked || $locked->jam_in == null) {
                    throw new \Exception('NOT_CLOCKED_IN');
                }

                // Validasi 2: Karyawan BELUM boleh clock-out dua kali
                if ($locked->jam_out != null) {
                    throw new \Exception('ALREADY_CLOCKED_OUT');
                }

                $locked->update([
                    'jam_out' => $jamPresensi,
                    'lokasi_out' => $lokasi,
                    'foto_out' => $fileName
                ]);
                $presensiRecord = $locked;
            });
        } catch (\Exception $e) {
            if ($e->getMessage() === 'NOT_CLOCKED_IN') {
                return [
                    'success' => false,
                    'code' => 400,
                    'message' => 'Anda belum melakukan absen masuk. Silakan lakukan absen masuk terlebih dahulu.',
                    'notifikasi' => 'notifikasi_belumabsenmasuk',
                    'suara' => 'Maaf, Anda belum melakukan presensi masuk hari ini.'
                ];
            }
            if ($e->getMessage() === 'ALREADY_CLOCKED_OUT') {
                return [
                    'success' => false,
                    'code' => 400,
                    'message' => 'Anda sudah melakukan absen pulang hari ini.',
                    'notifikasi' => 'notifikasi_sudahabsen',
                    'suara' => 'Anda sudah melakukan presensi pulang hari ini.'
                ];
            }
            return [
                'success' => false,
                'code' => 400,
                'message' => 'Gagal memproses presensi pulang: ' . $e->getMessage()
            ];
        }

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Berhasil Absen Pulang. Hati-hati di jalan!',
            'notifikasi' => 'notifikasi_absenpulang',
            'suara' => 'Terima kasih, presensi pulang berhasil. Hati-hati di jalan!',
            'data' => $presensiRecord
        ];
    }

    /**
     * Common Context & Validation (Employee Active, Face Registration, Backend GPS Radius, Anti-Fake GPS)
     */
    protected function resolveAttendanceContext(User $user, array $data, int $flowStatus): array
    {
        $userkaryawan = $user->userkaryawan ?? Userkaryawan::where('id_user', $user->id)->first();
        if (!$userkaryawan) {
            return [
                'success' => false,
                'code' => 403,
                'message' => 'Akun Anda tidak terhubung dengan data karyawan.'
            ];
        }

        $karyawan = Karyawan::where('nik', $userkaryawan->nik)->first();
        if (!$karyawan) {
            return [
                'success' => false,
                'code' => 404,
                'message' => 'Data karyawan tidak ditemukan.'
            ];
        }

        // 1. Validasi Karyawan Aktif
        if ($karyawan->status_aktif_karyawan !== '1' && $karyawan->status_aktif_karyawan !== 1) {
            return [
                'success' => false,
                'code' => 403,
                'message' => 'Status karyawan Anda non-aktif. Tidak dapat melakukan absensi.',
                'notifikasi' => 'notifikasi_nonaktif',
                'suara' => 'Maaf, status akun karyawan Anda saat ini non-aktif.'
            ];
        }

        $generalsetting = Pengaturanumum::getSetting();

        // 2. Validasi Registrasi Wajah di Backend jika sistem mewajibkan Face Recognition
        if (($generalsetting->face_recognition ?? 0) == 1) {
            $hasFace = Facerecognition::where('nik', $karyawan->nik)->exists();
            if (!$hasFace) {
                return [
                    'success' => false,
                    'code' => 400,
                    'message' => 'Wajah Anda belum terdaftar di sistem. Silakan daftarkan wajah Anda terlebih dahulu.',
                    'notifikasi' => 'notifikasi_wajah_belum_terdaftar',
                    'suara' => 'Wajah belum terdaftar. Silakan daftarkan wajah Anda terlebih dahulu.'
                ];
            }
        }

        // 3. Validasi Keberadaan Foto Bukti Absen
        if (empty($data['image'])) {
            return [
                'success' => false,
                'code' => 400,
                'message' => 'Foto wajah absensi wajib disertakan.',
                'notifikasi' => 'notifikasi_foto_kosong'
            ];
        }

        // 4. Validasi Format GPS
        $lokasi = $data['lokasi'] ?? null;
        if (empty($lokasi) || !str_contains($lokasi, ',')) {
            return [
                'success' => false,
                'code' => 400,
                'message' => 'Koordinat lokasi GPS tidak valid atau tidak terbaca.',
                'notifikasi' => 'notifikasi_gps_invalid'
            ];
        }

        // 5. Anti-Fake GPS & Anomali
        if (!empty($data['is_mock']) && $data['is_mock'] == '1') {
            return [
                'success' => false,
                'code' => 400,
                'message' => 'Terdeteksi menggunakan aplikasi Fake GPS / Mock Location.',
                'notifikasi' => 'notifikasi_fakegps'
            ];
        }

        // Konfigurasi Cabang & Timezone
        $cabang = Cabang::getByCode($karyawan->kode_cabang);
        $timezoneCabang = $cabang->timezone ?? $generalsetting->timezone ?? config('app.timezone');

        // ponytail: strictly resolve office coordinates from authorized DB records, never trust raw client coordinates
        $allowedCabangs = Cabang::whereIn('kode_cabang', $karyawan->kode_cabang_array ?? [])
            ->orWhere('kode_cabang', $karyawan->kode_cabang)
            ->get();

        $matchedCabang = null;
        if (!empty($data['lokasi_cabang'])) {
            $inputLokasiCabang = trim($data['lokasi_cabang']);
            $matchedCabang = $allowedCabangs->first(function ($c) use ($inputLokasiCabang) {
                return $c->kode_cabang === $inputLokasiCabang || $c->lokasi_cabang === $inputLokasiCabang;
            });
        }

        $activeCabang = $matchedCabang ?: $cabang;
        $lokasiKantor = $activeCabang ? $activeCabang->lokasi_cabang : null;
        if ($activeCabang) {
            $cabang = $activeCabang;
            $timezoneCabang = $cabang->timezone ?? $timezoneCabang;
        }

        if (empty($lokasiKantor) || !str_contains($lokasiKantor, ',')) {
            return [
                'success' => false,
                'code' => 400,
                'message' => 'Titik lokasi kantor cabang belum diatur oleh administrator.'
            ];
        }

        $carbonNow = Carbon::now($timezoneCabang);
        $tanggalSekarang = $carbonNow->format('Y-m-d');
        $jamSekarang = $carbonNow->format('H:i');
        $tanggalKemarin = $carbonNow->copy()->subDay()->format('Y-m-d');
        $tanggalBesok = $carbonNow->copy()->addDay()->format('Y-m-d');

        // Shift Kerja
        $kodeJamKerja = $data['kode_jam_kerja'] ?? null;
        $jamKerja = $kodeJamKerja ? Jamkerja::getByCode($kodeJamKerja) : null;
        if (!$jamKerja) {
            return [
                'success' => false,
                'code' => 400,
                'message' => 'Jadwal jam kerja tidak valid.'
            ];
        }

        // Cek Presensi Kemarin untuk Shift Lintas Hari
        $presensiKemarin = Presensi::where('nik', $karyawan->nik)
            ->join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->where('presensi.tanggal', $tanggalKemarin)
            ->first();

        $batasLintasHari = ($presensiKemarin && $presensiKemarin->batas_presensi_pulang)
            ? $presensiKemarin->batas_presensi_pulang
            : ($generalsetting->batas_presensi_lintashari ?? '06:00');

        $tanggalPresensi = $tanggalSekarang;
        $jamKerjaPulang = $jamKerja->jam_pulang;
        $tanggalPulang = ($jamKerja->lintashari == 1) ? $tanggalBesok : $tanggalSekarang;

        if ($flowStatus == 2 && $presensiKemarin && $presensiKemarin->lintashari == 1 && $presensiKemarin->jam_out == null) {
            if ($jamSekarang < $batasLintasHari) {
                $tanggalPresensi = $tanggalKemarin;
                $tanggalPulang = $tanggalSekarang;
                $jamKerjaPulang = $presensiKemarin->jam_pulang;
            }
        }

        // 6. Backend GPS Radius Validation
        [$latUser, $lngUser] = explode(',', $lokasi);
        [$latKantor, $lngKantor] = explode(',', $lokasiKantor);

        $jarak = hitungjarak((float)$latKantor, (float)$lngKantor, (float)$latUser, (float)$lngUser);
        $radiusMeters = round($jarak['meters'] ?? 999999);

        $statusLockLocation = $karyawan->lock_location ?? 1;

        $radiusAllowed = $cabang->radius_cabang ?? 50;
        if ($statusLockLocation == 1 && $radiusMeters > $radiusAllowed) {
            return [
                'success' => false,
                'code' => 400,
                'message' => 'Anda berada di luar radius kantor! Jarak Anda ' . formatAngka($radiusMeters) . ' meter dari kantor (Batas: ' . $radiusAllowed . ' m).',
                'notifikasi' => 'notifikasi_radius',
                'suara' => 'Maaf, Anda berada di luar radius kantor!'
            ];
        }

        $jamPresensi = $tanggalSekarang . ' ' . $jamSekarang;
        $jamPresensiCarbon = Carbon::parse($jamPresensi, $timezoneCabang);

        return [
            'success' => true,
            'karyawan' => $karyawan,
            'cabang' => $cabang,
            'generalsetting' => $generalsetting,
            'jam_kerja' => $jamKerja,
            'tanggal_presensi' => $tanggalPresensi,
            'tanggal_pulang' => $tanggalPulang,
            'jam_kerja_pulang' => $jamKerjaPulang,
            'jam_presensi' => $jamPresensi,
            'jam_presensi_carbon' => $jamPresensiCarbon,
            'timezone_cabang' => $timezoneCabang,
            'radius_meters' => $radiusMeters,
        ];
    }
}
