<?php

namespace App\Http\Controllers;

use App\Models\GlobalJamkerja;
use App\Models\Jamkerja;
use App\Models\Pengaturanumum;
use App\Models\KaryawanMenuSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class GeneralsettingController extends Controller
{
    public function index()
    {
        $data['setting'] = Pengaturanumum::where('id', 1)->first();
        $data['global_jamkerja'] = GlobalJamkerja::all()->keyBy('hari');
        $data['jamkerja_list'] = Jamkerja::orderBy('jam_masuk')->get();
        $data['karyawan_menus'] = KaryawanMenuSetting::orderBy('id')->get();
        return view('generalsettings.index', $data);
    }

    public function update(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $rules = [
            'nama_aplikasi' => 'required|string|max:255',
            'nama_perusahaan' => 'required',
            'alamat' => 'required',
            'telepon' => 'required',
            'total_jam_bulan' => 'required',
            'status_potongan_jam' => 'nullable',
            'periode_laporan_dari' => 'required',
            'periode_laporan_sampai' => 'required',
            'domain_email' => 'nullable|string',
            'provider_wa' => 'nullable|string',
            'tujuan_notifikasi_wa' => 'nullable|string',
            'id_group_wa' => 'nullable|string|max:255',
            'timezone' => 'required|string|max:50',
            'nama_hrd' => 'nullable|string',
            'theme_color_1' => 'nullable|string|max:20',
            'theme_color_2' => 'nullable|string|max:20',
            'mobile_theme_scheme' => 'nullable|string|max:20',
            'session_time' => 'nullable|integer|min:1',
            'show_rate_slip' => 'nullable',
            'sistem_hari_kerja' => 'required|in:5,6',
            'monthly_leave_quota' => 'nullable|integer|min:0',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        if (auth()->user()->hasRole('master admin')) {
            $rules['expired'] = 'nullable|date';
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();
            $setting = Pengaturanumum::findOrFail($id);

            $data = [
                'nama_aplikasi' => $request->nama_aplikasi,
                'nama_perusahaan' => $request->nama_perusahaan,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'nama_hrd' => $request->nama_hrd,
                'total_jam_bulan' => $request->total_jam_bulan,
                'show_rate_slip' => $request->has('show_rate_slip') ? 1 : 0,
                'status_potongan_jam' => $request->has('status_potongan_jam') ? 1 : 0,
                'denda' => $request->has('denda') ? true : false,
                'face_recognition' => $request->has('face_recognition') ? true : false,
                'periode_laporan_dari' => $request->periode_laporan_dari,
                'periode_laporan_sampai' => $request->periode_laporan_sampai,
                'periode_laporan_next_bulan' => $request->periode_laporan_next_bulan,
                'batasi_absen' => $request->has('batasi_absen') ? true : false,
                'multi_lokasi' => $request->has('multi_lokasi') ? true : false,
                'batas_jam_absen' => $request->batas_jam_absen,
                'batas_jam_absen_pulang' => $request->batas_jam_absen_pulang,
                'cloud_id' => $request->cloud_id ?? $setting->cloud_id ?? '',
                'api_key' => $request->api_key ?? $setting->api_key ?? '',
                'domain_email' => $request->domain_email ?? $setting->domain_email ?? 'gmail.com',
                'batasi_hari_izin' => $request->has('batasi_hari_izin') ? true : false,
                'jml_hari_izin_max' => $request->jml_hari_izin_max,
                'batas_presensi_lintashari' => $request->batas_presensi_lintashari,
                'timezone' => $request->timezone,
                'session_time' => $request->session_time,
                'sistem_hari_kerja' => $request->sistem_hari_kerja,
                'monthly_leave_quota' => $request->filled('monthly_leave_quota') ? (int)$request->monthly_leave_quota : 0,
                'global_jamkerja_aktif' => $request->has('global_jamkerja_aktif') ? 1 : 0,
                'theme_color_1' => $request->filled('theme_color_1') ? $request->theme_color_1 : ($setting->theme_color_1 ?? '#3C2A21'),
                'theme_color_2' => $request->filled('theme_color_2') ? $request->theme_color_2 : ($setting->theme_color_2 ?? '#634832'),
            ];

            if (auth()->user()->hasRole('master admin')) {
                $data['expired'] = $request->expired;
            }

            if ($request->hasFile('logo')) {
                if ($setting->logo) {
                    if (Storage::disk('public')->exists('logo/' . $setting->logo)) {
                        Storage::disk('public')->delete('logo/' . $setting->logo);
                    }
                    if (Storage::exists('public/logo/' . $setting->logo)) {
                        Storage::delete('public/logo/' . $setting->logo);
                    }
                }

                $logoName = \App\Helpers\ImageOptimizer::saveAsWebp(
                    $request->file('logo'),
                    'logo',
                    'logo_' . time() . '_' . uniqid(),
                    85,
                    512,
                    'public'
                );

                $data['logo'] = $logoName;

                // Sync otomatis ke public/logo.png dan public/favicon.ico agar favicon browser ikut terupdate
                try {
                    $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                    $logoImg = $manager->read($request->file('logo'));
                    $logoImg->toPng()->save(public_path('logo.png'));

                    $favImg = $manager->read($request->file('logo'));
                    $favImg->cover(64, 64)->toPng()->save(public_path('favicon.ico'));
                } catch (\Exception $e) {
                    \Log::warning('Gagal auto-sync logo.png / favicon.ico: ' . $e->getMessage());
                }
            }

            $oldTimezone = $setting->timezone ?? 'Asia/Jakarta';
            $oldSessionTime = $setting->session_time;
            $setting->update($data);
            \Illuminate\Support\Facades\Cache::forget('global_app_logo_relative_path');
            \Illuminate\Support\Facades\Cache::forget('pengaturan_umum_first');
            \App\Services\ThemeResolver::forgetCache();

            // Update jadwal kerja global per hari
            if ($request->has('global_jamkerja')) {
                foreach ($request->global_jamkerja as $hari => $kode_jam_kerja) {
                    GlobalJamkerja::updateOrCreate(
                        ['hari' => $hari],
                        ['kode_jam_kerja' => $kode_jam_kerja ?: null]
                    );
                }
            }
            
            // Update .env file dengan timezone baru jika timezone berubah
            if ($oldTimezone != $request->timezone) {
                $this->updateEnvFile('APP_TIMEZONE', $request->timezone);
                
                // Clear config cache agar perubahan .env langsung diterapkan
                try {
                    Artisan::call('config:clear');
                    Artisan::call('cache:clear');
                } catch (\Exception $e) {
                    // Jika clear cache gagal, tetap lanjutkan (bisa di-clear manual)
                }
            }

            // Update SESSION_LIFETIME jika session_time diubah
            if ($request->has('session_time') && $request->session_time != $oldSessionTime) {
                // Konversi hari ke menit (1 hari = 1440 menit)
                $sessionLifetime = $request->session_time * 1440;
                $this->updateEnvFile('SESSION_LIFETIME', $sessionLifetime);
                try {
                    Artisan::call('config:clear');
                } catch (\Exception $e) {
                }
            }
            // Update karyawan menu settings (jika ada input dari view)
            if ($request->has('karyawan_menu')) {
                $menu_inputs = $request->input('karyawan_menu', []);
                foreach (KaryawanMenuSetting::pluck('kode_menu') as $kode) {
                    KaryawanMenuSetting::where('kode_menu', $kode)->update([
                        'status' => isset($menu_inputs[$kode]) ? 1 : 0
                    ]);
                }
                KaryawanMenuSetting::clearCache();
            }
            \Illuminate\Support\Facades\Cache::forget('global_general_setting');
            \Illuminate\Support\Facades\Cache::forget('app_expiration_setting');
            
            DB::commit();
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan. Perubahan timezone telah diterapkan.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    /**
     * Update .env file dengan key dan value baru
     */
    private function updateEnvFile($key, $value)
    {
        $envFile = base_path('.env');
        
        if (!File::exists($envFile)) {
            return false;
        }

        // Sanitize key and value to prevent CRLF injection into .env
        $key = preg_replace('/[^A-Z0-9_]/i', '', (string)$key);
        $value = str_replace(["\r", "\n"], '', (string)$value);

        $envContent = File::get($envFile);
        
        // Cek apakah key sudah ada
        if (preg_match("/^{$key}=.*/m", $envContent)) {
            // Update existing key
            $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
        } else {
            // Tambahkan key baru di akhir file
            $envContent .= "\n{$key}={$value}\n";
        }

        File::put($envFile, $envContent);
        
        return true;
    }

    /**
     * Fix storage directory permissions recursively to 775
     */
    public function fixPermissions()
    {
        try {
            $storagePath = storage_path();
            
            // Perbaiki permission folder storage secara rekursif
            $this->recursiveChmod($storagePath, 0775, 0664);

            return response()->json([
                'success' => true,
                'message' => 'Permission folder storage berhasil diperbarui menjadi 775.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui permission: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Helper method to chmod directory and files recursively
     */
    private function recursiveChmod($path, $dirMode = 0775, $fileMode = 0664)
    {
        if (!file_exists($path)) {
            return;
        }

        // Chmod main directory
        @chmod($path, $dirMode);

        if (is_dir($path)) {
            $items = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($items as $item) {
                $itemPath = $item->getPathname();
                if ($item->isDir()) {
                    @chmod($itemPath, $dirMode);
                } else {
                    @chmod($itemPath, $fileMode);
                }
            }
        }
    }
}
