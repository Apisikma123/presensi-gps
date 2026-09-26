<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;

$dir = storage_path('app/public/uploads/karyawan');
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

// Generate distinct avatar images
function generateAvatar($name, $nik) {
    $im = imagecreatetruecolor(160, 160);
    
    // Choose pleasant colors based on hash of NIK
    $hash = md5($nik);
    $r = hexdec(substr($hash, 0, 2));
    $g = hexdec(substr($hash, 2, 2));
    $b = hexdec(substr($hash, 4, 2));
    
    // Ensure pleasant contrast
    $bgColor = imagecolorallocate($im, max(30, min(180, $r)), max(50, min(180, $g)), max(60, min(180, $b)));
    $white = imagecolorallocate($im, 255, 255, 255);
    $accent = imagecolorallocate($im, 240, 240, 240);
    
    imagefill($im, 0, 0, $bgColor);
    
    // Draw simple stylized head/body avatar silhouette
    imagefilledellipse($im, 80, 55, 50, 50, $white);
    imagefilledarc($im, 80, 140, 90, 80, 180, 360, $white, IMG_ARC_PIE);
    
    // Draw initials
    $initials = strtoupper(substr($name, 0, 1));
    imagestring($im, 5, 75, 47, $initials, $bgColor);
    
    ob_start();
    imagejpeg($im, null, 80);
    $data = ob_get_clean();
    imagedestroy($im);
    return $data;
}

$karyawans = Karyawan::where('status_aktif_karyawan', 1)->get();
echo "Generating avatars for " . $karyawans->count() . " karyawan...\n";

foreach ($karyawans as $k) {
    $filename = "avatar_{$k->nik}.jpg";
    file_put_contents("$dir/$filename", generateAvatar($k->nama_karyawan, $k->nik));
    $k->foto = $filename;
    $k->save();
}

echo "SUCCESS: Avatars generated and assigned to all employees.\n";
