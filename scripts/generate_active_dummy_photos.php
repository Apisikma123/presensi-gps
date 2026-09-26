<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Karyawan;
use App\Models\Presensi;
use Illuminate\Support\Facades\DB;

$uploadDir = storage_path('app/public/uploads/absensi');
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Generate a clean dummy avatar image buffer (200x200 JPEG with text)
function createDummyImage($text) {
    $im = imagecreatetruecolor(240, 240);
    $bg = imagecolorallocate($im, 60, 42, 33); // #3C2A21 Espresso Deep (DESIGN.md)
    $white = imagecolorallocate($im, 255, 255, 255);
    $yellow = imagecolorallocate($im, 245, 158, 11);
    
    imagefill($im, 0, 0, $bg);
    imagestring($im, 5, 20, 80, "PRESENSI DUMMY", $yellow);
    imagestring($im, 4, 20, 110, $text, $white);
    imagestring($im, 3, 20, 140, date('Y-m-d H:i:s'), $white);
    
    ob_start();
    imagejpeg($im, null, 75);
    $data = ob_get_clean();
    imagedestroy($im);
    return $data;
}

$sampleImage = createDummyImage("SAMPLE ATTENDANCE");

$karyawans = Karyawan::where('status_aktif_karyawan', 1)->get();
$totalKaryawan = $karyawans->count();

if ($totalKaryawan === 0) {
    echo "No active karyawan found.\n";
    exit(1);
}

$targetPhotos = 10000;
echo "Generating $targetPhotos active dummy attendance photos & records for current dates...\n";

$daysToGenerate = (int) ceil($targetPhotos / ($totalKaryawan * 2)); // in & out
$today = Carbon\Carbon::today();

$createdPhotos = 0;
$records = [];
$batchSize = 500;

for ($dayOffset = 0; $dayOffset <= $daysToGenerate; $dayOffset++) {
    $dateObj = $today->copy()->subDays($dayOffset);
    $tanggal = $dateObj->format('Y-m-d');
    
    // Clean existing records on these dates
    Presensi::where('tanggal', $tanggal)->delete();

    foreach ($karyawans as $k) {
        if ($createdPhotos >= $targetPhotos) {
            break 2;
        }

        $timeIn = sprintf('%02d:%02d:%02d', rand(6, 8), rand(0, 59), rand(0, 59));
        $timeOut = sprintf('%02d:%02d:%02d', rand(16, 18), rand(0, 59), rand(0, 59));

        $fotoIn = "dummy_{$k->nik}_{$tanggal}_in.jpg";
        $fotoOut = "dummy_{$k->nik}_{$tanggal}_out.jpg";

        file_put_contents("$uploadDir/$fotoIn", $sampleImage);
        $createdPhotos++;

        if ($createdPhotos < $targetPhotos) {
            file_put_contents("$uploadDir/$fotoOut", $sampleImage);
            $createdPhotos++;
            $hasOut = true;
        } else {
            $fotoOut = null;
            $hasOut = false;
        }

        $records[] = [
            'nik' => $k->nik,
            'tanggal' => $tanggal,
            'jam_in' => "$tanggal $timeIn",
            'jam_out' => $hasOut ? "$tanggal $timeOut" : null,
            'foto_in' => $fotoIn,
            'foto_out' => $fotoOut,
            'lokasi_in' => '-6.200000,106.816666',
            'lokasi_out' => $hasOut ? '-6.200000,106.816666' : null,
            'kode_jam_kerja' => 'JK01',
            'kode_cabang' => $k->kode_cabang ?? 'PST',
            'status' => 'h',
            'is_archived' => 0,
            'foto_in_archived' => 0,
            'foto_out_archived' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (count($records) >= $batchSize) {
            Presensi::insert($records);
            $records = [];
            echo "Created $createdPhotos / $targetPhotos photos...\n";
        }
    }
}

if (!empty($records)) {
    Presensi::insert($records);
}

echo "SUCCESS: Created $createdPhotos dummy attendance photos in $uploadDir and associated database records for view.\n";
