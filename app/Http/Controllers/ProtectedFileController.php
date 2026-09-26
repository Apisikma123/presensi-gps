<?php

namespace App\Http\Controllers;

use App\Models\Facerecognition;
use App\Models\Izinsakit;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Userkaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProtectedFileController extends Controller
{
    /**
     * Stream Surat Izin Dokter (SID) file with authorization.
     *
     * @param string $filename
     * @return BinaryFileResponse
     */
    public function streamSid(string $filename): BinaryFileResponse
    {
        /** @var User|null $user */
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        // Sanitize to prevent path traversal
        $safeFilename = basename($filename);
        if (empty($safeFilename) || $safeFilename !== $filename) {
            abort(400, 'Nama berkas tidak valid.');
        }

        // Find associated Izinsakit record
        $izinsakit = Izinsakit::where('doc_sid', $safeFilename)->first();

        // Check authorization
        $isAuthorized = false;
        if ($user->isSuperAdmin()) {
            $isAuthorized = true;
        } elseif ($izinsakit) {
            if ($user->hasRole('karyawan')) {
                $userkaryawan = $user->userkaryawan ?? Userkaryawan::where('id_user', $user->id)->first();
                if ($userkaryawan && $userkaryawan->nik === $izinsakit->nik) {
                    $isAuthorized = true;
                }
            } elseif ($user->can('izinsakit.index') || $user->can('izinsakit.approve') || $user->can('presensi.index')) {
                $karyawan = Karyawan::where('nik', $izinsakit->nik)->first();
                if ($karyawan) {
                    $userCabangs = $user->getCabangCodes();
                    $userDepartemens = $user->getDepartemenCodes();
                    $cabangAllowed = empty($userCabangs) || in_array($karyawan->kode_cabang, $userCabangs);
                    $deptAllowed = empty($userDepartemens) || in_array($karyawan->kode_dept, $userDepartemens);
                    if ($cabangAllowed && $deptAllowed) {
                        $isAuthorized = true;
                    }
                }
            }
        } elseif (!$user->hasRole('karyawan') && ($user->can('izinsakit.index') || $user->can('izinsakit.approve'))) {
            // If record is not in database yet or admin is testing
            $isAuthorized = true;
        }

        if (!$isAuthorized) {
            abort(403, 'Akses ditolak. Anda tidak memiliki wewenang untuk melihat berkas ini.');
        }

        // Check private path first, then fallback to legacy public path
        $privatePath = storage_path('app/private/uploads/sid/' . $safeFilename);
        $legacyPublicPath = storage_path('app/public/uploads/sid/' . $safeFilename);

        $resolvedPath = null;
        if (file_exists($privatePath) && is_file($privatePath)) {
            $resolvedPath = $privatePath;
        } elseif (file_exists($legacyPublicPath) && is_file($legacyPublicPath)) {
            $resolvedPath = $legacyPublicPath;
        }

        if (!$resolvedPath) {
            abort(404, 'Berkas SID tidak ditemukan.');
        }

        $mime = mime_content_type($resolvedPath) ?: 'application/octet-stream';

        return response()->file($resolvedPath, [
            'Content-Type' => $mime,
            'Cache-Control' => 'private, no-cache, must-revalidate',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Stream biometric face image with authorization.
     *
     * @param string $folder
     * @param string $filename
     * @return BinaryFileResponse
     */
    public function streamFace(string $folder, string $filename): BinaryFileResponse
    {
        /** @var User|null $user */
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        // Sanitize to prevent path traversal
        $safeFolder = basename($folder);
        $safeFilename = basename($filename);
        if (empty($safeFolder) || $safeFolder !== $folder || empty($safeFilename) || $safeFilename !== $filename) {
            abort(400, 'Parameter berkas tidak valid.');
        }

        // Extract NIK from folder format ({nik}-{name})
        $folderParts = explode('-', $safeFolder, 2);
        $folderNik = $folderParts[0] ?? '';

        // Check authorization
        $isAuthorized = false;
        if ($user->isSuperAdmin()) {
            $isAuthorized = true;
        } elseif ($user->hasRole('karyawan')) {
            $userkaryawan = $user->userkaryawan ?? Userkaryawan::where('id_user', $user->id)->first();
            if ($userkaryawan && $userkaryawan->nik === $folderNik) {
                $isAuthorized = true;
            }
        } elseif ($user->can('karyawan.index') || $user->can('karyawan.edit') || $user->can('presensi.index')) {
            $karyawan = Karyawan::where('nik', $folderNik)->first();
            if ($karyawan) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();
                $cabangAllowed = empty($userCabangs) || in_array($karyawan->kode_cabang, $userCabangs);
                $deptAllowed = empty($userDepartemens) || in_array($karyawan->kode_dept, $userDepartemens);
                if ($cabangAllowed && $deptAllowed) {
                    $isAuthorized = true;
                }
            } else {
                $isAuthorized = true;
            }
        }

        if (!$isAuthorized) {
            abort(403, 'Akses ditolak. Anda tidak berhak melihat data biometrik ini.');
        }

        // Check private path first, then fallback to legacy public path
        $privatePath = storage_path('app/private/uploads/facerecognition/' . $safeFolder . '/' . $safeFilename);
        $legacyPublicPath = storage_path('app/public/uploads/facerecognition/' . $safeFolder . '/' . $safeFilename);

        $resolvedPath = null;
        if (file_exists($privatePath) && is_file($privatePath)) {
            $resolvedPath = $privatePath;
        } elseif (file_exists($legacyPublicPath) && is_file($legacyPublicPath)) {
            $resolvedPath = $legacyPublicPath;
        }

        if (!$resolvedPath) {
            abort(404, 'Berkas wajah tidak ditemukan.');
        }

        $mime = mime_content_type($resolvedPath) ?: 'image/webp';

        return response()->file($resolvedPath, [
            'Content-Type' => $mime,
            'Cache-Control' => 'private, no-cache, must-revalidate',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Download monthly attendance photo ZIP archive (Super Admin / Authorized Admin only).
     *
     * @param Request $request
     * @param string $month
     * @return BinaryFileResponse
     */
    public function downloadAttendanceArchive(Request $request, string $month): BinaryFileResponse
    {
        /** @var User|null $user */
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        // Strict authorization: Super Admin or Admin with presensi.index permission
        if (!$user->isSuperAdmin() && !$user->can('presensi.index')) {
            abort(403, 'Akses ditolak. Anda tidak memiliki wewenang untuk mengunduh arsip presensi.');
        }

        // Sanitize month format strictly: YYYY-MM
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month)) {
            abort(400, 'Format bulan tidak valid. Gunakan format YYYY-MM.');
        }

        $archiveDir = config('attendance.archive_disk_path', storage_path('app/private/attendance-archive'));
        $filePath = $archiveDir . '/' . $month . '.zip';

        if (!file_exists($filePath) || !is_file($filePath)) {
            abort(404, 'Berkas arsip presensi untuk bulan ' . $month . ' tidak ditemukan.');
        }

        return response()->download($filePath, 'attendance-archive-' . $month . '.zip', [
            'Content-Type' => 'application/zip',
            'Cache-Control' => 'private, no-cache, must-revalidate',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Stream attendance photo with authentication and branch authorization.
     *
     * @param string $filename
     * @return BinaryFileResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function streamAttendancePhoto(string $filename)
    {
        /** @var User|null $user */
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        $safeFilename = basename($filename);
        if (empty($safeFilename) || $safeFilename !== $filename) {
            abort(400, 'Nama berkas tidak valid.');
        }

        // Find associated presensi record
        $presensi = \App\Models\Presensi::where('foto_in', $safeFilename)
            ->orWhere('foto_out', $safeFilename)
            ->first();

        $isAuthorized = false;
        if ($user->isSuperAdmin()) {
            $isAuthorized = true;
        } elseif ($presensi) {
            if ($user->hasRole('karyawan')) {
                $userkaryawan = $user->userkaryawan ?? Userkaryawan::where('id_user', $user->id)->first();
                if ($userkaryawan && $userkaryawan->nik === $presensi->nik) {
                    $isAuthorized = true;
                }
            } elseif ($user->can('presensi.index') || $user->can('trackingpresensi.index')) {
                $userCabangs = $user->getCabangCodes();
                $karyawan = Karyawan::where('nik', $presensi->nik)->first();
                $branch = !empty($presensi->kode_cabang) ? $presensi->kode_cabang : ($karyawan->kode_cabang ?? null);
                if (empty($userCabangs) || in_array($branch, $userCabangs) || ($karyawan && in_array($karyawan->kode_cabang, $userCabangs))) {
                    $isAuthorized = true;
                }
            }
        } elseif (!$user->hasRole('karyawan') && $user->can('presensi.index')) {
            $isAuthorized = true;
        }

        if (!$isAuthorized) {
            abort(403, 'Akses ditolak. Anda tidak memiliki wewenang untuk melihat foto presensi ini.');
        }

        // Check hot storage on disk
        $hotPath = storage_path('app/public/uploads/absensi/' . $safeFilename);
        $privateHotPath = storage_path('app/private/uploads/absensi/' . $safeFilename);

        $resolvedPath = null;
        if (file_exists($hotPath) && is_file($hotPath)) {
            $resolvedPath = $hotPath;
        } elseif (file_exists($privateHotPath) && is_file($privateHotPath)) {
            $resolvedPath = $privateHotPath;
        }

        if ($resolvedPath) {
            $mime = mime_content_type($resolvedPath) ?: 'image/webp';
            return response()->file($resolvedPath, [
                'Content-Type' => $mime,
                'Cache-Control' => 'private, no-cache, must-revalidate',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        // If file is archived, attempt streaming from monthly ZIP archive
        if ($presensi && ($presensi->is_archived || $presensi->foto_in_archived || $presensi->foto_out_archived)) {
            $month = date('Y-m', strtotime($presensi->tanggal));
            $archiveDir = config('attendance.archive_disk_path', storage_path('app/private/attendance-archive'));
            $zipPath = $archiveDir . '/' . $month . '.zip';
            if (file_exists($zipPath) && class_exists(\ZipArchive::class)) {
                $zip = new \ZipArchive();
                if ($zip->open($zipPath) === true) {
                    $possibleEntries = [
                        'uploads/absensi/' . $safeFilename,
                        str_ends_with($safeFilename, '-out.webp') || str_contains($safeFilename, '-out') 
                            ? 'pulang/' . $safeFilename 
                            : 'masuk/' . $safeFilename,
                        $safeFilename,
                    ];
                    $stream = null;
                    foreach ($possibleEntries as $entryName) {
                        $stream = $zip->getStream($entryName);
                        if ($stream) {
                            break;
                        }
                    }
                    if ($stream) {
                        $contents = stream_get_contents($stream);
                        fclose($stream);
                        $zip->close();
                        return response($contents, 200, [
                            'Content-Type' => 'image/webp',
                            'Cache-Control' => 'private, no-cache, must-revalidate',
                            'X-Content-Type-Options' => 'nosniff',
                        ]);
                    }
                    $zip->close();
                }
            }
        }

        abort(404, 'Berkas foto presensi tidak ditemukan.');
    }
}
