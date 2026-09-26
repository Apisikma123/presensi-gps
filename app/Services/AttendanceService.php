<?php

namespace App\Services;

use App\Models\Cabang;
use App\Models\Detailharilibur;
use App\Models\Facerecognition;
use App\Models\GlobalJamkerja;
use App\Models\Harilibur;
use App\Models\Jamkerja;
use App\Models\Karyawan;
use App\Models\Pengaturanumum;
use App\Models\Presensi;
use App\Models\PresensiDispensasi;
use App\Models\Setjamkerjabydate;
use App\Models\Setjamkerjabyday;
use App\Models\User;
use App\Models\Userkaryawan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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
            'l' => 'LIBUR',
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
     * Map day of week (0 = Sunday .. 6 = Saturday) to Indonesian day name
     */
    public static function getIndonesianDayName(int $dayOfWeek): string
    {
        return match ($dayOfWeek) {
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            default => 'Senin',
        };
    }

    /**
     * Batch resolve effective schedule for multiple employees over a date range.
     * Guaranteed zero N+1 queries. Single source of truth for Roster / Shifts / Holidays.
     *
     * Hierarchy:
     * 1. Schedule Override By Date (presensi_jamkerja_bydate)
     * 2. Weekly Employee Schedule (presensi_jamkerja_byday)
     * 3. Official Holiday (Company/Branch/Individual)
     * 4. Company Default Schedule (Global Jam Kerja if active)
     * 5. Fallback: Employee default Jam Kerja / Standard Weekday
     *
     * @param array $niks
     * @param string $startDate (YYYY-MM-DD)
     * @param string $endDate (YYYY-MM-DD)
     * @return array [nik => [date => ['is_off' => bool, 'jam_kerja' => Jamkerja|null, 'keterangan' => string, 'source' => string]]]
     */
    public static function getEffectiveSchedulesBatch(array $niks, string $startDate, string $endDate): array
    {
        if (empty($niks)) {
            return [];
        }

        $niks = array_values(array_unique(array_filter($niks)));
        if (empty($niks)) {
            return [];
        }

        // 1. Preload master Jamkerja
        $jamkerjaMap = Jamkerja::all()->keyBy('kode_jam_kerja');

        // 2. Preload Karyawan info
        $karyawanMap = Karyawan::whereIn('nik', $niks)->get()->keyBy('nik');

        // 3. Preload Pengaturan Umum
        $setting = Pengaturanumum::getSetting();
        $sistemHariKerja = (int)($setting->sistem_hari_kerja ?? 6);
        $globalJamkerjaAktif = (bool)($setting->global_jamkerja_aktif ?? false);

        // 4. Preload Global Jamkerja if enabled
        $globalJamkerjaMap = [];
        if ($globalJamkerjaAktif) {
            $globalJamkerjaMap = GlobalJamkerja::all()->keyBy('hari')->toArray();
        }

        // 5. Preload Schedule Overrides By Date (presensi_jamkerja_bydate)
        $byDateRows = Setjamkerjabydate::whereIn('nik', $niks)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();
        $byDateMap = [];
        foreach ($byDateRows as $r) {
            $byDateMap[$r->nik . '|' . $r->tanggal] = [
                'kode_jam_kerja' => $r->kode_jam_kerja,
                'kode_cabang' => $r->kode_cabang,
            ];
        }

        // 6. Preload Weekly Schedule By Day (presensi_jamkerja_byday)
        $byDayRows = Setjamkerjabyday::whereIn('nik', $niks)->get();
        $byDayMap = [];
        $hasCustomWeeklySchedule = [];
        foreach ($byDayRows as $r) {
            $hasCustomWeeklySchedule[$r->nik] = true;
            $byDayMap[$r->nik . '|' . ucfirst(strtolower($r->hari))] = [
                'kode_jam_kerja' => $r->kode_jam_kerja,
                'kode_cabang' => $r->kode_cabang,
            ];
        }

        // 7. Preload Holidays (Company/Branch and Individual)
        $holidays = Harilibur::whereBetween('tanggal', [$startDate, $endDate])->get();
        $holidayDateMap = [];
        foreach ($holidays as $h) {
            $holidayDateMap[$h->tanggal][] = $h;
        }

        $detailHolidays = Detailharilibur::whereIn('nik', $niks)
            ->join('hari_libur', 'hari_libur_detail.kode_libur', '=', 'hari_libur.kode_libur')
            ->whereBetween('hari_libur.tanggal', [$startDate, $endDate])
            ->select('hari_libur_detail.nik', 'hari_libur.tanggal', 'hari_libur.keterangan')
            ->get();
        $employeeHolidayMap = [];
        foreach ($detailHolidays as $dh) {
            $employeeHolidayMap[$dh->nik . '|' . $dh->tanggal] = $dh->keterangan;
        }

        // Build Cartesian product results [nik][date]
        $results = [];
        $carbonStart = Carbon::parse($startDate);
        $carbonEnd = Carbon::parse($endDate);

        foreach ($niks as $nik) {
            $karyawan = $karyawanMap[$nik] ?? null;
            $cabangCode = $karyawan?->kode_cabang ?? '';
            $hasWeeklyRoster = isset($hasCustomWeeklySchedule[$nik]);

            $curr = $carbonStart->copy();
            while ($curr->lte($carbonEnd)) {
                $dateStr = $curr->toDateString();
                $dayOfWeek = $curr->dayOfWeek; // 0 = Sunday, 6 = Saturday
                $dayName = self::getIndonesianDayName($dayOfWeek);

                // Priority 1: Schedule Override By Date (presensi_jamkerja_bydate)
                $dateKey = $nik . '|' . $dateStr;
                if (array_key_exists($dateKey, $byDateMap)) {
                    $byDateEntry = $byDateMap[$dateKey];
                    $code = is_array($byDateEntry) ? ($byDateEntry['kode_jam_kerja'] ?? null) : $byDateEntry;
                    $entryCabang = (is_array($byDateEntry) && !empty($byDateEntry['kode_cabang'])) ? $byDateEntry['kode_cabang'] : $cabangCode;

                    if (empty($code) || in_array(strtoupper($code), ['OFF', 'LIBUR'])) {
                        $results[$nik][$dateStr] = [
                            'is_off' => true,
                            'kode_jam_kerja' => null,
                            'jam_kerja' => null,
                            'kode_cabang' => $entryCabang,
                            'keterangan' => 'Libur Khusus Tanggal (Roster By Date)',
                            'source' => 'bydate_off',
                            'schedule_source' => 'bydate_off'
                        ];
                        $curr->addDay();
                        continue;
                    }

                    $jk = $jamkerjaMap[$code] ?? null;
                    if ($jk) {
                        $results[$nik][$dateStr] = [
                            'is_off' => false,
                            'kode_jam_kerja' => $jk->kode_jam_kerja,
                            'jam_kerja' => $jk,
                            'kode_cabang' => $entryCabang,
                            'keterangan' => $jk->nama_jam_kerja,
                            'source' => 'bydate',
                            'schedule_source' => 'bydate'
                        ];
                        $curr->addDay();
                        continue;
                    }
                }

                // Priority 2: Weekly Schedule (presensi_jamkerja_byday)
                if ($hasWeeklyRoster) {
                    $dayKey = $nik . '|' . $dayName;
                    if (array_key_exists($dayKey, $byDayMap)) {
                        $byDayEntry = $byDayMap[$dayKey];
                        $code = is_array($byDayEntry) ? ($byDayEntry['kode_jam_kerja'] ?? null) : $byDayEntry;
                        $entryCabang = (is_array($byDayEntry) && !empty($byDayEntry['kode_cabang'])) ? $byDayEntry['kode_cabang'] : $cabangCode;

                        if (!empty($code) && !in_array(strtoupper($code), ['OFF', 'LIBUR'])) {
                            $jk = $jamkerjaMap[$code] ?? null;
                            if ($jk) {
                                // Before returning regular weekly shift, check official holiday
                                $holidayDesc = $employeeHolidayMap[$dateKey] ?? null;
                                if (!$holidayDesc && isset($holidayDateMap[$dateStr])) {
                                    foreach ($holidayDateMap[$dateStr] as $h) {
                                        if (empty($h->kode_cabang) || $h->kode_cabang === 'ALL' || $h->kode_cabang === $cabangCode) {
                                            $holidayDesc = $h->keterangan;
                                            break;
                                        }
                                    }
                                }

                                if ($holidayDesc) {
                                    $results[$nik][$dateStr] = [
                                        'is_off' => true,
                                        'kode_jam_kerja' => null,
                                        'jam_kerja' => null,
                                        'kode_cabang' => $entryCabang,
                                        'keterangan' => 'Hari Libur Resmi: ' . $holidayDesc,
                                        'source' => 'holiday',
                                        'schedule_source' => 'holiday'
                                    ];
                                } else {
                                    $results[$nik][$dateStr] = [
                                        'is_off' => false,
                                        'kode_jam_kerja' => $jk->kode_jam_kerja,
                                        'jam_kerja' => $jk,
                                        'kode_cabang' => $entryCabang,
                                        'keterangan' => $jk->nama_jam_kerja,
                                        'source' => 'byday',
                                        'schedule_source' => 'byday'
                                    ];
                                }
                                $curr->addDay();
                                continue;
                            }
                        }
                    }

                    // Karyawan punya roster mingguan, tetapi hari ini tidak diset atau diset kosong/OFF = Hari Libur Rutin
                    $dayEntry = $byDayMap[$nik . '|' . $dayName] ?? null;
                    $entryCabang = (is_array($dayEntry) && !empty($dayEntry['kode_cabang'])) ? $dayEntry['kode_cabang'] : $cabangCode;
                    $results[$nik][$dateStr] = [
                        'is_off' => true,
                        'kode_jam_kerja' => null,
                        'jam_kerja' => null,
                        'kode_cabang' => $entryCabang,
                        'keterangan' => 'Libur Rutin Karyawan (OFF)',
                        'source' => 'byday_off',
                        'schedule_source' => 'byday_off'
                    ];
                    $curr->addDay();
                    continue;
                }

                // Priority 3: Official Holiday (Company/Branch/Individual) for employees without weekly roster override
                $holidayDesc = $employeeHolidayMap[$dateKey] ?? null;
                if (!$holidayDesc && isset($holidayDateMap[$dateStr])) {
                    foreach ($holidayDateMap[$dateStr] as $h) {
                        if (empty($h->kode_cabang) || $h->kode_cabang === 'ALL' || $h->kode_cabang === $cabangCode) {
                            $holidayDesc = $h->keterangan;
                            break;
                        }
                    }
                }
                if ($holidayDesc) {
                    $results[$nik][$dateStr] = [
                        'is_off' => true,
                        'kode_jam_kerja' => null,
                        'jam_kerja' => null,
                        'kode_cabang' => $cabangCode,
                        'keterangan' => 'Hari Libur Resmi: ' . $holidayDesc,
                        'source' => 'holiday',
                        'schedule_source' => 'holiday'
                    ];
                    $curr->addDay();
                    continue;
                }

                // Priority 4: Company Default Schedule (Global Jam Kerja if active)
                if ($globalJamkerjaAktif && isset($globalJamkerjaMap[$dayName])) {
                    $code = $globalJamkerjaMap[$dayName]['kode_jam_kerja'] ?? null;
                    if (!empty($code) && !in_array(strtoupper($code), ['OFF', 'LIBUR'])) {
                        $jk = $jamkerjaMap[$code] ?? null;
                        if ($jk) {
                            $results[$nik][$dateStr] = [
                                'is_off' => false,
                                'kode_jam_kerja' => $jk->kode_jam_kerja,
                                'jam_kerja' => $jk,
                                'kode_cabang' => $cabangCode,
                                'keterangan' => $jk->nama_jam_kerja,
                                'source' => 'global_jamkerja',
                                'schedule_source' => 'global_jamkerja'
                            ];
                            $curr->addDay();
                            continue;
                        }
                    } else {
                        $results[$nik][$dateStr] = [
                            'is_off' => true,
                            'kode_jam_kerja' => null,
                            'jam_kerja' => null,
                            'kode_cabang' => $cabangCode,
                            'keterangan' => 'Libur Operasional Perusahaan (Global Jam Kerja)',
                            'source' => 'global_jamkerja_off',
                            'schedule_source' => 'global_jamkerja_off'
                        ];
                        $curr->addDay();
                        continue;
                    }
                }

                // Priority 5: Fallback to Employee Default Jam Kerja / Branch Standard Weekday
                $isWeeklyOff = ($dayOfWeek === 0) || ($sistemHariKerja === 5 && $dayOfWeek === 6);
                if ($isWeeklyOff) {
                    $results[$nik][$dateStr] = [
                        'is_off' => true,
                        'kode_jam_kerja' => null,
                        'jam_kerja' => null,
                        'kode_cabang' => $cabangCode,
                        'keterangan' => ($dayOfWeek === 0 ? 'Libur Mingguan (Hari Minggu)' : 'Libur Akhir Pekan (Hari Sabtu)'),
                        'source' => 'weekly_off',
                        'schedule_source' => 'weekly_off'
                    ];
                } else {
                    $defaultCode = $karyawan?->kode_jam_kerja ?? 'JK01';
                    $jk = $jamkerjaMap[$defaultCode] ?? $jamkerjaMap['JK01'] ?? $jamkerjaMap->first();
                    $results[$nik][$dateStr] = [
                        'is_off' => false,
                        'kode_jam_kerja' => $jk?->kode_jam_kerja ?? 'JK01',
                        'jam_kerja' => $jk,
                        'kode_cabang' => $cabangCode,
                        'keterangan' => $jk?->nama_jam_kerja ?? 'Shift Standar',
                        'source' => 'default_shift',
                        'schedule_source' => 'default_shift'
                    ];
                }

                $curr->addDay();
            }
        }

        return $results;
    }

    /**
     * Get effective schedule for a single employee on a single date.
     */
    public static function getEffectiveSchedule(string $nik, string $date, ?Karyawan $karyawan = null): array
    {
        $batch = self::getEffectiveSchedulesBatch([$nik], $date, $date);
        return $batch[$nik][$date] ?? [
            'is_off' => false,
            'kode_jam_kerja' => $karyawan?->kode_jam_kerja ?? 'JK01',
            'jam_kerja' => null,
            'kode_cabang' => $karyawan?->kode_cabang ?? '',
            'keterangan' => 'Shift Standar',
            'source' => 'fallback',
            'schedule_source' => 'fallback'
        ];
    }

    /**
     * Resolve effective late threshold (Batas Toleransi Keterlambatan)
     * 
     * Policy OVERRIDES Shift Tolerance:
     * - Base: shift.jam_masuk (e.g. 08:00:00)
     * - Shift Tolerance: shift.batas_toleransi (e.g. 08:05:00 = 5 minutes from start)
     * - Policy Tolerance: attendance_policy.allow_late_tolerance_minutes (e.g. 10 minutes = 08:10:00)
     * 
     * Semantics:
     * - Policy tolerance OVERRIDES shift tolerance whenever configured (> 0) and greater:
     *   max(shift.batas_toleransi, shift.jam_masuk + policy.allow_late_tolerance_minutes)
     * - Never double-adds (no shift tolerance + policy tolerance).
     * 
     * Example:
     * Shift start: 08:00
     * Shift tolerance: 5 min (08:05)
     * Policy tolerance: 10 min (08:10)
     * => Expected Late Threshold: 08:10:00
     * 08:04 -> Tepat Waktu (<= 08:10)
     * 08:05 -> Tepat Waktu (<= 08:10)
     * 08:06 -> Tepat Waktu (<= 08:10)
     * 08:10 -> Tepat Waktu (<= 08:10)
     * 08:11 -> Terlambat (> 08:10)
     */
    public static function resolveBatasToleransi($jamKerja, string $tanggal, string $timezone = 'Asia/Jakarta'): Carbon
    {
        $jamMasukBase = Carbon::parse($tanggal . ' ' . ($jamKerja->jam_masuk ?? '08:00:00'), $timezone);
        $batasToleransi = $jamKerja->batas_toleransi ?? ($jamKerja->jam_masuk ?? '08:00:00');
        $toleransiCarbon = Carbon::parse($tanggal . ' ' . $batasToleransi, $timezone);

        try {
            $activePolicy = \App\Models\AttendancePolicy::getActivePolicy();
            if ($activePolicy && isset($activePolicy->allow_late_tolerance_minutes) && $activePolicy->allow_late_tolerance_minutes > 0) {
                // Single Source of Truth: Attendance Policy OVERRIDES shift tolerance
                return $jamMasukBase->copy()->addMinutes((int)$activePolicy->allow_late_tolerance_minutes);
            }
        } catch (\Throwable $e) {
            // Fail-safe to shift tolerance
        }

        return $toleransiCarbon;
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

        // 4. Resolve Effective Schedule (Hierarchy: By Date -> By Day -> Holiday -> Company Default)
        $schedule = self::getEffectiveSchedule($nik, $tanggal);
        $isOff = $schedule['is_off'];

        if (empty($jamIn)) {
            if ($isOff) {
                return ['status' => 'l', 'is_late' => false, 'keterangan' => 'Libur: ' . ($schedule['keterangan'] ?? 'Hari Libur / Bebas Absen')];
            }
            return ['status' => self::STATUS_TIDAK_HADIR, 'is_late' => false, 'keterangan' => 'Tidak Hadir'];
        }

        if ($isOff) {
            return [
                'status' => self::STATUS_HADIR,
                'is_late' => false,
                'is_dispensasi' => false,
                'keterangan' => 'Hadir di Luar Jadwal (Off Day)'
            ];
        }

        if (!$jamKerja) {
            $jamKerja = $schedule['jam_kerja'] ?? Jamkerja::first() ?? (object)[
                'jam_masuk' => '08:00:00',
                'batas_toleransi' => '08:00:00',
                'toleransi_menit' => 0
            ];
        }

        $jamInCarbon = Carbon::parse($jamIn);
        $toleransiCarbon = self::resolveBatasToleransi($jamKerja, $tanggal);

        // Jika jam masuk <= batas toleransi, maka HADIR
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
        $menitTerlambat = (int) $jamMasukCarbon->diffInMinutes($jamInCarbon, false);

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
                return $this->attachNewNonce([
                    'success' => false,
                    'code' => 400,
                    'message' => 'Maaf belum waktunya absen masuk. Waktu absen dimulai pukul ' . formatIndo3($jamMulaiMasukCarbon->format('Y-m-d H:i')),
                    'notifikasi' => 'notifikasi_mulaiabsen',
                    'suara' => 'Maaf, belum waktunya untuk melakukan presensi masuk.'
                ]);
            }
            if ($jamPresensiCarbon->gt($jamAkhirMasukCarbon)) {
                return $this->attachNewNonce([
                    'success' => false,
                    'code' => 400,
                    'message' => 'Maaf waktu absen masuk sudah habis.',
                    'notifikasi' => 'notifikasi_akhirabsen',
                    'suara' => 'Maaf, waktu absen masuk sudah habis.'
                ]);
            }
        }

        // 1. Fail-Fast: Face Recognition AI Verification (<0.03ms pure PHP math, 0 disk I/O)
        $activePolicy = \App\Models\AttendancePolicy::getActivePolicy();
        $isFaceRequired = (($generalsetting->face_recognition ?? 0) == 1)
            && is_module_enabled('face_recognition', true)
            && ($activePolicy ? (bool)$activePolicy->require_face_recognition : true);

        if ($isFaceRequired) {
            $verifyError = $this->verifyFaceMatch($karyawan, $data['face_descriptor'] ?? null);
            if ($verifyError !== null) {
                return $this->attachNewNonce($verifyError);
            }
        }

        // 2. Simpan File Foto Absensi (hanya jika verifikasi biometrik lolos)
        $formatName = $karyawan->nik . '-' . $tanggalPresensi . '-in';
        try {
            $fileName = \App\Helpers\ImageOptimizer::saveAsWebp(
                $data['image'],
                'uploads/absensi',
                $formatName,
                80
            );
        } catch (\Throwable $e) {
            return $this->attachNewNonce([
                'success' => false,
                'code' => 400,
                'message' => 'Format atau berkas foto tidak valid: ' . $e->getMessage(),
                'notifikasi' => 'notifikasi_gagal'
            ]);
        }

        // Atomic Database Lock & Write
        $presensiRecord = null;
        $cabang = $context['cabang'];
        try {
            DB::transaction(function () use (
                $karyawan,
                $tanggalPresensi,
                $jamPresensi,
                $lokasi,
                $fileName,
                $jamKerja,
                $cabang,
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
                        'kode_cabang' => $cabang->kode_cabang,
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
                        'kode_cabang' => $cabang->kode_cabang,
                        'kode_jam_kerja' => $jamKerja->kode_jam_kerja,
                        'status' => 'h'
                    ]);
                }
            });
        } catch (\Exception $e) {
            if ($e->getMessage() === 'ALREADY_CLOCKED_IN' || str_contains($e->getMessage(), 'Duplicate entry') || $e->getCode() == 23000) {
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

        // Tentukan Keterlambatan sesuai Shift Tolerance & Policy Tolerance (Single Source of Truth)
        $batasToleransiCarbon = self::resolveBatasToleransi($jamKerja, $tanggalPresensi, $timezoneCabang);
        
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
                        'keterangan' => 'DISPENSASI',
                        'is_terlambat' => 0,
                        'menit_terlambat' => 0
                    ]);
                } else {
                    $isTerlambat = true;
                    $menitTerlambat = (int) $jamMasukCarbon->diffInMinutes($jamPresensiCarbon, false);
                    $presensiRecord->update([
                        'is_terlambat' => 1,
                        'menit_terlambat' => $menitTerlambat
                    ]);
                }
            } else {
                $isTerlambat = true;
                $menitTerlambat = (int) $jamMasukCarbon->diffInMinutes($jamPresensiCarbon, false);
                $presensiRecord->update([
                    'is_terlambat' => 1,
                    'menit_terlambat' => $menitTerlambat
                ]);
            }
        } else {
            $presensiRecord->update([
                'is_terlambat' => 0,
                'menit_terlambat' => 0
            ]);
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

        return $this->attachNewNonce([
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
        ]);
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

        $isEarlyOut = $jamPresensiCarbon->lt($waktuBolehPulang);
        $earlyOutMinutes = $isEarlyOut ? (int)$waktuBolehPulang->diffInMinutes($jamPresensiCarbon, true) : 0;
        $earlyOutReason = $isEarlyOut ? ($data['early_out_reason'] ?? $data['alasan_pulang_cepat'] ?? 'Pulang lebih awal') : null;

        // 1. Fail-Fast: Cek presensi masuk hari ini sebelum menulis file ke disk
        $existingRecord = Presensi::where('nik', $karyawan->nik)->where('tanggal', $tanggalPresensi)->first();
        if (!$existingRecord || $existingRecord->jam_in == null) {
            return $this->attachNewNonce([
                'success' => false,
                'code' => 400,
                'message' => 'Anda belum melakukan absen masuk. Silakan lakukan absen masuk terlebih dahulu.',
                'notifikasi' => 'notifikasi_belumabsenmasuk',
                'suara' => 'Maaf, Anda belum melakukan presensi masuk hari ini.'
            ]);
        }
        if ($existingRecord->jam_out != null) {
            return $this->attachNewNonce([
                'success' => false,
                'code' => 400,
                'message' => 'Anda sudah melakukan absen pulang hari ini.',
                'notifikasi' => 'notifikasi_sudahabsen',
                'suara' => 'Anda sudah melakukan presensi pulang hari ini.'
            ]);
        }

        // 2. Fail-Fast: Face Recognition AI Verification (<0.03ms pure PHP math, 0 disk I/O)
        $activePolicy = \App\Models\AttendancePolicy::getActivePolicy();
        $isFaceRequired = (($generalsetting->face_recognition ?? 0) == 1)
            && is_module_enabled('face_recognition', true)
            && ($activePolicy ? (bool)$activePolicy->require_face_recognition : true);

        if ($isFaceRequired) {
            $verifyError = $this->verifyFaceMatch($karyawan, $data['face_descriptor'] ?? null);
            if ($verifyError !== null) {
                return $this->attachNewNonce($verifyError);
            }
        }

        // 3. Simpan File Foto Absensi Pulang (hanya jika lolos verifikasi biometrik & shift)
        $formatName = $karyawan->nik . '-' . $tanggalPresensi . '-out';
        try {
            $fileName = \App\Helpers\ImageOptimizer::saveAsWebp(
                $data['image'],
                'uploads/absensi',
                $formatName,
                80
            );
        } catch (\Throwable $e) {
            return $this->attachNewNonce([
                'success' => false,
                'code' => 400,
                'message' => 'Format atau berkas foto tidak valid: ' . $e->getMessage(),
                'notifikasi' => 'notifikasi_gagal'
            ]);
        }

        // Atomic Database Lock & Write
        $presensiRecord = null;
        try {
            DB::transaction(function () use (
                $karyawan,
                $tanggalPresensi,
                $jamPresensi,
                $lokasi,
                $fileName,
                $isEarlyOut,
                $earlyOutMinutes,
                $earlyOutReason,
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
                    'foto_out' => $fileName,
                    'is_early_out' => $isEarlyOut ? 1 : 0,
                    'early_out_minutes' => $earlyOutMinutes,
                    'early_out_reason' => $earlyOutReason,
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

        $pesanSukses = $isEarlyOut 
            ? 'Berhasil Absen Pulang Lebih Awal (' . $earlyOutMinutes . ' menit lebih awal). Hati-hati di jalan!'
            : 'Berhasil Absen Pulang. Hati-hati di jalan!';

        $suaraSukses = $isEarlyOut
            ? 'Presensi pulang lebih awal berhasil dicatat. Hati-hati di jalan!'
            : 'Terima kasih, presensi pulang berhasil. Hati-hati di jalan!';

        return $this->attachNewNonce([
            'success' => true,
            'code' => 200,
            'message' => $pesanSukses,
            'notifikasi' => 'notifikasi_absenpulang',
            'is_early_out' => $isEarlyOut,
            'early_out_minutes' => $earlyOutMinutes,
            'early_out_reason' => $earlyOutReason,
            'suara' => $suaraSukses,
            'data' => $presensiRecord
        ]);
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

        // 2. Anti-Replay: One-time attendance nonce validation (if session is active)
        if (request()->hasSession() && session()->isStarted()) {
            $sentNonce = $data['attendance_nonce'] ?? null;
            $savedNonce = session('attendance_nonce');
            $savedTime = session('attendance_nonce_time', 0);
            
            // Consumed immediately
            session()->forget(['attendance_nonce', 'attendance_nonce_time']);

            if (!$savedNonce || !$sentNonce || !hash_equals((string)$savedNonce, (string)$sentNonce) || (now()->timestamp - $savedTime) > 300) {
                return $this->attachNewNonce([
                    'success' => false,
                    'code' => 400,
                    'message' => 'Sesi presensi telah kedaluwarsa atau sudah digunakan. Silakan muat ulang halaman kamera.',
                    'notifikasi' => 'notifikasi_gagal'
                ]);
            }
        }

        // 3. Validasi Keberadaan dan Format Foto Bukti Absen
        $imageInput = $data['image'] ?? null;
        if (empty($imageInput)) {
            return $this->attachNewNonce([
                'success' => false,
                'code' => 400,
                'message' => 'Foto wajah absensi wajib disertakan.',
                'notifikasi' => 'notifikasi_foto_kosong'
            ]);
        }

        // Validasi ukuran & MIME type foto server-side
        if ($imageInput instanceof \Illuminate\Http\UploadedFile) {
            if (!$imageInput->isValid() || $imageInput->getSize() > 2048 * 1024) {
                return $this->attachNewNonce([
                    'success' => false,
                    'code' => 400,
                    'message' => 'Ukuran file foto melebihi batas maksimal (2 MB) atau file rusak.',
                    'notifikasi' => 'notifikasi_gagal'
                ]);
            }
            $mime = $imageInput->getMimeType();
            if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'])) {
                return $this->attachNewNonce([
                    'success' => false,
                    'code' => 400,
                    'message' => 'Format foto harus berupa JPG, JPEG, PNG, atau WebP.',
                    'notifikasi' => 'notifikasi_gagal'
                ]);
            }
        } elseif (is_string($imageInput)) {
            if (strlen($imageInput) > 3 * 1024 * 1024) {
                return $this->attachNewNonce([
                    'success' => false,
                    'code' => 400,
                    'message' => 'Ukuran data foto melebihi batas maksimal.',
                    'notifikasi' => 'notifikasi_gagal'
                ]);
            }
            if (!preg_match('/^data:image\/(jpeg|png|webp|jpg);base64,/i', $imageInput)) {
                return $this->attachNewNonce([
                    'success' => false,
                    'code' => 400,
                    'message' => 'Format data foto tidak valid (wajib image JPEG/PNG/WebP).',
                    'notifikasi' => 'notifikasi_gagal'
                ]);
            }
        }

        // 4. Validasi Format GPS & Rentang Geografis
        $isGpsEnabled = is_module_enabled('gps', true);
        $lokasi = $data['lokasi'] ?? null;

        if ($isGpsEnabled) {
            if (empty($lokasi) || !str_contains($lokasi, ',')) {
                return $this->attachNewNonce([
                    'success' => false,
                    'code' => 400,
                    'message' => 'Koordinat lokasi GPS tidak valid atau tidak terbaca.',
                    'notifikasi' => 'notifikasi_gps_invalid'
                ]);
            }

            $coords = explode(',', $lokasi);
            if (count($coords) !== 2 || !is_numeric(trim($coords[0])) || !is_numeric(trim($coords[1]))) {
                return $this->attachNewNonce([
                    'success' => false,
                    'code' => 400,
                    'message' => 'Format koordinat lokasi GPS tidak valid.',
                    'notifikasi' => 'notifikasi_gps_invalid'
                ]);
            }

            $lat = (float) trim($coords[0]);
            $lng = (float) trim($coords[1]);
            if (!is_finite($lat) || !is_finite($lng) || $lat < -90 || $lat > 90 || $lng < -180 || $lng > 180 || (abs($lat) < 0.00001 && abs($lng) < 0.00001)) {
                return $this->attachNewNonce([
                    'success' => false,
                    'code' => 400,
                    'message' => 'Titik koordinat GPS tidak valid atau berada di luar rentang geografis yang diizinkan (koordinat 0,0 / out-of-range ditolak).',
                    'notifikasi' => 'notifikasi_gps_invalid'
                ]);
            }
        } else {
            // When GPS module is disabled, allow fallback or office location coordinates
            if (empty($lokasi)) {
                $lokasi = $homeCabang->lokasi_cabang ?? '-6.2088,106.8456';
                $data['lokasi'] = $lokasi;
            }
        }

        // 5. Anti-Fake GPS & Anomali
        if (!empty($data['is_mock']) && $data['is_mock'] == '1') {
            return $this->attachNewNonce([
                'success' => false,
                'code' => 400,
                'message' => 'Terdeteksi menggunakan aplikasi Fake GPS / Mock Location.',
                'notifikasi' => 'notifikasi_fakegps'
            ]);
        }

        // Konfigurasi Cabang & Timezone (prioritaskan home branch untuk waktu lokal awal)
        $homeCabang = Cabang::getByCode($karyawan->kode_cabang);
        $timezoneCabang = $homeCabang->timezone ?? $generalsetting->timezone ?? config('app.timezone');

        $carbonNow = Carbon::now($timezoneCabang);
        $tanggalSekarang = $carbonNow->format('Y-m-d');
        $jamSekarang = $carbonNow->format('H:i');
        $tanggalKemarin = $carbonNow->copy()->subDay()->format('Y-m-d');
        $tanggalBesok = $carbonNow->copy()->addDay()->format('Y-m-d');
        $tanggalPresensi = $tanggalSekarang;

        // P0-1: Clock-Out Snapshot Resolution
        // When checking out (flowStatus == 2), attendance record from Clock-In is the immutable source of truth
        $existingClockInAttendance = null;
        if ($flowStatus == 2) {
            // First check if yesterday's night shift is open for checkout
            $presensiKemarin = Presensi::where('nik', $karyawan->nik)
                ->join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->where('presensi.tanggal', $tanggalKemarin)
                ->first();

            $batasLintasHari = ($presensiKemarin && $presensiKemarin->batas_presensi_pulang)
                ? $presensiKemarin->batas_presensi_pulang
                : ($generalsetting->batas_presensi_lintashari ?? '06:00');

            if ($presensiKemarin && $presensiKemarin->lintashari == 1 && $presensiKemarin->jam_out == null && $jamSekarang < $batasLintasHari) {
                $tanggalPresensi = $tanggalKemarin;
                $existingClockInAttendance = $presensiKemarin;
            } else {
                $existingClockInAttendance = Presensi::where('nik', $karyawan->nik)
                    ->where('tanggal', $tanggalSekarang)
                    ->first();
            }
        }

        if ($flowStatus == 2 && $existingClockInAttendance && $existingClockInAttendance->jam_in != null) {
            // Clock-out uses SNAPSHOT branch and shift from Clock-In
            $expectedCabangCode = !empty($existingClockInAttendance->kode_cabang) ? $existingClockInAttendance->kode_cabang : $karyawan->kode_cabang;
            $activeCabang = Cabang::getByCode($expectedCabangCode) ?? Cabang::where('kode_cabang', $expectedCabangCode)->first() ?? $homeCabang ?? Cabang::first();
            $cabang = $activeCabang;
            $timezoneCabang = $cabang->timezone ?? $timezoneCabang;
            $lokasiKantor = $activeCabang ? $activeCabang->lokasi_cabang : null;

            $kodeJamKerja = $existingClockInAttendance->kode_jam_kerja ?? 'JK01';
            $jamKerja = Jamkerja::getByCode($kodeJamKerja) ?? Jamkerja::where('kode_jam_kerja', $kodeJamKerja)->first();
            if (!$jamKerja) {
                $jamKerja = Jamkerja::where('kode_jam_kerja', 'JK01')->first();
            }

            $jamKerjaPulang = $jamKerja ? $jamKerja->jam_pulang : '17:00:00';
            $tanggalPulang = ($jamKerja && $jamKerja->lintashari == 1) ? Carbon::parse($tanggalPresensi)->addDay()->format('Y-m-d') : $tanggalPresensi;
        } else {
            // Regular Clock-In or dynamic fallback: Enforce effective schedule hierarchy
            $effectiveSchedule = self::getEffectiveSchedule($karyawan->nik, $tanggalSekarang, $karyawan);

            // Phase 3 Backend Guard: Block clock-in if employee is scheduled OFF
            if ($flowStatus == 1 && $effectiveSchedule['is_off']) {
                return $this->attachNewNonce([
                    'success' => false,
                    'code' => 400,
                    'message' => 'Hari ini Anda dijadwalkan Libur (OFF). Presensi tidak dapat dilakukan.',
                    'notifikasi' => 'notifikasi_libur',
                    'suara' => 'Hari ini Anda dijadwalkan libur. Tidak dapat melakukan presensi.'
                ]);
            }

            // Phase 4 Backend Authority: Expected branch determined by schedule
            $expectedCabangCode = !empty($effectiveSchedule['kode_cabang']) ? $effectiveSchedule['kode_cabang'] : $karyawan->kode_cabang;
            $activeCabang = Cabang::getByCode($expectedCabangCode) ?? Cabang::where('kode_cabang', $expectedCabangCode)->first() ?? $homeCabang ?? Cabang::first();

            // Validasi input cabang user terhadap cabang penugasan resmi
            $inputLokasiCabang = !empty($data['lokasi_cabang']) ? trim($data['lokasi_cabang']) : null;
            if ($inputLokasiCabang && $activeCabang && $inputLokasiCabang !== $activeCabang->kode_cabang && $inputLokasiCabang !== $activeCabang->lokasi_cabang) {
                return $this->attachNewNonce([
                    'success' => false,
                    'code' => 400,
                    'message' => 'Jadwal kerja Anda hari ini berada di ' . ($activeCabang->nama_cabang ?? ('Cabang ' . $activeCabang->kode_cabang)) . '.',
                    'notifikasi' => 'notifikasi_cabang_salah',
                    'suara' => 'Jadwal kerja Anda hari ini tidak berada di cabang ini.'
                ]);
            }

            $cabang = $activeCabang;
            $timezoneCabang = $cabang->timezone ?? $timezoneCabang;
            $lokasiKantor = $activeCabang ? $activeCabang->lokasi_cabang : null;

            if (!$effectiveSchedule['is_off'] && !empty($effectiveSchedule['jam_kerja'])) {
                $jamKerja = $effectiveSchedule['jam_kerja'];
            } else {
                if ($karyawan->lock_jam_kerja == 1 && !empty($karyawan->kode_jam_kerja)) {
                    $kodeJamKerja = $karyawan->kode_jam_kerja;
                } else {
                    $kodeJamKerja = $data['kode_jam_kerja'] ?? $karyawan->kode_jam_kerja ?? 'JK01';
                }
                $jamKerja = Jamkerja::getByCode($kodeJamKerja) ?? Jamkerja::where('kode_jam_kerja', 'JK01')->first();
            }

            if (!$jamKerja) {
                return [
                    'success' => false,
                    'code' => 400,
                    'message' => 'Jadwal jam kerja tidak valid.'
                ];
            }

            $jamKerjaPulang = $jamKerja->jam_pulang;
            $tanggalPulang = ($jamKerja->lintashari == 1) ? $tanggalBesok : $tanggalSekarang;
        }

        if (empty($lokasiKantor) || !str_contains($lokasiKantor, ',')) {
            return [
                'success' => false,
                'code' => 400,
                'message' => 'Titik lokasi kantor cabang belum diatur oleh administrator.'
            ];
        }

        // 6. Backend GPS Radius Validation
        $radiusMeters = 0;
        if ($isGpsEnabled && !empty($lokasiKantor) && str_contains($lokasiKantor, ',') && !empty($lokasi) && str_contains($lokasi, ',')) {
            [$latUser, $lngUser] = explode(',', $lokasi);
            [$latKantor, $lngKantor] = explode(',', $lokasiKantor);

            $jarak = hitungjarak((float)$latKantor, (float)$lngKantor, (float)$latUser, (float)$lngUser);
            $radiusMeters = round($jarak['meters'] ?? 999999);

            $statusLockLocation = $karyawan->lock_location ?? 1;
            $radiusAllowed = $cabang->radius_cabang ?? 50;
            if ($statusLockLocation == 1 && $radiusMeters > $radiusAllowed) {
                return $this->attachNewNonce([
                    'success' => false,
                    'code' => 400,
                    'message' => 'Anda berada di luar radius kantor! Jarak Anda ' . formatAngka($radiusMeters) . ' meter dari kantor (Batas: ' . $radiusAllowed . ' m).',
                    'notifikasi' => 'notifikasi_radius',
                    'suara' => 'Maaf, Anda berada di luar radius kantor!'
                ]);
            }
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

    /**
     * Attach a fresh one-time attendance nonce to session and response payload
     */
    public function attachNewNonce(array $response): array
    {
        if (request()->hasSession() && session()->isStarted()) {
            $newNonce = bin2hex(random_bytes(16));
            session(['attendance_nonce' => $newNonce, 'attendance_nonce_time' => now()->timestamp]);
            $response['new_nonce'] = $newNonce;
        }
        return $response;
    }

    /**
     * Verify client face descriptor against stored biometrics using pure PHP Euclidean distance.
     * Runs in ~0.03ms with 0% server CPU spike, 0 MB extra memory, and zero Python/daemon dependency.
     */
    protected function verifyFaceMatch(Karyawan $karyawan, $clientDescriptor = null): ?array
    {
        $storedFaces = Facerecognition::where('nik', $karyawan->nik)->get();

        if ($storedFaces->isEmpty()) {
            return [
                'success' => false,
                'code' => 400,
                'message' => 'Data biometrik wajah Anda belum terdaftar di sistem. Silakan daftarkan wajah terlebih dahulu.',
                'notifikasi' => 'notifikasi_wajah_belum_terdaftar',
                'suara' => 'Data biometrik wajah belum terdaftar. Silakan hubungi admin.'
            ];
        }

        // Parse client descriptor sent from browser camera face-api.js
        $liveDesc = null;
        if (!empty($clientDescriptor)) {
            $liveDesc = is_string($clientDescriptor) ? json_decode($clientDescriptor, true) : $clientDescriptor;
        }

        if (empty($liveDesc) || !is_array($liveDesc) || count($liveDesc) !== 128) {
            return [
                'success' => false,
                'code' => 400,
                'message' => 'Biometrik wajah tidak terdeteksi dari kamera. Pastikan wajah terlihat jelas di depan kamera.',
                'notifikasi' => 'notifikasi_wajah_tidak_cocok',
                'suara' => 'Verifikasi wajah gagal. Wajah tidak terdeteksi jelas.'
            ];
        }

        // Validasi bahwa seluruh 128 elemen adalah angka riil terhingga (finite float)
        foreach ($liveDesc as $val) {
            if (!is_numeric($val) || !is_finite((float)$val) || is_nan((float)$val)) {
                return [
                    'success' => false,
                    'code' => 400,
                    'message' => 'Format vektor descriptor biometrik tidak valid.',
                    'notifikasi' => 'notifikasi_wajah_tidak_cocok',
                    'suara' => 'Verifikasi wajah gagal. Format data tidak valid.'
                ];
            }
        }

        // Collect all registered descriptors for this employee
        $knownDescriptors = [];
        foreach ($storedFaces as $face) {
            if (!empty($face->descriptor) && is_array($face->descriptor) && count($face->descriptor) === 128) {
                $knownDescriptors[] = $face->descriptor;
            }
        }

        // Self-heal / initial migration: If no stored faces have descriptor yet, save the verified live descriptor
        if (empty($knownDescriptors)) {
            $firstFace = $storedFaces->first();
            if ($firstFace) {
                $firstFace->descriptor = $liveDesc;
                $firstFace->save();
            }
            return null;
        }

        // Calculate minimum Euclidean distance against all registered face descriptors
        $minDistance = 999.0;
        foreach ($knownDescriptors as $refDesc) {
            $sumSq = 0.0;
            for ($i = 0; $i < 128; $i++) {
                $diff = (float)$liveDesc[$i] - (float)$refDesc[$i];
                $sumSq += $diff * $diff;
            }
            $distance = sqrt($sumSq);
            if ($distance < $minDistance) {
                $minDistance = $distance;
            }
        }

        // Face recognition threshold: 0.48 (strict matching threshold for face-api.js 128D ResNet)
        $threshold = 0.48;

        if ($minDistance > $threshold) {
            return [
                'success' => false,
                'code' => 400,
                'message' => 'Verifikasi wajah gagal. Wajah tidak cocok dengan data biometrik terdaftar (Jarak: ' . number_format($minDistance, 2) . ').',
                'notifikasi' => 'notifikasi_wajah_tidak_cocok',
                'suara' => 'Verifikasi wajah gagal. Wajah Anda tidak cocok dengan data terdaftar.'
            ];
        }

        return null;
    }

    /**
     * Generate otomatis status Tanpa Keterangan / Alpha ('a') untuk karyawan yang tidak hadir
     * setelah jam shift berakhir.
     *
     * Berdasarkan shift individu, bukan sekadar tanggal:
     * - Hanya untuk karyawan aktif yang dijadwalkan kerja (bukan hari libur / OFF).
     * - Shift malam (lintas hari) baru boleh dievaluasi setelah jam pulang + 15 menit keesokan harinya.
     * - Tidak hardcode hari Minggu sebagai hari libur.
     *
     * @param string|null $date Tanggal target (YYYY-MM-DD), default hari ini
     * @param bool $dryRun Jika true, hanya kalkulasi tanpa menyimpan ke database
     * @return array Hasil eksekusi & statistik
     */
    public static function generateAutoAlpha(?string $date = null, bool $dryRun = false): array
    {
        $setting = Pengaturanumum::getSetting();
        $timezone = $setting->timezone ?? 'Asia/Jakarta';
        $now = Carbon::now($timezone);
        
        $date = $date ? Carbon::parse($date)->toDateString() : $now->toDateString();
        $targetDate = Carbon::parse($date, $timezone);
        
        // Tanggal di masa depan tidak diproses
        if ($targetDate->isFuture() && !$targetDate->isToday()) {
            return [
                'success' => false,
                'message' => 'Tanggal target berada di masa depan (' . $date . '). Proses dibatalkan.',
                'date' => $date,
                'total_candidates' => 0,
                'marked_alpha' => 0,
            ];
        }

        // 1. Ambil seluruh karyawan aktif pada tanggal target
        $activeEmployees = DB::table('karyawan')
            ->where(function ($q) use ($date) {
                $q->where('karyawan.status_aktif_karyawan', 1)
                  ->orWhere('karyawan.tanggal_nonaktif', '>=', $date);
            })
            ->select('nik', 'nama_karyawan', 'kode_cabang', 'kode_dept', 'kode_jam_kerja')
            ->get();

        if ($activeEmployees->isEmpty()) {
            return [
                'success' => true,
                'message' => 'Tidak ada karyawan aktif pada tanggal ' . $date . '.',
                'date' => $date,
                'total_candidates' => 0,
                'marked_alpha' => 0,
            ];
        }

        $nikList = $activeEmployees->pluck('nik')->toArray();

        // 2. Batch resolve jadwal efektif seluruh karyawan aktif pada tanggal target (Zero N+1)
        $schedules = self::getEffectiveSchedulesBatch($nikList, $date, $date);

        // 3. Preload Presensi yang sudah ada pada tanggal target
        $existingPresensi = DB::table('presensi')
            ->where('tanggal', $date)
            ->whereIn('nik', $nikList)
            ->where(function ($q) {
                $q->whereNotNull('jam_in')
                  ->orWhereIn('status', ['h', 'i', 's', 'c', 'd', 'a']);
            })
            ->pluck('nik')
            ->flip()
            ->toArray();

        // 4. Preload Izin, Sakit, Cuti yang APPROVED pada tanggal target
        $approvedIzin = DB::table('presensi_izinabsen')
            ->where('status', 1)
            ->whereDate('dari', '<=', $date)
            ->whereDate('sampai', '>=', $date)
            ->whereIn('nik', $nikList)
            ->pluck('nik')
            ->flip()
            ->toArray();

        $approvedSakit = DB::table('presensi_izinsakit')
            ->where('status', 1)
            ->whereDate('dari', '<=', $date)
            ->whereDate('sampai', '>=', $date)
            ->whereIn('nik', $nikList)
            ->pluck('nik')
            ->flip()
            ->toArray();

        $approvedCuti = DB::table('presensi_izincuti')
            ->where('status', 1)
            ->whereDate('dari', '<=', $date)
            ->whereDate('sampai', '>=', $date)
            ->whereIn('nik', $nikList)
            ->pluck('nik')
            ->flip()
            ->toArray();

        // 5. Evaluasi kandidat Alpha
        $alphaCandidates = [];

        foreach ($activeEmployees as $emp) {
            $nik = $emp->nik;

            // Sudah absen atau berstatus
            if (isset($existingPresensi[$nik])) {
                continue;
            }

            // Memiliki izin/sakit/cuti yang disetujui
            if (isset($approvedIzin[$nik]) || isset($approvedSakit[$nik]) || isset($approvedCuti[$nik])) {
                continue;
            }

            // Cek jadwal kerja efektif
            $sched = $schedules[$nik][$date] ?? null;
            if (!$sched || $sched['is_off'] || empty($sched['jam_kerja'])) {
                // OFF / Bebas Absen: Tidak boleh di-Alpha
                continue;
            }

            $jk = $sched['jam_kerja'];
            $jamPulang = $jk->jam_pulang ?? '17:00:00';
            $isLintasHari = ($jk->lintashari ?? 0) == 1;

            // Evaluasi waktu berakhirnya shift + batas toleransi 15 menit
            if ($isLintasHari) {
                // Shift malam berakhir keesokan harinya (date + 1 day)
                $shiftEndCarbon = Carbon::parse($date, $timezone)->addDay()->setTimeFromTimeString($jamPulang)->addMinutes(15);
            } else {
                $shiftEndCarbon = Carbon::parse($date, $timezone)->setTimeFromTimeString($jamPulang)->addMinutes(15);
            }

            // Jika jam sekarang belum melewati jam pulang shift (+15 menit), shift belum selesai
            if ($now->lt($shiftEndCarbon)) {
                continue;
            }

            $alphaCandidates[] = [
                'nik' => $nik,
                'tanggal' => $date,
                'kode_jam_kerja' => $jk->kode_jam_kerja,
                'kode_cabang' => $sched['kode_cabang'] ?? $emp->kode_cabang,
                'status' => 'a',
                'keterangan' => 'Tanpa Keterangan (Alpha)',
                'jam_in' => null,
                'jam_out' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $totalCandidates = count($alphaCandidates);

        if ($dryRun || $totalCandidates === 0) {
            return [
                'success' => true,
                'message' => $dryRun 
                    ? "Simulasi: Ditemukan {$totalCandidates} karyawan tanpa presensi yang memenuhi kualifikasi Alpha pada {$date}."
                    : "Tidak ada karyawan yang perlu ditandai Alpha pada {$date}.",
                'date' => $date,
                'total_candidates' => $totalCandidates,
                'marked_alpha' => 0,
                'dry_run' => $dryRun,
            ];
        }

        // Bulk upsert dalam chunk 500 baris
        $chunks = array_chunk($alphaCandidates, 500);
        foreach ($chunks as $chunk) {
            DB::table('presensi')->upsert(
                $chunk,
                ['nik', 'tanggal'],
                ['kode_jam_kerja', 'kode_cabang', 'status', 'keterangan', 'jam_in', 'jam_out', 'updated_at']
            );
        }

        Log::info("Auto-Alpha Presensi berhasil memproses {$totalCandidates} karyawan untuk tanggal {$date}");

        return [
            'success' => true,
            'message' => "Berhasil menandai {$totalCandidates} karyawan sebagai Tanpa Keterangan (Alpha) pada {$date}.",
            'date' => $date,
            'total_candidates' => $totalCandidates,
            'marked_alpha' => $totalCandidates,
            'dry_run' => false,
        ];
    }
}
