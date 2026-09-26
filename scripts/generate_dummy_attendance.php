<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Karyawan;
use App\Models\Presensi;

$uploadDir = storage_path('app/public/uploads/absensi');
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Minimal valid 1x1 JPEG (107 bytes)
$tinyJpg = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');

$niks = Karyawan::pluck('nik')->toArray();
if (empty($niks)) {
    $niks = ['DUMMY001'];
}

$targetMonth = '2024-01'; // Eligible month for archiving test
echo "Cleanup existing dummy data for $targetMonth...\n";
Presensi::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$targetMonth])->delete();

$targetPhotos = 10000;
echo "Generating $targetPhotos dummy attendance records & photos for $targetMonth...\n";

$records = [];
$batchSize = 1000;
$photoIndex = 0;

$days = 31;
$nikIndex = 0;
$dayIndex = 1;

for ($i = 1; $i <= $targetPhotos; $i++) {
    $nik = $niks[$nikIndex % count($niks)];
    $day = str_pad($dayIndex, 2, '0', STR_PAD_LEFT);
    $tanggal = "$targetMonth-$day";

    // Advance index
    $nikIndex++;
    if ($nikIndex % count($niks) === 0) {
        $dayIndex++;
        if ($dayIndex > $days) {
            $dayIndex = 1;
        }
    }

    $timeIn = sprintf('%02d:%02d:%02d', rand(7, 9), rand(0, 59), rand(0, 59));
    $fotoIn = "dummy_{$targetMonth}_{$i}_in.jpg";

    // Write physical photo
    file_put_contents("$uploadDir/$fotoIn", $tinyJpg);

    $records[] = [
        'nik' => $nik,
        'tanggal' => $tanggal,
        'jam_in' => "$tanggal $timeIn",
        'jam_out' => null,
        'foto_in' => $fotoIn,
        'foto_out' => null,
        'lokasi_in' => '-6.200000,106.816666',
        'lokasi_out' => null,
        'kode_jam_kerja' => 'JK01',
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
        echo "Inserted $i / $targetPhotos records...\n";
    }
}

if (!empty($records)) {
    Presensi::insert($records);
}

echo "SUCCESS: 10,000 photos generated in $uploadDir and 10,000 records in database for $targetMonth!\n";
