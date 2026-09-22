<?php

namespace App\Http\Controllers;

use App\Models\Facerecognition;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Userkaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class FacerecognitionController extends Controller
{
    public function create($nik)
    {
        $nik = Crypt::decrypt($nik);
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $karyawan = Karyawan::where('nik', $nik)->firstOrFail();

        if ($user->hasRole('karyawan')) {
            $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
            if (!$userkaryawan || $userkaryawan->nik !== $nik) {
                abort(403, 'Akses ditolak.');
            }
            if (Facerecognition::where('nik', $nik)->exists()) {
                return redirect()->route('facerecognition.karyawan.preview')->with('info', 'Data biometrik wajah Anda sudah terdaftar.');
            }
        } elseif (!$user->isSuperAdmin()) {
            if (!$user->can('karyawan.edit')) {
                abort(403, 'Anda tidak memiliki wewenang untuk mendaftarkan wajah.');
            }
            $userCabangs = $user->getCabangCodes();
            if (empty($userCabangs) || !in_array($karyawan->kode_cabang, $userCabangs)) {
                abort(403, 'Anda tidak memiliki akses ke karyawan cabang ini.');
            }
        }

        $data['nik'] = $nik;
        return view('facerecognition.create', $data);
    }

    // Halaman daftarkan wajah untuk karyawan (mobile layout)
    // Halaman daftarkan wajah untuk karyawan (mobile layout)
    public function createKaryawan(Request $request)
    {
        $user = auth()->user();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();

        if (!$userkaryawan) {
            return redirect()->route('dashboard.index')->with('error', 'Data karyawan tidak ditemukan');
        }

        $data['nik'] = $userkaryawan->nik;
        $data['karyawan'] = Karyawan::where('nik', $userkaryawan->nik)->first();

        // Cek apakah sudah ada data wajah sebelumnya. Redirect ke preview jika ada, kecuali ada parameter rekam ulang (?re=1)
        $existingWajah = Facerecognition::where('nik', $userkaryawan->nik)->count();
        if ($existingWajah > 0 && !$request->has('re')) {
            return redirect()->route('facerecognition.karyawan.preview');
        }

        return view('facerecognition.create-karyawan', $data);
    }

    // Halaman preview data wajah yang sudah ada untuk karyawan
    public function previewKaryawan()
    {
        $user = auth()->user();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();

        if (!$userkaryawan) {
            return redirect()->route('dashboard.index')->with('error', 'Data karyawan tidak ditemukan');
        }

        $data['nik'] = $userkaryawan->nik;
        $data['karyawan'] = Karyawan::where('nik', $userkaryawan->nik)->first();
        
        // Ambil semua data wajah yang sudah ada
        $wajahList = Facerecognition::where('nik', $userkaryawan->nik)
            ->orderBy('created_at', 'desc')
            ->get();

        // Siapkan URL untuk setiap gambar dengan pengecekan file
        $nama_folder = $data['karyawan']->nik . "-" . getNamaDepan(strtolower($data['karyawan']->nama_karyawan));
        $folderRelativePath = 'uploads/facerecognition/' . $nama_folder;
        
        $data['wajahList'] = $wajahList->map(function($wajah) use ($folderRelativePath, $nama_folder) {
            $filePath = $folderRelativePath . '/' . $wajah->wajah;
            $wajah->file_exists = Storage::disk('private')->exists($filePath) || Storage::disk('public')->exists($filePath);
            
            if ($wajah->file_exists) {
                try {
                    $fileTimestamp = Storage::disk('private')->exists($filePath)
                        ? Storage::disk('private')->lastModified($filePath)
                        : Storage::disk('public')->lastModified($filePath);
                } catch (\Exception $e) {
                    $fileTimestamp = \Carbon\Carbon::parse($wajah->created_at)->timestamp;
                }
                
                // Gunakan protected file route agar tidak expose ke public URL
                $wajah->image_url = route('file.face', [
                    'folder' => $nama_folder,
                    'filename' => $wajah->wajah
                ]) . '?v=' . $fileTimestamp . '_' . microtime(true);
            } else {
                $wajah->image_url = null;
            }
            
            return $wajah;
        });

        return view('facerecognition.preview-karyawan', $data);
    }

    // Hapus semua wajah karyawan yang sedang login untuk rekam ulang
    public function destroyAllKaryawan()
    {
        $user = auth()->user();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();

        if (!$userkaryawan) {
            return redirect()->route('dashboard.index')->with('error', 'Data karyawan tidak ditemukan');
        }

        $karyawan = Karyawan::where('nik', $userkaryawan->nik)->first();
        if (!$karyawan) {
            return redirect()->route('dashboard.index')->with('error', 'Data karyawan tidak ditemukan');
        }

        $folder = $karyawan->nik . '-' . getNamaDepan(strtolower($karyawan->nama_karyawan));
        $folderPath = 'uploads/facerecognition/' . $folder;

        try {
            if (Storage::disk('private')->exists($folderPath)) {
                Storage::disk('private')->deleteDirectory($folderPath);
            }
            if (Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->deleteDirectory($folderPath);
            }
            Facerecognition::where('nik', $userkaryawan->nik)->delete();

            return redirect()->route('facerecognition.karyawan.create', ['re' => 1])
                ->with('success', 'Data wajah lama berhasil direset. Silakan lakukan perekaman baru.');
        } catch (\Exception $e) {
            return redirect()->route('facerecognition.karyawan.preview')
                ->with('error', 'Gagal mereset data wajah: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();

        // Enforce identity: employees can only enroll their own face
        if ($user->hasRole('karyawan') || !$user->can('karyawan.edit')) {
            if (!$userkaryawan) {
                return response()->json(['success' => false, 'message' => 'Data karyawan Anda tidak ditemukan.'], 403);
            }
            $targetNik = $userkaryawan->nik;
        } else {
            $targetNik = $request->nik ?: ($userkaryawan ? $userkaryawan->nik : null);
        }

        if (!$targetNik) {
            return response()->json(['success' => false, 'message' => 'NIK tidak valid.'], 400);
        }

        $karyawan = Karyawan::where('nik', $targetNik)->first();
        if (!$karyawan) {
            return response()->json(['success' => false, 'message' => 'Karyawan tidak ditemukan.'], 404);
        }

        if (!$user->isSuperAdmin() && !$user->hasRole('karyawan')) {
            $userCabangs = $user->getCabangCodes();
            if (empty($userCabangs) || !in_array($karyawan->kode_cabang, $userCabangs)) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses ke karyawan cabang ini.'], 403);
            }
        }

        $nama_folder = $karyawan->nik . "-" . getNamaDepan(strtolower($karyawan->nama_karyawan));
        $folderPath = "uploads/facerecognition/" . $nama_folder;

        if (!Storage::disk('private')->exists($folderPath)) {
            Storage::disk('private')->makeDirectory($folderPath);
        }

        try {
            $saved = [];
            $descriptors = $request->has('descriptors') 
                ? (is_string($request->descriptors) ? json_decode($request->descriptors, true) : $request->descriptors) 
                : [];

            // Jika multi-capture dengan file upload (metode baru)
            if ($request->hasFile('files')) {
                // Jika rekam ulang oleh karyawan, bersihkan dataset lama agar dataset digantikan oleh sampel baru
                if ($user->hasRole('karyawan')) {
                    $oldWajah = Facerecognition::where('nik', $targetNik)->get();
                    foreach ($oldWajah as $old) {
                        $oldFile = $folderPath . '/' . $old->wajah;
                        if (Storage::disk('private')->exists($oldFile)) {
                            Storage::disk('private')->delete($oldFile);
                        }
                    }
                    Facerecognition::where('nik', $targetNik)->delete();
                }

                $metadata = json_decode($request->metadata, true);
                $files = $request->file('files');
                $cekWajah = Facerecognition::where('nik', $targetNik)->count();
                $urutan = $cekWajah + 1;

                foreach ($files as $index => $file) {
                    $direction = isset($metadata[$index]['direction']) ? $metadata[$index]['direction'] : 'front';
                    $baseName = $urutan . "_" . $direction;
                    
                    $fileName = \App\Helpers\ImageOptimizer::saveAsWebp(
                        $file,
                        $folderPath,
                        $baseName,
                        85,
                        640,
                        'private'
                    );

                    $desc = (isset($descriptors[$index]) && is_array($descriptors[$index]) && count($descriptors[$index]) === 128)
                        ? $descriptors[$index]
                        : null;

                    // Simpan ke database
                    Facerecognition::create([
                        'nik' => $targetNik,
                        'wajah' => $fileName,
                        'descriptor' => $desc
                    ]);

                    $saved[] = $fileName;
                    $urutan++;
                }
                return response()->json(['success' => true, 'message' => count($saved) . ' gambar berhasil disimpan', 'files' => $saved]);

            } else if ($request->has('images')) {
                // Jika rekam ulang oleh karyawan, bersihkan dataset lama
                if ($user->hasRole('karyawan')) {
                    $oldWajah = Facerecognition::where('nik', $targetNik)->get();
                    foreach ($oldWajah as $old) {
                        $oldFile = $folderPath . '/' . $old->wajah;
                        if (Storage::disk('private')->exists($oldFile)) {
                            Storage::disk('private')->delete($oldFile);
                        }
                    }
                    Facerecognition::where('nik', $targetNik)->delete();
                }

                // JSON Base64 string
                $images = json_decode($request->images, true);
                $cekWajah = Facerecognition::where('nik', $targetNik)->count();
                $urutan = $cekWajah + 1;
                foreach ($images as $index => $img) {
                    $direction = isset($img['direction']) ? $img['direction'] : 'front';
                    $baseName = $urutan . "_" . $direction;
                    $image = $img['image'];
                    
                    $fileName = \App\Helpers\ImageOptimizer::saveAsWebp(
                        $image,
                        $folderPath,
                        $baseName,
                        85,
                        640,
                        'private'
                    );
                    
                    $desc = (isset($descriptors[$index]) && is_array($descriptors[$index]) && count($descriptors[$index]) === 128)
                        ? $descriptors[$index]
                        : null;

                    Facerecognition::create([
                        'nik' => $targetNik,
                        'wajah' => $fileName,
                        'descriptor' => $desc
                    ]);
                    $saved[] = $fileName;
                    $urutan++;
                }
                return response()->json(['success' => true, 'message' => count($saved) . ' gambar berhasil disimpan', 'files' => $saved]);
            } else if ($request->has('image')) {
                // Satu gambar
                $cekWajah = Facerecognition::where('nik', $targetNik)->count();
                $formatName = $cekWajah + 1;
                $image = $request->image;
                
                $fileName = \App\Helpers\ImageOptimizer::saveAsWebp(
                    $image,
                    $folderPath,
                    (string) $formatName,
                    85,
                    640,
                    'private'
                );
                
                $desc = (isset($descriptors[0]) && is_array($descriptors[0]) && count($descriptors[0]) === 128)
                    ? $descriptors[0]
                    : (is_array($descriptors) && count($descriptors) === 128 ? $descriptors : null);

                Facerecognition::create([
                    'nik' => $targetNik,
                    'wajah' => $fileName,
                    'descriptor' => $desc
                ]);
                return response()->json(['success' => true, 'message' => 'Gambar berhasil disimpan', 'file' => $fileName]);
            } else {
                return response()->json(['success' => false, 'message' => 'Tidak ada gambar yang dikirim']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // SEC-008: Employees cannot delete biometric records directly
        if ($user->hasRole('karyawan') || !$user->can('karyawan.edit')) {
            abort(403, 'Akses ditolak. Penghapusan data biometrik wajah hanya dapat dilakukan oleh Administrator.');
        }

        $id = Crypt::decrypt($id);
        $facerecognition = Facerecognition::where('id', $id)->firstOrFail();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
        $karyawan = Karyawan::where('nik', $facerecognition->nik)->first();

        if (!$user->isSuperAdmin()) {
            if (!$karyawan) {
                abort(403, 'Data karyawan tidak ditemukan.');
            }
            $userCabangs = $user->getCabangCodes();
            if (empty($userCabangs) || !in_array($karyawan->kode_cabang, $userCabangs)) {
                abort(403, 'Anda tidak memiliki akses ke data wajah karyawan cabang ini.');
            }
        }
        try {
            $nama_file = $facerecognition->wajah;
            $nama_folder = $karyawan->nik . "-" . getNamaDepan(strtolower($karyawan->nama_karyawan));
            $path = 'uploads/facerecognition/' . $nama_folder . "/" . $nama_file;
            Storage::disk('private')->delete($path);
            Storage::disk('public')->delete($path);
            $facerecognition->delete();
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function getWajah()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user) {
            return response()->json([]);
        }
        $userkaryawan = $user->userkaryawan ?? Userkaryawan::where('id_user', $user->id)->first();
        if (!$userkaryawan) {
            return response()->json([]);
        }
        $wajah = Facerecognition::where('nik', $userkaryawan->nik)
            ->select('id', 'nik', 'wajah', 'descriptor')
            ->get();
        return response()->json($wajah);
    }

    public function syncDescriptors(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        $userkaryawan = $user->userkaryawan ?? Userkaryawan::where('id_user', $user->id)->first();
        if (!$userkaryawan) {
            return response()->json(['success' => false, 'message' => 'Karyawan not found'], 404);
        }

        $descriptors = $request->input('descriptors', []);
        if (empty($descriptors) || !is_array($descriptors)) {
            return response()->json(['success' => false, 'message' => 'No descriptors provided'], 400);
        }

        $updated = 0;
        foreach ($descriptors as $item) {
            $id = $item['id'] ?? null;
            $desc = $item['descriptor'] ?? null;
            if ($id && is_array($desc) && count($desc) === 128) {
                $record = Facerecognition::where('id', $id)
                    ->where('nik', $userkaryawan->nik)
                    ->first();
                if ($record) {
                    $record->descriptor = $desc;
                    $record->save();
                    $updated++;
                }
            }
        }

        return response()->json(['success' => true, 'updated' => $updated]);
    }

    // Hapus semua wajah berdasarkan NIK (Hanya Administrator berwenang)
    public function destroyAll($nik)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // SEC-008: Employees cannot reset biometric datasets
        if ($user->hasRole('karyawan') || !$user->can('karyawan.edit')) {
            abort(403, 'Akses ditolak. Reset data biometrik wajah hanya dapat dilakukan oleh Administrator.');
        }

        $nik = Crypt::decrypt($nik);
        $karyawan = Karyawan::where('nik', $nik)->first();
        if (!$karyawan) {
            return Redirect::back()->with(messageError('Karyawan tidak ditemukan'));
        }

        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            if (!in_array($karyawan->kode_cabang, $userCabangs) || !in_array($karyawan->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke data wajah karyawan cabang ini.');
            }
        }
        $folder = $karyawan->nik . '-' . getNamaDepan(strtolower($karyawan->nama_karyawan));
        $folderPath = 'uploads/facerecognition/' . $folder;
        try {
            // Hapus semua file di folder (private dan legacy public)
            if (Storage::disk('private')->exists($folderPath)) {
                Storage::disk('private')->deleteDirectory($folderPath);
            }
            if (Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->deleteDirectory($folderPath);
            }
            Facerecognition::where('nik', $nik)->delete();
            return Redirect::back()->with(messageSuccess('Semua data wajah berhasil dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError('Gagal menghapus semua wajah: ' . $e->getMessage()));
        }
    }
}
