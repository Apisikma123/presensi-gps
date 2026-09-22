<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Facerecognition;
use App\Models\Karyawan;
use App\Models\Userkaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FacerecognitionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();

        if (!$userkaryawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan'
            ], 404);
        }

        $karyawan = Karyawan::where('nik', $userkaryawan->nik)->first();
        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan'
            ], 404);
        }

        $wajahList = Facerecognition::where('nik', $userkaryawan->nik)->get();

        $nama_folder = $karyawan->nik . "-" . getNamaDepan(strtolower($karyawan->nama_karyawan));
        
        $formattedWajahList = $wajahList->map(function ($wajah) use ($nama_folder) {
            $relPath = 'uploads/facerecognition/' . $nama_folder . '/' . $wajah->wajah;
            $exists = Storage::disk('private')->exists($relPath) || Storage::disk('public')->exists($relPath) || Storage::exists('public/' . $relPath);
            
            return [
                'id' => $wajah->id,
                'nik' => $wajah->nik,
                'wajah' => $wajah->wajah,
                'url' => $exists ? route('api.file.face', ['folder' => $nama_folder, 'filename' => $wajah->wajah]) : null,
            ];
        });

        return response()->json([
            'success' => true,
            'registered' => $wajahList->count() > 0,
            'wajah_list' => $formattedWajahList
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();

        if (!$userkaryawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan'
            ], 404);
        }

        $karyawan = Karyawan::where('nik', $userkaryawan->nik)->first();
        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan'
            ], 404);
        }

        // Bersihkan data biometrik lama jika karyawan melakukan rekam ulang
        $oldWajah = Facerecognition::where('nik', $userkaryawan->nik)->get();
        foreach ($oldWajah as $old) {
            $oldFile = $folderPath . '/' . $old->wajah;
            if (Storage::disk('private')->exists($oldFile)) {
                Storage::disk('private')->delete($oldFile);
            }
        }
        Facerecognition::where('nik', $userkaryawan->nik)->delete();

        $validator = Validator::make($request->all(), [
            'files' => 'required|array',
            'files.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'directions' => 'nullable|array',
            'directions.*' => 'nullable|string|alpha_dash|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 400);
        }

        $nama_folder = $karyawan->nik . "-" . getNamaDepan(strtolower($karyawan->nama_karyawan));
        $folderPath = "uploads/facerecognition/" . $nama_folder;

        if (!Storage::disk('private')->exists($folderPath)) {
            Storage::disk('private')->makeDirectory($folderPath);
        }

        try {
            $saved = [];
            $cekWajah = Facerecognition::where('nik', $userkaryawan->nik)->count();
            $urutan = $cekWajah + 1;

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $index => $file) {
                    $rawDirection = $request->input("directions.$index", 'front');
                    $direction = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$rawDirection);
                    if (empty($direction)) {
                        $direction = 'front';
                    }
                    
                    $fileName = \App\Helpers\ImageOptimizer::saveAsWebp(
                        $file,
                        $folderPath,
                        $urutan . "_" . $direction,
                        85,
                        640,
                        'private'
                    );

                    Facerecognition::create([
                        'nik' => $userkaryawan->nik,
                        'wajah' => $fileName
                    ]);

                    $saved[] = $fileName;
                    $urutan++;
                }

                return response()->json([
                    'success' => true,
                    'message' => count($saved) . ' gambar berhasil disimpan',
                    'files' => $saved
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tidak ada gambar yang dikirim'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan wajah: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy()
    {
        return response()->json([
            'success' => false,
            'message' => 'Akses ditolak. Penghapusan data biometrik wajah hanya dapat dilakukan oleh Administrator.'
        ], 403);
    }
}
