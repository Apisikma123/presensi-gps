<?php

use App\Models\Pengaturanumum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;

function buatkode($nomor_terakhir, $kunci, $jumlah_karakter = 0)
{
    /* mencari nomor baru dengan memecah nomor terakhir dan menambahkan 1
    string nomor baru dibawah ini harus dengan format XXX000000
    untuk penggunaan dalam format lain anda harus menyesuaikan sendiri */
    $nomor_baru = intval(substr($nomor_terakhir, strlen($kunci))) + 1;
    //    menambahkan nol didepan nomor baru sesuai panjang jumlah karakter
    $nomor_baru_plus_nol = str_pad($nomor_baru, $jumlah_karakter, "0", STR_PAD_LEFT);
    //    menyusun kunci dan nomor baru
    $kode = $kunci . $nomor_baru_plus_nol;
    return $kode;
}

function messageSuccess($message)
{
    return ['success' => $message];
}


function messageError($message)
{
    return ['error' => $message];
}


// Mengubah ke Huruf Besar
function textUpperCase($value)
{
    return strtoupper(strtolower($value));
}
// Mengubah ke CamelCase
function textCamelCase($value)
{
    return ucwords(strtolower($value));
}


function getdocMarker($file)
{
    $url = url('/storage/marker/' . $file);
    return $url;
}


function getfotoPelanggan($file)
{
    if (empty($file)) {
        return asset('assets/img/avatars/default.png');
    }
    return url('/storage/pelanggan/' . $file);
}


function getfotoKaryawan($file)
{
    if (empty($file)) {
        return asset('assets/img/avatars/default.png');
    }
    return url('/storage/karyawan/' . $file);
}





function toNumber($value)
{
    if (!empty($value)) {
        // Hapus semua karakter kecuali angka, koma, dan titik (untuk menangani prefix Rp dll)
        $clean = preg_replace('/[^0-9,.]/', '', $value);
        // Jika format Indonesia: titik adalah ribuan (dihapus), koma adalah desimal (ganti ke titik)
        return str_replace([".", ","], ["", "."], $clean);
    } else {
        return 0;
    }
}


function formatRupiah($nilai)
{
    return number_format($nilai, '0', ',', '.');
}

function formatAngka($nilai)
{
    if (isset($nilai) && is_numeric($nilai)) {
        return number_format($nilai, '0', ',', '.');
    }
    return $nilai;
}


function formatAngkaDesimal($nilai)
{
    if (isset($nilai) && is_numeric($nilai)) {
        return number_format($nilai, '2', ',', '.');
    }
    return $nilai;
}



function DateToIndo($date2)
{ // fungsi atau method untuk mengubah tanggal ke format indonesia
    // variabel BulanIndo merupakan variabel array yang menyimpan nama-nama bulan
    $BulanIndo2 = array(
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember"
    );

    $tahun2 = substr($date2, 0, 4); // memisahkan format tahun menggunakan substring
    $bulan2 = substr($date2, 5, 2); // memisahkan format bulan menggunakan substring
    $tgl2   = substr($date2, 8, 2); // memisahkan format tanggal menggunakan substring

    $result = $tgl2 . " " . $BulanIndo2[(int)$bulan2 - 1] . " " . $tahun2;
    return ($result);
}


// function cektutupLaporan($tgl, $jenislaporan)
// {
//     $tanggal = explode("-", $tgl);
//     $bulan = $tanggal[1];
//     $tahun = $tanggal[0];
//     $cek = Tutuplaporan::where('jenis_laporan', $jenislaporan)
//         ->where('bulan', $bulan)
//         ->where('tahun', $tahun)
//         ->where('status', 1)
//         ->count();
//     return $cek;
// }


function getbulandantahunlalu($bulan, $tahun, $show)
{
    if ($bulan == 1) {
        $bulanlalu = 12;
        $tahunlalu = $tahun - 1;
    } else {
        $bulanlalu = $bulan - 1;
        $tahunlalu = $tahun;
    }

    if ($show == "tahun") {
        return $tahunlalu;
    } elseif ($show == "bulan") {
        return $bulanlalu;
    }
}


function getbulandantahunberikutnya($bulan, $tahun, $show)
{
    if ($bulan == 12) {
        $bulanberikutnya =  1;
        $tahunberikutnya = $tahun + 1;
    } else {
        $bulanberikutnya = $bulan + 1;
        $tahunberikutnya = $tahun;
    }

    if ($show == "tahun") {
        return $tahunberikutnya;
    } elseif ($show == "bulan") {
        return $bulanberikutnya;
    }
}


function lockreport($tanggal)
{
    $start_year = config('global.start_year');
    $lock_date = $start_year . "-01-01";

    if ($tanggal < $lock_date && !empty($tanggal)) {
        return "error";
    } else {
        return "success";
    }
}



// function getBeratliter($tanggal)
// {
//     if ($tanggal <= "2022-03-01") {
//         $berat = 0.9064;
//     } else {
//         $berat = 1;
//     }
//     return $berat;
// }
function formatIndo($date)
{
    $tanggal = !empty($date) ? date('d-m-Y', strtotime($date)) : '';
    return $tanggal;
}

function formatIndo2($date)
{
    $tanggal = !empty($date) ? date('d-m-y', strtotime($date)) : '';
    return $tanggal;
}

function formatIndo3($date)
{
    $tanggal = !empty($date) ? date('d-m-Y H:i', strtotime($date)) : '';
    return $tanggal;
}

function formatName2($name)
{
    // Kode ini mengambil nama lengkap dan mengembalikan hanya dua kata pertama dari nama tersebut.
    // Contoh: jika nama lengkap adalah "John Doe Smith", maka kode ini akan mengembalikan "John Doe".
    $words = explode(' ', $name); // Memecah nama menjadi array kata-kata berdasarkan spasi.
    return implode(' ', array_slice($words, 0, 2)); // Mengembalikan dua kata pertama yang dihubungkan dengan spasi.
}



function getNamaDepan($name)
{
    $words = explode(' ', $name);
    return $words[0];
}


function removeTitik($value)
{
    return str_replace('.', '', $value);
}
function getnamaHari($hari)
{
    // $hari = date("D");

    switch ($hari) {
        case 'Sun':
            $hari_ini = "Minggu";
            break;

        case 'Mon':
            $hari_ini = "Senin";
            break;

        case 'Tue':
            $hari_ini = "Selasa";
            break;

        case 'Wed':
            $hari_ini = "Rabu";
            break;

        case 'Thu':
            $hari_ini = "Kamis";
            break;

        case 'Fri':
            $hari_ini = "Jumat";
            break;

        case 'Sat':
            $hari_ini = "Sabtu";
            break;

        default:
            $hari_ini = "Tidak di ketahui";
            break;
    }

    return $hari_ini;
}


function hitungjarak($lat1, $lon1, $lat2, $lon2)
{
    $theta = $lon1 - $lon2;
    $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
    $miles = acos($miles);
    $miles = rad2deg($miles);
    $miles = $miles * 60 * 1.1515;
    $feet = $miles * 5280;
    $yards = $feet / 3;
    $kilometers = $miles * 1.609344;
    $meters = $kilometers * 1000;
    return compact('meters');
}


function hitungHari($startDate, $endDate)
{
    if ($startDate && $endDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);

        // Tambahkan 1 hari agar penghitungan inklusif
        $interval = $start->diff($end);
        $dayDifference = $interval->days + 1;

        return  $dayDifference;
    } else {
        return 0;
    }
}

function getSid($file)
{
    $url = url('/storage/uploads/sid/' . $file);
    return $url;
}

function hitungpulangcepat($tanggal_presensi, $jam_out, $jam_pulang, $istirahat, $jam_awal_istirahat, $jam_akhir_istirahat, $lintashari)
{


    $tanggal = $lintashari == 1 ? date('Y-m-d', strtotime($tanggal_presensi . ' +1 day')) : $tanggal_presensi;
    $jam_awal_istirahat = $tanggal . ' ' . $jam_awal_istirahat;
    $jam_akhir_istirahat = $tanggal . ' ' . $jam_akhir_istirahat;
    $jam_pulang = $tanggal . ' ' . $jam_pulang;

    if (empty($jam_out)) {
        return 0;
    }


    if ($istirahat == 1) {
        if ($jam_out >= $jam_akhir_istirahat) {
            $j_pulang = $jam_out;
            $pengurang = 0;
        } elseif ($jam_out < $jam_awal_istirahat) {
            $j_pulang = $jam_out;
            $pengurang = 1;
        } else {
            $j_pulang = $jam_akhir_istirahat;
            $pengurang = 0;
        }
    } else {
        $j_pulang = $jam_out;
        $pengurang = 0;
    }


    if ($j_pulang < $jam_pulang) {
        $j1 = strtotime($j_pulang);
        $j2 = strtotime($jam_pulang);
        $diffpulangcepat = $j2 - $j1;

        $jam_pulangcepat = floor($diffpulangcepat / (60 * 60));
        $menit_pulangcepat = floor(($diffpulangcepat - $jam_pulangcepat * (60 * 60)) / 60);

        $jpulangcepat = $jam_pulangcepat <= 9 ? '0' . $jam_pulangcepat : $jam_pulangcepat;
        $mpulangcepat = $menit_pulangcepat <= 9 ? '0' . $menit_pulangcepat : $menit_pulangcepat;

        $keterangan_pulangcepat = $jpulangcepat . ':' . $mpulangcepat;
        $desimal_pulangcepat = $jam_pulangcepat +   ROUND(($menit_pulangcepat / 60), 2) - $pengurang;

        return $desimal_pulangcepat;
    } else {
        return 0;
    }
}
function hitungjamterlambat($jam_in, $jam_mulai)
{

    // $jam_in = date('Y-m-d H:i', strtotime($jam_in));
    // $jam_mulai = date('Y-m-d H:i', strtotime($jam_mulai));
    if (!empty($jam_in)) {
        if ($jam_in > $jam_mulai) {
            $j1 = strtotime($jam_mulai);
            $j2 = strtotime($jam_in);

            $diffterlambat = $j2 - $j1;

            $jamterlambat = floor($diffterlambat / (60 * 60));
            $menitterlambat = floor(($diffterlambat - $jamterlambat * (60 * 60)) / 60);

            $jterlambat = $jamterlambat <= 9 ? '0' . $jamterlambat : $jamterlambat;
            $mterlambat = $menitterlambat <= 9 ? '0' . $menitterlambat : $menitterlambat;

            $keterangan_terlambat =  $jterlambat . ':' . $mterlambat;
            $desimal_terlambat = $jamterlambat +   ROUND(($menitterlambat / 60), 2);


            // if ($jamterlambat < 1 && $menitterlambat <= 5) {
            //     $color_terlambat = 'text-success';
            //     $desimal_terlambat = 0;
            // } elseif ($jamterlambat < 1 && $menitterlambat > 5) {
            //     $color_terlambat = 'text-warning';
            //     $desimal_terlambat = 0;
            // } else {
            //     $color_terlambat = 'text-danger';
            //     $desimal_terlambat = $desimal_terlambat;
            // }

            $show = $desimal_terlambat < 1 ? $menitterlambat . " Menit" : formatAngkaDesimal($desimal_terlambat) . " Jam";
            return [
                'keterangan_terlambat' => $keterangan_terlambat,
                'jamterlambat' => $jamterlambat,
                'menitterlambat' => $menitterlambat,
                'desimal_terlambat' => $desimal_terlambat,
                'show' => '<span style="color:red">' . $show . '</span>',
                'show_laporan' => 'Telat :' . $show,
                'color' => 'red'
                // 'color_terlambat' => $color_terlambat
            ];
        } else {
            return [
                'menitterlambat' => 0,
                'desimal_terlambat' => 0,
                'color' => 'green',
                'show' => '<span style="color:green">Tepat Waktu</span>',
                'show_laporan' => 'Tepat Waktu'
            ];
        }
    } else {
        return null;
    }
}




function hitungJumlahHari($tanggal_awal, $tanggal_akhir)
{
    $start_date = Carbon::parse($tanggal_awal);
    $end_date = Carbon::parse($tanggal_akhir);

    $jumlah_hari = $start_date->diffInDays($end_date);

    return $jumlah_hari;
}


function getdatalibur($dari, $sampai)
{
    $libur = [];
    $ceklibur = Detailharilibur::select(
        'nik',
        'tanggal',
        'kode_cabang',
        'keterangan'
    )
        ->leftJoin('hari_libur', 'hari_libur_detail.kode_libur', '=', 'hari_libur.kode_libur')
        ->whereBetween('tanggal', [$dari, $sampai])
        ->get();

    foreach ($ceklibur as $d) {
        $libur[] = [
            'nik' => $d->nik,
            'kode_cabang' => $d->kode_cabang,
            'tanggal' => $d->tanggal,
            'keterangan' => $d->keterangan
        ];
    }

    return $libur;
}

function getdataliburIndexed($dari, $sampai)
{
    $ceklibur = Detailharilibur::select(
        'nik',
        'tanggal',
        'kode_cabang',
        'keterangan'
    )
        ->leftJoin('hari_libur', 'hari_libur_detail.kode_libur', '=', 'hari_libur.kode_libur')
        ->whereBetween('tanggal', [$dari, $sampai])
        ->get();

    $indexed = [];
    $by_tanggal = [];
    $raw = [];

    foreach ($ceklibur as $d) {
        $item = [
            'nik' => $d->nik,
            'kode_cabang' => $d->kode_cabang,
            'tanggal' => $d->tanggal,
            'keterangan' => $d->keterangan
        ];
        $raw[] = $item;

        if (!empty($d->nik)) {
            $indexed[$d->nik . '|' . $d->tanggal][] = $item;
        }
        $by_tanggal[$d->tanggal][] = $item;
    }

    return [
        'raw' => $raw,
        'indexed' => $indexed,
        'by_tanggal' => $by_tanggal
    ];
}

function ceklibur($array, $search_list)
{
    if (empty($array)) {
        return [];
    }

    // Fast O(1) path if array is indexed map and search has nik and tanggal
    if (isset($search_list['nik']) && isset($search_list['tanggal'])) {
        $key = $search_list['nik'] . '|' . $search_list['tanggal'];
        if (isset($array[$key])) {
            return $array[$key];
        }
    }

    // Fast O(1) path if search is only by tanggal
    if (isset($search_list['tanggal']) && count($search_list) === 1 && isset($array[$search_list['tanggal']])) {
        return $array[$search_list['tanggal']];
    }

    // Fallback: array search
    $result = array();

    foreach ($array as $key => $value) {
        if (!is_array($value)) {
            continue;
        }

        foreach ($search_list as $k => $v) {
            if (!isset($value[$k]) || $value[$k] != $v) {
                continue 2;
            }
        }

        $result[] = $value;
    }

    return $result;
}

function getHari($date)
{
    $days = array(
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    );
    $dayName = date('l', strtotime($date));
    return $days[$dayName];
}


function getNamabulan($bulan)
{
    $namabulan = array(
        '1' => 'Januari',
        '2' => 'Februari',
        '3' => 'Maret',
        '4' => 'April',
        '5' => 'Mei',
        '6' => 'Juni',
        '7' => 'Juli',
        '8' => 'Agustus',
        '9' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    );
    return $namabulan[$bulan];
}

function hitungJam($startDate, $endDate)
{
    if ($startDate && $endDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);

        // Tambahkan 1 detik agar penghitungan inklusif
        $timeDifference = $end->getTimestamp() - $start->getTimestamp() + 1;
        $hourDifference = $timeDifference / 3600;

        return $hourDifference;
    } else {
        return 0;
    }
}



function hitungSisaHari($endDate)
{
    $today = new DateTime(date('Y-m-d'));
    $end = new DateTime($endDate);
    $end->setTime(0, 0, 0);

    $interval = $today->diff($end);
    $daysRemaining = $interval->days;

    if ($today > $end) {
        $daysRemaining = -$daysRemaining;
    }

    return $daysRemaining;
}

function formatName($fullName)
{
    // Pisahkan string menjadi array kata-kata
    $words = explode(' ', $fullName);

    // Jika ada lebih dari 3 kata
    if (count($words) >= 3) {
        // Ambil dua kata pertama
        $firstTwoWords = array_slice($words, 0, 2);

        // Ambil huruf pertama dari setiap kata setelah dua kata pertama
        $initials = array_map(function ($word) {
            return strtoupper($word[0]);
        }, array_slice($words, 2));

        // Gabungkan dua kata pertama dengan inisial-inisial
        $formattedName = implode(' ', $firstTwoWords) . ' ' . implode('', $initials);
    } else {
        // Jika tidak lebih dari 3 kata, gunakan nama asli
        $formattedName = $fullName;
    }

    return $formattedName;
}

function singkatString($string)
{
    $words = explode(' ', $string);

    // Jika string terdiri dari tepat 3 kata, buat singkatan huruf besar
    if (count($words) === 3) {
        $abbreviation = '';

        foreach ($words as $word) {
            if (strlen($word) >= 3) {
                $abbreviation .= strtoupper($word[0]);
            }
        }

        return $abbreviation;
    }

    // Jika tidak, buat camelCase
    return ucwords(strtolower($string));
}



/**
 * Calculate excess break time deduction.
 * Comparison between actual break duration and scheduled duration.
 * @param string $start_break Time when employee goes out for break (e.g., istirahat_out)
 * @param string $end_break Time when employee comes back from break (e.g., istirahat_in)
 */
function hitungPotonganIstirahat($start_break, $end_break, $jam_awal_istirahat, $jam_akhir_istirahat)
{
    if (!empty($start_break) && !empty($end_break)) {
        $awal = strtotime($start_break);
        $akhir = strtotime($end_break);
        $durasi_riil = $akhir - $awal;

        // Use the date from start_break to build the scheduled timestamps
        $tgl = date('Y-m-d', $awal);
        $awal_skd = strtotime($tgl . ' ' . $jam_awal_istirahat);
        $akhir_skd = strtotime($tgl . ' ' . $jam_akhir_istirahat);
        
        // Handle if scheduled break ends on next day (rare but possible)
        if ($akhir_skd < $awal_skd) {
             $akhir_skd = strtotime($tgl . ' ' . $jam_akhir_istirahat . ' +1 day');
        }

        $durasi_skd = $akhir_skd - $awal_skd;

        if ($durasi_riil > $durasi_skd) {
            $selisih = $durasi_riil - $durasi_skd;
            $jam = floor($selisih / 3600);
            $menit = floor(($selisih % 3600) / 60);
            $desimal = $jam + round($menit / 60, 2);
            return $desimal;
        }
    }
    return 0;
}



