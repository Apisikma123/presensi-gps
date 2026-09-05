<?php

use App\Http\Controllers\CabangController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\DispensasiController;
use App\Http\Controllers\FacerecognitionController;
use App\Http\Controllers\GeneralsettingController;
use App\Http\Controllers\PengajuanizinController;
use App\Http\Controllers\IzinabsenController;
use App\Http\Controllers\IzincutiController;
use App\Http\Controllers\IzinsakitController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\JamkerjaController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\Permission_groupController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrackingPresensiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BackupController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Whitelist - Final Scope Lock)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        $agent = new \Jenssegers\Agent\Agent();
        if ($agent->isMobile()) {
            return view('auth.loginusermobile');
        }
        return view('auth.loginuser');
    })->name('loginuser');

    Route::get('/loginuser', function () {
        return redirect()->route('loginuser');
    });
});

Route::middleware('auth')->group(function () {

    // Profile
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'index')->name('profile.index');
        Route::put('/profile', 'update')->name('profile.update');
        Route::get('/profile/editprofile', 'editprofile')->name('profile.editprofile');
        Route::post('/profile/updateprofile', 'updateprofile')->name('profile.updateprofile');
    });

    // Dashboard
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard.index');
        Route::post('/dashboard/get-karyawan-presensi', 'getKaryawanPresensi')->name('dashboard.get.karyawan.presensi');
        Route::get('/dashboard/global-search', 'globalSearch')->name('dashboard.global-search');
    });

    // Manajemen Karyawan (Attendance Focused)
    Route::controller(KaryawanController::class)->group(function () {
        Route::get('/karyawan', 'index')->name('karyawan.index')->can('karyawan.index');
        Route::get('/karyawan/create', 'create')->name('karyawan.create')->can('karyawan.create');
        Route::post('/karyawan', 'store')->name('karyawan.store')->can('karyawan.create');
        Route::get('/karyawan/import', 'import')->name('karyawan.import')->can('karyawan.create');
        Route::get('/karyawan/download-template', 'download_template')->name('karyawan.download_template')->can('karyawan.create');
        Route::get('/karyawan/export', 'export')->name('karyawan.export')->can('karyawan.index');
        Route::post('/karyawan/import', 'import_proses')->name('karyawan.import_proses')->can('karyawan.create');
        Route::get('/karyawan/{nik}/edit', 'edit')->name('karyawan.edit')->can('karyawan.edit');
        Route::put('/karyawan/{nik}', 'update')->name('karyawan.update')->can('karyawan.edit');
        Route::delete('/karyawan/{nik}', 'destroy')->name('karyawan.delete')->can('karyawan.delete');
        Route::get('/karyawan/{nik}/show', 'show')->name('karyawan.show')->can('karyawan.show');

        // Akun & Security
        Route::get('/karyawan/{nik}/createuser', 'createuser')->name('karyawan.createuser')->can('users.create');
        Route::get('/karyawan/{nik}/deleteuser', 'deleteuser')->name('karyawan.deleteuser')->can('users.create');
        Route::get('/karyawan/{nik}/lockunlocklocation', 'lockunlocklocation')->name('karyawan.lockunlocklocation')->can('karyawan.edit');
        Route::get('/karyawan/{nik}/lockunlockjamkerja', 'lockunlockjamkerja')->name('karyawan.lockunlockjamkerja')->can('karyawan.edit');

        Route::get('/karyawan/getkaryawan', 'getkaryawan')->name('karyawan.getkaryawan');
        Route::get('/karyawan/getkaryawantable', 'getkaryawantable')->name('karyawan.getkaryawantable');
    });

    // Datamaster: Cabang / Outlet
    Route::controller(CabangController::class)->group(function () {
        Route::get('/cabang', 'index')->name('cabang.index')->can('cabang.index');
        Route::get('/cabang/create', 'create')->name('cabang.create')->can('cabang.create');
        Route::post('/cabang', 'store')->name('cabang.store')->can('cabang.create');
        Route::get('/cabang/{kode_cabang}', 'edit')->name('cabang.edit')->can('cabang.edit');
        Route::put('/cabang/{kode_cabang}', 'update')->name('cabang.update')->can('cabang.edit');
        Route::delete('/cabang/{kode_cabang}/delete', 'destroy')->name('cabang.delete')->can('cabang.delete');
    });

    // Datamaster: Departemen
    Route::controller(DepartemenController::class)->group(function () {
        Route::get('/departemen', 'index')->name('departemen.index')->can('departemen.index');
        Route::get('/departemen/create', 'create')->name('departemen.create')->can('departemen.create');
        Route::post('/departemen', 'store')->name('departemen.store')->can('departemen.create');
        Route::get('/departemen/{nik}', 'edit')->name('departemen.edit')->can('departemen.edit');
        Route::put('/departemen/{nik}', 'update')->name('departemen.update')->can('departemen.edit');
        Route::delete('/departemen/{nik}/delete', 'destroy')->name('departemen.delete')->can('departemen.delete');
    });

    // Datamaster: Jabatan
    Route::controller(JabatanController::class)->group(function () {
        Route::get('/jabatan', 'index')->name('jabatan.index')->can('jabatan.index');
        Route::get('/jabatan/create', 'create')->name('jabatan.create')->can('jabatan.create');
        Route::post('/jabatan', 'store')->name('jabatan.store')->can('jabatan.create');
        Route::get('/jabatan/{kode_jabatan}', 'edit')->name('jabatan.edit')->can('jabatan.edit');
        Route::put('/jabatan/{kode_jabatan}', 'update')->name('jabatan.update')->can('jabatan.edit');
        Route::delete('/jabatan/{kode_jabatan}/delete', 'destroy')->name('jabatan.delete')->can('jabatan.delete');
    });

    // Datamaster: Cuti
    Route::controller(CutiController::class)->group(function () {
        Route::get('/cuti', 'index')->name('cuti.index')->can('cuti.index');
        Route::get('/cuti/create', 'create')->name('cuti.create')->can('cuti.create');
        Route::post('/cuti', 'store')->name('cuti.store')->can('cuti.create');
        Route::get('/cuti/{kode_cuti}', 'edit')->name('cuti.edit')->can('cuti.edit');
        Route::put('/cuti/{kode_cuti}', 'update')->name('cuti.update')->can('cuti.edit');
        Route::delete('/cuti/{kode_cuti}/delete', 'destroy')->name('cuti.delete')->can('cuti.delete');
    });

    // Datamaster: Jam Kerja (Shift Pagi & Siang)
    Route::controller(JamkerjaController::class)->group(function () {
        Route::get('/jamkerja', 'index')->name('jamkerja.index')->can('jamkerja.index');
        Route::get('/jamkerja/create', 'create')->name('jamkerja.create')->can('jamkerja.create');
        Route::post('/jamkerja', 'store')->name('jamkerja.store')->can('jamkerja.create');
        Route::get('/jamkerja/{kode_jam_kerja}/edit', 'edit')->name('jamkerja.edit')->can('jamkerja.edit');
        Route::put('/jamkerja/{kode_jam_kerja}/update', 'update')->name('jamkerja.update')->can('jamkerja.edit');
        Route::delete('/jamkerja/{kode_jam_kerja}/delete', 'destroy')->name('jamkerja.delete')->can('jamkerja.delete');
    });

    // Presensi Kehadiran & Monitoring
    Route::controller(PresensiController::class)->group(function () {
        Route::get('/presensi', 'index')->name('presensi.index')->can('presensi.index');
        Route::get('/presensi/histori', 'histori')->name('presensi.histori');
        Route::get('/presensi/create', 'create')->name('presensi.create');
        Route::post('/presensi', 'store')->name('presensi.store');
        Route::post('/presensi/edit', 'edit')->name('presensi.edit')->can('presensi.edit');
        Route::post('/presensi/update', 'update')->name('presensi.update')->can('presensi.edit');
        Route::delete('/presensi/{id}/delete', 'destroy')->name('presensi.delete')->can('presensi.delete');
        Route::get('/presensi/{id}/{status}/show', 'show')->name('presensi.show');
    });

    // Tracking Presensi GPS
    Route::middleware('permission:trackingpresensi.index')->controller(TrackingPresensiController::class)->group(function () {
        Route::get('/trackingpresensi', 'index')->name('trackingpresensi.index');
        Route::get('/trackingpresensi/getData', 'getData')->name('trackingpresensi.getData');
    });

    // Face Recognition (Biometrik Wajah)
    Route::controller(FacerecognitionController::class)->group(function () {
        Route::post('/facerecognition/hapus-semua/{nik}', 'destroyAll')->name('facerecognition.destroyAll')->can('karyawan.edit');
        Route::get('/facerecognition/{nik}/create', 'create')->name('facerecognition.create');
        Route::get('/karyawan/daftarkan-wajah', 'createKaryawan')->name('facerecognition.karyawan.create');
        Route::get('/karyawan/preview-wajah', 'previewKaryawan')->name('facerecognition.karyawan.preview');
        Route::post('/karyawan/hapus-wajah', 'destroyAllKaryawan')->name('facerecognition.karyawan.destroyAll');
        Route::post('/facerecognition/store', 'store')->name('facerecognition.store');
        Route::delete('/facerecognition/{id}/delete', 'destroy')->name('facerecognition.delete');
        Route::get('/facerecognition/getwajah', 'getWajah')->name('facerecognition.getwajah');
    });

    // Pengajuan Izin Absen
    Route::controller(IzinabsenController::class)->group(function () {
        Route::get('/izinabsen', 'index')->name('izinabsen.index')->can('izinabsen.index');
        Route::get('/izinabsen/create', 'create')->name('izinabsen.create')->can('izinabsen.create');
        Route::post('/izinabsen', 'store')->name('izinabsen.store')->can('izinabsen.create');
        Route::get('/izinabsen/{kode_izin}/approve', 'approve')->name('izinabsen.approve')->can('izinabsen.approve');
        Route::delete('/izinabsen/{kode_izin}/cancelapprove', 'cancelapprove')->name('izinabsen.cancelapprove')->can('izinabsen.approve');
        Route::post('/izinabsen/{kode_izin}/storeapprove', 'storeapprove')->name('izinabsen.storeapprove')->can('izinabsen.approve');
        Route::get('/izinabsen/{id}/edit', 'edit')->name('izinabsen.edit')->can('izinabsen.edit');
        Route::put('/izinabsen/{id}', 'update')->name('izinabsen.update')->can('izinabsen.edit');
        Route::get('/izinabsen/{kode_izin}/show', 'show')->name('izinabsen.show')->can('izinabsen.index');
        Route::delete('/izinabsen/{id}/delete', 'destroy')->name('izinabsen.delete')->can('izinabsen.delete');
    });

    // Pengajuan Izin Sakit
    Route::controller(IzinsakitController::class)->group(function () {
        Route::get('/izinsakit', 'index')->name('izinsakit.index')->can('izinsakit.index');
        Route::get('/izinsakit/create', 'create')->name('izinsakit.create')->can('izinsakit.create');
        Route::post('/izinsakit', 'store')->name('izinsakit.store')->can('izinsakit.create');
        Route::get('/izinsakit/{kode_izin_sakit}/edit', 'edit')->name('izinsakit.edit')->can('izinsakit.edit');
        Route::put('/izinsakit/{kode_izin_sakit}', 'update')->name('izinsakit.update')->can('izinsakit.edit');
        Route::get('/izinsakit/{kode_izin_sakit}/show', 'show')->name('izinsakit.show')->can('izinsakit.index');
        Route::delete('/izinsakit/{kode_izin_sakit}/delete', 'destroy')->name('izinsakit.delete')->can('izinsakit.delete');
        Route::get('/izinsakit/{kode_izin_sakit}/approve', 'approve')->name('izinsakit.approve')->can('izinsakit.approve');
        Route::delete('/izinsakit/{kode_izin_sakit}/cancelapprove', 'cancelapprove')->name('izinsakit.cancelapprove')->can('izinsakit.approve');
        Route::post('/izinsakit/{kode_izin_sakit}/storeapprove', 'storeapprove')->name('izinsakit.storeapprove')->can('izinsakit.approve');
    });

    // Pengajuan Cuti (Quota Bulanan)
    Route::controller(IzincutiController::class)->group(function () {
        Route::get('/izincuti', 'index')->name('izincuti.index')->can('izincuti.index');
        Route::get('/izincuti/create', 'create')->name('izincuti.create')->can('izincuti.create');
        Route::get('/izincuti/print-report', 'printReport')->name('izincuti.print-report')->can('izincuti.index');
        Route::post('/izincuti', 'store')->name('izincuti.store')->can('izincuti.create');
        Route::get('/izincuti/{kode_izin_cuti}/edit', 'edit')->name('izincuti.edit')->can('izincuti.edit');
        Route::put('/izincuti/{kode_izin_cuti}', 'update')->name('izincuti.update')->can('izincuti.edit');
        Route::get('/izincuti/{kode_izin_cuti}/show', 'show')->name('izincuti.show')->can('izincuti.index');
        Route::get('/izincuti/{kode_izin_cuti}/print', 'print')->name('izincuti.print')->can('izincuti.index');
        Route::delete('/izincuti/{kode_izin_cuti}/delete', 'destroy')->name('izincuti.delete')->can('izincuti.delete');
        Route::get('/izincuti/{kode_izin_cuti}/approve', 'approve')->name('izincuti.approve')->can('izincuti.approve');
        Route::delete('/izincuti/{kode_izin_cuti}/cancelapprove', 'cancelapprove')->name('izincuti.cancelapprove')->can('izincuti.approve');
        Route::post('/izincuti/{kode_izin_cuti}/storeapprove', 'storeapprove')->name('izincuti.storeapprove')->can('izincuti.approve');
        Route::get('/izincuti/getsisaharicuti', 'getsisaharicuti')->name('izincuti.getsisaharicuti');
    });

    // Dispensasi Keterlambatan
    Route::controller(DispensasiController::class)->group(function () {
        Route::get('/dispensasi', 'index')->name('dispensasi.index');
        Route::get('/dispensasi/create', 'create')->name('dispensasi.create');
        Route::post('/dispensasi', 'store')->name('dispensasi.store');
        Route::get('/dispensasi/{id}/approve', 'approve')->name('dispensasi.approve');
        Route::post('/dispensasi/{id}/storeapprove', 'storeApprove')->name('dispensasi.storeApprove');
        Route::delete('/dispensasi/{id}/cancelapprove', 'cancelApprove')->name('dispensasi.cancelApprove');
        Route::delete('/dispensasi/{id}', 'destroy')->name('dispensasi.destroy');
    });

    // Mobile Shortcut Menu & Hub Pengajuan Izin
    Route::get('/shortcut', [\App\Http\Controllers\ShortcutController::class, 'index'])->name('shortcut.index');
    Route::get('/pengajuanizin', [PengajuanizinController::class, 'index'])->name('pengajuanizin.index');

    // Laporan / Rekap (Presensi & Cuti Saja)
    Route::controller(LaporanController::class)->group(function () {
        Route::get('/laporan/presensi', 'presensi')->name('laporan.presensi')->can('laporan.presensi');
        Route::post('/laporan/cetakpresensi', 'cetakpresensi')->name('laporan.cetakpresensi')->can('laporan.presensi');
        Route::get('/laporan/cuti', 'cuti')->name('laporan.cuti')->can('laporan.cuti');
        Route::post('/laporan/cetakcuti', 'cetakcuti')->name('laporan.cetakcuti')->can('laporan.cuti');
    });

    // Pengaturan Umum / Super Admin
    Route::middleware('role:super admin')->group(function () {
        Route::controller(GeneralsettingController::class)->group(function () {
            Route::get('/generalsetting', 'index')->name('generalsetting.index')->can('generalsetting.index');
            Route::put('/generalsetting/{id}', 'update')->name('generalsetting.update')->can('generalsetting.edit');
            Route::post('/generalsetting/fix-permissions', 'fixPermissions')->name('generalsetting.fix-permissions');
        });

        Route::controller(UserController::class)->group(function () {
            Route::get('/users', 'index')->name('users.index');
            Route::get('/users/create', 'create')->name('users.create');
            Route::post('/users', 'store')->name('users.store');
            Route::get('/users/{id}/edit', 'edit')->name('users.edit');
            Route::put('/users/{id}/update', 'update')->name('users.update');
            Route::delete('/users/{id}/delete', 'destroy')->name('users.delete');
            Route::get('/users/{id}/editpassword', 'editpassword')->name('users.editpassword');
            Route::put('/users/{id}/updatepassword', 'updatepassword')->name('users.updatepassword');
        });

        Route::controller(Permission_groupController::class)->group(function () {
            Route::get('/permissiongroups', 'index')->name('permissiongroups.index');
            Route::get('/permissiongroups/create', 'create')->name('permissiongroups.create');
            Route::post('/permissiongroups', 'store')->name('permissiongroups.store');
            Route::get('/permissiongroups/{id}/edit', 'edit')->name('permissiongroups.edit');
            Route::put('/permissiongroups/{id}/update', 'update')->name('permissiongroups.update');
            Route::delete('/permissiongroups/{id}/delete', 'destroy')->name('permissiongroups.delete');
        });

        Route::controller(PermissionController::class)->group(function () {
            Route::get('/permissions', 'index')->name('permissions.index');
            Route::get('/permissions/create', 'create')->name('permissions.create');
            Route::post('/permissions', 'store')->name('permissions.store');
            Route::get('/permissions/{id}/edit', 'edit')->name('permissions.edit');
            Route::put('/permissions/{id}/update', 'update')->name('permissions.update');
            Route::delete('/permissions/{id}/delete', 'destroy')->name('permissions.delete');
        });

        Route::controller(BackupController::class)->group(function () {
            Route::get('/backup', 'index')->name('backup.index');
            Route::get('/backup/download', 'download')->name('backup.download');
            Route::post('/backup/restore', 'restore')->name('backup.restore');
        });
    });
});

// Fast caching route for face recognition model files
Route::get('/models/{file}', function ($file) {
    $baseDir = realpath(public_path('models'));
    $targetPath = realpath(public_path('models/' . $file));

    if (!$baseDir || !$targetPath || !str_starts_with($targetPath, $baseDir) || !file_exists($targetPath)) {
        abort(404);
    }
    $mime = str_ends_with($file, '.json') ? 'application/json' : 'application/octet-stream';
    return response()->file($targetPath, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=31536000, immutable',
        'Access-Control-Allow-Origin' => '*',
    ]);
})->where('file', '[a-zA-Z0-9_\-\.]+');

// Require auth routes
require __DIR__ . '/auth.php';
