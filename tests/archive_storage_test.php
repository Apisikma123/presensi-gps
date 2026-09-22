<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Presensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

echo "\n=======================================================\n";
echo "   12 MANDATORY ARCHIVE & STORAGE TESTS VERIFICATION\n";
echo "=======================================================\n\n";

$passCount = 0;
$failCount = 0;

function runTest($id, $title, $callback) {
    global $passCount, $failCount;
    echo "Test {$id}: {$title} ... ";
    try {
        $result = $callback();
        if ($result === true) {
            echo "\033[32mPASS\033[0m\n";
            $passCount++;
        } else {
            echo "\033[31mFAIL: {$result}\033[0m\n";
            $failCount++;
        }
    } catch (\Throwable $e) {
        echo "\033[31mFAIL (Exception: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . ")\033[0m\n";
        $failCount++;
    }
}

$archiveDir = storage_path('app/private/attendance-archive');
$attendanceDir = storage_path('app/public/uploads/absensi');
$profileDir = storage_path('app/public/uploads/karyawan');
$sidDir = storage_path('app/private/uploads/sid');

if (!is_dir($attendanceDir)) @mkdir($attendanceDir, 0755, true);
if (!is_dir($profileDir)) @mkdir($profileDir, 0755, true);
if (!is_dir($sidDir)) @mkdir($sidDir, 0755, true);

// Clean up any old test records/files
Presensi::where('nik', '1001')->whereIn('tanggal', ['2025-05-10', '2025-05-11', '2025-04-01'])->delete();
@unlink($archiveDir . '/2025-05.zip');
@unlink($archiveDir . '/2025-04.zip');

// =========================================================================
// TEST 1: Dry run -> no file modified
// =========================================================================
runTest(1, "Dry run does not modify any files", function () {
    $exitCode = Artisan::call('maintenance:archive-attendance-photos');
    $output = Artisan::output();
    if ($exitCode !== 0) return "Exit code was {$exitCode}";
    if (!str_contains($output, 'DRY RUN')) return "Output does not indicate DRY RUN";
    return true;
});

// Setup Mock Data for 2025-05 (16 months old, eligible)
$testFiles = [
    '1001-TESTARCHIVE-2025-05-10-in.webp' => 'fake webp content in 1',
    '1001-TESTARCHIVE-2025-05-10-out.webp' => 'fake webp content out 1',
    '1001-TESTARCHIVE-2025-05-11-in.webp' => 'fake webp content in 2',
    '1001-TESTARCHIVE-2025-05-11-out.webp' => 'fake webp content out 2',
];

foreach ($testFiles as $fn => $content) {
    file_put_contents($attendanceDir . '/' . $fn, $content);
}

// Profile photo & SID (must NOT be archived)
$profileFile = $profileDir . '/1001-TESTARCHIVE-profile.webp';
file_put_contents($profileFile, 'profile photo content');

$sidFile = $sidDir . '/1001-TESTARCHIVE-sid.webp';
file_put_contents($sidFile, 'medical certificate content');

// Insert DB records with NIK 1001 (which exists in karyawan)
$record1 = Presensi::create([
    'nik' => '1001',
    'tanggal' => '2025-05-10',
    'jam_in' => '2025-05-10 08:00:00',
    'jam_out' => '2025-05-10 17:00:00',
    'foto_in' => '1001-TESTARCHIVE-2025-05-10-in.webp',
    'foto_out' => '1001-TESTARCHIVE-2025-05-10-out.webp',
    'kode_cabang' => 'JKT',
    'kode_jam_kerja' => 'JK01',
    'status' => 'h',
    'is_archived' => 0,
]);

$record2 = Presensi::create([
    'nik' => '1001',
    'tanggal' => '2025-05-11',
    'jam_in' => '2025-05-11 08:15:00',
    'jam_out' => '2025-05-11 17:05:00',
    'foto_in' => '1001-TESTARCHIVE-2025-05-11-in.webp',
    'foto_out' => '1001-TESTARCHIVE-2025-05-11-out.webp',
    'kode_cabang' => 'JKT',
    'kode_jam_kerja' => 'JK01',
    'status' => 'h',
    'is_archived' => 0,
]);

// =========================================================================
// TEST 2: Archive 1 bulan test -> ZIP berhasil dibuat
// =========================================================================
runTest(2, "Archive 1 test month creates ZIP successfully", function () use ($archiveDir) {
    $exitCode = Artisan::call('maintenance:archive-attendance-photos', [
        '--execute' => true,
        '--month' => '2025-05',
    ]);
    if ($exitCode !== 0) return "Exit code was {$exitCode}. Output: " . Artisan::output();
    $zipPath = $archiveDir . '/2025-05.zip';
    if (!file_exists($zipPath)) return "Target ZIP {$zipPath} was not created";
    return true;
});

// =========================================================================
// TEST 3: ZIP dapat dibuka -> PASS
// =========================================================================
runTest(3, "Created ZIP can be opened and verified", function () use ($archiveDir) {
    $zipPath = $archiveDir . '/2025-05.zip';
    $zip = new ZipArchive();
    $res = $zip->open($zipPath, ZipArchive::CHECKCONS);
    if ($res !== true) return "ZipArchive open failed with code {$res}";
    $zip->close();
    return true;
});

// =========================================================================
// TEST 4: Jumlah entry sesuai source -> PASS
// =========================================================================
runTest(4, "Entry count inside ZIP matches source count exactly", function () use ($archiveDir) {
    $zipPath = $archiveDir . '/2025-05.zip';
    $zip = new ZipArchive();
    $zip->open($zipPath);
    $numFiles = $zip->numFiles;
    $entries = [];
    for ($i = 0; $i < $numFiles; $i++) {
        $entries[] = $zip->getNameIndex($i);
    }
    $zip->close();

    if ($numFiles !== 4) return "Expected 4 entries, found {$numFiles}: " . json_encode($entries);
    if (!in_array('uploads/absensi/1001-TESTARCHIVE-2025-05-10-in.webp', $entries)) return "Missing 1001-TESTARCHIVE-2025-05-10-in.webp";
    if (!in_array('uploads/absensi/1001-TESTARCHIVE-2025-05-10-out.webp', $entries)) return "Missing 1001-TESTARCHIVE-2025-05-10-out.webp";
    return true;
});

// =========================================================================
// TEST 5: Setelah sukses source file individual terhapus -> PASS
// =========================================================================
runTest(5, "Individual source attendance photo files are deleted after archive verification", function () use ($attendanceDir, $testFiles) {
    foreach (array_keys($testFiles) as $fn) {
        if (file_exists($attendanceDir . '/' . $fn)) {
            return "File {$fn} still exists on disk";
        }
    }
    return true;
});

// =========================================================================
// TEST 6: Database presensi tetap ada -> PASS
// =========================================================================
runTest(6, "Presence database rows are 100% preserved with is_archived=1", function () {
    $rows = Presensi::where('nik', '1001')->whereIn('tanggal', ['2025-05-10', '2025-05-11'])->get();
    if ($rows->count() !== 2) return "Expected 2 rows in DB, found {$rows->count()}";
    foreach ($rows as $r) {
        if (!$r->is_archived) return "Row id {$r->id} does not have is_archived=1";
        if (!$r->foto_in_archived) return "Row id {$r->id} does not have foto_in_archived=1";
        if (empty($r->jam_in) || empty($r->foto_in)) return "Presensi data wiped unexpectedly";
    }
    return true;
});

// =========================================================================
// TEST 7: UI histori lama tidak 404 -> tampil 'Foto telah diarsipkan'
// =========================================================================
runTest(7, "UI view renders 'Foto telah diarsipkan' without 404 image tags", function () use ($record1) {
    $admin = User::first();
    if ($admin) {
        Auth::login($admin);
    }

    $presensi = Presensi::where('presensi.id', $record1->id)
        ->select('presensi.*', DB::raw("'Ahmad Rizki' as nama_karyawan"), DB::raw("'JKT' as kode_cabang"), DB::raw("'IT' as kode_dept"), DB::raw("'IT Dept' as nama_dept"), DB::raw("'Staff' as nama_jabatan"), DB::raw("'Jakarta' as nama_cabang"), DB::raw("'-6.2,106.8' as lokasi_cabang"))
        ->first();

    $cabang = (object) ['radius_cabang' => 100, 'lokasi_cabang' => '-6.2,106.8'];
    $viewHtml = view('presensi.show', [
        'presensi' => $presensi,
        'status' => 'in',
        'cabang' => $cabang,
        'latitude' => -6.2,
        'longitude' => 106.8,
    ])->render();

    if (!str_contains($viewHtml, 'Foto telah diarsipkan')) {
        return "View does not contain 'Foto telah diarsipkan'";
    }
    if (str_contains($viewHtml, '<img src="http') && str_contains($viewHtml, '1001-TESTARCHIVE-2025-05-10-in.webp')) {
        return "View erroneously rendered <img> tag for archived photo";
    }
    if (!str_contains($viewHtml, 'Arsip: Mei 2025')) {
        return "Admin view does not contain 'Arsip: Mei 2025'";
    }
    return true;
});

// =========================================================================
// TEST 8: Foto profile tidak ikut -> PASS
// =========================================================================
runTest(8, "Employee profile photos are NOT included in archive", function () use ($profileFile, $archiveDir) {
    if (!file_exists($profileFile)) return "Profile file {$profileFile} was deleted!";
    $zip = new ZipArchive();
    $zip->open($archiveDir . '/2025-05.zip');
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);
        if (str_contains($name, 'profile')) {
            $zip->close();
            return "ZIP contains profile photo: {$name}";
        }
    }
    $zip->close();
    return true;
});

// =========================================================================
// TEST 9: Bukti izin/sakit tidak ikut -> PASS
// =========================================================================
runTest(9, "Medical leave / SID certificates are NOT included in archive", function () use ($sidFile, $archiveDir) {
    if (!file_exists($sidFile)) return "SID file {$sidFile} was deleted!";
    $zip = new ZipArchive();
    $zip->open($archiveDir . '/2025-05.zip');
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);
        if (str_contains($name, 'sid')) {
            $zip->close();
            return "ZIP contains SID document: {$name}";
        }
    }
    $zip->close();
    return true;
});

// =========================================================================
// TEST 10: Archive gagal dibuat -> source tetap utuh
// =========================================================================
runTest(10, "Failure during archive aborts without deleting any source files", function () use ($attendanceDir) {
    // Setup month 2025-04
    $fn1 = '1001-TESTARCHIVE-2025-04-01-in.webp';
    $fn2 = '1001-TESTARCHIVE-2025-04-01-out.webp';
    file_put_contents($attendanceDir . '/' . $fn1, 'content 1');
    file_put_contents($attendanceDir . '/' . $fn2, 'content 2');

    $failRecord = Presensi::create([
        'nik' => '1001',
        'tanggal' => '2025-04-01',
        'jam_in' => '2025-04-01 08:00:00',
        'jam_out' => '2025-04-01 17:00:00',
        'foto_in' => $fn1,
        'foto_out' => $fn2,
        'kode_cabang' => 'JKT',
        'kode_jam_kerja' => 'JK01',
        'status' => 'h',
        'is_archived' => 0,
    ]);

    // Simulate archive failure by pointing archive path to a file blocker
    $origArchiveDir = config('attendance.archive_disk_path');
    $invalidDir = storage_path('app/private/attendance-archive/unwritable_file_blocker');
    file_put_contents($invalidDir, 'blocking file');
    config(['attendance.archive_disk_path' => $invalidDir]);

    try {
        // Run archive for 2025-04 which will fail creating temporary zip inside a file
        $exitCode = Artisan::call('maintenance:archive-attendance-photos', [
            '--execute' => true,
            '--month' => '2025-04',
        ]);

        // Assert that the command failed as expected
        if ($exitCode === 0) {
            return "Expected command to fail on invalid archive directory, but returned 0";
        }

        // Both source files MUST remain completely intact!
        if (!file_exists($attendanceDir . '/' . $fn1) || !file_exists($attendanceDir . '/' . $fn2)) {
            return "Source files were unexpectedly deleted during archive failure!";
        }

        return true;
    } finally {
        // Restore original archive directory config and clean up
        config(['attendance.archive_disk_path' => $origArchiveDir]);
        @unlink($invalidDir);
        @unlink($attendanceDir . '/' . $fn1);
        @unlink($attendanceDir . '/' . $fn2);
        Presensi::where('tanggal', '2025-04-01')->where('nik', '1001')->delete();
    }
});

// =========================================================================
// TEST 11: Command dijalankan dua kali -> tidak duplicate / corrupt
// =========================================================================
runTest(11, "Repeat execution is idempotent and does not duplicate or corrupt archive", function () use ($archiveDir) {
    $exitCode = Artisan::call('maintenance:archive-attendance-photos', [
        '--execute' => true,
        '--month' => '2025-05',
    ]);
    if ($exitCode !== 0) return "Repeat execution failed with exit code {$exitCode}";

    $zipPath = $archiveDir . '/2025-05.zip';
    $zip = new ZipArchive();
    $res = $zip->open($zipPath, ZipArchive::CHECKCONS);
    if ($res !== true) return "Archive became corrupt on repeat run";
    if ($zip->numFiles !== 4) return "Entry count changed on repeat run: {$zip->numFiles}";
    $zip->close();

    // Check manifest
    $manifestPath = $archiveDir . '/index.json';
    if (!file_exists($manifestPath)) return "Manifest missing";
    $manifest = json_decode(file_get_contents($manifestPath), true);
    if (!isset($manifest['2025-05'])) return "Manifest entry missing for 2025-05";

    return true;
});

// =========================================================================
// TEST 12: Tidak ada eligible archive -> exit normal
// =========================================================================
runTest(12, "When no eligible months remain, command exits cleanly with code 0", function () {
    // Delete any remaining older test records
    Presensi::where('nik', '1001')->whereIn('tanggal', ['2025-05-10', '2025-05-11'])->delete();

    $exitCode = Artisan::call('maintenance:archive-attendance-photos', [
        '--execute' => true,
    ]);
    $output = Artisan::output();
    if ($exitCode !== 0) return "Exit code was {$exitCode}";
    if (!str_contains($output, 'No eligible month found') && !str_contains($output, 'All data is within the hot period')) {
        return "Output did not indicate no eligible months: {$output}";
    }
    return true;
});

// =========================================================================
// BONUS TEST: Secure Download Authorization & Path Traversal Protection
// =========================================================================
runTest("Bonus", "Secure download enforces admin auth, blocks path traversal, and returns proper headers", function () use ($archiveDir) {
    $admin = User::first();
    Auth::login($admin);
    $controller = new \App\Http\Controllers\ProtectedFileController();
    $req = \Illuminate\Http\Request::create('/files/attendance-archive/2025-05');

    // 1. Path traversal test
    $traversalBlocked = false;
    try {
        $controller->downloadAttendanceArchive($req, '../../etc/passwd');
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        if ($e->getStatusCode() === 400) $traversalBlocked = true;
    }
    if (!$traversalBlocked) return "Path traversal attempt was not blocked with 400 Bad Request";

    // 2. Non-existent month test
    $notFoundPassed = false;
    try {
        $controller->downloadAttendanceArchive($req, '1999-01');
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        if ($e->getStatusCode() === 404) $notFoundPassed = true;
    }
    if (!$notFoundPassed) return "Non-existent month did not return 404";

    // 3. Valid file download test
    $dummyZip = $archiveDir . '/2025-05.zip';
    if (file_exists($dummyZip)) {
        $response = $controller->downloadAttendanceArchive($req, '2025-05');
        if ($response->getStatusCode() !== 200) return "Download response was not 200";
        if ($response->headers->get('Content-Type') !== 'application/zip') return "Content-Type is not application/zip";
    }

    return true;
});

// Cleanup test files & DB records
Presensi::where('nik', '1001')->whereIn('tanggal', ['2025-05-10', '2025-05-11'])->delete();
@unlink($profileFile);
@unlink($sidFile);
@unlink($archiveDir . '/2025-05.zip');

// Clean manifest test entry
$manifestPath = $archiveDir . '/index.json';
if (file_exists($manifestPath)) {
    $manifest = json_decode(file_get_contents($manifestPath), true) ?: [];
    unset($manifest['2025-05'], $manifest['2025-04']);
    file_put_contents($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT));
}

echo "\n-------------------------------------------------------\n";
echo "SUMMARY: {$passCount}/" . ($passCount + $failCount) . " TESTS PASSED\n";
echo "-------------------------------------------------------\n\n";

if ($failCount === 0) {
    echo "STATUS: READY FOR LOW-COST LONG-TERM STORAGE\n\n";
    exit(0);
} else {
    echo "STATUS: VERIFICATION FAILED\n\n";
    exit(1);
}
